<?PHP

	// Main dialogs
	$xajax->register(XAJAX_FUNCTION,"havenShowShips");
	$xajax->register(XAJAX_FUNCTION,"havenShowTarget");
	$xajax->register(XAJAX_FUNCTION,"havenShowAction");
	$xajax->register(XAJAX_FUNCTION,"havenShowLaunch");

	// Helpers
	$xajax->register(XAJAX_FUNCTION,"havenReset");
	$xajax->register(XAJAX_FUNCTION,"havenTargetInfo");
	$xajax->register(XAJAX_FUNCTION,"havenBookmark");
	$xajax->register(XAJAX_FUNCTION,"havenCheckRes");
	$xajax->register(XAJAX_FUNCTION,"havenCheckPeople");
	$xajax->register(XAJAX_FUNCTION,"havenCheckAction");
	$xajax->register(XAJAX_FUNCTION,"havenAllianceAttack");
	$xajax->register(XAJAX_FUNCTION,"havenCheckSupport");
	

	
	/**
	* Show a list of all ships on the planet
	*/
	function havenShowShips()
	{
		$response = new xajaxResponse();
		ob_start();

		$fleet = unserialize($_SESSION['haven']['fleetObj']);	
		
		//Schiffsinfo
		echo "<div id=\"ship_info\"></div>";
			
		$structure = 0;	
		$shield = 0;
		$weapon = 0;
		$heal = 0;
		$count = 0;
		
		$weapon_tech_a = 1;
		$structure_tech_a = 1;
    	$shield_tech_a = 1;
    	$heal_tech_a = 1;
		
		$struct_tech_special=0;
		$shield_tech_special=0;
		$weaopn_tech_special=0;
		$heal_tech_special=0;
		
		$structure_tech_name = "";
		$shield_tech_name = "";
		$weapon_tech_name = "";
		$heal_tech_name = "";
		$structure_tech_level = 0;
		$shield_tech_level = 0;
		$weapon_tech_level = 0;
		$heal_tech_level = 0;
		

		// Infobox
  	tableStart("Hafen-Infos");
  		
		// Flotten unterwegs
  	echo "<tr><th class=\"tbltitle\">Aktive Flotten:</th><td class=\"tbldata\">";
		if ($fleet->fleetSlotsUsed() > 1)
			echo "<b>".$fleet->fleetSlotsUsed()."</b> Flotten dieses Planeten sind <a href=\"?page=fleets\">unterwegs</a>.";
		elseif ($fleet->fleetSlotsUsed()==1)
			echo "<b>Eine</b> Flotte dieses Planeten ist <a href=\"?page=fleets\">unterwegs</a>.";
		else
			echo "Es sind <b>keine</b> Flotten dieses Planeten unterwegs.";
		echo "</td></tr>";
	
		// Flotten startbar?
  	echo "<tr><th class=\"tbltitle\">Flottenstart:</th><td class=\"tbldata\">";
		if ($fleet->possibleFleetStarts() > 1 )
			echo "<b>".$fleet->possibleFleetStarts()."</b> Flotten k&ouml;nnen von diesem Planeten starten!";
		elseif ($fleet->possibleFleetStarts()==1 )
			echo "<b>Eine</b> Flotte kann von diesem Planeten starten!";
		else
			echo "Es k&ouml;nnen <b>keine</b> Flotten von diesem Planeten starten!";
		echo " (Flottenkontrolle Stufe ".$fleet->fleetControlLevel();
		if ($fleet->specialist->fleetMax>0)
			echo " +3 Flotten durch ".$fleet->specialist->name;
		echo ")</td></tr>";
	
		// Piloten		
  	echo "<tr><th class=\"tbltitle\">Piloten:</th><td class=\"tbldata\">";
		if ($fleet->pilotsAvailable() >1)
			echo "<b>".nf($fleet->pilotsAvailable())."</b> Piloten k&ouml;nnen eingesetzt werden.";
		elseif ($fleet->pilotsAvailable()==1)
			echo "<b>Ein</b> Pilot kann eingesetzt werden.";
		else
			echo "Es sind <b>keine</b> Piloten verf&uuml;gbar.";
		echo "</td></tr>";
				
		// Rasse		
		if ($fleet->raceSpeedFactor() != 1)
		{
			echo "<tr><th class=\"tbltitle\">Rassenbonus:</th><td class=\"tbldata\">";
			echo "Die Schiffe fliegen aufgrund deiner Rasse <b>".$fleet->ownerRaceName."</b> mit ".get_percent_string($fleet->raceSpeedFactor,1)." Geschwindigkeit!";
			echo "</td></tr>";
		}
		
		// Specialist		
		if ($fleet->specialist->fleetSpeedFactor != 1)
		{
			echo "<tr><th class=\"tbltitle\">Spezialistenbonus:</th><td class=\"tbldata\">";
			echo "Die Schiffe fliegen aufgrund des <b>".$fleet->specialist->name."</b> mit ".get_percent_string($fleet->specialist->fleetSpeedFactor,1)." Geschwindigkeit!";
			echo "</td></tr>";
		}
		tableEnd();
	
	
	
						
		// Schiffe auflisten
		$res = dbquery("
		SELECT
			*
		FROM
	    shiplist AS sl
		INNER JOIN
		  ships AS s
		ON
	    s.ship_id=sl.shiplist_ship_id
			AND sl.shiplist_user_id='".$fleet->ownerId()."'
			AND sl.shiplist_entity_id='".$fleet->sourceEntity->Id()."'
	    AND sl.shiplist_count>0
		ORDER BY
			s.special_ship DESC,
			s.ship_launchable DESC,
			s.ship_name;");

		if (mysql_num_rows($res)!=0)
		{
			$ships = $fleet->getShips();
						
	    $tabulator=1;
			echo "<form id=\"shipForm\" onsubmit=\"xajax_havenShowTarget(xajax.getFormValues('shipForm')); return false;\">";
			tableStart();
			//echo "<table class=\"tb\">";
			echo "<tr>
				<th colspan=\"6\">Schiffe wählen</th>
			</tr>";
			echo "<tr>
				<th colspan=\"2\">Typ</th>
				<th width=\"110\">Speed</th>
				<th width=\"110\">Piloten</th>
				<th width=\"110\">Anzahl</th>
				<th width=\"110\">Auswahl</th>
			</tr>\n";
			
			$jsAllShips = array();	// Array for selectable ships
			$launchable = 0;	// Counter for launchable ships
	    while ($arr = mysql_fetch_array($res))
	    {
				//Schiff-info calculation
 				$structure += $arr['ship_structure'] * $arr['shiplist_count'];
  				$shield += $arr['ship_shield'] * $arr['shiplist_count'];
  				$weapon += $arr['ship_weapon'] * $arr['shiplist_count'];
  				$heal += $arr['ship_heal'] * $arr['shiplist_count'];
  				$count += $arr['shiplist_count'];
				
				if (isset($ships[$arr['ship_id']]))
				{
					$val = max(0,$ships[$arr['ship_id']]['count']);
							
				}
				else
				{
					$val = 0;
				}
					
				if($arr['special_ship']==1)
				{
			    echo "<tr>
			    	<td style=\"width:40px;background:#000;\">
			    		<a href=\"?page=ship_upgrade&amp;id=".$arr['ship_id']."\">
			    			<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
			    		</a>
			    	</td>";
					
					//Schiff-Info calculation
			  		$struct_tech_special += $arr['shiplist_special_ship_bonus_structure'] * $arr['special_ship_bonus_structure'];
			  		$shield_tech_special += $arr['shiplist_special_ship_bonus_shield'] * $arr['special_ship_bonus_shield'];
			  		$weaopn_tech_special += $arr['shiplist_special_ship_bonus_weapon'] * $arr['special_ship_bonus_weapon'];
			  		$heal_tech_special += $arr['shiplist_special_ship_bonus_heal'] * $arr['special_ship_bonus_heal'];
				}
				else
				{
			    echo "<tr>
			    	<td style=\"width:40px;background:#000;\">
			    		<a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['ship_id']."\">
			    			<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
			    		</a>
			    	</td>";
				}
	
				// TODO: Rewrite this!
        //Geschwindigkeitsbohni der entsprechenden Antriebstechnologien laden und zusammenrechnen
        $vres=dbquery("
        SELECT
            l.techlist_current_level,
            t.tech_name,
            r.req_level
        FROM
            ship_requirements r
        INNER JOIN
        	techlist l
        	ON r.req_tech_id = l.techlist_tech_id
          AND l.techlist_user_id=".$fleet->ownerId()."
       	INNER JOIN
        	technologies t
	      	ON r.req_tech_id = t.tech_id
          AND t.tech_type_id = '".TECH_SPEED_CAT."'
        WHERE
					r.obj_id=".$arr['ship_id']."
        GROUP BY
            r.id;");
        if ($fleet->raceSpeedFactor()!=1)
            $speedtechstring="Rasse: ".get_percent_string($fleet->raceSpeedFactor(),1)."<br>";
        else
            $speedtechstring="";
			
        if ($fleet->specialist->fleetSpeedFactor!=1)
            $speedtechstring.="Spezialist: ".get_percent_string($fleet->specialist->fleetSpeedFactor,1)."<br>";
        else
            $speedtechstring.="";

        $timefactor=$fleet->raceSpeedFactor()+$fleet->specialist->fleetSpeedFactor-1;
        if (mysql_num_rows($vres)>0)
        {
            while ($varr=mysql_fetch_array($vres))
            {
                if($varr['techlist_current_level']-$varr['req_level']<=0)
                {
                    $timefactor+=0;
                }
                else
                {
                    $timefactor+=($varr['techlist_current_level']-$varr['req_level'])*0.1;
                    $speedtechstring.=$varr['tech_name']." ".$varr['techlist_current_level'].": ".get_percent_string((($varr['techlist_current_level']-$varr['req_level'])/10)+1,1)."<br>";
                }
            }
        }

        $arr['ship_speed']/=FLEET_FACTOR_F;	
	
	
				$actions = explode(",",$arr['ship_actions']);
				$accnt=count($actions);
				if ($accnt>0)
				{
					$acstr = "<br/><b>Fähigkeiten:</b> ";
					$x=0;
					foreach ($actions as $i)
					{
						if ($ac = FleetAction::createFactory($i))
						{
							$acstr.=$ac;
							if ($x<$accnt-1)
								$acstr.=", ";
						}
						$x++;
					}
					$acstr.="";
				}	
	

	      echo "<td ".tm($arr['ship_name'],"<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_middle.".IMAGE_EXT."\" style=\"float:left;margin-right:5px;\">".text2html($arr['ship_shortcomment']."<br/>".$acstr."<br style=\"clear:both;\"/>")).">".$arr['ship_name']."</td>";
	      echo "<td width=\"190\" ".tm("Geschwindigkeit","Grundgeschwindigkeit: ".$arr['ship_speed']." AE/h<br>$speedtechstring").">".nf($arr['ship_speed']*$timefactor)." AE/h</td>";
	      echo "<td width=\"110\">".nf($arr['ship_pilots'])."</td>";
	      echo "<td width=\"110\">".nf($arr['shiplist_count'])."<br/>";
	      
	      echo "</td>";
	      echo "<td width=\"110\">";
	      if ($arr['ship_launchable']==1 && $fleet->pilotsAvailable() > $arr['ship_pilots'])
	      {
	      	echo "<input type=\"text\" 
	      		id=\"ship_count_".$arr['ship_id']."\" 
	      		name=\"ship_count[".$arr['ship_id']."]\" 
	      		size=\"10\" value=\"$val\"  
	      		title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\" 
	      		onclick=\"this.select();\" tabindex=\"".$tabulator."\" 
	      		onkeyup=\"FormatNumber(this.id,this.value,".$arr['shiplist_count'].",'','');\"/>
	      	<br/>
	      	<a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_".$arr['ship_id']."').value=".$arr['shiplist_count'].";document.getElementById('ship_count_".$arr['ship_id']."').select()\">Alle</a> &nbsp; 
	      	<a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_".$arr['ship_id']."').value=0;document.getElementById('ship_count_".$arr['ship_id']."').select()\">Keine</a>";
		      $jsAllShips["ship_count_".$arr['ship_id']]=$arr['shiplist_count'];
		      $launchable++;
	      }
	      else
	      {
	      	echo "-";
	      }
	      echo "</td></tr>\n";
	      $tabulator++;
			}
			echo "<tr><td colspan=\"5\"></td>
			<td class=\"tbldata\">";
			
			// Select all ships button			
			echo "<a href=\"javascript:;\" onclick=\"";
			foreach ($jsAllShips as $k => $v)
			{
				echo "document.getElementById('".$k."').value=".$v.";";
			}
			echo "\">Alle wählen</a>";			
			echo "</td></tr>";
			tableEnd();
		
			// Show buttons if possible
			if ($fleet->possibleFleetStarts() > 0)
			{
				if ($launchable>0)
				{
					echo "<input type=\"submit\" value=\"Weiter zur Zielauswahl &gt;&gt;&gt;\" title=\"Wenn du die Schiffe ausgew&auml;hlt hast, klicke hier um das Ziel auszuw&auml;hlen\" tabindex=\"".($tabulator+1)."\" />";
					if (count($ships)>0)
					{
						echo " &nbsp; <input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
					}
				}
			}
			else
			{
				error_msg("Es k&ouml;nnen nicht noch mehr Flotten starten! Bau zuerst deine Flottenkontrolle aus!",1);
			} 
			echo "</form>";
		}
		else
		{
			error_msg("Es sind keine Schiffe auf diesem Planeten vorhanden!",1);		
		}
		
	
		$response->assign("havenContentShips","innerHTML",ob_get_contents());		
		
		$response->assign("havenContentTarget","innerHTML","");				
		$response->assign("havenContentTarget","style.display",'none');				

		$response->assign("havenContentAction","innerHTML","");				
		$response->assign("havenContentAction","style.display",'none');				

		$response->script("document.forms[0].elements[0].select();");
		ob_end_clean();
		
	
	  return $response;	
	}




	/**
	* Verify ships and show target selector
	*/
	function havenShowTarget($form)
	{
		$response = new xajaxResponse();

		// Get fleet object
		$fleet = unserialize($_SESSION['haven']['fleetObj']);
ob_start();

		// Do some checks
		if (count($form)>0 || $fleet->getShipCount() > 0)
		{
		
			// Add ships
			if (isset($form['ship_count']))
			{
				$fleet->resetShips();
				foreach ($form['ship_count'] as $sid => $cnt)
				{
					if (intval($cnt)>0)
					{
						$fleet->addShip($sid,$cnt);
					}
				}
			}
			
			// Check if there are enough people
			if ($fleet->fixShips())
			{
					//
					// Show ships in fleet
					//
					//ob_start();							
					
					tableStart();
					//echo "<table class=\"tb\">";
					echo "<tr>
						<th colspan=\"5\">Schiffe</th>
					</tr>";
					echo "<tr>
						<th>Anzahl</th>
						<th>Typ</th>
						<th>Piloten</th>
						<th>Speed</th>
						<th>Kosten / 100 AE</th>
					</tr>\n";	
					$shipCount=0;
					foreach ($fleet->getShips() as $sid => $sd)
					{				
						echo "<tr>
						<td>".nf($sd['count'])."</td>
						<td>".$sd['name']."</td>
						<td>".nf($sd['pilots'])."</td>
						<td>".round($fleet->getSpeed() / $sd['speed']*100 / $fleet->sBonusSpeed)."%</td>
						<td>".nf($sd['costs_per_ae'])." ".RES_FUEL."</td></tr>";
						$shipCount++;
					}								
					if ($shipCount>1)
					{
						echo "<tr><td colspan=\"5\">Schnellere Schiffe nehmen im Flottenverband automatisch die Geschwindigkeit des langsamsten Schiffes an, sie brauchen daf&uuml;r aber auch entsprechend weniger Treibstoff!</td></tr>";
					}
					
					
					echo "<tr><td colspan=\"5\">Mögliche Aktionen: ";
					$cnt=0;
					$shipAcCnt = count($fleet->shipActions);
					foreach ($fleet->shipActions as $ac)
					{
						$action = FleetAction::createFactory($ac);
						echo $action;
						if ($cnt< $shipAcCnt-1)
							echo ", ";
						$cnt++;
					}
					echo "</td></tr>";
					
					tableEnd();
					//echo "</table><br/>";					
					$response->assign("havenContentShips","innerHTML",ob_get_contents());				
					ob_end_clean();		
									
					//
					// Show Target form
					//
					ob_start();
					echo "<form id=\"targetForm\" onsubmit=\"xajax_havenShowAction(xajax.getFormValues('targetForm'));return false;\" >";
					
					tableStart();
					//echo "<table class=\"tb\">";
					echo "<tr>
						<th colspan=\"2\">Zielwahl</th>
					</tr>";

					if (isset($fleet->targetEntity))
					{
						$csx = $fleet->targetEntity->sx(); 
						$csy = $fleet->targetEntity->sy(); 
						$ccx = $fleet->targetEntity->cx(); 
						$ccy = $fleet->targetEntity->cy(); 
						$psp = $fleet->targetEntity->pos(); 
					}
					else
					{
						$csx = $fleet->sourceEntity->sx();
						$csy = $fleet->sourceEntity->sy();
						$ccx = $fleet->sourceEntity->cx();
						$ccy = $fleet->sourceEntity->cy();
						$psp = $fleet->sourceEntity->pos();
					}
					
					// Manuelle Auswahl
					echo "<tr><td class=\"tbltitle\" width=\"25%\" rowspan=\"2\">Zielwahl:</td><td class=\"tbldata\" width=\"75%\">
					Manuelle Eingabe: ";
					echo "<input type=\"text\" 
												id=\"man_sx\"
												name=\"man_sx\" 
												size=\"1\" 
												maxlength=\"1\" 
												value=\"$csx\" 
												title=\"Sektor X-Koordinate\" 
												tabindex=\"1\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeydown=\"detectChangeRegister(this,'t1');\"
												onkeyup=\"if (detectChangeTest(this,'t1')) { showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;/&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_sy\" 
												name=\"man_sy\" 
												size=\"1\" 
												maxlength=\"1\" 
												value=\"$csy\" 
												title=\"Sektor Y-Koordinate\" 
												tabindex=\"2\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeydown=\"detectChangeRegister(this,'t2');\"
												onkeyup=\"if (detectChangeTest(this,'t2')) { showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;&nbsp;:&nbsp;&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_cx\" 
												name=\"man_cx\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$ccx\" 
												title=\"Zelle X-Koordinate\" 
												tabindex=\"3\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeydown=\"detectChangeRegister(this,'t3');\"
												onkeyup=\"if (detectChangeTest(this,'t3')) { showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;/&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_cy\" 
												name=\"man_cy\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$ccy\" 
												tabindex=\"4\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeydown=\"detectChangeRegister(this,'t4');\"
												onkeyup=\"if (detectChangeTest(this,'t4')) { showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;&nbsp;:&nbsp;&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_p\" 
												name=\"man_p\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$psp\" 
												title=\"Position des Planeten im Sonnensystem\" 
												tabindex=\"5\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeydown=\"detectChangeRegister(this,'t5');\"
												onkeyup=\"if (detectChangeTest(this,'t5')) { showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
												onkeypress=\"return nurZahlen(event)\"
					/></td></tr>";
					
					echo "<tr><td class=\"tbldata\" width=\"75%\" align=\"left\">Zielfavoriten: ";
							echo "<select name=\"bookmarks\" 
											id=\"bookmarks\" 
											onchange=\"xajax_havenBookmark(xajax.getFormValues('targetForm'));\"
											tabindex=\"6\"
							>\n";
					echo "<option value=\"0\"";
					echo ">Wählen...</option>";
							
					$pRes=dbquery("
								SELECT
									planets.id
								FROM
									planets
								WHERE
									planets.planet_user_id=".$fleet->ownerid()."
								ORDER BY
									planet_user_main DESC,
									planet_name ASC;");
					
					if (mysql_num_rows($pRes)>0)
					{	
						while ($pArr=mysql_fetch_assoc($pRes))
						{
							$ent = Entity::createFactory('p',$pArr['id']);
							echo "<option value=\"".$ent->id()."\"";
							echo ">Eigener Planet: ".$ent."</option>\n";
						}
					}
					
					$bRes=dbquery("
								SELECT
									bookmarks.entity_id,
									bookmarks.comment,
									entities.code      
								FROM
									bookmarks
								INNER JOIN
									entities	
								ON bookmarks.entity_id=entities.id
									AND bookmarks.user_id=".$fleet->ownerid().";");
					
					if (mysql_num_rows($bRes)>0)
					{
						echo "<option value=\"0\"";
						echo ">-------------------------------</option>\n";
						
						while ($bArr=mysql_fetch_assoc($bRes))
						{
							$ent = Entity::createFactory($bArr['code'],$bArr['entity_id']);
							echo "<option value=\"".$ent->id()."\"";
							echo ">".$ent->entityCodeString()." - ".$ent." (".$bArr['comment'].")</option>\n";
						}
					}
					echo "</select>";
					
					echo "</td></tr>";
					
					// Speedfaktor
					echo "<tr>
						<td class=\"tbltitle\" width=\"25%\">Speedfaktor:</td>
						<td class=\"tbldata\" width=\"75%\" align=\"left\">";
							echo "<select name=\"speed_percent\" 
											id=\"duration_percent\" 
											onchange=\"showLoader('duration');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
											tabindex=\"6\"
							>\n";
							for ($x=100;$x>0;$x-=1)
							{
								echo "<option value=\"$x\"";
								if ($fleet->getSpeedPercent() == $x) echo " selected=\"selected\"";
								echo ">".$x."</option>\n";
							}
					echo "</select> %";
					
					echo "</td></tr>";
					
					// Daten anzeigen
					echo "<tr><td width=\"25%\"><b>Ziel-Informationen:</b></td>
						<td class=\"tbldata\" id=\"targetinfo\" style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
							<img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...
						</td></tr>";
					echo "<tr><td>Entfernung:</td>
						<td id=\"distance\">-</td></tr>";
					echo "<tr><td  width=\"25%\">Kosten/100 AE:</td>
						<td class=\"tbldata\" id=\"costae\">".nf($fleet->getCostsPerHundredAE())." t ".RES_FUEL."</td></tr>";
					echo "<tr><td>Geschwindigkeit:</td>
						<td id=\"speed\">".nf($fleet->getSpeed())." AE/h";
					If ($fleet->sBonusSpeed>1)
							echo " (inkl. ".get_percent_string($fleet->sBonusSpeed,1)." Mysticum-Bonus)";
					echo "</td></tr>";
					echo "<tr><td>Dauer:</td>
						<td><span id=\"duration\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landezeit von ".tf($fleet->getTimeLaunchLand()).")</td></tr>";
					echo "<tr><td>Treibstoff:</td>
						<td><span id=\"costs\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landeverbrauch von ".nf($fleet->getCostsLaunchLand())." ".RES_FUEL.")</td></tr>";
					echo "<tr><td>Nahrung:</td>
						<td><span id=\"food\"  style=\"font-weight:bold;\">-</span></td></tr>";
					echo "<tr><td>Piloten:</td>
						<td>".nf($fleet->getPilots());
						If ($fleet->sBonusPilots!=1)
							echo " (inkl. ".get_percent_string(1-$fleet->sBonusPilots,1,1)." Mysticum-Bonus)";
					echo "</td></tr>";
					echo "<tr><td>Bemerkungen:</td>
						<td id=\"comment\">-</td></tr>";
					echo "<tr id=\"allianceAttacks\" style=\"display: none;\"><td class=\"tbldata\">Allianzangriffe</td><td class=\"tbldata\" id=\"alliance\">-</td></tr>";
					tableEnd();
					
					echo "&nbsp;<input tabindex=\"7\" type=\"button\" onclick=\"xajax_havenShowShips()\" value=\"&lt;&lt; Zurück zur Schiffauswahl\" />&nbsp;";
					echo "<input tabindex=\"8\" type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />&nbsp;";
					echo "<div style=\"display:inline;\" id=\"chooseAction\"></div>";
					
					echo "</form>";
					
					$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
					$response->assign("havenContentTarget","style.display",'');			
					
					$response->assign("havenContentAction","innerHTML","");				
					$response->assign("havenContentAction","style.display",'none');				
					
					$response->script("document.getElementById('man_sx').focus();");
					$response->script("xajax_havenTargetInfo(xajax.getFormValues('targetForm'))");
					
					
					
					ob_end_clean();				
					
			}
			else
			{
				$response->alert($fleet->error());
			}		
		}
		else
		{
			$response->alert("Fehler! Es wurden keine Schiffe gewählt oder es sind keine vorhanden!");
		}
		
		
		$_SESSION['haven']['fleetObj']=serialize($fleet);
	  return $response;			
	}

	/**
	* Verify target and show action selector
	*/
	function havenShowAction($form)
	{
		$response = new xajaxResponse();

		// Do some checks
		if (count($form)>0)
		{
			// Get fleet object
			$fleet = unserialize($_SESSION['haven']['fleetObj']);
			
			$absX = (($form['man_sx']-1) * CELL_NUM_X) + $form['man_cx'];
			$absY = (($form['man_sy']-1) * CELL_NUM_Y) + $form['man_cy'];
			if ($fleet->owner->discovered($absX,$absY) == 0)
				$code='u';
			else 
				$code = '';
			
			$res = dbquery("
			SELECT
				entities.id,
				entities.code
			FROM
				entities
			INNER JOIN	
				cells
			ON
				entities.cell_id=cells.id
				AND cells.sx=".$form['man_sx']."
				AND cells.sy=".$form['man_sy']."
				AND cells.cx=".$form['man_cx']."
				AND cells.cy=".$form['man_cy']."
				AND entities.pos=".$form['man_p']."
			");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_row($res);
				if ($code == '')
					$ent = Entity::createFactory($arr[1],$arr[0]);
				else 
					$ent = Entity::createFactory($code,$arr[0]);
					
				if ($fleet->setTarget($ent,$form['speed_percent']))
				{												
					if ($fleet->checkTarget())
					{							
	
						
						// Target infos
						//	
						ob_start();
						tableStart();
						echo "<tr>
							<th colspan=\"2\">Zielinfos</th>
						</tr>";
						echo "<tr><td width=\"25%\"><b>Ziel-Informationen:</b></td>
							<td class=\"tbldata\" id=\"targetinfo\" style=\"padding:16px 2px 2px 60px;color:#fff;height:47px;background:#000 url('".$ent->imagePath()."') no-repeat 3px 3px;\">
								".$ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")
							</td></tr>";
						echo "<tr>
							<td class=\"tbltitle\" width=\"25%\">Speedfaktor:</td>
							<td class=\"tbldata\" width=\"75%\" align=\"left\">";
						echo $fleet->getSpeedPercent();
						echo "%</td></tr>";
						echo "<tr><td class=\"tbltitle\">Entfernung:</td>
							<td id=\"distance\">".nf($fleet->getDistance())." AE</td></tr>";
						echo "<tr><td class=\"tbltitle\">Dauer:</td>
							<td><span id=\"duration\" style=\"font-weight:bold;\">".tf($fleet->getDuration())."</span></td></tr>";
						echo "<tr><td class=\"tbltitle\">Treibstoff:</td>
							<td><span id=\"costs\" style=\"font-weight:bold;\">".nf($fleet->getCosts())." t ".RES_FUEL."</span></td></tr>";
						echo "<tr><td class=\"tbltitle\">Nahrung:</td>
							<td><span id=\"costsFood\" style=\"font-weight:bold;\">".nf($fleet->getCostsFood())." t ".RES_FOOD."</span></td></tr>";
						echo "<tr id=\"supportTime\" style=\"display: none;\"><td class=\"tbltitle\">Supportzeit:</td><td id=\"support\"></td></tr>";
						tableEnd();		
						
						$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
						$response->assign("havenContentTarget","style.display",'');			
						ob_end_clean();
						
						//
						// Action chooser
						//						
						ob_start();
						echo "<form id=\"actionForm\">";
						tableStart();
						echo "<tr>
							<th>Aktionswahl</th>
							<th colspan=\"2\">Ladung</th>
						</tr>";
						echo "<tr><td rowspan=\"9\">";
						$actionsAvailable = 0;
						foreach ($fleet->getAllowedActions() as $ac)
						{
							if ($fleet->getLeader()>0) {
								if ($ac->code() == "alliance") {
									echo "<input type=\"radio\" onchange=\"xajax_havenCheckAction('".$ac->code()."');\" name=\"fleet_action\" value=\"".$ac->code()."\"";

									echo " checked=\"checked\"";
									echo " /><span ".tm($ac->name(),$ac->desc())."> ".$ac." (unterstützen)</span><br/>";
									$actionsAvailable++;
								}
							} else {									
								echo "<input type=\"radio\" onchange=\"xajax_havenCheckAction('".$ac->code()."');\" name=\"fleet_action\" value=\"".$ac->code()."\"";

								if ($actionsAvailable == 0)
									echo " checked=\"checked\"";
								echo " /><span ".tm($ac->name(),$ac->desc())."> ".$ac."</span><br/>";
								$actionsAvailable++;
							}
						}
						if ($actionsAvailable==0)
						{
							echo "<i>Keine Aktion auf dieses Ziel verfügbar!</i><br/>";
						}
						echo "<br/>".$fleet->error();
						
						$tabindex = 1;
						
						echo "</td>
						<th style=\"width:170px;\">
						Freie Kapazität:</th>
						<td style=\"width:150px;\" id=\"resfree\">".nf($fleet->getCapacity())."</td></tr>
						<tr><th>Freie Passagierplätze:</th>
						<td style=\"width:150px;\" id=\"peoplefree\">".nf($fleet->getPeopleCapacity())."</td>
						</td></tr>
						<tr id=\"resbox1\" style=\"display:;\"><th>".RES_ICON_METAL."".RES_METAL."</th>
						<td><input type=\"text\" name=\"res1\" id=\"res1\" value=\"".$fleet->getLoadedRes(1)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(1,this.value)\" /> 
						<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(1,".floor($fleet->sourceEntity->getRes(1)).");\">max</a></td></tr>
						<tr id=\"resbox2\" style=\"display:;\"><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
						<td><input type=\"text\" name=\"res2\" id=\"res2\" value=\"".$fleet->getLoadedRes(2)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(2,this.value)\" /> 
						<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(2,".floor($fleet->sourceEntity->getRes(2)).");\">max</a></td></tr>
						<tr id=\"resbox3\" style=\"display:;\"><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
						<td><input type=\"text\" name=\"res3\" id=\"res3\" value=\"".$fleet->getLoadedRes(3)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(3,this.value)\" /> 
						<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(3,".floor($fleet->sourceEntity->getRes(3)).");\">max</a></td></tr>
						<tr id=\"resbox4\" style=\"display:;\"><th>".RES_ICON_FUEL."".RES_FUEL."</th>
						<td><input type=\"text\" name=\"res4\" id=\"res4\" value=\"".$fleet->getLoadedRes(4)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(4,this.value)\" /> 
						<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(4,".floor($fleet->sourceEntity->getRes(4)).");\">max</a></td></tr>
						<tr id=\"resbox5\" style=\"display:;\"><th>".RES_ICON_FOOD."".RES_FOOD."</th>
						<td><input type=\"text\" name=\"res5\" id=\"res5\" value=\"".$fleet->getLoadedRes(5)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(5,this.value)\" /> 
						<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(5,".floor($fleet->sourceEntity->getRes(5)).");\">max</a></td></tr>
						<tr id=\"resbox6\" style=\"display:;\"><th>".RES_ICON_PEOPLE."Passagiere</th>
						<td><input type=\"text\" name=\"resp\" id=\"resp\" value=\"".$fleet->capacityPeopleLoaded."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckPeople(this.value)\" /> 
						<a href=\"javascript:;\" onclick=\"xajax_havenCheckPeople(".floor($fleet->sourceEntity->people()).");\">max</a></td></tr>
						
						<tr id=\"fetchbox1\" style=\"display:none;\"><th>".RES_ICON_METAL."".RES_METAL."</th>
						<td><input type=\"text\" name=\"fetch1\" id=\"fres1\" value=\"0\" size=\"8\" onkeyup=\"FormatNumber(this.id,this.value, '".$fleet->getTotalCapacity()."', '', '');\"/> 
						<a href=\"javascript:;\" onclick=\"document.getElementById('fres1').value=".$fleet->getTotalCapacity()."\">max</a></td></tr>
						<tr id=\"fetchbox2\" style=\"display:none;\"><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
						<td><input type=\"text\" name=\"fetch2\" id=\"fres2\" value=\"0\" size=\"8\" onkeyup=\"FormatNumber(this.id,this.value, '".$fleet->getTotalCapacity()."', '', '');\"/> 
						<a href=\"javascript:;\" onclick=\"document.getElementById('fres2').value=".$fleet->getTotalCapacity()."\">max</a></td></tr>
						<tr id=\"fetchbox3\" style=\"display:none;\"><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
						<td><input type=\"text\" name=\"fetch3\" id=\"fres3\" value=\"0\" size=\"8\" onkeyup=\"FormatNumber(this.id,this.value, '".$fleet->getTotalCapacity()."', '', '');\"/> 
						<a href=\"javascript:;\" onclick=\"document.getElementById('fres3').value=".$fleet->getTotalCapacity()."\">max</a></td></tr>
						<tr id=\"fetchbox4\" style=\"display:none;\"><th>".RES_ICON_FUEL."".RES_FUEL."</th>
						<td><input type=\"text\" name=\"fetch4\" id=\"fres4\" value=\"0\" size=\"8\" onkeyup=\"FormatNumber(this.id,this.value, '".$fleet->getTotalCapacity()."', '', '');\"/> 
						<a href=\"javascript:;\" onclick=\"document.getElementById('fres4').value=".$fleet->getTotalCapacity()."\">max</a></td></tr>
						<tr id=\"fetchbox5\" style=\"display:none;\"><th>".RES_ICON_FOOD."".RES_FOOD."</th>
						<td><input type=\"text\" name=\"fetch5\" id=\"fres5\" value=\"0\" size=\"8\" onkeyup=\"FormatNumber(this.id,this.value, '".$fleet->getTotalCapacity()."', '', '');\"/> 
						<a href=\"javascript:;\" onclick=\"document.getElementById('fres5').value=".$fleet->getTotalCapacity()."\">max</a></td></tr>
						<tr id=\"fetchbox6\" style=\"display:none;\"><th>".RES_ICON_PEOPLE."Passagiere</th>
						<td><input type=\"text\" name=\"fetchp\" id=\"fresp\" value=\"0\" size=\"8\" onkeyup=\"FormatNumber(this.id,this.value, '".$fleet->getTotalPeopleCapacity()."', '', '');\"/> 
						<a href=\"javascript:;\" onclick=\"document.getElementById('fresp').value=".$fleet->getTotalPeopleCapacity()."\">max</a></td></tr>";
						
						tableEnd();                                                                                  
						
						echo "<input type=\"button\" onclick=\"xajax_havenShowTarget(null)\" value=\"&lt;&lt; Zurück zur Zielwahl\" /> &nbsp; ";
						echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" /> &nbsp; ";
						if ($actionsAvailable>0)
						{
							echo "<input type=\"button\" onclick=\"xajax_havenShowLaunch(xajax.getFormValues('actionForm'))\" value=\"Start! &gt;&gt;&gt;\"  />";
						}
						echo "</form>";			
						
						$response->assign("havenContentAction","innerHTML",ob_get_contents());				
						$response->assign("havenContentAction","style.display",'');			
			
						ob_end_clean();
					}
					else
					{
						$response->alert($fleet->error());				
					}
				}
				else
				{
					$response->alert($fleet->error());				
				}
			}
			else
			{
				$response->alert("Ungültiges Ziel!");				
			}
			
			$_SESSION['haven']['fleetObj']=serialize($fleet);
			
		}
		else
		{
			$response->alert("Fehler! Es wurden keine Ziel gewählt!");
		}
		
	  return $response;			
	}

	/**
	* Launch fleet
	*/
	function havenShowLaunch($form)
	{
		$response = new xajaxResponse();

		// Do some checks
		if (count($form)>0)
		{
			// Get fleet object
			$fleet = unserialize($_SESSION['haven']['fleetObj']);

			ob_start();

			if ($fleet->setAction($form['fleet_action']))
			{
				if ($form['fleet_action']=="fetch")
				{
					$fetch1 = $fleet->fetchResource(1,$form['fetch1']);
					$fetch2 = $fleet->fetchResource(2,$form['fetch2']);
					$fetch3 = $fleet->fetchResource(3,$form['fetch3']);
					$fetch4 = $fleet->fetchResource(4,$form['fetch4']);
					$fetch5 = $fleet->fetchResource(5,$form['fetch5']);
					$fetch6 = $fleet->fetchResource(6,$form['fetchp']);
					$load1 = $fleet->loadResource(1,0,1);
					$load2 = $fleet->loadResource(2,0,1);
					$load3 = $fleet->loadResource(3,0,1);
					$load4 = $fleet->loadResource(4,0,1);
					$load5 = $fleet->loadResource(5,0,1);
					$load6 = $fleet->loadPeople(0);
				}
				else
				{
					$load1 = $fleet->loadResource(1,nf_back($form['res1']),1);
					$load2 = $fleet->loadResource(2,nf_back($form['res2']),1);
					$load3 = $fleet->loadResource(3,nf_back($form['res3']),1);
					$load4 = $fleet->loadResource(4,nf_back($form['res4']),1);
					$load5 = $fleet->loadResource(5,nf_back($form['res5']),1);
				}
				
				if ($fid = $fleet->launch())
				{

					$ac = FleetAction::createFactory($form['fleet_action']);
					tableStart();
					echo "<tr>
						<th colspan=\"2\" style=\"color:#0f0\">Flotte gestartet!</th>
					</tr>";				
					echo "<tr>
						<td style=\"width:50%\"><b>Aktion:</b></td>
						<td style=\"color:".FleetAction::$attitudeColor[$ac->attitude()]."\">".$ac->name()."</td>
					</tr>";
					echo "<tr>
						<td><b>Ladung: ".RES_METAL."</b></td>
						<td>".nf($load1)."</td>
					</tr>";				
					echo "<tr>
						<td><b>Ladung: ".RES_CRYSTAL."</b></td>
						<td>".nf($load2)."</td>
					</tr>";				
					echo "<tr>
						<td><b>Ladung: ".RES_PLASTIC."</b></td>
						<td>".nf($load3)."</td>
					</tr>";				
					echo "<tr>
						<td><b>Ladung: ".RES_FUEL."</b></td>
						<td>".nf($load4)."</td>
					</tr>";				
					echo "<tr>
						<td><b>Ladung: ".RES_FOOD."</b></td>
						<td>".nf($load5)."</td>
					</tr>";				
					tableEnd();
					echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Weitere Flotte starten\" />
					&nbsp; <input type=\"button\" onclick=\"document.location='?page=fleetinfo&amp;id=".$fid."'\" value=\"Flotte beobachten\" />";
	
					$response->assign("havenContentAction","innerHTML",ob_get_contents());				
					$response->assign("havenContentAction","style.display",'');	
					$response->assign('support','innerHTML',tf($fleet->getSupportTime()) );
					ob_end_clean();
					$_SESSION['haven']['fleetObj']=serialize($fleet);				
				
				}
				else
				{
					$response->alert("Fehler! Kann Flotte nicht starten! ".$fleet->error());
				}				
			}
			else
			{
				$response->alert("Fehler! Ungültige Aktion! ".$fleet->error());
			}
		}
		else
		{
			$response->alert("Fehler! Es wurde keine Aktion gewählt!");
		}
		
	  return $response;			
	}





	/**
	* Reset everything
	*/
	function havenReset()
	{
		$response = new xajaxResponse();
		$_SESSION['haven']['fleetObj']=null;
		$response->script("document.location='?page=haven'");
	  return $response;			
	}
	
	/**
	* Shows information about the target
	*/
	function havenTargetInfo($form)
	{
		$response = new xajaxResponse();
		 $alliance = "";
		 $allianceStyle = 'none';
		 $comment = "-";
		 ob_start();
		if ($form['man_sx']!="" && $form['man_sy']!="" && $form['man_cx']!="" && $form['man_cy']!="" && $form['man_p']!=""
		&& $form['man_sx']>0 && $form['man_sy']>0 && $form['man_cx']>0 && $form['man_cy']>0 && $form['man_p']>=0)
		{		
			$absX = (($form['man_sx'] - 1)* CELL_NUM_X) + $form['man_cx'];
			$absY = (($form['man_sy']-1) * CELL_NUM_Y) + $form['man_cy'];	
			$fleet = unserialize($_SESSION['haven']['fleetObj']);

			if ($fleet->owner->discovered($absX,$absY) == 0)
				$code='u';
			else 
				$code = '';
			
			$res = dbquery("
				SELECT
					entities.id,
					entities.code
				FROM
					entities
				INNER JOIN	
					cells
				ON
					entities.cell_id=cells.id
					AND cells.sx=".$form['man_sx']."
					AND cells.sy=".$form['man_sy']."
					AND cells.cx=".$form['man_cx']."
					AND cells.cy=".$form['man_cy']."
					AND entities.pos=".$form['man_p']."
				");
			if (mysql_num_rows($res)>0 && !($code=='u' && $form['man_p']))
			{
				$arr=mysql_fetch_row($res);
				
				if ($code=='')
					$ent = Entity::createFactory($arr[1],$arr[0]);
				else
					$ent = Entity::createFactory($code,$arr[0]);
				
				$fleet->setTarget($ent);
				$fleet->setSpeedPercent($form['speed_percent']);
				$fleet->setLeader(0);
				$allianceAttack = "";
				
				$speedString = nf($fleet->getSpeed())." AE/h";
				If ($fleet->sBonusSpeed>1)
					$speedString .= " (inkl. ".get_percent_string($fleet->sBonusSpeed,1)." Mysticum-Bonus)";
				
				echo "<img src=\"".$ent->imagePath()."\" style=\"float:left;\" >";
				
				echo "<br/>&nbsp;&nbsp; ".$ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")";
				$response->assign('distance','innerHTML',nf($fleet->getDistance())." AE");
				$response->assign('duration','innerHTML',tf($fleet->getDuration())."");
				$response->assign('speed','innerHTML',$speedString);
				$response->assign('costae','innerHTML',nf($fleet->getCostsPerHundredAE())." t ".RES_FUEL."");
				$response->assign('costs','innerHTML',nf($fleet->getCosts())." t ".RES_FUEL."");
				$response->assign('food','innerHTML',nf($fleet->getCostsFood())." t ".RES_FOOD."");
				$response->assign('targetinfo','style.background',"#000");
				
				$action = "<input id=\"cooseAction\" tabindex=\"9\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";
					
				if ($ent->ownerId()>0) {
					$res = dbquery("
						SELECT
							id,
							user_id,
							landtime
						FROM
							fleet
						WHERE
							leader_id>'0'
							AND next_id='".$fleet->sourceEntity->ownerAlliance()."'
							AND entity_to='".$ent->id()."'
						ORDER BY
							landtime ASC;");

					if (mysql_num_rows($res)>0) {
						$alliance .= "<table style=\"width:100%;\">";
						while($arr=mysql_fetch_assoc($res)) {
							$alliance .= "<tr><input type=\"button\" style=\"width:100%;\" onclick=\"xajax_havenAllianceAttack(".$arr["id"].")\" name=\"".$arr["id"]."\" value=\"Flottenleader: ".get_user_nick($arr["user_id"])." Ankunftszeit: ".date("d.m.y, H:i:s",$arr["landtime"])."\"/></tr>";
						}
						$alliance .= "</table>";
						$allianceStyle = '';
					}
				}
				
			}
			else
			{
				echo "<div style=\"color:#f00\">Ziel nicht vorhanden!</div>";
				$response->assign('distance','innerHTML',"Unbekannt");
				$response->assign('targetinfo','style.background',"#f00");
				$action = "&nbsp; ";
			}
			$response->assign('targetinfo','innerHTML',ob_get_contents());
			$response->assign('comment','innerHTML',$comment);
			$response->assign('chooseAction','innerHTML',$action);
			$response->assign('alliance','innerHTML',$alliance);
			$response->assign('allianceAttacks',"style.display",$allianceStyle);
			ob_end_clean(); 
			
			$_SESSION['haven']['fleetObj']=serialize($fleet);

			/*
			ob_start();
			echo "Erlaubte Aktionen: ";
			$cnt=0;
			$entAcCnt = count($ent->allowedFleetActions());
			foreach ($ent->allowedFleetActions() as $ac)
			{
				$action = FleetAction::createFactory($ac);
				echo $action;
				if ($cnt < $entAcCnt -1)
					echo ", ";
				$cnt++;
			}
			$response->assign('comment','innerHTML',ob_get_clean());
			ob_end_clean(); 
			*/
			
		}
	  return $response;					
	}
	
	function havenBookmark($form)
	{
		$response = new xajaxResponse();
		
		$fleet = unserialize($_SESSION['haven']['fleetObj']);
	
		if ($form["bookmarks"])
		{
			$ent = Entity::createFactoryById($form["bookmarks"]);
			$csx = $ent->sx();
			$csy = $ent->sy();
			$ccx = $ent->cx();
			$ccy = $ent->cy();
			$psp = $ent->pos();			
		}
		else
		{
			$ent = $fleet->sourceEntity;
			$csx = $fleet->sourceEntity->sx();
			$csy = $fleet->sourceEntity->sy();
			$ccx = $fleet->sourceEntity->cx();
			$ccy = $fleet->sourceEntity->cy();
			$psp = $fleet->sourceEntity->pos();
		}
		
		$response->assign('man_sx','value',$csx);
		$response->assign('man_sy','value',$csy);
		$response->assign('man_cx','value',$ccx);
		$response->assign('man_cy','value',$ccy);
		$response->assign('man_p','value',$psp);
		
		
		$alliance = "";
		$allianceStyle = 'none';
		ob_start();
				
		$fleet->setTarget($ent);
		$fleet->setSpeedPercent($form['speed_percent']);
		$fleet->setLeader(0);
		$allianceAttack = "";
		
		$speedString = nf($fleet->getSpeed())." AE/h";
		If ($fleet->sBonusSpeed>1)
			$speedString .= " (inkl. ".get_percent_string($fleet->sBonusSpeed,1)." Mysticum-Bonus)";
				
		echo "<img src=\"".$ent->imagePath()."\" style=\"float:left;\" >";
				
		echo "<br/>&nbsp;&nbsp; ".$ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")";
		$response->assign('distance','innerHTML',nf($fleet->getDistance())." AE");
		$response->assign('duration','innerHTML',tf($fleet->getDuration())."");
		$response->assign('speed','innerHTML',$speedString);
		$response->assign('costae','innerHTML',nf($fleet->getCostsPerHundredAE())." t ".RES_FUEL."");
		$response->assign('costs','innerHTML',nf($fleet->getCosts())." t ".RES_FUEL."");
		$response->assign('food','innerHTML',nf($fleet->getCostsFood())." t ".RES_FOOD."");
		$response->assign('targetinfo','style.background',"#000");
				
		$action = "<input id=\"cooseAction\" tabindex=\"7\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";
					
		if ($ent->ownerId()>0) {
			$res = dbquery("
						SELECT
							id,
							user_id,
							landtime
						FROM
							fleet
						WHERE
							leader_id>'0'
							AND next_id='".$fleet->sourceEntity->ownerAlliance()."'
							AND entity_to='".$ent->id()."'
						ORDER BY
							landtime ASC;");

			if (mysql_num_rows($res)>0) {
				$alliance .= "<table style=\"width:100%;\">";
				while($arr=mysql_fetch_assoc($res)) {
					$alliance .= "<tr><input type=\"button\" style=\"width:100%;\" onclick=\"xajax_havenAllianceAttack(".$arr["id"].")\" name=\"".$arr["id"]."\" value=\"Flottenleader: ".get_user_nick($arr["user_id"])." Ankunftszeit: ".date("d.m.y, H:i:s",$arr["landtime"])."\"/></tr>";
				}
		
				$alliance .= "</table>";
				$allianceStyle = '';
			}
		}
		$response->assign('targetinfo','innerHTML',ob_get_contents());
		$response->assign('chooseAction','innerHTML',$action);
		$response->assign('alliance','innerHTML',$alliance);
		$response->assign('allianceAttacks',"style.display",$allianceStyle);
		
		ob_end_clean(); 
		
		$_SESSION['haven']['fleetObj']=serialize($fleet);
		return $response;
	}

	function havenCheckRes($id,$val)
	{
		$response = new xajaxResponse();
		$val = max(0,intval(nf_back($val)));
	
		$fleet = unserialize($_SESSION['haven']['fleetObj']);	
		
		$erg = $fleet->loadResource($id,$val);
		
		$response->assign('res'.$id,'value',nf($erg));
	  
		$response->assign('resfree','innerHTML',nf($fleet->getCapacity())." / ".nf($fleet->getTotalCapacity())	);
		$response->assign('resfree','style.color',"#0f0");
	  
	  $_SESSION['haven']['fleetObj']=serialize($fleet);
	  
	  return $response;					
	}
	
	function havenCheckPeople($val)
	{
		$response = new xajaxResponse();
		$val = max(0,intval(nf_back($val)));
		
		$fleet = unserialize($_SESSION['haven']['fleetObj']);	
		
		$erg = $fleet->loadPeople($val);
		
		$response->assign('resp','value',nf($erg));
		
		$response->assign('peoplefree','innerHTML',nf($fleet->getPeopleCapacity())." / ".nf($fleet->getTotalPeopleCapacity())	);
		$response->assign('peoplefree','style.color',"#0f0");
	  
	  	$_SESSION['haven']['fleetObj']=serialize($fleet);
	  
	  return $response;					
	}
	
	function havenCheckAction($code)
	{
		$response = new xajaxResponse();
		$fleet = unserialize($_SESSION['haven']['fleetObj']);
		ob_start();
		$fleet->resetSupport();
		
		if ($code == "support") {
			//echo "<span ".tm($fleet->getSupport(),$fleet->getSupportDesc())." style=\"font-weight:bold;\">";		
			echo "<form id=\"supportForm\">";
			echo "<input type=\"text\" 
								id=\"hour\"
								name=\"hour\" 
								size=\"1\" 
								maxlength=\"2\" 
								value=\"0\" 
								title=\"Stunden\" 
								tabindex=\"7\"
								autocomplete=\"off\" 
								onfocus=\"this.select()\" 
								onclick=\"this.select()\" 
								onkeydown=\"detectChangeRegister(this,'t1');\"
								onkeyup=\"if (detectChangeTest(this,'t1')) { xajax_havenCheckSupport(xajax.getFormValues('supportForm')); }\"
								onkeypress=\"return nurZahlen(event)\"
	/> h&nbsp;";
	echo "<input type=\"text\" 
								id=\"min\" 
								name=\"min\" 
								size=\"1\" 
								maxlength=\"2\" 
								value=\"0\" 
								title=\"Minuten\" 
								tabindex=\"8\"
								autocomplete=\"off\" 
								onfocus=\"this.select()\" 
								onclick=\"this.select()\" 
								onkeydown=\"detectChangeRegister(this,'t2');\"
								onkeyup=\"if (detectChangeTest(this,'t2')) { xajax_havenCheckSupport(xajax.getFormValues('supportForm')); }\"
								onkeypress=\"return nurZahlen(event)\"
	/> min&nbsp;&nbsp;";
	echo "<input type=\"text\" 
								id=\"second\" 
								name=\"second\" 
								size=\"1\" 
								maxlength=\"2\" 
								value=\"0\" 
								title=\"Sekunden\" 
								tabindex=\"9\"
								autocomplete=\"off\" 
								onfocus=\"this.select()\" 
								onclick=\"this.select()\" 
								onkeydown=\"detectChangeRegister(this,'t3');\"
								onkeyup=\"if (detectChangeTest(this,'t3')) { xajax_havenCheckSupport(xajax.getFormValues('supportForm')); }\"
								onkeypress=\"return nurZahlen(event)\"
	/> s</form>";//</span>";*/
			$response->assign('supportTime',"style.display",'');
			$response->assign('support','innerHTML',ob_get_contents() );
			ob_end_clean();
		}
		else
		{
			$fleet->setSupportTime(0);
			$response->assign("supportTime","style.display",'none');
			$response->assign("support","innerHTML","");
			$response->assign('costs','innerHTML',nf($fleet->getCosts())." t ".RES_FUEL);
			$response->assign('costsFood','innerHTML',"".nf($fleet->getCostsFood())." t ".RES_FOOD."");
			$response->assign('resfree','innerHTML',nf($fleet->getCapacity())." / ".nf($fleet->getTotalCapacity()));
			$response->assign('resfree','style.color',"#0f0");
		}
			$response->assign("peoplefree","innerHTML",$code);
		if ($code=="fetch")
		{
			$response->assign("fetchbox1","style.display",'');
			$response->assign("fetchbox2","style.display",'');
			$response->assign("fetchbox3","style.display",'');
			$response->assign("fetchbox4","style.display",'');
			$response->assign("fetchbox5","style.display",'');
			$response->assign("fetchbox6","style.display",'');
			$response->assign("resbox1","style.display",'none');
			$response->assign("resbox2","style.display",'none');
			$response->assign("resbox3","style.display",'none');
			$response->assign("resbox4","style.display",'none');
			$response->assign("resbox5","style.display",'none');
			$response->assign("resbox6","style.display",'none');
			$response->assign("peoplefree","innerHTML",nf($fleet->getTotalPeopleCapacity()));
			$response->assign("resfree","innerHTML",nf($fleet->getTotalCapacity()));
		}
		else
		{
			$response->assign("fetchbox1","style.display",'none');
			$response->assign("fetchbox2","style.display",'none');
			$response->assign("fetchbox3","style.display",'none');
			$response->assign("fetchbox4","style.display",'none');
			$response->assign("fetchbox5","style.display",'none');
			$response->assign("fetchbox6","style.display",'none');
			$response->assign("resbox1","style.display",'');
			$response->assign("resbox2","style.display",'');
			$response->assign("resbox3","style.display",'');
			$response->assign("resbox4","style.display",'');
			$response->assign("resbox5","style.display",'');
			$response->assign("resbox6","style.display",'');
			$response->assign('peoplefree','innerHTML',nf($fleet->getPeopleCapacity())." / ".nf($fleet->getTotalPeopleCapacity()));
			$response->assign('resfree','innerHTML',nf($fleet->getCapacity())." / ".nf($fleet->getTotalCapacity()));
		}
		$response->assign('resfree','style.color',"#0f0");
		$response->assign('peoplefree','style.color',"#0f0");
		$_SESSION['haven']['fleetObj']=serialize($fleet);
		
		return $response;
	}
	
	function havenAllianceAttack($id)
	{
		$response = new xajaxResponse();
		$fleet = unserialize($_SESSION['haven']['fleetObj']);
		
		$percentageSpeed = 100;
		$comment = "-";
		$fleet->setSpeedPercent($percentageSpeed);
		
		if ($id > 0 && $fleet->getLeader()!=$id) {
			$res = dbquery("
							SELECT
								id,
								user_id,
								landtime
							FROM
								fleet
							WHERE
								leader_id='$id'
								AND id='$id'
								AND next_id='".$fleet->sourceEntity->ownerAlliance()."'
							LIMIT 1;");

			if (mysql_num_rows($res)>0) {
				$arr=mysql_fetch_assoc($res);
				$duration = $fleet->distance / $fleet->getSpeed();	// Calculate duration
				$duration *= 3600;	// Convert to seconds
				$duration = ceil($duration);
				$maxTime = $arr["landtime"] - time() - $fleet->timeLaunchLand - 120;
				
				if ($duration < $maxTime) {
					$percentageSpeed =  ceil(100 * $duration / $maxTime);
					$fleet->setSpeedPercent($percentageSpeed);
					$fleet->setLeader($id);
					$comment = "Unterstützung des Allianzangriffes, mit geschätzter Ankunft: ".date("d.m.y, H:i:s",$arr["landtime"]);
				}
				else $comment = "Tut mir leid, aber den gewählten Angriff können wir nicht mehr erreichen.";

			}
				
				
		}
		elseif ($fleet->getLeader()==$id) $fleet->setLeader(0);
		ob_start();
		for ($x=100;$x>0;$x-=1)
		{
			echo "<option value=\"$x\"";
			if ($percentageSpeed == $x) echo " selected=\"selected\"";
			echo ">".$x."</option>\n";
		}
		$response->assign('duration_percent','innerHTML',ob_get_contents() );
		$response->assign('speed','innerHTML',nf($fleet->getSpeed())." AE/h");
		$response->assign('costae','innerHTML',nf($fleet->getCostsPerHundredAE())." t ".RES_FUEL."");
		$response->assign('duration','innerHTML',tf($fleet->getDuration())."");
		$response->assign('costs','innerHTML',nf($fleet->getCosts())." t ".RES_FUEL."");
		$response->assign('food','innerHTML',nf($fleet->getCostsFood())." t ".RES_FOOD."");
		$response->assign('comment','innerHTML',$comment);
		
		ob_end_clean();
		$_SESSION['haven']['fleetObj']=serialize($fleet);
		
		return $response;	
	} 
	
	function havenCheckSupport($form) {
		
		$response = new xajaxResponse();
		$fleet = unserialize($_SESSION['haven']['fleetObj']);
		ob_start();
		
		$supportTime = $form["second"] + $form["min"]*60 + $form["hour"]*3600;
		$maxTime = $fleet->getSupportMaxTime();
		
		if ($maxTime < $supportTime) {
			$supportTime = $maxTime;
			$hour = floor($maxTime/3600);
			$temp = $maxTime - $hour*3600;
			$minute = floor($temp/60);
			$second = $temp - $minute*60;
			
			$response->assign('hour','value',$hour);
			$response->assign('min','value',$minute);
			$response->assign('second','value',$second);
		}
		
		$fleet->setSupportTime($supportTime);
		
		$fuel = nf($fleet->getCosts())." t ".RES_FUEL;
		$food = nf($fleet->getCostsFood())." t ".RES_FOOD;
		
		if ($supportTime)
		{
			$fuel .= " (+ ".nf($fleet->getSupportFuel())." t ".RES_FUEL." Supportkosten)";
			if ($fleet->getSupportFood())
			{
				$food .= " (+ ".nf($fleet->getSupportFood())." t ".RES_FOOD." Supportkosten)";
			}
		}
		
		$response->assign('costs','innerHTML',$fuel);
		$response->assign('costsFood','innerHTML',$food);
		$response->assign('resfree','innerHTML',nf($fleet->getCapacity())." / ".nf($fleet->getTotalCapacity()));
		$response->assign('resfree','style.color',"#0f0");
		
		ob_end_clean();
		$_SESSION['haven']['fleetObj']=serialize($fleet);
		
		return $response;
	
	}
?>