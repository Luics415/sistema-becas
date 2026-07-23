<?php
// Detecta ruta activa
$currentC = strtolower($_GET['c'] ?? '');
$currentA = $_GET['a'] ?? '';

function navActive(string $c, string $a = ''): string {
    global $currentC, $currentA;
    if ($a) return ($currentC === $c && $currentA === $a) ? 'active' : '';
    return ($currentC === $c) ? 'active' : '';
}
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="brand-text">
                <span class="brand-name">SistemaBecas</span>
                <small class="brand-role">Panel Admin</small>
            </div>
        </div>
        <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <ul class="sidebar-nav">
        <li class="sidebar-label">Principal</li>
        <li>
            <a href="<?= BASE_URL ?>/index.php?c=admin&a=dashboard"
               class="sidebar-link <?= navActive('admin','dashboard') ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="sidebar-label">Gestión</li>
        <li>
            <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitantes"
               class="sidebar-link <?= navActive('admin','solicitantes') ?>">
                <i class="bi bi-people-fill"></i>
                <span>Solicitantes</span>
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/index.php?c=admin&a=becas"
               class="sidebar-link <?= navActive('admin','becas') ?>">
                <i class="bi bi-award-fill"></i>
                <span>Becas</span>
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/index.php?c=admin&a=documentos"
               class="sidebar-link <?= navActive('admin','documentos') ?>">
                <i class="bi bi-folder-fill"></i>
                <span>Documentos / Expedientes</span>
            </a>
        </li>
        <li class="sidebar-label">Análisis</li>
        <li>
            <a href="<?= BASE_URL ?>/index.php?c=admin&a=reportes"
               class="sidebar-link <?= navActive('admin','reportes') ?>">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Reportes y Estadísticas</span>
            </a>
        </li>
        <li class="sidebar-label">Sistema</li>
        <li>
            <a href="<?= BASE_URL ?>/index.php?c=admin&a=configuracion"
               class="sidebar-link <?= navActive('admin','configuracion') ?>">
                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/index.php?c=auth&a=logout" class="sidebar-link text-danger">
            <i class="bi bi-box-arrow-left"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</nav>
