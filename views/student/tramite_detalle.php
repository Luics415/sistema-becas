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
                <a href="<?= BASE_URL ?>/index.php?c=student&a=tramites">Mis Trámites</a>
            </li>
            <li class="breadcrumb-item active">Folio: <?= e($solicitud['folio'] ?? '—') ?></li>
        </ol>
    </nav>

    <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

    <!-- Encabezado con estado -->
    <div class="card card-shadow mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <div class="text-muted small mb-0">Folio de solicitud</div>
                    <div class="fs-4 fw-bold text-primary"><?= e($solicitud['folio'] ?? '—') ?></div>
                </div>
                <div class="vr d-none d-md-block"></div>
                <div>
                    <div class="text-muted small">Beca solicitada</div>
                    <div class="fw-semibold"><?= e($solicitud['beca'] ?? $solicitud['beca_nombre'] ?? '—') ?></div>
                </div>
                <div class="vr d-none d-md-block"></div>
                <div>
                    <div class="text-muted small">Estado</div>
                    <div><?= badgeEstado($solicitud['estado']) ?></div>
                </div>
                <div class="vr d-none d-md-block"></div>
                <div>
                    <div class="text-muted small">Fecha de envío</div>
                    <div class="small fw-semibold">
                        <?= isset($solicitud['fecha_envio']) && $solicitud['fecha_envio']
                            ? formatDate($solicitud['fecha_envio']) : '—' ?>
                    </div>
                </div>
                <div class="ms-auto">
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=tramites"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Columna izquierda: documentos + mensajes -->
        <div class="col-lg-8">

            <!-- Documentos -->
            <div class="card card-shadow mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-paperclip" style="color:var(--student-primary)"></i>
                    <strong>Mis Documentos</strong>
                    <span class="badge bg-secondary ms-auto"><?= count($documentos) ?></span>
                </div>
                <?php if (empty($documentos)): ?>
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                    No hay documentos registrados para esta solicitud.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Documento</th>
                                <th>Archivo</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($documentos as $doc): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold small"><?= e($doc['tipo_documento']) ?></div>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/<?= e($doc['ruta_archivo']) ?>"
                                   class="text-decoration-none small" target="_blank">
                                    <i class="bi bi-file-earmark me-1"></i><?= e($doc['nombre_archivo']) ?>
                                </a>
                            </td>
                            <td><?= badgeDocumento($doc['estado']) ?></td>
                            <td class="small text-muted">
                                <?= $doc['observaciones'] ? e($doc['observaciones']) : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php if (in_array($solicitud['estado'], ['enviada', 'en_revision'])): ?>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=documentos"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-upload me-1"></i>Subir / Reemplazar Documentos
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Mensajes con el administrador -->
            <div class="card card-shadow">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots" style="color:var(--student-primary)"></i>
                    <strong>Mensajes con el Administrador</strong>
                </div>

                <!-- Área de mensajes -->
                <div class="card-body" style="max-height:380px;overflow-y:auto" id="mensajesContainer">
                    <?php if (empty($mensajes)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat fs-2 d-block mb-2"></i>
                        No hay mensajes aún.
                    </div>
                    <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($mensajes as $msg): ?>
                        <?php
                        $esAlumno = $msg['remitente_id'] === Session::get('user_id');
                        $clase    = $esAlumno ? 'mensaje-alumno' : 'mensaje-admin';
                        $align    = $esAlumno ? 'end' : 'start';
                        ?>
                        <div class="d-flex justify-content-<?= $align ?>">
                            <div class="<?= $clase ?>" style="max-width:75%">
                                <div class="fw-semibold small mb-1">
                                    <?= $esAlumno ? 'Tú' : e($msg['remitente_nombre'] ?? 'Administrador') ?>
                                </div>
                                <div><?= nl2br(e($msg['cuerpo'])) ?></div>
                                <div class="text-muted mt-1" style="font-size:0.75rem">
                                    <?= formatDate($msg['created_at']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario de respuesta -->
                <?php if (!in_array($solicitud['estado'], ['aprobada', 'rechazada'])): ?>
                <div class="card-footer">
                    <form method="POST" action="<?= BASE_URL ?>/index.php?c=student&a=enviarMensaje"
                          class="d-flex gap-2 align-items-end">
                        <input type="hidden" name="solicitud_id" value="<?= $solicitud['id'] ?>">
                        <div class="flex-fill">
                            <textarea name="cuerpo" class="form-control form-control-sm"
                                      rows="2" placeholder="Escribe un mensaje al administrador…"
                                      required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Columna derecha: historial + info -->
        <div class="col-lg-4">

            <!-- Historial de estados -->
            <div class="card card-shadow mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history" style="color:var(--student-primary)"></i>
                    <strong>Historial</strong>
                </div>
                <div class="card-body">
                    <?php if (empty($historial)): ?>
                    <p class="text-muted small mb-0">Sin historial registrado.</p>
                    <?php else: ?>
                    <ul class="timeline-list">
                        <?php foreach ($historial as $h): ?>
                        <li class="timeline-item">
                            <div class="fw-semibold small"><?= badgeEstado($h['estado_nuevo']) ?></div>
                            <?php if ($h['comentario']): ?>
                            <div class="small text-muted mt-1"><?= e($h['comentario']) ?></div>
                            <?php endif; ?>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-person me-1"></i><?= e($h['cambio_por'] ?? 'Sistema') ?>
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i><?= formatDate($h['created_at']) ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información de la beca -->
            <div class="card card-shadow mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard" style="color:var(--student-primary)"></i>
                    <strong>Sobre esta Beca</strong>
                </div>
                <div class="card-body small">
                    <div class="fw-semibold mb-2">
                        <?= e($solicitud['beca'] ?? $solicitud['beca_nombre'] ?? '—') ?>
                    </div>
                    <?php if (!empty($solicitud['categoria'])): ?>
                    <span class="badge bg-<?= e($solicitud['color_badge'] ?? 'secondary') ?> mb-2">
                        <?= e($solicitud['categoria']) ?>
                    </span>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Monto:</span>
                        <strong class="text-success">
                            <?= isset($solicitud['monto']) ? formatMoney($solicitud['monto']) : '—' ?>
                        </strong>
                    </div>
                    <?php if (!empty($solicitud['tipo_monto'])): ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tipo:</span>
                        <strong class="text-capitalize"><?= e($solicitud['tipo_monto']) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ¿Qué sigue? -->
            <?php if (!in_array($solicitud['estado'], ['aprobada', 'rechazada'])): ?>
            <div class="card card-shadow border-0"
                 style="background:linear-gradient(135deg,#fff7f0,#fff)">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color:var(--student-primary)">
                        <i class="bi bi-lightbulb me-1"></i>¿Qué sigue?
                    </h6>
                    <?php if ($solicitud['estado'] === 'enviada'): ?>
                    <p class="small text-muted mb-2">Tu solicitud fue recibida. Un administrador revisará tus documentos y te notificará sobre cualquier cambio.</p>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>Solicitud enviada</li>
                        <li class="mb-1"><i class="bi bi-hourglass-split text-warning me-1"></i>Pendiente de revisión</li>
                        <li class="text-muted"><i class="bi bi-circle me-1"></i>Resolución final</li>
                    </ul>
                    <?php elseif ($solicitud['estado'] === 'en_revision'): ?>
                    <p class="small text-muted mb-2">Tu expediente está siendo revisado. Si se requiere información adicional, te contactarán por este medio.</p>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>Solicitud enviada</li>
                        <li class="mb-1"><i class="bi bi-gear-fill text-primary me-1"></i>En revisión activa</li>
                        <li class="text-muted"><i class="bi bi-circle me-1"></i>Resolución final</li>
                    </ul>
                    <?php elseif ($solicitud['estado'] === 'borrador'): ?>
                    <p class="small text-muted mb-2">Tu solicitud está guardada como borrador. Completa los pasos pendientes para enviarla.</p>
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=registroPaso1&beca_id=<?= $solicitud['beca_id'] ?>"
                       class="btn btn-sm btn-warning w-100">
                        <i class="bi bi-pencil me-1"></i>Continuar solicitud
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($solicitud['estado'] === 'aprobada'): ?>
            <div class="card card-shadow border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-patch-check-fill text-success fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold text-success">¡Beca Aprobada!</h6>
                    <p class="small text-muted mb-0">Felicidades. Tu solicitud fue aprobada. El área de becas se pondrá en contacto contigo con los siguientes pasos.</p>
                </div>
            </div>
            <?php elseif ($solicitud['estado'] === 'rechazada'): ?>
            <div class="card card-shadow border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle-fill text-danger fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold text-danger">Solicitud No Aprobada</h6>
                    <p class="small text-muted mb-0">Tu solicitud no fue aprobada en esta ocasión. Consulta los comentarios del historial para más detalles y verifica otras convocatorias disponibles.</p>
                    <a href="<?= BASE_URL ?>/index.php?c=student&a=convocatorias"
                       class="btn btn-sm btn-outline-danger mt-2">
                        Ver otras becas
                    </a>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
