<?php
require_once "modules/tipomovimientocuenta/model.php";
require_once "modules/tipomovimientocuenta/view.php";


class TipoMovimientoCuentaController {

	function __construct() {
		$this->model = new TipoMovimientoCuenta();
		$this->view = new TipoMovimientoCuentaView();
	}
}
?>