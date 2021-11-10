<?php


class GastoView extends View {
	
	function panel($gasto_collection, $gastocategoria_collection, $sum_gasto) {
		$gui = file_get_contents("static/modules/gasto/panel.html");
		$gui_tbl_gasto = file_get_contents("static/modules/gasto/tbl_gasto_array.html");
		$gui_slt_gastocategoria = file_get_contents("static/modules/gasto/slt_gastocategoria.html");
		$gui_tbl_gasto = $this->render_regex_dict('TBL_GASTO', $gui_tbl_gasto, $gasto_collection);
		$gui_slt_gastocategoria = $this->render_regex('SLT_GASTOCATEGORIA', $gui_slt_gastocategoria, $gastocategoria_collection);
		
		$render = str_replace('{tbl_gasto}', $gui_tbl_gasto, $gui);
		$render = str_replace('{slt_gastocategoria}', $gui_slt_gastocategoria, $render);
		$render = str_replace('{gasto-mensual}', $sum_gasto, $render);
		$render = str_replace('{fecha_sys}', date('Y-m-d'), $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($gasto_collection, $gastocategoria_collection, $obj_gasto) {
		$gui = file_get_contents("static/modules/gasto/editar.html");
		$gui_tbl_gasto = file_get_contents("static/modules/gasto/tbl_gasto_array.html");
		$gui_slt_gastocategoria = file_get_contents("static/modules/gasto/slt_gastocategoria.html");
		$gui_tbl_gasto = $this->render_regex_dict('TBL_GASTO', $gui_tbl_gasto, $gasto_collection);
		$gui_slt_gastocategoria = $this->render_regex('SLT_GASTOCATEGORIA', $gui_slt_gastocategoria, $gastocategoria_collection);
		
		$obj_gasto = $this->set_dict($obj_gasto);
		$render = str_replace('{tbl_gasto}', $gui_tbl_gasto, $gui);
		$render = str_replace('{slt_gastocategoria}', $gui_slt_gastocategoria, $render);
		$render = $this->render($obj_gasto, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>