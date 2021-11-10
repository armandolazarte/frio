<?php


class VehiculoModeloView extends View {

	function panel($vehiculomodelo_collection, $vehiculomarca_collection) {
		$gui = file_get_contents("static/modules/vehiculomodelo/panel.html");
		$gui_slt_vehiculomarca = file_get_contents("static/common/slt_vehiculomarca.html");

		$gui_slt_vehiculomarca = $this->render_regex('SLT_VEHICULOMARCA', $gui_slt_vehiculomarca, $vehiculomarca_collection);
		$render = $this->render_regex('TBL_VEHICULOMODELO', $gui, $vehiculomodelo_collection);
		$render = str_replace('{slt_vehiculomarca}', $gui_slt_vehiculomarca, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($vehiculomodelo_collection, $vehiculomarca_collection, $obj_vehiculomodelo) {
		$gui = file_get_contents("static/modules/vehiculomodelo/editar.html");
		$gui_slt_vehiculomarca = file_get_contents("static/common/slt_vehiculomarca.html");

		$vehiculomarca_modelo = $obj_vehiculomodelo->vehiculomarca->vehiculomarca_id;
		foreach ($vehiculomarca_collection as $clave=>$valor) if ($valor->vehiculomarca_id == $vehiculomarca_modelo) unset($vehiculomarca_collection[$clave]);
		
		$obj_vehiculomodelo = $this->set_dict($obj_vehiculomodelo);
		$gui_slt_vehiculomarca = $this->render_regex('SLT_VEHICULOMARCA', $gui_slt_vehiculomarca, $vehiculomarca_collection);
		$render = $this->render_regex('TBL_VEHICULOMODELO', $gui, $vehiculomodelo_collection);
		$render = str_replace('{slt_vehiculomarca}', $gui_slt_vehiculomarca, $render);
		$render = $this->render($obj_vehiculomodelo, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>
