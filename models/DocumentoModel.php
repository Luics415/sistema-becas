<?php

require_once __DIR__ . '/Model.php';

class DocumentoModel extends Model
{
    protected string $table = 'documentos';

    /** Documentos de una solicitud */
    public function porSolicitud(int $solicitudId): array
    {
        return $this->db->query(
            "SELECT d.*, CONCAT(v.nombre, ' ', v.apellidos) AS validado_por_nombre
             FROM documentos d
             LEFT JOIN usuarios v ON v.id = d.validado_por
             WHERE d.solicitud_id = ?
             ORDER BY d.tipo_documento ASC",
            [$solicitudId]
        )->fetchAll();
    }

    /** Documentos de un usuario */
    public function porUsuario(int $usuarioId): array
    {
        return $this->db->query(
            "SELECT d.*, s.folio, b.nombre AS beca_nombre,
                    CONCAT(v.nombre, ' ', v.apellidos) AS validado_por_nombre
             FROM documentos d
             JOIN solicitudes s ON s.id = d.solicitud_id
             JOIN becas b ON b.id = s.beca_id
             LEFT JOIN usuarios v ON v.id = d.validado_por
             WHERE d.usuario_id = ?
             ORDER BY d.created_at DESC",
            [$usuarioId]
        )->fetchAll();
    }

    /** Sube (registra) un documento */
    public function subir(array $data): int
    {
        $this->db->query(
            'INSERT INTO documentos
             (solicitud_id, usuario_id, tipo_documento, nombre_archivo, ruta_archivo, mime_type, tamano_bytes)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['solicitud_id'],
                $data['usuario_id'],
                $data['tipo_documento'],
                $data['nombre_archivo'],
                $data['ruta_archivo'],
                $data['mime_type']     ?? null,
                $data['tamano_bytes']  ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Actualiza (reemplaza) un documento existente por tipo dentro de la solicitud */
    public function reemplazar(int $solicitudId, string $tipoDox, array $data): bool
    {
        $stmt = $this->db->query(
            "UPDATE documentos
             SET nombre_archivo = ?, ruta_archivo = ?, mime_type = ?,
                 tamano_bytes = ?, estado = 'pendiente', observaciones = NULL,
                 validado_por = NULL, fecha_validacion = NULL
             WHERE solicitud_id = ? AND tipo_documento = ?",
            [
                $data['nombre_archivo'],
                $data['ruta_archivo'],
                $data['mime_type']    ?? null,
                $data['tamano_bytes'] ?? null,
                $solicitudId,
                $tipoDox,
            ]
        );
        return $stmt->rowCount() > 0;
    }

    /** Admin: cambia el estado de un documento */
    public function validar(int $id, string $estado, int $adminId, string $obs = ''): bool
    {
        $stmt = $this->db->query(
            "UPDATE documentos
             SET estado = ?, validado_por = ?, fecha_validacion = NOW(), observaciones = ?
             WHERE id = ?",
            [$estado, $adminId, $obs ?: null, $id]
        );
        return $stmt->rowCount() > 0;
    }

    /** Resumen de estados de documentos para el admin dashboard */
    public function resumen(): array
    {
        return $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(estado = 'pendiente') AS pendientes,
                SUM(estado = 'en_revision') AS en_revision,
                SUM(estado = 'validado') AS validados,
                SUM(estado = 'rechazado') AS rechazados
             FROM documentos"
        )->fetch();
    }

    /** Lista paginada para gestión de expedientes (admin) */
    public function listarExpedientes(
        int $offset = 0,
        int $limit  = ITEMS_PER_PAGE,
        string $filtroEstado = '',
        string $busqueda = ''
    ): array {
        $where  = [];
        $params = [];

        if ($filtroEstado) {
            $where[]  = 'd.estado = ?';
            $params[] = $filtroEstado;
        }
        if ($busqueda) {
            $where[]  = '(u.nombre LIKE ? OR u.apellidos LIKE ? OR s.folio LIKE ?)';
            $like     = '%' . $busqueda . '%';
            $params   = array_merge($params, [$like, $like, $like]);
        }

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->query(
            "SELECT d.id, d.tipo_documento, d.nombre_archivo, d.estado, d.observaciones,
                    d.created_at, d.updated_at,
                    s.folio, s.id AS solicitud_id,
                    CONCAT(u.nombre, ' ', u.apellidos) AS alumno,
                    b.nombre AS beca_nombre
             FROM documentos d
             JOIN solicitudes s ON s.id = d.solicitud_id
             JOIN usuarios u ON u.id = d.usuario_id
             JOIN becas b ON b.id = s.beca_id
             {$whereStr}
             ORDER BY d.updated_at DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    public function totalExpedientes(string $filtroEstado = '', string $busqueda = ''): int
    {
        $where  = [];
        $params = [];
        if ($filtroEstado) {
            $where[]  = 'd.estado = ?';
            $params[] = $filtroEstado;
        }
        if ($busqueda) {
            $where[]  = '(u.nombre LIKE ? OR u.apellidos LIKE ? OR s.folio LIKE ?)';
            $like     = '%' . $busqueda . '%';
            $params   = array_merge($params, [$like, $like, $like]);
        }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM documentos d
             JOIN solicitudes s ON s.id = d.solicitud_id
             JOIN usuarios u ON u.id = d.usuario_id
             {$whereStr}",
            $params
        )->fetchColumn();
    }
}
