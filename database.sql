-- ============================================================
-- Sistema Web para Registro y Administración de Becas Escolares
-- Base de Datos: sistema_becas
-- Motor: MySQL 5.7+ / MariaDB 10.4+
-- Charset: utf8mb4
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `sistema_becas`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sistema_becas`;

-- Eliminar tablas y vistas en orden inverso de dependencias
DROP VIEW  IF EXISTS `v_estadisticas_becas`;
DROP VIEW  IF EXISTS `v_solicitudes_resumen`;
DROP TABLE IF EXISTS `configuracion_sistema`;
DROP TABLE IF EXISTS `mensajes`;
DROP TABLE IF EXISTS `historial_estados`;
DROP TABLE IF EXISTS `documentos`;
DROP TABLE IF EXISTS `solicitudes`;
DROP TABLE IF EXISTS `informacion_academica`;
DROP TABLE IF EXISTS `becas`;
DROP TABLE IF EXISTS `niveles_educativos`;
DROP TABLE IF EXISTS `categorias_beca`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- TABLA: roles
-- ============================================================
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(50)      NOT NULL,
    `descripcion` VARCHAR(150)     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'admin',    'Administrador con acceso completo al sistema'),
(2, 'alumno',   'Alumno solicitante de beca');

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `rol_id`            TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `nombre`            VARCHAR(100)    NOT NULL,
    `apellidos`         VARCHAR(100)    NOT NULL,
    `email`             VARCHAR(150)    NOT NULL,
    `password_hash`     VARCHAR(255)    NOT NULL,
    `curp`              CHAR(18)        DEFAULT NULL,
    `telefono`          VARCHAR(20)     DEFAULT NULL,
    `fecha_nacimiento`  DATE            DEFAULT NULL,
    `genero`            ENUM('M','F','Otro') DEFAULT NULL,
    `activo`            TINYINT(1)      NOT NULL DEFAULT 1,
    `ultimo_acceso`     DATETIME        DEFAULT NULL,
    `token_reset`       VARCHAR(100)    DEFAULT NULL,
    `token_expira`      DATETIME        DEFAULT NULL,
    `avatar`            VARCHAR(255)    DEFAULT NULL,
    `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_email` (`email`),
    UNIQUE KEY `uq_usuarios_curp`  (`curp`),
    KEY `fk_usuarios_rol` (`rol_id`),
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5 usuarios de ejemplo (2 admins + 3 alumnos)
-- Contraseña para todos: password  (hash bcrypt de "password")
INSERT IGNORE INTO `usuarios` (`id`, `rol_id`, `nombre`, `apellidos`, `email`, `password_hash`, `curp`, `telefono`, `fecha_nacimiento`, `genero`, `activo`) VALUES
(1, 1, 'Sarah',     'Chen',           'admin@becas.edu.mx',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,                 '5555000001', '1985-03-12', 'F', 1),
(2, 1, 'Roberto',   'Díaz Morales',   'rdm@becas.edu.mx',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,                 '5555000002', '1980-07-22', 'M', 1),
(3, 2, 'Alejandro', 'Ruiz López',     'alejandro@alumno.edu',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RULA010215HMZZNL08', '5551234567', '2001-02-15', 'M', 1),
(4, 2, 'Ana',       'Martínez Soto',  'ana@alumno.edu',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'MASA020630MMZNTN09', '5557654321', '2002-06-30', 'F', 1),
(5, 2, 'Carlos',    'Ruiz Hernández', 'carlos@alumno.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RUHC031120HMZNRL05', '5559876543', '2003-11-20', 'M', 1);

-- ============================================================
-- TABLA: categorias_beca
-- ============================================================
CREATE TABLE IF NOT EXISTS `categorias_beca` (
    `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(80)      NOT NULL,
    `color_badge` VARCHAR(30)      DEFAULT 'primary',
    `icono`       VARCHAR(60)      DEFAULT 'bi-bookmark-star',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `categorias_beca` (`id`, `nombre`, `color_badge`, `icono`) VALUES
(1, 'Excelencia Académica', 'primary',   'bi-star-fill'),
(2, 'Apoyo Económico',      'success',   'bi-cash-coin'),
(3, 'Deportiva',            'warning',   'bi-trophy-fill'),
(4, 'Idiomas',              'info',      'bi-translate'),
(5, 'Movilidad',            'secondary', 'bi-globe2'),
(6, 'Ciencia y Tecnología', 'danger',    'bi-cpu-fill');

-- ============================================================
-- TABLA: niveles_educativos
-- ============================================================
CREATE TABLE IF NOT EXISTS `niveles_educativos` (
    `id`     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(80)      NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `niveles_educativos` (`id`, `nombre`) VALUES
(1, 'Preparatoria / Bachillerato'),
(2, 'Licenciatura'),
(3, 'Maestría'),
(4, 'Doctorado'),
(5, 'Técnico Superior Universitario');

-- ============================================================
-- TABLA: becas (convocatorias)
-- ============================================================
CREATE TABLE IF NOT EXISTS `becas` (
    `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `categoria_id`    TINYINT UNSIGNED NOT NULL,
    `nivel_id`        TINYINT UNSIGNED NOT NULL,
    `nombre`          VARCHAR(200)     NOT NULL,
    `descripcion`     TEXT             DEFAULT NULL,
    `monto`           DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
    `tipo_monto`      ENUM('mensual','semestral','anual','pago_unico') NOT NULL DEFAULT 'mensual',
    `promedio_minimo` DECIMAL(3,1)     DEFAULT NULL,
    `cupo_maximo`     SMALLINT UNSIGNED DEFAULT NULL,
    `fecha_inicio`    DATE             NOT NULL,
    `fecha_fin`       DATE             NOT NULL,
    `fecha_publicacion` DATE           DEFAULT NULL,
    `estado`          ENUM('borrador','publicada','cerrada','suspendida') NOT NULL DEFAULT 'borrador',
    `imagen_url`      VARCHAR(255)     DEFAULT NULL,
    `requisitos`      TEXT             DEFAULT NULL,
    `documentos_req`  TEXT             DEFAULT NULL COMMENT 'JSON array of required document types',
    `creado_por`      INT UNSIGNED     NOT NULL,
    `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_becas_categoria` (`categoria_id`),
    KEY `fk_becas_nivel`     (`nivel_id`),
    KEY `fk_becas_creador`   (`creado_por`),
    CONSTRAINT `fk_becas_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_beca` (`id`),
    CONSTRAINT `fk_becas_nivel`     FOREIGN KEY (`nivel_id`)     REFERENCES `niveles_educativos` (`id`),
    CONSTRAINT `fk_becas_creador`   FOREIGN KEY (`creado_por`)   REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10 becas de ejemplo
INSERT IGNORE INTO `becas` (`id`,`categoria_id`,`nivel_id`,`nombre`,`descripcion`,`monto`,`tipo_monto`,`promedio_minimo`,`cupo_maximo`,`fecha_inicio`,`fecha_fin`,`estado`,`requisitos`,`documentos_req`,`creado_por`) VALUES
(1,  1, 2, 'Beca de Excelencia Académica 2024',
    'Reconoce a los estudiantes con el promedio más alto de su generación.',
    5000.00, 'mensual', 9.0, 50, '2024-08-01', '2024-10-15', 'publicada',
    'Promedio mínimo 9.0, estar inscrito en el ciclo escolar vigente, no adeudar materias.',
    '["INE","CURP","Kardex","Comprobante_domicilio"]', 1),
(2,  3, 2, 'Talento Deportivo Elite',
    'Para estudiantes con destacada trayectoria deportiva de representación.',
    3500.00, 'mensual', 7.5, 20, '2024-09-01', '2024-11-22', 'publicada',
    'Promedio mínimo 7.5, carta de representación deportiva, no tener beca deportiva activa.',
    '["INE","CURP","Carta_deporte","Constancia_inscripcion"]', 1),
(3,  2, 2, 'Apoyo Alimenticio Universitario',
    'Subsidio para alimentación de estudiantes en situación de vulnerabilidad económica.',
    2000.00, 'mensual', 7.0, 100, '2024-09-15', '2024-12-05', 'publicada',
    'Promedio mínimo 7.0, estudio socioeconómico, no contar con otro apoyo alimenticio.',
    '["INE","CURP","Estudio_socioeconomico","Constancia_inscripcion"]', 1),
(4,  6, 2, 'Impulso Científico Joven',
    'Apoya proyectos de investigación científica y tecnológica estudiantil.',
    6000.00, 'mensual', 8.5, 30, '2024-08-20', '2024-10-20', 'publicada',
    'Promedio mínimo 8.5, carta de asesor, protocolo de investigación aprobado.',
    '["INE","CURP","Kardex","Protocolo_investigacion","Carta_asesor"]', 2),
(5,  4, 2, 'Perfeccionamiento de Idiomas',
    'Financiamiento para estudios de idiomas en instituciones certificadas.',
    4000.00, 'mensual', 8.0, 40, '2024-09-01', '2024-10-02', 'publicada',
    'Promedio mínimo 8.0, examen diagnóstico del idioma, carta de admisión al curso.',
    '["INE","CURP","Constancia_inscripcion","Carta_admision_idioma"]', 2),
(6,  5, 2, 'Prácticas Profesionales en el Extranjero',
    'Apoyo único para gastos de movilidad internacional en prácticas profesionales.',
    15000.00, 'pago_unico', 8.5, 15, '2024-10-01', '2025-01-10', 'publicada',
    'Promedio mínimo 8.5, carta de aceptación de empresa extranjera, pasaporte vigente.',
    '["INE","CURP","Pasaporte","Carta_aceptacion_empresa","Kardex"]', 1),
(7,  2, 1, 'Beca de Transporte Preparatoria',
    'Apoyo para gastos de transporte de alumnos de preparatoria foráneos.',
    1500.00, 'mensual', 7.0, 80, '2024-08-15', '2024-12-20', 'publicada',
    'Promedio mínimo 7.0, comprobante de domicilio fuera de la ciudad.',
    '["INE","CURP","Comprobante_domicilio","Constancia_inscripcion"]', 2),
(8,  2, 2, 'Apoyo a la Manutención',
    'Apoyo económico para estudiantes de licenciatura con bajos recursos.',
    2500.00, 'mensual', 7.5, 60, '2024-01-15', '2024-06-30', 'cerrada',
    'Promedio mínimo 7.5, estudio socioeconómico, constancia de ingresos familiares.',
    '["INE","CURP","Estudio_socioeconomico","Constancia_ingresos"]', 1),
(9,  1, 3, 'Excelencia en Posgrado',
    'Para estudiantes de maestría con publicaciones o ponencias en congresos.',
    8000.00, 'mensual', 9.0, 10, '2024-09-01', '2024-11-30', 'publicada',
    'Promedio mínimo 9.0, al menos una publicación indexada o ponencia en congreso.',
    '["INE","CURP","Kardex","Publicacion_o_ponencia","Carta_director"]', 2),
(10, 2, 2, 'Beca de Conectividad Digital',
    'Apoya a estudiantes sin acceso a equipo de cómputo o internet.',
    1800.00, 'mensual', 7.0, 120, '2023-08-01', '2024-01-31', 'cerrada',
    'Promedio mínimo 7.0, declaración de falta de equipo, estudio socioeconómico.',
    '["INE","CURP","Declaracion_conectividad","Estudio_socioeconomico"]', 1);

-- ============================================================
-- TABLA: informacion_academica
-- ============================================================
CREATE TABLE IF NOT EXISTS `informacion_academica` (
    `id`                  INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `usuario_id`          INT UNSIGNED     NOT NULL,
    `nivel_id`            TINYINT UNSIGNED NOT NULL,
    `institucion`         VARCHAR(200)     NOT NULL,
    `carrera_o_programa`  VARCHAR(200)     DEFAULT NULL,
    `semestre_o_grado`    TINYINT UNSIGNED DEFAULT NULL,
    `matricula`           VARCHAR(50)      DEFAULT NULL,
    `promedio`            DECIMAL(3,1)     DEFAULT NULL,
    `ciclo_escolar`       VARCHAR(30)      DEFAULT NULL,
    `created_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_info_academica_usuario` (`usuario_id`),
    CONSTRAINT `fk_info_academica_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_info_academica_nivel`   FOREIGN KEY (`nivel_id`)   REFERENCES `niveles_educativos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `informacion_academica` (`usuario_id`,`nivel_id`,`institucion`,`carrera_o_programa`,`semestre_o_grado`,`matricula`,`promedio`,`ciclo_escolar`) VALUES
(3, 2, 'Universidad Autónoma de México', 'Ingeniería en Sistemas Computacionales', 6, '20231001', 9.4, '2024-1'),
(4, 2, 'Instituto Tecnológico de México', 'Licenciatura en Administración', 4, '20232002', 8.7, '2024-1'),
(5, 1, 'Preparatoria Central', NULL, 3, '20233003', 8.2, '2024-1');

-- ============================================================
-- TABLA: solicitudes (aplicaciones a becas)
-- ============================================================
CREATE TABLE IF NOT EXISTS `solicitudes` (
    `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `folio`          VARCHAR(20)      NOT NULL,
    `usuario_id`     INT UNSIGNED     NOT NULL,
    `beca_id`        INT UNSIGNED     NOT NULL,
    `estado`         ENUM('borrador','enviada','en_revision','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'borrador',
    `paso_actual`    TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=Info personal, 2=Info académica, 3=Revisión',
    `comentarios`    TEXT             DEFAULT NULL COMMENT 'Comentarios del administrador',
    `motivo_rechazo` TEXT             DEFAULT NULL,
    `fecha_envio`    DATETIME         DEFAULT NULL,
    `fecha_resolucion` DATETIME       DEFAULT NULL,
    `revisado_por`   INT UNSIGNED     DEFAULT NULL,
    `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_solicitudes_folio` (`folio`),
    KEY `fk_solicitudes_usuario`  (`usuario_id`),
    KEY `fk_solicitudes_beca`     (`beca_id`),
    KEY `fk_solicitudes_revisor`  (`revisado_por`),
    CONSTRAINT `fk_solicitudes_usuario`  FOREIGN KEY (`usuario_id`)   REFERENCES `usuarios` (`id`),
    CONSTRAINT `fk_solicitudes_beca`     FOREIGN KEY (`beca_id`)      REFERENCES `becas` (`id`),
    CONSTRAINT `fk_solicitudes_revisor`  FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `solicitudes` (`id`,`folio`,`usuario_id`,`beca_id`,`estado`,`paso_actual`,`comentarios`,`fecha_envio`,`fecha_resolucion`,`revisado_por`) VALUES
(1, 'B-2024-0045', 3, 1, 'en_revision',  3, 'Expediente completo, pendiente de revisión de promedio.', '2024-08-10 10:30:00', NULL, 1),
(2, 'B-2024-0046', 3, 7, 'en_revision',  3, NULL, '2024-08-12 14:00:00', NULL, NULL),
(3, 'B-2023-0892', 4, 8, 'aprobada',     3, 'Todos los requisitos cumplidos, beca otorgada.', '2023-09-01 09:00:00', '2023-09-15 11:00:00', 1),
(4, 'B-2024-0012', 5, 7, 'borrador',     2, NULL, NULL, NULL, NULL),
(5, 'B-2023-1120', 4, 10,'aprobada',     3, 'Beca concluida en enero 2024.', '2023-08-05 08:00:00', '2023-08-20 10:00:00', 2);

-- ============================================================
-- TABLA: documentos
-- ============================================================
CREATE TABLE IF NOT EXISTS `documentos` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `solicitud_id`  INT UNSIGNED  NOT NULL,
    `usuario_id`    INT UNSIGNED  NOT NULL,
    `tipo_documento` VARCHAR(100) NOT NULL,
    `nombre_archivo` VARCHAR(255) NOT NULL,
    `ruta_archivo`  VARCHAR(500)  NOT NULL,
    `mime_type`     VARCHAR(100)  DEFAULT NULL,
    `tamano_bytes`  INT UNSIGNED  DEFAULT NULL,
    `estado`        ENUM('pendiente','en_revision','validado','rechazado') NOT NULL DEFAULT 'pendiente',
    `observaciones` TEXT          DEFAULT NULL,
    `validado_por`  INT UNSIGNED  DEFAULT NULL,
    `fecha_validacion` DATETIME   DEFAULT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_docs_solicitud`   (`solicitud_id`),
    KEY `fk_docs_usuario`     (`usuario_id`),
    KEY `fk_docs_validador`   (`validado_por`),
    CONSTRAINT `fk_docs_solicitud`  FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_docs_usuario`    FOREIGN KEY (`usuario_id`)   REFERENCES `usuarios` (`id`),
    CONSTRAINT `fk_docs_validador`  FOREIGN KEY (`validado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `documentos` (`solicitud_id`,`usuario_id`,`tipo_documento`,`nombre_archivo`,`ruta_archivo`,`mime_type`,`estado`,`validado_por`,`fecha_validacion`) VALUES
(1, 3, 'INE',                  'ine_alejandro.pdf',     'uploads/documentos/1/ine_alejandro.pdf',     'application/pdf', 'validado',    1, '2024-08-11 09:00:00'),
(1, 3, 'CURP',                 'curp_alejandro.pdf',    'uploads/documentos/1/curp_alejandro.pdf',    'application/pdf', 'validado',    1, '2024-08-11 09:05:00'),
(1, 3, 'Kardex',               'kardex_alejandro.pdf',  'uploads/documentos/1/kardex_alejandro.pdf',  'application/pdf', 'en_revision', NULL, NULL),
(1, 3, 'Comprobante_domicilio','domicilio_alejandro.pdf','uploads/documentos/1/domicilio_alejandro.pdf','application/pdf','pendiente',  NULL, NULL),
(3, 4, 'INE',                  'ine_ana.pdf',           'uploads/documentos/3/ine_ana.pdf',           'application/pdf', 'validado',    1, '2023-09-10 10:00:00'),
(3, 4, 'CURP',                 'curp_ana.pdf',          'uploads/documentos/3/curp_ana.pdf',          'application/pdf', 'validado',    1, '2023-09-10 10:05:00'),
(3, 4, 'Estudio_socioeconomico','estudio_ana.pdf',      'uploads/documentos/3/estudio_ana.pdf',       'application/pdf', 'validado',    1, '2023-09-10 10:10:00'),
(3, 4, 'Constancia_inscripcion','constancia_ana.pdf',   'uploads/documentos/3/constancia_ana.pdf',    'application/pdf', 'validado',    1, '2023-09-10 10:15:00');

-- ============================================================
-- TABLA: historial_estados (trazabilidad de cambios)
-- ============================================================
CREATE TABLE IF NOT EXISTS `historial_estados` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `solicitud_id` INT UNSIGNED NOT NULL,
    `estado_anterior` VARCHAR(50) DEFAULT NULL,
    `estado_nuevo`    VARCHAR(50) NOT NULL,
    `comentario`   TEXT         DEFAULT NULL,
    `cambiado_por` INT UNSIGNED DEFAULT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_historial_solicitud` (`solicitud_id`),
    KEY `fk_historial_usuario`   (`cambiado_por`),
    CONSTRAINT `fk_historial_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_historial_usuario`   FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `historial_estados` (`solicitud_id`,`estado_anterior`,`estado_nuevo`,`comentario`,`cambiado_por`) VALUES
(1, NULL,          'borrador',     'Solicitud creada.',                                  3),
(1, 'borrador',    'enviada',      'Solicitud enviada por el alumno.',                   3),
(1, 'enviada',     'en_revision',  'Expediente recibido, iniciando revisión.',           1),
(3, NULL,          'borrador',     'Solicitud creada.',                                  4),
(3, 'borrador',    'enviada',      'Solicitud enviada.',                                 4),
(3, 'enviada',     'en_revision',  'En revisión por administrador.',                     1),
(3, 'en_revision', 'aprobada',     'Todos los requisitos cumplidos, beca otorgada.',     1),
(5, NULL,          'borrador',     'Solicitud creada.',                                  4),
(5, 'borrador',    'enviada',      'Enviada.',                                           4),
(5, 'enviada',     'en_revision',  'En proceso.',                                        2),
(5, 'en_revision', 'aprobada',     'Aprobada. Beca finalizada en enero 2024.',           2);

-- ============================================================
-- TABLA: mensajes (comunicación admin-alumno)
-- ============================================================
CREATE TABLE IF NOT EXISTS `mensajes` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `solicitud_id` INT UNSIGNED NOT NULL,
    `remitente_id` INT UNSIGNED NOT NULL,
    `destinatario_id` INT UNSIGNED NOT NULL,
    `asunto`       VARCHAR(200) DEFAULT NULL,
    `cuerpo`       TEXT         NOT NULL,
    `leido`        TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_msg_solicitud`     (`solicitud_id`),
    KEY `fk_msg_remitente`     (`remitente_id`),
    KEY `fk_msg_destinatario`  (`destinatario_id`),
    CONSTRAINT `fk_msg_solicitud`     FOREIGN KEY (`solicitud_id`)    REFERENCES `solicitudes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_msg_remitente`     FOREIGN KEY (`remitente_id`)    REFERENCES `usuarios` (`id`),
    CONSTRAINT `fk_msg_destinatario`  FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `mensajes` (`solicitud_id`,`remitente_id`,`destinatario_id`,`asunto`,`cuerpo`,`leido`) VALUES
(1, 1, 3, 'Observación sobre tu expediente', 'Hola Alejandro, tu kardex requiere ser más legible. Por favor sube una versión con mejor resolución.', 0),
(1, 3, 1, 'Re: Observación sobre tu expediente', 'Gracias, subo el documento actualizado a la brevedad.', 1),
(3, 1, 4, 'Beca aprobada', 'Felicidades Ana, tu beca de manutención ha sido aprobada. Recibirás el depósito en los próximos 5 días hábiles.', 1);

-- ============================================================
-- TABLA: configuracion_sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS `configuracion_sistema` (
    `clave`     VARCHAR(80)  NOT NULL,
    `valor`     TEXT         DEFAULT NULL,
    `descripcion` VARCHAR(200) DEFAULT NULL,
    `grupo`     VARCHAR(50)  DEFAULT 'general',
    `updated_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `configuracion_sistema` (`clave`, `valor`, `descripcion`, `grupo`) VALUES
('nombre_sistema',       'Sistema de Becas Escolares',    'Nombre del sistema',                     'general'),
('institucion',          'Universidad Autónoma de México', 'Nombre de la institución educativa',     'general'),
('email_contacto',       'becas@institucion.edu.mx',      'Email de contacto institucional',         'general'),
('telefono_contacto',    '55 5555 0000',                  'Teléfono de contacto',                   'general'),
('zona_horaria',         'America/Mexico_City',            'Zona horaria del sistema',               'general'),
('modo_mantenimiento',   '0',                             '1 = activado, 0 = desactivado',          'sistema'),
('max_archivos_mb',      '5',                             'Tamaño máximo de archivos en MB',        'sistema'),
('tipos_archivos_ok',    'pdf,jpg,jpeg,png',              'Extensiones permitidas para documentos', 'sistema'),
('notif_email_admin',    '1',                             'Notificaciones email al admin',          'notificaciones'),
('notif_email_alumno',   '1',                             'Notificaciones email al alumno',         'notificaciones'),
('backup_auto',          '1',                             'Backup automático activado',             'backup'),
('version_sistema',      '1.0.0',                         'Versión del sistema',                    'general');

-- ============================================================
-- VISTAS ÚTILES
-- ============================================================

-- Vista resumen de solicitudes para admin
CREATE OR REPLACE VIEW `v_solicitudes_resumen` AS
SELECT
    s.id,
    s.folio,
    s.estado,
    s.paso_actual,
    s.fecha_envio,
    s.fecha_resolucion,
    s.created_at,
    CONCAT(u.nombre, ' ', u.apellidos) AS alumno,
    u.email AS alumno_email,
    b.nombre AS beca,
    b.monto,
    b.tipo_monto,
    c.nombre AS categoria,
    c.color_badge,
    CONCAT(rev.nombre, ' ', rev.apellidos) AS revisado_por_nombre,
    (SELECT COUNT(*) FROM documentos d WHERE d.solicitud_id = s.id) AS total_documentos,
    (SELECT COUNT(*) FROM documentos d WHERE d.solicitud_id = s.id AND d.estado = 'validado') AS docs_validados
FROM solicitudes s
JOIN usuarios u         ON u.id = s.usuario_id
JOIN becas b            ON b.id = s.beca_id
JOIN categorias_beca c  ON c.id = b.categoria_id
LEFT JOIN usuarios rev  ON rev.id = s.revisado_por;

-- Vista estadísticas de becas
CREATE OR REPLACE VIEW `v_estadisticas_becas` AS
SELECT
    b.id,
    b.nombre,
    c.nombre AS categoria,
    b.monto,
    b.estado AS estado_beca,
    b.fecha_fin,
    COUNT(s.id)                                             AS total_solicitudes,
    SUM(s.estado = 'aprobada')                              AS aprobadas,
    SUM(s.estado = 'rechazada')                             AS rechazadas,
    SUM(s.estado = 'en_revision')                           AS en_revision,
    SUM(s.estado = 'enviada')                               AS enviadas,
    SUM(s.estado = 'borrador')                              AS borradores,
    ROUND(SUM(s.estado='aprobada')/NULLIF(COUNT(s.id),0)*100,1) AS tasa_aprobacion
FROM becas b
JOIN categorias_beca c ON c.id = b.categoria_id
LEFT JOIN solicitudes s ON s.beca_id = b.id
GROUP BY b.id;
