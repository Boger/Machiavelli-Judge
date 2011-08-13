<?php
	require_once("../controllers/gamecontroller.php");
    
    $moveDestFinder = new gamecontroller();
	$moveDestFinder->newGame();
    
    $region = $_REQUEST['region'];

    foreach ($moveDestFinder->getMap() as $value) {
    	if ($value['abbr'] == $region) {
    		foreach ($value->alr as $alr) {
       			$html .= '<option value="'.$alr.'">'.$alr.'</option>';
       		}
    	}
    }	
    echo $html;

?> 