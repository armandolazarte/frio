<?php
require_once "modules/retencionclientedetalle/model.php";
require_once "modules/retencionclientedetalle/view.php";


class RetencionClienteDetalleController {

	function __construct() {
		$this->model = new RetencionClienteDetalle();
		$this->view = new RetencionClienteDetalleView();
	}
}
?>