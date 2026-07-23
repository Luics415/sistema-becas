<?php

require_once __DIR__ . '/Model.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

class SolicitudModel extends Model
{
    protected string $table = 'solicitudes';

    /** Solicitudes del alumno */
    public function porUsuario(int $usuarioId): array
    {
        return $this->db->query(
            "SELECT s.*, b.nombre AS beca_nombre, b.monto, b.tipo_monto,
                    c.nombre AS categoria, c.color_badge
             FROM solicitudes s
             JOIN becas b ON b.id = s.beca_id
             JOIN categorias_beca c ON c.id = b.categoria_id
             WHERE s.usuario_id = ?
             ORDER BY s.updated_at DESC",
            [$usuarioId]
        )->fetchAll();
    }

    /** Detalle de una solicitud (con info de alumno y beca) */
    public function detalle(int $id): ?array
    {
        $stmt = $this->db->query(
            "SELECT s.*,
                    CONCAT(u.nombre, ' ', u.apellidos) AS alumno_nombre,
                    u.email AS alumno_email, u.curp, u.telefono,
                    b.nombre AS beca_nombre, b.monto, b.tipo_monto, b.documentos_req,
                    c.nombre AS categoria, c.color_badge,
                    CONCAT(rev.nombre, ' ', rev.apellidos) AS revisor_nombre
             FROM solicitudes s
             JOIN usuarios u ON u.id = s.usuario_id
             JOIN becas b ON b.id = s.beca_id
             JOIN categorias_beca c ON c.id = b.categoria_id
             LEFT JOIN usuarios rev ON rev.id = s.revisado_por
             WHERE s.id = ? LIMIT 1",
            [$id]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Busca por folio (para consulta externa) */
    public function porFolio(string $folio): ?array
    {
        $stmt = $this->db->query(
            "SELECT s.*,
                    b.nombre AS beca_nombre, b.monto, b.tipo_monto,
                    c.nombre AS categoria
             FROM solicitudes s
             JOIN becas b ON b.id = s.beca_id
             JOIN categorias_beca c ON c.id = b.categoria_id
             WHERE s.folio = ? LIMIT 1",
            [strtoupper(trim($folio))]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Lista paginada para admin */
    public function listarAdmin(
        int $offset = 0,
        int $limit  = ITEMS_PER_PAGE,
        array $filtros = []
    ): array {
        [$where, $params] = $this->construirWhere($filtros);
        $params[] = $limit;
        $params[] = $offset;
        return $this->db->query(
            "SELECT s.id, s.folio, s.estado, s.paso_actual, s.fecha_envio, s.updated_at,
                    CONCAT(u.nombre, ' ', u.apellidos) AS alumno,
                    u.email AS alumno_email,
                    b.nombre AS beca, b.monto,
                    c.nombre AS categoria, c.color_badge,
                    (SELECT COUNT(*) FROM documentos d WHERE d.solicitud_id = s.id) AS total_docs,
                    (SELECT COUNT(*) FROM documentos d WHERE d.solicitud_id = s.id AND d.estado = 'validado') AS docs_ok
             FROM solicitudes s
             JOIN usuarios u ON u.id = s.usuario_id
             JOIN becas b ON b.id = s.beca_id
             JOIN categorias_beca c ON c.id = b.categoria_id
             {$where}
             ORDER BY s.updated_at DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    /** Total para paginación admin */
    public function totalAdmin(array $filtros = []): int
    {
        [$where, $params] = $this->construirWhere($filtros);
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM solicitudes s
             JOIN usuarios u ON u.id = s.usuario_id
             JOIN becas b ON b.id = s.beca_id
             {$where}",
            $params
        )->fetchColumn();
    }

    /** Inicia o recupera solicitud en borrador para un alumno+beca */
    public function iniciarORecuperar(int $usuarioId, int $becaId): array
    {
        // ¿Ya existe en borrador?
        $stmt = $this->db->query(
            "SELECT * FROM solicitudes WHERE usuario_id = ? AND beca_id = ? AND estado = 'borrador' LIMIT 1",
            [$usuarioId, $becaId]
        );
        $existente = $stmt->fetch();
        if ($existente) {
            return $existente;
        }
        // ¿Ya tiene solicitud aprobada o en revisión para esta beca?
        $stmt2 = $this->db->query(
            "SELECT id FROM solicitudes
             WHERE usuario_id = ? AND beca_id = ? AND estado IN ('enviada','en_revision','aprobada')
             LIMIT 1",
            [$usuarioId, $becaId]
        );
        if ($stmt2->fetchColumn()) {
            return ['error' => 'Ya tienes una solicitud activa para esta beca.'];
        }
        // Crear nueva
        $folio = $this->generarFolioUnico();
        $this->db->query(
            "INSERT INTO solicitudes (folio, usuario_id, beca_id, estado, paso_actual) VALUES (?, ?, ?, 'borrador', 1)",
            [$folio, $usuarioId, $becaId]
        );
        $id = (int) $this->db->lastInsertId();
        // Historial
        $this->registrarHistorial($id, null, 'borrador', 'Solicitud creada.', $usuarioId);
        return $this->find($id);
    }

    /** Avanza el paso del formulario */
    public function avanzarPaso(int $id, int $paso): bool
    {
        $stmt = $this->db->query(
            'UPDATE solicitudes SET paso_actual = ? WHERE id = ? AND estado = ?',
            [$paso, $id, 'borrador']
        );
        return $stmt->rowCount() > 0;
    }

    /** Envía la solicitud (cambia estado de borrador a enviada) */
    public function enviar(int $id, int $usuarioId): bool
    {
        $stmt = $this->db->query(
            "UPDATE solicitudes SET estado = 'enviada', fecha_envio = NOW() WHERE id = ? AND usuario_id = ? AND estado = 'borrador'",
            [$id, $usuarioId]
        );
        if ($stmt->rowCount() > 0) {
            $this->registrarHistorial($id, 'borrador', 'enviada', 'Solicitud enviada por el alumno.', $usuarioId);
            return true;
        }
        return false;
    }

    /** Admin: cambia el estado de la solicitud */
    public function cambiarEstado(
        int    $id,
        string $nuevoEstado,
        int    $adminId,
        string $comentario = ''
    ): bool {
        $actual = $this->find($id);
        if (!$actual) return false;
        $params = [$nuevoEstado, $adminId, $comentario ?: null];
        $set    = "estado = ?, revisado_por = ?, comentarios = ?";
        if (in_array($nuevoEstado, ['aprobada', 'rechazada'], true)) {
            $set   .= ", fecha_resolucion = NOW()";
        }
        if ($nuevoEstado === 'rechazada') {
            $set   .= ", motivo_rechazo = ?";
            $params[] = $comentario;
        }
        $params[] = $id;
        $stmt = $this->db->query("UPDATE solicitudes SET {$set} WHERE id = ?", $params);
        if ($stmt->rowCount() > 0) {
            $this->registrarHistorial($id, $actual['estado'], $nuevoEstado, $comentario, $adminId);
            return true;
        }
        return false;
    }

    /** Historial de estados de una solicitud */
    public function historial(int $solicitudId): array
    {
        return $this->db->query(
            "SELECT h.*, CONCAT(u.nombre, ' ', u.apellidos) AS actor
             FROM historial_estados h
             LEFT JOIN usuarios u ON u.id = h.cambiado_por
             WHERE h.solicitud_id = ?
             ORDER BY h.created_at ASC",
            [$solicitudId]
        )->fetchAll();
    }

    /** Mensajes de una solicitud */
    public function mensajes(int $solicitudId): array
    {
        return $this->db->query(
            "SELECT m.*, CONCAT(u.nombre, ' ', u.apellidos) AS remitente_nombre, u.rol_id AS remitente_rol
             FROM mensajes m
             JOIN usuarios u ON u.id = m.remitente_id
             WHERE m.solicitud_id = ?
             ORDER BY m.created_at ASC",
            [$solicitudId]
        )->fetchAll();
    }

    /** Agrega un mensaje */
    public function agregarMensaje(int $solicitudId, int $remitenteId, int $destinatarioId, string $cuerpo, string $asunto = ''): void
    {
        $this->db->query(
            'INSERT INTO mensajes (solicitud_id, remitente_id, destinatario_id, asunto, cuerpo) VALUES (?, ?, ?, ?, ?)',
            [$solicitudId, $remitenteId, $destinatarioId, $asunto ?: null, $cuerpo]
        );
    }

    /** Estadísticas rápidas para el dashboard del admin */
    public function resumenEstados(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(estado = 'enviada') AS enviadas,
                SUM(estado = 'en_revision') AS en_revision,
                SUM(estado = 'aprobada') AS aprobadas,
                SUM(estado = 'rechazada') AS rechazadas,
                SUM(estado = 'borrador') AS borradores
             FROM solicitudes"
        );
        return $stmt->fetch() ?: [];
    }

    /** Solicitudes recientes para dashboard */
    public function recientes(int $limite = 8): array
    {
        return $this->db->query(
            "SELECT s.id, s.folio, s.estado, s.fecha_envio,
                    CONCAT(u.nombre, ' ', u.apellidos) AS alumno,
                    b.nombre AS beca, c.color_badge
             FROM solicitudes s
             JOIN usuarios u ON u.id = s.usuario_id
             JOIN becas b ON b.id = s.beca_id
             JOIN categorias_beca c ON c.id = b.categoria_id
             ORDER BY s.updated_at DESC
             LIMIT ?",
            [$limite]
        )->fetchAll();
    }

    // ── Privados ─────────────────────────────────────────────

    private function generarFolioUnico(): string
    {
        do {
            $folio = generarFolio();
            $existe = $this->db->query(
                'SELECT COUNT(*) FROM solicitudes WHERE folio = ?',
                [$folio]
            )->fetchColumn();
        } while ($existe > 0);
        return $folio;
    }

    private function registrarHistorial(
        int $solicitudId, ?string $anterior, string $nuevo,
        string $comentario, int $usuarioId
    ): void {
        $this->db->query(
            'INSERT INTO historial_estados (solicitud_id, estado_anterior, estado_nuevo, comentario, cambiado_por)
             VALUES (?, ?, ?, ?, ?)',
            [$solicitudId, $anterior, $nuevo, $comentario ?: null, $usuarioId]
        );
    }

    private function construirWhere(array $filtros): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filtros['estado'])) {
            $conditions[] = 's.estado = ?';
            $params[]     = $filtros['estado'];
        }
        if (!empty($filtros['busqueda'])) {
            $conditions[] = '(s.folio LIKE ? OR u.nombre LIKE ? OR u.apellidos LIKE ? OR u.email LIKE ?)';
            $like         = '%' . $filtros['busqueda'] . '%';
            $params       = array_merge($params, [$like, $like, $like, $like]);
        }
        if (!empty($filtros['beca_id'])) {
            $conditions[] = 's.beca_id = ?';
            $params[]     = $filtros['beca_id'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}
