-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 18-12-2018 a las 01:12:28
-- Versión del servidor: 5.5.59-0+deb8u1
-- Versión de PHP: 5.6.33-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `dh.tordo.clean`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(1, 'IVA RESPONSABLE INSCRIPTO', '');

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
(1, 'CUENTA CORRIENTE', ''),
(2, 'CONTADO', ' ');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estadoentrega`
--

INSERT INTO `estadoentrega` (`estadoentrega_id`, `denominacion`) VALUES
(1, 'PENDIENTE'),
(2, 'PLANIFICADO'),
(3, 'EN RUTA'),
(4, 'ENTREGADO');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `flete`
--

INSERT INTO `flete` (`flete_id`, `denominacion`, `documento`, `domicilio`, `localidad`, `latitud`, `longitud`, `observacion`, `documentotipo`) VALUES
(1, 'SIN DEFINIR', 0, ' ', ' ', '0', '0', ' ', 3);

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
-- Estructura de tabla para la tabla `infocontacto`
--

CREATE TABLE IF NOT EXISTS `infocontacto` (
`infocontacto_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valor` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactocliente`
--

CREATE TABLE IF NOT EXISTS `infocontactocliente` (
`infocontactocliente_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactoflete`
--

CREATE TABLE IF NOT EXISTS `infocontactoflete` (
`infocontactoflete_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactoproveedor`
--

CREATE TABLE IF NOT EXISTS `infocontactoproveedor` (
`infocontactoproveedor_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactovendedor`
--

CREATE TABLE IF NOT EXISTS `infocontactovendedor` (
`infocontactovendedor_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(1, 'INGRESO'),
(2, 'EGRESO');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(1, 'FIAMBRES', ''),
(2, 'QUESOS', ''),
(3, 'CONGELADOS', '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(1, 'FELA', ''),
(2, 'PALADINI', '');

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
(1, 'kg', 'KILOS'),
(3, 'lts', 'LITROS'),
(5, 'un', 'UNIDADES');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT;
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
MODIFY `cuentacorrientecliente_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cuentacorrienteproveedor`
--
ALTER TABLE `cuentacorrienteproveedor`
MODIFY `cuentacorrienteproveedor_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `documentotipo`
--
ALTER TABLE `documentotipo`
MODIFY `documentotipo_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `egreso`
--
ALTER TABLE `egreso`
MODIFY `egreso_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `egresoafip`
--
ALTER TABLE `egresoafip`
MODIFY `egresoafip_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `egresocomision`
--
ALTER TABLE `egresocomision`
MODIFY `egresocomision_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `egresodetalle`
--
ALTER TABLE `egresodetalle`
MODIFY `egresodetalle_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `egresodetalleestado`
--
ALTER TABLE `egresodetalleestado`
MODIFY `egresodetalleestado_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `egresoentrega`
--
ALTER TABLE `egresoentrega`
MODIFY `egresoentrega_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `estadocomision`
--
ALTER TABLE `estadocomision`
MODIFY `estadocomision_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `estadoentrega`
--
ALTER TABLE `estadoentrega`
MODIFY `estadoentrega_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `estadomovimientocuenta`
--
ALTER TABLE `estadomovimientocuenta`
MODIFY `estadomovimientocuenta_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `flete`
--
ALTER TABLE `flete`
MODIFY `flete_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `frecuenciaventa`
--
ALTER TABLE `frecuenciaventa`
MODIFY `frecuenciaventa_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `infocontacto`
--
ALTER TABLE `infocontacto`
MODIFY `infocontacto_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `infocontactocliente`
--
ALTER TABLE `infocontactocliente`
MODIFY `infocontactocliente_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `infocontactoflete`
--
ALTER TABLE `infocontactoflete`
MODIFY `infocontactoflete_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `infocontactoproveedor`
--
ALTER TABLE `infocontactoproveedor`
MODIFY `infocontactoproveedor_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `infocontactovendedor`
--
ALTER TABLE `infocontactovendedor`
MODIFY `infocontactovendedor_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `ingreso`
--
ALTER TABLE `ingreso`
MODIFY `ingreso_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `ingresodetalle`
--
ALTER TABLE `ingresodetalle`
MODIFY `ingresodetalle_id` int(11) NOT NULL AUTO_INCREMENT;
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
MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `productocategoria`
--
ALTER TABLE `productocategoria`
MODIFY `productocategoria_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `productodetalle`
--
ALTER TABLE `productodetalle`
MODIFY `productodetalle_id` int(11) NOT NULL AUTO_INCREMENT;
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
MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT;
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
MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;
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
