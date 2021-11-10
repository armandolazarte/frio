<?php


class ConfiguracionComprobante extends StandardObject {
	
	function __construct() {
		$this->configuracioncomprobante_id = 0;
		$this->dias_alerta_comision = 0;
		$this->dias_vencimiento = 0;
		$this->dias_vencimiento_cuentacorrientecliente = 0;
		$this->facturacion_rapida = 0;
		$this->parteuno_codebar = 0;
		$this->separador_codebar = '';
		$this->partedos_codebar = 0;
	}
}
?>