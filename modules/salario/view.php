<?php


class SalarioView extends View {

	function panel($salario_collection, $empleado_collection) {
		$gui = file_get_contents("static/modules/salario/panel.html");
		$gui_tbl_salario = file_get_contents("static/modules/salario/tbl_salario_array.html");
		$gui_slt_empleado = file_get_contents("static/common/slt_empleado.html");
		$gui_tbl_salario = $this->render_regex_dict('TBL_SALARIO', $gui_tbl_salario, $salario_collection);
		$gui_slt_empleado = $this->render_regex('SLT_EMPLEADO', $gui_slt_empleado, $empleado_collection);

		$render = str_replace('{tbl_salario}', $gui_tbl_salario, $gui);
		$render = str_replace('{slt_empleado}', $gui_slt_empleado, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($salario_collection, $empleado_collection, $obj_salario) {
		$gui = file_get_contents("static/modules/salario/editar.html");
		$gui_tbl_salario = file_get_contents("static/modules/salario/tbl_salario_array.html");
		$gui_slt_empleado = file_get_contents("static/common/slt_empleado.html");
		$gui_tbl_salario = $this->render_regex_dict('TBL_SALARIO', $gui_tbl_salario, $salario_collection);
		$gui_slt_empleado = $this->render_regex('SLT_EMPLEADO', $gui_slt_empleado, $empleado_collection);

		$obj_salario = $this->set_dict($obj_salario);
		$render = str_replace('{tbl_salario}', $gui_tbl_salario, $gui);
		$render = str_replace('{slt_empleado}', $gui_slt_empleado, $render);
		$render = $this->render($obj_salario, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function filtrar_salario($salario_collection) {
		$gui = file_get_contents("static/modules/salario/filtrar_salario.html");
		$gui_tbl_salario = file_get_contents("static/modules/salario/tbl_salario_filtro_array.html");
		$gui_tbl_salario = $this->render_regex_dict('TBL_SALARIO', $gui_tbl_salario, $salario_collection);

		$render = str_replace('{tbl_salario}', $gui_tbl_salario, $gui);
		print $render;
	}
}
?>
