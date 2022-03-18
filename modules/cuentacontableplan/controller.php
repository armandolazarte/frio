<?php
//require_once "modules/cuentacontableplan/model.php";
require_once "modules/cuentacontableplan/view.php";


class CuentaContablePlanController {

	function __construct() {
		//$this->model = new CuentaContablePlan();
		$this->view = new CuentaContablePlanView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$cuentacontable_collection = Collector()->get('CuentaContable');
		foreach ($cuentacontable_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cuentacontable_collection[$clave]);
		}
		
		$this->view->panel($cuentacontable_collection);
	}

	function configurar() {
    	SessionHandler()->check_session();
		/*
		$cuentacontable_collection = Collector()->get('CuentaContable');
		foreach ($cuentacontable_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cuentacontable_collection[$clave]);
		}
		*/
		$this->view->configurar();
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->oculto = 0;
        $this->model->save();
		header("Location: " . URL_APP . "/cuentacontable/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->cuentacontable_id = $arg;
		$this->model->get();
		$cuentacontable_collection = Collector()->get('CuentaContable');
		foreach ($cuentacontable_collection as $clave=>$valor) {
			if ($valor->oculto == 1) unset($cuentacontable_collection[$clave]);
		}
		
		$this->view->editar($cuentacontable_collection, $this->model);
	}

	function ocultar($arg) {
		SessionHandler()->check_session();		
		$this->model->cuentacontable_id = $arg;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/cuentacontable/panel");		
	}
}
?>