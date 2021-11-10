<?php
require_once "modules/estadomovimientocuenta/model.php";
require_once "modules/estadomovimientocuenta/view.php";


class EstadoMovimientoCuentaController {

	function __construct() {
		$this->model = new EstadoMovimientoCuenta();
		$this->view = new EstadoMovimientoCuentaView();
	}
}
?>