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

	if (isset($_GET['searchcat']) && $_GET['searchcat']=="auctions")
		$searchCat = "auctions";
	elseif (isset($_GET['searchcat']) && $_GET['searchcat']=="ships")
		$searchCat = "ships";
	else
		$searchCat = "resources";

	if (isset($_GET['auctionid']) && $_GET['auctionid']>0)
	{
		$_SESSION['auctionid']=$_GET['auctionid'];
	}

	echo "<form action=\"?page=".$page."\" method=\"post\" id=\"search_selector\">\n";
	checker_init();
	tableStart("Suchfilter");

	echo "<tr><td>";
	//echo "<div id=\"search_cat_field\" style=\"text-align:center;vertical-align:middle;height:30px;\">
	echo "<div id=\"market_search_filter_category_selector\">Kategorie:
					<select id=\"search_cat\" name=\"search_cat\" onchange=\"showSearchFilter(this.value);applySearchFilter();\">";
	if(MIN_MARKET_LEVEL_RESS<=MARKET_LEVEL)
	{
		echo "<option value=\"resources\" ".($searchCat=="resources"?' selected="selected"':'').">Rohstoffe</option>";
	}
	if(MIN_MARKET_LEVEL_SHIP<=MARKET_LEVEL)
	{
		echo "<option value=\"ships\" ".($searchCat=="ships"?' selected="selected"':'').">Schiffe</option>";
	}
	if(MIN_MARKET_LEVEL_AUCTION<=MARKET_LEVEL)
	{
		echo "<option value=\"auctions\" ".($searchCat=="auctions"?' selected="selected"':'').">Auktionen</option>";
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

	echo "<div id=\"market_search_filter_container_auction\" style=\"display:none;\">";
	echo "</div>";


	echo "</td></tr>";

	tableEnd();
	echo "</form>";

	echo "<div id=\"market_search_results\">Angebote werden geladen ...</div>";

	echo "<script type=\"text/javascript\">xajax_marketSearch(xajax.getFormValues('search_selector'));</script>";

?>
