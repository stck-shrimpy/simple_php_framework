<?php

namespace classes\Storages;

use lib\Storage\BaseStorage;

class BulletinStorage extends BaseStorage
{
    protected $table               = 'bulletin';
    protected $password_column     = 'password';
    protected $validation_settings = [
        'name' => [
            'name'  => '名前',
            'rules' => [
                'required' => false,
                'length_range' => ['min' => 3, 'max' => 16],
            ]
        ],
        'title' => [
            'name'  => 'タイトル',
            'rules' => [
                'required'     => true,
                'length_range' => ['min' => 10, 'max' => 32],
            ],
        ],
        'content' => [
            'name'  => '投稿内容',
            'rules' => [
                'required'     => true,
                'length_range' => ['min' => 10, 'max' => 200],
            ],
        ],
        'password' => [
            'name'  => 'パスワード',
            'rules' => [
                'required' => false,
                'digit'    => true,
                'length'   => 4,
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

    public function softDeleteById($id)
    {
        return $this->update(
            [
                'deleted_at' => date('Y-m-d H:i:s'),
                'image'      => null,
            ],
            [
                [
                    'column'    => $this->primary_key,
                    'condition' => '=',
                    'value'     => $id
                ]
            ]
        );
    }
}
