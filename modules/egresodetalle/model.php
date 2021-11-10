<?php
require_once 'modules/egresodetalleestado/model.php';


class EgresoDetalle extends StandardObject {

	function __construct(EgresoDetalleEstado $egresodetalleestado=NULL) {
		$this->egresodetalle_id = 0;
		$this->codigo_producto = '';
		$this->descripcion_producto = '';
		$this->cantidad = 0.00;
		$this->descuento = 0.00;
		$this->valor_descuento = 0.00;
		$this->neto_producto = 0.00;
		$this->costo_producto = 0.00;
		$this->iva = 0.00;
		$this->importe = 0.00;
		$this->valor_ganancia = 0.00;
		$this->producto_id = 0;
		$this->egreso_id = 0;
		$this->egresodetalleestado = 0;
		$this->flete_producto = 0.00;
	}
}
?>
