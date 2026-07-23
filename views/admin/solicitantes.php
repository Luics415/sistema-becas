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
                    <h2 class="page-title">Solicitantes</h2>
                    <p class="text-muted mb-0"><?= $paginacion['total'] ?> alumnos registrados</p>
                </div>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <!-- Buscador -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-3 align-items-end">
                        <input type="hidden" name="c" value="admin">
                        <input type="hidden" name="a" value="solicitantes">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                       placeholder="Nombre, apellidos o correo…"
                                       value="<?= e($busqueda) ?>">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Buscar</button>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitantes"
                               class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card card-shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Alumno</th>
                                    <th>Institución</th>
                                    <th>Promedio</th>
                                    <th>Solicitudes</th>
                                    <th>Registro</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($alumnos as $al): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar-sm">
                                            <?= strtoupper(substr($al['nombre'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= e($al['nombre'] . ' ' . $al['apellidos']) ?></div>
                                            <small class="text-muted"><?= e($al['email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-truncate" style="max-width:160px">
                                    <?= e($al['institucion'] ?? '—') ?>
                                </td>
                                <td>
                                    <?php if ($al['promedio']): ?>
                                    <span class="badge bg-<?= $al['promedio'] >= 9 ? 'success' : ($al['promedio'] >= 8 ? 'primary' : 'warning') ?> bg-opacity-80">
                                        <?= number_format($al['promedio'], 1) ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark"><?= $al['total_solicitudes'] ?></span>
                                </td>
                                <td class="small text-muted"><?= formatDate($al['created_at']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $al['activo'] ? 'success' : 'secondary' ?>">
                                        <?= $al['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=expediente&id=<?= $al['id'] ?>"
                                           class="btn btn-outline-primary" title="Ver expediente">
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-<?= $al['activo'] ? 'danger' : 'success' ?>"
                                                onclick="toggleUsuario(<?= $al['id'] ?>)"
                                                title="<?= $al['activo'] ? 'Desactivar' : 'Activar' ?>">
                                            <i class="bi bi-<?= $al['activo'] ? 'person-slash' : 'person-check' ?>"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($alumnos)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                No se encontraron alumnos.
                            </td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Paginación -->
                <?php if ($paginacion['total_paginas'] > 1): ?>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">Página <?= $paginacion['pagina'] ?> de <?= $paginacion['total_paginas'] ?></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++):
                                $p = http_build_query(['c'=>'admin','a'=>'solicitantes','q'=>$busqueda,'pagina'=>$i]);
                            ?>
                            <li class="page-item <?= $i === $paginacion['pagina'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/index.php?<?= $p ?>"><?= $i ?></a>
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
function toggleUsuario(id) {
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=toggleUsuario', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: 'id=' + id
    })
    .then(r => r.json())
    .then(d => { if(d.success) location.reload(); else alert(d.message); });
}
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
