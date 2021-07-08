<?php

namespace classes\Storages;

use lib\Storage\BaseStorage;

class AdminsStorage extends BaseStorage
{
    protected $table           = 'admins';
    protected $password_column = 'password';
}
