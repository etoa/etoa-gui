<?PHP

	$xajax->register(XAJAX_FUNCTION,"havenShowShips");
	$xajax->register(XAJAX_FUNCTION,"havenShowTarget");
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
			// Create needed fleet variables
			$_SESSION['haven']['fleet']=null;
			$_SESSION['haven']['fleet']['costs_per_ae']=0;
			$_SESSION['haven']['fleet']['time_launch_land']=0;
			$_SESSION['haven']['fleet']['costs_launch_land']=0;
			$_SESSION['haven']['fleet']['pilots']=0;
			$_SESSION['haven']['fleet']['capacity']=0;
			$_SESSION['haven']['fleet']['people_capacity']=0;
			$_SESSION['haven']['fleet']['speed_percent']=100;
			$_SESSION['haven']['fleet']['ships']=array();
			
			$ships = array();
			$shipCount=0;
			// Check each ship
			foreach ($form['ship_count'] as $sid => $cnt)
			{
				if (intval($cnt)>0)
				{
					$res = dbquery("
					SELECT
						*
					FROM
		        ships
		      INNER JOIN
		        shiplist
					ON
		        ship_id=shiplist_ship_id
						AND shiplist_user_id='".$_SESSION['haven']['user_id']."'
						AND shiplist_planet_id='".$_SESSION['haven']['planet_id']."'
		        AND ship_id=".$sid."
		        AND shiplist_count>0;");
					if (mysql_num_rows($res)>0)
					{
						$arr = mysql_fetch_array($res);	
						$cnt = min($cnt,$arr['shiplist_count']);
						
						$ships[$sid] = array(
						"count" => $cnt,
						"speed" => $arr['ship_speed'],
						"fuel_use" => $arr['ship_fuel_use'] * $cnt,
						"name" => $arr['ship_name'],
						"pilots" => $arr['ship_pilots'] * $cnt,
						);
						
						if (!isset($_SESSION['haven']['fleet']['speed']))
						{
							$_SESSION['haven']['fleet']['speed'] = $arr['ship_speed'];
						}
						else
						{
							$_SESSION['haven']['fleet']['speed'] = min($_SESSION['haven']['fleet']['speed'], $arr['ship_speed']);
						}					
						$_SESSION['haven']['fleet']['time_launch_land'] = max($_SESSION['haven']['fleet']['time_launch_land'], $arr['ship_time2land'] + $arr['ship_time2start']);
						$_SESSION['haven']['fleet']['costs_launch_land'] += 2 * ($arr['ship_fuel_use_launch'] + $arr['ship_fuel_use_landing']) * $cnt;						
						$_SESSION['haven']['fleet']['pilots'] += $arr['ship_pilots'] * $cnt;
						$_SESSION['haven']['fleet']['capacity'] += $arr['ship_capacity'] * $cnt;
						$_SESSION['haven']['fleet']['people_capacity'] += $arr['ship_people_capacity'] * $cnt;
						$shipCount++;
					}
				}
			}
			
			// Check if ships are selected
			if ($shipCount>0)
			{
				// Calc Costs for all ships
				foreach ($ships as $sid => $sd)
				{
					$ships[$sid]['costs_per_ae'] = $sd['fuel_use'] * $_SESSION['haven']['fleet']['speed'] / $sd['speed'];
					$_SESSION['haven']['fleet']['costs_per_ae'] += $ships[$sid]['costs_per_ae'];
					
					$_SESSION['haven']['fleet']['ships'][$sid] = $sd['count'];
				}
				
				// Check if there are enough people
				if ($_SESSION['haven']['people_available'] >= $_SESSION['haven']['fleet']['pilots'])
				{
					// Show checked ships
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
					foreach ($ships as $sid => $sd)
					{				
						echo "<tr><td>".$sd['name']."</td>
						<td>".$sd['count']."</td>
						<td>".nf($sd['pilots'])."</td>
						<td>".round($_SESSION['haven']['fleet']['speed'] / $sd['speed']*100)."%</td>
						<td>".nf($sd['costs_per_ae'])." ".RES_FUEL."</td></tr>";
					}								
					if ($shipCount>1)
					{
						echo "<tr><td colspan=\"5\">Schnellere Schiffe nehmen im Flottenverband automatisch die Geschwindigkeit des langsamsten Schiffes an, sie brauchen daf&uuml;r aber auch entsprechend weniger Treibstoff!</td></tr>";
					}
					echo "</table><br/>";					
					$response->assign("havenContentShips","innerHTML",ob_get_contents());				
					ob_end_clean();		
									
					
					ob_start();
					echo "<form id=\"targetForm\">";
					echo "<table class=\"tb\">";
					echo "<tr>
						<th colspan=\"2\">Zielwahl</th>
					</tr>";

					$csx = $_SESSION['haven']['planet_sx'];
					$csy = $_SESSION['haven']['planet_sy'];
					$ccx = $_SESSION['haven']['planet_cx'];
					$ccy = $_SESSION['haven']['planet_cy'];
					$psp = $_SESSION['haven']['planet_p'];

					// Manuelle Auswahl
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Zielwahl:</td><td class=\"tbldata\" width=\"75%\">
					Manuelle Eingabe: ";
					echo "<input type=\"text\" 
												id=\"man_sx\"
												name=\"man_sx\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$csx\" 
												title=\"Sektor X-Koordinate\" 
												tabindex=\"20\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/>&nbsp;/&nbsp;";
					echo "<input type=\"text\" 
												id=\"man_sy\" 
												name=\"man_sy\" 
												size=\"2\" 
												maxlength=\"2\" 
												value=\"$csy\" 
												title=\"Sektor Y-Koordinate\" 
												tabindex=\"21\"
												autocomplete=\"off\" 
												onfocus=\"this.select()\" 
												onclick=\"this.select()\" 
												onkeyup=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
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
												onkeyup=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
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
												onkeyup=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
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
												onkeyup=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
												onkeypress=\"return nurZahlen(event)\"
					/></td></tr>";
					
					// Speedfaktor
					echo "<tr>
						<td class=\"tbltitle\" width=\"25%\">Speedfaktor:</td>
						<td class=\"tbldata\" width=\"75%\" align=\"left\">
							<select name=\"speed_percent\" 
											id=\"duration_percent\" 
											onchange=\"upd_values();\"
											tabindex=\"25\"
							>\n";
							for ($x=1;$x>0.1;$x-=0.1)
							{
								$perc = $x*100;
								echo "<option value=\"$x\"";
								if ($_SESSION['haven']['fleet']['speed_percent'] * 100==$perc) echo " selected=\"selected\"";
								echo ">$perc</option>\n";
							}
					echo "</select> %</td></tr>";
					
					// Daten anzeigen
					echo "<tr><td width=\"25%\"><b>Zielinfos:</b></td>
						<td class=\"tbldata\" id=\"targetinfo\">
							<img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...
						</td></tr>";
					echo "<tr><td  width=\"25%\">Kosten/100 AE:</td>
						<td class=\"tbldata\">".nf($_SESSION['haven']['fleet']['costs_per_ae'])." t</td></tr>";
					echo "<tr><td>Geschwindigkeit:</td>
						<td id=\"speed\">".nf($_SESSION['haven']['fleet']['speed'])." AE/h</td></tr>";
					echo "<tr><td>Entfernung:</td>
						<td id=\"distance\">-</td></tr>";
					echo "<tr><td>Dauer:</td>
						<td id=\"duration\">- (inkl. Start- und Landezeit von ".tf($_SESSION['haven']['fleet']['time_launch_land']).")</td></tr>";
					echo "<tr><td>Treibstoff:</td>
						<td id=\"costs\">- (inkl. Start- und Landeverbrauch von ".nf($_SESSION['haven']['fleet']['costs_launch_land'])." ".RES_FUEL.")</td></tr>";
					echo "<tr><td>Piloten:</td>
						<td>".nf($_SESSION['haven']['fleet']['pilots'])."</td></tr>";
					echo "<tr><td>Bemerkungen:</td>
						<td id=\"comment\">-</td></tr>";
					echo "</table><br/>";
					echo "<input type=\"button\" onclick=\"xajax_havenShowShips()\" value=\"&lt;&lt; Zurück zur Schiffauswahl\" /> &nbsp; ";
					echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
					echo "</form>";
					
					$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
					$response->assign("havenContentTarget","style.display",'');			
					$response->script("document.getElementById('fleet_sx').focus();");
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
	


			
			
			if (false)
			{

				
				ob_start();
						
				echo "<form id=\"targetForm\">";
				echo "<table class=\"tb\">";
				echo "<tr>
					<th colspan=\"6\">Ziel wählen</th>
				</tr>";				
						
				echo "</table>";
				
				$response->assign("havenContentTarget","innerHTML",ob_get_contents());				
				$response->assign("havenContentTarget","style.display",'');				
				ob_end_clean();
			}			
			
		}
		else
		{
			$response->alert("Fehler! Es wurden keine Schiffe gewählt oder es sind keine vorhanden!");
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
		ob_start();
		$ct = new Cell(array($form['man_sx'],$form['man_sy'],$form['man_cx'],$form['man_cy']));
		if ($ct->isValid())
		{
			$cs = new Cell(array($_SESSION['haven']['planet_sx'],$_SESSION['haven']['planet_sy'],$_SESSION['haven']['planet_cx'],$_SESSION['haven']['planet_cy']));
			echo "<b>Zelle:</b> ".$ct.", <b>Typ:</b> ".$ct->getType(1);
			
			$dist = round($cs->distance($ct),1);
			$response->assign('distance','innerHTML',$dist." AE");
		}
		else
		{
			echo "<div style=\"color:#f00\">".$cs->getError()."</div>";
		}	
		$response->assign('targetinfo','innerHTML',ob_get_contents());
		ob_end_clean();
	  return $response;					
	}

?>