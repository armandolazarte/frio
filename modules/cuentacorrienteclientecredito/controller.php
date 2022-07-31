<?php
require_once "modules/cuentacorrienteclientecredito/model.php";
require_once "modules/cuentacorrienteclientecredito/view.php";


class CuentaCorrienteClienteCreditoController {

	function __construct() {
		$this->model = new CuentaCorrienteClienteCredito();
		$this->view = new CuentaCorrienteClienteCreditoView();
	}

	function consultar_pagos_cliente($arg) {
		SessionHandler()->check_session();
		$cliente_id = $arg;

		$cm = new Cliente();
		$cm->cliente_id = $cliente_id;
		$cm->get();

		$select = "cccc.cuentacorrienteclientecredito_id AS CTACTECLICREID, cccc.fecha AS FECHA, cccc.referencia AS REFERENCIA, cccc.importe AS BALANCE, cccc.movimiento AS MOVIMIENTO";
		$from = "cuentacorrienteclientecredito cccc";
		$where = "cccc.cliente_id = {$cliente_id}";
		$credito_collection = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);

		$this->view->consultar_pagos_cliente($credito_collection, $cm);
	}
}
?>