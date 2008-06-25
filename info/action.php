<?PHP
	echo "<h2>Schiffsaktionen</h2>";
	
	define(HELP_URL,"?page=$page&site=shipyard");
	
	
	if ($_GET['action']!="")
	{
		$site = $_GET['site'];
		$action = $_GET['action'];
		if ($site!="" AND $action!="")
		{
			Help::navi(array("Schiffsaktionen","action"),array("$action","$action"),1);
			echo "<select onchange=\"document.location='?page=$page&site=action&action='+this.options[this.selectedIndex].value\">";
			$actions = FleetAction::getAll();
			foreach($actions as $data)			
			{
				echo "<option value=\"".$data->code()."\"";
				if ($data->code()==$action) echo " selected=\"selected\"";
				echo ">".$data->name()."</option>\n";
			}
			echo "</select><br/><br/>";
				
			$ac  = FleetAction::createFactory($action);
			infobox_start($ac->name());
			echo $ac->desc();
			echo "<br/><br/><b>Gesinnung:</b> 
			<span style=\"color:".FleetAction::$attitudeColor[$ac->attitude()]."\">
			".FleetAction::$attitudeString[$ac->attitude()]."</span>";
			infobox_end();				
				
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
				
			infobox_start("Schiffe",1,0);
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
				}
			}
			else
			{
				echo "<tr><td class=\"tbldata\">Keine bekannten Schiffe haben diese Aktion</td></tr>";
			}
			infobox_end(1);		
		}
		echo "&nbsp;<input type=\"button\" value=\"Schiffsaktionen\" onclick=\"document.location='?page=$page&site=action'\" />";
	}
	else
	{
		Help::navi(array("Schiffsaktionen","action"));
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
			infobox_start("<span style=\"color:".FleetAction::$attitudeColor[$a]."\">".FleetAction::$attitudeString[$a]."</span>",1);			
			foreach($actions as $data)
			{
				echo "<tr><td class=\"tbltitle\" width=\"25%\">".$data->name()."</td>
				<td class=\"tbldata\">".$data->desc()."</td>
				<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=".$data->code()."\">Anzeigen</a></td></tr>";

			}			
			infobox_end(1);
		}
	}

?>
