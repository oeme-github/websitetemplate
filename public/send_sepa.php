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
guardRateLimit('sepa');
requireEnvKeys([
    'MAIL_HOST', 'MAIL_PORT', 'MAIL_FROM', 'MAIL_TO',
    'PLACE', 'SEPA_CREDITOR_NAME', 'SEPA_CREDITOR_ADRESS', 'SEPA_CREDITOR_ID',
]);

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
    $errors['vorname'] = 'Vorname fehlt';
}

if ($nachname === '') {
    $errors['nachname'] = 'Nachname fehlt';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'E-Mail ungültig';
}

if ($strasse === '') {
    $errors['strasse'] = 'Straße fehlt';
}

if ($plz === '') {
    $errors['plz'] = 'PLZ fehlt';
}

if ($ort === '') {
    $errors['ort'] = 'Ort fehlt';
}

if (!in_array($beitrag, $validBeitrag, true)) {
    $errors['betrag'] = 'Beitrag ungültig';
}

if (!in_array($zahlungsrhythmus, $validRhythmus, true)) {
    $errors['zahlungsrhythmus'] = 'Zahlungsrhythmus ungültig';
}

if (!in_array($mitgliedschaft, $validMitgliedschaft, true)) {
    $errors['mitgliedschaft'] = 'Mitgliedschaft ungültig';
}

if (!$consent) {
    $errors['consent'] = 'DSGVO-Einwilligung fehlt';
}

if (!$consentdatenschutz) {
    $errors['consent-datenschutz'] = 'Datenschutz-Einwilligung fehlt';
}

if (!$consentsepa) {
    $errors['consent-sepa'] = 'SEPA-Einwilligung fehlt';
}

if ($nachricht === '') {
    $nachricht = 'keine';
}

// IBAN-Lookup
use App\Services\IbanLookup;
$bankFromApi = null;

$_SESSION['_rate_iban_lookup'] ??= [];
if (rateLimitCheck($_SESSION['_rate_iban_lookup'], time(), 10, 3600)) {
    $bankFromApi = IbanLookup::lookup($iban);
}

if ($bankFromApi !== null) {
    $bank = $bankFromApi;
}

// IBAN-Validierung
use App\Security\IbanValidator;
$ibanValidator = new IbanValidator();

if (!$ibanValidator->isValid($iban)) {
    $errors['iban'] = 'Die eingegebene IBAN ist ungültig';
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
$mail->Host        = $_ENV['MAIL_HOST'];
$mail->Port        = (int) $_ENV['MAIL_PORT'];
$mail->SMTPSecure  = $_ENV['MAIL_SECURE'] ?? '';
$mail->SMTPAutoTLS = !empty($_ENV['MAIL_SECURE'] ?? '');
$mail->SMTPAuth    = !empty($_ENV['MAIL_USER'] ?? '');
if ($mail->SMTPAuth) {
    $mail->Username = $_ENV['MAIL_USER'];
    $mail->Password = $_ENV['MAIL_PASS'];
}

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
