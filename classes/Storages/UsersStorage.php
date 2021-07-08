<?php

namespace classes\Storages;

use lib\Storage\BaseStorage;

class UsersStorage extends BaseStorage
{
    const REGISTRATION_TOKEN_EXPIRATION_HOURS = 24;

    protected $table               = 'users';
    protected $password_column     = 'password';
    protected $validation_settings = [
        'name' => [
            'name'  => '名前',
            'rules' => [
                'required'     => true,
                'length_range' => ['min' => 3, 'max' => 16],
            ],
        ],
        'email' => [
            'name'  => 'メールアドレス',
            'rules' => [
                'required' => true,
                'email'    => true,
            ],
        ],
        'password' => [
            'name'  => 'パスワード',
            'rules' => [
                'required'     => true,
                'length_range' => ['min' => 8, 'max' => 16],
            ],
        ],
    ];

    public function insert(array $column_values): bool
    {
        if (isset($column_values[$this->password_column])) {
            $column_values[$this->password_column] = $this->passwordHash($column_values[$this->password_column]);
        }

        return parent::insert($column_values);
    }

    public function update(array $column_values, array $where_clause_params = []): bool
    {
        if (isset($column_values[$this->password_column])) {
            $column_values[$this->password_column] = $this->passwordHash($column_values[$this->password_column]);
        }

        return parent::update($column_values, $where_clause_params);
    }

    public function isEmailTokenExpired(array $user): bool
    {
        if (!isset($user['created_at'])) {
            return true;
        }

        return ((time() - strtotime($user['created_at'])) > (self::REGISTRATION_TOKEN_EXPIRATION_HOURS * 60 * 60));
    }
}
