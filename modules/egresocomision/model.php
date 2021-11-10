<?php
require_once 'modules/estadocomision/model.php';


class EgresoComision extends StandardObject {

	function __construct(EstadoComision $estadocomision=NULL) {
		$this->egresocomision_id = 0;
		$this->fecha = '';
		$this->valor_comision = 0.00;
		$this->valor_abonado = 0.00;
		$this->estadocomision = $estadocomision;
		$this->iva = 0;
	}
}
?>
