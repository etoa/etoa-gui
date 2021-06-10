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

$cnt = 0;
			$cnt_error = 0;

			$supplyTotal = array_fill(0,count($resNames),0);
			$demandTotal = array_fill(0,count($resNames),0);

			if(isset($_POST['ressource_market_id']))
			{
				foreach ($_POST['ressource_market_id'] as $num => $id)
				{
					// Lädt Angebotsdaten
					$res = dbquery("
					SELECT
						*
					FROM
						market_ressource
					WHERE
						id='".$id."'
						AND buyable='1'
						AND user_id!=".$cu->id."
						;");

					// Prüft, ob Angebot noch vorhanden ist
					if (mysql_num_rows($res)!=0)
					{
						$arr = mysql_fetch_assoc($res);

						// Prüft, ob genug Rohstoffe vorhanden sind
						$ok = true;
						$buyarr = array();
						$sellarr = array();
						foreach ($resNames as $rk => $rn)
						{
							$buyarr[$rk] = $arr['buy_'.$rk];
							$sellarr[$rk] = $arr['sell_'.$rk];
						}

						if ($cp->checkRes($buyarr))
						{
							$cp->subRes($buyarr);

							$seller = new User($arr['user_id']);
							$sellerEntity = Entity::createFactoryById($arr['entity_id']);

							$id = $sellerEntity->id;

							if (!isset($id)) {
								$id = 0;
							};

							$tradeShip = new Ship(MARKET_SHIP_ID);
							$numSellerShip = ($tradeShip->capacity>0) ? ceil(array_sum($sellarr) / $tradeShip->capacity) : 1;

							$dist = $sellerEntity->distance($cp);
							$sellerFlighttime = ceil($dist / ($seller->specialist->tradeTime*$tradeShip->speed/3600) + $tradeShip->time2start+$tradeShip->time2land );
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
								res_metal,
								res_crystal,
								res_plastic,
								res_fuel,
								res_food,
								status
							)
							VALUES
							(
								".$cu->id.",
								".$id.",
								".$cp->id.",
								".$launchtime.",
								".$buyerLandtime.",
								'market',
								".$sellarr[0].",
								".$sellarr[1].",
								".$sellarr[2].",
								".$sellarr[3].",
								".$sellarr[4].",
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
								".MARKET_SHIP_ID.",
								".$numSellerShip."
							);");
							$launched = true;


							if ($launched)
							{
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


								$launched = true;

								if ($launched)
								{

									// Angebot löschen
									dbquery("
									DELETE FROM
										market_ressource
									WHERE
										id='".$arr['id']."'");

									// Add values for market rate calculation and
									// fill array for the market report ($mr)
									$mr = array();
									foreach ($resNames as $rk => $rn)
									{
										$supplyTotal[$rk] += $arr['sell_'.$rk];
										$demandTotal[$rk] += $arr['buy_'.$rk];

										$mr['sell_'.$rk] = $arr['sell_'.$rk];
										$mr['buy_'.$rk] = $arr['buy_'.$rk];
									}

									// Send report to seller
									MarketReport::addMarketReport(array(
										'user_id'=>$arr['user_id'],
										'entity1_id'=>$arr['entity_id'],
										'entity2_id'=>$cp->id,
										'opponent1_id'=>$cu->id,
										), "ressold", $arr['id'], array_merge($mr,array("fleet1_id"=>$sellerFid,"fleet2_id"=>$buyerFid)));

									// Send report to buyer (the current user)
									MarketReport::addMarketReport(array(
										'user_id'=>$cu->id,
										'entity1_id'=>$cp->id,
										'entity2_id'=>$arr['entity_id'],
										'opponent1_id'=>$arr['user_id'],
										), "resbought", $arr['id'], array_merge($mr,array("fleet1_id"=>$buyerFid,"fleet2_id"=>$sellerFid)));

									// Add market ratings
									$cu->rating->addTradeRating(TRADE_POINTS_PER_TRADE,false,'Handel #'.$arr['id'].' mit '.$arr['user_id']);
									if (strlen($arr['text'])>TRADE_POINTS_TRADETEXT_MIN_LENGTH)
										$seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE+TRADE_POINTS_PER_TRADETEXT,true,'Handel #'.$arr['id'].' mit '.$cu->id);
									else
										$seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE,true,'Handel #'.$arr['id'].' mit '.$cu->id);

									// Log schreiben, falls dieser Handel regelwidrig ist
									// TODO: Think of an implementation using the user class...
									$multi_res1=dbquery("
									SELECT
										multi_id
									FROM
										user_multi
									WHERE
										multi_id='".$cu->id."'
										AND user_id='".$arr['user_id']."';");

									$multi_res2=dbquery("
									SELECT
										user_id
									FROM
										user_multi
									WHERE
										multi_id='".$arr['user_id']."'
										AND user_id='".$cu->id."';");

									if(mysql_num_rows($multi_res1)!=0 || mysql_num_rows($multi_res2)!=0)
									{
										Log::add(Log::F_MULTITRADE,Log::INFO,"[page user sub=edit user_id=".$cu->id."][B]".$cu->nick."[/B][/page] hat von [page user sub=edit user_id=".$arr['user_id']."][B]".$seller."[/B][/page] Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_0'])."\n".RES_CRYSTAL.": ".nf($arr['sell_1'])."\n".RES_PLASTIC.": ".nf($arr['sell_2'])."\n".RES_FUEL.": ".nf($arr['sell_3'])."\n".RES_FOOD.": ".nf($arr['sell_4'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_0'])."\n".RES_CRYSTAL.": ".nf($arr['buy_1'])."\n".RES_PLASTIC.": ".nf($arr['buy_2'])."\n".RES_FUEL.": ".nf($arr['buy_3'])."\n".RES_FOOD.": ".nf($arr['buy_4']));
									}

									// Zählt die erfolgreich abgewickelten Angebote
									$cnt++;
								}
								else
								{
									error_msg($str);
								}
							}
							else
							{
								error_msg($str);
							}
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
			}
			else
			{
				error_msg("Kein(e) Angebot(e) ausgewählt!");
			}

			if($cnt > 0)
			{
				success_msg("".$cnt." Angebot(e) erfolgreich gekauft!");
			}
			if($cnt_error > 0)
			{
				error_msg("".$cnt_error." Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!");
			}

			// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
			MarketHandler::addResToRate($supplyTotal,$demandTotal);

?>
