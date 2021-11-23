<?php


class CuentaContable extends StandardObject {
	
	function __construct() {
		$this->cuentacontable_id = 0;
		$this->codigo = 0;
		$this->denominacion = '';
		$this->oculto = 0;
	}
}
?>