<?php
require_once "modules/ingresotipopago/model.php";
require_once "modules/tipomovimientocuenta/model.php";
require_once "modules/estadomovimientocuenta/model.php";


class CuentaCorrienteProveedor extends StandardObject {
	
	function __construct(IngresoTipoPago $ingresotipopago=NULL, TipoMovimientoCuenta $tipomovimientocuenta=NULL, 
						 EstadoMovimientoCuenta $estadomovimientocuenta=NULL) {
		$this->cuentacorrienteproveedor_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->referencia = '';
		$this->importe = 0.00;
		$this->ingreso = 0.00;
		$this->proveedor_id = 0;
		$this->ingreso_id = 0;
        $this->ingresotipopago = $ingresotipopago;
        $this->tipomovimientocuenta = $tipomovimientocuenta;
        $this->estadomovimientocuenta = $estadomovimientocuenta;
	}
}
?>