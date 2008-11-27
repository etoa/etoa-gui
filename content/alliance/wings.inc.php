<?PHP

if (Alliance::checkActionRights('wings'))
{
	echo "<h2>Wings verwalten</h2>";
	
	if (isset($_GET['remove']) && $_GET['remove']>0)
	{
		if ($ally->removeWing($_GET['remove']))
			ok_msg("Wing entfernt");
	}
	
	if (isset($_POST['add_wing_id']) && $_POST['add_wing_id']>0)
	{
		if ($ally->addWing($_POST['add_wing_id']))
			ok_msg("Wing hinzugefügt");		
	}	
	
	$wings = $ally->getWings();
	if (count($wings) > 0)
	{
		tableStart("Wings");
		echo "<tr>
			<th>Name</th>
			<th>Punkte</th>
			<th>Mitglieder</th>
			<th>Punkteschnitt</th>
			<th>Aktionen</th>
		</tr>";
		foreach ($wings as $wid => $wdata)
		{
			echo "<tr>
			<td>".$wdata."</td>
			<td>".nf($wdata->points)."</td>
			<td>".$wdata->memberCount."</td>
			<td>".nf($wdata->avgPoints)."</td>
			<td>
				<a href=\"?page=alliance&amp;id=".$wid."\">Allianzseite</a> &nbsp; 
				<a href=\"?page=alliance&amp;action=wings&amp;remove=".$wid."\" onclick=\"return confirm('Wingzuordnung wirklich aufheben?')\">Entfernen</a> 
			</td>
			</tr>";
		}
		echo "</td></tr>";
		tableEnd();
	}			

	
	echo "<form action=\"?page=$page&amp;action=wings\" method=\"post\">";
	iBoxStart("Allianz als Wing hinzufügen");
	echo "Allianz wählen: <select name=\"add_wing_id\">";
	foreach (Alliance::getList() as $k => $v)
	{
		if ($k != $ally->id)
			echo "<option value=\"$k\">$v</option>";
	}
	echo "</select> &nbsp; 
	<input type=\"submit\" name=\"add_wing\" value=\"Hinzufügen\" /> ";
	iBoxEnd();
	echo "</form>
	<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
}
?>