<?php


class ProductoView extends View {
	function panel() {
		$gui = file_get_contents("static/modules/producto/panel.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar_producto() {
		$gui = file_get_contents("static/modules/producto/buscar_producto.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function listar($producto_collection) {
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

		switch ($user_level) {
			case 2:
				$gui = file_get_contents("static/modules/producto/listar_supervisor.html");
				$tbl_producto_array = file_get_contents("static/modules/producto/tbl_producto_array_supervisor.html");
				break;
			default:
				$gui = file_get_contents("static/modules/producto/listar.html");
				$tbl_producto_array = file_get_contents("static/modules/producto/tbl_producto_array.html");
				break;
		}

		if($usuario_id == 13){
			$gui = file_get_contents("static/modules/producto/listar.html");
			$tbl_producto_array = file_get_contents("static/modules/producto/tbl_producto_array.html");
		}

		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function ocultos($producto_collection) {
		$gui = file_get_contents("static/modules/producto/listar.html");
		$tbl_producto_array = file_get_contents("static/modules/producto/tbl_producto_oculto_array.html");

		$tbl_producto_array = $this->render_regex_dict('TBL_PRODUCTO', $tbl_producto_array, $producto_collection);
		$render = str_replace('{tbl_producto}', $tbl_producto_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function lista_precio($producto_collection, $productomarca_collection, $proveedor_collection) {
		$gui = file_get_contents("static/modules/producto/lista_precio.html");
		$tbl_listaprecio_array = file_get_contents("static/modules/producto/tbl_listaprecio_array.html");
		$gui_slt_productomarca = file_get_contents("static/common/slt_productomarca.html");
		$gui_slt_proveedor = file_get_contents("static/common/slt_proveedor.html");

		foreach ($proveedor_collection as $clave=>$valor) unset($proveedor_collection[$clave]->infocontacto_collection);

		$productomarca_collection = $this->order_collection_objects($productomarca_collection, 'denominacion', SORT_ASC);
		$proveedor_collection = $this->order_collection_objects($proveedor_collection, 'razon_social', SORT_ASC);

		$tbl_listaprecio_array = $this->render_regex_dict('TBL_LISTAPRECIO', $tbl_listaprecio_array, $producto_collection);
		$gui_slt_productomarca = $this->render_regex('SLT_PRODUCTOMARCA', $gui_slt_productomarca, $productomarca_collection);
		$gui_slt_proveedor = $this->render_regex('SLT_PROVEEDOR', $gui_slt_proveedor, $proveedor_collection);
		$render = str_replace('{tbl_listaprecio}', $tbl_listaprecio_array, $gui);
		$render = str_replace('{slt_proveedor}', $gui_slt_proveedor, $render);
		$render = str_replace('{slt_productomarca}', $gui_slt_productomarca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function vdr_lista_precio($producto_collection, $productomarca_collection) {
		$gui = file_get_contents("static/modules/producto/vdr_lista_precio.html");
		$tbl_listaprecio_array = file_get_contents("static/modules/producto/vdr_tbl_listaprecio_array.html");
		$gui_slt_productomarca = file_get_contents("static/common/slt_productomarca.html");

		$tbl_listaprecio_array = $this->render_regex_dict('TBL_LISTAPRECIO', $tbl_listaprecio_array, $producto_collection);
		$gui_slt_productomarca = $this->render_regex('SLT_PRODUCTOMARCA', $gui_slt_productomarca, $productomarca_collection);
		$render = str_replace('{tbl_listaprecio}', $tbl_listaprecio_array, $gui);
		$render = str_replace('{slt_productomarca}', $gui_slt_productomarca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($productomarca_collection, $productocategoria_collection, $productounidad_collection, $proveedor_collection) {
		$gui = file_get_contents("static/modules/producto/agregar.html");
		$gui_slt_productomarca = file_get_contents("static/common/slt_productomarca.html");
		$gui_slt_productocategoria = file_get_contents("static/common/slt_productocategoria.html");
		$gui_slt_productounidad = file_get_contents("static/common/slt_productounidad.html");
		$gui_slt_proveedor = file_get_contents("static/common/slt_proveedor.html");

		$gui_slt_productomarca = $this->render_regex('SLT_PRODUCTOMARCA', $gui_slt_productomarca, $productomarca_collection);
		$gui_slt_productocategoria = $this->render_regex('SLT_PRODUCTOCATEGORIA', $gui_slt_productocategoria, $productocategoria_collection);
		$gui_slt_productounidad = $this->render_regex('SLT_PRODUCTOUNIDAD', $gui_slt_productounidad, $productounidad_collection);
		$gui_slt_proveedor = $this->render_regex('SLT_PROVEEDOR', $gui_slt_proveedor, $proveedor_collection);
		$render = str_replace('{slt_productomarca}', $gui_slt_productomarca, $gui);
		$render = str_replace('{slt_productocategoria}', $gui_slt_productocategoria, $render);
		$render = str_replace('{slt_productounidad}', $gui_slt_productounidad, $render);
		$render = str_replace('{slt_proveedor}', $gui_slt_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($productomarca_collection, $productocategoria_collection, $productounidad_collection,
					$productodetalle_collection, $proveedor_collection, $obj_producto) {
		$gui = file_get_contents("static/modules/producto/editar.html");
		$gui_slt_productomarca = file_get_contents("static/common/slt_productomarca.html");
		$gui_slt_productocategoria = file_get_contents("static/common/slt_productocategoria.html");
		$gui_slt_productounidad = file_get_contents("static/common/slt_productounidad.html");
		$gui_tbl_proveedorproducto = file_get_contents("static/modules/producto/tbl_proveedor_producto.html");
		$gui_tbl_opt_proveedor = file_get_contents("static/modules/producto/tbl_opt_proveedor.html");

		$fecha_sys = date('Y-m-d');
		foreach ($proveedor_collection as $clave=>$valor) unset($proveedor_collection[$clave]->infocontacto_collection);
		$obj_producto->txt_exento = ($obj_producto->exento == 1) ? 'SI' : 'NO';
		$obj_producto->txt_no_gravado = ($obj_producto->no_gravado == 1) ? 'SI' : 'NO';
		$obj_producto = $this->set_dict($obj_producto);

		$gui_slt_productomarca = $this->render_regex('SLT_PRODUCTOMARCA', $gui_slt_productomarca, $productomarca_collection);
		$gui_slt_productocategoria = $this->render_regex('SLT_PRODUCTOCATEGORIA', $gui_slt_productocategoria, $productocategoria_collection);
		$gui_slt_productounidad = $this->render_regex('SLT_PRODUCTOUNIDAD', $gui_slt_productounidad, $productounidad_collection);
		$gui_tbl_proveedorproducto = $this->render_regex_dict('TBL_PROVEEDOR', $gui_tbl_proveedorproducto, $productodetalle_collection);
		$gui_tbl_opt_proveedor = $this->render_regex('TBL_PROVEEDOR', $gui_tbl_opt_proveedor, $proveedor_collection);

		$render = str_replace('{tbl_proveedor}', $gui_tbl_opt_proveedor, $gui);
		$render = str_replace('{tbl_proveedorproducto}', $gui_tbl_proveedorproducto, $render);
		$render = str_replace('{slt_productomarca}', $gui_slt_productomarca, $render);
		$render = str_replace('{slt_productocategoria}', $gui_slt_productocategoria, $render);
		$render = str_replace('{slt_productounidad}', $gui_slt_productounidad, $render);
		$render = str_replace('{fecha_sys}', $fecha_sys, $render);
		$render = $this->render($obj_producto, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($stock_collection, $obj_producto) {
		$gui = file_get_contents("static/modules/producto/consultar.html");
		$tbl_stock_array = file_get_contents("static/modules/stock/tbl_stock_array.html");
		$tbl_stock_array = $this->render_regex_dict('TBL_STOCK', $tbl_stock_array, $stock_collection);

		$obj_producto = $this->set_dict($obj_producto);
		$render = $this->render($obj_producto, $gui);
		$render = str_replace('{tbl_stock_array}', $tbl_stock_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function proveedor($productodetalle_collection, $obj_producto, $obj_proveedor) {
		$gui = file_get_contents("static/modules/producto/proveedor.html");
		$gui_lst_infocontacto_proveedor = file_get_contents("static/common/lst_infocontacto_proveedor.html");

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		unset($obj_proveedor->infocontacto_collection);
		$obj_producto = $this->set_dict($obj_producto);
		$obj_proveedor = $this->set_dict($obj_proveedor);

		$gui_lst_infocontacto_proveedor = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto_proveedor,
															  $infocontacto_collection);
		$render = $this->render_regex_dict('LST_PRODUCTODETALLE', $gui, $productodetalle_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto_proveedor, $render);
		$render = $this->render($obj_producto, $render);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>