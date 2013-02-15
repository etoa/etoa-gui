<?PHP

$xajax->register(XAJAX_FUNCTION,'launchBookmarkProbe');
$xajax->register(XAJAX_FUNCTION,'searchShipList');
$xajax->register(XAJAX_FUNCTION,'bookmarkTargetInfo');
$xajax->register(XAJAX_FUNCTION,'bookmarkBookmark');

$xajax->register(XAJAX_FUNCTION,'showFleetCategorie');

// Spy and analyze probe also available on bookmark page
include_once('cell.xajax.php');

	function showFleetCategorie($cId)
	{
		$objResponse = new xajaxResponse();
		
		ob_start();
		
		$fbm = unserialize($_SESSION['bookmarks']['fbm']);
		
		echo $fbm->printBookmarks($cId);
		
		$_SESSION['bookmarks']['fbm'] = serialize($fbm);
		
		$objResponse->assign("bookmark$cId","innerHTML",ob_get_contents());				
		ob_end_clean();
		
		return $objResponse;
	}
	
	function launchBookmarkProbe($bid)
	{
		$cp = Entity::createFactoryById($_SESSION['cpid']);
		
		$objResponse = new xajaxResponse();

		ob_start();
		$launched = false;
		$bres = dbquery("
					   	SELECT
							target_id,
							ships,
							res,
							resfetch,
							action,
							speed
						FROM
							fleet_bookmarks
						WHERE
							id='".$bid."'
							AND user_id='".$cp->owner()->id."';");
		if (mysql_num_rows($bres))
		{
			$barr = mysql_fetch_assoc($bres);
			
			$fleet = new FleetLaunch($cp,$cp->owner());
			if ($fleet->checkHaven())
			{
				$shipOutput = "";
				$probeCount = true;
				$sidarr = explode(",",$barr['ships']);
				$sres = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 ORDER BY ship_type_id,ship_order;");
				while ($sarr = mysql_fetch_row($sres))
				{
					$ships[$sarr[0]] = $sarr[1];
				}
				foreach ($sidarr as $sd)
				{
					$sdi = explode(":",$sd);
					$probeCount = min($probeCount,$fleet->addShip($sdi[0],$sdi[1]));
					if ($shipOutput!="") $shipOutput .= ", ";
					$shipOutput .= $sdi[1]." ".$ships[$sdi[0]];
				}
				
				if ($probeCount)
				{
					if ($fleet->fixShips())
					{
						if ($ent = Entity::createFactoryById($barr['target_id']))
						{
							if ($fleet->setTarget($ent))
							{
								$fleet->setSpeedPercent($barr['speed']);
								if ($fleet->checkTarget())
								{
									if ($fleet->setAction($barr['action']))
									{
										$resarr = explode(",",$barr['res']);
										foreach ($resarr as $id=>$res)
										{
											$id++;
											if ($id==6)
											{
												$fleet->loadPeople($res);
											}
											else
											{
												$fleet->loadResource($id,$res,1);
											}
										}
										$fetcharr = explode(",",$barr['resfetch']);
										foreach ($fetcharr as $id=>$fetch)
										{
											$id++;
											$fleet->fetchResource($id,$fetch);
										}
										
										if ($fid = $fleet->launch())
										{
											$flObj = new Fleet($fid);
											
											
											$str= "Folgende Schiffe sind unterwegs: $shipOutput. Ankunft in ".tf($flObj->remainingTime());
											$launched = true;
										}
										else
											$str= $fleet->error();
									}
									else
										$str= $fleet->error();
								}
								else
									$str= $fleet->error();
							}
							else
								$str= $fleet->error();
						}
						else
						{
							$str= "Problem beim Finden des Zielobjekts!";
						}
					}
					else
					{
						$str= $fleet->error();
					}				
				}
				else
				{
					$str= "Auf deinem Planeten befinden sich nicht genug Schiffe der ausgewählten Typen!";
				}
			}
			else
			{
				$str= $fleet->error();
			}
		}
		else
		{
			$str= "Der ausgewählte Flottenfavorit ist ungültig!";
		}				
		if ($launched)
		{
			echo "<div style=\"color:#0f0\">".$str."<div>";
		}
		else
		{
			echo "<div style=\"color:#f90\">".$str."<div>";
		}

		$action_content = "<a href=\"javascript:;\" onclick=\"$('#fleet_bm_actions_" . $bid . "').html('Flotte wird gestartet...');xajax_launchBookmarkProbe(".$bid.");\"  onclick=\"\">Starten</a> 
							<a href=\"?page=bookmarks&amp;mode=new&amp;edit=".$bid."\">Bearbeiten</a> 
							<a href=\"?page=bookmarks&amp;mode=fleet&amp;del=".$bid."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>";
		$objResponse->assign("fleet_bm_actions_" . $bid, "innerHTML",$action_content);
		$objResponse->assign("fleet_info_box","style.display",'block');				
		$objResponse->append("fleet_info","innerHTML",ob_get_contents());				
		ob_end_clean();
	  return $objResponse;	
	}
	
	//Listet gefundene Schiffe auf
	function searchShipList($val)
	{
		$targetId = 'shiplist';
		$inputId = 'shipname';

	  	$sOut = "";
	  	$nCount = 0;
		
		$res=dbquery("SELECT 
			ship_name 
		FROM 
			ships 
		WHERE 
			(ship_show=1
				|| ship_buildable=1)
			AND ship_name LIKE '".$val."%' 
		LIMIT 20;");
		if (mysql_num_rows($res)>0)
	  {
			while($arr=mysql_fetch_row($res))
			{
		    $nCount++;
	      $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='".htmlspecialchars($arr[0])."';fleetBookmarkAddShipToList('".$arr[0]."');document.getElementById('$targetId').style.display = 'none';\">".htmlspecialchars($arr[0])."</a>";
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
			$objResponse->script("document.getElementById('$inputId').value=\"\"");
	   	 	$objResponse->script("fleetBookmarkAddShipToList('$sLastHit')");
	    }

	    $objResponse->assign("$targetId", "innerHTML", $sOut);
	    return $objResponse;
	}
	
	function bookmarkTargetInfo($form)
	{
		$response = new xajaxResponse();
		ob_start();
		
		if ($form['sx']!="" && $form['sy']!="" && $form['cx']!="" && $form['cy']!="" && $form['pos']!=""
		&& $form['sx']>0 && $form['sy']>0 && $form['cx']>0 && $form['cy']>0 && $form['pos']>=0)
		{		
			$absX = (($form['sx'] - 1)* CELL_NUM_X) + $form['cx'];
			$absY = (($form['sy']-1) * CELL_NUM_Y) + $form['cy'];	
			
			$user = new CurrentUser($_SESSION['user_id']);
			
			if ($user->discovered($absX,$absY) == 0)
				$code='u';
			else 
				$code = '';

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
					AND cells.sx=".$form['sx']."
					AND cells.sy=".$form['sy']."
					AND cells.cx=".$form['cx']."
					AND cells.cy=".$form['cy']."
					AND entities.pos=".$form['pos']."
				");
			if (mysql_num_rows($res)>0 && !($code=='u' && isset($form['man_p'])))
			{
				$arr=mysql_fetch_row($res);

				if ($code=='')
					$ent = Entity::createFactory($arr[1],$arr[0]);
				else
					$ent = Entity::createFactory($code,$arr[0]);
				
				echo "<img src=\"".$ent->imagePath()."\" style=\"float:left;\" >";

				echo "<br/>&nbsp;&nbsp; ".$ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")";
				$response->assign('targetinfo','style.background',"#000");
				$response->assign('submit',"style.display","");
				$response->assign('resbox',"style.display","");
			}
			else
			{
				echo "<div style=\"color:#f00\">Ziel nicht vorhanden!</div>";
				$response->assign('submit',"style.display","none");
				$response->assign('resbox',"style.display","none");
			}

			$response->assign('targetinfo','innerHTML',ob_get_contents());

			ob_end_clean();
		}
	  return $response;
	}
	
	function bookmarkBookmark($form)
	{
		$response = new xajaxResponse();
		
		if ($form["bookmarks"])
		{
			$ent = Entity::createFactoryById($form["bookmarks"]);
			$sx = $ent->sx();
			$sy = $ent->sy();
			$cx = $ent->cx();
			$cy = $ent->cy();
			$pos = $ent->pos();			
		
			$response->assign('sx','value',$sx);
			$response->assign('sy','value',$sy);
			$response->assign('cx','value',$cx);
			$response->assign('cy','value',$cy);
			$response->assign('pos','value',$pos);
			
			ob_start();
					
			echo "<img src=\"".$ent->imagePath()."\" style=\"float:left;\" >";
			
			echo "<br/>&nbsp;&nbsp; ".$ent." (".$ent->entityCodeString().", Besitzer: ".$ent->owner().")";
			$response->assign('targetinfo','style.background',"#000");
			
			$response->assign('targetinfo','innerHTML',ob_get_contents());
			$response->assign('submit',"style.display","");
			$response->assign('resbox',"style.display","");
			
			ob_end_clean();
		}
		return $response;
	}
	
?>