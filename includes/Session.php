<?php
/**
 * Gestión de sesiones y autenticación
 */

require_once dirname(__DIR__) . '/config/app.php';

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!self::$started && session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => false,   // true en HTTPS
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
            self::$started = true;
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        self::$started = false;
    }

    // ── Métodos de autenticación ──────────────────────────────

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        self::set('user_id',   $user['id']);
        self::set('user_name', $user['nombre'] . ' ' . $user['apellidos']);
        self::set('user_email',$user['email']);
        self::set('user_rol',  (int)$user['rol_id']);
        self::set('user_avatar', $user['avatar'] ?? null);
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    public static function isAdmin(): bool
    {
        return self::get('user_rol') === ROL_ADMIN;
    }

    public static function isAlumno(): bool
    {
        return self::get('user_rol') === ROL_ALUMNO;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/index.php?c=auth&a=login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL . '/index.php?c=student&a=dashboard');
            exit;
        }
    }

    public static function requireAlumno(): void
    {
        self::requireLogin();
        if (!self::isAlumno()) {
            header('Location: ' . BASE_URL . '/index.php?c=admin&a=dashboard');
            exit;
        }
    }

    // ── Flash messages ───────────────────────────────────────

    public static function flash(string $key, string $message, string $type = 'success'): void
    {
        self::set('flash_' . $key, ['message' => $message, 'type' => $type]);
    }

    public static function getFlash(string $key): ?array
    {
        $flash = self::get('flash_' . $key);
        self::remove('flash_' . $key);
        return $flash;
    }
}
