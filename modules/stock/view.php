<?php


class StockView extends View {
	function panel($stock_collection, $almacen_collection, $array_totales, $obj_almacen) {
		$gui = file_get_contents("static/modules/stock/panel.html");
		$tbl_stock = file_get_contents("static/modules/stock/tbl_stock.html");
		$tbl_stock = $this->render_regex('TBL_STOCK', $tbl_stock, $stock_collection);
		$slt_almacen = file_get_contents("static/common/slt_almacen.html");
		$slt_almacen = $this->render_regex('SLT_ALMACEN', $slt_almacen, $almacen_collection);		

		$obj_almacen = $this->set_dict($obj_almacen);
		$render = str_replace('{tbl_stock}', $tbl_stock, $gui);
		$render = str_replace('{slt_almacen}', $slt_almacen, $render);
		$render = str_replace('{fecha_sys}', date('d/m/Y'), $render);
		$render = $this->render($array_totales, $render);
		$render = $this->render($obj_almacen, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function vdr_stock($stock_collection, $obj_almacen) {
		$gui = file_get_contents("static/modules/stock/vdr_stock.html");
		$tbl_stock = file_get_contents("static/modules/stock/tbl_vdr_stock.html");
		$tbl_stock = $this->render_regex('TBL_STOCK', $tbl_stock, $stock_collection);

		$obj_almacen = $this->set_dict($obj_almacen);
		$render = str_replace('{tbl_stock}', $tbl_stock, $gui);
		$render = str_replace('{fecha_sys}', date('d/m/Y'), $render);
		$render = $this->render($array_totales, $render);
		$render = $this->render($obj_almacen, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function ajustar_stock($stock_collection, $almacen_collection, $obj_almacen, $flag_modal) {
		$gui = file_get_contents("static/modules/stock/ajustar_stock.html");
		$tbl_ajustar_stock = file_get_contents("static/modules/stock/tbl_ajustar_stock.html");
		$tbl_ajustar_stock = $this->render_regex('TBL_AJUSTARSTOCK', $tbl_ajustar_stock, $stock_collection);
		$slt_almacen = file_get_contents("static/common/slt_almacen.html");
		$slt_almacen = $this->render_regex('SLT_ALMACEN', $slt_almacen, $almacen_collection);

		switch ($flag_modal) {
			case 1:
				$array_modal = array('{display}'=>'none',
									 '{msj}'=>'');
				break;
			case 2:
				$array_modal = array('{display}'=>'show',
									 '{msj}'=>'Se ha generado el ajuste de stock al/los producto/s!');
				break;
			case 3:
				$array_modal = array('{display}'=>'show',
									 '{msj}'=>'No se ha definido una cantidad mayor a cero para algún producto!');
				break;
		}

		$obj_almacen = $this->set_dict($obj_almacen);
		$render = str_replace('{tbl_ajustar_stock}', $tbl_ajustar_stock, $gui);
		$render = str_replace('{slt_almacen}', $slt_almacen, $render);
		$render = $this->render($obj_almacen, $render);
		$render = $this->render($array_modal, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function movimiento_stock_1($almacen_collection) {
		$gui = file_get_contents("static/modules/stock/movimiento_stock_1.html");
		$slt_almacen = file_get_contents("static/common/slt_almacen.html");
		$slt_almacen = $this->render_regex('SLT_ALMACEN', $slt_almacen, $almacen_collection);
		$render = str_replace('{slt_almacen}', $slt_almacen, $gui);
		$render = $this->render($array_modal, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function movimiento_stock_2($producto_collection, $almacen_origen_id, $almacen_destino_id) {
		$gui = file_get_contents("static/modules/stock/movimiento_stock_2.html");
		$tbl_movimiento_stock = file_get_contents("static/modules/stock/tbl_movimiento_stock.html");
		$tbl_movimiento_stock = $this->render_regex('TBL_MOVIMIENTO_STOCK', $tbl_movimiento_stock, $producto_collection);	
		$render = str_replace('{tbl_movimiento_stock}', $tbl_movimiento_stock, $gui);
		$render = str_replace('{almacen_origen}', $almacen_origen_id, $render);
		$render = str_replace('{almacen_destino}', $almacen_destino_id, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function stock_inicial($stock_collection, $obj_almacen, $flag_modal) {
		$gui = file_get_contents("static/modules/stock/stock_inicial.html");
		$tbl_stock_inicial_array = file_get_contents("static/modules/stock/tbl_stock_inicial_array.html");
		
		switch ($flag_modal) {
			case 1:
				$array_modal = array('{display}'=>'none',
									 '{msj}'=>'');
				break;
			case 2:
				$array_modal = array('{display}'=>'show',
									 '{msj}'=>'Se ha cargado el stock inicial al/los producto/s!');
				break;
			case 3:
				$array_modal = array('{display}'=>'show',
									 '{msj}'=>'No se ha definido una cantidad mayor a cero para algún producto!');
				break;
		}

		$tbl_stock_inicial_array = $this->render_regex_dict('TBL_STOCK_INICIAL', $tbl_stock_inicial_array, $stock_collection);

		$obj_almacen = $this->set_dict($obj_almacen);
		$render = str_replace('{tbl_stock_inicial}', $tbl_stock_inicial_array, $gui);
		$render = $this->render($obj_almacen, $render);
		$render = $this->render($array_modal, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar_producto($stock_collection, $obj_producto) {
		$gui = file_get_contents("static/modules/stock/consultar_producto.html");
		$tbl_stock_array = file_get_contents("static/modules/stock/tbl_stock_array.html");
		$tbl_stock_array = $this->render_regex_dict('TBL_STOCK', $tbl_stock_array, $stock_collection);		

		$costo_descuento = $obj_producto->costo - (($obj_producto->costo * $obj_producto->descuento) / 100);
		$costo_iva = (($costo_descuento * $obj_producto->iva) / 100) + $costo_descuento;
		$valor_ganancia = $costo_iva * $obj_producto->porcentaje_ganancia / 100;
		$valor_venta = $costo_iva + $valor_ganancia;
		
		$obj_producto->costo_descuento = round($costo_descuento, 2);
		$obj_producto->costo_iva = round($costo_iva, 2);
		$obj_producto->valor_ganancia = round($valor_ganancia, 2);
		$obj_producto->valor_venta = round($valor_venta, 2);
		$obj_producto = $this->set_dict($obj_producto);
		
		$render = str_replace('{tbl_stock_array}', $tbl_stock_array, $gui);
		$render = $this->render($obj_producto, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>