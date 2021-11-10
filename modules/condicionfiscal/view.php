<?php


class CondicionFiscalView extends View {
	
	function panel($condicionfiscal_collection) {
		$gui = file_get_contents("static/modules/condicionfiscal/panel.html");

		$render = $this->render_regex('TBL_CONDICIONFISCAL', $gui, $condicionfiscal_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($condicionfiscal_collection, $obj_condicionfiscal) {
		$gui = file_get_contents("static/modules/condicionfiscal/editar.html");
		$obj_condicionfiscal = $this->set_dict($obj_condicionfiscal);
		$render = $this->render_regex('TBL_CONDICIONFISCAL', $gui, $condicionfiscal_collection);
		$render = $this->render($obj_condicionfiscal, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>