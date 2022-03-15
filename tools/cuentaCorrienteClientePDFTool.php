<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class CuentaCorrienteClientePDF extends View {
	
    public function descarga_cuentascorrientes($cuentacorriente_collection) { 
        $gui_html = file_get_contents("static/common/cuentacorrientecliente_pdf.html");
        $gui_tbl_cuentacorrientecliente = file_get_contents("static/common/tbl_cuentacorrientecliente_pdf.html");
        
        $cant_total = 0;
        foreach ($cuentacorriente_collection as $clave=>$valor) {
            $balance = abs($valor['BALANCE']);
            $cant_total = $cant_total + $balance;
            $cuentacorriente_collection[$clave]['BALANCE'] =  $balance;
        } 

        $nombre_PDF = "CuentasCorrientes-" . date('d-m-Y');
        $gui_tbl_cuentacorrientecliente = $this->render_regex_dict('TBL_CUENTACORRIENTECLIENTE', $gui_tbl_cuentacorrientecliente, $cuentacorriente_collection);
        $gui_html = str_replace('{fecha_sys}', date('d/m/Y'), $gui_html);
        $gui_html = str_replace('{cant_total}', $cant_total, $gui_html);
        $gui_html = str_replace('{tbl_cuentacorrientecliente}', $gui_tbl_cuentacorrientecliente, $gui_html);
        
        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
    }
}
?>