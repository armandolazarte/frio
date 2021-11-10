<?php


class ConfiguracionView extends View {
	
	function panel($vendedor_collection, $obj_configuracion, $obj_configuracioncomprobante) {
		$gui = file_get_contents("static/modules/configuracion/panel.html");
		$gui_slt_vendedor = file_get_contents("static/common/slt_vendedor.html");
		foreach ($vendedor_collection as $vendedor) unset($vendedor->infocontacto_collection);
		$gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);

		$obj_configuracioncomprobante->chk_dias_vencimiento_cuentacorrientecliente = ($obj_configuracioncomprobante->dias_vencimiento_cuentacorrientecliente == 0) ? '' : 'checked';
		$obj_configuracioncomprobante->chk_facturacion_rapida = ($obj_configuracioncomprobante->facturacion_rapida == 0) ? '' : 'checked';
		$obj_configuracioncomprobante->txt_parteuno_codebar = ($obj_configuracioncomprobante->parteuno_codebar == 1) ? 'BarCode' : 'Pesaje';
		$obj_configuracioncomprobante->txt_partedos_codebar = ($obj_configuracioncomprobante->partedos_codebar == 1) ? 'BarCode' : 'Pesaje';
		$obj_configuracion = $this->set_dict($obj_configuracion);
		$obj_configuracioncomprobante = $this->set_dict($obj_configuracioncomprobante);
		$render = $this->render($obj_configuracion, $gui);
		$render = $this->render($obj_configuracioncomprobante, $render);
		$render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>