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
	// $Author$
	// $Date$
	// $Rev$
	//

	// Speichert Bieterangebot in Array
	$buyRes = array();
	foreach ($resNames as $rk => $rn)
	{
		if (isset($_POST['auction_new_buy_'.$rk]))
			$buyRes[$rk] = nf_back($_POST['auction_new_buy_'.$rk]);
		else
			$buyRes[$rk] = 0;
	}

	$res=dbquery("
	SELECT
		*
	FROM
		market_auction
	WHERE
		id='".$_POST['auction_market_id']."'
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

			foreach ($resNames as $rk => $rn)
			{
				// Errechnet Rohstoffwert vom Angebot
				$sell_price += $arr['sell_'.$rk] * $cfg->{"market_rate_".$rk}->v;
				// Errechnet Rohstoffwert vom Höchstbietenden
				$current_price += $arr['buy_'.$rk] * $cfg->{"market_rate_".$rk}->v;
				// Errechnet Rohstoffwert vom abgegebenen Gebot
				$new_price += $buyRes[$rk] * $cfg->{"market_rate_".$rk}->v;

				$currentBuyRes[$rk] = $arr['buy_'.$rk];
			}

			// Prüft, ob Gebot höher ist als das vom Höchstbietenden
			if($current_price < $new_price)
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
						$msg = "Du wurdest vom Spieler ".$cu->nick." in einer Auktion &uuml;berboten\n";
						$msg .= "Die Auktion ist nun zu Ende und wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht.\n";
						$msg .= "[URL=?page=market&mode=auction&id=".$arr['id']."Hier[/URL] gehts zu der Auktion.\n\n";

						$msg .= "Das Handelsministerium";
						send_msg($arr['current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
					}

					// Rohstoffe dem Gewinner abziehen
					$cp->subRes($buyRes);

					// Nachricht an Verkäufer
					$msg = "Ein Handel ist erfolgreich zustande gekommen.\n";
					$msg .= "Der Spieler ".$cu." hat von dir folgende Rohstoffe ersteigert: \n\n";

					$msg .= "".RES_METAL.": ".nf($arr['sell_0'])."\n";
					$msg .= "".RES_CRYSTAL.": ".nf($arr['sell_1'])."\n";
					$msg .= "".RES_PLASTIC.": ".nf($arr['sell_2'])."\n";
					$msg .= "".RES_FUEL.": ".nf($arr['sell_3'])."\n";
					$msg .= "".RES_FOOD.": ".nf($arr['sell_4'])."\n\n";

					$msg .= "Dies macht dich um folgende Rohstoffe reicher:\n";
					$msg .= "".RES_METAL.": ".nf($buyRes[0])."\n";
					$msg .= "".RES_CRYSTAL.": ".nf($buyRes[1])."\n";
					$msg .= "".RES_PLASTIC.": ".nf($buyRes[2])."\n";
					$msg .= "".RES_FUEL.": ".nf($buyRes[3])."\n";
					$msg .= "".RES_FOOD.": ".nf($buyRes[4])."\n\n";

					$msg .= "Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.\n\n";

					$msg .= "Das Handelsministerium";
					send_msg($arr['user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

					$seller = new User($arr['user_id']);

					// Nachricht an Käufer
					$msg = "Du warst der h&ouml;chstbietende in der Auktion vom Spieler ".$seller.".\n";
					$msg .= "Du hast folgende Rohstoffe ersteigert:\n\n";
					$msg .= "".RES_METAL.": ".nf($arr['sell_0'])."\n";
					$msg .= "".RES_CRYSTAL.": ".nf($arr['sell_1'])."\n";
					$msg .= "".RES_PLASTIC.": ".nf($arr['sell_2'])."\n";
					$msg .= "".RES_FUEL.": ".nf($arr['sell_3'])."\n";
					$msg .= "".RES_FOOD.": ".nf($arr['sell_4'])."\n\n";
					$msg .= "Dies hat dich folgende Rohstoffe gekostet:\n";
					$msg .= "".RES_METAL.": ".nf($buyRes[0])."\n";
					$msg .= "".RES_CRYSTAL.": ".nf($buyRes[1])."\n";
					$msg .= "".RES_PLASTIC.": ".nf($buyRes[2])."\n";
					$msg .= "".RES_FUEL.": ".nf($buyRes[3])."\n";
					$msg .= "".RES_FOOD.": ".nf($buyRes[4])."\n\n";
					$msg .= "Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.\n\n";
					$msg .= "Das Handelsministerium";
					send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

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
						user_multi_multi_user_id
					FROM
						user_multi
					WHERE
						user_multi_user_id='".$arr['user_id']."'
						AND user_multi_multi_user_id='".$cu->id."';");

					$multi_res2=dbquery("
					SELECT
						user_multi_multi_user_id
					FROM
						user_multi
					WHERE
						user_multi_user_id='".$cu->id."'
						AND user_multi_multi_user_id='".$arr['user_id']."';");

					if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
					{
						// TODO
						//add_log(10,"[URL=?page=user&sub=edit&user_id=".$cu->id."][B]".$cu->nick."[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=".$arr['auction_user_id']."][B]".$partner_user_nick."[/B][/URL] gewonnen:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."",time());
					}

					// Log schreiben
					//// TODO
					//add_log(7,"Es wurde folgende Auktion erfolgreich beendet: Der Spieler ".$cu->nick." hat vom Spieler ".$partner_user_nick."  folgende Waren ersteigert:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."\n\nDie Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht",time());


					echo "Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag geboten hast!<br/>";

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
						$msg = "Du wurdest vom Spieler ".$cu->nick." in einer Auktion &uuml;berboten\n";
						$msg .= "Die Auktion dauert noch bis am ".date("d.m.Y H:i",$arr['date_end']).".\n";
						$msg .= "[URL=?page=market&mode=auction&id=".$arr['id']."]Hier[/URL] gehts zu der Auktion.\n\n";

						$msg .= "Das Handelsministerium";
						send_msg($arr['current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
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
					echo "Gebot erfolgeich abgegeben!<br/>";
				}
			}
			else
			{
				echo "Das Gebot muss höher sein als vom Höchstbietenden!<br/>";
				echo "<p>".button("Zurück zur Auktion", "?page=market&amp;mode=search&amp;searchcat=auctions&amp;auctionid=".$arr['id']."")."</p>";
			}
		}
		else
		{
			echo "Die gebotenen Rohstoffe sind nicht mehr verfügbar!<br/>";
		}
	}
	else
	{
		echo "Die Auktion ist nicht mehr vorhanden oder bereits abgelaufen!<br/>";
	}
?>
