<?php


class VehiculoMarcaView extends View {

	function panel($vehiculomarca_collection) {
		$gui = file_get_contents("static/modules/vehiculomarca/panel.html");
		$render = $this->render_regex('TBL_VEHICULOMARCA', $gui, $vehiculomarca_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($vehiculomarca_collection, $obj_vehiculomarca) {
		$gui = file_get_contents("static/modules/vehiculomarca/editar.html");

		$render = $this->render_regex('TBL_VEHICULOMARCA', $gui, $vehiculomarca_collection);
		$obj_vehiculomarca = $this->set_dict($obj_vehiculomarca);
		$render = $this->render($obj_vehiculomarca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>
