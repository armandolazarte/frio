<?php
require_once "modules/creditoproveedordetalle/model.php";
require_once "modules/creditoproveedordetalle/view.php";


class CreditoProveedorDetalleController {

	function __construct() {
		$this->model = new CreditoProveedorDetalle();
		$this->view = new CreditoProveedorDetalleView();
	}
}
?>