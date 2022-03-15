<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class FacturaPDF extends View {

    public function facturaA($egresodetalle_collection, $obj_configuracion, $obj_egreso, $vendedor, $flete) {
        $gui_html = file_get_contents("static/common/plantillas_facturas/plantilla_html.html");
        $obj_cliente = $obj_egreso->cliente;
        $obj_vendedor = $obj_egreso->vendedor;
        unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection,
              $obj_cliente->vendedor->infocontacto_collection, $obj_egreso->egresocomision, $obj_egreso->egresoentrega);

        $array_qr = array('fecha_venta'=>$obj_egreso->fecha,
                          'cuit'=>$obj_configuracion->cuit, 
                          'pto_venta'=>$obj_egreso->punto_venta, 
                          'tipofactura'=>$obj_egreso->tipofactura->afip_id, 
                          'numero_factura'=>$obj_egreso->numero_factura, 
                          'total'=>$obj_egreso->subtotal, 
                          'cliente_tipo_doc'=>$obj_cliente->documentotipo->afip_id, 
                          'cliente_nro_doc'=>$obj_cliente->documento, 
                          'cae'=>$obj_egreso->cae);

        $cod_qr = $this->qrAFIP($array_qr);
        $obj_egreso->cod_qr = $cod_qr;
        $obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
        $egreso_id = $obj_egreso->egreso_id;
        $obj_cliente->condicioniva = $obj_cliente->condicioniva->denominacion;

        $importe_total = 0;
        $suma_alicuota_iva = 0;
        $descuento_por_producto = 0;
        foreach ($egresodetalle_collection as $clave=>$valor) {
            $importe_total = $importe_total + $valor['IMPORTE'];
            $alicuota = (100 + $valor['IVA']) / 100;
            $subtotal = round($valor['IMPORTE'] / $alicuota, 2);
            $valor_alicuota_importe = round(($valor['IMPORTE'] - $subtotal),2);

            $valor_alicuota_costo = round($valor['COSTO'] / $alicuota, 2);
            $importe_neto = round(($valor['COSTO'] / $alicuota), 2);

            $egresodetalle_collection[$clave]['COSTO'] = "$" . $importe_neto;
            $egresodetalle_collection[$clave]['SUBTOTAL'] = "$" . $subtotal;
            $egresodetalle_collection[$clave]['IMPORTE'] = "$" . $valor['IMPORTE'];

            $egresodetalle_collection[$clave]["DESCUENTO"] = $valor["DESCUENTO"] . "%";
            $descuento_por_producto = $descuento_por_producto + $valor["VD"];
            $suma_alicuota_iva = $suma_alicuota_iva + $valor_alicuota_importe;
        }

        $descuento_por_factura = $obj_egreso->descuento * $importe_total / 100;
        $obj_egreso->valor_descuento_total = round(($descuento_por_producto + $descuento_por_factura),2);

        $obj_egreso->importe_iva = $suma_alicuota_iva;
        $obj_egreso->subtotal = round($obj_egreso->importe_total - $obj_egreso->importe_iva,2);
        $obj_egreso->subtotal = round($obj_egreso->subtotal - $obj_egreso->valor_descuento_total,2);

        $obj_egreso = $this->set_dict($obj_egreso);
        $obj_configuracion = $this->set_dict($obj_configuracion);
        $obj_cliente = $this->set_dict($obj_cliente);

        $new_array = array_chunk($egresodetalle_collection, 17);
        $contenido = '';
        $cantidad_hojas = count($new_array);
        $i = 1;
        foreach ($new_array as $egresodetalle_array) {
            $gui_facturaA = file_get_contents("static/common/plantillas_facturas/facturaA.html");
            $gui_tbl_facturaA = file_get_contents("static/common/plantillas_facturas/tbl_facturaA.html");
            $gui_tbl_facturaA = $this->render_regex_dict('TBL_EGRESODETALLE', $gui_tbl_facturaA, $egresodetalle_array);

            $gui_facturaA = $this->render($obj_egreso, $gui_facturaA);
            $gui_facturaA = $this->render($obj_configuracion, $gui_facturaA);
            $gui_facturaA = $this->render($obj_cliente, $gui_facturaA);
            $gui_facturaA = str_replace('{tbl_egresodetalle}', $gui_tbl_facturaA, $gui_facturaA);
            $gui_facturaA = str_replace('{cantidad_hojas}', $cantidad_hojas, $gui_facturaA);
            $gui_facturaA = str_replace('{numero_hoja}', $i, $gui_facturaA);
            $contenido .= $gui_facturaA;
            $i = $i + 1;
        }

        $contenido = str_replace('{flete}', $flete, $contenido);
        $contenido = str_replace('{vendedor}', $vendedor, $contenido);
        $nombre_PDF = "Factura-{$egreso_id}";
        $directorio = URL_PRIVATE . "facturas/egresos/";
        if(!file_exists($directorio)) {
                mkdir($directorio);
                chmod($directorio, 0777);
        }


        $gui_html = str_replace('{contenido}', $contenido, $gui_html);
        $output = $directorio . $nombre_PDF;
        $mipdf = new DOMPDF();
        $mipdf ->set_paper("A4", "landscape");
        $mipdf ->load_html($gui_html);
        $mipdf->render();
        $pdfoutput = $mipdf->output();
        $filename = $output;
        $fp = fopen($output, "a");
        fwrite($fp, $pdfoutput);
        fclose($fp);
    }

    public function facturaB($egresodetalle_collection, $obj_configuracion, $obj_egreso, $vendedor, $flete) {
        $gui_html = file_get_contents("static/common/plantillas_facturas/plantilla_html.html");

        $obj_cliente = $obj_egreso->cliente;
        $obj_vendedor = $obj_egreso->vendedor;
        unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection,
              $obj_cliente->vendedor->infocontacto_collection, $obj_egreso->egresocomision, $obj_egreso->egresoentrega);

        $array_qr = array('fecha_venta'=>$obj_egreso->fecha,
                          'cuit'=>$obj_configuracion->cuit, 
                          'pto_venta'=>$obj_egreso->punto_venta, 
                          'tipofactura'=>$obj_egreso->tipofactura->afip_id, 
                          'numero_factura'=>$obj_egreso->numero_factura, 
                          'total'=>$obj_egreso->subtotal, 
                          'cliente_tipo_doc'=>$obj_cliente->documentotipo->afip_id, 
                          'cliente_nro_doc'=>$obj_cliente->documento, 
                          'cae'=>$obj_egreso->cae);

        $cod_qr = $this->qrAFIP($array_qr);
        $obj_egreso->cod_qr = $cod_qr;

        $condicionfiscal_id = $obj_cliente->condicionfiscal->condicionfiscal_id;
        $condicioniva_id = $obj_cliente->condicioniva->condicioniva_id;

        $obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
        $egreso_id = $obj_egreso->egreso_id;
        $obj_cliente->condicioniva = $obj_cliente->condicioniva->denominacion;

        $importe_total = 0;
        $suma_alicuota_iva = 0;
        $descuento_por_producto = 0;
        foreach ($egresodetalle_collection as $clave=>$valor) {
            $importe_total = $importe_total + $valor['IMPORTE'];
            $alicuota = (100 + $valor['IVA']) / 100;
            $subtotal = round($valor['IMPORTE'] / $alicuota, 2);
            $valor_alicuota_importe = round(($valor['IMPORTE'] - $subtotal),2);

            $valor_alicuota_costo = round($valor['COSTO'] / $alicuota, 2);
            $iva = round(($valor['IVA'] * $valor['NETPRO'] / 100), 2);
            $importe_neto = round(($valor['NETPRO'] + $iva), 2);

            $egresodetalle_collection[$clave]['SUBTOTAL'] = "$" . $subtotal;
            $egresodetalle_collection[$clave]['IMPORTE'] = "$" . $valor['IMPORTE'];

            $egresodetalle_collection[$clave]["DESCUENTO"] = $valor["DESCUENTO"] . "%";
            $descuento_por_producto = $descuento_por_producto + $valor["VD"];
            $suma_alicuota_iva = $suma_alicuota_iva + $valor_alicuota_importe;
        }

        $descuento_por_factura = $obj_egreso->descuento * $importe_total / 100;
        $obj_egreso->valor_descuento_total = round(($descuento_por_producto + $descuento_por_factura),2);

        $obj_egreso->importe_iva = $suma_alicuota_iva;
        $obj_egreso->subtotal = round($obj_egreso->importe_total - $obj_egreso->importe_iva,2);
        $obj_egreso->subtotal = round($obj_egreso->subtotal - $obj_egreso->valor_descuento_total,2);

        $obj_egreso = $this->set_dict($obj_egreso);
        $obj_configuracion = $this->set_dict($obj_configuracion);
        $obj_cliente = $this->set_dict($obj_cliente);
        $new_array = array_chunk($egresodetalle_collection, 16);
        $contenido = '';
        $cantidad_hojas = count($new_array);
        $i = 1;
        foreach ($new_array as $egresodetalle_array) {
            $gui_facturaB = file_get_contents("static/common/plantillas_facturas/facturaB.html");
            $gui_tbl_facturaB = file_get_contents("static/common/plantillas_facturas/tbl_facturaB.html");
            $gui_tbl_facturaB = $this->render_regex_dict('TBL_EGRESODETALLE', $gui_tbl_facturaB, $egresodetalle_array);

            $gui_facturaB = $this->render($obj_egreso, $gui_facturaB);
            $gui_facturaB = $this->render($obj_configuracion, $gui_facturaB);
            $gui_facturaB = $this->render($obj_cliente, $gui_facturaB);
            $gui_facturaB = str_replace('{tbl_egresodetalle}', $gui_tbl_facturaB, $gui_facturaB);
            $gui_facturaB = str_replace('{cantidad_hojas}', $cantidad_hojas, $gui_facturaB);
            $gui_facturaB = str_replace('{numero_hoja}', $i, $gui_facturaB);
            $contenido .= $gui_facturaB;
            $i = $i + 1;
        }

        $contenido = str_replace('{flete}', $flete, $contenido);
        $contenido = str_replace('{vendedor}', $vendedor, $contenido);
        $nombre_PDF = "Factura-{$egreso_id}";
        $directorio = URL_PRIVATE . "facturas/egresos/";
        if(!file_exists($directorio)) {
                mkdir($directorio);
                chmod($directorio, 0777);
        }

        $gui_html = str_replace('{contenido}', $contenido, $gui_html);
        $output = $directorio . $nombre_PDF;
        $mipdf = new DOMPDF();
        $mipdf ->set_paper("A4", "landscape");
        $mipdf ->load_html($gui_html);
        $mipdf->render();
        $pdfoutput = $mipdf->output();
        $filename = $output;
        $fp = fopen($output, "a");
        fwrite($fp, $pdfoutput);
        fclose($fp);
    }

    public function remitoR($egresodetalle_collection, $obj_configuracion, $obj_egreso, $vendedor, $flete) {
        $gui_html = file_get_contents("static/common/plantillas_facturas/plantilla_html.html");

        $obj_cliente = $obj_egreso->cliente;
        $obj_vendedor = $obj_egreso->vendedor;
        unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection,
              $obj_cliente->vendedor->infocontacto_collection, $obj_egreso->egresocomision, $obj_egreso->egresoentrega);

        $obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
        $egreso_id = $obj_egreso->egreso_id;
        $obj_cliente->condicioniva = $obj_cliente->condicioniva->denominacion;

        $importe_total = 0;
        $descuento_por_producto = 0;
        foreach ($egresodetalle_collection as $clave=>$valor) {
            $importe_total = $importe_total + $valor['IMPORTE'];
            $descuento_por_producto = $descuento_por_producto + $valor["VD"];
        }

        $descuento_por_factura = $obj_egreso->descuento * $importe_total / 100;
        $obj_egreso->valor_descuento_total = round(($descuento_por_producto + $descuento_por_factura),2);
        $obj_egreso->subtotal = round($obj_egreso->subtotal - $obj_egreso->valor_descuento_total,2);

        $obj_egreso = $this->set_dict($obj_egreso);
        $obj_configuracion = $this->set_dict($obj_configuracion);
        $obj_cliente = $this->set_dict($obj_cliente);
        $new_array = array_chunk($egresodetalle_collection, 24);
        $contenido = '';
        $cantidad_hojas = count($new_array);
        $i = 1;
        foreach ($new_array as $egresodetalle_array) {
            $gui_remitoR = file_get_contents("static/common/plantillas_facturas/remitoR.html");
            $gui_tbl_remitoR = file_get_contents("static/common/plantillas_facturas/tbl_remitoR.html");
            $gui_tbl_remitoR = $this->render_regex_dict('TBL_EGRESODETALLE', $gui_tbl_remitoR, $egresodetalle_array);

            $gui_remitoR = $this->render($obj_configuracion, $gui_remitoR);
            $gui_remitoR = $this->render($obj_egreso, $gui_remitoR);
            $gui_remitoR = $this->render($obj_cliente, $gui_remitoR);
            $gui_remitoR = str_replace('{tbl_egresodetalle}', $gui_tbl_remitoR, $gui_remitoR);
            $gui_remitoR = str_replace('{cantidad_hojas}', $cantidad_hojas, $gui_remitoR);
            $gui_remitoR = str_replace('{numero_hoja}', $i, $gui_remitoR);
            $contenido .= $gui_remitoR;
            $i = $i + 1;
        }

        $contenido = str_replace('{flete}', $flete, $contenido);
        $contenido = str_replace('{vendedor}', $vendedor, $contenido);
        $nombre_PDF = "Factura-{$egreso_id}";
        $directorio = URL_PRIVATE . "facturas/egresos/";
        if(!file_exists($directorio)) {
                mkdir($directorio);
                chmod($directorio, 0777);
        }

        $gui_html = str_replace('{contenido}', $contenido, $gui_html);
        $output = $directorio . $nombre_PDF;
        $mipdf = new DOMPDF();
        $mipdf ->set_paper("A4", "landscape");
        $mipdf ->load_html($gui_html);
        $mipdf->render();
        $pdfoutput = $mipdf->output();
        $filename = $output;
        $fp = fopen($output, "a");
        fwrite($fp, $pdfoutput);
        fclose($fp);
    }

    public function presupuestoP($presupuestodetalle_collection, $obj_configuracion, $obj_presupuesto, $vendedor) {
        $gui_html = file_get_contents("static/common/plantillas_facturas/plantilla_html.html");
        $obj_cliente = $obj_presupuesto->cliente;
        $obj_vendedor = $obj_presupuesto->vendedor;
        unset($obj_presupuesto->cliente, $obj_presupuesto->vendedor, $obj_cliente->infocontacto_collection,
              $obj_cliente->vendedor->infocontacto_collection);

        $obj_presupuesto->punto_venta = str_pad($obj_presupuesto->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_presupuesto->numero_factura = str_pad($obj_presupuesto->numero_factura, 8, '0', STR_PAD_LEFT);
        $presupuesto_id = $obj_presupuesto->presupuesto_id;

        $importe_total = 0;
        $descuento_por_producto = 0;
        foreach ($presupuestodetalle_collection as $clave=>$valor) {
            $importe_total = $importe_total + $valor['IMPORTE'];
            $descuento_por_producto = $descuento_por_producto + $valor["VD"];
        }

        $descuento_por_factura = $obj_presupuesto->descuento * $importe_total / 100;
        $obj_presupuesto->valor_descuento_total = round(($descuento_por_producto + $descuento_por_factura),2);
        $obj_presupuesto->subtotal = round($obj_presupuesto->subtotal - $obj_presupuesto->valor_descuento_total,2);

        $obj_presupuesto = $this->set_dict($obj_presupuesto);
        $obj_configuracion = $this->set_dict($obj_configuracion);
        $obj_cliente = $this->set_dict($obj_cliente);
        $new_array = array_chunk($presupuestodetalle_collection, 14);
        $contenido = '';
        foreach ($new_array as $presupuestodetalle_array) {
            $gui_presupuestoP = file_get_contents("static/common/plantillas_facturas/presupuestoP.html");
            $gui_tbl_presupuestoP = file_get_contents("static/common/plantillas_facturas/tbl_presupuestoP.html");
            $gui_tbl_presupuestoP = $this->render_regex_dict('TBL_PRESUPUESTODETALLE', $gui_tbl_presupuestoP, $presupuestodetalle_array);

            $gui_presupuestoP = $this->render($obj_configuracion, $gui_presupuestoP);
            $gui_presupuestoP = $this->render($obj_presupuesto, $gui_presupuestoP);
            $gui_presupuestoP = $this->render($obj_cliente, $gui_presupuestoP);
            $gui_presupuestoP = str_replace('{tbl_presupuestodetalle}', $gui_tbl_presupuestoP, $gui_presupuestoP);

            $contenido .= $gui_presupuestoP;
        }

        $contenido = str_replace('{vendedor}', $vendedor, $contenido);
        $nombre_PDF = "Presupuesto-{$presupuesto_id}";
        $directorio = URL_PRIVATE . "facturas/presupuestos/";
        if(!file_exists($directorio)) {
                mkdir($directorio);
                chmod($directorio, 0777);
        }

        $gui_html = str_replace('{contenido}', $contenido, $gui_html);

        $output = $directorio . $nombre_PDF;
        $mipdf = new DOMPDF();
        $mipdf ->set_paper("A4", "portrait");
        $mipdf ->load_html($gui_html);
        $mipdf->render();
        $pdfoutput = $mipdf->output();
        $filename = $output;
        $fp = fopen($output, "a");
        fwrite($fp, $pdfoutput);
        fclose($fp);
    }

    public function descarga_notacredito($notascreditodetalle_collection, $obj_configuracion, $obj_egreso, $obj_notacredito) {
        $gui_html = file_get_contents("static/common/plantillas_facturas/plantilla_html.html");

        $obj_cliente = $obj_egreso->cliente;
        $obj_vendedor = $obj_egreso->vendedor;
        unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection,
              $obj_cliente->vendedor->infocontacto_collection, $obj_egreso->egresocomision, $obj_egreso->egresoentrega);

        $egreso_id = $obj_egreso->egreso_id;
        $obj_cliente->condicioniva = $obj_cliente->condicioniva->denominacion;

        $obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
        $obj_egreso = $this->set_dict($obj_egreso);
        $obj_notacredito = $this->set_dict($obj_notacredito);
        $obj_configuracion = $this->set_dict($obj_configuracion);
        $obj_cliente = $this->set_dict($obj_cliente);

        $new_array = array_chunk($notascreditodetalle_collection, 14);
        $contenido = '';
        foreach ($new_array as $notascreditodetalle_array) {
            $gui_notacreditoNC = file_get_contents("static/common/plantillas_facturas/notacreditoNC.html");
            $gui_tbl_notacreditoNC = file_get_contents("static/common/plantillas_facturas/tbl_notacreditoNC.html");
            $gui_tbl_notacreditoNC = $this->render_regex_dict('TBL_NOTACREDITODETALLE', $gui_tbl_notacreditoNC, $notascreditodetalle_collection);

            $gui_notacreditoNC = $this->render($obj_egreso, $gui_notacreditoNC);
            $gui_notacreditoNC = $this->render($obj_configuracion, $gui_notacreditoNC);
            $gui_notacreditoNC = $this->render($obj_cliente, $gui_notacreditoNC);
            $gui_notacreditoNC = $this->render($obj_notacredito, $gui_notacreditoNC);
            $gui_notacreditoNC = str_replace('{tbl_notacreditodetalle}', $gui_tbl_notacreditoNC, $gui_notacreditoNC);
            $contenido .= $gui_notacreditoNC;
        }

        $gui_html = str_replace('{contenido}', $contenido, $gui_html);
        $dompdf = new DOMPDF();
        $dompdf->set_paper("A4", "landscape");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("NotaCredito.pdf");
        exit;
    }

    public function descarga_hoja_ruta($hojaruta_collection, $obj_flete) {
        $gui_html = file_get_contents("static/common/hoja_ruta_flete_pdf.html");
        $gui_tbl_hoja_ruta_flete = file_get_contents("static/common/tbl_hoja_ruta_flete_pdf.html");

        $obj_flete = $this->set_dict($obj_flete);
        $gui_tbl_hoja_ruta_flete = $this->render_regex_dict('TBL_HOJARUTAFLETE', $gui_tbl_hoja_ruta_flete, $hojaruta_collection);

        $flete = $obj_flete->denominacion;
        $nombre_PDF = "HojaRuta-{$flete}";
        $gui_html = str_replace('{tbl_hoja_ruta_flete_pdf}', $gui_tbl_hoja_ruta_flete_pdf, $gui_html);
        $dompdf = new DOMPDF();
        $dompdf->set_paper("A4", "landscape");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
        exit;
    }

    function qrAFIP($array_qr) {
        require_once 'vendor/autoload.php';
        $datos_cmp_base_64 = json_encode([
            "ver" => 1,
            "fecha" => substr($array_qr['fecha_venta'], 0, 10),
            "cuit" => (int) $array_qr['cuit'],
            "ptoVta" => (int) $array_qr['pto_venta'],
            "tipoCmp" => (int) $array_qr['tipofactura'],
            "nroCmp" => (int) $array_qr['numero_factura'],
            "importe" => (float) $array_qr['total'],
            "moneda" => "PES",
            "ctz" => (float) 1,
            "tipoDocRec" => (int) $array_qr['cliente_tipo_doc'],
            "nroDocRec" => (int) $array_qr['cliente_nro_doc'],
            "tipoCodAut" => "E",
            "codAut" => (int) $array_qr['cae']
        ]);
        
        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
        $url = 'https://www.afip.gob.ar/fe/qr/';
        $to_qr = $url.'?p='.$datos_cmp_base_64;

        $barcode = new \Com\Tecnick\Barcode\Barcode();
        $bobj = $barcode->getBarcodeObj(
                'QRCODE,H',
                $to_qr,
                -4,
                -4,
                'black',
                array(-2, -2, -2, -2)
            )->setBackgroundColor('white');
        $qr_div = base64_encode($bobj->getPngData());
        return $qr_div;
    }
}
?>