<?php
use Slim\App;
use PDO;
use Psr\Container\ContainerInterface;

return function (App $app) {
    $container = $app->getContainer();

    $config = require __DIR__ . '/database.php';
};