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
		$vehiculomarca_collection = Collector()->get('VehiculoMarca');
		foreach ($vehiculomarca_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($vehiculomarca_collection[$clave]);
		}

		$this->view->panel($vehiculomarca_collection);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
		$this->model->oculto = 0;
        $this->model->save();
		header("Location: " . URL_APP . "/vehiculomarca/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->vehiculomarca_id = $arg;
		$this->model->get();
		$vehiculomarca_collection = Collector()->get('VehiculoMarca');
		foreach ($vehiculomarca_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($vehiculomarca_collection[$clave]);
		}
		
		$this->view->editar($vehiculomarca_collection, $this->model);
	}
	
	function eliminar($arg) {
		SessionHandler()->check_session();
		$vehiculomarca_id = $arg;
		$this->model->vehiculomarca_id = $vehiculomarca_id;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/vehiculomarca/listar");
	}
}
?>