<?php
declare(strict_types=1);

/**
 * Liefert ein CSRF-Token (erstellt bei Bedarf)
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

/**
 * Prüft ein übermitteltes CSRF-Token
 */
function csrf_verify(?string $token): bool
{
    if (
        empty($_SESSION['_csrf_token']) ||
        empty($token)
    ) {
        return false;
    }

    return hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Optional: Token nach erfolgreicher Nutzung erneuern
 */
function csrf_regenerate(): void
{
    unset($_SESSION['_csrf_token']);
}
