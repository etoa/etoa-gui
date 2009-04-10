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
	// 	Last edited: 22.09.2009
	// 	Last edited by: glaubinix <glaubinix@etoa.ch>
	//
		
	/**
	* Ship- and Resource-Market
	*
	* @author Lamborghini <lamborghini@etoa.ch>
	* @copyright Copyright (c) 2004-2009 by EtoA Gaming, www.etoa.net
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
	define("MARKET_TAX", max(1,MARKET_SELL_TAX * $cu->specialist->tradeBonus));


	// BEGIN SKRIPT //

	echo '<h1>Marktplatz des Planeten '.$cp->name().'</h1>';
	//echo "<br/><div style=\"color:red;font-size:20pt;\">In bearbeitung!</div><br/><br/>";
	echo '<div id="marketinfo"></div>'; //nur zu entwicklungszwecken!

	$mode = isset($_GET['mode']) ? $_GET['mode'] : "";

	// Zeigt Rohstoffbox an
	$cp->resBox($cu->properties->smallResBox);
	
	//Überprüfung ob der Marktplatz schon gebaut wurde
	$mres=dbquery("
	SELECT 
		buildlist_current_level 
	FROM 
		buildlist 
	WHERE 
	buildlist_entity_id='".$cp->id()."'
		AND buildlist_building_id='".MARKTPLATZ_ID."' 
		AND buildlist_current_level>0 
		AND buildlist_user_id='".$cu->id."';");
	
	if (mysql_num_rows($mres)>0)
	{
		?>
			<script type="text/javascript">
				
				// Überprüfungsscript für alle Formulare ob XAJAX die Eingaben vor dem Senden nochmals geprüft hat
				//
				function checkUpdate(formName, updateField)
				{
					//if(document.getElementById(updateField).value == 1)
					//{
						if(document.getElementById(updateField).value == 1)
						{
							document.forms[formName].submit();
						}
						else
						{
							alert('Eingaben wurden noch nicht aktualisiert!');
						}
					//}
					//else
					//{
					//	setTimeout("checkUpdate('"+formName+"', '"+updateField+"', '"+checkField+"')",50);
					//}
					
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
				
				
				
				/*************************************************************************/
				/* Markt: Rohstoffverkauf Check/Kalkulator                              */
				/* Berechnet und überprüft die Korrektheit der Eingaben                  */
				/* last_update: wird auf 1 gesetzt, wenn der Submitbutton gedrückt wird  */
				/*************************************************************************/
				
				function calcMarketRessPrice(last_update)
				{
					// Setzt Zeichen, dass die Eingaben noch nicht definitiv geprüft wurden vor dem Absenden
					document.getElementById('ress_last_update').value=0;



					//
					// Definitionen
					//
					
					MARKET_METAL_FACTOR = <?php echo MARKET_METAL_FACTOR; ?>;
					MARKET_CRYSTAL_FACTOR = <?php echo MARKET_CRYSTAL_FACTOR; ?>;
					MARKET_PLASTIC_FACTOR = <?php echo MARKET_PLASTIC_FACTOR; ?>;
					MARKET_FUEL_FACTOR = <?php echo MARKET_FUEL_FACTOR; ?>;
					MARKET_FOOD_FACTOR = <?php echo MARKET_FOOD_FACTOR; ?>;
					RESS_PRICE_FACTOR_MAX = <?php echo RESS_PRICE_FACTOR_MAX; ?>;
					RESS_PRICE_FACTOR_MIN = <?php echo RESS_PRICE_FACTOR_MIN; ?>;
					MARKET_TAX = <?php echo MARKET_TAX; ?>;



					//
					// Eingaben in Variablen Speichern und eventuell auf Korrektheit prüfen
					//

					// Aktuelle Rohstoffe auf dem Planeten werden abgefragt
					res_metal = document.getElementById('res_metal').value;
					res_crystal = document.getElementById('res_crystal').value;
					res_plastic = document.getElementById('res_plastic').value;
					res_fuel = document.getElementById('res_fuel').value;
					res_food = document.getElementById('res_food').value;
					
					// Zu verkaufende Rohstoffe (Angebot) formatieren (Trennzeichen entfernen) und in Variable speichern
					ress_sell_metal = document.getElementById('ress_sell_metal').value.replace(/`/g, "");
					ress_sell_crystal = document.getElementById('ress_sell_crystal').value.replace(/`/g, "");
					ress_sell_plastic = document.getElementById('ress_sell_plastic').value.replace(/`/g, "");
					ress_sell_fuel = document.getElementById('ress_sell_fuel').value.replace(/`/g, "");
					ress_sell_food = document.getElementById('ress_sell_food').value.replace(/`/g, "");
					
					// Die verlangten Rohstoffe (Preis) formatieren (Trennzeichen entfernen) und in Variable speichern
					ress_buy_metal = document.getElementById('ress_buy_metal').value.replace(/`/g, "");
					ress_buy_crystal = document.getElementById('ress_buy_crystal').value.replace(/`/g, "");
					ress_buy_plastic = document.getElementById('ress_buy_plastic').value.replace(/`/g, "");
					ress_buy_fuel = document.getElementById('ress_buy_fuel').value.replace(/`/g, "");
					ress_buy_food = document.getElementById('ress_buy_food').value.replace(/`/g, "");
					
					
					
					// Stellt sicher, dass nur positive Zahlen verrechnet werden
					// 1. Prüft, ob Wert eine Zahl ist und nicht leer ist. Wenn nicht, wird 0 in das Feld geschrieben
					// 2. Erstellt den absolut Wert der Zahl
					
					//
					// Zu verkaufende Rohstoffe (Angebot)
					//
					
					// Titan
					if(istZahl(ress_sell_metal)==false || ress_sell_metal=='')
					{
						ress_sell_metal = 0;
						document.getElementById('ress_sell_metal').value=ress_sell_metal;
					}
					ress_sell_metal = Math.abs(ress_sell_metal);
					
					// Silizium
					if(istZahl(ress_sell_crystal)==false || ress_sell_crystal=='')
					{
						ress_sell_crystal = 0;
						document.getElementById('ress_sell_crystal').value=ress_sell_crystal;
					}
					ress_sell_crystal = Math.abs(ress_sell_crystal);
					
					// PVC
					if(istZahl(ress_sell_plastic)==false || ress_sell_plastic=='')
					{
						ress_sell_plastic = 0;
						document.getElementById('ress_sell_plastic').value=ress_sell_plastic;
					}
					ress_sell_plastic = Math.abs(ress_sell_plastic);
					
					// Tritium
					if(istZahl(ress_sell_fuel)==false || ress_sell_fuel=='')
					{
						ress_sell_fuel = 0;
						document.getElementById('ress_sell_fuel').value=ress_sell_fuel;
					}
					ress_sell_fuel = Math.abs(ress_sell_fuel);
					
					// Nahrung
					if(istZahl(ress_sell_food)==false || ress_sell_food=='')
					{
						ress_sell_food = 0;
						document.getElementById('ress_sell_food').value=ress_sell_food;
					}
					ress_sell_food = Math.abs(ress_sell_food);
			
			
			
					//
					// Die verlangten Rohstoffe (Preis)
					//
					
					// Titan
					if(istZahl(ress_buy_metal)==false || ress_buy_metal=='')
					{
						ress_buy_metal = 0;
						document.getElementById('ress_buy_metal').value=ress_buy_metal;
					}
					ress_buy_metal = Math.abs(ress_buy_metal);
					
					// Silizium
					if(istZahl(ress_buy_crystal)==false || ress_buy_crystal=='')
					{
						ress_buy_crystal = 0;
						document.getElementById('ress_buy_crystal').value=ress_buy_crystal;
					}
					ress_buy_crystal = Math.abs(ress_buy_crystal);
					
					// PVC
					if(istZahl(ress_buy_plastic)==false || ress_buy_plastic=='')
					{
						ress_buy_plastic = 0;
						document.getElementById('ress_buy_plastic').value=ress_buy_plastic;
					}
					ress_buy_plastic = Math.abs(ress_buy_plastic);
					
					// Tritium
					if(istZahl(ress_buy_fuel)==false || ress_buy_fuel=='')
					{
						ress_buy_fuel = 0;
						document.getElementById('ress_buy_fuel').value=ress_buy_fuel;
					}
					ress_buy_fuel = Math.abs(ress_buy_fuel);
					
					// Nahrung
					if(istZahl(ress_buy_food)==false || ress_buy_food=='')
					{
						ress_buy_food = 0;
						document.getElementById('ress_buy_food').value=ress_buy_food;
					}
					ress_buy_food = Math.abs(ress_buy_food);						

					
					
			  	//
			  	// Errechnet und formatiert Preise
			  	//					
									
					var ress_buy_max = new Array();
					var log_ress_buy_max = new Array();
					var ress_buy_min = new Array();
					var log_ress_buy_min = new Array();
					var out_ress_min_max = new Array();
					
					// Errechnet den Preis mithilfe einer Schleife, welche alle 5 Rohstoffe beachtet
					for(res=0;res<5;res++)
					{
						// Titan
						if(res==0)
						{
							var actuel_res_factor = <?php echo MARKET_METAL_FACTOR; ?>;
							var ress_sell = ress_sell_metal;
							var ress_buy = ress_buy_metal;
							var buy_field = "ress_buy_metal";
						}
						// Silizium
						else if(res==1)
						{
							var actuel_res_factor = <?php echo MARKET_CRYSTAL_FACTOR;?>;
							var ress_sell = ress_sell_crystal;
							var ress_buy = ress_buy_crystal;
							var buy_field = "ress_buy_crystal";
						}
						// PVC
						else if(res==2)
						{
							var actuel_res_factor = <?php echo MARKET_PLASTIC_FACTOR;?>;
							var ress_sell = ress_sell_plastic;
							var ress_buy = ress_buy_plastic;
							var buy_field = "ress_buy_plastic";
						}
						// Tritium
						else if(res==3)
						{
							var actuel_res_factor = <?php echo MARKET_FUEL_FACTOR;?>;
							var ress_sell = ress_sell_fuel;
							var ress_buy = ress_buy_fuel;
							var buy_field = "ress_buy_fuel";
						}
						// Nahrung
						else if(res==4)
						{
							var actuel_res_factor = <?php echo MARKET_FOOD_FACTOR;?>;
							var ress_sell = ress_sell_food;
							var ress_buy = ress_buy_food;
							var buy_field = "ress_buy_food";
						}
												
	
						
			  		// MaxBetrag
			  		// Errechnet Grundbetrag (Noch ohne Abzüge von eingegebenen Preisen)
			  		ress_buy_max[res] =	ress_sell_metal / actuel_res_factor * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MAX
					  										+ ress_sell_crystal / actuel_res_factor * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MAX
					  										+ ress_sell_plastic / actuel_res_factor * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MAX
					  										+ ress_sell_fuel / actuel_res_factor * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MAX
					  										+ ress_sell_food / actuel_res_factor * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MAX;
					  										
					  // Errechnet Grundbetrag abzüglich bereits eingebener Preise
					  ress_buy_max[res] =  ress_buy_max[res]
					  										-	ress_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
					  										- ress_buy_crystal * MARKET_CRYSTAL_FACTOR / actuel_res_factor
					  										- ress_buy_plastic * MARKET_PLASTIC_FACTOR / actuel_res_factor
					  										- ress_buy_fuel * MARKET_FUEL_FACTOR / actuel_res_factor
					  										- ress_buy_food * MARKET_FOOD_FACTOR / actuel_res_factor;
					  ress_buy_max[res] = Math.floor(ress_buy_max[res]);		// Der Anzeigewert & Prüfwert
					  	

			  		// MinBetrag
			  		// Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
			  		ress_buy_min[res] =	ress_sell_metal / actuel_res_factor * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MIN
					  										+ ress_sell_crystal / actuel_res_factor * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MIN
					  										+ ress_sell_plastic / actuel_res_factor * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MIN
					  										+ ress_sell_fuel / actuel_res_factor * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MIN
					  										+ ress_sell_food / actuel_res_factor * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MIN;
					  // Errechnet Grundbetrag abzüglich bereits eingebener Preise
					  ress_buy_min[res] =  ress_buy_min[res]
					  										-	ress_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
					  										- ress_buy_crystal * MARKET_CRYSTAL_FACTOR / actuel_res_factor
					  										- ress_buy_plastic * MARKET_PLASTIC_FACTOR / actuel_res_factor
					  										- ress_buy_fuel * MARKET_FUEL_FACTOR / actuel_res_factor
					  										- ress_buy_food * MARKET_FOOD_FACTOR / actuel_res_factor;	
					  ress_buy_min[res] = Math.ceil(ress_buy_min[res]);					// Der Anzeigewert & Prüfwert
					  	
					  
					  /*
					  if(ress_buy_max[res]<=0)
					  {
					  	ress_buy_max[res]=0;
					  }  									
			
					  if(ress_buy_min[res]<=0)
					  {
					  	ress_buy_min[res]=0;
					  } 
					  */
					  
					  // Gibt das Preisfeld frei, wenn vom gleichen Rohstoff nichts verlangt wird. Ansonsten wird das Feld gesperrt und 0 als Defaultwert hineingeschrieben
						if (ress_sell>0)
						{
							document.getElementById(buy_field).disabled=true;  
							document.getElementById(buy_field).value=0;
							out_ress_min_max[res] = "";
						}
						else
						{
							document.getElementById(buy_field).disabled=false;  
							
							// Definiert die Zahl, welche in das Preisfeld geschrieben wird nach dem Klick auf den Min/Max Link
							var sum_min = ress_buy + ress_buy_min[res];
							var sum_max = ress_buy + ress_buy_max[res];
							// Formatiert die Min/Max Zahl für die Ausgabe im Link (1000-er Striche)
							var ress_min = FormatNumber('return',ress_buy_min[res],'','','');
							var ress_max = FormatNumber('return',ress_buy_max[res],'','','');
							
							// Definiert die "Min./Max." Ausgabe
						  out_ress_min_max[res]="<a href=\"javascript:;\" onclick=\"document.getElementById('"+buy_field+"').value="+sum_min+";calcMarketRessPrice('0');FormatNumber('"+buy_field+"','"+sum_min+"','','','');\">"+ress_min+"</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('"+buy_field+"').value="+sum_max+";calcMarketRessPrice('0');FormatNumber('"+buy_field+"','"+sum_max+"','','','');\">"+ress_max+"</a>"; 
						}	  
					}
					
					
					
					//
			  	// End Prüfung ob Angebot OK ist
			  	//
			  	
			  	// 0 Rohstoffe angegeben
			  	if(ress_sell_metal<=0 
			  		&& ress_sell_crystal<=0  
			  		&& ress_sell_plastic<=0  
			  		&& ress_sell_fuel<=0  
			  		&& ress_sell_food<=0 )
			  	{
			  		var check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ressource_sell_submit').disabled=true;  
						document.getElementById('ressource_sell_submit').style.color='#f00';
			  	}  	
			  	// Alle Rohstoffe angegeben (und somit kein Preis festgelegt)
			  	else if(ress_sell_metal>0 
			  		&& ress_sell_crystal>0  
			  		&& ress_sell_plastic>0  
			  		&& ress_sell_fuel>0  
			  		&& ress_sell_food>0 )
			  	{
			  		var check_message = "<div style=\"color:red;font-weight:bold;\">Das Angebot muss einen Preis haben!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ressource_sell_submit').disabled=true;  
						document.getElementById('ressource_sell_submit').style.color='#f00';  	
			  	}
			  	// Zu hohe Preise
			  	else if(ress_buy_max['0']<0 
			  		|| ress_buy_max['1']<0 
			  		|| ress_buy_max['2']<0 
			  		|| ress_buy_max['3']<0
			  		|| ress_buy_max['4']<0)
			  	{
			  		var check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu hoch!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ressource_sell_submit').disabled=true;  
						document.getElementById('ressource_sell_submit').style.color='#f00'; 
			  	}
			  	// Zu niedrige Preise
			  	else if(ress_buy_min['0']>0 
			  		|| ress_buy_min['1']>0  
			  		|| ress_buy_min['2']>0 
			  		|| ress_buy_min['3']>0 
			  		|| ress_buy_min['4']>0 )
			  	{
			  		var test2 = Math.round(-3.5);
			  		var check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu niedrig!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ressource_sell_submit').disabled=true;  
						document.getElementById('ressource_sell_submit').style.color='#f00'; 
			  	}
			  	// Zu wenig Rohstoffe auf dem Planeten
			  	else if(ress_sell_metal * MARKET_TAX > res_metal
			  		|| ress_sell_crystal * MARKET_TAX > res_crystal 
			  		|| ress_sell_plastic * MARKET_TAX > res_plastic 
			  		|| ress_sell_fuel * MARKET_TAX > res_fuel
			  		|| ress_sell_food * MARKET_TAX > res_food)
			  	{
			  		var check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden! (Beachte Verkaufsgebühr)</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ressource_sell_submit').disabled=true;  
						document.getElementById('ressource_sell_submit').style.color='#f00';
			  	}  
			  	// Unerlaubte Zeichen im Werbetext
			  	else if(check_illegal_signs(document.getElementById('ressource_text').value))
			  	{
			  		var check_message = "<div style=\"color:red;font-weight:bold;\">Unerlaubte Zeichen im Werbetext!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ressource_sell_submit').disabled=true;  
						document.getElementById('ressource_sell_submit').style.color='#f00'; 		
			  	}	
			  	// Angebot ist OK
			  	else
			  	{		
			  		// Rechnet gesamt Verkaufsgebühren
			  		var sell_tax = Math.ceil(
			  								ress_sell_metal * (MARKET_TAX - 1)
			  							+ ress_sell_crystal * (MARKET_TAX - 1)
			  							+ ress_sell_plastic * (MARKET_TAX - 1)
			  							+ ress_sell_fuel * (MARKET_TAX - 1)
			  							+ ress_sell_food * (MARKET_TAX - 1)); 
			  			
			  		// Formatiert Verkaufsgebühren				 		
			  		var sell_tax = FormatNumber('return',sell_tax,'','','');
			  		
			  		var check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>Verkaufsgebühren: "+sell_tax+" t</div>";
			  		
			  		// Gibt Sendebutton frei
			  		document.getElementById('ressource_sell_submit').disabled=false;  
						document.getElementById('ressource_sell_submit').style.color='#0f0';

			  		// XAJAX bestätigt die Korrektheit/Legalität der Eingaben
			  		document.getElementById('ress_last_update').value=last_update; 
			  	}



					//
					// Daten Ändern
					//

					// Ändert Daten in den Min/Max Feldern
					document.getElementById('ress_min_max_metal').innerHTML=out_ress_min_max['0'];
					document.getElementById('ress_min_max_crystal').innerHTML=out_ress_min_max['1'];
					document.getElementById('ress_min_max_plastic').innerHTML=out_ress_min_max['2'];
					document.getElementById('ress_min_max_fuel').innerHTML=out_ress_min_max['3'];
					document.getElementById('ress_min_max_food').innerHTML=out_ress_min_max['4'];
					
					// Ändert Statusnachricht
					document.getElementById('check_message').innerHTML=check_message;	
				}
				
				
				
				/******************************************************************************************************/
				/* Markt: Schiffsverkauf Check/Kalkulator                           															    */
				/* Berechnet und überprüft die Korrektheit der Eingaben     																					*/
				/* new_ship: ist 1, wenn der Preis neu berechnet werden muss (Anderer Schiffstyp oder Anzahl Schiffe) */
				/* last_update: wird auf 1 gesetzt, wenn der Submitbutton gedrückt wird  															*/
				/******************************************************************************************************/				
				
				function calcMarketShipPrice(new_ship, last_update)
				{
					// Setzt Zeichen, dass die Eingaben noch nicht definitiv geprüft wurden vor dem Absenden
					document.getElementById('ship_last_update').value=0;
					
					
					//
					// Definitionen
					//
					
					MARKET_METAL_FACTOR = <?php echo MARKET_METAL_FACTOR; ?>;
					MARKET_CRYSTAL_FACTOR = <?php echo MARKET_CRYSTAL_FACTOR; ?>;
					MARKET_PLASTIC_FACTOR = <?php echo MARKET_PLASTIC_FACTOR; ?>;
					MARKET_FUEL_FACTOR = <?php echo MARKET_FUEL_FACTOR; ?>;
					MARKET_FOOD_FACTOR = <?php echo MARKET_FOOD_FACTOR; ?>;
					SHIP_PRICE_FACTOR_MAX = <?php echo SHIP_PRICE_FACTOR_MAX; ?>;
					SHIP_PRICE_FACTOR_MIN = <?php echo SHIP_PRICE_FACTOR_MIN; ?>;
					MARKET_TAX = <?php echo MARKET_TAX; ?>;
					
			
					
					//
					// Eingaben in Variablen Speichern und eventuell auf Korrektheit prüfen
					//
					
					// Aktuelle Rohstoffe auf dem Planeten werden abgefragt
					res_metal = document.getElementById('res_metal').value;
					res_crystal = document.getElementById('res_crystal').value;
					res_plastic = document.getElementById('res_plastic').value;
					res_fuel = document.getElementById('res_fuel').value;
					res_food = document.getElementById('res_food').value;
					
					// Zu verkaufende Rohstoffe (Angebot) formatieren (Trennzeichen entfernen) und in Variable speichern
					ship_sell_metal = document.getElementById('ship_sell_metal').value.replace(/`/g, "");
					ship_sell_crystal = document.getElementById('ship_sell_crystal').value.replace(/`/g, "");
					ship_sell_plastic = document.getElementById('ship_sell_plastic').value.replace(/`/g, "");
					ship_sell_fuel = document.getElementById('ship_sell_fuel').value.replace(/`/g, "");
					ship_sell_food = document.getElementById('ship_sell_food').value.replace(/`/g, "");
					
					// Die verlangten Rohstoffe (Preis) formatieren (Trennzeichen entfernen) und in Variable speichern
					ship_buy_metal = document.getElementById('ship_buy_metal').value.replace(/`/g, "");
					ship_buy_crystal = document.getElementById('ship_buy_crystal').value.replace(/`/g, "");
					ship_buy_plastic = document.getElementById('ship_buy_plastic').value.replace(/`/g, "");
					ship_buy_fuel = document.getElementById('ship_buy_fuel').value.replace(/`/g, "");
					ship_buy_food = document.getElementById('ship_buy_food').value.replace(/`/g, "");
					
					// Die Anzahl Schiffe formatieren (Trennzeichen entfernen) und in Variable speichern
					var ship_count = document.getElementById('ship_count').value.replace(/`/g, "");
					
					// Die Schiffsdaten aus dem mit PHP erstellen JS-Array werden in einer neuer Variable gespeichert
					var ship_id = document.getElementById('ship_list').value;
					var ship_name = ships[ship_id]['name'];
			   	var ship_max_count = ships[ship_id]['count'];
			   	var ship_costs_metal = ships[ship_id]['costs_metal'];
			   	var ship_costs_crystal = ships[ship_id]['costs_crystal'];
			   	var ship_costs_plastic = ships[ship_id]['costs_plastic'];
			   	var ship_costs_fuel = ships[ship_id]['costs_fuel'];
			   	var ship_costs_food = ships[ship_id]['costs_food'];
		  					  
		  					  
				  
				  // Stellt sicher, dass nur positive Zahlen verrechnet werden
					// 1. Prüft, ob Wert eine Zahl ist und nicht leer ist. Wenn nicht, wird 0 in das Feld geschrieben
					// 2. Erstellt den absolut Wert der Zahl
				  
				  // Anzahl Schiffe
				  if(istZahl(ship_count)==false || ship_count=='')
					{
						ship_count = 0;
						document.getElementById('ship_count').value=ship_count;
					}
					ship_count = Math.abs(ship_count);
				  
				  
					// Die verlangten Rohstoffe (Preis)					
					// Titan
					if(istZahl(ship_buy_metal)==false || ship_buy_metal=='')
					{
						ship_buy_metal = 0;
						document.getElementById('ship_buy_metal').value=ship_buy_metal;
					}
					ship_buy_metal = Math.abs(ship_buy_metal);
					
					// Silizium
					if(istZahl(ship_buy_crystal)==false || ship_buy_crystal=='')
					{
						ship_buy_crystal = 0;
						document.getElementById('ship_buy_crystal').value=ship_buy_crystal;
					}
					ship_buy_crystal = Math.abs(ship_buy_crystal);
					
					// PVC
					if(istZahl(ship_buy_plastic)==false || ship_buy_plastic=='')
					{
						ship_buy_plastic = 0;
						document.getElementById('ship_buy_plastic').value=ship_buy_plastic;
					}
					ship_buy_plastic = Math.abs(ship_buy_plastic);
					
					// Tritium
					if(istZahl(ship_buy_fuel)==false || ship_buy_fuel=='')
					{
						ship_buy_fuel = 0;
						document.getElementById('ship_buy_fuel').value=ship_buy_fuel;
					}
					ship_buy_fuel = Math.abs(ship_buy_fuel);
					
					// Nahrung
					if(istZahl(ship_buy_food)==false || ship_buy_food=='')
					{
						ship_buy_food = 0;
						document.getElementById('ship_buy_food').value=ship_buy_food;
					}
					ship_buy_food = Math.abs(ship_buy_food);	
				  
				  
				  
				  //
				  // Verrechnung der Daten
				  //
				  
				  // Ermittelt die Anzahl Schiffe. Entweder die eingegebene Zahl, oder soviel, wie auf dem Planeten vorhanden ist
				 	var ship_count = Math.min(ship_count, ship_max_count);

					// Rechnet Gesamtkosten pro Rohstoff (Kosten * Anzahl) (Dient als Basis für Min/Max rechnung)
				  var ship_costs_metal_total = ship_costs_metal * ship_count;
				  var ship_costs_crystal_total = ship_costs_crystal * ship_count;
				  var ship_costs_plastic_total = ship_costs_plastic * ship_count;
				  var ship_costs_fuel_total = ship_costs_fuel * ship_count;
				  var ship_costs_food_total = ship_costs_food * ship_count; 
				  
				  // Ändert den Verkaufswert (Angebot) auf die aktuellen Kosten
				  ship_sell_metal = ship_costs_metal_total;
			  	ship_sell_crystal = ship_costs_crystal_total;
			  	ship_sell_plastic = ship_costs_plastic_total;
			  	ship_sell_fuel = ship_costs_fuel_total;
			  	ship_sell_food = ship_costs_food_total;
				  
			  	// Schreibt Originalpreise in "Preis-Felder" und berechnet Min/Max wenn eine neue Eingabe gemacht wurde
			  	if(new_ship==1)
			  	{		  		
			  		ship_buy_metal = ship_costs_metal_total;
			  		ship_buy_crystal = ship_costs_crystal_total;
			  		ship_buy_plastic = ship_costs_plastic_total;
			  		ship_buy_fuel = ship_costs_fuel_total;
			  		ship_buy_food = ship_costs_food_total;
				  	
				  	//Ändert Daten beim "Angebot Feld" welches gesperrt ist für Änderungen
				  	document.getElementById('ship_sell_metal').value=FormatNumber('return',ship_buy_metal,'','','');
				  	document.getElementById('ship_sell_crystal').value=FormatNumber('return',ship_buy_crystal,'','','');
				  	document.getElementById('ship_sell_plastic').value=FormatNumber('return',ship_buy_plastic,'','','');
				  	document.getElementById('ship_sell_fuel').value=FormatNumber('return',ship_buy_fuel,'','','');
				  	document.getElementById('ship_sell_food').value=FormatNumber('return',ship_buy_food,'','','');
			  	}				  	
				  	
				  	
				  	
				  //
			  	// Errechnet und formatiert Preise
			  	//					
									
					var ship_buy_max = new Array();
					var log_ship_buy_max = new Array();
					var ship_buy_min = new Array();
					var log_ship_buy_min = new Array();
					var out_ship_min_max = new Array();
					
					// Errechnet den Preis mithilfe einer Schleife, welche alle 5 Rohstoffe beachtet
					for(res=0;res<5;res++)
					{
						// Titan
						if(res==0)
						{
							var actuel_res_factor = <?php echo MARKET_METAL_FACTOR; ?>;
							var ship_buy = ship_buy_metal;
							var buy_field = "ship_buy_metal";
						}
						// Silizium
						else if(res==1)
						{
							var actuel_res_factor = <?php echo MARKET_CRYSTAL_FACTOR;?>;
							var ship_buy = ship_buy_crystal;
							var buy_field = "ship_buy_crystal";
						}
						// PVC
						else if(res==2)
						{
							var actuel_res_factor = <?php echo MARKET_PLASTIC_FACTOR;?>;
							var ship_buy = ship_buy_plastic;
							var buy_field = "ship_buy_plastic";
						}
						// Tritium
						else if(res==3)
						{
							var actuel_res_factor = <?php echo MARKET_FUEL_FACTOR;?>;
							var ship_buy = ship_buy_fuel;
							var buy_field = "ship_buy_fuel";
						}
						// Nahrung
						else if(res==4)
						{
							var actuel_res_factor = <?php echo MARKET_FOOD_FACTOR;?>;
							var ship_buy = ship_buy_food;
							var buy_field = "ship_buy_food";
						}
											
	
						
			  		// MaxBetrag
			  		// Errechnet Grundbetrag (Noch ohne Abzüge von eingegebenen Preisen)
			  		ship_buy_max[res] =	ship_sell_metal / actuel_res_factor * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MAX
					  										+ ship_sell_crystal / actuel_res_factor * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MAX
					  										+ ship_sell_plastic / actuel_res_factor * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MAX
					  										+ ship_sell_fuel / actuel_res_factor * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MAX
					  										+ ship_sell_food / actuel_res_factor * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MAX;
					  										
					  // Errechnet Grundbetrag abzüglich bereits eingebener Preise
					  ship_buy_max[res] =  ship_buy_max[res]
					  										-	ship_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
					  										- ship_buy_crystal * MARKET_CRYSTAL_FACTOR / actuel_res_factor
					  										- ship_buy_plastic * MARKET_PLASTIC_FACTOR / actuel_res_factor
					  										- ship_buy_fuel * MARKET_FUEL_FACTOR / actuel_res_factor
					  										- ship_buy_food * MARKET_FOOD_FACTOR / actuel_res_factor;
					  ship_buy_max[res] = Math.floor(ship_buy_max[res]);		// Der Anzeigewert & Prüfwert
					  	
					  				
			  		// MinBetrag
			  		// Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
			  		ship_buy_min[res] =	ship_sell_metal / actuel_res_factor * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MIN
					  										+ ship_sell_crystal / actuel_res_factor * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MIN
					  										+ ship_sell_plastic / actuel_res_factor * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MIN
					  										+ ship_sell_fuel / actuel_res_factor * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MIN
					  										+ ship_sell_food / actuel_res_factor * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MIN;
					  // Errechnet Grundbetrag abzüglich bereits eingebener Preise
					  ship_buy_min[res] =  ship_buy_min[res]
					  										-	ship_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
					  										- ship_buy_crystal * MARKET_CRYSTAL_FACTOR / actuel_res_factor
					  										- ship_buy_plastic * MARKET_PLASTIC_FACTOR / actuel_res_factor
					  										- ship_buy_fuel * MARKET_FUEL_FACTOR / actuel_res_factor
					  										- ship_buy_food * MARKET_FOOD_FACTOR / actuel_res_factor;	
					  ship_buy_min[res] = Math.floor(ship_buy_min[res]);					// Der Anzeigewert & Prüfwert
					  	
					  
						// Definiert die Zahl, welche in das Preisfeld geschrieben wird nach dem Klick auf den Min/Max Link
						var sum_min = ship_buy + ship_buy_min[res];
						var sum_max = ship_buy + ship_buy_max[res];
						// Formatiert die Min/Max Zahl für die Ausgabe im Link (1000-er Striche)
						var ship_min = FormatNumber('return',ship_buy_min[res],'','','');
						var ship_max = FormatNumber('return',ship_buy_max[res],'','','');
						
						// Definiert die "Min./Max." Ausgabe
					  out_ship_min_max[res]="<a href=\"javascript:;\" onclick=\"document.getElementById('"+buy_field+"').value="+sum_min+";calcMarketShipPrice(0, 0);FormatNumber('"+buy_field+"','"+sum_min+"','','','');\">"+ship_min+"</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('"+buy_field+"').value="+sum_max+";calcMarketShipPrice(0, 0);FormatNumber('"+buy_field+"','"+sum_max+"','','','');\">"+ship_max+"</a>"; 
					}
						
						
						
				  //
			  	// End Prüfung ob Angebot OK ist
			  	//
			  	
			  	// 0 Schiffe angegeben
			  	if(ship_count<=0)
			  	{
			  		var ship_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ship_sell_submit').disabled=true;  
						document.getElementById('ship_sell_submit').style.color='#f00';
			  	}  
			  	// Zu hohe Preise
			  	else if(ship_buy_max['0']<0 
			  		|| ship_buy_max['1']<0 
			  		|| ship_buy_max['2']<0 
			  		|| ship_buy_max['3']<0
			  		|| ship_buy_max['4']<0)
			  	{
			  		var ship_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu hoch!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ship_sell_submit').disabled=true;  
						document.getElementById('ship_sell_submit').style.color='#f00'; 
			  	}
			  	// Zu niedrige Preise
			  	else if(ship_buy_min['0']>0 
			  		|| ship_buy_min['1']>0  
			  		|| ship_buy_min['2']>0 
			  		|| ship_buy_min['3']>0 
			  		|| ship_buy_min['4']>0 )
			  	{
			  		var ship_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu niedrig!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ship_sell_submit').disabled=true;  
						document.getElementById('ship_sell_submit').style.color='#f00'; 
			  	}
			  	
			  	// Unerlaubte Zeichen im Werbetext
			  	else if(check_illegal_signs(document.getElementById('ship_text').value))
			  	{
			  		var ship_check_message = "<div style=\"color:red;font-weight:bold;\">Unerlaubte Zeichen im Werbetext!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('ship_sell_submit').disabled=true;  
						document.getElementById('ship_sell_submit').style.color='#f00'; 		
			  	}	
			  	// Angebot ist OK
			  	else
			  	{					  		
			  		var ship_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!</div>";
			  		
			  		// Gibt Sendebutton frei
			  		document.getElementById('ship_sell_submit').disabled=false;  
						document.getElementById('ship_sell_submit').style.color='#0f0';

			  		// Bestätigt die Korrektheit/Legalität der Eingaben
			  		document.getElementById('ship_last_update').value=last_update; 
			  	}
			   	
			   	
			   	
			   	//
			   	// Daten Ändern
			   	//
			   	
			   	// Ändert Daten in den Min/Max Feldern
			   	document.getElementById('ship_min_max_metal').innerHTML=out_ship_min_max['0'];
					document.getElementById('ship_min_max_crystal').innerHTML=out_ship_min_max['1'];
					document.getElementById('ship_min_max_plastic').innerHTML=out_ship_min_max['2'];
					document.getElementById('ship_min_max_fuel').innerHTML=out_ship_min_max['3'];
					document.getElementById('ship_min_max_food').innerHTML=out_ship_min_max['4'];
					
					// Gibt die eingegebenen Zahlen formatiert aus
					document.getElementById('ship_count').value=FormatNumber('return',ship_count,'','','');
					document.getElementById('ship_buy_metal').value=FormatNumber('return',ship_buy_metal,'','','');
					document.getElementById('ship_buy_crystal').value=FormatNumber('return',ship_buy_crystal,'','','');
					document.getElementById('ship_buy_plastic').value=FormatNumber('return',ship_buy_plastic,'','','');
					document.getElementById('ship_buy_fuel').value=FormatNumber('return',ship_buy_fuel,'','','');
					document.getElementById('ship_buy_food').value=FormatNumber('return',ship_buy_food,'','','');
					
					// Ändert Statusnachricht
					document.getElementById('ship_check_message').innerHTML=ship_check_message;	
					
					// Ändert das Hiddenfeld "Schiffsname", welches für die Weiterverarbeitung mit PHP benötigt wird
					document.getElementById('ship_name').value=ship_name;
							   	
				}
				
				
	
				/**************************************************************************/
				/* Markt: Auktions Check		            															    */
				/* Berechnet und überprüft die Korrektheit der Eingaben     							*/
				/* last_update: wird auf 1 gesetzt, wenn der Submitbutton gedrückt wird  	*/
				/**************************************************************************/				
				
				function checkMarketAuctionFormular(last_update)
				{	
					// Setzt Zeichen, dass die Eingaben noch nicht definitiv geprüft wurden vor dem Absenden
					document.getElementById('auction_last_update').value=0
					
					//
					// Definitionen
					//
					
					MARKET_TAX = <?php echo MARKET_TAX; ?>;
					
					
					
					//
					// Eingaben in Variablen Speichern und eventuell auf Korrektheit prüfen
					//

					// Auktionsdauer
					auction_time_min = document.getElementById('auction_time_min').value;
					auction_time_days = document.getElementById('auction_time_days').value;
					auction_time_hours = document.getElementById('auction_time_hours').value;

					// Aktuelle Rohstoffe auf dem Planeten werden abgefragt
					res_metal = document.getElementById('res_metal').value;
					res_crystal = document.getElementById('res_crystal').value;
					res_plastic = document.getElementById('res_plastic').value;
					res_fuel = document.getElementById('res_fuel').value;
					res_food = document.getElementById('res_food').value;
					
					// Zu verkaufende Rohstoffe (Angebot) formatieren (Trennzeichen entfernen) und in Variable speichern
					auction_sell_metal = document.getElementById('auction_sell_metal').value.replace(/`/g, "");
					auction_sell_crystal = document.getElementById('auction_sell_crystal').value.replace(/`/g, "");
					auction_sell_plastic = document.getElementById('auction_sell_plastic').value.replace(/`/g, "");
					auction_sell_fuel = document.getElementById('auction_sell_fuel').value.replace(/`/g, "");
					auction_sell_food = document.getElementById('auction_sell_food').value.replace(/`/g, "");
					
								
										
					// Stellt sicher, dass nur positive Zahlen verrechnet werden
					// 1. Prüft, ob Wert eine Zahl ist und nicht leer ist. Wenn nicht, wird 0 in das Feld geschrieben
					// 2. Erstellt den absolut Wert der Zahl
					
					//
					// Zu verkaufende Rohstoffe (Angebot)
					//
					
					// Titan
					if(istZahl(auction_sell_metal)==false || auction_sell_metal=='')
					{
						auction_sell_metal = 0;
						document.getElementById('auction_sell_metal').value=auction_sell_metal;
					}
					auction_sell_metal = Math.abs(auction_sell_metal);
					
					// Silizium
					if(istZahl(auction_sell_crystal)==false || auction_sell_crystal=='')
					{
						auction_sell_crystal = 0;
						document.getElementById('auction_sell_crystal').value=auction_sell_crystal;
					}
					auction_sell_crystal = Math.abs(auction_sell_crystal);
					
					// PVC
					if(istZahl(auction_sell_plastic)==false || auction_sell_plastic=='')
					{
						auction_sell_plastic = 0;
						document.getElementById('auction_sell_plastic').value=auction_sell_plastic;
					}
					auction_sell_plastic = Math.abs(auction_sell_plastic);
					
					// Tritium
					if(istZahl(auction_sell_fuel)==false || auction_sell_fuel=='')
					{
						auction_sell_fuel = 0;
						document.getElementById('auction_sell_fuel').value=auction_sell_fuel;
					}
					auction_sell_fuel = Math.abs(auction_sell_fuel);
					
					// Nahrung
					if(istZahl(auction_sell_food)==false || auction_sell_food=='')
					{
						auction_sell_food = 0;
						document.getElementById('auction_sell_food').value=auction_sell_food;
					}
					auction_sell_food = Math.abs(auction_sell_food);
					
					
	
					// Ändert den Wert der Preiskästchen. Es wird automatisch deselektiert, wenn vom gleichen Rohstoff verkaufen wird
					// Titan
					if(document.getElementById('auction_buy_metal').checked==true && auction_sell_metal==0)
					{
						auction_buy_metal = 1;
						document.getElementById('auction_buy_metal').value=1;
					}	
					else
					{
						auction_buy_metal = 0;
						document.getElementById('auction_buy_metal').value=0;
						document.getElementById('auction_buy_metal').checked=false;
					}	
					
					// Silizium
					if(document.getElementById('auction_buy_crystal').checked==true && auction_sell_crystal==0)
					{
						auction_buy_crystal = 1;
						document.getElementById('auction_buy_crystal').value=1;
					}
					else
					{
						auction_buy_crystal = 0;
						document.getElementById('auction_buy_crystal').value=0;
						document.getElementById('auction_buy_crystal').checked=false;
					}
					
					// PVC
					if(document.getElementById('auction_buy_plastic').checked==true && auction_sell_plastic==0)
					{
						auction_buy_plastic = 1;
						document.getElementById('auction_buy_plastic').value=1;
					}
					else
					{
						auction_buy_plastic = 0;
						document.getElementById('auction_buy_plastic').value=0;
						document.getElementById('auction_buy_plastic').checked=false;
					}
					
					// Tritium
					if(document.getElementById('auction_buy_fuel').checked==true && auction_sell_fuel==0)
					{
						auction_buy_fuel = 1;
						document.getElementById('auction_buy_fuel').value=1;
					}
					else
					{
						auction_buy_fuel = 0;
						document.getElementById('auction_buy_fuel').value=0;
						document.getElementById('auction_buy_fuel').checked=false;
					}
					
					// Nahrung
					if(document.getElementById('auction_buy_food').checked==true && auction_sell_food==0)
					{
						auction_buy_food = 1;
						document.getElementById('auction_buy_food').value=1;
					}
					else
					{
						auction_buy_food = 0;
						document.getElementById('auction_buy_food').value=0;
						document.getElementById('auction_buy_food').checked=false;
					}
			
			
					
					//
					// Berechnet und formatiert das Enddatum und gibt dieses aus
					//
					
					var auction_end_time = parseInt(auction_time_min) + parseInt(auction_time_days) * 24 * 3600 + parseInt(auction_time_hours) * 3600;
					
					var time = new Date();
					time.setTime (auction_end_time * 1000); // PHP-Timestamp * 1000 da JS in Millisekunden rechnet

					var year = time.getFullYear();
					var month = time.getMonth();
					var day = time.getDate();
					var hour = time.getHours();
					var minute = time.getMinutes();
				
					var auction_time = ""+day+"."+(month+1)+"."+year+" : "+hour+"."+minute+"";
					
					// Ändert das Datumsfeld
					document.getElementById('auction_end_time').innerHTML=auction_time;
			
			
			
			  	//
			  	// End Prüfung ob Angebot OK ist
			  	//
			  	
			  	// Keine Rohstoffe angegeben
			  	if(auction_sell_metal<=0 
			  		&& auction_sell_crystal<=0  
			  		&& auction_sell_plastic<=0  
			  		&& auction_sell_fuel<=0  
			  		&& auction_sell_food<=0 )
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_sell_submit').disabled=true;  
						document.getElementById('auction_sell_submit').style.color='#f00';
			  	}  	
			  	// Keinen Preis angegeben
			  	else if(auction_buy_metal==0 
			  		&& auction_buy_crystal==0  
			  		&& auction_buy_plastic==0  
			  		&& auction_buy_fuel==0  
			  		&& auction_buy_food==0)
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Das Angebot muss eine Zahlungsmöglichkeit aufweisen!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_sell_submit').disabled=true;  
						document.getElementById('auction_sell_submit').style.color='#f00';
			  	}
			  	// Zu wenig Rohstoffe auf dem Planeten
			  	else if(Math.floor(auction_sell_metal * MARKET_TAX) > res_metal
			  		|| Math.floor(auction_sell_crystal * MARKET_TAX) > res_crystal 
			  		|| Math.floor(auction_sell_plastic * MARKET_TAX) > res_plastic 
			  		|| Math.floor(auction_sell_fuel * MARKET_TAX) > res_fuel
			  		|| Math.floor(auction_sell_food * MARKET_TAX) > res_food)
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden! (Beachte Verkaufsgebühr)</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_sell_submit').disabled=true;  
						document.getElementById('auction_sell_submit').style.color='#f00';
			  	} 
			  	// Unerlaubte Zeichen im Werbetext
			  	else if(check_illegal_signs(document.getElementById('auction_text').value))
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Unerlaubte Zeichen im Werbetext!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_sell_submit').disabled=true;  
						document.getElementById('auction_sell_submit').style.color='#f00';  		
			  	}	  	 	
			  	// Angebot ist OK
			  	else
			  	{		
			  		// Rechnet gesamt Verkaufsgebühren
			  		var sell_tax = Math.ceil(
			  								auction_sell_metal * (MARKET_TAX - 1)
			  							+ auction_sell_crystal * (MARKET_TAX - 1)
			  							+ auction_sell_plastic * (MARKET_TAX - 1)
			  							+ auction_sell_fuel * (MARKET_TAX - 1)
			  							+ auction_sell_food * (MARKET_TAX - 1)); 
			  			
			  		// Formatiert Verkaufsgebühren				 		
			  		var sell_tax = FormatNumber('return',sell_tax,'','','');
			  		
			  		var auction_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>Verkaufsgebühren: "+sell_tax+" t</div>";
			  		
			  		// Gibt Sendebutton frei
			  		document.getElementById('auction_sell_submit').disabled=false;  
						document.getElementById('auction_sell_submit').style.color='#0f0';

			  		// XAJAX bestätigt die Korrektheit/Legalität der Eingaben
			  		document.getElementById('auction_last_update').value=last_update; 
			  	} 		
			  	
			  	
			  	
			  	//
					// Daten Ändern
					//

			  	// Ändert Statusnachricht
					document.getElementById('auction_check_message').innerHTML=auction_check_message;	
					
				}
				
				
				/***********************************************************************************/
				/* Markt: Auktion (Bieten) Check/Kalkulator                                        */
				/* Berechnet und überprüft die Korrektheit der Eingaben beim Bieten einer Auktion  */
				/* last_update: wird auf 1 gesetzt, wenn der Submitbutton gedrückt wird            */
				/***********************************************************************************/
				
				function calcMarketAuctionPrice(last_update)
				{
					// Setzt Zeichen, dass die Eingaben noch nicht definitiv geprüft wurden vor dem Absenden
					document.getElementById('auction_show_last_update').value=0;



					//
					// Definitionen
					//
					
					MARKET_METAL_FACTOR = <?php echo MARKET_METAL_FACTOR; ?>;
					MARKET_CRYSTAL_FACTOR = <?php echo MARKET_CRYSTAL_FACTOR; ?>;
					MARKET_PLASTIC_FACTOR = <?php echo MARKET_PLASTIC_FACTOR; ?>;
					MARKET_FUEL_FACTOR = <?php echo MARKET_FUEL_FACTOR; ?>;
					MARKET_FOOD_FACTOR = <?php echo MARKET_FOOD_FACTOR; ?>;
					AUCTION_PRICE_FACTOR_MAX = <?php echo AUCTION_PRICE_FACTOR_MAX; ?>;
					AUCTION_PRICE_FACTOR_MIN = <?php echo AUCTION_PRICE_FACTOR_MIN; ?>;
					MARKET_TAX = <?php echo MARKET_TAX; ?>;

					//321
					//
					// Eingaben in Variablen Speichern und eventuell auf Korrektheit prüfen
					//

					// Aktuelle Rohstoffe auf dem Planeten werden abgefragt
					res_metal = document.getElementById('res_metal').value;
					res_crystal = document.getElementById('res_crystal').value;
					res_plastic = document.getElementById('res_plastic').value;
					res_fuel = document.getElementById('res_fuel').value;
					res_food = document.getElementById('res_food').value;
					
					// Zu versteigernde Rohstoffe (Angebot) in Variablen speichern
					auction_sell_metal = document.getElementById('auction_sell_metal').value;
					auction_sell_crystal = document.getElementById('auction_sell_crystal').value;
					auction_sell_plastic = document.getElementById('auction_sell_plastic').value;
					auction_sell_fuel = document.getElementById('auction_sell_fuel').value;
					auction_sell_food = document.getElementById('auction_sell_food').value;
					
					// Das momentane Höchstgebot in Variablen speichern
					auction_buy_metal = document.getElementById('auction_buy_metal').value;
					auction_buy_crystal = document.getElementById('auction_buy_crystal').value;
					auction_buy_plastic = document.getElementById('auction_buy_plastic').value;
					auction_buy_fuel = document.getElementById('auction_buy_fuel').value;
					auction_buy_food = document.getElementById('auction_buy_food').value;
					
					// Das eingegebene Gebot (neuer Preis) formatieren (Trennzeichen entfernen) und in Variable speichern
					auction_new_buy_metal = document.getElementById('auction_new_buy_metal').value.replace(/`/g, "");
					auction_new_buy_crystal = document.getElementById('auction_new_buy_crystal').value.replace(/`/g, "");
					auction_new_buy_plastic = document.getElementById('auction_new_buy_plastic').value.replace(/`/g, "");
					auction_new_buy_fuel = document.getElementById('auction_new_buy_fuel').value.replace(/`/g, "");
					auction_new_buy_food = document.getElementById('auction_new_buy_food').value.replace(/`/g, "");
					
					
					// Stellt sicher, dass nur positive Zahlen verrechnet werden
					// 1. Prüft, ob Wert eine Zahl ist und nicht leer ist. Wenn nicht, wird 0 in das Feld geschrieben
					// 2. Erstellt den absolut Wert der Zahl
					
					//
					// Das eingegebene Gebot (neuer Preis)
					//
					
					// Titan
					if(istZahl(auction_new_buy_metal)==false || auction_new_buy_metal=='')
					{
						auction_new_buy_metal = 0;
						document.getElementById('auction_new_buy_metal').value=auction_new_buy_metal;
					}
					auction_new_buy_metal = Math.abs(auction_new_buy_metal);
					
					// Silizium
					if(istZahl(auction_new_buy_crystal)==false || auction_new_buy_crystal=='')
					{
						auction_new_buy_crystal = 0;
						document.getElementById('auction_new_buy_crystal').value=auction_new_buy_crystal;
					}
					auction_new_buy_crystal = Math.abs(auction_new_buy_crystal);
					
					// PVC
					if(istZahl(auction_new_buy_plastic)==false || auction_new_buy_plastic=='')
					{
						auction_new_buy_plastic = 0;
						document.getElementById('auction_new_buy_plastic').value=auction_new_buy_plastic;
					}
					auction_new_buy_plastic = Math.abs(auction_new_buy_plastic);
					
					// Tritium
					if(istZahl(auction_new_buy_fuel)==false || auction_new_buy_fuel=='')
					{
						auction_new_buy_fuel = 0;
						document.getElementById('auction_new_buy_fuel').value=auction_new_buy_fuel;
					}
					auction_new_buy_fuel = Math.abs(auction_new_buy_fuel);
					
					// Nahrung
					if(istZahl(auction_new_buy_food)==false || auction_new_buy_food=='')
					{
						auction_new_buy_food = 0;
						document.getElementById('auction_new_buy_food').value=auction_new_buy_food;
					}
					auction_new_buy_food = Math.abs(auction_new_buy_food);
			
				
					// Summiert das Höchstgebot
					buy_price = parseInt(auction_buy_metal) + parseInt(auction_buy_crystal) + parseInt(auction_buy_plastic) + parseInt(auction_buy_fuel) + parseInt(auction_buy_food);
					
					// Summiert die aktuellen Eingaben
					new_buy_price = auction_new_buy_metal + auction_new_buy_crystal + auction_new_buy_plastic + auction_new_buy_fuel + auction_new_buy_food;
				
					
			  	//
			  	// Errechnet und formatiert Preise
			  	//					
									
					var auction_buy_max = new Array();
					var log_auction_buy_max = new Array();
					var auction_buy_min = new Array();
					var log_auction_buy_min = new Array();
					var out_auction_min_max = new Array();
					
					// Errechnet den Preis mithilfe einer Schleife, welche alle 5 Rohstoffe beachtet
					for(res=0;res<5;res++)
					{
						// Titan
						if(res==0)
						{
							var actuel_res_factor = <?php echo MARKET_METAL_FACTOR; ?>;
							var auction_sell = auction_sell_metal;
							var auction_new_buy = auction_new_buy_metal;
							var buy_field = "auction_new_buy_metal";
						}
						// Silizium
						else if(res==1)
						{
							var actuel_res_factor = <?php echo MARKET_CRYSTAL_FACTOR;?>;
							var auction_sell = auction_sell_crystal;
							var auction_new_buy = auction_new_buy_crystal;
							var buy_field = "auction_new_buy_crystal";
						}
						// PVC
						else if(res==2)
						{
							var actuel_res_factor = <?php echo MARKET_PLASTIC_FACTOR;?>;
							var auction_sell = auction_sell_plastic;
							var auction_new_buy = auction_new_buy_plastic;
							var buy_field = "auction_new_buy_plastic";
						}
						// Tritium
						else if(res==3)
						{
							var actuel_res_factor = <?php echo MARKET_FUEL_FACTOR;?>;
							var auction_sell = auction_sell_fuel;
							var auction_new_buy = auction_new_buy_fuel;
							var buy_field = "auction_new_buy_fuel";
						}
						// Nahrung
						else if(res==4)
						{
							var actuel_res_factor = <?php echo MARKET_FOOD_FACTOR;?>;
							var auction_sell = auction_sell_food;
							var auction_new_buy = auction_new_buy_food;
							var buy_field = "auction_new_buy_food";
						}
												
	
						
			  		// MaxBetrag
			  		// Errechnet Grundbetrag (Noch ohne Abzüge von eingegebenen Preisen)
			  		auction_buy_max[res] =	auction_sell_metal / actuel_res_factor * MARKET_METAL_FACTOR * AUCTION_PRICE_FACTOR_MAX
					  										+ auction_sell_crystal / actuel_res_factor * MARKET_CRYSTAL_FACTOR * AUCTION_PRICE_FACTOR_MAX
					  										+ auction_sell_plastic / actuel_res_factor * MARKET_PLASTIC_FACTOR * AUCTION_PRICE_FACTOR_MAX
					  										+ auction_sell_fuel / actuel_res_factor * MARKET_FUEL_FACTOR * AUCTION_PRICE_FACTOR_MAX
					  										+ auction_sell_food / actuel_res_factor * MARKET_FOOD_FACTOR * AUCTION_PRICE_FACTOR_MAX;
					  										
					  // Errechnet Grundbetrag abzüglich bereits eingebener Preise
					  auction_buy_max[res] =  auction_buy_max[res]
					  										-	auction_new_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
					  										- auction_new_buy_crystal * MARKET_CRYSTAL_FACTOR / actuel_res_factor
					  										- auction_new_buy_plastic * MARKET_PLASTIC_FACTOR / actuel_res_factor
					  										- auction_new_buy_fuel * MARKET_FUEL_FACTOR / actuel_res_factor
					  										- auction_new_buy_food * MARKET_FOOD_FACTOR / actuel_res_factor;
					  auction_buy_max[res] = Math.floor(auction_buy_max[res]);		// Der Anzeigewert & Prüfwert
					  	
					  				
			  		// MinBetrag
			  		// Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
			  		auction_buy_min[res] =	auction_sell_metal / actuel_res_factor * MARKET_METAL_FACTOR * AUCTION_PRICE_FACTOR_MIN
					  										+ auction_sell_crystal / actuel_res_factor * MARKET_CRYSTAL_FACTOR * AUCTION_PRICE_FACTOR_MIN
					  										+ auction_sell_plastic / actuel_res_factor * MARKET_PLASTIC_FACTOR * AUCTION_PRICE_FACTOR_MIN
					  										+ auction_sell_fuel / actuel_res_factor * MARKET_FUEL_FACTOR * AUCTION_PRICE_FACTOR_MIN
					  										+ auction_sell_food / actuel_res_factor * MARKET_FOOD_FACTOR * AUCTION_PRICE_FACTOR_MIN;
					  // Errechnet Grundbetrag abzüglich bereits eingebener Preise
					  auction_buy_min[res] =  auction_buy_min[res]
					  										-	auction_new_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
					  										- auction_new_buy_crystal * MARKET_CRYSTAL_FACTOR / actuel_res_factor
					  										- auction_new_buy_plastic * MARKET_PLASTIC_FACTOR / actuel_res_factor
					  										- auction_new_buy_fuel * MARKET_FUEL_FACTOR / actuel_res_factor
					  										- auction_new_buy_food * MARKET_FOOD_FACTOR / actuel_res_factor;	
					  auction_buy_min[res] = Math.ceil(auction_buy_min[res]);					// Der Anzeigewert & Prüfwert	
					  	
					  
					  
					  
						// Definiert die Zahl, welche in das Preisfeld geschrieben wird nach dem Klick auf den Min/Max Link
						var sum_min = auction_new_buy + auction_buy_min[res];
						var sum_max = auction_new_buy + auction_buy_max[res];
						// Formatiert die Min/Max Zahl für die Ausgabe im Link (1000-er Striche)
						var auction_min = FormatNumber('return',auction_buy_min[res],'','','');
						var auction_max = FormatNumber('return',auction_buy_max[res],'','','');
						
						// Definiert die "Min./Max." Ausgabe
					  out_auction_min_max[res]="<a href=\"javascript:;\" onclick=\"document.getElementById('"+buy_field+"').value="+sum_min+";calcMarketAuctionPrice('0');FormatNumber('"+buy_field+"','"+sum_min+"','','','');\">"+auction_min+"</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('"+buy_field+"').value="+sum_max+";calcMarketAuctionPrice('0');FormatNumber('"+buy_field+"','"+sum_max+"','','','');\">"+auction_max+"</a>"; 
						
					}
					
					
					//
			  	// End Prüfung ob Angebot OK ist
			  	//
			  	
			  	// 0 Rohstoffe angegeben
			  	if(auction_new_buy_metal<=0 
			  		&& auction_new_buy_crystal<=0  
			  		&& auction_new_buy_plastic<=0  
			  		&& auction_new_buy_fuel<=0  
			  		&& auction_new_buy_food<=0 )
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_submit').disabled=true;  
						document.getElementById('auction_submit').style.color='#f00';
			  	}  	
			  	// Zu hohe Preise
			  	else if(auction_buy_max['0']<0 
			  		|| auction_buy_max['1']<0 
			  		|| auction_buy_max['2']<0 
			  		|| auction_buy_max['3']<0
			  		|| auction_buy_max['4']<0)
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu hoch!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_submit').disabled=true;  
						document.getElementById('auction_submit').style.color='#f00';
			  	}
			  	// Zu niedrige Preise
			  	else if(auction_buy_min['0']>0 
			  		|| auction_buy_min['1']>0  
			  		|| auction_buy_min['2']>0 
			  		|| auction_buy_min['3']>0 
			  		|| auction_buy_min['4']>0 )
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu niedrig!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_submit').disabled=true;  
						document.getElementById('auction_submit').style.color='#f00'; 
			  	}
			  	// Zu wenig Rohstoffe auf dem Planeten
			  	else if(auction_new_buy_metal * MARKET_TAX > res_metal
			  		|| auction_new_buy_crystal * MARKET_TAX > res_crystal 
			  		|| auction_new_buy_plastic * MARKET_TAX > res_plastic 
			  		|| auction_new_buy_fuel * MARKET_TAX > res_fuel
			  		|| auction_new_buy_food * MARKET_TAX > res_food)
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden! (Beachte Verkaufsgebühr)</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_submit').disabled=true;  
						document.getElementById('auction_submit').style.color='#f00'; 
			  	}  
			  	// Gebot ist tiefer als das vom Höchstbietenden
			  	else if(buy_price >= new_buy_price)
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Das Gebot muss höher sein als das vom Höchstbietenden!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_submit').disabled=true;  
						document.getElementById('auction_submit').style.color='#f00';  		
			  	}
			  	// Zeit ist abgelaufen 
			  	else if(document.getElementById('auction_rest_time').value <= 0)
			  	{
			  		var auction_check_message = "<div style=\"color:red;font-weight:bold;\">Auktion ist beendet!</div>";
			  		
			  		// Sperrt Sendebutton
			  		document.getElementById('auction_submit').disabled=true;  
						document.getElementById('auction_submit').style.color='#f00';  		
			  	} 	
			  	// Angebot ist OK
			  	else
			  	{					  		
			  		var auction_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!</div>";
			  		
			  		// Gibt Sendebutton frei
			  		document.getElementById('auction_submit').disabled=false;  
						document.getElementById('auction_submit').style.color='#0f0';

			  		// XAJAX bestätigt die Korrektheit/Legalität der Eingaben
			  		document.getElementById('auction_show_last_update').value=last_update; 
			  	}


					
					//
					// Daten Ändern
					//

					// Ändert Daten in den Min/Max Feldern
					if(document.getElementById('auction_currency_metal').value==1)
					{
						document.getElementById('auction_min_max_metal').innerHTML=out_auction_min_max['0'];
					}
					if(document.getElementById('auction_currency_crystal').value==1)
					{
						document.getElementById('auction_min_max_crystal').innerHTML=out_auction_min_max['1'];
					}
					if(document.getElementById('auction_currency_plastic').value==1)
					{
						document.getElementById('auction_min_max_plastic').innerHTML=out_auction_min_max['2'];
					}
					if(document.getElementById('auction_currency_fuel').value==1)
					{
						document.getElementById('auction_min_max_fuel').innerHTML=out_auction_min_max['3'];
					}
					if(document.getElementById('auction_currency_food').value==1)
					{
						document.getElementById('auction_min_max_food').innerHTML=out_auction_min_max['4'];
					}

					
					// Ändert Statusnachricht
					document.getElementById('auction_check_message').innerHTML=auction_check_message;	
					
				}
				
			</script>
		<?PHP 		
		
		
    $marr = mysql_fetch_array($mres);
    define("MARKET_LEVEL",$marr['buildlist_current_level']);
	
	// Läd die Anzahl aller eingestellter Angebote auf dem aktuellen Planeten
	$cnt_res=dbquery("
			SELECT
				(
					SELECT 
						COUNT(*)
					FROM 
						market_ressource
					WHERE
						user_id='".$cu->id."' 
						AND planet_id='".$cp->id()."'
				) AS ress_cnt,
				(
					SELECT 
						COUNT(*)
					FROM 
						market_ship
					WHERE
						user_id='".$cu->id."' 
						AND planet_id='".$cp->id()."'
				) AS ship_cnt,
				(
					SELECT 
						COUNT(*)
					FROM 
						market_auction
					WHERE
						auction_user_id='".$cu->id."' 
						AND auction_planet_id='".$cp->id()."'
				) AS auction_cnt
				;");
	
	$cnt_arr=mysql_fetch_assoc($cnt_res);
	
	// Summiert die eingestellten Angebote und berechnet die Anzahl der noch einstellbaren Angebote
	$anzahl = $cnt_arr['ress_cnt'] + $cnt_arr['ship_cnt'] + $cnt_arr['auction_cnt'];
	$possible=MARKET_LEVEL-$anzahl;
			
	// Lädt Stufe des Allianzmarktplatzes
	if ($cu->allianceId()>0)
		$alliance_market_level = $cu->alliance->getBuildingLevel("Handelszentrum");
	else
		$alliance_market_level = 0;
	
	// Calculate cooldown
	if ($alliance_market_level<5)
	{
		$factor = 0.2 * $alliance_market_level;
	}
	else
	{
		$factor = $alliance_market_level-4;
	}
	$cooldown = ($factor==0) ? 0 : 3600/$factor;
	if ($alliance_market_level>0)
	{
		if ($cu->alliance->getMarketCooldown()>time())
		{
			$status_text = "Bereit in <span id=\"cdcd\">".tf($cu->alliance->getMarketCooldown()-time()."</span>");
			$cd_enabled=true;
		}
		else
		{
			$status_text = "Bereit";
			$cd_enabled=false;
		}
	}
	else
	{
		$status_text = "Es wurde noch kein Handelszentrum gebaut!";
		$cd_enabled=false;
	}
	
    // Definiert den Rückgabefaktor beim zurückziehen eines Angebots
    $return_factor = 1 - (1/(MARKET_LEVEL+1));
	
	
	//Marktinof Bof
	tableStart("Marktplatz-Infos");
		echo "<tr><th>Angebote:</th>
				<td>Im Moment hast du ".$anzahl." Angebote von diesem Planet auf dem Markt</td></tr>";
		echo "<tr><th>Mögliche Angebote:</th>
				<td>Du kannst noch ".$possible." Angebote einstellen</td></tr>";
		echo "<tr><th>Rückzugsgebühren:</th>
				<td>Wenn du ein Angebot zur&uuml;ckziehst erh&auml;lst du ".(round($return_factor,2)*100)."% des Angebotes zur&uuml;ck (abgerundet).</td></tr>";
		echo "<tr><th>Verkaufsgebühren:</th>
				<td>Der Verkaufsgeb&uuml;hr des Marktplatzes betr&auml;gt ".get_percent_string(MARKET_TAX,1,1)."";
		if ($cu->specialist->tradeBonus!=1)
		{
			echo " (inkl ".get_percent_string($cu->specialist->tradeBonus,1,1)." Kostenverringerung durch ".$cu->specialist->name."!";
		}
		echo "	</td></tr>";
		if ($cu->specialist->tradeTime!=1)
		{
			echo "<tr><th>Handelsflottengeschwindigkeit:</th>
					<td>Die Handelsflotten fliegen durch ".$cu->specialist->name." mit ".get_percent_string($cu->specialist->tradeTime,1)." Geschwindigkeit!
					</td></tr>";
		}
		if ($cu->allianceId()>0)
		{
			echo "<tr><th>Allianzmarktstatus:</th>
					<td>".$status_text."</td></tr>";
		}

	tableEnd();
 

		// Navigation
		$tabitems = array(
			"user_home"=>"Angebote aufgeben",
	 		"user_sell"=>"Eigene Angebote",
	 		"search"=>"Angebotssuche",
		);
	 	show_tab_menu("mode",$tabitems);		 
		
		echo "<br/>";

		//
    // Alle Abgelaufenen Auktionen löschen und ev. waren versenden
    //
		//market_auction_update();

		//
		// Rohstoffkauf speichern
		//
		if (isset($_POST['ressource_submit']) && checker_verify())
		{
			$cnt = 0;
			$cnt_error = 0;
			$buy_metal_total = 0;
			$buy_crystal_total = 0;
			$buy_plastic_total = 0;
			$buy_fuel_total = 0;
			$buy_food_total = 0;
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
					market_ressource 
				WHERE 
					ressource_market_id='".$id."' 
					AND ressource_buyable='1';");
				// Prüft, ob Angebot noch vorhanden ist
				if (mysql_num_rows($res)!=0)
				{		
					$arr = mysql_fetch_array($res);
							
					// Prüft, ob genug Rohstoffe vorhanden sind
					if ($cp->resMetal >= $arr['buy_metal'] 
					&& $cp->resCrystal >= $arr['buy_crystal']  
					&& $cp->resPlastic >= $arr['buy_plastic']  
					&& $cp->resFuel >= $arr['buy_fuel']  
					&& $cp->resFood >= $arr['buy_food'])
					{
						$seller_user_nick = get_user_nick($arr['user_id']);	
	
						//Angebot reservieren (wird zu einem späteren Zeitpunkt verschickt)
						dbquery("
						UPDATE
							market_ressource
						SET
							ressource_buyable='0',
							ressource_buyer_id='".$cu->id."',
							ressource_buyer_planet_id='".$cp->id()."',
							ressource_buyer_cell_id='".$cp->cellId()."'
						WHERE
							ressource_market_id='".$arr['ressource_market_id']."'");
	
						// Rohstoffe vom Käuferplanet abziehen und $c-variabeln anpassen
						$cp->changeRes(-$arr['buy_metal'],-$arr['buy_crystal'],-$arr['buy_plastic'],-$arr['buy_fuel'],-$arr['buy_food']);
						
						// Nachricht an Verkäufer
						$msg = "Ein Handel ist zustande gekommen\n";
						$msg .= "Der Spieler ".$cu->nick." hat von dir folgende Rohstoffe gekauft:\n\n";
						
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
					    add_log(10,"[URL=?page=user&sub=edit&user_id=".$cu->id."][B]".$cu->nick."[/B][/URL] hat von [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$seller_user_nick."[/B][/URL] Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food']),time());
						}
	
						// Log schreiben
						add_log(7,"Ein Handel ist zustande gekommen\nDer Spieler ".$cu->nick." hat vom Spieler ".$seller_user_nick."  folgende Rohstoffe gekauft:\n\n".RES_METAL.": ".nf($arr['sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['sell_plastic'])."\n".RES_FUEL.": ".nf($arr['sell_fuel'])."\n".RES_FOOD.": ".nf($arr['sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['buy_plastic'])."\n".RES_FUEL.": ".nf($arr['buy_fuel'])."\n".RES_FOOD.": ".nf($arr['buy_food'])."\n\n",time());
						
						
						
						// Faktor = Kaufzeit - Verkaufzeit (in ganzen Tagen, mit einem Max. von 7)
						// Total = Mengen / Faktor
						$factor = min( ceil( (time() - $arr['datum']) / 3600 / 24 ) ,7);
						
						// Summiert gekaufte Rohstoffe für Config
						$buy_metal_total += $arr['buy_metal'] / $factor;
						$buy_crystal_total += $arr['buy_crystal'] / $factor;
						$buy_plastic_total += $arr['buy_plastic'] / $factor;
						$buy_fuel_total += $arr['buy_fuel'] / $factor;
						$buy_food_total += $arr['buy_food'] / $factor;
						
						// Summiert verkaufte Rohstoffe für Config
						$sell_metal_total += $arr['sell_metal'] / $factor;
						$sell_crystal_total += $arr['sell_crystal'] / $factor;
						$sell_plastic_total += $arr['sell_plastic'] / $factor;
						$sell_fuel_total += $arr['sell_fuel'] / $factor;
						$sell_food_total += $arr['sell_food'] / $factor;


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
			
			
			// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
			// Titan
			dbquery("
			UPDATE
				config
			SET
				config_value=config_value+".(round($buy_metal_total)).",
				config_param1=config_param1+".(round($sell_metal_total))."
			WHERE
				config_name='market_metal_logger'");		
				
			// Silizium
			dbquery("
			UPDATE
				config
			SET
				config_value=config_value+".(round($buy_crystal_total)).",
				config_param1=config_param1+".(round($sell_crystal_total))."
			WHERE
				config_name='market_crystal_logger'");	
				
			// PVC
			dbquery("
			UPDATE
				config
			SET
				config_value=config_value+".(round($buy_plastic_total)).",
				config_param1=config_param1+".(round($sell_plastic_total))."
			WHERE
				config_name='market_plastic_logger'");		
				
			// Tritium
			dbquery("
			UPDATE
				config
			SET
				config_value=config_value+".(round($buy_fuel_total)).",
				config_param1=config_param1+".(round($sell_fuel_total))."
			WHERE
				config_name='market_fuel_logger'");	
				
			// Food
			dbquery("
			UPDATE
				config
			SET
				config_value=config_value+".(round($buy_food_total)).",
				config_param1=config_param1+".(round($sell_food_total))."
			WHERE
				config_name='market_food_logger'");
		}



		//
		// Schiffskauf speichern
		//
		elseif (isset($_POST['ship_submit']) && checker_verify())
		{
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

		}
		
		
		
		//
		// Auktionsgebot speichern
		//
		elseif(isset($_POST['auction_show_last_update']) && $_POST['auction_show_last_update']==1  && checker_verify())
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
		
		}



		//
		// Rohstoffverkauf speichern
		//
		elseif (isset($_POST['ress_last_update']) && $_POST['ress_last_update']==1 && checker_verify())
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

			
			if(!isset($_POST['ressource_for_alliance']))
			{
				$_POST['ressource_for_alliance']=0;
				$for_alliance="";
			}
			elseif ($alliance_market_level>0 && !$cd_enabled)
			{
				$_POST['ressource_for_alliance']=$cu->allianceId;
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
				$_POST['ressource_for_alliance']=0;
				$for_alliance="";
			}
			
			// Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
  		if($_POST['ress_sell_metal'] * MARKET_TAX <= $cp->resMetal
  		&& $_POST['ress_sell_crystal'] * MARKET_TAX <= $cp->resCrystal 
  		&& $_POST['ress_sell_plastic'] * MARKET_TAX <= $cp->resPlastic  
  		&& $_POST['ress_sell_fuel'] * MARKET_TAX <= $cp->resFuel
  		&& $_POST['ress_sell_food'] * MARKET_TAX <= $cp->resFood)
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
	      send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Angebot eingetragen",$msg);
	
	      // Log schreiben
	      add_log(7,"Der Spieler ".$cu->nick." hat folgende Rohstoffe ".$for_alliance."angeboten:\n\n".RES_METAL.": ".nf($_POST['ress_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['ress_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['ress_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['ress_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['ress_sell_food'])."\n\nFolgender Preis muss daf&uuml;r gezahlt werden:\n\n".RES_METAL.": ".nf($_POST['ress_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['ress_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['ress_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['ress_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['ress_buy_food'])."\n\n",time());
	
	      // Rohstoffe vom Planet abziehen
        	// TODO: use planet class
	      dbquery("
	      UPDATE
	      	planets
	      SET
	        planet_res_metal=planet_res_metal-".($_POST['ress_sell_metal']*MARKET_TAX).",
	        planet_res_crystal=planet_res_crystal-".($_POST['ress_sell_crystal']*MARKET_TAX).",
	        planet_res_plastic=planet_res_plastic-".($_POST['ress_sell_plastic']*MARKET_TAX).",
	        planet_res_fuel=planet_res_fuel-".($_POST['ress_sell_fuel']*MARKET_TAX).",
	        planet_res_food=planet_res_food-".($_POST['ress_sell_food']*MARKET_TAX)."
	      WHERE
	      	id='".$cp->id()."'
	      	AND planet_user_id='".$cu->id."';");
	
				// Angebot speichern
	      dbquery("
	      INSERT INTO
	      market_ressource
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
	          ('".$cu->id."',
	          '".$cp->id()."',
	          '".$cp->cellId()."',
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
	          
	      ok_msg("Angebot erfolgreich aufgegeben");
	      return_btn();
    	}
    	else
    	{
 	      error_msg("Es sind nicht mehr genügend Rohstoffe vorhanden!");
	      return_btn();   		
    	}
    }
		
		

		//
		// Schiffverkauf speichern
		//
		elseif (isset($_POST['ship_last_update']) && $_POST['ship_last_update']==1 && checker_verify())
		{
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
			$ship_name = $_POST['ship_name'];
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
      	shiplist
      WHERE
      	shiplist_entity_id='".$cp->id()."'
      	AND shiplist_ship_id='".$ship_id."'");
      if(mysql_num_rows($check_res)>0)
      {
      	$check_arr=mysql_fetch_array($check_res);
      	
      	if($check_arr['shiplist_count']>=$ship_count)
      	{
      		// Schiffe vom Planeten abziehen
          dbquery("
          UPDATE
          	shiplist
          SET
          	shiplist_count=shiplist_count-".$ship_count."
          WHERE
              shiplist_entity_id='".$cp->id()."'
              AND shiplist_ship_id='".$_POST['ship_list']."';");

					// Angebot speicherns
          dbquery("
          INSERT INTO
          market_ship
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
              ('".$cu->id."',
              '".$cp->id()."',
              '".$cp->cellId()."',
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
          send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Angebot eingetragen",$msg);

          //Log schreiben
          add_log(LOG_CAT,"Der Spieler ".$cu->nick." hat folgende Schiffe zum Verkauf ".$for_alliance."angeboten:\n\n".nf($ship_count)." ".$ship_name."\n\nPreis:\n ".RES_METAL.": ".nf($_POST['ship_buy_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['ship_buy_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['ship_buy_plastic'])."\n".RES_FUEL.": ".nf($_POST['ship_buy_fuel'])."\n".RES_FOOD.": ".nf($_POST['ship_buy_food'])."\n\n",time());

        	ok_msg("Angebot erfolgreich abgesendet!");
          return_btn();
        }
        else
        {
       		error_msg("Die angegebene Anzahl Schiffe ist nicht mehr verfügbar!");
          return_btn();       	
        }
      }
      else
      {
      	error_msg("Die angegebenen Schiffe sind nicht mehr vorhanden!");
     		return_btn();
      }

		}



		//
		// Auktion Speichern
		//
		elseif (isset($_POST['auction_last_update']) && $_POST['auction_last_update']==1 && checker_verify())
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
      if (($_POST['auction_sell_metal']*MARKET_TAX)<=$cp->resMetal
          && ($_POST['auction_sell_crystal']*MARKET_TAX)<=$cp->resCrystal
          && ($_POST['auction_sell_plastic']*MARKET_TAX)<=$cp->resPlastic
          && ($_POST['auction_sell_fuel']*MARKET_TAX)<=$cp->resFuel
          && ($_POST['auction_sell_food']*MARKET_TAX)<=$cp->resFood)
      {

        // Rohstoffe + Taxe vom Planetenkonto abziehen
                	// TODO: use planet class
        dbquery("
        UPDATE
            planets
        SET
            planet_res_metal=planet_res_metal-".($_POST['auction_sell_metal']*MARKET_TAX).",
            planet_res_crystal=planet_res_crystal-".($_POST['auction_sell_crystal']*MARKET_TAX).",
            planet_res_plastic=planet_res_plastic-".($_POST['auction_sell_plastic']*MARKET_TAX).",
            planet_res_fuel=planet_res_fuel-".($_POST['auction_sell_fuel']*MARKET_TAX).",
            planet_res_food=planet_res_food-".($_POST['auction_sell_food']*MARKET_TAX)."
        WHERE
            id=".$cp->id()."
            AND planet_user_id=".$cu->id."");

        // Angebot speichern
        dbquery("
        INSERT INTO market_auction
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
            ('".$cu->id."',
            '".$cp->id()."',
            '".$cp->cellId()."',
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
        send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Auktion eingetragen",$msg);

        add_log(LOG_CAT,"Der Spieler ".$cu->nick." hat folgende Rohstoffe zur versteigerung angeboten:\n\n".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\nAuktionsende: ".date("d.m.Y H:i",$auction_end_time)."",time());

        ok_msg("Auktion erfolgreich lanciert");
        return_btn();

      }
      else
      {
          error_msg("Die angegebenen Rohstoffe sind nicht mehr verfügbar!");
          return_btn();
      }
    
		}



		//
		// Rohstoff Angebote anzeigen
		//
		elseif (isset($_POST['search_submit']) && $_POST['search_cat']=="ressource" && checker_verify())
		{
			echo "<h2>Rohstoffe</h2>";

			$res = dbquery("
			SELECT
				*
			FROM
				market_ressource
			WHERE
				ressource_buyable='1'
        AND user_id!='".$cu->id."'
        ".stripslashes($_POST['ressource_sql_add'])."
      ORDER BY
				datum ASC;");				
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;mode=ressource\" method=\"post\" id=\"ress_buy_selector\">\n";
				checker_init();				
				tableStart("Angebots&uuml;bersicht");
					echo "<tr>
								<th colspan=\"2\" width=\"25%\">Angebot:</th>
								<th width=\"15%\">Anbieter:</th>
								<th width=\"25%\">Datum:</th>
								<th colspan=\"2\" width=\"25%\">Preis:</th>
								<th width=\"10%\">Kaufen:</th>
							</tr>";
				$cnt=0;
				while ($arr=mysql_fetch_array($res))
				{
					// Für Allianzmitglied reserveriert
          if($arr['ressource_for_alliance']!=0)
          {
              $for_alliance="<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
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
					if ($cp->resMetal < $arr['buy_metal'])
					{ 
						$metal_class = "tbldata2";
					}
					if ($cp->resCrystal < $arr['buy_crystal'])
					{ 
						$crystal_class = "tbldata2";
					}					
					if ($cp->resPlastic < $arr['buy_plastic'])
					{ 
						$plastic_class = "tbldata2";
					}
					if ($cp->resFuel < $arr['buy_fuel'])
					{ 
						$fuel_class = "tbldata2";
					}
					if ($cp->resFood < $arr['buy_food'])
					{ 
						$food_class = "tbldata2";
					}
					
					echo "<tr>
									<td><b>".RES_METAL."</b>:";


					// Übergibt Daten an XAJAX
					// Aktuelle Rohstoffe vom Planeten
          echo "<input type=\"hidden\" value=\"".$cp->resMetal."\" name=\"res_metal\" />";
          echo "<input type=\"hidden\" value=\"".$cp->resCrystal."\" name=\"res_crystal\" />";
          echo "<input type=\"hidden\" value=\"".$cp->resPlastic."\" name=\"res_plastic\" />";
          echo "<input type=\"hidden\" value=\"".$cp->resFuel."\" name=\"res_fuel\" />";
          echo "<input type=\"hidden\" value=\"".$cp->resFood."\" name=\"res_food\" />";								
					
					// Preis
          echo "<input type=\"hidden\" value=\"".$arr['buy_metal']."\" name=\"ress_buy_metal[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_crystal']."\" name=\"ress_buy_crystal[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_plastic']."\" name=\"ress_buy_plastic[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_fuel']."\" name=\"ress_buy_fuel[".$arr['ressource_market_id']."]\" />";
          echo "<input type=\"hidden\" value=\"".$arr['buy_food']."\" name=\"ress_buy_food[".$arr['ressource_market_id']."]\" />";						

									
									
									
									echo "</td>
									<td>".nf($arr['sell_metal'])."</td>
									<td rowspan=\"5\">
										<a href=\"?page=userinfo&amp;id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a>
									</td>
									<td rowspan=\"5\">
										".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['ressource_text'])."
									</td>
									<td><b>".RES_METAL."</b>:</td>
									<td class=\"".$metal_class."\">".nf($arr['buy_metal'])."</td>
									<td rowspan=\"5\">
										<input type=\"checkbox\" name=\"ressource_market_id[]\" id=\"ressource_market_id\" value=\"".$arr['ressource_market_id']."\" onclick=\"xajax_calcMarketRessBuy(xajax.getFormValues('ress_buy_selector'));\" /><br/><br/>".$for_alliance."
									</td>
								</tr>
								<tr>
									<td><b>".RES_CRYSTAL."</b>:</td>
									<td>".nf($arr['sell_crystal'])."</td>
									<td><b>".RES_CRYSTAL."</b>:</td>
									<td class=\"".$crystal_class."\">".nf($arr['buy_crystal'])."</td>
								</tr>
								<tr>
									<td><b>".RES_PLASTIC."</b>:</td>
									<td>".nf($arr['sell_plastic'])."</td>
									<td><b>".RES_PLASTIC."</b>:</td>
									<td class=\"".$plastic_class."\">".nf($arr['buy_plastic'])."</td></tr>
								<tr>
									<td><b>".RES_FUEL."</b>:</td>
									<td>".nf($arr['sell_fuel'])."</td>
									<td><b>".RES_FUEL."</b>:</td>
									<td class=\"".$fuel_class."\">".nf($arr['buy_fuel'])."</td>
								</tr>
								<tr>
									<td><b>".RES_FOOD."</b>:</td>
									<td>".nf($arr['sell_food'])."</td>
									<td><b>".RES_FOOD."</b>:</td>
									<td class=\"".$food_class."\">".nf($arr['buy_food'])."</td>
								</tr>";
					$cnt++;
					// Setzt Lücke zwischen den Angeboten
					if ($cnt<mysql_num_rows($res))
					{
						echo "<tr>
										<td colspan=\"7\" style=\"height:10px;background:#000\">&nbsp;</td>
									</tr>";
					}

				}
				tableEnd();
				
				tableStart();
				echo "<tr>
								<td colspan=\"7\" id=\"ressource_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"7\" id=\"ressource_check_button\" style=\"text-align:center;vertical-align:middle;\">
									<input type=\"submit\" class=\"button\" name=\"ressource_submit\" id=\"ressource_submit\" value=\"Angebot annehmen\" disabled=\"disabled\"/>
								</td>
							</tr>";
				tableEnd();
				echo "</form>";
			}
			else
			{
				echo "Keine Angebote vorhanden!";
			}
		}



		//
		// Schiffs Angebote anzeigen
		//
		elseif(isset($_POST['search_submit']) && $_POST['search_cat']=="ship" && checker_verify())
		{		
			echo "<form action=\"?page=".$page."&amp;mode=ships\" method=\"post\" id=\"ship_buy_selector\">\n";
			checker_init();
				
			tableStart("Angebots&uuml;bersicht");
			echo "<tr>
						<th width=\"25%\">Angebot:</th>
						<th width=\"15%\">Anbieter:</th>
						<th width=\"25%\">Datum:</th>
						<th colspan=\"2\" width=\"25%\">Preis:</th>
						<th width=\"10%\">Kaufen:</th>
					</tr>";	
				
			$res = dbquery("	
			SELECT
				*
			FROM
				market_ship
			WHERE
				ship_buyable='1'
        AND user_id!='".$cu->id."'
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
          $hiddenFields = "<input type=\"hidden\" value=\"".$cp->resMetal."\" name=\"res_metal\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$cp->resCrystal."\" name=\"res_crystal\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$cp->resPlastic."\" name=\"res_plastic\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$cp->resFuel."\" name=\"res_fuel\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$cp->resFood."\" name=\"res_food\" />";								
					
					// Preis
          $hiddenFields.= "<input type=\"hidden\" value=\"".$arr['ship_costs_metal']."\" name=\"ship_buy_metal[".$arr['ship_market_id']."]\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$arr['ship_costs_crystal']."\" name=\"ship_buy_crystal[".$arr['ship_market_id']."]\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$arr['ship_costs_plastic']."\" name=\"ship_buy_plastic[".$arr['ship_market_id']."]\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$arr['ship_costs_fuel']."\" name=\"ship_buy_fuel[".$arr['ship_market_id']."]\" />";
          $hiddenFields.= "<input type=\"hidden\" value=\"".$arr['ship_costs_food']."\" name=\"ship_buy_food[".$arr['ship_market_id']."]\" />";						
					
					
					
					// Für Allianzmitglied reserveriert
          if($arr['ship_for_alliance']!=0)
          {
              $for_alliance="<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
          }
          else
          {
              $for_alliance="";
          }
              
          // Prüft, ob die Rohstoffe ausreichen für das Angebot und färbt dementsprechend Preis
          $metal_class = "";
          $crystal_class = "";
          $plastic_class = "";
          $fuel_class = "";
          $food_class = "";
					if ($cp->resMetal < $arr['ship_costs_metal'])
					{ 
						$metal_class = "resfullcolor";
					}
					if ($cp->resCrystal < $arr['ship_costs_crystal'])
					{ 
						$crystal_class = "resfullcolor";
					}					
					if ($cp->resPlastic < $arr['ship_costs_plastic'])
					{ 
						$plastic_class = "resfullcolor";
					}
					if ($cp->resFuel < $arr['ship_costs_fuel'])
					{ 
						$fuel_class = "resfullcolor";
					}
					if ($cp->resFood < $arr['ship_costs_food'])
					{ 
						$food_class = "resfullcolor";
					}
					
					echo "<tr>
									<td rowspan=\"5\">".$hiddenFields."
										".nf($arr['ship_count'])." <a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['ship_id']."\">".$arr['ship_name']."</a>
									</td>
									<td rowspan=\"5\">
										<a href=\"?page=userinfo&amp;id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a>
									</td>
									<td rowspan=\"5\">
										".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['ship_text'])."
									</td>
									<td><b>".RES_METAL."</b>:</td>
									<td class=\"".$metal_class."\">".nf($arr['ship_costs_metal'])."</td>
									<td rowspan=\"5\">
										<input type=\"checkbox\" name=\"ship_market_id[]\" id=\"ship_market_id_".$arr['ship_market_id']."\" value=\"".$arr['ship_market_id']."\" onclick=\"xajax_calcMarketShipBuy(xajax.getFormValues('ship_buy_selector'));\" /><br/><br/>".$for_alliance."
									</td>
								</tr>
								<tr>
									<td><b>".RES_CRYSTAL."</b>:</td>
									<td class=\"".$crystal_class."\">".nf($arr['ship_costs_crystal'])."</td>
								</tr>
								<tr>
									<td><b>".RES_PLASTIC."</b>:</td>
									<td class=\"".$plastic_class."\">".nf($arr['ship_costs_plastic'])."</td></tr>
								<tr>
									<td><b>".RES_FUEL."</b>:</td>
									<td class=\"".$fuel_class."\">".nf($arr['ship_costs_fuel'])."</td>
								</tr>
								<tr>
									<td><b>".RES_FOOD."</b>:</td>
									<td class=\"".$food_class."\">".nf($arr['ship_costs_food'])."</td>
								</tr>";
					$cnt++;
					// Setzt Lücke zwischen den Angeboten
					if ($cnt<mysql_num_rows($res))
					{
						echo "<tr>
										<td colspan=\"7\" style=\"height:10px;background:#000\">&nbsp;</td>
									</tr>";
					}

				}
				tableEnd();
				
				tableStart();
				echo "<tr>
								<td colspan=\"7\" id=\"ship_buy_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=\"7\" style=\"text-align:center;vertical-align:middle;\">
									<input type=\"submit\" name=\"ship_submit\" id=\"ship_submit\" value=\"Angebot annehmen\" disabled=\"disabled\"/>
								</td>
							</tr>";
				tableEnd();
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
		elseif((isset($_POST['search_submit']) && $_POST['search_cat']=="auction" || (isset($_POST['auction_back']) && $_POST['auction_back']==1)) && checker_verify())
		{
			echo "<h2>Auktionen</h2><br/>";

			$res = dbquery("	
			SELECT
				*
			FROM
				market_auction
			WHERE
				auction_user_id!='".$cu->id."'
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
				
				tableStart("Angebots&uuml;bersicht");
				tableEnd();
				$cnt=0;
				$acnts=array();
				$acnt=0;
				while ($arr=mysql_fetch_array($res))
				{	
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
						
						// Gibt Nachricht aus, wenn die Auktion beendet ist, aber noch kein Löschtermin festgelegt ist
						if($rest_time<=0)
						{
							$rest_time = "Auktion beendet!";
						}
						// und sonst Zeit bis zum Ende anzeigen
						else
						{
							$rest_time = "Noch ".$t."t ".$h."h ".$m."m ".$sec."s";
						}


						// Löschdatum anzeigen wenn dieses schon festgelegt ist
						if($arr['auction_delete_date']!=0)
						{
							$delete_rest_time = $arr['auction_delete_date']-time();
							
              $t = floor($delete_rest_time / 3600 / 24);
              $h = floor(($delete_rest_time) / 3600);
              $m = floor(($delete_rest_time-($h*3600))/60);
              $sec = floor(($delete_rest_time-($h*3600)-($m*60)));	
              
              $delete_header = "Löschung";
              $rest_time = "AUKTION BEENDET";
              
              // Gibt Nachricht aus, wenn der Löschzeitpunkt erreicht oder überschritten ist
              if($delete_rest_time<=0)
              {
              	$delete_time = "Gebot wird gelöscht...";
              }
              // und sonst Zeit bis zu Löschung anzeigen
              else
              {
              	$delete_time = "Gebot wird nach ".$h." Stunden und ".$m." Minuten gel&ouml;scht";
              }
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
											
							
						tableStart();	
						
						// Header
						echo "<tr>
		                <th colspan=\"2\">Info</th>
		                <th>Rohstoff</th>
		                <th>Angebot</th>
		                <th>Höchstgebot</th>
		              </tr>";
			             
						echo "<tr>
										<th style=\"width:20%;vertical-align:middle;\">Anbieter</th>
										<td style=\"width:25%;vertical-align:middle;\">
											<a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a>
										</td>
										<th style=\"width:15%;vertical-align:middle;\">".RES_METAL.":</th>
										<td id=\"auction_sell_metal_field\" style=\"width:20%;vertical-align:middle;\">
											".nf($arr['auction_sell_metal'])."
										</td>										
											<td class=\"".$auction_buy_metal_class."\" id=\"auction_buy_metal_field\" style=\"width:20%;vertical-align:middle;\">
											".$auction_buy_metal."
										</td>			
									</tr>
									<tr>
										<th>Start</th>
										<td>
											".date("d.m.Y  G:i:s", $arr['auction_start'])."
										</td>
										<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
										<td id=\"auction_sell_crystal_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_crystal'])."
										</td>										
											<td class=\"".$auction_buy_crystal_class."\" id=\"auction_buy_crystal_field\" style=\"vertical-align:middle;\">
											".$auction_buy_crystal."
										</td>
									</tr>	
									<tr>									
										<th>Ende</th>
										<td>
											".date("d.m.Y  G:i:s", $arr['auction_end'])."
										</td>
										<th style=\"vertical-align:middle;\">".RES_PLASTIC.":</th>
										<td id=\"auction_sell_plastic_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_plastic'])."
										</td>										
											<td class=\"".$auction_buy_plastic_class."\" id=\"auction_buy_plastic_field\" style=\"vertical-align:middle;\">
											".$auction_buy_plastic."
										</td>	
									</tr>
									<tr>									
										<th>Dauer</th>
										<td ".$class.">
											".$rest_time."
										</td>
										<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
										<td id=\"auction_sell_fuel_field\" style=\"vertical-align:middle;\">
											".nf($arr['auction_sell_fuel'])."
										</td>										
											<td class=\"".$auction_buy_fuel_class."\" id=\"auction_buy_fuel_field\" style=\"vertical-align:middle;\">
											".$auction_buy_fuel."
										</td>	
									</tr>
									<tr>									
										<th>Höchstbietender</th>
										<td>
											".$buyer."
										</td>
										<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
										<td id=\"auction_sell_food_field\" style=\"vertical-align:middle;\">
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
														<td colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">
															".stripslashes($arr['auction_text'])."
														</td>
													</tr>";
									}
									echo "<tr>
										<td colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">";
										// Bietbutton anzeigen wenn das Angebot noch steht
										if($arr['auction_buyable']==1)
										{
											// Der Höchstbietende kann nicht sein eigenes Gebot überbieten
											if($arr['auction_current_buyer_id']==$cu->id)
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
            tableEnd();
            echo "<br/><br/><br/>";				
				}
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
		elseif(isset($_POST['auction_market_id']) && $_POST['auction_market_id']!=0 && !isset($_POST['auction_cancel']) && checker_verify())
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
				market_auction 
			WHERE 
				auction_market_id='".intval($_POST['auction_market_id'])."'
				AND auction_user_id!='".$cu->id."' ");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				
				echo "<form action=\"?page=".$page."&amp;mode=auction\" method=\"post\" name=\"auctionShowFormular\" id=\"auction_show_selector\">";
				$cstr=checker_init();
				
				// Übergibt Daten an XAJAX
				// Rohstoffe
        echo "<input type=\"hidden\" value=\"".$cp->resMetal."\" name=\"res_metal\" id=\"res_metal\"/>";
        echo "<input type=\"hidden\" value=\"".$cp->resCrystal."\" name=\"res_crystal\" id=\"res_crystal\"/>";
        echo "<input type=\"hidden\" value=\"".$cp->resPlastic."\" name=\"res_plastic\" id=\"res_plastic\"/>";
        echo "<input type=\"hidden\" value=\"".$cp->resFuel."\" name=\"res_fuel\" id=\"res_fuel\"/>";
        echo "<input type=\"hidden\" value=\"".$cp->resFood."\" name=\"res_food\" id=\"res_food\"/>";					
				
				// Angebot
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_metal']."\" name=\"auction_sell_metal\" id=\"auction_sell_metal\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_crystal']."\" name=\"auction_sell_crystal\" id=\"auction_sell_crystal\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_plastic']."\" name=\"auction_sell_plastic\" id=\"auction_sell_plastic\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_fuel']."\" name=\"auction_sell_fuel\" id=\"auction_sell_fuel\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_sell_food']."\" name=\"auction_sell_food\" id=\"auction_sell_food\"/>";
        		
        // Höchstgebot
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_metal']."\" name=\"auction_buy_metal\" id=\"auction_buy_metal\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_crystal']."\" name=\"auction_buy_crystal\" id=\"auction_buy_crystal\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_plastic']."\" name=\"auction_buy_plastic\" id=\"auction_buy_plastic\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_fuel']."\" name=\"auction_buy_fuel\" id=\"auction_buy_fuel\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_buy_food']."\" name=\"auction_buy_food\" id=\"auction_buy_food\"/>";	
        
        // Gewünschte Währung	
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_metal']."\" name=\"auction_currency_metal\" id=\"auction_currency_metal\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_crystal']."\" name=\"auction_currency_crystal\" id=\"auction_currency_crystal\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_plastic']."\" name=\"auction_currency_plastic\" id=\"auction_currency_plastic\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_fuel']."\" name=\"auction_currency_fuel\" id=\"auction_currency_fuel\"/>";
        echo "<input type=\"hidden\" value=\"".$arr['auction_currency_food']."\" name=\"auction_currency_food\" id=\"auction_currency_food\"/>";
                  		
        // Zeit
        echo "<input type=\"hidden\" value=\"0\" name=\"auction_rest_time\" id=\"auction_rest_time\"/>";	
        
        // Angebot ID	
        echo "<input type=\"hidden\" value=\"".$arr['auction_market_id']."\" name=\"auction_market_id\" id=\"auction_market_id\"/>";	
        
        // SQL
        echo "<input type=\"hidden\" value=\"".stripslashes($_POST['auction_sql_add'])."\" id=\"auction_sql_add\" name=\"auction_sql_add\"/>";
        					
				//Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
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
				
				// Gibt Nachricht aus, wenn die Auktion beendet ist
				if($rest_time<=0)
				{
					$rest_time = "Auktion beendet!";
				}
				// und sonst wird die Zeit bis zum Ende angezeigt
				else
				{
					$rest_time = "Noch ".$t."t ".$h."h ".$m."m ".$sec."s";
				}
				
				// Übergibt die Endzeit an de Javascript Countdownfunktion
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
				tableStart("Angebotsinfo");
				echo "<tr>
                <th>Anbieter</th>
								<td>
									<a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a>
								</td>
							</tr>
							<tr>
                <th>Start</th>
								<td>
									".date("d.m.Y  G:i:s", $arr['auction_start'])."
								</td>
							</tr>
							<tr>
                <th>Ende</th>
								<td>
									".date("d.m.Y  G:i:s", $arr['auction_end'])."
								</td>
							</tr>
							<tr>
                <th>Dauer</th>";
							// Löschdatum anzeigen wenn dieses schon festgelegt ist und "Auktion beendet"
							if($arr['auction_delete_date']!=0)
							{
								$delete_rest_time = $arr['auction_delete_date']-time();
								
				        $t = floor($delete_rest_time / 3600 / 24);
				        $h = floor(($delete_rest_time) / 3600);
				        $m = floor(($delete_rest_time-($h*3600))/60);
				        $sec = floor(($delete_rest_time-($h*3600)-($m*60)));	
				        
				        // Gibt Nachricht aus, wenn Löschzeit erreicht oder überschritten
				        if($delete_rest_time<=0)
				        {
				        	$delete_time = "Wird gelöscht...";
				        }
				        // und sonst wird die Zeit bis zur Löschung angezeigt
				        else
				        {
				        	$delete_time = "In ".$h."h und ".$m."m";
				      	}
				        
				        
				        echo "<td>AUKTION BEENDET</td>
				        		</tr>
				        		<tr>
				        			<th>Löschung</th>
				        			<td>".$delete_time."</td>
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
				                <th>Höchstbietender</th>
												<td>
													".$buyer."
												</td>
											</tr>
											<tr>
				                <th>Geboten am</th>
												<td>
													".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."
												</td>
											</tr>";
							}
				tableEnd();
				
				echo "<script type=\"text/javascript\">";
				foreach ($acnts as $cfield=> $ctime)
				{
					echo "setCountdown('".$ctime."','".$cfield."');";
				}
				echo "</script>";		
				
				
				// Angebots/Preis Maske
				//Header
				tableStart();
				echo "<tr>
								<th style=\"width:15%;vertical-align:middle;\">Rohstoff</th>
								<th style=\"width:15%;vertical-align:middle;\">Angebot</th>
								<th style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</th>
								<th style=\"width:15%;vertical-align:middle;\">Höchstgebot</th>
								<th style=\"width:15%;vertical-align:middle;\">Bieten</th>
								<th style=\"width:35%;vertical-align:middle;\">Min./Max.</th>
							</tr>";
				// Titan
				echo "<tr>
								<th style=\"vertical-align:middle;\">".RES_METAL.":</th>
								<td id=\"auction_sell_metal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_metal'])."
								</td>		
								<th style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</th>
								<td id=\"auction_buy_metal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_metal'])."
								</td>
								<td style=\"vertical-align:middle;\">";
								if($arr['auction_currency_metal']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_metal'])."\" name=\"auction_new_buy_metal\" id=\"auction_new_buy_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');calcMarketAuctionPrice(0);\"/>";
								}
								else
								{
									echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_metal\" id=\"auction_new_buy_metal\"/>";
									echo " - ";
								}										
					echo "</td>
								<th id=\"auction_min_max_metal\" style=\"vertical-align:middle;\"> - </th>
							</tr>";
				// Silizium
				echo "<tr>
								<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
								<td id=\"auction_sell_crystal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_crystal'])."
								</td>		
								<th style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</th>
								<td id=\"auction_buy_crystal_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_crystal'])."
								</td>
								<td style=\"vertical-align:middle;\">";
								if($arr['auction_currency_crystal']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_crystal'])."\" name=\"auction_new_buy_crystal\" id=\"auction_new_buy_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');calcMarketAuctionPrice(0);\"/>";
								}
								else
								{
									echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_crystal\" id=\"auction_new_buy_crystal\"/>";
									echo " - ";
								}										
					echo "</td>
								<th id=\"auction_min_max_crystal\" style=\"vertical-align:middle;\"> - </th>
							</tr>";	
				// PVC
				echo "<tr>
								<th style=\"vertical-align:middle;\">".RES_PLASTIC.":</th>
								<td id=\"auction_sell_plastic_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_plastic'])."
								</td>		
								<th style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</th>
								<td id=\"auction_buy_plastic_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_plastic'])."
								</td>
								<td style=\"vertical-align:middle;\">";
								if($arr['auction_currency_plastic']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_plastic'])."\" name=\"auction_new_buy_plastic\" id=\"auction_new_buy_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');calcMarketAuctionPrice(0);\"/>";
								}
								else
								{
									echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_plastic\" id=\"auction_new_buy_plastic\"/>";
									echo " - ";
								}										
					echo "</td>
								<th id=\"auction_min_max_plastic\" style=\"vertical-align:middle;\"> - </th>
							</tr>";		
				// Tritium
				echo "<tr>
								<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
								<td id=\"auction_sell_fuel_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_fuel'])."
								</td>		
								<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</th>
								<td id=\"auction_buy_fuel_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_fuel'])."
								</td>
								<td style=\"vertical-align:middle;\">";
								if($arr['auction_currency_fuel']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_fuel'])."\" name=\"auction_new_buy_fuel\" id=\"auction_new_buy_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');calcMarketAuctionPrice(0);\"/>";
								}
								else
								{
									echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_fuel\" id=\"auction_new_buy_fuel\"/>";
									echo " - ";
								}										
					echo "</td>
								<th id=\"auction_min_max_fuel\" style=\"vertical-align:middle;\"> - </th>
							</tr>";	
				// Nahrung
				echo "<tr>
								<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
								<td id=\"auction_sell_food_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_sell_food'])."
								</td>		
								<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</th>
								<td id=\"auction_buy_food_field\" style=\"vertical-align:middle;\">
									".nf($arr['auction_buy_food'])."
								</td>
								<td style=\"vertical-align:middle;\">";
								if($arr['auction_currency_food']==1 && $arr['auction_buyable']==1)
								{
									echo "<input type=\"text\" value=\"".nf($arr['auction_buy_food'])."\" name=\"auction_new_buy_food\" id=\"auction_new_buy_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');calcMarketAuctionPrice(0);\"/>";
								}
								else
								{
									echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_food\" id=\"auction_new_buy_food\"/>";
									echo " - ";
								}										
					echo "</td>
								<th id=\"auction_min_max_food\" style=\"vertical-align:middle;\"> - </th>
							</tr>";	
							
				// Status Nachricht (Ajax Überprüfungstext)
				echo "<tr>
								<td colspan=\"6\" id=\"auction_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
							</tr>";		
				tableEnd();
				
				echo "<br/><br/><input type=\"button\" class=\"button\" name=\"auction_submit\" id=\"auction_submit\" value=\"Bieten\" disabled=\"disabled\" onclick=\"calcMarketAuctionPrice(1);checkUpdate('auctionShowFormular', 'auction_show_last_update');\"/><br/><br/><input type=\"button\" class=\"button\" name=\"auction_back_submit\" id=\"auction_back_submit\" value=\"Zurück\" onclick=\"auctionBack();\" />";
				echo "</form>";					
			}
			else
			{
				error_msg("Angebot nicht mehr vorhanden!");
			}
			
		}



		//
		// Suchmaske
		//
		elseif ($mode=="search")
		{
			echo "<form action=\"?page=".$page."\" method=\"post\" id=\"search_selector\">\n";
			checker_init();
			
			// Lädt Anzahl Angebote
			$cnt_res=dbquery("
			SELECT
				(
					SELECT 
						COUNT(*)
					FROM 
						market_ressource
					WHERE
						ressource_buyable='1'
        		AND user_id!='".$cu->id."'
        		AND (ressource_for_alliance='0' OR ressource_for_alliance='".$cu->allianceId."')
				) AS ress_cnt,
				(
					SELECT 
						COUNT(*)
					FROM 
						market_ship
					WHERE
						ship_buyable='1'
						AND user_id!='".$cu->id."'
						AND (ship_for_alliance='0' OR ship_for_alliance='".$cu->allianceId."')
				) AS ship_cnt,
				(
					SELECT 
						COUNT(*)
					FROM 
						market_auction
					WHERE
						auction_user_id!='".$cu->id."'
				) AS auction_cnt
				;");
	
			$cnt_arr=mysql_fetch_assoc($cnt_res);
			
			
			// Lädt Schiffliste
      $sres=dbquery("
      SELECT
        ship_id,
        ship_name                           
      FROM
         ships
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
      echo "<input type=\"hidden\" value=\"".$cp->resMetal."\" name=\"res_metal\" />";
      echo "<input type=\"hidden\" value=\"".$cp->resCrystal."\" name=\"res_crystal\" />";
      echo "<input type=\"hidden\" value=\"".$cp->resPlastic."\" name=\"res_plastic\" />";
      echo "<input type=\"hidden\" value=\"".$cp->resFuel."\" name=\"res_fuel\" />";
      echo "<input type=\"hidden\" value=\"".$cp->resFood."\" name=\"res_food\" />";		
      
      // Anzahl Gebote pro Kategorie		
			echo "<input type=\"hidden\" value=\"".$cnt_arr['ress_cnt']."\" name=\"ressource_cnt\" />";	
			echo "<input type=\"hidden\" value=\"".$cnt_arr['ship_cnt']."\" name=\"ship_cnt\" />";	
			echo "<input type=\"hidden\" value=\"".$cnt_arr['auction_cnt']."\" name=\"auction_cnt\" />";	
			
			// XAJAX übergibt SQL-String an Fomular
			echo "<input type=\"hidden\" value=\"\" id=\"ressource_sql_add\" name=\"ressource_sql_add\"/>";		
			echo "<input type=\"hidden\" value=\"\" id=\"ship_sql_add\" name=\"ship_sql_add\"/>";	
			echo "<input type=\"hidden\" value=\"\" id=\"auction_sql_add\" name=\"auction_sql_add\"/>";
			
			tableStart("Suche");
			
			// Kategorie
			echo "<tr><td>";
			echo "<div id=\"search_cat_field\" style=\"text-align:center;vertical-align:middle;height:30px;\">
							Kategorie:
							<select id=\"search_cat\" name=\"search_cat\" onchange=\"xajax_MarketSearchFormularShow(xajax.getFormValues('search_selector'));\">
								<option value=\"0\">keine</option>";
								if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
								{
									echo "<option value=\"ressource\">Rohstoffe (".$cnt_arr['ress_cnt'].")</option>";
								}
								if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
								{
									echo "<option value=\"ship\">Schiffe (".$cnt_arr['ship_cnt'].")</option>";
								}
								if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
								{
									echo "<option value=\"auction\">Auktionen (".$cnt_arr['auction_cnt'].")</option>";
								}
					echo "</select>
						</div>";
			echo "</td></tr><tr><td>";
			// Content
			echo "<div id=\"search_content\">
							&nbsp;
						</div>";
			
			// Check Message
			echo "<div id=\"search_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">
							<div style=\"color:red;font-weight:bold;\">Wähle eine Kategorie!</div>
						</div>";
			echo "</td></tr><tr><td style=\"text-align:center\">";
			// Sumbit
			echo "<input type=\"submit\" class=\"button\" name=\"search_submit\" id=\"search_submit\" value=\"Angebote anzeigen\" disabled=\"disabled\"/>";
			echo "</td></tr>";
			tableEnd();
														

			echo "</form>";
		}	



		//
		// Eigene Angebote anzeigen
		//
		elseif ($mode=="user_sell")
		{
			$return_factor = 1 - (1/(MARKET_LEVEL+1));

			// Schiffangebot löschen
			if (isset($_POST['ship_cancel']))
			{
				$scres=dbquery("
				SELECT
				 	* 
				FROM 
					market_ship 
				WHERE 
					ship_market_id='".$_POST['ship_market_id']."' 
					AND user_id='".$cu->id."'");
					
				if (mysql_num_rows($scres)>0)
				{
					$scrow=mysql_fetch_array($scres);
					dbquery("
					UPDATE 
						shiplist 
					SET 
						shiplist_count=shiplist_count+'".(floor($scrow['ship_count']*$return_factor))."' 
					WHERE 
						shiplist_user_id='".$scrow['user_id']."' 
						AND shiplist_entity_id='".$scrow['planet_id']."' 
						AND shiplist_ship_id='".$scrow['ship_id']."'");
						
					dbquery("
					DELETE FROM 
						market_ship 
					WHERE 
						ship_market_id='".$_POST['ship_market_id']."'");
					ok_msg("Angebot wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Schiffe zur&uuml;ck erhalten (es wird abgerundet)");
				}
				else
				{
					error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
					return_btn(array("mode"=>"user_sell"));
				}
			}

			// Rohstoffangebot löschen
			elseif (isset($_POST['ressource_cancel']))
			{
				$rcres=dbquery("
				SELECT 
					* 
				FROM 
					market_ressource 
				WHERE 
					ressource_market_id='".$_POST['ressource_market_id']."' 
					AND user_id='".$cu->id."'");
					
				if (mysql_num_rows($rcres)>0)
				{
                	// TODO: use planet class
					$rcrow=mysql_fetch_array($rcres);
					dbquery("
					UPDATE 
						planets
					SET 
						planet_res_metal=planet_res_metal+'".($rcrow['sell_metal']*$return_factor)."',
						planet_res_crystal=planet_res_crystal+'".($rcrow['sell_crystal']*$return_factor)."',
						planet_res_plastic=planet_res_plastic+'".($rcrow['sell_plastic']*$return_factor)."',
						planet_res_fuel=planet_res_fuel+'".($rcrow['sell_fuel']*$return_factor)."',
						planet_res_food=planet_res_food+'".($rcrow['sell_food']*$return_factor)."'
					WHERE 
						planet_user_id='".$rcrow['user_id']."' 
						AND id='".$rcrow['planet_id']."'");
						
					dbquery("
					DELETE FROM 
						market_ressource 
					WHERE 
						ressource_market_id='".$_POST['ressource_market_id']."'");
						
					add_log(7,"Der Spieler ".$cu->nick." zieht folgendes Rohstoffangebot zur&uuml;ck: \n\n".RES_METAL.": ".$rcrow['sell_metal']."\n".RES_CRYSTAL.": ".$rcrow['sell_crystal']."\n".RES_PLASTIC.": ".$rcrow['sell_plastic']."\n".RES_FUEL.": ".$rcrow['sell_fuel']."\n".RES_FOOD.": ".$rcrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());
					ok_msg("Angebot wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Rohstoffe zur&uuml;ck erhalten!");
				}
				else
				{
					error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
					return_btn(array("mode"=>"user_sell"));
				}
			}

			//Auktionen löschen
			elseif(isset($_POST['auction_cancel']))
			{
				$acres=dbquery("
				SELECT 
					* 
				FROM 
					market_auction 
				WHERE 
					auction_market_id='".$_POST['auction_market_id']."' 
					AND auction_user_id='".$cu->id."'");
				if (mysql_num_rows($acres)>0)
				{
					// Rohstoffe zurückgeben
					$acrow=mysql_fetch_array($acres);
                	// TODO: use planet class
					dbquery("
					UPDATE 
						planets
					SET
            planet_res_metal=planet_res_metal+'".($acrow['auction_sell_metal']*$return_factor)."',
            planet_res_crystal=planet_res_crystal+'".($acrow['auction_sell_crystal']*$return_factor)."',
            planet_res_plastic=planet_res_plastic+'".($acrow['auction_sell_plastic']*$return_factor)."',
            planet_res_fuel=planet_res_fuel+'".($acrow['auction_sell_fuel']*$return_factor)."',
            planet_res_food=planet_res_food+'".($acrow['auction_sell_food']*$return_factor)."'
					WHERE
						planet_user_id='".$acrow['auction_user_id']."'
						AND id='".$acrow['auction_planet_id']."'");

					//Auktion löschen
					dbquery("DELETE FROM market_auction WHERE auction_market_id='".$_POST['auction_market_id']."'");

					add_log(7,"Der Spieler ".$cu->nick." zieht folgende Auktion zur&uuml;ck:\nRohstoffe:\n".RES_METAL.": ".$acrow['sell_metal']."\n".RES_CRYSTAL.": ".$acrow['sell_crystal']."\n".RES_PLASTIC.": ".$acrow['sell_plastic']."\n".RES_FUEL.": ".$acrow['sell_fuel']."\n".RES_FOOD.": ".$acrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());

					ok_msg("Auktion wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Waren zur&uuml;ck erhalten (es wird abgerundet)!");
				}
				else
				{
					error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
					return_btn(array("mode"=>"user_sell"));
				}

			}

			// Eigene Angebote zeigen
			else
			{
				$cstr=checker_init();

				//
				// Rohstoffe
				//
				$res=dbquery("
				SELECT 
					* 
				FROM 
					market_ressource 
				WHERE 
					user_id='".$cu->id."' 
					AND ressource_buyable='1' 
				ORDER BY 
					datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					tableStart("Rohstoffe");
						echo "<tr><th colspan=\"2\" width=\"25%\">Angebot:</th>
						<th width=\"15%\">Anbieter:</th>
						<th width=\"25%\">Datum/Text:</th>
						<th colspan=\"2\" width=\"25%\">Preis:</th>
						<th width=\"10%\">Zur&uuml;ckziehen:</th></tr>";
					$cnt=0;
					while ($row=mysql_fetch_array($res))
					{
						if($row['ressource_for_alliance']!=0)
							$for_alliance="<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
						else
							$for_alliance="";

						echo "<tr><td><b>".RES_METAL."</b>:</td><td>".nf($row['sell_metal'])."</td>";
						echo "<td rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$row['user_id']."\">".get_user_nick($row['user_id'])."</a></td>";
						echo "<td rowspan=\"5\">".date("d.m.Y  G:i:s", $row['datum'])."<br/><br/>".stripslashes($row['ressource_text'])."</td>";
						echo "<td><b>".RES_METAL."</b>:</td><td>".nf($row['buy_metal'])."</td>";
						echo "<td rowspan=\"5\"><input type=\"radio\" name=\"ressource_market_id\" value=\"".$row['ressource_market_id']."\"><br/><br/>".$for_alliance."</td></tr>";

						echo "<tr><td><b>".RES_CRYSTAL."</b>:</td><td>".nf($row['sell_crystal'])."</td>";
						echo "<td><b>".RES_CRYSTAL."</b>:</td><td>".nf($row['buy_crystal'])."</td></tr>";
						echo "<tr><td><b>".RES_PLASTIC."</b>:</td><td>".nf($row['sell_plastic'])."</td>";
						echo "<td><b>".RES_PLASTIC."</b>:</td><td>".nf($row['buy_plastic'])."</td></tr>";
						echo "<tr><td><b>".RES_FUEL."</b>:</td><td>".nf($row['sell_fuel'])."</td>";
						echo "<td><b>".RES_FUEL."</b>:</td><td>".nf($row['buy_fuel'])."</td></tr>";
						echo "<tr><td><b>".RES_FOOD."</b>:</td><td>".nf($row['sell_food'])."</td>";
						echo "<td><b>".RES_FOOD."</b>:</td><td>".nf($row['buy_food'])."</td></tr>";
						$cnt++;
						if ($cnt<mysql_num_rows($res))
							echo "<tr><td colspan=\"7\" style=\"height:10px;background:#000\"></td></tr>";
					}
					tableEnd();
					echo "<input type=\"submit\" class=\"button\" name=\"ressource_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
					echo "</form><br/><br/>";
				}
				else
				{
					iBoxStart("Rohstoffe");
					echo "Keine Angebote vorhanden!";
					iBoxEnd();
				}
				
				
				//
				// Schiffe
				//
				$res=dbquery("
				SELECT 
					* 
				FROM 
					market_ship 
				WHERE 
					user_id='".$cu->id."' 
					AND ship_buyable='1' 
				ORDER BY 
					datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					tableStart("Schiffe");

                    echo "<tr><th width=\"25%\">Angebot:</th>
                    <th width=\"15%\">Anbieter:</th>
                    <th width=\"25%\">Datum/Text:</th>
                    <th colspan=\"2\" width=\"25%\">Preis:</th>
                    <th width=\"10%\">Zur&uuml;ckziehen:</th></tr>";

					$cnt=0;
					while ($arr=mysql_fetch_array($res))
					{
						if($arr['ship_for_alliance']!=0)
							$for_alliance="<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
						else
							$for_alliance="";

						echo "<tr><td rowspan=\"5\">".$arr['ship_count']." <a href=\"?page=help&site=shipyard&id=".$arr['ship_id']."\">".$arr['ship_name']."</a></td>";
						echo "<td rowspan=\"5\"><a href=\"?page=userinfo&amp;id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a></td>";
						echo "<td rowspan=\"5\">".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['ship_text'])."</td>";
						echo "<td><b>".RES_METAL."</b>:</td><td>".nf($arr['ship_costs_metal'])."</td>";
						echo "<td rowspan=\"5\"><input type=\"radio\" name=\"ship_market_id\" value=\"".$arr['ship_market_id']."\"><br/><br/>".$for_alliance."</td></tr>";

						echo "<tr><td><b>".RES_CRYSTAL."</b>:</td><td>".nf($arr['ship_costs_crystal'])."</td></tr>";
						echo "<tr><td><b>".RES_PLASTIC."</b>:</td><td>".nf($arr['ship_costs_plastic'])."</td></tr>";
						echo "<tr><td><b>".RES_FUEL."</b>:</td><td>".nf($arr['ship_costs_fuel'])."</td></tr>";
						echo "<tr><td><b>".RES_FOOD."</b>:</td><td>".nf($arr['ship_costs_food'])."</td></tr>";
						$cnt++;
						if ($cnt<mysql_num_rows($res))
							echo "<tr><td colspan=\"6\" style=\"height:10px;background:#000\"></td></tr>";
					}
					tableEnd();
					echo "<input type=\"submit\" class=\"button\" name=\"ship_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
					echo "</form><br/><br/>";
				}
				else
				{
					iBoxStart("Schiffe");
					echo "Keine Angebote vorhanden!";
					iBoxEnd();
				}



				//
				// Auktionen
				//
				$res=dbquery("
				SELECT 
					* 
				FROM 
					market_auction 
				WHERE 
					auction_user_id='".$cu->id."' 
				ORDER BY 
					auction_buyable DESC, 
					auction_end ASC");
				if (mysql_num_rows($res)>0)
				{
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
					
					
					echo "<form action=\"?page=".$page."&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					iBoxStart("Auktionen");
					iBoxEnd();
					$cnt=0;
					$acnts=array();
					$acnt=0;
					while ($arr=mysql_fetch_array($res))
					{
						$acnt++;
						tableStart();
						echo "<tr>
						<th>Anbieter</th>
						<th>Auktion Start/Ende</th>
						<th colspan=\"2\">Angebot</th>
						<th>Zur&uuml;ckziehen</th></tr>";

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
						elseif($rest_time<=0)
						{
							$class = "class=\"tbldata\"";
							$rest_time = "Auktion beendet!";
						}
						else
						{
							$class = "class=\"tbldata\"";
							$rest_time = "Noch ".$t."t ".$h."h ".$m."m ".$sec."s";
						}


						echo "<tr>
										<td rowspan=\"5\">
											<a href=\"?page=userinfo&amp;id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a>
										</td>
										<td>
											Start ".date("d.m.Y  G:i:s", $arr['auction_start'])."
										</td>
										<td><b>".RES_METAL."</b>:</td>
										<td>".nf($arr['auction_sell_metal'])."</td>";

						 // Zurückzieh button wenn noch niemand geboten hat
						if($arr['auction_current_buyer_id']==0)
						{
               echo "<td rowspan=\"5\">
               				<input type=\"radio\" name=\"auction_market_id\" value=\"".$arr['auction_market_id']."\">
               			</td>
               		</tr>";
            }
            elseif($arr['auction_buyable']==0)
            {
            	echo "<td class=\"resfullcolor\" rowspan=\"5\">Verkauft!</td>
            		</tr>";
            }
            else
            {
            	 echo "<td rowspan=\"5\">Es wurde bereits geboten</td>
            	 </tr>";
            }


						// Start/Ende Anzeigen sofern die auktion nicht schon beendet ist
						if($arr['auction_delete_date']==0)
						{
							$acnts['countdown'.$acnt]=$arr['auction_end']-time();
							echo "<tr><td>Ende ".date("d.m.Y  G:i:s", $arr['auction_end'])."</td><td><b>".RES_CRYSTAL."</b>:</td><td>".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td $class rowspan=\"3\" id=\"countdown".$acnt."\">".$rest_time."</td><td><b>".RES_PLASTIC."</b>:</td><td>".nf($arr['auction_sell_plastic'])."</td></tr>";
						}
						// sonst das löschdatum anzeigen
						else
						{
							$delete_rest_time = $arr['auction_delete_date']-time();

              $t = floor($delete_rest_time / 3600 / 24);
              $h = floor(($delete_rest_time) / 3600);
              $m = floor(($delete_rest_time-($h*3600))/60);
              $sec = floor(($delete_rest_time-($h*3600)-($m*60)));

							// Gibt Nachricht aus, wenn Löschzeit erreicht oder überschritten
							if($delete_rest_time<=0)
							{
								$delete_time = "Gebot wird gelöscht...";
							}
							// und sonst wird Zeit bis zur Löschung angezeigt
							else
							{
								$delete_time = "Gebot wird nach ".$h." Stunden und ".$m." Minuten gel&ouml;scht";
							}
							echo "<tr><td>Auktion beendet</td><td><b>".RES_CRYSTAL."</b>:</td><td>".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td rowspan=\"3\">".$delete_time."</td><td><b>".RES_PLASTIC."</b>:</td><td>".nf($arr['auction_sell_plastic'])."</td></tr>";
						}


						echo "<tr><td><b>".RES_FUEL."</b>:</td><td>".nf($arr['auction_sell_fuel'])."</td></tr>";
						echo "<tr><td><b>".RES_FOOD."</b>:</td><td>".nf($arr['auction_sell_food'])."</td></tr>";

						//Hochstgebot Anzeigen wenn schon geboten worden ist
						if($arr['auction_current_buyer_id']!=0)
						{
              echo "<tr>
              				<th colspan=\"5\">H&ouml;chstgebot</th>
              			</tr>
              			<tr>
              				<td rowspan=\"5\">
              					<a href=\"?page=userinfo&amp;id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a>
              				</td>
              				<td rowspan=\"5\">Geboten ".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."</td>
              				<td><b>".RES_METAL."</b>:</td><td>".nf($arr['auction_buy_metal'])."</td>";

							// meldung geben, falls der bietende, das maximum erreicht hat
              if($arr['auction_buyable']==1)
              {
                  echo "<td class=\"resfullcolor\" rowspan=\"5\">&nbsp;</td></tr>";
              }
              else
              {
                   echo "<td class=\"resfullcolor\" rowspan=\"5\">&nbsp;</td></tr>";
              }

              echo "<tr>
              				<td><b>".RES_CRYSTAL."</b>:</td>
              				<td>".nf($arr['auction_buy_crystal'])."</td>
              			</tr>
              			<tr>
              				<td><b>".RES_PLASTIC."</b>:</td>
              				<td>".nf($arr['auction_buy_plastic'])."</td>
              			</tr>
              			<tr>
              				<td><b>".RES_FUEL."</b>:</td>
              				<td>".nf($arr['auction_buy_fuel'])."</td>
              			</tr>
              			<tr>
              				<td><b>".RES_FOOD."</b>:</td>
              				<td>".nf($arr['auction_buy_food'])."</td>
              			</tr>";
            }
            tableEnd();
            echo "<br/><br/>";
            
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
					iBoxStart("Auktionen");
					echo "Keine Angebote vorhanden!";
					iBoxEnd(0);
				}
			}
		}



		//
		// Angebote aufgeben
		//
		else
		{	

			// Angebotsmaske Darstellen falls noch Angebote aufgegeben werden können
			if ($possible>0)
			{
		
				//
				// Rohstoffe
				//
				if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
				{		
          //Hier wird das ganze für die Rohstoffe noch angezeigt
          echo "<form action=\"?page=".$page."\" method=\"post\" name=\"ress_selector\" id=\"ress_selector\">\n";
          $cstr=checker_init();         
          
          tableStart("Rohstoffe verkaufen");
          

					//Header
					echo "<tr>
									<th style=\"width:15%;vertical-align:middle;\">Rohstoff";
									
				
				          //Roshtoff übergabe an xajax (da die $c-variabeln nicht abgerufen werden könnne)
				          echo "<input type=\"hidden\" value=\"".$cp->resMetal."\" name=\"res_metal\" id=\"res_metal\"/>";
				          echo "<input type=\"hidden\" value=\"".$cp->resCrystal."\" name=\"res_crystal\" id=\"res_crystal\"/>";
				          echo "<input type=\"hidden\" value=\"".$cp->resPlastic."\" name=\"res_plastic\" id=\"res_plastic\"/>";
				          echo "<input type=\"hidden\" value=\"".$cp->resFuel."\" name=\"res_fuel\" id=\"res_fuel\"/>";
				          echo "<input type=\"hidden\" value=\"".$cp->resFood."\" name=\"res_food\" id=\"res_food\" />";
				          
				          // Vor dem Absenden des Formulars, wird die Überprüfung noch einmal gestartet. Bevor diese nicht das "OK" gibt, kann nicht gesendet werden
				          echo "<input type=\"hidden\" value=\"0\" name=\"ress_last_update\" id=\"ress_last_update\"/>";

									echo "</th>
									<th style=\"width:10%;vertical-align:middle;\">Angebot</th>
									<th style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</th>
									<th style=\"width:10%;vertical-align:middle;\">Preis</th>
									<th style=\"width:40%;vertical-align:middle;\">Min./Max.</th>
								</tr>";
					// Titan
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_METAL.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_metal\" id=\"ress_sell_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');calcMarketRessPrice('0');\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</th>
									<td id=\"ress_buy_metal_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_metal\" id=\"ress_buy_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
									</td>
									<th id=\"ress_min_max_metal\" style=\"vertical-align:middle;\"> - </th>
								</tr>";
								
					// Silizium
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_crystal\" id=\"ress_sell_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');calcMarketRessPrice('0');\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</th>
									<td id=\"ress_buy_crystal_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_crystal\" id=\"ress_buy_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\"  disabled=\"disabled\"/>
									</td>
									<th id=\"ress_min_max_crystal\" style=\"vertical-align:middle;\"> - </th>
								</tr>";		
					// PVC
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_PLASTIC.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_plastic\" id=\"ress_sell_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');calcMarketRessPrice('0');\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</th>
									<td id=\"ress_buy_plastic_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_plastic\" id=\"ress_buy_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
									</td>
									<th id=\"ress_min_max_plastic\" style=\"vertical-align:middle;\"> - </th>
								</tr>";	
					// Tritium
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_fuel\" id=\"ress_sell_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');calcMarketRessPrice('0');\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</th>
									<td id=\"ress_buy_fuel_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_fuel\" id=\"ress_buy_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
									</td>
									<th id=\"ress_min_max_fuel\" style=\"vertical-align:middle;\"> - </th>
								</tr>";
					// Nahrung
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_sell_food\" id=\"ress_sell_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');calcMarketRessPrice('0');\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</th>
									<td id=\"ress_buy_food_field\" style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"ress_buy_food\" id=\"ress_buy_food\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
									</td>
									<th id=\"ress_min_max_food\" style=\"vertical-align:middle;\"> - </th>
								</tr>";		
								
          //Verkaufstext und für Allianzmitglied reservieren
          echo "<tr>
          				<th colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">Beschreibung und Reservation</th>
          			</tr>
          			<tr>
          				<td colspan=\"4\" style=\"vertical-align:middle;\">
          					<input type=\"text\" value=\"\" name=\"ressource_text\" id=\"ressource_text\" size=\"55\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"calcMarketRessPrice('0');\"/>	
          				</td>";
          //Für Allianzmitglied reservieren wenn in einer Allianz und diese den Allianzmarktplatz auf Stufe 1 oder höher hat
          if($cu->allianceId!=0 && $alliance_market_level>=1 && !$cd_enabled)
          {
            echo "<td colspan=\"1\" style=\"vertical-align:middle;\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen").">
            				<input type=\"checkbox\" name=\"ressource_for_alliance\" value=\"1\" /> F&uuml;r Allianzmitglieder Reservieren
            			</td>
            		</tr>";
          }
          else
          {
            echo "<td colspan=\"1\" style=\"vertical-align:middle;\">&nbsp;</td></tr>";
          }			
          					
					// Status Nachricht (Ajax Überprüfungstext)
					echo "<tr>
									<td colspan=\"5\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
								</tr>";								

          tableEnd();
          
          // Absend-Button (Per Ajax freigegeben)
          echo "<input type=\"button\" class=\"button\" name=\"ressource_sell_submit\" id=\"ressource_sell_submit\" value=\"Angebot aufgeben\" style=\"color:#f00;\" disabled=\"disabled\" onclick=\"calcMarketRessPrice('1');checkUpdate('ress_selector', 'ress_last_update');\"/></form><br/><br/>";
        }		
				
				

				//
				// Schiffe
				//
				if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
				{
					$check_res = dbquery("
					SELECT 
						COUNT(*)
					FROM 
						shiplist 
					WHERE 
						shiplist_entity_id='".$cp->id()."'");
					
          //Zuerst wird überprüft ob auf dem Planeten Schiffe sind
          if (mysql_result($check_res,0)>0)
          {
          	// Folgender Javascript Abschnitt, welcher von PHP-Teilen erzeugt wird, lädt die Daten von den Schiffen, welche sich auf dem aktuellen Planeten befinden, in ein JS-Array. Dies wird für die Preisberechnung benötigt. Das erzeugte PHP Array wird für die Schiffsauswahl (SELECT) verwendet.   	
          	
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
                shiplist
                INNER JOIN
                ships
                ON shiplist.shiplist_ship_id=ships.ship_id
            WHERE
            		shiplist.shiplist_entity_id='".$cp->id()."'
                AND shiplist.shiplist_count>'0'
                AND ships.special_ship='0'
				AND ships.ship_alliance_costs='0'
            ORDER BY
                ships.ship_name;");
                
            echo "<script type=\"text/javascript\">";
            
            echo "ships = new Array();\n";
            $ships=array();
            while ($sarr=mysql_fetch_array($sres))
            {
            	echo "ships[".$sarr['ship_id']."] = new Object();\n";
            	echo "ships[".$sarr['ship_id']."][\"name\"] = \"".$sarr['ship_name']."\";";
            	echo "ships[".$sarr['ship_id']."]['costs_metal'] = ".$sarr['ship_costs_metal'].";";
            	echo "ships[".$sarr['ship_id']."]['costs_crystal'] = ".$sarr['ship_costs_crystal'].";";
            	echo "ships[".$sarr['ship_id']."]['costs_plastic'] = ".$sarr['ship_costs_plastic'].";";
            	echo "ships[".$sarr['ship_id']."]['costs_fuel'] = ".$sarr['ship_costs_fuel'].";";
            	echo "ships[".$sarr['ship_id']."]['costs_food'] = ".$sarr['ship_costs_food'].";";
            	echo "ships[".$sarr['ship_id']."][\"count\"] = ".$sarr['shiplist_count'].";";
            	
            	$ships[$sarr['ship_id']]=$sarr;
            }

						echo "</script>";
						
	          echo "<form action=\"?page=".$page."\" method=\"post\" name=\"ship_selector\" id=\"ship_selector\">\n";
	          echo $cstr;

						// Vor dem Absenden des Formulars, wird die Überprüfung noch einmal gestartet. Bevor diese nicht das "OK" gibt, kann nicht gesendet werden
        		echo "<input type=\"hidden\" value=\"0\" name=\"ship_last_update\" id=\"ship_last_update\"/>";		
        		
        		// Übergibt den Schiffsnamen zum Spichern an PHP weiter	
        		echo "<input type=\"hidden\" value=\"0\" name=\"ship_name\" id=\"ship_name\"/>";	

	          tableStart("Schiffe verkaufen");
		
						// Header Angebot
						echo "<tr>
										<td height=\"30\" colspan=\"3\" style=\"vertical-align:middle;\">
											<select name=\"ship_list\" id=\"ship_list\" onchange=\"calcMarketShipPrice(1, 0);\">";
											// Listet alle vorhandenen Schiffe auf
				              foreach ($ships as $sarr)
				              {
				                echo "<option value=\"".$sarr['ship_id']."\">".$sarr['ship_name']." (".$sarr['shiplist_count'].")</option>";
				              }
            		echo "</select>
            				</td>
            				<td height=\"30\" colspan=\"2\" style=\"vertical-align:middle;\">
            					<input type=\"text\" value=\"0\" name=\"ship_count\" id=\"ship_count\" size=\"5\" maxlength=\"7\" onkeyup=\"calcMarketShipPrice(1, 0);\" /> St&uuml;ck
            				</td>
            			</tr>";										
															
						
						//Header Preis
						echo "<tr>
										<th style=\"width:15%;vertical-align:middle;\">Rohstoff</th>
										<th style=\"width:10%;vertical-align:middle;\">Angebot</th>
										<th style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</th>
										<th style=\"width:10%;vertical-align:middle;\">Preis</th>
										<th style=\"width:40%;vertical-align:middle;\">Min./Max.</th>
									</tr>";
						// Titan
						echo "<tr>
										<th style=\"vertical-align:middle;\">".RES_METAL.":</th>
										<td id=\"ship_sell_metal_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_sell_metal\" id=\"ship_sell_metal\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
										</td>		
										<th style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</th>
										<td id=\"ship_buy_metal_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_buy_metal\" id=\"ship_buy_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
										</td>
										<th id=\"ship_min_max_metal\" style=\"vertical-align:middle;\"> - </th>
									</tr>";
						// Silizium
						echo "<tr>
										<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
										<td id=\"ship_sell_crystal_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_sell_crystal\" id=\"ship_sell_crystal\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
										</td>	
										<th style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</th>
										<td id=\"ship_buy_crystal_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_buy_crystal\" id=\"ship_buy_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
										</td>
										<th id=\"ship_min_max_crystal\" style=\"vertical-align:middle;\"> - </th>
									</tr>";		
						// PVC
						echo "<tr>
										<th style=\"vertical-align:middle;\">".RES_PLASTIC.":</th>
										<td id=\"ship_sell_plastic_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_sell_plastic\" id=\"ship_sell_plastic\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
										</td>	
										<th style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</th>
										<td id=\"ship_buy_plastic_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_buy_plastic\" id=\"ship_buy_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
										</td>
										<th id=\"ship_min_max_plastic\" style=\"vertical-align:middle;\"> - </th>
									</tr>";	
						// Tritium
						echo "<tr>
										<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
										<td id=\"ship_sell_fuel_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_sell_fuel\" id=\"ship_sell_fuel\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
										</td>	
										<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</th>
										<td id=\"ship_buy_fuel_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_buy_fuel\" id=\"ship_buy_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
										</td>
										<th id=\"ship_min_max_fuel\" style=\"vertical-align:middle;\"> - </th>
									</tr>";
						// Nahrung
						echo "<tr>
										<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
										<td id=\"ship_sell_food_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_sell_food\" id=\"ship_sell_food\" size=\"7\" maxlength=\"15\" disabled=\"disabled\"/>
										</td>		
										<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</th>
										<td id=\"ship_buy_food_field\" style=\"vertical-align:middle;\">
											<input type=\"text\" value=\"0\" name=\"ship_buy_food\" id=\"ship_buy_food\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
										</td>
										<th id=\"ship_min_max_food\" style=\"vertical-align:middle;\"> - </th>
									</tr>";		
									
	          //Verkaufstext und für Allianzmitglied reservieren
	          echo "<tr>
	          				<th colspan=\"5\" style=\"text-align:center;vertical-align:middle;\">Beschreibung und Reservation</td>
	          			</tr>
	          			<tr>
	          				<td colspan=\"4\" style=\"vertical-align:middle;\">
	          					<input type=\"text\" value=\"\" name=\"ship_text\" id=\"ship_text\" size=\"55\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"calcMarketShipPrice(0, 0);\"/>	
	          				</td>";
	          //Für Allianzmitglied reservieren wenn in einer Allianz und diese den Allianzmarktplatz auf Stufe 2 oder höher hat
          	if($cu->allianceId!=0 && $alliance_market_level>=2 && !$cd_enabled)
          	{
	            echo "<td colspan=\"1\" style=\"vertical-align:middle;\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen").">
	            				<input type=\"checkbox\" name=\"ship_for_alliance\" value=\"1\"/> F&uuml;r Allianzmitglieder Reservieren
	            			</td>
	            		</tr>";
	          }
	          else
	          {
	            echo "<td colspan=\"1\" style=\"vertical-align:middle;\">&nbsp;</td></tr>";
	          }			
	          					
						// Status Nachricht (Ajax Überprüfungstext)
						echo "<tr>
										<td colspan=\"5\" id=\"ship_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
									</tr>";								
	
	          tableEnd();
	          
	          // Absend-Button (Per Ajax freigegeben)
	          echo "<input type=\"button\" class=\"button\" name=\"ship_sell_submit\" id=\"ship_sell_submit\" value=\"Angebot aufgeben\" style=\"color:#f00;\" disabled=\"disabled\" onclick=\"calcMarketShipPrice(0, 1);checkUpdate('ship_selector', 'ship_last_update');\"/></form><br/><br/>";
 
          }
        }
        


				//
				// Auktionen
				//
				if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
				{   
          echo "<form action=\"?page=".$page."\" method=\"post\" name=\"auction_selector\" id=\"auction_selector\">\n";
          echo $cstr;
          tableStart("Rohstoffe versteigern");
          
          // Frühstes Auktionsende
          $auction_time = time() + (AUCTION_MIN_DURATION*24*3600);
          

					//Header
					echo "<tr>
									<th style=\"width:15%;vertical-align:middle;\">Rohstoff";
									
					          // Min. Auktionsende an XAJAX weitergeben
					          echo "<input type=\"hidden\" value=\"".$auction_time."\" name=\"auction_time_min\" id=\"auction_time_min\"/>";
					          
					          //Roshtoff übergabe an XAJAX (da die $c-variabeln nicht abgerufen werden könnnen)
					          echo "<input type=\"hidden\" value=\"".$cp->resMetal."\" name=\"res_metal\" />";
					          echo "<input type=\"hidden\" value=\"".$cp->resCrystal."\" name=\"res_crystal\" />";
					          echo "<input type=\"hidden\" value=\"".$cp->resPlastic."\" name=\"res_plastic\" />";
					          echo "<input type=\"hidden\" value=\"".$cp->resFuel."\" name=\"res_fuel\" />";
					          echo "<input type=\"hidden\" value=\"".$cp->resFood."\" name=\"res_food\" />";
					          
					          //Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
					          echo "<input type=\"hidden\" value=\"0\" name=\"auction_last_update\" id=\"auction_last_update\"/>";
									
									
						echo "</th>
									<th style=\"width:10%;vertical-align:middle;\">Angebot</t>
									<th style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</th>
									<th style=\"width:5%;vertical-align:middle;\">Preis</th>
									<th colspan=\"2\" style=\"width:45%;text-align:center;vertical-align:middle;\">Zeit</th>
								</tr>";
					// Titan
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_METAL.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_metal\" id=\"auction_sell_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');checkMarketAuctionFormular(0);\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</th>
									<td id=\"auction_buy_metal_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_metal\" id=\"auction_buy_metal\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
									</td>
									<th colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</th>
								</tr>";												
					// Silizium und "Dauer" Feld
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_crystal\" id=\"auction_sell_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');checkMarketAuctionFormular(0);\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</th>
									<td id=\"auction_buy_crystal_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_crystal\" id=\"auction_buy_crystal\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
									</td>
									<th style=\"vertical-align:middle;\">Dauer:</th>
									<td name=\"auction_time_field\" id=\"auction_time_field\" style=\"vertical-align:middle;\">
										".AUCTION_MIN_DURATION." Tage + ";
				
					          //... in Tagen ...
					          echo "<select name=\"auction_time_days\" id=\"auction_time_days\" onchange=\"checkMarketAuctionFormular(0);\">";
					          for($x=0;$x<=10;$x++)
					          {
					                  echo "<option value=\"".$x."\">".$x."</option>";
					          }
					          echo "</select> Tage und ";
					
					          //... und in Stunden
					          echo "<select name=\"auction_time_hours\" id=\"auction_time_hours\" onchange=\"checkMarketAuctionFormular(0);\">";
					          for($x=0;$x<=24;$x++)
					          {
					                  echo "<option value=\"".$x."\">".$x."</option>";
					          }

					          echo "</select> Stunden";														
						echo "</td>					
								</tr>";										
					// PVC
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_PLASTIC.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_plastic\" id=\"auction_sell_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');checkMarketAuctionFormular(0);\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</th>
									<td id=\"auction_buy_plastic_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_plastic\" id=\"auction_buy_plastic\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
									</td>
									<th colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</th>
								</tr>";	
					// Tritium und "Ende" Feld
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_fuel\" id=\"auction_sell_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');checkMarketAuctionFormular(0);\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</th>
									<td id=\"auction_buy_fuel_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_fuel\" id=\"auction_buy_fuel\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
									</td>
									<th style=\"vertical-align:middle;\">Ende:</th>
									<td id=\"auction_end_time\" style=\"vertical-align:middle;\">".date("d.m.Y H:i",$auction_time)."</td>										
								</tr>";
					// Nahrung
					echo "<tr>
									<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
									<td style=\"vertical-align:middle;\">
										<input type=\"text\" value=\"0\" name=\"auction_sell_food\" id=\"auction_sell_food\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');checkMarketAuctionFormular(0);\"/>
									</td>
									<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</th>
									<td id=\"auction_buy_food_field\" style=\"text-align:center;vertical-align:middle;\">
										<input type=\"checkbox\" name=\"auction_buy_food\" id=\"auction_buy_food\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
									</td>
									<th colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</th>
								</tr>";	
          //Verkaufstext und für Allianzmitglied reservieren
          echo "<tr>
          				<th colspan=\"6\" style=\"text-align:center;vertical-align:middle;\">Beschreibung</th>
          			</tr>
          			<tr>
          				<td colspan=\"6\" style=\"text-align:center;vertical-align:middle;\">
          					<input type=\"text\" value=\"\" name=\"auction_text\" id=\"auction_text\" size=\"100\" maxlength=\"60\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"checkMarketAuctionFormular(0);\"/>	
          				</td>
          			</tr>";												
					// Status Nachricht (Ajax Überprüfungstext)
					echo "<tr>
									<td colspan=\"6\" name=\"auction_check_message\" id=\"auction_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
								</tr>";		
														
          tableEnd();
          
          // Absend-Button (Per Ajax freigegeben)
          echo "<input type=\"button\" class=\"button\" name=\"auction_sell_submit\" id=\"auction_sell_submit\" value=\"Angebot aufgeben\" style=\"color:#f00;\" disabled=\"disabled\" onclick=\"checkMarketAuctionFormular(1);checkUpdate('auction_selector', 'auction_last_update');\"/></form><br/><br/>";						
												
				}
			}
			else
			{
				error_msg("Auf diesem Planeten können keine weiteren Angebote erstellt werden!");
			}
		}
		
		if ($cd_enabled)
		{
			countDown("cdcd",$cu->alliance->getMarketCooldown());
		}

	//
	// Meldung dass noch kein Marktplatz gebaut wurde
	//
	}
	else
	{
		error_msg("Der Marktplatz wurde noch nicht gebaut.");
	}
		


?>
