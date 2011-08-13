<?php 

require_once("../controllers/gamecontroller.php");

echo '<html><head><title>Machiavelli</title><link rel="stylesheet" href="styles.css"></head><body><br><br><font size="5"><b>Machiavelli Adjudication Page</b></font size><br><br><br><br>';

$gamecontrollerobject = new gamecontroller();
$gamecontrollerobject->newGame();

// Test Orders

// Circular Movement (6.C)
$gamecontrollerobject->users['France']->units[0]->order = new orderMove("SWI");
$gamecontrollerobject->users['France']->units[1]->order = new orderMove("MAR");
$gamecontrollerobject->users['France']->units[2]->order = new orderMove("AVI");

// No self-dislodgement
$gamecontrollerobject->users['France']->units[3]->order = new orderHold();
$gamecontrollerobject->users['France']->units[4]->order = new orderMove("PRO");
$gamecontrollerobject->users['France']->units[5]->order = new orderSupport("GOL","move","PRO");

// No self-dislodgement, target moves elsewhere
$gamecontrollerobject->users['Austria']->units[0]->order = new orderMove("HUN");
$gamecontrollerobject->users['Austria']->units[1]->order = new orderSupport("TYR","move","HUN");
$gamecontrollerobject->users['Austria']->units[2]->order = new orderMove("SLA");

$gamecontrollerobject->users['Austria']->units[3]->order = new orderHold();
$gamecontrollerobject->users['Austria']->units[4]->order = new orderHold();
$gamecontrollerobject->users['Austria']->units[5]->order = new orderHold();


$gamecontrollerobject->users['Milan']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Milan']->units[1]->order = new orderHold();
$gamecontrollerobject->users['Milan']->units[2]->order = new orderHold();
$gamecontrollerobject->users['Milan']->units[3]->order = new orderHold();
$gamecontrollerobject->users['Milan']->units[4]->order = new orderHold();

$gamecontrollerobject->users['Venice']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Venice']->units[1]->order = new orderHold();
$gamecontrollerobject->users['Venice']->units[2]->order = new orderHold();
$gamecontrollerobject->users['Venice']->units[3]->order = new orderHold();
$gamecontrollerobject->users['Venice']->units[4]->order = new orderHold();

$gamecontrollerobject->users['Florence']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Florence']->units[1]->order = new orderHold();
$gamecontrollerobject->users['Florence']->units[2]->order = new orderHold();
$gamecontrollerobject->users['Florence']->units[3]->order = new orderHold();
$gamecontrollerobject->users['Florence']->units[4]->order = new orderHold();

$gamecontrollerobject->users['Papacy']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Papacy']->units[1]->order = new orderHold();
$gamecontrollerobject->users['Papacy']->units[2]->order = new orderHold();
$gamecontrollerobject->users['Papacy']->units[3]->order = new orderHold();
$gamecontrollerobject->users['Papacy']->units[4]->order = new orderHold();


$gamecontrollerobject->users['Naples']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Naples']->units[1]->order = new orderHold();
$gamecontrollerobject->users['Naples']->units[2]->order = new orderHold();
$gamecontrollerobject->users['Naples']->units[3]->order = new orderHold();
$gamecontrollerobject->users['Naples']->units[4]->order = new orderHold();

$gamecontrollerobject->users['Turkey']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Turkey']->units[1]->order = new orderHold();
$gamecontrollerobject->users['Turkey']->units[2]->order = new orderHold();
$gamecontrollerobject->users['Turkey']->units[3]->order = new orderHold();

// Test Orders END

$gamecontrollerobject->adjudication();
//$gamecontrollerobject->saveTurnData();

// TABLE: Orders

echo '<table border="10">';
echo '<colgroup> 
	<col width="80">
	<col width="80">
	<col width="200">
	<col width="80">
	<col width="80">
	<col width="80">
	<col width="80">
	<col width="80">
</colgroup>';
echo "<tr>
<th>Power</th>
<th>Unit</th>
<th>Order</th>
<th>Strength</th>
<th>Comment</th>
<th>Status</th>
<th>Result</th>
<th>Retreats</th>
</tr>";

/*<th>defComp</th>
<th>defHold</th>
<th>defAtt</th>
<th>tMovesElse</th>
<th>bounceComp</th>
<th>bounceHold</th>
<th>bounceAtt</th>*/

foreach ($gamecontrollerobject->users as $value) {
	echo "<tr>";
	echo "<td>".$value->power."</td>";
	echo "<td>";
		foreach ($value->units as $unit) {
			echo $unit->type." ".$unit->region."<br>";
		}
	echo "</td>";
	echo "<td>";
		foreach ($value->units as $unit) {
			echo $unit->getOrderString()."<br>";
		}
	echo "</td>";
	echo "<td>";
		foreach ($value->units as $unit) {
			echo $unit->strength."<br>";
		}
	echo "</td>";
	echo "<td>";
		foreach ($value->units as $unit) {
			echo $unit->comment."<br>";
		}
	echo "<td>";
		foreach ($value->units as $unit) {
			echo $unit->status."<br>";
		}
	echo "<td>";
		foreach ($value->units as $unit) {
			echo $unit->result."<br>";
		}

	echo "</tr>";
}
	
echo "</table>";

// TABLE Orders END

echo "<br><br>";
echo "<br><br>";

// TABLE: Regions

echo '<table border="10">';
echo "<tr>
<th>Name</th>
<th>Abbr.</th>
<th>City</th>
<th>Homeland</th>
<th>Adjacent Land Regions</th>
<th>Adjacent Sea Regions</th>
<th>Adjacent Coasts</th>
</tr>";

foreach ($gamecontrollerobject->getMap() as $value) {
	echo "<tr>";
	echo "<td>".$value['name']."</td>";
	echo "<td>".$value['abbr']."</td>";
	echo "<td>".$value['city']."</td>";
	echo "<td>".$value['homeland']."</td>";
	echo "<td>";
	foreach ($value->alr as $alr) {
		echo $alr." ";
	}
	echo "<td>";
	foreach ($value->asr as $asr) {
		echo $asr." ";
	}
	echo "<td>";
	foreach ($value->ac as $ac) {
		echo $ac." ";
	}
	echo "</td></tr>";
}
	
echo "</table>";

// TABLE Regions END

echo "<br><br>";
echo "<br><br>";



echo "</body></html>";
?>