<?php

require_once("../models/order.php");

class unit
{

	//Klassenvariablen

	public $type;
	public $region;
	public $order;
	
	function __construct($type, $region) {
		$this->type = $type;
		$this->region = $region;
		
	}

	public function getOrderString(){
	
		if (isset($this->order))
			return $this->type." ".$this->region." ".$this->order->orderSyntax();
			
		else 
			return "NOR";
	}
	
	
}

?>
