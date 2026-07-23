<?php
/**
 * Clase Database – Singleton PDO
 * Proporciona la única instancia de conexión a MySQL.
 */

require_once dirname(__DIR__) . '/config/database.php';

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En producción loguear y mostrar mensaje genérico
            error_log('DB Connection error: ' . $e->getMessage());
            die(json_encode([
                'success' => false,
                'message' => 'Error de conexión a la base de datos. Por favor contacte al administrador.'
            ]));
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /** Shortcut: prepara y ejecuta una sentencia, retorna el PDOStatement */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Última ID insertada */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    // Evitar clonar/deserializar
    private function __clone() {}
    public function __wakeup() { throw new \Exception('Cannot unserialize singleton.'); }
}
