<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Transacao;
use App\Utils\Validator;
use App\Services\EstatisticaService;
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

    public function buscar(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        $transacaoModel = new Transacao($this->db);
        $dados = $transacaoModel->buscarPorId($id);

        if ($dados) {
            $response->getBody()->write(json_encode($dados));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        return $response->withStatus(404); 
    }

    public function apagarTudo(): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec("DELETE FROM transacoes");
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Erro ao apagar todas as transações: " . $e->getMessage(), 0, $e);
        }
    }

    public function apagar(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        
        try {
            $transacaoModel = new Transacao($this->db);
            if ($transacaoModel->apagarPorId($id)) {
                return $response->withStatus(200);
            }
            return $response->withStatus(404);
        } catch (Exception $e) {
            error_log("Erro ao apagar transação por ID: " . $e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function estatisticas(Request $request, Response $response): Response
    {
        try {
            $estatisticaService = new EstatisticaService($this->db);
            $resultado = $estatisticaService->calcular();

            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            error_log("Erro ao calcular estatísticas: " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
