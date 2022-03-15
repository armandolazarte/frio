<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class visitaClientesVendedorPDF extends View {
	
    public function descarga_visita_clientes_vendedor($cliente_collection, $obj_vendedor) { 
        $gui_html = file_get_contents("static/common/descarga_visita_clientes_vendedor_pdf.html");
        $gui_tbl_visita_clientes_vendedor = file_get_contents("static/common/tbl_visita_clientes_vendedor_pdf.html");
        $gui_tbl_visita_clientes_vendedor = $this->render_regex_dict('TBL_VISITA_CLIENTE_VENDEDOR', $gui_tbl_visita_clientes_vendedor, $cliente_collection);
        
        unset($obj_vendedor->infocontacto_collection);
        $vendedor = $obj_vendedor->apellido . $obj_vendedor->nombre;
        $obj_vendedor = $this->set_dict($obj_vendedor);
        
        $nombre_PDF = "VisitaClientes-{$vendedor}";
        $gui_html = str_replace('{tbl_visita_clientes_vendedor}', $gui_tbl_visita_clientes_vendedor, $gui_html);
        $gui_html = $this->render($obj_vendedor, $gui_html);
        $gui_html = $this->render($array_extra, $gui_html);
        $gui_html = str_replace('{fecha_sys}', date('d/m/Y'), $gui_html);

        $dompdf = new Dompdf();
        $dompdf->set_paper("A4", "portrait");
        $dompdf->load_html($gui_html);
        $dompdf->render();
        $dompdf->stream("{$nombre_PDF}.pdf");
    }
}
?>