<?php


class IngresoView extends View {

	function listar($ingreso_collection, $array_msj) {
		$gui = file_get_contents("static/modules/ingreso/listar.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$tbl_ingreso_array = file_get_contents("static/modules/ingreso/tbl_ingreso_array_supervisor.html");
				break;
			default:
				$tbl_ingreso_array = file_get_contents("static/modules/ingreso/tbl_ingreso_array.html");
				break;
		}

		$tbl_ingreso_array = $this->render_regex_dict('TBL_INGRESO', $tbl_ingreso_array, $ingreso_collection);		
		$render = str_replace('{tbl_ingreso}', $tbl_ingreso_array, $gui);
		$render = $this->render_breadcrumb($render);
		$render = $this->render($array_msj, $render);
		$template = $this->render_template($render);
		print $template;
	}

	function ingresar($producto_collection, $proveedor_collection, $condicionpago_collection, 
					  $condicioniva_collection, $tipofactura_collection) {
		$gui = file_get_contents("static/modules/ingreso/ingresar.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$tbl_producto_array = file_get_contents("static/modules/ingreso/tbl_producto_array.html");
		$tbl_proveedor_array = file_get_contents("static/modules/ingreso/tbl_proveedor_array.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_proveedor_array = $this->render_regex_dict('TBL_PROVEEDOR', $tbl_proveedor_array, $proveedor_collection);
		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);

		$fecha_sys = strtotime(date('Y-m-d'));
		$vencimiento_default = date("Y-m-d", strtotime("14 days", $fecha_sys));

		$render = str_replace('{hora}', date('H:i:s'), $gui);
		$render = str_replace('{fecha}', date('Y-m-d'), $render);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $render);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_proveedor}', $tbl_proveedor_array, $render);
		$render = str_replace('{fecha_vencimiento}', $vencimiento_default, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($producto_collection, $proveedor_collection, $condicionpago_collection, 
					$condicioniva_collection, $ingresodetalle_collection, $tipofactura_collection, $obj_ingreso) {
		$gui = file_get_contents("static/modules/ingreso/editar.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$tbl_producto_array = file_get_contents("static/modules/ingreso/tbl_producto_array.html");
		$tbl_proveedor_array = file_get_contents("static/modules/ingreso/tbl_proveedor_array.html");
		$tbl_editar_ingresodetalle_array = file_get_contents("static/modules/ingreso/tbl_editar_ingresodetalle_array.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$hidden_editar_ingresodetalle_array = file_get_contents("static/modules/ingreso/hidden_editar_ingresodetalle_array.html");

		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		$slt_tipofactura = str_replace('<!--SLT_TIPOFACTURA-->', '', $slt_tipofactura);
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicionpago = str_replace('<!--SLT_CONDICIONPAGO-->', '', $slt_condicionpago);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);
		$slt_condicioniva = str_replace('<!--SLT_CONDICIONIVA-->', '', $slt_condicioniva);
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_producto_array = str_replace('<!--TBL_PRODUCTO-->', '', $tbl_producto_array);
		$tbl_proveedor_array = $this->render_regex_dict('TBL_PROVEEDOR', $tbl_proveedor_array, $proveedor_collection);
		$tbl_proveedor_array = str_replace('<!--TBL_PROVEEDOR-->', '', $tbl_proveedor_array);
		
		if (!empty($ingresodetalle_collection)) {
			$array_producto_ids = array();
			foreach ($ingresodetalle_collection as $clave=>$valor) $array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_ingreso->array_producto_ids = $array_producto_ids;
			$tbl_editar_ingresodetalle_array = $this->render_regex_dict('TBL_INGRESODETALLE', $tbl_editar_ingresodetalle_array, 
																		$ingresodetalle_collection);
			$tbl_editar_ingresodetalle_array = str_replace('<!--TBL_INGRESODETALLE-->', '', $tbl_editar_ingresodetalle_array);
			$hidden_editar_ingresodetalle_array = $this->render_regex_dict('HDN_INGRESODETALLE', $hidden_editar_ingresodetalle_array, 
																		   $ingresodetalle_collection);
			$hidden_editar_ingresodetalle_array = str_replace('<!--HDN_INGRESODETALLE-->', '', $hidden_editar_ingresodetalle_array);
			$costo_base = 0;
			foreach ($ingresodetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_ingreso->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_ingresodetalle_array = ''; 
			$hidden_editar_ingresodetalle_array = '';
		}

		unset($obj_ingreso->proveedor->infocontacto_collection);
		$txt_proveedor = $obj_ingreso->proveedor->documentotipo->denominacion . ' ' . $obj_ingreso->proveedor->documento;
		$txt_proveedor .= ' - ' . $obj_ingreso->proveedor->razon_social;

		$opcion_precio = $obj_ingreso->actualiza_precio_producto;
		$opcion_stock = $obj_ingreso->actualiza_stock;
		$obj_ingreso->checked_actualiza_precio_producto_si = ($opcion_precio == 1) ? 'checked' : '';
		$obj_ingreso->checked_actualiza_precio_producto_no = ($opcion_precio == 0) ? 'checked' : '';
		$obj_ingreso->proveedor->descripcion = $txt_proveedor;
		
		$obj_ingreso = $this->set_dict($obj_ingreso);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $gui);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_proveedor}', $tbl_proveedor_array, $render);
		$render = str_replace('{tbl_editar_ingresodetalle_array}', $tbl_editar_ingresodetalle_array, $render);
		$render = str_replace('{hidden_editar_ingresodetalle_array}', $hidden_editar_ingresodetalle_array, $render);
		$render = $this->render($obj_ingreso, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($ingresodetalle_collection, $cuentacorrienteproveedor_collection, $obj_notacreditoproveedor,
					   $notacreditoproveedordetalle_collection, $obj_ingreso, $notacredito_id) {
		$gui = file_get_contents("static/modules/ingreso/consultar.html");
		$tbl_ingresodetalle_array = file_get_contents("static/modules/ingreso/tbl_ingresodetalle_array.html");
		$tbl_ingresodetalle_array = $this->render_regex_dict('TBL_INGRESODETALLE', $tbl_ingresodetalle_array, $ingresodetalle_collection);

		$tbl_notacreditoproveedordetalle_array = file_get_contents("static/modules/ingreso/tbl_ingresodetalle_array.html");
		$tbl_notacreditoproveedordetalle_array = $this->render_regex_dict('TBL_INGRESODETALLE', $tbl_notacreditoproveedordetalle_array, $notacreditoproveedordetalle_collection);
		
		unset($obj_ingreso->proveedor->infocontacto_collection);
		$obj_ingreso->punto_venta = str_pad($obj_ingreso->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_ingreso->numero_factura = str_pad($obj_ingreso->numero_factura, 8, '0', STR_PAD_LEFT);

		if (!empty($cuentacorrienteproveedor_collection)) {
			$obj_ingreso->btn_generar_nc = 'none';
			$obj_ingreso->btn_consultar_nc = ($notacredito_id == 0) ? 'none' : 'block';
		} else {
			$obj_ingreso->btn_generar_nc = ($notacredito_id == 0) ? 'block' : 'none';
			$obj_ingreso->btn_consultar_nc = ($notacredito_id == 0) ? 'none' : 'block';
		}

		$obj_ingreso->volver_ingresodetalle = 'none';
		$obj_ingreso->notacredito = 'none';
		$obj_ingreso = $this->set_dict($obj_ingreso);
		$render = str_replace('{tbl_ingresodetalle}', $tbl_ingresodetalle_array, $gui);
		$render = str_replace('{tbl_notacreditoproveedordetalle}', $tbl_notacreditoproveedordetalle_array, $render);
		$render = $this->render($obj_ingreso, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function reingreso($ingresodetalle_collection, $obj_ingreso) {
		$gui = file_get_contents("static/modules/ingreso/reingreso.html");
		$tbl_producto_array = file_get_contents("static/modules/ingreso/tbl_producto_array.html");
		$tbl_editar_ingresodetalle_array = file_get_contents("static/modules/ingreso/tbl_reingreso_ingresodetalle_array.html");
		$hidden_editar_ingresodetalle_array = file_get_contents("static/modules/ingreso/hidden_editar_ingresodetalle_array.html");

		if (!empty($ingresodetalle_collection) OR is_array($ingresodetalle_collection)) {
			$array_producto_ids = array();
			foreach ($ingresodetalle_collection as $clave=>$valor) $array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_ingreso->array_producto_ids = $array_producto_ids;

			$tbl_editar_ingresodetalle_array = $this->render_regex_dict('TBL_INGRESODETALLE', $tbl_editar_ingresodetalle_array, 
																		$ingresodetalle_collection);
			$tbl_editar_ingresodetalle_array = str_replace('<!--TBL_INGRESODETALLE-->', '', $tbl_editar_ingresodetalle_array);
			$hidden_editar_ingresodetalle_array = $this->render_regex_dict('HDN_INGRESODETALLE', $hidden_editar_ingresodetalle_array, 
																		   $ingresodetalle_collection);
			$hidden_editar_ingresodetalle_array = str_replace('<!--HDN_INGRESODETALLE-->', '', $hidden_editar_ingresodetalle_array);
			$costo_base = 0;
			foreach ($ingresodetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_ingreso->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_ingresodetalle_array = ''; 
			$hidden_editar_ingresodetalle_array = '';
		}

		$opcion_precio = $obj_ingreso->actualiza_precio_producto;
		$opcion_stock = $obj_ingreso->actualiza_stock;
		unset($obj_ingreso->proveedor->infocontacto_collection);
		$obj_ingreso->checked_actualiza_precio_producto_si = ($opcion_precio == 1) ? 'checked' : '';
		$obj_ingreso->checked_actualiza_precio_producto_no = ($opcion_precio == 0) ? 'checked' : '';
		$obj_ingreso->checked_actualiza_stock_si = ($opcion_stock == 1) ? 'checked' : '';
		$obj_ingreso->checked_actualiza_stock_no = ($opcion_stock == 0) ? 'checked' : '';
		$obj_ingreso->proveedor_id = $obj_ingreso->proveedor->proveedor_id;
		$obj_ingreso->proveedor->descripcion = $obj_ingreso->proveedor->razon_social;
		$obj_ingreso = $this->set_dict($obj_ingreso);
		$render = str_replace('{ingreso-costobase}', $costo_base, $gui);
		$render = str_replace('{tbl_editar_ingresodetalle_array}', $tbl_editar_ingresodetalle_array, $render);
		$render = str_replace('{hidden_editar_ingresodetalle_array}', $hidden_editar_ingresodetalle_array, $render);
		$render = $this->render($obj_ingreso, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar_producto_ajax($producto_collection) {
		$gui_tbl_producto = file_get_contents("static/modules/ingreso/tbl_buscar_producto_ajax.html");
		$gui_tbl_producto = $this->render_regex_dict('TBL_PRODUCTO', $gui_tbl_producto, $producto_collection);
		$gui_tbl_producto = str_replace('<!--TBL_PRODUCTO-->', '', $gui_tbl_producto);
		print $gui_tbl_producto;
	}

	function traer_formulario_producto_ajax($obj_producto) {
		$gui = file_get_contents("static/modules/ingreso/formulario_producto.html");		
		$costo_iva = (($obj_producto->costo * $obj_producto->iva) / 100) + $obj_producto->costo;
		$valor_ganancia = $costo_iva * $obj_producto->porcentaje_ganancia / 100;
		$costo_iva_ganancia = $costo_iva + $valor_ganancia;
		$valor_descuento = $costo_iva_ganancia * $obj_producto->descuento / 100;
		$valor_venta = $costo_iva_ganancia - $valor_descuento;
		$obj_producto->costo = round($obj_producto->costo, 3);
		$obj_producto->costo_iva = round($costo_iva, 3);
		$obj_producto->valor_ganancia = round($valor_ganancia, 3);
		$obj_producto->valor_descuento = round($valor_descuento, 3);
		$obj_producto->valor_venta = round($valor_venta, 3);
		$obj_producto->descripcion = $obj_producto->productomarca->denominacion . ' ' . $obj_producto->denominacion . ' ';
		$obj_producto->contenido_unidad = $obj_producto->productounidad->denominacion;
		$obj_producto = $this->set_dict($obj_producto);
		$gui = $this->render($obj_producto, $gui);
		print $gui;
	}

	function traer_formulario_editar_producto_ajax($obj_producto, $old_costo) {
		$gui = file_get_contents("static/modules/ingreso/formulario_producto.html");		
		$costo_iva = (($obj_producto->costo * $obj_producto->iva) / 100) + $obj_producto->costo;
		$valor_ganancia = $costo_iva * $obj_producto->porcentaje_ganancia / 100;
		$costo_iva_ganancia = $costo_iva + $valor_ganancia;
		$valor_descuento = $costo_iva_ganancia * $obj_producto->descuento / 100;
		$valor_venta = $costo_iva_ganancia - $valor_descuento;
		$obj_producto->costo = round($obj_producto->costo, 3);
		$obj_producto->costo_iva = round($costo_iva, 3);
		$obj_producto->valor_ganancia = round($valor_ganancia, 3);
		$obj_producto->valor_descuento = round($valor_descuento, 3);
		$obj_producto->valor_venta = round($valor_venta, 3);
		if (is_array($old_costo) AND !empty($old_costo)) {
			$old_costo = $old_costo[0]['COSTO'];
			$obj_producto->costo = $old_costo;
		}
		
		$obj_producto->descripcion = $obj_producto->productomarca->denominacion . ' ' . $obj_producto->denominacion . ' ';
		$obj_producto->contenido_unidad = $obj_producto->productounidad->denominacion;
		$obj_producto = $this->set_dict($obj_producto);
		$gui = $this->render($obj_producto, $gui);
		print $gui;
	}

	function traer_formulario_reingreso_producto_ajax($obj_producto, $obj_ingresodetalle) {
		$gui = file_get_contents("static/modules/ingreso/formulario_reingreso_producto.html");		
		
		$costo_iva = (($obj_producto->costo * $obj_producto->iva) / 100) + $obj_producto->costo;
		$valor_ganancia = $costo_iva * $obj_producto->porcentaje_ganancia / 100;
		$costo_iva_ganancia = $costo_iva + $valor_ganancia;
		$valor_descuento = $costo_iva_ganancia * $obj_producto->descuento / 100;
		$valor_venta = $costo_iva_ganancia - $valor_descuento;
		
		$obj_producto->costo = round($obj_producto->costo, 3);
		$obj_producto->costo_iva = round($costo_iva, 3);
		$obj_producto->valor_ganancia = round($valor_ganancia, 3);
		$obj_producto->valor_descuento = round($valor_descuento, 3);
		$obj_producto->valor_venta = round($valor_venta, 3);
		$obj_producto->descripcion = $obj_producto->productomarca->denominacion . ' ' . $obj_producto->denominacion . ' ';
		$obj_producto->contenido_unidad = $obj_producto->productounidad->denominacion;
		$obj_producto = $this->set_dict($obj_producto);
		
		$obj_ingresodetalle = $this->set_dict($obj_ingresodetalle);
		
		$gui = $this->render($obj_producto, $gui);
		$gui = $this->render($obj_ingresodetalle, $gui);
		print $gui;
	}

	function traer_formulario_editar_ingresar_ajax($obj_ingreso, $proveedor_collection, $tipofactura_collection) {
		$gui = file_get_contents("static/modules/ingreso/formulario_editar_ingresar_ajax.html");		
		$slt_proveedor_array = file_get_contents("static/common/slt_proveedor_array.html");
		$slt_proveedor_array = $this->render_regex_dict('SLT_PROVEEDOR', $slt_proveedor_array, $proveedor_collection);
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		
		unset($obj_ingreso->proveedor->infocontacto_collection);
		$obj_ingreso = $this->set_dict($obj_ingreso);
		
		$render = str_replace('{slt_proveedor}', $slt_proveedor_array, $gui);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		$render = $this->render($obj_ingreso, $render);
		$render = $this->render($obj_ingresodetalle, $render);
		print $render;
	}
}
?>