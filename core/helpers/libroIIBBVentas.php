<?php
class LibroIIBBVentas {
	static function get_libro_iibb_ventas($desde, $hasta) {
	    $sql = "SELECT 
					LPAD(c.documento, 11, ' ') AS DOC,
				    RPAD('  0', 14, ' ') AS IIBB,
				    LPAD((left(c.razon_social, 30)), 30, ' ') AS CLIENTE,
				    LPAD((left(c.domicilio, 12)), 12, ' ') AS DOMICILIO,
				    date_format(e.fecha, '%d/%m/%Y') AS FECHA,
				    LPAD(e.importe_total, 13, ' ') AS IMPORTE_TOTAL,
				    LPAD((round((e.importe_total / 1.22), 2)), 13, ' ') AS BASE_IMPONIBLE,
				    LPAD('1.00', 6, ' ') AS ALICUOTA,
				    LPAD((round((1 * (e.importe_total / 1.22) / 100), 2)), 13, ' ') AS PERCEPCION,
				    LPAD(eafip.numero_factura, 8, 0) AS COMPROBANTE
				FROM 
					egreso e INNER JOIN cliente c ON e.cliente = c.cliente_id INNER JOIN				    
				    egresoafip eafip ON e.egreso_id = eafip.egreso_id
				WHERE 
					e.fecha BETWEEN ? AND ?";
	    $datos = array($desde, $hasta);
        $result = execute_query($sql, $datos);
        $result = (is_array($result) AND !empty($result)) ? $result : array();
		return $result;
	}
}

function LibroIIBBVentas() {return new LibroIIBBVentas();}
?>