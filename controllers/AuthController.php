<?php

require_once __DIR__ . '/Controller.php';
require_once dirname(__DIR__) . '/models/UsuarioModel.php';

class AuthController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new UsuarioModel();
    }

    /** GET /index.php?c=auth&a=login */
    public function login(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirigirPorRol();
        }
        $this->render('auth/login', [
            'title' => 'Iniciar Sesión - Sistema de Becas',
            'error' => Session::getFlash('error'),
        ]);
    }

    /** POST /index.php?c=auth&a=login */
    public function loginPost(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=auth&a=login');
        }

        $email    = $this->post('email', '');
        $password = $_POST['password'] ?? '';  // No limpiar password

        // Validación básica
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Por favor ingresa tu correo y contraseña.', 'danger');
            $this->redirect('index.php?c=auth&a=login');
        }

        $user = $this->usuarioModel->autenticar($email, $password);

        if (!$user) {
            Session::flash('error', 'Credenciales incorrectas. Por favor verifica tu correo y contraseña.', 'danger');
            $this->redirect('index.php?c=auth&a=login');
        }

        Session::login($user);
        $this->redirigirPorRol();
    }

    /** GET /index.php?c=auth&a=registro */
    public function registro(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirigirPorRol();
        }
        $this->render('auth/registro', [
            'title' => 'Registro de Alumno - Sistema de Becas',
            'error' => Session::getFlash('error'),
        ]);
    }

    /** POST /index.php?c=auth&a=registro */
    public function registroPost(): void
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?c=auth&a=registro');
        }

        $nombre    = $this->post('nombre', '');
        $apellidos = $this->post('apellidos', '');
        $email     = $this->post('email', '');
        $curp      = strtoupper($this->post('curp', ''));
        $password  = $_POST['password']          ?? '';
        $passwordC = $_POST['password_confirmar'] ?? '';
        $telefono  = $this->post('telefono', '');
        $genero    = $this->post('genero', '');

        $errors = [];
        if (empty($nombre))    $errors[] = 'El nombre es requerido.';
        if (empty($apellidos)) $errors[] = 'Los apellidos son requeridos.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El correo no es válido.';
        if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        if ($password !== $passwordC)  $errors[] = 'Las contraseñas no coinciden.';
        if ($curp && !validarCURP($curp)) $errors[] = 'El CURP no tiene el formato correcto.';
        if ($this->usuarioModel->emailExiste($email)) $errors[] = 'El correo ya está registrado.';
        if ($curp && $this->usuarioModel->curpExiste($curp)) $errors[] = 'El CURP ya está registrado.';

        if ($errors) {
            Session::flash('error', implode('<br>', $errors), 'danger');
            $this->redirect('index.php?c=auth&a=registro');
        }

        $id = $this->usuarioModel->crear([
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'password'  => $password,
            'curp'      => $curp ?: null,
            'telefono'  => $telefono ?: null,
            'genero'    => $genero ?: null,
            'rol_id'    => ROL_ALUMNO,
        ]);

        if ($id) {
            $user = $this->usuarioModel->find($id);
            Session::login($user);
            Session::flash('success', '¡Bienvenido! Tu cuenta ha sido creada correctamente.', 'success');
            $this->redirect('index.php?c=student&a=dashboard');
        } else {
            Session::flash('error', 'Ocurrió un error al crear la cuenta. Intenta de nuevo.', 'danger');
            $this->redirect('index.php?c=auth&a=registro');
        }
    }

    /** GET /index.php?c=auth&a=logout */
    public function logout(): void
    {
        Session::destroy();
        $this->redirect('index.php?c=auth&a=login');
    }

    // ── Privado ──────────────────────────────────────────────

    private function redirigirPorRol(): void
    {
        if (Session::isAdmin()) {
            $this->redirect('index.php?c=admin&a=dashboard');
        } else {
            $this->redirect('index.php?c=student&a=dashboard');
        }
    }
}
