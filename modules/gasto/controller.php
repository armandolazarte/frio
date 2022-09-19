<?php
require_once "modules/gasto/model.php";
require_once "modules/gasto/view.php";
require_once "modules/ingresotipopago/model.php";
require_once "modules/chequedetalle/model.php";
require_once "modules/transferenciadetalle/model.php";


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

		$ingresotipopago_collection = Collector()->get('IngresoTipoPago');
		foreach ($ingresotipopago_collection as $clave=>$valor) {
			if ($valor->ingresotipopago_id > 3) unset($ingresotipopago_collection[$clave]);
		}

		$this->view->panel($gasto_collection, $gastocategoria_collection, $ingresotipopago_collection, $sum_gasto);
	}

	function guardar() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

		$importe = filter_input(INPUT_POST, 'importe');
		$detalle = filter_input(INPUT_POST, 'detalle');
		$total = filter_input(INPUT_POST, 'total');
		$this->model->comprobante = filter_input(INPUT_POST, 'comprobante');
		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->importe = $importe;
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->total = $total;
		$this->model->detalle = $detalle;
		$this->model->gastocategoria = filter_input(INPUT_POST, 'gastocategoria');
		$this->model->save();

		$ingresotipopago_id = filter_input(INPUT_POST, 'ingresotipopago');
		switch ($ingresotipopago_id) {
			case 1:
				$cdm = new ChequeDetalle();
				$cdm->fecha = date('Y-m-d');
				$cdm->hora = date('H:i:s');
				$cdm->numero = filter_input(INPUT_POST, 'numero_cheque');
				$cdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento_cheque');
				$cdm->fecha_pago = filter_input(INPUT_POST, 'fecha_pago_cheque');
				$cdm->banco = filter_input(INPUT_POST, 'banco_cheque');
				$cdm->plaza = filter_input(INPUT_POST, 'plaza_cheque');
				$cdm->titular = filter_input(INPUT_POST, 'titular_cheque');
				$cdm->documento = filter_input(INPUT_POST, 'documento_cheque');
				$cdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_cheque');
				$cdm->importe = $total;
				$cdm->detalle = $detalle;
				$cdm->usuario_id = $usuario_id;
				$cdm->save();
				break;
			case 2:
				$tdm = new TransferenciaDetalle();
				$tdm->fecha = date('Y-m-d');
				$tdm->hora = date('H:i:s');
				$tdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
				$tdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
				$tdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
				$tdm->numero_cuenta = filter_input(INPUT_POST, 'cuenta_transferencia');
				$tdm->importe = $importe;
				$tdm->detalle = $detalle;
				$tdm->usuario_id = $usuario_id;
				$tdm->save();
				break;
		}

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