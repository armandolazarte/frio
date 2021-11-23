<?php
require_once "modules/almacen/model.php";
require_once "modules/almacen/view.php";


class AlmacenController {

	function __construct() {
		$this->model = new Almacen();
		$this->view = new AlmacenView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$almacen_collection = Collector()->get('Almacen');
		foreach ($almacen_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($almacen_collection[$clave]);
		}

		$this->view->panel($almacen_collection);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->oculto = 0;
        $this->model->save();
		header("Location: " . URL_APP . "/almacen/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->almacen_id = $arg;
		$this->model->get();
		$almacen_collection = Collector()->get('Almacen');
		foreach ($almacen_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($almacen_collection[$clave]);
		}

		$this->view->editar($almacen_collection, $this->model);
	}

	function ocultar($arg) {
		SessionHandler()->check_session();		
		$this->model->almacen_id = $arg;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/almacen/panel");		
	}
}
?>