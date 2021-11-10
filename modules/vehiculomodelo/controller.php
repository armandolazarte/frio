<?php
require_once "modules/vehiculomodelo/model.php";
require_once "modules/vehiculomodelo/view.php";
require_once "modules/vehiculomarca/model.php";


class VehiculoModeloController {

	function __construct() {
		$this->model = new VehiculoModelo();
		$this->view = new VehiculoModeloView();
	}

    function panel() {
    	SessionHandler()->check_session();		
		$vehiculomodelo_collection = Collector()->get('VehiculoModelo');
		$vehiculomarca_collection = Collector()->get('VehiculoMarca');
		$this->view->panel($vehiculomodelo_collection, $vehiculomarca_collection);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/vehiculomodelo/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();		
		$this->model->vehiculomodelo_id = $arg;
		$this->model->get();
		$vehiculomodelo_collection = Collector()->get('VehiculoModelo');
		$vehiculomarca_collection = Collector()->get('VehiculoMarca');
		$this->view->editar($vehiculomodelo_collection, $vehiculomarca_collection, $this->model);
	}
}
?>