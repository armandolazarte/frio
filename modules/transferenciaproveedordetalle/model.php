<?php


class TransferenciaProveedorDetalle extends StandardObject {
	
	function __construct() {
		$this->transferenciaproveedordetalle_id = 0;
		$this->numero = 0;
		$this->banco = '';
		$this->plaza = '';
		$this->numero_cuenta = '';
		$this->cuentacorrienteproveedor_id = 0;
	}
}
?>