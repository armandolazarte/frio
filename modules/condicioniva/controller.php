<?php
require_once "modules/condicioniva/model.php";
require_once "modules/condicioniva/view.php";


class CondicionIVAController {

	function __construct() {
		$this->model = new CondicionIVA();
		$this->view = new CondicionIVAView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$this->view->panel($condicioniva_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/condicioniva/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->condicioniva_id = $arg;
		$this->model->get();
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$this->view->editar($condicioniva_collection, $this->model);
	}
}
?>