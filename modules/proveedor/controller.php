<?php
require_once "modules/proveedor/model.php";
require_once "modules/proveedor/view.php";
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/producto/model.php";
require_once "modules/productodetalle/model.php";


class ProveedorController {

	function __construct() {
		$this->model = new Proveedor();
		$this->view = new ProveedorView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$this->view->panel();
	}

	function listar() {
		$select = "p.proveedor_id AS PROVEEDOR_ID, p.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, p.codigopostal AS CODPOSTAL,
				   p.razon_social AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', p.documento) AS DOCUMENTO";
		$from = "proveedor p INNER JOIN provincia pr ON p.provincia = pr.provincia_id INNER JOIN
				 documentotipo dt ON p.documentotipo = dt.documentotipo_id";
		$where = "p.oculto = 0";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);
		$this->view->listar($proveedor_collection);
	}

	function ocultos() {
		$select = "p.proveedor_id AS PROVEEDOR_ID, p.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, p.codigopostal AS CODPOSTAL,
				   p.razon_social AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', p.documento) AS DOCUMENTO";
		$from = "proveedor p INNER JOIN provincia pr ON p.provincia = pr.provincia_id INNER JOIN
				 documentotipo dt ON p.documentotipo = dt.documentotipo_id";
		$where = "p.oculto = 1";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);
		$this->view->ocultos($proveedor_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();

		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$this->view->agregar($provincia_collection, $documentotipo_collection, $condicioniva_collection);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		$select_productodetalle = "p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION,
				   				   p.costo as COSTO, ROUND((((p.costo * p.iva)/100)+p.costo), 3) AS CMI, p.iva AS IVA, p.producto_id AS PRODUCTO_ID,
				   				   ROUND((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100), 3) AS VG, p.descuento AS DESCUENTO,
				   				   p.porcentaje_ganancia AS GANANCIA, CASE WHEN MOD(@rownum:=@rownum+1,2) = 1 THEN 'even' ELSE 'odd' END AS CLASSTR,
				   				   ROUND((((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100) + ((p.costo * p.iva / 100) + p.costo)) * p.descuento / 100), 3) AS VD,
				   				   ROUND((((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100) + ((p.costo * p.iva / 100) + p.costo)) - (((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100) + ((p.costo * p.iva / 100) + p.costo)) * p.descuento / 100)), 3) AS VENTA";
		$from_productodetalle = "(SELECT @rownum:=0) r, producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN
								 productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN
								 productounidad pu ON p.productounidad = pu.productounidad_id INNER JOIN
								 productodetalle pd ON p.producto_id = pd.producto_id";
		$where_productodetalle = "pd.proveedor_id = {$arg}";
		$groupby_productodetalle = "pd.producto_id";

		$productodetalle_collection = CollectorCondition()->get('ProductoDetalle', $where_productodetalle, 4, $from_productodetalle,
																$select_productodetalle, $groupby_productodetalle);

		$this->model->proveedor_id = $arg;
		$this->model->get();
		$this->view->consultar($productodetalle_collection, $this->model);
	}

	function modificar_lista_precio($arg) {
		SessionHandler()->check_session();
		$ids = explode("@", $arg);
		$proveedor_id = $ids[0];
		if (!isset($ids[1])) {
			$msj_array = array('{mensaje}'=>'',
							   '{display}'=>'');
		} else {
			switch ($ids[1]) {
				case 1:
					$msj_array = array('{mensaje}'=>'Se actualizó la lista de precios!',
							   		   '{display}'=>'show');
					break;
				case 2:
					$msj_array = array('{mensaje}'=>'Por favor seleccione al menos un producto!',
							   		   '{display}'=>'show');
					break;
			}
		}

		$select_productodetalle = "p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION,
				   				   p.costo as COSTO, ROUND((((p.costo * p.iva)/100)+p.costo), 3) AS CMI, p.iva AS IVA, p.producto_id AS PRODUCTO_ID,
				   				   ROUND((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100), 3) AS VG, p.descuento AS DESCUENTO,
				   				   p.porcentaje_ganancia AS GANANCIA, CASE WHEN MOD(@rownum:=@rownum+1,2) = 1 THEN 'even' ELSE 'odd' END AS CLASSTR,
				   				   ROUND((((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100) + ((p.costo * p.iva / 100) + p.costo)) * p.descuento / 100), 3) AS VD,
				   				   ROUND((((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100) + ((p.costo * p.iva / 100) + p.costo)) - (((((p.costo * p.iva / 100) + p.costo) * p.porcentaje_ganancia / 100) + ((p.costo * p.iva / 100) + p.costo)) * p.descuento / 100)), 3) AS VENTA";
		$from_productodetalle = "(SELECT @rownum:=0) r, producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN
								 productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN
								 productounidad pu ON p.productounidad = pu.productounidad_id INNER JOIN
								 productodetalle pd ON p.producto_id = pd.producto_id";
		$where_productodetalle = "pd.proveedor_id = {$proveedor_id}";
		$groupby_productodetalle = "pd.producto_id";

		$productodetalle_collection = CollectorCondition()->get('ProductoDetalle', $where_productodetalle, 4, $from_productodetalle,
																$select_productodetalle, $groupby_productodetalle);

		$this->view->modificar_lista_precio($productodetalle_collection, $msj_array, $proveedor_id);
	}

	function editar($arg) {
		SessionHandler()->check_session();

		$this->model->proveedor_id = $arg;
		$this->model->get();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$condicioniva_collection = Collector()->get('CondicionIVA');
		$this->view->editar($provincia_collection, $documentotipo_collection, $condicioniva_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();

		$this->model->razon_social = filter_input(INPUT_POST, 'razon_social');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->oculto = 0;
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->save();
		$proveedor_id = $this->model->proveedor_id;

		$this->model = new Proveedor();
		$this->model->proveedor_id = $proveedor_id;
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

			$icpm = new InfoContactoProveedor($this->model);
			$icpm->save();
		}

		header("Location: " . URL_APP . "/proveedor/listar");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$proveedor_id = filter_input(INPUT_POST, 'proveedor_id');
		$this->model->proveedor_id = $proveedor_id;
		$this->model->get();
		$this->model->razon_social = filter_input(INPUT_POST, 'razon_social');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->condicioniva = filter_input(INPUT_POST, 'condicioniva');
		$this->model->save();

		$this->model = new Proveedor();
		$this->model->proveedor_id = $proveedor_id;
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

		header("Location: " . URL_APP . "/proveedor/listar");
	}

	function activar($arg) {
		SessionHandler()->check_session();
		$proveedor_id = $arg;
		$this->model->proveedor_id = $proveedor_id;
		$this->model->get();
		$this->model->oculto = 0;
		$this->model->save();
		header("Location: " . URL_APP . "/proveedor/listar");
	}

	function ocultar($arg) {
		SessionHandler()->check_session();
		$proveedor_id = $arg;
		$this->model->proveedor_id = $proveedor_id;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/proveedor/listar");
	}

	function actualizar_lista_precio() {
		$proveedor = filter_input(INPUT_POST, 'proveedor');
		$tipo_modificacion = filter_input(INPUT_POST, 'tipo_modificacion');
		if (!isset($_POST['objeto']) OR empty($_POST['objeto']) OR $_POST['objeto'] == 0) {
			header("Location: " . URL_APP . "/proveedor/modificar_lista_precio/{$proveedor}@2");
		} else {
			$array_productos = $_POST['objeto'];
			$fecha = filter_input(INPUT_POST, 'fecha');
			$porcentaje = filter_input(INPUT_POST, 'porcentaje');

			if ($tipo_modificacion == 1) {
				foreach ($array_productos as $producto_id) {
					$pm = new Producto();
					$pm->producto_id = $producto_id;
					$pm->get();

					$costo = $pm->costo;
					$new_costo = (($porcentaje * $costo) /100) + $costo;
					$pm->costo = $new_costo;
					$pm->save();

					$pdm = new ProductoDetalle();
					$pdm->fecha = $fecha;
					$pdm->precio_costo = $new_costo;
					$pdm->producto_id = $producto_id;
					$pdm->proveedor_id = $proveedor;
					$pdm->save();
				}
			} else {
				foreach ($array_productos as $producto_id) {
					$pm = new Producto();
					$pm->producto_id = $producto_id;
					$pm->get();
					$pm->descuento = $porcentaje;
					$pm->save();
				}
			}

			header("Location: " . URL_APP . "/proveedor/modificar_lista_precio/{$proveedor}@1");
		}
	}

	function buscar() {
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "p.proveedor_id AS PROVEEDOR_ID, p.localidad AS LOCALIDAD, pr.denominacion AS PROVINCIA, p.codigopostal AS CODPOSTAL,
				   p.razon_social AS RAZON_SOCIAL, CONCAT(dt.denominacion, ' ', p.documento) AS DOCUMENTO";
		$from = "proveedor p INNER JOIN provincia pr ON p.provincia = pr.provincia_id INNER JOIN
				 documentotipo dt ON p.documentotipo = dt.documentotipo_id";
		$where = "p.razon_social LIKE '%{$buscar}%' OR p.documento LIKE '%{$buscar}%'";
		$proveedor_collection = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);
		$this->view->listar($proveedor_collection);
	}

	function verifica_documento_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "proveedor p";
		$where = "p.documento = {$arg}";
		$flag = CollectorCondition()->get('Proveedor', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}

	function listar_todos() {
		SessionHandler()->check_session();

		$select = "cpd.numero AS NUMERO,cpd.importe AS IMPORTE,cpd.fecha AS FECHA,tf.denominacion AS TIPOFACTURA,p.razon_social AS PROVEEDOR,
		ccp.referencia AS REFERENCIA";
		$from = "creditoproveedordetalle cpd INNER JOIN tipofactura tf ON tf.tipofactura_id = cpd.tipofactura
		INNER JOIN cuentacorrienteproveedor ccp ON ccp.cuentacorrienteproveedor_id = cpd.cuentacorrienteproveedor_id
		 INNER JOIN proveedor p ON p.proveedor_id = ccp.proveedor_id";
		$notacreditoproveedor = CollectorCondition()->get('CreditoProveeDordetalle', NULL, 4, $from, $select);

 		$this->view->listar_todos($notacreditoproveedor);
	}

	function creditos() {
		SessionHandler()->check_session();
		$periodo_actual = date('Ym');
		$select = "cpd.creditoproveedordetalle_id AS ID,cpd.numero AS NUMERO,cpd.importe AS IMPORTE,cpd.fecha AS FECHA,tf.denominacion AS TIPOFACTURA,p.razon_social AS PROVEEDOR,
		ccp.referencia AS REFERENCIA";
		$from = "creditoproveedordetalle cpd INNER JOIN tipofactura tf ON tf.tipofactura_id = cpd.tipofactura
		INNER JOIN cuentacorrienteproveedor ccp ON ccp.cuentacorrienteproveedor_id = cpd.cuentacorrienteproveedor_id
		 INNER JOIN proveedor p ON p.proveedor_id = ccp.proveedor_id";
		 $where = "date_format(cpd.fecha, '%Y%m') = {$periodo_actual}";
		$notacreditoproveedor = CollectorCondition()->get('CreditoProveeDordetalle', $where, 4, $from, $select);

 		$this->view->creditos($notacreditoproveedor);
	}


	function consultar_notacredito($arg) {
		SessionHandler()->check_session();
		$periodo_actual = date('Ym');
		$select = "cpd.creditoproveedordetalle_id AS ID,cpd.numero AS NUMERO,cpd.importe AS IMPORTE,cpd.fecha AS FECHA,tf.denominacion AS TIPOFACTURA,p.razon_social AS PROVEEDOR,
		ccp.referencia AS REFERENCIA";
		$from = "creditoproveedordetalle cpd INNER JOIN tipofactura tf ON tf.tipofactura_id = cpd.tipofactura
		INNER JOIN cuentacorrienteproveedor ccp ON ccp.cuentacorrienteproveedor_id = cpd.cuentacorrienteproveedor_id
		 INNER JOIN proveedor p ON p.proveedor_id = ccp.proveedor_id";
		 $where = "date_format(cpd.fecha, '%Y%m') = {$periodo_actual}";
		$notacreditoproveedor = CollectorCondition()->get('CreditoProveeDordetalle', $where, 4, $from, $select);

 		$this->view->consultar_notacredito();
	}
}
?>
