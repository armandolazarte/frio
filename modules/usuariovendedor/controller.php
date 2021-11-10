<?php
require_once "modules/usuariovendedor/model.php";
require_once "modules/usuariovendedor/view.php";


class UsuarioVendedorController {

	function __construct() {
		$this->model = new UsuarioVendedor();
		$this->view = new UsuarioVendedorView();
	}
}
?>