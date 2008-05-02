<?PHP

if (Alliance::checkActionRights('applications'))
{

		echo "<h2>Bewerbungen</h2><br>";
		if(isset($_POST['applicationsubmit']) && checker_verify())
		{
			if (count($_POST['application_answer'])>0)
			{
				$message = "";
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
						$message .= "<b>".$nick."</b>: Angenommen<br/>";
						
						// Nachricht an den Bewerber schicken
						send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung angenommen","Deine Allianzbewerbung wurde angnommen!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));
						
						// Log schreiben
						add_alliance_history($cu->alliance_id,"Die Bewerbung von [b]".$nick."[/b] wurde akzeptiert!");
						add_log(5,"Der Spieler [b]".$nick."[/b] tritt der Allianz [b][".$alliances[$cu->alliance_id]['tag']."] ".$alliances[$cu->alliance_id]['name']."[/b] bei!",time());
						
						// Speichern
						dbquery("
						UPDATE 
							users
						SET 
							user_alliance_id=".$cu->alliance_id."
						WHERE 
							user_id='".$id."';");
							
						dbquery("
						DELETE FROM
							alliance_applications 
						WHERE
							user_id=".$id."
							AND alliance_id=".$cu->alliance_id.";");								
					}
					// Anfrage ablehnen
					elseif($answer==1)
					{
						$cnt++;
						$message .= "<b>".$nick."</b>: Abgelehnt<br/>";

						// Nachricht an den Bewerber schicken
						send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung abgelehnt","Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));
						
						// Log schreiben
						add_alliance_history($cu->alliance_id,"Die Bewerbung von [b]".$nick."[/b] wurde abgelehnt!");
						
						// Anfrage löschen
						dbquery("
						DELETE FROM
							alliance_applications 
						WHERE
							user_id=".$id."
							AND alliance_id=".$cu->alliance_id.";");								
					}
					// Anfrage unbearbeitet lassen, jedoch Nachricht verschicken wenn etwas geschrieben ist
					else
					{
						$text = str_replace(' ','',$_POST['application_answer_text'][$id]);
						if($text != '')
						{
							// Nachricht an den Bewerber schicken
							send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung: Nachricht","Antwort auf die Bewerbung an die Allianz [b][".$alliances[$cu->alliance_id]['tag']."] ".$alliances[$cu->alliance_id]['name']."[/b]:\n".$_POST['application_answer_text'][$id]."");
							
							$cnt++;
							$message .= "<b>".$nick."</b>: Nachricht gesendet<br>";
						}
					}					
				}
				
				// Wenn neue Members hinzugefügt worde sind werden ev. die Allianzrohstoffe angepasst
				if($new_member)
				{
					// Zählt aktuelle Memberanzahl und und läd den Wert, für welche Anzahl User die Allianzobjekte gebaut wurden
					$check_res=dbquery("
					SELECT
						(
							SELECT 
								COUNT(*)
							FROM 
								users
							WHERE
								user_alliance_id='".$cu->alliance_id."'
						) AS member_cnt,
						(
							SELECT 
								alliance_objects_for_members
							FROM 
								alliances
							WHERE
								alliance_id='".$cu->alliance_id."'

						) AS built_for_members
						;");
						
					$check_arr=mysql_fetch_assoc($check_res);
						
					// Allianzrohstoffe anpassen, wenn die Allianzobjekte nicht für diese Anzahl ausgebaut sind
					if($check_arr['member_cnt'] > $check_arr['built_for_members'])
					{
						$costs_metal = 0;
						$costs_crystal = 0;
						$costs_plastic = 0;
						$costs_fuel = 0;
						$costs_food = 0;
						$member_factor = pow($check_arr['built_for_members'],$conf['alliance_membercosts_factor']['v']);
						
						$new_costs_metal = 0;
						$new_costs_crystal = 0;
						$new_costs_plastic = 0;
						$new_costs_fuel = 0;
						$new_costs_food = 0;
						$new_member_factor = pow($check_arr['member_cnt'],$conf['alliance_membercosts_factor']['v']);
						
						// Berechnet Kostendifferenz
						
						// Allianzgebäude
						$res = dbquery("
						SELECT
							alliance_building_costs_metal,
							alliance_building_costs_crystal,
							alliance_building_costs_plastic,
							alliance_building_costs_fuel,
							alliance_building_costs_food,
							alliance_building_costs_factor,
						
							alliance_buildlist_current_level,
							alliance_buildlist_build_end_time
						FROM
								alliance_buildings
							INNER JOIN
								alliance_buildlist
							ON
								alliance_building_id=alliance_buildlist_building_id
						WHERE
							alliance_buildlist_alliance_id='".$cu->alliance_id."';");
						if(mysql_num_rows($res)>0)
						{
							while($arr=mysql_fetch_assoc($res))		
							{
								// Wenn ein Gebäude in Bau ist, wird die Stufe zur berechnung bereits erhöht
								if($arr['alliance_buildlist_build_end_time']>0)
								{
									$level = $arr['alliance_buildlist_current_level'] + 1;
								}
								else
								{
									$level = $arr['alliance_buildlist_current_level'];
								}
								
								// Berechnungen nur durchführen, wenn die Stufe >0 ist oder sich das Objekt in Bau befindet
								// Dies ist eine Sicherheit für den Fall, dass die Stufe manuel zurückgesetzt wird. Es würden falsche Kosten entstehen
								if($arr['alliance_buildlist_current_level']>0 || $arr['alliance_buildlist_build_end_time']>0)
								{									
									// Kosten von jedem Level des Gebäudes wird berechnet
									for ($x=1;$x<=$level;$x++)
									{
										$factor = pow($arr['alliance_building_costs_factor'],$x-1);
										
										// Summiert Kosten aller Gebäude mit der alten Anzahl Members
										if($factor<1)
										{
											$factor = 1;
										}
										$costs_metal += ceil($arr['alliance_building_costs_metal'] * $factor * $member_factor);
										$costs_crystal += ceil($arr['alliance_building_costs_crystal'] * $factor * $member_factor);
										$costs_plastic += ceil($arr['alliance_building_costs_plastic'] * $factor * $member_factor);
										$costs_fuel += ceil($arr['alliance_building_costs_fuel'] * $factor * $member_factor);
										$costs_food += ceil($arr['alliance_building_costs_food'] * $factor * $member_factor);
										
										// Summiert Kosten aller Gebäude mit der neuen Anzahl Members
										if($factor<1)
										{
											$factor = 1;
										}
										$new_costs_metal += ceil($arr['alliance_building_costs_metal'] * $factor * $new_member_factor);
										$new_costs_crystal += ceil($arr['alliance_building_costs_crystal'] * $factor * $new_member_factor);
										$new_costs_plastic += ceil($arr['alliance_building_costs_plastic'] * $factor * $new_member_factor);
										$new_costs_fuel += ceil($arr['alliance_building_costs_fuel'] * $factor * $new_member_factor);
										$new_costs_food += ceil($arr['alliance_building_costs_food'] * $factor * $new_member_factor);
									}
								}
							}
						}
						
						
						// Allianzforschungen
						$res = dbquery("
						SELECT
							alliance_tech_costs_metal,
							alliance_tech_costs_crystal,
							alliance_tech_costs_plastic,
							alliance_tech_costs_fuel,
							alliance_tech_costs_food,
							alliance_tech_costs_factor,
						
							alliance_techlist_current_level,
							alliance_techlist_build_end_time
						FROM
								alliance_technologies
							INNER JOIN
								alliance_techlist
							ON
								alliance_tech_id=alliance_techlist_tech_id
						WHERE
							alliance_techlist_alliance_id='".$cu->alliance_id."';");
						if(mysql_num_rows($res)>0)
						{
							while($arr=mysql_fetch_assoc($res))		
							{
								// Wenn eine Forschung in Bau ist, wird die Stufe zur Berechnung bereits erhöht
								if($arr['alliance_techlist_build_end_time']>0)
								{
									$level = $arr['alliance_techlist_current_level'] + 1;
								}
								else
								{
									$level = $arr['alliance_techlist_current_level'];
								}
								
								// Berechnungen nur durchführen, wenn die Stufe >0 ist oder sich das Objekt in Bau befindet
								// Dies ist eine Sicherheit für den Fall, dass die Stufe manuel zurückgesetzt wird. Es würden falsche Kosten entstehen
								if($arr['alliance_techlist_current_level']>0 || $arr['alliance_techlist_build_end_time']>0)
								{
									// Kosten von jedem Level der Forschung wird berechnet
									for ($x=1;$x<=$level;$x++)
									{
										$factor = pow($arr['alliance_tech_costs_factor'],$x-1);
										
										// Summiert Kosten aller Forschungen mit der alten Anzahl Members
										if($factor<1)
										{
											$factor = 1;
										}
										$costs_metal += ceil($arr['alliance_tech_costs_metal'] * $factor * $member_factor);
										$costs_crystal += ceil($arr['alliance_tech_costs_crystal'] * $factor * $member_factor);
										$costs_plastic += ceil($arr['alliance_tech_costs_plastic'] * $factor * $member_factor);
										$costs_fuel += ceil($arr['alliance_tech_costs_fuel'] * $factor * $member_factor);
										$costs_food += ceil($arr['alliance_tech_costs_food'] * $factor * $member_factor);
										
										// Summiert Kosten aller Forschungen mit der neuen Anzahl Members
										if($factor<1)
										{
											$factor = 1;
										}
										$new_costs_metal += ceil($arr['alliance_tech_costs_metal'] * $factor * $new_member_factor);
										$new_costs_crystal += ceil($arr['alliance_tech_costs_crystal'] * $factor * $new_member_factor);
										$new_costs_plastic += ceil($arr['alliance_tech_costs_plastic'] * $factor * $new_member_factor);
										$new_costs_fuel += ceil($arr['alliance_tech_costs_fuel'] * $factor * $new_member_factor);
										$new_costs_food += ceil($arr['alliance_tech_costs_food'] * $factor * $new_member_factor);
									}
								}
							}
						}
						
						// Berechnet die zu zahlenden Rohstoffe
						$metal = $new_costs_metal - $costs_metal;
						$crystal = $new_costs_crystal - $costs_crystal;
						$plastic = $new_costs_plastic - $costs_plastic;
						$fuel = $new_costs_fuel - $costs_fuel;
						$food = $new_costs_food - $costs_food;

						// Zieht Rohstoffe vom Allianzkonto ab und speichert Anzahl Members, für welche nun bezahlt ist
						if($metal>0 || $crystal>0 || $plastic>0 || $fuel>0 || $food>0)
						{
							dbquery("
				      UPDATE
				        alliances
				      SET
				        alliance_res_metal=alliance_res_metal-'".$metal."',
				        alliance_res_crystal=alliance_res_crystal-'".$crystal."',
				        alliance_res_plastic=alliance_res_plastic-'".$plastic."',
				        alliance_res_fuel=alliance_res_fuel-'".$fuel."',
				        alliance_res_food=alliance_res_food-'".$food."',
				        alliance_objects_for_members='".$check_arr['member_cnt']."'
				      WHERE
				        alliance_id='".$cu->alliance_id."';");
				        
				      // Log schreiben
							add_alliance_history($cu->alliance_id,"Dem Allianzkonto wurden folgende Rohstoffe abgezogen:\n[b]".RES_METAL."[/b]: ".nf($metal)."\n[b]".RES_CRYSTAL."[/b]: ".nf($crystal)."\n[b]".RES_PLASTIC."[/b]: ".nf($plastic)."\n[b]".RES_FUEL."[/b]: ".nf($fuel)."\n[b]".RES_FOOD."[/b]: ".nf($food)."\n\nDie Allianzobjekte sind nun für ".$check_arr['member_cnt']." Mitglieder verfügbar!");
						}
						else
						{
							dbquery("
				      UPDATE
				        alliances
				      SET
				        alliance_objects_for_members='".$check_arr['member_cnt']."'
				      WHERE
				        alliance_id='".$cu->alliance_id."';");
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
			AND aa.alliance_id=".$cu->alliance_id.";");
		if (mysql_num_rows($res)>0)
		{
			infobox_start("Bewerbungen prüfen",1);
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