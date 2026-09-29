<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Router for PHP's built-in web server (local development only)
|--------------------------------------------------------------------------
| php -S does not evaluate public/.htaccess. This script mirrors its rules:
|   php -S <host>:<port> -t public dev/router.php
|
| - whitelisted PHP endpoints are executed directly
| - any other direct .php request and dotfiles (.htaccess) → 403
| - existing files (assets) are served as-is
| - everything else → index.php?page=<path> (unknown pages → 404 in index.php)
*/

$publicDir = dirname(__DIR__) . '/public';
$path      = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

const ALLOWED_PHP = ['/index.php', '/send_kontakt.php', '/send_sepa.php', '/iban_lookup.php'];

if (str_contains($path, '/.') || (str_ends_with(strtolower($path), '.php') && !in_array($path, ALLOWED_PHP, true))) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

if ($path !== '/' && is_file($publicDir . $path)) {
    return false;
}

$page = trim($path, '/');
$_GET['page'] = $page === '' ? 'home' : $page;

require $publicDir . '/index.php';
