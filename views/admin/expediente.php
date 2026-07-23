<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header d-flex align-items-center gap-3 mb-4">
                <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitantes" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="page-title">Expediente del Estudiante</h2>
                    <p class="text-muted mb-0"><?= e($alumno['nombre'] . ' ' . $alumno['apellidos']) ?></p>
                </div>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <div class="row g-4">
                <!-- Datos personales -->
                <div class="col-md-6">
                    <div class="card card-shadow h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-person me-2"></i>Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">Nombre</dt>
                                <dd class="col-sm-8"><?= e($alumno['nombre'] . ' ' . $alumno['apellidos']) ?></dd>
                                <dt class="col-sm-4 text-muted">Correo</dt>
                                <dd class="col-sm-8"><?= e($alumno['email']) ?></dd>
                                <dt class="col-sm-4 text-muted">CURP</dt>
                                <dd class="col-sm-8"><?= e($alumno['curp'] ?? '—') ?></dd>
                                <dt class="col-sm-4 text-muted">Teléfono</dt>
                                <dd class="col-sm-8"><?= e($alumno['telefono'] ?? '—') ?></dd>
                                <dt class="col-sm-4 text-muted">Género</dt>
                                <dd class="col-sm-8"><?= e($alumno['genero'] ?? '—') ?></dd>
                                <dt class="col-sm-4 text-muted">Registro</dt>
                                <dd class="col-sm-8"><?= formatDate($alumno['created_at']) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Datos académicos -->
                <div class="col-md-6">
                    <div class="card card-shadow h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-book me-2"></i>Información Académica</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($infoAcademica): ?>
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Nivel</dt>
                                <dd class="col-sm-7"><?= e($infoAcademica['nivel_nombre']) ?></dd>
                                <dt class="col-sm-5 text-muted">Institución</dt>
                                <dd class="col-sm-7"><?= e($infoAcademica['institución'] ?? $infoAcademica['institucion']) ?></dd>
                                <dt class="col-sm-5 text-muted">Carrera</dt>
                                <dd class="col-sm-7"><?= e($infoAcademica['carrera_o_programa'] ?? '—') ?></dd>
                                <dt class="col-sm-5 text-muted">Semestre</dt>
                                <dd class="col-sm-7"><?= e($infoAcademica['semestre_o_grado'] ?? '—') ?></dd>
                                <dt class="col-sm-5 text-muted">Promedio</dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-success fs-6"><?= number_format($infoAcademica['promedio'], 1) ?></span>
                                </dd>
                                <dt class="col-sm-5 text-muted">Matrícula</dt>
                                <dd class="col-sm-7"><?= e($infoAcademica['matricula'] ?? '—') ?></dd>
                            </dl>
                            <?php else: ?>
                            <p class="text-muted mb-0">No se ha registrado información académica.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="col-12">
                    <div class="card card-shadow">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-files me-2"></i>Documentos (<?= count($documentos) ?>)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Documento</th>
                                            <th>Solicitud / Beca</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                            <?= e($doc['tipo_documento']) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark"><?= e($doc['folio']) ?></span>
                                            <small class="d-block text-muted"><?= e($doc['beca_nombre']) ?></small>
                                        </td>
                                        <td><?= badgeDocumento($doc['estado']) ?></td>
                                        <td class="small text-muted"><?= formatDate($doc['created_at']) ?></td>
                                        <td class="small text-muted"><?= e($doc['observaciones'] ?? '—') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($documentos)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Sin documentos.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Solicitudes -->
                <div class="col-12">
                    <div class="card card-shadow">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-file-text me-2"></i>Solicitudes de Beca (<?= count($solicitudes) ?>)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr><th>Folio</th><th>Beca</th><th>Estado</th><th>Fecha Envío</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($solicitudes as $sol): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($sol['folio']) ?></td>
                                        <td><?= e($sol['beca_nombre']) ?></td>
                                        <td><?= badgeEstado($sol['estado']) ?></td>
                                        <td class="small text-muted">
                                            <?= $sol['fecha_envio'] ? formatDate($sol['fecha_envio']) : '—' ?>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudDetalle&id=<?= $sol['id'] ?>"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($solicitudes)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Sin solicitudes.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
