<?php


class CondicionPagoView extends View {
	
	function panel($condicionpago_collection) {
		$gui = file_get_contents("static/modules/condicionpago/panel.html");

		$render = $this->render_regex('TBL_CONDICIONPAGO', $gui, $condicionpago_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($condicionpago_collection, $obj_condicionpago) {
		$gui = file_get_contents("static/modules/condicionpago/editar.html");
		$obj_condicionpago = $this->set_dict($obj_condicionpago);
		$render = $this->render_regex('TBL_CONDICIONPAGO', $gui, $condicionpago_collection);
		$render = $this->render($obj_condicionpago, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>