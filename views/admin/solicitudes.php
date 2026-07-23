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
                    <h2 class="page-title">Gestión de Solicitudes</h2>
                    <p class="text-muted mb-0"><?= $paginacion['total'] ?> solicitudes en el sistema</p>
                </div>
                <a href="<?= BASE_URL ?>/index.php?c=admin&a=exportarCSV"
                   class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i>Exportar CSV
                </a>
            </div>

            <!-- Filtros -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-3 align-items-end">
                        <input type="hidden" name="c" value="admin">
                        <input type="hidden" name="a" value="solicitudes">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                       placeholder="Folio, alumno o correo…"
                                       value="<?= e($filtros['busqueda'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="estado" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="enviada"     <?= ($filtros['estado'] ?? '') === 'enviada' ? 'selected' : '' ?>>Enviadas</option>
                                <option value="en_revision" <?= ($filtros['estado'] ?? '') === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                                <option value="aprobada"    <?= ($filtros['estado'] ?? '') === 'aprobada' ? 'selected' : '' ?>>Aprobadas</option>
                                <option value="rechazada"   <?= ($filtros['estado'] ?? '') === 'rechazada' ? 'selected' : '' ?>>Rechazadas</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudes"
                               class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Folio</th>
                                    <th>Alumno</th>
                                    <th>Beca</th>
                                    <th>Documentos</th>
                                    <th>Estado</th>
                                    <th>Fecha Envío</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= e($s['folio']) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= e($s['alumno']) ?></div>
                                    <small class="text-muted"><?= e($s['alumno_email']) ?></small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-truncate" style="max-width:160px">
                                        <?= e($s['beca']) ?>
                                    </div>
                                    <span class="badge bg-<?= e($s['color_badge']) ?> small">
                                        <?= e($s['categoria']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $s['docs_ok'] >= $s['total_docs'] && $s['total_docs'] > 0 ? 'success' : 'warning' ?>">
                                        <?= $s['docs_ok'] ?>/<?= $s['total_docs'] ?>
                                    </span>
                                </td>
                                <td><?= badgeEstado($s['estado']) ?></td>
                                <td class="small text-muted">
                                    <?= $s['fecha_envio'] ? formatDate($s['fecha_envio']) : '—' ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudDetalle&id=<?= $s['id'] ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($solicitudes)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay solicitudes.
                            </td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($paginacion['total_paginas'] > 1): ?>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">Página <?= $paginacion['pagina'] ?> de <?= $paginacion['total_paginas'] ?></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $paginacion['total_paginas']; $i++):
                                $p = http_build_query(array_merge(['c'=>'admin','a'=>'solicitudes'], $filtros, ['pagina'=>$i]));
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

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
