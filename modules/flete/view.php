<?php


class FleteView extends View {
	function panel() {
		$gui = file_get_contents("static/modules/flete/panel.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function listar($flete_collection) {
		$user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		switch ($user_level) {
			case 2:
				$gui = file_get_contents("static/modules/flete/listar_supervisor.html");
				$tbl_flete_array = file_get_contents("static/modules/flete/tbl_flete_array_supervisor.html");
				break;
			default:
				$gui = file_get_contents("static/modules/flete/listar.html");
				$tbl_flete_array = file_get_contents("static/modules/flete/tbl_flete_array.html");
				break;
		}

		$tbl_flete_array = $this->render_regex_dict('TBL_FLETE', $tbl_flete_array, $flete_collection);
		$render = str_replace('{tbl_flete}', $tbl_flete_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($documentotipo_collection) {
		$gui = file_get_contents("static/modules/flete/agregar.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($documentotipo_collection, $obj_flete) {
		$gui = file_get_contents("static/modules/flete/editar.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_lst_input_infocontacto = file_get_contents("static/modules/flete/lst_input_infocontacto.html");
		
		$infocontacto_collection = $obj_flete->infocontacto_collection;
		$gui_lst_input_infocontacto = $this->render_regex('LST_INPUT_INFOCONTACTO', $gui_lst_input_infocontacto, $infocontacto_collection);
		unset($obj_flete->infocontacto_collection);
		$obj_flete = $this->set_dict($obj_flete);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $gui);
		$render = str_replace('{lst_input_infocontacto}', $gui_lst_input_infocontacto, $render);
		$render = $this->render($obj_flete, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_flete) {
		$gui = file_get_contents("static/modules/flete/consultar.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		if ($obj_flete->documentotipo->denominacion == 'CUIL' OR $obj_flete->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_flete->documento, 0, 2);
			$cuil2 = substr($obj_flete->documento, 2, 8);
			$cuil3 = substr($obj_flete->documento, 10);
			$obj_flete->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_flete->infocontacto_collection;
		unset($obj_flete->infocontacto_collection);	
		$obj_flete = $this->set_dict($obj_flete);

		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = $this->render($obj_flete, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>