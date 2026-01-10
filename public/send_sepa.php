<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/
require dirname(__DIR__) . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| .env laden
|--------------------------------------------------------------------------
*/
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

/*
|--------------------------------------------------------------------------
| Global Error & Exception Handling (KERNSTÜCK)
|--------------------------------------------------------------------------
*/
set_error_handler(function (
    int $severity,
    string $message,
    string $file,
    int $line
) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $e) {
    respond(500, [
        'ok'      => false,
        'code'    => 'SERVER',
        'message' => 'Interner Serverfehler.',
        // DEBUG lokal optional:
        'debug' => $e->getMessage(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Response Helper (EINZIGE Output-Stelle)
|--------------------------------------------------------------------------
*/
function respond(int $status, array $payload): never
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
    }

    http_response_code($status);

    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| IBAN-Lookup
|--------------------------------------------------------------------------
*/
function ibanLookupAllowed(): bool
{
    $_SESSION['iban_lookup'] ??= [
        'count' => 0,
        'time'  => time(),
    ];

    // Reset nach 1 Stunde
    if (time() - $_SESSION['iban_lookup']['time'] > 3600) {
        $_SESSION['iban_lookup'] = [
            'count' => 0,
            'time'  => time(),
        ];
    }

    if ($_SESSION['iban_lookup']['count'] >= 10) {
        return false;
    }

    $_SESSION['iban_lookup']['count']++;
    return true;
}


/*
|--------------------------------------------------------------------------
| Guard: Nur POST
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, [
        'ok'      => false,
        'code'    => 'METHOD',
        'message' => 'Methode nicht erlaubt.',
    ]);
}

/*
|--------------------------------------------------------------------------
| Security: CSRF
|--------------------------------------------------------------------------
*/
if (!csrf_verify($_POST['_csrf'] ?? null)) {
    respond(403, [
        'ok'      => false,
        'code'    => 'CSRF',
        'message' => 'Ungültige Anfrage.',
    ]);
}

/*
|--------------------------------------------------------------------------
| Security: Honeypot
|--------------------------------------------------------------------------
*/
if (!empty($_POST['website'] ?? '')) {
    respond(200, [
        'ok'      => true,
        'message' => 'Danke.',
    ]);
}

/*
|--------------------------------------------------------------------------
| Voraussetzungen prüfen
|--------------------------------------------------------------------------
*/
$required = [
    'MAIL_HOST',
    'MAIL_PORT',
    'MAIL_USER',
    'MAIL_PASS',
    'MAIL_FROM',
    'MAIL_TO',
];

foreach ($required as $key) {
    if (empty($_ENV[$key] ?? null)) {
        throw new RuntimeException("Missing env variable: $key");
    }
}

/*
|--------------------------------------------------------------------------
| Input lesen
|--------------------------------------------------------------------------
*/
use app\Helpers\Helpers;

$vorname            = Helpers::clean($_POST['vorname'] ?? '');
$nachname           = Helpers::clean($_POST['nachname'] ?? '');
$email              = Helpers::clean($_POST['email'] ?? '');

$strasse            = Helpers::clean($_POST['strasse'] ?? '');
$plz                = Helpers::clean($_POST['plz'] ?? '');
$ort                = Helpers::clean($_POST['ort'] ?? '');

$iban               = Helpers::clean($_POST['iban'] ?? '');
$bank               = Helpers::clean($_POST['bank'] ?? '');

$beitrag            = Helpers::clean($_POST['betrag'] ?? '');
$zahlungsrhythmus   = Helpers::clean($_POST['zahlungsrhythmus'] ?? '');
$mitgliedschaft     = Helpers::clean($_POST['mitgliedschaft'] ?? '');
$nachricht          = Helpers::clean($_POST['nachricht'] ?? '');

$consent            = isset($_POST['consent']);
$consentdatenschutz = isset($_POST['consent-datenschutz']);
$consentsepa        = isset($_POST['consent-sepa']);

/*
|--------------------------------------------------------------------------
| Validierung
|--------------------------------------------------------------------------
*/
$errors = [];

if ($vorname === '') {
    $errors[] = 'Vorname fehlt';
}

if ($nachname === '') {
    $errors[] = 'Nachname fehlt';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-Mail ungültig';
}

if ($strasse === '' || $plz == '' || $ort === '') {
    $errors[] = 'Adresse nicht vollständig';
}

if (!$consent || !$consentdatenschutz || !$consentsepa ) {
    $errors[] = 'Einwilligung fehlt';
}

if ($zahlungsrhythmus === '') {
    $errors[] = 'Zahlungsrhythmus fehlt';
}

if ($nachricht === '' ){
    $nachricht = 'keine';
} 

// IBAN Lookup
use App\Services\IbanLookup;
$bankFromApi = null;

if (ibanLookupAllowed()) {
    $bankFromApi = IbanLookup::lookup($iban);
}

if ($bankFromApi !== null) {
    $bank = $bankFromApi; // überschreibt Formularwert
}

// IBAN-Validator
use App\Security\IbanValidator;
$ibanValidator = new IbanValidator();

// final check IBAN
if (!$ibanValidator->isValid($iban)) {
    $errors[] = 'Die eingegebene IBAN ist ungültig';
}

if ($errors) {
    respond(400, [
        'ok'      => false,
        'code'    => 'VALIDATION',
        'message' => 'Bitte Eingaben prüfen.',
        'errors'  => $errors,
    ]);
}

// Mandats-ID
$mandateId = 'SEPA-' . date('Ymd') . '-' . bin2hex(random_bytes(4));

// =======================
// SEPA PDF
// =======================
use App\Helpers\SepaPdf;
$pdf = new SepaPdf();
$pdfPath = $pdf->create([
    'place'             => $_ENV['PLACE'],
    'date'              => date('d.m.Y'),
    'creditor_name'     => $_ENV['SEPA_CREDITOR_NAME'],
    'creditor_adress'   => $_ENV['SEPA_CREDITOR_ADRESS'],
    'creditor_id'       => $_ENV['SEPA_CREDITOR_ID'],
    'name'              => $vorname . ' ' . $nachname,
    'strasse'           => $strasse,
    'plz'               => $plz,
    'ort'               => $ort,
    'email'             => $email,
    'iban'              => $iban,
    'bank'              => $bank,
    'fee'               => $beitrag,
    'frequ'             => $zahlungsrhythmus,
    'memship'           => $mitgliedschaft,
    'mes'               => $nachricht,
    'mandate_id'        => $mandateId,
    'consent_ts'        => date('c'),
    'consent'           => $consent,
    'consentds'         => $consentdatenschutz,
    'consentsepa'       => $consentsepa,
]);

/*
|--------------------------------------------------------------------------
| Business: Mailversand
|--------------------------------------------------------------------------
*/
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host       = $_ENV['MAIL_HOST'];
$mail->SMTPAuth   = true;
$mail->Username   = $_ENV['MAIL_USER'];
$mail->Password   = $_ENV['MAIL_PASS'];
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = (int) $_ENV['MAIL_PORT'];

$mail->CharSet  = 'UTF-8';
$mail->Encoding = 'base64';
$mail->isHTML(false);

$mail->setFrom($_ENV['MAIL_FROM'], 'Website');
$mail->addAddress($_ENV['MAIL_TO']);
$mail->addReplyTo($email, "$vorname $nachname");

$mail->Subject = 'Neue Förderung (SEPA)';

$mail->Body =
"Förderung\n\n".
"Name: $vorname $nachname\n".
"Beitrag: $beitrag\n".
"IBAN (maskiert): ".Helpers::maskIBAN($iban)."\n\n".
"Mitgliedschaft: $mitgliedschaft\n".
"Nachricht: $nachricht\n\n".
"SEPA-Mandat siehe PDF-Anhang.";

$mail->addAttachment($pdfPath, 'SEPA-Mandat.pdf');

try {
    $mail->send();
    // Errorhandling über Exception
} finally {
    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }
}

/*
|--------------------------------------------------------------------------
| Erfolg
|--------------------------------------------------------------------------
*/
csrf_regenerate();

respond(200, [
    'ok'      => true,
    'code'    => 'SENT',
    'message' => 'Danke! Nachricht wurde gesendet.',
    'csrf'    => csrf_token(),
]);
