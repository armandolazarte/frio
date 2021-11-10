<?php
require_once "modules/cliente/model.php";
require_once "modules/cliente/view.php";
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/condicionfiscal/model.php";
require_once "modules/vendedor/model.php";
require_once "modules/flete/model.php";
require_once "modules/tipofactura/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/listaprecio/model.php";
require_once "modules/categoriacliente/model.php";


class ClienteController {

	function __construct() {
		$this->model = new Cliente();
		$this->view = new ClienteView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$this->view->panel();
	}

	function listar() {
    	SessionHandler()->check_session();
		$select = "c.cliente_id AS CLIENTE_ID, LPAD(c.cliente_id, 5, 0) AS CODCLI, c.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, c.codigopostal AS CODPOSTAL, CONCAT (c.razon_social, ' (', c.nombre_fantasia, ')') AS RAZON_SOCIAL, cf.denominacion AS CONDICIONFISCAL, ci.denominacion AS CIV, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, CONCAT(fv.denominacion, ' (', fv.dia_1, '-', fv.dia_2, ')') AS FRECUENCIAVENTA, c.iva AS CONDIVA, c.dias_vencimiento_cuenta_corriente AS DVCC, c.descuento AS DESCUENTO,fl.denominacion AS FLETE";
		$from = "cliente c INNER JOIN provincia pr ON c.provincia = pr.provincia_id INNER JOIN condicionfiscal cf ON c.condicionfiscal = cf.condicionfiscal_id INNER JOIN condicioniva ci ON c.condicioniva = ci.condicioniva_id INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN vendedor v ON c.vendedor = v.vendedor_id INNER JOIN frecuenciaventa fv ON c.frecuenciaventa = fv.frecuenciaventa_id INNER JOIN flete fl ON fl.flete_id = c.flete";
		$where = "c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->listar($cliente_collection);
	}

	function ocultos() {
    	SessionHandler()->check_session();
		$select = "c.cliente_id AS CLIENTE_ID, LPAD(c.cliente_id, 5, 0) AS CODCLI, c.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, c.codigopostal AS CODPOSTAL, c.razon_social AS RAZON_SOCIAL, cf.denominacion AS CONDICIONFISCAL, ci.denominacion AS CIV, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, CONCAT(fv.denominacion, ' (', fv.dia_1, '-', fv.dia_2, ')') AS FRECUENCIAVENTA, c.iva AS CONDIVA, c.descuento AS DESCUENTO,fl.denominacion AS FLETE";
		$from = "cliente c INNER JOIN provincia pr ON c.provincia = pr.provincia_id INNER JOIN condicionfiscal cf ON c.condicionfiscal = cf.condicionfiscal_id INNER JOIN condicioniva ci ON c.condicioniva = ci.condicioniva_id INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN vendedor v ON c.vendedor = v.vendedor_id INNER JOIN frecuenciaventa fv ON c.frecuenciaventa = fv.frecuenciaventa_id INNER JOIN flete fl ON fl.flete_id = c.flete";
		$where = "c.oculto = 1";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->ocultos($cliente_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$condicionfiscal_collection = Collector()->get('CondicionFiscal');
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$vendedor_collection = Collector()->get('Vendedor');
		$flete_collection = Collector()->get('Flete');
		$tipofactura_collection = Collector()->get('TipoFactura');
		$listaprecio_collection = Collector()->get('ListaPrecio');
		$categoriacliente_collection = Collector()->get('CategoriaCliente');
		$this->view->agregar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $condicionfiscal_collection,
							 $frecuenciaventa_collection, $vendedor_collection, $flete_collection, $tipofactura_collection,$listaprecio_collection,$categoriacliente_collection);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		$this->model->cliente_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->cliente_id = $arg;
		$this->model->get();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$condicionfiscal_collection = Collector()->get('CondicionFiscal');
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$vendedor_collection = Collector()->get('Vendedor');
		$flete_collection = Collector()->get('Flete');
		$tipofactura_collection = Collector()->get('TipoFactura');
		$listaprecio_collection = Collector()->get('ListaPrecio');
		$categoriacliente_collection = Collector()->get('CategoriaCliente');
		$this->view->editar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $condicionfiscal_collection, $frecuenciaventa_collection, $vendedor_collection, $flete_collection, $tipofactura_collection, $this->model,$listaprecio_collection,$categoriacliente_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		$this->model->razon_social = filter_input(INPUT_POST, 'razon_social');
		$this->model->nombre_fantasia = filter_input(INPUT_POST, 'nombre_fantasia');
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');
		$this->model->impacto_ganancia = filter_input(INPUT_POST, 'impacto_ganancia');
		$this->model->dias_vencimiento_cuenta_corriente = filter_input(INPUT_POST, 'dias_vencimiento_cuenta_corriente');
		$this->model->oculto = 0;
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->ordenentrega = filter_input(INPUT_POST, 'ordenentrega');
		$this->model->entregaminima = filter_input(INPUT_POST, 'entregaminima');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionfiscal = filter_input(INPUT_POST, 'condicionfiscal');
		$this->model->frecuenciaventa = filter_input(INPUT_POST, 'frecuenciaventa');
		$this->model->vendedor = filter_input(INPUT_POST, 'vendedor');
		$this->model->flete = filter_input(INPUT_POST, 'flete');
		$this->model->tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$listaprecio = filter_input(INPUT_POST, 'lista_precio');
		$this->model->listaprecio = filter_input(INPUT_POST, 'lista_precio');
		$this->model->categoriacliente = filter_input(INPUT_POST, 'categoriacliente');
		$this->model->save();
		$cliente_id = $this->model->cliente_id;

		$this->model = new Cliente();
		$this->model->cliente_id = $cliente_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->denominacion = $clave;
				$icm->valor = $valor;
				$icm->save();
				$infocontacto_id = $icm->infocontacto_id;

				$icm = new InfoContacto();
				$icm->infocontacto_id = $infocontacto_id;
				$icm->get();

				$this->model->add_infocontacto($icm);
			}

			$iccm = new InfoContactoCliente($this->model);
			$iccm->save();
		}

		header("Location: " . URL_APP . "/cliente/listar");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$this->model->cliente_id = $cliente_id;
		$this->model->get();
		$this->model->razon_social = filter_input(INPUT_POST, 'razon_social');
		$this->model->nombre_fantasia = filter_input(INPUT_POST, 'nombre_fantasia');
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');
		$this->model->impacto_ganancia = filter_input(INPUT_POST, 'impacto_ganancia');
		$this->model->dias_vencimiento_cuenta_corriente = filter_input(INPUT_POST, 'dias_vencimiento_cuenta_corriente');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->ordenentrega = filter_input(INPUT_POST, 'ordenentrega');
		$this->model->entregaminima = filter_input(INPUT_POST, 'entregaminima');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionfiscal = filter_input(INPUT_POST, 'condicionfiscal');
		$this->model->frecuenciaventa = filter_input(INPUT_POST, 'frecuenciaventa');
		$this->model->vendedor = filter_input(INPUT_POST, 'vendedor');
		$this->model->flete = filter_input(INPUT_POST, 'flete');
		$this->model->tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$this->model->categoriacliente = filter_input(INPUT_POST, 'categoriacliente');
		$this->model->save();

		$this->model = new Cliente();
		$this->model->cliente_id = $cliente_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->infocontacto_id = $clave;
				$icm->get();
				$icm->valor = $valor;
				$icm->save();
			}
		}

		header("Location: " . URL_APP . "/cliente/listar");
	}

	function activar($arg) {
		SessionHandler()->check_session();
		$cliente_id = $arg;
		$this->model->cliente_id = $cliente_id;
		$this->model->get();
		$this->model->oculto = 0;
		$this->model->save();
		header("Location: " . URL_APP . "/cliente/listar");
	}

	function ocultar($arg) {
		SessionHandler()->check_session();
		$cliente_id = $arg;
		$this->model->cliente_id = $cliente_id;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/cliente/listar");
	}

	function buscar() {
		SessionHandler()->check_session();
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "c.cliente_id AS CLIENTE_ID, c.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, c.codigopostal AS CODPOSTAL,
				   c.razon_social AS RAZON_SOCIAL, cf.denominacion AS CONDICIONFISCAL, ci.denominacion AS CIV,
				   CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR,
				   CONCAT(fv.denominacion, ' (', fv.dia_1, '-', fv.dia_2, ')') AS FRECUENCIAVENTA, c.iva AS CONDIVA,
				   c.descuento AS DESCUENTO";
		$from = "cliente c INNER JOIN provincia pr ON c.provincia = pr.provincia_id INNER JOIN
				 condicionfiscal cf ON c.condicionfiscal = cf.condicionfiscal_id INNER JOIN
				 condicioniva ci ON c.condicioniva = ci.condicioniva_id INNER JOIN
				 documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN
				 vendedor v ON c.vendedor = v.vendedor_id INNER JOIN
				 frecuenciaventa fv ON c.frecuenciaventa = fv.frecuenciaventa_id";
		$where = "c.razon_social LIKE '%{$buscar}%' OR c.documento LIKE '%{$buscar}%'";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->listar($cliente_collection);
	}

	function verifica_documento_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "cliente c";
		$where = "c.documento = {$arg}";
		$flag = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}
}
?>
