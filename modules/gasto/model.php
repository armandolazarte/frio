<?php
require_once 'modules/gastocategoria/model.php';


class Gasto extends StandardObject {
	
	function __construct(GastoCategoria $gastocategoria=NULL) {
		$this->gasto_id = 0;
		$this->fecha = '';
		$this->importe = 0.00;
		$this->detalle = '';
		$this->gastocategoria = $gastocategoria;
	}
}
?>