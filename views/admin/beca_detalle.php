<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">

            <!-- Encabezado -->
            <div class="page-header d-flex justify-content-between align-items-start mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item">
                                <a href="<?= BASE_URL ?>/index.php?c=admin&a=becas">Gestión de Becas</a>
                            </li>
                            <li class="breadcrumb-item active"><?= e($beca['nombre']) ?></li>
                        </ol>
                    </nav>
                    <h2 class="page-title mb-0"><?= e($beca['nombre']) ?></h2>
                    <span class="badge bg-<?= e($beca['color_badge']) ?> mt-1"><?= e($beca['categoria_nombre']) ?></span>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($beca['estado'] === 'borrador'): ?>
                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaPublicar&id=<?= $beca['id'] ?>"
                           class="btn btn-success btn-sm"
                           data-confirm="¿Publicar esta beca?">
                            <i class="bi bi-megaphone me-1"></i>Publicar
                        </a>
                    <?php elseif ($beca['estado'] === 'publicada'): ?>
                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaCerrar&id=<?= $beca['id'] ?>"
                           class="btn btn-warning btn-sm"
                           data-confirm="¿Cerrar esta convocatoria?">
                            <i class="bi bi-x-circle me-1"></i>Cerrar
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaEditar&id=<?= $beca['id'] ?>"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?c=admin&a=becas"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <div class="row g-4">

                <!-- Columna principal -->
                <div class="col-lg-8">

                    <!-- Información general -->
                    <div class="card card-shadow mb-4">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            <strong>Información General</strong>
                            <span class="ms-auto badge bg-<?= $beca['estado'] === 'publicada' ? 'success' : ($beca['estado'] === 'cerrada' ? 'secondary' : 'warning text-dark') ?>">
                                <?= ucfirst($beca['estado']) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if ($beca['descripcion']): ?>
                                <p class="text-muted mb-3"><?= nl2br(e($beca['descripcion'])) ?></p>
                            <?php endif; ?>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold text-success"><?= formatMoney($beca['monto']) ?></div>
                                        <small class="text-muted text-capitalize"><?= e($beca['tipo_monto']) ?></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold text-primary">
                                            <?= $beca['cupo_maximo'] ? number_format($beca['cupo_maximo']) : '∞' ?>
                                        </div>
                                        <small class="text-muted">Cupo Máximo</small>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row g-2 small">
                                <div class="col-sm-6">
                                    <span class="text-muted">Nivel educativo:</span>
                                    <strong class="ms-1"><?= e($beca['nivel_nombre']) ?></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Promedio mínimo:</span>
                                    <strong class="ms-1">
                                        <?= $beca['promedio_minimo'] ? number_format($beca['promedio_minimo'], 1) : 'No requerido' ?>
                                    </strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Fecha de inicio:</span>
                                    <strong class="ms-1"><?= formatDate($beca['fecha_inicio']) ?></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Fecha de cierre:</span>
                                    <strong class="ms-1"><?= formatDate($beca['fecha_fin']) ?></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Creado por:</span>
                                    <strong class="ms-1"><?= e($beca['creado_por_nombre']) ?></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Fecha creación:</span>
                                    <strong class="ms-1"><?= formatDate($beca['created_at']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Requisitos -->
                    <?php if ($beca['requisitos']): ?>
                    <div class="card card-shadow mb-4">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-list-check text-primary"></i>
                            <strong>Requisitos</strong>
                        </div>
                        <div class="card-body">
                            <div class="text-muted"><?= nl2br(e($beca['requisitos'])) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Documentos requeridos -->
                    <?php
                    $docsReq = [];
                    if (!empty($beca['documentos_req'])) {
                        $docsReq = is_array($beca['documentos_req'])
                            ? $beca['documentos_req']
                            : (json_decode($beca['documentos_req'], true) ?? []);
                    }
                    ?>
                    <?php if ($docsReq): ?>
                    <div class="card card-shadow mb-4">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-paperclip text-primary"></i>
                            <strong>Documentos Requeridos</strong>
                            <span class="badge bg-secondary ms-auto"><?= count($docsReq) ?></span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($docsReq as $doc): ?>
                            <li class="list-group-item d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark text-muted"></i>
                                <?= e(is_array($doc) ? ($doc['nombre'] ?? $doc[0] ?? '') : $doc) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Solicitudes recibidas -->
                    <div class="card card-shadow">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-people text-primary"></i>
                            <strong>Solicitudes Recibidas</strong>
                            <span class="badge bg-primary ms-auto"><?= count($solicitudes) ?></span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($solicitudes)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Aún no hay solicitudes para esta beca.
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Alumno</th>
                                            <th>Estado</th>
                                            <th>Fecha Envío</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($solicitudes as $s): ?>
                                    <tr>
                                        <td class="fw-bold text-primary small"><?= e($s['folio'] ?? '—') ?></td>
                                        <td><?= e($s['alumno']) ?></td>
                                        <td><?= badgeEstado($s['estado']) ?></td>
                                        <td class="small text-muted">
                                            <?= isset($s['fecha_envio']) && $s['fecha_envio'] ? formatDate($s['fecha_envio']) : '—' ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudDetalle&id=<?= $s['id'] ?>"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Columna lateral -->
                <div class="col-lg-4">

                    <!-- Estadísticas de la beca -->
                    <div class="card card-shadow mb-4">
                        <div class="card-header">
                            <strong><i class="bi bi-bar-chart me-1 text-primary"></i>Estadísticas</strong>
                        </div>
                        <div class="card-body">
                            <?php
                            $total      = count($solicitudes);
                            $aprobadas  = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'aprobada'));
                            $rechazadas = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'rechazada'));
                            $enRevision = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'en_revision'));
                            $enviadas   = count(array_filter($solicitudes, fn($s) => $s['estado'] === 'enviada'));
                            $pct = $total > 0 ? round(($aprobadas / $total) * 100) : 0;
                            ?>

                            <div class="d-flex justify-content-between mb-1 small">
                                <span>Total solicitudes</span>
                                <strong><?= $total ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-success">Aprobadas</span>
                                <strong><?= $aprobadas ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-warning">En revisión</span>
                                <strong><?= $enRevision ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-info">Enviadas</span>
                                <strong><?= $enviadas ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3 small">
                                <span class="text-danger">Rechazadas</span>
                                <strong><?= $rechazadas ?></strong>
                            </div>

                            <?php if ($total > 0): ?>
                            <div class="mb-1 small d-flex justify-content-between">
                                <span>Tasa de aprobación</span>
                                <strong><?= $pct ?>%</strong>
                            </div>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                            </div>
                            <?php endif; ?>

                            <?php if ($beca['cupo_maximo'] && $aprobadas > 0): ?>
                            <hr>
                            <div class="mb-1 small d-flex justify-content-between">
                                <span>Cupo utilizado</span>
                                <strong><?= $aprobadas ?>/<?= $beca['cupo_maximo'] ?></strong>
                            </div>
                            <?php $pctCupo = min(100, round(($aprobadas / $beca['cupo_maximo']) * 100)); ?>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar bg-primary" style="width:<?= $pctCupo ?>%"></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Fechas y vigencia -->
                    <div class="card card-shadow mb-4">
                        <div class="card-header">
                            <strong><i class="bi bi-calendar me-1 text-primary"></i>Vigencia</strong>
                        </div>
                        <div class="card-body">
                            <?php
                            $hoy = new DateTime();
                            $fin = new DateTime($beca['fecha_fin']);
                            $diff = $hoy->diff($fin);
                            $diasRestantes = (int) $diff->format('%r%a');
                            ?>
                            <div class="text-center py-2">
                                <?php if ($beca['estado'] !== 'publicada'): ?>
                                    <div class="text-muted fs-5">
                                        <i class="bi bi-calendar-x d-block fs-2 mb-1"></i>
                                        <?= ucfirst($beca['estado']) ?>
                                    </div>
                                <?php elseif ($diasRestantes < 0): ?>
                                    <div class="text-danger">
                                        <i class="bi bi-exclamation-circle d-block fs-2 mb-1"></i>
                                        Convocatoria vencida
                                    </div>
                                <?php elseif ($diasRestantes <= 7): ?>
                                    <div class="text-warning fw-bold fs-4"><?= $diasRestantes ?></div>
                                    <small class="text-warning">días restantes</small>
                                <?php else: ?>
                                    <div class="text-success fw-bold fs-4"><?= $diasRestantes ?></div>
                                    <small class="text-muted">días restantes</small>
                                <?php endif; ?>
                            </div>
                            <hr>
                            <div class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Inicio:</span>
                                    <strong><?= formatDate($beca['fecha_inicio']) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Cierre:</span>
                                    <strong><?= formatDate($beca['fecha_fin']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="card card-shadow">
                        <div class="card-header">
                            <strong><i class="bi bi-lightning me-1 text-primary"></i>Acciones</strong>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaEditar&id=<?= $beca['id'] ?>"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <i class="bi bi-pencil text-primary"></i> Editar beca
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudes&beca_id=<?= $beca['id'] ?>"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <i class="bi bi-list-ul text-primary"></i> Ver todas las solicitudes
                            </a>
                            <?php if ($beca['estado'] === 'borrador'): ?>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaPublicar&id=<?= $beca['id'] ?>"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2 text-success"
                               data-confirm="¿Publicar esta beca?">
                                <i class="bi bi-megaphone"></i> Publicar convocatoria
                            </a>
                            <?php elseif ($beca['estado'] === 'publicada'): ?>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaCerrar&id=<?= $beca['id'] ?>"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2 text-warning"
                               data-confirm="¿Cerrar esta convocatoria?">
                                <i class="bi bi-x-circle"></i> Cerrar convocatoria
                            </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becaEliminar&id=<?= $beca['id'] ?>"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2 text-danger"
                               data-confirm="¿Eliminar esta beca permanentemente?">
                                <i class="bi bi-trash"></i> Eliminar beca
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
