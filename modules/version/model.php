<?php


class Version extends StandardObject {
	
	function __construct() {
		$this->version_id = 0;
                $this->version = '';                
		$this->changelog = '';
		$this->archivo = '';
                $this->fecha = '';
                $this->activa = 0;
	}
}
?>