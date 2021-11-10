<?php
require_once "modules/combustible/model.php";
require_once "modules/combustible/view.php";


class CombustibleController {

	function __construct() {
		$this->model = new Combustible();
		$this->view = new CombustibleView();
	}

	function panel() {
    	SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();
		$combustible_collection = Collector()->get('Combustible');
		$this->view->panel($combustible_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();		
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
        $this->model->save();
		header("Location: " . URL_APP . "/combustible/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();
		$this->model->combustible_id = $arg;
		$this->model->get();
		$combustible_collection = Collector()->get('Combustible');
		$this->view->editar($combustible_collection, $this->model);
	}
}
?>