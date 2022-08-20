<?php


class TransferenciaProveedorDetalle extends StandardObject {
	
	function __construct() {
		$this->transferenciaproveedordetalle_id = 0;
		$this->numero = 0;
		$this->banco = '';
		$this->plaza = '';
		$this->numero_cuenta = '';
		$this->importe = 0.00;
		$this->cuentacorrienteproveedor_id = 0;
	}
}
?>