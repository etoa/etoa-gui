<?PHP
if (Alliance::checkActionRights('polls'))
{


		echo "<h2>Umfragen verwalten</h2>";
		if (isset($_GET['pollaction']) && $_GET['pollaction']=="create")
		{
			if (isset($_POST['pollsubmitnew']) && $_POST['pollsubmitnew'] && checker_verify())
			{
				$_SESSION['alliance_poll']['poll_title']=$_POST['poll_title'];
				$_SESSION['alliance_poll']['poll_question']=$_POST['poll_question'];
				$_SESSION['alliance_poll']['poll_a1_text']=$_POST['poll_a1_text'];
				$_SESSION['alliance_poll']['poll_a2_text']=$_POST['poll_a2_text'];
				$_SESSION['alliance_poll']['poll_a3_text']=$_POST['poll_a3_text'];
				$_SESSION['alliance_poll']['poll_a4_text']=$_POST['poll_a4_text'];
				$_SESSION['alliance_poll']['poll_a5_text']=$_POST['poll_a5_text'];
				$_SESSION['alliance_poll']['poll_a6_text']=$_POST['poll_a6_text'];
				$_SESSION['alliance_poll']['poll_a7_text']=$_POST['poll_a7_text'];
				$_SESSION['alliance_poll']['poll_a8_text']=$_POST['poll_a8_text'];
				if ($_POST['poll_title']!="")
				{
					if ($_POST['poll_question']!="")
					{
						if ($_POST['poll_a1_text']!="" && $_POST['poll_a2_text']!="")
						{
							dbquery("INSERT INTO ".$db_table['alliance_polls']." (
								poll_alliance_id,
								poll_title,
								poll_question,
								poll_timestamp,
								poll_a1_text,
								poll_a2_text,
								poll_a3_text,
								poll_a4_text,
								poll_a5_text,
								poll_a6_text,
								poll_a7_text,
								poll_a8_text
							) VALUES (
								'".$arr['alliance_id']."',
								'".addslashes($_POST['poll_title'])."',
								'".addslashes($_POST['poll_question'])."',
								'".time()."',
								'".addslashes($_POST['poll_a1_text'])."',
								'".addslashes($_POST['poll_a2_text'])."',
								'".addslashes($_POST['poll_a3_text'])."',
								'".addslashes($_POST['poll_a4_text'])."',
								'".addslashes($_POST['poll_a5_text'])."',
								'".addslashes($_POST['poll_a6_text'])."',
								'".addslashes($_POST['poll_a7_text'])."',
								'".addslashes($_POST['poll_a8_text'])."'
							);");
							echo "Umfrage wurde gespeichert!";
							$_SESSION['alliance_poll']=null;
							$created=true;
						}
						else
							echo "<b>Fehler!</b> Mindestens die ersten zwei Antworten müssen definiert sein!";
					}
					else
						echo "<b>Fehler!</b> Frage fehlt!";
				}
				else
					echo "<b>Fehler!</b> Titel fehlt!";
				echo "<br/><br/>";
			}
			if (isset($created) && $created)
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Ok\" />";
			else
			{
				echo "Es müssen mindestens <b>zwei</b> Antwortfelder ausgefüllt sein!<br/><br/>";
				echo "<form action=\"?page=$page&amp;action=polls&amp;pollaction=create\" method=\"post\">";
				checker_init();
				infobox_start("Neue Umfrage erstellen",1);
				echo "<tr><th class=\"tbltitle\">Titel:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_title\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_title']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Frage:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_question\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_question']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 1:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a1_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a1_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 2:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a2_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a2_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 3:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a3_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a3_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 4:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a4_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a4_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 5:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a5_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a5_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 6:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a6_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a6_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 7:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a7_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a7_text']."\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">Antwort 8:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a8_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a8_text']."\" /></td></tr>";
				infobox_end(1);
				echo "<input type=\"submit\" name=\"pollsubmitnew\" value=\"Speichern\" /> &nbsp; ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Zur&uuml;ck\" /></form>";
			}
		}
		//
		// Umfrage bearbeiten
		//
		elseif (isset($_GET['edit']) && $_GET['edit']>0)
		{
			$pres=dbquery("SELECT * FROM ".$db_table['alliance_polls']." WHERE poll_id=".$_GET['edit']." AND poll_alliance_id=".$arr['alliance_id'].";");
			if (mysql_num_rows($pres)>0)
			{
				$parr=mysql_fetch_array($pres);
				$_SESSION['alliance_poll']['poll_title']=$parr['poll_title'];
				$_SESSION['alliance_poll']['poll_question']=$parr['poll_question'];
				$_SESSION['alliance_poll']['poll_a1_text']=$parr['poll_a1_text'];
				$_SESSION['alliance_poll']['poll_a2_text']=$parr['poll_a2_text'];
				$_SESSION['alliance_poll']['poll_a3_text']=$parr['poll_a3_text'];
				$_SESSION['alliance_poll']['poll_a4_text']=$parr['poll_a4_text'];
				$_SESSION['alliance_poll']['poll_a5_text']=$parr['poll_a5_text'];
				$_SESSION['alliance_poll']['poll_a6_text']=$parr['poll_a6_text'];
				$_SESSION['alliance_poll']['poll_a7_text']=$parr['poll_a7_text'];
				$_SESSION['alliance_poll']['poll_a8_text']=$parr['poll_a8_text'];

				if (isset($_POST['pollsubmit']) && $_POST['pollsubmit'] && checker_verify())
				{
					$_SESSION['alliance_poll']['poll_title']=$_POST['poll_title'];
					$_SESSION['alliance_poll']['poll_question']=$_POST['poll_question'];
					$_SESSION['alliance_poll']['poll_a1_text']=$_POST['poll_a1_text'];
					$_SESSION['alliance_poll']['poll_a2_text']=$_POST['poll_a2_text'];
					$_SESSION['alliance_poll']['poll_a3_text']=$_POST['poll_a3_text'];
					$_SESSION['alliance_poll']['poll_a4_text']=$_POST['poll_a4_text'];
					$_SESSION['alliance_poll']['poll_a5_text']=$_POST['poll_a5_text'];
					$_SESSION['alliance_poll']['poll_a6_text']=$_POST['poll_a6_text'];
					$_SESSION['alliance_poll']['poll_a7_text']=$_POST['poll_a7_text'];
					$_SESSION['alliance_poll']['poll_a8_text']=$_POST['poll_a8_text'];
					if ($_POST['poll_title']!="")
					{
						if ($_POST['poll_question']!="")
						{
							if ($_POST['poll_a1_text']!="" && $_POST['poll_a2_text']!="")
							{
								dbquery("UPDATE ".$db_table['alliance_polls']." SET
									poll_title='".addslashes($_POST['poll_title'])."',
									poll_question='".addslashes($_POST['poll_question'])."',
									poll_a1_text='".addslashes($_POST['poll_a1_text'])."',
									poll_a2_text='".addslashes($_POST['poll_a2_text'])."',
									poll_a3_text='".addslashes($_POST['poll_a3_text'])."',
									poll_a4_text='".addslashes($_POST['poll_a4_text'])."',
									poll_a5_text='".addslashes($_POST['poll_a5_text'])."',
									poll_a6_text='".addslashes($_POST['poll_a6_text'])."',
									poll_a7_text='".addslashes($_POST['poll_a7_text'])."',
									poll_a8_text='".addslashes($_POST['poll_a8_text'])."'
								WHERE
									poll_id=".$_GET['edit']."
									AND poll_alliance_id=".$arr['alliance_id'].";");
								echo "Umfrage wurde gespeichert!";
								$_SESSION['alliance_poll']=null;
								$updated=true;
							}
							else
								echo "<b>Fehler!</b> Mindestens die ersten zwei Antworten müssen definiert sein!";
						}
						else
							echo "<b>Fehler!</b> Frage fehlt!";
					}
					else
						echo "<b>Fehler!</b> Titel fehlt!";
					echo "<br/><br/>";
				}
				if ($updated)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Ok\" />";
				else
				{
					echo "<form action=\"?page=$page&amp;action=polls&amp;edit=".$parr['poll_id']."\" method=\"post\">";
					checker_init();
					infobox_start("Umfrage bearbeiten",1);
					echo "<tr><th class=\"tbltitle\">Titel:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_title\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_title']."\" /></td></tr>";
					echo "<tr><th class=\"tbltitle\">Frage:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_question\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_question']."\" /></td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 1:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a1_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a1_text']."\" /> ".$parr['poll_a1_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 2:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a2_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a2_text']."\" /> ".$parr['poll_a2_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 3:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a3_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a3_text']."\" /> ".$parr['poll_a3_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 4:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a4_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a4_text']."\" /> ".$parr['poll_a4_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 5:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a5_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a5_text']."\" /> ".$parr['poll_a5_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 6:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a6_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a6_text']."\" /> ".$parr['poll_a6_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 7:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a7_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a7_text']."\" /> ".$parr['poll_a7_count']." Stimmen</td></tr>";
					echo "<tr><th class=\"tbltitle\">Antwort 8:</th><td class=\"tbldata\"><input type=\"text\" name=\"poll_a8_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a8_text']."\" /> ".$parr['poll_a8_count']." Stimmen</td></tr>";
					infobox_end(1);
					echo "<input type=\"submit\" name=\"pollsubmit\" value=\"Speichern\" /> &nbsp; ";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Zur&uuml;ck\" /></form>";
				}
			}
		}
		//
		// Umfragen anzeigen
		//
		else
		{
			if (isset($_GET['del']) && $_GET['del']>0)
			{
				dbquery("DELETE FROM ".$db_table['alliance_polls']." WHERE poll_id=".$_GET['del']." AND poll_alliance_id=".$arr['alliance_id'].";");
				if (mysql_affected_rows()>0)
				{
					dbquery("DELETE FROM ".$db_table['alliance_poll_votes']." WHERE vote_poll_id=".$_GET['del']." AND vote_alliance_id=".$arr['alliance_id'].";");
					echo "Umfrage wurde gel&ouml;scht!<br/><br/>";
				}
			}
			if (isset($_GET['deactivate']) && $_GET['deactivate'])
				dbquery("UPDATE ".$db_table['alliance_polls']." SET poll_active=0 WHERE poll_id=".$_GET['deactivate']." AND poll_alliance_id=".$arr['alliance_id'].";");
			if (isset($_GET['activate']) && $_GET['activate'])
				dbquery("UPDATE ".$db_table['alliance_polls']." SET poll_active=1 WHERE poll_id=".$_GET['activate']." AND poll_alliance_id=".$arr['alliance_id'].";");

			$_SESSION['alliance_poll']=null;
			$pres=dbquery("SELECT * FROM ".$db_table['alliance_polls']." WHERE poll_alliance_id=".$arr['alliance_id'].";");
			if (mysql_num_rows($pres)>0)
			{
				echo "<table class=\"tb\"><tr><th>Titel</th><th>Frage</th><th>Erstellt</th><th style=\"width:200px;\">Aktionen</th></tr>";
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<tr><td>".stripslashes($parr['poll_title'])."</td>";
					echo "<td>".stripslashes($parr['poll_question'])."</td>";
					echo "<td>".df($parr['poll_timestamp'])."</td>";
					echo "<td><a href=\"?page=$page&amp;action=".$_GET['action']."&amp;edit=".$parr['poll_id']."\">Bearbeiten</a> ";
					if ($parr['poll_active']==1)
						echo "<a href=\"?page=$page&amp;action=".$_GET['action']."&amp;deactivate=".$parr['poll_id']."\">Deaktivieren</a> ";
					else
						echo "<a href=\"?page=$page&amp;action=".$_GET['action']."&amp;activate=".$parr['poll_id']."\">Aktivieren</a> ";
					echo "<a href=\"?page=$page&amp;action=".$_GET['action']."&amp;del=".$parr['poll_id']."\" onclick=\"return confirm('Umfrage wirklich löschen?');\">L&ouml;schen</a></td>";
				}
				echo "</table><br/>";
			}
			else
				echo "<i>Keine Umfragen vorhanden</i><br/><br/>";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=".$_GET['action']."&pollaction=create'\" value=\"Neue Umfrage erstellen\" /> &nbsp;
			<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
						}
						
}						
?>