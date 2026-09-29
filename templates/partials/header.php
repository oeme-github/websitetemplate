<?php
declare(strict_types=1);

// Security headers are set in public/index.php

// @var string $pageH1
// @var array  $site   (set in public/index.php)
$pageH1 = $pageH1 ?? $site['name'];

// Section key => anchor id
$navItems = [
    'hero'    => 'hero',
    'gallery' => 'features',
    'stats'   => 'stats',
    'about'   => 'about',
    'contact' => 'contact',
];
?>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-inner">
        <?php if (($site['tagline'] ?? '') !== ''): ?><span><?= e($site['tagline']) ?></span><?php endif; ?>
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
            <img class="logo-img" src="/assets/logo/logo_mark.svg" alt="<?= e($site['logoAlt']) ?>" width="40" height="40" />
        </a>

        <h1><?= htmlspecialchars($pageH1, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop" id="desktopMenu">
            <?php foreach ($navItems as $key => $anchor): if ($section($key)): ?><a href="/#<?= $anchor ?>"><?= e($site['nav'][$key] ?? '') ?></a><?php endif; endforeach; ?>
        </nav>

        <!-- Mobile Button -->
        <button id="menuToggle" class="menu-toggle" data-nav-toggle aria-label="Menü öffnen" aria-expanded="false">
            ☰
        </button>
    </div>

    <!-- Mobile Menü -->
    <nav id="mobileMenu" class="nav-mobile" data-nav>
        <?php foreach ($navItems as $key => $anchor): if ($section($key)): ?><a href="/#<?= $anchor ?>"><?= e($site['nav'][$key] ?? '') ?></a><?php endif; endforeach; ?>
    </nav>
</header>
