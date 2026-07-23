<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sistema de Becas') ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Fuente Lexend -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS propio -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <?= $extraHead ?? '' ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
