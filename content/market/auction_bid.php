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

			$_POST['auction_new_buy_metal'] = nf_back($_POST['auction_new_buy_metal']);
			$_POST['auction_new_buy_crystal'] = nf_back($_POST['auction_new_buy_crystal']);
			$_POST['auction_new_buy_plastic'] = nf_back($_POST['auction_new_buy_plastic']);
			$_POST['auction_new_buy_fuel'] = nf_back($_POST['auction_new_buy_fuel']);
			$_POST['auction_new_buy_food'] = nf_back($_POST['auction_new_buy_food']);

			$res=dbquery("
			SELECT
				*
			FROM
				market_auction
			WHERE
        auction_market_id='".$_POST['auction_market_id']."'
        AND auction_end>'".time()."'
        AND auction_buyable='1'");
      // Prüft, ob Angebot noch vorhaden ist
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);

				// Prüft, ob noch genug Rohstoffe vorhanden sind (eventueller Verlust durch Kampf?)
				if ($cp->resMetal >= $_POST['auction_new_buy_metal']
					&& $cp->resCrystal >= $_POST['auction_new_buy_crystal']
					&& $cp->resPlastic >= $_POST['auction_new_buy_plastic']
					&& $cp->resFuel >= $_POST['auction_new_buy_fuel']
					&& $cp->resFood >= $_POST['auction_new_buy_food'])
				{
					// Errechnet Rohstoffwert vom Angebot
					$sell_price = $arr['auction_sell_metal'] * MARKET_METAL_FACTOR
											+ $arr['auction_sell_crystal'] * MARKET_CRYSTAL_FACTOR
											+ $arr['auction_sell_plastic'] * MARKET_PLASTIC_FACTOR
											+ $arr['auction_sell_fuel'] * MARKET_FUEL_FACTOR
											+ $arr['auction_sell_food'] * MARKET_FOOD_FACTOR;
					// Errechnet Rohstoffwert vom Höchstbietenden
					$current_price = 	$arr['auction_buy_metal'] * MARKET_METAL_FACTOR
													+ $arr['auction_buy_crystal'] * MARKET_CRYSTAL_FACTOR
													+ $arr['auction_buy_plastic'] * MARKET_PLASTIC_FACTOR
													+ $arr['auction_buy_fuel'] * MARKET_FUEL_FACTOR
													+ $arr['auction_buy_food'] * MARKET_FOOD_FACTOR;
					// Errechnet Rohstoffwert vom abgegebenen Gebot
					$new_price = 	$_POST['auction_new_buy_metal'] * MARKET_METAL_FACTOR
											+ $_POST['auction_new_buy_crystal'] * MARKET_CRYSTAL_FACTOR
											+ $_POST['auction_new_buy_plastic'] * MARKET_PLASTIC_FACTOR
											+ $_POST['auction_new_buy_fuel'] * MARKET_FUEL_FACTOR
											+ $_POST['auction_new_buy_food'] * MARKET_FOOD_FACTOR;

					// Prüft, ob Gebot höher ist als das vom Höchstbietenden
					if($current_price < $new_price)
					{
            $partner_user_nick=get_user_nick($arr['auction_user_id']);

            // wenn der bietende das höchst mögliche (oder mehr) bietet...
            if(AUCTION_PRICE_FACTOR_MAX <= (ceil($new_price)/floor($sell_price)))
            {
                if($arr['auction_current_buyer_id']!=0)
                {
                	// TODO: use planet class
                    // Rohstoffe dem überbotenen User wieder zurückgeben
                    dbquery("
                    UPDATE
                        planets
                    SET
                        planet_res_metal=planet_res_metal+".$arr['auction_buy_metal'].",
                        planet_res_crystal=planet_res_crystal+".$arr['auction_buy_crystal'].",
                        planet_res_plastic=planet_res_plastic+".$arr['auction_buy_plastic'].",
                        planet_res_fuel=planet_res_fuel+".$arr['auction_buy_fuel'].",
                        planet_res_food=planet_res_food+".$arr['auction_buy_food']."
                    WHERE
                        id=".$arr['auction_current_buyer_planet_id']."
                        AND planet_user_id=".$arr['auction_current_buyer_id']."");

                    // Nachricht dem überbotenen User schicken
                    $msg = "Du wurdest vom Spieler ".$cu->nick." in einer Auktion &uuml;berboten\n";
                    $msg .= "Die Auktion ist nun zu Ende und wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht.\n";
                    $msg .= "[URL=?page=market&mode=auction&id=".$arr['auction_market_id']."Hier[/URL] gehts zu der Auktion.\n\n";

                    $msg .= "Das Handelsministerium";
                    send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
                }

                	// TODO: use planet class
                // Rohstoffe dem Gewinner abziehen
                dbquery("
                UPDATE
                    planets
                SET
                    planet_res_metal=planet_res_metal-'".$_POST['auction_new_buy_metal']."',
                    planet_res_crystal=planet_res_crystal-'".$_POST['auction_new_buy_crystal']."',
                    planet_res_plastic=planet_res_plastic-'".$_POST['auction_new_buy_plastic']."',
                    planet_res_fuel=planet_res_fuel-'".$_POST['auction_new_buy_fuel']."',
                    planet_res_food=planet_res_food-'".$_POST['auction_new_buy_food']."'
                WHERE
                    id='".$cp->id()."'
                    AND planet_user_id='".$cu->id."'");


                // Nachricht an Verkäufer
                $msg = "Ein Handel ist erfolgreich zustande gekommen.\n";
                $msg .= "Der Spieler ".$cu->nick." hat von dir folgende Rohstoffe ersteigert: \n\n";

                $msg .= "".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n";
                $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n";
                $msg .= "".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n";
                $msg .= "".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n";
                $msg .= "".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";

                $msg .= "Dies macht dich um folgende Rohstoffe reicher:\n";
                $msg .= "".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n";
                $msg .= "".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n";
                $msg .= "".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n";
                $msg .= "".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n";
                $msg .= "".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."\n\n";

                $msg .= "Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.\n\n";

                $msg .= "Das Handelsministerium";
                send_msg($arr['auction_user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

                // Nachricht an Käufer
                $msg = "Du warst der h&ouml;chstbietende in der Auktion vom Spieler ".$partner_user_nick.".\n";
                $msg .= "Du hast folgende Rohstoffe ersteigert:\n\n";

                $msg .= "".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n";
                $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n";
                $msg .= "".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n";
                $msg .= "".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n";
                $msg .= "".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";

                $msg .= "Dies hat dich folgende Rohstoffe gekostet:\n";
                $msg .= "".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n";
                $msg .= "".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n";
                $msg .= "".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n";
                $msg .= "".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n";
                $msg .= "".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."\n\n";

                $msg .= "Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.\n\n";

                $msg .= "Das Handelsministerium";
                send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);


                // Auktion Speichern und "Stoppen" so dass nicht mehr geboten werden kann
                $delete_date=time()+(AUCTION_DELAY_TIME*3600);
                dbquery("
                UPDATE
                    market_auction
                SET
                    auction_current_buyer_id='".$cu->id."',
                    auction_current_buyer_planet_id='".$cp->id()."',
                    auction_current_buyer_cell_id='".$cp->cellId()."',
                    auction_current_buyer_date='".time()."',
                    auction_buy_metal='".$_POST['auction_new_buy_metal']."',
                    auction_buy_crystal='".$_POST['auction_new_buy_crystal']."',
                    auction_buy_plastic='".$_POST['auction_new_buy_plastic']."',
                    auction_buy_fuel='".$_POST['auction_new_buy_fuel']."',
                    auction_buy_food='".$_POST['auction_new_buy_food']."',
                    auction_buyable='0',
                    auction_delete_date='".$delete_date."'
                WHERE
                    auction_market_id=".$_POST['auction_market_id']."");

                //Log schreiben, falls dieser Handel regelwidrig ist
                $multi_res1=dbquery("
                SELECT
                    user_multi_multi_user_id
                FROM
                    user_multi
                WHERE
                    user_multi_user_id='".$arr['auction_user_id']."'
                    AND user_multi_multi_user_id='".$cu->id."';");

                $multi_res2=dbquery("
                SELECT
                    user_multi_multi_user_id
                FROM
                    user_multi
                WHERE
                    user_multi_user_id='".$cu->id."'
                    AND user_multi_multi_user_id='".$arr['auction_user_id']."';");

                if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
                {
			            add_log(10,"[URL=?page=user&sub=edit&user_id=".$cu->id."][B]".$cu->nick."[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=".$arr['auction_user_id']."][B]".$partner_user_nick."[/B][/URL] gewonnen:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."",time());
                }

                // Log schreiben
                add_log(7,"Es wurde folgende Auktion erfolgreich beendet: Der Spieler ".$cu->nick." hat vom Spieler ".$partner_user_nick."  folgende Waren ersteigert:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."\n\nDie Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht",time());


                echo "Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag geboten hast!<br/>";

                // Berechnet Faktor für verkaufte Rohstoffe
								// Faktor = Kaufzeit - Verkaufzeit (in ganzen Tagen, mit einem Max. von 7)
								// Total = Mengen / Faktor
								$factor = min( ceil( (time() - $arr['auction_start']) / 3600 / 24 ) ,7);

								// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
								// Titan
								dbquery("
								UPDATE
									config
								SET
									config_value=config_value+".(round($_POST['auction_new_buy_metal']/$factor)).",
									config_param1=config_param1+".(round($arr['auction_sell_metal']/$factor))."
								WHERE
									config_name='market_metal_logger'");

								// Silizium
								dbquery("
								UPDATE
									config
								SET
									config_value=config_value+".(round($_POST['auction_new_buy_crystal']/$factor)).",
									config_param1=config_param1+".(round($arr['auction_sell_crystal']/$factor))."
								WHERE
									config_name='market_crystal_logger'");

								// PVC
								dbquery("
								UPDATE
									config
								SET
									config_value=config_value+".(round($_POST['auction_new_buy_plastic']/$factor)).",
									config_param1=config_param1+".(round($arr['auction_sell_plastic']/$factor))."
								WHERE
									config_name='market_plastic_logger'");

								// Tritium
								dbquery("
								UPDATE
									config
								SET
									config_value=config_value+".(round($_POST['auction_new_buy_fuel']/$factor)).",
									config_param1=config_param1+".(round($arr['auction_sell_fuel']/$factor))."
								WHERE
									config_name='market_fuel_logger'");

								// Food
								dbquery("
								UPDATE
									config
								SET
									config_value=config_value+".(round($_POST['auction_new_buy_food']/$factor)).",
									config_param1=config_param1+".(round($arr['auction_sell_food']/$factor))."
								WHERE
									config_name='market_food_logger'");
            }
            else
            {

                if($arr['auction_current_buyer_id']!=0)
                {
                    // TODO: use planet class
										// Rohstoffe dem überbotenen User wieder zurückgeben
                    dbquery("
                    UPDATE
                        planets
                    SET
                        planet_res_metal=planet_res_metal+".$arr['auction_buy_metal'].",
                        planet_res_crystal=planet_res_crystal+".$arr['auction_buy_crystal'].",
                        planet_res_plastic=planet_res_plastic+".$arr['auction_buy_plastic'].",
                        planet_res_fuel=planet_res_fuel+".$arr['auction_buy_fuel'].",
                        planet_res_food=planet_res_food+".$arr['auction_buy_food']."
                    WHERE
                        id=".$arr['auction_current_buyer_planet_id']."
                        AND planet_user_id=".$arr['auction_current_buyer_id']."");

                    // Nachricht dem überbotenen user schicken
                    $msg = "Du wurdest vom Spieler ".$cu->nick." in einer Auktion &uuml;berboten\n";
                    $msg .= "Die Auktion dauert noch bis am ".date("d.m.Y H:i",$arr['auction_end']).".\n";
                    $msg .= "[URL=?page=market&mode=auction&id=".$_POST['auction_market_id']."]Hier[/URL] gehts zu der Auktion.\n\n";

                    $msg .= "Das Handelsministerium";
                    send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
                }


               	// TODO: use planet class
                // Rohstoffe vom neuen Bieter abziehen
                dbquery("
                UPDATE
                   planets
                SET
                    planet_res_metal=planet_res_metal-'".$_POST['auction_new_buy_metal']."',
                    planet_res_crystal=planet_res_crystal-'".$_POST['auction_new_buy_crystal']."',
                    planet_res_plastic=planet_res_plastic-'".$_POST['auction_new_buy_plastic']."',
                    planet_res_fuel=planet_res_fuel-'".$_POST['auction_new_buy_fuel']."',
                    planet_res_food=planet_res_food-'".$_POST['auction_new_buy_food']."'
                WHERE
                    id='".$cp->id()."'
                    AND planet_user_id='".$cu->id."'");

                //Das neue Angebot Speichern
                dbquery("
                UPDATE
                  market_auction
                SET
                  auction_current_buyer_id='".$cu->id."',
                  auction_current_buyer_planet_id='".$cp->id()."',
                  auction_current_buyer_date='".time()."',
                  auction_buy_metal='".$_POST['auction_new_buy_metal']."',
                  auction_buy_crystal='".$_POST['auction_new_buy_crystal']."',
                  auction_buy_plastic='".$_POST['auction_new_buy_plastic']."',
                  auction_buy_fuel='".$_POST['auction_new_buy_fuel']."',
                  auction_buy_food='".$_POST['auction_new_buy_food']."'
                WHERE
                	auction_market_id='".$_POST['auction_market_id']."';");

                echo "Gebot erfolgeich abgegeben!<br/>";
            }
					}
					else
					{
						echo "Das Gebot muss höher sein als vom Höchstbietenden!<br/>";
					}
				}
				else
				{
					echo "Die gebotenen Rohstoffe sind nicht mehr verfügbar!<br/>";
				}
			}
			else
			{
				"Die Auktion ist nicht mehr vorhanden oder bereits abgelaufen!<br/>";
			}
?>
