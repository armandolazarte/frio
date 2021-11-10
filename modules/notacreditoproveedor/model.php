<?php
require_once "modules/tipofactura/model.php";


class NotaCreditoProveedor extends StandardObject {
	
	function __construct(TipoFactura $tipofactura=NULL) {
		$this->notacreditoproveedor_id = 0;
		$this->punto_venta = 0;
		$this->numero_factura = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->subtotal = 0.00;
		$this->importe_total = 0.00;
		$this->ingreso_id = 0;
	}

	function eliminar_nota_credito() {
		$sql = "DELETE FROM notacreditoproveedor WHERE ingreso_id = ?";
		$datos = array($this->ingreso_id);
		execute_query($sql, $datos);
	}
}
?>