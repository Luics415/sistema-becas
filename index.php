<?php
/**
 * ============================================================
 * Sistema Web para Registro y Administración de Becas Escolares
 * Punto de entrada principal – Front Controller (MVC)
 * ============================================================
 */

declare(strict_types=1);

// ── Carga de configuración ───────────────────────────────────
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Session.php';
require_once __DIR__ . '/includes/helpers.php';

// ── Manejo de errores según entorno ─────────────────────────
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ── Enrutador básico ─────────────────────────────────────────
$controllerName = strtolower($_GET['c'] ?? 'auth');
$actionName     = $_GET['a'] ?? '';

// Tabla de rutas: controller => [action => método, ...]
// Si la acción no está en la tabla, redirige al login
$routes = [
    'auth' => [
        'login'         => ['class' => 'AuthController',    'method' => 'login',    'post' => 'loginPost'],
        'registro'      => ['class' => 'AuthController',    'method' => 'registro', 'post' => 'registroPost'],
        'logout'        => ['class' => 'AuthController',    'method' => 'logout'],
        ''              => ['class' => 'AuthController',    'method' => 'login',    'post' => 'loginPost'],
    ],
    'admin' => [
        'dashboard'            => ['class' => 'AdminController', 'method' => 'dashboard'],
        'becas'                => ['class' => 'AdminController', 'method' => 'becas'],
        'becaDetalle'          => ['class' => 'AdminController', 'method' => 'becaDetalle'],
        'becaNueva'            => ['class' => 'AdminController', 'method' => 'becaNueva'],
        'becaCrear'            => ['class' => 'AdminController', 'method' => 'becaCrear'],
        'becaEditar'           => ['class' => 'AdminController', 'method' => 'becaEditar'],
        'becaActualizar'       => ['class' => 'AdminController', 'method' => 'becaActualizar'],
        'becaPublicar'         => ['class' => 'AdminController', 'method' => 'becaPublicar'],
        'becaCerrar'           => ['class' => 'AdminController', 'method' => 'becaCerrar'],
        'becaEliminar'         => ['class' => 'AdminController', 'method' => 'becaEliminar'],
        'solicitantes'         => ['class' => 'AdminController', 'method' => 'solicitantes'],
        'expediente'           => ['class' => 'AdminController', 'method' => 'expediente'],
        'solicitudes'          => ['class' => 'AdminController', 'method' => 'solicitudes'],
        'solicitudDetalle'     => ['class' => 'AdminController', 'method' => 'solicitudDetalle'],
        'solicitudCambiarEstado' => ['class' => 'AdminController', 'method' => 'solicitudCambiarEstado'],
        'solicitudEnviarMensaje' => ['class' => 'AdminController', 'method' => 'solicitudEnviarMensaje'],
        'documentos'           => ['class' => 'AdminController', 'method' => 'documentos'],
        'documentoValidar'     => ['class' => 'AdminController', 'method' => 'documentoValidar'],
        'documentoVisor'       => ['class' => 'AdminController', 'method' => 'documentoVisor'],
        'reportes'             => ['class' => 'AdminController', 'method' => 'reportes'],
        'exportarCSV'          => ['class' => 'AdminController', 'method' => 'exportarCSV'],
        'configuracion'        => ['class' => 'AdminController', 'method' => 'configuracion'],
        'guardarConfiguracion' => ['class' => 'AdminController', 'method' => 'guardarConfiguracion'],
        'guardarPerfil'        => ['class' => 'AdminController', 'method' => 'guardarPerfil'],
        'toggleUsuario'        => ['class' => 'AdminController', 'method' => 'toggleUsuario'],
        ''                     => ['class' => 'AdminController', 'method' => 'dashboard'],
    ],
    'student' => [
        'dashboard'         => ['class' => 'StudentController', 'method' => 'dashboard'],
        'convocatorias'     => ['class' => 'StudentController', 'method' => 'convocatorias'],
        'convocatoriaDetalle' => ['class' => 'StudentController', 'method' => 'convocatoriaDetalle'],
        'tramites'          => ['class' => 'StudentController', 'method' => 'tramites'],
        'tramiteDetalle'    => ['class' => 'StudentController', 'method' => 'tramiteDetalle'],
        'enviarMensaje'     => ['class' => 'StudentController', 'method' => 'enviarMensaje'],
        'registroPaso1'     => ['class' => 'StudentController', 'method' => 'registroPaso1'],
        'registroPaso1Post' => ['class' => 'StudentController', 'method' => 'registroPaso1Post'],
        'registroPaso2'     => ['class' => 'StudentController', 'method' => 'registroPaso2'],
        'registroPaso2Post' => ['class' => 'StudentController', 'method' => 'registroPaso2Post'],
        'registroResumen'   => ['class' => 'StudentController', 'method' => 'registroResumen'],
        'registroEnviar'    => ['class' => 'StudentController', 'method' => 'registroEnviar'],
        'confirmacion'      => ['class' => 'StudentController', 'method' => 'confirmacion'],
        'documentos'        => ['class' => 'StudentController', 'method' => 'documentos'],
        'subirDocumento'    => ['class' => 'StudentController', 'method' => 'subirDocumento'],
        'documentoVisor'    => ['class' => 'StudentController', 'method' => 'documentoVisor'],
        'perfil'            => ['class' => 'StudentController', 'method' => 'perfil'],
        'guardarPerfil'     => ['class' => 'StudentController', 'method' => 'guardarPerfil'],
        'consultaEstatus'   => ['class' => 'StudentController', 'method' => 'consultaEstatus'],
        ''                  => ['class' => 'StudentController', 'method' => 'dashboard'],
    ],
    'public' => [
        'consultaEstatus'   => ['class' => 'PublicController',  'method' => 'consultaEstatus'],
        ''                  => ['class' => 'PublicController',  'method' => 'consultaEstatus'],
    ],
];

// ── Resolver ruta ────────────────────────────────────────────
if (!isset($routes[$controllerName])) {
    // Si el controlador no existe, redirigir según sesión
    Session::start();
    if (Session::isLoggedIn()) {
        $url = Session::isAdmin()
            ? 'index.php?c=admin&a=dashboard'
            : 'index.php?c=student&a=dashboard';
    } else {
        $url = 'index.php?c=auth&a=login';
    }
    header('Location: ' . BASE_URL . '/' . $url);
    exit;
}

$controllerRoutes = $routes[$controllerName];
$route = $controllerRoutes[$actionName] ?? $controllerRoutes[''] ?? null;

if (!$route) {
    http_response_code(404);
    echo '<h1>Página no encontrada (404)</h1>';
    exit;
}

// ── Cargar clase del controlador ─────────────────────────────
$classFile = __DIR__ . '/controllers/' . $route['class'] . '.php';
if (!file_exists($classFile)) {
    http_response_code(500);
    echo '<h1>Error interno del servidor</h1>';
    exit;
}
require_once $classFile;

$controllerClass = $route['class'];
$controller      = new $controllerClass();

// ── Determinar método (GET vs POST) ──────────────────────────
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$method = ($isPost && isset($route['post'])) ? $route['post'] : $route['method'];

if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo '<h1>Acción no encontrada</h1>';
    exit;
}

// ── Ejecutar ─────────────────────────────────────────────────
$controller->$method();
