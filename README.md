# Sistema de Becas Escolares

Aplicación web en PHP con patrón MVC para gestionar convocatorias de becas, solicitudes de alumnos, validación documental y administración de usuarios.

## Descripción

Este proyecto permite:

- Registrar y administrar convocatorias o becas.
- Gestionar solicitudes de estudiantes.
- Subir y revisar documentos requeridos.
- Dar seguimiento al estado de cada trámite.
- Separar accesos entre administradores y alumnos.

## Tecnologías

- PHP 8+
- MySQL / MariaDB
- PDO para acceso a datos
- HTML, CSS y JavaScript
- Bootstrap (vistas y componentes visuales)
- Arquitectura MVC básica

## Estructura del proyecto

```text
├── assets/             # recursos estáticos (CSS, JS, uploads)
├── config/             # configuración de la app y base de datos
├── controllers/        # controladores MVC
├── includes/           # clases auxiliares y helpers
├── models/             # modelos de acceso a datos
├── views/              # plantillas y vistas por rol
├── index.php           # punto de entrada principal
├── database.sql        # script de creación y carga de datos
└── .htaccess           # reglas básicas de seguridad y Apache
```

## Requisitos

Antes de levantar el proyecto asegúrate de tener instalado:

- PHP 8 o superior
- Apache o Nginx con soporte PHP
- MySQL 5.7+ o MariaDB 10.4+
- XAMPP, WAMP o Laragon (recomendado para desarrollo local)

## Instalación y configuración

1. Clona o descarga el proyecto en tu servidor local.
2. Crea la base de datos `sistema_becas` en MySQL.
3. Importa el archivo `database.sql`.
4. Ajusta la configuración de conexión en:
   - `config/database.php`
5. Ajusta la URL base en:
   - `config/app.php`

### Configuración recomendada local

En `config/app.php`:

```php
define('BASE_URL', 'http://localhost/sistema_becas');
```

En `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_becas');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Ejecución

Coloca la carpeta del proyecto dentro de tu directorio web local, por ejemplo:

- `htdocs/sistema_becas` en XAMPP
- `www/sistema_becas` en WAMP

Luego abre en el navegador:

```text
http://localhost/sistema_becas/index.php
```

<img width="1178" height="792" alt="image" src="https://github.com/user-attachments/assets/cd377779-0f21-4bc9-b350-061ab1f3a1b3" />

## Acceso de prueba

El script SQL incluye usuarios de ejemplo. Puedes iniciar sesión con:

- Administrador: `admin@becas.edu.mx`
- Contraseña: `password`

También se incluyen alumnos de prueba con la misma contraseña.

## Flujo principal

- La aplicación usa `index.php` como front controller.
- El parámetro `c` indica el controlador.
- El parámetro `a` indica la acción.

Ejemplos:

```text
index.php?c=auth&a=login
index.php?c=admin&a=dashboard
index.php?c=student&a=convocatorias
```

## Roles del sistema

- `admin`: administración completa del sistema.
- `alumno`: registro, consulta y seguimiento de trámites.

<img width="1166" height="749" alt="image" src="https://github.com/user-attachments/assets/fff551f8-032e-49ea-9485-f261dbcbfaf0" />
<img width="1167" height="744" alt="image" src="https://github.com/user-attachments/assets/6e005a7e-ee64-47cb-980d-9c4ead0fc4d3" />


## Carpetas clave

- `controllers/`: lógica de cada módulo.
- `models/`: consultas y operaciones con la base de datos.
- `views/`: interfaz de usuario por rol.
- `includes/`: helpers, sesión y conexión singleton.

## Seguridad

El proyecto incluye reglas básicas con `.htaccess` para evitar acceso directo a carpetas internas y archivos sensibles como `.sql` y `.log`.

## Nota de desarrollo

Este proyecto está pensado como una solución MVC ligera para gestión escolar y becas, ideal para práctica, demostración o adaptación a un entorno real.
No es el producto final que fue entregado, ya que el uso y documentacion tiene informacion privada.


