<header class="topbar">
    <button class="btn btn-link sidebar-hamburger" id="sidebarHamburger">
        <i class="bi bi-list fs-4"></i>
    </button>

    <!-- Buscador Global -->
    <form class="topbar-search d-none d-md-flex" method="GET" action="<?= BASE_URL ?>/index.php">
        <input type="hidden" name="c" value="admin">
        <input type="hidden" name="a" value="solicitantes">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" name="q" class="form-control border-start-0"
                   placeholder="Buscar solicitante, folio…" value="<?= e($_GET['q'] ?? '') ?>">
        </div>
    </form>

    <div class="topbar-actions">
        <!-- Notificaciones -->
        <div class="dropdown">
            <button class="btn btn-link topbar-icon-btn" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger badge-dot">3</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-menu p-0">
                <div class="dropdown-header px-3 py-2 border-bottom">
                    <strong>Notificaciones</strong>
                </div>
                <a class="dropdown-item py-2 px-3" href="<?= BASE_URL ?>/index.php?c=admin&a=documentos">
                    <i class="bi bi-file-earmark-check text-warning me-2"></i>
                    Documentos pendientes de revisión
                </a>
                <a class="dropdown-item py-2 px-3" href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudes&estado=enviada">
                    <i class="bi bi-envelope text-info me-2"></i>
                    Solicitudes nuevas por revisar
                </a>
            </div>
        </div>

        <!-- Perfil Admin -->
        <div class="dropdown">
            <button class="btn btn-link topbar-user" data-bs-toggle="dropdown">
                <div class="user-avatar-sm">
                    <?php $initials = strtoupper(substr(Session::get('user_name', 'A'), 0, 1)); ?>
                    <?= $initials ?>
                </div>
                <span class="d-none d-md-inline"><?= e(Session::get('user_name', 'Admin')) ?></span>
                <i class="bi bi-chevron-down ms-1 small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header"><?= e(Session::get('user_email', '')) ?></h6></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php?c=admin&a=configuracion">
                    <i class="bi bi-person-gear me-2"></i>Mi Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/index.php?c=auth&a=logout">
                    <i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</header>
