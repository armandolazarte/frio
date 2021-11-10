<?php


class CondicionIVAView extends View {
	
	function panel($condicioniva_collection) {
		$gui = file_get_contents("static/modules/condicioniva/panel.html");

		$render = $this->render_regex('TBL_CONDICIONIVA', $gui, $condicioniva_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($condicioniva_collection, $obj_condicioniva) {
		$gui = file_get_contents("static/modules/condicioniva/editar.html");
		$obj_condicioniva = $this->set_dict($obj_condicioniva);
		$render = $this->render_regex('TBL_CONDICIONIVA', $gui, $condicioniva_collection);
		$render = $this->render($obj_condicioniva, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>