<?php
require_once "modules/condicionfiscal/model.php";
require_once "modules/condicionfiscal/view.php";


class CondicionFiscalController {

	function __construct() {
		$this->model = new CondicionFiscal();
		$this->view = new CondicionFiscalView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$condicionfiscal_collection = Collector()->get('CondicionFiscal');
		$this->view->panel($condicionfiscal_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/condicionfiscal/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->condicionfiscal_id = $arg;
		$this->model->get();
		$condicionfiscal_collection = Collector()->get('CondicionFiscal');
		$this->view->editar($condicionfiscal_collection, $this->model);
	}
}
?>