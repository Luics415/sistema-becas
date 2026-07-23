<?php

require_once __DIR__ . '/Model.php';

class UsuarioModel extends Model
{
    protected string $table = 'usuarios';

    /** Encuentra usuario por email */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->query(
            'SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             JOIN roles r ON r.id = u.rol_id
             WHERE u.email = ? AND u.activo = 1
             LIMIT 1',
            [$email]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Verifica credenciales y retorna usuario o null */
    public function autenticar(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (!$user) return null;
        if (!password_verify($password, $user['password_hash'])) return null;
        // Actualizar último acceso
        $this->db->query(
            'UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?',
            [$user['id']]
        );
        return $user;
    }

    /** Crea un nuevo usuario alumno */
    public function crear(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        $this->db->query(
            'INSERT INTO usuarios (rol_id, nombre, apellidos, email, password_hash, curp, telefono, fecha_nacimiento, genero)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['rol_id']          ?? ROL_ALUMNO,
                $data['nombre'],
                $data['apellidos'],
                $data['email'],
                $hash,
                $data['curp']            ?? null,
                $data['telefono']        ?? null,
                $data['fecha_nacimiento']?? null,
                $data['genero']          ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Actualiza perfil básico (incluye CURP) */
    public function actualizarPerfil(int $id, array $data): bool
    {
        $stmt = $this->db->query(
            'UPDATE usuarios
             SET nombre = ?, apellidos = ?, curp = ?, telefono = ?, fecha_nacimiento = ?, genero = ?, avatar = ?
             WHERE id = ?',
            [
                $data['nombre'],
                $data['apellidos'],
                $data['curp']            ?? null,
                $data['telefono']        ?? null,
                $data['fecha_nacimiento']?? null,
                $data['genero']          ?? null,
                $data['avatar']          ?? null,
                $id,
            ]
        );
        return $stmt->rowCount() >= 0;
    }

    /** Cambia contraseña */
    public function cambiarPassword(int $id, string $nuevaPassword): bool
    {
        $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $this->db->query(
            'UPDATE usuarios SET password_hash = ? WHERE id = ?',
            [$hash, $id]
        );
        return $stmt->rowCount() > 0;
    }

    /** Verifica si el email ya existe */
    public function emailExiste(string $email, int $exceptoId = 0): bool
    {
        $stmt = $this->db->query(
            'SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?',
            [$email, $exceptoId]
        );
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Verifica si la CURP ya existe */
    public function curpExiste(string $curp, int $exceptoId = 0): bool
    {
        $stmt = $this->db->query(
            'SELECT COUNT(*) FROM usuarios WHERE curp = ? AND id != ?',
            [$curp, $exceptoId]
        );
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Lista paginada de alumnos para el admin */
    public function listarAlumnos(int $offset = 0, int $limit = ITEMS_PER_PAGE, string $busqueda = ''): array
    {
        $params = [ROL_ALUMNO];
        $where  = 'u.rol_id = ?';
        if ($busqueda) {
            $where .= ' AND (u.nombre LIKE ? OR u.apellidos LIKE ? OR u.email LIKE ?)';
            $like   = '%' . $busqueda . '%';
            $params = array_merge($params, [$like, $like, $like]);
        }
        $params[] = $limit;
        $params[] = $offset;
        return $this->db->query(
            "SELECT u.id, u.nombre, u.apellidos, u.email, u.curp, u.activo, u.created_at,
                    ia.promedio, ia.institucion, ia.carrera_o_programa,
                    (SELECT COUNT(*) FROM solicitudes s WHERE s.usuario_id = u.id) AS total_solicitudes
             FROM usuarios u
             LEFT JOIN informacion_academica ia ON ia.usuario_id = u.id
             WHERE {$where}
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    /** Total de alumnos (para paginación) */
    public function totalAlumnos(string $busqueda = ''): int
    {
        $params = [ROL_ALUMNO];
        $where  = 'rol_id = ?';
        if ($busqueda) {
            $where  .= ' AND (nombre LIKE ? OR apellidos LIKE ? OR email LIKE ?)';
            $like    = '%' . $busqueda . '%';
            $params  = array_merge($params, [$like, $like, $like]);
        }
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM usuarios WHERE {$where}",
            $params
        )->fetchColumn();
    }

    /** Activa o desactiva un usuario */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->query(
            'UPDATE usuarios SET activo = NOT activo WHERE id = ?',
            [$id]
        );
        return $stmt->rowCount() > 0;
    }

    /** Información académica del alumno */
    public function infoAcademica(int $usuarioId): ?array
    {
        $stmt = $this->db->query(
            'SELECT ia.*, n.nombre AS nivel_nombre
             FROM informacion_academica ia
             JOIN niveles_educativos n ON n.id = ia.nivel_id
             WHERE ia.usuario_id = ? LIMIT 1',
            [$usuarioId]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Guarda o actualiza la información académica */
    public function guardarInfoAcademica(int $usuarioId, array $data): bool
    {
        $existente = $this->infoAcademica($usuarioId);
        if ($existente) {
            $stmt = $this->db->query(
                'UPDATE informacion_academica
                 SET nivel_id = ?, institucion = ?, carrera_o_programa = ?,
                     semestre_o_grado = ?, matricula = ?, promedio = ?, ciclo_escolar = ?
                 WHERE usuario_id = ?',
                [
                    $data['nivel_id'],
                    $data['institucion'],
                    $data['carrera_o_programa'] ?? null,
                    $data['semestre_o_grado']   ?? null,
                    $data['matricula']           ?? null,
                    $data['promedio']            ?? null,
                    $data['ciclo_escolar']       ?? null,
                    $usuarioId,
                ]
            );
        } else {
            $stmt = $this->db->query(
                'INSERT INTO informacion_academica
                 (usuario_id, nivel_id, institucion, carrera_o_programa, semestre_o_grado, matricula, promedio, ciclo_escolar)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $usuarioId,
                    $data['nivel_id'],
                    $data['institucion'],
                    $data['carrera_o_programa'] ?? null,
                    $data['semestre_o_grado']   ?? null,
                    $data['matricula']           ?? null,
                    $data['promedio']            ?? null,
                    $data['ciclo_escolar']       ?? null,
                ]
            );
        }
        return $stmt->rowCount() > 0 || (bool) $this->db->lastInsertId();
    }
}
