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
require_once "modules/clientecentral/model.php";
require_once "modules/clientecentralcliente/model.php";
require_once "modules/cuentacorrienteclientecredito/model.php";


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
		$select = "c.cliente_id AS CLIENTE_ID, c.codigo AS CODCLI, c.barrio AS BARRIO, CONCAT (c.razon_social, ' (', c.nombre_fantasia, ')') AS RAZON_SOCIAL, ci.denominacion AS CIV, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, c.iva AS CONDIVA, c.domicilio AS DOMICILIO";
		$from = "cliente c INNER JOIN condicioniva ci ON c.condicioniva = ci.condicioniva_id INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN vendedor v ON c.vendedor = v.vendedor_id";
		$where = "c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->listar($cliente_collection);
	}

	function ocultos() {
    	SessionHandler()->check_session();
		$select = "c.cliente_id AS CLIENTE_ID, c.codigo AS CODCLI, c.barrio AS BARRIO, CONCAT (c.razon_social, ' (', c.nombre_fantasia, ')') AS RAZON_SOCIAL, ci.denominacion AS CIV, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, c.iva AS CONDIVA, c.domicilio AS DOMICILIO";
		$from = "cliente c INNER JOIN condicioniva ci ON c.condicioniva = ci.condicioniva_id INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN vendedor v ON c.vendedor = v.vendedor_id";
		$where = "c.oculto = 1";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->ocultos($cliente_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$vendedor_collection = Collector()->get('Vendedor');
		$flete_collection = Collector()->get('Flete');
		$tipofactura_collection = Collector()->get('TipoFactura');
		$listaprecio_collection = Collector()->get('ListaPrecio');
		$categoriacliente_collection = Collector()->get('CategoriaCliente');

		foreach ($listaprecio_collection as $clave=>$valor) {
			if ($clave->oculto == 1) unset($listaprecio_collection[$clave]);
		}

		$select = "(c.codigo + 1) AS NEW_CODE";
		$from = "cliente c";
		$where = "c.oculto = 0 ORDER BY c.cliente_id DESC LIMIT 1";
		$new_code = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$new_code = (is_array($new_code) AND !empty($new_code)) ? $new_code[0]["NEW_CODE"] : '';

		$this->view->agregar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $frecuenciaventa_collection, $vendedor_collection, $flete_collection, $tipofactura_collection, $listaprecio_collection, $categoriacliente_collection, $new_code);
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
		$frecuenciaventa_collection = Collector()->get('FrecuenciaVenta');
		$vendedor_collection = Collector()->get('Vendedor');
		$flete_collection = Collector()->get('Flete');
		$tipofactura_collection = Collector()->get('TipoFactura');
		$listaprecio_collection = Collector()->get('ListaPrecio');
		$categoriacliente_collection = Collector()->get('CategoriaCliente');

		foreach ($listaprecio_collection as $clave=>$valor) {
			if ($clave->oculto == 1) unset($listaprecio_collection[$clave]);
		}

		$this->view->editar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $frecuenciaventa_collection, $vendedor_collection, $flete_collection, $tipofactura_collection, $this->model,$listaprecio_collection,$categoriacliente_collection);
	}

	function guardar() {
		SessionHandler()->check_session();

		$codigo = filter_input(INPUT_POST, 'codigo');
		$razon_social = filter_input(INPUT_POST, 'razon_social');
		$nombre_fantasia = filter_input(INPUT_POST, 'nombre_fantasia');
		$iva = filter_input(INPUT_POST, 'iva');
		$documento = filter_input(INPUT_POST, 'documento');
		$ingresos_brutos = filter_input(INPUT_POST, 'ingresosbrutos');
		$departamento = filter_input(INPUT_POST, 'departamento');
		$localidad = filter_input(INPUT_POST, 'localidad');
		$documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$provincia = filter_input(INPUT_POST, 'provincia');
		$codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$barrio = filter_input(INPUT_POST, 'barrio');
		$latitud = filter_input(INPUT_POST, 'latitud');
		$longitud = filter_input(INPUT_POST, 'longitud');
		$habilita_cuenta_corriente = filter_input(INPUT_POST, 'habilita_cuenta_corriente');
		$dias_vencimiento_cuenta_corriente = filter_input(INPUT_POST, 'dias_vencimiento_cuenta_corriente');
		$domicilio = filter_input(INPUT_POST, 'domicilio');
		$ordenentrega = filter_input(INPUT_POST, 'ordenentrega');
		$freezer = filter_input(INPUT_POST, 'freezer');
		$horno = filter_input(INPUT_POST, 'horno');
		$observacion = filter_input(INPUT_POST, 'observacion');
		$condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$condicionfiscal = filter_input(INPUT_POST, 'condicioniva');
		$frecuenciaventa = filter_input(INPUT_POST, 'frecuenciaventa');
		$vendedor = filter_input(INPUT_POST, 'vendedor');
		$flete = filter_input(INPUT_POST, 'flete');
		$tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$listaprecio = filter_input(INPUT_POST, 'lista_precio');
		$categoriacliente = filter_input(INPUT_POST, 'categoriacliente');


		$this->model->codigo = (is_null($codigo) OR empty($codigo)) ? 0 : $codigo;
		$this->model->razon_social = (is_null($razon_social) OR empty($razon_social)) ? '-' : $razon_social;
		$this->model->nombre_fantasia = (is_null($nombre_fantasia) OR empty($nombre_fantasia)) ? '-' : $nombre_fantasia;
		$this->model->descuento = 0;
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->documento = (is_null($documento) OR empty($documento)) ? 0 : $documento;
		$this->model->ingresos_brutos = (is_null($ingresos_brutos) OR empty($ingresos_brutos)) ? 0 : $ingresos_brutos;
		$this->model->departamento = (is_null($departamento) OR empty($departamento)) ? '-' : $departamento;
		$this->model->localidad = (is_null($localidad) OR empty($localidad)) ? '-' : $localidad;
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = (is_null($codigopostal) OR empty($codigopostal)) ? 0 : $codigopostal;
		$this->model->barrio = (is_null($barrio) OR empty($barrio)) ? '-' : $barrio;
		$this->model->latitud = (is_null($latitud) OR empty($latitud)) ? 0 : $latitud;
		$this->model->longitud = (is_null($longitud) OR empty($longitud)) ? 0 : $longitud;
		$this->model->impacto_ganancia = 1;
		$this->model->habilita_cuenta_corriente = (is_null($habilita_cuenta_corriente) OR empty($habilita_cuenta_corriente)) ? 0 : $habilita_cuenta_corriente;
		$this->model->dias_vencimiento_cuenta_corriente = (is_null($dias_vencimiento_cuenta_corriente) OR empty($dias_vencimiento_cuenta_corriente)) ? 15 : $dias_vencimiento_cuenta_corriente;
		$this->model->oculto = 0;
		$this->model->domicilio = (is_null($domicilio) OR empty($domicilio)) ? '-' : $domicilio;
		$this->model->ordenentrega = (is_null($ordenentrega) OR empty($ordenentrega)) ? 1 : $ordenentrega;
		$this->model->entregaminima = 100;
		$this->model->freezer = (is_null($freezer) OR empty($freezer)) ? '-' : $freezer;
		$this->model->horno = (is_null($horno) OR empty($horno)) ? '-' : $horno;
		$this->model->observacion = (is_null($observacion) OR empty($observacion)) ? '-' : $observacion;
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionfiscal = filter_input(INPUT_POST, 'condicioniva');
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
		$this->model->codigo = filter_input(INPUT_POST, 'codigo');
		$this->model->razon_social = filter_input(INPUT_POST, 'razon_social');
		$this->model->nombre_fantasia = filter_input(INPUT_POST, 'nombre_fantasia');
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->ingresos_brutos = filter_input(INPUT_POST, 'ingresosbrutos');
		$this->model->departamento = filter_input(INPUT_POST, 'departamento');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->barrio = filter_input(INPUT_POST, 'barrio');
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');
		$this->model->habilita_cuenta_corriente = filter_input(INPUT_POST, 'habilita_cuenta_corriente');
		$this->model->dias_vencimiento_cuenta_corriente = filter_input(INPUT_POST, 'dias_vencimiento_cuenta_corriente');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->ordenentrega = filter_input(INPUT_POST, 'ordenentrega');
		$this->model->entregaminima = 100;
		$this->model->freezer = filter_input(INPUT_POST, 'freezer');
		$this->model->horno = filter_input(INPUT_POST, 'horno');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->condicionfiscal = filter_input(INPUT_POST, 'condicioniva');
		$this->model->frecuenciaventa = filter_input(INPUT_POST, 'frecuenciaventa');
		$this->model->vendedor = filter_input(INPUT_POST, 'vendedor');
		$this->model->flete = filter_input(INPUT_POST, 'flete');
		$this->model->tipofactura = filter_input(INPUT_POST, 'tipofactura');
		$this->model->categoriacliente = filter_input(INPUT_POST, 'categoriacliente');
		$this->model->listaprecio = filter_input(INPUT_POST, 'lista_precio');		
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
		$select = "c.cliente_id AS CLIENTE_ID, c.barrio AS BARRIO, pr.denominacion AS PROVINCIA, c.codigopostal AS CODPOSTAL, c.razon_social AS RAZON_SOCIAL, cf.denominacion AS CONDICIONFISCAL, ci.denominacion AS CIV, CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, CONCAT(fv.denominacion, ' (', fv.dia_1, '-', fv.dia_2, ')') AS FRECUENCIAVENTA, c.iva AS CONDIVA, c.descuento AS DESCUENTO";
		$from = "cliente c INNER JOIN provincia pr ON c.provincia = pr.provincia_id INNER JOIN condicionfiscal cf ON c.condicionfiscal = cf.condicionfiscal_id INNER JOIN condicioniva ci ON c.condicioniva = ci.condicioniva_id INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id INNER JOIN vendedor v ON c.vendedor = v.vendedor_id INNER JOIN frecuenciaventa fv ON c.frecuenciaventa = fv.frecuenciaventa_id";
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

	// GENERADOR DE CLIENTES CENTRALES Y SUCURSALES
	function listar_centrales() {
		SessionHandler()->check_session();
		$select = "cc.clientecentral_id AS CLICENID, cc.denominacion AS DENOMINACION, CASE WHEN cc.cliente_id = 0 THEN 'Sin Definir' ELSE c.razon_social END AS CLIENTE, (SELECT COUNT(ccc.clientecentral_id) FROM clientecentralcliente ccc WHERE ccc.clientecentral_id = cc.clientecentral_id) AS CANT";
		$from = "clientecentral cc LEFT JOIN cliente c ON cc.cliente_id = c.cliente_id";
		$clientecentral_collection = CollectorCondition()->get('ClienteCentral', NULL, 4, $from, $select);

		foreach ($clientecentral_collection as $clave=>$valor) {
			$clientecentral_id = $valor['CLICENID'];

			$select = "ccc.clientecentralcliente_id AS CLICENCLIID, ccc.cliente_id AS CLIID";
			$from = "clientecentralcliente ccc";
			$where = "ccc.clientecentral_id = {$clientecentral_id}";
			$clientecentralcliente_collection = CollectorCondition()->get('ClienteCentralCliente', $where, 4, $from, $select);

			$importe_credito_total = 0;
			if (is_array($clientecentralcliente_collection) AND !empty($clientecentralcliente_collection)) {
				foreach ($clientecentralcliente_collection as $c=>$v) {
					$cliente_id = $v['CLIID'];
					$select = "cccc.cuentacorrienteclientecredito_id AS ID";
					$from = "cuentacorrienteclientecredito cccc";
					$where = "cccc.cliente_id = {$cliente_id} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
					$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
					$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

					if ($max_cuentacorrienteclientecredito_id == 0) {
						$importe_cuentacorrienteclientecredito = 0;
					} else {
						$cccc = new CuentaCorrienteClienteCredito();
						$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
						$cccc->get();
						$importe_cuentacorrienteclientecredito = $cccc->importe;
					}
			
					$importe_credito_total = $importe_credito_total + $importe_cuentacorrienteclientecredito;
				}
			}

			$clientecentral_collection[$clave]['CREDITO'] = number_format($importe_credito_total, 2, ',', '.');
		}

		$this->view->listar_centrales($clientecentral_collection);
	}

	function crear_clientecentral() {
		SessionHandler()->check_session();
		$select = "c.cliente_id AS CLIENTE_ID, c.codigo AS CODCLI, c.barrio AS BARRIO, CONCAT (c.razon_social, ' (', c.nombre_fantasia, ')') AS RAZON_SOCIAL, c.domicilio AS DOMICILIO";
		$from = "cliente c";
		$where = "c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
		$this->view->crear_clientecentral($cliente_collection);
	}

	function guardar_clientecentral() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

		$ccm = new ClienteCentral();
		$ccm->fecha_creacion = date('Y-m-d');
		$ccm->fecha_modificacion = date('Y-m-d');
		$ccm->denominacion = filter_input(INPUT_POST, 'denominacion');
		$ccm->cliente_id = 0;
		$ccm->usuario_id = $usuario_id;
		$ccm->save();
		$clientecentral_id = $ccm->clientecentral_id;

		$clientes_ids = $_POST['clientes'];
		foreach ($clientes_ids as $cliente_id) {
			$cccm = new ClienteCentralCliente();
			$cccm->fecha_creacion = date('Y-m-d');
			$cccm->fecha_modificacion = date('Y-m-d');
			$cccm->clientecentral_id = $clientecentral_id;
			$cccm->cliente_id = $cliente_id;
			$cccm->save();
		}
		
		header("Location: " . URL_APP . "/cliente/consultar_clientecentral/{$clientecentral_id}");
	}

	function consultar_clientecentral($arg) {
		SessionHandler()->check_session();
		$clientecentral_id = $arg;
		$ccm = new ClienteCentral();
		$ccm->clientecentral_id = $clientecentral_id;
		$ccm->get();
		$cliente_seleccionado = $ccm->cliente_id;

		$select = "ccc.clientecentralcliente_id AS CLICENCLIID, c.codigo AS COD, c.razon_social AS RAZSOC, CONCAT(c.barrio, ' - ', c.domicilio) AS DOMICILIO, ccc.cliente_id AS CLIID";
		$from = "clientecentralcliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.clientecentral_id = {$clientecentral_id}";
		$clientecentralcliente_collection = CollectorCondition()->get('ClienteCentralCliente', $where, 4, $from, $select);

		$importe_credito_total = 0;
		foreach ($clientecentralcliente_collection as $clave=>$valor) {
			$cliente_id = $valor['CLIID'];
			if ($cliente_seleccionado == $cliente_id) {
				$clientecentralcliente_collection[$clave]['DISPLAY_SELECT'] = 'inline-block';
				$clientecentralcliente_collection[$clave]['DISPLAY_BTN'] = 'none';
			} else {
				$clientecentralcliente_collection[$clave]['DISPLAY_SELECT'] = 'none';
				$clientecentralcliente_collection[$clave]['DISPLAY_BTN'] = 'inline-block';
			}

			$select = "cccc.cuentacorrienteclientecredito_id AS ID";
			$from = "cuentacorrienteclientecredito cccc";
			$where = "cccc.cliente_id = {$cliente_id} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
			$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
			$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

			if ($max_cuentacorrienteclientecredito_id == 0) {
				$importe_cuentacorrienteclientecredito = 0;
			} else {
				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
				$cccc->get();
				$importe_cuentacorrienteclientecredito = $cccc->importe;
			}
			
			$importe_credito_total = $importe_credito_total + $importe_cuentacorrienteclientecredito;
			$clientecentralcliente_collection[$clave]['CREDITO'] = number_format($importe_cuentacorrienteclientecredito, 2, ',', '.');
		}

		$ccm->credito_disponible = number_format($importe_credito_total, 2, ',', '.');

		if ($cliente_seleccionado != 0) {
			$cm = new Cliente();
			$cm->cliente_id = $cliente_seleccionado;
			$cm->get();

			$ccm->cliente_razonsocial = $cm->razon_social;
		} else {
			$ccm->cliente_razonsocial = 'Sin Definir';
		}

		$select = "c.cliente_id AS CLIENTE_ID, c.codigo AS CODCLI, c.barrio AS BARRIO, CONCAT (c.razon_social, ' (', c.nombre_fantasia, ')') AS RAZON_SOCIAL, c.domicilio AS DOMICILIO";
		$from = "cliente c";
		$where = "c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);

		$this->view->consultar_clientecentral($clientecentralcliente_collection, $cliente_collection, $ccm);
	}

	function eliminar_clientecentralcliente($arg) {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$ids = explode('@', $arg);
		$clientecentral_id = $ids[0];
		$clientecentralcliente_id = $ids[1];

		$ccm = new ClienteCentral();
		$ccm->clientecentral_id = $clientecentral_id;
		$ccm->get();
		$ccm->fecha_modificacion = date('Y-m-d');
		$ccm->usuario_id = $usuario_id;
		$ccm->save();

		$cccm = new ClienteCentralCliente();
		$cccm->clientecentralcliente_id = $clientecentralcliente_id;
		$cccm->delete();

		header("Location: " . URL_APP . "/cliente/consultar_clientecentral/{$clientecentral_id}");
	}

	function seleccionar_clientecentralcliente_credito($arg) {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$ids = explode('@', $arg);
		$clientecentral_id = $ids[0];
		$clientecentralcliente_id = $ids[1];

		$cccm = new ClienteCentralCliente();
		$cccm->clientecentralcliente_id = $clientecentralcliente_id;		
		$cccm->get();
		$cliente_id = $cccm->cliente_id;
		
		$ccm = new ClienteCentral();
		$ccm->clientecentral_id = $clientecentral_id;
		$ccm->get();
		$ccm->cliente_id = $cliente_id;
		$ccm->fecha_modificacion = date('Y-m-d');
		$ccm->usuario_id = $usuario_id;
		$ccm->save();

		header("Location: " . URL_APP . "/cliente/consultar_clientecentral/{$clientecentral_id}");
	}

	function actualizar_clientecentralcliente($arg) {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$ids = explode('@', $arg);
		$clientecentral_id = $ids[0];
		$cliente_id = $ids[1];

		$ccm = new ClienteCentral();
		$ccm->clientecentral_id = $clientecentral_id;
		$ccm->get();
		$ccm->fecha_modificacion = date('Y-m-d');
		$ccm->usuario_id = $usuario_id;
		$ccm->save();

		$cccm = new ClienteCentralCliente();
		$cccm->fecha_creacion = date('Y-m-d');
		$cccm->fecha_modificacion = date('Y-m-d');
		$cccm->clientecentral_id = $clientecentral_id;
		$cccm->cliente_id = $cliente_id;
		$cccm->save();

		header("Location: " . URL_APP . "/cliente/consultar_clientecentral/{$clientecentral_id}");
	}
}
?>