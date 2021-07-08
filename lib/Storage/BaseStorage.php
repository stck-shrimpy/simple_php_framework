<?php

namespace lib\Storage;

use LogicException;
use lib\Storage\Database\MysqlDatabase;
use lib\Validator;

class BaseStorage
{
    protected $database;
    protected $validation_settings;
    protected $table;
    protected $primary_key       = 'id';
    protected $password_column   = 'password';
    protected $password_settings = ['algo' => PASSWORD_DEFAULT, 'options' => []];

    public function __construct()
    {
        $this->database = new MysqlDatabase();
    }

    public function validate(array $inputs): array
    {
        $validator = new Validator($this->getValidationSettings());

        return $validator->validate($inputs);
    }

    public function fetch(array $columns = ['*'], array $where_clause_params = []): array
    {
        return $this->database->fetch($this->table, $columns, $where_clause_params);
    }

    public function selectById($id, array $columns = ['*']): array
    {
        return $this->database->selectById($this->table, $this->primary_key, $id, $columns);
    }

    public function select(array $columns = ['*'], array $where_clause_params = [], array $order_by_clause_params = [], int $limit = null, int $offset = null): array
    {
        return $this->database->select($this->table, $columns, $where_clause_params, $order_by_clause_params, $limit, $offset);
    }

    public function count(array $where_clause_params = []): int
    {
        return $this->database->count($this->table, $where_clause_params);
    }

    public function insert(array $column_values): bool
    {
        return $this->database->insert($this->table, $column_values);
    }

    public function updateById($id, array $column_values): bool
    {
        return $this->database->updateById($this->table, $this->primary_key, $id, $column_values);
    }

    public function update(array $column_values, array $where_clause_params = []): bool
    {
        return $this->database->update($this->table, $column_values, $where_clause_params);
    }

    public function deleteById($id): bool
    {
        return $this->database->deleteById($this->table, $this->primary_key, $id);
    }

    public function delete(array $where_clause_params = []): bool
    {
        return $this->database->delete($this->table, $where_clause_params);
    }

    public function getInsertId(): int
    {
        return $this->database->getInsertId();
    }

    public function passwordVerify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function passwordHash(string $password): string
    {
        return password_hash($password, $this->password_settings['algo'], $this->password_settings['options']);
    }

    protected function getValidationSettings(): array
    {
        if (empty($this->validation_settings)) {
            throw new LogicException(__METHOD__ . '() バリデーションのセッティングが設定されていません');
        }

        return $this->validation_settings;
    }

    protected function getPrimaryKey(): string
    {
        if (!isset($this->primary_key)) {
            throw new LogicException(__METHOD__ . '() プライマリーキーが設定されていません');
        }

        return $this->primary_key;
    }
}
