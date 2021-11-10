<?php


class VehiculoView extends View {

	function panel($vehiculo_collection, $vehiculomodelo_collection, $combustible_collection) {
		$gui = file_get_contents("static/modules/vehiculo/panel.html");
		$gui_tbl_vehiculo = file_get_contents("static/modules/vehiculo/tbl_vehiculo.html");
		$gui_tbl_vehiculo = $this->render_regex('TBL_VEHICULO', $gui_tbl_vehiculo, $vehiculo_collection);
		$gui_slt_vehiculomodelo = file_get_contents("static/common/slt_vehiculomodelo.html");
		$gui_slt_vehiculomodelo = $this->render_regex('SLT_VEHICULOMODELO', $gui_slt_vehiculomodelo, $vehiculomodelo_collection);
		$gui_slt_combustible = file_get_contents("static/common/slt_combustible.html");
		$gui_slt_combustible = $this->render_regex('SLT_COMBUSTIBLE', $gui_slt_combustible, $combustible_collection);

		$render = str_replace('{tbl_vehiculo}', $gui_tbl_vehiculo, $gui);
		$render = str_replace('{slt_vehiculomodelo}', $gui_slt_vehiculomodelo, $render);
		$render = str_replace('{slt_combustible}', $gui_slt_combustible, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($vehiculo_collection, $vehiculomodelo_collection, $combustible_collection, $obj_vehiculo) {
		$gui = file_get_contents("static/modules/vehiculo/editar.html");
		$gui_tbl_vehiculo = file_get_contents("static/modules/vehiculo/tbl_vehiculo.html");
		$gui_tbl_vehiculo = $this->render_regex('TBL_VEHICULO', $gui_tbl_vehiculo, $vehiculo_collection);
		$gui_slt_vehiculomodelo = file_get_contents("static/common/slt_vehiculomodelo.html");
		$gui_slt_vehiculomodelo = $this->render_regex('SLT_VEHICULOMODELO', $gui_slt_vehiculomodelo, $vehiculomodelo_collection);
		$gui_slt_combustible = file_get_contents("static/common/slt_combustible.html");
		$gui_slt_combustible = $this->render_regex('SLT_COMBUSTIBLE', $gui_slt_combustible, $combustible_collection);

		$obj_vehiculo = $this->set_dict($obj_vehiculo);
		$render = str_replace('{tbl_vehiculo}', $gui_tbl_vehiculo, $gui);
		$render = str_replace('{slt_vehiculomodelo}', $gui_slt_vehiculomodelo, $render);
		$render = str_replace('{slt_combustible}', $gui_slt_combustible, $render);
		$render = $this->render($obj_vehiculo, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function combustible($vehiculocombustible_collection, $obj_vehiculo) {
		$gui = file_get_contents("static/modules/vehiculo/combustible.html");
		$gui_tbl_vehiculocombustible = file_get_contents("static/modules/vehiculo/tbl_vehiculocombustible.html");
		$gui_tbl_vehiculocombustible = $this->render_regex_dict('TBL_VEHICULOCOMBUSTIBLE', $gui_tbl_vehiculocombustible, $vehiculocombustible_collection);
		
		$obj_vehiculo = $this->set_dict($obj_vehiculo);
		$render = str_replace('{tbl_vehiculocombustible}', $gui_tbl_vehiculocombustible, $gui);
		$render = str_replace('{fecha_sys}', date('Y-m-d'), $render);
		$render = $this->render($obj_vehiculo, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar_combustible($vehiculocombustible_collection, $obj_vehiculocombustible) {
		$gui = file_get_contents("static/modules/vehiculo/editar_combustible.html");
		$gui_tbl_vehiculocombustible = file_get_contents("static/modules/vehiculo/tbl_vehiculocombustible.html");
		$gui_tbl_vehiculocombustible = $this->render_regex_dict('TBL_VEHICULOCOMBUSTIBLE', $gui_tbl_vehiculocombustible, $vehiculocombustible_collection);
		
		$obj_vehiculocombustible = $this->set_dict($obj_vehiculocombustible);
		$render = str_replace('{tbl_vehiculocombustible}', $gui_tbl_vehiculocombustible, $gui);
		$render = str_replace('{fecha_sys}', date('Y-m-d'), $render);
		$render = $this->render($obj_vehiculocombustible, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>
