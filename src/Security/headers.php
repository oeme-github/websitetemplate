<?php
declare(strict_types=1);

// Nur setzen, wenn noch nichts gesendet wurde
if (!headers_sent()) {

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // CSP bewusst minimal (kein JS-Block!)
    header(
        "Content-Security-Policy: ".
        "default-src 'self'; ".
        "img-src 'self' data:; ".
        "style-src 'self'; ".
        "script-src 'self';"
    );
}
