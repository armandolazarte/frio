<?php
class LibroIvaCompras {
	static function get_libro_iva_compras($desde, $hasta) {
	    $sql = "SELECT
					date_format(i.fecha, '%d/%m/%Y') AS FECHA,
				    'FT' AS CLA,
				    CONCAT(tf.nomenclatura, ' ', LPAD(i.punto_venta, 5, 0), '-', LPAD(i.numero_factura, 8, 0)) AS COMPROBANTE,
				    p.razon_social AS PROVEEDOR,
				    p.documento AS CUIT,
				    i.costo_total AS NETO,
				    '0' AS EXENTO,
				    i.iva AS IVA,
				    '0' AS IVA10,
				    '0' AS IVA27,
				    i.impuesto_interno AS IMPINTERNO,
				    '0' AS RETIVA,
				    '0' AS RETIIBB,
				    i.percepcion_iva AS PERIVA,
				    i.ingresos_brutos AS PERIIBB,
				    '0' AS PERGANANCIA,
				    '0' AS CNOGRAVADO,
				    '0' AS IMPTEM,
				    '0' AS PERIIBBCF,
				    i.costo_total_iva AS TOTAL
				FROM
					ingreso i INNER JOIN 
				    proveedor p ON i.proveedor = p.proveedor_id INNER JOIN 
				    tipofactura tf ON i.tipofactura = tf.tipofactura_id
				WHERE	
					i.fecha BETWEEN ? AND ?
				ORDER BY
					i.fecha ASC";
	    $datos = array($desde, $hasta);
        $result = execute_query($sql, $datos);
        $result = (is_array($result) AND !empty($result)) ? $result : array();
		return $result;
	}
}

function LibroIvaCompras() {return new LibroIvaCompras();}
?>