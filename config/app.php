<?php
/**
 * Configuración general de la aplicación
 * Sistema de Becas Escolares
 */

// ── Entorno ──────────────────────────────────────────────────
define('APP_ENV',  'development');   // 'production' en producción
define('APP_DEBUG', true);

// ── URL base ─────────────────────────────────────────────────
// Ajustar según nombre de carpeta en htdocs/
define('BASE_URL',  'http://localhost/sistema_becas');
define('BASE_PATH', dirname(__DIR__));   // ruta absoluta al proyecto

// ── Sesión ───────────────────────────────────────────────────
define('SESSION_NAME',    'sb_session');
define('SESSION_LIFETIME', 3600);        // 1 hora en segundos

// ── Rutas de almacenamiento ───────────────────────────────────
define('UPLOAD_PATH', BASE_PATH . '/assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5 MB
define('ALLOWED_EXTS', ['pdf', 'jpg', 'jpeg', 'png']);

// ── Roles ────────────────────────────────────────────────────
define('ROL_ADMIN',  1);
define('ROL_ALUMNO', 2);

// ── Paginación ───────────────────────────────────────────────
define('ITEMS_PER_PAGE', 10);

// ── Zona horaria ─────────────────────────────────────────────
date_default_timezone_set('America/Mexico_City');
