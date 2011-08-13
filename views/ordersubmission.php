<?php 


require_once("../controllers/gamecontroller.php");

$gamecontrollerobject = new gamecontroller();
$gamecontrollerobject->newGame();



?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8">

$(document).ready(function() {
  $('select#orderType').change(function() {
    var cat = $(this).val();    
    if(cat != '') {
    	alert("region:" + $(this).text());
        $.get("getMoveDestinations.php",{region: OrderType}, function(data){
            $("select#moveDestination").html(data);
            
        });
    }
});
}); 
</script>


<form action="/submitOrders.php">
<?php 


foreach ($gamecontrollerobject->users as $power) {
 echo "<br><br>".$power->power."<br><br>";
	foreach ($power->units as $unit) {
		echo $unit->type." ".$unit->region;

echo <<<DELIMITER

  <select name="id" id="orderType">
    <option value="1">Moves to</option>
    <option value="2">Converts to</option>
    <option value="3">Supports</option>
    <option value="4">Besieges</option>
    <option value="5">Convoys</option>
    <option value="6">Holds</option>
  </select>

  <select name="moveDestination" id="moveDestination" type="
  DELIMITER;
  echo $unit->region;
echo <<<DELIMITER
  ">
  	
  </select>


<br>
DELIMITER;
	}
}
echo "<input type='submit' name='action' value='Submit' />";
echo "</form>";
?>




<?php 

?>