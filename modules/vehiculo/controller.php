<?php
require_once "modules/vehiculo/model.php";
require_once "modules/vehiculo/view.php";
require_once "modules/vehiculomodelo/model.php";
require_once "modules/vehiculocombustible/model.php";
require_once "modules/combustible/model.php";


class VehiculoController {

	function __construct() {
		$this->model = new Vehiculo();
		$this->view = new VehiculoView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$vehiculo_collection = Collector()->get('Vehiculo');
		$vehiculomodelo_collection = Collector()->get('VehiculoModelo');
		$combustible_collection = Collector()->get('Combustible');

		foreach ($vehiculo_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($vehiculo_collection[$clave]);
		}

		foreach ($vehiculomodelo_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($vehiculomodelo_collection[$clave]);
		}

		$this->view->panel($vehiculo_collection, $vehiculomodelo_collection, $combustible_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $key=>$value) $this->model->$key = $value;
		$this->model->oculto = 0;
		$this->model->save();
		header("Location: " . URL_APP . "/vehiculo/panel");
	}
	
	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->vehiculo_id = $arg;
		$this->model->get();
		$vehiculo_collection = Collector()->get('Vehiculo');
		$vehiculomodelo_collection = Collector()->get('VehiculoModelo');
		$combustible_collection = Collector()->get('Combustible');

		foreach ($vehiculo_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($vehiculo_collection[$clave]);
		}

		foreach ($vehiculomodelo_collection as $clave=>$valor) {
			if($valor->oculto == 1) unset($vehiculomodelo_collection[$clave]);
		}

		$this->view->editar($vehiculo_collection, $vehiculomodelo_collection, $combustible_collection, $this->model);
	}

	function combustible($arg) {
		SessionHandler()->check_session();
		$this->model->vehiculo_id = $arg;
		$this->model->get();

		$select = "vc.vehiculocombustible_id AS VEHCOMID, vc.fecha AS FECHA, vc.cantidad AS LITROS, vc.importe AS IMPORTE";
		$from = "vehiculocombustible vc INNER JOIN vehiculo v ON vc.vehiculo = v.vehiculo_id";
		$where = "vc.vehiculo = {$arg}"; 
		$vehiculocombustible_collection = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);
		
		$this->view->combustible($vehiculocombustible_collection,  $this->model);
	}

	function editar_combustible($arg) {
		SessionHandler()->check_session();
		$vcm = new VehiculoCombustible();
		$vcm->vehiculocombustible_id = $arg;
		$vcm->get();
		$vehiculo_id = $vcm->vehiculo->vehiculo_id;

		$select = "vc.vehiculocombustible_id AS VEHCOMID, vc.fecha AS FECHA, vc.cantidad AS LITROS, vc.importe AS IMPORTE";
		$from = "vehiculocombustible vc INNER JOIN vehiculo v ON vc.vehiculo = v.vehiculo_id";
		$where = "vc.vehiculo = {$vehiculo_id}"; 
		$vehiculocombustible_collection = CollectorCondition()->get('VehiculoCombustible', $where, 4, $from, $select);

		$this->view->editar_combustible($vehiculocombustible_collection,  $vcm);
	}

	function guardar_combustible() {
		SessionHandler()->check_session();
		$vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id');
		$vcm = new VehiculoCombustible();
		$vcm->cantidad = filter_input(INPUT_POST, 'cantidad');
		$vcm->importe = filter_input(INPUT_POST, 'importe');
		$vcm->fecha = filter_input(INPUT_POST, 'fecha');
		$vcm->vehiculo = $vehiculo_id;
		$vcm->save();
		header("Location: " . URL_APP . "/vehiculo/combustible/{$vehiculo_id}");
	}

	function actualizar_combustible() {
		SessionHandler()->check_session();
		$vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id');
		$vcm = new VehiculoCombustible();
		$vcm->vehiculocombustible_id = filter_input(INPUT_POST, 'vehiculocombustible_id');
		$vcm->get();
		$vcm->cantidad = filter_input(INPUT_POST, 'cantidad');
		$vcm->importe = filter_input(INPUT_POST, 'importe');
		$vcm->fecha = filter_input(INPUT_POST, 'fecha');
		$vcm->save();
		header("Location: " . URL_APP . "/vehiculo/combustible/{$vehiculo_id}");
	}

	function eliminar_combustible($arg) {
		SessionHandler()->check_session();
		$vehiculocombustible_id = $arg;
		$vcm = new VehiculoCombustible();
		$vcm->vehiculocombustible_id = $vehiculocombustible_id;
		$vcm->get();
		$vehiculo_id = $vcm->vehiculo->vehiculo_id;
		$vcm->delete();		
		header("Location: " . URL_APP . "/vehiculo/combustible/{$vehiculo_id}");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$vehiculo_id = $arg;
		$this->model->vehiculo_id = $vehiculo_id;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/vehiculo/listar");
	}
}
?>