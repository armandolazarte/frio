<?php


class ChequeProveedorDetalle extends StandardObject {
	
	function __construct() {
		$this->chequeproveedordetalle_id = 0;
		$this->numero = 0;
		$this->fecha_vencimiento = '';
		$this->fecha_pago = '';
		$this->estado = '';
		$this->cuentacorrienteproveedor_id = 0;
	}
}
?>