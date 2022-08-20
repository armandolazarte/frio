<?php
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/cuentacorrientecliente/view.php";
require_once "modules/cliente/model.php";
require_once "modules/egreso/model.php";
require_once "modules/cobrador/model.php";
require_once "modules/tipomovimientocuenta/model.php";
require_once "modules/ingresotipopago/model.php";
require_once "modules/chequeclientedetalle/model.php";
require_once "modules/transferenciaclientedetalle/model.php";
require_once "modules/retencionclientedetalle/model.php";
require_once "modules/cuentacorrienteclientecredito/model.php";
require_once "modules/clientecentral/model.php";
require_once "tools/cuentaCorrienteClientePDFTool.php";


class CuentaCorrienteClienteController {

	function __construct() {
		$this->model = new CuentaCorrienteCliente();
		$this->view = new CuentaCorrienteClienteView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$groupby = "ccc.cliente_id";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA, ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$totales_array = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);

		$vendedor_collection = Collector()->get('Vendedor');
		foreach ($vendedor_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($vendedor_collection[$clave]);
		}
		
		$this->view->panel($cuentacorriente_collection, $totales_array, $vendedor_collection);
	}

	function panel_centrales() {
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
			$importe_deuda_total = 0;
			if (is_array($clientecentralcliente_collection) AND !empty($clientecentralcliente_collection)) {
				foreach ($clientecentralcliente_collection as $c=>$v) {
					//CALCULO CREDITO
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

					//CALCULO DEUDA
					$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
					$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
					$where = "ccc.cliente_id = {$cliente_id}";
					$groupby = "ccc.cliente_id";
					$estado_ctacte = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

					$deuda = (is_null($estado_ctacte[0]['DEUDA'])) ? 0 : round($estado_ctacte[0]['DEUDA'],2);
					$ingreso = (is_null($estado_ctacte[0]['INGRESO'])) ? 0 : round($estado_ctacte[0]['INGRESO'],2);
					$cuenta = round(($ingreso - $deuda),2);
					$importe_deuda_total = $importe_deuda_total + $cuenta;
				}
			}

			$importe_deuda_total = ($importe_deuda_total > 0 AND $importe_deuda_total < 1) ? 0 : $importe_deuda_total;
			$importe_deuda_total = ($importe_deuda_total > -1 AND $importe_deuda_total < 0) ? 0 : $importe_deuda_total;
			$class = ($importe_deuda_total >= 0) ? 'info' : 'danger';
			$clientecentral_collection[$clave]['CUENTA'] = abs($importe_deuda_total);
			$clientecentral_collection[$clave]['CLASS'] = $class;
			$clientecentral_collection[$clave]['CREDITO'] = $importe_credito_total;
		}

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA, ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc";
		$totales_array = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select);

		$vendedor_collection = Collector()->get('Vendedor');
		foreach ($vendedor_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($vendedor_collection[$clave]);
		}
		
		$this->view->panel_centrales($clientecentral_collection, $totales_array, $vendedor_collection);
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

    	$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "c.vendedor = {$vendedor_id} AND c.oculto = 0";
		$groupby = "ccc.cliente_id";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$select = "ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN ccc.importe ELSE 0 END),2) AS TDEUDA, ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 OR ccc.tipomovimientocuenta = 3 THEN ccc.importe ELSE 0 END),2) AS TINGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$totales_array = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$this->view->vdr_panel($cuentacorriente_collection, $totales_array);
	}

	function bk_consultar($arg) {
    	SessionHandler()->check_session();
		
    	$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$groupby = "ccc.cliente_id";
		$cuentascorrientes_collection = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select, $groupby);

    	$cm = new Cliente();
    	$cm->cliente_id = $arg;
    	$cm->get();
    	
		$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID, ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ingresotipopago AS ING_TIP_PAG, ccc.cuentacorrientecliente_id CCCID, ccc.cliente_id AS CLIID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg} AND ccc.estadomovimientocuenta != 4 AND ccc.importe != 0";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$temp_cuentacorrientecliente_id = $valor['CCCID'];
			$egreso_id = $valor['EID'];
			$ingresotipopago_id = $valor['ING_TIP_PAG'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, IF (ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
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
			//if ($_SESSION["data-login-" . APP_ABREV]["usuario-nivel"] == 1) $cuentacorriente_collection[$clave]['BTN_DISPLAY'] = 'none';
			
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

			switch ($ingresotipopago_id) {
				case 1:
					$select = "ccd.chequeclientedetalle_id AS ID";
					$from = "chequeclientedetalle ccd";
					$where = "ccd.cuentacorrientecliente_id = {$temp_cuentacorrientecliente_id}";
					$chequeclientedetalle_id = CollectorCondition()->get('ChequeClienteDetalle', $where, 4, $from, $select);
					$chequeclientedetalle_id = (is_array($chequeclientedetalle_id) AND !empty($chequeclientedetalle_id)) ? $chequeclientedetalle_id[0]['ID'] : 0;

					if ($chequeclientedetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $chequeclientedetalle_id;
					} else {
						$btn_display_ver_tipopago = 'none';
						$btn_tipopago_id = '#';
						$btn_movimiento_id = '#';
					}
					break;
				case 2:
					$select = "tcd.transferenciaclientedetalle_id AS ID";
					$from = "transferenciaclientedetalle tcd";
					$where = "tcd.cuentacorrientecliente_id = {$temp_cuentacorrientecliente_id}";
					$transferenciaclientedetalle_id = CollectorCondition()->get('TransferenciaClienteDetalle', $where, 4, $from, $select);
					$transferenciaclientedetalle_id = (is_array($transferenciaclientedetalle_id) AND !empty($transferenciaclientedetalle_id)) ? $transferenciaclientedetalle_id[0]['ID'] : 0;

					if ($transferenciaclientedetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $transferenciaclientedetalle_id;
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
			
		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id = {$arg}";
		$groupby = "ccc.cliente_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$this->view->bk_consultar($cuentascorrientes_collection, $cuentacorriente_collection, $montos_cuentacorriente, $cm);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		$select = "ccc.cliente_id AS CID, c.razon_social AS CLIENTE, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$groupby = "ccc.cliente_id";
		$cuentascorrientes_collection = CollectorCondition()->get('CuentaCorrienteCliente', NULL, 4, $from, $select, $groupby);

    	$cm = new Cliente();
    	$cm->cliente_id = $arg;
    	$cm->get();

    	$select = "ccc.clientecentral_id AS CLICENID";
		$from = "clientecentralcliente ccc";
		$where = "ccc.cliente_id = {$arg}";
		$clientecentral_id = CollectorCondition()->get('ClienteCentralCliente', $where, 4, $from, $select);
		$clientecentral_id = (is_array($clientecentral_id) AND !empty($clientecentral_id)) ? $clientecentral_id[0]['CLICENID'] : 0;

		if ($clientecentral_id != 0) {
			$cm->clientecentral_id = $clientecentral_id;
			$cm->btn_display_clientecentral = 'inline-block';
		} else { 
			$cm->clientecentral_id = 0;
			$cm->btn_display_clientecentral = 'none';
		}
    	
		$select = "ccc.fecha AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID, ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ingresotipopago AS ING_TIP_PAG, ccc.cuentacorrientecliente_id CCCID, ccc.cliente_id AS CLIID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg} AND ccc.estadomovimientocuenta != 4 AND ccc.importe != 0";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$temp_cuentacorrientecliente_id = $valor['CCCID'];
			$egreso_id = $valor['EID'];
			$ingresotipopago_id = $valor['ING_TIP_PAG'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, IF (ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);
			
			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BALCAL'] = abs($balance);
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

			switch ($ingresotipopago_id) {
				case 1:
					$select = "ccd.chequeclientedetalle_id AS ID";
					$from = "chequeclientedetalle ccd";
					$where = "ccd.cuentacorrientecliente_id = {$temp_cuentacorrientecliente_id}";
					$chequeclientedetalle_id = CollectorCondition()->get('ChequeClienteDetalle', $where, 4, $from, $select);
					$chequeclientedetalle_id = (is_array($chequeclientedetalle_id) AND !empty($chequeclientedetalle_id)) ? $chequeclientedetalle_id[0]['ID'] : 0;

					if ($chequeclientedetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $chequeclientedetalle_id;
					} else {
						$btn_display_ver_tipopago = 'none';
						$btn_tipopago_id = '#';
						$btn_movimiento_id = '#';
					}
					break;
				case 2:
					$select = "tcd.transferenciaclientedetalle_id AS ID";
					$from = "transferenciaclientedetalle tcd";
					$where = "tcd.cuentacorrientecliente_id = {$temp_cuentacorrientecliente_id}";
					$transferenciaclientedetalle_id = CollectorCondition()->get('TransferenciaClienteDetalle', $where, 4, $from, $select);
					$transferenciaclientedetalle_id = (is_array($transferenciaclientedetalle_id) AND !empty($transferenciaclientedetalle_id)) ? $transferenciaclientedetalle_id[0]['ID'] : 0;

					if ($transferenciaclientedetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $transferenciaclientedetalle_id;
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
			
		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id = {$arg}";
		$groupby = "ccc.cliente_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
		}

		$select = "cccc.cuentacorrienteclientecredito_id AS ID";
		$from = "cuentacorrienteclientecredito cccc";
		$where = "cccc.cliente_id = {$arg} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
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

		$this->view->consultar($cuentascorrientes_collection, $cuentacorriente_collection, $cobrador_collection, $montos_cuentacorriente, $cm, $importe_cuentacorrienteclientecredito);
	}

	function consultar_central($arg) {
    	SessionHandler()->check_session();
    	$clientecentral_id = $arg;
    	$select = "ccc.cliente_id AS CLIID";
    	$from = "clientecentralcliente ccc";
    	$where = "ccc.clientecentral_id = {$clientecentral_id}";
		$clientecentral_collection = CollectorCondition()->get('ClienteCentral', $where, 4, $from, $select);
		
		$cliente_ids_array = array();
		foreach ($clientecentral_collection as $clave=>$valor) {
			$cliente_id = $valor['CLIID'];
			if(!in_array($cliente_id, $cliente_ids_array)) $cliente_ids_array[] = $cliente_id;
		}
		
		$cliente_ids = implode(',', $cliente_ids_array);
		$ccm = new ClienteCentral();
		$ccm->clientecentral_id = $clientecentral_id;
		$ccm->get();
		$clientecredito_id = $ccm->cliente_id;
		if ($clientecredito_id != 0) {
    		$cm = new Cliente();
    		$cm->cliente_id = $clientecredito_id;
    		$cm->get();
    		$ccm->cliente_razonsocial = $cm->razon_social;
		} else {
    		$ccm->cliente_razonsocial = 'Sin Definir';
		}


		$select = "ccc.fecha AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID, ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ingresotipopago AS ING_TIP_PAG, ccc.cuentacorrientecliente_id CCCID, ccc.cliente_id AS CLIID, c.razon_social AS RAZSOC";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id IN ({$cliente_ids}) AND ccc.estadomovimientocuenta != 4 AND ccc.importe != 0";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$temp_cuentacorrientecliente_id = $valor['CCCID'];
			$egreso_id = $valor['EID'];
			$ingresotipopago_id = $valor['ING_TIP_PAG'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, IF (ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance_class = ($balance >= 0) ? 'primary' : 'danger';
			$new_balance = ($balance >= 0) ? "$" . $balance : str_replace('-', '-$', $balance);
			
			$cuentacorriente_collection[$clave]['BALANCE'] = $new_balance;
			$cuentacorriente_collection[$clave]['BALCAL'] = abs($balance);
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

			switch ($ingresotipopago_id) {
				case 1:
					$select = "ccd.chequeclientedetalle_id AS ID";
					$from = "chequeclientedetalle ccd";
					$where = "ccd.cuentacorrientecliente_id = {$temp_cuentacorrientecliente_id}";
					$chequeclientedetalle_id = CollectorCondition()->get('ChequeClienteDetalle', $where, 4, $from, $select);
					$chequeclientedetalle_id = (is_array($chequeclientedetalle_id) AND !empty($chequeclientedetalle_id)) ? $chequeclientedetalle_id[0]['ID'] : 0;

					if ($chequeclientedetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $chequeclientedetalle_id;
					} else {
						$btn_display_ver_tipopago = 'none';
						$btn_tipopago_id = '#';
						$btn_movimiento_id = '#';
					}
					break;
				case 2:
					$select = "tcd.transferenciaclientedetalle_id AS ID";
					$from = "transferenciaclientedetalle tcd";
					$where = "tcd.cuentacorrientecliente_id = {$temp_cuentacorrientecliente_id}";
					$transferenciaclientedetalle_id = CollectorCondition()->get('TransferenciaClienteDetalle', $where, 4, $from, $select);
					$transferenciaclientedetalle_id = (is_array($transferenciaclientedetalle_id) AND !empty($transferenciaclientedetalle_id)) ? $transferenciaclientedetalle_id[0]['ID'] : 0;

					if ($transferenciaclientedetalle_id != 0) {
						$btn_display_ver_tipopago = 'inline-block';
						$btn_tipopago_id = $ingresotipopago_id;
						$btn_movimiento_id = $transferenciaclientedetalle_id;
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
			
		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
		$from = "cuentacorrientecliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.cliente_id IN ({$cliente_ids})";
		$groupby = "ccc.cliente_id";
		$montos_cuentacorriente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
		}

		$select = "ccc.cliente_id AS CLIID, c.codigo AS COD, c.razon_social AS RAZSOC";
		$from = "clientecentralcliente ccc INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id";
		$where = "ccc.clientecentral_id = {$clientecentral_id}";
		$clientecentralcliente_collection = CollectorCondition()->get('ClienteCentralCliente', $where, 4, $from, $select);

		$importe_cuentacorrienteclientecredito = 0;
		if (is_array($clientecentralcliente_collection) AND !empty($clientecentralcliente_collection)) {
			foreach ($clientecentralcliente_collection as $clave=>$valor) {
				$cliente_id = $valor['CLIID'];
				
				$select = "cccc.cuentacorrienteclientecredito_id AS ID";
				$from = "cuentacorrienteclientecredito cccc";
				$where = "cccc.cliente_id = {$cliente_id} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
				$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
				$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;
				
				if ($max_cuentacorrienteclientecredito_id != 0) {
					$cccc = new CuentaCorrienteClienteCredito();
					$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
					$cccc->get();
					$importe_cuentacorrienteclientecredito = $importe_cuentacorrienteclientecredito + $cccc->importe;
					$clientecentralcliente_collection[$clave]["CREDITO"] = $cccc->importe;
				} else {
					$clientecentralcliente_collection[$clave]["CREDITO"] = 0;
				}
			}
		}

		$this->view->consultar_central($cuentacorriente_collection, $cobrador_collection, $montos_cuentacorriente, $clientecentralcliente_collection, $ccm, $importe_cuentacorrienteclientecredito);
	}

	function vdr_consultar($arg) {
    	SessionHandler()->check_session();
		
    	$cm = new Cliente();
    	$cm->cliente_id = $arg;
    	$cm->get();
    	
		$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID, ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg} AND ccc.estadomovimientocuenta != 4 AND ccc.importe != 0";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$egreso_id = $valor['EID'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, IF (ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2)))) >= 0, 'none', 'inline-block') AS BTN_DISPLAY";
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
			
		$select = "(SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 1 AND  dccc.cliente_id = ccc.cliente_id) AS DEUDA, (SELECT ROUND(SUM(dccc.importe),2) FROM cuentacorrientecliente dccc WHERE dccc.tipomovimientocuenta = 2 AND dccc.cliente_id = ccc.cliente_id) AS INGRESO";
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
    	
    	$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, ccc.egreso_id AS EID, ccc.referencia AS REFERENCIA, ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.cliente_id = {$arg}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		
		$egreso_ids = array();
		foreach ($cuentacorriente_collection as $clave=>$valor) {
			$egreso_id = $valor['EID'];
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$array_temp = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			$balance = $array_temp[0]['BALANCE'];
			$balance = ($balance == '-0') ? abs($balance) : $balance;
			$balance = ($balance > -0,5 AND $balance < 0,5) ? 0 : $balance;
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

		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO, CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) < CURDATE() THEN 'VENCIDA' ELSE 'PENDIENTE' END AS ESTACOMP, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 {$prewhere}";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select, $groupby);

		$vendedor_collection = Collector()->get('Vendedor');
		$this->view->buscar($cuentacorriente_collection, $vendedor_collection, $argumento);
	}

	function descargar_cuentacorriente_excel($arg) {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		
		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO, CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) < CURDATE() THEN 'VENCIDA' ELSE 'PENDIENTE' END AS ESTACOMP, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		switch ($arg) {
			case 'all':
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5";				
				break;
			default:
				$vendedor_id = $arg;
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 AND v.vendedor_id = {$vendedor_id}";				
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
			$array_temp = array($valor["VENDEDOR"]
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


		$select = "e.egreso_id, date_format(e.fecha, '%d/%m/%Y') AS FECHA, date_format(DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY), '%d/%m/%Y') AS VENCIMIENTO, CASE WHEN DATE_ADD(e.fecha, INTERVAL e.dias_vencimiento DAY) < CURDATE() THEN 'VENCIDA' ELSE 'PENDIENTE' END AS ESTACOMP, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, c.razon_social AS CLIENTE, c.localidad AS BARRIO, c.domicilio AS DOMICILIO, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, ((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) AS BALANCE";
		$from = "cuentacorrientecliente ccc INNER JOIN egreso e ON ccc.egreso_id = e.egreso_id INNER JOIN cliente c ON ccc.cliente_id = c.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id INNER JOIN tipofactura tf ON e.tipofactura = tf.tipofactura_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$groupby = "ccc.egreso_id ORDER BY e.fecha ASC";
		switch ($vendedor_id) {
			case 'all':
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
				break;
			default:
				$vendedor_id = $vendedor_id;
				$where = "((IF((SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id) IS NULL, 0, (SELECT ROUND(SUM(cccia.importe),2) FROM cuentacorrientecliente cccia WHERE cccia.tipomovimientocuenta = 2 AND cccia.egreso_id = ccc.egreso_id))) - (SELECT ROUND(SUM(cccd.importe),2) FROM cuentacorrientecliente cccd WHERE cccd.tipomovimientocuenta = 1 AND cccd.egreso_id = ccc.egreso_id)) < -0.5 AND v.vendedor_id = {$vendedor_id} AND e.fecha BETWEEN '{$desde}' AND '{$hasta}'";
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
			$array_temp = array($valor["VENDEDOR"]
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
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$cuentacorrientecliente_id = filter_input(INPUT_POST, 'cuentacorrientecliente_id');
		$importe = filter_input(INPUT_POST, 'importe');
		$cobrador = filter_input(INPUT_POST, 'cobrador');
		$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$egreso_id = filter_input(INPUT_POST, 'egreso_id');

		$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
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

		$select = "eafip.punto_venta AS PUNTO_VENTA, eafip.numero_factura AS NUMERO_FACTURA, tf.nomenclatura AS TIPOFACTURA, eafip.cae AS CAE, eafip.vencimiento AS FVENCIMIENTO, eafip.fecha AS FECHA, tf.tipofactura_id AS TF_ID";
		$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
		$where = "eafip.egreso_id = {$egreso_id}";
		$egresoafip = CollectorCondition()->get('EgresoAfip', $where, 4, $from, $select);

		if (is_array($egresoafip)) {
			$em->punto_venta = $egresoafip[0]['PUNTO_VENTA'];
			$em->numero_factura = $egresoafip[0]['NUMERO_FACTURA'];
		}

		$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
		
		$ingresotipopago_id = filter_input(INPUT_POST, 'ingresotipopago');
		$this->model = new CuentaCorrienteCliente();
		$this->model->fecha = filter_input(INPUT_POST, 'fecha');
		$this->model->hora = date('H:i:s');
		$this->model->referencia = "Pago de comprobante {$comprobante}";
		$this->model->importe = $importe;
		$this->model->ingreso = $importe;
		$this->model->cliente_id = $cliente_id;
		$this->model->egreso_id = $egreso_id;
		$this->model->ingresotipopago = $ingresotipopago_id;
		$this->model->tipomovimientocuenta = 2;
		$this->model->estadomovimientocuenta = $estadomovimientocuenta;
		$this->model->cobrador = $cobrador;
		$this->model->save();
		$cuentacorrientecliente_id = $this->model->cuentacorrientecliente_id;

		switch ($ingresotipopago_id) {
			case 1:
				$cpdm = new ChequeClienteDetalle();
				$cpdm->numero = filter_input(INPUT_POST, 'numero_cheque');
				$cpdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
				$cpdm->fecha_pago = null;
				$cpdm->banco = filter_input(INPUT_POST, 'banco');
				$cpdm->plaza = filter_input(INPUT_POST, 'plaza');
				$cpdm->titular = filter_input(INPUT_POST, 'titular');
				$cpdm->documento = filter_input(INPUT_POST, 'documento');
				$cpdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
				$cpdm->estado = 1;
				$cpdm->importe = $importe;
				$cpdm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$cpdm->egreso_id = $egreso_id;
				$cpdm->save();
				break;
			case 2:
				$tpdm = new TransferenciaClienteDetalle();
				$tpdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
				$tpdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
				$tpdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
				$tpdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
				$tpdm->importe = $importe;
				$tpdm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$tpdm->egreso_id = $egreso_id;
				$tpdm->save();
				break;
			case 5:
				$tpdm = new RetencionClienteDetalle();
				$tpdm->numero = filter_input(INPUT_POST, 'numero_retencion');
				$tpdm->mes = filter_input(INPUT_POST, 'mes_retencion');
				$tpdm->anio = filter_input(INPUT_POST, 'anio_retencion');
				$tpdm->importe = $importe;
				$tpdm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$tpdm->egreso_id = $egreso_id;
				$tpdm->caja = 1;
				$tpdm->save();
				break;
			case 6:
				$select = "ccc.clientecentral_id AS CLICENID";
				$from = "clientecentralcliente ccc";
				$where = "ccc.cliente_id = {$cliente_id}";
				$clientecentral_id = CollectorCondition()->get('ClienteCentralCliente', $where, 4, $from, $select);
				$clientecentral_id = (is_array($clientecentral_id) AND !empty($clientecentral_id)) ? $clientecentral_id[0]['CLICENID'] : 0;

				if ($clientecentral_id != 0) {
					$ccm = new ClienteCentral();
					$ccm->clientecentral_id = $clientecentral_id;
					$ccm->get();
					$clientecredito = $ccm->cliente_id;
				} else { 
					$clientecredito = $cliente_id;
				}

				$select = "cccc.cuentacorrienteclientecredito_id AS ID";
				$from = "cuentacorrienteclientecredito cccc";
				$where = "cccc.cliente_id = {$clientecredito} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
				$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
				$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
				$cccc->get();
				$importe_actual = $cccc->importe;
				$nuevo_importe = $importe_actual - $importe;

				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->fecha = date('Y-m-d');
				$cccc->hora = date('H:i:s');
				$cccc->referencia = "Pago de comprobante {$comprobante}";
				$cccc->importe = $nuevo_importe;
				$cccc->movimiento = round($importe, 2);
				$cccc->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$cccc->egreso_id = $egreso_id;
				$cccc->cliente_id = $clientecredito;
				$cccc->chequeclientedetalle_id = 0;
				$cccc->transferenciaclientedetalle_id = 0;
				$cccc->usuario_id = $usuario_id;
				$cccc->save();
				break;
		}

		header("Location: " . URL_APP . "/cuentacorrientecliente/consultar/{$cliente_id}");
	}

	function guardar_ingreso_cuentacorriente_conjunto() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$ingresotipopago_id = filter_input(INPUT_POST, 'ingresotipopago');
		$importe = filter_input(INPUT_POST, 'importe');
		$importe_movimiento = filter_input(INPUT_POST, 'importe');
		$cobrador = filter_input(INPUT_POST, 'cobrador');
		$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$cuentacorrientecliente_collection = $_POST["cuentacorrientecliente"];

		$egreso_ids = array();
		foreach ($cuentacorrientecliente_collection as $clave=>$valor) {
			$cuentacorrientecliente_id = $valor["cuentacorrientecliente_id"];
			$cccm = new CuentaCorrienteCliente();
			$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
			$cccm->get();

			$egreso_id = $cccm->egreso_id;
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
		}
		
		asort($egreso_ids);
		foreach ($egreso_ids as $egreso_id) {
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			if ($importe > 0) {
				if ($importe > abs($balance[0]['BALANCE'])) {
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

					$em = new Egreso();
					$em->egreso_id = $egreso_id;
					$em->get();
					$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
					$comprobante .= str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);

					$this->model = new CuentaCorrienteCliente();
					$this->model->fecha = filter_input(INPUT_POST, 'fecha');
					$this->model->hora = date('H:i:s');
					$this->model->referencia = "Pago de comprobante {$comprobante}";
					$this->model->importe = abs($balance[0]['BALANCE']);
					$this->model->ingreso = abs($balance[0]['BALANCE']);
					$this->model->cliente_id = $cliente_id;
					$this->model->egreso_id = $egreso_id;
					$this->model->ingresotipopago = $ingresotipopago_id;
					$this->model->tipomovimientocuenta = 2;
					$this->model->estadomovimientocuenta = 4;
					$this->model->cobrador = $cobrador;
					$this->model->save();
					$final_cuentacorrientecliente_id = $this->model->cuentacorrientecliente_id;

					if ($ingresotipopago_id == 1) {
						$numero_cheque = filter_input(INPUT_POST, 'numero_cheque'); 
						$ccdm = new ChequeClienteDetalle();
						$ccdm->numero = filter_input(INPUT_POST, 'numero_cheque');
						$ccdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
						$ccdm->fecha_pago = date('Y-m-d');
						$ccdm->banco = filter_input(INPUT_POST, 'banco');
						$ccdm->plaza = filter_input(INPUT_POST, 'plaza');
						$ccdm->titular = filter_input(INPUT_POST, 'titular');
						$ccdm->documento = filter_input(INPUT_POST, 'documento');
						$ccdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
						$ccdm->estado = 2;
						$ccdm->importe = $importe_movimiento;
						$ccdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$ccdm->egreso_id = $egreso_id;
						$ccdm->save();
						$chequeclientedetalle_id = $ccdm->chequeclientedetalle_id;
						$referencia = "Sobrante de pago con Cheque N° {$numero_cheque}";
					} else {
						$numero_transferencia = filter_input(INPUT_POST, 'numero_transferencia'); 
						$tcdm = new TransferenciaClienteDetalle();
						$tcdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
						$tcdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
						$tcdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
						$tcdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
						$tcdm->importe = $importe_movimiento;
						$tcdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$tcdm->egreso_id = $egreso_id;
						$tcdm->save();	
						$transferenciaclientedetalle_id = $tcdm->transferenciaclientedetalle_id;
						$referencia = "Sobrante de pago con Transferencia N° {$numero_transferencia}";
					}

					//RESTANTE IMPORTE CHEQUE
					$importe = $importe - abs($balance[0]['BALANCE']);
				} else {
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
					$this->model->ingresotipopago = $ingresotipopago_id;
					$this->model->tipomovimientocuenta = 2;
					$this->model->estadomovimientocuenta = 3;
					$this->model->cobrador = $cobrador;
					$this->model->save();
					$final_cuentacorrientecliente_id = $this->model->cuentacorrientecliente_id;

					if ($ingresotipopago_id == 1) {
						$ccdm = new ChequeClienteDetalle();
						$ccdm->numero = filter_input(INPUT_POST, 'numero_cheque');
						$ccdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
						$ccdm->fecha_pago = date('Y-m-d');
						$ccdm->banco = filter_input(INPUT_POST, 'banco');
						$ccdm->plaza = filter_input(INPUT_POST, 'plaza');
						$ccdm->titular = filter_input(INPUT_POST, 'titular');
						$ccdm->documento = filter_input(INPUT_POST, 'documento');
						$ccdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
						$ccdm->estado = 2;
						$ccdm->importe = $importe_movimiento;
						$ccdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$ccdm->egreso_id = $egreso_id;
						$ccdm->save();
						$chequeclientedetalle_id = $ccdm->chequeclientedetalle_id;
					} else {
						$tcdm = new TransferenciaClienteDetalle();
						$tcdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
						$tcdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
						$tcdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
						$tcdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
						$tcdm->importe = $importe_movimiento;
						$tcdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$tcdm->egreso_id = $egreso_id;
						$tcdm->save();
						$transferenciaclientedetalle_id = $tcdm->transferenciaclientedetalle_id;
					}
					
					//FINALIZA IMPORTE CHEQUE
					$importe = 0;
				}	
			}
		}

		if ($importe > 0.5) {
			$select = "ccc.clientecentral_id AS CLICENID";
			$from = "clientecentralcliente ccc";
			$where = "ccc.cliente_id = {$cliente_id}";
			$clientecentral_id = CollectorCondition()->get('ClienteCentralCliente', $where, 4, $from, $select);
			$clientecentral_id = (is_array($clientecentral_id) AND !empty($clientecentral_id)) ? $clientecentral_id[0]['CLICENID'] : 0;

			if ($clientecentral_id != 0) {
				$ccm = new ClienteCentral();
				$ccm->clientecentral_id = $clientecentral_id;
				$ccm->get();
				$clientecredito = $ccm->cliente_id;
			} else { 
				$clientecredito = $cliente_id;
			}

			$select = "cccc.cuentacorrienteclientecredito_id AS ID";
			$from = "cuentacorrienteclientecredito cccc";
			$where = "cccc.cliente_id = {$clientecredito} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
			$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
			$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

			if ($max_cuentacorrienteclientecredito_id == 0) {
				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->fecha = date('Y-m-d');
				$cccc->hora = date('H:i:s');
				$cccc->referencia = $referencia;
				$cccc->importe = $importe;
				$cccc->movimiento = $importe;
				$cccc->cuentacorrientecliente_id = 0;
				$cccc->egreso_id = 0;
				$cccc->cliente_id = $clientecredito;
				$cccc->chequeclientedetalle_id = ($ingresotipopago_id == 1) ? $chequeclientedetalle_id : 0;
				$cccc->transferenciaclientedetalle_id = ($ingresotipopago_id == 2) ? $transferenciaclientedetalle_id : 0;
				$cccc->usuario_id = $usuario_id;
				$cccc->save();
			} else {
				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
				$cccc->get();
				$importe_actual = $cccc->importe;
				$nuevo_importe = $importe_actual + $importe;

				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->fecha = date('Y-m-d');
				$cccc->hora = date('H:i:s');
				$cccc->referencia = $referencia;
				$cccc->importe = $nuevo_importe;
				$cccc->movimiento = $importe;
				$cccc->cuentacorrientecliente_id = 0;
				$cccc->egreso_id = 0;
				$cccc->cliente_id = $clientecredito;
				$cccc->chequeclientedetalle_id = ($ingresotipopago_id == 1) ? $chequeclientedetalle_id : 0;
				$cccc->transferenciaclientedetalle_id = ($ingresotipopago_id == 2) ? $transferenciaclientedetalle_id : 0;
				$cccc->usuario_id = $usuario_id;
				$cccc->save();	
			}
		}

		header("Location: " . URL_APP . "/cuentacorrientecliente/consultar/{$cliente_id}");
	}

	function guardar_ingreso_cuentacorriente_conjunto_central() {
		SessionHandler()->check_session();
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$ingresotipopago_id = filter_input(INPUT_POST, 'ingresotipopago');
		$importe = filter_input(INPUT_POST, 'importe');
		$importe_movimiento = filter_input(INPUT_POST, 'importe');
		$cobrador = filter_input(INPUT_POST, 'cobrador');
		//$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$clientecentral_id = filter_input(INPUT_POST, 'clientecentral_id');
		$cuentacorrientecliente_collection = $_POST["cuentacorrientecliente"];

		$egreso_ids = array();
		foreach ($cuentacorrientecliente_collection as $clave=>$valor) {
			$cuentacorrientecliente_id = $valor["cuentacorrientecliente_id"];
			$cccm = new CuentaCorrienteCliente();
			$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
			$cccm->get();

			$egreso_id = $cccm->egreso_id;
			if (!in_array($egreso_id, $egreso_ids)) $egreso_ids[] = $egreso_id;
		}
		
		asort($egreso_ids);
		foreach ($egreso_ids as $egreso_id) {
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
			$from = "cuentacorrientecliente ccc";
			$where = "ccc.egreso_id = {$egreso_id}";
			$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
			
			if ($importe > 0) {
				if ($importe > abs($balance[0]['BALANCE'])) {
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

					$em = new Egreso();
					$em->egreso_id = $egreso_id;
					$em->get();
					$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
					$comprobante .= str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
					$cliente_id = $em->cliente->cliente_id;

					$this->model = new CuentaCorrienteCliente();
					$this->model->fecha = filter_input(INPUT_POST, 'fecha');
					$this->model->hora = date('H:i:s');
					$this->model->referencia = "Pago de comprobante {$comprobante}";
					$this->model->importe = abs($balance[0]['BALANCE']);
					$this->model->ingreso = abs($balance[0]['BALANCE']);
					$this->model->cliente_id = $cliente_id;
					$this->model->egreso_id = $egreso_id;
					$this->model->ingresotipopago = $ingresotipopago_id;
					$this->model->tipomovimientocuenta = 2;
					$this->model->estadomovimientocuenta = 4;
					$this->model->cobrador = $cobrador;
					$this->model->save();
					$final_cuentacorrientecliente_id = $this->model->cuentacorrientecliente_id;

					if ($ingresotipopago_id == 1) {
						$numero_cheque = filter_input(INPUT_POST, 'numero_cheque'); 
						$ccdm = new ChequeClienteDetalle();
						$ccdm->numero = filter_input(INPUT_POST, 'numero_cheque');
						$ccdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
						$ccdm->fecha_pago = date('Y-m-d');
						$ccdm->banco = filter_input(INPUT_POST, 'banco');
						$ccdm->plaza = filter_input(INPUT_POST, 'plaza');
						$ccdm->titular = filter_input(INPUT_POST, 'titular');
						$ccdm->documento = filter_input(INPUT_POST, 'documento');
						$ccdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
						$ccdm->estado = 2;
						$ccdm->importe = $importe_movimiento;
						$ccdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$ccdm->egreso_id = $egreso_id;
						$ccdm->save();
						$chequeclientedetalle_id = $ccdm->chequeclientedetalle_id;
						$referencia = "Sobrante de pago con Cheque N° {$numero_cheque}";
					} else {
						$numero_transferencia = filter_input(INPUT_POST, 'numero_transferencia'); 
						$tcdm = new TransferenciaClienteDetalle();
						$tcdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
						$tcdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
						$tcdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
						$tcdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
						$tcdm->importe = $importe_movimiento;
						$tcdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$tcdm->egreso_id = $egreso_id;
						$tcdm->save();	
						$transferenciaclientedetalle_id = $tcdm->transferenciaclientedetalle_id;
						$referencia = "Sobrante de pago con Transferencia N° {$numero_transferencia}";
					}

					//RESTANTE IMPORTE CHEQUE
					$importe = $importe - abs($balance[0]['BALANCE']);
				} else {
					$em = new Egreso();
					$em->egreso_id = $egreso_id;
					$em->get();
					$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
					$comprobante .= str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
					$cliente_id = $em->cliente->cliente_id;

					$this->model = new CuentaCorrienteCliente();
					$this->model->fecha = filter_input(INPUT_POST, 'fecha');
					$this->model->hora = date('H:i:s');
					$this->model->referencia = "Pago de comprobante {$comprobante}";
					$this->model->importe = $importe;
					$this->model->ingreso = $importe;
					$this->model->cliente_id = $cliente_id;
					$this->model->egreso_id = $egreso_id;
					$this->model->ingresotipopago = $ingresotipopago_id;
					$this->model->tipomovimientocuenta = 2;
					$this->model->estadomovimientocuenta = 3;
					$this->model->cobrador = $cobrador;
					$this->model->save();
					$final_cuentacorrientecliente_id = $this->model->cuentacorrientecliente_id;

					if ($ingresotipopago_id == 1) {
						$ccdm = new ChequeClienteDetalle();
						$ccdm->numero = filter_input(INPUT_POST, 'numero_cheque');
						$ccdm->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
						$ccdm->fecha_pago = date('Y-m-d');
						$ccdm->banco = filter_input(INPUT_POST, 'banco');
						$ccdm->plaza = filter_input(INPUT_POST, 'plaza');
						$ccdm->titular = filter_input(INPUT_POST, 'titular');
						$ccdm->documento = filter_input(INPUT_POST, 'documento');
						$ccdm->cuenta_corriente = filter_input(INPUT_POST, 'cuenta_corriente');
						$ccdm->estado = 2;
						$ccdm->importe = $importe_movimiento;
						$ccdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$ccdm->egreso_id = $egreso_id;
						$ccdm->save();
						$chequeclientedetalle_id = $ccdm->chequeclientedetalle_id;
					} else {
						$tcdm = new TransferenciaClienteDetalle();
						$tcdm->numero = filter_input(INPUT_POST, 'numero_transferencia');
						$tcdm->banco = filter_input(INPUT_POST, 'banco_transferencia');
						$tcdm->plaza = filter_input(INPUT_POST, 'plaza_transferencia');
						$tcdm->numero_cuenta = filter_input(INPUT_POST, 'numero_cuenta_transferencia');
						$tcdm->importe = $importe_movimiento;
						$tcdm->cuentacorrientecliente_id = $final_cuentacorrientecliente_id;
						$tcdm->egreso_id = $egreso_id;
						$tcdm->save();
						$transferenciaclientedetalle_id = $tcdm->transferenciaclientedetalle_id;
					}
					
					//FINALIZA IMPORTE CHEQUE
					$importe = 0;
				}	
			}
		}

		if ($importe > 0.5) {
			$ccm = new ClienteCentral();
			$ccm->clientecentral_id = $clientecentral_id;
			$ccm->get();
			$clientecredito = $ccm->cliente_id;
			
			$select = "cccc.cuentacorrienteclientecredito_id AS ID";
			$from = "cuentacorrienteclientecredito cccc";
			$where = "cccc.cliente_id = {$clientecredito} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
			$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
			$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

			if ($max_cuentacorrienteclientecredito_id == 0) {
				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->fecha = date('Y-m-d');
				$cccc->hora = date('H:i:s');
				$cccc->referencia = $referencia;
				$cccc->importe = $importe;
				$cccc->movimiento = $importe;
				$cccc->cuentacorrientecliente_id = 0;
				$cccc->egreso_id = 0;
				$cccc->cliente_id = $clientecredito;
				$cccc->chequeclientedetalle_id = ($ingresotipopago_id == 1) ? $chequeclientedetalle_id : 0;
				$cccc->transferenciaclientedetalle_id = ($ingresotipopago_id == 2) ? $transferenciaclientedetalle_id : 0;
				$cccc->usuario_id = $usuario_id;
				$cccc->save();
			} else {
				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
				$cccc->get();
				$importe_actual = $cccc->importe;
				$nuevo_importe = $importe_actual + $importe;

				$cccc = new CuentaCorrienteClienteCredito();
				$cccc->fecha = date('Y-m-d');
				$cccc->hora = date('H:i:s');
				$cccc->referencia = $referencia;
				$cccc->importe = $nuevo_importe;
				$cccc->movimiento = $importe;
				$cccc->cuentacorrientecliente_id = 0;
				$cccc->egreso_id = 0;
				$cccc->cliente_id = $clientecredito;
				$cccc->chequeclientedetalle_id = ($ingresotipopago_id == 1) ? $chequeclientedetalle_id : 0;
				$cccc->transferenciaclientedetalle_id = ($ingresotipopago_id == 2) ? $transferenciaclientedetalle_id : 0;
				$cccc->usuario_id = $usuario_id;
				$cccc->save();	
			}
		}

		header("Location: " . URL_APP . "/cuentacorrientecliente/consultar_central/{$clientecentral_id}");
	}

	function traer_formulario_abonar_ajax($arg) {
		$cuentacorrientecliente_id = $arg;
		$this->model->cuentacorrientecliente_id = $cuentacorrientecliente_id;
		$this->model->get();
		$egreso_id = $this->model->egreso_id;

		$cm = new Cliente();
		$cm->cliente_id = $this->model->cliente_id;
		$cm->get();

		$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
		$from = "cuentacorrientecliente ccc";
		$where = "ccc.egreso_id = {$egreso_id}";
		$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
		}

		$ingresotipopago_collection = Collector()->get('IngresoTipoPago');
		foreach ($ingresotipopago_collection as $clave=>$valor) {
			if($valor->ingresotipopago_id == 4) unset($ingresotipopago_collection[$clave]);
		}

		$this->view->traer_formulario_abonar_ajax($cobrador_collection, $ingresotipopago_collection, $this->model, $cm, $balance);
	}

	function traer_chequeclientedetalle_ajax($arg) {
    	SessionHandler()->check_session();
		$ids = explode('@', $arg);
		$chequeclientedetalle_id = $ids[0];
		$cliente_id = $ids[1];
		$cpdm = new ChequeClienteDetalle();
		$cpdm->chequeclientedetalle_id = $chequeclientedetalle_id;
		$cpdm->get();
		$this->view->traer_chequeclientedetalle_ajax($cpdm, $cliente_id);
	}

	function traer_transferenciaclientedetalle_ajax($arg) {
    	SessionHandler()->check_session();
		$transferenciaclientedetalle_id = $arg;
		$tpdm = new TransferenciaClienteDetalle();
		$tpdm->transferenciaclientedetalle_id = $transferenciaclientedetalle_id;
		$tpdm->get();
		$this->view->traer_transferenciaclientedetalle_ajax($tpdm);
	}

	function traer_listado_movimientos_cuentacorriente_ajax($arg) {
		$egreso_id = $arg;
		$select = "date_format(ccc.fecha, '%d/%m/%Y') AS FECHA, ccc.importe AS IMPORTE, ccc.ingreso AS INGRESO, tmc.denominacion AS MOVIMIENTO, ccc.egreso_id AS EID, ccc.referencia AS REFERENCIA, CASE ccc.tipomovimientocuenta WHEN 1 THEN 'danger' WHEN 2 THEN 'success' END AS CLASS, ccc.cuentacorrientecliente_id CCCID";
		$from = "cuentacorrientecliente ccc INNER JOIN tipomovimientocuenta tmc ON ccc.tipomovimientocuenta = tmc.tipomovimientocuenta_id";
		$where = "ccc.egreso_id = {$egreso_id}";
		$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
		
		foreach ($cuentacorriente_collection as $clave=>$valor) {
		
			$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE, 'inline-block' AS BTN_DISPLAY";
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
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$cuentacorrientecliente_id = $arg;
		$this->model->cuentacorrientecliente_id = $cuentacorrientecliente_id;
		$this->model->get();
		$cliente_id = $this->model->cliente_id;
		$egreso_id = $this->model->egreso_id;
		$ingresotipopago_id = $this->model->ingresotipopago->ingresotipopago_id;
		$importe = $this->model->ingreso;
		$this->model->delete();

		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();

		$select = "eafip.punto_venta AS PUNTO_VENTA, eafip.numero_factura AS NUMERO_FACTURA, tf.nomenclatura AS TIPOFACTURA, eafip.cae AS CAE, eafip.vencimiento AS FVENCIMIENTO, eafip.fecha AS FECHA, tf.tipofactura_id AS TF_ID";
		$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
		$where = "eafip.egreso_id = {$egreso_id}";
		$egresoafip = CollectorCondition()->get('EgresoAfip', $where, 4, $from, $select);

		if (is_array($egresoafip)) {
			$em->punto_venta = $egresoafip[0]['PUNTO_VENTA'];
			$em->numero_factura = $egresoafip[0]['NUMERO_FACTURA'];
		}

		$comprobante = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
		$comprobante .= str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);

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

			switch ($ingresotipopago_id) {
				case 1:
					#CHEQUE
					$select = "ccd.chequeclientedetalle_id AS ID";
					$from = "chequeclientedetalle ccd";
					$where = "ccd.egreso_id = {$egreso_id} AND ccd.cuentacorrientecliente_id = {$cuentacorrientecliente_id}";
					$chequeclientedetalle_id = CollectorCondition()->get('ChequeClienteDetalle', $where, 4, $from, $select);
					$chequeclientedetalle_id = (is_array($chequeclientedetalle_id) AND !empty($chequeclientedetalle_id)) ? $chequeclientedetalle_id[0]['ID'] : 0;
					
					if ($chequeclientedetalle_id != 0) {
						$ccdm = new ChequeClienteDetalle();
						$ccdm->chequeclientedetalle_id = $chequeclientedetalle_id;
						$ccdm->delete();
					}
					break;
				case 2:
					#TRANSFERENCIA
					$select = "tcd.transferenciaclientedetalle_id AS ID";
					$from = "transferenciaclientedetalle tcd";
					$where = "tcd.egreso_id = {$egreso_id} AND tcd.cuentacorrientecliente_id = {$cuentacorrientecliente_id}";
					$transferenciaclientedetalle_id = CollectorCondition()->get('TransferenciaClienteDetalle', $where, 4, $from, $select);
					$transferenciaclientedetalle_id = (is_array($transferenciaclientedetalle_id) AND !empty($transferenciaclientedetalle_id)) ? $transferenciaclientedetalle_id[0]['ID'] : 0;
					
					if ($transferenciaclientedetalle_id != 0) {
						$tcdm = new TransferenciaClienteDetalle();
						$tcdm->transferenciaclientedetalle_id = $transferenciaclientedetalle_id;
						$tcdm->delete();
					}
					break;
				case 5:
					#RETENCIÓN
					$select = "rcd.retencionclientedetalle_id AS ID";
					$from = "retencionclientedetalle rcd";
					$where = "rcd.egreso_id = {$egreso_id} AND rcd.cuentacorrientecliente_id = {$cuentacorrientecliente_id}";
					$retencionclientedetalle_id = CollectorCondition()->get('RetencionClienteDetalle', $where, 4, $from, $select);
					$retencionclientedetalle_id = (is_array($retencionclientedetalle_id) AND !empty($retencionclientedetalle_id)) ? $retencionclientedetalle_id[0]['ID'] : 0;
					
					if ($retencionclientedetalle_id != 0) {
						$rcdm = new RetencionClienteDetalle();
						$rcdm->retencionclientedetalle_id = $retencionclientedetalle_id;
						$rcdm->delete();
					}
					break;
				case 6:
					#CREDITO
					$select = "cccc.cuentacorrienteclientecredito_id AS ID";
					$from = "cuentacorrienteclientecredito cccc";
					$where = "cccc.cliente_id = {$cliente_id} ORDER BY cccc.cuentacorrienteclientecredito_id DESC LIMIT 1";
					$max_cuentacorrienteclientecredito_id = CollectorCondition()->get('CuentaCorrienteClienteCredito', $where, 4, $from, $select);
					$max_cuentacorrienteclientecredito_id = (is_array($max_cuentacorrienteclientecredito_id) AND !empty($max_cuentacorrienteclientecredito_id)) ? $max_cuentacorrienteclientecredito_id[0]['ID'] : 0;

					$cccc = new CuentaCorrienteClienteCredito();
					$cccc->cuentacorrienteclientecredito_id = $max_cuentacorrienteclientecredito_id;
					$cccc->get();
					$importe_actual = $cccc->importe;
					$nuevo_importe = $importe_actual + $importe;

					$cccc = new CuentaCorrienteClienteCredito();
					$cccc->fecha = date('Y-m-d');
					$cccc->hora = date('H:i:s');
					$cccc->referencia = "Elimina Pago de comprobante {$comprobante}";
					$cccc->importe = $nuevo_importe;
					$cccc->movimiento = round($importe, 2);
					$cccc->cuentacorrientecliente_id = 0;
					$cccc->egreso_id = $egreso_id;
					$cccc->cliente_id = $cliente_id;
					$cccc->chequeclientedetalle_id = 0;
					$cccc->transferenciaclientedetalle_id = 0;
					$cccc->usuario_id = $usuario_id;
					$cccc->save();
					break;
			}
		}
		
		header("Location: " . URL_APP . "/cuentacorrientecliente/listar_cuentas/{$cliente_id}");
	}

	function abonar_chequecliente() {
		$chequeclientedetalle_id = filter_input(INPUT_POST, 'chequeclientedetalle_id');
		$cliente_id = filter_input(INPUT_POST, 'cliente_id');
		$cpdm = new ChequeClienteDetalle();
		$cpdm->chequeclientedetalle_id = $chequeclientedetalle_id;
		$cpdm->get();
		$cpdm->estado = 2;
		$cpdm->fecha_pago = date('Y-m-d');
		$cpdm->save();
		header("Location: " . URL_APP . "/cuentacorrientecliente/consultar/{$cliente_id}");
	}
}
?>