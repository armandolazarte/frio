<?php
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/condicionfiscal/model.php";
require_once "modules/frecuenciaventa/model.php";
require_once "modules/vendedor/model.php";
require_once "modules/flete/model.php";
require_once "modules/tipofactura/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/listaprecio/model.php";
require_once "modules/categoriacliente/model.php";


class Cliente extends StandardObject {

	function __construct(Provincia $provincia=NULL, DocumentoTipo $documentotipo=NULL, CondicionIVA $condicioniva=NULL, Flete $flete=NULL, CondicionFiscal $condicionfiscal=NULL, FrecuenciaVenta $frecuenciaventa=NULL, Vendedor $vendedor=NULL, TipoFactura $tipofactura=NULL,ListaPrecio $listaprecio=NULL,CategoriaCliente $categoriacliente=NULL) {
		$this->cliente_id = 0;
        $this->razon_social = '';
		$this->nombre_fantasia = '';
        $this->descuento = 0.00;
		$this->iva = 0.00;
		$this->documento = 0;
        $this->domicilio = '';
        $this->codigopostal = 0;
        $this->localidad = 0;
        $this->latitud = '';
        $this->longitud = '';
        $this->impacto_ganancia = 0;
        $this->dias_vencimiento_cuenta_corriente = 0;
        $this->oculto = 0;
		$this->ordenentrega = 0;
		$this->entregaminima = 0;		
		$this->observacion = '';
        $this->provincia = $provincia;
        $this->documentotipo = $documentotipo;
        $this->condicioniva = $condicioniva;
        $this->condicionfiscal = $condicionfiscal;
        $this->frecuenciaventa = $frecuenciaventa;
        $this->vendedor = $vendedor;
        $this->flete = $flete;
		$this->tipofactura = $tipofactura;
		$this->listaprecio = $listaprecio;
		$this->categoriacliente = $categoriacliente;
		$this->infocontacto_collection = array();
	}

	function add_infocontacto(InfoContacto $infocontacto) {
        $this->infocontacto_collection[] = $infocontacto;
    }
}

class InfoContactoCliente {

    function __construct(Cliente $cliente=null) {
        $this->infocontactocliente_id = 0;
        $this->compuesto = $cliente;
        $this->compositor = $cliente->infocontacto_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM infocontactocliente WHERE compuesto=?";
        $datos = array($this->compuesto->cliente_id);
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
        $sql = "INSERT INTO infocontactocliente (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $infocontacto) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->cliente_id;
            $datos[] = $infocontacto->infocontacto_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM infocontactocliente WHERE compuesto=?";
        $datos = array($this->compuesto->cliente_id);
        execute_query($sql, $datos);
    }
}
?>
