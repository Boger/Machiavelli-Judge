<?php 

class adjudicationHelpers {

	function cuts($users) {
	
	/* Check for all supporting units ($supporter) if they are being cut. A unit is cut if all of the following is true:
	
	(1) Any unit's moveDestination is region.
	(2) The cutting unit does not belong to the same power.
	(3) The cutting unit's region is not supportDestination.
	
	If so, set comment to "cut". */
						
		foreach ($users as $powerOrders) {
			foreach ($powerOrders->units as $supporter) {
				foreach ($users as $allPotentialCuts) {
					foreach ($allPotentialCuts->units as $cuttingUnit) {
						if (
							// (1)
							$supporter->order->orderType == "support" && 
							$cuttingUnit->order->orderType == "move" &&
							$cuttingUnit->order->moveDestination == $supporter->region &&
							// (2)
							$allPotentialCuts->power != $powerOrders->power &&
							// (3)
							$cuttingUnit->region != $supporter->order->supportDestination
							)
								$supporter->comment = "cut";
					}
				}
			}
		}
	}
	
	function supports($users) {

	/* Check for all units ($receiver) if they are being supported by other units ($supporter). A unit is being supported if all of the following is true:

	(1) Any unit's supportTarget is receiver's region.
	(2) The supporter's comment is not "cut".
	(3) The supporter's supportClass is receiver's orderClass.
	(4) The supporter's supportDestination (if any) is receiver's moveDestination.

	If so, increase strength by 1. 
	If (3) or (4) is false, set supporter's comment to "void". Only uncut supporters can get status "void", i.e. "cut" overrides "void" (for better or for worse). */
		
		foreach ($users as $powerOrders) {
			foreach ($powerOrders->units as $receiver) {
				foreach ($users as $allPotentialSupports) {
					foreach ($allPotentialSupports->units as $supporter) {
						if (
							// (1)
							$supporter->order->orderType == "support" &&
							$supporter->order->supportTarget == $receiver->region &&
							// (2)
							$supporter->comment != "cut"
							)
							// (3)
							if ($supporter->order->supportClass == $receiver->order->orderClass()) {
								// (4)
								if (
									$receiver->order->orderClass() == "move" &&
									$supporter->order->supportDestination == $receiver->order->moveDestination
									)
										$receiver->strength++;
								elseif ($receiver->order->orderClass() == "hold")
									$receiver->strength++;
								// Set uncut supports to "void" if destinations don't match
								elseif (
									$receiver->order->orderClass() == "move" &&
									$supporter->order->supportDestination != 	$receiver->order->moveDestination
									)
										$supporter->comment = "void";
							// Set uncut supports to "void" if supportClass is not orderClass
							}
							elseif ($supporter->order->supportClass != $receiver->order->orderClass())
								$supporter->comment = "void";
					}
				}
			}
		}
	}
	
	function adjudicationLoop($users, $loopCount) {
		foreach ($this->users as $power) {
			foreach ($power->units as $onePowersUnit) {
				// Unprocessed holding units get status "hold"
				if (
				$onePowersUnit->status == "null"
				&& $onePowersUnit->order->orderClass() == "hold"
				)
					$onePowersUnit->status = "hold";
				// Holding units which have been dislodged get result "dislodged"
				if (
				$onePowersUnit->result == "null" // ist das notwendig? schon oder?
				&& $onePowersUnit->status == "dislodged"
				&& $onePowersUnit->order->orderClass() == "hold"
				)
					$onePowersUnit->result = "dislodged";
				// Moving units try to move (whether or not they have been dislodged)
				if (
				$onePowersUnit->result == "null"
				&& $onePowersUnit->order->orderClass() == "move"
				&& ($onePowersUnit->status == "null" || $onePowersUnit->status == "dislodged")
				)
					adjudicationHelpers::attack($onePowersUnit, $this->users);
			}
		}
		$loopCount++;
		return $loopCount;
	}
	
	function loopController($users, $loopCount) {
		$finished = true;
		// Do all units either have a result, or status "hold"?
		foreach ($this->users as $power) {
			foreach ($power->units as $onePowersUnit) {
				if ($onePowersUnit->result == "null" && $onePowersUnit->status != "hold")
					$finished = false;
			}
		}
		// Or if 10 loops have been executed to prevent infinite loops
		if ($loopCount == 10)
			$finished = true;
		return $finished;
	}
	
	function getStrongestCompetitor($users, $attacker) {
		$combatlist = array();
		// Add all units having the same moveDestination to combatlist
		foreach ($users as $powerOrders) {
			foreach ($powerOrders->units as $competitor) {
				if (
					$attacker->order->moveDestination == $competitor->order->moveDestination &&
					$attacker != $competitor &&
					isset ($competitor->order->moveDestination)
					)
					array_push($combatlist, $competitor);
			}
		}
		// Sort the combatlist by strength.
		usort($combatlist, function($a, $b) {
			if ($a->strength == $b->strength) {
				return 0;
    		}
    			return ($a->strength > $b->strength) ? -1 : 1;
    	});
		return $combatlist[0];
		// Note: Returns only one even if there are two or more strongest competitors
	}
	
	function getTarget($users, $attacker) {
		foreach ($users as $powerOrders) {
			foreach ($powerOrders->units as $target) {
				if ($attacker->order->moveDestination == $target->region)
					return $target;
			}
		}
	}
	
	function attack($attacker, $users) {
		$strongestCompetitor = adjudicationHelpers::getStrongestCompetitor($users, $attacker);
		$target = adjudicationHelpers::getTarget($users, $attacker);
		
		// SET #1 - Attacker is stronger (or target moves elsewhere)
		
		// Is attacker stronger than anyone moving to same region?
		if (
			$attacker->strength > $strongestCompetitor->strength
			) 
			$defeatCompetitors = true;
		else $defeatCompetitors = false;
		// Is attacker stronger than holding target (if there is a target)?
		if (
			(isset($target) &&
			($target->order->orderClass() == "hold" ||
			$target->status == "hold") &&
			$attacker->strength > $target->strength) ||
			// or there is no target
			!isset($target)
			)
			$defeatHoldingTarget = true;
		else $defeatHoldingTarget = false;
		// Is attacker stronger than target moving to attacker's region?
		if (
			$attacker->region == $target->order->moveDestination &&
			//!isset($target->order->moveDestination)) && // really? lieber unten if1&&(if2||if3)?
			$attacker->strength > $target->strength
			)
			$defeatAttackingTarget = true;
		else $defeatAttackingTarget = false;
		// Has target already moved elsewhere?
		if (
			$target->result == "move" &&
			$target->order->moveDestination != $attacker->region
			) 
			$targetMovesElsewhere = true;
			else $targetMovesElsewhere = false;
		
		// Consequences #1
		
		// Attacker and target do not belong to the same power or there is no target
		if ($attacker->power != $target->power || !isset($target)) {
			// Attacker defeats all OR attacker defeats competitors and target moved elsewhere
			if (
				($defeatCompetitors &&
				($defeatHoldingTarget || $defeatAttackingTarget)) || 
				($defeatCompetitors && $targetMovesElsewhere)
				) {
					$attacker->result = "move";
			}
			// Attacker defeats all, target hasn't moved elsewhere yet
			if (
				$defeatCompetitors && 
				$defeatHoldingTarget && 
				$defeatAttackingTarget &&
				$targetMovesElsewhere == false
				) {
					$attacker->result = "move";
					$target->status = "dislodged";
			}		
			// Target is defeated --- nicht nötig weil kriegt er wenn er dran ist??
			if ($defeatAttackingTarget) {
					$target->comment = "defeated";
			}
			// Target is dislodged --- Muß sein!
			if ($defeatHoldingTarget || $defeatAttackingTarget) {
					$target->result = "dislodged";
			}
		}
		// Attacker and target belong to the same power
		elseif ($attacker->power == $target->power && isset($target)) {
			// Attacker defeats all
			if (
				$defeatCompetitors &&
				($defeatHoldingTarget || $defeatAttackingTarget) &&
				$targetMovesElsewhere == false
				) {
					$attacker->status = "hold";
					$attacker->comment = "bounce";
			}
			// Attacker defeats competitors and target moved elsewhere
			if ($defeatCompetitors && $targetMovesElsewhere) {
					$attacker->result = "move";
			}
			// Target is defeated but isn't dislodged
			if ($defeatAttackingTarget) {
					$target->comment = "bounce";
			}
		}
		
		// SET #2 - Attacker is equally strong
		
		// Is attacker equal to strongest competitor?
		if ($attacker->strength == $strongestCompetitor->strength) 
			$bounceCompetitors = true;
		else $bounceCompetitors = false;
		// Is attacker equal to holding target?
		if (
			isset($target) &&
			($target->order->orderClass == "hold" ||
			$target->status == "hold") && // function orderClass funzt hier nicht!?!
			$attacker->strength == $target->strength
			)
			$bounceHoldingTarget = true;
		else $bounceHoldingTarget = false;
		// Is attacker equal to target moving to attacker's region?
		if (
			($attacker->region == $target->order->moveDestination || !isset($target->order->moveDestination)) &&
			$attacker->strength == $target->strength
			)
			$bounceAttackingTarget = true;
		else $bounceAttackingTarget = false;
		
		// Consequences #2
		
		// Attacker bounces but hasn't yet been dislodged
		if (
			($bounceCompetitors || $bounceHoldingTarget || $bounceAttackingTarget) &&
			$attacker->status != "dislodged"
			) {
			$attacker->status = "hold";
			//$attacker->strength = "0.5";
			$attacker->comment = "bounce";
		}
		// Attacker bounces and has already been dislodged
		if (
			($bounceCompetitors || $bounceHoldingTarget || $bounceAttackingTarget) &&
			$attacker->status == "dislodged"
			) {
			$attacker->result = "dislodged";
			$attacker->comment = "bounce";
		}
		
		// SET #3 - Attacker is weaker
		
		// Is attacker weaker than strongest competitor?
		if ($attacker->strength < $strongestCompetitor->strength) 
			$loseToCompetitors = true;
		else $loseToCompetitors = false;
		// Is attacker weaker than holding target?
		if (
			isset($target) &&
			($target->order->orderClass == "hold" ||
			$target->status == "hold") && // function orderClass funzt hier nicht!?!
			$attacker->strength < $target->strength
			)
			$loseToHoldingTarget = true;
		else $loseToHoldingTarget = false;
		// Is attacker weaker than target moving to attacker's region?
		if (
			($attacker->region == $target->order->moveDestination || !isset($target->order->moveDestination)) &&
			$attacker->strength < $target->strength
			)
			$loseToAttackingTarget = true;
		else $loseToAttackingTarget = false;
		
		// Consequences #3
		
		// Attacker is defeated by competitor or attacking target but hasn't yet been dislodged
		if (($loseToCompetitors || $loseToAttackingTarget) && $attacker->status != "dislodged") {
			$attacker->status = "hold";
			//$attacker->strength = "1"; // Das muß noch getestet werden!
			$attacker->comment = "defeated";
		}
		// Attacker is defeated by competitor or attacking target and has already been dislodged
		if (($loseToCompetitors || $loseToAttackingTarget) && $attacker->status == "dislodged") {
			$attacker->result = "dislodged";
			$attacker->comment = "defeated"; // Das funktioniert noch nicht ganz ohne Zeile 205
		}
		// Attacker is defeated by holding target ("bounces") but hasn't yet been dislodged
		if ($loseToHoldingTarget && $attacker->status != "dislodged") {
			$attacker->status = "hold";
			//$attacker->strength = "1"; // Das muß noch getestet werden!
			$attacker->comment = "bounce";
		}
		// Attacker is defeated by holding target ("bounces") and has already been dislodged
		if ($loseToHoldingTarget && $attacker->status == "dislodged") {
			$attacker->result = "dislodged";
			$attacker->comment = "bounce";
		}
	
	/*	Boolean Attack States:
		
		$targetMovesElsewhere
		$defeatCompetitors
		$defeatHoldingTarget
		$defeatAttackingTarget
		$bounceCompetitors
		$bounceHoldingTarget
		$bounceAttackingTarget
		$loseToCompetitors
		$loseToHoldingTarget
		$loseToAttackingTarget	*/
		
	/* Test-Display
	
	echo "<br>------------------".$attacker->region." dislodges ".$target->region."<br>";
	echo "<br>".$attacker->region." - ".$target->region."<br>defeatCompetitors: ".$defeatCompetitors."<br>defeatHoldingTarget: ".$defeatHoldingTarget."<br>defeatAttackingTarget: ".$defeatAttackingTarget."<br>targetMovesElsewhere: ".$targetMovesElsewhere."<br>Comment: ".$attacker->comment."<br>Status: ".$attacker->status."<br>Result: ".$attacker->result."<br>";
	*/
	
	}
	
	function setHoldResults($users) {
		// Set all "null"-results to "hold" if status "hold"
		foreach ($users as $power) {
			foreach ($power->units as $unit) {
				if ($unit->result == "null" && $unit->status == "hold")
					$unit->result = "hold";
			}
		}
	}
}

?>














