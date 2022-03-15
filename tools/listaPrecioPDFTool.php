<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class listaPrecioPDF extends View {
	
    public function descarga_lista_precio($listaprecio_collection, $obj_denominacion) { 
        $gui_html = file_get_contents("static/common/listaprecio_pdf.html");
        $gui_tbl_lista_precio = file_get_contents("static/common/tbl_lista_precio_pdf.html");
        $gui_tbl_lista_precio = $this->render_regex_dict('TBL_LISTAPRECIO', $gui_tbl_lista_precio, $listaprecio_collection);
        
        $obj_denominacion = strtoupper($obj_denominacion);
        $nombre_PDF = "ListaPrecio-{$obj_denominacion}";
        $gui_html = str_replace('{fecha_sys}', date('d/m/Y'), $gui_html);
        $gui_html = str_replace('{tbl_listaprecio}', $gui_tbl_lista_precio, $gui_html);
        $gui_html = str_replace('{objeto-denominacion}', $obj_denominacion, $gui_html);

        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
    }
}
?>