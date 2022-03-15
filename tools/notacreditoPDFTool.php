<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class NotaCreditoPDF extends View {
	
    public function genera_notacredito($notascreditodetalle_collection, $obj_configuracion, $obj_egreso, $obj_notacredito) { 
        $gui_html = file_get_contents("static/common/plantillas_facturas/plantilla_html.html");
        $obj_cliente = $obj_egreso->cliente;
        $obj_vendedor = $obj_egreso->vendedor;
        unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection, $obj_cliente->flete,  
              $obj_cliente->vendedor->infocontacto_collection, $obj_egreso->egresocomision, $obj_egreso->egresoentrega);
        $egreso_id = $obj_egreso->egreso_id;
        $notacredito_id = $obj_notacredito->notacredito_id;
        $condicioniva = $obj_cliente->condicioniva->denominacion;
        $obj_cliente->condicioniva = $condicioniva;
        
        $obj_egreso->punto_venta = str_pad($obj_egreso->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_egreso->numero_factura = str_pad($obj_egreso->numero_factura, 8, '0', STR_PAD_LEFT);
        $obj_egreso = $this->set_dict($obj_egreso);

        $obj_notacredito->punto_venta = str_pad($obj_notacredito->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_notacredito->numero_factura = str_pad($obj_notacredito->numero_factura, 8, '0', STR_PAD_LEFT);
        $obj_notacredito = $this->set_dict($obj_notacredito);

        $obj_configuracion = $this->set_dict($obj_configuracion);
        $obj_cliente = $this->set_dict($obj_cliente);

        $notascreditodetalle_collection = (is_array($notascreditodetalle_collection)) ? $notascreditodetalle_collection : array();
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

        $nombre_PDF = "NotaCredito-{$notacredito_id}";
        $directorio = URL_PRIVATE . "facturas/notascreditos/";
        if(!file_exists($directorio)) {
                mkdir($directorio);
                chmod($directorio, 0777);
        }

        $gui_html = str_replace('{contenido}', $contenido, $gui_html);
        $output = $directorio . $nombre_PDF;
        $mipdf = new Dompdf();
        $mipdf ->set_paper("A4", "landscape");
        $mipdf ->load_html($gui_html);
        $mipdf->render(); 
        $pdfoutput = $mipdf->output(); 
        $filename = $output; 
        $fp = fopen($output, "a"); 
        fwrite($fp, $pdfoutput); 
        fclose($fp);
    }
}
?>