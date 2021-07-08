<?php

use classes\Controllers\User\BulletinController;

require_once('/var/www/html/bbs/config/init.php');

$controller = new BulletinController();
$controller->setParams(array_merge($_GET, $_POST));
$controller->setFiles($_FILES);
$controller->setEnvs($_SERVER);
$controller->execute('delete');
