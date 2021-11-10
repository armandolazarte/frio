<?php
require_once "modules/productocategoria/model.php";
require_once "modules/productocategoria/view.php";


class ProductoCategoriaController {

	function __construct() {
		$this->model = new ProductoCategoria();
		$this->view = new ProductoCategoriaView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$productocategoria_collection = Collector()->get('ProductoCategoria');
		$this->view->panel($productocategoria_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/productocategoria/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->productocategoria_id = $arg;
		$this->model->get();
		$productocategoria_collection = Collector()->get('ProductoCategoria');
		$this->view->editar($productocategoria_collection, $this->model);
	}
}
?>