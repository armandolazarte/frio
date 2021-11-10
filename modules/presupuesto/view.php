<?php


class PresupuestoView extends View {
	
	function listar($presupuesto_collection, $array_msj) {
		$gui = file_get_contents("static/modules/presupuesto/listar.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$tbl_presupuesto_array = file_get_contents("static/modules/presupuesto/tbl_presupuesto_array_operador.html");
				break;
			case 2:
				$tbl_presupuesto_array = file_get_contents("static/modules/presupuesto/tbl_presupuesto_array_supervisor.html");
				break;
			default:
				$tbl_presupuesto_array = file_get_contents("static/modules/presupuesto/tbl_presupuesto_array.html");
				break;
		}

		$tbl_presupuesto_array = $this->render_regex_dict('TBL_PRESUPUESTO', $tbl_presupuesto_array, $presupuesto_collection);
		$render = str_replace('{tbl_presupuesto}', $tbl_presupuesto_array, $gui);
		$render = $this->render_breadcrumb($render);
		$render = $this->render($array_msj, $render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function presupuestar($producto_collection, $cliente_collection, $vendedor_collection, $array_presupuesto) {
		$gui = file_get_contents("static/modules/presupuesto/presupuestar.html");
		$tbl_producto_array = file_get_contents("static/modules/presupuesto/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/presupuesto/tbl_cliente_array.html");
		$tbl_vendedor_array = file_get_contents("static/modules/presupuesto/tbl_vendedor_array.html");
		
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_vendedor_array = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor_array, $vendedor_collection);

		$render = str_replace('{hora}', date('H:i:s'), $gui);
		$render = str_replace('{fecha}', date('Y-m-d'), $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor_array, $render);
		$render = $this->render($array_presupuesto, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($producto_collection, $cliente_collection, $vendedor_collection, $presupuestodetalle_collection, $obj_presupuesto) {
		$gui = file_get_contents("static/modules/presupuesto/editar.html");
		$tbl_producto_array = file_get_contents("static/modules/presupuesto/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/presupuesto/tbl_cliente_array.html");
		$tbl_vendedor_array = file_get_contents("static/modules/presupuesto/tbl_vendedor_array.html");
		$tbl_editar_presupuestodetalle_array = file_get_contents("static/modules/presupuesto/tbl_editar_presupuestodetalle_array.html");
		$hidden_editar_presupuestodetalle_array = file_get_contents("static/modules/presupuesto/hidden_editar_presupuestodetalle_array.html");

		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_producto_array = str_replace('<!--TBL_PRODUCTO-->', '', $tbl_producto_array);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_cliente_array = str_replace('<!--TBL_CLIENTE-->', '', $tbl_cliente_array);
		$tbl_vendedor_array = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor_array, $vendedor_collection);
		$tbl_vendedor_array = str_replace('<!--TBL_VENDEDOR-->', '', $tbl_vendedor_array);
		
		if (!empty($presupuestodetalle_collection) OR is_array($presupuestodetalle_collection)) {
			$array_producto_ids = array();
			foreach ($presupuestodetalle_collection as $clave=>$valor) $array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_presupuesto->array_producto_ids = $array_producto_ids;

			$tbl_editar_presupuestodetalle_array = $this->render_regex_dict('TBL_PRESUPUESTODETALLE', $tbl_editar_presupuestodetalle_array, 
																		$presupuestodetalle_collection);
			$tbl_editar_presupuestodetalle_array = str_replace('<!--TBL_PRESUPUESTODETALLE-->', '', $tbl_editar_presupuestodetalle_array);
			$hidden_editar_presupuestodetalle_array = $this->render_regex_dict('HDN_PRESUPUESTODETALLE', $hidden_editar_presupuestodetalle_array, 
																		   $presupuestodetalle_collection);
			$hidden_editar_presupuestodetalle_array = str_replace('<!--HDN_PRESUPUESTODETALLE-->', '', $hidden_editar_presupuestodetalle_array);
			$costo_base = 0;
			foreach ($presupuestodetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_presupuesto->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_presupuestodetalle_array = ''; 
			$hidden_editar_presupuestodetalle_array = '';
		}

		unset($obj_presupuesto->cliente->infocontacto_collection, $obj_presupuesto->vendedor->infocontacto_collection, 
			  $obj_presupuesto->cliente->vendedor->infocontacto_collection);
		$txt_cliente = $obj_presupuesto->cliente->documentotipo->denominacion . ' ' . $obj_presupuesto->cliente->documento;
		$txt_cliente .= ' - ' . $obj_presupuesto->cliente->razon_social;
		$obj_presupuesto->cliente->descripcion = $txt_cliente;
		$txt_vendedor = $obj_presupuesto->vendedor->documentotipo->denominacion . ' ' . $obj_presupuesto->vendedor->documento;
		$txt_vendedor .= ' - ' . $obj_presupuesto->vendedor->apellido . ' ' . $obj_presupuesto->vendedor->nombre;
		$obj_presupuesto->vendedor->descripcion = $txt_vendedor;
		$obj_presupuesto->vendedor_id = $obj_presupuesto->vendedor->vendedor_id;
		$obj_presupuesto->punto_venta = str_pad($obj_presupuesto->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_presupuesto->numero_factura = str_pad($obj_presupuesto->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_presupuesto = $this->set_dict($obj_presupuesto);
		
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $gui);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor_array, $render);
		$render = str_replace('{presupuesto-costobase}', $costo_base, $render);
		$render = str_replace('{tbl_editar_presupuestodetalle_array}', $tbl_editar_presupuestodetalle_array, $render);
		$render = str_replace('{hidden_editar_presupuestodetalle_array}', $hidden_editar_presupuestodetalle_array, $render);
		$render = $this->render($obj_presupuesto, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($presupuestodetalle_collection, $obj_presupuesto) {
		$gui = file_get_contents("static/modules/presupuesto/consultar.html");
		$tbl_presupuestodetalle_array = file_get_contents("static/modules/presupuesto/tbl_presupuestodetalle_array.html");
		$tbl_presupuestodetalle_array = $this->render_regex_dict('TBL_PRESUPUESTODETALLE', $tbl_presupuestodetalle_array, $presupuestodetalle_collection);
		
		unset($obj_presupuesto->cliente->infocontacto_collection, $obj_presupuesto->vendedor->infocontacto_collection, $obj_presupuesto->cliente->flete->infocontacto_collection,
			  $obj_presupuesto->cliente->vendedor->infocontacto_collection);
		$obj_presupuesto->punto_venta = str_pad($obj_presupuesto->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_presupuesto->numero_factura = str_pad($obj_presupuesto->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_presupuesto = $this->set_dict($obj_presupuesto);
	
		$render = str_replace('{tbl_presupuestodetalle}', $tbl_presupuestodetalle_array, $gui);
		$render = $this->render($obj_presupuesto, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar($presupuesto_collection) {
		$gui = file_get_contents("static/modules/presupuesto/buscar.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$tbl_presupuesto_array = file_get_contents("static/modules/presupuesto/tbl_presupuesto_array_operador.html");
				break;
			case 2:
				$tbl_presupuesto_array = file_get_contents("static/modules/presupuesto/tbl_presupuesto_array_supervisor.html");
				break;
			default:
				$tbl_presupuesto_array = file_get_contents("static/modules/presupuesto/tbl_presupuesto_array.html");
				break;
		}
		
		$tbl_presupuesto_array = $this->render_regex_dict('TBL_PRESUPUESTO', $tbl_presupuesto_array, $presupuesto_collection);
		$render = str_replace('{tbl_presupuesto}', $tbl_presupuesto_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar_producto_ajax($producto_collection) {
		$gui_tbl_producto = file_get_contents("static/modules/presupuesto/tbl_buscar_producto_ajax.html");
		$gui_tbl_producto = $this->render_regex_dict('TBL_PRODUCTO', $gui_tbl_producto, $producto_collection);
		$gui_tbl_producto = str_replace('<!--TBL_PRODUCTO-->', '', $gui_tbl_producto);
		print $gui_tbl_producto;
	}

	function traer_formulario_producto_ajax($obj_producto, $cantidad_disponible) {
		$gui = file_get_contents("static/modules/presupuesto/formulario_producto.html");		
		$costo_flete = $obj_producto->costo + (($obj_producto->costo * $obj_producto->flete) / 100);
		$costo_iva = (($costo_flete * $obj_producto->iva) / 100) + $costo_flete;
		$valor_ganancia = $costo_iva * $obj_producto->porcentaje_ganancia / 100;
		$valor_venta = $costo_iva + $valor_ganancia;
		
		$obj_producto->costo = round($obj_producto->costo, 2);
		$obj_producto->valor_venta = round($costo_iva, 2);
		$obj_producto->valor_ganancia = round($valor_ganancia, 2);
		$obj_producto->valor_venta = round($valor_venta, 2);
		$obj_producto->descripcion = $obj_producto->productomarca->denominacion . ' ' . $obj_producto->denominacion . ' ';
		$obj_producto = $this->set_dict($obj_producto);
		
		$gui = $this->render($obj_producto, $gui);
		$gui = str_replace('{cantidad_disponible}', $cantidad_disponible, $gui);
		print $gui;
	}

	function modal_mensaje_formulario_ajax($cliente_id) {
		$gui = file_get_contents("static/modules/presupuesto/modal_mensaje_formulario_ajax.html");
		$gui = str_replace('{cliente-cliente_id}', $cliente_id, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}
}
?>