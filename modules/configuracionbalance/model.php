<?php


class ConfiguracionBalance extends StandardObject {
	
	function __construct() {
		$this->configuracionbalance_id = 0;
		$this->activo_caja = "";
		$this->activo_stock_valorizado = "";
		$this->activo_cuenta_corriente_cliente = "";
		$this->activo_carga_pendiente = "";
		$this->pasivo_cuenta_corriente_proveedor = "";
		$this->pasivo_comisiones_pendientes = "";
	}
}
?>