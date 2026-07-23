<?php
$bodyClass = 'student-layout';
require_once BASE_PATH . '/includes/partials/head.php';
require_once BASE_PATH . '/includes/partials/navbar_student.php';
?>

<div class="container py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias">Convocatorias</a>
            </li>
            <li class="breadcrumb-item active"><?= e($beca['nombre']) ?></li>
        </ol>
    </nav>

    <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

    <div class="row g-4">

        <!-- Contenido principal -->
        <div class="col-lg-8">
            <div class="card card-shadow mb-4">
                <!-- Banner de categoría -->
                <div class="card-header d-flex align-items-center gap-2 py-3"
                     style="background:linear-gradient(135deg,var(--student-primary),#c44c0a);color:#fff">
                    <?php if (!empty($beca['icono'])): ?>
                    <i class="bi bi-<?= e($beca['icono']) ?> fs-3"></i>
                    <?php endif; ?>
                    <div>
                        <div class="fw-bold fs-5"><?= e($beca['nombre']) ?></div>
                        <small class="opacity-75"><?= e($beca['categoria_nombre']) ?> · <?= e($beca['nivel_nombre']) ?></small>
                    </div>
                </div>

                <div class="card-body">
                    <?php if ($beca['descripcion']): ?>
                    <h6 class="fw-bold text-muted mb-2">Descripción</h6>
                    <p><?= nl2br(e($beca['descripcion'])) ?></p>
                    <hr>
                    <?php endif; ?>

                    <!-- Monto destacado -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4 text-center">
                            <div class="border rounded p-3">
                                <div class="fs-3 fw-bold text-success"><?= formatMoney($beca['monto']) ?></div>
                                <small class="text-muted text-capitalize"><?= e($beca['tipo_monto']) ?></small>
                            </div>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="border rounded p-3">
                                <div class="fs-3 fw-bold text-primary">
                                    <?= $beca['promedio_minimo'] ? number_format($beca['promedio_minimo'], 1) : 'N/A' ?>
                                </div>
                                <small class="text-muted">Promedio mínimo</small>
                            </div>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="border rounded p-3">
                                <div class="fs-3 fw-bold text-info">
                                    <?= $beca['cupo_maximo'] ? number_format($beca['cupo_maximo']) : '∞' ?>
                                </div>
                                <small class="text-muted">Cupo disponible</small>
                            </div>
                        </div>
                    </div>

                    <!-- Requisitos -->
                    <?php if ($beca['requisitos']): ?>
                    <h6 class="fw-bold text-muted mb-2">Requisitos</h6>
                    <div class="bg-light rounded p-3 mb-3">
                        <?= nl2br(e($beca['requisitos'])) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Documentos requeridos -->
                    <?php
                    $docsReq = [];
                    if (!empty($beca['documentos_req'])) {
                        $docsReq = is_array($beca['documentos_req'])
                            ? $beca['documentos_req']
                            : (json_decode($beca['documentos_req'], true) ?? []);
                    }
                    ?>
                    <?php if ($docsReq): ?>
                    <h6 class="fw-bold text-muted mb-2">Documentos a Entregar</h6>
                    <ul class="list-group list-group-flush border rounded mb-3">
                        <?php foreach ($docsReq as $doc): ?>
                        <li class="list-group-item d-flex align-items-center gap-2 small">
                            <i class="bi bi-file-earmark-check text-success"></i>
                            <?= e(is_array($doc) ? ($doc['nombre'] ?? '') : $doc) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <!-- Vigencia -->
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-calendar-event fs-5"></i>
                        <div class="small">
                            Vigencia de la convocatoria:
                            <strong><?= formatDate($beca['fecha_inicio']) ?></strong> al
                            <strong><?= formatDate($beca['fecha_fin']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar de acción -->
        <div class="col-lg-4">

            <!-- CTA: Postular -->
            <div class="card card-shadow mb-4">
                <div class="card-body text-center py-4">
                    <?php if ($existeSolicitud): ?>
                        <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                        <h6 class="fw-bold text-success">Ya tienes una solicitud</h6>
                        <p class="small text-muted mb-3">
                            Estado actual: <?= badgeEstado($existeSolicitud['estado']) ?>
                        </p>
                        <a href="<?= BASE_URL ?>/index.php?c=student&a=tramiteDetalle&id=<?= $existeSolicitud['id'] ?>"
                           class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-1"></i>Ver mi solicitud
                        </a>
                    <?php else: ?>
                        <i class="bi bi-mortarboard" style="font-size:3rem;color:var(--student-primary)"></i>
                        <h6 class="fw-bold mt-2">¿Te interesa esta beca?</h6>
                        <p class="small text-muted mb-3">
                            Completa el proceso de registro en 3 sencillos pasos.
                        </p>
                        <?php
                        $hoy = new DateTime();
                        $fin = new DateTime($beca['fecha_fin']);
                        $vencida = $hoy > $fin;
                        ?>
                        <?php if ($vencida): ?>
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Esta convocatoria ya cerró el <?= formatDate($beca['fecha_fin']) ?>.
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1&beca_id=<?= $beca['id'] ?>"
                               class="btn btn-lg w-100"
                               style="background:var(--student-primary);color:#fff;border-color:var(--student-primary)">
                                <i class="bi bi-send me-1"></i>Postular ahora
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resumen -->
            <div class="card card-shadow">
                <div class="card-header fw-bold small">Resumen de la Beca</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Categoría</span>
                        <span class="badge bg-<?= e($beca['color_badge']) ?>"><?= e($beca['categoria_nombre']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Nivel</span>
                        <strong><?= e($beca['nivel_nombre']) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Monto</span>
                        <strong class="text-success"><?= formatMoney($beca['monto']) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Periodicidad</span>
                        <strong class="text-capitalize"><?= e($beca['tipo_monto']) ?></strong>
                    </li>
                    <?php if ($beca['promedio_minimo']): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Promedio req.</span>
                        <strong><?= number_format($beca['promedio_minimo'], 1) ?></strong>
                    </li>
                    <?php endif; ?>
                    <?php if ($beca['cupo_maximo']): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Cupo</span>
                        <strong><?= number_format($beca['cupo_maximo']) ?> lugares</strong>
                    </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Cierre</span>
                        <strong><?= formatDate($beca['fecha_fin']) ?></strong>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
