<?php
require_once "modules/movimientocajatipo/model.php";
require_once "modules/movimientocajatipo/view.php";


class MovimientoCajaTipoController {

	function __construct() {
		$this->model = new MovimientoCajaTipo();
		$this->view = new MovimientoCajaTipoView();
	}
}
?>