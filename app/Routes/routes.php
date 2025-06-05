<?php
use Slim\App;
use App\Controllers\TransacaoController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Exception;

return function (App $app) {

    $createPdoConnection = function () use ($app) {
        $config = require __DIR__ . '/../../config/database.php';
        try {
            $pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
            throw new Exception("Não foi possível conectar ao banco de dados.", 0, $e);
        }
    };

    $app->post('/transacao', function (Request $request, Response $response) use ($createPdoConnection) {
        $db = $createPdoConnection();
        $controller = new TransacaoController($db);
        return $controller->criar($request, $response);
    });

    $app->get('/transacao/{id}', function (Request $request, Response $response, array $args) use ($createPdoConnection) {
        $db = $createPdoConnection();
        $controller = new TransacaoController($db);
        return $controller->buscar($request, $response, $args);
    });

    $app->delete('/transacao', function (Request $request, Response $response) use ($createPdoConnection) {
        $db = $createPdoConnection();
        $controller = new TransacaoController($db);
        return $controller->apagarTudo($request, $response);
    });

};
