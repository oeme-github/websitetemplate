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
guardRateLimit('kontakt');
requireEnvKeys(['MAIL_HOST', 'MAIL_PORT', 'MAIL_FROM', 'MAIL_TO']);

/*
|--------------------------------------------------------------------------
| Input lesen
|--------------------------------------------------------------------------
*/
use App\Helpers\Helpers;

$vorname   = Helpers::clean($_POST['vorname']   ?? '');
$nachname  = Helpers::clean($_POST['nachname']  ?? '');
$email     = Helpers::clean($_POST['email']     ?? '');
$nachricht = Helpers::clean($_POST['nachricht'] ?? '');
$consent   = isset($_POST['consent']);

/*
|--------------------------------------------------------------------------
| Validierung
|--------------------------------------------------------------------------
*/
$errors = [];

if ($vorname === '') {
    $errors['vorname'] = 'Vorname fehlt';
}

if ($nachname === '') {
    $errors['nachname'] = 'Nachname fehlt';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'E-Mail ungültig';
}

if (!$consent) {
    $errors['consent'] = 'Einwilligung fehlt';
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

$mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME'] ?? 'Website');
$mail->addAddress($_ENV['MAIL_TO']);
$mail->addReplyTo($email, "$vorname $nachname");

$mail->Subject = 'Kontaktformular';
$mail->Body =
    "Vorname: $vorname\n" .
    "Nachname: $nachname\n" .
    "E-Mail: $email\n\n" .
    $nachricht;

$mail->send();

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
