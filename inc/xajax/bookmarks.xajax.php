<?PHP

$xajax->register(XAJAX_FUNCTION,'launchBookmarkProbe');
$xajax->register(XAJAX_FUNCTION,'addBookmarkShip');

	function launchBookmarkProbe($bid)
	{
		$cp = unserialize($_SESSION['currentEntity']);
		
		$objResponse = new xajaxResponse();
		
		ob_start();
		$launched = false;
		$bres = dbquery("
					   	SELECT
							target_id,
							ships,
							res,
							resfetch,
							action
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

	function addBookmarkShip($form,$delete=-1) {
		$objResponse = new xajaxResponse();
		
		ob_start();
		$res = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 ORDER BY ship_type_id,ship_order;");
		while ($arr = mysql_fetch_row($res))
		{
			$ships[$arr[0]] = $arr[1];
		}
		
		$cnt = 0;
		foreach ($form['scount'] as $k => $v)
		{
			if ($delete!=$k)
			{
				echo "<input type=\"text\" name=\"scount[]\" id=\"ship_".$cnt."\" value=\"".$v."\" size=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>&nbsp;
					<select name=\"sid[]\">";
				foreach ($ships as $k1 => $v1)
				{
					echo "<option ";
					if ($k1==$form['sid'][$k]) 
						echo "selected ";
					echo "value=\"".$k1."\">".$v1."</option>";
				}
				echo "</select>";
				echo "&nbsp;<a onclick=\"xajax_addBookmarkShip(xajax.getFormValues('shipboxadd'),$cnt);\"><img src=\"images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a>";
				echo "<br />";
				$cnt++;
			}
		}
		
		if ($delete<0 || $cnt==1 && $delete==0)
		{
			echo "<input type=\"text\" name=\"scount[]\" id=\"ship_".$cnt."\" value=\"1\" size=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>&nbsp;
				<select name=\"sid[]\">";
			foreach ($ships as $k => $v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select>";
			echo "&nbsp;<a onclick=\"xajax_addBookmarkShip(xajax.getFormValues('shipboxadd'),$cnt);\"><img src=\"images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a>";
		}
		
		$objResponse->assign("shipboxadd","innerHTML",ob_get_contents());				
		ob_end_clean();
		return $objResponse;
	}
?>