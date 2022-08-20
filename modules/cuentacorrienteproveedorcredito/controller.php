<?php
require_once "modules/cuentacorrienteproveedorcredito/model.php";
require_once "modules/cuentacorrienteproveedorcredito/view.php";
require_once "modules/proveedor/model.php";


class CuentaCorrienteProveedorCreditoController {

	function __construct() {
		$this->model = new CuentaCorrienteProveedorCredito();
		$this->view = new CuentaCorrienteProveedorCreditoView();
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		$select = "ccp.proveedor_id AS PID, p.razon_social AS PROVEEDOR, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.proveedor_id";
		$cuentascorrientes_collection = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select, $groupby);

		$proveedor_id = $arg;
		$cm = new Proveedor();
		$cm->proveedor_id = $proveedor_id;
		$cm->get();

		$select = "(SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor c ON ccp.proveedor_id = c.proveedor_id";
		$where = "ccp.proveedor_id = {$proveedor_id}";
		$groupby = "ccp.proveedor_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select, $groupby);

		$select = "cccp.cuentacorrienteproveedorcredito_id AS ID";
		$from = "cuentacorrienteproveedorcredito cccp";
		$where = "cccp.proveedor_id = {$arg} ORDER BY cccp.cuentacorrienteproveedorcredito_id DESC LIMIT 1";
		$max_cuentacorrienteproveedorcredito_id = CollectorCondition()->get('CuentaCorrienteProveedorCredito', $where, 4, $from, $select);
		$max_cuentacorrienteproveedorcredito_id = (is_array($max_cuentacorrienteproveedorcredito_id) AND !empty($max_cuentacorrienteproveedorcredito_id)) ? $max_cuentacorrienteproveedorcredito_id[0]['ID'] : 0;

		if ($max_cuentacorrienteproveedorcredito_id == 0) {
			$importe_cuentacorrienteproveedorcredito = 0;
		} else {
			$cccp = new CuentaCorrienteProveedorCredito();
			$cccp->cuentacorrienteproveedorcredito_id = $max_cuentacorrienteproveedorcredito_id;
			$cccp->get();
			$importe_cuentacorrienteproveedorcredito = $cccp->importe;
		}

		$select = "cccp.cuentacorrienteproveedorcredito_id AS CTACTEPROCREID, cccp.fecha AS FECHA, cccp.referencia AS REFERENCIA, cccp.importe AS BALANCE, cccp.movimiento AS MOVIMIENTO";
		$from = "cuentacorrienteproveedorcredito cccp";
		$where = "cccp.proveedor_id = {$proveedor_id}";
		$credito_collection = CollectorCondition()->get('CuentaCorrienteProveedorCredito', $where, 4, $from, $select);

		$this->view->consultar($cuentascorrientes_collection, $credito_collection, $montos_cuentacorriente, $importe_cuentacorrienteproveedorcredito, $cm);
	}
}
?>