<?php

use app\core\Application;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

const ROOT_DIR = __DIR__ . '/..';
const CONFIG_DIR = ROOT_DIR . '/config';

const CONTROLLER_NAMESPACE = 'app\controllers';

require_once __DIR__ . '/utils.php';

header('Content-type: text/javascript');

$app = new Application();
$app->run();