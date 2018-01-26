<?php
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//

	$for_user = 0;
	$for_alliance = 0;

	if ($_POST['ship_offer_reservation'] == 1)
	{
		$for_user = User::findIdByNick(trim($_POST['ship_offer_user_nick']));
		if ($for_user == 0)
		{
			$errMsg = "Reservation nicht möglich, Spieler nicht gefunden!";
		}
	}
	if ($_POST['ship_offer_reservation'] == 2)
	{
		if ($alliance_market_level > 0 && !$cd_enabled)
		{
			$for_alliance = $cu->allianceId;
		}
		else
		{
			$errMsg = "Reservation nicht möglich, Allianzmarkt nicht vorhanden oder nicht bereit!";
		}
	}

	if (empty($errMsg))
	{

		$ship_id = $_POST['ship_list'];
		$ship_count = nf_back($_POST['ship_count']);

		$cf=$cv="";
		$marr = array("ship_id"=>$ship_id,"ship_count"=>$ship_count);
		foreach ($resNames as $rk => $rn)
		{
			// Convert formatted number back to integer
			$_POST['ship_buy_'.$rk] = nf_back($_POST['ship_buy_'.$rk]);
			$cf.="costs_".$rk.",";
			$cv.=$_POST['ship_buy_'.$rk].",";
			$marr['buy_'.$rk] = $_POST['ship_buy_'.$rk];
		}

		// Überprüft ob die angegebene Anzahl Schiffe noch vorhanden ist (eventuelle Zerstörung durch Kampf?)
		$sl = new ShipList($cp->id,$cu->id);

		// Schiffe vom Planeten abziehen
        $removed_ships_count = $sl->remove($ship_id,$ship_count);

        // Falls alle Schiffe abgezogen werden konnten
		if ($ship_count == $removed_ships_count)
		{
			// Angebot speicherns
			dbquery("
			INSERT INTO
				market_ship
			(
				user_id,
				entity_id,
				ship_id,
				`count`,
				".$cf."
				for_user,
				for_alliance,
				`text`,
				datum
			)
			VALUES
					('".$cu->id."',
					'".$cp->id()."',
					'".$ship_id."',
					'".$ship_count."',
					".$cv."
					'".$for_user."',
					'".$for_alliance."',
					'".mysql_real_escape_string($_POST['ship_text'])."',
					'".time()."')");

			MarketReport::addMarketReport(array(
				'user_id'=>$cu->id,
				'entity1_id'=>$cp->id,
				'content'=>$_POST['ship_text']
				), "shipadd", mysql_insert_id(), $marr);


			if ($for_alliance > 0)
			{
				// Set cooldown
				$cd = time()+$cooldown;
				dbquery("
						UPDATE
							alliance_buildlist
						SET
							alliance_buildlist_cooldown=".$cd."
						WHERE
							alliance_buildlist_alliance_id='".$cu->allianceId."'
							AND alliance_buildlist_building_id='".ALLIANCE_MARKET_ID."';");

				$cu->alliance->buildlist->setCooldown(ALLIANCE_MARKET_ID,$cd);
			}

			success_msg("Angebot erfolgreich abgesendet!");
			return_btn();
		}
        else
		{
            // if only some ships have been removed, re-add the removed ships
			if($removed_ships_count > 0)
            {
                $sl->add($ship_id, $removed_ships_count);
                // log action because this was a bug earlier
                Log::add(Log::F_ILLEGALACTION,Log::WARNING,
                         'User '.$cu->nick.' hat versucht, auf dem Planeten'.$cp->name()
                         .' mehr Schiffe der ID '.$ship_id .' zu verkaufen, als vorhanden sind.'
                         .' Vorhanden: '.$removed_ships_count.', Versuchte Verkaufszahl: '.$ship_count);
            }
            error_msg("Die angegebenen Schiffe sind nicht mehr vorhanden!");
			return_btn();
		}
	}
	else
	{
		error_msg($errMsg);
	}
?>
