<?php
require_once "modules/cobrador/model.php";
require_once "modules/cobrador/view.php";


class CobradorController {

	function __construct() {
		$this->model = new Cobrador();
		$this->view = new CobradorView();
	}
}
?>