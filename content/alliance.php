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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: alliance.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Create, view and manage an alliance
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//Bewerbung aktualisieren
	$res=dbquery("
	SELECT
		user_alliance_application,
		user_alliance_id,
		user_alliance_rank_id
	FROM
		".$db_table['users']."
	WHERE
		user_id=".$s['user']['id']."");
		$arr = mysql_fetch_array($res);
    if ($arr['user_alliance_application']!='' && $arr['user_alliance_id']>0)
        $application=1;
    else
        $application=0;
  $myRankId=$arr['user_alliance_rank_id'];

 	$s['user']['alliance_id']=$arr['user_alliance_id'];
 	$s['user']['alliance_application']=$application;


	// BEGIN SKRIPT //
	echo "<h1>Allianz</h1>";
	echo "<div id=\"allianceinfo\"></div>"; //nur zu entwicklungszwecken!

	//
	// Allianzgründung
	//
	if (isset($_GET['action']) && $_GET['action']=="create")
	{
		echo "<h2>Gr&uuml;ndung einer Allianz</h2>";
		if ($s['user']['alliance_id']==0)
		{
			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			infobox_start("Allianz-Daten",1);
			echo "<tr><td class=\"tbltitle\">Allianz-Tag:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".$_SESSION['alliance_creation']['alliance_tag']."\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Allianz-Name:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".$_SESSION['alliance_creation']['alliance_name']."\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Beschreibung:</td><td class=\"tbldata\"><textarea rows=\"10\" cols=\"50\" name=\"alliance_text\">".$_SESSION['alliance_creation']['alliance_text']."</textarea></td></tr>";
			echo "<tr><td class=\"tbltitle\">Website/Forum:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_url\" size=\"40\" maxlength=\"255\" value=\"".$_SESSION['alliance_creation']['alliance_url']."\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Bildpfad:</td><td class=\"tbldata\">Das Allianz-Bild kann nachträglich in den Allianz-Einstellungen heraufgeladen werden!</td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" name=\"createsubmit\" value=\"Speichern\" /></form>";
		}
		else
		{
			echo "<b>Fehler:</b> Du bist schon in einer Allianz und kannst deshalb keine neue gr&uuml;nden!<br/><br/>";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur Allianz-&Uuml;bersicht\" />";
		}
	}

	//
	// Allianzgründung speichern
	//
	elseif (isset($_POST['createsubmit']) && $_POST['createsubmit']!="" && checker_verify())
	{
		echo "<h2>Gr&uuml;ndung einer Allianz</h2><br>";
		if ($s['user']['alliance_id']==0)
		{
			// Prüfen, ob der Allianzname bzw. Tag nicht nur aus Leerschlägen besteht
			$check_tag = str_replace(' ','',$_POST['alliance_tag']);
			$check_name = str_replace(' ','',$_POST['alliance_name']);
			
			if($check_name!='' && $check_tag!='')
			{
				$_SESSION['alliance_creation']['alliance_tag']=$_POST['alliance_tag'];
				$_SESSION['alliance_creation']['alliance_name']=$_POST['alliance_name'];
				$_SESSION['alliance_creation']['alliance_text']=$_POST['alliance_text'];
				$_SESSION['alliance_creation']['alliance_url']=$_POST['alliance_url'];
				
	
				$check_tag = check_illegal_signs($_POST['alliance_tag']);
				$check_name = check_illegal_signs($_POST['alliance_name']);
				$signs= check_illegal_signs("gibt eine liste von unerlaubten zeichen aus! ; < > .&");
	
				if ($_POST['alliance_tag']=="" || $_POST['alliance_name']=="" || $check_tag!="" || $check_name!="")
				{
					echo "<b>Fehler:</b> Du hast keinen Namen oder kein Tag eingegeben oder ein unerlaubtes Zeichen (".$signs.") verwendet!<br/><br/>";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=create'\" value=\"Zur&uuml;ck\" />";
				}
				elseif (mysql_num_rows(dbquery("SELECT alliance_id FROM ".$db_table['alliances']." WHERE alliance_tag='".$_POST['alliance_tag']."';"))>0)
				{
					echo "<b>Fehler:</b> Die Allianz mit dem Tag <b>".$_POST['alliance_tag']."</b> existiert bereits!<br/><br/>";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=create'\" value=\"Zur&uuml;ck\" />";
				}
				else
				{
					dbquery("INSERT INTO ".$db_table['alliances']." (alliance_tag,alliance_name,alliance_text,alliance_url,alliance_founder_id,alliance_foundation_date) VALUES ('".addslashes($_POST['alliance_tag'])."','".addslashes($_POST['alliance_name'])."','".addslashes($_POST['alliance_text'])."','".$_POST['alliance_url']."','".$s['user']['id']."','".time()."');");
					$aid = mysql_insert_id();
					dbquery("UPDATE ".$db_table['users']." SET user_alliance_id='$aid' WHERE user_id='".$s['user']['id']."';");
					$s['user']['alliance_id']=$aid;
					add_log(5,"Die Allianz [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b] wurde vom Spieler [b]".$s['user']['nick']."[/b] gegr&uuml;ndet!",time());
					add_alliance_history($aid,"Die Allianz [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b] wurde vom Spieler [b]".$s['user']['nick']."[/b] gegründet!");
					$_SESSION['alliance_creation']=Null;
					echo "Die Allianz <b>".$_POST['alliance_name']." (".$_POST['alliance_tag'].")</b> wurde gegr&uuml;ndet!<br/><br/>";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur Allianz-&Uuml;bersicht\" />";
				}
			}
			else
			{
				echo "<b>Fehler:</b> Der Allianzname und/oder der Allianztag darf nicht nur aus Leerschlägen bestehen!<br/><br/>";
			}
		}
		else
		{
			echo "<b>Fehler:</b> Du bist schon in einer Allianz und kannst deshalb keine neue gr&uuml;nden!<br/><br/>";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur Allianz-&Uuml;bersicht\" />";
		}
	}

	//
	// Bewerbung bei einer Allianz
	//
	elseif (isset($_GET['action']) && $_GET['action']=="join")
	{
		if ($application==0)
		{
			// Bewerbungstext schreiben
			if (intval($_GET['alliance_id'])>0)
			{
				$res=dbquery("
				SELECT 
					alliance_id,
					alliance_tag,
					alliance_name,
					alliance_application_template,
					alliance_accept_applications
				FROM 
					".$db_table['alliances']." 
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
						infobox_start("Bewerbungstext",1);
						echo "<tr><td class=\"tbltitle\">Nachricht:</td><td class=\"tbldata\"><textarea rows=\"15\" cols=\"80\" name=\"user_alliance_application\">".$arr['alliance_application_template']."</textarea></td>";
						infobox_end(1);
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
			elseif ($_POST['submitapplication'] && checker_verify())
			{
				echo "<h2>Bewerbung abschicken</h2>";

				$check_appliaction = $_POST['user_alliance_application'];
				$check_appliaction=str_replace(' ','',$check_appliaction);

				if ($check_appliaction!='')
				{
					$alliances = get_alliance_names();
					send_msg($alliances[$_POST['user_alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung","Der Spieler ".$s['user']['nick']." hat sich bei deiner Allianz beworben. Geh auf die Allianzseite für Details!");
					add_alliance_history($_POST['user_alliance_id'],"Der Spieler [b]".$s['user']['nick']."[/b] bewirbt sich sich bei der Allianz.");
					dbquery("UPDATE ".$db_table['users']." SET user_alliance_application='".addslashes($_POST['user_alliance_application'])."',user_alliance_id='".$_POST['user_alliance_id']."' WHERE user_id='".$s['user']['id']."';");
					$alliances = get_alliance_names();
					echo "Deine Bewerbung bei der Allianz <b>[".$alliances[$_POST['user_alliance_id']]['tag']."] ".$alliances[$_POST['user_alliance_id']]['name']."</b> wurde gespeichert! Die Allianzleitung wurde informiert und wird deine Bewerbung ansehen.";
					echo "<br/><br/><input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
					$application=1;
					$s['user']['alliance_id']=$_POST['user_alliance_id'];
				}
				else
					echo "<b>Fehler:</b> Du musst einen Bewerbungstext eingeben! <br/><br/><input value=\"Zur&uuml;ck\" type=\"button\" onclick=\"document.location='?page=$page&action=join&alliance_id=".$_POST['user_alliance_id']."'\" />";
			}
			// Allianzauswahl anzeigen
			else
			{
				echo "<h2>Allianz w&auml;hlen</h2>";
				$res=dbquery("
				SELECT
					alliances.alliance_id,
					alliances.alliance_tag,
					alliances.alliance_name,
					alliance_accept_applications
				FROM
					".$db_table['alliances']."
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
						
						if($arr['alliance_accept_applications']==1) 
						{
							echo "&nbsp;<a href=\"?page=$page&action=join&alliance_id=".$arr['alliance_id']."\">Bewerben</a>";
						}
						echo "</td></tr>";
					}
					echo "</table>";
				}
				else
				{
					echo "Es gibt noch keine Allianzen denen man beitreten k&ouml;nnte! <a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.";
				}
			}
		}
		else
			echo "Du hast dich bereits beworben! <br/><br/><input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
	}

	//
	// Externe Allianz-Info anzeigen
	//
	elseif ((isset($_GET['info_id']) && intval($_GET['info_id'])>0) || (isset($_GET['id']) && intval($_GET['id'])>0))
	{
		require("alliance/external.inc.php");
	}
	else
	{

		//
		// Infotext wenn in keiner Allianz
		//
		if ($s['user']['alliance_id']==0 && $application==0)
		{
			echo "Es kann von Vorteil sein, wenn man sich nicht alleine gegen den Rest des Universums durchsetzen muss. Dazu gibt es dass Allianz-System,
			 mit dem du dich mit anderen Spielern als Team zusammentun kannst. Viele Allianzen pflegen eine regelm&auml;ssige Kommunikation, bieten Schutz vor
			 Angriffen oder r&auml;chen dich wenn du angegriffen worden bist. Trete einer Allianz bei oder gr&uuml;nde selber eine Allianz.<br/><br/>";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Einer Allianz beitreten\" />&nbsp;&nbsp;&nbsp;";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=create';\" value=\"Eine Allianz gr&uuml;nden\" />";
		}

		//
		// Infotext bei aktiver Bewerbung
		//
		elseif ($application==1)
		{
			// Bewerbung zurückziehen
			if (isset($_GET['action']) && $_GET['action']=="cancelapplication")
			{
				$ares = dbquery("
				SELECT
					user_id,
					user_alliance_application
				FROM
					".$db_table['users']."
				WHERE
					user_id='".$s['user']['id']."'
					AND user_alliance_id!=0;");

				if (mysql_num_rows($ares)>0)
				{
					$aarr = mysql_fetch_array($ares);
					if($aarr['user_alliance_application']!='')
					{
                        $alliances = get_alliance_names();
                        send_msg($alliances[$s['user']['alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung zurückgezogen","Der Spieler ".$s['user']['nick']." hat die Bewerbung bei deiner Allianz zurückgezogen!");
                        add_alliance_history($s['user']['alliance_id'],"Der Spieler [b]".$s['user']['nick']."[/b] zieht seine Bewerbung zurück.");
                        dbquery("UPDATE ".$db_table['users']." SET user_alliance_application='',user_alliance_id=0 WHERE user_id='".$s['user']['id']."';");
                        $application=0;
                        $s['user']['alliance_id']=0;
                        echo "Deine Bewerbung wurde gel&ouml;scht!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"OK\" />";
					}
					else
					{
						echo "Du kannst deine Bewerbung nicht mehr zurückziehen, sie wurde bereits bearbeitet!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"OK\" />";
					}
				}

			}
			// Bewerbungsstatus anzeigen
			else
			{
				echo "<h2>Bewerbungsstatus</h2>";
				$appres = dbquery("
				SELECT
                    users.user_alliance_id,
                    users.user_alliance_application,
                    alliances.alliance_tag,
                    alliances.alliance_name
				FROM
                    ".$db_table['users'].",
                    ".$db_table['alliances']."
				WHERE
					users.user_alliance_id=alliances.alliance_id
					AND users.user_id='".$s['user']['id']."';");
				if (mysql_num_rows($appres)>0)
				{
					$apparr = mysql_fetch_array($appres);
					// Bewerbung gelesen
					if($apparr['user_alliance_application']=='')
					{
						$s['user']['alliance_id']=$apparr['user_alliance_id'];
            $application=0;
            echo "Deine Bewerbung wurde gelesen! Klicke <a href=\"?page=messages\">hier für den Bericht!";
          }
          // Bewerbung ausstehend
          else
          {
          	echo "Du hast dich bei der Allianz <b>[".$apparr['alliance_tag']."] ".$apparr['alliance_name']."</b> beworben<br/> und musst nun darauf warten, dass deine Bewerbung akzeptiert wird!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page&action=cancelapplication';\" value=\"Bewerbung zurückziehen\" />";
          }
				}
			}
		}

/**************************************************/
/* User ist in der Allianz                        */
/**************************************************/

		//
		// Allianz-Features
		//
		else
		{
			// Allianzdaten laden
			$res = dbquery("
			SELECT
			    *
			FROM
				".$db_table['alliances']."
			WHERE
				alliance_id='".$s['user']['alliance_id']."';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);

				// Rechte laden
				$rightres=dbquery("SELECT * FROM ".$db_table['alliance_rights']." ORDER BY right_desc;");
				$rights=array();
				if (mysql_num_rows($rightres)>0)
				{
					while ($rightarr=mysql_fetch_array($rightres))
					{
						$rights[$rightarr['right_id']]['key']=$rightarr['right_key'];
						$rights[$rightarr['right_id']]['desc']=$rightarr['right_desc'];
						$check_res = dbquery("
            SELECT
                alliance_rankrights.rr_id
            FROM
                ".$db_table['alliance_rankrights'].",
                ".$db_table['alliance_ranks']."
            WHERE
                alliance_ranks.rank_id=alliance_rankrights.rr_rank_id
                AND alliance_ranks.rank_alliance_id=".$s['user']['alliance_id']."
                AND alliance_rankrights.rr_right_id=".$rightarr['right_id']."
                AND alliance_rankrights.rr_rank_id=".$myRankId.";");
						
						if (mysql_num_rows($check_res)>0)
							$myRight[$rightarr['right_key']]=true;
						else
							$myRight[$rightarr['right_key']]=false;
					}
				}

				// Gründer prüfen
				if ($arr['alliance_founder_id']==$s['user']['id'])
				{
					$isFounder=true;
				}
				else
				{
					$isFounder=false;
				}

				//
				// Allianzdaten ändern
				//
				if (isset($_GET['action']) && $_GET['action']=="editdata")
				{
					if (Alliance::checkActionRights('editdata'))
					{
						require("alliance/editdata.inc.php");
					}
				}

				//
				// Bewerbungsvorlage bearbeiten
				//
				elseif (isset($_GET['action']) && $_GET['action']=="applicationtemplate")
				{
					if (Alliance::checkActionRights('applicationtemplate'))
					{
						require("alliance/applicationtemplate.inc.php");
					}
				}

				//
				// Umfragen anzeigen
				//
				elseif (isset($_GET['action']) && $_GET['action']=="viewpoll")
				{
					require("alliance/viewpoll.inc.php");
				}

				//
				// Umfragen erstellen / bearbeiten
				//
				elseif (isset($_GET['action']) && $_GET['action']=="polls")
				{
					if (Alliance::checkActionRights('polls'))
					{
						require("alliance/polls.inc.php");
					}
				}

				//
				// Mitglieder bearbeiten
				//
				elseif (isset($_GET['action']) && $_GET['action']=="editmembers")
				{
					if (Alliance::checkActionRights('editmembers'))
					{
						require("alliance/editmembers.inc.php");
					}
				}

				//
				// Rundmail
				//
				elseif (isset($_GET['action']) && $_GET['action']=="massmail")
				{
					if (Alliance::checkActionRights('massmail'))
					{
						require("alliance/massmail.inc.php");
					}
				}

				//
				// Ränge
				//
				elseif (isset($_GET['action']) && $_GET['action']=="ranks")
				{
					if (Alliance::checkActionRights('ranks'))
					{
						require("alliance/ranks.inc.php");
					}
				}

				//
				// Bewerbungen
				//
				elseif (isset($_GET['action']) && $_GET['action']=="applications")
				{
					if (Alliance::checkActionRights('applications'))
					{
						require("alliance/applications.inc.php");
					}
				}

				//
				// Allianz auflösen bestätigen
				//
				elseif (isset($_GET['action']) && $_GET['action']=="liquidate")
				{
					if (Alliance::checkActionRights('liquidate'))
					{
						require("alliance/liquidate.inc.php");
					}
				}

				//
				// Allianz-News
				//
				elseif (isset($_GET['action']) && $_GET['action']=="alliancenews")
				{
					if (Alliance::checkActionRights('alliancenews'))
					{
						require("alliance/alliancenews.inc.php");
					}
				}

				//
				// Bündniss-/Kriegspartner wählen
				//
				elseif (isset($_GET['action']) && $_GET['action']=="relations")
				{
					if (Alliance::checkActionRights('relations'))
					{
						require("alliance/diplomacy.inc.php");
					}					
				}

				//
				// Geschichte anzeigen
				//
				elseif (isset($_GET['action']) && $_GET['action']=="history")
				{
					if (Alliance::checkActionRights('history'))
					{
						require("alliance/history.inc.php");
					}
				}

				//
				// Mitglieder anzeigen
				//
				elseif (isset($_GET['action']) && $_GET['action']=="viewmembers")
				{
					if (Alliance::checkActionRights('viewmembers'))
					{
						require("alliance/viewmembers.inc.php");
					}
				}

				//
				// Allianz verlassen (Durchführen)
				//
				elseif (isset($_GET['action']) && $_GET['action']=="leave" && !$isFounder)
				{
					echo "<h2>Allianz-Austritt</h2>";
					if ($s['user']['alliance_id']!=0)
					{
						echo "Du bist aus der Allianz ausgetreten!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
						$alliances = get_alliance_names();
						dbquery("UPDATE ".$db_table['users']." SET user_alliance_rank_id=0,user_alliance_id=0 WHERE user_id='".$s['user']['id']."';");
						send_msg($alliances[$s['user']['alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Allianzaustritt","Der Spieler ".$s['user']['nick']." trat aus der Allianz aus!");
						add_alliance_history($s['user']['alliance_id'],"Der Spieler [b]".$s['user']['nick']."[/b] trat aus der Allianz aus!");
						$allys = get_alliance_names();
						add_log(5,"Der Spieler [b]".$s['user']['nick']."[/b] ist aus der Allianz [b][".$allys[$s['user']['alliance_id']]['tag']."] ".$allys[$s['user']['alliance_id']]['name']."[/b] ausgetreten!",time());
						$s['user']['alliance_id']=0;
					}
					else
						echo "Du bist in keiner Allianz!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
				}

				//
				// Allianz-Hauptseite anzeigen
				//
				else
				{
					// Änderungen übernehmen
					if (isset($_POST['editsubmit']) && $_POST['editsubmit']!="" && checker_verify())
					{
            $alliance_img_string="";
            if ($_POST['alliance_img_del']==1)
            {
              if (file_exists(ALLIANCE_IMG_DIR."/".$arr['alliance_img']))
              {
                  @unlink(ALLIANCE_IMG_DIR."/".$arr['alliance_img']);
              }
              $alliance_img_string="alliance_img='',";
            }
            elseif ($_FILES['alliance_img_file']['tmp_name']!="")
            {
            	if ($_FILES['alliance_img_file']['size']<=ALLIANCE_IMG_MAX_SIZE)
            	{
            		
                $source=$_FILES['alliance_img_file']['tmp_name'];
                $ims = getimagesize($source);
                
               	$ext = substr($ims['mime'],strrpos($ims['mime'],"/")+1);
               	if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png")
               	{                  
                  //überprüft Bildgrösse
                  if ($ims[0]<=ALLIANCE_IMG_MAX_WIDTH && $ims[1]<=ALLIANCE_IMG_MAX_HEIGHT)
                  {
                      $fname = "alliance_".$s['user']['alliance_id']."_".time().".".$ext;
                      if (file_exists(ALLIANCE_IMG_DIR."/".$arr['user_avatar']))
                          @unlink(ALLIANCE_IMG_DIR."/".$arr['user_avatar']);
                      move_uploaded_file($source,ALLIANCE_IMG_DIR."/".$fname);
	                    if ($ims[0]>ALLIANCE_IMG_WIDTH || $ims[1]>ALLIANCE_IMG_HEIGHT)
											{
												if (resizeImage(ALLIANCE_IMG_DIR."/".$fname,ALLIANCE_IMG_DIR."/".$fname,ALLIANCE_IMG_WIDTH,ALLIANCE_IMG_HEIGHT,$ext))
												{
													echo "Bildgrösse wurde angepasst! ";
                        	echo "Allianzbild gespeichert!<br/>";
                        	$alliance_img_string="alliance_img='".$fname."',";
												}
												else
												{
													Echo "Bildgrösse konnte nicht angepasst werden!";
                          @unlink(ALLIANCE_IMG_DIR."/".$arr['user_avatar']);
												}
											}
											else
											{
                      	echo "Allianzbild gespeichert!<br/>";
                      	$alliance_img_string="alliance_img='".$fname."',";
                      }
                  }
                  else
                  {
                      echo "Fehler! Das Allianzbild hat die falsche Gr&ouml;sse (".$ims[0]."*".$ims[1].")!<br/>";
                  }
               	}
               	else
               	{
                  echo "Fehler! Das Allianzbild muss vom Typ jpeg, png oder gif sein.!<br/>";
								}
							}	                 	
             	else
             	{
                echo "Fehler! Das Allianzbild ist zu gross (Max ".nf(ALLIANCE_IMG_MAX_SIZE)." Byte)!<br/>";
							}
            }

						dbquery("
						UPDATE 
							".$db_table['alliances']." 
						SET 
							alliance_tag='".addslashes($_POST['alliance_tag'])."', 
							alliance_name='".addslashes($_POST['alliance_name'])."', 
							alliance_text='".addslashes($_POST['alliance_text'])."',
						 	".$alliance_img_string."
							alliance_url='".$_POST['alliance_url']."',
							alliance_accept_applications='".$_POST['alliance_accept_applications']."',
							alliance_accept_bnd='".$_POST['alliance_accept_bnd']."'
						WHERE 
							alliance_id=".$s['user']['alliance_id'].";");
						$res = dbquery("SELECT * FROM ".$db_table['alliances']." WHERE alliance_id='".$s['user']['alliance_id']."';");
						$arr = mysql_fetch_array($res);
						echo "Die &Auml;nderungen wurden übernommen!<br/>$message<br/>";
					}

					// Bewerbungsvorlage speichern
					if (isset($_POST['applicationtemplatesubmit']) && $_POST['applicationtemplatesubmit']!="" && checker_verify())
					{
						dbquery("UPDATE ".$db_table['alliances']." SET alliance_application_template='".addslashes($_POST['alliance_application_template'])."' WHERE alliance_id=".$s['user']['alliance_id'].";");
						echo "Die &Auml;nderungen wurden übernommen!<br/><br/>";
					}

	        // Allianz auflösen
					if (isset($_POST['liquidatesubmit']) && $_POST['liquidatesubmit']!="" && $isFounder && $s['user']['alliance_id']==$_POST['id_control'] && checker_verify())
					{
						delete_alliance($arr['alliance_id'],true);
						$s['user']['alliance_id']=0;
						echo "Die Allianz wurde aufgel&ouml;st!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
					}
					// Allianzdaten anzeigen
					else
					{
						$member_count = mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id='".$s['user']['alliance_id']."' AND user_alliance_application='';"));
						infobox_start("[".stripslashes($arr['alliance_tag'])."] ".stripslashes($arr['alliance_name']),1);
						if ($arr['alliance_img']!="")
						{
							$im = ALLIANCE_IMG_DIR."/".$arr['alliance_img'];
							if (file_exists($im))
							{
								$ims = getimagesize($im);
								echo "<tr><td class=\"tblblack\" colspan=\"3\" style=\"text-align:center;background:#000\">
								<img src=\"".$im."\" alt=\"Allianz-Logo\" style=\"width:".$ims[0]."px;height:".$ims[1]."\" /></td></tr>";
							}
						}

						// Internes Forum verlinken
						if ($myRight['allianceboard'] || $isFounder)
							$pres=dbquery("
							SELECT
								topic_subject,
								post_id,topic_id,
								topic_timestamp,
								post_user_id
							FROM
								".BOARD_POSTS_TABLE.",
								".BOARD_TOPIC_TABLE.",
								".BOARD_CAT_TABLE."
							WHERE
								post_topic_id=topic_id
								AND topic_cat_id=cat_id
								AND cat_alliance_id=".$arr['alliance_id']."
							GROUP BY
								post_id
							ORDER BY
								post_timestamp DESC
							LIMIT 1;");
						else
							$pres=dbquery("
							SELECT
								topic_subject,
								post_id,topic_id,
								topic_timestamp,
								post_user_id
							FROM
								".BOARD_POSTS_TABLE.",
								".BOARD_TOPIC_TABLE.",
								".BOARD_CAT_TABLE.",
								".$db_table['allianceboard_catranks']."
							WHERE
								cr_cat_id=cat_id
								AND cr_rank_id=".$myRankId."
								AND post_topic_id=topic_id
								AND topic_cat_id=cat_id
								AND cat_alliance_id=".$arr['alliance_id']."
							GROUP BY
								post_id
							ORDER BY
								post_timestamp DESC
							LIMIT 1;");
						if (mysql_num_rows($pres)>0)
						{
							$parr=mysql_fetch_row($pres);
							$ps="Neuster Post: <a href=\"?page=allianceboard&amp;topic=".$parr[2]."#".$parr[1]."\"><b>".$parr[0]."</b>, geschrieben von: <b>".get_user_nick($parr[4])."</b>, <b>".df($parr[3])."</b></a>";
						}
						else
							$ps="<i>Noch keine Beitr&auml;ge vorhanden";
						echo "<tr><td class=\"tbltitle\">Internes Forum</td><td class=\"tbldata\" colspan=\"2\"><b><a href=\"?page=allianceboard\">Forum&uuml;bersicht</a></b> &nbsp; $ps</td></tr>";

						// Umfrage verlinken
						$pres=dbquery("SELECT poll_title,poll_question,poll_id FROM ".$db_table['alliance_polls']." WHERE poll_alliance_id=".$arr['alliance_id']." ORDER BY poll_timestamp DESC LIMIT 2;");
						$pcnt=mysql_num_rows($pres);
						if ($pcnt>0)
						{
							$parr=mysql_fetch_array($pres);
							echo "<tr><td class=\"tbltitle\">Umfrage:</td>
							<td class=\"tbldata\" colspan=\"2\"><a href=\"?page=$page&amp;action=viewpoll\"><b>".stripslashes($parr['poll_title']).":</b> ".stripslashes($parr['poll_question'])."</a>";
							if ($pcnt>1)
								echo " &nbsp; (<a href=\"?page=$page&amp;action=viewpoll\">mehr Umfragen</a>)";
							echo "</td></tr>";
						}

						// Bewerbungen anzeigen
						if ($isFounder || $myRight['applications'])
						{
							$bewerb_res = dbquery("SELECT user_id,user_alliance_application FROM ".$db_table['users']." WHERE user_alliance_id='".$s['user']['alliance_id']."';");
							if (mysql_num_rows($bewerb_res)>0)
							{
								$bewerbung = 0;
								while($bewerb_arr=mysql_fetch_array($bewerb_res))
								{
									if($bewerb_arr['user_alliance_application']!='')
										$bewerbung = 1;
								}
								if($bewerbung==1)
								{
									echo "<tr><td class=\"tbltitle\" colspan=\"3\" align=\"center\"><div align=\"center\"><b><a href=\"?page=$page&action=applications\">Es sind Bewerbungen vorhanden!</a></b></div></td></tr>";
								}
							}
						}

						// Bündnissanfragen anzeigen
						if ($isFounder || $myRight['relations'])
						{
							if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id2='".$s['user']['alliance_id']."' AND alliance_bnd_level='0';"))>0)
								echo "<tr>
									<td class=\"tbltitle\" colspan=\"3\" style=\"text-align:center;color:#0f0\">
										<a  style=\"color:#0f0\" href=\"?page=$page&action=relations\">Es sind B&uuml;ndnisanfragen vorhanden!</a>
								</td></tr>";
						}

						// Kriegserklärung anzeigen
						$time=time()-192600;
						if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id2='".$s['user']['alliance_id']."' AND alliance_bnd_level='3' AND alliance_bnd_date>'$time';"))>0)
						if ($isFounder || $myRight['relations'])
							echo "<tr><td class=\"tbltitle\" colspan=\"3\" align=\"center\"><b><div align=\"center\"><a href=\"?page=$page&action=relations\">Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</a></div></b></td></tr>";
						else
							echo "<tr><td class=\"tbltitle\" colspan=\"3\" align=\"center\"><div align=\"center\"><b>Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</b></div></td></tr>";

						// Verwaltung
						$adminBox=array();
						if ($isFounder || $myRight['editdata']) array_push($adminBox,"<a href=\"?page=$page&amp;action=editdata\">Allianz-Daten</a>");
						if ($isFounder || $myRight['editmembers']) array_push($adminBox,"<a href=\"?page=$page&action=editmembers\">Mitglieder verwalten</a>");
						if ($isFounder || $myRight['applicationtemplate']) array_push($adminBox,"<a href=\"?page=$page&action=applicationtemplate\">Bewerbungsvorlage</a>");
						if ($isFounder || $myRight['history']) array_push($adminBox,"<a href=\"?page=$page&action=history\">Geschichte</a>");
						if ($isFounder || $myRight['massmail']) array_push($adminBox,"<a href=\"?page=$page&action=massmail\">Rundmail</a>");
						if ($isFounder || $myRight['ranks']) array_push($adminBox,"<a href=\"?page=$page&action=ranks\">R&auml;nge</a>");
						if ($isFounder || $myRight['alliancenews']) array_push($adminBox,"<a href=\"?page=$page&action=alliancenews\">Allianznews (Rathaus)</a>");
						if ($isFounder || $myRight['relations']) array_push($adminBox,"<a href=\"?page=$page&action=relations\">Diplomatie</a>");
						if ($isFounder || $myRight['polls']) array_push($adminBox,"<a href=\"?page=$page&action=polls\">Umfragen verwalten</a>");
						if ($isFounder || $myRight['liquidate']) array_push($adminBox,"<a href=\"?page=$page&action=liquidate\">Allianz aufl&ouml;sen</a>");
						$cnt=count($adminBox);
						if ($cnt>0)
						{
							echo"<tr><td class=\"tbltitle\" width=\"120\" rowspan=\"".(ceil($cnt/2)+1)."\">Verwaltung:</td>";
							$bcnt=0;
							foreach ($adminBox as $ab)
							{
								if ($bcnt%2==0)
								{
									echo "<tr>";
								}
								
								echo "<td class=\"tbldata\" width=\"50%\"><b>".$ab."</b></td>\n";
								if ($bcnt%2==1)
								{
									echo "</tr>";
								}
								$bcnt++;
							}
							if ($bcnt%2==1)
							echo "<td class=\"tbldata\"></td></tr>";
						}

						// Optionen
						$optionBox=array();
						if ($isFounder || $myRight['viewmembers']) array_push($optionBox,"<td class=\"tbldata\" colspan=\"2\"><b><a href=\"?page=$page&action=viewmembers\">Mitglieder</a></b></td></tr><tr>");
						if (!$isFounder) array_push($optionBox,"<td class=\"tbldata\" colspan=\"2\"><b><a href=\"?page=$page&amp;action=leave\" onclick=\"return confirm('Willst du wirklich aus der Allianz austreten?');\">Aus der Allianz austreten</a></b></td></tr>");
						$cnt=count($optionBox);
						if ($cnt>0)
						{
							echo "<tr><td class=\"tbltitle\" width=\"120\" rowspan=\"".$cnt."\">Optionen:</td>";
							foreach ($optionBox as $ab)
								echo $ab."\n";
						}

						// Letzte Ereignisse anzeigen
						if ($isFounder || $myRight['history'])
						{
							echo "<tr>
								<td class=\"tbltitle\" width=\"120\">Letzte Ereignisse:</td>
								<td class=\"tbldata\" colspan=\"2\">";
							$hres=dbquery("
							SELECT 
								* 
							FROM 
								alliance_history 
							WHERE 
								history_alliance_id=".$s['user']['alliance_id']." 
							ORDER BY 
								history_timestamp DESC
							LIMIT 5;");
							while ($harr=mysql_fetch_array($hres))
							{
								echo "<div style=\"border-bottom:1px solid #fff;\"><b>".df($harr['history_timestamp']).":</b> 
									".text2html($harr['history_text'])."</div>";
							}
							echo "</td></tr>";							
						}						

						// Text anzeigen
						if ($arr['alliance_text']!="")
						{
							echo "<tr><td class=\"tbldata\" colspan=\"3\" style=\"text-align:center\">".text2html($arr['alliance_text'])."</td></tr>\n";
						}
			
						// Kriege
						$wars=dbquery("
						SELECT 
							a1.alliance_tag as a1tag,
							a1.alliance_name as a1name,
							a1.alliance_id as a1id,
							a2.alliance_tag as a2tag,
							a2.alliance_name as a2name,
							a2.alliance_id as a2id,
							alliance_bnd_date as date
						FROM 
							alliance_bnd
						INNER JOIN
							alliances as a1
							ON alliance_bnd_alliance_id1=a1.alliance_id
						INNER JOIN
							alliances as a2
							ON alliance_bnd_alliance_id2=a2.alliance_id
					 	WHERE 
					 		(alliance_bnd_alliance_id1='".$s['user']['alliance_id']."' 
					 		OR alliance_bnd_alliance_id2='".$s['user']['alliance_id']."') 
					 		AND alliance_bnd_level=3
					 	;");
						if (mysql_num_rows($wars)>0)
						{
							
							echo "<tr>
											<td class=\"tbltitle\">Kriege:</td>
											<td class=\"tbldata\" colspan=\"2\">
												<table class=\"tbl\">
													<tr>
														<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
														<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
														<td class=\"tbltitle\" style=\"width:25%;\">Bis</td>
													</tr>";
									while ($war=mysql_fetch_array($wars))
									{
										if ($war['a1id']==$id) 
										{
											$opId = $war['a2id'];
											$opTag = $war['a2tag'];
											$opName = $war['a2name'];
										}
										else
										{
											$opId = $war['a1id'];
											$opTag = $war['a1tag'];
											$opName = $war['a1name'];
										}
										echo "<tr>
														<td class=\"tbldata\">
															[".$opTag."] ".$opName." 
															[<a href=\"?page=$page&amp;id=".$opId."\">Info</a>]
														</td>
														<td class=\"tbldata\">".df($war['date'])."</td>
														<td class=\"tbldata\">".df($war['date']+WAR_DURATION)."</td>
													</tr>";
									}
									echo "</table>
											</td>
										</tr>";
						}
			
			
						// Friedensabkommen
						$wars=dbquery("
						SELECT 
							a1.alliance_tag as a1tag,
							a1.alliance_name as a1name,
							a1.alliance_id as a1id,
							a2.alliance_tag as a2tag,
							a2.alliance_name as a2name,
							a2.alliance_id as a2id,
							alliance_bnd_date as date
						FROM 
							alliance_bnd
						INNER JOIN
							alliances as a1
							ON alliance_bnd_alliance_id1=a1.alliance_id
						INNER JOIN
							alliances as a2
							ON alliance_bnd_alliance_id2=a2.alliance_id
					 	WHERE 
					 		(alliance_bnd_alliance_id1='".$s['user']['alliance_id']."' 
					 		OR alliance_bnd_alliance_id2='".$s['user']['alliance_id']."') 
					 		AND alliance_bnd_level=4
					 	;");
						if (mysql_num_rows($wars)>0)
						{			
							echo "<tr>
											<td class=\"tbltitle\">Friedensabkommen:</td>
											<td class=\"tbldata\" colspan=\"2\">
												<table class=\"tbl\">
													<tr>
														<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
														<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
														<td class=\"tbltitle\" style=\"width:25%;\">Bis</td>
													</tr>";					
									while ($war=mysql_fetch_array($wars))
									{
										if ($war['a1id']==$id) 
										{
											$opId = $war['a2id'];
											$opTag = $war['a2tag'];
											$opName = $war['a2name'];
										}
										else
										{
											$opId = $war['a1id'];
											$opTag = $war['a1tag'];
											$opName = $war['a1name'];
										}
										echo "<tr>
														<td class=\"tbldata\">
															[".$opTag."] ".$opName." 
															[<a href=\"?page=$page&amp;id=".$opId."\">Info</a>]
														</td>
														<td class=\"tbldata\">".df($war['date'])."</td>
														<td class=\"tbldata\">".df($war['date']+PEACE_DURATION)."</td>
													</tr>";				
									}
									echo "</table>
											</td>
										</tr>";
						}						
			
						// Bündnisse
						$wars=dbquery("
						SELECT 
							a1.alliance_tag as a1tag,
							a1.alliance_name as a1name,
							a1.alliance_id as a1id,
							a2.alliance_tag as a2tag,
							a2.alliance_name as a2name,
							a2.alliance_id as a2id,
							alliance_bnd_date as date,
							alliance_bnd_name as name
						FROM 
							alliance_bnd
						INNER JOIN
							alliances as a1
							ON alliance_bnd_alliance_id1=a1.alliance_id
						INNER JOIN
							alliances as a2
							ON alliance_bnd_alliance_id2=a2.alliance_id
					 	WHERE 
					 		(alliance_bnd_alliance_id1='".$s['user']['alliance_id']."' 
					 		OR alliance_bnd_alliance_id2='".$s['user']['alliance_id']."') 
					 		AND alliance_bnd_level=2
					 	;");
						if (mysql_num_rows($wars)>0)
						{				
							echo "<tr>
											<td class=\"tbltitle\">Bündnisse:</td>
											<td class=\"tbldata\" colspan=\"2\">
												<table class=\"tbl\">
													<tr>
														<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
														<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
														<td class=\"tbltitle\" style=\"width:25%;\">Bündnisname</td>
													</tr>";		
			
									while ($war=mysql_fetch_array($wars))
									{
										if ($war['a1id']==$id) 
										{
											$opId = $war['a2id'];
											$opTag = $war['a2tag'];
											$opName = $war['a2name'];
										}
										else
										{
											$opId = $war['a1id'];
											$opTag = $war['a1tag'];
											$opName = $war['a1name'];
										}
										echo "<tr>
														<td class=\"tbldata\">
															[".$opTag."] ".$opName." 
															[<a href=\"?page=$page&amp;id=".$opId."\">Info</a>]
														</td>
														<td class=\"tbldata\">".df($war['date'])."</td>
														<td class=\"tbldata\">".stripslashes($war['name'])."</td>
													</tr>";							
																
									}
									echo "</table>
											</td>
										</tr>";
						}				

						// Besucher
						if ($arr['alliance_visits_ext']>0)
						{
							echo "<tr><td class=\"tbltitle\" width=\"120\">Besucherz&auml;hler:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['alliance_visits_ext'])." Besucher</td></tr>\n";
						}

						// Website
						if ($arr['alliance_url']!="")
						{
							echo "<tr><td class=\"tbltitle\" width=\"120\">Website/Forum:</td><td class=\"tbldata\" colspan=\"2\"><b>".format_link($arr['alliance_url'])."</a></b></td></tr>\n";
						}
						
						// Diverses
						echo "<tr><td class=\"tbltitle\" width=\"120\">Mitglieder:</td><td class=\"tbldata\" colspan=\"2\">$member_count</td></tr>\n";
						echo "<tr><td class=\"tbltitle\" width=\"120\">Gr&uuml;nder:</td><td class=\"tbldata\" colspan=\"2\"><a href=\"?page=userinfo&amp;id=".$arr['alliance_founder_id']."\">".get_user_nick($arr['alliance_founder_id'])."</a></td></tr>";
						echo "\n</table><br/>";
					}
				}
			}
			else
			{
				if ($_POST['resolvefalseallyid']!="")
				{
					dbquery("UPDATE ".$db_table['users']." SET user_alliance_id=0,user_alliance_rank_id=0,user_alliance_application='' WHERE user_id=".$s['user']['id'].";");
					echo "Die fehlerhafte Verkn&uuml;pfung wurde gel&ouml;st!";
				}
				else
					echo "<form action=\"?page=$page\" method=\"post\">Diese Allianz existiert nicht!<br/><br/><input type=\"submit\" name=\"resolvefalseallyid\" value=\"Fehlerhafte Allianzverkn&uuml;pfung l&ouml;schen\" /></form>";
			}
		}
	}
?>
