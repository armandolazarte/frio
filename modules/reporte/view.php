<?php


class ReporteView extends View {

	function home() {
		$gui = file_get_contents("static/modules/reporte/home.html");

		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function panel($stock_collection, $array_totales, $sum_importe_producto, $sum_cantidad_producto, $sum_semestre_cuentas,
				   $vendedor_collection, $gasto_collection, $cuentacorrienteproveedor_collection) {
		$gui = file_get_contents("static/modules/reporte/panel.html");
		$tbl_cuentacorrienteproveedor = file_get_contents("static/modules/reporte/tbl_cuentacorrienteproveedor.html");
		$tbl_sum_importe_producto = file_get_contents("static/modules/reporte/tbl_sum_importe_producto.html");
		$tbl_sum_cantidad_producto = file_get_contents("static/modules/reporte/tbl_sum_cantidad_producto.html");
		$barchart_periodo = file_get_contents("static/modules/reporte/barchart_periodo.html");
		$barchart_sum_semestre_cc = file_get_contents("static/modules/reporte/barchart_sum_semestre_cc.html");
		$barchart_sum_semestre_cont = file_get_contents("static/modules/reporte/barchart_sum_semestre_cont.html");
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor_array.html");
		$gui_lbl_piechart_gasto = file_get_contents("static/modules/reporte/lbl_piechart_gasto.html");
		$gui_valores_piechart_gasto = file_get_contents("static/modules/reporte/valores_piechart_gasto.html");

		$gui_slt_vendedor = $this->render_regex_dict('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
		$tbl_cuentacorrienteproveedor = $this->render_regex_dict('TBL_CUENTACORRIENTEPROVEEDOR', $tbl_cuentacorrienteproveedor, $cuentacorrienteproveedor_collection);
		$gui_lbl_piechart_gasto = $this->render_regex_dict('LBL_PIECHART_GASTO', $gui_lbl_piechart_gasto, $gasto_collection);
		$gui_valores_piechart_gasto = $this->render_regex_dict('VALORES_PIECHART_GASTO', $gui_valores_piechart_gasto, $gasto_collection);
		$gui_lbl_piechart_gasto = str_replace('<!--LBL_PIECHART_GASTO-->', '', $gui_lbl_piechart_gasto);
		$gui_valores_piechart_gasto = str_replace('<!--VALORES_PIECHART_GASTO-->', '', $gui_valores_piechart_gasto);


		if (is_array($sum_importe_producto) AND !empty($sum_importe_producto)) {
			$sum_importe_producto = $this->order_collection_array($sum_importe_producto, 'IMPORTE', SORT_DESC);
			$i = 0;
			foreach ($sum_importe_producto as $clave=>$valor) {
				if ($i > 4) unset($sum_importe_producto[$clave]);
				$i = $i + 1;
			}
		}

		if (is_array($sum_cantidad_producto) AND !empty($sum_cantidad_producto)) {
			$sum_cantidad_producto = $this->order_collection_array($sum_cantidad_producto, 'CANTIDAD', SORT_DESC);
			$j = 0;
			foreach ($sum_cantidad_producto as $clave=>$valor) {
				if ($j > 4) unset($sum_cantidad_producto[$clave]);
				$j = $j + 1;
			}
		}

		$tbl_sum_importe_producto = $this->render_regex_dict('TBL_SUM_IMPORTE_PRODUCTO', $tbl_sum_importe_producto, $sum_importe_producto);
		$tbl_sum_cantidad_producto = $this->render_regex_dict('TBL_SUM_CANTIDAD_PRODUCTO', $tbl_sum_cantidad_producto, $sum_cantidad_producto);

		$array_periodos = array();
		$array_semestre_sum_cc = array();
		$array_semestre_sum_cont = array();
		$sum_semestre_cuentas = (is_array($sum_semestre_cuentas) AND !empty($sum_semestre_cuentas)) ? $sum_semestre_cuentas : array();
		foreach ($sum_semestre_cuentas as $clave=>$valor) {
			$array_temp_periodos = array('PERIODO'=>$valor['PERIODO']);
			$array_periodos[] = $array_temp_periodos;
			$array_temp_cc = array('SUMCC'=>$valor['SUMCC']);
			$array_semestre_sum_cc[] = $array_temp_cc;
			$array_temp_cont = array('SUMCONT'=>$valor['SUMCONT']);
			$array_semestre_sum_cont[] = $array_temp_cont;
		}

		$barchart_periodo = $this->render_regex_dict('BARCHART_PERIODOS', $barchart_periodo, $array_periodos);
		$barchart_sum_semestre_cc = $this->render_regex_dict('BARCHART_SUM_SEMESTRE_CC', $barchart_sum_semestre_cc, $array_semestre_sum_cc);
		$barchart_sum_semestre_cont = $this->render_regex_dict('BARCHART_SUM_SEMESTRE_CONT', $barchart_sum_semestre_cont, $array_semestre_sum_cont);

		$render = str_replace('{tbl_cuentacorrienteproveedor}', $tbl_cuentacorrienteproveedor, $gui);
		$render = str_replace('{fecha_sys}', date('d/m/Y'), $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{tbl_sum_importe_producto}', $tbl_sum_importe_producto, $render);
		$render = str_replace('{tbl_sum_cantidad_producto}', $tbl_sum_cantidad_producto, $render);
		$render = str_replace('{barchart_periodo}', $barchart_periodo, $render);
		$render = str_replace('{barchart_sum_semestre_cc}', $barchart_sum_semestre_cc, $render);
		$render = str_replace('{barchart_sum_semestre_cont}', $barchart_sum_semestre_cont, $render);
		$render = str_replace('{lbl_piechart_gasto}', $gui_lbl_piechart_gasto, $render);
		$render = str_replace('{valores_piechart_gasto}', $gui_valores_piechart_gasto, $render);
		$render = $this->render($array_totales, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function vdr_panel($pedidovendedor_collection, $array_totales) {
		$gui = file_get_contents("static/modules/reporte/vdr_panel.html");
		$tbl_pedidovendedor_array = file_get_contents("static/modules/pedidovendedor/tbl_small_pedidovendedor_array.html");
		$tbl_pedidovendedor_array = $this->render_regex_dict('TBL_PEDIDOVENDEDOR', $tbl_pedidovendedor_array, $pedidovendedor_collection);
		$render = str_replace('{tbl_pedidovendedor}', $tbl_pedidovendedor_array, $gui);
		$render = $this->render($array_totales, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function resumen_diario($array_totales, $cobranza_collection, $pagoproveedor_collection,$detalle_gasto_diario,$detalle_liquidacion,$detalle_vehiculos,$detalle_comision,$tipo_resumen_diario) {
		if ($tipo_resumen_diario == 1) {
			$gui = file_get_contents("static/modules/reporte/resumen_diario.html");
		} else {
			$gui = file_get_contents("static/modules/reporte/filtro_resumen_diario.html");
		}

		$gui_detalle_cobranza = file_get_contents("static/modules/reporte/detalle_cobrador_cobranza.html");
		$gui_detalle_cobranza = $this->render_regex_dict('DETALLE_COBRANZA', $gui_detalle_cobranza, $cobranza_collection);

		$gui_detalle_pagoproveedor = file_get_contents("static/modules/reporte/detalle_pagoproveedor.html");
		$gui_detalle_pagoproveedor = $this->render_regex_dict('DETALLE_PAGOPROVEEDOR', $gui_detalle_pagoproveedor, $pagoproveedor_collection);

		$gui_detalle_gasto_diario = file_get_contents("static/modules/reporte/detalle_gastodiario.html");
		$gui_detalle_gasto_diario = $this->render_regex_dict('DETALLE_GASTODIARIO', $gui_detalle_gasto_diario, $detalle_gasto_diario);

		$gui_detalle_liquidacion = file_get_contents("static/modules/reporte/detalle_liquidacion.html");
		$gui_detalle_liquidacion = $this->render_regex_dict('DETALLE_LIQUIDACION', $gui_detalle_liquidacion, $detalle_liquidacion);

		$gui_detalle_vehiculos = file_get_contents("static/modules/reporte/detalle_vehiculos.html");
		$gui_detalle_vehiculos = $this->render_regex_dict('DETALLE_VEHICULOS', $gui_detalle_vehiculos, $detalle_vehiculos);

		$gui_detalle_comision = file_get_contents("static/modules/reporte/detalle_comision.html");
		$gui_detalle_comision = $this->render_regex_dict('DETALLE_COMISION', $gui_detalle_comision, $detalle_comision);

		$render = $this->render($array_totales, $gui);
		$render = str_replace('{detalle_cobranza}', $gui_detalle_cobranza, $render);
		$render = str_replace('{detalle_pagoproveedor}', $gui_detalle_pagoproveedor, $render);
		$render = str_replace('{detalle_gastosvarios}', $gui_detalle_gasto_diario, $render);
		$render = str_replace('{detalle_liquidacion}', $gui_detalle_liquidacion, $render);
		$render = str_replace('{detalle_vehiculos}', $gui_detalle_vehiculos, $render);
		$render = str_replace('{detalle_comision}', $gui_detalle_comision, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function detalle_cobrador_cobranza($cuentacorriente_collection, $obj_cobrador, $cobranza, $cobrador_id, $fecha) {
		$gui = file_get_contents("static/modules/reporte/ver_detalle_cobrador_cobranza.html");

		$gui_detalle_cobranza = file_get_contents("static/modules/reporte/tbl_detalle_cobrador_cobranza.html");
		$gui_detalle_cobranza = $this->render_regex_dict('TBL_COBRADOR_COBRANZA', $gui_detalle_cobranza, $cuentacorriente_collection);

		$obj_cobrador = $this->set_dict($obj_cobrador);

		$render = str_replace('{cobranza}', $cobranza[0]['COBRANZA'], $gui);
		$render = str_replace('{cobrador_id}', $cobrador_id, $render);
		$render = str_replace('{fecha}', $fecha, $render);
		$render = $this->render($obj_cobrador, $render);
		$render = str_replace('{tbl_detalle_cobrador_cobranza}', $gui_detalle_cobranza, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function formulario_cajadiaria_ajax($cajadiaria) {
		$gui = file_get_contents("static/modules/reporte/formulario_cajadiaria_ajax.html");
		$caja_enabled = ($cajadiaria == 0) ? '' : 'disabled';
		$btn_enabled = ($cajadiaria == 0) ? '' : 'disabled';
		$render = str_replace('{caja_enabled}', $caja_enabled, $gui);
		$render = str_replace('{btn_enabled}', $btn_enabled, $render);
		$render = str_replace('{cajadiaria-caja}', $cajadiaria, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}

	function form_buscar_caja_diaria_ajax() {
		$gui = file_get_contents("static/modules/reporte/form_buscar_caja_diaria_ajax.html");
		$render = str_replace('{url_app}', URL_APP, $gui);
		print $render;
	}

	function balance($array_balances, $pagocomisiones_collection, $periodo, $obj_configuracionbalance, $vehiculocombustible_collection, $producto_collection, $productomarca_collection, $salario_collection, $ganancia_vendedor_dia, $creditoproveedordetalle_collection) {
		$gui = file_get_contents("static/modules/reporte/balance.html");
		$gui_lbl_piechart_gasto = file_get_contents("static/modules/reporte/lbl_piechart_gasto.html");
		$gui_valores_piechart_gasto = file_get_contents("static/modules/reporte/valores_piechart_gasto.html");
		$gui_lbl_piechart_pago_comision = file_get_contents("static/modules/reporte/lbl_piechart_pago_comision.html");
		$gui_valores_piechart_pago_comision = file_get_contents("static/modules/reporte/valores_piechart_pago_comision.html");
		$gui_tbl_vendedor_pago_comision = file_get_contents("static/modules/reporte/tbl_vendedor_pago_comision.html");
		$gui_tbl_vendedor_pago_salarios = file_get_contents("static/modules/reporte/tbl_vendedor_pago_salarios.html");
		$gui_tbl_vendedor_ganancia = file_get_contents("static/modules/reporte/tbl_vendedor_ganancia.html");
		$gui_tbl_proveedor_credito = file_get_contents("static/modules/reporte/tbl_proveedor_credito.html");
		$gui_tbl_vehiculocombustible = file_get_contents("static/modules/reporte/tbl_vehiculocombustible.html");
		$tbl_producto = file_get_contents("static/modules/reporte/tbl_producto_array.html");
		$tbl_productomarca = file_get_contents("static/modules/reporte/tbl_productomarca.html");

		$tbl_productomarca = $this->render_regex('TBL_PRODUCTOMARCA', $tbl_productomarca, $productomarca_collection);
		$tbl_producto = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto, $producto_collection);
		$gui_tbl_vehiculocombustible = $this->render_regex_dict('TBL_VEHICULOCOMBUSTIBLE', $gui_tbl_vehiculocombustible, $vehiculocombustible_collection);
		$gui_tbl_vendedor_pago_comision = $this->render_regex_dict('TBL_PAGOCOMISION', $gui_tbl_vendedor_pago_comision, $pagocomisiones_collection);
		$gui_tbl_vendedor_pago_salarios = $this->render_regex_dict('TBL_PAGOSALARIOS', $gui_tbl_vendedor_pago_salarios, $salario_collection);
		$gui_tbl_vendedor_ganancia = $this->render_regex_dict('TBL_VENDEDOR', $gui_tbl_vendedor_ganancia, $ganancia_vendedor_dia);
		$gui_tbl_proveedor_credito = $this->render_regex_dict('TBL_CREDITOPROVEEDORDETALLE', $gui_tbl_proveedor_credito, $creditoproveedordetalle_collection);

		$gui_lbl_piechart_pago_comision = $this->render_regex_dict('LBL_PIECHART_PAGO_COMISION', $gui_lbl_piechart_pago_comision, $pagocomisiones_collection);
		$gui_valores_piechart_pago_comision = $this->render_regex_dict('VALORES_PIECHART_PAGO_COMISION', $gui_valores_piechart_pago_comision, $pagocomisiones_collection);
		$gui_lbl_piechart_pago_comision = str_replace('<!--LBL_PIECHART_PAGO_COMISION-->', '', $gui_lbl_piechart_pago_comision);
		$gui_valores_piechart_pago_comision = str_replace('<!--VALORES_PIECHART_PAGO_COMISION-->', '', $gui_valores_piechart_pago_comision);

		$obj_configuracionbalance = $this->set_dict($obj_configuracionbalance);
		foreach ($obj_configuracionbalance as $propiedad=>$valor) {
			$nueva_propiedad = str_replace('}', '_tachado}', $propiedad);
			$nuevo_valor = ($valor == 'checked') ? 'none' : 'line-through';
			$obj_configuracionbalance["{$nueva_propiedad}"] = $nuevo_valor;
		}
		
		$render = $this->render($array_balances, $gui);
		$render = $this->render($obj_configuracionbalance, $render);
		$render = str_replace('{valores_piechart_pago_comision}', $gui_valores_piechart_pago_comision, $render);
		$render = str_replace('{lbl_piechart_pago_comision}', $gui_lbl_piechart_pago_comision, $render);
		$render = str_replace('{tbl_vendedor_pago_comision}', $gui_tbl_vendedor_pago_comision, $render);
		$render = str_replace('{tbl_vendedor_pago_salarios}', $gui_tbl_vendedor_pago_salarios, $render);
		$render = str_replace('{tbl_vehiculocombustible}', $gui_tbl_vehiculocombustible, $render);
		$render = str_replace('{periodo_balance}', $periodo, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto, $render);
		$render = str_replace('{tbl_productomarca}', $tbl_productomarca, $render);
		$render = str_replace('{tbl_vendedor_ganancia}', $gui_tbl_vendedor_ganancia, $render);
		$render = str_replace('{tbl_proveedor_credito}', $gui_tbl_proveedor_credito, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function reportes($sum_importe_producto, $sum_cantidad_producto, $vendedor_collection, $producto_collection, $gastocategoria_collection, $productomarca_collection, $proveedor_collection,$user_level,$clientes_collection) {
		$gui = file_get_contents("static/modules/reporte/reportes.html");
		$tbl_proveedor = file_get_contents("static/modules/reporte/tbl_proveedor.html");
		$tbl_productos = file_get_contents("static/modules/reporte/tbl_productos_array.html");
		$tbl_productomarcas = file_get_contents("static/modules/reporte/tbl_productomarcas.html");
		$tbl_vendedor = file_get_contents("static/modules/reporte/tbl_vendedor.html");

		$tbl_producto = file_get_contents("static/modules/reporte/tbl_producto_array.html");
		$tbl_productomarca = file_get_contents("static/modules/reporte/tbl_productomarca.html");
		$slt_productomarca = file_get_contents("static/common/slt_productomarca.html");
		$tbl_productomarca_grafico = file_get_contents("static/modules/reporte/tbl_productomarcagrafico.html");
		$tbl_sum_importe_producto = file_get_contents("static/modules/reporte/tbl_sum_importe_producto.html");
		$tbl_sum_cantidad_producto = file_get_contents("static/modules/reporte/tbl_sum_cantidad_producto.html");
		
		$tbl_producto = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto, $producto_collection);
		$tbl_productomarca = $this->render_regex('TBL_PRODUCTOMARCA', $tbl_productomarca, $productomarca_collection);
		$slt_productomarca = $this->render_regex('SLT_PRODUCTOMARCA', $slt_productomarca, $productomarca_collection);
		$tbl_productomarca_grafico = $this->render_regex('TBL_PRODUCTOMARCAGRAFICO', $tbl_productomarca_grafico, $productomarca_collection);
		
		$tbl_productos = $this->render_regex_dict('TBL_PRODUCTOS', $tbl_productos, $producto_collection);
		$tbl_productomarcas = $this->render_regex('TBL_PRODUCTOMARCAS', $tbl_productomarcas, $productomarca_collection);
		$tbl_proveedor = $this->render_regex_dict('TBL_PROVEEDOR', $tbl_proveedor, $proveedor_collection);
		$tbl_vendedor = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor, $vendedor_collection);

		$sum_importe_producto = $this->order_collection_array($sum_importe_producto, 'IMPORTE', SORT_DESC);
		$sum_cantidad_producto = $this->order_collection_array($sum_cantidad_producto, 'CANTIDAD', SORT_DESC);

		$i = 0;
		foreach ($sum_importe_producto as $clave=>$valor) {
			if ($i > 4) unset($sum_importe_producto[$clave]);
			$i = $i + 1;
		}

		$j = 0;
		foreach ($sum_cantidad_producto as $clave=>$valor) {
			if ($j > 4) unset($sum_cantidad_producto[$clave]);
			$j = $j + 1;
		}

		$tbl_sum_importe_producto = $this->render_regex_dict('TBL_SUM_IMPORTE_PRODUCTO', $tbl_sum_importe_producto, $sum_importe_producto);
		$tbl_sum_cantidad_producto = $this->render_regex_dict('TBL_SUM_CANTIDAD_PRODUCTO', $tbl_sum_cantidad_producto, $sum_cantidad_producto);

		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor_array.html");
		$gui_slt_vendedor = $this->render_regex_dict('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);

		$gui_slt_proveedor = file_get_contents("static/common/slt_proveedor_array.html");
		$gui_slt_proveedor = $this->render_regex_dict('SLT_PROVEEDOR', $gui_slt_proveedor, $proveedor_collection);

		$gui_slt_gastocategoria = file_get_contents("static/common/slt_gastocategoria.html");
		$gui_slt_gastocategoria = $this->render_regex('SLT_GASTOCATEGORIA', $gui_slt_gastocategoria, $gastocategoria_collection);

		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		if($usuario_id == 13){
			$display_perfil = '';
		} else {
			$display_perfil = ($user_level == 2) ? 'none' : '';
		}

		$render = str_replace('{fecha_sys}', date('d/m/Y'), $gui);
		$render = str_replace('{periodo_actual}', date('Ym'), $render);
		$render = str_replace('{tbl_sum_importe_producto}', $tbl_sum_importe_producto, $render);
		$render = str_replace('{tbl_sum_cantidad_producto}', $tbl_sum_cantidad_producto, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto, $render);
		$render = str_replace('{tbl_productomarca}', $tbl_productomarca, $render);
		$render = str_replace('{tbl_productomarca_grafico}', $tbl_productomarca_grafico, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{slt_productomarca}', $slt_productomarca, $render);
		$render = str_replace('{slt_proveedor}', $gui_slt_proveedor, $render);
		$render = str_replace('{slt_gastocategoria}', $gui_slt_gastocategoria, $render);
		$render = str_replace('{display_perfil}', $display_perfil, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor, $render);
		$render = str_replace('{tbl_proveedor}', $tbl_proveedor, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{tbl_productos}', $tbl_productos, $render);
		$render = str_replace('{tbl_productomarcas}', $tbl_productomarcas, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function detalle_facturaproveedor($cuentacorriente_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/reporte/detalle_facturaproveedor.html");
		$gui_tbl_cuentacorriente = file_get_contents("static/modules/reporte/tbl_cuentacorriente_corto_array.html");

		unset($obj_proveedor->infocontacto_collection);

		$obj_proveedor = $this->set_dict($obj_proveedor);
		$gui_tbl_cuentacorriente = $this->render_regex_dict('TBL_CUENTACORRIENTE', $gui_tbl_cuentacorriente, $cuentacorriente_collection);
		$render = str_replace('{tbl_cuentacorriente}', $gui_tbl_cuentacorriente, $gui);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	

	//PANELES REPORTES
	function reportes_productos($sum_importe_producto, $sum_cantidad_producto, $vendedor_collection, $producto_collection, $productomarca_collection, $proveedor_collection, $user_level) {
		$gui = file_get_contents("static/modules/reporte/reportes_productos.html");
		$tbl_proveedor = file_get_contents("static/modules/reporte/tbl_proveedor.html");
		$tbl_productos = file_get_contents("static/modules/reporte/tbl_productos_array.html");
		$tbl_productomarcas = file_get_contents("static/modules/reporte/tbl_productomarcas.html");
		$tbl_vendedor = file_get_contents("static/modules/reporte/tbl_vendedor.html");

		$tbl_producto = file_get_contents("static/modules/reporte/tbl_producto_array.html");
		$tbl_productomarca = file_get_contents("static/modules/reporte/tbl_productomarca.html");
		$slt_productomarca = file_get_contents("static/common/slt_productomarca.html");
		$tbl_productomarca_grafico = file_get_contents("static/modules/reporte/tbl_productomarcagrafico.html");
		$tbl_sum_importe_producto = file_get_contents("static/modules/reporte/tbl_sum_importe_producto.html");
		$tbl_sum_cantidad_producto = file_get_contents("static/modules/reporte/tbl_sum_cantidad_producto.html");
		
		$tbl_producto = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto, $producto_collection);
		$tbl_productomarca = $this->render_regex('TBL_PRODUCTOMARCA', $tbl_productomarca, $productomarca_collection);
		$slt_productomarca = $this->render_regex('SLT_PRODUCTOMARCA', $slt_productomarca, $productomarca_collection);
		$tbl_productomarca_grafico = $this->render_regex('TBL_PRODUCTOMARCAGRAFICO', $tbl_productomarca_grafico, $productomarca_collection);
		
		$tbl_productos = $this->render_regex_dict('TBL_PRODUCTOS', $tbl_productos, $producto_collection);
		$tbl_productomarcas = $this->render_regex('TBL_PRODUCTOMARCAS', $tbl_productomarcas, $productomarca_collection);
		$tbl_proveedor = $this->render_regex_dict('TBL_PROVEEDOR', $tbl_proveedor, $proveedor_collection);
		$tbl_vendedor = $this->render_regex_dict('TBL_VENDEDOR', $tbl_vendedor, $vendedor_collection);

		$sum_importe_producto = $this->order_collection_array($sum_importe_producto, 'IMPORTE', SORT_DESC);
		$sum_cantidad_producto = $this->order_collection_array($sum_cantidad_producto, 'CANTIDAD', SORT_DESC);

		$i = 0;
		foreach ($sum_importe_producto as $clave=>$valor) {
			if ($i > 4) unset($sum_importe_producto[$clave]);
			$i = $i + 1;
		}

		$j = 0;
		foreach ($sum_cantidad_producto as $clave=>$valor) {
			if ($j > 4) unset($sum_cantidad_producto[$clave]);
			$j = $j + 1;
		}

		$tbl_sum_importe_producto = $this->render_regex_dict('TBL_SUM_IMPORTE_PRODUCTO', $tbl_sum_importe_producto, $sum_importe_producto);
		$tbl_sum_cantidad_producto = $this->render_regex_dict('TBL_SUM_CANTIDAD_PRODUCTO', $tbl_sum_cantidad_producto, $sum_cantidad_producto);

		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor_array.html");
		$gui_slt_vendedor = $this->render_regex_dict('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);

		$gui_slt_proveedor = file_get_contents("static/common/slt_proveedor_array.html");
		$gui_slt_proveedor = $this->render_regex_dict('SLT_PROVEEDOR', $gui_slt_proveedor, $proveedor_collection);

		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		if($usuario_id == 13){
			$display_perfil = '';
		} else {
			$display_perfil = ($user_level == 2) ? 'none' : '';
		}

		$render = str_replace('{fecha_sys}', date('d/m/Y'), $gui);
		$render = str_replace('{periodo_actual}', date('Ym'), $render);
		$render = str_replace('{tbl_sum_importe_producto}', $tbl_sum_importe_producto, $render);
		$render = str_replace('{tbl_sum_cantidad_producto}', $tbl_sum_cantidad_producto, $render);
		$render = str_replace('{tbl_producto}', $tbl_producto, $render);
		$render = str_replace('{tbl_productomarca}', $tbl_productomarca, $render);
		$render = str_replace('{tbl_productomarca_grafico}', $tbl_productomarca_grafico, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{slt_productomarca}', $slt_productomarca, $render);
		$render = str_replace('{slt_proveedor}', $gui_slt_proveedor, $render);
		$render = str_replace('{display_perfil}', $display_perfil, $render);
		$render = str_replace('{tbl_vendedor}', $tbl_vendedor, $render);
		$render = str_replace('{tbl_proveedor}', $tbl_proveedor, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = str_replace('{tbl_productos}', $tbl_productos, $render);
		$render = str_replace('{tbl_productomarcas}', $tbl_productomarcas, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	// REPORTES PRODUCTOS
	function ajax_cobertura_marca($obj_resultado, $array_titulo, $tipo_grafico, $vendedor) {
		if ($tipo_grafico == 1) {
			$gui = file_get_contents("static/modules/reporte/generar_grafico_importe.html");
			$gui_tbl_cobertura_vendedor_marca = file_get_contents("static/modules/reporte/tbl_cobertura_vendedor_marca_importe.html");
			$obj_resultado = $this->order_collection_array($obj_resultado, 'IMPORTE', SORT_DESC);
			$gui_tbl_cobertura_vendedor_marca = $this->render_regex_dict('TBL_COBERTURA_VENDEDOR_MARCA', $gui_tbl_cobertura_vendedor_marca, $obj_resultado);
		} else {
			$gui = file_get_contents("static/modules/reporte/generar_grafico_cantidad.html");
			$gui_tbl_cobertura_vendedor_marca = file_get_contents("static/modules/reporte/tbl_cobertura_vendedor_marca_cantidad.html");
			$obj_resultado = $this->order_collection_array($obj_resultado, 'CANTIDAD', SORT_DESC);
			$gui_tbl_cobertura_vendedor_marca = $this->render_regex_dict('TBL_COBERTURA_VENDEDOR_MARCA', $gui_tbl_cobertura_vendedor_marca, $obj_resultado);
		}
		
		$render = str_replace('{tbl_cobertura_vendedor_marca}', $gui_tbl_cobertura_vendedor_marca, $gui);
		$render = str_replace('{vendedor}', $vendedor, $render);
		$render = $this->render_regex_dict('DATOS_GRAFICO', $render, $obj_resultado);
		$render = $this->render($array_titulo, $render);
 		print $render;
	}
}
?>
