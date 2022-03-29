<?php
require_once 'modules/ingresotipopago/model.php';
require_once 'modules/estadoentrega/model.php';


class DetalleCierreHojaRuta extends StandardObject {
	
	function __construct(IngresoTipoPago $ingresotipopago=NULL, EstadoEntrega $estadoentrega=NULL) {
		$this->detallecierrehojaruta_id = 0;
		$this->importe = 0.00;
		$this->egreso_id = 0;
		$this->cierrehojaruta_id = 0;
		$this->ingresotipopago = $ingresotipopago;
		$this->estadoentrega = $estadoentrega;
	}
}
?>