<?php


class ListaPrecio extends StandardObject {

	function __construct() {
  		$this->listaprecio_id = 0;
		$this->denominacion = '';
		$this->condicion = '';
    	$this->porcentaje = 0;
    	$this->oculto = 0;
	}
}
?>
