<?php
require_once 'v1ktor/core/SplClassLoader.php';

$appControllers = new SplClassLoader('v1ktor\app\controllers', __DIR__);
$appControllers->register();

$core = new SplClassLoader('v1ktor\core', __DIR__);
$core->register();

use v1ktor\core\FrontController;

$frontController = new FrontController();
$frontController->run();