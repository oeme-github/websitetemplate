<?php
namespace App;

class Helpers
{
    public static function clean(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function maskIBAN(string $iban): string {
        $iban = preg_replace('/\s+/', '', $iban);
        return substr($iban, 0, 4) . ' **** **** ' . substr($iban, -4);
    }
    
    private function mod97(string $numeric): int
    {
        $checksum = 0;
        for ($i = 0; $i < strlen($numeric); $i++) {
            $checksum = ($checksum * 10 + (int)$numeric[$i]) % 97;
        }
        return $checksum;
    }

    public function isValidIban(string $iban): bool
    {
        $iban = strtoupper(str_replace(' ', '', $iban));

        if (!preg_match('/^[A-Z0-9]{15,34}$/', $iban)) {
            return false;
        }

        $lengths = [
            'DE' => 22,
            'AT' => 20,
            'CH' => 21,
            'NL' => 18,
            'BE' => 16
        ];

        $country = substr($iban, 0, 2);
        if (!isset($lengths[$country]) || strlen($iban) !== $lengths[$country]) {
            return false;
        }

        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';

        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char)
                ? ord($char) - 55
                : $char;
        }

        return $this->mod97($numeric);
    }

}
