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
$vorname   = Helpers::clean($_POST['vorname']);
$nachname  = Helpers::clean($_POST['nachname']);
$email     = Helpers::clean($_POST['email']);
$nachricht = Helpers::clean($_POST['nachricht']);
$consent   = isset($_POST['consent']);

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

if (!$consent) {
    $errors[] = 'Einwilligung fehlt';
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

$mail->Subject = 'Kontaktformular';
$mail->Body =
    "Vorname: $vorname\n" .
    "Nachname: $nachname\n" .
    "E-Mail: $email\n\n" .
    $nachricht;

$mail->send();
// Fehler über Exception-Handling

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
