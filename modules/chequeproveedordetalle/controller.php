<?php
require_once "modules/chequeproveedordetalle/model.php";
require_once "modules/chequeproveedordetalle/view.php";
require_once "modules/proveedor/model.php";


class ChequeProveedorDetalleController {

	function __construct() {
		$this->model = new ChequeProveedorDetalle();
		$this->view = new ChequeProveedorDetalleView();
	}

	function consultar_pagos($arg) {
		SessionHandler()->check_session();
		$proveedor_id = $arg;

		$pm = new Proveedor();
		$pm->proveedor_id = $proveedor_id;
		$pm->get();

		$select = "cpd.chequeproveedordetalle_id AS CHEPRODETID, cpd.fecha_pago AS FECHA, cpd.numero AS NUM_CHEQUE, cpd.titular AS TITULAR, cpd.banco AS BANCO, CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE i.tipofactura = tf.tipofactura_id), ' ', LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, ccp.cuentacorrienteproveedor_id AS MOVCCP, ccp.ingreso AS PAGO";
		$from = "chequeproveedordetalle cpd INNER JOIN cuentacorrienteproveedor ccp ON cpd.cuentacorrienteproveedor_id = ccp.cuentacorrienteproveedor_id INNER JOIN ingreso i ON ccp.ingreso_id = i.ingreso_id";
		$where = "ccp.proveedor_id = {$proveedor_id}";
		$pagos_chequeproveedordetalle_collection = CollectorCondition()->get('ChequeProveedorDetalle', $where, 4, $from, $select);

		$select = "cpd.fecha_pago AS FECHA, cpd.numero AS CHEQUE, ROUND((SUM(ccp.ingreso)), 2) AS PAGO";
		$from = "chequeproveedordetalle cpd INNER JOIN cuentacorrienteproveedor ccp ON cpd.cuentacorrienteproveedor_id = ccp.cuentacorrienteproveedor_id";
		$where = "ccp.proveedor_id = {$proveedor_id}";
		$groupby = "cpd.numero";
		$chequeproveedordetalle_collection = CollectorCondition()->get('ChequeProveedorDetalle', $where, 4, $from, $select, $groupby);

		foreach ($chequeproveedordetalle_collection as $clave=>$valor) {
			$num_cheque = $valor['CHEQUE'];
			$select = "ccpc.chequeproveedordetalle_id AS CHEPRODETID, cpd.numero AS CHEQUE, ccpc.movimiento SOBRANTE";
			$from = "cuentacorrienteproveedorcredito ccpc INNER JOIN chequeproveedordetalle cpd ON ccpc.chequeproveedordetalle_id = cpd.chequeproveedordetalle_id";
			$where = "cpd.numero = {$num_cheque}";
			$sobrantecheque = CollectorCondition()->get('CuentaCorrienteProveedorCredito', $where, 4, $from, $select);

			if (is_array($sobrantecheque) AND !empty($sobrantecheque)) {
				$chequeproveedordetalle_collection[$clave]['CREDITO'] = $sobrantecheque[0]['SOBRANTE'];
				$chequeproveedordetalle_collection[$clave]['TOTAL'] = round(($valor['PAGO'] + $sobrantecheque[0]['SOBRANTE']) , 2);
			} else {
				$chequeproveedordetalle_collection[$clave]['CREDITO'] = 0;
				$chequeproveedordetalle_collection[$clave]['TOTAL'] = round($valor['PAGO'], 2);
			}
		}

		foreach ($chequeproveedordetalle_collection as $clave=>$valor) {
			$chequeproveedordetalle_collection[$clave]['PAGO'] = number_format($valor["PAGO"], 2, ',', '.');
			$chequeproveedordetalle_collection[$clave]['SOBRANTE'] = number_format($valor["SOBRANTE"], 2, ',', '.');
			$chequeproveedordetalle_collection[$clave]['CREDITO'] = number_format($valor["CREDITO"], 2, ',', '.');
			$chequeproveedordetalle_collection[$clave]['TOTAL'] = number_format($valor["TOTAL"], 2, ',', '.');
		}

		$this->view->consultar_pagos($pagos_chequeproveedordetalle_collection, $chequeproveedordetalle_collection, $pm);
	}
}
?>