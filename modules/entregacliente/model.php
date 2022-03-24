<?php
require_once "modules/entregaclientedetalle/model.php";

class EntregaCliente  extends StandardObject {

	function __construct() {
		$this->entregacliente_id = 0;
		$this->fecha = '';
		$this->monto = 0;
		$this->estado = 0;
		$this->vendedor_id= 0;
		$this->cliente_id = 0;
		$this->anulada = 0;		
	}

	function getDetalles() {
		$this->entregaclientedetalle_collection = array();
        $sql = "SELECT entregaclientedetalle_id FROM entregaclientedetalle WHERE entregacliente_id=?";
        $datos = array($this->entregacliente_id);
        $resultados = execute_query($sql, $datos);
        if ($resultados) {
            foreach ($resultados as $array) {
                $obj = new EntregaClienteDetalle();
                $obj->entregaclientedetalle_id = $array['entregaclientedetalle_id'];
                $obj->get();
                array_push($this->entregaclientedetalle_collection, $obj);
            }
        }
    }
}
?>
