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
    <link rel="stylesheet" href="/assets/css/main.css">
</head>

<body>
    <!-- Header einbinden -->
    <?php require __DIR__ . '/../partials/header.php'; ?>
    <!-- Hauptinhalt -->
    <main id="main" tabindex="-1">
        <?php
        $template = ltrim($template, '/');
        // Sicherheitscheck: Nur Templates aus dem pages-Verzeichnis laden [otional]
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
    <!-- Footer einbinden -->
    <?php require __DIR__ . '/../partials/footer.php'; ?>
    <!-- JavaScript-Dateien -->
    <script src="/assets/js/main.js" defer></script>
</body>

</html>