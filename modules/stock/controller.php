<?php
require_once "modules/stock/model.php";
require_once "modules/stock/view.php";
require_once "modules/producto/model.php";
require_once "modules/egreso/model.php";
require_once "modules/egresocomision/model.php";
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/cuentacorrienteproveedor/model.php";
require_once "modules/reporte/controller.php";


class StockController {

	function __construct() {
		$this->model = new Stock();
		$this->view = new StockView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$select = "ROUND(SUM(e.importe_total),2) AS CONTADO"; 
		$from = "egreso e";
		$where = "e.condicionpago = 2 AND e.fecha = CURDATE()";
		$sum_contado = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado = (is_array($sum_contado)) ? $sum_contado[0]['CONTADO'] : 0;
		$sum_contado = (is_null($sum_contado)) ? 0 : $sum_contado;

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$sum_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);
		if (is_array($sum_cuentacorriente)) {
			$deuda = $sum_cuentacorriente[0]['TDEUDA'];
			$ingreso = $sum_cuentacorriente[0]['TINGRESO'];
			$sum_cuentacorriente = abs(round(($deuda - $ingreso),2));
		} else {
			$sum_cuentacorriente = 0;
		}
		
		$sum_cuentacorriente = ($sum_cuentacorriente > 0.5) ? $sum_cuentacorriente : 0;
		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$cuentacorriente_total = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);
		if (is_array($cuentacorriente_total)) {
			$cuentacorriente_deuda = $cuentacorriente_total[0]['TDEUDA'];
			$cuentacorriente_deuda = (is_null($cuentacorriente_deuda)) ? 0 : $cuentacorriente_deuda;

			$cuentacorriente_ingreso = $cuentacorriente_total[0]['TINGRESO'];
			$cuentacorriente_ingreso = (is_null($cuentacorriente_ingreso)) ? 0 : $cuentacorriente_ingreso;

			$deuda_cuentacorrientecliente = abs(round(($cuentacorriente_deuda - $cuentacorriente_ingreso),2));
		} else {
			$deuda_cuentacorrientecliente = 0;
		} 

		$deuda_cuentacorrientecliente = ($deuda_cuentacorrientecliente > 0.5) ? $deuda_cuentacorrientecliente : 0;
		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN ccp.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN ccp.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrienteproveedor ccp";
		$cuentacorrienteproveedor_total = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select);
		if (is_array($cuentacorrienteproveedor_total)) {
			$cuentacorrienteproveedor_deuda = $cuentacorrienteproveedor_total[0]['TDEUDA'];
			$cuentacorrienteproveedor_deuda = (is_null($cuentacorrienteproveedor_deuda)) ? 0 : $cuentacorrienteproveedor_deuda;

			$cuentacorrienteproveedor_ingreso = $cuentacorrienteproveedor_total[0]['TINGRESO'];
			$cuentacorrienteproveedor_ingreso = (is_null($cuentacorrienteproveedor_ingreso)) ? 0 : $cuentacorrienteproveedor_ingreso;

			$deuda_cuentacorrienteproveedor = abs(round(($cuentacorrienteproveedor_deuda - $cuentacorrienteproveedor_ingreso),2));
		} else {
			$deuda_cuentacorrienteproveedor = 0;
		}

		$deuda_cuentacorrienteproveedor = ($deuda_cuentacorrienteproveedor > 0.5) ? $deuda_cuentacorrienteproveedor : 0;
		$select_producto_id = "s.producto_id AS PROD_ID";
		$from_producto_id = "stock s";
		$where_producto_id = "s.producto_id != 344";
		$groupby_producto_id = "s.producto_id";
		$productoid_collection = CollectorCondition()->get('Stock', $where_producto_id, 4, $from_producto_id, 
														   $select_producto_id, $groupby_producto_id);
		$stock_valorizado = 0;
		if ($productoid_collection == 0 || empty($productoid_collection) || !is_array($productoid_collection)) {
			$stock_collection = array();
		} else {
			$producto_ids = array();
			foreach ($productoid_collection as $producto_id) $producto_ids[] = $producto_id['PROD_ID'];
			$producto_ids = implode(',', $producto_ids);

			$select_stock = "MAX(s.stock_id) AS STOCK_ID";
			$from_stock = "stock s";
			$where_stock = "s.producto_id IN ({$producto_ids})";
			$groupby_stock = "s.producto_id";
			$stockid_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock, $groupby_stock);

			$stock_collection = array();
			foreach ($stockid_collection as $stock_id) {
				$this->model = new Stock();
				$this->model->stock_id = $stock_id['STOCK_ID'];
				$this->model->get();

				$pm = new Producto();
				$pm->producto_id = $this->model->producto_id;
				$pm->get();

				if ($pm->oculto == 0) {
					$costo_iva = (($pm->costo * $pm->iva) / 100) + $pm->costo;
					$valor_stock_producto = round(($costo_iva * $this->model->cantidad_actual),2);
					$stock_valorizado = $stock_valorizado + $valor_stock_producto;

					$class_stm = ($this->model->cantidad_actual < $pm->stock_minimo) ? 'danger' : 'success';
					$this->model->producto = $pm;
					$this->model->valor_stock = $valor_stock_producto;
					$this->model->class_stm = $class_stm;
					$this->model->mensaje_stm = $mensaje_stm;
					unset($this->model->producto_id);
					$stock_collection[] = $this->model;
				}
			}
		}

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha = CURDATE()";
		$ingreso_cuentacorriente_hoy = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_hoy = (is_array($ingreso_cuentacorriente_hoy)) ? $ingreso_cuentacorriente_hoy[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_hoy = (is_null($ingreso_cuentacorriente_hoy)) ? 0 : $ingreso_cuentacorriente_hoy;

		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha = CURDATE()";
		$egreso_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_hoy = (is_array($egreso_cuentacorrienteproveedor_hoy)) ? $egreso_cuentacorrienteproveedor_hoy[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_hoy = (is_null($egreso_cuentacorrienteproveedor_hoy)) ? 0 : $egreso_cuentacorrienteproveedor_hoy;

		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec";
		$where = "ec.fecha = CURDATE() AND ec.estadocomision IN (2,3)";
		$egreso_comision_hoy = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_hoy = (is_array($egreso_comision_hoy)) ? $egreso_comision_hoy[0]['ECOMISION'] : 0;
		$egreso_comision_hoy = (is_null($egreso_comision_hoy)) ? 0 : $egreso_comision_hoy;
		
		$ingresos_hoy = $sum_contado + $ingreso_cuentacorriente_hoy;
		$egresos_hoy = $egreso_comision_hoy + $egreso_cuentacorrienteproveedor_hoy;
		$total_facturado = round(($ingresos_hoy - $egresos_hoy),2);
		$total_facturado = ($total_facturado >= 0) ? "$" . $total_facturado : "-$" . abs($total_facturado);

		$rc = new ReporteController();
		$cajadiaria = $rc->calcula_cajadiaria();
		$total_facturado_class = ($cajadiaria >= 0) ? 'blue' : 'red';

		$array_totales = array('{estado_actual}'=>($total_facturado + $stock_valorizado) - ($deuda_cuentacorrientecliente + $deuda_cuentacorrientecliente),
							   '{total_facturado}'=>"$" . $cajadiaria,
							   '{total_facturado_class}'=>$total_facturado_class,
							   '{deuda_cuentacorrientecliente}'=>$deuda_cuentacorrientecliente,
							   '{deuda_cuentacorrienteproveedor}'=>$deuda_cuentacorrienteproveedor,
							   '{stock_valorizado}'=>$stock_valorizado,
							   '{ingreso_cuentacorrientecliente_hoy}'=>$ingreso_cuentacorriente_hoy,
							   '{egreso_cuentacorrienteproveedor_hoy}'=>$egreso_cuentacorrienteproveedor_hoy,
							   '{ingreso_contado_hoy}'=>$sum_contado,
							   '{egreso_comision_hoy}'=>$egreso_comision_hoy);
		
		$this->view->panel($stock_collection, $array_totales);
	}

	function vdr_stock() {
    	SessionHandler()->check_session();
    	
		$select_producto_id = "s.producto_id AS PROD_ID";
		$from_producto_id = "stock s";
		$where_producto_id = "s.producto_id != 344";
		$groupby_producto_id = "s.producto_id";
		$productoid_collection = CollectorCondition()->get('Stock', $where_producto_id, 4, $from_producto_id, 
														   $select_producto_id, $groupby_producto_id);
		$stock_valorizado = 0;
		if ($productoid_collection == 0 || empty($productoid_collection) || !is_array($productoid_collection)) {
			$stock_collection = array();
		} else {
			$producto_ids = array();
			foreach ($productoid_collection as $producto_id) $producto_ids[] = $producto_id['PROD_ID'];
			$producto_ids = implode(',', $producto_ids);

			$select_stock = "MAX(s.stock_id) AS STOCK_ID";
			$from_stock = "stock s";
			$where_stock = "s.producto_id IN ({$producto_ids})";
			$groupby_stock = "s.producto_id";
			$stockid_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock, $groupby_stock);

			$stock_collection = array();
			foreach ($stockid_collection as $stock_id) {
				$this->model = new Stock();
				$this->model->stock_id = $stock_id['STOCK_ID'];
				$this->model->get();

				$pm = new Producto();
				$pm->producto_id = $this->model->producto_id;
				$pm->get();

				if ($pm->oculto == 0) {
					$costo_iva = (($pm->costo * $pm->iva) / 100) + $pm->costo;
					$valor_stock_producto = round(($costo_iva * $this->model->cantidad_actual),2);
					$stock_valorizado = $stock_valorizado + $valor_stock_producto;

					$class_stm = ($this->model->cantidad_actual < $pm->stock_minimo) ? 'danger' : 'success';
					$mensaje_stm = ($this->model->cantidad_actual < $pm->stock_minimo) ? 'Reponer producto!' : '';

					$this->model->producto = $pm;
					$this->model->valor_stock = $valor_stock_producto;
					$this->model->class_stm = $class_stm;
					$this->model->mensaje_stm = $mensaje_stm;
					unset($this->model->producto_id);
					$stock_collection[] = $this->model;
				}
			}
		}
		
		$this->view->vdr_stock($stock_collection);
	}

	function ajustar_stock($arg) {
    	SessionHandler()->check_session();
    	$select_producto_id = "s.producto_id AS PROD_ID";
		$from_producto_id = "stock s";
		$groupby_producto_id = "s.producto_id";
		$productoid_collection = CollectorCondition()->get('Stock', NULL, 4, $from_producto_id, $select_producto_id,
														   $groupby_producto_id);

		if ($productoid_collection == 0 || empty($productoid_collection) || !is_array($productoid_collection)) {
			$stock_collection = array();
		} else {
			$producto_ids = array();
			foreach ($productoid_collection as $producto_id) $producto_ids[] = $producto_id['PROD_ID'];
			$producto_ids = implode(',', $producto_ids);

			$select_stock = "MAX(s.stock_id) AS STOCK_ID";
			$from_stock = "stock s";
			$where_stock = "s.producto_id IN ({$producto_ids})";
			$groupby_stock = "s.producto_id";
			$stockid_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock,
															$groupby_stock);

			$stock_collection = array();
			foreach ($stockid_collection as $stock_id) {
				$this->model = new Stock();
				$this->model->stock_id = $stock_id['STOCK_ID'];
				$this->model->get();

				$pm = new Producto();
				$pm->producto_id = $this->model->producto_id;
				$pm->get();
				$this->model->producto = $pm;
				unset($this->model->producto_id);
				$stock_collection[] = $this->model;
			}

		}

		$this->view->ajustar_stock($stock_collection, $arg);
	}

	function guardar_ajuste_stock() {
    	SessionHandler()->check_session();
		$ajuste_stock_array = $_POST['producto_cantidad'];
		$i = 0;
		
		foreach ($ajuste_stock_array as $producto_id=>$valor) {
			if ($valor != 'SV') {
				$pm = new Producto();
				$pm->producto_id = $producto_id;
				$pm->get();
				
				$sm = new Stock();
				$sm->fecha = date('Y-m-d');
				$sm->hora = date('H:i:s');
				$sm->concepto = 'Ajuste de Stock.';
				$sm->codigo = $pm->codigo;
				$sm->cantidad_actual = $valor;
				$sm->cantidad_movimiento = $valor;
				$sm->producto_id = $producto_id;
				$sm->save();
				$i = $i + 1;
			}
		}

		if ($i > 0) {
			header("Location: " . URL_APP . "/stock/ajustar_stock/2");
		} else {
			header("Location: " . URL_APP . "/stock/ajustar_stock/3");
		}
	}

	function stock_inicial($arg) {
    	SessionHandler()->check_session();
		
		$select_stock = "p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, pm.denominacion AS MARCA, p.denominacion AS PDT,
						 p.costo as COSTO, p.iva AS IVA, p.stock_ideal AS STIDEAL, p.porcentaje_ganancia AS GANANCIA, p.descuento AS DESCUENTO, 
						 p.stock_minimo AS STMINIMO, p.producto_id AS PRODUCTO_ID, ROUND(((p.costo * p.descuento)/100),2) AS VALOR_DESC,
						 ROUND((p.costo - ((p.costo * p.descuento)/100)),2) AS CD, ROUND(((p.costo - ((p.costo * p.descuento)/100)) * p.iva / 100),2) AS VALOR_IVA,
						 ROUND((((p.costo - ((p.costo * p.descuento)/100)) * p.iva / 100) + (p.costo - ((p.costo * p.descuento)/100))),2) AS COSI,
						 ROUND(((((p.costo - ((p.costo * p.descuento)/100)) * p.iva / 100) + (p.costo - ((p.costo * p.descuento)/100))) * p.porcentaje_ganancia / 100),2) AS VALOR_GANANCIA,
						 ROUND((((((p.costo - ((p.costo * p.descuento)/100)) * p.iva / 100) + (p.costo - ((p.costo * p.descuento)/100))) * p.porcentaje_ganancia / 100) + (((p.costo - ((p.costo * p.descuento)/100)) * p.iva / 100) + (p.costo - ((p.costo * p.descuento)/100)))),2) AS VALOR_VENTA";
		$from_stock = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN 
					   productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN 
					   productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_stock = "p.producto_id NOT IN (SELECT s.producto_id FROM stock s)";
		$stock_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

		if (!is_array($stock_collection)) $stock_collection = array();
		

		$this->view->stock_inicial($stock_collection, $arg);
		
	}

	function guardar_stock_inicial() {
    	SessionHandler()->check_session();
		$stock_inicial_array = $_POST['producto_cantidad'];
		
		$i = 0;
		foreach ($stock_inicial_array as $producto_id=>$valor) {
			if ($valor != 'SV') {
				$pm = new Producto();
				$pm->producto_id = $producto_id;
				$pm->get();
				
				$sm = new Stock();
				$sm->fecha = date('Y-m-d');
				$sm->hora = date('H:i:s');
				$sm->concepto = 'Ingreso. Stock Inicial';
				$sm->codigo = $pm->codigo;
				$sm->cantidad_actual = $valor;
				$sm->cantidad_movimiento = $valor;
				$sm->producto_id = $producto_id;
				$sm->save();
				$i = $i + 1;
			}
		}

		if ($i > 0) {
			header("Location: " . URL_APP . "/stock/stock_inicial/2");
		} else {
			header("Location: " . URL_APP . "/stock/stock_inicial/3");
		}
	}

	function consultar_producto($arg) {
    	SessionHandler()->check_session();
		
		$select_stock = "s.stock_id AS ID, s.fecha AS FECHA, s.hora AS HORA, CONCAT(s.cantidad_actual, pu.denominacion) AS CANT,
						 s.concepto AS CONCEPTO, CONCAT(s.cantidad_movimiento, pu.denominacion) AS MOVIMIENTO";
		$from_stock = "stock s INNER JOIN producto p ON s.producto_id = p.producto_id INNER JOIN productounidad pu 
					   ON p.productounidad = pu.productounidad_id";
		$where_stock = "s.producto_id = {$arg} ORDER BY s.stock_id DESC";
		$stock_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

		if ($stock_collection == 0 || empty($stock_collection) || !is_array($stock_collection)) {
			$stock_collection = array();
		}

		$pm = new Producto();
		$pm->producto_id = $arg;
		$pm->get();

		$this->view->consultar_producto($stock_collection, $pm);
	}
}
?>