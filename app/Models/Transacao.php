<?php
namespace App\Models;

use PDO;
use PDOException;
use Exception;

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

     public function buscarPorId(string $id): ?array
    {
        $sql = "SELECT id, valor, dataHora FROM transacoes WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function apagarPorId(string $id): bool
    {
        $sql = "DELETE FROM transacoes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao apagar transação por ID: " . $e->getMessage(), 0, $e);
        }
    }
}