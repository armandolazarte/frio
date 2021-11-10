<?php


class EgresoAFIP extends StandardObject {
	
	function __construct() {
		$this->egresoafip_id = 0;
		$this->punto_venta = 0;
		$this->numero_factura = 0;
		$this->tipofactura = 0;
		$this->cae = '';
		$this->fecha = '';
		$this->vencimiento = '';
		$this->egreso_id = '';
	}
}
?>