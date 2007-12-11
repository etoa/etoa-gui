<?PHP

$xajax->register(XAJAX_FUNCTION,'launchSypProbe');

	function launchSypProbe($tid,$cid,$cif)
	{
		global $s;
		$objResponse = new xajaxResponse();
		ob_start();
		if ($s['user']['spyship_id']>0)
		{			
			$res = dbquery("
			SELECT
				shiplist_count
			FROM
				shiplist
			WHERE
				shiplist_ship_id=".$s['user']['spyship_id']."
				AND shiplist_planet_id=".$cid."
				AND shiplist_user_id=".$s['user']['id']."
				AND shiplist_count>=".$s['user']['spyship_count']."
			;");
			if (mysql_num_rows($res)>0)
			{
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
				}
			}
			else
			{
				echo "Auf deinem Planeten befinden sich keine Sonden des <a href=\"?page=userconfig&mode=game\">gewählten</a> Typs!<br/>";
			}
		}
		else
		{
			echo "Du hast noch keine Standard-Spionagesonde gewählt, überprüfe bitte deine <a href=\"?page=userconfig&mode=game\">Spieleinstellungen</a>!<br/>";
		}				
				
		$objResponse->assign("spy_info_box","style.display",'block');				
		$objResponse->append("spy_info","innerHTML",ob_get_contents());				
		ob_end_clean();
	  return $objResponse;	
	}

?>