<?PHP
	echo "<h2>Schiffsaktionen</h2>";
	
	if ($_GET['action']!="")
	{
		$site = $_GET['site'];
		$action = $_GET['action'];
		if ($site!="" AND $action!="")
		{
			if (@file_exists("info/action/$action.php"))
			{
				$actio['colonie']="Kolonialisieren";
				$actio['invasion']="Invasieren";
				$actio['spy']="Spionage";
				$actio['tech_steal']="Techklau";
				$actio['transport']="Transport";
				$actio['recycling']="Tr&uuml;mmer recyclen";
				$actio['nebula']="Gas/Nebel saugen";
				$actio['asteroid']="Asteroiden sammeln";
				$actio['bomb']="Bombardieren";
				$actio['deactivate']="Deaktivieren (EMP)";
				$actio['antrax']="Antrax";
				$actio['giftgas']="Giftgas";
				$actio['tarned']="Tarnangriff";
				$actio['fake']="Fakeangriff";
				
				Help::navi(array("Schiffsaktionen","action"),array("$action","$action"),1);
				echo "<select onchange=\"document.location='?page=$page&site=action&action='+this.options[this.selectedIndex].value\">";
				foreach ($actio as $ak=>$av)
				{
					echo "<option value=\"$ak\"";
					if ($ak==$action) echo " selected=\"selected\"";
					echo ">$av</option>";
				}
				echo "</select><br/><br/>";
				
				//Liest alle notwenidgen Daten aus der Schiffs-DB
				$res = dbquery("
				SELECT 
			        ship_id, 
			        ship_name, 
			        ship_people_capacity, 
			        ship_colonialize, 
			        ship_invade, 
			        ship_recycle, 
			        ship_asteroid, 
			        ship_nebula, 
			        ship_antrax,
			        ship_forsteal, 
			        ship_build_destroy, 
			        ship_tarned, 
			        ship_fake, 
			        ship_heal, 
			        ship_antrax_food, 
			        ship_deactivade, 
			        ship_tf 
				FROM 
					".$db_table['ships']." 
				WHERE 
					ship_buildable='1'
					AND special_ship='0'
				ORDER BY 
					ship_name ASC");
				include ("info/action/$action.php");
			}
		}
		echo "&nbsp;<input type=\"button\" value=\"Schiffsaktionen\" onclick=\"document.location='?page=$page&site=action'\" />";
	}
	else
	{
		Help::navi(array("Schiffsaktionen","action"));
		echo "Alle Schiffsaktionen in der &Uuml;bersicht:<br/><br/>";

		infobox_start("Planeten&uuml;bernahme",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Kolonialisieren</td>
		<td class=\"tbldata\">Funktion, Nutzen und Wissenswertes</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=colonie\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Invasieren</td>
		<td class=\"tbldata\">Funktion, Nutzen und Wissenswertes</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=invasion\">Anzeigen</a></td></tr>";
		infobox_end(1);

		infobox_start("Spionage",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Spionage</td>
		<td class=\"tbldata\">Wie das Spionagesystem funktioniert</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=spy\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Techklau</td>
		<td class=\"tbldata\">Techklau/Spionageangriff</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=tech_steal\">Anzeigen</a></td></tr>";
		infobox_end(1);

		infobox_start("Transport/Recycling/Asteroid",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Transport</td>
		<td class=\"tbldata\">Was kann mit welchen Schiffen transportiert werden</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=transport\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Tr&uuml;mmer recyclen</td>
		<td class=\"tbldata\">Sammle die Tr&uuml;mmerfelder und recycle sie</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=recycling\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Gas/Nebel saugen</td>
		<td class=\"tbldata\">Was Gas/Nebel ist und was es bringt</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=nebula\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Asteroiden sammeln</td>
		<td class=\"tbldata\">Was es ist und was man beachten sollte</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=asteroid\">Anzeigen</a></td></tr>";
		infobox_end(1);

		infobox_start("Bombardieren",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Bombardieren</td>
		<td class=\"tbldata\">Geb&auml;ude um 1 Level senken</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=bomb\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Deaktivieren (EMP)</td>
		<td class=\"tbldata\">Mit EMP-Technologie Geb&auml;ude deaktivieren</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=deactivate\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Antrax</td>
		<td class=\"tbldata\">Der Weg, Bewohner und Nahrung zu vernichten</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=antrax\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Giftgas</td>
		<td class=\"tbldata\">Die Vernichtunswaffe der Nahrung</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=giftgas\">Anzeigen</a></td></tr>";
		infobox_end(1);

		infobox_start("Tarnung",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Tarnangriff</td>
		<td class=\"tbldata\">Unsichtbare Flotte</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=tarned\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Fakeangriff</td>
		<td class=\"tbldata\">Gaukelt dem Gegner eine Flotte vor die gar nicht da ist</td>
		<td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action&action=fake\">Anzeigen</a></td></tr>";
		infobox_end(1);



	}

?>
