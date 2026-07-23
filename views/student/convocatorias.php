<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-4">
            <h2 class="page-title">Convocatorias Disponibles</h2>
            <p class="text-muted">Explora las becas activas y aplica a las que cumples los requisitos.</p>
        </div>

        <!-- Filtros -->
        <div class="card card-shadow mb-4">
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-3 align-items-end">
                    <input type="hidden" name="c" value="student">
                    <input type="hidden" name="a" value="convocatorias">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control"
                                   placeholder="Buscar beca…"
                                   value="<?= e($filtros['busqueda'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="categoria_id" class="form-select">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                    <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-student flex-fill">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                           class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tarjetas de becas -->
        <div class="row g-4">
        <?php foreach ($becas as $b): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card card-shadow beca-card h-100">
                <div class="beca-card-header" style="background: var(--student-primary)">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-white text-dark"><?= e($b['categoria_nombre']) ?></span>
                        <?php if (strtotime($b['fecha_fin']) - time() < 86400 * 3): ?>
                        <span class="badge bg-danger">¡Cierra pronto!</span>
                        <?php endif; ?>
                    </div>
                    <h5 class="beca-card-title text-white mt-2"><?= e($b['nombre']) ?></h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3 flex-grow-1">
                        <?= e(mb_strimwidth($b['descripcion'] ?? '', 0, 120, '…')) ?>
                    </p>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="beca-info-item">
                                <i class="bi bi-cash-coin text-success me-1"></i>
                                <strong><?= formatMoney($b['monto']) ?></strong>
                                <small class="text-muted d-block"><?= e($b['tipo_monto']) ?></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="beca-info-item">
                                <i class="bi bi-calendar-x text-danger me-1"></i>
                                <strong><?= formatDate($b['fecha_fin']) ?></strong>
                                <small class="text-muted d-block">Fecha límite</small>
                            </div>
                        </div>
                        <?php if ($b['promedio_minimo']): ?>
                        <div class="col-6">
                            <div class="beca-info-item">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <strong><?= $b['promedio_minimo'] ?></strong>
                                <small class="text-muted d-block">Promedio mín.</small>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-6">
                            <div class="beca-info-item">
                                <i class="bi bi-mortarboard me-1 text-primary"></i>
                                <strong class="small"><?= e($b['nivel_nombre']) ?></strong>
                                <small class="text-muted d-block">Nivel</small>
                            </div>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1&beca_id=<?= $b['id'] ?>"
                       class="btn btn-student w-100">
                        <i class="bi bi-send-fill me-2"></i>Solicitar Beca
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($becas)): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-award text-muted fs-1 d-block mb-3"></i>
                <h5 class="text-muted">No hay convocatorias disponibles</h5>
                <p class="text-muted small">Revisa más tarde o modifica los filtros de búsqueda.</p>
            </div>
        </div>
        <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
