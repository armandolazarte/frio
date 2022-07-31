<?php


class ChequeClienteDetalleView extends View {
	function consultar_pagos_cliente($pagos_chequeclientedetalle_collection, $chequeclientedetalle_collection, $obj_cliente) {
		$gui = file_get_contents("static/modules/chequeclientedetalle/consultar_pagos_cliente.html");
		$tbl_pagos_cheques_cliente = file_get_contents("static/modules/chequeclientedetalle/tbl_pagos_cheques_cliente.html");
		$tbl_pagos_cheques_cliente = $this->render_regex_dict('TBL_CHEQUECLIENTEDETALLE', $tbl_pagos_cheques_cliente, $chequeclientedetalle_collection);		
		$tbl_chequeclientedetalle = file_get_contents("static/modules/chequeclientedetalle/tbl_chequeclientedetalle.html");
		$tbl_chequeclientedetalle = $this->render_regex_dict('TBL_CHEQUECLIENTEDETALLE', $tbl_chequeclientedetalle, $chequeclientedetalle_collection);		

		$obj_vendedor = $obj_cliente->vendedor;
		$obj_flete = $obj_cliente->flete;
		$infocontacto_collection = $obj_cliente->infocontacto_collection;
		unset($obj_cliente->vendedor, $obj_cliente->flete, $obj_cliente->infocontacto_collection);

		$obj_cliente = $this->set_dict($obj_cliente);

		$render = str_replace('{tbl_pagos_cheques_cliente}', $tbl_pagos_cheques_cliente, $gui);
		$render = str_replace('{tbl_chequeclientedetalle}', $tbl_chequeclientedetalle, $render);
		$render = $this->render($obj_cliente, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>