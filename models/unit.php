<?php

require_once("../models/order.php");

class unit
{

	// Klassenvariablen		// Mögliche Werte

	public $type; 			// A || F || G
	public $region;			// One of 63 land regions, 10 sea regions (36 city regions)
	public $order;			// Type && Region && Order Syntax
	public $finalType;		// A || F || G
	public $finalRegion;	// One of 63 land regions, 10 sea regions (36 city regions)
	public $strength;		// [Numeric]
	public $status;			// null || hold || dislodged
	public $result;			// null || move || hold || dislodged
	public $comment;		// null || cut || void || bounce || standoff || defeated
	
	function __construct($type, $region) {
		$this->type = $type;
		$this->region = $region;
		$this->strength = 1;
		$this->status = "null";
		$this->result = "null";
		$this->comment = "null";
	}

	function getOrderString(){
	
		if (isset($this->order))
			return $this->type." ".$this->region." ".$this->order->orderSyntax();
		else 
			return "NOR";
	}
	
	function saveTurnData() {
	
	$mysql = mysql_connect('localhost', 'root', ''); 
	mysql_select_db('Machiavelli');
    $result = mysql_query("INSERT INTO TurnData (Type,Region,Order,Comment,Result) VALUES ('$this->type','$this->region','xy','$this->comment','$this->result');");
    mysql_close();
    
    }
	
}

?>
