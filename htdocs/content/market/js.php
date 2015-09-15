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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
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
          
          			//Die %-Zahl ermitteln und in Variable speichern
          			var ship_percent = document.getElementById('ship_percent').value;

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

					// % checken
				  if(istZahl(ship_percent)==false || ship_percent=='')
					{
						ship_percent = 100;
						document.getElementById('ship_percent').value=ship_percent;
					}
					ship_percent /= 100;
					
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
				  	    ship_buy_metal = Math.round(ship_costs_metal_total*ship_percent);
				  	    ship_buy_crystal = Math.round(ship_costs_crystal_total*ship_percent);
				    	ship_buy_plastic = Math.round(ship_costs_plastic_total*ship_percent);
				    	ship_buy_fuel = Math.round(ship_costs_fuel_total*ship_percent);
				  	    ship_buy_food = Math.round(ship_costs_food_total*ship_percent);

					  	//Ändert Daten beim "Angebot Feld" welches gesperrt ist für Änderungen
					  	document.getElementById('ship_sell_metal').value=FormatNumber('return',ship_costs_metal_total,'','','');
					  	document.getElementById('ship_sell_crystal').value=FormatNumber('return',ship_costs_crystal_total,'','','');
					  	document.getElementById('ship_sell_plastic').value=FormatNumber('return',ship_costs_plastic_total,'','','');
					  	document.getElementById('ship_sell_fuel').value=FormatNumber('return',ship_costs_fuel_total,'','','');
					    document.getElementById('ship_sell_food').value=FormatNumber('return',ship_costs_food_total,'','','');
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
				  										- ship_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
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
				  										- ship_buy_metal * MARKET_METAL_FACTOR / actuel_res_factor
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
					window.alert(1);
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
					res_metal = document.getElementById('res_0').value;
					res_crystal = document.getElementById('res_1').value;
					res_plastic = document.getElementById('res_2').value;
					res_fuel = document.getElementById('res_3').value;
					res_food = document.getElementById('res_4').value;

					// Zu versteigernde Rohstoffe (Angebot) in Variablen speichern
					auction_sell_metal = document.getElementById('sell_0').value;
					auction_sell_crystal = document.getElementById('sell_1').value;
					auction_sell_plastic = document.getElementById('sell_2').value;
					auction_sell_fuel = document.getElementById('sell_3').value;
					auction_sell_food = document.getElementById('sell_4').value;

					// Das momentane Höchstgebot in Variablen speichern
					auction_buy_metal = document.getElementById('buy_0').value;
					auction_buy_crystal = document.getElementById('buy_1').value;
					auction_buy_plastic = document.getElementById('buy_2').value;
					auction_buy_fuel = document.getElementById('buy_3').value;
					auction_buy_food = document.getElementById('buy_4').value;

					// Das eingegebene Gebot (neuer Preis) formatieren (Trennzeichen entfernen) und in Variable speichern
					auction_new_buy_metal = document.getElementById('auction_new_buy_0').value.replace(/`/g, "");
					auction_new_buy_crystal = document.getElementById('auction_new_buy_1').value.replace(/`/g, "");
					auction_new_buy_plastic = document.getElementById('auction_new_buy_2').value.replace(/`/g, "");
					auction_new_buy_fuel = document.getElementById('auction_new_buy_3').value.replace(/`/g, "");
					auction_new_buy_food = document.getElementById('auction_new_buy_4').value.replace(/`/g, "");


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
						document.getElementById('auction_new_buy_0').value=auction_new_buy_metal;
					}
					auction_new_buy_metal = Math.abs(auction_new_buy_metal);

					// Silizium
					if(istZahl(auction_new_buy_crystal)==false || auction_new_buy_crystal=='')
					{
						auction_new_buy_crystal = 0;
						document.getElementById('auction_new_buy_1').value=auction_new_buy_crystal;
					}
					auction_new_buy_crystal = Math.abs(auction_new_buy_crystal);

					// PVC
					if(istZahl(auction_new_buy_plastic)==false || auction_new_buy_plastic=='')
					{
						auction_new_buy_plastic = 0;
						document.getElementById('auction_new_buy_2').value=auction_new_buy_plastic;
					}
					auction_new_buy_plastic = Math.abs(auction_new_buy_plastic);

					// Tritium
					if(istZahl(auction_new_buy_fuel)==false || auction_new_buy_fuel=='')
					{
						auction_new_buy_fuel = 0;
						document.getElementById('auction_new_buy_3').value=auction_new_buy_fuel;
					}
					auction_new_buy_fuel = Math.abs(auction_new_buy_fuel);

					// Nahrung
					if(istZahl(auction_new_buy_food)==false || auction_new_buy_food=='')
					{
						auction_new_buy_food = 0;
						document.getElementById('auction_new_buy_4').value=auction_new_buy_food;
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
							var buy_field = "auction_new_buy_0";
						}
						// Silizium
						else if(res==1)
						{
							var actuel_res_factor = <?php echo MARKET_CRYSTAL_FACTOR;?>;
							var auction_sell = auction_sell_crystal;
							var auction_new_buy = auction_new_buy_crystal;
							var buy_field = "auction_new_buy_1";
						}
						// PVC
						else if(res==2)
						{
							var actuel_res_factor = <?php echo MARKET_PLASTIC_FACTOR;?>;
							var auction_sell = auction_sell_plastic;
							var auction_new_buy = auction_new_buy_plastic;
							var buy_field = "auction_new_buy_2";
						}
						// Tritium
						else if(res==3)
						{
							var actuel_res_factor = <?php echo MARKET_FUEL_FACTOR;?>;
							var auction_sell = auction_sell_fuel;
							var auction_new_buy = auction_new_buy_fuel;
							var buy_field = "auction_new_buy_3";
						}
						// Nahrung
						else if(res==4)
						{
							var actuel_res_factor = <?php echo MARKET_FOOD_FACTOR;?>;
							var auction_sell = auction_sell_food;
							var auction_new_buy = auction_new_buy_food;
							var buy_field = "auction_new_buy_4";
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
					if(document.getElementById('currency_0').value==1)
					{
						document.getElementById('min_max_0').innerHTML=out_auction_min_max['0'];
					}
					if(document.getElementById('currency_1').value==1)
					{
						document.getElementById('min_max_1').innerHTML=out_auction_min_max['1'];
					}
					if(document.getElementById('currency_2').value==1)
					{
						document.getElementById('min_max_2').innerHTML=out_auction_min_max['2'];
					}
					if(document.getElementById('currency_3').value==1)
					{
						document.getElementById('min_max_3').innerHTML=out_auction_min_max['3'];
					}
					if(document.getElementById('currency_4').value==1)
					{
						document.getElementById('min_max_4').innerHTML=out_auction_min_max['4'];
					}


					// Ändert Statusnachricht
					document.getElementById('auction_check_message').innerHTML=auction_check_message;

				}


				function showSearchFilter(type)
				{
					document.getElementById('market_search_filter_container_res').style.display="none";
					document.getElementById('market_search_filter_container_ship').style.display="none";
					document.getElementById('market_search_filter_container_auction').style.display="none";
					if (type=="ships")
					{
						document.getElementById('market_search_filter_container_ship').style.display="";
					}
					else if(type=="auctions")
					{
						document.getElementById('market_search_filter_container_auction').style.display="";
					}
					else
					{
						document.getElementById('market_search_filter_container_res').style.display="";
					}

				}

				function applySearchFilter()
				{
					if (document.getElementById('market_search_loading'))
						document.getElementById('market_search_loading').style.display='';

					xajax_marketSearch(xajax.getFormValues('search_selector'));
				}
				function sortSearch(sortingKey,sortingDirection)
				{
					if (document.getElementById('market_search_loading'))
						document.getElementById('market_search_loading').style.display='';

					xajax_marketSearch(xajax.getFormValues('search_selector'),sortingKey,sortingDirection);
				}


			</script>
<!-- additional market functions by river -->
<script type="text/javascript">
	var uname = '';
	function jqinit()
	{
		$(".offer").each(function()
		{
			var $th = $(this);
			var $pa = $th.parent();
			var $sp;
			if($(this).find('.rtext').html().toLowerCase().indexOf(uname.toLowerCase()) > -1)
			{
				$sp = (($th.next().size() > 0 ? ($th.next()) : ($th.prev())).detach());
				$th.addClass('foryou').detach().after($sp).prependTo($pa);
				$th.find('td').css('background-color','navy').css('background-image','none');
			}
			$pa.find('thead').detach().prependTo($pa);
		});
		if($('#filter_strict').size() == 0)
		{
			$('#market_search_filter_payable')
			.parent().append(
				'&nbsp;<input id="filter_strict" name="filter_strict" type="checkbox" />'+
				'<label for="filter_strict">Striktes Filtern</label>'
			);
			$('#filter_strict').click(filterRess);
		}
		else
		{
			$('#filter_strict').attr('checked', false);
		}
		if($('#ship_filter_strict').size() == 0)
		{
			$('#market_ship_search_filter_payable')
			.parent().append(
				'&nbsp;<input id="ship_filter_strict" name="ship_filter_strict" type="checkbox" />'+
				'<label for="ship_filter_strict">Striktes Filtern</label>'
			);
			$('#ship_filter_strict').click(filterShip);
		}
		else
		{
			$('#ship_filter_strict').attr('checked', false);
		}
	}

	function filterRess()
	{
		if($('#filter_strict:checked').size() == 0)
		{
			applySearchFilter();
		}
		else
		{
			var $su = new Array();
			var $de = new Array();
			for(var i=0;i<5;i++)
			{
				$su.push($('#market_search_filter_supply_'+i));
				$de.push($('#market_search_filter_demand_'+i));
			}
			$(".offer").each(function()
			{
				for(var i=0;i<5;i++)
				{
					if
					(
						(
							$su[i].filter(':checked').size() == 0 &&
							$(this).find('.rescolor'+i).not(function()
							{
								return (
									$(this).filter('.rsupp').size() == 0 ||
									$(this).html() == '-'
								);
							}).size() > 0
						) || (
							$de[i].filter(':checked').size() == 0 &&
							$(this).find('.rescolor'+i).not(function()
							{
								return (
									$(this).filter('.rdema').size() == 0 ||
									$(this).html() == '-'
								);
							}).size() > 0
						)
					)
					{
						if($(this).next().size() > 0)
						{
							$(this).next().detach();
						}
						else
						{
							$(this).prev().detach();
						}
						$(this).detach();
					}	
				}
			});
			if($('.offer').size() == 0)
			{
				$('#ress_buy_check_message').html('<div style="font-weight:bold;">Keine Angebote vorhanden!</div>');
			}
		}
	}

	function filterShip()
	{
		if($('#ship_filter_strict:checked').size() == 0)
		{
			applySearchFilter();
		}
		else
		{
			var $de = new Array();
			for(var i=0;i<5;i++)
			{
				$de.push($('#market_ship_search_filter_demand_'+i));
			}
			$(".offer").each(function()
			{
				for(var i=0;i<5;i++)
				{
					if
					(
						$de[i].filter(':checked').size() == 0 &&
						$(this).find('.rescolor'+i).not(function()
						{
							return (
								$(this).filter('.rdema').size() == 0 ||
								$(this).html() == '-'
							);
						}).size() > 0
					)
					{
						if($(this).next().size() > 0)
						{
							$(this).next().detach();
						}
						else
						{
							$(this).prev().detach();
						}
						$(this).detach();
					}	
				}
			});
			if($('.offer').size() == 0)
			{
				$('#ship_buy_check_message').html('<div style="font-weight:bold;">Keine Angebote vorhanden!</div>');
			}
		}
	}

</script>
