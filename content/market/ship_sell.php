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

			foreach ($_POST['ship_market_id'] as $num => $id)
			{
				// Lädt Angebotsdaten
				$res = dbquery("
				SELECT
					*
				FROM
					market_ship
				WHERE
					ship_market_id='".$id."'
					AND ship_buyable='1';");
				// Prüft, ob Angebot noch vorhanden ist
				if (mysql_num_rows($res)!=0)
				{
					$arr = mysql_fetch_array($res);

					// Prüft, ob genug Rohstoffe vorhanden sind
					if ($cp->resMetal >= $arr['ship_costs_metal']
					&& $cp->resCrystal >= $arr['ship_costs_crystal']
					&& $cp->resPlastic >= $arr['ship_costs_plastic']
					&& $cp->resFuel >= $arr['ship_costs_fuel']
					&& $cp->resFood >= $arr['ship_costs_food'])
					{
						$seller_user_nick = get_user_nick($arr['user_id']);

						//Angebot reservieren (wird zu einem späteren Zeitpunkt verschickt)
						dbquery("
						UPDATE
							market_ship
						SET
							ship_buyable='0',
							ship_buyer_id='".$cu->id."',
							ship_buyer_planet_id='".$cp->id()."',
							ship_buyer_cell_id='".$cp->cellId()."'
						WHERE
							ship_market_id='".$id."'");

						// Rohstoffe vom Käuferplanet abziehen und $c-variabeln anpassen
						$cp->changeRes(-$arr['ship_costs_metal'],-$arr['ship_costs_crystal'],-$arr['ship_costs_plastic'],-$arr['ship_costs_fuel'],-$arr['ship_costs_food']);

						// Nachricht an Verkäufer
						$msg = "Der Handel war erfolgreich: Der User ".$cu->nick." hat folgende Schiffe von dir gekauft:\n\n";

						$msg .= "".$arr['ship_name'].": ".$arr['ship_count']."\n\n";

						$msg .= "Dies hat dich um folgende Rohstoffe reicher gemacht:\n\n";

						$msg .= "".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n";
						$msg .= "".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n";
						$msg .= "".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n";
						$msg .= "".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n";
						$msg .= "".RES_FOOD.": ".nf($arr['ship_costs_food'])."\n";

						$msg .= "Die Rohstoffe werden in wenigen Minuten versendet.\n\n";

						$msg .= "Das Handelsministerium";
						send_msg($arr['user_id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);


						// Nachricht an Käufer
						$msg = "Ein Handel wurde erfolgreich vollzogen. Du hast vom Spieler ".$seller_user_nick." folgende Schiffe gekauft:\n\n";

						$msg .= "".$arr['ship_name'].": ".$arr['ship_count']."\n\n";

						$msg .= "Dies hat dich folgende Rohstoffe gekostet:\n\n";

						$msg .= "".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n";
						$msg .= "".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n";
						$msg .= "".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n";
						$msg .= "".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n";
						$msg .= "".RES_FOOD.": ".nf($arr['ship_costs_food'])."\n\n";

						$msg .= "Die Waren werden in wenigen Minuten versendet.\n\n";

						$msg .= "Das Handelsministerium";
						send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);


						//Log schreiben, falls dieser Handel regelwidrig ist
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
					    add_log(10,"[URL=?page=user&sub=edit&user_id=".$cu->id."][B]".$cu->nick."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$seller_user_nick."[/B][/URL] Schiffe gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());
						}

						//Marktlog schreiben
						add_log(7,"Der Spieler ".$cu->nick." hat folgende Schiffe von ".$seller_user_nick." gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());

						// Zählt die erfolgreich abgewickelten Angebote
						$cnt++;
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
				ok_msg("".$cnt." Angebot(e) erfolgreich gekauft!");
			}
			if($cnt_error > 0)
			{
				error_msg("".$cnt_error." Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!");
			}		

?>
