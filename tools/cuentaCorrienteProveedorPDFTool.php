<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class CuentaCorrienteProveedorPDF extends View {
	
    public function descarga_cuentascorrientes($cuentacorriente_collection) { 
        $gui_html = file_get_contents("static/common/cuentacorrienteproveedor_pdf.html");
        $gui_tbl_cuentacorrienteproveedor = file_get_contents("static/common/tbl_cuentacorrienteproveedor_pdf.html");
        
        $cant_total = 0;
        foreach ($cuentacorriente_collection as $clave=>$valor) {
            $balance = abs($valor['BALANCE']);
            $cant_total = $cant_total + $balance;
            $cuentacorriente_collection[$clave]['BALANCE'] =  $balance;
        } 

        $nombre_PDF = "CuentasCorrientes-" . date('d-m-Y');
        $gui_tbl_cuentacorrienteproveedor = $this->render_regex_dict('TBL_CUENTACORRIENTEPROVEEDOR', $gui_tbl_cuentacorrienteproveedor, $cuentacorriente_collection);
        $gui_html = str_replace('{fecha_sys}', date('d/m/Y'), $gui_html);
        $gui_html = str_replace('{cant_total}', $cant_total, $gui_html);
        $gui_html = str_replace('{tbl_cuentacorrienteproveedor}', $gui_tbl_cuentacorrienteproveedor, $gui_html);
        
        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
    }
}
?>