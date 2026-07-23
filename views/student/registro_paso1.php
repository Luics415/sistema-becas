<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
$partes = explode(' ', $alumno['nombre'] ?? '');
$nombres = $alumno['nombre'] ?? '';
$apellidos = $alumno['apellidos'] ?? '';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container py-4" style="max-width:800px">
        <!-- Stepper -->
        <div class="stepper mb-5">
            <div class="stepper-item active"><div class="stepper-number">1</div><div class="stepper-label">Info Personal</div></div>
            <div class="stepper-line"></div>
            <div class="stepper-item"><div class="stepper-number">2</div><div class="stepper-label">Info Académica</div></div>
            <div class="stepper-line"></div>
            <div class="stepper-item"><div class="stepper-number">3</div><div class="stepper-label">Revisión y Envío</div></div>
        </div>

        <div class="card card-shadow">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Paso 1: Información Personal</h5>
                    <span class="badge bg-student"><?= e($beca['nombre']) ?></span>
                </div>
            </div>
            <div class="card-body">
                <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

                <form method="POST" action="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1Post">
                    <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">
                    <input type="hidden" name="beca_id" value="<?= $beca['id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   value="<?= e($nombres) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control"
                                   value="<?= e($apellidos) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CURP</label>
                            <input type="text" name="curp" id="curpInput"
                                   class="form-control text-uppercase"
                                   placeholder="RULA010215HMZZNL08" maxlength="18"
                                   value="<?= e($alumno['curp'] ?? '') ?>">
                            <div id="curpFeedback" class="form-text"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control"
                                   value="<?= e($alumno['fecha_nacimiento'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Género</label>
                            <select name="genero" class="form-select">
                                <option value="">Seleccionar…</option>
                                <option value="M" <?= ($alumno['genero'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= ($alumno['genero'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                                <option value="Otro" <?= ($alumno['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control"
                                   placeholder="55 1234 5678"
                                   value="<?= e($alumno['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" class="form-control" disabled
                                   value="<?= e(Session::get('user_email')) ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=tramites"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-x me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-student">
                            Siguiente <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
const curpInput = document.getElementById('curpInput');
const curpFeedback = document.getElementById('curpFeedback');
const curpRegex = /^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$/;

curpInput.addEventListener('input', () => {
    const val = curpInput.value.toUpperCase();
    curpInput.value = val;
    if (val.length === 18) {
        if (curpRegex.test(val)) {
            curpInput.classList.remove('is-invalid'); curpInput.classList.add('is-valid');
            curpFeedback.className = 'form-text text-success'; curpFeedback.textContent = 'CURP válido.';
        } else {
            curpInput.classList.remove('is-valid'); curpInput.classList.add('is-invalid');
            curpFeedback.className = 'form-text text-danger'; curpFeedback.textContent = 'Formato de CURP incorrecto.';
        }
    } else {
        curpInput.classList.remove('is-valid','is-invalid'); curpFeedback.textContent = '';
    }
});
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
