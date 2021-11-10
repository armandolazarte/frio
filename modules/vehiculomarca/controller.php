<?php
require_once "modules/vehiculomarca/model.php";
require_once "modules/vehiculomarca/view.php";


class VehiculoMarcaController {

	function __construct() {
		$this->model = new VehiculoMarca();
		$this->view = new VehiculoMarcaView();
	}

	function panel() {
    	SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();
		$vehiculomarca_collection = Collector()->get('VehiculoMarca');
		$this->view->panel($vehiculomarca_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();		
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
        $this->model->save();
		header("Location: " . URL_APP . "/vehiculomarca/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();
		$this->model->vehiculomarca_id = $arg;
		$this->model->get();
		$vehiculomarca_collection = Collector()->get('VehiculoMarca');
		$this->view->editar($vehiculomarca_collection, $this->model);
	}
}
?>