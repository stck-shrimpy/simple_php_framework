<?php

namespace lib\Uploader;

use LogicException;
use RuntimeException;
use lib\Validator;

class FileUploader
{
    const UPLOAD_DIR_NAME = 'upload';

    protected $dir_path;
    protected $validation_params;

    public function __construct(string $dir = null)
    {
        $this->setDirPath($dir);
    }

    public function setDirPath(string $dir = null, bool $create_dir = true)
    {
        if (empty($dir)) {
            $dir = APP_ROOT . '/' . self::UPLOAD_DIR_NAME;
        } else {
            $dir = APP_ROOT . '/' . ltrim($dir, '/');
        }

        if (file_exists($dir) && is_file($dir)) {
            throw new LogicException(__METHOD__ . "() {$dir} はファイルです");
        }

        if (!file_exists($dir)) {
            if ($create_dir) {
                if (!mkdir($dir, 0777, true)) {
                    throw new RuntimeException(__METHOD__ . "() {$dir}の作成に失敗しました");
                }
            } else {
                throw new LogicException(__METHOD__ . "() {$dir}が存在しません");
            }
        }

        $this->dir_path = $dir;
    }

    public function setValidationParams(array $validation_params)
    {
        $this->validation_params = $validation_params;
    }

    public function save($data, string $file_name, bool $uniq_name = true)
    {
        $_file_name = $file_name;
        if ($uniq_name) {
            $_file_name = $this->generateFileName(pathinfo($file_name, PATHINFO_EXTENSION));
        }

        $file_path = $this->dir_path . '/' . $_file_name;
        if (!file_put_contents($file_path, $data)) {
            throw new RuntimeException(__METHOD__ . "() ファイルの保存に失敗しました '{$file_path}'");
        }

        return $_file_name;
    }

    public function delete(string $file_name, bool $move = false): bool
    {
        if ($move) {
            $src_path  = $this->dir_path . '/' . $file_name;
            $dest_dir  = $this->dir_path . '/deleted';
            $dest_path = $dest_dir . '/' . $file_name;
            if (!is_dir($dest_dir)) {
                if (!mkdir($dest_dir, 0777, true)) {
                  throw new RuntimeException(__METHOD__ . "() ディレクトリーの作成に失敗しました '{$dest_dir}'.");
                }
              }

              chmod($dest_dir, 0777);
              chmod($src_path, 0777);

              if (!rename($src_path, $dest_path)) {
                throw new RuntimeException(__METHOD__ . "() ファイルの移動に失敗しました. '{$src_path}' -> '{$dest_path}'");
              }
        } else {
            $file_path = $this->dir_path . '/' . $file_name;
            if (!file_exists($file_path)) {
                throw new RuntimeException(__METHOD__ . "() ディレクトリーパスにファイルが存在しません '{$file_path}'");
            }

            if (!unlink($file_path)) {
                throw new RuntimeException(__METHOD__ . "() ファイルの削除に失敗しました '{$file_path}'");
            }
        }

        return true;
    }

    public function generateFileName(string $ext): string
    {
        $file_name = get_uniq_string() . '.' . $ext;
        $file_path = $this->dir_path . get_uniq_string() . '.' . $ext;
        while (file_exists($file_path)) {
            $file_name = get_uniq_string() . '.' . $ext;
            $file_path = $this->dir_path . $file_name;
        }

        return $file_name;
    }

    public function validate(array $file, string $validation_setting_key): array
    {
        $validator = new Validator([$validation_setting_key => $this->getValidationParams()]);

        return $validator->validate([$validation_setting_key => $file]);
    }

    protected function getValidationParams(): array
    {
        if (empty($this->validation_params)) {
            throw new LogicException(__METHOD__ . '() バリデーションのパラメータが設定されていません');
        }

        return $this->validation_params;
    }
}
