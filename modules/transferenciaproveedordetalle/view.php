<?php


class TransferenciaProveedorDetalleView extends View {
	function consultar_pagos($pagos_transferenciaproveedordetalle_collection, $transferenciaproveedordetalle_collection, $obj_proveedor) {
		$gui = file_get_contents("static/modules/transferenciaproveedordetalle/consultar_pagos.html");		
		$tbl_pagos_transferencias_proveedor = file_get_contents("static/modules/transferenciaproveedordetalle/tbl_pagos_transferencias_proveedor.html");
		$tbl_pagos_transferencias_proveedor = $this->render_regex_dict('TBL_TRANSFERENCIAPROVEEDORDETALLE', $tbl_pagos_transferencias_proveedor, $pagos_transferenciaproveedordetalle_collection);

		$tbl_transferenciaproveedordetalle = file_get_contents("static/modules/transferenciaproveedordetalle/tbl_transferenciaproveedordetalle.html");
		$tbl_transferenciaproveedordetalle = $this->render_regex_dict('TBL_TRANSFERENCIAPROVEEDORDETALLE', $tbl_transferenciaproveedordetalle, $transferenciaproveedordetalle_collection);		

		$infocontacto_collection = $obj_proveedor->infocontacto_collection;
		unset($obj_proveedor->infocontacto_collection);
		$obj_proveedor = $this->set_dict($obj_proveedor);

		$render = str_replace('{tbl_pagos_transferencias_proveedor}', $tbl_pagos_transferencias_proveedor, $gui);
		$render = str_replace('{tbl_transferenciaproveedordetalle}', $tbl_transferenciaproveedordetalle, $render);
		$render = $this->render($obj_proveedor, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>