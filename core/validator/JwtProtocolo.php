<?php
require_once "common/libs/php-jwt/JWT.php";
require_once "common/libs/php-jwt/ExpiredException.php";
use \Firebase\JWT\JWT;


Class JwtProtocolo {

	private $keyjwt = "128793978129837";
	private $session_tiempo_auth = 6;
	private $session_tiempo_cliente = 15;
	private $hora;
	private $hora_exp;
	private $hora_exp_cliente;

	public function __construct(){
		$this->hora = time();
		$this->hora_exp = $this->hora + ($this->session_tiempo_auth * 60);
		$this->hora_exp_cliente = $this->hora + ($this->session_tiempo_cliente * 60);
	}

    public static function autenticar($token) {
        $keyjwt = "128793978129837";
        $kfinal = $keyjwt . $keyjwt . $keyjwt . $keyjwt . $keyjwt;
        $hash = false;
        try {
            $dec = JWT::decode($token, $kfinal, array('HS256'));
            $dec_array = (array) $dec;
            $aud = $dec_array["aud"];
            if ($aud == "proyectodharma.com") {
                $hash = true;
            } else {
                $hash = false;
            }
        } catch (Exception $e) {
            $hash = false;
        }
        return $hash;
    }

    public function crearToken($cod)
    {
        $kfinal = $this->keyjwt . $this->keyjwt . $this->keyjwt . $this->keyjwt . $this->keyjwt;
        $datos = array(
            "jti" => $cod->resultados['usuario']->usuario_id,
            "iss" => $cod->resultados['usuario']->usuariodetalle->apellido,
            "aud" => "proyectodharma.com",
            "iat" => $this->hora,
            "exp" => $this->hora_exp,
            'auth' => [
                'usuario' => $cod->resultados['usuario']->denominacion,
                'hashp' => $cod->resultados['usuario']->usuariodetalle->token]);
        $token = JWT::encode($datos, $kfinal);
        $cod->resultados['usuario']->jwt = $token;
        return $cod;
    }

	public function descifar ($respuesta){
		$kfinal = $this->keyjwt.$this->keyjwt.$this->keyjwt.$this->keyjwt.$this->keyjwt;
		try {
			$decoded = JWT::decode($respuesta , $kfinal , array('HS256'));
		} catch (Exception $e) {
			$respuesta = "ERROR_DECODEJWT";
			return $respuesta;
		}
		return $decoded;
	}

}
?>