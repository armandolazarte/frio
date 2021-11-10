<?php
require_once 'modules/condicioniva/model.php';


class Configuracion extends StandardObject {
	
	function __construct(CondicionIVA $condicioniva=NULL) {
		$this->configuracion_id = 0;
		$this->razon_social = '';
		$this->domicilio_comercial = '';
		$this->cuit = 0;
		$this->ingresos_brutos = '';
		$this->fecha_inicio_actividad = '';
		$this->punto_venta = 0;
		$this->condicioniva = $condicioniva;
	}

	function configurar_dias_vencimiento_cta_cte_cliente($dias_vencimiento){
		$sql = "UPDATE cliente SET dias_vencimiento_cuenta_corriente = ?";
		$datos = array($dias_vencimiento);
		execute_query($sql, $datos);
	}

	function configurar_dias_vencimiento_cta_cte_cliente_vendedor($dias_vencimiento, $vendedor){
		$sql = "UPDATE cliente SET dias_vencimiento_cuenta_corriente = ? WHERE vendedor = ?";
		$datos = array($dias_vencimiento, $vendedor);
		execute_query($sql, $datos);
	}
}
?>