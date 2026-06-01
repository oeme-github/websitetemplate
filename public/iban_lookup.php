<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

setApiSecurityHeaders();
formBootstrap();

use App\Services\IbanLookup;

$_SESSION['_rate_iban_lookup'] ??= [];

if (!rateLimitCheck($_SESSION['_rate_iban_lookup'], time(), 10, 3600)) {
    respond(429, ['ok' => false, 'bank' => null]);
}

$iban = preg_replace('/\s+/', '', strtoupper((string) ($_GET['iban'] ?? '')));

if ($iban === '') {
    respond(200, ['ok' => false, 'bank' => null]);
}

$bank = IbanLookup::lookup($iban);

respond(200, ['ok' => $bank !== null, 'bank' => $bank]);
