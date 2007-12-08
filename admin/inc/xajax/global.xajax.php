<?PHP

function planetSelectorByCell($form,$function,$show_user_id=0)
{
	global $db_table;
	$objResponse = new xajaxResponse();
	if ($form['cell_sx']!=0 && $form['cell_sy']!=0 && $form['cell_cx']!=0 && $form['cell_cy']!=0)
	{
		$res=dbquery("
		SELECT
			cell_id,
			cell_solsys_num_planets,
			cell_solsys_solsys_sol_type
		FROM
			".$db_table['space_cells']."
		WHERE
			cell_sx='".$form['cell_sx']."'
			AND cell_sy='".$form['cell_sy']."'
			AND cell_cx='".$form['cell_cx']."'
			AND cell_cy='".$form['cell_cy']."'
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			if($arr['cell_solsys_num_planets']>0 && $arr['cell_solsys_solsys_sol_type']>0)
			{
				$pres=dbquery("
				SELECT
					planet_id,
					planet_name,
					planet_user_id,
					planet_solsys_pos,
					user_nick
				FROM
					".$db_table['planets']."
				LEFT JOIN
					".$db_table['users']."
					ON planet_user_id=user_id
				WHERE
					planet_solsys_id=".$arr['cell_id'].";
				");
				$nr=mysql_num_rows($pres);
				if ($nr>0)
				{
					$out="<select name=\"planet_id\" size=\"$nr\" onchange=\"xajax_".$function."(this.options[this.selectedIndex].value);\">";
					while ($parr=mysql_fetch_array($pres))
					{
						if ($show_user_id==1)
						{
							$val=$parr['planet_id'].":".$parr['planet_user_id'];;
						}
						else
						{
							$val=$parr['planet_id'];
						}
						if ($parr['planet_user_id']>0)
							$out.="<option value=\"$val\">".$parr['planet_solsys_pos']." ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
						else
							$out.="<option value=\"$val\" style=\"font-style:italic\">".$parr['planet_solsys_pos']." Unbewohnt</option>";
					}
					$out.="</select>";
				}
			}
			else
			{
				$out="Dies ist kein Sonnensystem!";
			}
		}   
		else
		{
			$out="Zelle nicht gefunden!";
		}
  }
	else
	{
		$out="Sonnensystem w&auml;hlen...";
	}
  $objResponse->addAssign("planetSelector","innerHTML", $out);
  $objResponse->addAssign("user_nick","value", "");
	return $objResponse;
}

function planetSelectorByUser($userNick,$function,$show_user_id=1)
{
	global $db_table;
	$objResponse = new xajaxResponse();
	if ($userNick!="")
	{
		$pres=dbquery("
		SELECT
			planet_id,
			planet_name,
			planet_user_id,
			cell_sx,
			cell_sy,
			cell_cx,
			cell_cy,
			planet_solsys_pos
		FROM
			".$db_table['planets']."
		INNER JOIN
			".$db_table['users']."
			ON planet_user_id=user_id
		INNER JOIN
			".$db_table['space_cells']."
			ON planet_solsys_id=cell_id
		WHERE
			user_nick='$userNick'				
			;
		");
		$nr=mysql_num_rows($pres);
		if ($nr>0)
		{
			$out="<select name=\"planet_id\" size=\"$nr\" onchange=\"xajax_".$function."(this.options[this.selectedIndex].value);\">";
			while ($parr=mysql_fetch_array($pres))
			{
				if ($show_user_id==1)
				{
					$val=$parr['planet_id'].":".$parr['planet_user_id'];;
				}
				else
				{
					$val=$parr['planet_id'];
				}
				$out.="<option value=\"$val\">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." - ".$parr['planet_name']."</option>";
			}
			$out.="</select>";
		}
		else
		{
			$out="Keine Planeten gefunden!";					
		}
  }
	else
	{
		$out="Korrekten Usernamen w&auml;hlen...";
	}
  $objResponse->addAssign("planetSelector","innerHTML", $out);
	return $objResponse;	
}

function showShipsOnPlanet($pid)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	if ($pid!=0)
	{
		$updata=explode(":",$pid);
		$pid=$updata[0];
		$res=dbquery("
		SELECT
			ship_name,
			shiplist_count,
			shiplist_id
		FROM
			".$db_table['shiplist']."
		INNER JOIN
			".$db_table['ships']."
			ON shiplist_ship_id=ship_id
			AND shiplist_planet_id='".$pid."'
		ORDER BY
			ship_name
		;");
		if (mysql_num_rows($res)>0)
		{
			$out="<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				$out.="<tr><td style=\"width:80px\" id=\"cnt_".$arr['shiplist_id']."\">".$arr['shiplist_count']."</td>
				<th>".$arr['ship_name']."</th>
				<td style=\"width:150px\" id=\"actions_".$arr['shiplist_id']."\"><a href=\"javascript:;\" onclick=\"xajax_editShip(xajax.getFormValues('selector'),".$arr['shiplist_id'].")\">Bearbeiten</a> 
				<a href=\"javascript:;\" onclick=\"if (confirm('Sollen ".$arr['shiplist_count']." ".$arr['ship_name']." von diesem Planeten gel&ouml;scht werden?')) {xajax_removeShipFromPlanet(xajax.getFormValues('selector'),".$arr['shiplist_id'].")}\">L&ouml;schen</td>
				</tr>";
			}
			$out.="</table>";
		}
		else
		{
			$out="Keine Schiffe vorhanden!";
		}
	}
	else
	{
		$out="Planet w&auml;hlen...";
	}	
  $objResponse->addAssign("shipsOnPlanet","innerHTML", $out);
	return $objResponse;		
}

function addShipToPlanet($form)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	if ($updata[1]>0)
	{
		shiplistAdd($updata[0],$updata[1],$form['ship_id'],intval($form['shiplist_count']));	
  	$objResponse->addScript("xajax_showShipsOnPlanet(".$updata[0].")");
  }
  else
  {
  	$out="Planet unbewohnt. Kann keine Schiffe hier bauen!";
   	$objResponse->addAssign("shipsOnPlanet","innerHTML", $out); 	
  }
	return $objResponse;			
}

function removeShipFromPlanet($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		".$db_table['shiplist']."
	WHERE
		shiplist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showShipsOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editShip($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	$res=dbquery("
	SELECT
		shiplist_count,
		shiplist_id
	FROM
		".$db_table['shiplist']."
	WHERE
		shiplist_planet_id=".$updata[0]."
	;");
	if (mysql_num_rows($res))
	{
		while ($arr=mysql_fetch_array($res))
		{
			if ($arr['shiplist_id']==$listId)
			{
				$out="<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_".$listId."\" value=\"".$arr['shiplist_count']."\" />";
		 		$objResponse->addAssign("cnt_".$listId,"innerHTML", $out); 	
		 		$out="<a href=\"javaScript:;\" onclick=\"xajax_submitEditShip(xajax.getFormValues('selector'),".$listId.");\">Speichern</a> ";
		 		$out.="<a href=\"javaScript:;\" onclick=\"xajax_showShipsOnPlanet(".$updata[0].");\">Abbrechen</a>";
		 		$objResponse->addAssign("actions_".$listId,"innerHTML", $out); 	
			}
			else
			{
		 		$objResponse->addAssign("actions_".$arr['shiplist_id'],"innerHTML", ""); 					
			}
		}
	}
  
	return $objResponse;		
}

function submitEditShip($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	UPDATE
		".$db_table['shiplist']."
	SET
		shiplist_count=".intval($form['editcnt_'.$listId])."
	WHERE
		shiplist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showShipsOnPlanet(".$updata[0].");");
	return $objResponse;		
}

// Missiles

function showMissilesOnPlanet($pid)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	if ($pid!=0)
	{
		$updata=explode(":",$pid);
		$pid=$updata[0];
		$res=dbquery("
		SELECT
			missile_name,
			missilelist_count,
			missilelist_id
		FROM
			missilelist
		INNER JOIN
			missiles
			ON missilelist_missile_id=missile_id
			AND missilelist_planet_id='".$pid."'
		ORDER BY
			missile_name
		;");
		if (mysql_num_rows($res)>0)
		{
			$out="<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				$out.="<tr><td style=\"width:80px\" id=\"cnt_".$arr['missilelist_id']."\">".$arr['missilelist_count']."</td>
				<th>".$arr['missile_name']."</th>
				<td style=\"width:150px\" id=\"actions_".$arr['missilelist_id']."\"><a href=\"javascript:;\" onclick=\"xajax_editMissile(xajax.getFormValues('selector'),".$arr['missilelist_id'].")\">Bearbeiten</a> 
				<a href=\"javascript:;\" onclick=\"if (confirm('Sollen ".$arr['missilelist_count']." ".$arr['missile_name']." von diesem Planeten gel&ouml;scht werden?')) {xajax_removeMissileFromPlanet(xajax.getFormValues('selector'),".$arr['missilelist_id'].")}\">L&ouml;schen</td>
				</tr>";
			}
			$out.="</table>";
		}
		else
		{
			$out="Keine Raketen vorhanden!";
		}
	}
	else
	{
		$out="Planet w&auml;hlen...";
	}	
  $objResponse->addAssign("shipsOnPlanet","innerHTML", $out);
	return $objResponse;		
}

function addMissileToPlanet($form)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	if ($updata[1]>0)
	{
		missilelistAdd($updata[0],$updata[1],$form['ship_id'],intval($form['shiplist_count']));	
  	$objResponse->addScript("xajax_showMissilesOnPlanet(".$updata[0].")");
  }
  else
  {
  	$out="Planet unbewohnt. Kann keine Schiffe hier bauen!";
   	$objResponse->addAssign("shipsOnPlanet","innerHTML", $out); 	
  }
	return $objResponse;			
}

function removeMissileFromPlanet($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		missilelist
	WHERE
		missilelist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showMissilesOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editMissile($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	$res=dbquery("
	SELECT
		missilelist_count,
		missilelist_id
	FROM
		missilelist
	WHERE
		missilelist_planet_id=".$updata[0]."
	;");
	if (mysql_num_rows($res))
	{
		while ($arr=mysql_fetch_array($res))
		{
			if ($arr['missilelist_id']==$listId)
			{
				$out="<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_".$listId."\" value=\"".$arr['missilelist_count']."\" />";
		 		$objResponse->addAssign("cnt_".$listId,"innerHTML", $out); 	
		 		$out="<a href=\"javaScript:;\" onclick=\"xajax_submitEditMissile(xajax.getFormValues('selector'),".$listId.");\">Speichern</a> ";
		 		$out.="<a href=\"javaScript:;\" onclick=\"xajax_showMissilesOnPlanet(".$updata[0].");\">Abbrechen</a>";
		 		$objResponse->addAssign("actions_".$listId,"innerHTML", $out); 	
			}
			else
			{
		 		$objResponse->addAssign("actions_".$arr['missilelist_id'],"innerHTML", ""); 					
			}
		}
	}
  
	return $objResponse;		
}

function submitEditMissile($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	UPDATE
		missilelist
	SET
		missilelist_count=".intval($form['editcnt_'.$listId])."
	WHERE
		missilelist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showMissilesOnPlanet(".$updata[0].");");
	return $objResponse;		
}


// Defense

function showDefenseOnPlanet($pid)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	if ($pid!=0)
	{
		$updata=explode(":",$pid);
		$pid=$updata[0];
		$res=dbquery("
		SELECT
			def_name,
			deflist_count,
			deflist_id
		FROM
			deflist
		INNER JOIN
			defense
			ON deflist_def_id=def_id
			AND deflist_planet_id='".$pid."'
		ORDER BY
			def_name
		;");
		if (mysql_num_rows($res)>0)
		{
			$out="<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				$out.="<tr><td style=\"width:80px\" id=\"cnt_".$arr['deflist_id']."\">".$arr['deflist_count']."</td>
				<th>".$arr['def_name']."</th>
				<td style=\"width:150px\" id=\"actions_".$arr['deflist_id']."\"><a href=\"javascript:;\" onclick=\"xajax_editDefense(xajax.getFormValues('selector'),".$arr['deflist_id'].")\">Bearbeiten</a> 
				<a href=\"javascript:;\" onclick=\"if (confirm('Sollen ".$arr['deflist_count']." ".$arr['def_name']." von diesem Planeten gel&ouml;scht werden?')) {xajax_removeDefenseFromPlanet(xajax.getFormValues('selector'),".$arr['deflist_id'].")}\">L&ouml;schen</td>
				</tr>";
			}
			$out.="</table>";
		}
		else
		{
			$out="Keine Verteidigung vorhanden!";
		}
	}
	else
	{
		$out="Planet w&auml;hlen...";
	}	
  $objResponse->addAssign("shipsOnPlanet","innerHTML", $out);
	return $objResponse;		
}

function addDefenseToPlanet($form)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	if ($updata[1]>0)
	{
		deflistAdd($updata[0],$updata[1],$form['ship_id'],intval($form['shiplist_count']));	
  	$objResponse->addScript("xajax_showDefenseOnPlanet(".$updata[0].")");
  }
  else
  {
  	$out="Planet unbewohnt. Kann keine Schiffe hier bauen!";
   	$objResponse->addAssign("shipsOnPlanet","innerHTML", $out); 	
  }
	return $objResponse;			
}

function removeDefenseFromPlanet($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		deflist
	WHERE
		deflist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showDefenseOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editDefense($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	$res=dbquery("
	SELECT
		deflist_count,
		deflist_id
	FROM
		deflist
	WHERE
		deflist_planet_id=".$updata[0]."
	;");
	if (mysql_num_rows($res))
	{
		while ($arr=mysql_fetch_array($res))
		{
			if ($arr['deflist_id']==$listId)
			{
				$out="<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_".$listId."\" value=\"".$arr['deflist_count']."\" />";
		 		$objResponse->addAssign("cnt_".$listId,"innerHTML", $out); 	
		 		$out="<a href=\"javaScript:;\" onclick=\"xajax_submitEditDefense(xajax.getFormValues('selector'),".$listId.");\">Speichern</a> ";
		 		$out.="<a href=\"javaScript:;\" onclick=\"xajax_showDefenseOnPlanet(".$updata[0].");\">Abbrechen</a>";
		 		$objResponse->addAssign("actions_".$listId,"innerHTML", $out); 	
			}
			else
			{
		 		$objResponse->addAssign("actions_".$arr['deflist_id'],"innerHTML", ""); 					
			}
		}
	}
  
	return $objResponse;		
}

function submitEditDefense($form,$listId)
{
	global $db_table;
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	UPDATE
		deflist
	SET
		deflist_count=".intval($form['editcnt_'.$listId])."
	WHERE
		deflist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showDefenseOnPlanet(".$updata[0].");");
	return $objResponse;		
}



//Listet gefundene User auf
function searchUser($val,$field_id='user_nick',$box_id='citybox')
{
	global $db_table;
	
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
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"block\"");
	}
	else
	{
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"none\"");
	}
	
	//Wenn nur noch ein User in frage kommt, diesen Anzeigen
	if($nCount == 1)
	{
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"none\"");
		$objResponse->addScript("document.getElementById('".$field_id."').value = \"".$sLastHit."\"");
	}
	
	$objResponse->addAssign($box_id, "innerHTML", $sOut);
	
	return $objResponse->getXML();
}


//Listet gefundene User auf (Speziel für Schiffs-, Def-, und Raketenformular)
function searchUserList($val,$function)
{
	global $db_table;
	$targetId = 'userlist';
	$inputId = 'userlist_nick';

  	$sOut = "";
  	$nCount = 0;

	$res=dbquery("SELECT 
		user_nick 
	FROM 
		users 
	WHERE 
		user_nick LIKE '".$val."%' 
	LIMIT 20;");
	if (mysql_num_rows($res)>0)
  {
		while($arr=mysql_fetch_row($res))
		{
	    $nCount++;
      $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='".htmlentities($arr[0])."';xajax_planetSelectorByUser('".$arr[0]."','".$function."');document.getElementById('$targetId').style.display = 'none';\">".htmlentities($arr[0])."</a>";
      $sLastHit = $arr[0];
    }
	}

    if($nCount > 20)
    {
    	$sOut = "";
    }

    $objResponse = new xajaxResponse();
   	$objResponse->addScript("xajax_showShipsOnPlanet(0)");

  	if(strlen($sOut) > 0)  
  	{
		$sOut = "".$sOut."";
    	$objResponse->addScript("document.getElementById('$targetId').style.display = \"block\"");
    }
  	else  
  	{
		$objResponse->addScript("document.getElementById('$targetId').style.display = \"none\"");
  	}

	//Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if($nCount == 1)  
    {
        $objResponse->addScript("document.getElementById('$targetId').style.display = \"none\"");
        $objResponse->addScript("document.getElementById('$inputId').value = \"".$sLastHit."\"");
   	 		$objResponse->addScript("xajax_planetSelectorByUser('$sLastHit','".$function."')");
    }

    $objResponse->addAssign("$targetId", "innerHTML", $sOut);

    return $objResponse->getXML();
}


//Listet gefundene Allianzen auf
function searchAlliance($val,$field_id='alliance_name',$box_id='citybox')
{
	global $db_table;
	
	$sOut = "";
	$nCount = 0;
	
	$res=dbquery("SELECT alliance_name FROM alliances WHERE alliance_name LIKE '%".$val."%' LIMIT 20;");
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
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"block\"");
	}
	else
	{
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"none\"");
	}
	
	//Wenn nur noch ein User in frage kommt, diesen Anzeigen
	if($nCount == 1)
	{
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"none\"");
		$objResponse->addScript("document.getElementById('".$field_id."').value = \"".$sLastHit."\"");
	}
	
	$objResponse->addAssign($box_id, "innerHTML", $sOut);
	
	return $objResponse->getXML();
}


//Listet gefundene Planeten auf
function searchPlanet($val,$field_id='planet_name',$box_id='citybox')
{
	global $db_table;
	
	$sOut = "";
	$nCount = 0;
	
	$res=dbquery("SELECT planet_name FROM planets WHERE planet_name LIKE '".$val."%' LIMIT 20;");
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
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"block\"");
	}
	else
	{
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"none\"");
	}
	
	//Wenn nur noch ein User in frage kommt, diesen Anzeigen
	if($nCount == 1)
	{
		$objResponse->addScript("document.getElementById('".$box_id."').style.display = \"none\"");
		$objResponse->addScript("document.getElementById('".$field_id."').value = \"".$sLastHit."\"");
	}
	
	$objResponse->addAssign($box_id, "innerHTML", $sOut);
	
	return $objResponse->getXML();
}



function lockUser($uid,$time,$reason)
{
	global $db_table;
	$t1 = time();
	$t2 = $t1 + $time;
	dbquery("
	UPDATE
		".$db_table['users']."
	SET
		user_blocked_from=".$t1.",
		user_blocked_to=".$t2.",
		user_ban_reason='".addslashes($reason)."',
		user_ban_admin_id='".$_SESSION[SESSION_NAME]['user_id']."'
	WHERE
		user_id='".$uid."'	
	;");
	$objResponse = new xajaxResponse();
  $objResponse->addAlert("Der Benutzer wurde gesperrt!");
	return $objResponse;			
}

/***********/

function buildingPrices($id,$lvl)
{
	$objResponse = new xajaxResponse();
	$res = dbquery("
	SELECT
		building_costs_metal,
		building_costs_crystal,
		building_costs_plastic,
		building_costs_fuel,
		building_costs_food,
		building_costs_power,
		building_build_costs_factor
	FROM
		buildings
	WHERE
		building_id=".$id."
	;");
	$arr = mysql_fetch_array($res);
	$bc = calcBuildingCosts($arr,$lvl);
	$objResponse->addAssign("c1_metal","innerHTML",nf($bc['metal']));
	$objResponse->addAssign("c1_crystal","innerHTML",nf($bc['crystal']));
	$objResponse->addAssign("c1_plastic","innerHTML",nf($bc['plastic']));
	$objResponse->addAssign("c1_fuel","innerHTML",nf($bc['fuel']));
	$objResponse->addAssign("c1_food","innerHTML",nf($bc['food']));
	$objResponse->addAssign("c1_power","innerHTML",nf($bc['power']));

	return $objResponse;			
}

function totalBuildingPrices($form)
{
	$objResponse = new xajaxResponse();
	$bctt = array();
	foreach ($form['b_lvl'] as $id=>$lvl)
	{
		$res = dbquery("
		SELECT
			building_costs_metal,
			building_costs_crystal,
			building_costs_plastic,
			building_costs_fuel,
			building_costs_food,
			building_costs_power,
			building_build_costs_factor
		FROM
			buildings
		WHERE
			building_id=".$id."
		;");
		$arr = mysql_fetch_array($res);
		$bct = array();
		for ($x=0;$x<$lvl;$x++)
		{
			$bc = calcBuildingCosts($arr,$x);	
			$bct['metal']+=$bc['metal'];
			$bct['crystal']+=$bc['crystal'];
			$bct['plastic']+=$bc['plastic'];
			$bct['fuel']+=$bc['fuel'];
			$bct['food']+=$bc['food'];
		}
		$bctt['metal']+=$bct['metal'];
		$bctt['crystal']+=$bct['crystal'];
		$bctt['plastic']+=$bct['plastic'];
		$bctt['fuel']+=$bct['fuel'];
		$bctt['food']+=$bct['food'];
		$objResponse->addAssign("b_metal_".$id,"innerHTML",nf($bct['metal']));
		$objResponse->addAssign("b_crystal_".$id,"innerHTML",nf($bct['crystal']));
		$objResponse->addAssign("b_plastic_".$id,"innerHTML",nf($bct['plastic']));
		$objResponse->addAssign("b_fuel_".$id,"innerHTML",nf($bct['fuel']));
		$objResponse->addAssign("b_food_".$id,"innerHTML",nf($bct['food']));
	}
		$objResponse->addAssign("t_metal","innerHTML",nf($bctt['metal']));
		$objResponse->addAssign("t_crystal","innerHTML",nf($bctt['crystal']));
		$objResponse->addAssign("t_plastic","innerHTML",nf($bctt['plastic']));
		$objResponse->addAssign("t_fuel","innerHTML",nf($bctt['fuel']));
		$objResponse->addAssign("t_food","innerHTML",nf($bctt['food']));
	
		
	return $objResponse;	
}

$xajax->registerFunction("planetSelectorByCell");
$xajax->registerFunction("planetSelectorByUser");

$xajax->registerFunction("showShipsOnPlanet");
$xajax->registerFunction("addShipToPlanet");
$xajax->registerFunction("removeShipFromPlanet");
$xajax->registerFunction("editShip");
$xajax->registerFunction("submitEditShip");

$xajax->registerFunction("showMissilesOnPlanet");
$xajax->registerFunction("addMissileToPlanet");
$xajax->registerFunction("removeMissileFromPlanet");
$xajax->registerFunction("editMissile");
$xajax->registerFunction("submitEditMissile");

$xajax->registerFunction("showDefenseOnPlanet");
$xajax->registerFunction("addDefenseToPlanet");
$xajax->registerFunction("removeDefenseFromPlanet");
$xajax->registerFunction("editDefense");
$xajax->registerFunction("submitEditDefense");

$xajax->registerFunction("searchUser");
$xajax->registerFunction("searchUserList");
$xajax->registerFunction("searchAlliance");
$xajax->registerFunction("searchPlanet");
$xajax->registerFunction("lockUser");

$xajax->registerFunction("buildingPrices");
$xajax->registerFunction("totalBuildingPrices");

?>