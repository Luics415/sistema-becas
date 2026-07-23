<?php require_once BASE_PATH . '/includes/partials/head.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-brand text-center mb-4">
            <div class="auth-logo-circle mx-auto mb-3">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1 class="auth-title">Sistema de Becas</h1>
            <p class="text-muted small">Ingresa tus credenciales para continuar</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?= $error['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?c=auth&a=login" novalidate>
            <div class="mb-3">
                <label class="form-label fw-semibold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                           placeholder="correo@ejemplo.com" required
                           value="<?= e($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passwordInput"
                           class="form-control" placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword('passwordInput', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
        </form>

        <div class="text-center">
            <p class="text-muted small mb-2">¿Eres nuevo alumno?
                <a href="<?= BASE_URL ?>/index.php?c=auth&a=registro" class="fw-semibold">Regístrate aquí</a>
            </p>
            <a href="<?= BASE_URL ?>/index.php?c=public&a=consultaEstatus"
               class="text-muted small">
                <i class="bi bi-search me-1"></i>Consultar estatus de beca sin iniciar sesión
            </a>
        </div>

        <!-- Credenciales de demo -->
        <div class="mt-4 p-3 bg-light rounded">
            <p class="small fw-bold mb-2 text-muted">Credenciales de prueba:</p>
            <div class="row g-2">
                <div class="col-6">
                    <div class="p-2 bg-white rounded border">
                        <small class="d-block fw-semibold text-primary">
                            <i class="bi bi-shield-fill me-1"></i>Admin
                        </small>
                        <small class="text-muted">admin@becas.edu.mx</small><br>
                        <small class="text-muted">password</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 bg-white rounded border">
                        <small class="d-block fw-semibold text-warning">
                            <i class="bi bi-person-fill me-1"></i>Alumno
                        </small>
                        <small class="text-muted">alejandro@alumno.edu</small><br>
                        <small class="text-muted">password</small>
                    </div>
                </div>
            </div>
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
