<?php


class ChequeClienteDetalle extends StandardObject {
	
	function __construct() {
		$this->chequeclientedetalle_id = 0;
		$this->numero = 0;
		$this->fecha_vencimiento = '';
		$this->fecha_pago = '';
		$this->banco = '';
		$this->plaza = '';
		$this->titular = '';
		$this->documento = '';
		$this->cuenta_corriente = '';
		$this->estado = '';
		$this->cuentacorrientecliente_id = 0;
		$this->egreso_id = 0;
	}
}
?>