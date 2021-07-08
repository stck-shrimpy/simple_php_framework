<?php

namespace lib;

use LogicException;
use RuntimeException;

class Logger
{
    protected $dir_path = '';
    protected $files    = [];

    public function __construct(string $dir = null)
    {
        $this->setDirPath($dir);

        register_shutdown_function([$this, 'shutdown']);
    }

    public function setDirPath(string $dir = null, bool $create_dir = true)
    {
        if (empty($dir)) {
            if (defined('LOGS_DIR')) {
              $dir = LOGS_DIR;
            } else {
              throw new LogicException(__METHOD__ . '() ログディレクトリーが定義されていません');
            }
        }

        if (file_exists($dir) && is_file($dir)) {
            throw new RuntimeException(__METHOD__ . "() {$dir} はファイルです");
        }

        if (!file_exists($dir)) {
            if ($create_dir) {
                if (!mkdir($dir, 0777)) {
                    throw new RuntimeException(__METHOD__ . "() {$dir}の作成に失敗しました");
                }
            } else {
                throw new RuntimeException(__METHOD__ . "() {$dir}が存在しません");
            }
        }

        $this->dir_path = $dir;
    }

    public function write(string $message, int $errType = E_ALL, string $name = 'default')
    {
        $file_name = $name . '.log';
        if (!isset($this->files[$file_name])) {
            chmod ($this->dir_path, 0777);
            $this->files[$file_name] = fopen($this->dir_path . '/' . $file_name, 'a+');
        }

        $log = trim('[' . date('Y-m-d H:i:s') . '] ' . $this->errTypeToString($errType) . ' ' . $message) . PHP_EOL;
        chmod ($this->dir_path . '/' . $file_name, 0777);
        fwrite($this->files[$file_name], $log);

    }

    public function shutdown()
    {
        foreach ($this->files as $fp) {
            fclose($fp);
        }
        $this->files = [];
    }

    protected function errTypeToString(int $type): string
    {
        switch($type) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return $type;
    }

}
