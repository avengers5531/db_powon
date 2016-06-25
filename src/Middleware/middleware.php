<?php
// Application middleware

$app->add(new \Powon\Middleware\SessionLoader($container['logger'], $container['sessionService']));
