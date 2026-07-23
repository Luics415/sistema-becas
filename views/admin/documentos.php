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
                <h2 class="page-title">Gestión de Documentos / Expedientes</h2>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <?php
                $stats = [
                    ['label'=>'Total Archivos', 'key'=>'total',       'icon'=>'bi-files',               'cls'=>'primary'],
                    ['label'=>'Pendientes',     'key'=>'pendientes',   'icon'=>'bi-hourglass',           'cls'=>'warning'],
                    ['label'=>'En Revisión',    'key'=>'en_revision',  'icon'=>'bi-eye',                 'cls'=>'info'],
                    ['label'=>'Validados',      'key'=>'validados',    'icon'=>'bi-check-circle-fill',   'cls'=>'success'],
                    ['label'=>'Rechazados',     'key'=>'rechazados',   'icon'=>'bi-x-circle-fill',       'cls'=>'danger'],
                ];
                foreach ($stats as $s):
                ?>
                <div class="col-6 col-md-4 col-xl-2d4">
                    <div class="card card-shadow text-center py-3">
                        <i class="bi <?= $s['icon'] ?> fs-3 text-<?= $s['cls'] ?>"></i>
                        <div class="fw-bold fs-5 mt-1"><?= $resumen[$s['key']] ?? 0 ?></div>
                        <small class="text-muted"><?= $s['label'] ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Filtros -->
            <div class="card card-shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-3 align-items-end">
                        <input type="hidden" name="c" value="admin">
                        <input type="hidden" name="a" value="documentos">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                       placeholder="Alumno, folio…" value="<?= e($busqueda) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="estado" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="pendiente"   <?= $filtroEst === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="en_revision" <?= $filtroEst === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                                <option value="validado"    <?= $filtroEst === 'validado' ? 'selected' : '' ?>>Validado</option>
                                <option value="rechazado"   <?= $filtroEst === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
                            <a href="<?= BASE_URL ?>/index.php?c=admin&a=documentos" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card card-shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Tipo de Documento</th>
                                    <th>Solicitud / Beca</th>
                                    <th>Estado</th>
                                    <th>Última Actualización</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($doc['alumno']) ?></div>
                                    <small class="text-muted"><?= e($doc['folio']) ?></small>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                    <?= e($doc['tipo_documento']) ?>
                                </td>
                                <td class="small text-truncate" style="max-width:180px"><?= e($doc['beca_nombre']) ?></td>
                                <td><?= badgeDocumento($doc['estado']) ?></td>
                                <td class="small text-muted"><?= formatDate($doc['updated_at'], 'd/m/Y H:i') ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button"
                                                class="btn btn-outline-success"
                                                onclick="cambiarEstadoDoc(<?= $doc['id'] ?>, 'validado')"
                                                title="Validar">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-info"
                                                onclick="cambiarEstadoDoc(<?= $doc['id'] ?>, 'en_revision')"
                                                title="Poner en revisión">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="rechazarDoc(<?= $doc['id'] ?>)"
                                                title="Rechazar">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudDetalle&id=<?= $doc['solicitud_id'] ?>"
                                           class="btn btn-outline-secondary" title="Ver expediente">
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($documentos)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>No se encontraron documentos.
                            </td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal rechazo -->
<div class="modal fade" id="modalRechazo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechazar Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                <textarea id="motivoRechazo" class="form-control" rows="3"
                          placeholder="Ej. El documento es ilegible, está vencido…"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">Confirmar Rechazo</button>
            </div>
        </div>
    </div>
</div>

<script>
let docIdRechazo = null;

function cambiarEstadoDoc(id, estado) {
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=documentoValidar', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: `documento_id=${id}&estado=${estado}`
    })
    .then(r => r.json())
    .then(d => { if(d.success) location.reload(); else alert(d.message); });
}

function rechazarDoc(id) {
    docIdRechazo = id;
    document.getElementById('motivoRechazo').value = '';
    new bootstrap.Modal(document.getElementById('modalRechazo')).show();
}

function confirmarRechazo() {
    const obs = document.getElementById('motivoRechazo').value.trim();
    if (!obs) { alert('Por favor ingresa el motivo del rechazo.'); return; }
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=documentoValidar', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: `documento_id=${docIdRechazo}&estado=rechazado&observaciones=${encodeURIComponent(obs)}`
    })
    .then(r => r.json())
    .then(d => { if(d.success) location.reload(); else alert(d.message); });
}
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
