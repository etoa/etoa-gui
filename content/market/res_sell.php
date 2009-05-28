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
							if ($cp->resources[$rk] < $arr['buy_'.$rk])
							{
								$ok = false;
								break;
							}
							$buyarr[$rk] = $arr['buy_'.$rk];
							$sellarr[$rk] = $arr['sell_'.$rk];
						}

						if ($ok)
						{
							$seller = new User($arr['user_id']);
							$sellerEntity = Entity::createFactoryById($arr['entity_id']);

							// Fleet Seller -> Buyer
							$sellerShipList = new ShipList($sellerEntity->id,$seller->id);
							$tradeShip = new Ship(MARKET_SHIP_ID);
							$numSellerShip = ($tradeShip->capacity>0) ? ceil(array_sum($sellarr) / $tradeShip->capacity) : 1;

							$sellerShipList->add(MARKET_SHIP_ID, $numSellerShip);

							$launched = false;
							$fleet = new FleetLaunch($sellerEntity,$seller);
							if ($fleet->checkHaven())
							{
								if ($probeCount = $fleet->addShip(MARKET_SHIP_ID,$numSellerShip))
								{
									if ($fleet->fixShips())
									{
											if ($fleet->setTarget($cp))
											{
												if ($fleet->checkTarget())
												{
													if ($fleet->setAction("market"))
													{
														if ($sellerFid = $fleet->launch())
														{
															$flObj = new Fleet($sellerFid);
															$str = "Handelssschiffe unterwegs. Ankunft in ".tf($flObj->remainingTime());
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
										$str= $fleet->error();
									}
								}
								else
								{
									$str= "Auf dem Verkäuferplaneten befinden sich keine Handelsschiffe! ".$fleet->error();
								}
							}
							else
							{
								$str= $fleet->error();
							}

							//$cp->subRes($subarr)
							//
							// Rohstoffe vom Käuferplanet abziehen und $c-variabeln anpassen
							if ($launched)
							{
								$myShipList = new ShipList($cp->id,$cu->id);
								$numBuyerShip = ($tradeShip->capacity>0) ? ceil(array_sum($buyarr) / $tradeShip->capacity) : 1;
								$myShipList->add(MARKET_SHIP_ID, $numBuyerShip);

								$launched = false;
								$fleet = new FleetLaunch($cp,$cu);
								if ($fleet->checkHaven())
								{
									if ($probeCount = $fleet->addShip(MARKET_SHIP_ID,$numBuyerShip))
									{
										if ($fleet->fixShips())
										{
												if ($fleet->setTarget($sellerEntity))
												{
													if ($fleet->checkTarget())
													{
														if ($fleet->setAction("market"))
														{
															if ($buyerFid = $fleet->launch())
															{
																$flObj = new Fleet($buyerFid);
																$str = "Handel #".$arr['id'].": Handelssschiffe unterwegs. Ankunft in ".tf($flObj->remainingTime());
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
											$str= $fleet->error();
										}
									}
									else
									{
										$str= "Auf dem Käuferplaneten befinden sich keine Handelsschiffe! ".$fleet->error();
									}
								}
								else
								{
									$str= $fleet->error();
								}

								if ($launched)
								{
									// Angebot als gekauft markieren (wird zu einem späteren Zeitpunkt gelöscht)
									// todo: delete
									dbquery("
									UPDATE
										market_ressource
									SET
										buyable='0',
										buyer_id='".$cu->id."',
										buyer_entity_id='".$cp->id."'
									WHERE
										id='".$arr['id']."'");

									// Add values for market rate calculation and
									// fill array for the market report ($mr)
									$mr = array();
									foreach ($resNames as $rk => $rn)
									{
										// Faktor = Kaufzeit - Verkaufzeit (in ganzen Tagen, mit einem Max. von 7)
										// Total = Mengen / Faktor
										// deprecated: New market race calculation is checked every half hour,
										// so no daily average value us needed
										//$factor = min( ceil( (time() - $arr['datum']) / 3600 / 24 ) ,7);

										$supplyTotal[$rk] += $arr['sell_'.$rk];
										$demandTotal[$rk] += $arr['buy_'.$rk];

										$mr['sell_'.$rk] = $arr['sell_'.$rk];
										$mr['buy_'.$rk] = $arr['buy_'.$rk];
									}

									// Send report to seller
									MarketReport::add(array(
										'user_id'=>$arr['user_id'],
										'entity1_id'=>$arr['entity_id'],
										'entity2_id'=>$cp->id,
										'opponent1_id'=>$cu->id,
										'subject'=>"Rohstoffe verkauft",
										), "ressold", $arr['id'], array_merge($mr,array("fleet1_id"=>$sellerFid,"fleet2_id"=>$buyerFid)));

									// Send report to buyer (the current user)
									MarketReport::add(array(
										'user_id'=>$cu->id,
										'entity1_id'=>$cp->id,
										'entity2_id'=>$arr['entity_id'],
										'opponent1_id'=>$arr['user_id'],
										'subject'=>"Rohstoffe gekauft",
										), "resbought", $arr['id'], array_merge($mr,array("fleet1_id"=>$buyerFid,"fleet2_id"=>$sellerFid)));

									// Log schreiben, falls dieser Handel regelwidrig ist
									// TODO: Think of an implementation using the user class...
									$multi_res1=dbquery("
									SELECT
										user_multi_multi_user_id
									FROM
										user_multi
									WHERE
										user_multi_user_id='".$cu->id."'
										AND user_multi_multi_user_id='".$arr['user_id']."';");

									$multi_res2=dbquery("
									SELECT
										user_multi_multi_user_id
									FROM
										user_multi
									WHERE
										user_multi_user_id='".$arr['user_id']."'
										AND user_multi_multi_user_id='".$cu->id."';");

									if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
									{
										add_log(10,"[URL=?page=user&sub=edit&user_id=".$cu->id."][B]".$cu->nick."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$seller_user_nick."[/B][/URL] Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food']),time());
									}

									// Zählt die erfolgreich abgewickelten Angebote
									$cnt++;
								}
								else
								{
									err_msg($str);
								}
							}
							else
							{
								err_msg($str);
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
				err_msg("Kein(e) Angebot(e) ausgewählt!");
			}

			if($cnt > 0)
			{
				ok_msg("".$cnt." Angebot(e) erfolgreich gekauft!");
			}
			if($cnt_error > 0)
			{
				error_msg("".$cnt_error." Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!");
			}

			// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
			MarketHandler::addResToRate($supplyTotal,$demandTotal);

?>
