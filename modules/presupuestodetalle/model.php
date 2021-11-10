<?php


class PresupuestoDetalle extends StandardObject {
	
	function __construct() {
		$this->presupuestodetalle_id = 0;
		$this->codigo_producto = '';
		$this->descripcion_producto = '';
		$this->cantidad = 0.00;
		$this->descuento = 0.00;
		$this->valor_descuento = 0.00;
		$this->costo_producto = 0.00;
		$this->iva = 0.00;
		$this->importe = 0.00;
		$this->producto_id = 0;
		$this->presupuesto_id = 0;
	}
}
?>