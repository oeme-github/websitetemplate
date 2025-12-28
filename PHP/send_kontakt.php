<?php
declare(strict_types=1);

/* =========================
    Init & Security
========================= */
if (!isset($_SERVER)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server context missing']);
    exit;
}

$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_secure', $isHttps ? '1' : '0');

ini_set('display_errors', '1'); /* später wieder auf 0 */
ini_set('display_startup_errors', '1'); /* später wieder auf 0 */
ini_set('log_errors', '1');

error_reporting(E_ALL); /* später wieder auskommentieren */

session_start();

header('Content-Type: application/json; charset=utf-8');

/* =========================
    Request Method
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

/* =========================
    CSRF Check
========================= */
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

/* =========================
    Honeypot (Spam)
========================= */
if (!empty($_POST['website'] ?? '')) {
    http_response_code(200);
    echo json_encode(['spam' => true]);
    exit;
}

/* =========================
    Includes
========================= */
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Helpers.php';

use App\Env;
use App\Helpers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

Env::load(__DIR__ . '/.env');
$mailConfig = require __DIR__ . '/config/mail.php';

$helpers = new Helpers();
$errors  = [];

/* =========================
    Input & Validation
========================= */
$vorname   = $helpers->clean($_POST['vorname'] ?? '');
$nachname  = $helpers->clean($_POST['nachname'] ?? '');
$email     = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$nachricht = $helpers->clean($_POST['nachricht'] ?? '');
$consent   = $_POST['consent'] ?? null;

if ($vorname === '') {
    $errors['vorname'] = 'Vorname ist erforderlich';
}
if ($nachname === '') {
    $errors['nachname'] = 'Nachname ist erforderlich';
}
if (!$email) {
    $errors['email'] = 'Ungültige E-Mail-Adresse';
}
if (empty($consent)) {
    $errors['consent'] = 'Bitte stimmen Sie der Datenschutzerklärung zu.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'errors'  => $errors
    ]);
    exit;
}

/* =========================
    Mail Setup
========================= */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $mailConfig['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailConfig['user'];
    $mail->Password   = $mailConfig['pass'];
    $mail->SMTPSecure = $mailConfig['secure'];
    $mail->Port       = $mailConfig['port'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($mailConfig['from'], $mailConfig['from_name']);
    $mail->addAddress($mailConfig['from']);
    $mail->addReplyTo($email, "$vorname $nachname");

    $mail->isHTML(true);
    $mail->Subject = 'Neue Kontaktanfrage';

    /* HTML */
    $mail->Body = "
        <h2>Kontaktanfrage</h2>
        <p><strong>Name:</strong> {$vorname} {$nachname}</p>
        <p><strong>E-Mail:</strong> {$email}</p>
        <p><strong>Nachricht:</strong><br>" . nl2br($nachricht) . "</p>
    ";

    /* Text */
    $mail->AltBody =
        "Kontaktanfrage\n\n" .
        "Name: {$vorname} {$nachname}\n" .
        "E-Mail: {$email}\n\n" .
        "Nachricht:\n{$nachricht}\n";

    $mail->send();

    echo json_encode(['success' => true]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    error_log('Mail error: ' . $mail->ErrorInfo);

    echo json_encode([
        'success' => false,
        'message' => 'Mailversand fehlgeschlagen. Bitte später erneut versuchen.'
    ]);
    exit;
}
