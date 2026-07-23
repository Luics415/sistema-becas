<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container py-4" style="max-width:800px">
        <!-- Stepper -->
        <div class="stepper mb-5">
            <div class="stepper-item completed"><div class="stepper-number"><i class="bi bi-check"></i></div><div class="stepper-label">Info Personal</div></div>
            <div class="stepper-line active"></div>
            <div class="stepper-item completed"><div class="stepper-number"><i class="bi bi-check"></i></div><div class="stepper-label">Info Académica</div></div>
            <div class="stepper-line active"></div>
            <div class="stepper-item active"><div class="stepper-number">3</div><div class="stepper-label">Revisión y Envío</div></div>
        </div>

        <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

        <!-- Resumen info personal -->
        <div class="card card-shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Información Personal</h5>
                <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1&beca_id=<?= $solicitud['beca_id'] ?>"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Nombre Completo</small>
                        <p class="fw-semibold"><?= e($alumno['nombre'] . ' ' . $alumno['apellidos']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">CURP</small>
                        <p class="fw-semibold"><?= e($alumno['curp'] ?? '—') ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Correo</small>
                        <p class="fw-semibold"><?= e($alumno['email'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Teléfono</small>
                        <p class="fw-semibold"><?= e($alumno['telefono'] ?? '—') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen académico -->
        <div class="card card-shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Información Académica</h5>
                <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso2&solicitud_id=<?= $solicitud['id'] ?>"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
            <div class="card-body">
                <?php if ($infoAc): ?>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Institución</small>
                        <p class="fw-semibold"><?= e($infoAc['institucion'] ?? '—') ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Carrera</small>
                        <p class="fw-semibold"><?= e($infoAc['carrera_o_programa'] ?? '—') ?></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Promedio</small>
                        <p class="fw-semibold text-success fs-5"><?= $infoAc['promedio'] ?? '—' ?></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Semestre</small>
                        <p class="fw-semibold"><?= $infoAc['semestre_o_grado'] ?? '—' ?></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Matrícula</small>
                        <p class="fw-semibold"><?= e($infoAc['matricula'] ?? '—') ?></p>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-muted">No se registró información académica.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Documentos adjuntos -->
        <div class="card card-shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i>Documentos Adjuntos (<?= count($documentos) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if ($documentos): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($documentos as $doc): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                            <?= e($doc['tipo_documento']) ?>
                            <small class="text-muted ms-2"><?= e($doc['nombre_archivo']) ?></small>
                        </div>
                        <?= badgeDocumento($doc['estado']) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">No se han adjuntado documentos.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Beca seleccionada -->
        <div class="card card-shadow bg-light mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="fw-bold mb-1"><?= e($solicitud['beca_nombre']) ?></h5>
                        <p class="text-muted mb-0">Folio tentativo: <strong class="text-primary"><?= e($solicitud['folio']) ?></strong></p>
                    </div>
                    <div class="col-auto">
                        <div class="fw-bold text-success fs-5"><?= formatMoney($solicitud['monto']) ?></div>
                        <small class="text-muted">/<?= e($solicitud['tipo_monto']) ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consentimiento y envío -->
        <form method="POST" action="<?= BASE_URL ?>/index.php?c=student&a=registroEnviar">
            <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="consentimiento" required>
                <label class="form-check-label" for="consentimiento">
                    Declaro que la información proporcionada es verídica y autorizo el uso de mis datos para el proceso de beca.
                    Acepto el <a href="#" target="_blank">Aviso de Privacidad</a>.
                </label>
            </div>
            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso2&solicitud_id=<?= $solicitud['id'] ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Paso Anterior
                </a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-send-fill me-2"></i>Enviar Solicitud Final
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
