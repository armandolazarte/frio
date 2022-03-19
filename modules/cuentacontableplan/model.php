<?php


class CuentaContablePlan extends StandardObject {
	
	function __construct() {
		$this->cuentacontableplan_id = 0;
		$this->fecha = '';
		$this->codigo = '';
		$this->denominacion = '';
		$this->tipomovimiento = 0;		
		$this->debe_cuenta_id = 0;
		$this->haber_cuenta_id = 0;
		$this->referencia_id = 0;
		$this->usuario_id = 0;
	}

	/*
	TIPOS MOVIMIENTOS
	1 - VENTAS

	REFERENCIA ID
	1 - VENTAS
		1 - CHEQUE
		2 - DEPÓSITO
		3 - EFECTIVO
	*/
}
?>