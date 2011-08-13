<?php

abstract class order
{
	// Klassenvariablen

	public $orderType;	// move || convert || hold || support || besiege || convoy
	
	function orderClass() {
		 return ($this->orderType == "move" || $this->orderType == "convert") ? "move" : "hold";
	}
}

class orderMove extends order
{
	// Klassenvariablen

	public $moveDestination;
	
	function __construct($a) {
		$this->moveDestination = $a;
		$this->orderType = "move";
		$this->orderClass = "move";
	}
	
	function orderSyntax() {
		return " - ".$this->moveDestination;
	}
}

class orderConvert extends order
{
	// Klassenvariablen

	public $convertType;
	
	function __construct($a) {
		$this->convertType = $a;
		$this->orderType = "convert";
		$this->orderClass = "move";
	}

	function orderSyntax() {
		return " = ".$this->convertType;
	}
}
	
class orderHold extends order
{	
	function __construct() {
		$this->orderType = "hold";
		$this->orderClass = "hold";
	}

	function orderSyntax() {
		return " H";
	}
}

class orderSupport extends order
{
	// Klassenvariablen

	public $supportTarget;		// The unit to be supported.
	public $supportClass;		// The class of the supported order.
	public $supportDestination;	// The destination of the supported move order.
	
	function __construct($a, $b, $c = null) {
		$this->supportTarget = $a;
		$this->supportClass = $b;
		$this->supportDestination = $c;
		$this->orderType = "support";
		$this->orderClass = "hold";
	}

	function orderSyntax() {
		if ($this->supportClass == "hold")
			return " S ".$this->supportTarget." H";
		elseif ($this->supportClass == "move") // Conversion muß hier noch rein
			return " S ".$this->supportTarget." - ".$this->supportDestination;
	}
			
}
	
class orderBesiege extends order
{
	function __construct() {
		$this->orderType = "besiege";
		$this->orderClass = "hold";
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
	
	function __construct($a, $b) {
		$this->convoyUnit = $a;
		$this->convoyDestination = $b;
		$this->orderType = "convoy";
		$this->orderClass = "hold";
	}

	function orderSyntax(){
		return " C ".$this->convoyUnit." - ".$this->convoyDestination;
	}
}

?>

