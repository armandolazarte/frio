<?php
require_once "modules/entregaclientedetalle/model.php";
require_once "modules/entregaclientedetalle/view.php";
require_once "modules/vendedor/model.php";
require_once "modules/cobrador/model.php";
require_once "modules/entregacliente/model.php";
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/ingresotipopago/model.php";
require_once "tools/cobrosPDFTool.php";


class EntregaClienteDetalleController {

	function __construct() {
		$this->model = new EntregaClienteDetalle();
		$this->view = new EntregaClienteDetalleView();
	}

  	function panel() {
    	SessionHandler()->check_session();
    	$vendedor_collection = Collector()->get('Vendedor');
		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
			if ($valor->vendedor_id == 0) unset($cobrador_collection[$clave]);
		}

		foreach ($vendedor_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($vendedor_collection[$clave]);
		}

    	$this->view->panel($vendedor_collection,$cobrador_collection);
  	}

  	function vendedor_cobranza($arg) {
	    SessionHandler()->check_session();

	    $select = "ecd.entregaclientedetalle_id AS ID, ec.entregacliente_id AS ENTREGACLIENTE, ecd.egreso_id AS EGRESO, CONCAT(c.razon_social,'(', c.nombre_fantasia , ')') AS CLIENTE, ec.cliente_id AS CLINT, ec.estado AS ESTADO, ec.fecha AS FECHA, CONCAT('$',ec.monto) AS MONTO, e.punto_venta AS PUNTO_VENTA, ecd.parcial AS VAL_PARCIAL, (CASE WHEN ecd.parcial = 1 THEN 'PARCIAL' ELSE 'TOTAL' END) AS PARCIAL, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, ecd.ingresotipopago_id AS INGTIPPAG";
	    $from = "entregaclientedetalle ecd INNER JOIN entregacliente ec ON ec.entregacliente_id = ecd.entregacliente_id INNER JOIN cliente c on c.cliente_id = ec.cliente_id INNER JOIN egreso e ON e.egreso_id = ecd.egreso_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
	    $where = "ec.vendedor_id  = {$arg} AND ec.anulada = 0 and ec.estado = 1 and ec.fecha < now() ORDER BY ecd.egreso_id ASC";
	    $entregacliente_collection = CollectorCondition()->get('EntregaClienteDetalle', $where, 4, $from, $select);
	    $entregacliente_collection = (is_array($entregacliente_collection) AND !empty($entregacliente_collection)) ? $entregacliente_collection : array();

	    if (!empty($entregacliente_collection)) {
	    	
	    	foreach ($entregacliente_collection as $clave=>$valor) {
	    		$egreso_id = $valor['EGRESO'];
	    		$cliente_id = $valor['CLINT'];
		    	$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
					  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$egreso_id} AND ccc.cliente_id = {$cliente_id}";
				$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
	    		$balance = (is_array($balance) AND !empty($balance)) ? $balance[0]['BALANCE'] : 0;				
				$entregacliente_collection[$clave]['BALANCE'] = $balance;

				$itpm = new IngresoTipoPago();
				$itpm->ingresotipopago_id = $entregacliente_collection[$clave]['INGTIPPAG'];
				$itpm->get();
				$temp_tipo_pago = $itpm->denominacion;
				$entregacliente_collection[$clave]['DENTIPPAG'] = $temp_tipo_pago;
	    	}
	    }

	    $this->view->vendedor_cobranza($entregacliente_collection);
  	}

  	function vendedor_cobranza_total($arg) {
	    SessionHandler()->check_session();
	    $select = "CONCAT('$ ',ROUND(SUM(ec.monto), 2)) AS TOTAL";
	    $from = "entregaclientedetalle ecd INNER JOIN entregacliente ec ON ec.entregacliente_id = ecd.entregacliente_id";
	    $where = "ec.vendedor_id  = {$arg} AND ec.anulada = 0 and ec.estado = 1 and ec.fecha < now() ORDER BY ecd.egreso_id ASC";
	    $cobranza = CollectorCondition()->get('EntregaClienteDetalle', $where, 4, $from, $select);
	    $total = (is_array($cobranza)) ? $cobranza[0]["TOTAL"] : 0 ;
	    print_r($total);
  	}

	function guardar() {
    	SessionHandler()->check_session();
		$fecha = date('Y-m-d');
		$total = filter_input(INPUT_POST, 'total');
		$total = explode("$", $total);
		$total = $total[1];
		$cobrador = filter_input(INPUT_POST, 'cobrador');
		$var = explode("@", $cobrador);
		$cobrador_id = $var[0];
		$cobrador_denominacion = $var[1];
		$cobros_array = $_POST['cobro'];

		if (is_array($cobros_array)) {
			$fecha_actual = date('Y-m-d');
			$hora = date('H:i:s');

			/*PROCESA COBRO*/
			foreach ($cobros_array as $key => $cobro) {
				$comprobante = str_pad($cobro['punto_venta'], 4, '0', STR_PAD_LEFT) . "-";
				$comprobante .= str_pad($cobro['factura'], 8, '0', STR_PAD_LEFT);

	 			$monto = explode("$", $cobro['monto']);
				$egreso_id = $cobro['egreso_id'];
				$importe = $monto[1];
				$entregacliente_id = $cobro['entregacliente_id'];

				$ecdm = new EntregaCliente();
				$ecdm->entregacliente_id  = $entregacliente_id;
				$ecdm->get();
				$cliente_id = $ecdm->cliente_id;

				$ecdm->estado = 2;
				$ecdm->save();

				if ($cobro['val_parcial'] == 1) {
					$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) -
							  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
					$from = "cuentacorrientecliente ccc";
					$where = "ccc.egreso_id = {$egreso_id} AND ccc.cliente_id = {$cliente_id}";
					$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
					$deuda = abs($balance[0]['BALANCE']) - $importe;
					if ($deuda > 0) {
						$estadomovimientocuenta = 3;
					} else {
						$select = "ccc.cuentacorrientecliente_id AS ID";
						$from = "cuentacorrientecliente ccc";
						$where = "ccc.egreso_id = {$egreso_id} AND ccc.estadomovimientocuenta IN (1,2,3) AND ccc.cliente_id = {$cliente_id}";
						$cuentacorriente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
						$estadomovimientocuenta = 4;

						foreach ($cuentacorriente_collection as $cuentacorrientecliente) {
							$cuentacorrientecliente_id = $cuentacorrientecliente['ID'];
							$cccm_temp = new CuentaCorrienteCliente();
							$cccm_temp->cuentacorrientecliente_id = $cuentacorrientecliente_id;
							$cccm_temp->get();
							$cccm_temp->estadomovimientocuenta = 4;
							$cccm_temp->save();
						}
					}

					$cccm = new CuentaCorrienteCliente();
					$cccm->fecha = date('Y-m-d');
					$cccm->hora = date('H:i:s');
					$cccm->referencia = "Pago de comprobante {$comprobante}";
					$cccm->importe = $monto[1];
					$cccm->ingreso = $monto[1];
					$cccm->cliente_id = $cobro['cliente_id'];
					$cccm->egreso_id = $egreso_id;
					$cccm->ingresotipopago = $cobro['ingresotipopago_id'];
					$cccm->tipomovimientocuenta = 2;
					$cccm->estadomovimientocuenta = $estadomovimientocuenta;
					$cccm->cobrador = $cobrador_id;
					$cccm->save();
				} else {
					$select = "ccc.cuentacorrientecliente_id AS ID ";
					$from = "cuentacorrientecliente ccc";
					$where = "ccc.egreso_id = {$egreso_id} AND ccc.cliente_id = {$cliente_id} ORDER BY ccc.cuentacorrientecliente_id ASC";
					$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);

					foreach ($cuentacorrientecliente_collection as $key => $cuentacorrientecliente) {
						$cccm = new CuentaCorrienteCliente();
						$cccm->cuentacorrientecliente_id = $cuentacorrientecliente['ID'];
						$cccm->get();
						$cccm->estadomovimientocuenta = 4;
						$cccm->save();
					}

					$cccm = new CuentaCorrienteCliente();
					$cccm->fecha = date('Y-m-d');
					$cccm->hora = date('H:i:s');
					$cccm->referencia = "Pago de comprobante {$comprobante}";
					$cccm->importe = $monto[1];
					$cccm->ingreso = $monto[1];
					$cccm->cliente_id = $cobro['cliente_id'];
					$cccm->egreso_id = $egreso_id;
					$cccm->ingresotipopago = $cobro['ingresotipopago_id'];
					$cccm->tipomovimientocuenta = 2;
					$cccm->estadomovimientocuenta = 4;
					$cccm->cobrador = $cobrador_id;
					$cccm->save();
				}
			}
		}

		header("Location: " . URL_APP . "/entregaclientedetalle/panel");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$entregaclientedetalle_id = $arg;
		$this->model->entregaclientedetalle_id = $entregaclientedetalle_id;
		$this->model->get();
		$entregacliente_id = $this->model->entregacliente_id;

		$ecm = new EntregaCliente();
		$ecm->entregacliente_id = $entregacliente_id;
		$ecm->get();
		$vendedor_id = $ecm->vendedor_id;
		$ecm->delete();
		$this->model->delete();

		header("Location: " . URL_APP . "/entregaclientedetalle/panel_vendedor_cobranza/{$vendedor_id}");
	}

	function editar_ajax($arg) {
		SessionHandler()->check_session();
		$entregaclientedetalle_id = $arg;
		$this->model->entregaclientedetalle_id = $entregaclientedetalle_id;
		$this->model->get();
		$entregacliente_id = $this->model->entregacliente_id;
		$ingresotipopago_id = $this->model->ingresotipopago_id;

		$itpm = new IngresoTipoPago();
		$itpm->ingresotipopago_id = $ingresotipopago_id;
		$itpm->get();
		$this->model->ingresotipopago = $itpm;

		$ecm = new EntregaCliente();
		$ecm->entregacliente_id = $entregacliente_id;
		$ecm->get();

		$ingresotipopago_collection = Collector()->get('IngresoTipoPago');
		$this->view->editar_ajax($ingresotipopago_collection, $ecm, $this->model);
	}

	function actualizar() {
		SessionHandler()->check_session();
		$entregacliente_id = filter_input(INPUT_POST, "entregacliente_id");
		$entregaclientedetalle_id = filter_input(INPUT_POST, "entregaclientedetalle_id");
		$monto = filter_input(INPUT_POST, "monto");
		$parcial = filter_input(INPUT_POST, "parcial");
		$vendedor_id = filter_input(INPUT_POST, "vendedor_id");
		$ingresotipopago_id = filter_input(INPUT_POST, "ingresotipopago");

		$this->model->entregaclientedetalle_id = $entregaclientedetalle_id;
		$this->model->get();
		$this->model->monto = $monto;
		$this->model->parcial = $monto;
		$this->model->ingresotipopago_id = $ingresotipopago_id;
		$this->model->save();

		$ecm = new EntregaCliente();
		$ecm->entregacliente_id = $entregacliente_id;
		$ecm->get();
		$ecm->monto = $monto;
		$ecm->save();

		header("Location: " . URL_APP . "/entregaclientedetalle/panel_vendedor_cobranza/{$vendedor_id}");
	}

	function panel_vendedor_cobranza($arg) {
    	SessionHandler()->check_session();
    	$vendedor_collection = Collector()->get('Vendedor');
		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
		}

		$vendedor_id = $arg;
		$select = "ecd.entregaclientedetalle_id AS ID, ec.entregacliente_id AS ENTREGACLIENTE, ecd.egreso_id AS EGRESO, CONCAT(c.razon_social,'(', c.nombre_fantasia , ')') AS CLIENTE, ec.cliente_id AS CLINT, ec.estado AS ESTADO, ec.fecha AS FECHA, CONCAT('$',ec.monto) AS MONTO, e.punto_venta AS PUNTO_VENTA, ecd.parcial AS VAL_PARCIAL, (CASE WHEN ecd.parcial = 1 THEN 'PARCIAL' ELSE 'TOTAL' END) AS PARCIAL, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, ecd.ingresotipopago_id AS INGTIPPAG";
	    $from = "entregaclientedetalle ecd INNER JOIN entregacliente ec ON ec.entregacliente_id = ecd.entregacliente_id INNER JOIN cliente c on c.cliente_id = ec.cliente_id INNER JOIN egreso e ON e.egreso_id = ecd.egreso_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
	    $where = "ec.vendedor_id  = {$arg} AND ec.anulada = 0 and ec.estado=1 and ec.fecha < now() ORDER BY ecd.egreso_id ASC";
	    $entregacliente_collection = CollectorCondition()->get('EntregaClienteDetalle', $where, 4, $from, $select);

	    if (!empty($entregacliente_collection)) {
	    	
	    	foreach ($entregacliente_collection as $clave=>$valor) {
	    		$egreso_id = $valor['EGRESO'];
	    		$cliente_id = $valor['CLINT'];
		    	$select = "ROUND(((ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 2 THEN importe ELSE 0 END),2)) - 
					  (ROUND(SUM(CASE WHEN ccc.tipomovimientocuenta = 1 THEN importe ELSE 0 END),2))),2) AS BALANCE";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$egreso_id} AND ccc.cliente_id = {$cliente_id}";
				$balance = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
	    		$balance = (is_array($balance) AND !empty($balance)) ? $balance[0]['BALANCE'] : 0;				
				$entregacliente_collection[$clave]['BALANCE'] = $balance;

				$itpm = new IngresoTipoPago();
				$itpm->ingresotipopago_id = $entregacliente_collection[$clave]['INGTIPPAG'];
				$itpm->get();
				$temp_tipo_pago = $itpm->denominacion;
				$entregacliente_collection[$clave]['DENTIPPAG'] = $temp_tipo_pago;
	    	}
	    }

	    $select = "CONCAT('$ ',ROUND(SUM(ec.monto), 2)) AS TOTAL";
	    $from = "entregaclientedetalle ecd INNER JOIN entregacliente ec ON ec.entregacliente_id = ecd.entregacliente_id";
	    $where = "ec.vendedor_id  = {$vendedor_id} AND ec.anulada = 0 and ec.estado = 1 and ec.fecha < now() ORDER BY ecd.egreso_id ASC";
	    $cobranza = CollectorCondition()->get('EntregaClienteDetalle', $where, 4, $from, $select);
	    $total_cobranza = (is_array($cobranza)) ? $cobranza[0]["TOTAL"] : 0 ;

    	$this->view->panel_vendedor_cobranza($vendedor_collection, $cobrador_collection, $entregacliente_collection, $total_cobranza, $vendedor_id);
  	}

  	function imprimir_cobranza($arg) {
    	SessionHandler()->check_session();
    	$fecha = date('Y-m-d');
    	$ids = explode("@", $arg);
    	$vendedor_id = $ids[0];
    	$cobrador_id = $ids[1];

  		$select = "CONCAT('$',ROUND(SUM(ec.monto), 2)) AS TOTAL";
	    $from = "entregaclientedetalle ecd INNER JOIN entregacliente ec ON ec.entregacliente_id = ecd.entregacliente_id";
	    $where = "ec.vendedor_id  = {$vendedor_id} AND ec.anulada = 0 and ec.estado = 1 and ec.fecha < now() ORDER BY ecd.egreso_id ASC";
	    $cobranza = CollectorCondition()->get('EntregaClienteDetalle', $where, 4, $from, $select);
	    $total = (is_array($cobranza)) ? $cobranza[0]["TOTAL"] : 0 ;

	    $cm = new Cobrador();
	    $cm->cobrador_id = $cobrador_id;
	    $cm->get();
	    $cobrador_denominacion = $cm->denominacion;

	    $select = "ecd.entregaclientedetalle_id AS ID, ec.entregacliente_id AS ENTREGACLIENTE, ecd.egreso_id AS EGRESO, CONCAT(c.razon_social,'(', c.nombre_fantasia , ')') AS CLIENTE, ec.cliente_id AS CLINT, ec.estado AS ESTADO, e.fecha AS FECHA, CONCAT('$',ec.monto) AS MONTO, e.punto_venta AS PUNTO_VENTA, ecd.parcial AS VAL_PARCIAL, (CASE WHEN ecd.parcial = 1 THEN 'PARCIAL' ELSE 'TOTAL' END) AS PARCIAL, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
	    $from = "entregaclientedetalle ecd INNER JOIN entregacliente ec ON ec.entregacliente_id = ecd.entregacliente_id INNER JOIN cliente c on c.cliente_id = ec.cliente_id INNER JOIN egreso e ON e.egreso_id = ecd.egreso_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
	    $where = "ec.vendedor_id  = {$vendedor_id} AND ec.anulada = 0 and ec.estado = 1 and ec.fecha < now() ORDER BY ecd.egreso_id ASC";
	    $entregacliente_collection = CollectorCondition()->get('EntregaClienteDetalle', $where, 4, $from, $select);

    	/*GENERACION DE PDF*/
		$cuentaCorrienteProveedorPDFHelper = new cobrosPDF();
		$cuentaCorrienteProveedorPDFHelper->descarga_cobros_vendedor($fecha, $total, $cobrador_id, $cobrador_denominacion, $entregacliente_collection);
  		print_r($valores);exit;
  	}
}
?>