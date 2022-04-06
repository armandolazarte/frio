<?php


class ClienteView extends View {
	function panel() {
		$gui = file_get_contents("static/modules/cliente/panel.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function listar($cliente_collection) {
		$gui = file_get_contents("static/modules/cliente/listar.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$tbl_cliente_array = file_get_contents("static/modules/cliente/tbl_cliente_array_operador.html");
				break;
			case 2:
				$tbl_cliente_array = file_get_contents("static/modules/cliente/tbl_cliente_array_supervisor.html");
				break;
			default:
				$tbl_cliente_array = file_get_contents("static/modules/cliente/tbl_cliente_array.html");
				break;
		}

		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function ocultos($cliente_collection) {
		$gui = file_get_contents("static/modules/cliente/listar.html");
		$tbl_cliente_array = file_get_contents("static/modules/cliente/tbl_cliente_oculto_array.html");

		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $frecuenciaventa_collection, $vendedor_collection, $flete_collection, $tipofactura_collection, $listaprecio_collection,$categoriacliente_collection) {
		$gui = file_get_contents("static/modules/cliente/agregar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$gui_slt_frecuenciaventa = file_get_contents("static/common/slt_frecuenciaventa.html");
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor.html");
		$gui_slt_flete = file_get_contents("static/common/slt_flete.html");
		$gui_slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$gui_slt_listaprecio = file_get_contents("static/common/slt_listaprecio.html");
		$gui_slt_categoriacliente = file_get_contents("static/common/slt_categoriacliente.html");
		foreach ($vendedor_collection as $vendedor) unset($vendedor->infocontacto_collection);
		foreach ($flete_collection as $flete) unset($flete->infocontacto_collection);

		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$gui_slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $gui_slt_condicioniva, $condicioniva_collection);
		$gui_slt_frecuenciaventa = $this->render_regex('SLT_FRECUENCIAVENTA', $gui_slt_frecuenciaventa, $frecuenciaventa_collection);
		$gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$gui_slt_flete = $this->render_regex('SLT_FLETE', $gui_slt_flete, $flete_collection);
		$gui_slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $gui_slt_tipofactura, $tipofactura_collection);
		$gui_slt_listaprecio = $this->render_regex('SLT_LISTAPRECIO', $gui_slt_listaprecio, $listaprecio_collection);
		$gui_slt_categoriacliente = $this->render_regex('SLT_CATEGORIACLIENTE', $gui_slt_categoriacliente, $categoriacliente_collection);

		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = str_replace('{slt_condicioniva}', $gui_slt_condicioniva, $render);
		$render = str_replace('{slt_frecuenciaventa}', $gui_slt_frecuenciaventa, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{slt_flete}', $gui_slt_flete, $render);
		$render = str_replace('{slt_tipofactura}', $gui_slt_tipofactura, $render);
		$render = str_replace('{slt_listaprecio}', $gui_slt_listaprecio, $render);
		$render = str_replace('{slt_categoriacliente}', $gui_slt_categoriacliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $frecuenciaventa_collection, $vendedor_collection, $flete_collection,$tipofactura_collection, $obj_cliente,$listaprecio_collection,$categoriacliente_collection) {
		$gui = file_get_contents("static/modules/cliente/editar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$gui_slt_frecuenciaventa = file_get_contents("static/common/slt_frecuenciaventa.html");
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor.html");
		$gui_slt_flete = file_get_contents("static/common/slt_flete.html");
		$gui_slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$gui_lst_input_infocontacto = file_get_contents("static/modules/cliente/lst_input_infocontacto.html");
		$gui_slt_categoriacliente = file_get_contents("static/common/slt_categoriacliente.html");
		$gui_slt_listaprecio = file_get_contents("static/common/slt_listaprecio.html");

		foreach ($vendedor_collection as $vendedor) unset($vendedor->infocontacto_collection, $vendedor->frecuenciaventa);
		foreach ($flete_collection as $flete) unset($flete->infocontacto_collection);

		$infocontacto_collection = $obj_cliente->infocontacto_collection;
		$gui_lst_input_infocontacto = $this->render_regex('LST_INPUT_INFOCONTACTO', $gui_lst_input_infocontacto, $infocontacto_collection);
		unset($obj_cliente->infocontacto_collection, $obj_cliente->vendedor->infocontacto_collection,
			  $obj_cliente->vendedor->frecuenciaventa);

		$obj_cliente->txt_impacto_ganancia = ($obj_cliente->impacto_ganancia == 1) ? 'SI' : 'NO';
		$obj_cliente->checked_habilita_cuenta_corriente_si = ($obj_cliente->habilita_cuenta_corriente == 1) ? 'checked' : '';
		$obj_cliente->checked_habilita_cuenta_corriente_no = ($obj_cliente->habilita_cuenta_corriente == 1) ? '' : 'checked';
		$obj_cliente->checked_ingresosbrutos_si = ($obj_cliente->ingresosbrutos == 1) ? 'checked' : '';
		$obj_cliente->checked_ingresosbrutos_no = ($obj_cliente->ingresosbrutos == 1) ? '' : 'checked';
		$obj_cliente->documentotipo_id = $obj_cliente->documentotipo->documentotipo_id;
		$obj_cliente->documento_denominacion = $obj_cliente->documentotipo->denominacion;
		$obj_cliente = $this->set_dict($obj_cliente);
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$gui_slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $gui_slt_condicioniva, $condicioniva_collection);
		$gui_slt_frecuenciaventa = $this->render_regex('SLT_FRECUENCIAVENTA', $gui_slt_frecuenciaventa, $frecuenciaventa_collection);
		$gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$gui_slt_flete = $this->render_regex('SLT_FLETE', $gui_slt_flete, $flete_collection);
		$gui_slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $gui_slt_tipofactura, $tipofactura_collection);
		$gui_slt_categoriacliente = $this->render_regex('SLT_CATEGORIACLIENTE', $gui_slt_categoriacliente, $categoriacliente_collection);
		$gui_slt_listaprecio = $this->render_regex('SLT_LISTAPRECIO', $gui_slt_listaprecio, $listaprecio_collection);

		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = str_replace('{slt_condicioniva}', $gui_slt_condicioniva, $render);
		$render = str_replace('{slt_frecuenciaventa}', $gui_slt_frecuenciaventa, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{slt_flete}', $gui_slt_flete, $render);
		$render = str_replace('{slt_tipofactura}', $gui_slt_tipofactura, $render);
		$render = str_replace('{lst_input_infocontacto}', $gui_lst_input_infocontacto, $render);
		$render = str_replace('{slt_categoriacliente}', $gui_slt_categoriacliente, $render);
		$render = str_replace('{slt_listaprecio}', $gui_slt_listaprecio, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($obj_cliente) {
		$gui = file_get_contents("static/modules/cliente/consultar.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		if ($obj_cliente->documentotipo->denominacion == 'CUIL' OR $obj_cliente->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_cliente->documento, 0, 2);
			$cuil2 = substr($obj_cliente->documento, 2, 8);
			$cuil3 = substr($obj_cliente->documento, 10);
			$obj_cliente->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_cliente->infocontacto_collection;
		unset($obj_cliente->infocontacto_collection, $obj_cliente->vendedor->infocontacto_collection,
			  $obj_cliente->vendedor->frecuenciaventa, $obj_cliente->flete->infocontacto_collection);
		$obj_cliente = $this->set_dict($obj_cliente);

		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>