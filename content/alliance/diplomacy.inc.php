
<script type="text/javascript">
	function checkWarDeclaration()
	{
		f = document.forms['wardeclaration'];
		if (f.alliance_bnd_text.value=="")
		{
			alert("Du musst eine Nachricht schreiben!");
			f.alliance_bnd_text.focus();
			return false;
		}
		if (f.alliance_bnd_text_pub.value=="")
		{
			alert("Du musst eine öffentliche Kriegserklärung hinzufügen!");
			f.alliance_bnd_text_pub.focus();
			return false;
		}		
		return true;
	}
	
	function checkPactOffer()
	{
		f = document.forms['pactoffer'];
		if (f.alliance_bnd_name.value=="")
		{
			alert("Du musst dem Bündnis einen Namen geben!");
			f.alliance_bnd_name.focus();
			return false;
		}
		if (f.alliance_bnd_text.value=="")
		{
			alert("Du musst eine Nachricht schreiben!");
			f.alliance_bnd_text.focus();
			return false;
		}
		
		return true;		
	}	
	
	function checkEndPact()
	{
		f = document.forms['endpact'];
		if (f.pact_end_text.value=="")
		{
			alert("Du musst eine Nachricht schreiben!");
			f.pact_end_text.focus();
			return false;
		}
		return true;		
	}
</script>

<?PHP

	if (Alliance::checkActionRights('relations'))
	{
		echo "<h2>Diplomatie</h2>";

		

			$alliances = get_alliance_names();

			//
			// Kriegserklärung schreiben
			//
			if (isset($_GET['begin_war']) && $_GET['begin_war']>0)
			{
				$check = false;
				if(!isset($_GET['begin_bnd']) || $_GET['begin_bnd']!=$cu->allianceId())
				{
					$check = true;
				}
				
				$ares=dbquery("SELECT * FROM ".$db_table['alliances']." WHERE alliance_id='".$_GET['begin_war']."';");
				if (mysql_num_rows($ares)>0 && $check)
				{
					$aarr=mysql_fetch_array($ares);
					echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"wardeclaration\">";
					checker_init();
					
					tableStart("Kriegserkl&auml;rung an die Allianz [".$aarr['alliance_tag']."] ".$aarr['alliance_name']);
					echo "<tr><th class=\"tbltitle\">Nachricht:</th><td class=\"tbldata\"><textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea></td></tr>";
					echo "<tr><th class=\"tbltitle\">Öffentlicher Text:</th><td class=\"tbldata\"><textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text_pub\"></textarea></td></tr>";
					tableEnd();
					
					echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"".$aarr['alliance_id']."\" />";
					echo "<input type=\"submit\" name=\"sbmit_new_war\" value=\"Senden\" onclick=\"return checkWarDeclaration()\" onsubmit=\"return checkWarDeclaration()\" />&nbsp;
					<input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
					echo "</form>";
				}
				else
				{
					echo "<b>Fehler:</b> Diese Allianz existiert nicht!<br/><br/>";
				}
			}

			//
			// Bündnisanfrage schreiben
			//
			elseif (isset($_GET['begin_bnd']) && $_GET['begin_bnd']>0)
			{
				$ares=dbquery("
				SELECT
					alliance_id,
					alliance_tag,
					alliance_name,
					alliance_accept_bnd
				FROM
					".$db_table['alliances']."
				WHERE
					alliance_id='".$_GET['begin_bnd']."';");
				if (mysql_num_rows($ares)>0 && isset($_GET['begin_bnd']) && $_GET['begin_bnd']!=$cu->allianceId())
				{
					$aarr=mysql_fetch_array($ares);
					
					if($aarr['alliance_accept_bnd']==1)
					{
						echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"pactoffer\">";
						checker_init();
						
						tableStart("B&uuml;ndnisanfrage an die Allianz [".$aarr['alliance_tag']."] ".$aarr['alliance_name']);
						echo "<tr>
							<th class=\"tbltitle\">Name des Bündnisses:</th>
							<td class=\"tbldata\">
								<input type=\"text\" size=\"30\" maxlength=\"30\" name=\"alliance_bnd_name\" />
							</td>
						</tr>";
						echo "<tr>
							<th class=\"tbltitle\">Bündnisanfrage:</th>
							<td class=\"tbldata\">
								<textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea>
							</td>
						</tr>";
						tableEnd();
						
						echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"".$aarr['alliance_id']."\" />";
						echo "<input type=\"submit\" name=\"sbmit_new_bnd\" value=\"Senden\" onclick=\"return checkPactOffer()\" onsubmit=\"return checkPactOffer()\" />&nbsp;
						<input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
						echo "</form>";
					}
					else
					{
						echo "Die Allianz nimmt keine Bündnisanfragen an!<br>";
					}
				}
				else
				{
					echo "<b>Fehler:</b> Diese Allianz existiert nicht!<br/><br/>";
				}
			}

			//
			// Büdniss/Kriegs- Text ansehen
			//
			elseif (isset($_GET['view']) && $_GET['view']>0)
			{
				$id=$_GET['view'];					
				$res = dbquery("
				SELECT 
					alliance_bnd_text,
					alliance_bnd_text_pub,
					a1.alliance_id as a1id,
					a2.alliance_id as a2id,
					a1.alliance_name as a1name,
					a2.alliance_name as a2name,
					alliance_bnd_name,
					alliance_bnd_level
				FROM 
					".$db_table['alliance_bnd']." 
				INNER JOIN
					alliances as a1
					ON a1.alliance_id=alliance_bnd_alliance_id1
				INNER JOIN
					alliances as a2
					ON a2.alliance_id=alliance_bnd_alliance_id2					
				WHERE 
					(alliance_bnd_alliance_id1='".$cu->allianceId()."' 
					OR alliance_bnd_alliance_id2='".$cu->allianceId()."') 
					AND alliance_bnd_id='".$id."'
				;");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);
					if ($arr['a1id']==$cu->allianceId())
					{
						$opId = $arr['a2id'];
						$opName = $arr['a2name'];
					}
					else
					{
						$opId = $arr['a1id'];
						$opName = $arr['a1name'];
					}
					
					echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\">";
					
					switch ($arr['alliance_bnd_level'])
					{
						case 0:
							tableStart("Status der Bündnissanfrage");
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Allianz</th>
								<td class=\"tbldata\">".$opName."</td>
							</tr>";
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Bündnissname</th>
								<td class=\"tbldata\">".text2html($arr['alliance_bnd_name'])."</td>
							</tr>";
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Text</th>
								<td class=\"tbldata\">".text2html($arr['alliance_bnd_text'])."</td>
							</tr>";
							if ($arr['a1id']==$cu->allianceId())
							{					
								echo "<tr>
									<th class=\"tbltitle\" style=\"width:200px;\">Status</th>
									<td class=\"tbldata\">Die Anfrage wurde noch nicht angenommen.</td>
								</tr>";
							}
							else
							{
								echo "<tr>
									<th class=\"tbltitle\" style=\"width:200px;\">Antwort</th>
									<td class=\"tbldata\"><textarea name=\"pact_answer\" rows=\"6\" cols=\"70\"></textarea></td>
								</tr>";
							}
							tableEnd();				
							echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";					
							if ($arr['a1id']==$cu->allianceId())
							{							
								echo "<input type=\"submit\" name=\"submit_withdraw_pact\" value=\"Bündnisangebot zurückziehen\" onclick=\"return confirm('Angebot wirklich zurückziehen?')\" /> &nbsp; ";
							}
							else
							{
								echo "<input type=\"submit\" name=\"pact_accept\" value=\"Bündnisangebot annehmen\" /> &nbsp; ";								
								echo "<input type=\"submit\" name=\"pact_reject\" value=\"Bündnisangebot ablehnen\" /> &nbsp; ";								
							}								
							break;
						case 2:
							tableStart("Bündnis \"".$arr['alliance_bnd_name']."\"");
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Allianz</th>
								<td class=\"tbldata\">".$opName."</td>
							</tr>";
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Anfragetext</th>
								<td class=\"tbldata\">".text2html($arr['alliance_bnd_text'])."</td>
							</tr>";
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Öffentlicher Text</th>
								<td class=\"tbldata\"><textarea name=\"alliance_bnd_text_pub\" rows=\"6\" cols=\"70\">".stripslashes($arr['alliance_bnd_text_pub'])."</textarea></td>
							</tr>";
							tableEnd();				
							echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";					
							echo "<input type=\"submit\" name=\"submit_pact_public_text\" value=\"Speichern\" /> &nbsp; ";
							break;		
						case 3:
							tableStart("Krieg");
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Allianz</th>
								<td class=\"tbldata\">".$opName."</td>
							</tr>";
							echo "<tr>
								<th class=\"tbltitle\" style=\"width:200px;\">Kriegserklärung</th>
								<td class=\"tbldata\">".text2html($arr['alliance_bnd_text'])."</td>
							</tr>";
							if ($arr['a1id']==$cu->allianceId())
							{
								echo "<tr>
									<th class=\"tbltitle\" style=\"width:200px;\">Öffentlicher Text</th>
									<td class=\"tbldata\"><textarea name=\"alliance_bnd_text_pub\" rows=\"6\" cols=\"70\">".stripslashes($arr['alliance_bnd_text_pub'])."</textarea></td>
								</tr>";
							}
							else
							{
								echo "<tr>
									<th class=\"tbltitle\" style=\"width:200px;\">Öffentlicher Text</th>
									<td class=\"tbldata\">".text2html($arr['alliance_bnd_text_pub'])."</td>
								</tr>";
							}
							tableEnd();				
							if ($arr['a1id']==$cu->allianceId())
							{
								echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";					
								echo "<input type=\"submit\" name=\"submit_war_public_text\" value=\"Speichern\" /> &nbsp; ";
							}
							break;												
						default:
							echo "Test";					
					}
					echo "<input type=\"button\" onclick=\"document.location='?page=alliance&amp;action=relations';\" value=\"Zur&uuml;ck\" />";
					echo "</form>";
				}
				else
				{
					echo "Datensatz nicht vorhanden!";
				}
			}

			//
			// End pact
			//
			elseif (isset($_GET['end_pact']) && $_GET['end_pact']>0)
			{
				$id=$_GET['end_pact'];					
				$res = dbquery("
				SELECT 
					alliance_bnd_text,
					alliance_bnd_text_pub,
					a1.alliance_id as a1id,
					a2.alliance_id as a2id,
					a1.alliance_name as a1name,
					a2.alliance_name as a2name,
					alliance_bnd_name,
					alliance_bnd_level
				FROM 
					".$db_table['alliance_bnd']." 
				INNER JOIN
					alliances as a1
					ON a1.alliance_id=alliance_bnd_alliance_id1
				INNER JOIN
					alliances as a2
					ON a2.alliance_id=alliance_bnd_alliance_id2					
				WHERE 
					(alliance_bnd_alliance_id1='".$cu->allianceId()."' 
					OR alliance_bnd_alliance_id2='".$cu->allianceId()."') 
					AND alliance_bnd_id='".$id."'
					AND alliance_bnd_level=2
				;");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);
					if ($arr['a1id']==$cu->allianceId())
					{
						$opId = $arr['a2id'];
						$opName = $arr['a2name'];
					}
					else
					{
						$opId = $arr['a1id'];
						$opName = $arr['a1name'];
					}
					
					echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"endpact\">";

					tableStart("Bündnis \"".stripslashes($arr['alliance_bnd_name'])."\" beenden");
					echo "<tr>
						<th class=\"tbltitle\" style=\"width:200px;\">Allianz</th>
						<td class=\"tbldata\">".$opName."</td>
					</tr>";
					echo "<tr>
						<th class=\"tbltitle\" style=\"width:200px;\">Begründung</th>
						<td class=\"tbldata\"><textarea name=\"pact_end_text\" rows=\"6\" cols=\"70\"></textarea></td>
					</tr>";
					tableEnd();				
					echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";					
					echo "<input type=\"submit\" name=\"submit_pact_end\" value=\"Auflösen\"  onclick=\"return checkEndPact()\" onsubmit=\"return checkEndPact()\" /> &nbsp; ";
					echo "<input type=\"button\" onclick=\"document.location='?page=alliance&amp;action=relations';\" value=\"Zur&uuml;ck\" />";
					echo "</form>";
				}
			}

			//
			// Beziehungsübersicht anzeigen
			//
			else
			{
				// Save pact offer
				if (isset($_POST['sbmit_new_bnd']) && checker_verify())
				{
					$bnd_res = dbquery("
					SELECT 
						alliance_bnd_id 
					FROM 
						".$db_table['alliance_bnd']." 
					WHERE 
						(
							(alliance_bnd_alliance_id1='".$cu->allianceId()."' 
							AND alliance_bnd_alliance_id2='".$_POST['alliance_bnd_alliance_id']."') 
						OR 
							(alliance_bnd_alliance_id2='".$cu->allianceId()."' 
							AND alliance_bnd_alliance_id1='".$_POST['alliance_bnd_alliance_id']."')
						) 
						AND alliance_bnd_level>0");
					
					
					if (mysql_num_rows($bnd_res)>0)
					{
						echo "Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!<br/><br/>";
					}
					else
					{
						dbquery("
						INSERT INTO 
							".$db_table['alliance_bnd']." 
						(
							alliance_bnd_alliance_id1,
							alliance_bnd_alliance_id2,
							alliance_bnd_level,
							alliance_bnd_text,
							alliance_bnd_name,
							alliance_bnd_date,
							alliance_bnd_diplomat_id
						) 
						VALUES 
						(
							'".$cu->allianceId()."',
							'".$_POST['alliance_bnd_alliance_id']."',
							'0',
							'".addslashes($_POST['alliance_bnd_text'])."',
							'".addslashes($_POST['alliance_bnd_name'])."',
							".time().",
							'".$cu->id()."'
						);");
						echo "Du hast einer Allianz erfolgreich ein B&uuml;ndnis angeboten!<br/><br/>";

						//Nachricht an den Leader der gegnerischen Allianz schreiben
						$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_POST['alliance_bnd_alliance_id']."'");
						$arr=mysql_fetch_array($res);

						send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Bündnisanfrage","Die Allianz [b][".$alliances[$cu->allianceId()]['tag']."] ".$alliances[$cu->allianceId()]['name']."[/b] fragt euch für ein Bündnis an.\n
						[b]Text:[/b] ".addslashes($_POST['alliance_bnd_text'])."\n 
						Geschrieben von [b]".$cu->nick()."[/b].\n Gehe auf die [page=alliance]Allianzseite[/page] um die Anfrage zu bearbeiten!");
					}
				}
				
				// Save war
				if (isset($_POST['sbmit_new_war']) && checker_verify())
				{
					$alliances = get_alliance_names();
					
					$war_res = dbquery("
					SELECT 
						alliance_bnd_id 
					FROM 
						".$db_table['alliance_bnd']." 
					WHERE 
						(
							(alliance_bnd_alliance_id1='".$cu->allianceId()."' 
							AND alliance_bnd_alliance_id2='".$_POST['alliance_bnd_alliance_id']."') 
							OR 
							(alliance_bnd_alliance_id2='".$cu->allianceId()."' 
							AND alliance_bnd_alliance_id1='".$_POST['alliance_bnd_alliance_id']."')
						) 
						AND alliance_bnd_level>0");
					
					if (mysql_num_rows($war_res)>0)
					{
						echo "Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!<br/><br/>";
					}
					else
					{
						dbquery("
						INSERT INTO 
						".$db_table['alliance_bnd']."
						(
							alliance_bnd_alliance_id1,
							alliance_bnd_alliance_id2,
							alliance_bnd_level,
							alliance_bnd_text,
							alliance_bnd_text_pub,
							alliance_bnd_date,
							alliance_bnd_points,
							alliance_bnd_diplomat_id
						) 
						VALUES 
						(
							'".$cu->allianceId()."',
							'".$_POST['alliance_bnd_alliance_id']."',
							'3',
							'".addslashes($_POST['alliance_bnd_text'])."',
							'".addslashes($_POST['alliance_bnd_text_pub'])."',
							'".time()."',
							".DIPLOMACY_POINTS_PER_WAR.",
							'".$cu->id()."'
						)");
						
						echo "Du hast einer Allianz den Krieg erkl&auml;rt!<br/><br/>";
						
						add_alliance_history($cu->allianceId(),"Der Allianz [b][".$alliances[$_POST['alliance_bnd_alliance_id']]['tag']."] ".$alliances[$_POST['alliance_bnd_alliance_id']]['name']."[/b] wird der Krieg erkl&auml;rt!");
						add_alliance_history($_POST['alliance_bnd_alliance_id'],"Die Allianz [b][".$alliances[$cu->allianceId()]['tag']."] ".$alliances[$cu->allianceId()]['name']."[/b] erkl&auml;rt den Krieg!");

						//Nachricht an den Leader der gegnerischen Allianz schreiben
						$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_POST['alliance_bnd_alliance_id']."'");
						$arr=mysql_fetch_array($res);

						send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Kriegserklärung","Die Allianz [b][".$alliances[$cu->allianceId()]['tag']."] ".$alliances[$cu->allianceId()]['name']."[/b] erklärt euch den Krieg!\n
						Die Kriegserklärung wurde von [b]".$cu->nick()."[/b] geschrieben.\n Geh auf die Allianzseite für mehr Details!");
					}
				}

				// End pact
				if (isset($_POST['submit_pact_end']))
				{
					$res=dbquery("
					SELECT
						a1.alliance_id as a1id,
						a2.alliance_id as a2id,
						a1.alliance_name as a1name,
						a2.alliance_name as a2name,
						a1.alliance_tag as a1tag,
						a2.alliance_tag as a2tag,
						alliance_bnd_name,
						alliance_bnd_level
					FROM 
						".$db_table['alliance_bnd']." 
					INNER JOIN
						alliances as a1
						ON a1.alliance_id=alliance_bnd_alliance_id1
					INNER JOIN
						alliances as a2
						ON a2.alliance_id=alliance_bnd_alliance_id2			
					WHERE 
					(
						alliance_bnd_alliance_id1=".$cu->allianceId()." 
						OR alliance_bnd_alliance_id2=".$cu->allianceId()." 
					) 
					AND alliance_bnd_id=".$_POST['id']."
					AND alliance_bnd_level='2';");
					
					if (mysql_num_rows($res)==1)
					{
						$arr = mysql_fetch_array($res);
						if ($arr['a1id']==$cu->allianceId())
						{
							$opId = $arr['a2id'];
							$opName = $arr['a2name'];
							$opTag = $arr['a2tag'];
							$selfId = $arr['a1id'];
							$selfName = $arr['a1name'];							
							$selfTag = $arr['a1tag'];							
						}
						else
						{
							$opId = $arr['a1id'];
							$opName = $arr['a1name'];
							$opTag = $arr['a1tag'];
							$selfId = $arr['a2id'];
							$selfName = $arr['a2name'];							
							$selfTag = $arr['a2tag'];							
						}
						
						//Delete Bnd Forum
						$bres=dbquery("SELECT * FROM allianceboard_topics WHERE topic_bnd_id=".$_POST['id'].";");
						while ($barr=mysql_fetch_array($bres))
						{
							dbquery("DELETE FROM allianceboard_posts WHERE post_topic_id=".$barr['topic_id'].";");
						}
						dbquery("DELETE FROM allianceboard_topics WHERE topic_bnd_id=".$_POST['id'].";");
						
						// Delete entity
						dbquery("
						DELETE FROM 
							".$db_table['alliance_bnd']." 
						WHERE 
							alliance_bnd_id=".$_POST['id']."
						;");

						// Add log							
						add_alliance_history($selfId,"Das Bündnis [b]".$arr['alliance_bnd_name']."[/b] mit der Allianz [b][".$opTag."] ".$opName."[/b] wird aufgelöst!");
						add_alliance_history($opId,"Die Allianz [b][".$selfTag."] ".$selfName."[/b] löst das Bündnis [b]".$arr['alliance_bnd_name']."[/b] auf!");

						// Send message to leader
						$fres=dbquery("
						SELECT 
							alliance_founder_id 
						FROM 
							".$db_table['alliances']." 
						WHERE 
							alliance_id='".$opId."'
						;");
						$farr=mysql_fetch_array($fres);
						send_msg($farr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Bündnis ".$arr['alliance_bnd_name']." beendet","Die Allianz [b][".$selfTag."] ".$selfName."[/b] beendet ihr Bündnis [b]".$arr['alliance_bnd_name']."[/b] mit eurer Allianz!\n
						Ausgelöst von [b]".$cu->nick()."[/b].\nBegründung: ".$_POST['pact_end_text']);
							
						echo "Das B&uuml;ndnis <b>".$arr['alliance_bnd_name']."</b> mit der Allianz <b>".$opName."</b> wurde aufgel&ouml;st!<br/><br/>";
					}
				}

				// Withdraw pact offer
				if(isset($_POST['submit_withdraw_pact']))
				{
					$id=$_POST['id'];
					$res=dbquery("
						SELECT 
							alliance_bnd_id,
							alliance_bnd_alliance_id2
						FROM 
							alliance_bnd
						WHERE
							alliance_bnd_alliance_id1=".$cu->allianceId()."
							AND alliance_bnd_id='".$id."' 
						;");
					$arr = mysql_fetch_array($res);
					if(mysql_num_rows($res)>0)
					{
						// Remove request
						dbquery("
						DELETE FROM 
							".$db_table['alliance_bnd']." 
						WHERE 
							alliance_bnd_id='".$arr['alliance_bnd_id']."'
						;");

						// Inform opposite leader
						$res=dbquery("SELECT alliance_founder_id,alliance_name FROM ".$db_table['alliances']." WHERE alliance_id='".$arr['alliance_bnd_alliance_id2']."'");
	          $arr=mysql_fetch_array($res);
	   				send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Anfrage zurückgenommen","Die Allianz [b][".$alliances[$cu->allianceId()]['tag']."] ".$alliances[$cu->allianceId()]['name']."[/b] hat ihre Büdnisanfrage wieder zurückgezogen.");

						// Display message
						echo "Anfrage gel&ouml;scht! Die Allianzleitung der Allianz <b>".$arr['alliance_name']."</b> wurde per Nachricht dar&uuml;ber informiert.<br/><br/>";
					}			
				}

				// Accept pact offer
				if (isset($_POST['pact_accept']))
				{
					$id=$_POST['id'];
					$res=dbquery("
						SELECT 
							alliance_bnd_id,
							alliance_bnd_name,
							a1.alliance_id as a1id,
							a2.alliance_id as a2id,
							a1.alliance_name as a1name,
							a2.alliance_name as a2name,	
							a1.alliance_tag as a1tag,
							a2.alliance_tag as a2tag,	
							a1.alliance_founder_id as a1founder							
						FROM 
							alliance_bnd
						INNER JOIN
							alliances as a1
							ON alliance_bnd_alliance_id1=a1.alliance_id
						INNER JOIN
							alliances as a2
							ON alliance_bnd_alliance_id2=a2.alliance_id
						WHERE
							alliance_bnd_alliance_id2=".$cu->allianceId()."
							AND alliance_bnd_id='".$id."' 
							AND alliance_bnd_level=0
						;");
					if(mysql_num_rows($res)>0)
					{
						$arr = mysql_fetch_array($res);

						// Send message to alliance leader
						$text = "Das Bündnis [b]".$arr['alliance_bnd_name']."[/b] zwischen den Allianzen [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] und [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] ist zustande gekommen!\n\nBitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!\n[b]Nachricht:[/b] ".$_POST['pact_answer'];
						send_msg($arr['a1founder'],MSG_ALLYMAIL_CAT,"Bündnis angenommen",$text);

						// Log decision
						$text = "Die Allianzen [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] und [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] schliessen ein Bündnis!";
						add_alliance_history($cu->allianceId(),$text);
						add_alliance_history($alliance_id,$text);
						
						// Save pact
						dbquery("
						UPDATE 
							".$db_table['alliance_bnd']." 
						SET 
							alliance_bnd_level='2',
							alliance_bnd_points=".DIPLOMACY_POINTS_PER_PACT." 
						WHERE 
							alliance_bnd_id=".$id." 
						;");
						echo "Bündniss angenommen! Bitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!<br/><br/>";
					}
				}

				// Reject pact offer
				if (isset($_POST['pact_reject']))
				{
					$id=$_POST['id'];
					$res=dbquery("
						SELECT 
							alliance_bnd_id,
							alliance_bnd_name,
							a1.alliance_id as a1id,
							a2.alliance_id as a2id,
							a1.alliance_name as a1name,
							a2.alliance_name as a2name,	
							a1.alliance_tag as a1tag,
							a2.alliance_tag as a2tag,	
							a1.alliance_founder_id as a1founder							
						FROM 
							alliance_bnd
						INNER JOIN
							alliances as a1
							ON alliance_bnd_alliance_id1=a1.alliance_id
						INNER JOIN
							alliances as a2
							ON alliance_bnd_alliance_id2=a2.alliance_id
						WHERE
							alliance_bnd_alliance_id2=".$cu->allianceId()."
							AND alliance_bnd_id='".$id."' 
							AND alliance_bnd_level=0
						;");
					if(mysql_num_rows($res)>0)
					{
						$arr = mysql_fetch_array($res);


						// Nachricht an den Leader der anfragenden Allianz
						$text = "Die Bündnisanfrage [b]".$arr['alliance_bnd_name']."[/b] wurde von der Allianz [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] abgelehnt!\n\n[b]Nachricht:[/b] ".$_POST['pact_answer'];
						send_msg($arr['a1founder'],MSG_ALLYMAIL_CAT,"Bündnisantrag abgelehnt",$text);
						
						// Löscht BND
						dbquery("
						DELETE FROM 
							".$db_table['alliance_bnd']." 
						WHERE 
							alliance_bnd_id=".$id." 
						");
																						
						// Logt die Absage
						add_alliance_history($cu->allianceId(),"Die Bündnisanfrage [b]".$arr['alliance_bnd_name']."[/b] der Allianz [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] wird abgelehnt!");
						add_alliance_history($arr['a2id'],"Die Bündnisanfrage [b]".$arr['alliance_bnd_name']."[/b] wird von der Allianz [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] abgelehnt!");

						echo "Bündniss abgelehnt!<br/><br/>";
					}
				}

				// Save public pact text
				if (isset($_POST['submit_pact_public_text']))
				{
					$id=$_POST['id'];
					dbquery("
					UPDATE
						alliance_bnd
					SET
						alliance_bnd_text_pub='".addslashes($_POST['alliance_bnd_text_pub'])."'
					WHERE
						(
							alliance_bnd_alliance_id1=".$cu->allianceId()."
							OR alliance_bnd_alliance_id2=".$cu->allianceId()."
						)
						AND alliance_bnd_id='".$id."' 
						AND alliance_bnd_level=2
					");
					echo "Text gespeichert!<br/><br/>";
				}

				// Save public war text
				if (isset($_POST['submit_war_public_text']))
				{
					$id=$_POST['id'];
					dbquery("
					UPDATE
						alliance_bnd
					SET
						alliance_bnd_text_pub='".addslashes($_POST['alliance_bnd_text_pub'])."'
					WHERE
						(
							alliance_bnd_alliance_id1=".$cu->allianceId()."
							OR alliance_bnd_alliance_id2=".$cu->allianceId()."
						)
						AND alliance_bnd_id='".$id."' 
						AND alliance_bnd_level=3
					");
					echo "Text gespeichert!<br/><br/>";
				}


				// Beziehungen laden
				$bres=dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['alliance_bnd']." 
				WHERE 
					alliance_bnd_alliance_id1='".$cu->allianceId()."' 
					OR alliance_bnd_alliance_id2='".$cu->allianceId()."'
				;");
				$relations=array();
				if (mysql_num_rows($bres)>0)
				{
					while($barr=mysql_fetch_array($bres))
					{
						if ($barr['alliance_bnd_alliance_id1']==$cu->allianceId())
						{
							$relations[$barr['alliance_bnd_alliance_id2']]['master']=true;
							$relations[$barr['alliance_bnd_alliance_id2']]['id']=$barr['alliance_bnd_id'];
							$relations[$barr['alliance_bnd_alliance_id2']]['name']=$barr['alliance_bnd_name'];
							$relations[$barr['alliance_bnd_alliance_id2']]['level']=$barr['alliance_bnd_level'];
							$relations[$barr['alliance_bnd_alliance_id2']]['date']=$barr['alliance_bnd_date'];
							$relations[$barr['alliance_bnd_alliance_id2']]['text']=$barr['alliance_bnd_text'];
						}
						else
						{
							$relations[$barr['alliance_bnd_alliance_id2']]['master']=false;
							$relations[$barr['alliance_bnd_alliance_id1']]['id']=$barr['alliance_bnd_id'];
							$relations[$barr['alliance_bnd_alliance_id1']]['name']=$barr['alliance_bnd_name'];
							$relations[$barr['alliance_bnd_alliance_id1']]['level']=$barr['alliance_bnd_level'];
							$relations[$barr['alliance_bnd_alliance_id1']]['date']=$barr['alliance_bnd_date'];
							$relations[$barr['alliance_bnd_alliance_id1']]['text']=$barr['alliance_bnd_text'];
						}
					}
				}

				// Allianzen laden
				$ares=dbquery("
				SELECT
					*
				FROM
					".$db_table['alliances']."
				WHERE
					alliance_id!='".$cu->allianceId()."'
				ORDER BY
					alliance_name,
					alliance_tag;");
				if (mysql_num_rows($ares)>0)
				{
					tableStart("&Uuml;bersicht");
					echo "<tr><td class=\"tbltitle\" colspan=\"2\">Allianz</td>
					<td class=\"tbltitle\">Status</td>
					<td class=\"tbltitle\">Start</td>
					<td class=\"tbltitle\">Ende / Name</td>
					<td class=\"tbltitle\">Aktionen</td>
					</tr>";
					while ($aarr=mysql_fetch_array($ares))
					{
						echo "<tr>
							<td class=\"tbldata\">
								<a href=\"?page=alliance&amp;info_id=".$aarr['alliance_id']."\">
								[".$aarr['alliance_tag']."]
								</a>
							</td>
							<td class=\"tbldata\">
							 ".text2html($aarr['alliance_name'])."
							</td>";
							
						if(isset($relations[$aarr['alliance_id']]))
						{	
							if ($relations[$aarr['alliance_id']]['level']==2)
							{
								echo "<td class=\"tbldata\" style=\"color:#0f0;\">B&uuml;ndnis</td>";
								echo "<td class=\"tbldata\">".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td class=\"tbldata\">".$relations[$aarr['alliance_id']]['name']."</td>";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==3)
							{
								echo "<td class=\"tbldata\" style=\"color:#f00;\">Krieg</td>";
								echo "<td class=\"tbldata\">".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td class=\"tbldata\">".df($relations[$aarr['alliance_id']]['date']+WAR_DURATION)."</td>";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==4)
							{
								echo "<td class=\"tbldata\" style=\"color:#3f9;\">Frieden</td>";
								echo "<td class=\"tbldata\">".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td class=\"tbldata\">".df($relations[$aarr['alliance_id']]['date']+PEACE_DURATION)."</td>";
							}									
							elseif ($relations[$aarr['alliance_id']]['level']==0 && count($relations[$aarr['alliance_id']])>0)
							{
								if ($relations[$aarr['alliance_id']]['master'])
								{
									echo "<td class=\"tbldata\" style=\"color:#ff0;\">Anfrage</td>";
								}
								else
								{
									echo "<td class=\"tbldata\" style=\"color:#f90;\">Anfrage an uns</td>";
								}
								echo "<td class=\"tbldata\">".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td class=\"tbldata\">-</td>";
							}
							else
							{
								echo "<td class=\"tbldata\">-</td>";
								echo "<td class=\"tbldata\">-</td>";
								echo "<td class=\"tbldata\">-</td>";
							}
						}
						else
						{
							echo "<td class=\"tbldata\">-</td>";
							echo "<td class=\"tbldata\">-</td>";
							echo "<td class=\"tbldata\">-</td>";
						}
												
						echo "<td class=\"tbldata\">";
						
						if(isset($relations[$aarr['alliance_id']]))
						{
							if ($relations[$aarr['alliance_id']]['level']==2)
							{
								echo "<a href=\"?page=$page&action=relations&amp;view=".$relations[$aarr['alliance_id']]['id']."\">Details</a> &nbsp; ";
								echo "<a href=\"?page=$page&action=relations&amp;end_pact=".$relations[$aarr['alliance_id']]['id']."\">Auflösen</a> ";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==3)
							{
								echo "<a href=\"?page=$page&action=relations&view=".$relations[$aarr['alliance_id']]['id']."\">Kriegserklärung</a> ";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==4)
							{
								echo "-";
							}									
							elseif ($relations[$aarr['alliance_id']]['level']==0 && count($relations[$aarr['alliance_id']])>0)
							{
								if ($relations[$aarr['alliance_id']]['master'])
								{
									echo "<a href=\"?page=$page&action=relations&view=".$relations[$aarr['alliance_id']]['id']."\">Anschauen / Löschen</a> ";
								}
								else
								{
									echo "<a href=\"?page=$page&action=relations&view=".$relations[$aarr['alliance_id']]['id']."\">Beantworten</a> ";
								}
							}
							else
							{
								if($aarr['alliance_accept_bnd']==1)
								{
									echo "<a href=\"?page=$page&action=relations&amp;begin_bnd=".$aarr['alliance_id']."\">B&uuml;ndnis</a> &nbsp; ";
								}
								echo "<a href=\"?page=$page&action=relations&amp;begin_war=".$aarr['alliance_id']."\">Krieg</a> ";
							}
						}
						else
						{
							if($aarr['alliance_accept_bnd']==1)
								{
									echo "<a href=\"?page=$page&action=relations&amp;begin_bnd=".$aarr['alliance_id']."\">B&uuml;ndnis</a> &nbsp; ";
								}
								echo "<a href=\"?page=$page&action=relations&amp;begin_war=".$aarr['alliance_id']."\">Krieg</a> ";
						}
						echo "</td></tr>";
					}
					tableEnd();
				}
				else
					echo "Es gibt noch keine Allianzen, welcher du den Krieg erkl&auml;ren kannst.<br/><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zur Hauptseite\" onclick=\"document.location='?page=$page'\" />";

			}

	}

?>
