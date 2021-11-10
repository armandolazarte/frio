<?php
require_once "modules/configuracioncomprobante/model.php";
require_once "modules/configuracioncomprobante/view.php";


class ConfiguracionComprobanteController {

	function __construct() {
		$this->model = new ConfiguracionComprobante();
		$this->view = new ConfiguracionComprobanteView();
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->save();
		header("Location: " . URL_APP . "/reporte/balance");
	}
}
?>