<?php
require_once "modules/estadopedido/model.php";
require_once "modules/estadopedido/view.php";


class EstadoPedidoController {

	function __construct() {
		$this->model = new EstadoPedido();
		$this->view = new EstadoPedidoView();
	}
}
?>