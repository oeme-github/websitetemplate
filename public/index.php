<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$metaDescription = null;
$metaRobots = null;
$action = null;

/*
|--------------------------------------------------------------------------
| .env laden
|--------------------------------------------------------------------------
*/
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

/*
|--------------------------------------------------------------------------
| Environment handling (DEV / PROD)
|--------------------------------------------------------------------------
*/
use App\Helpers\Helpers;

if ( Helpers::is_dev() ) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

/*
|--------------------------------------------------------------------------
| Routing-Definition (Whitelist)
|--------------------------------------------------------------------------
*/
$routes = [
    'home' => [
        'template'  => 'pages/home.php',
        'title'     => 'Startseite',
        'variant'   => 'home',
    ],
    'impressum' => [
        'template'   => 'pages/impressum.php',
        'title'      => 'Impressum',
        'metaRobots' => 'noindex, follow',
        'variant'    => 'default',
    ],
    'datenschutz' => [
        'template'   => 'pages/datenschutz.php',
        'title'      => 'Datenschutz',
        'metaRobots' => 'noindex, follow',
        'variant'    => 'default',
    ],
];

/*
|--------------------------------------------------------------------------
| Formular festlegen
|--------------------------------------------------------------------------
*/
$formType = $_ENV['FORM_TYPE'] ?? 'contact';

$formType = in_array($formType, ['contact', 'sepa'], true)
    ? $formType
    : 'contact';

$formPartial = match ($formType) {
    'sepa'  => 'partials/forms/sepa.php',
    default => 'partials/forms/contact.php',
};

$action = match ($formType) {
    'sepa'  => '/send_sepa.php',
    default => '/send_kontakt.php',
};

/*
|--------------------------------------------------------------------------
| Seite bestimmen
|--------------------------------------------------------------------------
*/
$page = $_GET['page'] ?? 'home';

/*
|--------------------------------------------------------------------------
| Route auflösen
|--------------------------------------------------------------------------
*/
if (!isset($routes[$page])) {
    http_response_code(404);

    $template           = 'pages/404.php';
    $title              = '404 – Seite nicht gefunden';
    $metaRobots         = 'noindex, follow';
} else {
    $route              = $routes[$page];
    $template           = $route['template'];
    $title              = $route['title'];
    $metaDescription    = $route['metaDescription'] ?? null;
    $metaRobots         = $route['metaRobots'] ?? null;
    $variant            = $route['variant'] ?? 'default';
}

/*
|--------------------------------------------------------------------------
| Layout rendern
|--------------------------------------------------------------------------
*/
require dirname(__DIR__) . '/templates/layout/base.php';
