<?php
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/cuentacorrientecliente/view.php";
require_once "modules/cliente/model.php";
require_once "modules/egreso/model.php";
require_once "modules/cobrador/model.php";
require_once "modules/tipomovimientocuenta/model.php";
require_once "tools/cuentaCorrienteClientePDFTool.php";


class CuentaCorrienteClienteController {

	function __construct() {
		$this->model = new CuentaCorrienteCliente();
		$this->view = new CuentaCorrienteClienteView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM
    			   cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA,
				   (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND
				   dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$groupby = "ccc.cliente_id";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$totales_array = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);

		$vendedor_collection = Collector()->get('Vendedor');
		$this->view->panel($cuentacorriente_collection, $totales_array, $vendedor_collection);
	}

	function vdr_panel() {
    	SessionHandler()->check_session();
    	$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];	
    	$select = "uv.usuario_id AS USUID, uv.vendedor_id AS VENID";
		$from = "usuariovendedor uv";
		$where = "uv.usuario_id = {$usuario_id}";
		$usuariovendedor_id = CollectorCondition()->get('UsuarioVendedor', $where, 4, $from, $select);
		if (is_array($usuariovendedor_id) AND !empty($usuariovendedor_id)) {
			$vendedor_id = $usuariovendedor_id[0]['VENID'];
		} else {
			header("Location: " . URL_APP . "/reporte/vdr_panel");
		}

    	$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM
    			   cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA,
				   (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND
				   dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "c.vendedor = {$vendedor_id} AND c.oculto = 0";
		$groupby = "ccc.cliente_id";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA,
				   ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$totales_array = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$this->view->vdr_panel($cuentacorriente_collection, $totales_array);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		
    	$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM
    			   cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA,
				   (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND
				   dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$groupby = "ccc.cliente_id";
		$cuentascorrientes_collection = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select, $groupby);

    	$cm = new Cliente();
    	$cm->cliente_id = $arg;
    	$cm->get();
    	
		$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID,
				   ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS,
				   ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg} AND ccc.estadomovimientocuenta != 4 AND ccc.importe != 0";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$egreso_id = $valor['EID'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  	  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE,
					  IF (ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
					  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			$cuentacorriente_collection[$clave]['BTN_DISPLAY'] = $array_temp[0]['BTN_DISPLAY'];
			if ($_SESSION["data-login-" . APP_ABREV]["usuario-nivel"] == 1) $cuentacorriente_collection[$clave]['BTN_DISPLAY'] = 'none';
			
			$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
			$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
			$where = "eafip.egreso_id = {$egreso_id}";
			$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);
			if (is_array($eafip)) {
				$cuentacorriente_collection[$clave]['REFERENCIA'] = $eafip[0]['REFERENCIA'];
			} else {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();
				$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
				$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
				$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
				$cuentacorriente_collection[$clave]['REFERENCIA'] = "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura}";
			}
		}

		$max_cuentacorrientecliente_ids = array();
		foreach ($egreso_ids as $egreso_id) {
			$select = "ccc.cuentacorrientecliente_id AS ID";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC LIMIT 1";
			$max_id = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			if (!in_array($max_id[0]['ID'], $max_cuentacorrientecliente_ids)) $max_cuentacorrientecliente_ids[] = $max_id[0]['ID'];
		}
		
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			if (!in_array($valor['CCCID'], $max_cuentacorrientecliente_ids)) $cuentacorriente_collection[$clave]['BTN_DISPLAY'] = 'none';
		}
			
		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND 
					dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc 
					WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id = {$arg}";
		$groupby = "ccc.cliente_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$this->view->consultar($cuentascorrientes_collection, $cuentacorriente_collection, $montos_cuentacorriente, $cm);
	}

	function vdr_consultar($arg) {
    	SessionHandler()->check_session();
		
    	$cm = new Cliente();
    	$cm->cliente_id = $arg;
    	$cm->get();
    	
		$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID,
				   ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS,
				   ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg} AND ccc.estadomovimientocuenta != 4 AND ccc.importe != 0";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$egreso_id = $valor['EID'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  	  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE,
					  IF (ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
					  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			$cuentacorriente_collection[$clave]['BTN_DISPLAY'] = $array_temp[0]['BTN_DISPLAY'];
			if ($_SESSION["data-login-" . APP_ABREV]["usuario-nivel"] == 1) $cuentacorriente_collection[$clave]['BTN_DISPLAY'] = 'none';
			
			$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
			$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
			$where = "eafip.egreso_id = {$egreso_id}";
			$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);
			if (is_array($eafip)) {
				$cuentacorriente_collection[$clave]['REFERENCIA'] = $eafip[0]['REFERENCIA'];
			} else {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();
				$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
				$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
				$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
				$cuentacorriente_collection[$clave]['REFERENCIA'] = "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura}";
			}
		}

		$max_cuentacorrientecliente_ids = array();
		foreach ($egreso_ids as $egreso_id) {
			$select = "ccc.cuentacorrientecliente_id AS ID";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC LIMIT 1";
			$max_id = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			if (!in_array($max_id[0]['ID'], $max_cuentacorrientecliente_ids)) $max_cuentacorrientecliente_ids[] = $max_id[0]['ID'];
		}
		
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			if (!in_array($valor['CCCID'], $max_cuentacorrientecliente_ids)) $cuentacorriente_collection[$clave]['BTN_DISPLAY'] = 'none';
		}
			
		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND 
					dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc 
					WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id = {$arg}";
		$groupby = "ccc.cliente_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$this->view->vdr_consultar($cuentacorriente_collection, $montos_cuentacorriente, $cm);
	}

	function listar_cuentas($arg) {
    	SessionHandler()->check_session();
		
    	$cm = new Cliente();
    	$cm->cliente_id = $arg;
    	$cm->get();
    	
    	$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, ccc.egreso_id AS EID,
				   ccc.referencia AS REFERENCIA, ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		
		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$egreso_id = $valor['EID'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  	  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			
			$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
			$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
			$where = "eafip.egreso_id = {$egreso_id}";
			$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);
			if (is_array($eafip)) {
				$cuentacorriente_collection[$clave]['REFERENCIA'] = $eafip[0]['REFERENCIA'];
			} else {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();
				$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
				$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
				$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
				$cuentacorriente_collection[$clave]['REFERENCIA'] = "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura}";
			}
		}

		$max_cuentacorrientecliente_ids = array();
		foreach ($egreso_ids as $egreso_id) {
			$select = "ccc.cuentacorrientecliente_id AS ID";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC LIMIT 1";
			$max_id = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			if (!in_array($max_id[0]['ID'], $max_cuentacorrientecliente_ids)) $max_cuentacorrientecliente_ids[] = $max_id[0]['ID'];
		}

		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$cuentacorrientecliente_id = $valor["CCCID"];
			if (!in_array($cuentacorrientecliente_id, $max_cuentacorrientecliente_ids)) unset($cuentacorriente_collection[$clave]);
		}
		
		$this->view->listar_cuentas($cuentacorriente_collection, $cm);
	}

	function buscar() {
    	SessionHandler()->check_session();
		
		$argumento = filter_input(INPUT_POST, 'vendedor');
		
		if ($argumento == 'all') {
			$prewhere = "";
		} else {
			$prewhere = "AND v.vendedor_id = {$argumento}";
		}

		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO, CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_alerta_comision DAY) <= CURDATE() AND DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) > CURDATE() THEN CONCAT('ALERTA(+', e.dias_alerta_comision, 'Días)') WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) <= CURDATE() THEN CONCAT('VENCIDA(+', e.dias_vencimiento, ')') ELSE 'PENDIENTE' END AS ESTACOMP, CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_alerta_comision DAY) <= CURDATE() AND DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) > CURDATE() THEN 'warning' WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) <= CURDATE() THEN 'danger' ELSE 'success' END AS CLASSCOMP, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 {$prewhere}";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);
		
		$vendedor_collection = Collector()->get('Vendedor');
		$this->view->buscar($cuentacorriente_collection, $vendedor_collection, $argumento);
	}

	function buscar_fecha() {
    	SessionHandler()->check_session();
		
		$argumento = filter_input(INPUT_POST, 'vendedor');
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');
		
		if ($argumento == 'all') {
			$prewhere = "AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		} else {
			$prewhere = "AND v.vendedor_id = {$argumento} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
		}

		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO,
				   CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) < CURDATE() THEN 'VENCIDA' ELSE 'PENDIENTE' END AS ESTACOMP,
				   CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', 
				   LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', 
				   LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR,
				   ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2)
				   	FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd
					WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN 
				 cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN
				 tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND
					  cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia
					  WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2)
					  FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 {$prewhere}";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$vendedor_collection = Collector()->get('Vendedor');
		$this->view->buscar($cuentacorriente_collection, $vendedor_collection, $argumento);
	}

	function descargar_cuentacorriente_excel($arg) {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		
		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO,
				   CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) < CURDATE() THEN 'VENCIDA' ELSE 'PENDIENTE' END AS ESTACOMP,
				   CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', 
				   LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', 
				   LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR,
				   ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2)
				   	FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd
					WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN 
				 cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN
				 tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		switch ($arg) {
			case 'all':
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND
						  cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia
						  WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2)
						  FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5";
				
				break;
			default:
				$vendedor_id = $arg;
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND
						  cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia
						  WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2)
						  FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 AND
						  v.vendedor_id = {$vendedor_id}";
				
				break;
		}

		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$subtitulo = "LISTA DE CUENTAS CORRIENTES POR VENDEDOR";
		$array_encabezados = array('VENDEDOR', 'FECHA', 'VENCIMIENTO', 'ESTADO', 'FACTURA', 'CLIENTE', 'BARRIO', 'DOMICILIO', 'BALANCE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["BALANCE"];
			$array_temp = array();
			$array_temp = array(
						  $valor["VENDEDOR"]
						, $valor["FECHA"]
						, $valor["VENCIMIENTO"]
						, $valor["ESTACOMP"]
						, $valor["FACTURA"]
						, $valor["CLIENTE"]
						, $valor["BARRIO"]
						, $valor["DOMICILIO"]
						, $valor["BALANCE"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', '', '', '', '', 'TOTAL', $sum_importe);
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function descargar_cuentacorriente_fecha_excel() {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		
		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$desde = filter_input(INPUT_POST, 'desde');
		$hasta = filter_input(INPUT_POST, 'hasta');


		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO,
				   CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) < CURDATE() THEN 'VENCIDA' ELSE 'PENDIENTE' END AS ESTACOMP,
				   CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', 
				   LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', 
				   LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR,
				   ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2)
				   	FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd
					WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN 
				 cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN
				 tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		switch ($vendedor_id) {
			case 'all':
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND
						  cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia
						  WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2)
						  FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5
						  AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
				
				break;
			default:
				$vendedor_id = $vendedor_id;
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND
						  cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia
						  WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2)
						  FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 AND
						  v.vendedor_id = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
				
				break;
		}

		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$subtitulo = "LISTA DE CUENTAS CORRIENTES POR VENDEDOR";
		$array_encabezados = array('VENDEDOR', 'FECHA', 'VENCIMIENTO', 'ESTADO', 'FACTURA', 'CLIENTE', 'BARRIO', 'DOMICILIO', 'BALANCE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["BALANCE"];
			$array_temp = array();
			$array_temp = array(
						  $valor["VENDEDOR"]
						, $valor["FECHA"]
						, $valor["VENCIMIENTO"]
						, $valor["ESTACOMP"]
						, $valor["FACTURA"]
						, $valor["CLIENTE"]
						, $valor["BARRIO"]
						, $valor["DOMICILIO"]
						, $valor["BALANCE"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', '', '', '', '', 'TOTAL', $sum_importe);
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;
	}

	function guardar_ingreso() {
		SessionHandler()->check_session();

		$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$ingreso_id = filter_input(INPUT_POST, 'ingreso_id');
		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->hora = date('H:i:s');
		$this->model->referencia = 'Pago';
		$this->model->importe = filter_input(INPUT_POST, 'importe');
		$this->model->ingreso = filter_input(INPUT_POST, 'ingreso');
		$this->model->cliente_id = $cliente_id;
		$this->model->egreso_id = 0;
		$this->model->tipomovimientocuenta = 2;
		$this->model->estadomovimientocuenta = 2;
		$this->model->save();

		header("Location: " . URL_APP . "/cuentacorrientecliente/consultar/{$cliente_id}");
	}

	function guardar_ingreso_cuentacorriente() {
		SessionHandler()->check_session();

		$cuentacorrientecliente_id = filter_input(INPUT_POST, 'cuentacorrientecliente_id');
		$importe = filter_input(INPUT_POST, 'importe');
		$cobrador = filter_input(INPUT_POST, 'cobrador');
		$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$egreso_id = filter_input(INPUT_POST, 'egreso_id');

		$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id}";
		$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$deuda = abs($balance[0]['BALANCE']) - $importe;
		if ($deuda > 0) {
			$estadomovimientocuenta = 3;
		} else {
			$select = "ccc.cuentacorrientecliente_id AS ID";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id} AND ccc.estadomovimientocuenta IN (1,2,3)";
			$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			$estadomovimientocuenta = 4;
			
			foreach ($cuentacorriente_collection as $cuentacorrientecliente) {
				$cuentacorrientecliente_id = $cuentacorrientecliente['ID'];
				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$cccm->get();
				$cccm->estadomovimientocuenta = 4;
				$cccm->save();
			}
		}

		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();

		$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
		
		$this->model = new CuentaCorrienteCliente();
		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->hora = date('H:i:s');
		$this->model->referencia = "Pago de comprobante {$comprobante}";
		$this->model->importe = $importe;
		$this->model->ingreso = $importe;
		$this->model->cliente_id = $cliente_id;
		$this->model->egreso_id = $egreso_id;
		$this->model->tipomovimientocuenta = 2;
		$this->model->estadomovimientocuenta = $estadomovimientocuenta;
		$this->model->cobrador = $cobrador;
		$this->model->save();

		header("Location: " . URL_APP . "/cuentacorrientecliente/consultar/{$cliente_id}");
	}

	function traer_formulario_abonar_ajax($arg) {
		$cuentacorrientecliente_id = $arg;
		$this->model->cuentacorrientecliente_id = $cuentacorrientecliente_id;
		$this->model->get();
		$egreso_id = $this->model->egreso_id;

		$cm = new Cliente();
		$cm->cliente_id = $this->model->cliente_id;
		$cm->get();

		$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id}";
		$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
		}

		$this->view->traer_formulario_abonar_ajax($cobrador_collection, $this->model, $cm, $balance);
	}

	function traer_listado_movimientos_cuentacorriente_ajax($arg) {
		$egreso_id = $arg;
		$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID,
				   ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS,
				   ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.egreso_id = {$egreso_id}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		
		foreach ($cuentacorriente_collection as $clave=>$valor) {
		
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
				  	  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, 'inline-block' AS BTN_DISPLAY";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'blue' : 'red';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);

			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BCOLOR'] = $balance_class;
			$cuentacorriente_collection[$clave]['BTN_DISPLAY'] = $array_temp[0]['BTN_DISPLAY'];
			
			$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
			$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
			$where = "eafip.egreso_id = {$egreso_id}";
			$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);
			if (is_array($eafip)) {
				$cuentacorriente_collection[$clave]['REFERENCIA'] = $eafip[0]['REFERENCIA'];
			} else {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();
				$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
				$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
				$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
				$cuentacorriente_collection[$clave]['REFERENCIA'] = "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura}";
			}
		}

		$this->view->traer_listado_movimientos_cuentacorriente_ajax($cuentacorriente_collection);
	}

	function eliminar_movimiento($arg) {
		SessionHandler()->check_session();

		$cuentacorrientecliente_id = $arg;
		$this->model->cuentacorrientecliente_id = $cuentacorrientecliente_id;
		$this->model->get();
		$cliente_id = $this->model->cliente_id;
		$egreso_id = $this->model->egreso_id;
		$this->model->delete();

		$select = "ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, ccc.cuentacorrientecliente_id  AS ID";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC";
		$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		if (is_array($cuentacorrientecliente_collection) AND !empty($cuentacorrientecliente_collection)) {
			$primer_elemento = $cuentacorrientecliente_collection[0];
			$tmp_importe = $primer_elemento['IMPORTE'];
			$tmp_ingreso = $primer_elemento['INGRESO'];
			$tmp_id = $primer_elemento['ID'];

			$ultimo_elemento = end($cuentacorrientecliente_collection);
			$ultimo_id = $ultimo_elemento['ID'];
			$deuda = $ultimo_elemento['IMPORTE'];
			
			$suma_ingresos = 0;
			foreach ($cuentacorrientecliente_collection as $cuentacorrientecliente) $suma_ingresos = $suma_ingresos + $cuentacorrientecliente['INGRESO'];			
			if ($tmp_ingreso == 0) {
				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $tmp_id;
				$cccm->get();
				$cccm->estadomovimientocuenta = 1;
				$cccm->save();
			} elseif ($suma_ingresos > 0 AND $suma_ingresos < $deuda) {
				foreach ($cuentacorrientecliente_collection as $cuentacorrientecliente) {
					$cccm = new CuentaCorrienteCliente();
					$cccm->cuentacorrientecliente_id = $cuentacorrientecliente['ID'];
					$cccm->get();
					$cccm->estadomovimientocuenta = 3;
					$cccm->save();
				}

				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $ultimo_id;
				$cccm->get();
				$cccm->estadomovimientocuenta = 1;
				$cccm->save();	
			} elseif ($suma_ingresos == $deuda OR $suma_ingresos > $deuda) {
				foreach ($cuentacorrientecliente_collection as $cuentacorrientecliente) {
					$cccm = new CuentaCorrienteCliente();
					$cccm->cuentacorrientecliente_id = $cuentacorrientecliente['ID'];
					$cccm->get();
					$cccm->estadomovimientocuenta = 4;
					$cccm->save();
				}
			}
		}
		
		header("Location: " . URL_APP . "/cuentacorrientecliente/listar_cuentas/{$cliente_id}");
	}
}
?>