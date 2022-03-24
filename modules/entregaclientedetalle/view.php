<?php


class EntregaClienteDetalleView extends View {

    function panel($vendedor_collection,$cobrador_collection) {
        $gui = file_get_contents("static/modules/entregaclientedetalle/panel.html");
        $gui_slt_vendedor = file_get_contents("static/modules/entregaclientedetalle/slt_vendedor.html");
        $gui_slt_cobrador = file_get_contents("static/modules/entregaclientedetalle/slt_cobrador.html");

        $user_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
        $user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];

        if ($user_id == 13 OR $user_id == 31) {
          $gui_procesar_cobranza = file_get_contents("static/modules/entregaclientedetalle/procesar_cobranza.html");
          $gui = str_replace('{procesar_cobranza}', $gui_procesar_cobranza, $gui);
          $gui = str_replace('{display-alert-cobrador}', 'inline-block', $gui);
        }else {
          switch ($user_level) {
              case 2:
                  $gui = str_replace('{procesar_cobranza}', '', $gui);
                  $gui = str_replace('{display-alert-cobrador}', 'none', $gui);
                  break;
              default:
                  $gui_procesar_cobranza = file_get_contents("static/modules/entregaclientedetalle/procesar_cobranza.html");
                  $gui = str_replace('{procesar_cobranza}', $gui_procesar_cobranza, $gui);
                  $gui = str_replace('{display-alert-cobrador}', 'inline-block', $gui);
                  break;
          }
        }

        foreach ($vendedor_collection as $key => $value) unset($value->infocontacto_collection);
        $gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
        $gui_slt_cobrador = $this->render_regex('SLT_COBRADOR', $gui_slt_cobrador, $cobrador_collection);

        $render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $gui);
        $render = str_replace('{slt_cobrador}', $gui_slt_cobrador, $render);
        $render = $this->render_breadcrumb($render);
        $template = $this->render_template($render);
        print $template;
    }

  function vendedor_cobranza($entregacliente_collection) {
    $user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
    $user_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

    if ($user_id == 13 OR $user_id == 31) {
      $gui = file_get_contents("static/modules/entregaclientedetalle/tbl_cobranza.html");
    }else {
      switch ($user_level) {
          case 2:
              $gui = file_get_contents("static/modules/entregaclientedetalle/tbl_cobranza_supervisor.html");
              break;
          default:
              $gui = file_get_contents("static/modules/entregaclientedetalle/tbl_cobranza.html");
              break;
      }
    }

    $gui = $this->render_regex_dict('TBL_COBRANZA', $gui, $entregacliente_collection);
    $render = str_replace('{url_app}', URL_APP, $gui);
    print $render;
  }

  function editar_ajax($ingresotipopago_collection, $obj_entregacliente, $obj_entregaclientedetalle) {
    $gui = file_get_contents("static/modules/entregaclientedetalle/editar_ajax.html");
    $gui_slt_ingresotipopago = file_get_contents("static/common/slt_ingresotipopago.html");

    $gui_slt_ingresotipopago = $this->render_regex('SLT_INGRESOTIPOPAGO', $gui_slt_ingresotipopago, $ingresotipopago_collection);
    $obj_entregaclientedetalle->selected_parcial = ($obj_entregaclientedetalle->parcial == 1) ? 'selected' : '';
    $obj_entregaclientedetalle->selected_total = ($obj_entregaclientedetalle->parcial == 1) ? '' : 'selected';
    $obj_entregaclientedetalle->monto = round($obj_entregaclientedetalle->monto, 2);
    $obj_entregacliente = $this->set_dict($obj_entregacliente);
    $obj_entregaclientedetalle = $this->set_dict($obj_entregaclientedetalle);
    $render = $this->render($obj_entregacliente, $gui);
    $render = $this->render($obj_entregaclientedetalle, $render);
    $render = str_replace('{slt_ingresotipopago}', $gui_slt_ingresotipopago, $render);
    $render = str_replace('{url_app}', URL_APP, $render);
    print $render;
  }

  function panel_vendedor_cobranza($vendedor_collection,$cobrador_collection, $entregacliente_collection, $total_cobranza, $vendedor_id) {
    $gui = file_get_contents("static/modules/entregaclientedetalle/panel_vendedor_cobranza.html");
    $gui_slt_vendedor = file_get_contents("static/modules/entregaclientedetalle/slt_vendedor.html");
    $gui_slt_cobrador = file_get_contents("static/modules/entregaclientedetalle/slt_cobrador.html");

    $user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
    switch ($user_level) {
        case 2:
            $gui_tbl_entregacliente = file_get_contents("static/modules/entregaclientedetalle/tbl_cobranza_supervisor.html");
            $gui = str_replace('{procesar_cobranza}', '', $gui);
            $gui = str_replace('{display-alert-cobrador}', 'none', $gui);
            break;
        default:
            $gui_tbl_entregacliente = file_get_contents("static/modules/entregaclientedetalle/tbl_cobranza.html");
            $gui_procesar_cobranza = file_get_contents("static/modules/entregaclientedetalle/procesar_cobranza.html");

            $gui = str_replace('{procesar_cobranza}', $gui_procesar_cobranza, $gui);
            $gui = str_replace('{display-alert-cobrador}', 'inline-block', $gui);
            break;
    }

    foreach ($vendedor_collection as $key => $value) unset($value->infocontacto_collection);
    $gui_slt_vendedor = $this->render_regex('SLT_VENDEDOR', $gui_slt_vendedor, $vendedor_collection);
    $gui_slt_cobrador = $this->render_regex('SLT_COBRADOR', $gui_slt_cobrador, $cobrador_collection);
    $gui_tbl_entregacliente = $this->render_regex_dict('TBL_COBRANZA', $gui_tbl_entregacliente, $entregacliente_collection);

    $render = str_replace('{slt_vendedor}', $gui_slt_vendedor, $gui);
    $render = str_replace('{slt_cobrador}', $gui_slt_cobrador, $render);
    $render = str_replace('{tbl_entregacliente}', $gui_tbl_entregacliente, $render);
    $render = str_replace('{total_cobranza}', $total_cobranza, $render);
    $render = str_replace('{vendedor-vendedor_id}', $vendedor_id, $render);
    $render = $this->render_breadcrumb($render);
    $template = $this->render_template($render);
    print $template;
  }
}
?>
