<?PHP
	if ($cu->allianceId() == 0)
	{
		// Check application
		$application_alliance=0;
		$res = dbquery("
		SELECT
			alliance_id,
			timestamp
		FROM
			alliance_applications
		WHERE
			user_id=".$cu->id()."	
		;");
		if (mysql_num_rows($res))
		{
			$arr=mysql_fetch_row($res);
			$application_alliance=$arr[0];
			$application_timestamp=$arr[1];
		}				

		//
		// Infotext bei aktiver Bewerbung
		//
		if ($application_alliance>0)
		{
			// Bewerbung zurückziehen
			if (isset($_GET['action']) && $_GET['action']=="cancelapplication")
			{
        $alliances = get_alliance_names();
        send_msg($alliances[$application_alliance]['founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung zurückgezogen","Der Spieler ".$cu->nick()." hat die Bewerbung bei deiner Allianz zurückgezogen!");
        add_alliance_history($application_alliance,"Der Spieler [b]".$cu->nick()."[/b] zieht seine Bewerbung zurück.");
        dbquery("
        DELETE FROM 
        	alliance_applications
        WHERE 
        	user_id=".$cu->id()."
        	AND alliance_id=".$application_alliance.";");
        echo "Deine Bewerbung wurde gel&ouml;scht!<br/><br/>
        <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"OK\" />";
			}
			// Bewerbungsstatus anzeigen
			else
			{
				echo "<h2>Bewerbungsstatus</h2>";
				$appres = dbquery("
				SELECT
        	alliance_tag,
        	alliance_name
				FROM
        	alliances
				WHERE
					alliance_id=".$application_alliance.";");
				if (mysql_num_rows($appres)>0)
				{
					$apparr = mysql_fetch_array($appres);
         	echo "Du hast dich am ".df($application_timestamp)." bei der Allianz <b>[".$apparr['alliance_tag']."] ".$apparr['alliance_name']."</b> beworben<br/> 
         	und musst nun darauf warten, dass deine Bewerbung akzeptiert wird!<br/><br/>
         	<input type=\"button\" onclick=\"document.location='?page=$page&action=cancelapplication';\" value=\"Bewerbung zurückziehen\" />";
				}
				else
				{
         	echo "Du hast dich am ".df($application_timestamp)." bei einer Allianz beworben, diese Allianz existiert aber leider nicht mehr.
         	Deine Bewerbung wurde deshalb gelöscht! 
         	<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Bei einer anderen Allianz bewerben\" />";
				}
			}
		}
		
		//
		// Allianzgründung
		//
		elseif (isset($_GET['action']) && $_GET['action']=="create")
		{
			echo "<h2>Gr&uuml;ndung einer Allianz</h2>";

			// Allianzgründung speichern
			if (isset($_POST['createsubmit']) && $_POST['createsubmit']!="" && checker_verify())
			{
				// Prüfen, ob der Allianzname bzw. Tag nicht nur aus Leerschlägen besteht
				$check_tag = str_replace(' ','',$_POST['alliance_tag']);
				$check_name = str_replace(' ','',$_POST['alliance_name']);
				
				if($check_name!='' && $check_tag!='')
				{
					$s['alliance_creation_tag']=$_POST['alliance_tag'];
					$s['alliance_creation_name']=$_POST['alliance_name'];
					$s['alliance_creation_text']=$_POST['alliance_text'];
					$s['alliance_creation_url']=$_POST['alliance_url'];
		
					$check_tag = check_illegal_signs($_POST['alliance_tag']);
					$check_name = check_illegal_signs($_POST['alliance_name']);
					$signs= check_illegal_signs("gibt eine liste von unerlaubten zeichen aus! ; < > .&");
		
					if ($_POST['alliance_tag']=="" || $_POST['alliance_name']=="" || $check_tag!="" || $check_name!="")
					{
						echo "<b>Fehler:</b> Du hast keinen Namen oder kein Tag eingegeben oder ein unerlaubtes Zeichen (".$signs.") verwendet!<br/><br/>";
						echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=create'\" value=\"Zur&uuml;ck\" />";
					}
					elseif (mysql_num_rows(dbquery("SELECT alliance_id FROM alliances WHERE alliance_tag='".$_POST['alliance_tag']."';"))>0)
					{
						echo "<b>Fehler:</b> Die Allianz mit dem Tag <b>".$_POST['alliance_tag']."</b> existiert bereits!<br/><br/>";
						echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=create'\" value=\"Zur&uuml;ck\" />";
					}
					else
					{
						dbquery("INSERT INTO alliances (alliance_tag,alliance_name,alliance_text,alliance_url,alliance_founder_id,alliance_foundation_date) VALUES ('".addslashes($_POST['alliance_tag'])."','".addslashes($_POST['alliance_name'])."','".addslashes($_POST['alliance_text'])."','".$_POST['alliance_url']."','".$cu->id()."','".time()."');");
						$aid = mysql_insert_id();
						dbquery("UPDATE users SET user_alliance_id='$aid' WHERE user_id='".$cu->id()."';");
						
						$cu->setAllianceId($aid);
						add_log(5,"Die Allianz [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b] wurde vom Spieler [b]".$cu->nick()."[/b] gegr&uuml;ndet!",time());
						add_alliance_history($aid,"Die Allianz [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b] wurde vom Spieler [b]".$cu->nick()."[/b] gegründet!");
						
						$cu->addToUserLog("alliance","{nick} hat die Allianz ".$_POST['alliance_name']." gegründet.");
						
						
						echo "Die Allianz <b>".$_POST['alliance_name']." (".$_POST['alliance_tag'].")</b> wurde gegr&uuml;ndet!<br/><br/>";
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur Allianz-&Uuml;bersicht\" />";
					}
				}
				else
				{
					echo "<b>Fehler:</b> Der Allianzname und/oder der Allianztag darf nicht nur aus Leerschlägen bestehen!<br/><br/>
					<input type=\"button\" onclick=\"document.location='?page=$page&action=create'\" value=\"Zur&uuml;ck\" />";
				}
			}
			else
			{
				echo "<form action=\"?page=$page&amp;action=create\" method=\"post\">";
				checker_init();
				tableStart("Allianz-Daten");
				echo "<tr><td class=\"tbltitle\">Allianz-Tag:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".$s['alliance_creation_tag']."\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Allianz-Name:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".$s['alliance_creation_name']."\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Beschreibung:</td><td class=\"tbldata\"><textarea rows=\"10\" cols=\"50\" name=\"alliance_text\">".$s['alliance_creation_text']."</textarea></td></tr>";
				echo "<tr><td class=\"tbltitle\">Website/Forum:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_url\" size=\"40\" maxlength=\"255\" value=\"".$s['alliance_creation_url']."\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Bildpfad:</td><td class=\"tbldata\">Das Allianz-Bild kann nachträglich in den Allianz-Einstellungen heraufgeladen werden!</td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"createsubmit\" value=\"Speichern\" /></form>";
			}
		}
	
		//
		// Bewerbung bei einer Allianz
		//
		elseif (isset($_GET['action']) && $_GET['action']=="join")
		{
			// Bewerbungstext schreiben
			if (isset($_GET['alliance_id']) && intval($_GET['alliance_id'])>0)
			{
				$res=dbquery("
				SELECT 
					alliance_id,
					alliance_tag,
					alliance_name,
					alliance_application_template,
					alliance_accept_applications
				FROM 
					alliances 
				WHERE 
					alliance_id='".intval($_GET['alliance_id'])."'");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					echo "<h2>Bewerbung bei der Allianz [".$arr['alliance_tag']."] ".$arr['alliance_name']."</h2>";
					if($arr['alliance_accept_applications']==1)
					{
						echo "<form action=\"?page=$page&amp;action=join\" method=\"post\">";
						checker_init();
						tableStart("Bewerbungstext");
						echo "<tr><td class=\"tbltitle\">Nachricht:</td><td class=\"tbldata\"><textarea rows=\"15\" cols=\"80\" name=\"user_alliance_application\">".$arr['alliance_application_template']."</textarea></td>";
						tableEnd();
						echo "<input type=\"hidden\" name=\"user_alliance_id\" value=\"".intval($arr['alliance_id'])."\" />";
						echo "<input type=\"submit\" name=\"submitapplication\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=join'\" value=\"Zur&uuml;ck\" /></form>";
					}
					else
					{
						echo "Die Allianz nimmt keine Bewerbungen an!<br>";
					}
				}
				else
				{
					echo "Fehler! Allianzdatensatz nicht gefunden!";
				}
			}
			// Bewerbungstext senden
			elseif (isset($_POST['submitapplication']) && checker_verify())
			{
				echo "<h2>Bewerbung abschicken</h2>";

				if ($_POST['user_alliance_application']!='')
				{
					$alliances = get_alliance_names();
					send_msg($alliances[$_POST['user_alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung","Der Spieler ".$cu->nick()." hat sich bei deiner Allianz beworben. Gehe auf die [url ?page=alliance&action=applications]Allianzseite[/url] für Details!");
					add_alliance_history($_POST['user_alliance_id'],"Der Spieler [b]".$cu->nick()."[/b] bewirbt sich sich bei der Allianz.");
					dbquery("
					INSERT INTO
						alliance_applications
					(
						user_id,
						alliance_id,
						text,
						timestamp
					)
					VALUES
					(
						".$cu->id().",
						".$_POST['user_alliance_id'].",
						'".addslashes($_POST['user_alliance_application'])."',
						".time()."
					);
					");
					
					echo "Deine Bewerbung bei der Allianz <b>[".$alliances[$_POST['user_alliance_id']]['tag']."] ".$alliances[$_POST['user_alliance_id']]['name']."</b> wurde gespeichert!<br/>
					 Die Allianzleitung wurde informiert und wird deine Bewerbung ansehen.";
					echo "<br/><br/><input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
				}
				else
				{
					echo "<b>Fehler:</b> Du musst einen Bewerbungstext eingeben! <br/><br/>
					<input value=\"Zur&uuml;ck\" type=\"button\" onclick=\"document.location='?page=$page&action=join&alliance_id=".$_POST['user_alliance_id']."'\" />";
				}
			}
			// Allianzauswahl anzeigen
			else
			{
				echo "<h2>Allianz w&auml;hlen</h2>
				Nicht alle Allianzen akzeptieren jederzeit eine Bewerbung. <br/>
				Im Folgenden findest du eine Liste der Allianzen die momentan Bewerbungen akzeptieren:<br/><br/>";
				$res=dbquery("
				SELECT
					alliance_id,
					alliance_tag,
					alliance_name,
					alliance_accept_applications
				FROM
					alliances
				WHERE
					alliance_accept_applications=1
				ORDER BY
					alliance_name,
					alliance_tag;");
				if (mysql_num_rows($res)>0)
				{
					echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
					echo "<tr>
									<td class=\"tbltitle\">Tag</td>
									<td class=\"tbltitle\">Name</td>
									<td class=\"tbltitle\" style=\"width:100px;\">Aktionen</td>
							</tr>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<tr><td class=\"tbldata\">".$arr['alliance_tag']."</td>
						<td class=\"tbldata\">".$arr['alliance_name']."</td>
						<td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['alliance_id']."\">Info</a>";
						echo "&nbsp;<a href=\"?page=$page&action=join&alliance_id=".$arr['alliance_id']."\">Bewerben</a>";
						echo "</td></tr>";
					}
					echo "</table><br/><a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.</a>";
				}
				else
				{
					echo "Es gibt im Moment keine Allianzen denen man beitreten k&ouml;nnte! 
					<a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.</a>";
				}
			}			
		}

		//
		// Infotext wenn in keiner Allianz
		//
		else
		{
			echo "Es kann von Vorteil sein, wenn man sich nicht alleine gegen den Rest des Universums durchsetzen muss. Dazu gibt es dass Allianz-System,
			 mit dem du dich mit anderen Spielern als Team zusammentun kannst. Viele Allianzen pflegen eine regelm&auml;ssige Kommunikation, bieten Schutz vor
			 Angriffen oder r&auml;chen dich wenn du angegriffen worden bist. Trete einer Allianz bei oder gr&uuml;nde selber eine Allianz.<br/><br/>";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Einer Allianz beitreten\" />&nbsp;&nbsp;&nbsp;";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=create';\" value=\"Eine Allianz gr&uuml;nden\" />";
		}
		
	}

?>