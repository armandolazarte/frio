<?php


class Stock extends StandardObject {
	
	function __construct() {
		$this->stock_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->concepto = '';
		$this->codigo = '';
		$this->cantidad_actual = 0.00;
		$this->cantidad_movimiento = 0.00;
		$this->producto_id = 0;
		$this->almacen_id = 0;
	}
}
?>