<?php
require_once "modules/transferenciaclientedetalle/model.php";
require_once "modules/transferenciaclientedetalle/view.php";
require_once "modules/cliente/model.php";


class TransferenciaClienteDetalleController {

	function __construct() {
		$this->model = new TransferenciaClienteDetalle();
		$this->view = new TransferenciaClienteDetalleView();
	}

	function consultar_pagos_cliente($arg) {
		SessionHandler()->check_session();
		$cliente_id = $arg;

		$cm = new Cliente();
		$cm->cliente_id = $cliente_id;
		$cm->get();

		$select = "tcd.transferenciaclientedetalle_id AS TRACLIDETID, ccc.fecha AS FECHA, tcd.numero AS NUMTRA, tcd.banco AS BANCO, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, ccc.cuentacorrientecliente_id AS MOVCCC, FORMAT(ccc.ingreso, 2,'de_DE') AS PAGO";
		$from = "transferenciaclientedetalle tcd INNER JOIN cuentacorrientecliente ccc ON tcd.cuentacorrientecliente_id = ccc.cuentacorrientecliente_id INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "ccc.cliente_id = {$cliente_id}";
		$pagos_transferenciaclientedetalle_collection = CollectorCondition()->get('TransferenciaClienteDetalle', $where, 4, $from, $select);

		$select = "ccc.fecha AS FECHA, tcd.numero AS TRANSFERENCIA, ROUND((SUM(ccc.ingreso)), 2) AS PAGO";
		$from = "transferenciaclientedetalle tcd INNER JOIN cuentacorrientecliente ccc ON tcd.cuentacorrientecliente_id = ccc.cuentacorrientecliente_id";
		$where = "ccc.cliente_id = {$cliente_id}";
		$groupby = "tcd.numero";
		$transferenciaclientedetalle_collection = CollectorCondition()->get('TransferenciaClienteDetalle', $where, 4, $from, $select, $groupby);

		foreach ($transferenciaclientedetalle_collection as $clave=>$valor) {
			$num_transferencia = $valor['TRANSFERENCIA'];
			$select = "cccc.transferenciaclientedetalle_id AS TRACLIDETID, tcd.numero AS TRANSFERENCIA, cccc.movimiento SOBRANTE";
			$from = "cuentacorrienteclientecredito cccc INNER JOIN trasnferenciaclientedetalle tcd ON cccc.transferenciaclientedetalle_id = tcd.transferenciaclientedetalle_id";
			$where = "tcd.numero = {$num_transferencia}";
			$sobrantetransferencia = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);

			if (is_array($sobrantetransferencia) AND !empty($sobrantetransferencia)) {
				$transferenciaclientedetalle_collection[$clave]['CREDITO'] = $sobrantetransferencia[0]['SOBRANTE'];
				$transferenciaclientedetalle_collection[$clave]['TOTAL'] = round(($valor['PAGO'] + $sobrantetransferencia[0]['SOBRANTE']) , 2);
			} else {
				$transferenciaclientedetalle_collection[$clave]['CREDITO'] = 0;
				$transferenciaclientedetalle_collection[$clave]['TOTAL'] = round($valor['PAGO'], 2);
			}
		}

		foreach ($transferenciaclientedetalle_collection as $clave=>$valor) {
			$transferenciaclientedetalle_collection[$clave]['PAGO'] = number_format($valor["PAGO"], 2, ',', '.');
			$transferenciaclientedetalle_collection[$clave]['SOBRANTE'] = number_format($valor["SOBRANTE"], 2, ',', '.');
			$transferenciaclientedetalle_collection[$clave]['CREDITO'] = number_format($valor["CREDITO"], 2, ',', '.');
			$transferenciaclientedetalle_collection[$clave]['TOTAL'] = number_format($valor["TOTAL"], 2, ',', '.');
		}

		$this->view->consultar_pagos_cliente($pagos_transferenciaclientedetalle_collection, $transferenciaclientedetalle_collection, $cm);
	}
}
?>