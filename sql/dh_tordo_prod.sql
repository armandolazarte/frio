-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-08-2021 a las 21:49:23
-- Versión del servidor: 10.1.37-MariaDB-0+deb9u1
-- Versión de PHP: 7.3.4-1+0~20190412071350.37+stretch~1.gbpabc171

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dh.tordo.prod`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup`
--

CREATE TABLE `backup` (
  `backup_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `usuario` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajadiaria`
--

CREATE TABLE `cajadiaria` (
  `cajadiaria_id` int(11) NOT NULL,
  `caja` float DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriacliente`
--

CREATE TABLE `categoriacliente` (
  `categoriacliente_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categoriacliente`
--

INSERT INTO `categoriacliente` (`categoriacliente_id`, `denominacion`) VALUES
(1, 'Despensa'),
(2, 'Kiosco'),
(3, 'Autoservicio'),
(4, 'Panadería'),
(5, 'Carnicería');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chequeproveedordetalle`
--

CREATE TABLE `chequeproveedordetalle` (
  `chequeproveedordetalle_id` int(11) NOT NULL,
  `numero` bigint(20) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado` int(1) DEFAULT NULL,
  `cuentacorrienteproveedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cliente_id` int(11) NOT NULL,
  `razon_social` text COLLATE utf8_spanish_ci,
  `nombre_fantasia` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `descuento` float NOT NULL,
  `iva` float NOT NULL,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `codigopostal` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `localidad` text COLLATE utf8_spanish_ci,
  `latitud` text COLLATE utf8_spanish_ci,
  `longitud` text COLLATE utf8_spanish_ci,
  `impacto_ganancia` int(11) NOT NULL DEFAULT '1',
  `dias_vencimiento_cuenta_corriente` int(11) NOT NULL DEFAULT '0',
  `oculto` int(11) NOT NULL DEFAULT '0',
  `ordenentrega` bigint(2) DEFAULT NULL,
  `entregaminima` bigint(2) DEFAULT NULL,
  `observacion` text COLLATE utf8_spanish_ci,
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL,
  `condicionfiscal` int(11) DEFAULT NULL,
  `frecuenciaventa` int(11) DEFAULT NULL,
  `vendedor` int(11) DEFAULT NULL,
  `flete` int(11) DEFAULT NULL,
  `tipofactura` int(11) DEFAULT NULL,
  `listaprecio` int(11) NOT NULL DEFAULT '0',
  `categoriacliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobrador`
--

CREATE TABLE `cobrador` (
  `cobrador_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `oculto` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `combustible`
--

CREATE TABLE `combustible` (
  `combustible_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `combustible`
--

INSERT INTO `combustible` (`combustible_id`, `denominacion`) VALUES
(1, 'Nafta'),
(2, 'GasOil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicionfiscal`
--

CREATE TABLE `condicionfiscal` (
  `condicionfiscal_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `condicionfiscal`
--

INSERT INTO `condicionfiscal` (`condicionfiscal_id`, `denominacion`, `detalle`) VALUES
(1, 'RESPONSABLE INSCRIPTO', ''),
(2, 'MONOTRIBUTISTA', ''),
(3, 'COMSUMIDOR FINAL', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicioniva`
--

CREATE TABLE `condicioniva` (
  `condicioniva_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `condicioniva`
--

INSERT INTO `condicioniva` (`condicioniva_id`, `denominacion`, `detalle`) VALUES
(1, 'IVA RESPONSABLE INSCRIPTO', ''),
(2, 'MONOTRIBUTO', ' '),
(3, 'CONSUMIDOR FINAL', ' ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicionpago`
--

CREATE TABLE `condicionpago` (
  `condicionpago_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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

CREATE TABLE `configuracion` (
  `configuracion_id` int(11) NOT NULL,
  `razon_social` text COLLATE utf8_spanish_ci,
  `domicilio_comercial` text COLLATE utf8_spanish_ci,
  `cuit` bigint(20) DEFAULT NULL,
  `ingresos_brutos` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_inicio_actividad` date DEFAULT NULL,
  `punto_venta` int(11) NOT NULL,
  `condicioniva` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`configuracion_id`, `razon_social`, `domicilio_comercial`, `cuit`, `ingresos_brutos`, `fecha_inicio_actividad`, `punto_venta`, `condicioniva`) VALUES
(1, 'VALDEZ JULIO', 'España 333 - La Rioja - La Rioja', 20280565424, '000-044426-6', '2018-04-01', 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracionbalance`
--

CREATE TABLE `configuracionbalance` (
  `configuracionbalance_id` int(11) NOT NULL,
  `activo_caja` varchar(10) COLLATE utf8_spanish_ci DEFAULT 'checked',
  `activo_stock_valorizado` varchar(10) COLLATE utf8_spanish_ci DEFAULT 'checked',
  `activo_cuenta_corriente_cliente` varchar(10) COLLATE utf8_spanish_ci DEFAULT 'checked',
  `pasivo_cuenta_corriente_proveedor` varchar(10) COLLATE utf8_spanish_ci DEFAULT 'checked',
  `pasivo_comisiones_pendientes` varchar(10) COLLATE utf8_spanish_ci DEFAULT 'checked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracionbalance`
--

INSERT INTO `configuracionbalance` (`configuracionbalance_id`, `activo_caja`, `activo_stock_valorizado`, `activo_cuenta_corriente_cliente`, `pasivo_cuenta_corriente_proveedor`, `pasivo_comisiones_pendientes`) VALUES
(1, '', 'checked', 'checked', 'checked', 'checked');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracioncomprobante`
--

CREATE TABLE `configuracioncomprobante` (
  `configuracioncomprobante_id` int(11) NOT NULL,
  `dias_alerta_comision` int(11) NOT NULL,
  `dias_vencimiento` int(11) DEFAULT NULL,
  `dias_vencimiento_cuentacorrientecliente` int(11) NOT NULL DEFAULT '0',
  `facturacion_rapida` int(11) NOT NULL DEFAULT '0',
  `parteuno_codebar` int(11) NOT NULL DEFAULT '0',
  `separador_codebar` varchar(1) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `partedos_codebar` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracioncomprobante`
--

INSERT INTO `configuracioncomprobante` (`configuracioncomprobante_id`, `dias_alerta_comision`, `dias_vencimiento`, `dias_vencimiento_cuentacorrientecliente`, `facturacion_rapida`, `parteuno_codebar`, `separador_codebar`, `partedos_codebar`) VALUES
(1, 5, 10, 1, 0, 1, '@', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracionmenu`
--

CREATE TABLE `configuracionmenu` (
  `configuracionmenu_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracionmenu`
--

INSERT INTO `configuracionmenu` (`configuracionmenu_id`, `denominacion`, `nivel`) VALUES
(1, 'DESARROLLADOR', 9),
(2, 'ADMINISTRADOR', 3),
(3, 'FACTURADOR', 1),
(4, 'SUPERVISOR', 2),
(5, 'VENDEDOR', 1),
(6, 'LEGUIZAMON CLAUDIO', 1),
(7, 'MARASSO FERNANDO', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creditoproveedordetalle`
--

CREATE TABLE `creditoproveedordetalle` (
  `creditoproveedordetalle_id` int(11) NOT NULL,
  `numero` bigint(20) NOT NULL,
  `importe` float NOT NULL,
  `fecha` date NOT NULL,
  `cuentacorrienteproveedor_id` int(11) NOT NULL,
  `tipofactura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentacorrientecliente`
--

CREATE TABLE `cuentacorrientecliente` (
  `cuentacorrientecliente_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `referencia` text COLLATE utf8_spanish_ci,
  `importe` float DEFAULT NULL,
  `ingreso` float NOT NULL DEFAULT '0',
  `cliente_id` int(11) DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL,
  `tipomovimientocuenta` int(11) DEFAULT NULL,
  `estadomovimientocuenta` int(11) DEFAULT NULL,
  `cobrador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Estructura de tabla para la tabla `cuentacorrienteproveedor`
--

CREATE TABLE `cuentacorrienteproveedor` (
  `cuentacorrienteproveedor_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `referencia` text COLLATE utf8_spanish_ci,
  `importe` float DEFAULT NULL,
  `ingreso` float NOT NULL DEFAULT '0',
  `proveedor_id` int(11) DEFAULT NULL,
  `ingreso_id` int(11) DEFAULT NULL,
  `ingresotipopago` int(11) DEFAULT NULL,
  `tipomovimientocuenta` int(11) DEFAULT NULL,
  `estadomovimientocuenta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentotipo`
--

CREATE TABLE `documentotipo` (
  `documentotipo_id` int(11) NOT NULL,
  `afip_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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

CREATE TABLE `egreso` (
  `egreso_id` int(11) NOT NULL,
  `punto_venta` int(4) DEFAULT NULL,
  `numero_factura` int(8) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `emitido` int(11) NOT NULL DEFAULT '0',
  `dias_alerta_comision` int(11) NOT NULL DEFAULT '0',
  `dias_vencimiento` int(11) NOT NULL,
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

CREATE TABLE `egresoafip` (
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

CREATE TABLE `egresocomision` (
  `egresocomision_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `valor_comision` float NOT NULL,
  `valor_abonado` float DEFAULT NULL,
  `estadocomision` int(11) DEFAULT NULL,
  `iva` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresodetalle`
--

CREATE TABLE `egresodetalle` (
  `egresodetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento` float DEFAULT '0',
  `valor_descuento` float NOT NULL DEFAULT '0',
  `neto_producto` float NOT NULL DEFAULT '0',
  `costo_producto` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `valor_ganancia` float NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL,
  `egresodetalleestado` int(11) DEFAULT NULL,
  `flete_producto` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresodetalleestado`
--

CREATE TABLE `egresodetalleestado` (
  `egresodetalleestado_id` int(11) NOT NULL,
  `codigo` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `egresodetalleestado`
--

INSERT INTO `egresodetalleestado` (`egresodetalleestado_id`, `codigo`, `denominacion`) VALUES
(1, 'PEN', 'PENDIENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresoentrega`
--

CREATE TABLE `egresoentrega` (
  `egresoentrega_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `flete` int(11) DEFAULT NULL,
  `estadoentrega` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `empleado_id` int(11) NOT NULL,
  `apellido` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `documento` bigint(20) DEFAULT NULL,
  `telefono` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `codigopostal` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `localidad` text COLLATE utf8_spanish_ci,
  `observacion` text COLLATE utf8_spanish_ci,
  `oculto` int(11) NOT NULL DEFAULT '0',
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregacliente`
--

CREATE TABLE `entregacliente` (
  `entregacliente_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `estado` int(1) DEFAULT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `anulada` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregaclientedetalle`
--

CREATE TABLE `entregaclientedetalle` (
  `entregaclientedetalle_id` int(11) NOT NULL,
  `egreso_id` int(11) NOT NULL,
  `monto` double DEFAULT NULL,
  `entregacliente_id` int(11) NOT NULL,
  `parcial` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadocomision`
--

CREATE TABLE `estadocomision` (
  `estadocomision_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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

CREATE TABLE `estadoentrega` (
  `estadoentrega_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estadoentrega`
--

INSERT INTO `estadoentrega` (`estadoentrega_id`, `denominacion`) VALUES
(1, 'PENDIENTE'),
(2, 'PLANIFICADO'),
(3, 'EN RUTA'),
(4, 'ENTREGADO'),
(5, 'CANCELADO'),
(6, 'POSTERGADO'),
(7, 'CERRADA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadomovimientocuenta`
--

CREATE TABLE `estadomovimientocuenta` (
  `estadomovimientocuenta_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
-- Estructura de tabla para la tabla `estadopedido`
--

CREATE TABLE `estadopedido` (
  `estadopedido_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `estadopedido`
--

INSERT INTO `estadopedido` (`estadopedido_id`, `denominacion`) VALUES
(1, 'SOLICITADO'),
(2, 'PROCESADO'),
(3, 'CANCELADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flete`
--

CREATE TABLE `flete` (
  `flete_id` int(11) NOT NULL,
  `denominacion` text COLLATE utf8_spanish_ci,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `localidad` text COLLATE utf8_spanish_ci,
  `latitud` text COLLATE utf8_spanish_ci,
  `longitud` text COLLATE utf8_spanish_ci,
  `observacion` text COLLATE utf8_spanish_ci,
  `documentotipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frecuenciaventa`
--

CREATE TABLE `frecuenciaventa` (
  `frecuenciaventa_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dia_1` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dia_2` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `frecuenciaventa`
--

INSERT INTO `frecuenciaventa` (`frecuenciaventa_id`, `denominacion`, `dia_1`, `dia_2`) VALUES
(1, 'Frecuencia 1', 'Lunes', 'Jueves'),
(2, 'Frecuencia 2', 'Martes', 'Viernes'),
(3, 'Frecuencia 3', 'Miércoles', 'Sábado'),
(4, 'Frecuencia 4', 'Domingo', 'Domingo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gasto`
--

CREATE TABLE `gasto` (
  `gasto_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci,
  `gastocategoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Estructura de tabla para la tabla `gastocategoria`
--

CREATE TABLE `gastocategoria` (
  `gastocategoria_id` int(11) NOT NULL,
  `codigo` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `denominacion` text COLLATE utf8_spanish_ci,
  `oculto` int(11) NOT NULL DEFAULT '0',
  `gastosubcategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastosubcategoria`
--

CREATE TABLE `gastosubcategoria` (
  `gastosubcategoria_id` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `denominacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `gastosubcategoria`
--

INSERT INTO `gastosubcategoria` (`gastosubcategoria_id`, `codigo`, `denominacion`) VALUES
(1, 'LI', 'Liquidación'),
(2, 'VA', 'Varios'),
(3, 'GF', 'Gasto Fijo'),
(4, 'IM', 'Impuestos'),
(5, 'CM', 'Combustible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hojaruta`
--

CREATE TABLE `hojaruta` (
  `hojaruta_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `flete_id` int(11) DEFAULT NULL,
  `egreso_ids` varchar(500) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estadoentrega` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontacto`
--

CREATE TABLE `infocontacto` (
  `infocontacto_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valor` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactocliente`
--

CREATE TABLE `infocontactocliente` (
  `infocontactocliente_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactoflete`
--

CREATE TABLE `infocontactoflete` (
  `infocontactoflete_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactoproveedor`
--

CREATE TABLE `infocontactoproveedor` (
  `infocontactoproveedor_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infocontactovendedor`
--

CREATE TABLE `infocontactovendedor` (
  `infocontactovendedor_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE `ingreso` (
  `ingreso_id` int(11) NOT NULL,
  `punto_venta` int(4) DEFAULT NULL,
  `numero_factura` int(8) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `percepcion_iva` float NOT NULL DEFAULT '0',
  `costo_distribucion` float DEFAULT NULL,
  `costo_total` float DEFAULT NULL,
  `costo_total_iva` float DEFAULT NULL,
  `actualiza_precio_producto` int(11) NOT NULL DEFAULT '1',
  `actualiza_precio_proveedor` int(11) NOT NULL DEFAULT '0',
  `actualiza_stock` int(11) NOT NULL DEFAULT '1',
  `proveedor` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL,
  `condicionpago` int(11) DEFAULT NULL,
  `tipofactura` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresodetalle`
--

CREATE TABLE `ingresodetalle` (
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
-- Estructura de tabla para la tabla `ingresotipopago`
--

CREATE TABLE `ingresotipopago` (
  `ingresotipopago_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ingresotipopago`
--

INSERT INTO `ingresotipopago` (`ingresotipopago_id`, `denominacion`) VALUES
(1, 'CHEQUE'),
(2, 'DEPÓSITO'),
(3, 'EFECTIVO'),
(4, 'CRÉDITO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `submenu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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

CREATE TABLE `itemconfiguracionmenu` (
  `itemconfiguracionmenu_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `itemconfiguracionmenu`
--

INSERT INTO `itemconfiguracionmenu` (`itemconfiguracionmenu_id`, `compuesto`, `compositor`) VALUES
(153, 1, 1),
(154, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `listaprecio`
--

CREATE TABLE `listaprecio` (
  `listaprecio_id` int(11) NOT NULL,
  `denominacion` varchar(250) NOT NULL,
  `condicion` varchar(5) NOT NULL,
  `porcentaje` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `listaprecio`
--

INSERT INTO `listaprecio` (`listaprecio_id`, `denominacion`, `condicion`, `porcentaje`) VALUES
(1, 'Default', '+', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `localidad`
--

CREATE TABLE `localidad` (
  `localidad_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(14, 'INGRESOS', 'fa-archive', '#'),
(15, 'GASTOS', 'fa-usd', '#'),
(16, 'VEHICULOS', 'fa-car', '#'),
(17, 'EMPLEADOS', 'fa-users', '#');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientotipo`
--

CREATE TABLE `movimientotipo` (
  `movimientotipo_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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

CREATE TABLE `notacredito` (
  `notacredito_id` int(11) NOT NULL,
  `punto_venta` int(11) DEFAULT '0',
  `numero_factura` int(11) NOT NULL DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `egreso_id` int(11) DEFAULT NULL,
  `emitido_afip` int(11) NOT NULL DEFAULT '0',
  `tipofactura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notacreditodetalle`
--

CREATE TABLE `notacreditodetalle` (
  `notacreditodetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `valor_descuento` float DEFAULT NULL,
  `neto_producto` float NOT NULL DEFAULT '0',
  `costo_producto` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `valor_ganancia` float NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `egreso_id` int(11) NOT NULL,
  `notacredito_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notacreditoproveedor`
--

CREATE TABLE `notacreditoproveedor` (
  `notacreditoproveedor_id` int(11) NOT NULL,
  `punto_venta` int(11) DEFAULT NULL,
  `numero_factura` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `ingreso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notacreditoproveedordetalle`
--

CREATE TABLE `notacreditoproveedordetalle` (
  `notacreditoproveedordetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento1` float DEFAULT NULL,
  `descuento2` float DEFAULT NULL,
  `descuento3` float DEFAULT NULL,
  `costo_producto` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `percepcion_iva` float DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `ingreso_id` int(11) DEFAULT NULL,
  `notacreditoproveedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidovendedor`
--

CREATE TABLE `pedidovendedor` (
  `pedidovendedor_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `estadopedido` int(11) DEFAULT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidovendedordetalle`
--

CREATE TABLE `pedidovendedordetalle` (
  `pedidovendedordetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `valor_descuento` float NOT NULL,
  `costo_producto` float DEFAULT NULL,
  `iva` float NOT NULL,
  `importe` float DEFAULT NULL,
  `valor_ganancia` float NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `pedidovendedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE `presupuesto` (
  `presupuesto_id` int(11) NOT NULL,
  `punto_venta` int(4) DEFAULT NULL,
  `numero_factura` int(8) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `importe_total` float DEFAULT NULL,
  `cliente` int(11) DEFAULT NULL,
  `vendedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuestodetalle`
--

CREATE TABLE `presupuestodetalle` (
  `presupuestodetalle_id` int(11) NOT NULL,
  `codigo_producto` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_producto` text COLLATE utf8_spanish_ci,
  `cantidad` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `valor_descuento` float DEFAULT NULL,
  `costo_producto` float DEFAULT NULL,
  `iva` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `presupuesto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `producto_id` int(11) NOT NULL,
  `codigo` bigint(20) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `peso` float NOT NULL DEFAULT '0',
  `costo` float NOT NULL,
  `descuento` float NOT NULL DEFAULT '0',
  `flete` float NOT NULL DEFAULT '0',
  `porcentaje_ganancia` float NOT NULL,
  `iva` float NOT NULL,
  `exento` int(11) NOT NULL DEFAULT '0',
  `no_gravado` int(11) NOT NULL DEFAULT '0',
  `stock_minimo` int(11) NOT NULL,
  `stock_ideal` int(11) NOT NULL,
  `dias_reintegro` int(11) NOT NULL,
  `oculto` int(11) NOT NULL DEFAULT '0',
  `detalle` text COLLATE utf8_spanish_ci,
  `barcode` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `productomarca` int(11) DEFAULT NULL,
  `productocategoria` int(11) DEFAULT NULL,
  `productounidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productocategoria`
--

CREATE TABLE `productocategoria` (
  `productocategoria_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productocategoria`
--

INSERT INTO `productocategoria` (`productocategoria_id`, `denominacion`, `detalle`) VALUES
(1, 'FIAMBRES', ''),
(2, 'QUESOS', ''),
(3, 'CONGELADOS', ''),
(4, 'SECO', ''),
(5, 'ENLATADOS', ''),
(6, 'PASTA', ''),
(7, 'FLETE', ''),
(8, 'OTROS', ''),
(9, 'CARNES', ''),
(10, 'COMISION', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productodetalle`
--

CREATE TABLE `productodetalle` (
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

CREATE TABLE `productomarca` (
  `productomarca_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productounidad`
--

CREATE TABLE `productounidad` (
  `productounidad_id` int(11) NOT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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

CREATE TABLE `proveedor` (
  `proveedor_id` int(11) NOT NULL,
  `razon_social` text COLLATE utf8_spanish_ci,
  `documento` bigint(20) DEFAULT NULL,
  `domicilio` text COLLATE utf8_spanish_ci,
  `codigopostal` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `localidad` text COLLATE utf8_spanish_ci,
  `oculto` int(11) NOT NULL DEFAULT '0',
  `observacion` text COLLATE utf8_spanish_ci NOT NULL,
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL,
  `condicioniva` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedorproducto`
--

CREATE TABLE `proveedorproducto` (
  `proveedorproducto_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincia`
--

CREATE TABLE `provincia` (
  `provincia_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
-- Estructura de tabla para la tabla `salario`
--

CREATE TABLE `salario` (
  `salario_id` int(11) NOT NULL,
  `periodo` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `empleado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
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

CREATE TABLE `submenu` (
  `submenu_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `detalle` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(60, 'Cta Corriente Proveedor', 'fa-table', '/cuentacorrienteproveedor/panel', '', 7),
(61, 'Listar Notas Crédito', 'fa-table', '/notacredito/listar', '', 10),
(62, 'Gastos', 'fa-cog', '/gasto/panel', '', 15),
(63, 'Categoria de Gasto', 'fa-cog', '/gastocategoria/panel', '', 15),
(64, 'Estadísticas', 'fa-bar-chart', '/vendedor/estadisticas', '', 12),
(65, 'Ocultos', 'fa-eye-slash', '/cliente/ocultos', '', 9),
(66, 'Ocultos', 'fa-eye-slash', '/producto/ocultos', '', 8),
(67, 'Ocultos', 'fa-eye-slash', '/proveedor/ocultos', '', 7),
(68, 'Presupuestos', 'fa-file-text', '/presupuesto/listar', '', 10),
(69, 'Lista de Precio', 'fa-usd', '/producto/vdr_lista_precio', '', 8),
(70, 'Inventario', 'fa-cube', '/stock/vdr_stock', '', 8),
(71, 'Cuentas Corrientes', 'fa-table', '/cuentacorrientecliente/vdr_panel', '', 9),
(72, 'Pedidos', 'fa-file-text', '/pedidovendedor/panel', '', 10),
(73, 'Notas de Credito', 'fa-usd', '/proveedor/creditos', '', 7),
(74, 'Agregar Lista de Precio', 'fa-plus', '/listaprecio/panel', '', 8),
(75, 'Cobranzas', 'fa-usd', '/entregaclientedetalle/panel', '', 10),
(76, 'Panel', 'fa-cube', '/vehiculo/panel', '', 16),
(77, 'Marcas', 'fa-cog', '/vehiculomarca/panel', '', 16),
(78, 'Modelos', 'fa-cog', '/vehiculomodelo/panel', '', 16),
(79, 'Panel', 'fa-cube', '/empleado/listar', '', 17),
(80, 'Ocultos', 'fa-eye-slash', '/empleado/listar_ocultos', '', 17),
(81, 'Salario', 'fa-file-text-o', '/salario/listar', '', 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submenuconfiguracionmenu`
--

CREATE TABLE `submenuconfiguracionmenu` (
  `submenuconfiguracionmenu_id` int(11) NOT NULL,
  `compuesto` int(11) DEFAULT NULL,
  `compositor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `submenuconfiguracionmenu`
--

INSERT INTO `submenuconfiguracionmenu` (`submenuconfiguracionmenu_id`, `compuesto`, `compositor`) VALUES
(7032, 5, 71),
(7033, 5, 69),
(7034, 5, 70),
(7035, 5, 72),
(8172, 6, 69),
(8173, 4, 51),
(8174, 4, 55),
(8175, 4, 57),
(8176, 4, 61),
(8177, 4, 39),
(8178, 4, 36),
(8179, 4, 58),
(8180, 4, 37),
(8181, 4, 38),
(8182, 4, 60),
(8183, 4, 43),
(8184, 4, 64),
(8185, 4, 52),
(8186, 4, 27),
(8187, 4, 50),
(8188, 4, 65),
(8189, 4, 72),
(8190, 4, 75),
(9722, 3, 50),
(9723, 3, 51),
(9724, 3, 55),
(9725, 3, 57),
(9726, 3, 61),
(9727, 3, 37),
(9728, 3, 27),
(9729, 3, 72),
(9730, 3, 69),
(10757, 2, 51),
(10758, 2, 50),
(10759, 2, 55),
(10760, 2, 39),
(10761, 2, 33),
(10762, 2, 36),
(10763, 2, 24),
(10764, 2, 37),
(10765, 2, 27),
(10766, 2, 38),
(10767, 2, 22),
(10768, 2, 43),
(10769, 2, 44),
(10770, 2, 52),
(10771, 2, 54),
(10772, 2, 59),
(10773, 2, 56),
(10774, 2, 60),
(10775, 2, 64),
(10776, 2, 65),
(10777, 2, 68),
(10778, 2, 61),
(10779, 2, 57),
(10780, 2, 72),
(10781, 2, 73),
(10782, 2, 67),
(10783, 2, 74),
(10784, 2, 58),
(10785, 2, 66),
(10786, 2, 29),
(10787, 2, 30),
(10788, 2, 31),
(10789, 2, 75),
(10790, 2, 76),
(10791, 2, 77),
(10792, 2, 78),
(10793, 2, 62),
(10794, 2, 63),
(10795, 2, 79),
(10796, 2, 80),
(10797, 2, 32),
(10798, 2, 9),
(10799, 2, 81),
(10800, 1, 51),
(10801, 1, 39),
(10802, 1, 33),
(10803, 1, 36),
(10804, 1, 24),
(10805, 1, 37),
(10806, 1, 27),
(10807, 1, 38),
(10808, 1, 22),
(10809, 1, 43),
(10810, 1, 44),
(10811, 1, 52),
(10812, 1, 54),
(10813, 1, 59),
(10814, 1, 56),
(10815, 1, 60),
(10816, 1, 62),
(10817, 1, 63),
(10818, 1, 64),
(10819, 1, 65),
(10820, 1, 61),
(10821, 1, 55),
(10822, 1, 72),
(10823, 1, 68),
(10824, 1, 57),
(10825, 1, 50),
(10826, 1, 73),
(10827, 1, 67),
(10828, 1, 74),
(10829, 1, 58),
(10830, 1, 70),
(10831, 1, 66),
(10832, 1, 29),
(10833, 1, 30),
(10834, 1, 31),
(10835, 1, 75),
(10836, 1, 76),
(10837, 1, 77),
(10838, 1, 78),
(10839, 1, 79),
(10840, 1, 80),
(10841, 1, 45),
(10842, 1, 34),
(10843, 1, 35),
(10844, 1, 49),
(10845, 1, 35),
(10846, 1, 32),
(10847, 1, 8),
(10848, 1, 9),
(10849, 1, 81),
(10962, 7, 50),
(10963, 7, 51),
(10964, 7, 55),
(10965, 7, 57),
(10966, 7, 61),
(10967, 7, 72),
(10968, 7, 75),
(10969, 7, 39),
(10970, 7, 59),
(10971, 7, 36),
(10972, 7, 58),
(10973, 7, 27),
(10974, 7, 37),
(10975, 7, 65),
(10976, 7, 43),
(10977, 7, 64),
(10978, 7, 52);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipofactura`
--

CREATE TABLE `tipofactura` (
  `tipofactura_id` int(11) NOT NULL,
  `afip_id` int(11) NOT NULL,
  `nomenclatura` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `denominacion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `plantilla_impresion` text COLLATE utf8_spanish_ci NOT NULL,
  `detalle` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipofactura`
--

INSERT INTO `tipofactura` (`tipofactura_id`, `afip_id`, `nomenclatura`, `denominacion`, `plantilla_impresion`, `detalle`) VALUES
(1, 1, 'A', '', 'facturaA', ''),
(2, 0, 'R', 'REMITO', 'remitoR', ''),
(3, 6, 'B', ' ', 'facturaB', ' '),
(4, 3, 'NCA', 'NOTA DE CRÉDITO A', 'notacreditoNC', ' '),
(5, 8, 'NCB', 'NOTA DE CRÉDITO B', 'notacreditoNC', ' '),
(6, 0, 'NCR', 'NOTA CRÉDITO R', 'notacreditoNC', ' '),
(7, 0, 'P', 'PRESUPUESTO', 'presupuestoP', ' ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipomovimientocuenta`
--

CREATE TABLE `tipomovimientocuenta` (
  `tipomovimientocuenta_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipomovimientocuenta`
--

INSERT INTO `tipomovimientocuenta` (`tipomovimientocuenta_id`, `denominacion`) VALUES
(1, 'DEUDA'),
(2, 'INGRESO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferenciaproveedordetalle`
--

CREATE TABLE `transferenciaproveedordetalle` (
  `transferenciaproveedordetalle_id` int(11) NOT NULL,
  `numero` bigint(20) DEFAULT NULL,
  `cuentacorrienteproveedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `denominacion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nivel` int(1) DEFAULT NULL,
  `usuariodetalle` int(11) DEFAULT NULL,
  `configuracionmenu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `denominacion`, `nivel`, `usuariodetalle`, `configuracionmenu`) VALUES
(1, 'admin', 3, 1, 2),
(2, 'desarrollador', 9, 2, 1),
(3, 'frovira', 3, 3, 2),
(4, 'CREINOSO', 3, 4, 2),
(8, 'mrovira', 3, 8, 2),
(10, 'coliva', 1, 10, 3),
(12, 'supervisor', 2, 12, 4),
(13, 'fmarasso', 2, 13, 7),
(14, 'lvergara', 3, 14, 1),
(15, 'restevez', 1, 15, 5),
(16, 'msalguero', 1, 16, 5),
(17, 'emachuca', 1, 17, 5),
(19, 'sanagasta', 1, 19, 5),
(21, 'faguero', 1, 21, 5),
(25, 'jrodriguez', 1, 25, 5),
(26, 'afederico', 1, 26, 5),
(27, 'PDANIEL', 1, 27, 5),
(29, 'mismael', 1, 29, 5),
(31, 'pprueba', 2, 31, 7),
(33, 'HABDALA', 1, 33, 3),
(34, 'LUISC', 1, 34, 3),
(35, 'romeroale', 1, 35, 5),
(36, 'fcontreras', 1, 36, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariodetalle`
--

CREATE TABLE `usuariodetalle` (
  `usuariodetalle_id` int(11) NOT NULL,
  `apellido` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `correoelectronico` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `token` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuariodetalle`
--

INSERT INTO `usuariodetalle` (`usuariodetalle_id`, `apellido`, `nombre`, `correoelectronico`, `token`) VALUES
(1, 'Admin', 'admin', 'admin@admin.com', '4850fc35306cb8590e00564f5462e1bb'),
(2, 'Desarrollador', 'Admin', 'infozamba@gmail.com', '7ea60ee02f2b59bc8841b2b481c11d68'),
(3, 'Rovira', 'Fernando', '', '912af22a03b0ee177c7212a08bad7f83'),
(4, 'REINOSO', 'CRISTIAN', '', '6ff095926f86fbd15931c291f6574135'),
(8, 'ROVIRA ', 'Martin', '', '81938212cf803f92915c883801fb8ea8'),
(10, 'oliva', 'carlos', '', '156f56a53b8d1b105c02d8f6246142e6'),
(12, 'Supervisor', 'Usuario', 'hu.ce.ro@gmail.com', 'f7312a9b91a9886e9a5fa512e8efb598'),
(13, 'Marasso', 'Fernando', '', 'e339ed48e64421636b66baaa11708370'),
(14, 'Vergara', 'Luis Federico', 'hu.ce.ro@gmail.com', '1260be9f2e194fef6d5f8b27b65f2103'),
(15, 'ROGELIO', 'ESTEVEZ', 'rogelio@gmail.com', '820d1268ec7b534f01037f3b6afcbc30'),
(16, 'SALGUERO', 'MATIAS', 'msalguero@gmail.com', 'bd8dbda7196a6f0034db160c0abe7467'),
(17, 'Machuca', 'Enzo', 'machuca@gmail.com', '57e24561af28a151659961b2fd8f5934'),
(19, 'CONTRERAS', 'FLAVIO', '', '4bb2764529dafab673c8f1997dad4834'),
(21, 'AGUERO', 'FEDERICO JUAN', '', 'b804553ce62bda2006bb5ed123209c1d'),
(25, 'RODRIGUEZ ', 'JONATHAN EXEQUIEL', '', 'b74ded33bfb88d8d3cd61f9b7947fbd3'),
(26, 'AGUERO', 'FEDERICO GASTRONOMIA', '', '1a43c6c4224b092c5c2898d607ae3754'),
(27, 'PEREZLINDO', 'DANIEL', '', '757d1b6d4ccf2c7240e9a17c6e8102cf'),
(29, 'Muñiz', 'Ismael', '', 'cbb71f52ada2d436b1e82c95ac3e307a'),
(31, 'Prueba', 'Prueba', '', '8fb730d676bfaa9205d22813638207fa'),
(33, 'ABDALA', 'HECTOR', '', '99a9a4d5aa460513628d3d39d4d71177'),
(34, 'CARRIZO', 'LUIS', '', 'a048b4801d47498b3149572a095bd5aa'),
(35, 'romero', 'alejandro', '', '08259bab182a6bba62d665d1c3da66cc'),
(36, 'Contreras', 'Flavio', '', '196332af7cf2251f08bb5027132b6f12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariovendedor`
--

CREATE TABLE `usuariovendedor` (
  `usuariovendedor_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `vendedor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuariovendedor`
--

INSERT INTO `usuariovendedor` (`usuariovendedor_id`, `usuario_id`, `vendedor_id`) VALUES
(1, 15, 2),
(2, 16, 3),
(3, 12, 5),
(4, 13, 5),
(5, 17, 4),
(6, 18, 11),
(7, 19, 6),
(8, 21, 7),
(9, 22, 10),
(11, 8, 1),
(12, 24, 13),
(13, 25, 14),
(14, 26, 15),
(15, 27, 16),
(16, 29, 17),
(18, 36, 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculo`
--

CREATE TABLE `vehiculo` (
  `vehiculo_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dominio` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `detalle` text COLLATE utf8_spanish_ci,
  `combustible` int(11) DEFAULT NULL,
  `vehiculomodelo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculocombustible`
--

CREATE TABLE `vehiculocombustible` (
  `vehiculocombustible_id` int(11) NOT NULL,
  `cantidad` float DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `vehiculo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculomarca`
--

CREATE TABLE `vehiculomarca` (
  `vehiculomarca_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `vehiculomarca`
--

INSERT INTO `vehiculomarca` (`vehiculomarca_id`, `denominacion`) VALUES
(1, 'FIAT'),
(2, 'RENAULT'),
(3, 'FORD'),
(4, 'MERCEDES-BENZ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculomodelo`
--

CREATE TABLE `vehiculomodelo` (
  `vehiculomodelo_id` int(11) NOT NULL,
  `denominacion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `capacidad_tanque` int(11) DEFAULT NULL,
  `vehiculomarca` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `vehiculomodelo`
--

INSERT INTO `vehiculomodelo` (`vehiculomodelo_id`, `denominacion`, `capacidad_tanque`, `vehiculomarca`) VALUES
(1, 'FIORINO', 70, 1),
(2, 'MASTER', 100, 2),
(3, 'TRANSIT ', 100, 3),
(4, 'sprinter', 100, 4),
(5, 'CARGO 916', 150, 3),
(6, 'CARGO', 200, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedor`
--

CREATE TABLE `vendedor` (
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
  `oculto` int(11) NOT NULL DEFAULT '0',
  `provincia` int(11) DEFAULT NULL,
  `documentotipo` int(11) DEFAULT NULL,
  `frecuenciaventa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `version`
--

CREATE TABLE `version` (
  `version_id` int(11) NOT NULL,
  `version` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `changelog` text COLLATE utf8_spanish_ci,
  `archivo` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `activa` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `version`
--

INSERT INTO `version` (`version_id`, `version`, `changelog`, `archivo`, `fecha`, `activa`) VALUES
(1, '1.1.0', '<p><strong><span style=\"color: #000080;\">Versi&oacute;n 1.0.0</span></strong></p>', 'tordoapp_1.1.0.apk', '2020-07-22', 0),
(2, '1.1.1', '<p><strong><span style=\"color: #000080;\">Versi&oacute;n 1.1.1</span></strong></p>', 'tordoapp_1.1.1.apk', '2020-08-01', 0),
(3, '1.2.0', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.2.0.apk', '2020-08-18', 0),
(4, '1.2.1', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.2.1.apk', '2020-08-28', 0),
(5, '1.2.2', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\n<ul>\n<li>Se quitaron permisos de Acceso</li>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\n<ul>\n<li>Migracion a Prod</li>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\n<ul>\n<li>Cambio en la forma de crear Cobros</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\n<ul>\n<li>Primera Version Estable</li>\n</ul>', 'tordoapp_1.2.2.apk', '2020-09-06', 0),
(6, '1.3.1', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\n<ul>\n<li>Se mejoro la Interfaz</li>\n<li>Se agrego el duplicado de producto en pedidos</li>\n<li>Se muestra articulo con stock positivo</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\n<ul>\n<li>Se quitaron permisos de Acceso</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\n<ul>\n<li>Migracion a Prod</li>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\n<ul>\n<li>Cambio en la forma de crear Cobros</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\n<ul>\n<li>Primera Version Estable</li>\n</ul>', 'tordoapp_1.3.1.apk', '2020-10-26', 0),
(7, '1.3.2', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.3.2.apk', '2020-10-27', 0),
(8, '1.3.3', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\r\n<ul>\r\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\r\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.3.3.apk', '2020-10-28', 0),
(9, '1.4.1', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\r\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\r\n<li>Se agrego pantalla Home</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\r\n<ul>\r\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\r\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.4.1.apk', '2020-11-05', 0),
(10, '1.5.1', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.5.1</strong></span></p>\r\n<ul>\r\n<li>Se soluciono fix que no mantenia cobros ni pedidos al momento de actualizar datos</li>\r\n<li>Se agrego la funcionalidad de alerta de clientes con mas de 15 dias sin venta</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\r\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\r\n<li>Se agrego pantalla Home</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\r\n<ul>\r\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\r\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.5.1.apk', '2021-03-17', 0),
(11, '1.6.1', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.1</strong></span></p>\r\n<ul>\r\n<li>Agregado validacion clientes morosos</li>\r\n<li>Actualizacion de librerias</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.5.1</strong></span></p>\r\n<ul>\r\n<li>Se soluciono fix que no mantenia cobros ni pedidos al momento de actualizar datos</li>\r\n<li>Se agrego la funcionalidad de alerta de clientes con mas de 15 dias sin venta</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\r\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\r\n<li>Se agrego pantalla Home</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\r\n<ul>\r\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\r\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.6.1.apk', '2021-03-19', 0),
(12, '1.6.2', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.2</strong></span></p>\n<ul>\n<li>Solucion fix pagos parciales</li>\n<li>Actualizacion de librerias</li>\n</ul>\n\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.1</strong></span></p>\n<ul>\n<li>Agregado validacion clientes morosos</li>\n<li>Actualizacion de librerias</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.5.1</strong></span></p>\n<ul>\n<li>Se soluciono fix que no mantenia cobros ni pedidos al momento de actualizar datos</li>\n<li>Se agrego la funcionalidad de alerta de clientes con mas de 15 dias sin venta</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\n<ul>\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\n<li>Se agrego pantalla Home</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\n<ul>\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\n<ul>\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\n<li>Mejora visual en el elemento \"Saldo\"</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\n<ul>\n<li>Se mejoro la Interfaz</li>\n<li>Se agrego el duplicado de producto en pedidos</li>\n<li>Se muestra articulo con stock positivo</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\n<ul>\n<li>Se quitaron permisos de Acceso</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\n<ul>\n<li>Migracion a Prod</li>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\n<ul>\n<li>Cambio en la forma de crear Cobros</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\n<ul>\n<li>Primera Version Estable</li>\n</ul>', 'tordoapp_1.6.2.apk', '2021-05-27', 0),
(13, '1.6.3', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.3</strong></span></p>\n<ul>\n<li>Solucion fix duplicacion</li>\n<li>Bloqueo desactualizado</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.2</strong></span></p>\n<ul>\n<li>Solucion fix pagos parciales</li>\n<li>Actualizacion de librerias</li>\n</ul>\n\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.1</strong></span></p>\n<ul>\n<li>Agregado validacion clientes morosos</li>\n<li>Actualizacion de librerias</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.5.1</strong></span></p>\n<ul>\n<li>Se soluciono fix que no mantenia cobros ni pedidos al momento de actualizar datos</li>\n<li>Se agrego la funcionalidad de alerta de clientes con mas de 15 dias sin venta</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\n<ul>\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\n<li>Se agrego pantalla Home</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\n<ul>\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\n<ul>\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\n<li>Mejora visual en el elemento \"Saldo\"</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\n<ul>\n<li>Se mejoro la Interfaz</li>\n<li>Se agrego el duplicado de producto en pedidos</li>\n<li>Se muestra articulo con stock positivo</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\n<ul>\n<li>Se quitaron permisos de Acceso</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\n<ul>\n<li>Migracion a Prod</li>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\n<ul>\n<li>Cambio en la forma de crear Cobros</li>\n</ul>\n\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\n<ul>\n<li>Primera Version Estable</li>\n</ul>', 'tordoapp_1.6.3.apk', '2021-06-17', 0),
(14, '1.6.4', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.3</strong></span></p>\r\n<ul>\r\n<li>Solucion fix duplicacion</li>\r\n<li>Bloqueo desactualizado</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.2</strong></span></p>\r\n<ul>\r\n<li>Solucion fix pagos parciales</li>\r\n<li>Actualizacion de librerias</li>\r\n</ul>\r\n\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.1</strong></span></p>\r\n<ul>\r\n<li>Agregado validacion clientes morosos</li>\r\n<li>Actualizacion de librerias</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.5.1</strong></span></p>\r\n<ul>\r\n<li>Se soluciono fix que no mantenia cobros ni pedidos al momento de actualizar datos</li>\r\n<li>Se agrego la funcionalidad de alerta de clientes con mas de 15 dias sin venta</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\r\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\r\n<li>Se agrego pantalla Home</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\r\n<ul>\r\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\r\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.6.4.apk', '2021-06-18', 0),
(15, '1.6.5', '<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.5</strong></span></p>\r\n<ul>\r\n<li>Solucion fix duplicacion</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.3</strong></span></p>\r\n<ul>\r\n<li>Solucion fix duplicacion</li>\r\n<li>Bloqueo desactualizado</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.2</strong></span></p>\r\n<ul>\r\n<li>Solucion fix pagos parciales</li>\r\n<li>Actualizacion de librerias</li>\r\n</ul>\r\n\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.6.1</strong></span></p>\r\n<ul>\r\n<li>Agregado validacion clientes morosos</li>\r\n<li>Actualizacion de librerias</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.5.1</strong></span></p>\r\n<ul>\r\n<li>Se soluciono fix que no mantenia cobros ni pedidos al momento de actualizar datos</li>\r\n<li>Se agrego la funcionalidad de alerta de clientes con mas de 15 dias sin venta</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.4.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la obtencion de clientes para que no traigan ocultos</li>\r\n<li>Se mejoro la obtencion de productos para que no traigan ocultos</li>\r\n<li>Se agrego pantalla Home</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.3</strong></span></p>\r\n<ul>\r\n<li>Se agrego monto diario y acumulado en el listado de cobros</li>\r\n<li>Se agrego el detalle de facturas cobradas en los CARD de cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.2</strong></span></p>\r\n<ul>\r\n<li>Se oculto boton de detalles de movimietos de facturas saldadas</li>\r\n<li>Los cobros enviados y no cerrados por el administrador, se siguen mostrando</li>\r\n<li>Se  agrego validacion para no cobrar mas de lo que debe el cliente</li>\r\n<li>Mejora visual en el elemento \"Saldo\"</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.3.1</strong></span></p>\r\n<ul>\r\n<li>Se mejoro la Interfaz</li>\r\n<li>Se agrego el duplicado de producto en pedidos</li>\r\n<li>Se muestra articulo con stock positivo</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.2</strong></span></p>\r\n<ul>\r\n<li>Se quitaron permisos de Acceso</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.1</strong></span></p>\r\n<ul>\r\n<li>Migracion a Prod</li>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.2.0</strong></span></p>\r\n<ul>\r\n<li>Cambio en la forma de crear Cobros</li>\r\n</ul>\r\n\r\n<p><span style=\"color: #000080;\"><strong>Versi&oacute;n 1.1.1</strong></span></p>\r\n<ul>\r\n<li>Primera Version Estable</li>\r\n</ul>', 'tordoapp_1.6.5.apk', '2021-07-16', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `backup`
--
ALTER TABLE `backup`
  ADD PRIMARY KEY (`backup_id`);

--
-- Indices de la tabla `cajadiaria`
--
ALTER TABLE `cajadiaria`
  ADD PRIMARY KEY (`cajadiaria_id`);

--
-- Indices de la tabla `categoriacliente`
--
ALTER TABLE `categoriacliente`
  ADD PRIMARY KEY (`categoriacliente_id`);

--
-- Indices de la tabla `chequeproveedordetalle`
--
ALTER TABLE `chequeproveedordetalle`
  ADD PRIMARY KEY (`chequeproveedordetalle_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cliente_id`),
  ADD KEY `provincia` (`provincia`),
  ADD KEY `documentotipo` (`documentotipo`),
  ADD KEY `condicioniva` (`condicioniva`),
  ADD KEY `condicionfiscal` (`condicionfiscal`),
  ADD KEY `frecuenciaventa` (`frecuenciaventa`,`vendedor`),
  ADD KEY `vendedor` (`vendedor`),
  ADD KEY `flete` (`flete`),
  ADD KEY `tipofactura` (`tipofactura`);

--
-- Indices de la tabla `cobrador`
--
ALTER TABLE `cobrador`
  ADD PRIMARY KEY (`cobrador_id`);

--
-- Indices de la tabla `combustible`
--
ALTER TABLE `combustible`
  ADD PRIMARY KEY (`combustible_id`);

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
  ADD PRIMARY KEY (`configuracion_id`),
  ADD KEY `condicioniva` (`condicioniva`);

--
-- Indices de la tabla `configuracionbalance`
--
ALTER TABLE `configuracionbalance`
  ADD PRIMARY KEY (`configuracionbalance_id`);

--
-- Indices de la tabla `configuracioncomprobante`
--
ALTER TABLE `configuracioncomprobante`
  ADD PRIMARY KEY (`configuracioncomprobante_id`);

--
-- Indices de la tabla `configuracionmenu`
--
ALTER TABLE `configuracionmenu`
  ADD PRIMARY KEY (`configuracionmenu_id`);

--
-- Indices de la tabla `creditoproveedordetalle`
--
ALTER TABLE `creditoproveedordetalle`
  ADD PRIMARY KEY (`creditoproveedordetalle_id`),
  ADD KEY `cuentacorrienteproveedor_id` (`cuentacorrienteproveedor_id`),
  ADD KEY `tipofactura` (`tipofactura`);

--
-- Indices de la tabla `cuentacorrientecliente`
--
ALTER TABLE `cuentacorrientecliente`
  ADD PRIMARY KEY (`cuentacorrientecliente_id`),
  ADD KEY `tipomovimientocuenta` (`tipomovimientocuenta`),
  ADD KEY `estadomovimientocuenta` (`estadomovimientocuenta`),
  ADD KEY `tipomovimientocuenta_2` (`tipomovimientocuenta`),
  ADD KEY `estadomovimientocuenta_2` (`estadomovimientocuenta`),
  ADD KEY `egreso_id` (`egreso_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `cobrador` (`cobrador`);

--
-- Indices de la tabla `cuentacorrienteproveedor`
--
ALTER TABLE `cuentacorrienteproveedor`
  ADD PRIMARY KEY (`cuentacorrienteproveedor_id`),
  ADD KEY `tipomovimientocuenta` (`tipomovimientocuenta`),
  ADD KEY `estadomovimientocuenta` (`estadomovimientocuenta`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `ingreso_id` (`ingreso_id`),
  ADD KEY `ingresotipopago` (`ingresotipopago`);

--
-- Indices de la tabla `documentotipo`
--
ALTER TABLE `documentotipo`
  ADD PRIMARY KEY (`documentotipo_id`);

--
-- Indices de la tabla `egreso`
--
ALTER TABLE `egreso`
  ADD PRIMARY KEY (`egreso_id`),
  ADD KEY `cliente` (`cliente`),
  ADD KEY `vendedor` (`vendedor`),
  ADD KEY `tipofactura` (`tipofactura`),
  ADD KEY `condicioniva` (`condicioniva`),
  ADD KEY `condicionpago` (`condicionpago`),
  ADD KEY `estadocomision` (`egresocomision`),
  ADD KEY `egresoentrega` (`egresoentrega`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `numero_factura` (`numero_factura`),
  ADD KEY `punto_venta` (`punto_venta`),
  ADD KEY `importe_total` (`importe_total`);

--
-- Indices de la tabla `egresoafip`
--
ALTER TABLE `egresoafip`
  ADD PRIMARY KEY (`egresoafip_id`),
  ADD KEY `punto_venta` (`punto_venta`),
  ADD KEY `numero_factura` (`numero_factura`),
  ADD KEY `tipofactura` (`tipofactura`),
  ADD KEY `egreso_id` (`egreso_id`);

--
-- Indices de la tabla `egresocomision`
--
ALTER TABLE `egresocomision`
  ADD PRIMARY KEY (`egresocomision_id`),
  ADD KEY `estadocomision` (`estadocomision`);

--
-- Indices de la tabla `egresodetalle`
--
ALTER TABLE `egresodetalle`
  ADD PRIMARY KEY (`egresodetalle_id`),
  ADD KEY `egresodetalleestado` (`egresodetalleestado`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `egreso_id` (`egreso_id`);

--
-- Indices de la tabla `egresodetalleestado`
--
ALTER TABLE `egresodetalleestado`
  ADD PRIMARY KEY (`egresodetalleestado_id`);

--
-- Indices de la tabla `egresoentrega`
--
ALTER TABLE `egresoentrega`
  ADD PRIMARY KEY (`egresoentrega_id`),
  ADD KEY `flete` (`flete`),
  ADD KEY `estadoentrega` (`estadoentrega`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`empleado_id`),
  ADD KEY `provincia` (`provincia`),
  ADD KEY `documentotipo` (`documentotipo`);

--
-- Indices de la tabla `entregacliente`
--
ALTER TABLE `entregacliente`
  ADD PRIMARY KEY (`entregacliente_id`);

--
-- Indices de la tabla `entregaclientedetalle`
--
ALTER TABLE `entregaclientedetalle`
  ADD PRIMARY KEY (`entregaclientedetalle_id`),
  ADD KEY `egreso_id` (`egreso_id`),
  ADD KEY `entregacliente_id` (`entregacliente_id`);

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
-- Indices de la tabla `estadopedido`
--
ALTER TABLE `estadopedido`
  ADD PRIMARY KEY (`estadopedido_id`);

--
-- Indices de la tabla `flete`
--
ALTER TABLE `flete`
  ADD PRIMARY KEY (`flete_id`),
  ADD KEY `documentotipo` (`documentotipo`);

--
-- Indices de la tabla `frecuenciaventa`
--
ALTER TABLE `frecuenciaventa`
  ADD PRIMARY KEY (`frecuenciaventa_id`);

--
-- Indices de la tabla `gasto`
--
ALTER TABLE `gasto`
  ADD PRIMARY KEY (`gasto_id`),
  ADD KEY `gastocategoria` (`gastocategoria`);

--
-- Indices de la tabla `gastocategoria`
--
ALTER TABLE `gastocategoria`
  ADD PRIMARY KEY (`gastocategoria_id`);

--
-- Indices de la tabla `gastosubcategoria`
--
ALTER TABLE `gastosubcategoria`
  ADD PRIMARY KEY (`gastosubcategoria_id`);

--
-- Indices de la tabla `hojaruta`
--
ALTER TABLE `hojaruta`
  ADD PRIMARY KEY (`hojaruta_id`),
  ADD KEY `estadoentrega` (`estadoentrega`),
  ADD KEY `flete_id` (`flete_id`),
  ADD KEY `egreso_ids` (`egreso_ids`(255));

--
-- Indices de la tabla `infocontacto`
--
ALTER TABLE `infocontacto`
  ADD PRIMARY KEY (`infocontacto_id`);

--
-- Indices de la tabla `infocontactocliente`
--
ALTER TABLE `infocontactocliente`
  ADD PRIMARY KEY (`infocontactocliente_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `infocontactoflete`
--
ALTER TABLE `infocontactoflete`
  ADD PRIMARY KEY (`infocontactoflete_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `infocontactoproveedor`
--
ALTER TABLE `infocontactoproveedor`
  ADD PRIMARY KEY (`infocontactoproveedor_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `infocontactovendedor`
--
ALTER TABLE `infocontactovendedor`
  ADD PRIMARY KEY (`infocontactovendedor_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD PRIMARY KEY (`ingreso_id`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `condicioniva` (`condicioniva`),
  ADD KEY `condicionpago` (`condicionpago`),
  ADD KEY `tipofactura` (`tipofactura`);

--
-- Indices de la tabla `ingresodetalle`
--
ALTER TABLE `ingresodetalle`
  ADD PRIMARY KEY (`ingresodetalle_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `ingreso_id` (`ingreso_id`);

--
-- Indices de la tabla `ingresotipopago`
--
ALTER TABLE `ingresotipopago`
  ADD PRIMARY KEY (`ingresotipopago_id`);

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `submenu` (`submenu`);

--
-- Indices de la tabla `itemconfiguracionmenu`
--
ALTER TABLE `itemconfiguracionmenu`
  ADD PRIMARY KEY (`itemconfiguracionmenu_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `listaprecio`
--
ALTER TABLE `listaprecio`
  ADD PRIMARY KEY (`listaprecio_id`);

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
  ADD PRIMARY KEY (`notacredito_id`),
  ADD KEY `tipofactura` (`tipofactura`);

--
-- Indices de la tabla `notacreditodetalle`
--
ALTER TABLE `notacreditodetalle`
  ADD PRIMARY KEY (`notacreditodetalle_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `egreso_id` (`egreso_id`),
  ADD KEY `notacredito_id` (`notacredito_id`);

--
-- Indices de la tabla `notacreditoproveedor`
--
ALTER TABLE `notacreditoproveedor`
  ADD PRIMARY KEY (`notacreditoproveedor_id`),
  ADD KEY `ingreso_id` (`ingreso_id`);

--
-- Indices de la tabla `notacreditoproveedordetalle`
--
ALTER TABLE `notacreditoproveedordetalle`
  ADD PRIMARY KEY (`notacreditoproveedordetalle_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `ingreso_id` (`ingreso_id`),
  ADD KEY `notacreditoproveedor_id` (`notacreditoproveedor_id`);

--
-- Indices de la tabla `pedidovendedor`
--
ALTER TABLE `pedidovendedor`
  ADD PRIMARY KEY (`pedidovendedor_id`),
  ADD KEY `estadopedido` (`estadopedido`);

--
-- Indices de la tabla `pedidovendedordetalle`
--
ALTER TABLE `pedidovendedordetalle`
  ADD PRIMARY KEY (`pedidovendedordetalle_id`);

--
-- Indices de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD PRIMARY KEY (`presupuesto_id`),
  ADD KEY `cliente` (`cliente`),
  ADD KEY `vendedor` (`vendedor`);

--
-- Indices de la tabla `presupuestodetalle`
--
ALTER TABLE `presupuestodetalle`
  ADD PRIMARY KEY (`presupuestodetalle_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`producto_id`),
  ADD KEY `productomarca` (`productomarca`),
  ADD KEY `productocategoria` (`productocategoria`),
  ADD KEY `productounidad` (`productounidad`);

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
  ADD PRIMARY KEY (`proveedor_id`),
  ADD KEY `provincia` (`provincia`),
  ADD KEY `documentotipo` (`documentotipo`),
  ADD KEY `condicioniva` (`condicioniva`);

--
-- Indices de la tabla `proveedorproducto`
--
ALTER TABLE `proveedorproducto`
  ADD PRIMARY KEY (`proveedorproducto_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `provincia`
--
ALTER TABLE `provincia`
  ADD PRIMARY KEY (`provincia_id`);

--
-- Indices de la tabla `salario`
--
ALTER TABLE `salario`
  ADD PRIMARY KEY (`salario_id`),
  ADD KEY `empleado` (`empleado`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indices de la tabla `submenu`
--
ALTER TABLE `submenu`
  ADD PRIMARY KEY (`submenu_id`),
  ADD KEY `submenu` (`menu`);

--
-- Indices de la tabla `submenuconfiguracionmenu`
--
ALTER TABLE `submenuconfiguracionmenu`
  ADD PRIMARY KEY (`submenuconfiguracionmenu_id`),
  ADD KEY `compuesto` (`compuesto`),
  ADD KEY `compositor` (`compositor`);

--
-- Indices de la tabla `tipofactura`
--
ALTER TABLE `tipofactura`
  ADD PRIMARY KEY (`tipofactura_id`),
  ADD KEY `nomenclatura` (`nomenclatura`),
  ADD KEY `afip_id` (`afip_id`);

--
-- Indices de la tabla `tipomovimientocuenta`
--
ALTER TABLE `tipomovimientocuenta`
  ADD PRIMARY KEY (`tipomovimientocuenta_id`);

--
-- Indices de la tabla `transferenciaproveedordetalle`
--
ALTER TABLE `transferenciaproveedordetalle`
  ADD PRIMARY KEY (`transferenciaproveedordetalle_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD KEY `usuariodetalle` (`usuariodetalle`),
  ADD KEY `configuracionmenu` (`configuracionmenu`);

--
-- Indices de la tabla `usuariodetalle`
--
ALTER TABLE `usuariodetalle`
  ADD PRIMARY KEY (`usuariodetalle_id`);

--
-- Indices de la tabla `usuariovendedor`
--
ALTER TABLE `usuariovendedor`
  ADD PRIMARY KEY (`usuariovendedor_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Indices de la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD PRIMARY KEY (`vehiculo_id`),
  ADD KEY `vehiculomodelo` (`vehiculomodelo`),
  ADD KEY `combustible` (`combustible`);

--
-- Indices de la tabla `vehiculocombustible`
--
ALTER TABLE `vehiculocombustible`
  ADD PRIMARY KEY (`vehiculocombustible_id`),
  ADD KEY `vehiculo` (`vehiculo`);

--
-- Indices de la tabla `vehiculomarca`
--
ALTER TABLE `vehiculomarca`
  ADD PRIMARY KEY (`vehiculomarca_id`);

--
-- Indices de la tabla `vehiculomodelo`
--
ALTER TABLE `vehiculomodelo`
  ADD PRIMARY KEY (`vehiculomodelo_id`),
  ADD KEY `vehiculomarca` (`vehiculomarca`);

--
-- Indices de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  ADD PRIMARY KEY (`vendedor_id`),
  ADD KEY `provincia` (`provincia`),
  ADD KEY `documentotipo` (`documentotipo`),
  ADD KEY `frecuenciaventa` (`frecuenciaventa`),
  ADD KEY `apellido` (`apellido`),
  ADD KEY `nombre` (`nombre`),
  ADD KEY `comision` (`comision`);

--
-- Indices de la tabla `version`
--
ALTER TABLE `version`
  ADD PRIMARY KEY (`version_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `backup`
--
ALTER TABLE `backup`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cajadiaria`
--
ALTER TABLE `cajadiaria`
  MODIFY `cajadiaria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=459;
--
-- AUTO_INCREMENT de la tabla `categoriacliente`
--
ALTER TABLE `categoriacliente`
  MODIFY `categoriacliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `chequeproveedordetalle`
--
ALTER TABLE `chequeproveedordetalle`
  MODIFY `chequeproveedordetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2067;
--
-- AUTO_INCREMENT de la tabla `cobrador`
--
ALTER TABLE `cobrador`
  MODIFY `cobrador_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT de la tabla `combustible`
--
ALTER TABLE `combustible`
  MODIFY `combustible_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `condicionfiscal`
--
ALTER TABLE `condicionfiscal`
  MODIFY `condicionfiscal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `condicioniva`
--
ALTER TABLE `condicioniva`
  MODIFY `condicioniva_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `condicionpago`
--
ALTER TABLE `condicionpago`
  MODIFY `condicionpago_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `configuracion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `configuracionbalance`
--
ALTER TABLE `configuracionbalance`
  MODIFY `configuracionbalance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `configuracioncomprobante`
--
ALTER TABLE `configuracioncomprobante`
  MODIFY `configuracioncomprobante_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `configuracionmenu`
--
ALTER TABLE `configuracionmenu`
  MODIFY `configuracionmenu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `creditoproveedordetalle`
--
ALTER TABLE `creditoproveedordetalle`
  MODIFY `creditoproveedordetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
--
-- AUTO_INCREMENT de la tabla `cuentacorrientecliente`
--
ALTER TABLE `cuentacorrientecliente`
  MODIFY `cuentacorrientecliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160713;
--
-- AUTO_INCREMENT de la tabla `cuentacorrienteproveedor`
--
ALTER TABLE `cuentacorrienteproveedor`
  MODIFY `cuentacorrienteproveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2374;
--
-- AUTO_INCREMENT de la tabla `documentotipo`
--
ALTER TABLE `documentotipo`
  MODIFY `documentotipo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `egreso`
--
ALTER TABLE `egreso`
  MODIFY `egreso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67472;
--
-- AUTO_INCREMENT de la tabla `egresoafip`
--
ALTER TABLE `egresoafip`
  MODIFY `egresoafip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30319;
--
-- AUTO_INCREMENT de la tabla `egresocomision`
--
ALTER TABLE `egresocomision`
  MODIFY `egresocomision_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67468;
--
-- AUTO_INCREMENT de la tabla `egresodetalle`
--
ALTER TABLE `egresodetalle`
  MODIFY `egresodetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237520;
--
-- AUTO_INCREMENT de la tabla `egresodetalleestado`
--
ALTER TABLE `egresodetalleestado`
  MODIFY `egresodetalleestado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `egresoentrega`
--
ALTER TABLE `egresoentrega`
  MODIFY `egresoentrega_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67474;
--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `empleado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT de la tabla `entregacliente`
--
ALTER TABLE `entregacliente`
  MODIFY `entregacliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22049;
--
-- AUTO_INCREMENT de la tabla `entregaclientedetalle`
--
ALTER TABLE `entregaclientedetalle`
  MODIFY `entregaclientedetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22089;
--
-- AUTO_INCREMENT de la tabla `estadocomision`
--
ALTER TABLE `estadocomision`
  MODIFY `estadocomision_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `estadoentrega`
--
ALTER TABLE `estadoentrega`
  MODIFY `estadoentrega_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `estadomovimientocuenta`
--
ALTER TABLE `estadomovimientocuenta`
  MODIFY `estadomovimientocuenta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `estadopedido`
--
ALTER TABLE `estadopedido`
  MODIFY `estadopedido_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `flete`
--
ALTER TABLE `flete`
  MODIFY `flete_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `frecuenciaventa`
--
ALTER TABLE `frecuenciaventa`
  MODIFY `frecuenciaventa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `gasto`
--
ALTER TABLE `gasto`
  MODIFY `gasto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1892;
--
-- AUTO_INCREMENT de la tabla `gastocategoria`
--
ALTER TABLE `gastocategoria`
  MODIFY `gastocategoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `gastosubcategoria`
--
ALTER TABLE `gastosubcategoria`
  MODIFY `gastosubcategoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `hojaruta`
--
ALTER TABLE `hojaruta`
  MODIFY `hojaruta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1870;
--
-- AUTO_INCREMENT de la tabla `infocontacto`
--
ALTER TABLE `infocontacto`
  MODIFY `infocontacto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6397;
--
-- AUTO_INCREMENT de la tabla `infocontactocliente`
--
ALTER TABLE `infocontactocliente`
  MODIFY `infocontactocliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6226;
--
-- AUTO_INCREMENT de la tabla `infocontactoflete`
--
ALTER TABLE `infocontactoflete`
  MODIFY `infocontactoflete_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT de la tabla `infocontactoproveedor`
--
ALTER TABLE `infocontactoproveedor`
  MODIFY `infocontactoproveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;
--
-- AUTO_INCREMENT de la tabla `infocontactovendedor`
--
ALTER TABLE `infocontactovendedor`
  MODIFY `infocontactovendedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT de la tabla `ingreso`
--
ALTER TABLE `ingreso`
  MODIFY `ingreso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1076;
--
-- AUTO_INCREMENT de la tabla `ingresodetalle`
--
ALTER TABLE `ingresodetalle`
  MODIFY `ingresodetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7510;
--
-- AUTO_INCREMENT de la tabla `ingresotipopago`
--
ALTER TABLE `ingresotipopago`
  MODIFY `ingresotipopago_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `itemconfiguracionmenu`
--
ALTER TABLE `itemconfiguracionmenu`
  MODIFY `itemconfiguracionmenu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;
--
-- AUTO_INCREMENT de la tabla `listaprecio`
--
ALTER TABLE `listaprecio`
  MODIFY `listaprecio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `localidad`
--
ALTER TABLE `localidad`
  MODIFY `localidad_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT de la tabla `movimientotipo`
--
ALTER TABLE `movimientotipo`
  MODIFY `movimientotipo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `notacredito`
--
ALTER TABLE `notacredito`
  MODIFY `notacredito_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5917;
--
-- AUTO_INCREMENT de la tabla `notacreditodetalle`
--
ALTER TABLE `notacreditodetalle`
  MODIFY `notacreditodetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20585;
--
-- AUTO_INCREMENT de la tabla `notacreditoproveedor`
--
ALTER TABLE `notacreditoproveedor`
  MODIFY `notacreditoproveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT de la tabla `notacreditoproveedordetalle`
--
ALTER TABLE `notacreditoproveedordetalle`
  MODIFY `notacreditoproveedordetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=377;
--
-- AUTO_INCREMENT de la tabla `pedidovendedor`
--
ALTER TABLE `pedidovendedor`
  MODIFY `pedidovendedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37017;
--
-- AUTO_INCREMENT de la tabla `pedidovendedordetalle`
--
ALTER TABLE `pedidovendedordetalle`
  MODIFY `pedidovendedordetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156339;
--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `presupuesto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `presupuestodetalle`
--
ALTER TABLE `presupuestodetalle`
  MODIFY `presupuestodetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=584;
--
-- AUTO_INCREMENT de la tabla `productocategoria`
--
ALTER TABLE `productocategoria`
  MODIFY `productocategoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `productodetalle`
--
ALTER TABLE `productodetalle`
  MODIFY `productodetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1799;
--
-- AUTO_INCREMENT de la tabla `productomarca`
--
ALTER TABLE `productomarca`
  MODIFY `productomarca_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT de la tabla `productounidad`
--
ALTER TABLE `productounidad`
  MODIFY `productounidad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT de la tabla `proveedorproducto`
--
ALTER TABLE `proveedorproducto`
  MODIFY `proveedorproducto_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `provincia`
--
ALTER TABLE `provincia`
  MODIFY `provincia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `salario`
--
ALTER TABLE `salario`
  MODIFY `salario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;
--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260922;
--
-- AUTO_INCREMENT de la tabla `submenu`
--
ALTER TABLE `submenu`
  MODIFY `submenu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT de la tabla `submenuconfiguracionmenu`
--
ALTER TABLE `submenuconfiguracionmenu`
  MODIFY `submenuconfiguracionmenu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10979;
--
-- AUTO_INCREMENT de la tabla `tipofactura`
--
ALTER TABLE `tipofactura`
  MODIFY `tipofactura_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `tipomovimientocuenta`
--
ALTER TABLE `tipomovimientocuenta`
  MODIFY `tipomovimientocuenta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `transferenciaproveedordetalle`
--
ALTER TABLE `transferenciaproveedordetalle`
  MODIFY `transferenciaproveedordetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT de la tabla `usuariodetalle`
--
ALTER TABLE `usuariodetalle`
  MODIFY `usuariodetalle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT de la tabla `usuariovendedor`
--
ALTER TABLE `usuariovendedor`
  MODIFY `usuariovendedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT de la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  MODIFY `vehiculo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `vehiculocombustible`
--
ALTER TABLE `vehiculocombustible`
  MODIFY `vehiculocombustible_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;
--
-- AUTO_INCREMENT de la tabla `vehiculomarca`
--
ALTER TABLE `vehiculomarca`
  MODIFY `vehiculomarca_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `vehiculomodelo`
--
ALTER TABLE `vehiculomodelo`
  MODIFY `vehiculomodelo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  MODIFY `vendedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `version`
--
ALTER TABLE `version`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
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
-- Filtros para la tabla `creditoproveedordetalle`
--
ALTER TABLE `creditoproveedordetalle`
  ADD CONSTRAINT `creditoproveedordetalle_ibfk_1` FOREIGN KEY (`tipofactura`) REFERENCES `tipofactura` (`tipofactura_id`) ON UPDATE SET NULL;

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
  ADD CONSTRAINT `cuentacorrienteproveedor_ibfk_2` FOREIGN KEY (`estadomovimientocuenta`) REFERENCES `estadomovimientocuenta` (`estadomovimientocuenta_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuentacorrienteproveedor_ibfk_3` FOREIGN KEY (`ingresotipopago`) REFERENCES `ingresotipopago` (`ingresotipopago_id`) ON DELETE SET NULL;

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
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `empleado_ibfk_1` FOREIGN KEY (`provincia`) REFERENCES `provincia` (`provincia_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empleado_ibfk_2` FOREIGN KEY (`documentotipo`) REFERENCES `documentotipo` (`documentotipo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `flete`
--
ALTER TABLE `flete`
  ADD CONSTRAINT `flete_ibfk_1` FOREIGN KEY (`documentotipo`) REFERENCES `documentotipo` (`documentotipo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `gasto`
--
ALTER TABLE `gasto`
  ADD CONSTRAINT `gasto_ibfk_1` FOREIGN KEY (`gastocategoria`) REFERENCES `gastocategoria` (`gastocategoria_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `ingreso_ibfk_3` FOREIGN KEY (`condicionpago`) REFERENCES `condicionpago` (`condicionpago_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingreso_ibfk_4` FOREIGN KEY (`tipofactura`) REFERENCES `tipofactura` (`tipofactura_id`) ON DELETE SET NULL;

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
-- Filtros para la tabla `pedidovendedor`
--
ALTER TABLE `pedidovendedor`
  ADD CONSTRAINT `pedidovendedor_ibfk_1` FOREIGN KEY (`estadopedido`) REFERENCES `estadopedido` (`estadopedido_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD CONSTRAINT `presupuesto_ibfk_1` FOREIGN KEY (`cliente`) REFERENCES `cliente` (`cliente_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presupuesto_ibfk_2` FOREIGN KEY (`vendedor`) REFERENCES `vendedor` (`vendedor_id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `salario`
--
ALTER TABLE `salario`
  ADD CONSTRAINT `salario_ibfk_1` FOREIGN KEY (`empleado`) REFERENCES `empleado` (`empleado_id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD CONSTRAINT `vehiculo_ibfk_1` FOREIGN KEY (`vehiculomodelo`) REFERENCES `vehiculomodelo` (`vehiculomodelo_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehiculo_ibfk_2` FOREIGN KEY (`combustible`) REFERENCES `combustible` (`combustible_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `vehiculocombustible`
--
ALTER TABLE `vehiculocombustible`
  ADD CONSTRAINT `vehiculocombustible_ibfk_1` FOREIGN KEY (`vehiculo`) REFERENCES `vehiculo` (`vehiculo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vehiculomodelo`
--
ALTER TABLE `vehiculomodelo`
  ADD CONSTRAINT `vehiculomodelo_ibfk_1` FOREIGN KEY (`vehiculomarca`) REFERENCES `vehiculomarca` (`vehiculomarca_id`) ON DELETE CASCADE;

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
