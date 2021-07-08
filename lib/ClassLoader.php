<?php

class ClassLoader
{
    public static function autoload($class_name)
    {
        if (class_exists($class_name)) {
            return;
        }

        $full_path = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
        if (is_readable($full_path)) {
            require_once($full_path);
            return;
        }
    }
}
