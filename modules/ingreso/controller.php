<?php
require_once "modules/ingreso/model.php";
require_once "modules/ingreso/view.php";
require_once "modules/producto/model.php";
require_once "modules/proveedor/model.php";
require_once "modules/condicionpago/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/tipofactura/model.php";
require_once "modules/ingresodetalle/model.php";
require_once "modules/stock/model.php";
require_once "modules/cuentacorrienteproveedor/model.php";
require_once "modules/notacreditoproveedor/model.php";
require_once "modules/notacreditoproveedordetalle/model.php";


class IngresoController {

	function __construct() {
		$this->model = new Ingreso();
		$this->view = new IngresoView();
	}

	function listar($arg) {
    	SessionHandler()->check_session();
    	$select = "i.ingreso_id AS INGRESO_ID, i.fecha AS FECHA, date_format(i.fecha, '%d/%m/%Y') AS FECEMI, date_format(i.fecha_ingreso, '%d/%m/%Y') AS FECING, date_format(i.fecha_vencimiento, '%d/%m/%Y') AS FECVEN, prv.razon_social AS PROV, ci.denominacion AS CONDI,CONCAT(tf.nomenclatura, ' ', LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, i.costo_total AS TOTAL, i.costo_total_iva AS TIVA, cp.denominacion AS CP, CASE WHEN (SELECT COUNT(ccp.ingreso_id) FROM cuentacorrienteproveedor ccp WHERE ccp.ingreso_id = i.ingreso_id) > 1 THEN 'none' ELSE 'inline-block'END AS DSP_BTN_EDIT";
		$from = "ingreso i INNER JOIN proveedor prv ON i.proveedor = prv.proveedor_id INNER JOIN  condicionpago cp ON i.condicionpago = cp.condicionpago_id INNER JOIN  condicioniva ci ON i.condicioniva = ci.condicioniva_id INNER JOIN tipofactura tf ON i.tipofactura = tf.tipofactura_id ORDER BY i.fecha DESC";
		$ingreso_collection = CollectorCondition()->get('Ingreso', NULL, 4, $from, $select);
		
		foreach ($ingreso_collection as $clave=>$valor) {
			$ingreso_id = $valor["INGRESO_ID"];
			$select = "ncp.importe_total AS IMPORTETOTAL";
			$from = "notacreditoproveedor ncp";
			$where = "ncp.ingreso_id = {$ingreso_id}";
			$notacredito = CollectorCondition()->get('NotaCreditoProveedor', $where, 4, $from, $select);

			if (is_array($notacredito) AND !empty($notacredito)) {
				$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
				$ingreso_collection[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
				$ingreso_collection[$clave]['TIVA'] = $ingreso_collection[$clave]['TIVA'] - $importe_notacredito;
				$ingreso_collection[$clave]['DSP_BTN_EDIT'] = 'none';
			} else {
				$ingreso_collection[$clave]['NC_IMPORTE_TOTAL'] = 0;
			}
		}

		switch ($arg) {
			case 1:
				$array_msj = array('{mensaje}'=>'Se han ingresado productos al inventario',
								   '{display}'=>'block');
				break;
			case 2:
				$array_msj = array('{mensaje}'=>'Se ha editado un ingreso de productos al inventario',
								   '{display}'=>'block');
				break;
			default:
				$array_msj = array('{mensaje}'=>'',
								   '{display}'=>'none');
				break;
		}

		$this->view->listar($ingreso_collection, $array_msj);
	}

	function ingresar() {
    	SessionHandler()->check_session();
		$condicionpago_collection = Collector()->get('CondicionPago');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$tipofactura_collection = Collector()->get('TipoFactura');
		
		$array_ids = array(1,2,3);
		foreach ($tipofactura_collection as $clave=>$valor) {
			if (!in_array($valor->tipofactura_id, $array_ids)) unset($tipofactura_collection[$clave]);
		}

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO, p.stock_minimo AS STMINIMO, p.stock_ideal AS STIDEAL, p.costo as COSTO, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA, (((p.costo * p.porcentaje_ganancia)/100)+p.costo) AS VENTA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id LEFT JOIN productodetalle pd ON p.producto_id = pd.producto_id LEFT JOIN proveedor prv ON pd.proveedor_id = prv.proveedor_id";
		$where = "p.oculto = 0";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select, $groupby);

		$select = "p.proveedor_id AS PROVEEDOR_ID, p.razon_social AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', p.documento) AS DOCUMENTO";
		$from = "proveedor p INNER JOIN documentotipo dt ON p.documentotipo = dt.documentotipo_id";
		$where = "p.oculto = 0";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);

		$this->view->ingresar($producto_collection, $proveedor_collection, $condicionpago_collection, $condicioniva_collection, $tipofactura_collection);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		
		$ingreso_id = $arg;
		$this->model->ingreso_id = $ingreso_id;
		$this->model->get();

		$select_ingresos = "id.codigo_producto AS CODIGO, id.descripcion_producto AS DESCRIPCION, CONCAT(id.cantidad, pu.denominacion)  AS CANTIDAD, id.descuento1 AS DESCUENTO1, id.descuento2 AS DESCUENTO2, id.descuento3 AS DESCUENTO3, id.costo_producto AS COSTO, id.importe AS IMPORTE";
		$from_ingresos = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_ingresos = "id.ingreso_id = {$ingreso_id}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where_ingresos, 4, $from_ingresos, $select_ingresos);

		$select = "ccp.referencia AS REF, ccp.importe AS IMP, estadomovimientocuenta AS EST";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.ingreso_id = {$ingreso_id} AND ccp.tipomovimientocuenta = 2 ORDER BY ccp.cuentacorrienteproveedor_id DESC";
		$cuentacorrienteproveedor_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$cuentacorrienteproveedor_collection = (is_array($cuentacorrienteproveedor_collection)) ? $cuentacorrienteproveedor_collection : array();

		$select = "ncp.notacreditoproveedor_id";
		$from = "notacreditoproveedor ncp";
		$where = "ncp.ingreso_id = {$ingreso_id}";
		$notacredito = CollectorCondition()->get('NotaCreditoProveedor', $where, 4, $from, $select);
		if (is_array($notacredito) AND !empty($notacredito)) {
			$notacredito_id = $notacredito[0]['notacreditoproveedor_id'];
			$ncpm = new NotaCreditoProveedor();
			$ncpm->notacreditoproveedor_id = $ingreso_id;
			$ncpm->get();

			$select = "ncpd.codigo_producto AS CODIGO, ncpd.descripcion_producto AS DESCRIPCION, CONCAT(ncpd.cantidad, pu.denominacion) as CANTIDAD, ncpd.descuento1 AS DESCUENTO1, ncpd.descuento2 AS DESCUENTO2, ncpd.descuento3 AS DESCUENTO3, costo_producto AS COSTO, ncpd.importe AS IMPORTE";
			$from = "notacreditoproveedordetalle ncpd INNER JOIN producto p ON ncpd.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
			$where = "ncpd.ingreso_id = {$ingreso_id} AND ncpd.notacreditoproveedor_id = {$notacredito_id} ORDER BY ncpd.notacreditoproveedordetalle_id DESC";
			$notacreditoproveedordetalle_collection = CollectorCondition()->get('NotaCreditoProveedorDetalle', $where, 4, $from, $select);
		} else {
			$ncpm = null;
			$notacredito_id = 0;
			$notacreditoproveedordetalle_collection = array();
		}

		$this->view->consultar($ingresodetalle_collection, $cuentacorrienteproveedor_collection, $ncpm,  $notacreditoproveedordetalle_collection, $this->model, $notacredito_id);
	}

	function editar($arg) {
    	SessionHandler()->check_session();		
		$this->model->ingreso_id = $arg;
		$this->model->get();

		$condicionpago_collection = Collector()->get('CondicionPago');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$tipofactura_collection = Collector()->get('TipoFactura');
		
		$array_ids = array(1,2,3);
		foreach ($tipofactura_collection as $clave=>$valor) {
			if (!in_array($valor->tipofactura_id, $array_ids)) unset($tipofactura_collection[$clave]);
		}

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION,  pc.denominacion AS CATEGORIA, p.codigo AS CODIGO, p.stock_minimo AS STMINIMO, p.stock_ideal AS STIDEAL,  p.costo as COSTO, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA, (((p.costo * p.porcentaje_ganancia)/100)+p.costo) AS VENTA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id LEFT JOIN productodetalle pd ON p.producto_id = pd.producto_id LEFT JOIN proveedor prv ON pd.proveedor_id = prv.proveedor_id";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', NULL, 4, $from, $select, $groupby);

		$select = "p.proveedor_id AS PROVEEDOR_ID, p.razon_social AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', p.documento) AS DOCUMENTO";
		$from = "proveedor p INNER JOIN documentotipo dt ON p.documentotipo = dt.documentotipo_id";
		$proveedor_collection = CollectorCondition()->get('Proveedor', NULL, 4, $from, $select);

		$select_ingresos = "id.codigo_producto AS CODIGO, id.descripcion_producto AS DESCRIPCION, id.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, id.descuento1 AS DESCUENTO1, id.descuento2 AS DESCUENTO2, id.descuento3 AS DESCUENTO3, id.costo_producto AS COSTO, id.importe AS IMPORTE, id.ingresodetalle_id AS ID, id.producto_ID AS PRODUCTO";
		$from_ingresos = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_ingresos = "id.ingreso_id = {$arg}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where_ingresos, 4, $from_ingresos, $select_ingresos);

		$this->view->editar($producto_collection, $proveedor_collection, $condicionpago_collection, 
							$condicioniva_collection, $ingresodetalle_collection, $tipofactura_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		$almacen_id = $_SESSION["data-login-" . APP_ABREV]["almacen-almacen_id"];
		$punto_venta = filter_input(INPUT_POST, 'punto_venta');
		$numero_factura = filter_input(INPUT_POST, 'numero_factura');
		$fecha = filter_input(INPUT_POST, 'fecha');
		$fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
		$hora = date('H:i:s');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);
		$condicionpago = filter_input(INPUT_POST, 'condicionpago');
		$proveedor_id = filter_input(INPUT_POST, 'proveedor');
		$costo_final = filter_input(INPUT_POST, 'suma_total_iva');
		$costo_distribucion = filter_input(INPUT_POST, 'costo_distribucion');
		$valor_distribucion = ($costo_distribucion * 1 / 100) + 1;
		$opcion_actualiza_stock = filter_input(INPUT_POST, 'opcion_stock');
		$opcion_actualiza_producto = filter_input(INPUT_POST, 'opcion_producto');
		$opcion_actualiza_producto_proveedor = filter_input(INPUT_POST, 'opcion_producto_proveedor');
		
		$tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$this->model->punto_venta = $punto_venta;
		$this->model->numero_factura = $numero_factura;
		$this->model->fecha = $fecha;
		$this->model->fecha_ingreso = filter_input(INPUT_POST, 'fecha_ingreso');
		$this->model->vencimiento = filter_input(INPUT_POST, 'vencimiento');
		$this->model->fecha_vencimiento = $fecha_vencimiento;
		$this->model->hora = $hora;
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->percepcion_iva = filter_input(INPUT_POST, 'percepcion_iva');
		$this->model->impuesto_interno = filter_input(INPUT_POST, 'impuesto_interno');
		$this->model->ingresos_brutos = filter_input(INPUT_POST, 'ingresos_brutos');
		$this->model->costo_distribucion = $costo_distribucion;
		$this->model->costo_total = filter_input(INPUT_POST, 'suma_total');
		$this->model->costo_total_iva = $costo_final;
		$this->model->actualiza_precio_producto = $opcion_actualiza_producto;
		$this->model->actualiza_precio_proveedor = $opcion_actualiza_producto_proveedor;
		$this->model->actualiza_stock = $opcion_actualiza_stock;
		$this->model->proveedor = $proveedor_id;
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionpago = $condicionpago;
		$this->model->tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$this->model->save();
		$ingreso_id = $this->model->ingreso_id;

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura;
		$tfm->get();
		$nomenclatura = $tfm->nomenclatura;

		if ($condicionpago == 1) {
			$ccpm = new CuentaCorrienteProveedor();
			$ccpm->fecha = date('Y-m-d');
			$ccpm->hora = date('H:i:s');
			$ccpm->referencia = "Comprobante: {$nomenclatura} {$comprobante}";
			$ccpm->importe = $costo_final;
			$ccpm->ingreso = 0;
			$ccpm->proveedor_id = $proveedor_id;
			$ccpm->ingreso_id = $ingreso_id;
			$ccpm->ingresotipopago = null;
			$ccpm->tipomovimientocuenta = 1;
			$ccpm->estadomovimientocuenta = 1;
			$ccpm->save();
		}
		
		$ingresos_array = $_POST['ingreso'];
		$productos_ids = array();
		foreach ($ingresos_array as $ingreso) {
			$costo_base = round(($ingreso['costo_total'] / $ingreso['cantidad']),2);
			
			$productos_ids[] = $ingreso['producto_id'];
			$pm = new Producto();
			$pm->producto_id = $ingreso['producto_id'];
			$pm->get();
			$pm->costo = $costo_base;
			$iva = $pm->iva;
			$porcentaje_ganancia = $pm->porcentaje_ganancia;
			$valor_iva = ($iva * $costo_base) / 100;
			$costo_iva = $valor_iva + $costo_base;
			$valor_ganancia = ($valor_ganancia * $costo_iva) / 100;
			$precio_venta = $costo_iva + $valor_ganancia;
			$pm->precio_venta = round($precio_venta, 2);
			if ($opcion_actualiza_producto == 1) $pm->save();

			$idm = new IngresoDetalle();
			$idm->codigo_producto = $ingreso['codigo'];
			$idm->descripcion_producto = $ingreso['descripcion'];
			$idm->cantidad = $ingreso['cantidad'];
			$idm->descuento1 = $ingreso['descuento1'];
			$idm->descuento2 = $ingreso['descuento2'];
			$idm->descuento3 = $ingreso['descuento3'];
			$idm->costo_producto = $ingreso['costo'];
			$idm->importe = $ingreso['costo_total'];
			$idm->producto_id = $ingreso['producto_id'];
			$idm->ingreso_id = $ingreso_id;
			$idm->save();
		}

		$select = "id.producto_id AS PRODUCTO_ID, id.codigo_producto AS CODIGO, id.cantidad AS CANTIDAD";
		$from = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id";
		$where = "id.ingreso_id = {$ingreso_id}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where, 4, $from, $select);
		
		foreach ($ingresodetalle_collection as $ingreso) {
			$temp_producto_id = $ingreso['PRODUCTO_ID'];
			$select = "MAX(s.stock_id) AS STOCK_ID";
			$from = "stock s";
			$where = "s.producto_id = {$temp_producto_id} AND s.almacen_id = {$almacen_id}";
			$rst_stock = CollectorCondition()->get('Stock', $where, 4, $from, $select);

			if ($rst_stock == 0 || empty($rst_stock) || !is_array($rst_stock)) {
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Ingreso. Comprobante: {$comprobante}";
				$sm->codigo = $ingreso['CODIGO'];
				$sm->cantidad_actual = $ingreso['CANTIDAD'];
				$sm->cantidad_movimiento = $ingreso['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->almacen_id = 1;
				$sm->save();
			} else {
				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad + $ingreso['CANTIDAD'];
				
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Ingreso. Comprobante: {$comprobante}";
				$sm->codigo = $ingreso['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = $ingreso['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->almacen_id = 1;
				$sm->save();
			}
		}
		
		header("Location: " . URL_APP . "/ingreso/listar/1");
	}


	function actualizar() {
		SessionHandler()->check_session();
		$almacen_id = $_SESSION["data-login-" . APP_ABREV]["almacen-almacen_id"];
		$ingreso_id = filter_input(INPUT_POST, 'ingreso_id');
		$this->model->ingreso_id = $ingreso_id;
		$this->model->get();

		$punto_venta = filter_input(INPUT_POST, 'punto_venta');
		$numero_factura = filter_input(INPUT_POST, 'numero_factura');
		$fecha = filter_input(INPUT_POST, 'fecha');
		$fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
		$hora = date('H:i:s');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);
		$opcion_actualiza_stock = $this->model->actualiza_stock;
		$opcion_actualiza_producto = $this->model->actualiza_precio_producto;

		$costo_distribucion = filter_input(INPUT_POST, 'costo_distribucion');
		$proveedor = filter_input(INPUT_POST, 'proveedor');
		$condicionpago = filter_input(INPUT_POST, 'condicionpago');
		$importe_total = filter_input(INPUT_POST, 'suma_total');
		$importe_total_iva = filter_input(INPUT_POST, 'suma_total_iva');
		$tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$this->model->punto_venta = $punto_venta;
		$this->model->numero_factura = $numero_factura;
		$this->model->fecha = $fecha;
		$this->model->fecha_vencimiento = $fecha_vencimiento;
		$this->model->hora = $hora;
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->percepcion_iva = filter_input(INPUT_POST, 'percepcion_iva');
		$this->model->costo_distribucion = $costo_distribucion;
		$this->model->costo_total = $importe_total;
		$this->model->costo_total_iva = $importe_total_iva;
		$this->model->proveedor = $proveedor;
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionpago = $condicionpago;
		$this->model->tipofactura = $tipofactura;
		$this->model->save();
		
		$select = "ccp.cuentacorrienteproveedor_id AS ID";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.ingreso_id = {$ingreso_id}";
		$cuentacorrienteproveedor = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		
		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura;
		$tfm->get();
		$nomenclatura = $tfm->nomenclatura;

		if ($condicionpago == 1) {
			if (is_array($cuentacorrienteproveedor) AND !empty($cuentacorrienteproveedor)) {
				$ccpm = new CuentaCorrienteProveedor();
				$ccpm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor[0]['ID'];
				$ccpm->get();
				$ccpm->importe = $importe_total_iva;
				$ccpm->proveedor_id = $proveedor;
				$ccpm->save();
			} else {
				$ccpm = new CuentaCorrienteProveedor();
				$ccpm->fecha = date('Y-m-d');
				$ccpm->hora = date('H:i:s');
				$ccpm->referencia = "Comprobante: {$nomenclatura} {$comprobante}";
				$ccpm->importe = $importe_total_iva;
				$ccpm->ingreso = 0;
				$ccpm->proveedor_id = $proveedor;
				$ccpm->ingreso_id = $ingreso_id;
				$ccpm->ingresotipopago = null;
				$ccpm->tipomovimientocuenta = 1;
				$ccpm->estadomovimientocuenta = 1;
				
				$ccpm->save();
			}
		} else {
			if (is_array($cuentacorrienteproveedor) AND !empty($cuentacorrienteproveedor)) {
				$ccpm = new CuentaCorrienteProveedor();
				$ccpm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor[0]['ID'];
				$ccpm->delete();
			}
		}

		$select = "id.ingresodetalle_id AS ID,id.producto_id AS PRODUCTO_ID, id.codigo_producto AS CODIGO, id.cantidad AS CANTIDAD";
		$from = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id";
		$where = "id.ingreso_id = {$ingreso_id}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where, 4, $from, $select);		
		
		if (!empty($ingresodetalle_collection) AND is_array($ingresodetalle_collection)) {
			foreach ($ingresodetalle_collection as $ingresodetalle) {
				$temp_ingresodetalle_id = $ingresodetalle['ID'];
				if ($opcion_actualiza_stock == 1) {
					$temp_producto_id = $ingresodetalle['PRODUCTO_ID'];
					$select = "MAX(s.stock_id) AS STOCK_ID";
					$from = "stock s";
					$where = "s.producto_id = {$temp_producto_id} AND s.almacen_id = {$almacen_id}";
					$rst_stock = CollectorCondition()->get('Stock', $where, 4, $from, $select);
					
					$stock_id = $rst_stock[0]['STOCK_ID'];
					$sm = new Stock();
					$sm->stock_id = $stock_id;
					$sm->get();
					$ultima_cantidad = $sm->cantidad_actual;
					$nueva_cantidad = $ultima_cantidad - $ingresodetalle['CANTIDAD'];
					
					$sm = new Stock();
					$sm->fecha = $fecha;
					$sm->hora = $hora;
					$sm->concepto = "Edición Ingreso. Comprobante: {$comprobante}";
					$sm->codigo = $ingresodetalle['CODIGO'];
					$sm->cantidad_actual = $nueva_cantidad;
					$sm->cantidad_movimiento = -$ingresodetalle['CANTIDAD'];
					$sm->producto_id = $temp_producto_id;
					$sm->almacen_id = 1;
					$sm->save();
				}

				$idm = new IngresoDetalle();
				$idm->ingresodetalle_id = $temp_ingresodetalle_id;
				$idm->delete();
			}
		}

		$ingresos_array = $_POST['ingreso'];
		foreach ($ingresos_array as $ingreso) {
			$costo_base = round(($ingreso['costo_total'] / $ingreso['cantidad']),2);
			
			$pm = new Producto();
			$pm->producto_id = $ingreso['producto_id'];
			$pm->get();
			$pm->costo = $costo_base;
			if ($opcion_actualiza_producto == 1) $pm->save();

			$idm = new IngresoDetalle();
			$idm->codigo_producto = $ingreso['codigo'];
			$idm->descripcion_producto = $ingreso['descripcion'];
			$idm->cantidad = $ingreso['cantidad'];
			$idm->descuento1 = $ingreso['descuento1'];
			$idm->descuento2 = $ingreso['descuento2'];
			$idm->descuento3 = $ingreso['descuento3'];
			$idm->costo_producto = $ingreso['costo'];
			$idm->importe = $ingreso['costo_total'];
			$idm->producto_id = $ingreso['producto_id'];
			$idm->ingreso_id = $ingreso_id;
			$idm->save();
		}
		
		$select = "id.producto_id AS PRODUCTO_ID, id.codigo_producto AS CODIGO, id.cantidad AS CANTIDAD";
		$from = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id";
		$where = "id.ingreso_id = {$ingreso_id}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where, 4, $from, $select);	

		foreach ($ingresodetalle_collection as $ingreso) {
			$temp_producto_id = $ingreso['PRODUCTO_ID'];
			$select = "MAX(s.stock_id) AS STOCK_ID";
			$from = "stock s";
			$where = "s.producto_id = {$temp_producto_id} AND s.almacen_id = {$almacen_id}";
			$rst_stock = CollectorCondition()->get('Stock', $where, 4, $from, $select);

			if ($rst_stock == 0 || empty($rst_stock) || !is_array($rst_stock)) {
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Edición Ingreso. Comprobante: {$comprobante}";
				$sm->codigo = $ingreso['CODIGO'];
				$sm->cantidad_actual = $ingreso['CANTIDAD'];
				$sm->cantidad_movimiento = $ingreso['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->almacen_id = 1;
				$sm->save();
			} else {
				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad + $ingreso['CANTIDAD'];
				
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Edición Ingreso. Comprobante: {$comprobante}";
				$sm->codigo = $ingreso['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = $ingreso['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->almacen_id = 1;
				$sm->save();
			}
		}
		

		header("Location: " . URL_APP . "/ingreso/listar/2");
	}

	function actualizar_abreviado() {
		SessionHandler()->check_session();

		$ingreso_id = filter_input(INPUT_POST, 'ingreso_id');
		$punto_venta = filter_input(INPUT_POST, 'punto_venta');
		$numero_factura = filter_input(INPUT_POST, 'numero_factura');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);
		$proveedor = filter_input(INPUT_POST, 'proveedor');
		$tipofactura = filter_input(INPUT_POST, 'tipofactura');

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura;
		$tfm->get();
		$nomenclatura = $tfm->nomenclatura;

		$this->model->ingreso_id = $ingreso_id;
		$this->model->get();
		$condicionpago = $this->model->condicionpago;

		$this->model->punto_venta = $punto_venta;
		$this->model->numero_factura = $numero_factura;
		$this->model->proveedor = $proveedor;
		$this->model->tipofactura = $tipofactura;
		$this->model->save();

		if ($condicionpago == 1) {
			$select = "ccp.cuentacorrienteproveedor_id AS ID";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id}";
			$cuentacorrienteproveedor = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		
			foreach ($cuentacorrienteproveedor as $clave=>$valor) {
				$ccpm = new CuentaCorrienteProveedor();
				$ccpm->cuentacorrienteproveedor_id = $valor['ID'];
				$ccpm->get();
				$ccpm->referencia = "Comprobante: {$nomenclatura} {$comprobante}";
				$ccpm->proveedor_id = $proveedor;
				$ccpm->save();
			}			
		}
		
		header("Location: " . URL_APP . "/ingreso/consultar/{$ingreso_id}");
	}

	function reingreso($arg) {
    	SessionHandler()->check_session();
		
		$this->model->ingreso_id = $arg;
		$this->model->get();
		
		$select = "id.codigo_producto AS CODIGO, id.descripcion_producto AS DESCRIPCION, id.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, id.descuento1 AS DESCUENTO1, id.descuento2 AS DESCUENTO2,  id.descuento3 AS DESCUENTO3, id.costo_producto AS COSTO, id.importe AS IMPORTE,  id.ingresodetalle_id AS INGRESODETALLEID, id.producto_id AS PRODUCTO";
		$from = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "id.ingreso_id = {$arg}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where, 4, $from, $select);
		$this->view->reingreso($ingresodetalle_collection, $this->model);
	}

	function guardar_nota_credito() {
		SessionHandler()->check_session();
		$almacen_id = $_SESSION["data-login-" . APP_ABREV]["almacen-almacen_id"];		
		$ingreso_id = filter_input(INPUT_POST, 'ingreso_id');
		$suma_total = filter_input(INPUT_POST, 'suma_total');
		$suma_total_iva = filter_input(INPUT_POST, 'suma_total_iva');
		
		$this->model->ingreso_id = $ingreso_id;
		$this->model->get();
		$iva_ingreso = $this->model->iva;
		$percepcioniva_ingreso = $this->model->percepcion_iva;
		$opcion_actualiza_stock = $this->model->actualiza_stock;
		$opcion_actualiza_producto = $this->model->actualiza_precio_producto;

		$condicionpago_ingreso = $this->model->condicionpago->condicionpago_id;
		$fecha = date('Y-m-d');
		$hora = date('H:i:s');
		$punto_venta = $this->model->punto_venta;
		$numero_factura = $this->model->numero_factura;

		$ncpm = new NotaCreditoProveedor();
		$ncpm->ingreso_id = $ingreso_id;
		$ncpm->eliminar_nota_credito();

		$ncpm = new NotaCreditoProveedor();
		$ncpm->punto_venta = $punto_venta;
		$ncpm->numero_factura = $numero_factura;
		$ncpm->fecha = $fecha;
		$ncpm->hora = $hora;
		$ncpm->subtotal = $suma_total;
		$ncpm->importe_total = $suma_total_iva;
		$ncpm->ingreso_id = $ingreso_id;
		$ncpm->save();
		$notacreditoproveedor_id = $ncpm->notacreditoproveedor_id;
		
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);

		$select = "ncpd.notacreditoproveedordetalle_id AS ID, ncpd.producto_id AS PRODUCTO_ID, ncpd.codigo_producto AS CODIGO, ncpd.cantidad AS CANTIDAD";
		$from = "notacreditoproveedordetalle ncpd INNER JOIN producto p ON ncpd.producto_id = p.producto_id";
		$where = "ncpd.ingreso_id = {$ingreso_id}";
		$notascreditodetalle_collection = CollectorCondition()->get('NotaCreditoProveedorDetalle', $where, 4, $from, $select);		

		if (!empty($notascreditodetalle_collection) AND is_array($notascreditodetalle_collection)) {
			foreach ($notascreditodetalle_collection as $notacredito) {
				$temp_notacreditoproveedor_id = $notacredito['ID'];
				if ($opcion_actualiza_stock == 1) {
					$temp_producto_id = $notacredito['PRODUCTO_ID'];
					$select = "MAX(s.stock_id) AS STOCK_ID";
					$from = "stock s";
					$where = "s.producto_id = {$temp_producto_id} AND s.almacen_id = {$almacen_id}";
					$rst_stock = CollectorCondition()->get('Stock', $where, 4, $from, $select);
					
					$stock_id = $rst_stock[0]['STOCK_ID'];
					$sm = new Stock();
					$sm->stock_id = $stock_id;
					$sm->get();
					$ultima_cantidad = $sm->cantidad_actual;
					$nueva_cantidad = $ultima_cantidad + $notacredito['CANTIDAD'];
					
					$sm = new Stock();
					$sm->fecha = $fecha;
					$sm->hora = $hora;
					$sm->concepto = "Edición Nota de Crédito. Comprobante: {$comprobante}";
					$sm->codigo = $notacredito['CODIGO'];
					$sm->cantidad_actual = $nueva_cantidad;
					$sm->cantidad_movimiento = $notacredito['CANTIDAD'];
					$sm->producto_id = $temp_producto_id;
					$sm->almacen_id = 1;
					$sm->save();
				}

				$ncpdm = new NotaCreditoProveedorDetalle();
				$ncpdm->notacreditoproveedordetalle_id = $temp_notacreditoproveedor_id;
				$ncpdm->delete();
			}
		}

		$ingresos_array = $_POST['ingreso'];
		foreach ($ingresos_array as $ingreso) {
			$ncpdm = new NotaCreditoProveedorDetalle();
			$ncpdm->codigo_producto = $ingreso['codigo'];
			$ncpdm->descripcion_producto = $ingreso['descripcion'];
			$ncpdm->cantidad = $ingreso['cantidad'];
			$ncpdm->descuento1 = $ingreso['descuento1'];
			$ncpdm->descuento2 = $ingreso['descuento2'];
			$ncpdm->descuento3 = $ingreso['descuento3'];
			$ncpdm->costo_producto = $ingreso['costo'];
			$ncpdm->importe = $ingreso['costo_total'];
			$ncpdm->iva = $iva_ingreso;
			$ncpdm->percepcion_iva = $percepcioniva_ingreso;
			$ncpdm->producto_id = $ingreso['producto_id'];
			$ncpdm->ingreso_id = $ingreso_id;
			$ncpdm->notacreditoproveedor_id = $notacreditoproveedor_id;
			$ncpdm->save();
		}

		if ($opcion_actualiza_stock == 1) {
			$select = "ncpd.producto_id AS PRODUCTO_ID, ncpd.codigo_producto AS CODIGO, ncpd.cantidad AS CANTIDAD";
			$from = "notacreditoproveedordetalle ncpd INNER JOIN producto p ON ncpd.producto_id = p.producto_id";
			$where = "ncpd.ingreso_id = {$ingreso_id} AND ncpd.notacreditoproveedor_id = {$notacreditoproveedor_id}";
			$notascreditodetalle_collection = CollectorCondition()->get('NotaCreditoProveedorDetalle', $where, 4, $from, $select);	

			foreach ($notascreditodetalle_collection as $notacredito) {
				$temp_producto_id = $notacredito['PRODUCTO_ID'];
				$select = "MAX(s.stock_id) AS STOCK_ID";
				$from = "stock s";
				$where = "s.producto_id = {$temp_producto_id} AND s.almacen_id = {$almacen_id}";
				$rst_stock = CollectorCondition()->get('Stock', $where, 4, $from, $select);

				$stock_id = $rst_stock[0]['STOCK_ID'];
				$sm = new Stock();
				$sm->stock_id = $stock_id;
				$sm->get();
				$ultima_cantidad = $sm->cantidad_actual;
				$nueva_cantidad = $ultima_cantidad - $notacredito['CANTIDAD'];
				
				$sm = new Stock();
				$sm->fecha = $fecha;
				$sm->hora = $hora;
				$sm->concepto = "Nota de Crédito. Comprobante: {$comprobante}";
				$sm->codigo = $notacredito['CODIGO'];
				$sm->cantidad_actual = $nueva_cantidad;
				$sm->cantidad_movimiento = -$notacredito['CANTIDAD'];
				$sm->producto_id = $temp_producto_id;
				$sm->almacen_id = 1;
				$sm->save();				
			}
		}

		if ($condicionpago_ingreso == 1) {
			$select = "ccp.cuentacorrienteproveedor_id CCPID";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id}";
			$cuentacorrienteproveedor_id = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
			$cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id[0]['CCPID'];
			$ccpm = new CuentaCorrienteProveedor();
			$ccpm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
			$ccpm->get();
			$ccpm->referencia = 'Ajuste por NC. ' . $ccpm->referencia;
			$ccpm->importe = $ccpm->importe - $suma_total_iva;
			$ccpm->save();
		}
		
		header("Location: " . URL_APP . "/ingreso/consultar/{$ingreso_id}");
	}

	function traer_formulario_producto_ajax($arg) {
		$pm = new Producto();
		$pm->producto_id = $arg;
		$pm->get();
		$this->view->traer_formulario_producto_ajax($pm);
	}

	function traer_cantidad_bulto_ajax($arg) {
		$ids = explode('@', $arg);
		$producto_id = $ids[0];
		$bultos = $ids[1];
		$pm = new Producto();
		$pm->producto_id = $producto_id;
		$pm->get();

		$unidad_bulto = $pm->unidad_bulto;
		$cantidad = $unidad_bulto * $bultos;
		print $cantidad;		
	}

	function traer_formulario_editar_producto_ajax($arg) {
		$ids = explode('@', $arg);
		$ingreso_id = $ids[0];
		$producto_id = $ids[1];

		$pm = new Producto();
		$pm->producto_id = $producto_id;
		$pm->get();

		$select = "id.costo_producto AS COSTO";
		$from = "ingresodetalle id";
		$where = "id.producto_id = {$producto_id} AND id.ingreso_id = {$ingreso_id}";
		$old_costo = CollectorCondition()->get('IngresoDetalle', $where, 4, $from, $select);
		
		$this->view->traer_formulario_editar_producto_ajax($pm, $old_costo);
	}

	function traer_formulario_reingreso_producto_ajax($arg) {
		$idm = new IngresoDetalle();
		$idm->ingresodetalle_id = $arg;
		$idm->get();
		$producto_id = $idm->producto_id;
		
		$pm = new Producto();
		$pm->producto_id = $producto_id;
		$pm->get();
		
		$this->view->traer_formulario_reingreso_producto_ajax($pm, $idm);
	}

	function traer_costo_producto_ajax($arg) {
		$pm = new Producto();
		$pm->producto_id = $arg;
		$pm->get();
		$costo = $pm->costo;
		print $costo;
	}

	function traer_descripcion_proveedor_ajax($arg) {
		$pm = new Proveedor();
		$pm->proveedor_id = $arg;
		$pm->get();
		$denominacion =$pm->documentotipo->denominacion . ' ' . $pm->documento . ' - ' . $pm->razon_social;
		print $denominacion;
	}

	function traer_formulario_editar_ingresar_ajax($arg) {
		$ingreso_id = $arg;
		$this->model->ingreso_id = $ingreso_id;
		$this->model->get();

		$select = "p.proveedor_id AS ID, p.razon_social AS DENOMINACION, CONCAT(dt.denominacion, ' ', p.documento) AS DOCUMENTO";
		$from = "proveedor p INNER JOIN documentotipo dt ON p.documentotipo = dt.documentotipo_id";
		$where = "p.oculto = 0";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);
		$tipofactura_collection = Collector()->get('TipoFactura');

		$this->view->traer_formulario_editar_ingresar_ajax($this->model, $proveedor_collection, $tipofactura_collection);
	}

	function traer_proveedor_tipofactura_ajax($arg) {
		$pm = new Proveedor();
		$pm->proveedor_id = $arg;
		$pm->get();

		$condicioniva_id = $pm->condicioniva->condicioniva_id;
		switch ($condicioniva_id) {
			case 1:
				$tipofactura_id = 1;
				break;
			case 2:
				$tipofactura_id = 3;
				break;
			case 3:
				$tipofactura_id = 2;
				break;
			default:
				$tipofactura_id = 2;
				break;
		}

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura_id;
		$tfm->get();
		$tipofactura = $tfm->tipofactura_id . '@';
		$tipofactura .= $tfm->nomenclatura . ' - ' . $tfm->denominacion;
		print $tipofactura;
	}

	function imprimir_boleta_testigo($arg) {
		SessionHandler()->check_session();		
		require_once 'tools/boletaPDFTool.php';
		$ingreso_id = $arg;
		$this->model->ingreso_id = $ingreso_id;
		$this->model->get();

		$select = "id.descripcion_producto AS DESCRIPCION, CONCAT(id.cantidad, pu.denominacion)  AS CANTIDAD, id.costo_producto AS COSTO, id.importe AS IMPORTE, id.descuento1 AS DESCUENTO1, id.descuento2 AS DESCUENTO2, id.descuento3 AS DESCUENTO3";
		$from = "ingresodetalle id INNER JOIN producto p ON id.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "id.ingreso_id = {$ingreso_id}";
		$ingresodetalle_collection = CollectorCondition()->get('IngresoDetalle', $where, 4, $from, $select);

		$facturaPDFHelper = new BoletaPDF();
		$facturaPDFHelper->boleta_testigo($ingresodetalle_collection, $this->model);
	}
}
?>