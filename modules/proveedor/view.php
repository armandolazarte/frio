<?php


class ProveedorView extends View {
	function panel() {
		$gui = file_get_contents("static/modules/proveedor/panel.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function listar($proveedor_collection) {
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$gui = file_get_contents("static/modules/proveedor/listar_supervisor.html");
				$tbl_proveedor_array = file_get_contents("static/modules/proveedor/tbl_proveedor_array_supervisor.html");
				break;
			default:
				$gui = file_get_contents("static/modules/proveedor/listar.html");
				$tbl_proveedor_array = file_get_contents("static/modules/proveedor/tbl_proveedor_array.html");
				break;
		}


		$tbl_proveedor_array = $this->render_regex_dict('TBL_PROVEEDOR', $tbl_proveedor_array, $proveedor_collection);
		$render = str_replace('{tbl_proveedor}', $tbl_proveedor_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function ocultos($proveedor_collection) {
		$gui = file_get_contents("static/modules/proveedor/listar.html");
		$tbl_proveedor_array = file_get_contents("static/modules/proveedor/tbl_proveedor_oculto_array.html");

		$tbl_proveedor_array = $this->render_regex_dict('TBL_PROVEEDOR', $tbl_proveedor_array, $proveedor_collection);
		$render = str_replace('{tbl_proveedor}', $tbl_proveedor_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($provincia_collection, $documentotipo_collection, $condicioniva_collection) {
		$gui = file_get_contents("static/modules/proveedor/agregar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");

		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$gui_slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $gui_slt_condicioniva, $condicioniva_collection);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = str_replace('{slt_condicioniva}', $gui_slt_condicioniva, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/proveedor/editar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$gui_lst_input_infocontacto = file_get_contents("static/modules/proveedor/lst_input_infocontacto.html");

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		$gui_lst_input_infocontacto = $this->render_regex('LST_INPUT_INFOCONTACTO', $gui_lst_input_infocontacto, $infocontacto_collection);
		unset($obj_proveedor->infocontacto_collection);

		$obj_proveedor = $this->set_dict($obj_proveedor);
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$gui_slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $gui_slt_condicioniva, $condicioniva_collection);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = str_replace('{slt_condicioniva}', $gui_slt_condicioniva, $render);
		$render = str_replace('{lst_input_infocontacto}', $gui_lst_input_infocontacto, $render);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($productodetalle_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/proveedor/consultar.html");
		$gui_tbl_productodetalle = file_get_contents("static/modules/proveedor/tbl_productodetalle_array.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		if ($obj_proveedor->documentotipo->denominacion == 'CUIL' OR $obj_proveedor->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_proveedor->documento, 0, 2);
			$cuil2 = substr($obj_proveedor->documento, 2, 8);
			$cuil3 = substr($obj_proveedor->documento, 10);
			$obj_proveedor->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		unset($obj_proveedor->infocontacto_collection);
		$obj_proveedor = $this->set_dict($obj_proveedor);

		$gui_tbl_productodetalle = $this->render_regex_dict('TBL_PRODUCTODETALLE', $gui_tbl_productodetalle, $productodetalle_collection);
		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{tbl_productodetalle}', $gui_tbl_productodetalle, $render);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function modificar_lista_precio($productodetalle_collection, $msj_array, $proveedor_id) {
		$gui = file_get_contents("static/modules/proveedor/modificar_lista_precio.html");
		$gui_tbl_productodetalle = file_get_contents("static/modules/proveedor/tbl_chk_productodetalle_array.html");

		$gui_tbl_productodetalle = $this->render_regex_dict('TBL_PRODUCTODETALLE', $gui_tbl_productodetalle, $productodetalle_collection);
		$render = str_replace('{tbl_productodetalle}', $gui_tbl_productodetalle, $gui);
		$render = str_replace('<!--TBL_PRODUCTODETALLE-->', '', $render);
		$render = str_replace('{fecha}', date('Y-m-d'), $render);
		$render = str_replace('{proveedor-proveedor_id}', $proveedor_id, $render);
		$render = $this->render($msj_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function creditos($notacreditoproveedor_collection) {
			$gui = file_get_contents("static/modules/proveedor/creditos.html");
			$gui_tbl_notacredito_proveedor = file_get_contents("static/modules/proveedor/tbl_credito_proveedor.html");
			$gui_tbl_notacredito_proveedor = $this->render_regex_dict('TBL_CREDITOS', $gui_tbl_notacredito_proveedor, $notacreditoproveedor_collection);
			$render = str_replace('{tbl_creditos}', $gui_tbl_notacredito_proveedor, $gui);
	 		$render = $this->render_breadcrumb($render);
			$template = $this->render_template($render);
			print $template;
		}

	function listar_todos($notacreditoproveedor_collection) {
				$gui = file_get_contents("static/modules/proveedor/listar_todos.html");
				$gui_tbl_notacredito_proveedor = file_get_contents("static/modules/proveedor/tbl_credito_proveedor.html");
				$gui_tbl_notacredito_proveedor = $this->render_regex_dict('TBL_CREDITOS', $gui_tbl_notacredito_proveedor, $notacreditoproveedor_collection);
				$render = str_replace('{tbl_creditos}', $gui_tbl_notacredito_proveedor, $gui);
		 		$render = $this->render_breadcrumb($render);
				$template = $this->render_template($render);
				print $template;
	}
}
?>
