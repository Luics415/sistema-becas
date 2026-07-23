<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
// Preparar datos para Chart.js
$labels    = array_column($distribucion, 'categoria');
$dataSetSol= array_column($distribucion, 'total_solicitudes');
$colores   = ['#1978e5','#198754','#ffc107','#0dcaf0','#6f42c1','#dc3545'];
$tendMeses = array_column($tendencia, 'mes');
$tendTotales = array_column($tendencia, 'total');
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Reportes y Estadísticas</h2>
                    <p class="text-muted mb-0">Análisis integral del sistema de becas</p>
                </div>
                <a href="<?= BASE_URL ?>/index.php?c=admin&a=exportarCSV"
                   class="btn btn-success">
                    <i class="bi bi-download me-2"></i>Exportar CSV
                </a>
            </div>

            <!-- KPIs -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-primary">
                        <div class="kpi-icon"><i class="bi bi-cash-coin"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= '$' . number_format($estadisticas['presupuesto_total'] ?? 0, 0, '.', ',') ?></div>
                            <div class="kpi-label">Presupuesto Total MXN</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-success">
                        <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= $estadisticas['beneficiarios'] ?? 0 ?></div>
                            <div class="kpi-label">Total Beneficiarios</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-info">
                        <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <div class="kpi-body">
                            <?php $tasa = $estadisticas['solicitudes_total'] > 0
                                ? round($estadisticas['solicitudes_aprobadas'] / $estadisticas['solicitudes_total'] * 100, 1) : 0; ?>
                            <div class="kpi-value"><?= $tasa ?>%</div>
                            <div class="kpi-label">Tasa de Aprobación</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card kpi-warning">
                        <div class="kpi-icon"><i class="bi bi-award-fill"></i></div>
                        <div class="kpi-body">
                            <div class="kpi-value"><?= $estadisticas['publicadas'] ?? 0 ?></div>
                            <div class="kpi-label">Becas Activas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    <div class="card card-shadow h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Distribución por Categoría</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="chartDistribucion" style="max-height:280px"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card card-shadow h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tendencia de Solicitudes (Últimos 6 meses)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartTendencia" style="max-height:280px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de becas con estadísticas -->
            <div class="card card-shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas por Beca</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Beca</th>
                                    <th>Categoría</th>
                                    <th>Monto</th>
                                    <th>Total Sol.</th>
                                    <th>Aprobadas</th>
                                    <th>En Revisión</th>
                                    <th>Rechazadas</th>
                                    <th>Tasa Aprob.</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($estadsBecas as $eb): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e($eb['nombre']) ?></div>
                                    <?php
                                    $ebcol = ['publicada'=>'success','cerrada'=>'dark','borrador'=>'secondary'];
                                    $ec    = $ebcol[$eb['estado_beca']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $ec ?> small"><?= ucfirst($eb['estado_beca']) ?></span>
                                </td>
                                <td><?= e($eb['categoria']) ?></td>
                                <td><?= formatMoney($eb['monto']) ?></td>
                                <td class="text-center fw-semibold"><?= $eb['total_solicitudes'] ?></td>
                                <td class="text-center text-success fw-semibold"><?= $eb['aprobadas'] ?></td>
                                <td class="text-center text-warning fw-semibold"><?= $eb['en_revision'] ?></td>
                                <td class="text-center text-danger fw-semibold"><?= $eb['rechazadas'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px">
                                            <div class="progress-bar bg-success"
                                                 style="width:<?= $eb['tasa_aprobacion'] ?? 0 ?>%"></div>
                                        </div>
                                        <small><?= $eb['tasa_aprobacion'] ?? 0 ?>%</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
// Gráfica donut categorías
const ctxDist = document.getElementById("chartDistribucion");
new Chart(ctxDist, {
    type: "doughnut",
    data: {
        labels: ' . json_encode($labels) . ',
        datasets: [{
            data: ' . json_encode($dataSetSol) . ',
            backgroundColor: ' . json_encode(array_slice($colores, 0, count($labels))) . ',
            borderWidth: 2
        }]
    },
    options: { responsive: true, plugins: { legend: { position: "bottom" } } }
});

// Gráfica tendencia barras
const ctxTend = document.getElementById("chartTendencia");
new Chart(ctxTend, {
    type: "bar",
    data: {
        labels: ' . json_encode($tendMeses) . ',
        datasets: [{
            label: "Solicitudes",
            data: ' . json_encode($tendTotales) . ',
            backgroundColor: "#1978e540",
            borderColor: "#1978e5",
            borderWidth: 2
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>';
require_once BASE_PATH . '/includes/partials/footer_scripts.php';
?>
