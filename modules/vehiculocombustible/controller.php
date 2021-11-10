<?php
require_once "modules/vehiculocombustible/model.php";
require_once "modules/vehiculocombustible/view.php";


class VehiculoCombustibleController {

	function __construct() {
		$this->model = new VehiculoCombustible();
		$this->view = new VehiculoCombustibleView();
	}
}
?>