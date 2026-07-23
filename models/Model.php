<?php
/**
 * Modelo base – todos los modelos extienden esta clase.
 */

require_once dirname(__DIR__) . '/includes/Database.php';

abstract class Model
{
    protected Database $db;
    protected string $table    = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Busca un registro por PK */
    public function find(int $id): ?array
    {
        $stmt = $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1",
            [$id]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Trae todos los registros (opcional ORDER BY) */
    public function all(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    /** Cuenta registros con condición opcional */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        return (int) $this->db->query($sql, $params)->fetchColumn();
    }

    /** Elimina por PK */
    public function delete(int $id): bool
    {
        $stmt = $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
        return $stmt->rowCount() > 0;
    }
}
