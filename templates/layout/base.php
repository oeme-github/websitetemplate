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

    <?php require __DIR__ . '/../partials/header.php'; ?>

    <main id="main" tabindex="-1">
        <?php
        $template = ltrim($template, '/');
        $templatePath = __DIR__ . '/../' . $template;
        if (is_file($templatePath)) {
            require $templatePath;
        } else {
            http_response_code(404);
            require __DIR__ . '/../pages/404.php';
        }
        ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="/assets/js/main.js" defer></script>
</body>

</html>