<?php 

require_once("../controllers/gamecontroller.php");

echo '<html><head><title>Machiavelli</title><link rel="stylesheet" href="styles.css"></head><body><br><br><font size="5"><b>Machiavelli Adjudication Page</b></font size><br><br><br><br>';

$gamecontrollerobject = new gamecontroller();
$gamecontrollerobject->newGame();


// Test Orders

$gamecontrollerobject->users['France']->units[0]->order = new orderMove("TUR");
$gamecontrollerobject->users['France']->units[1]->order = new orderSupport("AVI","PRO");
$gamecontrollerobject->users['France']->units[2]->order = new orderMove("SWI");
$gamecontrollerobject->users['Austria']->units[0]->order = new orderMove("TUR");
$gamecontrollerobject->users['Austria']->units[1]->order = new orderMove("CARIN");
$gamecontrollerobject->users['Austria']->units[2]->order = new orderMove("SLA");
$gamecontrollerobject->users['Milan']->units[0]->order = new orderHold();
$gamecontrollerobject->users['Milan']->units[1]->order = new orderSupport("TYR","TUR");
$gamecontrollerobject->users['Milan']->units[2]->order = new orderSupport("MIL","H");
$gamecontrollerobject->users['Austria']->units[3]->order = new orderSupport("MIL","H");

// Test Orders END

$gamecontrollerobject->adjudication();

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
</colgroup>';
echo "<tr>
<th>Power</th>
<th>Units</th>
<th>Orders</th>
<th>Strength</th>
<th>Status</th>
<th>Results</th>
<th>Retreats</th>
</tr>";

foreach ($gamecontrollerobject->users as $value) {
	echo "<tr>";
	echo "<td>".$value->power."</td>";
	echo "<td>";
		foreach ($value->units as $muschi) {
			echo $muschi->type." ".$muschi->region."<br>";
		}
	echo "</td>";
	echo "<td>";
		foreach ($value->units as $muschi) {
			echo $muschi->getOrderString()."<br>";
		}
	echo "</td>";
	echo "<td>";
		foreach ($value->units as $muschi) {
			echo $muschi->order->strength."<br>";
		}
	echo "</td>";
	echo "<td>";
		foreach ($value->units as $muschi) {
			echo $muschi->order->status."<br>";
		}
	echo "</td>";
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