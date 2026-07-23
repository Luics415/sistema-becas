<?php require_once BASE_PATH . '/includes/partials/head.php'; ?>

<div class="auth-wrapper auth-wrapper-wide">
    <div class="auth-card auth-card-wide">
        <div class="auth-brand text-center mb-4">
            <div class="auth-logo-circle mx-auto mb-3">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h1 class="auth-title">Crear Cuenta de Alumno</h1>
            <p class="text-muted small">Regístrate para solicitar becas</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?= $error['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?c=auth&a=registro" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre(s) <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control"
                           placeholder="Alejandro" required value="<?= e($_POST['nombre'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="apellidos" class="form-control"
                           placeholder="Ruiz López" required value="<?= e($_POST['apellidos'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                           placeholder="correo@ejemplo.com" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">CURP</label>
                    <input type="text" name="curp" class="form-control text-uppercase"
                           placeholder="RULA010215HMZZNL08" maxlength="18"
                           value="<?= e($_POST['curp'] ?? '') ?>">
                    <div class="form-text">18 caracteres. Opcional pero recomendado.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="tel" name="telefono" class="form-control"
                           placeholder="55 1234 5678" value="<?= e($_POST['telefono'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           value="<?= e($_POST['fecha_nacimiento'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Género</label>
                    <select name="genero" class="form-select">
                        <option value="">Seleccionar…</option>
                        <option value="M" <?= ($_POST['genero'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= ($_POST['genero'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                        <option value="Otro" <?= ($_POST['genero'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="pw1"
                               class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('pw1', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password_confirmar" id="pw2"
                               class="form-control" placeholder="Repite tu contraseña" required>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('pw2', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terminos" required>
                        <label class="form-check-label small" for="terminos">
                            Acepto el <a href="#" target="_blank">Aviso de Privacidad</a> y los
                            <a href="#" target="_blank">Términos de Uso</a> del sistema.
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-person-check-fill me-2"></i>Crear Cuenta
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center mt-3">
            <p class="text-muted small">¿Ya tienes cuenta?
                <a href="<?= BASE_URL ?>/index.php?c=auth&a=login" class="fw-semibold">Inicia sesión</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
