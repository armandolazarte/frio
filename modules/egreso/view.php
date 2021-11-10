<?php


class EgresoView extends View {
	
	function listar($egreso_collection, $array_msj, $array_totales) {
		$gui = file_get_contents("static/modules/egreso/listar.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_egreso_array_operador.html");
				break;
			case 2:
				$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_egreso_array_supervisor.html");
				break;
			default:
				$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_egreso_array.html");
				break;
		}

		$tbl_egreso_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_array, $egreso_collection);		
		$render = str_replace('{tbl_egreso}', $tbl_egreso_array, $gui);
		$render = $this->render($array_totales, $render);
		$render = $this->render_breadcrumb($render);
		$render = $this->render($array_msj, $render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function buscar($egreso_collection) {
		$gui = file_get_contents("static/modules/egreso/buscar.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_egreso_array_operador.html");
				break;
			case 2:
				$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_egreso_array_supervisor.html");
				break;
			default:
				$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_egreso_array.html");
				break;
		}
		$tbl_egreso_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_array, $egreso_collection);		

		$render = str_replace('{tbl_egreso}', $tbl_egreso_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function entregas_pendientes($egreso_collection, $flete_collection, $arg) {
		$gui = file_get_contents("static/modules/egreso/entregas_pendientes.html");
		$slt_flete = file_get_contents("static/common/slt_flete.html");
		$lst_btn_flete = file_get_contents("static/modules/egreso/lst_btn_flete.html");
		$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_entregaspendientes_array.html");
		$tbl_egreso_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_array, $egreso_collection);		

		foreach ($flete_collection as $clave=>$valor) if ($valor->oculto == 1) unset($flete_collection[$clave]);
		foreach ($flete_collection as $flete) unset($flete->infocontacto_collection);
		$lst_btn_flete = $this->render_regex('LST_FLETE', $lst_btn_flete, $flete_collection);		

		switch ($arg) {
			case 1:
				$modal_array = array('{display_modal}'=>'',
									 '{display_btn}'=>'none',
									 '{txt_modal}'=>'');
				break;
			case 2:
				$modal_array = array('{display_modal}'=>'show',
									 '{display_btn}'=>'inline-block',
									 '{txt_modal}'=>'Se ha generado la hoja de ruta.');
				break;
			case 3:
				$modal_array = array('{display_modal}'=>'show',
									 '{display_btn}'=>'none',
									 '{txt_modal}'=>'Seleccione al menos una entrega pendiente.');
				break;
			case 4:
				$modal_array = array('{display_modal}'=>'show',
									 '{display_btn}'=>'none',
									 '{txt_modal}'=>'Ha ocurrido un error, por favor intente nuevamente.');
				break;
			case 5:
				$modal_array = array('{display_modal}'=>'show',
									 '{display_btn}'=>'none',
									 '{txt_modal}'=>'Se han confirmado las entregas pendientes.');
				break;
		}

		$slt_flete = $this->render_regex('SLT_FLETE', $slt_flete, $flete_collection);		
		$render = str_replace('{tbl_entregaspendientes}', $tbl_egreso_array, $gui);
		$render = str_replace('{slt_flete}', $slt_flete, $render);
		$render = str_replace('{lst_btn_flete}', $lst_btn_flete, $render);
		$render = $this->render($modal_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function flete_entregas_pendientes($egreso_collection, $flete_collection, $obj_flete) {
		$gui = file_get_contents("static/modules/egreso/flete_entregas_pendientes.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_entregaspendientes_flete_array.html");
		$tbl_egreso_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_array, $egreso_collection);		

		foreach ($flete_collection as $clave=>$valor) if ($valor->flete_id == 1) unset($flete_collection[$clave]);
		foreach ($flete_collection as $flete) unset($flete->infocontacto_collection);

		if ($obj_flete->documentotipo->denominacion == 'CUIL' OR $obj_flete->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_flete->documento, 0, 2);
			$cuil2 = substr($obj_flete->documento, 2, 8);
			$cuil3 = substr($obj_flete->documento, 10);
			$obj_flete->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}
		
		$infocontacto_collection = $obj_flete->infocontacto_collection;
		unset($obj_flete->infocontacto_collection);
		$obj_flete = $this->set_dict($obj_flete);
		
		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$render = $this->render_regex('LST_FLETE', $gui, $flete_collection);		
		$render = str_replace('{tbl_entregaspendientes}', $tbl_egreso_array, $render);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $render);
		$render = $this->render($obj_flete, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function egresar($producto_collection, $cliente_collection, $vendedor_collection, $tipofactura_collection,
					 $condicionpago_collection, $condicioniva_collection, $array_remito) {
		$gui = file_get_contents("static/modules/egreso/egresar.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$tbl_producto_array = file_get_contents("static/modules/egreso/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/egreso/tbl_cliente_array.html");
		$tbl_vendedor_array = file_get_contents("static/modules/egreso/tbl_vendedor_array.html");
		
		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_vendedor_array = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor_array, $vendedor_collection);

		$render = str_replace('{hora}', date('H:i:s'), $gui);
		$render = str_replace('{fecha}', date('Y-m-d'), $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $render);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor_array, $render);
		$render = $this->render($array_remito, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function cb_egresar($producto_collection, $cliente_collection, $vendedor_collection, $tipofactura_collection,
					 	$condicionpago_collection, $condicioniva_collection, $array_remito, $obj_configuracioncomprobante) {
		$gui = file_get_contents("static/modules/egreso/cb_egresar.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$tbl_producto_array = file_get_contents("static/modules/egreso/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/egreso/tbl_cliente_array.html");
		$tbl_vendedor_array = file_get_contents("static/modules/egreso/tbl_vendedor_array.html");
		
		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_vendedor_array = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor_array, $vendedor_collection);

		$obj_configuracioncomprobante->txt_parteuno_codebar = ($obj_configuracioncomprobante->parteuno_codebar == 1) ? 'BarCode' : 'Pesaje';
		$obj_configuracioncomprobante->txt_partedos_codebar = ($obj_configuracioncomprobante->partedos_codebar == 1) ? 'BarCode' : 'Pesaje';
		$obj_configuracioncomprobante = $this->set_dict($obj_configuracioncomprobante);

		$render = str_replace('{hora}', date('H:i:s'), $gui);
		$render = str_replace('{fecha}', date('Y-m-d'), $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $render);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor_array, $render);
		$render = $this->render($array_remito, $render);
		$render = $this->render($obj_configuracioncomprobante, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($producto_collection, $cliente_collection, $vendedor_collection, $condicionpago_collection, 
					$condicioniva_collection, $egresodetalle_collection, $obj_egreso) {
		$gui = file_get_contents("static/modules/egreso/editar.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		$slt_condicioniva = file_get_contents("static/common/slt_condicioniva.html");
		$tbl_producto_array = file_get_contents("static/modules/egreso/tbl_producto_array.html");
		$tbl_cliente_array = file_get_contents("static/modules/egreso/tbl_cliente_array.html");
		$tbl_vendedor_array = file_get_contents("static/modules/egreso/tbl_vendedor_array.html");
		$tbl_editar_egresodetalle_array = file_get_contents("static/modules/egreso/tbl_editar_egresodetalle_array.html");
		$hidden_editar_egresodetalle_array = file_get_contents("static/modules/egreso/hidden_editar_egresodetalle_array.html");

		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_condicionpago = str_replace('<!--SLT_CONDICIONPAGO-->', '', $slt_condicionpago);
		$slt_condicioniva = $this->render_regex('SLT_CONDICIONIVA', $slt_condicioniva, $condicioniva_collection);
		$slt_condicioniva = str_replace('<!--SLT_CONDICIONIVA-->', '', $slt_condicioniva);
		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_producto_array = str_replace('<!--TBL_PRODUCTO-->', '', $tbl_producto_array);
		$tbl_cliente_array = $this->render_regex_dict('TBL_CLIENTE', $tbl_cliente_array, $cliente_collection);
		$tbl_cliente_array = str_replace('<!--TBL_CLIENTE-->', '', $tbl_cliente_array);
		$tbl_vendedor_array = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor_array, $vendedor_collection);
		$tbl_vendedor_array = str_replace('<!--TBL_VENDEDOR-->', '', $tbl_vendedor_array);
		
		if (!empty($egresodetalle_collection) OR is_array($egresodetalle_collection)) {
			$array_producto_ids = array();
			$j = 1;
			foreach ($egresodetalle_collection as $clave=>$valor) {
				$egresodetalle_collection[$clave]["INDICE"] = $j;
				$array_producto_ids[] = '"' . $valor['PRODUCTO'] . '"';
				$j = $j + 1;
			}

			$array_producto_ids = implode(',', $array_producto_ids);
			$obj_egreso->array_producto_ids = $array_producto_ids;

			$tbl_editar_egresodetalle_array = $this->render_regex_dict('TBL_EGRESODETALLE', $tbl_editar_egresodetalle_array, 
																		$egresodetalle_collection);
			$tbl_editar_egresodetalle_array = str_replace('<!--TBL_EGRESODETALLE-->', '', $tbl_editar_egresodetalle_array);
			$hidden_editar_egresodetalle_array = $this->render_regex_dict('HDN_EGRESODETALLE', $hidden_editar_egresodetalle_array, 
																		   $egresodetalle_collection);
			$hidden_editar_egresodetalle_array = str_replace('<!--HDN_EGRESODETALLE-->', '', $hidden_editar_egresodetalle_array);
			$costo_base = 0;
			foreach ($egresodetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_egreso->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_egresodetalle_array = ''; 
			$hidden_editar_egresodetalle_array = '';
		}

		unset($obj_egreso->cliente->infocontacto_collection, $obj_egreso->vendedor->infocontacto_collection, 
			  $obj_egreso->cliente->vendedor->infocontacto_collection, $obj_egreso->egresoentrega);
		$txt_cliente = $obj_egreso->cliente->documentotipo->denominacion . ' ' . $obj_egreso->cliente->documento;
		$txt_cliente .= ' - ' . $obj_egreso->cliente->razon_social;
		$obj_egreso->cliente->descripcion = $txt_cliente;
		$txt_vendedor = $obj_egreso->vendedor->documentotipo->denominacion . ' ' . $obj_egreso->vendedor->documento;
		$txt_vendedor .= ' - ' . $obj_egreso->vendedor->apellido . ' ' . $obj_egreso->vendedor->nombre;
		$obj_egreso->vendedor->descripcion = $txt_vendedor;
		$obj_egreso->vendedor_id = $obj_egreso->vendedor->vendedor_id;
		$obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_egreso = $this->set_dict($obj_egreso);
		
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $gui);
		$render = str_replace('{slt_condicioniva}', $slt_condicioniva, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $render);
		$render = str_replace('{tbl_cliente}', $tbl_cliente_array, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor_array, $render);
		$render = str_replace('{egreso-costobase}', $costo_base, $render);
		$render = str_replace('{tbl_editar_egresodetalle_array}', $tbl_editar_egresodetalle_array, $render);
		$render = str_replace('{hidden_editar_egresodetalle_array}', $hidden_editar_egresodetalle_array, $render);
		$render = $this->render($obj_egreso, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function reingreso($producto_collection, $egresodetalle_collection, $obj_egreso) {
		$gui = file_get_contents("static/modules/egreso/reingreso.html");
		$tbl_producto_array = file_get_contents("static/modules/egreso/tbl_producto_array.html");
		$tbl_editar_egresodetalle_array = file_get_contents("static/modules/egreso/tbl_editar_egresodetalle_array.html");
		$hidden_editar_egresodetalle_array = file_get_contents("static/modules/egreso/hidden_editar_egresodetalle_array.html");

		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$tbl_producto_array = str_replace('<!--TBL_PRODUCTO-->', '', $tbl_producto_array);
		
		if (!empty($egresodetalle_collection) OR is_array($egresodetalle_collection)) {
			$j = 1;
			foreach ($egresodetalle_collection as $clave=>$valor) {
				$egresodetalle_collection[$clave]["INDICE"] = $j;
				$j = $j + 1;
			}

			$tbl_editar_egresodetalle_array = $this->render_regex_dict('TBL_EGRESODETALLE', $tbl_editar_egresodetalle_array, 
																		$egresodetalle_collection);
			$tbl_editar_egresodetalle_array = str_replace('<!--TBL_EGRESODETALLE-->', '', $tbl_editar_egresodetalle_array);
			$hidden_editar_egresodetalle_array = $this->render_regex_dict('HDN_EGRESODETALLE', $hidden_editar_egresodetalle_array, 
																		   $egresodetalle_collection);
			$hidden_editar_egresodetalle_array = str_replace('<!--HDN_EGRESODETALLE-->', '', $hidden_editar_egresodetalle_array);
			$costo_base = 0;
			foreach ($egresodetalle_collection as $clave=>$valor) $costo_base = $costo_base + $valor['IMPORTE'];
			$obj_egreso->costo_base = $costo_base;
		} else {
			$costo_base = 0;
			$tbl_editar_egresodetalle_array = ''; 
			$hidden_editar_egresodetalle_array = '';
		}

		unset($obj_egreso->cliente->infocontacto_collection, $obj_egreso->vendedor->infocontacto_collection, 
			  $obj_egreso->cliente->vendedor->infocontacto_collection, $obj_egreso->egresoentrega);
		$txt_cliente = $obj_egreso->cliente->documentotipo->denominacion . ' ' . $obj_egreso->cliente->documento;
		$txt_cliente .= ' - ' . $obj_egreso->cliente->razon_social;
		$obj_egreso->cliente->descripcion = $txt_cliente;
		$txt_vendedor = $obj_egreso->vendedor->documentotipo->denominacion . ' ' . $obj_egreso->vendedor->documento;
		$txt_vendedor .= ' - ' . $obj_egreso->vendedor->apellido . ' ' . $obj_egreso->vendedor->nombre;
		$obj_egreso->vendedor->descripcion = $txt_vendedor;
		$obj_egreso->vendedor_id = $obj_egreso->vendedor->vendedor_id;
		$obj_egreso = $this->set_dict($obj_egreso);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $gui);
		$render = str_replace('{egreso-costobase}', $costo_base, $render);
		$render = str_replace('{tbl_editar_egresodetalle_array}', $tbl_editar_egresodetalle_array, $render);
		$render = str_replace('{hidden_editar_egresodetalle_array}', $hidden_editar_egresodetalle_array, $render);
		$render = $this->render($obj_egreso, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($egresodetalle_collection, $cuentacorrientecliente_collection, $obj_egreso, $egresoafip, $notacredito_id) {
		$gui = file_get_contents("static/modules/egreso/consultar.html");
		$tbl_egresodetalle_array = file_get_contents("static/modules/egreso/tbl_egresodetalle_array.html");
		$tbl_egresodetalle_array = $this->render_regex_dict('TBL_EGRESODETALLE', $tbl_egresodetalle_array, $egresodetalle_collection);
		
		$tipofactura_cliente_nomenclatura = $obj_egreso->cliente->tipofactura->nomenclatura;
		$tipofactura_cliente_id = $obj_egreso->cliente->tipofactura->tipofactura_id;
		unset($obj_egreso->cliente->infocontacto_collection, $obj_egreso->vendedor->infocontacto_collection, $obj_egreso->cliente->flete->infocontacto_collection,
			  $obj_egreso->cliente->vendedor->infocontacto_collection, $obj_egreso->egresoentrega->flete->infocontacto_collection);
		$obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_egreso->egresocomision->valor_abonado = round($obj_egreso->egresocomision->valor_abonado, 2);
		$estadoentrega_id = $obj_egreso->egresoentrega->estadoentrega->estadoentrega_id;
		$btn_entrega_display = ($estadoentrega_id == 1 OR $estadoentrega_id == 2) ? 'block' : 'none';
		$obj_egreso->egresoentrega->btn_entrega_display = $btn_entrega_display;
		
		if (!empty($cuentacorrientecliente_collection)) {
			$obj_egreso->btn_generar_nc = 'none';
			$obj_egreso->btn_consultar_nc = ($notacredito_id == 0) ? 'none' : 'block';
		} else {
			if ($obj_egreso->egresocomision->estadocomision->estadocomision_id != 1) {
				$obj_egreso->btn_generar_nc = 'none';
				$obj_egreso->btn_consultar_nc = ($notacredito_id == 0) ? 'none' : 'block';
			} else {
				$obj_egreso->btn_generar_nc = ($notacredito_id == 0) ? 'block' : 'none';
				$obj_egreso->btn_consultar_nc = ($notacredito_id == 0) ? 'none' : 'block';
			}
		}

		$obj_egreso->div_facturarafip_display = (empty($egresoafip)) ? 'inline-block' : 'none';
		$obj_egreso->div_facturarafip_display = ($obj_egreso->tipofactura->tipofactura_id == 2) ? 'none' : $obj_egreso->div_facturarafip_display;
		$obj_egreso->div_datos_facturarafip_display = (empty($egresoafip)) ? 'none' : 'inline-block';
		$obj_egreso->btn_imprimir_comprobante = ($obj_egreso->emitido == 0) ? 'none' : 'inline-block';
		$obj_egreso->btn_cerrar_comprobante = ($obj_egreso->emitido == 0) ? 'inline-block' : 'none';
		$obj_egreso = $this->set_dict($obj_egreso);

		if (isset($egresoafip['PUNTO_VENTA'])) $egresoafip['PUNTO_VENTA'] = str_pad($egresoafip['PUNTO_VENTA'], 4, '0', STR_PAD_LEFT);
		if (isset($egresoafip['NUMERO_FACTURA'])) $egresoafip['NUMERO_FACTURA'] = str_pad($egresoafip['NUMERO_FACTURA'], 8, '0', STR_PAD_LEFT);
		$egresoafip = $this->set_dict_array($egresoafip);

		$render = str_replace('{tbl_egresodetalle}', $tbl_egresodetalle_array, $gui);
		$render = str_replace('{notacredito-notacredito_id}', $notacredito_id, $render);
		$render = str_replace('{cliente-tipofactura-nomenclatura}', $tipofactura_cliente_nomenclatura, $render);
		$render = str_replace('{cliente-tipofactura-id}', $tipofactura_cliente_id, $render);
		$render = $this->render($obj_egreso, $render);
		$render = $this->render($egresoafip, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function configurar($egresodetalle_collection, $cuentacorrientecliente_collection, $obj_egreso, $egresoafip, $notacredito_id, 
						$vendedor_collection, $tipofactura_collection, $condicionpago_collection) {
		$gui = file_get_contents("static/modules/egreso/configurar.html");
		$slt_vendedor_array = file_get_contents("static/modules/egreso/slt_vendedor_array.html");
		$tbl_egresodetalle_array = file_get_contents("static/modules/egreso/tbl_egresodetalle_array.html");
		$slt_tipofactura = file_get_contents("static/common/slt_tipofactura.html");
		$slt_condicionpago = file_get_contents("static/common/slt_condicionpago.html");
		
		$vendedor_id = $obj_egreso->vendedor->vendedor_id;
		foreach ($vendedor_collection as $clave=>$valor) {
			$vendedor_collection[$clave]["SELECTED"] = ($vendedor_collection[$clave]["ID"] == $vendedor_id) ? 'selected' : '';
		}

		$tipofactura_cliente_nomenclatura = $obj_egreso->cliente->tipofactura->nomenclatura;
		$tipofactura_cliente_id = $obj_egreso->cliente->tipofactura->tipofactura_id;
		$tipofactura_egreso = $obj_egreso->tipofactura->tipofactura_id;
		unset($obj_egreso->cliente->infocontacto_collection, $obj_egreso->vendedor->infocontacto_collection, $obj_egreso->cliente->flete->infocontacto_collection,
			  $obj_egreso->cliente->vendedor->infocontacto_collection, $obj_egreso->egresoentrega->flete->infocontacto_collection);
		
		
		$obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_egreso->tipofactura_readonly = ($tipofactura_egreso == 2) ? '' : 'readonly';
		$obj_egreso->tipofactura_enabled = ($tipofactura_egreso == 2) ? '' : 'disabled';
		$obj_egreso = $this->set_dict($obj_egreso);
		
		if (isset($egresoafip['PUNTO_VENTA'])) $egresoafip['PUNTO_VENTA'] = str_pad($egresoafip['PUNTO_VENTA'], 4, '0', STR_PAD_LEFT);
		if (isset($egresoafip['NUMERO_FACTURA'])) $egresoafip['NUMERO_FACTURA'] = str_pad($egresoafip['NUMERO_FACTURA'], 8, '0', STR_PAD_LEFT);
		if (isset($egresoafip)) $egresoafip = $this->set_dict_array($egresoafip);

		$slt_tipofactura = $this->render_regex('SLT_TIPOFACTURA', $slt_tipofactura, $tipofactura_collection);
		$slt_condicionpago = $this->render_regex('SLT_CONDICIONPAGO', $slt_condicionpago, $condicionpago_collection);
		$slt_vendedor_array = $this->render_regex_dict('SLT_VENDEDOR', $slt_vendedor_array, $vendedor_collection);
		$tbl_egresodetalle_array = $this->render_regex_dict('TBL_EGRESODETALLE', $tbl_egresodetalle_array, $egresodetalle_collection);
		$render = str_replace('{tbl_egresodetalle}', $tbl_egresodetalle_array, $gui);
		$render = str_replace('{slt_vendedor}', $slt_vendedor_array, $render);
		$render = str_replace('{slt_tipofactura}', $slt_tipofactura, $render);
		$render = str_replace('{slt_condicionpago}', $slt_condicionpago, $render);
		$render = str_replace('{fecha_sys}', date('d/m/Y'), $render);
		$render = $this->render($obj_egreso, $render);
		$render = $this->render($egresoafip, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar_producto_ajax($producto_collection) {
		$gui_tbl_producto = file_get_contents("static/modules/egreso/tbl_buscar_producto_ajax.html");
		$gui_tbl_producto = $this->render_regex_dict('TBL_PRODUCTO', $gui_tbl_producto, $producto_collection);
		$gui_tbl_producto = str_replace('<!--TBL_PRODUCTO-->', '', $gui_tbl_producto);
		print $gui_tbl_producto;
	}

	function traer_formulario_entrega_ajax($flete_collection, $obj_egreso) {
		$gui = file_get_contents("static/modules/egreso/formulario_programar_entrega_ajax.html");
		$gui_slt_flete = file_get_contents("static/common/slt_flete.html");

		$obj_cliente = $obj_egreso->cliente;
		$obj_vendedor = $obj_egreso->vendedor;
		
		unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->vendedor, $obj_cliente->infocontacto_collection,
			  $obj_vendedor->infocontacto_collection);	
		foreach ($flete_collection as $clave=>$valor) unset($valor->infocontacto_collection);

		$comision = $obj_vendedor->comision;
		$valor_comision = round(($obj_egreso->importe_total * $comision / 100), 2);

		$obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_egreso->valor_a_abonar = round(($obj_egreso->importe_total * $obj_egreso->egresocomision->valor_comision / 100),2);
		$obj_vendedor->valor_comision = $valor_comision;
		$obj_vendedor = $this->set_dict($obj_vendedor);
		$obj_cliente = $this->set_dict($obj_cliente);
		$obj_egreso = $this->set_dict($obj_egreso);

		$gui_slt_flete = $this->render_regex('SLT_FLETE', $gui_slt_flete, $flete_collection);
		$render = str_replace('{slt_flete}', $gui_slt_flete, $gui);
		$render = $this->render($obj_vendedor, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($obj_egreso, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;	
	}

	function traer_formulario_producto_ajax($obj_producto, $cantidad_disponible) {
		$gui = file_get_contents("static/modules/egreso/formulario_producto.html");		
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

	function traer_formulario_producto_codebar_ajax($obj_producto, $cantidad_disponible, $pesaje) {
		$gui = file_get_contents("static/modules/egreso/formulario_producto_barcode.html");		
		$costo_flete = $obj_producto->costo + (($obj_producto->costo * $obj_producto->flete) / 100);
		$costo_iva = (($costo_flete * $obj_producto->iva) / 100) + $costo_flete;
		$valor_ganancia = $costo_iva * $obj_producto->porcentaje_ganancia / 100;
		$valor_venta = $costo_iva + $valor_ganancia;
		
		$obj_producto->costo = round($obj_producto->costo, 2);
		$obj_producto->valor_venta = round($costo_iva, 2);
		$obj_producto->valor_ganancia = round($valor_ganancia, 2);
		$obj_producto->valor_venta = round($valor_venta, 2);
		$obj_producto->descripcion = $obj_producto->productomarca->denominacion . ' ' . $obj_producto->denominacion . ' ';
		$subtotal = $obj_producto->valor_venta * $pesaje;
		$obj_producto = $this->set_dict($obj_producto);

		$gui = $this->render($obj_producto, $gui);
		$gui = str_replace('{cantidad_disponible}', $cantidad_disponible, $gui);
		$gui = str_replace('{barcode-pesaje}', $pesaje, $gui);
		$gui = str_replace('{subtotal}', $subtotal, $gui);
		print $gui;
	}

	function error_codebar_ajax($mensaje) {
		$gui = file_get_contents("static/modules/egreso/error_codebar_ajax.html");		
		$gui = str_replace('{mensaje}', $mensaje, $gui);
		print $gui;
	}

	function modal_mensaje_formulario_ajax($cliente_id) {
		$gui = file_get_contents("static/modules/egreso/modal_mensaje_formulario_ajax.html");
		$gui = str_replace('{cliente-cliente_id}', $cliente_id, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}

	function prepara_factura_afip($array_afip) {
		$gui = file_get_contents("static/modules/egreso/datos_factura_afip_ajax.html");

		$array_alicuotas = $array_afip[0];
		$array_afip['iva_0'] = $array_afip[0]['{sum_iva}'];
		$array_afip['iva_2_5'] = $array_afip[1]['{sum_iva}'];
		$array_afip['iva_5'] = $array_afip[2]['{sum_iva}'];
		$array_afip['iva_10_5'] = $array_afip[3]['{sum_iva}'];
		$array_afip['iva_21'] = $array_afip[4]['{sum_iva}'];
		$array_afip['iva_27'] = $array_afip[5]['{sum_iva}'];
		unset($array_afip[0],$array_afip[1],$array_afip[2],$array_afip[3],$array_afip[4],$array_afip[5]);
		$array_afip['punto_venta'] = str_pad($array_afip['punto_venta'], 4, '0', STR_PAD_LEFT);
		$array_afip['nueva_factura'] = str_pad($array_afip['nueva_factura'], 8, '0', STR_PAD_LEFT);
		$array_afip = $this->set_dict_array($array_afip);
		$gui = $this->render($array_afip, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}
}
?>