<?php


class ProductoDetalle extends StandardObject {
	
	function __construct() {
		$this->productodetalle_id = 0;
		$this->fecha = '';
		$this->precio_costo = 0.0;
		$this->producto_id = 0;
		$this->proveedor_id = 0;
	}
}
?>