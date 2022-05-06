<?php
require_once "modules/reporte/view.php";
require_once "modules/stock/model.php";
require_once "modules/producto/model.php";
require_once "modules/productomarca/model.php";
require_once "modules/egreso/model.php";
require_once "modules/egresodetalle/model.php";
require_once "modules/egresocomision/model.php";
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/cuentacorrienteproveedor/model.php";
require_once "modules/gasto/model.php";
require_once "modules/gastocategoria/model.php";
require_once "modules/configuracionbalance/model.php";
require_once "modules/notacredito/model.php";
require_once "modules/notacreditodetalle/model.php";
require_once "modules/proveedor/model.php";
require_once "modules/vendedor/model.php";
require_once "modules/cajadiaria/model.php";


class ReporteController {

	function __construct() {
		$this->stock = new Stock();
		$this->producto = new Producto();
		$this->view = new ReporteView();
	}

	function home() {
	 SessionHandler()->check_session();

	 $this->view->home();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$fecha_sys = strtotime(date('Y-m-d'));
		$periodo_minimo = date("Ym", strtotime("-6 month", $fecha_sys));
    	$periodo_actual = date('Ym');
    	$primer_dia_mes = date('Y-m') . '-01'; 
		$fecha_sys1 = date('Y-m-d');

    	$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega esen ON ee.estadoentrega = esen.estadoentrega_id";
		$where = "e.condicionpago = 2 AND ee.fecha = CURDATE() AND esen.estadoentrega_id = 4";
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

		$select = "ccp.proveedor_id AS PID, p.razon_social AS PROVEEDOR, (SELECT ROUND(SUM(dccp.importe),2) FROM
    			   cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA,
				   (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND
				   dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.proveedor_id";
		$cuentacorrienteproveedor_total = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select, $groupby);
		if (is_array($cuentacorrienteproveedor_total)) {
			$deuda_cuentacorrienteproveedor = 0;
			foreach ($cuentacorrienteproveedor_total as $clave=>$valor) {
				$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
				$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
				$cuenta = round(($ingreso - $deuda),2);
				$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
				$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
				$deuda_cuentacorrienteproveedor = $deuda_cuentacorrienteproveedor + $cuenta;

			}
		} else {
			$deuda_cuentacorrienteproveedor = 0;
		}

		$deuda_cuentacorrienteproveedor = abs($deuda_cuentacorrienteproveedor);
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
				$this->stock = new Stock();
				$this->stock->stock_id = $stock_id['STOCK_ID'];
				$this->stock->get();

				$this->producto = new Producto();
				$this->producto->producto_id = $this->stock->producto_id;
				$this->producto->get();

				if ($this->producto->oculto == 0) {
					$costo_iva = (($this->producto->costo * $this->producto->iva) / 100) + $this->producto->costo;
					$valor_stock_producto = round(($costo_iva * $this->stock->cantidad_actual),2);
					$stock_valorizado = $stock_valorizado + $valor_stock_producto;

					$this->stock->producto = $this->producto;
					$this->stock->valor_stock = $valor_stock_producto;
					unset($this->stock->producto_id);
					if ($this->stock->cantidad_actual < $this->producto->stock_minimo) {
						$stock_collection[] = $this->stock;
					}
				}
			}
		}

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha = '{$fecha_sys1}'";
		$ingreso_cuentacorriente_hoy = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_hoy = (is_array($ingreso_cuentacorriente_hoy)) ? $ingreso_cuentacorriente_hoy[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_hoy = (is_null($ingreso_cuentacorriente_hoy)) ? 0 : $ingreso_cuentacorriente_hoy;

		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha = '{$fecha_sys1}'";
		$egreso_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_hoy = (is_array($egreso_cuentacorrienteproveedor_hoy)) ? $egreso_cuentacorrienteproveedor_hoy[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_hoy = (is_null($egreso_cuentacorrienteproveedor_hoy)) ? 0 : $egreso_cuentacorrienteproveedor_hoy;

		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec";
		$where = "ec.fecha = '{$fecha_sys1}' AND ec.estadocomision IN (2,3)";
		$egreso_comision_hoy = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_hoy = (is_array($egreso_comision_hoy)) ? $egreso_comision_hoy[0]['ECOMISION'] : 0;
		$egreso_comision_hoy = (is_null($egreso_comision_hoy)) ? 0 : $egreso_comision_hoy;

		$select = "e.egreso_id AS EGRESO_ID, e.importe_total AS IMPORTETOTAL";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
				 egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN
				 egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "e.fecha = '{$fecha_sys1}'";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$suma_ingresos_hoy = 0;
		$suma_notacredito_hoy = 0;
		$total_facturacion_hoy = 0;
		if (is_array($egresos_collection) AND !empty($egresos_collection)) {
			foreach ($egresos_collection as $clave=>$valor) {
				$egreso_importe_total = $egresos_collection[$clave]['IMPORTETOTAL'];

				$egreso_id = $valor['EGRESO_ID'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$suma_notacredito_hoy = $suma_notacredito_hoy + $importe_notacredito;
				}

				$suma_ingresos_hoy = $suma_ingresos_hoy + $egreso_importe_total;
			}
		}

		$total_facturacion_hoy = $suma_ingresos_hoy - $suma_notacredito_hoy;

		$select = "e.egreso_id AS EGRESO_ID, e.subtotal AS SUBTOTAL, e.importe_total AS IMPORTETOTAL, e.condicionpago AS CONDPAGO";
		$from = "egreso e";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$suma_importe_ventas_cc = 0;
		$suma_importe_ventas_cont = 0;
		if (is_array($egreso_collection) AND !empty($egreso_collection)) {
			foreach ($egreso_collection as $clave=>$valor) {
				$egreso_id = $valor['EGRESO_ID'];
				$importe_total = $valor['IMPORTETOTAL'];
				$condicionpago = $valor['CONDPAGO'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$importe_total = $importe_total - $importe_notacredito;
				}

				if ($condicionpago == 1) {
					$suma_importe_ventas_cc = $suma_importe_ventas_cc + $importe_total;
				} elseif ($condicionpago == 2) {
					$suma_importe_ventas_cont = $suma_importe_ventas_cont + $importe_total;
				}
			}
		}

		$ingresos_hoy = $sum_contado + $ingreso_cuentacorriente_hoy;
		$egresos_hoy = $egreso_comision_hoy + $egreso_cuentacorrienteproveedor_hoy;
		$total_facturado = $this->calcula_cajadiaria();

		$total_facturado_class = ($total_facturado >= 0) ? 'blue' : 'red';
		$total_facturado_int = ($total_facturado >= 0) ? $total_facturado : "-" . abs($total_facturado);
		$total_facturado = ($total_facturado >= 0) ? "$" . $total_facturado : "-$" . abs($total_facturado);

		$select = "ROUND(SUM(CASE WHEN e.tipofactura = 1 THEN e.importe_total WHEN e.tipofactura = 3 THEN e.importe_total ELSE 0
        END),2) AS BLANCO, ROUND(SUM(CASE WHEN e.tipofactura = 2 THEN e.importe_total ELSE 0 END),2) AS NEGRO, ROUND(SUM(e.importe_total), 2) AS TOTAL";
		$from = "egreso e";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
		$ventas_tipofactura = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$ventas_tipofactura = (is_array($ventas_tipofactura) AND !empty($ventas_tipofactura)) ? $ventas_tipofactura : array(array('BLANCO'=>0, 'NEGRO'=>0, 'TOTAL'=>0));

		$select = "ROUND(SUM(CASE WHEN nc.tipofactura = 4 THEN nc.importe_total WHEN nc.tipofactura = 5 THEN nc.importe_total ELSE 0 END),2) AS BLANCO, ROUND(SUM(CASE WHEN nc.tipofactura = 6 THEN nc.importe_total ELSE 0 END),2) AS NEGRO, ROUND(SUM(nc.importe_total), 2) AS TOTAL";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
		$notacredito_tipofactura = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$notacredito_tipofactura = (is_array($notacredito_tipofactura) AND !empty($notacredito_tipofactura)) ? $notacredito_tipofactura : array(array('BLANCO'=>0, 'NEGRO'=>0, 'TOTAL'=>0));

		$suma_importe_factura = $ventas_tipofactura[0]['BLANCO'] - $notacredito_tipofactura[0]['BLANCO'];
		$suma_importe_remito = $ventas_tipofactura[0]['NEGRO'] - $notacredito_tipofactura[0]['NEGRO'];

		$select = "i.ingreso_id AS ID, i.costo_total_iva AS IMPORTE_TOTAL, i.tipofactura AS TIPOFACTURA";
		$from = "ingreso i";
		$where = "i.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
		$ingreso_tipofactura = CollectorCondition()->get('Ingreso', $where, 4, $from, $select);
		$ingreso_tipofactura = (is_array($ingreso_tipofactura) AND !empty($ingreso_tipofactura)) ? $ingreso_tipofactura : array();
	
		$suma_importe_compras_factura = 0;
		$suma_importe_compras_remito = 0;
		foreach ($ingreso_tipofactura as $clave=>$valor) {
			$ingreso_id = $valor['ID'];
			$compra_total = $valor['IMPORTE_TOTAL'];
			$compra_tipofactura = $valor['TIPOFACTURA'];

			$select = "ncp.importe_total AS IMPORTE_TOTAL";
			$from = "notacreditoproveedor ncp";
			$where = "ncp.ingreso_id = {$ingreso_id}";
			$notacreditoproveedor_tipofactura = CollectorCondition()->get('NotaCreditoProveedor', $where, 4, $from, $select);
			if (is_array($notacreditoproveedor_tipofactura) AND !empty($notacreditoproveedor_tipofactura)) {
				$notacreditoproveedor_total = $notacreditoproveedor_tipofactura[0]['IMPORTE_TOTAL'];
				$compra_total = $compra_total - $notacreditoproveedor_total;
			}

			switch ($compra_tipofactura) {
				case 1:
					$suma_importe_compras_factura = $suma_importe_compras_factura + $compra_total;
					break;
				case 2:
					$suma_importe_compras_remito = $suma_importe_compras_remito + $compra_total;
					break;
				case 3:
					$suma_importe_compras_factura = $suma_importe_compras_factura + $compra_total;
					break;
			}

		}

		$suma_total_compras = $suma_importe_compras_factura + $suma_importe_compras_remito;
		
		$array_totales = array('{periodo_actual}'=>$periodo_actual,
							   '{estado_actual}'=>($total_facturado_int + $stock_valorizado) - ($deuda_cuentacorrientecliente + $deuda_cuentacorrienteproveedor),
							   '{total_facturado}'=>$total_facturado,
							   '{total_facturado_class}'=>$total_facturado_class,
							   '{deuda_cuentacorrientecliente}'=>$deuda_cuentacorrientecliente,
							   '{deuda_cuentacorrienteproveedor}'=>$deuda_cuentacorrienteproveedor,
							   '{stock_valorizado}'=>$stock_valorizado,
							   '{ingreso_cuentacorrientecliente_hoy}'=>$ingreso_cuentacorriente_hoy,
							   '{egreso_cuentacorrienteproveedor_hoy}'=>$egreso_cuentacorrienteproveedor_hoy,
							   '{ingreso_contado_hoy}'=>$sum_contado,
							   '{egreso_comision_hoy}'=>$egreso_comision_hoy,
							   '{suma_importe_ventas_cc}'=>$suma_importe_ventas_cc,
							   '{suma_importe_ventas_cont}'=>$suma_importe_ventas_cont,
							   '{suma_total_ventas}'=>$suma_importe_ventas_cont + $suma_importe_ventas_cc,
							   '{suma_importe_factura}'=>$suma_importe_factura,
							   '{suma_importe_remito}'=>$suma_importe_remito,
							   '{suma_importe_compras_factura}'=>$suma_importe_compras_factura,
							   '{suma_importe_compras_remito}'=>$suma_importe_compras_remito,
							   '{suma_total_compras}'=>$suma_total_compras,
							   '{suma_ingresos_hoy}'=>$suma_ingresos_hoy,
							   '{suma_notacredito_hoy}'=>$suma_notacredito_hoy,
							   '{total_facturacion_hoy}'=>$total_facturacion_hoy);

		$select = "ed.codigo_producto AS COD, ed.descripcion_producto AS PRODUCTO, ROUND(SUM(ed.importe),2) AS IMPORTE,
				   ROUND(SUM(ed.cantidad),2) AS CANTIDAD, ed.producto_id AS PRID";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";

		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.importe),2) DESC";
		$sum_importe_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.cantidad),2) DESC";
		$sum_cantidad_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(ncd.importe),2) AS IMPORTE, ROUND(SUM(ncd.cantidad),2) AS CANTIDAD";
		$from = "notacreditodetalle ncd INNER JOIN notacredito nc ON ncd.notacredito_id = nc.notacredito_id";
		if (is_array($sum_importe_producto) AND !empty($sum_importe_producto)) {
			foreach ($sum_importe_producto as $clave=>$valor) {
				$tmp_producto_id = $valor["PRID"];
				$where = "ncd.producto_id = {$tmp_producto_id} AND nc.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
				$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

				if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
					$nuevo_valor_importe = $sum_importe_producto[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
					$nuevo_valor_cantidad = $sum_importe_producto[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
				} else {
					$nuevo_valor_importe = 0;
					$nuevo_valor_cantidad = 0;
				}

				$sum_importe_producto[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
				$sum_importe_producto[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
			}
		}

		if (is_array($sum_cantidad_producto) AND !empty($sum_cantidad_producto)) {
			foreach ($sum_cantidad_producto as $clave=>$valor) {
				$tmp_producto_id = $valor["PRID"];
				$where = "ncd.producto_id = {$tmp_producto_id} AND nc.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
				$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
				if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
					$nuevo_valor_importe = $sum_cantidad_producto[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
					$nuevo_valor_cantidad = $sum_cantidad_producto[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
				}

				$sum_cantidad_producto[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
				$sum_cantidad_producto[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
			}
		}

		$select = "date_format(e.fecha, '%Y%m') AS PERIODO, ROUND(SUM(CASE WHEN e.condicionpago = 1 THEN e.importe_total ELSE 0 END),2) AS SUMCC,
				   ROUND(SUM(CASE WHEN e.condicionpago = 2 THEN e.importe_total ELSE 0 END),2) AS SUMCONT ";
		$from = "egreso e";
		$where = "date_format(e.fecha, '%Y%m') BETWEEN '{$periodo_minimo}' AND '{$periodo_actual}'";
		$groupby = "date_format(e.fecha, '%Y%m') ORDER BY date_format(e.fecha, '%Y%m') ASC LIMIT 7";
		$sum_semestre_cuentas = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from = "vendedor v ORDER BY CONCAT(v.apellido, ' ', v.nombre) ASC";
		$vendedor_collection = CollectorCondition()->get('Egreso', NULL, 4, $from, $select);

		$select = "gc.denominacion AS DENOMINACION, SUM(g.importe) AS IMPORTE";
		$from = "gasto g INNER JOIN	gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "g.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys1}'";
		$group_by = "gc.gastocategoria_id";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select, $group_by);

		// BOLETAS CON VENCIMIENTO
		$select = "date_format(i.fecha, '%d/%m/%Y') AS FECHA, date_format(i.fecha_vencimiento, '%d/%m/%Y') AS VENCIMIENTO, ccp.ingreso_id AS IID, CONCAT(LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, ccp.proveedor_id AS PROID, p.razon_social AS PROVEEDOR, i.fecha_vencimiento AS FECVEN, i.costo_total_iva AS IMPORTE, ccp.cuentacorrienteproveedor_id AS CCPID, ccp.ingresotipopago AS ING_TIP_PAG";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id INNER JOIN ingreso i ON ccp.ingreso_id = i.ingreso_id";
		$where = "ccp.estadomovimientocuenta != 4 GROUP BY ccp.ingreso_id";
		$cuentacorrienteproveedor_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$cuentacorrienteproveedor_collection = (is_array($cuentacorrienteproveedor_collection) AND !empty($cuentacorrienteproveedor_collection)) ? $cuentacorrienteproveedor_collection : array();

		$ingreso_ids = array();
		foreach ($cuentacorrienteproveedor_collection as $clave=>$valor) {
			$temp_cuentacorrienteproveedor_id = $valor['CCPID'];
			$ingreso_id = $valor['IID'];
			$ingresotipopago_id = $valor['ING_TIP_PAG'];
			if (!in_array($ingreso_id, $ingreso_ids)) $ingreso_ids[] = $ingreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) -
				  	  (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE,
					  IF (ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) -
					  (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

			$cuentacorrienteproveedor_collection[$clave]['BALANCE'] = $new_balance;
		}

		$this->view->panel($stock_collection, $array_totales, $sum_importe_producto, $sum_cantidad_producto, $sum_semestre_cuentas,
						   $vendedor_collection, $gasto_collection, $cuentacorrienteproveedor_collection);
	}

	function vdr_panel() {
    	SessionHandler()->check_session();
    	$fecha_sys = date('Y-m-d');
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
    	$select = "uv.usuario_id AS USUID, uv.vendedor_id AS VENID";
		$from = "usuariovendedor uv";
		$where = "uv.usuario_id = {$usuario_id}";
		$usuariovendedor_id = CollectorCondition()->get('UsuarioVendedor', $where, 4, $from, $select);
		$vendedor_id = $usuariovendedor_id[0]['VENID'];

    	$select = "pv.pedidovendedor_id AS PEDVENID, CONCAT(date_format(pv.fecha, '%d/%m/%Y'), ' ', LEFT(pv.hora,5)) AS FECHA,
    			   UPPER(cl.razon_social) AS CLIENTE, UPPER(cl.nombre_fantasia) AS FANTASIA, pv.subtotal AS SUBTOTAL,
    			   pv.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR,
    			   CASE pv.estadopedido WHEN 1 THEN 'inline-block' WHEN 2 THEN 'none' WHEN 3 THEN 'none' END AS DSPBTN,
    			   CASE pv.estadopedido WHEN 1 THEN 'SOLICITADO' WHEN 2 THEN 'PROCESADO' WHEN 3 THEN 'CANCELADO' END AS LBLEST,
    			   CASE pv.estadopedido WHEN 1 THEN 'primary' WHEN 2 THEN 'success' WHEN 3 THEN 'danger' END AS CLAEST,
    			   LPAD(pv.pedidovendedor_id, 8, 0) AS NUMPED";
		$from = "pedidovendedor pv INNER JOIN cliente cl ON pv.cliente_id = cl.cliente_id INNER JOIN
				 vendedor ve ON pv.vendedor_id = ve.vendedor_id INNER JOIN
				 estadopedido ep ON pv.estadopedido = ep.estadopedido_id";
		$where = "pv.fecha = '{$fecha_sys}' AND pv.vendedor_id = {$vendedor_id} ORDER BY pv.importe_total DESC LIMIT 5";
		$pedidovendedor_collection = CollectorCondition()->get('PedidoVendedor', $where, 4, $from, $select);

		$select = "COUNT(pv.vendedor_id) AS CANT, ROUND(SUM(pv.importe_total), 2) AS TOTAL";
		$from = "pedidovendedor pv";
		$where = "pv.fecha = '{$fecha_sys}' AND pv.vendedor_id = {$vendedor_id}";
		$totales = CollectorCondition()->get('PedidoVendedor', $where, 4, $from, $select);

		if (is_array($totales) AND !empty($totales)) {
			$array_totales = array("{total_venta}"=>$totales[0]["TOTAL"], "{cantidad_venta}"=>$totales[0]["CANT"]);
		} else {
			$array_totales = array("{total_venta}"=>0, "{cantidad_venta}"=>0);
		}

		$this->view->vdr_panel($pedidovendedor_collection, $array_totales);
	}

	function resumen_diario() {
    	SessionHandler()->check_session();
    	$fecha_sys = date('Y-m-d');

    	$select = "cd.caja AS CAJA";
		$from = "cajadiaria cd ORDER BY cd.fecha DESC LIMIT 1";
		$cajadiaria = CollectorCondition()->get('CajaDiaria', NULL, 4, $from, $select);
		$cajadiaria = (is_array($cajadiaria) AND !empty($cajadiaria)) ? $cajadiaria[0]['CAJA'] : 0;
		$cajadiaria = (is_null($cajadiaria)) ? 0 : $cajadiaria;

    	$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega esen ON ee.estadoentrega = esen.estadoentrega_id";
		$where = "e.condicionpago = 2 AND ee.fecha = CURDATE() AND esen.estadoentrega_id = 4";
		$sum_contado = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado = (is_array($sum_contado)) ? $sum_contado[0]['CONTADO'] : 0;
		$sum_contado = (is_null($sum_contado)) ? 0 : $sum_contado;
		
		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha = '{$fecha_sys}' AND ccc.ingresotipopago = 3";
		$ingreso_cuentacorriente_hoy = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_hoy = (is_array($ingreso_cuentacorriente_hoy)) ? $ingreso_cuentacorriente_hoy[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_hoy = (is_null($ingreso_cuentacorriente_hoy)) ? 0 : $ingreso_cuentacorriente_hoy;

		//COBRANZA
		$cobranza = $sum_contado + $ingreso_cuentacorriente_hoy;

		$select = "e.egreso_id AS EGRESO_ID, e.importe_total AS IMPORTETOTAL";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
				 egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN
				 egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "e.fecha = '{$fecha_sys}'";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		//DETALLE COBRANZA
		$select = "c.cobrador_id AS CID, ccc.fecha AS FECHA, c.denominacion AS COBRADOR, ROUND(SUM(ccc.ingreso), 2) AS COBRANZA";
		$from = "cuentacorrientecliente ccc INNER JOIN cobrador c ON ccc.cobrador = c.cobrador_id";
		$where = "ccc.fecha = '{$fecha_sys}' AND ccc.tipomovimientocuenta = 2";
		$group_by = "ccc.cobrador";
		$cobranza_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $group_by);

		$suma_ingresos_hoy = 0;
		$suma_notacredito_hoy = 0;
		$total_facturacion_hoy = 0;
		if (is_array($egresos_collection) AND !empty($egresos_collection)) {
			foreach ($egresos_collection as $clave=>$valor) {
				$egreso_importe_total = $egresos_collection[$clave]['IMPORTETOTAL'];

				$egreso_id = $valor['EGRESO_ID'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$suma_notacredito_hoy = $suma_notacredito_hoy + $importe_notacredito;
				}

				$suma_ingresos_hoy = $suma_ingresos_hoy + $egreso_importe_total;
			}
		}

		//VENTAS DEL DÍA
		$total_facturacion_hoy = $suma_ingresos_hoy - $suma_notacredito_hoy;

		//PAGO PROVEEDORES
		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha = '{$fecha_sys}' AND ccp.ingresotipopago NOT IN (1,4)";
		$egreso_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_hoy = (is_array($egreso_cuentacorrienteproveedor_hoy)) ? $egreso_cuentacorrienteproveedor_hoy[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_hoy = (is_null($egreso_cuentacorrienteproveedor_hoy)) ? 0 : $egreso_cuentacorrienteproveedor_hoy;

		$select = "ROUND(SUM(i.costo_total_iva),2) AS PROVCONT";
		$from = "ingreso i";
		$where = "i.fecha = '{$fecha_sys}' AND i.condicionpago = 2";
		$egreso_contadoproveedor_hoy = CollectorCondition()->get('Ingreso', $where, 4, $from, $select);
		$egreso_contadoproveedor_hoy = (is_array($egreso_contadoproveedor_hoy)) ? $egreso_contadoproveedor_hoy[0]['PROVCONT'] : 0;
		$egreso_contadoproveedor_hoy = (is_null($egreso_contadoproveedor_hoy)) ? 0 : $egreso_contadoproveedor_hoy;
		$pago_proveedores = $egreso_cuentacorrienteproveedor_hoy + $egreso_contadoproveedor_hoy;

		//DETALLE PAGO PROVEEDORES
		$select = "p.razon_social AS RAZSOC, p.proveedor_id AS PID,
				   ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA,'{$fecha_sys}' AS FECHA";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$where = "ccp.fecha = '{$fecha_sys}' AND ccp.ingresotipopago != 1";
		$groupby = "ccp.proveedor_id";
		$detalle_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select, $groupby);

		$select = "p.razon_social AS RAZSOC, p.proveedor_id AS PID, ROUND(SUM(i.costo_total_iva),2) AS TSALIDA";
		$from = "ingreso i INNER JOIN proveedor p ON i.proveedor = p.proveedor_id";
		$where = "i.fecha = '{$fecha_sys}' AND i.condicionpago = 2";
		$groupby = "i.proveedor";
		$detalle_contadoproveedor_hoy = CollectorCondition()->get('Ingreso', $where, 4, $from, $select, $groupby);

		if (is_array($detalle_cuentacorrienteproveedor_hoy) AND !empty($detalle_cuentacorrienteproveedor_hoy)) {
			$detalle_pagoproveedor = $detalle_cuentacorrienteproveedor_hoy;

			if (is_array($detalle_contadoproveedor_hoy) AND !empty($detalle_contadoproveedor_hoy)) {
				foreach ($detalle_pagoproveedor as $clave=>$valor) {
					$temp_proveedor_id = $valor["PID"];
					foreach ($detalle_contadoproveedor_hoy as $k=>$v) {
						$proveedor_id = $v["PID"];
						if ($temp_proveedor_id == $proveedor_id) {
							$detalle_pagoproveedor[$clave]["TSALIDA"] = $detalle_pagoproveedor[$clave]["TSALIDA"] + $detalle_contadoproveedor_hoy[$k]["TSALIDA"];
						} else {
							$array_temp = array("PID"=>$valor["PID"],
												"RAZSOC"=>$valor["RAZSOC"],
												"TSALIDA"=>$valor["TSALIDA"]);
							$detalle_pagoproveedor[] = $array_temp;
						}
					}
				}
			}
		} else {
			if (is_array($detalle_contadoproveedor_hoy) AND !empty($detalle_contadoproveedor_hoy)) {
				$detalle_pagoproveedor = $detalle_contadoproveedor_hoy;
			} else {
				$detalle_pagoproveedor = array();
			}
		}

		//PAGO COMISIONES
		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec";
		$where = "ec.fecha = '{$fecha_sys}' AND ec.estadocomision IN (2,3)";
		$egreso_comision_hoy = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_hoy = (is_array($egreso_comision_hoy)) ? $egreso_comision_hoy[0]['ECOMISION'] : 0;
		$egreso_comision_hoy = (is_null($egreso_comision_hoy)) ? 0 : $egreso_comision_hoy;

		//DETALLE COMISIONES
		$select = "CONCAT(v.apellido,',',v.nombre) AS VENDEDOR,ROUND(SUM(ec.valor_abonado),2) AS VALOR,esc.denominacion AS ESTADO";
		$from = "egresocomision ec  INNER JOIN egreso e ON e.egresocomision = ec.egresocomision_id INNER JOIN vendedor v ON v.vendedor_id = e.vendedor INNER JOIN estadocomision esc ON esc.estadocomision_id = ec.estadocomision";
		$where = "ec.fecha = '{$fecha_sys}' AND ec.estadocomision IN (2,3) GROUP BY e.vendedor";
		$detalle_comision = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);

		//GASTO DIARIO
		$select = "ROUND(SUM(g.importe), 2) AS IMPORTETOTAL";
		$from = "gasto g";
		$where = "g.fecha = '{$fecha_sys}' ";
		$gasto_diario = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$gasto_diario = (is_array($gasto_diario)) ? $gasto_diario[0]['IMPORTETOTAL'] : 0;
		$gasto_diario = (is_null($gasto_diario)) ? 0 : $gasto_diario;

		//DETALLE GASTO DIARIO
		$select = "gc.denominacion AS CATEGORIA,g.detalle AS DETALLE,ROUND(g.importe, 2) AS IMPORTETOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc on gc.gastocategoria_id = g.gastocategoria INNER JOIN gastosubcategoria gs on gs.gastosubcategoria_id = gc.gastosubcategoria";
		$where = "g.fecha = '{$fecha_sys}'";
		$detalle_gasto_diario = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		//LIQUIDACIONES
		$select = "ROUND(SUM(s.monto), 2) AS IMPORTETOTAL";
		$from = "salario s";
		$where = "s.fecha = '{$fecha_sys}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$liquidacion = CollectorCondition()->get('Salario', $where, 4, $from, $select);
		$liquidacion = (is_array($liquidacion)) ? $liquidacion[0]['IMPORTETOTAL'] : 0;
		$liquidacion = (is_null($liquidacion)) ? 0 : $liquidacion;

		//DETALLE LIQUIDACIONES
		$select = "CONCAT(e.apellido, e.nombre) AS EMPLEADO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), 'hasta el ', date_format(s.hasta, '%d/%m/%Y')) AS DETALLE, ROUND(s.monto, 2) AS IMPORTETOTAL";
		$from = "salario s INNER JOIN empleado e on e.empleado_id = s.empleado";
		$where = "s.fecha = '{$fecha_sys}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$detalle_liquidacion = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		//VEHICULOS
		$select = "ROUND(SUM(vc.importe), 2) AS IMPORTETOTAL";
		$from = "vehiculocombustible vc";
		$where = "vc.fecha = '{$fecha_sys}'";
		$vehiculos = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);
		$vehiculos = (is_array($vehiculos)) ? $vehiculos[0]['IMPORTETOTAL'] : 0;
		$vehiculos = (is_null($vehiculos)) ? 0 : $vehiculos;

		//DETALLE VEHICULOS
		$select = "v.denominacion AS DETALLE,ROUND(vc.importe, 2) AS IMPORTETOTAL";
		$from = "vehiculocombustible vc INNER JOIN vehiculo v ON v.vehiculo_id = vc.vehiculo";
		$where = "vc.fecha = '{$fecha_sys}'";
		$detalle_vehiculos = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);

		$calculo_cajadiaria = $this->calcula_cajadiaria();

		$array_totales = array('{cobranza}'=>$cobranza,
							   '{ventas}'=>$total_facturacion_hoy,
							   '{pago_proveedores}'=>$pago_proveedores,
							   '{pago_comisiones}'=>$egreso_comision_hoy,
							   '{gasto_diario}'=>$gasto_diario,
								 '{liquidacion}'=>$liquidacion,
								 '{vehiculos}'=>$vehiculos,
							   '{caja}'=>$calculo_cajadiaria,
							   '{fecha}'=>$fecha_sys);
		$this->view->resumen_diario($array_totales, $cobranza_collection, $detalle_pagoproveedor,$detalle_gasto_diario,$detalle_liquidacion,$detalle_vehiculos,$detalle_comision, 1);
	}

	function detalle_facturaproveedor($arg) {
		SessionHandler()->check_session();

		$var = explode('@',$arg);
		$proveedor_id = $var[0];
		$fecha = $var[1];

		$pm = new Proveedor();
		$pm->proveedor_id = $proveedor_id;
		$pm->get();

		$select = "date_format(ccp.fecha, '%d/%m/%Y') AS FECHA, ccp.importe AS IMPORTE, ccp.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccp.ingreso_id AS IID,
					 ccp.referencia AS REFERENCIA, CASE ccp.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS,
					 ccp.cuentacorrienteproveedor_id CCPID";
		$from = "cuentacorrienteproveedor ccp INNER JOIN tipomovimientocuenta tmc ON ccp.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccp.proveedor_id = {$proveedor_id} and ccp.fecha = '{$fecha}' and ccp.ingresotipopago BETWEEN 2 AND 3";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

		$ingreso_ids = array();
		if(is_array($cuentacorriente_collection)){
			foreach ($cuentacorriente_collection as $clave=>$valor) {
				$ingreso_id = $valor['IID'];
				if (!in_array($ingreso_id, $ingreso_ids)) $ingreso_ids[] = $ingreso_id;
				$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) -
								(ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
				$from = "cuentacorrienteproveedor ccp";
				$where = "ccp.ingreso_id = {$ingreso_id} ";
				$array_temp = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

				$balance = $array_temp[0]['BALANCE'];
				$balance = ($balance == '-0') ? abs($balance) : $balance;
				$balance_class = ($balance >= 0) ? 'primary' : 'danger';
				$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

				$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
				$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;

			}
		}

		if(is_array($ingreso_ids)){
			$max_cuentacorrienteproveedor_ids = array();
			foreach ($ingreso_ids as $ingreso_id) {
				$select = "ccp.cuentacorrienteproveedor_id AS ID";
				$from = "cuentacorrienteproveedor ccp";
				$where = "ccp.ingreso_id = {$ingreso_id} ORDER BY ccp.cuentacorrienteproveedor_id DESC LIMIT 1";
				$max_id = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
				if (!in_array($max_id[0]['ID'], $max_cuentacorrienteproveedor_ids)) $max_cuentacorrienteproveedor_ids[] = $max_id[0]['ID'];
			}
		}

		$this->view->detalle_facturaproveedor($cuentacorriente_collection, $pm);
	}

	function filtra_resumen_diario() {
    	SessionHandler()->check_session();
    	$fecha_filtro = filter_input(INPUT_POST, 'fecha');

    	$select = "cd.caja AS CAJA";
		$from = "cajadiaria cd";
		$where = "cd.fecha = '{$fecha_filtro}' ORDER BY cd.fecha DESC LIMIT 1";
		$cajadiaria = CollectorCondition()->get('CajaDiaria', $where, 4, $from, $select);
		$cajadiaria = (is_array($cajadiaria) AND !empty($cajadiaria)) ? $cajadiaria[0]['CAJA'] : 0;
		$cajadiaria = (is_null($cajadiaria)) ? 0 : $cajadiaria;

    	$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega esen ON ee.estadoentrega = esen.estadoentrega_id";
		$where = "e.condicionpago = 2 AND ee.fecha = '{$fecha_filtro}' AND esen.estadoentrega_id = 4";
		$sum_contado = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado = (is_array($sum_contado)) ? $sum_contado[0]['CONTADO'] : 0;
		$sum_contado = (is_null($sum_contado)) ? 0 : $sum_contado;

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha = '{$fecha_filtro}' AND ccc.ingresotipopago = 3";
		$ingreso_cuentacorriente_hoy = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_hoy = (is_array($ingreso_cuentacorriente_hoy)) ? $ingreso_cuentacorriente_hoy[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_hoy = (is_null($ingreso_cuentacorriente_hoy)) ? 0 : $ingreso_cuentacorriente_hoy;

		//COBRANZA
		$cobranza = $sum_contado + $ingreso_cuentacorriente_hoy;

		$select = "e.egreso_id AS EGRESO_ID, e.importe_total AS IMPORTETOTAL";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "e.fecha = '{$fecha_filtro}'";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$suma_ingresos_hoy = 0;
		$suma_notacredito_hoy = 0;
		$total_facturacion_hoy = 0;
		if (is_array($egresos_collection) AND !empty($egresos_collection)) {
			foreach ($egresos_collection as $clave=>$valor) {
				$egreso_importe_total = $egresos_collection[$clave]['IMPORTETOTAL'];

				$egreso_id = $valor['EGRESO_ID'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$suma_notacredito_hoy = $suma_notacredito_hoy + $importe_notacredito;
				}

				$suma_ingresos_hoy = $suma_ingresos_hoy + $egreso_importe_total;
			}
		}

		//VENTAS DEL DÍA
		$total_facturacion_hoy = $suma_ingresos_hoy - $suma_notacredito_hoy;

		//PAGO PROVEEDORES
		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha = '{$fecha_filtro}' AND ccp.ingresotipopago NOT IN (1,4)";
		$egreso_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_hoy = (is_array($egreso_cuentacorrienteproveedor_hoy)) ? $egreso_cuentacorrienteproveedor_hoy[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_hoy = (is_null($egreso_cuentacorrienteproveedor_hoy)) ? 0 : $egreso_cuentacorrienteproveedor_hoy;

		$select = "ROUND(SUM(i.costo_total_iva),2) AS PROVCONT";
		$from = "ingreso i";
		$where = "i.fecha = '{$fecha_filtro}' AND i.condicionpago = 2";
		$egreso_contadoproveedor_hoy = CollectorCondition()->get('Ingreso', $where, 4, $from, $select);
		$egreso_contadoproveedor_hoy = (is_array($egreso_contadoproveedor_hoy)) ? $egreso_contadoproveedor_hoy[0]['PROVCONT'] : 0;
		$egreso_contadoproveedor_hoy = (is_null($egreso_contadoproveedor_hoy)) ? 0 : $egreso_contadoproveedor_hoy;
		$pago_proveedores = $egreso_cuentacorrienteproveedor_hoy + $egreso_contadoproveedor_hoy;

		//DETALLE PAGO PROVEEDORES
		$select = "p.razon_social AS RAZSOC, p.proveedor_id AS PID, ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA,'{$fecha_filtro}' AS FECHA";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$where = "ccp.fecha = '{$fecha_filtro}' AND ccp.ingresotipopago != 4";
		$groupby = "ccp.proveedor_id";
		$detalle_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select, $groupby);

		$select = "p.razon_social AS RAZSOC, p.proveedor_id AS PID, ROUND(SUM(i.costo_total_iva),2) AS TSALIDA";
		$from = "ingreso i INNER JOIN proveedor p ON i.proveedor = p.proveedor_id";
		$where = "i.fecha = '{$fecha_filtro}' AND i.condicionpago = 2";
		$groupby = "i.proveedor";
		$detalle_contadoproveedor_hoy = CollectorCondition()->get('Ingreso', $where, 4, $from, $select, $groupby);

		if (is_array($detalle_cuentacorrienteproveedor_hoy) AND !empty($detalle_cuentacorrienteproveedor_hoy)) {
			$detalle_pagoproveedor = $detalle_cuentacorrienteproveedor_hoy;

			if (is_array($detalle_contadoproveedor_hoy) AND !empty($detalle_contadoproveedor_hoy)) {
				foreach ($detalle_pagoproveedor as $clave=>$valor) {
					$temp_proveedor_id = $valor["PID"];
					foreach ($detalle_contadoproveedor_hoy as $k=>$v) {
						$proveedor_id = $v["PID"];
						if ($temp_proveedor_id == $proveedor_id) {
							$detalle_pagoproveedor[$clave]["TSALIDA"] = $detalle_pagoproveedor[$clave]["TSALIDA"] + $detalle_contadoproveedor_hoy[$k]["TSALIDA"];
						} else {
							$array_temp = array("PID"=>$valor["PID"],
												"RAZSOC"=>$valor["RAZSOC"],
												"TSALIDA"=>$valor["TSALIDA"]);
							$detalle_pagoproveedor[] = $array_temp;
						}
					}
				}
			}
		} else {
			if (is_array($detalle_contadoproveedor_hoy) AND !empty($detalle_contadoproveedor_hoy)) {
				$detalle_pagoproveedor = $detalle_contadoproveedor_hoy;
			} else {
				$detalle_pagoproveedor = array();
			}
		}

		//PAGO COMISIONES
		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec";
		$where = "ec.fecha = '{$fecha_filtro}' AND ec.estadocomision IN (2,3)";
		$egreso_comision_hoy = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_hoy = (is_array($egreso_comision_hoy)) ? $egreso_comision_hoy[0]['ECOMISION'] : 0;
		$egreso_comision_hoy = (is_null($egreso_comision_hoy)) ? 0 : $egreso_comision_hoy;

		//DETALLE COMISIONES
		$select = "CONCAT(v.apellido,',',v.nombre) AS VENDEDOR,ROUND(SUM(ec.valor_abonado),2) AS VALOR,esc.denominacion AS ESTADO";
		$from = "egresocomision ec  INNER JOIN egreso e ON e.egresocomision = ec.egresocomision_id INNER JOIN vendedor v ON v.vendedor_id = e.vendedor INNER JOIN estadocomision esc ON esc.estadocomision_id = ec.estadocomision";
		$where = "ec.fecha = '{$fecha_filtro}' AND ec.estadocomision IN (2,3) GROUP BY e.vendedor";
		$detalle_comision = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);

		//GASTO DIARIO
		$select = "ROUND(SUM(g.importe), 2) AS IMPORTETOTAL";
		$from = "gasto g";
		$where = "g.fecha = '{$fecha_filtro}'";
		$gasto_diario = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$gasto_diario = (is_array($gasto_diario)) ? $gasto_diario[0]['IMPORTETOTAL'] : 0;
		$gasto_diario = (is_null($gasto_diario)) ? 0 : $gasto_diario;

		//DETALLE GASTO DIARIO
		$select = "gc.denominacion AS CATEGORIA,g.detalle AS DETALLE,ROUND(g.importe, 2) AS IMPORTETOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc on gc.gastocategoria_id = g.gastocategoria INNER JOIN gastosubcategoria gs on gs.gastosubcategoria_id = gc.gastosubcategoria";
		$where = "g.fecha = '{$fecha_filtro}'";
		$detalle_gasto_diario = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		//LIQUIDACIONES
		$select = "ROUND(SUM(s.monto), 2) AS IMPORTETOTAL";
		$from = "salario s";
		$where = "s.fecha = '{$fecha_filtro}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$liquidacion = CollectorCondition()->get('Salario', $where, 4, $from, $select);
		$liquidacion = (is_array($liquidacion)) ? $liquidacion[0]['IMPORTETOTAL'] : 0;
		$liquidacion = (is_null($liquidacion)) ? 0 : $liquidacion;

		//DETALLE LIQUIDACIONES
		$select = "CONCAT(e.apellido, e.nombre) AS EMPLEADO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), 'hasta el ', date_format(s.hasta, '%d/%m/%Y')) AS DETALLE, ROUND(s.monto, 2) AS IMPORTETOTAL";
		$from = "salario s INNER JOIN empleado e on e.empleado_id = s.empleado";
		$where = "s.fecha = '{$fecha_filtro}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$detalle_liquidacion = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		//VEHICULOS
		$select = "ROUND(SUM(vc.importe), 2) AS IMPORTETOTAL";
		$from = "vehiculocombustible vc";
		$where = "vc.fecha = '{$fecha_filtro}'";
		$vehiculos = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);
		$vehiculos = (is_array($vehiculos)) ? $vehiculos[0]['IMPORTETOTAL'] : 0;
		$vehiculos = (is_null($vehiculos)) ? 0 : $vehiculos;

		//DETALLE VEHICULOS
		$select = "v.denominacion AS DETALLE,ROUND(vc.importe, 2) AS IMPORTETOTAL";
		$from = "vehiculocombustible vc INNER JOIN vehiculo v ON v.vehiculo_id = vc.vehiculo";
		$where = "vc.fecha = '{$fecha_filtro}'";
		$detalle_vehiculos = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);

		if ($cajadiaria != 0 AND !is_null($cajadiaria)) {
			$calculo_cajadiaria = round($cajadiaria,2);
		} else {
			$calculo_cajadiaria = 0;
		}

		$select = "c.cobrador_id AS CID, ccc.fecha AS FECHA, c.denominacion AS COBRADOR, ROUND(SUM(ccc.ingreso), 2) AS COBRANZA";
		$from = "cuentacorrientecliente ccc INNER JOIN cobrador c ON ccc.cobrador = c.cobrador_id";
		$where = "ccc.fecha = '{$fecha_filtro}' AND ccc.tipomovimientocuenta = 2";
		$group_by = "ccc.cobrador";
		$cobranza_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $group_by);

		$array_totales = array('{cobranza}'=>$cobranza,
							   '{ventas}'=>$total_facturacion_hoy,
							   '{pago_proveedores}'=>$pago_proveedores,
							   '{pago_comisiones}'=>$egreso_comision_hoy,
							   '{gasto_diario}'=>$gasto_diario,
								 '{liquidacion}'=>$liquidacion,
								 '{vehiculos}'=>$vehiculos,
							   '{caja}'=>$calculo_cajadiaria,
							   '{fecha}'=>$fecha_filtro);
		$this->view->resumen_diario($array_totales, $cobranza_collection, $detalle_pagoproveedor,$detalle_gasto_diario,$detalle_liquidacion,$detalle_vehiculos,$detalle_comision, 2);
	}

	function detalle_cobrador_cobranza($arg) {
		$args = explode("@", $arg);
		$cobrador_id = $args[0];
		$fecha = $args[1];

		$cm = new Cobrador();
		$cm->cobrador_id = $cobrador_id;
		$cm->get();

		$select = "ROUND(SUM(ccc.ingreso), 2) AS COBRANZA";
		$from = "cuentacorrientecliente ccc INNER JOIN cobrador c ON ccc.cobrador = c.cobrador_id";
		$where = "ccc.fecha = '{$fecha}' AND ccc.tipomovimientocuenta = 2 AND c.cobrador_id = {$cobrador_id}";
		$group_by = "ccc.cobrador";
		$cobranza = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $group_by);

		$select = "ccc.referencia AS REFERENCIA, ccc.ingreso AS INGRESO, c.razon_social AS RAZSOC, c.nombre_fantasia AS NOMFAN";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.fecha = '{$fecha}' AND ccc.tipomovimientocuenta = 2 AND ccc.cobrador = {$cobrador_id}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$this->view->detalle_cobrador_cobranza($cuentacorriente_collection, $cm, $cobranza, $cobrador_id, $fecha);
	}

	function form_buscar_caja_diaria_ajax() {
		$this->view->form_buscar_caja_diaria_ajax();
	}

	function verificar_caja_diaria_ajax() {
		$fecha_sys = date('Y-m-d');
		$select = "cd.caja as CAJA";
		$from = "cajadiaria cd";
		$where = "cd.fecha = '{$fecha_sys}'";
		$cajadiaria = CollectorCondition()->get('CajaDiaria', $where, 4, $from, $select);
		$cajadiaria = (is_array($cajadiaria) AND !empty($cajadiaria)) ? $cajadiaria[0]['CAJA'] : 0;
		$this->view->formulario_cajadiaria_ajax($cajadiaria);
	}

	function cerrar_cajadiaria() {
		$caja = filter_input(INPUT_POST, 'caja');

		$cdm = new CajaDiaria();
		$cdm->caja = $caja;
		$cdm->fecha = date('Y-m-d');
		$cdm->usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$cdm->save();
		header("Location: " . URL_APP . "/reporte/resumen_diario");
	}

	function balance() {
		SessionHandler()->check_session();
		$cbm = new ConfiguracionBalance();
		$cbm->configuracionbalance_id = 1;
		$cbm->get();

		$anio = date('Y');
		$mes = date('m');
		$desde = "{$anio}-{$mes}-01";
		$hasta = date("Y-m-d");
		$periodo = "desde el {$desde} hasta el {$hasta}";
		$fecha_sys = date('Y-m-d');

		$periodo_actual = date('Ym');
		$select = "e.egreso_id AS EGRESO_ID, e.importe_total AS IMPORTETOTAL";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$suma_ingresos_per_actual = 0;
		$suma_notacredito_per_actual = 0;
		$total_ingresos_per_actual = 0;
		$egreso_id_array = array();
		if (is_array($egresos_collection) AND !empty($egresos_collection)) {
			foreach ($egresos_collection as $clave=>$valor) {
				$egreso_importe_total = $egresos_collection[$clave]['IMPORTETOTAL'];

				$egreso_id = $valor['EGRESO_ID'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$suma_notacredito_per_actual = $suma_notacredito_per_actual + $importe_notacredito;
				}

				$suma_ingresos_per_actual = $suma_ingresos_per_actual + $egreso_importe_total;
				if(!in_array($egreso_id, $egreso_id_array)) $egreso_id_array[] = $egreso_id;
			}
		}

		$total_ingresos_per_actual = $suma_ingresos_per_actual - $suma_notacredito_per_actual;
		$ganancia_egreso_ids = implode(',', $egreso_id_array);

		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec INNER JOIN egreso e ON ec.egresocomision_id = e.egresocomision";
		$where = "ec.estadocomision IN (2,3) AND e.egreso_id IN ({$ganancia_egreso_ids})";
		$egreso_comision_per_actual = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_per_actual = (is_array($egreso_comision_per_actual)) ? $egreso_comision_per_actual[0]['ECOMISION'] : 0;
		$egreso_comision_per_actual = (is_null($egreso_comision_per_actual)) ? 0 : $egreso_comision_per_actual;

		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$egreso_cuentacorrienteproveedor_per_actual = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_per_actual = (is_array($egreso_cuentacorrienteproveedor_per_actual)) ? $egreso_cuentacorrienteproveedor_per_actual[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_per_actual = (is_null($egreso_cuentacorrienteproveedor_per_actual)) ? 0 : $egreso_cuentacorrienteproveedor_per_actual;

		$select = "ROUND(SUM(g.importe), 2) AS IMPORTETOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc ON gc.gastocategoria_id = g.gastocategoria INNER JOIN gastosubcategoria gsc ON gsc.gastosubcategoria_id = gc.gastosubcategoria";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$egreso_gasto_per_actual = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$egreso_gasto_per_actual = (is_array($egreso_gasto_per_actual)) ? $egreso_gasto_per_actual[0]['IMPORTETOTAL'] : 0;
		$egreso_gasto_per_actual = (is_null($egreso_gasto_per_actual)) ? 0 : $egreso_gasto_per_actual;

		$select = "s.producto_id AS PROD_ID";
		$from = "stock s";
		$groupby = "s.producto_id";
		$productoid_collection = CollectorCondition()->get('Stock', NULL, 4, $from, $select, $groupby);
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
				$this->stock = new Stock();
				$this->stock->stock_id = $stock_id['STOCK_ID'];
				$this->stock->get();

				$this->producto = new Producto();
				$this->producto->producto_id = $this->stock->producto_id;
				$this->producto->get();

				if ($this->producto->oculto == 0) {
					$costo_iva = $this->producto->costo + (($this->producto->costo * $this->producto->iva) / 100);
					$valor_stock_producto = round(($costo_iva * $this->stock->cantidad_actual),2);
					$stock_valorizado = $stock_valorizado + $valor_stock_producto;

					$this->stock->producto = $this->producto;
					$this->stock->valor_stock = $valor_stock_producto;
					unset($this->stock->producto_id);
					if ($this->stock->cantidad_actual < $this->producto->stock_minimo) {
						$stock_collection[] = $this->stock;
					}
				}
			}
		}

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA, ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$estado_cuentacorrientecliente = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);
		if (is_array($estado_cuentacorrientecliente) AND !empty($estado_cuentacorrientecliente)) {
			$estado_cuentacorrientecliente = $estado_cuentacorrientecliente[0]['TDEUDA'] - $estado_cuentacorrientecliente[0]['TINGRESO'];
		} else {
			$estado_cuentacorrientecliente = 0;
		}

		$select = "ccp.proveedor_id AS PID, p.razon_social AS PROVEEDOR, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.proveedor_id";
		$cuentacorrienteproveedor_total = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select, $groupby);
		if (is_array($cuentacorrienteproveedor_total)) {
			$deuda_cuentacorrienteproveedor = 0;
			foreach ($cuentacorrienteproveedor_total as $clave=>$valor) {
				$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
				$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
				$cuenta = round(($ingreso - $deuda),2);
				$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
				$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
				$deuda_cuentacorrienteproveedor = $deuda_cuentacorrienteproveedor + $cuenta;

			}
		} else {
			$deuda_cuentacorrienteproveedor = 0;
		}

		$deuda_cuentacorrienteproveedor = abs($deuda_cuentacorrienteproveedor);
		$deuda_cuentacorrienteproveedor = ($deuda_cuentacorrienteproveedor > 0.5) ? $deuda_cuentacorrienteproveedor : 0;

		$select = "e.egreso_id AS EID, e.importe_total AS IMPTOTAL, ec.valor_comision AS VALCOM ";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id";
		$where = "ec.estadocomision = 1 AND ec.fecha IS NOT NULL";
		$egreso_ids_comision = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$egreso_ids_comision = (is_array($egreso_ids_comision) AND !empty($egreso_ids_comision)) ? $egreso_ids_comision : array();

		$deuda_comision_total = 0;
		foreach ($egreso_ids_comision as $clave=>$valor) {
			$tmp_valor_comision = 0;
			$array_egreso_id = $valor['EID'];
			$array_importe_total = $valor['IMPTOTAL'];
			$array_valor_comision = $valor['VALCOM'];
			$select = "nc.notacredito_id AS NCID";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$array_egreso_id}";
			$tmp_notacredito_array = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($tmp_notacredito_array) AND !empty($tmp_notacredito_array)) {
				$tmp_notacredito_id = $tmp_notacredito_array[0]['NCID'];
				$ncm = new NotaCredito();
				$ncm->notacredito_id = $tmp_notacredito_id;
				$ncm->get();
				$nc_importe_total = $ncm->importe_total;

				$array_importe_total = $array_importe_total - $nc_importe_total;
			}

			$tmp_valor_comision = round(($array_valor_comision * $array_importe_total / 100), 2);
			$deuda_comision_total = $deuda_comision_total + $tmp_valor_comision;
		}

		//GRAFICOS
		$select = "CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(valor_abonado),2) AS ECOMISION ";
		$from = "egresocomision ec INNER JOIN egreso e ON ec.egresocomision_id = e.egresocomision INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "date_format(ec.fecha, '%Y%m') = '{$periodo_actual}' AND ec.estadocomision IN (2,3)";
		$group_by = "v.vendedor_id,	date_format(ec.fecha, '%Y%m') ORDER BY SUM(valor_abonado) DESC";
		$pagocomisiones_collection = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select, $group_by);

		//ENTREGAS PENDIENTES
		$select = "hr.egreso_ids AS IDS";
		$from = "hojaruta hr";
		$where = "hr.estadoentrega = 3";
		$hojaruta_collection = CollectorCondition()->get('HojaRuta', $where, 4, $from, $select);
		$array_egreso_ids = array();
		foreach ($hojaruta_collection as $clave=>$valor) {
			$array_tuplas = explode(",", $valor['IDS']);
			foreach ($array_tuplas as $tupla) {
				$ids = explode("@", $tupla);
				$egreso_id = $ids[0];
				$estadoentrega_id = $ids[1];
				if(!in_array($egreso_id, $array_egreso_ids) AND $estadoentrega_id == 3) $array_egreso_ids[] = $egreso_id;
			}
		}

		$egreso_ids = implode(',', $array_egreso_ids);
		$select = "ROUND(SUM(e.importe_total), 2) CONTADO";
		$from = "egreso e";
		$where = "e.egreso_id IN ({$egreso_ids}) AND e.condicionpago = 2";
		$carga_pendiente = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$carga_pendiente = (is_array($carga_pendiente) AND !empty($carga_pendiente)) ? $carga_pendiente[0]['CONTADO'] : 0;

		$activo_corriente = 0;
		if ($cbm->activo_stock_valorizado == 'checked') {
			$activo_corriente = $activo_corriente + $stock_valorizado;
		}

		if ($cbm->activo_cuenta_corriente_cliente == 'checked') {
			$activo_corriente = $activo_corriente + $estado_cuentacorrientecliente;
		}

		if ($cbm->activo_carga_pendiente == 'checked') {
			$activo_corriente = $activo_corriente + $carga_pendiente;
		}

		$pasivo_corriente = 0;
		if ($cbm->pasivo_comisiones_pendientes == 'checked') {
			$pasivo_corriente = $pasivo_corriente + $deuda_comision_total;
		}

		if ($cbm->pasivo_cuenta_corriente_proveedor == 'checked') {
			$pasivo_corriente = $pasivo_corriente + $deuda_cuentacorrienteproveedor;
		}

		$cajadiaria = $this->calcula_cajadiaria();

		$select = "ROUND(SUM(ed.valor_ganancia),2) AS GANANCIA";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id";
		$where = "e.egreso_id IN ({$ganancia_egreso_ids}) AND c.impacto_ganancia = 1";
		$sum_ganancia_per_actual = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_ganancia_per_actual = (is_array($sum_ganancia_per_actual) AND !empty($sum_ganancia_per_actual)) ? $sum_ganancia_per_actual[0]['GANANCIA'] : 0;
		$sum_ganancia_per_actual = (is_null($sum_ganancia_per_actual)) ? 0 : $sum_ganancia_per_actual;

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "date_format(ccc.fecha, '%Y%m') = '{$periodo_actual}'";
		$ingreso_cuentacorriente_per_actual = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_per_actual = (is_array($ingreso_cuentacorriente_per_actual) AND !empty($ingreso_cuentacorriente_per_actual)) ? $ingreso_cuentacorriente_per_actual[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_per_actual = (is_null($ingreso_cuentacorriente_per_actual)) ? 0 : $ingreso_cuentacorriente_per_actual;

		$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega esen ON ee.estadoentrega = esen.estadoentrega_id";
		$where = "e.condicionpago = 2 AND date_format(ee.fecha, '%Y%m') = '{$periodo_actual}' AND esen.estadoentrega_id = 4";
		$sum_contado_per_actual = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado_per_actual = (is_array($sum_contado_per_actual) AND !empty($sum_contado_per_actual)) ? $sum_contado_per_actual[0]['CONTADO'] : 0;
		$sum_contado_per_actual = (is_null($sum_contado_per_actual)) ? 0 : $sum_contado_per_actual;

		$select = "ROUND(SUM(ncd.valor_ganancia),2) AS GANANCIA";
		$from = "notacredito nc INNER JOIN notacreditodetalle ncd ON nc.notacredito_id = ncd.notacredito_id INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id";
		$where = "e.egreso_id IN ({$ganancia_egreso_ids}) AND c.impacto_ganancia = 1";
		$rest_nc_ganancia_per_actual = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$rest_nc_ganancia_per_actual = (is_array($rest_nc_ganancia_per_actual) AND !empty($rest_nc_ganancia_per_actual)) ? $rest_nc_ganancia_per_actual[0]['GANANCIA'] : 0;
		$rest_nc_ganancia_per_actual = (is_null($rest_nc_ganancia_per_actual)) ? 0 : $rest_nc_ganancia_per_actual;

		$select = "ROUND(SUM(vc.importe), 2) AS TOTAL";
		$from = "vehiculocombustible vc";
		$where = "vc.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$vehiculocombustible_total = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);
		$vehiculocombustible_total = (is_array($vehiculocombustible_total) AND !empty($vehiculocombustible_total)) ? $vehiculocombustible_total[0]['TOTAL'] : 0;
		$vehiculocombustible_total = (is_null($vehiculocombustible_total)) ? 0 : $vehiculocombustible_total;

		//SALARIO
		$select = "ROUND(SUM(s.monto), 2) AS TOTAL";
		$from = "salario s";
		$where = "s.fecha BETWEEN '{$desde}' AND '{$hasta}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$salario_total = CollectorCondition()->get('Salario', $where, 4, $from, $select);
		$salario_total = (is_array($salario_total) AND !empty($salario_total)) ? $salario_total[0]['TOTAL'] : 0;
		$salario_total = (is_null($salario_total)) ? 0 : $salario_total;

		//GANANCIA DIARIA
		$select = "v.vendedor_id, FORMAT((SUM(ed.valor_ganancia)), 2,'de_DE') AS GANANCIA, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "e.fecha = '{$fecha_sys}' AND c.impacto_ganancia = 1";
		$groupby = "v.vendedor_id";
		$ganancia_vendedor_dia = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ganancia_vendedor_dia = (is_array($ganancia_vendedor_dia) AND !empty($ganancia_vendedor_dia)) ? $ganancia_vendedor_dia : array();

		//CREDITO PROVEEDORES
		$select = "p.proveedor_id, FORMAT((SUM(cpd.importe)), 2,'de_DE') AS IMPORTE, p.razon_social AS PROVEEDOR";
		$from = "creditoproveedordetalle cpd INNER JOIN proveedor p ON cpd.proveedor = p.proveedor_id";
		$where = "cpd.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "p.proveedor_id";
		$creditoproveedordetalle_collection = CollectorCondition()->get('CreditoProveedorDetalle', $where, 4, $from, $select, $groupby);
		$creditoproveedordetalle_collection = (is_array($creditoproveedordetalle_collection) AND !empty($creditoproveedordetalle_collection)) ? $creditoproveedordetalle_collection : array();

		$ganancia_per_actual = $sum_ganancia_per_actual - $rest_nc_ganancia_per_actual - $egreso_comision_per_actual - $egreso_gasto_per_actual - $vehiculocombustible_total - $salario_total;
		$array_balance = array('{suma_ingresos_per_actual}'=>number_format($suma_ingresos_per_actual, 2, ',', '.'),
							   '{suma_notacredito_per_actual}'=>number_format($suma_notacredito_per_actual, 2, ',', '.'),
							   '{total_ingresos_per_actual}'=>number_format($total_ingresos_per_actual, 2, ',', '.'),
							   '{egreso_comision_per_actual}'=>number_format($egreso_comision_per_actual, 2, ',', '.'),
							   '{egreso_salario}'=>number_format($salario_total, 2, ',', '.'),
							   '{egreso_cuentacorrienteproveedor_per_actual}'=>number_format($egreso_cuentacorrienteproveedor_per_actual, 2, ',', '.'),
							   '{egreso_gasto_per_actual}'=>number_format($egreso_gasto_per_actual, 2, ',', '.'),
							   '{egreso_combustible}'=>number_format($vehiculocombustible_total, 2, ',', '.'),
							   '{stock_valorizado}'=>number_format($stock_valorizado, 2, ',', '.'),
							   '{deuda_ccclientes}'=>number_format($estado_cuentacorrientecliente, 2, ',', '.'),
							   '{carga_pendiente}'=>number_format($carga_pendiente, 2, ',', '.'),
							   '{stock_valorizado_graph}'=>$stock_valorizado,
							   '{deuda_ccclientes_graph}'=>$estado_cuentacorrientecliente,
							   '{carga_pendiente_graph}'=>$carga_pendiente,
							   '{deuda_ccproveedores}'=>number_format($deuda_cuentacorrienteproveedor, 2, ',', '.'),
							   '{deuda_comisiones}'=>number_format($deuda_comision_total, 2, ',', '.'),
							   '{deuda_ccproveedores_graph}'=>$deuda_cuentacorrienteproveedor,
							   '{deuda_comisiones_graph}'=>$deuda_comision_total,
							   '{cajadiaria}'=>number_format($cajadiaria, 2, ',', '.'),
							   '{activo_corriente}'=>number_format($activo_corriente, 2, ',', '.'),
							   '{pasivo_corriente}'=>number_format($pasivo_corriente, 2, ',', '.'),
							   '{ganancia_per_actual}'=>number_format($ganancia_per_actual, 2, ',', '.'));

		$select = "CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, FORMAT((SUM(s.monto)), 2,'de_DE') AS IMPORTE";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = "s.fecha BETWEEN '{$desde}' AND '{$hasta}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$groupby = "s.empleado";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select,$groupby);

		$select = "v.dominio AS DOMINIO, v.denominacion AS REFERENCIA, CONCAT(vma.denominacion, ' ', vm.denominacion) AS VEHICULO,
				   FORMAT((SUM(vc.importe)), 2,'de_DE') AS TIMPORTE, FORMAT((SUM(vc.cantidad)), 2,'de_DE') AS TLITRO";
		$from = "vehiculocombustible vc INNER JOIN vehiculo v ON vc.vehiculo = v.vehiculo_id INNER JOIN
				 vehiculomodelo vm ON v.vehiculomodelo = vm.vehiculomodelo_id INNER JOIN
				 vehiculomarca vma ON vm.vehiculomarca = vma.vehiculomarca_id";
		$where = "vc.fecha BETWEEN '{$desde}' AND '{$hasta}' GROUP BY vc.vehiculo ORDER BY SUM(vc.importe) DESC";
		$vehiculocombustible_collection = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION,
				   pc.denominacion AS CATEGORIA, p.codigo AS CODIGO";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN
				 productomarca pm ON p.productomarca = pm.productomarca_id";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', NULL, 4, $from, $select, $groupby);

		$productomarca_collection = Collector()->get('ProductoMarca');

		$this->view->balance($array_balance, $pagocomisiones_collection, $periodo_actual, $cbm, $vehiculocombustible_collection, $producto_collection, $productomarca_collection, $salario_collection, $ganancia_vendedor_dia, $creditoproveedordetalle_collection);
	}

	function generar_balance() {
		SessionHandler()->check_session();

		$fecha_sys = date('Y-m-d');
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$periodo = "desde el {$desde} hasta el {$hasta}";

		if (isset($_SESSION["data-search-balance-" . APP_ABREV])) {
			$_SESSION["data-search-balance-" . APP_ABREV]['desde'] = $desde;
			$_SESSION["data-search-balance-" . APP_ABREV]['hasta'] = $hasta;
		} else {
			$array_busqueda = array('desde'=>$desde, 'hasta'=>$hasta);
			$_SESSION["data-search-balance-" . APP_ABREV] = $array_busqueda;
		}

		$cbm = new ConfiguracionBalance();
		$cbm->configuracionbalance_id = 1;
		$cbm->get();

		$select = "e.egreso_id AS EGRESO_ID, e.importe_total AS IMPORTETOTAL";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$suma_ingresos_per_actual = 0;
		$suma_notacredito_per_actual = 0;
		$total_ingresos_per_actual = 0;
		$egreso_id_array = array();
		if (is_array($egresos_collection) AND !empty($egresos_collection)) {
			foreach ($egresos_collection as $clave=>$valor) {
				$egreso_importe_total = $egresos_collection[$clave]['IMPORTETOTAL'];

				$egreso_id = $valor['EGRESO_ID'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$suma_notacredito_per_actual = $suma_notacredito_per_actual + $importe_notacredito;
				}

				$suma_ingresos_per_actual = $suma_ingresos_per_actual + $egreso_importe_total;
				if(!in_array($egreso_id, $egreso_id_array)) $egreso_id_array[] = $egreso_id;
			}
		}

		$total_ingresos_per_actual = $suma_ingresos_per_actual - $suma_notacredito_per_actual;
		$ganancia_egreso_ids = implode(',', $egreso_id_array);

		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec INNER JOIN egreso e ON ec.egresocomision_id = e.egresocomision";
		$where = "ec.estadocomision IN (2,3) AND e.egreso_id IN ({$ganancia_egreso_ids})";
		$egreso_comision_per_actual = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_per_actual = (is_array($egreso_comision_per_actual)) ? $egreso_comision_per_actual[0]['ECOMISION'] : 0;
		$egreso_comision_per_actual = (is_null($egreso_comision_per_actual)) ? 0 : $egreso_comision_per_actual;

		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$egreso_cuentacorrienteproveedor_per_actual = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_per_actual = (is_array($egreso_cuentacorrienteproveedor_per_actual)) ? $egreso_cuentacorrienteproveedor_per_actual[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_per_actual = (is_null($egreso_cuentacorrienteproveedor_per_actual)) ? 0 : $egreso_cuentacorrienteproveedor_per_actual;

		$select = "ROUND(SUM(g.importe), 2) AS IMPORTETOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc ON gc.gastocategoria_id = g.gastocategoria INNER JOIN gastosubcategoria gsc ON gsc.gastosubcategoria_id = gc.gastosubcategoria";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$egreso_gasto_per_actual = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$egreso_gasto_per_actual = (is_array($egreso_gasto_per_actual)) ? $egreso_gasto_per_actual[0]['IMPORTETOTAL'] : 0;
		$egreso_gasto_per_actual = (is_null($egreso_gasto_per_actual)) ? 0 : $egreso_gasto_per_actual;

		$select = "s.producto_id AS PROD_ID";
		$from = "stock s";
		$groupby = "s.producto_id";
		$productoid_collection = CollectorCondition()->get('Stock', NULL, 4, $from, $select, $groupby);
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
				$this->stock = new Stock();
				$this->stock->stock_id = $stock_id['STOCK_ID'];
				$this->stock->get();

				$this->producto = new Producto();
				$this->producto->producto_id = $this->stock->producto_id;
				$this->producto->get();

				if ($this->producto->oculto == 0) {
					$costo_iva = $this->producto->costo + (($this->producto->costo * $this->producto->iva) / 100);
					$valor_stock_producto = round(($costo_iva * $this->stock->cantidad_actual),2);
					$stock_valorizado = $stock_valorizado + $valor_stock_producto;

					$this->stock->producto = $this->producto;
					$this->stock->valor_stock = $valor_stock_producto;
					unset($this->stock->producto_id);
					if ($this->stock->cantidad_actual < $this->producto->stock_minimo) {
						$stock_collection[] = $this->stock;
					}
				}
			}
		}

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA, ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$estado_cuentacorrientecliente = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);
		if (is_array($estado_cuentacorrientecliente) AND !empty($estado_cuentacorrientecliente)) {
			$estado_cuentacorrientecliente = $estado_cuentacorrientecliente[0]['TDEUDA'] - $estado_cuentacorrientecliente[0]['TINGRESO'];
		} else {
			$estado_cuentacorrientecliente = 0;
		}

		$select = "ccp.proveedor_id AS PID, p.razon_social AS PROVEEDOR, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.proveedor_id";
		$cuentacorrienteproveedor_total = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select, $groupby);
		if (is_array($cuentacorrienteproveedor_total)) {
			$deuda_cuentacorrienteproveedor = 0;
			foreach ($cuentacorrienteproveedor_total as $clave=>$valor) {
				$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
				$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
				$cuenta = round(($ingreso - $deuda),2);
				$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
				$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
				$deuda_cuentacorrienteproveedor = $deuda_cuentacorrienteproveedor + $cuenta;

			}
		} else {
			$deuda_cuentacorrienteproveedor = 0;
		}

		$deuda_cuentacorrienteproveedor = abs($deuda_cuentacorrienteproveedor);
		$deuda_cuentacorrienteproveedor = ($deuda_cuentacorrienteproveedor > 0.5) ? $deuda_cuentacorrienteproveedor : 0;

		$select = "e.egreso_id AS EID, e.importe_total AS IMPTOTAL, ec.valor_comision AS VALCOM ";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id";
		$where = "ec.estadocomision = 1 AND ec.fecha IS NOT NULL";
		$egreso_ids_comision = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$egreso_ids_comision = (is_array($egreso_ids_comision) AND !empty($egreso_ids_comision)) ? $egreso_ids_comision : array();

		$deuda_comision_total = 0;
		foreach ($egreso_ids_comision as $clave=>$valor) {
			$tmp_valor_comision = 0;
			$array_egreso_id = $valor['EID'];
			$array_importe_total = $valor['IMPTOTAL'];
			$array_valor_comision = $valor['VALCOM'];
			$select = "nc.notacredito_id AS NCID";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$array_egreso_id}";
			$tmp_notacredito_array = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($tmp_notacredito_array) AND !empty($tmp_notacredito_array)) {
				$tmp_notacredito_id = $tmp_notacredito_array[0]['NCID'];
				$ncm = new NotaCredito();
				$ncm->notacredito_id = $tmp_notacredito_id;
				$ncm->get();
				$nc_importe_total = $ncm->importe_total;

				$array_importe_total = $array_importe_total - $nc_importe_total;
			}

			$tmp_valor_comision = round(($array_valor_comision * $array_importe_total / 100), 2);
			$deuda_comision_total = $deuda_comision_total + $tmp_valor_comision;
		}

		//GRÁFICOS
		$select = "CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(valor_abonado),2) AS ECOMISION ";
		$from = "egresocomision ec INNER JOIN egreso e ON ec.egresocomision_id = e.egresocomision INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "ec.estadocomision IN (2,3) AND ec.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$group_by = "v.vendedor_id, ec.fecha ORDER BY SUM(valor_abonado) DESC";
		$pagocomisiones_collection = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select, $group_by);

		//ENTREGAS PENDIENTES
		$select = "hr.egreso_ids AS IDS";
		$from = "hojaruta hr";
		$where = "hr.estadoentrega = 3";
		$hojaruta_collection = CollectorCondition()->get('HojaRuta', $where, 4, $from, $select);
		$array_egreso_ids = array();
		foreach ($hojaruta_collection as $clave=>$valor) {
			$array_tuplas = explode(",", $valor['IDS']);
			foreach ($array_tuplas as $tupla) {
				$ids = explode("@", $tupla);
				$egreso_id = $ids[0];
				$estadoentrega_id = $ids[1];
				if(!in_array($egreso_id, $array_egreso_ids) AND $estadoentrega_id == 3) $array_egreso_ids[] = $egreso_id;
			}
		}

		$egreso_ids = implode(',', $array_egreso_ids);
		$select = "ROUND(SUM(e.importe_total), 2) CONTADO";
		$from = "egreso e";
		$where = "e.egreso_id IN ({$egreso_ids}) AND e.condicionpago = 2";
		$carga_pendiente = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$carga_pendiente = (is_array($carga_pendiente) AND !empty($carga_pendiente)) ? $carga_pendiente[0]['CONTADO'] : 0;

		$activo_corriente = 0;
		if ($cbm->activo_stock_valorizado == 'checked') {
			$activo_corriente = $activo_corriente + $stock_valorizado;
		}

		if ($cbm->activo_cuenta_corriente_cliente == 'checked') {
			$activo_corriente = $activo_corriente + $estado_cuentacorrientecliente;
		}

		if ($cbm->activo_carga_pendiente == 'checked') {
			$activo_corriente = $activo_corriente + $carga_pendiente;
		}

		$pasivo_corriente = 0;
		if ($cbm->pasivo_comisiones_pendientes == 'checked') {
			$pasivo_corriente = $pasivo_corriente + $deuda_comision_total;
		}

		if ($cbm->pasivo_cuenta_corriente_proveedor == 'checked') {
			$pasivo_corriente = $pasivo_corriente + $deuda_cuentacorrienteproveedor;
		}

		$cajadiaria = $this->calcula_cajadiaria();

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$ingreso_cuentacorriente_per_actual = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_per_actual = (is_array($ingreso_cuentacorriente_per_actual) AND !empty($ingreso_cuentacorriente_per_actual)) ? $ingreso_cuentacorriente_per_actual[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_per_actual = (is_null($ingreso_cuentacorriente_per_actual)) ? 0 : $ingreso_cuentacorriente_per_actual;

		$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega esen ON ee.estadoentrega = esen.estadoentrega_id";
		$where = "e.condicionpago = 2 AND ee.fecha BETWEEN '{$desde}' AND '{$hasta}' AND esen.estadoentrega_id = 4";
		$sum_contado_per_actual = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado_per_actual = (is_array($sum_contado_per_actual) AND !empty($sum_contado_per_actual)) ? $sum_contado_per_actual[0]['CONTADO'] : 0;
		$sum_contado_per_actual = (is_null($sum_contado_per_actual)) ? 0 : $sum_contado_per_actual;

		$select = "ROUND(SUM(ed.valor_ganancia),2) AS GANANCIA";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id";
		$where = "e.egreso_id IN ({$ganancia_egreso_ids}) AND c.impacto_ganancia = 1";
		$sum_ganancia_per_actual = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_ganancia_per_actual = (is_array($sum_ganancia_per_actual) AND !empty($sum_ganancia_per_actual)) ? $sum_ganancia_per_actual[0]['GANANCIA'] : 0;
		$sum_ganancia_per_actual = (is_null($sum_ganancia_per_actual)) ? 0 : $sum_ganancia_per_actual;

		$select = "ROUND(SUM(ncd.valor_ganancia),2) AS GANANCIA";
		$from = "notacredito nc INNER JOIN notacreditodetalle ncd ON nc.notacredito_id = ncd.notacredito_id INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id";
		$where = "e.egreso_id IN ({$ganancia_egreso_ids}) AND c.impacto_ganancia = 1";
		$rest_nc_ganancia_per_actual = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$rest_nc_ganancia_per_actual = (is_array($rest_nc_ganancia_per_actual) AND !empty($rest_nc_ganancia_per_actual)) ? $rest_nc_ganancia_per_actual[0]['GANANCIA'] : 0;
		$rest_nc_ganancia_per_actual = (is_null($rest_nc_ganancia_per_actual)) ? 0 : $rest_nc_ganancia_per_actual;

		$select = "ROUND(SUM(vc.importe), 2) AS TOTAL";
		$from = "vehiculocombustible vc";
		$where = "vc.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$vehiculocombustible_total = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);
		$vehiculocombustible_total = (is_array($vehiculocombustible_total) AND !empty($vehiculocombustible_total)) ? $vehiculocombustible_total[0]['TOTAL'] : 0;
		$vehiculocombustible_total = (is_null($vehiculocombustible_total)) ? 0 : $vehiculocombustible_total;

		//SALARIO
		$select = "ROUND(SUM(s.monto), 2) AS TOTAL";
		$from = "salario s";
		$where = "s.fecha BETWEEN '{$desde}' AND '{$hasta}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$salario_total = CollectorCondition()->get('Salario', $where, 4, $from, $select);
		$salario_total = (is_array($salario_total) AND !empty($salario_total)) ? $salario_total[0]['TOTAL'] : 0;
		$salario_total = (is_null($salario_total)) ? 0 : $salario_total;

		//GANANCIA DIARIA
		$select = "v.vendedor_id, FORMAT((SUM(ed.valor_ganancia)), 2,'de_DE') AS GANANCIA, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}' AND c.impacto_ganancia = 1";
		$groupby = "v.vendedor_id";
		$ganancia_vendedor_dia = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ganancia_vendedor_dia = (is_array($ganancia_vendedor_dia) AND !empty($ganancia_vendedor_dia)) ? $ganancia_vendedor_dia : array();
		
		//CREDITO PROVEEDORES
		$select = "p.proveedor_id, FORMAT((SUM(cpd.importe)), 2,'de_DE') AS CREDITO, p.razon_social AS PROVEEDOR";
		$from = "creditoproveedordetalle cpd INNER JOIN proveedor p ON cpd.proveedor = p.proveedor_id";
		$where = "cpd.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "p.proveedor_id";
		$creditoproveedordetalle_collection = CollectorCondition()->get('CreditoProveedorDetalle', $where, 4, $from, $select, $groupby);
		$creditoproveedordetalle_collection = (is_array($creditoproveedordetalle_collection) AND !empty($creditoproveedordetalle_collection)) ? $creditoproveedordetalle_collection : array();

		$ganancia_per_actual = $sum_ganancia_per_actual - $rest_nc_ganancia_per_actual - $egreso_comision_per_actual - $egreso_gasto_per_actual - $vehiculocombustible_total - $salario_total;

		$array_balance = array('{suma_ingresos_per_actual}'=>number_format($suma_ingresos_per_actual, 2, ',', '.'),
							   '{suma_notacredito_per_actual}'=>number_format($suma_notacredito_per_actual, 2, ',', '.'),
							   '{total_ingresos_per_actual}'=>number_format($total_ingresos_per_actual, 2, ',', '.'),
							   '{egreso_comision_per_actual}'=>number_format($egreso_comision_per_actual, 2, ',', '.'),
							   '{egreso_salario}'=>number_format($salario_total, 2, ',', '.'),
							   '{egreso_cuentacorrienteproveedor_per_actual}'=>number_format($egreso_cuentacorrienteproveedor_per_actual, 2, ',', '.'),
							   '{egreso_gasto_per_actual}'=>number_format($egreso_gasto_per_actual, 2, ',', '.'),
							   '{egreso_combustible}'=>number_format($vehiculocombustible_total, 2, ',', '.'),
							   '{stock_valorizado}'=>number_format($stock_valorizado, 2, ',', '.'),
							   '{deuda_ccclientes}'=>number_format($estado_cuentacorrientecliente, 2, ',', '.'),
							   '{carga_pendiente}'=>number_format($carga_pendiente, 2, ',', '.'),
							   '{stock_valorizado_graph}'=>$stock_valorizado,
							   '{deuda_ccclientes_graph}'=>$estado_cuentacorrientecliente,
							   '{carga_pendiente_graph}'=>$carga_pendiente,
							   '{deuda_ccproveedores}'=>number_format($deuda_cuentacorrienteproveedor, 2, ',', '.'),
							   '{deuda_comisiones}'=>number_format($deuda_comision_total, 2, ',', '.'),
							   '{deuda_ccproveedores_graph}'=>$deuda_cuentacorrienteproveedor,
							   '{deuda_comisiones_graph}'=>$deuda_comision_total,
							   '{cajadiaria}'=>number_format($cajadiaria, 2, ',', '.'),
							   '{activo_corriente}'=>number_format($activo_corriente, 2, ',', '.'),
							   '{pasivo_corriente}'=>number_format($pasivo_corriente, 2, ',', '.'),
							   '{ganancia_per_actual}'=>number_format($ganancia_per_actual, 2, ',', '.'));

		$select = "CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, FORMAT((SUM(s.monto)), 2,'de_DE') AS IMPORTE";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = "s.fecha BETWEEN '{$desde}' AND '{$hasta}' AND s.tipo_pago IN ('SALARIO', 'ADELANTO')";
		$groupby = "s.empleado";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select,$groupby);

		$select = "v.dominio AS DOMINIO, v.denominacion AS REFERENCIA, CONCAT(vma.denominacion, ' ', vm.denominacion) AS VEHICULO, FORMAT((SUM(vc.importe)), 2,'de_DE') AS TIMPORTE, FORMAT((SUM(vc.cantidad)), 2,'de_DE') AS TLITRO";
		$from = "vehiculocombustible vc INNER JOIN vehiculo v ON vc.vehiculo = v.vehiculo_id INNER JOIN vehiculomodelo vm ON v.vehiculomodelo = vm.vehiculomodelo_id INNER JOIN vehiculomarca vma ON vm.vehiculomarca = vma.vehiculomarca_id";
		$where = "vc.fecha BETWEEN '{$desde}' AND '{$hasta}' GROUP BY vc.vehiculo ORDER BY SUM(vc.importe) DESC";
		$vehiculocombustible_collection = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', NULL, 4, $from, $select, $groupby);

		$productomarca_collection = Collector()->get('ProductoMarca');

		$this->view->balance($array_balance, $pagocomisiones_collection, $periodo, $cbm, $vehiculocombustible_collection, $producto_collection, $productomarca_collection, $salario_collection, $ganancia_vendedor_dia, $creditoproveedordetalle_collection);
	}

	function ver_detalle_resultado_ganancia() {
		SessionHandler()->check_session();
		if (isset($_SESSION["data-search-balance-" . APP_ABREV])) {
			$desde = $_SESSION["data-search-balance-" . APP_ABREV]['desde'];
			$hasta = $_SESSION["data-search-balance-" . APP_ABREV]['hasta'];
			$where_egreso = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
			$where_notacredito = "nc.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		} else {
			$periodo_actual = date('Ym');
			$where_egreso = "date_format(e.fecha, '%Y%m') = '{$periodo_actual}'";
			$where_notacredito = "date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
		}

		$select = "CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0))
      			   ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA,
      			   c.razon_social AS CLIENTE, SUM(ROUND(((((p.porcentaje_ganancia - ed.descuento) / 100 + 1) * (ed.cantidad * ed.costo_producto)) - (ed.cantidad * ed.costo_producto)),2)) AS GANANCIA";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN
				 cliente c ON e.cliente = c.cliente_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$groupby = "e.egreso_id";
		$detalle_ganancia_per_actual = CollectorCondition()->get('Egreso', $where_egreso, 4, $from, $select, $groupby);
		$detalle_ganancia_per_actual = (is_array($detalle_ganancia_per_actual) AND !empty($detalle_ganancia_per_actual)) ? $detalle_ganancia_per_actual : array();

		$select = "CONCAT(tf.nomenclatura, ' ', LPAD(nc.punto_venta, 4, 0), '-', LPAD(nc.numero_factura, 8, 0)) END AS NOTACREDITO,
      			   c.razon_social AS CLIENTE, SUM(ROUND(((((p.porcentaje_ganancia - ncd.descuento) / 100 + 1) * (ncd.cantidad * ncd.costo_producto)) - (ed.cantidad * ed.costo_producto)),2)) AS GANANCIA";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN
				 cliente c ON e.cliente = c.cliente_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$groupby = "e.egreso_id";
		$detalle_ganancia_per_actual = CollectorCondition()->get('Egreso', $where_egreso, 4, $from, $select, $groupby);
		$detalle_ganancia_per_actual = (is_array($detalle_ganancia_per_actual) AND !empty($detalle_ganancia_per_actual)) ? $detalle_ganancia_per_actual : array();
		print_r($detalle_ganancia_per_actual);exit;

	}

	function reportes() {
		SessionHandler()->check_session();
		 $user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
    	$fecha_sys = strtotime(date('Y-m-d'));
		$periodo_minimo = date("Ym", strtotime("-6 month", $fecha_sys));
    	$periodo_actual = date('Ym');

    	$select = "ed.codigo_producto AS COD, ed.descripcion_producto AS PRODUCTO, ROUND(SUM(ed.importe),2) AS IMPORTE,
				   ROUND(SUM(ed.cantidad),2) AS CANTIDAD, ed.producto_id AS PRID";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id";
		$where = "date_format(e.fecha, '%Y%m') = '{$periodo_actual}'";
		
		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.importe),2) DESC";
		$sum_importe_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.cantidad),2) DESC";
		$sum_cantidad_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(ncd.importe),2) AS IMPORTE, ROUND(SUM(ncd.cantidad),2) AS CANTIDAD";
		$from = "notacreditodetalle ncd INNER JOIN notacredito nc ON ncd.notacredito_id = nc.notacredito_id";
		foreach ($sum_importe_producto as $clave=>$valor) {
			$tmp_producto_id = $valor["PRID"];
			$where = "ncd.producto_id = {$tmp_producto_id} AND date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
			$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
				$nuevo_valor_importe = $sum_importe_producto[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
				$nuevo_valor_cantidad = $sum_importe_producto[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
			} else {
				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
			}

			$sum_importe_producto[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
			$sum_importe_producto[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
		}

		foreach ($sum_cantidad_producto as $clave=>$valor) {
			$tmp_producto_id = $valor["PRID"];
			$where = "ncd.producto_id = {$tmp_producto_id} AND date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
			$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
				$nuevo_valor_importe = $sum_cantidad_producto[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
				$nuevo_valor_cantidad = $sum_cantidad_producto[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
			} else {
				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
			}

			$sum_cantidad_producto[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
			$sum_cantidad_producto[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
		}

		$select = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from = "vendedor v";
		$where = "v.oculto = 0 ORDER BY CONCAT(v.apellido, ' ', v.nombre) ASC";
		$vendedor_collection = CollectorCondition()->get('Vendedor', $where, 4, $from, $select);

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "p.oculto = 0";
		$groupby = "p.producto_id ORDER BY CONCAT(pm.denominacion, ' ', p.denominacion) ASC";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select, $groupby);
		$productomarca_collection = Collector()->get('ProductoMarca');
		$gastocategoria_collection = Collector()->get('GastoCategoria');

		$select = "p.proveedor_id AS ID, p.razon_social AS DENOMINACION";
		$from = "proveedor p";
		$where = "p.oculto = 0 ORDER BY p.razon_social ASC";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);

		$select = "cl.cliente_id AS ID,cl.razon_social AS RAZON_SOCIAL, cl.nombre_fantasia AS NOMBRE_FANTASIA";
		$from = "cliente cl";
		$where = "cl.oculto = 0 ORDER BY c.razon_social ASC";
		$clientes_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);

		$this->view->reportes($sum_importe_producto, $sum_cantidad_producto, $vendedor_collection, $producto_collection, $gastocategoria_collection, $productomarca_collection, $proveedor_collection,$user_level,$clientes_collection);
	}

	function calcula_cajadiaria() {
		$fecha_sys = date('Y-m-d');

    	$select = "cd.cajadiaria_id AS ID";
		$from = "cajadiaria cd ORDER BY cd.fecha DESC LIMIT 1";
		$cajadiaria_id = CollectorCondition()->get('CajaDiaria', NULL, 4, $from, $select);
		$cajadiaria_id = (is_array($cajadiaria_id) AND !empty($cajadiaria_id)) ? $cajadiaria_id[0]['ID'] : 0;
		$cajadiaria_id = (is_null($cajadiaria_id)) ? 0 : $cajadiaria_id;

		$cdm = new CajaDiaria();
		$cdm->cajadiaria_id = $cajadiaria_id;
		$cdm->get();
		$cajadiaria = $cdm->caja;
		$fecha_cajadiaria = $cdm->fecha;

    	$select = "ROUND(SUM(e.importe_total),2) AS CONTADO";
		$from = "egreso e INNER JOIN egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega esen ON ee.estadoentrega = esen.estadoentrega_id";
		$where = "e.condicionpago = 2 AND ee.fecha = '{$fecha_sys}' AND esen.estadoentrega_id = 4";
		$sum_contado = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$sum_contado = (is_array($sum_contado)) ? $sum_contado[0]['CONTADO'] : 0;
		$sum_contado = (is_null($sum_contado)) ? 0 : $sum_contado;

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.fecha = '{$fecha_sys}' AND ccc.ingresotipopago = 3";
		$ingreso_cuentacorriente_hoy = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		$ingreso_cuentacorriente_hoy = (is_array($ingreso_cuentacorriente_hoy)) ? $ingreso_cuentacorriente_hoy[0]['TINGRESO'] : 0;
		$ingreso_cuentacorriente_hoy = (is_null($ingreso_cuentacorriente_hoy)) ? 0 : $ingreso_cuentacorriente_hoy;

		//COBRANZA
		$cobranza = $sum_contado + $ingreso_cuentacorriente_hoy;

		$fecha_dia = date('Y-m-d');
		$select = "e.egreso_id AS EGRESO_ID, e.importe_total AS IMPORTETOTAL";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
				 egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN
				 egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "e.fecha = '{$fecha_sys}'";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$suma_ingresos_hoy = 0;
		$suma_notacredito_hoy = 0;
		$total_facturacion_hoy = 0;
		if (is_array($egresos_collection) AND !empty($egresos_collection)) {
			foreach ($egresos_collection as $clave=>$valor) {
				$egreso_importe_total = $egresos_collection[$clave]['IMPORTETOTAL'];

				$egreso_id = $valor['EGRESO_ID'];
				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$suma_notacredito_hoy = $suma_notacredito_hoy + $importe_notacredito;
				}

				$suma_ingresos_hoy = $suma_ingresos_hoy + $egreso_importe_total;
			}
		}

		//VENTAS DEL DÍA
		$total_facturacion_hoy = $suma_ingresos_hoy - $suma_notacredito_hoy;

		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 OR ccp.tipomovimientocuenta = 3 THEN ccp.importe ELSE 0 END),2) AS TSALIDA";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.fecha = '{$fecha_sys}' AND ccp.ingresotipopago NOT IN (1,4)";
		$egreso_cuentacorrienteproveedor_hoy = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$egreso_cuentacorrienteproveedor_hoy = (is_array($egreso_cuentacorrienteproveedor_hoy)) ? $egreso_cuentacorrienteproveedor_hoy[0]['TSALIDA'] : 0;
		$egreso_cuentacorrienteproveedor_hoy = (is_null($egreso_cuentacorrienteproveedor_hoy)) ? 0 : $egreso_cuentacorrienteproveedor_hoy;

		$select = "ROUND(SUM(i.costo_total_iva),2) AS PROVCONT";
		$from = "ingreso i";
		$where = "i.fecha = '{$fecha_sys}' AND i.condicionpago = 2";
		$egreso_contadoproveedor_hoy = CollectorCondition()->get('Ingreso', $where, 4, $from, $select);
		$egreso_contadoproveedor_hoy = (is_array($egreso_contadoproveedor_hoy)) ? $egreso_contadoproveedor_hoy[0]['PROVCONT'] : 0;
		$egreso_contadoproveedor_hoy = (is_null($egreso_contadoproveedor_hoy)) ? 0 : $egreso_contadoproveedor_hoy;

		//PAGO PROVEEDORES
		$pago_proveedores = $egreso_cuentacorrienteproveedor_hoy + $egreso_contadoproveedor_hoy;

		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egresocomision ec";
		$where = "ec.fecha = '{$fecha_sys}' AND ec.estadocomision IN (2,3)";
		$egreso_comision_hoy = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_hoy = (is_array($egreso_comision_hoy)) ? $egreso_comision_hoy[0]['ECOMISION'] : 0;

		//PAGO COMISIONES
		$egreso_comision_hoy = (is_null($egreso_comision_hoy)) ? 0 : $egreso_comision_hoy;

		$select = "ROUND(SUM(g.importe), 2) AS IMPORTETOTAL";
		$from = "gasto g";
		$where = "g.fecha = '{$fecha_sys}'";
		$gasto_diario = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$gasto_diario = (is_array($gasto_diario)) ? $gasto_diario[0]['IMPORTETOTAL'] : 0;

		//GASTO DIARIO
		$gasto_diario = (is_null($gasto_diario)) ? 0 : $gasto_diario;

		//GASTO COMBUSTIBLE
		$select = "ROUND(SUM(vc.importe), 2) AS TOTAL";
		$from = "vehiculocombustible vc";
		$where = "vc.fecha = '{$fecha_sys}'";
		$vehiculocombustible_total = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);
		$vehiculocombustible_total = (is_array($vehiculocombustible_total) AND !empty($vehiculocombustible_total)) ? $vehiculocombustible_total[0]['TOTAL'] : 0;
		$vehiculocombustible_total = (is_null($vehiculocombustible_total)) ? 0 : $vehiculocombustible_total;

		//SALARIO
		$select = "ROUND(SUM(s.monto), 2) AS TOTAL";
		$from = "salario s";
		$where = "s.fecha = '{$fecha_sys}'";
		$salario_total = CollectorCondition()->get('Salario', $where, 4, $from, $select);
		$salario_total = (is_array($salario_total) AND !empty($salario_total)) ? $salario_total[0]['TOTAL'] : 0;
		$salario_total = (is_null($salario_total)) ? 0 : $salario_total;

		if ($fecha_cajadiaria == $fecha_sys) {
			$calculo_cajadiaria = round($cajadiaria,2);
		} else {
			$calculo_cajadiaria = round(($cajadiaria + $cobranza - $pago_proveedores - $egreso_comision_hoy - $gasto_diario - $vehiculocombustible_total - $salario_total),2);
		}

		return $calculo_cajadiaria;
	}

	function post_descarga() {
		SessionHandler()->check_session();
		require_once "tools/excelreport_tipo2.php";

		$fecha_sys = strtotime(date('Y-m-d'));
		$periodo_minimo = date("Ym", strtotime("-6 month", $fecha_sys));
    	$periodo_actual = date('Ym');

    	//PARAMETROS
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$gastocategoria_id = filter_input(INPUT_POST, 'gastocategoria');

		$tipo_busqueda = filter_input(INPUT_POST, 'tipo_busqueda');
		$tipo_filtro = filter_input(INPUT_POST, 'tipo_informe');
		switch ($tipo_busqueda) {
			case 1:
				$select = "e.egreso_id AS EGRESO_ID, date_format(e.fecha, '%d/%m/%Y') AS FECHA, UPPER(cl.razon_social) AS CLIENTE,
						   ci.denominacion AS CI, CONCAT(LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) AS BK,
						   e.subtotal AS SUBTOTAL, ese.denominacion AS ENTREGA, e.importe_total AS IMPORTETOTAL,
						   UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR, UPPER(cp.denominacion) AS CP,
						   CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0))
						   ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
				$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
						 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
						 egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id LEFT JOIN
						 egresoafip eafip ON e.egreso_id = eafip.egreso_id";
				$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY e.fecha DESC";
				$datos_reporte = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

				foreach ($datos_reporte as $clave=>$valor) {
					$egreso_id = $valor['EGRESO_ID'];
					$select = "nc.importe_total AS IMPORTETOTAL";
					$from = "notacredito nc";
					$where = "nc.egreso_id = {$egreso_id}";
					$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

					if (is_array($notacredito) AND !empty($notacredito)) {
						$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
						$datos_reporte[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
						$datos_reporte[$clave]['IMPORTETOTAL'] = $datos_reporte[$clave]['IMPORTETOTAL'] - $importe_notacredito;
						//$datos_reporte[$clave]['VC'] = round(($datos_reporte[$clave]['COMISION'] * $datos_reporte[$clave]['IMPORTETOTAL'] / 100),2);

					} else {
						$datos_reporte[$clave]['NC_IMPORTE_TOTAL'] = 0;
					}
				}

				$subtitulo = "VENTAS POR RANGO DE FECHA";
				$array_encabezados = array('FECHA', 'FACTURA', 'CLIENTE', 'VENDEDOR', 'PAGO', 'ENTREGA', 'IMPORTE');
				$array_exportacion = array();
				$array_exportacion[] = $array_encabezados;
				$sum_importe = 0;
				foreach ($datos_reporte as $clave=>$valor) {
					$sum_importe = $sum_importe + $valor["IMPORTETOTAL"];
					$array_temp = array();
					$array_temp = array(
								  $valor["FECHA"]
								, $valor["FACTURA"]
								, $valor["CLIENTE"]
								, $valor["VENDEDOR"]
								, $valor["CP"]
								, $valor["ENTREGA"]
								, $valor["IMPORTETOTAL"]);
					$array_exportacion[] = $array_temp;
				}

				$array_exportacion[] = array('', '', '', '', '', '', '');
				$array_exportacion[] = array('', '', '', '', '', 'TOTAL', $sum_importe);
				break;
			case 2:
				$tipo_reporte = filter_input(INPUT_POST, 'tipo_reporte');

				$vm = new Vendedor();
				$vm->vendedor_id = $vendedor_id;
				$vm->get();
				$razon_social = $vm->apellido . ' ' . $vm->nombre;

				$select = "e.egreso_id AS EGRESO_ID, e.fecha AS FECHA, cl.razon_social AS CLIENTE, ci.denominacion AS CI,
						   e.subtotal AS SUBTOTAL, ec.fecha AS FECCOM, ROUND(ec.valor_abonado,2) AS VALABO,
						   e.importe_total AS IMPORTETOTAL, CONCAT(ve.APELLIDO, ' ', ve.nombre) AS VENDEDOR, cp.denominacion AS CP,
						   ec.valor_comision AS COMISION, ROUND((ec.valor_comision * e.importe_total / 100),2) AS VC, esc.denominacion AS ESTCOM,
						   CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0))
						   ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
				$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
						 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
						 egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN estadocomision esc ON ec.estadocomision = esc.estadocomision_id LEFT JOIN
						 egresoafip eafip ON e.egreso_id = eafip.egreso_id";
				$where = "e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY e.fecha DESC";
				$datos_reporte = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

				foreach ($datos_reporte as $clave=>$valor) {
					$egreso_id = $valor['EGRESO_ID'];
					$select = "nc.importe_total AS IMPORTETOTAL";
					$from = "notacredito nc";
					$where = "nc.egreso_id = {$egreso_id}";
					$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

					if (is_array($notacredito) AND !empty($notacredito)) {
						$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
						$datos_reporte[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
						$datos_reporte[$clave]['IMPORTETOTAL'] = $datos_reporte[$clave]['IMPORTETOTAL'] - $importe_notacredito;
						$datos_reporte[$clave]['VC'] = round(($datos_reporte[$clave]['COMISION'] * $datos_reporte[$clave]['IMPORTETOTAL'] / 100),2);

					} else {
						$datos_reporte[$clave]['NC_IMPORTE_TOTAL'] = 0;
					}
				}

				if ($tipo_reporte == 1) {
					$subtitulo = "VENDEDOR: {$razon_social} - DESDE: {$desde}   HASTA {$hasta}";
					$array_encabezados = array('FECHA', 'FACTURA', 'CLIENTE', 'VENDEDOR', 'PAGO', 'COMISIÓN', 'ESTADO COMISIÓN', 'IMPORTE');
					$array_exportacion = array();
					$array_exportacion[] = $array_encabezados;
					$sum_importe = 0;
					foreach ($datos_reporte as $clave=>$valor) {
						$sum_importe = $sum_importe + $valor["IMPORTETOTAL"];
						$array_temp = array();
						$array_temp = array(
									  $valor["FECHA"]
									, $valor["FACTURA"]
									, $valor["CLIENTE"]
									, $valor["VENDEDOR"]
									, $valor["CP"]
									, $valor["COMISION"] . '%'
									, 'PAGO ' . $valor["ESTCOM"]
									, $valor["IMPORTETOTAL"]);
						$array_exportacion[] = $array_temp;
					}

					$array_exportacion[] = array('', '', '', '', '', '', '', '');
					$array_exportacion[] = array('', '', '', '', '', '', 'TOTAL', $sum_importe);
				} elseif ($tipo_reporte == 2) {

					$egreso_ids = array();
					foreach ($datos_reporte as $clave=>$valor) {
						if ($datos_reporte[$clave]['IMPORTETOTAL'] == 0 AND $datos_reporte[$clave]["VC"] == 0) {
							unset($datos_reporte[$clave]);
						} else {
							if (!in_array($datos_reporte[$clave]['EGRESO_ID'], $egreso_ids)) {
								$egreso_ids[] = $datos_reporte[$clave]['EGRESO_ID'];
							}
						}
					}

					$egreso_ids = implode(',', $egreso_ids);

					$select = "c.razon_social AS CLIENTE, COUNT(e.cliente) AS CANT";
					$from = "egreso e INNER JOIN cliente c ON e.cliente = c.cliente_id";
					$where = "e.egreso_id IN ({$egreso_ids}) AND e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
					$group_by = "c.cliente_id ORDER BY COUNT(e.cliente) DESC";
					$datos_reporte = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $group_by);

					$subtitulo = "VENDEDOR: {$razon_social} - DESDE: {$desde}   HASTA {$hasta}";
					$array_encabezados = array('CLIENTE', 'CANTIDAD VENTAS','','','');
					$array_exportacion = array();
					$array_exportacion[] = $array_encabezados;
					foreach ($datos_reporte as $clave=>$valor) {
						$array_temp = array();
						$array_temp = array(
									  $valor["CLIENTE"]
									, $valor["CANT"]
									, ''
									, ''
									, '');
						$array_exportacion[] = $array_temp;
					}
				}

				break;
		}

		ExcelReportTipo2()->extraer_informe($subtitulo, $array_exportacion);
		exit;
	}

	function generar_libro_iibb_ventas() {
		SessionHandler()->check_session();
		require_once 'core/helpers/libroIIBBVentas.php';
		//PARAMETROS
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');

		$libro_iibb_ventas = LibroIIBBVentas::get_libro_iibb_ventas($desde, $hasta);
		$directorio = URL_PRIVATE . "percepcion/";
		$archivo = 'SAP-LARIOJA.txt';
		$fp = fopen($directorio . $archivo, "a" )or die("Unable to open file!");

		foreach ($libro_iibb_ventas as $clave=>$valor) {
			$linea = '';
			$linea = $valor['DOC'] . $valor['IIBB'] . $valor['CLIENTE'] . $valor['DOMICILIO'] . $valor['FECHA'] . $valor['IMPORTE_TOTAL'] . $valor['BASE_IMPONIBLE'] . $valor['ALICUOTA'] . $valor['PERCEPCION'] . $valor['COMPROBANTE'];
			fwrite($fp, $linea);
			fwrite($fp, "\r\n");
		}

		fclose($fp);
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.$archivo);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($directorio . $archivo));
		header("Content-Type: text/plain");
		readfile($directorio . $archivo);
		unlink($directorio . $archivo);
		exit;
	}

	function generar_libro_iva_ventas() {
		SessionHandler()->check_session();
		require_once 'core/helpers/libroIVAVentas.php';
		require_once "tools/excelreport.php";
		//PARAMETROS
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');

		$libro_iva_ventas = LibroIvaVentas::get_libro_iva_ventas($desde, $hasta);
		$subtitulo = "LIBRO IVA VENTAS: {$desde} - {$hasta}";
		$array_encabezados = array('FECHA', 'TIPO', 'PUNTO DE VENTA', 'NÚMERO DESDE', 'NRO. DOC. RECEPTOR', 'DENOMINACIÓN RECEPTOR', 'IMP. NETO GRAVADO', 'IMP. NETO NO GRAVADO', 'IMP. OP. EXENTAS', 'IVA', 'IVA21', 'IVA10', 'IMP. TOTAL');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$total_iva = 0;
		$total_iva_21 = 0;
		$total_iva_10 = 0;
		$total = 0;
		foreach ($libro_iva_ventas as $clave=>$valor) {
			$total_iva = $total_iva + $valor["IVA"];
			$total_iva_21 = $total_iva_21 + $valor["IVA21"];
			$total_iva_10 = $total_iva_10 + $valor["IVA10"];
			$total = $total_iva_10 + $valor["IMP_TOTAL"];
			$array_temp = array();
			$array_temp = array(
						  $valor["FECHA"]
						, $valor["TIPOFACTURA"]
						, $valor["PTO_VENTA"]
						, $valor["NRO_DESDE"]
						, $valor["DOC_RECEPTOR"]
						, $valor["RECEPTOR"]
						, $valor["IMP_NETO_GRAVADO"]
						, $valor["IMP_NETO_NO_GRAVADO"]
						, $valor["IMP_OP_EXENTAS"]
						, $valor["IVA"]
						, $valor["IVA21"]
						, $valor["IVA10"]
						, $valor["IMP_TOTAL"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '', '', '', '', '', '', $total_iva, $total_iva_21, $total_iva_10, '');
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function generar_libro_iva_compras() {
		SessionHandler()->check_session();
		require_once 'core/helpers/libroIVACompras.php';
		require_once "tools/excelreport.php";
		//PARAMETROS
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');

		$libro_iva_compras = LibroIvaCompras::get_libro_iva_compras($desde, $hasta);
		$subtitulo = "LIBRO IVA COMPRAS: {$desde} - {$hasta}";
		$array_encabezados = array('Fecha', 'Cla', 'Comprobante', 'Proveedor', 'CUIT', 'Neto', 'Exento', 'I.V.A.', 'IVa D/10.5', 'IVA D/27', 'Imp.Internos', 'Ret.IVA', 'Ret.IIBB', 'Per.IVA', 'Per.IIBB', 'Per.GAN', 'C.No Gravado', 'Imp TEM', 'Per IIBB CF', 'Total');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$total_neto = 0;
		$total_exento = 0;
		$total_iva = 0;
		$total_iva_10 = 0;
		$total_iva_27 = 0;
		$total_impint = 0;
		$total_retiva = 0;
		$total_retiibb = 0;
		$total_periva = 0;
		$total_periibb = 0;
		$total_pergan = 0;
		$total_nogravado = 0;
		$total_imptem = 0;
		$total_periibbcf = 0;
		$total = 0;
		foreach ($libro_iva_compras as $clave=>$valor) {
			$total_neto = $total_neto + $valor['NETO'];
			$total_exento = $total_exento + $valor['EXENTO'];
			$total_iva = $total_iva + $valor['IVA'];
			$total_iva_10 = $total_iva_10 + $valor['IVA10'];
			$total_iva_27 = $total_iva_27 + $valor['IVA27'];
			$total_impint = $total_impint + $valor['IMPINTERNO'];
			$total_retiva = $total_retiva + $valor['RETIVA'];
			$total_retiibb = $total_retiibb + $valor['RETIIBB'];
			$total_periva = $total_periva + $valor['PERIVA'];
			$total_periibb = $total_periibb + $valor['PERIIBB'];
			$total_pergan = $total_pergan + $valor['PERGANANCIA'];
			$total_nogravado = $total_nogravado + $valor['CNOGRAVADO'];
			$total_imptem = $total_imptem + $valor['IMPTEM'];
			$total_periibbcf = $total_periibbcf + $valor['PERIIBBCF'];
			$total = $total + $valor['TOTAL'];
			$array_temp = array();
			$array_temp = array($valor["FECHA"]
								, $valor["CLA"]
								, $valor["COMPROBANTE"]
								, $valor["PROVEEDOR"]
								, $valor["CUIT"]
								, $valor["NETO"]
								, $valor["EXENTO"]
								, $valor["IVA"]
								, $valor["IVA10"]
								, $valor["IVA27"]
								, $valor["IMPINTERNO"]
								, $valor["RETIVA"]
								, $valor["RETIIBB"]
								, $valor["PERIVA"]
								, $valor["PERIIBB"]
								, $valor["PERGANANCIA"]
								, $valor["CNOGRAVADO"]
								, $valor["IMPTEM"]
								, $valor["PERIIBBCF"]
								, $valor["TOTAL"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('TOTALES', '', '', '', '', $total_neto, $total_exento, $total_iva, $total_iva_10, $total_iva_27, $total_impint, $total_retiva, $total_retiibb, $total_periva, $total_periibb, $total_pergan, $total_nogravado, $total_imptem, $total_periibbcf, $total);
		$array_exportacion[] = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Neto', $total_neto, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Exento', $total_exento, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'I.v.a.', $total_iva, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Iva D/10.5', $total_iva_10, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Iva D/27', $total_iva_27, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Impuestos', $total_impint, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Retencion Iva', $total_retiva, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Retencion IIBB', $total_retiibb, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Percepcion Iva', $total_periva, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Percepcion Ing.Brutos', $total_periibb, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Percepcion Ganancia', $total_pergan, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Conceptos No Gravados', $total_nogravado, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Impuestos TEM', $total_imptem, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Per IIBB Cap Fed', $total_periibbcf, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'Total General', $total, '', '', '', '', '', '', '', '', '', '', '', '', '', '');

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function clientes_no_compra_x_dias() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$dias = filter_input(INPUT_POST, 'dias');
		$cliente = filter_input(INPUT_POST, 'cliente');

		$select = "cl.cliente_id AS ID,cl.razon_social AS RAZON_SOCIAL, cl.nombre_fantasia AS NOMBRE_FANTASIA, cl.documento AS DOCUMENTO,p.denominacion AS PROVINCIA";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN provincia p ON cl.provincia = p.provincia_id";
		$where_cliente = "e.fecha BETWEEN CURDATE() - INTERVAL {$dias} DAY AND CURDATE() AND cl.oculto = 0";
		$where = ($cliente == 'all') ? $where_cliente : "{$where_cliente} AND cl.cliente_id = {$cliente} AND cl.oculto = 0";
		$groupby = 'e.cliente ORDER BY cl.razon_social ASC';

		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select,$groupby);

		$select = "cl.cliente_id AS ID,cl.razon_social AS RAZON_SOCIAL, cl.nombre_fantasia AS NOMBRE_FANTASIA, cl.documento AS DOCUMENTO,p.denominacion AS PROVINCIA";
		$from = "cliente cl INNER JOIN provincia p ON cl.provincia = p.provincia_id";
		$where = "cl.oculto = 0  ORDER BY cl.razon_social ASC";
		$clientes_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);

		$subtitulo = "Lista de clientes que no compran hace X({$dias}) Días";
		$array_encabezados = array('COD', 'CLIENTE', 'NOM FANTASIA', 'DOCUMENTO', 'PROVINCIA');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;

		if ($cliente == 'all') {
			if (is_array($egresos_collection)) {
				$newArray = array();
				foreach ($clientes_collection as $key => $cliente) {
					if (array_search($cliente['ID'], array_column($egresos_collection, 'ID')) === FALSE) {
						$newArray[] = array('ID'=>$cliente['ID'],'RAZON_SOCIAL'=>$cliente['RAZON_SOCIAL'], 'NOMBRE_FANTASIA'=>$cliente['NOMBRE_FANTASIA'],'DOCUMENTO'=>$cliente['DOCUMENTO'],'PROVINCIA'=>$cliente['PROVINCIA']);
					}
				}
			} else {
				$newArray = $clientes_collection;
			}
		} else {
			if (is_array($egresos_collection)) {
				$newArray = array();
			} else {
				$select = "cl.cliente_id AS ID,cl.razon_social AS RAZON_SOCIAL, cl.nombre_fantasia AS NOMBRE_FANTASIA, cl.documento AS DOCUMENTO,p.denominacion AS PROVINCIA";
				$from = "cliente cl INNER JOIN provincia p ON cl.provincia = p.provincia_id";
				$where = "cl.cliente_id = {$cliente} AND cl.oculto = 0";
				$clientes_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
				$newArray = $clientes_collection;
			}
		}

		foreach ($newArray as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array(
							$valor["ID"]
						,	$valor["RAZON_SOCIAL"]
						, $valor["NOMBRE_FANTASIA"]
						, $valor["DOCUMENTO"]
						, $valor["PROVINCIA"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	

	function reporte_balance_producto() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$fecha_desde = filter_input(INPUT_POST, 'fecha_desde');
		$fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta');

		if (!empty($_POST['producto_id'])) {
			$producto_ids = $_POST['producto_id'];
			$producto_ids = implode(',', $producto_ids);

			$select = "ed.producto_id AS ID,ed.descripcion_producto AS PRODUCTO,ed.codigo_producto AS CODIGO,ROUND(SUM(ed.valor_descuento), 2) AS VALOR_DESCUENTO,
			sum(ROUND((ROUND((ROUND(costo_producto-ROUND((((ed.neto_producto + (ed.neto_producto * ed.flete_producto / 100)) * p.iva / 100) + (ed.neto_producto + (ed.neto_producto * ed.flete_producto /100))),2),2)-ROUND(ed.descuento*ROUND(costo_producto-ROUND((((ed.neto_producto + (ed.neto_producto * ed.flete_producto / 100)) * p.iva / 100) + (ed.neto_producto + (ed.neto_producto * ed.flete_producto /100))),2),2)/100,2)),2))*ed.cantidad,2)) AS RENTABILIDAD";
		  $from = "egresodetalle ed INNER JOIN egreso e ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON p.producto_id = ed.producto_id
			LEFT JOIN notacredito n ON n.egreso_id = ed.egreso_id";
			$where = "ed.producto_id IN ({$producto_ids}) AND e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' AND n.notacredito_id IS NULL";
			$group_by = "ed.producto_id";
			$egresos_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select,$group_by);

			$select = "ed.producto_id AS ID,ed.descripcion_producto AS PRODUCTO,ed.codigo_producto AS CODIGO,'0.0' AS VALOR_DESCUENTO";
			$from = " egresodetalle ed";
			$where = "ed.producto_id IN ({$producto_ids})";
			$group_by = "ed.producto_id";
			$productos_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select,$group_by);

			foreach ($productos_collection as $clave => $value) {
				$key = array_search($value['ID'], array_column($egresos_collection, 'ID'));
				if ($key === FALSE){ array_push($egresos_collection, $productos_collection[$clave]);}
			}

			$subtitulo = "Reporte de Bonificación periodo ({$fecha_desde} - {$fecha_hasta})";
			$array_encabezados = array('DETALLE', 'COD', 'BONIFICACIÓN', 'RENTABILIDAD','');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;

		$sum_importe = 0;
			$sum_rentabilidad = 0;
		foreach ($egresos_collection as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["VALOR_DESCUENTO"];
				$sum_rentabilidad = $sum_rentabilidad + $valor["RENTABILIDAD"];
			$array_temp = array();
			$array_temp = array(
							$valor["PRODUCTO"]
						,	$valor["CODIGO"]
							, $valor["VALOR_DESCUENTO"]
							, $valor["RENTABILIDAD"]);
			$array_exportacion[] = $array_temp;
		}

			$array_exportacion[] = array('', '', '', '','');
			$array_exportacion[] = array('', 'TOTAL', $sum_importe, $sum_rentabilidad,'');
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

}

	function reporte_balance_marca() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$fecha_desde = filter_input(INPUT_POST, 'fecha_desde');
		$fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta');

		if (!empty($_POST['marca_id'])) {
			$marca_ids = $_POST['marca_id'];
			$marca_ids = implode(',', $marca_ids);

			$select = "pm.productomarca_id AS ID,pm.denominacion AS MARCA,ROUND(SUM(ed.valor_descuento), 2) AS VALOR_DESCUENTO,
				sum(ROUND((ROUND((ROUND(costo_producto-ROUND((((ed.neto_producto + (ed.neto_producto * ed.flete_producto / 100)) * p.iva / 100) + (ed.neto_producto + (ed.neto_producto * ed.flete_producto /100))),2),2)-ROUND(ed.descuento*ROUND(costo_producto-ROUND((((ed.neto_producto + (ed.neto_producto * ed.flete_producto / 100)) * p.iva / 100) + (ed.neto_producto + (ed.neto_producto * ed.flete_producto /100))),2),2)/100,2)),2))*ed.cantidad,2)) AS RENTABILIDAD,
				ROUND(SUM(ed.importe),2) AS IMPORTE_FACTURADO";
			$from = "egresodetalle ed INNER JOIN egreso e ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id
			INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id LEFT JOIN notacredito n ON n.egreso_id = ed.egreso_id";
			$where = "pm.productomarca_id IN ({$marca_ids}) AND e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' AND n.notacredito_id IS NULL";
			$group_by = "pm.productomarca_id ORDER BY ed.descripcion_producto ASC";
			$marca_collections = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select,$group_by);

			$subtitulo = "Reporte de Bonificación periodo ({$fecha_desde} - {$fecha_hasta})";
			$array_encabezados = array('MARCA','FACTURADO','BONIFICACIÓN', 'RENTABILIDAD', '', '');
			$array_exportacion = array();
			$array_exportacion[] = $array_encabezados;

			$sum_importe = 0;
			$sum_rentabilidad = 0;
			$sum_facturado = 0;
			foreach ($marca_collections as $clave=>$valor) {
				$sum_importe = $sum_importe + $valor["VALOR_DESCUENTO"];
				$sum_rentabilidad = $sum_rentabilidad + $valor["RENTABILIDAD"];
				$sum_facturado = $sum_facturado + $valor["IMPORTE_FACTURADO"];
				$array_temp = array();
				$array_temp = array(
								$valor["MARCA"]
							, $valor["IMPORTE_FACTURADO"]
							, $valor["VALOR_DESCUENTO"]
							, $valor["RENTABILIDAD"]);
				$array_exportacion[] = $array_temp;

			}

			$array_exportacion[] = array('', '', '', '');
			$array_exportacion[] = array('TOTAL', $sum_facturado,$sum_importe,$sum_rentabilidad,'');
			ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
			exit;
		}

	}

	function reporte_15dias(){
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$fecha_desde = filter_input(INPUT_POST, 'desde');
		$fecha_hasta = filter_input(INPUT_POST, 'hasta');
		$tipo_busqueda = filter_input(INPUT_POST, 'tipo_busqueda');

		$array_exportacion = array();
		switch ($tipo_busqueda) {
		    case 1:
						if (!empty($_POST['producto_id'])) {
							$producto_id = $_POST['producto_id'];
							$producto_id = implode(',', $producto_id);

 							$tipo_where = "pd.producto_id IN ({$producto_id})";
							$tipo_groupby = "";

							$subtitulo = "Reporte periodo {$fecha_desde} a  {$fecha_hasta} - Productos";
							$array_encabezados = array('CODIGO','PRODUCTO', 'CANTIDAD', 'CLIENTE', 'NOMBRE_FANTASIA');
						}
		        break;
		    case 2:
						if (!empty($_POST['marca_id'])) {
							$marca_id = $_POST['marca_id'];
							$marca_id = implode(',', $marca_id);

							$tipo_where = "pro.productomarca IN ({$marca_id})";
							$tipo_groupby = ",pro.productomarca";

							$subtitulo = "Reporte periodo {$fecha_desde} a  {$fecha_hasta} - Marcas";
							$array_encabezados = array('CODIGO','PRODUCTO', 'CANTIDAD', 'CLIENTE', 'NOMBRE_FANTASIA', 'MARCA');

						}
		        break;
		    case 3:
						if (!empty($_POST['proveedor_id'])) {
							$proveedor_id = $_POST['proveedor_id'];
							$proveedor_id = implode(',', $proveedor_id);

							$tipo_where = "pd.proveedor_id IN ({$proveedor_id})";
							$tipo_groupby = ",pd.proveedor_id";

							$subtitulo = "Reporte periodo {$fecha_desde} a  {$fecha_hasta} - Proveedores";
							$array_encabezados = array('CODIGO','PRODUCTO', 'CANTIDAD', 'CLIENTE', 'NOMBRE_FANTASIA', 'PROVEEDOR');
 						}
		        break;
				case 4:
						if (!empty($_POST['vendedor_id'])) {
							$vendedor_id = $_POST['vendedor_id'];
							$vendedor_id = implode(',', $vendedor_id);

							$tipo_where = "e.vendedor IN ({$vendedor_id})";
							$tipo_groupby = ",e.vendedor";

							$subtitulo = "Reporte periodo {$fecha_desde} a  {$fecha_hasta} - Vendedores";
							$array_encabezados = array('CODIGO','PRODUCTO', 'CANTIDAD', 'CLIENTE', 'NOMBRE_FANTASIA', 'VENDEDOR');
						}
		        break;
		}

		$select = "ed.codigo_producto AS CODIGO,ed.descripcion_producto AS PRODUCTO,ed.cantidad AS CANTIDAD,cl.razon_social AS CLIENTE,
		cl.nombre_fantasia AS NOMBRE_FANTASIA,CONCAT(v.apellido,',',v.nombre) AS VENDEDOR,pr.razon_social AS PROVEEDOR,pm.denominacion AS MARCA";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN provincia p ON cl.provincia = p.provincia_id
		INNER JOIN egresodetalle ed ON ed.egreso_id = e.egreso_id INNER JOIN productodetalle pd ON pd.producto_id = ed.producto_id
		INNER JOIN vendedor v ON v.vendedor_id = e.vendedor INNER JOIN proveedor pr ON pr.proveedor_id = pd.proveedor_id
		INNER JOIN producto pro ON pro.producto_id = ed.producto_id INNER JOIN productomarca pm ON pm.productomarca_id = pro.productomarca";
		$where = "e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' AND {$tipo_where}";
 		$groupby = "ed.producto_id,e.cliente{$tipo_groupby} ORDER BY cl.razon_social ASC";
		$egresos_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select,$groupby);

		$array_temp = array();
		$array_exportacion[] = $array_encabezados;
		foreach ($egresos_collection as $clave=>$valor) {
			switch ($tipo_busqueda) {
			    case 1:
							$campo = "";
			        break;
			    case 2:
							$campo = $valor["MARCA"];
			        break;
			    case 3:
							$campo = $valor["PROVEEDOR"];
			        break;
					case 4:
					  	$campo = $valor["VENDEDOR"];
			        break;
			}

			$array_temp = array(
							$valor["CODIGO"]
						, $valor["PRODUCTO"]
						, $valor["CANTIDAD"]
						, $valor["CLIENTE"]
						, $valor["NOMBRE_FANTASIA"]
						, $campo);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '');
		$array_exportacion[] = array('', '', '', '');
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	// PANELES REPORTES
	function reportes_productos() {
		SessionHandler()->check_session();
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
    	$fecha_sys = strtotime(date('Y-m-d'));
		$periodo_minimo = date("Ym", strtotime("-6 month", $fecha_sys));
    	$periodo_actual = date('Ym');

    	$select = "ed.codigo_producto AS COD, ed.descripcion_producto AS PRODUCTO, ROUND(SUM(ed.importe),2) AS IMPORTE,
				   ROUND(SUM(ed.cantidad),2) AS CANTIDAD, ed.producto_id AS PRID";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id";
		$where = "date_format(e.fecha, '%Y%m') = '{$periodo_actual}'";
		
		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.importe),2) DESC";
		$sum_importe_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.cantidad),2) DESC";
		$sum_cantidad_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(ncd.importe),2) AS IMPORTE, ROUND(SUM(ncd.cantidad),2) AS CANTIDAD";
		$from = "notacreditodetalle ncd INNER JOIN notacredito nc ON ncd.notacredito_id = nc.notacredito_id";
		foreach ($sum_importe_producto as $clave=>$valor) {
			$tmp_producto_id = $valor["PRID"];
			$where = "ncd.producto_id = {$tmp_producto_id} AND date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
			$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
				$nuevo_valor_importe = $sum_importe_producto[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
				$nuevo_valor_cantidad = $sum_importe_producto[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
			} else {
				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
			}

			$sum_importe_producto[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
			$sum_importe_producto[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
		}

		foreach ($sum_cantidad_producto as $clave=>$valor) {
			$tmp_producto_id = $valor["PRID"];
			$where = "ncd.producto_id = {$tmp_producto_id} AND date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
			$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
				$nuevo_valor_importe = $sum_cantidad_producto[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
				$nuevo_valor_cantidad = $sum_cantidad_producto[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
			} else {
				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
			}

			$sum_cantidad_producto[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
			$sum_cantidad_producto[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
		}

		$select = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from = "vendedor v";
		$where = "v.oculto = 0 ORDER BY CONCAT(v.apellido, ' ', v.nombre) ASC";
		$vendedor_collection = CollectorCondition()->get('Vendedor', $where, 4, $from, $select);

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, pc.denominacion AS CATEGORIA, p.codigo AS CODIGO";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "p.oculto = 0";
		$groupby = "p.producto_id ORDER BY CONCAT(pm.denominacion, ' ', p.denominacion) ASC";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select, $groupby);
		$productomarca_collection = Collector()->get('ProductoMarca');
		
		$select = "p.proveedor_id AS ID, p.razon_social AS DENOMINACION";
		$from = "proveedor p";
		$where = "p.oculto = 0 ORDER BY p.razon_social ASC";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);

		$this->view->reportes_productos($sum_importe_producto, $sum_cantidad_producto, $vendedor_collection, $producto_collection, $productomarca_collection, $proveedor_collection, $user_level);
	}

	// REPORTES PRODUCTOS
	function ajax_cobertura_marca($arg){
		SessionHandler()->check_session();
		
		$var = explode("@", $arg);
		$desde = $var[0];
		$hasta = $var[1];
		$vendedor_id = $var[2];
		$tipo_grafico = $var[3];
		$marca_ids = $var[4];

		$vm = new Vendedor();
		$vm->vendedor_id = $vendedor_id;
		$vm->get();
		$vendedor = $vm->apellido . ' ' . $vm->nombre;

		$pmm = new ProductoMarca();
		$pmm->productomarca_id = $marca_ids;
		$pmm->get();
		$marca = $pmm->denominacion;
		
		$select = "CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ed.cantidad AS CANTIDAD, pm.denominacion AS MARCA, ed.producto_id AS PRID, ed.egreso_id AS EGRID, ed.descuento AS DESCUENTO, ed.importe AS IMPORTE, date_format(e.fecha, '%d/%m/%Y') AS FECHA, ed.descripcion_producto AS PRODUCTO";
		$from = "egresodetalle ed INNER JOIN egreso e ON ed.egreso_id = e.egreso_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN cliente c ON e.cliente = c.cliente_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where_vendedor_all = "pm.productomarca_id IN ({$marca_ids}) AND e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY v.vendedor_id DESC, pm.productomarca_id DESC";
		$where_vendedor = "pm.productomarca_id IN ({$marca_ids}) AND e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY v.vendedor_id DESC, pm.productomarca_id DESC";
		$where = ($vendedor_id == 'all') ? $where_vendedor_all : $where_vendedor;
		$datos_reporte = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);
		$array_titulo = array('{fecha_desde}'=>$desde, '{fecha_hasta}'=>$hasta, '{marca}'=>$marca);

		if (is_array($datos_reporte)) {
			foreach ($datos_reporte as $clave=>$valor) {
				$tmp_producto_id = $valor["PRID"];
				$tmp_egreso_id = $valor["EGRID"];
				$select = "ncd.cantidad AS CANTIDAD, ncd.importe AS IMPORTE";
				$from = "notacreditodetalle ncd";
				$where = "ncd.producto_id = {$tmp_producto_id} AND ncd.egreso_id = {$tmp_egreso_id}";
				$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

				if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
					$datos_reporte[$clave]['NC_IMPORTE'] = $datos_notacredito[0]['IMPORTE'];
					$datos_reporte[$clave]['NC_CANTIDAD'] = $datos_notacredito[0]['CANTIDAD'];
				} else {
					$datos_reporte[$clave]['NC_IMPORTE'] = 0;
					$datos_reporte[$clave]['NC_CANTIDAD'] = 0;
				}
			}

			$array_exportacion = array();
			$sum_importe = 0;
			foreach ($datos_reporte as $clave=>$valor) {
				$temp_importe = 0;
				$temp_importe = $valor["IMPORTE"] - $valor["NC_IMPORTE"];
				$sum_importe = $sum_importe + $temp_importe;
				$array_temp = array();
				$array_temp = array('VENDEDOR' => $valor["VENDEDOR"]
									,'MARCA' => $valor["MARCA"]
									,'CANTIDAD' => $valor["CANTIDAD"] - $valor["NC_CANTIDAD"]
									,'IMPORTE' => $temp_importe);
				$array_exportacion[] = $array_temp;
			}

			$array_temp = array();
			if ($vendedor_id == 'all') {
				foreach ($array_exportacion as $key => $value) {
					if (array_search($value['VENDEDOR'], array_column($array_temp, 'VENDEDOR')) === FALSE) {
						$array_temp[] = array('VENDEDOR' => $value['VENDEDOR']
											  ,'MARCA' => $value['MARCA']
											  ,'CANTIDAD' => $value['CANTIDAD']
											  ,'IMPORTE' => $value['IMPORTE']);
					} else {
						$key = array_search($value['VENDEDOR'], array_column($array_temp, 'VENDEDOR'));
						if ($array_temp[$key]['MARCA'] == $value['MARCA']) {
							$array_temp[$key]['IMPORTE'] = $value['IMPORTE'] + $array_temp[$key]['IMPORTE'];
							$array_temp[$key]['CANTIDAD'] = $value['CANTIDAD'] + $array_temp[$key]['CANTIDAD'];
						} else {
							$array_temp[] = array('VENDEDOR' => $value['VENDEDOR']
												  ,'MARCA' => $value['MARCA']
												  ,'CANTIDAD' => $value['CANTIDAD']
												  ,'IMPORTE' => $value['IMPORTE']);
						}
					}
				}
			} else {
				foreach ($array_exportacion as $key => $value) {
					if (array_search($value['MARCA'], array_column($array_temp, 'MARCA')) === FALSE) {
						$array_temp[] = array('VENDEDOR' => $value['VENDEDOR']
											  ,'MARCA' => $value['MARCA']
											  ,'CANTIDAD' => $value['CANTIDAD']
											  ,'IMPORTE' => $value['IMPORTE']);
					} else {
						$key = array_search($value['MARCA'], array_column($array_temp, 'MARCA'));
						$array_temp[$key]['IMPORTE'] = $value['IMPORTE'] + $array_temp[$key]['IMPORTE'];
						$array_temp[$key]['CANTIDAD'] = $value['CANTIDAD'] + $array_temp[$key]['CANTIDAD'];
					}
				}
			}
		} else {
			$array_temp = array();
		}

		$this->view->ajax_cobertura_marca($array_temp, $array_titulo, $tipo_grafico, $vendedor);
	}

	function desc_cantidad_producto_vendedor_fecha() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor = filter_input(INPUT_POST, 'vendedor');

		$vm = new Vendedor();
		$vm->vendedor_id = $vendedor;
		$vm->get();
		$denominacion_vendedor = $vm->apellido . ' ' .$vm->nombre;

		$select = "e.egreso_id AS EGRESO_ID";
		$from = "egreso e";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}' AND e.vendedor = {$vendedor}";
		$egreso_ids_array = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$egreso_ids_array = (is_array($egreso_ids_array) AND !empty($egreso_ids_array)) ? $egreso_ids_array : array();

		$egreso_ids = array();
		foreach ($egreso_ids_array as $clave=>$valor) {
			$egreso_id = $valor['EGRESO_ID'];
			if(!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
		}

		if (!empty($egreso_ids)) {
			$egreso_ids = implode(',', $egreso_ids);
			$select = "p.producto_id AS PRODUCTO_ID, p.codigo AS CODIGO, CONCAT(pm.denominacion, ' ', p.denominacion) AS PRODUCTO, ROUND(SUM(ed.cantidad), 2) AS CANTIDAD";
			$from = "egresodetalle ed INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
			$where = "ed.egreso_id IN ($egreso_ids)";
			$group_by = "ed.producto_id";
			$cantidad_venta_producto = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select, $group_by);

			$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS PRODUCTO, ROUND(SUM(ncd.cantidad), 2) AS CANTIDAD";
			$from = "notacreditodetalle ncd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
			$where = "ncd.egreso_id IN ($egreso_ids)";
			$group_by = "ncd.producto_id";
			$cantidad_nc_producto = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select, $group_by);

			foreach ($cantidad_venta_producto as $clave=>$valor) {
				$venta_producto_id = $valor['PRODUCTO_ID'];
				$venta_cantidad = $valor['CANTIDAD'];
				$nc_cantidad_temp = 0;

				foreach ($cantidad_nc_producto as $c=>$v) {
					$nc_producto_id = $v['PRODUCTO_ID'];
					$nc_cantidad = $v['CANTIDAD'];

					if ($venta_producto_id == $nc_producto_id) {
						$venta_cantidad = $venta_cantidad - $nc_cantidad;
						$nc_cantidad_temp = $nc_cantidad;
					}
				}

				$cantidad_venta_producto[$clave]['CANTIDAD_NC'] = $nc_cantidad_temp;
				$cantidad_venta_producto[$clave]['CANTIDAD_FINAL'] = $venta_cantidad;
			}
			
		} else {
			$cantidad_venta_producto = array();
		}
		
		$subtitulo = "Cant. Productos Vendidos por Vendedor: {$denominacion_vendedor} - ({$desde} - {$hasta})";
		$array_encabezados = array('COD', 'PRODUCTO','CANT. VENTA','CANT. NC.', 'CANT. FINAL');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;

		foreach ($cantidad_venta_producto as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array($valor["CODIGO"]
								, $valor["PRODUCTO"]
								, $valor["CANTIDAD"]
								, $valor["CANTIDAD_NC"]
								, $valor["CANTIDAD_FINAL"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
	}

	function desc_vendedor_marca() {
		SessionHandler()->check_session();
		require_once "tools/excelreport_tipo2.php";

		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor_id');
		$marca_id = filter_input(INPUT_POST, "marca_id");
		
		$select = "ROUND(SUM(ed.cantidad),2) AS TOTCANT, ROUND(SUM(ed.importe),2) AS TOTIMPO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, pm.denominacion AS MARCA, ed.producto_id AS PRID, date_format(e.fecha, '%d/%m/%Y') AS FECHA, ed.descripcion_producto AS PRODUCTO";
		$from = "egresodetalle ed INNER JOIN egreso e ON ed.egreso_id = e.egreso_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "pm.productomarca_id = {$marca_id} AND e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$group_by = "ed.producto_id ORDER BY ed.descripcion_producto ASC, e.fecha DESC";
		$datos_reporte = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select, $group_by);

		$select = "ed.producto_id AS PRID, e.egreso_id AS EGRID";
		$from = "egresodetalle ed INNER JOIN egreso e ON ed.egreso_id = e.egreso_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "pm.productomarca_id = {$marca_id} AND e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$datos_egresos = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

		if (is_array($datos_reporte) AND !empty($datos_reporte)) {
			foreach ($datos_egresos as $clave=>$valor) {
				$tmp_egreso_id = $valor["EGRID"];
				$tmp_producto_id = $valor["PRID"];
				$select = "ncd.cantidad AS CANTIDAD, ncd.importe AS IMPORTE";
				$from = "notacreditodetalle ncd";
				$where = "ncd.producto_id = {$tmp_producto_id} AND ncd.egreso_id = {$tmp_egreso_id}";
				$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

				if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {

					foreach ($datos_reporte as $c=>$v) {
						$producto_id = $v["PRID"];
						if ($producto_id == $tmp_producto_id) {
							$datos_reporte[$c]['TOTIMPO'] = $datos_reporte[$c]['TOTIMPO'] - $datos_notacredito[0]['IMPORTE'];
							$datos_reporte[$c]['TOTCANT'] = $datos_reporte[$c]['TOTCANT'] - $datos_notacredito[0]['CANTIDAD'];

						}
					}

				}
			}
		}

		$subtitulo = "VENTAS POR VENDEDOR, RANGO DE FECHA, MARCA Y PRODUCTO";
		$array_encabezados = array('VENDEDOR', 'MARCA', 'PRODUCTO', 'CANTIDAD', 'IMPORTE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($datos_reporte as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array($valor["VENDEDOR"]
								, $valor["MARCA"]
								, $valor["PRODUCTO"]
								, $valor["TOTCANT"]
								, $valor["TOTIMPO"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReportTipo2()->extraer_informe($subtitulo, $array_exportacion);
		exit;
	}

	function desc_stock_valorizado() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$proveedor_id = filter_input(INPUT_POST, "proveedor");
		$pvm = new Proveedor();
		$pvm->proveedor_id = $proveedor_id;
		$pvm->get();
		$proveedor = $pvm->razon_social;

		$select = "s.producto_id AS PROD_ID";
		$from = "stock s INNER JOIN producto p ON s.producto_id = p.producto_id INNER JOIN productodetalle pd ON p.producto_id = pd.producto_id";
		$where = "pd.proveedor_id = {$proveedor_id}";
		$groupby = "s.producto_id";
		$productoid_collection = CollectorCondition()->get('Stock', $where, 4, $from, $select, $groupby);

		$stock_valorizado = 0;
		$array_exportacion = array();
		$subtitulo = "STOCK VALORIZADO - PROVEEDOR: {$proveedor}";
		$array_encabezados = array('CODIGO', 'PRODUCTO', '$ COSTO', 'CANT ACTUAL', 'VALORIZADO');
		$array_exportacion[] = $array_encabezados;
		$valor_stock_total = 0;
		if ($productoid_collection != 0 || !empty($productoid_collection) || is_array($productoid_collection)) {
			$producto_ids = array();
			foreach ($productoid_collection as $producto_id) $producto_ids[] = $producto_id['PROD_ID'];
			$producto_ids = implode(',', $producto_ids);

			$select_stock = "MAX(s.stock_id) AS STOCK_ID";
			$from_stock = "stock s";
			$where_stock = "s.producto_id IN ({$producto_ids})";
			$groupby_stock = "s.producto_id";
			$stockid_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock, $groupby_stock);

			foreach ($stockid_collection as $stock_id) {
				$array_temp = array();
				$sm = new Stock();
				$sm->stock_id = $stock_id['STOCK_ID'];
				$sm->get();
				$producto_cantidad_actual = $sm->cantidad_actual;

				$pm = new Producto();
				$pm->producto_id = $sm->producto_id;
				$pm->get();
				$costo_producto = $pm->costo;
				$flete_producto = $pm->flete;
				$iva_producto = $pm->iva;

				$costo_iva = (($costo_producto * $iva_producto) / 100) + $costo_producto;
				$valor_stock_producto = round(($costo_iva * $sm->cantidad_actual),2);
				$stock_valorizado = $stock_valorizado + $valor_stock_producto;

				$array_temp = array($pm->codigo,
									$pm->denominacion,
									round($costo_iva, 2),
									round($producto_cantidad_actual, 2),
									$valor_stock_producto);

				$array_exportacion[] = $array_temp;
				$valor_stock_total = $valor_stock_total + $valor_stock_producto;
			}
		}

		$array_linea_blanco = array('', '', '', '', '');
		$array_valorizado_total = array('', '', '', 'TOTAL', $valor_stock_total);
		$array_exportacion[] = $array_linea_blanco;
		$array_exportacion[] = $array_valorizado_total;

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function desc_producto_importe() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$fecha_sys = strtotime(date('Y-m-d'));
		$periodo_minimo = date("Ym", strtotime("-6 month", $fecha_sys));
    	$periodo_actual = date('Ym');

		$tipo_descarga = $arg;
		$select = "ed.codigo_producto AS COD, p.denominacion AS PRODUCTO, pm.denominacion MARCA, ROUND(SUM(ed.importe),2) AS IMPORTE,
				   ROUND(SUM(ed.cantidad),2) AS CANTIDAD, ed.producto_id AS PRID";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN
				 productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "date_format(e.fecha, '%Y%m') = '{$periodo_actual}'";
		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.importe),2) DESC";
		$datos_reporte = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(ncd.importe),2) AS IMPORTE, ROUND(SUM(ncd.cantidad),2) AS CANTIDAD";
		$from = "notacreditodetalle ncd INNER JOIN notacredito nc ON ncd.notacredito_id = nc.notacredito_id";
		foreach ($datos_reporte as $clave=>$valor) {
			$tmp_producto_id = $valor["PRID"];
			$where = "ncd.producto_id = {$tmp_producto_id} AND date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
			$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
				$nuevo_valor_importe = $datos_reporte[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
				$nuevo_valor_cantidad = $datos_reporte[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
			} else {
				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
			}

			$datos_reporte[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
			$datos_reporte[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
		}

		$datos_reporte = $this->view->order_collection_array($datos_reporte, 'IMPORTE', SORT_DESC);
		$subtitulo = "+ VENDIDOS POR IMPORTE";
		$array_encabezados = array('CÓDIGO', 'MARCA', 'PRODUCTO', 'CANTIDAD', 'IMPORTE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($datos_reporte as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["IMPORTE"];
			$array_temp = array();
			$array_temp = array($valor["COD"]
								, $valor["MARCA"]
								, $valor["PRODUCTO"]
								, $valor["CANTIDAD"]
								, $valor["IMPORTE"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'TOTAL', $sum_importe);
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function desc_producto_cantidad() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$fecha_sys = strtotime(date('Y-m-d'));
		$periodo_minimo = date("Ym", strtotime("-6 month", $fecha_sys));
    	$periodo_actual = date('Ym');

		$tipo_descarga = $arg;
		$select = "ed.codigo_producto AS COD, p.denominacion AS PRODUCTO, pm.denominacion MARCA, ROUND(SUM(ed.importe),2) AS IMPORTE, ROUND(SUM(ed.cantidad),2) AS CANTIDAD, ed.producto_id AS PRID";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "date_format(e.fecha, '%Y%m') = '{$periodo_actual}'";
		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.cantidad),2) DESC";
		$datos_reporte = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(ncd.importe),2) AS IMPORTE, ROUND(SUM(ncd.cantidad),2) AS CANTIDAD";
		$from = "notacreditodetalle ncd INNER JOIN notacredito nc ON ncd.notacredito_id = nc.notacredito_id";
		foreach ($datos_reporte as $clave=>$valor) {
			$tmp_producto_id = $valor["PRID"];
			$where = "ncd.producto_id = {$tmp_producto_id} AND date_format(nc.fecha, '%Y%m') = '{$periodo_actual}'";
			$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
				$nuevo_valor_importe = $datos_reporte[$clave]['IMPORTE'] - $datos_notacredito[0]['IMPORTE'];
				$nuevo_valor_cantidad = $datos_reporte[$clave]['CANTIDAD'] - $datos_notacredito[0]['CANTIDAD'];
			} else {
				$nuevo_valor_importe = 0;
				$nuevo_valor_cantidad = 0;
			}

			$datos_reporte[$clave]['IMPORTE'] = round($nuevo_valor_importe, 2);
			$datos_reporte[$clave]['CANTIDAD'] = round($nuevo_valor_cantidad, 2);
		}

		$datos_reporte = $this->view->order_collection_array($datos_reporte, 'CANTIDAD', SORT_DESC);
		$subtitulo = "+ VENDIDOS POR CANTIDAD";
		$array_encabezados = array('CÓDIGO', 'MARCA', 'PRODUCTO', 'CANTIDAD', 'IMPORTE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($datos_reporte as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["IMPORTE"];
			$array_temp = array();
			$array_temp = array($valor["COD"]
								, $valor["MARCA"]
								, $valor["PRODUCTO"]
								, $valor["CANTIDAD"]
								, $valor["IMPORTE"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'TOTAL', $sum_importe);
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function desc_producto_vendedor_fecha() {
		SessionHandler()->check_session();
		require_once "tools/excelreport_tipo2.php";

		//PARAMETROS
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$tipo_filtro = filter_input(INPUT_POST, 'tipo_informe');
		$producto_ids = $_POST['producto_id'];
		$producto_ids = implode(',', $producto_ids);

		if ($tipo_filtro == 1) {
			$select = "ed.egreso_id AS EGRID";
			$from = "egresodetalle ed INNER JOIN egreso e ON ed.egreso_id = e.egreso_id";
			$where_vendedor_all = "ed.producto_id IN ({$producto_ids}) AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
			$where_vendedor = "ed.producto_id IN ({$producto_ids}) AND e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
			$group_by = "e.egreso_id ORDER BY e.vendedor DESC";
			$where = ($vendedor_id == 'all') ? $where_vendedor_all : $where_vendedor;
			$datos_temp = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select, $group_by);

			$egreso_ids = array();
			foreach ($datos_temp as $clave=>$valor) {
				if (!in_array($valor["EGRID"], $egreso_ids)) $egreso_ids[] = $valor["EGRID"];
			}

			$egreso_ids = implode(',', $egreso_ids);
			$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ed.producto_id AS PRID, pm.denominacion AS MARCA, p.denominacion AS PRODUCTO, ROUND(SUM(ed.cantidad),2) AS CANTIDAD, ROUND(SUM(ed.descuento),2) AS DESCUENTO, ROUND(SUM(ed.importe),2) AS IMPORTE, p.productounidad AS PROUNI";
			$from = "egresodetalle ed INNER JOIN egreso e ON ed.egreso_id = e.egreso_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
			$where = "e.egreso_id IN ({$egreso_ids}) AND ed.producto_id IN ({$producto_ids})";
			$group_by = "v.vendedor_id, p.producto_id ORDER BY v.apellido ASC, v.nombre ASC, pm.denominacion ASC, p.denominacion ASC, SUM(ed.cantidad) DESC, SUM(ed.importe) DESC";
			$datos_temp = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select, $group_by);

			foreach ($datos_temp as $clave=>$valor) {
				$tmp_producto_id = $valor["PRID"];
				$tmp_vendedor_id = $valor["VID"];
				$tmp_productounidad = $valor["PROUNI"];

				$select = "ROUND(SUM(ncd.cantidad),2) AS CANTIDAD, ROUND(SUM(ncd.importe),2) AS IMPORTE";
				$from = "notacreditodetalle ncd INNER JOIN egreso e ON ncd.egreso_id = e.egreso_id";
				$where = "ncd.producto_id = {$tmp_producto_id} AND e.egreso_id IN ({$egreso_ids}) AND e.vendedor = {$tmp_vendedor_id}";
				$group_by = "ncd.producto_id";
				$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select, $group_by);

				if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
					$cantidad_ini = $datos_temp[$clave]['CANTIDAD'];
					$cantidad_ncd = $datos_notacredito[0]['CANTIDAD'];
					$importe_ini = $datos_temp[$clave]['IMPORTE'];
					$importe_ncd = $datos_notacredito[0]['IMPORTE'];
					$datos_temp[$clave]['CANTIDAD'] = $cantidad_ini - $cantidad_ncd;
					$datos_temp[$clave]['IMPORTE'] = $importe_ini - $importe_ncd;
				}

				if ($tmp_productounidad == 5) {
					$pm = new Producto();
					$pm->producto_id = $tmp_producto_id;
					$pm->get();
					$datos_temp[$clave]['CANTIDAD'] = $datos_temp[$clave]['CANTIDAD'] * $pm->peso;
				}
			}

			$subtitulo = "VENTAS POR VENDEDOR, RANGO DE FECHA Y PRODUCTO";
			$array_encabezados = array('VENDEDOR', 'MARCA', 'PRODUCTO', 'CANTIDAD', 'DESCUENTO', 'IMPORTE');
			$array_exportacion = array();
			$array_exportacion[] = $array_encabezados;
			$sum_importe = 0;
			foreach ($datos_temp as $clave=>$valor) {
				$temp_importe = 0;
				$temp_importe = $valor["IMPORTE"];
				$sum_importe = $sum_importe + $temp_importe;
				$array_temp = array();
				$array_temp = array($valor["VENDEDOR"]
									, $valor["MARCA"]
									, $valor["PRODUCTO"]
									, $valor["CANTIDAD"]
									, $valor["DESCUENTO"]
									, $temp_importe);
				$array_exportacion[] = $array_temp;
			}

			$array_exportacion[] = array('', '', '', '', '', '');
			$array_exportacion[] = array('', '', '', '', 'TOTAL', $sum_importe);
		} else {
			$select = "CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, v.vendedor_id AS VID, ed.cantidad AS CANTIDAD, ed.producto_id AS PRID, ed.egreso_id AS EGRID, ed.descuento AS DESCUENTO, ed.importe AS IMPORTE, date_format(e.fecha, '%d/%m/%Y') AS FECHA, p.denominacion AS PRODUCTO, pm.denominacion AS MARCA";
			$from = "egresodetalle ed INNER JOIN egreso e ON ed.egreso_id = e.egreso_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN cliente c ON e.cliente = c.cliente_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
			$where_vendedor_all = "ed.producto_id IN ({$producto_ids}) AND e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY ed.descripcion_producto ASC, e.fecha DESC";
			$where_vendedor = "ed.producto_id IN ({$producto_ids}) AND e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY ed.descripcion_producto ASC, e.fecha DESC";
			$where = ($vendedor_id == 'all') ? $where_vendedor_all : $where_vendedor;
			$datos_reporte = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

			foreach ($datos_reporte as $clave=>$valor) {
				$tmp_producto_id = $valor["PRID"];
				$tmp_egreso_id = $valor["EGRID"];
				$select = "ncd.cantidad AS CANTIDAD, ncd.importe AS IMPORTE";
				$from = "notacreditodetalle ncd";
				$where = "ncd.producto_id = {$tmp_producto_id} AND ncd.egreso_id = {$tmp_egreso_id}";
				$datos_notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

				if (is_array($datos_notacredito) AND !empty($datos_notacredito)) {
					$datos_reporte[$clave]['NC_IMPORTE'] = $datos_notacredito[0]['IMPORTE'];
					$datos_reporte[$clave]['NC_CANTIDAD'] = $datos_notacredito[0]['CANTIDAD'];
				} else {
					$datos_reporte[$clave]['NC_IMPORTE'] = 0;
					$datos_reporte[$clave]['NC_CANTIDAD'] = 0;
				}
			}

			$subtitulo = "VENTAS POR VENDEDOR, RANGO DE FECHA Y PRODUCTO";
			$array_encabezados = array('FECHA', 'FACTURA', 'CLIENTE', 'VENDEDOR', 'MARCA', 'PRODUCTO', 'CANTIDAD', 'DESCUENTO', 'IMPORTE');
			$array_exportacion = array();
			$array_exportacion[] = $array_encabezados;
			$sum_importe = 0;
			foreach ($datos_reporte as $clave=>$valor) {
				$temp_importe = 0;
				$temp_importe = $valor["IMPORTE"] - $valor["NC_IMPORTE"];
				$sum_importe = $sum_importe + $temp_importe;
				$array_temp = array();
				$array_temp = array($valor["FECHA"]
									, $valor["FACTURA"]
									, $valor["CLIENTE"]
									, $valor["VENDEDOR"]
									, $valor["MARCA"]
									, $valor["PRODUCTO"]
									, $valor["CANTIDAD"] - $valor["NC_CANTIDAD"]
									, $valor["DESCUENTO"]
									, $temp_importe);
				$array_exportacion[] = $array_temp;
			}

			$array_exportacion[] = array('', '', '', '', '', '', '', '', '');
			$array_exportacion[] = array('', '', '', '', '', '', '', 'TOTAL', $sum_importe);
		}
		
		ExcelReportTipo2()->extraer_informe($subtitulo, $array_exportacion);
		exit;
	}

	// REPORTES CLIENTES
	function desc_importe_venta_cliente_vendedor_fecha() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor = filter_input(INPUT_POST, 'vendedor');
		
		$vm = new Vendedor();
		$vm->vendedor_id = $vendedor;
		$vm->get();
		$denominacion_vendedor = $vm->apellido . ' ' .$vm->nombre;

		$select = "e.egreso_id AS EGRESO_ID";
		$from = "egreso e";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}' AND e.vendedor = {$vendedor}";
		$egreso_ids_array = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$egreso_ids_array = (is_array($egreso_ids_array) AND !empty($egreso_ids_array)) ? $egreso_ids_array : array();

		$egreso_ids = array();
		foreach ($egreso_ids_array as $clave=>$valor) {
			$egreso_id = $valor['EGRESO_ID'];
			if(!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
		}

		if (!empty($egreso_ids)) {
			$egreso_ids = implode(',', $egreso_ids);
			$select = "c.cliente_id AS CLIENTE_ID, c.codigo AS CODIGO, c.razon_social AS CLIENTE, ROUND(SUM(e.importe_total), 2) AS IMPORTETOTAL";
			$from = "egreso e INNER JOIN cliente c ON e.cliente = c.cliente_id";
			$where = "e.egreso_id IN ($egreso_ids)";
			$group_by = "e.cliente";
			$importe_venta_cliente = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $group_by);
			
			$select = "c.cliente_id AS CLIENTE_ID, c.codigo AS CODIGO, c.razon_social AS CLIENTE, ROUND(SUM(nc.importe_total), 2) AS IMPORTETOTAL";
			$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN cliente c ON e.cliente = c.cliente_id";
			$where = "nc.egreso_id IN ($egreso_ids)";
			$group_by = "e.cliente";
			$importe_nc_cliente = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $group_by);

			foreach ($importe_venta_cliente as $clave=>$valor) {
				$venta_cliente_id = $valor['CLIENTE_ID'];
				$venta_importetotal = $valor['IMPORTETOTAL'];
				$nc_importetotal_temp = 0;

				foreach ($importe_nc_cliente as $c=>$v) {
					$nc_cliente_id = $v['CLIENTE_ID'];
					$nc_importotal = $v['IMPORTETOTAL'];

					if ($venta_cliente_id == $nc_cliente_id) {
						$venta_importetotal = $venta_importetotal - $nc_importotal;
						$nc_importetotal_temp = $nc_importotal;
					}
				}

				$importe_venta_cliente[$clave]['IMPORTETOTAL_NC'] = $nc_importetotal_temp;
				$importe_venta_cliente[$clave]['IMPORTETOTAL_FINAL'] = $venta_importetotal;
			}
			
		} else {
			$importe_venta_cliente = array();
		}
			
		$subtitulo = "Importes de Venta por Cliente y Vendedor: {$denominacion_vendedor} - ({$desde} - {$hasta})";
		$array_encabezados = array('COD', 'CLIENTE', 'IMPORTE TOTAL VENTA','IMPORTE TOTAL NC.', 'IMPORTE TOTAL FINAL');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;

		foreach ($importe_venta_cliente as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array($valor["CODIGO"]
								, $valor["CLIENTE"]
								, $valor["IMPORTETOTAL"]
								, $valor["IMPORTETOTAL_NC"]
								, $valor["IMPORTETOTAL_FINAL"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
	}

	// REPORTES GASTOS
	function desc_gastos_categoria_fecha() {
		SessionHandler()->check_session();
		require_once "tools/excelreport_tipo2.php";

		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$gastocategoria_id = filter_input(INPUT_POST, 'gastocategoria');
		
		$select = "gc.denominacion AS DENOMINACION, g.fecha AS FECHA, g.detalle AS DETALLE, g.importe AS IMPORTE";
		$from = "gastocategoria gc INNER JOIN gasto g ON gc.gastocategoria_id = g.gastocategoria";
		$where_categoria = ($gastocategoria_id == 'all') ? '' : 'AND gc.gastocategoria_id = ' . $gastocategoria_id;
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}' {$where_categoria}";
		$datos_reporte = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);

		$subtitulo = "GASTOS POR RANGO DE FECHA Y CATEGORÍA";
		$array_encabezados = array('FECHA', 'CATEGORÍA', 'DETALLE', 'IMPORTE', '');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($datos_reporte as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["IMPORTE"];
			$array_temp = array();
			$array_temp = array($valor["FECHA"]
								, $valor["DENOMINACION"]
								, $valor["DETALLE"]
								, $valor["IMPORTE"]
								, '');
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '');
		$array_exportacion[] = array('', '', 'TOTAL', $sum_importe);

		ExcelReportTipo2()->extraer_informe($subtitulo, $array_exportacion);
		exit;
	}
}
?>