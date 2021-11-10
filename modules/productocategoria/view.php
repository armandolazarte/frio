<?php


class ProductoCategoriaView extends View {
	
	function panel($productocategoria_collection) {
		$gui = file_get_contents("static/modules/productocategoria/panel.html");

		$render = $this->render_regex('TBL_PRODUCTOCATEGORIA', $gui, $productocategoria_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($productocategoria_collection, $obj_productocategoria) {
		$gui = file_get_contents("static/modules/productocategoria/editar.html");
		$obj_productocategoria = $this->set_dict($obj_productocategoria);
		$render = $this->render_regex('TBL_PRODUCTOCATEGORIA', $gui, $productocategoria_collection);
		$render = $this->render($obj_productocategoria, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>