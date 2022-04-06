<?php


class NotaCreditoView extends View {
	function listar($notacredito_collection) {
		$gui = file_get_contents("static/modules/notacredito/listar.html");
		$tbl_notacredito_array = file_get_contents("static/modules/notacredito/tbl_notacredito_array.html");
		$tbl_notacredito_array = $this->render_regex_dict('TBL_NOTACREDITO', $tbl_notacredito_array, $notacredito_collection);

		$render = str_replace('{tbl_notacredito}', $tbl_notacredito_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar($notacredito_collection) {
		$gui = file_get_contents("static/modules/notacredito/buscar.html");
		$tbl_notacredito_array = file_get_contents("static/modules/notacredito/tbl_notacredito_array.html");
		$tbl_notacredito_array = $this->render_regex_dict('TBL_NOTACREDITO', $tbl_notacredito_array, $notacredito_collection);		

		$render = str_replace('{tbl_notacredito}', $tbl_notacredito_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($notacreditodetalle_collection, $obj_notacredito, $egresoafip, $obj_egreso, $notacredito_id, $flag_ccc) {
		$gui = file_get_contents("static/modules/notacredito/consultar.html");
		$tbl_notacreditodetalle_array = file_get_contents("static/modules/notacredito/tbl_notacreditodetalle_array.html");
		$tbl_notacreditodetalle_array = $this->render_regex_dict('TBL_NOTACREDITODETALLE', $tbl_notacreditodetalle_array, $notacreditodetalle_collection);

		if (is_object($obj_egreso)) {
			$tipofactura_cliente_nomenclatura = $obj_egreso->cliente->tipofactura->nomenclatura;
			$tipofactura_cliente_id = $obj_egreso->cliente->tipofactura->tipofactura_id;
			
			$obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
			$obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
			
			unset($obj_egreso->cliente->infocontacto_collection, $obj_egreso->vendedor->infocontacto_collection, $obj_egreso->cliente->flete->infocontacto_collection,
				  $obj_egreso->cliente->vendedor->infocontacto_collection, $obj_egreso->egresoentrega->flete->infocontacto_collection);
			
			$obj_egreso->egresocomision->valor_abonado = ($obj_egreso->egresocomision->valor_abonado != 0) ? $obj_egreso->egresocomision->valor_abonado : 0;
			$valor_abonado = round($obj_egreso->egresocomision->valor_abonado, 2);
			$obj_egreso->egresocomision->valor_abonado = $valor_abonado;
			$obj_egreso->div_facturarafip_display = (empty($egresoafip)) ? 'inline-block' : 'none';
			$obj_egreso->div_datos_facturarafip_display = (empty($egresoafip)) ? 'none' : 'inline-block';

		}

		$obj_notacredito->punto_venta = str_pad($obj_notacredito->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_notacredito->numero_factura = str_pad($obj_notacredito->numero_factura, 8, '0', STR_PAD_LEFT);
		$emitido_afip = $obj_notacredito->emitido_afip;
		
		if ($obj_notacredito->tipofactura->tipofactura_id == 6) {
			$obj_notacredito->btn_presentarafip_nc = 'none';
		} else {
			if ($obj_notacredito->emitido_afip == 1) {
				$obj_notacredito->btn_presentarafip_nc = 'none';
			} else {
				$obj_notacredito->btn_presentarafip_nc = 'block';
			}
		}

		if ($emitido_afip == 1) {
			$obj_notacredito->btn_anular_nc = 'none';
		} else {
			if ($flag_ccc == 1) {
				$obj_notacredito->btn_anular_nc = 'none';
			} else {
				$obj_notacredito->btn_anular_nc = 'block';
			}
		}

		$obj_egreso = $this->set_dict($obj_egreso);
		$obj_notacredito = $this->set_dict($obj_notacredito);
		if (!empty($egresoafip)) {
			$egresoafip = $egresoafip[0];
			$afip_punto_venta = str_pad($egresoafip['PUNTO_VENTA'], 4, '0', STR_PAD_LEFT);
			$afip_numero_factura = str_pad($egresoafip['NUMERO_FACTURA'], 8, '0', STR_PAD_LEFT);
			$egresoafip['PUNTO_VENTA'] = $afip_punto_venta;
			$egresoafip['NUMERO_FACTURA'] = $afip_numero_factura;
		} else {
			$egresoafip['PUNTO_VENTA'] = 0;
			$egresoafip['NUMERO_FACTURA'] = 0;
		}
		
		$egresoafip = $this->set_dict_array($egresoafip);
		$render = str_replace('{tbl_notacreditodetalle}', $tbl_notacreditodetalle_array, $gui);
		$render = str_replace('{cliente-tipofactura-nomenclatura}', $tipofactura_cliente_nomenclatura, $render);
		$render = str_replace('{cliente-tipofactura-id}', $tipofactura_cliente_id, $render);
		$render = $this->render($obj_notacredito, $render);
		$render = $this->render($obj_egreso, $render);
		$render = $this->render($egresoafip, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function prepara_notacredito_afip($array_afip) {
		$gui = file_get_contents("static/modules/notacredito/datos_notacredito_afip_ajax.html");
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