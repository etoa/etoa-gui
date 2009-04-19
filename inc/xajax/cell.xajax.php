<?PHP

$xajax->register(XAJAX_FUNCTION,'launchSypProbe');

	function launchSypProbe($tid)
	{
		global $cu;

		$cp = unserialize($_SESSION['currentEntity']);
		
		$objResponse = new xajaxResponse();
		ob_start();
		$launched = false;

		if ($cu->properties->spyShipId > 0)
		{			
			$fleet = new FleetLaunch($cp,$cu);
			if ($fleet->checkHaven())
			{
				if ($probeCount = $fleet->addShip($cu->properties->spyShipId,$cu->properties->spyShipCount))
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



?>