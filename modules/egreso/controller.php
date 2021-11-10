<?php
require_once "modules/egreso/model.php";
require_once "modules/egreso/view.php";
require_once "modules/producto/model.php";
require_once "modules/cliente/model.php";
require_once "modules/vendedor/model.php";
require_once "modules/flete/model.php";
require_once "modules/tipofactura/model.php";
require_once "modules/condicionpago/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/egresoafip/model.php";
require_once "modules/egresocomision/model.php";
require_once "modules/egresoentrega/model.php";
require_once "modules/egresodetalle/model.php";
require_once "modules/notacredito/model.php";
require_once "modules/notacreditodetalle/model.php";
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/stock/model.php";
require_once "modules/hojaruta/model.php";
require_once "modules/configuracion/model.php";
require_once "modules/configuracioncomprobante/model.php";
require_once "modules/usuariodetalle/model.php";
require_once "tools/facturaAFIPTool.php";
require_once "tools/hojaRutaPDFTool.php";


class EgresoController {

	function __construct() {
		$this->model = new Egreso();
		$this->view = new EgresoView();
	}

	function listar($arg) {
    	SessionHandler()->check_session();
    	$periodo_actual = date('Ym');
    	$select = "e.egreso_id AS EGRESO_ID, CONCAT(date_format(e.fecha, '%d/%m/%Y'), ' ', LEFT(e.hora,5)) AS FECHA, UPPER(cl.razon_social) AS CLIENTE, ci.denominacion AS CONDIV, CONCAT(LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) AS BK, e.subtotal AS SUBTOTAL, ese.denominacion AS ENTREGA, e.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR, UPPER(cp.denominacion) AS CP, CASE ee.estadoentrega WHEN 1 THEN 'inline-block' WHEN 2 THEN 'inline-block' WHEN 3 THEN 'none' WHEN 4 THEN 'none' END AS DSP_BTN_ENT, CASE e.emitido WHEN 1 THEN 'none' ELSE (CASE WHEN eafip.egresoafip_id IS NULL THEN 'inline-block' ELSE 'none' END) END AS DSP_BTN_EDIT, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "date_format(e.fecha, '%Y%m') = {$periodo_actual} ORDER BY e.fecha DESC";
		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e";
		$where = "e.condicionpago = 2";
		$sum_contado = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado = (is_array($sum_contado)) ? $sum_contado[0]['CONTADO'] : 0;

		$select = "ROUND(SUM(ccc.importe),2) AS CUENTACORRIENTE";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.tipomovimientocuenta = 2";
		$sum_cuntacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$sum_cuntacorriente = (is_array($sum_cuntacorriente)) ? $sum_cuntacorriente[0]['CUENTACORRIENTE'] : 0;
		$total_facturado = $sum_contado + $sum_cuntacorriente;

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$cuentacorriente_total = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);
		if (is_array($cuentacorriente_total)) {
			$cuentacorriente_deuda = $cuentacorriente_total[0]['TDEUDA'];
			$cuentacorriente_deuda = (is_null($cuentacorriente_deuda)) ? 0 : $cuentacorriente_deuda;

			$cuentacorriente_ingreso = $cuentacorriente_total[0]['TINGRESO'];
			$cuentacorriente_ingreso = (is_null($cuentacorriente_ingreso)) ? 0 : $cuentacorriente_ingreso;

			$deuda_cuentacorrientecliente = $cuentacorriente_deuda - $cuentacorriente_ingreso;
		} else {
			$deuda_cuentacorrientecliente = 0;
		}

		$array_totales = array('{total_facturado}'=>$total_facturado,
							   '{deuda_cuentacorrientecliente}'=>$deuda_cuentacorrientecliente);

		switch ($arg) {
			case 1:
				$array_msj = array('{mensaje}'=>'[INFO] Se ha registrado la venta',
								   '{class}'=>'info',
								   '{display}'=>'block');
				break;
			case 2:
				$array_msj = array('{mensaje}'=>'[INFO] Se ha editado un registro de venta',
								   '{class}'=>'info',
								   '{display}'=>'block');
				break;
			case 3:
				$array_msj = array('{mensaje}'=>'[ERROR] No se ha podido registrar la venta. No posee conexión para facturar en AFIP.',
								   '{class}'=>'danger',
								   '{display}'=>'block');
				break;
			case 4:
				$array_msj = array('{mensaje}'=>'[ERROR] No se ha podido registrar la venta. El N° de Documento del cliente seleccionado no corresponde al tipo de facturación definido en su perfil.',
								   '{class}'=>'danger',
								   '{display}'=>'block');
				break;
			default:
				$array_msj = array('{mensaje}'=>'',
								   '{class}'=>'info',
								   '{display}'=>'none');
				break;
		}

		$this->view->listar($egreso_collection, $array_msj, $array_totales);
	}

	function egresar() {
    	SessionHandler()->check_session();
		$condicionpago_collection = Collector()->get('CondicionPago');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$tipofactura_collection = Collector()->get('TipoFactura');

		$array_ids = array(1,2,3);
		foreach ($tipofactura_collection as $clave=>$valor) {
			if (!in_array($valor->tipofactura_id, $array_ids)) unset($tipofactura_collection[$clave]);
		}

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "p.oculto = 0 AND p.producto_id != 344";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select, $groupby);
		foreach ($producto_collection as $clave=>$valor) {
			$producto_id = $valor['PRODUCTO_ID'];
			$select = "MAX(s.stock_id) AS STOCK_ID";
			$from = "stock s";
			$where = "s.producto_id = {$producto_id}";
			$groupby = "s.producto_id";
			$stock_id = CollectorCondition()->get('Stock', $where, 4, $from, $select, $groupby);
			$stock_id = (is_array($stock_id) AND !empty($stock_id)) ? $stock_id[0]['STOCK_ID'] : 0;

			if ($stock_id == 0) {
				$producto_collection[$clave]['STOCK'] = 0;
				$producto_collection[$clave]['CLASTO'] = 'danger';
			} else {
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$class_stm = ($sm->cantidad_actual > 0) ? 'success' : 'danger';
				
				$producto_collection[$clave]['STOCK'] = $sm->cantidad_actual;
				$producto_collection[$clave]['CLASTO'] = $class_stm;
			}
		}

		$select = "c.cliente_id AS CLIENTE_ID, LPAD(c.cliente_id, 5, 0) AS CODCLI, CONCAT(c.razon_social, '(', c.nombre_fantasia, ')') AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO";
		$from = "cliente c INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id";
		$where = "c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);

		$select = "v.vendedor_id AS VENDEDOR_ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION, CONCAT(dt.denominacion, ' ', v.documento) AS DOCUMENTO";
		$from = "vendedor v INNER JOIN documentotipo dt ON v.documentotipo = dt.documentotipo_id";
		$vendedor_collection = CollectorCondition()->get('Vendedor', NULL, 4, $from, $select);

		$select = "(MAX(e.numero_factura) + 1 ) AS SIGUIENTE_NUMERO ";
		$from = "egreso e";
		$where = "e.tipofactura = 2";
		$groupby = "e.tipofactura";
		$siguiente_remito = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$siguiente_remito = (!is_array($siguiente_remito)) ? 1 : $siguiente_remito[0]['SIGUIENTE_NUMERO'];

		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();
		$facturacion_rapida = $ccm->facturacion_rapida;

		$array_remito = array("{punto_venta}"=>str_pad($cm->punto_venta, 4, '0', STR_PAD_LEFT),
							  "{numero_remito}"=>str_pad($siguiente_remito, 8, '0', STR_PAD_LEFT));

		if ($facturacion_rapida == 1) {
			$this->view->cb_egresar($producto_collection, $cliente_collection, $vendedor_collection, $tipofactura_collection,
								 	$condicionpago_collection, $condicioniva_collection, $array_remito, $ccm);
		} else {
			$this->view->egresar($producto_collection, $cliente_collection, $vendedor_collection, $tipofactura_collection,
								 $condicionpago_collection, $condicioniva_collection, $array_remito);
		}
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		require_once 'tools/facturaPDFTool.php';
		require_once 'modules/configuracion/model.php';
		require_once "core/helpers/file.php";

		$egreso_id = $arg;
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$this->model->egreso_id = $egreso_id;
		$this->model->get();
		$condicionpago_id = $this->model->condicionpago->condicionpago_id;
		
		$cliente_documentotipo = $this->model->cliente->documentotipo->denominacion;
		$this->model->cliente_documentotipo = $cliente_documentotipo;
		$infocontacto_collection = $this->model->cliente->infocontacto_collection;

		if (is_array($infocontacto_collection) AND !empty($infocontacto_collection)) {
			foreach ($infocontacto_collection as $infocontacto) if ($infocontacto->denominacion == 'Teléfono') $telefono = $infocontacto->valor;

			if (!empty($telefono)) {
				$this->model->cliente->telefono = 'Tel: ' . $telefono;
			}else {
				$this->model->cliente->telefono = '';
			}
		} else {
			$this->model->cliente->telefono = '';
		}

		if ($condicionpago_id == 1) {
			if (!empty($this->model->cliente->entregaminima)) {
				$porcentaje_entregaminima = $this->model->cliente->entregaminima;
				$importe_total = $this->model->importe_total;
				$monto_entrega = ($porcentaje_entregaminima * $importe_total)/100;
				$monto_entrega = round($monto_entrega, 0);
				$this->model->cliente->monto_entrega = 'Entrega Minima: $' . $monto_entrega;
			} else {
				$this->model->cliente->monto_entrega = '';
			}
		} else {
			$this->model->cliente->monto_entrega = '';
		}

		$this->model->cliente->cliente_frecuencia = $this->model->cliente->frecuenciaventa->denominacion." (".$this->model->cliente->frecuenciaventa->dia_1."-".$this->model->cliente->frecuenciaventa->dia_2.")";

		$select = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.valor_descuento AS VD, ed.costo_producto AS COSTO, ROUND(ed.importe, 2) AS IMPORTE, ed.iva AS IVA, ed.neto_producto AS NETPRO";
		$from = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN
				 productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

		$select = "ccc.referencia AS REF, ccc.importe AS IMP, estadomovimientocuenta AS EST";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id} AND ccc.tipomovimientocuenta = 2 ORDER BY ccc.cuentacorrientecliente_id DESC";
		$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$cuentacorrientecliente_collection = (is_array($cuentacorrientecliente_collection)) ? $cuentacorrientecliente_collection : array();

		$select = "nc.notacredito_id";
		$from = "notacredito nc";
		$where = "nc.egreso_id = {$egreso_id}";
		$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$notacredito_id = (is_array($notacredito) AND !empty($notacredito)) ? $notacredito[0]['notacredito_id'] : 0;

		$select_egresoafip = "eafip.punto_venta AS PUNTO_VENTA, eafip.numero_factura AS NUMERO_FACTURA, tf.nomenclatura AS TIPOFACTURA, eafip.cae AS CAE, eafip.vencimiento AS FVENCIMIENTO, eafip.fecha AS FECHA, tf.tipofactura_id AS TF_ID";
		$from_egresoafip = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
		$where_egresoafip = "eafip.egreso_id = {$egreso_id}";
		$egresoafip = CollectorCondition()->get('EgresoAfip', $where_egresoafip, 4, $from_egresoafip, $select_egresoafip);

		$telefono_vendedor = '';
		$infocontacto_vendedor = $this->model->vendedor->infocontacto_collection;
		foreach ($infocontacto_vendedor as $c=>$v) {
			if($v->denominacion == 'Celular') $telefono_vendedor = 'Cel: ' . $v->valor;
		}

		$telefono_flete = '';
		$infocontacto_flete = $this->model->cliente->flete->infocontacto_collection;
		foreach ($infocontacto_flete as $c=>$v) {
			if($v->denominacion == 'Celular') $telefono_flete = 'Cel: ' . $v->valor;
		}

		$vendedor = $this->model->vendedor->apellido . ' ' . $this->model->vendedor->nombre . ' (' . $telefono_vendedor .')';
		$flete = $this->model->cliente->flete->denominacion  . ' (' . $telefono_flete .')';
		$facturaPDFHelper = new FacturaPDF();
		if (!is_array($egresoafip)) {
			$egresoafip = array();
			$this->model->cae = 0;
			$this->model->fecha_vencimiento = 0;
			$tipofactura_id = $this->model->tipofactura->tipofactura_id;
			$plantilla_tipofactura = $this->model->tipofactura->plantilla_impresion;
		} else {
			$egresoafip = $egresoafip[0];
			$tipofactura_id = $egresoafip['TF_ID'];
			$tfm = new TipoFactura();
			$tfm->tipofactura_id = $tipofactura_id;
			$tfm->get();

			$this->model->punto_venta = $egresoafip['PUNTO_VENTA'];
			$this->model->numero_factura = $egresoafip['NUMERO_FACTURA'];
			$this->model->fecha = $egresoafip['FECHA'];
			$this->model->cae = $egresoafip['CAE'];
			$this->model->fecha_vencimiento = $egresoafip['FVENCIMIENTO'];
			unset($this->model->tipofactura);
			$this->model->tipofactura = $tfm;
			$tipofactura_id = $this->model->tipofactura->tipofactura_id;
		}

		switch ($tipofactura_id) {
			case 1:
				$facturaPDFHelper->facturaA($egresodetalle_collection, $cm, $this->model, $vendedor, $flete);
				break;
			case 2:
				$facturaPDFHelper->remitoR($egresodetalle_collection, $cm, $this->model, $vendedor, $flete);
				break;
			case 3:
				$facturaPDFHelper->facturaB($egresodetalle_collection, $cm, $this->model, $vendedor, $flete);
				break;
		}

		$this->model = new Egreso();
		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		if (!empty($egresoafip)) {
			$this->model->punto_venta = $egresoafip['PUNTO_VENTA'];
			$this->model->numero_factura = $egresoafip['NUMERO_FACTURA'];
		}

		$this->view->consultar($egresodetalle_collection, $cuentacorrientecliente_collection, $this->model, $egresoafip, $notacredito_id);
	}

	function configurar($arg) {
    	SessionHandler()->check_session();
		require_once 'tools/facturaPDFTool.php';
		require_once 'modules/configuracion/model.php';
		require_once "core/helpers/file.php";

		$egreso_id = $arg;
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		$select = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.valor_descuento AS VD, ed.costo_producto AS COSTO, ROUND(ed.importe, 2) AS IMPORTE, ed.iva AS IVA";
		$from = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

		$select = "ccc.referencia AS REF, ccc.importe AS IMP, estadomovimientocuenta AS EST";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id} AND ccc.tipomovimientocuenta = 2 ORDER BY ccc.cuentacorrientecliente_id DESC";
		$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$cuentacorrientecliente_collection = (is_array($cuentacorrientecliente_collection)) ? $cuentacorrientecliente_collection : array();

		$select = "nc.notacredito_id";
		$from = "notacredito nc";
		$where = "nc.egreso_id = {$egreso_id}";
		$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		if (is_array($notacredito) AND !empty($notacredito)) {
			$notacredito_id = $notacredito[0]['notacredito_id'];
		} else {
			$notacredito_id = 0;
		}

		$select_egresoafip = "eafip.punto_venta AS PUNTO_VENTA, eafip.numero_factura AS NUMERO_FACTURA, tf.nomenclatura AS TIPOFACTURA, eafip.cae AS CAE, eafip.vencimiento AS FVENCIMIENTO, eafip.fecha AS FECHA, tf.tipofactura_id AS TF_ID";
		$from_egresoafip = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
		$where_egresoafip = "eafip.egreso_id = {$egreso_id}";
		$egresoafip = CollectorCondition()->get('EgresoAfip', $where_egresoafip, 4, $from_egresoafip, $select_egresoafip);

		$this->model = new Egreso();
		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		if (!empty($egresoafip)) {
			$this->model->punto_venta = $egresoafip[0]['PUNTO_VENTA'];
			$this->model->numero_factura = $egresoafip[0]['NUMERO_FACTURA'];
		}

		$select = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION, CONCAT(dt.denominacion, ' ', v.documento) AS DOCUMENTO";
		$from = "vendedor v INNER JOIN documentotipo dt ON v.documentotipo = dt.documentotipo_id";
		$vendedor_collection = CollectorCondition()->get('Vendedor', NULL, 4, $from, $select);
		$tipofactura_collection = Collector()->get('TipoFactura');
		$condicionpago_collection = Collector()->get('CondicionPago');
		$array_ids = array(1,2,3);
		foreach ($tipofactura_collection as $clave=>$valor) {
			if (!in_array($valor->tipofactura_id, $array_ids)) unset($tipofactura_collection[$clave]);
		}

		$this->view->configurar($egresodetalle_collection, $cuentacorrientecliente_collection, $this->model, $egresoafip, $notacredito_id, $vendedor_collection, $tipofactura_collection, $condicionpago_collection);
	}

	function reconfigurar() {
		SessionHandler()->check_session();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$this->model->egreso_id = $egreso_id;
		$this->model->get();
		$fecha_original = $this->model->fecha;
		$hora_original = $this->model->hora;
		$tipofactura_original = $this->model->tipofactura;
		$vendedor_original = $this->model->vendedor;

		$tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$condicionpago = filter_input(INPUT_POST, 'condicionpago');

		$bandera_tipofactura = filter_input(INPUT_POST, 'bandera_tipofactura');
		if ($bandera_tipofactura == 1) {
			if ($tipofactura == 1 OR $tipofactura == 3) {
				$this->model = new Egreso();
				$this->model->egreso_id = $egreso_id;
				$this->model->get();
				$this->model->tipofactura = $tipofactura;
				$this->model->fecha = date('Y-m-d');
				$this->model->hora = date('H:i:s');
				$this->model->save();
				$flag_error = 0;
				try {
				    $this->facturar_afip_argumento($egreso_id);
				} catch (Exception $e) {
					switch ($e->getCode()) {
						case 4:
							$flag_error = 3;
							break;
						case 10015:
							$flag_error = 4;
							break;
					}
				}
			}

			if ($flag_error != 0) {
				$this->model = new Egreso();
				$this->model->egreso_id = $egreso_id;
				$this->model->get();

				$this->model->tipofactura = $tipofactura_original;
				$this->model->fecha = $fecha_original;
				$this->model->hora = $hora_original;
				$this->model->save();
				header("Location: " . URL_APP . "/egreso/listar/{$flag_error}");
			}
		}

		$bandera_vendedor = filter_input(INPUT_POST, 'bandera_vendedor');
		if ($bandera_vendedor == 1) {
			$this->model = new Egreso();
			$this->model->egreso_id = $egreso_id;
			$this->model->get();
			$this->model->vendedor = filter_input(INPUT_POST, 'vendedor');
			$this->model->save();
		}

		$bandera_condicionpago = filter_input(INPUT_POST, 'bandera_condicionpago');
		if ($bandera_condicionpago == 1) {
			$this->model = new Egreso();
			$this->model->egreso_id = $egreso_id;
			$this->model->get();

			$cliente_id = $this->model->cliente->cliente_id;
			$importe_total = $this->model->importe_total;
			$punto_venta = $this->model->punto_venta;
			$numero_factura = $this->model->numero_factura;
			$fecha = filter_input(INPUT_POST, 'fecha');
			$hora = date('H:i:s');
			$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
			$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);

			$condicionpago = filter_input(INPUT_POST, 'condicionpago');
			if ($condicionpago == 1) {
				$cccm = new CuentaCorrienteCliente();
				$cccm->fecha = date('Y-m-d');
				$cccm->hora = date('H:i:s');
				$cccm->referencia = "Comprobante venta {$comprobante}";
				$cccm->importe = $importe_total;
				$cccm->cliente_id = $cliente_id;
				$cccm->egreso_id = $egreso_id;
				$cccm->tipomovimientocuenta = 1;
				$cccm->estadomovimientocuenta = 1;
				$cccm->save();
				$cuentacorrientecliente_id = $cccm->cuentacorrientecliente_id;
			} else {
				$select = "ccc.cuentacorrientecliente_id AS ID";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$egreso_id}";
				$cuentacorrientecliente_ids = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

				if (is_array($cuentacorrientecliente_ids) AND !empty($cuentacorrientecliente_ids)) {
					foreach ($cuentacorrientecliente_ids as $clave=>$valor) {
						$temp_cuentacorrientecliente_id = $valor['ID'];
						$cccm = new CuentaCorrienteCliente();
						$cccm->cuentacorrientecliente_id = $temp_cuentacorrientecliente_id;
						$cccm->delete();
					}
				}
			}

			$this->model->condicionpago = $condicionpago;
			$this->model->save();
		}

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}

	function descarga_notacredito($arg) {
    	SessionHandler()->check_session();
		require_once 'tools/facturaPDFTool.php';
		require_once 'modules/configuracion/model.php';

		$notacredito_id = $arg;
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$ncm = new NotaCredito();
		$ncm->notacredito_id = $notacredito_id;
		$ncm->get();
		$plantilla_tipofactura = $this->model->tipofactura->plantilla_impresion;
		$this->model->egreso_id = $ncm->egreso_id;
		$this->model->get();

		$select_notascredito = "ncd.codigo_producto AS CODIGO, ncd.descripcion_producto AS DESCRIPCION, ncd.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ncd.descuento AS DESCUENTO, ncd.valor_descuento AS VD, ncd.costo_producto AS COSTO, ROUND((ncd.costo_producto * ncd.cantidad),2) AS IMPORTE, ncd.iva AS IVA";
		$from_notascredito = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_notascredito = "ncd.notacredito_id = {$notacredito_id}";
		$notascreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where_notascredito, 4, $from_notascredito, $select_notascredito);

		$facturaPDFHelper = new FacturaPDF();
		$facturaPDFHelper->descarga_notacredito($notascreditodetalle_collection, $cm, $this->model, $ncm);
	}

	function editar($arg) {
    	SessionHandler()->check_session();

		$this->model->egreso_id = $arg;
		$this->model->get();

		$condicionpago_collection = Collector()->get('CondicionPago');
		$condicioniva_collection = Collector()->get('CondicionIVA');

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO, p.stock_minimo AS STMINIMO, p.stock_ideal AS STIDEAL, p.costo as COSTO, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA, (((p.costo * p.porcentaje_ganancia)/100)+p.costo) AS VENTA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id LEFT JOIN productodetalle pd ON p.producto_id = pd.producto_id LEFT JOIN proveedor prv ON pd.proveedor_id = prv.proveedor_id";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', NULL, 4, $from, $select, $groupby);

		$select = "c.cliente_id AS CLIENTE_ID, LPAD(c.cliente_id, 5, 0) AS CODCLI, CONCAT(c.razon_social, '(', c.nombre_fantasia, ')') AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO";
		$from = "cliente c INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id";
		$cliente_collection = CollectorCondition()->get('Cliente', NULL, 4, $from, $select);

		$select = "v.vendedor_id AS VENDEDOR_ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION, CONCAT(dt.denominacion, ' ', v.documento) AS DOCUMENTO";
		$from = "vendedor v INNER JOIN documentotipo dt ON v.documentotipo = dt.documentotipo_id";
		$vendedor_collection = CollectorCondition()->get('Vendedor', NULL, 4, $from, $select);

		$select_egresos = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.costo_producto AS COSTO, ed.importe AS IMPORTE, ed.egresodetalle_id AS EGRESODETALLEID, ed.producto_id AS PRODUCTO, ed.valor_descuento AS VD, ed.iva AS IVA, ed.neto_producto AS NETPRO";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_egresos = "ed.egreso_id = {$arg}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		$this->view->editar($producto_collection, $cliente_collection, $vendedor_collection, $condicionpago_collection,
							$condicioniva_collection, $egresodetalle_collection, $this->model);
	}

	function reingreso($arg) {
    	SessionHandler()->check_session();

		$this->model->egreso_id = $arg;
		$this->model->get();

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO, p.stock_minimo AS STMINIMO, p.stock_ideal AS STIDEAL, p.costo as COSTO, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA, (((p.costo * p.porcentaje_ganancia)/100)+p.costo) AS VENTA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id LEFT JOIN productodetalle pd ON p.producto_id = pd.producto_id LEFT JOIN proveedor prv ON pd.proveedor_id = prv.proveedor_id";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', NULL, 4, $from, $select, $groupby);

		$select_egresos = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.costo_producto AS COSTO, ed.importe AS IMPORTE, ed.egresodetalle_id AS EGRESODETALLEID, ed.producto_id AS PRODUCTO, ed.valor_descuento AS VD, ed.iva AS IVA, ed.neto_producto AS NETPRO";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_egresos = "ed.egreso_id = {$arg}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		$this->view->reingreso($producto_collection, $egresodetalle_collection, $this->model);
	}

	function siguiente_remito() {
		SessionHandler()->check_session();

		$select = "(MAX(e.numero_factura) + 1 ) AS SIGUIENTE_NUMERO ";
		$from = "egreso e";
		$where = "e.tipofactura = 2";
		$groupby = "e.tipofactura";
		$siguiente_remito = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$siguiente_remito = (!is_array($siguiente_remito)) ? 1 : $siguiente_remito[0]['SIGUIENTE_NUMERO'];

		return $siguiente_remito;
	}

	function guardar() {
		SessionHandler()->check_session();

		$com = new Configuracion();
		$com->configuracion_id = 1;
		$com->get();
		$punto_venta = $com->punto_venta;

		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();
		$dias_alerta_comision = $ccm->dias_alerta_comision;
		$dias_vencimiento = $ccm->dias_vencimiento;
		$num_factura = $this->siguiente_remito();

		$select = "e.numero_factura AS NUMERO_FACTURA";
		$from = "egreso e";
		$where = "e.numero_factura = {$num_factura}";
		$groupby = "e.tipofactura";
		$verificar_remito = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		if (is_array($verificar_remito)) {
			$num_factura = $this->siguiente_remito();
		}

		$fecha = filter_input(INPUT_POST, 'fecha');
		$hora = date('H:i:s');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($num_factura, 8, '0', STR_PAD_LEFT);

		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$vm = new Vendedor();
		$vm->vendedor_id = $vendedor_id;
		$vm->get();
		$comision = $vm->comision;

		$ecm = new EgresoComision();
		$ecm->fecha = $fecha;
		$ecm->valor_comision = round($comision, 2);
		$ecm->valor_abonado = 0;
		$ecm->estadocomision = 1;
		$ecm->save();
		$egresocomision_id = $ecm->egresocomision_id;

		$cliente_id = filter_input(INPUT_POST, 'cliente');
		$cm = new Cliente();
		$cm->cliente_id = $cliente_id;
		$cm->get();
		$flete_id = $cm->flete->flete_id;

		$fecha_entrega = strtotime('+1 day', strtotime($fecha));
		$fecha_entrega = date('Y-m-d', $fecha_entrega);

		$eem = new EgresoEntrega();
		$eem->fecha = $fecha_entrega;
		$eem->flete = $flete_id;
		$eem->estadoentrega = 2;
		$eem->save();
		$egresoentrega_id = $eem->egresoentrega_id;

		$condicionpago = filter_input(INPUT_POST, 'condicionpago');
		$importe_total = filter_input(INPUT_POST, 'importe_total');
		$tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$descuento = filter_input(INPUT_POST, 'descuento');
		$this->model = new Egreso();
		$this->model->punto_venta = $punto_venta;
		$this->model->numero_factura = intval($num_factura);
		$this->model->fecha = $fecha;
		$this->model->hora = $hora;
		$this->model->descuento = (is_null($descuento)) ? 0 : $descuento;
		$this->model->subtotal = filter_input(INPUT_POST, 'subtotal');
		$this->model->importe_total = $importe_total;
		$this->model->emitido = 0;
		$this->model->dias_alerta_comision = $dias_alerta_comision;
		$this->model->dias_vencimiento = $dias_vencimiento;
		$this->model->cliente = $cliente_id;
		$this->model->vendedor = $vendedor_id;
		$this->model->tipofactura = $tipofactura;
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionpago = $condicionpago;
		$this->model->egresocomision = $egresocomision_id;
		$this->model->egresoentrega = $egresoentrega_id;		
		$this->model->save();
		$egreso_id = $this->model->egreso_id;
		
		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		if ($condicionpago == 1) {
			$cccm = new CuentaCorrienteCliente();
			$cccm->fecha = date('Y-m-d');
			$cccm->hora = date('H:i:s');
			$cccm->referencia = "Comprobante venta {$comprobante}";
			$cccm->importe = $importe_total;
			$cccm->cliente_id = $cliente_id;
			$cccm->egreso_id = $egreso_id;
			$cccm->tipomovimientocuenta = 1;
			$cccm->estadomovimientocuenta = 1;
			$cccm->save();
			$cuentacorrientecliente_id = $cccm->cuentacorrientecliente_id;
		}

		$egresos_array = $_POST['egreso'];
		$egresodetalle_ids = array();
		foreach ($egresos_array as $egreso) {
			$producto_id = $egreso['producto_id'];
			$cantidad = $egreso['cantidad'];
			$costo_producto = $egreso['costo'];
			$valor_descuento = $egreso['importe_descuento'];
			$importe = $egreso['costo_total'];
			$iva = $egreso['iva'];
			
			$pm = new Producto();
			$pm->producto_id = $producto_id;
			$pm->get();

			$neto = $pm->costo;
			$flete = $pm->flete;
			$porcentaje_ganancia = $pm->porcentaje_ganancia;
			
			if ($tipofactura == 2) {
				$valor_neto = $neto + ($iva * $neto / 100);
				$valor_neto = $valor_neto + ($flete * $valor_neto / 100);
			} else {
				$valor_neto = $neto + ($flete * $neto / 100);
			}
			
			$total_neto = $valor_neto * $cantidad;
			$ganancia_temp = $total_neto * ($porcentaje_ganancia / 100 + 1);
			$ganancia = round(($ganancia_temp - $total_neto),2);

			$edm = new EgresoDetalle();
			$edm->codigo_producto = $egreso['codigo'];
			$edm->descripcion_producto = $egreso['descripcion'];
			$edm->cantidad = $cantidad;
			$edm->valor_descuento = $valor_descuento;
			$edm->descuento = $egreso['descuento'];
			$edm->neto_producto = $neto;
			$edm->costo_producto = $costo_producto;
			$edm->iva = $egreso['iva'];
			$edm->importe = $importe;
			$edm->valor_ganancia = $ganancia;
			$edm->producto_id = $egreso['producto_id'];
			$edm->egreso_id = $egreso_id;
			$edm->egresodetalleestado = 1;
			$edm->flete_producto = $flete;
			$edm->save();
			$egresodetalle_ids[] = $edm->egresodetalle_id;
		}

		$select_egresos = "ed.producto_id AS PRODUCTO_ID, ed.codigo_producto AS CODIGO, ed.cantidad AS CANTIDAD";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id";
		$where_egresos = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		$flag_error = 0;
		if ($tipofactura == 1 OR $tipofactura == 3) {
			try {
			    $this->facturar_afip_argumento($egreso_id);
			} catch (Exception $e) {
				$ecm = new EgresoComision();
				$ecm->egresocomision_id = $egresocomision_id;
				$ecm->delete();

				$eem = new EgresoEntrega();
				$eem->egresoentrega_id = $egresoentrega_id;
				$eem->delete();

				$this->model =  new Egreso();
				$this->model->egreso_id = $egreso_id;
				$this->model->delete();

				if ($condicionpago == 1) {
					$cccm = new CuentaCorrienteCliente();
					$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
					$cccm->delete();
				}

				foreach ($egresodetalle_ids as $egresodetalle_id) {
					$edm = new EgresoDetalle();
					$edm->egresodetalle_id = $egresodetalle_id;
					$edm->delete();
				}

				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->delete();
				print_r($e->getMessage());exit;
				switch ($e->getCode()) {
					case 4:
						$flag_error = 3;
						break;
					case 10015:
						$flag_error = 4;
						break;
				}
			}
		}

		if ($flag_error == 0) {
			foreach ($egresodetalle_collection as $egreso) {
				$temp_producto_id = $egreso['PRODUCTO_ID'];
				$select_stock = "MAX(s.stock_id) AS STOCK_ID";
				$from_stock = "stock s";
				$where_stock = "s.producto_id = {$temp_producto_id}";
				$rst_stock = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

				if ($rst_stock == 0 || empty($rst_stock) || !is_array($rst_stock)) {
					$sm = new Stock();
					$sm->fecha = $fecha;
					$sm->hora = $hora;
					$sm->concepto = "Venta. Comprobante: {$comprobante}";
					$sm->codigo = $egreso['CODIGO'];
					$sm->cantidad_actual = $egreso['CANTIDAD'];
					$sm->cantidad_movimiento = -$egreso['CANTIDAD'];
					$sm->producto_id = $temp_producto_id;
					$sm->save();
				} else {
					$stock_id = $rst_stock[0]['STOCK_ID'];
					$sm = new Stock();
					$sm->stock_id = $stock_id;
					$sm->get();
					$ultima_cantidad = $sm->cantidad_actual;
					$nueva_cantidad = $ultima_cantidad - $egreso['CANTIDAD'];

					$sm = new Stock();
					$sm->fecha = $fecha;
					$sm->hora = $hora;
					$sm->concepto = "Venta. Comprobante: {$comprobante}";
					$sm->codigo = $egreso['CODIGO'];
					$sm->cantidad_actual = $nueva_cantidad;
					$sm->cantidad_movimiento = -$egreso['CANTIDAD'];
					$sm->producto_id = $temp_producto_id;
					$sm->save();
				}
			}

			header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
		} else {
			header("Location: " . URL_APP . "/egreso/listar/{$flag_error}");
		}
	}

	function actualizar() {
		SessionHandler()->check_session();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		$punto_venta = filter_input(INPUT_POST, 'punto_venta');
		$numero_factura = filter_input(INPUT_POST, 'numero_factura');
		$fecha = filter_input(INPUT_POST, 'fecha');
		$hora = date('H:i:s');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);

		$condicionpago = filter_input(INPUT_POST, 'condicionpago');
		$importe_total = filter_input(INPUT_POST, 'importe_total');
		$this->model->fecha = $fecha;
		$this->model->hora = $hora;
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->subtotal = filter_input(INPUT_POST, 'subtotal');
		$this->model->importe_total = $importe_total;
		$this->model->cliente = filter_input(INPUT_POST, 'cliente');
		$this->model->vendedor = filter_input(INPUT_POST, 'vendedor');
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionpago = $condicionpago;
		$this->model->save();

		$select = "ccc.cuentacorrientecliente_id AS ID";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id}";
		$cuentacorrientecliente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		if ($condicionpago == 1) {
			if (is_array($cuentacorrientecliente) AND !empty($cuentacorrientecliente)) {
				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $cuentacorrientecliente[0]['ID'];
				$cccm->get();
				$cccm->importe = $importe_total;
				$cccm->save();
			} else {
				$cccm = new CuentaCorrienteCliente();
				$cccm->fecha = date('Y-m-d');
				$cccm->hora = date('H:i:s');
				$cccm->referencia = "Comprobante venta {$comprobante}";
				$cccm->importe = $importe_total;
				$cccm->cliente_id = $cliente_id;
				$cccm->egreso_id = $egreso_id;
				$cccm->tipomovimientocuenta = 1;
				$cccm->estadomovimientocuenta = 1;
				$cccm->save();
			}
		} else {
			if (is_array($cuentacorrientecliente) AND !empty($cuentacorrientecliente)) {
				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $cuentacorrientecliente[0]['ID'];
				$cccm->delete();
			}
		}

		$select_egresos = "ed.egresodetalle_id AS ID,ed.producto_id AS PRODUCTO_ID, ed.codigo_producto AS CODIGO, ed.cantidad AS CANTIDAD";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id";
		$where_egresos = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		if (!empty($egresodetalle_collection) AND is_array($egresodetalle_collection)) {
			foreach ($egresodetalle_collection as $egresodetalle) {
				$temp_egresodetalle_id = $egresodetalle['ID'];
				$temp_producto_id = $egresodetalle['PRODUCTO_ID'];
				$select_stock = "MAX(s.stock_id) AS STOCK_ID";
				$from_stock = "stock s";
				$where_stock = "s.producto_id = {$temp_producto_id}";
				$rst_stock = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad + $egresodetalle['CANTIDAD'];

				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Edición Venta. Comprobante: {$comprobante}";
				$sm->codigo = $egresodetalle['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = +$egresodetalle['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->save();

				$edm = new EgresoDetalle();
				$edm->egresodetalle_id = $temp_egresodetalle_id;
				$edm->delete();
			}
		}

		$egresos_array = $_POST['egreso'];
		foreach ($egresos_array as $egreso) {
			$producto_id = $egreso['producto_id'];
			$cantidad = $egreso['cantidad'];
			$costo_producto = $egreso['costo'];
			$valor_descuento = $egreso['importe_descuento'];
			$importe = $egreso['costo_total'];

			$pm = new Producto();
			$pm->producto_id = $producto_id;
			$pm->get();

			$neto = $pm->costo;
			$flete = $pm->flete;
			$porcentaje_ganancia = $pm->porcentaje_ganancia;
			$valor_neto = $neto + ($flete * $neto / 100);
			$total_neto = $valor_neto * $cantidad;

			$ganancia_temp = $total_neto * ($porcentaje_ganancia / 100 + 1);
			$ganancia = round(($ganancia_temp - $total_neto),2);

			$edm = new EgresoDetalle();
			$edm->codigo_producto = $egreso['codigo'];
			$edm->descripcion_producto = $egreso['descripcion'];
			$edm->cantidad = $egreso['cantidad'];
			$edm->valor_descuento = $egreso['importe_descuento'];
			$edm->descuento = $egreso['descuento'];
			$edm->neto_producto = $neto;
			$edm->costo_producto = $egreso['costo'];
			$edm->iva = $egreso['iva'];
			$edm->importe = $egreso['costo_total'];
			$edm->valor_ganancia = $ganancia;
			$edm->producto_id = $egreso['producto_id'];
			$edm->egreso_id = $egreso_id;
			$edm->egresodetalleestado = 1;
			$edm->flete_producto = $flete;
			$edm->save();
		}

		$select_egresos = "ed.producto_id AS PRODUCTO_ID, ed.codigo_producto AS CODIGO, ed.cantidad AS CANTIDAD";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id";
		$where_egresos = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		foreach ($egresodetalle_collection as $egreso) {
			$temp_producto_id = $egreso['PRODUCTO_ID'];
			$select_stock = "MAX(s.stock_id) AS STOCK_ID";
			$from_stock = "stock s";
			$where_stock = "s.producto_id = {$temp_producto_id}";
			$rst_stock = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

			if ($rst_stock == 0 || empty($rst_stock) || !is_array($rst_stock)) {
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Edición Venta. Comprobante: {$comprobante}";
				$sm->codigo = $egreso['CODIGO'];
				$sm->cantidad_actual = $egreso['CANTIDAD'];
				$sm->cantidad_movimiento = -$egreso['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->save();
			} else {
				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad - $egreso['CANTIDAD'];

				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Edición Venta. Comprobante: {$comprobante}";
				$sm->codigo = $egreso['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = -$egreso['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->save();
			}
		}

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}

	function cerrar_comprobante($arg) {
		SessionHandler()->check_session();
		$egreso_id = $arg;
		$this->model->egreso_id = $egreso_id;
		$this->model->get();
		$this->model->emitido = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}

	function guardar_nota_credito() {
		SessionHandler()->check_session();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$importe_total = filter_input(INPUT_POST, 'importe_total');
		$this->model->egreso_id = $egreso_id;
		$this->model->get();
		$condicionpago_egreso = $this->model->condicionpago->condicionpago_id;
		$tipofactura_egreso = $this->model->tipofactura->tipofactura_id;
		switch ($tipofactura_egreso) {
			case 1:
				$tipofactura_nc = 4;
				break;
			case 2:
				$tipofactura_nc = 6;
				break;
			case 3:
				$tipofactura_nc = 5;
				break;
		}

		$fecha = date('Y-m-d');
		$hora = date('H:i:s');
		$punto_venta = $this->model->punto_venta;
		$numero_factura = $this->model->numero_factura;

		$ncm = new NotaCredito();
		$ncm->egreso_id = $egreso_id;
		$ncm->eliminar_nota_credito();

		$ncm = new NotaCredito();
		$ncm->punto_venta = $punto_venta;
		$ncm->numero_factura = $numero_factura;
		$ncm->fecha = $fecha;
		$ncm->hora = $hora;
		$ncm->subtotal = filter_input(INPUT_POST, 'subtotal');
		$ncm->importe_total = $importe_total;
		$ncm->egreso_id = $egreso_id;
		$ncm->tipofactura = $tipofactura_nc;

		$ncm->save();
		$notacredito_id = $ncm->notacredito_id;

		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);

		$select_notascredito = "ncd.notacreditodetalle_id AS ID, ncd.producto_id AS PRODUCTO_ID, ncd.codigo_producto AS CODIGO, ncd.cantidad AS CANTIDAD";
		$from_notascredito = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id";
		$where_notascredito = "ncd.egreso_id = {$egreso_id}";
		$notascreditodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_notascredito, 4, $from_notascredito, $select_notascredito);

		if (!empty($notascreditodetalle_collection) AND is_array($notascreditodetalle_collection)) {
			foreach ($notascreditodetalle_collection as $notacredito) {
				$temp_notacredito_id = $notacredito['ID'];
				$temp_producto_id = $notacredito['PRODUCTO_ID'];
				$select_stock = "MAX(s.stock_id) AS STOCK_ID";
				$from_stock = "stock s";
				$where_stock = "s.producto_id = {$temp_producto_id}";
				$rst_stock = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad - $notacredito['CANTIDAD'];

				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Edición Nota de Crédito. Comprobante: {$comprobante}";
				$sm->codigo = $notacredito['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = -$notacredito['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->save();

				$ncdm = new NotaCreditoDetalle();
				$ncdm->notacreditodetalle_id = $temp_notacredito_id;
				$ncdm->delete();
			}
		}

		$egresos_array = $_POST['egreso'];
		foreach ($egresos_array as $egreso) {
			$producto_id = $egreso['producto_id'];
			$cantidad = $egreso['cantidad'];
			$costo_producto = $egreso['costo'];
			$valor_descuento = $egreso['importe_descuento'];
			$importe = $egreso['costo_total'];
			$ganancia_temp = $egreso['ganancia'];

			/*
			$neto = $pm->costo;
			$flete = $pm->flete;
			$porcentaje_ganancia = $pm->porcentaje_ganancia;
			$valor_neto = $neto + ($flete * $neto / 100);
			$total_neto = $valor_neto * $cantidad;

			$ganancia_temp = $total_neto * ($porcentaje_ganancia / 100 + 1);
			$ganancia = round(($ganancia_temp - $total_neto),2);
			---------------------
			*/

			$pm = new Producto();
			$pm->producto_id = $producto_id;
			$pm->get();
			$flete = $pm->flete;
			$neto = $pm->costo;
			$porcentaje_ganancia = $pm->porcentaje_ganancia;
			$valor_neto = $neto + ($flete * $neto / 100);
			$total_neto = $valor_neto * $cantidad;

			$ganancia_temp = $total_neto * ($porcentaje_ganancia / 100 + 1);
			$ganancia = round(($ganancia_temp - $total_neto),2);

			$ncdm = new NotaCreditoDetalle();
			$ncdm->codigo_producto = $egreso['codigo'];
			$ncdm->descripcion_producto = $egreso['descripcion'];
			$ncdm->cantidad = $egreso['cantidad'];
			$ncdm->descuento = $egreso['descuento'];
			$ncdm->valor_descuento = $egreso['importe_descuento'];
			$ncdm->neto_producto = $neto;
			$ncdm->costo_producto = $egreso['costo'];
			$ncdm->iva = $egreso['iva'];
			$ncdm->importe = $egreso['costo_total'];
			$ncdm->valor_ganancia = $ganancia;
			$ncdm->producto_id = $egreso['producto_id'];
			$ncdm->egreso_id = $egreso_id;
			$ncdm->notacredito_id = $notacredito_id;
			$ncdm->save();
		}

		$select_notascredito = "ncd.producto_id AS PRODUCTO_ID, ncd.codigo_producto AS CODIGO, ncd.cantidad AS CANTIDAD";
		$from_notascredito = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id";
		$where_notascredito = "ncd.egreso_id = {$egreso_id} AND ncd.notacredito_id = {$notacredito_id}";
		$notascreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where_notascredito, 4, $from_notascredito, $select_notascredito);

		foreach ($notascreditodetalle_collection as $notacredito) {
			$temp_producto_id = $notacredito['PRODUCTO_ID'];
			$select_stock = "MAX(s.stock_id) AS STOCK_ID";
			$from_stock = "stock s";
			$where_stock = "s.producto_id = {$temp_producto_id}";
			$rst_stock = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

			if ($rst_stock == 0 || empty($rst_stock) || !is_array($rst_stock)) {
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Nota de Crédito. Comprobante: {$comprobante}";
				$sm->codigo = $notacredito['CODIGO'];
				$sm->cantidad_actual = $notacredito['CANTIDAD'];
				$sm->cantidad_movimiento = $notacredito['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->save();
			} else {
				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad + $notacredito['CANTIDAD'];

				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Nota de Crédito. Comprobante: {$comprobante}";
				$sm->codigo = $notacredito['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = $notacredito['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->save();
			}
		}

		if ($condicionpago_egreso == 1) {
			$select = "ccc.cuentacorrientecliente_id CCCID";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$cuentacorrientecliente_id = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			$cuentacorrientecliente_id = $cuentacorrientecliente_id[0]['CCCID'];
			$cccm = new CuentaCorrienteCliente();
			$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
			$cccm->get();
			$cccm->referencia = 'Ajuste por NC. ' . $cccm->referencia;
			$cccm->importe = $cccm->importe - $importe_total;
			$cccm->save();
		}

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}

	function entregas_pendientes($arg) {
		SessionHandler()->check_session();
    	$select = "e.egreso_id AS EGRESO_ID, date_format(e.fecha, '%d/%m/%Y') AS FECHA, UPPER(cl.razon_social) AS CLIENTE, ci.denominacion AS CI, e.subtotal AS SUBTOTAL, f.denominacion AS FLETE, e.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR, UPPER(cp.denominacion) AS CP, CONCAT(ese.denominacion, ' (', date_format(ee.fecha, '%d/%m/%Y'), ')') AS ENTREGA, CASE ee.estadoentrega WHEN 1 THEN 'inline-block' WHEN 3 THEN 'none' END AS DSP_BTN_ENT, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id INNER JOIN flete f ON ee.flete = f.flete_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "ee.estadoentrega NOT IN(3,4,5) ORDER BY e.fecha ASC";
		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		foreach ($egreso_collection as $clave=>$valor) {
			$egreso_id = $valor['EGRESO_ID'];
			$select = "nc.importe_total AS IMPORTETOTAL";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$egreso_id}";
			$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($notacredito) AND !empty($notacredito)) {
				$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
				$egreso_collection[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
				$egreso_collection[$clave]['IMPORTETOTAL'] = $egreso_collection[$clave]['IMPORTETOTAL'] - $importe_notacredito;
				//$egreso_collection[$clave]['VC'] = round(($egreso_collection[$clave]['COMISION'] * $egreso_collection[$clave]['IMPORTETOTAL'] / 100),2);
			} else {
				$egreso_collection[$clave]['NC_IMPORTE_TOTAL'] = 0;
			}

			if ($egreso_collection[$clave]['IMPORTETOTAL'] == 0 AND $egreso_collection[$clave]["VC"] == 0) {
				unset($egreso_collection[$clave]);
			}
		}

		$flete_collection = Collector()->get('Flete');

		$this->view->entregas_pendientes($egreso_collection, $flete_collection, $arg);
	}

	function flete_entregas_pendientes($arg) {
		SessionHandler()->check_session();
    	$select = "e.egreso_id AS EGRESO_ID, date_format(e.fecha, '%d/%m/%Y') AS FECHA, UPPER(cl.razon_social) AS CLIENTE, CONCAT(LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) AS FACTURA, e.subtotal AS SUBTOTAL, f.denominacion AS FLETE, e.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR, UPPER(cp.denominacion) AS CP, CONCAT(ese.denominacion, ' (', date_format(ee.fecha, '%d/%m/%Y'), ')') AS ENTREGA, CASE ee.estadoentrega WHEN 1 THEN 'inline-block' WHEN 3 THEN 'none' END AS DSP_BTN_ENT, cl.localidad AS LOCALIDAD, cl.domicilio AS DOMICILIO";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id INNER JOIN flete f ON ee.flete = f.flete_id";
		$where = "ee.estadoentrega != 4 AND f.flete_id = {$arg} ORDER BY e.fecha ASC";
		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$flete_collection = Collector()->get('Flete');

		$fm = new Flete();
		$fm->flete_id = $arg;
		$fm->get();

		$this->view->flete_entregas_pendientes($egreso_collection, $flete_collection, $fm);
	}

	function buscar() {
		SessionHandler()->check_session();

    	$select = "e.egreso_id AS EGRESO_ID, date_format(e.fecha, '%d/%m/%Y') AS FECHA, UPPER(cl.razon_social) AS CLIENTE, ci.denominacion AS CI, CONCAT(LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) AS BK, e.subtotal AS SUBTOTAL, ese.denominacion AS ENTREGA, e.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR, UPPER(cp.denominacion) AS CP, CASE ee.estadoentrega WHEN 1 THEN 'inline-block' WHEN 2 THEN 'inline-block' WHEN 3 THEN 'none' END AS DSP_BTN_ENT, CASE WHEN eafip.egresoafip_id IS NULL THEN 'inline-block' ELSE 'none' END AS DSP_BTN_EDIT, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";

		$tipo_busqueda = filter_input(INPUT_POST, 'tipo_busqueda');
		switch ($tipo_busqueda) {
			case 1:
				$desde = filter_input(INPUT_POST, 'desde');
				$hasta = filter_input(INPUT_POST, 'hasta');
				$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY e.fecha DESC";
				break;
		}

		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$this->view->buscar($egreso_collection);
	}

	function guardar_entrega() {
		SessionHandler()->check_session();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		$eem = new EgresoEntrega();
		$eem->egresoentrega_id = $this->model->egresoentrega->egresoentrega_id;
		$eem->get();
		$eem->fecha = filter_input(INPUT_POST, 'fecha_entrega');
		$eem->flete = filter_input(INPUT_POST, 'flete');
		$eem->estadoentrega = 1;
		$eem->save();

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}

	function confirmar_entrega($arg) {
		SessionHandler()->check_session();

		$this->model->egreso_id = $arg;
		$this->model->get();

		$eem = new EgresoEntrega();
		$eem->egresoentrega_id = $this->model->egresoentrega->egresoentrega_id;
		$eem->get();
		$eem->estadoentrega = 4;
		$eem->fecha = date('Y-m-d');
		$eem->save();

		header("Location: " . URL_APP . "/egreso/consultar/{$arg}");
	}

	function descargar_hoja_ruta_flete() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		unset($_POST['tbl_egreso_length']);

		if (isset($_POST['objeto']) AND is_array($_POST['objeto'])) {
			$egreso_ids = $_POST['objeto'];
			$egreso_in_ids =  implode('@3,', $egreso_ids);
			$egreso_in_ids =  "{$egreso_in_ids}@3";
			$array_exportacion = array();

			$flete_id = filter_input(INPUT_POST, 'flete_id');
			$fm = new Flete();
			$fm->flete_id = $flete_id;
			$fm->get();
			$denominacion = $fm->denominacion;

			$cant_cuentacorriente = 0;
			$cant_contado = 0;

			$array_encabezados = array('FECHA', 'COMPROBANTE', 'CLIENTE', 'COND PAGO', 'IMPORTE TOTAL');
			$array_exportacion[] = $array_encabezados;
			$total = 0;
			foreach ($egreso_ids as $egreso_id) {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();

				$total = $total + $em->importe_total;
				$condicionpago_id = $em->condicionpago->condicionpago_id;
				switch ($condicionpago_id) {
				 	case 1:
				 		$cant_cuentacorriente = $cant_cuentacorriente + $em->importe_total;
				 		break;
			 		case 2:
				 		$cant_contado = $cant_contado + $em->importe_total;
				 		break;
				}

				$egresoentrega_id = $em->egresoentrega->egresoentrega_id;
				$eem = new EgresoEntrega();
				$eem->egresoentrega_id = $egresoentrega_id;
				$eem->get();
				$eem->fecha = date('Y-m-d');
				$eem->estadoentrega = 3;
				$eem->flete = $flete_id;
				$eem->save();

				$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
				$numero_factura = str_pad($em->numero_factura, 4, '0', STR_PAD_LEFT);
				$array_temp = array(
								$em->fecha
								, "{$punto_venta}-{$numero_factura}"
								, $em->cliente->razon_social
								, $em->condicionpago->denominacion
								, $em->importe_total);
				$array_exportacion[] = $array_temp;
			}

			$array_exportacion[] = array('','','','','');
			$array_exportacion[] = array('','','','','');
			$array_exportacion[] = array('','','','Cuenta Corriente',$cant_cuentacorriente);
			$array_exportacion[] = array('','','','Contado',$cant_contado);
			$array_exportacion[] = array('','','','Total',$total);
			$array_exportacion[] = array('','','','','');
			$array_exportacion[] = array('','','','Combustible','$.......................');
			$array_exportacion[] = array('','','','Sencillo','$.......................');
			$array_exportacion[] = array('','','','Descuentos','$.......................');
			$array_exportacion[] = array('','','','Cta. Cte.','$.......................');
			$array_exportacion[] = array('','','','Efectivo','$.......................');
			$array_exportacion[] = array('','','','Totales','$.......................');

			$fecha_actual = date('Y-m-d');
			$hrm = new HojaRuta();
			$hrm->fecha = $fecha_actual;
			$hrm->flete_id = $flete_id;
			$hrm->egreso_ids = $egreso_in_ids;
			$hrm->estadoentrega = 3;
			$hrm->save();
			$hrm->get();

			$array_cantidades = array('{cant_cuentacorriente}'=>$cant_cuentacorriente,
									  '{cant_contado}'=>$cant_contado);
			$subtitulo = "{$fecha_actual} - HOJA DE RUTA: {$denominacion} - Nº{$hrm->hojaruta_id}";

			ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		} else {
			header("Location: " . URL_APP . "/egreso/entregas_pendientes/3");
		}
	}

	function reimprimir_hoja_ruta_flete($arg) {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$fecha_actual = date('Y-m-d');

		$hrm = new HojaRuta();
		$hrm->hojaruta_id = $arg;
		$hrm->get();
 		$egreso_ids = $hrm->egreso_ids;
		$egreso_ids =  explode('@3,', $egreso_ids);
		$egreso_ids = str_replace('@3', '', $egreso_ids);
		$array_exportacion = array();

		$flete_id = $hrm->flete_id;
		$fm = new Flete();
		$fm->flete_id = $flete_id;
		$fm->get();
		$denominacion = $fm->denominacion;

		$cant_cuentacorriente = 0;
		$cant_contado = 0;
		$array_productos = array();
		foreach ($egreso_ids as $egreso_id) {
			$select = 'ed.codigo_producto AS COD,ed.descripcion_producto AS PRODUCTO,cantidad AS CANTIDAD,pu.denominacion AS UNIDAD';
			$from = 'egresodetalle ed INNER JOIN producto pr ON pr.producto_id = ed.producto_id INNER JOIN productounidad pu ON pu.productounidad_id = pr.productounidad';
			$where = "ed.egreso_id = {$egreso_id}";
			$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

			foreach ($egresodetalle_collection as $clave => $producto) {
				$key = array_search($producto['COD'], array_column($array_productos, 'COD'));
				if (false !== $key OR !empty($key)) {
					$array_productos[$key]['CANTIDAD'] = $array_productos[$key]['CANTIDAD']+$producto['CANTIDAD'];
 				}else {
					array_push($array_productos, $producto);
				}
			}
		}
		
		$array_encabezados2 = array('CODIGO', 'PRODUCTO', 'CANTIDAD', 'UNIDAD', '', '', '');
		$array_exportacion2[] = $array_encabezados2;
		foreach ($array_productos as $producto) {
			$array_temp = array($producto['COD']
								, $producto['PRODUCTO']
								, $producto['CANTIDAD']
								, $producto['UNIDAD']
								, ''
								, ''
								, '');
			$array_exportacion2[] = $array_temp;
		}

		$subtitulo = "{$hrm->fecha} - HOJA DE RUTA: {$denominacion} - Nº{$arg}";
		$array_encabezados = array('FECHA', 'COMPROBANTE', 'CLIENTE', 'COND PAGO', 'IMPORTE TOTAL');
		$array_exportacion[] = $array_encabezados;
		$total = 0;
		foreach ($egreso_ids as $egreso_id) {
			$em = new Egreso();
			$em->egreso_id = $egreso_id;
			$em->get();

			$condicionpago_id = $em->condicionpago->condicionpago_id;
			switch ($condicionpago_id) {
				case 1:
					$cant_cuentacorriente = $cant_cuentacorriente + $em->importe_total;
					break;
				case 2:
					$cant_contado = $cant_contado + $em->importe_total;
					break;
			}

			$total = $total + $em->importe_total;
			$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
			$numero_factura = str_pad($em->numero_factura, 4, '0', STR_PAD_LEFT);
			$array_temp = array(
							$em->fecha
							, "{$punto_venta}-{$numero_factura}"
							, $em->cliente->razon_social
							, $em->condicionpago->denominacion
							, $em->importe_total);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('','','','','');
		$array_exportacion[] = array('','','','','');
		$array_exportacion[] = array('','','','Cuenta Corriente',$cant_cuentacorriente);
		$array_exportacion[] = array('','','','Contado',$cant_contado);
		$array_exportacion[] = array('','','','Total',$total);
		$array_exportacion[] = array('','','','','');
		$array_exportacion[] = array('','','','Combustible','$.......................');
		$array_exportacion[] = array('','','','Sencillo','$.......................');
		$array_exportacion[] = array('','','','Descuentos','$.......................');
		$array_exportacion[] = array('','','','Cuenta Corriente','$.......................');
		$array_exportacion[] = array('','','','Efectivo','$.......................');
		$array_exportacion[] = array('','','','Totales','$.......................');
		$array_cantidades = array('{cant_cuentacorriente}'=>$cant_cuentacorriente, '{cant_contado}'=>$cant_contado);

		ExcelReport()->extraer_informe_conjunto_remanente($subtitulo, $array_exportacion,$array_exportacion2);

	}

	function confirmar_entregas() {
		SessionHandler()->check_session();
		unset($_POST['tbl_egreso_length'], $_POST['flete_id']);
		if (isset($_POST['objeto']) AND is_array($_POST['objeto'])) {
			$egreso_ids = $_POST['objeto'];

			foreach ($egreso_ids as $egreso_id) {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();

				$egresoentrega_id = $em->egresoentrega->egresoentrega_id;
				$eem = new EgresoEntrega();
				$eem->egresoentrega_id = $egresoentrega_id;
				$eem->get();
				$eem->estadoentrega = 4;
				$eem->save();
			}

			header("Location: " . URL_APP . "/egreso/entregas_pendientes/5");
		} else {
			header("Location: " . URL_APP . "/egreso/entregas_pendientes/3");
		}
	}

	function traer_formulario_entrega_ajax($arg) {
		$this->model->egreso_id = $arg;
		$this->model->get();
		$flete_collection = Collector()->get('Flete');
		$this->view->traer_formulario_entrega_ajax($flete_collection, $this->model);
	}

	function traer_formulario_producto_ajax($arg) {
		$producto_id = $arg;
		$pm = new Producto();
		$pm->producto_id = $producto_id;
		$pm->get();
		$select = "MAX(s.stock_id) AS MAXID";
		$from = "stock s";
		$where = "s.producto_id = {$producto_id}";
		$stock_id = CollectorCondition()->get('Stock', $where, 4, $from, $select);
		$stock_id = $stock_id[0]['MAXID'];

		$sm = new Stock();
		$sm->stock_id = $stock_id;
		$sm->get();
		$cantidad_disponible = $sm->cantidad_actual;

		$this->view->traer_formulario_producto_ajax($pm, $cantidad_disponible);
	}

	function traer_formulario_producto_barcode_ajax($arg) {
		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();
		$parteuno_codebar = $ccm->parteuno_codebar;
		$partedos_codebar = $ccm->partedos_codebar;
		$separador_codebar = $ccm->separador_codebar;

		$parametros = explode($separador_codebar, $arg);
		$codebar = "";
		$pesaje = 0;
		switch ($parteuno_codebar) {
			case 1:
				$codebar = $parametros[0];
				break;
			case 2:
				$pesaje = $parametros[0];
				break;
		}

		switch ($partedos_codebar) {
			case 1:
				$codebar = $parametros[1];
				break;
			case 2:
				$pesaje = $parametros[1];
				break;
		}

		if (!empty($codebar)) {
			$select = "p.producto_id AS ID";
			$from = "producto p";
			$where = "p.barcode = {$codebar}";
			$producto_id = CollectorCondition()->get('Producto', $where, 4, $from, $select);
			$producto_id = (is_array($producto_id) AND !empty($producto_id)) ? $producto_id[0]['ID'] : 0;

			if ($producto_id != 0) {
				$pm = new Producto();
				$pm->producto_id = $producto_id;
				$pm->get();
				$select = "MAX(s.stock_id) AS MAXID";
				$from = "stock s";
				$where = "s.producto_id = {$producto_id}";
				$stock_id = CollectorCondition()->get('Stock', $where, 4, $from, $select);
				$stock_id = $stock_id[0]['MAXID'];

				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$cantidad_disponible = $sm->cantidad_actual;

				$this->view->traer_formulario_producto_codebar_ajax($pm, $cantidad_disponible, $pesaje);
			} else {
				print "0";
			}
		} else {
			print "1";
		}
	}

	function traer_costo_producto_ajax($arg) {
		$pm = new Producto();
		$pm->producto_id = $arg;
		$pm->get();
		$costo = $pm->costo;
		print $costo;
	}

	function traer_listaprecio_cliente_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$listaprecio = $cm->listaprecio->porcentaje . ' @ ' . $cm->listaprecio->condicion;
		print $listaprecio;
	}

	function traer_descripcion_cliente_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$denominacion = $cm->documento . ' @ ' . $cm->razon_social;
		print $denominacion;
	}

	function traer_cliente_vendedor_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$vendedor = $cm->vendedor;
		$denominacion = $vendedor->apellido . ' ' . $vendedor->nombre . '@' . $vendedor->vendedor_id;
		print $denominacion;
	}

	function traer_cliente_tipofactura_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$tipofactura = $cm->tipofactura->tipofactura_id . '@';
		$tipofactura .= $cm->tipofactura->nomenclatura . ' - ' . $cm->tipofactura->denominacion;
		print $tipofactura;
	}

	function traer_cliente_condicioniva_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$condicioniva = $cm->condicioniva->condicioniva_id . '@' . $cm->condicioniva->denominacion;
		print $condicioniva;
	}

	function verificar_vencimiento_cuenta_ajax($arg) {
		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();
		$dias_vencimiento_cuentacorrientecliente = $ccm->dias_vencimiento_cuentacorrientecliente;

		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$dias_vencimiento_cuenta_corriente = $cm->dias_vencimiento_cuenta_corriente;

		$select = "COUNT(ccc.egreso_id) AS CANT";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha < date_add(NOW(), INTERVAL -{$dias_vencimiento_cuenta_corriente} DAY) AND ccc.cliente_id IN ({$arg}) AND ccc.estadomovimientocuenta != 4 AND (ccc.importe > 0 OR ccc.ingreso > 0)";
		$groupby = "ccc.egreso_id ORDER BY ccc.cliente_id ASC, ccc.egreso_id ASC, ccc.fecha DESC, ccc.estadomovimientocuenta DESC";
		$vencimiento_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);
		$cant_facturas_vencidas = (is_array($vencimiento_collection) AND !empty($vencimiento_collection)) ? $vencimiento_collection[0]['CANT'] : 0;

		print "{$dias_vencimiento_cuentacorrientecliente}@{$cant_facturas_vencidas}";
	}

	function traer_descripcion_vendedor_ajax($arg) {
		$vm = new Vendedor();
		$vm->vendedor_id = $arg;
		$vm->get();
		$denominacion = $vm->documentotipo->denominacion . ' ' . $vm->documento . ' - ';
		$denominacion .= $vm->apellido . ' ' .$vm->nombre;
		print $denominacion;
	}

	function traer_siguiente_numero_factura_ajax($arg) {
		$tipofactura_id = $arg;
		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura_id;
		$tfm->get();

		$cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();

		if ($tipofactura_id != 2) {
			$tipofactura_afip = $tfm->afip_id;
			$siguiente_factura = FacturaAFIPTool()->traerSiguienteFacturaAFIP($tipofactura_afip);
		} else {
			$select = "(MAX(e.numero_factura) + 1 ) AS SIGUIENTE_NUMERO ";
			$from = "egreso e";
			$where = "e.tipofactura = 2";
			$groupby = "e.tipofactura";
			$nuevo_numero = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
			$nuevo_numero = (!is_array($nuevo_numero)) ? 1 : $nuevo_numero[0]['SIGUIENTE_NUMERO'];

			$siguiente_factura = str_pad($cm->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
        	$siguiente_factura .= str_pad($nuevo_numero, 8, '0', STR_PAD_LEFT);
		}

		print $siguiente_factura;
	}

	function modal_mensaje_formulario_ajax($arg) {
		$cliente_id = $arg;
		$this->view->modal_mensaje_formulario_ajax($cliente_id);
	}

	function verificar_clave_autorizacion_ajax() {
		$pass = $_POST['clave_autorizar'];
		$select = "u.denominacion AS denominacion";
		$from = "usuario u";
		$where = "u.nivel IN (3,9) OR usuario_id = 33";
		$usuario_collection = CollectorCondition()->get('Usuario', $where, 4, $from, $select);
		$usuario_collection = (!is_array($usuario_collection)) ? array() : $usuario_collection;

    	$bandera_clave = 0;
    	$clave = hash(ALGORITMO_PASS, $pass);
		foreach ($usuario_collection as $c=>$v) {
			$usuario = $v['denominacion'];
			$user = hash(ALGORITMO_USER, $usuario);
    		$token = hash(ALGORITMO_FINAL, $user . $clave);
			$sql = "SELECT usuariodetalle_id FROM usuariodetalle WHERE token = ?";
	    	$datos = array($token);
        	$result = execute_query($sql, $datos);
			$bandera_clave = (is_array($result)) ? 1 : $bandera_clave;
		}

		print $bandera_clave;
	}

	function prepara_factura_afip($arg) {
		SessionHandler()->check_session();

		$ids = explode("@", $arg);
		$egreso_id = $ids[0];
		$tipofactura_id = $ids[1];
		$this->model->egreso_id = $egreso_id;
		$this->model->get();

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura_id;
		$tfm->get();

		$select_egresos = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.valor_descuento AS VD, p.no_gravado AS NOGRAVADO, ed.costo_producto AS COSTO, ROUND(ed.importe, 2) AS IMPORTE, ed.iva AS IVA, p.exento AS EXENTO";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_egresos = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		$array_afip = FacturaAFIPTool()->preparaFacturaAFIP($tfm, $this->model, $egresodetalle_collection);
		unset($array_afip['array_alicuotas']);
		$array_afip['egreso_id'] = $egreso_id;
		$array_afip['tipofactura_id'] = $tipofactura_id;
		$this->view->prepara_factura_afip($array_afip);
	}

	function facturar_afip() {
		SessionHandler()->check_session();

		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$this->model->egreso_id = $egreso_id;
		$this->model->get();
		$tipofactura_id = $this->model->tipofactura->tipofactura_id;

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura_id;
		$tfm->get();

		$select_egresos = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.valor_descuento AS VD, p.no_gravado AS NOGRAVADO, ed.costo_producto AS COSTO, ROUND(ed.importe, 2) AS IMPORTE, ed.iva AS IVA, p.exento AS EXENTO";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_egresos = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		$resultadoAFIP = FacturaAFIPTool()->facturarAFIP($cm, $tfm, $this->model, $egresodetalle_collection);
		if (is_array($resultadoAFIP)) {
			$eam = new EgresoAFIP();
			$eam->cae = $resultadoAFIP['CAE'];
			$eam->fecha = date('Y-m-d');
			$eam->punto_venta = $cm->punto_venta;
			$eam->numero_factura = $resultadoAFIP['NUMFACTURA'];
			$eam->vencimiento = $resultadoAFIP['CAEFchVto'];
			$eam->tipofactura = $tipofactura_id;
			$eam->egreso_id = $egreso_id;
			$eam->save();

			$this->model->egreso_id = $egreso_id;
			$this->model->get();
			$this->model->emitido = 1;
			$this->model->save();
		}

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}

	function facturar_afip_argumento($arg) {
		SessionHandler()->check_session();

		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$egreso_id = $arg;
		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();
		$tipofactura_id = $em->tipofactura->tipofactura_id;

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura_id;
		$tfm->get();

		$select_egresos = "ed.codigo_producto AS CODIGO, ed.descripcion_producto AS DESCRIPCION, ed.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ed.descuento AS DESCUENTO, ed.valor_descuento AS VD, p.no_gravado AS NOGRAVADO, ed.costo_producto AS COSTO, ROUND(ed.importe, 2) AS IMPORTE, ed.iva AS IVA, p.exento AS EXENTO";
		$from_egresos = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_egresos = "ed.egreso_id = {$egreso_id}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where_egresos, 4, $from_egresos, $select_egresos);

		$resultadoAFIP = FacturaAFIPTool()->facturarAFIP($cm, $tfm, $em, $egresodetalle_collection);
		if (is_array($resultadoAFIP)) {
			$eam = new EgresoAFIP();
			$eam->cae = $resultadoAFIP['CAE'];
			$eam->fecha = date('Y-m-d');
			$eam->punto_venta = $cm->punto_venta;
			$eam->numero_factura = $resultadoAFIP['NUMFACTURA'];
			$eam->vencimiento = $resultadoAFIP['CAEFchVto'];
			$eam->tipofactura = $tipofactura_id;
			$eam->egreso_id = $egreso_id;
			$eam->save();

			$this->model->egreso_id = $egreso_id;
			$this->model->get();
			$this->model->emitido = 1;
			$this->model->save();
		}

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}


	function afip() {
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$afip = new Afip(array('CUIT' => $cm->cuit, 'production' => false));
		$voucher_types = $afip->ElectronicBilling->GetVoucherTypes();
		print_r($voucher_types);
		exit;
		//$last_voucher = $afip->ElectronicBilling->GetLastVoucher(1,11);

		$data = array(
			'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
			'PtoVta' 	=> 1,  // Punto de venta
			'CbteTipo' 	=> 11,  // Tipo de comprobante (ver tipos disponibles)
			'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
			'DocTipo' 	=> 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
			'DocNro' 	=> 0,  // Número de documento del comprador (0 consumidor final)
			'CbteDesde' 	=> 1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
			'CbteHasta' 	=> 1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
			'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
			'ImpTotal' 	=> 121, // Importe total del comprobante
			'ImpTotConc' 	=> 0,   // Importe neto no gravado
			'ImpNeto' 	=> 121, // Importe neto gravado
			'ImpOpEx' 	=> 0,   // Importe exento de IVA
			'ImpIVA' 	=> 0,  //Importe total de IVA
			'ImpTrib' 	=> 0,   //Importe total de tributos
			'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
			'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)
		);

		$res = $afip->ElectronicBilling->CreateVoucher($data);

		$res['CAE']; //CAE asignado el comprobante
		$res['CAEFchVto']; //Fecha de vencimiento del CAE (yyyy-mm-dd)

		print_r($res);
		exit;
	}

	function traer_tipos_alicuotas() {
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$afip = new Afip(array('CUIT' => $cm->cuit, 'production' => false));
		$voucher_types = $afip->ElectronicBilling->GetAliquotTypes();
		print_r($voucher_types);
		exit;
	}

	function traer_datos_cliente($arg) {
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$afip = new Afip(array('CUIT' => $cm->cuit, 'production' => false));
		$contribuyente_details = $afip->RegisterScopeThirteen->GetTaxpayerDetails($arg);
		print_r($contribuyente_details);
		exit;
	}

	function actualizar_ganancia($arg) {
		SessionHandler()->check_session();
		$parametros = explode('@', $arg);
		$desde = $parametros[0];
		$hasta = $parametros[1];
		$select = "ed.egresodetalle_id AS ID, ROUND(((ed.importe * (p.porcentaje_ganancia / 100 + 1)) - ed.importe),2) AS GANANCIA";
		$from = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id";
		$where = "ed.egresodetalle_id BETWEEN {$desde} AND {$hasta}";
		$egresodetalle_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

		foreach ($egresodetalle_collection as $clave=>$valor) {
			$egresodetalle_id = $valor['ID'];
			$valor_ganancia = $valor['GANANCIA'];
			$edm = new EgresoDetalle();
			$edm->egresodetalle_id = $egresodetalle_id;
			$edm->get();
			$edm->valor_ganancia = $valor_ganancia;
			$edm->save();
		}

		print_r($egresodetalle_collection);exit;

	}

	function actualizar_ganancia_nc($arg) {
		SessionHandler()->check_session();
		$parametros = explode('@', $arg);
		$desde = $parametros[0];
		$hasta = $parametros[1];
		$select = "ncd.notacreditodetalle_id AS ID, ROUND(((ncd.importe * (p.porcentaje_ganancia / 100 + 1)) - ncd.importe),2) AS GANANCIA";
		$from = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id";
		$where = "ncd.notacreditodetalle_id BETWEEN {$desde} AND {$hasta}";
		$notacreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

		foreach ($notacreditodetalle_collection as $clave=>$valor) {
			$notacreditodetalle_id = $valor['ID'];
			$valor_ganancia = $valor['GANANCIA'];
			$ncdm = new NotaCreditoDetalle();
			$ncdm->notacreditodetalle_id = $notacreditodetalle_id;
			$ncdm->get();
			$ncdm->valor_ganancia = $valor_ganancia;
			$ncdm->save();
		}

		print_r($notacreditodetalle_collection);exit;

	}

	function final_hoja_ruta_flete($arg) {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$fecha_actual = date('Y-m-d');

		$hrm = new HojaRuta();
		$hrm->hojaruta_id = $arg;
		$hrm->get();
 		$egreso_ids = $hrm->egreso_ids;
		$egreso_ids =  explode('@4,', $egreso_ids);
		$egreso_ids = str_replace('@4', '', $egreso_ids);
		$array_exportacion = array();

		$flete_id = $hrm->flete_id;
		$fm = new Flete();
		$fm->flete_id = $flete_id;
		$fm->get();
		$denominacion = $fm->denominacion;

		$cant_cuentacorriente = 0;
		$cant_contado = 0;
		$total_abonado = 0;
		$total_deuda = 0;

		$subtitulo = "{$fecha_actual} - HOJA DE RUTA: {$denominacion} - Nº{$arg}";
		$array_encabezados = array('FECHA', 'COMPROBANTE', 'CLIENTE', 'COND PAGO', 'IMPORTE TOTAL', 'TOTAL/PARCIAL');
		$array_exportacion[] = $array_encabezados;
		$total = 0;
		foreach ($egreso_ids as $egreso_id) {
			$em = new Egreso();
			$em->egreso_id = $egreso_id;
			$em->get();

			$select = "importe AS IMPORTE";
			$from = "cuentacorrientecliente";
			$where = "egreso_id = {$em->egreso_id} AND tipomovimientocuenta = 2";
			$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			$importe = $cuentacorrientecliente_collection[0]['IMPORTE'];
			$total_abonado = $total_abonado + $importe;
			$deuda = $em->importe_total - $importe;
			$total_deuda = $total_deuda + $deuda;

			$total = $total + $em->importe_total;
			$condicionpago_id = $em->condicionpago->condicionpago_id;
			switch ($condicionpago_id) {
				case 1:
					$cant_cuentacorriente = $cant_cuentacorriente + $em->importe_total;
					break;
				case 2:
					$cant_contado = $cant_contado + $em->importe_total;
					break;
			}

			$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
			$numero_factura = str_pad($em->numero_factura, 4, '0', STR_PAD_LEFT);
			$array_temp = array($em->fecha
								, "{$punto_venta}-{$numero_factura}"
								, $em->cliente->razon_social
								, $em->condicionpago->denominacion
								, $em->importe_total
								, $importe);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('','','','','','');
		$array_exportacion[] = array('','','','','','');
		$array_exportacion[] = array('','','','','Cuenta Corriente',$cant_cuentacorriente);
		$array_exportacion[] = array('','','','','Contado',$cant_contado);
		$array_exportacion[] = array('','','','','Total',$total);
		$array_exportacion[] = array('','','','','Total Abonado',$total_abonado);
		$array_exportacion[] = array('','','','','Total Deuda',$total_deuda);
		$array_exportacion[] = array('','','','','','');
		$array_exportacion[] = array('','','','','Combustible','$.......................');
		$array_exportacion[] = array('','','','','Sencillo','$.......................');
		$array_exportacion[] = array('','','','','Descuentos','$.......................');
		$array_exportacion[] = array('','','','','Cuenta Corriente','$.......................');
		$array_exportacion[] = array('','','','','Efectivo','$.......................');
		$array_exportacion[] = array('','','','','Totales','$.......................');

		$array_cantidades = array('{cant_cuentacorriente}'=>$cant_cuentacorriente, '{cant_contado}'=>$cant_contado);

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
	}
}
?>