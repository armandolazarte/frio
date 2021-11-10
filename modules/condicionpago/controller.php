<?php
require_once "modules/condicionpago/model.php";
require_once "modules/condicionpago/view.php";


class CondicionPagoController {

	function __construct() {
		$this->model = new CondicionPago();
		$this->view = new CondicionPagoView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$condicionpago_collection = Collector()->get('CondicionPago');
		$this->view->panel($condicionpago_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/condicionpago/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->condicionpago_id = $arg;
		$this->model->get();
		$condicionpago_collection = Collector()->get('CondicionPago');
		$this->view->editar($condicionpago_collection, $this->model);
	}
}
?>