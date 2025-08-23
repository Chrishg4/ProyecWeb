-- Base de datos para el sistema inmobiliario
CREATE DATABASE IF NOT EXISTS inmobiliaria_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inmobiliaria_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    privilegio ENUM('administrador', 'agente') DEFAULT 'agente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de configuración del sitio
CREATE TABLE configuracion_sitio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_sitio VARCHAR(100) DEFAULT 'UTH SOLUTIONS REAL STATE',
    color_esquema ENUM('azul', 'amarillo', 'gris', 'blanco_gris') DEFAULT 'azul',
    icono_principal VARCHAR(255),
    icono_blanco VARCHAR(255),
    imagen_banner VARCHAR(255),
    mensaje_banner TEXT,
    titulo_quienes_somos VARCHAR(200),
    descripcion_quienes_somos TEXT,
    imagen_quienes_somos VARCHAR(255),
    facebook_url VARCHAR(255),
    youtube_url VARCHAR(255),
    instagram_url VARCHAR(255),
    direccion TEXT,
    telefono_contacto VARCHAR(20),
    email_contacto VARCHAR(100),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de propiedades
CREATE TABLE propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('alquiler', 'venta') NOT NULL,
    destacada BOOLEAN DEFAULT FALSE,
    titulo VARCHAR(200) NOT NULL,
    descripcion_breve TEXT,
    descripcion_larga TEXT,
    precio DECIMAL(12,2) NOT NULL,
    agente_id INT,
    imagen_destacada VARCHAR(255),
    ubicacion VARCHAR(255),
    mapa TEXT,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agente_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabla de imágenes adicionales de propiedades
CREATE TABLE imagenes_propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT,
    imagen VARCHAR(255) NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE
);

-- Insertar usuario administrador por defecto
-- Insertar usuario administrador con contraseña segura '123'
INSERT INTO usuarios (
  nombre, telefono, correo, email, usuario, contrasena, privilegio
) VALUES (
  'Administrador',
  '0000-0000',
  'admin@uthsolutions.com',
  'admin@uthsolutions.com',
  'Admin',
  '$2y$10$Bl7S/tDHLDserA.5Jopg6OcH4BD1JVahPFxWET4PM.0OsaB.FTCm.', -- bcrypt de '123'
  'administrador'
);

-- La contraseña es '123' encriptada

-- Insertar configuración inicial del sitio
INSERT INTO configuracion_sitio (
    nombre_sitio, 
    color_esquema, 
    mensaje_banner, 
    titulo_quienes_somos, 
    descripcion_quienes_somos,
    facebook_url,
    youtube_url,
    instagram_url,
    direccion,
    telefono_contacto,
    email_contacto
) VALUES (
    'UTH SOLUTIONS REAL STATE',
    'azul',
    'PERMÍTENOS AYUDARTE A CUMPLIR TUS SUEÑOS',
    'QUIENES SOMOS',
    'Curabitur congue eleifend orci, sit mollit tristram nec. Phasellus vestibulum nibh nisl. Donec eu viverdut nisl. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi pretium erat et orci vehicula, id fringilla lorem tempus. Pellentesque ex libero, luctus quis mauris congue sed vitae rutrum tellus. Pellentesque a mauris mauris, quis vestibulum metus at fringilla lorem orci et lacinia tellus. Pellentesque scelerisque erat at mauris lacinia rutrum.',
    'https://facebook.com',
    'https://youtube.com',
    'https://instagram.com',
    'Calles Guanacaste, 100 mts Este',
    '8800-2030',
    'info@uthrealestate.com'
);

-- Insertar algunas propiedades de ejemplo
INSERT INTO propiedades (tipo, destacada, titulo, descripcion_breve, descripcion_larga, precio, agente_id, ubicacion) VALUES 
('venta', TRUE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 65000.00, 1, 'Volcán Arenal'),
('venta', TRUE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 65000.00, 1, 'Volcán Arenal'),
('venta', TRUE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 65000.00, 1, 'Volcán Arenal'),
('venta', FALSE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 65000.00, 1, 'Volcán Arenal'),
('venta', FALSE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 65000.00, 1, 'Volcán Arenal'),
('venta', FALSE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 65000.00, 1, 'Volcán Arenal'),
('alquiler', FALSE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 800.00, 1, 'Volcán Arenal'),
('alquiler', FALSE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 800.00, 1, 'Volcán Arenal'),
('alquiler', FALSE, 'Casa Mora', 'Ubicada en las faldas del volcán Arenal, contiene 1000 mts de terreno y una de las mejores vistas del lugar.', 'Hermosa casa ubicada en las faldas del volcán Arenal con una vista espectacular. La propiedad cuenta con 1000 metros cuadrados de terreno, jardines amplios, 3 habitaciones, 2 baños, sala, comedor, cocina moderna y área de entretenimiento. Perfecta para familias que buscan tranquilidad y conexión con la naturaleza.', 800.00, 1, 'Volcán Arenal');
