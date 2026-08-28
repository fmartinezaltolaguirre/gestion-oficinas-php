<?php $title = 'Error'; require __DIR__ . '/partials/header.php'; ?>
<section class="card"><h1>No se pudo completar la operacion</h1><p><?= htmlspecialchars($message ?? 'Error interno') ?></p></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
