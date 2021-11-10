<?php

Class Util {

    public static function comprobarRespuestaDatos($respuesta) {
        $haydatos = true;
        $flag_error = 0;
        $r = trim($respuesta);
        $errores = new Errores();
        switch ($r) {
            case $errores->COD_CONEXION:
                $haydatos = false;
                $flag_error = 71;
                break;
            case $errores->COD_NOAUTH_JWT:
                $haydatos = false;
                $flag_error = 72;
                break;
            case $errores->COD_NO_JWT:
                $haydatos = false;
                $flag_error = 73;
                break;
            case $errores->COD_CONEXIONBASE:
                $haydatos = false;
                $flag_error = 74;
                break;
            case $errores->COD_NOAUTH_USER:
                $haydatos = false;
                $flag_error = 75;
                break;
            case $errores->COD_QUERYBASE:
                $haydatos = false;
                $flag_error = 76;
                break;
            case $errores->COD_PARAMETROS:
                $haydatos = false;
                $flag_error = 77;
                break;
            case $errores->COD_NO_DATA:
                $haydatos = false;
                $flag_error = 1;
                break;
            case $errores->COD_RESTRICTED_DATA:
                $haydatos = false;
                $flag_error = 2;
                break;
            case $errores->COD_NIS_INVALIDO:
                $haydatos = false;
                $flag_error = 10;
                break;
            case $errores->COD_DNI_INVALIDO:
                $haydatos = false;
                $flag_error = 11;
                break;
            case $errores->COD_EMAIL_INVALIDO:
                $haydatos = false;
                $flag_error = 12;
                break;
            case $errores->COD_IDCLIENTE_INVALIDO:
                $haydatos = false;
                $flag_error = 13;
                break;
            case $errores->COD_DESC:
                $haydatos = false;
                $flag_error = 90;
                break;
            default:
                if (Util::contiene($errores->COD_CONEXION_SERVLET, $r)) {
                    $haydatos = false;
                    $flag_error = 78;
                }
                if (Util::contiene($errores->COD_CONEXION_SERVLET_GATEWAY, $r)) {
                    $haydatos = false;
                    $flag_error = 79;
                }
                if (Util::contiene($errores->COD_CONEXION_SERVLET_CONCURRENCIA, $r)) {
                    $haydatos = false;
                    $flag_error = 80;
                }
                if (Util::contiene($errores->COD_CONEXION_PROHIBIDO, $r)) {
                    $haydatos = false;
                    $flag_error = 81;
                }
                break;
        }
        $array_haydatos = array('haydatos' => $haydatos, 'flag_error' => $flag_error);
        return $array_haydatos;
    }

    public static function LogError($codigo, $servlet, $ws, $ip, $session_id) {
        if (Util::islogearError($codigo)) {
            Util::insertarLog($codigo, $servlet, $ws, $ip, $session_id);
        }
    }

    public static function islogearError($codigo) {
        $logear = false;
        switch ($codigo) {
            case 71:
                $logear = true;
                break;
            case 72:
                $logear = true;
                break;
            case 73:
                $logear = true;
                break;
            case 74:
                $logear = true;
                break;
            case 75:
                $logear = true;
                break;
            case 76:
                $logear = true;
                break;
            case 77:
                $logear = true;
                break;
            case 78:
                $logear = true;
                break;
            case 79:
                $logear = true;
                break;
            case 80:
                $logear = true;
                break;
            case 81:
                $logear = true;
                break;
            case 90:
                $logear = true;
                break;
            default:
                break;
        }
        return $logear;
    }

    public static function LogAccess($servlet, $ws, $ip, $debug, $extras, $session_id, $uagent, $user_ws) {
        $conexion_config = "mysql:host=localhost;dbname=" . LOG_BASE . "";
        $opciones = array(PDO::ATTR_PERSISTENT => false);
        $sql = "INSERT INTO log_ws_acceso (ws,servlet,ip,session,parametros,debug,estado,fecha,uagent,usuario_ws) VALUES (?,?,?,?,?,?,?,now(),?,?)";
        $conexion = null;
        $parametros = "";
        $cantidad = count($extras);
        $cont = 1;
        foreach ($extras as $peticion) {
            $parametros = $parametros . $peticion->getParametro() . "=" . urlencode($peticion->getValor());
            if ($cantidad > $cont)
                $parametros = $parametros . "&";
            $cont++;
        }
        try {
            $conexion = new PDO($conexion_config, LOG_DB_USER, LOG_DB_PASS, $opciones);
            try {                
                $stmt = $conexion->prepare($sql);
                $conexion->beginTransaction();
                $d = 0;
                if ($debug)
                    $d = 1;
                $stmt->execute([$ws, $servlet, $ip, $session_id, $parametros, $d, "ejecutando", $uagent, $user_ws]);
                $conexion->commit();
            } catch (Exception $ex) {
                $conexion->rollback();
            }
        } catch (PDOException $e) {
            
        }
    }

    public static function insertarLog($codigo, $servlet, $ws, $ip, $session_id) {
        $denominacion = Util::getTextoCodigo($codigo);
        $conexion_config = "mysql:host=localhost;dbname=" . LOG_BASE . "";
        $opciones = array(PDO::ATTR_PERSISTENT => true);
        $sql = "INSERT INTO log_ws_error (codigo,denominacion,ws,servlet,ip,session,fecha) VALUES (?,?,?,?,?,?,now())";
        $conexion = null;
        try {
            $conexion = new PDO($conexion_config, LOG_DB_USER, LOG_DB_PASS, $opciones);
            try {
                $stmt = $conexion->prepare($sql);
                $conexion->beginTransaction();
                $stmt->execute([$codigo, $denominacion, $ws, $servlet, $ip, $session_id]);
                $conexion->commit();
            } catch (Exception $ex) {
                $conexion->rollback();
            }
        } catch (PDOException $e) {
            
        }
    }

    public static function UpdateAccessLog($session_id, $estado) {
        $conexion_config = "mysql:host=localhost;dbname=" . LOG_BASE . "";
        $opciones = array(PDO::ATTR_PERSISTENT => true);
        $sql = "UPDATE log_ws_acceso SET estado = ? WHERE session = ?";
        $conexion = null;
        try {
            $conexion = new PDO($conexion_config, LOG_DB_USER, LOG_DB_PASS, $opciones);
            try {
                $stmt = $conexion->prepare($sql);
                $conexion->beginTransaction();
                $stmt->execute([$estado, $session_id]);
                $conexion->commit();
            } catch (Exception $ex) {
                $conexion->rollback();
            }
        } catch (PDOException $e) {
            
        }
    }

    public static function getTextoCodigo($codigo) {
        $error_texto = "";
        switch ($codigo) {
            case 71:
                $error_texto = "Error de usuario/contraseña incorrectos.\n";
                break;
            case 72:
                $error_texto = "Error token invalido.\n";
                break;
            case 73:
                $error_texto = "No se encontro el archivo.\n";
                break;
            case 75:
                $error_texto = "Error usuario de ws invalido.\n";
                break;
            case 77:
                $error_texto = "No se enviaron parametros.\n";
                break;
            default:
                $error_texto = "Error deconocido.\n";
                break;
        }
        return $error_texto;
    }

    public static function respuestaJSON($datos) {
        $json = "";
        $json = json_encode($datos);
        return $json;
    }

    public static function contiene($buscar, $texto) {
        return strpos($texto, $buscar) !== false;
    }

}

?>