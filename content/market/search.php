<?php
			echo "<form action=\"?page=".$page."\" method=\"post\" id=\"search_selector\">\n";
			checker_init();
			tableStart("Suchfilter");

			echo "<tr><td>";
			//echo "<div id=\"search_cat_field\" style=\"text-align:center;vertical-align:middle;height:30px;\">
			echo "<div id=\"market_search_filter_category_selector\">Kategorie:
							<select id=\"search_cat\" name=\"search_cat\" onchange=\"showSearchFilter(this.value);applySearchFilter();\">";
							if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
							{
								echo "<option value=\"resources\">Rohstoffe</option>";
							}
							if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
							{
								echo "<option value=\"ships\">Schiffe</option>";
							}
							if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
							{
								echo "<option value=\"auctions\">Auktionen</option>";
							}
			echo "</select></div>";

			// Resource filter
			echo "<div id=\"market_search_filter_container_res\" style=\"\">";
			echo "<span>Angebot:</span>";
			foreach ($resNames as $rk=>$rn)
			{
				echo "<input id=\"market_search_filter_supply_".$rk."\" name=\"market_search_filter_supply_".$rk."\" type=\"checkbox\" value=\"1\" checked=\"checked\"  onclick=\"applySearchFilter();\" />
				<label for=\"market_search_filter_supply_".$rk."\" class=\"rescolor".$rk."\">".$rn."</label>";
			}
			echo "<br/>";
			echo "<span>Preis:</span>";
			foreach ($resNames as $rk=>$rn)
			{
				echo "<input id=\"market_search_filter_demand_".$rk."\" name=\"market_search_filter_demand_".$rk."\" type=\"checkbox\" value=\"1\" checked=\"checked\" onclick=\"applySearchFilter();\" />
				<label for=\"market_search_filter_demand_".$rk."\" class=\"rescolor".$rk."\">".$rn."</label>";
			}
			echo "<br/>";
			echo "<input type=\"checkbox\" id=\"market_search_filter_payable\" name=\"market_search_filter_payable\" value=\"1\"  onclick=\"applySearchFilter()\" />";
			echo "<label for=\"market_search_filter_payable\" style=\"width:200px;\">Nur bezahlbare Angebote anzeigen</label>";
			echo "</div>";

			// Ship filter
			echo "<div id=\"market_search_filter_container_ship\" style=\"display:none;\">";
			echo "<span>Preis:</span>";
			foreach ($resNames as $rk=>$rn)
			{
				echo "<input id=\"market_ship_search_filter_demand_".$rk."\" name=\"market_ship_search_filter_demand_".$rk."\" type=\"checkbox\" value=\"1\" checked=\"checked\" onclick=\"applySearchFilter();\" />
				<label for=\"market_ship_search_filter_demand_".$rk."\" class=\"rescolor".$rk."\">".$rn."</label>";
			}
			echo "<br/>";
			echo "<input type=\"checkbox\" id=\"market_ship_search_filter_payable\" name=\"market_ship_search_filter_payable\" value=\"1\"  onclick=\"applySearchFilter()\" />";
			echo "<label for=\"market_ship_search_filter_payable\" style=\"width:200px;\">Nur bezahlbare Angebote anzeigen</label>";
			echo "</div>";



			echo "</td></tr>";


			/*
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
			*/

			tableEnd();
			echo "</form>";

			echo "<div id=\"market_search_results\">Angebote werden geladen ...</div>";

			echo "<script type=\"text/javascript\">xajax_marketSearch(xajax.getFormValues('search_selector'));</script>";

?>
