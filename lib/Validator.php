<?php

namespace lib;

use LogicException;
class Validator
{
    protected $settings;

    public function __construct(array $settings)
    {
        if (!$settings) {
            throw new LogicException(__METHOD__ . '() バリデーション設定が存在しません！');
        }

        $this->settings = $settings;
    }

    public function validate(array $inputs): array
    {
        $error_messages = [];
        foreach ($this->settings as $validation_key => $setting) {
            $validation_name = $setting['name'];
            $rules           = $setting['rules'];

            if (!array_key_exists($validation_key, $inputs)) {
                continue;
            }

            if (isset($rules['required']) && $rules['required'] === true) {
                if ($error_message = self::required($inputs[$validation_key], $validation_name)) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['digit']) && $rules['digit'] === true) {
                if ($error_message = self::digit($inputs[$validation_key], $validation_name)) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['email']) && $rules['email'] === true) {
                if ($error_message = self::email($inputs[$validation_key], $validation_name)) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['length'])) {
                if ($error_message = self::length($inputs[$validation_key], $validation_name, $rules['length'])) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['length_range'])) {
                $min = null;
                if (isset($rules['length_range']['min'])) {
                    $min = $rules['length_range']['min'];
                }

                $max = null;
                if (isset($rules['length_range']['max'])) {
                    $max = $rules['length_range']['max'];
                }

                if ($error_message = self::lengthRange($inputs[$validation_key], $validation_name, $min, $max)) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['file_size'])) {
                if ($error_message = self::fileSize($inputs[$validation_key], $validation_name, $rules['file_size'])) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['file_mimes'])) {
                if ($error_message = self::fileMime($inputs[$validation_key], $validation_name, $rules['file_mimes'])) {
                    $error_messages[] = $error_message;
                }
            }

            if (isset($rules['file_extensions'])) {
                if ($error_message = self::fileExtension($inputs[$validation_key], $validation_name, $rules['file_extensions'])) {
                    $error_messages[] = $error_message;
                }
            }
        }

        return $error_messages;
    }

    public static function required($input, string $validation_name): string
    {
        if (is_empty($input)) {
            return "{$validation_name}を必ず入力してください！";
        }

        return '';
    }

    public static function digit($input, string $validation_name): string
    {
        if (is_empty($input)) {
            return '';
        }

        if (!ctype_digit($input)) {
            return "{$validation_name}は半角数字で入力してください！";
        }

        return '';
    }

    public static function email($input, string $validation_name): string
    {
        if (is_empty($input)) {
            return '';
        }

        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return "{$validation_name}を正しく入力してください！";
        }

        return '';
    }

    public static function length($input, string $validation_name, int $length): string
    {
        if (is_empty($input)) {
            return '';
        }

        $input_length = mb_strlen($input);
        if ($input_length !== $length) {
            return "{$validation_name}は{$length}文字で入力してください！";
        }

        return '';
    }

    public static function lengthRange($input, string $validation_name, int $min = null, int $max = null): string
    {
        if (is_empty($input)) {
            return '';
        }

        $input_length = mb_strlen($input);
        if ($min && $max) {
            if ($input_length < $min || $input_length > $max) {
                return "{$validation_name}は{$min}文字以上{$max}文字以内で入力してください！";
            }
        } elseif ($min) {
            if ($input_length < $min) {
                return "{$validation_name}は{$min}文字以上で入力してください！";
            }
        } elseif ($max) {
            if ($input_length > $max) {
                return "{$validation_name}は{$max}文字以内で入力してください!";
            }
        }

        return '';
    }

    public static function fileSize($input, string $validation_name, int $size): string
    {
        if (empty($input['size'])) {
            return '';
        }

        if ($input['size'] > $size) {
            return "{$validation_name}は" . format_byte($size) . '以下にしてください！';
        }

        return '';
    }

    public static function fileMime($input, string $validation_name, array $mimes): string
    {
        if (empty($input['size'])) {
            return '';
        }

        if (!in_array(mime_content_type($input['tmp_name']), $mimes)) {
            return "正しい{$validation_name}ファイルを入力してください";
        }

        return '';
    }

    public static function fileExtension($input, string $validation_name, array $extensions): string
    {
        if (empty($input['size'])) {
            return '';
        }

        if (!in_array(pathinfo($input['name'], PATHINFO_EXTENSION), $extensions)) {
            return "{$validation_name}の拡張子は" . implode(', ', $extensions) . 'にしてください！';
        }

        return '';
    }
}
