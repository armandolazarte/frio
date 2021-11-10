<?php
require_once 'modules/gastosubcategoria/model.php';

class GastoCategoria extends StandardObject {

	function __construct(GastoSubCategoria $gastosubcategoria=NULL) {
		$this->gastocategoria_id = 0;
		$this->codigo = '';
		$this->denominacion = '';
		$this->oculto = 0;
		$this->gastosubcategoria = $gastosubcategoria;
	}
}
?>
