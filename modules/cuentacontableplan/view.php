<?php


class CuentaContablePlanView extends View {
	
	function panel($cuentacontable_collection) {
		$gui = file_get_contents("static/modules/cuentacontable/panel.html");
		$render = $this->render_regex('TBL_CUENTACONTABLE', $gui, $cuentacontable_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function configurar($cuentacontable_collection, $ingresotipopago_collection) {
		$gui = file_get_contents("static/modules/cuentacontableplan/configurar.html");
		$gui_slt_cuentacontable = file_get_contents("static/common/slt_cuentacontable.html");
		$gui_slt_cuentacontable = $this->render_regex('SLT_CUENTACONTABLE', $gui_slt_cuentacontable, $cuentacontable_collection);
		$gui_slt_ingresotipopago = file_get_contents("static/common/slt_ingresotipopago.html");
		$gui_slt_ingresotipopago = $this->render_regex('SLT_INGRESOTIPOPAGO', $gui_slt_ingresotipopago, $ingresotipopago_collection);
		/*
		$obj_cuentacontable = $this->set_dict($obj_cuentacontable);
		$render = $this->render_regex('TBL_CUENTACONTABLE', $gui, $cuentacontable_collection);
		$render = $this->render($obj_cuentacontable, $render);
		*/
		$render = str_replace('{slt_cuentacontable}', $gui_slt_cuentacontable, $gui);
		$render = str_replace('{slt_ingresotipopago}', $gui_slt_ingresotipopago, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function editar($cuentacontable_collection, $obj_cuentacontable) {
		$gui = file_get_contents("static/modules/cuentacontable/editar.html");
		$obj_cuentacontable = $this->set_dict($obj_cuentacontable);
		$render = $this->render_regex('TBL_CUENTACONTABLE', $gui, $cuentacontable_collection);
		$render = $this->render($obj_cuentacontable, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>