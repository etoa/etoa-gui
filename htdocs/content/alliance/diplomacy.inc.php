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

	if (Alliance::checkActionRights('relations'))
	{
		echo "<h2>Diplomatie</h2>";


        /** @var \EtoA\Alliance\AllianceRepository $allianceRepository */
        $allianceRepository = $app['etoa.alliance.repository'];
        $allianceNamesWithTags = $allianceRepository->getAllianceNamesWithTags();

			//
			// Kriegserklärung schreiben
			//
			if (isset($_GET['begin_war']) && intval($_GET['begin_war'])>0)
			{
				$aid = intval($_GET['begin_war']);

				$check = false;
				if(!isset($_GET['begin_bnd']) || $_GET['begin_bnd']!=$cu->allianceId)
				{
					$check = true;
				}

				$otherAlliance = $allianceRepository->getAlliance($aid);
				if ($otherAlliance !== null && $check)
				{
					echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"wardeclaration\">";
					checker_init();

					tableStart("Kriegserkl&auml;rung an die Allianz " . $otherAlliance->nameWithTag);
					echo "<tr><th>Nachricht:</th><td><textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea></td></tr>";
					echo "<tr><th>Öffentlicher Text:</th><td><textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text_pub\"></textarea></td></tr>";
					tableEnd();

					echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"".$otherAlliance->id."\" />";
					echo "<input type=\"submit\" name=\"sbmit_new_war\" value=\"Senden\" onclick=\"return checkWarDeclaration()\" onsubmit=\"return checkWarDeclaration()\" />&nbsp;
					<input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
					echo "</form>";
				}
				else
				{
					error_msg("Diese Allianz existiert nicht!");
				}
			}

			//
			// Bündnisanfrage schreiben
			//
			elseif (isset($_GET['begin_bnd']) && intval($_GET['begin_bnd'])>0)
			{
				$aid = intval($_GET['begin_bnd']);

				$otherAlliance = $allianceRepository->getAlliance($aid);
				if ($otherAlliance !== null && $otherAlliance->id != $cu->allianceId)
				{

					if($otherAlliance->acceptBnd)
					{
						echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"pactoffer\">";
						checker_init();

						tableStart("B&uuml;ndnisanfrage an die Allianz ".$otherAlliance->nameWithTag);
						echo "<tr>
							<th>Name des Bündnisses:</th>
							<td>
								<input type=\"text\" size=\"30\" maxlength=\"30\" name=\"alliance_bnd_name\" />
							</td>
						</tr>";
						echo "<tr>
							<th>Bündnisanfrage:</th>
							<td>
								<textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea>
							</td>
						</tr>";
						tableEnd();

						echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"".$otherAlliance->id."\" />";
						echo "<input type=\"submit\" name=\"sbmit_new_bnd\" value=\"Senden\" onclick=\"return checkPactOffer()\" onsubmit=\"return checkPactOffer()\" />&nbsp;
						<input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
						echo "</form>";
					}
					else
					{
						error_msg("Die Allianz nimmt keine Bündnisanfragen an!",1);
					}
				}
				else
				{
					error_msg("Diese Allianz existiert nicht!");
				}
			}

			//
			// Büdniss/Kriegs- Text ansehen
			//
			elseif (isset($_GET['view']) && intval($_GET['view'])>0)
			{
				$id = intval($_GET['view']);

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
					alliance_bnd
				INNER JOIN
					alliances as a1
					ON a1.alliance_id=alliance_bnd_alliance_id1
				INNER JOIN
					alliances as a2
					ON a2.alliance_id=alliance_bnd_alliance_id2
				WHERE
					(alliance_bnd_alliance_id1='".$cu->allianceId."'
					OR alliance_bnd_alliance_id2='".$cu->allianceId."')
					AND alliance_bnd_id='".$id."'
				;");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);
					if ($arr['a1id']==$cu->allianceId)
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
								<th style=\"width:200px;\">Allianz</th>
								<td>".$opName."</td>
							</tr>";
							echo "<tr>
								<th style=\"width:200px;\">Bündnissname</th>
								<td>".text2html($arr['alliance_bnd_name'])."</td>
							</tr>";
							echo "<tr>
								<th style=\"width:200px;\">Text</th>
								<td>".text2html($arr['alliance_bnd_text'])."</td>
							</tr>";
							if ($arr['a1id']==$cu->allianceId)
							{
								echo "<tr>
									<th style=\"width:200px;\">Status</th>
									<td>Die Anfrage wurde noch nicht angenommen.</td>
								</tr>";
							}
							else
							{
								echo "<tr>
									<th style=\"width:200px;\">Antwort</th>
									<td><textarea name=\"pact_answer\" rows=\"6\" cols=\"70\"></textarea></td>
								</tr>";
							}
							tableEnd();
							echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";
							if ($arr['a1id']==$cu->allianceId)
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
								<th style=\"width:200px;\">Allianz</th>
								<td>".$opName."</td>
							</tr>";
							echo "<tr>
								<th style=\"width:200px;\">Anfragetext</th>
								<td>".text2html($arr['alliance_bnd_text'])."</td>
							</tr>";
							echo "<tr>
								<th style=\"width:200px;\">Öffentlicher Text</th>
								<td><textarea name=\"alliance_bnd_text_pub\" rows=\"6\" cols=\"70\">".StringUtils::encodeDBStringForTextarea($arr['alliance_bnd_text_pub'])."</textarea></td>
							</tr>";
							tableEnd();
							echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";
							echo "<input type=\"submit\" name=\"submit_pact_public_text\" value=\"Speichern\" /> &nbsp; ";
							break;
						case 3:
							tableStart("Krieg");
							echo "<tr>
								<th style=\"width:200px;\">Allianz</th>
								<td>".$opName."</td>
							</tr>";
							echo "<tr>
								<th style=\"width:200px;\">Kriegserklärung</th>
								<td>".text2html($arr['alliance_bnd_text'])."</td>
							</tr>";
							if ($arr['a1id']==$cu->allianceId)
							{
								echo "<tr>
									<th style=\"width:200px;\">Öffentlicher Text</th>
									<td><textarea name=\"alliance_bnd_text_pub\" rows=\"6\" cols=\"70\">".StringUtils::encodeDBStringForTextarea($arr['alliance_bnd_text_pub'])."</textarea></td>
								</tr>";
							}
							else
							{
								echo "<tr>
									<th style=\"width:200px;\">Öffentlicher Text</th>
									<td>".text2html($arr['alliance_bnd_text_pub'])."</td>
								</tr>";
							}
							tableEnd();
							if ($arr['a1id']==$cu->allianceId)
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
					error_msg("Datensatz nicht vorhanden!");
				}
			}

			//
			// End pact
			//
			elseif (isset($_GET['end_pact']) && intval($_GET['end_pact'])>0)
			{
				$id = intval($_GET['end_pact']);

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
					alliance_bnd
				INNER JOIN
					alliances as a1
					ON a1.alliance_id=alliance_bnd_alliance_id1
				INNER JOIN
					alliances as a2
					ON a2.alliance_id=alliance_bnd_alliance_id2
				WHERE
					(alliance_bnd_alliance_id1='".$cu->allianceId."'
					OR alliance_bnd_alliance_id2='".$cu->allianceId."')
					AND alliance_bnd_id='".$id."'
					AND alliance_bnd_level=2
				;");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);
					if ($arr['a1id']==$cu->allianceId)
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
						<th style=\"width:200px;\">Allianz</th>
						<td>".$opName."</td>
					</tr>";
					echo "<tr>
						<th style=\"width:200px;\">Begründung</th>
						<td><textarea name=\"pact_end_text\" rows=\"6\" cols=\"70\"></textarea></td>
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
				if (isset($_POST['sbmit_new_bnd']) && isset($_POST['alliance_bnd_alliance_id']) && checker_verify())
				{
					$id = intval($_POST['alliance_bnd_alliance_id']);

					$bnd_res = dbquery("
					SELECT
						alliance_bnd_id
					FROM
						alliance_bnd
					WHERE
						(
							(alliance_bnd_alliance_id1='".$cu->allianceId."'
							AND alliance_bnd_alliance_id2='".$id."')
						OR
							(alliance_bnd_alliance_id2='".$cu->allianceId."'
							AND alliance_bnd_alliance_id1='".$id."')
						)
						AND alliance_bnd_level>0");


					if (mysql_num_rows($bnd_res)>0)
					{
						error_msg("Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!");
					}
					else
					{
						dbquery("
						INSERT INTO
							alliance_bnd
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
							'".$cu->allianceId."',
							'".$id."',
							'0',
							'".mysql_real_escape_string($_POST['alliance_bnd_text'])."',
							'".mysql_real_escape_string($_POST['alliance_bnd_name'])."',
							".time().",
							'".$cu->id."'
						);");
						success_msg("Du hast einer Allianz erfolgreich ein B&uuml;ndnis angeboten!");

						//Nachricht an den Leader der gegnerischen Allianz schreiben
						$res=dbquery("SELECT alliance_founder_id FROM alliances WHERE alliance_id='".$id."'");
						$arr=mysql_fetch_array($res);

						send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Bündnisanfrage","Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] fragt euch für ein Bündnis an.\n
						[b]Text:[/b] ".addslashes($_POST['alliance_bnd_text'])."\n
						Geschrieben von [b]".$cu->nick."[/b].\n Gehe auf die [page=alliance]Allianzseite[/page] um die Anfrage zu bearbeiten!");
					}
				}

				// Save war
				if (isset($_POST['sbmit_new_war']) && intval($_POST['alliance_bnd_alliance_id']) > 0 && checker_verify())
				{
					$id = intval($_POST['alliance_bnd_alliance_id']);

					$war_res = dbquery("
					SELECT
						alliance_bnd_id
					FROM
						alliance_bnd
					WHERE
						(
							(alliance_bnd_alliance_id1='".$cu->allianceId."'
							AND alliance_bnd_alliance_id2='".$id."')
							OR
							(alliance_bnd_alliance_id2='".$cu->allianceId."'
							AND alliance_bnd_alliance_id1='".$id."')
						)
						AND alliance_bnd_level>0");

					if (mysql_num_rows($war_res)>0)
					{
						error_msg("Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!");
					}
					else
					{
						dbquery("
						INSERT INTO
						alliance_bnd
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
							'".$cu->allianceId."',
							'".$id."',
							'3',
							'".mysql_real_escape_string($_POST['alliance_bnd_text'])."',
							'".mysql_real_escape_string($_POST['alliance_bnd_text_pub'])."',
							'".time()."',
							".DIPLOMACY_POINTS_PER_WAR.",
							'".$cu->id."'
						)");

						success_msg("Du hast einer Allianz den Krieg erkl&auml;rt!");

						add_alliance_history($cu->allianceId,"Der Allianz [b]" . $allianceNamesWithTags[$id] ."[/b] wird der Krieg erkl&auml;rt!");
						add_alliance_history($id,"Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] erkl&auml;rt den Krieg!");

						//Nachricht an den Leader der gegnerischen Allianz schreiben
						$res=dbquery("SELECT alliance_founder_id FROM alliances WHERE alliance_id='".$id."';");
						$arr=mysql_fetch_array($res);

						send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Kriegserklärung","Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] erklärt euch den Krieg!\n
						Die Kriegserklärung wurde von [b]".$cu->nick."[/b] geschrieben.\n Geh auf die Allianzseite für mehr Details!");
					}
				}

				// End pact
				if (isset($_POST['submit_pact_end']) && isset($_POST['id']) && intval($_POST['id']) > 0)
				{
					$id = intval($_POST['id']);

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
						alliance_bnd
					INNER JOIN
						alliances as a1
						ON a1.alliance_id=alliance_bnd_alliance_id1
					INNER JOIN
						alliances as a2
						ON a2.alliance_id=alliance_bnd_alliance_id2
					WHERE
					(
						alliance_bnd_alliance_id1=".$cu->allianceId."
						OR alliance_bnd_alliance_id2=".$cu->allianceId."
					)
					AND alliance_bnd_id=".$id."
					AND alliance_bnd_level='2';");

					if (mysql_num_rows($res)==1)
					{
						$arr = mysql_fetch_array($res);
						if ($arr['a1id']==$cu->allianceId)
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
						$bres=dbquery("SELECT * FROM allianceboard_topics WHERE topic_bnd_id=".$id.";");
						while ($barr=mysql_fetch_array($bres))
						{
							dbquery("DELETE FROM allianceboard_posts WHERE post_topic_id=".$barr['topic_id'].";");
						}
						dbquery("DELETE FROM allianceboard_topics WHERE topic_bnd_id=".$id.";");

						// Delete entity
						dbquery("
						DELETE FROM
							alliance_bnd
						WHERE
							alliance_bnd_id=".$id."
						;");

						// Add log
						add_alliance_history($selfId,"Das Bündnis [b]".$arr['alliance_bnd_name']."[/b] mit der Allianz [b][".$opTag."] ".$opName."[/b] wird aufgelöst!");
						add_alliance_history($opId,"Die Allianz [b][".$selfTag."] ".$selfName."[/b] löst das Bündnis [b]".$arr['alliance_bnd_name']."[/b] auf!");

						// Send message to leader
						$fres=dbquery("
						SELECT
							alliance_founder_id
						FROM
							alliances
						WHERE
							alliance_id='".$opId."'
						;");
						$farr=mysql_fetch_array($fres);
						send_msg($farr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Bündnis ".$arr['alliance_bnd_name']." beendet","Die Allianz [b][".$selfTag."] ".$selfName."[/b] beendet ihr Bündnis [b]".$arr['alliance_bnd_name']."[/b] mit eurer Allianz!\n
						Ausgelöst von [b]".$cu->nick."[/b].\nBegründung: ".$_POST['pact_end_text']);

						echo "Das B&uuml;ndnis <b>".$arr['alliance_bnd_name']."</b> mit der Allianz <b>".$opName."</b> wurde aufgel&ouml;st!<br/><br/>";
					}
				}

				// Withdraw pact offer
				if(isset($_POST['submit_withdraw_pact']) && isset($_POST['id']) && intval($_POST['id']) > 0)
				{
					$id = intval($_POST['id']);

					$res=dbquery("
						SELECT
							alliance_bnd_id,
							alliance_bnd_alliance_id2
						FROM
							alliance_bnd
						WHERE
							alliance_bnd_alliance_id1=".$cu->allianceId."
							AND alliance_bnd_id='".$id."'
						;");
					$arr = mysql_fetch_array($res);
					if(mysql_num_rows($res)>0)
					{
						// Remove request
						dbquery("
						DELETE FROM
							alliance_bnd
						WHERE
							alliance_bnd_id='".$arr['alliance_bnd_id']."'
						;");

						// Inform opposite leader
                        $otherAlliance = $allianceRepository->getAlliance($arr['alliance_bnd_alliance_id2']);
	   				    send_msg($otherAlliance->founderId,MSG_ALLYMAIL_CAT,"Anfrage zurückgenommen","Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] hat ihre Büdnisanfrage wieder zurückgezogen.");

						// Display message
						echo "Anfrage gel&ouml;scht! Die Allianzleitung der Allianz <b>".$otherAlliance->name."</b> wurde per Nachricht dar&uuml;ber informiert.<br/><br/>";
					}
				}

				// Accept pact offer
				if (isset($_POST['pact_accept']) && isset($_POST['id']) && intval($_POST['id']) > 0)
				{
					$id = intval($_POST['id']);

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
							alliance_bnd_alliance_id2=".$cu->allianceId."
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
						add_alliance_history($cu->allianceId,$text);
						add_alliance_history($arr['a1id'], $text);

						// Save pact
						dbquery("
						UPDATE
							alliance_bnd
						SET
							alliance_bnd_level='2',
							alliance_bnd_points=".DIPLOMACY_POINTS_PER_PACT."
						WHERE
							alliance_bnd_id=".$id."
						;");
						success_msg("Bündniss angenommen! Bitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!");
					}
				}

				// Reject pact offer
				if (isset($_POST['pact_reject']) && isset($_POST['id']) && intval($_POST['id']) > 0)
				{
					$id = intval($_POST['id']);

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
							alliance_bnd_alliance_id2=".$cu->allianceId."
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
							alliance_bnd
						WHERE
							alliance_bnd_id=".$id."
						");

						// Logt die Absage
						add_alliance_history($cu->allianceId,"Die Bündnisanfrage [b]".$arr['alliance_bnd_name']."[/b] der Allianz [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] wird abgelehnt!");
						add_alliance_history($arr['a2id'],"Die Bündnisanfrage [b]".$arr['alliance_bnd_name']."[/b] wird von der Allianz [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] abgelehnt!");

						success_msg("Bündniss abgelehnt!");
					}
				}

				// Save public pact text
				if (isset($_POST['submit_pact_public_text']) && isset($_POST['id']) && intval($_POST['id']) > 0)
				{
					$id = intval($_POST['id']);

					dbquery("
					UPDATE
						alliance_bnd
					SET
						alliance_bnd_text_pub='".mysql_real_escape_string($_POST['alliance_bnd_text_pub'])."'
					WHERE
						(
							alliance_bnd_alliance_id1=".$cu->allianceId."
							OR alliance_bnd_alliance_id2=".$cu->allianceId."
						)
						AND alliance_bnd_id='".$id."'
						AND alliance_bnd_level=2
					");
					success_msg("Text gespeichert!");
				}

				// Save public war text
				if (isset($_POST['submit_war_public_text']) && isset($_POST['id']) && intval($_POST['id']) > 0)
				{
					$id = intval($_POST['id']);

					dbquery("
					UPDATE
						alliance_bnd
					SET
						alliance_bnd_text_pub='".mysql_real_escape_string($_POST['alliance_bnd_text_pub'])."'
					WHERE
						(
							alliance_bnd_alliance_id1=".$cu->allianceId."
							OR alliance_bnd_alliance_id2=".$cu->allianceId."
						)
						AND alliance_bnd_id='".$id."'
						AND alliance_bnd_level=3
					");
					success_msg("Text gespeichert!");
				}


				// Beziehungen laden
				$bres=dbquery("
				SELECT
					*
				FROM
					alliance_bnd
				WHERE
					alliance_bnd_alliance_id1='".$cu->allianceId."'
					OR alliance_bnd_alliance_id2='".$cu->allianceId."'
				;");
				$relations=array();
				if (mysql_num_rows($bres)>0)
				{
					while($barr=mysql_fetch_array($bres))
					{
						if ($barr['alliance_bnd_alliance_id1']==$cu->allianceId)
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
					alliances
				WHERE
					alliance_id!='".$cu->allianceId."'
				ORDER BY
					alliance_name,
					alliance_tag;");
				if (mysql_num_rows($ares)>0)
				{
					tableStart("&Uuml;bersicht");
					echo "<tr><th colspan=\"2\">Allianz</td>
					<th>Status</td>
					<th>Start</td>
					<th>Ende / Name</td>
					<th>Aktionen</td>
					</tr>";
					while ($aarr=mysql_fetch_array($ares))
					{
						echo "<tr>
							<td>
								<a href=\"?page=alliance&amp;info_id=".$aarr['alliance_id']."\">
								[".$aarr['alliance_tag']."]
								</a>
							</td>
							<td>
							 ".text2html($aarr['alliance_name'])."
							</td>";

						if(isset($relations[$aarr['alliance_id']]))
						{
							if ($relations[$aarr['alliance_id']]['level']==2)
							{
								echo "<td style=\"color:#0f0;\">B&uuml;ndnis</td>";
								echo "<td>".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td>".$relations[$aarr['alliance_id']]['name']."</td>";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==3)
							{
								echo "<td style=\"color:#f00;\">Krieg</td>";
								echo "<td>".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td>".df($relations[$aarr['alliance_id']]['date']+WAR_DURATION)."</td>";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==4)
							{
								echo "<td style=\"color:#3f9;\">Frieden</td>";
								echo "<td>".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td>".df($relations[$aarr['alliance_id']]['date']+PEACE_DURATION)."</td>";
							}
							elseif ($relations[$aarr['alliance_id']]['level']==0 && count($relations[$aarr['alliance_id']])>0)
							{
								if (isset($relations[$aarr['alliance_id']]['master']) && $relations[$aarr['alliance_id']]['master'])
								{
									echo "<td style=\"color:#ff0;\">Anfrage</td>";
								}
								else
								{
									echo "<td style=\"color:#f90;\">Anfrage an uns</td>";
								}
								echo "<td>".df($relations[$aarr['alliance_id']]['date'])."</td>";
								echo "<td>-</td>";
							}
							else
							{
								echo "<td>-</td>";
								echo "<td>-</td>";
								echo "<td>-</td>";
							}
						}
						else
						{
							echo "<td>-</td>";
							echo "<td>-</td>";
							echo "<td>-</td>";
						}

						echo "<td>";

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
								if (isset($relations[$aarr['alliance_id']]['master']) && $relations[$aarr['alliance_id']]['master'])
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
					error_msg("Es gibt noch keine Allianzen, welcher du den Krieg erkl&auml;ren kannst.");
				echo "<input type=\"button\" value=\"Zur&uuml;ck zur Hauptseite\" onclick=\"document.location='?page=$page'\" />";

			}
	}

?>
