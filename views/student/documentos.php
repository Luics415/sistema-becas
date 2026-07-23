<?php
$bodyClass = 'student-layout d-flex flex-column min-vh-100';
require_once BASE_PATH . '/includes/partials/head.php';
?>

<?php require_once BASE_PATH . '/includes/partials/navbar_student.php'; ?>

<main class="flex-grow-1">
    <div class="container-fluid px-4 py-4">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title">Mis Documentos</h2>
                <p class="text-muted mb-0">Gestiona los archivos de tu expediente.</p>
            </div>
            <button class="btn btn-student" data-bs-toggle="modal" data-bs-target="#modalSubir">
                <i class="bi bi-upload me-2"></i>Subir Documento
            </button>
        </div>

        <?php require_once BASE_PATH . '/includes/partials/flash.php'; ?>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <?php
            $stats = [
                ['label'=>'Validados',    'key'=>'validados',   'cls'=>'success', 'icon'=>'bi-check-circle-fill'],
                ['label'=>'En Revisión',  'key'=>'en_revision', 'cls'=>'warning', 'icon'=>'bi-hourglass-split'],
                ['label'=>'Rechazados',   'key'=>'rechazados',  'cls'=>'danger',  'icon'=>'bi-x-circle-fill'],
                ['label'=>'Total',        'key'=>'total',       'cls'=>'primary', 'icon'=>'bi-files'],
            ];
            foreach ($stats as $s):
            ?>
            <div class="col-6 col-md-3">
                <div class="card card-shadow text-center py-3">
                    <i class="bi <?= $s['icon'] ?> fs-3 text-<?= $s['cls'] ?>"></i>
                    <div class="fw-bold fs-4 mt-1"><?= $resumen[$s['key']] ?></div>
                    <small class="text-muted"><?= $s['label'] ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tabla documentos -->
        <div class="card card-shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Documento</th>
                                <th>Solicitud</th>
                                <th>Fecha de Subida</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($documentos as $doc): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                    <div>
                                        <div class="fw-semibold"><?= e($doc['tipo_documento']) ?></div>
                                        <small class="text-muted"><?= e($doc['nombre_archivo']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= e($doc['folio'] ?? '—') ?></span>
                                <small class="d-block text-muted"><?= e($doc['beca_nombre'] ?? '') ?></small>
                            </td>
                            <td class="small text-muted"><?= formatDate($doc['created_at']) ?></td>
                            <td><?= badgeDocumento($doc['estado']) ?></td>
                            <td class="small text-muted">
                                <?php if ($doc['estado'] === 'rechazado' && $doc['observaciones']): ?>
                                <span class="text-danger">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <?= e($doc['observaciones']) ?>
                                </span>
                                <?php else: ?>
                                <?= e($doc['observaciones'] ?? '—') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($documentos)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-folder2 fs-2 d-block mb-2"></i>
                            Aún no has subido ningún documento.
                        </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Requisitos generales -->
        <div class="card card-shadow mt-4 bg-light">
            <div class="card-body">
                <h6 class="fw-semibold mb-2"><i class="bi bi-info-circle me-2"></i>Requisitos para Documentos</h6>
                <ul class="mb-0 small text-muted">
                    <li>Formato: PDF, JPG o PNG.</li>
                    <li>Tamaño máximo: 5 MB por archivo.</li>
                    <li>El archivo debe ser legible y de buena resolución.</li>
                    <li>No se aceptan documentos alterados o con datos ilegibles.</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<!-- Modal subir documento -->
<div class="modal fade" id="modalSubir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Subir Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?c=student&a=subirDocumento"
                  enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="tipo_documento" class="form-select" required>
                            <option value="">Seleccionar…</option>
                            <option value="INE">INE / Identificación Oficial</option>
                            <option value="CURP">CURP</option>
                            <option value="Kardex">Kardex Académico</option>
                            <option value="Comprobante_domicilio">Comprobante de Domicilio</option>
                            <option value="Constancia_inscripcion">Constancia de Inscripción</option>
                            <option value="Estudio_socioeconomico">Estudio Socioeconómico</option>
                            <option value="Carta_asesor">Carta de Asesor / Tutor</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <!-- Necesitamos asociar a una solicitud -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Solicitud Relacionada</label>
                        <select name="solicitud_id" class="form-select" required>
                            <option value="">Seleccionar solicitud…</option>
                            <?php
                            // Necesitamos acceso a las solicitudes del alumno; las inyectamos via JS desde el padre
                            // Si estás en la vista de documentos, no tenemos $solicitudes directamente.
                            // Usaremos una query directa
                            $userId2 = Session::get('user_id');
                            $sols = Database::getInstance()->query(
                                "SELECT id, folio, beca_id FROM solicitudes WHERE usuario_id = ? ORDER BY created_at DESC",
                                [$userId2]
                            )->fetchAll();
                            foreach ($sols as $so):
                            ?>
                            <option value="<?= $so['id'] ?>"><?= e($so['folio']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Archivo <span class="text-danger">*</span></label>
                        <input type="file" name="archivo" class="form-control"
                               accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text">PDF, JPG o PNG. Máximo 5 MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-student">
                        <i class="bi bi-upload me-2"></i>Subir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/partials/footer_student.php'; ?>
<?php require_once BASE_PATH . '/includes/partials/footer_scripts.php'; ?>
