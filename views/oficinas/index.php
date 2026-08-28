<?php require dirname(__DIR__) . '/partials/header.php'; ?>
<div class="toolbar"><h1>Oficinas</h1><a class="button" href="/oficinas/nueva">Nueva</a></div>
<div class="card"><table><thead><tr><th>ID</th><th>Nombre</th><th>Codigo</th></tr></thead><tbody>
<?php foreach ($items as $item): $fields = $item['fields'] ?? []; ?>
<tr>
  <td><?= htmlspecialchars((string) ($item['id'] ?? '')) ?></td>
  <td><?= htmlspecialchars((string) ($fields['Title'] ?? '')) ?></td>
  <td><?= htmlspecialchars((string) ($fields['Codigo'] ?? '')) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
