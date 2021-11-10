<?php


class ProductoUnidadView extends View {
	
	function panel($productounidad_collection) {
		$gui = file_get_contents("static/modules/productounidad/panel.html");

		$render = $this->render_regex('TBL_PRODUCTOUNIDAD', $gui, $productounidad_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($productounidad_collection, $obj_productounidad) {
		$gui = file_get_contents("static/modules/productounidad/editar.html");
		$obj_productounidad = $this->set_dict($obj_productounidad);
		$render = $this->render_regex('TBL_PRODUCTOUNIDAD', $gui, $productounidad_collection);
		$render = $this->render($obj_productounidad, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>