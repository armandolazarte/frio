<?php
require_once "modules/chequeclientedetalle/model.php";
require_once "modules/chequeclientedetalle/view.php";


class ChequeClienteDetalleController {

	function __construct() {
		$this->model = new ChequeClienteDetalle();
		$this->view = new ChequeClienteDetalleView();
	}
}
?>