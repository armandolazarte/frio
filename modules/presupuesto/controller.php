<?php
require_once "modules/presupuesto/model.php";
require_once "modules/presupuesto/view.php";
require_once "modules/producto/model.php";
require_once "modules/cliente/model.php";
require_once "modules/vendedor/model.php";
require_once "modules/stock/model.php";
require_once "modules/presupuestodetalle/model.php";
require_once "modules/configuracion/model.php";
require_once "modules/configuracioncomprobante/model.php";
require_once "modules/usuariodetalle/model.php";


class PresupuestoController {

	function __construct() {
		$this->model = new Presupuesto();
		$this->view = new PresupuestoView();
	}

	function listar($arg) {
    	SessionHandler()->check_session();
    	$periodo_actual = date('Ym');
    	$select = "p.presupuesto_id AS PRESUPUESTO_ID, CONCAT(date_format(p.fecha, '%d/%m/%Y'), ' ', LEFT(p.hora,5)) AS FECHA, UPPER(cl.razon_social) AS CLIENTE,
    			   CONCAT(LPAD(p.punto_venta, 4, 0), '-', LPAD(p.numero_factura, 8, 0)) AS FACTURA, p.subtotal AS SUBTOTAL,
    			   p.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR";
		$from = "presupuesto p INNER JOIN cliente cl ON p.cliente = cl.cliente_id INNER JOIN 
				 vendedor ve ON p.vendedor = ve.vendedor_id";
		$where = "date_format(p.fecha, '%Y%m') = {$periodo_actual} ORDER BY p.fecha DESC";
		$presupuesto_collection = CollectorCondition()->get('Presupuesto', $where, 4, $from, $select);
		switch ($arg) {
			case 1:
				$array_msj = array('{mensaje}'=>'[INFO] Se ha registrado el presupuesto',
								   '{class}'=>'info',
								   '{display}'=>'block');
				break;
			case 2:
				$array_msj = array('{mensaje}'=>'[INFO] Se ha editado un registro de presupuesto',
								   '{class}'=>'info',
								   '{display}'=>'block');
				break;
			case 3:
				$array_msj = array('{mensaje}'=>'[ERROR] No se ha podido registrar el presupuesto. No posee conexión.',
								   '{class}'=>'danger',
								   '{display}'=>'block');
				break;
			case 4:
				$array_msj = array('{mensaje}'=>'[ERROR] No se ha podido registrar el presupuesto.',
								   '{class}'=>'danger',
								   '{display}'=>'block');
				break;
			default:
				$array_msj = array('{mensaje}'=>'',
								   '{class}'=>'info',
								   '{display}'=>'none');
				break;
		}

		$this->view->listar($presupuesto_collection, $array_msj);
	}

	function presupuestar() {
    	SessionHandler()->check_session();
		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, 
				   pc.denominacion AS CATEGORIA, p.codigo AS CODIGO";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN
				 productomarca pm ON p.productomarca = pm.productomarca_id";
		$where = "p.oculto = 0 AND p.producto_id != 344";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select, $groupby);

		$select = "c.cliente_id AS CLIENTE_ID, LPAD(c.cliente_id, 5, 0) AS CODCLI, CONCAT(c.razon_social, '(', c.nombre_fantasia, ')') AS RAZON_SOCIAL,  
				   CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO";
		$from = "cliente c INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id";
		$where = "c.oculto = 0";
		$cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);

		$select = "v.vendedor_id AS VENDEDOR_ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION,  
				   CONCAT(dt.denominacion, ' ', v.documento) AS DOCUMENTO";
		$from = "vendedor v INNER JOIN documentotipo dt ON v.documentotipo = dt.documentotipo_id";
		$vendedor_collection = CollectorCondition()->get('Vendedor', NULL, 4, $from, $select);

		$select = "(MAX(p.numero_factura) + 1 ) AS SIGUIENTE_NUMERO ";
		$from = "presupuesto p";
		$siguiente_presupuesto = CollectorCondition()->get('Presupuesto', NULL, 4, $from, $select);
		$siguiente_presupuesto = (!is_array($siguiente_presupuesto)) ? 1 : $siguiente_presupuesto[0]['SIGUIENTE_NUMERO'];
		$siguiente_presupuesto = (is_null($siguiente_presupuesto)) ? 1 : $siguiente_presupuesto;

		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();
		
		$array_presupuesto = array("{punto_venta}"=>str_pad($cm->punto_venta, 4, '0', STR_PAD_LEFT),
							  	   "{numero_remito}"=>str_pad($siguiente_presupuesto, 8, '0', STR_PAD_LEFT));
		
		$this->view->presupuestar($producto_collection, $cliente_collection, $vendedor_collection, $array_presupuesto);
		
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		require_once 'tools/facturaPDFTool.php';
		require_once 'modules/configuracion/model.php';
		
		$presupuesto_id = $arg;
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$this->model->presupuesto_id = $presupuesto_id;
		$this->model->get();
		$documentotipo_denominacion = $this->model->cliente->documentotipo->denominacion;
		$this->model->cliente_documentotipo = $documentotipo_denominacion;
		$vendedor = $this->model->vendedor->apellido . ' ' . $this->model->vendedor->nombre;
		
		$select = "pd.codigo_producto AS CODIGO, pd.descripcion_producto AS DESCRIPCION, pd.cantidad AS CANTIDAD,
				   pu.denominacion AS UNIDAD, pd.descuento AS DESCUENTO, pd.valor_descuento AS VD, 
				   pd.costo_producto AS COSTO, ROUND(pd.importe, 2) AS IMPORTE, pd.iva AS IVA";
		$from = "presupuestodetalle pd INNER JOIN producto p ON pd.producto_id = p.producto_id INNER JOIN
				 productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "pd.presupuesto_id = {$presupuesto_id}";
		$presupuestodetalle_collection = CollectorCondition()->get('PresupuestoDetalle', $where, 4, $from, $select);
		
		$facturaPDFHelper = new FacturaPDF();
		@$facturaPDFHelper->presupuestoP($presupuestodetalle_collection, $cm, $this->model, $vendedor);			
		
		$this->model = new Presupuesto();
		$this->model->presupuesto_id = $presupuesto_id;
		$this->model->get();

		$this->view->consultar($presupuestodetalle_collection, $this->model);
	}

	function ver_pdf() {
		require_once "core/helpers/file.php";
	}
	
	function editar($arg) {
    	SessionHandler()->check_session();
		
		$this->model->presupuesto_id = $arg;
		$this->model->get();

		$select = "p.producto_id AS PRODUCTO_ID, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, 
				   pc.denominacion AS CATEGORIA, p.codigo AS CODIGO, p.stock_minimo AS STMINIMO, p.stock_ideal AS STIDEAL, 
				   p.costo as COSTO, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA, (((p.costo * p.porcentaje_ganancia)/100)+p.costo) AS VENTA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN
				 productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id LEFT JOIN
				 productodetalle pd ON p.producto_id = pd.producto_id LEFT JOIN proveedor prv ON pd.proveedor_id = prv.proveedor_id";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', NULL, 4, $from, $select, $groupby);

		$select = "c.cliente_id AS CLIENTE_ID, LPAD(c.cliente_id, 5, 0) AS CODCLI, CONCAT(c.razon_social, '(', c.nombre_fantasia, ')') AS RAZON_SOCIAL,
				   CONCAT(dt.denominacion, ' ', c.documento) AS DOCUMENTO";
		$from = "cliente c INNER JOIN documentotipo dt ON c.documentotipo = dt.documentotipo_id";
		$cliente_collection = CollectorCondition()->get('Cliente', NULL, 4, $from, $select);

		$select = "v.vendedor_id AS VENDEDOR_ID, CONCAT(v.apellido, ' ', v.nombre) AS DENOMINACION,  
				   CONCAT(dt.denominacion, ' ', v.documento) AS DOCUMENTO";
		$from = "vendedor v INNER JOIN documentotipo dt ON v.documentotipo = dt.documentotipo_id";
		$vendedor_collection = CollectorCondition()->get('Vendedor', NULL, 4, $from, $select);

		$select_presupuestos = "pd.codigo_producto AS CODIGO, pd.descripcion_producto AS DESCRIPCION,
						   		pd.cantidad AS CANTIDAD, pu.denominacion AS UNIDAD, pd.descuento AS DESCUENTO, 
						   		pd.costo_producto AS COSTO, pd.importe AS IMPORTE, pd.presupuestodetalle_id AS PRESUPUESTODETALLEID, 
						   		pd.producto_id AS PRODUCTO, pd.valor_descuento AS VD, pd.iva AS IVA";
		$from_presupuestos = "presupuestodetalle pd INNER JOIN producto p ON pd.producto_id = p.producto_id INNER JOIN
						  	  productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_presupuestos = "pd.presupuesto_id = {$arg}";
		$presupuestodetalle_collection = CollectorCondition()->get('PresupuestoDetalle', $where_presupuestos, 4, $from_presupuestos, $select_presupuestos);
		
		$this->view->editar($producto_collection, $cliente_collection, $vendedor_collection,
							$presupuestodetalle_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();

		$com = new Configuracion();
		$com->configuracion_id = 1;
		$com->get();
		$punto_venta = $com->punto_venta;

		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();
		
		$numero_factura = filter_input(INPUT_POST, 'numero_factura');
		$explode_numero = explode('-', $numero_factura);
		$num_factura = $explode_numero[1];
		$fecha = filter_input(INPUT_POST, 'fecha');
		$hora = date('H:i:s');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($num_factura, 8, '0', STR_PAD_LEFT);

		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$vm = new Vendedor();
		$vm->vendedor_id = $vendedor_id;
		$vm->get();

		$cliente_id = filter_input(INPUT_POST, 'cliente');
		$cm = new Cliente();
		$cm->cliente_id = $cliente_id;
		$cm->get();
		$importe_total = filter_input(INPUT_POST, 'importe_total');
		$this->model->punto_venta = $punto_venta;
		$this->model->numero_factura = str_replace('0', '', $num_factura);
		$this->model->fecha = $fecha;
		$this->model->hora = $hora;
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->subtotal = filter_input(INPUT_POST, 'subtotal');
		$this->model->importe_total = $importe_total;
		$this->model->cliente = $cliente_id;
		$this->model->vendedor = $vendedor_id;
		$this->model->save();
		$presupuesto_id = $this->model->presupuesto_id;

		$presupuestos_array = $_POST['presupuesto'];
		$presupuestodetalle_ids = array();
		foreach ($presupuestos_array as $presupuesto) {
			$producto_id = $presupuesto['producto_id'];
			$cantidad = $presupuesto['cantidad'];
			$costo_producto = $presupuesto['costo'];
			$valor_descuento = $presupuesto['importe_descuento'];
			$importe = $presupuesto['costo_total'];
			
			$pm = new Producto();
			$pm->producto_id = $producto_id;
			$pm->get();

			$pdm = new PresupuestoDetalle();
			$pdm->codigo_producto = $presupuesto['codigo'];
			$pdm->descripcion_producto = $presupuesto['descripcion'];
			$pdm->cantidad = $cantidad;
			$pdm->valor_descuento = $valor_descuento;
			$pdm->descuento = $presupuesto['descuento'];
			$pdm->costo_producto = $costo_producto;
			$pdm->iva = $presupuesto['iva'];
			$pdm->importe = $importe;
			$pdm->producto_id = $presupuesto['producto_id'];
			$pdm->presupuesto_id = $presupuesto_id;
			$pdm->save();
			$presupuestodetalle_ids[] = $edm->presupuestodetalle_id;
		}

		header("Location: " . URL_APP . "/presupuesto/consultar/{$presupuesto_id}");
	}
	
	function actualizar() {
		SessionHandler()->check_session();

		$presupuesto_id = filter_input(INPUT_POST, 'presupuesto_id');
		$this->model->presupuesto_id = $presupuesto_id;
		$this->model->get();

		$punto_venta = filter_input(INPUT_POST, 'punto_venta');
		$numero_factura = filter_input(INPUT_POST, 'numero_factura');
		$fecha = filter_input(INPUT_POST, 'fecha');
		$hora = date('H:i:s');
		$comprobante = str_pad($punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($numero_factura, 8, '0', STR_PAD_LEFT);

		$importe_total = filter_input(INPUT_POST, 'importe_total');
		$this->model->fecha = $fecha;
		$this->model->hora = $hora;
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->subtotal = filter_input(INPUT_POST, 'subtotal');
		$this->model->importe_total = $importe_total;
		$this->model->cliente = filter_input(INPUT_POST, 'cliente');
		$this->model->vendedor = filter_input(INPUT_POST, 'vendedor');
		$this->model->save();
		
		$select_presupuestos = "pd.presupuestodetalle_id AS ID,pd.producto_id AS PRODUCTO_ID, pd.codigo_producto AS CODIGO, 
						   pd.cantidad AS CANTIDAD";
		$from_presupuestos = "presupuestodetalle pd INNER JOIN producto p ON pd.producto_id = p.producto_id";
		$where_presupuestos = "pd.presupuesto_id = {$presupuesto_id}";
		$presupuestodetalle_collection = CollectorCondition()->get('PresupuestoDetalle', $where_presupuestos, 4, $from_presupuestos, $select_presupuestos);		

		if (!empty($presupuestodetalle_collection) AND is_array($presupuestodetalle_collection)) {
			foreach ($presupuestodetalle_collection as $presupuestodetalle) {
				$temp_presupuestodetalle_id = $presupuestodetalle['ID'];
				$pdm = new PresupuestoDetalle();
				$pdm->presupuestodetalle_id = $temp_presupuestodetalle_id;
				$pdm->delete();
			}
		}

		$presupuestos_array = $_POST['presupuesto'];
		foreach ($presupuestos_array as $presupuesto) {
			$producto_id = $presupuesto['producto_id'];
			$cantidad = $presupuesto['cantidad'];
			$costo_producto = $presupuesto['costo'];
			$valor_descuento = $presupuesto['importe_descuento'];
			$importe = $presupuesto['costo_total'];
			
			$pm = new Producto();
			$pm->producto_id = $producto_id;
			$pm->get();
			
			$pdm = new PresupuestoDetalle();
			$pdm->codigo_producto = $presupuesto['codigo'];
			$pdm->descripcion_producto = $presupuesto['descripcion'];
			$pdm->cantidad = $presupuesto['cantidad'];
			$pdm->valor_descuento = $presupuesto['importe_descuento'];
			$pdm->descuento = $presupuesto['descuento'];
			$pdm->costo_producto = $presupuesto['costo'];
			$pdm->iva = $presupuesto['iva'];
			$pdm->importe = $presupuesto['costo_total'];
			$pdm->producto_id = $presupuesto['producto_id'];
			$pdm->presupuesto_id = $presupuesto_id;
			$pdm->save();
		}

		header("Location: " . URL_APP . "/presupuesto/consultar/{$presupuesto_id}");
	}

	function buscar() {
		SessionHandler()->check_session();

    	$select = "p.presupuesto_id AS PRESUPUESTO_ID, CONCAT(date_format(p.fecha, '%d/%m/%Y'), ' ', LEFT(p.hora,5)) AS FECHA, UPPER(cl.razon_social) AS CLIENTE,
    			   CONCAT(LPAD(p.punto_venta, 4, 0), '-', LPAD(p.numero_factura, 8, 0)) AS FACTURA, p.subtotal AS SUBTOTAL,
    			   p.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR";
		$from = "presupuesto p INNER JOIN cliente cl ON p.cliente = cl.cliente_id INNER JOIN 
				 vendedor ve ON p.vendedor = ve.vendedor_id";
		$presupuesto_collection = CollectorCondition()->get('Presupuesto', $where, 4, $from, $select);

		$tipo_busqueda = filter_input(INPUT_POST, 'tipo_busqueda');
		switch ($tipo_busqueda) {
			case 1:
				$desde = filter_input(INPUT_POST, 'desde');
				$hasta = filter_input(INPUT_POST, 'hasta');
				$where = "p.fecha BETWEEN '{$desde}' AND '{$hasta}' ORDER BY p.fecha DESC";
				break;
			default:
				$where = "date_format(p.fecha, '%Y%m') = {$periodo_actual} ORDER BY p.fecha DESC";
				break;
		}
		
		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
		$this->view->buscar($egreso_collection);
	}
	
	function traer_formulario_producto_ajax($arg) {
		$producto_id = $arg;
		$pm = new Producto();
		$pm->producto_id = $producto_id;
		$pm->get();
		$select = "MAX(s.stock_id) AS MAXID";
		$from = "stock s";
		$where = "s.producto_id = {$producto_id}";
		$stock_id = CollectorCondition()->get('Stock', $where, 4, $from, $select);
		$stock_id = $stock_id[0]['MAXID'];

		$sm = new Stock();
		$sm->stock_id = $stock_id;
		$sm->get();
		$cantidad_disponible = $sm->cantidad_actual;

		$this->view->traer_formulario_producto_ajax($pm, $cantidad_disponible);
	}

	function traer_costo_producto_ajax($arg) {
		$pm = new Producto();
		$pm->producto_id = $arg;
		$pm->get();
		$costo = $pm->costo;
		print $costo;
	}

	function traer_descripcion_cliente_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$denominacion = $cm->documento . ' @ ' . $cm->razon_social;
		print $denominacion;
	}

	function traer_cliente_vendedor_ajax($arg) {
		$cm = new Cliente();
		$cm->cliente_id = $arg;
		$cm->get();
		$vendedor = $cm->vendedor;
		$denominacion = $vendedor->apellido . ' ' . $vendedor->nombre . '@' . $vendedor->vendedor_id;
		print $denominacion;
	}

	function traer_descripcion_vendedor_ajax($arg) {
		$vm = new Vendedor();
		$vm->vendedor_id = $arg;
		$vm->get();
		$denominacion = $vm->documentotipo->denominacion . ' ' . $vm->documento . ' - ';
		$denominacion .= $vm->apellido . ' ' .$vm->nombre;
		print $denominacion;
	}

	function traer_siguiente_numero_factura_ajax($arg) {
		$cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();
        
		$select = "(MAX(p.numero_factura) + 1 ) AS SIGUIENTE_NUMERO ";
		$from = "presupuesto p";
		$nuevo_numero = CollectorCondition()->get('Presupuesto', $where, 4, $from, $select, $groupby);
		$nuevo_numero = (!is_array($nuevo_numero)) ? 1 : $nuevo_numero[0]['SIGUIENTE_NUMERO'];

		$siguiente_factura = str_pad($cm->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
    	$siguiente_factura .= str_pad($nuevo_numero, 8, '0', STR_PAD_LEFT);
	
		print $siguiente_factura;
	}

	function modal_mensaje_formulario_ajax($arg) {
		$cliente_id = $arg;
		$this->view->modal_mensaje_formulario_ajax($cliente_id);
	}
}
?>