<?php
require_once "modules/transferenciaproveedordetalle/model.php";
require_once "modules/transferenciaproveedordetalle/view.php";
require_once "modules/proveedor/model.php";


class TransferenciaProveedorDetalleController {

	function __construct() {
		$this->model = new TransferenciaProveedorDetalle();
		$this->view = new TransferenciaProveedorDetalleView();
	}

	function consultar_pagos($arg) {
		SessionHandler()->check_session();
		$proveedor_id = $arg;

		$pm = new Proveedor();
		$pm->proveedor_id = $proveedor_id;
		$pm->get();

		$select = "tpd.transferenciaproveedordetalle_id AS TRAPRODETID, ccp.fecha AS FECHA, tpd.numero AS NUMTRA, tpd.banco AS BANCO, CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE i.tipofactura = tf.tipofactura_id), ' ', LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, ccp.cuentacorrienteproveedor_id AS MOVCCP, ccp.ingreso AS PAGO";
		$from = "transferenciaproveedordetalle tpd INNER JOIN cuentacorrienteproveedor ccp ON tpd.cuentacorrienteproveedor_id = ccp.cuentacorrienteproveedor_id INNER JOIN ingreso i ON ccp.ingreso_id = i.ingreso_id";
		$where = "ccp.proveedor_id = {$proveedor_id}";
		$pagos_transferenciaproveedordetalle_collection = CollectorCondition()->get('TransferenciaProveedorDetalle', $where, 4, $from, $select);

		$select = "ccp.fecha AS FECHA, tpd.numero AS TRANSFERENCIA, ROUND((SUM(ccp.ingreso)), 2) AS PAGO";
		$from = "transferenciaproveedordetalle tpd INNER JOIN cuentacorrienteproveedor ccp ON tpd.cuentacorrienteproveedor_id = ccp.cuentacorrienteproveedor_id";
		$where = "ccp.proveedor_id = {$proveedor_id}";
		$groupby = "tpd.numero";
		$transferenciaproveedordetalle_collection = CollectorCondition()->get('TransferenciaProveedorDetalle', $where, 4, $from, $select, $groupby);

		foreach ($transferenciaproveedordetalle_collection as $clave=>$valor) {
			$num_transferencia = $valor['TRANSFERENCIA'];
			$select = "ccpc.transferenciaproveedordetalle_id AS TRAPRODETID, tpd.numero AS TRANSFERENCIA, ccpc.movimiento SOBRANTE";
			$from = "cuentacorrienteproveedorcredito ccpc INNER JOIN trasnferenciaproveedordetalle tpd ON ccpc.transferenciaproveedordetalle_id = tpd.transferenciaproveedordetalle_id";
			$where = "tpd.numero = {$num_transferencia}";
			$sobrantetransferencia = CollectorCondition()->get('CuentaCorrienteProveedorCredito', $where, 4, $from, $select);

			if (is_array($sobrantetransferencia) AND !empty($sobrantetransferencia)) {
				$transferenciaproveedordetalle_collection[$clave]['CREDITO'] = $sobrantetransferencia[0]['SOBRANTE'];
				$transferenciaproveedordetalle_collection[$clave]['TOTAL'] = round(($valor['PAGO'] + $sobrantetransferencia[0]['SOBRANTE']) , 2);
			} else {
				$transferenciaproveedordetalle_collection[$clave]['CREDITO'] = 0;
				$transferenciaproveedordetalle_collection[$clave]['TOTAL'] = round($valor['PAGO'], 2);
			}
		}

		$this->view->consultar_pagos($pagos_transferenciaproveedordetalle_collection, $transferenciaproveedordetalle_collection, $pm);
	}
}
?>