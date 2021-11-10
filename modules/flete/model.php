<?php
require_once "modules/documentotipo/model.php";
require_once "modules/infocontacto/model.php";


class Flete extends StandardObject {
	
	function __construct(DocumentoTipo $documentotipo=NULL) {
		$this->flete_id = 0;
		$this->denominacion = '';
        $this->documento = 0;
        $this->domicilio = '';
        $this->localidad = 0;
        $this->latitud = '';
        $this->longitud = '';
		$this->observacion = '';
        $this->oculto = 0;
        $this->documentotipo = $documentotipo;
        $this->infocontacto_collection = array();
	}

	function add_infocontacto(InfoContacto $infocontacto) {
        $this->infocontacto_collection[] = $infocontacto;
    }
}

class InfoContactoFlete {
    
    function __construct(Flete $flete=null) {
        $this->infocontactoflete_id = 0;
        $this->compuesto = $flete;
        $this->compositor = $flete->infocontacto_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM infocontactoflete WHERE compuesto=?";
        $datos = array($this->compuesto->flete_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            foreach($resultados as $array) {
                $obj = new InfoContacto();
                $obj->infocontacto_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_infocontacto($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO infocontactoflete (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $infocontacto) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->flete_id;
            $datos[] = $infocontacto->infocontacto_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM infocontactoflete WHERE compuesto=?";
        $datos = array($this->compuesto->flete_id);
        execute_query($sql, $datos);
    }
}
?>