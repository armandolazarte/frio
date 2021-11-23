<?php


class Almacen extends StandardObject {
	
	function __construct() {
		$this->almacen_id = 0;
		$this->codigo = 0;
		$this->denominacion = '';
		$this->direccion = '';
		$this->localidad = '';
		$this->oculto = 0;
	}
}
?>