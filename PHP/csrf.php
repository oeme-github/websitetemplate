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
//error_log('CSRF SESSION ID: ' . session_id());
// =======================
// Basis-Header
// =======================
header('Content-Type: application/json; charset=utf-8');

$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

header('Content-Type: application/json');
echo json_encode(['csrf_token' => $token]);
