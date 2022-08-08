<?php


class TransferenciaClienteDetalle extends StandardObject {
	
	function __construct() {
		$this->transferenciaclientedetalle_id = 0;
		$this->numero = 0;
		$this->banco = '';
		$this->plaza = '';
		$this->numero_cuenta = '';
		$this->importe = 0.00;
		$this->cuentacorrientecliente_id = 0;
		$this->egreso_id = 0;
	}
}
?>