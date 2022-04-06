<?php
require_once "modules/notacredito/model.php";
require_once "modules/notacredito/view.php";
require_once "modules/notacreditodetalle/model.php";
require_once "modules/stock/model.php";
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/egreso/model.php";
require_once "modules/tipofactura/model.php";
require_once "tools/facturaAFIPTool.php";


class NotaCreditoController {

	function __construct() {
		$this->model = new NotaCredito();
		$this->view = new NotaCreditoView();
	}

	function listar() {
    	SessionHandler()->check_session();
    	$periodo_actual = date('Ym');
		$select = "nc.fecha AS FECHA, CONCAT(tifa.nomenclatura, ' ', LPAD(nc.punto_venta, 4, 0), '-', LPAD(nc.numero_factura, 8, 0)) AS NOTCRE, CASE WHEN nc.emitido_afip = 0 THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS REFERENCIA, cl.razon_social AS CLIENTE, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, nc.importe_total AS IMPORTETOTAL, nc.notacredito_id AS NOTACREDITO_ID";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN tipofactura tifa ON nc.tipofactura = tifa.tipofactura_id INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "date_format(nc.fecha, '%Y%m') = {$periodo_actual} ORDER BY e.fecha DESC";
		$notacredito_collection = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$this->view->listar($notacredito_collection);
	}

	function buscar() {
    	SessionHandler()->check_session();
		$select = "nc.fecha AS FECHA, CONCAT(tifa.nomenclatura, ' ', LPAD(nc.punto_venta, 4, 0), '-', LPAD(nc.numero_factura, 8, 0)) AS NOTCRE, CASE WHEN nc.emitido_afip = 0 THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS REFERENCIA, cl.razon_social AS CLIENTE, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, nc.importe_total AS IMPORTETOTAL, nc.notacredito_id AS NOTACREDITO_ID";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN tipofactura tifa ON nc.tipofactura = tifa.tipofactura_id INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$tipo_busqueda = filter_input(INPUT_POST, 'tipo_busqueda');
		switch ($tipo_busqueda) {
			case 1:
				$desde = filter_input(INPUT_POST, 'desde');
				$hasta = filter_input(INPUT_POST, 'hasta');
				$where = "nc.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY nc.fecha DESC";
				break;
		}
		
		$notacredito_collection = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$this->view->buscar($notacredito_collection);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		require_once 'tools/notacreditoPDFTool.php';
		require_once 'modules/configuracion/model.php';
		require_once "core/helpers/file.php";		

		$notacredito_id = $arg;
		$this->model->notacredito_id = $notacredito_id;
		$this->model->get();
		$egreso_id = $this->model->egreso_id;

		$em = new Egreso();
		$em->egreso_id = $this->model->egreso_id;
		$em->get();

		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$select = "ncd.codigo_producto AS CODIGO, ncd.descripcion_producto AS DESCRIPCION, ncd.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ncd.descuento AS DESCUENTO, ncd.valor_descuento AS VD, ncd.costo_producto AS COSTO, ROUND(ncd.importe, 2) AS IMPORTE, ncd.iva AS IVA";
		$from = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ncd.notacredito_id = {$notacredito_id}";
		$notacreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);
		
		$select_egresoafip = "eafip.punto_venta AS PUNTO_VENTA, eafip.numero_factura AS NUMERO_FACTURA, tf.nomenclatura AS TIPOFACTURA, eafip.cae AS CAE, eafip.vencimiento AS FVENCIMIENTO, eafip.fecha AS FECHA, tf.tipofactura_id AS TF_ID";
		$from_egresoafip = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
		$where_egresoafip = "eafip.egreso_id = {$egreso_id}";
		$egresoafip = CollectorCondition()->get('EgresoAfip', $where_egresoafip, 4, $from_egresoafip, $select_egresoafip);
		$egresoafip = (is_array($egresoafip)) ? $egresoafip : array();

		if (!empty($egresoafip)) {
			$em->punto_venta = $egresoafip[0]['PUNTO_VENTA'];
			$em->numero_factura = $egresoafip[0]['NUMERO_FACTURA'];		
		}		
		
		$notacreditoPDFHelper = new NotaCreditoPDF();
		$notacreditoPDFHelper->genera_notacredito($notacreditodetalle_collection, $cm, $em, $this->model);
		
		$em = new Egreso();
		$em->egreso_id = $this->model->egreso_id;
		$em->get();

		$select = "ccc.importe AS IMP";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id} AND ccc.tipomovimientocuenta = 2 ORDER BY ccc.cuentacorrientecliente_id DESC";
		$flag_ccc = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$flag_ccc = (is_array($flag_ccc) AND !empty($flag_ccc)) ? 1 : 0;
		
		$this->view->consultar($notacreditodetalle_collection, $this->model, $egresoafip, $em, $notacredito_id, $flag_ccc);
	}

	function prepara_notacredito_afip($arg) {
		SessionHandler()->check_session();

		$egreso_id = $arg;
		$select = "nc.notacredito_id AS NCID";
		$from = "notacredito nc";
		$where = "nc.egreso_id = {$egreso_id}";
		$notacredito_id = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$notacredito_id = $notacredito_id[0]['NCID'];
		
		$this->model->notacredito_id = $notacredito_id;
		$this->model->get();

		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();
		$tipofactura_id = $em->tipofactura->tipofactura_id;

		switch ($tipofactura_id) {
			case 1:
				$tiponc_id = 4;
				break;
			case 3:
				$tiponc_id = 5;
				break;
		}

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tiponc_id;
		$tfm->get();

		$select = "ncd.codigo_producto AS CODIGO, ncd.descripcion_producto AS DESCRIPCION, ncd.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ncd.descuento AS DESCUENTO, ncd.valor_descuento AS VD, p.no_gravado AS NOGRAVADO, ncd.costo_producto AS COSTO, ROUND(ncd.importe, 2) AS IMPORTE, ncd.iva AS IVA, p.exento AS EXENTO";
		$from = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ncd.egreso_id = {$egreso_id}";
		$notacreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

		$array_afip = FacturaAFIPTool()->preparaFacturaAFIPNC($tfm, $this->model, $notacreditodetalle_collection);
		unset($array_afip['array_alicuotas']);
		$array_afip['egreso_id'] = $egreso_id;
		$array_afip['notacredito_id'] = $notacredito_id;
		$this->view->prepara_notacredito_afip($array_afip);
	}

	function notacredito_afip() {
		SessionHandler()->check_session();
		
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$notacredito_id = filter_input(INPUT_POST, 'notacredito_id');
		$this->model->notacredito_id = $notacredito_id;
		$this->model->get();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();
		$tipofactura_id = $em->tipofactura->tipofactura_id;

		switch ($tipofactura_id) {
			case 1:
				$tiponc_id = 4;
				break;
			case 3:
				$tiponc_id = 5;
				break;
		}

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tiponc_id;
		$tfm->get();

		$select = "ncd.codigo_producto AS CODIGO, ncd.descripcion_producto AS DESCRIPCION, ncd.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ncd.descuento AS DESCUENTO, ncd.valor_descuento AS VD, p.no_gravado AS NOGRAVADO, ncd.costo_producto AS COSTO, ROUND(ncd.importe, 2) AS IMPORTE, ncd.iva AS IVA, p.exento AS EXENTO";
		$from = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ncd.egreso_id = {$egreso_id}";
		$notacreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

		$resultadoAFIP = FacturaAFIPTool()->notaCreditoAFIP($cm, $tfm, $this->model, $em, $notacreditodetalle_collection);
		if (is_array($resultadoAFIP)) {
			$this->model = new NotaCredito();
			$this->model->notacredito_id = $notacredito_id;
			$this->model->get();
			$this->model->punto_venta = $cm->punto_venta;
			$this->model->numero_factura = $resultadoAFIP['NUMFACTURA'];
			$this->model->numero_cae = $resultadoAFIP['CAE'];
			$this->model->vencimiento_cae = $resultadoAFIP['CAEFchVto'];
			$this->model->emitido_afip = 1;
			$this->model->save();
		}

		header("Location: " . URL_APP . "/notacredito/consultar/{$notacredito_id}");
	}

	function anular($arg) {
		SessionHandler()->check_session();
		
		$notacredito_id = $arg;
		$this->model->notacredito_id = $notacredito_id;
		$this->model->get();
		$egreso_id = $this->model->egreso_id;
		$cliente_id = $this->model->cliente->cliente_id;
		$facturador = $this->model->usuario_id;

		$select = "u.almacen AS ALMACEN_ID";
		$from = "usuario u";
		$where = "u.usuario_id = {$facturador}";
		$almacen_id = CollectorCondition()->get('Usuario', $where, 4, $from, $select); 
		if (is_array($almacen_id) AND !empty($almacen_id)) {
			$almacen_id = $almacen_id[0]['ALMACEN_ID'];
		} else {
			$almacen_id = $_SESSION["data-login-" . APP_ABREV]["almacen-almacen_id"];
		}

		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();
		$condicionpago_id = $em->condicionpago->condicionpago_id;

		$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
		$comprobante .= '-' . str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
		$importe = $em->importe_total;

		$select = "ncd.notacreditodetalle_id AS NCDID, ncd.codigo_producto AS CODIGO, ncd.descripcion_producto AS DESCRIPCION, ncd.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, ncd.descuento AS DESCUENTO, ncd.valor_descuento AS VD, p.no_gravado AS NOGRAVADO, ncd.costo_producto AS COSTO, ROUND(ncd.importe, 2) AS IMPORTE, ncd.iva AS IVA, p.exento AS EXENTO, ncd.producto_id AS PROID";
		$from = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ncd.notacredito_id = {$notacredito_id}";
		$notacreditodetalle_collection = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);
		$notacreditodetalle_collection = (is_array($notacreditodetalle_collection) AND !empty($notacreditodetalle_collection)) ? $notacreditodetalle_collection : array();

		foreach ($notacreditodetalle_collection as $clave=>$valor) {
			$notacreditodetalle_id = $valor['NCDID'];
			$producto_id = $valor['PROID'];
			$cantidad = $valor['CANTIDAD'];
			$codigo = $valor['CODIGO'];

			$select = "s.stock_id AS ID";
			$from = "stock s";
			$where = "s.producto_id = {$producto_id} AND s.almacen_id = {$almacen_id} ORDER BY s.stock_id DESC LIMIT 1";
			$stock_id = CollectorCondition()->get('Stock', $where, 4, $from, $select);
			$stock_id = (is_array($stock_id) AND !empty($stock_id)) ? $stock_id[0]['ID'] : 0;
			
			if ($stock_id != 0) {
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$cantidad_actual = $sm->cantidad_actual;
				$nueva_cantidad = $cantidad_actual - $cantidad;

				$sm = new Stock();
				$sm->fecha = date('Y-m-d');
				$sm->hora = date('H:i:s');
				$sm->concepto = 'Anulación Nota de Crédito';
				$sm->codigo = $codigo;
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = '-' . $cantidad;
				$sm->producto_id = $producto_id;
				$sm->almacen_id = $almacen_id;
				$sm->save();
			}

			$ncdm = new NotaCreditoDetalle();
			$ncdm->notacreditodetalle_id = $notacreditodetalle_id;
			$ncdm->delete();
		}

		if ($condicionpago_id == 1) {
			$select = "ccc.cuentacorrientecliente_id AS ID";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id} ADN ccc.cliente_id = {$cliente_id} ORDER BY ccc.egreso_id ASC";
			$cuentacorrientecliente_id = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			if (is_array($cuentacorrientecliente_id) AND !empty($cuentacorrientecliente_id)) {
				if (count($cuentacorrientecliente_id) > 1) {
					foreach ($cuentacorrientecliente_id as $clave=>$valor) {
						$cuentacorrientecliente_id = $valor['ID'];
						$cccm = new CuentaCorrienteCliente();
						$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
						$cccm->get();
						$cccm->referencia = "Comprobante venta: {$comprobante}";
						$cccm->estadomovimientocuenta = 3;
						$cccm->save();
					}

					$primer_elemento_id = $cuentacorrientecliente[0]['ID'];
					$cccm = new CuentaCorrienteCliente();
					$cccm->cuentacorrientecliente_id = $primer_elemento_id;
					$cccm->get();
					$cccm->referencia = "Comprobante venta: {$comprobante}";
					$cccm->importe = $importe;
					$cccm->estadomovimientocuenta = 1;
					$cccm->save();
				} else {
					$cccm = new CuentaCorrienteCliente();
					$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
					$cccm->get();
					$cccm->referencia = "Comprobante venta: {$comprobante}";
					$cccm->importe = $importe;
					$cccm->estadomovimientocuenta = 1;
					$cccm->save();
				}
			}
		}

		$ncm = new NotaCredito();
		$ncm->notacredito_id = $notacredito_id;
		$ncm->delete();

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}
}
?>