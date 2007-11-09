<?PHP

/* Support functions */


function getShipAbilities ($arr)
{
	$str = "";
	if ($arr['ship_colonialize']==1)
	{
		$str .= '- Kolonialisieren<br/>';
	}
	if ($arr['ship_invade']==1)
	{
		$str .= '- Invasieren<br/>';
	}
	if ($arr['ship_recycle']==1)
	{
		$str .= '- TrÃ¼mmer sammeln<br/>';
	}
	if ($arr['ship_nebula']==1)
	{
		$str .= '- Gas saugen<br/>';
	}
	if ($arr['ship_asteroid']==1)
	{
		$str .= '- Asteroiden sammeln<br/>';
	}
	if ($arr['ship_antrax']==1)
	{
		$str .= '- Giftgasangriff<br/>';
	}
	if ($arr['ship_forsteal']==1)
	{
		$str .= '- Technologie klauen<br/>';
	}
	if ($arr['ship_build_destroy']==1)
	{
		$str .= '- Bombardieren<br/>';
	}
	if ($arr['ship_tarned']==1)
	{
		$str .= '- Tarnangriff<br/>';
	}
	if ($arr['ship_fake']==1)
	{
		$str .= '- Fakeangriff<br/>';
	}
	if ($arr['ship_heal']>0)
	{
		$str .= '- Heilen<br/>';
	}
	if ($arr['ship_antrax_food']==1)
	{
		$str .= '- Antraxangriff<br/>';
	}
	if ($arr['ship_deactivade']==1)
	{
		$str .= '- GebÃ¤ude temporÃ¤r deaktivieren<br/>';
	}
	if ($arr['ship_tf']==1)
	{
		$str .= '- TrÃ¼mmerfeld erstellen<br/>';
	}
	
	if ($str!='')
		return "<br/><b>SpezialfÃ¤higkeiten:</b><br/><span style=\"color:#0f0;\">".$str."</span>";
	return '';
}

//Gibt alle Schiffe eines Planeten aus
function getShipsOnPlanet()
{
	global $s;
	/*	ship_id,
		ship_name,
		ship_pilots,
		ship_speed,
		ship_shortcomment,
*/

	$res = dbquery("
	SELECT
		ships.*,
		shiplist_count
	FROM
    shiplist
	INNER JOIN
  	ships
  	ON ship_id=shiplist_ship_id
    AND shiplist_planet_id='".$_SESSION['haven']['start_planet_id']."'
    AND shiplist_user_id='".$s['user']['id']."'
    AND shiplist_count>0
	ORDER BY
		ship_name;");
	while ($arr=mysql_fetch_array($res))
	{
		$ship[$arr['ship_id']]=$arr;
		$ship[$arr['ship_id']]['name']=$arr['ship_name'];
		$ship[$arr['ship_id']]['pilots']=$arr['ship_pilots'];
		$ship[$arr['ship_id']]['count']=$arr['shiplist_count'];
		$ship[$arr['ship_id']]['speed']=$arr['ship_speed'];
		$ship[$arr['ship_id']]['comment']=$arr['ship_shortcomment'];
	}
	
	return $ship;
}

/* XAJAX Functions */

function havenUpdateSelectedShips($id,$val)
{
  $objResponse = new xajaxResponse();   
	$_SESSION['haven']['fleet_ships'][$id]=$val;
	
	$sel = 0;
	$pil = 0;
	$speed = -1;
	foreach ($_SESSION['haven']['fleet_ships'] as $k => $v)
	{
		if ($v>0)	
		{
			$objResponse->addAssign("ship_name_".$k,"style.color","#0f0");		
	    $objResponse->addAssign('ship_'.$id, 'style.color', '#0f0');
		}
		else
		{
			$objResponse->addAssign("ship_name_".$k,"style.color","");		
	    $objResponse->addAssign('ship_'.$id, 'style.color', '');
		}

		$sel += $v;
		$pil += $v * $_SESSION['haven']['ships'][$k]['pilots'];
		if ($speed==-1)
			$speed = $_SESSION['haven']['ships'][$k]['speed'];
		else
			$speed = min($speed,$_SESSION['haven']['ships'][$k]['speed']);		
	}
	$_SESSION['haven']['fleet_ships_sel']=$sel;
	$_SESSION['haven']['pilots_reserved']=$pil;
	$_SESSION['haven']['fleet_speed']=$speed;
	
	
	
	$objResponse->addAssign("fleet_ships_sel","innerHTML",$sel);
	$objResponse->addAssign("fleet_ships_sel","style.color","#0f0");
	$objResponse->addAssign("fleet_speed","innerHTML",$speed);
	$objResponse->addAssign("pilots_reserved","innerHTML",$pil);
	if ($pil > $_SESSION['haven']['pilots_free'])
	{
		$objResponse->addAssign("pilots_reserved","style.color","#f00");
	}
	else
	{
		$objResponse->addAssign("pilots_reserved","style.color","#0f0");
	}
 	return $objResponse->getXML();
}

function havenCheckShipCount($id, $val)
{
	global $s;
	
  $objResponse = new xajaxResponse();    
  if ($val<0) 
  	$val = 0;
  if (!is_int($val))
    $val = intval($val);
  if ($val > $_SESSION['haven']['ships'][$id]['count'])
    $val = $_SESSION['haven']['ships'][$id]['count'];   

	$objResponse->addAssign('ship_'.$id, 'value', $val);
 	$objResponse->addScript("xajax_havenUpdateSelectedShips(".$id.",document.getElementById('ship_".$id."').value);");


	//$objResponse->addAssign('ship_info', 'innerHTML', ($_SESSION['haven']['ships'][$id]['pilots']*$val)." + ".$_SESSION['haven']['pilots_reserved']);   

/*
	if (($_SESSION['haven']['ships'][$id]['pilots']*$val) + $_SESSION['haven']['pilots_reserved'] > $_SESSION['haven']['pilots_free'])
	{                                    
		$val = (($_SESSION['haven']['pilots_free'] -  $_SESSION['haven']['pilots_reserved'])/$_SESSION['haven']['ships'][$id]['pilots']);
		$objResponse->addAssign('ship_'.$id, 'value', $val);   
		$objResponse->addAssign("pilots_reserved","style.color","#f00");
	}*/



  return $objResponse->getXML();
}

function havenDisplayShipBox()
{
	$objResponse = new xajaxResponse();

	$objResponse->addAssign('target_table', 'style.display', 'none');

	if ($_SESSION['haven']['shipbox']==1)
	{
		$objResponse->addAssign('ship_info_table', 'style.display', '');
		$objResponse->addAssign('ship_table', 'style.display', '');
	}
	else
	{
		$objResponse->addScript('xajax_havenLoadShipBox()');
	}
 	return $objResponse->getXML();
}

function havenLoadShipBox()
{
	global $s;
	$objResponse = new xajaxResponse();
	ob_start();
	
	$_SESSION['haven']['shipbox']=1;
	
	echo '<table class="tb" style="width:600px;" id="ship_info_table">';
	echo '<tr><th>VerfÃ¼gbare Piloten:</th>';
	if ($_SESSION['haven']['pilots_free']>0)
	{
		echo '<td style="color:#0f0;">'.nf($_SESSION['haven']['pilots_free']).'</td>';
	}
	else
	{
		echo '<td style="color:red;">Keine Piloten verfÃ¼gbar!</td>';
	}
	echo '<th>Reservierte Piloten:</th>';
	echo '<td id="pilots_reserved">0</td>';
	echo '</tr><tr>';
	echo '<th>Flottengeschwindigkeit:</th>';
	echo '<td id="fleet_speed">0 AE/h</td>';
	echo '<th>Anzahl Schiffe:</th>';
	echo '<td id="fleet_ships_sel">0</td>';
	echo '</tr><th>Info</th><td colspan="3" id="ship_info">-</td></table>';
	
	//if (!isset($_SESSION['haven']['ships']))
	//{
 		$_SESSION['haven']['ships'] = getShipsOnPlanet();
 		
 	//}
 	
	if (count($_SESSION['haven']['ships'])>0)
	{
		echo '<table class="tb" style="width:600px;" id="ship_table">';
		echo '<tr><th>Schiff</th><th>Speed</th><th>Piloten/Schiff</th><th>Vorhanden</th><th>Auswahl</th></tr>';
 		foreach ($_SESSION['haven']['ships'] as $k=>$v)
 		{
 			//$box.= "<tr><td style=\"width:40px;\"><img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$k."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"".$v['ship_name']."\" border=\"0\"/></td>";
 			
 			echo "<tr><td id=\"ship_name_".$k."\" ".tm($v['name'],$v['comment'].getShipAbilities($v)).">".$v['name']."</td>";
 			echo "<td>".$v['speed']." AE/h</td>";
 			echo "<td>".$v['pilots']."</td>";
			echo '<td>'.nf($v['count']).'</td>';
 			echo '<td>';
 			if ($_SESSION['haven']['pilots_free']>=$v['pilots'])
 			{
 				echo "<input type=\"text\" name=\"ship_count\" id=\"ship_".$k."\" onclick=\"selectText(this);\" onKeyUp=\"xajax_havenCheckShipCount($k,this.value);\" value=\"0\" size=\"5\" maxlength=\"".strlen($v['count'])."\"/>
 				<a href=\"javascript:;\" onclick=\"document.getElementById('ship_".$k."').value=".$v['count'].";xajax_havenCheckShipCount($k,".$v['count'].")\">Max</a>
 				<a href=\"javascript:;\" onclick=\"document.getElementById('ship_".$k."').value=0;xajax_havenCheckShipCount($k,0)\">Keine</a>
 				";
 			}
 			else
 			{
 				echo '<span style="color:red;">Zuwenig Piloten</span>';
 			}
 			echo '</td></tr>';
 		}
		echo '</table>';
 	}
 	else
 		echo "Keine Schiffe vorhanden!";
 	
	
	$objResponse->addAppend('havenBox', 'innerHTML', ob_get_contents());
	ob_end_clean();
 	return $objResponse->getXML();
}

function havenDisplayTargetBox()
{
	$objResponse = new xajaxResponse();

	$objResponse->addAssign('ship_info_table', 'style.display', 'none');
	$objResponse->addAssign('ship_table', 'style.display', 'none');

	if ($_SESSION['haven']['targetbox']==1)
	{
		$objResponse->addAssign('target_table', 'style.display', '');
	}
	else
	{
		$objResponse->addScript('xajax_havenLoadTargetBox()');
	}
 	return $objResponse->getXML();
}


function havenLoadTargetBox()
{
	$_SESSION['haven']['targetbox']=1;
 	$objResponse = new xajaxResponse();
	ob_start();
 	
	echo '<form id="targetForm"><table class="tb" id="target_table">';
	echo "<tr><th>Manuelle Zielwahl:</th><td>";
	echo "<input type=\"text\" name=\"target_sx\" id=\"target_sx\" onKeyUp=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\" value=\"".$_SESSION['haven']['target_sx']."\" onblur=\"\" size=\"2\" maxlength=\"2\"/> / ";
	echo "<input type=\"text\" name=\"target_sy\" id=\"target_sy\" onKeyUp=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\" value=\"".$_SESSION['haven']['target_sy']."\" onblur=\"\" size=\"2\" maxlength=\"2\"/> : ";
	echo "<input type=\"text\" name=\"target_cx\" id=\"target_cx\" onKeyUp=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\" value=\"".$_SESSION['haven']['target_cx']."\" onblur=\"\" size=\"2\" maxlength=\"2\"/> / ";
	echo "<input type=\"text\" name=\"target_cy\" id=\"target_cy\" onKeyUp=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\" value=\"".$_SESSION['haven']['target_cy']."\" onblur=\"\" size=\"2\" maxlength=\"2\"/> : ";
	echo "<input type=\"text\" name=\"target_pp\" id=\"target_pp\" onKeyUp=\"xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\" value=\"".$_SESSION['haven']['target_pp']."\" size=\"2\" maxlength=\"2\"/>";
	echo '</td></tr>';
	echo '<tr><th>Zielinfo</th><td id="targetinfo">0 AE</td>';
	echo '<tr><th>Geschwindigkeitsfaktor:</th><td><select id="speedfactor" name="speedfactor" onchange="xajax_havenTargetInfo(xajax.getFormValues(\'targetForm\'))">';
	for ($x=100;$x>0;$x-=10)
	{
		echo"<option value=\"$x\">$x %</option>";
	}
	echo '</select></td></tr>';
	echo '<tr><th>Geschwindigkeit</th><td id="fleet_speed_t">0 AE/h</td></tr>';
	echo '<tr><th>Zeit</th><td id="fleet_duration">0 s</td></tr>';
	echo '<tr><th>Entfernung</th><td id="fleet_distance">0 AE</td></tr>';
	echo'</table></form>';

	$objResponse->addAppend('havenBox', 'innerHTML', ob_get_contents());
	ob_end_clean();
 	return $objResponse->getXML();
}

function havenDisplayActionBox()
{
  	$objResponse = new xajaxResponse();
  	
	$box.= 'Under construction!';
	$objResponse->addAssign('havenBox', 'innerHTML', $box);
	
  	return $objResponse->getXML();
}

function havenTargetInfo($f)
{
	global $conf,$s;
	$objResponse = new xajaxResponse();
	ob_start();
	$launch = true;
	
	$sx1 = $_SESSION['haven']['source_sx'];
	$sy1 = $_SESSION['haven']['source_sy'];
	$cx1 = $_SESSION['haven']['source_cx'];
	$cy1 = $_SESSION['haven']['source_cy'];
	$pp1 = $_SESSION['haven']['source_pp'];
                                         	
	$sx2=intval($f['target_sx']);
	$sy2=intval($f['target_sy']);
	$cx2=intval($f['target_cx']);
	$cy2=intval($f['target_cy']);
	$pp2=intval($f['target_pp']);
		
	if ($sx2 < 1)
	{
		$sx2=1;
		$objResponse->addAssign("target_sx","value",1);										
	}
	if ($sy2 < 1)
	{
		$sy2=1;
		$objResponse->addAssign("target_sy","value",1);										
	}
	if ($cx2 < 1)
	{
		$cx2=1;
		$objResponse->addAssign("target_cx","value",1);										
	}
	if ($cy2 < 1)
	{
		$cy2=1;
		$objResponse->addAssign("target_cy","value",1);										
	}
	if ($sx2 > $conf['num_of_sectors']['p1'])
	{
		$sx2=$conf['num_of_sectors']['p1'];
		$objResponse->addAssign("target_sx","value",$sx2);										
	}
	if ($sy2 > $conf['num_of_sectors']['p2'])
	{
		$sy2=$conf['num_of_sectors']['p2'];
		$objResponse->addAssign("target_sy","value",$sy2);										
	}
	if ($cx2 > $conf['num_of_cells']['p1'])
	{
		$cx2=$conf['num_of_cells']['p1'];
		$objResponse->addAssign("target_cx","value",$cx2);										
	}
	if ($cy2 > $conf['num_of_cells']['p2'])
	{
		$cy2=$conf['num_of_cells']['p2'];
		$objResponse->addAssign("target_cy","value",$cy2);										
	}
	if ($pp2<1)
	{
		$pp2=1;
		$objResponse->addAssign("target_pp","value",$pp2);										
	}
	if ($pp2 > $conf['num_planets']['p2'])
	{
		$pp2=$conf['num_planets']['p2'];
		$objResponse->addAssign("target_pp","value",$pp2);										
	}

	$speed = $_SESSION['haven']['fleet_speed'] * $f['speedfactor'];

	if ($pp2>0)
	{
			$res = dbquery("
			SELECT
				planet_name,
				user_nick,
				user_id,
				cell_id,
				planet_id
			FROM
				space_cells
			INNER JOIN
			(
				planets
				LEFT JOIN
					users
					ON planet_user_id=user_id				
			)
			ON 
				planet_solsys_id=cell_id
				AND cell_sx=".intval($sx2)."
				AND cell_sy=".intval($sy2)."
				AND cell_cx=".intval($cx2)."
				AND cell_cy=".intval($cy2)."
				AND planet_solsys_pos=".intval($pp2)."
				
			");
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
					if ($s['user']['id']==$arr['user_id'] && $arr['user_id']>0)
					{
						$out.=' (Eigener Planet)';								
					}
				}
				$objResponse->addAssign("targetinfo","innerHTML",$out);								
				$objResponse->addAssign("targetinfo","style.color",'#0f0');								
			}			
			else
			{
				$objResponse->addAssign("targetinfo","innerHTML","Hier existiert kein Planet!");				
				$objResponse->addAssign("targetinfo","style.color",'#f00');		
			}
	}
	
	
	$dist = calcDistance($sx1,$sy1,$cx1,$cy1,$pp1,$sx2,$sy2,$cx2,$cy2,$pp2);
	$duration = $dist / $speed * 3600;	
		

	$objResponse->addAssign("fleet_distance","innerHTML",nf($dist)." AE");				
	$objResponse->addAssign("fleet_speed_t","innerHTML",nf($speed)." AE/h");				
	$objResponse->addAssign("fleet_duration","innerHTML",tf($duration));				

	$objResponse->addAppend("targetinfo","innerHTML",ob_get_contents());				
	ob_end_clean();
  return $objResponse->getXML();	
}



$objAjax->registerFunction('havenCheckShipCount');
$objAjax->registerFunction('havenLoadShipBox');
$objAjax->registerFunction('havenDisplayShipBox');
$objAjax->registerFunction('havenDisplayTargetBox');
$objAjax->registerFunction('havenLoadTargetBox');
$objAjax->registerFunction('havenDisplayActionBox');
$objAjax->registerFunction('havenTargetInfo');
$objAjax->registerFunction('havenUpdateSelectedShips');

?>