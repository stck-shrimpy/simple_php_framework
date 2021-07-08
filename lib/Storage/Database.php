<?php

namespace lib\Storage;

use LogicException;
abstract class Database
{
    protected $conn;
    protected $config;

    public function __construct()
    {
        $this->setConfig();
        $this->connect();
    }

    private function setConfig()
    {
        if (!defined('DB_CONFIG')) {
            throw new LogicException (__METHOD__ . '() データベース設定が存在しません');
        }

        $this->config = DB_CONFIG;
    }

    abstract protected function connect();
}
