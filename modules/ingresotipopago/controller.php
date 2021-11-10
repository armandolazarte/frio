<?php
require_once "modules/ingresotipopago/model.php";
require_once "modules/ingresotipopago/view.php";


class IngresoTipoPagoController {

	function __construct() {
		$this->model = new IngresoTipoPago();
		$this->view = new IngresoTipoPagoView();
	}
}
?>