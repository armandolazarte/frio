<?php


class CuentaContablePlan extends StandardObject {
	
	function __construct() {
		$this->cuentacontableplan_id = 0;
		$this->fecha = '';
		$this->codigo = '';
		$this->denominacion = '';
		$this->denominacion = '';
		$this->tipomovimiento = 0;		
		$this->debe_cuenta_id = 0;
		$this->haber_cuenta_id = 0;
	}
}
?>