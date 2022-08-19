<?php


class CuentaCorrienteProveedorCredito extends StandardObject {
	
	function __construct() {
		$this->cuentacorrienteproveedorcredito_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->referencia = '';
		$this->importe = 0.00;
		$this->movimiento = 0.00;
		$this->cuentacorrienteproveedor_id = 0;
		$this->ingreso_id = 0;
		$this->proveedor_id = 0;
		$this->chequeproveedordetalle_id = 0;
		$this->transferenciaproveedordetalle_id = 0;
		$this->usuario_id = 0;
	}
}
?>