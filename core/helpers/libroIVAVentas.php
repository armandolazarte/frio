<?php
class LibroIvaVentas {
	static function get_libro_iva_ventas($desde, $hasta) {
	    $sql = "(SELECT 
					e.egreso_id AS ID,
					e.fecha AS FECHA,
				    CONCAT(tf.afip_id, ' - Factura ', tf.nomenclatura) AS TIPOFACTURA,
				    eafip.punto_venta AS PTO_VENTA,
				    eafip.numero_factura AS NRO_DESDE,
				    c.documento AS DOC_RECEPTOR,
				    c.razon_social AS RECEPTOR,
				    ROUND((SELECT 
							ROUND((SUM(ed.importe) / (ed.iva / 100 + 1)),2)
					 	   FROM 
							egresodetalle ed INNER JOIN
							producto pa ON ed.producto_id = pa.producto_id
					 	   WHERE 
							ed.egreso_id = e.egreso_id AND
							pa.exento != 1 AND
							pa.no_gravado != 1),2) AS IMP_NETO_GRAVADO,
					(SELECT 
						CASE
				    		WHEN ROUND(SUM(ed.importe),2) > 0 THEN ROUND(SUM(ed.importe),2)
				    		ELSE 0
				    	END
					 FROM 
						egresodetalle ed INNER JOIN
						producto pb ON ed.producto_id = pb.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pb.no_gravado = 1) AS IMP_NETO_NO_GRAVADO,
					(SELECT 
						CASE
				    		WHEN ROUND(SUM(ed.importe),2) > 0 THEN ROUND(SUM(ed.importe),2)
				    		ELSE 0
				    	END
					 FROM 
						egresodetalle ed INNER JOIN
						producto pb ON ed.producto_id = pb.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pb.exento = 1) AS IMP_OP_EXENTAS,
					((SELECT 
						ROUND(SUM(ed.importe),2)
					 FROM 
						egresodetalle ed INNER JOIN
						producto pd ON ed.producto_id = pd.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pd.exento != 1 AND
						pd.no_gravado != 1) - 
				    ROUND((SELECT 
						ROUND((SUM(ed.importe) / (ed.iva / 100 + 1)),2)
					 FROM 
						egresodetalle ed INNER JOIN
						producto pe ON ed.producto_id = pe.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pe.exento != 1 AND
						pe.no_gravado != 1),2)) AS IVA,

					((SELECT 
						IF(ROUND(SUM(ed.importe),2) > 0, ROUND(SUM(ed.importe),2), 0)
					 FROM 
						egresodetalle ed INNER JOIN
						producto pd ON ed.producto_id = pd.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pd.exento != 1 AND
						pd.no_gravado != 1 AND
						ed.iva = 21) - 
				    ROUND((SELECT 
						ROUND(
							(
								SUM(CASE WHEN ed.iva = 21 THEN ed.importe ELSE 0 END) / (ed.iva / 100 + 1)
							)
						,2)
					 FROM 
						egresodetalle ed INNER JOIN
						producto pe ON ed.producto_id = pe.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pe.exento != 1 AND
						pe.no_gravado != 1),2)) AS IVA21,

					((SELECT 
						IF(ROUND(SUM(ed.importe),2) > 0, ROUND(SUM(ed.importe),2), 0)
					 FROM 
						egresodetalle ed INNER JOIN
						producto pd ON ed.producto_id = pd.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pd.exento != 1 AND
						pd.no_gravado != 1 AND
						ed.iva = 10.5) - 
				    ROUND((SELECT 
						ROUND(
							(
								SUM(CASE WHEN ed.iva = 10.5 THEN ed.importe ELSE 0 END) / (ed.iva / 100 + 1)
							)
						,2)
					 FROM 
						egresodetalle ed INNER JOIN
						producto pe ON ed.producto_id = pe.producto_id
					 WHERE 
						ed.egreso_id = e.egreso_id AND
						pe.exento != 1 AND
						pe.no_gravado != 1),2)) AS IVA10,					

				    (SELECT 
						ROUND(SUM(ed.importe),2)
					 FROM 
						egresodetalle ed
					 WHERE 
						ed.egreso_id = e.egreso_id) AS IMP_TOTAL
				FROM 
					egreso e INNER JOIN
				    egresoafip eafip ON e.egreso_id = eafip.egreso_id INNER JOIN
				    tipofactura tf ON eafip.tipofactura = tf.tipofactura_id INNER JOIN
				    cliente c ON e.cliente = c.cliente_id
				WHERE 
					e.fecha BETWEEN ? AND ?
				ORDER BY
					e.fecha ASC)
			UNION
				(SELECT 
					nc.notacredito_id AS ID,
					nc.fecha AS FECHA,
				    CONCAT(tf.afip_id, ' - Nota Crédito ', tf.nomenclatura) AS TIPOFACTURA,
				    nc.punto_venta AS PTO_VENTA,
				    nc.numero_factura AS NRO_DESDE,
				    c.documento AS DOC_RECEPTOR,
				    c.razon_social AS RECEPTOR,
				    ROUND((SELECT 
						ROUND((SUM(ncd.importe) / (ncd.iva / 100 + 1)),2)
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.exento != 1 AND
						p.no_gravado != 1),2) AS IMP_NETO_GRAVADO,
					(SELECT 
						CASE
				    		WHEN ROUND(SUM(ncd.importe),2) > 0 THEN ROUND(SUM(ncd.importe),2)
				    		ELSE 0
				    	END
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.no_gravado = 1) AS IMP_NETO_NO_GRAVADO,
					(SELECT 
						CASE
				    		WHEN ROUND(SUM(ncd.importe),2) > 0 THEN ROUND(SUM(ncd.importe),2)
				    		ELSE 0
				    	END
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.exento = 1) AS IMP_OP_EXENTAS,
					((SELECT 
						ROUND(SUM(ncd.importe),2)
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.exento != 1 AND
						p.no_gravado != 1) - 
				    ROUND((SELECT 
						ROUND((SUM(ncd.importe) / (ncd.iva / 100 + 1)),2)
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.exento != 1 AND
						p.no_gravado != 1),2)) AS IVA,

					((SELECT 
						IF(ROUND(SUM(ncd.importe),2) > 0, ROUND(SUM(ncd.importe),2), 0)
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.exento != 1 AND
						p.no_gravado != 1 AND
						ncd.iva = 21) - 
				    ROUND((SELECT 
						ROUND(
							(
								SUM(CASE WHEN ncd.iva = 21 THEN ncd.importe ELSE 0 END) / (ncd.iva / 100 + 1)
							)
						,2)
					 FROM 
						notacreditodetalle ncd INNER JOIN
						producto p ON ncd.producto_id = p.producto_id
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id AND
						p.exento != 1 AND
						p.no_gravado != 1),2)) AS IVA21,	

					(
						(SELECT 
							IF(ROUND(SUM(ncd.importe),2) > 0, ROUND(SUM(ncd.importe),2), 0)
						 FROM 
							notacreditodetalle ncd INNER JOIN
							producto p ON ncd.producto_id = p.producto_id
						 WHERE 
							ncd.notacredito_id = nc.notacredito_id AND
							p.exento != 1 AND
							p.no_gravado != 1 AND
							ncd.iva = 10.5) - 
					    ROUND(
					    	(SELECT 
								ROUND(
									(
										SUM(CASE WHEN ncd.iva = 10.5 THEN ncd.importe ELSE 0 END) / (ncd.iva / 100 + 1)
									)
								,2)
						 	FROM 
								notacreditodetalle ncd INNER JOIN
								producto p ON ncd.producto_id = p.producto_id
						 	WHERE 
								ncd.notacredito_id = nc.notacredito_id AND
								p.exento != 1 AND
								p.no_gravado != 1)
							,2)
					) AS IVA10,

				    (SELECT 
						ROUND(SUM(ncd.importe),2)
					 FROM 
						notacreditodetalle ncd
					 WHERE 
						ncd.notacredito_id = nc.notacredito_id) AS IMP_TOTAL
				FROM 
					notacredito nc INNER JOIN
				    egreso e ON nc.egreso_id = e.egreso_id INNER JOIN
				    tipofactura tf ON nc.tipofactura = tf.tipofactura_id INNER JOIN
				    cliente c ON e.cliente = c.cliente_id
				WHERE 
					nc.emitido_afip = 1 AND
					nc.fecha BETWEEN ? AND ?
				ORDER BY
					nc.fecha ASC)";
	    $datos = array($desde, $hasta, $desde, $hasta);
        $result = execute_query($sql, $datos);
        $result = (is_array($result) AND !empty($result)) ? $result : array();
		return $result;
	}
}

function LibroIvaVentas() {return new LibroIvaVentas();}
?>