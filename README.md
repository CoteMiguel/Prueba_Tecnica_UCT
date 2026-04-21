# Sistema de Solicitudes - UCT

##  Propósito: 
Este proyecto corresponde a la Prueba Técnica – Desarrollador/a de la Dirección de Informática de la Universidad. Su objetivo es implementar un módulo básico para gestionar solicitudes administrativas internas.

## Tabla de Contenidos

- [Requisitos Previos](#-requisitos-previos)
- [Instalación](#-instalación)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Funcionalidades](#-funcionalidades)
- [Usuarios de Prueba](#-usuarios-de-prueba)
- [Decisiones Técnicas](#-decisiones-técnicas)

---

## Requisitos Previos

| Componente | Versión Recomendada |
|-----------|---------------------|
| PHP | 8.1 o superior |
| SQL Server | 2019 o superior |
| Servidor Web | Apache 2.4+ (XAMPP recomendado) |
| Extensión PHP | `sqlsrv` y `pdo_sqlsrv` |

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/TU_USUARIO/Prueba_Tecnica_UCT.git
cd Prueba_Tecnica_UCT
```

### 2. Configurar la base de datos

#### 2.1. Crear usuario SQL Server

Conectarse con el usuario `sa` y ejecutar:

```sql
USE master;
GO

CREATE LOGIN UCT WITH PASSWORD = 'PWD1234-';
GO

CREATE USER UCT FOR LOGIN UCT;
GO
```

#### 2.2. Ejecutar el script de base de datos

```bash
sqlcmd -S localhost -U sa -P TU_PASSWORD -i database/schema.sql
```

O desde SQL Server Management Studio (SSMS):
1. Abrir `database/schema.sql`
2. Ejecutar (F5)

Esto creará:
- Base de datos `prueba_tecnica`
- 7 tablas con relaciones
- Datos iniciales (roles, módulos, tipos de solicitud)
- 3 usuarios de prueba

### 3. Configurar la aplicación

Editar `backend/config/conexion.php` si es necesario:

```php
$serverName = "localhost";
$conexion = [
    "Database" => "prueba_tecnica",
    "UID" => "UCT",
    "PWD" => "PWD1234-",
    "CharacterSet" => "UTF-8"
];
```

También se pueden usar variables de entorno.

### 4. Configurar Apache (XAMPP)

Copiar el proyecto a `C:\xampp\htdocs\Prueba_Tecnica_UCT`

Acceder desde el navegador:
```
http://localhost/Prueba_Tecnica_UCT/frontend/index.html
```

---

## Estructura del Proyecto

```
Prueba_Tecnica_UCT/
├── backend/
│   ├── config/
│   │   └── conexion.php              # Conexión a SQL Server
│   ├── controllers/
│   │   ├── AuthController.php        # Autenticación y sesiones
│   │   ├── SolicitudController.php   # CRUD de solicitudes
│   │   └── TipoSolicitudController.php
│   ├── models/
│   │   ├── Usuario.php               # Modelo de usuarios
│   │   ├── Solicitud.php             # Modelo de solicitudes
│   │   └── Tipo_Solicitud.php
│   ├── index.php                     # Router principal
│   └── session.php                   # Validación de sesiones
├── frontend/
│   ├── index.html                    # Página de login
│   ├── home.html                     # Dashboard principal
│   ├── js/
│   │   ├── main.js                   # Lógica de login
│   │   └── home.js                   # Lógica del dashboard
│   └── styles/
│       └── style.css                 # Estilos personalizados
├── database/
│   └── schema.sql                    # Script DDL completo
└── README.md
```

---
### Endpoints de Solicitudes

#### `GET ?action=listarSolicitudes`
Obtiene todas las solicitudes.

**Respuesta:**
```json
[
  {
    "id_solicitud": 1,
    "nombre_solicitante": "Juan Pérez González",
    "correo": "juan@uct.cl",
    "tipo_solicitud": "Solicitud Académica",
    "descripcion": "Cambio de sección...",
    "estado": "Pendiente",
    "fecha_creacion": "2026-04-20 13:25:00",
    "fecha_actualizacion": "2026-04-20 13:25:00"
  }
]
```

---

#### `POST ?action=filtrarSolicitudes`
Filtra solicitudes por criterios.

**Body (JSON):**
```json
{
  "buscar": "Juan",
  "estado": "Pendiente",
  "tipo": "1"
}
```

---

#### `POST ?action=crearSolicitud`
Crea una nueva solicitud.

**Body (JSON):**
```json
{
  "solicitanteId": 1,
  "tipo": 1,
  "descripcion": "Solicito certificado de alumno regular",
  "estado": "Pendiente"
}
```

**Respuesta:**
```json
{
  "success": true,
  "msg": "Solicitud creada"
}
```

---

#### `POST ?action=actualizarSolicitud`
Actualiza una solicitud existente (requiere permisos de edición).

**Body (JSON):**
```json
{
  "id_solicitud": 1,
  "tipo": 2,
  "descripcion": "Descripción actualizada",
  "estado": "En revisión"
}
```

---

#### `GET ?action=listarTipos`
Obtiene el catálogo de tipos de solicitud.

**Respuesta:**
```json
[
  { "id_tiso": 1, "nombre": "Solicitud Académica" },
  { "id_tiso": 2, "nombre": "Certificado" },
  { "id_tiso": 3, "nombre": "Actualización de Datos" },
  { "id_tiso": 4, "nombre": "Otra" }
]
```

---

## Funcionalidades

### Sistema de Login
- Validación de credenciales contra SQL Server
- Creación de sesión PHP con datos del usuario y permisos
- Redirección automática al dashboard

### Dashboard Principal
- **Listado de solicitudes** con información completa
- **Búsqueda en tiempo real** por nombre o correo
- **Filtros** por estado y tipo de solicitud
- **Tabla responsive** con Bootstrap

### Gestión de Solicitudes
- Creación de nuevas solicitudes (solo si el usuario tiene permiso de creación)
- Cambio de estado (solo si el usuario tiene permisos de edición)
- Estados disponibles: Pendiente, En revisión, Aprobada, Rechazada

### Control de Acceso
- Validación de permisos en backend antes de cada operación
- Interfaz adaptativa según rol del usuario
- Botones de edición visibles solo para administradores

---

## Usuarios de Prueba

El script `database/schema.sql` crea automáticamente 3 usuarios:

| Correo | Contraseña | Rol | Permisos |
|--------|-----------|-----|----------|
| `juan@uct.cl` | `1234` | Usuario Interno | Ver, Crear |
| `maria@uct.cl` | `1234` | Usuario Interno | Ver, Crear |
| `admin@uct.cl` | `1234` | Administrador | Ver, Crear, Editar, Eliminar |

---

## Decisiones Técnicas

### Backend

**PHP sin framework**
- Se optó por PHP puro para cumplir el enunciado sin dependencias externas
- Estructura MVC manual que mantiene separación de responsabilidades
- Mayor claridad del código para revisión técnica

**SQL Server como motor de base de datos**
- Elegido porque es el motor utilizado internamente en la institución
- Uso de la extensión `sqlsrv` nativa de Microsoft
- Queries parametrizadas para prevenir SQL injection

**Control de acceso basado en roles**
- Implementación de un sistema escalable de permisos
- Separación entre roles y permisos permite fácil extensión

**Validación en dos capas**
- Frontend: validación básica para experiencia de usuario
- Backend: validación completa antes de persistir 

**Sesiones PHP para autenticación**
- Almacenamiento de datos del usuario y permisos en `$_SESSION`
- Validación de permisos en cada endpoint sensible
- No se implementó JWT por simplicidad y alcance de la prueba

### Frontend

**Bootstrap 5.3 + JavaScript Vanilla**
- Bootstrap para layout responsivo sin escribir CSS extenso
- JavaScript puro sin frameworks para mantener simplicidad
- Fetch API para comunicación asíncrona con el backend

**Separación de archivos JS**
- `main.js` → lógica de login
- `home.js` → lógica del dashboard
- Evita mezclar responsabilidades en un solo archivo

**Diseño austero y funcional**
- Enfoque en usabilidad sobre estética elaborada
- Iconos de Bootstrap Icons para claridad visual
- Paleta de colores profesional (azul institucional)

### Base de Datos

**Estados como CHECK constraint**
- Los 4 estados posibles están definidos por el enunciado
- No requieren tabla separada al ser valores fijos
- CHECK constraint garantiza integridad de datos

**Separación de nombre y apellidos**
- Mejor normalización que `nombre_completo`
- Facilita ordenamiento y búsquedas por apellido
- `apellido_materno` es NULL porque no todas las personas lo tienen

**Uso de `CHAR(1)` para flags**
- Valores `'1'` y `'0'` en vez de `BIT`
- Facilita lectura directa de datos en consultas

**Uso de `DATETIME` en vez de `DATETIME2`**
- Compatible con versiones anteriores de SQL Server
- Suficiente precisión para este caso de uso

---

## Licencia

Este proyecto fue desarrollado como parte de una prueba técnica para la Universidad Católica de Temuco.

---

## Autor
José Martínez Jaque.
Postulante a Desarrollador - Dirección de Informática UCT