<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1 d-flex align-items-center py-5">
    <div class="container" style="max-width:600px">
        <div class="card card-shadow text-center">
            <div class="card-body py-5">
                <div class="confirmation-icon mx-auto mb-4">
                    <i class="bi bi-check-circle-fill text-success"></i>
                </div>
                <h2 class="fw-bold mb-2">¡Solicitud Enviada!</h2>
                <p class="text-muted mb-4">
                    Tu solicitud ha sido recibida exitosamente.
                    Conserva tu número de folio para dar seguimiento.
                </p>

                <div class="folio-box p-4 mb-4">
                    <small class="text-muted d-block mb-1">Tu número de folio es:</small>
                    <h3 class="fw-bold text-primary mb-0"><?= e($solicitud['folio']) ?></h3>
                </div>

                <div class="text-start mb-4">
                    <h6 class="fw-semibold mb-3">Próximos pasos:</h6>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-1-circle-fill text-primary mt-1"></i>
                            <span>El equipo de becas revisará tu expediente en los próximos <strong>5 días hábiles</strong>.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-2-circle-fill text-primary mt-1"></i>
                            <span>Recibirás una notificación si se requiere información adicional.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-3-circle-fill text-primary mt-1"></i>
                            <span>Puedes consultar el estatus en cualquier momento con tu folio.</span>
                        </li>
                    </ul>
                </div>

                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=tramiteDetalle&id=<?= $solicitud['id'] ?>"
                       class="btn btn-student">
                        <i class="bi bi-eye me-2"></i>Ver mi Solicitud
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=dashboard"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-house me-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
