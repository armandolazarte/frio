<?php


class MovimientoCajaView extends View {
	function panel($movimientocajatipo_collection) {
		$gui = file_get_contents("static/modules/movimientocaja/panel.html");
		$slt_movimientocajatipo = file_get_contents("static/modules/movimientocaja/slt_movimientocajatipo.html");

		$slt_movimientocajatipo = $this->render_regex('SLT_MOVIMIENTOCAJATIPO', $slt_movimientocajatipo, $movimientocajatipo_collection);		
		$render = str_replace('{slt_movimientocajatipo}', $slt_movimientocajatipo, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>