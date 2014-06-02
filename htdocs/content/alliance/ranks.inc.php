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

if (Alliance::checkActionRights('ranks'))
{


						echo "<h2>R&auml;nge</h2>";
	
						// Ränge speichern
						if (isset($_POST) && count($_POST)>0 && checker_verify())
						{
							if(isset($_POST['ranknew']))
							{
								dbquery("INSERT INTO alliance_ranks (rank_alliance_id) VALUES (".$arr['alliance_id'].");");
							}
							if(isset($_POST['ranksubmit']) || isset($_POST['ranknew']))
							{
								if (isset($_POST['rank_name']) && count($_POST['rank_name'])>0)
								{
									foreach ($_POST['rank_name'] as $id=>$name)
									{
						                $id = intval($id);
										dbquery("DELETE FROM alliance_rankrights WHERE rr_rank_id=$id;");
										if (isset($_POST['rank_del'][$id]) && $_POST['rank_del'][$id]==1)
										{
											dbquery("DELETE FROM alliance_ranks WHERE rank_id=$id;");
											dbquery("UPDATE users SET user_alliance_rank_id=0 WHERE user_alliance_rank_id=$id;");
										}
										else
										{
											dbquery("UPDATE 
												alliance_ranks 
											SET 
												rank_name='".mysql_real_escape_string($name)."',
												rank_level='".$_POST['rank_level'][$id]."' 
											WHERE rank_id=$id;");
											if (isset($_POST['rankright']) && isset($_POST['rankright'][$id]))
											{
												foreach ($_POST['rankright'][$id] as $rid=>$rv)
												{
												    $rid = intval($rid);
													dbquery("INSERT INTO alliance_rankrights (rr_right_id,rr_rank_id) VALUES ($rid,$id);");
												}
											}
										}
									}
								}
								ok_msg("Änderungen wurden übernommen!");
							}
						}
						echo "<form action=\"?page=$page&action=ranks\" method=\"post\">";
						checker_init();

						$rankres=dbquery("
						SELECT 
							rank_name,
							rank_id,
							rank_level 
						FROM 
							alliance_ranks 
						WHERE 
							rank_alliance_id=".$arr['alliance_id']."
						ORDER BY rank_level;");
						if (mysql_num_rows($rankres)>0)
						{
							tableStart("Verf&uuml;gbare R&auml;nge");
							echo "<tr>
								<th>Rangname:</th>
								<th>Rechte:</th>
								<th>L&ouml;schen:</th>
							</tr>";
							while ($rarr = mysql_fetch_array($rankres))
							{
								echo "<tr>
									<td>
										<input type=\"text\" name=\"rank_name[".$rarr['rank_id']."]\" value=\"".$rarr['rank_name']."\" /><br/>
										Level: <input type=\"text\" name=\"rank_level[".$rarr['rank_id']."]\" value=\"".$rarr['rank_level']."\" maxlength=\"1\" size=\"2\" />
									</td>
									<td>";
								foreach ($rights as $k=>$v)
								{
									echo "<input type=\"checkbox\" name=\"rankright[".$rarr['rank_id']."][".$k."]\" value=\"1\" ";
									$rrres=dbquery("SELECT rr_id FROM alliance_rankrights WHERE rr_right_id=".$k." AND rr_rank_id=".$rarr['rank_id'].";");
									if (mysql_num_rows($rrres)>0)
										echo " checked=\"checked\" /><span style=\"color:#0f0;\">".$v['desc']."</span><br/>";
									else
										echo" /> <span style=\"color:#f50;\">".$v['desc']."</span><br/>";
								}
								echo "</td>";

								echo "<td><input type=\"checkbox\" name=\"rank_del[".$rarr['rank_id']."]\" value=\"1\" /></td></tr>";
							}
							tableEnd();
							echo "<input type=\"submit\" name=\"ranksubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
						}
						else
						{
							error_msg("Keine R&auml;nge vorhanden!");
						}
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />&nbsp;&nbsp;&nbsp;
						<input type=\"submit\" name=\"ranknew\" value=\"Neuer Rang\" /></form>";
						
	}
?>