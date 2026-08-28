</main>
<footer class="app-footer" role="contentinfo">
  <div class="container">
    <p>
      &copy; <?= date('Y') ?> <?= htmlspecialchars($title ?? 'Gestión de Oficinas', ENT_QUOTES, 'UTF-8') ?> —
      <a href="/oficinas">Abrir oficinas</a> · <a href="/">Inicio</a>
    </p>
    <?php if (filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL)): ?>
      <p class="debug">Entorno: <?= htmlspecialchars($_ENV['APP_ENV'] ?? 'local') ?> — URL: <?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?></p>
    <?php endif; ?>
  </div>
</footer>
</body>
</html>
