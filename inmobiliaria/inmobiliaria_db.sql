-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-08-2025 a las 10:11:41
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
  `banner_imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_sitio`
--

INSERT INTO `configuracion_sitio` (`id`, `nombre_sitio`, `color_esquema`, `icono_principal`, `icono_blanco`, `imagen_banner`, `mensaje_banner`, `titulo_quienes_somos`, `descripcion_quienes_somos`, `imagen_quienes_somos`, `facebook_url`, `youtube_url`, `instagram_url`, `direccion`, `telefono_contacto`, `email_contacto`, `fecha_actualizacion`, `logo_navbar`, `logo_footer`, `banner_imagen`) VALUES
(1, 'SITIO DE PRUEBA', 'gris', NULL, NULL, NULL, 'Ibisay la mas marisol', 'NUESTRO EQUIPO PERSONALIZADO', 'SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS', 'quienes_somos_1756022793.jpg', '', '', '', '', '', '', '2025-08-24 08:08:07', NULL, NULL, 'banner_1756022887.jpg');

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
(1, 'venta', 0, 'IT essentials', 'si', 'completa', 1400000.00, 1, 'property_1756015450_4698.png', 'cañas', NULL, 'activa', '2025-08-24 06:04:10', '2025-08-24 06:04:10'),
(2, 'alquiler', 0, 'fedePenhouse', 'apartemento lujoso en escazu', 'cuanta con 4 habitaciones y 5 baños', 500000.00, 2, 'property_1756016591_9509.jpg', 'Escazu', NULL, 'activa', '2025-08-24 06:23:11', '2025-08-24 06:23:11');

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
(1, 'Administrador', '0000-0000', 'admin@uthsolutions.com', 'admin@uthsolutions.com', 'Admin', '$2y$10$Bl7S/tDHLDserA.5Jopg6OcH4BD1JVahPFxWET4PM.0OsaB.FTCm.', 'administrador', '2025-08-24 05:47:18', '2025-08-24 05:47:18'),
(2, 'Federico', '84096422', 'fede@gmail.com', 'fede@gmail.com', 'fedeXD', '$2y$10$iXq/JzAjaGQYOD0MhZ9pTOLeamibhAXV./K0eWa4nARrM9sGrQtDO', 'agente', '2025-08-24 06:16:32', '2025-08-24 06:16:32');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
