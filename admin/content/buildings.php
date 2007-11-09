<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: buildings.php
	// 	Topic: Gebäudeverwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Kategorien bearbeiten
	//
	if ($sub=="prices")
	{
		echo "<h1>Preisrechner</h1>";
		echo "<script type=\"text/javascript\">
		function showPrices()
		{
			xajax_buildingPrices(
			document.getElementById('c1_id').options[document.getElementById('c1_id').selectedIndex].value,
			document.getElementById('c1_level').options[document.getElementById('c1_level').selectedIndex].value
			)
		}           
		
		function showTotalPrices()
		{
			xajax_totalBuildingPrices(xajax.getFormValues('totalCosts'));
		}		
		</script>";		
		
		$res = dbquery("
		SELECT
			building_id,
			building_name
		FROM
			buildings
		ORDER BY
			building_name
		");
		$bs = array();
		while ($arr=mysql_Fetch_row($res))
		{
			$bs[$arr[0]]=$arr[1];
		}		
				
		echo "<h2>(Aus)baukosten (von Stufe x-1 auf Stufe x)</h2>";
		echo "<table class=\"tb\">
			<tr>
				<th>Gebäude</th>
				<th>Stufe</th>
				<th>Zeit</th>
				<th>".RES_METAL."</th>
				<th>".RES_CRYSTAL."</th>
				<th>".RES_PLASTIC."</th>
				<th>".RES_FUEL."</th>
				<th>".RES_FOOD."</th>
				<th>Energie</th>
			</tr>";
		echo "<tr>
		<td><select id=\"c1_id\" onchange=\"showPrices()\">";
		foreach ($bs as $k => $v)
		{
			echo "<option value=\"".$k."\">".$v."</option>";
		}
		echo "</select></td>
		<td><select id=\"c1_level\" onchange=\"showPrices()\">";
		for ($x=1;$x<=40;$x++)
		{
			echo "<option value=\"".($x-1)."\">".$x."</option>";
		}
		echo "</select></td>
		<td id=\"c1_time\">-</td>
		<td id=\"c1_metal\">-</td>
		<td id=\"c1_crystal\">-</td>
		<td id=\"c1_plastic\">-</td>
		<td id=\"c1_fuel\">-</td>
		<td id=\"c1_food\">-</td>
		<td id=\"c1_power\">-</td>
		";
		echo "</tr></table>";
		
		echo "<h2>Totale Kosten</h2>
		<form action=\"?page=$page&amp;sub=$sub\" method=\"post\" id=\"totalCosts\">";
		ecHo "<table class=\"tb\">
		<tr>
			<th>Gebäude</th>
			<th>Level</th>
			<th>".RES_METAL."</th>
			<th>".RES_CRYSTAL."</th>
			<th>".RES_PLASTIC."</th>
			<th>".RES_FUEL."</th>
			<th>".RES_FOOD."</th>
		</tr>";
		foreach ($bs as $k => $v)
		{
			echo "<tr>
			<td>".$v."</td>
			<td>Level 0 bis <select name=\"b_lvl[".$k."]\" onchange=\"showTotalPrices()\">";
			for ($x=0;$x<=40;$x++)
			{
				echo "<option value=\"".$x."\">".$x."</option>";
			}		
			echo "</select></td>
			<td id=\"b_metal_".$k."\">-</td>
			<td id=\"b_crystal_".$k."\">-</td>
			<td id=\"b_plastic_".$k."\">-</td>
			<td id=\"b_fuel_".$k."\">-</td>
			<td id=\"b_food_".$k."\">-</td>
			</tr>";	
		}		
		echo "<tr><td style=\"height:2px;\" colspan=\"7\"></tr>";
		echo "<tr>
			<td colspan=\"2\">Total</td>
			<td id=\"t_metal\">-</td>
			<td id=\"t_crystal\">-</td>
			<td id=\"t_plastic\">-</td>
			<td id=\"t_fuel\">-</td>
			<td id=\"t_food\">-</td>
		</tr>";
		echo "</table></form>";
	}

	//
	// Gebäudepunkte
	//
	elseif ($sub=="points")
	{
		echo "<h1>Geb&auml;udepunkte</h1>";
		echo "<h2>Geb&auml;udepunkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if ($_POST['recalc']!="")
		{
			dbquery("DELETE FROM ".$db_table['building_points'].";");
			$res = dbquery("
			SELECT
				building_id,
                building_costs_metal,
                building_costs_crystal,
                building_costs_fuel,
                building_costs_plastic,
                building_costs_food,
				building_build_costs_factor
			FROM
				".$db_table['buildings'].";");
			$mnr = mysql_num_rows($res);
			if ($mnr>0)
			{
				while ($arr = mysql_fetch_array($res))
				{
					for ($level=1;$level<=intval($_POST['maxlevel']);$level++)
					{
						$r = $arr['building_costs_metal']+$arr['building_costs_crystal']+$arr['building_costs_fuel']+$arr['building_costs_plastic']+$arr['building_costs_food'];
						$p = ($r*(1-pow($arr['building_build_costs_factor'],$level))/(1-$arr['building_build_costs_factor'])) / $conf['points_update']['p1'];
						
						dbquery("
						INSERT INTO 
						".$db_table['building_points']." 
                            (bp_building_id,
                            bp_level,
                            bp_points) 
						VALUES 
                            (".$arr['building_id'].",
                            '".$level."',
                            '".$p."');");
					}
				}
			}
			if ($mnr>0)
				echo "Die Geb&auml;udepunkte von <b>$mnr</b> Geb&auml;uden wurden aktualisiert!<br/><br/>";
		}
		echo "Nach jeder &Auml;nderung an den Geb&auml;uden m&uuml;ssen die Geb&auml;udepunkte neu berechnet werden.<br/><br/>Punkte bis und mit Level ";
		echo "<input type=\"text\" name=\"maxlevel\" value=\"40\" size=\"2\" maxlength=\"2\" /> <input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";

		echo "<h2>Geb&auml;udepunkte</h2>";
		$res=dbquery("
		SELECT
			building_id,
			building_name
		FROM 
			".$db_table['buildings']."
		ORDER BY 
			building_order,
			building_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><th>".$arr['building_name']."</th><td style=\"width:70%\"><table class=\"tb\">";
				$pres=dbquery("
				SELECT
					bp_level,
					bp_points
				FROM 
					".$db_table['building_points']."
				WHERE
					bp_building_id=".$arr['building_id']."
				ORDER BY 
					bp_level ASC;");
				if (mysql_num_rows($pres)>0)
				{
					$cnt=0;
					while ($parr=mysql_fetch_array($pres))
					{
						if ($cnt==0)
							echo "<tr>";
						echo "<th>".$parr['bp_level']."</th><td>".$parr['bp_points']."</td>";
						if ($cnt=="3")
						{
							echo "</tr>";
							$cnt=0;
						}
						else
							$cnt++;
					}
				}
				echo "</table></td></tr>";
			}
			echo "</table>";
		}

	}

	//
	// Kategorien bearbeiten
	//
	elseif ($sub=="type")
	{
		simple_form("building_types");
	}

	//
	// Gebäude bearbeiten
	//
	elseif ($sub=="data")
	{
		advanced_form("buildings");
	}

	//
	// Voraussetzungen
	//
	elseif ($sub=="req")
	{

		define(TITLE,"Geb&auml;udeanforderungen");
		define(ITEMS_TBL,"buildings");
		define(TYPES_TBL,"building_types");
		define(REQ_TBL,"building_requirements");
		define(REQ_ITEM_FLD,"req_building_id");
		define(ITEM_ID_FLD,"building_id");
		define(ITEM_NAME_FLD,"building_name");
		define(ITEM_SHOW_FLD,"building_show");
		define(ITEM_ORDER_FLD,"building_type_id,building_order,building_name");
		define(NO_ITEMS_MSG,"In dieser Kategorie gibt es keine Geb&auml;ude!");

		echo "<h1>".TITLE."</h1>";
		if ($_POST['submit_changes']!="")
		{
			// Gebäudeänderungen speichern
			foreach ($_POST['building_id'] as $id=>$val)
			{
				if ($_POST['building_level'][$id]<1)
					dbquery("DELETE FROM ".$db_table['building_requirements']." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".$db_table['building_requirements']." SET req_req_building_id=$val,req_req_building_level=".$_POST['building_level'][$id]." WHERE req_id=$id;");
			}
			// Technologieänderungen speichern
			foreach ($_POST['tech_id'] as $id=>$val)
			{
				if ($_POST['tech_level'][$id]<1)
					dbquery("DELETE FROM ".$db_table['building_requirements']." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".$db_table['building_requirements']." SET req_req_tech_id=$val,req_req_tech_level=".$_POST['tech_level'][$id]." WHERE req_id=$id;");
			}
		}

		// Gebäudeverknüpfung speichern
		if ($_POST['add_building']!="")
		{
			if ($_POST['new_item_id']!="")
			{
				if (mysql_num_rows(dbquery("SELECT req_id FROM ".$db_table['building_requirements']." WHERE req_building_id=".$_POST['new_id']." AND req_req_building_id=".$_POST['new_item_id'].";"))==0)
				{
					dbquery("INSERT INTO ".$db_table['building_requirements']." (req_building_id,req_req_building_id,req_req_building_level) VALUES ('".$_POST['new_id']."','".$_POST['new_item_id']."','".$_POST['new_item_level']."');");
				}
				else
					echo "Fehler! Diese Geb&auml;udeverkn&uuml;pfung existiert bereits!<br/><br/>";
			}
			else
				echo "Fehler! Kein verkn&uuml;pfendes Geb&auml;ude ausgew&auml;hlt!<br/><br/>";
		}

		// Technologieverknüpfung speicher
		if ($_POST['add_tech']!="")
		{
			if ($_POST['new_item_id']!="")
			{
				if (mysql_num_rows(dbquery("SELECT req_id FROM ".$db_table['building_requirements']." WHERE req_building_id=".$_POST['new_id']." AND req_req_tech_id=".$_POST['new_item_id'].";"))==0)
				{
					dbquery("INSERT INTO ".$db_table['building_requirements']." (req_building_id,req_req_tech_id,req_req_tech_level) VALUES ('".$_POST['new_id']."','".$_POST['new_item_id']."','".$_POST['new_item_level']."');");
				}
				else
					echo "Fehler! Diese Forschungsverkn&uuml;pfung existiert bereits!<br/><br/>";
			}
			else
				echo "Fehler! Keine verkn&uuml;pfende Forschung ausgew&auml;hlt!<br/><br/>";
		}

		// Gebäudeverknüpfungen löschen
		if ($_POST['del_building']!="")
		{
			if (count($_POST['del_building'])>0)
			{
				foreach ($_POST['del_building'] as $req_building_id=>$req_req_building_id)
				{
					foreach ($req_req_building_id as $key=>$val)
					{
						dbquery("DELETE FROM ".$db_table['building_requirements']." WHERE req_building_id=$req_building_id AND req_req_building_id=$key;");
					}
				}
			}
		}

		// Technologieknüpfungen löschen
		if ($_POST['del_tech']!="")
		{
			if (count($_POST['del_tech'])>0)
			{
				foreach ($_POST['del_tech'] as $req_building_id=>$req_req_tech_id)
				{
					foreach ($req_req_tech_id as $key=>$val)
					{
						dbquery("DELETE FROM ".$db_table['building_requirements']." WHERE req_building_id=$req_building_id AND req_req_tech_id=$key;");
					}
				}
			}
		}


		// Lade Gebäude- & Technologienamen
		$bures = dbquery("SELECT building_id,building_name FROM ".$db_table['buildings']." WHERE building_show=1;");
		while ($buarr = mysql_fetch_array($bures))
		{
			$bu_name[$buarr['building_id']]=$buarr['building_name'];
		}
		$teres = dbquery("SELECT tech_id,tech_name FROM ".$db_table['technologies']." WHERE tech_show=1;");
		while ($tearr = mysql_fetch_array($teres))
		{
			$te_name[$tearr['tech_id']]=$tearr['tech_name'];
		}

		// Lade Anforderungen
		$rres = dbquery("SELECT * FROM ".$db_table[REQ_TBL].";");
		while ($rarr = mysql_fetch_array($rres))
		{
			$b_req[$rarr[REQ_ITEM_FLD]]['i'][$rarr['req_req_building_id']]=$rarr['req_id'];
			$b_req[$rarr[REQ_ITEM_FLD]]['i'][$rarr['req_req_tech_id']]=$rarr['req_id'];
			if ($rarr['req_req_building_id']>0) $b_req[$rarr[REQ_ITEM_FLD]]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
			if ($rarr['req_req_tech_id']>0) $b_req[$rarr[REQ_ITEM_FLD]]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
		}

		$res = dbquery("SELECT * FROM ".$db_table[ITEMS_TBL]." WHERE ".ITEM_SHOW_FLD."=1 ORDER BY ".ITEM_ORDER_FLD.";");
		if (mysql_num_rows($res)>0)
		{
			if ($_GET['action']=="new_building" || $_GET['action']=="new_tech")
				$form_addition=" disabled=\"disabled\"";

			while ($arr=mysql_fetch_array($res))
			{
				echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
				echo "<table class=\"tb\" style=\"width:400px;\">";
				echo "<tr><th colspan=\"3\">".$arr[ITEM_NAME_FLD]."</th></tr>";
				$using_something=0;

				// Gespeicherte Gebäudeanforderungen
				if (count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
				{
					foreach ($b_req[$arr[ITEM_ID_FLD]]['b'] as $b=>$l)
					{
						echo "<tr>";
						echo "<td width=\"200\"><select name=\"building_id[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" $form_addition>";
						if ($b==0)
						echo "<option value=\"\"><i>Geb&auml;ude w&auml;hlen</i></option>";
						foreach ($bu_name as $key=>$val)
						{
							echo "<option value=\"$key\"";
							if ($b==$key) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
						echo "</select></td><td width=\"50\"><input type=\"text\" name=\"building_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
							echo "<td><input type=\"submit\" name=\"del_building[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\" /></td></tr>";
						else
							echo "<td>&nbsp;</td></tr>";
					}
					$using_something=1;
				}
				// Neue Gebäudeanforderung
				if ($_GET['action']=="new_building" && $_GET['id']==$arr[ITEM_ID_FLD])
				{
					echo "<input type=\"hidden\" name=\"new_id\" value=\"".$arr[ITEM_ID_FLD]."\">";
					echo "<tr><td width=\"200\"><select name=\"new_item_id\">";
					echo "<option value=\"\" style=\"font-style:italic;\">Geb&auml;ude w&auml;hlen</option>";
					foreach ($bu_name as $key=>$val)
					{
						if ($key!=$arr[ITEM_ID_FLD])
							echo "<option value=\"$key\">$val</option>";
					}
					echo "</select></td><td><input type=\"text\" name=\"new_item_level\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
					echo "<td><input type=\"submit\" name=\"add_building\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}

				// Gespeicherte Forschungsanforderungen
				if (count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
				{
					foreach ($b_req[$arr[ITEM_ID_FLD]]['t'] as $b=>$l)
					{
						echo "<tr><td width=\"200\"><select name=\"tech_id[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" $form_addition>";
						if ($b==0)
						echo "<option value=\"\"><i>Geb&auml;ude w&auml;hlen</i></option>";
						foreach ($te_name as $key=>$val)
						{
							echo "<option value=\"$key\"";
							if ($b==$key) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
						echo "</select></td><td width=\"50\"><input type=\"text\" name=\"tech_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
							echo "<td><input type=\"submit\" name=\"del_tech[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\"$form_addition /></td></tr>";
						else
							echo "<td>&nbsp;</td></tr>";
					}
					$using_something=1;
				}
				// Neue Forschungsanforderung
				if ($_GET['action']=="new_tech" && $_GET['id']==$arr[ITEM_ID_FLD])
				{
					echo "<input type=\"hidden\" name=\"new_id\" value=\"".$arr[ITEM_ID_FLD]."\">";
					echo "<tr><td width=\"200\"><select name=\"new_item_id\">";
					echo "<option value=\"\" style=\"font-style:italic;\">Technologie w&auml;hlen</option>";
					foreach ($te_name as $key=>$val)
					{
						echo "<option value=\"$key\">$val</option>";
					}
					echo "</select></td><td><input type=\"text\" name=\"new_item_level\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
					echo "<td><input type=\"submit\" name=\"add_tech\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}
				if ($using_something==0)
					echo "<tr><td width=\"200\">&nbsp;</td><td colspan=\"2\">Keine Voraussetzungen</td></tr>";
				if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
				{
					echo "<tr><td>Neue Voraussetzung?</td>";
					echo "<td colspan=\"2\"><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_building&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Geb&auml;ude\" />&nbsp;";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_tech&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Forschung\" /></tr>";
				}
				echo "</table><br/>";
			}
			if ($form_addition=="")
				echo "<p align=\"center\"><input type=\"submit\" name=\"submit_changes\" value=\"&Auml;nderungen &uuml;bernehmen\" /></p>";
		}
		else
			echo "<p class=\"infomsg\">".NO_ITEMS_MSG."</p>";
	}

	//
	// Liste
	//
	else
	{
		echo "<h1>Geb&auml;udeliste</h1>";

		$build_type[0]="Unt&auml;tig";
		$build_type[1]="Bauen";
		$build_type[2]="Abreissen";

		$build_colors[0]="#fff";
		$build_colors[1]="#0f0";
		$build_colors[2]="orange";

		if ($_POST['save']!="")
		{
			dbquery("
			UPDATE 
				".$db_table['buildlist']." 
			SET 
                buildlist_current_level='".$_POST['buildlist_current_level']."',
                buildlist_build_type='".$_POST['buildlist_build_type']."',
                buildlist_build_start_time=UNIX_TIMESTAMP('".$_POST['buildlist_build_start_time']."'),
                buildlist_build_end_time=UNIX_TIMESTAMP('".$_POST['buildlist_build_end_time']."') 
			WHERE 
				buildlist_id='".$_POST['buildlist_id']."';");
		}
		elseif ($_POST['del']!="")
		{
			dbquery("DELETE FROM ".$db_table['buildlist']." WHERE buildlist_id='".$_POST['buildlist_id']."';");
		}


		//
		// Datensatz bearbeiten
		//
		if ($_GET['action']=="edit")
		{
			echo "<h2>Datensatz bearbeiten</h2>";
			$res = dbquery("
			SELECT 
				buildlist.buildlist_id,
				buildlist.buildlist_current_level,
				buildlist.buildlist_build_start_time,
				buildlist.buildlist_build_end_time,
				buildlist.buildlist_build_type,
				planets.planet_name,
				users.user_nick,
				buildings.building_name
			FROM 
                ".$db_table['buildlist'].",
                ".$db_table['planets'].",
                ".$db_table['users'].",
                ".$db_table['buildings']." 
			WHERE 
                buildlist.buildlist_building_id=buildings.building_id 
                AND users.user_id=buildlist.buildlist_user_id 
                AND planets.planet_id=buildlist.buildlist_planet_id 
                AND buildlist.buildlist_id=".$_GET['buildlist_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=$sub&amp;action=search\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"buildlist_id\" value=\"".$arr['buildlist_id']."\" />";
				echo "<table class=\"tb\">";
				echo "<tr><th>ID</th><td>".$arr['buildlist_id']."</td></tr>";
				echo "<tr><th>Planet</th><td>".$arr['planet_name']."</td></tr>";
				echo "<tr><th>Spieler</th><td>".$arr['user_nick']."</td></tr>";
				echo "<tr><th>Geb&auml;ude</th><td>".$arr['building_name']."</td></tr>";
				echo "<tr><th>Level</th><td><input type=\"text\" name=\"buildlist_current_level\" value=\"".$arr['buildlist_current_level']."\" size=\"2\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th>Baustatus</th><td><select name=\"buildlist_build_type\">";
				foreach ($build_type as $id=>$val)
				{
					echo "<option value=\"$id\"";
					if ($arr['buildlist_build_type']==$id) echo " selected=\"selected\"";
					echo ">$val</option>";
				}
				echo "</select></td></tr>";
				if ($arr['buildlist_build_start_time']>0) $bst = date(DATE_FORMAT,$arr['buildlist_build_start_time']); else $bst = "";
				if ($arr['buildlist_build_end_time']>0) $bet = date(DATE_FORMAT,$arr['buildlist_build_end_time']); else $bet = "";
				echo "<tr><th>Baustart</th><td><input type=\"text\" name=\"buildlist_build_start_time\" id=\"buildlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('buildlist_build_start_time').value='".date("Y-d-m h:i")."'\" /></td></tr>";
				echo "<tr><th>Bauende</th><td><input type=\"text\" name=\"buildlist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" />&nbsp;";
				echo "<input type=\"submit\" value=\"Zur&uuml;ck zu den Suchergebnissen\" />&nbsp;";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
				echo "</form>";
			}
			else
				echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Neue Suche</a>";
		}

		//
		// Suchergebnisse anzeigen
		//
		elseif (($_POST['buildlist_search']!="" || $_POST['new']!="" || $_SESSION['admin']['building_query']!="") && $_GET['action']=="search")
		{
			$tables = $db_table['buildlist'].",".$db_table['planets'].",".$db_table['users'].",".$db_table['buildings'];
			if ($_POST['new']!="")
			{
				$updata=explode(":",$_POST['planet_id']);
				if (mysql_num_rows(dbquery("SELECT buildlist_id FROM ".$db_table['buildlist']." WHERE buildlist_planet_id=".$updata[0]." AND buildlist_building_id=".$_POST['building_id'].";"))==0)
				{
					dbquery("
					INSERT INTO 
					".$db_table['buildlist']." 
                        (buildlist_planet_id,
                        buildlist_user_id,
                        buildlist_building_id,
                        buildlist_current_level) 
					VALUES 
                        (".$updata[0].",
                        ".$updata[1].",
                        ".$_POST['building_id'].",
                        ".$_POST['building_level'].");");
					echo "Geb&auml;ude wurde hinzugef&uuml;gt!<br/>";
				}
				else
					echo "Geb&auml;ude kann nicht hinzugef&uuml;gt werden, es ist bereits vorhanden!<br/>";
				$sql= "planet_id=".$updata[0]." AND ";
				$_SESSION['admin']['building_query']="";


				echo "<h2>Neues Geb&auml;ude hinzuf&uuml;gen</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
				infobox_start("",1);
				echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><td class=\"tbldata\"><select name=\"building_id\">";
				$bres = dbquery("SELECT building_id,building_name FROM ".$db_table['buildings']." ORDER BY building_type_id,building_order,building_name;");
				while ($barr=mysql_fetch_array($bres))
				{
					echo "<option value=\"".$barr['building_id']."\">".$barr['building_name']."</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th class=\"tbltitle\">mit Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"building_level\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">auf dem Planeten</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
				$pres=dbquery("SELECT user_id,planet_id,planet_name,user_nick,planet_solsys_pos,cell_sx,cell_sy,cell_cx,cell_cy FROM ".$db_table['planets'].",".$db_table['space_cells'].",".$db_table['users']." WHERE planet_user_id=user_id AND planet_solsys_id=cell_id ORDER BY planet_id;");
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<option value=\"".$parr['planet_id'].":".$parr['user_id']."\"";
					if ($updata[0]==$parr['planet_id']) echo " selected=\"selected\"";
					echo ">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." &nbsp; ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
				}
				echo "</select></td></tr>";
				infobox_end(1);
				echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form><br/>";


			}
			else
			{
				if ($_POST['planet_id']!="")
				{
					$sql.= "planet_id=".$_POST['planet_id']." AND ";
				}
				if ($_POST['planet_name']!="")
				{
					if (stristr($_POST['qmode']['planet_name'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "planet_name ".stripslashes($_POST['qmode']['planet_name']).$_POST['planet_name']."$addchars' AND ";
				}
				if ($_POST['user_id']!="")
				{
					$sql.= "user_id=".$_POST['user_id']." AND ";
				}
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars' AND ";
				}
				if ($_POST['building_id']!="")
				{
					$sql.= "building_id=".$_POST['building_id']." AND ";
				}
			}

			echo "<h2>Suchergebnisse</h2>";
			$sqlstart = "SELECT * FROM $tables WHERE ";
			$sqlend = "buildlist_building_id=building_id AND user_id=buildlist_user_id AND planet_id=buildlist_planet_id
			GROUP BY buildlist_id
			ORDER BY buildlist_user_id,buildlist_planet_id,building_type_id,building_order,building_name;";

  		if ($_SESSION['admin']['building_query']=="")
  			$_SESSION['admin']['building_query']=$sqlstart.$sql.$sqlend;

			$res = dbquery($_SESSION['admin']['building_query']);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" /><br/><br/>";

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<th class=\"tbltitle\">Planet</th>";
				echo "<th class=\"tbltitle\">Spieler</th>";
				echo "<th class=\"tbltitle\">Geb&auml;ude</th>";
				echo "<th class=\"tbltitle\">Stufe</th>";
				echo "<th class=\"tbltitle\">Status</th>";
				echo "<th></th>";
				echo "</tr>";
				for ($x=0;$x<mysql_num_rows($res);$x++)
				{
					if ($narr!=null)
						$arr=$narr;
					else
						$arr = mysql_fetch_array($res);
					$narr=mysql_fetch_array($res);

					echo "<tr>";
					if ($larr['user_id']==$arr['user_id'] && $narr['user_id']==$arr['user_id'] && $larr['planet_id']==$arr['planet_id'] && $narr['planet_id']==$arr['planet_id'])
						echo "<td class=\"tbldatawtb\">&nbsp;</td>";
					elseif ($larr['user_id']==$arr['user_id'] && $larr['planet_id']==$arr['planet_id'])
						echo "<td class=\"tbldatawt\">&nbsp;</td>";
					elseif ($narr['user_id']==$arr['user_id'] && $narr['planet_id']==$arr['planet_id'])
						echo "<td class=\"tbldatawb\"><a href=\"?page=galaxy&amp;sub=edit&amp;planet_id=".$arr['buildlist_planet_id']."\" title=\"".$arr['planet_name']."\">".cut_string($arr['planet_name'],11)."</a></td>";
					else
						echo "<td class=\"tbldata\"><a href=\"?page=galaxy&amp;sub=edit&amp;planet_id=".$arr['buildlist_planet_id']."\" title=\"".$arr['planet_name']."\">".cut_string($arr['planet_name'],11)."</a></td>";

					if ($larr['user_id']==$arr['user_id'] && $narr['user_id']==$arr['user_id'] && $larr['planet_id']==$arr['planet_id'] && $narr['planet_id']==$arr['planet_id'])
						echo "<td class=\"tbldatawtb\">&nbsp;</td>";
					elseif ($larr['user_id']==$arr['user_id'] && $larr['planet_id']==$arr['planet_id'])
						echo "<td class=\"tbldatawt\">&nbsp;</td>";
					elseif ($narr['user_id']==$arr['user_id'] && $narr['planet_id']==$arr['planet_id'])
						echo "<td class=\"tbldatawb\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['buildlist_user_id']."\" title=\"".$arr['user_nick']."\">".cut_string($arr['user_nick'],11)."</a></td>";
					else
						echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['buildlist_user_id']."\" title=\"".$arr['user_nick']."\">".cut_string($arr['user_nick'],11)."</a></td>";
					$style=" style=\"color:".$build_colors[$arr['buildlist_build_type']]."\"";

					echo "<td class=\"tbldata\" $style>".$arr['building_name']."</td>";
					echo "<td class=\"tbldata\" $style>".nf($arr['buildlist_current_level'])."</td>";
					echo "<td class=\"tbldata\" $style>".$build_type[$arr['buildlist_build_type']]."</td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&amp;sub=$sub&amp;action=edit&amp;buildlist_id=".$arr['buildlist_id'])."</td>";
					echo "</tr>";

					$larr=$arr;
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Zur&uuml;ck\" />";
			}
		}

		//
		// Suchformular
		//
		else
		{
			$bres = dbquery("SELECT building_id,building_name FROM ".$db_table['buildings']." ORDER BY building_type_id,building_order,building_name;");
			while ($barr=mysql_fetch_array($bres))
			{
				$bdata[$barr['building_id']]=$barr;
			}
			$_SESSION['admin']['building_query']="";
			echo "<h2>Suchmaske</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Planet ID</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
			echo "<tr><th class=\"tbltitle\">Planetname</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" />&nbsp;";
			fieldqueryselbox('planet_name');
			echo "</td>";;
			echo "<tr><th class=\"tbltitle\">Spieler ID</th><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
			echo "<tr><th class=\"tbltitle\">Spieler Nick</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
			fieldqueryselbox('user_nick');
			echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td>";
			echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><td class=\"tbldata\"><select name=\"building_id\"><option value=\"\"><i>---</i></option>";
			foreach ($bdata as $barr)
			{
				echo "<option value=\"".$barr['building_id']."\">".$barr['building_name']."</option>";
			}
			echo "</select></td>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"buildlist_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(buildlist_id) FROM ".$db_table['buildlist'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>";



			echo "<h2>Neues Geb&auml;ude hinzuf&uuml;gen</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			infobox_start("",1);
			echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><td class=\"tbldata\"><select name=\"building_id\">";
			foreach ($bdata as $barr)
			{
				echo "<option value=\"".$barr['building_id']."\">".$barr['building_name']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><th class=\"tbltitle\">mit Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"building_level\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">auf dem Planeten</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
			$pres=dbquery("
			SELECT 
                users.user_id,
                users.user_nick,
                planets.planet_id,
                planets.planet_name,
                planets.planet_solsys_pos,
                space_cells.cell_sx,
                space_cells.cell_sy,
                space_cells.cell_cx,
                space_cells.cell_cy 
			FROM 
                ".$db_table['planets'].",
                ".$db_table['space_cells'].",
                ".$db_table['users']." 
			WHERE 
                planets.planet_user_id=users.user_id 
                AND planets.planet_solsys_id=space_cells.cell_id 
			ORDER BY 
				planets.planet_id;");
			while ($parr=mysql_fetch_array($pres))
			{
				echo "<option value=\"".$parr['planet_id'].":".$parr['user_id']."\">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." &nbsp; ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
			}
			echo "</select></td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form>";
		}
	}


?>
