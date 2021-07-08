<?php

use classes\Controllers\User\RegistrationController;

require_once('/var/www/html/bbs/config/init.php');

$controller = new RegistrationController();
$controller->setParams(array_merge($_GET, $_POST));
$controller->setFiles($_FILES);
$controller->setEnvs($_SERVER);
$controller->execute('showRegistrationForm');
