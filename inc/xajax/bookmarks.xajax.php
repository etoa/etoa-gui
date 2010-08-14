<?PHP

$xajax->register(XAJAX_FUNCTION,'launchBookmarkProbe');
$xajax->register(XAJAX_FUNCTION,'searchShipList');
$xajax->register(XAJAX_FUNCTION,'addShipToList');
$xajax->register(XAJAX_FUNCTION,'removeShipFromList');
$xajax->register(XAJAX_FUNCTION,'bookmarkTargetInfo');
$xajax->register(XAJAX_FUNCTION,'bookmarkBookmark');

$xajax->register(XAJAX_FUNCTION,'showFleetCategorie');


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
	      $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='".htmlentities($arr[0])."';xajax_addShipToList('".$arr[0]."');document.getElementById('$targetId').style.display = 'none';\">".htmlentities($arr[0])."</a>";
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
	   	 	$objResponse->script("xajax_addShipToList('$sLastHit','".$function."')");
	    }

	    $objResponse->assign("$targetId", "innerHTML", $sOut);
	    return $objResponse;
	}
	
	function addShipToList($ship, $count=0)
	{
		defineImagePaths();
		$objResponse = new xajaxResponse();
		$objResponse->script("document.getElementById('shipname').value=\"\"");
		
		if (is_numeric($ship)) $sql = " AND ship_id='".$ship."'";
		else $sql = "AND ship_name='".$ship."'";
		$res = dbquery("
					SELECT
						ship_id,
						ship_name,
						special_ship,
						ship_actions,
						ship_shortcomment,
						ship_launchable
					FROM
						ships
					WHERE
						(ship_show=1
							|| ship_buildable=1)
						".$sql."
					LIMIT 1;");

		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			if (!in_array($arr['ship_id'], $_SESSION['bookmarks']['added']))
			{
				array_push($_SESSION['bookmarks']['added'], $arr['ship_id']);
				ob_start();
				echo "<tr id=\"ship_".$arr['ship_id']."\">";
				if($arr['special_ship']==1)
				{
					echo "<td style=\"width:40px;background:#000;\">
				    		<a href=\"?page=ship_upgrade&amp;id=".$arr['ship_id']."\">
				    		<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
				    		</a>
				    	</td>";
				}
				else
				{
 					echo "<td style=\"width:40px;background:#000;\">
							<a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['ship_id']."\">
							<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
							</a>
						</td>";
				}
				
				$actions = explode(",",$arr['ship_actions']);
				$accnt=count($actions);
				if ($accnt>0)
				{
					$acstr = "<br/><b>Fähigkeiten:</b> ";
					$x=0;
					foreach ($actions as $i)
					{
						if ($ac = FleetAction::createFactory($i))
						{
							$acstr.=$ac;
							if ($x<$accnt-1)
								$acstr.=", ";
						}
						$x++;
					}
					$acstr.="";
				}
				
 				echo "<td ".tm($arr['ship_name'],"<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_middle.".IMAGE_EXT."\" style=\"float:left;margin-right:5px;\">".text2html($arr['ship_shortcomment']."<br/>".$acstr."<br style=\"clear:both;\"/>")).">".$arr['ship_name']."</td>";
				echo "<td width=\"110\">";
				if ($arr['ship_launchable']==1)
				{
					echo "<input type=\"text\" 
							id=\"ship_count_".$arr['ship_id']."\" 
							name=\"ship_count[".$arr['ship_id']."]\" 
							size=\"10\" value=\"".$count."\"  
							title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\" 
							onclick=\"this.select();\" tabindex=\"".$tabulator."\" 
							onkeyup=\"FormatNumber(this.id,this.value,'','','');\"/>";
				}
				else
				{
 					echo "-";
				}
 				echo "</td><td><a onclick=\"xajax_removeShipFromList('".$arr['ship_id']."');\"><img src=\"images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a></td></tr>";
				$objResponse->append("input", "innerHTML", ob_get_contents());
				$objResponse->assign('saveShips',"style.display","");
				ob_end_clean();
				}
			}
		
		return $objResponse;
	}
	
	function removeShipFromList($shipId)
	{
		$response = new xajaxResponse();
		$response->script("document.getElementById('input').removeChild(document.getElementById('ship_".$shipId."').parentNode);");
		$key = array_search($shipId, $_SESSION['bookmarks']['added']);
		unset( $_SESSION['bookmarks']['added'][$key] );
		return $response;
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
			if (mysql_num_rows($res)>0 && !($code=='u' && $form['man_p']))
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