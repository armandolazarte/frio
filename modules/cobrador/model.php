<?php


class Cobrador extends StandardObject {
	
	function __construct() {
		$this->cobrador_id = 0;
		$this->denominacion = '';
		$this->oculto = 0;
		$this->vendedor_id = 0;
		$this->flete_id = 0;
	}
}
?>