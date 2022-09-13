<?php


class ProductoFamiliaView extends View {
	
	function panel($productofamilia_collection) {
		$gui = file_get_contents("static/modules/productofamilia/panel.html");

		$render = $this->render_regex('TBL_PRODUCTOFAMILIA', $gui, $productofamilia_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($productofamilia_collection, $obj_productofamilia) {
		$gui = file_get_contents("static/modules/productofamilia/editar.html");
		$obj_productofamilia = $this->set_dict($obj_productofamilia);
		$render = $this->render_regex('TBL_PRODUCTOFAMILIA', $gui, $productofamilia_collection);
		$render = $this->render($obj_productofamilia, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>