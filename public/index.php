<?php

require '../powon_autoload.php';

$settings = require '../config/settings.php';

$app = new \Slim\App($settings);


// Set up dependencies
require __DIR__.'/../src/dependencies.php';
// Register middleware
require __DIR__.'/../src/Middleware/middleware.php';
// Register routes
require __DIR__ . '/../src/Routes/routes.php';



$app->run();
