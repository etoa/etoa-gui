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

	// Speichert Bieterangebot in Array
	$buyRes = array();
	foreach ($resNames as $rk => $rn)
	{
		if (isset($_POST['new_buy_'.$rk]))
			$buyRes[$rk] = nf_back($_POST['new_buy_'.$rk]);
		else
			$buyRes[$rk] = 0;
	}

	$res=dbquery("
	SELECT
		*
	FROM
		market_auction
	WHERE
		id='".intval($_POST['auction_market_id'])."'
		AND user_id!=".$cu->id."
		AND date_end>'".time()."'
	");

	// Prüft, ob Angebot noch vorhaden ist
	if (mysql_num_rows($res)>0)
	{
		$arr = mysql_fetch_array($res);

		// Prüft, ob noch genug Rohstoffe vorhanden sind (eventueller Verlust durch Kampf?)
		if ($cp->checkRes($buyRes))
		{
			$sell_price = 0;
			$current_price = 0;
			$new_price = 0;

			$currentBuyRes = array();
			$marr = array();
			foreach ($resNames as $rk => $rn)
			{
				// Errechnet Rohstoffwert vom Angebot
				$sell_price += $arr['sell_'.$rk] * $cfg->{"market_rate_".$rk}->v;
				// Errechnet Rohstoffwert vom Höchstbietenden
				$current_price += $arr['buy_'.$rk] * $cfg->{"market_rate_".$rk}->v;
				// Errechnet Rohstoffwert vom abgegebenen Gebot
				$new_price += $buyRes[$rk] * $cfg->{"market_rate_".$rk}->v;

				$currentBuyRes[$rk] = $arr['buy_'.$rk];
				$marr['sell_'.$rk] = $arr['sell_'.$rk];
				$marr['buy_'.$rk] = $buyRes[$rk];
			}

			// Prüft, ob Gebot höher ist als das vom Höchstbietenden
			if($current_price*(1+AUCTION_OVERBID) < $new_price)
			{
						

				// wenn der bietende das höchst mögliche (oder mehr) bietet...
				if(AUCTION_PRICE_FACTOR_MAX <= (ceil($new_price)/floor($sell_price)))
				{
					if($arr['current_buyer_id']!=0)
					{
						// Rohstoffe dem überbotenen User wieder zurückgeben
						$highestBidderEntity = Entity::createFactoryById($arr['current_buyer_entity_id']);
						if ($highestBidderEntity->isValid())
						{
							$highestBidderEntity->addRes($currentBuyRes);
						}

						// Nachricht dem überbotenen User schicken
						$marr['timestamp2']='0';
						MarketReport::add(array(
							'user_id'=>$arr['current_buyer_id'],
							'entity1_id'=>$cp->id,
							'opponent1_id'=>$cu->id,
							), "auctionoverbid", $arr['id'], $marr);
					}

					// Rohstoffe dem Gewinner abziehen
					$cp->subRes($buyRes);

					// Nachricht an Verkäufer
					MarketReport::add(array(
						'user_id'=>$arr['user_id'],
						'entity1_id'=>$cp->id,
						'opponent1_id'=>$cu->id,
						), "auctionfinished", $arr['id'], $marr);

					MarketReport::add(array(
						'user_id'=>$cu->id,
						'entity1_id'=>$cp->id,
						'opponent1_id'=>$arr['user_id'],
						), "auctionwon", $arr['id'], $marr);
						
					// Add market ratings
					$seller = new User($arr['user_id']);
					$cu->rating->addTradeRating(TRADE_POINTS_PER_TRADE,false,'Handel #'.$arr['id'].' mit '.$arr['user_id']);
					if (strlen($arr['text'])>TRADE_POINTS_TRADETEXT_MIN_LENGTH)
						$seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE+TRADE_POINTS_PER_TRADETEXT,true,'Handel #'.$arr['id'].' mit '.$cu->id);
					else
						$seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE,true,'Handel #'.$arr['id'].' mit '.$cu->id);

					$resourceString = "";
					foreach ($resNames as $rk => $rn)
					{
						$resourceString.= "buy_$rk='".$buyRes[$rk]."',";
					}

					// Auktion Speichern und "Stoppen" so dass nicht mehr geboten werden kann
					$delete_date=time()+(AUCTION_DELAY_TIME*3600);
					dbquery("
					UPDATE
						market_auction
					SET
						current_buyer_id='".$cu->id."',
						current_buyer_entity_id='".$cp->id()."',
						current_buyer_date='".time()."',
						$resourceString
						buyable='0',
						date_delete=$delete_date,
						bidcount=bidcount+1
					WHERE
						id=".$arr['id']."");

					//Log schreiben, falls dieser Handel regelwidrig ist
					$multi_res1=dbquery("
					SELECT
						multi_id
					FROM
						user_multi
					WHERE
						user_id='".$arr['user_id']."'
						AND multi_id='".$cu->id."';");

					$multi_res2=dbquery("
					SELECT
						user_id
					FROM
						user_multi
					WHERE
						multi_id='".$cu->id."'
						AND user_id='".$arr['user_id']."';");

					if(mysql_num_rows($multi_res1)!=0 || mysql_num_rows($multi_res2)!=0)
					{
						// TODO
						Log::add(Log::F_MULTITRADE,Log::INFO,"[page user sub=edit user_id=".$cu->id."][B]".$cu->nick."[/B][/page] hat an einer Auktion von [page user sub=edit user_id=".$arr['user_id']."][B]".$seller."[/B][/page] gewonnen:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['sell_0'])."\n".RES_CRYSTAL.": ".nf($arr['sell_1'])."\n".RES_PLASTIC.": ".nf($arr['sell_2'])."\n".RES_FUEL.": ".nf($arr['sell_3'])."\n".RES_FOOD.": ".nf($arr['sell_4'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($_POST['new_buy_0'])."\n".RES_CRYSTAL.": ".nf($_POST['new_buy_1'])."\n".RES_PLASTIC.": ".nf($_POST['new_buy_2'])."\n".RES_FUEL.": ".nf($_POST['new_buy_3'])."\n".RES_FOOD.": ".nf($_POST['new_buy_4'])."",time());
					}

					// Log schreiben
					//// TODO
					//add_log(7,"Es wurde folgende Auktion erfolgreich beendet: Der Spieler ".$cu->nick." hat vom Spieler ".$partner_user_nick."  folgende Waren ersteigert:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."\n\nDie Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht",time());


					success_msg("Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag geboten hast!");

					// TODO: Market course update
				}
				else
				{
					if($arr['current_buyer_id']!=0)
					{
						// Rohstoffe dem überbotenen User wieder zurückgeben
						$highestBidderEntity = Entity::createFactoryById($arr['current_buyer_entity_id']);
						if ($highestBidderEntity->isValid())
						{
							$highestBidderEntity->addRes($currentBuyRes);
						}

						// Nachricht dem überbotenen user schicken
						$marr['timestamp2']=$arr['date_end'];
						MarketReport::add(array(
							'user_id'=>$arr['current_buyer_id'],
							'entity1_id'=>$cp->id,
							'opponent1_id'=>$cu->id,
							), "auctionoverbid", $arr['id'], $marr);
					}


					// Rohstoffe vom neuen Bieter abziehen
					$cp->subRes($buyRes);

					$resourceString = "";
					foreach ($resNames as $rk => $rn)
					{
						$resourceString.= "buy_$rk='".$buyRes[$rk]."',";
					}

					//Das neue Angebot Speichern
					dbquery("
					UPDATE
					  market_auction
					SET
						$resourceString
						current_buyer_id='".$cu->id."',
						current_buyer_entity_id='".$cp->id()."',
						current_buyer_date='".time()."',
						bidcount=bidcount+1
					WHERE
						id='".$arr['id']."';");
					success_msg("Gebot erfolgeich abgegeben!");
					echo "<p>".button("Zurück zur Auktion", "?page=market&amp;mode=search&amp;searchcat=auctions&amp;auctionid=".$arr['id']."")."</p>";
				}
			}
			else
			{
				error_msg("Das Gebot muss mindestens ".AUCTION_OVERBID."% höher sein als das Gebot des Höchstbietenden!");
				echo "<p>".button("Zurück zur Auktion", "?page=market&amp;mode=search&amp;searchcat=auctions&amp;auctionid=".$arr['id']."")."</p>";
			}
		}
		else
		{
			error_msg("Die gebotenen Rohstoffe sind nicht mehr verfügbar!");
		}
	}
	else
	{
		error_msg("Die Auktion ist nicht mehr vorhanden oder bereits abgelaufen!");
	}
?>
