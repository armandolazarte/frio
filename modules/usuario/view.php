<?php


class UsuarioView extends View {
	
	function login($arg) {
		$template = $this->render_login();
		if ($arg == "mError")  {
			$gui_mError = file_get_contents("static/modules/usuario/mError.html");	
			$template = str_replace("{gui_mError}", $gui_mError, $template);
		} else {
			$template = str_replace("{gui_mError}", "", $template);
		}
		print $template;
	}

	function agregar($usuario_collection, $vendedor_collection, $configuracionmenu_collection, $almacen_collection) {
		$gui = file_get_contents("static/modules/usuario/agregar.html");
		$slt_almacen = file_get_contents("static/common/slt_almacen.html");
		$slt_configuracionmenu = file_get_contents("static/modules/usuario/slt_configuracionmenu.html");
		$slt_vendedor = file_get_contents("static/modules/usuario/slt_vendedor.html");
		
		$almacen_collection = $this->order_collection_objects($almacen_collection, 'denominacion', SORT_ASC);
		$slt_almacen = $this->render_regex('SLT_ALMACEN', $slt_almacen, $almacen_collection);
		$configuracionmenu_collection = $this->order_collection_array($configuracionmenu_collection, 'DENOMINACION', SORT_ASC);
		$slt_configuracionmenu = $this->render_regex_dict('SLT_CONFIGURACIONMENU', $slt_configuracionmenu, $configuracionmenu_collection);

		$vendedor_collection = $this->order_collection_array($vendedor_collection, 'VENDEDOR', SORT_ASC);
		$slt_vendedor = $this->render_regex_dict('SLT_VENDEDOR', $slt_vendedor, $vendedor_collection);

		$render = $this->render_regex_dict('TBL_USUARIO', $gui, $usuario_collection);
		$render = str_replace('{slt_configuracionmenu}', $slt_configuracionmenu, $render);
		$render = str_replace('{slt_almacen}', $slt_almacen, $render);
		$render = str_replace('{slt_vendedor}', $slt_vendedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($usuario_collection, $configuracionmenu_collection, $almacen_collection, $usuario) {
		$gui = file_get_contents("static/modules/usuario/editar.html");
		$slt_almacen = file_get_contents("static/common/slt_almacen.html");
		$slt_configuracionmenu = file_get_contents("static/modules/usuario/slt_configuracionmenu.html");

		$almacen_collection = $this->order_collection_objects($almacen_collection, 'denominacion', SORT_ASC);
		$slt_almacen = $this->render_regex('SLT_ALMACEN', $slt_almacen, $almacen_collection);
		$configuracionmenu_collection = $this->order_collection_array($configuracionmenu_collection, 'DENOMINACION', SORT_ASC);
		$slt_configuracionmenu = $this->render_regex_dict('SLT_CONFIGURACIONMENU', $slt_configuracionmenu, $configuracionmenu_collection);
		
		$usuario_nivel = $usuario->nivel;
		$nivel_denominacion = ($usuario_nivel == 1) ? "Operador" : "";
		$nivel_denominacion = ($usuario_nivel == 2) ? "Analista" : $nivel_denominacion;
		$nivel_denominacion = ($usuario_nivel == 3) ? "Administrador" : $nivel_denominacion;
		$nivel_denominacion = ($usuario_nivel == 9) ? "Desarrollador" : $nivel_denominacion;
		$usuario->nivel_denominacion = $nivel_denominacion;
		unset($usuario->configuracionmenu->submenu_collection, $usuario->configuracionmenu->item_collection);
		$usuario = $this->set_dict($usuario);
		
		$render = $this->render_regex_dict('TBL_USUARIO', $gui, $usuario_collection);
		$render = str_replace('{slt_configuracionmenu}', $slt_configuracionmenu, $render);
		$render = str_replace('{slt_almacen}', $slt_almacen, $render);
		$render = $this->render($usuario, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function perfil() {
		$gui = file_get_contents("static/modules/usuario/perfil.html");
		$dict_perfil = array(
			"{usuario-usuario_id}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"],
			"{usuario-denominacion}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"],
			"{usuario-nombre}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-nombre"],
			"{usuario-apellido}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-apellido"],
			"{usuario-nivel}"=>$_SESSION["data-login-" . APP_ABREV]["nivel-denominacion"],
			"{usuario-rol}"=>$_SESSION["data-login-" . APP_ABREV]["configuracionmenu-denominacion"],
			"{usuariodetalle-correoelectronico}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-correoelectronico"]);
		$render = $this->render($dict_perfil, $gui);
		$template = $this->render_template($render);
		print $template;
	}

	function administrador() {
		$gui = file_get_contents("static/modules/usuario/administrador.html");
		$template = $this->render_template($gui);
		print $template;
	}
}
?>