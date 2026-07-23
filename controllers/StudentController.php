<?php

require_once __DIR__ . '/Controller.php';
require_once dirname(__DIR__) . '/models/BecaModel.php';
require_once dirname(__DIR__) . '/models/SolicitudModel.php';
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__) . '/models/DocumentoModel.php';

class StudentController extends Controller
{
    private BecaModel      $becaModel;
    private SolicitudModel $solicitudModel;
    private UsuarioModel   $usuarioModel;
    private DocumentoModel $documentoModel;

    public function __construct()
    {
        parent::__construct();
        Session::requireAlumno();
        $this->becaModel      = new BecaModel();
        $this->solicitudModel = new SolicitudModel();
        $this->usuarioModel   = new UsuarioModel();
        $this->documentoModel = new DocumentoModel();
    }

    // ══════════════════════════════════════════════════════════
    // DASHBOARD (INICIO)
    // ══════════════════════════════════════════════════════════

    public function dashboard(): void
    {
        $userId    = Session::get('user_id');
        $alumno    = $this->usuarioModel->find($userId);
        $infoAc    = $this->usuarioModel->infoAcademica($userId);
        $solicitudes = $this->solicitudModel->porUsuario($userId);
        $becasDisp = $this->becaModel->publicadas();

        // Filtrar becas ya solicitadas
        $becasIds  = array_column($solicitudes, 'beca_id');
        $becasDisp = array_filter($becasDisp, fn($b) => !in_array($b['id'], $becasIds));
        $becasDisp = array_slice($becasDisp, 0, 4);

        // Mensajes no leídos
        $mensajesNoLeidos = Database::getInstance()->query(
            'SELECT COUNT(*) FROM mensajes WHERE destinatario_id = ? AND leido = 0',
            [$userId]
        )->fetchColumn();

        $this->render('student/dashboard', [
            'title'           => 'Mi Portal - Sistema de Becas',
            'alumno'          => $alumno,
            'infoAcademica'   => $infoAc,
            'solicitudes'     => $solicitudes,
            'becasDisponibles'=> array_values($becasDisp),
            'mensajesNL'      => (int) $mensajesNoLeidos,
            'flash'           => Session::getFlash('success'),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // CONVOCATORIAS
    // ══════════════════════════════════════════════════════════

    public function convocatorias(): void
    {
        $categoriaId = (int) $this->get('categoria_id', 0);
        $busqueda    = $this->get('q', '');
        $filtros     = array_filter([
            'categoria_id' => $categoriaId ?: null,
            'busqueda'     => $busqueda,
            'estado'       => 'publicada',
        ]);
        $becas      = $this->becaModel->publicadas($filtros);
        $categorias = $this->becaModel->categorias();

        $this->render('student/convocatorias', [
            'title'      => 'Convocatorias Disponibles',
            'becas'      => $becas,
            'categorias' => $categorias,
            'filtros'    => $filtros,
        ]);
    }

    public function convocatoriaDetalle(): void
    {
        $id   = (int) $this->get('id', 0);
        $beca = $this->becaModel->detalle($id);
        if (!$beca || $beca['estado'] !== 'publicada') {
            Session::flash('error', 'Convocatoria no encontrada o no disponible.', 'danger');
            $this->redirect('index.php?c=student&a=convocatorias');
        }
        $userId  = Session::get('user_id');
        $existeSolicitud = Database::getInstance()->query(
            "SELECT id, estado FROM solicitudes WHERE usuario_id = ? AND beca_id = ? LIMIT 1",
            [$userId, $id]
        )->fetch();

        $this->render('student/convocatoria_detalle', [
            'title'           => $beca['nombre'],
            'beca'            => $beca,
            'existeSolicitud' => $existeSolicitud,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // MIS TRÁMITES
    // ══════════════════════════════════════════════════════════

    public function tramites(): void
    {
        $userId      = Session::get('user_id');
        $solicitudes = $this->solicitudModel->porUsuario($userId);

        $this->render('student/tramites', [
            'title'       => 'Mis Trámites',
            'solicitudes' => $solicitudes,
            'flash'       => Session::getFlash('success'),
        ]);
    }

    public function tramiteDetalle(): void
    {
        $userId    = Session::get('user_id');
        $id        = (int) $this->get('id', 0);
        $solicitud = $this->solicitudModel->detalle($id);

        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            Session::flash('error', 'Solicitud no encontrada.', 'danger');
            $this->redirect('index.php?c=student&a=tramites');
        }
        $historial  = $this->solicitudModel->historial($id);
        $mensajes   = $this->solicitudModel->mensajes($id);
        $documentos = $this->documentoModel->porSolicitud($id);

        // Marcar mensajes como leídos
        Database::getInstance()->query(
            'UPDATE mensajes SET leido = 1 WHERE solicitud_id = ? AND destinatario_id = ?',
            [$id, $userId]
        );

        $this->render('student/tramite_detalle', [
            'title'     => 'Trámite: ' . $solicitud['folio'],
            'solicitud' => $solicitud,
            'historial' => $historial,
            'mensajes'  => $mensajes,
            'documentos'=> $documentos,
            'flash'     => Session::getFlash('success'),
        ]);
    }

    public function enviarMensaje(): void
    {
        if (!$this->isPost()) {
            $this->json(false, 'Método no permitido.');
        }
        $solicitudId = (int) $this->post('solicitud_id', 0);
        $cuerpo      = $this->post('cuerpo', '');
        $userId      = Session::get('user_id');

        if (empty($cuerpo) || !$solicitudId) {
            $this->json(false, 'Datos incompletos.');
        }
        // Verificar que la solicitud pertenece al alumno
        $solicitud = $this->solicitudModel->find($solicitudId);
        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            $this->json(false, 'Solicitud no encontrada.');
        }
        // Enviar al revisor o admin 1
        $destinatario = $solicitud['revisado_por'] ?? 1;
        $this->solicitudModel->agregarMensaje($solicitudId, $userId, $destinatario, $cuerpo);
        $this->json(true, 'Mensaje enviado.');
    }

    // ══════════════════════════════════════════════════════════
    // REGISTRO DE SOLICITUD (3 PASOS)
    // ══════════════════════════════════════════════════════════

    /** Paso 1: Información Personal */
    public function registroPaso1(): void
    {
        $becaId = (int) $this->get('beca_id', 0);
        if (!$becaId) {
            $this->redirect('index.php?c=student&a=convocatorias');
        }
        $beca = $this->becaModel->detalle($becaId);
        if (!$beca || $beca['estado'] !== 'publicada') {
            Session::flash('error', 'Convocatoria no disponible.', 'danger');
            $this->redirect('index.php?c=student&a=convocatorias');
        }

        $userId    = Session::get('user_id');
        $alumno    = $this->usuarioModel->find($userId);

        // Crear/recuperar solicitud en borrador
        $solicitud = $this->solicitudModel->iniciarORecuperar($userId, $becaId);
        if (isset($solicitud['error'])) {
            Session::flash('error', $solicitud['error'], 'danger');
            $this->redirect('index.php?c=student&a=tramites');
        }

        $this->render('student/registro_paso1', [
            'title'     => 'Registro de Solicitud - Paso 1',
            'beca'      => $beca,
            'alumno'    => $alumno,
            'solicitud' => $solicitud,
            'error'     => Session::getFlash('error'),
        ]);
    }

    /** POST Paso 1 → guarda info personal y avanza a paso 2 */
    public function registroPaso1Post(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=student&a=convocatorias');
        }
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->post('solicitud_id', 0);
        $becaId      = (int) $this->post('beca_id', 0);
        $nombre      = trim($this->post('nombre', ''));
        $apellidos   = trim($this->post('apellidos', ''));
        $curp        = strtoupper(trim($this->post('curp', '')));
        $telefono    = trim($this->post('telefono', ''));
        $genero      = $this->post('genero', '');
        $fechaNac    = $this->post('fecha_nacimiento', '');

        // Validar campos requeridos
        if (!$nombre || !$apellidos) {
            Session::flash('error', 'Nombre y apellidos son requeridos.', 'danger');
            $this->redirect("index.php?c=student&a=registroPaso1&beca_id={$becaId}");
        }

        // Validar CURP si fue proporcionado
        if ($curp && !validarCURP($curp)) {
            Session::flash('error', 'El CURP no tiene el formato correcto.', 'danger');
            $this->redirect("index.php?c=student&a=registroPaso1&beca_id={$becaId}");
        }

        // Verificar CURP duplicado (otro usuario)
        if ($curp && $this->usuarioModel->curpExiste($curp, $userId)) {
            Session::flash('error', 'Ese CURP ya está registrado por otro usuario.', 'danger');
            $this->redirect("index.php?c=student&a=registroPaso1&beca_id={$becaId}");
        }

        $this->usuarioModel->actualizarPerfil($userId, [
            'nombre'           => $nombre,
            'apellidos'        => $apellidos,
            'curp'             => $curp ?: null,
            'telefono'         => $telefono ?: null,
            'fecha_nacimiento' => $fechaNac ?: null,
            'genero'           => $genero ?: null,
        ]);
        $this->solicitudModel->avanzarPaso($solicitudId, 2);

        $this->redirect("index.php?c=student&a=registroPaso2&solicitud_id={$solicitudId}");
    }

    /** Paso 2: Información Académica */
    public function registroPaso2(): void
    {
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->get('solicitud_id', 0);
        $solicitud   = $this->solicitudModel->find($solicitudId);

        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            $this->redirect('index.php?c=student&a=tramites');
        }
        $beca      = $this->becaModel->detalle($solicitud['beca_id']);
        $infoAc    = $this->usuarioModel->infoAcademica($userId);
        $niveles   = $this->becaModel->niveles();

        $this->render('student/registro_paso2', [
            'title'     => 'Registro de Solicitud - Paso 2',
            'beca'      => $beca,
            'solicitud' => $solicitud,
            'infoAc'    => $infoAc,
            'niveles'   => $niveles,
            'error'     => Session::getFlash('error'),
        ]);
    }

    /** POST Paso 2 → guarda info académica y avanza a paso 3 */
    public function registroPaso2Post(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=student&a=dashboard');
        }
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->post('solicitud_id', 0);
        $solicitud   = $this->solicitudModel->find($solicitudId);

        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            $this->redirect('index.php?c=student&a=tramites');
        }

        $nivelId    = (int) $this->post('nivel_id', 0);
        $institucion= $this->post('institucion', '');
        $carrera    = $this->post('carrera_o_programa', '');
        $semestre   = (int) $this->post('semestre_o_grado', 0);
        $matricula  = $this->post('matricula', '');
        $promedio   = (float) $this->post('promedio', 0);
        $ciclo      = $this->post('ciclo_escolar', '');

        $errors = [];
        if (!$nivelId)       $errors[] = 'Selecciona el nivel educativo.';
        if (!$institucion)   $errors[] = 'La institución es requerida.';
        if ($promedio <= 0)  $errors[] = 'El promedio debe ser mayor a 0.';

        if ($errors) {
            Session::flash('error', implode('<br>', $errors), 'danger');
            $this->redirect("index.php?c=student&a=registroPaso2&solicitud_id={$solicitudId}");
        }

        $this->usuarioModel->guardarInfoAcademica($userId, [
            'nivel_id'           => $nivelId,
            'institucion'        => $institucion,
            'carrera_o_programa' => $carrera,
            'semestre_o_grado'   => $semestre ?: null,
            'matricula'          => $matricula,
            'promedio'           => $promedio,
            'ciclo_escolar'      => $ciclo,
        ]);

        // Subida de documentos adjuntos
        $this->procesarDocumentos($solicitudId, $userId);
        $this->solicitudModel->avanzarPaso($solicitudId, 3);

        $this->redirect("index.php?c=student&a=registroResumen&solicitud_id={$solicitudId}");
    }

    /** Paso 3: Resumen y Envío */
    public function registroResumen(): void
    {
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->get('solicitud_id', 0);
        $solicitud   = $this->solicitudModel->detalle($solicitudId);

        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            $this->redirect('index.php?c=student&a=tramites');
        }
        $alumno     = $this->usuarioModel->find($userId);
        $infoAc     = $this->usuarioModel->infoAcademica($userId);
        $documentos = $this->documentoModel->porSolicitud($solicitudId);

        $this->render('student/registro_resumen', [
            'title'     => 'Resumen y Envío Final',
            'solicitud' => $solicitud,
            'alumno'    => $alumno,
            'infoAc'    => $infoAc,
            'documentos'=> $documentos,
            'error'     => Session::getFlash('error'),
        ]);
    }

    /** POST Envío final */
    public function registroEnviar(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=student&a=dashboard');
        }
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->post('solicitud_id', 0);

        $ok = $this->solicitudModel->enviar($solicitudId, $userId);
        if (!$ok) {
            Session::flash('error', 'No se pudo enviar la solicitud. Verifica que esté completa.', 'danger');
            $this->redirect("index.php?c=student&a=registroResumen&solicitud_id={$solicitudId}");
        }
        $solicitud = $this->solicitudModel->find($solicitudId);
        $this->redirect("index.php?c=student&a=confirmacion&folio=" . urlencode($solicitud['folio']));
    }

    public function confirmacion(): void
    {
        $folio     = $this->get('folio', '');
        $solicitud = $folio ? $this->solicitudModel->porFolio($folio) : null;
        $userId    = Session::get('user_id');

        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            $this->redirect('index.php?c=student&a=tramites');
        }
        $this->render('student/confirmacion', [
            'title'     => 'Solicitud Enviada Exitosamente',
            'solicitud' => $solicitud,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // MIS DOCUMENTOS
    // ══════════════════════════════════════════════════════════

    public function documentos(): void
    {
        $userId     = Session::get('user_id');
        $documentos = $this->documentoModel->porUsuario($userId);

        $resumen = [
            'validados'   => count(array_filter($documentos, fn($d) => $d['estado'] === 'validado')),
            'en_revision' => count(array_filter($documentos, fn($d) => $d['estado'] === 'en_revision')),
            'rechazados'  => count(array_filter($documentos, fn($d) => $d['estado'] === 'rechazado')),
            'pendientes'  => count(array_filter($documentos, fn($d) => $d['estado'] === 'pendiente')),
            'total'       => count($documentos),
        ];

        $this->render('student/documentos', [
            'title'     => 'Mis Documentos',
            'documentos'=> $documentos,
            'resumen'   => $resumen,
            'flash'     => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function subirDocumento(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=student&a=documentos');
        }
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->post('solicitud_id', 0);
        $tipoDox     = $this->post('tipo_documento', '');

        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'Por favor selecciona un archivo.', 'danger');
            $this->redirect('index.php?c=student&a=documentos');
        }

        $validacion = validarArchivo($_FILES['archivo']);
        if (!$validacion['ok']) {
            Session::flash('error', $validacion['msg'], 'danger');
            $this->redirect('index.php?c=student&a=documentos');
        }

        // Guardar archivo
        $dir  = UPLOAD_PATH . "documentos/{$solicitudId}/";
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $nombreArchivo = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['archivo']['name']);
        $rutaRelativa  = "uploads/documentos/{$solicitudId}/{$nombreArchivo}";
        $rutaAbsoluta  = $dir . $nombreArchivo;

        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaAbsoluta)) {
            Session::flash('error', 'Error al guardar el archivo. Intenta de nuevo.', 'danger');
            $this->redirect('index.php?c=student&a=documentos');
        }

        $fileData = [
            'solicitud_id'   => $solicitudId,
            'usuario_id'     => $userId,
            'tipo_documento' => $tipoDox,
            'nombre_archivo' => $_FILES['archivo']['name'],
            'ruta_archivo'   => $rutaRelativa,
            'mime_type'      => $validacion['mime'],
            'tamano_bytes'   => $_FILES['archivo']['size'],
        ];

        // Verificar si ya existe ese tipo en la solicitud (reemplazar)
        $existente = Database::getInstance()->query(
            "SELECT id FROM documentos WHERE solicitud_id = ? AND tipo_documento = ? LIMIT 1",
            [$solicitudId, $tipoDox]
        )->fetchColumn();

        if ($existente) {
            $this->documentoModel->reemplazar($solicitudId, $tipoDox, $fileData);
        } else {
            $this->documentoModel->subir($fileData);
        }

        Session::flash('success', 'Documento subido correctamente.', 'success');
        $this->redirect('index.php?c=student&a=documentos');
    }

    /** Visor de documentos del alumno para una solicitud */
    public function documentoVisor(): void
    {
        $userId      = Session::get('user_id');
        $solicitudId = (int) $this->get('solicitud_id', 0);
        $docId       = (int) $this->get('doc_id', 0);

        if (!$solicitudId) {
            $this->redirect('index.php?c=student&a=documentos');
        }

        $solicitud = $this->solicitudModel->find($solicitudId);
        if (!$solicitud || $solicitud['usuario_id'] != $userId) {
            $this->redirect('index.php?c=student&a=tramites');
        }

        $documentos = $this->documentoModel->porSolicitud($solicitudId);
        $docActivo  = null;
        foreach ($documentos as $d) {
            if ($d['id'] == $docId || !$docActivo) {
                $docActivo = $d;
                if ($d['id'] == $docId) break;
            }
        }

        $this->render('student/documento_visor', [
            'title'      => 'Mis Documentos — Ver archivo',
            'solicitud'  => $solicitud,
            'documentos' => $documentos,
            'docActivo'  => $docActivo,
            'flash'      => Session::getFlash('success'),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // PERFIL DEL ALUMNO
    // ══════════════════════════════════════════════════════════

    public function perfil(): void
    {
        $userId  = Session::get('user_id');
        $alumno  = $this->usuarioModel->find($userId);
        $infoAc  = $this->usuarioModel->infoAcademica($userId);
        $niveles = $this->becaModel->niveles();

        $this->render('student/perfil', [
            'title'  => 'Mi Perfil',
            'alumno' => $alumno,
            'infoAc' => $infoAc,
            'niveles'=> $niveles,
            'flash'  => Session::getFlash('success'),
            'error'  => Session::getFlash('error'),
        ]);
    }

    public function guardarPerfil(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=student&a=perfil');
        }
        $userId    = Session::get('user_id');
        $nombre    = $this->post('nombre', '');
        $apellidos = $this->post('apellidos', '');
        $telefono  = $this->post('telefono', '');
        $genero    = $this->post('genero', '');
        $fechaNac  = $this->post('fecha_nacimiento', '');

        $this->usuarioModel->actualizarPerfil($userId, [
            'nombre'           => $nombre,
            'apellidos'        => $apellidos,
            'telefono'         => $telefono,
            'fecha_nacimiento' => $fechaNac ?: null,
            'genero'           => $genero ?: null,
        ]);

        // Info académica
        $nivelId   = (int) $this->post('nivel_id', 0);
        if ($nivelId) {
            $this->usuarioModel->guardarInfoAcademica($userId, [
                'nivel_id'           => $nivelId,
                'institucion'        => $this->post('institucion', ''),
                'carrera_o_programa' => $this->post('carrera_o_programa', ''),
                'semestre_o_grado'   => (int) $this->post('semestre_o_grado', 0) ?: null,
                'matricula'          => $this->post('matricula', ''),
                'promedio'           => (float) $this->post('promedio', 0) ?: null,
                'ciclo_escolar'      => $this->post('ciclo_escolar', ''),
            ]);
        }

        // Cambio de contraseña
        $pwActual  = $_POST['password_actual']    ?? '';
        $pwNueva   = $_POST['password_nueva']     ?? '';
        $pwConfirm = $_POST['password_confirmar'] ?? '';
        if (!empty($pwNueva)) {
            $alumno = $this->usuarioModel->find($userId);
            if (!password_verify($pwActual, $alumno['password_hash'])) {
                Session::flash('error', 'La contraseña actual no es correcta.', 'danger');
                $this->redirect('index.php?c=student&a=perfil');
            }
            if ($pwNueva !== $pwConfirm || strlen($pwNueva) < 8) {
                Session::flash('error', 'Las contraseñas nuevas no coinciden o son muy cortas.', 'danger');
                $this->redirect('index.php?c=student&a=perfil');
            }
            $this->usuarioModel->cambiarPassword($userId, $pwNueva);
        }

        Session::set('user_name', $nombre . ' ' . $apellidos);
        Session::flash('success', 'Perfil actualizado correctamente.', 'success');
        $this->redirect('index.php?c=student&a=perfil');
    }

    // ══════════════════════════════════════════════════════════
    // CONSULTA DE ESTATUS (pública también, ver PublicController)
    // ══════════════════════════════════════════════════════════

    public function consultaEstatus(): void
    {
        $folio     = $this->get('folio', '');
        $solicitud = null;
        $historial = [];
        if ($folio) {
            $solicitud = $this->solicitudModel->porFolio($folio);
            if ($solicitud) {
                $historial = $this->solicitudModel->historial($solicitud['id']);
            }
        }
        $this->render('student/consulta_estatus', [
            'title'     => 'Consulta de Estatus',
            'folio'     => $folio,
            'solicitud' => $solicitud,
            'historial' => $historial,
        ]);
    }

    // ── Privado ──────────────────────────────────────────────

    private function procesarDocumentos(int $solicitudId, int $userId): void
    {
        if (empty($_FILES['documentos']['name'])) return;
        foreach ($_FILES['documentos']['name'] as $tipoDox => $nombre) {
            if ($_FILES['documentos']['error'][$tipoDox] !== UPLOAD_ERR_OK) continue;
            $fileArr = [
                'name'     => $_FILES['documentos']['name'][$tipoDox],
                'tmp_name' => $_FILES['documentos']['tmp_name'][$tipoDox],
                'error'    => $_FILES['documentos']['error'][$tipoDox],
                'size'     => $_FILES['documentos']['size'][$tipoDox],
            ];
            $validacion = validarArchivo($fileArr);
            if (!$validacion['ok']) continue;

            $dir  = UPLOAD_PATH . "documentos/{$solicitudId}/";
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $nomArch  = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileArr['name']);
            $rutaRel  = "uploads/documentos/{$solicitudId}/{$nomArch}";
            $rutaAbs  = $dir . $nomArch;
            if (!move_uploaded_file($fileArr['tmp_name'], $rutaAbs)) continue;

            $fileData = [
                'solicitud_id'   => $solicitudId,
                'usuario_id'     => $userId,
                'tipo_documento' => $tipoDox,
                'nombre_archivo' => $fileArr['name'],
                'ruta_archivo'   => $rutaRel,
                'mime_type'      => $validacion['mime'],
                'tamano_bytes'   => $fileArr['size'],
            ];
            $existente = Database::getInstance()->query(
                "SELECT id FROM documentos WHERE solicitud_id = ? AND tipo_documento = ? LIMIT 1",
                [$solicitudId, $tipoDox]
            )->fetchColumn();
            if ($existente) {
                $this->documentoModel->reemplazar($solicitudId, $tipoDox, $fileData);
            } else {
                $this->documentoModel->subir($fileData);
            }
        }
    }
}
