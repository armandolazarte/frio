<?php


class CierreHojaRutaView extends View {

	function panel($cierrehojaruta_collection, $cobrador_collection) {
		$gui = file_get_contents("static/modules/cierrehojaruta/panel.html");
		$gui_slt_cobrador = file_get_contents("static/common/slt_cobrador.html");
		$gui_tbl_cierrehojaruta = file_get_contents("static/modules/cierrehojaruta/tbl_cierrehojaruta.html");

		$cobrador_collection = $this->order_collection_objects($cobrador_collection, 'denominacion', SORT_ASC);
		$gui_slt_cobrador = $this->render_regex('SLT_COBRADOR', $gui_slt_cobrador, $cobrador_collection);
		$gui_tbl_cierrehojaruta = $this->render_regex_dict('TBL_CIERREHOJARUTA', $gui_tbl_cierrehojaruta, $cierrehojaruta_collection);
		$render = str_replace('{tbl_cierrehojaruta}', $gui_tbl_cierrehojaruta, $gui);
		$render = str_replace('{slt_cobrador}', $gui_slt_cobrador, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function consultar($detallecierrehojaruta_collection, $obj_cierrehojaruta) {
		$gui = file_get_contents("static/modules/cierrehojaruta/consultar.html");
		$gui_tbl_detallecierrehojaruta = file_get_contents("static/modules/cierrehojaruta/tbl_detallecierrehojaruta.html");
		$gui_tbl_detallecierrehojaruta = $this->render_regex_dict('TBL_DETALLECIERREHOJARUTA', $gui_tbl_detallecierrehojaruta, $detallecierrehojaruta_collection);

		$obj_cierrehojaruta->rendicion = number_format($obj_cierrehojaruta->rendicion, 2, ',', '.');
		$obj_cierrehojaruta->fecha = $this->reacomodar_fecha($obj_cierrehojaruta->fecha);
		$obj_cierrehojaruta = $this->set_dict($obj_cierrehojaruta);

		$render = str_replace('{tbl_detallecierrehojaruta}', $gui_tbl_detallecierrehojaruta, $gui);
		$render = $this->render($obj_cierrehojaruta, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}	

	function buscar($cierrehojaruta_collection, $cobrador_collection, $obj_cobrador) {
		$gui = file_get_contents("static/modules/cierrehojaruta/buscar.html");
		$gui_slt_cobrador = file_get_contents("static/common/slt_cobrador.html");
		$gui_tbl_cierrehojaruta = file_get_contents("static/modules/cierrehojaruta/tbl_cierrehojaruta.html");


		$obj_cobrador = $this->set_dict($obj_cobrador);
		$cobrador_collection = $this->order_collection_objects($cobrador_collection, 'denominacion', SORT_ASC);
		$gui_slt_cobrador = $this->render_regex('SLT_COBRADOR', $gui_slt_cobrador, $cobrador_collection);
		$gui_tbl_cierrehojaruta = $this->render_regex_dict('TBL_CIERREHOJARUTA', $gui_tbl_cierrehojaruta, $cierrehojaruta_collection);
		$render = str_replace('{tbl_cierrehojaruta}', $gui_tbl_cierrehojaruta, $gui);
		$render = str_replace('{slt_cobrador}', $gui_slt_cobrador, $render);
		$render = $this->render($obj_cobrador, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>