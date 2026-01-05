<?php
declare(strict_types=1);


// Entwicklungsmodus aktivieren
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Produktionsmodus deaktivieren
// ini_set('display_errors', '0');
// ini_set('log_errors', '1');
// error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

// .env-Datei laden
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// erlaubte Seiten (Whitelist!)
$pages = [
    'home'       => 'home.php',
    'impressum'  => 'impressum.php',
    'datenschutz'=> 'datenschutz.php',
];

// Seite bestimmen
$page = $_GET['page'] ?? 'home';

// Fallback auf Startseite
if (!array_key_exists($page, $pages)) {
    $page = 'home';
}

// Seitentitel
$titles = [
    'home'        => 'Startseite',
    'impressum'   => 'Impressum',
    'datenschutz' => 'Datenschutz',
];

$title = $titles[$page];

// Template (absoluter Pfad!)
$template = dirname(__DIR__) . '/templates/pages/' . $pages[$page];

// Layout rendern
require dirname(__DIR__) . '/templates/layout/base.php';
