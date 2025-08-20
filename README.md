# Sistema de Gestión de la ESyA
## Escuela de Suboficiales y Agentes - Policía de Tucumán

### Descripción
Sistema integral de gestión para la Escuela de Suboficiales y Agentes de la Policía de Tucumán. Permite la administración completa de aspirantes, control de asistencias, gestión de materias y usuarios del sistema.

### Características Principales
- **Navegación Jerárquica**: Sistema de navegación intuitivo por divisiones
- **Gestión de Aspirantes**: Registro completo con datos personales y seguimiento académico
- **Control de Asistencia**: Registro diario por materia con observaciones
- **Administración de Materias**: Gestión de plan de estudios y profesores
- **Sistema de Usuarios**: Control de acceso con roles (Admin/Operador)
- **Divisiones ESyA**: Acceso específico por división (Jefatura de Cuerpo, Jefatura de Estudios, Servicios Médicos, Ayudantía)

### Requisitos del Sistema

#### Requisitos Previos
- **XAMPP** (Apache y MySQL)
  - Configurado para ejecutarse en:
    - Puerto **1443** para Apache (HTTPS)
    - Puerto **8012** para HTTP
    - Puerto **3306** para MySQL (por defecto)
- **Navegador Web** moderno
- **Git** para control de versiones
- **VSCode** (recomendado para desarrollo)

#### Configuración de XAMPP
1. Instalar XAMPP por defecto.

### Instalación

#### 1. Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd sis-esya
```

#### 2. Configurar Base de Datos
1. Iniciar Apache y MySQL en XAMPP
2. Acceder a phpMyAdmin: `http://localhost/phpmyadmin`
3. Crear base de datos `esyabd`
4. Importar el archivo `esyabd.sql`

#### 3. Configurar Aplicación
1. Verificar configuración en `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'esyabd');
   ```

2. Ajustar URL base si es necesario:
   ```php
   define('BASE_URL', 'http://localhost/sis-esya/');
   ```

#### 4. Acceder al Sistema
- URL principal: `http://localhost/sis-esya/`
- Usuario por defecto: `admin`
- Contraseña por defecto: `admin1234`

### Estructura de Navegación

```
Inicio
├── Formación de Agentes
│   ├── Sistema de Gestión de la ESyA
│   │   ├── Jefatura de Cuerpo → Login
│   │   ├── Jefatura de Estudios → Login
│   │   ├── Servicios Médicos → Login
│   │   └── Ayudantía → Login
│   └── Aula Virtual (En desarrollo)
└── División Capacitaciones (Próximamente)
```

### Estructura del Proyecto

```
├── assets/
│   ├── css/
│   │   ├── style.css          # Estilos principales
│   │   └── inicio.css         # Estilos para navegación
│   ├── js/
│   │   ├── script.js          # Funciones generales
│   │   └── usuarios.js        # Funciones específicas usuarios
│   └── img/                   # Imágenes del sistema
├── includes/
│   ├── config.php             # Configuración general
│   ├── database.php           # Conexión a BD
│   ├── auth.php               # Autenticación
│   ├── unified_header.php             # Header común
│   └── unified_footer.php             # Footer común
├── modules/
│   ├── aspirantes/            # Gestión de aspirantes
│   ├── asistencia/            # Control de asistencia
│   ├── materias/              # Gestión de materias
│   └── usuarios/              # Administración de usuarios
├── inicio.php                 # Página de inicio
├── formacion.php              # Formación de Agentes
├── esya.php                   # Divisiones ESyA
├── login.php                  # Autenticación
├── dashboard.php              # Panel principal
├── perfil.php                 # Gestión de perfil
└── logout.php                 # Cerrar sesión
```

### Funcionalidades por Módulo

#### Aspirantes
- Registro de nuevos aspirantes
- Edición de datos personales
- Control de estados (Activo/Inactivo/Graduado)
- Búsqueda avanzada
- Historial académico

#### Asistencia
- Registro diario por materia
- Observaciones personalizadas
- Reportes de asistencia
- Control por fecha y materia

#### Materias
- Gestión de plan de estudios
- Asignación de profesores
- Control de carga horaria
- Administración de horarios

#### Usuarios (Solo Administradores)
- Creación de nuevos usuarios
- Asignación de roles
- Gestión de permisos
- Control de sesiones

### Roles del Sistema

#### Administrador
- Acceso completo al sistema
- Gestión de usuarios
- Configuración general
- Reportes avanzados

#### Operador
- Gestión de aspirantes
- Control de asistencia
- Administración de materias
- Acceso limitado a configuración

### Seguridad

- **Autenticación**: Sistema de login seguro
- **Sesiones**: Control automático de expiración
- **Roles**: Permisos diferenciados por rol
- **SQL Injection**: Consultas preparadas
- **XSS**: Escape de datos de salida

### Desarrollo

#### Agregar Nuevos Módulos
1. Crear carpeta en `modules/`
2. Implementar CRUD básico
3. Integrar con navegación principal
4. Actualizar permisos según roles

#### Personalización de Estilos
- Modificar `assets/css/style.css` para estilos generales
- Usar `assets/css/inicio.css` para páginas de navegación
- Mantener consistencia con el diseño actual

### Soporte y Contacto

Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo de la Jefatura de Educación y Capacitación.

### Licencia

Sistema desarrollado específicamente para la Policía de Tucumán. Uso interno exclusivo.

---

**Versión**: 2.0  
**Última Actualización**: Agosto 2025  
**Estado**: En Desarrollo Activo