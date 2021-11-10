-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 21-01-2019 a las 21:22:18
-- Versión del servidor: 5.5.59-0+deb8u1
-- Versión de PHP: 5.6.33-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `dh.tordo.ejemplo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup`
--

CREATE TABLE IF NOT EXISTS `backup` (
`backup_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `usuario` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE IF NOT EXISTS `cliente` (
`cliente_id` int(11) NOT NULL,
  `razon_social` text COLLATE utf8_spanish_ci,
  `descuento` float NOT NULL,
  `iva` float NOT NULL,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `codigopostal` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `localidad` text COLLATE utf8_spanish_ci,
  `latitud` text COLLATE utf8_spanish_ci,
  `longitud` text COLLATE utf8_spanish_ci,
  `observacion` text COLLATE utf8_spanish_ci,
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL,
  `condicionfiscal` int(11) DEFAULT NULL,
  `frecuenciaventa` int(11) DEFAULT NULL,
  `vendedor` int(11) DEFAULT NULL,
  `flete` int(11) DEFAULT NULL,
  `tipofactura` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`cliente_id`, `razon_social`, `descuento`, `iva`, `documento`, `domicilio`, `codigopostal`, `localidad`, `latitud`, `longitud`, `observacion`, `provincia`, `documentotipo`, `condicioniva`, `condicionfiscal`, `frecuenciaventa`, `vendedor`, `flete`, `tipofactura`) VALUES
(1, 'NIETO FRANCO', 0, 21, 20325889056, 'Viamonte', '', 'Federación I', '', '', '', 11, 1, 1, 2, 1, 1, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicionfiscal`
--

CREATE TABLE IF NOT EXISTS `condicionfiscal` (
`condicionfiscal_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `condicionfiscal`
--

INSERT INTO `condicionfiscal` (`condicionfiscal_id`, `denominacion`, `detalle`) VALUES
(1, 'CONSUMIDOR FINAL', ''),
(2, 'MONOTRIBUTO', ''),
(3, 'RESPONSABLE INSCRIPTO', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicioniva`
--

CREATE TABLE IF NOT EXISTS `condicioniva` (
`condicioniva_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `condicioniva`
--

INSERT INTO `condicioniva` (`condicioniva_id`, `denominacion`, `detalle`) VALUES
(1, 'IVA Responsable Inscripto', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicionpago`
--

CREATE TABLE IF NOT EXISTS `condicionpago` (
`condicionpago_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `condicionpago`
--

INSERT INTO `condicionpago` (`condicionpago_id`, `denominacion`, `detalle`) VALUES
(1, 'Cuenta Corriente', ''),
(2, 'Contado', ' ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE IF NOT EXISTS `configuracion` (
`configuracion_id` int(11) NOT NULL,
  `razon_social` text COLLATE utf8_spanish_ci,
  `domicilio_comercial` text COLLATE utf8_spanish_ci,
  `cuit` bigint(20) DEFAULT NULL,
  `ingresos_brutos` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_inicio_actividad` date DEFAULT NULL,
  `punto_venta` int(11) NOT NULL,
  `condicioniva` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`configuracion_id`, `razon_social`, `domicilio_comercial`, `cuit`, `ingresos_brutos`, `fecha_inicio_actividad`, `punto_venta`, `condicioniva`) VALUES
(1, 'ROVIRA FERNANDO', 'España 333 - La Rioja - La Rioja', 20280565424, '000-044426-6', '2018-04-01', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracionmenu`
--

CREATE TABLE IF NOT EXISTS `configuracionmenu` (
`configuracionmenu_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracionmenu`
--

INSERT INTO `configuracionmenu` (`configuracionmenu_id`, `denominacion`, `nivel`) VALUES
(1, 'DESARROLLADOR', 9),
(2, 'ADMINISTRADOR', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentacorrientecliente`
--

CREATE TABLE IF NOT EXISTS `cuentacorrientecliente` (
`cuentacorrientecliente_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `referencia` text COLLATE utf8_spanish_ci,
  `importe` float DEFAULT NULL,
  `ingreso` float NOT NULL DEFAULT '0',
  `cliente_id` int(11) DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL,
  `tipomovimientocuenta` int(11) DEFAULT NULL,
  `estadomovimientocuenta` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cuentacorrientecliente`
--

INSERT INTO `cuentacorrientecliente` (`cuentacorrientecliente_id`, `fecha`, `hora`, `referencia`, `importe`, `ingreso`, `cliente_id`, `egreso_id`, `tipomovimientocuenta`, `estadomovimientocuenta`) VALUES
(1, '2018-11-09', '22:40:23', 'Comprobante venta 0003-00000001', 20740.3, 0, 1, 1, 1, 4),
(2, '2018-11-10', '00:59:52', 'Pago de comprobante 0003-00000001', 20740.3, 20740.3, 1, 1, 2, 4),
(3, '2018-11-10', '01:07:21', 'Comprobante venta 0003-00000002', 175.45, 0, 1, 2, 1, 4),
(4, '2018-11-15', '22:42:51', 'Comprobante venta 0003-00000003', 12346.6, 0, 1, 3, 1, 4),
(5, '2018-11-20', '22:45:56', 'Comprobante venta 0003-0000004', 923.06, 0, 1, 4, 1, 4),
(6, '2018-11-20', '23:43:03', 'Comprobante venta 0003-0000005', 3407.07, 0, 1, 5, 1, 4),
(7, '2018-11-24', '19:39:43', 'Comprobante venta 0003-00000006', 2669.77, 0, 1, 6, 1, 4),
(8, '2018-11-24', '19:54:21', 'Comprobante venta 0003-00000007', 5442.23, 0, 1, 7, 1, 4),
(9, '2018-11-26', '23:33:46', 'Comprobante venta 0003-00000008', 3237.31, 0, 1, 8, 1, 4),
(10, '2018-11-26', '23:38:38', 'Comprobante venta 0003-00000009', 5569.93, 0, 1, 9, 1, 4),
(11, '2018-11-27', '21:44:28', 'Comprobante venta 0003-00000010', 1325.63, 0, 1, 10, 1, 4),
(12, '2018-11-27', '22:10:11', 'Comprobante venta 0003-00000011', 1176.59, 0, 1, 11, 1, 4),
(13, '2018-11-27', '22:12:33', 'Comprobante venta 0003-00000012', 333.72, 0, 1, 12, 1, 4),
(14, '2018-11-27', '23:19:13', 'Pago de comprobante 0003-00000012', 333.72, 333.72, 1, 12, 2, 4),
(18, '2018-12-03', '22:22:17', 'Comprobante venta ﻿﻿﻿0003-00000013', 350.16, 0, 1, 13, 1, 4),
(19, '2018-12-03', '22:44:47', 'Comprobante venta ﻿﻿﻿0003-00000014', 1180.48, 0, 1, 14, 1, 4),
(21, '2018-12-09', '00:50:40', 'Pago de comprobante 0003-00000003', 12346.6, 12346.6, 1, 3, 2, 4),
(22, '2018-12-09', '00:54:47', 'Pago de comprobante 0003-00000013', 350.16, 350.16, 1, 13, 2, 4),
(23, '2018-12-09', '01:02:08', 'Comprobante venta ﻿﻿﻿0003-00000015', 1152.72, 0, 1, 15, 1, 4),
(24, '2018-12-09', '01:05:54', 'Comprobante venta ﻿﻿﻿0003-00000016', 486.55, 0, 1, 16, 1, 4),
(25, '2018-12-09', '01:08:23', 'Comprobante venta ﻿﻿﻿0003-00000017', 1316.43, 0, 1, 17, 1, 4),
(26, '2018-12-09', '01:18:41', 'Comprobante venta ﻿﻿﻿0003-00000018', 407.87, 0, 1, 18, 1, 4),
(29, '2018-12-09', '01:29:21', 'Comprobante venta ﻿﻿﻿0003-00000019', 4090.03, 0, 1, 21, 1, 4),
(30, '2018-12-09', '02:10:02', 'Pago de comprobante 0003-00000015', 500, 500, 1, 15, 2, 4),
(31, '2018-12-09', '03:15:43', 'Pago de comprobante 0003-00000015', 500, 500, 1, 15, 2, 4),
(32, '2018-12-09', '03:15:51', 'Pago de comprobante 0003-00000015', 153, 153, 1, 15, 2, 4),
(33, '2018-12-09', '03:16:05', 'Pago de comprobante 0003-00000019', 4090, 4090, 1, 21, 2, 4),
(34, '2018-12-09', '03:16:10', 'Pago de comprobante 0003-00000018', 408, 408, 1, 18, 2, 4),
(35, '2018-12-09', '03:16:16', 'Pago de comprobante 0003-00000017', 1316, 1316, 1, 17, 2, 4),
(36, '2018-12-09', '03:16:20', 'Pago de comprobante 0003-00000016', 487, 487, 1, 16, 2, 4),
(37, '2018-12-09', '03:16:25', 'Pago de comprobante 0003-00000014', 1180, 1180, 1, 14, 2, 4),
(38, '2018-12-09', '03:16:30', 'Pago de comprobante 0003-00000011', 1177, 1177, 1, 11, 2, 4),
(39, '2018-12-09', '03:16:38', 'Pago de comprobante 0003-00000010', 1326, 1326, 1, 10, 2, 4),
(40, '2018-12-09', '03:16:46', 'Pago de comprobante 0003-00000009', 3000, 3000, 1, 9, 2, 4),
(41, '2018-12-09', '03:16:50', 'Pago de comprobante 0003-00000009', 2570, 2570, 1, 9, 2, 4),
(42, '2018-12-09', '03:17:29', 'Pago de comprobante 0003-00000008', 3237, 3237, 1, 8, 2, 4),
(43, '2018-12-09', '03:17:34', 'Pago de comprobante 0003-00000005', 3407, 3407, 1, 5, 2, 4),
(44, '2018-12-09', '03:17:39', 'Pago de comprobante 0003-00000007', 5442, 5442, 1, 7, 2, 4),
(45, '2018-12-09', '03:17:46', 'Pago de comprobante 0003-00000006', 2670, 2670, 1, 6, 2, 4),
(46, '2018-12-09', '03:17:53', 'Pago de comprobante 0003-00000004', 923, 923, 1, 4, 2, 4),
(47, '2018-12-09', '03:17:59', 'Pago de comprobante 0003-00000002', 175, 175, 1, 2, 2, 4),
(50, '2018-12-09', '03:28:11', 'Comprobante venta 0003-00000020', 7512.3, 0, 1, 24, 1, 4),
(53, '2018-12-09', '03:38:10', 'Comprobante venta 0003-00000021', 7512.3, 0, 1, 27, 1, 4),
(54, '2018-12-09', '03:39:41', 'Comprobante venta 0003-00000022', 15859.3, 0, 1, 28, 1, 4),
(55, '2018-12-09', '03:40:51', 'Comprobante venta 0003-00000023', 4509.59, 0, 1, 29, 1, 4),
(56, '2018-12-09', '03:41:16', 'Pago de comprobante 0003-00000020', 7512, 7512, 1, 24, 2, 4),
(57, '2018-12-09', '03:41:31', 'Pago de comprobante 0003-00000021', 5000, 5000, 1, 27, 2, 4),
(58, '2018-12-09', '03:41:48', 'Pago de comprobante 0003-00000021', 2512, 2512, 1, 27, 2, 4),
(59, '2018-12-09', '03:41:53', 'Pago de comprobante 0003-00000023', 4510, 4510, 1, 29, 2, 4),
(60, '2018-12-09', '03:41:59', 'Pago de comprobante 0003-00000022', 10000, 10000, 1, 28, 2, 4),
(61, '2018-12-09', '03:42:36', 'Pago de comprobante 0003-00000022', 5859, 5859, 1, 28, 2, 4),
(63, '2018-12-09', '04:06:36', 'Comprobante venta 0003-00000024', 167.14, 0, 1, 31, 1, 4),
(64, '2018-12-09', '04:19:57', 'Comprobante venta 0003-00000008', 167.14, 0, 1, 32, 1, 4),
(65, '2018-12-09', '22:12:07', 'Pago de comprobante 0003-00000008', 167, 167, 1, 32, 2, 4),
(66, '2018-12-11', '21:18:11', 'Comprobante venta 0003-00000011', 1164.6, 0, 1, 34, 1, 4),
(67, '2018-12-12', '23:05:16', 'Comprobante venta 0003-00000003', 525.19, 0, 1, 36, 1, 4),
(68, '2018-12-17', '21:39:57', 'Pago de comprobante 0003-00000024', 100, 100, 1, 31, 2, 4),
(69, '2018-12-17', '21:40:16', 'Pago de comprobante 0003-00000024', 67, 67, 1, 31, 2, 4),
(70, '2018-12-17', '23:20:57', 'Pago de comprobante 0003-00000003', 25, 25, 1, 36, 2, 4),
(71, '2019-01-06', '13:22:36', 'Pago de comprobante 0003-00000003', 500, 500, 1, 36, 2, 4),
(72, '2019-01-06', '13:22:52', 'Pago de comprobante 0003-00000011', 165, 165, 1, 34, 2, 4),
(73, '2019-01-06', '13:24:14', 'Pago de comprobante 0003-00000011', 100, 100, 1, 34, 2, 4),
(74, '2019-01-06', '13:25:03', 'Pago de comprobante 0003-00000011', 500, 500, 1, 34, 2, 4),
(75, '2019-01-06', '15:30:29', 'Pago de comprobante 0003-00000011', 400, 400, 1, 34, 2, 4),
(76, '2019-01-06', '15:45:29', 'Comprobante venta 0003-00000014', 475.27, 0, 1, 37, 1, 4),
(77, '2019-01-06', '20:33:17', 'Pago de comprobante 0003-00000014', 75.96, 75.96, 1, 37, 2, 4),
(78, '2019-01-06', '21:39:56', 'Comprobante venta 0003-00000027', 359.35, 0, 1, 39, 1, 4),
(79, '2019-01-06', '21:56:01', 'Comprobante venta 0003-00000028', 20867.5, 0, 1, 40, 1, 4),
(80, '2019-01-06', '21:56:28', 'Pago de comprobante 0003-00000014', 399, 399, 1, 37, 2, 4),
(81, '2019-01-06', '21:56:31', 'Pago de comprobante 0003-00000027', 359, 359, 1, 39, 2, 4),
(82, '2019-01-06', '21:56:36', 'Pago de comprobante 0003-00000028', 10000, 10000, 1, 40, 2, 4),
(83, '2019-01-09', '19:57:21', 'Pago de comprobante 0003-00000028', 5000, 5000, 1, 40, 2, 4),
(84, '2019-01-10', '21:10:09', 'Comprobante venta 0003-00000016', 7562.16, 0, 1, 42, 1, 4),
(85, '2019-01-10', '21:12:26', 'Comprobante venta 0003-00000016', 10674.4, 0, 1, 44, 1, 1),
(86, '2019-01-10', '21:23:15', 'Pago de comprobante 0003-00000028', 5868, 5868, 1, 40, 2, 4),
(87, '2019-01-10', '21:23:18', 'Pago de comprobante 0003-00000016', 7562, 7562, 1, 42, 2, 4),
(88, '2019-01-10', '21:23:26', 'Pago de comprobante 0003-00000016', 5674, 5674, 1, 44, 2, 3),
(89, '2019-01-10', '21:23:35', 'Pago de comprobante 0003-00000016', 1.44, 1.44, 1, 44, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentacorrienteproveedor`
--

CREATE TABLE IF NOT EXISTS `cuentacorrienteproveedor` (
`cuentacorrienteproveedor_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `referencia` text COLLATE utf8_spanish_ci,
  `importe` float DEFAULT NULL,
  `ingreso` float NOT NULL DEFAULT '0',
  `proveedor_id` int(11) DEFAULT NULL,
  `ingreso_id` int(11) DEFAULT NULL,
  `tipomovimientocuenta` int(11) DEFAULT NULL,
  `estadomovimientocuenta` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cuentacorrienteproveedor`
--

INSERT INTO `cuentacorrienteproveedor` (`cuentacorrienteproveedor_id`, `fecha`, `hora`, `referencia`, `importe`, `ingreso`, `proveedor_id`, `ingreso_id`, `tipomovimientocuenta`, `estadomovimientocuenta`) VALUES
(1, '2018-12-17', '20:57:04', 'Comprobante ingreso 0001-00023568', 1376.21, 0, 2, 2, 1, 4),
(2, '2018-12-17', '21:41:47', 'Pago de comprobante 0001-00023568', 376, 376, 2, 2, 2, 4),
(3, '2018-12-17', '21:42:31', 'Pago de comprobante 0001-00023568', 500.21, 500.21, 2, 2, 2, 4),
(4, '2018-12-17', '21:45:52', 'Comprobante ingreso 0001-00000044', 1579.85, 0, 1, 5, 1, 4),
(5, '2018-12-17', '22:18:22', 'Pago de comprobante 0001-00023568', 500, 500, 2, 2, 2, 4),
(6, '2019-01-06', '15:02:39', 'Pago de comprobante 0001-00000044', 580, 580, 1, 5, 2, 4),
(7, '2019-01-06', '15:02:46', 'Pago de comprobante 0001-00000044', 200, 200, 1, 5, 2, 4),
(8, '2019-01-06', '15:03:12', 'Pago de comprobante 0001-00000044', 100, 100, 1, 5, 2, 4),
(9, '2019-01-06', '15:10:18', 'Pago de comprobante 0001-00000044', 600, 600, 1, 5, 2, 4),
(10, '2019-01-06', '15:14:47', 'Pago de comprobante 0001-00000044', 100, 100, 1, 5, 2, 4),
(11, '2019-01-06', '21:21:45', 'Comprobante ingreso 0001-00000056', 56863.1, 0, 1, 6, 1, 4),
(12, '2019-01-06', '21:30:09', 'Pago de comprobante 0001-00000056', 863.93, 863.93, 1, 6, 2, 4),
(13, '2019-01-06', '21:30:35', 'Pago de comprobante 0001-00000056', 999, 999, 1, 6, 2, 4),
(14, '2019-01-08', '20:27:25', 'Pago de comprobante 0001-00000056', 55000, 55000, 1, 6, 2, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentotipo`
--

CREATE TABLE IF NOT EXISTS `documentotipo` (
`documentotipo_id` int(11) NOT NULL,
  `afip_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `documentotipo`
--

INSERT INTO `documentotipo` (`documentotipo_id`, `afip_id`, `denominacion`) VALUES
(1, 80, 'CUIT'),
(2, 86, 'CUIL'),
(3, 96, 'DNI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egreso`
--

CREATE TABLE IF NOT EXISTS `egreso` (
`egreso_id` int(11) NOT NULL,
  `punto_venta` int(4) DEFAULT NULL,
  `numero_factura` int(8) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `cliente` int(11) DEFAULT NULL,
  `vendedor` int(11) DEFAULT NULL,
  `tipofactura` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL,
  `condicionpago` int(11) DEFAULT NULL,
  `egresocomision` int(11) DEFAULT NULL,
  `egresoentrega` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egreso`
--

INSERT INTO `egreso` (`egreso_id`, `punto_venta`, `numero_factura`, `fecha`, `hora`, `descuento`, `subtotal`, `importe_total`, `cliente`, `vendedor`, `tipofactura`, `condicioniva`, `condicionpago`, `egresocomision`, `egresoentrega`) VALUES
(1, 3, 1, '2018-11-27', '22:09:03', 0, 20740.3, 20740.3, 1, 1, 2, 1, 1, 1, 1),
(2, 3, 2, '2018-11-27', '22:07:33', 5, 175.45, 175.45, 1, 1, 2, 1, 1, 2, 2),
(3, 3, 3, '2019-01-06', '22:04:41', 8, 11414.9, 14496.9, 1, 1, 2, 1, 1, 3, 3),
(4, 3, 4, '2018-11-27', '22:02:59', 5, 1245.22, 1245.22, 1, 1, 2, 1, 1, 4, 4),
(5, 3, 5, '2018-11-20', '23:43:03', 5, 3407.07, 3407.07, 1, 1, 2, 1, 1, 5, 5),
(6, 3, 6, '2018-10-27', '08:52:44', 5, 2669.77, 2669.77, 1, 1, 2, 1, 1, 6, 6),
(7, 3, 7, '2018-08-20', '20:11:02', 5, 5442.23, 5442.23, 1, 1, 2, 1, 1, 7, 7),
(8, 3, 8, '2018-10-26', '23:33:45', 5, 3237.31, 3237.31, 1, 1, 2, 1, 1, 8, 8),
(9, 3, 9, '2018-11-26', '23:38:38', 18.2, 5569.93, 5569.93, 1, 1, 2, 1, 1, 9, 9),
(10, 3, 10, '2018-11-27', '21:44:27', 0, 1325.63, 1325.63, 1, 1, 2, 1, 1, 10, 10),
(11, 3, 11, '2018-07-27', '22:10:11', 0, 1176.59, 1176.59, 1, 1, 2, 1, 1, 11, 11),
(12, 3, 12, '2018-11-14', '22:12:32', 0, 333.72, 333.72, 1, 1, 2, 1, 1, 12, 12),
(13, 3, 13, '2018-12-03', '22:22:17', 0, 350.16, 350.16, 1, 1, 2, 1, 1, 13, 13),
(14, 3, 14, '2018-12-03', '22:44:47', 0, 1180.48, 1180.48, 1, 1, 2, 1, 1, 14, 14),
(15, 3, 15, '2018-12-09', '01:02:08', 3, 1152.72, 1152.72, 1, 1, 2, 1, 1, 15, 15),
(16, 3, 16, '2018-08-09', '01:05:53', 0, 486.55, 486.55, 1, 1, 2, 1, 1, 16, 16),
(17, 3, 17, '2018-08-09', '01:08:22', 3, 1316.43, 1316.43, 1, 1, 2, 1, 1, 17, 17),
(18, 3, 18, '2018-08-09', '01:18:41', 2, 407.87, 407.87, 1, 1, 2, 1, 1, 18, 18),
(21, 3, 19, '2018-08-09', '01:29:21', 2, 4090.03, 4090.03, 1, 1, 2, 1, 1, 21, 21),
(24, 3, 20, '2018-09-09', '03:28:11', 10, 7512.3, 7512.3, 1, 1, 2, 1, 1, 24, 24),
(27, 3, 21, '2018-09-09', '03:38:10', 10, 7512.3, 7512.3, 1, 1, 2, 1, 1, 27, 27),
(28, 3, 22, '2018-10-09', '03:39:40', 5, 15859.3, 15859.3, 1, 1, 2, 1, 1, 28, 28),
(29, 3, 23, '2018-10-09', '03:54:57', 5, 2828.5, 2828.5, 1, 1, 2, 1, 1, 29, 29),
(31, 3, 24, '2018-12-09', '04:06:51', 0, 167.14, 167.14, 1, 1, 2, 1, 1, 31, 31),
(32, 3, 8, '2018-12-09', '04:19:56', 0, 167.14, 167.14, 1, 1, 3, 1, 1, 32, 32),
(33, 3, 25, '2019-01-11', '21:17:20', 0, 1188.37, 1188.37, 1, 1, 2, 1, 2, 33, 33),
(34, 3, 11, '2018-12-11', '21:18:10', 2, 1164.6, 1164.6, 1, 1, 3, 1, 1, 34, 34),
(35, 3, 12, '2018-12-11', '21:18:42', 3, 1080.35, 1080.35, 1, 1, 3, 1, 2, 35, 35),
(36, 3, 3, '2018-12-12', '23:05:15', 0, 525.19, 525.19, 1, 2, 1, 1, 1, 36, 36),
(37, 3, 14, '2019-01-06', '15:45:29', 5, 475.27, 475.27, 1, 1, 3, 1, 1, 37, 37),
(38, 3, 26, '2018-09-06', '20:50:37', 0, 756.16, 756.16, 1, 1, 2, 1, 2, 38, 38),
(39, 3, 27, '2019-01-06', '21:39:56', 0, 359.35, 359.35, 1, 2, 2, 1, 1, 39, 39),
(40, 3, 28, '2018-07-11', '21:56:01', 0, 20867.5, 20867.5, 1, 1, 2, 1, 1, 40, 40),
(41, 3, 29, '2018-10-08', '23:52:03', 0, 13059.5, 13059.5, 1, 1, 2, 1, 2, 41, 41),
(42, 3, 16, '2019-01-10', '21:10:09', 0, 7562.16, 7562.16, 1, 2, 3, 1, 1, 42, 42),
(43, 3, 16, '2019-01-10', '21:10:58', 0, 4161.9, 4161.9, 1, 2, 3, 1, 2, 43, 43),
(44, 3, 16, '2018-07-20', '21:12:26', 0, 10674.4, 10674.4, 1, 1, 3, 1, 1, 44, 44),
(45, 3, 16, '2018-07-20', '21:13:25', 5, 4793.13, 4793.13, 1, 2, 3, 1, 2, 45, 45);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresoafip`
--

CREATE TABLE IF NOT EXISTS `egresoafip` (
`egresoafip_id` int(11) NOT NULL,
  `punto_venta` int(11) NOT NULL,
  `numero_factura` int(11) NOT NULL,
  `tipofactura` int(11) NOT NULL,
  `cae` text COLLATE utf8_spanish_ci,
  `fecha` date NOT NULL,
  `vencimiento` date DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egresoafip`
--

INSERT INTO `egresoafip` (`egresoafip_id`, `punto_venta`, `numero_factura`, `tipofactura`, `cae`, `fecha`, `vencimiento`, `egreso_id`) VALUES
(1, 1, 1, 3, '68470700807590', '2018-11-24', '2018-11-30', 5),
(2, 1, 2, 3, '68470700807812', '2018-11-25', '2018-12-01', 7),
(3, 1, 3, 3, '68480700872719', '2018-11-26', '2018-12-02', 8),
(4, 1, 4, 3, '68480700874279', '2018-11-27', '2018-12-03', 9),
(5, 1, 5, 3, '68480700874282', '2018-11-27', '2018-12-03', 6),
(6, 1, 6, 3, '68480702724818', '2018-11-27', '2018-12-03', 10),
(7, 1, 7, 3, '68480702753099', '2018-11-27', '2018-12-03', 4),
(8, 1, 8, 3, '68480702753484', '2018-11-27', '2018-12-03', 2),
(9, 1, 9, 3, '68480702753586', '2018-11-27', '2018-12-03', 1),
(10, 1, 10, 3, '68480702753675', '2018-11-27', '2018-12-03', 11),
(11, 1, 11, 3, '68480702753879', '2018-11-27', '2018-12-07', 12),
(12, 3, 5, 3, '68494705372489', '2018-12-03', '2018-12-13', 13),
(13, 3, 1, 1, '68494705373862', '2018-12-03', '2018-12-13', 14),
(14, 3, 6, 3, '68494711026352', '2018-12-09', '2018-12-19', 15),
(15, 3, 2, 1, '68494711026365', '2018-12-09', '2018-12-19', 16),
(16, 3, 7, 3, '68494711026378', '2018-12-09', '2018-12-19', 17),
(17, 3, 8, 3, '68494711026381', '2018-12-09', '2018-12-19', 32),
(18, 3, 10, 3, '68504711298244', '2018-12-11', '2018-12-21', 33),
(19, 3, 11, 3, '68504711298346', '2018-12-11', '2018-12-21', 34),
(20, 3, 12, 3, '68504711298406', '2018-12-11', '2018-12-21', 35),
(21, 3, 14, 3, '69014719272867', '2019-01-06', '2019-01-16', 40),
(22, 3, 15, 3, '69024719891464', '2019-01-08', '2019-01-18', 41);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresocomision`
--

CREATE TABLE IF NOT EXISTS `egresocomision` (
`egresocomision_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `valor_comision` float NOT NULL,
  `valor_abonado` float DEFAULT NULL,
  `estadocomision` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egresocomision`
--

INSERT INTO `egresocomision` (`egresocomision_id`, `fecha`, `valor_comision`, `valor_abonado`, `estadocomision`) VALUES
(1, '2019-01-07', 5, 1037.02, 3),
(2, '2019-01-08', 5, 8.7725, 3),
(3, '2019-01-10', 5, 724.845, 3),
(4, '2019-01-08', 5, 62.261, 3),
(5, '2019-01-10', 5, 170.354, 3),
(6, '2019-01-08', 5, 133.488, 3),
(7, '2019-01-10', 5, 272.112, 3),
(8, '2019-01-08', 5, 161.865, 3),
(9, '2019-01-10', 5, 278.496, 3),
(10, '2019-01-08', 5, 66.2815, 3),
(11, '2019-01-08', 5, 58.8295, 3),
(12, '2019-01-10', 5, 16.686, 3),
(13, '2019-01-08', 5, 17.508, 3),
(14, '2019-01-08', 5, 59.024, 3),
(15, '2019-01-08', 5, 57.636, 3),
(16, '2019-01-08', 5, 24.3275, 3),
(17, '2019-01-08', 5, 65.8215, 3),
(18, '2019-01-07', 5, 20.3935, 3),
(19, '2018-12-09', 5, 0, 1),
(20, '2018-12-09', 5, 0, 1),
(21, '2019-01-06', 5, 204.501, 3),
(24, '2019-01-06', 5, 375.615, 3),
(27, '2019-01-06', 5, 375.615, 3),
(28, '2019-01-06', 5, 792.965, 3),
(29, '2019-01-06', 5, 141.425, 3),
(31, '2019-01-06', 5, 8.357, 3),
(32, '2019-01-06', 5, 8.357, 3),
(33, '2019-01-08', 5, 59.4185, 3),
(34, '2019-01-08', 5, 58.23, 3),
(35, '2019-01-08', 5, 54.0175, 3),
(36, '2018-12-12', 15, 0, 1),
(37, '2019-01-08', 5, 23.7635, 3),
(38, '2019-01-08', 5, 37.808, 3),
(39, '2019-01-06', 15, 0, 1),
(40, '2019-01-08', 5, 1043.38, 3),
(41, '2019-01-10', 5, 652.975, 3),
(42, '2019-01-10', 15, 0, 1),
(43, '2019-01-10', 15, 0, 1),
(44, '2019-01-10', 5, 533.72, 3),
(45, '2018-07-20', 15, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresodetalle`
--

CREATE TABLE IF NOT EXISTS `egresodetalle` (
`egresodetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento` float DEFAULT '0',
  `valor_descuento` float NOT NULL DEFAULT '0',
  `costo_producto` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL,
  `egresodetalleestado` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=320 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egresodetalle`
--

INSERT INTO `egresodetalle` (`egresodetalle_id`, `codigo_producto`, `descripcion_producto`, `cantidad`, `descuento`, `valor_descuento`, `costo_producto`, `iva`, `importe`, `producto_id`, `egreso_id`, `egresodetalleestado`) VALUES
(139, '20552', 'Fela Salame Crespon ', 1.369, 2, 4.53, 165.48, 21, 222.01, 1, 5, 1),
(140, '45678', 'Fela Jamón Cocido ', 2, 0, 0, 184.68, 21, 369.36, 2, 5, 1),
(141, '32145', 'Paladini Jamón Crudo ', 3.698, 2, 61.12, 826.43, 21, 2995.02, 3, 5, 1),
(151, '20552', 'Fela Salame Crespon ', 2.365, 0, 0, 165.48, 21, 391.36, 1, 7, 1),
(152, '45678', 'Fela Jamón Cocido ', 0.654, 0, 0, 184.68, 21, 120.78, 2, 7, 1),
(153, '32145', 'Paladini Jamón Crudo ', 6.356, 7, 367.7, 826.43, 21, 4885.09, 3, 7, 1),
(154, '45120', 'Fela Mortadela x 4.5 ', 2.354, 3, 10.25, 145.15, 21, 331.43, 4, 7, 1),
(159, '20552', 'Fela Salame Crespon ', 0.698, 0, 0, 165.48, 21, 115.51, 1, 8, 1),
(160, '45678', 'Fela Jamón Cocido ', 3.256, 0, 0, 184.68, 21, 601.32, 2, 8, 1),
(161, '32145', 'Paladini Jamón Crudo ', 3.256, 0, 0, 826.43, 21, 2690.86, 3, 8, 1),
(162, '20552', 'Fela Salame Crespon ', 4.356, 0, 0, 165.48, 21, 720.83, 1, 9, 1),
(163, '45678', 'Fela Jamón Cocido ', 4.562, 0, 0, 184.68, 21, 842.51, 2, 9, 1),
(164, '32145', 'Paladini Jamón Crudo ', 5.123, 0, 0, 826.43, 21, 4233.8, 3, 9, 1),
(165, '45120', 'Fela Mortadela x 4.5 ', 6.123, 0, 0, 145.15, 21, 888.75, 4, 9, 1),
(166, '7924', 'Paladini Morcilla x6 ', 0.654, 0, 0, 188.57, 21, 123.32, 5, 9, 1),
(167, '20552', 'Fela Salame Crespon ', 0.689, 1, 1.14, 165.48, 21, 112.88, 1, 6, 1),
(168, '45678', 'Fela Jamón Cocido ', 6.562, 1, 12.12, 184.68, 21, 1199.75, 2, 6, 1),
(169, '32145', 'Paladini Jamón Crudo ', 1.145, 1, 9.46, 826.43, 21, 936.8, 3, 6, 1),
(170, '45120', 'Fela Mortadela x 4.5 ', 0.895, 0, 0, 145.15, 21, 129.91, 4, 6, 1),
(171, '7924', 'Paladini Morcilla x6 ', 2.356, 3, 13.33, 188.57, 21, 430.94, 5, 6, 1),
(192, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 826.43, 21, 826.43, 3, 10, 1),
(193, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 145.15, 21, 145.15, 4, 10, 1),
(194, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 188.57, 21, 188.57, 5, 10, 1),
(195, '20552', 'Fela Salame Crespon ', 1, 0, 0, 165.48, 21, 165.48, 1, 10, 1),
(200, '20552', 'Fela Salame Crespon ', 1, 0, 0, 165.48, 21, 165.48, 1, 4, 1),
(201, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 184.68, 21, 184.68, 2, 4, 1),
(202, '32145', 'Paladini Jamón Crudo ', 0.752, 0, 0, 826.43, 21, 621.48, 3, 4, 1),
(203, '45120', 'Fela Mortadela x 4.5 ', 2.384, 2, 6.92, 145.15, 21, 339.12, 4, 4, 1),
(204, '20552', 'Fela Salame Crespon ', 5, 0, 0, 165.48, 21, 827.4, 1, 3, 1),
(205, '45678', 'Fela Jamón Cocido ', 5, 0, 0, 184.68, 21, 923.4, 2, 3, 1),
(206, '32145', 'Paladini Jamón Crudo ', 10, 0, 0, 826.43, 21, 8264.3, 3, 3, 1),
(207, '45120', 'Fela Mortadela x 4.5 ', 5, 0, 0, 145.15, 21, 725.75, 4, 3, 1),
(208, '90154', 'Paladini Queso Tybo ', 5, 0, 0, 333.32, 27, 1666.6, 21, 3, 1),
(209, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 184.68, 21, 184.68, 2, 2, 1),
(210, '20552', 'Fela Salame Crespon ', 0.235, 0, 0, 165.48, 21, 38.89, 1, 1, 1),
(211, '45678', 'Fela Jamón Cocido ', 4.568, 0, 0, 184.68, 21, 843.62, 2, 1, 1),
(212, '32145', 'Paladini Jamón Crudo ', 3.551, 0, 0, 826.43, 21, 2934.65, 3, 1, 1),
(213, '45120', 'Fela Mortadela x 4.5 ', 1.254, 0, 0, 145.15, 21, 182.02, 4, 1, 1),
(214, '7924', 'Paladini Morcilla x6 ', 30.245, 0, 0, 188.57, 21, 5703.3, 5, 1, 1),
(215, '32167', 'Paladini Hamburguesas Criollas x2 ', 0.258, 0, 0, 74.45, 21, 19.21, 6, 1, 1),
(216, '684961', 'Paladini Hamburguesas Cheddar y Panceta x2 ', 2.235, 0, 0, 87.6, 21, 195.79, 7, 1, 1),
(217, '65461', 'Paladini Hamburguesas Clásicas x2 ', 0.258, 0, 0, 71.55, 21, 18.46, 8, 1, 1),
(218, '654767', 'Paladini Hamburguesas Gigantes x2 ', 3.658, 0, 0, 99.42, 21, 363.68, 9, 1, 1),
(219, '32187', 'Paladini Hamburguesas Pollo Jamón y Queso x2 ', 1.215, 0, 0, 79.04, 21, 96.03, 10, 1, 1),
(220, '5646', 'Paladini Hamburguesas Soja x2 ', 2.154, 0, 0, 65.09, 21, 140.2, 11, 1, 1),
(221, '8721', 'Paladini Salame Crespon ', 0.874, 0, 0, 229.26, 21, 200.37, 12, 1, 1),
(222, '3218', 'Fela Vienna x12 ', 5.213, 0, 0, 61.44, 21, 320.29, 13, 1, 1),
(223, '3218', 'Fela Vienna x6 ', 6.214, 0, 0, 48.58, 21, 301.88, 14, 1, 1),
(224, '32187', 'Paladini Chorizos x6 ', 4.254, 0, 0, 198.5, 21, 844.42, 16, 1, 1),
(225, '7898', 'Fela Paleta  ', 3.256, 0, 0, 229.11, 21, 745.98, 17, 1, 1),
(226, '50240', 'Fela Queso Barra ', 7.214, 0, 0, 228.99, 21, 1651.93, 18, 1, 1),
(227, '60241', 'Paladini Queso Cheddar ', 5.236, 0, 0, 273.45, 21, 1431.78, 19, 1, 1),
(228, '80135', 'Paladini Queso Cremoso ', 9.235, 0, 0, 321.59, 21, 2969.88, 20, 1, 1),
(229, '90154', 'Paladini Queso Tybo ', 5.214, 0, 0, 333.32, 21, 1737.93, 21, 1, 1),
(230, '20552', 'Fela Salame Crespon ', 1, 0, 0, 165.48, 21, 165.48, 1, 11, 1),
(231, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 184.68, 21, 184.68, 2, 11, 1),
(232, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 826.43, 21, 826.43, 3, 11, 1),
(233, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 188.57, 21, 188.57, 5, 12, 1),
(234, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 145.15, 21, 145.15, 4, 12, 1),
(235, '20552', 'Fela Salame Crespon ', 1, 0, 0, 165.48, 21, 165.48, 1, 13, 1),
(236, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 184.68, 21, 184.68, 2, 13, 1),
(237, '20552', 'Fela Salame Crespon ', 1, 0, 0, 165.48, 21, 165.48, 1, 14, 1),
(238, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 826.43, 21, 826.43, 3, 14, 1),
(239, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 188.57, 21, 188.57, 5, 14, 1),
(240, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 15, 1),
(241, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 186.53, 21, 186.53, 2, 15, 1),
(242, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 834.7, 21, 834.7, 3, 15, 1),
(243, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 146.61, 21, 146.61, 4, 16, 1),
(244, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 190.46, 21, 190.46, 5, 16, 1),
(245, '654767', 'Paladini Hamburguesas Gigantes x2 ', 1, 0, 0, 100.41, 21, 100.41, 9, 16, 1),
(246, '3218', 'Fela Vienna x6 ', 1, 0, 0, 49.07, 21, 49.07, 14, 16, 1),
(247, '95135', 'Paladini Queso Fontina ', 1, 0, 0, 223, 21, 223, 23, 17, 1),
(248, '2314', 'Paladini Bondiola x4 ', 1, 0, 0, 196.51, 21, 196.51, 22, 17, 1),
(249, '90154', 'Paladini Queso Tybo ', 1, 0, 0, 336.65, 21, 336.65, 21, 17, 1),
(250, '80135', 'Paladini Queso Cremoso ', 1, 0, 0, 324.8, 21, 324.8, 20, 17, 1),
(251, '60241', 'Paladini Queso Cheddar ', 1, 0, 0, 276.18, 21, 276.18, 19, 17, 1),
(252, '32167', 'Paladini Hamburguesas Criollas x2 ', 1, 0, 0, 75.2, 21, 75.2, 6, 18, 1),
(253, '684961', 'Paladini Hamburguesas Cheddar y Panceta x2 ', 1, 0, 0, 88.48, 21, 88.48, 7, 18, 1),
(254, '65461', 'Paladini Hamburguesas Clásicas x2 ', 1, 0, 0, 72.27, 21, 72.27, 8, 18, 1),
(255, '654767', 'Paladini Hamburguesas Gigantes x2 ', 1, 0, 0, 100.41, 21, 100.41, 9, 18, 1),
(256, '32187', 'Paladini Hamburguesas Pollo Jamón y Queso x2 ', 1, 0, 0, 79.83, 21, 79.83, 10, 18, 1),
(260, '32145', 'Paladini Jamón Crudo ', 5, 0, 0, 834.7, 21, 4173.5, 3, 21, 1),
(263, '32145', 'Paladini Jamón Crudo ', 10, 0, 0, 834.7, 21, 8347, 3, 24, 1),
(266, '32145', 'Paladini Jamón Crudo ', 10, 0, 0, 834.7, 21, 8347, 3, 27, 1),
(267, '32145', 'Paladini Jamón Crudo ', 20, 0, 0, 834.7, 21, 16694, 3, 28, 1),
(270, '32145', 'Paladini Jamón Crudo ', 3.567, 0, 0, 834.7, 21, 2977.37, 3, 29, 1),
(271, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 31, 1),
(272, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 32, 1),
(273, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 33, 1),
(274, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 186.53, 21, 186.53, 2, 33, 1),
(275, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 834.7, 21, 834.7, 3, 33, 1),
(276, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 34, 1),
(277, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 186.53, 21, 186.53, 2, 34, 1),
(278, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 834.7, 21, 834.7, 3, 34, 1),
(279, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 35, 1),
(280, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 190.46, 21, 190.46, 5, 35, 1),
(281, '95135', 'Paladini Queso Fontina ', 1, 0, 0, 223, 21, 223, 23, 35, 1),
(282, '2314', 'Paladini Bondiola x4 ', 1, 0, 0, 196.51, 21, 196.51, 22, 35, 1),
(283, '90154', 'Paladini Queso Tybo ', 1, 0, 0, 336.65, 21, 336.65, 21, 35, 1),
(284, '8721', 'Paladini Salame Crespon ', 1, 0, 0, 231.55, 21, 231.55, 12, 36, 1),
(285, '3218', 'Fela Vienna x12 ', 1, 0, 0, 62.06, 21, 62.06, 13, 36, 1),
(286, '3218', 'Fela Vienna x6 ', 1, 0, 0, 49.07, 21, 49.07, 14, 36, 1),
(287, '1386', 'Fela Salchichón Primavera ', 1, 0, 0, 182.51, 21, 182.51, 15, 36, 1),
(288, '20552', 'Fela Salame Crespon ', 1, 0, 0, 167.14, 21, 167.14, 1, 37, 1),
(289, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 186.53, 21, 186.53, 2, 37, 1),
(290, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 146.61, 21, 146.61, 4, 37, 1),
(291, '90154', 'Paladini Queso Tybo ', 1, 0, 0, 336.65, 21, 336.65, 21, 38, 1),
(292, '2314', 'Paladini Bondiola x4 ', 1, 0, 0, 196.51, 21, 196.51, 22, 38, 1),
(293, '95135', 'Paladini Queso Fontina ', 1, 0, 0, 223, 21, 223, 23, 38, 1),
(294, '5646', 'Paladini Hamburguesas Soja x2 ', 1, 0, 0, 65.74, 21, 65.74, 11, 39, 1),
(295, '8721', 'Paladini Salame Crespon ', 1, 0, 0, 231.55, 21, 231.55, 12, 39, 1),
(296, '3218', 'Fela Vienna x12 ', 1, 0, 0, 62.06, 21, 62.06, 13, 39, 1),
(297, '32145', 'Paladini Jamón Crudo ', 25, 0, 0, 834.7, 21, 20867.5, 3, 40, 1),
(298, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 834.7, 21, 834.7, 3, 41, 1),
(299, '45120', 'Fela Mortadela x 4.5 ', 25, 0, 0, 146.61, 21, 3665.25, 4, 41, 1),
(300, '90154', 'Paladini Queso Tybo ', 12, 0, 0, 336.65, 21, 4039.8, 21, 41, 1),
(301, '2314', 'Paladini Bondiola x4 ', 23, 0, 0, 196.51, 21, 4519.73, 22, 41, 1),
(302, '7898', 'Fela Paleta  ', 5.698, 0, 0, 231.4, 21, 1318.52, 17, 42, 1),
(303, '50240', 'Fela Queso Barra ', 4.325, 0, 0, 231.28, 21, 1000.29, 18, 42, 1),
(304, '80135', 'Paladini Queso Cremoso ', 8.369, 0, 0, 324.8, 21, 2718.25, 20, 42, 1),
(305, '65461', 'Paladini Hamburguesas Clásicas x2 ', 10, 0, 0, 72.27, 21, 722.7, 8, 42, 1),
(306, '32187', 'Paladini Hamburguesas Pollo Jamón y Queso x2 ', 10, 0, 0, 79.83, 21, 798.3, 10, 42, 1),
(307, '654767', 'Paladini Hamburguesas Gigantes x2 ', 10, 0, 0, 100.41, 21, 1004.1, 9, 42, 1),
(308, '32167', 'Paladini Hamburguesas Criollas x2 ', 10, 0, 0, 75.2, 21, 752, 6, 43, 1),
(309, '684961', 'Paladini Hamburguesas Cheddar y Panceta x2 ', 10, 0, 0, 88.48, 21, 884.8, 7, 43, 1),
(310, '65461', 'Paladini Hamburguesas Clásicas x2 ', 10, 0, 0, 72.27, 21, 722.7, 8, 43, 1),
(311, '654767', 'Paladini Hamburguesas Gigantes x2 ', 10, 0, 0, 100.41, 21, 1004.1, 9, 43, 1),
(312, '32187', 'Paladini Hamburguesas Pollo Jamón y Queso x2 ', 10, 0, 0, 79.83, 21, 798.3, 10, 43, 1),
(313, '90154', 'Paladini Queso Tybo ', 15.354, 0, 0, 336.65, 21, 5168.92, 21, 44, 1),
(314, '2314', 'Paladini Bondiola x4 ', 10.452, 0, 0, 196.51, 21, 2053.92, 22, 44, 1),
(315, '95135', 'Paladini Queso Fontina ', 15.478, 0, 0, 223, 21, 3451.59, 23, 44, 1),
(316, '32167', 'Paladini Hamburguesas Criollas x2 ', 15, 0, 0, 75.2, 21, 1128, 6, 45, 1),
(317, '684961', 'Paladini Hamburguesas Cheddar y Panceta x2 ', 15, 0, 0, 88.48, 21, 1327.2, 7, 45, 1),
(318, '65461', 'Paladini Hamburguesas Clásicas x2 ', 15, 0, 0, 72.27, 21, 1084.05, 8, 45, 1),
(319, '654767', 'Paladini Hamburguesas Gigantes x2 ', 15, 0, 0, 100.41, 21, 1506.15, 9, 45, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresodetalleestado`
--

CREATE TABLE IF NOT EXISTS `egresodetalleestado` (
`egresodetalleestado_id` int(11) NOT NULL,
  `codigo` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egresodetalleestado`
--

INSERT INTO `egresodetalleestado` (`egresodetalleestado_id`, `codigo`, `denominacion`) VALUES
(1, 'PEN', 'PENDIENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresoentrega`
--

CREATE TABLE IF NOT EXISTS `egresoentrega` (
`egresoentrega_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `flete` int(11) DEFAULT NULL,
  `estadoentrega` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egresoentrega`
--

INSERT INTO `egresoentrega` (`egresoentrega_id`, `fecha`, `flete`, `estadoentrega`) VALUES
(1, '2018-11-10', 2, 4),
(2, '2018-11-11', 2, 4),
(3, '2018-11-16', 2, 4),
(4, '2018-11-21', 2, 4),
(5, '2018-11-21', 3, 4),
(6, '2018-11-25', 2, 4),
(7, '2018-11-25', 2, 4),
(8, '2018-11-27', 2, 4),
(9, '2018-11-27', 2, 4),
(10, '2018-11-28', 2, 4),
(11, '2018-11-28', 2, 4),
(12, '2018-11-15', 2, 4),
(13, '2019-01-06', 3, 4),
(14, '2019-01-06', 3, 4),
(15, '2019-01-06', 3, 4),
(16, '2019-01-06', 3, 4),
(17, '2019-01-07', 2, 4),
(18, '2019-01-07', 2, 4),
(19, '2018-12-10', 2, 2),
(20, '2018-12-10', 2, 2),
(21, '2019-01-07', 2, 4),
(24, '2019-01-07', 2, 4),
(27, '2019-01-07', 2, 1),
(28, '2019-01-07', 2, 1),
(29, '2019-01-07', 2, 4),
(31, '2019-01-07', 2, 4),
(32, '2019-01-07', 2, 4),
(33, '2018-12-12', 2, 4),
(34, '2019-01-06', 3, 1),
(35, '2018-12-12', 2, 4),
(36, '2019-01-06', 3, 1),
(37, '2019-01-07', 2, 2),
(38, '2019-01-07', 2, 4),
(39, '2019-01-10', 2, 1),
(40, '2019-01-07', 2, 2),
(41, '2019-01-09', 2, 2),
(42, '2019-01-11', 2, 2),
(43, '2019-01-11', 2, 2),
(44, '2018-07-21', 2, 2),
(45, '2018-07-21', 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadocomision`
--

CREATE TABLE IF NOT EXISTS `estadocomision` (
`estadocomision_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estadocomision`
--

INSERT INTO `estadocomision` (`estadocomision_id`, `denominacion`) VALUES
(1, 'PENDIENTE'),
(2, 'PARCIAL'),
(3, 'TOTAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoentrega`
--

CREATE TABLE IF NOT EXISTS `estadoentrega` (
`estadoentrega_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estadoentrega`
--

INSERT INTO `estadoentrega` (`estadoentrega_id`, `denominacion`) VALUES
(1, 'PENDIENTE'),
(2, 'PLANIFICADO'),
(3, 'EN RUTA'),
(4, 'ENTREGADO'),
(5, 'CANCELADO'),
(6, 'POSTERGADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadomovimientocuenta`
--

CREATE TABLE IF NOT EXISTS `estadomovimientocuenta` (
`estadomovimientocuenta_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estadomovimientocuenta`
--

INSERT INTO `estadomovimientocuenta` (`estadomovimientocuenta_id`, `denominacion`) VALUES
(1, 'PENDIENTE'),
(2, 'CERRADO'),
(3, 'PARCIAL'),
(4, 'ABONADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flete`
--

CREATE TABLE IF NOT EXISTS `flete` (
`flete_id` int(11) NOT NULL,
  `denominacion` text COLLATE utf8_spanish_ci,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `localidad` text COLLATE utf8_spanish_ci,
  `latitud` text COLLATE utf8_spanish_ci,
  `longitud` text COLLATE utf8_spanish_ci,
  `observacion` text COLLATE utf8_spanish_ci,
  `documentotipo` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `flete`
--

INSERT INTO `flete` (`flete_id`, `denominacion`, `documento`, `domicilio`, `localidad`, `latitud`, `longitud`, `observacion`, `documentotipo`) VALUES
(1, 'SIN DEFINIR', 0, ' ', ' ', '0', '0', ' ', 3),
(2, 'GRIEZMANN ANTOINE', 20326549876, '', 'CAPITAL', '', '', '', 1),
(3, 'KYLIAN MBAPPÉ', 0, '', '', '', '', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frecuenciaventa`
--

CREATE TABLE IF NOT EXISTS `frecuenciaventa` (
`frecuenciaventa_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dia_1` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dia_2` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `frecuenciaventa`
--

INSERT INTO `frecuenciaventa` (`frecuenciaventa_id`, `denominacion`, `dia_1`, `dia_2`) VALUES
(1, 'Frecuencia 1', 'Lunes', 'Jueves'),
(2, 'Frecuencia 2', 'Martes', 'Viernes'),
(3, 'Frecuencia 3', 'Miércoles', 'Sábado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hojaruta`
--

CREATE TABLE IF NOT EXISTS `hojaruta` (
`hojaruta_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `flete_id` int(11) DEFAULT NULL,
  `egreso_ids` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estadoentrega` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `hojaruta`
--

INSERT INTO `hojaruta` (`hojaruta_id`, `fecha`, `flete_id`, `egreso_ids`, `estadoentrega`) VALUES
(2, '2019-01-06', 3, '15@4,16@4,34@1,36@1,14@4,13@4', 4),
(3, '2019-01-07', 2, '17@4,18@4,21@4,24@4,27@1,28@1,29@4,31@4,32@4', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontacto`
--

CREATE TABLE IF NOT EXISTS `infocontacto` (
`infocontacto_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valor` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `infocontacto`
--

INSERT INTO `infocontacto` (`infocontacto_id`, `denominacion`, `valor`) VALUES
(1, 'Teléfono', ''),
(2, 'Celular', ''),
(3, 'Email', ''),
(4, 'Teléfono', ''),
(5, 'Celular', ''),
(6, 'Email', ''),
(7, 'Teléfono', ''),
(8, 'Celular', ''),
(9, 'Email', ''),
(10, 'Teléfono', ''),
(11, 'Celular', ''),
(12, 'Email', ''),
(13, 'Teléfono', ''),
(14, 'Celular', ''),
(15, 'Email', ''),
(16, 'Teléfono', ''),
(17, 'Celular', ''),
(18, 'Email', ''),
(19, 'Teléfono', ''),
(20, 'Celular', ''),
(21, 'Email', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactocliente`
--

CREATE TABLE IF NOT EXISTS `infocontactocliente` (
`infocontactocliente_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `infocontactocliente`
--

INSERT INTO `infocontactocliente` (`infocontactocliente_id`, `compuesto`, `compositor`) VALUES
(1, 1, 7),
(2, 1, 8),
(3, 1, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactoflete`
--

CREATE TABLE IF NOT EXISTS `infocontactoflete` (
`infocontactoflete_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `infocontactoflete`
--

INSERT INTO `infocontactoflete` (`infocontactoflete_id`, `compuesto`, `compositor`) VALUES
(1, 2, 1),
(2, 2, 2),
(3, 2, 3),
(4, 3, 13),
(5, 3, 14),
(6, 3, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactoproveedor`
--

CREATE TABLE IF NOT EXISTS `infocontactoproveedor` (
`infocontactoproveedor_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `infocontactoproveedor`
--

INSERT INTO `infocontactoproveedor` (`infocontactoproveedor_id`, `compuesto`, `compositor`) VALUES
(1, 1, 10),
(2, 1, 11),
(3, 1, 12),
(4, 2, 16),
(5, 2, 17),
(6, 2, 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactovendedor`
--

CREATE TABLE IF NOT EXISTS `infocontactovendedor` (
`infocontactovendedor_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `infocontactovendedor`
--

INSERT INTO `infocontactovendedor` (`infocontactovendedor_id`, `compuesto`, `compositor`) VALUES
(1, 1, 4),
(2, 1, 5),
(3, 1, 6),
(4, 2, 19),
(5, 2, 20),
(6, 2, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE IF NOT EXISTS `ingreso` (
`ingreso_id` int(11) NOT NULL,
  `punto_venta` int(4) DEFAULT NULL,
  `numero_factura` int(8) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `costo_distribucion` float DEFAULT NULL,
  `costo_total` float DEFAULT NULL,
  `costo_total_iva` float DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL,
  `condicionpago` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ingreso`
--

INSERT INTO `ingreso` (`ingreso_id`, `punto_venta`, `numero_factura`, `fecha`, `hora`, `iva`, `costo_distribucion`, `costo_total`, `costo_total_iva`, `proveedor`, `condicioniva`, `condicionpago`) VALUES
(1, 1, 1, '2018-11-09', '22:28:36', 21, 5, 168963, 204445, 1, 1, 2),
(2, 1, 23568, '2018-12-17', '20:57:04', 21, 5, 1137.37, 1376.21, 2, 1, 1),
(3, 1, 2, '2018-12-17', '21:43:12', 21, 5, 1257.74, 1521.87, 1, 1, 1),
(4, 1, 3, '2018-12-17', '21:44:42', 21, 5, 1257.74, 1521.87, 1, 1, 1),
(5, 1, 44, '2018-12-17', '21:45:52', 21, 9, 1305.66, 1579.85, 1, 1, 1),
(6, 1, 56, '2019-01-06', '21:21:45', 21, 12, 46994.3, 56863.1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresodetalle`
--

CREATE TABLE IF NOT EXISTS `ingresodetalle` (
`ingresodetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento1` float DEFAULT NULL,
  `descuento2` float DEFAULT NULL,
  `descuento3` float DEFAULT NULL,
  `costo_producto` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `ingreso_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ingresodetalle`
--

INSERT INTO `ingresodetalle` (`ingresodetalle_id`, `codigo_producto`, `descripcion_producto`, `cantidad`, `descuento1`, `descuento2`, `descuento3`, `costo_producto`, `importe`, `producto_id`, `ingreso_id`) VALUES
(1, '20552', 'Fela Salame Crespon ', 50, 0, 0, 0, 130.25, 6512.5, 1, 1),
(2, '45678', 'Fela Jamón Cocido ', 50, 0, 0, 0, 145.36, 7268, 2, 1),
(3, '32145', 'Paladini Jamón Crudo ', 50, 0, 0, 0, 650.48, 32524, 3, 1),
(4, '45120', 'Fela Mortadela x 4.5 ', 50, 0, 0, 0, 114.25, 5712.5, 4, 1),
(5, '7924', 'Paladini Morcilla x6 ', 50, 0, 0, 0, 145.65, 7282.5, 5, 1),
(6, '32167', 'Paladini Hamburguesas Criollas x2 ', 50, 0, 0, 0, 58.6, 2930, 6, 1),
(7, '684961', 'Paladini Hamburguesas Cheddar y Panceta x2 ', 50, 0, 0, 0, 68.95, 3447.5, 7, 1),
(8, '65461', 'Paladini Hamburguesas Clásicas x2 ', 50, 0, 0, 0, 56.32, 2816, 8, 1),
(9, '654767', 'Paladini Hamburguesas Gigantes x2 ', 50, 0, 0, 0, 78.25, 3912.5, 9, 1),
(10, '32187', 'Paladini Hamburguesas Pollo Jamón y Queso x2 ', 50, 0, 0, 0, 62.21, 3110.5, 10, 1),
(11, '5646', 'Paladini Hamburguesas Soja x2 ', 50, 0, 0, 0, 51.23, 2561.5, 11, 1),
(12, '8721', 'Paladini Salame Crespon ', 50, 0, 0, 0, 180.45, 9022.5, 12, 1),
(13, '3218', 'Fela Vienna x12 ', 50, 0, 0, 0, 48.36, 2418, 13, 1),
(14, '3218', 'Fela Vienna x6 ', 50, 0, 0, 0, 38.24, 1912, 14, 1),
(15, '1386', 'Fela Salchichón Primavera ', 50, 0, 0, 0, 142.23, 7111.5, 15, 1),
(16, '32187', 'Paladini Chorizos x6 ', 50, 0, 0, 0, 156.24, 7812, 16, 1),
(17, '7898', 'Fela Paleta  ', 50, 0, 0, 0, 180.33, 9016.5, 17, 1),
(18, '50240', 'Fela Queso Barra ', 50, 0, 0, 0, 180.24, 9012, 18, 1),
(19, '60241', 'Paladini Queso Cheddar ', 50, 0, 0, 0, 215.23, 10761.5, 19, 1),
(20, '80135', 'Paladini Queso Cremoso ', 50, 0, 0, 0, 253.12, 12656, 20, 1),
(21, '90154', 'Paladini Queso Tybo ', 50, 0, 0, 0, 262.35, 13117.5, 21, 1),
(22, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 0, 146.814, 146.814, 2, 2),
(23, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 0, 147.107, 147.107, 5, 2),
(24, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 0, 115.393, 115.393, 4, 2),
(25, '20552', 'Fela Salame Crespon ', 1, 0, 0, 0, 131.553, 131.553, 1, 2),
(26, '90154', 'Paladini Queso Tybo ', 1, 0, 0, 0, 264.974, 264.974, 21, 2),
(27, '2314', 'Paladini Bondiola x4 ', 1, 0, 0, 0, 129.926, 129.926, 22, 2),
(28, '95135', 'Paladini Queso Fontina ', 1, 0, 0, 0, 147.44, 147.44, 23, 2),
(29, '20552', 'Fela Salame Crespon ', 1, 0, 0, 0, 131.553, 131.553, 1, 3),
(30, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 0, 146.814, 146.814, 2, 3),
(31, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 0, 656.985, 656.985, 3, 3),
(32, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 0, 115.393, 115.393, 4, 3),
(33, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 0, 147.107, 147.107, 5, 3),
(34, '20552', 'Fela Salame Crespon ', 1, 0, 0, 0, 131.553, 131.553, 1, 4),
(35, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 0, 146.814, 146.814, 2, 4),
(36, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 0, 656.985, 656.985, 3, 4),
(37, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 0, 115.393, 115.393, 4, 4),
(38, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 0, 147.107, 147.107, 5, 4),
(39, '20552', 'Fela Salame Crespon ', 1, 0, 0, 0, 131.553, 131.553, 1, 5),
(40, '45678', 'Fela Jamón Cocido ', 1, 0, 0, 0, 146.814, 146.814, 2, 5),
(41, '32145', 'Paladini Jamón Crudo ', 1, 0, 0, 0, 656.985, 656.985, 3, 5),
(42, '45120', 'Fela Mortadela x 4.5 ', 1, 0, 0, 0, 115.393, 115.393, 4, 5),
(43, '7924', 'Paladini Morcilla x6 ', 1, 0, 0, 0, 147.107, 147.107, 5, 5),
(44, '32145', 'Paladini Jamón Crudo ', 50, 0, 0, 0, 656.985, 32849.2, 3, 6),
(45, '45120', 'Fela Mortadela x 4.5 ', 25, 0, 0, 0, 115.393, 2884.82, 4, 6),
(46, '20552', 'Fela Salame Crespon ', 25, 0, 0, 0, 131.553, 3288.82, 1, 6),
(47, '45678', 'Fela Jamón Cocido ', 20, 0, 0, 0, 146.814, 2936.28, 2, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

CREATE TABLE IF NOT EXISTS `item` (
`item_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `submenu` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `item`
--

INSERT INTO `item` (`item_id`, `denominacion`, `detalle`, `url`, `submenu`) VALUES
(1, 'Panel', 'Menú', '/menu/panel', 8),
(2, 'Agregar Ítems', 'Agregar Ítems', '/menu/agregar', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `itemconfiguracionmenu`
--

CREATE TABLE IF NOT EXISTS `itemconfiguracionmenu` (
`itemconfiguracionmenu_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `itemconfiguracionmenu`
--

INSERT INTO `itemconfiguracionmenu` (`itemconfiguracionmenu_id`, `compuesto`, `compositor`) VALUES
(139, 1, 1),
(140, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `localidad`
--

CREATE TABLE IF NOT EXISTS `localidad` (
`localidad_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
`menu_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`menu_id`, `denominacion`, `icon`, `url`) VALUES
(3, 'STOCK', 'fa-archive', '/stock/panel'),
(4, 'CONFIGURACIÓN', 'fa-cogs', '#'),
(7, 'PROVEEDORES', 'fa-briefcase', '#'),
(8, 'PRODUCTOS', 'fa-archive', '#'),
(9, 'CLIENTES', 'fa-briefcase', '#'),
(10, 'VENTAS', 'fa-usd', '#'),
(11, 'OTROS', 'fa-cogs', '#'),
(12, 'VENDEDORES', 'fa-briefcase', '#'),
(13, 'FLETES', 'fa-truck', '#'),
(14, 'INGRESOS', 'fa-archive', '#');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientotipo`
--

CREATE TABLE IF NOT EXISTS `movimientotipo` (
`movimientotipo_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `movimientotipo`
--

INSERT INTO `movimientotipo` (`movimientotipo_id`, `denominacion`) VALUES
(1, 'Ingreso'),
(2, 'Egreso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notacredito`
--

CREATE TABLE IF NOT EXISTS `notacredito` (
`notacredito_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL,
  `tipofactura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notacreditodetalle`
--

CREATE TABLE IF NOT EXISTS `notacreditodetalle` (
`notacreditodetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `valor_descuento` float DEFAULT NULL,
  `costo_producto` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `egreso_id` int(11) NOT NULL,
  `notacredito_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE IF NOT EXISTS `producto` (
`producto_id` int(11) NOT NULL,
  `codigo` bigint(20) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `costo` float NOT NULL,
  `descuento` float NOT NULL DEFAULT '0',
  `porcentaje_ganancia` float NOT NULL,
  `iva` float NOT NULL,
  `exento` int(11) NOT NULL DEFAULT '0',
  `no_gravado` int(11) NOT NULL DEFAULT '0',
  `stock_minimo` int(11) NOT NULL,
  `stock_ideal` int(11) NOT NULL,
  `dias_reintegro` int(11) NOT NULL,
  `detalle` text COLLATE utf8_spanish_ci,
  `productomarca` int(11) DEFAULT NULL,
  `productocategoria` int(11) DEFAULT NULL,
  `productounidad` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`producto_id`, `codigo`, `denominacion`, `costo`, `descuento`, `porcentaje_ganancia`, `iva`, `exento`, `no_gravado`, `stock_minimo`, `stock_ideal`, `dias_reintegro`, `detalle`, `productomarca`, `productocategoria`, `productounidad`) VALUES
(1, 20552, 'Salame Crespon', 131.553, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 1, 1),
(2, 45678, 'Jamón Cocido', 146.814, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 1, 1),
(3, 32145, 'Jamón Crudo', 656.985, 0, 5, 21, 0, 0, 30, 70, 7, '', 2, 1, 1),
(4, 45120, 'Mortadela x 4.5', 115.393, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 1, 1),
(5, 7924, 'Morcilla x6', 147.107, 0, 7, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(6, 32167, 'Hamburguesas Criollas x2', 59.186, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(7, 684961, 'Hamburguesas Cheddar y Panceta x2', 69.6395, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(8, 65461, 'Hamburguesas Clásicas x2', 56.8832, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(9, 654767, 'Hamburguesas Gigantes x2', 79.0325, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(10, 32187, 'Hamburguesas Pollo Jamón y Queso x2', 62.8321, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(11, 5646, 'Hamburguesas Soja x2', 51.7423, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(12, 8721, 'Salame Crespon', 182.255, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 1, 1),
(13, 3218, 'Vienna x12', 48.8436, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 3, 5),
(14, 3218, 'Vienna x6', 38.6224, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 3, 5),
(15, 1386, 'Salchichón Primavera', 143.652, 0, 5, 21, 0, 0, 40, 80, 7, '', 1, 1, 1),
(16, 32187, 'Chorizos x6', 157.802, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 3, 5),
(17, 7898, 'Paleta ', 182.133, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 1, 1),
(18, 50240, 'Queso Barra', 182.042, 0, 5, 21, 0, 0, 50, 100, 7, '', 1, 2, 1),
(19, 60241, 'Queso Cheddar', 217.382, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 2, 1),
(20, 80135, 'Queso Cremoso', 255.651, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 2, 1),
(21, 90154, 'Queso Tybo', 264.974, 0, 5, 21, 0, 0, 50, 100, 7, '', 2, 2, 1),
(22, 2314, 'Bondiola x4', 129.926, 0, 25, 21, 0, 0, 50, 100, 7, '', 2, 1, 1),
(23, 95135, 'Queso Fontina', 147.44, 0, 25, 21, 0, 0, 50, 100, 7, '', 2, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productocategoria`
--

CREATE TABLE IF NOT EXISTS `productocategoria` (
`productocategoria_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productocategoria`
--

INSERT INTO `productocategoria` (`productocategoria_id`, `denominacion`, `detalle`) VALUES
(1, 'Fiambres', ''),
(2, 'Quesos', ''),
(3, 'Congelados', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productodetalle`
--

CREATE TABLE IF NOT EXISTS `productodetalle` (
`productodetalle_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `precio_costo` float DEFAULT NULL,
  `producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productodetalle`
--

INSERT INTO `productodetalle` (`productodetalle_id`, `fecha`, `precio_costo`, `producto_id`, `proveedor_id`) VALUES
(2, '2018-11-09', 145.36, 2, 1),
(3, '2018-11-09', 650.48, 3, 1),
(4, '2018-11-09', 114.25, 4, 1),
(5, '2018-11-09', 145.65, 5, 1),
(6, '2018-11-09', 58.6, 6, 1),
(7, '2018-11-09', 68.95, 7, 1),
(8, '2018-11-09', 56.32, 8, 1),
(9, '2018-11-09', 78.25, 9, 1),
(10, '2018-11-09', 62.21, 10, 1),
(11, '2018-11-09', 51.23, 11, 1),
(12, '2018-11-09', 180.45, 12, 1),
(13, '2018-11-09', 48.36, 13, 1),
(14, '2018-11-09', 38.24, 14, 1),
(15, '2018-11-09', 142.23, 15, 1),
(16, '2018-11-09', 156.24, 16, 1),
(17, '2018-11-09', 180.33, 17, 1),
(18, '2018-11-09', 180.24, 18, 1),
(19, '2018-11-09', 215.23, 19, 1),
(20, '2018-11-09', 253.12, 20, 1),
(21, '2018-11-09', 262.35, 21, 1),
(22, '2018-12-04', 145.98, 23, 1),
(23, '2018-12-04', 128.64, 22, 1),
(25, '2018-12-07', 146.814, 2, 1),
(26, '2018-12-07', 656.985, 3, 1),
(27, '2018-12-07', 115.393, 4, 1),
(28, '2018-12-07', 147.107, 5, 1),
(29, '2018-12-07', 59.186, 6, 1),
(30, '2018-12-07', 69.6395, 7, 1),
(31, '2018-12-07', 56.8832, 8, 1),
(32, '2018-12-07', 79.0325, 9, 1),
(33, '2018-12-07', 62.8321, 10, 1),
(34, '2018-12-07', 51.7423, 11, 1),
(35, '2018-12-07', 182.255, 12, 1),
(36, '2018-12-07', 48.8436, 13, 1),
(37, '2018-12-07', 38.6224, 14, 1),
(38, '2018-12-07', 143.652, 15, 1),
(39, '2018-12-07', 157.802, 16, 1),
(40, '2018-12-07', 182.133, 17, 1),
(41, '2018-12-07', 182.042, 18, 1),
(42, '2018-12-07', 217.382, 19, 1),
(43, '2018-12-07', 255.651, 20, 1),
(44, '2018-12-07', 264.974, 21, 1),
(45, '2018-12-07', 129.926, 22, 1),
(46, '2018-12-07', 147.44, 23, 1),
(47, '2018-12-07', 131.553, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productomarca`
--

CREATE TABLE IF NOT EXISTS `productomarca` (
`productomarca_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productomarca`
--

INSERT INTO `productomarca` (`productomarca_id`, `denominacion`, `detalle`) VALUES
(1, 'Fela', ''),
(2, 'Paladini', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productounidad`
--

CREATE TABLE IF NOT EXISTS `productounidad` (
`productounidad_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productounidad`
--

INSERT INTO `productounidad` (`productounidad_id`, `denominacion`, `detalle`) VALUES
(1, 'kg', 'Kilos'),
(3, 'lts', 'Litros'),
(5, 'un', 'Unidades');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE IF NOT EXISTS `proveedor` (
`proveedor_id` int(11) NOT NULL,
  `razon_social` text COLLATE utf8_spanish_ci,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `codigopostal` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `localidad` text COLLATE utf8_spanish_ci,
  `observacion` text COLLATE utf8_spanish_ci NOT NULL,
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`proveedor_id`, `razon_social`, `documento`, `domicilio`, `codigopostal`, `localidad`, `observacion`, `provincia`, `documentotipo`, `condicioniva`) VALUES
(1, 'PALADINI', 30154879654, '', '', '', '', 1, 1, 1),
(2, 'NORTE DISTRIBUCIONES', 20134879866, '', '', '', '', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedorproducto`
--

CREATE TABLE IF NOT EXISTS `proveedorproducto` (
`proveedorproducto_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincia`
--

CREATE TABLE IF NOT EXISTS `provincia` (
`provincia_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `provincia`
--

INSERT INTO `provincia` (`provincia_id`, `denominacion`) VALUES
(1, 'Buenos Aires'),
(2, 'Catamarca'),
(3, 'Chaco'),
(4, 'Chubut'),
(5, 'Córdoba'),
(6, 'Corrientes'),
(7, 'Entre Ríos'),
(8, 'Formosa'),
(9, 'Jujuy'),
(10, 'La Pampa'),
(11, 'La Rioja'),
(12, 'Mendoza'),
(13, 'Misiones'),
(14, 'Neuquén'),
(15, 'Río Negro'),
(16, 'Salta'),
(17, 'San Juan'),
(18, 'San Luis'),
(19, 'Santa Cruz'),
(20, 'Santa Fe'),
(21, 'Santiago del Estero'),
(22, 'Tierra del Fuego, Antártida e Islas del Atlántico Sur'),
(23, 'Tucumán');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
`stock_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `concepto` text COLLATE utf8_spanish_ci,
  `codigo` bigint(20) DEFAULT NULL,
  `cantidad_actual` float DEFAULT NULL,
  `cantidad_movimiento` float DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=547 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`stock_id`, `fecha`, `hora`, `concepto`, `codigo`, `cantidad_actual`, `cantidad_movimiento`, `producto_id`) VALUES
(1, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 20552, 50, 50, 1),
(2, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 45678, 50, 50, 2),
(3, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 32145, 50, 50, 3),
(4, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 45120, 50, 50, 4),
(5, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 7924, 50, 50, 5),
(6, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 32167, 50, 50, 6),
(7, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 684961, 50, 50, 7),
(8, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 65461, 50, 50, 8),
(9, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 654767, 50, 50, 9),
(10, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 32187, 50, 50, 10),
(11, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 5646, 50, 50, 11),
(12, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 8721, 50, 50, 12),
(13, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 3218, 50, 50, 13),
(14, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 3218, 50, 50, 14),
(15, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 1386, 50, 50, 15),
(16, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 32187, 50, 50, 16),
(17, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 7898, 50, 50, 17),
(18, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 50240, 50, 50, 18),
(19, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 60241, 50, 50, 19),
(20, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 80135, 50, 50, 20),
(21, '2018-11-09', '22:28:36', 'Ingreso. Comprobante: 0001-00000001', 90154, 50, 50, 21),
(22, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 20552, 49.765, -0.235, 1),
(23, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 45678, 45.432, -4.568, 2),
(24, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 32145, 46.449, -3.551, 3),
(25, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 45120, 48.746, -1.254, 4),
(26, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 7924, 19.755, -30.245, 5),
(27, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 32167, 49.742, -0.258, 6),
(28, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 684961, 47.765, -2.235, 7),
(29, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 65461, 49.742, -0.258, 8),
(30, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 654767, 46.342, -3.658, 9),
(31, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 32187, 48.785, -1.215, 10),
(32, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 5646, 47.846, -2.154, 11),
(33, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 8721, 49.126, -0.874, 12),
(34, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 3218, 44.787, -5.213, 13),
(35, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 3218, 43.786, -6.214, 14),
(36, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 32187, 45.746, -4.254, 16),
(37, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 7898, 46.744, -3.256, 17),
(38, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 50240, 42.786, -7.214, 18),
(39, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 60241, 44.764, -5.236, 19),
(40, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 80135, 40.765, -9.235, 20),
(41, '2018-11-09', '22:40:23', 'Venta. Comprobante: 0001-00000001', 90154, 44.786, -5.214, 21),
(42, '2018-11-10', '01:07:21', 'Venta. Comprobante: 0001-00000002', 45678, 44.432, -1, 2),
(43, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 20552, 50, 0.235, 1),
(44, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 45678, 49, 4.568, 2),
(45, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32145, 50, 3.551, 3),
(46, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 45120, 50, 1.254, 4),
(47, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 7924, 50, 30.245, 5),
(48, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32167, 50, 0.258, 6),
(49, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 684961, 50, 2.235, 7),
(50, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 65461, 50, 0.258, 8),
(51, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 654767, 50, 3.658, 9),
(52, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32187, 50, 1.215, 10),
(53, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 5646, 50, 2.154, 11),
(54, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 8721, 50, 0.874, 12),
(55, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 3218, 50, 5.213, 13),
(56, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 3218, 50, 6.214, 14),
(57, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32187, 50, 4.254, 16),
(58, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 7898, 50, 3.256, 17),
(59, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 50240, 50, 7.214, 18),
(60, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 60241, 50, 5.236, 19),
(61, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 80135, 50, 9.235, 20),
(62, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 90154, 50, 5.214, 21),
(63, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 20552, 49.765, -0.235, 1),
(64, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 45678, 44.432, -4.568, 2),
(65, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32145, 46.449, -3.551, 3),
(66, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 45120, 48.746, -1.254, 4),
(67, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 7924, 19.755, -30.245, 5),
(68, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32167, 49.742, -0.258, 6),
(69, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 684961, 47.765, -2.235, 7),
(70, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 65461, 49.742, -0.258, 8),
(71, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 654767, 46.342, -3.658, 9),
(72, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32187, 48.785, -1.215, 10),
(73, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 5646, 47.846, -2.154, 11),
(74, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 8721, 49.126, -0.874, 12),
(75, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 3218, 44.787, -5.213, 13),
(76, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 3218, 43.786, -6.214, 14),
(77, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 32187, 45.746, -4.254, 16),
(78, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 7898, 46.744, -3.256, 17),
(79, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 50240, 42.786, -7.214, 18),
(80, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 60241, 44.764, -5.236, 19),
(81, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 80135, 40.765, -9.235, 20),
(82, '2018-11-09', '19:07:08', 'Edición Venta. Comprobante: 0001-00000003', 90154, 44.786, -5.214, 21),
(83, '2018-11-10', '19:10:56', 'Edición Venta. Comprobante: 0001-00000002', 45678, 45.432, 1, 2),
(84, '2018-11-10', '19:10:56', 'Edición Venta. Comprobante: 0001-00000002', 45678, 44.432, -1, 2),
(85, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 20552, 50, 0.235, 1),
(86, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 45678, 49, 4.568, 2),
(87, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32145, 50, 3.551, 3),
(88, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 45120, 50, 1.254, 4),
(89, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 7924, 50, 30.245, 5),
(90, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32167, 50, 0.258, 6),
(91, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 684961, 50, 2.235, 7),
(92, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 65461, 50, 0.258, 8),
(93, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 654767, 50, 3.658, 9),
(94, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32187, 50, 1.215, 10),
(95, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 5646, 50, 2.154, 11),
(96, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 8721, 50, 0.874, 12),
(97, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 3218, 50, 5.213, 13),
(98, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 3218, 50, 6.214, 14),
(99, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32187, 50, 4.254, 16),
(100, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 7898, 50, 3.256, 17),
(101, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 50240, 50, 7.214, 18),
(102, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 60241, 50, 5.236, 19),
(103, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 80135, 50, 9.235, 20),
(104, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 90154, 50, 5.214, 21),
(105, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 20552, 49.765, -0.235, 1),
(106, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 45678, 44.432, -4.568, 2),
(107, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32145, 46.449, -3.551, 3),
(108, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 45120, 48.746, -1.254, 4),
(109, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 7924, 19.755, -30.245, 5),
(110, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32167, 49.742, -0.258, 6),
(111, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 684961, 47.765, -2.235, 7),
(112, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 65461, 49.742, -0.258, 8),
(113, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 654767, 46.342, -3.658, 9),
(114, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32187, 48.785, -1.215, 10),
(115, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 5646, 47.846, -2.154, 11),
(116, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 8721, 49.126, -0.874, 12),
(117, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 3218, 44.787, -5.213, 13),
(118, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 3218, 43.786, -6.214, 14),
(119, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 32187, 45.746, -4.254, 16),
(120, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 7898, 46.744, -3.256, 17),
(121, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 50240, 42.786, -7.214, 18),
(122, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 60241, 44.764, -5.236, 19),
(123, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 80135, 40.765, -9.235, 20),
(124, '2018-11-09', '20:43:48', 'Edición Venta. Comprobante: 0001-00000003', 90154, 44.786, -5.214, 21),
(125, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 20552, 50, 0.235, 1),
(126, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 45678, 49, 4.568, 2),
(127, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32145, 50, 3.551, 3),
(128, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 45120, 50, 1.254, 4),
(129, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 7924, 50, 30.245, 5),
(130, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32167, 50, 0.258, 6),
(131, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 684961, 50, 2.235, 7),
(132, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 65461, 50, 0.258, 8),
(133, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 654767, 50, 3.658, 9),
(134, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32187, 50, 1.215, 10),
(135, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 5646, 50, 2.154, 11),
(136, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 8721, 50, 0.874, 12),
(137, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 3218, 50, 5.213, 13),
(138, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 3218, 50, 6.214, 14),
(139, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32187, 50, 4.254, 16),
(140, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 7898, 50, 3.256, 17),
(141, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 50240, 50, 7.214, 18),
(142, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 60241, 50, 5.236, 19),
(143, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 80135, 50, 9.235, 20),
(144, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 90154, 50, 5.214, 21),
(145, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 20552, 49.765, -0.235, 1),
(146, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 45678, 44.432, -4.568, 2),
(147, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32145, 46.449, -3.551, 3),
(148, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 45120, 48.746, -1.254, 4),
(149, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 7924, 19.755, -30.245, 5),
(150, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32167, 49.742, -0.258, 6),
(151, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 684961, 47.765, -2.235, 7),
(152, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 65461, 49.742, -0.258, 8),
(153, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 654767, 46.342, -3.658, 9),
(154, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32187, 48.785, -1.215, 10),
(155, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 5646, 47.846, -2.154, 11),
(156, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 8721, 49.126, -0.874, 12),
(157, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 3218, 44.787, -5.213, 13),
(158, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 3218, 43.786, -6.214, 14),
(159, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 32187, 45.746, -4.254, 16),
(160, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 7898, 46.744, -3.256, 17),
(161, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 50240, 42.786, -7.214, 18),
(162, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 60241, 44.764, -5.236, 19),
(163, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 80135, 40.765, -9.235, 20),
(164, '2018-11-10', '21:59:16', 'Edición Venta. Comprobante: 0001-00000001', 90154, 44.786, -5.214, 21),
(165, '2018-11-15', '22:42:51', 'Venta. Comprobante: 0001-00000001', 20552, 44.765, -5, 1),
(166, '2018-11-15', '22:42:51', 'Venta. Comprobante: 0001-00000001', 45678, 39.432, -5, 2),
(167, '2018-11-15', '22:42:51', 'Venta. Comprobante: 0001-00000001', 32145, 36.449, -10, 3),
(168, '2018-11-15', '22:42:51', 'Venta. Comprobante: 0001-00000001', 45120, 43.746, -5, 4),
(169, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 20552, 45, 0.235, 1),
(170, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 45678, 44, 4.568, 2),
(171, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32145, 40, 3.551, 3),
(172, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 45120, 45, 1.254, 4),
(173, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 7924, 50, 30.245, 5),
(174, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32167, 50, 0.258, 6),
(175, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 684961, 50, 2.235, 7),
(176, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 65461, 50, 0.258, 8),
(177, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 654767, 50, 3.658, 9),
(178, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32187, 50, 1.215, 10),
(179, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 5646, 50, 2.154, 11),
(180, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 8721, 50, 0.874, 12),
(181, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 3218, 50, 5.213, 13),
(182, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 3218, 50, 6.214, 14),
(183, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32187, 50, 4.254, 16),
(184, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 7898, 50, 3.256, 17),
(185, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 50240, 50, 7.214, 18),
(186, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 60241, 50, 5.236, 19),
(187, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 80135, 50, 9.235, 20),
(188, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 90154, 50, 5.214, 21),
(189, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 20552, 44.765, -0.235, 1),
(190, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 45678, 39.432, -4.568, 2),
(191, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32145, 36.449, -3.551, 3),
(192, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 45120, 43.746, -1.254, 4),
(193, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 7924, 19.755, -30.245, 5),
(194, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32167, 49.742, -0.258, 6),
(195, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 684961, 47.765, -2.235, 7),
(196, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 65461, 49.742, -0.258, 8),
(197, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 654767, 46.342, -3.658, 9),
(198, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32187, 48.785, -1.215, 10),
(199, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 5646, 47.846, -2.154, 11),
(200, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 8721, 49.126, -0.874, 12),
(201, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 3218, 44.787, -5.213, 13),
(202, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 3218, 43.786, -6.214, 14),
(203, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 32187, 45.746, -4.254, 16),
(204, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 7898, 46.744, -3.256, 17),
(205, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 50240, 42.786, -7.214, 18),
(206, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 60241, 44.764, -5.236, 19),
(207, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 80135, 40.765, -9.235, 20),
(208, '2018-11-10', '22:43:06', 'Edición Venta. Comprobante: 0001-00000002', 90154, 44.786, -5.214, 21),
(209, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 20552, 49.765, 5, 1),
(210, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 45678, 44.432, 5, 2),
(211, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 32145, 46.449, 10, 3),
(212, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 45120, 48.746, 5, 4),
(213, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 20552, 44.765, -5, 1),
(214, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 45678, 39.432, -5, 2),
(215, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 32145, 36.449, -10, 3),
(216, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 45120, 43.746, -5, 4),
(217, '2018-11-15', '23:24:26', 'Edición Venta. Comprobante: 0001-00000001', 90154, 39.786, -5, 21),
(218, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 20552, 49.765, 5, 1),
(219, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 45678, 44.432, 5, 2),
(220, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 32145, 46.449, 10, 3),
(221, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 45120, 48.746, 5, 4),
(222, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 90154, 44.786, 5, 21),
(223, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 20552, 44.765, -5, 1),
(224, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 45678, 39.432, -5, 2),
(225, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 32145, 36.449, -10, 3),
(226, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 45120, 43.746, -5, 4),
(227, '2018-11-15', '23:32:47', 'Edición Venta. Comprobante: 0001-00000001', 90154, 39.786, -5, 21),
(228, '2018-11-20', '22:45:55', 'Venta. Comprobante: 0001-00000099', 20552, 43.765, -1, 1),
(229, '2018-11-20', '22:45:55', 'Venta. Comprobante: 0001-00000099', 45678, 38.432, -1, 2),
(230, '2018-11-20', '22:45:55', 'Venta. Comprobante: 0001-00000099', 32145, 35.697, -0.752, 3),
(231, '2018-11-20', '22:47:59', 'Edición Venta. Comprobante: 0001-00000099', 20552, 44.765, 1, 1),
(232, '2018-11-20', '22:47:59', 'Edición Venta. Comprobante: 0001-00000099', 45678, 39.432, 1, 2),
(233, '2018-11-20', '22:47:59', 'Edición Venta. Comprobante: 0001-00000099', 32145, 36.449, 0.752, 3),
(234, '2018-11-20', '22:47:59', 'Edición Venta. Comprobante: 0001-00000099', 20552, 43.765, -1, 1),
(235, '2018-11-20', '22:47:59', 'Edición Venta. Comprobante: 0001-00000099', 45678, 38.432, -1, 2),
(236, '2018-11-20', '22:47:59', 'Edición Venta. Comprobante: 0001-00000099', 32145, 35.697, -0.752, 3),
(237, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 20552, 44.765, 1, 1),
(238, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 45678, 39.432, 1, 2),
(239, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 32145, 36.449, 0.752, 3),
(240, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 20552, 43.765, -1, 1),
(241, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 45678, 38.432, -1, 2),
(242, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 32145, 35.697, -0.752, 3),
(243, '2018-11-20', '22:52:37', 'Edición Venta. Comprobante: 0001-00000099', 45120, 41.362, -2.384, 4),
(244, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 20552, 44.765, 1, 1),
(245, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 45678, 39.432, 1, 2),
(246, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 32145, 36.449, 0.752, 3),
(247, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 45120, 43.746, 2.384, 4),
(248, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 20552, 43.765, -1, 1),
(249, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 45678, 38.432, -1, 2),
(250, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 32145, 35.697, -0.752, 3),
(251, '2018-11-20', '23:35:12', 'Edición Venta. Comprobante: 0001-00000099', 45120, 41.362, -2.384, 4),
(252, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 20552, 44.765, 1, 1),
(253, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 45678, 39.432, 1, 2),
(254, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 32145, 36.449, 0.752, 3),
(255, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 45120, 43.746, 2.384, 4),
(256, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 20552, 43.765, -1, 1),
(257, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 45678, 38.432, -1, 2),
(258, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 32145, 35.697, -0.752, 3),
(259, '2018-11-20', '23:36:31', 'Edición Venta. Comprobante: 0001-00000099', 45120, 41.362, -2.384, 4),
(260, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 20552, 44.765, 1, 1),
(261, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 45678, 39.432, 1, 2),
(262, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 32145, 36.449, 0.752, 3),
(263, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 45120, 43.746, 2.384, 4),
(264, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 20552, 43.765, -1, 1),
(265, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 45678, 38.432, -1, 2),
(266, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 32145, 35.697, -0.752, 3),
(267, '2018-11-20', '23:39:18', 'Edición Venta. Comprobante: 0001-00000099', 45120, 41.362, -2.384, 4),
(268, '2018-11-20', '23:43:03', 'Venta. Comprobante: 0001-00000098', 20552, 42.396, -1.369, 1),
(269, '2018-11-20', '23:43:03', 'Venta. Comprobante: 0001-00000098', 45678, 36.432, -2, 2),
(270, '2018-11-20', '23:43:03', 'Venta. Comprobante: 0001-00000098', 32145, 31.999, -3.698, 3),
(271, '2018-11-24', '19:39:42', 'Venta. Comprobante: 0000-0001-00000006', 20552, 41.707, -0.689, 1),
(272, '2018-11-24', '19:39:42', 'Venta. Comprobante: 0000-0001-00000006', 45678, 29.87, -6.562, 2),
(273, '2018-11-24', '19:39:42', 'Venta. Comprobante: 0000-0001-00000006', 32145, 30.854, -1.145, 3),
(274, '2018-11-24', '19:39:42', 'Venta. Comprobante: 0000-0001-00000006', 45120, 40.467, -0.895, 4),
(275, '2018-11-24', '19:39:42', 'Venta. Comprobante: 0000-0001-00000006', 7924, 17.399, -2.356, 5),
(276, '2018-11-24', '19:54:21', 'Venta. Comprobante: 0001-00000007', 20552, 39.342, -2.365, 1),
(277, '2018-11-24', '19:54:21', 'Venta. Comprobante: 0001-00000007', 45678, 29.216, -0.654, 2),
(278, '2018-11-24', '19:54:21', 'Venta. Comprobante: 0001-00000007', 32145, 24.498, -6.356, 3),
(279, '2018-11-24', '19:54:21', 'Venta. Comprobante: 0001-00000007', 45120, 38.113, -2.354, 4),
(280, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 20552, 41.707, 2.365, 1),
(281, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 45678, 29.87, 0.654, 2),
(282, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 32145, 30.854, 6.356, 3),
(283, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 45120, 40.467, 2.354, 4),
(284, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 20552, 39.342, -2.365, 1),
(285, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 45678, 29.216, -0.654, 2),
(286, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 32145, 24.498, -6.356, 3),
(287, '2018-11-20', '20:11:02', 'Edición Venta. Comprobante: 0000-0001-00000007', 45120, 38.113, -2.354, 4),
(288, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 20552, 40.342, 1, 1),
(289, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 45678, 30.216, 1, 2),
(290, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 32145, 25.25, 0.752, 3),
(291, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 45120, 40.497, 2.384, 4),
(292, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 20552, 39.342, -1, 1),
(293, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 45678, 29.216, -1, 2),
(294, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 32145, 24.498, -0.752, 3),
(295, '2018-11-21', '21:11:00', 'Edición Venta. Comprobante: 0000-0001-00000004', 45120, 38.113, -2.384, 4),
(296, '2018-11-26', '23:33:45', 'Venta. Comprobante: 0001-00000008', 20552, 38.644, -0.698, 1),
(297, '2018-11-26', '23:33:45', 'Venta. Comprobante: 0001-00000008', 45678, 25.96, -3.256, 2),
(298, '2018-11-26', '23:33:45', 'Venta. Comprobante: 0001-00000008', 32145, 21.242, -3.256, 3),
(299, '2018-11-26', '23:38:38', 'Venta. Comprobante: 0001-00000009', 20552, 34.288, -4.356, 1),
(300, '2018-11-26', '23:38:38', 'Venta. Comprobante: 0001-00000009', 45678, 21.398, -4.562, 2),
(301, '2018-11-26', '23:38:38', 'Venta. Comprobante: 0001-00000009', 32145, 16.119, -5.123, 3),
(302, '2018-11-26', '23:38:38', 'Venta. Comprobante: 0001-00000009', 45120, 31.99, -6.123, 4),
(303, '2018-11-26', '23:38:38', 'Venta. Comprobante: 0001-00000009', 7924, 16.745, -0.654, 5),
(304, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 20552, 34.977, 0.689, 1),
(305, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 45678, 27.96, 6.562, 2),
(306, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 32145, 17.264, 1.145, 3),
(307, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 45120, 32.885, 0.895, 4),
(308, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 7924, 19.101, 2.356, 5),
(309, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 20552, 34.288, -0.689, 1),
(310, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 45678, 21.398, -6.562, 2),
(311, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 32145, 16.119, -1.145, 3),
(312, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 45120, 31.99, -0.895, 4),
(313, '2018-11-27', '08:52:44', 'Edición Venta. Comprobante: 0000-0001-00000006', 7924, 16.745, -2.356, 5),
(314, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 20552, 34.523, 0.235, 1),
(315, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 45678, 25.966, 4.568, 2),
(316, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32145, 19.67, 3.551, 3),
(317, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 45120, 33.244, 1.254, 4),
(318, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 7924, 46.99, 30.245, 5),
(319, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32167, 50, 0.258, 6),
(320, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 684961, 50, 2.235, 7),
(321, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 65461, 50, 0.258, 8),
(322, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 654767, 50, 3.658, 9),
(323, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 50, 1.215, 10),
(324, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 5646, 50, 2.154, 11),
(325, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 8721, 50, 0.874, 12),
(326, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 50, 5.213, 13),
(327, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 50, 6.214, 14),
(328, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 50, 4.254, 16),
(329, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 7898, 50, 3.256, 17),
(330, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 50240, 50, 7.214, 18),
(331, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 60241, 50, 5.236, 19),
(332, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 80135, 50, 9.235, 20),
(333, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 90154, 45, 5.214, 21),
(334, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 20552, 34.288, -0.235, 1),
(335, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 45678, 21.398, -4.568, 2),
(336, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32145, 16.119, -3.551, 3),
(337, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 45120, 31.99, -1.254, 4),
(338, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 7924, 16.745, -30.245, 5),
(339, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32167, 49.742, -0.258, 6),
(340, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 684961, 47.765, -2.235, 7),
(341, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 65461, 49.742, -0.258, 8),
(342, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 654767, 46.342, -3.658, 9),
(343, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 48.785, -1.215, 10),
(344, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 5646, 47.846, -2.154, 11),
(345, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 8721, 49.126, -0.874, 12),
(346, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 44.787, -5.213, 13),
(347, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 43.786, -6.214, 14),
(348, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 45.746, -4.254, 16),
(349, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 7898, 46.744, -3.256, 17),
(350, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 50240, 42.786, -7.214, 18),
(351, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 60241, 44.764, -5.236, 19),
(352, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 80135, 40.765, -9.235, 20),
(353, '2018-11-25', '21:43:02', 'Edición Venta. Comprobante: 0000-0001-00000001', 90154, 39.786, -5.214, 21),
(354, '2018-11-27', '21:44:27', 'Venta. Comprobante: 0001-00000010', 32145, 15.119, -1, 3),
(355, '2018-11-27', '21:44:27', 'Venta. Comprobante: 0001-00000010', 45120, 30.99, -1, 4),
(356, '2018-11-27', '21:44:27', 'Venta. Comprobante: 0001-00000010', 7924, 15.745, -1, 5),
(357, '2018-11-27', '21:44:27', 'Venta. Comprobante: 0001-00000010', 20552, 33.288, -1, 1),
(358, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 20552, 34.288, 1, 1),
(359, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45678, 22.398, 1, 2),
(360, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 32145, 15.871, 0.752, 3),
(361, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45120, 33.374, 2.384, 4),
(362, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 20552, 33.288, -1, 1),
(363, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45678, 21.398, -1, 2),
(364, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 32145, 15.119, -0.752, 3),
(365, '2018-11-26', '22:01:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45120, 30.99, -2.384, 4),
(366, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 20552, 34.288, 1, 1),
(367, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45678, 22.398, 1, 2),
(368, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 32145, 15.871, 0.752, 3),
(369, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45120, 33.374, 2.384, 4),
(370, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 20552, 33.288, -1, 1),
(371, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45678, 21.398, -1, 2),
(372, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 32145, 15.119, -0.752, 3),
(373, '2018-11-27', '22:02:59', 'Edición Venta. Comprobante: 0000-0001-00000004', 45120, 30.99, -2.384, 4),
(374, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 20552, 38.288, 5, 1),
(375, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 45678, 26.398, 5, 2),
(376, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 32145, 25.119, 10, 3),
(377, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 45120, 35.99, 5, 4),
(378, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 90154, 44.786, 5, 21),
(379, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 20552, 33.288, -5, 1),
(380, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 45678, 21.398, -5, 2),
(381, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 32145, 15.119, -10, 3),
(382, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 45120, 30.99, -5, 4),
(383, '2018-11-27', '22:04:41', 'Edición Venta. Comprobante: 0000-0001-00000003', 90154, 39.786, -5, 21),
(384, '2018-11-27', '22:07:33', 'Edición Venta. Comprobante: 0000-0001-00000002', 45678, 22.398, 1, 2),
(385, '2018-11-27', '22:07:33', 'Edición Venta. Comprobante: 0000-0001-00000002', 45678, 21.398, -1, 2),
(386, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 20552, 33.523, 0.235, 1),
(387, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 45678, 25.966, 4.568, 2),
(388, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32145, 18.67, 3.551, 3),
(389, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 45120, 32.244, 1.254, 4),
(390, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 7924, 45.99, 30.245, 5),
(391, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32167, 50, 0.258, 6),
(392, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 684961, 50, 2.235, 7),
(393, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 65461, 50, 0.258, 8),
(394, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 654767, 50, 3.658, 9),
(395, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 50, 1.215, 10),
(396, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 5646, 50, 2.154, 11),
(397, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 8721, 50, 0.874, 12),
(398, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 50, 5.213, 13),
(399, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 50, 6.214, 14),
(400, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 50, 4.254, 16),
(401, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 7898, 50, 3.256, 17),
(402, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 50240, 50, 7.214, 18),
(403, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 60241, 50, 5.236, 19),
(404, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 80135, 50, 9.235, 20),
(405, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 90154, 45, 5.214, 21),
(406, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 20552, 33.288, -0.235, 1),
(407, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 45678, 21.398, -4.568, 2),
(408, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32145, 15.119, -3.551, 3),
(409, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 45120, 30.99, -1.254, 4),
(410, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 7924, 15.745, -30.245, 5),
(411, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32167, 49.742, -0.258, 6),
(412, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 684961, 47.765, -2.235, 7),
(413, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 65461, 49.742, -0.258, 8),
(414, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 654767, 46.342, -3.658, 9),
(415, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 48.785, -1.215, 10),
(416, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 5646, 47.846, -2.154, 11),
(417, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 8721, 49.126, -0.874, 12),
(418, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 44.787, -5.213, 13),
(419, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 3218, 43.786, -6.214, 14),
(420, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 32187, 45.746, -4.254, 16),
(421, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 7898, 46.744, -3.256, 17),
(422, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 50240, 42.786, -7.214, 18),
(423, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 60241, 44.764, -5.236, 19),
(424, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 80135, 40.765, -9.235, 20),
(425, '2018-11-27', '22:09:03', 'Edición Venta. Comprobante: 0000-0001-00000001', 90154, 39.786, -5.214, 21),
(426, '2018-11-27', '22:10:11', 'Venta. Comprobante: 0001-00000011', 20552, 32.288, -1, 1),
(427, '2018-11-27', '22:10:11', 'Venta. Comprobante: 0001-00000011', 45678, 20.398, -1, 2),
(428, '2018-11-27', '22:10:11', 'Venta. Comprobante: 0001-00000011', 32145, 14.119, -1, 3),
(429, '2018-11-14', '22:12:32', 'Venta. Comprobante: 0001-00000012', 7924, 14.745, -1, 5),
(430, '2018-11-14', '22:12:32', 'Venta. Comprobante: 0001-00000012', 45120, 29.99, -1, 4),
(431, '2018-12-03', '22:22:17', 'Venta. Comprobante: ﻿﻿﻿0001-00000013', 20552, 31.288, -1, 1),
(432, '2018-12-03', '22:22:17', 'Venta. Comprobante: ﻿﻿﻿0001-00000013', 45678, 19.398, -1, 2),
(433, '2018-12-03', '22:44:47', 'Venta. Comprobante: ﻿﻿﻿0003-00000001', 20552, 30.288, -1, 1),
(434, '2018-12-03', '22:44:47', 'Venta. Comprobante: ﻿﻿﻿0003-00000001', 32145, 13.119, -1, 3),
(435, '2018-12-03', '22:44:47', 'Venta. Comprobante: ﻿﻿﻿0003-00000001', 7924, 13.745, -1, 5),
(436, '2018-12-04', '22:18:48', 'Ingreso. Stock Inicial', 2314, 50, 50, 22),
(437, '2018-12-09', '01:02:08', 'Venta. Comprobante: ﻿﻿﻿0003-00000015', 20552, 29.288, -1, 1),
(438, '2018-12-09', '01:02:08', 'Venta. Comprobante: ﻿﻿﻿0003-00000015', 45678, 18.398, -1, 2),
(439, '2018-12-09', '01:02:08', 'Venta. Comprobante: ﻿﻿﻿0003-00000015', 32145, 12.119, -1, 3),
(440, '2018-12-09', '01:05:53', 'Venta. Comprobante: ﻿﻿﻿0003-00000016', 45120, 28.99, -1, 4),
(441, '2018-12-09', '01:05:53', 'Venta. Comprobante: ﻿﻿﻿0003-00000016', 7924, 12.745, -1, 5),
(442, '2018-12-09', '01:05:53', 'Venta. Comprobante: ﻿﻿﻿0003-00000016', 654767, 45.342, -1, 9),
(443, '2018-12-09', '01:05:53', 'Venta. Comprobante: ﻿﻿﻿0003-00000016', 3218, 42.786, -1, 14),
(444, '2018-12-09', '01:08:22', 'Venta. Comprobante: ﻿﻿﻿0003-00000017', 95135, -1, -1, 23),
(445, '2018-12-09', '01:08:22', 'Venta. Comprobante: ﻿﻿﻿0003-00000017', 2314, 49, -1, 22),
(446, '2018-12-09', '01:08:22', 'Venta. Comprobante: ﻿﻿﻿0003-00000017', 90154, 38.786, -1, 21),
(447, '2018-12-09', '01:08:22', 'Venta. Comprobante: ﻿﻿﻿0003-00000017', 80135, 39.765, -1, 20),
(448, '2018-12-09', '01:08:22', 'Venta. Comprobante: ﻿﻿﻿0003-00000017', 60241, 43.764, -1, 19),
(449, '2018-12-09', '01:18:41', 'Venta. Comprobante: ﻿﻿﻿0003-00000018', 32167, 48.742, -1, 6),
(450, '2018-12-09', '01:18:41', 'Venta. Comprobante: ﻿﻿﻿0003-00000018', 684961, 46.765, -1, 7),
(451, '2018-12-09', '01:18:41', 'Venta. Comprobante: ﻿﻿﻿0003-00000018', 65461, 48.742, -1, 8),
(452, '2018-12-09', '01:18:41', 'Venta. Comprobante: ﻿﻿﻿0003-00000018', 654767, 44.342, -1, 9),
(453, '2018-12-09', '01:18:41', 'Venta. Comprobante: ﻿﻿﻿0003-00000018', 32187, 47.785, -1, 10),
(454, '2018-12-09', '01:25:36', 'Venta. Comprobante: ﻿﻿﻿3-00000006', 45120, 25.99, -3, 4),
(455, '2018-12-09', '01:25:36', 'Venta. Comprobante: ﻿﻿﻿3-00000006', 32145, 10.119, -2, 3),
(456, '2018-12-09', '01:27:36', 'Venta. Comprobante: ﻿﻿﻿3-00000006', 32145, 5.119, -5, 3),
(457, '2018-12-09', '01:29:21', 'Venta. Comprobante: ﻿﻿﻿3-00000019', 32145, 0.119, -5, 3),
(458, '2018-12-09', '03:22:54', 'Venta. Comprobante: 0003-00000002', 32145, -9.881, -10, 3),
(459, '2018-12-09', '03:25:39', 'Venta. Comprobante: 0003-00000006', 32145, -10.881, -1, 3),
(460, '2018-12-09', '03:28:11', 'Venta. Comprobante: 0003-00000020', 32145, -20.881, -10, 3),
(461, '2018-12-09', '03:29:18', 'Venta. Comprobante: 0003-00000006', 32145, -25.881, -5, 3),
(462, '2018-12-09', '03:30:29', 'Venta. Comprobante: 0003-00000006', 32145, -35.881, -10, 3),
(463, '2018-12-09', '03:38:10', 'Venta. Comprobante: 0003-00000000', 32145, -45.881, -10, 3),
(464, '2018-12-09', '03:39:40', 'Venta. Comprobante: 0003-00000000', 32145, -65.881, -20, 3),
(465, '2018-12-09', '03:40:51', 'Venta. Comprobante: 0003-00000023', 32145, -71.568, -5.687, 3),
(466, '2018-12-09', '03:54:34', 'Edición Venta. Comprobante: 0000-0003-00000023', 32145, -65.881, 5.687, 3),
(467, '2018-12-09', '03:54:34', 'Edición Venta. Comprobante: 0000-0003-00000023', 32145, -69.381, -3.5, 3),
(468, '2018-12-09', '03:54:57', 'Edición Venta. Comprobante: 0000-0003-00000023', 32145, -65.881, 3.5, 3),
(469, '2018-12-09', '03:54:57', 'Edición Venta. Comprobante: 0000-0003-00000023', 32145, -69.448, -3.567, 3),
(470, '2018-12-09', '04:06:51', 'Edición Venta. Comprobante: 0000-0003-00000024', 20552, 28.288, -1, 1),
(471, '2018-12-09', '04:19:56', 'Venta. Comprobante: 0003-00000008', 20552, 27.288, -1, 1),
(472, '2018-12-09', '20:31:11', 'Ajuste de Stock.', 95135, 52.642, 52.642, 23),
(473, '2018-12-09', '20:31:31', 'Ajuste de Stock.', 32145, 52.698, 52.698, 3),
(474, '2018-12-11', '21:17:20', 'Venta. Comprobante: 0003-00000025', 20552, 26.288, -1, 1),
(475, '2018-12-11', '21:17:20', 'Venta. Comprobante: 0003-00000025', 45678, 17.398, -1, 2),
(476, '2018-12-11', '21:17:20', 'Venta. Comprobante: 0003-00000025', 32145, 51.698, -1, 3),
(477, '2018-12-11', '21:18:10', 'Venta. Comprobante: 0003-00000011', 20552, 25.288, -1, 1),
(478, '2018-12-11', '21:18:10', 'Venta. Comprobante: 0003-00000011', 45678, 16.398, -1, 2),
(479, '2018-12-11', '21:18:10', 'Venta. Comprobante: 0003-00000011', 32145, 50.698, -1, 3),
(480, '2018-12-11', '21:18:42', 'Venta. Comprobante: 0003-00000012', 20552, 24.288, -1, 1),
(481, '2018-12-11', '21:18:42', 'Venta. Comprobante: 0003-00000012', 7924, 11.745, -1, 5),
(482, '2018-12-11', '21:18:42', 'Venta. Comprobante: 0003-00000012', 95135, 51.642, -1, 23),
(483, '2018-12-11', '21:18:42', 'Venta. Comprobante: 0003-00000012', 2314, 48, -1, 22),
(484, '2018-12-11', '21:18:42', 'Venta. Comprobante: 0003-00000012', 90154, 37.786, -1, 21),
(485, '2018-12-12', '23:05:15', 'Venta. Comprobante: 0003-00000003', 8721, 48.126, -1, 12),
(486, '2018-12-12', '23:05:15', 'Venta. Comprobante: 0003-00000003', 3218, 43.787, -1, 13),
(487, '2018-12-12', '23:05:15', 'Venta. Comprobante: 0003-00000003', 3218, 41.786, -1, 14),
(488, '2018-12-12', '23:05:15', 'Venta. Comprobante: 0003-00000003', 1386, 49, -1, 15),
(489, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 45678, 17.398, 1, 2),
(490, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 7924, 12.745, 1, 5),
(491, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 45120, 26.99, 1, 4),
(492, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 20552, 25.288, 1, 1),
(493, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 90154, 38.786, 1, 21),
(494, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 2314, 49, 1, 22),
(495, '2018-12-17', '20:57:04', 'Ingreso. Comprobante: 0001-00023568', 95135, 52.642, 1, 23),
(496, '2018-12-17', '21:43:12', 'Ingreso. Comprobante: 0001-00000002', 20552, 26.288, 1, 1),
(497, '2018-12-17', '21:43:12', 'Ingreso. Comprobante: 0001-00000002', 45678, 18.398, 1, 2),
(498, '2018-12-17', '21:43:12', 'Ingreso. Comprobante: 0001-00000002', 32145, 51.698, 1, 3),
(499, '2018-12-17', '21:43:12', 'Ingreso. Comprobante: 0001-00000002', 45120, 27.99, 1, 4),
(500, '2018-12-17', '21:43:12', 'Ingreso. Comprobante: 0001-00000002', 7924, 13.745, 1, 5);
INSERT INTO `stock` (`stock_id`, `fecha`, `hora`, `concepto`, `codigo`, `cantidad_actual`, `cantidad_movimiento`, `producto_id`) VALUES
(501, '2018-12-17', '21:44:42', 'Ingreso. Comprobante: 0001-00000003', 20552, 27.288, 1, 1),
(502, '2018-12-17', '21:44:42', 'Ingreso. Comprobante: 0001-00000003', 45678, 19.398, 1, 2),
(503, '2018-12-17', '21:44:42', 'Ingreso. Comprobante: 0001-00000003', 32145, 52.698, 1, 3),
(504, '2018-12-17', '21:44:42', 'Ingreso. Comprobante: 0001-00000003', 45120, 28.99, 1, 4),
(505, '2018-12-17', '21:44:42', 'Ingreso. Comprobante: 0001-00000003', 7924, 14.745, 1, 5),
(506, '2018-12-17', '21:45:52', 'Ingreso. Comprobante: 0001-00000044', 20552, 28.288, 1, 1),
(507, '2018-12-17', '21:45:52', 'Ingreso. Comprobante: 0001-00000044', 45678, 20.398, 1, 2),
(508, '2018-12-17', '21:45:52', 'Ingreso. Comprobante: 0001-00000044', 32145, 53.698, 1, 3),
(509, '2018-12-17', '21:45:52', 'Ingreso. Comprobante: 0001-00000044', 45120, 29.99, 1, 4),
(510, '2018-12-17', '21:45:52', 'Ingreso. Comprobante: 0001-00000044', 7924, 15.745, 1, 5),
(511, '2019-01-06', '15:45:29', 'Venta. Comprobante: 0003-00000014', 20552, 27.288, -1, 1),
(512, '2019-01-06', '15:45:29', 'Venta. Comprobante: 0003-00000014', 45678, 19.398, -1, 2),
(513, '2019-01-06', '15:45:29', 'Venta. Comprobante: 0003-00000014', 45120, 28.99, -1, 4),
(514, '2019-01-06', '20:50:37', 'Venta. Comprobante: 0003-00000026', 90154, 37.786, -1, 21),
(515, '2019-01-06', '20:50:37', 'Venta. Comprobante: 0003-00000026', 2314, 48, -1, 22),
(516, '2019-01-06', '20:50:37', 'Venta. Comprobante: 0003-00000026', 95135, 51.642, -1, 23),
(517, '2019-01-06', '21:21:45', 'Ingreso. Comprobante: 0001-00000056', 32145, 103.698, 50, 3),
(518, '2019-01-06', '21:21:45', 'Ingreso. Comprobante: 0001-00000056', 45120, 53.99, 25, 4),
(519, '2019-01-06', '21:21:45', 'Ingreso. Comprobante: 0001-00000056', 20552, 52.288, 25, 1),
(520, '2019-01-06', '21:21:45', 'Ingreso. Comprobante: 0001-00000056', 45678, 39.398, 20, 2),
(521, '2019-01-06', '21:39:56', 'Venta. Comprobante: 0003-00000027', 5646, 46.846, -1, 11),
(522, '2019-01-06', '21:39:56', 'Venta. Comprobante: 0003-00000027', 8721, 47.126, -1, 12),
(523, '2019-01-06', '21:39:56', 'Venta. Comprobante: 0003-00000027', 3218, 42.787, -1, 13),
(524, '2019-01-06', '21:56:01', 'Venta. Comprobante: 0003-00000028', 32145, 78.698, -25, 3),
(525, '2019-01-08', '23:52:03', 'Venta. Comprobante: 0003-00000029', 32145, 77.698, -1, 3),
(526, '2019-01-08', '23:52:03', 'Venta. Comprobante: 0003-00000029', 45120, 28.99, -25, 4),
(527, '2019-01-08', '23:52:03', 'Venta. Comprobante: 0003-00000029', 90154, 25.786, -12, 21),
(528, '2019-01-08', '23:52:03', 'Venta. Comprobante: 0003-00000029', 2314, 25, -23, 22),
(529, '2019-01-10', '21:10:09', 'Venta. Comprobante: 0003-00000016', 7898, 41.046, -5.698, 17),
(530, '2019-01-10', '21:10:09', 'Venta. Comprobante: 0003-00000016', 50240, 38.461, -4.325, 18),
(531, '2019-01-10', '21:10:09', 'Venta. Comprobante: 0003-00000016', 80135, 31.396, -8.369, 20),
(532, '2019-01-10', '21:10:09', 'Venta. Comprobante: 0003-00000016', 65461, 38.742, -10, 8),
(533, '2019-01-10', '21:10:09', 'Venta. Comprobante: 0003-00000016', 32187, 37.785, -10, 10),
(534, '2019-01-10', '21:10:09', 'Venta. Comprobante: 0003-00000016', 654767, 34.342, -10, 9),
(535, '2019-01-10', '21:10:58', 'Venta. Comprobante: 0003-00000016', 32167, 38.742, -10, 6),
(536, '2019-01-10', '21:10:58', 'Venta. Comprobante: 0003-00000016', 684961, 36.765, -10, 7),
(537, '2019-01-10', '21:10:58', 'Venta. Comprobante: 0003-00000016', 65461, 28.742, -10, 8),
(538, '2019-01-10', '21:10:58', 'Venta. Comprobante: 0003-00000016', 654767, 24.342, -10, 9),
(539, '2019-01-10', '21:10:58', 'Venta. Comprobante: 0003-00000016', 32187, 27.785, -10, 10),
(540, '2018-07-20', '21:12:26', 'Venta. Comprobante: 0003-00000016', 90154, 10.432, -15.354, 21),
(541, '2018-07-20', '21:12:26', 'Venta. Comprobante: 0003-00000016', 2314, 14.548, -10.452, 22),
(542, '2018-07-20', '21:12:26', 'Venta. Comprobante: 0003-00000016', 95135, 36.164, -15.478, 23),
(543, '2018-07-20', '21:13:25', 'Venta. Comprobante: 0003-00000016', 32167, 23.742, -15, 6),
(544, '2018-07-20', '21:13:25', 'Venta. Comprobante: 0003-00000016', 684961, 21.765, -15, 7),
(545, '2018-07-20', '21:13:25', 'Venta. Comprobante: 0003-00000016', 65461, 13.742, -15, 8),
(546, '2018-07-20', '21:13:25', 'Venta. Comprobante: 0003-00000016', 654767, 9.342, -15, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submenu`
--

CREATE TABLE IF NOT EXISTS `submenu` (
`submenu_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `menu` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `submenu`
--

INSERT INTO `submenu` (`submenu_id`, `denominacion`, `icon`, `url`, `detalle`, `menu`) VALUES
(8, 'Menú', 'fa-bars', '#', '', 4),
(9, 'Usuarios', 'fa-users', '/usuario/agregar', '', 4),
(22, 'Agregar', 'fa-plus-circle', '/proveedor/agregar', '', 7),
(24, 'Agregar Producto', 'fa-plus-circle', '/producto/agregar', '', 8),
(27, 'Agregar', 'fa-plus-circle', '/cliente/agregar', '', 9),
(28, 'Buscar Producto', 'fa-search', '/producto/buscar_producto', '', 8),
(29, 'Marcas', 'fa-archive', '/productomarca/panel', '', 8),
(30, 'Rubros', 'fa-archive', '/productocategoria/panel', '', 8),
(31, 'Unidades de Medida', 'fa-archive', '/productounidad/panel', '', 8),
(32, 'Configuración', 'fa-cog', '/configuracion/panel', '', 4),
(33, 'Ingresar Productos', 'fa-level-up', '/ingreso/ingresar', '', 14),
(34, 'Condición IVA', 'fa-cog', '/condicioniva/panel', '', 11),
(35, 'Condición de Pago', 'fa-cog', '/condicionpago/panel', '', 11),
(36, 'Listar', 'fa-table', '/producto/listar', '', 8),
(37, 'Listar', 'fa-table', '/cliente/listar', '', 9),
(38, 'Listar', 'fa-table', '/proveedor/listar', '', 7),
(39, 'Listar Ingresos', 'fa-table', '/ingreso/listar', '', 14),
(40, 'Panel', 'fa-cube', '/stock/panel', '', 10),
(41, 'Zonas de Venta', 'fa-cog', '/frecuenciaventa/panel', '', 11),
(43, 'Listar', 'fa-table', '/vendedor/listar', '', 12),
(44, 'Agregar', 'fa-plus-circle', '/vendedor/agregar', '', 12),
(45, 'Condición Fiscal', 'fa-cog', '/condicionfiscal/panel', '', 11),
(46, 'Buscar Cliente', 'fa-search', '/cliente/panel', '', 9),
(47, 'Buscar Proveedor', 'fa-search', '/proveedor/panel', '', 7),
(48, 'Buscar Vendedor', 'fa-search', '/vendedor/panel', '', 12),
(49, 'Tipos de Factura', 'fa-cog', '/tipofactura/panel', '', 11),
(50, 'Registrar Venta', 'fa-usd', '/egreso/egresar', '', 10),
(51, 'Listar Ventas', 'fa-table', '/egreso/listar', '', 10),
(52, 'Listar', 'fa-table', '/flete/listar', '', 13),
(53, 'Buscar flete', 'fa-search', '/flete/panel', '', 13),
(54, 'Agregar Flete', 'fa-plus-circle', '/flete/agregar', '', 13),
(55, 'Cta Corriente Cliente', 'fa-table', '/cuentacorrientecliente/panel', '', 10),
(56, 'Cargar Stock Inicial', 'fa-plus-circle', '/stock/stock_inicial/1', '', 14),
(57, 'Entregas Pendientes', 'fa-truck', '/egreso/entregas_pendientes/1', '', 10),
(58, 'Lista de Precio', 'fa-usd', '/producto/lista_precio', '', 8),
(59, 'Ajustar Stock', 'fa-cogs', '/stock/ajustar_stock', '', 14),
(60, 'Cta Corriente Proveedor', 'fa-table', '/cuentacorrienteproveedor/panel', '', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submenuconfiguracionmenu`
--

CREATE TABLE IF NOT EXISTS `submenuconfiguracionmenu` (
`submenuconfiguracionmenu_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4587 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `submenuconfiguracionmenu`
--

INSERT INTO `submenuconfiguracionmenu` (`submenuconfiguracionmenu_id`, `compuesto`, `compositor`) VALUES
(4530, 2, 51),
(4531, 2, 50),
(4532, 2, 55),
(4533, 2, 39),
(4534, 2, 33),
(4535, 2, 36),
(4536, 2, 24),
(4537, 2, 29),
(4538, 2, 30),
(4539, 2, 37),
(4540, 2, 27),
(4541, 2, 38),
(4542, 2, 22),
(4543, 2, 43),
(4544, 2, 44),
(4545, 2, 52),
(4546, 2, 54),
(4547, 2, 34),
(4548, 2, 35),
(4549, 2, 41),
(4550, 2, 45),
(4551, 2, 9),
(4552, 2, 57),
(4553, 2, 58),
(4554, 2, 59),
(4555, 2, 56),
(4556, 1, 51),
(4557, 1, 39),
(4558, 1, 50),
(4559, 1, 33),
(4560, 1, 36),
(4561, 1, 24),
(4562, 1, 55),
(4563, 1, 37),
(4564, 1, 27),
(4565, 1, 38),
(4566, 1, 22),
(4567, 1, 43),
(4568, 1, 44),
(4569, 1, 52),
(4570, 1, 54),
(4571, 1, 34),
(4572, 1, 35),
(4573, 1, 41),
(4574, 1, 45),
(4575, 1, 49),
(4576, 1, 8),
(4577, 1, 9),
(4578, 1, 32),
(4579, 1, 57),
(4580, 1, 58),
(4581, 1, 29),
(4582, 1, 30),
(4583, 1, 31),
(4584, 1, 59),
(4585, 1, 56),
(4586, 1, 60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipofactura`
--

CREATE TABLE IF NOT EXISTS `tipofactura` (
`tipofactura_id` int(11) NOT NULL,
  `afip_id` int(11) NOT NULL,
  `nomenclatura` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `plantilla_impresion` text COLLATE utf8_spanish_ci NOT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipofactura`
--

INSERT INTO `tipofactura` (`tipofactura_id`, `afip_id`, `nomenclatura`, `denominacion`, `plantilla_impresion`, `detalle`) VALUES
(1, 1, 'A', '', 'facturaA', ''),
(2, 0, 'R', 'REMITO', 'remitoR', ''),
(3, 6, 'B', ' ', 'facturaB', ' '),
(4, 3, 'NCA', 'NOTA DE CRÉDITO A', 'notacreditoNC', ' ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipomovimientocuenta`
--

CREATE TABLE IF NOT EXISTS `tipomovimientocuenta` (
`tipomovimientocuenta_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipomovimientocuenta`
--

INSERT INTO `tipomovimientocuenta` (`tipomovimientocuenta_id`, `denominacion`) VALUES
(1, 'DEUDA'),
(2, 'INGRESO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
`usuario_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nivel` int(1) DEFAULT NULL,
  `usuariodetalle` int(11) DEFAULT NULL,
  `configuracionmenu` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `denominacion`, `nivel`, `usuariodetalle`, `configuracionmenu`) VALUES
(1, 'admin', 3, 1, 2),
(2, 'desarrollador', 9, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariodetalle`
--

CREATE TABLE IF NOT EXISTS `usuariodetalle` (
`usuariodetalle_id` int(11) NOT NULL,
  `apellido` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `correoelectronico` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `token` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuariodetalle`
--

INSERT INTO `usuariodetalle` (`usuariodetalle_id`, `apellido`, `nombre`, `correoelectronico`, `token`) VALUES
(1, 'Admin', 'admin', 'admin@admin.com', '4850fc35306cb8590e00564f5462e1bb'),
(2, 'Desarrollador', 'Admin', 'infozamba@gmail.com', '2d646827cffbc42da16cee8033e209d4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedor`
--

CREATE TABLE IF NOT EXISTS `vendedor` (
`vendedor_id` int(11) NOT NULL,
  `apellido` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `comision` float DEFAULT NULL,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `codigopostal` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `localidad` text COLLATE utf8_spanish_ci,
  `latitud` text COLLATE utf8_spanish_ci,
  `longitud` text COLLATE utf8_spanish_ci,
  `observacion` text COLLATE utf8_spanish_ci,
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL,
  `frecuenciaventa` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `vendedor`
--

INSERT INTO `vendedor` (`vendedor_id`, `apellido`, `nombre`, `comision`, `documento`, `domicilio`, `codigopostal`, `localidad`, `latitud`, `longitud`, `observacion`, `provincia`, `documentotipo`, `frecuenciaventa`) VALUES
(1, 'RIQUELME', 'JUAN ROMÁN', 5, 20325889051, '', '5300', 'CAPITAL', '', '', '', 11, 1, 1),
(2, 'PALERMO ', 'JUAN MARTIN', 15, 28546789, '', '', '', '', '', '', 11, 1, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `backup`
--
ALTER TABLE `backup`
 ADD PRIMARY KEY (`backup_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
 ADD PRIMARY KEY (`cliente_id`), ADD KEY `provincia` (`provincia`), ADD KEY `documentotipo` (`documentotipo`), ADD KEY `condicioniva` (`condicioniva`), ADD KEY `condicionfiscal` (`condicionfiscal`), ADD KEY `frecuenciaventa` (`frecuenciaventa`,`vendedor`), ADD KEY `vendedor` (`vendedor`), ADD KEY `flete` (`flete`), ADD KEY `tipofactura` (`tipofactura`);

--
-- Indices de la tabla `condicionfiscal`
--
ALTER TABLE `condicionfiscal`
 ADD PRIMARY KEY (`condicionfiscal_id`);

--
-- Indices de la tabla `condicioniva`
--
ALTER TABLE `condicioniva`
 ADD PRIMARY KEY (`condicioniva_id`);

--
-- Indices de la tabla `condicionpago`
--
ALTER TABLE `condicionpago`
 ADD PRIMARY KEY (`condicionpago_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
 ADD PRIMARY KEY (`configuracion_id`), ADD KEY `condicioniva` (`condicioniva`);

--
-- Indices de la tabla `configuracionmenu`
--
ALTER TABLE `configuracionmenu`
 ADD PRIMARY KEY (`configuracionmenu_id`);

--
-- Indices de la tabla `cuentacorrientecliente`
--
ALTER TABLE `cuentacorrientecliente`
 ADD PRIMARY KEY (`cuentacorrientecliente_id`), ADD KEY `tipomovimientocuenta` (`tipomovimientocuenta`), ADD KEY `estadomovimientocuenta` (`estadomovimientocuenta`);

--
-- Indices de la tabla `cuentacorrienteproveedor`
--
ALTER TABLE `cuentacorrienteproveedor`
 ADD PRIMARY KEY (`cuentacorrienteproveedor_id`), ADD KEY `tipomovimientocuenta` (`tipomovimientocuenta`), ADD KEY `estadomovimientocuenta` (`estadomovimientocuenta`);

--
-- Indices de la tabla `documentotipo`
--
ALTER TABLE `documentotipo`
 ADD PRIMARY KEY (`documentotipo_id`);

--
-- Indices de la tabla `egreso`
--
ALTER TABLE `egreso`
 ADD PRIMARY KEY (`egreso_id`), ADD KEY `cliente` (`cliente`), ADD KEY `vendedor` (`vendedor`), ADD KEY `tipofactura` (`tipofactura`), ADD KEY `condicioniva` (`condicioniva`), ADD KEY `condicionpago` (`condicionpago`), ADD KEY `estadocomision` (`egresocomision`), ADD KEY `egresoentrega` (`egresoentrega`);

--
-- Indices de la tabla `egresoafip`
--
ALTER TABLE `egresoafip`
 ADD PRIMARY KEY (`egresoafip_id`);

--
-- Indices de la tabla `egresocomision`
--
ALTER TABLE `egresocomision`
 ADD PRIMARY KEY (`egresocomision_id`), ADD KEY `estadocomision` (`estadocomision`);

--
-- Indices de la tabla `egresodetalle`
--
ALTER TABLE `egresodetalle`
 ADD PRIMARY KEY (`egresodetalle_id`), ADD KEY `egresodetalleestado` (`egresodetalleestado`);

--
-- Indices de la tabla `egresodetalleestado`
--
ALTER TABLE `egresodetalleestado`
 ADD PRIMARY KEY (`egresodetalleestado_id`);

--
-- Indices de la tabla `egresoentrega`
--
ALTER TABLE `egresoentrega`
 ADD PRIMARY KEY (`egresoentrega_id`), ADD KEY `flete` (`flete`), ADD KEY `estadoentrega` (`estadoentrega`);

--
-- Indices de la tabla `estadocomision`
--
ALTER TABLE `estadocomision`
 ADD PRIMARY KEY (`estadocomision_id`);

--
-- Indices de la tabla `estadoentrega`
--
ALTER TABLE `estadoentrega`
 ADD PRIMARY KEY (`estadoentrega_id`);

--
-- Indices de la tabla `estadomovimientocuenta`
--
ALTER TABLE `estadomovimientocuenta`
 ADD PRIMARY KEY (`estadomovimientocuenta_id`);

--
-- Indices de la tabla `flete`
--
ALTER TABLE `flete`
 ADD PRIMARY KEY (`flete_id`), ADD KEY `documentotipo` (`documentotipo`);

--
-- Indices de la tabla `frecuenciaventa`
--
ALTER TABLE `frecuenciaventa`
 ADD PRIMARY KEY (`frecuenciaventa_id`);

--
-- Indices de la tabla `hojaruta`
--
ALTER TABLE `hojaruta`
 ADD PRIMARY KEY (`hojaruta_id`), ADD KEY `estadoentrega` (`estadoentrega`);

--
-- Indices de la tabla `infocontacto`
--
ALTER TABLE `infocontacto`
 ADD PRIMARY KEY (`infocontacto_id`);

--
-- Indices de la tabla `infocontactocliente`
--
ALTER TABLE `infocontactocliente`
 ADD PRIMARY KEY (`infocontactocliente_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `infocontactoflete`
--
ALTER TABLE `infocontactoflete`
 ADD PRIMARY KEY (`infocontactoflete_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `infocontactoproveedor`
--
ALTER TABLE `infocontactoproveedor`
 ADD PRIMARY KEY (`infocontactoproveedor_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `infocontactovendedor`
--
ALTER TABLE `infocontactovendedor`
 ADD PRIMARY KEY (`infocontactovendedor_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `ingreso`
--
ALTER TABLE `ingreso`
 ADD PRIMARY KEY (`ingreso_id`), ADD KEY `proveedor` (`proveedor`), ADD KEY `condicioniva` (`condicioniva`), ADD KEY `condicionpago` (`condicionpago`);

--
-- Indices de la tabla `ingresodetalle`
--
ALTER TABLE `ingresodetalle`
 ADD PRIMARY KEY (`ingresodetalle_id`);

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
 ADD PRIMARY KEY (`item_id`), ADD KEY `submenu` (`submenu`);

--
-- Indices de la tabla `itemconfiguracionmenu`
--
ALTER TABLE `itemconfiguracionmenu`
 ADD PRIMARY KEY (`itemconfiguracionmenu_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `localidad`
--
ALTER TABLE `localidad`
 ADD PRIMARY KEY (`localidad_id`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
 ADD PRIMARY KEY (`menu_id`);

--
-- Indices de la tabla `movimientotipo`
--
ALTER TABLE `movimientotipo`
 ADD PRIMARY KEY (`movimientotipo_id`);

--
-- Indices de la tabla `notacredito`
--
ALTER TABLE `notacredito`
 ADD PRIMARY KEY (`notacredito_id`), ADD KEY `tipofactura` (`tipofactura`);

--
-- Indices de la tabla `notacreditodetalle`
--
ALTER TABLE `notacreditodetalle`
 ADD PRIMARY KEY (`notacreditodetalle_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
 ADD PRIMARY KEY (`producto_id`), ADD KEY `productomarca` (`productomarca`), ADD KEY `productocategoria` (`productocategoria`), ADD KEY `productounidad` (`productounidad`);

--
-- Indices de la tabla `productocategoria`
--
ALTER TABLE `productocategoria`
 ADD PRIMARY KEY (`productocategoria_id`);

--
-- Indices de la tabla `productodetalle`
--
ALTER TABLE `productodetalle`
 ADD PRIMARY KEY (`productodetalle_id`);

--
-- Indices de la tabla `productomarca`
--
ALTER TABLE `productomarca`
 ADD PRIMARY KEY (`productomarca_id`);

--
-- Indices de la tabla `productounidad`
--
ALTER TABLE `productounidad`
 ADD PRIMARY KEY (`productounidad_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
 ADD PRIMARY KEY (`proveedor_id`), ADD KEY `provincia` (`provincia`), ADD KEY `documentotipo` (`documentotipo`), ADD KEY `condicioniva` (`condicioniva`);

--
-- Indices de la tabla `proveedorproducto`
--
ALTER TABLE `proveedorproducto`
 ADD PRIMARY KEY (`proveedorproducto_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `provincia`
--
ALTER TABLE `provincia`
 ADD PRIMARY KEY (`provincia_id`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
 ADD PRIMARY KEY (`stock_id`);

--
-- Indices de la tabla `submenu`
--
ALTER TABLE `submenu`
 ADD PRIMARY KEY (`submenu_id`), ADD KEY `submenu` (`menu`);

--
-- Indices de la tabla `submenuconfiguracionmenu`
--
ALTER TABLE `submenuconfiguracionmenu`
 ADD PRIMARY KEY (`submenuconfiguracionmenu_id`), ADD KEY `compuesto` (`compuesto`), ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `tipofactura`
--
ALTER TABLE `tipofactura`
 ADD PRIMARY KEY (`tipofactura_id`);

--
-- Indices de la tabla `tipomovimientocuenta`
--
ALTER TABLE `tipomovimientocuenta`
 ADD PRIMARY KEY (`tipomovimientocuenta_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
 ADD PRIMARY KEY (`usuario_id`), ADD KEY `usuariodetalle` (`usuariodetalle`), ADD KEY `configuracionmenu` (`configuracionmenu`);

--
-- Indices de la tabla `usuariodetalle`
--
ALTER TABLE `usuariodetalle`
 ADD PRIMARY KEY (`usuariodetalle_id`);

--
-- Indices de la tabla `vendedor`
--
ALTER TABLE `vendedor`
 ADD PRIMARY KEY (`vendedor_id`), ADD KEY `provincia` (`provincia`), ADD KEY `documentotipo` (`documentotipo`), ADD KEY `frecuenciaventa` (`frecuenciaventa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `backup`
--
ALTER TABLE `backup`
MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `condicionfiscal`
--
ALTER TABLE `condicionfiscal`
MODIFY `condicionfiscal_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `condicioniva`
--
ALTER TABLE `condicioniva`
MODIFY `condicioniva_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `condicionpago`
--
ALTER TABLE `condicionpago`
MODIFY `condicionpago_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
MODIFY `configuracion_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `configuracionmenu`
--
ALTER TABLE `configuracionmenu`
MODIFY `configuracionmenu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `cuentacorrientecliente`
--
ALTER TABLE `cuentacorrientecliente`
MODIFY `cuentacorrientecliente_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=90;
--
-- AUTO_INCREMENT de la tabla `cuentacorrienteproveedor`
--
ALTER TABLE `cuentacorrienteproveedor`
MODIFY `cuentacorrienteproveedor_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT de la tabla `documentotipo`
--
ALTER TABLE `documentotipo`
MODIFY `documentotipo_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `egreso`
--
ALTER TABLE `egreso`
MODIFY `egreso_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT de la tabla `egresoafip`
--
ALTER TABLE `egresoafip`
MODIFY `egresoafip_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT de la tabla `egresocomision`
--
ALTER TABLE `egresocomision`
MODIFY `egresocomision_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT de la tabla `egresodetalle`
--
ALTER TABLE `egresodetalle`
MODIFY `egresodetalle_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=320;
--
-- AUTO_INCREMENT de la tabla `egresodetalleestado`
--
ALTER TABLE `egresodetalleestado`
MODIFY `egresodetalleestado_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `egresoentrega`
--
ALTER TABLE `egresoentrega`
MODIFY `egresoentrega_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT de la tabla `estadocomision`
--
ALTER TABLE `estadocomision`
MODIFY `estadocomision_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `estadoentrega`
--
ALTER TABLE `estadoentrega`
MODIFY `estadoentrega_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `estadomovimientocuenta`
--
ALTER TABLE `estadomovimientocuenta`
MODIFY `estadomovimientocuenta_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `flete`
--
ALTER TABLE `flete`
MODIFY `flete_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `frecuenciaventa`
--
ALTER TABLE `frecuenciaventa`
MODIFY `frecuenciaventa_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `hojaruta`
--
ALTER TABLE `hojaruta`
MODIFY `hojaruta_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `infocontacto`
--
ALTER TABLE `infocontacto`
MODIFY `infocontacto_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT de la tabla `infocontactocliente`
--
ALTER TABLE `infocontactocliente`
MODIFY `infocontactocliente_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `infocontactoflete`
--
ALTER TABLE `infocontactoflete`
MODIFY `infocontactoflete_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `infocontactoproveedor`
--
ALTER TABLE `infocontactoproveedor`
MODIFY `infocontactoproveedor_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `infocontactovendedor`
--
ALTER TABLE `infocontactovendedor`
MODIFY `infocontactovendedor_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `ingreso`
--
ALTER TABLE `ingreso`
MODIFY `ingreso_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `ingresodetalle`
--
ALTER TABLE `ingresodetalle`
MODIFY `ingresodetalle_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `itemconfiguracionmenu`
--
ALTER TABLE `itemconfiguracionmenu`
MODIFY `itemconfiguracionmenu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=141;
--
-- AUTO_INCREMENT de la tabla `localidad`
--
ALTER TABLE `localidad`
MODIFY `localidad_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT de la tabla `movimientotipo`
--
ALTER TABLE `movimientotipo`
MODIFY `movimientotipo_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `notacredito`
--
ALTER TABLE `notacredito`
MODIFY `notacredito_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `notacreditodetalle`
--
ALTER TABLE `notacreditodetalle`
MODIFY `notacreditodetalle_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `productocategoria`
--
ALTER TABLE `productocategoria`
MODIFY `productocategoria_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `productodetalle`
--
ALTER TABLE `productodetalle`
MODIFY `productodetalle_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT de la tabla `productomarca`
--
ALTER TABLE `productomarca`
MODIFY `productomarca_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `productounidad`
--
ALTER TABLE `productounidad`
MODIFY `productounidad_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `proveedorproducto`
--
ALTER TABLE `proveedorproducto`
MODIFY `proveedorproducto_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `provincia`
--
ALTER TABLE `provincia`
MODIFY `provincia_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=547;
--
-- AUTO_INCREMENT de la tabla `submenu`
--
ALTER TABLE `submenu`
MODIFY `submenu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=61;
--
-- AUTO_INCREMENT de la tabla `submenuconfiguracionmenu`
--
ALTER TABLE `submenuconfiguracionmenu`
MODIFY `submenuconfiguracionmenu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4587;
--
-- AUTO_INCREMENT de la tabla `tipofactura`
--
ALTER TABLE `tipofactura`
MODIFY `tipofactura_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `tipomovimientocuenta`
--
ALTER TABLE `tipomovimientocuenta`
MODIFY `tipomovimientocuenta_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `usuariodetalle`
--
ALTER TABLE `usuariodetalle`
MODIFY `usuariodetalle_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `vendedor`
--
ALTER TABLE `vendedor`
MODIFY `vendedor_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`provincia`) REFERENCES `provincia` (`provincia_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_2` FOREIGN KEY (`documentotipo`) REFERENCES `documentotipo` (`documentotipo_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_3` FOREIGN KEY (`condicioniva`) REFERENCES `condicioniva` (`condicioniva_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_4` FOREIGN KEY (`condicionfiscal`) REFERENCES `condicionfiscal` (`condicionfiscal_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_5` FOREIGN KEY (`frecuenciaventa`) REFERENCES `frecuenciaventa` (`frecuenciaventa_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_6` FOREIGN KEY (`vendedor`) REFERENCES `vendedor` (`vendedor_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_7` FOREIGN KEY (`flete`) REFERENCES `flete` (`flete_id`) ON DELETE SET NULL,
ADD CONSTRAINT `cliente_ibfk_8` FOREIGN KEY (`tipofactura`) REFERENCES `tipofactura` (`tipofactura_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `configuracion`
--
ALTER TABLE `configuracion`
ADD CONSTRAINT `configuracion_ibfk_1` FOREIGN KEY (`condicioniva`) REFERENCES `condicioniva` (`condicioniva_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuentacorrientecliente`
--
ALTER TABLE `cuentacorrientecliente`
ADD CONSTRAINT `cuentacorrientecliente_ibfk_1` FOREIGN KEY (`tipomovimientocuenta`) REFERENCES `tipomovimientocuenta` (`tipomovimientocuenta_id`) ON DELETE CASCADE,
ADD CONSTRAINT `cuentacorrientecliente_ibfk_2` FOREIGN KEY (`estadomovimientocuenta`) REFERENCES `estadomovimientocuenta` (`estadomovimientocuenta_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuentacorrienteproveedor`
--
ALTER TABLE `cuentacorrienteproveedor`
ADD CONSTRAINT `cuentacorrienteproveedor_ibfk_1` FOREIGN KEY (`tipomovimientocuenta`) REFERENCES `tipomovimientocuenta` (`tipomovimientocuenta_id`) ON DELETE CASCADE,
ADD CONSTRAINT `cuentacorrienteproveedor_ibfk_2` FOREIGN KEY (`estadomovimientocuenta`) REFERENCES `estadomovimientocuenta` (`estadomovimientocuenta_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `egreso`
--
ALTER TABLE `egreso`
ADD CONSTRAINT `egreso_ibfk_1` FOREIGN KEY (`cliente`) REFERENCES `cliente` (`cliente_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egreso_ibfk_2` FOREIGN KEY (`vendedor`) REFERENCES `vendedor` (`vendedor_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egreso_ibfk_3` FOREIGN KEY (`tipofactura`) REFERENCES `tipofactura` (`tipofactura_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egreso_ibfk_4` FOREIGN KEY (`condicioniva`) REFERENCES `condicioniva` (`condicioniva_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egreso_ibfk_5` FOREIGN KEY (`condicionpago`) REFERENCES `condicionpago` (`condicionpago_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egreso_ibfk_6` FOREIGN KEY (`egresocomision`) REFERENCES `egresocomision` (`egresocomision_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egreso_ibfk_7` FOREIGN KEY (`egresoentrega`) REFERENCES `egresoentrega` (`egresoentrega_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `egresocomision`
--
ALTER TABLE `egresocomision`
ADD CONSTRAINT `egresocomision_ibfk_1` FOREIGN KEY (`estadocomision`) REFERENCES `estadocomision` (`estadocomision_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `egresodetalle`
--
ALTER TABLE `egresodetalle`
ADD CONSTRAINT `egresodetalle_ibfk_1` FOREIGN KEY (`egresodetalleestado`) REFERENCES `egresodetalleestado` (`egresodetalleestado_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `egresoentrega`
--
ALTER TABLE `egresoentrega`
ADD CONSTRAINT `egresoentrega_ibfk_1` FOREIGN KEY (`flete`) REFERENCES `flete` (`flete_id`) ON DELETE SET NULL,
ADD CONSTRAINT `egresoentrega_ibfk_2` FOREIGN KEY (`estadoentrega`) REFERENCES `estadoentrega` (`estadoentrega_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `flete`
--
ALTER TABLE `flete`
ADD CONSTRAINT `flete_ibfk_1` FOREIGN KEY (`documentotipo`) REFERENCES `documentotipo` (`documentotipo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `hojaruta`
--
ALTER TABLE `hojaruta`
ADD CONSTRAINT `hojaruta_ibfk_1` FOREIGN KEY (`estadoentrega`) REFERENCES `estadoentrega` (`estadoentrega_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `infocontactocliente`
--
ALTER TABLE `infocontactocliente`
ADD CONSTRAINT `infocontactocliente_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `cliente` (`cliente_id`) ON DELETE CASCADE,
ADD CONSTRAINT `infocontactocliente_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `infocontacto` (`infocontacto_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `infocontactoflete`
--
ALTER TABLE `infocontactoflete`
ADD CONSTRAINT `infocontactoflete_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `flete` (`flete_id`) ON DELETE CASCADE,
ADD CONSTRAINT `infocontactoflete_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `infocontacto` (`infocontacto_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `infocontactoproveedor`
--
ALTER TABLE `infocontactoproveedor`
ADD CONSTRAINT `infocontactoproveedor_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `proveedor` (`proveedor_id`) ON DELETE CASCADE,
ADD CONSTRAINT `infocontactoproveedor_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `infocontacto` (`infocontacto_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `infocontactovendedor`
--
ALTER TABLE `infocontactovendedor`
ADD CONSTRAINT `infocontactovendedor_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `vendedor` (`vendedor_id`) ON DELETE CASCADE,
ADD CONSTRAINT `infocontactovendedor_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `infocontacto` (`infocontacto_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ingreso`
--
ALTER TABLE `ingreso`
ADD CONSTRAINT `ingreso_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`proveedor_id`) ON DELETE CASCADE,
ADD CONSTRAINT `ingreso_ibfk_2` FOREIGN KEY (`condicioniva`) REFERENCES `condicioniva` (`condicioniva_id`) ON DELETE CASCADE,
ADD CONSTRAINT `ingreso_ibfk_3` FOREIGN KEY (`condicionpago`) REFERENCES `condicionpago` (`condicionpago_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `item`
--
ALTER TABLE `item`
ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`submenu`) REFERENCES `submenu` (`submenu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `itemconfiguracionmenu`
--
ALTER TABLE `itemconfiguracionmenu`
ADD CONSTRAINT `itemconfiguracionmenu_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `configuracionmenu` (`configuracionmenu_id`) ON DELETE CASCADE,
ADD CONSTRAINT `itemconfiguracionmenu_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `item` (`item_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notacredito`
--
ALTER TABLE `notacredito`
ADD CONSTRAINT `notacredito_ibfk_1` FOREIGN KEY (`tipofactura`) REFERENCES `tipofactura` (`tipofactura_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`productomarca`) REFERENCES `productomarca` (`productomarca_id`) ON DELETE CASCADE,
ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`productocategoria`) REFERENCES `productocategoria` (`productocategoria_id`) ON DELETE CASCADE,
ADD CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`productounidad`) REFERENCES `productounidad` (`productounidad_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`provincia`) REFERENCES `provincia` (`provincia_id`) ON DELETE SET NULL,
ADD CONSTRAINT `proveedor_ibfk_2` FOREIGN KEY (`documentotipo`) REFERENCES `documentotipo` (`documentotipo_id`) ON DELETE SET NULL,
ADD CONSTRAINT `proveedor_ibfk_3` FOREIGN KEY (`condicioniva`) REFERENCES `condicioniva` (`condicioniva_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `proveedorproducto`
--
ALTER TABLE `proveedorproducto`
ADD CONSTRAINT `proveedorproducto_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `producto` (`producto_id`) ON DELETE CASCADE,
ADD CONSTRAINT `proveedorproducto_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `proveedor` (`proveedor_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `submenu`
--
ALTER TABLE `submenu`
ADD CONSTRAINT `submenu_ibfk_1` FOREIGN KEY (`menu`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `submenuconfiguracionmenu`
--
ALTER TABLE `submenuconfiguracionmenu`
ADD CONSTRAINT `submenuconfiguracionmenu_ibfk_1` FOREIGN KEY (`compuesto`) REFERENCES `configuracionmenu` (`configuracionmenu_id`) ON DELETE CASCADE,
ADD CONSTRAINT `submenuconfiguracionmenu_ibfk_2` FOREIGN KEY (`compositor`) REFERENCES `submenu` (`submenu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`usuariodetalle`) REFERENCES `usuariodetalle` (`usuariodetalle_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vendedor`
--
ALTER TABLE `vendedor`
ADD CONSTRAINT `vendedor_ibfk_1` FOREIGN KEY (`provincia`) REFERENCES `provincia` (`provincia_id`) ON DELETE SET NULL,
ADD CONSTRAINT `vendedor_ibfk_2` FOREIGN KEY (`documentotipo`) REFERENCES `documentotipo` (`documentotipo_id`) ON DELETE SET NULL,
ADD CONSTRAINT `vendedor_ibfk_3` FOREIGN KEY (`frecuenciaventa`) REFERENCES `frecuenciaventa` (`frecuenciaventa_id`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
