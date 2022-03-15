<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class pagoComisionPDF extends View {
	
    public function descarga_pago_comision($pagocomision_collection, $array_extra, $obj_vendedor) { 
        $gui_html = file_get_contents("static/common/pago_egresocomision_vendedor_pdf.html");
        $gui_tbl_pago_egresocomision_vendedor = file_get_contents("static/common/tbl_pago_egresocomision_vendedor_pdf.html");
        $gui_tbl_pago_egresocomision_vendedor = $this->render_regex_dict('TBL_PAGO_EGRESOCOMISION', $gui_tbl_pago_egresocomision_vendedor, $pagocomision_collection);
        unset($obj_vendedor->infocontacto_collection);
        $vendedor = $obj_vendedor->apellido . $obj_vendedor->nombre;
        $obj_vendedor = $this->set_dict($obj_vendedor);
        $nombre_PDF = "PagoComision-{$vendedor}";
        $gui_html = str_replace('{tbl_pago_egresocomision}', $gui_tbl_pago_egresocomision_vendedor, $gui_html);
        $gui_html = $this->render($obj_vendedor, $gui_html);
        $gui_html = $this->render($array_extra, $gui_html);
        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
    }
}
?>