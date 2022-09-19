<?php
require_once "modules/chequedetalle/model.php";
require_once "modules/chequedetalle/view.php";


class ChequeDetalleController {

	function __construct() {
		$this->model = new ChequeDetalle();
		$this->view = new ChequeDetalleView();
	}
}
?>