<?php
require_once "modules/configuracionbalance/model.php";
require_once "modules/configuracionbalance/view.php";


class ConfiguracionBalanceController {

	function __construct() {
		$this->model = new ConfiguracionBalance();
		$this->view = new ConfiguracionBalanceView();
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->save();
		header("Location: " . URL_APP . "/reporte/balance");
	}
}
?>