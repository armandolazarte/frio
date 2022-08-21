<?php
require_once "modules/movimientocaja/model.php";
require_once "modules/movimientocaja/view.php";
require_once "modules/movimientocajatipo/model.php";


class MovimientoCajaController {

	function __construct() {
		$this->model = new MovimientoCaja();
		$this->view = new MovimientoCajaView();
	}

	function panel() {
		SessionHandler()->check_session();
		#CAJA
		/*
    	$select = "i.ingreso_id AS INGRESO_ID, i.fecha AS FECHA, date_format(i.fecha, '%d/%m/%Y') AS FECEMI, date_format(i.fecha_ingreso, '%d/%m/%Y') AS FECING, date_format(i.fecha_vencimiento, '%d/%m/%Y') AS FECVEN, prv.razon_social AS PROV, ci.denominacion AS CONDI, i.descuento AS DESCUENTO, CONCAT(tf.nomenclatura, ' ', LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, i.costo_total AS TOTAL, i.costo_total_iva AS TIVA, cp.denominacion AS CP, CASE WHEN (SELECT COUNT(ccp.ingreso_id) FROM cuentacorrienteproveedor ccp WHERE ccp.ingreso_id = i.ingreso_id) > 1 THEN 'none' ELSE 'inline-block'END AS DSP_BTN_EDIT";
		$from = "ingreso i INNER JOIN proveedor prv ON i.proveedor = prv.proveedor_id INNER JOIN  condicionpago cp ON i.condicionpago = cp.condicionpago_id INNER JOIN  condicioniva ci ON i.condicioniva = ci.condicioniva_id INNER JOIN tipofactura tf ON i.tipofactura = tf.tipofactura_id ORDER BY i.fecha DESC";
		$ingreso_collection = CollectorCondition()->get('Ingreso', NULL, 4, $from, $select);*/

		$movimientocajatipo_collection = Collector()->get('MovimientoCajaTipo');
		$this->view->panel($movimientocajatipo_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->hora = date('H:i:s');
		$this->model->numero = filter_input(INPUT_POST, 'numero');
		$this->model->banco = filter_input(INPUT_POST, 'banco');
		$this->model->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta');
		$this->model->importe = filter_input(INPUT_POST, 'importe');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->usuario_id = $usuario_id;
		$this->model->movimientocajatipo = filter_input(INPUT_POST, 'movimientocajatipo');
		$this->model->save();
		header("Location: " . URL_APP . "/movimientocajatipo/panel");
	}
}
?>