<?PHP

$xajax->register(XAJAX_FUNCTION,'searchUser');
$xajax->register(XAJAX_FUNCTION,'getFlightTargetInfo');
$xajax->register(XAJAX_FUNCTION,'formatNumbers');

$xajax->register(XAJAX_FUNCTION,'setupShowRace');

$xajax->register(XAJAX_FUNCTION,'sendMsg');


//Listet gefundene User auf
function searchUser($val,$field_id='user_nick',$box_id='citybox',$separator=";")
{
	$outNick = "";
	$temp = "";
	$nicks = explode($separator,$val);
	foreach ($nicks as $nick)
	{
		if (strlen($temp)>0) $outNick.=$temp.=";";
		$val=$nick;
		$temp=$val;
	}
	$sOut = "";
	$nCount = 0;
	
	$res=dbquery("SELECT user_nick FROM users WHERE user_nick LIKE '".$val."%' LIMIT 20;");
	if (mysql_num_rows($res)>0)
	{
		while($arr=mysql_fetch_row($res))
		{
			$nCount++;
			$sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('".$field_id."').value='".htmlentities($arr[0])."';document.getElementById('".$box_id."').style.display = 'none';\">".htmlentities($arr[0])."</a>";
			$sLastHit = $arr[0];
		}
	}
	
	if($nCount > 20)
	{
		$sOut = "";
	}
	
	$objResponse = new xajaxResponse();
	
	if(strlen($sOut) > 0)
	{
		$sOut = "".$sOut."";
		$objResponse->script("document.getElementById('".$box_id."').style.display = \"block\"");
	}
	else
	{
		$objResponse->script("document.getElementById('".$box_id."').style.display = \"none\"");
	}
	
	//Wenn nur noch ein User in frage kommt, diesen Anzeigen
	if($nCount == 1)
	{
		$objResponse->script("document.getElementById('".$box_id."').style.display = \"none\"");
		$objResponse->script("document.getElementById('".$field_id."').value = \"".$outNick.$sLastHit."\"");
		$objResponse->script("document.getElementById('".$field_id."').focus()");
	}
	$objResponse->assign($box_id, "innerHTML", $sOut);
	
	return $objResponse;
}


function getFlightTargetInfo($f,$sx1,$sy1,$cx1,$cy1,$p1)
{
	global $conf, $s;
	$objResponse = new xajaxResponse();
	ob_start();
	$launch = true;
	
	$sx=intval($f['sx']);
	$sy=intval($f['sy']);
	$cx=intval($f['cx']);
	$cy=intval($f['cy']);
	$p=intval($f['p']);
		
	if ($sx<1)
	{
		$sx=1;
		$objResponse->assign("sx","value",1);										
	}
	if ($sy<1)
	{
		$sy=1;
		$objResponse->assign("sy","value",1);										
	}
	if ($cx<1)
	{
		$cx=1;
		$objResponse->assign("cx","value",1);										
	}
	if ($cy<1)
	{
		$cy=1;
		$objResponse->assign("cy","value",1);										
	}
	if ($sx>$conf['num_of_sectors']['p1'])
	{
		$sx=$conf['num_of_sectors']['p1'];
		$objResponse->assign("sx","value",$sx);										
	}
	if ($sy>$conf['num_of_sectors']['p2'])
	{
		$sy=$conf['num_of_sectors']['p2'];
		$objResponse->assign("sy","value",$sy);										
	}
	if ($cx>$conf['num_of_cells']['p1'])
	{
		$cx=$conf['num_of_cells']['p1'];
		$objResponse->assign("cx","value",$cx);										
	}
	if ($cy>$conf['num_of_cells']['p2'])
	{
		$cy=$conf['num_of_cells']['p2'];
		$objResponse->assign("cy","value",$cy);										
	}
	if ($p<1)
	{
		$p=1;
		$objResponse->assign("p","value",$p);										
	}
	if ($p>$conf['num_planets']['p2'])
	{
		$p=$conf['num_planets']['p2'];
		$objResponse->assign("p","value",$p);										
	}

	// Total selected missiles
	$total=0;

	// Calc speed
	if (isset($f['count']))
	{
		foreach ($f['speed'] as $k => $v)
		{
			if (!isset($speed) && $f['count'][$k]>0)
			{
				$speed = $v;
				$total+=$f['count'][$k];
			}
			elseif ($f['count'][$k]>0)
			{
				$speed = min($v,$speed);
				$total+=$f['count'][$k];
			}
		}
		
		// Calc range
		foreach ($f['range'] as $k => $v)
		{
			if (!isset($range) && $f['count'][$k]>0)
			{
				$range = $v;
			}
			elseif ($f['count'][$k]>0)
			{
				$range = min($v,$range);
			}
		}
	}
	
	if ($total>0)
	{
		if ($p>0)
		{
			$res = dbquery("
			SELECT
				planet_name,
				user_nick,
				user_id,
				entities.cell_id,
				planets.id

			FROM 
				entities
			INNER JOIN 
				cells 
			ON 
				entities.cell_id = cells.id
				AND entities.pos=".intval($p)."
				AND cells.sx=".intval($sx)."
				AND cells.sy=".intval($sy)."
				AND cells.cx=".intval($cx)."
				AND cells.cy=".intval($cy)."
			INNER JOIN
				planets
			ON 
				entities.id=planets.id
			LEFT JOIN
				users
			ON planet_user_id=user_id				
				
			");
			$out = mysql_num_rows($res);
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				if ($arr['planet_name']!='')
				{
					$out = "<b>Planet:</b> ".$arr['planet_name'];
				}
				else
				{
					$out = "<i>Unbenannter Planet</i>";
				}
				if ($arr['user_id']>0) 
				{
					$out.=" <b>Besitzer:</b> ".$arr['user_nick'];
					if ($s->getInstance()->user_id==$arr['user_id'] && $arr['user_id']>0)
					{
						$out.=' (Eigener Planet)';								
						$objResponse->assign("targetinfo","style.color",'#f00');								
						$launch=false;
					}
					else
					{
						$objResponse->assign("targetinfo","style.color",'#0f0');								
					}
				}
				else
				{			
					$objResponse->assign("targetinfo","style.color",'#f00');								
					$launch=false;
				}
				$objResponse->assign("targetinfo","innerHTML",$out);								
				$objResponse->assign("targetcell","value",$arr['cell_id']);								
				$objResponse->assign("targetplanet","value",$arr['id']);								
			}			
			else
			{
				$objResponse->assign("targetinfo","innerHTML","Hier existiert kein Planet!");				
				$objResponse->assign("targetinfo","style.color",'#f00');		
				$launch=false;						
				$objResponse->assign("targetcell","value",0);								
				$objResponse->assign("targetplanet","value",0);								
			}

			// Calc time and distance
			$nx=$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
			$ny=$conf['num_of_cells']['p2'];		// Anzahl Zellen X
			$ae=$conf['cell_length']['v'];			// Länge vom Solsys in AE
			$np=$conf['num_planets']['p2'];			// Max. Planeten im Solsys
			$dx = abs(((($sx-1) * $nx) + $cx) - ((($sx1-1) * $nx) + $cx1));
			$dy = abs(((($sy-1) * $nx) + $cy) - ((($sy1-1) * $nx) + $cy1));
			$sd = sqrt(pow($dx,2)+pow($dy,2));			// Distanze zwischen den beiden Zellen
			$sae = $sd * $ae;											// Distance in AE units
			if ($sx1==$sx && $sy1==$sy && $cx1==$cx && $cy1=$cy)
				$ps = abs($p-$p1)*$ae/4/$np;				// Planetendistanz wenn sie im selben Solsys sind
			else
				$ps = ($ae/2) - (($p)*$ae/4/$np);	// Planetendistanz wenn sie nicht im selben Solsys sind
			$ssae = $sae + $ps;
			$timeforflight = $ssae / $speed * 3600;
					
			$objResponse->assign("time","innerHTML",tf($timeforflight));								
			$objResponse->assign("timeforflight","value",$timeforflight);								
			$objResponse->assign("distance","innerHTML",nf($ssae)." AE");								
			if ($ssae > $range)
			{
				$objResponse->assign("distance","style.color","#f00");		
				$objResponse->append("distance","innerHTML"," (zu weit entfernt, ".nf($range)." max)");								
				$launch=false;						
			}
			else
			{
				$objResponse->assign("distance","style.color","#0f0");		
			}
			$objResponse->assign("speed","innerHTML",round($speed,2)." AE/h");			

		}
		else
		{
			$launch=false;
		}
	}
	else
	{
		$objResponse->assign("targetinfo","innerHTML","Keine Raketen gewählt!");								
		$launch=false;		
	}
	
	if ($launch)
	{
		$objResponse->assign("launchbutton","style.color",'#0f0');				
		$objResponse->assign("launchbutton","disabled",false);				
	}
	else
	{
		$objResponse->assign("launchbutton","style.color",'#f00');				
		$objResponse->assign("launchbutton","disabled",true);				
	}
	
	$objResponse->append("targetinfo","innerHTML",ob_get_contents());				
	ob_end_clean();
  return $objResponse;	
}


// Formatiert Zahlen
function formatNumbers($field_id,$val,$format=0,$max)
{
	$objResponse = new xajaxResponse();
	ob_start();
	$val = str_replace('`', '', $val);
	$val = str_replace('k', '000', $val);
	$val = str_replace('m', '000000', $val);
	
	if($max!="")
	{
		$val = min($val,$max);
	}
	
	$val = abs(intval($val));
	
	if(is_integer($val))	
	{
		if($format==1)
		{
			$out = nf($val);
		}
		else
		{
			$out = $val;
		}
	}
	else
	{
		$out = 0;
	}

	$objResponse->assign($field_id,"value",$out);
	
	$objResponse->assign("population_info","innerHTML",ob_get_contents());
	ob_end_clean();
  return $objResponse;	
}

//Zeigt Rasseninfos an
function setupShowRace($val)
{
	$objResponse = new xajaxResponse();
	defineImagePaths();
	if ($val>0)
	{
	$res=dbquery("
		SELECT 
			* 
		FROM 
			races 
		WHERE 
			race_id='$val'
	;");
	$arr=mysql_fetch_array($res);
	
	ob_start();
	
	echo text2html($arr['race_comment'])."<br/><br/>";
	tableStart('',300);
	echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
	if ($arr['race_f_metal']!=1)
	{
		echo "<tr><th>".RES_ICON_METAL."Produktion von ".RES_METAL.":</td><td>".get_percent_string($arr['race_f_metal'],1)."</td></tr>";
	}
	if ($arr['race_f_crystal']!=1)
	{
		echo "<tr><th>".RES_ICON_CRYSTAL."Produktion von ".RES_CRYSTAL.":</td><td>".get_percent_string($arr['race_f_crystal'],1)."</td></tr>";
	}
	if ($arr['race_f_plastic']!=1)
	{
		echo "<tr><th>".RES_ICON_PLASTIC."Produktion von ".RES_PLASTIC.":</td><td>".get_percent_string($arr['race_f_plastic'],1)."</td></tr>";
	}
	if ($arr['race_f_fuel']!=1)
	{
		echo "<tr><th>".RES_ICON_FUEL."Produktion von ".RES_FUEL.":</td><td>".get_percent_string($arr['race_f_fuel'],1)."</td></tr>";
	}
	if ($arr['race_f_food']!=1)
	{
		echo "<tr><th>".RES_ICON_FOOD."Produktion von ".RES_FOOD.":</td><td>".get_percent_string($arr['race_f_food'],1)."</td></tr>";
	}
	if ($arr['race_f_power']!=1)
	{
		echo "<tr><th>".RES_ICON_POWER."Produktion von Energie:</td><td>".get_percent_string($arr['race_f_power'],1)."</td></tr>";
	}
	if ($arr['race_f_population']!=1)
	{
		echo "<tr><th>".RES_ICON_PEOPLE."Bevölkerungswachstum:</td><td>".get_percent_string($arr['race_f_population'],1)."</td></tr>";
	}
	if ($arr['race_f_researchtime']!=1)
	{
		echo "<tr><th>".RES_ICON_TIME."Forschungszeit:</td><td>".get_percent_string($arr['race_f_researchtime'],1,1)."</td></tr>";
	}
	if ($arr['race_f_buildtime']!=1)
	{
		echo "<tr><th>".RES_ICON_TIME."Bauzeit:</td><td>".get_percent_string($arr['race_f_buildtime'],1,1)."</td></tr>";
	}
	if ($arr['race_f_fleettime']!=1)
	{
		echo "<tr><th>".RES_ICON_TIME."Fluggeschwindigkeit:</td><td>".get_percent_string($arr['race_f_fleettime'],1)."</td></tr>";
	}
	tableEnd();
	tableStart('',500);
	
	echo  "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
	$res=dbquery("
	SELECT 
		ship_id
	FROM 
		ships 
	WHERE 
  	ship_race_id='".$val."' 
  	AND ship_buildable=1 
  	AND special_ship=0;");
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_array($res))
		{
			$ship = new Ship($arr['ship_id']);
			echo "<tr><td style=\"background:black;\"><img src=\"".$ship->imgPath()."\" style=\"width:40px;height:40px;border:none;\" alt=\"ship".$ship->id."\" /></td>
			<th style=\"width:180px;\">".text2html($ship->name)."</th>
			<td>".text2html($ship->shortComment)."</td></tr>";
		}
	}
	else
		echo "<tr><td colspan=\"3\">Keine Rassenschiffe vorhanden</td></tr>";
	
	tableEnd();
	tableStart('',500);
	echo  "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
	$res=dbquery("
	SELECT 
		def_id,
		def_name,
		def_shortcomment 
	FROM 
		defense 
	WHERE 
  	def_race_id='".$val."' 
  	AND def_buildable=1;");
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_array($res))
		{
   	  $s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT;
			echo "<tr><td style=\"background:black;\"><img src=\"".$s_img."\" style=\"width:40px;height:40px;border:none;\" alt=\"def".$arr['def_id']."\" /></td>
			<th style=\"width:180px;\">".text2html($arr['def_name'])."</th>
			<td>".text2html($arr['def_shortcomment'])."</td></tr>";
		}
	}
	else
		echo "<tr><td colspan=\"3\">Keine Rassenverteidigung vorhanden</td></tr>";
	
		
	tableEnd();

	echo "<br/><br/><input type=\"submit\" name=\"submit_setup1\" id=\"submit_setup1\" value=\"Weiter\" />";
	
 	$objResponse->assign('raceInfo', 'innerHTML', ob_get_contents());
 	ob_end_clean();
	}
	else
	{
	 	$objResponse->assign('raceInfo', 'innerHTML',"Bitte Rasse auswählen!");
	}
  return $objResponse;
}

function sendMsg($userString, $subject, $message)
{
	 $objResponse = new xajaxResponse();
	
	 $userArr = explode(";", $userString);
	 $senderId = $_SESSION['user_id'];
	 foreach ($userArr as $userToNick)
	 {
		  $uid = get_user_id($userToNick);
		  if ($uid> 0 )
		  {
			  // Prüfe Ignore
			  $res = dbquery("
					SELECT
						  COUNT(ignore_id)
					 FROM
						  message_ignore
					 WHERE
						  ignore_owner_id=".$uid."
						  AND ignore_target_id=".$senderId."
					  LIMIT 1
				  ;");
			  $arr=mysql_fetch_row($res);
			  if ($arr[0] == 0)
			  {
					//// Prüfe Titel
					$check_subject = check_illegal_signs($subject);
					if($check_subject=="")
					{
						 Message::sendFromUserToUser($senderId,$uid,$subject,$message);
						 $out = "Nachricht wurde an <b>".$userToNick."</b> gesendet! ";
					 }
					 else
					 {
						  $out = "Du hast ein unerlaubtes Zeichen ( ".$check_subject." ) im Betreff!<br/>";
					  }
			 }
			 else
			 {
				 $out = "Dieser Benutzer hat dich ignoriert, die Nachricht wurde nicht gesendet!<br/>";
			 }
		  }
		  else
		  {
			  $out = "Der Benutzer <b>".$userToNick."</b> existiert nicht!<br/>";
		  }
	 }
	 $objResponse->addScriptCall('bindings()');
	$objResponse->append('info', "innerHTML", $out);

	return $objResponse;
}
?>