<?php
require_once "modules/movimientocaja/model.php";
require_once "modules/movimientocaja/view.php";
require_once "modules/movimientocajatipo/model.php";
require_once "modules/usuario/model.php";


class MovimientoCajaController {

	function __construct() {
		$this->model = new MovimientoCaja();
		$this->view = new MovimientoCajaView();
	}

	function panel() {
		SessionHandler()->check_session();
		#CAJA
    	$select = "mc.movimientocaja_id AS MOVCAJID, mc.fecha AS FECHA, mc.numero AS NUMERO, mc.banco AS BANCO, mc.numero_cuenta AS NUMCUENTA, mct.destino AS TIPMOV, FORMAT(mc.importe, 2,'de_DE') AS IMPORTE, mc.detalle AS DETALLE, mct.codigo AS CODIGO, CONCAT(ud.apellido, ' ', ud.nombre) AS USUARIO, CASE WHEN mct.codigo = 'INGCAJ00001' THEN 'success' ELSE 'danger' END AS CLAICO, CASE WHEN mct.codigo = 'INGCAJ00001' THEN 'arrow-up' ELSE 'arrow-down' END AS ICON";
		$from = "movimientocaja mc INNER JOIN movimientocajatipo mct ON mc.movimientocajatipo = mct.movimientocajatipo_id INNER JOIN usuario u ON mc.usuario_id = u.usuario_id INNER JOIN usuariodetalle ud ON u.usuariodetalle = ud.usuariodetalle_id";
		$where = "mct.codigo IN ('INGCAJ00001', 'EGRCAJ00001')";
		$movimientocaja_collection = CollectorCondition()->get('MovimientoCaja', $where, 4, $from, $select);

		$movimientocajatipo_collection = Collector()->get('MovimientoCajaTipo');
		$this->view->panel($movimientocaja_collection, $movimientocajatipo_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$movimientocaja_id = $arg;

		#CAJA
		$select = "mc.movimientocaja_id AS MOVCAJID, mc.fecha AS FECHA, mc.numero AS NUMERO, mc.banco AS BANCO, mc.numero_cuenta AS NUMCUENTA, mct.destino AS TIPMOV, mc.importe AS IMPORTE, mc.detalle AS DETALLE, mct.codigo AS CODIGO, CONCAT(ud.apellido, ' ', ud.nombre) AS USUARIO, CASE WHEN mct.codigo = 'INGCAJ00001' THEN 'success' ELSE 'danger' END AS CLAICO, CASE WHEN mct.codigo = 'INGCAJ00001' THEN 'arrow-up' ELSE 'arrow-down' END AS ICON";
		$from = "movimientocaja mc INNER JOIN movimientocajatipo mct ON mc.movimientocajatipo = mct.movimientocajatipo_id INNER JOIN usuario u ON mc.usuario_id = u.usuario_id INNER JOIN usuariodetalle ud ON u.usuariodetalle = ud.usuariodetalle_id";
		$where = "mct.codigo IN ('INGCAJ00001', 'EGRCAJ00001')";
		$movimientocaja_collection = CollectorCondition()->get('MovimientoCaja', $where, 4, $from, $select);
		$movimientocajatipo_collection = Collector()->get('MovimientoCajaTipo');
		
		$this->model->movimientocaja_id = $movimientocaja_id;
		$this->model->get();
		$this->view->editar($movimientocaja_collection, $movimientocajatipo_collection, $this->model);
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
		header("Location: " . URL_APP . "/movimientocaja/panel");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$movimientocaja_id = filter_input(INPUT_POST, 'movimientocaja_id');

		$this->model->movimientocaja_id = $movimientocaja_id;
		$this->model->get();
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
		header("Location: " . URL_APP . "/movimientocaja/panel");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$movimientocaja_id = $arg;
		$this->model->movimientocaja_id = $movimientocaja_id;
		$this->model->delete();
		header("Location: " . URL_APP . "/movimientocaja/panel");
	}
	
	function traer_movimientocaja_ajax($arg) {
		SessionHandler()->check_session();
		$movimientocaja_id = $arg;
		$this->model->movimientocaja_id = $movimientocaja_id;
		$this->model->get();
		$usuario_id = $this->model->usuario_id;

		$um = new Usuario();
		$um->usuario_id = $usuario_id;
		$um->get();
		unset($um->configuracionmenu);
		$this->model->usuario = $um;

		$this->view->traer_movimientocaja_ajax($this->model);
	}
}
?>