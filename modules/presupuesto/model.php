<?php
require_once "modules/cliente/model.php";
require_once "modules/vendedor/model.php";


class Presupuesto extends StandardObject {
	
	function __construct(Cliente $cliente=NULL, Vendedor $vendedor=NULL) {
		$this->presupuesto_id = 0;
		$this->punto_venta = 0;
		$this->numero_factura = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->descuento = 0.00;
		$this->subtotal = 0.00;
		$this->importe_total = 0.00;
		$this->cliente = $cliente;
        $this->vendedor = $vendedor;
	}
}
?>