<?PHP

	// Main dialogs
	$xajax->register(XAJAX_FUNCTION,"havenShowShips");
	$xajax->register(XAJAX_FUNCTION,"havenShowTarget");
	$xajax->register(XAJAX_FUNCTION,"havenShowAction");
	$xajax->register(XAJAX_FUNCTION,"havenShowLaunch");

	// Helpers
	$xajax->register(XAJAX_FUNCTION,"havenReset");
	$xajax->register(XAJAX_FUNCTION,"havenTargetInfo");
	$xajax->register(XAJAX_FUNCTION,"havenCheckRes");

	
	/**
	* Show a list of all ships on the planet
	*/
	function havenShowShips()
	{
		$response = new xajaxResponse();
		ob_start();
						
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
			AND sl.shiplist_user_id='".$_SESSION['haven']['user_id']."'
			AND sl.shiplist_planet_id='".$_SESSION['haven']['planet_id']."'
	    AND sl.shiplist_count>0
		ORDER BY
			s.special_ship DESC,
			s.ship_launchable DESC,
			s.ship_name;");

		if (mysql_num_rows($res)!=0)
		{
			$fleet = unserialize($_SESSION['haven']['fleetObj']);			
			$ships = $fleet->getShips();
						
	    $tabulator=1;
			echo "<form id=\"shipForm\" onsubmit=\"xajax_havenShowTarget(xajax.getFormValues('shipForm')); return false;\">";
			echo "<table class=\"tb\">";
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
			    			<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
			    		</a>
			    	</td>";
				}
				else
				{
			    echo "<tr>
			    	<td style=\"width:40px;background:#000;\">
			    		<a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['ship_id']."\">
			    			<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
			    		</a>
			    	</td>";
				}
	
				// TODO: Rewrite this!
        //Geschwindigkeitsbohni der entsprechenden Antriebstechnologien laden und zusammenrechnen
        $vres=dbquery("
        SELECT
            techlist.techlist_current_level,
            technologies.tech_name,
            ship_requirements.req_req_tech_level
        FROM
            techlist,
            ship_requirements,
            technologies
        WHERE
            ship_requirements.req_ship_id=".$arr['ship_id']."
            AND technologies.tech_type_id='".TECH_SPEED_CAT."'
            AND ship_requirements.req_req_tech_id=technologies.tech_id
            AND technologies.tech_id=techlist.techlist_tech_id
            AND techlist.techlist_tech_id=ship_requirements.req_req_tech_id
            AND techlist.techlist_user_id=".$_SESSION['haven']['user_id']."
        GROUP BY
            ship_requirements.req_id;");
        if ($_SESSION['haven']['race_speed_factor']!=1)
            $speedtechstring="Rasse: ".get_percent_string($_SESSION['haven']['race_speed_factor'],1)."<br>";
        else
            $speedtechstring="";

        $timefactor=$_SESSION['haven']['race_speed_factor'];
        if (mysql_num_rows($vres)>0)
        {
            while ($varr=mysql_fetch_array($vres))
            {
                if($varr['techlist_current_level']-$varr['req_req_tech_level']<=0)
                {
                    $timefactor+=0;
                }
                else
                {
                    $timefactor+=($varr['techlist_current_level']-$varr['req_req_tech_level'])*0.1;
                    $speedtechstring.=$varr['tech_name']." ".$varr['techlist_current_level'].": ".get_percent_string((($varr['techlist_current_level']-$varr['req_req_tech_level'])/10)+1,1)."<br>";
                }
            }
        }

        $arr['ship_speed']/=FLEET_FACTOR_F;	
	

	      echo "<td ".tm($arr['ship_name'],text2html($arr['ship_shortcomment'])).">".$arr['ship_name']."</td>";
	      echo "<td width=\"190\" ".tm("Geschwindigkeit","Grundgeschwindigkeit: ".$arr['ship_speed']." AE/h<br>$speedtechstring").">".nf($arr['ship_speed']*$timefactor)." AE/h</td>";
	      echo "<td width=\"110\">".nf($arr['ship_pilots'])."</td>";
	      echo "<td width=\"110\">".nf($arr['shiplist_count'])."<br/>";
	      
	      echo "</td>";
	      echo "<td width=\"110\">";
	      if ($arr['ship_launchable']==1 && $_SESSION['haven']['people_available']>$arr['ship_pilots'])
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
			infobox_end(1);
		
			// Show buttons if possible
			if ($_SESSION['haven']['fleets_start_possible']>0)
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
			
			// Check if ships are selected
			if ($fleet->getShipCount() >0)
			{
				$fleet->fixShips();
				
				// Check if there are enough people
				if ($_SESSION['haven']['people_available'] >= $fleet->getPilots())
				{
					//
					// Show ships in fleet
					//
					ob_start();							
					
					echo "<table class=\"tb\">";
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
						<td>".round($fleet->getSpeed() / $sd['speed']*100)."%</td>
						<td>".nf($sd['costs_per_ae'])." ".RES_FUEL."</td></tr>";
						$shipCount++;
					}								
					if ($shipCount>1)
					{
						echo "<tr><td colspan=\"5\">Schnellere Schiffe nehmen im Flottenverband automatisch die Geschwindigkeit des langsamsten Schiffes an, sie brauchen daf&uuml;r aber auch entsprechend weniger Treibstoff!</td></tr>";
					}
					echo "</table><br/>";					
					$response->assign("havenContentShips","innerHTML",ob_get_contents());				
					ob_end_clean();		
									
					//
					// Show Target form
					//
					ob_start();
					echo "<form id=\"targetForm\" onsubmit=\"xajax_havenShowAction(xajax.getFormValues('targetForm'));return false;\" >";
					
					echo "<table class=\"tb\">";
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
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Zielwahl:</td><td class=\"tbldata\" width=\"75%\">
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
						<td id=\"speed\">".nf($fleet->getSpeed())." AE/h</td></tr>";
					echo "<tr><td>Dauer:</td>
						<td><span id=\"duration\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landezeit von ".tf($fleet->getTimeLaunchLand()).")</td></tr>";
					echo "<tr><td>Treibstoff:</td>
						<td><span id=\"costs\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landeverbrauch von ".nf($fleet->getCostsLaunchLand())." ".RES_FUEL.")</td></tr>";
					echo "<tr><td>Piloten:</td>
						<td>".nf($fleet->getPilots())."</td></tr>";
					echo "<tr><td>Bemerkungen:</td>
						<td id=\"comment\">-</td></tr>";
					echo "</table><br/>";
					echo "<input tabindex=\"8\" type=\"button\" onclick=\"xajax_havenShowShips()\" value=\"&lt;&lt; Zurück zur Schiffauswahl\" /> &nbsp; ";
					echo "<input tabindex=\"7\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp; ";
					echo "<input tabindex=\"9\" type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
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
					$response->alert("Fehler! Nicht genügend Piloten (".$_SESSION['haven']['people_available']." verfügbar, ".$_SESSION['haven']['fleet']['pilots']." benötigt)!");
				}		
			}
			else
			{
				$response->alert("Fehler! Keine Schiffe ausgewählt!");
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
				$ent = Entity::createFactory($arr[1],$arr[0]);
				
				$fleet->setTarget($ent);
				$fleet->setSpeedPercent($form['speed_percent']);
							
				if ($fleet->sourceEntity->resFuel() >= $fleet->getCosts())
				{							

					//
					// Target infos
					//	
					ob_start();
					echo "<table class=\"tb\">";
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
					echo "</table><br/>";			
					
					$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
					$response->assign("havenContentTarget","style.display",'');			
					ob_end_clean();
					
					//
					// Action chooser
					//						
					ob_start();
					echo "<form id=\"actionForm\">";
					echo "<table class=\"tb\">";
					echo "<tr>
						<th>Aktionswahl</th>
						<th colspan=\"2\">Ladung</th>
					</tr>";
					echo "<tr><td rowspan=\"8\">";
					$actions = FleetAction::getAll();
					
					$actionsAvailable = 0;
					foreach ($actions as $ai)
					{
						// Source and target the same
						if (
						($fleet->sourceEntity->id() == $fleet->targetEntity->id() && $ai->allowSourceEntity()) || 
						($fleet->sourceEntity->ownerId() == $fleet->targetEntity->ownerId() && $fleet->sourceEntity->id() != $fleet->targetEntity->id() && $ai->allowOwnEntities()) ||
						($fleet->sourceEntity->ownerId() != $fleet->targetEntity->ownerId() && $fleet->targetEntity->ownerId()>0 && $ai->allowPlayerEntities()) ||
						($fleet->targetEntity->ownerId() == 0 && $ai->allowNpcEntities()) 
						)
						{
							if (in_array($ai->code(),$fleet->targetEntity->allowedFleetActions()))
							{
								echo "<input type=\"radio\" name=\"fleet_action\" value=\"".$ai->code()."\"";
								if ($actionsAvailable == 0)
									echo " checked=\"checked\"";
								echo " /> 
								<span style=\"font-weight:bold;color:".FleetAction::$attitudeColor[$ai->attitude()]."\">".$ai->name()."</span> ".$ai->desc()."<br/>";
								$actionsAvailable++;
							}
						}
						
						
					}
					if ($actionsAvailable==0)
					{
						echo "<i>Keine Aktion auf dieses Ziel verfügbar!</i>";
					}
					
					$tabindex = 1;
					
					echo "</td>
					<th style=\"width:170px;\">
					Freie Kapazität:</th>
					<td style=\"width:150px;\" id=\"resfree\">".nf($fleet->getCapacity())."</td></tr>
					<tr><th>Freie Passagierplätze:</th>
					<td>".nf($fleet->getPeopleCapacity())."</td>
					</td></tr>
					<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
					<td><input type=\"text\" name=\"res1\" id=\"res1\" value=\"".$fleet->getLoadedRes(1)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(1,this.value)\" /> 
					<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(1,".floor($fleet->sourceEntity->getRes(1)).");\">max</a></td></tr>
					<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
					<td><input type=\"text\" name=\"res2\" id=\"res2\" value=\"".$fleet->getLoadedRes(2)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(2,this.value)\" /> 
					<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(2,".floor($fleet->sourceEntity->getRes(2)).");\">max</a></td></tr>
					<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
					<td><input type=\"text\" name=\"res3\" id=\"res3\" value=\"".$fleet->getLoadedRes(3)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(3,this.value)\" /> 
					<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(3,".floor($fleet->sourceEntity->getRes(3)).");\">max</a></td></tr>
					<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
					<td><input type=\"text\" name=\"res4\" id=\"res4\" value=\"".$fleet->getLoadedRes(4)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(4,this.value)\" /> 
					<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(4,".floor($fleet->sourceEntity->getRes(4)).");\">max</a></td></tr>
					<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
					<td><input type=\"text\" name=\"res5\" id=\"res5\" value=\"".$fleet->getLoadedRes(5)."\" size=\"8\" tabindex=\"".($tabindex++)."\" onblur=\"xajax_havenCheckRes(5,this.value)\" /> 
					<a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(5,".floor($fleet->sourceEntity->getRes(5)).");\">max</a></td></tr>
					<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
					<td><input type=\"text\" name=\"resp\" id=\"resp\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"/></td></tr>					
					</table><br/>";                                                                                   
					
					
					echo "<input type=\"button\" onclick=\"xajax_havenShowTarget(null)\" value=\"&lt;&lt; Zurück zur Zielwahl\" /> &nbsp; ";
					if ($actionsAvailable>0)
					{
						echo "<input type=\"button\" onclick=\"xajax_havenShowLaunch(xajax.getFormValues('actionForm'))\" value=\"Start! &gt;&gt;&gt;\"  /> &nbsp; ";
					}
					echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
					echo "</form>";			
					
					$response->assign("havenContentAction","innerHTML",ob_get_contents());				
					$response->assign("havenContentAction","style.display",'');			
		
					
		
					ob_end_clean();
				}
				else
				{
					$response->alert("Zuwenig Treibstoff! ".nf($fleet->sourceEntity->resFuel())." t ".RES_FUEL." vorhanden, ".nf($fleet->getCosts())." t benötigt.");				
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
				$load1 = $fleet->loadResource(1,$form['res1'],1);
				$load2 = $fleet->loadResource(2,$form['res2'],1);
				$load3 = $fleet->loadResource(3,$form['res3'],1);
				$load4 = $fleet->loadResource(4,$form['res4'],1);
				$load5 = $fleet->loadResource(5,$form['res5'],1);
					
				$fleet->launch();

				$ac = FleetAction::createFactory($form['fleet_action']);
				echo "<table class=\"tb\">";
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
				echo "</table><br/>";
				echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Weitere Flotte starten\" />";




				$response->assign("havenContentAction","innerHTML",ob_get_contents());				
				$response->assign("havenContentAction","style.display",'');			
				ob_end_clean();
				$_SESSION['haven']['fleetObj']=serialize($fleet);				
			}
			else
			{
				$response->alert("Fehler! Ungültige Aktion!");
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
	*
	*/
	function havenTargetInfo($form)
	{
		$response = new xajaxResponse();
		
		if ($form['man_sx']!="" && $form['man_sy']!="" && $form['man_cx']!="" && $form['man_cy']!="" && $form['man_p']!=""
		&& $form['man_sx']>0 && $form['man_sy']>0 && $form['man_cx']>0 && $form['man_cy']>0 && $form['man_p']>=0)
		{		
			ob_start();
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
				$ent = Entity::createFactory($arr[1],$arr[0]);

				$fleet = unserialize($_SESSION['haven']['fleetObj']);
				$fleet->setTarget($ent);
				$fleet->setSpeedPercent($form['speed_percent']);

				echo "<img src=\"".$ent->imagePath()."\" style=\"float:left;\" >";
				
				echo "<br/>&nbsp;&nbsp; ".$ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")";
				$response->assign('distance','innerHTML',nf($fleet->getDistance())." AE");
				$response->assign('duration','innerHTML',tf($fleet->getDuration())."");
				$response->assign('speed','innerHTML',nf($fleet->getSpeed())." AE/h");
				$response->assign('costae','innerHTML',nf($fleet->getCostsPerHundredAE())." t ".RES_FUEL."");
				$response->assign('costs','innerHTML',nf($fleet->getCosts())." t ".RES_FUEL."");
				
				$_SESSION['haven']['fleetObj']=serialize($fleet);
			}
			else
			{
				echo "<div style=\"color:#f00\">Ziel nicht vorhanden!</div>";
				$response->assign('distance','innerHTML',"Unbekannt");
				$response->assign('targetinfo','style.background',"#f00");
			}	
			$response->assign('targetinfo','innerHTML',ob_get_contents());
			ob_end_clean();
		}
	  return $response;					
	}

	function havenCheckRes($id,$val)
	{
		$response = new xajaxResponse();
		$val = max(0,intval($val));
	
		$fleet = unserialize($_SESSION['haven']['fleetObj']);	
		
		$erg = $fleet->loadResource($id,$val);
		
		$response->assign('res'.$id,'value',$erg);
	  
		$response->assign('resfree','innerHTML',nf($fleet->getCapacity())." / ".nf($fleet->getTotalCapacity())	);
		$response->assign('resfree','style.color',"#0f0");
	  
	  $_SESSION['haven']['fleetObj']=serialize($fleet);
	  
	  return $response;					
	}

?>