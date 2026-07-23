<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container py-5" style="max-width:720px">
        <div class="page-header text-center mb-5">
            <h2 class="page-title">Consulta de Estatus</h2>
            <p class="text-muted">Ingresa tu número de folio para ver el estado de tu solicitud.</p>
        </div>

        <!-- Buscador por folio -->
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="mb-5">
            <input type="hidden" name="c" value="student">
            <input type="hidden" name="a" value="consultaEstatus">
            <div class="input-group input-group-lg shadow-sm">
                <input type="text" name="folio" class="form-control"
                       placeholder="Ej. B-2024-0045"
                       value="<?= e($folio) ?>" required>
                <button type="submit" class="btn btn-student">
                    <i class="bi bi-search me-2"></i>Consultar
                </button>
            </div>
        </form>

        <?php if ($folio && !$solicitud): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No se encontró ninguna solicitud con el folio <strong><?= e($folio) ?></strong>.
            Verifica el número e intenta de nuevo.
        </div>
        <?php endif; ?>

        <?php if ($solicitud): ?>
        <!-- Resultado -->
        <div class="card card-shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Folio: <strong class="text-primary"><?= e($solicitud['folio']) ?></strong></h5>
                <?= badgeEstado($solicitud['estado']) ?>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">Beca Solicitada</small>
                        <p class="fw-semibold"><?= e($solicitud['beca_nombre']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Categoría</small>
                        <p><?= e($solicitud['categoria']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Monto</small>
                        <p class="fw-semibold"><?= formatMoney($solicitud['monto']) ?>/<?= e($solicitud['tipo_monto']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Fecha de Envío</small>
                        <p><?= $solicitud['fecha_envio'] ? formatDate($solicitud['fecha_envio']) : '—' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline / historial -->
        <?php if ($historial): ?>
        <div class="card card-shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial del Proceso</h5>
            </div>
            <div class="card-body">
                <ul class="timeline-list">
                <?php foreach ($historial as $h): ?>
                <li class="timeline-item">
                    <div class="timeline-badge"></div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between">
                            <strong><?= e(ucfirst(str_replace('_', ' ', $h['estado_nuevo']))) ?></strong>
                            <small class="text-muted"><?= formatDate($h['created_at'], 'd/m/Y H:i') ?></small>
                        </div>
                        <?php if ($h['comentario']): ?>
                        <p class="text-muted small mb-0"><?= e($h['comentario']) ?></p>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
