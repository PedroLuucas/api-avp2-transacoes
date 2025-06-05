<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

// Criar a aplicação Slim
$app = AppFactory::create();

// Middleware para parsear JSON (essencial para POST/PUT)
$app->addBodyParsingMiddleware();

// Carregar configurações
(require __DIR__ . '/../config/settings.php')($app);

// Carregar rotas
(require __DIR__ . '/../app/Routes/routes.php')($app);

// Adicionar o middleware de roteamento (necessário para o Slim 4)
$app->addRoutingMiddleware();

// Adicionar o middleware de erro (para capturar exceptions e mostrar detalhes durante o desenvolvimento)
$app->addErrorMiddleware(true, true, true); // (displayErrorDetails, logErrors, logErrorDetails)

// Executa a aplicação
$app->run();