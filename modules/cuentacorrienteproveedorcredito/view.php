<?php


class CuentaCorrienteProveedorCreditoView extends View {
	function consultar($cuentascorrientes_collection, $credito_collection, $montos_cuentacorriente, $importe_cuentacorrienteproveedorcredito, $obj_proveedor) {
		$gui = file_get_contents("static/modules/cuentacorrienteproveedorcredito/consultar.html");
		$lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$tbl_cuentascorrientes = file_get_contents("static/modules/cuentacorrienteproveedor/tbl_cuentacorriente_array.html");
		$tbl_credito = file_get_contents("static/modules/cuentacorrienteproveedorcredito/tbl_credito.html");
		$tbl_credito = $this->render_regex_dict('TBL_CUENTACORRIENTEPROVEEDORCREDITO', $tbl_credito, $credito_collection);

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

		if ($obj_proveedor->documentotipo->denominacion == 'CUIL' OR $obj_proveedor->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_proveedor->documento, 0, 2);
			$cuil2 = substr($obj_proveedor->documento, 2, 8);
			$cuil3 = substr($obj_proveedor->documento, 10);
			$obj_proveedor->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		unset($obj_proveedor->infocontacto_collection, $infocontacto_collection[2]);
		$obj_proveedor = $this->set_dict($obj_proveedor);

		$deuda = (is_null($montos_cuentacorriente[0]['DEUDA'])) ? 0 : $montos_cuentacorriente[0]['DEUDA'];
		$ingreso = (is_null($montos_cuentacorriente[0]['INGRESO'])) ? 0 : $montos_cuentacorriente[0]['INGRESO'];
		$valor_cuentacorriente = round(($ingreso - $deuda), 2);
		$valor_cuentacorriente = (abs($valor_cuentacorriente) > 0 AND abs($valor_cuentacorriente) < 0.99) ? 0 : $valor_cuentacorriente;
		$class = ($valor_cuentacorriente >= 0) ? 'blue' : 'red';
		$icon = ($valor_cuentacorriente >= 0) ? 'up' : 'down';
		$msj = ($valor_cuentacorriente >= 0) ? 'Posee deuda' : 'Posee deuda';
		
		$array_cuentacorriente = array('{cuentacorriente-valor}'=>abs($valor_cuentacorriente),
									   '{cuentacorriente-icon}'=>$icon,
									   '{cuentacorriente-msj}'=>$msj,
									   '{cuentacorriente-class}'=>$class,
									   '{cuentacorriente-credito}'=>round($importe_cuentacorrienteproveedorcredito, 2));

		$tbl_cuentascorrientes = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentascorrientes, $cuentascorrientes_collection);
		$lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $lst_infocontacto, $infocontacto_collection);
		$render = str_replace('{lst_infocontacto}', $lst_infocontacto, $gui);
		$render = str_replace('{tbl_credito}', $tbl_credito, $render);
		$render = str_replace('{tbl_cuentascorrientes}', $tbl_cuentascorrientes, $render);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render($array_cuentacorriente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>