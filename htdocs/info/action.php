<?PHP
	echo "<h2>Schiffsaktionen</h2>";

	define("HELP_URL","?$link&amp;site=shipyard");


	if (isset($_GET['action']) && ctype_alpha($_GET['action']))
	{
		$site = $_GET['site'];
		$action = $_GET['action'];
		if ($site!="" AND $action!="")
		{
			HelpUtil::breadCrumbs(array("Schiffsaktionen","action"),array("$action","$action"),1);
			echo "<select onchange=\"document.location='?$link&amp;site=action&amp;action='+this.options[this.selectedIndex].value\">";
			$actions = FleetAction::getAll();
			foreach($actions as $data)
			{
				echo "<option value=\"".$data->code()."\"";
				if ($data->code()==$action) echo " selected=\"selected\"";
				echo ">".$data->name()."</option>\n";
			}
			echo "</select><br/><br/>";

			$ac  = FleetAction::createFactory($action);
			iBoxStart($ac->name());
			echo $ac->desc()."<br/><br/>".$ac->longDesc();
			echo "<br/><br/><b>Gesinnung:</b>
			<span style=\"color:".FleetAction::$attitudeColor[$ac->attitude()]."\">
			".FleetAction::$attitudeString[$ac->attitude()]."</span>
			<br/><b>Sichtbarkeit:</b> ".($ac->visible() ? 'Für das Ziel sichtbar.' : 'Nur für mich sichtbar.')."
			<br/><b>Exklusiv:</b> ".($ac->exclusive() ? 'Ja, nur Spezialschiffe oder Schiffe mit dieser Fähigkeit dürfen mitfliegen.' : 'Nein, alle Schiffe können mitfliegen.');
			iBoxEnd();

				//Liest alle notwenidgen Daten aus der Schiffs-DB
				$res = dbquery("
				SELECT
	        ship_id,
	        ship_name
				FROM
					ships
				WHERE
					ship_buildable='1'
					AND special_ship='0'
					AND (
					ship_actions LIKE '%,".$ac->code()."'
					OR ship_actions LIKE '".$ac->code().",%'
					OR ship_actions LIKE '%,".$ac->code().",%'
					OR ship_actions LIKE '".$ac->code()."'
					)
				ORDER BY
					ship_name ASC");

			tableStart("Schiffe");
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr['ship_id']."\">".$arr['ship_name']."</a></td></tr> ";
				}
			}
			else
			{
				echo "<tr><td class=\"tbldata\">Keine bekannten Schiffe haben diese Aktion</td></tr>";
			}
			tableEnd();
		}
		echo "&nbsp;<input type=\"button\" value=\"Schiffsaktionen\" onclick=\"document.location='?$link&amp;site=action'\" />";
	}
	else
	{
		HelpUtil::breadCrumbs(array("Schiffsaktionen","action"));
		echo "Alle Schiffsaktionen in der &Uuml;bersicht:<br/><br/>";

		$attitudes = array();

		$actions = FleetAction::getAll();
		foreach($actions as $key => $data)
		{
			$attitudes[$data->attitude()][] = $data;
		}

		ksort($attitudes);

		foreach ($attitudes as $a => $actions)
		{
			tableStart("<span style=\"color:".FleetAction::$attitudeColor[$a]."\">".FleetAction::$attitudeString[$a]."</span>");
			foreach($actions as $data)
			{
				echo "<tr><td class=\"tbltitle\" width=\"25%\">".$data->name()."</td>
				<td class=\"tbldata\">".$data->desc()."</td>
				<td class=\"tbldata\" width=\"60\"><a href=\"?$link&amp;site=action&amp;action=".$data->code()."\">Anzeigen</a></td></tr>";

			}
			tableEnd();
		}
	}

?>
