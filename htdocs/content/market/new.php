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

	// Angebotsmaske Darstellen falls noch Angebote aufgegeben werden können
	if ($possible>0)
	{
		//
		// Rohstoffe
		//
		if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
		{
			// Hier wird das ganze für die Rohstoffe noch angezeigt
			echo "<form action=\"?page=".$page."\" method=\"post\" name=\"ress_selector\" id=\"ress_selector\">\n";
			$cstr=checker_init();

			tableStart("Rohstoffe verkaufen");

			//Header
			echo "<tr><th style=\"width:15%;vertical-align:middle;\">Rohstoff";

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
						<input type=\"text\" value=\"0\" name=\"res_sell_0\" id=\"ress_sell_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');calcMarketRessPrice('0');\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</th>
					<td id=\"ress_buy_metal_field\" style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_buy_0\" id=\"ress_buy_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
					</td>
					<th id=\"ress_min_max_metal\" style=\"vertical-align:middle;\"> - </th>
				</tr>";

			// Silizium
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_sell_1\" id=\"ress_sell_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');calcMarketRessPrice('0');\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</th>
					<td id=\"ress_buy_crystal_field\" style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_buy_1\" id=\"ress_buy_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\"  disabled=\"disabled\"/>
					</td>
					<th id=\"ress_min_max_crystal\" style=\"vertical-align:middle;\"> - </th>
				</tr>";
			
			// PVC
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_PLASTIC.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_sell_2\" id=\"ress_sell_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');calcMarketRessPrice('0');\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</th>
					<td id=\"ress_buy_plastic_field\" style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_buy_2\" id=\"ress_buy_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
					</td>
					<th id=\"ress_min_max_plastic\" style=\"vertical-align:middle;\"> - </th>
				</tr>";
			
			// Tritium
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_sell_3\" id=\"ress_sell_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');calcMarketRessPrice('0');\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</th>
					<td id=\"ress_buy_fuel_field\" style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_buy_3\" id=\"ress_buy_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
					</td>
					<th id=\"ress_min_max_fuel\" style=\"vertical-align:middle;\"> - </th>
				</tr>";
			
			// Nahrung
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_sell_4\" id=\"ress_sell_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');calcMarketRessPrice('0');\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</th>
					<td id=\"ress_buy_food_field\" style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"res_buy_4\" id=\"ress_buy_food\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,'','','');calcMarketRessPrice('0');\" disabled=\"disabled\"/>
					</td>
					<th id=\"ress_min_max_food\" style=\"vertical-align:middle;\"> - </th>
				</tr>";

			// Verkaufstext
			echo "<tr>
				<th>Beschreibung</th>
				<td colspan=\"4\">
					<input type=\"text\" value=\"\" name=\"ressource_text\" id=\"ressource_text\" size=\"55\" maxlength=\"60\" style=\"width:98%\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"calcMarketRessPrice('0');\"/>
				</td>
			</tr>";
		  
			// Reservation
			echo "<tr>
			<th>Reservation</th>
			<td colspan=\"3\">";
			echo "<input type=\"radio\" name=\"resource_offer_reservation\" id=\"resource_offer_reservation_0\" value=\"0\" checked=\"checked\" /> <label for=\"resource_offer_reservation_0\">Öffentliches Angebot</label><br/>";
		    if ($cfg->market_user_reservation_active->v == 1)
			{
				echo "<input type=\"radio\" name=\"resource_offer_reservation\" id=\"resource_offer_reservation_1\" value=\"1\" /> <label for=\"resource_offer_reservation_1\">Für eine bestimmte Person</label><br/>";
			}
			//Für Allianzmitglied reservieren wenn in einer Allianz und diese den Allianzmarktplatz auf Stufe 1 oder höher hat
			if($cu->allianceId!=0 && $alliance_market_level>=1 && !$cd_enabled)
			{
				echo "<input type=\"radio\" name=\"resource_offer_reservation\" id=\"resource_offer_reservation_2\" value=\"2\" /> 
				<label for=\"resource_offer_reservation_2\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen").">F&uuml;r Allianzmitglieder</label>";
			}
			echo "</td>";
			echo "<td style=\"vertical-align:middle\"><input type=\"text\" name=\"resource_offer_user_nick\" id=\"resource_offer_user_nick\"  maxlength=\"".NICK_MAXLENGHT."\" size=\"25\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'resource_offer_user_nick')\"><br/><div class=\"citybox\" id=\"citybox\">&nbsp;</div></td>";
			echo "</tr>";			

			// Status Nachricht (Ajax Überprüfungstext)
			echo "<tr>
					<td colspan=\"5\" id=\"check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
				</tr>";

			tableEnd();
			
			?>
			<script>
				$(function(){
					$('#resource_offer_user_nick').hide();
					$('input[name=resource_offer_reservation]').click(function(){
						if ($(this).val() == 1) {
							$('#resource_offer_user_nick').show();	
						} else {
							$('#resource_offer_user_nick').hide();
						}
					});
				});
			</script>
			<?PHP

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

			// Zuerst wird überprüft ob auf dem Planeten Schiffe sind
			if (mysql_result($check_res,0)>0)
			{
				// Folgender Javascript Abschnitt, welcher von PHP-Teilen erzeugt wird, lädt die Daten von den Schiffen, welche sich auf dem aktuellen Planeten befinden, 
				// in ein JS-Array. Dies wird für die Preisberechnung benötigt. Das erzeugte PHP Array wird für die Schiffsauswahl (SELECT) verwendet.

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
					AND ships.ship_tradable='1'
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
				echo "</script>\n";

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
						&nbsp;
						<input type=\"text\" value=\"100\" name=\"ship_percent\" id=\"ship_percent\" size=\"5\" maxlength=\"7\" onkeyup=\"calcMarketShipPrice(1, 0);\" /> Verkaufspreis in %
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
							<input type=\"text\" value=\"0\" name=\"ship_buy_0\" id=\"ship_buy_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
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
							<input type=\"text\" value=\"0\" name=\"ship_buy_1\" id=\"ship_buy_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
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
							<input type=\"text\" value=\"0\" name=\"ship_buy_2\" id=\"ship_buy_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
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
							<input type=\"text\" value=\"0\" name=\"ship_buy_3\" id=\"ship_buy_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
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
							<input type=\"text\" value=\"0\" name=\"ship_buy_4\" id=\"ship_buy_food\" size=\"7\" maxlength=\"15\" onkeyup=\"calcMarketShipPrice(0, 0);\"/>
						</td>
						<th id=\"ship_min_max_food\" style=\"vertical-align:middle;\"> - </th>
					</tr>";

				// Verkaufstext
				echo "<tr>
					<th>Beschreibung</th>
					<td colspan=\"4\">
						<input type=\"text\" value=\"\" name=\"ship_text\" id=\"ship_text\" size=\"55\" maxlength=\"60\" style=\"width:98%\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"calcMarketShipPrice('0');\"/>
					</td>
				</tr>";

				// Reservation
				echo "<tr>
				<th>Reservation</th>
				<td colspan=\"3\">";
				echo "<input type=\"radio\" name=\"ship_offer_reservation\" id=\"ship_offer_reservation_0\" value=\"0\" checked=\"checked\" /> <label for=\"ship_offer_reservation_0\">Öffentliches Angebot</label><br/>";
				if ($cfg->market_user_reservation_active->v == 1)
				{
					echo "<input type=\"radio\" name=\"ship_offer_reservation\" id=\"ship_offer_reservation_1\" value=\"1\" /> <label for=\"ship_offer_reservation_1\">Für eine bestimmte Person</label><br/>";
				}
				//Für Allianzmitglied reservieren wenn in einer Allianz und diese den Allianzmarktplatz auf Stufe 2 oder höher hat
				if($cu->allianceId!=0 && $alliance_market_level>=2 && !$cd_enabled)
				{
					echo "<input type=\"radio\" name=\"ship_offer_reservation\" id=\"ship_offer_reservation_2\" value=\"2\" /> 
					<label for=\"ship_offer_reservation_2\" ".tm("Reservation","Fall dieses Angebot nur Spieler aus deiner Allianz kaufen sollen, mach hier ein H&auml;kchen").">F&uuml;r Allianzmitglieder</label>";
				}
				echo "</td>";
				echo "<td style=\"vertical-align:middle\"><input type=\"text\" name=\"ship_offer_user_nick\" id=\"ship_offer_user_nick\"  maxlength=\"".NICK_MAXLENGHT."\" size=\"25\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value,'ship_offer_user_nick')\"><br/><div class=\"citybox\" id=\"citybox\">&nbsp;</div></td>";
				echo "</tr>";

				// Status Nachricht (Ajax Überprüfungstext)
				echo "<tr>
					<td colspan=\"5\" id=\"ship_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
				</tr>";

				tableEnd();
				
				?>
				<script>
					$(function(){
						$('#ship_offer_user_nick').hide();
						$('input[name=ship_offer_reservation]').click(function(){
							if ($(this).val() == 1) {
								$('#ship_offer_user_nick').show();	
							} else {
								$('#ship_offer_user_nick').hide();
							}
						});
					});
				</script>
				<?PHP

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
			echo "<tr><th style=\"width:15%;vertical-align:middle;\">Rohstoff";

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
						<input type=\"text\" value=\"0\" name=\"auction_sell_0\" id=\"auction_sell_metal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');checkMarketAuctionFormular(0);\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_METAL_FACTOR."</th>
					<td id=\"auction_buy_metal_field\" style=\"text-align:center;vertical-align:middle;\">
						<input type=\"checkbox\" name=\"auction_buy_0\" id=\"auction_buy_metal\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
					</td>
					<th colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</th>
				</tr>";
			
			// Silizium und "Dauer" Feld
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_CRYSTAL.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"auction_sell_1\" id=\"auction_sell_crystal\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');checkMarketAuctionFormular(0);\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_CRYSTAL_FACTOR."</th>
					<td id=\"auction_buy_crystal_field\" style=\"text-align:center;vertical-align:middle;\">
						<input type=\"checkbox\" name=\"auction_buy_1\" id=\"auction_buy_crystal\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
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
						<input type=\"text\" value=\"0\" name=\"auction_sell_2\" id=\"auction_sell_plastic\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');checkMarketAuctionFormular(0);\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_PLASTIC_FACTOR."</th>
					<td id=\"auction_buy_plastic_field\" style=\"text-align:center;vertical-align:middle;\">
						<input type=\"checkbox\" name=\"auction_buy_2\" id=\"auction_buy_plastic\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
					</td>
					<th colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</th>
				</tr>";
			
			// Tritium und "Ende" Feld
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_FUEL.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"auction_sell_3\" id=\"auction_sell_fuel\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');checkMarketAuctionFormular(0);\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FUEL_FACTOR."</th>
					<td id=\"auction_buy_fuel_field\" style=\"text-align:center;vertical-align:middle;\">
						<input type=\"checkbox\" name=\"auction_buy_3\" id=\"auction_buy_fuel\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
					</td>
					<th style=\"vertical-align:middle;\">Ende:</th>
					<td id=\"auction_end_time\" style=\"vertical-align:middle;\">".date("d.m.Y H:i",$auction_time)."</td>
				</tr>";
			
			// Nahrung
			echo "<tr>
					<th style=\"vertical-align:middle;\">".RES_FOOD.":</th>
					<td style=\"vertical-align:middle;\">
						<input type=\"text\" value=\"0\" name=\"auction_sell_4\" id=\"auction_sell_food\" size=\"7\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');checkMarketAuctionFormular(0);\"/>
					</td>
					<th style=\"text-align:center;vertical-align:middle;\">".MARKET_FOOD_FACTOR."</th>
					<td id=\"auction_buy_food_field\" style=\"text-align:center;vertical-align:middle;\">
						<input type=\"checkbox\" name=\"auction_buy_4\" id=\"auction_buy_food\" value=\"1\" onclick=\"checkMarketAuctionFormular(0);\" checked=\"checked\"/>
					</td>
					<th colspan=\"2\" style=\"vertical-align:middle;\">&nbsp;</th>
				</tr>";
		  
			// Verkaufstext
			echo "<tr>
				<th>Beschreibung</th>
				<td colspan=\"5\">
					<input type=\"text\" value=\"\" name=\"auction_text\" id=\"auction_text\" size=\"55\" maxlength=\"60\" style=\"width:98%\" ".tm("Text","Schreib einen kleinen Werbetext f&uuml;r deine Waren.")." onkeyup=\"checkMarketAuctionFormular('0');\"/>
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
?>
