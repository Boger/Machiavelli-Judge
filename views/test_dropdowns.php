<?php
if (!isset($_REQUEST['ajax'])) { // if a normal request for the page
  if (!isset($_REQUEST['id'])) $_REQUEST['id'] = 1;

  // ensure we select the top level select box is pre-selected
  $selected = array('1' => '', '2' => '', '3' => '');
  $selected[$_REQUEST['id']] = 'selected="selected"';

?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8">
$(function(){
  $("select#ctlJob").change(function(){
    $.getJSON("/select.php",{id: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#ctlPerson").html(options);
    })
  })
})
</script>

<form action="/test_dropdowns.php">
  <label for="ctlJob">Job Function:</label>
  <select id="ctlJob">
<?php
  echo '  <option ' . $selected['1'] . ' value="1">Managers</option>';
  echo '  <option ' . $selected['2'] . ' value="2">Team Leaders</option>';
  echo '  <option ' . $selected['3'] . ' value="3">Developers</option>';
?>
  </select>
  <noscript>
    <input type="submit" name="action" value="Load Individuals" />
  </noscript>
  <label for="ctlPerson">Individual:</label>
  <select id="ctlPerson">
<?php
	foreach (getIndividual($_REQUEST['id']) as $id => $name) {
	  echo '    <option value="' . $id . '">' . $name . "</option>\n";
	}
?>
  </select>
<input type="submit" name="action" value="Book" />
</form>
<?php
} else { // we are just asking for the json response via ajax
  $names = getIndividual($_REQUEST['id']);
  $json = '['; // start the json array element
  $json_names = array();
  foreach ($names as $id => $name) {
    $json_names[] = "{id: $id, name: '$name'}";
  }
  
  $json .= implode(',', $json_names); // join the objects by commas;
  $json .= ']'; // end the json array element
  echo $json;
}

function getIndividual($id) {
	
	if ($id == 1) {
		$names = array('0' => 'Mark', '1' => 'Andy', '2' => 'Richard');
	} else if ($id == 2) {
		$names = array('10' => 'Remy', '11' => 'Arif', '12' => 'JC');
	} else if ($id == 3) {
		$names = array('20' => 'Aidan', '21' => 'Henry');
	}
	
	return $names;
}
?>