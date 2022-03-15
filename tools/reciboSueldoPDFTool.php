<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class reciboSueldoPDFTool extends View {
	
    public function generarReciboSueldo($obj_salario, $salario_collection) { 
        $gui_html = file_get_contents("static/common/plantilla_recibosueldo.html");
        $gui_tbl_salario = file_get_contents("static/common/tbl_recibosueldo.html");
        $nombre_recibo = "Recibo de Sueldo: " . $obj_salario->empleado->apellido . " " . $obj_salario->empleado->nombre . ".pdf";
        
        $periodo_salario = "Desde " . $obj_salario->desde . " hasta " . $obj_salario->hasta;
        $detalle_salario = $obj_salario->detalle;
        $tipopago_salario = $obj_salario->tipo_pago;
        $importe_salario = $obj_salario->monto;
        
        $array_recibo = array();
        $monto_adelanto = 0;
        $array_temp = array('DETALLE'=>$periodo_salario, 'TIPOPAGO'=>$tipopago_salario, 'MONTO'=>'$' . $importe_salario);
        $array_recibo[] = $array_temp; 
        foreach ($salario_collection as $clave=>$valor) {
            $array_temp = array();
            $array_temp = array('DETALLE'=>$valor['DETALLE'], 'TIPOPAGO'=>$valor['TIPO'], 'MONTO'=>'-$' . $valor['IMPORTE']);
            $array_recibo[] = $array_temp;

            $monto_adelanto = $monto_adelanto + $valor['IMPORTE'];
        }

        $obj_salario->monto = round(($obj_salario->monto - $monto_adelanto), 2);
        $gui_tbl_salario = $this->render_regex_dict('TBL_RECIBOSUELDO', $gui_tbl_salario, $array_recibo);
        $obj_salario = $this->set_dict($obj_salario);
        $gui_html = str_replace('{tbl_recibosueldo}', $gui_tbl_salario, $gui_html);
        $gui_html = $this->render($obj_salario, $gui_html);

        $mipdf = new Dompdf();
        $mipdf->set_paper("A4", "landscape");
        $mipdf->load_html($gui_html);
        $mipdf->render(); 
        $mipdf->stream($nombre_recibo); 
    }
}
?>