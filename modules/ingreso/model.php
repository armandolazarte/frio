<?php
require_once "modules/producto/model.php";
require_once "modules/proveedor/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/condicionpago/model.php";
require_once "modules/tipofactura/model.php";


class Ingreso extends StandardObject {
	
	function __construct(Producto $producto=NULL, Proveedor $proveedor=NULL, CondicionIVA $condicioniva=NULL, 
						 CondicionPago $condicionpago=NULL, TipoFactura $tipofactura=NULL) {
		$this->ingreso_id = 0;
		$this->punto_venta = 0;
		$this->numero_factura = 0;
		$this->fecha = '';
		$this->fecha_ingreso = '';
		$this->vencimiento = 0;
		$this->fecha_vencimiento = '';
		$this->hora = '';
		$this->iva = 0.00;
		$this->percepcion_iva = 0.00;
		$this->impuesto_interno = 0.00;
		$this->ingresos_brutos = 0.00;
		$this->costo_distribucion = 0.00;
		$this->costo_total = 0.00;
		$this->costo_total_iva = 0.00;
		$this->actualiza_precio_producto = 0;
		$this->actualiza_precio_proveedor = 0;
		$this->actualiza_stock = 0;
        $this->proveedor = $proveedor;
        $this->condicioniva = $condicioniva;
        $this->condicionpago = $condicionpago;
        $this->tipofactura = $tipofactura;
	}
}
?>