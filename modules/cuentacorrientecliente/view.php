<?php


class CuentaCorrienteClienteView extends View {
	function panel($cuentacorriente_collection, $totales_array, $vendedor_collection) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/panel.html");
		$tbl_cuentacorriente_array = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_array.html");
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor.html");

		foreach ($vendedor_collection as $vendedor) unset($vendedor->infocontacto_collection);
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
			$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
			$cuenta = round(($ingreso - $deuda),2);
			$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
			$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
			$class = ($cuenta >= 0) ? 'info' : 'danger';
			$cuentacorriente_collection[$clave]['CUENTA'] = abs($cuenta);
			$cuentacorriente_collection[$clave]['CLASS'] = $class;
		}

		$totales_array = $totales_array[0];
		$totales_array['BALANCE'] = abs(round(($totales_array['TDEUDA'] - $totales_array['TINGRESO']),2));
		$totales_array['BALANCE'] = ($totales_array['BALANCE'] > 0.5) ? $totales_array['BALANCE'] : 0;
		$totales_array['CLASS_BALANCE'] = ($totales_array['TDEUDA'] <= $totales_array['TINGRESO']) ? "green" : "red";
		$totales_array['TXT_BALANCE'] = ($totales_array['TDEUDA'] <= $totales_array['TINGRESO']) ? "positivo" : "negativo";
		$totales_array = $this->set_dict_array($totales_array);

		$gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$tbl_cuentacorriente_array = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentacorriente_array, $cuentacorriente_collection);		
		$render = str_replace('{tbl_cuentacorriente}', $tbl_cuentacorriente_array, $gui);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = $this->render($totales_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function vdr_panel($cuentacorriente_collection, $totales_array) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/vdr_panel.html");
		$tbl_cuentacorriente_array = file_get_contents("static/modules/cuentacorrientecliente/vdr_tbl_cuentacorriente_array.html");
		
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
			$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
			$cuenta = round(($ingreso - $deuda),2);
			$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
			$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
			$class = ($cuenta >= 0) ? 'info' : 'danger';
			$cuentacorriente_collection[$clave]['CUENTA'] = abs($cuenta);
			$cuentacorriente_collection[$clave]['CLASS'] = $class;
		}

		$totales_array = $totales_array[0];
		$totales_array['BALANCE'] = abs(round(($totales_array['TDEUDA'] - $totales_array['TINGRESO']),2));
		$totales_array['BALANCE'] = ($totales_array['BALANCE'] > 0.5) ? $totales_array['BALANCE'] : 0;
		$totales_array['CLASS_BALANCE'] = ($totales_array['TDEUDA'] <= $totales_array['TINGRESO']) ? "green" : "red";
		$totales_array['TXT_BALANCE'] = ($totales_array['TDEUDA'] <= $totales_array['TINGRESO']) ? "positivo" : "negativo";
		$totales_array = $this->set_dict_array($totales_array);

		$tbl_cuentacorriente_array = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentacorriente_array, $cuentacorriente_collection);		
		$render = str_replace('{tbl_cuentacorriente}', $tbl_cuentacorriente_array, $gui);
		$render = $this->render($totales_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar($cuentacorriente_collection, $vendedor_collection, $argumento) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/buscar.html");
		$tbl_cuentacorriente_array = file_get_contents("static/modules/cuentacorrientecliente/tbl_buscar_cuentacorriente_array.html");
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor.html");

		$cant_total = 0;
		foreach ($vendedor_collection as $vendedor) unset($vendedor->infocontacto_collection);
		foreach ($cuentacorriente_collection as $clave=>$valor) {
            $balance = abs($valor['BALANCE']);
            $cant_total = $cant_total + $balance;
            $cuentacorriente_collection[$clave]['BALANCE'] =  $balance;
        } 

		$gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$tbl_cuentacorriente_array = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentacorriente_array, $cuentacorriente_collection);		
		$render = str_replace('{tbl_cuentacorriente}', $tbl_cuentacorriente_array, $gui);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{cant_total}', $cant_total, $render);
		$render = str_replace('{argumento}', $argumento, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($cuentascorrientes_collection, $cuentacorriente_collection, $montos_cuentacorriente, $obj_cliente) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/consultar.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$tbl_cuentascorrientes_array = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_array.html");
		
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_expandido_array_operador.html");
				break;
			case 2:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_expandido_array_supervisor.html");
				break;
			default:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_expandido_array.html");
				break;
		}

		foreach ($cuentascorrientes_collection as $clave=>$valor) {
			$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
			$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
			$cuenta = round(($ingreso - $deuda),2);
			$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
			$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
			$class = ($cuenta >= 0) ? 'info' : 'danger';
			$cuentascorrientes_collection[$clave]['CUENTA'] = abs($cuenta);
			$cuentascorrientes_collection[$clave]['CLASS'] = $class;
		}
		
		$obj_cliente->codigo = str_pad($obj_cliente->cliente_id, 5, '0', STR_PAD_LEFT);
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

		$deuda = (is_null($montos_cuentacorriente[0]['DEUDA'])) ? 0 : $montos_cuentacorriente[0]['DEUDA'];
		$ingreso = (is_null($montos_cuentacorriente[0]['INGRESO'])) ? 0 : $montos_cuentacorriente[0]['INGRESO'];
		$valor_cuentacorriente = round(($ingreso - $deuda), 2);
		$valor_cuentacorriente = (abs($valor_cuentacorriente) > 0 AND abs($valor_cuentacorriente) < 0.99) ? 0 : $valor_cuentacorriente;
		$class = ($valor_cuentacorriente >= 0) ? 'blue' : 'red';
		$icon = ($valor_cuentacorriente >= 0) ? 'up' : 'down';
		$msj = ($valor_cuentacorriente >= 0) ? 'no posee deuda!' : 'posee deuda!';
		
		$array_cuentacorriente = array('{cuentacorriente-valor}'=>abs($valor_cuentacorriente),
									   '{cuentacorriente-icon}'=>$icon,
									   '{cuentacorriente-msj}'=>$msj,
									   '{cuentacorriente-class}'=>$class);

		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$tbl_cuentascorrientes_array = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentascorrientes_array, $cuentascorrientes_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $render);
		$render = str_replace('{tbl_cuentascorrientes}', $tbl_cuentascorrientes_array, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($array_cuentacorriente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function vdr_consultar($cuentacorriente_collection, $montos_cuentacorriente, $obj_cliente) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/vdr_consultar.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/vdr_tbl_cuentacorriente_expandido_array.html");
		
		foreach ($cuentascorrientes_collection as $clave=>$valor) {
			$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
			$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
			$cuenta = round(($ingreso - $deuda),2);
			$cuenta = ($cuenta > 0 AND $cuenta < 1) ? 0 : $cuenta;
			$cuenta = ($cuenta > -1 AND $cuenta < 0) ? 0 : $cuenta;
			$class = ($cuenta >= 0) ? 'info' : 'danger';
			$cuentascorrientes_collection[$clave]['CUENTA'] = abs($cuenta);
			$cuentascorrientes_collection[$clave]['CLASS'] = $class;
		}
		
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

		$deuda = (is_null($montos_cuentacorriente[0]['DEUDA'])) ? 0 : $montos_cuentacorriente[0]['DEUDA'];
		$ingreso = (is_null($montos_cuentacorriente[0]['INGRESO'])) ? 0 : $montos_cuentacorriente[0]['INGRESO'];
		$valor_cuentacorriente = round(($ingreso - $deuda), 2);
		$valor_cuentacorriente = (abs($valor_cuentacorriente) > 0 AND abs($valor_cuentacorriente) < 0.99) ? 0 : $valor_cuentacorriente;
		$class = ($valor_cuentacorriente >= 0) ? 'blue' : 'red';
		$icon = ($valor_cuentacorriente >= 0) ? 'up' : 'down';
		$msj = ($valor_cuentacorriente >= 0) ? 'no posee deuda!' : 'posee deuda!';
		
		$array_cuentacorriente = array('{cuentacorriente-valor}'=>abs($valor_cuentacorriente),
									   '{cuentacorriente-icon}'=>$icon,
									   '{cuentacorriente-msj}'=>$msj,
									   '{cuentacorriente-class}'=>$class);

		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $render);
		$render = str_replace('{tbl_cuentascorrientes}', $tbl_cuentascorrientes_array, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($array_cuentacorriente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function listar_cuentas($cuentacorriente_collection, $obj_cliente) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/listar_cuentas.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 1:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_corto_array_operador.html");
				break;
			case 2:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_corto_array_supervisor.html");
				break;
			default:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_corto_array.html");
				break;
		}

		unset($obj_cliente->infocontacto_collection, $obj_cliente->vendedor->infocontacto_collection, 
			  $obj_cliente->vendedor->frecuenciaventa, $obj_cliente->flete->infocontacto_collection);	
		
		$obj_cliente = $this->set_dict($obj_cliente);
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$render = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $gui);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_formulario_abonar_ajax($cobrador_collection, $obj_cuentacorrientecliente, $obj_cliente, $balance) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/abonar_cuentacorrientecliente_ajax.html");		
		$gui_slt_cobrador = file_get_contents("static/modules/cuentacorrientecliente/slt_cobrador.html");
		$gui_slt_cobrador = $this->render_regex('SLT_COBRADOR', $gui_slt_cobrador, $cobrador_collection);

		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$gui = str_replace('{readonly-fecha}', 'readonly', $gui);
				break;
			case 3:
				$gui = str_replace('{readonly-fecha}', '', $gui);
				break;
			case 9:
				$gui = str_replace('{readonly-fecha}', '', $gui);
				break;
			default:
				$gui = str_replace('{readonly-fecha}', 'readonly', $gui);
				break;
		}

		$balance = abs($balance[0]['BALANCE']);
		$fecha_sys = date('Y-m-d');
		unset($obj_cliente->infocontacto_collection, $obj_cliente->flete, $obj_cliente->vendedor);
		$obj_cuentacorrientecliente = $this->set_dict($obj_cuentacorrientecliente);
		$obj_cliente = $this->set_dict($obj_cliente);
		$render = str_replace('{fecha_sys}', $fecha_sys, $gui);
		$render = str_replace('{balance}', $balance, $render);
		$render = str_replace('{slt_cobrador}', $gui_slt_cobrador, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($obj_cuentacorrientecliente, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}

	function traer_listado_movimientos_cuentacorriente_ajax($cuentacorriente_collection) {
		$gui = file_get_contents("static/modules/cuentacorrientecliente/movimientos_cuentacorrientecliente_ajax.html");		
		$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrientecliente/tbl_movimientos_cuentacorriente_corto_array.html");
		
		$cuentacorriente_collection = (is_array($cuentacorriente_collection)) ? $cuentacorriente_collection : array();
		$referencia = (!empty($cuentacorriente_collection)) ? $cuentacorriente_collection[0]['REFERENCIA'] : ""; 
		$balance_color = (!empty($cuentacorriente_collection)) ? $cuentacorriente_collection[0]['BCOLOR'] : ""; 
		$balance = (!empty($cuentacorriente_collection)) ? $cuentacorriente_collection[0]['BALANCE'] : ""; 
		if (!empty($cuentacorriente_collection)) $cuentacorriente_collection[0]['BTN_DISPLAY'] = "none"; 
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$gui = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $gui);
		$gui = str_replace('{egreso-referencia}', $referencia, $gui);
		$gui = str_replace('{cuentacorriente-balance}', $balance, $gui);
		$gui = str_replace('{balance-class}', $balance_color, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}
}
?>