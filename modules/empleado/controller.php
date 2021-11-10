<?php
require_once "modules/empleado/model.php";
require_once "modules/empleado/view.php";
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";


class EmpleadoController {

	function __construct() {
		$this->model = new Empleado();
		$this->view = new EmpleadoView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$this->view->panel();
	}

	function listar() {
		$empleado_collection = Collector()->get('Empleado');
		foreach ($empleado_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($empleado_collection[$clave]);
		}

		$this->view->listar($empleado_collection);
	}

	function listar_ocultos() {
		$empleado_collection = Collector()->get('Empleado');
		foreach ($empleado_collection as $clave=>$valor) {
			if ($valor->oculto == 0) unset($empleado_collection[$clave]);
		}

		$this->view->listar_ocultos($empleado_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$this->view->agregar($provincia_collection, $documentotipo_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->empleado_id = $arg;
		$this->model->get();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$this->view->editar($provincia_collection, $documentotipo_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();

		$this->model->apellido = filter_input(INPUT_POST, 'apellido');
		$this->model->nombre = filter_input(INPUT_POST, 'nombre');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->telefono = filter_input(INPUT_POST, 'telefono');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->oculto = 0;
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->save();
		$empleado_id = $this->model->empleado_id;

		
		header("Location: " . URL_APP . "/empleado/listar");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$empleado_id = filter_input(INPUT_POST, 'empleado_id');
		$this->model->empleado_id = $empleado_id;
		$this->model->get();
		$this->model->apellido = filter_input(INPUT_POST, 'apellido');
		$this->model->nombre = filter_input(INPUT_POST, 'nombre');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->telefono = filter_input(INPUT_POST, 'telefono');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->save();

		header("Location: " . URL_APP . "/empleado/listar");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$empleado_id = $arg;
		$this->model->empleado_id = $empleado_id;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();

		header("Location: " . URL_APP . "/empleado/listar");
	}

	function reactivar($arg) {
		SessionHandler()->check_session();
		$empleado_id = $arg;
		$this->model->empleado_id = $empleado_id;
		$this->model->get();
		$this->model->oculto = 0;
		$this->model->save();

		header("Location: " . URL_APP . "/empleado/listar");
	}

	function buscar() {
		SessionHandler()->check_session();
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "e.empleado_id AS EMPLEADO_ID, e.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, e.codigopostal AS CODPOSTAL,
				   e.apellido AS APELLIDO, e.nombre AS NOMBRE, e.documento AS DOCUMENTO";
		$from = "empleado e INNER JOIN provincia pr ON c.provincia = pr.provincia_id";
		$where = "e.apellido LIKE '%{$buscar}%' OR e.nombre LIKE '%{$buscar}%' OR e.documento LIKE '%{$buscar}%'";
		$empleado_collection = CollectorCondition()->get('Empleado', $where, 4, $from, $select);
		$this->view->listar($empleado_collection);
	}

	function verifica_documento_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "empleado e";
		$where = "e.documento = {$arg}";
		$flag = CollectorCondition()->get('Empleado', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}

	/*
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

		$select_vendedor = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from_vendedor = "vendedor v";
		$vendedor_collection = CollectorCondition()->get('Egreso', NULL, 4, $from_vendedor, $select_vendedor);

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(e.importe_total), 2) AS TOTAL,tf.plantilla_impresion AS TIPOFAC,
		(CASE WHEN tf.nomenclatura = 'R' THEN 'Negro' WHEN tf.nomenclatura = 'B' THEN 'Blanco' WHEN tf.nomenclatura = 'A' THEN 'Blanco' END) AS VENTA";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON tf.tipofactura_id = e.tipofactura";
		$where = "MONTH(e.fecha)=MONTH(CURDATE())";
		$groupby = "e.vendedor,tf.nomenclatura";
		$ventas_vendedor_tipo_factura = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ventas_vendedor_tipo_factura = (is_array($ventas_vendedor_tipo_factura) AND !empty($ventas_vendedor_tipo_factura)) ? $ventas_vendedor_tipo_factura : array();

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(nc.importe_total), 2) AS TOTAL,tf.plantilla_impresion AS TIPOFAC";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON tf.tipofactura_id = e.tipofactura";
		$where = "MONTH(e.fecha)=MONTH(CURDATE())";
		$groupby = "e.vendedor,tf.nomenclatura";
		$notacredito_vendedor_tipo_factura = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$notacredito_vendedor_tipo_factura = (is_array($notacredito_vendedor_tipo_factura) AND !empty($notacredito_vendedor_tipo_factura)) ? $notacredito_vendedor_tipo_factura : array();

		foreach ($ventas_vendedor_tipo_factura as $clave=>$valor) {
			$vendedor_id = $valor['VID'];
			$tipofac = $valor['TIPOFAC'];
			foreach ($notacredito_vendedor_tipo_factura as $c=>$v) {
				if ($vendedor_id == $v['VID'] AND $tipofac == $v['TIPOFAC']) $ventas_vendedor_tipo_factura[$clave]['TOTAL'] = $ventas_vendedor_tipo_factura[$clave]['TOTAL'] - $v['TOTAL'];
			}
		}

		$newArray = array();
		foreach($ventas_vendedor_tipo_factura as $key => $value){
			if (array_search($value['VID'], array_column($newArray, 'VID')) === FALSE) {
					$newArray[] = array('VID'=>$value['VID'], 'VENDEDOR'=>$value['VENDEDOR'],'TOTAL'=>$value['TOTAL'],'TIPOFAC'=>$value['TIPOFAC'],'VENTA'=>$value['VENTA'],'SUMA'=>$value['TOTAL']);
			}else {
				$clave = array_search($value['VID'], array_column($newArray, 'VID'));
				if ($newArray[$clave]['VENTA'] == $value['VENTA']) {
					$newArray[$clave]['SUMA'] += $value['TOTAL'];
				}else {
					$newArray[] = array('VID'=>$value['VID'], 'VENDEDOR'=>$value['VENDEDOR'],'TOTAL'=>$value['TOTAL'],'TIPOFAC'=>$value['TIPOFAC'],'VENTA'=>$value['VENTA'],'SUMA'=>$value['TOTAL']);
				}
			}
		}
		$ventas_vendedor_tipo_factura = $newArray;

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(e.importe_total), 2) AS TOTAL";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "MONTH(e.fecha)=MONTH(CURDATE())";
		$groupby = "e.vendedor";
		$ventas_vendedor = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ventas_vendedor = (is_array($ventas_vendedor) AND !empty($ventas_vendedor)) ? $ventas_vendedor : array();

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(nc.importe_total), 2) AS TOTAL";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id";
		$where = "MONTH(e.fecha)=MONTH(CURDATE())";
		$groupby = "e.vendedor";
		$notacredito_vendedor = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$notacredito_vendedor = (is_array($notacredito_vendedor) AND !empty($notacredito_vendedor)) ? $notacredito_vendedor : array();

		foreach ($ventas_vendedor as $clave=>$valor) {
			$vendedor_id = $valor['VID'];
			foreach ($notacredito_vendedor as $c=>$v) {
				if ($vendedor_id == $v['VID']) $ventas_vendedor[$clave]['TOTAL'] = $ventas_vendedor[$clave]['TOTAL'] - $v['TOTAL'];
			}
		}

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, LEFT(pr.razon_social, 25) AS PROVEEDOR, ROUND(SUM(ed.importe),2) AS IMPORTE";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN
				 producto p ON ed.producto_id = p.producto_id INNER JOIN productodetalle pd ON p.producto_id = pd.producto_id INNER JOIN
				 proveedor pr ON pd.proveedor_id = pr.proveedor_id";
		$where = "MONTH(e.fecha)=MONTH(CURDATE())";
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

		$this->view->estadisticas($vendedor_collection, $ventas_vendedor,$ventas_vendedor_tipo_factura, $top3_vendedor_proveedor_final);
	}

	function filtro_estadisticas() {
		SessionHandler()->check_session();

		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');

		$select_vendedor = "v.vendedor_id AS ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION";
		$from_vendedor = "vendedor v";
		$vendedor_collection = CollectorCondition()->get('Egreso', NULL, 4, $from_vendedor, $select_vendedor);

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(e.importe_total), 2) AS TOTAL,tf.plantilla_impresion AS TIPOFAC,
		(CASE WHEN tf.nomenclatura = 'R' THEN 'Negro' WHEN tf.nomenclatura = 'B' THEN 'Blanco' WHEN tf.nomenclatura = 'A' THEN 'Blanco' END) AS VENTA";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON tf.tipofactura_id = e.tipofactura";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "e.vendedor,tf.nomenclatura";
		$ventas_vendedor_tipo_factura = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ventas_vendedor_tipo_factura = (is_array($ventas_vendedor_tipo_factura) AND !empty($ventas_vendedor_tipo_factura)) ? $ventas_vendedor_tipo_factura : array();

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(nc.importe_total), 2) AS TOTAL,tf.plantilla_impresion AS TIPOFAC";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON tf.tipofactura_id = e.tipofactura";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "e.vendedor,tf.nomenclatura";
		$notacredito_vendedor_tipo_factura = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$notacredito_vendedor_tipo_factura = (is_array($notacredito_vendedor_tipo_factura) AND !empty($notacredito_vendedor_tipo_factura)) ? $notacredito_vendedor_tipo_factura : array();

		foreach ($ventas_vendedor_tipo_factura as $clave=>$valor) {
			$vendedor_id = $valor['VID'];
			$tipofac = $valor['TIPOFAC'];
			foreach ($notacredito_vendedor_tipo_factura as $c=>$v) {
				if ($vendedor_id == $v['VID'] AND $tipofac == $v['TIPOFAC']) $ventas_vendedor_tipo_factura[$clave]['TOTAL'] = $ventas_vendedor_tipo_factura[$clave]['TOTAL'] - $v['TOTAL'];
			}
		}

		$newArray = array();
		foreach($ventas_vendedor_tipo_factura as $key => $value){
			if (array_search($value['VID'], array_column($newArray, 'VID')) === FALSE) {
					$newArray[] = array('VID'=>$value['VID'], 'VENDEDOR'=>$value['VENDEDOR'],'TOTAL'=>$value['TOTAL'],'TIPOFAC'=>$value['TIPOFAC'],'VENTA'=>$value['VENTA'],'SUMA'=>$value['TOTAL']);
			}else {
				$clave = array_search($value['VID'], array_column($newArray, 'VID'));
				if ($newArray[$clave]['VENTA'] == $value['VENTA']) {
					$newArray[$clave]['SUMA'] += $value['TOTAL'];
				}else {
					$newArray[] = array('VID'=>$value['VID'], 'VENDEDOR'=>$value['VENDEDOR'],'TOTAL'=>$value['TOTAL'],'TIPOFAC'=>$value['TIPOFAC'],'VENTA'=>$value['VENTA'],'SUMA'=>$value['TOTAL']);
				}
			}
		}
		$ventas_vendedor_tipo_factura = $newArray;

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(e.importe_total), 2) AS TOTAL";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "e.vendedor";
		$ventas_vendedor = CollectorCondition()->get('Egreso', $where, 4, $from, $select, $groupby);
		$ventas_vendedor = (is_array($ventas_vendedor) AND !empty($ventas_vendedor)) ? $ventas_vendedor : array();

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ROUND(SUM(nc.importe_total), 2) AS TOTAL";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN
				 vendedor v ON e.vendedor = v.vendedor_id";
		$where = "e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$groupby = "e.vendedor";
		$notacredito_vendedor = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select, $groupby);
		$notacredito_vendedor = (is_array($notacredito_vendedor) AND !empty($notacredito_vendedor)) ? $notacredito_vendedor : array();

		foreach ($ventas_vendedor as $clave=>$valor) {
			$vendedor_id = $valor['VID'];
			foreach ($notacredito_vendedor as $c=>$v) {
				if ($vendedor_id == $v['VID']) $ventas_vendedor[$clave]['TOTAL'] = $ventas_vendedor[$clave]['TOTAL'] - $v['TOTAL'];
			}
		}

		$select = "v.vendedor_id AS VID, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, LEFT(pr.razon_social, 25) AS PROVEEDOR, ROUND(SUM(ed.importe),2) AS IMPORTE";
		$from = "egreso e INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN egresodetalle ed ON e.egreso_id = ed.egreso_id INNER JOIN
				 producto p ON ed.producto_id = p.producto_id INNER JOIN productodetalle pd ON p.producto_id = pd.producto_id INNER JOIN
				 proveedor pr ON pd.proveedor_id = pr.proveedor_id";
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

		$this->view->filtro_estadisticas($vendedor_collection, $ventas_vendedor,$ventas_vendedor_tipo_factura,$array_fechas, $top3_vendedor_proveedor_final);
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

		$select_ventas_per_actual = "date_format(e.fecha, '%Y%m') AS PERIODO, COUNT(e.egreso_id) AS CANTIDAD";
		$from_ventas_per_actual = "egreso e";
		$where_ventas_per_actual = "e.fecha >= date_sub(curdate(), interval 6 month) AND e.vendedor = {$vendedor_id}";
		$groupby_ventas_per_actual = "date_format(e.fecha, '%Y%m')";
		$ventas_per_actual_collection = CollectorCondition()->get('Egreso', $where_ventas_per_actual, 4, $from_ventas_per_actual,
										$select_ventas_per_actual, $groupby_ventas_per_actual);

		$valor_comision_abonado = $valor_comision_total - $valor_comision_pendiente;

		$array_totales = array('{fecha_desde}'=>$fecha_desde,
							   '{fecha_hasta}'=>$fecha_hasta,
							   '{porcentaje_comision}'=>"%{$porcentaje_comision}",
							   '{valor_comision_total}'=>round($valor_comision_total,2),
		 					   '{valor_comision_pendiente}'=>round($valor_comision_pendiente,2),
		 					   '{valor_comision_abonada}'=>round($valor_comision_abonado,2),
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
			$ecm->save();
		}

		$array_busqueda = array("{fecha_desde}"=>filter_input(INPUT_POST, 'fecha_desde'),
							 	"{fecha_hasta}"=>filter_input(INPUT_POST, 'fecha_hasta'),
							 	"{vendedor_id}"=>filter_input(INPUT_POST, 'vendedor_id'));
        $_SESSION["data-search-" . APP_ABREV] = $array_busqueda;
		header("Location: " . URL_APP . "/vendedor/ventas_vendedor");
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

		$select = "LPAD(c.cliente_id, 5, 0) AS CODCLI, c.razon_social AS CLIENTE, c.nombre_fantasia AS FANTASIA, c.domicilio AS DOMICILIO, c.localidad AS BARRIO,
				   CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, cf.denominacion AS CONDICION, tf.nomenclatura AS TIPOFAC";
		$from = "cliente c INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN condicionfiscal cf ON c.condicionfiscal = cf.condicionfiscal_id INNER JOIN
				 tipofactura tf ON c.tipofactura = tf.tipofactura_id";
		$where = "c.vendedor = {$vendedor_id} AND c.frecuenciaventa = {$frecuenciaventa_id} AND c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$cliente_collection = (!is_array($cliente_collection)) ? array() : $cliente_collection;

		//$visitaClientesVendedorPDFTool = new visitaClientesVendedorPDF();
		//$visitaClientesVendedorPDFTool->descarga_visita_clientes_vendedor($cliente_collection, $this->model);

		$subtitulo = "{$vendedor_denominacion} - {$frecuencia_denominacion}";
		$array_encabezados = array('COD', 'CLIENTE', 'NOM FANTASIA', 'DOCUMENTO', 'CONDICION', 'BARRIO', 'DOMICILIO');
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
						, $valor["DOMICILIO"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);

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
	*/
}
?>
