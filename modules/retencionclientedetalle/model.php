<?php


class RetencionClienteDetalle extends StandardObject {
	
	function __construct() {
		$this->retencionclientedetalle_id = 0;
		$this->numero = 0;
		$this->mes = 0;
		$this->anio = 0;
		$this->importe = 0.00;
		$this->cuentacorrientecliente_id = 0;
		$this->egreso_id = 0;
		$this->caja = 0;
	}
}
?>