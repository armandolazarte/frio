<?php
require_once "modules/tipofactura/model.php";


class CreditoProveedorDetalle extends StandardObject {
	
	function __construct(TipoFactura $tipofactura=NULL) {
		$this->creditoproveedordetalle_id = 0;
		$this->numero = 0;
		$this->importe = 0.00;
		$this->fecha = '';
		$this->cuentacorrienteproveedor_id = 0;
		$this->tipofactura = $tipofactura;
	}
}
?>