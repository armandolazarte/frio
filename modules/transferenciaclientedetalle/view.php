<?php


class TransferenciaClienteDetalleView extends View {
	function consultar_pagos_cliente($pagos_transferenciaclientedetalle_collection, $transferenciaclientedetalle_collection, $obj_cliente) {
		$gui = file_get_contents("static/modules/transferenciaclientedetalle/consultar_pagos_cliente.html");		
		$tbl_pagos_transferencias_cliente = file_get_contents("static/modules/transferenciaclientedetalle/tbl_pagos_transferencias_cliente.html");
		$tbl_pagos_transferencias_cliente = $this->render_regex_dict('TBL_TRANSFERENCIACLIENTEDETALLE', $tbl_pagos_transferencias_cliente, $pagos_transferenciaclientedetalle_collection);

		$tbl_transferenciaclientedetalle = file_get_contents("static/modules/transferenciaclientedetalle/tbl_transferenciaclientedetalle.html");
		$tbl_transferenciaclientedetalle = $this->render_regex_dict('TBL_TRANSFERENCIACLIENTEDETALLE', $tbl_transferenciaclientedetalle, $transferenciaclientedetalle_collection);		

		$obj_vendedor = $obj_cliente->vendedor;
		$obj_flete = $obj_cliente->flete;
		$infocontacto_collection = $obj_cliente->infocontacto_collection;
		unset($obj_cliente->vendedor, $obj_cliente->flete, $obj_cliente->infocontacto_collection);

		$obj_cliente = $this->set_dict($obj_cliente);

		$render = str_replace('{tbl_pagos_transferencias_cliente}', $tbl_pagos_transferencias_cliente, $gui);
		$render = str_replace('{tbl_transferenciaclientedetalle}', $tbl_transferenciaclientedetalle, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>