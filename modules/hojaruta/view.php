<?php


class HojaRutaView extends View {

	function panel($hojaruta_collection) {
		$gui = file_get_contents("static/modules/hojaruta/panel.html");
		$gui_tbl_hojaruta = file_get_contents("static/modules/hojaruta/tbl_hojaruta_array.html");
		$gui_tbl_hojaruta = $this->render_regex_dict('TBL_HOJARUTA', $gui_tbl_hojaruta, $hojaruta_collection);

		$render = str_replace('{tbl_hojaruta_array}', $gui_tbl_hojaruta, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_formulario_entrega_ajax($array_formulario, $obj_hojaruta) {
		$gui = file_get_contents("static/modules/hojaruta/formulario_entrega_ajax.html");
		$obj_hojaruta = $this->set_dict($obj_hojaruta);

		$gui = $this->render_regex_dict('TBL_FORM_ENTREGA', $gui, $array_formulario);
		$gui = $this->render($obj_hojaruta, $gui);
		$gui = str_replace('{url_app}', URL_APP, $gui);
		$gui = str_replace('{url_static}', URL_STATIC, $gui);
		print_r($gui);

	}

	function entregas($array_formulario, $obj_hojaruta,$flete,$cobrador_collection,$monto_total, $monto_contado) {
		$gui = file_get_contents("static/modules/hojaruta/entregas.html");
		$gui_tbl_entregas = file_get_contents("static/modules/hojaruta/tbl_entregas.html");
		$gui_slt_cobrador = file_get_contents("static/modules/entregaclientedetalle/slt_cobrador.html");

		$obj_hojaruta = $this->set_dict($obj_hojaruta);
		$gui = $this->render($obj_hojaruta, $gui);

		$gui_tbl_entregas = $this->render_regex_dict('TBL_FORM_ENTREGA', $gui_tbl_entregas, $array_formulario);
		$gui_slt_cobrador = $this->render_regex('SLT_COBRADOR', $gui_slt_cobrador, $cobrador_collection);

		$render = str_replace('{tbl_entregas}', $gui_tbl_entregas, $gui);
		$render = str_replace('{flete}',$flete, $render);
		$render = str_replace('{slt_cobrador}', $gui_slt_cobrador, $render);
		$render = str_replace('{monto_total}', $monto_total, $render);
		$render = str_replace('{monto_contado}', $monto_contado, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		$render = str_replace('{url_static}', URL_STATIC, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar_hojaruta($obj_hojaruta,$flete,$egreso_collection,$estado) {
		$gui = file_get_contents("static/modules/hojaruta/editar_hojaruta.html");
		$tbl_egreso_array = file_get_contents("static/modules/egreso/tbl_entregaspendientes_array.html");
		$tbl_egreso_array = $this->render_regex_dict('TBL_EGRESO', $tbl_egreso_array, $egreso_collection);

		switch ($estado){
			case 1:
				$modal_array = array('{display_modal}'=>'',
									 '{txt_modal}'=>'');
				break;
			case 2:
				$modal_array = array('{display_modal}'=>'show',
									 '{txt_modal}'=>'Se ha generado la hoja de ruta.');
				break;
			case 3:
				$modal_array = array('{display_modal}'=>'show',
									 '{txt_modal}'=>'Seleccione al menos una entrega pendiente.');
				break;
			case 5:
				$modal_array = array('{display_modal}'=>'show',
									 '{display_btn}'=>'none',
									 '{txt_modal}'=>'Se han confirmado las entregas pendientes.');
				break;
		}

		$obj_hojaruta = $this->set_dict($obj_hojaruta);
		$gui = $this->render($obj_hojaruta, $gui);
		$render = str_replace('{tbl_entregaspendientes}', $tbl_egreso_array, $gui);
		$render = str_replace('{flete}', $flete, $render);
		$render = $this->render($modal_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

}
?>
