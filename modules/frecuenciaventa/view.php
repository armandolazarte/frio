<?php


class FrecuenciaVentaView extends View {
	
	function panel($frecuenciaventa_collection) {
		$gui = file_get_contents("static/modules/frecuenciaventa/panel.html");

		$render = $this->render_regex('TBL_FRECUENCIAVENTA', $gui, $frecuenciaventa_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($frecuenciaventa_collection, $obj_frecuenciaventa) {
		$gui = file_get_contents("static/modules/frecuenciaventa/editar.html");
		$obj_frecuenciaventa = $this->set_dict($obj_frecuenciaventa);
		$render = $this->render_regex('TBL_FRECUENCIAVENTA', $gui, $frecuenciaventa_collection);
		$render = $this->render($obj_frecuenciaventa, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>