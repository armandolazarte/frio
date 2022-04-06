<?php
require_once "modules/tipofactura/model.php";


class NotaCredito extends StandardObject {
	
	function __construct(TipoFactura $tipofactura=NULL) {
		$this->notacredito_id = 0;
		$this->punto_venta = 0;
		$this->numero_factura = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->subtotal = 0.00;
		$this->importe_total = 0.00;
		$this->numero_cae = 0;
		$this->vencimiento_cae = '';
		$this->egreso_id = 0;
		$this->emitido_afip = 0;
		$this->tipofactura = $tipofactura;
	}

	function eliminar_nota_credito() {
		$sql = "DELETE FROM notacredito WHERE egreso_id = ?";
		$datos = array($this->egreso_id);
		execute_query($sql, $datos);
	}
}
?>