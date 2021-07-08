<?php

error_reporting(E_ALL);

define('BASE_PATH', '/bbs');
define('APP_ROOT', '/var/www/html' . BASE_PATH);

define('CLASSES_DIR', APP_ROOT . '/classes');
define('LIB_DIR',     APP_ROOT . '/lib');
define('LOGS_DIR',    APP_ROOT . '/logs');
define('HTML_DIR',    APP_ROOT . '/html');
define('CSS_DIR',     APP_ROOT . '/css');
define('CONFIG_DIR',  APP_ROOT . '/config');
define('UPLOAD_DIR',  APP_ROOT . '/upload');

require_once(CONFIG_DIR . '/database.php');
require_once(LIB_DIR    . '/functions.php');
require_once(LIB_DIR    . '/ClassLoader.php');

spl_autoload_register(['ClassLoader', 'autoload']);
