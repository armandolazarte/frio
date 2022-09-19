<?php
require_once "modules/transferenciadetalle/model.php";
require_once "modules/transferenciadetalle/view.php";


class TransferenciaDetalleController {

	function __construct() {
		$this->model = new TransferenciaDetalle();
		$this->view = new TransferenciaDetalleView();
	}
}
?>