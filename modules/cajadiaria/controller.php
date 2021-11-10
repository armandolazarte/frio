<?php
require_once "modules/cajadiaria/model.php";
require_once "modules/cajadiaria/view.php";


class CajaDiariaController {

	function __construct() {
		$this->model = new CajaDiaria();
		$this->view = new CajaDiariaView();
	}
}
?>