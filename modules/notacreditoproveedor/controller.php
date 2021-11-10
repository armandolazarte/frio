<?php
require_once "modules/notacreditoproveedor/model.php";
require_once "modules/notacreditoproveedor/view.php";
require_once "modules/notacreditoproveedordetalle/model.php";
require_once "modules/ingreso/model.php";


class NotaCreditoProveedorController {

	function __construct() {
		$this->model = new NotaCreditoProveedor();
		$this->view = new NotaCreditoProveedorView();
	}

	function listar() {
    	SessionHandler()->check_session();
		$select = "ncp.fecha AS FECHA, CONCAT(LPAD(ncp.punto_venta, 4, 0), '-', LPAD(ncp.numero_factura, 8, 0)) AS NOTCRE,
    			   CONCAT(LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) END AS REFERENCIA,
    			   p.razon_social AS PROVEEDOR, ncp.importe_total AS IMPORTETOTAL, ncp.notacredito_id AS NOTACREDITO_ID";
		$from = "notacreditoproveedor ncp INNER JOIN ingreso i ON ncp.ingreso_id = i.ingreso_id INNER JOIN 
				 proveedor p ON i.proveedor = p.proveedor_id";
		$notacredito_collection = CollectorCondition()->get('NotaCreditoProveedor', null, 4, $from, $select);
		$this->view->listar($notacredito_collection);
	}

	function buscar() {
    	SessionHandler()->check_session();
		$select = "ncp.fecha AS FECHA, CONCAT(LPAD(ncp.punto_venta, 4, 0), '-', LPAD(ncp.numero_factura, 8, 0)) AS NOTCRE,
    			   CONCAT(LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) END AS REFERENCIA,
    			   p.razon_social AS PROVEEDOR, ncp.importe_total AS IMPORTETOTAL, ncp.notacredito_id AS NOTACREDITO_ID";
		$from = "notacreditoproveedor ncp INNER JOIN ingreso i ON ncp.ingreso_id = i.ingreso_id INNER JOIN 
				 proveedor p ON i.proveedor = p.proveedor_id";

		$tipo_busqueda = filter_input(INPUT_POST, 'tipo_busqueda');
		switch ($tipo_busqueda) {
			case 1:
				$desde = filter_input(INPUT_POST, 'desde');
				$hasta = filter_input(INPUT_POST, 'hasta');
				$where = "ncp.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY ncp.fecha DESC";
				break;
		}
		
		$notacredito_collection = CollectorCondition()->get('NotaCreditoProveedor', $where, 4, $from, $select);
		$this->view->buscar($notacredito_collection);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		require_once 'tools/notacreditoPDFTool.php';
		require_once 'modules/configuracion/model.php';
		require_once "core/helpers/file.php";
		

		$notacreditoproveedor_id = $arg;
		$this->model->notacreditoproveedor_id = $notacreditoproveedor_id;
		$this->model->get();

		$im = new Ingreso();
		$im->ingreso_id = $this->model->ingreso_id;
		$im->get();

		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$select = "ncpd.codigo_producto AS CODIGO, ncpd.descripcion_producto AS DESCRIPCION, ncpd.cantidad AS CANTIDAD,
				   pu.denominacion AS UNIDAD, ncpd.descuento1 AS DESC1, ncpd.descuento2 AS DESC2 , ncpd.descuento3 AS DESC3,
				   ncpd.costo_producto AS COSTO, ROUND(ncpd.importe, 2) AS IMPORTE";
		$from = "notacreditoproveeedordetalle ncpd INNER JOIN producto p ON ncd.producto_id = p.producto_id INNER JOIN
				 productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "ncpd.notacreditoproveedor_id = {$notacreditoproveedor_id}";
		$notacreditodetalle_collection = CollectorCondition()->get('NotaCreditoProveedorDetalle', $where, 4, $from, $select);
		
		$notacreditoPDFHelper = new NotaCreditoPDF();
		$notacreditoPDFHelper->genera_notacredito($notacreditodetalle_collection, $cm, $im, $this->model);
		
		$im = new Ingreso();
		$im->ingreso_id = $this->model->ingreso_id;
		$im->get();
		
		$this->view->consultar($notacreditodetalle_collection, $this->model, $im, $notacreditoproveedor_id);
	}
}
?>