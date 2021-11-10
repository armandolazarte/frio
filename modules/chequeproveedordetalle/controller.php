<?php
require_once "modules/chequeproveedordetalle/model.php";
require_once "modules/chequeproveedordetalle/view.php";


class ChequeProveedorDetalleController {

	function __construct() {
		$this->model = new ChequeProveedorDetalle();
		$this->view = new ChequeProveedorDetalleView();
	}
}
?>