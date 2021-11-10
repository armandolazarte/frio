<?php
require_once "modules/tipomovimientocuenta/model.php";
require_once "modules/estadomovimientocuenta/model.php";
require_once "modules/cobrador/model.php";


class CuentaCorrienteCliente extends StandardObject {
	
	function __construct(TipoMovimientoCuenta $tipomovimientocuenta=NULL, EstadoMovimientoCuenta $estadomovimientocuenta=NULL, 
						 Cobrador $cobrador=NULL) {
		$this->cuentacorrientecliente_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->referencia = '';
		$this->importe = 0.00;
		$this->ingreso = 0.00;
		$this->cliente_id = 0;
		$this->egreso_id = 0;
        $this->tipomovimientocuenta = $tipomovimientocuenta;
        $this->estadomovimientocuenta = $estadomovimientocuenta;
        $this->cobrador = $cobrador;
	}
}
?>