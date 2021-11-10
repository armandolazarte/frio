<?php


class CombustibleView extends View {

	function panel($combustible_collection) {
		$gui = file_get_contents("static/modules/combustible/panel.html");
		$render = $this->render_regex('TBL_COMBUSTIBLE', $gui, $combustible_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($combustible_collection, $obj_combustible) {
		$gui = file_get_contents("static/modules/combustible/editar.html");
		$render = $this->render_regex('TBL_COMBUSTIBLE', $gui, $combustible_collection);
		$obj_combustible = $this->set_dict($obj_combustible);
		$render = $this->render($obj_combustible, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>
