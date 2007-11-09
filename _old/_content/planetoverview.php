<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: buildings.php													//
	// Topic: Bauhof-Modul				 									//
	// Version: 0.1																	//
	// Letzte Änderung: 10.05.2006 Lamborghini			i//
	//////////////////////////////////////////////////

	// BEGIN SKRIPT //

	if ($c->id>0)
	{
		// Planetenname ändern
		if ($_GET['action']=="change_name")
		{
			echo "<h2>:: Planetenname/-beschreibung &auml;ndern ::</h2>";
			echo '<script type="text/javascript" src="inc/planetname.js"></script>';
			echo "<form action=\"?page=$page\" method=\"POST\" style=\"text-align:center;\">";
			infobox_start("Hier den neuen Namen eingeben:",1);
			echo "<tr><th class=\"tbltitle\">Name:</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" id=\"planet_name\" value=\"".$c->name."\" length=\"16\" maxlength=\"15\"></td></tr>";
			echo "<tr><th class=\"tbltitle\">Beschreibung:</th><td class=\"tbldata\"><textarea name=\"planet_desc\" rows=\"2\" cols=\"30\">".stripslashes($c->desc)."</textarea></td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" name=\"submit_change\" value=\"Speichern\"> &nbsp; ";
			echo '<input onclick="GenPlot();" type="button" value="Name generieren" /> &nbsp; ';
			echo '<input onclick="document.location=\'?page='.$page.'\';" type="button" value="Abbrechen" /> &nbsp; ';
			echo "</form>";
		}

		// Kolonie aufgeben
		elseif ($_GET['action']=="remove")
		{
			echo "<h2>:: Kolonie auf diesem Planeten aufheben ::</h2>";
			echo "<form action=\"?page=$page\" method=\"POST\">";
			infobox_start("Sicherheitsabfrage");
			echo "Willst du die Kolonie auf dem Planeten <b>".$c->getString()."</b> wirklich l&ouml;schen?";
			infobox_end();
			echo "<input type=\"submit\" name=\"submit_noremove\" value=\"Nein, Vorgang abbrechen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_remove\" value=\"Ja, die Kolonie soll aufgehoben werden\">";
			echo "</form>";
		}

		// Kolonie aufheben ausführen
		elseif ($_POST['submit_remove']!="")
		{
			if (mysql_num_rows(dbquery("SELECT shiplist_id FROM ".$db_table['shiplist']." WHERE shiplist_planet_id='".$c->id."' AND (shiplist_count>0 OR shiplist_build_count>0);"))==0)
			{
				if (mysql_num_rows(dbquery("SELECT fleet_id FROM ".$db_table['fleet']." WHERE fleet_planet_to='".$c->id."' OR fleet_planet_from='".$c->id."';"))==0)
				{
					if (mysql_num_rows(dbquery("SELECT planet_id FROM ".$db_table['planets']." WHERE planet_id='".$c->id."' AND planet_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND planet_user_main=0;"))==1)
					{
						if (reset_planet($c->id))
						{
							//Liest ID des Hauptplaneten aus
							$main_res=dbquery("
							SELECT
								planet_id
							FROM
                                ".$db_table['planets']."
							WHERE
                                planet_user_id='".$_SESSION[ROUNDID]['user']['id']."'
                                AND planet_user_main=1;");
							$main_arr=mysql_fetch_array($main_res);

							echo "<br>Die Kolonie wurde aufgehoben!<br>";
							echo "<a href=\"?page=overview&planet_id=".$main_arr['planet_id']."\">Zur &Uuml;bersicht</a>";

							$c->id=NULL;
							$_SESSION[ROUNDID]['currentPlanetId'] = $main_arr['planet_id'];
							$cpid = $main_arr['planet_id'];
						}
						else
							err_msg("Beim Aufheben der Kolonie trat ein Fehler auf! Bitte wende dich an einen Game-Admin!");
					}
					else
						err_msg("Der Planet ist aktuell nicht ausgew&auml;hlt, er geh&ouml;rt nicht dir oder er ist ein Hauptplanet!");
				}
				else
			 		err_msg("Kolonie kann nicht gel&ouml;scht werden da Schiffe von/zu diesem Planeten unterwegs sind!");
			}
			else
				err_msg("Kolonie kann nicht gel&ouml;scht werden da noch Schiffe auf dem Planeten stationiert sind oder Schiffe noch im Bau sind!");
		}

		//
		// Felderbelegung anzeigen
		//
		elseif ($_GET['sub']=="fields")
		{
		 	echo "<h1>Felderbelegung des Planeten ".$c->name."</h1>";
			$c->resBox();

			$res=dbquery("
			SELECT
				buildings.building_name as name,
				buildings.building_fields * buildlist.buildlist_current_level AS fields,
				buildlist.buildlist_current_level as cnt
			FROM
				".$db_table['buildings'].",
				".$db_table['buildlist']."
			WHERE
				buildlist.buildlist_building_id=buildings.building_id
				AND buildlist.buildlist_planet_id=".$c->id."
			ORDER BY 
				fields DESC;");
			if (mysql_num_rows($res)>0)
			{
				$fcnt=0;
				infobox_start("Felderbenutzung durch Geb&auml;ude",1);
				echo "<tr><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Stufe</th><th class=\"tbltitle\">Felder</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><th class=\"tbltitle\">".text2html($arr['name'])."</th>";
					echo "<td class=\"tbldata\">".text2html($arr['cnt'])."</td>";
					echo "<td class=\"tbldata\">".text2html($arr['fields'])."</td></tr>";
					$fcnt+=$arr['fields'];
				}
				echo "<tr><th class=\"tbltitle\" colspan=\"2\">Total</th><td class=\"tbldata\">$fcnt</td></tr>";
				infobox_end(1);
			}
			else
				echo "<i>Keine Geb&auml;ude vorhanden!</i><br/>";

			$res=dbquery("SELECT
				defense.def_name as name,
				defense.def_fields * deflist.deflist_count AS fields,
				deflist.deflist_count as cnt
			FROM
				".$db_table['defense'].",
				".$db_table['deflist']."
			WHERE
				deflist.deflist_def_id=defense.def_id
				AND deflist.deflist_planet_id=".$c->id."
			ORDER BY 
				fields DESC;");
			if (mysql_num_rows($res)>0)
			{
				$fcnt=0;
				infobox_start("Felderbenutzung durch Verteidigungsanlagen",1);
				echo "<tr><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">Felder</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><th class=\"tbltitle\">".text2html($arr['name'])."</th>";
					echo "<td class=\"tbldata\">".text2html($arr['cnt'])."</td>";
					echo "<td class=\"tbldata\">".text2html($arr['fields'])."</td></tr>";
					$fcnt+=$arr['fields'];
				}
				echo "<tr><th class=\"tbltitle\" colspan=\"2\">Total</th><td class=\"tbldata\">$fcnt</td></tr>";
				infobox_end(1);
			}
			else
				echo "<i>Keine Verteidigungsanlagen vorhanden!</i><br/>";

			echo "<br/><input type=\"button\" value=\"Zur&uuml;k\" onclick=\"document.location='?page=$page'\" />";
		}

		//
		// Planeteninfo anzeigen
		//
		else
		{
			if ($_POST['submit_change']!="")
			{
				if ($_POST['planet_name']!="")
				{
					$signs=check_illegal_signs("<a¨!&ad >;");
					$check_name = check_illegal_signs($_POST['planet_name']);
					$check_desc = check_illegal_signs($_POST['planet_desc']);
					if ($check_name=="" && $check_desc=="")
					{
						dbquery("UPDATE ".$db_table['planets']." SET planet_name='".$_POST['planet_name']."',planet_desc='".addslashes($_POST['planet_desc'])."' WHERE planet_id='".$c->id."';");
						$c->name=$_POST['planet_name'];
						$c->desc=$_POST['planet_desc'];
					}
					else
						echo "Unerlaubtes Zeichen (".$signs.") im Planetennamen oder in der Beschreibung!<br>";
				}
			}

		 	echo "<h1>&Uuml;bersicht &uuml;ber den Planeten ".$c->name."</h1>";
			$c->resBox();
			echo "<table class=\"tbl\">";
			echo "<tr><td width=\"310\" style=\"background:#000\" rowspan=\"8\"><img src=\"".IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$c->image.".gif\" width=\"310\" height=\"310\"/></td>";
			echo "<td class=\"tbltitle\">Sonnensystem</td><td class=\"tbldata\">".$c->getSectorSolsys()." [<a href=\"?page=solsys&id=".$c->solsys_id."\">Zeigen</a>]</td></tr>";
			echo "<tr><td class=\"tbltitle\">Sonnentyp</td><td class=\"tbldata\">".$c->sol_type_name."</td></tr>";
			echo "<tr><td class=\"tbltitle\">Planettyp</td><td class=\"tbldata\">".$c->type_name."</td></tr>";
			echo "<tr><td class=\"tbltitle\">Felder</td><td class=\"tbldata\">".$c->fields_used." benutzt, ".$c->fields." total</td></tr>";
			echo "<tr><td class=\"tbltitle\">Extra-Felder</td><td class=\"tbldata\">".$c->fields_extra."</td></tr>";
			echo "<tr><td class=\"tbltitle\">Gr&ouml;sse</td><td class=\"tbldata\">".nf($conf['field_squarekm']['v']*$c->fields)." km&sup2;</td></tr>";
			echo "<tr><td class=\"tbltitle\">Temperatur</td><td class=\"tbldata\">".$c->temp_from."&deg;C bis ".$c->temp_to."&deg;C</td></tr>";
			echo "<tr><td class=\"tbltitle\">Beschreibung</td><td class=\"tbldata\">".stripslashes($c->desc)."</td></tr>";
			echo "</table><br/>";
			echo "<input type=\"button\" value=\"Name / Beschreibung des Planeten &auml;ndern\" onClick=\"document.location='?page=$page&action=change_name'\"> ";
			echo "<input type=\"button\" value=\"Felderbelegung anzeigen\" onClick=\"document.location='?page=$page&amp;sub=fields'\"> ";
			if (!$c->isMain)
				echo "&nbsp;<input type=\"button\" value=\"Kolonie aufheben\" onClick=\"document.location='?page=$page&action=remove'\">";
		}
	}
	else
		echo "<h2>: Fehler :</h2> Dieser Planet existiert nicht oder er gehl&ouml;rt nicht dir!";

?>

