<?php
require_once 'common/libs/domPDF/dompdf_config.inc.php';


class reciboSueldoPDFTool extends View {
	
    public function generarReciboSueldo($obj_salario) { 
        $gui_html = file_get_contents("static/common/plantilla_recibosueldo.html");
        $nombre_recibo = "Recibo de Sueldo: " . $obj_salario->empleado->apellido . " " . $obj_salario->empleado->nombre . ".pdf";
        $obj_salario = $this->set_dict($obj_salario);
        $gui_html = $this->render($obj_salario, $gui_html);
        
        $mipdf = new DOMPDF();
        $mipdf->set_paper("A4", "landscape");
        $mipdf->load_html($gui_html);
        $mipdf->render(); 
        $mipdf->stream($nombre_recibo); 
    }
}
?>