<?php
declare(strict_types=1);

function setBaseSecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header_remove('X-Powered-By');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function setApiSecurityHeaders(): void
{
    setBaseSecurityHeaders();
    header('Content-Type: application/json; charset=utf-8');
}

function setHtmlSecurityHeaders(): void
{
    setBaseSecurityHeaders();
    header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "img-src 'self' data:; " .
        "style-src 'self'; " .
        "script-src 'self';"
    );
}
