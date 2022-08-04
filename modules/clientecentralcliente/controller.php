<?php
require_once "modules/clientecentralcliente/model.php";
require_once "modules/clientecentralcliente/view.php";


class ClienteCentralClienteController {

	function __construct() {
		$this->model = new ClienteCentralCliente();
		$this->view = new ClienteCentralClienteView();
	}
}
?>