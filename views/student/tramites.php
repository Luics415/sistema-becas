<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-4">
            <h2 class="page-title">Mis Trámites</h2>
            <p class="text-muted">Historial de todas tus solicitudes de beca.</p>
        </div>

        <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

        <div class="card card-shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre de la Beca</th>
                                <th>Folio</th>
                                <th>Fecha de Inicio</th>
                                <th>Última Actualización</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($solicitudes as $sol): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($sol['beca_nombre']) ?></div>
                                <span class="badge bg-<?= e($sol['color_badge']) ?> small">
                                    <?= e($sol['categoria']) ?>
                                </span>
                            </td>
                            <td class="fw-bold text-primary"><?= e($sol['folio']) ?></td>
                            <td class="small text-muted"><?= formatDate($sol['created_at']) ?></td>
                            <td class="small text-muted"><?= formatDate($sol['updated_at']) ?></td>
                            <td><?= badgeEstado($sol['estado']) ?></td>
                            <td class="text-center">
                                <?php if ($sol['estado'] === 'borrador'): ?>
                                <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso<?= $sol['paso_actual'] ?>&solicitud_id=<?= $sol['id'] ?>"
                                   class="btn btn-sm btn-student">
                                    <i class="bi bi-pencil me-1"></i>Continuar
                                </a>
                                <?php else: ?>
                                <a href="<?= BASE_URL ?>/index.php?c=student&a=tramiteDetalle&id=<?= $sol['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye me-1"></i>Ver Detalle
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($solicitudes)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Aún no tienes trámites.
                            <br>
                            <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                               class="btn btn-student mt-3">
                                <i class="bi bi-award me-2"></i>Ver Convocatorias
                            </a>
                        </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ayuda -->
        <div class="alert alert-info mt-4 d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <strong>¿Necesitas ayuda?</strong> Si tienes dudas sobre el estado de tu solicitud,
                <a href="<?= BASE_URL ?>/index.php?c=student&a=consultaEstatus" class="alert-link">
                    consulta el estatus con tu folio
                </a> o revisa el detalle de cada trámite.
            </div>
        </div>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
