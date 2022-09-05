<?php
require_once "modules/salario/model.php";
require_once "modules/salario/view.php";
require_once "modules/empleado/model.php";


class SalarioController {

	function __construct() {
		$this->model = new Salario();
		$this->view = new SalarioView();
	}

	function listar() {
    	SessionHandler()->check_session();
    	$periodo_actual =  date('Ym');

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, FORMAT(s.monto, 2,'de_DE') AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = "date_format(s.fecha, '%Y%m') = {$periodo_actual}";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		$empleado_collection = Collector()->get('Empleado');
		foreach ($empleado_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($empleado_collection[$clave]);
		}

		$this->view->listar($salario_collection, $empleado_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->hora = date('H:i:s');
		$this->model->usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
        $this->model->save();
		header("Location: " . URL_APP . "/salario/listar");
	}

	function editar($arg) {
		SessionHandler()->check_session();
    	$periodo_actual =  date('Ym');

		$this->model->salario_id = $arg;
		$this->model->get();

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, FORMAT(s.monto, 2,'de_DE') AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
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

	function filtrar_salario() {
    	SessionHandler()->check_session();
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$empleado = filter_input(INPUT_POST, 'empleado');

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, FORMAT(s.monto, 2,'de_DE') AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = (empty($empleado)) ? "s.fecha BETWEEN '{$desde}' AND '{$hasta}'" : "s.fecha BETWEEN '{$desde}' AND '{$hasta}' and s.empleado = {$empleado}";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		$this->view->filtrar_salario($salario_collection);
	}

	function eliminar($arg) {
		SessionHandler()->check_session();		
    	$this->model->salario_id = $arg;
		$this->model->delete();
		header("Location: " . URL_APP . "/salario/listar");
	}

	function desc_salarios_fecha() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		//PARAMETROS
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		$empleado = filter_input(INPUT_POST, 'empleado');

		$em = new Empleado();
		$em->empleado_id = $empleado;
		$em->get();
		$denominacion_empleado = $em->apellido . ' ' . $em->nombre;

		$select = "s.salario_id AS SALARIO_ID, CONCAT(date_format(s.fecha, '%d/%m/%Y'), ' ', s.hora) AS FECHA, u.denominacion AS USUARIO, CONCAT(e.apellido, ' ', e.nombre) AS EMPLEADO, s.monto AS IMPORTE, s.detalle AS DETALLE, s.tipo_pago AS TIPO, CONCAT('Desde ', date_format(s.desde, '%d/%m/%Y'), ' hasta ', date_format(s.hasta, '%d/%m/%Y')) AS PERIODO";
		$from = "salario s INNER JOIN empleado e ON s.empleado = e.empleado_id INNER JOIN usuario u ON s.usuario_id = u.usuario_id";
		$where = "s.fecha BETWEEN '{$desde}' AND '{$hasta}' AND s.empleado = {$empleado} ORDER BY s.fecha ASC";
		$salario_collection = CollectorCondition()->get('Salario', $where, 4, $from, $select);

		$subtitulo = "SALARIOS: {$desde} - {$hasta} de {$denominacion_empleado}";
		$array_encabezados = array('Fecha', 'Período', 'Detalle', 'Tipo', 'Importe');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;

		foreach ($salario_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array($valor["FECHA"]
								, $valor["PERIODO"]
								, $valor["DETALLE"]
								, $valor["TIPO"]
								, $valor["IMPORTE"]);
			$array_exportacion[] = $array_temp;
		}
		
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}	
}
?>