<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

(require __DIR__ . '/../config/settings.php')($app);

(require __DIR__ . '/../app/Routes/routes.php')($app);

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true); // (displayErrorDetails, logErrors, logErrorDetails)

$app->run();