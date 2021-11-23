<?php
require_once "modules/almacen/model.php";
require_once "modules/usuariodetalle/model.php";
require_once "modules/configuracionmenu/model.php";


class Usuario extends StandardObject {
	
	function __construct(Almacen $almacen=NULL, UsuarioDetalle $detalle=NULL, ConfiguracionMenu $configuracionmenu=NULL) {
		$this->usuario_id = 0;
		$this->denominacion = '';
		$this->nivel = 0;
		$this->almacen = $almacen;
		$this->usuariodetalle = $detalle;
		$this->configuracionmenu = $configuracionmenu;
	}
}
?>