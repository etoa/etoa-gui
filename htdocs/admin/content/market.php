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

	echo "<h1>Marktplatz</h1>";

	if($sub=="ress")
	{
		echo "<h2>Rohstoffe</h2>";
		if($_GET['ressource_delete']>0)
		{
			dbquery("DELETE FROM market_ressource WHERE id=".$_GET['ressource_delete']."");
			echo MessageBox::ok("", "Angebot gel&ouml;scht!");
		}
		$res=dbquery("SELECT * FROM market_ressource ORDER BY datum ASC");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$username=get_user_nick($arr['user_id']);
				echo "<table class=\"tb\">";
				echo "<tr>
						<th width=\"100\">
							Datum:
						</th>
						<td colspan=\"2\" width=\"200\">
							".date("d.m.Y - H:i:s", $arr['datum'])."
						</td>
						<th width=\"100\">
							Spieler:
						</th>
						<td width=\"100\">
							<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">".$username."</a></td><td class=\"tbltitle\"><input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ressource_delete=".$arr['id']."'\" value=\"L&ouml;schen\"/>
						</td>
					</tr>
					<tr>
						<th rowspan=\"6\">
							Angebot:
						</th>";
						$first = true;
						foreach ($resNames as $k=>$v)
						{
							if (!first) echo "<tr>";
							echo "	<td width=\"110\">".$v."</td>
									<td width=\"100\">
										".nf($arr['sell_'.$k.''])."
									</td>";
							if ($first)
							{
								echo "<th rowspan=\"5\">Preis:</th>";
								$first = false;
							}
							echo	"<td width=\"110\">".$v."</td>
									<td width=\"100\">
										".nf($arr['buy_'.$k.''])."
									</td>
								</tr>";

						}
				echo "</table><br/>";
			}
		}else{
		 	error_msg("Keine Angebote vorhanden",1);
		}
	}
	elseif ($sub=="ships")
	{
		echo "<h2>Schiffe</h2>";
		if ($_GET['ship_delete']!="")
		{
				dbquery("DELETE FROM market_ship WHERE id=".$_GET['ship_delete']."");
				echo MessageBox::ok("", "Angebot gel&ouml;scht");
		}
		$res=dbquery("SELECT * FROM market_ship ORDER BY datum DESC;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$username=get_user_nick($arr['user_id']);
				$ship = new ship($arr['ship_id']);
				echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">\n";
				echo "<input type=\"hidden\" name=\"ship_market_id\" value=\"".$arr['id']."\">";
				echo "<table class=\"tb\">
						<tr>
							<th width=\"100\">
								Datum:
							</th>
							<td colspan=\"2\" width=\"200\">
								".date("d.m.Y - H:i:s", $arr['datum'])."
							</td>
							<th width=\"100\">
								Spieler:
							</th>
							<td width=\"100\">
								<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">".$username."</a>
							</td>
							<td rowspan=\"4\">
								<input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ship_delete=".$arr['id']."'\" value=\"L&ouml;schen\"/>
							</td>
						</tr>
						<tr>
							<th width=\"100\">
								Schiffname:
							</th>
							<td colspan=\"2\" width=\"200\">
								".$ship."
							</td>
							<th width=\"100\">
								Anzahl:
							</td>
							<td width=\"100\">
								".$arr['count']."
							</td>
						</tr>
						<tr>";
				foreach ($resNames as $k=>$v)
				{
					echo "<th width=\"100\">
							".$v."
						</th>";
				}
				echo "</tr>
						<tr>";
				foreach ($resNames as $k=>$v)
				{
					echo "<td width=\"100\">
							".nf($arr['costs_'.$k.''])."
						</td>";
					
				}
				
				echo "</tr>
					</table><br/>";
			}
		}else {
		 	error_msg("Keine Angebote vorhanden",1);
		}
	}
	elseif ($sub=="auction")
	{
		echo "<h2>Auktionen</h2>";
		if ($_GET['auction_delete']!="")
		{
				dbquery("DELETE FROM market_auction WHERE id=".$_GET['auction_delete']."");
				echo MessageBox::ok("", "Auktion gel&ouml;scht");
		}
		$res=dbquery("SELECT * FROM market_auction ORDER BY date_end ASC;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
						tableStart();
						echo "<tr>
						<th>Anbieter</td>
						<th>Auktion Start/Ende</td>
						<th colspan=\"3\">Angebot</td>
						<th>Status</td></tr>";

						//restliche zeit bis zum ende
						$rest_time=$arr['date_end']-time();

                        $t = floor($rest_time / 3600 / 24);
                        $h = floor(($rest_time-($t*24*3600)) / 3600);
                        $m = floor(($rest_time-($t*24*3600)-($h*3600))/60);
                        $s = floor(($rest_time-($t*24*3600)-($h*3600)-($m*60)));

						$rest_time = "Noch $t t $h h $m m $s s";


						echo "<tr>
								<td rowspan=\"5\">
									<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">".get_user_nick($arr['user_id'])."</a>
								</td>
								<td>
									Start ".date("d.m.Y  G:i:s", $arr['date_start'])."
								</td>";


						// Sind Schiffe angeboten
						if($arr['ship_id']>0)
						{
							$ship = new ship($arr['ship_id']);
							echo "<td rowspan=\"5\">
									".$arr['ship_count']." <a href=\"?page=help&site=shipyard&id=".$arr['ship_id']."\">".$ship."</a>
								</td>";
						}
						else
						{
							echo "<td rowspan=\"5\">Keine Schiffe</td>";
						}

						echo "<td>
								<b>".RES_METAL."</b>:
							</td>
							<td>
								".nf($arr['sell_0'])."
							</td>";

						 // Zurückzieh button wenn noch niemand geboten hat
						if($arr['current_buyer_id']==0)
						{
                            echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=".$arr['id']."'\" value=\"L&ouml;schen\"/></td></tr>";
                        }
                        elseif($arr['buyable']==0)
                        {
                        	echo "<td class=\"tbldata\" rowspan=\"5\">Verkauft!<br><br><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=".$arr['id']."'\" value=\"L&ouml;schen\"/></td></tr>";
                        }
                        else
                        {
                        	 echo "<td class=\"tbldata\" rowspan=\"5\">Es wurde bereits geboten<br><br><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=".$arr['id']."'\" value=\"L&ouml;schen\"/></td></tr>";
                        }

						// Start/Ende Anzeigen sofern die auktion nicht schon beendet ist
						if($arr['date_end']>time())
						{
							echo "<tr>
									<td>
										Ende ".date("d.m.Y  G:i:s", $arr['date_end'])."
									</td>";
						}
						// sonst das löschdatum anzeigen
						else
						{
							$delete_rest_time = $arr['date_delete']-time();

                            $t = floor($delete_rest_time / 3600 / 24);
                            $h = floor(($delete_rest_time) / 3600);
                            $m = floor(($delete_rest_time-($h*3600))/60);
                            $s = floor(($delete_rest_time-($h*3600)-($m*60)));

							echo "<tr>
									<td>
										Auktion beendet
									</td>";
						}
						
						echo "		<td>
										<b>".RES_CRYSTAL."</b>:
									</td>
									<td>
										".nf($arr['sell_1'])."
									</td>
								</tr>
								<tr>
									<td rowspan=\"3\">
										$rest_time
									</td>
									<td>
										<b>".RES_PLASTIC."</b>:
									</td>
									<td>
										".nf($arr['sell_2'])."
									</td>
								</tr>
								<tr>
									<td>
										<b>".RES_FUEL."</b>:
									</td>
									<td>
										".nf($arr['sell_3'])."
									</td>
								</tr>
								<tr>
									<td>
										<b>".RES_FOOD."</b>:
									</td>
									<td>
										".nf($arr['sell_4'])."
									</td>
								</tr>";

						//Hochstgebot Anzeigen wenn schon geboten worden ist
						if($arr['current_buyer_id']!=0)
						{
                            echo "<tr>
									<th colspan=\"6\">
										H&ouml;chstgebot
									</th>
								</tr>";
                            //Höchstbietender User anzeigen wenn vorhanden
                            echo "<tr>
									<td rowspan=\"5\">
										<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['current_buyer_id']."\">".get_user_nick($arr['current_buyer_id'])."</a>
									</td>
                            		<td rowspan=\"5\">
										Geboten ".date("d.m.Y  G:i:s", $arr['current_buyer_date'])."
									</td>
									<td>
										<b>".RES_METAL."</b>:
									</td>
									<td colspan=\"2\">
										".nf($arr['buy_0'])."
									</td>
									<td rowspan=\"5\">
										&nbsp;
									</td>
								</tr>
								<tr>
									<td>
										<b>".RES_CRYSTAL."</b>:
									</td>
									<td colspan=\"2\">
										".nf($arr['buy_1'])."
									</td>
								</tr>
								<tr>
									<td>
										<b>".RES_PLASTIC."</b>:
									</td>
									<td colspan=\"2\">
										".nf($arr['buy_2'])."
									</td>
								</tr>
								<tr>
									<td>
										<b>".RES_FUEL."</b>:
									</td>
									<td colspan=\"2\">
										".nf($arr['buy_3'])."
									</td>
								</tr>
								<tr>
									<td>
										<b>".RES_FOOD."</b>:
									</td>
									<td colspan=\"2\">
										".nf($arr['buy_4'])."
									</td>
								</tr>";
                        }
                        tableEnd();


			}


		}else{
		 	error_msg("Keine Angebote vorhanden",1);
		}
	}
	else
	{
		echo '<div style="float:left;">';
		echo "Willkommen bei der Marktplatzverwaltung. <br/><br/>";
		
		echo '<input type="button" value="Schiffe" onclick="document.location=\'?page='.$page.'&amp;sub=ships\'" /><br/><br/>';
		echo '<input type="button" value="Rohstoffe" onclick="document.location=\'?page='.$page.'&amp;sub=ress\'" /><br/><br/>';
		echo '<input type="button" value="Auktionen" onclick="document.location=\'?page='.$page.'&amp;sub=auction\'" /><br/><br/>';

		echo "<h2>Rohstoffkurse</h2>";
		if (isset($_GET['action']) && $_GET['action']=="updaterates")
		{
			$tr = new PeriodicTaskRunner();
			success_msg($tr->runTask('MarketrateUpdateTask'));
		}

		echo "<table class=\"tb\" style=\"width:200px;\">";
		for ($i=0;$i<NUM_RESOURCES;$i++)
		{
			echo "<tr><th>".$resNames[$i]."</th><td>".RuntimeDataStore::get('market_rate_'.$i, 1)."</td></tr>";
		}
		echo "</table>";

		echo '<p>Die Marktkurse werden periodisch neu berechnet.</p>';
		echo '<input type="button" value="Kurse manuell aktualisieren" onclick="document.location=\'?page='.$page.'&amp;action=updaterates\'" /><br/><br/>';
			
		echo '</div>';

		echo '<img src="../misc/market.image.php" alt="Kursverlauf" style="float:right;" />';

	}

?>
