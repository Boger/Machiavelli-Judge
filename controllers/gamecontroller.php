<?php 

require_once("../models/map.php");
require_once("../models/user.php");

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
		 	$currentpower = (string)$mong["name"];
			$this->users[$currentpower] = new user($currentpower);
			$this->users[$currentpower]->setInitialUnits();
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
	
	/* Check for all supporting units ($unitOrder) if they are being cut by units not belonging to the same power. If so, set order status to "cut". */
						
		foreach ($this->users as $powerOrders) {
			foreach ($powerOrders->units as $supportOrder) {
				foreach ($this->users as $allPotentialCuts) {
					foreach ($allPotentialCuts->units as $cut) {
						if ($supportOrder->order->orderType == "support" && $cut->order->orderType == "move") {
							if ($cut->order->moveDestination == $supportOrder->region && $allPotentialCuts->power != $powerOrders->power)
								$supportOrder->order->status = "cut";
						}
					}
				}
			}
		}
		
	/* Check for all units ($unitOrder) if they are being supported by other units ($supportOrder). If so, increase strength by 1. */
		
		foreach ($this->users as $powerOrders) {
			foreach ($powerOrders->units as $unitOrder) {
				foreach ($this->users as $allPotentialSupports) {
					foreach ($allPotentialSupports->units as $supportOrder) {
						if ($supportOrder->order->orderType == "support" && $supportOrder->order->supportUnit == $unitOrder->region && $supportOrder->order->status != "cut") {
							if ($unitOrder->order->orderClass() == "move") {
								if ($unitOrder->order->moveDestination == $supportOrder->order->supportDestination)
										$unitOrder->order->strength++;
							}
							elseif ($unitOrder->order->orderClass() == "hold") {
								if ($supportOrder->order->supportDestination = "H")
										$unitOrder->order->strength++;
							}
						}
					}
				}
			}
		}


	/* Create a combat list for each region. The combat list contains all units trying to occupy the region (by holding it, moving into it, or failing to move out of it). */

		foreach ($this->getMap() as $region) {
			$finished = 1;
			$combatlist = array();
			foreach ($this->users as $power) {
				foreach ($power->units as $onePowersUnits) {
					if ($onePowersUnits->order->status == "unprocessed") {	
						// Units trying to move into the region:
						if ($onePowersUnits->order->orderType == "move") {
							if ($onePowersUnits->order->moveDestination == 	$region['abbr']) {
								array_push($combatlist, $onePowersUnits);
								$finished = 0;
							}						
						}
						// Units trying to hold the region:
						elseif ($onePowersUnits->order->orderClass() == "hold") {
							if ($onePowersUnits->region == $region['abbr']) {
								array_push($combatlist, $onePowersUnits);
								$finished = 0;
							}
						}
						// TODO: Units failing to move out of the region:
						elseif ($onePowersUnits->order->orderType == "move") {
							if ($onePowersUnits->order->moveDestination == 	$region['abbr']) {
								array_push($combatlist, $onePowersUnits);
								$finished = 0;
							}						
						}
					}
				}
			}
	
	// Sort the combat list by strength.
		
			usort($combatlist, function($a, $b) {
    			if ($a->order->strength == $b->order->strength) {
       				return 0;
    			}
    			return ($a->order->strength > $b->order->strength) ? -1 : 1;
    		});
	
	// If there is a winner, set statuses accordingly.
	
			if ($combatlist[0]->order->strength > $combatlist[1]->order->strength) {
				foreach ($combatlist as $key => $value) {
					if ($key == 0 && $value->order->orderClass() == "hold")
						$value->order->status = "hold";
					elseif ($key == 0 && $value->order->orderClass() == "move")
						$value->order->status = "move";
					elseif ($value->order->orderClass() == "hold")
						$value->order->status = "dislodged";
					elseif ($value->order->orderClass() == "move")
						$value->order->status = "defeated";
				}
			}
		
	// If there is a tie, set statuses accordingly.
		
			else {
				foreach ($combatlist as $value) {
					if ($value->order->orderClass() == "hold")
						$value->order->status = "hold";
					elseif ($value->order->orderClass() == "move" && 				$value->order < $combatlist[0]->order->strength)
						$value->order->status = "defeated";
					elseif ($value->order->orderClass() == "move" && 				$value->order == $combatlist[0]->order->strength)
						$value->order->status = "bounce";
					}
			}
		
		// TODO: Include two units moving to each other's region.
		
		print_r ($combatlist);
		echo "<br>";
					
		}
		
		echo "<br>"."<br>"."<br>";
	}
	

}

?>
