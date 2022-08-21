<?php
require_once 'modules/movimientocajatipo/model.php';


class MovimientoCaja extends StandardObject {
	
	function __construct(MovimientoCajaTipo $movimientocajatipo=NULL) {
		$this->movimientocaja_id = 0;
		$this->fecha = '';
		$this->hora = '';
        $this->numero = '';
        $this->banco = '';
        $this->numero_cuenta = '';
        $this->importe = 0.00;
        $this->detalle = '';
        $this->usuario_id = 0;
        $this->movimientocajatipo = $movimientocajatipo;
	}
}
?>