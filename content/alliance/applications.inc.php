<?PHP

if (Alliance::checkActionRights('applications'))
{

						echo "<h2>Bewerbungen</h2><br>";
						if($_POST['applicationsubmit']!="" && checker_verify())
						{
							if (count($_POST['application_answer'])>0)
							{
								$message = "";
								$cnt = 0;
								$alliances = get_alliance_names();
								
								foreach ($_POST['application_answer'] as $id=>$answer)
								{
									if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id='".$s['user']['alliance_id']."' AND user_alliance_application!='' && user_id='".$id."';"))>0)
									{
										$nick = get_user_nick($id);
										// Anfrage annehmen
										if ($answer==2)
										{
											$cnt++;
											$message .= "<b>".$nick."</b>: Angenommen<br/>";
											
											// Nachricht an den Bewerber schicken
											send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung angenommen","Deine Allianzbewerbung wurde angnommen!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));
											
											// Log schreiben
											add_alliance_history($s['user']['alliance_id'],"Die Bewerbung von [b]".$nick."[/b] wurde akzeptiert!");
											add_log(5,"Der Spieler [b]".$nick."[/b] tritt der Allianz [b][".$alliances[$s['user']['alliance_id']]['tag']."] ".$alliances[$s['user']['alliance_id']]['name']."[/b] bei!",time());
											
											// Speichern
											dbquery("
											UPDATE 
												".$db_table['users']." 
											SET 
												user_alliance_application=NULL 
											WHERE 
												user_id='".$id."';");
										}
										// Anfrage ablehnen
										elseif($answer==1)
										{
											$cnt++;
											$message .= "<b>".$nick."</b>: Abgelehnt<br/>";

											// Nachricht an den Bewerber schicken
											send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung abgelehnt","Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));
											
											// Log schreiben
											add_alliance_history($s['user']['alliance_id'],"Die Bewerbung von [b]".$nick."[/b] wurde abgelehnt!");
											
											// Speichern
											dbquery("
											UPDATE 
											".$db_table['users']." 
											SET 
												user_alliance_id='0',
												user_alliance_application=NULL 
											WHERE 
												user_id='".$id."';");
										}
										// Anfrage unbearbeitet lassen, jedoch Nachricht verschicken wenn etwas geschrieben ist
										else
										{
											$text = str_replace(' ','',$_POST['application_answer_text'][$id]);
											if($text != '')
											{
												// Nachricht an den Bewerber schicken
												send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung: Nachricht","Antwort auf die Bewerbung an die Allianz [b][".$alliances[$s['user']['alliance_id']]['tag']."] ".$alliances[$s['user']['alliance_id']]['name']."[/b]:\n".$_POST['application_answer_text'][$id]."");
												
												$cnt++;
												$message .= "<b>".$nick."</b>: Nachricht gesendet<br>";
											}
										}
									}
									
								}
								echo "<br/>Änderungen übernommen<br><br>";
								if($cnt>0)
								{
									echo "".$message."<br><br>";
								}
							}
						}
						
						
						echo "<form action=\"?page=$page&action=applications\" method=\"post\">";
						checker_init();
						$res = dbquery("
						SELECT
	          	user_id,
	            user_alliance_application,
	            user_points,
	            user_rank_current,
	            user_registered
						FROM
							".$db_table['users']."
						WHERE
							user_alliance_id='".$s['user']['alliance_id']."'
							AND user_alliance_application!='';");
						if (mysql_num_rows($res)>0)
						{
							infobox_start("Bewerbungen prüfen",1);
							echo "<tr>
											<td class=\"tbltitle\" width=\"10%\">User</td>
											<td class=\"tbltitle\" width=\"35%\">Text</td>
											<td class=\"tbltitle\" width=\"35%\">Nachricht</td>
											<td class=\"tbltitle\" width=\"20%\">Aktion</td>
										</tr>";
							while ($arr = mysql_fetch_array($res))
							{
								$user = get_user_nick($arr['user_id']);
								echo "<tr>
												<td class=\"tbldata\" ".tm("Info","Rang: ".$arr['user_rank_current']."<br>Punkte: ".nf($arr['user_points'])."<br>Registriert: ".date("d.m.Y H:i",$arr['user_registered'])."").">
													<a href=\"?page=userinfo&id=".$arr['user_id']."\">".$user."</a>
												</td>
												<td class=\"tbldata\">".text2html($arr['user_alliance_application'])."</td>
												<td class=\"tbldata\">
													<textarea rows=\"6\" cols=\"40\" name=\"application_answer_text[".$arr['user_id']."]\" /></textarea>
												</td>
												<td class=\"tbldata\">
													<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"2\"/> <span ".tm("Anfrage annehmen","".$user." wird in die Allianz aufgenommen.<br>Eine Nachricht wird versendet.").">Annehmen</span><br><br>
													<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"1\"/> <span ".tm("Anfrage ablehnen","".$user." wird der Zutritt zu der Allianz verweigert.<br>Eine Nachricht wird versendet.").">Ablehnen</span><br><br>
													<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"0\" checked=\"checked\"/> <span ".tm("Anfrage nicht bearbeiten","Sofern vorhanden, wird eine Nachricht an ".$user." geschickt.").">Nicht bearbeiten</span>
												</td>
												</tr>";
							}
							infobox_end(1);
							echo "<input type=\"submit\" name=\"applicationsubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
						}
						else
						{
							echo "<i>Keine Bewerbungen vorhanden!</i><br/><br/>";
						}
						echo "<input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" /></form>";


}

?>