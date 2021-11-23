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
    foreach ($listaprecio_collection as $clave=>$valor) {
      if ($valor->oculto == 1) unset($listaprecio_collection[$clave]);
    }

    $this->view->panel($listaprecio_collection);
  }

  function guardar() {
    SessionHandler()->check_session();
    foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
    $this->model->oculto = 0;
    $this->model->save();
    header("Location: " . URL_APP . "/listaprecio/panel");
  }

  function editar($arg) {
    SessionHandler()->check_session();
    $this->model->listaprecio_id = $arg;
    $this->model->get();

    $listaprecio_collection = Collector()->get('ListaPrecio');
    foreach ($listaprecio_collection as $clave=>$valor) {
      if ($valor->oculto == 1) unset($listaprecio_collection[$clave]);
    }
    
    $this->view->editar($listaprecio_collection, $this->model);
  }

  function ocultar($arg) {
    SessionHandler()->check_session();
    $listaprecio_id = $arg;
    $this->model->listaprecio_id = $listaprecio_id;
    $this->model->get();
    $this->model->oculto = 1;
    $this->model->save();

    $select = "c.cliente_id AS ID";
    $from = "cliente c";
    $where = "c.listaprecio = {$listaprecio_id}";
    $cliente_collection = CollectorCondition()->get('Cliente', $where, 4, $from, $select);
    $cliente_collection = (is_array($cliente_collection) AND !empty($cliente_collection)) ? $cliente_collection : array();

    foreach ($cliente_collection as $clave=>$valor) {
      $cliente_id = $valor['ID'];
      $cm = new Cliente();
      $cm->cliente_id = $cliente_id;
      $cm->get();
      $cm->listaprecio = 1;
      $cm->save();
    }

    header("Location: " . URL_APP . "/listaprecio/panel");
  }
}
?>