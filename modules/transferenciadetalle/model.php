<?php


class TransferenciaDetalle extends StandardObject {
	
	function __construct() {
		$this->transferenciadetalle_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->numero = 0;
		$this->banco = '';
		$this->plaza = '';
		$this->numero_cuenta = '';
		$this->importe = 0.00;
		$this->detalle = '';
		$this->usuario_id = 0;
    }
}
?>