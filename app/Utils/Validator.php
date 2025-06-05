<?php
namespace App\Utils;

class Validator
{
    public static function validarTransacao(array $data): bool
    {
        if (!self::isISO8601($data['dataHora'])) {
            error_log("Validação: dataHora não é ISO8601: " . $data['dataHora']);
            return false;
        }

        $dataHoraTimestamp = strtotime($data['dataHora']);
        if ($dataHoraTimestamp === false) {
            error_log("Validação: strtotime falhou para dataHora: " . $data['dataHora']);
            return false;
        }
        if ($dataHoraTimestamp > time()) {
            error_log("Validação: dataHora no futuro. Enviada: " . date('Y-m-d H:i:s', $dataHoraTimestamp) . ", Agora: " . date('Y-m-d H:i:s', time()));
            return false;
        }

        if (!self::isUUID($data['id'])) {
            return false;
        }

        if (!is_numeric($data['valor']) || (float)$data['valor'] < 0) {
            return false;
        }

        if (!self::isISO8601($data['dataHora'])) {
            return false;
        }

        $dataHoraTimestamp = strtotime($data['dataHora']);
        if ($dataHoraTimestamp === false || $dataHoraTimestamp > time()) {
            return false;
        }

        return true;
    }

    private static function isUUID(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }

    private static function isISO8601(string $date): bool
    {
        $d = \DateTime::createFromFormat(\DateTime::ATOM, $date);
        return $d && $d->format(\DateTime::ATOM) === $date;
    }
}
