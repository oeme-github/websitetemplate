<?php
declare(strict_types=1);

// Security headers are set in public/index.php

// @var string $pageH1
$pageH1 = $pageH1 ?? 'Mein One-Pager';
?>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-inner">
        <span>One-Pager Template</span>
        <?php $topbarLinks = $gallery('home/topbar-links'); if ($topbarLinks): ?>
        <nav class="topbar-links" aria-label="Externe Links">
            <?php foreach ($topbarLinks as $link):
                $url   = htmlspecialchars($link['url']   ?? '', ENT_QUOTES, 'UTF-8');
                $label = htmlspecialchars($link['label'] ?? '', ENT_QUOTES, 'UTF-8');
                $aria  = htmlspecialchars($link['aria']  ?? $label, ENT_QUOTES, 'UTF-8');
                $svg   = $link['svg'] ?? '';
            ?>
            <a href="<?= $url ?>" class="topbar-link" target="_blank" rel="noopener noreferrer" aria-label="<?= $aria ?>">
                <?= $svg ?>
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>
    </div>
</div>

<header class="header">
    <div class="header-inner">
        <a href="/#top" class="logo" aria-label="Startseite">
            <img class="logo-img" src="/assets/logo/logo_mark.svg" alt="Mein One-Pager" width="40" height="40" />
        </a>

        <h1><?= htmlspecialchars($pageH1, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop" id="desktopMenu">
            <a href="/#hero">Start</a>
            <a href="/#features">Galerie</a>
            <a href="/#stats">Zahlen</a>
            <a href="/#about">Über uns</a>
            <a href="/#contact">Kontakt</a>
        </nav>

        <!-- Mobile Button -->
        <button id="menuToggle" class="menu-toggle" data-nav-toggle aria-label="Menü öffnen" aria-expanded="false">
            ☰
        </button>
    </div>

    <!-- Mobile Menü -->
    <nav id="mobileMenu" class="nav-mobile" data-nav>
        <a href="/#hero">Start</a>
        <a href="/#features">Galerie</a>
        <a href="/#stats">Zahlen</a>
        <a href="/#about">Über uns</a>
        <a href="/#contact">Kontakt</a>
    </nav>
</header>
