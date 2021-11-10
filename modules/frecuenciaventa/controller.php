<?php
require_once "modules/frecuenciaventa/model.php";
require_once "modules/frecuenciaventa/view.php";


class FrecuenciaVentaController {

	function __construct() {
		$this->model = new FrecuenciaVenta();
		$this->view = new FrecuenciaVentaView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$this->view->panel($frecuenciaventa_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/frecuenciaventa/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->frecuenciaventa_id = $arg;
		$this->model->get();
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$this->view->editar($frecuenciaventa_collection, $this->model);
	}
}
?>