<?php
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/infocontacto/model.php";


class Proveedor extends StandardObject {
	
	function __construct(Provincia $provincia=NULL, DocumentoTipo $documentotipo=NULL, CondicionIVA $condicioniva=NULL) {
		$this->proveedor_id = 0;
		$this->razon_social = '';
		$this->documento = 0;
        $this->domicilio = '';
        $this->codigopostal = 0;
        $this->localidad = 0;
        $this->oculto = 0;
		$this->observacion = '';
        $this->provincia = $provincia;
        $this->documentotipo = $documentotipo;
        $this->condicioniva = $condicioniva;
		$this->infocontacto_collection = array();
	}

	function add_infocontacto(InfoContacto $infocontacto) {
        $this->infocontacto_collection[] = $infocontacto;
    }
}

class InfoContactoProveedor {
    
    function __construct(Proveedor $proveedor=null) {
        $this->infocontactoproveedor_id = 0;
        $this->compuesto = $proveedor;
        $this->compositor = $proveedor->infocontacto_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM infocontactoproveedor WHERE compuesto=?";
        $datos = array($this->compuesto->proveedor_id);
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
        $sql = "INSERT INTO infocontactoproveedor (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $infocontacto) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->proveedor_id;
            $datos[] = $infocontacto->infocontacto_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM infocontactoproveedor WHERE compuesto=?";
        $datos = array($this->compuesto->proveedor_id);
        execute_query($sql, $datos);
    }
}
?>