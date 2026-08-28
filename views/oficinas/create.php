<?php require dirname(__DIR__) . '/partials/header.php'; ?>
<section class="card">
  <h1>Nueva oficina</h1>
  <?php if (!empty($error)): ?>
    <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" action="/oficinas">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
    <label>Nombre<input name="nombre" required maxlength="255" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"></label>
    <label>Codigo<input name="codigo" maxlength="100" value="<?= htmlspecialchars($old['codigo'] ?? '') ?>"></label>
    <button type="submit">Guardar</button>
  </form>
</section>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
