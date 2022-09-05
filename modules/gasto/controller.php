<?php
require_once "modules/gasto/model.php";
require_once "modules/gasto/view.php";


class GastoController {

	function __construct() {
		$this->model = new Gasto();
		$this->view = new GastoView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$periodo_actual =  date('Ym');
		
		$select = "g.gasto_id AS ID, g.comprobante AS COMPROBANTE, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, FORMAT(g.importe, 2,'de_DE') AS IMPORTE, FORMAT(g.iva, 2,'de_DE') AS IVA, FORMAT(g.total, 2,'de_DE') AS TOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "date_format(g.fecha, '%Y%m') = {$periodo_actual}";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$select = "FORMAT(SUM(g.total), 2,'de_DE') AS IMPORTE";
		$from = "gasto g";
		$where = "date_format(g.fecha, '%Y%m') = {$periodo_actual}";
		$sum_gasto = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$sum_gasto = (is_array($sum_gasto) AND !empty($sum_gasto)) ? $sum_gasto[0]['IMPORTE'] : 0;

		$gastocategoria_collection = Collector()->get('GastoCategoria');
		foreach ($gastocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($gastocategoria_collection[$clave]);
		}

		$this->view->panel($gasto_collection, $gastocategoria_collection, $sum_gasto);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/gasto/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();		
    	$periodo_actual =  date('Ym');

		$this->model->gasto_id = $arg;
		$this->model->get();

		$select = "g.gasto_id AS ID, g.comprobante AS COMPROBANTE, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, FORMAT(g.importe, 2,'de_DE') AS IMPORTE, FORMAT(g.iva, 2,'de_DE') AS IVA, FORMAT(g.total, 2,'de_DE') AS TOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "date_format(g.fecha, '%Y%m') = {$periodo_actual}";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$gastocategoria_collection = Collector()->get('GastoCategoria');
		foreach ($gastocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($gastocategoria_collection[$clave]);
		}
		
		$this->view->editar($gasto_collection, $gastocategoria_collection, $this->model);
	}

	function eliminar($arg) {
		SessionHandler()->check_session();		
    	$this->model->gasto_id = $arg;
		$this->model->delete();
		header("Location: " . URL_APP . "/gasto/panel");
	}

	function buscar() {
    	SessionHandler()->check_session();
    	$desde = filter_input(INPUT_POST, 'desde');
    	$hasta = filter_input(INPUT_POST, 'hasta');
		
		$select = "g.gasto_id AS ID, g.comprobante AS COMPROBANTE, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, FORMAT(g.importe, 2,'de_DE') AS IMPORTE, FORMAT(g.iva, 2,'de_DE') AS IVA, FORMAT(g.total, 2,'de_DE') AS TOTAL";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$select = "FORMAT(SUM(g.total), 2,'de_DE') AS IMPORTE";
		$from = "gasto g";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$sum_gasto = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$sum_gasto = (is_array($sum_gasto) AND !empty($sum_gasto)) ? $sum_gasto[0]['IMPORTE'] : 0;

		$gastocategoria_collection = Collector()->get('GastoCategoria');
		$this->view->panel($gasto_collection, $gastocategoria_collection, $sum_gasto);
	}
}
?>