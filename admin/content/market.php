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
	// 	Topic: Marktplatz-Verwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	define("USER_MESSAGE_CAT_ID",1);
	define("SYS_MESSAGE_CAT_ID",5);
	$rsc = get_resources_array();

	echo "<h1>Marktplatz</h1>";

	if($sub=="ress")
	{
		echo "<h2>Rohstoffe</h2>";
		if($_GET['ressource_delete']>0)
		{
			dbquery("DELETE FROM ".$db_table['market_ressource']." WHERE ressource_market_id=".$_GET['ressource_delete']."");
			cms_ok_msg("Angebot gel&ouml;scht!");
		}
		$res=dbquery("SELECT * FROM ".$db_table['market_ressource']." ORDER BY datum ASC");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$username=get_user_nick($arr['user_id']);
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\" width=\"100\">Datum:</td><td class=\"tbldata\" colspan=\"2\" width=\"200\">".date("d.m.Y - H:i:s", $arr['datum'])."</td><td class=\"tbltitle\" width=\"100\">Spieler:</td><td class=\"tbldata\" width=\"100\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">".$username."</a></td><td class=\"tbltitle\"><input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ressource_delete=".$arr['ressource_market_id']."'\" value=\"L&ouml;schen\"/></td></tr>";
				echo "<tr><td class=\"tbltitle\" rowspan=\"5\">Angebot:</td><td class=\"tbldata\" width=\"110\">".$rsc['metal'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['sell_metal'])."</td><td class=\"tbltitle\" rowspan=\"5\">Preis:</td><td class=\"tbldata\" width=\"100\">".$rsc['metal'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['buy_metal'])."</td></tr>";
				echo "<tr><td class=\"tbldata\" width=\"100\">".$rsc['crystal'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['sell_crystal'])."</td><td class=\"tbldata\" width=\"100\">".$rsc['crystal'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['buy_crystal'])."</td></tr>";
				echo "<tr><td class=\"tbldata\" width=\"100\">".$rsc['plastic'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['sell_plastic'])."</td><td class=\"tbldata\" width=\"100\">".$rsc['plastic'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['buy_plastic'])."</td></tr>";
				echo "<tr><td class=\"tbldata\" width=\"100\">".$rsc['fuel'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['sell_fuel'])."</td><td class=\"tbldata\" width=\"100\">".$rsc['fuel'].":</td><td class=\"tbldata\" width=\"100\">".nf($arr['buy_fuel'])."</td></tr>";
				echo "<tr><td class=\"tbldata\" width=\"100\">".$rsc['food']."</td><td class=\"tbldata\" width=\"100\">".nf($arr['sell_food'])."</td><td class=\"tbldata\" width=\"100\">".$rsc['food']."</td><td class=\"tbldata\" width=\"100\">".nf($arr['buy_food'])."</td></tr>";
				echo "</table><br/>";
			}
		}else{
		 	echo "Keine Angebote vorhanden";
		}
	}
	elseif ($sub=="ships")
	{
		echo "<h2>Schiffe</h2>";
		if ($_GET['ship_delete']!="")
		{
				dbquery("DELETE FROM ".$db_table['market_ship']." WHERE ship_market_id=".$_GET['ship_delete']."");
				cms_ok_msg("Angebot gel&ouml;scht");
		}
		$res=dbquery("SELECT * FROM ".$db_table['market_ship']." ORDER BY datum DESC;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$username=get_user_nick($arr['user_id']);
				echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">\n";
				echo "<input type=\"hidden\" name=\"ship_market_id\" value=\"".$arr['ship_market_id']."\">";
				echo "<table class=\"tbl\">\n";
				echo "<tr><td class=\"tbltitle\" width=\"100\">Datum:</td><td class=\"tbldata\" colspan=\"2\" width=\"200\">".date("d.m.Y - H:i:s", $arr['datum'])."</td><td class=\"tbltitle\" width=\"100\">Spieler:</td><td class=\"tbldata\" width=\"100\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">".$username."</a></td><td class=\"tbldata\" rowspan=\"4\"><input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ship_delete=".$arr['ship_market_id']."'\" value=\"L&ouml;schen\"/></td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"100\">Schiffname:</td><td class=\"tbldata\" colspan=\"2\" width=\"200\">".$arr['ship_name']."</td><td class=\"tbltitle\" width=\"100\">Anzahl:</td><td class=\"tbldata\" width=\"100\">".$arr['ship_count']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"100\">".$rsc['metal'].":</td><td class=\"tbltitle\" width=\"100\">".$rsc['crystal'].":</td><td class=\"tbltitle\" width=\"100\">".$rsc['plastic'].":</td><td class=\"tbltitle\" width=\"100\">".$rsc['fuel'].":</td><td class=\"tbltitle\" width=\"100\">".$rsc['food']."</td></tr>";
				echo "<tr><td class=\"tbldata\" width=\"100\">".nf($arr['ship_costs_metal'])."</td><td class=\"tbldata\" width=\"100\">".nf($arr['ship_costs_crystal'])."</td><td class=\"tbldata\" width=\"100\">".nf($arr['ship_costs_plastic'])."</td><td class=\"tbldata\" width=\"100\">".nf($arr['ship_costs_fuel'])."</td><td class=\"tbldata\" width=\"100\">".nf($arr['ship_costs_food'])."</td></tr>";
				echo "</table><br/>";
			}
		}else{
		 	echo "Keine Angebote vorhanden";
		}
	}
	elseif ($sub=="auction")
	{
		echo "<h2>Auktionen</h2>";
		if ($_GET['auction_delete']!="")
		{
				dbquery("DELETE FROM ".$db_table['market_auction']." WHERE auction_market_id=".$_GET['auction_delete']."");
				cms_ok_msg("Auktion gel&ouml;scht");
		}
		$res=dbquery("SELECT * FROM ".$db_table['market_auction']." ORDER BY auction_end ASC;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
						infobox_start("",1);
						echo "<tr>
						<td class=\"tbltitle\">Anbieter</td>
						<td class=\"tbltitle\">Auktion Start/Ende</td>
						<td class=\"tbltitle\" colspan=\"3\">Angebot</td>
						<td class=\"tbltitle\">Status</td></tr>";

						//restliche zeit bis zum ende
						$rest_time=$arr['auction_end']-time();

                        $t = floor($rest_time / 3600 / 24);
                        $h = floor(($rest_time-($t*24*3600)) / 3600);
                        $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
                        $s = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));

						$rest_time = "Noch $t t $h h $m m $s s";


						echo "<tr><td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['auction_user_id']."\">".get_user_nick($arr['auction_user_id'])."</a></td>";
						echo "<td class=\"tbldata\">Start ".date("d.m.Y  G:i:s", $arr['auction_start'])."</td>";


						// Sind Schiffe angeboten
						if($arr['auction_ship_id']>0)
						{
							echo "<td class=\"tbldata\" rowspan=\"5\">".$arr['auction_ship_count']." <a href=\"?page=help&site=shipyard&id=".$arr['auction_ship_id']."\">".$arr['auction_ship_name']."</a></td>";
						}
						else
						{
							echo "<td class=\"tbldata\" rowspan=\"5\">Keine Schiffe</td>";
						}

						echo "<td class=\"tbldata\"><b>".$rsc['metal']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_metal'])."</td>";

						 // Zurückzieh button wenn noch niemand geboten hat
						if($arr['auction_current_buyer_id']==0)
						{
                            echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=".$arr['auction_market_id']."'\" value=\"L&ouml;schen\"/></td></tr>";
                        }
                        elseif($arr['auction_buyable']==0)
                        {
                        	echo "<td class=\"tbldata\" rowspan=\"5\">Verkauft!<br><br><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=".$arr['auction_market_id']."'\" value=\"L&ouml;schen\"/></td></tr>";
                        }
                        else
                        {
                        	 echo "<td class=\"tbldata\" rowspan=\"5\">Es wurde bereits geboten<br><br><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=".$arr['auction_market_id']."'\" value=\"L&ouml;schen\"/></td></tr>";
                        }

						// Start/Ende Anzeigen sofern die auktion nicht schon beendet ist
						if($arr['auction_delete_date']==0)
						{
							echo "<tr><td class=\"tbldata\">Ende ".date("d.m.Y  G:i:s", $arr['auction_end'])."</td><td class=\"tbldata\"><b>".$rsc['crystal']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td class=\"tbldata\" rowspan=\"3\">$rest_time</td><td class=\"tbldata\"><b>".$rsc['plastic']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}
						// sonst das löschdatum anzeigen
						else
						{
							$delete_rest_time = $arr['auction_delete_date']-time();

                            $t = floor($delete_rest_time / 3600 / 24);
                            $h = floor(($delete_rest_time) / 3600);
                            $m = floor(($delete_rest_time-($h*3600))/60);
                            $s = floor(($delete_rest_time-($h*3600)-($m*60)));

							echo "<tr><td class=\"tbldata\">Auktion beendet</td><td class=\"tbldata\"><b>".$rsc['crystal']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_crystal'])."</td></tr>";
							echo "<tr><td class=\"tbldata\" rowspan=\"3\">Gebot wird nach $h Stunden und $m Minuten gel&ouml;scht</td><td class=\"tbldata\"><b>".$rsc['plastic']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_plastic'])."</td></tr>";
						}


						echo "<tr><td class=\"tbldata\"><b>".$rsc['fuel']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_fuel'])."</td></tr>";
						echo "<tr><td class=\"tbldata\"><b>".$rsc['food']."</b>:</td><td class=\"tbldata\">".nf($arr['auction_sell_food'])."</td></tr>";

						//Hochstgebot Anzeigen wenn schon geboten worden ist
						if($arr['auction_current_buyer_id']!=0)
						{
                            echo "<tr><td class=\"tbltitle\" colspan=\"6\">H&ouml;chstgebot</td></tr>";
                            //Höchstbietender User anzeigen wenn vorhanden
                            echo "<tr><td class=\"tbldata\" rowspan=\"5\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['auction_current_buyer_id']."\">".get_user_nick($arr['auction_current_buyer_id'])."</a></td>";
                            echo "<td class=\"tbldata\" rowspan=\"5\">Geboten ".date("d.m.Y  G:i:s", $arr['auction_current_buyer_date'])."</td>";

                            echo "<td class=\"tbldata\"><b>".$rsc['metal']."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_metal'])."</td>";

                            echo "<td class=\"tbldata\" rowspan=\"5\">&nbsp;</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".$rsc['crystal']."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_crystal'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".$rsc['plastic']."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_plastic'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".$rsc['fuel']."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_fuel'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\"><b>".$rsc['food']."</b>:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['auction_buy_food'])."</td></tr>";
                        }
                        infobox_end(1);


			}


		}else{
		 	echo "Keine Angebote vorhanden";
		}
	}
	else
	{
		echo "Willkommen bei der Marktplatzverwaltung. Bitte w&auml;hle einen Bereich aus dem Men&uuml; rechts aus!";
	}

?>
