<?php


class ProductoMarca extends StandardObject {
	
	function __construct() {
		$this->productomarca_id = 0;
		$this->denominacion = '';
		$this->detalle = '';
		$this->oculto = 0;
	}
}
?>