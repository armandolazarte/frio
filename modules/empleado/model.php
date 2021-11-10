<?php
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";


class Empleado extends StandardObject {
	
	function __construct(Provincia $provincia=NULL, DocumentoTipo $documentotipo=NULL) {
		$this->empleado_id = 0;
                $this->apellido = '';
		$this->nombre = '';
		$this->documento = 0;
                $this->telefono = 0;
                $this->domicilio = '';
                $this->codigopostal = 0;
                $this->localidad = 0;
                $this->observacion = '';
		$this->oculto = 0;
                $this->provincia = $provincia;
                $this->documentotipo = $documentotipo;
	}
}
?>