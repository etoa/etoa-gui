<?PHP

	$tm = new TextManager();

	$sx_num=$conf['num_of_sectors']['p1'];
	$sy_num=$conf['num_of_sectors']['p2'];
	$cx_num=$conf['num_of_cells']['p1'];
	$cy_num=$conf['num_of_cells']['p2'];

	echo "<h1>Willkommen in Andromeda</h1>";

	echo "<div class=\"userSetupContainer\">";

		// Apply choosen itemset
	if (isset($s->itemset_key) && isset($_POST[md5($s->itemset_key)]) && isset($_POST['itemset_id']))
	{
		Usersetup::addItemSetListToPlanet($s->itemset_planet,$cu->id,$_POST['itemset_id']);
		$s->itemset_key=null;
		$s->itemset_planet=null;
		$cu->setSetupFinished();
		$mode = "finished";		
	}
	elseif (isset($_POST['submit_chooseplanet']) && intval($_POST['choosenplanetid'])>0 && checker_verify() && !isset($cp))
	{

		$tp = Planet::getById($_POST['choosenplanetid']);
        $cfg = Config::getInstance();

		if($tp && $tp->habitable && $tp->userId == 0 && $tp->fields>$cfg->value('user_min_fields')) {

            $tp->reset();
            $tp->assignToUser($cu->id,1);
            $tp->setDefaultResources();

            $cu->addToUserLog("planets","{nick} wählt [b]".$tp."[/b] als Hauptplanet aus.",0);

            $res = dbquery("
                SELECT
                    set_id,
                    set_name
                FROM
                    default_item_sets
                WHERE
                    set_active=1
                ");
            if (mysql_num_rows($res)>1)
            {
                $mode="itemsets";
            }
            elseif(mysql_num_rows($res)==1)
            {
                $arr = mysql_fetch_array($res);
                Usersetup::addItemSetListToPlanet($tp->id,$cu->id,$arr['set_id']);
                $cu->setSetupFinished();
                $mode = "finished";
            }
            else
            {
                $cu->setSetupFinished();
                $mode = "finished";
            }
        }
	}
	elseif (isset($_GET['setup_sx']) && isset($_GET['setup_sy']) && $_GET['setup_sx']>0 && $_GET['setup_sy']>0 && $_GET['setup_sx']<=$sx_num && $_GET['setup_sy']<=$sy_num)
	{
		if ($pid = PlanetManager::getFreePlanet($_GET['setup_sx'],$_GET['setup_sy'],array_key_exists('filter_p',$_GET) ? $_GET['filter_p'] : null,array_key_exists('filter_s',$_GET) ? $_GET['filter_s'] : null))
		{
			$mode = "checkplanet";
		}		
		else
		{
			echo "Leider konnte kein geeigneter Planet in diesem Sektor gefunden werden.<br/>
			Bitte wähle einen anderen Sektor!<br/><br/>";
			$mode = "choosesector";	
		}		
	}
	
	elseif ($cu->raceId >0 && !isset($cp))
	{
		$mode = "choosesector";	
	}
	elseif (isset($_POST['submit_setup1']) && intval($_POST['register_user_race_id'])>0 && checker_verify())
	{
		$cu->race = new Race($_POST['register_user_race_id']);
		$mode = "choosesector";	
	}
	elseif ($cu->raceId==0)
	{
		$mode = "race";
	}

	if ($mode=="itemsets")	
	{
		$k = mt_rand(10000,99999);
		$s->temset_key=$k;
		$s->itemset_planet=$tp->id();
		iBoxStart("Start-Objekte");
		echo "<form action=\"?\" method=\"post\">";
		checker_init();
		echo "Euch stehen mehrere Vorlagen von Start-Objekte zur Auswahl. Bitte wählt eine Vorlage aus, die darin definierten Objekte
		werden dann eurem Hauptplanet hinzugefügt: <br/><br/><select name=\"itemset_id\">";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<option value=\"".$arr['set_id']."\">".$arr['set_name']."</option>";
		}
		echo "</select> <input type=\"submit\" value=\"Weiter\" name=\"".md5($k)."\" /></form>";
		iBoxEnd();		
	}	
	elseif ($mode=="checkplanet")
	{
		echo "<form action=\"?\" method=\"post\">";
		checker_init();

		echo "<h2>Planetenwahl bestätigen</h2>";
		$tp = Planet::getById($pid);

		echo "<input type=\"hidden\" name=\"choosenplanetid\" value=\"".$pid."\" />";
		echo "Folgender Planet wurde für Euch ausgewählt:<br/><br/>";
		tableStart("Daten",300);
		echo "<tr><th>Koordinaten:</th><td>".$tp."</td></tr>";
		echo "<tr>
			<th>Sonnentyp:</th>
			<td>".$tp->starTypeName."</td></tr>";
		echo "<tr>
			<th>Planettyp:</th>
			<td>".$tp->type()."</td></tr>";
		echo "<tr>
			<th>Felder:</td>
			<td>".$tp->fields." total</td></tr>";
		echo "<tr>
			<th>Temperatur:</td>
			<td>".$tp->temp_from."&deg;C bis ".$tp->temp_to."&deg;C";
		echo "</td></tr>";		
		echo "<tr><th>Ansicht:</th><td style=\"background:#000;text-align:center;\"><img src=\"".$tp->imagePath("m")."\" style=\"border:none;\" alt=\"planet\" /></td></tr>
		</table>";
		echo "<table class='tb'>
		<tr>
		<td>
		Du kannst einmal während des Spiels eine andere Kolonie zum Hauptplaneten bestimmen.
		</td>
		</tr>
		</table>";
        tableStart("Filter",300);
        echo "<tr>
			<th>Sonnentyp:</th>
			<td>
			
			<select name=\"filter_sol_id\" id=\"filter_sol_id\">
			<option value=\"0\">Bitte wählen...</option>";
		$res = dbquery("
		SELECT
			sol_type_id,
			sol_type_name
		FROM
			sol_types
		WHERE
			sol_type_consider=1
		ORDER BY
			sol_type_name;
		");
		while ($sol = mysql_fetch_array($res))
		{
            $selected = 0;

            if ((array_key_exists('filter_s',$_GET) ? $_GET['filter_s'] : null) == $sol['sol_type_id']) {
				$selected = 'selected';
            }
			echo "<option value=\"".$sol['sol_type_id']."\"";
			echo "$selected>".$sol['sol_type_name']."</option>";
		}
		echo "</select>
		
			</td></tr>";
        echo "<tr>
			<th>Planettyp:</th>
			<td><select name=\"filter_planet_id\" id=\"filter_planet_id\">
			<option value=\"0\">Bitte wählen...</option>";
		$res = dbquery("
		SELECT
			type_id,
			type_name
		FROM
			planet_types
		WHERE
			type_consider=1
		AND 
		    type_habitable = 1	
		ORDER BY
			type_name;
		");
		while ($planets = mysql_fetch_array($res))
		{
		    $selected = 0;

		    if ((array_key_exists('filter_p',$_GET) ? $_GET['filter_p'] : null) == $planets['type_id']) {
		        $selected = 'selected';
            }

			echo "<option value=\"".$planets['type_id']."\"";
			echo "$selected>".$planets['type_name']."</option>";
		}
		echo "</select></td></tr>
        </table>";

		tableStart("Bonis dieser Zusammenstellung",600);
		echo "<tr><th>Rohstoff</th>
		<th>".$tp->typeName."</th>";
		echo "<th>".$cu->race->name."</th>";
		echo "<th>".$tp->starTypeName."</th>";
		echo "<th>TOTAL</th></tr>";		
		
		echo "<tr><td class=\"tbldata\">".RES_ICON_METAL."Produktion ".RES_METAL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typeMetal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->metal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starMetal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typeMetal,$cu->race->metal,$tp->starMetal),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_CRYSTAL."Produktion ".RES_CRYSTAL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typeCrystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->crystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starCrystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typeCrystal,$cu->race->crystal,$tp->starCrystal),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_PLASTIC."Produktion ".RES_PLASTIC."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typePlastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->plastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starPlastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typePlastic,$cu->race->plastic,$tp->starPlastic),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_FUEL."Produktion ".RES_FUEL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typeFuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->fuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starFuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typeFuel,$cu->race->fuel,$tp->starFuel),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_FOOD."Produktion ".RES_FOOD."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typeFood,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->food,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starFood,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typeFood,$cu->race->food,$tp->starFood),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_POWER."Produktion Energie</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typePower,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->power,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starPower,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typePower,$cu->race->power,$tp->starPower),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_PEOPLE."Bev&ouml;lkerungswachstum</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typePopulation,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->population,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starPopulation,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typePopulation,$cu->race->population,$tp->starPopulation),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_TIME."Forschungszeit</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typeResearchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->researchTime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starResearchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typeResearchtime,$cu->race->researchTime,$tp->starResearchtime),1,1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_TIME."Bauzeit (Geb&auml;ude)</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->typeBuildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->buildTime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($tp->starBuildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($tp->typeBuildtime,$cu->race->buildTime,$tp->starBuildtime),1,1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_TIME."Fluggeschwindigkeit</td>";
		echo "<td class=\"tbldata\">-</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->fleetSpeedFactor,1)."</td>";
		echo "<td class=\"tbldata\">-</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->race->fleetSpeedFactor,1)."</td></tr>";
		tableEnd();

        echo "<input type=\"submit\" name=\"submit_chooseplanet\" value=\"Auswählen\" />
		<input type=\"button\" onclick=\"setSelectUrl()\"
		value=\"Einen neuen Planeten auswählen\" />
		<input type=\"submit\" name=\"redo\" value=\"Einen neuen Sektor auswählen\" />";
		echo "</form>";
	}	
	elseif ($mode=="choosesector")
	{
		echo "<form action=\"?\" method=\"post\">";
		checker_init();
		echo "<h2>Heimatsektor auswählen</h2>";
		echo "Wählt einen Sektor aus, in dem sich euer Heimatplanet befinden soll:<br/><br/>";

		echo "Anzeigen: <select onchange=\"document.getElementById('img').src='misc/map.image.php'+this.options[this.selectedIndex].value;\">
		<option value=\"?t=".time()."\">Normale Galaxieansicht</option>
		<option value=\"?type=populated&t=".time()."\">Bev&ouml;lkerte Systeme</option>
		
		</select><br/><br/>";
		echo "<img src=\"misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/>";
		
		echo "<map name=\"Galaxy\">\n";
		$sec_x_size=GALAXY_MAP_WIDTH/$sx_num;
		$sec_y_size=GALAXY_MAP_WIDTH/$sy_num;
		$xcnt=1;
		$ycnt=1;
		for ($x=0;$x<GALAXY_MAP_WIDTH;$x+=$sec_x_size)
		{
		 	$ycnt=1;
			for ($y=0;$y<GALAXY_MAP_WIDTH;$y+=$sec_y_size)
			{
				$res = dbquery("
				SELECT
					COUNT(entities.id)										
				FROM
					cells
				INNER JOIN
					entities
					ON entities.cell_id=cells.id
					AND entities.code='s'
					AND sx=".$xcnt."
					AND sy=".$ycnt."
				;
				");
				$arr = mysql_fetch_row($res);				
				
				$res = dbquery("
				SELECT
					COUNT(entities.id)										
				FROM
					cells
				INNER JOIN
					entities
					ON entities.cell_id=cells.id
					AND entities.code='p'
					AND sx=".$xcnt."
					AND sy=".$ycnt."
				;
				");
				$arr2 = mysql_fetch_row($res);

				$res = dbquery("
				SELECT
					COUNT(entities.id) 
				FROM
					cells
				INNER JOIN
				(
					entities
					INNER JOIN
						planets 
						ON planets.id=entities.id
						AND planet_user_id>0
					)
					ON entities.cell_id=cells.id
					AND	cells.sx=".$xcnt."
					AND cells.sy=".$ycnt."
				;");
				$arr3 = mysql_fetch_row($res);
				
				$tt = new Tooltip();
				$tt->addTitle("Sektor $xcnt/$ycnt");
				$tt->addText("Sternensysteme: ".$arr[0]);
				$tt->addText("Planeten: ".$arr2[0]);
				$tt->addGoodCond("Bewohnte Planeten: ".$arr3[0]);
				$tt->addComment("Klickt hier um euren Heimatplaneten in Sektor <b>".$xcnt."/".$ycnt."</b> anzusiedeln!");
		  	echo "<area shape=\"rect\" coords=\"$x,".(GALAXY_MAP_WIDTH-$y).",".($x+$sec_x_size).",".(GALAXY_MAP_WIDTH-$y-$sec_y_size)."\" href=\"?setup_sx=".$xcnt."&amp;setup_sy=".$ycnt."\" alt=\"Sektor $xcnt / $ycnt\" ".$tt.">\n";
		  	$ycnt++;
		  }
		  $xcnt++;
		}
		echo "</map>\n";		
		
		
		echo "</form>";
	}
	elseif ($mode=="race")
	{
		echo "<form action=\"?\" method=\"post\">";
		checker_init();

		echo "<h2>Rasse auswählen</h2>
		Bitte wählt die Rasse eures Volkes aus.<br/>
		Jede Rasse hat Vor- und Nachteile sowie einige Spezialeinheiten:<br/><br/>";
	
		echo "<select name=\"register_user_race_id\" id=\"register_user_race_id\">
		<option value=\"0\">Bitte wählen...</option>";
		$res = dbquery("
		SELECT
			race_id,
			race_name
		FROM
			races
		WHERE
			race_active=1
		ORDER BY
			race_name;
		");
		while ($race = mysql_fetch_array($res))
		{
			echo "<option value=\"".$race['race_id']."\"";
			echo ">".$race['race_name']."</option>";
		}
		echo "</select>";
	
		echo " &nbsp; <input type=\"button\" name=\"random\" id=\"random\" value=\"Zufällige Rasse auswählen\"  onclick=\"rdm()\"/>"; 
      
		// xajax content will be placed in the following cell
		echo "<br/><br/><div id=\"raceInfo\"></div>";
		echo "<br/><br/><input type=\"submit\" name=\"submit_setup1\" id=\"submit_setup1\" value=\"Weiter\" />";
		echo "</form>";
	}
	elseif ($mode=="finished")
	{
		echo "<h2>Einrichtung abgeschlossen</h2>";

		$welcomeText = $tm->getText('welcome_message');
		if ($welcomeText->enabled && !empty($welcomeText->content))
		{
			iBoxStart("Willkommen");
			echo text2html($welcomeText->content);
			iBoxEnd();
			send_msg($cu->id,USER_MSG_CAT_ID, 'Willkommen', $welcomeText->content);
		}
		echo '<input type="button" value="Zum Heimatplaneten" onclick="document.location=\'?page=planetoverview\'" />';
	}
	else
	{
		echo "Fehler";
	}
	echo "</div>";
?>