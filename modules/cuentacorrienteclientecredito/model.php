<?php


class CuentaCorrienteClienteCredito extends StandardObject {
	
	function __construct() {
		$this->cuentacorrienteclientecredito_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->referencia = '';
		$this->importe = 0.00;
		$this->movimiento = 0.00;
		$this->cuentacorrientecliente_id = 0;
		$this->egreso_id = 0;
		$this->cliente_id = 0;
		$this->chequeclientedetalle_id = 0;
		$this->transferenciaclientedetalle_id = 0;
		$this->usuario_id = 0;
	}
}
?>