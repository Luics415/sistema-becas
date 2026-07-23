<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container py-4" style="max-width:900px">
        <div class="page-header mb-4">
            <h2 class="page-title">Mi Perfil</h2>
        </div>

        <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?c=student&a=guardarPerfil">
            <div class="row g-4">
                <!-- Datos personales -->
                <div class="col-md-6">
                    <div class="card card-shadow h-100">
                        <div class="card-header"><h5 class="card-title mb-0">Datos Personales</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nombre(s)</label>
                                    <input type="text" name="nombre" class="form-control"
                                           value="<?= e($alumno['nombre'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Apellidos</label>
                                    <input type="text" name="apellidos" class="form-control"
                                           value="<?= e($alumno['apellidos'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Correo Electrónico</label>
                                    <input type="email" class="form-control" disabled
                                           value="<?= e($alumno['email'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control"
                                           value="<?= e($alumno['telefono'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Género</label>
                                    <select name="genero" class="form-select">
                                        <option value="">Seleccionar…</option>
                                        <option value="M" <?= ($alumno['genero'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="F" <?= ($alumno['genero'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                                        <option value="Otro" <?= ($alumno['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control"
                                           value="<?= e($alumno['fecha_nacimiento'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos académicos -->
                <div class="col-md-6">
                    <div class="card card-shadow h-100">
                        <div class="card-header"><h5 class="card-title mb-0">Datos Académicos</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nivel Educativo</label>
                                    <select name="nivel_id" class="form-select">
                                        <option value="">Seleccionar…</option>
                                        <?php foreach ($niveles as $niv): ?>
                                        <option value="<?= $niv['id'] ?>"
                                                <?= ($infoAc['nivel_id'] ?? '') == $niv['id'] ? 'selected' : '' ?>>
                                            <?= e($niv['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Institución</label>
                                    <input type="text" name="institucion" class="form-control"
                                           value="<?= e($infoAc['institucion'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Carrera / Programa</label>
                                    <input type="text" name="carrera_o_programa" class="form-control"
                                           value="<?= e($infoAc['carrera_o_programa'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Promedio</label>
                                    <input type="number" name="promedio" class="form-control"
                                           min="0" max="10" step="0.01"
                                           value="<?= $infoAc['promedio'] ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Semestre</label>
                                    <input type="number" name="semestre_o_grado" class="form-control"
                                           min="1" max="20"
                                           value="<?= $infoAc['semestre_o_grado'] ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Matrícula</label>
                                    <input type="text" name="matricula" class="form-control"
                                           value="<?= e($infoAc['matricula'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Ciclo Escolar</label>
                                    <input type="text" name="ciclo_escolar" class="form-control"
                                           placeholder="2024-1"
                                           value="<?= e($infoAc['ciclo_escolar'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="col-12">
                    <div class="card card-shadow">
                        <div class="card-header"><h5 class="card-title mb-0">Cambiar Contraseña</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contraseña Actual</label>
                                    <input type="password" name="password_actual" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nueva Contraseña</label>
                                    <input type="password" name="password_nueva" class="form-control" minlength="8">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Confirmar Nueva</label>
                                    <input type="password" name="password_confirmar" class="form-control">
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                Deja estos campos vacíos si no deseas cambiar tu contraseña.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-student btn-lg">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
