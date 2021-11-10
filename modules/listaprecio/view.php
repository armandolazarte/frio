<?php


class ListaPrecioView extends View {

	function panel($listaprecio_collection) {
		$gui = file_get_contents("static/modules/listaprecio/panel.html");

		$render = $this->render_regex('TBL_LISTAPRECIO', $gui, $listaprecio_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($listaprecio_collection, $obj_listaprecio) {
		$gui = file_get_contents("static/modules/listaprecio/editar.html");
		$obj_listaprecio = $this->set_dict($obj_listaprecio);
		$render = $this->render_regex('TBL_LISTAPRECIO', $gui, $listaprecio_collection);
		$render = $this->render($obj_listaprecio, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>
