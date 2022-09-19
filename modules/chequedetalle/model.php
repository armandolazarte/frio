<?php


class ChequeDetalle extends StandardObject {
	
	function __construct() {
		$this->chequedetalle_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->numero = 0;
		$this->fecha_vencimiento = '';
		$this->fecha_pago = '';
		$this->banco = '';
		$this->plaza = '';
		$this->titular = '';
		$this->documento = '';
		$this->cuenta_corriente = '';
		$this->importe = 0.00;
		$this->detalle = '';
		$this->usuario_id = 0;
	}
}
?>