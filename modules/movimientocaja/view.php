<?php


class MovimientoCajaView extends View {
	function panel($movimientocaja_collection, $movimientocajatipo_collection) {
		$gui = file_get_contents("static/modules/movimientocaja/panel.html");
		$tbl_movimientocaja = file_get_contents("static/modules/movimientocaja/tbl_movimientocaja.html");
		$slt_movimientocajatipo = file_get_contents("static/modules/movimientocaja/slt_movimientocajatipo.html");

		$tbl_movimientocaja = $this->render_regex_dict('TBL_MOVIMIENTOCAJA', $tbl_movimientocaja, $movimientocaja_collection);
		$slt_movimientocajatipo = $this->render_regex('SLT_MOVIMIENTOCAJATIPO', $slt_movimientocajatipo, $movimientocajatipo_collection);		

		$render = str_replace('{tbl_movimientocaja}', $tbl_movimientocaja, $gui);
		$render = str_replace('{slt_movimientocajatipo}', $slt_movimientocajatipo, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_movimientocaja_ajax($obj_movimientocaja) {
		$gui = file_get_contents("static/modules/movimientocaja/traer_movimientocaja_ajax.html");
		$obj_movimientocaja = $this->set_dict($obj_movimientocaja);
		$render = $this->render($obj_movimientocaja, $gui);
		print $render;
	}
}
?>