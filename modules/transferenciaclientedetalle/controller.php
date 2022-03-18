<?php
require_once "modules/transferenciaclientedetalle/model.php";
require_once "modules/transferenciaclientedetalle/view.php";


class TransferenciaClienteDetalleController {

	function __construct() {
		$this->model = new TransferenciaClienteDetalle();
		$this->view = new TransferenciaClienteDetalleView();
	}
}
?>