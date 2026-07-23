<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header mb-4">
                <h2 class="page-title">Dashboard General</h2>
                <p class="text-muted">Bienvenido, <?= e(Session::get('user_name')) ?> &mdash; <?= date('d/m/Y') ?></p>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <!-- KPI Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-primary">
                        <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= number_format($estadisticas['beneficiarios'] ?? 0) ?></div>
                            <div class="kpi-label">Beneficiarios Activos</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-success">
                        <div class="kpi-icon"><i class="bi bi-award-fill"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= number_format($estadisticas['publicadas'] ?? 0) ?></div>
                            <div class="kpi-label">Becas Activas</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-warning">
                        <div class="kpi-icon"><i class="bi bi-hourglass-split"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= number_format($resumenSols['en_revision'] ?? 0) ?></div>
                            <div class="kpi-label">En Revisión</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-info">
                        <div class="kpi-icon"><i class="bi bi-cash-coin"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= '$' . number_format(($estadisticas['presupuesto_total'] ?? 0), 0, '.', ',') ?></div>
                            <div class="kpi-label">Presupuesto Total MXN</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Solicitudes Recientes -->
                <div class="col-xl-8">
                    <div class="card card-shadow h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-file-text me-2 text-primary"></i>Solicitudes Recientes
                            </h5>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudes"
                               class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Alumno</th>
                                            <th>Beca</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($recientes as $s): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudDetalle&id=<?= $s['id'] ?>"
                                               class="fw-semibold text-decoration-none">
                                                <?= e($s['folio']) ?>
                                            </a>
                                        </td>
                                        <td><?= e($s['alumno']) ?></td>
                                        <td class="text-truncate" style="max-width:180px"><?= e($s['beca']) ?></td>
                                        <td><?= badgeEstado($s['estado']) ?></td>
                                        <td class="text-muted small">
                                            <?= $s['fecha_envio'] ? formatDate($s['fecha_envio']) : '—' ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recientes)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">
                                        No hay solicitudes aún.
                                    </td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral -->
                <div class="col-xl-4 d-flex flex-column gap-4">
                    <!-- Próximas a vencer -->
                    <div class="card card-shadow">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar-event me-2 text-warning"></i>Próximas a Vencer
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                            <?php foreach ($proximas as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold small"><?= e($p['nombre']) ?></div>
                                    <small class="text-muted">Cierra: <?= formatDate($p['fecha_fin']) ?></small>
                                </div>
                                <span class="badge <?= $p['dias_restantes'] <= 7 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                    <?= $p['dias_restantes'] ?> días
                                </span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($proximas)): ?>
                            <li class="list-group-item text-muted text-center py-3 small">No hay convocatorias próximas.</li>
                            <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Resumen estados solicitudes -->
                    <div class="card card-shadow">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pie-chart me-2 text-info"></i>Resumen de Solicitudes
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $items = [
                                ['label'=>'Aprobadas',   'key'=>'aprobadas',   'cls'=>'success'],
                                ['label'=>'En Revisión', 'key'=>'en_revision', 'cls'=>'warning'],
                                ['label'=>'Enviadas',    'key'=>'enviadas',    'cls'=>'info'],
                                ['label'=>'Rechazadas',  'key'=>'rechazadas',  'cls'=>'danger'],
                            ];
                            $total = max(1, $resumenSols['total'] ?? 1);
                            foreach ($items as $it):
                                $val = $resumenSols[$it['key']] ?? 0;
                                $pct = round($val / $total * 100);
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-semibold"><?= $it['label'] ?></small>
                                    <small class="text-muted"><?= $val ?> (<?= $pct ?>%)</small>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-<?= $it['cls'] ?>"
                                         style="width:<?= $pct ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /admin-content -->
    </div><!-- /admin-main -->
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
