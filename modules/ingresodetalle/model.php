<?php


class IngresoDetalle extends StandardObject {
	
	function __construct() {
		$this->ingresodetalle_id = 0;
		$this->codigo_producto = '';
		$this->descripcion_producto = '';
		$this->cantidad = 0.00;
		$this->descuento1 = 0.00;
		$this->descuento2 = 0.00;
		$this->descuento3 = 0.00;
		$this->costo_producto = 0.00;
		$this->importe = 0.00;
		$this->producto_id = 0;
		$this->ingreso_id = 0;
	}
}
?>