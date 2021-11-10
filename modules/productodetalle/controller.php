<?php
require_once "modules/productodetalle/model.php";
require_once "modules/productodetalle/view.php";


class ProductoDetalleController {

	function __construct() {
		$this->model = new ProductoDetalle();
		$this->view = new ProductoDetalleView();
	}
}
?>