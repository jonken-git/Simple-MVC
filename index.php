<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 
require_once(__DIR__ . "/config/constants.php");
require_once(__TRAITS__ . "Singleton.php");
require_once(__CONFIG__ . "ErrorTypes.php");
require_once(__DIR__ . "/app/Request.php");
require_once(__DIR__ . "/app/Tools.php");
require_once("Server.php");

spl_autoload_register(function ($model) {
    include __MODELS__ . $model . '.php';
});
spl_autoload_register(function ($controller) {
    include __CONTROLLERS__ . $controller . '.php';
});

Server::serve();

