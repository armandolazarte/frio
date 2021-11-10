<?php
require_once "modules/listaprecio/model.php";
require_once "modules/listaprecio/view.php";


class ListaPrecioController {

	function __construct() {
		$this->model = new ListaPrecio();
		$this->view = new ListaPrecioView();
	}

  function panel() {
  	SessionHandler()->check_session();

  	$listaprecio_collection = Collector()->get('ListaPrecio');
  	$this->view->panel($listaprecio_collection);
  }

  function guardar() {
    SessionHandler()->check_session();

    foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
    $this->model->save();
  	header("Location: " . URL_APP . "/listaprecio/panel");
  }

  function editar($arg) {
    SessionHandler()->check_session();

    $this->model->listaprecio_id = $arg;
    $this->model->get();
    $listaprecio_collection = Collector()->get('ListaPrecio');
    $this->view->editar($listaprecio_collection, $this->model);
  }
}
?>
