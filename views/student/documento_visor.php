<?php
$bodyClass = 'student-layout';
require_once BASE_PATH . '/includes/partials/head.php';
require_once BASE_PATH . '/includes/partials/navbar_student.php';
?>
<style>
/* ── Visor Alumno ─────────────────────────────────────── */
.svisor-wrap    { display:flex; height:calc(100vh - 68px); overflow:hidden; }
.svisor-main    { flex:1; display:flex; flex-direction:column; overflow:hidden; background:#fafafa; }
.svisor-toolbar { background:#fff; border-bottom:1px solid #e3e7ef; padding:.6rem 1.25rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-shrink:0; }
.svisor-badge   { display:inline-flex; align-items:center; gap:.35rem; background:#fff3ec; color:var(--student-primary); border-radius:20px; padding:.25rem .85rem; font-size:.82rem; font-weight:600; }
.svisor-controls{ display:flex; align-items:center; gap:.25rem; background:#fff; border:1px solid #e3e7ef; border-radius:8px; padding:.25rem; }
.svisor-btn     { border:none; background:transparent; color:#555; padding:.3rem .5rem; border-radius:6px; cursor:pointer; display:flex; align-items:center; transition:background .15s; }
.svisor-btn:hover{ background:#fff3ec; color:var(--student-primary); }
.svisor-zoom    { font-size:.78rem; font-weight:700; padding:0 .5rem; border-left:1px solid #e3e7ef; border-right:1px solid #e3e7ef; color:#444; }
.svisor-canvas  { flex:1; overflow:auto; display:flex; align-items:flex-start; justify-content:center; padding:1.5rem; }
.svisor-frame   { background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,.12); width:680px; min-height:960px; overflow:hidden; }
.svisor-frame iframe,
.svisor-frame img{ width:100%; min-height:960px; border:none; display:block; object-fit:contain; transform-origin:top center; }
.svisor-placeholder{ display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:960px; color:#bbb; gap:1rem; }
.svisor-tab-bar { background:#fff; border-top:1px solid #e3e7ef; display:flex; overflow-x:auto; flex-shrink:0; }
.svisor-tab     { padding:.85rem 1rem; font-size:.78rem; font-weight:700; border:none; background:transparent; color:#94a3b8; cursor:pointer; border-bottom:2px solid transparent; white-space:nowrap; transition:all .15s; }
.svisor-tab.active{ color:var(--student-primary); border-bottom-color:var(--student-primary); background:#fff8f5; }

/* Panel lateral alumno */
.svisor-panel   { width:340px; min-width:280px; background:#fff; border-left:1px solid #e3e7ef; display:flex; flex-direction:column; overflow-y:auto; }
.svisor-sec     { padding:1.1rem 1.25rem; border-bottom:1px solid #f0f0f0; }
.svisor-sec-ttl { font-size:.68rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin-bottom:.75rem; }
.estado-card    { border:1px solid #e3e7ef; border-radius:10px; padding:.9rem; }
.field-lbl      { font-size:.68rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#94a3b8; margin-bottom:.15rem; }
.field-val      { font-size:.88rem; font-weight:500; color:#1e293b; }
</style>

<div class="svisor-wrap">

    <!-- ════ ÁREA PRINCIPAL ════ -->
    <div class="svisor-main">

        <!-- Barra de herramientas -->
        <div class="svisor-toolbar">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASE_URL ?>/index.php?c=student&a=tramiteDetalle&id=<?= $solicitud['id'] ?>"
                   class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <?php if ($docActivo): ?>
                <span class="svisor-badge">
                    <i class="bi bi-file-earmark-text"></i>
                    <?= e($docActivo['nombre_archivo']) ?>
                </span>
                <?php endif; ?>
            </div>

            <div class="svisor-controls">
                <button class="svisor-btn" onclick="cambiarZoom(-10)" title="Reducir">
                    <i class="bi bi-zoom-out"></i>
                </button>
                <span class="svisor-zoom" id="zoomLabel">100%</span>
                <button class="svisor-btn" onclick="cambiarZoom(10)" title="Ampliar">
                    <i class="bi bi-zoom-in"></i>
                </button>
                <div style="width:1px;height:20px;background:#e3e7ef;margin:0 .25rem"></div>
                <button class="svisor-btn" onclick="rotar()" title="Rotar">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <?php if ($docActivo): ?>
                <a href="<?= BASE_URL ?>/<?= e($docActivo['ruta_archivo']) ?>"
                   download class="svisor-btn" title="Descargar">
                    <i class="bi bi-download"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Área del documento -->
        <div class="svisor-canvas">
            <?php if ($docActivo): ?>
            <?php
            $mime     = $docActivo['mime_type'] ?? '';
            $ruta     = BASE_URL . '/' . e($docActivo['ruta_archivo']);
            $esImagen = str_starts_with($mime, 'image/');
            $esPdf    = $mime === 'application/pdf';
            ?>
            <div class="svisor-frame">
                <?php if ($esPdf): ?>
                    <iframe src="<?= $ruta ?>" id="docFrame"></iframe>
                <?php elseif ($esImagen): ?>
                    <img src="<?= $ruta ?>" id="docFrame" alt="<?= e($docActivo['nombre_archivo']) ?>">
                <?php else: ?>
                    <div class="svisor-placeholder">
                        <i class="bi bi-file-earmark fs-1"></i>
                        <div class="fw-semibold">Vista previa no disponible</div>
                        <small class="text-muted">Tipo: <?= e($mime ?: 'desconocido') ?></small>
                        <a href="<?= $ruta ?>" download class="btn btn-sm mt-2"
                           style="background:var(--student-primary);color:#fff">
                            <i class="bi bi-download me-1"></i>Descargar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="svisor-placeholder" style="width:100%">
                <i class="bi bi-folder2-open fs-1"></i>
                <div class="fw-semibold text-muted">Selecciona un documento</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tabs / lista de documentos -->
        <div class="svisor-tab-bar">
            <?php foreach ($documentos as $doc): ?>
            <a href="<?= BASE_URL ?>/index.php?c=student&a=documentoVisor&solicitud_id=<?= $solicitud['id'] ?>&doc_id=<?= $doc['id'] ?>"
               class="svisor-tab <?= ($docActivo && $docActivo['id'] == $doc['id']) ? 'active' : '' ?>">
                <i class="bi bi-<?= str_starts_with($doc['mime_type'] ?? '', 'image/') ? 'image' : 'file-earmark-pdf' ?> me-1"></i>
                <?= e($doc['tipo_documento']) ?>
                <?php if ($doc['estado'] === 'validado'): ?>
                    <i class="bi bi-check-circle-fill text-success ms-1" style="font-size:11px"></i>
                <?php elseif ($doc['estado'] === 'rechazado'): ?>
                    <i class="bi bi-x-circle-fill text-danger ms-1" style="font-size:11px"></i>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
            <?php if (empty($documentos)): ?>
            <span class="svisor-tab">Sin documentos</span>
            <?php endif; ?>
        </div>

    </div><!-- /svisor-main -->

    <!-- ════ PANEL LATERAL ALUMNO ════ -->
    <aside class="svisor-panel">

        <!-- Info de la solicitud -->
        <div class="svisor-sec">
            <div class="svisor-sec-ttl">Mi Solicitud</div>
            <div class="estado-card">
                <div class="fw-bold mb-1" style="color:var(--student-primary)">
                    <?= e($solicitud['folio'] ?? '—') ?>
                </div>
                <div class="small text-muted mb-2"><?= e($solicitud['beca'] ?? $solicitud['beca_nombre'] ?? '') ?></div>
                <?= badgeEstado($solicitud['estado']) ?>
            </div>
        </div>

        <!-- Detalles del documento activo -->
        <?php if ($docActivo): ?>
        <div class="svisor-sec">
            <div class="svisor-sec-ttl">Detalles del Documento</div>
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="field-lbl">Tipo</div>
                    <div class="field-val"><?= e($docActivo['tipo_documento']) ?></div>
                </div>
                <div>
                    <div class="field-lbl">Archivo</div>
                    <div class="field-val small text-truncate"><?= e($docActivo['nombre_archivo']) ?></div>
                </div>
                <div>
                    <div class="field-lbl">Estado de Revisión</div>
                    <div><?= badgeDocumento($docActivo['estado']) ?></div>
                </div>
                <div>
                    <div class="field-lbl">Subido el</div>
                    <div class="field-val"><?= formatDate($docActivo['created_at']) ?></div>
                </div>
                <?php if ($docActivo['observaciones']): ?>
                <div class="alert alert-warning py-2 px-3 mb-0 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Observación:</strong> <?= e($docActivo['observaciones']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones del alumno -->
        <?php if (in_array($solicitud['estado'], ['enviada', 'en_revision', 'borrador'])): ?>
        <div class="svisor-sec">
            <div class="svisor-sec-ttl">Acciones</div>
            <a href="<?= BASE_URL ?>/index.php?c=student&a=documentos"
               class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
               style="background:var(--student-primary);color:#fff;border:none">
                <i class="bi bi-upload"></i> Reemplazar documento
            </a>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Lista de todos los documentos -->
        <div class="svisor-sec flex-fill">
            <div class="svisor-sec-ttl">Mis Documentos (<?= count($documentos) ?>)</div>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($documentos as $doc): ?>
                <?php $isActive = $docActivo && $docActivo['id'] == $doc['id']; ?>
                <a href="<?= BASE_URL ?>/index.php?c=student&a=documentoVisor&solicitud_id=<?= $solicitud['id'] ?>&doc_id=<?= $doc['id'] ?>"
                   class="d-flex align-items-center gap-2 p-2 text-decoration-none"
                   style="border:1px solid <?= $isActive ? 'var(--student-primary)' : '#e3e7ef' ?>;
                          background:<?= $isActive ? '#fff8f5' : '#fff' ?>;border-radius:8px">
                    <i class="bi bi-<?= str_starts_with($doc['mime_type'] ?? '', 'image/') ? 'image' : 'file-earmark-pdf' ?>
                       fs-5" style="color:<?= $isActive ? 'var(--student-primary)' : '#aaa' ?>"></i>
                    <div class="flex-fill overflow-hidden">
                        <div class="small fw-semibold text-truncate"
                             style="color:<?= $isActive ? 'var(--student-primary)' : '#333' ?>">
                            <?= e($doc['tipo_documento']) ?>
                        </div>
                        <div class="text-muted text-truncate" style="font-size:.72rem">
                            <?= e($doc['nombre_archivo']) ?>
                        </div>
                    </div>
                    <?= badgeDocumento($doc['estado']) ?>
                </a>
                <?php endforeach; ?>
                <?php if (empty($documentos)): ?>
                <div class="text-center text-muted py-3 small">
                    <i class="bi bi-folder-x d-block fs-3 mb-1"></i>
                    No has subido documentos aún.
                </div>
                <?php endif; ?>
            </div>
        </div>

    </aside>

</div><!-- /svisor-wrap -->

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
<script>
let zoomLevel = 100, rotacion = 0;

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
    if (el) {
        el.style.transform = `scale(${zoomLevel/100}) rotate(${rotacion}deg)`;
        el.style.transformOrigin = 'top center';
    }
}
</script>
