<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Administración de Becas</h2>
                    <p class="text-muted mb-0"><?= $paginacion['total'] ?> becas registradas</p>
                </div>
                <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaNueva" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Beca
                </a>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <!-- Filtros -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-3 align-items-end">
                        <input type="hidden" name="c" value="admin">
                        <input type="hidden" name="a" value="becas">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Buscar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                       placeholder="Nombre de la beca…"
                                       value="<?= e($filtros['busqueda'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">Todos</option>
                                <option value="publicada" <?= ($filtros['estado'] ?? '') === 'publicada' ? 'selected' : '' ?>>Publicadas</option>
                                <option value="borrador"  <?= ($filtros['estado'] ?? '') === 'borrador' ? 'selected' : '' ?>>Borradores</option>
                                <option value="cerrada"   <?= ($filtros['estado'] ?? '') === 'cerrada' ? 'selected' : '' ?>>Cerradas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Categoría</label>
                            <select name="categoria_id" class="form-select">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                        <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becas"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Becas -->
            <div class="card card-shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Beca</th>
                                    <th>Categoría</th>
                                    <th>Monto</th>
                                    <th>Cierre</th>
                                    <th>Solicitudes</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($becas as $b): ?>
                            <tr>
                                <td class="text-muted small"><?= $b['id'] ?></td>
                                <td>
                                    <div class="fw-semibold"><?= e($b['nombre']) ?></div>
                                    <small class="text-muted"><?= e($b['nivel_nombre']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= e($b['color_badge']) ?>">
                                        <?= e($b['categoria_nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= formatMoney($b['monto']) ?></span>
                                    <small class="d-block text-muted">/<?= e($b['tipo_monto']) ?></small>
                                </td>
                                <td class="small"><?= formatDate($b['fecha_fin']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">
                                        <?= $b['total_solicitudes'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $estadoBadge = [
                                        'publicada'  => 'success',
                                        'borrador'   => 'secondary',
                                        'cerrada'    => 'dark',
                                        'suspendida' => 'danger',
                                    ];
                                    $col = $estadoBadge[$b['estado']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $col ?>">
                                        <?= ucfirst(e($b['estado'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaDetalle&id=<?= $b['id'] ?>"
                                           class="btn btn-outline-primary" title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaEditar&id=<?= $b['id'] ?>"
                                           class="btn btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($b['estado'] === 'borrador'): ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaPublicar&id=<?= $b['id'] ?>"
                                           class="btn btn-outline-success" title="Publicar"
                                           onclick="return confirm('¿Publicar esta beca?')">
                                            <i class="bi bi-send-fill"></i>
                                        </a>
                                        <?php elseif ($b['estado'] === 'publicada'): ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaCerrar&id=<?= $b['id'] ?>"
                                           class="btn btn-outline-warning" title="Cerrar"
                                           onclick="return confirm('¿Cerrar la convocatoria?')">
                                            <i class="bi bi-lock-fill"></i>
                                        </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-danger"
                                                title="Eliminar"
                                                onclick="eliminarBeca(<?= $b['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($becas)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No se encontraron becas con los filtros actuales.
                            </td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Paginación -->
                <?php if ($paginacion['total_paginas'] > 1): ?>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando <?= count($becas) ?> de <?= $paginacion['total'] ?> becas
                    </small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++):
                                $params = http_build_query(array_merge(['c'=>'admin','a'=>'becas'], $filtros, ['pagina'=>$i]));
                            ?>
                            <li class="page-item <?= $i === $paginacion['pagina'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/index.php?<?= $params ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function eliminarBeca(id) {
    if (!confirm('¿Estás seguro de eliminar esta beca? Esta acción no se puede deshacer.')) return;
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=becaEliminar', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { alert(data.message); }
    });
}
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
