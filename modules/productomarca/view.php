<?php


class ProductoMarcaView extends View {
	
	function panel($productomarca_collection) {
		$gui = file_get_contents("static/modules/productomarca/panel.html");

		$render = $this->render_regex('TBL_PRODUCTOMARCA', $gui, $productomarca_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($productomarca_collection, $obj_productomarca) {
		$gui = file_get_contents("static/modules/productomarca/editar.html");
		$obj_productomarca = $this->set_dict($obj_productomarca);
		$render = $this->render_regex('TBL_PRODUCTOMARCA', $gui, $productomarca_collection);
		$render = $this->render($obj_productomarca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>