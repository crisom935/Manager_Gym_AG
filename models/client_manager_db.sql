-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-12-2025 a las 02:26:26
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
-- Base de datos: `client_manager_db`
--
CREATE DATABASE IF NOT EXISTS `client_manager_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `client_manager_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_clientes`
--

DROP TABLE IF EXISTS `tb_clientes`;
CREATE TABLE `tb_clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `plan_suscripcion` varchar(50) NOT NULL,
  `pago_efectivo` decimal(10,2) DEFAULT 0.00,
  `pago_tarjeta` decimal(10,2) DEFAULT 0.00,
  `pago_transferencia` decimal(10,2) DEFAULT 0.00,
  `total_pagado` decimal(10,2) DEFAULT 0.00,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_inscripcion` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_clientes`
--

INSERT INTO `tb_clientes` (`id_cliente`, `nombre_cliente`, `plan_suscripcion`, `pago_efectivo`, `pago_tarjeta`, `pago_transferencia`, `total_pagado`, `correo`, `telefono`, `fecha_inscripcion`, `fecha_vencimiento`, `created_at`) VALUES
(65, 'Oneal troll', 'Individual Semanal', 200.00, 0.00, 0.00, 200.00, '', '', '2025-12-10', '2025-12-17', '2025-12-10 20:06:01'),
(66, 'Oneal Sayayin', 'Individual Semanal', 100.00, 100.00, 0.00, 200.00, '', '', '2025-12-10', '2025-12-17', '2025-12-10 20:07:00'),
(67, 'Juan Guarza', 'Familiar #2', 0.00, 2300.00, 0.00, 2300.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:08:20'),
(68, 'Laura MMole', 'Familiar #2', 0.00, 0.00, 0.00, 0.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:08:20'),
(69, 'Dante Padre', 'Familiar #2', 0.00, 0.00, 0.00, 0.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:08:20'),
(70, 'Jose Medero', 'Familiar #2', 0.00, 0.00, 0.00, 0.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:08:20'),
(71, 'Deus XEs', 'Familiar #2', 2000.00, 300.00, 0.00, 2300.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:41:33'),
(72, 'Mani', 'Familiar #2', 0.00, 0.00, 0.00, 0.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:41:33'),
(73, 'luber', 'Familiar #2', 0.00, 0.00, 0.00, 0.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:41:33'),
(74, 'joel', 'Familiar #2', 0.00, 0.00, 0.00, 0.00, 'familia@gmail.com', '5632083462', '2025-12-10', '2026-01-10', '2025-12-10 20:41:33'),
(75, 'Oneal troll', 'Paquete Amigos', 1100.00, 0.00, 0.00, 1100.00, '', '', '2025-12-10', '2026-01-10', '2025-12-10 20:41:50'),
(76, 'spartabn', 'Paquete Amigos', 0.00, 0.00, 0.00, 0.00, '', '', '2025-12-10', '2026-01-10', '2025-12-10 20:41:50'),
(78, 'steven tyler', 'Individual Mensual', 650.00, 0.00, 0.00, 650.00, '', '', '2025-10-01', '2025-11-01', '2025-12-10 21:51:13'),
(82, 'Oneal troll', 'Individual Semanal', 0.00, 0.00, 200.00, 200.00, '', '', '2025-12-11', '2025-12-18', '2025-12-11 20:08:31'),
(83, 'Oneal troll', 'Individual Mensual', 0.00, 0.00, 650.00, 650.00, '', '', '2025-12-05', '2026-01-05', '2025-12-12 14:23:04'),
(84, 'nat', 'Individual Semanal', 0.00, 0.00, 200.00, 200.00, '', '', '2025-12-12', '2025-12-19', '2025-12-12 14:46:45'),
(85, 'leo', 'Individual Semanal', 0.00, 0.00, 200.00, 200.00, '', '', '2025-12-12', '2025-12-19', '2025-12-12 14:52:39'),
(86, 'mañana vence mes', 'Individual Mensual', 0.00, 0.00, 650.00, 650.00, '', '', '2025-11-12', '2025-12-12', '2025-12-12 18:40:38'),
(87, 'test1', 'Individual Semanal', 0.00, 0.00, 200.00, 200.00, '', '', '2025-12-12', '2025-12-19', '2025-12-12 18:51:52'),
(88, 'test2', 'Individual Semanal', 0.00, 0.00, 200.00, 200.00, '', '', '2025-12-12', '2025-12-19', '2025-12-12 18:53:31'),
(89, 'TEST3', 'Individual Mensual', 650.00, 0.00, 0.00, 650.00, '', '', '2025-12-12', '2026-01-12', '2025-12-12 19:34:57'),
(90, 'Cristo', 'Individual Semanal', 50.00, 50.00, 100.00, 200.00, '', '', '2025-12-15', '2025-12-22', '2025-12-13 02:12:19'),
(91, 'Cris', 'Individual Semanal', 200.00, 0.00, 0.00, 200.00, '', '', '2025-12-12', '2025-12-19', '2025-12-13 02:18:36'),
(93, 'Jose Luis Aguirre Castillon', 'Individual Semanal', 50.00, 50.00, 100.00, 200.00, 'jaguirrec@hotmail.com', '1234567890', '2025-12-15', '2025-12-22', '2025-12-15 22:38:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuarios`
--

DROP TABLE IF EXISTS `tb_usuarios`;
CREATE TABLE `tb_usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_usuarios`
--

INSERT INTO `tb_usuarios` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(0, 'Administrador', 'admin@client.manager.com', '$2y$10$rfLse/rwbnmiZmRsHXHCyeX0.q8H8giEFhljtrBrDw5wtsw1.iS4i', '2025-11-25 04:57:46'),
(0, 'Oneal Ovando', 'oneal@gmail.com', '$2y$10$yXo6Kcn3..INYbkFfbzxfuho.8ltPxVB7DADuxECbtQKf9NK.Uo5e', '2025-12-12 17:58:45'),
(0, 'PATO ZAMBRANO', 'PATO@GMAIL.COM', '$2y$10$Da4PpKQZzO2ya1gtqmtUdOPppv4fbQhf.ZRp5ZeKjfnNhSuPr/ZYi', '2025-12-15 22:35:36');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
