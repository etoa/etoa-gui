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

			$return_factor = 1 - (1/(MARKET_LEVEL+1));

			// Schiffangebot löschen
			// <editor-fold>
			if (isset($_POST['ship_cancel']))
			{
				if (isset($_POST['ship_market_id']))
				{
					$scres=dbquery("
					SELECT
						*
					FROM
						market_ship
					WHERE
						id='".$_POST['ship_market_id']."'
						AND user_id='".$cu->id."'");

					if (mysql_num_rows($scres)>0)
					{
						$scrow=mysql_fetch_array($scres);

						$marr = array('factor'=>$return_factor,"ship_id"=>$scrow['ship_id'],"ship_count"=>$scrow['count']);
						foreach ($resNames as $rk => $rn)
						{
							// todo: when non on the planet where the deal belongs to, the return_factor
							// is based on the local marketplace, for better or worse... change that so that the
							// origin marketplace return factor will be taken
							$marr['buy_'.$rk] = $scrow['costs_'.$rk];
						}

						$returnCount = floor($scrow['count']*$return_factor);
						$rsl = new ShipList($scrow['entity_id'],$scrow['user_id']);
						$rsl->add($scrow['ship_id'], $returnCount);

						dbquery("
						DELETE FROM
							market_ship
						WHERE
							id='".$_POST['ship_market_id']."'");

					MarketReport::add(array(
						'user_id'=>$cu->id,
						'entity1_id'=>$cp->id,
						'subject'=>"Schiffangebot zurückgezogen"
						), "shipcancel", $_POST['ship_market_id'], $marr);

						ok_msg("Angebot wurde gel&ouml;scht und du hast $returnCount (".(round($return_factor,2)*100)."%) der angebotenen Schiffe zur&uuml;ck erhalten (es wird abgerundet)");
					}
					else
					{
						error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
						return_btn(array("mode"=>"own"));
					}
				}
				else
				{
					error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
					return_btn(array("mode"=>"own"));
				}
			}
			// </editor-fold>

			// Rohstoffangebot löschen
			// <editor-fold>
			elseif (isset($_POST['ressource_cancel']) && isset($_POST['ressource_market_id']))
			{
				$rcres=dbquery("
				SELECT
					*
				FROM
					market_ressource
				WHERE
					id='".$_POST['ressource_market_id']."'
					AND user_id='".$cu->id."'");

				if (mysql_num_rows($rcres)>0)
				{
					$rcrow = mysql_fetch_assoc($rcres);

					$rarr = array();
					$marr = array('factor'=>$return_factor);
					foreach ($resNames as $rk => $rn)
					{
						if ($rcrow['sell_'.$rk]>0)
						{
							// todo: when non on the planet where the deal belongs to, the return_factor
							// is based on the local marketplace, for better or worse... change that so that the
							// origin marketplace return factor will be taken
							$rarr[$rk] = $rcrow['sell_'.$rk] * $return_factor;
							$marr['sell_'.$rk] = $rcrow['sell_'.$rk];
						}
					}

					$tp = Entity::createFactoryById($rcrow['entity_id']);
					$tp->addRes($rarr);
					unset($tp);

					MarketReport::add(array(
						'user_id'=>$cu->id,
						'entity1_id'=>$rcrow['entity_id'],
						'subject'=>"Rohstoffangebot zurückgezogen",
						), "rescancel", $_POST['ressource_market_id'], $marr);

					dbquery("
					DELETE FROM
						market_ressource
					WHERE
						id='".$_POST['ressource_market_id']."'");

					ok_msg("Angebot wurde gel&ouml;scht und du hast ".(round($return_factor,2)*100)."% der angebotenen Rohstoffe zur&uuml;ck erhalten!");
				}
				else
				{
					error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
					return_btn(array("mode"=>"user_sell"));
				}
			}
			// </editor-fold>

			//Auktionen löschen
			// <editor-fold>
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
			// </editor-fold>

			// Eigene Angebote zeigen
			else
			{
				$cstr=checker_init();

				//
				// Rohstoffe
				// <editor-fold>
				$res=dbquery("
				SELECT
					*
				FROM
					market_ressource
				WHERE
					user_id='".$cu->id."'
					AND buyable='1'
				ORDER BY
					datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					tableStart("Rohstoffe");
						echo "<tr>
						<th>Rohstoffe:</th>
						<th>Angebot:</th>
						<th>Preis:</th>
						<th>Marktplatz:</th>
						<th>Datum/Text:</th>
						<th>Zur&uuml;ckziehen:</th></tr>";
					$cnt=0;
					while ($row=mysql_fetch_array($res))
					{
						if($row['for_alliance']!=0)
							$for_alliance="<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
						else
							$for_alliance="";

						$i = 0;
						foreach ($resNames as $rk=>$rn)
						{
							echo "<tr>
							<td class=\"rescolor".$rk."\">".$resIcons[$rk]." <b>".$rn."</b>:</td>
							<td class=\"rescolor".$rk."\">".($row['sell_'.$rk]>0 ? nf($row['sell_'.$rk]) : '-')."</td>
							<td class=\"rescolor".$rk."\">".($row['buy_'.$rk]>0 ? nf($row['buy_'.$rk]) : '-')."</td>";
							if ($i++==0)
							{
								$te = Entity::createFactoryById($row['entity_id']);
								echo "<td rowspan=\"5\">".$te->detailLink()."</td>";
								echo "<td rowspan=\"5\">".date("d.m.Y  G:i:s", $row['datum'])."<br/><br/>".stripslashes($row['text'])."</td>";
								echo "<td rowspan=\"5\"><input type=\"radio\" name=\"ressource_market_id\" value=\"".$row['id']."\"><br/><br/>".$for_alliance."</td></tr>";
							}
							echo "</tr>";
						}
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
				// </editor-fold>

				//
				// Schiffe
				// <editor-fold>
				$res=dbquery("
				SELECT
					*
				FROM
					market_ship
				WHERE
					user_id='".$cu->id."'
					AND buyable='1'
				ORDER BY
					datum ASC");
				if (mysql_num_rows($res)>0)
				{
					echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
					echo $cstr;
					tableStart("Schiffe");

					echo "<tr>
					<th>Angebot:</th>
					<th colspan=\"2\">Preis:</th>
					<th>Datum/Text:</th>
					<th>Zur&uuml;ckziehen:</th></tr>";

					$cnt=0;
					while ($arr=mysql_fetch_array($res))
					{
						if($arr['for_alliance']!=0)
							$for_alliance="<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
						else
							$for_alliance="";

						$i=0;
						$resCnt = count($resNames);
						foreach ($resNames as $rk => $rn)
						{
							echo "<tr>";
							if ($i==0)
							{
								$ship = new Ship($arr['ship_id']);
								echo "<td rowspan=\"$resCnt\">".$arr['count']." <a href=\"?page=help&site=shipyard&id=".$arr['ship_id']."\">".$ship->toolTip()."</a></td>";
							}
							echo "<td class=\"rescolor".$rk."\">".$resIcons[$rk]."<b>".$rn."</b>:</td>
							<td class=\"rescolor".$rk."\">".nf($arr['costs_'.$rk])."</td>";
							if ($i++==0)
							{
								echo "<td rowspan=\"$resCnt\">".date("d.m.Y  G:i:s", $arr['datum'])."<br/><br/>".stripslashes($arr['text'])."</td>";
								echo "<td rowspan=\"$resCnt\"><input type=\"radio\" name=\"ship_market_id\" value=\"".$arr['id']."\"><br/><br/>".$for_alliance."</td>";

							}
							echo "</tr>";							
						}

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
				// </editor-fold>


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
	                else if( cnt >= 3600 && cnt < (3600*24) )
	                {

	                	nv = 'Noch '+h+'h '+m+'m '+s+'s';
	                }
	                else if( cnt < 3600 && cnt > 0)
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

?>
