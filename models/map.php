<?php

class map
{

	
	function __construct() {
		$this->basicmap = simplexml_load_file("../configs/map.xml");
	}
	
	
	public function getBasicMap() {
		return $this->basicmap;
	}

}

?>
