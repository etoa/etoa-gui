<?PHP

$xajax->registerFunction("planetSelectorByCell");
$xajax->registerFunction("planetSelectorByUser");
$xajax->registerFunction("showShipsOnPlanet");
$xajax->registerFunction("addShipToPlanet");
$xajax->registerFunction("removeShipFromPlanet");
$xajax->registerFunction("editShip");
$xajax->registerFunction("submitEditShip");
$xajax->registerFunction("searchUser");
$xajax->registerFunction("logSelectorCat");

function planetSelectorByCell($form,$show_user_id=0)
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
					id,
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
					$out="<select name=\"planet_id\" size=\"$nr\" onchange=\"xajax_showShipsOnPlanet(this.options[this.selectedIndex].value);\">";
					while ($parr=mysql_fetch_array($pres))
					{
						if ($show_user_id==1)
						{
							$val=$parr['id'].":".$parr['planet_user_id'];;
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

function planetSelectorByUser($userNick,$show_user_id=1)
{
	$objResponse = new xajaxResponse();
	if ($userNick!="")
	{
		$pres=dbquery("
		SELECT
			id,
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
			$out="<select name=\"planet_id\" size=\"$nr\" onchange=\"xajax_showShipsOnPlanet(this.options[this.selectedIndex].value);\">";
			while ($parr=mysql_fetch_array($pres))
			{
				if ($show_user_id==1)
				{
					$val=$parr['id'].":".$parr['planet_user_id'];;
				}
				else
				{
					$val=$parr['id'];
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
			shiplist
		INNER JOIN
			ships
			ON shiplist_ship_id=ship_id
			AND shiplist_entity_id='".$pid."'
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
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	DELETE FROM
		shiplist
	WHERE
		shiplist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showShipsOnPlanet(".$updata[0].");");
	return $objResponse;		
}

function editShip($form,$listId)
{
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	$res=dbquery("
	SELECT
		shiplist_count,
		shiplist_id
	FROM
		shiplist
	WHERE
		shiplist_entity_id=".$updata[0]."
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
	$objResponse = new xajaxResponse();	
	
	$updata=explode(":",$form['planet_id']);
	dbquery("
	UPDATE
		shiplist
	SET
		shiplist_count=".intval($form['editcnt_'.$listId])."
	WHERE
		shiplist_id=".intval($listId)."
	;");
  $objResponse->addScript("xajax_showShipsOnPlanet(".$updata[0].");");
	return $objResponse;		
}

//Listet gefundene User auf
function searchUser($val)
{
	$targetId = 'userlist';
	$inputId = 'user_nick';

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
      $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='".htmlentities($arr[0])."';xajax_planetSelectorByUser('".$arr[0]."');document.getElementById('$targetId').style.display = 'none';\">".htmlentities($arr[0])."</a>";
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
   	 		$objResponse->addScript("xajax_planetSelectorByUser('$sLastHit')");
    }

    $objResponse->addAssign("$targetId", "innerHTML", $sOut);

    return $objResponse->getXML();
}





?>
