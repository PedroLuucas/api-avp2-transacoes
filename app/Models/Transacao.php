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
            if ($e->getCode() === '23000') {
                throw new Exception("ID da transação já existe.", 0, $e);
            }
            throw new Exception("Erro ao inserir transação no banco de dados: " . $e->getMessage(), 0, $e);
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

    public function apagarPorId(string $id): bool
    {
        $sql = "DELETE FROM transacoes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao apagar transação por ID no banco de dados: " . $e->getMessage(), 0, $e);
        }
    }

    public function transacoesUltimoMinuto(): array
    {
        $sql = "SELECT valor FROM transacoes WHERE dataHora >= NOW() - INTERVAL 60 SECOND";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
