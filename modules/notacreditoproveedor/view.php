<?php


class NotaCreditoProveedorView extends View {
	function listar($notacredito_collection) {
		$gui = file_get_contents("static/modules/notacreditoproveedor/listar.html");
		$tbl_notacredito_array = file_get_contents("static/modules/notacreditoproveedor/tbl_notacredito_array.html");
		$tbl_notacredito_array = $this->render_regex_dict('TBL_NOTACREDITOPROVEEDOR', $tbl_notacredito_array, $notacredito_collection);

		$render = str_replace('{tbl_notacredito}', $tbl_notacredito_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function buscar($notacredito_collection) {
		$gui = file_get_contents("static/modules/notacreditoproveedor/buscar.html");
		$tbl_notacredito_array = file_get_contents("static/modules/notacreditoproveedor/tbl_notacredito_array.html");
		$tbl_notacredito_array = $this->render_regex_dict('TBL_NOTACREDITOPROVEEDOR', $tbl_notacredito_array, $notacredito_collection);		

		$render = str_replace('{tbl_notacredito}', $tbl_notacredito_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($notacreditodetalle_collection, $obj_notacredito, $obj_ingreso, $notacredito_id) {
		$gui = file_get_contents("static/modules/notacreditoproveedor/consultar.html");
		$tbl_notacreditodetalle_array = file_get_contents("static/modules/notacreditoproveedor/tbl_notacreditodetalle_array.html");
		$tbl_notacreditodetalle_array = $this->render_regex_dict('TBL_NOTACREDITODETALLE', $tbl_notacreditodetalle_array, $notacreditodetalle_collection);
		
		if (is_object($obj_ingreso)) {
			$obj_ingreso->punto_venta = str_pad($obj_ingreso->punto_venta, 4, '0', STR_PAD_LEFT);
			$obj_ingreso->numero_factura = str_pad($obj_ingreso->numero_factura, 8, '0', STR_PAD_LEFT);
			unset($obj_ingreso->proveedor->infocontacto_collection);
		}

		$obj_notacredito->punto_venta = str_pad($obj_notacredito->punto_venta, 4, '0', STR_PAD_LEFT);
		$obj_notacredito->numero_factura = str_pad($obj_notacredito->numero_factura, 8, '0', STR_PAD_LEFT);
		$obj_ingreso = $this->set_dict($obj_ingreso);
		
		$obj_notacredito = $this->set_dict($obj_notacredito);
		$render = str_replace('{tbl_notacreditodetalle}', $tbl_notacreditodetalle_array, $gui);
		$render = $this->render($obj_notacredito, $render);
		$render = $this->render($obj_ingreso, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>