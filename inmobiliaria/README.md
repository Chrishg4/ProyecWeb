# Sistema Inmobiliario UTH SOLUTIONS

## Instrucciones de Instalación

### 1. Requisitos Previos
- XAMPP o WAMP instalado
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

### 2. Instalación de la Base de Datos

1. Abre phpMyAdmin desde XAMPP
2. Crea una nueva base de datos llamada `inmobiliaria_db`
3. Importa el archivo `database.sql` o ejecuta el script SQL contenido en ese archivo

### 3. Configuración del Proyecto

1. Copia la carpeta `inmobiliaria` al directorio `htdocs` de XAMPP
2. Verifica que la estructura de carpetas sea correcta:
   ```
   htdocs/
   └── inmobiliaria/
       ├── css/
       ├── images/
       ├── admin/
       ├── index.php
       ├── conexion.php
       └── ...
   ```

### 4. Configuración de la Base de Datos

El archivo `conexion.php` ya está configurado para trabajar con:
- Host: localhost
- Usuario: root
- Contraseña: (vacía)
- Base de datos: inmobiliaria_db

Si necesitas cambiar estos valores, edita el archivo `conexion.php`.

### 5. Acceso al Sistema

1. Inicia XAMPP y activa Apache y MySQL
2. Abre tu navegador y visita: `http://localhost/inmobiliaria`
3. Para acceder al panel administrativo:
   - Usuario: Admin
   - Contraseña: 123

### 6. Funcionalidades Implementadas

#### Para Administradores:
- ✅ Personalizar página (colores, imágenes, mensajes)
- ✅ Gestionar usuarios (crear, editar, eliminar)
- ✅ Gestionar propiedades
- ✅ Actualizar datos personales
- ✅ Panel de estadísticas

#### Para Agentes de Ventas:
- ✅ Gestionar sus propiedades
- ✅ Actualizar datos personales
- ✅ Restricciones de acceso apropiadas

#### Para Visitantes:
- ✅ Ver propiedades destacadas, en venta y alquiler
- ✅ Búsqueda de propiedades
- ✅ Ver detalles completos de propiedades
- ✅ Formulario de contacto
- ✅ Diseño responsivo
- ✅ Enlaces a redes sociales

### 7. Características de Seguridad

- ✅ Contraseñas encriptadas con password_hash()
- ✅ Manejo de sesiones seguro
- ✅ Validación de formularios
- ✅ Sanitización de datos
- ✅ Protección contra inyección SQL (PDO preparadas)
- ✅ Control de acceso por roles

### 8. Estructura de la Base de Datos

#### Tablas principales:
- `usuarios` - Información de administradores y agentes
- `configuracion_sitio` - Configuración personalizable del sitio
- `propiedades` - Información de propiedades
- `imagenes_propiedades` - Imágenes adicionales de propiedades

### 9. Datos Precargados

El sistema incluye:
- Usuario administrador por defecto (Admin/123)
- Configuración inicial del sitio
- Propiedades de ejemplo

### 10. Personalización

Para personalizar el sitio:
1. Inicia sesión como administrador
2. Ve a "Personalizar Página"
3. Configura colores, mensajes, enlaces sociales y información de contacto

### 11. Agregar Imágenes

Para agregar imágenes de propiedades:
1. Sube las imágenes a la carpeta `images/`
2. En el panel de administración, especifica el nombre del archivo en el campo de imagen

### 12. Soporte y Mantenimiento

El sistema está completamente funcional y incluye todas las características solicitadas:
- Diseño fiel a la imagen proporcionada
- Funcionalidad completa de administración
- Sistema de roles y permisos
- Seguridad implementada
- Diseño responsivo

## ¡El sistema está listo para usar!

Visita `http://localhost/inmobiliaria` para comenzar.
