<?php
/**
 * Controlador base
 */

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/Session.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

abstract class Controller
{
    protected array $data = [];

    public function __construct()
    {
        Session::start();
    }

    /**
     * Renderiza una vista pasando variables.
     * @param string $view  Ej: 'admin/dashboard'  →  views/admin/dashboard.php
     * @param array  $data  Variables que estarán disponibles en la vista
     */
    protected function render(string $view, array $data = []): void
    {
        $data = array_merge($this->data, $data);
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . "/views/{$view}.php";
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "<h1>Vista no encontrada: {$view}</h1>";
            return;
        }
        require $viewFile;
    }

    /** Renderiza un partial (parcial) sin layout */
    protected function renderPartial(string $partial, array $data = []): void
    {
        extract(array_merge($this->data, $data), EXTR_SKIP);
        $file = dirname(__DIR__) . "/includes/partials/{$partial}.php";
        if (file_exists($file)) {
            require $file;
        }
    }

    /** Redirige al path relativo */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /** Retorna respuesta JSON y termina */
    protected function json(bool $success, string $message, array $data = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
        exit;
    }

    /** Limpia string de entrada */
    protected function clean(string $input): string
    {
        return trim(strip_tags($input));
    }

    /** Obtiene valor POST */
    protected function post(string $key, $default = null)
    {
        return isset($_POST[$key]) ? $this->clean((string) $_POST[$key]) : $default;
    }

    /** Obtiene valor GET */
    protected function get(string $key, $default = null)
    {
        return isset($_GET[$key]) ? $this->clean((string) $_GET[$key]) : $default;
    }

    /** Verifica que la petición sea POST */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** Verifica petición AJAX */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
