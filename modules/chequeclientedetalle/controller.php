<?php
require_once "modules/chequeclientedetalle/model.php";
require_once "modules/chequeclientedetalle/view.php";
require_once "modules/cliente/model.php";


class ChequeClienteDetalleController {

	function __construct() {
		$this->model = new ChequeClienteDetalle();
		$this->view = new ChequeClienteDetalleView();
	}

	function consultar_pagos_cliente($arg) {
		SessionHandler()->check_session();
		$cliente_id = $arg;

		$cm = new Cliente();
		$cm->cliente_id = $cliente_id;
		$cm->get();

		$select = "ccd.chequeclientedetalle_id AS CHECLIDETID, ccd.fecha_pago AS FECHA, ccd.numero AS NUM_CHEQUE, ccd.titular AS TITULAR, ccd.banco AS BANCO, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, ccc.cuentacorrientecliente_id AS MOVCCC, ccc.ingreso AS PAGO";
		$from = "chequeclientedetalle ccd INNER JOIN cuentacorrientecliente ccc ON ccd.cuentacorrientecliente_id = ccc.cuentacorrientecliente_id INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "ccc.cliente_id = {$cliente_id}";
		$chequeclientedetalle_collection = CollectorCondition()->get('ChequeClienteDetalle', $where, 4, $from, $select);

		$select = "ccd.numero AS CHEQUE, ROUND((SUM(ccc.ingreso)), 2) AS PAGO";
		$from = "chequeclientedetalle ccd INNER JOIN cuentacorrientecliente ccc ON ccd.cuentacorrientecliente_id = ccc.cuentacorrientecliente_id";
		$where = "ccc.cliente_id = {$cliente_id}";
		$groupby = "ccd.numero";
		$detallecheque_collection = CollectorCondition()->get('ChequeClienteDetalle', $where, 4, $from, $select, $groupby);

		foreach ($detallecheque_collection as $clave=>$valor) {
			$num_cheque = $valor['CHEQUE'];
			$select = "cccc.chequeclientedetalle_id AS CHECLIDETID, ccd.numero AS CHEQUE, cccc.movimiento SOBRANTE";
			$from = "cuentacorrienteclientecredito cccc INNER JOIN chequeclientedetalle ccd ON cccc.chequeclientedetalle_id = ccd.chequeclientedetalle_id";
			$where = "ccd.numero = {$num_cheque}";
			$sobrantecheque = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);

			print_r($sobrantecheque);

		}

		exit;


		$this->view->consultar_pagos_cliente($chequeclientedetalle_collection, $cm);
	}
}
?>