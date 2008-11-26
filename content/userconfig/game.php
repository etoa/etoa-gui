<?PHP
 	// Datenänderung übernehmen
  if (isset($_POST['data_submit']) && checker_verify())
  {
  	$cu->properties->spyShipId = $_POST['spyship_id'];
  	$cu->properties->spyShipCount = $_POST['spyship_count'];
    success_msg("Benutzer-Daten wurden ge&auml;ndert!");
  }
			

  echo "<form action=\"?page=$page&mode=game\" method=\"post\" enctype=\"multipart/form-data\">";
  $cstr = checker_init();
  tableStart("Spieloptionen");

  echo "<tr>
  	<th style=\"width:250px;\"><b>Anzahl Spionagesonden für Direktscan:</b></th>
    <td>
    	<input type=\"text\" name=\"spyship_count\" maxlength=\"5\" size=\"5\" value=\"".$cu->properties->spyShipCount."\">
    </td>
  </tr>";
            
  echo "<tr><th>Typ des Spionageschiffs für Direktscan:</th>
  <td>";
	$sres = dbquery("
	SELECT 
    ship_id, 
    ship_name
	FROM 
		ships 
	WHERE 
		ship_buildable='1'
		AND (
		ship_actions LIKE '%,spy'
		OR ship_actions LIKE 'spy,%'
		OR ship_actions LIKE '%,spy,%'
		OR ship_actions LIKE 'spy'
		)
	ORDER BY 
		ship_name ASC");
  if (mysql_num_rows($sres)>0)
  {
  	echo '<select name="spyship_id"><option value="0">(keines)</option>';
  	while ($sarr=mysql_fetch_array($sres))
  	{
  		echo '<option value="'.$sarr['ship_id'].'"';
  		if ($cu->properties->spyShipId == $sarr['ship_id'])
  		 echo ' selected="selected"';
  		echo '>'.$sarr['ship_name'].'</option>';
  	}
  }
  else
  {
  	echo "Momentan steht kein Schiff zur Auswahl!";
  }
  echo "</td></tr>";

  tableEnd();
  echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
  echo "</form><br/><br/>";
?>