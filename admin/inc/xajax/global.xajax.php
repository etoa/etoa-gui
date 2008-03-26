<?PHP

$xajax->register(XAJAX_FUNCTION,"planetSelectorByCell");
$xajax->register(XAJAX_FUNCTION,"planetSelectorByUser");

$xajax->register(XAJAX_FUNCTION,"showShipsOnPlanet");
$xajax->register(XAJAX_FUNCTION,"addShipToPlanet");
$xajax->register(XAJAX_FUNCTION,"removeShipFromPlanet");
$xajax->register(XAJAX_FUNCTION,"editShip");
$xajax->register(XAJAX_FUNCTION,"submitEditShip");
$xajax->register(XAJAX_FUNCTION,"calcShipLevel");



$xajax->register(XAJAX_FUNCTION,"showMissilesOnPlanet");
$xajax->register(XAJAX_FUNCTION,"addMissileToPlanet");
$xajax->register(XAJAX_FUNCTION,"removeMissileFromPlanet");
$xajax->register(XAJAX_FUNCTION,"editMissile");
$xajax->register(XAJAX_FUNCTION,"submitEditMissile");

$xajax->register(XAJAX_FUNCTION,"showDefenseOnPlanet");
$xajax->register(XAJAX_FUNCTION,"addDefenseToPlanet");
$xajax->register(XAJAX_FUNCTION,"removeDefenseFromPlanet");
$xajax->register(XAJAX_FUNCTION,"editDefense");
$xajax->register(XAJAX_FUNCTION,"submitEditDefense");

$xajax->register(XAJAX_FUNCTION,"searchUser");
$xajax->register(XAJAX_FUNCTION,"searchUserList");
$xajax->register(XAJAX_FUNCTION,"searchAlliance");
$xajax->register(XAJAX_FUNCTION,"searchPlanet");
$xajax->register(XAJAX_FUNCTION,"lockUser");

$xajax->register(XAJAX_FUNCTION,"buildingPrices");
$xajax->register(XAJAX_FUNCTION,"totalBuildingPrices");


function planetSelectorByCell($form,$function,$show_user_id=0)
{
	$objResponse = new xajaxResponse();
	if ($form['cell_sx']!=0 && $form['cell_sy']!=0 && $form['cell_cx']!=0 && $form['cell_cy']!=0)
	{
		$res=dbquery("
		SELECT
			cell_id,
			cell_solsys_num_planets,
			cell_solsys_solsys_sol_type
		FROM
			space_cells
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
					planets
				LEFT JOIN
					users
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
  $objResponse->assign("planetSelector","innerHTML", $out);
  $objResponse->assign("user_nick","value", "");
	return $objResponse;
}

function planetSelectorByUser($userNick,$function,$show_user_id=1)
{
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
			planets
		INNER JOIN
			users
			ON planet_user_id=user_id
		INNER JOIN
			space_cells
			ON planet_solsys_id=cell_id
		WHERE
			user_nick='$userNick'				
			;
		");
		$nr=mysql_num_rows($pres);
		if ($nr>0)
		{
			$out="<select name=\"planet_id\" size=\"$nr\" onchange=\"showLoader('shipsOnPlanet');xajax_".$function."(this.options[this.selectedIndex].value);\">\n";
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
				$out.="<option value=\"$val\">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." - ".$parr['planet_name']."</option>\n";
			}
			$out.="</select>\n";

			if ($nr==1)
			{
				$objResponse->script("showLoader('shipsOnPlanet');xajax_".$function."('".$val."');");
			}
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
  $objResponse->assign("planetSelector","innerHTML", $out);
	return $objResponse;	
}

function showShipsOnPlanet($pid)
{
	$objResponse = new xajaxResponse();	
	

	if ($pid!=0)
	{
		$updata=explode(":",$pid);
		$pid=$updata[0];
		$res=dbquery("
		SELECT
			ship_name,
			shiplist_count,
			shiplist_id,
			special_ship_need_exp as ship_xp_base, 
			special_ship_exp_factor as ship_xp_factor,
			shiplist_special_ship_exp as shiplist_xp
		FROM
			shiplist
		INNER JOIN
			ships
			ON shiplist_ship_id=ship_id
			AND shiplist_planet_id='".$pid."'
		WHERE
			shiplist_count>0
		ORDER BY
			ship_name
		;");
		if (mysql_num_rows($res)>0)
		{
			$out="<table class=\"tb\">
			<tr><th>Anzahl</th><th>Typ</th><th>Spezielles</th><th>Aktionen</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				$out.="<tr><td style=\"width:80px\" id=\"cnt_".$arr['shiplist_id']."\">".$arr['shiplist_count']."</td>
				<td>".$arr['ship_name']."</td>
				<td id=\"special_".$arr['shiplist_id']."\">";
				if ($arr['ship_xp_base']>0)
				{
					$out.= nf($arr['shiplist_xp'])." XP, Level ".Ship::levelByXp($arr['ship_xp_base'], $arr['ship_xp_factor'],$arr['shiplist_xp']);
				}
				$out.= "
				<td style=\"width:180px\" id=\"actions_".$arr['shiplist_id']."\" id=\"actions_".$arr['shiplist_id']."\">
				<input type=\"button\" value=\"Bearbeiten\" onclick=\"xajax_editShip(xajax.getFormValues('selector'),".$arr['shiplist_id'].")\" />
				<input type=\"button\" value=\"Löschen\" onclick=\"if (confirm('Sollen ".$arr['shiplist_count']." ".$arr['ship_name']." von diesem Planeten gel&ouml;scht werden?')) {showLoaderPrepend('shipsOnPlanet');xajax_removeShipFromPlanet(xajax.getFormValues('selector'),".$arr['shiplist_id'].")}\" /><br/><br/>
				</td>
				</tr>";
			}
			$out.="</table>";
		}
		else
		{
			$out="Keine Schiffe vorhanden!";
		}
		$out.="<br/><br/><input type=\"Button\" value=\"Neu laden\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('".$pid."');\">";
	}
	else
	{
		$out="Planet w&auml;hlen...";
	}	
  $objResponse->assign("shipsOnPlanet","innerHTML", $out);
	return $objResponse;		
}

function addShipToPlanet($form)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	if ($updata[1]>0)
	{
		shiplistAdd($updata[0],$updata[1],$form['ship_id'],intval($form['shiplist_count']));	
  	$objResponse->script("xajax_showShipsOnPlanet(".$updata[0].")");
  }
  else
  {
  	$out="Planet unbewohnt. Kann keine Schiffe hier bauen!";
   	$objResponse->assign("shipsOnPlanet","innerHTML", $out); 	
  }
	return $objResponse;			
}

function removeShipFromPlanet($form,$listId)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		shiplist
	WHERE
		shiplist_id=".intval($listId)."
	;");
  $objResponse->script("xajax_showShipsOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editShip($form,$listId)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	$res=dbquery("
	SELECT
		shiplist_count,
		shiplist_id,
		special_ship_need_exp as ship_xp_base, 
		special_ship_exp_factor as ship_xp_factor,
		shiplist_special_ship_exp as shiplist_xp,
				shiplist_special_ship_bonus_weapon,
				shiplist_special_ship_bonus_structure,
				shiplist_special_ship_bonus_shield,
				shiplist_special_ship_bonus_heal,
				shiplist_special_ship_bonus_capacity,
				shiplist_special_ship_bonus_speed,
				shiplist_special_ship_bonus_pilots,
				shiplist_special_ship_bonus_tarn,
				shiplist_special_ship_bonus_antrax,
				shiplist_special_ship_bonus_forsteal,
				shiplist_special_ship_bonus_build_destroy,
				shiplist_special_ship_bonus_antrax_food,
				shiplist_special_ship_bonus_deactivade		
	FROM
		shiplist
	INNER JOIN
		ships
		ON shiplist_ship_id=ship_id		
	AND
		shiplist_planet_id=".$updata[0]."
	;");
	if (mysql_num_rows($res))
	{
		while ($arr=mysql_fetch_array($res))
		{
			if ($arr['shiplist_id']==$listId)
			{
				$out="<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_".$listId."\" value=\"".$arr['shiplist_count']."\" />";
		 		$objResponse->assign("cnt_".$listId,"innerHTML", $out); 	
				if ($arr['ship_xp_base']>0)
				{
					$out= "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editxp_".$listId."\" value=\"".$arr['shiplist_xp']."\" onkeyup=\"xajax_calcShipLevel(".$listId.",".$arr['ship_xp_base'].",".$arr['ship_xp_factor'].",this.value);\" /> XP, 
					Level <b><span id=\"editlevel_".$listId."\">".Ship::levelByXp($arr['ship_xp_base'], $arr['ship_xp_factor'],$arr['shiplist_xp'])."</span></b><br/>
					
					<b>Waffenlevel:</b> <input type=\"text\" name=\"edit_bonus_weapon_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_weapon']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Strukturlevel:</b> <input type=\"text\" name=\"edit_bonus_structure_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_structure']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Schildlevel:</b> <input type=\"text\" name=\"edit_bonus_shield_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_shield']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Heallevel:</b> <input type=\"text\" name=\"edit_bonus_heal_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_heal']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Kapazit&auml;tlevel:</b> <input type=\"text\" name=\"edit_bonus_capacity_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_capacity']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Speedlevel:</b> <input type=\"text\" name=\"edit_bonus_speed_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_speed']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Besatzungslevel:</b> <input type=\"text\" name=\"edit_bonus_pilots_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_pilots']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Tarnungslevel:</b> <input type=\"text\" name=\"edit_bonus_tarn_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_tarn']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Giftgaslevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_antrax']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Techklaulevel:</b> <input type=\"text\" name=\"edit_bonus_forsteal_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_forsteal']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Bombardierlevel:</b> <input type=\"text\" name=\"edit_bonus_build_destroy_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_build_destroy']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Antraxlevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_food_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_antrax_food']."\" size=\"5\" maxlength=\"20\" /><br/>
					<b>Deaktivierlevel:</b> <input type=\"text\" name=\"edit_bonus_deactivade_".$listId."\" value=\"".$arr['shiplist_special_ship_bonus_deactivade']."\" size=\"5\" maxlength=\"20\" />";
				
					
				}		 	
				else
					$out="";	
		 		$objResponse->assign("special_".$listId,"innerHTML", $out); 	
		 		$out="<input type=\"button\" value=\"Speichern\" onclick=\"showLoader('actions_".$listId."');xajax_submitEditShip(xajax.getFormValues('selector'),".$listId.");\" /> ";
		 		$out.="<input type=\"button\" value=\"Abbrechen\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet(".$updata[0].");\" />";
		 		$objResponse->assign("actions_".$listId,"innerHTML", $out); 	
			}
			else
			{
		 		$objResponse->assign("actions_".$arr['shiplist_id'],"innerHTML", ""); 					
			}
		}
	}
  
	return $objResponse;		
}

function calcShipLevel($slid,$base,$factor,$xp)
{
	$objResponse = new xajaxResponse();	
	
	$objResponse->assign("editlevel_".$slid,"innerHTML", Ship::levelByXp($base, $factor,$xp)); 					
	return $objResponse;
}

function submitEditShip($form,$listId)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	UPDATE
		shiplist
	SET
		shiplist_count=".intval($form['editcnt_'.$listId]).",
		shiplist_special_ship_exp=".intval($form['editxp_'.$listId]).",
		shiplist_special_ship_bonus_weapon='".intval($form['edit_bonus_weapon_'.$listId])."',
		shiplist_special_ship_bonus_structure='".intval($form['edit_bonus_structure_'.$listId])."',
		shiplist_special_ship_bonus_shield='".intval($form['edit_bonus_shield_'.$listId])."',
		shiplist_special_ship_bonus_heal='".intval($form['edit_bonus_heal_'.$listId])."',
		shiplist_special_ship_bonus_capacity='".intval($form['edit_bonus_capacity_'.$listId])."',
		shiplist_special_ship_bonus_speed='".intval($form['edit_bonus_speed_'.$listId])."',
		shiplist_special_ship_bonus_pilots='".intval($form['edit_bonus_pilots_'.$listId])."',
		shiplist_special_ship_bonus_tarn='".intval($form['edit_bonus_tarn_'.$listId])."',
		shiplist_special_ship_bonus_antrax='".intval($form['edit_bonus_antrax_'.$listId])."',
		shiplist_special_ship_bonus_forsteal='".intval($form['edit_bonus_forsteal_'.$listId])."',
		shiplist_special_ship_bonus_build_destroy='".intval($form['edit_bonus_build_destroy_'.$listId])."',
		shiplist_special_ship_bonus_antrax_food='".intval($form['edit_bonus_antrax_food_'.$listId])."',
		shiplist_special_ship_bonus_deactivade='".intval($form['edit_bonus_deactivade_'.$listId])."'		
	WHERE
		shiplist_id=".intval($listId)."
	;");
  $objResponse->script("xajax_showShipsOnPlanet(".$updata[0].");");
	return $objResponse;		
}

// Missiles

function showMissilesOnPlanet($pid)
{
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
  $objResponse->assign("shipsOnPlanet","innerHTML", $out);
	return $objResponse;		
}

function addMissileToPlanet($form)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	if ($updata[1]>0)
	{
		missilelistAdd($updata[0],$updata[1],$form['ship_id'],intval($form['shiplist_count']));	
  	$objResponse->script("xajax_showMissilesOnPlanet(".$updata[0].")");
  }
  else
  {
  	$out="Planet unbewohnt. Kann keine Schiffe hier bauen!";
   	$objResponse->assign("shipsOnPlanet","innerHTML", $out); 	
  }
	return $objResponse;			
}

function removeMissileFromPlanet($form,$listId)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		missilelist
	WHERE
		missilelist_id=".intval($listId)."
	;");
  $objResponse->script("xajax_showMissilesOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editMissile($form,$listId)
{
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
		 		$objResponse->assign("cnt_".$listId,"innerHTML", $out); 	
		 		$out="<a href=\"javaScript:;\" onclick=\"xajax_submitEditMissile(xajax.getFormValues('selector'),".$listId.");\">Speichern</a> ";
		 		$out.="<a href=\"javaScript:;\" onclick=\"xajax_showMissilesOnPlanet(".$updata[0].");\">Abbrechen</a>";
		 		$objResponse->assign("actions_".$listId,"innerHTML", $out); 	
			}
			else
			{
		 		$objResponse->assign("actions_".$arr['missilelist_id'],"innerHTML", ""); 					
			}
		}
	}
  
	return $objResponse;		
}

function submitEditMissile($form,$listId)
{
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
  $objResponse->script("xajax_showMissilesOnPlanet(".$updata[0].");");
	return $objResponse;		
}


// Defense

function showDefenseOnPlanet($pid)
{
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
  $objResponse->assign("shipsOnPlanet","innerHTML", $out);
	return $objResponse;		
}

function addDefenseToPlanet($form)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	if ($updata[1]>0)
	{
		deflistAdd($updata[0],$updata[1],$form['ship_id'],intval($form['shiplist_count']));	
  	$objResponse->script("xajax_showDefenseOnPlanet(".$updata[0].")");
  }
  else
  {
  	$out="Planet unbewohnt. Kann keine Schiffe hier bauen!";
   	$objResponse->assign("shipsOnPlanet","innerHTML", $out); 	
  }
	return $objResponse;			
}

function removeDefenseFromPlanet($form,$listId)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		deflist
	WHERE
		deflist_id=".intval($listId)."
	;");
  $objResponse->script("xajax_showDefenseOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editDefense($form,$listId)
{
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
		 		$objResponse->assign("cnt_".$listId,"innerHTML", $out); 	
		 		$out="<a href=\"javaScript:;\" onclick=\"xajax_submitEditDefense(xajax.getFormValues('selector'),".$listId.");\">Speichern</a> ";
		 		$out.="<a href=\"javaScript:;\" onclick=\"xajax_showDefenseOnPlanet(".$updata[0].");\">Abbrechen</a>";
		 		$objResponse->assign("actions_".$listId,"innerHTML", $out); 	
			}
			else
			{
		 		$objResponse->assign("actions_".$arr['deflist_id'],"innerHTML", ""); 					
			}
		}
	}
  
	return $objResponse;		
}

function submitEditDefense($form,$listId)
{
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
  $objResponse->script("xajax_showDefenseOnPlanet(".$updata[0].");");
	return $objResponse;		
}



//Listet gefundene User auf
function searchUser($val,$field_id='user_nick',$box_id='citybox')
{
	
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
		$objResponse->script("document.getElementById('".$field_id."').value = \"".$sLastHit."\"");
	}
	
	$objResponse->assign($box_id, "innerHTML", $sOut);
	
	return $objResponse;
}


//Listet gefundene User auf (Speziel für Schiffs-, Def-, und Raketenformular)
function searchUserList($val,$function)
{
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
   	//$objResponse->script("xajax_showShipsOnPlanet(0)");

  	if(strlen($sOut) > 0)  
  	{
		$sOut = "".$sOut."";
    	$objResponse->script("document.getElementById('$targetId').style.display = \"block\"");
    }
  	else  
  	{
		$objResponse->script("document.getElementById('$targetId').style.display = \"none\"");
  	}

	//Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if($nCount == 1)  
    {
        $objResponse->script("document.getElementById('$targetId').style.display = \"none\"");
        $objResponse->script("document.getElementById('$inputId').value = \"".$sLastHit."\"");
   	 		$objResponse->script("xajax_planetSelectorByUser('$sLastHit','".$function."')");
    }

    $objResponse->assign("$targetId", "innerHTML", $sOut);

    return $objResponse;
}


//Listet gefundene Allianzen auf
function searchAlliance($val,$field_id='alliance_name',$box_id='citybox')
{
	
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
		$objResponse->script("document.getElementById('".$field_id."').value = \"".$sLastHit."\"");
	}
	
	$objResponse->assign($box_id, "innerHTML", $sOut);
	
	return $objResponse;
}


//Listet gefundene Planeten auf
function searchPlanet($val,$field_id='planet_name',$box_id='citybox')
{
	
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
		$objResponse->script("document.getElementById('".$field_id."').value = \"".$sLastHit."\"");
	}
	
	$objResponse->assign($box_id, "innerHTML", $sOut);
	
	return $objResponse;
}



function lockUser($uid,$time,$reason)
{
	$t1 = time();
	$t2 = $t1 + $time;
	dbquery("
	UPDATE
		users
	SET
		user_blocked_from=".$t1.",
		user_blocked_to=".$t2.",
		user_ban_reason='".addslashes($reason)."',
		user_ban_admin_id='".$_SESSION[SESSION_NAME]['user_id']."'
	WHERE
		user_id='".$uid."'	
	;");
	$objResponse = new xajaxResponse();
  $objResponse->alert("Der Benutzer wurde gesperrt!");
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
	$objResponse->assign("c1_metal","innerHTML",nf($bc['metal']));
	$objResponse->assign("c1_crystal","innerHTML",nf($bc['crystal']));
	$objResponse->assign("c1_plastic","innerHTML",nf($bc['plastic']));
	$objResponse->assign("c1_fuel","innerHTML",nf($bc['fuel']));
	$objResponse->assign("c1_food","innerHTML",nf($bc['food']));
	$objResponse->assign("c1_power","innerHTML",nf($bc['power']));

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
		$objResponse->assign("b_metal_".$id,"innerHTML",nf($bct['metal']));
		$objResponse->assign("b_crystal_".$id,"innerHTML",nf($bct['crystal']));
		$objResponse->assign("b_plastic_".$id,"innerHTML",nf($bct['plastic']));
		$objResponse->assign("b_fuel_".$id,"innerHTML",nf($bct['fuel']));
		$objResponse->assign("b_food_".$id,"innerHTML",nf($bct['food']));
	}
		$objResponse->assign("t_metal","innerHTML",nf($bctt['metal']));
		$objResponse->assign("t_crystal","innerHTML",nf($bctt['crystal']));
		$objResponse->assign("t_plastic","innerHTML",nf($bctt['plastic']));
		$objResponse->assign("t_fuel","innerHTML",nf($bctt['fuel']));
		$objResponse->assign("t_food","innerHTML",nf($bctt['food']));
	
		
	return $objResponse;	
}


?>