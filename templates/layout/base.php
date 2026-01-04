<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?= e($title ?? 'Website') ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
    <?php require $template; ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>
