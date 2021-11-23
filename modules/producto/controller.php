<?php
require_once "modules/producto/model.php";
require_once "modules/producto/view.php";
require_once "modules/productomarca/model.php";
require_once "modules/productocategoria/model.php";
require_once "modules/productounidad/model.php";
require_once "modules/productodetalle/model.php";
require_once "modules/proveedor/model.php";


class ProductoController {

	function __construct() {
		$this->model = new Producto();
		$this->view = new ProductoView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$this->view->panel();
	}

	function listar() {
		$select = "p.producto_id AS PRODUCTO_ID, p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, ROUND(p.costo, 2) as COSTO, p.iva AS IVA, p.precio_venta AS VENTA, p.ubicacion AS UBICACION";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "p.oculto = 0";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);
		$this->view->listar($producto_collection);
	}

	function ocultos() {
		$select = "p.producto_id AS PRODUCTO_ID, p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, ROUND(p.costo, 2) as COSTO, p.iva AS IVA, p.precio_venta AS VENTA, p.ubicacion AS UBICACION";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "p.oculto = 1";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);
		$this->view->ocultos($producto_collection);
	}

	function lista_precio() {
		$select = "p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, p.costo as COSTO, p.producto_id AS PRODUCTO_ID, p.precio_venta AS VALOR_VENTA, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "p.oculto = 0";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);

		$productomarca_collection = Collector()->get('ProductoMarca');
		foreach ($productomarca_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($productomarca_collection[$clave]);
		}

		$proveedor_collection = Collector()->get('Proveedor');
		foreach ($proveedor_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($proveedor_collection[$clave]);
		}

		$this->view->lista_precio($producto_collection, $productomarca_collection, $proveedor_collection);
	}

	function vdr_lista_precio() {
		$select = "p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, p.producto_id AS PRODUCTO_ID, pm.denominacion AS PROMAR, p.precio_venta AS VALOR_VENTA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "p.oculto = 0";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);

		$productomarca_collection = Collector()->get('ProductoMarca');
		$this->view->vdr_lista_precio($producto_collection, $productomarca_collection);
	}

	function descargar_lista_precio() {
		require_once "tools/excelreport.php";

		$filtro_consulta = filter_input(INPUT_POST, 'filtro');

		$select = "DISTINCT p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, pm.denominacion AS MARCA, p.denominacion AS DENOMINACION, p.producto_id AS PRODUCTO_ID, p.precio_venta AS VALOR_VENTA";

		switch ($filtro_consulta) {
			case 1:

				$proveedor_id = filter_input(INPUT_POST, "proveedor");
				$from = "productodetalle pd, producto p, productocategoria pc, productomarca pm, productounidad pu";
				if ($proveedor_id == 'all') {
					$where = "p.oculto = 0 AND p.productocategoria = pc.productocategoria_id AND p.productomarca = pm.productomarca_id AND p.productounidad = pu.productounidad_id AND p.producto_id = pd.producto_id ORDER BY pm.denominacion ASC, p.denominacion ASC";
					$obj_denominacion = 'TODOS';
				} else {
					$pm = new Proveedor();
					$pm->proveedor_id = $proveedor_id;
					$pm->get();
					$where = "p.oculto = 0 AND p.productocategoria = pc.productocategoria_id AND p.productomarca = pm.productomarca_id AND p.productounidad = pu.productounidad_id AND p.producto_id = pd.producto_id AND pd.productodetalle_id IN (SELECT MAX(sq.productodetalle_id) FROM productodetalle sq WHERE sq.proveedor_id = {$proveedor_id} GROUP BY sq.producto_id) ORDER BY pm.denominacion ASC, p.denominacion ASC";
					$obj_denominacion = $pm->razon_social;
				}

				$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);
				break;
			case 2:
				$productomarca_id = filter_input(INPUT_POST, "productomarca");
				if ($productomarca_id == 'all') {
					$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
					$where = "p.oculto = 0 ORDER BY pm.denominacion ASC, p.denominacion ASC";
					$obj_denominacion = 'TODAS';
					$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);
				} else {
					$pm = new ProductoMarca();
					$pm->productomarca_id = $productomarca_id;
					$pm->get();
					$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
					$where = "p.oculto = 0  AND pm.productomarca_id = {$productomarca_id} ORDER BY pm.denominacion ASC, p.denominacion ASC";
					$obj_denominacion = $pm->denominacion;
					$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);
				}

				break;
			case 3:
				$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
				$where = "p.oculto = 0 ORDER BY pm.denominacion ASC, p.denominacion ASC";
				$obj_denominacion = 'TODOS';
				$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select);
				break;
		}

		$subtitulo = "LISTA DE PRECIO";
		$array_encabezados = array('COD', 'RUBRO', 'MARCA', 'PRODUCTO', 'PRECIO UNITARIO');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		foreach ($producto_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array(
						  $valor["CODIGO"]
						, $valor["CATEGORIA"]
						, $valor["MARCA"]
						, $valor["DENOMINACION"]
						, $valor["VALOR_VENTA"]);
			$array_exportacion[] = $array_temp;
		}

		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function agregar() {
    	SessionHandler()->check_session();
		$productomarca_collection = Collector()->get('ProductoMarca');
		$productocategoria_collection = Collector()->get('ProductoCategoria');
		$productounidad_collection = Collector()->get('ProductoUnidad');
		$proveedor_collection = Collector()->get('Proveedor');

		foreach ($productomarca_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productomarca_collection[$clave]);
		}

		foreach ($productocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productocategoria_collection[$clave]);
		}

		foreach ($productounidad_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productounidad_collection[$clave]);
		}

		foreach ($proveedor_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($proveedor_collection[$clave]);
		}


		$this->view->agregar($productomarca_collection, $productocategoria_collection , $productounidad_collection, $proveedor_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();

		$this->model->producto_id = $arg;
		$this->model->get();

		$select_productoproveedor = "p.producto_id AS PROD_ID, pd.proveedor_id AS PROV_ID, prv.razon_social AS RAZON_SOCIAL";
		$from_productoproveedor = "producto p INNER JOIN productodetalle pd ON p.producto_id = pd.producto_id INNER JOIN proveedor prv ON pd.proveedor_id = prv.proveedor_id ";
		$where_productoproveedor = "p.producto_id = {$arg}";
		$groupby_productoproveedor = "pd.proveedor_id";
		$productodetalle_collection = CollectorCondition()->get('ProductoDetalle', $where_productoproveedor, 4, $from_productoproveedor, $select_productoproveedor, $groupby_productoproveedor);
		$productomarca_collection = Collector()->get('ProductoMarca');
		$productocategoria_collection = Collector()->get('ProductoCategoria');
		$productounidad_collection = Collector()->get('ProductoUnidad');
		$proveedor_collection = Collector()->get('Proveedor');

		foreach ($productomarca_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productomarca_collection[$clave]);
		}

		foreach ($productocategoria_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productocategoria_collection[$clave]);
		}

		foreach ($productounidad_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($productounidad_collection[$clave]);
		}

		$this->view->editar($productomarca_collection, $productocategoria_collection , $productounidad_collection, $productodetalle_collection, $proveedor_collection, $this->model);
	}

	function consultar($arg) {
		SessionHandler()->check_session();

		$select_stock = "s.stock_id AS ID, s.fecha AS FECHA, s.hora AS HORA, CONCAT(s.cantidad_actual, pu.denominacion) AS CANT, s.concepto AS CONCEPTO, CONCAT(s.cantidad_movimiento, pu.denominacion) AS MOVIMIENTO";
		$from_stock = "stock s INNER JOIN producto p ON s.producto_id = p.producto_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where_stock = "s.producto_id = {$arg} ORDER BY s.stock_id DESC";
		$stock_collection = CollectorCondition()->get('Stock', $where_stock, 4, $from_stock, $select_stock);

		if ($stock_collection == 0 || empty($stock_collection) || !is_array($stock_collection)) {
			$stock_collection = array();
		}

		$this->model->producto_id = $arg;
		$this->model->get();
		$this->view->consultar($stock_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();

		$this->model->codigo = filter_input(INPUT_POST, 'codigo');
		$this->model->denominacion = filter_input(INPUT_POST, 'denominacion');
		$this->model->peso = filter_input(INPUT_POST, 'peso');
		$this->model->costo = filter_input(INPUT_POST, 'costo');
		$this->model->descuento = 0;
		$this->model->flete = filter_input(INPUT_POST, 'flete');
		$this->model->porcentaje_ganancia = filter_input(INPUT_POST, 'porcentaje_ganancia');
		$this->model->precio_venta = filter_input(INPUT_POST, 'precio_venta');
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->exento = filter_input(INPUT_POST, 'exento');
		$this->model->no_gravado = filter_input(INPUT_POST, 'no_gravado');
		$this->model->stock_minimo = filter_input(INPUT_POST, 'stock_minimo');
		$this->model->stock_ideal = filter_input(INPUT_POST, 'stock_ideal');
		$this->model->dias_reintegro = filter_input(INPUT_POST, 'dias_reintegro');
		$this->model->unidad_bulto = filter_input(INPUT_POST, 'unidad_bulto');
		$this->model->pisos_pallet = filter_input(INPUT_POST, 'pisos_pallet');
		$this->model->bulto_pallet = filter_input(INPUT_POST, 'bulto_pallet');
		$this->model->ubicacion = filter_input(INPUT_POST, 'ubicacion');
		$this->model->oculto = 0;
		$this->model->barcode = filter_input(INPUT_POST, 'barcode');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->productomarca = filter_input(INPUT_POST, 'productomarca');
		$this->model->productocategoria = filter_input(INPUT_POST, 'productocategoria');
		$this->model->productounidad = filter_input(INPUT_POST, 'productounidad');
		$this->model->save();
		$producto_id = $this->model->producto_id;

		$pdm = new ProductoDetalle();
		$pdm->fecha = date('Y-m-d');
		$pdm->precio_costo = filter_input(INPUT_POST, 'costo');
		$pdm->producto_id = $producto_id;
		$pdm->proveedor_id = filter_input(INPUT_POST, 'proveedor');
		$pdm->save();

		header("Location: " . URL_APP . "/producto/listar");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$producto_id = filter_input(INPUT_POST, 'producto_id');
		$this->model->producto_id = $producto_id;
		$this->model->get();
		$this->model->codigo = filter_input(INPUT_POST, 'codigo');
		$this->model->denominacion = filter_input(INPUT_POST, 'denominacion');
		$this->model->peso = filter_input(INPUT_POST, 'peso');
		$this->model->costo = filter_input(INPUT_POST, 'costo');
		$this->model->descuento = 0;
		$this->model->flete = filter_input(INPUT_POST, 'flete');
		$this->model->porcentaje_ganancia = filter_input(INPUT_POST, 'porcentaje_ganancia');
		$this->model->precio_venta = filter_input(INPUT_POST, 'precio_venta');
		$this->model->iva = filter_input(INPUT_POST, 'iva');
		$this->model->exento = filter_input(INPUT_POST, 'exento');
		$this->model->no_gravado = filter_input(INPUT_POST, 'no_gravado');
		$this->model->stock_minimo = filter_input(INPUT_POST, 'stock_minimo');
		$this->model->stock_ideal = filter_input(INPUT_POST, 'stock_ideal');
		$this->model->dias_reintegro = filter_input(INPUT_POST, 'dias_reintegro');
		$this->model->unidad_bulto = filter_input(INPUT_POST, 'unidad_bulto');
		$this->model->pisos_pallet = filter_input(INPUT_POST, 'pisos_pallet');
		$this->model->bulto_pallet = filter_input(INPUT_POST, 'bulto_pallet');
		$this->model->ubicacion = filter_input(INPUT_POST, 'ubicacion');
		$this->model->barcode = filter_input(INPUT_POST, 'barcode');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->productomarca = filter_input(INPUT_POST, 'productomarca');
		$this->model->productocategoria = filter_input(INPUT_POST, 'productocategoria');
		$this->model->productounidad = filter_input(INPUT_POST, 'productounidad');
		$this->model->save();
		header("Location: " . URL_APP . "/producto/listar");
	}

	function activar($arg) {
		SessionHandler()->check_session();
		$producto_id = $arg;
		$this->model->producto_id = $producto_id;
		$this->model->get();
		$this->model->oculto = 0;
		$this->model->save();
		header("Location: " . URL_APP . "/producto/listar");
	}

	function ocultar($arg) {
		SessionHandler()->check_session();
		$producto_id = $arg;
		$this->model->producto_id = $producto_id;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/producto/listar");
	}

	function asociar_proveedor() {
		$producto_id = filter_input(INPUT_POST, 'producto_id');
		$pdm = new ProductoDetalle();
		$pdm->fecha = filter_input(INPUT_POST, 'fecha');
		$pdm->precio_costo = filter_input(INPUT_POST, 'precio_costo');
		$pdm->producto_id = $producto_id;
		$pdm->proveedor_id = filter_input(INPUT_POST, 'proveedor_id');
		$pdm->save();
		header("Location: " . URL_APP . "/producto/editar/{$producto_id}");
	}

	function proveedor($arg) {
		$ids = explode('@', $arg);
		$producto_id = $ids[0];
		$proveedor_id = $ids[1];

		$pvm = new Proveedor();
		$pvm->proveedor_id = $proveedor_id;
		$pvm->get();

		$pdm = new Producto();
		$pdm->producto_id = $producto_id;
		$pdm->get();

		$select_productodetalle = "pd.productodetalle_id AS ID, pd.fecha AS FECHA, pd.precio_costo AS PRECIO";
		$from_productodetalle = "productodetalle pd";
		$where_productodetalle = "pd.producto_id = {$producto_id} AND pd.proveedor_id = {$proveedor_id} ORDER BY pd.fecha DESC";
		$productodetalle_collection = CollectorCondition()->get('ProductoDetalle', $where_productodetalle, 4, $from_productodetalle, $select_productodetalle);
		$this->view->proveedor($productodetalle_collection, $pdm, $pvm);
	}

	function agregar_precio_proveedor() {
		$producto_id = filter_input(INPUT_POST, 'producto_id');
		$proveedor_id = filter_input(INPUT_POST, 'proveedor_id');

		$pdm = new ProductoDetalle();
		$pdm->fecha = filter_input(INPUT_POST, 'fecha');
		$pdm->precio_costo = filter_input(INPUT_POST, 'precio_costo');
		$pdm->producto_id = $producto_id;
		$pdm->proveedor_id = $proveedor_id;
		$pdm->save();
		header("Location: " . URL_APP . "/producto/proveedor/{$producto_id}@{$proveedor_id}");
	}

	function quitar_precio_proveedor($arg) {
		$pdm = new ProductoDetalle();
		$pdm->productodetalle_id = $arg;
		$pdm->get();
		$producto_id = $pdm->producto_id;
		$proveedor_id = $pdm->proveedor_id;
		$pdm->delete();
		header("Location: " . URL_APP . "/producto/proveedor/{$producto_id}@{$proveedor_id}");
	}

	function buscar_producto() {
    	SessionHandler()->check_session();
		$this->view->buscar_producto();
	}

	function buscar() {
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "p.codigo AS CODIGO, pc.denominacion AS CATEGORIA, CONCAT(pm.denominacion, ' ', p.denominacion) AS DENOMINACION, p.costo as COSTO, p.producto_id AS PRODUCTO_ID, p.precio_venta AS VALOR_VENTA, p.iva AS IVA, p.porcentaje_ganancia AS GANANCIA";
		$from = "producto p INNER JOIN productocategoria pc ON p.productocategoria = pc.productocategoria_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id INNER JOIN productounidad pu ON p.productounidad = pu.productounidad_id";
		$where = "p.denominacion LIKE '%{$buscar}%' OR pm.denominacion LIKE '%{$buscar}%' OR pc.denominacion LIKE '%{$buscar}%'";
		$groupby = "p.producto_id";
		$producto_collection = CollectorCondition()->get('Producto', $where, 4, $from, $select, $groupby);
		$this->view->listar($producto_collection);
	}

	function verifica_existencia_codigo($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "producto p";
		$where = "p.codigo = {$arg}";
		$flag = CollectorCondition()->get('Producto', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}
}
?>