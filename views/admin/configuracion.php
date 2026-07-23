<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header mb-4">
                <h2 class="page-title">Configuración del Sistema</h2>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="configTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tabPerfil">
                        <i class="bi bi-person me-1"></i>Mi Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabSistema">
                        <i class="bi bi-sliders me-1"></i>Sistema
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabUsuarios">
                        <i class="bi bi-people me-1"></i>Usuarios
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Perfil -->
                <div class="tab-pane fade show active" id="tabPerfil">
                    <div class="card card-shadow">
                        <div class="card-header"><h5 class="card-title mb-0">Datos del Perfil</h5></div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?c=admin&a=guardarPerfil">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nombre(s)</label>
                                        <input type="text" name="nombre" class="form-control"
                                               value="<?= e($perfil['nombre'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Apellidos</label>
                                        <input type="text" name="apellidos" class="form-control"
                                               value="<?= e($perfil['apellidos'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Correo Electrónico</label>
                                        <input type="email" class="form-control" disabled
                                               value="<?= e($perfil['email'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Teléfono</label>
                                        <input type="tel" name="telefono" class="form-control"
                                               value="<?= e($perfil['telefono'] ?? '') ?>">
                                    </div>
                                    <div class="col-12"><hr></div>
                                    <div class="col-12">
                                        <h6 class="fw-semibold">Cambiar Contraseña (dejar vacío para no cambiar)</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Contraseña Actual</label>
                                        <input type="password" name="password_actual" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" name="password_nueva" class="form-control" minlength="8">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Confirmar Nueva</label>
                                        <input type="password" name="password_confirmar" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Guardar Perfil
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sistema -->
                <div class="tab-pane fade" id="tabSistema">
                    <form method="POST" action="<?= BASE_URL ?>/index.php?c=admin&a=guardarConfiguracion">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card card-shadow">
                                    <div class="card-header"><h5 class="card-title mb-0">General</h5></div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nombre del Sistema</label>
                                            <input type="text" name="nombre_sistema" class="form-control"
                                                   value="<?= e($config['nombre_sistema'] ?? '') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Institución</label>
                                            <input type="text" name="institucion" class="form-control"
                                                   value="<?= e($config['institucion'] ?? '') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email de Contacto</label>
                                            <input type="email" name="email_contacto" class="form-control"
                                                   value="<?= e($config['email_contacto'] ?? '') ?>">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold">Zona Horaria</label>
                                            <select name="zona_horaria" class="form-select">
                                                <option value="America/Mexico_City" <?= ($config['zona_horaria']??'') === 'America/Mexico_City' ? 'selected' : '' ?>>
                                                    América/Ciudad de México (CST)
                                                </option>
                                                <option value="America/Monterrey" <?= ($config['zona_horaria']??'') === 'America/Monterrey' ? 'selected' : '' ?>>
                                                    América/Monterrey
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-shadow mb-4">
                                    <div class="card-header"><h5 class="card-title mb-0">Mantenimiento</h5></div>
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="modo_mantenimiento"
                                                   id="switchMant" value="1"
                                                   <?= ($config['modo_mantenimiento'] ?? '0') === '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="switchMant">
                                                Modo mantenimiento activo
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-shadow">
                                    <div class="card-header"><h5 class="card-title mb-0">Notificaciones</h5></div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                   name="notif_email_admin" value="1"
                                                   <?= ($config['notif_email_admin'] ?? '1') === '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label">Notificar al administrador</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="notif_email_alumno" value="1"
                                                   <?= ($config['notif_email_alumno'] ?? '1') === '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label">Notificar al alumno</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Usuarios -->
                <div class="tab-pane fade" id="tabUsuarios">
                    <div class="card card-shadow">
                        <div class="card-header"><h5 class="card-title mb-0">Gestión de Usuarios</h5></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Último Acceso</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($u['nombre'] . ' ' . $u['apellidos']) ?></td>
                                        <td class="small"><?= e($u['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $u['rol_id'] == ROL_ADMIN ? 'primary' : 'warning' ?>">
                                                <?= $u['rol_id'] == ROL_ADMIN ? 'Admin' : 'Alumno' ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= $u['ultimo_acceso'] ? formatDate($u['ultimo_acceso'], 'd/m/Y H:i') : 'Nunca' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $u['activo'] ? 'success' : 'secondary' ?>">
                                                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-<?= $u['activo'] ? 'danger' : 'success' ?>"
                                                    onclick="toggleUsuario(<?= $u['id'] ?>)">
                                                <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUsuario(id) {
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=toggleUsuario', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body:'id='+id
    }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else alert(d.message); });
}
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
