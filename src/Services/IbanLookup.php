<?php
declare(strict_types=1);

namespace App\Services;

final class IbanLookup
{
    private const ENDPOINT = 'https://openiban.com/validate/';
    private const TIMEOUT  = 5;

    public static function lookup(string $iban): ?string
    {
        $iban = preg_replace('/\s+/', '', strtoupper($iban));

        if ($iban === '') {
            return null;
        }

        $url = self::ENDPOINT . rawurlencode($iban) . '?getBIC=true';

        $context = stream_context_create([
            'http' => [
                'timeout' => self::TIMEOUT,
                'ignore_errors' => true,
                'header' => "User-Agent: Website-SEPA-Validator\r\n",
            ],
        ]);

        $json = @file_get_contents($url, false, $context);
        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }

        if (!($data['valid'] ?? false)) {
            return null;
        }

        return $data['bankData']['name'] ?? null;
    }
}
