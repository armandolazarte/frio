<?php


class ChequeClienteDetalleView extends View {
	function consultar_pagos_cliente($chequeclientedetalle_collection) {
		$gui = file_get_contents("static/modules/chequeclientedetalle/consultar_pagos_cliente.html");
		$tbl_pagos_cheques_cliente = file_get_contents("static/modules/chequeclientedetalle/tbl_pagos_cheques_cliente.html");
		$tbl_pagos_cheques_cliente = $this->render_regex_dict('TBL_CHEQUECLIENTEDETALLE', $tbl_pagos_cheques_cliente, $chequeclientedetalle_collection);		
		
		$render = str_replace('{tbl_pagos_cheques_cliente}', $tbl_pagos_cheques_cliente, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>