<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* -------------------------------------------------
| JSON-Response Helper (einheitlich)
-------------------------------------------------- */
function json_response(
    int $httpCode,
    bool $ok,
    string $code,
    string $message,
    array $extra = []
): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
    }

    http_response_code($httpCode);

    echo json_encode(array_merge([
        'ok'      => $ok,
        'code'    => $code,
        'message' => $message,
    ], $extra), JSON_UNESCAPED_UNICODE);

    exit;
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
        json_response(500, false, 'ENV', 'Konfiguration unvollständig.');
    }
}

/*
|--------------------------------------------------------------------------
| 1. Nur POST erlauben
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(405, false, 'METHOD', 'Methode nicht erlaubt.');
}

/*
|--------------------------------------------------------------------------
| 2. CSRF prüfen (zwingend!)
|--------------------------------------------------------------------------
*/
if (!csrf_verify($_POST['_csrf'] ?? null)) {
    json_response(403, false, 'CSRF', 'Ungültige Anfrage.');
}

/*
|--------------------------------------------------------------------------
| 3. Website prüfen (still)
|--------------------------------------------------------------------------
*/
if (!empty($_POST['website'] ?? '')) {
    json_response(200, true, 'OK', 'Danke!');
}

/*
|--------------------------------------------------------------------------
| 4. Eingaben lesen
|--------------------------------------------------------------------------
*/
$vorname   = trim($_POST['vorname'] ?? '');
$nachname  = trim($_POST['nachname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$nachricht = trim($_POST['nachricht'] ?? '');
$consent   = isset($_POST['consent']);

/*
|--------------------------------------------------------------------------
| 5. Validierung
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
    json_response(400, false, 'VALIDATION', 'Bitte Eingaben prüfen.');
}

/*
|--------------------------------------------------------------------------
| 6. Verarbeitung: Mail mit PHPMailer
|--------------------------------------------------------------------------
*/
try {
    // PHPMailer-Instanz
    $mail = new PHPMailer(true);

    // SMTP (empfohlen)
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USER'];
    $mail->Password = $_ENV['MAIL_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int) $_ENV['MAIL_PORT'];

    // Absender & Empfänger
    $mail->setFrom($_ENV['MAIL_FROM'], 'Website');
    $mail->addAddress($_ENV['MAIL_TO']);

    // Reply-To vom Absender (wichtig!)
    $mail->addReplyTo($email, $vorname . ' ' . $nachname);

    // Inhalt
    $mail->isHTML(false);
    $mail->Subject = 'Kontaktformular';
    $mail->Body =
        "Vorname: $vorname\n" .
        "Nachname: $nachname\n" .
        "E-Mail: $email\n\n" .
        $nachricht;

    $mail->send();

} catch (Exception $e) {
    // Kein Leak!
    // error_log($e->getMessage()); // optional
    json_response(500, false, 'MAIL', 'Leider ist ein Fehler aufgetreten. Bitte später erneut versuchen.');
}

/*
|--------------------------------------------------------------------------
| 7. CSRF regenerieren & Erfolg
|--------------------------------------------------------------------------
*/
csrf_regenerate();
$newToken = csrf_token();

json_response(200, true, 'SENT', 'Danke! Nachricht wurde gesendet.', ['csrf' => $newToken]);
