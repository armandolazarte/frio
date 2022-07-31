<?php


class CuentaCorrienteClienteCreditoView extends View {
	function consultar($cuentascorrientes_collection, $credito_collection, $montos_cuentacorriente, $importe_cuentacorrienteclientecredito, $obj_cliente) {
		$gui = file_get_contents("static/modules/cuentacorrienteclientecredito/consultar.html");
		$lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$tbl_cuentascorrientes = file_get_contents("static/modules/cuentacorrientecliente/tbl_cuentacorriente_array.html");
		$tbl_credito = file_get_contents("static/modules/cuentacorrienteclientecredito/tbl_credito.html");
		$tbl_credito = $this->render_regex_dict('TBL_CUENTACORRIENTECLIENTECREDITO', $tbl_credito, $credito_collection);

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

		$obj_vendedor = $obj_cliente->vendedor;
		$obj_flete = $obj_cliente->flete;
		$infocontacto_collection = $obj_cliente->infocontacto_collection;
		unset($obj_cliente->vendedor, $obj_cliente->flete, $obj_cliente->infocontacto_collection, $obj_vendedor->infocontacto_collection, $infocontacto_collection[2]);
		$obj_cliente = $this->set_dict($obj_cliente);
		$obj_vendedor = $this->set_dict($obj_vendedor);

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
									   '{cuentacorriente-class}'=>$class,
									   '{cuentacorriente-credito}'=>round($importe_cuentacorrienteclientecredito, 2));

		$tbl_cuentascorrientes = $this->render_regex_dict('TBL_CUENTACORRIENTE', $tbl_cuentascorrientes, $cuentascorrientes_collection);
		$lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $lst_infocontacto, $infocontacto_collection);
		$render = str_replace('{lst_infocontacto}', $lst_infocontacto, $gui);
		$render = str_replace('{tbl_credito}', $tbl_credito, $render);
		$render = str_replace('{tbl_cuentascorrientes}', $tbl_cuentascorrientes, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($obj_vendedor, $render);
		$render = $this->render($array_cuentacorriente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>