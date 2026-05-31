<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (!empty($metaDescription)): ?>
        <meta name="description" content="<?= e($metaDescription) ?>">
    <?php endif; ?>

    <?php if (!empty($metaRobots)): ?>
        <meta name="robots" content="<?= e($metaRobots) ?>">
    <?php endif; ?>

    <title><?= e($title ?? 'Website') ?></title>

    <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/apple-touch-icon.png">
    <link rel="manifest" href="/assets/icons/site.webmanifest">

    <!-- Preloads für LCP (Bildpfad ggf. anpassen) -->
    <link rel="preload" href="/assets/css/main.css" as="style">
    <link rel="preload" href="/assets/images/hero/hero_background.png" as="image">

    <!-- FOUC Prevention (synchron, kein defer) -->
    <script src="/assets/js/fouc-prevention.js"></script>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>

<body class="layout-root">
    <div class="layout-main">
        <!-- Header einbinden -->
        <div class="site-header">
            <?php require __DIR__ . '/../partials/header.php'; ?>
        </div>
        <!-- Hauptinhalt -->
        <main id="main" tabindex="-1">
            <?php
            $template = ltrim($template, '/');
            // Sicherheitscheck: Nur Templates aus dem pages-Verzeichnis laden [optional]
            //if (!str_starts_with($template, 'pages/')) {
            //    http_response_code(404);
            //    require __DIR__ . '/../pages/404.php';
            //    return;
            //}
            $templatePath = __DIR__ . '/../' . $template;
            if (is_file($templatePath)) {
                require $templatePath;
            } else {
                http_response_code(404);
                require __DIR__ . '/../pages/404.php';
            }
            ?>
        </main>
        <!-- Cookie-Hinweis -->
        <?php require __DIR__ . '/../partials/cookie-notice.php'; ?>
    </div>
    <!-- Footer einbinden -->
    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <!-- JavaScript-Dateien -->
    <script src="/assets/js/main.js" defer></script>
</body>

</html>
