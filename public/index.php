<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Environment (DEV / PROD)
|--------------------------------------------------------------------------
| ToDo: Für später: idealerweise über APP_ENV steuern
*/
ini_set('display_errors', '1');
error_reporting(E_ALL);

/*
// PROD (später aktivieren)
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
*/

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
| Routing-Definition (Whitelist)
|--------------------------------------------------------------------------
*/
$routes = [
    'home' => [
        'template' => 'pages/home.php',
        'title'    => 'Startseite',
        'action'   => '/send_kontakt.php',
        'variant'  => 'home',
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
    $route = $routes[$page];

    $template           = $route['template'];
    $title              = $route['title'];
    $metaDescription    = $route['metaDescription'] ?? null;
    $metaRobots         = $route['metaRobots'] ?? null;
    $action             = $route['action'] ?? null;
    $variant            = $route['variant'] ?? 'default';
}

/*
|--------------------------------------------------------------------------
| Layout rendern
|--------------------------------------------------------------------------
*/
require dirname(__DIR__) . '/templates/layout/base.php';
