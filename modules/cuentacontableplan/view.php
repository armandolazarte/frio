<?php


class CuentaContablePlanView extends View {
	
	function panel($cuentacontable_collection) {
		$gui = file_get_contents("static/modules/cuentacontable/panel.html");
		$render = $this->render_regex('TBL_CUENTACONTABLE', $gui, $cuentacontable_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function configurar() {
		$gui = file_get_contents("static/modules/cuentacontable/configurar.html");
		/*
		$obj_cuentacontable = $this->set_dict($obj_cuentacontable);
		$render = $this->render_regex('TBL_CUENTACONTABLE', $gui, $cuentacontable_collection);
		$render = $this->render($obj_cuentacontable, $render);
		*/
		$render = $this->render_breadcrumb($gui);
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