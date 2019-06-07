<?php
use Dotenv\Dotenv;
$dotenv = Dotenv::create(BASE);
$dotenv->load();
$app = new Slim\App(require_once (APP . "config.php"));
require_once (SRC . "Tools" . DS . "functions.php");
require_once (APP . "container.php");
require_once (SRC . "router.php");
$app->run();

