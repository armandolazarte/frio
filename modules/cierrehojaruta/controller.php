<?php
require_once "modules/cierrehojaruta/model.php";
require_once "modules/cierrehojaruta/view.php";
require_once "modules/detallecierrehojaruta/model.php";
require_once "modules/hojaruta/model.php";
require_once "modules/cobrador/model.php";
require_once "modules/notacredito/model.php";


class CierreHojaRutaController {

	function __construct() {
		$this->model = new CierreHojaRuta();
		$this->view = new CierreHojaRutaView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$desde = date('Y-m');
    	$hasta = date('Y-m-d');
    	$select = "chr.cierrehojaruta_id AS CHRID, CONCAT(date_format(chr.fecha, '%d/%m/%Y'), ' ', chr.hora) AS FECHA, FORMAT(chr.rendicion, 2,'de_DE') AS RENDICION, chr.hojaruta_id AS HOJARUTA, c.denominacion AS FLETE";
    	$from = "cierrehojaruta chr INNER JOIN cobrador c ON chr.cobrador = c.cobrador_id";
    	$where = "chr.fecha BETWEEN '{$desde}-01' AND '{$hasta}' ORDER BY chr.cierrehojaruta_id DESC";
    	$cierrehojaruta_collection = CollectorCondition()->get('CierreHojaRuta', $where, 4, $from, $select);
    	
    	$cobrador_collection = Collector()->get('Cobrador');
    	foreach ($cobrador_collection as $clave=>$valor) {
    		if ($valor->flete_id == 0 OR $valor->oculto == 1) unset($cobrador_collection[$clave]);
    	}

    	$this->view->panel($cierrehojaruta_collection, $cobrador_collection);
	}

	function consultar($arg) {
    	SessionHandler()->check_session();
		$cierrehojaruta_id = $arg;
		$this->model->cierrehojaruta_id = $cierrehojaruta_id;
		$this->model->get();

		$select = "dchr.detallecierrehojaruta_id AS DCHRID, itp.denominacion AS TIPOPAGO, ee.denominacion AS ESTADOENTREGA, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, FORMAT(dchr.importe, 2,'de_DE') AS IMPORTE, e.egreso_id AS EGRID, e.importe_total AS EGRIMPTOT, dchr.tipoentrega AS TIPOENTREGA, c.razon_social AS CLIENTE";
    	$from = "detallecierrehojaruta dchr LEFT JOIN ingresotipopago itp ON dchr.ingresotipopago = itp.ingresotipopago_id LEFT JOIN estadoentrega ee ON dchr.estadoentrega = ee.estadoentrega_id LEFT JOIN egreso e ON dchr.egreso_id = e.egreso_id LEFT JOIN cliente c ON e.cliente = c.cliente_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
    	$where = "dchr.cierrehojaruta_id = {$cierrehojaruta_id}";
    	$detallecierrehojaruta_collection = CollectorCondition()->get('DetalleCierreHojaRuta', $where, 4, $from, $select);

        foreach ($detallecierrehojaruta_collection as $clave=>$valor) {
            $egreso_id = $valor['EGRID'];
            $importe_egreso = $valor['EGRIMPTOT'];
            $importe = $valor['IMPORTE'];
            $estadoentrega = $valor['ESTADOENTREGA'];

            $select = "nc.importe_total AS IMPORTE";
            $from = "notacredito nc";
            $where = "nc.egreso_id = {$egreso_id}";
            $importe_notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
            $importe_notacredito = (is_array($importe_notacredito) AND !empty($importe_notacredito)) ? $importe_notacredito[0]['IMPORTE'] : 0;
            if ($importe_notacredito > 0 AND $importe_notacredito >= $importe_egreso ) $detallecierrehojaruta_collection[$clave]["ESTADOENTREGA"] = "ANULADO";
            if ($importe_notacredito > 0 AND $importe_notacredito >= $importe_egreso ) $detallecierrehojaruta_collection[$clave]["TIPOPAGO"] = "ANULADO";
            if ($importe_notacredito > 0 AND $importe_notacredito >= $importe_egreso ) $detallecierrehojaruta_collection[$clave]["TIPOENTREGA"] = "ANULADO";

            if ($importe == 0 AND $estadoentrega == 'ENTREGADO') {
                $detallecierrehojaruta_collection[$clave]["TIPOPAGO"] = "FIRMA";
            }
        }

    	$this->view->consultar($detallecierrehojaruta_collection, $this->model);
	}

	function buscar() {
    	SessionHandler()->check_session();
    	$desde = filter_input(INPUT_POST, 'desde');
    	$hasta = filter_input(INPUT_POST, 'hasta');
    	$cobrador = filter_input(INPUT_POST, 'cobrador');

    	$select = "chr.cierrehojaruta_id AS CHRID, CONCAT(date_format(chr.fecha, '%d/%m/%Y'), ' ', chr.hora) AS FECHA, ROUND(chr.rendicion, 2) AS RENDICION, chr.hojaruta_id AS HOJARUTA, c.denominacion AS FLETE";
    	$from = "cierrehojaruta chr INNER JOIN cobrador c ON chr.cobrador = c.cobrador_id";
    	$where = "chr.fecha BETWEEN '{$desde}-01' AND '{$hasta}' AND chr.cobrador = {$cobrador} ORDER BY chr.cierrehojaruta_id DESC";
    	$cierrehojaruta_collection = CollectorCondition()->get('CierreHojaRuta', $where, 4, $from, $select);
    	$rendicion_total = 0;
    	foreach ($cierrehojaruta_collection as $clave=>$valor) {
    		$rendicion_total = $rendicion_total + $valor['RENDICION'];
    		$cierrehojaruta_collection[$clave]['RENDICION'] = number_format($cierrehojaruta_collection[$clave]['RENDICION'], 2, ',', '.');
    	}
    	
    	$cobrador_collection = Collector()->get('Cobrador');
    	foreach ($cobrador_collection as $clave=>$valor) {
    		if ($valor->flete_id == 0 OR $valor->oculto == 1) unset($cobrador_collection[$clave]);
    	}

    	$cm = new Cobrador();
    	$cm->cobrador_id = $cobrador;
    	$cm->get();
    	$cm->rendicion_total = number_format($rendicion_total, 2, ',', '.');

    	$this->view->buscar($cierrehojaruta_collection, $cobrador_collection, $cm);
	}

	function desc_cierrehojaruta($arg) {
    	SessionHandler()->check_session();
    	require_once "tools/excelreport.php";

		$cierrehojaruta_id = $arg;
		$this->model->cierrehojaruta_id = $cierrehojaruta_id;
		$this->model->get();
		$hojaruta_id = $this->model->hojaruta_id;

		$select = "dchr.detallecierrehojaruta_id AS DCHRID, itp.denominacion AS TIPOPAGO, ee.denominacion AS ESTADOENTREGA, CASE WHEN eafip.egresoafip_id IS NULL THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS FACTURA, FORMAT(dchr.importe, 2,'de_DE') AS IMPORTE, e.egreso_id AS EGRID, e.importe_total AS EGRIMPTOT, dchr.tipoentrega AS TIPOENTREGA, c.razon_social AS CLIENTE";
    	$from = "detallecierrehojaruta dchr LEFT JOIN ingresotipopago itp ON dchr.ingresotipopago = itp.ingresotipopago_id LEFT JOIN estadoentrega ee ON dchr.estadoentrega = ee.estadoentrega_id LEFT JOIN egreso e ON dchr.egreso_id = e.egreso_id LEFT JOIN cliente c ON e.cliente = c.cliente_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
    	$where = "dchr.cierrehojaruta_id = {$cierrehojaruta_id}";
    	$detallecierrehojaruta_collection = CollectorCondition()->get('DetalleCierreHojaRuta', $where, 4, $from, $select);

        foreach ($detallecierrehojaruta_collection as $clave=>$valor) {
            $egreso_id = $valor['EGRID'];
            $importe_egreso = $valor['EGRIMPTOT'];
            $importe = $valor['IMPORTE'];
            $estadoentrega = $valor['ESTADOENTREGA'];

            $select = "nc.importe_total AS IMPORTE";
            $from = "notacredito nc";
            $where = "nc.egreso_id = {$egreso_id}";
            $importe_notacredito = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
            $importe_notacredito = (is_array($importe_notacredito) AND !empty($importe_notacredito)) ? $importe_notacredito[0]['IMPORTE'] : 0;
            if ($importe_notacredito > 0 AND $importe_notacredito >= $importe_egreso ) $detallecierrehojaruta_collection[$clave]["ESTADOENTREGA"] = "ANULADO";
            if ($importe_notacredito > 0 AND $importe_notacredito >= $importe_egreso ) $detallecierrehojaruta_collection[$clave]["TIPOPAGO"] = "ANULADO";
            if ($importe_notacredito > 0 AND $importe_notacredito >= $importe_egreso ) $detallecierrehojaruta_collection[$clave]["TIPOENTREGA"] = "ANULADO";

            if ($importe == 0 AND $estadoentrega == 'ENTREGADO') {
                $detallecierrehojaruta_collection[$clave]["TIPOPAGO"] = "FIRMA";
            }
        }

        $subtitulo = "CIERRE HOJA DE RUTA N° {$hojaruta_id}";
		$array_encabezados = array('COMPROBANTE', 'CLIENTE', 'TIPO PAGO', 'TIPO ENTREGA', 'ESTADO ENTREGA', 'RENDICIÓN');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($datos_reporte as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["IMPORTE"];
			$array_temp = array();
			$array_temp = array($valor["FACTURA"]
								, $valor["CLIENTE"]
								, $valor["TIPOPAGO"]
								, $valor["TIPOENTREGA"]
								, $valor["ESTADOENTREGA"]
								, '$' . $valor["IMPORTE"]);
			$array_exportacion[] = $array_temp;
		}

		$array_exportacion[] = array('', '', '', '', '', '');
		$array_exportacion[] = array('', '', '', '', 'TOTAL', $sum_importe);
		ExcelReport()->extraer_informe_conjunto($subtitulo, $array_exportacion);
		exit;

    	
	}
}
?>