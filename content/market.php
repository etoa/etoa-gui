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
	// 	File: market.php
	// 	Created: 01.12.2004
	// 	Last edited: 05.08.2007
	// 	Last edited by: Lamborghini <lamborghini@etoa.ch>
	//
		
	/**
	* Ship- and Resource-Market
	*
	* @package etoa_gameserver
	* @author Lamborghini <lamborghini@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	/********
	*	Datei-Struktur
	*	Die Datei market.php ist wie folgt aufgebaut
	*
	*	if(Markt gebaut)
	*	{
	*		-> Navigation
	*		-> Markt Update
	*		
	*		-> Rohstoffverkauf speichern
	*		-> Schiffskauf speichern
	*		-> Auktionsgebot speichern
	*		
	*		-> Rohstoffverkauf speichern
	*		-> Schiffverkauf speichern
	*		-> Auktion Speichern
	*		
	*		-> Rohstoff Angebote anzeigen
	*		-> Schiffs Angebote anzeigen
	*		-> Auktionen anzeigen
	*		-> Einzelne Auktion anzeigen 
	*		
	*		-> Suchmaske
	*		-> Eigene Angebote anzeigen
	*		-> Angebote aufgeben
	*	}
	*
	********/

	//Alle Definitionen befinden sich in der def.inc.php!


	// BEGIN SKRIPT //

	echo "<h1>Marktplatz des Planeten ".$c->name."</h1>";
	//echo "<br/><div style=\"color:red;font-size:20pt;\">In bearbeitung!</div><br/><br/>";
	echo "<div id=\"marketinfo\"></div>"; //nur zu entwicklungszwecken!

	// Zeigt Rohstoffbox an
	$c->resBox();

	//Überprüfung ob der Marktplatz schon gebaut wurde
	$mres=dbquery("
	SELECT 
		buildlist_current_level 
	FROM 
		".$db_table['buildlist']." 
	WHERE 
	buildlist_planet_id='".$c->id."'
		AND buildlist_building_id='".MARKTPLATZ_ID."' 
		AND buildlist_current_level>0 
		AND buildlist_user_id='".$s['user']['id']."';");
	
	if (mysql_num_rows($mres)>0)
	{
		?>
			<script type="text/javascript">
				
				// Überprüfungsscript für alle Formulare ob XAJAX die Eingaben vor dem Senden nochmals geprüft hat
				function checkUpdate(formName, updateField, checkField)
				{
					if(document.getElementById(updateField).value == 1)
					{
						if(document.getElementById(checkField).value == 1)
						{
							document.forms[formName].submit();
						}
						else
						{
							alert('Eingaben wurden noch nicht aktualisiert!');
						}
					}
					else
					{
						setTimeout("checkUpdate('"+formName+"', '"+updateField+"', '"+checkField+"')",50);
					}
					
				}
				
				// Wird benötigt um vom "Bieten-Formular" zurück zu den ausgewählten Auktionen zu gelangen
				function auctionBack()
				{
					document.getElementById('auction_back').value=1;
					document.forms['auctionShowFormular'].submit();
				}
				
				// Leitet zum "Biet-Formular" weiter. Die ID der gewählten Auktion wird vorher noch definiert
				function showAuction(field, val)
				{
					document.getElementById(field).value=val;
					document.forms['auctionListFormular'].submit();
				}
			</script>
		<?PHP 		
		
		
    $marr = mysql_fetch_array($mres);
    define("MARKET_LEVEL",$marr['buildlist_current_level']);
    
    // Definiert den Rückgabefaktor beim zurückziehen eines Angebots
    $return_factor = 1 - (1/(MARKET_LEVEL+1));

		// Navigation
		echo "[ <a href=\"?page=".$page."&amp;mode=user_home\">Angebote aufgeben</a> | 
		<a href=\"?page=".$page."&amp;mode=user_sell\">Eigene Angebote</a> | 
		<a href=\"?page=".$page."&amp;mode=search\">Angebotssuche</a> ]<br/><br/>";

		//
    // Alle Abgelaufenen Auktionen löschen und ev. waren versenden
    //
		market_auction_update();



		//
		// Rohstoffkauf speichern
		//
		if ($_POST['ressource_submit']!="" && checker_verify())
		{
			$cnt = 0;
			$cnt_error = 0;
			$sell_metal_total = 0;
			$sell_crystal_total = 0;
			$sell_plastic_total = 0;
			$sell_fuel_total = 0;
			$sell_food_total = 0;
	
			foreach ($_POST['ressource_market_id'] as $num => $id)
			{
				// Lädt Angebotsdaten
				$res = dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['market_ressource']." 
				WHERE 
					ressource_market_id='".$id."' 
					AND ressource_buyable='1';");
				// Prüft, ob Angebot noch vorhanden ist
				if (mysql_num_rows($res)!=0)
				{		
					$arr = mysql_fetch_array($res);
							
					// Prüft, ob genug Rohstoffe vorhanden sind
					if ($c->res->metal >= $arr['buy_metal'] 
					&& $c->res->crystal >= $arr['buy_crystal']  
					&& $c->res->plastic >= $arr['buy_plastic']  
					&& $c->res->fuel >= $arr['buy_fuel']  
					&& $c->res->food >= $arr['buy_food'])
					{
						$seller_user_nick = get_user_nick($arr['user_id']);	
	
						//Angebot reservieren (wird zu einem späteren Zeitpunkt verschickt)
						dbquery("
						UPDATE
							".$db_table['market_ressource']."
						SET
							ressource_buyable='0',
							ressource_buyer_id='".$s['user']['id']."',
							ressource_buyer_planet_id='".$c->id."',
							ressource_buyer_cell_id='".$c->solsys_id."'
						WHERE
							ressource_market_id='".$arr['ressource_market_id']."'");
	
						// Rohstoffe vom Käuferplanet abziehen und $c-variabeln anpassen
						$c->changeRes(-$arr['buy_metal'],-$arr['buy_crystal'],-$arr['buy_plastic'],-$arr['buy_fuel'],-$arr['buy_food']);
						
						// Nachricht an Verkäufer
						$msg = "Ein Handel ist zustande gekommen\n";
						$msg .= "Der Spieler ".$s['user']['nick']." hat von dir folgende Rohstoffe gekauft:\n\n";
						
						$msg .= "".RES_METAL.": ".nf($arr['sell_metal'])."\n";
						$msg .= "".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n";
						$msg .= "".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n";
						$msg .= "".RES_FUEL.": ".nf($arr['sell_fuel'])."\n";
						$msg .= "".RES_FOOD.": ".nf($arr['sell_food'])."\n\n";
						
						$msg .= "Dies macht dich um folgende Rohstoffe reicher:\n\n";
						
						$msg .= "".RES_METAL.": ".nf($arr['buy_metal'])."\n";
						$msg .= "".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n";
						$msg .= "".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n";
						$msg .= "".RES_FUEL.": ".nf($arr['buy_fuel'])."\n";
						$msg .= "".RES_FOOD.": ".nf($arr['buy_food'])."\n\n";
						
						$msg .= "Die Rohstoffe werden in wenigen Minuten versendet.\n\n";
						
						$msg .= "Das Handelsministerium";
						send_msg($arr['user_id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);
	
						// Nachricht an Käufer
						$msg="Ein Handel ist zustande gekommen
						Du hast vom Spieler ".$seller_user_nick." folgende Rohstoffe gekauft:\n\n";
						
						$msg .= "".RES_METAL.": ".nf($arr['sell_metal'])."\n";
						$msg .= "".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n";
						$msg .= "".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n";
						$msg .= "".RES_FUEL.": ".nf($arr['sell_fuel'])."\n";
						$msg .= "".RES_FOOD.": ".nf($arr['sell_food'])."\n";

						$msg .= "Dies hat dich folgende Rohstoffe gekostet:\n\n";

						$msg .= "".RES_METAL.": ".nf($arr['buy_metal'])."\n";
						$msg .= "".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n";
						$msg .= "".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n";
						$msg .= "".RES_FUEL.": ".nf($arr['buy_fuel'])."\n";
						$msg .= "".RES_FOOD.": ".nf($arr['buy_food'])."\n\n";

						$msg .= "Die Rohstoffe werden in wenigen Minuten versendet.\n\n";
						
						$msg .= "Das Handelsministerium";
						send_msg($s['user']['id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);
	
						//Log schreiben, falls dieser Handel regelwidrig ist
						$multi_res1=dbquery("
						SELECT
							user_multi_multi_user_id
						FROM
							".$db_table['user_multi']."
						WHERE
							user_multi_user_id='".$s['user']['id']."'
							AND user_multi_multi_user_id='".$arr['user_id']."';");
	
						$multi_res2=dbquery("
						SELECT
							user_multi_multi_user_id
						FROM
							".$db_table['user_multi']."
						WHERE
							user_multi_user_id='".$arr['user_id']."'
							AND user_multi_multi_user_id='".$s['user']['id']."';");
	
						if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
						{
					    add_log(10,"[URL=?page=user&sub=edit&user_id=".$s['user']['id']."][B]".$s['user']['nick']."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$seller_user_nick."[/B][/URL] Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food']),time());
						}
	
						// Log schreiben
						add_log(7,"Ein Handel ist zustande gekommen\nDer Spieler ".$s['user']['nick']." hat vom Spieler ".$seller_user_nick."  folgende Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food'])."\n\n",time());
						
						// Summiert verkaufte Rohstoffe für Config
						$sell_metal_total += $arr['sell_metal'];
						$sell_crystal_total += $arr['sell_crystal'];
						$sell_plastic_total += $arr['sell_plastic'];
						$sell_fuel_total += $arr['sell_fuel'];
						$sell_food_total += $arr['sell_food'];

						$cnt++;
					}
					else
					{
						$cnt_error++;
					}							
				}
				else
				{
					$cnt_error++;
				}
			}
			
			if($cnt > 0)
			{
				echo "".$cnt." Angebot(e) erfolgreich gekauft!<br/>";
			}
			if($cnt_error > 0)
			{
				echo "".$cnt_error." Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!<br/>";
			}
			
			
			// Verkaufte Rohstoffe in Config-DB speichern für Kurs berechnung
			// Titan
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_metal_total."
			WHERE
				config_name='market_metal_factor'");		
				
			// Silizium
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_crystal_total."
			WHERE
				config_name='market_crystal_factor'");	
				
			// PVC
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_plastic_total."
			WHERE
				config_name='market_plastic_factor'");		
				
			// Tritium
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_fuel_total."
			WHERE
				config_name='market_fuel_factor'");	
				
			// Food
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_food_total."
			WHERE
				config_name='market_food_factor'");
		}



		//
		// Schiffskauf speichern
		//
		elseif ($_POST['ship_submit']!="" && checker_verify())
		{
			$cnt = 0;
			$cnt_error = 0;
			$sell_metal_total = 0;
			$sell_crystal_total = 0;
			$sell_plastic_total = 0;
			$sell_fuel_total = 0;
			$sell_food_total = 0;
				
			foreach ($_POST['ship_market_id'] as $num => $id)
			{
				// Lädt Angebotsdaten
				$res = dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['market_ship']." 
				WHERE 
					ship_market_id='".$id."' 
					AND ship_buyable='1';");
				// Prüft, ob Angebot noch vorhanden ist
				if (mysql_num_rows($res)!=0)
				{		
					$arr = mysql_fetch_array($res);
							
					// Prüft, ob genug Rohstoffe vorhanden sind
					if ($c->res->metal >= $arr['ship_costs_metal'] 
					&& $c->res->crystal >= $arr['ship_costs_crystal']  
					&& $c->res->plastic >= $arr['ship_costs_plastic']  
					&& $c->res->fuel >= $arr['ship_costs_fuel']  
					&& $c->res->food >= $arr['ship_costs_food'])
					{
						$seller_user_nick = get_user_nick($arr['user_id']);			
			
						//Angebot reservieren (wird zu einem späteren Zeitpunkt verschickt)
						dbquery("
						UPDATE
							".$db_table['market_ship']."
						SET
							ship_buyable='0',
							ship_buyer_id='".$s['user']['id']."',
							ship_buyer_planet_id='".$c->id."',
							ship_buyer_cell_id='".$c->solsys_id."'
						WHERE
							ship_market_id='".$id."'");			
			
						// Rohstoffe vom Käuferplanet abziehen und $c-variabeln anpassen
						$c->changeRes(-$arr['ship_costs_metal'],-$arr['ship_costs_crystal'],-$arr['ship_costs_plastic'],-$arr['ship_costs_fuel'],-$arr['ship_costs_food']);	
							
						// Nachricht an Verkäufer
						$msg = "Der Handel war erfolgreich: Der User ".$s['user']['nick']." hat folgende Schiffe von dir gekauft:\n\n";
						
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
						send_msg($s['user']['id'],SHIP_MISC_MSG_CAT_ID,"Handel vollzogen",$msg);
	
	
						//Log schreiben, falls dieser Handel regelwidrig ist
						$multi_res1=dbquery("
						SELECT
							user_multi_multi_user_id
						FROM
							".$db_table['user_multi']."
						WHERE
							user_multi_user_id='".$s['user']['id']."'
							AND user_multi_multi_user_id='".$arr['user_id']."';");
	
						$multi_res2=dbquery("
						SELECT
							user_multi_multi_user_id
						FROM
							".$db_table['user_multi']."
						WHERE
							user_multi_user_id='".$arr['user_id']."'
							AND user_multi_multi_user_id='".$s['user']['id']."';");
	
						if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
						{
					    add_log(10,"[URL=?page=user&sub=edit&user_id=".$s['user']['id']."][B]".$s['user']['nick']."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$seller_user_nick."[/B][/URL] Schiffe gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());
						}
	
						//Marktlog schreiben
						add_log(7,"Der Spieler ".$s['user']['nick']." hat folgende Schiffe von ".$seller_user_nick." gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());		
						
						$cnt++;
					}
					else
					{
						$cnt_error++;
					}							
				}
				else
				{
					$cnt_error++;
				}
			}
			
			if($cnt > 0)
			{
				echo "".$cnt." Angebot(e) erfolgreich gekauft!<br/>";
			}
			if($cnt_error > 0)
			{
				echo "".$cnt_error." Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!<br/>";
			}			

		}
		
		
		
		//
		// Auktionsgebot speichern
		//
		//elseif ($_POST['auction_submit']!="" && checker_verify())
		elseif($_POST['auction_show_last_update']==1  && checker_verify())
		{	
			//Prüft, ob die Eingaben durch die XAJAX Funktionen aktualisiert und ausgewertet wurden
			if($_POST['auction_show_check_submit']==1)
			{
				$_POST['auction_new_buy_metal'] = nf_back($_POST['auction_new_buy_metal']);
				$_POST['auction_new_buy_crystal'] = nf_back($_POST['auction_new_buy_crystal']);
				$_POST['auction_new_buy_plastic'] = nf_back($_POST['auction_new_buy_plastic']);
				$_POST['auction_new_buy_fuel'] = nf_back($_POST['auction_new_buy_fuel']);
				$_POST['auction_new_buy_food'] = nf_back($_POST['auction_new_buy_food']);			
			
				$res=dbquery("
				SELECT
					*
				FROM
					".$db_table['market_auction']."
				WHERE
	        auction_market_id='".$_POST['auction_market_id']."'
	        AND auction_end>'".time()."'
	        AND auction_buyable='1'");
	      // Prüft, ob Angebot noch vorhaden ist
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					
					// Prüft, ob noch genug Rohstoffe vorhanden sind (eventueller Verlust durch Kampf?)
					if ($c->res->metal >= $_POST['auction_new_buy_metal'] 
						&& $c->res->crystal >= $_POST['auction_new_buy_crystal']
						&& $c->res->plastic >= $_POST['auction_new_buy_plastic']
						&& $c->res->fuel >= $_POST['auction_new_buy_fuel']
						&& $c->res->food >= $_POST['auction_new_buy_food'])
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
	                    // Rohstoffe dem überbotenen User wieder zurückgeben
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
	
	                    // Nachricht dem überbotenen User schicken
	                    $msg = "Du wurdest vom Spieler ".$s['user']['nick']." in einer Auktion &uuml;berboten\n";
	                    $msg .= "Die Auktion ist nun zu Ende und wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht.\n";
	                    $msg .= "[URL=?page=market&mode=auction&id=".$arr['auction_market_id']."Hier[/URL] gehts zu der Auktion.\n\n";
	                    
	                    $msg .= "Das Handelsministerium";
	                    send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
	                }
	
	                // Rohstoffe dem Gewinner abziehen
	                dbquery("
	                UPDATE
	                    ".$db_table['planets']."
	                SET
	                    planet_res_metal=planet_res_metal-'".$_POST['auction_new_buy_metal']."',
	                    planet_res_crystal=planet_res_crystal-'".$_POST['auction_new_buy_crystal']."',
	                    planet_res_plastic=planet_res_plastic-'".$_POST['auction_new_buy_plastic']."',
	                    planet_res_fuel=planet_res_fuel-'".$_POST['auction_new_buy_fuel']."',
	                    planet_res_food=planet_res_food-'".$_POST['auction_new_buy_food']."'
	                WHERE
	                    planet_id='".$c->id."'
	                    AND planet_user_id='".$s['user']['id']."'");
	
	
	                // Nachricht an Verkäufer
	                $msg = "Ein Handel ist erfolgreich zustande gekommen.\n";
	                $msg .= "Der Spieler ".$s['user']['nick']." hat von dir folgende Rohstoffe ersteigert: \n\n";
	
	                $msg .= "".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n";
	                $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n";
	                $msg .= "".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n";
	                $msg .= "".RES_FUEL.": ".$arr['auction_sell_fuel']."\n";
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
	                send_msg($s['user']['id'],SHIP_MISC_MSG_CAT_ID,"Auktion vorzeitig beendet",$msg);
	
	
	                // Auktion Speichern und "Stoppen" so dass nicht mehr geboten werden kann
	                $delete_date=time()+(AUCTION_DELAY_TIME*3600);
	                dbquery("
	                UPDATE
	                    ".$db_table['market_auction']."
	                SET
	                    auction_current_buyer_id='".$s['user']['id']."',
	                    auction_current_buyer_planet_id='".$c->id."',
	                    auction_current_buyer_cell_id='".$c->solsys_id."',
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
	                    ".$db_table['user_multi']."
	                WHERE
	                    user_multi_user_id='".$arr['auction_user_id']."'
	                    AND user_multi_multi_user_id='".$s['user']['id']."';");
	
	                $multi_res2=dbquery("
	                SELECT
	                    user_multi_multi_user_id
	                FROM
	                    ".$db_table['user_multi']."
	                WHERE
	                    user_multi_user_id='".$s['user']['id']."'
	                    AND user_multi_multi_user_id='".$arr['auction_user_id']."';");
	
	                if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
	                {
				            add_log(10,"[URL=?page=user&sub=edit&user_id=".$s['user']['id']."][B]".$s['user']['nick']."[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=".$arr['auction_user_id']."][B]".$partner_user_nick."[/B][/URL] gewonnen:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."",time());
	                }
	
	                // Log schreiben
	                add_log(7,"Es wurde folgende Auktion erfolgreich beendet: Der Spieler ".$s['user']['nick']." hat vom Spieler ".$partner_user_nick."  folgende Waren ersteigert:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:".RES_METAL.": ".nf($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_new_buy_food'])."\n\nDie Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht",time());
	
	
	                echo "Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag geboten hast!<br/>";
	                
	                // Verkaufte Rohstoffe in Config-DB speichern für Kurs berechnung
									// Titan
									dbquery("
									UPDATE
										".$db_table['config']."
									SET
										config_param1=config_param1+".$arr['auction_sell_metal']."
									WHERE
										config_name='market_metal_factor'");		
										
									// Silizium
									dbquery("
									UPDATE
										".$db_table['config']."
									SET
										config_param1=config_param1+".$arr['auction_sell_crystal']."
									WHERE
										config_name='market_crystal_factor'");	
										
									// PVC
									dbquery("
									UPDATE
										".$db_table['config']."
									SET
										config_param1=config_param1+".$arr['auction_sell_plastic']."
									WHERE
										config_name='market_plastic_factor'");		
										
									// Tritium
									dbquery("
									UPDATE
										".$db_table['config']."
									SET
										config_param1=config_param1+".$arr['auction_sell_fuel']."
									WHERE
										config_name='market_fuel_factor'");	
										
									// Food
									dbquery("
									UPDATE
										".$db_table['config']."
									SET
										config_param1=config_param1+".$arr['auction_sell_food']."
									WHERE
										config_name='market_food_factor'");
	            }
	            else
	            {
	
	                if($arr['auction_current_buyer_id']!=0)
	                {
	                    // Rohstoffe dem überbotenen User wieder zurückgeben
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
	                    $msg = "Du wurdest vom Spieler ".$s['user']['nick']." in einer Auktion &uuml;berboten\n";
	                    $msg .= "Die Auktion dauert noch bis am ".date("d.m.Y H:i",$arr['auction_end']).".\n";
	                    $msg .= "[URL=?page=market&mode=auction&id=".$_POST['auction_market_id']."]Hier[/URL] gehts zu der Auktion.\n\n";
	                    
	                    $msg .= "Das Handelsministerium";
	                    send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Überboten",$msg);
	                }
	
	                // Rohstoffe vom neuen Bieter abziehen
	                dbquery("
	                UPDATE
	                    ".$db_table['planets']."
	                SET
	                    planet_res_metal=planet_res_metal-'".$_POST['auction_new_buy_metal']."',
	                    planet_res_crystal=planet_res_crystal-'".$_POST['auction_new_buy_crystal']."',
	                    planet_res_plastic=planet_res_plastic-'".$_POST['auction_new_buy_plastic']."',
	                    planet_res_fuel=planet_res_fuel-'".$_POST['auction_new_buy_fuel']."',
	                    planet_res_food=planet_res_food-'".$_POST['auction_new_buy_food']."'
	                WHERE
	                    planet_id='".$c->id."'
	                    AND planet_user_id='".$s['user']['id']."'");
	
	                //Das neue Angebot Speichern
	                dbquery("
	                UPDATE
	                  ".$db_table['market_auction']."
	                SET
	                  auction_current_buyer_id='".$s['user']['id']."',
	                  auction_current_buyer_planet_id='".$c->id."',
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
			}
			else
			{
				echo "Die Eingaben wurden vor dem Absenden nicht aktualisiert!<br/><br/>";
	      return_btn();
			}
		}



		//
		// Rohstoffverkauf speichern
		//
		elseif ($_POST['ress_last_update']==1 && checker_verify())
		{		
			//Prüft, ob die Eingaben durch die XAJAX Funktionen aktualisiert und ausgewertet wurden
			if($_POST['ress_check_submit']==1)
			{
				$_POST['ress_sell_metal'] = nf_back($_POST['ress_sell_metal']);
				$_POST['ress_sell_crystal'] = nf_back($_POST['ress_sell_crystal']);
				$_POST['ress_sell_plastic'] = nf_back($_POST['ress_sell_plastic']);
				$_POST['ress_sell_fuel'] = nf_back($_POST['ress_sell_fuel']);
				$_POST['ress_sell_food'] = nf_back($_POST['ress_sell_food']);
				
				$_POST['ress_buy_metal'] = nf_back($_POST['ress_buy_metal']);
				$_POST['ress_buy_crystal'] = nf_back($_POST['ress_buy_crystal']);
				$_POST['ress_buy_plastic'] = nf_back($_POST['ress_buy_plastic']);
				$_POST['ress_buy_fuel'] = nf_back($_POST['ress_buy_fuel']);
				$_POST['ress_buy_food'] = nf_back($_POST['ress_buy_food']);

				
				if($_POST['ressource_for_alliance']=="")
				{
					$_POST['ressource_for_alliance']=0;
					$for_alliance="";
				}
				else
				{
					$_POST['ressource_for_alliance']=$s['user']['alliance_id'];
					$for_alliance="f&uuml;r ein Allianzmitglied ";
				}
	
				// Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
	  		if($_POST['ress_sell_metal'] * MARKET_SELL_TAX <= $c->res->metal
	  		&& $_POST['ress_sell_crystal'] * MARKET_SELL_TAX <= $c->res->crystal 
	  		&& $_POST['ress_sell_plastic'] * MARKET_SELL_TAX <= $c->res->plastic  
	  		&& $_POST['ress_sell_fuel'] * MARKET_SELL_TAX <= $c->res->fuel
	  		&& $_POST['ress_sell_food'] * MARKET_SELL_TAX <= $c->res->food)
	  		{
		      //Nachricht versenden
		      $msg = "Du hast folgende Rohstoffe ".$for_alliance."angeboten:\n\n";
		      
		      $msg .= "".RES_METAL.": ".nf($_POST['ress_sell_metal'])."\n";
		      $msg .= "".RES_CRYSTAL.": ".nf($_POST['ress_sell_crystal'])."\n";
		      $msg .= "".RES_PLASTIC.": ".nf($_POST['ress_sell_plastic'])."\n";
		      $msg .= "".RES_FUEL.": ".nf($_POST['ress_sell_fuel'])."\n";
		      $msg .= "".RES_FOOD.": ".nf($_POST['ress_sell_food'])."\n\n";
		      
		      $msg .= "Du verlangst folgenden Preis daf&uuml;r:\n\n";
	
		      $msg .= "".RES_METAL.": ".nf($_POST['ress_buy_metal'])."\n";
		      $msg .= "".RES_CRYSTAL.": ".nf($_POST['ress_buy_crystal'])."\n";
		      $msg .= "".RES_PLASTIC.": ".nf($_POST['ress_buy_plastic'])."\n";
		      $msg .= "".RES_FUEL.": ".nf($_POST['ress_buy_fuel'])."\n";
		      $msg .= "".RES_FOOD.": ".nf($_POST['ress_buy_food'])."\n\n";
	
		      $msg .= "Das Handelsministerium";
		      send_msg($s['user']['id'],SHIP_MISC_MSG_CAT_ID,"Angebot eingetragen",$msg);
		
		      // Log schreiben
		      add_log(7,"Der Spieler ".$s['user']['nick']." hat folgende Rohstoffe ".$for_alliance."angeboten:\n\n".RES_METAL.": ".nf($_POST['ress_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['ress_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['ress_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['ress_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['ress_sell_food'])."\n\nFolgender Preis muss daf&uuml;r gezahlt werden:\n\n".RES_METAL.": ".nf($_POST['ress_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['ress_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['ress_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['ress_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['ress_buy_food'])."\n\n",time());
		
		      // Rohstoffe vom Planet abziehen
		      dbquery("
		      UPDATE
		      ".$db_table['planets']."
		      SET
		        planet_res_metal=planet_res_metal-".($_POST['ress_sell_metal']*MARKET_SELL_TAX).",
		        planet_res_crystal=planet_res_crystal-".($_POST['ress_sell_crystal']*MARKET_SELL_TAX).",
		        planet_res_plastic=planet_res_plastic-".($_POST['ress_sell_plastic']*MARKET_SELL_TAX).",
		        planet_res_fuel=planet_res_fuel-".($_POST['ress_sell_fuel']*MARKET_SELL_TAX).",
		        planet_res_food=planet_res_food-".($_POST['ress_sell_food']*MARKET_SELL_TAX)."
		      WHERE
		      	planet_id='".$c->id."'
		      	AND planet_user_id='".$s['user']['id']."';");
		
					// Angebot speichern
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
		          ('".$s['user']['id']."',
		          '".$c->id."',
		          '".$c->solsys_id."',
		          '".$_POST['ress_sell_metal']."',
		          '".$_POST['ress_sell_crystal']."',
		          '".$_POST['ress_sell_plastic']."',
		          '".$_POST['ress_sell_fuel']."',
		          '".$_POST['ress_sell_food']."',
		          '".$_POST['ress_buy_metal']."',
		          '".$_POST['ress_buy_crystal']."',
		          '".$_POST['ress_buy_plastic']."',
		          '".$_POST['ress_buy_fuel']."',
		          '".$_POST['ress_buy_food']."',
		          '".$_POST['ressource_for_alliance']."',
		          '".addslashes($_POST['ressource_text'])."',
		          '".time()."');");
		          
		      echo "Angebot erfolgreich aufgegeben<br/><br/>";
		      return_btn();
	    	}
	    	else
	    	{
	 	      echo "Es sind nicht mehr genügend Rohstoffe vorhanden!<br/><br/>";
		      return_btn();   		
	    	}
    	}
    	else
    	{
    		echo "Die Eingaben wurden vor dem Absenden nicht aktualisiert!<br/><br/>";
	      return_btn();
    	}
    }
		
		

		//
		// Schiffverkauf speichern
		//
		elseif ($_POST['ship_last_update']==1 && checker_verify())
		{
			//Prüft, ob die Eingaben durch die XAJAX Funktionen aktualisiert und ausgewertet wurden
			if($_POST['ship_check_submit']==1)
			{
				if($_POST['ship_for_alliance']=="")
				{
					$_POST['ship_for_alliance']=0;
					$for_alliance="";
				}
				else
				{
					$_POST['ship_for_alliance']=$s['user']['alliance_id'];
					$for_alliance="für ein Allianzmitglied ";
				}
				
				$ship_id = $_POST['ship_list'];
				$ship_name = $_SESSION['market']['ship_data'][$ship_id]['ship_name'];
				$ship_count = nf_back($_POST['ship_count']);
				$_POST['ship_buy_metal'] = nf_back($_POST['ship_buy_metal']);
				$_POST['ship_buy_crystal'] = nf_back($_POST['ship_buy_crystal']);
				$_POST['ship_buy_plastic'] = nf_back($_POST['ship_buy_plastic']);
				$_POST['ship_buy_fuel'] = nf_back($_POST['ship_buy_fuel']);
				$_POST['ship_buy_food'] = nf_back($_POST['ship_buy_food']);			
					
				// Überprüft ob die angegebene Anzahl Schiffe noch vorhanden ist (eventuelle Zerstörung durch Kampf?)
	      $check_res=dbquery("
	      SELECT
	      	shiplist_count
	      FROM
	      	".$db_table['shiplist']."
	      WHERE
	      	shiplist_planet_id='".$c->id."'
	      	AND shiplist_ship_id='".$ship_id."'");
	      if(mysql_num_rows($check_res)>0)
	      {
	      	$check_arr=mysql_fetch_array($check_res);
	      	
	      	if($check_arr['shiplist_count']>=$ship_count)
	      	{
	      		// Schiffe vom Planeten abziehen
	          dbquery("
	          UPDATE
	          	".$db_table['shiplist']."
	          SET
	          	shiplist_count=shiplist_count-".$ship_count."
	          WHERE
	              shiplist_planet_id='".$c->id."'
	              AND shiplist_ship_id='".$_POST['ship_list']."';");
	
						// Angebot speicherns
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
	              ('".$s['user']['id']."',
	              '".$c->id."',
	              '".$c->solsys_id."',
	              '".$ship_id."',
	              '".$ship_name."',
	              '".$ship_count."',
	              '".$_POST['ship_buy_metal']."',
	              '".$_POST['ship_buy_crystal']."',
	              '".$_POST['ship_buy_plastic']."',
	              '".$_POST['ship_buy_fuel']."',
	              '".$_POST['ship_buy_food']."',
	              '".$_POST['ship_for_alliance']."',
	              '".addslashes($_POST['ship_text'])."',
	              '".time()."')");
	
	
	          //Nachricht senden
	          $msg="Du hast folgende Schiffe ".$for_alliance."angeboten:\n\n";
	          
	          $msg .= "".$ship_name.": ".nf($ship_count)."\n\n";
	
	          $msg .= "Dies zu folgendem Preis:\n\n";
	
	          $msg .= "".RES_METAL.": ".nf($_POST['ship_buy_metal'])."\n";
	          $msg .= "".RES_CRYSTAL.": ".nf($_POST['ship_buy_crystal'])."\n";
	          $msg .= "".RES_PLASTIC.": ".nf($_POST['ship_buy_plastic'])."\n";
	          $msg .= "".RES_FUEL.": ".nf($_POST['ship_buy_fuel'])."\n";
	          $msg .= "".RES_FOOD.": ".nf($_POST['ship_buy_food'])."\n\n";
	
	          $msg .= "Das Handelsministerium";
	          send_msg($s['user']['id'],SHIP_MISC_MSG_CAT_ID,"Angebot eingetragen",$msg);
	
	          //Log schreiben
	          add_log(LOG_CAT,"Der Spieler ".$s['user']['nick']." hat folgende Schiffe zum Verkauf ".$for_alliance."angeboten:\n\n".nf($ship_count)." ".$ship_name."\n\nPreis:\n ".RES_METAL.": ".nf($_POST['ship_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['ship_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['ship_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['ship_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['ship_buy_food'])."\n\n",time());
	
	        	echo "Angebot erfolgreich abgesendet<br/><br/>";
	          return_btn();
	        }
	        else
	        {
	       		echo "Die angegebene Anzahl Schiffe ist nicht mehr verfügbar!<br/><br/>";
	          return_btn();       	
	        }
	      }
	      else
	      {
	      	echo "Die angegebenen Schiffe sind nicht mehr vorhanden!<br/><br/>";
	     		return_btn();
	      }
	    }
	    else
    	{
    		echo "Die Eingaben wurden vor dem Absenden nicht aktualisiert!<br/><br/>";
	      return_btn();
    	}
		}



		//
		// Auktion Speichern
		//
		elseif ($_POST['auction_last_update']==1 && checker_verify())
		{	
			//Prüft, ob die Eingaben durch die XAJAX Funktionen aktualisiert und ausgewertet wurden
			if($_POST['auction_check_submit']==1)
			{
				// Berechnet Endzeit
		 		$auction_min_time = AUCTION_MIN_DURATION * 24 * 3600;
		 		$auction_time_days = $_POST['auction_time_days'];
		 		$auction_time_hours = $_POST['auction_time_hours'];
		 		$auction_end_time = time() + $auction_min_time + $auction_time_days * 24 * 3600 + $auction_time_hours * 3600;
	
		 		$ship_update=0;
		 		$ress_update=0;
	
				$_POST['auction_sell_metal'] = nf_back($_POST['auction_sell_metal']);
				$_POST['auction_sell_crystal'] = nf_back($_POST['auction_sell_crystal']);
				$_POST['auction_sell_plastic'] = nf_back($_POST['auction_sell_plastic']);
				$_POST['auction_sell_fuel'] = nf_back($_POST['auction_sell_fuel']);
				$_POST['auction_sell_food'] = nf_back($_POST['auction_sell_food']);
	
				// Prüft ob Rohstoffe noch vorhanden sind (eventueller verlust durch Kampf?)
	      if (($_POST['auction_sell_metal']*MARKET_SELL_TAX)<=$c->res->metal
	          && ($_POST['auction_sell_crystal']*MARKET_SELL_TAX)<=$c->res->crystal
	          && ($_POST['auction_sell_plastic']*MARKET_SELL_TAX)<=$c->res->plastic
	          && ($_POST['auction_sell_fuel']*MARKET_SELL_TAX)<=$c->res->fuel
	          && ($_POST['auction_sell_food']*MARKET_SELL_TAX)<=$c->res->food)
	      {
	
	        // Rohstoffe + Taxe vom Planetenkonto abziehen
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
	            AND planet_user_id=".$s['user']['id']."");
	
	        // Angebot speichern
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
	            auction_text,
	            auction_currency_metal,
	            auction_currency_crystal,
	            auction_currency_plastic,
	            auction_currency_fuel,
	            auction_currency_food,
	            auction_buyable)
	        VALUES
	            ('".$s['user']['id']."',
	            '".$c->id."',
	            '".$c->solsys_id."',
	            '".time()."',
	            '".$auction_end_time."',
	            '".$_POST['auction_sell_metal']."',
	            '".$_POST['auction_sell_crystal']."',
	            '".$_POST['auction_sell_plastic']."',
	            '".$_POST['auction_sell_fuel']."',
	            '".$_POST['auction_sell_food']."',
	            '".addslashes($_POST['auction_text'])."',
	            '".$_POST['auction_buy_metal']."',
	            '".$_POST['auction_buy_crystal']."',
	            '".$_POST['auction_buy_plastic']."',
	            '".$_POST['auction_buy_fuel']."',
	            '".$_POST['auction_buy_food']."',
	            '1')");
	
	
	        //Nachricht senden
	        $msg = "Du hast folgende Rohstoffe zur versteigerung angeboten:\n\n";
	        
	        $msg .= "".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n";
	        $msg .= "".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n";
	        $msg .= "".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n";
	        $msg .= "".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n";
	        $msg .= "".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\n";
	        
	        $msg .= "Die Auktion endet am ".date("d.m.Y",$auction_end_time)." um ".date("H:i",$auction_end_time)." Uhr.\n\n";
	        
	        $msg .= "Das Handelsministerium";
	        send_msg($s['user']['id'],SHIP_MISC_MSG_CAT_ID,"Auktion eingetragen",$msg);
	
	        add_log(LOG_CAT,"Der Spieler ".$s['user']['nick']." hat folgende Rohstoffe zur versteigerung angeboten:\n\n".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\nAuktionsende: ".date("d.m.Y H:i",$auction_end_time)."",time());
	
	        echo "Auktion erfolgreich lanciert<br/><br/>";
	        return_btn();
	
	      }
	      else
	      {
	          echo "Die angegebenen Rohstoffe sind nicht mehr verfügbar!<br/><br/>";
	          return_btn();
	      }
	    }
	    else
    	{
    		echo "Die Eingaben wurden vor dem Absenden nicht aktualisiert!<br/><br/>";
	      return_btn();
    	}	      
		}



		//
		// Rohstoff Angebote anzeigen
		//
		elseif ($_POST['search_submit']!="" && $_POST['search_cat']=="ressource" && checker_verify())
		{
			echo "<h2>Rohstoffe</h2>";

			$res = dbquery("
			SELECT
				*
			FROM
				".$db_table['market_ressource']."
			WHERE
				ressource_buyable='1'
        AND user_id!='".$s['user']['id']."'
        ".stripslashes($_POST['ressource_sql_add'])."
      ORDER BY
				datum ASC;");				
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;mode=ressource\" method=\"post\" id=\"ress_buy_selector\">\n";
				checker_init();				
				infobox_start("Angebots&uuml;bersicht",1);
					echo "<tr>
								<td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Angebot:</td>
								<td class=\"tbltitle\" width=\"15%\">Anbieter:</td>
								<td class=\"tbltitle\" width=\"25%\">Datum:</td>
								<td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Preis:</td>
								<td class=\"tbltitle\" width=\"10%\">Kaufen:</td>
							</tr>";
				$cnt=0;
				while ($arr=mysql_fetch_array($res))
				{
					// Für Allianzmitglied reserveriert
          if($arr['ressource_for_alliance']!=0)
          {
              $for_alliance="<span style=\"color:".$conf['color_alliance']['v']."\">F&uuml;r Allianzmitglied Reserviert</span>";
          }
          else
          {
              $for_alliance="";
          }
              
          // Prüft, ob die Rohstoffe ausreichen für das Angebot und färbt dementsprechend Preis
          $metal_class = "tbldata";
          $crystal_class = "tbldata";
          $plastic_class = "tbldata";
          $fuel_class = "tbldata";
          $food_class = "tbldata";
					if ($c->res->metal < $arr['buy_metal'])
					{ 
						$metal_class = "tbldata2";
					}
					if ($c->res->crystal < $arr['buy_crystal'])
					{ 
						$crystal_class = "tbldata2";
					}					
					if ($c->res->plastic < $arr['buy_plastic'])
					{ 
						$plastic_class = "tbldata2";
					}
					if ($c->res->fuel < $arr['buy_fuel'])
					{ 
						$fuel_class = "tbldata2";
					}
					if ($c->res->food < $arr['buy_food'])
					{ 
						$food_class = "tbldata2";
					}
					
					echo "<tr>
									<td class=\"tbldata\"><b>".RES_METAL."</b>:";


					// Übergibt Daten an XAJAX
					// Aktuelle Rohstoffe vom Planeten
          echo "<input type=\"hidden\" value=\"".$c->res->metal."\" name=\"res_metal\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->crystal."\" name=\"res_crystal\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->plastic."\" name=\"res_plastic\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->fuel."\" name=\"res_fuel\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->food."\" name=\"res_food\" />";								
					
					// Preis
          echo "<input type=\"hidden\" value=\"".$arr['buy_metal']."\" name=\"ress_buy_metal[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_crystal']."\" name=\"ress_buy_crystal[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_plastic']."\" name=\"ress_buy_plastic[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_fuel']."\" name=\"ress_buy_fuel[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_food']."\" name=\"ress_buy_food[".$arr['ressource_market_id']."]\" />";						

									
									
									
									echo "</td>
									<td class=\"tbldata\">".nf($arr['sell_metal'])."</td>
									<td class=\"tbldata\" rowspan=\"5\">
										<a href=\"?page=userinfo&amp;id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a>
									</td>
									<td class=\"tbldata\" rowspan=\"5\">
										".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['ressource_text'])."
									</td>
									<td class=\"tbldata\"><b>".RES_METAL."</b>:</td>
									<td class=\"".$metal_class."\">".nf($arr['buy_metal'])."</td>
									<td class=\"tbldata\" rowspan=\"5\">
										<input type=\"checkbox\" name=\"ressource_market_id[]\" id=\"ressource_market_id\" value=\"".$arr['ressource_market_id']."\" onclick=\"xajax_calcMarketRessBuy(xajax.getFormValues('ress_buy_selector'));\"><br/><br/>".$for_alliance."
									</td>
								</tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td>
									<td class=\"tbldata\">".nf($arr['sell_crystal'])."</td>
									<td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td>
									<td class=\"".$crystal_class."\">".nf($arr['buy_crystal'])."</td>
								</tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td>
									<td class=\"tbldata\">".nf($arr['sell_plastic'])."</td>
									<td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td>
									<td class=\"".$plastic_class."\">".nf($arr['buy_plastic'])."</td></tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_FUEL."</b>:</td>
									<td class=\"tbldata\">".nf($arr['sell_fuel'])."</td>
									<td class=\"tbldata\"><b>".RES_FUEL."</b>:</td>
									<td class=\"".$fuel_class."\">".nf($arr['buy_fuel'])."</td>
								</tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_FOOD."</b>:</td>
									<td class=\"tbldata\">".nf($arr['sell_food'])."</td>
									<td class=\"tbldata\"><b>".RES_FOOD."</b>:</td>
									<td class=\"".$food_class."\">".nf($arr['buy_food'])."</td>
								</tr>";
					$cnt++;
					// Setzt Lücke zwischen den Angeboten
					if ($cnt<mysql_num_rows($res))
					{
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\" style=\"height:10px;background:#000\">&nbsp;</td>
									</tr>";
					}

				}
				infobox_end(1);
				
				infobox_start("",1);
				echo "<tr>
								<td class=\"tbldata\" colspan=\"7\" id=\"ressource_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
							</tr>
							<tr>
								<td class=\"tbldata\" colspan=\"7\" id=\"ressource_check_message\" style=\"text-align:center;vertical-align:middle;\">
									<input type=\"submit\" class=\"button\" name=\"ressource_submit\" id=\"ressource_submit\" value=\"Angebot annehmen\" disabled=\"disabled\"/>
								</td>
							</tr>";
				infobox_end(1);
				echo "</form>";
			}
			else
			{
				echo "Keine Angebote vorhanden!";
			}
			
			echo "</form>\n";
		}



		//
		// Schiffs Angebote anzeigen
		//
		elseif($_POST['search_submit']!="" && $_POST['search_cat']=="ship" && checker_verify())
		{		
			echo "<form action=\"?page=".$page."&amp;mode=ships\" method=\"post\" id=\"ship_buy_selector\">\n";
			checker_init();
				
			infobox_start("Angebots&uuml;bersicht",1);
			echo "<tr>
						<td class=\"tbltitle\" width=\"25%\">Angebot:</td>
						<td class=\"tbltitle\" width=\"15%\">Anbieter:</td>
						<td class=\"tbltitle\" width=\"25%\">Datum:</td>
						<td class=\"tbltitle\" colspan=\"2\" width=\"25%\">Preis:</td>
						<td class=\"tbltitle\" width=\"10%\">Kaufen:</td>
					</tr>";	
				
			$res = dbquery("	
			SELECT
				*
			FROM
				".$db_table['market_ship']."
			WHERE
				ship_buyable='1'
        AND user_id!='".$s['user']['id']."'
        ".stripslashes($_POST['ship_sql_add'])."
      ORDER BY
				datum ASC;");															
			$cnt=0;
			if(mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_array($res))
				{
					// Übergibt Daten an XAJAX
					// Aktuelle Rohstoffe vom Planeten
          echo "<input type=\"hidden\" value=\"".$c->res->metal."\" name=\"res_metal\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->crystal."\" name=\"res_crystal\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->plastic."\" name=\"res_plastic\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->fuel."\" name=\"res_fuel\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->food."\" name=\"res_food\" />";								
					
					// Preis
          echo "<input type=\"hidden\" value=\"".$arr['ship_costs_metal']."\" name=\"ship_buy_metal[".$arr['ship_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['ship_costs_crystal']."\" name=\"ship_buy_crystal[".$arr['ship_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['ship_costs_plastic']."\" name=\"ship_buy_plastic[".$arr['ship_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['ship_costs_fuel']."\" name=\"ship_buy_fuel[".$arr['ship_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['ship_costs_food']."\" name=\"ship_buy_food[".$arr['ship_market_id']."]\" />";						
					
					
					
					// Für Allianzmitglied reserveriert
          if($arr['ship_for_alliance']!=0)
          {
              $for_alliance="<span style=\"color:".$conf['color_alliance']['v']."\">F&uuml;r Allianzmitglied Reserviert</span>";
          }
          else
          {
              $for_alliance="";
          }
              
          // Prüft, ob die Rohstoffe ausreichen für das Angebot und färbt dementsprechend Preis
          $metal_class = "tbldata";
          $crystal_class = "tbldata";
          $plastic_class = "tbldata";
          $fuel_class = "tbldata";
          $food_class = "tbldata";
					if ($c->res->metal < $arr['ship_costs_metal'])
					{ 
						$metal_class = "tbldata2";
					}
					if ($c->res->crystal < $arr['ship_costs_crystal'])
					{ 
						$crystal_class = "tbldata2";
					}					
					if ($c->res->plastic < $arr['ship_costs_plastic'])
					{ 
						$plastic_class = "tbldata2";
					}
					if ($c->res->fuel < $arr['ship_costs_fuel'])
					{ 
						$fuel_class = "tbldata2";
					}
					if ($c->res->food < $arr['ship_costs_food'])
					{ 
						$food_class = "tbldata2";
					}
					
					echo "<tr>
									<td class=\"tbldata\" rowspan=\"5\">
										".nf($arr['ship_count'])." <a href=\"?page=help&site=shipyard&id=".$arr['ship_id']."\">".$arr['ship_name']."</a>
									</td>
									<td class=\"tbldata\" rowspan=\"5\">
										<a href=\"?page=userinfo&amp;id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a>
									</td>
									<td class=\"tbldata\" rowspan=\"5\">
										".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['ship_text'])."
									</td>
									<td class=\"tbldata\"><b>".RES_METAL."</b>:</td>
									<td class=\"".$metal_class."\">".nf($arr['ship_costs_metal'])."</td>
									<td class=\"tbldata\" rowspan=\"5\">
										<input type=\"checkbox\" name=\"ship_market_id[]\" id=\"ship_market_id\" value=\"".$arr['ship_market_id']."\" onclick=\"xajax_calcMarketShipBuy(xajax.getFormValues('ship_buy_selector'));\"><br/><br/>".$for_alliance."
									</td>
								</tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td>
									<td class=\"".$crystal_class."\">".nf($arr['ship_costs_crystal'])."</td>
								</tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td>
									<td class=\"".$plastic_class."\">".nf($arr['ship_costs_plastic'])."</td></tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_FUEL."</b>:</td>
									<td class=\"".$fuel_class."\">".nf($arr['ship_costs_fuel'])."</td>
								</tr>
								<tr>
									<td class=\"tbldata\"><b>".RES_FOOD."</b>:</td>
									<td class=\"".$food_class."\">".nf($arr['ship_costs_food'])."</td>
								</tr>";
					$cnt++;
					// Setzt Lücke zwischen den Angeboten
					if ($cnt<mysql_num_rows($res))
					{
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\" style=\"height:10px;background:#000\">&nbsp;</td>
									</tr>";
					}

				}
				infobox_end(1);
				
				infobox_start("",1);
				echo "<tr>
								<td class=\"tbldata\" colspan=\"7\" id=\"ship_buy_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
							</tr>
							<tr>
								<td class=\"tbldata\" colspan=\"7\" style=\"text-align:center;vertical-align:middle;\">
									<input type=\"submit\" class=\"button\" name=\"ship_submit\" id=\"ship_submit\" value=\"Angebot annehmen\" disabled=\"disabled\"/>
								</td>
							</tr>";
				infobox_end(1);
			}
			else
			{
				echo "Keine Angebote vorhanden!";
			}			
			echo "</form>";
		}



		//
		// Auktionen Anzeigen (von der Suche oder dem Zurück-Button)
		//
		elseif(($_POST['search_submit']!="" && $_POST['search_cat']=="auction" || $_POST['auction_back']==1) && checker_verify())
		{
			echo "<h2>Auktionen</h2><br/>";

			$res = dbquery("	
			SELECT
				*
			FROM
				".$db_table['market_auction']."
			WHERE
				auction_user_id!='".$s['user']['id']."'
        ".stripslashes($_POST['auction_sql_add'])."
      ORDER BY
				auction_end ASC;");							
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=".$page."&amp;mode=auction\" method=\"post\" name=\"auctionListFormular\" id=\"auction_show_selector\">\n";
				checker_init();
				
				// Auktion ID (Wird beim Klick definiert)
				echo "<input type=\"hidden\" value=\"0\" id=\"auction_market_id\" name=\"auction_market_id\"/>";
				
				// SQL weitergeben (Für "Zurück" auf diese Page)
				echo "<input type=\"hidden\" value=\"".stripslashes($_POST['auction_sql_add'])."\" id=\"auction_sql_add\" name=\"auction_sql_add\"/>";
				
				infobox_start("Angebots&uuml;bersicht",1);
				infobox_end(1,1);
				$cnt=0;
				$acnts=array();
				$acnt=0;
				while ($arr=mysql_fetch_array($res))
				{
						$acnt++;
						
						//restliche zeit bis zum ende
						$rest_time=$arr['auction_end']-time();
						
            $t = floor($rest_time / 3600 / 24);
            $h = floor(($rest_time-($t*24*3600)) / 3600);
            $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
            $sec = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));
						
						if($rest_time<=3600)
						{
							$class = "class=\"tbldata2\"";
						}
						else
						{
							$class = "class=\"tbldata\"";
						}
						$rest_time = "Noch ".$t."t ".$h."h ".$m."m ".$sec."s";
						$acnts['countdown'.$acnt]=$arr['auction_end']-time();


						// Löschdatum anzeigen wenn dieses schon festgelegt ist
						if($arr['auction_delete_date']!=0)
						{
							$delete_rest_time = $arr['auction_delete_date']-time();
							
              $t = floor($delete_rest_time / 3600 / 24);
              $h = floor(($delete_rest_time) / 3600);
              $m = floor(($delete_rest_time-($h*3600))/60);
              $sec = floor(($delete_rest_time-($h*3600)-($m*60)));	
              
              $delete_header = "Löschung";
              $delete_time = "Gebot wird nach ".$h." Stunden und ".$m." Minuten gel&ouml;scht";
              $rest_time = "AUKTION BEENDET";
						}
						else
						{
							$delete_header = "&nbsp;";
							$delete_time = "&nbsp;";
							$delete_rest_time = "&nbsp;";
						}
							
						// Höchstbietender anzeigen wenn vorhanden
						if($arr['auction_current_buyer_id']!=0)
						{
							$buyer = "<a href=\"?page=userinfo&amp;id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a>";
						}		
						else
						{
							$buyer = "&nbsp;";
						}					
							
						// Formatiert "Höchstgebots-Zellen". Zeigt, mit welchen Rohstoffen geboten werden kann
						if($arr['auction_currency_metal']==1)
						{
							$auction_buy_metal_class = "tbldata";
							$auction_buy_metal = nf($arr['auction_buy_metal']);
						}
						else
						{
							$auction_buy_metal_class = "tbltitle";
							$auction_buy_metal = "-";
						}
						
						if($arr['auction_currency_crystal']==1)
						{
							$auction_buy_crystal_class = "tbldata";
							$auction_buy_crystal = nf($arr['auction_buy_crystal']);
						}
						else
						{
							$auction_buy_crystal_class = "tbltitle";
							$auction_buy_crystal = "-";
						}	
						
						if($arr['auction_currency_plastic']==1)
						{
							$auction_buy_plastic_class = "tbldata";
							$auction_buy_plastic = nf($arr['auction_buy_plastic']);
						}
						else
						{
							$auction_buy_plastic_class = "tbltitle";
							$auction_buy_plastic = "-";
						}		
						
						if($arr['auction_currency_fuel']==1)
						{
							$auction_buy_fuel_class = "tbldata";
							$auction_buy_fuel = nf($arr['auction_buy_fuel']);
						}
						else
						{
							$auction_buy_fuel_class = "tbltitle";
							$auction_buy_fuel = "-";
						}	
						
						if($arr['auction_currency_food']==1)
						{
							$auction_buy_food_class = "tbldata";
							$auction_buy_food = nf($arr['auction_buy_food']);
						}
						else
						{
							$auction_buy_food_class = "tbltitle";
							$auction_buy_food = "-";
						}				
											
							
						infobox_start("",1);	
						
						// Header
						echo "<tr>
		                <td class=\"tbltitle\" colspan=\"2\">Info</td>
		                <td class=\"tbltitle\">Rohstoff</td>
		                <td class=\"tbltitle\">Angebot</td>
		                <td class=\"tbltitle\">Höchstgebot</td>
		              </tr>";
			             
						echo "<tr>
										<td class=\"tbltitle\" style=\"width:20%;vertical-align:middle;\">Anbieter</td>
										<td class=\"tbldata\" style=\"width:25%;vertical-align:middle;\">
											<a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a>
										</td>
										<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">".RES_METAL.":</td>
										<td class=\"tbldata\" id=\"auction_sell_metal_field\" style=\"width:20%;vertical-align:middle;\">
											".nf($arr['auction_sell_metal'])."
										</td>										
											<td class=\"".$auction_buy_metal_class."\" id=\"auction_buy_metal_field\" style=\"width:20%;vertical-align:middle;\">
											".$auction_buy_metal."
										</td>			
									</tr>
									<tr>
										<td class=\"tbltitle\">Start</td>
										<td class=\"tbldata\">
											".date("d.m.Y  G:i:s", $arr['auction_start'])."
										</td>
										<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>
										<td class=\"tbldata\" id=\"auction_sell_crystal_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_crystal'])."
										</td>										
											<td class=\"".$auction_buy_crystal_class."\" id=\"auction_buy_crystal_field\" style=\"vertical-align:middle;\">
											".$auction_buy_crystal."
										</td>
									</tr>	
									<tr>									
										<td class=\"tbltitle\">Ende</td>
										<td class=\"tbldata\">
											".date("d.m.Y  G:i:s", $arr['auction_end'])."
										</td>
										<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>
										<td class=\"tbldata\" id=\"auction_sell_plastic_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_plastic'])."
										</td>										
											<td class=\"".$auction_buy_plastic_class."\" id=\"auction_buy_plastic_field\" style=\"vertical-align:middle;\">
											".$auction_buy_plastic."
										</td>	
									</tr>
									<tr>									
										<td class=\"tbltitle\">Dauer</td>
										<td class=\"tbldata\" ".$class." id=\"countdown".$acnt."\" >
											".$rest_time."
										</td>
										<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>
										<td class=\"tbldata\" id=\"auction_sell_fuel_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_fuel'])."
										</td>										
											<td class=\"".$auction_buy_fuel_class."\" id=\"auction_buy_fuel_field\" style=\"vertical-align:middle;\">
											".$auction_buy_fuel."
										</td>	
									</tr>
									<tr>									
										<td class=\"tbltitle\">Höchstbietender</td>
										<td class=\"tbldata\">
											".$buyer."
										</td>
										<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FOOD.":</td>
										<td class=\"tbldata\" id=\"auction_sell_food_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_food'])."
										</td>										
											<td class=\"".$auction_buy_food_class."\" id=\"auction_buy_food_field\" style=\"vertical-align:middle;\">
											".$auction_buy_food."
										</td>	
									</tr>";
									// Werbetext anzeigen falls vorhanden
									if($arr['auction_text']!="")
									{
										echo "<tr>
														<td class=\"tbldata\" colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">
															".stripslashes($arr['auction_text'])."
														</td>
													</tr>";
									}
									echo "<tr>
										<td class=\"tbldata\" colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">";
										// Bietbutton anzeigen wenn das Angebot noch steht
										if($arr['auction_buyable']==1)
										{
											// Der Höchstbietende kann nicht sein eigenes Gebot überbieten
											if($arr['auction_current_buyer_id']==$s['user']['id'])
											{
												$disabled = "disabled=\"disabled\"";
											}
											else
											{
												$disabled = "";
											}
											// Submit Button. Er übergibt vor dem abschicken noch die benötigte ID
											echo "<input type=\"button\" class=\"button\" name=\"auction_show_submit\" id=\"auction_show_submit\" value=\"Bieten\" onclick=\"showAuction('auction_market_id', ".$arr['auction_market_id'].");\" ".$disabled."/>";
										}
										// Und sonst Löschzeit anzeigen
										else
										{
											echo $delete_time;
										}
							echo "</td>
									</tr>";
            infobox_end(1,1);
            echo "<br/><br/><br/>";				
				}

				echo "<script type=\"text/javascript\">";
				foreach ($acnts as $cfield=> $ctime)
				{
					echo "setCountdown('".$ctime."','".$cfield."');";
				}
				echo "</script>";
			}
			else
			{
				echo "Keine Auktionen vorhanden!";
			}
			echo "</form>\n";
		}



		//
		// Einzelne Auktion anzeigen (Bei einer Auktion bieten)
		//
		elseif($_POST['auction_market_id']!=0 && checker_verify())
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
							nv = '3,2,';
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
						document.getElementById('auction_submit').disabled=true;
					}
					document.getElementById(field).firstChild.nodeValue=nv;
					document.getElementById('auction_rest_time').value=cnt;
					cnt = cnt - 1;
					setTimeout("setCountdown('"+cnt+"','"+field+"')",1000);
				}
			</script>

			<?PHP			
			

			$cnt=0;
			$acnts=array();
			$acnt=0;
			
			$res=dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['market_auction']." 
			WHERE 
				auction_market_id='".intval($_POST['auction_market_id'])."'
				AND auction_user_id!='".$s['user']['id']."' ");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				
				echo "<form action=\"?page=".$page."&amp;mode=auction\" method=\"post\" name=\"auctionShowFormular\" id=\"auction_show_selector\">";
				$cstr=checker_init();
				
				// Übergibt Daten an XAJAX
				// Rohstoffe
        echo "<input type=\"hidden\" value=\"".$c->res->metal."\" name=\"res_metal\" />";
        echo "<input type=\"hidden\" value=\"".$c->res->crystal."\" name=\"res_crystal\" />";
        echo "<input type=\"hidden\" value=\"".$c->res->plastic."\" name=\"res_plastic\" />";
        echo "<input type=\"hidden\" value=\"".$c->res->fuel."\" name=\"res_fuel\" />";
        echo "<input type=\"hidden\" value=\"".$c->res->food."\" name=\"res_food\" />";					
				
				// Angebot
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_metal']."\" name=\"auction_sell_metal\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_crystal']."\" name=\"auction_sell_crystal\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_plastic']."\" name=\"auction_sell_plastic\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_fuel']."\" name=\"auction_sell_fuel\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_food']."\" name=\"auction_sell_food\" />";
        		
        // Höchstgebot
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_metal']."\" name=\"auction_buy_metal\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_crystal']."\" name=\"auction_buy_crystal\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_plastic']."\" name=\"auction_buy_plastic\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_fuel']."\" name=\"auction_buy_fuel\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_food']."\" name=\"auction_buy_food\" />";	
        
        // Gewünschte Währung	
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_metal']."\" name=\"auction_currency_metal\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_crystal']."\" name=\"auction_currency_crystal\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_plastic']."\" name=\"auction_currency_plastic\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_fuel']."\" name=\"auction_currency_fuel\" />";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_food']."\" name=\"auction_currency_food\" />";
                  		
        // Zeit
        echo "<input type=\"hidden\" value=\"0\" name=\"auction_rest_time\" id=\"auction_rest_time\"/>";	
        
        // Angebot ID	
        echo "<input type=\"hidden\" value=\"".$arr['auction_market_id']."\" name=\"auction_market_id\" id=\"auction_market_id\"/>";	
        
        // SQL
        echo "<input type=\"hidden\" value=\"".stripslashes($_POST['auction_sql_add'])."\" id=\"auction_sql_add\" name=\"auction_sql_add\"/>";
        					
				//Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
        echo "<input type=\"hidden\" value=\"0\" name=\"auction_show_check_submit\" id=\"auction_show_check_submit\"/>";
        echo "<input type=\"hidden\" value=\"0\" name=\"auction_show_last_update\" id=\"auction_show_last_update\"/>"; 
        
        // Wird gewechselt wenn man den "Zurückbutton" benutzt
        echo "<input type=\"hidden\" value=\"0\" name=\"auction_back\" id=\"auction_back\"/>"; 
				
				//restliche zeit bis zum ende
				$rest_time=$arr['auction_end']-time();
				
        $t = floor($rest_time / 3600 / 24);
        $h = floor(($rest_time-($t*24*3600)) / 3600);
        $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
        $sec = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));
				
				if($rest_time<=3600)
				{
					$class = "class=\"tbldata2\"";
				}
				else
				{
					$class = "class=\"tbldata\"";
				}
				
				$rest_time = "Noch ".$t."t ".$h."h ".$m."m ".$sec."s";
				$acnts['countdown'.$acnt]=$arr['auction_end']-time();
					
				// Höchstbietender anzeigen wenn vorhanden
				if($arr['auction_current_buyer_id']!=0)
				{
					$buyer = "<a href=\"?page=userinfo&amp;id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a>";
				}		
				else
				{
					$buyer = "&nbsp;";
				}							
							
				
				
				// Allgemeine Angebotsinfo
				infobox_start("Angebotsinfo",1,0);
				echo "<tr>
                <td class=\"tbltitle\">Anbieter</td>
								<td class=\"tbldata\">
									<a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a>
								</td>
							</tr>
							<tr>
                <td class=\"tbltitle\">Start</td>
								<td class=\"tbldata\">
									".date("d.m.Y  G:i:s", $arr['auction_start'])."
								</td>
							</tr>
							<tr>
                <td class=\"tbltitle\">Ende</td>
								<td class=\"tbldata\">
									".date("d.m.Y  G:i:s", $arr['auction_end'])."
								</td>
							</tr>
							<tr>
                <td class=\"tbltitle\">Dauer</td>";
							// Löschdatum anzeigen wenn dieses schon festgelegt ist und "Auktion beendet"
							if($arr['auction_delete_date']!=0)
							{
								$delete_rest_time = $arr['auction_delete_date']-time();
								
				        $t = floor($delete_rest_time / 3600 / 24);
				        $h = floor(($delete_rest_time) / 3600);
				        $m = floor(($delete_rest_time-($h*3600))/60);
				        $sec = floor(($delete_rest_time-($h*3600)-($m*60)));	
				        
				        echo "<td class=\"tbldata\">AUKTION BEENDET</td>
				        		</tr>
				        		<tr>
				        			<td class=\"tbltitle\">Löschung</td>
				        			<td class=\"tbldata\">In ".$h."h und ".$m."m</td>
				        		</tr>";
							}
							else
							{
								echo "<td ".$class." id=\"countdown".$acnt."\">".$rest_time."</td>";
							}		                
                
                
							echo "</tr>";
							
							// Höchstbietender anzeigen wenn vorhanden
							if($arr['auction_current_buyer_id']!=0)
							{
								echo "<tr>
				                <td class=\"tbltitle\">Höchstbietender</td>
												<td class=\"tbldata\">
													".$buyer."
												</td>
											</tr>
											<tr>
				                <td class=\"tbltitle\">Geboten am</td>
												<td class=\"tbldata\">
													".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."
												</td>
											</tr>";
							}
				infobox_end(1);
				
				echo "<script type=\"text/javascript\">";
				foreach ($acnts as $cfield=> $ctime)
				{
					echo "setCountdown('".$ctime."','".$cfield."');";
				}
				echo "</script>";		
				
				
				// Angebots/Preis Maske
				//Header
				infobox_start("",1);
				echo "<tr>
								<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Rohstoff</td>
								<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Angebot</td>
								<td class=\"tbltitle\" style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</td>
								<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Höchstgebot</td>
								<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Bieten</td>
								<td class=\"tbltitle\" style=\"width:35%;vertical-align:middle;\">Min./Max.</td>
							</tr>";
				// Titan
				echo "<tr>
								<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_METAL.":</td>
								<td class=\"tbldata\" id=\"auction_sell_metal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_metal'])."
								</td>		
								<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</td>	
								<td class=\"tbldata\" id=\"auction_buy_metal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_metal'])."
								</td>
								<td class=\"tbldata\" style=\"vertical-align:middle;\">";
								if($arr['auction_currency_metal']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_metal'])."\" name=\"auction_new_buy_metal\" id=\"auction_new_buy_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\"/>";
								}
								else
								{
									echo " - ";
								}										
					echo "</td>
								<td class=\"tbltitle\" id=\"auction_min_max_metal\" style=\"vertical-align:middle;\"> - </td>
							</tr>";
				// Silizium
				echo "<tr>
								<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>
								<td class=\"tbldata\" id=\"auction_sell_crystal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_crystal'])."
								</td>		
								<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</td>	<td class=\"tbldata\" id=\"auction_buy_crystal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_crystal'])."
								</td>
								<td class=\"tbldata\" style=\"vertical-align:middle;\">";
								if($arr['auction_currency_crystal']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_crystal'])."\" name=\"auction_new_buy_crystal\" id=\"auction_new_buy_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\"/>";
								}
								else
								{
									echo " - ";
								}										
					echo "</td>
								<td class=\"tbltitle\" id=\"auction_min_max_crystal\" style=\"vertical-align:middle;\"> - </td>
							</tr>";	
				// PVC
				echo "<tr>
								<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>
								<td class=\"tbldata\" id=\"auction_sell_plastic_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_plastic'])."
								</td>		
								<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</td>	<td class=\"tbldata\" id=\"auction_buy_plastic_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_plastic'])."
								</td>
								<td class=\"tbldata\" style=\"vertical-align:middle;\">";
								if($arr['auction_currency_plastic']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_plastic'])."\" name=\"auction_new_buy_plastic\" id=\"auction_new_buy_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\"/>";
								}
								else
								{
									echo " - ";
								}										
					echo "</td>
								<td class=\"tbltitle\" id=\"auction_min_max_plastic\" style=\"vertical-align:middle;\"> - </td>
							</tr>";		
				// Tritium
				echo "<tr>
								<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>
								<td class=\"tbldata\" id=\"auction_sell_fuel_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_fuel'])."
								</td>		
								<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</td>	<td class=\"tbldata\" id=\"auction_buy_fuel_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_fuel'])."
								</td>
								<td class=\"tbldata\" style=\"vertical-align:middle;\">";
								if($arr['auction_currency_fuel']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_fuel'])."\" name=\"auction_new_buy_fuel\" id=\"auction_new_buy_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\"/>";
								}
								else
								{
									echo " - ";
								}										
					echo "</td>
								<td class=\"tbltitle\" id=\"auction_min_max_fuel\" style=\"vertical-align:middle;\"> - </td>
							</tr>";	
				// Nahrung
				echo "<tr>
								<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FOOD.":</td>
								<td class=\"tbldata\" id=\"auction_sell_food_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_food'])."
								</td>		
								<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</td>	<td class=\"tbldata\" id=\"auction_buy_food_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_food'])."
								</td>
								<td class=\"tbldata\" style=\"vertical-align:middle;\">";
								if($arr['auction_currency_food']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_food'])."\" name=\"auction_new_buy_food\" id=\"auction_new_buy_food\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\"/>";
								}
								else
								{
									echo " - ";
								}										
					echo "</td>
								<td class=\"tbltitle\" id=\"auction_min_max_food\" style=\"vertical-align:middle;\"> - </td>
							</tr>";	
							
				// Status Nachricht (Ajax Überprüfungstext)
				echo "<tr>
								<td class=\"tbldata\" colspan=\"6\" id=\"auction_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
							</tr>";		
				infobox_end(1,1);
				
				echo "<br/><br/><input type=\"button\" class=\"button\" name=\"auction_submit\" id=\"auction_submit\" value=\"Bieten\" disabled=\"disabled\" onclick=\"checkUpdate('auctionShowFormular', 'auction_show_last_update','auction_show_check_submit');xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'),1);\"/><br/><br/><input type=\"button\" class=\"button\" name=\"auction_back_submit\" id=\"auction_back_submit\" value=\"Zurück\" onclick=\"auctionBack();\" />";
				echo "</form>";					
			}
			else
			{
				echo "Angebot nicht mehr vorhanden!<br/>";
			}
			
		}



		//
		// Suchmaske
		//
		elseif ($_GET['mode']=="search")
		{
			echo "<form action=\"?page=".$page."\" method=\"post\" id=\"search_selector\">\n";
			checker_init();
			
			// Lädt Anzahl Angebote
			
			// Rohstoffe
			$ressource_res = dbquery("
			SELECT
				ressource_market_id
			FROM
				".$db_table['market_ressource']."
			WHERE
				ressource_buyable='1'
        AND user_id!='".$s['user']['id']."'
        AND (ressource_for_alliance='0' OR ressource_for_alliance='".$s['user']['alliance_id']."');");
        		
			// Schiffe
			$ship_res = dbquery("
			SELECT
				ship_market_id
			FROM
				".$db_table['market_ship']."
			WHERE
				ship_buyable='1'
				AND user_id!='".$s['user']['id']."'
				AND (ship_for_alliance='0' OR ship_for_alliance='".$s['user']['alliance_id']."');");									
			
			// Auktionen
			$auction_res = dbquery("
			SELECT
				auction_market_id
			FROM
				".$db_table['market_auction']."
			WHERE
				auction_user_id!='".$s['user']['id']."';");				
			
			
			// Lädt Schiffliste
      $sres=dbquery("
      SELECT
        ship_id,
        ship_name                           
      FROM
         ".$db_table['ships']."
			WHERE
				ship_buildable='1'
				AND ship_show='1'
				AND special_ship='0'
			ORDER BY 
				ship_name;");
      $ships=array();
      while ($sarr=mysql_fetch_array($sres))
      {
          $ships[$sarr['ship_id']]=$sarr;
      }

			// Übergibt Schiffsdaten dem xajax Tool
			$_SESSION['market']['ship_list']=$ships;			
			
			
			// Übergibt Daten an XAJAX
			// Aktuelle Rohstoffe vom Planeten
      echo "<input type=\"hidden\" value=\"".$c->res->metal."\" name=\"res_metal\" />";
      echo "<input type=\"hidden\" value=\"".$c->res->crystal."\" name=\"res_crystal\" />";
      echo "<input type=\"hidden\" value=\"".$c->res->plastic."\" name=\"res_plastic\" />";
      echo "<input type=\"hidden\" value=\"".$c->res->fuel."\" name=\"res_fuel\" />";
      echo "<input type=\"hidden\" value=\"".$c->res->food."\" name=\"res_food\" />";		
      
      // Anzahl Gebote pro Kategorie		
			echo "<input type=\"hidden\" value=\"".mysql_num_rows($ressource_res)."\" name=\"ressource_cnt\" />";	
			echo "<input type=\"hidden\" value=\"".mysql_num_rows($ship_res)."\" name=\"ship_cnt\" />";	
			echo "<input type=\"hidden\" value=\"".mysql_num_rows($auction_res)."\" name=\"auction_cnt\" />";	
			
			// XAJAX übergibt SQL-String an Fomular
			echo "<input type=\"hidden\" value=\"\" id=\"ressource_sql_add\" name=\"ressource_sql_add\"/>";		
			echo "<input type=\"hidden\" value=\"\" id=\"ship_sql_add\" name=\"ship_sql_add\"/>";	
			echo "<input type=\"hidden\" value=\"\" id=\"auction_sql_add\" name=\"auction_sql_add\"/>";
			
			infobox_start("Suche");
			
			// Kategorie
			echo "<div id=\"search_cat_field\" style=\"text-align:center;vertical-align:middle;height:30px;\">
							Kategorie:
							<select id=\"search_cat\" name=\"search_cat\" onchange=\"xajax_MarketSearchFormularShow(xajax.getFormValues('search_selector'));\">
								<option value=\"0\">keine</option>";
								if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
								{
									echo "<option value=\"ressource\">Rohstoffe (".mysql_num_rows($ressource_res).")</option>";
								}
								if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
								{
									echo "<option value=\"ship\">Schiffe (".mysql_num_rows($ship_res).")</option>";
								}
								if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
								{
									echo "<option value=\"auction\">Auktionen (".mysql_num_rows($auction_res).")</option>";
								}
					echo "</select>
						</div>";
			infobox_end(0,1);
			
			// Content
			infobox_start("");
			echo "<div id=\"search_content\">
							&nbsp;
						</div>";
			infobox_end(0,1);	
			
			// Check Message
			infobox_start("");
			echo "<div id=\"search_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">
							<div style=\"color:red;font-weight:bold;\">Wähle eine Kategorie!</div>
						</div>";
			infobox_end(0,1);
			
			// Sumbit
			infobox_start("");
			echo "<input type=\"submit\" class=\"button\" name=\"search_submit\" id=\"search_submit\" value=\"Angebote anzeigen\" disabled=\"disabled\"/>";
			infobox_end(0,1);
														

			echo "</form>";
		}	



		//
		// Eigene Angebote anzeigen
		//
		elseif ($_GET['mode']=="user_sell")
		{
			$return_factor = 1 - (1/(MARKET_LEVEL+1));

			// Schiffangebot löschen
			if ($_POST['ship_cancel']!="")
			{
				$scres=dbquery("
				SELECT
				 	* 
				FROM 
					".$db_table['market_ship']." 
				WHERE 
					ship_market_id='".$_POST['ship_market_id']."' 
					AND user_id='".$s['user']['id']."'");
					
				if (mysql_num_rows($scres)>0)
				{
					$scrow=mysql_fetch_array($scres);
					dbquery("
					UPDATE 
						".$db_table['shiplist']." 
					SET 
						shiplist_count=shiplist_count+'".(floor($scrow['ship_count']*$return_factor))."' 
					WHERE 
						shiplist_user_id='".$scrow['user_id']."' 
						AND shiplist_planet_id='".$scrow['planet_id']."' 
						AND shiplist_ship_id='".$scrow['ship_id']."'");
						
					dbquery("
					DELETE FROM 
						".$db_table['market_ship']." 
					WHERE 
						ship_market_id='".$_POST['ship_market_id']."'");
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
				$rcres=dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['market_ressource']." 
				WHERE 
					ressource_market_id='".$_POST['ressource_market_id']."' 
					AND user_id='".$s['user']['id']."'");
					
				if (mysql_num_rows($rcres)>0)
				{
					$rcrow=mysql_fetch_array($rcres);
					dbquery("
					UPDATE 
						".$db_table['planets']." 
					SET 
						planet_res_metal=planet_res_metal+'".($rcrow['sell_metal']*$return_factor)."',
						planet_res_crystal=planet_res_crystal+'".($rcrow['sell_crystal']*$return_factor)."',
						planet_res_plastic=planet_res_plastic+'".($rcrow['sell_plastic']*$return_factor)."',
						planet_res_fuel=planet_res_fuel+'".($rcrow['sell_fuel']*$return_factor)."',
						planet_res_food=planet_res_food+'".($rcrow['sell_food']*$return_factor)."'
					WHERE 
						planet_user_id='".$rcrow['user_id']."' 
						AND planet_id='".$rcrow['planet_id']."'");
						
					dbquery("
					DELETE FROM 
						".$db_table['market_ressource']." 
					WHERE 
						ressource_market_id='".$_POST['ressource_market_id']."'");
						
					add_log(7,"Der Spieler ".$s['user']['nick']." zieht folgendes Rohstoffangebot zur&uuml;ck: \n\n".RES_METAL.": ".$rcrow['sell_metal']."\n".RES_CRYSTAL.": ".$rcrow['sell_crystal']."\n".RES_PLASTIC.": ".$rcrow['sell_plastic']."\n".RES_FUEL.": ".$rcrow['sell_fuel']."\n".RES_FOOD.": ".$rcrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());
					echo "Angebot wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Rohstoffe zur&uuml;ck erhalten!";
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
					auction_market_id='".$_POST['auction_market_id']."' 
					AND auction_user_id='".$s['user']['id']."'");
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

					//Auktion löschen
					dbquery("DELETE FROM ".$db_table['market_auction']." WHERE auction_market_id='".$_POST['auction_market_id']."'");

					add_log(7,"Der Spieler ".$s['user']['nick']." zieht folgende Auktion zur&uuml;ck:\nRohstoffe:\n".RES_METAL.": ".$acrow['sell_metal']."\n".RES_CRYSTAL.": ".$acrow['sell_crystal']."\n".RES_PLASTIC.": ".$acrow['sell_plastic']."\n".RES_FUEL.": ".$acrow['sell_fuel']."\n".RES_FOOD.": ".$acrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());

					echo "Auktion wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Waren zur&uuml;ck erhalten (es wird abgerundet)!";
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

				echo "Wenn du ein Angebot zur&uuml;ckziehst erh&auml;lst du ".(round($return_factor,2)*100)."% des Angebotes zur&uuml;ck (abgerundet).<br/><br/>";

				//
				// Rohstoffe
				//
				$res=dbquery("SELECT * FROM ".$db_table['market_ressource']." WHERE user_id='".$s['user']['id']."' AND ressource_buyable='1' ORDER BY datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					infobox_start("Rohstoffe",1);
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
						echo "<td class=\"tbldata\" rowspan=\"5\">".date("d.m.Y  G:i:s", $row['datum'])."<br/><br/>".stripslashes($row['ressource_text'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($row['buy_metal'])."</td>";
						echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"ressource_market_id\" value=\"".$row['ressource_market_id']."\"><br/><br/>".$for_alliance."</td></tr>";

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
					echo "</form><br/><br/>";
				}
				else
				{
					infobox_start("Rohstoffe");
					echo "Keine Angebote vorhanden!";
					infobox_end(0);
				}


				//
				// Schiffe
				//
				$res=dbquery("SELECT * FROM ".$db_table['market_ship']." WHERE user_id='".$s['user']['id']."' AND ship_buyable='1' ORDER BY datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					infobox_start("Schiffe",1);

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
						echo "<td class=\"tbldata\" rowspan=\"5\">".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['ship_text'])."</td>";
						echo "<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($arr['ship_costs_metal'])."</td>";
						echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"radio\" name=\"ship_market_id\" value=\"".$arr['ship_market_id']."\"><br/><br/>".$for_alliance."</td></tr>";

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
					echo "</form><br/><br/>";
				}
				else
				{
					infobox_start("Schiffe");
					echo "Keine Angebote vorhanden!";
					infobox_end(0);
				}

				//
				// Auktionen
				//
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

				$res=dbquery("SELECT * FROM ".$db_table['market_auction']." WHERE auction_user_id='".$s['user']['id']."' ORDER BY auction_buyable DESC, auction_end ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=".$page."&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					infobox_start("Auktionen",1);
					infobox_end(0);
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
						<td class=\"tbltitle\" colspan=\"2\">Angebot</td>
						<td class=\"tbltitle\">Zur&uuml;ckziehen</td></tr>";

						//restliche zeit bis zum ende
						$rest_time=$arr['auction_end']-time();

						
            $t = floor($rest_time / 3600 / 24);
            $h = floor(($rest_time-($t*24*3600)) / 3600);
            $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
            $sec = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));
						

						if($rest_time<=3600)
						{
							$class = "class=\"tbldata2\"";
							 $rest_time = "Noch ".$m." m ".$sec." s";
						}
						else
						{
							$class = "class=\"tbldata\"";
							$rest_time = "Noch ".$t."t ".$h."h ".$m."m ".$sec."s";
						}


						echo "<tr>
										<td class=\"tbldata\" rowspan=\"5\">
											<a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a>
										</td>
										<td class=\"tbldata\">
											Start ".date("d.m.Y  G:i:s", $arr['auction_start'])."
										</td>
										<td class=\"tbldata\"><b>".RES_METAL."</b>:</td>
										<td class=\"tbldata\">".nf($arr['auction_sell_metal'])."</td>";

						 // Zurückzieh button wenn noch niemand geboten hat
						if($arr['auction_current_buyer_id']==0)
						{
               echo "<td class=\"tbldata\" rowspan=\"5\">
               				<input type=\"radio\" name=\"auction_market_id\" value=\"".$arr['auction_market_id']."\">
               			</td>
               		</tr>";
            }
            elseif($arr['auction_buyable']==0)
            {
            	echo "<td class=\"tbldata2\" rowspan=\"5\">Verkauft!</td>
            		</tr>";
            }
            else
            {
            	 echo "<td class=\"tbldata\" rowspan=\"5\">Es wurde bereits geboten</td>
            	 </tr>";
            }


						// Start/Ende Anzeigen sofern die auktion nicht schon beendet ist
						if($arr['auction_delete_date']==0)
						{
							$acnts['countdown'.$acnt]=$arr['auction_end']-time();
							echo "<tr><td class=\"tbldata\">Ende ".date("d.m.Y  G:i:s", $arr['auction_end'])."</td><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td $class rowspan=\"3\" id=\"countdown".$acnt."\">".$rest_time."</td><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}
						// sonst das löschdatum anzeigen
						else
						{
							$delete_rest_time = $arr['auction_delete_date']-time();

              $t = floor($delete_rest_time / 3600 / 24);
              $h = floor(($delete_rest_time) / 3600);
              $m = floor(($delete_rest_time-($h*3600))/60);
              $sec = floor(($delete_rest_time-($h*3600)-($m*60)));

							echo "<tr><td class=\"tbldata\">Auktion beendet</td><td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td class=\"tbldata\" rowspan=\"3\">Gebot wird nach ".$h." Stunden und ".$m." Minuten gel&ouml;scht</td><td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}


						echo "<tr><td class=\"tbldata\"><b>".RES_FUEL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_fuel'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".RES_FOOD."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_food'])."</td></tr>";

						//Hochstgebot Anzeigen wenn schon geboten worden ist
						if($arr['auction_current_buyer_id']!=0)
						{
              echo "<tr>
              				<td class=\"tbltitle\" colspan=\"5\">H&ouml;chstgebot</td>
              			</tr>
              			<tr>
              				<td class=\"tbldata\" rowspan=\"5\">
              					<a href=\"?page=userinfo&amp;id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a>
              				</td>
              				<td class=\"tbldata\" rowspan=\"5\">Geboten ".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."</td>
              				<td class=\"tbldata\"><b>".RES_METAL."</b>:</td><td class=\"tbldata\">".nf($arr['auction_buy_metal'])."</td>";

							// meldung geben, falls der bietende, das maximum erreicht hat
              if($arr['auction_buyable']==1)
              {
                  echo "<td class=\"tbldata2\" rowspan=\"5\">&nbsp;</td></tr>";
              }
              else
              {
                   echo "<td class=\"tbldata2\" rowspan=\"5\">&nbsp;</td></tr>";
              }

              echo "<tr>
              				<td class=\"tbldata\"><b>".RES_CRYSTAL."</b>:</td>
              				<td class=\"tbldata\">".nf($arr['auction_buy_crystal'])."</td>
              			</tr>
              			<tr>
              				<td class=\"tbldata\"><b>".RES_PLASTIC."</b>:</td>
              				<td class=\"tbldata\">".nf($arr['auction_buy_plastic'])."</td>
              			</tr>
              			<tr>
              				<td class=\"tbldata\"><b>".RES_FUEL."</b>:</td>
              				<td class=\"tbldata\">".nf($arr['auction_buy_fuel'])."</td>
              			</tr>
              			<tr>
              				<td class=\"tbldata\"><b>".RES_FOOD."</b>:</td>
              				<td class=\"tbldata\">".nf($arr['auction_buy_food'])."</td>
              			</tr>";
            }
            infobox_end(1);
            echo "<br/><br/>";
            
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
					echo "</form><br/>";
				}
				else
				{
					infobox_start("Auktionen");
					echo "Keine Angebote vorhanden!";
					infobox_end(0);
				}
			}
		}



		//
		// Angebote aufgeben
		//
		else
		{
			// Anzahl momentaner Angebote und wie viele der User noch einstellen kann
			$sares=dbquery("SELECT ship_market_id FROM ".$db_table['market_ship']." WHERE user_id='".$s['user']['id']."' AND planet_id='".$c->id."'");
			$rares=dbquery("SELECT ressource_market_id FROM ".$db_table['market_ressource']." WHERE user_id='".$s['user']['id']."' AND planet_id='".$c->id."'");
			$aares=dbquery("SELECT auction_market_id FROM ".$db_table['market_auction']." WHERE auction_user_id='".$s['user']['id']."' AND auction_planet_id='".$c->id."'");
			$anzahl=mysql_num_rows($sares)+mysql_num_rows($rares)+mysql_num_rows($aares);
			$possible=MARKET_LEVEL-$anzahl;
			echo "Im Moment hast du ".$anzahl." Angebote von diesem Planet auf dem Markt<br/>";
			echo "Du kannst noch ".$possible." Angebote einstellen<br/>";
			echo "Der Verkaufsgeb&uuml;hr des Marktplatzes betr&auml;gt ".round(((MARKET_SELL_TAX-1)*100),3)."%<br/><br/>";
			

			// Angebotsmaske Darstellen falls noch Angebote aufgegeben werden können
			if ($possible>0)
			{
				//
				// Rohstoffe
				//
				if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
				{		
          //Hier wird das ganze für die Rohstoffe noch angezeigt
          echo "<form action=\"?page=".$page."\" method=\"post\" name=\"ressFormular\" id=\"ress_selector\">\n";
          $cstr=checker_init();         
          
          infobox_start("Rohstoffe verkaufen",1);
          

					//Header
					echo "<tr>
									<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Rohstoff";
									

          //Roshtoff übergabe an xajax (da die $c-variabeln nicht abgerufen werden könnne)
          echo "<input type=\"hidden\" value=\"".$c->res->metal."\" name=\"res_metal\" id=\"res_metal\"/>";
          echo "<input type=\"hidden\" value=\"".$c->res->crystal."\" name=\"res_crystal\" id=\"res_crystal\"/>";
          echo "<input type=\"hidden\" value=\"".$c->res->plastic."\" name=\"res_plastic\" id=\"res_plastic\"/>";
          echo "<input type=\"hidden\" value=\"".$c->res->fuel."\" name=\"res_fuel\" id=\"res_fuel\"/>";
          echo "<input type=\"hidden\" value=\"".$c->res->food."\" name=\"res_food\" id=\"res_food\" />";
          
          //Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
          echo "<input type=\"hidden\" value=\"0\" name=\"ress_check_submit\" id=\"ress_check_submit\"/>";
          echo "<input type=\"hidden\" value=\"0\" name=\"ress_last_update\" id=\"ress_last_update\"/>";


									
									
									echo "</td>
									<td class=\"tbltitle\" style=\"width:10%;vertical-align:middle;\">Angebot</td>
									<td class=\"tbltitle\" style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</td>
									<td class=\"tbltitle\" style=\"width:10%;vertical-align:middle;\">Preis</td>
									<td class=\"tbltitle\" style=\"width:40%;vertical-align:middle;\">Min./Max.</td>
								</tr>";
					// Titan
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_METAL.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_metal\" id=\"ress_sell_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</td>
									<td class=\"tbldata\" id=\"ress_buy_metal_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_metal\" id=\"ress_buy_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\" disabled=\"disabled\"/>
									</td>
									<td class=\"tbltitle\" id=\"ress_min_max_metal\" style=\"vertical-align:middle;\"> - </td>
								</tr>";
								
					// Silizium
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_crystal\" id=\"ress_sell_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</td>
									<td class=\"tbldata\" id=\"ress_buy_crystal_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_crystal\" id=\"ress_buy_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"  disabled=\"disabled\"/>
									</td>
									<td class=\"tbltitle\" id=\"ress_min_max_crystal\" style=\"vertical-align:middle;\"> - </td>
								</tr>";		
					// PVC
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_plastic\" id=\"ress_sell_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</td>
									<td class=\"tbldata\" id=\"ress_buy_plastic_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_plastic\" id=\"ress_buy_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\" disabled=\"disabled\"/>
									</td>
									<td class=\"tbltitle\" id=\"ress_min_max_plastic\" style=\"vertical-align:middle;\"> - </td>
								</tr>";	
					// Tritium
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_fuel\" id=\"ress_sell_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</td>
									<td class=\"tbldata\" id=\"ress_buy_fuel_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_fuel\" id=\"ress_buy_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\" disabled=\"disabled\"/>
									</td>
									<td class=\"tbltitle\" id=\"ress_min_max_fuel\" style=\"vertical-align:middle;\"> - </td>
								</tr>";
					// Nahrung
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FOOD.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_food\" id=\"ress_sell_food\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</td>
									<td class=\"tbldata\" id=\"ress_buy_food_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_food\" id=\"ress_buy_food\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\" disabled=\"disabled\"/>
									</td>
									<td class=\"tbltitle\" id=\"ress_min_max_food\" style=\"vertical-align:middle;\"> - </td>
								</tr>";		
								
          //Verkaufstext und für Allianzmitglied reservieren
          echo "<tr>
          				<td class=\"tbltitle\" colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">Beschreibung und Reservation</td>
          			</tr>
          			<tr>
          				<td class=\"tbldata\" colspan=\"4\" style=\"vertical-align:middle;\">
          					<input type=\"text\" value=\"\" name=\"ressource_text\" size=\"55\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));\"/>	
          				</td>";
          //Für allianzmitglied reservieren
          if($s['user']['alliance_id']!=0 && $s['user']['alliance_application']==0)
          {
            echo "<td class=\"tbldata\" colspan=\"1\" style=\"vertical-align:middle;\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen").">
            				<input type=\"checkbox\" name=\"ressource_for_alliance\" value=\"1\" /> F&uuml;r Allianzmitglieder Reservieren
            			</td>
            		</tr>";
          }
          else
          {
            echo "<td class=\"tbldata\" colspan=\"1\" style=\"vertical-align:middle;\">&nbsp;</td></tr>";
          }			
          					
					// Status Nachricht (Ajax Überprüfungstext)
					echo "<tr>
									<td class=\"tbldata\" colspan=\"5\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
								</tr>";								

          infobox_end(1);
          
          // Absend-Button (Per Ajax freigegeben)
          echo "<input type=\"button\" class=\"button\" name=\"ressource_sell_submit\" id=\"ressource_sell_submit\" value=\"Angebot aufgeben\" style=\"color:#f00;\" disabled=\"disabled\" onclick=\"checkUpdate('ressFormular', 'ress_last_update','ress_check_submit');xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'),1);\"/></form><br/><br/>";
        }


				//
				// Schiffe
				//
				if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
				{
          //Zuerst wird überprüft ob auf dem Planeten Schiffe sind, auch ob diese dem User gehören
          if (mysql_num_rows(dbquery("SELECT shiplist_id FROM ".$db_table['shiplist']." WHERE shiplist_planet_id='".$c->id."'"))>0)
          {
          		// Lädt Daten von den vorhandenen Schiffen auf dem aktuellen Planeten
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
                  ".$db_table['shiplist']."
                  INNER JOIN
                  ".$db_table['ships']."
                  ON shiplist.shiplist_ship_id=ships.ship_id
                  AND shiplist.shiplist_planet_id='".$c->id."'
                  AND shiplist.shiplist_count>'0'
                  AND ships.special_ship='0'
              ORDER BY
                  ships.ship_name;");
              $ships=array();
              while ($sarr=mysql_fetch_array($sres))
              {
                  $ships[$sarr['ship_id']]=$sarr;
              }


		          echo "<form action=\"?page=".$page."\" method=\"post\" name=\"shipFormular\" id=\"ship_selector\">\n";
		          echo $cstr;

							//Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
          		echo "<input type=\"hidden\" value=\"0\" name=\"ship_check_submit\" id=\"ship_check_submit\"/>";
          		echo "<input type=\"hidden\" value=\"0\" name=\"ship_last_update\" id=\"ship_last_update\"/>";			

		          infobox_start("Schiffe verkaufen",1);
		
							// Übergibt Schiffsdaten dem xajax Tool
							$_SESSION['market']['ship_data']=$ships;
															
										
										
							// Header Angebot
							echo "<tr>
											<td class=\"tbldata\" height=\"30\" colspan=\"3\" style=\"vertical-align:middle;\">
												<select name=\"ship_list\" id=\"ship_list\" onchange=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'),1);\">";
												// Listet alle vorhandenen Schiffe auf
					              foreach ($ships as $sarr)
					              {
					                echo "<option value=\"".$sarr['ship_id']."\">".$sarr['ship_name']." (".$sarr['shiplist_count'].")</option>";
					              }
              		echo "</select>
              				</td>
              				<td class=\"tbldata\" height=\"30\" colspan=\"2\" style=\"vertical-align:middle;\">
              					<input type=\"text\" value=\"0\" name=\"ship_count\" id=\"ship_count\" size=\"5\" maxlength=\"7\" onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'),1);\" /> St&uuml;ck
              				</td>
              			</tr>";										
																
							
							//Header Preis
							echo "<tr>
											<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Rohstoff</td>
											<td class=\"tbltitle\" style=\"width:10%;vertical-align:middle;\">Angebot</td>
											<td class=\"tbltitle\" style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</td>
											<td class=\"tbltitle\" style=\"width:10%;vertical-align:middle;\">Preis</td>
											<td class=\"tbltitle\" style=\"width:40%;vertical-align:middle;\">Min./Max.</td>
										</tr>";
							// Titan
							echo "<tr>
											<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_METAL.":</td>
											<td class=\"tbldata\" id=\"ship_sell_metal_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_sell_metal\" id=\"ship_sell_metal\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
											</td>		
											<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</td>	<td class=\"tbldata\" id=\"ship_buy_metal_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_buy_metal\" id=\"ship_buy_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));\"/>
											</td>
											<td class=\"tbltitle\" id=\"ship_min_max_metal\" style=\"vertical-align:middle;\"> - </td>
										</tr>";
							// Silizium
							echo "<tr>
											<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>
											<td class=\"tbldata\" id=\"ship_sell_crystal_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_sell_crystal\" id=\"ship_sell_crystal\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
											</td>	
											<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</td>										
											<td class=\"tbldata\" id=\"ship_buy_crystal_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_buy_crystal\" id=\"ship_buy_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));\"/>
											</td>
											<td class=\"tbltitle\" id=\"ship_min_max_crystal\" style=\"vertical-align:middle;\"> - </td>
										</tr>";		
							// PVC
							echo "<tr>
											<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>
											<td class=\"tbldata\" id=\"ship_sell_plastic_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_sell_plastic\" id=\"ship_sell_plastic\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
											</td>	
											<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</td>											
											<td class=\"tbldata\" id=\"ship_buy_plastic_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_buy_plastic\" id=\"ship_buy_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));\"/>
											</td>
											<td class=\"tbltitle\" id=\"ship_min_max_plastic\" style=\"vertical-align:middle;\"> - </td>
										</tr>";	
							// Tritium
							echo "<tr>
											<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>
											<td class=\"tbldata\" id=\"ship_sell_fuel_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_sell_fuel\" id=\"ship_sell_fuel\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
											</td>	
											<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</td>		<td class=\"tbldata\" id=\"ship_buy_fuel_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_buy_fuel\" id=\"ship_buy_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));\"/>
											</td>
											<td class=\"tbltitle\" id=\"ship_min_max_fuel\" style=\"vertical-align:middle;\"> - </td>
										</tr>";
							// Nahrung
							echo "<tr>
											<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FOOD.":</td>
											<td class=\"tbldata\" id=\"ship_sell_food_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_sell_food\" id=\"ship_sell_food\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
											</td>		
											<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</td>		<td class=\"tbldata\" id=\"ship_buy_food_field\" style=\"vertical-align:middle;\">
												<input type=\"text\" value=\"0\" name=\"ship_buy_food\" id=\"ship_buy_food\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));\"/>
											</td>
											<td class=\"tbltitle\" id=\"ship_min_max_food\" style=\"vertical-align:middle;\"> - </td>
										</tr>";		
										
		          //Verkaufstext und für Allianzmitglied reservieren
		          echo "<tr>
		          				<td class=\"tbltitle\" colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">Beschreibung und Reservation</td>
		          			</tr>
		          			<tr>
		          				<td class=\"tbldata\" colspan=\"4\" style=\"vertical-align:middle;\">
		          					<input type=\"text\" value=\"\" name=\"ship_text\" size=\"55\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));\"/>	
		          				</td>";
		          //Für allianzmitglied reservieren
		          if($s['user']['alliance_id']!=0 && $s['user']['alliance_application']==0)
		          {
		            echo "<td class=\"tbldata\" colspan=\"1\" style=\"vertical-align:middle;\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen").">
		            				<input type=\"checkbox\" name=\"ship_for_alliance\" value=\"1\"/> F&uuml;r Allianzmitglieder Reservieren
		            			</td>
		            		</tr>";
		          }
		          else
		          {
		            echo "<td class=\"tbldata\" colspan=\"1\" style=\"vertical-align:middle;\">&nbsp;</td></tr>";
		          }			
		          					
							// Status Nachricht (Ajax Überprüfungstext)
							echo "<tr>
											<td class=\"tbldata\" colspan=\"5\" id=\"ship_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
										</tr>";								
		
		          infobox_end(1);
		          
		          // Absend-Button (Per Ajax freigegeben)
		          echo "<input type=\"button\" class=\"button\" name=\"ship_sell_submit\" id=\"ship_sell_submit\" value=\"Angebot aufgeben\" style=\"color:#f00;\" disabled=\"disabled\" onclick=\"checkUpdate('shipFormular', 'ship_last_update','ship_check_submit');xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'),0,1);\"/></form><br/><br/>";
 
          }
        }


				//
				// Auktionen
				//
				if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
				{
					
          echo "<form action=\"?page=".$page."\" method=\"post\" name=\"auctionFormular\" id=\"auction_selector\">\n";
          echo $cstr;
          infobox_start("Rohstoffe versteigern",1);
          
          // Frühstes Auktionsende
          $auction_time = time() + (AUCTION_MIN_DURATION*24*3600);
          
        

					//Header
					echo "<tr>
									<td class=\"tbltitle\" style=\"width:15%;vertical-align:middle;\">Rohstoff";
									
          // Min. Auktionsende an XAJAX weitergeben
          echo "<input type=\"hidden\" value=\"".$auction_time."\" name=\"auction_time_min\" />";
          
          //Roshtoff übergabe an XAJAX (da die $c-variabeln nicht abgerufen werden könnnen)
          echo "<input type=\"hidden\" value=\"".$c->res->metal."\" name=\"res_metal\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->crystal."\" name=\"res_crystal\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->plastic."\" name=\"res_plastic\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->fuel."\" name=\"res_fuel\" />";
          echo "<input type=\"hidden\" value=\"".$c->res->food."\" name=\"res_food\" />";
          
          //Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
          echo "<input type=\"hidden\" value=\"0\" name=\"auction_check_submit\" id=\"ress_check_submit\"/>";
          echo "<input type=\"hidden\" value=\"0\" name=\"auction_last_update\" id=\"ress_last_update\"/>";
									
									
									echo "</td>
									<td class=\"tbltitle\" style=\"width:10%;vertical-align:middle;\">Angebot</td>
									<td class=\"tbltitle\" style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</td>
									<td class=\"tbltitle\" style=\"width:5%;vertical-align:middle;\">Preis</td>
									<td class=\"tbltitle\" colspan=\"2\" style=\"width:45%;text-align:center;vertical-align:middle;\">Zeit</td>
								</tr>";
					// Titan
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_METAL.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_metal\" id=\"auction_sell_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</td>
									<td class=\"tbldata\" id=\"auction_buy_metal_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_metal\" value=\"1\" onclick=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\" checked=\"checked\"/>
									</td>
									<td class=\"tbltitle\" colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</td>
								</tr>";												
					// Silizium und "Dauer" Feld
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_CRYSTAL.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_crystal\" id=\"auction_sell_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</td>
									<td class=\"tbldata\" id=\"auction_buy_crystal_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_crystal\" value=\"1\" onclick=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\" checked=\"checked\"/>
									</td>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">Dauer:</td>
									<td class=\"tbldata\" id=\"auction_time_field\" style=\"vertical-align:middle;\">
										".AUCTION_MIN_DURATION." Tage + ";
				
					          //... in Tagen ...
					          echo "<select name=\"auction_time_days\" id=\"auction_time_days\" onchange=\"xajax_calcMarketAuctionTime(xajax.getFormValues('auction_selector'));\">";
					          for($x=0;$x<=10;$x++)
					          {
					                  echo "<option value=\"".$x."\">".$x."</option>";
					          }
					          echo "</select> Tage und ";
					
					          //... und in Stunden
					          echo "<select name=\"auction_time_hours\" id=\"auction_time_hours\" onchange=\"xajax_calcMarketAuctionTime(xajax.getFormValues('auction_selector'));\">";
					          for($x=0;$x<=24;$x++)
					          {
					                  echo "<option value=\"".$x."\">".$x."</option>";
					          }

					          echo "</select> Stunden";														
						echo "</td>					
								</tr>";										
					// PVC
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_PLASTIC.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_plastic\" id=\"auction_sell_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</td>
									<td class=\"tbldata\" id=\"auction_buy_plastic_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_plastic\" value=\"1\" onclick=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\" checked=\"checked\"/>
									</td>
									<td class=\"tbltitle\" colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</td>
								</tr>";	
					// Tritium und "Ende" Feld
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FUEL.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_fuel\" id=\"auction_sell_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</td>
									<td class=\"tbldata\" id=\"auction_buy_fuel_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_fuel\" value=\"1\" onclick=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\" checked=\"checked\"/>
									</td>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">Ende:</td>
									<td class=\"tbldata\" id=\"auction_end_time\" style=\"vertical-align:middle;\">".date("d.m.Y H:i",$auction_time)."</td>										
								</tr>";
					// Nahrung
					echo "<tr>
									<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_FOOD.":</td>
									<td class=\"tbldata\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_food\" id=\"auction_sell_food\" size=\"7\" maxlength=\"15\" onkeyup=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\"/>
									</td>
									<td class=\"tbltitle\" style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</td>
									<td class=\"tbldata\" id=\"auction_buy_food_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_food\" value=\"1\" onclick=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\" checked=\"checked\"/>
									</td>
									<td class=\"tbltitle\" colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</td>
								</tr>";	
          //Verkaufstext und für Allianzmitglied reservieren
          echo "<tr>
          				<td class=\"tbltitle\" colspan=\"6\" style=\"text-align:center;vertical-align:middle;\">Beschreibung</td>
          			</tr>
          			<tr>
          				<td class=\"tbldata\" colspan=\"6\" style=\"text-align:center;vertical-align:middle;\">
          					<input type=\"text\" value=\"\" name=\"auction_text\" size=\"100\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'));\"/>	
          				</td>
          			</tr>";												
					// Status Nachricht (Ajax Überprüfungstext)
					echo "<tr>
									<td class=\"tbldata\" colspan=\"6\" id=\"auction_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
								</tr>";		
														
          infobox_end(1);
          
          // Absend-Button (Per Ajax freigegeben)
          echo "<input type=\"button\" class=\"button\" name=\"auction_sell_submit\" id=\"auction_sell_submit\" value=\"Angebot aufgeben\" style=\"color:#f00;\" disabled=\"disabled\" onclick=\"checkUpdate('auctionFormular', 'auction_last_update','auction_check_submit');xajax_checkMarketAuctionFormular(xajax.getFormValues('auction_selector'),1);\"/></form><br/><br/>";								
												
				}
			}
		}


	//
	// Meldung dass noch kein Marktplatz gebaut wurde
	//
	}
	else
	{
		echo "Der Marktplatz wurde noch nicht gebaut.";
	}
		


?>
