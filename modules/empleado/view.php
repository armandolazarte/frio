<?php


class EmpleadoView extends View {
	function panel() {
		$gui = file_get_contents("static/modules/empleado/panel.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function listar($empleado_collection) {
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$gui = file_get_contents("static/modules/empleado/listar_supervisor.html");
				$tbl_empleado_array = file_get_contents("static/modules/empleado/tbl_empleado_array_supervisor.html");
				break;
			default:
				$gui = file_get_contents("static/modules/empleado/listar.html");
				$tbl_empleado_array = file_get_contents("static/modules/empleado/tbl_empleado_array.html");
				break;
		}

		$tbl_empleado_array = $this->render_regex('TBL_EMPLEADO', $tbl_empleado_array, $empleado_collection);
		$render = str_replace('{tbl_empleado}', $tbl_empleado_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function listar_ocultos($empleado_collection) {
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			default:
				$gui = file_get_contents("static/modules/empleado/listar.html");
				$tbl_empleado_array = file_get_contents("static/modules/empleado/tbl_empleado_oculto_array.html");
				break;
		}

		$tbl_empleado_array = $this->render_regex('TBL_EMPLEADO', $tbl_empleado_array, $empleado_collection);
		$render = str_replace('{tbl_empleado}', $tbl_empleado_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($provincia_collection, $documentotipo_collection) {
		$gui = file_get_contents("static/modules/empleado/agregar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");

		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($provincia_collection, $documentotipo_collection, $obj_empleado) {
		$gui = file_get_contents("static/modules/empleado/editar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		
		$obj_empleado = $this->set_dict($obj_empleado);
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = $this->render($obj_empleado, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	/*
	function consultar($obj_empleado) {
		$gui = file_get_contents("static/modules/empleado/consultar.html");
		$gui_tbl_pago_egresocomision = file_get_contents("static/modules/empleado/tbl_pago_egresocomision.html");
		$gui_tbl_sum_importeventa_producto = file_get_contents("static/modules/empleado/tbl_sum_importeventa_producto.html");
		$gui_tbl_sum_cantidadventa_producto = file_get_contents("static/modules/empleado/tbl_sum_cantidadventa_producto.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		if ($obj_empleado->documentotipo->denominacion == 'CUIL' OR $obj_empleado->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_empleado->documento, 0, 2);
			$cuil2 = substr($obj_empleado->documento, 2, 8);
			$cuil3 = substr($obj_empleado->documento, 10);
			$obj_empleado->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_empleado->infocontacto_collection;
		unset($obj_empleado->infocontacto_collection);
		$obj_empleado = $this->set_dict($obj_empleado);
		$estadisticas = $this->set_dict_array($estadisticas);

		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$gui_tbl_pago_egresocomision = $this->render_regex_dict('TBL_PAGO_EGRESOCOMISION', $gui_tbl_pago_egresocomision, $egresocomision_collection);
		$gui_tbl_sum_importeventa_producto = $this->render_regex_dict('TBL_SUM_IMPORTEVENTA', $gui_tbl_sum_importeventa_producto, $sum_importe_producto);
		$gui_tbl_sum_cantidadventa_producto = $this->render_regex_dict('TBL_SUM_CANTIDADVENTA', $gui_tbl_sum_cantidadventa_producto, $sum_cantidad_producto);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{tbl_pago_egresocomision}', $gui_tbl_pago_egresocomision, $render);
		$render = str_replace('{tbl_sum_importe_venta_producto}', $gui_tbl_sum_importeventa_producto, $render);
		$render = str_replace('{tbl_sum_cantidad_venta_producto}', $gui_tbl_sum_cantidadventa_producto, $render);
		$render = $this->render($obj_empleado, $render);
		$render = $this->render($estadisticas, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function estadisticas($empleado_collection, $ventas_empleado,$ventas_empleado_tipo_factura, $top3_empleado_proveedor) {
		$gui = file_get_contents("static/modules/empleado/estadisticas.html");
		$gui_tbl_ventas_empleado = file_get_contents("static/modules/empleado/tbl_ventas_empleado.html");

		$gui_tbl_ventas_empleado_tipo_factura = file_get_contents("static/modules/empleado/tbl_ventas_empleado_tipo_factura.html");
		$gui_tbl_row_ventas_empleado_tipo_factura = file_get_contents("static/modules/empleado/tbl_row_ventas_empleado_tipo_factura.html");
		$gui_tbl_row_ventas_empleado_tipo_factura = $this->render_regex_dict('TBL_TOTAL_VENDEDOR_TIPO_FACTURA', $gui_tbl_row_ventas_vendedor_tipo_factura, $ventas_vendedor_tipo_factura);
		$gui_tbl_ventas_vendedor_tipo_factura = $this->get_regex('TBL_VENDEDOR_TIPO_FACTURA', $gui_tbl_ventas_vendedor_tipo_factura);
		$gui_tbl_ventas_vendedor_tipo_factura = str_replace('{tbl_row_vendedor_tipo_factura}', $gui_tbl_row_ventas_vendedor_tipo_factura, $gui_tbl_ventas_vendedor_tipo_factura);

		$gui_tbl_ventas_vendedor = $this->render_regex_dict('LST_VENTAS', $gui_tbl_ventas_vendedor, $ventas_vendedor);
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor_array.html");
		$gui_slt_vendedor = $this->render_regex_dict('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$gui_carga_barchart_venta_vendedor = file_get_contents("static/modules/vendedor/carga_barchart_venta_vendedor.html");
		$gui_carga_barchart_venta_vendedor = $this->render_regex_dict('BARCHART_VENTA', $gui_carga_barchart_venta_vendedor, $ventas_vendedor);
		$gui_top3_vendedor_proveedor = file_get_contents("static/modules/vendedor/tbl_top3_vendedor_proveedor.html");

		$periodo_actual = date('Ym');
		$render_top3_vendedor_proveedor = '';
        $cod_tbl_vendedor_proveedor = $this->get_regex('TBL_TOP3_VENDEDOR_PROVEEDOR', $gui_top3_vendedor_proveedor);

        foreach ($top3_vendedor_proveedor as $dict_vendedor) {
            $totales_proveedores = $dict_vendedor['ARRAY_TOTALES'];
            unset($dict_vendedor['ARRAY_TOTALES']);
            $tbl_vendedor = str_replace("{VENDEDOR}", $dict_vendedor['{VENDEDOR}'], $cod_tbl_vendedor_proveedor);
			$gui_top3_vendedor_proveedor_filas = file_get_contents("static/modules/vendedor/tbl_row_top3_vendedor_proveedor.html");
            $tbl_row_top3 = $this->get_regex('TBL_TOTAL_VENDEDOR_PROVEEDOR', $gui_top3_vendedor_proveedor_filas);
            $render_row_top3 = '';
            foreach($totales_proveedores as $dict) $render_row_top3 .= str_replace(array_keys($dict), array_values($dict), $tbl_row_top3);
            $tbl_vendedor = str_replace('{tbl_row_top3_vendedor_proveedor}', $render_row_top3, $tbl_vendedor);
            $render_top3_vendedor_proveedor .= $tbl_vendedor;
        }

	    $gui_top3_vendedor_proveedor = str_replace($cod_tbl_vendedor_proveedor, $render_top3_vendedor_proveedor, $gui_top3_vendedor_proveedor);
		$render = str_replace('{carga_barchart_venta_vendedor}', $gui_carga_barchart_venta_vendedor, $gui);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{tbl_ventas_vendedor}', $gui_tbl_ventas_vendedor, $render);
		$render = str_replace('{tbl_ventas_vendedor_tipo_factura}', $gui_tbl_ventas_vendedor_tipo_factura, $render);
		$render = str_replace('{periodo_actual}', $periodo_actual, $render);
		$render = str_replace('{top3_vendedor_proveedor}', $gui_top3_vendedor_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function filtro_estadisticas($vendedor_collection, $ventas_vendedor,$ventas_vendedor_tipo_factura,$array_fechas, $top3_vendedor_proveedor) {
		$gui = file_get_contents("static/modules/vendedor/filtro_estadisticas.html");

		$gui_tbl_ventas_vendedor_tipo_factura = file_get_contents("static/modules/vendedor/tbl_ventas_vendedor_tipo_factura.html");
		$gui_tbl_row_ventas_vendedor_tipo_factura = file_get_contents("static/modules/vendedor/tbl_row_ventas_vendedor_tipo_factura.html");
		$gui_tbl_row_ventas_vendedor_tipo_factura = $this->render_regex_dict('TBL_TOTAL_VENDEDOR_TIPO_FACTURA', $gui_tbl_row_ventas_vendedor_tipo_factura, $ventas_vendedor_tipo_factura);
		$gui_tbl_ventas_vendedor_tipo_factura = $this->get_regex('TBL_VENDEDOR_TIPO_FACTURA', $gui_tbl_ventas_vendedor_tipo_factura);
		$gui_tbl_ventas_vendedor_tipo_factura = str_replace('{tbl_row_vendedor_tipo_factura}', $gui_tbl_row_ventas_vendedor_tipo_factura, $gui_tbl_ventas_vendedor_tipo_factura);

		$gui_tbl_ventas_vendedor = file_get_contents("static/modules/vendedor/tbl_ventas_vendedor.html");
		$gui_tbl_ventas_vendedor = $this->render_regex_dict('LST_VENTAS', $gui_tbl_ventas_vendedor, $ventas_vendedor);
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor_array.html");
		$gui_slt_vendedor = $this->render_regex_dict('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$gui_carga_barchart_venta_vendedor = file_get_contents("static/modules/vendedor/carga_barchart_venta_vendedor.html");
		$gui_carga_barchart_venta_vendedor = $this->render_regex_dict('BARCHART_VENTA', $gui_carga_barchart_venta_vendedor, $ventas_vendedor);
		$gui_top3_vendedor_proveedor = file_get_contents("static/modules/vendedor/tbl_top3_vendedor_proveedor.html");

		$periodo_actual = date('Ym');
		$render_top3_vendedor_proveedor = '';
        $cod_tbl_vendedor_proveedor = $this->get_regex('TBL_TOP3_VENDEDOR_PROVEEDOR', $gui_top3_vendedor_proveedor);
        foreach ($top3_vendedor_proveedor as $dict_vendedor) {
            $totales_proveedores = $dict_vendedor['ARRAY_TOTALES'];
            unset($dict_vendedor['ARRAY_TOTALES']);
            $tbl_vendedor = str_replace("{VENDEDOR}", $dict_vendedor['{VENDEDOR}'], $cod_tbl_vendedor_proveedor);
			$gui_top3_vendedor_proveedor_filas = file_get_contents("static/modules/vendedor/tbl_row_top3_vendedor_proveedor.html");
            $tbl_row_top3 = $this->get_regex('TBL_TOTAL_VENDEDOR_PROVEEDOR', $gui_top3_vendedor_proveedor_filas);
            $render_row_top3 = '';
            foreach($totales_proveedores as $dict) $render_row_top3 .= str_replace(array_keys($dict), array_values($dict), $tbl_row_top3);
            $tbl_vendedor = str_replace('{tbl_row_top3_vendedor_proveedor}', $render_row_top3, $tbl_vendedor);
            $render_top3_vendedor_proveedor .= $tbl_vendedor;
        }

    	$gui_top3_vendedor_proveedor = str_replace($cod_tbl_vendedor_proveedor, $render_top3_vendedor_proveedor, $gui_top3_vendedor_proveedor);
		$render = str_replace('{carga_barchart_venta_vendedor}', $gui_carga_barchart_venta_vendedor, $gui);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{tbl_ventas_vendedor}', $gui_tbl_ventas_vendedor, $render);
		$render = str_replace('{tbl_ventas_vendedor_tipo_factura}', $gui_tbl_ventas_vendedor_tipo_factura, $render);
		$render = str_replace('{periodo_actual}', $periodo_actual, $render);
		$render = str_replace('{top3_vendedor_proveedor}', $gui_top3_vendedor_proveedor, $render);
		$render = $this->render($array_fechas, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function ventas_vendedor($egreso_pendiente_collection, $egreso_total_collection, $ventas_per_actual_collection,
							 $array_busqueda, $obj_vendedor, $array_totales) {
		$gui = file_get_contents("static/modules/vendedor/ventas_vendedor.html");
		$tbl_egreso_pendiente_array = file_get_contents("static/modules/vendedor/tbl_chk_egreso_pendiente_array.html");
		$tbl_egreso_pendiente_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_pendiente_array, $egreso_pendiente_collection);
		$tbl_egreso_total_array = file_get_contents("static/modules/vendedor/tbl_egreso_total_array.html");
		$tbl_egreso_total_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_total_array, $egreso_total_collection);
		$lst_carga_bardonuts_periodo_array = file_get_contents("static/modules/vendedor/lst_carga_bardonuts_periodo.html");
		$lst_carga_bardonuts_cantidad_array = file_get_contents("static/modules/vendedor/lst_carga_bardonuts_cantidad.html");
		$lst_carga_bardonuts_periodo_array = $this->render_regex_dict('LST_PERIODO', $lst_carga_bardonuts_periodo_array,
											 $ventas_per_actual_collection);
		$lst_carga_bardonuts_cantidad_array = $this->render_regex_dict('LST_CANTIDAD', $lst_carga_bardonuts_cantidad_array,
											 $ventas_per_actual_collection);

		$infocontacto_collection = $obj_vendedor->infocontacto_collection;
		unset($obj_vendedor->infocontacto_collection);
		$obj_vendedor = $this->set_dict($obj_vendedor);

		$array_totales['{comision_fecha_desde}'] = $array_totales['{fecha_desde}'];
		$array_totales['{comision_fecha_hasta}'] = $array_totales['{fecha_hasta}'];
		$array_totales['{fecha_desde}'] = $this->reacomodar_fecha($array_totales['{fecha_desde}']);
		$array_totales['{fecha_hasta}'] = $this->reacomodar_fecha($array_totales['{fecha_hasta}']);

		$render = str_replace('{tbl_egreso_pendiente}', $tbl_egreso_pendiente_array, $gui);
		$render = str_replace('{tbl_egreso_total}', $tbl_egreso_total_array, $render);
		$render = str_replace('{lst_carga_bardonuts_venta_periodo_producto}', $lst_carga_bardonuts_periodo_array, $render);
		$render = str_replace('{lst_carga_bardonuts_venta_cantidad_producto}', $lst_carga_bardonuts_cantidad_array, $render);
		$render = str_replace('{fecha_sys}', date('Y-m-d'), $render);
		$render = $this->render($obj_vendedor, $render);
		$render = $this->render($array_totales, $render);
		$render = $this->render($array_busqueda, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function formulario_abonar_egreso_ajax($obj_egreso) {
		$gui = file_get_contents("static/modules/vendedor/formulario_abonar_egreso_ajax.html");

		$obj_cliente = $obj_egreso->cliente;
		$obj_vendedor = $obj_egreso->vendedor;

		unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->vendedor, $obj_cliente->infocontacto_collection,
			  $obj_vendedor->infocontacto_collection);

		$comision = $obj_vendedor->comision;
		$obj_egreso->importe_total = $obj_egreso->importe_total - $obj_egreso->nc_importe_total;
		$valor_comision = round(($obj_egreso->importe_total * $comision / 100), 2);

		$obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_egreso->valor_a_abonar = round(($obj_egreso->importe_total * $obj_egreso->egresocomision->valor_comision / 100),2);
		$obj_vendedor->valor_comision = $valor_comision;
		$obj_vendedor = $this->set_dict($obj_vendedor);
		$obj_cliente = $this->set_dict($obj_cliente);
		$obj_egreso = $this->set_dict($obj_egreso);

		$render = $this->render($obj_vendedor, $gui);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render($obj_egreso, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}
	*/
}
?>
