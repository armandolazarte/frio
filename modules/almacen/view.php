<?php


class AlmacenView extends View {
	
	function panel($almacen_collection) {
		$gui = file_get_contents("static/modules/almacen/panel.html");

		$render = $this->render_regex('TBL_ALMACEN', $gui, $almacen_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($almacen_collection, $obj_almacen) {
		$gui = file_get_contents("static/modules/almacen/editar.html");
		$obj_almacen = $this->set_dict($obj_almacen);
		$render = $this->render_regex('TBL_ALMACEN', $gui, $almacen_collection);
		$render = $this->render($obj_almacen, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>