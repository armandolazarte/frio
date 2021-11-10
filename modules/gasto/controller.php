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
		
		$select = "g.gasto_id AS ID, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, g.importe AS IMPORTE";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "date_format(g.fecha, '%Y%m') = {$periodo_actual}";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$select = "ROUND(SUM(g.importe),2) AS IMPORTE";
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

		$select = "g.gasto_id AS ID, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, g.importe AS IMPORTE";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "date_format(g.fecha, '%Y%m') = {$periodo_actual}";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$gastocategoria_collection = Collector()->get('GastoCategoria');
		foreach ($gastocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($gastocategoria_collection[$clave]);
		}
		
		$this->view->editar($gasto_collection, $gastocategoria_collection, $this->model);
	}

	function buscar() {
    	SessionHandler()->check_session();
    	$desde = filter_input(INPUT_POST, 'desde');
    	$hasta = filter_input(INPUT_POST, 'hasta');
		
		$select = "g.gasto_id AS ID, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, g.importe AS IMPORTE";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$select = "ROUND(SUM(g.importe),2) AS IMPORTE";
		$from = "gasto g";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$sum_gasto = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$sum_gasto = (is_array($sum_gasto) AND !empty($sum_gasto)) ? $sum_gasto[0]['IMPORTE'] : 0;

		$gastocategoria_collection = Collector()->get('GastoCategoria');
		$this->view->panel($gasto_collection, $gastocategoria_collection, $sum_gasto);
	}
}
?>