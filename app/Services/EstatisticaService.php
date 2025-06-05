<?php
namespace App\Services;

use App\Models\Transacao;
use PDO;

class EstatisticaService
{
    private Transacao $transacaoModel;

    public function __construct(PDO $db)
    {
        $this->transacaoModel = new Transacao($db);
    }

    public function calcular(): array
    {
        $transacoes = $this->transacaoModel->transacoesUltimoMinuto();
        $valores = array_column($transacoes, 'valor');

        if (empty($valores)) {
            return [
                'sum' => 0.00,
                'avg' => 0.00,
                'min' => 0.00,
                'max' => 0.00,
                'count' => 0
            ];
        }

        $sum = array_sum($valores);
        $count = count($valores);
        $avg = $count > 0 ? round($sum / $count, 2) : 0.00;
        $min = min($valores);
        $max = max($valores);

        return [
            'count' => $count,
            'sum' => round($sum, 2),
            'avg' => $avg,
            'min' => round($min, 2),
            'max' => round($max, 2)
        ];
    }
}
