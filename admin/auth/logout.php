<?php

use classes\Controllers\Admin\AdminAuthController;

require_once('/var/www/html/bbs/config/init.php');

$controller = new AdminAuthController();
$controller->setParams(array_merge($_GET, $_POST));
$controller->setFiles($_FILES);
$controller->setEnvs($_SERVER);
$controller->execute('logout');
