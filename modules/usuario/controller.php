<?php
require_once "modules/usuario/model.php";
require_once "modules/usuario/view.php";
require_once "modules/usuariodetalle/controller.php";
require_once "modules/usuariovendedor/model.php";
require_once "modules/almacen/model.php";
require_once "modules/configuracionmenu/model.php";


class UsuarioController {

	function __construct() {
		$this->model = new Usuario();
		$this->view = new UsuarioView();
	}

	function login($arg=0) {
		$this->view->login($arg);
	}

	function checkin() {
        SessionHandler()->checkin();
    }

	function checkout() {
        SessionHandler()->checkout();
    }

    function agregar() {
    	SessionHandler()->check_session();
		$usuario = $_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"];
		$select_usuario = "u.usuario_id AS USUARIO_ID, u.denominacion AS DENOMINACION, CONCAT(ud.apellido, ', ', ud.nombre) AS USUARIO";
		$from_usuario = "usuario u INNER JOIN usuariodetalle ud ON u.usuariodetalle = ud.usuariodetalle_id";

		$select_menu = "cm.denominacion AS DENOMINACION, cm.configuracionmenu_id AS CONFIGURACIONMENU_ID";
		$from_menu = "configuracionmenu cm";

		if ($usuario == "desarrollador") {
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', NULL, 4, $from_menu, $select_menu);
			$usuario_collection = CollectorCondition()->get('Usuario', NULL, 4, $from_usuario, $select_usuario);
		} else {
			$where_menu = "cm.denominacion != 'DESARROLLADOR'";
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', $where_menu, 4, $from_menu, $select_menu);
			$where_usuario = "u.denominacion != 'desarrollador'";
			$usuario_collection = CollectorCondition()->get('Usuario', $where_usuario, 4, $from_usuario, $select_usuario);
		}

		$select = "CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, v.vendedor_id AS VID";
		$from = "vendedor v";
		$vendedor_collection = CollectorCondition()->get('Vendedor', NULL, 4, $from, $select);
		$almacen_collection = Collector()->get('Almacen');
		foreach ($almacen_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($almacen_collection[$clave]);
		}

		$this->view->agregar($usuario_collection, $vendedor_collection, $configuracionmenu_collection, $almacen_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		$detalle = new UsuarioDetalleController();
        $detalle->guardar();
        $this->model->denominacion = filter_input(INPUT_POST, "denominacion");
        $this->model->nivel = filter_input(INPUT_POST, "nivel");
        $this->model->configuracionmenu = filter_input(INPUT_POST, "configuracionmenu");
        $this->model->almacen = filter_input(INPUT_POST, "almacen");
        $this->model->usuariodetalle = $detalle->model->usuariodetalle_id;        
        $this->model->save();
		header("Location: " . URL_APP . "/usuario/agregar");
	}

	function guardar_vendedor() {
		SessionHandler()->check_session();
		$detalle = new UsuarioDetalleController();
        $detalle->guardar();
        $this->model->denominacion = filter_input(INPUT_POST, "denominacion");
        $this->model->nivel = filter_input(INPUT_POST, "nivel");
        $this->model->configuracionmenu = filter_input(INPUT_POST, "configuracionmenu");
        $this->model->almacen = filter_input(INPUT_POST, "almacen");
        $this->model->usuariodetalle = $detalle->model->usuariodetalle_id;        
        $this->model->save();
        $usuario_id = $this->model->usuario_id;

        $uvm = new UsuarioVendedor();
        $uvm->usuario_id = $usuario_id;
        $uvm->vendedor_id = filter_input(INPUT_POST, 'vendedor');
        $uvm->save();

		header("Location: " . URL_APP . "/usuario/agregar");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		
		$this->model->usuario_id = $arg;
		$this->model->get();
		
		$usuario = $_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"];
		$select_usuario = "u.usuario_id AS USUARIO_ID, u.denominacion AS DENOMINACION, CONCAT(ud.apellido, ', ', ud.nombre) AS USUARIO";
		$from_usuario = "usuario u INNER JOIN usuariodetalle ud ON u.usuariodetalle = ud.usuariodetalle_id";
		$select_menu = "cm.denominacion AS DENOMINACION, cm.configuracionmenu_id AS CONFIGURACIONMENU_ID";
		$from_menu = "configuracionmenu cm";

		if ($usuario == "desarrollador") {
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', NULL, 4, $from_menu, $select_menu);
			$usuario_collection = CollectorCondition()->get('Usuario', NULL, 4, $from_usuario, $select_usuario);
		} else {
			$where_menu = "cm.denominacion != 'DESARROLLADOR'";
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', $where_menu, 4, $from_menu, $select_menu);
			$where_usuario = "u.denominacion != 'desarrollador'";
			$usuario_collection = CollectorCondition()->get('Usuario', $where_usuario, 4, $from_usuario, $select_usuario);
		}

		$almacen_collection = Collector()->get('Almacen');
		foreach ($almacen_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($almacen_collection[$clave]);
		}

		$this->view->editar($usuario_collection, $configuracionmenu_collection, $almacen_collection, $this->model);
	}

	function actualizar() {
		SessionHandler()->check_session();
		
		$detalle = new UsuarioDetalleController();
        $detalle->actualizar();

        $this->model->usuario_id = filter_input(INPUT_POST, "usuario_id");
        $this->model->get();
        $this->model->nivel = filter_input(INPUT_POST, "nivel");
        $this->model->almacen = filter_input(INPUT_POST, "almacen");
        $this->model->configuracionmenu = filter_input(INPUT_POST, "configuracionmenu");
		$this->model->save();
		header("Location: " . URL_APP . "/usuario/agregar");
	}

	function actualizar_token() {
		SessionHandler()->check_session();
		
		$this->model->usuario_id = $_POST["usuario_id"];
		$this->model->get();
		$usuariodetalle_id = $this->model->usuariodetalle->usuariodetalle_id;
		$udc = new UsuarioDetalleController();
        $udc->actualizar_token($usuariodetalle_id);
		header("Location: " . URL_APP . "/usuario/perfil");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();

		$this->model->usuario_id = $arg;
		$this->model->get();
		$usuariodetalle_id = $this->model->usuariodetalle->usuariodetalle_id;
		$udc = new UsuarioDetalleController();
		$udc->eliminar($usuariodetalle_id);
		$this->model->delete();
		header("Location: " . URL_APP . "/usuario/agregar");
	}
	
	function regenerar_token($arg) {
		SessionHandler()->check_session();
		
		$this->model->usuario_id = $arg;
		$this->model->get();
		$detalle = new UsuarioDetalleController();
        $detalle->regenerar_token($this->model->usuariodetalle->usuariodetalle_id, $this->model->denominacion);
		header("location:" . URL_APP . "/usuario/agregar");
	}

	function perfil() {
		SessionHandler()->check_session();
		$this->view->perfil();
	}

	function panel() {
		SessionHandler()->check_session();
		$this->administrador();
	}

	function administrador() {
		SessionHandler()->check_session();
		$perfil_id = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		$this->view->administrador();
	}

	function analista() {
		$this->administrador();
	}

	function informar_clave() {
		SessionHandler()->check_session();
		$usuario_collection = Collector()->get("Usuario");
		$usuario_temp = array();
		foreach ($usuario_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array("{usuario-nombre}"=>$valor->usuariodetalle->nombre,
								"{usuario-usuario}"=>$valor->denominacion,
								"{usuario-contraseña}"=>$valor->denominacion . "$1",
								"{usuario_correo}"=>$valor->usuariodetalle->correoelectronico);
			$usuario_temp[] = $array_temp;
			
		}

		$emailHelper = new EmailUsuario();
		$emailHelper->envia_email_usuario($usuario_temp);		
	}
}
?>