<?php
require_once "modules/vehiculo/model.php";


class VehiculoCombustible extends StandardObject {
	
	function __construct(Vehiculo $vehiculo=NULL) {
		$this->vehiculocombustible_id = 0;
		$this->cantidad = 0.00;
		$this->importe = 0.00;
		$this->fecha = "";
		$this->vehiculo = $vehiculo;
	}
}
?>