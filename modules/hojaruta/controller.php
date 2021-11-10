<?php
require_once "modules/hojaruta/model.php";
require_once "modules/hojaruta/view.php";
require_once "modules/egreso/model.php";
require_once "modules/egresoafip/model.php";
require_once "modules/estadoentrega/model.php";
require_once "modules/egresoentrega/model.php";
require_once "modules/cuentacorrientecliente/model.php";


class HojaRutaController {

	function __construct() {
		$this->model = new HojaRuta();
		$this->view = new HojaRutaView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$periodo_actual = date('Ym');
    	$select = "hr.hojaruta_id AS HRID, hr.fecha AS FECHA, f.denominacion AS FLETE, ee.denominacion AS ESTENTREGA, hr.egreso_ids AS EIDS, CASE WHEN hr.estadoentrega = 7 THEN 'none' ELSE 'inline-block' END AS BTN_CERRAR_HR, CASE WHEN hr.estadoentrega = 7 THEN 'final_hoja_ruta_flete'  ELSE 'reimprimir_hoja_ruta_flete'  END AS BTN_PRINT, hr.estadoentrega AS EEID";
    	$from = "hojaruta hr INNER JOIN flete f ON hr.flete_id = f.flete_id INNER JOIN estadoentrega ee ON hr.estadoentrega = ee.estadoentrega_id";
    	$where = "date_format(hr.fecha, '%Y%m') = {$periodo_actual}";
    	$hojaruta_collection = CollectorCondition()->get('HojaRuta', $where, 4, $from, $select);

    	if (!is_array($hojaruta_collection)) {
    		$hojaruta_collection = array();
    	} else {
    		foreach ($hojaruta_collection as $clave=>$valor) {
    			$hojaruta_id = $valor['HRID'];
    			$egreso_ids = $valor['EIDS'];
    			$temp_estadoentrega_id = $valor['EEID'];
    			$array_egreso_ids = explode(',', $egreso_ids);

    			if (!is_array($array_egreso_ids)) $array_egreso_ids = array();
    			$array_nums_facturas = array();
    			foreach ($array_egreso_ids as $egreso) {
    				$ids = explode('@', $egreso);
    				$egreso_id = $ids[0];
    				$estadoentrega_id = $ids[1];

    				$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
					$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
					$where = "eafip.egreso_id = {$egreso_id}";
					$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);

					if ($temp_estadoentrega_id == 7) {
						$lbl_quitar = '';
					} else {
						$lbl_quitar = "<a href='{url_app}/hojaruta/liberar_egreso/{$hojaruta_id}@{$egreso_id}' class='btn btn-danger btn-xs' title='Quitar de hoja de ruta'><i class='fa fa-trash-o'></i> Quitar de Hoja de Ruta</a>";
					}

					if (is_array($eafip)) {
						$factura = $eafip[0]['REFERENCIA'] . " {$lbl_quitar}";
					} else {
						$em = new Egreso();
						$em->egreso_id = $egreso_id;
						$em->get();
						$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
						$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
						$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
						$factura = "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura} {$lbl_quitar}";
					}

					$array_nums_facturas[] = $factura;
    			}

    			$hojaruta_collection[$clave]['FACTURAS'] = implode('<br />', $array_nums_facturas);
    		}
    	}

    	$this->view->panel($hojaruta_collection);
	}

	function filtro_hojas_rutas() {
    	SessionHandler()->check_session();
    	$periodo_actual = date('Ym');
    	$desde = filter_input(INPUT_POST, 'desde');
    	$hasta = filter_input(INPUT_POST, 'hasta');
    	$select = "hr.hojaruta_id AS HRID, hr.fecha AS FECHA, f.denominacion AS FLETE, ee.denominacion AS ESTENTREGA, hr.egreso_ids AS EIDS,
    			   CASE WHEN hr.estadoentrega = 7 THEN 'none' ELSE 'inline-block' END AS BTN_CERRAR_HR";
    	$from = "hojaruta hr INNER JOIN flete f ON hr.flete_id = f.flete_id INNER JOIN estadoentrega ee ON hr.estadoentrega = ee.estadoentrega_id";
    	$where = "hr.fecha BETWEEN '{$desde}' AND '{$hasta}'";
    	$hojaruta_collection = CollectorCondition()->get('HojaRuta', $where, 4, $from, $select);

    	if (!is_array($hojaruta_collection)) {
    		$hojaruta_collection = array();
    	} else {
    		foreach ($hojaruta_collection as $clave=>$valor) {
    			$egreso_ids = $valor['EIDS'];
    			$array_egreso_ids = explode(',', $egreso_ids);

    			if (!is_array($array_egreso_ids)) $array_egreso_ids = array();
    			$array_nums_facturas = array();
    			foreach ($array_egreso_ids as $egreso) {
    				$ids = explode('@', $egreso);
    				$egreso_id = $ids[0];
    				$estadoentrega_id = $ids[1];

    				$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
					$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
					$where = "eafip.egreso_id = {$egreso_id}";
					$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);

					$eem = new EstadoEntrega();
					$eem->estadoentrega_id = $estadoentrega_id;
					$eem->get();
					$denominacion_estadoentrega = $eem->denominacion;

					if (is_array($eafip)) {
						$factura = $eafip[0]['REFERENCIA'] . " - {$denominacion_estadoentrega}";
					} else {
						$em = new Egreso();
						$em->egreso_id = $egreso_id;
						$em->get();
						$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
						$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
						$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
						$factura = "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura} - {$denominacion_estadoentrega}";
					}

					$array_nums_facturas[] = $factura;
    			}

    			$hojaruta_collection[$clave]['FACTURAS'] = implode('<br />', $array_nums_facturas);
    		}
    	}

    	$this->view->panel($hojaruta_collection);
	}

	function traer_formulario_entrega_ajax($arg) {
		SessionHandler()->check_session();
		$this->model->hojaruta_id = $arg;
		$this->model->get();

		$array_formulario = array();
		$egreso_ids = explode(',', $this->model->egreso_ids);
		foreach ($egreso_ids as $egreso) {
			$ids = explode('@', $egreso);
			$egreso_id = $ids[0];

			$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
			$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
			$where = "eafip.egreso_id = {$egreso_id}";
			$eafip = CollectorCondition()->get('EgrasoAFIP', $where, 4, $from, $select);

			$em = new Egreso();
			$em->egreso_id = $egreso_id;
			$em->get();

			$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
			$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
			$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
			$factura = (is_array($eafip)) ? $eafip[0]['REFERENCIA'] : "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura}";

			if ($em->condicionpago->condicionpago_id == 1) {
				$select = "ccc.estadomovimientocuenta AS ESTMOVCUENTA";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC";
				$cuentacorrientecliente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
				$estadomovimientocuenta = (is_array($cuentacorrientecliente) AND !empty($cuentacorrientecliente)) ? $cuentacorrientecliente[0]['ESTMOVCUENTA'] : 0;

				switch ($estadomovimientocuenta) {
					case 1:
						$chk_abonado_check = 'checked';
						$chk_abonado_display = 'block';
						$chk_abonado_msj = 'Abonar';
						$txt_abonado_msj = '';
						$txt_abonado_display = 'none';
						break;
					case 3:
						$chk_abonado_check = '';
						$chk_abonado_display = 'none';
						$chk_abonado_msj = '';
						$txt_abonado_msj = 'Posee un pago parcial.';
						$txt_abonado_display = 'block';
						break;
					case 4:
						$chk_abonado_check = '';
						$chk_abonado_display = 'none';
						$chk_abonado_msj = '';
						$txt_abonado_msj = 'Comprobante abonado.';
						$txt_abonado_display = 'block';
						break;
				}

				$txt_tipopago_msj = 'Cuenta Corriente';
			} else {
				$chk_abonado_check = '';
				$chk_abonado_display = 'none';
				$chk_abonado_msj = '';
				$txt_abonado_msj = 'Comprobante contado.';
				$txt_tipopago_msj = 'Contado';
				$txt_abonado_display = 'block';
			}

			$array_temp = array('{formulario-egreso_id}'=>$egreso_id,
								'{formulario-chk_abonado_check}'=>$chk_abonado_check,
								'{formulario-chk_abonado_display}'=>$chk_abonado_display,
								'{formulario-chk_abonado_msj}'=>$chk_abonado_msj,
								'{formulario-txt_abonado_msj}'=>$txt_abonado_msj,
								'{formulario-txt_abonado_display}'=>$txt_abonado_display,
								'{formulario-txt_tipopago_msj}'=>$txt_tipopago_msj,
								'{formulario-factura}'=>$factura);
			$array_formulario[] = $array_temp;
		}

		$this->view->traer_formulario_entrega_ajax($array_formulario, $this->model);
	}

	function cerrar_hojaruta() {
		SessionHandler()->check_session();

		$hojaruta_id = filter_input(INPUT_POST, 'hojaruta_id');
		$this->model->hojaruta_id = $hojaruta_id;
		$this->model->get();

		$egreso_estadoentrega_array = $_POST["egreso_estadoentrega"];
		$egreso_abonado_array = $_POST["egreso_abonado"];
		$array_egreso_ids = array();
		foreach ($egreso_estadoentrega_array as $clave=>$valor) {
			$array_egreso_ids[] = "{$clave}@{$valor}";
			$egreso_id = $clave;
			$em = new Egreso();
			$em->egreso_id = $egreso_id;
			$em->get();

			$egresoentrega_id = $em->egresoentrega->egresoentrega_id;
			$eem = new EgresoEntrega();
			$eem->egresoentrega_id = $egresoentrega_id;
			$eem->get();
			$eem->estadoentrega = $valor;
			$eem->save();
		}

		if (!empty($egreso_abonado_array) AND is_array($egreso_abonado_array)) {
			foreach ($egreso_abonado_array as $clave=>$valor) {
				$select = "ccc.cuentacorrientecliente_id AS CCCID";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$clave} ORDER BY ccc.cuentacorrientecliente_id DESC LIMIT 1";
				$cuentacorrientecliente_id = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
				$cuentacorrientecliente_id = $cuentacorrientecliente_id[0]['CCCID'];

				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$cccm->get();

				$cccma = new CuentaCorrienteCliente();
				$cccma->fecha = date('Y-m-d');
				$cccma->hora = date('H:i:s');
				$cccma->referencia = "Pago " . $cccm->referencia;
				$cccma->importe = $cccm->importe;
				$cccma->ingreso = $cccm->importe;
				$cccma->cliente_id = $cccm->cliente_id;
				$cccma->egreso_id = $cccm->egreso_id;
				$cccma->tipomovimientocuenta = 2;
				$cccma->estadomovimientocuenta = 4;
				$cccma->save();

				$cccm->estadomovimientocuenta = 4;
				$cccm->save();
			}
		}

		$egreso_ids = implode(',', $array_egreso_ids);
		$this->model->estadoentrega = 7	;
		$this->model->egreso_ids = $egreso_ids;
		$this->model->save();

		header("Location: " . URL_APP . "/hojaruta/panel");
	}

	function entregas($arg) {
		SessionHandler()->check_session();

		$this->model->hojaruta_id = $arg;
		$this->model->get();

		$fm = new Flete();
		$fm->flete_id = $this->model->flete_id;
		$fm->get();
		$flete = $fm->denominacion;

		$monto_total=0;
		$array_formulario = array();
		$egreso_ids = explode(',', $this->model->egreso_ids);
		foreach ($egreso_ids as $egreso) {
			$ids = explode('@', $egreso);
			$egreso_id = $ids[0];

			$select = "CONCAT(tf.nomenclatura, ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) AS REFERENCIA";
			$from = "egresoafip eafip INNER JOIN tipofactura tf ON eafip.tipofactura = tf.tipofactura_id";
			$where = "eafip.egreso_id = {$egreso_id}";
			$eafip = CollectorCondition()->get('EgresoAFIP', $where, 4, $from, $select);

			$em = new Egreso();
			$em->egreso_id = $egreso_id;
			$em->get();

			$select = "nc.importe_total AS IMPORTETOTAL";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$egreso_id}";
			$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($notacredito) AND !empty($notacredito)) {
				$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
				$monto = $em->importe_total - $importe_notacredito;
			} else {
				$monto = $em->importe_total;
			}
			
			//$monto = $em->importe_total;
			$fecha = $em->fecha.' '.$em->hora;
			$cliente = $em->cliente->razon_social;
			$tipofactura_nomenclatura = $em->tipofactura->nomenclatura;
			$punto_venta = str_pad($em->punto_venta, 4, '0', STR_PAD_LEFT);
			$numero_factura = str_pad($em->numero_factura, 8, '0', STR_PAD_LEFT);
			$factura = (is_array($eafip)) ? $eafip[0]['REFERENCIA'] : "{$tipofactura_nomenclatura} {$punto_venta}-{$numero_factura}";

			if ($em->condicionpago->condicionpago_id == 1) {
				$select = "ccc.estadomovimientocuenta AS ESTMOVCUENTA";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC";
				$cuentacorrientecliente = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
				$estadomovimientocuenta = (is_array($cuentacorrientecliente) AND !empty($cuentacorrientecliente)) ? $cuentacorrientecliente[0]['ESTMOVCUENTA'] : 0;

				switch ($estadomovimientocuenta) {
					case 1:
						$chk_abonado_check = '';
						$chk_abonado_display = 'block';
						$chk_abonado_msj = 'Debe';
						$txt_abonado_msj = '';
						$txt_abonado_display = 'none';
						break;
					case 3:
						$chk_abonado_check = '';
						$chk_abonado_display = 'none';
						$chk_abonado_msj = '';
						$txt_abonado_msj = 'Posee un pago parcial.';
						$txt_abonado_display = 'block';
						break;
					case 4:
						$chk_abonado_check = '';
						$chk_abonado_display = 'none';
						$chk_abonado_msj = '';
						$txt_abonado_msj = 'Comprobante abonado.';
						$txt_abonado_display = 'block';
						break;
				}

				$txt_tipopago_msj = 'Cuenta Corriente';
			} else {
				$chk_abonado_check = '';
				$chk_abonado_display = 'none';
				$chk_abonado_msj = '';
				$txt_abonado_msj = 'Comprobante contado.';
				$txt_tipopago_msj = 'Contado';
				$txt_abonado_display = 'block';
			}

			$array_temp = array('{formulario-egreso_id}'=>$egreso_id,
								'{formulario-chk_abonado_check}'=>$chk_abonado_check,
								'{formulario-chk_abonado_display}'=>$chk_abonado_display,
								'{formulario-chk_abonado_msj}'=>$chk_abonado_msj,
								'{formulario-txt_abonado_msj}'=>$txt_abonado_msj,
								'{formulario-txt_abonado_display}'=>$txt_abonado_display,
								'{formulario-txt_tipopago_msj}'=>$txt_tipopago_msj,
								'{formulario-factura}'=>$factura,
								'{formulario-monto}'=>$monto,
								'{formulario-cliente}'=>$cliente,
								'{formulario-fecha}'=>$fecha);
			$array_formulario[] = $array_temp;
			$monto_total = $monto_total + $monto;
		}

		$cobrador_collection = Collector()->get('Cobrador');
		foreach ($cobrador_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cobrador_collection[$clave]);
			if ($valor->flete_id == 0) unset($cobrador_collection[$clave]);
		}

		$this->view->entregas($array_formulario, $this->model,$flete,$cobrador_collection,$monto_total);
	}

	function editar_hojaruta($arg){
		SessionHandler()->check_session();
		$var = explode("@", $arg);
		$hojaruta_id = $var[0];
		$estado = $var[1];

		$hrm = new HojaRuta();
		$hrm->hojaruta_id = $hojaruta_id;
		$hrm->get();

		$fm = new Flete();
		$fm->flete_id = $hrm->flete_id;
		$fm->get();
		$flete=$fm->denominacion;

		$select = "e.egreso_id AS EGRESO_ID, date_format(e.fecha, '%d/%m/%Y') AS FECHA, UPPER(cl.razon_social) AS CLIENTE, ci.denominacion AS CI,
					 e.subtotal AS SUBTOTAL, f.denominacion AS FLETE, e.importe_total AS IMPORTETOTAL, UPPER(CONCAT(ve.APELLIDO, ' ', ve.nombre)) AS VENDEDOR,
					 UPPER(cp.denominacion) AS CP, CONCAT(ese.denominacion, ' (', date_format(ee.fecha, '%d/%m/%Y'), ')') AS ENTREGA, CASE ee.estadoentrega WHEN 1 THEN 'inline-block' WHEN 3 THEN 'none' END AS DSP_BTN_ENT,
					 CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0))
					 ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA";
		$from = "egreso e INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor ve ON e.vendedor = ve.vendedor_id INNER JOIN
				 condicionpago cp ON e.condicionpago = cp.condicionpago_id INNER JOIN condicioniva ci ON e.condicioniva = ci.condicioniva_id INNER JOIN
				 egresoentrega ee ON e.egresoentrega = ee.egresoentrega_id INNER JOIN estadoentrega ese ON ee.estadoentrega = ese.estadoentrega_id INNER JOIN
				 flete f ON ee.flete = f.flete_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "ee.estadoentrega NOT IN(3,4,5) ORDER BY e.fecha ASC";
		$egreso_collection = CollectorCondition()->get('Egreso', $where, 4, $from, $select);

		foreach ($egreso_collection as $clave=>$valor) {
			$egreso_id = $valor['EGRESO_ID'];
			$select = "nc.importe_total AS IMPORTETOTAL";
			$from = "notacredito nc";
			$where = "nc.egreso_id = {$egreso_id}";
			$notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);

			if (is_array($notacredito) AND !empty($notacredito)) {
				$importe_notacredito = $notacredito[0]['IMPORTETOTAL'];
				$egreso_collection[$clave]['NC_IMPORTE_TOTAL'] = $importe_notacredito;
				$egreso_collection[$clave]['IMPORTETOTAL'] = $egreso_collection[$clave]['IMPORTETOTAL'] - $importe_notacredito;
				$egreso_collection[$clave]['VC'] = round(($egreso_collection[$clave]['COMISION'] * $egreso_collection[$clave]['IMPORTETOTAL'] / 100),2);
			} else {
				$egreso_collection[$clave]['NC_IMPORTE_TOTAL'] = 0;
			}

			if ($egreso_collection[$clave]['IMPORTETOTAL'] == 0 AND $egreso_collection[$clave]["VC"] == 0) {
				unset($egreso_collection[$clave]);
			}
		}

		$this->view->editar_hojaruta($hrm,$flete,$egreso_collection,$estado);
	}

	function actualizar_hoja_ruta_flete($arg) {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$fecha_actual = date('Y-m-d');
		$hojaruta_id = $arg;

		if (isset($_POST['objeto']) AND is_array($_POST['objeto'])) {
			$egreso_ids = $_POST['objeto'];
			$egreso_in_ids =  implode('@3,', $egreso_ids);
			$egreso_in_ids =  "{$egreso_in_ids}@3";

			$hrm = new HojaRuta();
			$hrm->hojaruta_id = $hojaruta_id;
			$hrm->get();
			$hrm->egreso_ids = 	$hrm->egreso_ids.','.$egreso_in_ids;
			$hrm->save();

			foreach ($egreso_ids as $egreso_id) {
					$em = new Egreso();
					$em->egreso_id = $egreso_id;
					$em->get();

				$egresoentrega_id = $em->egresoentrega->egresoentrega_id;
				$eem = new EgresoEntrega();
				$eem->egresoentrega_id = $egresoentrega_id;
				$eem->get();
				$eem->estadoentrega = 3;
				$eem->save();
					}

			header("Location: " . URL_APP . "/hojaruta/entregas/{$hojaruta_id}");
		} else {
			header("Location: " . URL_APP . "/hojaruta/editar_hojaruta/{$hojaruta_id}@3");
		}
	}

	function confirmar_entregas($arg) {
		SessionHandler()->check_session();
		$hojaruta_id = $arg;

		if (isset($_POST['objeto']) AND is_array($_POST['objeto'])) {
			$egreso_ids = $_POST['objeto'];

			foreach ($egreso_ids as $egreso_id) {
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();

				$egresoentrega_id = $em->egresoentrega->egresoentrega_id;
				$eem = new EgresoEntrega();
				$eem->egresoentrega_id = $egresoentrega_id;
				$eem->get();
				$eem->estadoentrega = 4;
				$eem->save();
			}

			header("Location: " . URL_APP . "/hojaruta/editar_hojaruta/{$hojaruta_id}@5");
		} else {
			header("Location: " . URL_APP . "/hojaruta/editar_hojaruta/{$hojaruta_id}@3");
		}
	}

	function cerrar_toda_hojaruta() {
		SessionHandler()->check_session();

		$cobrador = filter_input(INPUT_POST, 'cobrador');
		$var = explode("@", $cobrador);
		$cobrador_id = $var[0];

		$hojaruta_id = filter_input(INPUT_POST, 'hojaruta_id');
		$this->model->hojaruta_id = $hojaruta_id;
		$this->model->get();

		$egreso_estadoentrega_array = $_POST["egreso_estadoentrega"];
		$egreso_abonado_array = $_POST["egreso_abonado"];
		$egreso_pagoentrega_array = $_POST["egreso_pagoentrega"];
		$egreso_monto_parcial_array = $_POST["monto_parcial"];

		$array_egreso_ids = array();
		foreach ($egreso_estadoentrega_array as $clave=>$valor) {
			$egreso_id = $clave;
			$estadoentrega_id = $valor;
			$array_egreso_ids[] = "{$egreso_id}@{$estadoentrega_id}";
			
			$em = new Egreso();
			$em->egreso_id = $egreso_id;
			$em->get();
			$egresoentrega_id = $em->egresoentrega->egresoentrega_id;

			$eem = new EgresoEntrega();
			$eem->egresoentrega_id = $egresoentrega_id;
			$eem->get();
			$eem->estadoentrega = $estadoentrega_id;
			$eem->save();

			$estado_abonado = $egreso_abonado_array[$egreso_id];
			if ($estado_abonado == 1) {
				$select = "ccc.cuentacorrientecliente_id AS CCCID";
				$from = "cuentacorrientecliente ccc";
				$where = "ccc.egreso_id = {$egreso_id} ORDER BY ccc.cuentacorrientecliente_id DESC LIMIT 1";
				$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
				$cuentacorrientecliente_id = $cuentacorrientecliente_collection[0]['CCCID'];

				$cccm = new CuentaCorrienteCliente();
				$cccm->cuentacorrientecliente_id = $cuentacorrientecliente_id;
				$cccm->get();

				if ($egreso_pagoentrega_array[$egreso_id] == 4 OR $egreso_monto_parcial_array[$egreso_id] == $cccm->importe) {
					$select = "ccc.cuentacorrientecliente_id AS CCCID";
					$from = "cuentacorrientecliente ccc";
					$where = "ccc.egreso_id = {$egreso_id}";
					$cuentacorrientecliente_collection = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
					$cuentacorrientecliente_collection = (is_array($cuentacorrientecliente_collection) AND !empty($cuentacorrientecliente_collection)) ? $cuentacorrientecliente_collection : array();

					foreach ($cuentacorrientecliente_collection as $c=>$v) {
						$cccma = new CuentaCorrienteCliente();
						$cccma->cuentacorrientecliente_id = $v['CCCID'];
						$cccma->get();
						$cccma->estadomovimientocuenta = 4;
						$cccma->save();
					}

					$cccma = new CuentaCorrienteCliente();
					$cccma->fecha = date('Y-m-d');
					$cccma->hora = date('H:i:s');
					$cccma->referencia = "Pago " . $cccm->referencia;
					$cccma->importe = $cccm->importe;
					$cccma->ingreso = $cccm->importe;
					$cccma->cliente_id = $cccm->cliente_id;
					$cccma->egreso_id = $cccm->egreso_id;
					$cccma->tipomovimientocuenta = 2;
					$cccma->estadomovimientocuenta = 4;
					$cccma->cobrador = $cobrador_id;
					$cccma->save();
				} else {
					$cccma = new CuentaCorrienteCliente();
					$cccma->fecha = date('Y-m-d');
					$cccma->hora = date('H:i:s');
					$cccma->referencia = "Pago " . $cccm->referencia;
					$cccma->importe = $egreso_monto_parcial_array[$egreso_id];
					$cccma->ingreso = $egreso_monto_parcial_array[$egreso_id];
					$cccma->cliente_id = $cccm->cliente_id;
					$cccma->egreso_id = $cccm->egreso_id;
					$cccma->tipomovimientocuenta = 2;
					$cccma->estadomovimientocuenta = 3;
					$cccma->cobrador = $cobrador_id;
					$cccma->save();
				}
			}			
		}

		$egreso_ids = implode(',', $array_egreso_ids);
		$this->model->hojaruta_id = $hojaruta_id;
		$this->model->get();
		$this->model->egreso_ids = $egreso_ids;
		$this->model->estadoentrega = 7;
		$this->model->save();

		header("Location: " . URL_APP . "/hojaruta/panel");
	}

	function liberar_egreso($arg) {
		SessionHandler()->check_session();
		$ids = explode('@', $arg);
		$hojaruta_id = $ids[0];
		$egreso_id = $ids[1];

		$this->model->hojaruta_id = $hojaruta_id;
		$this->model->get();
		$egreso_ids = $this->model->egreso_ids;
		$array_tuplas = explode(',', $egreso_ids);
		
		foreach ($array_tuplas as $clave=>$valor) {
			$tmp_ids = explode('@', $valor);
			$tmp_egreso_id = $tmp_ids[0];
			if ($egreso_id == $tmp_egreso_id) {
				unset($array_tuplas[$clave]);
				$em = new Egreso();
				$em->egreso_id = $egreso_id;
				$em->get();
				$egresoentrega_id = $em->egresoentrega->egresoentrega_id;

				$eem = new EgresoEntrega();
				$eem->egresoentrega_id = $egresoentrega_id;
				$eem->get();
				$eem->estadoentrega = 2;
				$eem->save();
			}
		}

		$tuplas = implode(',', $array_tuplas);
		$this->model->hojaruta_id = $hojaruta_id;
		$this->model->get();
		$this->model->egreso_ids = $tuplas;
		$this->model->save();

		header("Location: " . URL_APP . "/hojaruta/panel");
	}
}
?>