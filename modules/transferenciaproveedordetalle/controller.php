<?php
require_once "modules/transferenciaproveedordetalle/model.php";
require_once "modules/transferenciaproveedordetalle/view.php";


class TransferenciaProveedorDetalleController {

	function __construct() {
		$this->model = new TransferenciaProveedorDetalle();
		$this->view = new TransferenciaProveedorDetalleView();
	}
}
?>