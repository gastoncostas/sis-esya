-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-08-2025 a las 14:55:44
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
  `materia_id` int NOT NULL,
  `fecha` date NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `aspirante_id` (`aspirante_id`),
  KEY `materia_id` (`materia_id`),
  KEY `fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `aspirante_id`, `materia_id`, `fecha`, `presente`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-08-13', 1, 'Presente y participativo', '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(2, 2, 1, '2025-08-13', 1, NULL, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, 1, 2, '2025-08-13', 0, 'Ausente justificado', '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(4, 2, 2, '2025-08-13', 1, 'Excelente participación', '2025-08-13 19:12:23', '2025-08-13 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspirantes`
--

DROP TABLE IF EXISTS `aspirantes`;
CREATE TABLE IF NOT EXISTS `aspirantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dni` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado_civil` enum('SOLTERO/A','CASADO/A','DIVORCIADO/A','CONCUBINATO','VIUDO/A') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL DEFAULT 'SOLTERO/A',
  `domicilio_real` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `estado` enum('ASPIRANTE','CURSANTE','SUPLENTE') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL DEFAULT 'ASPIRANTE',
  `sit_revista` enum('A.R.T.','NOTA MEDICA','DISPONIBLE','PASIVO','ACTIVO') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL DEFAULT 'ACTIVO',
  `fecha_ingreso` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`),
  KEY `estado` (`estado`),
  KEY `fecha_ingreso` (`fecha_ingreso`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aspirantes`
--

INSERT INTO `aspirantes` (`id`, `dni`, `nombre`, `apellido`, `fecha_nacimiento`, `estado_civil`, `domicilio_real`, `telefono`, `email`, `estado`, `sit_revista`, `fecha_ingreso`, `created_at`, `updated_at`) VALUES
(1, 12345678, 'Juan', 'Pérez', '1995-05-15', 'SOLTERO/A', 'Av. Independencia 123', '381-1234567', 'juan.perez@email.com', '', 'ACTIVO', '2024-03-01', '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(2, 87654321, 'María', 'González', '1996-08-22', 'SOLTERO/A', 'San Martín 456', '381-2345678', 'maria.gonzalez@email.com', '', 'ACTIVO', '2024-03-01', '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, 11223344, 'Carlos', 'Rodríguez', '1994-12-10', 'SOLTERO/A', 'Belgrano 789', '381-3456789', 'carlos.rodriguez@email.com', '', 'ACTIVO', '2023-03-01', '2025-08-13 19:12:23', '2025-08-13 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `divisiones`
--

DROP TABLE IF EXISTS `divisiones`;
CREATE TABLE IF NOT EXISTS `divisiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `divisiones`
--

INSERT INTO `divisiones` (`id`, `nombre`, `descripcion`, `activo`, `created_at`) VALUES
(1, 'Jefatura de Cuerpo', 'Gestión administrativa y operativa del cuerpo de cadetes', 1, '2025-08-13 19:12:23'),
(2, 'Jefatura de Estudios', 'Coordinación académica y planes de estudio', 1, '2025-08-13 19:12:23'),
(3, 'Servicios Médicos', 'Atención médica y seguimiento sanitario', 1, '2025-08-13 19:12:23'),
(4, 'Ayudantía', 'Servicios de apoyo y asistencia administrativa', 1, '2025-08-13 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

DROP TABLE IF EXISTS `materias`;
CREATE TABLE IF NOT EXISTS `materias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `profesor` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `modulo` enum('I','II') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL DEFAULT 'I',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `profesor`, `modulo`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Derecho Penal', 'Dr. Roberto Silva', 'I', 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(2, 'Procedimientos Policiales', 'Comisario Ana López', 'I', 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, 'Educación Física', 'Prof. Miguel Torres', 'I', 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(4, 'Primeros Auxilios', 'Dr. Laura Martínez', 'I', 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `apellido` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `password` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('Administrador','Operador') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL DEFAULT 'Operador',
  `division_id` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`user`),
  UNIQUE KEY `email` (`email`),
  KEY `division_id` (`division_id`),
  KEY `activo` (`activo`),
  KEY `rol` (`rol`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `user`, `nombre`, `apellido`, `email`, `password`, `rol`, `division_id`, `activo`, `last_login`, `login_attempts`, `created_at`, `updated_at`) VALUES
(1, 'admin', '', '', 'admin@esya.gob.ar', 'admin1234', '', NULL, 1, '2025-08-16 12:21:32', 0, '2025-08-07 11:38:25', '2025-08-16 12:21:32'),
(2, 'jefe_estudios', '', '', 'estudios@esya.gob.ar', 'estudios12', 'Operador', 2, 1, NULL, 0, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, 'jefe_cuerpo', '', '', 'cuerpo@esya.gob.ar', 'cuerpo123', 'Operador', 1, 1, NULL, 0, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(4, 'servicios_medicos', '', '', 'medicos@esya.gob.ar', 'medicos123', 'Operador', 3, 1, NULL, 0, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(5, 'ayudantia', '', '', 'ayudantia@esya.gob.ar', 'ayudantia1', 'Operador', 4, 1, NULL, 0, '2025-08-13 19:12:23', '2025-08-13 19:12:23');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`aspirante_id`) REFERENCES `aspirantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisiones` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
