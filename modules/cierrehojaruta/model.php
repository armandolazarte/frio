<?php
require_once 'modules/cobrador/model.php';


class CierreHojaRuta extends StandardObject {
	
	function __construct(Cobrador $cobrador=NULL) {
		$this->cierrehojaruta_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->rendicion = 0.00;
		$this->hojaruta_id = 0;
		$this->cobrador = $cobrador;
	}
}
?>