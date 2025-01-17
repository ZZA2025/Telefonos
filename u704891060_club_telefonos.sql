-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-01-2025 a las 14:01:43
-- Versión del servidor: 10.11.11-MariaDB
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u704891060_club_telefonos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `nombre`, `precio`) VALUES
(1, 'Fútbol', 200.00),
(2, 'Natación', 150.00),
(3, 'Tenis', 180.00),
(4, 'Gimnasio', 100.00),
(5, 'Yoga', 120.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `username`, `password`) VALUES
(1, 'Sguazza', '6fde787bba8f608463a465f319842238');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_socios`
--

CREATE TABLE `pagos_socios` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) DEFAULT NULL,
  `mes_pago` date DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos_socios`
--

INSERT INTO `pagos_socios` (`id`, `socio_id`, `mes_pago`, `metodo_pago`) VALUES
(7, 10, '2025-01-16', 'Efectivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responsables`
--

CREATE TABLE `responsables` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `relacion` varchar(50) NOT NULL,
  `telefono_emergencia` varchar(20) NOT NULL,
  `socio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `id` int(11) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `edad` int(11) NOT NULL,
  `tipo_socio_id` int(11) NOT NULL,
  `cuota` decimal(10,2) NOT NULL,
  `estado_cuenta` varchar(20) NOT NULL,
  `actividad` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_ultimo_pago` date DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`id`, `apellido`, `nombre`, `dni`, `telefono`, `email`, `foto`, `edad`, `tipo_socio_id`, `cuota`, `estado_cuenta`, `actividad`, `fecha_inicio`, `fecha_ultimo_pago`, `fecha_registro`) VALUES
(10, 'Sguazza', 'Martin', '29343056', '02235820117', 'martin_sguazza@hotmail.com', 'computer-1591018_1280.jpg', 43, 1, 0.00, 'Efectivo', NULL, '2025-01-16', '2025-01-16', '2025-01-16 23:42:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios_actividades`
--

CREATE TABLE `socios_actividades` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `actividad_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `socios_actividades`
--

INSERT INTO `socios_actividades` (`id`, `socio_id`, `actividad_id`) VALUES
(97, 10, 1),
(98, 10, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temp_actividades`
--

CREATE TABLE `temp_actividades` (
  `id` int(11) NOT NULL,
  `nombre_actividad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_socios`
--

CREATE TABLE `tipos_socios` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_socios`
--

INSERT INTO `tipos_socios` (`id`, `tipo`, `monto`) VALUES
(1, 'Activo', 1000.00),
(2, 'Adherente', 800.00),
(3, 'Por Actividad', 600.00),
(4, 'Por Convenio', 500.00),
(5, 'Grupo Familiar', 1500.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos_socios`
--
ALTER TABLE `pagos_socios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagos_socios_ibfk_1` (`socio_id`);

--
-- Indices de la tabla `responsables`
--
ALTER TABLE `responsables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsables_ibfk_1` (`socio_id`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_socio_id` (`tipo_socio_id`),
  ADD KEY `actividad` (`actividad`);

--
-- Indices de la tabla `socios_actividades`
--
ALTER TABLE `socios_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `socio_id` (`socio_id`),
  ADD KEY `actividad_id` (`actividad_id`);

--
-- Indices de la tabla `temp_actividades`
--
ALTER TABLE `temp_actividades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_socios`
--
ALTER TABLE `tipos_socios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pagos_socios`
--
ALTER TABLE `pagos_socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `responsables`
--
ALTER TABLE `responsables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `socios_actividades`
--
ALTER TABLE `socios_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `temp_actividades`
--
ALTER TABLE `temp_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipos_socios`
--
ALTER TABLE `tipos_socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pagos_socios`
--
ALTER TABLE `pagos_socios`
  ADD CONSTRAINT `pagos_socios_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `responsables`
--
ALTER TABLE `responsables`
  ADD CONSTRAINT `responsables_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `socios`
--
ALTER TABLE `socios`
  ADD CONSTRAINT `socios_ibfk_1` FOREIGN KEY (`tipo_socio_id`) REFERENCES `tipos_socios` (`id`),
  ADD CONSTRAINT `socios_ibfk_2` FOREIGN KEY (`actividad`) REFERENCES `actividades` (`id`);

--
-- Filtros para la tabla `socios_actividades`
--
ALTER TABLE `socios_actividades`
  ADD CONSTRAINT `socios_actividades_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `socios_actividades_ibfk_2` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
