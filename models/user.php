<?php

require_once("../models/unit.php");

class user {

	// Klassenvariablen

	public $power;
	public $units;
	
	public function __construct($power) {
		$this->power = $power;
	}
	
	public function setInitialUnits() {
		
		$setup = simplexml_load_file("../configs/initialunits.xml");

		$this->units = array();

		foreach($setup as $powerName){
			if ((string)$powerName["name"] == $this->power){
				$initialunits = $powerName;
			}
		}
		
		foreach($initialunits as $unitTypeAndRegion){
			$unit = new unit((string)$unitTypeAndRegion["type"], (string)$unitTypeAndRegion["region"]);
			array_push($this->units, $unit);
			}
	}
}

?>
