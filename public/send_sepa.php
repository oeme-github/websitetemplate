<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

setApiSecurityHeaders();

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

formBootstrap();
guardMethod();
guardCsrf();
guardHoneypot();
requireEnvKeys([
    'MAIL_HOST', 'MAIL_PORT', 'MAIL_USER', 'MAIL_PASS', 'MAIL_FROM', 'MAIL_TO',
    'PLACE', 'SEPA_CREDITOR_NAME', 'SEPA_CREDITOR_ADRESS', 'SEPA_CREDITOR_ID',
]);

/*
|--------------------------------------------------------------------------
| IBAN-Lookup Rate-Limit (session-basiert)
|--------------------------------------------------------------------------
*/
function ibanLookupAllowed(): bool
{
    $_SESSION['iban_lookup'] ??= ['count' => 0, 'time' => time()];

    if (time() - $_SESSION['iban_lookup']['time'] > 3600) {
        $_SESSION['iban_lookup'] = ['count' => 0, 'time' => time()];
    }

    if ($_SESSION['iban_lookup']['count'] >= 10) {
        return false;
    }

    $_SESSION['iban_lookup']['count']++;
    return true;
}

/*
|--------------------------------------------------------------------------
| Input lesen
|--------------------------------------------------------------------------
*/
use App\Helpers\Helpers;

$vorname          = Helpers::clean($_POST['vorname']          ?? '');
$nachname         = Helpers::clean($_POST['nachname']         ?? '');
$email            = Helpers::clean($_POST['email']            ?? '');
$strasse          = Helpers::clean($_POST['strasse']          ?? '');
$plz              = Helpers::clean($_POST['plz']              ?? '');
$ort              = Helpers::clean($_POST['ort']              ?? '');
$iban             = Helpers::clean($_POST['iban']             ?? '');
$bank             = Helpers::clean($_POST['bank']             ?? '');
$beitrag          = Helpers::clean($_POST['betrag']           ?? '');
$zahlungsrhythmus = Helpers::clean($_POST['zahlungsrhythmus'] ?? '');
$mitgliedschaft   = Helpers::clean($_POST['mitgliedschaft']   ?? '');
$nachricht        = Helpers::clean($_POST['nachricht']        ?? '');

$consent            = isset($_POST['consent']);
$consentdatenschutz = isset($_POST['consent-datenschutz']);
$consentsepa        = isset($_POST['consent-sepa']);

/*
|--------------------------------------------------------------------------
| Validierung
|--------------------------------------------------------------------------
*/
$errors = [];

$validBeitrag = ['10 €', '25 €', '50 €', '100 €', 'Nachricht'];
$validRhythmus = ['Spende einmalig', 'Jahr', 'Qurtal', 'Monat', 'Nachricht'];
$validMitgliedschaft = ['Ja', 'Nein', 'Nachricht'];

if ($vorname === '') {
    $errors[] = 'Vorname fehlt';
}

if ($nachname === '') {
    $errors[] = 'Nachname fehlt';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-Mail ungültig';
}

if ($strasse === '' || $plz === '' || $ort === '') {
    $errors[] = 'Adresse nicht vollständig';
}

if (!in_array($beitrag, $validBeitrag, true)) {
    $errors[] = 'Beitrag ungültig';
}

if (!in_array($zahlungsrhythmus, $validRhythmus, true)) {
    $errors[] = 'Zahlungsrhythmus ungültig';
}

if (!in_array($mitgliedschaft, $validMitgliedschaft, true)) {
    $errors[] = 'Mitgliedschaft ungültig';
}

if (!$consent || !$consentdatenschutz || !$consentsepa) {
    $errors[] = 'Einwilligung fehlt';
}

if ($nachricht === '') {
    $nachricht = 'keine';
}

// IBAN-Lookup
use App\Services\IbanLookup;
$bankFromApi = null;

if (ibanLookupAllowed()) {
    $bankFromApi = IbanLookup::lookup($iban);
}

if ($bankFromApi !== null) {
    $bank = $bankFromApi;
}

// IBAN-Validierung
use App\Security\IbanValidator;
$ibanValidator = new IbanValidator();

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

/*
|--------------------------------------------------------------------------
| SEPA PDF
|--------------------------------------------------------------------------
*/
$mandateId = 'SEPA-' . date('Ymd') . '-' . bin2hex(random_bytes(4));

use App\Helpers\SepaPdf;
$pdf = new SepaPdf();
$pdfPath = $pdf->create([
    'place'           => $_ENV['PLACE'],
    'date'            => date('d.m.Y'),
    'creditor_name'   => $_ENV['SEPA_CREDITOR_NAME'],
    'creditor_adress' => $_ENV['SEPA_CREDITOR_ADRESS'],
    'creditor_id'     => $_ENV['SEPA_CREDITOR_ID'],
    'name'            => "$vorname $nachname",
    'strasse'         => $strasse,
    'plz'             => $plz,
    'ort'             => $ort,
    'email'           => $email,
    'iban'            => $iban,
    'bank'            => $bank,
    'fee'             => $beitrag,
    'frequ'           => $zahlungsrhythmus,
    'memship'         => $mitgliedschaft,
    'mes'             => $nachricht,
    'mandate_id'      => $mandateId,
    'consent_ts'      => date('c'),
    'consent'         => $consent,
    'consentds'       => $consentdatenschutz,
    'consentsepa'     => $consentsepa,
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
    "Förderung\n\n" .
    "Name: $vorname $nachname\n" .
    "Beitrag: $beitrag\n" .
    "IBAN (maskiert): " . Helpers::maskIBAN($iban) . "\n\n" .
    "Mitgliedschaft: $mitgliedschaft\n" .
    "Nachricht: $nachricht\n\n" .
    "SEPA-Mandat siehe PDF-Anhang.";

$mail->addAttachment($pdfPath, 'SEPA-Mandat.pdf');

try {
    $mail->send();
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
