<?PHP

	$xajax->register(XAJAX_FUNCTION,"havenShowShips");
	$xajax->register(XAJAX_FUNCTION,"havenShowTarget");
	$xajax->register(XAJAX_FUNCTION,"havenShowAction");
	$xajax->register(XAJAX_FUNCTION,"havenReset");
	$xajax->register(XAJAX_FUNCTION,"havenTargetInfo");
	
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
	
	      echo "<td ".tm($arr['ship_name'],text2html($arr['ship_shortcomment'])).">".$arr['ship_name']."</td>";
	      // ".tm("Geschwindigkeit","Grundgeschwindigkeit: ".$arr['ship_speed']." AE/h<br>$speedtechstring")."
	      // *$timefactor
	      echo "<td width=\"190\" >".nf($arr['ship_speed'])." AE/h</td>";
	      echo "<td width=\"110\">".nf($arr['ship_pilots'])."</td>";
	      echo "<td width=\"110\">".nf($arr['shiplist_count'])."<br/>";
	      
	      //if ($_SESSION['haven']['people_available']<$arr['shiplist_count']*$arr['ship_pilots'])
	      //    echo "(<span title=\"Mit der momentanen Anzahl Piloten k&ouml;nnen soviel Schiffe gestartet werden\">".floor($people_available/$arr['ship_pilots']).")</span>";
	      echo "</td>";
	      echo "<td width=\"110\">";
	      if ($arr['ship_launchable']==1)
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
	* Show target selector and calc ships
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
						<td class=\"tbldata\" id=\"targetinfo\" style=\"padding:16px 2px 2px 60px;background:#000;color:#fff;height:47px;\">
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
	* Show action selector
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
					echo "<tr><td rowspan=\"6\">";
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
					
					echo "</td>
					<td>".RES_ICON_METAL."".RES_METAL."</td><td><input type=\"text\" name=\"res1\" value=\"0\" size=\"5\"/></td></tr>
					<tr><td>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td><td><input type=\"text\" name=\"res2\" value=\"0\" size=\"5\"/></td></tr>
					<tr><td>".RES_ICON_PLASTIC."".RES_PLASTIC."</td><td><input type=\"text\" name=\"res3\" value=\"0\" size=\"5\"/></td></tr>
					<tr><td>".RES_ICON_FUEL."".RES_FUEL."</td><td><input type=\"text\" name=\"res4\" value=\"0\" size=\"5\"/></td></tr>
					<tr><td>".RES_ICON_FOOD."".RES_FOOD."</td><td><input type=\"text\" name=\"res5\" value=\"0\" size=\"5\"/></td></tr>
					<tr><td>".RES_ICON_PEOPLE."Passagiere</td><td><input type=\"text\" name=\"res6\" value=\"0\" size=\"5\"/></td></tr>					
					</table><br/>";                                                                                   
					
					
					echo "<input type=\"button\" onclick=\"xajax_havenShowTarget(null)\" value=\"&lt;&lt; Zurück zur Zielwahl\" /> &nbsp; ";
					//echo "<input type=\"button\" onclick=\"xajax_havenShowLaunch(xajax.getFormValues('actionForm'))\" value=\"Start! &gt;&gt;&gt;\"  /> &nbsp; ";
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
		}
		else
		{
			$response->alert("Fehler! Es wurden keine Ziel gewählt!");
		}
		
	  return $response;			
	}


	function havenReset()
	{
		$response = new xajaxResponse();
		$response->script("document.location='?page=haven'");
	  return $response;			
	}
	
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
				
				echo $ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")";
				$response->assign('targetinfo','style.background',"#000 url('".$ent->imagePath()."') no-repeat 3px 3px;");
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
				$response->assign('targetinfo','style.background',"#000");
			}	
			$response->assign('targetinfo','innerHTML',ob_get_contents());
			ob_end_clean();
		}
	  return $response;					
	}

?>