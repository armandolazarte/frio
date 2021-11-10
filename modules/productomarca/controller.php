<?php
require_once "modules/productomarca/model.php";
require_once "modules/productomarca/view.php";


class ProductoMarcaController {

	function __construct() {
		$this->model = new ProductoMarca();
		$this->view = new ProductoMarcaView();
	}

	function panel() {
    	SessionHandler()->check_session();
		
		$productomarca_collection = Collector()->get('ProductoMarca');
		$this->view->panel($productomarca_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/productomarca/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->productomarca_id = $arg;
		$this->model->get();
		$productomarca_collection = Collector()->get('ProductoMarca');
		$this->view->editar($productomarca_collection, $this->model);
	}
}
?>