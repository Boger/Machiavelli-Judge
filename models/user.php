<?php

require_once("../models/unit.php");

class user
{
	//Klassenvariablen

	public $power;
	public $units;
	
	public function __construct($horst) {
		$this->power = $horst;
	}
	
	public function setInitialUnits() {
		
		$setup = simplexml_load_file("../configs/initialunits.xml");

		$this->units = array();

		foreach($setup as $mong){
			if ((string)$mong["name"] == $this->power){
				$initialunits = $mong;
			}
		}
		
		foreach($initialunits as $mong){
			$unit = new unit((string)$mong["type"], (string)$mong["region"]);
			array_push($this->units, $unit);
			}
	}
}

?>
