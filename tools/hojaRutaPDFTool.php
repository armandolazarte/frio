<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class hojaRutaPDF extends View {
	
    public function descarga_hoja_ruta($hojaruta_collection, $array_cantidades, $obj_flete) { 
        $gui_html = file_get_contents("static/common/hoja_ruta_flete_pdf.html");
        $gui_tbl_hoja_ruta_flete = file_get_contents("static/common/tbl_hoja_ruta_flete_pdf.html");
        $gui_tbl_hoja_ruta_flete = $this->render_regex_dict('TBL_HOJARUTAFLETE', $gui_tbl_hoja_ruta_flete, $hojaruta_collection);
        unset($obj_flete->infocontacto_collection);
        $flete = $obj_flete->denominacion;
        $obj_flete = $this->set_dict($obj_flete);
        $nombre_PDF = "HojaRuta-{$flete}";
        $gui_html = str_replace('{fecha_sys}', date('d/m/Y'), $gui_html);
        $gui_html = str_replace('{tbl_hojaruta}', $gui_tbl_hoja_ruta_flete, $gui_html);
        $gui_html = $this->render($obj_flete, $gui_html);
        $gui_html = $this->render($array_cantidades, $gui_html);
        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
    }
}
?>