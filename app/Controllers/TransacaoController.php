<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Transacao;
use App\Utils\Validator;
use PDO;
use Exception;

class TransacaoController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function criar(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!Validator::validarTransacao($data)) {
            return $response->withStatus(422);
        }

        try {
            $transacao = new Transacao($this->db);
            $transacao->criar($data);
            return $response->withStatus(201);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'for key \'PRIMARY\'') !== false) {
                return $response->withStatus(422);
            }
            return $response->withStatus(400);
        }
    }
}
