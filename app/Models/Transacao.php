<?php
namespace App\Models;

use PDO;
use PDOException;

class Transacao
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function criar(array $data): void
    {
        $sql = "INSERT INTO transacoes (id, valor, dataHora) VALUES (:id, :valor, :dataHora)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':id' => $data['id'],
                ':valor' => $data['valor'],
                ':dataHora' => $data['dataHora'],
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao inserir transação: " . $e->getMessage(), 0, $e);
        }
    }
}