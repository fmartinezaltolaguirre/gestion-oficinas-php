<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= htmlspecialchars($title ?? 'Gestión de Oficinas', ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="icon" href="/assets/images/logos/logoIneco.jpg">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>

<body>

<header class="app-header">
    <a class="brand" href="/">
        <img alt="Ineco" src="/assets/images/logos/logoIneco.jpg" height="40" />
        <span class="brand-title">Gestión de Oficinas</span>
    </a>

    <nav class="main-navigation" aria-label="Navegación principal">
        <a href="/">Inicio</a>
        <a href="/oficinas">Oficinas</a>
    </nav>
</header>

<main class="app-content">
