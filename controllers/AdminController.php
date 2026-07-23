<?php

require_once __DIR__ . '/Controller.php';
require_once dirname(__DIR__) . '/models/BecaModel.php';
require_once dirname(__DIR__) . '/models/SolicitudModel.php';
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__) . '/models/DocumentoModel.php';

class AdminController extends Controller
{
    private BecaModel      $becaModel;
    private SolicitudModel $solicitudModel;
    private UsuarioModel   $usuarioModel;
    private DocumentoModel $documentoModel;

    public function __construct()
    {
        parent::__construct();
        Session::requireAdmin();
        $this->becaModel      = new BecaModel();
        $this->solicitudModel = new SolicitudModel();
        $this->usuarioModel   = new UsuarioModel();
        $this->documentoModel = new DocumentoModel();
    }

    // ══════════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════════

    public function dashboard(): void
    {
        $estadisticas  = $this->becaModel->estadisticas();
        $resumenSols   = $this->solicitudModel->resumenEstados();
        $recientes     = $this->solicitudModel->recientes(8);
        $resumenDocs   = $this->documentoModel->resumen();

        // Próximas a vencer (becas activas cuya fecha_fin ≤ 30 días)
        $proximas = Database::getInstance()->query(
            "SELECT id, nombre, fecha_fin, estado,
                    DATEDIFF(fecha_fin, CURDATE()) AS dias_restantes
             FROM becas
             WHERE estado = 'publicada' AND fecha_fin >= CURDATE()
             ORDER BY fecha_fin ASC LIMIT 5"
        )->fetchAll();

        $this->render('admin/dashboard', [
            'title'         => 'Dashboard - Admin',
            'estadisticas'  => $estadisticas,
            'resumenSols'   => $resumenSols,
            'recientes'     => $recientes,
            'resumenDocs'   => $resumenDocs,
            'proximas'      => $proximas,
            'flash'         => Session::getFlash('success'),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // GESTIÓN DE BECAS (CRUD)
    // ══════════════════════════════════════════════════════════

    public function becas(): void
    {
        $pagina    = max(1, (int) $this->get('pagina', 1));
        $busqueda  = $this->get('q', '');
        $estado    = $this->get('estado', '');
        $categoriaId = (int) $this->get('categoria_id', 0);

        $filtros   = array_filter([
            'busqueda'    => $busqueda,
            'estado'      => $estado,
            'categoria_id'=> $categoriaId ?: null,
        ]);
        $total     = $this->becaModel->total($filtros);
        $paginacion = paginar($total, $pagina);
        $becas     = $this->becaModel->listar($paginacion['offset'], ITEMS_PER_PAGE, $filtros);
        $categorias = $this->becaModel->categorias();

        $this->render('admin/becas', [
            'title'      => 'Gestión de Becas',
            'becas'      => $becas,
            'categorias' => $categorias,
            'paginacion' => $paginacion,
            'filtros'    => $filtros,
            'flash'      => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function becaDetalle(): void
    {
        $id   = (int) $this->get('id', 0);
        $beca = $id ? $this->becaModel->detalle($id) : null;
        if (!$beca) {
            Session::flash('error', 'Beca no encontrada.', 'danger');
            $this->redirect('index.php?c=admin&a=becas');
        }
        $solicitudes = Database::getInstance()->query(
            "SELECT s.*, CONCAT(u.nombre,' ',u.apellidos) AS alumno
             FROM solicitudes s JOIN usuarios u ON u.id = s.usuario_id
             WHERE s.beca_id = ? ORDER BY s.updated_at DESC LIMIT 20",
            [$id]
        )->fetchAll();

        $this->render('admin/beca_detalle', [
            'title'       => 'Detalle de Beca: ' . $beca['nombre'],
            'beca'        => $beca,
            'solicitudes' => $solicitudes,
        ]);
    }

    public function becaNueva(): void
    {
        $this->render('admin/beca_form', [
            'title'      => 'Nueva Beca',
            'beca'       => null,
            'categorias' => $this->becaModel->categorias(),
            'niveles'    => $this->becaModel->niveles(),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function becaCrear(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=admin&a=becaNueva');
        }

        $data = $this->validarDatosBeca();
        if (!$data['ok']) {
            Session::flash('error', $data['msg'], 'danger');
            $this->redirect('index.php?c=admin&a=becaNueva');
        }

        $data['creado_por'] = Session::get('user_id');
        $id = $this->becaModel->crear($data);
        Session::flash('success', 'Beca creada correctamente.', 'success');
        $this->redirect("index.php?c=admin&a=becaDetalle&id={$id}");
    }

    public function becaEditar(): void
    {
        $id   = (int) $this->get('id', 0);
        $beca = $this->becaModel->detalle($id);
        if (!$beca) {
            Session::flash('error', 'Beca no encontrada.', 'danger');
            $this->redirect('index.php?c=admin&a=becas');
        }
        $this->render('admin/beca_form', [
            'title'      => 'Editar Beca',
            'beca'       => $beca,
            'categorias' => $this->becaModel->categorias(),
            'niveles'    => $this->becaModel->niveles(),
            'error'      => Session::getFlash('error'),
        ]);
    }

    public function becaActualizar(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=admin&a=becas');
        }
        $id   = (int) $this->post('id', 0);
        $data = $this->validarDatosBeca();
        if (!$data['ok']) {
            Session::flash('error', $data['msg'], 'danger');
            $this->redirect("index.php?c=admin&a=becaEditar&id={$id}");
        }
        $this->becaModel->actualizar($id, $data);
        Session::flash('success', 'Beca actualizada correctamente.', 'success');
        $this->redirect("index.php?c=admin&a=becaDetalle&id={$id}");
    }

    public function becaPublicar(): void
    {
        $id = (int) $this->get('id', 0);
        $this->becaModel->cambiarEstado($id, 'publicada');
        Session::flash('success', 'Beca publicada.', 'success');
        $this->redirect("index.php?c=admin&a=becaDetalle&id={$id}");
    }

    public function becaCerrar(): void
    {
        $id = (int) $this->get('id', 0);
        $this->becaModel->cambiarEstado($id, 'cerrada');
        Session::flash('success', 'Beca cerrada.', 'success');
        $this->redirect("index.php?c=admin&a=becaDetalle&id={$id}");
    }

    public function becaEliminar(): void
    {
        $id = (int) $this->post('id', 0);
        // Solo borrar si no tiene solicitudes enviadas/aprobadas
        $count = Database::getInstance()->query(
            "SELECT COUNT(*) FROM solicitudes WHERE beca_id = ? AND estado NOT IN ('borrador','cancelada')",
            [$id]
        )->fetchColumn();
        if ($count > 0) {
            $this->json(false, 'No se puede eliminar una beca con solicitudes activas.');
        }
        $this->becaModel->delete($id);
        $this->json(true, 'Beca eliminada correctamente.');
    }

    // ══════════════════════════════════════════════════════════
    // SOLICITANTES / EXPEDIENTES
    // ══════════════════════════════════════════════════════════

    public function solicitantes(): void
    {
        $pagina   = max(1, (int) $this->get('pagina', 1));
        $busqueda = $this->get('q', '');
        $total    = $this->usuarioModel->totalAlumnos($busqueda);
        $paginacion = paginar($total, $pagina);
        $alumnos  = $this->usuarioModel->listarAlumnos($paginacion['offset'], ITEMS_PER_PAGE, $busqueda);

        $this->render('admin/solicitantes', [
            'title'      => 'Solicitantes',
            'alumnos'    => $alumnos,
            'paginacion' => $paginacion,
            'busqueda'   => $busqueda,
            'flash'      => Session::getFlash('success'),
        ]);
    }

    public function expediente(): void
    {
        $id     = (int) $this->get('id', 0);
        $alumno = $this->usuarioModel->find($id);
        if (!$alumno || $alumno['rol_id'] != ROL_ALUMNO) {
            Session::flash('error', 'Alumno no encontrado.', 'danger');
            $this->redirect('index.php?c=admin&a=solicitantes');
        }
        $infoAcademica  = $this->usuarioModel->infoAcademica($id);
        $solicitudes    = $this->solicitudModel->porUsuario($id);
        $documentos     = $this->documentoModel->porUsuario($id);

        $this->render('admin/expediente', [
            'title'         => 'Expediente: ' . $alumno['nombre'] . ' ' . $alumno['apellidos'],
            'alumno'        => $alumno,
            'infoAcademica' => $infoAcademica,
            'solicitudes'   => $solicitudes,
            'documentos'    => $documentos,
            'flash'         => Session::getFlash('success'),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // GESTIÓN DE SOLICITUDES
    // ══════════════════════════════════════════════════════════

    public function solicitudes(): void
    {
        $pagina   = max(1, (int) $this->get('pagina', 1));
        $busqueda = $this->get('q', '');
        $estado   = $this->get('estado', '');
        $filtros  = array_filter(['busqueda' => $busqueda, 'estado' => $estado]);

        $total    = $this->solicitudModel->totalAdmin($filtros);
        $paginacion = paginar($total, $pagina);
        $solicitudes = $this->solicitudModel->listarAdmin($paginacion['offset'], ITEMS_PER_PAGE, $filtros);

        $this->render('admin/solicitudes', [
            'title'       => 'Gestión de Solicitudes',
            'solicitudes' => $solicitudes,
            'paginacion'  => $paginacion,
            'filtros'     => $filtros,
        ]);
    }

    public function solicitudDetalle(): void
    {
        $id       = (int) $this->get('id', 0);
        $solicitud = $this->solicitudModel->detalle($id);
        if (!$solicitud) {
            Session::flash('error', 'Solicitud no encontrada.', 'danger');
            $this->redirect('index.php?c=admin&a=solicitudes');
        }
        $historial  = $this->solicitudModel->historial($id);
        $mensajes   = $this->solicitudModel->mensajes($id);
        $documentos = $this->documentoModel->porSolicitud($id);

        $this->render('admin/solicitud_detalle', [
            'title'     => 'Expediente: ' . $solicitud['folio'],
            'solicitud' => $solicitud,
            'historial' => $historial,
            'mensajes'  => $mensajes,
            'documentos'=> $documentos,
            'flash'     => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function solicitudCambiarEstado(): void
    {
        if (!$this->isPost()) {
            $this->json(false, 'Método no permitido.');
        }
        $id          = (int) $this->post('solicitud_id', 0);
        $nuevoEstado = $this->post('estado', '');
        $comentario  = $this->post('comentario', '');
        $adminId     = Session::get('user_id');

        $estadosValidos = ['en_revision', 'aprobada', 'rechazada', 'cancelada'];
        if (!in_array($nuevoEstado, $estadosValidos, true)) {
            $this->json(false, 'Estado no válido.');
        }

        $ok = $this->solicitudModel->cambiarEstado($id, $nuevoEstado, $adminId, $comentario);
        if ($ok) {
            $this->json(true, 'Estado actualizado correctamente.');
        } else {
            $this->json(false, 'No se pudo actualizar el estado.');
        }
    }

    public function solicitudEnviarMensaje(): void
    {
        if (!$this->isPost()) {
            $this->json(false, 'Método no permitido.');
        }
        $solicitudId    = (int) $this->post('solicitud_id', 0);
        $destinatarioId = (int) $this->post('destinatario_id', 0);
        $cuerpo         = $this->post('cuerpo', '');
        $asunto         = $this->post('asunto', '');
        $remitenteId    = Session::get('user_id');

        if (empty($cuerpo) || !$solicitudId || !$destinatarioId) {
            $this->json(false, 'Datos incompletos.');
        }

        $this->solicitudModel->agregarMensaje($solicitudId, $remitenteId, $destinatarioId, $cuerpo, $asunto);
        $this->json(true, 'Mensaje enviado correctamente.');
    }

    // ══════════════════════════════════════════════════════════
    // GESTIÓN DE DOCUMENTOS
    // ══════════════════════════════════════════════════════════

    public function documentos(): void
    {
        $pagina      = max(1, (int) $this->get('pagina', 1));
        $filtroEst   = $this->get('estado', '');
        $busqueda    = $this->get('q', '');

        $total       = $this->documentoModel->totalExpedientes($filtroEst, $busqueda);
        $paginacion  = paginar($total, $pagina);
        $documentos  = $this->documentoModel->listarExpedientes($paginacion['offset'], ITEMS_PER_PAGE, $filtroEst, $busqueda);
        $resumen     = $this->documentoModel->resumen();

        $this->render('admin/documentos', [
            'title'      => 'Gestión de Documentos / Expedientes',
            'documentos' => $documentos,
            'resumen'    => $resumen,
            'paginacion' => $paginacion,
            'filtroEst'  => $filtroEst,
            'busqueda'   => $busqueda,
            'flash'      => Session::getFlash('success'),
        ]);
    }

    public function documentoValidar(): void
    {
        if (!$this->isPost()) {
            $this->json(false, 'Método no permitido.');
        }
        $id      = (int) $this->post('documento_id', 0);
        $estado  = $this->post('estado', '');
        $obs     = $this->post('observaciones', '');
        $adminId = Session::get('user_id');

        $estados = ['en_revision', 'validado', 'rechazado'];
        if (!in_array($estado, $estados, true)) {
            $this->json(false, 'Estado no válido.');
        }
        $ok = $this->documentoModel->validar($id, $estado, $adminId, $obs);
        $this->json($ok, $ok ? 'Documento actualizado.' : 'Error al actualizar.');
    }

    /** Visor de documentos de una solicitud */
    public function documentoVisor(): void
    {
        $solicitudId = (int) $this->get('solicitud_id', 0);
        $docId       = (int) $this->get('doc_id', 0);

        if (!$solicitudId) {
            Session::flash('error', 'Solicitud no especificada.', 'danger');
            $this->redirect('index.php?c=admin&a=documentos');
        }

        $solicitud  = $this->solicitudModel->detalle($solicitudId);
        if (!$solicitud) {
            Session::flash('error', 'Solicitud no encontrada.', 'danger');
            $this->redirect('index.php?c=admin&a=documentos');
        }

        $documentos = $this->documentoModel->porSolicitud($solicitudId);
        // Documento activo: el solicitado o el primero
        $docActivo  = null;
        foreach ($documentos as $d) {
            if ($d['id'] == $docId || !$docActivo) {
                $docActivo = $d;
                if ($d['id'] == $docId) break;
            }
        }

        $this->render('admin/documento_visor', [
            'title'      => 'Visor de Documentos — ' . $solicitud['folio'],
            'solicitud'  => $solicitud,
            'documentos' => $documentos,
            'docActivo'  => $docActivo,
            'flash'      => Session::getFlash('success'),
            'error'      => Session::getFlash('error'),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // REPORTES Y ESTADÍSTICAS
    // ══════════════════════════════════════════════════════════

    public function reportes(): void
    {
        $estadisticas    = $this->becaModel->estadisticas();
        $distribucion    = $this->becaModel->distribucionPorCategoria();
        $tendencia       = $this->becaModel->tendenciaMensual();
        $estadsBecas     = Database::getInstance()->query(
            "SELECT * FROM v_estadisticas_becas ORDER BY total_solicitudes DESC"
        )->fetchAll();

        $this->render('admin/reportes', [
            'title'       => 'Reportes y Estadísticas',
            'estadisticas'=> $estadisticas,
            'distribucion'=> $distribucion,
            'tendencia'   => $tendencia,
            'estadsBecas' => $estadsBecas,
        ]);
    }

    /** AJAX: exporta CSV de solicitudes */
    public function exportarCSV(): void
    {
        $estado   = $this->get('estado', '');
        $filtros  = $estado ? ['estado' => $estado] : [];
        $data     = $this->solicitudModel->listarAdmin(0, 10000, $filtros);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="becas_' . date('Ymd') . '.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($out, ['Folio', 'Alumno', 'Email', 'Beca', 'Categoría', 'Monto', 'Estado', 'Fecha Envío']);
        foreach ($data as $row) {
            fputcsv($out, [
                $row['folio'],
                $row['alumno'],
                $row['alumno_email'],
                $row['beca'],
                $row['categoria'],
                $row['monto'],
                $row['estado'],
                $row['fecha_envio'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }

    // ══════════════════════════════════════════════════════════
    // CONFIGURACIÓN DE PERFIL / SISTEMA
    // ══════════════════════════════════════════════════════════

    public function configuracion(): void
    {
        $db     = Database::getInstance();
        $config = $db->query('SELECT * FROM configuracion_sistema ORDER BY grupo, clave')->fetchAll();
        $configMap = [];
        foreach ($config as $c) {
            $configMap[$c['clave']] = $c['valor'];
        }

        $adminId = Session::get('user_id');
        $perfil  = $this->usuarioModel->find($adminId);
        $usuarios = $db->query(
            'SELECT id, nombre, apellidos, email, rol_id, activo, ultimo_acceso FROM usuarios ORDER BY rol_id, nombre'
        )->fetchAll();

        $this->render('admin/configuracion', [
            'title'    => 'Configuración del Sistema',
            'config'   => $configMap,
            'perfil'   => $perfil,
            'usuarios' => $usuarios,
            'flash'    => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ]);
    }

    public function guardarConfiguracion(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=admin&a=configuracion');
        }
        $db = Database::getInstance();
        $campos = ['nombre_sistema', 'institucion', 'email_contacto', 'telefono_contacto',
                   'zona_horaria', 'modo_mantenimiento', 'max_archivos_mb',
                   'notif_email_admin', 'notif_email_alumno', 'backup_auto'];

        foreach ($campos as $clave) {
            $valor = $this->post($clave, '0');
            $db->query('UPDATE configuracion_sistema SET valor = ? WHERE clave = ?', [$valor, $clave]);
        }
        Session::flash('success', 'Configuración guardada correctamente.', 'success');
        $this->redirect('index.php?c=admin&a=configuracion');
    }

    public function guardarPerfil(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=admin&a=configuracion');
        }
        $adminId  = Session::get('user_id');
        $nombre   = $this->post('nombre', '');
        $apellidos= $this->post('apellidos', '');
        $telefono = $this->post('telefono', '');

        $this->usuarioModel->actualizarPerfil($adminId, [
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'telefono'  => $telefono,
        ]);

        // Cambio de contraseña opcional
        $pwActual  = $_POST['password_actual']    ?? '';
        $pwNueva   = $_POST['password_nueva']     ?? '';
        $pwConfirm = $_POST['password_confirmar'] ?? '';
        if (!empty($pwNueva)) {
            $admin = $this->usuarioModel->find($adminId);
            if (!password_verify($pwActual, $admin['password_hash'])) {
                Session::flash('error', 'La contraseña actual no es correcta.', 'danger');
                $this->redirect('index.php?c=admin&a=configuracion');
            }
            if ($pwNueva !== $pwConfirm) {
                Session::flash('error', 'Las contraseñas nuevas no coinciden.', 'danger');
                $this->redirect('index.php?c=admin&a=configuracion');
            }
            if (strlen($pwNueva) < 8) {
                Session::flash('error', 'La contraseña debe tener al menos 8 caracteres.', 'danger');
                $this->redirect('index.php?c=admin&a=configuracion');
            }
            $this->usuarioModel->cambiarPassword($adminId, $pwNueva);
        }

        // Actualizar nombre en sesión
        Session::set('user_name', $nombre . ' ' . $apellidos);
        Session::flash('success', 'Perfil actualizado correctamente.', 'success');
        $this->redirect('index.php?c=admin&a=configuracion');
    }

    public function toggleUsuario(): void
    {
        if (!$this->isPost()) {
            $this->json(false, 'Método no permitido.');
        }
        $id = (int) $this->post('id', 0);
        $ok = $this->usuarioModel->toggleActivo($id);
        $this->json($ok, $ok ? 'Estado de usuario actualizado.' : 'Error.');
    }

    // ── Privado: validación de datos de beca ─────────────────

    private function validarDatosBeca(): array
    {
        $nombre     = $this->post('nombre', '');
        $categoriaId= (int) $this->post('categoria_id', 0);
        $nivelId    = (int) $this->post('nivel_id', 0);
        $monto      = (float) str_replace(',', '', $this->post('monto', '0'));
        $tipoMonto  = $this->post('tipo_monto', 'mensual');
        $promedio   = $this->post('promedio_minimo', '');
        $cupo       = $this->post('cupo_maximo', '');
        $fechaInicio= $this->post('fecha_inicio', '');
        $fechaFin   = $this->post('fecha_fin', '');
        $estado     = $this->post('estado', 'borrador');
        $descripcion= $this->post('descripcion', '');
        $requisitos = $this->post('requisitos', '');

        $errors = [];
        if (empty($nombre))    $errors[] = 'El nombre de la beca es requerido.';
        if (!$categoriaId)     $errors[] = 'Selecciona una categoría.';
        if (!$nivelId)         $errors[] = 'Selecciona un nivel educativo.';
        if ($monto <= 0)       $errors[] = 'El monto debe ser mayor a cero.';
        if (empty($fechaInicio))$errors[] = 'La fecha de inicio es requerida.';
        if (empty($fechaFin))  $errors[] = 'La fecha de cierre es requerida.';
        if ($fechaFin < $fechaInicio) $errors[] = 'La fecha de cierre debe ser posterior a la de inicio.';

        if ($errors) {
            return ['ok' => false, 'msg' => implode('<br>', $errors)];
        }

        return [
            'ok'             => true,
            'nombre'         => $nombre,
            'categoria_id'   => $categoriaId,
            'nivel_id'       => $nivelId,
            'monto'          => $monto,
            'tipo_monto'     => $tipoMonto,
            'promedio_minimo'=> $promedio ?: null,
            'cupo_maximo'    => $cupo ?: null,
            'fecha_inicio'   => $fechaInicio,
            'fecha_fin'      => $fechaFin,
            'estado'         => $estado,
            'descripcion'    => $descripcion,
            'requisitos'     => $requisitos,
        ];
    }
}
