<?php

abstract class order
{
	// Klassenvariablen

	public $status;
	public $strength;
	public $orderType;
	
	function initializeValues() {
		$this->strength = 1;
		$this->status = "unprocessed";
	}
	
	function orderClass() {
		 return ($this->orderType == "move") ? "move" : "hold";
	}
}

class orderMove extends order
{
	// Klassenvariablen

	public $moveDestination;
	
	function __construct($horst) {
		$this->moveDestination = $horst;
		$this->orderType = "move";
		$this->initializeValues();
	}
	
	function orderSyntax() {
		return " - ".$this->moveDestination;
	}
}

class orderConvert extends order
{
	// Klassenvariablen

	public $convertType;
	
	function __construct($horst) {
		$this->convertType = $horst;
		$this->orderType = "convert";
		$this->initializeValues();
	}

	function orderSyntax() {
		return " = ".$this->convertType;
	}
}
	
class orderHold extends order
{	
	function __construct() {
		$this->orderType = "hold";
		$this->initializeValues();
	}

	function orderSyntax() {
		return " H";
	}
}

class orderSupport extends order
{
	// Klassenvariablen

	public $supportUnit;
	public $supportDestination;
	
	function __construct($horst, $muschi) {
		$this->supportUnit = $horst;
		$this->supportDestination = $muschi;
		$this->orderType = "support";
		$this->initializeValues();
	}

	function orderSyntax() {
		if ($this->supportDestination == "H")
			return " S ".$this->supportUnit." ".$this->supportDestination;
		else return " S ".$this->supportUnit." - ".$this->supportDestination;
	}
			
}
	
class orderBesiege extends order
{
	function __construct() {
		$this->orderType = "besiege";
		$this->initializeValues();
	}

	function orderSyntax(){
		return " B";
	}
}

class orderConvoy extends order
{
	// Klassenvariablen

	public $convoyUnit;
	public $convoyDestination;
	
	function __construct($horst, $muschi) {
		$this->convoyUnit = $horst;
		$this->convoyDestination = $muschi;
		$this->orderType = "convoy";
		$this->initializeValues();
	}

	function orderSyntax(){
		return " C ".$this->convoyUnit." - ".$this->convoyDestination;
	}
}

?>

