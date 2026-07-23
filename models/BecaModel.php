<?php

require_once __DIR__ . '/Model.php';

class BecaModel extends Model
{
    protected string $table = 'becas';

    /** Lista becas con categoría y nivel (paginada) */
    public function listar(
        int $offset = 0,
        int $limit  = ITEMS_PER_PAGE,
        array $filtros = []
    ): array {
        [$where, $params] = $this->construirWhere($filtros);
        $params[] = $limit;
        $params[] = $offset;
        return $this->db->query(
            "SELECT b.*, c.nombre AS categoria_nombre, c.color_badge,
                    n.nombre AS nivel_nombre,
                    CONCAT(u.nombre, ' ', u.apellidos) AS creado_por_nombre,
                    (SELECT COUNT(*) FROM solicitudes s WHERE s.beca_id = b.id) AS total_solicitudes
             FROM becas b
             JOIN categorias_beca c    ON c.id = b.categoria_id
             JOIN niveles_educativos n ON n.id = b.nivel_id
             JOIN usuarios u           ON u.id = b.creado_por
             {$where}
             ORDER BY b.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    /** Total de becas filtradas */
    public function total(array $filtros = []): int
    {
        [$where, $params] = $this->construirWhere($filtros);
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM becas b {$where}",
            $params
        )->fetchColumn();
    }

    /** Detalle completo de una beca */
    public function detalle(int $id): ?array
    {
        $stmt = $this->db->query(
            "SELECT b.*, c.nombre AS categoria_nombre, c.color_badge, c.icono,
                    n.nombre AS nivel_nombre,
                    CONCAT(u.nombre, ' ', u.apellidos) AS creado_por_nombre
             FROM becas b
             JOIN categorias_beca c    ON c.id = b.categoria_id
             JOIN niveles_educativos n ON n.id = b.nivel_id
             JOIN usuarios u           ON u.id = b.creado_por
             WHERE b.id = ? LIMIT 1",
            [$id]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Becas publicadas (para el portal del alumno) */
    public function publicadas(array $filtros = []): array
    {
        $filtros['estado'] = 'publicada';
        return $this->listar(0, 100, $filtros);
    }

    /** Crea una nueva beca */
    public function crear(array $data): int
    {
        $this->db->query(
            'INSERT INTO becas
             (categoria_id, nivel_id, nombre, descripcion, monto, tipo_monto,
              promedio_minimo, cupo_maximo, fecha_inicio, fecha_fin, estado,
              requisitos, documentos_req, creado_por)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['categoria_id'],
                $data['nivel_id'],
                $data['nombre'],
                $data['descripcion']    ?? null,
                $data['monto'],
                $data['tipo_monto']     ?? 'mensual',
                $data['promedio_minimo']?? null,
                $data['cupo_maximo']    ?? null,
                $data['fecha_inicio'],
                $data['fecha_fin'],
                $data['estado']         ?? 'borrador',
                $data['requisitos']     ?? null,
                isset($data['documentos_req']) ? json_encode($data['documentos_req']) : null,
                $data['creado_por'],
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Actualiza una beca */
    public function actualizar(int $id, array $data): bool
    {
        $stmt = $this->db->query(
            'UPDATE becas
             SET categoria_id = ?, nivel_id = ?, nombre = ?, descripcion = ?,
                 monto = ?, tipo_monto = ?, promedio_minimo = ?, cupo_maximo = ?,
                 fecha_inicio = ?, fecha_fin = ?, estado = ?, requisitos = ?, documentos_req = ?
             WHERE id = ?',
            [
                $data['categoria_id'],
                $data['nivel_id'],
                $data['nombre'],
                $data['descripcion']    ?? null,
                $data['monto'],
                $data['tipo_monto']     ?? 'mensual',
                $data['promedio_minimo']?? null,
                $data['cupo_maximo']    ?? null,
                $data['fecha_inicio'],
                $data['fecha_fin'],
                $data['estado'],
                $data['requisitos']     ?? null,
                isset($data['documentos_req']) ? json_encode($data['documentos_req']) : null,
                $id,
            ]
        );
        return $stmt->rowCount() > 0;
    }

    /** Cambia el estado de una beca */
    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->db->query(
            'UPDATE becas SET estado = ? WHERE id = ?',
            [$estado, $id]
        );
        return $stmt->rowCount() > 0;
    }

    /** Categorías disponibles */
    public function categorias(): array
    {
        return $this->db->query('SELECT * FROM categorias_beca ORDER BY nombre')->fetchAll();
    }

    /** Niveles educativos */
    public function niveles(): array
    {
        return $this->db->query('SELECT * FROM niveles_educativos ORDER BY id')->fetchAll();
    }

    /** Estadísticas globales para dashboard */
    public function estadisticas(): array
    {
        $row = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(estado = 'publicada') AS publicadas,
                SUM(estado = 'cerrada') AS cerradas,
                SUM(estado = 'borrador') AS borradores,
                SUM(monto) AS presupuesto_total,
                (SELECT COUNT(*) FROM solicitudes WHERE estado = 'aprobada') AS solicitudes_aprobadas,
                (SELECT COUNT(*) FROM solicitudes WHERE estado = 'en_revision') AS solicitudes_revision,
                (SELECT COUNT(*) FROM solicitudes) AS solicitudes_total,
                (SELECT COUNT(DISTINCT usuario_id) FROM solicitudes WHERE estado = 'aprobada') AS beneficiarios
             FROM becas"
        )->fetch();
        return $row ?: [];
    }

    /** Distribución por categoría (para reportes) */
    public function distribucionPorCategoria(): array
    {
        return $this->db->query(
            "SELECT c.nombre AS categoria, c.color_badge,
                    COUNT(s.id) AS total_solicitudes,
                    SUM(s.estado = 'aprobada') AS aprobadas
             FROM categorias_beca c
             LEFT JOIN becas b ON b.categoria_id = c.id
             LEFT JOIN solicitudes s ON s.beca_id = b.id
             GROUP BY c.id
             ORDER BY total_solicitudes DESC"
        )->fetchAll();
    }

    /** Tendencia mensual de solicitudes (últimos 6 meses) */
    public function tendenciaMensual(): array
    {
        return $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes,
                    COUNT(*) AS total
             FROM solicitudes
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY mes
             ORDER BY mes ASC"
        )->fetchAll();
    }

    /** Construye cláusula WHERE dinámica */
    private function construirWhere(array $filtros): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filtros['estado'])) {
            $conditions[] = 'b.estado = ?';
            $params[]     = $filtros['estado'];
        }
        if (!empty($filtros['categoria_id'])) {
            $conditions[] = 'b.categoria_id = ?';
            $params[]     = $filtros['categoria_id'];
        }
        if (!empty($filtros['nivel_id'])) {
            $conditions[] = 'b.nivel_id = ?';
            $params[]     = $filtros['nivel_id'];
        }
        if (!empty($filtros['busqueda'])) {
            $conditions[] = '(b.nombre LIKE ? OR b.descripcion LIKE ?)';
            $like         = '%' . $filtros['busqueda'] . '%';
            $params[]     = $like;
            $params[]     = $like;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}
