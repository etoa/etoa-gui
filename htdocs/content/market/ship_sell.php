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

use EtoA\Ship\ShipDataRepository;

$cnt = 0;
			$cnt_error = 0;

			foreach ($_POST['ship_market_id'] as $num => $id)
			{
				// Lädt Angebotsdaten
				$res = dbquery("
				SELECT
					*
				FROM
					market_ship
				WHERE
					id='".$id."'
					AND buyable='1';");
				// Prüft, ob Angebot noch vorhanden ist
				if (mysql_num_rows($res)!=0)
				{
					$arr = mysql_fetch_array($res);

					$buyarr = array();
					$mr = array("ship_id"=>$arr['ship_id'],"ship_count"=>$arr['count']);
					foreach ($resNames as $rk => $rn)
					{
						$buyarr[$rk] = $arr['costs_'.$rk];
						$mr['buy_'.$rk] = $arr['costs_'.$rk];
					}

					// Prüft, ob genug Rohstoffe vorhanden sind
					if ($cp->checkRes($buyarr))
					{
						$seller_user_nick = get_user_nick($arr['user_id']);

						// Rohstoffe vom Käuferplanet abziehen
						$cp->subRes($buyarr);

						$seller = new User($arr['user_id']);
						$sellerEntity = Entity::createFactoryById($arr['entity_id']);

						$tradeShip = new Ship(MARKET_SHIP_ID);

						$dist = $sellerEntity->distance($cp);
						$sellerFlighttime = ceil($dist / ($seller->specialist->tradeTime*$tradeShip->speed/3600) + $tradeShip->time2start+$tradeShip->time2land);
						$buyerFlighttime = ceil($dist / ($cu->specialist->tradeTime*$tradeShip->speed/3600) + $tradeShip->time2start+$tradeShip->time2land);

						$launchtime = time();
						$sellerLandtime = $launchtime + $sellerFlighttime;
						$buyerLandtime = $launchtime + $buyerFlighttime;

						// Fleet Seller -> Buyer
						dbquery("
						INSERT INTO
							fleet
						(
							user_id,
							entity_from,
							entity_to,
							launchtime,
							landtime,
							action,
							status
						)
						VALUES
						(
							".$cu->id.",
							".$sellerEntity->id.",
							".$cp->id.",
							".$launchtime.",
							".$buyerLandtime.",
							'market',
							0
						);");
						$sellerFid = mysql_insert_id();
						dbquery("
						INSERT INTO
							fleet_ships
						(
							fs_fleet_id,
							fs_ship_id,
							fs_ship_cnt
						)
						VALUES
						(
							".$sellerFid.",
							".$arr['ship_id'].",
							".$arr['count']."
						);");


						$numBuyerShip = ($tradeShip->capacity>0) ? ceil(array_sum($buyarr) / $tradeShip->capacity) : 1;

						// Fleet Buyer->Seller
						dbquery("
						INSERT INTO
							fleet
						(
							user_id,
							entity_from,
							entity_to,
							launchtime,
							landtime,
							action,
							res_metal,
							res_crystal,
							res_plastic,
							res_fuel,
							res_food,
							status
						)
						VALUES
						(
							".$seller->id.",
							".$cp->id.",
							".$sellerEntity->id.",
							".$launchtime.",
							".$sellerLandtime.",
							'market',
						".$buyarr[0].",
						".$buyarr[1].",
						".$buyarr[2].",
						".$buyarr[3].",
						".$buyarr[4].",
							0
						);");
						$buyerFid = mysql_insert_id();
						dbquery("
						INSERT INTO
							fleet_ships
						(
							fs_fleet_id,
							fs_ship_id,
							fs_ship_cnt
						)
						VALUES
						(
							".$buyerFid.",
							".MARKET_SHIP_ID.",
							".$numBuyerShip."
						);");


						dbquery("
							DELETE FROM
								market_ship
							WHERE
								id='".$id."'");
							$cnt++;


						// Send report to seller
						MarketReport::addMarketReport(array(
							'user_id'=>$arr['user_id'],
							'entity1_id'=>$arr['entity_id'],
							'entity2_id'=>$cp->id,
							'opponent1_id'=>$cu->id,
							), "shipsold", $arr['id'], array_merge($mr,array("fleet1_id"=>$sellerFid,"fleet2_id"=>$buyerFid)));

						// Send report to buyer (the current user)
						MarketReport::addMarketReport(array(
							'user_id'=>$cu->id,
							'entity1_id'=>$cp->id,
							'entity2_id'=>$arr['entity_id'],
							'opponent1_id'=>$arr['user_id'],
							), "shipbought", $arr['id'], array_merge($mr,array("fleet1_id"=>$buyerFid,"fleet2_id"=>$sellerFid)));

						// Add market ratings
						$cu->rating->addTradeRating(TRADE_POINTS_PER_TRADE,false,'Handel #'.$arr['id'].' mit '.$arr['user_id']);
						if (strlen($arr['text'])>TRADE_POINTS_TRADETEXT_MIN_LENGTH)
							$seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE+TRADE_POINTS_PER_TRADETEXT,true,'Handel #'.$arr['id'].' mit '.$cu->id);
						else
							$seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE,true,'Handel #'.$arr['id'].' mit '.$cu->id);



						//Log schreiben, falls dieser Handel regelwidrig ist
						$multi_res1=dbquery("
						SELECT
							multi_id
						FROM
							user_multi
						WHERE
							user_id='".$cu->id."'
							AND multi_id='".$arr['user_id']."';");

						$multi_res2=dbquery("
						SELECT
							multi_id
						FROM
							user_multi
						WHERE
							user_id='".$arr['user_id']."'
							AND multi_id='".$cu->id."';");

						if(mysql_num_rows($multi_res1)!=0 || mysql_num_rows($multi_res2)!=0)
						{
                            /** @var ShipDataRepository $shipRepository */
                            $shipRepository = $app[ShipDataRepository::class];
                            $shipNames = $shipRepository->getShipNames(true);

					    	Log::add(Log::F_MULTITRADE,Log::INFO,"[page user sub=edit user_id=".$cu->id."][B]".$cu->nick."[/B][/page] hat von [page user sub=edit user_id=".$arr['user_id']."][B]".$seller."[/B][/page] Schiffe gekauft:\n\n".$arr['count']." ".$shipNames[$arr['ship_id']]."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['costs_0'])."\n".RES_CRYSTAL.": ".nf($arr['costs_1'])."\n".RES_PLASTIC.": ".nf($arr['costs_2'])."\n".RES_FUEL.": ".nf($arr['costs_3'])."\n".RES_FOOD.": ".nf($arr['costs_4']));
						}

						//Marktlog schreiben
//						Log::add(7,Log::INFO, "Der Spieler ".$cu->nick." hat folgende Schiffe von ".$seller_user_nick." gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());

						// Zählt die erfolgreich abgewickelten Angebote

					}
					else
					{
						// Zählt die gescheiterten Angebote
						$cnt_error++;
					}
				}
				else
				{
					// Zählt die gescheiterten Angebote
					$cnt_error++;
				}
			}

			if($cnt > 0)
			{
				success_msg("".$cnt." Angebot(e) erfolgreich gekauft!");
			}
			if($cnt_error > 0)
			{
				error_msg("".$cnt_error." Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!");
			}

?>
