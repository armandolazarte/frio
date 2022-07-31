<?php


class CuentaCorrienteClienteCreditoView extends View {
	function consultar($credito_collection, $obj_cliente) {
		$gui = file_get_contents("static/modules/cuentacorrienteclientecredito/consultar.html");
		$tbl_credito = file_get_contents("static/modules/cuentacorrienteclientecredito/tbl_credito.html");
		$tbl_credito = $this->render_regex_dict('TBL_CUENTACORRIENTECLIENTECREDITO', $tbl_credito, $credito_collection);

		$obj_vendedor = $obj_cliente->vendedor;
		$obj_flete = $obj_cliente->flete;
		$infocontacto_collection = $obj_cliente->infocontacto_collection;
		unset($obj_cliente->vendedor, $obj_cliente->flete, $obj_cliente->infocontacto_collection);
		$obj_cliente = $this->set_dict($obj_cliente);

		$render = str_replace('{tbl_credito}', $tbl_credito, $gui);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>