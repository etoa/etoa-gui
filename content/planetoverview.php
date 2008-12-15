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
			echo "<tr><th class=\"tbltitle\">Name:</th><td>
			<input type=\"text\" name=\"planet_name\" id=\"planet_name\" value=\"".$cp->name."\" length=\"16\" maxlength=\"15\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Beschreibung:</th><td><textarea name=\"planet_desc\" rows=\"2\" cols=\"30\">".stripslashes($cp->desc)."</textarea></td></tr>";
			tableEnd();
			echo "<input type=\"submit\" name=\"submit_change\" value=\"Speichern\" /> &nbsp; ";
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
					echo "Willst du die Kolonie auf dem Planeten <b>".$cp->name()."</b> wirklich l&ouml;schen?";
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
							if (mysql_num_rows(dbquery("SELECT id FROM planets WHERE id='".$cp->id."' AND planet_user_id='".$cu->id."' AND planet_user_main=0;"))==1)
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
		                                planet_user_id='".$cu->id."'
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
					echo "<tr><th>".text2html($arr['name'])."</th>";
					echo "<td>".nf($arr['cnt'])."</td>";
					echo "<td>".nf($arr['fields'])."</td></tr>";
					$fcnt+=$arr['fields'];
				}
				echo "<tr><th colspan=\"2\">Total</th><td>".nf($fcnt)."</td></tr>";
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
				echo "<tr><th>Name</th><th>Anzahl</th><th>Felder</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><th>".text2html($arr['name'])."</th>";
					echo "<td>".nf($arr['cnt'])."</td>";
					echo "<td>".nf($arr['fields'])."</td></tr>";
					$dfcnt+=$arr['fields'];
				}
				echo "<tr><th colspan=\"2\">Total</th><td>".nf($dfcnt)."</td></tr>";
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

			$tg = ($cfg->param1('planet_temp')+$cfg->param2('planet_temp'))/2;
			$tp = ($cp->temp_from+$cp->temp_to)/2;
			if ($tp<$tg/3)
				$tw = "Kalter Planet";
			elseif ($tp<$tg*2/3)
				$tw = "Gemässigter Planet";
			else
				$tw = "Warmer Planet";
				
			tableStart("Übersicht");
			echo "<tr><td colspan=\"2\" style=\"padding:0px;\">";
			echo "<div style=\"position:relative;height:380px;padding:0px;background:#000 url('images/sunset.jpg');\">";
			echo "<div style=\"position:absolute;left:20px;bottom:20px;\">
			<div style=\"float:left;width:110px;\"><b>Grösse</b></div> ".nf($conf['field_squarekm']['v']*$cp->fields)." km&sup2;<br style=\"clear:left;\"/>
			<div style=\"float:left;width:110px;\"><b>Temperatur</b></div>	".$cp->temp_from."&deg;C bis ".$cp->temp_to."&deg;C ($tw) <br style=\"clear:left;\"/>
			<div style=\"float:left;width:110px;\"><b>System</b></div> <a href=\"?page=cell&amp;id=".$cp->cellId()."&amp;hl=".$cp->id()."\">".$cp->getSectorSolsys()."</a> (Position ".$cp->pos.")<br style=\"clear:left;\"/>
			<div style=\"float:left;width:110px;\"><b>Kennung</b></div> <a href=\"?page=entity&amp;id=".$cp->id()."\">".$cp->id()."</a><br style=\"clear:left;\"/>
			<div style=\"float:left;width:110px;\"><b>Stern</b></div> ".$cp->starTypeName." ".helpLink("stars")."<br style=\"clear:left;\"/>
			<div style=\"float:left;width:110px;\"><b>Planetentyp</b></div> ".$cp->type()." ".helpLink("planets")."<br style=\"clear:left;\"/>
			<div style=\"float:left;width:110px;\"><b>Felder</b></div> ".nf($cp->fields_used)." benutzt (".round($cp->fields_used/$cp->fields*100)."%), ".(nf($cp->fields))." total, ".$cp->fields_extra." extra<br style=\"clear:left;\"/>";
			if ($cp->debrisField)
			{
				echo "<div style=\"float:left;width:110px;\"><b>Trümmerfeld:</b></div> 
				<span class=\"resmetal\">".nf($cp->debrisMetal,0,1)."</span>
				<span class=\"rescrystal\">".nf($cp->debrisCrystal,0,1)."</span>
				<span class=\"resfuel\">".nf($cp->debrisPlastic,0,1)."</span>
				<br style=\"clear:left;\"/>";
			}			
			if ($cp->desc!="")			
				echo "<div style=\"float:left;width:110px;\"><b>Beschreibung</b></div> ".stripslashes($cp->desc)."<br style=\"clear:left;\"/>";
			if ($cp->isMain)				
				echo "<div style=\"float:left;width:110px;\"><b>Hauptplanet</b></div> Hauptplaneten können nicht invasiert oder aufgegeben werden!<br style=\"clear:left;\"/>";
			echo "</div>";
			echo "</div>";
			echo "</td></tr>";
			echo "<tr>
			<th>Produktion:</th><td>
			<div class=\"resmetal\">".nf($cp->prodMetal,0,1)." ".RES_METAL." / h</div> 
			<div class=\"rescrystal\">".nf($cp->prodCrystal,0,1)." ".RES_CRYSTAL." / h</div>
			<div class=\"resplastic\">".nf($cp->prodPlastic,0,1)." ".RES_PLASTIC." / h</div> 
			<div class=\"resfuel\">".nf($cp->prodFuel,0,1)." ".RES_FUEL." / h</div> 
			<div class=\"resfood\">".nf($cp->prodFood,0,1)." ".RES_FOOD." / h</div> 
			<div class=\"respeople\">".nf($cp->prodPeople,0,1)." Einwohner / h</div> 
			<div class=\"respower\">".nf($cp->prodPower,0,1)." Energieproduktion</div> 
			<div class=\"respoweru\">".nf($cp->usePower,0,1)." Energieverbrauch</div></td></tr>
			<tr><th>Temperatureffekt ".helpLink("tempbonus").":</th><td>
			<span style=\"background:url('images/heat_small.png') no-repeat;padding:1px 2px 5px 20px;\" />Wärmebonus: ";
			$spw = $cp->solarPowerBonus();
			if ($spw>=0)
			{
				echo "<span style=\"color:#0f0\">+".$spw."</span>";
			}
			else
			{
				echo "<span style=\"color:#f00\">".$spw."</span>";
			}
			echo " MW </span> <br/>
			<span style=\"background:url('images/ice_small.png') no-repeat;padding:1px 2px 5px 22px;\" />Kältebonus: ";
			$spw = $cp->fuelProductionBonus();
			if ($spw>=0)
			{
				echo "<span style=\"color:#0f0\">+".$spw."%</span>";
			}
			else
			{
				echo "<span style=\"color:#f00\">".$spw."%</span>";
			}				
			echo "</span>
			</td></tr>
			";
			tableEnd();
			
			/*
			
			tableStart("Details",650);
			echo "<tr>
				<td style=\"vertical-align:middle;width:330px;padding:0px;background:#000 url('".IMAGE_PATH."/backgrounds/bg1.jpg');\" rowspan=\"".($cp->debrisField ? 11 : 10)."\">
				<img src=\"".IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$cp->image.".".IMAGE_EXT."\" alt=\"Planet\" style=\"width:310px;height:310px\"/>
			</td>";
			echo "<th style=\"width:110px;\">Kennung:</th>
			<td style=\"width:210px;\">
				".$cp->id()." [<a href=\"?page=entity&amp;id=".$cp->id()."\">Suchen</a>]</td>
			</tr>";
			echo "<tr>
			<th>Koordinaten:</th>
			<td>
				".$cp->getCoordinates()." [<a href=\"?page=cell&amp;id=".$cp->cellId()."&amp;hl=".$cp->id()."\">Zeigen</a>]</td>
			</tr>";
			echo "<tr>
				<th>Sonnentyp:</th>
				<td>
					".$cp->starTypeName." ".helpLink("stars")."
					</td></tr>";
			echo "<tr>
				<th>Planettyp:</th>
				<td>
					".$cp->type()." ".helpLink("planets")."</td></tr>";
			echo "<tr>
				<th>Felder:</th>
				<td>
					".nf($cp->fields_used)." benutzt, ".(nf($cp->fields))." total (".round($cp->fields_used/$cp->fields*100)."%)</td></tr>";
			echo "<tr>
				<th>Extra-Felder:</th>
				<td>
					".$cp->fields_extra."</td></tr>";
			echo "<tr>
				<th>Gr&ouml;sse:</th>
				<td>
					".nf($conf['field_squarekm']['v']*$cp->fields)." km&sup2;</td></tr>";
			echo "<tr>
				<th>Temperatur:</th>
				<td>
					".$cp->temp_from."&deg;C bis ".$cp->temp_to."&deg;C &nbsp; <br/>
					<img src=\"images/heat_small.png\" alt=\"Heat\" style=\"width:16px;float:left;\" />Wärmebonus: ";
				$spw = $cp->solarPowerBonus();
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."</span>";
				}
				echo " MW ".helpLink("tempbonus")."<br style=\"clear:both;\"/>
				<img src=\"images/ice_small.png\" alt=\"Cold\" style=\"width:16px;float:left;\" />
				Kältebonus: ";
				$spw = $cp->fuelProductionBonus();
				if ($spw>=0)
				{
					echo "<span style=\"color:#0f0\">+".$spw."%</span>";
				}
				else
				{
					echo "<span style=\"color:#f00\">".$spw."%</span>";
				}				
			echo " ".helpLink("tempbonus")."</td></tr>";

			echo "<tr>
				<th>Beschreibung:</th>
				<td>
					".($cp->desc!='' ? stripslashes($cp->desc) : '-')."
				</td>
			</tr>";
			if ($cp->debrisField)
			{
				echo "<tr>
				<th>Trümmerfeld:</th><td>
				".RES_ICON_METAL."".nf($cp->debrisMetal)."<br style=\"clear:both;\" /> 
				".RES_ICON_CRYSTAL."".nf($cp->debrisCrystal)."<br style=\"clear:both;\" /> 
				".RES_ICON_PLASTIC."".nf($cp->debrisPlastic)."<br style=\"clear:both;\" /> 
				</td></tr>";
			}
			echo "</table><br/>";
			*/
			echo "<input type=\"button\" value=\"Name / Beschreibung des Planeten &auml;ndern\" onclick=\"document.location='?page=$page&amp;action=change_name'\" /> ";
			echo "<input type=\"button\" value=\"Felderbelegung anzeigen\" onclick=\"document.location='?page=$page&amp;sub=fields'\" /> ";
			if (!$cp->isMain)
			{
				echo "&nbsp;<input type=\"button\" value=\"Kolonie aufheben\" onclick=\"document.location='?page=$page&action=remove'\" />";
			}
	
		}
	}
	else
		echo "<h2>: Fehler :</h2> Dieser Planet existiert nicht oder er gehl&ouml;rt nicht dir!";

?>

