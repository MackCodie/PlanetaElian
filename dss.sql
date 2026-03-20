-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-03-2026 a las 00:37:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dss`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `valor_maximo` decimal(5,2) DEFAULT 100.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `nocontrol` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `grupo` varchar(15) DEFAULT NULL,
  `periodo` varchar(50) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `nocontrol`, `nombre`, `email`, `password_hash`, `grupo`, `periodo`, `fecha_registro`) VALUES
(3, '20560303', 'elian', 'elianalumno@gmail.com', '$argon2id$v=19$m=65536,t=4,p=2$bGk5LllzcUkyVnNCWnE4dQ$1uEqcVZyiCs+6tFEclqAMcJpSMSKqfOl9wOtKTILYrc', '65', 'ENE-JUN', '2026-03-11 19:20:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id_asistencia` int(11) NOT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('Presente','Falta','Retardo','Justificado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id_calificacion` int(11) NOT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_actividad` int(11) DEFAULT NULL,
  `puntaje_obtenido` decimal(5,2) NOT NULL,
  `fecha_evaluacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `nombre_grupo` varchar(50) NOT NULL,
  `ciclo_escolar` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorio_hashing`
--

CREATE TABLE `laboratorio_hashing` (
  `id` int(11) NOT NULL,
  `usuario_prueba` varchar(50) DEFAULT NULL,
  `password_texto` varchar(100) DEFAULT NULL,
  `password_encriptado` varchar(255) DEFAULT NULL,
  `metodo_usado` varchar(50) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `laboratorio_hashing`
--

INSERT INTO `laboratorio_hashing` (`id`, `usuario_prueba`, `password_texto`, `password_encriptado`, `metodo_usado`, `fecha`) VALUES
(1, 'usuariomd5', '123', '202cb962ac59075b964b07152d234b70', 'md5', '2026-02-10 00:55:32'),
(2, 'usuariosha1', '123', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'sha1', '2026-02-10 00:55:43'),
(3, 'usuarioBCRYPT', '123', '$2y$10$bwVh9AjTGjPD3M6DirbGiO.r8Sd0swYktEK1unuLBhMA9IGlNGo8G', 'default', '2026-02-10 00:55:58'),
(4, 'usuariosha256', '123', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'sha256', '2026-02-10 00:59:21'),
(5, 'admin_md5', 'secreto123', '6d4173db2a351fbc5563e4a318a5d247', 'MD5', '2026-02-10 01:09:33'),
(6, 'user_sha1', 'secreto123', '9696ca289ab6b0efd4758876bb6a79cdddc8f6c7', 'SHA-1', '2026-02-10 01:09:33'),
(7, 'dev_sha256', 'secreto123', '71c9377fbb319c6f0e4df5b1123b439cc03ff3fc9d5789c8459e7040a99fec8b', 'SHA-256', '2026-02-10 01:09:33'),
(8, 'root_sha512', 'secreto123', '42d0ea697109af4149b88017f59e2d11309cb80f4a98bc7b848349c13e962fcc83aae6e0e467bc7d7d48c640251291f7136e18c8d4750f5df60255b6a964961c', 'SHA-512', '2026-02-10 01:09:33'),
(9, 'web_bcrypt', 'secreto123', '$2y$10$n9qo8uLOickgx2ZMRZoMyeIjZAgCXzqshfG2QA8zdzXp.YAtuGOfO', 'Bcrypt (via PHP)', '2026-02-10 01:09:34'),
(10, 'admin_argon2', 'secreto123', '$argon2id$v=19$m=65536,t=4,p=1$M09VdzZ6TTR0OElSUEVnNw$H/9P8kXoYnI', 'Argon2id', '2026-02-10 01:09:34'),
(11, 'user_scrypt', 'secreto123', '$scrypt$ln=16384,r=8,p=1$S2Fsdm9TYWx0$D9...', 'Scrypt', '2026-02-10 01:09:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre_materia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `descripcion`) VALUES
(1, 'alumno_select', 'Ver la lista de alumnos'),
(2, 'alumno_insert', 'Registrar nuevos alumnos (Individual o Masivo)'),
(3, 'alumno_update', 'Editar información de alumnos'),
(4, 'alumno_delete', 'Eliminar alumnos del sistema'),
(5, 'tomar_asistencia', 'Registrar asistencias de los grupos'),
(6, 'ver_calificaciones', 'Consultar calificaciones'),
(7, 'editar_calificaciones', 'Modificar calificaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id_profesor`, `nombre`, `correo`, `password_hash`) VALUES
(6, 'Profesor Prueba', 'pruebaProfesor29072002@gmail.com', '$2y$10$dkqSNLL2hy94.C/oW7.WV.88R5/9Zs68jPODM58wSLARbVUiEghGy'),
(7, 'Prueba segunda', 'pruebaProfesor29072002v2@gmail.com', '$2y$10$iSEmDPpc1dJ.rG1DjJjUDO1AjhKZFUJsgrN377hRdiY7qnwQD0x8a'),
(8, 'RFTGHJ', 'GFDRGT@GMAIL.COM', '$2y$10$NRKSOK4nri35YLrsakv1COGe3RAqA.k4rZIHDL.oHpAOOVd6MxXOu'),
(9, 'HOLs', 'RtryFTGH@GMAIL.COM', '$2y$10$tzum28bGuj5d3AAAG7oIHOeXTkuHpWac3gf71w4sz4KNsEhZbtUwm');

--
-- Disparadores `profesores`
--
DELIMITER $$
CREATE TRIGGER `despues_insertar_profesor` AFTER INSERT ON `profesores` FOR EACH ROW BEGIN
    INSERT INTO usuarios (correo, password_hash, id_rol)
    VALUES (NEW.correo, NEW.password_hash, 2);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_extra`
--

CREATE TABLE `puntos_extra` (
  `id_punto_extra` int(11) NOT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `cantidad` decimal(5,2) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Profesor', 'Acceso a pase de lista y calificaciones'),
(3, 'Alumno', 'Solo lectura de su propia información');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

CREATE TABLE `rol_permiso` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permiso`
--

INSERT INTO `rol_permiso` (`id_rol`, `id_permiso`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 1),
(2, 2),
(2, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `password_hash`, `id_rol`, `estado`) VALUES
(3, 'Elian Administrador', 'elianglzrmz@gmail.com', '$2y$10$dwgj.jeNvW6rhUx3vxYM3.66lbP2BmT2H2Ue6HjJ9gdIzpZyJcOYW', 1, 'activo'),
(4, '', 'pruebaProfesor29072002@gmail.com', '$2y$10$dkqSNLL2hy94.C/oW7.WV.88R5/9Zs68jPODM58wSLARbVUiEghGy', 2, 'activo'),
(6, '', 'pruebaProfesor29072002v2@gmail.com', '$2y$10$QjSHW9shS6ZpGvG9N9G9Gu.p.E.p.E.p.E.p.E.p.E.p.E.p.E.p.E', 2, 'activo'),
(7, '', 'GFDRGT@GMAIL.COM', '$2y$10$NRKSOK4nri35YLrsakv1COGe3RAqA.k4rZIHDL.oHpAOOVd6MxXOu', 2, 'activo'),
(8, '', 'RtryFTGH@GMAIL.COM', '$2y$10$tzum28bGuj5d3AAAG7oIHOeXTkuHpWac3gf71w4sz4KNsEhZbtUwm', 2, 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_grupo` (`id_grupo`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `nocontrol` (`nocontrol`),
  ADD KEY `id_grupo` (`grupo`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_alumno` (`id_alumno`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id_calificacion`),
  ADD KEY `id_alumno` (`id_alumno`),
  ADD KEY `id_actividad` (`id_actividad`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_grupo` (`id_grupo`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `laboratorio_hashing`
--
ALTER TABLE `laboratorio_hashing`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`),
  ADD UNIQUE KEY `nombre_materia` (`nombre_materia`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`),
  ADD UNIQUE KEY `nombre_permiso` (`nombre_permiso`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_profesor`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `puntos_extra`
--
ALTER TABLE `puntos_extra`
  ADD PRIMARY KEY (`id_punto_extra`),
  ADD KEY `id_alumno` (`id_alumno`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id_calificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `laboratorio_hashing`
--
ALTER TABLE `laboratorio_hashing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `puntos_extra`
--
ALTER TABLE `puntos_extra`
  MODIFY `id_punto_extra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE,
  ADD CONSTRAINT `actividades_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `actividades_ibfk_3` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencias_ibfk_3` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`) ON DELETE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE,
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `horarios_ibfk_3` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntos_extra`
--
ALTER TABLE `puntos_extra`
  ADD CONSTRAINT `puntos_extra_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `puntos_extra_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE,
  ADD CONSTRAINT `puntos_extra_ibfk_3` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `rol_permiso_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permiso_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
