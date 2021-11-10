<?php
//error_reporting(0);
/**
* Dharma Tordo
*
* FrontCrontoller de la Aplicación.
* Rutea las peticiones del cliente teniendo en cuenta la estructura
* /MODULO/RECURSO/ARGUMENTO
*
* @package    Dharma Tordo
* @version    1.1b
**/

header('Content-Type: text/html; charset=utf8');
require_once 'settings.php';
require_once 'core/database.php';
require_once 'core/collector.php';
require_once 'core/collector_condition.php';
require_once 'core/view.php';
require_once 'core/standardobject.php';
require_once 'core/sessions.php';
require_once "core/helpers/configuracionmenu.php";
//if (AMBIENTE == 'desa') error_reporting(0);

$peticion = $_SERVER['REQUEST_URI'];
if (SO_UNIX == true) {
	@list($null, $app, $modulo, $recurso, $argumento) = explode('/', $peticion);
} else {
	@list($null, $app, $modulo, $recurso, $argumento) = explode('/', $peticion);
}


if (empty($modulo)) { $modulo = DEFAULT_MODULE; }
if (empty($recurso)) { $recurso = DEFAULT_ACTION; }

if (!file_exists("modules/{$modulo}/controller.php")) {
    $modulo = DEFAULT_MODULE;
}
$archivo = "modules/{$modulo}/controller.php";

require_once $archivo;
$controller_name = ucwords($modulo) . 'Controller';
$controller = new $controller_name;
$recurso = (method_exists($controller, $recurso)) ? $recurso : DEFAULT_ACTION;
$controller->$recurso($argumento);
?>
