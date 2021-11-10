<?php


class CuentaCorrienteProveedorView extends View {
	function panel($cuentacorriente_collection, $totales_array, $proveedor_collection) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/panel.html");
		$tbl_cuentacorriente_array = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_array.html");
		$gui_slt_proveedor = file_get_contents("static/common/slt_proveedor.html");

		$balance_temp = 0;
		foreach ($proveedor_collection as $proveedor) unset($proveedor->infocontacto_collection);
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
			$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
			$cuenta = round(($ingreso - $deuda),2);
			$class = ($cuenta >= 0) ? 'info' : 'danger';
			$cuenta = abs($cuenta);
			$balance_temp = $balance_temp + $cuenta;
			$cuentacorriente_collection[$clave]['CUENTA'] = ($cuenta > 0.5) ? $cuenta : 0;
			$cuentacorriente_collection[$clave]['CLASS'] = $class;

		}

		$totales_array = $totales_array[0];
		$totales_array['BALANCE'] = abs(round($balance_temp,2));
		$totales_array['BALANCE'] = ($totales_array['BALANCE'] > 0.5) ? $totales_array['BALANCE'] : 0;
		$totales_array['CLASS_BALANCE'] = ($totales_array['TDEUDA'] <= $totales_array['TINGRESO']) ? "green" : "red";
		$totales_array['TXT_BALANCE'] = ($totales_array['TDEUDA'] <= $totales_array['TINGRESO']) ? "positivo" : "negativo";
		$totales_array = $this->set_dict_array($totales_array);

		$gui_slt_proveedor = $this->render_regex('SLT_PROVEEDOR', $gui_slt_proveedor, $proveedor_collection);
		$tbl_cuentacorriente_array = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentacorriente_array, $cuentacorriente_collection);		
		$render = str_replace('{tbl_cuentacorriente}', $tbl_cuentacorriente_array, $gui);
		$render = str_replace('{slt_proveedor}', $gui_slt_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$render = $this->render($totales_array, $render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar($cuentacorriente_collection, $proveedor_collection, $argumento) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/buscar.html");
		$tbl_cuentacorriente_array = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_buscar_cuentacorriente_array.html");
		$gui_slt_proveedor = file_get_contents("static/common/slt_proveedor.html");

		$cant_total = 0;
		foreach ($proveedor_collection as $proveedor) unset($proveedor->infocontacto_collection);
		foreach ($cuentacorriente_collection as $clave=>$valor) {
            $balance = abs($valor['BALANCE']);
            $cant_total = $cant_total + $balance;
            $cuentacorriente_collection[$clave]['BALANCE'] =  $balance;
        } 

		$gui_slt_proveedor = $this->render_regex('SLT_PROVEEDOR', $gui_slt_proveedor, $proveedor_collection);
		$tbl_cuentacorriente_array = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentacorriente_array, $cuentacorriente_collection);		
		$render = str_replace('{tbl_cuentacorriente}', $tbl_cuentacorriente_array, $gui);
		$render = str_replace('{slt_proveedor}', $gui_slt_proveedor, $render);
		$render = str_replace('{cant_total}', $cant_total, $render);
		$render = str_replace('{argumento}', $argumento, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function consultar($cuentascorrientes_collection, $cuentacorriente_collection, $montos_cuentacorriente, $obj_proveedor) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/consultar.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$tbl_cuentascorrientes_array = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_array.html");

		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_expandido_array_supervisor.html");
				break;
			default:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_expandido_array.html");
				break;
		}

		if ($obj_proveedor->documentotipo->denominacion == 'CUIL' OR $obj_proveedor->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_proveedor->documento, 0, 2);
			$cuil2 = substr($obj_proveedor->documento, 2, 8);
			$cuil3 = substr($obj_proveedor->documento, 10);
			$obj_proveedor->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		foreach ($cuentascorrientes_collection as $clave=>$valor) {
			$deuda = (is_null($valor['DEUDA'])) ? 0 : round($valor['DEUDA'],2);
			$ingreso = (is_null($valor['INGRESO'])) ? 0 : round($valor['INGRESO'],2);
			$cuenta = round(($ingreso - $deuda),2);
			$class = ($cuenta >= 0) ? 'info' : 'danger';
			$cuenta = abs($cuenta);
			$cuentascorrientes_collection[$clave]['CUENTA'] = ($cuenta > 0.5) ? $cuenta : 0;
			$cuentascorrientes_collection[$clave]['CLASS'] = $class;
		}

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		unset($obj_proveedor->infocontacto_collection);	
		$obj_proveedor = $this->set_dict($obj_proveedor);

		$deuda = (is_null($montos_cuentacorriente[0]['DEUDA'])) ? 0 : $montos_cuentacorriente[0]['DEUDA'];
		$ingreso = (is_null($montos_cuentacorriente[0]['INGRESO'])) ? 0 : $montos_cuentacorriente[0]['INGRESO'];
		$valor_cuentacorriente = round(($ingreso - $deuda), 2);
		$valor_cuentacorriente = (abs($valor_cuentacorriente) > 0 AND abs($valor_cuentacorriente) < 0.99) ? 0 : $valor_cuentacorriente;
		$class = ($valor_cuentacorriente >= 0) ? 'blue' : 'red';
		$icon = ($valor_cuentacorriente >= 0) ? 'up' : 'down';
		$msj = ($valor_cuentacorriente >= 0) ? 'No posee deuda' : 'Posee deuda';
		
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
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render($array_cuentacorriente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function listar_cuentas($cuentacorriente_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/listar_cuentas.html");
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_corto_array_supervisor.html");
				break;
			default:
				$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_corto_array.html");
				break;
		}

		unset($obj_proveedor->infocontacto_collection);	
		
		$obj_proveedor = $this->set_dict($obj_proveedor);
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$render = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $gui);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_listado_movimientos_cuentacorriente_ajax($cuentacorriente_collection) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/movimientos_cuentacorrienteproveedor_ajax.html");		
		$gui_tbl_cuentacorriente = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_movimientos_cuentacorriente_corto_array.html");
		
		$cuentacorriente_collection = (is_array($cuentacorriente_collection)) ? $cuentacorriente_collection : array();
		$referencia = (!empty($cuentacorriente_collection)) ? $cuentacorriente_collection[0]['REFERENCIA'] : ""; 
		$balance_color = (!empty($cuentacorriente_collection)) ? $cuentacorriente_collection[0]['BCOLOR'] : ""; 
		$balance = (!empty($cuentacorriente_collection)) ? $cuentacorriente_collection[0]['BALANCE'] : ""; 
		if (!empty($cuentacorriente_collection)) $cuentacorriente_collection[0]['BTN_DISPLAY'] = "none"; 
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$gui = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $gui);
		$gui = str_replace('{ingreso-referencia}', $referencia, $gui);
		$gui = str_replace('{cuentacorriente-balance}', $balance, $gui);
		$gui = str_replace('{balance-class}', $balance_color, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}
	
	function traer_formulario_abonar_ajax($ingresotipopago_collection, $obj_cuentacorrienteproveedor, $obj_proveedor, $balance) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/abonar_cuentacorrienteproveedor_ajax.html");
		$gui_slt_ingresotipopago = file_get_contents("static/modules/ingresotipopago/slt_ingresotipopago.html");
		$gui_slt_ingresotipopago = $this->render_regex('SLT_INGRESOTIPOPAGO', $gui_slt_ingresotipopago, $ingresotipopago_collection);

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
		unset($obj_proveedor->infocontacto_collection);
		$obj_cuentacorrienteproveedor = $this->set_dict($obj_cuentacorrienteproveedor);
		$obj_proveedor = $this->set_dict($obj_proveedor);
		$gui = str_replace('{fecha_sys}', $fecha_sys, $gui);
		$gui = str_replace('{balance}', $balance, $gui);
		$gui = str_replace('{slt_ingresotipopago}', $gui_slt_ingresotipopago, $gui);
		$gui = $this->render($obj_proveedor, $gui);
		$gui = $this->render($obj_cuentacorrienteproveedor, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}

	function traer_chequeproveedordetalle_ajax($obj_chequeproveedordetalle, $proveedor_id) {
		if ($obj_chequeproveedordetalle->estado == 1 AND is_null($obj_chequeproveedordetalle->fecha_pago)) {
			$gui = file_get_contents("static/modules/cuentacorrienteproveedor/form_abonar_chequeproveedordetalle_ajax.html");
			$gui = str_replace('{proveedor-proveedor_id}', $proveedor_id, $gui);
		} else {
			$gui = file_get_contents("static/modules/cuentacorrienteproveedor/traer_chequeproveedordetalle_ajax.html");

			$estado_denominacion = ($obj_chequeproveedordetalle->estado == 1) ? 'PENDIENTE' : '';
			$estado_denominacion = ($obj_chequeproveedordetalle->estado == 2) ? 'ABONADO' : $estado_denominacion;
			$estado_denominacion = ($obj_chequeproveedordetalle->estado == 3) ? 'ANULADO' : $estado_denominacion;
			$obj_chequeproveedordetalle->estado_denominacion = $estado_denominacion;
		}

		$obj_chequeproveedordetalle = $this->set_dict($obj_chequeproveedordetalle);
		$gui = $this->render($obj_chequeproveedordetalle, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		print $gui;
	}

	function traer_transferenciaproveedordetalle_ajax($obj_transferenciaproveedordetalle) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedor/traer_transferenciaproveedordetalle_ajax.html");
		$estado_denominacion = 'ABONADO';
		$obj_transferenciaproveedordetalle->estado_denominacion = $estado_denominacion;
		$obj_transferenciaproveedordetalle = $this->set_dict($obj_transferenciaproveedordetalle);
		$gui = $this->render($obj_transferenciaproveedordetalle, $gui);
		print $gui;
	}
}
?>