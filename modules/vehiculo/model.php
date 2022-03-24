<?php
require_once 'modules/vehiculomodelo/model.php';
require_once 'modules/combustible/model.php';


class Vehiculo extends StandardObject {
	
	function __construct(Combustible $combustible=NULL, VehiculoModelo $vehiculomodelo=NULL) {
		$this->vehiculo_id = 0;
		$this->denominacion = '';
		$this->dominio = '';
		$this->anio = 0;
		$this->detalle = '';
		$this->oculto = 0;
		$this->combustible = $combustible;
		$this->vehiculomodelo = $vehiculomodelo;
	}
}
?>