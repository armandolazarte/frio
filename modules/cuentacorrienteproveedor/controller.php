<?php
require_once "modules/cuentacorrienteproveedor/model.php";
require_once "modules/cuentacorrienteproveedor/view.php";
require_once "modules/proveedor/model.php";
require_once "modules/ingreso/model.php";
require_once "modules/tipomovimientocuenta/model.php";
require_once "modules/ingresotipopago/model.php";
require_once "modules/chequeproveedordetalle/model.php";
require_once "modules/creditoproveedordetalle/model.php";
require_once "modules/transferenciaproveedordetalle/model.php";
require_once "modules/cuentacorrienteproveedorcredito/model.php";
require_once "tools/cuentaCorrienteProveedorPDFTool.php";


class CuentaCorrienteProveedorController {

	function __construct() {
		$this->model = new CuentaCorrienteProveedor();
		$this->view = new CuentaCorrienteProveedorView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$select = "ccp.proveedor_id AS PID, p.razon_social AS PROVEEDOR, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.proveedor_id";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN ccp.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN ccp.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrienteproveedor ccp";
		$totales_array = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select);

		$proveedor_collection = Collector()->get('Proveedor');
		$this->view->panel($cuentacorriente_collection, $totales_array, $proveedor_collection);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
    	$proveedor_id = $arg;
    	$select = "ccp.proveedor_id AS PID, p.razon_social AS PROVEEDOR, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.proveedor_id";
		$cuentascorrientes_collection = CollectorCondition()->get('CuentaCorrienteProveedor', NULL, 4, $from, $select, $groupby);
		
    	$pm = new Proveedor();
    	$pm->proveedor_id = $arg;
    	$pm->get();
    	
		$select = "date_format(ccp.fecha, '%d/%m/%Y') AS FECHA, ccp.importe AS IMPORTE, ccp.ingreso AS INGRESO, SUBSTRING(tmc.denominacion,1,3) AS MOVIMIENTO, ccp.ingreso_id AS IID, ccp.referencia AS REFERENCIA, CASE ccp.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, CASE ccp.tipomovimientocuenta WHEN 1 THEN 'down' WHEN 2 THEN 'up' END AS MOVICON, ccp.cuentacorrienteproveedor_id CCPID, ingresotipopago AS ING_TIP_PAG, ccp.proveedor_id AS PROID";
		$from = "cuentacorrienteproveedor ccp INNER JOIN tipomovimientocuenta tmc ON ccp.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccp.proveedor_id = {$arg} AND ccp.estadomovimientocuenta != 4";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

		$ingreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$temp_cuentacorrienteproveedor_id = $valor['CCPID'];
			$ingreso_id = $valor['IID'];
			$ingresotipopago_id = $valor['ING_TIP_PAG'];
			if (!in_array($ingreso_id, $ingreso_ids)) $ingreso_ids[] = $ingreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, IF (ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);
			
			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			$cuentacorriente_collection[$clave]['BTN_DISPLAY'] = $array_temp[0]['BTN_DISPLAY'];

			switch ($ingresotipopago_id) {
				case 1:
					$select = "cpd.chequeproveedordetalle_id AS ID";
					$from = "chequeproveedordetalle cpd";
					$where = "cpd.cuentacorrienteproveedor_id = {$temp_cuentacorrienteproveedor_id}";
					$chequeproveedordetalle_id = CollectorCondition()->get('ChequeProveedorDetalle', $where, 4, $from, $select);
					$chequeproveedordetalle_id = (is_array($chequeproveedordetalle_id) AND !empty($chequeproveedordetalle_id)) ? $chequeproveedordetalle_id[0]['ID'] : 0;

					if ($chequeproveedordetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $chequeproveedordetalle_id;
					} else {
						$btn_display_ver_tipopago = 'none';
						$btn_tipopago_id = '#';
						$btn_movimiento_id = '#';
					}
					break;
				case 2:
					$select = "tpd.transferenciaproveedordetalle_id AS ID";
					$from = "transferenciaproveedordetalle tpd";
					$where = "tpd.cuentacorrienteproveedor_id = {$temp_cuentacorrienteproveedor_id}";
					$transferenciaproveedordetalle_id = CollectorCondition()->get('TransferenciaProveedorDetalle', $where, 4, $from, $select);
					$transferenciaproveedordetalle_id = (is_array($transferenciaproveedordetalle_id) AND !empty($transferenciaproveedordetalle_id)) ? $transferenciaproveedordetalle_id[0]['ID'] : 0;

					if ($transferenciaproveedordetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $transferenciaproveedordetalle_id;
					} else {
						$btn_display_ver_tipopago = 'none';
						$btn_tipopago_id = '#';
						$btn_movimiento_id = '#';
					}
					break;
				default:
					$btn_display_ver_tipopago = 'none';
					$btn_tipopago_id = '#';
					$btn_movimiento_id = '#';
					break;
			}
			
			$cuentacorriente_collection[$clave]['DISPLAY_VER_TIPOPAGO'] = $btn_display_ver_tipopago;
			$cuentacorriente_collection[$clave]['BTN_TIPOPAGO_ID'] = $btn_tipopago_id;
			$cuentacorriente_collection[$clave]['MOVID'] = $btn_movimiento_id;
		}

		$max_cuentacorrienteproveedor_ids = array();
		foreach ($ingreso_ids as $ingreso_id) {
			$select = "ccp.cuentacorrienteproveedor_id AS ID";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id} ORDER BY ccp.cuentacorrienteproveedor_id DESC LIMIT 1";
			$max_id = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
			if (!in_array($max_id[0]['ID'], $max_cuentacorrienteproveedor_ids)) $max_cuentacorrienteproveedor_ids[] = $max_id[0]['ID'];
		}

		foreach ($cuentacorriente_collection as $clave=>$valor) {
			if (!in_array($valor['CCPID'], $max_cuentacorrienteproveedor_ids)) $cuentacorriente_collection[$clave]['BTN_DISPLAY'] = 'none';
		}
			
		$select = "(SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 1 AND  dccp.proveedor_id = ccp.proveedor_id) AS DEUDA, (SELECT ROUND(SUM(dccp.importe),2) FROM cuentacorrienteproveedor dccp WHERE dccp.tipomovimientocuenta = 2 AND dccp.proveedor_id = ccp.proveedor_id) AS INGRESO";
		$from = "cuentacorrienteproveedor ccp INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$where = "ccp.proveedor_id = {$arg}";
		$groupby = "ccp.proveedor_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select, $groupby);

		$select = "cccp.cuentacorrienteproveedorcredito_id AS ID";
		$from = "cuentacorrienteproveedorcredito cccp";
		$where = "cccp.proveedor_id = {$proveedor_id} ORDER BY cccp.cuentacorrienteproveedorcredito_id DESC LIMIT 1";
		$max_cuentacorrienterpoveedorcredito_id = CollectorCondition()->get('CuentaCorrienteProveedorCredito', $where, 4, $from, $select);
		$max_cuentacorrienterpoveedorcredito_id = (is_array($max_cuentacorrienterpoveedorcredito_id) AND !empty($max_cuentacorrienterpoveedorcredito_id)) ? $max_cuentacorrienterpoveedorcredito_id[0]['ID'] : 0;
		print_r($max_cuentacorrienterpoveedorcredito_id);exit;
		if ($max_cuentacorrienterpoveedorcredito_id == 0) {
			$importe_cuentacorrienterpoveedorcredito = 0;
		} else {
			$cccp = new CuentaCorrienteProveedorCredito();
			$cccp->cuentacorrienteproveedorcredito_id = $max_cuentacorrienteproveedorcredito_id;
			$cccp->get();
			$importe_cuentacorrienteproveedorcredito = $cccp->importe;
		}

		$this->view->consultar($cuentascorrientes_collection, $cuentacorriente_collection, $montos_cuentacorriente, $pm, $importe_cuentacorrienteproveedorcredito);
	}

	function listar_cuentas($arg) {
    	SessionHandler()->check_session();
		
    	$pm = new Proveedor();
    	$pm->proveedor_id = $arg;
    	$pm->get();
    	
		$select = "date_format(ccp.fecha, '%d/%m/%Y') AS FECHA, ccp.importe AS IMPORTE, ccp.ingreso AS INGRESO, ccp.ingreso_id AS IID, tmc.denominacion AS MOVIMIENTO, ccp.referencia AS REFERENCIA, CASE ccp.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ccp.cuentacorrienteproveedor_id CCPID";
		$from = "cuentacorrienteproveedor ccp INNER JOIN tipomovimientocuenta tmc ON ccp.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccp.proveedor_id = {$arg}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

		$ingreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$ingreso_id = $valor['IID'];
			if (!in_array($ingreso_id, $ingreso_ids)) $ingreso_ids[] = $ingreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  	  (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);
			
			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			
		}

		$max_cuentacorrienteproveedor_ids = array();
		foreach ($ingreso_ids as $ingreso_id) {
			$select = "ccp.cuentacorrienteproveedor_id AS ID";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id} ORDER BY ccp.cuentacorrienteproveedor_id DESC LIMIT 1";
			$max_id = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
			if (!in_array($max_id[0]['ID'], $max_cuentacorrienteproveedor_ids)) $max_cuentacorrienteproveedor_ids[] = $max_id[0]['ID'];
		}

		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$cuentacorrienteproveedor_id = $valor["CCPID"];
			if (!in_array($cuentacorrienteproveedor_id, $max_cuentacorrienteproveedor_ids)) unset($cuentacorriente_collection[$clave]);
		}
		
		$this->view->listar_cuentas($cuentacorriente_collection, $pm);
	}

	function traer_listado_movimientos_cuentacorriente_ajax($arg) {
    	SessionHandler()->check_session();
		$ingreso_id = $arg;
		$select = "date_format(ccp.fecha, '%d/%m/%Y') AS FECHA, ccp.importe AS IMPORTE, ccp.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccp.ingreso_id AS IID, ccp.referencia AS REFERENCIA, CASE ccp.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ccp.cuentacorrienteproveedor_id CCPID";
		$from = "cuentacorrienteproveedor ccp INNER JOIN tipomovimientocuenta tmc ON ccp.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccp.ingreso_id = {$ingreso_id}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		
		foreach ($cuentacorriente_collection as $clave=>$valor) {
		
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  	  (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, 'inline-block' AS BTN_DISPLAY";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'blue' : 'red';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			$cuentacorriente_collection[$clave]['BTN_DISPLAY'] = $array_temp[0]['BTN_DISPLAY'];
		}

		$this->view->traer_listado_movimientos_cuentacorriente_ajax($cuentacorriente_collection);
	}

	function traer_chequeproveedordetalle_ajax($arg) {
    	SessionHandler()->check_session();
		$ids = explode('@', $arg);
		$chequeproveedordetalle_id = $ids[0];
		$proveedor_id = $ids[1];
		$cpdm = new ChequeProveedorDetalle();
		$cpdm->chequeproveedordetalle_id = $chequeproveedordetalle_id;
		$cpdm->get();
		$this->view->traer_chequeproveedordetalle_ajax($cpdm, $proveedor_id);
	}

	function traer_transferenciaproveedordetalle_ajax($arg) {
    	SessionHandler()->check_session();
		$transferenciaproveedordetalle_id = $arg;
		$tpdm = new TransferenciaProveedorDetalle();
		$tpdm->transferenciaproveedordetalle_id = $transferenciaproveedordetalle_id;
		$tpdm->get();
		$this->view->traer_transferenciaproveedordetalle_ajax($tpdm);
	}

	function buscar() {
    	SessionHandler()->check_session();
		$argumento = filter_input(INPUT_POST, 'proveedor');		
		
		if ($argumento == 'all') {
			$prewhere = "";
		} else {
			$prewhere = "AND p.proveedor_id = {$argumento}";
		}

		$select = "i.ingreso_id, date_format(i.fecha, '%d/%m/%Y') AS FECHA, CONCAT(LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, p.razon_social AS PROVEEDOR, p.localidad AS BARRIO, p.domicilio AS DOMICILIO, ((IF((SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id) IS NULL, 0, (SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id))) - (SELECT ROUND(SUM(ccpd.importe),2) FROM cuentacorrienteproveedor ccpd WHERE ccpd.tipomovimientocuenta = 1 AND ccpd.ingreso_id = ccp.ingreso_id)) AS BALANCE";
		$from = "cuentacorrienteproveedor ccp INNER JOIN ingreso i ON ccp.ingreso_id = i.ingreso_id INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$where = "((IF((SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id) IS NULL, 0, (SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id))) - (SELECT ROUND(SUM(ccpd.importe),2) FROM cuentacorrienteproveedor ccpd WHERE ccpd.tipomovimientocuenta = 1 AND ccpd.ingreso_id = ccp.ingreso_id)) < -0.5 {$prewhere}";
		$groupby = "ccp.ingreso_id ORDER BY i.fecha ASC";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select, $groupby);

		$proveedor_collection = Collector()->get('Proveedor');
		$this->view->buscar($cuentacorriente_collection, $proveedor_collection, $argumento);
	}

	function descargar_cuentacorriente_pdf($arg) {
		SessionHandler()->check_session();
		
		$select = "i.ingreso_id, date_format(i.fecha, '%d/%m/%Y') AS FECHA, CONCAT(LPAD(i.punto_venta, 4, 0), '-', LPAD(i.numero_factura, 8, 0)) AS FACTURA, p.razon_social AS PROVEEDOR, p.localidad AS LOCALIDAD, p.domicilio AS DOMICILIO, ((IF((SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id) IS NULL, 0, (SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id))) - (SELECT ROUND(SUM(ccpd.importe),2) FROM cuentacorrienteproveedor ccpd WHERE ccpd.tipomovimientocuenta = 1 AND ccpd.ingreso_id = ccp.ingreso_id)) AS BALANCE";
		$from = "cuentacorrienteproveedor ccp INNER JOIN ingreso i ON ccp.ingreso_id = i.ingreso_id INNER JOIN proveedor p ON ccp.proveedor_id = p.proveedor_id";
		$groupby = "ccp.ingreso_id ORDER BY i.fecha ASC";
		switch ($arg) {
			case 'all':
				$where = "((IF((SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id) IS NULL, 0, (SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id))) - (SELECT ROUND(SUM(ccpd.importe),2) FROM cuentacorrienteproveedor ccpd WHERE ccpd.tipomovimientocuenta = 1 AND ccpd.ingreso_id = ccp.ingreso_id)) < -0.5";
				break;
			default:
				$proveedor_id = $arg;
				$where = "((IF((SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id) IS NULL, 0, (SELECT ROUND(SUM(ccpia.importe),2) FROM cuentacorrienteproveedor ccpia WHERE ccpia.tipomovimientocuenta = 2 AND ccpia.ingreso_id = ccp.ingreso_id))) - (SELECT ROUND(SUM(ccpd.importe),2) FROM cuentacorrienteproveedor ccpd WHERE ccpd.tipomovimientocuenta = 1 AND ccpd.ingreso_id = ccp.ingreso_id)) < -0.5 AND p.proveedor_id = {$proveedor_id}";
				break;
		}

		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select, $groupby);
		$cuentaCorrienteProveedorPDFHelper = new CuentaCorrienteProveedorPDF();
		$cuentaCorrienteProveedorPDFHelper->descarga_cuentascorrientes($cuentacorriente_collection);
	}

	function guardar_ingreso() {
		SessionHandler()->check_session();

		$proveedor_id = filter_input(INPUT_POST, 'proveedor_id');
		$ingreso_id = filter_input(INPUT_POST, 'ingreso_id');
		$im = new Ingreso();
		$im->ingreso_id = $ingreso_id;
		$im->get();

		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->hora = date('H:i:s');
		$this->model->referencia = $im->referencia;
		$this->model->importe = filter_input(INPUT_POST, 'importe');
		$this->model->ingreso = filter_input(INPUT_POST, 'ingreso');
		$this->model->proveedor_id = $proveedor_id;
		$this->model->ingreso_id = $ingreso_id;
		$this->model->tipomovimientocuenta = 2;
		$this->model->estadomovimientocuenta = 2;
		$this->model->save();

		header("Location: " . URL_APP . "/cuentacorrienteproveedor/consultar/{$proveedor_id}");
	}

	function guardar_ingreso_cuentacorriente() {
		SessionHandler()->check_session();

		$cuentacorrienteproveedor_id = filter_input(INPUT_POST, 'cuentacorrienteproveedor_id');
		$importe = filter_input(INPUT_POST, 'importe');
		$importe_original = filter_input(INPUT_POST, 'importe');
		$proveedor_id = filter_input(INPUT_POST, 'proveedor_id');
		$ingreso_id = filter_input(INPUT_POST, 'ingreso_id');

		$im = new Ingreso();
		$im->ingreso_id = $ingreso_id;
		$im->get();

		$comprobante = str_pad($im->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($im->numero_factura, 8, '0', STR_PAD_LEFT);

		$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.ingreso_id = {$ingreso_id}";
		$balance = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

		$deuda = abs($balance[0]['BALANCE']) - $importe;
		if ($deuda > 0) {
			$estadomovimientocuenta = 3;
		} else {
			$select = "ccp.cuentacorrienteproveedor_id AS ID";
			$from = "cuentacorrienteproveedor ccp";
			$where = "ccp.ingreso_id = {$ingreso_id} AND ccp.estadomovimientocuenta IN (1,2,3)";
			$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
			$estadomovimientocuenta = 4;
			
			foreach ($cuentacorriente_collection as $cuentacorrienteproveedor) {
				$cuentacorrienteproveedor_id = $cuentacorrienteproveedor['ID'];
				$ccpm = new CuentaCorrienteProveedor();
				$ccpm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
				$ccpm->get();
				$ccpm->estadomovimientocuenta = 4;
				$ccpm->save();
			}
		}

		$ingresotipopago_id = filter_input(INPUT_POST, 'ingresotipopago');
		$this->model = new CuentaCorrienteProveedor();
		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->hora = date('H:i:s');
		$this->model->referencia = "Pago de comprobante {$comprobante}";
		$this->model->importe = $importe;
		$this->model->ingreso = $importe;
		$this->model->proveedor_id = $proveedor_id;
		$this->model->ingreso_id = $ingreso_id;
		$this->model->ingresotipopago = $ingresotipopago_id;
		$this->model->tipomovimientocuenta = 2;
		$this->model->estadomovimientocuenta = $estadomovimientocuenta;
		$this->model->save();
		$cuentacorrienteproveedor_id = $this->model->cuentacorrienteproveedor_id;

		switch ($ingresotipopago_id) {
			case 1:
				$cpdm = new ChequeProveedorDetalle();
				$cpdm->numero = filter_input(INPUT_POST, 'numero_cheque');
				$cpdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
				$cpdm->fecha_pago = null;
				$cpdm->banco = filter_input(INPUT_POST, 'banco');
				$cpdm->plaza = filter_input(INPUT_POST, 'plaza');
				$cpdm->titular = filter_input(INPUT_POST, 'titular');
				$cpdm->documento = filter_input(INPUT_POST, 'documento');
				$cpdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
				$cpdm->estado = 1;
				$cpdm->importe = $importe_original;
				$cpdm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
				$cpdm->save();
				break;
			case 2:
				$tpdm = new TransferenciaProveedorDetalle();
				$tpdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
				$tpdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
				$tpdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
				$tpdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
				$tpdm->importe = $importe_original;
				$tpdm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
				$tpdm->save();
				break;
			case 4:
				$cpdm = new CreditoProveedorDetalle();
				$cpdm->numero = filter_input(INPUT_POST, 'numero_nc');
				$cpdm->importe = $importe;
				$cpdm->fecha = filter_input(INPUT_POST, 'fecha_nc');
				$cpdm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
				$cpdm->tipofactura = filter_input(INPUT_POST, 'tipofactura_nc');
				$cpdm->save();
				break;
		}

		header("Location: " . URL_APP . "/cuentacorrienteproveedor/consultar/{$proveedor_id}");
	}

	function guardar_credito_cuentacorriente() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$ingresotipopago_id = filter_input(INPUT_POST, 'ingresotipopago');
		$importe = filter_input(INPUT_POST, 'importe');
		$importe_movimiento = filter_input(INPUT_POST, 'importe');
		$proveedor_id = filter_input(INPUT_POST, 'proveedor_id');
		
		if ($ingresotipopago_id == 1) {
			$numero_cheque = filter_input(INPUT_POST, 'numero_cheque'); 
			$cpdm = new ChequeProveedorDetalle();
			$cpdm->numero = filter_input(INPUT_POST, 'numero_cheque');
			$cpdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
			$cpdm->fecha_pago = date('Y-m-d');
			$cpdm->banco = filter_input(INPUT_POST, 'banco');
			$cpdm->plaza = filter_input(INPUT_POST, 'plaza');
			$cpdm->titular = filter_input(INPUT_POST, 'titular');
			$cpdm->documento = filter_input(INPUT_POST, 'documento');
			$cpdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
			$cpdm->estado = 2;
			$cpdm->importe = $importe;
			$cpdm->cuentacorrienteproveedor_id = 0;
			$cpdm->save();
			$chequeproveedordetalle_id = $cpdm->chequeproveedordetalle_id;
			$referencia = "Crédito de pago con Cheque N° {$numero_cheque}";
		} else {
			$numero_transferencia = filter_input(INPUT_POST, 'numero_transferencia'); 
			$tcdm = new TransferenciaProveedorDetalle();
			$tcdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
			$tcdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
			$tcdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
			$tcdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
			$tcdm->importe = $importe;
			$tcdm->cuentacorrienteproveedor_id = 0;
			$tcdm->save();	
			$transferenciaproveedordetalle_id = $tcdm->transferenciaproveedordetalle_id;
			$referencia = "Crédito de pago con Transferencia N° {$numero_transferencia}";
		}	

		$select = "ccpc.cuentacorrienteproveedorcredito_id AS ID";
		$from = "cuentacorrienteproveedorcredito ccpc";
		$where = "ccpc.proveedor_id = {$proveedor_id} ORDER BY ccpc.cuentacorrienteproveedorcredito_id DESC LIMIT 1";
		$max_cuentacorrienteproveedorcredito_id = CollectorCondition()->get('CuentaCorrienteProveedorCredito', $where, 4, $from, $select);
		$max_cuentacorrienteproveedorcredito_id = (is_array($max_cuentacorrienteproveedorcredito_id) AND !empty($max_cuentacorrienteproveedorcredito_id)) ? $max_cuentacorrienteproveedorcredito_id[0]['ID'] : 0;

		if ($max_cuentacorrienteproveedorcredito_id == 0) {
			$ccpc = new CuentaCorrienteProveedorCredito();
			$ccpc->fecha = date('Y-m-d');
			$ccpc->hora = date('H:i:s');
			$ccpc->referencia = $referencia;
			$ccpc->importe = $importe;
			$ccpc->movimiento = $importe;
			$ccpc->cuentacorrienteproveedor_id = 0;
			$ccpc->ingreso_id = 0;
			$ccpc->proveedor_id = $proveedor_id;
			$ccpc->chequeproveedordetalle_id = ($ingresotipopago_id == 1) ? $chequeproveedordetalle_id : 0;
			$ccpc->transferenciaproveedordetalle_id = ($ingresotipopago_id == 2) ? $transferenciaproveedordetalle_id : 0;
			$ccpc->usuario_id = $usuario_id;
			$ccpc->save();
		} else {
			$cccc = new CuentaCorrienteProveedorCredito();
			$cccc->cuentacorrienteproveedorcredito_id = $max_cuentacorrienteproveedorcredito_id;
			$cccc->get();
			$importe_actual = $cccc->importe;
			$nuevo_importe = $importe_actual + $importe;

			$cccc = new CuentaCorrienteproveedorCredito();
			$cccc->fecha = date('Y-m-d');
			$cccc->hora = date('H:i:s');
			$cccc->referencia = $referencia;
			$cccc->importe = $nuevo_importe;
			$cccc->movimiento = $importe;
			$cccc->cuentacorrienteproveedor_id = 0;
			$cccc->ingreso_id = 0;
			$cccc->proveedor_id = $proveedor_id;
			$cccc->chequeproveedordetalle_id = ($ingresotipopago_id == 1) ? $chequeproveedordetalle_id : 0;
			$cccc->transferenciaproveedordetalle_id = ($ingresotipopago_id == 2) ? $transferenciaproveedordetalle_id : 0;
			$cccc->usuario_id = $usuario_id;
			$cccc->save();	
		}		

		header("Location: " . URL_APP . "/cuentacorrienteproveedor/consultar/{$proveedor_id}");
	}

	function traer_formulario_abonar_ajax($arg) {
		$cuentacorrienteproveedor_id = $arg;
		$this->model->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
		$this->model->get();
		$ingreso_id = $this->model->ingreso_id;

		$pm = new Proveedor();
		$pm->proveedor_id = $this->model->proveedor_id;
		$pm->get();

		$select = "ROUND(((ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccp.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.ingreso_id = {$ingreso_id}";
		$balance = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);
		$ingresotipopago_collection = Collector()->get('IngresoTipoPago');

		$this->view->traer_formulario_abonar_ajax($ingresotipopago_collection, $this->model, $pm, $balance);
	}

	function eliminar_movimiento($arg) {
		SessionHandler()->check_session();

		$cuentacorrienteproveedor_id = $arg;
		$this->model->cuentacorrienteproveedor_id = $cuentacorrienteproveedor_id;
		$this->model->get();
		$proveedor_id = $this->model->proveedor_id;
		$ingreso_id = $this->model->ingreso_id;
		$ingresotipopago_id = $this->model->ingresotipopago->ingresotipopago_id;
		$this->model->delete();

		$chequeproveedordetalle_id = 0;
		$transferenciaproveedordetalle_id = 0;
		switch ($ingresotipopago_id) {
			case 1:
				$select = "cpd.chequeproveedordetalle_id AS ID";
				$from = "chequeproveedordetalle cpd";
				$where = "cpd.cuentacorrienteproveedor_id = {$cuentacorrienteproveedor_id}";
				$chequeproveedordetalle_id = CollectorCondition()->get('ChequeProveedorDetalle', $where, 4, $from, $select);
				$chequeproveedordetalle_id = (is_array($chequeproveedordetalle_id) AND !empty($chequeproveedordetalle_id)) ? $chequeproveedordetalle_id[0]['ID'] : 0;

				if ($chequeproveedordetalle_id != 0) {
					$cpdm = new ChequeProveedorDetalle();
					$cpdm->chequeproveedordetalle_id = $chequeproveedordetalle_id;
					$cpdm->delete();
				}
				break;			
			case 2:
				$select = "tpd.transferenciaproveedordetalle_id AS ID";
				$from = "transferenciaproveedordetalle tpd";
				$where = "tpd.cuentacorrienteproveedor_id = {$cuentacorrienteproveedor_id}";
				$transferenciaproveedordetalle_id = CollectorCondition()->get('TransferenciaProveedorDetalle', $where, 4, $from, $select);
				$transferenciaproveedordetalle_id = (is_array($transferenciaproveedordetalle_id) AND !empty($transferenciaproveedordetalle_id)) ? $transferenciaproveedordetalle_id[0]['ID'] : 0;

				if ($transferenciaproveedordetalle_id != 0) {
					$tpdm = new TransferenciaProveedorDetalle();
					$tpdm->transferenciaproveedordetalle_id = $transferenciaproveedordetalle_id;
					$tpdm->delete();
				}
				break;
			case 4:
				$select = "cpd.creditoproveedordetalle_id AS ID";
				$from = "creditoproveedordetalle cpd";
				$where = "cpd.cuentacorrienteproveedor_id = {$cuentacorrienteproveedor_id}";
				$creditoproveedordetalle_id = CollectorCondition()->get('CreditoProveedorDetalle', $where, 4, $from, $select);
				$creditoproveedordetalle_id = (is_array($creditoproveedordetalle_id) AND !empty($creditoproveedordetalle_id)) ? $creditoproveedordetalle_id[0]['ID'] : 0;

				if ($creditoproveedordetalle_id != 0) {
					$cpdm = new CreditoProveedorDetalle();
					$cpdm->creditoproveedordetalle_id = $creditoproveedordetalle_id;
					$cpdm->delete();
				}
				break;
		}		

		$select = "ccp.importe AS IMPORTE, ccp.ingreso AS INGRESO, ccp.cuentacorrienteproveedor_id AS ID";
		$from = "cuentacorrienteproveedor ccp";
		$where = "ccp.ingreso_id = {$ingreso_id} ORDER BY ccp.cuentacorrienteproveedor_id DESC";
		$cuentacorrienteproveedor_collection = CollectorCondition()->get('CuentaCorrienteProveedor', $where, 4, $from, $select);

		if (is_array($cuentacorrienteproveedor_collection) AND !empty($cuentacorrienteproveedor_collection)) {
			$primer_elemento = $cuentacorrienteproveedor_collection[0];
			$tmp_importe = $primer_elemento['IMPORTE'];
			$tmp_ingreso = $primer_elemento['INGRESO'];
			$tmp_id = $primer_elemento['ID'];

			$ultimo_elemento = end($cuentacorrienteproveedor_collection);
			$ultimo_id = $ultimo_elemento['ID'];
			$deuda = $ultimo_elemento['IMPORTE'];
			
			$suma_ingresos = 0;
			foreach ($cuentacorrienteproveedor_collection as $cuentacorrienteproveedor) $suma_ingresos = $suma_ingresos + $cuentacorrienteproveedor['INGRESO'];			
			if ($tmp_ingreso == 0) {
				$cccm = new CuentaCorrienteProveedor();
				$cccm->cuentacorrienteproveedor_id = $tmp_id;
				$cccm->get();
				$cccm->estadomovimientocuenta = 1;
				$cccm->save();
			} elseif ($suma_ingresos > 0 AND $suma_ingresos < $deuda) {
				foreach ($cuentacorrienteproveedor_collection as $cuentacorrienteproveedor) {
					$cccm = new CuentaCorrienteProveedor();
					$cccm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor['ID'];
					$cccm->get();
					$cccm->estadomovimientocuenta = 3;
					$cccm->save();
				}

				$cccm = new CuentaCorrienteProveedor();
				$cccm->cuentacorrienteproveedor_id = $ultimo_id;
				$cccm->get();
				$cccm->estadomovimientocuenta = 1;
				$cccm->save();	
			} elseif ($suma_ingresos == $deuda OR $suma_ingresos > $deuda) {
				foreach ($cuentacorrienteproveedor_collection as $cuentacorrienteproveedor) {
					$cccm = new CuentaCorrienteProveedor();
					$cccm->cuentacorrienteproveedor_id = $cuentacorrienteproveedor['ID'];
					$cccm->get();
					$cccm->estadomovimientocuenta = 4;
					$cccm->save();
				}
			}
		}
		
		header("Location: " . URL_APP . "/cuentacorrienteproveedor/listar_cuentas/{$proveedor_id}");
	}

	function abonar_chequeproveedor() {
		$chequeproveedordetalle_id = filter_input(INPUT_POST, 'chequeproveedordetalle_id');
		$proveedor_id = filter_input(INPUT_POST, 'proveedor_id');
		$cpdm = new ChequeProveedorDetalle();
		$cpdm->chequeproveedordetalle_id = $chequeproveedordetalle_id;
		$cpdm->get();
		$cpdm->estado = 2;
		$cpdm->fecha_pago = date('Y-m-d');
		$cpdm->save();

		header("Location: " . URL_APP . "/cuentacorrienteproveedor/consultar/{$proveedor_id}");
	}
}
?>