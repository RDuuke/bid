<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
define("DS", DIRECTORY_SEPARATOR);
define("BASE", dirname(__DIR__) . DS);
define("VIEW", dirname(__DIR__) . DS . "view" . DS);
define("RESOURCE", dirname(__DIR__) . DS . "resourse" . DS);
define("APP", dirname(__DIR__) . DS . "bootstrap" . DS);
define("SRC", dirname(__DIR__) . DS . "src" . DS);
define("TEMP", dirname(__DIR__) . DS . "resource" . DS . "temp" . DS);
define("FILES", dirname(__DIR__) . DS . "resource" . DS . "files" . DS);
date_default_timezone_set("America/Bogota");