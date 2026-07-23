<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <!-- Hero Banner -->
    <div class="student-hero py-4 px-4 mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1 text-white">
                        ¡Hola, <?= e(explode(' ', Session::get('user_name'))[0]) ?>!
                    </h2>
                    <p class="text-white-75 mb-3">
                        <?php $activas = count(array_filter($solicitudes, fn($s) => in_array($s['estado'], ['en_revision','enviada']))); ?>
                        Tienes <strong><?= $activas ?></strong> solicitud<?= $activas !== 1 ? 'es' : '' ?> en proceso.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                           class="btn btn-white text-primary fw-semibold">
                            <i class="bi bi-megaphone-fill me-2"></i>Ver Convocatorias
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=tramites"
                           class="btn btn-outline-light">
                            <i class="bi bi-file-text me-2"></i>Mis Trámites
                        </a>
                    </div>
                </div>
                <div class="col-md-4 d-none d-md-flex justify-content-end">
                    <div class="hero-stat-card">
                        <div class="hero-stat-item">
                            <span class="hero-stat-value"><?= e($infoAcademica['promedio'] ?? '—') ?></span>
                            <span class="hero-stat-label">Promedio</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-value">
                                <?= count(array_filter($solicitudes, fn($s) => $s['estado'] === 'aprobada')) ?>
                            </span>
                            <span class="hero-stat-label">Becas Activas</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-value"><?= $mensajesNL ?? 0 ?></span>
                            <span class="hero-stat-label">Mensajes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

        <div class="row g-4">
            <!-- Mis Solicitudes -->
            <div class="col-lg-8">
                <div class="card card-shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-text me-2 text-student"></i>Mis Solicitudes Actuales
                        </h5>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=tramites"
                           class="btn btn-sm btn-outline-primary">Ver todas</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($solicitudes): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Beca</th>
                                        <th>Folio</th>
                                        <th>Estado</th>
                                        <th>Avance</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach (array_slice($solicitudes, 0, 4) as $sol): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold small"><?= e($sol['beca_nombre']) ?></div>
                                        <span class="badge bg-<?= e($sol['color_badge']) ?> small"><?= e($sol['categoria']) ?></span>
                                    </td>
                                    <td class="fw-semibold text-primary small"><?= e($sol['folio']) ?></td>
                                    <td><?= badgeEstado($sol['estado']) ?></td>
                                    <td>
                                        <?php
                                        $pct = min(100, ($sol['paso_actual'] - 1) * 33);
                                        if ($sol['estado'] === 'aprobada') $pct = 100;
                                        elseif ($sol['estado'] === 'en_revision') $pct = 75;
                                        elseif ($sol['estado'] === 'enviada') $pct = 60;
                                        ?>
                                        <div class="progress" style="height:6px;width:80px">
                                            <div class="progress-bar bg-student" style="width:<?= $pct ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($sol['estado'] === 'borrador'): ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso<?= $sol['paso_actual'] ?>&solicitud_id=<?= $sol['id'] ?>"
                                           class="btn btn-sm btn-student">Continuar</a>
                                        <?php else: ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=student&a=tramiteDetalle&id=<?= $sol['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary">Ver</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-plus text-muted fs-1 d-block mb-3"></i>
                            <p class="text-muted">Aún no tienes solicitudes.</p>
                            <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                               class="btn btn-student">
                                <i class="bi bi-plus-circle me-2"></i>Aplicar a una Beca
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Convocatorias disponibles -->
                <?php if ($becasDisponibles): ?>
                <div class="card card-shadow">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-megaphone-fill me-2 text-student"></i>Convocatorias Disponibles
                        </h5>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                           class="btn btn-sm btn-outline-primary">Ver todas</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                        <?php foreach ($becasDisponibles as $b): ?>
                        <div class="col-md-6">
                            <div class="beca-card-small border rounded p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-<?= e($b['color_badge']) ?>"><?= e($b['categoria_nombre']) ?></span>
                                    <small class="text-muted">Cierra: <?= formatDate($b['fecha_fin']) ?></small>
                                </div>
                                <h6 class="fw-semibold mb-1"><?= e($b['nombre']) ?></h6>
                                <p class="text-muted small mb-2 text-truncate"><?= e($b['descripcion'] ?? '') ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-student"><?= formatMoney($b['monto']) ?>/<?= e($b['tipo_monto']) ?></strong>
                                    <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1&beca_id=<?= $b['id'] ?>"
                                       class="btn btn-sm btn-student">Aplicar</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Resumen de cuenta -->
                <div class="card card-shadow mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Mi Cuenta</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="user-avatar-lg mx-auto mb-2">
                                <?= strtoupper(substr(Session::get('user_name', 'A'), 0, 1)) ?>
                            </div>
                            <h6 class="mb-0"><?= e(Session::get('user_name')) ?></h6>
                            <small class="text-muted"><?= e($alumno['email'] ?? '') ?></small>
                        </div>
                        <div class="d-flex justify-content-around border-top pt-3">
                            <div class="text-center">
                                <div class="fw-bold text-student"><?= count($solicitudes) ?></div>
                                <small class="text-muted">Trámites</small>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold text-success">
                                    <?= count(array_filter($solicitudes, fn($s) => $s['estado'] === 'aprobada')) ?>
                                </div>
                                <small class="text-muted">Aprobadas</small>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold text-primary">
                                    <?= $infoAcademica['promedio'] ?? '—' ?>
                                </div>
                                <small class="text-muted">Promedio</small>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <a href="<?= BASE_URL ?>/index.php?c=student&a=perfil"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Editar Perfil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Acceso rápido -->
                <div class="card card-shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-lightning me-2"></i>Acciones Rápidas</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=documentos"
                           class="list-group-item list-group-item-action py-3">
                            <i class="bi bi-folder-fill text-warning me-2"></i>Subir Documentos
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=consultaEstatus"
                           class="list-group-item list-group-item-action py-3">
                            <i class="bi bi-search text-info me-2"></i>Consultar Estatus
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                           class="list-group-item list-group-item-action py-3">
                            <i class="bi bi-award text-student me-2"></i>Ver Convocatorias
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
