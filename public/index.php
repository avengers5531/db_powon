<?php

require '../vendor/autoload.php';
$settings = require '../config/settings.php';

$app = new \Slim\App($settings);


// Set up dependencies
require __DIR__.'/../src/dependencies.php';
// Register middleware
require __DIR__.'/../src/middleware.php';
// Register routes
require __DIR__ .'/../src/routes/routes.php';



$app->run();
