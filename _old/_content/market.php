<?PHP

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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: market.php
	// 	Topic: Marktplatz
	// 	Autor: Stephan Vock (Glaubinix)
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud (MrCage)
	// 	Bearbeitet am: 04.06.2006
	// 	Kommentar:
	//

	// DEFINITIONEN //

	define("TBL_SPACING",$conf['general_table_offset']['v']);
	define("TBL_PADDING",$conf['general_table_offset']['p1']);

	//Der Rest aller Definitionen befindet sich in der main.php!

	// BEGIN SKRIPT //

	echo "<h1>Marktplatz des Planeten ".$c->name."</h1>";

	$c->resBox();
	$pr = get_ress_on_planet($c->id);


	//Überprüfung ob der Marktplatz schon gebaut wurde
	$mres=dbquery("SELECT buildlist_current_level FROM ".$db_table['buildlist']." WHERE buildlist_current_level>0 AND buildlist_building_id='".MARKTPLATZ_ID."' AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND buildlist_planet_id='".$c->id."'");
	if (mysql_num_rows($mres)>0)
	{
        $mrow=mysql_fetch_array($mres);
        define(MARKET_LEVEL,$mrow['buildlist_current_level']);
        $return_factor = 1 - (1/(MARKET_LEVEL+1));



		// Navigation
		if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
			$ress_link="| <a href=\"?page=$page&mode=ressource\">Rohstoffe</a>";
		else
			$ress_link="";

		if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
			$ship_link="| <a href=\"?page=$page&mode=ships\">Schiffe</a>";
		else
			$ship_link="";

		if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
			$auction_link="| <a href=\"?page=$page&mode=auction\">Auktionen</a>";
		else
			$auction_link="";

		echo "[ <a href=\"?page=$page&mode=user_home\">Angebote aufgeben</a> | <a href=\"?page=$page&mode=user_sell\">Eigene Angebote</a> $ress_link $ship_link $auction_link ]<br/><br/>";

		//
        // Alle Abgelaufenen Auktionen löschen und ev. waren versenden
        //
		market_auction_update();

		//
		// Wenn ein Schiff gekauft wird
		//
		if ($_POST['ship_submit']!="" && checker_verify())
		{
			$res=dbquery("SELECT * FROM ".$db_table['market_ship']." WHERE ship_market_id='".$_POST['ship_market_id']."' AND ship_buyable='1';");
			if (mysql_num_rows($res)!=0)
			{
				$arr=mysql_fetch_array($res);
				if ($pr['metal']>=$arr['ship_costs_metal'] && $pr['crystal']>=$arr['ship_costs_crystal'] && $pr['plastic']>=$arr['ship_costs_plastic']  && $pr['fuel']>=$arr['ship_costs_fuel']  && $pr['food']>=$arr['ship_costs_food'] && $_SESSION[ROUNDID]['user']['id']!=$arr['user_id'])
				{
					$partner_user_nick=get_user_nick($arr['user_id']);

					//Angebot reservieren (wird zu einem späteren Zeitpunk verschickt)
					dbquery("
					UPDATE
						".$db_table['market_ship']."
					SET
						ship_buyable='0',
						ship_buyer_id=".$_SESSION[ROUNDID]['user']['id'].",
						ship_buyer_planet_id=".$c->id.",
						ship_buyer_cell_id=".$c->solsys_id."
					WHERE
						ship_market_id='".$arr['ship_market_id']."'");

					//Ress vom Käuferplanet abziehen
					dbquery("
					UPDATE
						".$db_table['planets']."
					SET
                        planet_res_metal=planet_res_metal-'".$arr['ship_costs_metal']."',
                        planet_res_crystal=planet_res_crystal-".$arr['ship_costs_crystal'].",
                        planet_res_plastic=planet_res_plastic-".$arr['ship_costs_plastic'].",
                        planet_res_fuel=planet_res_fuel-".$arr['ship_costs_fuel'].",
                        planet_res_food=planet_res_food-".$arr['ship_costs_food']."
					 WHERE
					 	planet_id='".$c->id."'
					 	AND planet_user_id='".$_SESSION[ROUNDID]['user']['id']."'");

					// Nachricht an Verkäufer
					$msg="Dein Handel war erfolgreich: Der User ".$_SESSION[ROUNDID]['user']['nick']." hat folgende Schiffe von dir gekauft:\n\n";
					$msg.=$arr['ship_name'].": ".$arr['ship_count']."\n\n";
					$msg.="Dies hat dich um folgende Rohstoffe reicher gemacht:\n\n";
					$msg.=RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food'])."\n\n";
					$msg.="Die Rohstoffe werden zur n&auml;chsten vollen Stunde verschickt.\nDas Handelsministerium";
					send_msg($arr['user_id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);

					// Nachricht an Käufer
					$msg="Dein Handel wurde erfolgreich vollzogen. Du hast vom Spieler ".$partner_user_nick."  folgende Schiffe gekauft:\n\n";
					$msg.=$arr['ship_name'].": ".$arr['ship_count']."\n\n";
					$msg.="Dies hat dich folgende Rohstoffe gekostet:\n\n";
					$msg.=RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food'])."\n\n";
					$msg.="Die Waren werden zur n&auml;chsten vollen Stunde verschickt.\nDas Handelsministerium";
					send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);


					//Log schreiben, falls dieser Handel regelwidrig ist
					$multi_res1=dbquery("
					SELECT
						user_multi_multi_user_id
					FROM
						".$db_table['user_multi']."
					WHERE
						user_multi_user_id='".$_SESSION[ROUNDID]['user']['id']."'
						AND user_multi_multi_user_id='".$arr['user_id']."';");

					$multi_res2=dbquery("
					SELECT
						user_multi_multi_user_id
					FROM
						".$db_table['user_multi']."
					WHERE
						user_multi_user_id='".$arr['user_id']."'
						AND user_multi_multi_user_id='".$_SESSION[ROUNDID]['user']['id']."';");

					if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
					{
			    add_log(10,"[URL=?page=user&sub=edit&user_id=".$_SESSION[ROUNDID]['user']['id']."][B]".$_SESSION[ROUNDID]['user']['nick']."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$partner_user_nick."[/B][/URL] Schiffe gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());
					}

					//Marktlog schreiben
					add_log(7,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat folgende Schiffe von ".$partner_user_nick." gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());

					echo "Aktion wurde erfolgreich ausgeführt!";
				}
				else
				{
					echo "Zu wenig Rohstoffe vorhanden!";
				}
			}
			else
			{
				echo "Angebot nicht mehr vorhanden!";
			}
		}

		//
		//Wenn Rohstoffe gekauft wurden
		//
		elseif ($_POST['ressource_submit']!="" && checker_verify())
		{
			$res=dbquery("SELECT * FROM ".$db_table['market_ressource']." WHERE ressource_market_id='".$_POST['ressource_market_id']."' AND ressource_buyable='1';");
			if (mysql_num_rows($res)!=0)
			{
				$arr=mysql_fetch_array($res);
				if ($pr['metal']>=$arr['buy_metal'] && $pr['crystal']>=$arr['buy_crystal']  && $pr['plastic']>=$arr['buy_plastic']  && $pr['fuel']>=$arr['buy_fuel']  && $pr['food']>=$arr['buy_food'] && $_SESSION[ROUNDID]['user']['id']!=$arr['user_id'])
				{

					$partner_user_nick=get_user_nick($arr['user_id']);

					//Angebot reservieren (wird zu einem späteren Zeitpunk verschickt)
					dbquery("
					UPDATE
						".$db_table['market_ressource']."
					SET
						ressource_buyable='0',
						ressource_buyer_id=".$_SESSION[ROUNDID]['user']['id'].",
						ressource_buyer_planet_id=".$c->id.",
						ressource_buyer_cell_id=".$c->solsys_id."
					WHERE
						ressource_market_id='".$arr['ressource_market_id']."'");

					//Ress vom Käuferplanet abziehen
					dbquery("
					UPDATE
						".$db_table['planets']."
					SET
                        planet_res_metal=planet_res_metal-".$arr['buy_metal'].",
                        planet_res_crystal=planet_res_crystal-".$arr['buy_crystal'].",
                        planet_res_plastic=planet_res_plastic-".$arr['buy_plastic'].",
                        planet_res_fuel=planet_res_fuel-".$arr['buy_fuel'].",
                        planet_res_food=planet_res_food-".$arr['buy_food']."
					WHERE
                        planet_id=".$c->id."
                        AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");


					// Nachricht an Verkäufer
					$msg="Ein Handel ist zustande gekommen\nDer Spieler ".$_SESSION[ROUNDID]['user']['nick']."  hat von dir folgende Rohstoffe gekauft:\n\n";
					$msg.=RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\n";
					$msg.="Dies macht dich um folgende Rohstoffe reicher:\n\n";
					$msg.=RES_METAL.": ".nf(round($arr['buy_metal']))."\n".RES_CRYSTAL.": ".nf(round($arr['buy_crystal']))."\n".RES_PLASTIC.": ".nf(round($arr['buy_plastic']))."\n".RES_FUEL.": ".nf(round($arr['buy_fuel']))."\n".RES_FOOD.": ".nf(round($arr['buy_food']))."\n\n";
					$msg.="Die Rohstoffe werden zur n&auml;chsten vollen Stunde verschickt.\n\nDas Handelsministerium";
					send_msg($arr['user_id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);

					// Nachricht an Käufer
					$msg="Ein Handel ist zustande gekommen\nDu hast vom Spieler ".$partner_user_nick."  folgende Rohstoffe gekauft:\n\n";
					$msg.=RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\n";
					$msg.="Dies hat dich folgende Rohstoffe gekostet:\n\n";
					$msg.=RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food'])."\n\n";
					$msg.="Die Rohstoffe werden zur n&auml;chsten vollen Stunde verschickt.\n\nDas Handelsministerium";
					send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);

					//Log schreiben, falls dieser Handel regelwidrig ist
					$multi_res1=dbquery("
					SELECT
						user_multi_multi_user_id
					FROM
						".$db_table['user_multi']."
					WHERE
						user_multi_user_id='".$_SESSION[ROUNDID]['user']['id']."'
						AND user_multi_multi_user_id='".$arr['user_id']."';");

					$multi_res2=dbquery("
					SELECT
						user_multi_multi_user_id
					FROM
						".$db_table['user_multi']."
					WHERE
						user_multi_user_id='".$arr['user_id']."'
						AND user_multi_multi_user_id='".$_SESSION[ROUNDID]['user']['id']."';");

					if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
					{
			    add_log(10,"[URL=?page=user&sub=edit&user_id=".$_SESSION[ROUNDID]['user']['id']."][B]".$_SESSION[ROUNDID]['user']['nick']."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$partner_user_nick."[/B][/URL] Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food']),time());
					}

					// Log schreiben
					add_log(7,"Ein Handel ist zustande gekommen\nDer Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat vom Spieler ".$partner_user_nick."  folgende Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food'])."\n\n",time());

					echo "Die Aktion wurde erfolgreich ausgeführt!";
				}
				else
				{
					echo "Zu wenig Rohstoffe vorhanden!";
				}
			}
			else
			{
				echo "Angebot nicht mehr vorhanden!";
			}
		}

		//
		// Wenn bei einer Auktion geboten wird
		//
		elseif ($_POST['auction_submit']!="" && checker_verify())
		{
			$id=$_POST['auction_market_id'];
			if($_POST['auction_buy_metal'][$id]=="") $_POST['auction_buy_metal']=0;
			if($_POST['auction_buy_crystal'][$id]=="") $_POST['auction_buy_crystal']=0;
			if($_POST['auction_buy_plastic'][$id]=="") $_POST['auction_buy_plastic']=0;
			if($_POST['auction_buy_fuel'][$id]=="") $_POST['auction_buy_fuel']=0;
			if($_POST['auction_buy_food'][$id]=="") $_POST['auction_buy_food']=0;


			$_POST['auction_buy_metal'] = abs(floor(deltick($_POST['auction_buy_metal'][$id])));
			$_POST['auction_buy_crystal'] = abs(floor(deltick($_POST['auction_buy_crystal'][$id])));
			$_POST['auction_buy_plastic'] = abs(floor(deltick($_POST['auction_buy_plastic'][$id])));
			$_POST['auction_buy_fuel'] = abs(floor(deltick($_POST['auction_buy_fuel'][$id])));
			$_POST['auction_buy_food'] = abs(floor(deltick($_POST['auction_buy_food'][$id])));

			$auction_buy_total = $_POST['auction_buy_metal'] + $_POST['auction_buy_crystal'] + $_POST['auction_buy_plastic'] + $_POST['auction_buy_fuel'] + $_POST['auction_buy_food'];


			$res=dbquery("
			SELECT
				*
			FROM
				".$db_table['market_auction']."
			WHERE
                auction_market_id='".$_POST['auction_market_id']."'
                AND auction_end>".time()."
                AND auction_buyable='1'");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				if ($pr['metal']>=$_POST['auction_buy_metal'] && $pr['crystal']>=$_POST['auction_buy_crystal'] && $pr['plastic']>=$_POST['auction_buy_plastic'] && $pr['fuel']>=$_POST['auction_buy_fuel']  && $pr['food']>=$_POST['auction_buy_food'] && $_SESSION[ROUNDID]['user']['id']!=$arr['auction_user_id'])
				{


                    //Den Wert des Angebotes errechnen...
                    $ship_cost_total = 0;
                    $sell_ress_total = $arr['auction_sell_metal'] + $arr['auction_sell_crystal'] + $arr['auction_sell_plastic'] + $arr['auction_sell_fuel'] + $arr['auction_sell_food'];

                    //wenn schiffe angeboten werden, hier den wert errechnen
                    if($arr['auction_ship_id']>0)
                    {
                        $res_cost=dbquery("
                        SELECT
                            ship_costs_metal,
                            ship_costs_crystal,
                            ship_costs_plastic,
                            ship_costs_fuel,
                            ship_costs_food
                        FROM
                            ".$db_table['ships']."
                        WHERE
                            ship_id='".$arr['auction_ship_id']."'");

                        $arr_cost=mysql_fetch_array($res_cost);
                        $ship_cost_total = ($arr_cost['ship_costs_metal'] + $arr_cost['ship_costs_crystal'] + $arr_cost['ship_costs_plastic'] + $arr_cost['ship_costs_fuel'] + $arr_cost['ship_costs_food'])*$arr['auction_ship_count'];

                    }

                    $sell_price = $sell_ress_total + $ship_cost_total;

                    $current_buyer_ress_total = $arr['auction_buy_metal'] + $arr['auction_buy_crystal'] + $arr['auction_buy_plastic'] + $arr['auction_buy_fuel'] + $arr['auction_buy_food'];


                    // Überprüfung ob das Verhältnis der verkauften/erkauften ress nicht zu grosse unterschiede hat!
                    if(AUCTION_PRICE_FACTOR_MIN <= ($auction_buy_total/$sell_price) || $arr['auction_current_buyer_id']!=0)
                    {

                        // überprüft, ob die gebotenen ress grösser sind als diese vom höchstbietenden
                        if($auction_buy_total>$current_buyer_ress_total)
                        {

                            $partner_user_nick=get_user_nick($arr['auction_user_id']);

                            // wenn der bietende das höchst mögliche (oder mehr) bietet...
                            if(AUCTION_PRICE_FACTOR_MAX <= ($auction_buy_total/$sell_price))
                            {
                                if($arr['auction_current_buyer_id']!=0)
                                {
                                    //Ress dem überbotenen user wieder zurückgeben
                                    dbquery("
                                    UPDATE
                                        ".$db_table['planets']."
                                    SET
                                        planet_res_metal=planet_res_metal+".$arr['auction_buy_metal'].",
                                        planet_res_crystal=planet_res_crystal+".$arr['auction_buy_crystal'].",
                                        planet_res_plastic=planet_res_plastic+".$arr['auction_buy_plastic'].",
                                        planet_res_fuel=planet_res_fuel+".$arr['auction_buy_fuel'].",
                                        planet_res_food=planet_res_food+".$arr['auction_buy_food']."
                                    WHERE
                                        planet_id=".$arr['auction_current_buyer_planet_id']."
                                        AND planet_user_id=".$arr['auction_current_buyer_id']."");

                                    // Nachricht dem überbotenen user schicken
                                    $msg="Du wurdest vom Spieler ".$_SESSION[ROUNDID]['user']['nick']." in einer Auktion &uuml;berboten\n";
                                    $msg.="Die Auktion ist nun zu Ende und wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht.\n";
                                    $msg.="[URL=?page=market&mode=auction]Hier[/URL] gehts zu den Auktionen.\n\n";
                                    $msg.="Das Handelsministerium";
                                    send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
                                }

                                // ress überschuss anpassen
                                $factor = ($sell_price * AUCTION_PRICE_FACTOR_MAX) / $auction_buy_total;
                                $_POST['auction_buy_metal'] = round($_POST['auction_buy_metal'] * $factor);
                                $_POST['auction_buy_crystal'] = round($_POST['auction_buy_crystal'] * $factor);
                                $_POST['auction_buy_plastic'] = round($_POST['auction_buy_plastic'] * $factor);
                                $_POST['auction_buy_fuel'] = round($_POST['auction_buy_fuel'] * $factor);
                                $_POST['auction_buy_food'] = round($_POST['auction_buy_food'] * $factor);

                                //Angepasste Ress vom gewinner planet konto abziehen...
                                dbquery("
                                UPDATE
                                    ".$db_table['planets']."
                                SET
                                    planet_res_metal=planet_res_metal-".$_POST['auction_buy_metal'].",
                                    planet_res_crystal=planet_res_crystal-".$_POST['auction_buy_crystal'].",
                                    planet_res_plastic=planet_res_plastic-".$_POST['auction_buy_plastic'].",
                                    planet_res_fuel=planet_res_fuel-".$_POST['auction_buy_fuel'].",
                                    planet_res_food=planet_res_food-".$_POST['auction_buy_food']."
                                WHERE
                                    planet_id=".$c->id."
                                    AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");


                                // Nachricht schicken
                                if($arr['auction_ship_id']!=0)
                                {
                                    // Nachricht an Verkäufer
                                    $msg="Ein Handel wurde erfolgreich abgeschlossen.\nDer Spieler ".$_SESSION[ROUNDID]['user']['nick']."  hat von dir folgende Waren vorzeitig ersteigert, da er das Maximalgebot erreicht- oder &uuml;berboten hat:\n\n";
                                    $msg.="Schiffe:\n";
                                    $msg.="".nf($arr['auction_ship_count'])." ".$arr['auction_ship_name']."\n\n";
                                    $msg.="Rohstoffe:\n";
                                    $msg.=RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
                                    $msg.="Dies macht dich um folgende Rohstoffe reicher:\n";
                                    $msg.=RES_METAL.": ".nf($_POST['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_buy_food'])."\n\n";
                                    $msg.="Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden zur n&auml;chsten vollen Stunde verschickt!\n\n";
                                    $msg.="Das Handelsministerium";
                                    send_msg($arr['auction_user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

                                    // Nachricht an Käufer
                                    $msg="Du warst der h&ouml;chstbietende in der Auktion vom Spieler ".$partner_user_nick.". Du hast folgende Waren ersteigert:\n\n";
                                    $msg.="Schiffe\n";
                                    $msg.="".nf($arr['auction_ship_count'])." ".$arr['auction_ship_name']."\n\n";
                                    $msg.="Rohstoffe\n";
                                    $msg.=RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
                                    $msg.="Dies hat dich folgende Rohstoffe gekostet:\n";
                                    $msg.=RES_METAL.": ".nf($_POST['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_buy_food'])."\n\n";
                                    $msg.="Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden zur n&auml;chsten vollen Stunde verschickt!\n\n";
                                    $msg.="Das Handelsministerium";
                                    send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

                                }
                                // Schickt nur gekaufte ress mit handelsschiff
                                else
                                {
                                    // Nachricht an Verkäufer
                                    $msg="Ein Handel ist erfolgreich zustande gekommen.\nDer Spieler ".$_SESSION[ROUNDID]['user']['nick']."  hat von dir folgende Rohstoffe ersteigert:\n\n";
                                    $msg.=RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".$arr['auction_sell_fuel']."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
                                    $msg.="Dies macht dich um folgende Rohstoffe reicher:\n";
                                    $msg.=RES_METAL.": ".nf($_POST['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_buy_food'])."\n\n";
                                    $msg.="Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden zur n&auml;chsten vollen Stunde verschickt!\n\n";
                                    $msg.="Das Handelsministerium";
                                    send_msg($arr['auction_user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

                                    // Nachricht an Käufer
                                    $msg="Du warst der h&ouml;chstbietende in der Auktion vom Spieler ".$partner_user_nick.". Du hast folgende Rohstoffe ersteigert:\n\n";
                                    $msg.=RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
                                    $msg.="Dies hat dich folgende Rohstoffe gekostet:\n";
                                    $msg.=RES_METAL.": ".nf($_POST['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_buy_food'])."\n\n";
                                    $msg.="Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht und die Waren werden zur n&auml;chsten vollen Stunde verschickt!\n\n";
                                    $msg.="Das Handelsministerium";
                                    send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);

                                }


                                // Auktion Speichern und "Stoppen" so dass nicht mehr geboten werden kann
                                $delete_date=time()+(AUCTION_DELAY_TIME*3600);
                                dbquery("
                                UPDATE
                                    ".$db_table['market_auction']."
                                SET
                                    auction_current_buyer_id=".$_SESSION[ROUNDID]['user']['id'].",
                                    auction_current_buyer_planet_id=".$c->id.",
                                    auction_current_buyer_cell_id=".$c->solsys_id.",
                                    auction_current_buyer_date=".time().",
                                    auction_buy_metal=".$_POST['auction_buy_metal'].",
                                    auction_buy_crystal=".$_POST['auction_buy_crystal'].",
                                    auction_buy_plastic=".$_POST['auction_buy_plastic'].",
                                    auction_buy_fuel=".$_POST['auction_buy_fuel'].",
                                    auction_buy_food=".$_POST['auction_buy_food'].",
                                    auction_buyable='0',
                                    auction_delete_date='".$delete_date."'
                                WHERE
                                    auction_market_id=".$_POST['auction_market_id']."");

                                //Log schreiben, falls dieser Handel regelwidrig ist
                                $multi_res1=dbquery("
                                SELECT
                                    user_multi_multi_user_id
                                FROM
                                    ".$db_table['user_multi']."
                                WHERE
                                    user_multi_user_id='".$arr['auction_user_id']."'
                                    AND user_multi_multi_user_id='".$_SESSION[ROUNDID]['user']['id']."';");

                                $multi_res2=dbquery("
                                SELECT
                                    user_multi_multi_user_id
                                FROM
                                    ".$db_table['user_multi']."
                                WHERE
                                    user_multi_user_id='".$_SESSION[ROUNDID]['user']['id']."'
                                    AND user_multi_multi_user_id='".$arr['auction_user_id']."';");

                                if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
                                {
                            add_log(10,"[URL=?page=user&sub=edit&user_id=".$_SESSION[ROUNDID]['user']['id']."][B]".$_SESSION[ROUNDID]['user']['nick']."[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=".$arr['auction_user_id']."][B]".$partner_user_nick."[/B][/URL] gewonnen:\n\nSchiffe:\n".nf($arr['auction_ship_count'])." ".$arr['auction_ship_name']."\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($_POST['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_buy_food'])."",time());
                                }

                                // Log schreiben
                                add_log(7,"Es wurde folgende Auktion erfolgreich beendet: Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat vom Spieler ".$partner_user_nick."  folgende Waren ersteigert:\n\nSchiffe:\n".nf($arr['auction_ship_count'])." ".$arr['auction_ship_name']."\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:".RES_METAL.": ".nf($_POST['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_buy_food'])."\n\nDie Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht",time());


                                echo "Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag &uuml;berboten hast!";
                            }
                            else
                            {

                                if($arr['auction_current_buyer_id']!=0)
                                {
                                    //Ress dem überbotenen user wieder zurückgeben
                                    dbquery("
                                    UPDATE
                                        ".$db_table['planets']."
                                    SET
                                        planet_res_metal=planet_res_metal+".$arr['auction_buy_metal'].",
                                        planet_res_crystal=planet_res_crystal+".$arr['auction_buy_crystal'].",
                                        planet_res_plastic=planet_res_plastic+".$arr['auction_buy_plastic'].",
                                        planet_res_fuel=planet_res_fuel+".$arr['auction_buy_fuel'].",
                                        planet_res_food=planet_res_food+".$arr['auction_buy_food']."
                                    WHERE
                                        planet_id=".$arr['auction_current_buyer_planet_id']."
                                        AND planet_user_id=".$arr['auction_current_buyer_id']."");

                                    // Nachricht dem überbotenen user schicken
                                    $msg="Du wurdest vom Spieler ".$_SESSION[ROUNDID]['user']['nick']." in einer Auktion &uuml;berboten\n";
                                    $msg.="Die Auktion dauert noch bis am ".date("d.m.Y H:i",$arr['auction_end']).".\n";
                                    $msg.="[URL=?page=market&mode=auction]Hier[/URL] gehts zu den Auktionen.\n\n";
                                    $msg.="Das Handelsministerium";
                                    send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
                                }

                                //Ress vom neuen bieter abziehen
                                dbquery("
                                UPDATE
                                    ".$db_table['planets']."
                                SET
                                    planet_res_metal=planet_res_metal-".$_POST['auction_buy_metal'].",
                                    planet_res_crystal=planet_res_crystal-".$_POST['auction_buy_crystal'].",
                                    planet_res_plastic=planet_res_plastic-".$_POST['auction_buy_plastic'].",
                                    planet_res_fuel=planet_res_fuel-".$_POST['auction_buy_fuel'].",
                                    planet_res_food=planet_res_food-".$_POST['auction_buy_food']."
                                WHERE
                                    planet_id=".$c->id."
                                    AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");

                                //Das neue Angebot Speichern
                                dbquery("
                                UPDATE
                                    ".$db_table['market_auction']."
                                SET
                                    auction_current_buyer_id=".$_SESSION[ROUNDID]['user']['id'].",
                                    auction_current_buyer_planet_id =".$c->id.",
                                    auction_current_buyer_date=".time().",
                                    auction_buy_metal=".$_POST['auction_buy_metal'].",
                                    auction_buy_crystal=".$_POST['auction_buy_crystal'].",
                                    auction_buy_plastic=".$_POST['auction_buy_plastic'].",
                                    auction_buy_fuel=".$_POST['auction_buy_fuel'].",
                                    auction_buy_food=".$_POST['auction_buy_food']."
                                WHERE
                                 auction_market_id=".$_POST['auction_market_id']."");

                                echo "Gebot erfolgeich abgegeben!<br>";
                            }


                        }
                        else
                        {
                            echo "Du musst mehr als ".nf(ceil($current_buyer_ress_total))." t Rohstoffe bieten";
                        }
                    }
                    else
                    {
                        echo "Das Startgebot liegt bei ".nf(ceil(($sell_price*AUCTION_PRICE_FACTOR_MIN)))." t Rohstoffen<br/>";
                    }


				}
				else
				{
					echo "Du hast nicht so viele Rohstoffe wie du bieten wolltest!";
				}
			}
			else
			{
				"Die Auktion ist nicht mehr vorhanden oder bereits abgelaufen!";
			}
		}

		//
		// Rohstoffe-Verkauft Speichern
		//
		elseif ($_POST['ressource_sell_submit']!="" && checker_verify())
		{
			$_POST['sell_metal']=abs(floor(deltick($_POST['sell_metal'])));
			$_POST['sell_crystal']=abs(floor(deltick($_POST['sell_crystal'])));
			$_POST['sell_plastic']=abs(floor(deltick($_POST['sell_plastic'])));
			$_POST['sell_fuel']=abs(floor(deltick($_POST['sell_fuel'])));
			$_POST['sell_food']=abs(floor(deltick($_POST['sell_food'])));
			$_POST['buy_metal']=abs(deltick($_POST['buy_metal']));
			$_POST['buy_crystal']=abs(deltick($_POST['buy_crystal']));
			$_POST['buy_plastic']=abs(deltick($_POST['buy_plastic']));
			$_POST['buy_fuel']=abs(deltick($_POST['buy_fuel']));
			$_POST['buy_food']=abs(deltick($_POST['buy_food']));

			if($_POST['ressource_for_alliance']=="")
				$_POST['ressource_for_alliance']=0;
			else
				$_POST['ressource_for_alliance']=$_SESSION[ROUNDID]['user']['alliance_id'];

			$check_text = check_illegal_signs($_POST['ressource_text']);

			$sell_total = $_POST['sell_metal']+$_POST['sell_crystal']+$_POST['sell_plastic']+$_POST['sell_fuel']+$_POST['sell_food'];
			$buy_total = $_POST['buy_metal']+$_POST['buy_crystal']+$_POST['buy_plastic']+$_POST['buy_fuel']+$_POST['buy_food'];

			//überprüfung ob im beschreibtext unerlaubte zeichen sind
			if($check_text=="")
			{
                //Nicht gleich 0 Überprüfung
                if ($sell_total>0 && $buy_total>0)
                {
                    //überprüft ob das verhältniss der verkauften/erkauften ress nicht zu grosse unterschiede hat!
                    if(RESS_PRICE_FACTOR_MIN<=($buy_total/$sell_total) && ($buy_total/$sell_total)<=RESS_PRICE_FACTOR_MAX)
                    {
                        //überprüft ob genug ress vorhanden ist
                        if (
                        ($_POST['sell_metal']*MARKET_SELL_TAX)<=$c->res->metal
                        && ($_POST['sell_crystal']*MARKET_SELL_TAX)<=$c->res->crystal
                        && ($_POST['sell_plastic']*MARKET_SELL_TAX)<=$c->res->plastic
                        && ($_POST['sell_fuel']*MARKET_SELL_TAX)<=$c->res->fuel
                        && ($_POST['sell_food']*MARKET_SELL_TAX)<=$c->res->food
                        )
                        {
                            //überprüft ob der user einen anderen wert verlangt als er haben will -> 100 titan gegen 200 titan
                            if(
                            ($_POST['sell_metal']!=0 && $_POST['buy_metal']!=0)
                            || ($_POST['sell_crystal']!=0 && $_POST['buy_crystal']!=0)
                            || ($_POST['sell_plastic']!=0 && $_POST['buy_plastic']!=0)
                            || ($_POST['sell_fuel']!=0 && $_POST['buy_fuel']!=0)
                            || ($_POST['sell_food']!=0 && $_POST['buy_food']!=0)
                            )
                            {
                                echo "Die verlangten Rohstoffe d&uuml;rfen nicht identisch sein mit den zu verkaufenden Rohstoffen!<br/><br>";
                                return_btn();
                            }
                            else
                            {
                            	if($_POST['ressource_for_alliance']!=0)
                            		$for_alliance="f&uuml;r ein Allianzmitglied";
                            	else
                            		$for_alliance="";

                                //Nachricht versenden
                                $msg="Du hast folgende Rohstoffe ".$for_alliance." angeboten:\n\n";
                                $msg.=RES_METAL.": ".nf($_POST['sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['sell_food'])."\n\n";
                                $msg.="Du verlangst folgenden Preis daf&uuml;r:\n\n";
                                $msg.=RES_METAL.": ".nf($_POST['buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['buy_food'])."\n\n";
                                $msg.="Das Handelsministerium";
                                send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Angebot eingetragen",$msg);

                                // Log schreiben
                                add_log(7,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat folge Rohstoffe ".$for_alliance." angeboten:\n\n".RES_METAL.": ".nf($_POST['sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['sell_food'])."\n\nFolgender Preis muss daf&uuml;r gezahlt werden:\n\n".RES_METAL.": ".nf($_POST['buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['buy_food'])."\n\n",time());

                                //Werte in die MySQL schreiben
                                dbquery("
                                UPDATE
                                ".$db_table['planets']."
                                SET
                                    planet_res_metal=planet_res_metal-".($_POST['sell_metal']*MARKET_SELL_TAX).",
                                    planet_res_crystal=planet_res_crystal-".($_POST['sell_crystal']*MARKET_SELL_TAX).",
                                    planet_res_plastic=planet_res_plastic-".($_POST['sell_plastic']*MARKET_SELL_TAX).",
                                    planet_res_fuel=planet_res_fuel-".($_POST['sell_fuel']*MARKET_SELL_TAX).",
                                    planet_res_food=planet_res_food-".($_POST['sell_food']*MARKET_SELL_TAX)."
                                WHERE
                                	planet_id=".$c->id."
                                	AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");

                                dbquery("
                                INSERT INTO
                                ".$db_table['market_ressource']."
                                    (user_id,
                                    planet_id,
                                    cell_id,
                                    sell_metal,
                                    sell_crystal,
                                    sell_plastic,
                                    sell_fuel,
                                    sell_food,
                                    buy_metal,
                                    buy_crystal,
                                    buy_plastic,
                                    buy_fuel,
                                    buy_food,
                                    ressource_for_alliance,
                                    ressource_text,
                                    datum)
                                VALUES
                                    ('".$_SESSION[ROUNDID]['user']['id']."',
                                    '".$c->id."',
                                    '".$c->solsys_id."',
                                    '".$_POST['sell_metal']."',
                                    '".$_POST['sell_crystal']."',
                                    '".$_POST['sell_plastic']."',
                                    '".$_POST['sell_fuel']."',
                                    '".$_POST['sell_food']."',
                                    '".$_POST['buy_metal']."',
                                    '".$_POST['buy_crystal']."',
                                    '".$_POST['buy_plastic']."',
                                    '".$_POST['buy_fuel']."',
                                    '".$_POST['buy_food']."',
                                    '".$_POST['ressource_for_alliance']."',
                                    '".addslashes($_POST['ressource_text'])."',
                                    '".time()."');");
                                echo "Angebot erfolgreich aufgegeben<br/><br/>";
                                return_btn();
                            }
                        }
                        else
                        {
                            echo "Angebot konnte nicht aufgegeben werden, keine entsprechende Ressourcen auf dem Planeten vorhanden!<br/>Achtung, Markttaxe von ".MARKET_SELL_TAX." wird auch berechnet!<br/>";
                            return_btn();
                        }
                    }
                    else
                    {
                        echo "Angebot konnte nicht aufgegeben werden, der Ertrag weisst zu grosse Unterschiede auf!<br/>(kleiner als ".(RESS_PRICE_FACTOR_MIN*100)."% oder gr&ouml;sser als ".(RESS_PRICE_FACTOR_MAX*100)."%)<br/><br/>";
                        return_btn();
                    }
                }
                else
                {
                    echo "Angebot konnte nicht aufgegeben werden, keine Rohstoffe festgelegt!<br/><br/>";
                    return_btn();
                }
            }
            else
            {
                echo "Unerlaubtes Zeichen (".$check_text.") in der Beschreibung!<br/><br/>";
                return_btn();
            }
		}

		//
		// Schiff-Verkauf speichern
		//
		elseif ($_POST['ship_sell_submit']!="" && checker_verify())
		{
			$_POST['sbuy_metal']=abs(deltick($_POST['sbuy_metal']));
			$_POST['sbuy_crystal']=abs(deltick($_POST['sbuy_crystal']));
			$_POST['sbuy_plastic']=abs(deltick($_POST['sbuy_plastic']));
			$_POST['sbuy_fuel']=abs(deltick($_POST['sbuy_fuel']));
			$_POST['sbuy_food']=abs(deltick($_POST['sbuy_food']));
	 		$_POST['count_ship']=abs(ceil(deltick($_POST['count_ship'])));


			if($_POST['ship_for_alliance']=="")
				$_POST['ship_for_alliance']=0;
			else
				$_POST['ship_for_alliance']=$_SESSION[ROUNDID]['user']['alliance_id'];

			$check_text = check_illegal_signs($_POST['ship_text']);

			if($check_text=="")
			{
                // Überprüfung ob Anzahl Schiffe zu verkaufen grösser als null ist
                if ($_POST['count_ship']>0)
                {
                    //Wenn nicht genug Schiff vorhanden sind, wird die anzahl reduziert
                    $sl_res=dbquery("
                    SELECT
                        shiplist_count
                    FROM
                        ".$db_table['shiplist']."
                    WHERE
                        shiplist_planet_id='".$c->id."'
                        AND shiplist_ship_id='".$_POST['ship']." '
                        AND shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."'");
                    $sl_arr=mysql_fetch_array($sl_res);
                    $ship_count=min($sl_arr['shiplist_count'],$_POST['count_ship']);

                	//Passt Rohstoffe proportional an, falls der user weniger schiffe hat, als er angegeben hat
                	if($_POST['count_ship']>$sl_arr['shiplist_count'])
                	{
                		$faktor=ceil($_POST['count_ship']/$sl_arr['shiplist_count']);
                        $_POST['sbuy_metal']=$_POST['sbuy_metal']/$faktor;
                        $_POST['sbuy_crystal']=$_POST['sbuy_crystal']/$faktor;
                        $_POST['sbuy_plastic']=$_POST['sbuy_plastic']/$faktor;
                        $_POST['sbuy_fuel']=$_POST['sbuy_fuel']/$faktor;
                        $_POST['sbuy_food']=$_POST['sbuy_food']/$faktor;
                	}

                    $res_cost=dbquery("
                    SELECT
                        ship_costs_metal,
                        ship_costs_crystal,
                        ship_costs_plastic,
                        ship_costs_fuel,
                        ship_costs_food
                    FROM
                    	".$db_table['ships']."
                    WHERE
                    	ship_id='".$_POST['ship']."'");
                    $arr_cost=mysql_fetch_array($res_cost);

                    $sbuy_total = $_POST['sbuy_metal']+$_POST['sbuy_crystal']+$_POST['sbuy_plastic']+$_POST['sbuy_fuel']+$_POST['sbuy_food'];
                    $ship_cost_total = ($arr_cost['ship_costs_metal'] + $arr_cost['ship_costs_crystal'] + $arr_cost['ship_costs_plastic'] + $arr_cost['ship_costs_fuel'] + $arr_cost['ship_costs_food'])*$ship_count;

                    // Überprüfung ob die Kosten nich gleich 0 sind
                    if ($sbuy_total>0)
                    {

                        // Überprüfung ob das Verhältnis der verkauften/erkauften ress nicht zu grosse unterschiede hat!
                        if(SHIP_PRICE_FACTOR_MIN<=($sbuy_total/$ship_cost_total) && ($sbuy_total/$ship_cost_total)<=SHIP_PRICE_FACTOR_MAX)
                        {
                            //Wenn nicht 0 Schiffe vorhanden sind werden die Schiffe vom Planeten abgezogen
                            if ($ship_count>0)
                            {
                                $res_name=dbquery("
                                SELECT
                                	ship_name
                                FROM
                                	".$db_table['ships']."
                                WHERE
                                	ship_id='".$_POST['ship']."'");
                                $arr_name=mysql_fetch_array($res_name);

                                $zzz=dbquery("
                                UPDATE
                                	".$db_table['shiplist']."
                                SET
                                	shiplist_count=shiplist_count-".$ship_count."
                                WHERE
                                    shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
                                    AND shiplist_planet_id='".$c->id."'
                                    AND shiplist_ship_id='".$_POST['ship']."' ");

                                dbquery("
                                INSERT INTO
                                ".$db_table['market_ship']."
                                    (user_id,
                                    planet_id,
                                    cell_id,
                                    ship_id,
                                    ship_name,
                                    ship_count,
                                    ship_costs_metal,
                                    ship_costs_crystal,
                                    ship_costs_plastic,
                                    ship_costs_fuel,
                                    ship_costs_food,
                                    ship_for_alliance,
                                    ship_text,
                                    datum)
                                VALUES
                                    ('".$_SESSION[ROUNDID]['user']['id']."',
                                    '".$c->id."',
                                    '".$c->solsys_id."',
                                    '".$_POST['ship']."',
                                    '".$arr_name['ship_name']."',
                                    '".$ship_count."',
                                    '".$_POST['sbuy_metal']."',
                                    '".$_POST['sbuy_crystal']."',
                                    '".$_POST['sbuy_plastic']."',
                                    '".$_POST['sbuy_fuel']."',
                                    '".$_POST['sbuy_food']."',
                                    '".$_POST['ship_for_alliance']."',
                                    '".addslashes($_POST['ship_text'])."',
                                    '".time()."')");

                                dbquery("
                                UPDATE
                                	".$db_table['planets']."
                                SET
                                    planet_res_metal=planet_res_metal-".($_POST['sbuy_metal']*(MARKET_SELL_TAX-1)).",
                                    planet_res_crystal=planet_res_crystal-".($_POST['sbuy_crystal']*(MARKET_SELL_TAX-1)).",
                                    planet_res_plastic=planet_res_plastic-".($_POST['sbuy_plastic']*(MARKET_SELL_TAX-1)).",
                                    planet_res_fuel=planet_res_fuel-".($_POST['sbuy_fuel']*(MARKET_SELL_TAX-1)).",
                                    planet_res_food=planet_res_food-".($_POST['sbuy_food']*(MARKET_SELL_TAX-1))."
                                WHERE
                                    planet_id=".$c->id."
                                    AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");


                            	if($_POST['ressource_for_alliance']!=0)
                            		$for_alliance="für ein Allianzmitglied";
                            	else
                            		$for_alliance="";

                                //Nachricht senden
                                $msg="Du hast folgende Schiffe ".$for_alliance." angeboten:\n\n";
                                $msg.=$arr_name['ship_name'].": ".nf($ship_count)."\n\n";
                                $msg.="Dies zu folgendem Preis:\n\n";
                                $msg.=RES_METAL.": ".nf($_POST['sbuy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['sbuy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['sbuy_plastic'])."\n".RES_FUEL.": ".nf($_POST['sbuy_fuel'])."\n".RES_FOOD.": ".nf($_POST['sbuy_food'])."\n\n";
                                $msg.="Das Handelsministerium";
                                send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Angebot eingetragen",$msg);

                                //Log schreiben
                                add_log(LOG_CAT,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat folgende Schiffe zum Verkauf ".$for_alliance." angeboten:\n\n".nf($ship_count)." ".$arr_name['ship_name']."\n\nPreis:\n ".RES_METAL.": ".nf($_POST['sbuy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['sbuy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['sbuy_plastic'])."\n".RES_FUEL.": ".nf($_POST['sbuy_fuel'])."\n".RES_FOOD.": ".nf($_POST['sbuy_food'])."\n\n",time());

                              echo "Angebot erfolgreich abgesendet<br/><br/>";
                                return_btn();
                            }
                            else
                            {
                                echo "Keine Schiffe dieses Typs vorhanden!<br/><br/>";
                                return_btn();
                            }
                        }
                        else
                        {
                            echo "Angebot konnte nicht aufgegeben werden, der Preisvorschlag weist zu grosse Unterschiede auf!<br/>(weniger als ".(SHIP_PRICE_FACTOR_MIN*100)."% oder mehr als ".(SHIP_PRICE_FACTOR_MAX*100)."%)<br/><br/>";
                            return_btn();
                        }
                    }
                    else
                    {
                        echo "Angebot konnte nicht aufgegeben werden, du hast keinen Preis festgelegt!<br/><br/>";
                        return_btn();
                    }
                }
                else
                {
                    echo "Du hast die Anzahl Schiffe die du verkaufen willst nicht angegeben!<br/><br/>";
                    return_btn();
                }
            }
            else
            {
                echo "Unerlaubtes Zeichen (".$check_text.") in der Beschreibung!<br/><br/>";
                return_btn();
            }
		}

		//
		// Aufgegebene Auktione Speichern
		//
		elseif ($_POST['auction_sell_submit']!="" && checker_verify())
		{
			$_POST['auction_sell_metal']=abs(floor(deltick($_POST['auction_sell_metal'])));
			$_POST['auction_sell_crystal']=abs(floor(deltick($_POST['auction_sell_crystal'])));
			$_POST['auction_sell_plastic']=abs(floor(deltick($_POST['auction_sell_plastic'])));
			$_POST['auction_sell_fuel']=abs(floor(deltick($_POST['auction_sell_fuel'])));
			$_POST['auction_sell_food']=abs(floor(deltick($_POST['auction_sell_food'])));

	 		$_POST['auction_count_ship']=abs(ceil(deltick($_POST['auction_count_ship'])));

	 		if($_POST['auction_currency_metal']=="") $_POST['auction_currency_metal']=0;
	 		if($_POST['auction_currency_crystal']=="") $_POST['auction_currency_crystal']=0;
	 		if($_POST['auction_currency_plastic']=="") $_POST['auction_currency_plastic']=0;
	 		if($_POST['auction_currency_fuel']=="") $_POST['auction_currency_fuel']=0;
	 		if($_POST['auction_currency_food']=="") $_POST['auction_currency_food']=0;


	 		$min_time = AUCTION_MIN_DURATION*24*3600;
	 		$auction_time_days = $_POST['auction_time_days'];
	 		$auction_time_hours = $_POST['auction_time_hours'];
	 		$auction_end_time = time() + $min_time + ($auction_time_days*24*3600) + ($auction_time_hours*3600);

	 		$ship_update=0;
	 		$ress_update=0;


	 		// Überprüfung, ob Schiffe und/oder Rohstoffe angegebon worden sind
	 		if ($_POST['auction_count_ship']>0 || $_POST['auction_sell_metal']>0 || $_POST['auction_sell_crystal']>0 || $_POST['auction_sell_plastic']>0 || $_POST['auction_sell_fuel']>0 || $_POST['auction_sell_food']>0)
	 		{

	 			//überprüft ob min. ein zahlungsmittel angegeben ist
	 			if($_POST['auction_currency_metal']==1 || $_POST['auction_currency_crystal']==1 || $_POST['auction_currency_plastic']==1 || $_POST['auction_currency_fuel']==1 || $_POST['auction_currency_food']==1)
	 			{

                        //
                        // Zuerst werden die Schiffe gespeichert, falls welche angegeben wurden
                        //

                        // Überprüfung ob Anzahl Schiffe zu verkaufen grösser als null ist
                        if ($_POST['auction_count_ship']>0)
                        {

                                //Zuerst wird überprüft wie viele Schiffe vorhanden sind
                                $res_shipling=dbquery("
                                SELECT
                                	shiplist_count
                                FROM
                                	".$db_table['shiplist']."
                                WHERE
                                	shiplist_planet_id='".$c->id."'
                                	AND shiplist_ship_id=".$_POST['auction_ship']."
                                	AND shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."'");
                                $rowi=mysql_fetch_array($res_shipling);
                                $ship_count=min($rowi['shiplist_count'],$_POST['auction_count_ship']);

                                //Wenn nicht 0 Schiffe vorhanden sind werden die Schiffe vom Planeten abgezogen
                                if ($ship_count>0)
                                {
									$ship_update=1;
                                }
                                else
                                {
                                    echo "Keine Schiffe dieses Typs vorhanden!<br/><br/>";
                                    return_btn();
                                    $ship_update=0;
                                    $ress_update=0;
                                }

                        }

                        //
                        // Dann werden die Rohstoffe gespeichert, falls welche angegeben wurden
                        //


                        if (
                            ($_POST['auction_sell_metal']*MARKET_SELL_TAX)<=$c->res->metal
                            && ($_POST['auction_sell_crystal']*MARKET_SELL_TAX)<=$c->res->crystal
                            && ($_POST['auction_sell_plastic']*MARKET_SELL_TAX)<=$c->res->plastic
                            && ($_POST['auction_sell_fuel']*MARKET_SELL_TAX)<=$c->res->fuel
                            && ($_POST['auction_sell_food']*MARKET_SELL_TAX)<=$c->res->food
                            )
                        {
                        	//jetzt noch überprüfen, ob der user das gleiche verkaufen will wie er kaufen will -> 100 titan gegen 200 titan
                        	if
                        	(
                        	($_POST['auction_sell_metal']!=0 && $_POST['auction_currency_metal']==1)
                        	|| ($_POST['auction_sell_crystal']!=0 && $_POST['auction_currency_crystal']==1)
                        	|| ($_POST['auction_sell_plastic']!=0 && $_POST['auction_currency_plastic']==1)
                        	|| ($_POST['auction_sell_fuel']!=0 && $_POST['auction_currency_fuel']==1)
                        	|| ($_POST['auction_sell_food']!=0 && $_POST['auction_currency_food']==1)
                        	)
                        	{
                                echo "Die verlangten Rohstoffe d&uuml;rfen nicht identisch sein mit den zu verkaufenden Rohstoffen!<br><br/>";
                                return_btn();
                                $ship_update=0;
                        		$ress_update=0;
                            }
                            else
                            {
                            	$ress_update=1;
                            }
                        }
                        else
                        {
                            echo "Angebot konnte nicht aufgegeben werden, keine entsprechende Ressourcen auf dem Planeten vorhanden!<br/><br/>";
                            return_btn();
                            $ship_update=0;
                            $ress_update=0;
                        }


						//Schiffe und ev. auch noch Rohstoffe wurde angegeben
                        if($ship_update==1)
                        {
                            //Rohstoffe + Taxe vom Planetenkonto abziehen
                            dbquery("
                            UPDATE
                                ".$db_table['planets']."
                            SET
                                planet_res_metal=planet_res_metal-".($_POST['auction_sell_metal']*MARKET_SELL_TAX).",
                                planet_res_crystal=planet_res_crystal-".($_POST['auction_sell_crystal']*MARKET_SELL_TAX).",
                                planet_res_plastic=planet_res_plastic-".($_POST['auction_sell_plastic']*MARKET_SELL_TAX).",
                                planet_res_fuel=planet_res_fuel-".($_POST['auction_sell_fuel']*MARKET_SELL_TAX).",
                                planet_res_food=planet_res_food-".($_POST['auction_sell_food']*MARKET_SELL_TAX)."
                            WHERE
                                planet_id=".$c->id."
                                AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");

                            $res_name=dbquery("SELECT ship_name FROM ".$db_table['ships']." WHERE ship_id='".$_POST['auction_ship']."'");
                            $arr_name=mysql_fetch_array($res_name);

                            // Anzahl Schiffe vom Planeten abziehen
                            dbquery("
                            UPDATE
                            	".$db_table['shiplist']."
                            SET
                            	shiplist_count='".($rowi['shiplist_count']-$ship_count)."'
                            WHERE
                                shiplist_ship_id='".$_POST['auction_ship']."'
                                AND shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
                                AND shiplist_planet_id='".$c->id."'");

                            // In Datenbank Speichern
                            dbquery("
                            INSERT INTO ".$db_table['market_auction']."
                                (auction_user_id,
                                auction_planet_id,
                                auction_cell_id,
                                auction_start,
                                auction_end,
                                auction_sell_metal,
                                auction_sell_crystal,
                                auction_sell_plastic,
                                auction_sell_fuel,
                                auction_sell_food,
                                auction_ship_id,
                                auction_ship_name,
                                auction_ship_count,
                                auction_currency_metal,
                                auction_currency_crystal,
                                auction_currency_plastic,
                                auction_currency_fuel,
                                auction_currency_food,
                                auction_buyable)
                            VALUES
                                ('".$_SESSION[ROUNDID]['user']['id']."',
                                '".$c->id."',
                                '".$c->solsys_id."',
                                '".time()."',
                                '".$auction_end_time."',
                                '".$_POST['auction_sell_metal']."',
                                '".$_POST['auction_sell_crystal']."',
                                '".$_POST['auction_sell_plastic']."',
                                '".$_POST['auction_sell_fuel']."',
                                '".$_POST['auction_sell_food']."',
                                '".$_POST['auction_ship']."',
                                '".$arr_name['ship_name']."',
                                '".$ship_count."',
                                '".$_POST['auction_currency_metal']."',
                                '".$_POST['auction_currency_crystal']."',
                                '".$_POST['auction_currency_plastic']."',
                                '".$_POST['auction_currency_fuel']."',
                                '".$_POST['auction_currency_food']."',
                                '1')");


                            //Nachricht senden
                            $msg="Du hast folgende Schiffe und Rohstoffe zur versteigerung angeboten\n\n";
                            $msg.="Schiffe:\n";
                            $msg.="".$arr_name['ship_name'].": ".nf($_POST['auction_count_ship'])."\n\n";
                            $msg.="Rohstoffe:\n";
                            $msg.=RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\n";
                            $msg.="Die Auktion endet am ".date("d.m.Y",$auction_end_time)." um ".date("H:i",$auction_end_time)." Uhr\n\n";
                            $msg.="Das Handelsministerium";
                            send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Auktion eingetragen",$msg);

							//Log schreiben
                            add_log(LOG_CAT,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat folgende Waren zur versteigerung angeboten:\n\n".nf($_POST['auction_count_ship'])." ".$arr_name['auction_ship_name']."\n\nRohstoffe:\n ".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\nAuktionsende: ".date("d.m.Y H:i",$auction_end_time)."",time());

                            echo "Auktion erfolgreich lanciert<br/><br/>";
                            return_btn();
                      	}
                      	//Nur Rohstoffe wurden angegeben
                      	elseif($ship_update==0 && $ress_update==1)
                      	{

                            //Rohstoffe + Taxe vom Planetenkonto abziehen
                            dbquery("
                            UPDATE
                                ".$db_table['planets']."
                            SET
                                planet_res_metal=planet_res_metal-".($_POST['auction_sell_metal']*MARKET_SELL_TAX).",
                                planet_res_crystal=planet_res_crystal-".($_POST['auction_sell_crystal']*MARKET_SELL_TAX).",
                                planet_res_plastic=planet_res_plastic-".($_POST['auction_sell_plastic']*MARKET_SELL_TAX).",
                                planet_res_fuel=planet_res_fuel-".($_POST['auction_sell_fuel']*MARKET_SELL_TAX).",
                                planet_res_food=planet_res_food-".($_POST['auction_sell_food']*MARKET_SELL_TAX)."
                            WHERE
                                planet_id=".$c->id."
                                AND planet_user_id=".$_SESSION[ROUNDID]['user']['id']."");

                            // In Datenbank Speichern
                            dbquery("
                            INSERT INTO ".$db_table['market_auction']."
                                (auction_user_id,
                                auction_planet_id,
                                auction_cell_id,
                                auction_start,
                                auction_end,
                                auction_sell_metal,
                                auction_sell_crystal,
                                auction_sell_plastic,
                                auction_sell_fuel,
                                auction_sell_food,
                                auction_currency_metal,
                                auction_currency_crystal,
                                auction_currency_plastic,
                                auction_currency_fuel,
                                auction_currency_food,
                                auction_buyable)
                            VALUES
                                ('".$_SESSION[ROUNDID]['user']['id']."',
                                '".$c->id."',
                                '".$c->solsys_id."',
                                '".time()."',
                                '$auction_end_time',
                                '".$_POST['auction_sell_metal']."',
                                '".$_POST['auction_sell_crystal']."',
                                '".$_POST['auction_sell_plastic']."',
                                '".$_POST['auction_sell_fuel']."',
                                '".$_POST['auction_sell_food']."',
                                '".$_POST['auction_currency_metal']."',
                                '".$_POST['auction_currency_crystal']."',
                                '".$_POST['auction_currency_plastic']."',
                                '".$_POST['auction_currency_fuel']."',
                                '".$_POST['auction_currency_food']."',
                                '1')");



                            //Nachricht senden
                            $msg="Du hast folgende Rohstoffe zur versteigerung angeboten\n\n";
                            $msg.=RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\n";
                            $msg.="Die Auktion endet am ".date("d.m.Y",$auction_end_time)." um ".date("H:i",$auction_end_time)." Uhr\n\n";
                            $msg.="Das Handelsministerium";
                            send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_MISC_MSG_CAT_ID,"Auktion eingetragen",$msg);

                            add_log(LOG_CAT,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat folgende Rohstoffe zur versteigerung angeboten:\n\n".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\nAuktionsende: ".date("d.m.Y H:i",$auction_end_time)."",time());

                            echo "Auktion erfolgreich lanciert<br/><br/>";
                            return_btn();
                  		}
					}
					else
					{
						echo "Auktion konnte nicht aufgegeben werden, es muss mindestens ein Zahlungsmittel angegeben werden!<br/><br/>";
						return_btn();
					}
			}
			else
			{
				echo "Auktion konnte nicht aufgegeben werden, es wurden weder Schiffe noch Rohstoffe angegeben!<br/><br/>";
				return_btn();
			}


		}

		//
		// Die Auswahl an Angeboten von Schiffen
		//
		elseif ($_GET['mode']=="ships" && MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
		{
			echo "<h2>Schiffe</h2>";

			if($_SESSION[ROUNDID]['user']['alliance_application']==0)
				$show_alliance="OR ship_for_alliance='".$_SESSION[ROUNDID]['user']['alliance_id']."'";
			else
				$show_alliance="";

			$res=dbquery("
			SELECT
				*
			FROM
				".$db_table['market_ship']."
			WHERE
				user_id!='".$_SESSION[ROUNDID]['user']['id']."'
				AND ship_buyable='1'
				AND (ship_for_alliance='0'	$show_alliance)
			ORDER BY
				datum ASC");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;mode=ships\" method=\"post\">\n";
				checker_init();
				infobox_start("Angebots&uuml;bersicht",1);
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Angebot:</td>
					<td class=\"tbltitle\" width=\"15%\">Anbieter:</td>
					<td class=\"tbltitle\" width=\"25%\">Datum:</td>
					<td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Preis:</td>
					<td class=\"tbltitle\" width=\"10%\">Kaufen:</td></tr>";
				$cnt=0;
				while ($row=mysql_fetch_array($res))
				{
                    if($row['ship_for_alliance']!=0)
                        $for_alliance="<span style=\"color:".$conf['color_alliance']['v']."\">F&uuml;r Allianzmitglied Reserviert</span>";
                    else
                        $for_alliance="";

					echo "<tr><td class=\"tbldata\" rowspan=\"5\">".$row['ship_count']." <a href=\"?page=help&site=shipyard&id=".$row['ship_id']."\">".$row['ship_name']."</a></td>";
					echo "<td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$row['user_id']."\">".get_user_nick($row['user_id'])."</a></td>";
					echo "<td class=\"tbldata\" rowspan=\"5\">".date("d.m.Y  G:i:s", $row['datum'])."<br><br>".stripslashes($row['ship_text'])."</td>";
					echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\"";
					if ($pr['metal']<$row['ship_costs_metal']) echo " style=\"color:red;\"";
					echo ">".nf($row['ship_costs_metal'])."</td>";
					echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"ship_market_id\" value=\"".$row['ship_market_id']."\"><br><br>".$for_alliance."</td></tr>";

					echo "<tr><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\"";
					if ($pr['crystal']<$row['ship_costs_crystal']) echo " style=\"color:red;\"";
					echo ">".nf($row['ship_costs_crystal'])."</td></tr>";
					echo "<tr><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\"";
					if ($pr['plastic']<$row['ship_costs_plastic']) echo " style=\"color:red;\"";
					echo ">".nf($row['ship_costs_plastic'])."</td></tr>";
					echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\"";
					if ($pr['fuel']<$row['ship_costs_fuel']) echo " style=\"color:red;\"";
					echo ">".nf($row['ship_costs_fuel'])."</td></tr>";
					echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\"";
					if ($pr['food']<$row['ship_costs_food']) echo " style=\"color:red;\"";
					echo ">".nf($row['ship_costs_food'])."</td></tr>";
					$cnt++;
					if ($cnt<mysql_num_rows($res))
						echo "<tr><td class=\"tbldata\" colspan=\"6\" style=\"height:10px;background:#000\"></td></tr>";
				}
				infobox_end(1);
				echo "<input type=\"submit\" class=\"button\" name=\"ship_submit\" value=\"Angebot annehmen\"/>";
				echo "</form>\n";
			}
			else
				echo "Keine Angebote vorhanden";
		}

		//
		// Die Angebote an Rohstoffem anzeigen
		//
		elseif ($_GET['mode']=="ressource" && MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
		{
			echo "<h2>Rohstoffe</h2>";
			$filter="";
			if ($_POST['filter_apply']!="")
			{
				if ($_POST['filter']['sell_metal']>0) $filter.=" AND sell_metal>0";
				if ($_POST['filter']['sell_crystal']>0) $filter.=" AND sell_crystal>0";
				if ($_POST['filter']['sell_plastic']>0) $filter.=" AND sell_plastic>0";
				if ($_POST['filter']['sell_fuel']>0) $filter.=" AND sell_fuel>0";
				if ($_POST['filter']['sell_food']>0) $filter.=" AND sell_food>0";
				if ($_POST['filter']['buy_metal']>0) $filter.=" AND buy_metal>0";
				if ($_POST['filter']['buy_crystal']>0) $filter.=" AND buy_crystal>0";
				if ($_POST['filter']['buy_plastic']>0) $filter.=" AND buy_plastic>0";
				if ($_POST['filter']['buy_fuel']>0) $filter.=" AND buy_fuel>0";
				if ($_POST['filter']['buy_food']>0) $filter.=" AND buy_food>0";

				if ($_POST['filter']['can_buy']>0)
					$filter.=" AND buy_metal<=".$c->res->metal." AND buy_crystal<=".$c->res->crystal." AND buy_plastic<=".$c->res->plastic." AND buy_fuel<=".$c->res->fuel." AND buy_food<=".$c->res->food."";
			}

			echo "<form action=\"?page=$page&amp;mode=ressource\" method=\"post\">\n";
			infobox_start("Filter");
			echo "<table style=\"margin:0px auto;\">";
			echo "<tr><th>Angebot:</th>";
			echo "<td><input type=\"checkbox\" name=\"filter[sell_metal]\" value=\"1\" ";
			if (stristr($filter,"sell_metal>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_METAL."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[sell_crystal]\" value=\"1\" ";
			if (stristr($filter,"sell_crystal>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_CRYSTAL."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[sell_plastic]\" value=\"1\" ";
			if (stristr($filter,"sell_plastic>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_PLASTIC."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[sell_fuel]\" value=\"1\" ";
			if (stristr($filter,"sell_fuel>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_FUEL."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[sell_food]\" value=\"1\" ";
			if (stristr($filter,"sell_food>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_FOOD."</td></tr>";
			echo "<tr><th>Bezahlung:</th>";
			echo "<td><input type=\"checkbox\" name=\"filter[buy_metal]\" value=\"1\" ";
			if (stristr($filter,"buy_metal>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_METAL."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[buy_crystal]\" value=\"1\" ";
			if (stristr($filter,"buy_crystal>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_CRYSTAL."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[buy_plastic]\" value=\"1\" ";
			if (stristr($filter,"buy_plastic>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_PLASTIC."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[buy_fuel]\" value=\"1\" ";
			if (stristr($filter,"buy_fuel>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_FUEL."</td>";
			echo "<td><input type=\"checkbox\" name=\"filter[buy_food]\" value=\"1\" ";
			if (stristr($filter,"buy_food>0")) echo "checked=\"checked\" ";
			echo "/> ".RES_FOOD."</td>";

			echo "<td><input type=\"checkbox\" name=\"filter[can_buy]\" value=\"1\" ";
			if (stristr($filter,"buy_metal<=")) echo "checked=\"checked\" ";
			echo "/> Bezahlbar</td>";

			echo "</tr>";
			echo "</table>";
			infobox_end();
			echo "<input type=\"submit\" name=\"filter_apply\" value=\"Anzeigen\" /> ";
			if ($filter!="")
				echo "&nbsp;&nbsp;&nbsp; <input type=\"submit\" name=\"filter_reset\" value=\"Filter ausschalten\" />";
			echo "<br/><br/>";

			if($_SESSION[ROUNDID]['user']['alliance_application']==0)
				$show_alliance="OR ressource_for_alliance='".$_SESSION[ROUNDID]['user']['alliance_id']."'";
			else
				$show_alliance="";

			$res=dbquery("
			SELECT
				*
			FROM
				".$db_table['market_ressource']."
			WHERE
                user_id!='".$_SESSION[ROUNDID]['user']['id']."'
                AND ressource_buyable='1'
                AND (ressource_for_alliance='0' $show_alliance)
                $filter
			ORDER BY
				datum ASC");
			if (mysql_num_rows($res)>0)
			{
				checker_init();
				infobox_start("Angebots&uuml;bersicht",1);
					echo "<tr><td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Angebot:</td>
					<td class=\"tbltitle\" width=\"15%\">Anbieter:</td>
					<td class=\"tbltitle\" width=\"25%\">Datum:</td>
					<td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Preis:</td>
					<td class=\"tbltitle\" width=\"10%\">Kaufen:</td></tr>";
				$cnt=0;
				while ($row=mysql_fetch_array($res))
				{
                    if($row['ressource_for_alliance']!=0)
                        $for_alliance="<span style=\"color:".$conf['color_alliance']['v']."\">F&uuml;r Allianzmitglied Reserviert</span>";
                    else
                        $for_alliance="";

					echo "<tr><td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($row['sell_metal'])."</td>";
					echo "<td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$row['user_id']."\">".get_user_nick($row['user_id'])."</a></td>";
					echo "<td class=\"tbldata\" rowspan=\"5\">".date("d.m.Y  G:i:s", $row['datum'])."<br><br>".stripslashes($row['ressource_text'])."</td>";
					echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\"";
					if ($pr['metal']<$row['buy_metal']) echo " style=\"color:red;\"";
					echo ">".nf($row['buy_metal'])."</td>";
					echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"ressource_market_id\" value=\"".$row['ressource_market_id']."\"><br><br>".$for_alliance."</td></tr>";

					echo "<tr><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($row['sell_crystal'])."</td>";
					echo "<td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\"";
					if ($pr['crystal']<$row['buy_crystal']) echo " style=\"color:red;\"";
					echo ">".nf($row['buy_crystal'])."</td></tr>";
					echo "<tr><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($row['sell_plastic'])."</td>";
					echo "<td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\"";
					if ($pr['plastic']<$row['buy_plastic']) echo " style=\"color:red;\"";
					echo ">".nf($row['buy_plastic'])."</td></tr>";
					echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($row['sell_fuel'])."</td>";
					echo "<td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\"";
					if ($pr['fuel']<$row['buy_fuel']) echo " style=\"color:red;\"";
					echo ">".nf($row['buy_fuel'])."</td></tr>";
					echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($row['sell_food'])."</td>";
					echo "<td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\"";
					if ($pr['food']<$row['buy_food']) echo " style=\"color:red;\"";
					echo ">".nf($row['buy_food'])."</td></tr>";
					$cnt++;
					if ($cnt<mysql_num_rows($res))
						echo "<tr><td class=\"tbldata\" colspan=\"7\" style=\"height:10px;background:#000\"></td></tr>";

				}
				infobox_end(1);
				echo "<input type=\"submit\" class=\"button\" name=\"ressource_submit\" value=\"Angebot annehmen\"/>";
			}
			else
				echo "Keine Angebote vorhanden!";
			echo "</form>\n";
		}

		//
		// Auktionen Anzeigen
		//
		elseif ($_GET['mode']=="auction" && MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
		{
			?>
			<script type="text/javascript">
				function setCountdown(cnt,field)
				{
					//alert(cnt + " " + field);

					if (cnt>=0)
					{
						t = Math.floor(cnt / 3600 / 24);
						h = Math.floor((cnt-(t*3600*24)) / 3600);
						m = Math.floor((cnt-(t*3600*24)-(h*3600))/60);
						s = Math.floor((cnt-(t*3600*24)-(h*3600)-(m*60)));

						if (cnt==3)
						{
							nv = '3,';
						}
						else if(cnt==2)
						{
							nv = '3,2';
						}
						else if(cnt==1)
						{
							nv = '3,2,1';
						}
						else if(cnt==0)
						{
							nv = '3,2,1... Meins';
						}
                        else if(cnt>=3600 && cnt<(3600*24))
                        {

                        	nv = 'Noch '+h+'h '+m+'m '+s+'s';
                        }
                        else if(cnt<3600)
                        {
                        	nv = 'Noch '+m+'m '+s+'s';
                        }

						else
						{
							nv = 'Noch '+t+'t '+h+'h '+m+'m '+s+'s';
						}

					}
					else
					{
						nv = "AUKTION BEENDET!";
					}
					document.getElementById(field).firstChild.nodeValue=nv;
					cnt = cnt - 1;
					setTimeout("setCountdown('"+cnt+"','"+field+"')",1000);
				}
			</script>

			<?PHP
			echo "<h2>Auktionen</h2>";
			$filter="";
			if ($_POST['auction_filter_apply']!="")
			{
				if ($_POST['auction_filter']['auction_sell_metal']>0) $filter.=" AND auction_sell_metal>0";
				if ($_POST['auction_filter']['auction_sell_crystal']>0) $filter.=" AND auction_sell_crystal>0";
				if ($_POST['auction_filter']['auction_sell_plastic']>0) $filter.=" AND auction_sell_plastic>0";
				if ($_POST['auction_filter']['auction_sell_fuel']>0) $filter.=" AND auction_sell_fuel>0";
				if ($_POST['auction_filter']['auction_sell_food']>0) $filter.=" AND auction_sell_food>0";
				if ($_POST['auction_filter']['auction_ship_id']>0) $filter.=" AND auction_ship_id>0";
				if ($_POST['auction_filter']['auction_buyable']>0) $filter.=" AND auction_buyable=1";

				if ($_POST['auction_filter']['auction_currency_metal']>0) $filter.=" AND auction_currency_metal=1";
				if ($_POST['auction_filter']['auction_currency_crystal']>0) $filter.=" AND auction_currency_crystal=1";
				if ($_POST['auction_filter']['auction_currency_plastic']>0) $filter.=" AND auction_currency_plastic=1";
				if ($_POST['auction_filter']['auction_currency_fuel']>0) $filter.=" AND auction_currency_fuel=1";
				if ($_POST['auction_filter']['auction_currency_food']>0) $filter.=" AND auction_currency_food=1";
			}

			echo "<form action=\"?page=$page&amp;mode=auction\" method=\"post\">\n";
			infobox_start("Filter");
			echo "<table style=\"margin:0px auto;\">";
			echo "<tr><th>Angebot:</th>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_sell_metal]\" value=\"1\" ";
			if (stristr($filter,"auction_sell_metal")) echo "checked=\"checked\" ";
			echo "/> ".RES_METAL."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_sell_crystal]\" value=\"1\" ";
			if (stristr($filter,"auction_sell_crystal")) echo "checked=\"checked\" ";
			echo "/> ".RES_CRYSTAL."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_sell_plastic]\" value=\"1\" ";
			if (stristr($filter,"auction_sell_plastic")) echo "checked=\"checked\" ";
			echo "/> ".RES_PLASTIC."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_sell_fuel]\" value=\"1\" ";
			if (stristr($filter,"auction_sell_fuel")) echo "checked=\"checked\" ";
			echo "/> ".RES_FUEL."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_sell_food]\" value=\"1\" ";
			if (stristr($filter,"auction_sell_food")) echo "checked=\"checked\" ";
			echo "/> ".RES_FOOD."&nbsp;</td>";
			echo "<td><div align=\"left\"><input type=\"checkbox\" name=\"auction_filter[auction_ship_id]\" value=\"1\" ";
			if (stristr($filter,"auction_ship_id")) echo "checked=\"checked\" ";
			echo "/> Schiffe&nbsp;</td></div></tr>";

			echo "<tr><th>Bezahlung:</th>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_currency_metal]\" value=\"1\" ";
			if (stristr($filter,"auction_currency_metal")) echo "checked=\"checked\" ";
			echo "/> ".RES_METAL."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_currency_crystal]\" value=\"1\" ";
			if (stristr($filter,"auction_currency_crystal")) echo "checked=\"checked\" ";
			echo "/> ".RES_CRYSTAL."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_currency_plastic]\" value=\"1\" ";
			if (stristr($filter,"auction_currency_plastic")) echo "checked=\"checked\" ";
			echo "/> ".RES_PLASTIC."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_currency_fuel]\" value=\"1\" ";
			if (stristr($filter,"auction_currency_fuel")) echo "checked=\"checked\" ";
			echo "/> ".RES_FUEL."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_currency_food]\" value=\"1\" ";
			if (stristr($filter,"auction_currency_food")) echo "checked=\"checked\" ";
			echo "/> ".RES_FOOD."&nbsp;</td>";
			echo "<td><input type=\"checkbox\" name=\"auction_filter[auction_buyable]\" value=\"1\" ";
			if (stristr($filter,"auction_buyable")) echo "checked=\"checked\" ";
			echo "/> Verkaufte ausblenden&nbsp;</td></tr>";
			echo "</table>";
			infobox_end();

			echo "<input type=\"submit\" name=\"auction_filter_apply\" value=\"Anzeigen\" /> ";
			if ($filter!="")
				echo "&nbsp;&nbsp;&nbsp; <input type=\"submit\" name=\"auction_filter_reset\" value=\"Filter ausschalten\" />";
			echo "<br/><br/>";

			$res=dbquery("SELECT * FROM ".$db_table['market_auction']." WHERE auction_user_id!='".$_SESSION[ROUNDID]['user']['id']."' $filter ORDER BY auction_end ASC");
			if (mysql_num_rows($res)>0)
			{
				checker_init();
				infobox_start("Angebots&uuml;bersicht",1);
                infobox_end(1,1);
				$cnt=0;
				$acnts=array();
				$acnt=0;
				while ($arr=mysql_fetch_array($res))
				{
					$acnt++;
						infobox_start("",1);

						echo "<tr>
                        <td class=\"tbltitle\">Anbieter</td>
                        <td class=\"tbltitle\">Auktion Start/Ende</td>
                        <td class=\"tbltitle\" colspan=\"3\">Angebot</td>
                        <td class=\"tbltitle\">Bieten</td></tr>";

						//restliche zeit bis zum ende
						$rest_time=$arr['auction_end']-time();

                        $t = floor($rest_time / 3600 / 24);
                        $h = floor(($rest_time-($t*24*3600)) / 3600);
                        $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
                        $s = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));

						if($rest_time<=3600)
						{
							$class = "class=\"tbldata2\"";
						}
						else
						{
							$class = "class=\"tbldata\"";
						}
						$rest_time = "Noch $t t $h h $m m $s s";

						echo "<tr><td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a></td>";
						echo "<td class=\"tbldata\">Start ".date("d.m.Y  G:i:s", $arr['auction_start'])."</td>";

						// Sind Schiffe angeboten
						if($arr['auction_ship_id']>0)
						{
							echo "<td class=\"tbldata\" rowspan=\"5\">".$arr['auction_ship_count']." <a href=\"?page=help&site=shipyard&id=".$arr['auction_ship_id']."\">".$arr['auction_ship_name']."</a></td>";
						}
						else
						{
							echo "<td class=\"tbldata\" rowspan=\"5\" width=\"150\">&nbsp;</td>";
						}

						echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_metal'])."</td>";

						// "Bieten" button wenn der höchstbietende schaut
						if($arr['auction_current_buyer_id']==$_SESSION[ROUNDID]['user']['id'] && $arr['auction_buyable']==1)
						{
							echo "<td class=\"tbldata\" rowspan=\"5\">&nbsp;</td></tr>";
						}
						// wenn das gebot schon versteigert wurde
						elseif($arr['auction_buyable']==0)
						{
							echo "<td class=\"tbldata2\" rowspan=\"5\">Versteigert</td></tr>";
						}
						else
						{
							echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"auction_market_id\" value=\"".$arr['auction_market_id']."\"></td></tr>";
						}


						if($arr['auction_delete_date']==0)
						{
							$acnts['countdown'.$acnt]=$arr['auction_end']-time();
							echo "<tr><td class=\"tbldata\">Ende ".date("d.m.Y  G:i:s", $arr['auction_end'])."</td><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td $class rowspan=\"3\" id=\"countdown".$acnt."\">$rest_time</td><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}
						else
						{
							$delete_rest_time = $arr['auction_delete_date']-time();

                            $t = floor($delete_rest_time / 3600 / 24);
                            $h = floor(($delete_rest_time) / 3600);
                            $m = floor(($delete_rest_time-($h*3600))/60);
                            $s = floor(($delete_rest_time-($h*3600)-($m*60)));

							echo "<tr><td class=\"tbldata\">Auktion beendet</td><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td class=\"tbldata\" rowspan=\"3\">Gebot wird nach $h Stunden und $m Minuten gel&ouml;scht</td><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}
						echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_fuel'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_food'])."</td></tr>";

						//Hochstgebot Anzeigen wenn schon geboten worden ist
						if($arr['auction_current_buyer_id']!=0)
						{
                            echo "<tr><td class=\"tbltitle\" colspan=\"6\">H&ouml;chstgebot</td></tr>";
                            //Höchstbietender User anzeigen wenn vorhanden
                            echo "<tr><td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a></td>";
                            echo "<td class=\"tbldata\" rowspan=\"5\">Geboten ".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."</td>";

                            echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_metal'])."</td>";
                            echo "<td class=\"tbldata2\" rowspan=\"5\"></td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_crystal'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_plastic'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_fuel'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_food'])."</td></tr>";
                        }

                        infobox_end(1,1);


						if($arr['auction_buyable']==1)
						{
                            infobox_start("",1);
                            // Hier kann der User bieten
                            echo "<tr><td class=\"tbltitle\" colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">Bieten</td></tr>";
                            echo "<tr><td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".RES_METAL.":</td>";
                            echo "<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".RES_CRYSTAL.":</td>";
                            echo "<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".RES_PLASTIC.":</td>";
                            echo "<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".RES_FUEL.":</td>";
                            echo "<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".RES_FOOD."</td></tr>";

                            if($arr['auction_currency_metal']==1)
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_buy_metal[".$arr['auction_market_id']."]\" size=\"7\" maxlength=\"15\"/></td>";
                            else
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\">-</td>";

                            if($arr['auction_currency_crystal']==1)
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_buy_crystal[".$arr['auction_market_id']."]\" size=\"7\" maxlength=\"15\"/></td>";
                            else
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\">-</td>";

                            if($arr['auction_currency_plastic']==1)
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_buy_plastic[".$arr['auction_market_id']."]\" size=\"7\" maxlength=\"15\"/></td>";
                            else
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\">-</td>";

                            if($arr['auction_currency_fuel']==1)
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_buy_fuel[".$arr['auction_market_id']."]\" size=\"7\" maxlength=\"15\"/></td>";
                            else
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\">-</td>";

                            if($arr['auction_currency_food']==1)
                                echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_buy_food[".$arr['auction_market_id']."]\" size=\"7\" maxlength=\"15\"/></td>";
                            else
                                 echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"text-align:center;vertical-align:middle;\">-</td>";
                            echo "</tr>";
                            infobox_end(1);
                        }
                        else
                        {
                        	echo "<br>";
                        }
                        echo "<br><br>";


				}

				echo "<script type=\"text/javascript\">";
				foreach ($acnts as $cfield=> $ctime)
				{
					echo "setCountdown('".$ctime."','".$cfield."');";
				}
				echo "</script>";

				echo "<input type=\"submit\" class=\"button\" name=\"auction_submit\" value=\"Bieten\"/>";
			}
			else
			{
				echo "Keine Auktionen vorhanden!";
			}
			echo "</form>\n";

		}

		//
		// Eigene Angebote anzeigen
		//
		elseif ($_GET['mode']=="user_sell" || $mode=="user_sell")
		{
			$return_factor = 1 - (1/(MARKET_LEVEL+1));

			// Schiffangebot löschen
			if ($_POST['ship_cancel']!="")
			{
				$scres=dbquery("SELECT * FROM ".$db_table['market_ship']." WHERE planet_id=".$c->id." AND ship_market_id='".$_POST['ship_market_id']."' AND user_id='".$_SESSION[ROUNDID]['user']['id']."'");
				if (mysql_num_rows($scres)>0)
				{
					$scrow=mysql_fetch_array($scres);
					dbquery("UPDATE ".$db_table['shiplist']." SET shiplist_count=shiplist_count+'".(floor($scrow['ship_count']*$return_factor))."' WHERE shiplist_user_id='".$scrow['user_id']."' AND shiplist_planet_id='".$scrow['planet_id']."' AND shiplist_ship_id='".$scrow['ship_id']."'");
					dbquery("DELETE FROM ".$db_table['market_ship']." WHERE ship_market_id='".$_POST['ship_market_id']."'");
					echo "Angebot wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Schiffe zur&uuml;ck erhalten (es wird abgerundet)";
				}
				else
				{
					echo "Es wurde kein entsprechendes Angebot ausgew&auml;hlt!<br/><br/>";
					return_btn(array("mode"=>"user_sell"));
				}
			}

			// Rohstoffangebot löschen
			elseif ($_POST['ressource_cancel']!="")
			{
				$rcres=dbquery("SELECT * FROM ".$db_table['market_ressource']." WHERE planet_id=".$c->id." AND ressource_market_id='".$_POST['ressource_market_id']."' AND user_id='".$_SESSION[ROUNDID]['user']['id']."'");
				if (mysql_num_rows($rcres)>0)
				{
					$rcrow=mysql_fetch_array($rcres);
					dbquery("UPDATE ".$db_table['planets']." SET planet_res_metal=planet_res_metal+'".($rcrow['sell_metal']*$return_factor)."',
													planet_res_crystal=planet_res_crystal+'".($rcrow['sell_crystal']*$return_factor)."',
													planet_res_plastic=planet_res_plastic+'".($rcrow['sell_plastic']*$return_factor)."',
													planet_res_fuel=planet_res_fuel+'".($rcrow['sell_fuel']*$return_factor)."',
													planet_res_food=planet_res_food+'".($rcrow['sell_food']*$return_factor)."'
												WHERE planet_user_id='".$rcrow['user_id']."' AND planet_id='".$rcrow['planet_id']."'");
					dbquery("DELETE FROM ".$db_table['market_ressource']." WHERE ressource_market_id='".$_POST['ressource_market_id']."'");
					add_log(7,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." zieht folgendes Rohstoffangebot zur&uuml;ck: \n\n".RES_METAL.": ".$rcrow['sell_metal']."\n".RES_CRYSTAL.": ".$rcrow['sell_crystal']."\n".RES_PLASTIC.": ".$rcrow['sell_plastic']."\n".RES_FUEL.": ".$rcrow['sell_fuel']."\n".RES_FOOD.": ".$rcrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());
					echo "Angebot wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Rohstoffe zur&uuml;ck erhalten";
				}
				else
				{
					echo "Es wurde kein entsprechendes Angebot ausgew&auml;hlt!<br/><br/>";
					return_btn(array("mode"=>"user_sell"));
				}
			}

			//Auktionen löschen
			elseif($_POST['auction_cancel']!="")
			{
				$acres=dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['market_auction']." 
				WHERE 
					auction_planet_id=".$c->id." 
					AND auction_market_id='".$_POST['auction_market_id']."' 
					AND auction_user_id='".$_SESSION[ROUNDID]['user']['id']."'");
				if (mysql_num_rows($acres)>0)
				{
					// Rohstoffe zurückgeben
					$acrow=mysql_fetch_array($acres);
					dbquery("
					UPDATE 
						".$db_table['planets']." 
					SET
                        planet_res_metal=planet_res_metal+'".($acrow['auction_sell_metal']*$return_factor)."',
                        planet_res_crystal=planet_res_crystal+'".($acrow['auction_sell_crystal']*$return_factor)."',
                        planet_res_plastic=planet_res_plastic+'".($acrow['auction_sell_plastic']*$return_factor)."',
                        planet_res_fuel=planet_res_fuel+'".($acrow['auction_sell_fuel']*$return_factor)."',
                        planet_res_food=planet_res_food+'".($acrow['auction_sell_food']*$return_factor)."'
					WHERE
						planet_user_id='".$acrow['auction_user_id']."'
						AND planet_id='".$acrow['auction_planet_id']."'");

					//Schiffe zurückgeben
					if($acrow['auction_ship_id']!=0)
					{
                        dbquery("UPDATE ".$db_table['shiplist']." SET
                        shiplist_count=shiplist_count+'".(floor($acrow['auction_ship_count']*$return_factor))."'
                        WHERE
                        shiplist_user_id='".$acrow['auction_user_id']."'
                        AND shiplist_planet_id='".$acrow['auction_planet_id']."'
                        AND shiplist_ship_id='".$acrow['auction_ship_id']."'");

                        $log_msg = "Schiffe:\n".$acrow['auction_ship_count']." ".$acrow['auction_ship_name']."\n";
					}

					//Auktion löschen
					dbquery("DELETE FROM ".$db_table['market_auction']." WHERE auction_market_id='".$_POST['auction_market_id']."'");

					add_log(7,"Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." zieht folgende Auktion zur&uuml;ck: \n\n$log_msg\nRohstoffe:\n".RES_METAL.": ".$acrow['sell_metal']."\n".RES_CRYSTAL.": ".$acrow['sell_crystal']."\n".RES_PLASTIC.": ".$acrow['sell_plastic']."\n".RES_FUEL.": ".$acrow['sell_fuel']."\n".RES_FOOD.": ".$acrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());

					echo "Auktion wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Waren zur&uuml;ck erhalten (es wird abgerundet).";
				}
				else
				{
					echo "Es wurde kein entsprechendes Angebot ausgew&auml;hlt!<br/><br/>";
					return_btn(array("mode"=>"user_sell"));
				}

			}

			// Eigene Angebote zeigen
			else
			{
				$cstr=checker_init();

				echo "Wenn du ein Angebot zur&uuml;ckziehst erh&auml;lst du ".(round($return_factor,2)*100)."% des Angebotes zur&uuml;ck (abgerundet).";

				//
				// Rohstoffe
				//
				echo "<h2>Rohstoffe</h2>";
				$res=dbquery("SELECT * FROM ".$db_table['market_ressource']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."' AND ressource_buyable='1' ORDER BY datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					infobox_start("Angebots&uuml;bersicht",1);
						echo "<tr><td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Angebot:</td>
						<td class=\"tbltitle\" width=\"15%\">Anbieter:</td>
						<td class=\"tbltitle\" width=\"25%\">Datum/Text:</td>
						<td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Preis:</td>
						<td class=\"tbltitle\" width=\"10%\">Zur&uuml;ckziehen:</td></tr>";
					$cnt=0;
					while ($row=mysql_fetch_array($res))
					{
						if($row['ressource_for_alliance']!=0)
							$for_alliance="<span style=\"color:".$conf['color_alliance']['v']."\">F&uuml;r Allianzmitglied Reserviert</span>";
						else
							$for_alliance="";

						echo "<tr><td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($row['sell_metal'])."</td>";
						echo "<td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$row['user_id']."\">".get_user_nick($row['user_id'])."</a></td>";
						echo "<td class=\"tbldata\" rowspan=\"5\">".date("d.m.Y  G:i:s", $row['datum'])."<br><br>".stripslashes($row['ressource_text'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($row['buy_metal'])."</td>";
						echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"ressource_market_id\" value=\"".$row['ressource_market_id']."\"><br><br>".$for_alliance."</td></tr>";

						echo "<tr><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($row['sell_crystal'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($row['buy_crystal'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($row['sell_plastic'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($row['buy_plastic'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($row['sell_fuel'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($row['buy_fuel'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($row['sell_food'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($row['buy_food'])."</td></tr>";
						$cnt++;
						if ($cnt<mysql_num_rows($res))
							echo "<tr><td class=\"tbldata\" colspan=\"7\" style=\"height:10px;background:#000\"></td></tr>";
					}
					infobox_end(1);
					echo "<input type=\"submit\" class=\"button\" name=\"ressource_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
					echo "</form><br><br>";
				}
				else
					echo "Keine Angebote vorhanden!<br><br>";


				//
				// Schiffe
				//
				echo "<h2>Schiffe</h2>";
				$res=dbquery("SELECT * FROM ".$db_table['market_ship']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."' AND ship_buyable='1' ORDER BY datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					infobox_start("Angebots&uuml;bersicht",1);

                    echo "<tr><td class=\"tbltitle\" width=\"25%\">Angebot:</td>
                    <td class=\"tbltitle\" width=\"15%\">Anbieter:</td>
                    <td class=\"tbltitle\" width=\"25%\">Datum/Text:</td>
                    <td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Preis:</td>
                    <td class=\"tbltitle\" width=\"10%\">Zur&uuml;ckziehen:</td></tr>";

					$cnt=0;
					while ($arr=mysql_fetch_array($res))
					{
						if($arr['ship_for_alliance']!=0)
							$for_alliance="<span style=\"color:".$conf['color_alliance']['v']."\">F&uuml;r Allianzmitglied Reserviert</span>";
						else
							$for_alliance="";

						echo "<tr><td class=\"tbldata\" rowspan=\"5\">".$arr['ship_count']." <a href=\"?page=help&site=shipyard&id=".$arr['ship_id']."\">".$arr['ship_name']."</a></td>";
						echo "<td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a></td>";
						echo "<td class=\"tbldata\" rowspan=\"5\">".date("d.m.Y  G:i:s", $arr['datum'])."<br><br>".stripslashes($arr['ship_text'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($arr['ship_costs_metal'])."</td>";
						echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"ship_market_id\" value=\"".$arr['ship_market_id']."\"><br><br>".$for_alliance."</td></tr>";

						echo "<tr><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['ship_costs_crystal'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['ship_costs_plastic'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($arr['ship_costs_fuel'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($arr['ship_costs_food'])."</td></tr>";
						$cnt++;
						if ($cnt<mysql_num_rows($res))
							echo "<tr><td class=\"tbldata\" colspan=\"6\" style=\"height:10px;background:#000\"></td></tr>";
					}
					infobox_end(1);
					echo "<input type=\"submit\" class=\"button\" name=\"ship_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
					echo "</form><br><br>";
				}
				else
					echo "Keine Angebote vorhanden<br><br>";

				//
				// Auktionen
				//
				echo "<h2>Auktionen</h2>";
                ?>
                <script type="text/javascript">
                    function setCountdown(cnt,field)
                    {
                        //alert(cnt + " " + field);

                        t = Math.floor(cnt / 3600 / 24);
                        h = Math.floor((cnt-(t*3600*24)) / 3600);
                        m = Math.floor((cnt-(t*3600*24)-(h*3600))/60);
                        s = Math.floor((cnt-(t*3600*24)-(h*3600)-(m*60)));

                        if (cnt>=(3600*24))
                        {
                            nv = 'Noch '+t+'t '+h+'h '+m+'m '+s+'s';
                        }
                        else if(cnt>=3600 && cnt<(3600*24))
                        {

                        	nv = 'Noch '+h+'h '+m+'m '+s+'s';
                        }
                        else if(cnt<3600 && cnt>0)
                        {
                        	nv = 'Noch '+m+'m '+s+'s';
                        }
                        else
                        {
                            nv = "AUKTION BEENDET!";
                        }
                        document.getElementById(field).firstChild.nodeValue=nv;
                        cnt = cnt - 1;
                        setTimeout("setCountdown('"+cnt+"','"+field+"')",1000);
                    }
                </script>

                <?PHP

				$res=dbquery("SELECT * FROM ".$db_table['market_auction']." WHERE auction_user_id='".$_SESSION[ROUNDID]['user']['id']."' ORDER BY auction_buyable DESC, auction_end ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					infobox_start("Angebots&uuml;bersicht",1);
					infobox_end(1,1);
					$cnt=0;
					$acnts=array();
					$acnt=0;
					while ($arr=mysql_fetch_array($res))
					{
						$acnt++;
						infobox_start("",1);
						echo "<tr>
						<td class=\"tbltitle\">Anbieter</td>
						<td class=\"tbltitle\">Auktion Start/Ende</td>
						<td class=\"tbltitle\" colspan=\"3\">Angebot</td>
						<td class=\"tbltitle\">Zur&uuml;ckziehen</td></tr>";

						//restliche zeit bis zum ende
						$rest_time=$arr['auction_end']-time();

                        $t = floor($rest_time / 3600 / 24);
                        $h = floor(($rest_time-($t*24*3600)) / 3600);
                        $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
                        $s = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));


						if($rest_time<=3600)
						{
							$class = "class=\"tbldata2\"";
							 $rest_time = "Noch $m m $s s";
						}
						else
						{
							$class = "class=\"tbldata\"";
							$rest_time = "Noch $t t $h h $m m $s s";
						}

                        //$rest_time = "Noch $t t $h h $m m $s s";

						echo "<tr><td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a></td>";
						echo "<td class=\"tbldata\">Start ".date("d.m.Y  G:i:s", $arr['auction_start'])."</td>";


						// Sind Schiffe angeboten
						if($arr['auction_ship_id']>0)
						{
							echo "<td class=\"tbldata\" rowspan=\"5\">".$arr['auction_ship_count']." <a href=\"?page=help&site=shipyard&id=".$arr['auction_ship_id']."\">".$arr['auction_ship_name']."</a></td>";
						}
						else
						{
							echo "<td class=\"tbldata\" rowspan=\"5\">&nbsp;</td>";
						}

						echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_metal'])."</td>";

						 // Zurückzieh button wenn noch niemand geboten hat
						if($arr['auction_current_buyer_id']==0)
						{
                            echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"auction_market_id\" value=\"".$arr['auction_market_id']."\"></td></tr>";
                        }
                        elseif($arr['auction_buyable']==0)
                        {
                        	echo "<td class=\"tbldata2\" rowspan=\"5\">Verkauft!</td></tr>";
                        }
                        else
                        {
                        	 echo "<td class=\"tbldata\" rowspan=\"5\">Es wurde bereits geboten</td></tr>";
                        }


						// Start/Ende Anzeigen sofern die auktion nicht schon beendet ist
						if($arr['auction_delete_date']==0)
						{
							$acnts['countdown'.$acnt]=$arr['auction_end']-time();
							echo "<tr><td class=\"tbldata\">Ende ".date("d.m.Y  G:i:s", $arr['auction_end'])."</td><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td $class rowspan=\"3\" id=\"countdown".$acnt."\">$rest_time</td><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}
						// sonst das löschdatum anzeigen
						else
						{
							$delete_rest_time = $arr['auction_delete_date']-time();

                            $t = floor($delete_rest_time / 3600 / 24);
                            $h = floor(($delete_rest_time) / 3600);
                            $m = floor(($delete_rest_time-($h*3600))/60);
                            $s = floor(($delete_rest_time-($h*3600)-($m*60)));

							echo "<tr><td class=\"tbldata\">Auktion beendet</td><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td class=\"tbldata\" rowspan=\"3\">Gebot wird nach $h Stunden und $m Minuten gel&ouml;scht</td><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}


						echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_fuel'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_food'])."</td></tr>";

						//Hochstgebot Anzeigen wenn schon geboten worden ist
						if($arr['auction_current_buyer_id']!=0)
						{
                            echo "<tr><td class=\"tbltitle\" colspan=\"6\">H&ouml;chstgebot</td></tr>";
                            //Höchstbietender User anzeigen wenn vorhanden
                            echo "<tr><td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a></td>";
                            echo "<td class=\"tbldata\" rowspan=\"5\">Geboten ".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."</td>";

                            echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_metal'])."</td>";

							// meldung geben, falls der bietende, das maximum erreicht hat
	                        if($arr['auction_buyable']==1)
                            {
                                echo "<td class=\"tbldata2\" rowspan=\"5\">&nbsp;</td></tr>";
                            }
                            else
                            {
                                 echo "<td class=\"tbldata2\" rowspan=\"5\">&nbsp;</td></tr>";
                            }

                            echo "<tr><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_crystal'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_plastic'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_fuel'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_food'])."</td></tr>";
                        }
                        infobox_end(1);
                        echo "<br><br>";
/*
						$cnt++;
						if ($cnt<mysql_num_rows($res))
							echo "<tr><td class=\"tbldata\" colspan=\"6\" style=\"height:10px;background:#000\"></td></tr>";
							*/
					}
					echo "<script type=\"text/javascript\">";
                    foreach ($acnts as $cfield=> $ctime)
                    {
                        echo "setCountdown('".$ctime."','".$cfield."');";
                    }
                    echo "</script>";
					echo "<input type=\"submit\" class=\"button\" name=\"auction_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
					echo "</form><br>";
				}
				else
					echo "Keine Angebote vorhanden<br><br>";
			}
		}

		//
		//Und sonst kann der User noch eigene Angebote aufgeben
		//
		else
		{
			//Anzahl momentaner Angebote und wie viele er noch kann
			$sares=dbquery("SELECT ship_market_id FROM ".$db_table['market_ship']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."' AND planet_id='".$c->id."'");
			$rares=dbquery("SELECT ressource_market_id FROM ".$db_table['market_ressource']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."' AND planet_id='".$c->id."'");
			$aares=dbquery("SELECT auction_market_id FROM ".$db_table['market_auction']." WHERE auction_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND auction_planet_id='".$c->id."'");
			$anzahl=mysql_num_rows($sares)+mysql_num_rows($rares)+mysql_num_rows($aares);
			$possible=MARKET_LEVEL-$anzahl;
			echo "Im Moment hast du ".$anzahl." Angebote von diesem Planet auf dem Markt<br/>";
			echo "Du kannst noch ".$possible." Angebote einstellen<br/>";
			echo "Der Verkaufsgeb&uuml;hr des Marktplatzes betr&auml;gt ".round(((MARKET_SELL_TAX-1)*100),3)."%<br/><br/>";

			if ($possible>0)
			{


				//
				// Rohstoffe
				//
				if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
				{
                    //Hier wird das ganze für die Rohstoffe noch angezeigt
                    echo "<form action=\"?page=$page\" method=\"post\">\n";
                    $cstr=checker_init();
                    infobox_start("Rohstoffe verkaufen",1);

                    //Zuerst die zu verkaufenden Rohstoffe
                    echo "<tr><td class=\"tbltitle\" colspan=\"5\">Anzahl Rohstoffe:</td></tr>";
                    echo "<tr><td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_METAL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_FOOD."</td></tr>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"sell_metal\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"sell_crystal\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"sell_plastic\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"sell_fuel\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"sell_food\" size=\"7\" maxlength=\"15\"/></td></tr>";

                    //Dann noch den Preis für die Rohstoffe
                    echo "<tr><td class=\"tbltitle\" colspan=\"5\">Verkaufspreis:</td></tr>";
                    echo "<tr><td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_METAL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\" style=\"vertical-align:middle;\">".RES_FOOD."</td></tr>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"buy_metal\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"buy_crystal\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"buy_plastic\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"buy_fuel\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"buy_food\" size=\"7\" maxlength=\"15\"/></td></tr>";

                    //Verkaufstext und für Allianzmitglied reservieren
                    echo "<tr><td class=\"tbltitle\" colspan=\"5\">Beschreibung und Reservation:</td></tr>";
                    echo "<tr><td class=\"tbldata\" colspan=\"3\" height=\"30\" width=\"60%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"\" name=\"ressource_text\" size=\"55\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")."/></td>";

                    //Für allianzmitglied reservieren
                    if($_SESSION[ROUNDID]['user']['alliance_id']!=0 && $_SESSION[ROUNDID]['user']['alliance_application']==0)
                    {
                         echo "<td class=\"tbldata\" colspan=\"2\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen")."><input type=\"checkbox\" name=\"ressource_for_alliance\" value=\"1\"> F&uuml;r Allianzmitglieder Reservieren</td></tr>";
                    }
                    else
                    {
                        echo "<td class=\"tbldata\" colspan=\"2\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\">&nbsp;</td></tr>";
                    }

                    infobox_end(1);
                    echo "<input type=\"submit\" class=\"button\" name=\"ressource_sell_submit\" value=\"Angebot aufgeben\"/></form><br/><br/>";
                }


				//
				// Schiffe
				//
				if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
				{
                    //Zuerst wird überprüft ob auf dem Planeten Schiffe sind, auch ob diese dem User gehören
                    if (mysql_num_rows(dbquery("SELECT shiplist_id FROM ".$db_table['shiplist']." WHERE shiplist_planet_id='".$c->id."'"))>0)
                    {
                        $sres=dbquery("
                        SELECT
                            ships.ship_id,
                            ships.ship_name,
                            ships.ship_costs_metal,
                            ships.ship_costs_crystal,
                            ships.ship_costs_plastic,
                            ships.ship_costs_fuel,
                            ships.ship_costs_food,
                            shiplist.shiplist_count                            
                        FROM
                            ".$db_table['shiplist'].",
                            ".$db_table['ships']."
                        WHERE
                            shiplist.shiplist_ship_id=ships.ship_id
                            AND shiplist.shiplist_planet_id='".$c->id."'
                            AND shiplist.shiplist_special_ship=0
                        ORDER BY
                            ships.ship_name;");
                        $ships=array();
                        while ($sarr=mysql_fetch_array($sres))
                        {
                            array_push($ships,$sarr);
                        }

                        echo "<script type=\"text/javascript\">
                        function market_ship_price()
                        {
                            res=new Array();
                            ";
                            foreach ($ships as $sarr)
                            {
                                echo "res[".$sarr['ship_id']."]=new Array();";
                                echo "res[".$sarr['ship_id']."][0]=".$sarr['ship_costs_metal'].";\n";
                                echo "res[".$sarr['ship_id']."][1]=".$sarr['ship_costs_crystal'].";\n";
                                echo "res[".$sarr['ship_id']."][2]=".$sarr['ship_costs_plastic'].";\n";
                                echo "res[".$sarr['ship_id']."][3]=".$sarr['ship_costs_fuel'].";\n";
                                echo "res[".$sarr['ship_id']."][4]=".$sarr['ship_costs_food'].";\n";
                            }
                            echo "x=document.getElementById('ship').options[document.getElementById('ship').selectedIndex].value;
                            document.getElementById('ship_price_0').value=res[x][0]*document.getElementById('count_ship').value;
                            document.getElementById('ship_price_1').value=res[x][1]*document.getElementById('count_ship').value;
                            document.getElementById('ship_price_2').value=res[x][2]*document.getElementById('count_ship').value;
                            document.getElementById('ship_price_3').value=res[x][3]*document.getElementById('count_ship').value;
                            document.getElementById('ship_price_4').value=res[x][4]*document.getElementById('count_ship').value;

                            setTick(document.getElementById('ship_price_0'));
                            setTick(document.getElementById('ship_price_1'));
                            setTick(document.getElementById('ship_price_2'));
                            setTick(document.getElementById('ship_price_3'));
                            setTick(document.getElementById('ship_price_4'));


                        }

                        </script>";

                        //Hier die Ausgabe aller Schiffstypen, die der User auf diesem Planeten hat
                        echo "<form action=\"?page=$page\" method=\"post\">\n";
                        echo $cstr;
                        infobox_start("Schiffe verkaufen",1);
                        echo "<tr><td class=\"tbldata\" height=\"30\" width=\"195\" style=\"vertical-align:middle;\"><b>Schiffname:</b></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"195\" colspan=\"2\" style=\"vertical-align:middle;\"><select name=\"ship\" id=\"ship\" onchange=\"market_ship_price()\">";
                        foreach ($ships as $sarr)
                        {
                            if ($sarr['shiplist_count']>0)
                                echo "<option value=\"".$sarr['ship_id']."\">".$sarr['ship_name']." (".$sarr['shiplist_count'].")</option>";
                        }
                        echo "</select></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"195\" colspan=\"2\" style=\"vertical-align:middle;\"><input  type=\"text\" value=\"1\" ".numberfield(1)." id=\"count_ship\" name=\"count_ship\" size=\"4\" maxlength=\"6\" onchange=\"market_ship_price()\"/> St&uuml;ck</td></tr>";

                        //Hier kann der Verkaufende noch den Preis festlegen
                        echo "<tr><td class=\"tbltitle\" width=\"110\">".RES_METAL.":</td>";
                        echo "<td class=\"tbltitle\" width=\"97\">".RES_CRYSTAL.":</td>";
                        echo "<td class=\"tbltitle\" width=\"98\">".RES_PLASTIC.":</td>";
                        echo "<td class=\"tbltitle\" width=\"97\">".RES_FUEL.":</td>";
                        echo "<td class=\"tbltitle\" width=\"98\">".RES_FOOD."</td></tr>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." id=\"ship_price_0\" name=\"sbuy_metal\" size=\"6\" maxlength=\"15\"/></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." id=\"ship_price_1\" name=\"sbuy_crystal\" size=\"6\" maxlength=\"15\"/></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." id=\"ship_price_2\" name=\"sbuy_plastic\" size=\"6\" maxlength=\"15\"/></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." id=\"ship_price_3\" name=\"sbuy_fuel\" size=\"6\" maxlength=\"15\"/></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." id=\"ship_price_4\" name=\"sbuy_food\" size=\"6\" maxlength=\"15\"/></td></tr>";


                        //Verkaufstext
                        echo "<tr><td class=\"tbltitle\" colspan=\"5\">Beschreibung und Reservation:</td></tr>";
                        echo "<tr><td class=\"tbldata\" colspan=\"3\" height=\"30\" width=\"60%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"\" name=\"ship_text\" size=\"55\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")."/></td>";

                        //Für allianzmitglied reservieren
                        if($_SESSION[ROUNDID]['user']['alliance_id']!=0 && $_SESSION[ROUNDID]['user']['alliance_application']==0)
                        {
                        	echo "<td class=\"tbldata\" colspan=\"2\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen")."><input type=\"checkbox\" name=\"ship_for_alliance\" value=\"1\"> F&uuml;r Allianzmitglieder Reservieren</td></tr>";
                        }
                        else
                        {
                        	echo "<td class=\"tbldata\" colspan=\"2\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\">&nbsp;</td></tr>";
                        }

                        infobox_end(1);
                        echo "<input type=\"submit\" class=\"button\" name=\"ship_sell_submit\" value=\"Angebot aufgeben\"/>";
                        echo "</form><br/><br/>";
                        echo "<script type=\"text/javascript\">market_ship_price();</script>";
                    }
                }


				//
				// Auktionen
				//
				if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
				{
                    infobox_start("Schiffe und/oder Rohstoffe versteigern",1);
                    echo "<form action=\"?page=$page\" method=\"post\">\n";
                    echo $cstr;
                    // Schiffs versteigerung (gleiches formular wie bei "schiffe")
                    //Zuerst wird überprüft ob auf dem Planeten Schiffe sind, auch ob diese dem User gehören
                    if (mysql_num_rows(dbquery("SELECT shiplist_id FROM ".$db_table['shiplist']." WHERE shiplist_planet_id='".$c->id."'"))>0)
                    {
                        $sres=dbquery("
                        SELECT
                            ships.ship_id,
                            ships.ship_name,
                            ships.ship_costs_metal,
                            ships.ship_costs_crystal,
                            ships.ship_costs_plastic,
                            ships.ship_costs_fuel,
                            ships.ship_costs_food,
                            shiplist.shiplist_count                            
                        FROM
                            ".$db_table['shiplist'].",
                            ".$db_table['ships']."
                        WHERE
                            shiplist.shiplist_ship_id=ships.ship_id
                            AND shiplist.shiplist_planet_id='".$c->id."'
                            AND shiplist.shiplist_special_ship=0
                        ORDER BY
                            ships.ship_name;");
                        $ships=array();
                        while ($sarr=mysql_fetch_array($sres))
                        {
                            array_push($ships,$sarr);
                        }

                        //Hier die Ausgabe aller Schiffstypen, die der User auf diesem Planeten hat
                        echo "<tr><td class=\"tbltitle\" colspan=\"5\">Schiffe</td></tr>";
                        echo "<tr><td class=\"tbldata\" height=\"30\" width=\"195\" style=\"vertical-align:middle;\"><b>Schiffname:</b></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"195\" style=\"vertical-align:middle;\" colspan=\"2\"><select name=\"auction_ship\" id=\"ship\">";
                        foreach ($ships as $sarr)
                        {
                            if ($sarr['shiplist_count']>0)
                                echo "<option value=\"".$sarr['ship_id']."\">".$sarr['ship_name']." (".$sarr['shiplist_count'].")</option>";
                        }
                        echo "</select></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"195\" style=\"vertical-align:middle;\" colspan=\"2\"><input  type=\"text\" value=\"0\" ".numberfield(1)." id=\"count_ship\" name=\"auction_count_ship\" size=\"4\" maxlength=\"6\" /> St&uuml;ck</td></tr>";
                    }
                    // Keine Schiffe vorhanden
                    else
                    {
                        echo "<tr><td class=\"tbltitle\" colspan=\"5\">Schiffe</td></tr>";
                        echo "<tr><td class=\"tbldata\" height=\"30\"  width=\"195\"><b>Schiffname:</b></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"195\" colspan=\"2\"><select name=\"ship\" id=\"ship\" onchange=\"market_ship_price()\">";
                        echo "<option>Keine Schiffe vorhanden</option>";
                        echo "</select></td>";
                        echo "<td class=\"tbldata\" height=\"30\" width=\"195\" colspan=\"2\"><input  type=\"text\" value=\"-\" ".numberfield(1)." size=\"4\" maxlength=\"6\" readonly=\"readonly\"/> St&uuml;ck</td></tr>";
                    }


                    //Rohstoff versteigerung
                    echo "<tr><td class=\"tbltitle\" colspan=\"5\">Rohstoffe</td></tr>";
                    echo "<tr>";
                    echo "<td class=\"tbltitle\" width=\"20%\">".RES_METAL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\">".RES_CRYSTAL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\">".RES_PLASTIC.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\">".RES_FUEL.":</td>";
                    echo "<td class=\"tbltitle\" width=\"20%\">".RES_FOOD."</td></tr>";
                    echo "<tr>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_sell_metal\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_sell_crystal\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_sell_plastic\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_sell_fuel\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"text\" value=\"0\" ".numberfield()." name=\"auction_sell_food\" size=\"7\" maxlength=\"15\"/></td>";
                    echo "</tr>";

                    // Welche Rohstoffe dürfen als zahlungsmittel gebraucht werden
                    echo "<tr><td class=\"tbltitle\" colspan=\"5\">Bezahlung mit:</td></tr>";
                    echo "<tr>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"checkbox\" name=\"auction_currency_metal\" value=\"1\" checked=\"checked\"> ".RES_METAL."</td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"checkbox\" name=\"auction_currency_crystal\" value=\"1\" checked=\"checked\"> ".RES_CRYSTAL."</td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"checkbox\" name=\"auction_currency_plastic\" value=\"1\" checked=\"checked\"> ".RES_PLASTIC."</td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"checkbox\" name=\"auction_currency_fuel\" value=\"1\" checked=\"checked\"> ".RES_FUEL."</td>";
                    echo "<td class=\"tbldata\" height=\"30\" width=\"20%\" style=\"vertical-align:middle;\"><input type=\"checkbox\" name=\"auction_currency_food\" value=\"1\" checked=\"checked\"> ".RES_FOOD."</td>";
                    echo "</tr>";

                    // Dauer der Auktion...
                    echo "<tr><td class=\"tbltitle\" colspan=\"5\">Dauer</td></tr>";
                    echo "<tr>";
                    echo "<td class=\"tbldata\" height=\"30\" colspan=\"3\" style=\"vertical-align:middle;\">";
                    echo AUCTION_MIN_DURATION." Tage + ";

                    //... in Tagen ...
                    echo "<select name=\"auction_time_days\" id=\"auction_time_days\" onchange=\"auction_end_time();\">";
                    for($x=0;$x<=10;$x++)
                    {
                            echo "<option value=\"".$x."\">$x</option>";
                    }
                    echo "</select> Tage und ";

                    //... und in Stunden
                    echo "<select name=\"auction_time_hours\" id=\"auction_time_hours\" onchange=\"auction_end_time();\">";
                    for($x=0;$x<=24;$x++)
                    {
                            echo "<option value=\"".$x."\">$x</option>";
                    }


                    echo "</select> Stunden  ";
                    echo "bis zum Auktionsende";
                    echo "</td>";
                    echo "
                    <script type=\"text/javascript\">
                    function auction_end_time()
                    {
                        t=".time().";
                        var auction_time_days=document.getElementById('auction_time_days').options[ document.getElementById('auction_time_days').selectedIndex].value;
                        var auction_time_hours=document.getElementById('auction_time_hours').options[ document.getElementById('auction_time_hours').selectedIndex].value;
                        newtime=t+(".AUCTION_MIN_DURATION."*24*3600)+(auction_time_days*24*3600)+(auction_time_hours*3600);
                        var date = new Date(newtime*1000);
                                            var day = date.getDate();
                        var month = date.getMonth()+1;
                                            var year = date.getFullYear();
                                            var hours = date.getHours();
                                            var minutes = date.getMinutes();

                                            datum = ((day < 10) ? '0' + day : day)  + '.'+ ((month < 10) ? '0' + month : month) +'.'+ year + ' ' +((hours < 10) ? '0' + hours : hours) +':'+ ((minutes < 10) ? '0' + minutes : minutes);
                        document.getElementById('end_time').firstChild.nodeValue='Auktionsende: '+datum;


                    }

                    </script>
                    ";

                    $time = time() + (AUCTION_MIN_DURATION*24*3600);

                    echo "<td class=\"tbldata\" height=\"30\" colspan=\"2\"  id=\"end_time\" style=\"vertical-align:middle;\">Auktionsende: ".date("d.m.Y H:i",$time)."</td>";
                    echo "</tr>";
                    infobox_end(1);
                    echo "<input type=\"submit\" class=\"button\" name=\"auction_sell_submit\" value=\"Angebot aufgeben\"/>";
                    echo "</form><br/><br/>";



                }
            }
		}
	}

	//
	// Meldung dass noch kein Marktplatz gebaut wurde
	//
	else
		echo "Der Marktplatz wurde noch nicht gebaut.";


?>
