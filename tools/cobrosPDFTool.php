<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class cobrosPDF extends View {

    public function descarga_cobros_vendedor($fecha,$total,$cobrador_id,$cobrador_denominacion,$cobros_array) {
        $gui_html = file_get_contents("static/common/cobros_vendedor_pdf.html");
        $gui_tbl_cobros_vendedor_pdf = file_get_contents("static/common/tbl_cobros_vendedor_pdf.html");
        $gui_tbl_cobros_vendedor_pdf = $this->render_regex_dict('TBL_COBRO_VENDEDOR', $gui_tbl_cobros_vendedor_pdf, $cobros_array);
        $gui_html = str_replace('{tbl_cobros}', $gui_tbl_cobros_vendedor_pdf, $gui_html);
        $gui_html = str_replace('{fecha_sys}', $fecha, $gui_html);
        $gui_html = str_replace('{cant_total}', $total, $gui_html);
        $gui_html = str_replace('{cobrador}', $cobrador_denominacion, $gui_html);
        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("cobros_vendedor-{$fecha}-{$cobrador_denominacion}.pdf");
    }
}
?>
