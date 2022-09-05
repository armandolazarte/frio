<?php


class ChequeProveedorDetalleView extends View {

	function consultar_pagos($pagos_chequeproveedordetalle_collection, $chequeproveedordetalle_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/chequeproveedordetalle/consultar_pagos.html");		
		$tbl_pagos_cheques_proveedor = file_get_contents("static/modules/chequeproveedordetalle/tbl_pagos_cheques_proveedor.html");
		$tbl_pagos_cheques_proveedor = $this->render_regex_dict('TBL_CHEQUEPROVEEDORDETALLE', $tbl_pagos_cheques_proveedor, $pagos_chequeproveedordetalle_collection);

		$tbl_chequeproveedordetalle = file_get_contents("static/modules/chequeproveedordetalle/tbl_chequeproveedordetalle.html");
		$tbl_chequeproveedordetalle = $this->render_regex_dict('TBL_CHEQUEPROVEEDORDETALLE', $tbl_chequeproveedordetalle, $chequeproveedordetalle_collection);		

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		unset($obj_proveedor->infocontacto_collection);

		$obj_proveedor = $this->set_dict($obj_proveedor);
		$render = str_replace('{tbl_pagos_cheques_proveedor}', $tbl_pagos_cheques_proveedor, $gui);
		$render = str_replace('{tbl_chequeproveedordetalle}', $tbl_chequeproveedordetalle, $render);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>