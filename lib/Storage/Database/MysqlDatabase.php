<?php

namespace lib\Storage\Database;

use LogicException;
use RuntimeException;
use lib\Storage\Database;

class MysqlDatabase extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectById(string $table, string $primary_key, $id, array $columns = ['*']): array
    {
        $results = $this->select($table, $columns, [['column' => $primary_key, 'condition' => '=', 'value' => $id]]);

        return (empty($results)) ? [] : $results[0];
    }

    public function fetch(string $table, array $columns = ['*'], array $where_clause_params = []): array
    {
        $results = $this->select($table, $columns, $where_clause_params);

        return (empty($results)) ? [] : $results[0];
    }

    public function select(string $table, array $columns = ['*'], array $where_clause_params = [], array $order_by_clause_params = [], int $limit = null, int $offset = null): array
    {
        $columns_clause = implode(', ', $columns);
        $sql            = "SELECT {$columns_clause} FROM {$table} " . $this->getWhereBindSQL($where_clause_params) . $this->getOrderBySQL($order_by_clause_params);
        $bind_values    = $this->filterBindValues(array_column($where_clause_params, 'value'));

        if ($limit) {
            $sql .= " LIMIT {$limit} ";

            if ($offset) {
                $sql .= " OFFSET {$offset} ";
            }
        }

        $stmt = $this->conn->prepare($sql);
        if ($bind_values) {
            $stmt->bind_param($this->getBindTypes($bind_values), ...$bind_values);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException(__METHOD__ . "() {$stmt->error()}");
        }

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function count(string $table, array $where_clause_params = []): int
    {
        $results = $this->select($table, ['COUNT(*) AS count'], $where_clause_params);
        if (!isset($results[0])) {
            throw new RuntimeException(__METHOD__ . '() データ取得に失敗しました');
        }

        return $results[0]['count'];
    }

    public function insert(string $table, array $column_values): bool
    {
        if (empty($column_values)) {
            throw new LogicException(__METHOD__ . '() 入力データが空です');
        }

        $columns_clause = implode(', ', array_keys($column_values));
        $bind_keys      = array_fill(1, count($column_values), '?');
        $values_clause  = implode(', ', $bind_keys);
        $bind_values    = array_values($column_values);
        $stmt           = $this->conn->prepare("INSERT INTO {$table} ({$columns_clause}) VALUES ({$values_clause})");
        $stmt->bind_param($this->getBindTypes($bind_values), ...$bind_values);
        if (!$stmt->execute()) {
            throw new RuntimeException(__METHOD__ . "() {$stmt->error()}");
        }

        return true;
    }

    public function updateById(string $table, string $primary_key, $id, array $column_values): bool
    {
        return $this->update($table, $column_values, [['column' => $primary_key, 'condition' => '=', 'value' => $id]]);
    }

    public function update(string $table, array $column_values, array $where_clause_params = []): bool
    {
        $set_clauses = [];
        foreach ($column_values as $column => $value) {
            $set_clause = " {$column} = ";
            if ($value === null) {
                $set_clause .= ' NULL ';
            } else {
                $set_clause .= ' ? ';
            }

            $set_clauses[] = $set_clause;
        }

        $stmt        = $this->conn->prepare("UPDATE {$table} SET " . implode(', ', $set_clauses) . $this->getWhereBindSQL($where_clause_params));
        $bind_values = $this->filterBindValues(array_merge(array_values($column_values), array_column($where_clause_params, 'value')));

        $stmt->bind_param($this->getBindTypes($bind_values), ...$bind_values);

        if (!$stmt->execute()) {
            throw new RuntimeException(__METHOD__ . "() {$stmt->error()}");
        }

        return true;
    }

    public function deleteById(string $table, string $primary_key, $id): bool
    {
        return $this->delete($table, [['column' => $primary_key, 'condition' => '=', 'value' => $id]]);
    }

    public function delete(string $table, array $where_clause_params = []): bool
    {
        $stmt        = $this->conn->prepare("DELETE FROM {$table} " . $this->getWhereBindSQL($where_clause_params));
        $bind_values = $this->filterBindValues(array_column($where_clause_params, 'value'));
        $stmt->bind_param($this->getBindTypes($bind_values), ...$bind_values);
        if (!$stmt->execute()) {
            throw new RuntimeException(__METHOD__ . "() {$stmt->error()}");
        }

        return true;
    }

    public function getInsertId(): int
    {
        return $this->conn->insert_id;
    }

    protected function filterBindValues(array $values) {
        return array_filter($values, function ($value) {
            return ($value !== null);
        });
    }

    protected function getOrderBySQL(array $order_by_clause_params): string
    {
        if (!$order_by_clause_params) {
            return '';
        }

        $order_by_clauses = [];
        foreach ($order_by_clause_params as $order_by_clause_param) {
            if (!isset($order_by_clause_param['column'])) {
                throw new LogicException(__METHOD__ . '() 必要な配列の要素が存在しません。');
            }

            $order_by_clause = " {$order_by_clause_param['column']} ";
            if (isset($order_by_clause_param['sort'])) {
                $order_by_clause .= strtoupper($order_by_clause_param['sort']);
            }

            $order_by_clauses[] = $order_by_clause;
        }

        return ' ORDER BY ' . implode(', ', $order_by_clauses);
    }

    protected function getWhereBindSQL(array $where_clause_params): string
    {
        if (!$where_clause_params) {
            return '';
        }

        $where_clauses = [];
        foreach ($where_clause_params as $where_clause_param) {
            if (!isset($where_clause_param['column']) || !isset($where_clause_param['condition'])) {
                throw new LogicException(__METHOD__ . '() 必要な配列の要素が足りません。');
            }

            if (array_key_exists('value', $where_clause_param)) {
                if ($where_clause_param['value'] === null) {
                    throw new LogicException(__METHOD__ . '() WHERE句のVALUEにNULL値は指定できません。CONDITION => IS NOT NULL || IS を使用してください');
                } else {
                    $where_clauses[] = " {$where_clause_param['column']} {$where_clause_param['condition']} ?";
                }
            } else {
                $where_clauses[] = " {$where_clause_param['column']} {$where_clause_param['condition']} ";
            }
        }

        return ' WHERE ' . implode(' AND ', $where_clauses);
    }

    protected function getBindTypes(array $bind_values): string
    {
        $bind_types = '';
        foreach ($bind_values as $bind_value) {
            if (is_int($bind_value)) {
                $bind_types .= 'i';
            } elseif (is_string($bind_value)) {
                $bind_types .= 's';
            } elseif (is_float($bind_value)) {
                $bind_types .= 'd';
            } elseif ($bind_value === null) {
                $bind_types .= 's';
            } else {
                throw new LogicException(__METHOD__ .'() バインドに対応していない値です。');
            }
        }

        return $bind_types;
    }

    protected function connect()
    {
        mysqli_report(MYSQLI_REPORT_STRICT);

        if (empty($this->config['host']) || empty($this->config['username']) ||
            empty($this->config['password']) || empty($this->config['database'])) {
            throw new LogicException(__METHOD__ . '() データベースの設定が存在しません');
        }

        $this->conn = mysqli_connect($this->config['host'], $this->config['username'], $this->config['password'], $this->config['database']);
    }
}
