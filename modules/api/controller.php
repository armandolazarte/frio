<?php

require_once "core/validator/Util.php";
require_once "core/validator/JwtProtocolo.php";
require_once 'core/helpers/user.php';
require_once 'modules/usuario/model.php';
require_once 'modules/vendedor/model.php';
require_once 'modules/usuariovendedor/model.php';
require_once 'modules/cliente/model.php';
require_once 'modules/egreso/model.php';
require_once 'modules/egresodetalle/model.php';
require_once 'modules/notacredito/model.php';
require_once 'modules/notacreditodetalle/model.php';
require_once 'modules/configuracionmenu/model.php';
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/condicioniva/model.php";
require_once "modules/condicionfiscal/model.php";
require_once "modules/frecuenciaventa/model.php";
require_once "modules/vendedor/model.php";
require_once "modules/flete/model.php";
require_once "modules/tipofactura/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/listaprecio/model.php";
require_once "modules/cuentacorrientecliente/model.php";
require_once "modules/tipomovimientocuenta/model.php";
require_once "modules/estadomovimientocuenta/model.php";
require_once "modules/estadoentrega/model.php";
require_once "modules/entregacliente/model.php";
require_once "modules/entregaclientedetalle/model.php";
require_once "modules/productomarca/model.php";
require_once "modules/productocategoria/model.php";
require_once "modules/productounidad/model.php";
require_once "modules/producto/model.php";
require_once "modules/stock/model.php";
require_once "modules/estadopedido/model.php";
require_once "modules/pedidovendedor/model.php";
require_once "modules/pedidovendedordetalle/model.php";
require_once "core/helpers/filehandler.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

class ApiController {

    function __construct() {
        
    }

    function checkin() {
        $usuario = filter_input(INPUT_POST, 'usuario');
        $pass = filter_input(INPUT_POST, 'pass');
        if (!is_null($usuario) && !is_null($pass)) {
            $user = hash(ALGORITMO_USER, $usuario);
            $clave = hash(ALGORITMO_PASS, $pass);
            $hash = hash(ALGORITMO_FINAL, $user . $clave);
            
            $usuariodetalle_id = User::get_usuariodetalle_id($hash);
            $respuesta = array();
            if ($usuariodetalle_id != 0) {
                $usuario_id = User::get_usuario_id($usuariodetalle_id);
                if ($usuario_id != 0) {
                    $um = new Usuario();
                    $um->usuario_id = $usuario_id;
                    $um->get();
                    $respuesta['usuario'] = $um;
                    $arraySinCRC32 = Collector()->get('Usuario');
                    $arrayConCRC32 = array();
                    foreach ($arraySinCRC32 as $value) {
                        $userAux = new stdClass();
                        $userAux->crc32 = hash(ALGORITMO_USER, $value->denominacion);
                        $userAux->usuario_id = $value->usuario_id;
                        $arrayConCRC32[]=$userAux;
                    }
                    $respuesta['usuario_collection'] = $arraySinCRC32;
                    $respuesta['crc_collection'] = $arrayConCRC32;
                    $json = new stdClass();
                    $json->resultados = $respuesta;
                    $jwt = new JwtProtocolo();
                    echo Util::respuestaJSON($jwt->crearToken($json));
                }
            } else {
                $error_texto = Util::getTextoCodigo(71);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function usuario() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $arraySinCRC32 = Collector()->get('Usuario');
                $arrayConCRC32 = array();
                foreach ($arraySinCRC32 as $value) {
                    $userAux = new stdClass();
                    $userAux->crc32 = hash(ALGORITMO_USER, $value->denominacion);
                    $userAux->usuario_id = $value->usuario_id;
                    $arrayConCRC32[] = $userAux;
                }
                $respuesta['usuario_collection'] = $arraySinCRC32;
                $respuesta['crc_collection'] = $arrayConCRC32;
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function vendedor() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('Vendedor');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function usuariovendedor() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('UsuarioVendedor');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function provincia() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('Provincia');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function documentoTipo() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('DocumentoTipo');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function condicionIva() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('CondicionIva');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function condicionFiscal() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('CondicionFiscal');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function frecuenciaVenta() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('FrecuenciaVenta');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function tipoFactura() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('TipoFactura');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function listaPrecio() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('ListaPrecio');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function cliente() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $vendedor_id = $_POST['vendedor_id'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $select = "cliente_id";
                $from = "cliente";
                $where = "vendedor = {$vendedor_id} AND oculto = 0";
                $ids = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
                $arrayClientes = array();
                if (is_array($ids)) {
                    foreach ($ids as $value) {
                        $cliente = new Cliente();
                        $cliente->cliente_id = $value['cliente_id'];
                        $cliente->get();
                        array_push($arrayClientes, $cliente);
                    }
                }
                $clientes = array();
                for ($index = 0; $index < count($arrayClientes); $index++) {
                    $clienteAux = new stdClass();
                    
                    $clienteAux->cliente = $arrayClientes[$index];
                    $clienteAux->facturas =  array();
                    /*Obtengo Egresos del Cliente*/
                    $select = "egreso_id";
                    $from = "egreso";
                    $where = "cliente = {$arrayClientes[$index]->cliente_id}  ORDER BY fecha DESC LIMIT 10";
                    $egresos = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
                    /*--------------------*/
                    if(is_array($egresos)){
                        foreach ($egresos as $obj) {
                            $facturaAux = new stdClass();
                            $facturaDetalleArray = array();
                            $egreso = new Egreso();
                            $egreso->egreso_id = $obj['egreso_id'];
                            $egreso__id = $obj['egreso_id'];
                            $egreso->get();
                            if($egreso->tipofactura->tipofactura_id==2){
                                $comprobante = str_pad($egreso->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
                                $comprobante .= str_pad($egreso->numero_factura, 8, '0', STR_PAD_LEFT);
                            } else {
                                $select_egresoafip = "CONCAT(LPAD(eafip.punto_venta, 4, 0), '-', LPAD (eafip.numero_factura, 8, 0)) AS nro";
                                $from_egresoafip = "egresoafip eafip ";
                                $where_egresoafip = "eafip.egreso_id = {$egreso__id}";
                                $egresoafip = CollectorCondition()->get('EgresoAfip', $where_egresoafip, 4, $from_egresoafip, $select_egresoafip);
                                $comprobante = (is_array($egresoafip))?$egresoafip[0]['nro']:0;
                            }
                            $facturaAux->egreso_id = $egreso->egreso_id;
                            $facturaAux->nro_comprobante = $comprobante;
                            /* Obtengo Detalles del Egresos del Cliente */
                            $select = "egresodetalle_id";
                            $from = "egresodetalle";
                            $where = "egreso_id = {$egreso->egreso_id}";
                            $detallesegreso = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);
                            $arrayDetalleEgreso = array();                            
                            if(is_array($detallesegreso)){
                                foreach ($detallesegreso as $value) {
                                    $detalleegreso = new EgresoDetalle();
                                    $detalleegreso->egresodetalle_id = $value['egresodetalle_id'];
                                    $detalleegreso->get();
                                    array_push($arrayDetalleEgreso, $detalleegreso);
                                }
                            }
                            /* -------------------- */
                            /* Obtengo Nota de Credito del Egreso */
                            $select = "notacredito_id";
                            $from = "notacredito";
                            $where = "egreso_id = {$egreso->egreso_id}";
                            $notacredito_id = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
                            
                            /* -------------------- */
                            $notacredito = NULL;
                            $detalleNotaCredito = array();
                            if(is_array($notacredito_id)){ 
                                $notacredito = new NotaCredito();
                                $notacredito->notacredito_id = $notacredito_id[0]['notacredito_id'];
                                $notacredito->get();
                                /* Obtengo Detalle Nota de Credito del Egreso */
                                $select = "notacreditodetalle_id";
                                $from = "notacreditodetalle";
                                $where = "notacredito_id = {$notacredito->notacredito_id}";
                                $notacreditodetalles = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);
                                
                            if(is_array($notacreditodetalles)){
                                foreach ($notacreditodetalles as $value) {
                                    $detallenota = new NotaCreditoDetalle();
                                    $detallenota->notacreditodetalle_id = $value['notacreditodetalle_id'];
                                    $detallenota->get();
                                    array_push($detalleNotaCredito, $detallenota);
                                    }
                                } 
                                /* -------------------- */
                            }
                           
                            if($notacredito==NULL){
                                $facturaAux->fecha = $egreso->fecha;
                                $facturaAux->importe = $egreso->importe_total;
                                foreach ($arrayDetalleEgreso as $detalleegresoaux) {
                                    $detalleFactura = new stdClass();
                                    $detalleFactura->producto = $detalleegresoaux->producto_id;
                                    $detalleFactura->cantidad = $detalleegresoaux->cantidad;
                                    $detalleFactura->importe = $detalleegresoaux->importe;
                                    $detalleFactura->precio_unitario = $detalleegresoaux->importe/$detalleegresoaux->cantidad;
                                    array_push($facturaDetalleArray, $detalleFactura);
                                }
                                $facturaAux->detalle = $facturaDetalleArray;
                            } else {
                                $facturaAux->fecha = $egreso->fecha;
                                $facturaAux->importe = $egreso->importe_total - $notacredito->importe_total;
                                foreach ($arrayDetalleEgreso as $detalleegresoaux) {
                                    $detalleFactura = new stdClass();
                                    $restado = false;
                                    foreach ($detalleNotaCredito as $detallenotaAux) {
                                        if ($detalleegresoaux->producto_id == $detallenotaAux->producto_id) {
                                            $detalleFactura->producto = $detalleegresoaux->producto_id;
                                            $detalleFactura->cantidad = $detalleegresoaux->cantidad - $detallenotaAux->cantidad;
                                            $detalleFactura->importe = $detalleegresoaux->importe - $detallenotaAux->cantidad;
                                            $detalleFactura->precio_unitario = $detalleegresoaux->importe / $detalleegresoaux->cantidad;
                                            $restado = true;
                                        }
                                    }
                                    if (!$restado) {
                                        $detalleFactura->producto = $detalleegresoaux->producto_id;
                                        $detalleFactura->cantidad = $detalleegresoaux->cantidad;
                                        $detalleFactura->importe = $detalleegresoaux->importe;
                                        $detalleFactura->precio_unitario = $detalleegresoaux->importe / $detalleegresoaux->cantidad;
                                    }
                                    array_push($facturaDetalleArray, $detalleFactura);                                    
                                }
                                $facturaAux->detalle = $facturaDetalleArray;
                            }
                            array_push($clienteAux->facturas, $facturaAux);
                        }
                    }
                    array_push($clientes, $clienteAux);
                }
                
                $respuesta = $clientes;
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function newcliente() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $vendedor_id = $_POST['vendedor_id'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $select = "cliente_id";
                $from = "cliente";
                $where = "vendedor = {$vendedor_id} AND oculto = 0";
                $ids = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
                $arrayClientes = array();
                if (is_array($ids)) {
                    foreach ($ids as $value) {
                        $cliente = new Cliente();
                        $cliente->cliente_id = $value['cliente_id'];
                        $cliente->get();
                        array_push($arrayClientes, $cliente);
                    }
                }
                $clientes = array();
                for ($index = 0; $index < count($arrayClientes); $index++) {
                    $clienteAux = new stdClass();
                    
                    $clienteAux->cliente = $arrayClientes[$index];
                    $clienteAux->facturas =  array();
                    /*Obtengo Egresos del Cliente*/
                    $select = "egreso_id";
                    $from = "egreso";
                    $where = "cliente = {$arrayClientes[$index]->cliente_id} ORDER BY fecha DESC LIMIT 10";
                    $egresos = CollectorCondition()->get('Egreso', $where, 4, $from, $select);
                    /*--------------------*/
                    if(is_array($egresos)){
                        foreach ($egresos as $obj) {
                            $facturaAux = new stdClass();
                            $facturaDetalleArray = array();
                            $egreso = new Egreso();
                            $egreso->egreso_id = $obj['egreso_id'];
                            $egreso__id = $obj['egreso_id'];
                            $egreso->get();
                            if($egreso->tipofactura->tipofactura_id==2){
                                $comprobante = str_pad($egreso->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
                                $comprobante .= str_pad($egreso->numero_factura, 8, '0', STR_PAD_LEFT);
                            } else {
                                $select_egresoafip = "CONCAT(LPAD(eafip.punto_venta, 4, 0), '-', LPAD (eafip.numero_factura, 8, 0)) AS nro";
                                $from_egresoafip = "egresoafip eafip ";
                                $where_egresoafip = "eafip.egreso_id = {$egreso__id}";
                                $egresoafip = CollectorCondition()->get('EgresoAfip', $where_egresoafip, 4, $from_egresoafip, $select_egresoafip);
                                $comprobante = (is_array($egresoafip))?$egresoafip[0]['nro']:0;
                            }
                            $facturaAux->egreso_id = $egreso->egreso_id;
                            $facturaAux->nro_comprobante = $comprobante;
                            /* Obtengo Detalles del Egresos del Cliente */
                            $select = "egresodetalle_id";
                            $from = "egresodetalle";
                            $where = "egreso_id = {$egreso->egreso_id}";
                            $detallesegreso = CollectorCondition()->get('EgresoDetalle', $where, 4, $from, $select);
                            $arrayDetalleEgreso = array();                            
                            if(is_array($detallesegreso)){
                                foreach ($detallesegreso as $value) {
                                    $detalleegreso = new EgresoDetalle();
                                    $detalleegreso->egresodetalle_id = $value['egresodetalle_id'];
                                    $detalleegreso->get();
                                    array_push($arrayDetalleEgreso, $detalleegreso);
                                }
                            }
                            /* -------------------- */
                            /* Obtengo Nota de Credito del Egreso */
                            $select = "notacredito_id";
                            $from = "notacredito";
                            $where = "egreso_id = {$egreso->egreso_id}";
                            $notacredito_id = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
                            
                            /* -------------------- */
                            $notacredito = NULL;
                            $detalleNotaCredito = array();
                            if(is_array($notacredito_id)){ 
                                $notacredito = new NotaCredito();
                                $notacredito->notacredito_id = $notacredito_id[0]['notacredito_id'];
                                $notacredito->get();
                                /* Obtengo Detalle Nota de Credito del Egreso */
                                $select = "notacreditodetalle_id";
                                $from = "notacreditodetalle";
                                $where = "notacredito_id = {$notacredito->notacredito_id}";
                                $notacreditodetalles = CollectorCondition()->get('NotaCreditoDetalle', $where, 4, $from, $select);
                                
                            if(is_array($notacreditodetalles)){
                                foreach ($notacreditodetalles as $value) {
                                    $detallenota = new NotaCreditoDetalle();
                                    $detallenota->notacreditodetalle_id = $value['notacreditodetalle_id'];
                                    $detallenota->get();
                                    array_push($detalleNotaCredito, $detallenota);
                                    }
                                } 
                                /* -------------------- */
                            }
                           
                            if($notacredito==NULL){
                                $facturaAux->fecha = $egreso->fecha;
                                $facturaAux->importe = $egreso->importe_total;
                                foreach ($arrayDetalleEgreso as $detalleegresoaux) {
                                    $detalleFactura = new stdClass();
                                    $detalleFactura->producto = $detalleegresoaux->producto_id;
                                    $detalleFactura->cantidad = $detalleegresoaux->cantidad;
                                    $detalleFactura->importe = $detalleegresoaux->importe;
                                    $detalleFactura->precio_unitario = $detalleegresoaux->importe/$detalleegresoaux->cantidad;
                                    array_push($facturaDetalleArray, $detalleFactura);
                                }
                                $facturaAux->detalle = $facturaDetalleArray;
                            } else {
                                $facturaAux->fecha = $egreso->fecha;
                                $facturaAux->importe = $egreso->importe_total - $notacredito->importe_total;
                                foreach ($arrayDetalleEgreso as $detalleegresoaux) {
                                    $detalleFactura = new stdClass();
                                    $restado = false;
                                    foreach ($detalleNotaCredito as $detallenotaAux) {
                                        if ($detalleegresoaux->producto_id == $detallenotaAux->producto_id) {
                                            $detalleFactura->producto = $detalleegresoaux->producto_id;
                                            $detalleFactura->cantidad = $detalleegresoaux->cantidad - $detallenotaAux->cantidad;
                                            $detalleFactura->importe = $detalleegresoaux->importe - $detallenotaAux->cantidad;
                                            $detalleFactura->precio_unitario = $detalleegresoaux->importe / $detalleegresoaux->cantidad;
                                            $restado = true;
                                        }
                                    }
                                    if (!$restado) {
                                        $detalleFactura->producto = $detalleegresoaux->producto_id;
                                        $detalleFactura->cantidad = $detalleegresoaux->cantidad;
                                        $detalleFactura->importe = $detalleegresoaux->importe;
                                        $detalleFactura->precio_unitario = $detalleegresoaux->importe / $detalleegresoaux->cantidad;
                                    }
                                    array_push($facturaDetalleArray, $detalleFactura);                                    
                                }
                                $facturaAux->detalle = $facturaDetalleArray;
                            }
                            array_push($clienteAux->facturas, $facturaAux);
                        }
                    }
                    
                    /*--------------------*/
                    $select = "ccc.importe AS IMPORTE, ccc.fecha AS FECHA";
                    $from = "cuentacorrientecliente ccc";
                    $where = "ccc.cliente_id = {$arrayClientes[$index]->cliente_id} AND ccc.egreso_id = 0";
                    $ctacte_migracion = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
                    if (is_array($ctacte_migracion) AND !empty($ctacte_migracion)) {
                        $facturaAux = new stdClass();
                        $facturaDetalleArray = array();

                        $facturaAux->egreso_id = 0;
                        $facturaAux->nro_comprobante = 'Migración cta cte';
                        $facturaAux->fecha = $ctacte_migracion[0]['FECHA'];
                        $facturaAux->importe = $ctacte_migracion[0]['IMPORTE'];
                        $facturaAux->detalle = array();
                        array_push($clienteAux->facturas, $facturaAux);
                    }
                    /*--------------------*/
                    array_push($clientes, $clienteAux);
                }
                
                $respuesta = $clientes;
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function tipoMovimientoCuenta() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('TipoMovimientoCuenta');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function estadoMovimientoCuenta() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('EstadoMovimientoCuenta');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function cuentaCorrienteCliente() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $vendedor_id = $_POST['vendedor_id'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $select = "cc.cuentacorrientecliente_id cuentacorrientecliente_id";
                $from = "cuentacorrientecliente cc, cliente c";
                $where = "cc.estadomovimientocuenta != 4 AND cc.cliente_id = c.cliente_id AND c.vendedor = {$vendedor_id}";
                $ids = CollectorCondition()->get('CuentaCorrienteCliente', $where, 4, $from, $select);
                
                $respuesta = array();
                if (is_array($ids)) {
                    foreach ($ids as $value) {
                        $cuenta = new CuentaCorrienteCliente();
                        $cuenta->cuentacorrientecliente_id = $value['cuentacorrientecliente_id'];
                        $cuenta->get();

                        $egreso_id = $cuenta->egreso_id;
                        if ($egreso_id == 0) {
                            $comprobante = 'Migración de cuenta corriente';
                        } else {
                            $egreso = new Egreso();
                            $egreso->egreso_id = $cuenta->egreso_id;
                            $egreso->get();
                            $egreso__id = $egreso->egreso_id;

                            if ($egreso->tipofactura->tipofactura_id == 2) {
                                $comprobante = str_pad($egreso->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
                                $comprobante .= str_pad($egreso->numero_factura, 8, '0', STR_PAD_LEFT);
                            } else {
                                $select_egresoafip = "CONCAT(LPAD(eafip.punto_venta, 4, 0), '-', LPAD (eafip.numero_factura, 8, 0)) AS nro";
                                $from_egresoafip = "egresoafip eafip ";
                                $where_egresoafip = "eafip.egreso_id = {$egreso__id}";
                                $egresoafip = CollectorCondition()->get('EgresoAfip', $where_egresoafip, 4, $from_egresoafip, $select_egresoafip);
                                $comprobante = (is_array($egresoafip))?$egresoafip[0]['nro']:0;
                            }
                        }

                        $cuenta->referencia = $comprobante;    
                        array_push($respuesta, $cuenta);
                    }
                }
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function estadoEntrega() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('EstadoEntrega');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function entregaCliente() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                if (isset($_POST['objeto']) && !empty($_POST['objeto'])) {
                    $json = json_decode($_POST['objeto']);
                    $entregaCliente = new EntregaCliente;
                    $entregaCliente->fecha = $json->fecha;
                    $entregaCliente->monto = $json->monto;
                    $entregaCliente->estado = $json->estadoentrega;
                    $entregaCliente->vendedor_id = $json->vendedor;
                    $entregaCliente->cliente_id = $json->cliente;
                    $entregaCliente->entregacliente_id = $json->idremoto;
                    $entregaCliente->save();
                    
                    foreach ($json->detalles as $detalle) {
                        
                        $entregaclientedetalle = new EntregaClienteDetalle();
                        $entregaclientedetalle->entregacliente_id = $entregaCliente->entregacliente_id;
                        $entregaclientedetalle->egreso_id = $detalle->egreso_id;
                        $entregaclientedetalle->monto = $detalle->monto;
                        $entregaclientedetalle->parcial = $detalle->parcial;                        
                        $entregaclientedetalle->save();
                    }
                    $respuesta = $entregaCliente;
                } else {
                    $vendedor_id = $_POST['vendedor_id'];
                    $select = "entregacliente_id";
                    $from = "entregacliente";
                    $where = "estado = 1 AND vendedor_id = {$vendedor_id}";
                    $ids = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
                    $arrayEntregaClientes = array();
                    if (is_array($ids)) {
                        foreach ($ids as $value) {
                            $entregacliente = new EntregaCliente();
                            $entregacliente->entregacliente_id = $value['entregacliente_id'];
                            $entregacliente->get();
                            $entregacliente->getDetalles();
                            array_push($arrayEntregaClientes, $entregacliente);
                        }
                    }
                    $respuesta = $arrayEntregaClientes;
                }
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function productoMarca() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('ProductoMarca');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function productoCategoria() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('ProductoCategoria');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function productoUnidad() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('ProductoUnidad');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function bk_producto() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $productos = Collector()->get('Producto');
                $respuesta = array();
                if (is_array($productos)) {
                    foreach ($productos as $obj) {
                        $producto = new stdClass();
                        $select = "MAX(s.stock_id) AS MAXID";
                        $from = "stock s";
                        $where = "s.producto_id = {$obj->producto_id}"; 
                        $stock_id = CollectorCondition()->get('Stock', $where, 4, $from, $select);
                        $stock_id = $stock_id[0]['MAXID'];

                        $sm = new Stock();
                        $sm->stock_id = $stock_id;
                        $sm->get();

                        $select = "SUM(pvd.cantidad) AS SUGERIDO ";
                        $from = "pedidovendedor pv INNER JOIN pedidovendedordetalle pvd ON pv.pedidovendedor_id = pvd.pedidovendedor_id";
                        $where = "pv.estadopedido = 1 AND pvd.producto_id = {$obj->producto_id}"; 
                        $group_by = "pvd.producto_id"; 
                        $sugerido = CollectorCondition()->get('PedidoVendedor', $where, 4, $from, $select, $group_by);
                        $sugerido = (is_array($sugerido) AND !empty($sugerido)) ? $sugerido[0]['SUGERIDO'] : 0;

                        $cantidad_disponible = $sm->cantidad_actual;
                        $stock_sugerido = round(($cantidad_disponible - $sugerido),2);
                        $producto->producto_id = $obj->producto_id;
                        $producto->codigo = $obj->codigo;
                        $producto->denominacion = $obj->codigo." - ".$obj->denominacion;
                        $producto->costo = $obj->costo;
                        $producto->descuento = $obj->descuento;
                        $producto->flete = $obj->flete;
                        $producto->porcentaje_ganancia = $obj->porcentaje_ganancia;
                        $producto->iva = $obj->iva;
                        $producto->exento = $obj->exento;
                        $producto->no_gravado = $obj->no_gravado;
                        $producto->stock_minimo = $obj->stock_minimo;
                        $producto->stock_ideal = $obj->stock_ideal;
                        $producto->dias_reintegro = $obj->dias_reintegro;
                        $producto->oculto = $obj->oculto;
                        $producto->barcode = $obj->barcode;
                        $producto->detalle = $obj->detalle;
                        $producto->productomarca = $obj->productomarca;
                        $producto->productocategoria = $obj->productocategoria;
                        $producto->productounidad = $obj->productounidad;
                        $producto->cantidad_disponible = $cantidad_disponible;
                        $producto->cantidad_sugerida = $stock_sugerido;
                        if($producto->oculto == 0){
                            array_push($respuesta, $producto);
                        }
                    }
                }
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function producto() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $select = "p.producto_id AS ID, s.cantidad_actual AS CANTIDAD";
                $from = "stock s INNER JOIN (SELECT MAX(s1.stock_id) AS MAXID, s1.producto_id AS PROID FROM stock s1 WHERE s1.almacen_id = 1 GROUP BY s1.producto_id) ultimo ON ultimo.MAXID = s.stock_id AND ultimo.PROID = s.producto_id INNER JOIN producto p ON s.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
                $where = "s.cantidad_actual > 0  AND p.oculto = 0 AND s.almacen_id = 1";
                $stock_collection = CollectorCondition()->get('Stock', $where, 4, $from, $select);

                $respuesta = array();
                if (is_array($stock_collection)) {
                    foreach ($stock_collection as $clave=>$valor) {
                        $producto_id = $valor['ID'];                        
                        $pm = new Producto(); 
                        $pm->producto_id = $producto_id;
                        $pm->get();

                        if ($pm->oculto == 0) {
                            $select = "SUM(pvd.cantidad) AS SUGERIDO ";
                            $from = "pedidovendedor pv INNER JOIN pedidovendedordetalle pvd ON pv.pedidovendedor_id = pvd.pedidovendedor_id";
                            $where = "pv.estadopedido = 1 AND pvd.producto_id = {$producto_id}"; 
                            $group_by = "pvd.producto_id"; 
                            $sugerido = CollectorCondition()->get('PedidoVendedor', $where, 4, $from, $select, $group_by);
                            $sugerido = (is_array($sugerido) AND !empty($sugerido)) ? $sugerido[0]['SUGERIDO'] : 0;

                            $cantidad_disponible = $valor['CANTIDAD'];
                            $stock_sugerido = round(($cantidad_disponible - $sugerido),2);

                            $producto = new stdClass();
                            $producto->producto_id = $pm->producto_id;
                            $producto->codigo = $pm->codigo;
                            $producto->denominacion = $pm->codigo." - ".$pm->denominacion;
                            $producto->costo = $pm->costo;
                            $producto->descuento = $pm->descuento;
                            $producto->flete = $pm->flete;
                            $producto->porcentaje_ganancia = $pm->porcentaje_ganancia;
                            $producto->iva = $pm->iva;
                            $producto->exento = $pm->exento;
                            $producto->no_gravado = $pm->no_gravado;
                            $producto->stock_minimo = $pm->stock_minimo;
                            $producto->stock_ideal = $pm->stock_ideal;
                            $producto->dias_reintegro = $pm->dias_reintegro;
                            $producto->oculto = $pm->oculto;
                            $producto->barcode = $pm->barcode;
                            $producto->detalle = $pm->detalle;
                            $producto->productomarca = $pm->productomarca;
                            $producto->productocategoria = $pm->productocategoria;
                            $producto->productounidad = $pm->productounidad;
                            $producto->cantidad_disponible = $cantidad_disponible;
                            $producto->cantidad_sugerida = $stock_sugerido;
                            array_push($respuesta, $producto);
                        }
                    }
                }

                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function lista_productos() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $select = "p.producto_id AS ID, s.cantidad_actual AS CANTIDAD";
                $from = "stock s INNER JOIN (SELECT MAX(s1.stock_id) AS MAXID, s1.producto_id AS PROID FROM stock s1 WHERE s1.almacen_id = 1 GROUP BY s1.producto_id) ultimo ON ultimo.MAXID = s.stock_id AND ultimo.PROID = s.producto_id INNER JOIN producto p ON s.producto_id = p.producto_id INNER JOIN productomarca pm ON p.productomarca = pm.productomarca_id";
                $where = "s.cantidad_actual > 0  AND p.oculto = 0 AND s.almacen_id = 1";
                $stock_collection = CollectorCondition()->get('Stock', $where, 4, $from, $select);

                $respuesta = array();
                if (is_array($stock_collection)) {
                    foreach ($stock_collection as $clave=>$valor) {
                        $producto_id = $valor['ID'];                        
                        $pm = new Producto(); 
                        $pm->producto_id = $producto_id;
                        $pm->get();

                        if ($pm->oculto == 0) {
                            $select = "SUM(pvd.cantidad) AS SUGERIDO ";
                            $from = "pedidovendedor pv INNER JOIN pedidovendedordetalle pvd ON pv.pedidovendedor_id = pvd.pedidovendedor_id";
                            $where = "pv.estadopedido = 1 AND pvd.producto_id = {$producto_id}"; 
                            $group_by = "pvd.producto_id"; 
                            $sugerido = CollectorCondition()->get('PedidoVendedor', $where, 4, $from, $select, $group_by);
                            $sugerido = (is_array($sugerido) AND !empty($sugerido)) ? $sugerido[0]['SUGERIDO'] : 0;

                            $cantidad_disponible = $valor['CANTIDAD'];
                            $stock_sugerido = round(($cantidad_disponible - $sugerido),2);

                            $producto = new stdClass();
                            $producto->producto_id = $pm->producto_id;
                            $producto->codigo = $pm->codigo;
                            $producto->denominacion = $pm->codigo." - ".$pm->denominacion;
                            $producto->costo = $pm->costo;
                            $producto->descuento = $pm->descuento;
                            $producto->flete = $pm->flete;
                            $producto->porcentaje_ganancia = $pm->porcentaje_ganancia;
                            $producto->iva = $pm->iva;
                            $producto->exento = $pm->exento;
                            $producto->no_gravado = $pm->no_gravado;
                            $producto->stock_minimo = $pm->stock_minimo;
                            $producto->stock_ideal = $pm->stock_ideal;
                            $producto->dias_reintegro = $pm->dias_reintegro;
                            $producto->oculto = $pm->oculto;
                            $producto->barcode = $pm->barcode;
                            $producto->detalle = $pm->detalle;
                            $producto->productomarca = $pm->productomarca;
                            $producto->productocategoria = $pm->productocategoria;
                            $producto->productounidad = $pm->productounidad;
                            $producto->cantidad_disponible = $cantidad_disponible;
                            $producto->cantidad_sugerida = $stock_sugerido;
                            array_push($respuesta, $producto);
                        }
                    }
                }

                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function estadoPedido() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                $respuesta = Collector()->get('EstadoPedido');
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function pedidoVendedor() {
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                if (isset($_POST['objeto']) && !empty($_POST['objeto'])) {
                    $json = json_decode($_POST['objeto']);
                    $fecha_hora = explode(" ", $json->fecha_hora);
                    $pedidoVendedor = new PedidoVendedor();
                    $pedidoVendedor->fecha = $fecha_hora[0];
                    $pedidoVendedor->hora = $fecha_hora[1];
                    $pedidoVendedor->subtotal = $json->subtotal;
                    $pedidoVendedor->importe_total = $json->importe_total;
                    $pedidoVendedor->detalle = $json->detalle;
                    $pedidoVendedor->condicionpago = $json->condicion_pago;
                    $pedidoVendedor->estadopedido = $json->estado_pedido;
                    $pedidoVendedor->cliente_id = $json->cliente_id;
                    $pedidoVendedor->vendedor_id = $json->vendedor_id;
                    $pedidoVendedor->save();
                    $detalle_array = $json->detalles;
                    $pedidovendedordetalle_ids = array();
                    foreach ($detalle_array as $detalle) {
                        $edm = new PedidoVendedorDetalle();
                        $edm->codigo_producto = $detalle->codigo_producto;
                        $edm->descripcion_producto = $detalle->descripcion_producto;
                        $edm->cantidad = $detalle->cantidad;
                        $edm->descuento = $detalle->descuento;
                        $edm->valor_descuento = $detalle->valor_descuento;
                        $edm->costo_producto = $detalle->costo_producto;
                        $edm->iva = $detalle->iva;
                        $edm->importe = $detalle->importe;
                        $edm->valor_ganancia = $detalle->valor_ganancia;
                        $edm->producto_id = $detalle->producto_id;
                        $edm->pedidovendedor_id = $pedidoVendedor->pedidovendedor_id;
                        $edm->save();
                        $pedidovendedordetalle_ids[] = $edm->pedidovendedordetalle_id;
                    }
                    if (count($detalle_array) == count($pedidovendedordetalle_ids)) {
                        $respuesta = $pedidoVendedor;
                    }
                } else {
                    $respuesta = Collector()->get('EntregaCliente');
                }
                $json = new stdClass();
                $json->resultados = $respuesta;
                echo Util::respuestaJSON($json);
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }

    function check_version(){
        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $jwt = new JwtProtocolo();
            if ($jwt->autenticar($token)) {
                if (isset($_POST['objeto']) && !empty($_POST['objeto'])) {
                    $version = $_POST['objeto'];
                    $where_version = "version > '$version' AND activa = 1 ORDER BY version_id DESC LIMIT 1";
                    $respuesta = CollectorCondition()->get('Version', $where_version, 4, 'version', '*');
                    $json = new stdClass();
                    $json->resultados = $respuesta;
                    echo Util::respuestaJSON($json);
                } else {
                    $error_texto = Util::getTextoCodigo(77);
                    echo $error_texto;
                }
            } else {
                $error_texto = Util::getTextoCodigo(72);
                echo $error_texto;
            }
        } else {
            $error_texto = Util::getTextoCodigo(77);
            echo $error_texto;
        }
    }
    
    function download_version($arg) {
        header("Location: " . "/dh_tordo/static/apk/".$arg);
    }

}
?>