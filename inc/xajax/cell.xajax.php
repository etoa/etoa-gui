<?PHP

$xajax->register(XAJAX_FUNCTION,'launchSypProbe');

	function launchSypProbe($tid)
	{
		global $cu;

		$cp = unserialize($_SESSION['currentEntity']);
		
		$objResponse = new xajaxResponse();
		ob_start();
		if ($cu->spyship_id>0)
		{			
			$fleet = new FleetLaunch($cp,$cu);
			if ($fleet->checkHaven())
			{
				if ($probeCount = $fleet->addShip($cu->spyship_id,$cu->spyship_count))
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
										echo "$probeCount Spionagesonden unterwegs";
									}
									else
										echo $fleet->error();
								}
								else
									echo $fleet->error();
							}
							else
								echo $fleet->error();
						}
						else
						{
							echo "Problem beim Finden des Zielobjekts!";
						}
					}
					else
					{
						echo $fleet->error();
					}				
				}
				else
				{
					echo "Auf deinem Planeten befinden sich keine Spionagesonden des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!";
				}
			}
			else
			{
				echo $fleet->error();
			}
		}
		else
		{
			echo "Du hast noch keine Standard-Spionagesonde gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!";
		}				
		echo "<br/>";
		$objResponse->assign("spy_info_box","style.display",'block');				
		$objResponse->append("spy_info","innerHTML",ob_get_contents());				
		ob_end_clean();
	  return $objResponse;	
	}


/*
				$fl = new Fleet($s['user']['id'],"so");
				$fl->setSourceByPlanetId($cid);
				if ($fl->setTargetByPlanetId(intval($tid)))
				{
					if ($fl->target->user_id>0 && $fl->target->user_id!=$s['user']['id'])
					{
						$fl->addShip($s['user']['spyship_id'],$s['user']['spyship_count']);
						$fl->calcDist();
						$fl->calcFlight();
						if ($fl->fuel <= $cif)
						{
							echo "<span style=\"color:#0f0\">Sonde gestartet!</span> 
							Ziel: ".$fl->target->sx."/".$fl->target->sy." : ".$fl->target->cx."/".$fl->target->cy." : ".$fl->target->pp."
							Entfernung: ".nf($fl->distance)." AE, Zeit: ".tf($fl->duration).", Kosten: ".nf($fl->fuel)." ".RES_FUEL."<br/>";
							$fl->launch();
						}
						else
						{
							echo "Zuwenig ".RES_FUEL." für diesen Flug (".$fl->fuel." benötigt)<br/>";
						}							
					}
					else
					{
						echo "Ungültiger Planet!<br/>";
					}
				}
				else
				{
					echo "Ungültiges Ziel!<br/>";
				}*/
?>