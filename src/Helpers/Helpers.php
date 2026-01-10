<?php
namespace App\Helpers;

final class Helpers
{
    public static function clean(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function maskIBAN(string $iban): string
    {
        $iban = preg_replace('/\s+/', '', $iban);
        return substr($iban, 0, 4) . ' **** **** ' . substr($iban, -4);
    }

    public static function is_dev(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'prod') === 'dev';
    }

}
