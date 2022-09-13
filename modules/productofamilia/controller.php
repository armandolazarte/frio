<?php
require_once "modules/productofamilia/model.php";
require_once "modules/productofamilia/view.php";


class ProductoFamiliaController {

	function __construct() {
		$this->model = new ProductoFamilia();
		$this->view = new ProductoFamiliaView();
	}

	function panel() {
    	SessionHandler()->check_session();		
		$productofamilia_collection = Collector()->get('ProductoFamilia');
		$this->view->panel($productofamilia_collection);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/productofamilia/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();		
		$this->model->productofamilia_id = $arg;
		$this->model->get();
		$productofamilia_collection = Collector()->get('ProductoFamilia');
		$this->view->editar($productofamilia_collection, $this->model);
	}
}
?>