<?php
require_once 'modules/flete/model.php';
require_once 'modules/estadoentrega/model.php';


class EgresoEntrega extends StandardObject {
	
	function __construct(Flete $flete=NULL, EstadoEntrega $estadoentrega=NULL) {
		$this->egresoentrega_id = 0;
		$this->fecha = '';
		$this->flete = $flete;
		$this->estadoentrega = $estadoentrega;
	}
}
?>