<?php
require_once "modules/configuracion/model.php";
require_once "modules/configuracion/view.php";
require_once "modules/configuracioncomprobante/model.php";
require_once "modules/vendedor/model.php";


class ConfiguracionController {

	function __construct() {
		$this->model = new Configuracion();
		$this->view = new ConfiguracionView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	require_once "core/helpers/file.php";
    	$this->model->configuracion_id = 1;
    	$this->model->get();

    	$ccm = new ConfiguracionComprobante();
    	$ccm->configuracioncomprobante_id = 1;
    	$ccm->get();

    	$vendedor_collection = Collector()->get('Vendedor');
    	$this->view->panel($vendedor_collection, $this->model, $ccm);
	}

	function definir_entidad() {
		SessionHandler()->check_session();
		$this->model->configuracion_id = filter_input(INPUT_POST, "configuracion_id");
		$this->model->get();
		$this->model->entidad = filter_input(INPUT_POST, "entidad");
		$this->model->save();
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function definir_parametros_facturacion() {
		SessionHandler()->check_session();
		$dias_vencimiento_cuentacorrientecliente = filter_input(INPUT_POST, 'dias_vencimiento_cuentacorrientecliente');
		$dias_vencimiento_cuentacorrientecliente = (is_null($dias_vencimiento_cuentacorrientecliente)) ? 0 : 1;
		
		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();
		$ccm->dias_alerta_comision = filter_input(INPUT_POST, "dias_alerta_comision");
		$ccm->dias_vencimiento = filter_input(INPUT_POST, "dias_vencimiento");
		$ccm->dias_vencimiento_cuentacorrientecliente = $dias_vencimiento_cuentacorrientecliente;
		$ccm->save();
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function definir_parametros_codebar() {
		SessionHandler()->check_session();
		$facturacion_rapida = filter_input(INPUT_POST, 'facturacion_rapida');
		$facturacion_rapida = (is_null($facturacion_rapida)) ? 0 : 1;

		$ccm = new ConfiguracionComprobante();
		$ccm->configuracioncomprobante_id = 1;
		$ccm->get();		
		$ccm->facturacion_rapida = $facturacion_rapida;
		$ccm->parteuno_codebar = filter_input(INPUT_POST, "parteuno_codebar");
		$ccm->separador_codebar = filter_input(INPUT_POST, "separador_codebar");
		$ccm->partedos_codebar = filter_input(INPUT_POST, "partedos_codebar");
		$ccm->save();		
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function configurar_dias_vencimiento_cta_cte_cliente() {
		SessionHandler()->check_session();
		$dias_vencimiento_cta_cte_cliente = filter_input(INPUT_POST, 'dias_vencimiento_cta_cte_cliente');
		$this->model->configurar_dias_vencimiento_cta_cte_cliente($dias_vencimiento_cta_cte_cliente);
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function configurar_dias_vencimiento_cta_cte_cliente_vendedor() {
		SessionHandler()->check_session();
		$dias_vencimiento_cta_cte_cliente = filter_input(INPUT_POST, 'dias_vencimiento_cta_cte_cliente');
		$vendedor_id = filter_input(INPUT_POST, 'vendedor');
		$this->model->configurar_dias_vencimiento_cta_cte_cliente_vendedor($dias_vencimiento_cta_cte_cliente, $vendedor_id);
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function definir_logo() {
		SessionHandler()->check_session();

		$directorio = URL_PRIVATE . "configuracion/logo/";
		
		$archivo = $_FILES["logo"]["tmp_name"];
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($archivo);
		$formato = explode("/", $mime);
		$mimes_permitidos = array("image/jpeg");
		
		$name = "logo";
		if(in_array($mime, $mimes_permitidos)) move_uploaded_file($archivo, "{$directorio}/{$name}"); 
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function guardar() {
		SessionHandler()->check_session();
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/configuracion/panel");
	}
}
?>