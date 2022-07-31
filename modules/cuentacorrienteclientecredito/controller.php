<?php
require_once "modules/cuentacorrienteclientecredito/model.php";
require_once "modules/cuentacorrienteclientecredito/view.php";
require_once "modules/cliente/model.php";


class CuentaCorrienteClienteCreditoController {

	function __construct() {
		$this->model = new CuentaCorrienteClienteCredito();
		$this->view = new CuentaCorrienteClienteCreditoView();
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		$cliente_id = $arg;
		$cm = new Cliente();
		$cm->cliente_id = $cliente_id;
		$cm->get();

		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id = {$arg}";
		$groupby = "ccc.cliente_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$select = "cccc.cuentacorrienteclientecredito_id AS ID";
		$from = "cuentacorrienteclientecredito cccc";
		$where = "cccc.cliente_id = {$arg} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
		$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
		$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

		if ($max_cuentacorrienteclientecredito_id == 0) {
			$importe_cuentacorrienteclientecredito = 0;
		} else {
			$cccc = new CuentaCorrienteClienteCredito();
			$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
			$cccc->get();
			$importe_cuentacorrienteclientecredito = $cccc->importe;
		}

		$select = "cccc.cuentacorrienteclientecredito_id AS CTACTECLICREID, cccc.fecha AS FECHA, cccc.referencia AS REFERENCIA, cccc.importe AS BALANCE, cccc.movimiento AS MOVIMIENTO";
		$from = "cuentacorrienteclientecredito cccc";
		$where = "cccc.cliente_id = {$cliente_id}";
		$credito_collection = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);

		$this->view->consultar($credito_collection, $montos_cuentacorriente, $importe_cuentacorrienteclientecredito, $cm);
	}
}
?>