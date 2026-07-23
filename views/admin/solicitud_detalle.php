<?php
$bodyClass = 'admin-layout';
require_once BASE_PATH . '/includes/partials/head.php';
?>
<div class="admin-container">
    <?php require_once BASE_PATH . '/includes/partials/sidebar_admin.php'; ?>
    <div class="admin-main">
        <?php require_once BASE_PATH . '/includes/partials/topbar_admin.php'; ?>
        <div class="admin-content">
            <div class="page-header d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= BASE_URL ?>/index.php?c=admin&a=solicitudes"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="page-title mb-0">Solicitud: <?= e($solicitud['folio']) ?></h2>
                        <p class="text-muted mb-0"><?= e($solicitud['alumno_nombre']) ?> &mdash; <?= e($solicitud['beca_nombre']) ?></p>
                    </div>
                </div>
                <?= badgeEstado($solicitud['estado']) ?>
            </div>

            <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

            <div class="row g-4">
                <!-- Info principal -->
                <div class="col-lg-8">
                    <!-- Datos del Alumno -->
                    <div class="card card-shadow mb-4">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person me-2"></i>Datos del Solicitante</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Nombre Completo</small>
                                    <strong><?= e($solicitud['alumno_nombre']) ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Correo</small>
                                    <strong><?= e($solicitud['alumno_email']) ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">CURP</small>
                                    <strong><?= e($solicitud['curp'] ?? '—') ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Folio</small>
                                    <strong class="text-primary"><?= e($solicitud['folio']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos -->
                    <div class="card card-shadow mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0"><i class="bi bi-files me-2"></i>Documentos</h5>
                            <small class="text-muted">
                                <?= count(array_filter($documentos, fn($d) => $d['estado'] === 'validado')) ?>
                                / <?= count($documentos) ?> validados
                            </small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr><th>Documento</th><th>Estado</th><th>Observaciones</th><th>Acciones</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                            <?= e($doc['tipo_documento']) ?>
                                        </td>
                                        <td><?= badgeDocumento($doc['estado']) ?></td>
                                        <td class="small text-muted"><?= e($doc['observaciones'] ?? '—') ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button onclick="cambiarEstadoDoc(<?= $doc['id'] ?>, 'validado')"
                                                        class="btn btn-outline-success btn-sm" title="Validar">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button onclick="rechazarDoc(<?= $doc['id'] ?>)"
                                                        class="btn btn-outline-danger btn-sm" title="Rechazar">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($documentos)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">Sin documentos adjuntos.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de estados -->
                    <div class="card card-shadow mb-4">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Historial</h5></div>
                        <div class="card-body">
                            <ul class="timeline-list">
                            <?php foreach ($historial as $h): ?>
                            <li class="timeline-item">
                                <div class="timeline-badge"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>
                                            <?= $h['estado_anterior'] ? e($h['estado_anterior']) . ' → ' : '' ?>
                                            <?= e($h['estado_nuevo']) ?>
                                        </strong>
                                        <small class="text-muted"><?= formatDate($h['created_at'], 'd/m/Y H:i') ?></small>
                                    </div>
                                    <?php if ($h['comentario']): ?>
                                    <p class="text-muted small mb-0"><?= e($h['comentario']) ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">por: <?= e($h['actor'] ?? 'Sistema') ?></small>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Mensajes -->
                    <div class="card card-shadow">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-chat-dots me-2"></i>Mensajes</h5></div>
                        <div class="card-body">
                            <div class="mensajes-container mb-3" style="max-height:300px;overflow-y:auto;">
                            <?php foreach ($mensajes as $msg): ?>
                            <div class="mensaje-item <?= $msg['remitente_rol'] == ROL_ADMIN ? 'mensaje-admin' : 'mensaje-alumno' ?>">
                                <div class="mensaje-header">
                                    <strong><?= e($msg['remitente_nombre']) ?></strong>
                                    <small class="text-muted"><?= formatDate($msg['created_at'], 'd/m/Y H:i') ?></small>
                                </div>
                                <p class="mensaje-body"><?= nl2br(e($msg['cuerpo'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($mensajes)): ?>
                            <p class="text-muted text-center py-3">Sin mensajes.</p>
                            <?php endif; ?>
                            </div>
                            <!-- Responder -->
                            <form onsubmit="enviarMensaje(event)">
                                <div class="input-group">
                                    <input type="text" id="msgCuerpo" class="form-control"
                                           placeholder="Escribe un mensaje al alumno…">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel de acciones -->
                <div class="col-lg-4">
                    <div class="card card-shadow mb-4">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-gear me-2"></i>Cambiar Estado</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nuevo Estado</label>
                                <select id="nuevoEstado" class="form-select">
                                    <option value="en_revision">En Revisión</option>
                                    <option value="aprobada">Aprobada</option>
                                    <option value="rechazada">Rechazada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Comentario</label>
                                <textarea id="comentarioEstado" class="form-control" rows="3"
                                          placeholder="Motivo del cambio de estado…"></textarea>
                            </div>
                            <button type="button" class="btn btn-primary w-100" onclick="cambiarEstado()">
                                <i class="bi bi-arrow-repeat me-2"></i>Actualizar Estado
                            </button>
                        </div>
                    </div>

                    <!-- Info de beca -->
                    <div class="card card-shadow">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-award me-2"></i>Beca Solicitada</h5></div>
                        <div class="card-body">
                            <p class="fw-semibold mb-1"><?= e($solicitud['beca_nombre']) ?></p>
                            <p class="text-muted small mb-2"><?= e($solicitud['categoria']) ?></p>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Monto:</span>
                                <strong><?= formatMoney($solicitud['monto']) ?>/<?= e($solicitud['tipo_monto']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">Fecha envío:</span>
                                <span class="small"><?= $solicitud['fecha_envio'] ? formatDate($solicitud['fecha_envio']) : '—' ?></span>
                            </div>
                            <?php if ($solicitud['revisado_por']): ?>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">Revisor:</span>
                                <span class="small"><?= e($solicitud['revisor_nombre']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal rechazo doc -->
<div class="modal fade" id="modalRechazoDoc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechazar Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="motivoRechazoDoc" class="form-control" rows="3"
                          placeholder="Motivo del rechazo…"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazoDoc()">Rechazar</button>
            </div>
        </div>
    </div>
</div>

<script>
const SOLICITUD_ID    = <?= $solicitud['id'] ?>;
const DESTINATARIO_ID = <?= $solicitud['usuario_id'] ?>;

function cambiarEstado() {
    const estado    = document.getElementById('nuevoEstado').value;
    const comentario= document.getElementById('comentarioEstado').value;
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=solicitudCambiarEstado', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: `solicitud_id=${SOLICITUD_ID}&estado=${estado}&comentario=${encodeURIComponent(comentario)}`
    })
    .then(r => r.json())
    .then(d => { if(d.success) location.reload(); else alert(d.message); });
}

function enviarMensaje(e) {
    e.preventDefault();
    const cuerpo = document.getElementById('msgCuerpo').value.trim();
    if (!cuerpo) return;
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=solicitudEnviarMensaje', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: `solicitud_id=${SOLICITUD_ID}&destinatario_id=${DESTINATARIO_ID}&cuerpo=${encodeURIComponent(cuerpo)}`
    })
    .then(r => r.json())
    .then(d => { if(d.success) location.reload(); else alert(d.message); });
}

let docRechazoId = null;
function cambiarEstadoDoc(id, estado) {
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=documentoValidar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body:`documento_id=${id}&estado=${estado}`
    }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else alert(d.message); });
}
function rechazarDoc(id) {
    docRechazoId = id;
    document.getElementById('motivoRechazoDoc').value = '';
    new bootstrap.Modal(document.getElementById('modalRechazoDoc')).show();
}
function confirmarRechazoDoc() {
    const obs = document.getElementById('motivoRechazoDoc').value.trim();
    if (!obs) { alert('Ingresa el motivo.'); return; }
    fetch('<?= BASE_URL ?>/index.php?c=admin&a=documentoValidar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body:`documento_id=${docRechazoId}&estado=rechazado&observaciones=${encodeURIComponent(obs)}`
    }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else alert(d.message); });
}
</script>

<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
