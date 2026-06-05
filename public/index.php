<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

setHtmlSecurityHeaders();

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
| Content-Loader (Markdown + JSON)
|--------------------------------------------------------------------------
*/
$parsedown   = new \Parsedown();
$contentRoot = dirname(__DIR__) . '/content';

$md = static function (string $name) use ($parsedown, $contentRoot): string {
    $safe = preg_replace('/[^a-z0-9\/\-_]/', '', strtolower($name));
    $path = $contentRoot . '/' . $safe . '.md';
    if (!is_file($path)) {
        $path = $contentRoot . '/' . $safe . '.example.md';
    }
    if (!is_file($path)) {
        return '';
    }
    $html = $parsedown->text(file_get_contents($path));
    return preg_replace_callback('/\{\{([A-Z0-9_]+)\}\}/', static function (array $m): string {
        return $_ENV[$m[1]] ?? '';
    }, $html);
};

$gallery = static function (string $name) use ($contentRoot): array {
    $safe = preg_replace('/[^a-z0-9\/\-_]/', '', strtolower($name));
    $path = $contentRoot . '/' . $safe . '.json';
    if (!is_file($path)) {
        $path = $contentRoot . '/' . $safe . '.example.json';
    }
    if (!is_file($path)) {
        return [];
    }
    $data = json_decode(file_get_contents($path), true);
    if (!is_array($data)) {
        return [];
    }
    // Resolve {{VAR_NAME}} placeholders in string values using $_ENV
    array_walk_recursive($data, static function (mixed &$value): void {
        if (is_string($value)) {
            $value = preg_replace_callback('/\{\{([A-Z0-9_]+)\}\}/', static function (array $m): string {
                return $_ENV[$m[1]] ?? '';
            }, $value);
        }
    });
    return $data;
};

/*
|--------------------------------------------------------------------------
| Section-Flags (SECTION_* aus .env, Default: aktiviert)
|--------------------------------------------------------------------------
*/
$section = static function (string $name): bool {
    $key = 'SECTION_' . strtoupper($name);
    return ($_ENV[$key] ?? 'true') !== 'false';
};

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
