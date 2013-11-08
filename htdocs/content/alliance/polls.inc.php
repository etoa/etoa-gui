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
							dbquery("INSERT INTO alliance_polls (
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
							ok_msg("Umfrage wurde gespeichert!");
							$_SESSION['alliance_poll']=null;
							$created=true;
						}
						else
							error_msg("Mindestens die ersten zwei Antworten müssen definiert sein!");
					}
					else
						error_msg("Frage fehlt!");
				}
				else
					error_msg("Titel fehlt!");
			}
			if (isset($created) && $created)
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Ok\" />";
			else
			{
				echo "<form action=\"?page=$page&amp;action=polls&amp;pollaction=create\" method=\"post\">";
				checker_init();
				tableStart("Neue Umfrage erstellen");
				echo "<tr><th colspan=\"2\">Es müssen mindestens <b>zwei</b> Antwortfelder ausgefüllt sein!</th>";
				echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"poll_title\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_title']."\" /></td></tr>";
				echo "<tr><th>Frage:</th><td><input type=\"text\" name=\"poll_question\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_question']."\" /></td></tr>";
				echo "<tr><th>Antwort 1:</th><td><input type=\"text\" name=\"poll_a1_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a1_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 2:</th><td><input type=\"text\" name=\"poll_a2_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a2_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 3:</th><td><input type=\"text\" name=\"poll_a3_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a3_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 4:</th><td><input type=\"text\" name=\"poll_a4_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a4_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 5:</th><td><input type=\"text\" name=\"poll_a5_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a5_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 6:</th><td><input type=\"text\" name=\"poll_a6_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a6_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 7:</th><td><input type=\"text\" name=\"poll_a7_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a7_text']."\" /></td></tr>";
				echo "<tr><th>Antwort 8:</th><td><input type=\"text\" name=\"poll_a8_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a8_text']."\" /></td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"pollsubmitnew\" value=\"Speichern\" /> &nbsp; ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Zur&uuml;ck\" /></form>";
			}
		}
		//
		// Umfrage bearbeiten
		//
		elseif (isset($_GET['edit']) && $_GET['edit']>0)
		{
			$pres=dbquery("SELECT * FROM alliance_polls WHERE poll_id=".$_GET['edit']." AND poll_alliance_id=".$arr['alliance_id'].";");
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
								dbquery("UPDATE alliance_polls SET
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
								error_msg("Mindestens die ersten zwei Antworten müssen definiert sein!");
						}
						else
							error_msg("Frage fehlt!");
					}
					else
						error_msg("Titel fehlt!");
				}
				if ($updated)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=".$_GET['action']."';\" value=\"Ok\" />";
				else
				{
					echo "<form action=\"?page=$page&amp;action=polls&amp;edit=".$parr['poll_id']."\" method=\"post\">";
					checker_init();
					tableStart("Umfrage bearbeiten");
					echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"poll_title\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_title']."\" /></td></tr>";
					echo "<tr><th>Frage:</th><td><input type=\"text\" name=\"poll_question\" size=\"80\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_question']."\" /></td></tr>";
					echo "<tr><th>Antwort 1:</th><td><input type=\"text\" name=\"poll_a1_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a1_text']."\" /> ".$parr['poll_a1_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 2:</th><td><input type=\"text\" name=\"poll_a2_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a2_text']."\" /> ".$parr['poll_a2_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 3:</th><td><input type=\"text\" name=\"poll_a3_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a3_text']."\" /> ".$parr['poll_a3_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 4:</th><td><input type=\"text\" name=\"poll_a4_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a4_text']."\" /> ".$parr['poll_a4_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 5:</th><td><input type=\"text\" name=\"poll_a5_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a5_text']."\" /> ".$parr['poll_a5_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 6:</th><td><input type=\"text\" name=\"poll_a6_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a6_text']."\" /> ".$parr['poll_a6_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 7:</th><td><input type=\"text\" name=\"poll_a7_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a7_text']."\" /> ".$parr['poll_a7_count']." Stimmen</td></tr>";
					echo "<tr><th>Antwort 8:</th><td><input type=\"text\" name=\"poll_a8_text\" size=\"70\" maxlength=\"150\" value=\"".$_SESSION['alliance_poll']['poll_a8_text']."\" /> ".$parr['poll_a8_count']." Stimmen</td></tr>";
					tableEnd();
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
				dbquery("DELETE FROM alliance_polls WHERE poll_id=".$_GET['del']." AND poll_alliance_id=".$arr['alliance_id'].";");
				if (mysql_affected_rows()>0)
				{
					dbquery("DELETE FROM alliance_poll_votes WHERE vote_poll_id=".$_GET['del']." AND vote_alliance_id=".$arr['alliance_id'].";");
					ok_msg("Umfrage wurde gel&ouml;scht!");
				}
			}
			if (isset($_GET['deactivate']) && $_GET['deactivate'])
				dbquery("UPDATE alliance_polls SET poll_active=0 WHERE poll_id=".$_GET['deactivate']." AND poll_alliance_id=".$arr['alliance_id'].";");
			if (isset($_GET['activate']) && $_GET['activate'])
				dbquery("UPDATE alliance_polls SET poll_active=1 WHERE poll_id=".$_GET['activate']." AND poll_alliance_id=".$arr['alliance_id'].";");

			$_SESSION['alliance_poll']=null;
			$pres=dbquery("SELECT * FROM alliance_polls WHERE poll_alliance_id=".$arr['alliance_id'].";");
			if (mysql_num_rows($pres)>0)
			{
				tableStart();
				echo "<tr><th>Titel</th><th>Frage</th><th>Erstellt</th><th style=\"width:200px;\">Aktionen</th></tr>";
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
				tableEnd();
			}
			else
				error_msg("Keine Umfragen vorhanden!");
			echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=".$_GET['action']."&pollaction=create'\" value=\"Neue Umfrage erstellen\" /> &nbsp;
			<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
						}
						
}						
?>