<?php
session_start();
define("DS", DIRECTORY_SEPARATOR);
define("BASE", dirname(__DIR__) . DS);
define("VIEW", dirname(__DIR__) . DS . "view" . DS);
define("RESOURCE", dirname(__DIR__) . DS . "resourse" . DS);
define("APP", dirname(__DIR__) . DS . "bootstrap" . DS);
define("SRC", dirname(__DIR__) . DS . "src" . DS);
define("TEMP", dirname(__DIR__) . DS . "resourse" . DS . "temp" . DS);
date_default_timezone_set("America/Bogota");