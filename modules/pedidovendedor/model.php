<?php
require_once "modules/condicionpago/model.php";
require_once "modules/estadopedido/model.php";


class PedidoVendedor extends StandardObject {
	
	function __construct(CondicionPago $condicionpago=NULL, EstadoPedido $estadopedido=NULL) {
		$this->pedidovendedor_id = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->subtotal = 0.00;
		$this->importe_total = 0.00;
		$this->detalle = '';
		$this->condicionpago = $condicionpago;
		$this->estadopedido = $estadopedido;
		$this->vendedor_id = 0;
		$this->cliente_id = 0;
		$this->egreso_id = 0;
		$this->tipofactura_id = 0;
		$this->condicioniva_id = 0;
	}
}
?>