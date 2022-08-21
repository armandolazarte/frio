<?php


class MovimientoCajaTipo extends StandardObject {
	
	function __construct() {
		$this->movimientocajatipo_id = 0;
		$this->codigo = '';
        $this->tipo = '';
        $this->destino = '';
	}
}
?>