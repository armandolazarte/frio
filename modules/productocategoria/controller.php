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
		foreach ($productocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productocategoria_collection[$clave]);
		}

		$this->view->panel($productocategoria_collection);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->oculto = 0;		
        $this->model->save();
		header("Location: " . URL_APP . "/productocategoria/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();		
		$this->model->productocategoria_id = $arg;
		$this->model->get();

		$productocategoria_collection = Collector()->get('ProductoCategoria');
		foreach ($productocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productocategoria_collection[$clave]);
		}

		$this->view->editar($productocategoria_collection, $this->model);
	}

	function ocultar($arg) {
		SessionHandler()->check_session();		
		$this->model->productocategoria_id = $arg;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/productocategoria/panel");		
	}
}
?>