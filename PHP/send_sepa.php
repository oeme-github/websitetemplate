<?php

// =======================
// typsicherheit
// =======================
declare(strict_types=1);
// =======================
// init section
// =======================
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_secure', '1');
ini_set('display_errors', '0');       // nichts an Browser ausgeben
ini_set('display_startup_errors', 0); 
ini_set('log_errors', '0');           // Fehler ins Error-Log
//ini_set('error_log', __DIR__ . '/logs/php_errors.log');   //eignes Log-File
//error_reporting(E_ALL);
// =======================
// Session
// =======================
session_start();
//error_log('SEND SESSION ID: ' . session_id());

// =======================
// Basis-Header
// =======================
header('Content-Type: application/json; charset=utf-8');

// =======================
// Request Check
// =======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}
// =======================
// CSRF-Prüfung
// =======================
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid CSRF token'
    ]);
    exit;
}
// =======================
// Includes
// =======================
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Helpers.php';
require __DIR__ . '/src/SepaPdf.php';

use App\Env;
use App\Helpers;
use App\SepaPdf;;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

Env::load(__DIR__ . '/.env');

$mailConfig = require __DIR__ . '/config/mail.php';

// =======================
// Serverseitige Validierung
// =======================
$errors = [];

// =======================
// Honypot -> muss leer bleiben
// =======================
if (!empty($_POST['website'] ?? '')) {
    echo json_encode(['spam' => true]);
    exit;
}

// =======================
// Daten
// =======================
$helpers = new Helpers();

$vorname  = $helpers->clean($_POST['Vorname'] ?? '');
$nachname = $helpers->clean($_POST['Nachname'] ?? '');
$email    = filter_var($_POST['E-Mail'] ?? '', FILTER_VALIDATE_EMAIL);
// Fehler behandeln / zurückleiten
if (!$email) {
    $errors['email'] = 'Ungültige E-Mail-Adresse';
}

$adresse  = $helpers->clean($_POST['Adresse'] ?? '');
$ort      = $helpers->clean($_POST['Ort'] ?? '');
$iban     = $helpers->clean($_POST['IBAN'] ?? '');

// check IBAN
if (!$helpers->isValidIban($iban)) {
    $errors['iban'] = 'Die eingegebene IBAN ist ungültig';
}

$bank     = $helpers->clean($_POST['Bank'] ?? '');
$beitrag  = $helpers->clean($_POST['Betrag'] ?? '');

$zahlungsrhythmus = $helpers->clean($_POST['Zahlungsrhythmus'] ?? '');
$mitgliedschaft = $helpers->clean($_POST['Mitgliedschaft'] ?? '');
$nachricht = $helpers->clean($_POST['Nachricht'] ?? '');

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'errors'  => $errors
    ]);
    exit;
}
// =======================
// SEPA PDF
// =======================
$pdf = new SepaPdf();
$pdfPath = $pdf->create([
    'name'    => $vorname . ' ' . $nachname,
    'address' => $adresse . ', ' . $ort,
    'email'   => $email,
    'iban'    => $iban,
    'bank'    => $bank,
    'fee'     => $beitrag,
    'frequ'   => $zahlungsrhythmus,
    'memship' => $mitgliedschaft,
    'mes'     => $nachricht
]);

// =======================
// Mail an Verein
// =======================
$mail = new PHPMailer(true);

// =======================
// DEBUG - NICHT AUF PRODUKTION !!!!
// =======================
$mail->SMTPDebug = 0;   // E-Mail-Debug aus

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
    $mail->Subject = 'Neue Förderung (SEPA)';

    $mail->Body = '
    <p>Förderung</p>
    <p>
    Name: '.$vorname.' '.$nachname.'<br>
    Beitrag: '.$beitrag.'<br>
    IBAN (maskiert): '.$helpers->maskIBAN($iban).'<br><br>
    Antrag Mitglieschaft: '.$mitgliedschaft.'<br>
    Nachricht: '.$nachricht.'
    </p>
    <p>SEPA-Mandat siehe PDF-Anhang.</p>
    ';

    $mail->addAttachment($pdfPath, 'SEPA-Mandat.pdf');
    // Ergbnis von send() merken
    $sent = false;
    try {
        $sent = $mail->send();
    } catch (Exception $e) {
        $sent = false;
    }
} finally {
    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }
}

// =======================
// Erfolg
// =======================
if ($sent) {
    echo json_encode([
        'success' => true
    ]);
} else {
    http_response_code(500);
    error_log($mail->ErrorInfo); // 🔧 LOGGEN, nicht ausgeben

    echo json_encode([
        'success' => false,
        'message' => 'Mailversand fehlgeschlagen, bitte später nochmal versuchen.'
    ]);
}

exit;
