<?PHP
if (Alliance::checkActionRights('ranks'))
{


						echo "<h2>R&auml;nge</h2>";
	
						// Ränge speichern
						if (isset($_POST) && count($_POST)>0 && checker_verify())
						{
							if(isset($_POST['ranknew']))
							{
								dbquery("INSERT INTO ".$db_table['alliance_ranks']." (rank_alliance_id) VALUES (".$arr['alliance_id'].");");
							}
							if(isset($_POST['ranksubmit']) || isset($_POST['ranknew']))
							{
								if (isset($_POST['rank_name']) && count($_POST['rank_name'])>0)
								{
									foreach ($_POST['rank_name'] as $id=>$name)
									{
										dbquery("DELETE FROM ".$db_table['alliance_rankrights']." WHERE rr_rank_id=$id;");
										if (isset($_POST['rank_del'][$id]) && $_POST['rank_del'][$id]==1)
										{
											dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_id=$id;");
											dbquery("UPDATE ".$db_table['users']." SET user_alliance_rank_id=0 WHERE user_alliance_rank_id=$id;");
										}
										else
										{
											dbquery("UPDATE ".$db_table['alliance_ranks']." SET rank_name='".$name."' WHERE rank_id=$id;");
											if (isset($_POST['rankright']))
											{
												foreach ($_POST['rankright'][$id] as $rid=>$rv)
												{
													dbquery("INSERT INTO ".$db_table['alliance_rankrights']." (rr_right_id,rr_rank_id) VALUES ($rid,$id);");
												}
											}
										}
									}
								}
								echo 'Änderungen wurden übernommen!<br/><br/>';
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
							".$db_table['alliance_ranks']." 
						WHERE 
							rank_alliance_id=".$arr['alliance_id'].";");
						if (mysql_num_rows($rankres)>0)
						{
							tableStart("Verf&uuml;gbare R&auml;nge");
							echo "<tr><td class=\"tbltitle\">Rangname</td><td class=\"tbltitle\">Rechte</td><td class=\"tbltitle\">L&ouml;schen</td></tr>";
							while ($rarr = mysql_fetch_array($rankres))
							{
								echo "<tr><td class=\"tbldata\"><input type=\"text\" name=\"rank_name[".$rarr['rank_id']."]\" value=\"".$rarr['rank_name']."\" />";
								echo "<td class=\"tbldata\">";
								foreach ($rights as $k=>$v)
								{
									echo "<input type=\"checkbox\" name=\"rankright[".$rarr['rank_id']."][".$k."]\" value=\"1\" ";
									$rrres=dbquery("SELECT rr_id FROM ".$db_table['alliance_rankrights']." WHERE rr_right_id=".$k." AND rr_rank_id=".$rarr['rank_id'].";");
									if (mysql_num_rows($rrres)>0)
										echo " checked=\"checked\" /><span style=\"color:#0f0;\">".$v['desc']."</span><br/>";
									else
										echo" /> <span style=\"color:#f50;\">".$v['desc']."</span><br/>";
								}
								echo "</td>";

								echo "<td class=\"tbldata\"><input type=\"checkbox\" name=\"rank_del[".$rarr['rank_id']."]\" value=\"1\" /></td></tr>";
							}
							tableEnd();
							echo "<input type=\"submit\" name=\"ranksubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
						}
						else
						{
							echo "<i>Keine R&auml;nge vorhanden!</i><br/><br/>";
						}
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />&nbsp;&nbsp;&nbsp;
						<input type=\"submit\" name=\"ranknew\" value=\"Neuer Rang\" /></form>";
						
	}
?>