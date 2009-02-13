<?PHP

if (Alliance::checkActionRights('applications'))
{

		echo "<h2>Bewerbungen</h2><br>";
		if(isset($_POST['applicationsubmit']) && checker_verify())
		{
			if (count($_POST['application_answer'])>0)
			{
				$cnt = 0;
				$alliances = get_alliance_names();
				$new_member = false;
				
				foreach ($_POST['application_answer'] as $id=>$answer)
				{

					$nick = $_POST['application_user_nick_'.$id.''];
					
					// Anfrage annehmen
					if ($answer==2)
					{
						$cnt++;
						$new_member = true;
						ok_msg($nick." wurde angenommen.");
						
						// Nachricht an den Bewerber schicken
						send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung angenommen","Deine Allianzbewerbung wurde angenommen!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));
						
						// Log schreiben
						add_alliance_history($cu->allianceId,"Die Bewerbung von [b]".$nick."[/b] wurde akzeptiert!");
						add_log(5,"Der Spieler [b]".$nick."[/b] tritt der Allianz [b][".$alliances[$cu->allianceId]['tag']."] ".$alliances[$cu->allianceId]['name']."[/b] bei!",time());
						
						$tu = new User($id);
						$tu->addToUserLog("alliance","{nick} ist nun ein Mitglied der der Allianz ".$alliances[$cu->allianceId]['name'].".");
						
						// Speichern
						dbquery("
						UPDATE 
							users
						SET 
							user_alliance_id=".$cu->allianceId."
						WHERE 
							user_id='".$id."';");
							
						dbquery("
						DELETE FROM
							alliance_applications 
						WHERE
							user_id=".$id."
							AND alliance_id=".$cu->allianceId.";");								
					}
					// Anfrage ablehnen
					elseif($answer==1)
					{
						$cnt++;
						ok_msg($nick." wurde abgelehnt.");

						// Nachricht an den Bewerber schicken
						send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung abgelehnt","Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));
						
						// Log schreiben
						add_alliance_history($cu->allianceId,"Die Bewerbung von [b]".$nick."[/b] wurde abgelehnt!");
						
						// Anfrage löschen
						dbquery("
						DELETE FROM
							alliance_applications 
						WHERE
							user_id=".$id."
							AND alliance_id=".$cu->allianceId.";");								
					}
					// Anfrage unbearbeitet lassen, jedoch Nachricht verschicken wenn etwas geschrieben ist
					else
					{
						$text = str_replace(' ','',$_POST['application_answer_text'][$id]);
						if($text != '')
						{
							// Nachricht an den Bewerber schicken
							send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung: Nachricht","Antwort auf die Bewerbung an die Allianz [b][".$alliances[$cu->allianceId]['tag']."] ".$alliances[$cu->allianceId]['name']."[/b]:\n".$_POST['application_answer_text'][$id]."");
							
							$cnt++;
							ok_msg($nick.": Nachricht gesendet");
						}
					}					
				}
				
				// Wenn neue Members hinzugefügt worde sind werden ev. die Allianzrohstoffe angepasst
				if($new_member)
				{
					$cu->alliance->calcMemberCosts();		
				}
				
				ok_msg("Änderungen übernommen");
			}
		}
						
						
		echo "<form action=\"?page=$page&action=applications\" method=\"post\">";
		checker_init();
		$res = dbquery("
		SELECT
			aa.timestamp,
			aa.text,
    	u.user_id,
    	u.user_nick,
      u.user_points,
      u.user_rank,
      u.user_registered
		FROM
			alliance_applications as aa
		INNER JOIN
			users as u
		ON
			aa.user_id=u.user_id
			AND aa.alliance_id=".$cu->allianceId.";");
		if (mysql_num_rows($res)>0)
		{
			tableStart("Bewerbungen prüfen");
			echo "<tr>
							<td class=\"tbltitle\" width=\"10%\">User</td>
							<td class=\"tbltitle\" width=\"35%\">Datum / Text</td>
							<td class=\"tbltitle\" width=\"35%\">Nachricht</td>
							<td class=\"tbltitle\" width=\"20%\">Aktion</td>
						</tr>";
			while ($arr = mysql_fetch_array($res))
			{
				echo "<tr>
				<td class=\"tbldata\" ".tm("Info","Rang: ".$arr['user_rank']."<br>Punkte: ".nf($arr['user_points'])."<br>Registriert: ".date("d.m.Y H:i",$arr['user_registered'])."").">
					<a href=\"?page=userinfo&id=".$arr['user_id']."\">".$arr['user_nick']."</a>";
					
					// Übergibt Usernick dem Formular, damit beim Submit nicht nochmals eine DB Abfrage gestartet werden muss
					echo "<input type=\"hidden\" name=\"application_user_nick_".$arr['user_id']."\" value=\"".$arr['user_nick']."\" />";
	echo "</td>
				<td class=\"tbldata\">".df($arr['timestamp'])."<br/><br/>".text2html($arr['text'])."</td>
				<td class=\"tbldata\">
					<textarea rows=\"6\" cols=\"40\" name=\"application_answer_text[".$arr['user_id']."]\" /></textarea>
				</td>
				<td class=\"tbldata\">
					<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"2\"/> <span ".tm("Anfrage annehmen","".$arr['user_nick']." wird in die Allianz aufgenommen.<br>Eine Nachricht wird versendet.").">Annehmen</span><br><br>
					<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"1\"/> <span ".tm("Anfrage ablehnen","".$arr['user_nick']." wird der Zutritt zu der Allianz verweigert.<br>Eine Nachricht wird versendet.").">Ablehnen</span><br><br>
					<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"0\" checked=\"checked\"/> <span ".tm("Anfrage nicht bearbeiten","Sofern vorhanden, wird eine Nachricht an ".$arr['user_nick']." geschickt.").">Nicht bearbeiten</span>
				</td>
				</tr>";
			}
			tableEnd();
			echo "<input type=\"submit\" name=\"applicationsubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
		}
		else
		{
			error_msg("Keine Bewerbungen vorhanden!");
		}
		echo "<input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" /></form>";


}

?>