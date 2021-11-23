<?php


class ProductoCategoria extends StandardObject {
	
	function __construct() {
		$this->productocategoria_id = 0;
		$this->denominacion = '';
		$this->detalle = '';
		$this->oculto = 0;
	}
}
?>