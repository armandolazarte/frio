<?php
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/frecuenciaventa/model.php";
require_once "modules/infocontacto/model.php";


class Vendedor extends StandardObject {
	
	function __construct(Provincia $provincia=NULL, DocumentoTipo $documentotipo=NULL, CondicionIVA $condicioniva=NULL,
                         FrecuenciaVenta $frecuenciaventa=NULL) {
		$this->vendedor_id = 0;
        $this->apellido = '';
		$this->nombre = '';
        $this->comision = 0.00;
		$this->documento = 0;
        $this->domicilio = '';
        $this->codigopostal = 0;
        $this->localidad = 0;
        $this->latitud = '';
        $this->longitud = '';
        $this->observacion = '';
		$this->oculto = 0;
        $this->provincia = $provincia;
        $this->documentotipo = $documentotipo;
        $this->frecuenciaventa = $frecuenciaventa;
		$this->infocontacto_collection = array();
	}

	function add_infocontacto(InfoContacto $infocontacto) {
        $this->infocontacto_collection[] = $infocontacto;
    }
}

class InfoContactoVendedor {
    
    function __construct(Vendedor $vendedor=null) {
        $this->infocontactovendedor_id = 0;
        $this->compuesto = $vendedor;
        $this->compositor = $vendedor->infocontacto_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM infocontactovendedor WHERE compuesto=?";
        $datos = array($this->compuesto->vendedor_id);
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
        $sql = "INSERT INTO infocontactovendedor (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $infocontacto) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->vendedor_id;
            $datos[] = $infocontacto->infocontacto_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM infocontactovendedor WHERE compuesto=?";
        $datos = array($this->compuesto->vendedor_id);
        execute_query($sql, $datos);
    }
}
?>