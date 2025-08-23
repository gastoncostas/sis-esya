-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 22-08-2025 a las 23:07:46
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `esyabd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

DROP TABLE IF EXISTS `asistencia`;
CREATE TABLE IF NOT EXISTS `asistencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `aspirante_id` int NOT NULL,
  `comision` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `presente` tinyint(1) DEFAULT '0',
  `justificado` tinyint(1) DEFAULT '0',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance` (`aspirante_id`,`comision`,`fecha`),
  KEY `fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `aspirante_id`, `comision`, `fecha`, `presente`, `justificado`, `observaciones`, `created_at`) VALUES
(1, 1, 'C', '2025-08-22', 0, 0, '', '2025-08-22 20:03:18'),
(2, 6, 'D', '2025-08-22', 0, 0, '', '2025-08-22 20:03:21'),
(3, 2, 'E', '2025-08-22', 0, 0, '', '2025-08-22 20:03:48'),
(4, 5, 'E', '2025-08-22', 0, 0, '', '2025-08-22 20:03:48'),
(12, 1, 'A', '2025-08-22', 0, 0, '', '2025-08-22 20:08:50'),
(14, 6, 'E', '2025-08-22', 0, 0, '', '2025-08-22 20:09:32'),
(15, 1, 'B', '2025-08-22', 1, 0, '', '2025-08-22 22:59:24'),
(16, 6, 'C', '2025-08-22', 1, 0, '', '2025-08-22 22:59:28'),
(17, 4, 'E', '2025-08-22', 1, 0, '', '2025-08-22 22:59:33'),
(18, 7, 'F', '2025-08-22', 1, 0, '', '2025-08-22 22:59:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspirantes`
--

DROP TABLE IF EXISTS `aspirantes`;
CREATE TABLE IF NOT EXISTS `aspirantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `lugar_nacimiento` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicilio` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_civil` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel_educativo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `comision` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'A',
  `fecha_ingreso` date DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aspirantes`
--

INSERT INTO `aspirantes` (`id`, `dni`, `apellido`, `nombre`, `fecha_nacimiento`, `lugar_nacimiento`, `domicilio`, `telefono`, `email`, `estado_civil`, `nivel_educativo`, `estado`, `comision`, `fecha_ingreso`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, '42467885', 'Costas', 'Gastón Jesús Sebastián', '2000-05-30', 'Concepción, Chicligasta, Tucumán.', 'Lote 38, Loteo Martínez, San Andrés, Cruz Alta, Tucumán.', '3815543893', 'gncostas@gmail.com', 'soltero', 'terciario', 'activo', 'B', '2025-02-03', NULL, '2025-08-21 20:09:52', '2025-08-22 19:49:59'),
(2, '43706634', 'Ortega', 'Sebastian Eduardo', '2002-04-04', 'San Miguel de Tucumán, Tucumán.', 'Pasaje San José 2369, San Miguel de Tucumán, Tucumán.', '3814025001', 'sebaortegaa8@gmail.com', 'soltero', 'secundario', 'activo', 'E', '2025-02-03', NULL, '2025-08-21 20:14:50', '2025-08-22 19:50:14'),
(4, '44232302', 'Quipildor Rueda', 'Santiago', '2000-01-01', NULL, 'Salta 655, Barrio Norte, San Miguel de Tucumán, Tucumán.', '3813060111', 'djquipicirujaa7@gmail.com', NULL, NULL, 'activo', 'E', '2025-02-03', NULL, '2025-08-21 21:09:51', '2025-08-22 19:50:21'),
(5, '38743470', 'Roldán', 'Gabriel Elias', '1995-06-23', NULL, 'Laprida 650, Alderetes, Tucumán.', '3815692618', 'rgabrielelias@icloud.com', 'soltero', 'terciario', 'activo', 'E', '2018-04-19', NULL, '2025-08-21 21:14:50', '2025-08-22 19:50:34'),
(6, '42355921', 'Elias', 'Lucas Serafín', '1999-10-24', 'San Miguel de Tucumán, Tucumán.', 'Av. Amador Lucero 2115', '3815985387', 'lucaselias2115@gmail.com', 'soltero', 'terciario', 'activo', 'C', '2025-02-03', NULL, '2025-08-22 16:50:43', '2025-08-22 23:02:38'),
(7, '41960849', 'Maldonado Ruiz', 'Karen Emilia', '2003-05-09', 'Simoca, Tucumán.', 'Av. Soldati 654', '3812545893', 'karencitah@hotmail.com', 'soltero', 'terciario', 'activo', 'F', '2025-02-03', '', '2025-08-22 22:59:01', '2025-08-22 22:59:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

DROP TABLE IF EXISTS `materias`;
CREATE TABLE IF NOT EXISTS `materias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horas_semanales` int DEFAULT NULL,
  `profesor` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'activa',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_completo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `password`, `nombre_completo`, `rol`, `last_login`, `created_at`) VALUES
(1, 'admin', NULL, 'esya2025', 'Administrador del Sistema', 'administrador', '2025-08-22 16:47:11', '2025-08-21 19:30:28');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`aspirante_id`) REFERENCES `aspirantes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
