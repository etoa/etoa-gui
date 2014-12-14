<?PHP

$xajax->register(XAJAX_FUNCTION,'launchSypProbe');
$xajax->register(XAJAX_FUNCTION,'launchAnalyzeProbe');
$xajax->register(XAJAX_FUNCTION,'launchExplorerProbe');

	function launchSypProbe($tid)
	{
		$cp = Entity::createFactoryById($_SESSION['cpid']);
		
		$objResponse = new xajaxResponse();
		ob_start();
		$launched = false;
		
		if ($cp->owner()->properties->spyShipId > 0)
		{			
			$fleet = new FleetLaunch($cp,$cp->owner());
			if ($fleet->checkHaven())
			{
				if ($probeCount = $fleet->addShip($cp->owner()->properties->spyShipId,$cp->owner()->properties->spyShipCount))
				{
					if ($fleet->fixShips())
					{
						if ($ent = Entity::createFactoryById($tid))
						{
							if ($fleet->setTarget($ent))
							{
								if ($fleet->checkTarget())
								{
									if ($fleet->setAction("spy"))
									{
										if ($fid = $fleet->launch())
										{
											$flObj = new Fleet($fid);
											
											
											$str= "$probeCount Spionagesonden unterwegs. Ankunft in ".tf($flObj->remainingTime());
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
					$str= "Auf deinem Planeten befinden sich keine Spionagesonden des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
				}
			}
			else
			{
				$str= $fleet->error();
			}
		}
		else
		{
			$str= "Du hast noch keine Standard-Spionagesonde gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
		}				
		if ($launched)
		{
			echo "<div style=\"color:#0f0\">".$str."<div>";
		}
		else
		{
			echo "<div style=\"color:#f90\">".$str."<div>";
		}
		$objResponse->assign("spy_info_box","style.display",'block');				
		$objResponse->append("spy_info","innerHTML",ob_get_contents());				
		ob_end_clean();
	  return $objResponse;	
	}

	// add the following line to the php of the calling site:
	// $_SESSION['currentEntity']=serialize($cp);
	function launchAnalyzeProbe($tid)
	{
		$cp = unserialize($_SESSION['currentEntity']);
		
		$objResponse = new xajaxResponse();
		ob_start();
		$launched = false;
		
		if ($cp->owner()->properties->analyzeShipId > 0)
		{			
			$fleet = new FleetLaunch($cp,$cp->owner());
			if ($fleet->checkHaven())
			{
				if ($probeCount = $fleet->addShip($cp->owner()->properties->analyzeShipId,$cp->owner()->properties->analyzeShipCount))
				{
					if ($fleet->fixShips())
					{
						if ($ent = Entity::createFactoryById($tid))
						{
							if ($fleet->setTarget($ent))
							{
								if ($fleet->checkTarget())
								{
									if ($fleet->setAction("analyze"))
									{
										if ($fid = $fleet->launch())
										{
											$flObj = new Fleet($fid);
											
											
											$str= "$probeCount Analysatoren unterwegs. Ankunft in ".tf($flObj->remainingTime());
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
					$str= "Auf deinem Planeten befinden sich keine Analysatoren des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
				}
			}
			else
			{
				$str= $fleet->error();
			}
		}
		else
		{
			$str= "Du hast noch keinen Standard-Analysator gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
		}				
		if ($launched)
		{
			echo "<div style=\"color:#0f0\">".$str."<div>";
		}
		else
		{
			echo "<div style=\"color:#f90\">".$str."<div>";
		}
		$objResponse->assign("spy_info_box","style.display",'block');				
		$objResponse->append("spy_info","innerHTML",ob_get_contents());				
		ob_end_clean();
	  return $objResponse;	
	}
	
	// add the following line to the php of the calling site:
	// $_SESSION['currentEntity']=serialize($cp);
	function launchExplorerProbe($tcid)
	{
		$cp = unserialize($_SESSION['currentEntity']);
		
		$objResponse = new xajaxResponse();
		ob_start();
		$launched = false;
		
		if ($cp->owner()->properties->exploreShipId > 0)
		{			
			$fleet = new FleetLaunch($cp,$cp->owner());
			if ($fleet->checkHaven())
			{
				if ($probeCount = $fleet->addShip($cp->owner()->properties->exploreShipId,$cp->owner()->properties->exploreShipCount))
				{
					if ($fleet->fixShips())
					{
						$tc = new Cell($tcid);
						if ($tc->isValid())
						{
							$tce = $tc->getEntities();
							if (isset($tce[0]))
							{
								$ent = $tce[0];
								if ($fleet->setTarget($ent))
								{
									if ($fleet->checkTarget())
									{
										if ($fleet->setAction("explore"))
										{
											if ($fid = $fleet->launch())
											{
												$flObj = new Fleet($fid);
												
												
												$str= "$probeCount Explorer unterwegs. Ankunft in ".tf($flObj->remainingTime());
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
								$str= "Problem beim Finden des Zielobjekts (Objekt 0)!";
							}
						}
						else
						{
							$str= "Problem beim Finden des Zielobjekts (Zelle)!";
						}
					}
					else
					{
						$str= $fleet->error();
					}
				}
				else
				{
					$str= "Auf deinem Planeten befinden sich keine Explorer des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
				}
			}
			else
			{
				$str= $fleet->error();
			}
		}
		else
		{
			$str= "Du hast noch keinen Standard-Explorer gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
		}				
		if ($launched)
		{
			echo "<div style=\"color:#0f0\">".$str."<div>";
		}
		else
		{
			echo "<div style=\"color:#f90\">".$str."<div>";
		}
		$objResponse->assign("spy_info_box","style.display",'block');				
		$objResponse->append("spy_info","innerHTML",ob_get_contents());				
		ob_end_clean();
	  return $objResponse;	
	}

?>