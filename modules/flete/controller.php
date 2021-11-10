<?php
require_once "modules/flete/model.php";
require_once "modules/flete/view.php";
require_once "modules/documentotipo/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/cobrador/model.php";


class FleteController {

	function __construct() {
		$this->model = new Flete();
		$this->view = new FleteView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$this->view->panel();
	}

	function listar() {
		$select = "f.flete_id AS FLETE_ID, f.localidad AS LOCALIDAD, f.denominacion AS DENOMINACION, 
				   CONCAT(dt.denominacion, ' ', f.documento) AS DOCUMENTO";
		$from = "flete f INNER JOIN documentotipo dt ON f.documentotipo = dt.documentotipo_id";
		$where = "f.oculto = 0";
		$flete_collection = CollectorCondition()->get('Flete', $where, 4, $from, $select);
		$this->view->listar($flete_collection);
	}

	function agregar() {
    	SessionHandler()->check_session();		
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$this->view->agregar($documentotipo_collection);
	}

	function consultar($arg) {
		SessionHandler()->check_session();		
		$this->model->flete_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function editar($arg) {
		SessionHandler()->check_session();		
		$this->model->flete_id = $arg;
		$this->model->get();
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$this->view->editar($documentotipo_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();

		$denominacion = filter_input(INPUT_POST, 'denominacion');	
		$this->model->denominacion = $denominacion;	
		$this->model->documento = filter_input(INPUT_POST, 'documento');	
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');	
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');	
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');	
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');	
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');	
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->oculto = 0;
		$this->model->save();
		$flete_id = $this->model->flete_id;

		$this->model = new Flete();
		$this->model->flete_id = $flete_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->denominacion = $clave;
				$icm->valor = $valor;
				$icm->save();
				$infocontacto_id = $icm->infocontacto_id;
				
				$icm = new InfoContacto();
				$icm->infocontacto_id = $infocontacto_id;
				$icm->get();

				$this->model->add_infocontacto($icm);
			}

			$icfm = new InfoContactoFlete($this->model);
			$icfm->save();
		}

		$cm = new Cobrador();
		$cm->denominacion = $denominacion;
		$cm->oculto = 0;
		$cm->vendedor_id = 0;
		$cm->flete_id = $flete_id;
		$cm->save();
	
		header("Location: " . URL_APP . "/flete/listar");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$flete_id = filter_input(INPUT_POST, 'flete_id');
		$this->model->flete_id = $flete_id;
		$this->model->get();

		$this->model->denominacion = filter_input(INPUT_POST, 'denominacion');	
		$this->model->documento = filter_input(INPUT_POST, 'documento');	
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');	
		$this->model->localidad = filter_input(INPUT_POST, 'localidad');	
		$this->model->latitud = filter_input(INPUT_POST, 'latitud');	
		$this->model->longitud = filter_input(INPUT_POST, 'longitud');	
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');	
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');	
		$this->model->save();
		
		$this->model = new Flete();
		$this->model->flete_id = $flete_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->infocontacto_id = $clave;
				$icm->get();
				$icm->valor = $valor;
				$icm->save();
			}
		}
	
		header("Location: " . URL_APP . "/flete/listar");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$flete_id = $arg;
		$this->model->flete_id = $arg;
		$this->model->get();
		$this->model->oculto = 1;
		$this->model->save();

		$select = "c.cobrador_id AS ID";
		$from = "cobrador c";
		$where = "c.flete_id = {$flete_id}";
		$cobrador_id = CollectorCondition()->get('Cobrador', $where, 4, $from, $select);
		$cobrador_id = (is_array($cobrador_id) AND !empty($cobrador_id)) ? $cobrador_id[0]['ID'] : 0;
		if ($cobrador_id != 0) {
			$cm = new Cobrador();
			$cm->cobrador_id = $cobrador_id;
			$cm->get();
			$cm->oculto = 1;
			$cm->save();
		}
		
		header("Location: " . URL_APP . "/flete/listar");
	}

	function buscar() {
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "f.flete_id AS FLETE_ID, f.localidad AS LOCALIDAD, f.denominacion AS DENOMINACION, 
				   CONCAT(dt.denominacion, ' ', f.documento) AS DOCUMENTO";
		$from = "flete f INNER JOIN documentotipo dt ON f.documentotipo = dt.documentotipo_id";
		$where = "f.denominacion LIKE '%{$buscar}%' OR f.documento LIKE '%{$buscar}%'";
		$flete_collection = CollectorCondition()->get('Flete', $where, 4, $from, $select);
		$this->view->listar($flete_collection);
	}

	function verifica_documento_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "flete f";
		$where = "f.documento = {$arg}";
		$flag = CollectorCondition()->get('Flete', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}
}
?>