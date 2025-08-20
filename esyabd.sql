SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

CREATE TABLE
  `asistencia` (
    `id` int (11) NOT NULL,
    `aspirante_id` int (11) NOT NULL,
    `materia_id` int (11) NOT NULL,
    `fecha` date NOT NULL,
    `presente` tinyint (1) NOT NULL DEFAULT 0,
    `observaciones` text DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `aspirantes` (
    `id` int (11) NOT NULL,
    `dni` varchar(20) NOT NULL,
    `nombre` varchar(255) NOT NULL,
    `apellido` varchar(255) NOT NULL,
    `fecha_nacimiento` date DEFAULT NULL,
    `direccion` varchar(255) DEFAULT NULL,
    `telefono` varchar(50) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `estado` enum ('activo', 'inactivo', 'graduado') NOT NULL DEFAULT 'activo',
    `fecha_ingreso` date DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `materias` (
    `id` int (11) NOT NULL,
    `nombre` varchar(255) NOT NULL,
    `profesor` varchar(255) NOT NULL,
    `horas_cursado` int (11) DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `usuarios` (
    `id` int (11) NOT NULL,
    `username` varchar(255) NOT NULL,
    `nombre_completo` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `rol` enum ('admin', 'operador') NOT NULL DEFAULT 'operador',
    `last_login` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO
  `usuarios` (
    `id`,
    `username`,
    `nombre_completo`,
    `email`,
    `password`,
    `rol`,
    `last_login`,
    `created_at`
  )
VALUES
  (
    1,
    'admin',
    'Administrador del Sistema',
    'admin@example.com',
    'admin1234',
    '',
    '2025-08-07 15:50:57',
    '2025-08-07 11:38:25'
  );

ALTER TABLE `asistencia` ADD PRIMARY KEY (`id`),
ADD KEY `aspirante_id` (`aspirante_id`),
ADD KEY `materia_id` (`materia_id`);

ALTER TABLE `aspirantes` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `dni` (`dni`);

ALTER TABLE `materias` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `usuarios` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `username` (`username`),
ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `asistencia` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `aspirantes` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `materias` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

ALTER TABLE `asistencia` ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`aspirante_id`) REFERENCES `aspirantes` (`id`),
ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

COMMIT;