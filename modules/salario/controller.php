<?php
require_once "modules/salario/model.php";
require_once "modules/salario/view.php";
require_once "modules/empleado/model.php";


class SalarioController {

	function __construct() {
		$this->model = new Salario();
		$this->view = new SalarioView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$periodo_actual =  date('Ym');

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, s.monto AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = "date_format(s.fecha, '%Y%m') = {$periodo_actual}";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		$empleado_collection = Collector()->get('Empleado');
		foreach ($empleado_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($empleado_collection[$clave]);
		}

		$this->view->panel($salario_collection, $empleado_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->fecha = date('Y-m-d');
		$this->model->hora = date('H:i:s');
		$this->model->usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
        $this->model->save();
		header("Location: " . URL_APP . "/salario/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
    	$periodo_actual =  date('Ym');

		$this->model->salario_id = $arg;
		$this->model->get();

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, s.monto AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = "date_format(s.fecha, '%Y%m') = {$periodo_actual}";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		$empleado_collection = Collector()->get('Empleado');
		foreach ($empleado_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($empleado_collection[$clave]);
		}

		$this->view->editar($salario_collection, $empleado_collection, $this->model);
	}

	function generar_comprobante($arg) {
    	SessionHandler()->check_session();
    	require_once 'tools/reciboSueldoPDFTool.php';
		$salario_id = $arg;
		$this->model->salario_id = $salario_id;
		$this->model->get();
		$desde = $this->model->desde;
		$hasta = $this->model->hasta;
		$tipo_pago = $this->model->tipo_pago;
		$empleado_id = $this->model->empleado->empleado_id;

		if ($tipo_pago == 'ADELANTO') {
			$salario_collection = array();
		} else {
			$select = "s.monto AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
			$from = "salario s";
			$where = "s.desde BETWEEN '{$desde}' AND '{$hasta}' AND s.tipo_pago = 'ADELANTO' AND s.empleado = {$empleado_id}";
			$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);
			$salario_collection = (is_array($salario_collection) AND !empty($salario_collection)) ? $salario_collection : array();
		}

		$reciboSueldoPDFHelper = new reciboSueldoPDFTool();
		$reciboSueldoPDFHelper->generarReciboSueldo($this->model, $salario_collection);
	}

	function buscar() {
    	SessionHandler()->check_session();
    	$desde = filter_input(INPUT_POST, 'desde');
    	$hasta = filter_input(INPUT_POST, 'hasta');

		$select = "g.gasto_id AS ID, g.fecha AS FECHA, gc.denominacion AS CATEGORIA, g.detalle AS DETALLE, g.importe AS IMPORTE";
		$from = "gasto g INNER JOIN gastocategoria gc ON g.gastocategoria = gc.gastocategoria_id";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$gasto_collection = CollectorCondition()->get('Gasto', $where, 4, $from, $select);

		$select = "ROUND(SUM(g.importe),2) AS IMPORTE";
		$from = "gasto g";
		$where = "g.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		$sum_gasto = CollectorCondition()->get('Gasto', $where, 4, $from, $select);
		$sum_gasto = (is_array($sum_gasto) AND !empty($sum_gasto)) ? $sum_gasto[0]['IMPORTE'] : 0;

		$gastocategoria_collection = Collector()->get('GastoCategoria');
		$this->view->panel($gasto_collection, $gastocategoria_collection, $sum_gasto);
	}

	function filtrar_salario() {
    	SessionHandler()->check_session();
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$empleado = filter_input(INPUT_POST, 'empleado');

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, s.monto AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = (empty($empleado)) ? "s.fecha BETWEEN '{$desde}' AND '{$hasta}'" : "s.fecha BETWEEN '{$desde}' AND '{$hasta}' and s.empleado = {$empleado}";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		$this->view->filtrar_salario($salario_collection);
	}	
}
?>