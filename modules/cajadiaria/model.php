<?php


class CajaDiaria extends StandardObject {
	
	function __construct() {
		$this->cajadiaria_id = 0;
		$this->caja = 0.00;
		$this->fecha = '';
		$this->usuario_id = 0;
	}
}
?>