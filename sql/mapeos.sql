/* ****************************************************************************************** */
/* PARA MENÚ 
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS menu (
    menu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenu (
    submenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
    , menu INT(11)
    , INDEX(menu)
    , FOREIGN KEY (menu)
        REFERENCES menu (menu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS item (
    item_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , url VARCHAR(50)
    , detalle VARCHAR(100)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS configuracionmenu (
    configuracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , nivel INT(11)
    , gerencia INT(11)
    , INDEX (gerencia)
    , FOREIGN KEY (gerencia)
        REFERENCES gerencia (gerencia_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenuconfiguracionmenu (
    submenuconfiguracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES submenu (submenu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS itemconfiguracionmenu (
    itemconfiguracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES item (item_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS archivo (
    archivo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , url VARCHAR(100)
    , fecha_carga DATE
    , formato VARCHAR(50)
) ENGINE=InnoDb;

/* ****************************************************************************************** */
/* PARA USUARIO DESARROLLADOR
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS usuariodetalle (
    usuariodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(50)
    , nombre VARCHAR(50)
    , correoelectronico VARCHAR(250)
    , token TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS usuario (
    usuario_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , nivel INT(1)
    , usuariodetalle INT(11)
    , INDEX (usuariodetalle)
    , FOREIGN KEY (usuariodetalle)
        REFERENCES usuariodetalle (usuariodetalle_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

INSERT INTO usuariodetalle (usuariodetalle_id, apellido, nombre, correoelectronico, token) 
VALUES (1, 'Admin', 'admin', 'admin@admin.com', 'ff050c2a6dd7bc3e4602e9702de81d21');

INSERT INTO usuario (usuario_id, denominacion, nivel, usuariodetalle) 
VALUES (1, 'admin', 3, 1);

/* ****************************************************************************************** */
/* PARA OBJETOS DE CASOS DE USO 
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS configuracion (
    configuracion_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , razon_social TEXT
    , domicilio_comercial TEXT
    , cuit BIGINT(20)
    , ingresos_brutos VARCHAR(50)
    , fecha_inicio_actividad DATE
    , punto_venta INT(11)
    , condicioniva INT(11)
    , INDEX (condicioniva)
    , FOREIGN KEY (condicioniva)
        REFERENCES condicioniva (condicioniva_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS localidad (
    localidad_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS documentotipo (
    documentotipo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS provincia (
    provincia_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS condicioniva (
    condicioniva_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS proveedor (
    proveedor_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido TEXT
    , nombre TEXT
    , razon_social TEXT
    , documento BIGINT(20)
    , domicilio TEXT
    , codigopostal VARCHAR(50)
    , localidad TEXT
    , observacion TEXT
    , provincia INT(11)
    , INDEX (provincia)
    , FOREIGN KEY (provincia)
        REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
    , documentotipo INT(11)
    , INDEX (documentotipo)
    , FOREIGN KEY (documentotipo)
        REFERENCES documentotipo (documentotipo_id)
        ON DELETE CASCADE
    , condicioniva INT(11)
    , INDEX (condicioniva)
    , FOREIGN KEY (condicioniva)
        REFERENCES condicioniva (condicioniva_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontacto (
    infocontacto_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , valor TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontactoproveedor (
    infocontactoproveedor_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES proveedor (proveedor_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES infocontacto (infocontacto_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS productocategoria (
    productocategoria_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS productounidad (
    productounidad_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS productomarca (
    productomarca_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS producto (
    producto_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo BIGINT(20)
    , denominacion VARCHAR(150)
    , costo FLOAT
    , descuento FLOAT
    , porcentaje_ganancia FLOAT
    , iva FLOAT
    , exento INT(1)
    , no_gravado INT(1)
    , stock_minimo INT(11)
    , stock_ideal INT(11)
    , dias_reintegro INT(11)
    , detalle TEXT
    , productomarca INT(11)
    , INDEX (productomarca)
    , FOREIGN KEY (productomarca)
        REFERENCES productomarca (productomarca_id)
        ON DELETE CASCADE
    , productocategoria INT(11)
    , INDEX (productocategoria)
    , FOREIGN KEY (productocategoria)
        REFERENCES productocategoria (productocategoria_id)
        ON DELETE CASCADE
    , productounidad INT(11)
    , INDEX (productounidad)
    , FOREIGN KEY (productounidad)
        REFERENCES productounidad (productounidad_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS proveedorproducto (
    proveedorproducto_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES producto (producto_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES proveedor (proveedor_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS productodetalle (
    productodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , precio_costo FLOAT
    , producto_id INT(11)
    , proveedor_id INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS frecuenciaventa (
    zonaventa_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , dia_1 VARCHAR(50)
    , dia_2 VARCHAR(50)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS condicionfiscal (
    condicionfiscal_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS condicionpago (
    condicionpago_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cliente (
    cliente_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , razon_social TEXT
    , descuento FLOAT
    , iva FLOAT
    , documento BIGINT(20)
    , domicilio TEXT
    , codigopostal VARCHAR(50)
    , localidad TEXT
    , latitud TEXT
    , longitud TEXT
    , observacion TEXT
    , provincia INT(11)
    , INDEX (provincia)
    , FOREIGN KEY (provincia)
        REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
    , documentotipo INT(11)
    , INDEX (documentotipo)
    , FOREIGN KEY (documentotipo)
        REFERENCES documentotipo (documentotipo_id)
        ON DELETE CASCADE
    , condicioniva INT(11)
    , INDEX (condicioniva)
    , FOREIGN KEY (condicioniva)
        REFERENCES condicioniva (condicioniva_id)
        ON DELETE CASCADE
    , condicionfiscal INT(11)
    , INDEX (condicionfiscal)
    , FOREIGN KEY (condicionfiscal)
        REFERENCES condicionfiscal (condicionfiscal_id)
        ON DELETE CASCADE
    , frecuenciaventa INT(11)
    , INDEX (frecuenciaventa)
    , FOREIGN KEY (frecuenciaventa)
        REFERENCES frecuenciaventa (frecuenciaventa_id)
        ON DELETE CASCADE
    , vendedor INT(11)
    , INDEX (vendedor)
    , FOREIGN KEY (vendedor)
        REFERENCES vendedor (vendedor_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontactocliente (
    infocontactocliente_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES cliente (cliente_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES infocontacto (infocontacto_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS movimientotipo (
    movimientotipo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS ingreso (
    ingreso_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , punto_venta INT(4)
    , numero_factura INT(8)
    , fecha DATE
    , hora TIME
    , iva FLOAT
    , costo_distribucion FLOAT
    , costo_total FLOAT
    , costo_total_iva FLOAT
    , proveedor INT(11)
    , INDEX (proveedor)
    , FOREIGN KEY (proveedor)
        REFERENCES proveedor (proveedor_id)
        ON DELETE CASCADE
    , condicioniva INT(11)
    , INDEX (condicioniva)
    , FOREIGN KEY (condicioniva)
        REFERENCES condicioniva (condicioniva_id)
        ON DELETE CASCADE
    , condicionpago INT(11)
    , INDEX (condicionpago)
    , FOREIGN KEY (condicionpago)
        REFERENCES condicionpago (condicionpago_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS ingresodetalleestado (
    ingresodetalleestado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo VARCHAR(150)
    , denominacion VARCHAR(150)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS ingresodetalle (
    ingresodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo_producto VARCHAR(250)
    , descripcion_producto TEXT
    , cantidad FLOAT
    , descuento1 FLOAT
    , descuento2 FLOAT
    , descuento3 FLOAT
    , costo_producto FLOAT
    , importe FLOAT
    , producto_id INT(11)
    , ingreso_id INT(11)
    , ingresodetalleestado INT(11)
    , INDEX (ingresodetalleestado)
    , FOREIGN KEY (ingresodetalleestado)
        REFERENCES ingresodetalleestado (ingresodetalleestado_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS vendedor (
    vendedor_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(250)
    , nombre VARCHAR(250)
    , comision FLOAT
    , documento BIGINT(20)
    , domicilio TEXT
    , codigopostal VARCHAR(50)
    , localidad TEXT
    , latitud TEXT
    , longitud TEXT
    , observacion TEXT
    , provincia INT(11)
    , INDEX (provincia)
    , FOREIGN KEY (provincia)
        REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
    , documentotipo INT(11)
    , INDEX (documentotipo)
    , FOREIGN KEY (documentotipo)
        REFERENCES documentotipo (documentotipo_id)
        ON DELETE CASCADE
    , frecuenciaventa INT(11)
    , INDEX (frecuenciaventa)
    , FOREIGN KEY (frecuenciaventa)
        REFERENCES frecuenciaventa (frecuenciaventa_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontactovendedor (
    infocontactovendedor_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES vendedor (vendedor_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES infocontacto (infocontacto_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS stock (
    stock_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , hora TIME
    , concepto TEXT
    , codigo BIGINT(20)
    , cantidad_actual FLOAT
    , cantidad_movimiento FLOAT
    , producto_id INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS flete (
    flete_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , documento BIGINT(20)
    , domicilio TEXT
    , localidad TEXT
    , latitud TEXT
    , longitud TEXT
    , observacion TEXT
    , documentotipo INT(11)
    , INDEX (documentotipo)
    , FOREIGN KEY (documentotipo)
        REFERENCES documentotipo (documentotipo_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontactoflete (
    infocontactoflete_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES flete (flete_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES infocontacto (infocontacto_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS tipofactura (
    tipofactura_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , nomenclatura VARCHAR(150)
    , denominacion VARCHAR(150)
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS estadocomision (
    estadocomision_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS egresocomision (
    egresocomision_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , valor_abonado FLOAT
    , estadocomision INT(11)
    , INDEX (estadocomision)
    , FOREIGN KEY (estadocomision)
        REFERENCES estadocomision (estadocomision_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS estadoentrega (
    estadoentrega_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS egresoentrega (
    egresoentrega_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , flete INT(11)
    , INDEX (flete)
    , FOREIGN KEY (flete)
        REFERENCES flete (flete_id)
        ON DELETE CASCADE
    , estadoentrega INT(11)
    , INDEX (estadoentrega)
    , FOREIGN KEY (estadoentrega)
        REFERENCES estadoentrega (estadoentrega_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS egreso (
    egreso_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , punto_venta INT(4)
    , numero_factura INT(8)
    , fecha DATE
    , hora TIME
    , descuento FLOAT
    , subtotal FLOAT
    , importe_total FLOAT
    , emitido INT(11)
    , dias_alerta_comision INT(11)
    , dias_vencimiento INT(11)
    , cliente INT(11)
    , INDEX (cliente)
    , FOREIGN KEY (cliente)
        REFERENCES cliente (cliente_id)
        ON DELETE CASCADE
    , vendedor INT(11)
    , INDEX (vendedor)
    , FOREIGN KEY (vendedor)
        REFERENCES vendedor (vendedor_id)
        ON DELETE CASCADE
    , tipofactura INT(11)
    , INDEX (tipofactura)
    , FOREIGN KEY (tipofactura)
        REFERENCES tipofactura (tipofactura_id)
        ON DELETE CASCADE
    , condicioniva INT(11)
    , INDEX (condicioniva)
    , FOREIGN KEY (condicioniva)
        REFERENCES condicioniva (condicioniva_id)
        ON DELETE CASCADE
    , condicionpago INT(11)
    , INDEX (condicionpago)
    , FOREIGN KEY (condicionpago)
        REFERENCES condicionpago (condicionpago_id)
        ON DELETE CASCADE
    , egresocomision INT(11)
    , INDEX (egresocomision)
    , FOREIGN KEY (egresocomision)
        REFERENCES egresocomision (egresocomision_id)
        ON DELETE CASCADE
    , egresoentrega INT(11)
    , INDEX (egresoentrega)
    , FOREIGN KEY (egresoentrega)
        REFERENCES egresoentrega (egresoentrega_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS egresodetalleestado (
    egresodetalleestado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo VARCHAR(150)
    , denominacion VARCHAR(150)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS egresodetalle (
    egresodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo_producto VARCHAR(250)
    , descripcion_producto TEXT
    , cantidad FLOAT
    , descuento FLOAT
    , valor_descuento FLOAT
    , costo_producto FLOAT
    , iva FLOAT
    , importe FLOAT
    , producto_id INT(11)
    , egreso_id INT(11)
    , egresodetalleestado INT(11)
    , INDEX (egresodetalleestado)
    , FOREIGN KEY (egresodetalleestado)
        REFERENCES egresodetalleestado (egresodetalleestado_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS notacredito (
    notacredito_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , hora TIME
    , subtotal FLOAT
    , importe_total FLOAT
    , egreso_id INT(11)
    , tipofactura INT(11)
    , INDEX (tipofactura)
    , FOREIGN KEY (tipofactura)
        REFERENCES tipofactura (tipofactura_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS notacreditodetalle (
    notacreditodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo_producto VARCHAR(250)
    , descripcion_producto TEXT
    , cantidad FLOAT
    , descuento FLOAT
    , valor_descuento FLOAT
    , costo_producto FLOAT
    , iva FLOAT
    , importe FLOAT
    , producto_id INT(11)
    , egreso_id INT(11)
    , notacredito_id INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS tipomovimientocuenta (
    tipomovimientocuenta_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS estadomovimientocuenta (
    estadomovimientocuenta_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuentacorrientecliente (
    cuentacorrientecliente_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , hora TIME
    , referencia TEXT
    , importe FLOAT
    , ingreso FLOAT
    , cliente_id INT(11)
    , egreso_id INT(11)
    , tipomovimientocuenta INT(11)
    , INDEX (tipomovimientocuenta)
    , FOREIGN KEY (tipomovimientocuenta)
        REFERENCES tipomovimientocuenta (tipomovimientocuenta_id)
        ON DELETE CASCADE
    , estadomovimientocuenta INT(11)
    , INDEX (estadomovimientocuenta)
    , FOREIGN KEY (estadomovimientocuenta)
        REFERENCES estadomovimientocuenta (estadomovimientocuenta_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS egresoafip (
    egresoafip_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , cae TEXT
    , fecha DATE
    , vencimiento DATE
    , egreso_id INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuentacorrienteproveedor (
    cuentacorrienteproveedor_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , hora TIME
    , referencia TEXT
    , importe FLOAT
    , ingreso FLOAT
    , proveedor_id INT(11)
    , egreso_id INT(11)
    , tipomovimientocuenta INT(11)
    , INDEX (tipomovimientocuenta)
    , FOREIGN KEY (tipomovimientocuenta)
        REFERENCES tipomovimientocuenta (tipomovimientocuenta_id)
        ON DELETE CASCADE
    , estadomovimientocuenta INT(11)
    , INDEX (estadomovimientocuenta)
    , FOREIGN KEY (estadomovimientocuenta)
        REFERENCES estadomovimientocuenta (estadomovimientocuenta_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS hojaruta (
    hojaruta_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , flete_id INT(11)
    , egreso_ids VARCHAR(250)
    , estadoentrega INT(11)
    , INDEX (estadoentrega)
    , FOREIGN KEY (estadoentrega)
        REFERENCES estadoentrega (estadoentrega_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS gastocategoria (
    gastocategoria_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , codigo VARCHAR(10)
    , denominacion TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS gasto (
    gasto_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , importe FLOAT
    , detalle TEXT
    , gastocategoria INT(11)
    , INDEX (gastocategoria)
    , FOREIGN KEY (gastocategoria)
        REFERENCES gastocategoria (gastocategoria_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS configuracionbalance (
    configuracionbalance_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , activo_caja VARCHAR(10)
    , activo_stock_valorizado VARCHAR(10)
    , activo_cuenta_corriente_cliente VARCHAR(10)
    , pasivo_cuenta_corriente_proveedor VARCHAR(10)
    , pasivo_comisiones_pendientes VARCHAR(10)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS salario (
    salario_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , hora TIME
    , monto FLOAT
    , usuario_id INT(11)
    , empleado INT(11)
    , INDEX (empleado)
    , FOREIGN KEY (empleado)
        REFERENCES empleado (empleado_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS vendedorempleado (
    vendedorempleado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , vendedor_id INT(11)
    , INDEX (vendedor_id)
    , empleado_id INT(11)
    , INDEX (empleado_id)
) ENGINE=InnoDb;