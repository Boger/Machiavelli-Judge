<?php 

require_once("../models/map.php");
require_once("../models/user.php");
require_once("../controllers/adjudicationHelpers.php");

class gamecontroller {
	
	// Klassenvariablen
	
	public $mapobject;
	public $users;
	
	function __construct() {
		$this->mapobject = new map();
	}
	
	function newGame() {
		$setup = simplexml_load_file("../configs/initialunits.xml");
		$this->users=array();
		foreach($setup as $mong){
		 	$currentPower = (string)$mong["name"];
			$this->users[$currentPower] = new user($currentPower);
			$this->users[$currentPower]->setInitialUnits();
		}
	}
	
	/* Returns the current map. Current implementation can only return the start map. ToDo: Get map depending on current game status.*/
	
	function getMap() {
		return $this->mapobject->getBasicMap();
	}
	
	function getUsers() {
		return $this->users;
	}
	
	function adjudication() {
		adjudicationHelpers::cuts($this->users);
		adjudicationHelpers::supports($this->users);
		// Repeat adjudicationLoop until all units either have a result, or status "hold", or 10 loops have been executed
		$loopCount = 0;
		$finished = false;
		do {
			$loopCount = adjudicationHelpers::adjudicationLoop($this->users, $loopCount);
			$finished = adjudicationHelpers::loopController($this->users, $loopCount);
		} while ($finished == false && $loopCount < 10);
		adjudicationHelpers::setHoldResults($this->users);
		// Show number of loops
		echo "<br>*** Adjudication Loops = ".$loopCount." ***<br><br>";
	}
	
	/*	function adjudication() {
		adjudicationHelpers::cuts($this->users);
		adjudicationHelpers::supports($this->users);
		// Repeat adjudicationLoop until all units either have a result, or status "hold", or 10 loops have been executed
		$loopCount = 0;
	//	$finished = false;
		do {
			adjudicationHelpers::adjudicationLoop($this->users, $loopCount);
			adjudicationHelpers::loopController($this->users, $loopCount);
		} while ($finished == false && $loopCount < 10);
		adjudicationHelpers::setHoldResults($this->users);
		// Show number of loops
		echo "<br>*** Adjudication Loops = ".$loopCount." ***<br><br>";
	} */
	
	function saveTurnData() {
		foreach ($this->users as $power) {
			foreach ($power->units as $unitData) {
				$unitData->saveTurnData();
			}
		}
	}
}

?>