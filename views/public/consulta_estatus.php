<?php
$bodyClass = 'd-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<nav class="navbar navbar-light bg-white border-bottom px-4">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php?c=auth&a=login">
        <i class="bi bi-mortarboard-fill me-2 text-primary"></i>Sistema de Becas
    </a>
    <a href="<?= BASE_URL ?>/index.php?c=auth&a=login" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-person me-1"></i>Iniciar Sesión
    </a>
</nav>

<main class="flex-grow-1 bg-light">
    <div class="container py-5" style="max-width:700px">
        <div class="text-center mb-5">
            <div class="auth-logo-circle mx-auto mb-3">
                <i class="bi bi-search"></i>
            </div>
            <h2 class="fw-bold">Consulta de Estatus de Beca</h2>
            <p class="text-muted">Ingresa tu número de folio para verificar el estado de tu solicitud sin necesidad de iniciar sesión.</p>
        </div>

        <!-- Formulario -->
        <div class="card card-shadow mb-4">
            <div class="card-body p-4">
                <form method="GET" action="<?= BASE_URL ?>/index.php">
                    <input type="hidden" name="c" value="public">
                    <input type="hidden" name="a" value="consultaEstatus">
                    <label class="form-label fw-semibold">Número de Folio</label>
                    <div class="input-group input-group-lg">
                        <input type="text" name="folio" class="form-control"
                               placeholder="Ej. B-2024-0045"
                               value="<?= e($folio) ?>" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Consultar
                        </button>
                    </div>
                    <div class="form-text mt-2">
                        El folio fue asignado cuando enviaste tu solicitud.
                    </div>
                </form>
            </div>
        </div>

        <!-- Error -->
        <?php if (!empty($error)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>

        <!-- Resultado -->
        <?php if ($solicitud): ?>
        <div class="card card-shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Folio: <strong class="text-primary"><?= e($solicitud['folio']) ?></strong></h5>
                <?= badgeEstado($solicitud['estado']) ?>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <small class="text-muted">Beca Solicitada</small>
                        <p class="fw-semibold mb-0"><?= e($solicitud['beca_nombre']) ?></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Monto</small>
                        <p class="fw-semibold mb-0"><?= formatMoney($solicitud['monto']) ?>/<?= e($solicitud['tipo_monto']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($historial): ?>
        <div class="card card-shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial</h5>
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

        <div class="text-center mt-4">
            <p class="text-muted small">
                ¿Eres alumno? <a href="<?= BASE_URL ?>/index.php?c=auth&a=login">Inicia sesión</a> para acceder a más funciones.
            </p>
        </div>
    </div>
</main>

<footer class="bg-white border-top py-3 text-center">
    <small class="text-muted">&copy; <?= date('Y') ?> Sistema de Becas Escolares. Todos los derechos reservados.</small>
</footer>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
