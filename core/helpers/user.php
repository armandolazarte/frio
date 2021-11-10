<?php
class User {
	static function get_usuariodetalle_id($hash) {
	    $sql = "SELECT 
	    			usuariodetalle_id
	    		FROM 
	    			usuariodetalle 
	    		WHERE 
	    			token = ?";
	    $datos = array($hash);
        $result = execute_query($sql, $datos);
        $usuariodetalle_id = (is_array($result) AND !empty($result)) ? $result[0]['usuariodetalle_id'] : 0;
		return $usuariodetalle_id;
	}

	static function get_usuario_id($usuariodetalle_id) {
	    $sql = "SELECT 
	    			usuario_id
	    		FROM 
	    			usuario 
	    		WHERE 
	    			usuariodetalle = ?";
	    $datos = array($usuariodetalle_id);
        $result = execute_query($sql, $datos);
        $usuario_id = (is_array($result) AND !empty($result)) ? $result[0]['usuario_id'] : 0;
		return $usuario_id;
	}
}

function User() {return new User();}
?>