<?php
require_once "modules/clientecentral/model.php";
require_once "modules/clientecentral/view.php";


class ClienteCentralController {

	function __construct() {
		$this->model = new ClienteCentral();
		$this->view = new ClienteCentralView();
	}
}
?>