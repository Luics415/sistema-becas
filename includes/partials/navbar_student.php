<?php
$currentA = $_GET['a'] ?? 'dashboard';
function navStudentActive(string $action): string {
    global $currentA;
    return ($currentA === $action) ? 'active' : '';
}
?>
<nav class="navbar navbar-expand-lg navbar-student sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand student-brand" href="<?= BASE_URL ?>/index.php?c=student&a=dashboard">
            <i class="bi bi-mortarboard-fill me-2"></i>EduBecas
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="studentNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= navStudentActive('dashboard') ?>"
                       href="<?= BASE_URL ?>/index.php?c=student&a=dashboard">
                        <i class="bi bi-house-fill me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navStudentActive('convocatorias') ?>"
                       href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias">
                        <i class="bi bi-megaphone-fill me-1"></i>Convocatorias
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navStudentActive('tramites') ?>"
                       href="<?= BASE_URL ?>/index.php?c=student&a=tramites">
                        <i class="bi bi-file-text-fill me-1"></i>Mis Trámites
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= navStudentActive('documentos') ?>"
                       href="<?= BASE_URL ?>/index.php?c=student&a=documentos">
                        <i class="bi bi-folder-fill me-1"></i>Documentos
                    </a>
                </li>
            </ul>
            <div class="navbar-user-actions d-flex align-items-center gap-2">
                <!-- Notificación -->
                <a href="#" class="btn btn-link topbar-icon-btn position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if (!empty($mensajesNL) && $mensajesNL > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $mensajesNL ?>
                    </span>
                    <?php endif; ?>
                </a>
                <!-- Perfil -->
                <div class="dropdown">
                    <button class="btn btn-link topbar-user p-0" data-bs-toggle="dropdown">
                        <div class="user-avatar-sm-student">
                            <?= strtoupper(substr(Session::get('user_name', 'A'), 0, 1)) ?>
                        </div>
                        <i class="bi bi-chevron-down ms-1 small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?= e(Session::get('user_name', '')) ?></h6></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php?c=student&a=perfil">
                            <i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php?c=student&a=consultaEstatus">
                            <i class="bi bi-search me-2"></i>Consultar Estatus</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger"
                               href="<?= BASE_URL ?>/index.php?c=auth&a=logout">
                            <i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
