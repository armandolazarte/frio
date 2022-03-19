<?php
require_once "modules/cuentacontableplan/model.php";
require_once "modules/cuentacontableplan/view.php";
require_once "modules/cuentacontable/model.php";
require_once "modules/ingresotipopago/model.php";


class CuentaContablePlanController {

	function __construct() {
		$this->model = new CuentaContablePlan();
		$this->view = new CuentaContablePlanView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$select = "date_format(ccp.fecha, '%d/%m/%Y') AS FECHA, ccp.codigo AS COD, ccp.denominacion AS DENOMINACION, ccd.denominacion AS DEBE, cch.denominacion AS HABER, CASE ccp.tipomovimiento WHEN 1 THEN 'VENTA' WHEN 2 THEN 'COMPRA' WHEN 3 THEN 'SALARIO' WHEN 4 THEN 'COMISIÓN' WHEN 5 THEN 'GASTO' WHEN 6 THEN 'COMBUSTIBLE' ELSE 'SIN DEFINIR' END AS ACCION";
    	$from = "cuentacontableplan ccp INNER JOIN cuentacontable ccd ON ccd.cuentacontable_id = ccp.debe_cuenta_id INNER JOIN cuentacontable cch ON cch.cuentacontable_id = ccp.haber_cuenta_id";
		$cuentacontableplan_collection = CollectorCondition()->get('CuentaContable', NULL, 4, $from, $select);		
		$this->view->panel($cuentacontableplan_collection);
	}

	function configurar() {
    	SessionHandler()->check_session();
		
		$cuentacontable_collection = Collector()->get('CuentaContable');
		foreach ($cuentacontable_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cuentacontable_collection[$clave]);
		}

		$ingresotipopago_collection = Collector()->get('IngresoTipoPago');
		foreach ($ingresotipopago_collection as $clave=>$valor) {
			if ($valor->oculto == 1 OR $valor->ingresotipopago_id == 4) unset($ingresotipopago_collection[$clave]);
		}
		
		$this->view->configurar($cuentacontable_collection, $ingresotipopago_collection);
	}

	function guardar_plan_venta() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$codigo = filter_input(INPUT_POST, 'codigo');
		$ingresotipopago = filter_input(INPUT_POST, 'ingresotipopago');
		$debecuentacontable_id = filter_input(INPUT_POST, 'debecuentacontable_id');
		$habercuentacontable_id = filter_input(INPUT_POST, 'habercuentacontable_id');
		$tipomovimiento = filter_input(INPUT_POST, 'tipomovimiento');

		$itpm = new IngresoTipoPago();
		$itpm->ingresotipopago_id = $ingresotipopago;
		$itpm->get();
		$denominacion = $itpm->denominacion;

		$this->model->fecha = date('Y-m-d');
		$this->model->codigo = $codigo;
		$this->model->denominacion = "Venta {$denominacion}";
		$this->model->tipomovimiento = $tipomovimiento;		
		$this->model->debe_cuenta_id = $debecuentacontable_id;
		$this->model->haber_cuenta_id = $habercuentacontable_id;
		$this->model->referencia_id = $ingresotipopago;
		$this->model->usuario_id = $usuario_id;
		$this->model->save();

		header("Location: " . URL_APP . "/cuentacontableplan/panel");
	}













	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->cuentacontable_id = $arg;
		$this->model->get();
		$cuentacontable_collection = Collector()->get('CuentaContable');
		foreach ($cuentacontable_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cuentacontable_collection[$clave]);
		}
		
		$this->view->editar($cuentacontable_collection, $this->model);
	}

	function ocultar($arg) {
		SessionHandler()->check_session();		
		$this->model->cuentacontable_id = $arg;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/cuentacontable/panel");		
	}
}
?>