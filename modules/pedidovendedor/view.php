<?php


class PedidoVendedorView extends View {
	function panel($pedidovendedor_collection, $vendedor_collection) {
		$gui = file_get_contents("static/modules/pedidovendedor/panel.html");
		$gui_tbl_vendedor = file_get_contents("static/modules/pedidovendedor/tbl_vendedor_array.html");
		$gui_tbl_vendedor = $this->render_regex_dict('TBL_VENDEDOR', $gui_tbl_vendedor, $vendedor_collection);

		$user_rol = $_SESSION["data-login-" . APP_ABREV]["usuario-configuracionmenu"];
		switch ($user_rol) {
			// FACTURADOR
			case 3:
				$tbl_pedidovendedor_array = file_get_contents("static/modules/pedidovendedor/tbl_pedidovendedor_array_facturador.html");
				$display_descargar = 'inline-block';
				break;
			// SUPERVISOR
			case 4:
				$tbl_pedidovendedor_array = file_get_contents("static/modules/pedidovendedor/tbl_pedidovendedor_array_supervisor.html");
				$display_descargar = 'inline-block';
				break;
			// VENDEDOR
			case 5:
				$tbl_pedidovendedor_array = file_get_contents("static/modules/pedidovendedor/tbl_pedidovendedor_array_vendedor.html");
				$display_descargar = 'none';
				break;
			default:
				$tbl_pedidovendedor_array = file_get_contents("static/modules/pedidovendedor/tbl_pedidovendedor_array.html");
				$display_descargar = 'inline-block';
				break;
		}

		$tbl_pedidovendedor_array = $this->render_regex_dict('TBL_PEDIDOVENDEDOR', $tbl_pedidovendedor_array, $pedidovendedor_collection);	
		$render = str_replace('{tbl_pedidovendedor}', $tbl_pedidovendedor_array, $gui);
		$render = str_replace('{slt_vendedor}', $gui_tbl_vendedor, $render);
		$render = str_replace('{usuario-display_descargar_pedidos}', $display_descargar, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($producto_collection, $cliente_collection, $condicionpago_collection, $condicioniva_collection, $tipofactura_collection) {
		$gui = file_get_contents("static/modules/pedidovendedor/agregar.html");
		$tbl_producto_array = file_get_contents("static/modules/pedidovendedor/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/pedidovendedor/tbl_cliente_array.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		
		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		
		$render = str_replace('{hora}', date('H:i:s'), $gui);
		$render = str_replace('{fecha}', date('Y-m-d'), $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $render);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_formulario_producto_ajax($obj_producto, $cantidad_disponible) {
		$gui = file_get_contents("static/modules/pedidovendedor/formulario_producto.html");		
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

	function consultar($pedidovendedordetalle_collection, $obj_pedidovendedor, $obj_cliente, $obj_vendedor) {
		$gui = file_get_contents("static/modules/pedidovendedor/consultar.html");
		$tbl_pedidovendedor_array = file_get_contents("static/modules/pedidovendedor/tbl_pedidovendedordetalle_array.html");
		$tbl_pedidovendedor_array = $this->render_regex_dict('TBL_PEDIDOVENDEDORDETALLE', $tbl_pedidovendedor_array, $pedidovendedordetalle_collection);
		
		unset($obj_cliente->infocontacto_collection, $obj_vendedor->infocontacto_collection, $obj_cliente->flete, $obj_cliente->vendedor);

		$obj_pedidovendedor->numero_pedido = str_pad($obj_pedidovendedor->pedidovendedor_id, 8, '0', STR_PAD_LEFT);
		$obj_pedidovendedor = $this->set_dict($obj_pedidovendedor);
		$obj_cliente = $this->set_dict($obj_cliente);
		$obj_vendedor = $this->set_dict($obj_vendedor);
		
		$render = str_replace('{tbl_pedidovendedordetalle}', $tbl_pedidovendedor_array, $gui);
		$render = $this->render($obj_pedidovendedor, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($obj_vendedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($producto_collection, $cliente_collection, $pedidovendedordetalle_collection, $obj_pedidovendedor, $obj_cliente) {
		$gui = file_get_contents("static/modules/pedidovendedor/editar.html");
		$tbl_producto_array = file_get_contents("static/modules/pedidovendedor/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/pedidovendedor/tbl_cliente_array.html");
		$tbl_editar_pedidovendedordetalle_array = file_get_contents("static/modules/pedidovendedor/tbl_editar_pedidovendedordetalle_array.html");
		$hidden_editar_pedidovendedordetalle_array = file_get_contents("static/modules/pedidovendedor/hidden_editar_pedidovendedordetalle_array.html");

		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_producto_array = str_replace('<!--TBL_PRODUCTO-->', '', $tbl_producto_array);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_cliente_array = str_replace('<!--TBL_CLIENTE-->', '', $tbl_cliente_array);
		if (!empty($pedidovendedordetalle_collection) OR is_array($pedidovendedordetalle_collection)) {
			$array_producto_ids = array();
			foreach ($pedidovendedordetalle_collection as $clave=>$valor) $array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_pedidovendedor->array_producto_ids = $array_producto_ids;

			$tbl_editar_pedidovendedordetalle_array = $this->render_regex_dict('TBL_PEDIDOVENDEDORDETALLE', $tbl_editar_pedidovendedordetalle_array, 
																			   $pedidovendedordetalle_collection);
			$tbl_editar_pedidovendedordetalle_array = str_replace('<!--TBL_PEDIDOVENDEDORDETALLE-->', '', $tbl_editar_pedidovendedordetalle_array);
			
			$hidden_editar_pedidovendedordetalle_array = $this->render_regex_dict('HDN_PEDIDOVENDEDORDETALLE', $hidden_editar_pedidovendedordetalle_array, 
																		   		  $pedidovendedordetalle_collection);
			$hidden_editar_pedidovendedordetalle_array = str_replace('<!--HDN_PEDIDOVENDEDORDETALLE-->', '', $hidden_editar_pedidovendedordetalle_array);
			$costo_base = 0;
			foreach ($pedidovendedordetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_pedidovendedor->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_pedidovendedordetalle_array = ''; 
			$hidden_editar_pedidovendedordetalle_array = '';
		}

		unset($obj_cliente->infocontacto_collection, $obj_cliente->vendedor->infocontacto_collection);
		$obj_pedidovendedor = $this->set_dict($obj_pedidovendedor);
		$obj_cliente = $this->set_dict($obj_cliente);
		
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $gui);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{pedidovendedor-costobase}', $costo_base, $render);
		$render = str_replace('{tbl_editar_pedidovendedordetalle_array}', $tbl_editar_pedidovendedordetalle_array, $render);
		$render = str_replace('{hidden_editar_pedidovendedordetalle_array}', $hidden_editar_pedidovendedordetalle_array, $render);
		$render = $this->render($obj_pedidovendedor, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function procesar($producto_collection, $cliente_collection, $pedidovendedordetalle_collection, $condicionpago_collection, $condicioniva_collection, $tipofactura_collection, $obj_pedidovendedor, $obj_cliente, $flag_error) {
		$gui = file_get_contents("static/modules/pedidovendedor/procesar.html");
		$tbl_producto_array = file_get_contents("static/modules/pedidovendedor/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/pedidovendedor/tbl_cliente_array.html");
		$tbl_editar_pedidovendedordetalle_array = file_get_contents("static/modules/pedidovendedor/tbl_editar_pedidovendedordetalle_array.html");
		$hidden_editar_pedidovendedordetalle_array = file_get_contents("static/modules/pedidovendedor/hidden_editar_pedidovendedordetalle_array.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		
		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);

		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_producto_array = str_replace('<!--TBL_PRODUCTO-->', '', $tbl_producto_array);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_cliente_array = str_replace('<!--TBL_CLIENTE-->', '', $tbl_cliente_array);
		
		if (!empty($pedidovendedordetalle_collection) OR is_array($pedidovendedordetalle_collection)) {
			$array_producto_ids = array();
			foreach ($pedidovendedordetalle_collection as $clave=>$valor) {
				$costo_flete = $valor['COSTO'] + (($valor['COSTO'] * $valor['FLETE']) / 100);
				$costo_iva = (($costo_flete * $valor['IVA']) / 100) + $costo_flete;
				$valor_ganancia = $costo_iva * $valor['VALGAN'] / 100;
				$valor_venta = $costo_iva + $valor_ganancia;
				$pedidovendedordetalle_collection[$clave]['COSTO'] = round($valor_venta, 2);
				$array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
			}
			
			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_pedidovendedor->array_producto_ids = $array_producto_ids;

			$tbl_editar_pedidovendedordetalle_array = $this->render_regex_dict('TBL_PEDIDOVENDEDORDETALLE', $tbl_editar_pedidovendedordetalle_array, $pedidovendedordetalle_collection);
			$tbl_editar_pedidovendedordetalle_array = str_replace('<!--TBL_PEDIDOVENDEDORDETALLE-->', '', $tbl_editar_pedidovendedordetalle_array);
			
			$hidden_editar_pedidovendedordetalle_array = $this->render_regex_dict('HDN_PEDIDOVENDEDORDETALLE', $hidden_editar_pedidovendedordetalle_array, $pedidovendedordetalle_collection);
			$hidden_editar_pedidovendedordetalle_array = str_replace('<!--HDN_PEDIDOVENDEDORDETALLE-->', '', $hidden_editar_pedidovendedordetalle_array);
			$costo_base = 0;
			foreach ($pedidovendedordetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_pedidovendedor->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_pedidovendedordetalle_array = ''; 
			$hidden_editar_pedidovendedordetalle_array = '';
		}

		$obj_vendedor = $obj_cliente->vendedor;
		unset($obj_cliente->infocontacto_collection, $obj_cliente->vendedor->infocontacto_collection, $obj_cliente->flete->infocontacto_collection);
		
		$obj_pedidovendedor = $this->set_dict($obj_pedidovendedor);
		$obj_cliente = $this->set_dict($obj_cliente);
		$btn_disabled = ($flag_error == 1) ? 'disabled' : '';
		
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $gui);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $render);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{pedidovendedor-costobase}', $costo_base, $render);
		$render = str_replace('{tbl_editar_pedidovendedordetalle_array}', $tbl_editar_pedidovendedordetalle_array, $render);
		$render = str_replace('{hidden_editar_pedidovendedordetalle_array}', $hidden_editar_pedidovendedordetalle_array, $render);
		$render = str_replace('{btn_disabled}', $btn_disabled, $render);
		$render = $this->render($obj_pedidovendedor, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function prepara_lote_vendedor($pedidovendedor_collection, $obj_vendedor) {
		$gui = file_get_contents("static/modules/pedidovendedor/prepara_lote_vendedor.html");
		$tbl_pedidovendedor = file_get_contents("static/modules/pedidovendedor/tbl_prepara_lote_pedidovendedor_array.html");
		$tbl_pedidovendedor = $this->render_regex_dict('TBL_PEDIDOVENDEDOR', $tbl_pedidovendedor, $pedidovendedor_collection);

		unset($obj_vendedor->infocontacto_collection);
		$obj_vendedor = $this->set_dict($obj_vendedor);
		$render = str_replace('{tbl_pedidovendedor}', $tbl_pedidovendedor, $gui);
		$render = $this->render($obj_vendedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_pedidovendedor_procesolote_ajax($producto_collection, $pedidovendedordetalle_collection, $condicionpago_collection, $condicioniva_collection, $tipofactura_collection, $obj_pedidovendedor, $obj_cliente) {
		$gui = file_get_contents("static/modules/pedidovendedor/procesolote_pedidovendedor_ajax.html");
		$tbl_pedidovendedordetalle = file_get_contents("static/modules/pedidovendedor/tbl_procesolote_pedidovendedordetalle.html");
		
		if (!empty($pedidovendedordetalle_collection) OR is_array($pedidovendedordetalle_collection)) {
			$array_producto_ids = array();
			foreach ($pedidovendedordetalle_collection as $clave=>$valor) {
				$costo_flete = $valor['COSTO'] + (($valor['COSTO'] * $valor['FLETE']) / 100);
				$costo_iva = (($costo_flete * $valor['IVA']) / 100) + $costo_flete;
				$valor_ganancia = $costo_iva * $valor['VALGAN'] / 100;
				$valor_venta = $costo_iva + $valor_ganancia;
				$pedidovendedordetalle_collection[$clave]['COSTO'] = round($valor_venta, 2);
				$array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
			}
			
			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_pedidovendedor->array_producto_ids = $array_producto_ids;

			$tbl_pedidovendedordetalle = $this->render_regex_dict('TBL_PEDIDOVENDEDORDETALLE', $tbl_pedidovendedordetalle, $pedidovendedordetalle_collection);
			$tbl_pedidovendedordetalle = str_replace('<!--TBL_PEDIDOVENDEDORDETALLE-->', '', $tbl_pedidovendedordetalle);
			
			$costo_base = 0;
			foreach ($pedidovendedordetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_pedidovendedor->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_pedidovendedordetalle = ''; 
			$hidden_editar_pedidovendedordetalle_array = '';
		}

		unset($obj_cliente->infocontacto_collection, $obj_cliente->flete, $obj_cliente->vendedor, $obj_cliente->condicioniva, $obj_cliente->condicionfiscal, $obj_cliente->tipofactura);
		$obj_cliente = $this->set_dict($obj_cliente);

		$obj_pedidovendedor->numero_pedido = str_pad($obj_pedidovendedor->pedidovendedor_id, 8, '0', STR_PAD_LEFT);
		$obj_pedidovendedor = $this->set_dict($obj_pedidovendedor);

		$render = str_replace('{tbl_pedidovendedordetalle}', $tbl_pedidovendedordetalle, $gui);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($obj_pedidovendedor, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}
}
?>