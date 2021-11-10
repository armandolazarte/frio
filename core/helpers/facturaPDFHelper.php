<?php
require_once 'common/libs/domPDF/dompdf_config.inc.php';


class FacturaPDF extends View {
	
        public function facturaA($egresodetalle_collection, $obj_configuracion, $obj_egreso) { 
                $gui_facturaA = file_get_contents("static/common/plantillas_facturas/facturaA.html");
                $gui_tbl_facturaA = file_get_contents("static/common/plantillas_facturas/tbl_facturaA.html");
                $gui_tbl_facturaA = $this->render_regex_dict('TBL_EGRESODETALLE', $gui_tbl_facturaA, $egresodetalle_collection);

                $obj_cliente = $obj_egreso->cliente;
                $obj_vendedor = $obj_egreso->vendedor;
                unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection, 
                      $obj_cliente->vendedor->infocontacto_collection);

                $egreso_id = $obj_egreso->egreso_id;
                $obj_cliente->condicioniva = $obj_cliente->condicioniva->denominacion;

                $obj_egreso = $this->set_dict($obj_egreso);
                $obj_configuracion = $this->set_dict($obj_configuracion);
                $obj_cliente = $this->set_dict($obj_cliente);
                
                $gui_facturaA = $this->render($obj_egreso, $gui_facturaA);
                $gui_facturaA = $this->render($obj_configuracion, $gui_facturaA);
                $gui_facturaA = $this->render($obj_cliente, $gui_facturaA);
                $gui_facturaA = str_replace('{tbl_egresodetalle}', $gui_tbl_facturaA, $gui_facturaA);

                $nombre_PDF = "Factura-{$egreso_id}";
                $directorio = URL_PRIVATE . "facturas/egresos/";
                if(!file_exists($directorio)) {
                        mkdir($directorio);
                        chmod($directorio, 0777);
                }

                $output = $directorio . $nombre_PDF;
                $mipdf = new DOMPDF();
                $mipdf ->set_paper("A4", "portrait");
                $mipdf ->load_html($gui_facturaA);
                $mipdf->render(); 
                $pdfoutput = $mipdf->output(); 
                $filename = $output; 
                $fp = fopen($output, "a"); 
                fwrite($fp, $pdfoutput); 
                fclose($fp);
        }

        public function facturaB($egresodetalle_collection, $obj_configuracion, $obj_egreso) { 
                $gui_facturaB = file_get_contents("static/common/plantillas_facturas/facturaB.html");
                $gui_tbl_facturaB = file_get_contents("static/common/plantillas_facturas/tbl_facturaB.html");
                $gui_tbl_facturaB = $this->render_regex_dict('TBL_EGRESODETALLE', $gui_tbl_facturaB, $egresodetalle_collection);

                $obj_cliente = $obj_egreso->cliente;
                $obj_vendedor = $obj_egreso->vendedor;
                unset($obj_egreso->cliente, $obj_egreso->vendedor, $obj_cliente->infocontacto_collection, 
                      $obj_cliente->vendedor->infocontacto_collection);

                $egreso_id = $obj_egreso->egreso_id;
                $obj_cliente->condicioniva = $obj_cliente->condicioniva->denominacion;

                $obj_egreso = $this->set_dict($obj_egreso);
                $obj_configuracion = $this->set_dict($obj_configuracion);
                $obj_cliente = $this->set_dict($obj_cliente);
                
                $gui_facturaB = $this->render($obj_egreso, $gui_facturaB);
                $gui_facturaB = $this->render($obj_configuracion, $gui_facturaB);
                $gui_facturaB = $this->render($obj_cliente, $gui_facturaB);
                $gui_facturaB = str_replace('{tbl_egresodetalle}', $gui_tbl_facturaB, $gui_facturaB);

                $nombre_PDF = "Factura-{$egreso_id}";
                $directorio = URL_PRIVATE . "facturas/egresos/";
                if(!file_exists($directorio)) {
                        mkdir($directorio);
                        chmod($directorio, 0777);
                }

                $output = $directorio . $nombre_PDF;
                $mipdf = new DOMPDF();
                $mipdf ->set_paper("A4", "portrait");
                $mipdf ->load_html($gui_facturaB);
                $mipdf->render(); 
                $pdfoutput = $mipdf->output(); 
                $filename = $output; 
                $fp = fopen($output, "a"); 
                fwrite($fp, $pdfoutput); 
                fclose($fp);
        }
}
?>