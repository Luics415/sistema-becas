<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<style>
/* ── Visor de Documentos ──────────────────────────────── */
.visor-layout     { display:flex; height:calc(100vh - 56px); overflow:hidden; }
.visor-main       { flex:1; display:flex; flex-direction:column; overflow:hidden; background:#f4f6fb; }
.visor-toolbar    { background:#fff; border-bottom:1px solid #e3e7ef; padding:.6rem 1.25rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-shrink:0; }
.visor-doc-badge  { display:inline-flex; align-items:center; gap:.35rem; background:#e8efff; color:var(--admin-primary); border-radius:20px; padding:.25rem .85rem; font-size:.82rem; font-weight:600; }
.visor-controls   { display:flex; align-items:center; gap:.25rem; background:#fff; border:1px solid #e3e7ef; border-radius:8px; padding:.25rem; }
.visor-ctrl-btn   { border:none; background:transparent; color:#555; padding:.3rem .5rem; border-radius:6px; cursor:pointer; display:flex; align-items:center; transition:background .15s; }
.visor-ctrl-btn:hover { background:#f0f4ff; color:var(--admin-primary); }
.visor-ctrl-divider { width:1px; height:20px; background:#e3e7ef; margin:0 .25rem; }
.visor-zoom-label { font-size:.78rem; font-weight:700; padding:0 .5rem; border-left:1px solid #e3e7ef; border-right:1px solid #e3e7ef; color:#444; }
.visor-canvas     { flex:1; overflow:auto; display:flex; align-items:flex-start; justify-content:center; padding:1.5rem; }
.visor-frame-wrap { background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,.12); width:680px; min-height:960px; position:relative; overflow:hidden; }
.visor-frame-wrap iframe,
.visor-frame-wrap img { width:100%; height:100%; min-height:960px; border:none; display:block; }
.visor-placeholder{ display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:960px; color:#aaa; gap:1rem; }
.visor-pages      { background:#fff; border-top:1px solid #e3e7ef; padding:.6rem 1.25rem; display:flex; align-items:center; justify-content:center; gap:1rem; flex-shrink:0; }
.visor-page-btn   { border:none; background:transparent; color:var(--admin-primary); font-weight:600; font-size:.85rem; cursor:pointer; display:flex; align-items:center; gap:.25rem; padding:.25rem .5rem; border-radius:6px; transition:background .15s; }
.visor-page-btn:hover { background:#e8efff; }
.visor-page-badge { background:#e3e7ef; border-radius:20px; font-size:.78rem; font-weight:600; color:#555; padding:.2rem .85rem; }

/* Panel lateral */
.visor-panel      { width:380px; min-width:320px; background:#fff; border-left:1px solid #e3e7ef; display:flex; flex-direction:column; overflow-y:auto; }
.visor-panel-sec  { padding:1.25rem; border-bottom:1px solid #f0f0f0; }
.visor-sec-title  { font-size:.7rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin-bottom:.85rem; }
.visor-solicitante{ display:flex; gap:.85rem; align-items:flex-start; background:#f8faff; border:1px solid #e3e7ef; border-radius:10px; padding:.9rem; }
.visor-avatar     { width:44px; height:44px; border-radius:8px; background:#e8efff; display:flex; align-items:center; justify-content:center; color:var(--admin-primary); font-size:1.6rem; flex-shrink:0; }
.visor-tab-bar    { display:flex; border-bottom:1px solid #e3e7ef; background:#fff; flex-shrink:0; overflow-x:auto; }
.visor-tab        { padding:.9rem 1.1rem; font-size:.78rem; font-weight:700; border:none; background:transparent; color:#94a3b8; cursor:pointer; border-bottom:2px solid transparent; white-space:nowrap; transition:all .15s; }
.visor-tab.active { color:var(--admin-primary); border-bottom-color:var(--admin-primary); background:#f8faff; }
.visor-tab:hover:not(.active) { color:#555; background:#f8f8f8; }
.visor-tab-icon   { font-size:14px; margin-right:.3rem; vertical-align:-2px; }

.field-label  { font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; margin-bottom:.2rem; }
.field-value  { font-size:.9rem; font-weight:500; color:#1e293b; }
.nota-box     { background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:.75rem 1rem; font-size:.82rem; color:#1d4ed8; line-height:1.45; }

/* Botones acción */
.btn-validar  { background:var(--admin-primary); color:#fff; border:none; border-radius:10px; padding:.7rem 1rem; font-weight:700; font-size:.88rem; display:flex; align-items:center; justify-content:center; gap:.4rem; cursor:pointer; transition:background .15s, transform .1s; }
.btn-validar:hover { background:#0d3b9e; }
.btn-validar:active { transform:scale(.97); }
.btn-rechazar { background:#fff; color:#374151; border:2px solid #e3e7ef; border-radius:10px; padding:.7rem 1rem; font-weight:700; font-size:.88rem; display:flex; align-items:center; justify-content:center; gap:.4rem; cursor:pointer; transition:all .15s; }
.btn-rechazar:hover { border-color:#fee2e2; background:#fff5f5; color:#dc2626; }
.btn-rechazar:active { transform:scale(.97); }
</style>

<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>

    <div class="admin-main" style="overflow:hidden;">
        <!-- Topbar compacto -->
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>

        <!-- Layout visor -->
        <div class="visor-layout">

            <!-- ════ ÁREA PRINCIPAL: VISOR ════ -->
            <div class="visor-main">

                <!-- Barra de herramientas -->
                <div class="visor-toolbar">
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudDetalle&id=<?= $solicitud['id'] ?>"
                           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <?php if ($docActivo): ?>
                        <span class="visor-doc-badge">
                            <i class="bi bi-file-earmark-text"></i>
                            <?= e($docActivo['nombre_archivo']) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Controles de zoom / descarga -->
                    <div class="visor-controls">
                        <button class="visor-ctrl-btn" onclick="cambiarZoom(-10)" title="Reducir">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <span class="visor-zoom-label" id="zoomLabel">100%</span>
                        <button class="visor-ctrl-btn" onclick="cambiarZoom(10)" title="Ampliar">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <div class="visor-ctrl-divider"></div>
                        <button class="visor-ctrl-btn" onclick="rotar()" title="Rotar">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <?php if ($docActivo): ?>
                        <a href="<?= BASE_URL ?>/<?= e($docActivo['ruta_archivo']) ?>"
                           download class="visor-ctrl-btn" title="Descargar">
                            <i class="bi bi-download"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Área del documento -->
                <div class="visor-canvas" id="visorCanvas">
                    <?php if ($docActivo): ?>
                    <?php
                    $mime = $docActivo['mime_type'] ?? '';
                    $ruta = BASE_URL . '/' . e($docActivo['ruta_archivo']);
                    $esImagen = str_starts_with($mime, 'image/');
                    $esPdf    = $mime === 'application/pdf';
                    ?>
                    <div class="visor-frame-wrap" id="visorFrameWrap">
                        <?php if ($esPdf): ?>
                            <iframe src="<?= $ruta ?>" id="docFrame"></iframe>
                        <?php elseif ($esImagen): ?>
                            <img src="<?= $ruta ?>" id="docFrame"
                                 style="object-fit:contain;transform-origin:top center;"
                                 alt="<?= e($docActivo['nombre_archivo']) ?>">
                        <?php else: ?>
                            <div class="visor-placeholder">
                                <i class="bi bi-file-earmark fs-1"></i>
                                <div class="fw-semibold">Vista previa no disponible</div>
                                <small class="text-muted">Tipo: <?= e($mime ?: 'desconocido') ?></small>
                                <a href="<?= $ruta ?>" download
                                   class="btn btn-primary btn-sm mt-2">
                                    <i class="bi bi-download me-1"></i>Descargar archivo
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="visor-placeholder" style="width:100%">
                        <i class="bi bi-folder2-open fs-1"></i>
                        <div class="fw-semibold text-muted">Selecciona un documento de la lista inferior</div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Paginación / tabs de documentos -->
                <div class="visor-tab-bar">
                    <?php foreach ($documentos as $doc): ?>
                    <a href="<?= BASE_URL ?>/index.php?c=admin&a=documentoVisor&solicitud_id=<?= $solicitud['id'] ?>&doc_id=<?= $doc['id'] ?>"
                       class="visor-tab <?= ($docActivo && $docActivo['id'] == $doc['id']) ? 'active' : '' ?>">
                        <i class="bi bi-<?= str_starts_with($doc['mime_type'] ?? '', 'image/') ? 'image' : 'file-earmark-pdf' ?> visor-tab-icon"></i>
                        <?= e($doc['tipo_documento']) ?>
                        <?php if ($doc['estado'] === 'validado'): ?>
                            <i class="bi bi-check-circle-fill text-success ms-1" style="font-size:11px"></i>
                        <?php elseif ($doc['estado'] === 'rechazado'): ?>
                            <i class="bi bi-x-circle-fill text-danger ms-1" style="font-size:11px"></i>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($documentos)): ?>
                    <span class="visor-tab">Sin documentos subidos</span>
                    <?php endif; ?>
                </div>

            </div><!-- /visor-main -->

            <!-- ════ PANEL LATERAL DE VALIDACIÓN ════ -->
            <aside class="visor-panel">

                <!-- Solicitante -->
                <div class="visor-panel-sec">
                    <div class="visor-sec-title">Información del Solicitante</div>
                    <div class="visor-solicitante">
                        <div class="visor-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="color:#1e293b">
                                <?= e($solicitud['alumno']) ?>
                            </div>
                            <div class="text-muted small">Folio: <?= e($solicitud['folio']) ?></div>
                            <div class="mt-1"><?= badgeEstado($solicitud['estado']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Detalles del documento activo -->
                <?php if ($docActivo): ?>
                <div class="visor-panel-sec">
                    <div class="visor-sec-title">Detalles del Documento</div>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="field-label">Tipo de Documento</div>
                            <div class="field-value"><?= e($docActivo['tipo_documento']) ?></div>
                        </div>
                        <div>
                            <div class="field-label">Nombre del Archivo</div>
                            <div class="field-value"><?= e($docActivo['nombre_archivo']) ?></div>
                        </div>
                        <div>
                            <div class="field-label">Estado Actual</div>
                            <div><?= badgeDocumento($docActivo['estado']) ?></div>
                        </div>
                        <div>
                            <div class="field-label">Fecha de Carga</div>
                            <div class="field-value"><?= formatDate($docActivo['created_at']) ?></div>
                        </div>
                        <?php if ($docActivo['observaciones']): ?>
                        <div>
                            <div class="field-label">Observaciones Previas</div>
                            <div class="field-value text-muted small"><?= e($docActivo['observaciones']) ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="nota-box">
                            <strong>Nota:</strong> Verifique que la información del documento
                            corresponda con los datos del solicitante y que el documento
                            esté vigente y sea legible.
                        </div>
                    </div>
                </div>

                <!-- Formulario de validación -->
                <div class="visor-panel-sec" style="margin-top:auto">
                    <div class="visor-sec-title">Acción de Validación</div>
                    <div class="mb-3">
                        <label class="field-label d-block mb-1">Comentarios / Observaciones</label>
                        <textarea id="observacionesInput" class="form-control form-control-sm"
                                  rows="3"
                                  placeholder="Razón del rechazo o notas adicionales…"><?= e($docActivo['observaciones'] ?? '') ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn-rechazar w-100"
                                        onclick="accionDoc(<?= $docActivo['id'] ?>, 'rechazado')">
                                    <i class="bi bi-x-circle"></i> Rechazar
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn-validar w-100"
                                        onclick="accionDoc(<?= $docActivo['id'] ?>, 'validado')">
                                    <i class="bi bi-check-circle"></i> Validar
                                </button>
                            </div>
                        </div>
                        <?php if ($docActivo['estado'] !== 'en_revision'): ?>
                        <button class="btn btn-outline-secondary btn-sm"
                                onclick="accionDoc(<?= $docActivo['id'] ?>, 'en_revision')">
                            <i class="bi bi-hourglass me-1"></i>Marcar en Revisión
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="visor-panel-sec text-center text-muted py-5">
                    <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                    No hay documento seleccionado o esta solicitud no tiene documentos adjuntos.
                </div>
                <?php endif; ?>

                <!-- Lista de todos los documentos -->
                <div class="visor-panel-sec">
                    <div class="visor-sec-title">Todos los Documentos (<?= count($documentos) ?>)</div>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($documentos as $doc): ?>
                        <?php $isActive = $docActivo && $docActivo['id'] == $doc['id']; ?>
                        <a href="<?= BASE_URL ?>/index.php?c=admin&a=documentoVisor&solicitud_id=<?= $solicitud['id'] ?>&doc_id=<?= $doc['id'] ?>"
                           class="d-flex align-items-center gap-2 p-2 rounded text-decoration-none
                                  <?= $isActive ? 'bg-primary bg-opacity-10' : 'hover-light' ?>"
                           style="border:1px solid <?= $isActive ? 'var(--admin-primary)' : '#e3e7ef' ?>;border-radius:8px!important">
                            <i class="bi bi-<?= str_starts_with($doc['mime_type'] ?? '', 'image/') ? 'image' : 'file-earmark-pdf' ?>
                               <?= $isActive ? 'text-primary' : 'text-muted' ?> fs-5"></i>
                            <div class="flex-fill overflow-hidden">
                                <div class="small fw-semibold text-truncate
                                            <?= $isActive ? 'text-primary' : 'text-dark' ?>">
                                    <?= e($doc['tipo_documento']) ?>
                                </div>
                                <div class="text-muted" style="font-size:.72rem">
                                    <?= e($doc['nombre_archivo']) ?>
                                </div>
                            </div>
                            <?= badgeDocumento($doc['estado']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </aside><!-- /visor-panel -->

        </div><!-- /visor-layout -->
    </div><!-- /admin-main -->
</div><!-- /admin-container -->

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
<script>
// ── Zoom ──────────────────────────────────────────────────
let zoomLevel = 100;
let rotacion  = 0;

function cambiarZoom(delta) {
    zoomLevel = Math.min(200, Math.max(40, zoomLevel + delta));
    aplicarTransform();
    document.getElementById('zoomLabel').textContent = zoomLevel + '%';
}

function rotar() {
    rotacion = (rotacion + 90) % 360;
    aplicarTransform();
}

function aplicarTransform() {
    const el = document.getElementById('docFrame');
    if (!el) return;
    el.style.transform = `scale(${zoomLevel/100}) rotate(${rotacion}deg)`;
    el.style.transformOrigin = 'top center';
}

// ── Validar / Rechazar documento ─────────────────────────
function accionDoc(docId, estado) {
    const obs = document.getElementById('observacionesInput')?.value ?? '';
    if (estado === 'rechazado' && !obs.trim()) {
        showToast('Escribe el motivo del rechazo en el campo de observaciones.', 'warning');
        return;
    }
    const textos = {validado:'validar',rechazado:'rechazar',en_revision:'marcar en revisión'};
    if (!confirm(`¿Confirmas ${textos[estado] ?? estado} este documento?`)) return;

    fetch('<?= BASE_URL ?>/index.php?c=admin&a=documentoValidar', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({documento_id: docId, estado, observaciones: obs})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(() => showToast('Error de conexión.', 'danger'));
}
</script>
