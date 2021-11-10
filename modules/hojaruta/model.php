<?php
require_once 'modules/estadoentrega/model.php';

class HojaRuta extends StandardObject {
	
	function __construct(EstadoEntrega $estadoentrega=NULL) {
		$this->hojaruta_id = 0;
		$this->fecha = '';
		$this->flete_id = 0;
		$this->egreso_ids = '';
		$this->estadoentrega = $estadoentrega;
	}
}
?>