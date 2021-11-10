<?php
require_once "modules/vendedor/model.php";
require_once "modules/vendedor/view.php";
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/frecuenciaventa/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/egreso/model.php";
require_once "modules/egresocomision/model.php";
require_once "modules/cliente/model.php";
require_once "modules/notacredito/model.php";
require_once "modules/empleado/model.php";
require_once "modules/vendedorempleado/model.php";
require_once "modules/salario/model.php";
require_once "modules/cobrador/model.php";
require_once "tools/pagoComisionPDFTool.php";
require_once "tools/visitaClientesPDFTool.php";


class VendedorController {

	function __construct() {
		$this->model = new Vendedor();
		$this->view = new VendedorView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$this->view->panel();
	}

	function listar() {
		$select = "v.vendedor_id AS VENDEDOR_ID, v.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, v.codigopostal AS CODPOSTAL,
				   v.apellido AS APELLIDO, v.nombre AS NOMBRE, v.documento AS DOCUMENTO, v.comision AS COMISION,
				   CONCAT(fv.denominacion, ' (', fv.dia_1, '-', fv.dia_2, ')') AS FRECUENCIAVENTA";
		$from = "vendedor v INNER JOIN provincia pr ON v.provincia = pr.provincia_id INNER JOIN
				 frecuenciaventa fv ON v.frecuenciaventa = fv.frecuenciaventa_id";
		$where = "v.oculto = 0";
		$vendedor_collection = CollectorCondition()->get('Vendedor', $where, 4, $from, $select);

		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$vendedorobj_collection = Collector()->get('Vendedor');
		foreach ($vendedorobj_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($vendedorobj_collection[$clave]);
		}

		$this->view->listar($vendedor_collection, $frecuenciaventa_collection, $vendedorobj_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$this->view->agregar($provincia_collection, $documentotipo_collection, $frecuenciaventa_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->vendedor_id = $arg;
		$this->model->get();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$this->view->editar($provincia_collection, $documentotipo_collection, $frecuenciaventa_collection, $this->model);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		$vendedor_id = $arg;
		$this->model->vendedor_id = $vendedor_id;
		$this->model->get();

		$periodo_actual = date('Ym');
		$select = "ROUND(SUM(e.importe_total),2) AS TOTAL, COUNT(*) AS CANTVENTAS";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id";
		$where = "v.vendedor_id = {$vendedor_id} AND date_format(e.fecha, '%Y%m') = '{$periodo_actual}'";
		$groupby = "date_format(e.fecha, '%Y%m')";
		$estadisticas = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$estadisticas = $estadisticas[0];

		$select = "ROUND(SUM(valor_abonado),2) AS ECOMISION";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id";
		$where = "date_format(ec.fecha, '%Y%m') = '{$periodo_actual}' AND ec.estadocomision IN (2,3) AND e.vendedor = {$vendedor_id}";
		$egreso_comision_periodoactual = CollectorCondition()->get('EgresoComision', $where, 4, $from, $select);
		$egreso_comision_periodoactual = (is_array($egreso_comision_periodoactual)) ? $egreso_comision_periodoactual[0]['ECOMISION'] : 0;
		$egreso_comision_periodoactual = (is_null($egreso_comision_periodoactual)) ? 0 : $egreso_comision_periodoactual;

		$estadisticas['PERACTUAL'] = $periodo_actual;
		$estadisticas['EGRESOCOMISION'] = $egreso_comision_periodoactual;

		$select = "v.vendedor_id AS VID, date_format(ec.fecha, '%d/%m/%Y') AS ECFECHA, ec.fecha AS ECFECBUS,
				   ROUND(SUM(ec.valor_abonado),2) AS COMISION, ROUND(SUM(e.importe_total),2) AS TOTALFACTURADO";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id";
		$where = "v.vendedor_id = {$vendedor_id} AND ec.estadocomision IN (2,3)";
		$groupby = "ec.fecha ORDER BY date_format(ec.fecha, '%d/%m/%Y') DESC";
		$egresocomision_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$select = "ed.codigo_producto AS COD, ed.descripcion_producto AS PRODUCTO, ROUND(SUM(ed.importe),2) AS IMPORTE,
				   ROUND(SUM(ed.cantidad),2) AS CANTIDAD";
		$from = "egreso e INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id";
		$where = "date_format(e.fecha, '%Y%m') = '{$periodo_actual}' AND e.vendedor = {$vendedor_id}";

		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.importe),2) DESC";
		$sum_importe_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$groupby = "ed.producto_id, ed.codigo_producto ORDER BY	ROUND(SUM(ed.cantidad),2) DESC";
		$sum_cantidad_producto = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);

		$this->view->consultar($egresocomision_collection, $sum_importe_producto, $sum_cantidad_producto, $this->model, $estadisticas);
	}

	function estadisticas() {
		SessionHandler()->check_session();
		$primer_dia_mes = date('Y-m') . '-01'; 
		$fecha_sys = date('Y-m-d'); 

		$select = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from = "vendedor v";
		$vendedor_collection = CollectorCondition()->get('Egreso', NULL, 4, $from, $select);

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(CASE WHEN e.tipofactura = 1 THEN e.importe_total WHEN e.tipofactura = 3 THEN e.importe_total ELSE 0 END),2) AS BLANCO, ROUND(SUM(CASE WHEN e.tipofactura = 2 THEN e.importe_total ELSE 0 END),2) AS NEGRO, ROUND(SUM(e.importe_total), 2) AS TOTAL";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys}'";
		$groupby = "e.vendedor";
		$ventas_vendedor_tipo_factura = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ventas_vendedor_tipo_factura = (is_array($ventas_vendedor_tipo_factura) AND !empty($ventas_vendedor_tipo_factura)) ? $ventas_vendedor_tipo_factura : array();

		$select = "e.vendedor AS VID, ROUND(SUM(CASE WHEN nc.tipofactura = 4 THEN nc.importe_total WHEN nc.tipofactura = 5 THEN nc.importe_total ELSE 0 END),2) AS BLANCO, ROUND(SUM(CASE WHEN nc.tipofactura = 6 THEN nc.importe_total ELSE 0         END),2) AS NEGRO, ROUND(SUM(nc.importe_total), 2) AS TOTAL";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys}'";
		$groupby = "e.vendedor";
		$notacredito_vendedor_tipo_factura = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$notacredito_vendedor_tipo_factura = (is_array($notacredito_vendedor_tipo_factura) AND !empty($notacredito_vendedor_tipo_factura)) ? $notacredito_vendedor_tipo_factura : array();

		foreach ($ventas_vendedor_tipo_factura as $clave=>$valor) {
			$ventas_vendedor_id = $valor['VID'];
			$ventas_blanco = $valor['BLANCO'];
			$ventas_negro = $valor['NEGRO'];
			$ventas_total = $valor['TOTAL'];

			foreach ($notacredito_vendedor_tipo_factura as $c=>$v) {
				$nc_vendedor_id = $v['VID'];
				$nc_blanco = $v['BLANCO'];
				$nc_negro = $v['NEGRO'];
				$nc_total = $v['TOTAL'];
				
				if ($ventas_vendedor_id == $nc_vendedor_id) {
					$ventas_vendedor_tipo_factura[$clave]['BLANCO'] = $ventas_blanco - $nc_blanco;
					$ventas_vendedor_tipo_factura[$clave]['NEGRO'] = $ventas_negro - $nc_negro;
					$ventas_vendedor_tipo_factura[$clave]['TOTAL'] = $ventas_total - $nc_total;
				}
			}
		}

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, LEFT(pr.razon_social, 25) AS PROVEEDOR, ROUND(SUM(ed.importe),2) AS IMPORTE";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productodetalle pd ON p.producto_id = pd.producto_id INNER JOIN proveedor pr ON pd.proveedor_id = pr.proveedor_id";
		$where = "e.fecha BETWEEN '{$primer_dia_mes}' AND '{$fecha_sys}'";
		$groupby = "v.vendedor_id, pr.proveedor_id ORDER BY	CONCAT(v.apellido, ' ', v.nombre), ROUND(SUM(ed.importe),2) DESC";
		$top3_vendedor_proveedor = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$top3_vendedor_proveedor = (is_array($top3_vendedor_proveedor) AND !empty($top3_vendedor_proveedor)) ? $top3_vendedor_proveedor : array();

		$temp_cont_vendedor = 0;
		$array_vendedor_id = array();
		$temp_top3_vendedor_proveedor = array();
		foreach ($top3_vendedor_proveedor as $clave=>$valor) {
			$temp_vendedor_id = $valor['VID'];

			if (!in_array($temp_vendedor_id, $array_vendedor_id)) {
				$array_vendedor_id[] = $temp_vendedor_id;
				$temp_cont_vendedor = 1;
			} else {
				$temp_cont_vendedor = $temp_cont_vendedor + 1;
			}

			if ($temp_cont_vendedor > 3) {
				unset($top3_vendedor_proveedor[$clave]);
			} else {
				$temp_top3_vendedor_proveedor[] = $valor;
			}
		}

		$flag_ini = 0;
		$cant_array = count($temp_top3_vendedor_proveedor);
		$array_vendedor_id = array();
		$temp_array_totales = array();
		foreach ($temp_top3_vendedor_proveedor as $clave=>$valor) {
			$temp_vendedor_id = $valor['VID'];

			if (!in_array($temp_vendedor_id, $array_vendedor_id)) {
				$array_vendedor_id[] = $temp_vendedor_id;

				if ($flag_ini == 0) {
					$temp_vendedor_denominacion = $valor['VENDEDOR'];
					$temp_array_totales[] = array('{PROVEEDOR}'=>$valor['PROVEEDOR'], '{IMPORTE}'=>'$' . $valor['IMPORTE']);
					$flag_ini = 1;
				} else {
					if (count($temp_array_totales) < 3) {
						$faltantes = 3 - count($temp_array_totales);
						for ($i=0; $i < $faltantes; $i++) $temp_array_totales[] = array('{PROVEEDOR}'=>'-', '{IMPORTE}'=>'');
					}

					$temp_array = array('{VENDEDOR}'=>$temp_vendedor_denominacion,
										'ARRAY_TOTALES'=>$temp_array_totales);
					$top3_vendedor_proveedor_final[] = $temp_array;
					$temp_array_totales = array();
					$temp_array_totales[] = array('{PROVEEDOR}'=>$valor['PROVEEDOR'], '{IMPORTE}'=>'$' . $valor['IMPORTE']);
					$temp_vendedor_denominacion = $valor['VENDEDOR'];
				}
			} else {
				$temp_array_totales[] = array('{PROVEEDOR}'=>$valor['PROVEEDOR'], '{IMPORTE}'=>'$' . $valor['IMPORTE']);
				if ($cant_array == ($clave + 1)) {
					if (count($temp_array_totales) < 3) {
						$faltantes = 3 - count($temp_array_totales);
						for ($i=0; $i < $faltantes; $i++) $temp_array_totales[] = array('{PROVEEDOR}'=>'-', '{IMPORTE}'=>'');
					}

					$temp_array = array('{VENDEDOR}'=>$temp_vendedor_denominacion,
										'ARRAY_TOTALES'=>$temp_array_totales);
					$top3_vendedor_proveedor_final[] = $temp_array;
					$temp_array_totales = array();
				}
			}
		}

		$this->view->estadisticas($vendedor_collection, $ventas_vendedor_tipo_factura, $top3_vendedor_proveedor_final);
	}

	function filtro_estadisticas() {
		SessionHandler()->check_session();

		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');

		$select_vendedor = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from_vendedor = "vendedor v";
		$vendedor_collection = CollectorCondition()->get('Egreso', NULL, 4, $from_vendedor, $select_vendedor);

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(CASE WHEN e.tipofactura = 1 THEN e.importe_total WHEN e.tipofactura = 3 THEN e.importe_total ELSE 0 END),2) AS BLANCO, ROUND(SUM(CASE WHEN e.tipofactura = 2 THEN e.importe_total ELSE 0 END),2) AS NEGRO, ROUND(SUM(e.importe_total), 2) AS TOTAL";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "e.vendedor";
		$ventas_vendedor_tipo_factura = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ventas_vendedor_tipo_factura = (is_array($ventas_vendedor_tipo_factura) AND !empty($ventas_vendedor_tipo_factura)) ? $ventas_vendedor_tipo_factura : array();

		$select = "e.vendedor AS VID, ROUND(SUM(CASE WHEN nc.tipofactura = 4 THEN nc.importe_total WHEN nc.tipofactura = 5 THEN nc.importe_total ELSE 0 END),2) AS BLANCO, ROUND(SUM(CASE WHEN nc.tipofactura = 6 THEN nc.importe_total ELSE 0         END),2) AS NEGRO, ROUND(SUM(nc.importe_total), 2) AS TOTAL";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "e.vendedor";
		$notacredito_vendedor_tipo_factura = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$notacredito_vendedor_tipo_factura = (is_array($notacredito_vendedor_tipo_factura) AND !empty($notacredito_vendedor_tipo_factura)) ? $notacredito_vendedor_tipo_factura : array();

		foreach ($ventas_vendedor_tipo_factura as $clave=>$valor) {
			$ventas_vendedor_id = $valor['VID'];
			$ventas_blanco = $valor['BLANCO'];
			$ventas_negro = $valor['NEGRO'];
			$ventas_total = $valor['TOTAL'];

			foreach ($notacredito_vendedor_tipo_factura as $c=>$v) {
				$nc_vendedor_id = $v['VID'];
				$nc_blanco = $v['BLANCO'];
				$nc_negro = $v['NEGRO'];
				$nc_total = $v['TOTAL'];
				
				if ($ventas_vendedor_id == $nc_vendedor_id) {
					$ventas_vendedor_tipo_factura[$clave]['BLANCO'] = $ventas_blanco - $nc_blanco;
					$ventas_vendedor_tipo_factura[$clave]['NEGRO'] = $ventas_negro - $nc_negro;
					$ventas_vendedor_tipo_factura[$clave]['TOTAL'] = $ventas_total - $nc_total;
				}
			}
		}

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, LEFT(pr.razon_social, 25) AS PROVEEDOR, ROUND(SUM(ed.importe),2) AS IMPORTE";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN producto p ON ed.producto_id = p.producto_id INNER JOIN productodetalle pd ON p.producto_id = pd.producto_id INNER JOIN proveedor pr ON pd.proveedor_id = pr.proveedor_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "v.vendedor_id, pr.proveedor_id ORDER BY	CONCAT(v.apellido, ' ', v.nombre), ROUND(SUM(ed.importe),2) DESC";
		$top3_vendedor_proveedor = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$top3_vendedor_proveedor = (is_array($top3_vendedor_proveedor) AND !empty($top3_vendedor_proveedor)) ? $top3_vendedor_proveedor : array();

		$temp_cont_vendedor = 0;
		$array_vendedor_id = array();
		$temp_top3_vendedor_proveedor = array();
		foreach ($top3_vendedor_proveedor as $clave=>$valor) {
			$temp_vendedor_id = $valor['VID'];

			if (!in_array($temp_vendedor_id, $array_vendedor_id)) {
				$array_vendedor_id[] = $temp_vendedor_id;
				$temp_cont_vendedor = 1;
			} else {
				$temp_cont_vendedor = $temp_cont_vendedor + 1;
			}

			if ($temp_cont_vendedor > 3) {
				unset($top3_vendedor_proveedor[$clave]);
			} else {
				$temp_top3_vendedor_proveedor[] = $valor;
			}
		}

		$flag_ini = 0;
		$cant_array = count($temp_top3_vendedor_proveedor);
		$array_vendedor_id = array();
		$temp_array_totales = array();
		foreach ($temp_top3_vendedor_proveedor as $clave=>$valor) {
			$temp_vendedor_id = $valor['VID'];

			if (!in_array($temp_vendedor_id, $array_vendedor_id)) {
				$array_vendedor_id[] = $temp_vendedor_id;

				if ($flag_ini == 0) {
					$temp_vendedor_denominacion = $valor['VENDEDOR'];
					$temp_array_totales[] = array('{PROVEEDOR}'=>$valor['PROVEEDOR'], '{IMPORTE}'=>'$' . $valor['IMPORTE']);
					$flag_ini = 1;
				} else {
					if (count($temp_array_totales) < 3) {
						$faltantes = 3 - count($temp_array_totales);
						for ($i=0; $i < $faltantes; $i++) $temp_array_totales[] = array('{PROVEEDOR}'=>'-', '{IMPORTE}'=>'');
					}

					$temp_array = array('{VENDEDOR}'=>$temp_vendedor_denominacion,
										'ARRAY_TOTALES'=>$temp_array_totales);
					$top3_vendedor_proveedor_final[] = $temp_array;
					$temp_array_totales = array();
					$temp_array_totales[] = array('{PROVEEDOR}'=>$valor['PROVEEDOR'], '{IMPORTE}'=>'$' . $valor['IMPORTE']);
					$temp_vendedor_denominacion = $valor['VENDEDOR'];
				}
			} else {
				$temp_array_totales[] = array('{PROVEEDOR}'=>$valor['PROVEEDOR'], '{IMPORTE}'=>'$' . $valor['IMPORTE']);
				if ($cant_array == ($clave + 1)) {
					if (count($temp_array_totales) < 3) {
						$faltantes = 3 - count($temp_array_totales);
						for ($i=0; $i < $faltantes; $i++) $temp_array_totales[] = array('{PROVEEDOR}'=>'-', '{IMPORTE}'=>'');
					}

					$temp_array = array('{VENDEDOR}'=>$temp_vendedor_denominacion,
										'ARRAY_TOTALES'=>$temp_array_totales);
					$top3_vendedor_proveedor_final[] = $temp_array;
					$temp_array_totales = array();
				}
			}
		}

		$array_fechas = array('{desde}'=>$desde, '{hasta}'=>$hasta);

		$this->view->filtro_estadisticas($vendedor_collection, $ventas_vendedor_tipo_factura,$array_fechas, $top3_vendedor_proveedor_final);
	}

	function ventas_vendedor() {
		SessionHandler()->check_session();

		$fecha_desde = filter_input(INPUT_POST, 'fecha_desde');
		$fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		if (is_null($fecha_desde) AND is_null($fecha_hasta) AND is_null($vendedor_id)) {
			if (isset($_SESSION["data-search-" . APP_ABREV])) {
				$fecha_desde = $_SESSION["data-search-" . APP_ABREV]['{fecha_desde}'];
				$fecha_hasta = $_SESSION["data-search-" . APP_ABREV]['{fecha_hasta}'];
				$vendedor_id = $_SESSION["data-search-" . APP_ABREV]['{vendedor_id}'];
			} else {
				header("Location: " . URL_APP . "/vendedor/ventas_vendedor");
			}
		}

		$array_busqueda = array('{fecha_desde}'=>$fecha_desde,
								'{fecha_hasta}'=>$fecha_hasta,
								'{vendedor_id}'=>$vendedor_id);
		$_SESSION["data-search-" . APP_ABREV] = $array_busqueda;

		$vm = new Vendedor();
		$vm->vendedor_id = $vendedor_id;
		$vm->get();

		$select = "e.egreso_id AS EGRESO_ID, e.fecha AS FECHA, cl.razon_social AS CLIENTE, ci.denominacion AS CI,
    			   e.subtotal AS SUBTOTAL, ec.fecha AS FECCOM, ROUND(ec.valor_abonado,2) AS VALABO,
    			   e.importe_total AS IMPORTETOTAL, CONCAT(ve.APELLIDO, ' ', ve.nombre) AS VENDEDOR, cp.denominacion AS CP,
    			   ec.valor_comision AS COMISION, ROUND((ec.valor_comision * e.importe_total / 100),2) AS VC, esc.denominacion AS ESTCOM,
    			   CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0))
				   ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA,
				   'checked' AS CHK";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN
				 vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN
				 condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
				 egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN
				 estadocomision esc ON ec.estadocomision = esc.estadocomision_id LEFT JOIN
				 egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where_pendiente = "e.vendedor = {$vendedor_id} AND ec.estadocomision IN(1,2) AND e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' ORDER BY e.fecha DESC";
		$where_total = "e.vendedor = {$vendedor_id} AND e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' ORDER BY e.fecha DESC";
		$egreso_pendiente_collection = CollectorCondition()->get('Egreso', $where_pendiente, 4, $from, $select);
		$egreso_total_collection = CollectorCondition()->get('Egreso', $where_total, 4, $from, $select);

		$valor_comision_total = 0;
		$valor_total_facturado = 0;
		$porcentaje_comision = 0;
		foreach ($egreso_total_collection as $clave=>$valor) {
			$porcentaje_comision = ($egreso_total_collection[$clave]['COMISION'] == $porcentaje_comision) ? $porcentaje_comision : $egreso_total_collection[$clave]['COMISION'];
			$egreso_id = $valor['EGRESO_ID'];
			$select = "nc.importe_total AS IMPORTETOTAL";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$egreso_id}";
			$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($notacredito) AND !empty($notacredito)) {
				$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
				$egreso_total_collection[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
				$egreso_total_collection[$clave]['IMPORTETOTAL'] = $egreso_total_collection[$clave]['IMPORTETOTAL'] - $importe_notacredito;
				$egreso_total_collection[$clave]['VC'] = round(($egreso_total_collection[$clave]['COMISION'] * $egreso_total_collection[$clave]['IMPORTETOTAL'] / 100),2);

			} else {
				$egreso_total_collection[$clave]['NC_IMPORTE_TOTAL'] = 0;
			}

			$valor_comision_total = $valor_comision_total + $egreso_total_collection[$clave]["VC"];
			$valor_total_facturado = $valor_total_facturado + $egreso_total_collection[$clave]["IMPORTETOTAL"];

			if ($egreso_total_collection[$clave]['IMPORTETOTAL'] == 0 AND $egreso_total_collection[$clave]["VC"] == 0) {
				unset($egreso_total_collection[$clave]);
			}
		}

		$valor_comision_pendiente = 0;
		$valor_total_facturado_comision_pendiente = 0;
		foreach ($egreso_pendiente_collection as $clave=>$valor) {
			$egreso_id = $valor['EGRESO_ID'];
			$select = "nc.importe_total AS IMPORTETOTAL";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$egreso_id}";
			$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($notacredito) AND !empty($notacredito)) {
				$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
				$egreso_pendiente_collection[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
				$egreso_pendiente_collection[$clave]['IMPORTETOTAL'] = $egreso_pendiente_collection[$clave]['IMPORTETOTAL'] - $importe_notacredito;
				$egreso_pendiente_collection[$clave]['VC'] = round(($egreso_pendiente_collection[$clave]['COMISION'] * $egreso_pendiente_collection[$clave]['IMPORTETOTAL'] / 100),2);

			} else {
				$egreso_pendiente_collection[$clave]['NC_IMPORTE_TOTAL'] = 0;
			}

			$valor_comision_pendiente = $valor_comision_pendiente + $egreso_pendiente_collection[$clave]["VC"];
			$valor_total_facturado_comision_pendiente = $valor_total_facturado_comision_pendiente + $egreso_pendiente_collection[$clave]["IMPORTETOTAL"];

			if ($egreso_pendiente_collection[$clave]['IMPORTETOTAL'] == 0 AND $egreso_pendiente_collection[$clave]["VC"] == 0) {
				unset($egreso_pendiente_collection[$clave]);
			}
		}


		/*COMISION SIN IVA*/
		$valor_comision_pendiente_siniva = 0;
		foreach ($egreso_pendiente_collection as $clave=>$valor) {
			$egreso_id = $valor['EGRESO_ID'];
			$select = "SUM(ROUND((importe/CONCAT(1,'.',(iva))),2)) as ITSINIVA";
			$from = "egresodetalle";
			$where = "egreso_id = {$egreso_id}";
			$egreso_siniva_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);
			$importe_total_sin_iva = $egreso_siniva_collection[0]['ITSINIVA'];
			$egreso_pendiente_collection[$clave]['ITSINIVA'] =  $importe_total_sin_iva;
			$egreso_pendiente_collection[$clave]['CSINIVA'] =  ROUND(($valor['COMISION'] * $importe_total_sin_iva / 100),2);

			/*nota de credito sin iva*/
			$select = "SUM(ROUND((importe/CONCAT(1,'.',(iva))),2)) as IMPORTETOTAL";
			$from = "notacreditodetalle";
			$where = "egreso_id = {$egreso_id}";
			$notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);
			if (is_array($notacredito) AND !empty($notacredito)) {

			 $importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
			 $egreso_pendiente_collection[$clave]['NC_IMPORTE_TOTAL_SINIVA'] = $importe_notacredito;
			 $egreso_pendiente_collection[$clave]['ITSINIVA'] = $egreso_pendiente_collection[$clave]['ITSINIVA'] - $importe_notacredito;
			 $egreso_pendiente_collection[$clave]['CSINIVA'] = round(($egreso_pendiente_collection[$clave]['COMISION'] * $egreso_pendiente_collection[$clave]['ITSINIVA'] / 100),2);
		 } else {
			 $egreso_pendiente_collection[$clave]['NC_IMPORTE_TOTAL_SINIVA'] = 0;
		 }

		 $valor_comision_pendiente_siniva = $valor_comision_pendiente_siniva + $egreso_pendiente_collection[$clave]["CSINIVA"];
		}
		$select_ventas_per_actual = "date_format(e.fecha, '%Y%m') AS PERIODO, COUNT(e.egreso_id) AS CANTIDAD";
		$from_ventas_per_actual = "egreso e";
		$where_ventas_per_actual = "e.fecha >= date_sub(curdate(), interval 6 month) AND e.vendedor = {$vendedor_id}";
		$groupby_ventas_per_actual = "date_format(e.fecha, '%Y%m')";
		$ventas_per_actual_collection = CollectorCondition()->get('Egreso', $where_ventas_per_actual, 4, $from_ventas_per_actual,
										$select_ventas_per_actual, $groupby_ventas_per_actual);

		//$valor_comision_abonado = $valor_comision_total - $valor_comision_pendiente;
		$valor_comision_abonado_siniva = $valor_comision_total - $valor_comision_pendiente_siniva;

		/*Comisión Abonada-SIN IVA*/
		$select = "ROUND(SUM(ec.valor_abonado),2) AS VALOR_ABONADO";
		$from = "egreso e INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN
				 estadocomision esc ON ec.estadocomision = esc.estadocomision_id";
		$where_pendiente = "e.vendedor = {$vendedor_id} AND ec.estadocomision IN(3) AND e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' AND ec.iva = 1 ORDER BY e.fecha DESC";
		$egresopendiente_siniva_collection = CollectorCondition()->get('Egreso', $where_pendiente, 4, $from, $select);

		$select = "ROUND(SUM(ec.valor_abonado),2) AS VALOR_ABONADO";
		$from = "egreso e INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN
				 estadocomision esc ON ec.estadocomision = esc.estadocomision_id";
		$where_pendiente = "e.vendedor = {$vendedor_id} AND ec.estadocomision IN(3) AND e.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' AND ec.iva = 0 ORDER BY e.fecha DESC";
		$comisionabonada_collection = CollectorCondition()->get('Egreso', $where_pendiente, 4, $from, $select);

		if (is_array($egresopendiente_siniva_collection) AND !empty($egresopendiente_siniva_collection)) {
			$abonado_siniva = $egresopendiente_siniva_collection[0]['VALOR_ABONADO'];
			$valor_comision_abonado_siniva = $abonado_siniva;
		}else {
			$valor_comision_abonado_siniva = 0;
		}

		if (is_array($comisionabonada_collection) AND !empty($comisionabonada_collection)) {
			$abonado = $comisionabonada_collection[0]['VALOR_ABONADO'];
			$valor_comision_abonado = $abonado;
		}else {
			$valor_comision_abonado = 0;
		}

		$array_totales = array('{fecha_desde}'=>$fecha_desde,
							   '{fecha_hasta}'=>$fecha_hasta,
							   '{desde}'=>$fecha_desde,
							   '{hasta}'=>$fecha_hasta,
							   '{porcentaje_comision}'=>"%{$porcentaje_comision}",
							   '{valor_comision_total}'=>round($valor_comision_total,2),
		 					   '{valor_comision_pendiente}'=>round($valor_comision_pendiente,2),
							   '{valor_comision_pendiente_siniva}'=>round($valor_comision_pendiente_siniva,2),
		 					   '{valor_comision_abonada}'=>round($valor_comision_abonado,2),
							   '{valor_comision_abonado_siniva}'=>round($valor_comision_abonado_siniva,2),
		 					   '{valor_total_facturado}'=>round($valor_total_facturado,2),
		 					   '{valor_total_facturado_comision_pendiente}'=>round($valor_total_facturado_comision_pendiente,2));

		$this->view->ventas_vendedor($egreso_pendiente_collection, $egreso_total_collection, $ventas_per_actual_collection,
									 $array_busqueda, $vm, $array_totales);
	}

	function cambiar_comision_conjunta() {
		SessionHandler()->check_session();
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor_id');
		$comision = filter_input(INPUT_POST, 'comision');

		$select = "e.egreso_id AS EGRESO_ID, ec.egresocomision_id AS ECID";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id";
		$where_pendiente = "e.vendedor = {$vendedor_id} AND ec.estadocomision IN(1) AND e.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY e.fecha DESC";
		$egreso_collection = CollectorCondition()->get('Egreso', $where_pendiente, 4, $from, $select);

		foreach ($egreso_collection as $clave=>$valor) {
			$egresocomision_id = $egreso_collection[$clave]["ECID"];
			$ecm = new EgresoComision();
			$ecm->egresocomision_id = $egresocomision_id;
			$ecm->get();
			$ecm->valor_comision = $comision;
			$ecm->save();
		}

		header("Location: " . URL_APP . "/vendedor/ventas_vendedor");
	}

	function abonar_comision_conjunta() {
		SessionHandler()->check_session();
		$egreso_ids = $_POST['objeto'];
		$flag_iva = filter_input(INPUT_POST, 'flag_iva');
		$fecha_desde = filter_input(INPUT_POST, 'fecha_desde');
		$fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta');
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor_id');
		
		$array_busqueda = array("{fecha_desde}"=>$fecha_desde, "{fecha_hasta}"=>$fecha_hasta, "{vendedor_id}"=>$vendedor_id);
    	$_SESSION["data-search-" . APP_ABREV] = $array_busqueda;
			
    	$select = "ve.empleado_id AS ID";
	 	$from = "vendedorempleado ve";
	 	$where = "ve.vendedor_id = {$vendedor_id}";
	 	$empleado_id = CollectorCondition()->get('VendedorEmpleado', $where, 4, $from, $select);
	 	$empleado_id = (is_array($empleado_id) AND !empty($empleado_id)) ? $empleado_id[0]['ID'] : 0;
	 	
		$importe_salario = 0;
		if ($flag_iva == 0) {
			foreach ($egreso_ids as $egreso_id) {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();
				$importe_total = $em->importe_total;
				$comision = $em->egresocomision->valor_comision;

				$select = "nc.importe_total AS IMPORTETOTAL";
				$from = "notacredito nc";
				$where = "nc.egreso_id = {$egreso_id}";
				$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

				if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
					$importe_total = $importe_total - $importe_notacredito;
				}

				$valor_abonado = round(($comision * $importe_total / 100),2);
				$ecm = new EgresoComision();
				$ecm->egresocomision_id = $em->egresocomision->egresocomision_id;
				$ecm->get();
				$ecm->fecha = filter_input(INPUT_POST, 'fecha_pago');
				$ecm->valor_abonado = $valor_abonado;
				$ecm->estadocomision = 3;
				$ecm->iva = 0;
				$ecm->save();

				$importe_salario = $importe_salario + $valor_abonado;
			} 
		} else {
			/*PAGO COMISION SIN IVA*/
			foreach ($egreso_ids as $egreso_id) {
			 	$em = new Egreso();
			 	$em->egreso_id = $egreso_id;
			 	$em->get();
			 	$comision = $em->egresocomision->valor_comision;

 			 	$select = "SUM(ROUND((importe/CONCAT(1,'.',(iva))),2)) as ITSINIVA";
			 	$from = "egresodetalle";
			 	$where = "egreso_id = {$egreso_id}";
			 	$egreso_siniva_collection = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);
			 	$importe_total_sin_iva = $egreso_siniva_collection[0]['ITSINIVA'];
			 	$comision_sin_iva =  ROUND(($comision * $importe_total_sin_iva / 100),2);

			 	/*nota de credito sin iva*/
			 	$select = "SUM(ROUND((importe/CONCAT(1,'.',(iva))),2)) as IMPORTETOTAL";
			 	$from = "notacreditodetalle";
			 	$where = "egreso_id = {$egreso_id}";
			 	$notacredito = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);

			 	if (is_array($notacredito) AND !empty($notacredito)) {
					$importe_notacredito_sin_iva = $notacredito[0]['IMPORTETOTAL'];
					$importe_total_sin_iva = $importe_total_sin_iva - $importe_notacredito_sin_iva;
 				}

				$valor_abonado = ROUND(($comision * $importe_total_sin_iva / 100),2);

				$ecm = new EgresoComision();
				$ecm->egresocomision_id = $em->egresocomision->egresocomision_id;
				$ecm->get();
				$ecm->fecha = filter_input(INPUT_POST, 'fecha_pago');
				$ecm->valor_abonado = $valor_abonado;
				$ecm->estadocomision = 3;
				$ecm->iva = 1;
				$ecm->save();

				$importe_salario = $importe_salario + $valor_abonado;
			}
		}
		
		if ($empleado_id != 0) {
			$sm = new Salario();
	    	$sm->desde = $desde;
	    	$sm->hasta = $hasta;
	    	$sm->detalle = "Desde {$fecha_desde} hasta {$fecha_hasta}";
			$sm->tipo_pago = 'SALARIO';
			$sm->fecha = date('Y-m-d');
			$sm->hora = date('H:i:s');
			$sm->monto = round($importe_salario, 2);
			$sm->usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
			$sm->empleado = $empleado_id;
			$sm->save();
			$salario_id = $sm->salario_id;
	 	}


		header("Location: " . URL_APP . "/salario/panel");
	}

	function abonar_comision_parcial() {
		SessionHandler()->check_session();
		$egreso_id = filter_input(INPUT_POST, "egreso_id");
		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();

		$ecm = new EgresoComision();
		$ecm->egresocomision_id = $em->egresocomision->egresocomision_id;
		$ecm->get();
		$ecm->fecha = date('Y-m-d');
		$ecm->valor_abonado = filter_input(INPUT_POST, 'valor_abonado');
		$ecm->estadocomision = filter_input(INPUT_POST, 'estadocomision');
		$ecm->save();

		header("Location: " . URL_APP . "/vendedor/ventas_vendedor");
	}

	function guardar() {
		SessionHandler()->check_session();

		$apellido = (is_null(filter_input(INPUT_POST, 'apellido'))) ? '-' : filter_input(INPUT_POST, 'apellido');
		$nombre = (is_null(filter_input(INPUT_POST, 'nombre'))) ? '-' : filter_input(INPUT_POST, 'nombre');
		$documento = (is_null(filter_input(INPUT_POST, 'documento'))) ? 0 : filter_input(INPUT_POST, 'documento');
		$codigopostal = (is_null(filter_input(INPUT_POST, 'codigopostal'))) ? 0 : filter_input(INPUT_POST, 'codigopostal');
		$domicilio = (is_null(filter_input(INPUT_POST, 'domicilio'))) ? '-' : filter_input(INPUT_POST, 'domicilio');
		$localidad = (is_null(filter_input(INPUT_POST, 'localidad'))) ? '-' : filter_input(INPUT_POST, 'localidad');
		$provincia = filter_input(INPUT_POST, 'provincia');
		$documentotipo = filter_input(INPUT_POST, 'documentotipo');
		
		$this->model->apellido = $apellido;
		$this->model->nombre = $nombre;
		$this->model->comision = filter_input(INPUT_POST, 'comision');
		$this->model->frecuenciaventa = filter_input(INPUT_POST, 'frecuenciaventa');
		$this->model->documento = $documento;
		$this->model->documentotipo = $documentotipo;
		$this->model->provincia = $provincia;
		$this->model->codigopostal = $codigopostal;
		$this->model->localidad = $localidad;
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');
		$this->model->domicilio = $domicilio;
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->oculto = 0;
		$this->model->save();
		$vendedor_id = $this->model->vendedor_id;

		$this->model = new Vendedor();
		$this->model->vendedor_id = $vendedor_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				if ($clave == 'Celular') {
					if (is_null($valor) OR empty($valor) OR $valor == '') {
						$telefono = 0;
					} else {
						$telefono = $valor;
					}
				}
				$icm = new InfoContacto();
				$icm->denominacion = $clave;
				$icm->valor = $valor;
				$icm->save();
				$infocontacto_id = $icm->infocontacto_id;

				$icm = new InfoContacto();
				$icm->infocontacto_id = $infocontacto_id;
				$icm->get();

				$this->model->add_infocontacto($icm);
			}

			$iccm = new InfoContactoVendedor($this->model);
			$iccm->save();
		}

		$em = new Empleado();
		$em->apellido = $apellido;
		$em->nombre = $nombre;
		$em->documento = $documento;
		$em->telefono = $telefono;
		$em->domicilio = $domicilio;
		$em->codigopostal = $codigopostal;
		$em->localidad = $localidad;
		$em->observacion = 'Vendedor';
		$em->oculto = 0;
		$em->provincia = $provincia;
		$em->documentotipo = $documentotipo;
		$em->save();
		$empleado_id = $em->empleado_id;

		$vem = new VendedorEmpleado();
		$vem->vendedor_id = $vendedor_id;
		$vem->empleado_id = $empleado_id;
		$vem->save();

		$cm = new Cobrador();
		$cm->denominacion = "{$apellido} {$nombre}";
		$cm->oculto = 0;
		$cm->vendedor_id = $vendedor_id;
		$cm->flete_id = 0;
		$cm->save();

		header("Location: " . URL_APP . "/vendedor/listar");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$vendedor_id = filter_input(INPUT_POST, 'vendedor_id');
		$this->model->vendedor_id = $vendedor_id;
		$this->model->get();
		$this->model->apellido = filter_input(INPUT_POST, 'apellido');
		$this->model->nombre = filter_input(INPUT_POST, 'nombre');
		$this->model->comision = filter_input(INPUT_POST, 'comision');
		$this->model->frecuenciaventa = filter_input(INPUT_POST, 'frecuenciaventa');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->save();

		$this->model = new Vendedor();
		$this->model->vendedor_id = $vendedor_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->infocontacto_id = $clave;
				$icm->get();
				$icm->valor = $valor;
				$icm->save();
			}
		}

		header("Location: " . URL_APP . "/vendedor/listar");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$vendedor_id = $arg;
		$this->model->vendedor_id = $arg;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();

		$select = "c.cobrador_id AS ID";
		$from = "cobrador c";
		$where = "c.vendedor_id = {$vendedor_id}";
		$cobrador_id = CollectorCondition()->get('Cobrador', $where, 4, $from, $select);
		$cobrador_id = (is_array($cobrador_id) AND !empty($cobrador_id)) ? $cobrador_id[0]['ID'] : 0;
		if ($cobrador_id != 0) {
			$cm = new Cobrador();
			$cm->cobrador_id = $cobrador_id;
			$cm->get();
			$cm->oculto = 1;
			$cm->save();
		}
		
		header("Location: " . URL_APP . "/vendedor/listar");
	}

	function buscar() {
		SessionHandler()->check_session();
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "v.vendedor_id AS VENDEDOR_ID, v.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, v.codigopostal AS CODPOSTAL,
				   v.apellido AS APELLIDO, v.nombre AS NOMBRE, v.documento AS DOCUMENTO, v.comision AS COMISION,
				   CONCAT(fv.denominacion, ' (', fv.dia_1, '-', fv.dia_2, ')') AS FRECUENCIAVENTA";
		$from = "vendedor v INNER JOIN provincia pr ON c.provincia = pr.provincia_id INNER JOIN
				 frecuenciaventa fv ON v.frecuenciaventa = fv.frecuenciaventa_id";
		$where = "v.apellido LIKE '%{$buscar}%' OR v.nombre LIKE '%{$buscar}%' OR v.documento LIKE '%{$buscar}%'";
		$vendedor_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->listar($vendedor_collection);
	}

	function descargar_pago($arg) {
		SessionHandler()->check_session();

		$args = explode('@', $arg);
		$vendedor_id = $args[0];
		$fecha = $args[1];

		$this->model->vendedor_id = $vendedor_id;
		$this->model->get();

		$select = "CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0))
    			   ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA,
    			   ROUND(ec.valor_abonado,2) AS COMISION, ROUND(e.importe_total,2) AS TOTAL, e.condicionpago AS CONDPAGO, estc.denominacion AS ESTCOM";
		$from = "egreso e INNER JOIN egresocomision ec ON e.egresocomision = ec.egresocomision_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id INNER JOIN estadocomision estc ON ec.estadocomision = estc.estadocomision_id INNER JOIN
				 condicioniva ci ON e.condicioniva = ci.condicioniva_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "v.vendedor_id = {$vendedor_id} AND ec.estadocomision IN (2,3) AND ec.fecha = '{$fecha}'
				  ORDER BY date_format(ec.fecha, '%d/%m/%Y') DESC";
		$egresocomision_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		$cant_cuentacorriente = 0;
		$cant_contado = 0;
		$cant_comision = 0;
		foreach ($egresocomision_collection as $egresocomision) {
			$cant_comision = round(($cant_comision + $egresocomision['COMISION']),2);
			switch ($egresocomision['CONDPAGO']) {
			 	case 1:
			 		$cant_cuentacorriente = $cant_cuentacorriente + $egresocomision['TOTAL'];
			 		break;
		 		case 2:
			 		$cant_contado = $cant_contado + $egresocomision['TOTAL'];
			 		break;
			}
		}

		$array_extra = array('{cant_cuentacorriente}'=>$cant_cuentacorriente,
							 '{cant_contado}'=>$cant_contado,
							 '{cant_comision}'=>$cant_comision,
							 '{fecha}'=>$fecha);

		$pagoComisionPDFTool = new pagoComisionPDF();
		$pagoComisionPDFTool->descarga_pago_comision($egresocomision_collection, $array_extra, $this->model);
	}

	function descargar_visita_clientes() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";

		$frecuenciaventa_id = filter_input(INPUT_POST, 'frecuenciaventa');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$this->model->vendedor_id = $vendedor_id;
		$this->model->get();
		$vendedor_denominacion = $this->model->apellido . " " . $this->model->nombre;

		$fvm = new FrecuenciaVenta();
		$fvm->frecuenciaventa_id = $frecuenciaventa_id;
		$fvm->get();
		$frecuencia_denominacion = $fvm->dia_1 . "/" . $fvm->dia_2;

		$select = "LPAD(c.cliente_id, 5, 0) AS CODCLI, c.razon_social AS CLIENTE, c.nombre_fantasia AS FANTASIA, c.domicilio AS DOMICILIO, c.localidad AS BARRIO, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, cf.denominacion AS CONDICION, tf.nomenclatura AS TIPOFAC, (SELECT valor FROM infocontacto ic INNER JOIN infocontactocliente icc ON ic.infocontacto_id = icc.compositor WHERE icc.compuesto = c.cliente_id AND ic.denominacion = 'Teléfono') AS TEL, f.denominacion AS FLETE";
		$from = "cliente c INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN condicionfiscal cf ON c.condicionfiscal = cf.condicionfiscal_id INNER JOIN tipofactura tf ON c.tipofactura = tf.tipofactura_id INNER JOIN flete f ON c.flete = f.flete_id";
		$where = "c.vendedor = {$vendedor_id} AND c.frecuenciaventa = {$frecuenciaventa_id} AND c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$cliente_collection = (!is_array($cliente_collection)) ? array() : $cliente_collection;

		//$visitaClientesVendedorPDFTool = new visitaClientesVendedorPDF();
		//$visitaClientesVendedorPDFTool->descarga_visita_clientes_vendedor($cliente_collection, $this->model);

		$subtitulo = "{$vendedor_denominacion} - {$frecuencia_denominacion}";
		$array_encabezados = array('COD', 'CLIENTE', 'NOM FANTASIA', 'DOCUMENTO', 'CONDICION', 'BARRIO', 'DOMICILIO', 'TELEFONO', 'FLETE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		foreach ($cliente_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array(
						  $valor["CODCLI"]
						, $valor["CLIENTE"]
						, $valor["FANTASIA"]
						, $valor["DOCUMENTO"]
						, $valor["TIPOFAC"]
						, $valor["BARRIO"]
						, $valor["DOMICILIO"]
						, $valor["TEL"]
						, $valor["FLETE"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
	}

	function verifica_documento_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "vendedor v";
		$where = "v.documento = {$arg}";
		$flag = CollectorCondition()->get('Vendedor', $where, 4, $from, $select);
		$flag = (is_array($flag) AND !empty($flag)) ? $flag[0]['DUPLICADO'] : 0;
		print $flag;
	}

	function formulario_abonar_egreso_ajax($arg) {
		SessionHandler()->check_session();
		$em = new Egreso();
		$em->egreso_id = $arg;
		$em->get();

		$select = "nc.importe_total AS NC_IMPORTETOTAL";
		$from = "notacredito nc";
		$where = "nc.egreso_id = {$arg}";
		$nc_importe_total = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$nc_importe_total = (is_array($nc_importe_total) AND !empty($nc_importe_total)) ? $nc_importe_total[0]['NC_IMPORTETOTAL'] : 0;
		$em->nc_importe_total = $nc_importe_total;

		$this->view->formulario_abonar_egreso_ajax($em);
	}
}
?>
