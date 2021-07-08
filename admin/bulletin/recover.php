<?php

use classes\Controllers\Admin\AdminBulletinController;

require_once('/var/www/html/bbs/config/init.php');

$controller = new AdminBulletinController();
$controller->setParams(array_merge($_GET, $_POST));
$controller->setFiles($_FILES);
$controller->setEnvs($_SERVER);
$controller->execute('recover');
