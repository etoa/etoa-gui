<?php

		if(!isset($_POST['ship_for_alliance']))
		{
			$_POST['ship_for_alliance']=0;
			$for_alliance="";
		}
		elseif ($alliance_market_level>0 && !$cd_enabled)
		{
			$_POST['ship_for_alliance']=$cu->allianceId;
			$for_alliance="f&uuml;r ein Allianzmitglied ";

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

			$cu->alliance->setMarketCooldown($cd);
		}
		else
		{
			$_POST['ship_for_alliance']=0;
			$for_alliance="";
		}

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
		if ($sl->remove($ship_id,$ship_count))
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
					'".$_POST['ship_for_alliance']."',
					'".addslashes($_POST['ship_text'])."',
					'".time()."')");

			MarketReport::add(array(
				'user_id'=>$cu->id,
				'entity1_id'=>$cp->id,
				'subject'=>"Schiffangebot ".$for_alliance."eingetragen",
				'content'=>$_POST['ship_text']
				), "shipadd", mysql_insert_id(), $marr);

			ok_msg("Angebot erfolgreich abgesendet!");
			return_btn();
		}
		else
		{
			error_msg("Die angegebenen Schiffe sind nicht mehr vorhanden!");
			return_btn();
		}


?>
