-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-08-2025 a las 21:17:22
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
-- Base de datos: `esyabd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `aspirante_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `aspirantes` (
  `id` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo','graduado') NOT NULL DEFAULT 'activo',
  `fecha_ingreso` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aspirantes`
--

INSERT INTO `aspirantes` (`id`, `dni`, `nombre`, `apellido`, `fecha_nacimiento`, `direccion`, `telefono`, `email`, `estado`, `fecha_ingreso`, `created_at`, `updated_at`) VALUES
(1, '12345678', 'Juan', 'Pérez', '1995-05-15', 'Av. Independencia 123', '381-1234567', 'juan.perez@email.com', 'activo', '2024-03-01', '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(2, '87654321', 'María', 'González', '1996-08-22', 'San Martín 456', '381-2345678', 'maria.gonzalez@email.com', 'activo', '2024-03-01', '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, '11223344', 'Carlos', 'Rodríguez', '1994-12-10', 'Belgrano 789', '381-3456789', 'carlos.rodriguez@email.com', 'graduado', '2023-03-01', '2025-08-13 19:12:23', '2025-08-13 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `divisiones`
--

CREATE TABLE `divisiones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `profesor` varchar(255) NOT NULL,
  `horas_cursado` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `profesor`, `horas_cursado`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Derecho Penal', 'Dr. Roberto Silva', 80, 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(2, 'Procedimientos Policiales', 'Comisario Ana López', 60, 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, 'Educación Física', 'Prof. Miguel Torres', 40, 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(4, 'Primeros Auxilios', 'Dr. Laura Martínez', 30, 1, '2025-08-13 19:12:23', '2025-08-13 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','operador') NOT NULL DEFAULT 'operador',
  `division_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `nombre_completo`, `email`, `password`, `rol`, `division_id`, `activo`, `last_login`, `login_attempts`, `locked_until`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrador del Sistema', 'admin@esya.gob.ar', 'admin1234', 'admin', NULL, 1, '2025-08-13 19:15:20', 0, NULL, '2025-08-07 11:38:25', '2025-08-13 19:15:20'),
(2, 'jefe_estudios', 'Jefe de Estudios', 'estudios@esya.gob.ar', 'estudios123', 'operador', 2, 1, NULL, 0, NULL, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(3, 'jefe_cuerpo', 'Jefe de Cuerpo', 'cuerpo@esya.gob.ar', 'cuerpo123', 'operador', 1, 1, NULL, 0, NULL, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(4, 'servicios_medicos', 'Servicios Médicos', 'medicos@esya.gob.ar', 'medicos123', 'operador', 3, 1, NULL, 0, NULL, '2025-08-13 19:12:23', '2025-08-13 19:12:23'),
(5, 'ayudantia', 'Ayudantía', 'ayudantia@esya.gob.ar', 'ayudantia123', 'operador', 4, 1, NULL, 0, NULL, '2025-08-13 19:12:23', '2025-08-13 19:12:23');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aspirante_id` (`aspirante_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `fecha` (`fecha`);

--
-- Indices de la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `estado` (`estado`),
  ADD KEY `fecha_ingreso` (`fecha_ingreso`);

--
-- Indices de la tabla `divisiones`
--
ALTER TABLE `divisiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `activo` (`activo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `division_id` (`division_id`),
  ADD KEY `activo` (`activo`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `divisiones`
--
ALTER TABLE `divisiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
