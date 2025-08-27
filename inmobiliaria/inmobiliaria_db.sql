-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 03:27:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inmobiliaria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sitio`
--

CREATE TABLE `configuracion_sitio` (
  `id` int(11) NOT NULL,
  `nombre_sitio` varchar(100) DEFAULT 'UTH SOLUTIONS REAL STATE',
  `color_esquema` enum('azul','amarillo','gris','blanco_gris') DEFAULT 'azul',
  `icono_principal` varchar(255) DEFAULT NULL,
  `icono_blanco` varchar(255) DEFAULT NULL,
  `imagen_banner` varchar(255) DEFAULT NULL,
  `mensaje_banner` text DEFAULT NULL,
  `titulo_quienes_somos` varchar(200) DEFAULT NULL,
  `descripcion_quienes_somos` text DEFAULT NULL,
  `imagen_quienes_somos` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `telefono_contacto` varchar(20) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `logo_navbar` varchar(255) DEFAULT NULL,
  `logo_footer` varchar(255) DEFAULT NULL,
  `banner_imagen` varchar(255) DEFAULT NULL,
  `color_primario` varchar(10) DEFAULT '#1a237e',
  `color_secundario` varchar(10) DEFAULT '#FFC107'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_sitio`
--

INSERT INTO `configuracion_sitio` (`id`, `nombre_sitio`, `color_esquema`, `icono_principal`, `icono_blanco`, `imagen_banner`, `mensaje_banner`, `titulo_quienes_somos`, `descripcion_quienes_somos`, `imagen_quienes_somos`, `facebook_url`, `youtube_url`, `instagram_url`, `direccion`, `telefono_contacto`, `email_contacto`, `fecha_actualizacion`, `logo_navbar`, `logo_footer`, `banner_imagen`, `color_primario`, `color_secundario`) VALUES
(1, 'UTN SOLUTIONS REAL STATE', 'azul', NULL, NULL, NULL, 'Permitenos ayudarte a cumplir tus sueños', 'QUIENES SOMOS', '', 'quienes_somos_1756253500.jpg', 'https://www.facebook.com/UniversidadTecnicaNacional', 'https://youtu.be/r7086iAv50Q?si=rnbgMpPOyX6uqIvo', '', 'Cañas Guanacaste, 100 mts Este Parque de Cañas', '88902030', 'info@utnrealstate.com', '2025-08-27 01:21:09', 'logo_navbar_1756255338.png', 'logo_footer_1756255619.png', 'banner_1756257669.jpg', '#45a1bf', '#21e881');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_propiedades`
--

CREATE TABLE `imagenes_propiedades` (
  `id` int(11) NOT NULL,
  `propiedad_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

CREATE TABLE `propiedades` (
  `id` int(11) NOT NULL,
  `tipo` enum('alquiler','venta') NOT NULL,
  `destacada` tinyint(1) DEFAULT 0,
  `titulo` varchar(200) NOT NULL,
  `descripcion_breve` text DEFAULT NULL,
  `descripcion_larga` text DEFAULT NULL,
  `precio` decimal(12,2) NOT NULL,
  `agente_id` int(11) DEFAULT NULL,
  `imagen_destacada` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `mapa` text DEFAULT NULL,
  `estado` enum('activa','inactiva') DEFAULT 'activa',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`id`, `tipo`, `destacada`, `titulo`, `descripcion_breve`, `descripcion_larga`, `precio`, `agente_id`, `imagen_destacada`, `ubicacion`, `mapa`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(4, 'venta', 1, 'Casa con vista panorámica', 'Casa con balcón y vista al valle, perfecta para relajarse.', 'Ubicada en una colina, ofrece vistas espectaculares. Tiene 2 habitaciones, 2 baños, cocina abierta, balcón panorámico y acceso privado.', 4000000.00, 2, 'property_1756253399_4206.jpg', 'Jaco', NULL, 'activa', '2025-08-27 00:09:59', '2025-08-27 00:29:37'),
(5, 'venta', 0, 'Residencia con jardín privado', 'Propiedad con jardín amplio y acabados modernos.', 'Casa de 250 m² con jardín frontal y trasero, 4 habitaciones, 3 baños, cochera para 2 vehículos y terraza techada. Ubicada en zona de alta plusvalía.', 3000000.00, 2, 'property_1756253450_5996.jpg', 'Escazu', NULL, 'activa', '2025-08-27 00:10:50', '2025-08-27 00:18:52'),
(6, 'venta', 1, 'Casa de playa con acceso directo al mar', 'Propiedad frente al mar con piscina y terraza.', '4 habitaciones, piscina privada, terraza con vista al océano y acceso directo a la playa. Ideal para inversión o residencia vacacional.', 6000000.00, 2, 'property_1756253597_1769.jpg', 'Tamarindo', NULL, 'activa', '2025-08-27 00:13:17', '2025-08-27 00:29:43'),
(7, 'alquiler', 1, 'Casa estilo colonial restaurada', 'Casa colonial con detalles en madera y techos altos.', '3 habitaciones, 2 baños, jardín interno, cochera y corredores amplios. Restaurada recientemente.', 400.00, 2, 'property_1756253828_8157.jpg', 'Carmona', NULL, 'activa', '2025-08-27 00:17:08', '2025-08-27 00:29:32'),
(8, 'venta', 1, 'Casa nueva en condominio cerrado', 'Casa moderna en condominio con seguridad y áreas comunes.', '180 m², 3 habitaciones, 2 baños, cocina moderna, cochera techada. Acceso a piscina y zonas verdes.', 600.00, 2, 'property_1756254050_3266.jpg', 'Conchal', NULL, 'activa', '2025-08-27 00:20:50', '2025-08-27 01:19:34'),
(9, 'alquiler', 0, 'Casa campestre con terreno amplio', 'Casa rural con terreno para cultivo y árboles frutales.', '1 hectárea, 3 habitaciones, 2 baños, cocina rústica, corredor amplio. Ideal para retiro o agricultura.', 800.00, 2, 'property_1756254119_5283.jpg', 'Monteverde', NULL, 'activa', '2025-08-27 00:21:59', '2025-08-27 00:24:06'),
(10, 'alquiler', 1, 'Casa compacta para pareja joven', 'Casa pequeña y funcional con patio trasero.', '1 habitación, 1 baño, cocina integrada, patio trasero. Ideal para parejas o personas solteras.', 200.00, 3, 'property_1756254464_3629.jpg', 'Cañas', NULL, 'activa', '2025-08-27 00:27:44', '2025-08-27 00:29:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `privilegio` enum('administrador','agente') DEFAULT 'agente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `telefono`, `correo`, `email`, `usuario`, `contrasena`, `privilegio`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administrador', '71270696', 'admin@uthsolutions.com', 'admin@uthsolutions.com', 'Admin', '$2y$10$Bl7S/tDHLDserA.5Jopg6OcH4BD1JVahPFxWET4PM.0OsaB.FTCm.', 'administrador', '2025-08-24 05:47:18', '2025-08-24 22:09:41'),
(2, 'Federico', '84096422', 'fede@gmail.com', 'fede@gmail.com', 'fede', '$2y$10$iXq/JzAjaGQYOD0MhZ9pTOLeamibhAXV./K0eWa4nARrM9sGrQtDO', 'agente', '2025-08-24 06:16:32', '2025-08-26 23:57:25'),
(3, 'Ibisay', '71270696', 'ibiM@gmail.com', 'ibiM@gmail.com', 'ibi', '$2y$10$W68Z6br.N2qzF6QlqEnNHePdgVbYofU/UBjNSxSFgferVz2A3U7NW', 'agente', '2025-08-27 00:26:04', '2025-08-27 00:26:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion_sitio`
--
ALTER TABLE `configuracion_sitio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propiedad_id` (`propiedad_id`);

--
-- Indices de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agente_id` (`agente_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion_sitio`
--
ALTER TABLE `configuracion_sitio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `imagenes_propiedades`
--
ALTER TABLE `imagenes_propiedades`
  ADD CONSTRAINT `imagenes_propiedades_ibfk_1` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
