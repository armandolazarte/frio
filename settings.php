<?php
# Ambiente del sistema
const AMBIENTE = "prod";
const SO_UNIX = true;

# Credenciales para la conexión con la base de datos MySQL
const DB_HOST = 'localhost';
const DB_USER = 'Takodana';
const DB_PASS = 'fr10d15tr1buc10n35';
const DB_NAME = 'dh.tordo.prod';


# Algoritmos utilizados para la encriptación de credenciales
# para el registro y acceso de usuarios del sistema
const ALGORITMO_USER = 'crc32';
const ALGORITMO_PASS = 'sha512';
const ALGORITMO_FINAL = 'md5';


# Direcciones a recursos estáticos de interfaz gráfica
const TEMPLATE = "static/template.html";
if (SO_UNIX == true) {
	define('URL_APP', "/frio");
	define('URL_STATIC', "/static/template/");
	
	# Directorio private del sistema
	$url_private = "/srv/websites/frio/private/";
	define('URL_PRIVATE', $url_private);
	ini_set("include_path", URL_PRIVATE);
} else {
	define('URL_APP', "/frio");
	define('URL_STATIC', "/frio/static/template/");

	# Directorio private del sistema
	$url_private = "c:/dhTordoFiles/private/";
	define('URL_PRIVATE', $url_private);
	ini_set("include_path", URL_PRIVATE);
}

# Configuración estática del sistema
const APP_TITTLE = "dhTordo";
const APP_VERSION = "v3.0.1";
const APP_ABREV = "Dharma";
const LOGIN_URI = "/usuario/login";
const DEFAULT_MODULE = "reporte";
const DEFAULT_ACTION = "panel";

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
ini_set('include_path', DOCUMENT_ROOT);

session_start();
$session_vars = array('login'=>false);
foreach($session_vars as $var=>$value) {
    if(!isset($_SESSION[$var])) $_SESSION[$var] = $value;
}
?>
