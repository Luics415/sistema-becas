<?php
$bodyClass = 'admin-layout';
$isEdit = !empty($beca);
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header d-flex align-items-center gap-3 mb-4">
                <a href="<?= BASE_URL ?>/index.php?c=admin&a=becas" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="page-title"><?= $isEdit ? 'Editar Beca' : 'Nueva Beca' ?></h2>
                    <p class="text-muted mb-0"><?= $isEdit ? 'Modifica los datos de la convocatoria' : 'Registra una nueva convocatoria de beca' ?></p>
                </div>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?c=admin&a=<?= $isEdit ? 'becaActualizar' : 'becaCrear' ?>">
                <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $beca['id'] ?>">
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Columna principal -->
                    <div class="col-lg-8">
                        <div class="card card-shadow mb-4">
                            <div class="card-header"><h5 class="card-title mb-0">Información General</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nombre de la Beca <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control"
                                           placeholder="Ej. Beca de Excelencia Académica 2025" required
                                           value="<?= e($beca['nombre'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3"
                                              placeholder="Describe el objetivo y beneficios de la beca…"><?= e($beca['descripcion'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Requisitos</label>
                                    <textarea name="requisitos" class="form-control" rows="4"
                                              placeholder="Lista los requisitos que debe cumplir el solicitante…"><?= e($beca['requisitos'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="card card-shadow mb-4">
                            <div class="card-header"><h5 class="card-title mb-0">Periodo de Convocatoria</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fecha de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" class="form-control" required
                                               value="<?= e($beca['fecha_inicio'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fecha de Cierre <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_fin" class="form-control" required
                                               value="<?= e($beca['fecha_fin'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna lateral -->
                    <div class="col-lg-4">
                        <div class="card card-shadow mb-4">
                            <div class="card-header"><h5 class="card-title mb-0">Clasificación</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                                    <select name="categoria_id" class="form-select" required>
                                        <option value="">Seleccionar…</option>
                                        <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"
                                                <?= ($beca['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                            <?= e($cat['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nivel Educativo <span class="text-danger">*</span></label>
                                    <select name="nivel_id" class="form-select" required>
                                        <option value="">Seleccionar…</option>
                                        <?php foreach ($niveles as $niv): ?>
                                        <option value="<?= $niv['id'] ?>"
                                                <?= ($beca['nivel_id'] ?? '') == $niv['id'] ? 'selected' : '' ?>>
                                            <?= e($niv['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="borrador"   <?= ($beca['estado'] ?? 'borrador') === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                                        <option value="publicada"  <?= ($beca['estado'] ?? '') === 'publicada' ? 'selected' : '' ?>>Publicar ahora</option>
                                        <option value="suspendida" <?= ($beca['estado'] ?? '') === 'suspendida' ? 'selected' : '' ?>>Suspendida</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card card-shadow mb-4">
                            <div class="card-header"><h5 class="card-title mb-0">Monto y Cupo</h5></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Monto (MXN) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="monto" class="form-control"
                                               placeholder="5000" required min="1" step="0.01"
                                               value="<?= $beca['monto'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tipo de Monto</label>
                                    <select name="tipo_monto" class="form-select">
                                        <option value="mensual"   <?= ($beca['tipo_monto'] ?? 'mensual') === 'mensual'    ? 'selected' : '' ?>>Mensual</option>
                                        <option value="semestral" <?= ($beca['tipo_monto'] ?? '') === 'semestral'  ? 'selected' : '' ?>>Semestral</option>
                                        <option value="anual"     <?= ($beca['tipo_monto'] ?? '') === 'anual'      ? 'selected' : '' ?>>Anual</option>
                                        <option value="pago_unico"<?= ($beca['tipo_monto'] ?? '') === 'pago_unico' ? 'selected' : '' ?>>Pago Único</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Promedio Mínimo</label>
                                    <input type="number" name="promedio_minimo" class="form-control"
                                           placeholder="Ej. 8.5" min="0" max="10" step="0.1"
                                           value="<?= $beca['promedio_minimo'] ?? '' ?>">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Cupo Máximo</label>
                                    <input type="number" name="cupo_maximo" class="form-control"
                                           placeholder="Dejar vacío = sin límite" min="1"
                                           value="<?= $beca['cupo_maximo'] ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-2"></i>
                                <?= $isEdit ? 'Guardar Cambios' : 'Crear Beca' ?>
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becas"
                               class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
