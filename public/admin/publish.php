<?php
require_once __DIR__ . '/../../src/Autoloader.php';

use StackPress\Config\Config;
use StackPress\Controller\BuildController;

Config::getInstance();

$controller = new BuildController();
$controller->build();

header('Location: /public/admin/index.php');
exit;