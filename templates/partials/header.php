<?php
declare(strict_types=1);

// Information Leakage vermeiden
header_remove('X-Powered-By');

// Nur setzen, wenn noch nichts gesendet wurde
if (!headers_sent()) {

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');

    header(
        "Content-Security-Policy: ".
        "default-src 'self'; ".
        "img-src 'self' data:; ".
        "style-src 'self'; ".
        "script-src 'self';"
    );
}

// @var string $pageH1
// @var string $navBase  '' oder 'index.php' -->
$navBase = $navBase ?? '';
$pageH1 = $pageH1 ?? 'Mein One-Pager';
?>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-inner">
        <span>One-Pager Template</span>
    </div>
</div>

<header class="header">
    <div class="header-inner">
        <a href="<?= $navBase ?>#hero" class="logo" aria-label="Startseite">
            <img class="logo-img" alt="Mein One-Pager" />
        </a>

        <h1><?= htmlspecialchars($pageH1, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop" id="desktopMenu">
            <a href="<?= $navBase ?>#hero">Hero</a>
            <a href="<?= $navBase ?>#features">Features</a>
            <a href="<?= $navBase ?>#about">Über uns</a>
            <a href="<?= $navBase ?>#contact">Kontakt aufnehmen</a>
        </nav>

        <!-- Mobile Button -->
        <button id="menuToggle" class="menu-toggle" aria-label="Menü öffnen" aria-expanded="false">
            ☰
        </button>
    </div>

    <!-- Mobile Menü -->
    <nav id="mobileMenu" class="nav-mobile" aria-hidden="true">
        <a href="<?= $navBase ?>#hero" tabindex="-1">Hero</a>
        <a href="<?= $navBase ?>#features" tabindex="-1">Features</a>
        <a href="<?= $navBase ?>#about" tabindex="-1">Über uns</a>
        <a href="<?= $navBase ?>#contact" tabindex="-1">Kontakt</a>
    </nav>
</header>