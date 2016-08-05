<?php
require '../powon_autoload.php';

$settings = require '../config/settings.php';

$app = new \Slim\App($settings);


// Set up dependencies
require __DIR__.'/../src/dependencies.php';
// Register routes
require __DIR__ . '/../src/Routes/routes.php';
// Register general middleware last
// outermost 'shell'; see http://www.slimframework.com/docs/concepts/middleware.html
require __DIR__.'/../src/Middleware/middleware.php';



$app->run();
