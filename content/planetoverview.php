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
	// 	File: planetoverview.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about the current planet
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	if ($cp)
	{
		// Planetenname ändern
		if (isset($_GET['action']) && $_GET['action']=="change_name")
		{
			echo "<h2>:: Planetenname/-beschreibung &auml;ndern ::</h2>";
			echo '<script type="text/javascript" src="js/planetname.js"></script>';
			echo "<form action=\"?page=$page\" method=\"POST\" style=\"text-align:center;\">";
			tableStart("Hier den neuen Namen eingeben:");
			echo "<tr><th class=\"tbltitle\">Name:</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" id=\"planet_name\" value=\"".$cp->name."\" length=\"16\" maxlength=\"15\"></td></tr>";
			echo "<tr><th class=\"tbltitle\">Beschreibung:</th><td class=\"tbldata\"><textarea name=\"planet_desc\" rows=\"2\" cols=\"30\">".stripslashes($cp->desc)."</textarea></td></tr>";
			tableEnd();
			echo "<input type=\"submit\" name=\"submit_change\" value=\"Speichern\"> &nbsp; ";
			echo '<input onclick="GenPlot();" type="button" value="Name generieren" /> &nbsp; ';
			echo '<input onclick="document.location=\'?page='.$page.'\';" type="button" value="Abbrechen" /> &nbsp; ';
			echo "</form>";
		}

		// Kolonie aufgeben
		elseif (isset($_GET['action']) && $_GET['action']=="remove")
		{
			if (!$cp->isMain)
			{		
				echo "<h2>:: Kolonie auf diesem Planeten aufheben ::</h2>";
				
				$t = $cp->userChanged()+COLONY_DELETE_THRESHOLD;
				if ($t < time())
				{			
					echo "<form action=\"?page=$page\" method=\"POST\">";
					iBoxStart("Sicherheitsabfrage");
					echo "Willst du die Kolonie auf dem Planeten <b>".$cp->getString()."</b> wirklich l&ouml;schen?";
					iBoxEnd();
					echo "<input type=\"submit\" name=\"submit_noremove\" value=\"Nein, Vorgang abbrechen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_remove\" value=\"Ja, die Kolonie soll aufgehoben werden\">";
					echo "</form>";
				}
				else
				{
					echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
					erst ab <b>".df($t)."</b> gelöscht werden!<br/><br/>
					<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
				}
			}
			else
				err_msg("Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!");
		}

		// Kolonie aufheben ausführen
		elseif (isset($_POST['submit_remove']) && $_POST['submit_remove']!="")
		{
			if (!$cp->isMain)
			{			
				$t = $cp->userChanged()+COLONY_DELETE_THRESHOLD;
				if ($t < time())
				{							
					if (mysql_num_rows(dbquery("SELECT shiplist_id FROM shiplist WHERE shiplist_entity_id='".$cp->id."' AND shiplist_count>0;"))==0)
					{
						if (mysql_num_rows(dbquery("SELECT id FROM fleet WHERE entity_to='".$cp->id."' OR entity_from='".$cp->id."';"))==0)
						{
							if (mysql_num_rows(dbquery("SELECT id FROM planets WHERE id='".$cp->id."' AND planet_user_id='".$s['user']['id']."' AND planet_user_main=0;"))==1)
							{
								if (reset_planet($cp->id))
								{
									//Liest ID des Hauptplaneten aus
									$main_res=dbquery("
									SELECT
										id
									FROM
		                                planets
									WHERE
		                                planet_user_id='".$s['user']['id']."'
		                                AND planet_user_main=1;");
									$main_arr=mysql_fetch_array($main_res);
		
									echo "<br>Die Kolonie wurde aufgehoben!<br>";
									echo "<a href=\"?page=overview&planet_id=".$main_arr['id']."\">Zur &Uuml;bersicht</a>";
		
									$cp->id=NULL;
									$s['currentPlanetId'] = $main_arr['id'];
									$cpid = $main_arr['id'];
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
				else
				{
					echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
					erst ab <b>".df($t)."</b> gelöscht werden!<br/><br/>
					<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
				}
			}
			else
				err_msg("Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!");
		}

		//
		// Felderbelegung anzeigen
		//
		elseif (isset($_GET['sub']) && $_GET['sub']=="fields")
		{
		 	echo "<h1>Felderbelegung des Planeten ".$cp->name."</h1>";
			$cp->resBox();

			echo "<table style=\"width:100%;\"><tr><td style=\"width:50%;vertical-align:top;padding:5px;\">";
			
			$res=dbquery("
			SELECT
				buildings.building_name as name,
				buildings.building_fields * buildlist.buildlist_current_level AS fields,
				buildlist.buildlist_current_level as cnt
			FROM
				buildings,
				buildlist
			WHERE
				buildlist.buildlist_building_id=buildings.building_id
				AND buildlist.buildlist_entity_id=".$cp->id."
			ORDER BY 
				fields DESC;");
			if (mysql_num_rows($res)>0)
			{
				$fcnt=0;
				tableStart("Felderbenutzung durch Geb&auml;ude",'48%');
				echo "<tr><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Stufe</th><th class=\"tbltitle\">Felder</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><th class=\"tbltitle\">".text2html($arr['name'])."</th>";
					echo "<td class=\"tbldata\">".nf($arr['cnt'])."</td>";
					echo "<td class=\"tbldata\">".nf($arr['fields'])."</td></tr>";
					$fcnt+=$arr['fields'];
				}
				echo "<tr><th class=\"tbltitle\" colspan=\"2\">Total</th><td class=\"tbldata\">".nf($fcnt)."</td></tr>";
				tableEnd();
			}
			else
				echo "<i>Keine Geb&auml;ude vorhanden!</i><br/>";

			echo "</td><td style=\"width:50%;vertical-align:top;padding:5px;\">";
			
			$res=dbquery("SELECT
				defense.def_name as name,
				defense.def_fields * deflist.deflist_count AS fields,
				deflist.deflist_count as cnt
			FROM
				defense,
				deflist
			WHERE
				deflist.deflist_def_id=defense.def_id
				AND deflist.deflist_entity_id=".$cp->id."
			ORDER BY 
				fields DESC;");
			if (mysql_num_rows($res)>0)
			{
				$dfcnt=0;
				tableStart("Felderbenutzung durch Verteidigungsanlagen",'48%');
				echo "<tr><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">Felder</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><th class=\"tbltitle\">".text2html($arr['name'])."</th>";
					echo "<td class=\"tbldata\">".nf($arr['cnt'])."</td>";
					echo "<td class=\"tbldata\">".nf($arr['fields'])."</td></tr>";
					$dfcnt+=$arr['fields'];
				}
				echo "<tr><th class=\"tbltitle\" colspan=\"2\">Total</th><td class=\"tbldata\">".nf($dfcnt)."</td></tr>";
				tableEnd();
			}
			else
				echo "<i>Keine Verteidigungsanlagen vorhanden!</i><br/><br/>";
			
			echo "</table>";
			
			echo "<input type=\"button\" value=\"Planeteninfos anzeigen\" onclick=\"document.location='?page=$page'\" />";
		}

		//
		// Planeteninfo anzeigen
		//
		else
		{
			if (isset($_POST['submit_change']) && $_POST['submit_change']!="")
			{
				if ($_POST['planet_name']!="")
				{
					$cp->setNameAndComment($_POST['planet_name'],$_POST['planet_desc']);
				}
			}
	
		 	echo "<h1>&Uuml;bersicht &uuml;ber den Planeten ".$cp->name."</h1>";
			$cp->resBox();

			tableStart("Details",750);
			echo "<tr>
				<td style=\"width:330px;background:#000 url('".IMAGE_PATH."/backgrounds/bg".mt_rand(1,PLANET_BACKGROUND_COUNT).".jpg');\" rowspan=\"".($cp->debrisField ? 11 : 10)."\">
				<img src=\"".IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$cp->image.".".IMAGE_EXT."\" alt=\"Planet\" style=\"width:310px;height:310px\"/>
			</td>";
			echo "<td class=\"tbltitle\">Kennung:</td><td class=\"tbldata\">
				".$cp->id()." [<a href=\"?page=entity&id=".$cp->id()."\">Suchen</a>]</td>
			</tr>";
			echo "<td class=\"tbltitle\">Koordinaten:</td><td class=\"tbldata\">
				".$cp->getCoordinates()." [<a href=\"?page=cell&id=".$cp->cellId()."&amp;hl=".$cp->id()."\">Zeigen</a>]</td>
			</tr>";
			echo "<tr>
				<td class=\"tbltitle\">Sonnentyp:</td><td class=\"tbldata\">
					".$cp->starTypeName." [<a href=\"?page=help&site=stars\">Infos</a>]
					</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Planettyp:</td><td class=\"tbldata\">
					".$cp->type()." [<a href=\"?page=help&site=planets\">Infos</a>]</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Felder:</td><td class=\"tbldata\">
					".nf($cp->fields_used)." benutzt, ".(nf($cp->fields))." total (".round($cp->fields_used/$cp->fields*100)."%)</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Extra-Felder:</td><td class=\"tbldata\">
					".$cp->fields_extra."</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Gr&ouml;sse:</td><td class=\"tbldata\">
					".nf($conf['field_squarekm']['v']*$cp->fields)." km&sup2;</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Temperatur:</td><td class=\"tbldata\">
					".$cp->temp_from."&deg;C bis ".$cp->temp_to."&deg;C &nbsp; <br/>
					<img src=\"images/heat_small.png\" alt=\"Heat\" style=\"width:16px;float:left;\" /> <a href=\"?page=help&amp;site=tempbonus\">Wärmebonus</a>: ";
				$spw = $cp->solarPowerBonus();
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."</span>";
				}
				echo " Energie pro Solarsatellit<br style=\"clear:both;\"/>
				<img src=\"images/ice_small.png\" alt=\"Cold\" style=\"width:16px;float:left;\" /> <a href=\"?page=help&amp;site=tempbonus\"> Kältebonus</a>: ";
				$spw = $cp->fuelProductionBonus();
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."%</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."%</span>";
				}				
			echo " ".RES_FUEL."-Produktion</td></tr>";
			echo "<tr><td class=\"tbltitle\">Produktion:</td><td class=\"tbldata\">
			".RES_ICON_METAL."".nf($cp->prodMetal)." / h<br style=\"clear:both;\" /> 
			".RES_ICON_CRYSTAL."".nf($cp->prodCrystal)." / h<br style=\"clear:both;\" /> 
			".RES_ICON_PLASTIC."".nf($cp->prodPlastic)." / h<br style=\"clear:both;\" /> 
			".RES_ICON_FUEL."".nf($cp->prodFuel)." / h<br style=\"clear:both;\" /> 
			".RES_ICON_FOOD."".nf($cp->prodFood)." / h<br style=\"clear:both;\" /> 
			".RES_ICON_PEOPLE."".nf($cp->prodPeople)." / h<br style=\"clear:both;\" /> 
			".RES_ICON_POWER."".nf($cp->prodPower)."<br style=\"clear:both;\" /> 
			".RES_ICON_POWER_USE."".nf($cp->usePower)."</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Beschreibung:</td>
				<td class=\"tbldata\">
					".($cp->desc!='' ? stripslashes($cp->desc) : '-')."
				</td>
			</tr>";
			if ($cp->debrisField)
			{
				echo '<tr>
				<th class="tbltitle">Trümmerfeld:</th><td class="tbldata">
				'.RES_ICON_METAL."".nf($cp->debrisMetal).'<br style="clear:both;" /> 
				'.RES_ICON_CRYSTAL."".nf($cp->debrisCrystal).'<br style="clear:both;" /> 
				'.RES_ICON_PLASTIC."".nf($cp->debrisPlastic).'<br style="clear:both;" /> 
				</td></tr>';
			}
			
			echo "</table><br/>";
			echo "<input type=\"button\" value=\"Name / Beschreibung des Planeten &auml;ndern\" onClick=\"document.location='?page=$page&action=change_name'\"> ";
			echo "<input type=\"button\" value=\"Felderbelegung anzeigen\" onClick=\"document.location='?page=$page&amp;sub=fields'\"> ";
			if (!$cp->isMain)
			{
				echo "&nbsp;<input type=\"button\" value=\"Kolonie aufheben\" onClick=\"document.location='?page=$page&action=remove'\">";
			}
			else
			{
				echo "<br/><br/>";
				iBoxStart("Hauptplanet");
				echo "<b>Dies ist dein Hauptplanet. Hauptplaneten können nicht invasiert oder aufgegeben werden!</b>";
				iBoxEnd();
			}	
		}
	}
	else
		echo "<h2>: Fehler :</h2> Dieser Planet existiert nicht oder er gehl&ouml;rt nicht dir!";

?>

