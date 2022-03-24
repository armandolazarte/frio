<?php
require_once "modules/egreso/model.php";
require_once "modules/entregacliente/model.php";


class EntregaClienteDetalle  extends StandardObject {

	function __construct(Egreso $egreso=NULL,EntregaCliente $entregacliente=NULL) {
		$this->entregaclientedetalle_id = 0;
		$this->egreso_id = 0;
		$this->monto = 0;
		$this->entregacliente_id = 0;
		$this->parcial = 0;
		$this->ingresotipopago_id = 0;
	}
}
?>
