<?php


class ChequeProveedorDetalle extends StandardObject {
	
	function __construct() {
		$this->chequeproveedordetalle_id = 0;
		$this->numero = 0;
		$this->fecha_vencimiento = '';
		$this->fecha_pago = '';
		$this->banco = '';
		$this->plaza = '';
		$this->titular = '';
		$this->documento = '';
		$this->cuenta_corriente = '';
		$this->estado = '';
		$this->cuentacorrienteproveedor_id = 0;
	}
}
?>