<?php
use Slim\App;
use PDO;
use Psr\Container\ContainerInterface;

return function (App $app) {
    $container = $app->getContainer();

    $config = require __DIR__ . '/database.php';

    // Adiciona a conexão PDO ao container
    $container[PDO::class] = function (ContainerInterface $c) use ($config) {
        return new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']}",
            $config['user'],
            $config['pass']
        );
    };
};