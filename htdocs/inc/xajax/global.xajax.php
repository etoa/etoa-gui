<?PHP

use EtoA\Core\Configuration\ConfigurationService;

$xajax->register(XAJAX_FUNCTION,'searchUser');
$xajax->register(XAJAX_FUNCTION,'getFlightTargetInfo');
$xajax->register(XAJAX_FUNCTION,'getCryptoDistance');
$xajax->register(XAJAX_FUNCTION,'formatNumbers');

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
		$val=trim($nick);
		$temp=$val;
	}
	$sOut = "";
	$nCount = 0;
	$sLastHit = null;

	$res=dbquery("SELECT user_nick FROM users WHERE user_nick LIKE '".$val."%' LIMIT 20;");
	if (mysql_num_rows($res)>0)
	{
		while($arr=mysql_fetch_row($res))
		{
			$nCount++;
			$sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('".$field_id."').value=(document.getElementById('".$field_id."').value && document.getElementById('".$field_id."').value.indexOf(';')!=-1)?document.getElementById('".$field_id."').value.replace(/^(.+);[^;]+$/,'$1;')+'".htmlentities($arr[0],ENT_QUOTES,'UTF-8')."':'".htmlentities($arr[0],ENT_QUOTES,'UTF-8')."';document.getElementById('".$box_id."').style.display = 'none';\">".htmlentities($arr[0],ENT_QUOTES,'UTF-8')."</a>";
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
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app['etoa.config.service'];

	global $s;
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
	if ($sx > $config->param1Int('num_of_sectors'))
	{
		$sx = $config->param1Int('num_of_sectors');
		$objResponse->assign("sx","value",$sx);
	}
	if ($sy > $config->param2Int('num_of_sectors'))
	{
		$sy = $config->param2Int('num_of_sectors');
		$objResponse->assign("sy","value",$sy);
	}
	if ($cx > $config->param1Int('num_of_cells'))
	{
		$cx = $config->param1Int('num_of_cells');
		$objResponse->assign("cx","value",$cx);
	}
	if ($cy > $config->param2Int('num_of_cells'))
	{
		$cy = $config->param2Int('num_of_cells');
		$objResponse->assign("cy","value",$cy);
	}
	if ($p<1)
	{
		$p=1;
		$objResponse->assign("p","value",$p);
	}
	if ($p > $config->param2Int('num_planets'))
	{
		$p = $config->param2Int('num_planets');
		$objResponse->assign("p","value",$p);
	}

	// Total selected missiles
	$total=0;
	$speed = 0;
	$range = 0;

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

	if ($total > 0) {
		if ($p > 0) {
			$target = Entity::createFactoryByCoords($sx, $sy, $cx, $cy, $p);
			if (!$target instanceof Planet) {
				$objResponse->assign("targetinfo","innerHTML","Hier existiert kein Planet!");
				$objResponse->assign("targetinfo","style.color",'#f00');
				$launch=false;
				$objResponse->assign("targetcell","value",0);
				$objResponse->assign("targetplanet","value",0);
			} else {
				if ($target->name()) {
					$out = "<b>Planet:</b> ".$target->name();
				} else {
					$out = "<i>Unbenannter Planet</i>";
				}

				if ($target->owner()) {
					$out.=" <b>Besitzer:</b> " . $target->owner()->nick;
					if ($s->getInstance()->user_id == $target->owner()->id) {
						$out.=' (Eigener Planet)';
						$objResponse->assign("targetinfo","style.color",'#f00');
						$launch=false;
					} else {
						$objResponse->assign("targetinfo","style.color",'#0f0');
					}
				} else {
					$objResponse->assign("targetinfo","style.color",'#f00');
					$launch=false;
				}

				$objResponse->assign("targetinfo","innerHTML",$out);
				$objResponse->assign("targetcell","value",$target->cellId());
				$objResponse->assign("targetplanet","value",$target->id());
			}

			if ($target) {
				$distanceValue = $target->distanceByCoords($sx1, $sy1, $cx1, $cy1, $p1);
				$timeforflight = $distanceValue / $speed * 3600;
				$distance = sprintf('%s AE', nf($distanceValue));
			} else {
				$distanceValue = -1;
				$distance = '-';
				$timeforflight = null;
			}

			$objResponse->assign("time","innerHTML",tf($timeforflight));
			$objResponse->assign("timeforflight","value",$timeforflight);
			$objResponse->assign("distance","innerHTML",$distance);

			if ($distanceValue === -1) {
				$objResponse->assign("distance","style.color","#f00");
				$launch=false;
			} elseif ($distanceValue > $range) {
				$objResponse->assign("distance","style.color","#f00");
				$objResponse->append("distance","innerHTML"," (zu weit entfernt, ".nf($range)." max)");
				$launch=false;
			} else {
				$objResponse->assign("distance","style.color","#0f0");
			}
			$objResponse->assign("speed","innerHTML",round($speed,2)." AE/h");
		} else {
			$launch=false;
		}
	} else {
		$objResponse->assign("targetinfo","innerHTML","Keine Raketen gewählt!");
		$launch=false;
	}

	if ($launch) {
		$objResponse->assign("launchbutton","style.color",'#0f0');
		$objResponse->assign("launchbutton","disabled",false);
	} else {
		$objResponse->assign("launchbutton","style.color",'#f00');
		$objResponse->assign("launchbutton","disabled",true);
	}

	$objResponse->append("targetinfo","innerHTML",ob_get_contents());
	ob_end_clean();
	return $objResponse;
}


function getCryptoDistance($f,$sx1,$sy1,$cx1,$cy1,$p1)
{
	global $s;

    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app['etoa.config.service'];

	$objResponse = new xajaxResponse();
	ob_start();

	$sx=intval($f['sx']);
	$sy=intval($f['sy']);
	$cx=intval($f['cx']);
	$cy=intval($f['cy']);
	$p=intval($f['p']);

	$range= $f['range'];

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
	if ($sx>$config->param1Int('num_of_sectors'))
	{
		$sx=$config->param1Int('num_of_sectors');
		$objResponse->assign("sx","value",$sx);
	}
	if ($sy>$config->param2Int('num_of_sectors'))
	{
		$sy=$config->param2Int('num_of_sectors');
		$objResponse->assign("sy","value",$sy);
	}
	if ($cx>$config->param1Int('num_of_cells'))
	{
		$cx=$config->param1Int('num_of_cells');
		$objResponse->assign("cx","value",$cx);
	}
	if ($cy>$config->param2Int('num_of_cells'))
	{
		$cy=$config->param2Int('num_of_cells');
		$objResponse->assign("cy","value",$cy);
	}
	if ($p<1)
	{
		$p=1;
		$objResponse->assign("p","value",$p);
	}
	if ($p>$config->param2Int('num_planets'))
	{
		$p=$config->param2Int('num_planets');
		$objResponse->assign("p","value",$p);
	}

	$target = Entity::createFactoryByCoords($sx,$sy,$cx,$cy,$p);
	if (!$target instanceof Planet) {
		$objResponse->assign("targetinfo","innerHTML","Hier existiert kein Planet!");
		$objResponse->assign("targetinfo","style.color",'#f00');
		$objResponse->assign("targetcell","value",0);
		$objResponse->assign("targetplanet","value",0);
		$objResponse->assign("distance", "innerHTML", "-");
		$objResponse->assign("distance","style.color","#f00");

		return $objResponse;
	}

	if ($target->name) {
		$out = "<b>Planet:</b> " . $target->name;
	} else {
		$out = "<i>Unbenannter Planet</i>";
	}

	if ($target->owner()->isValid) {
		$owner = $target->owner();
		$out .= " <b>Besitzer:</b> " . $owner->nick;
		if ($s->getInstance()->user_id == $owner->id && $owner->id > 0) {
			$out.=' (Eigener Planet)';
			$objResponse->assign("targetinfo","style.color",'#f00');
		} else {
			$objResponse->assign("targetinfo","style.color",'#0f0');
		}
	} else {
		$objResponse->assign("targetinfo","style.color",'#f00');
	}

	$objResponse->assign("targetinfo","innerHTML",$out);
	$objResponse->assign("targetcell","value", $target->cellId());
	$objResponse->assign("targetplanet","value", $target->id());

	$launch=false; // TODO this looks broken. I think this should be true
	$distance = $target->distanceByCoords($sx1, $sy1, $cx1, $cy1, $p1);

	$objResponse->assign("distance","innerHTML",nf($distance)." AE");
	if ($distance > $range) {
		$objResponse->assign("distance","style.color","#f00");
		$objResponse->append("distance","innerHTML"," (zu weit entfernt, ".nf($range)." max)");
		$launch=false;
	} else {
		$objResponse->assign("distance","style.color","#0f0");
	}

	if ($launch) {
		$objResponse->assign("scan","style.color",'#0f0');
		$objResponse->assign("scan","disabled",false);
	} else {
		$objResponse->assign("scan","style.color",'#f00');
		$objResponse->assign("scan","disabled",true);
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

	$val = abs((int) $val);
	if($format==1) {
		$out = nf($val);
	} else {
		$out = $val;
	}


	$objResponse->assign($field_id,"value",$out);

	$objResponse->assign("population_info","innerHTML",ob_get_contents());
	ob_end_clean();
  return $objResponse;
}

function sendMsg($userString, $subject, $message)
{
    global $app;

    /** @var \EtoA\User\UserRepository $userRepository */
    $userRepository = $app['etoa.user.repository'];

	 $objResponse = new xajaxResponse();

	 $userArr = explode(";", $userString);
	 $senderId = $_SESSION['user_id'];
	 $out = '';
	 foreach ($userArr as $userToNick)
	 {
		  $uid = $userRepository->getUserIdByNick($userToNick);
		  if ($uid !== null) {
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
