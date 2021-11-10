<?php


class GastoCategoriaView extends View {

	function panel($gastocategoria_collection,$gastosubcategoria_collection) {
		$gui = file_get_contents("static/modules/gastocategoria/panel.html");
		$gui_gastosubcategoria = file_get_contents("static/modules/gastosubcategoria/slt_subcategoria.html");

		$gui_gastosubcategoria = $this->render_regex('LST_GASTOSUBCATEGORIA', $gui_gastosubcategoria, $gastosubcategoria_collection);
		$gui = str_replace('{subcategoria}', $gui_gastosubcategoria, $gui);
		$render = $this->render_regex('TBL_GASTOCATEGORIA', $gui, $gastocategoria_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($gastocategoria_collection, $obj_gastocategoria,$gastosubcategoria_collection) {
		$gui = file_get_contents("static/modules/gastocategoria/editar.html");
		$gui_gastosubcategoria = file_get_contents("static/modules/gastosubcategoria/slt_subcategoria.html");

		$obj_gastocategoria = $this->set_dict($obj_gastocategoria);
		$gui_gastosubcategoria = $this->render_regex('LST_GASTOSUBCATEGORIA', $gui_gastosubcategoria, $gastosubcategoria_collection);
		$gui = str_replace('{subcategoria}', $gui_gastosubcategoria, $gui);

		$render = $this->render_regex('TBL_GASTOCATEGORIA', $gui, $gastocategoria_collection);
		$render = $this->render($obj_gastocategoria, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>
