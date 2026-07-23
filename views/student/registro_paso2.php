<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
$docsReq = json_decode($beca['documentos_req'] ?? '[]', true) ?: [];
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container py-4" style="max-width:800px">
        <!-- Stepper -->
        <div class="stepper mb-5">
            <div class="stepper-item completed"><div class="stepper-number"><i class="bi bi-check"></i></div><div class="stepper-label">Info Personal</div></div>
            <div class="stepper-line active"></div>
            <div class="stepper-item active"><div class="stepper-number">2</div><div class="stepper-label">Info Académica</div></div>
            <div class="stepper-line"></div>
            <div class="stepper-item"><div class="stepper-number">3</div><div class="stepper-label">Revisión y Envío</div></div>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/index.php?c=student&a=registroPaso2Post"
              enctype="multipart/form-data">
            <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">

            <div class="card card-shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Paso 2: Información Académica</h5>
                </div>
                <div class="card-body">
                    <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nivel Educativo <span class="text-danger">*</span></label>
                            <select name="nivel_id" class="form-select" required>
                                <option value="">Seleccionar…</option>
                                <?php foreach ($niveles as $niv): ?>
                                <option value="<?= $niv['id'] ?>"
                                        <?= ($infoAc['nivel_id'] ?? '') == $niv['id'] ? 'selected' : '' ?>>
                                    <?= e($niv['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Institución <span class="text-danger">*</span></label>
                            <input type="text" name="institucion" class="form-control" required
                                   placeholder="Nombre de tu escuela o universidad"
                                   value="<?= e($infoAc['institucion'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Carrera / Programa</label>
                            <input type="text" name="carrera_o_programa" class="form-control"
                                   placeholder="Ingeniería, Licenciatura en…"
                                   value="<?= e($infoAc['carrera_o_programa'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Semestre / Grado</label>
                            <input type="number" name="semestre_o_grado" class="form-control"
                                   min="1" max="20"
                                   value="<?= e($infoAc['semestre_o_grado'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Promedio <span class="text-danger">*</span></label>
                            <input type="number" name="promedio" class="form-control"
                                   min="0" max="10" step="0.01" required
                                   value="<?= e($infoAc['promedio'] ?? '') ?>">
                            <?php if ($beca['promedio_minimo']): ?>
                            <div class="form-text">Mínimo requerido: <?= $beca['promedio_minimo'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Matrícula</label>
                            <input type="text" name="matricula" class="form-control"
                                   value="<?= e($infoAc['matricula'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ciclo Escolar</label>
                            <input type="text" name="ciclo_escolar" class="form-control"
                                   placeholder="2024-1" value="<?= e($infoAc['ciclo_escolar'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <?php if ($docsReq): ?>
            <div class="card card-shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i>Documentos Requeridos</h5>
                    <small class="text-muted">Formato PDF, JPG o PNG. Máximo 5 MB por archivo.</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                    <?php foreach ($docsReq as $doc): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <?= e(str_replace('_', ' ', $doc)) ?> <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="documentos[<?= e($doc) ?>]"
                               class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1&beca_id=<?= $beca['id'] ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Paso Anterior
                </a>
                <button type="submit" class="btn btn-student">
                    Siguiente <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
