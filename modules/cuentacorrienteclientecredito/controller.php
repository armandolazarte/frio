<?php
require_once "modules/cuentacorrienteclientecredito/model.php";
require_once "modules/cuentacorrienteclientecredito/view.php";


class CuentaCorrienteClienteCreditoController {

	function __construct() {
		$this->model = new CuentaCorrienteClienteCredito();
		$this->view = new CuentaCorrienteClienteCreditoView();
	}
}
?>