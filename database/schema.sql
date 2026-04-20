-- ============================================================
-- Sistema de Solicitudes Administrativas - UCT
-- SQL Server 2019
-- ============================================================

USE master;
GO

IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'prueba_tecnica')
BEGIN
    CREATE DATABASE prueba_tecnica;
END
GO

USE prueba_tecnica;
GO

-- ============================================================
-- Tabla: roles
-- ============================================================
IF OBJECT_ID('roles', 'U') IS NOT NULL DROP TABLE roles;
GO

CREATE TABLE roles (
    id_roles  INT           NOT NULL IDENTITY(1,1),
    nombre    VARCHAR(255)  NOT NULL,
    activo    CHAR(1)       NOT NULL,
    CONSTRAINT PK_roles PRIMARY KEY (id_roles)
);
GO

-- ============================================================
-- Tabla: modulos
-- ============================================================
IF OBJECT_ID('modulos', 'U') IS NOT NULL DROP TABLE modulos;
GO

CREATE TABLE modulos (
    id_modulo   INT           NOT NULL IDENTITY(1,1),
    nombre      VARCHAR(100)  NOT NULL,
    descripcion VARCHAR(255)  NULL,
    activo      CHAR(1)       NOT NULL,
    CONSTRAINT PK_modulos PRIMARY KEY (id_modulo)
);
GO

-- ============================================================
-- Tabla: usuarios
-- ============================================================
IF OBJECT_ID('usuarios', 'U') IS NOT NULL DROP TABLE usuarios;
GO

CREATE TABLE usuarios (
    id_usuario        INT           NOT NULL IDENTITY(1,1),
    nombre            VARCHAR(255)  NOT NULL,
    apellido_paterno  VARCHAR(255)  NOT NULL,
    apellido_materno  VARCHAR(255)  NULL,
    correo            VARCHAR(255)  NOT NULL,
    password          VARCHAR(255)  NOT NULL,
    activo            CHAR(1)       NOT NULL,
    fecha_creacion    DATETIME      NOT NULL,
    CONSTRAINT PK_usuarios PRIMARY KEY (id_usuario),
    CONSTRAINT UQ_usuarios_correo UNIQUE (correo)
);
GO

-- ============================================================
-- Tabla: usuarios_roles
-- ============================================================
IF OBJECT_ID('usuarios_roles', 'U') IS NOT NULL DROP TABLE usuarios_roles;
GO

CREATE TABLE usuarios_roles (
    id_usuario  INT  NOT NULL,
    id_roles    INT  NOT NULL,
    CONSTRAINT PK_usuarios_roles PRIMARY KEY (id_usuario, id_roles),
    CONSTRAINT FK_usuarios_roles_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    CONSTRAINT FK_usuarios_roles_rol     FOREIGN KEY (id_roles)   REFERENCES roles(id_roles)
);
GO

-- ============================================================
-- Tabla: roles_modulos
-- ============================================================
IF OBJECT_ID('roles_modulos', 'U') IS NOT NULL DROP TABLE roles_modulos;
GO

CREATE TABLE roles_modulos (
    id_roles        INT     NOT NULL,
    id_modulo       INT     NOT NULL,
    puede_ver       CHAR(1) NOT NULL,
    puede_crear     CHAR(1) NOT NULL,
    puede_editar    CHAR(1) NOT NULL,
    puede_eliminar  CHAR(1) NOT NULL,
    CONSTRAINT PK_roles_modulos PRIMARY KEY (id_roles, id_modulo),
    CONSTRAINT FK_roles_modulos_rol    FOREIGN KEY (id_roles)  REFERENCES roles(id_roles),
    CONSTRAINT FK_roles_modulos_modulo FOREIGN KEY (id_modulo) REFERENCES modulos(id_modulo)
);
GO

-- ============================================================
-- Tabla: tipo_solicitud
-- ============================================================
IF OBJECT_ID('tipo_solicitud', 'U') IS NOT NULL DROP TABLE tipo_solicitud;
GO

CREATE TABLE tipo_solicitud (
    id_tiso  INT           NOT NULL IDENTITY(1,1),
    nombre   VARCHAR(100)  NOT NULL,
    activo   CHAR(1)       NOT NULL,
    CONSTRAINT PK_tipo_solicitud PRIMARY KEY (id_tiso)
);
GO

-- ============================================================
-- Tabla: solicitudes
-- ============================================================
IF OBJECT_ID('solicitudes', 'U') IS NOT NULL DROP TABLE solicitudes;
GO

CREATE TABLE solicitudes (
    id_solicitud        INT           NOT NULL IDENTITY(1,1),
    id_usuario          INT           NOT NULL,
    id_tiso             INT           NOT NULL,
    descripcion         VARCHAR(MAX)  NOT NULL,
    estado              VARCHAR(20)   NOT NULL,
    fecha_creacion      DATETIME      NOT NULL,
    fecha_actualizacion DATETIME      NOT NULL,
    CONSTRAINT PK_solicitudes    PRIMARY KEY (id_solicitud),
    CONSTRAINT FK_solic_usuario  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    CONSTRAINT FK_solic_tipo     FOREIGN KEY (id_tiso)    REFERENCES tipo_solicitud(id_tiso),
    CONSTRAINT CK_solic_estado   CHECK (estado IN ('Pendiente','En revisión','Aprobada','Rechazada'))
);
GO

-- ============================================================
-- Datos iniciales
-- ============================================================

INSERT INTO roles (nombre, activo) VALUES
    ('Administrador', '1'),
    ('Usuario Interno', '1');
GO

INSERT INTO modulos (nombre, descripcion, activo) VALUES
    ('solicitudes', 'Gestión de solicitudes administrativas', '1');
GO

-- Administrador: acceso total
INSERT INTO roles_modulos (id_roles, id_modulo, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
    (1, 1, '1', '1', '1', '1');

-- Usuario Interno: solo ver y crear
INSERT INTO roles_modulos (id_roles, id_modulo, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
    (2, 1, '1', '1', '0', '0');
GO

INSERT INTO tipo_solicitud (nombre, activo) VALUES
    ('Solicitud Académica', '1'),
    ('Certificado', '1'),
    ('Actualización de Datos', '1'),
    ('Otra', '1');
GO
-- ============================================================
-- Usuarios de prueba
-- ============================================================

INSERT INTO usuarios (
    nombre,
    apellido_paterno,
    apellido_materno,
    correo,
    password,
    activo,
    fecha_creacion
)
VALUES
-- Usuarios internos (rol 2)
('Juan', 'Pérez', 'González', 'juan@uct.cl', '1234', '1', GETDATE()),
('María', 'Muñoz', 'Rojas', 'maria@uct.cl', '1234', '1', GETDATE()),

-- Administrador (rol 1)
('Admin', 'Sistema', NULL, 'admin@uct.cl', '1234', '1', GETDATE());
GO


-- ============================================================
-- Asignación de roles
-- ============================================================

INSERT INTO usuarios_roles (id_usuario, id_roles)
VALUES
-- Usuarios internos (rol 2)
(1, 2),
(2, 2),

-- Admin (rol 1)
(3, 1);
GO

PRINT 'Base de datos prueba_tecnica creada exitosamente.';
GO