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
	    $tabulator=1;
			echo "<form id=\"shipForm\">";
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
	/*
	            //Geschwindigkeitsbohni der entsprechenden Antriebstechnologien laden und zusammenrechnen
	            $vres=dbquery("
	            SELECT
	                techlist.techlist_current_level,
	                technologies.tech_name,
	                ship_requirements.req_req_tech_level
	            FROM
	                ".$db_table['techlist'].",
	                ".$db_table['ship_requirements'].",
	                ".$db_table['technologies']."
	            WHERE
	                ship_requirements.req_ship_id=".$arr['ship_id']."
	                AND technologies.tech_type_id='".TECH_SPEED_CAT."'
	                AND ship_requirements.req_req_tech_id=technologies.tech_id
	                AND technologies.tech_id=techlist.techlist_tech_id
	                AND techlist.techlist_tech_id=ship_requirements.req_req_tech_id
	                AND techlist.techlist_user_id=".$s['user']['id']."
	            GROUP BY
	                ship_requirements.req_id;");
	            if ($rarr['race_f_fleettime']!=1)
	                $speedtechstring="Rasse: ".((1-$rarr['race_f_fleettime'])*100)."%<br>";
	            else
	                $speedtechstring="";
	
	            $timefactor=$racefactor;
	            if (mysql_num_rows($vres)>0)
	            {
	                while ($varr=mysql_fetch_array($vres))
	                {
	                    if($varr['techlist_current_level']-$varr['req_req_tech_level']<=0)
	                    {
	                        $timefactor+=0;
	                         $speedtechstring.=$varr['tech_name']." ".$varr['techlist_current_level'].": +0%<br>";
	                    }
	                    else
	                    {
	                        $timefactor+=($varr['techlist_current_level']-$varr['req_req_tech_level'])*0.1;
	                        $speedtechstring.=$varr['tech_name']." ".$varr['techlist_current_level'].": +".(($varr['techlist_current_level']-$varr['req_req_tech_level'])*10)."%<br>";
	                    }
	                }
	            }
	
	
	            if ($_SESSION['haven']['fleet'][$arr['ship_id']]>0)
	                $val = $_SESSION['haven']['fleet'][$arr['ship_id']];
	            else
	                $val=0;
	
	            $arr['ship_speed']/=FLEET_FACTOR_F;
	
	*/	
				
				if (isset($_SESSION['haven']['fleet']['ships'][$arr['ship_id']]) && $_SESSION['haven']['fleet']['ships'][$arr['ship_id']]>0)
				{
					$val = $_SESSION['haven']['fleet']['ships'][$arr['ship_id']];			
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
	      	<a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_".$arr['ship_id']."').value=".$arr['shiplist_count']."\">Alle</a> &nbsp; 
	      	<a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_".$arr['ship_id']."').value=0\">Keine</a>";
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
			infobox_end(1);

			// Select all ships button			
			echo "<input type=\"button\" value=\"Alle wählen\" onclick=\"";
			foreach ($jsAllShips as $k => $v)
			{
				echo "document.getElementById('".$k."').value=".$v.";";
			}
			echo "\" /> &nbsp; ";
		
			// Show buttons if possible
			if ($_SESSION['haven']['fleets_start_possible']>0)
			{
				if ($launchable>0)
				{
					echo "<input type=\"button\" onclick=\"xajax_havenShowTarget(xajax.getFormValues('shipForm'))\" value=\"Weiter zur Zielauswahl &gt;&gt;&gt;\" title=\"Wenn du die Schiffe ausgew&auml;hlt hast, klicke hier um das Ziel auszuw&auml;hlen\" tabindex=\"".($tabulator+1)."\" />";
					if (isset($_SESSION['haven']['fleet']) && $_SESSION['haven']['fleet']!=null)
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

	
		ob_end_clean();
	  return $response;	
	}


	/**
	* Show target selector and calc ships
	*/
	function havenShowTarget($form)
	{
		$response = new xajaxResponse();

		// Do some checks
		if (count($form)>0)
		{
			// Get fleet object
			$fleet = unserialize($_SESSION['haven']['fleetObj']);
		
			// Add ships
			foreach ($form['ship_count'] as $sid => $cnt)
			{
				if (intval($cnt)>0)
				{
					$fleet->addShip($sid,$cnt);
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
						<th>Typ</th>
						<th>Anzahl</th>
						<th>Piloten</th>
						<th>Speed</th>
						<th>Kosten / 100 AE</th>
					</tr>\n";	
					$shipCount=0;
					foreach ($fleet->getShips() as $sid => $sd)
					{				
						echo "<tr><td>".$sd['name']."</td>
						<td>".$sd['count']."</td>
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
					echo "<form id=\"targetForm\">";
					echo "<table class=\"tb\">";
					echo "<tr>
						<th colspan=\"2\">Zielwahl</th>
					</tr>";

					if (isset($_SESSION['haven']['target']) && $_SESSION['haven']['target']>0)
					{
						$ent = Entity::createFactoryById($_SESSION['haven']['target']);
						$ent->loadCoords();
						$csx = $ent->sx; 
						$csy = $ent->sy; 
						$ccx = $ent->cx; 
						$ccy = $ent->cy; 
						$psp = $ent->pos; 
					}
					else
					{
						$csx = $fleet->sourceEntity->sx;
						$csy = $fleet->sourceEntity->sy;
						$ccx = $fleet->sourceEntity->cx;
						$ccy = $fleet->sourceEntity->cy;
						$psp = $fleet->sourceEntity->pos;
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
												tabindex=\"20\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;/&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_sy\" 
												name=\"man_sy\" 
												size=\"1\" 
												maxlength=\"1\" 
												value=\"$csy\" 
												title=\"Sektor Y-Koordinate\" 
												tabindex=\"21\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;&nbsp;:&nbsp;&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_cx\" 
												name=\"man_cx\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$ccx\" 
												title=\"Zelle X-Koordinate\" 
												tabindex=\"22\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;/&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_cy\" 
												name=\"man_cy\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$ccy\" 
												tabindex=\"23\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;&nbsp;:&nbsp;&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_p\" 
												name=\"man_p\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$psp\" 
												title=\"Position des Planeten im Sonnensystem\" 
												tabindex=\"24\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/></td></tr>";
					
					// Speedfaktor
					echo "<tr>
						<td class=\"tbltitle\" width=\"25%\">Speedfaktor:</td>
						<td class=\"tbldata\" width=\"75%\" align=\"left\">";
							echo "<select name=\"speed_percent\" 
											id=\"duration_percent\" 
											onchange=\"showLoader('duration');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
											tabindex=\"25\"
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
					echo "<input type=\"button\" onclick=\"xajax_havenShowShips()\" value=\"&lt;&lt; Zurück zur Schiffauswahl\" /> &nbsp; ";
					echo "<input type=\"button\" onclick=\"xajax_havenShowAction(xajax.getFormValues('targetForm'))\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp; ";
					echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
					echo "</form>";
					
					$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
					$response->assign("havenContentTarget","style.display",'');			
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
	
			//
			// Show ships in fleet
			//
			ob_start();							
			
			echo "<table class=\"tb\">";
			echo "<tr>
				<th colspan=\"5\">Schiffe</th>
			</tr>";
			echo "<tr>
				<th>Typ</th>
				<th>Anzahl</th>
				<th>Piloten</th>
				<th>Speed</th>
				<th>Kosten / 100 AE</th>
			</tr>\n";	
			$shipCount=0;
			foreach ($fleet->getShips() as $sid => $sd)
			{				
				echo "<tr><td>".$sd['name']."</td>
				<td>".$sd['count']."</td>
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
		
		
			ob_start();
			echo "<table class=\"tb\">";
			echo "<tr>
				<th colspan=\"2\">Zielinfos</th>
			</tr>";
			echo "<tr><td width=\"25%\"><b>Ziel-Informationen:</b></td>
				<td class=\"tbldata\" id=\"targetinfo\" style=\"padding:16px 2px 2px 60px;color:#fff;height:47px;background:#000 url('".$fleet->targetEntity->imagePath()."') no-repeat 3px 3px;\">
					".$fleet->targetEntity."
				</td></tr>";
			echo "<tr>
				<td class=\"tbltitle\" width=\"25%\">Speedfaktor:</td>
				<td class=\"tbldata\" width=\"75%\" align=\"left\">";
			echo $fleet->getSpeedPercent();
			echo "%</td></tr>";
			echo "<tr><td class=\"tbltitle\">Entfernung:</td>
				<td id=\"distance\">".$fleet->getDistance()." AE</td></tr>";
			echo "<tr><td class=\"tbltitle\">Dauer:</td>
				<td><span id=\"duration\" style=\"font-weight:bold;\">".tf($fleet->getDuration())."</span></td></tr>";
			echo "<tr><td class=\"tbltitle\">Treibstoff:</td>
				<td><span id=\"costs\" style=\"font-weight:bold;\">".nf($fleet->getCosts())." t ".RES_FUEL."</span></td></tr>";
			echo "</table><br/>";			
			
			$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
			$response->assign("havenContentTarget","style.display",'');			
			ob_end_clean();
			
						
			ob_start();
			echo "<form id=\"actionForm\">";
			echo "<table class=\"tb\">";
			echo "<tr>
				<th colspan=\"2\">Aktionswahl</th>
			</tr>";
		
			
			echo "</table><br/>";
			
			echo "<input type=\"button\" onclick=\"xajax_havenShowTarget()\" value=\"&lt;&lt; Zurück zur Zielwahl\" /> &nbsp; ";
			echo "<input type=\"button\" onclick=\"xajax_havenShowLaunch(xajax.getFormValues('actionForm'))\" value=\"Start! &gt;&gt;&gt;\"  /> &nbsp; ";
			echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
			echo "</form>";			
			
			$response->assign("havenContentAction","innerHTML",ob_get_contents());				
			$response->assign("havenContentAction","style.display",'');			


			ob_end_clean();
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
		$_SESSION['haven']['fleet']=null;

		$response->script("xajax_havenShowShips()");
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