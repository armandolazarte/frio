<?php
require_once "modules/vehiculomarca/model.php";


class VehiculoModelo extends StandardObject {
	
	function __construct(VehiculoMarca $vehiculomarca=NULL) {
		$this->vehiculomodelo_id = 0;
		$this->denominacion = '';
		$this->capacidad_tanque = 0;
		$this->oculto = 0;
		$this->vehiculomarca = $vehiculomarca;
	}
}
?>