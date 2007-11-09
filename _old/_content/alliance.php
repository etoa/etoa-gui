<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: stats.php															//
	// Topic: Statistik-Modul				 								//
	// Version: 0.1																	//
	// Letzte Änderung: 01.10.2004									//
	//////////////////////////////////////////////////


	// DEFINITIONEN //
	define(TBL_SPACING,$conf['general_table_offset']['v']);
	define(TBL_PADDING,$conf['general_table_offset']['p1']);
	define(MISC_MSG_CAT_ID,5);
	define(USER_MSG_CAT_ID,1);
	define(ONLINE_TIME,$conf['online_threshold']['v']);
	define(IMG_MAX_WIDTH,800);
	define(IMG_MAX_HEIGHT,600);

	//Bewerbung aktualisieren
	$res=dbquery("
	SELECT
		user_alliance_application,
		user_alliance_id,
		user_alliance_rank_id
	FROM
		".$db_table['users']."
	WHERE
		user_id=".$_SESSION[ROUNDID]['user']['id']."");
		$arr = mysql_fetch_array($res);
    if ($arr['user_alliance_application']!='' && $arr['user_alliance_id']>0)
        $application=1;
    else
        $application=0;
  $myRankId=$arr['user_alliance_rank_id'];

 	$_SESSION[ROUNDID]['user']['alliance_id']=$arr['user_alliance_id'];
 	$_SESSION[ROUNDID]['user']['alliance_application']=$application;


	// BEGIN SKRIPT //
	echo "<h1>Allianz</h1>";

	//
	// Allianzgründung
	//
	if ($_GET['action']=="create")
	{
		echo "<h2>Gr&uuml;ndung einer Allianz</h2>";
		if ($_SESSION[ROUNDID]['user']['alliance_id']==0)
		{
			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			infobox_start("Allianz-Daten",1);
			echo "<tr><td class=\"tbltitle\">Allianz-Tag:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".$_SESSION['alliance_creation']['alliance_tag']."\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Allianz-Name:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".$_SESSION['alliance_creation']['alliance_name']."\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Beschreibung:</td><td class=\"tbldata\"><textarea rows=\"10\" cols=\"50\" name=\"alliance_text\">".$_SESSION['alliance_creation']['alliance_text']."</textarea></td></tr>";
			echo "<tr><td class=\"tbltitle\">Website/Forum:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_url\" size=\"40\" maxlength=\"255\" value=\"".$_SESSION['alliance_creation']['alliance_url']."\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Bildpfad:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_img\" size=\"40\" maxlength=\"255\" value=\"".$_SESSION['alliance_creation']['alliance_img']."\" /> Es d&uuml;rfen keine Copyrights verletzt werden!</td></tr>";
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
	elseif ($_POST['createsubmit']!="" && checker_verify())
	{
		echo "<h2>Gr&uuml;ndung einer Allianz</h2>";
		if ($_SESSION[ROUNDID]['user']['alliance_id']==0)
		{
			$_SESSION['alliance_creation']['alliance_tag']=$_POST['alliance_tag'];
			$_SESSION['alliance_creation']['alliance_name']=$_POST['alliance_name'];
			$_SESSION['alliance_creation']['alliance_text']=$_POST['alliance_text'];
			$_SESSION['alliance_creation']['alliance_url']=$_POST['alliance_url'];
			$_SESSION['alliance_creation']['alliance_img']=$_POST['alliance_img'];

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
				dbquery("INSERT INTO ".$db_table['alliances']." (alliance_tag,alliance_name,alliance_text,alliance_img,alliance_url,alliance_founder_id,alliance_foundation_date) VALUES ('".addslashes($_POST['alliance_tag'])."','".addslashes($_POST['alliance_name'])."','".addslashes($_POST['alliance_text'])."','".$_POST['alliance_img']."','".$_POST['alliance_url']."','".$_SESSION[ROUNDID]['user']['id']."','".time()."');");
				$aid = mysql_insert_id();
				dbquery("UPDATE ".$db_table['users']." SET user_alliance_id='$aid' WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';");
				$_SESSION[ROUNDID]['user']['alliance_id']=$aid;
				add_log(5,"Die Allianz [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b] wurde vom Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] gegr&uuml;ndet!",time());
				add_alliance_history($aid,"Die Allianz [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b] wurde vom Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] gegründet!");
				$_SESSION['alliance_creation']=Null;
				echo "Die Allianz <b>".$_POST['alliance_name']." (".$_POST['alliance_tag'].")</b> wurde gegr&uuml;ndet!<br/><br/>";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur Allianz-&Uuml;bersicht\" />";
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
	elseif ($_GET['action']=="join")
	{
		if ($application==0)
		{
			// Bewerbungstext schreiben
			if (intval($_GET['alliance_id'])>0)
			{
				$res=dbquery("SELECT alliance_id,alliance_tag,alliance_name,alliance_application_template FROM ".$db_table['alliances']." WHERE alliance_id='".intval($_GET['alliance_id'])."'");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					echo "<h2>Bewerbung bei der Allianz [".$arr['alliance_tag']."] ".$arr['alliance_name']."</h2>";
					echo "<form action=\"?page=$page&amp;action=join\" method=\"post\">";
					checker_init();
					infobox_start("Bewerbungstext",1);
					echo "<tr><td class=\"tbltitle\">Nachricht:</td><td class=\"tbldata\"><textarea rows=\"15\" cols=\"80\" name=\"user_alliance_application\">".$arr['alliance_application_template']."</textarea></td>";
					infobox_end(1);
					echo "<input type=\"hidden\" name=\"user_alliance_id\" value=\"".intval($arr['alliance_id'])."\" />";
					echo "<input type=\"submit\" name=\"submitapplication\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=join'\" value=\"Zur&uuml;ck\" /></form>";
				}
				else
					echo "Fehler! Allianzdatensatz nicht gefunden!";
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
					send_msg($alliances[$_POST['user_alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung","Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat sich bei deiner Allianz beworben. Geh auf die Allianzseite für Details!");
					add_alliance_history($_POST['user_alliance_id'],"Der Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] bewirbt sich sich bei der Allianz.");
					dbquery("UPDATE ".$db_table['users']." SET user_alliance_application='".addslashes($_POST['user_alliance_application'])."',user_alliance_id='".$_POST['user_alliance_id']."' WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';");
					$alliances = get_alliance_names();
					echo "Deine Bewerbung bei der Allianz <b>[".$alliances[$_POST['user_alliance_id']]['tag']."] ".$alliances[$_POST['user_alliance_id']]['name']."</b> wurde gespeichert! Die Allianzleitung wurde informiert und wird deine Bewerbung ansehen.";
					echo "<br/><br/><input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
					$application=1;
					$_SESSION[ROUNDID]['user']['alliance_id']=$_POST['user_alliance_id'];
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
					COUNT(users.user_id) AS cnt,
					FLOOR(SUM(users.user_points)/".$conf['points_update']['p2'].") AS pnt,
					SUM(users.user_points) as sup
				FROM
					".$db_table['alliances'].",
					".$db_table['users']."
				WHERE
					users.user_alliance_id=alliances.alliance_id
					AND users.user_alliance_application=''
					AND users.user_show_stats=1
				GROUP BY
					alliances.alliance_id");
				if (mysql_num_rows($res)>0)
				{
					echo "<table width=\"300\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">";
					echo "<tr><td class=\"tbltitle\">Tag</td>
					<td class=\"tbltitle\">Name</td>
					<td class=\"tbltitle\">Mitglieder</td>
					<td class=\"tbltitle\">Punkte</td>
					<td class=\"tbltitle\">Punkteschnitt</td>
					<td class=\"tbltitle\" style=\"width:100px;\">Aktionen</td></tr>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<tr><td class=\"tbldata\">".$arr['alliance_tag']."</td>
						<td class=\"tbldata\">".$arr['alliance_name']."</td>
						<td class=\"tbldata\">".$arr['cnt']."</td>
						<td class=\"tbldata\">".nf($arr['pnt'])."</td>
						<td class=\"tbldata\">".nf(floor($arr['sup']/$arr['cnt']))."</td>
						<td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['alliance_id']."\">Info</a> &nbsp;
						<a href=\"?page=$page&action=join&alliance_id=".$arr['alliance_id']."\">Bewerben</a></td></tr>";
					}
					echo "</table>";
				}
				else
					echo "Es gibt noch keine Allianzen denen man beitreten k&ouml;nnte! <a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.";
			}
		}
		else
			echo "Du hast dich bereits beworben! <br/><br/><input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
	}

	//
	// Allianz-Info anzeigen
	//
	elseif (intval($_GET['info_id'])>0)
	{
		$res = dbquery("
		SELECT
			*
		FROM
			".$db_table['alliances']."
		WHERE
			alliance_id='".intval($_GET['info_id'])."';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			dbquery("UPDATE ".$db_table['alliances']." SET alliance_visits_ext=alliance_visits_ext+1 WHERE alliance_id='".intval($_GET['info_id'])."';");

			$member_count = mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id='".intval($_GET['info_id'])."' AND user_alliance_application='';"));
 			echo "<table width=\"500\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;\">".stripslashes($arr['alliance_tag'])." ".stripslashes($arr['alliance_name'])."</td></tr>";
      if ($arr['alliance_img']!="")
      {
          if($arr['alliance_img_dim']=='%')
          {
		          $img_info = getimagesize($arr['alliance_img']);
              $arr['alliance_img_width']=$img_info[0]/100*$arr['alliance_img_width'];
              $arr['alliance_img_height']=$img_info[1]/100*$arr['alliance_img_height'];
              $arr['alliance_img_dim']='px';
          }
          echo "<tr><td class=\"tblblack\" colspan=\"2\" align=\"center\"><img src=\"".$arr['alliance_img']."\" alt=\"Allianz-Logo\" style=\"width:".$arr['alliance_img_width']."".$arr['alliance_img_dim'].";height:".$arr['alliance_img_height']."".$arr['alliance_img_dim']."\" /></td></tr>";
      }
			if ($arr['alliance_text']!="")
			echo "<tr><td class=\"tbldata\" colspan=\"2\" style=\"text-align:center;\">".text2html($arr['alliance_text'])."</td></tr>";

			$wars=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE (alliance_bnd_alliance_id1='".intval($_GET['info_id'])."' OR alliance_bnd_alliance_id2='".intval($_GET['info_id'])."') AND alliance_bnd_level=3");
			$bnds=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE (alliance_bnd_alliance_id1='".intval($_GET['info_id'])."' OR alliance_bnd_alliance_id2='".intval($_GET['info_id'])."') AND alliance_bnd_level=2");
			$alliances=get_alliance_names();

			//
			//Kriege
			//
			if (mysql_num_rows($wars)>0)
			{
				echo "<tr><td class=\"tbltitle\">Kriege:</td><td class=\"tbldata\"><ul>";

				//Wenn man die eigene Allianz anschauen geht...
				if(intval($_GET['info_id'])==$_SESSION[ROUNDID]['user']['alliance_id'])
				{
                    while ($war=mysql_fetch_array($wars))
                    {
                        if ($war['alliance_bnd_alliance_id1']==$_SESSION[ROUNDID]['user']['alliance_id'])
                        {
                            $war_alliance=$war['alliance_bnd_alliance_id2'];
                        }
                        elseif ($war['alliance_bnd_alliance_id2']==$_SESSION[ROUNDID]['user']['alliance_id'])
                        {
                        	$war_alliance=$war['alliance_bnd_alliance_id1'];
                        }

                        echo "<li>[".$alliances[$war_alliance]['tag']."] ".$alliances[$war_alliance]['name']."</li>";
                    }
                    echo "</ul></td></tr>";
				}
				//...wenn man alle anderen allianz schauen geht
				else
				{
                    while ($war=mysql_fetch_array($wars))
                    {
                        if ($war['alliance_bnd_alliance_id1']==intval($_GET['info_id']))
                        {
                            $war_alliance=$war['alliance_bnd_alliance_id2'];
                        }
                        else
                        {
                            $war_alliance=$war['alliance_bnd_alliance_id1'];
                        }

                        echo "<li>[".$alliances[$war_alliance]['tag']."] ".$alliances[$war_alliance]['name']."</li>";
                    }
                    echo "</ul></td></tr>";
                }
			}

			//
			//Bündnisse
			//
			if (mysql_num_rows($bnds)>0)
			{
				echo "<tr><td class=\"tbltitle\">B&uuml;ndnisse:</td><td class=\"tbldata\"><ul>";

				//Wenn man die eigene Allianz anschauen geht...
				if(intval($_GET['info_id'])==$_SESSION[ROUNDID]['user']['alliance_id'])
				{
                    while ($bnd=mysql_fetch_array($bnds))
                    {
                        if ($bnd['alliance_bnd_alliance_id1']==$_SESSION[ROUNDID]['user']['alliance_id'])
                        {
                            $bnd_alliance=$bnd['alliance_bnd_alliance_id2'];
                        }
                        elseif ($bnd['alliance_bnd_alliance_id2']==$_SESSION[ROUNDID]['user']['alliance_id'])
                        {
                        	$bnd_alliance=$bnd['alliance_bnd_alliance_id1'];
                        }

                        echo "<li>[".$alliances[$bnd_alliance]['tag']."] ".$alliances[$bnd_alliance]['name']."</li>";
                    }
                    echo "</ul></td></tr>";
				}
				else
				{
                    while ($bnd=mysql_fetch_array($bnds))
                    {
                        if ($bnd['alliance_bnd_alliance_id1']==intval($_GET['info_id']))
                        {
                            $bnd_alliance=$bnd['alliance_bnd_alliance_id2'];
                        }
                        else
                        {
                            $bnd_alliance=$bnd['alliance_bnd_alliance_id1'];
                        }

                        echo "<li>[".$alliances[$bnd_alliance]['tag']."] ".$alliances[$bnd_alliance]['name']."</li>";
                    }
                    echo "</ul></td></tr>";
                }
			}

			if ($arr['alliance_url']!="")
				echo "<tr><td class=\"tbltitle\" width=\"120\">Website/Forum:</td><td class=\"tbldata\"><b>".format_link($arr['alliance_url'])."</b></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"120\">Mitglieder:</td><td class=\"tbldata\">$member_count</td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"120\">Gr&uuml;nder:</td><td class=\"tbldata\"><a href=\"?page=userinfo&amp;id=".$arr['alliance_founder_id']."\">".get_user_nick($arr['alliance_founder_id'])."</a></td></tr>";
			echo "</table>";
		}
		else
			echo "Diese Allianz existiert nicht!";
		echo "<br/><br/><input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";
	}
	else
	{

		//
		// Infotext wenn in keiner Allianz
		//
		if ($_SESSION[ROUNDID]['user']['alliance_id']==0 && $application==0)
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
			if ($_GET['action']=="cancelapplication")
			{
				$ares = dbquery("
				SELECT
					user_id,
					user_alliance_application
				FROM
					".$db_table['users']."
				WHERE
					user_id='".$_SESSION[ROUNDID]['user']['id']."'
					AND user_alliance_id!=0;");

				if (mysql_num_rows($ares)>0)
				{
					$aarr = mysql_fetch_array($ares);
					if($aarr['user_alliance_application']!='')
					{
                        $alliances = get_alliance_names();
                        send_msg($alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung zurückgezogen","Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." hat die Bewerbung bei deiner Allianz zurückgezogen!");
                        add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] zieht seine Bewerbung zurück.");
                        dbquery("UPDATE ".$db_table['users']." SET user_alliance_application='',user_alliance_id=0 WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';");
                        $application=0;
                        $_SESSION[ROUNDID]['user']['alliance_id']=0;
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
					AND users.user_id='".$_SESSION[ROUNDID]['user']['id']."';");
				if (mysql_num_rows($appres)>0)
				{
					$apparr = mysql_fetch_array($appres);
					// Bewerbung gelesen
					if($apparr['user_alliance_application']=='')
					{
						$_SESSION[ROUNDID]['user']['alliance_id']=$apparr['user_alliance_id'];
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
				alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
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
						if (mysql_num_rows(dbquery("
                                            SELECT
                                                alliance_rankrights.rr_id
                                            FROM
                                                ".$db_table['alliance_rankrights'].",
                                                ".$db_table['alliance_ranks']."
                                            WHERE
                                                alliance_ranks.rank_id=alliance_rankrights.rr_rank_id
                                                AND alliance_ranks.rank_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id']."
                                                AND alliance_rankrights.rr_right_id=".$rightarr['right_id']."
                                                AND alliance_rankrights.rr_rank_id=".$myRankId.";"))>0)
							$myRight[$rightarr['right_key']]=true;
						else
							$myRight[$rightarr['right_key']]=false;
					}
				}

				// Gründer prüfen
				if ($arr['alliance_founder_id']==$_SESSION[ROUNDID]['user']['id'])
					$isFounder=true;
				else
					$isFounder=false;

				//
				// Allianzdaten ändern
				//
				if ($_GET['action']=="editdata")
				{
					echo "<h2>Allianzdaten &auml;ndern</h2>";
					if ($isFounder || $myRight['editdata'])
					{
						echo "<form action=\"?page=$page\" method=\"post\">";
						checker_init();
						infobox_start("Daten der Info-Seite",1);
						echo "<tr><td class=\"tbltitle\">Allianz-Tag:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".stripslashes($arr['alliance_tag'])."\" /></td></tr>";
						echo "<tr><td class=\"tbltitle\">Allianz-Name:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".stripslashes($arr['alliance_name'])."\" /></td></tr>";
						echo "<tr><td class=\"tbltitle\">Beschreibung:</td><td class=\"tbldata\"><textarea rows=\"10\" cols=\"50\" name=\"alliance_text\">".stripslashes($arr['alliance_text'])."</textarea></td></tr>";
						echo "<tr><td class=\"tbltitle\">Website/Forum:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_url\" size=\"40\" maxlength=\"255\" value=\"".stripslashes($arr['alliance_url'])."\" /></td></tr>";
						echo "<tr><td class=\"tbltitle\">Bildpfad:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_img\" size=\"40\" maxlength=\"255\" value=\"".stripslashes($arr['alliance_img'])."\" /></td></tr>";
						echo "<tr><td class=\"tbltitle\">Bildbreite:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_img_width\" size=\"3\" maxlength=\"3\" value=\"".$arr['alliance_img_width']."\" /> (max ".IMG_MAX_WIDTH." Pixel)</td></tr>";
						echo "<tr><td class=\"tbltitle\">Bildbh&ouml;he:</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_img_height\" size=\"3\" maxlength=\"3\" value=\"".$arr['alliance_img_height']."\" /> (max ".IMG_MAX_HEIGHT." Pixel)</td></tr>";
						echo "<tr><td class=\"tbltitle\">Einheit:</td><td class=\"tbldata\">
						<select name=\"alliance_img_dim\">";

						if ($arr['alliance_img_dim']=="px") echo "<option value=\"px\" selected=\"selected\">Pixel</option>"; else echo "<option value=\"px\">Pixel</option>";
						if ($arr['alliance_img_dim']=="%") echo "<option value=\"%\" selected=\"selected\">Prozent</option>"; else echo "<option value=\"%\">Prozent</option>";
						echo "</select>";

						infobox_end(1);
						echo "<input type=\"submit\" name=\"editsubmit\" value=\"Speichern\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Bewerbungsvorlage bearbeiten
				//
				elseif($_GET['action']=="applicationtemplate")
				{
					echo "<h2>Bewerbungsvorlage bearbeiten</h2>";
					if ($isFounder || $myRight['applicationtemplate'])
					{
						echo "<form action=\"?page=$page\" method=\"post\">";
						checker_init();
						infobox_start("Bewerbungsvorlage",1);
						echo "<tr><td class=\"tbltitle\">Text:</td><td class=\"tbldata\"><textarea rows=\"15\" cols=\"60\" name=\"alliance_application_template\">".stripslashes($arr['alliance_application_template'])."</textarea></td></tr>";
						infobox_end(1);
						echo "<input type=\"submit\" name=\"applicationtemplatesubmit\" value=\"Speichern\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";

				}

				//
				// Umfragen anzeigen
				//
				elseif ($_GET['action']=="viewpoll")
				{
					echo "<h2>Umfragen</h2>";

					if ($_POST['vote_submit']!="" && checker_verify() && $_GET['vote']>0 && $_POST['poll_answer']>0)
					{
						dbquery("UPDATE ".$db_table['alliance_polls']." SET poll_a".$_POST['poll_answer']."_count=poll_a".$_POST['poll_answer']."_count+1 WHERE poll_alliance_id=".$arr['alliance_id']." AND poll_id=".$_GET['vote'].";");
						if (mysql_affected_rows()==1)
						{
							dbquery("INSERT INTO ".$db_table['alliance_poll_votes']." (
								vote_poll_id,
								vote_user_id,
								vote_alliance_id,
								vote_number
							) VALUES (
							'".$_GET['vote']."',
							'".$_SESSION[ROUNDID]['user']['id']."',
							'".$arr['alliance_id']."',
							'".$_POST['poll_answer']."'
							)");
						}
					}

					$pres=dbquery("
					SELECT
						*
					FROM
						".$db_table['alliance_polls']."
					WHERE
						poll_alliance_id=".$arr['alliance_id']."
					ORDER BY
						poll_timestamp DESC;");
					if (mysql_num_rows($pres)>0)
					{
						define("POLL_BAR_WIDTH",120);
						$chk=checker_init();
						while ($parr=mysql_fetch_array($pres))
						{
							$upres=dbquery("
							SELECT
								vote_id
							FROM
								".$db_table['alliance_poll_votes']."
							WHERE
								vote_poll_id=".$parr['poll_id']."
								AND vote_user_id=".$_SESSION[ROUNDID]['user']['id']."
								AND vote_alliance_id=".$arr['alliance_id'].";");
							if (mysql_num_rows($upres)>0 || $parr['poll_active']==0)
							{
								infobox_start(stripslashes($parr['poll_title']),1);
								echo "<tr><th colspan=\"2\" class=\"tbltitle\">".stripslashes($parr['poll_question'])."</th></tr>";
								$num_votes = $parr['poll_a1_count']+$parr['poll_a2_count']+$parr['poll_a3_count']+$parr['poll_a4_count']+$parr['poll_a5_count']+$parr['poll_a6_count']+$parr['poll_a7_count']+$parr['poll_a8_count'];
								for ($x=1;$x<=8;$x++)
								{
									if ($parr['poll_a'.$x.'_text']!="")
									{
										echo "<tr><td class=\"tbldata\">".stripslashes($parr['poll_a'.$x.'_text'])."</td>";
										if ($parr['poll_a'.$x.'_count']>0)
										{
											$p = 100/$num_votes*$parr['poll_a'.$x.'_count'];
											$iw = (POLL_BAR_WIDTH/$num_votes*$parr['poll_a'.$x.'_count'])+1;
										}
										else
										{
											$p = 0;
											$iw = 1;
										}
										$iiw = POLL_BAR_WIDTH-$iw;
										$img = "poll".$x;
										echo "<td class=\"tbldata\" style=\"width:250px;\"><img src=\"images/".$img.".jpg\" width=\"$iw\" height=\"10\" alt=\"Poll\" /><img src=\"images/blank.gif\" width=\"$iiw\" height=\"10\"> ".round($p,2)." % (".$parr['poll_a'.$x.'_count']." Stimmen)</td></tr>";
									}
								}
								infobox_end(1);
							}
							else
							{
								echo "<form action=\"?page=$page&amp;action=".$_GET['action']."&amp;vote=".$parr['poll_id']."\" method=\"post\">";
								echo $chk;
								infobox_start(stripslashes($parr['poll_title']),1);
								echo "<tr><th colspan=\"2\" class=\"tbltitle\">".stripslashes($parr['poll_question'])."</th></tr>";
								for ($x=1;$x<=8;$x++)
								{
									if ($parr['poll_a'.$x.'_text']!="")
										echo "<tr><td class=\"tbldata\"><input type=\"radio\" name=\"poll_answer\" value=\"$x\" /> ".stripslashes($parr['poll_a'.$x.'_text'])."</td>";
								}
								infobox_end(1);
								echo "<input type=\"submit\" value=\"Abstimmen!\" name=\"vote_submit\"></form><br/><br/>";
							}
						}
					}
					else
						echo "<i>Keine Umfragen vorhanden</i><br/><br/>";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Umfragen erstellen / bearbeiten
				//
				elseif($_GET['action']=="polls")
				{
					echo "<h2>Umfragen verwalten</h2>";
					if ($isFounder || $myRight['polls'])
					{
						if ($_GET['pollaction']=="create")
						{
							if ($_POST['pollsubmitnew'] && checker_verify())
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
							if ($created)
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
						elseif ($_GET['edit']>0)
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

								if ($_POST['pollsubmit'] && checker_verify())
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
							if ($_GET['del']>0)
							{
								dbquery("DELETE FROM ".$db_table['alliance_polls']." WHERE poll_id=".$_GET['del']." AND poll_alliance_id=".$arr['alliance_id'].";");
								if (mysql_affected_rows()>0)
								{
									dbquery("DELETE FROM ".$db_table['alliance_poll_votes']." WHERE vote_poll_id=".$_GET['del']." AND vote_alliance_id=".$arr['alliance_id'].";");
									echo "Umfrage wurde gel&ouml;scht!<br/><br/>";
								}
							}
							if ($_GET['deactivate'])
								dbquery("UPDATE ".$db_table['alliance_polls']." SET poll_active=0 WHERE poll_id=".$_GET['deactivate']." AND poll_alliance_id=".$arr['alliance_id'].";");
							if ($_GET['activate'])
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
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";

				}

				//
				// Mitglieder bearbeiten
				//
				elseif ($_GET['action']=="editmembers")
				{
					echo "<h2>Allianzmitglieder</h2>";
					if ($isFounder || $myRight['editmembers'])
					{
						// Ränge laden
						$rres = dbquery("
						SELECT
							rank_name,
							rank_id
						FROM
							".$db_table['alliance_ranks']."
						WHERE
							rank_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
						while ($rarr=mysql_fetch_array($rres))
						{
							$rank[$rarr['rank_id']]=$rarr['rank_name'];
						}
						echo "<form action=\"?page=$page&amp;action=editmembers\" method=\"post\">";

						// Mitgliederänderungen speichern
						if ($_POST['editmemberssubmit']!="" && checker_verify())
						{
							if (count($_POST['user_alliance_rank_id'])>0)
							{
								foreach ($_POST['user_alliance_rank_id'] as $uid=>$rid)
								{
									if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_rank_id!='$rid' AND user_id='$uid';"))>0)
									{
										add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Spieler [b]".get_user_nick($uid)."[/b] bekommt den Rang [b]".$rank[$rid]."[/b].");
										dbquery("UPDATE ".$db_table['users']." SET user_alliance_rank_id='$rid' WHERE user_id='$uid';");
									}
								}
								echo "&Auml;nderungen wurden übernommen!<br/><br/>";
							}
						}

						// Gründer wechseln
						if ($_GET['setfounder']>0 && $isFounder && $_SESSION[ROUNDID]['user']['id']!=$_GET['setfounder'])
						{
							$ures=dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id']." AND user_id=".$_GET['setfounder'].";");
							if (mysql_num_rows($ures)>0)
							{
								dbquery("UPDATE ".$db_table['alliances']." SET alliance_founder_id=".$_GET['setfounder']." WHERE alliance_id=".$arr['alliance_id'].";");
								$arr['alliance_founder_id']=$_GET['setfounder'];
								add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Spieler [b]".get_user_nick($_GET['setfounder'])."[/b] wird vom Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] zum Gründer befördert.");
								add_log(5,"Der Spieler [b]".get_user_nick($_GET['setfounder'])."[/b] wird vom Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] zum Gründer befördert.",time());
								send_msg($_GET['setfounder'],MSG_ALLYMAIL_CAT,"Gründer","Du hast nun die Gründerrechte deiner Allianz!");
								echo "Gründer ge&auml;ndert!<br/><br/>";
							}
							else
								echo "<b>Fehler!</b> User nicht gefunden!<br/><br/>";
						}

						// Mitglied kicken
						if (intval($_GET['kickuser'])>0 && checker_verify())
						{
							$ures = dbquery("
							SELECT
                                users.user_nick,
                                alliances.alliance_name,
                                alliances.alliance_tag,
                                alliances.alliance_id,
                                alliances.alliance_founder_id
							FROM
                                ".$db_table['alliances'].",
                                ".$db_table['users']."
							WHERE
                                users.user_alliance_id=alliances.alliance_id
                                AND users.user_id=".intval($_GET['kickuser'])."
                                AND alliances.alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
							if (mysql_num_rows($res))
							{
								$uarr = mysql_fetch_array($ures);
								echo "Der Spieler wurde aus der Allianz ausgeschlossen!<br/><br/>";
								dbquery("UPDATE ".$db_table['users']." SET user_alliance_rank_id=0,user_alliance_id=0 WHERE user_alliance_id=".$arr['alliance_id']." AND user_id='".intval($_GET['kickuser'])."';");
								send_msg(intval($_GET['kickuser']),MSG_ALLYMAIL_CAT,"Allianzausschluss","Du wurdest aus der Allianz ausgeschlossen!");
								add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Spieler [b]".$uarr['user_nick']."[/b] wurde von [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] aus der Allianz ausgeschlossen!");
								add_log(5,"Der Spieler [b]".$uarr['user_nick']."[/b] wurde von [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] aus der Allianz [b][".$arr['alliance_tag']."] ".$arr['alliance_name']."[/b] ausgeschlossen!",time());
							}
							else
								echo "Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er kein Mitglieder dieser Allianz ist!<br/><br/>";
						}

						checker_init();
						echo "<table class=\"tbl\">";
						echo "<tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Heimatplanet</td><td class=\"tbltitle\">Punkte</td><td class=\"tbltitle\">Rang</td><td class=\"tbltitle\">Angriffe</td><td class=\"tbltitle\">Online</td><td class=\"tbltitle\">Aktionen</td>";
						$ures = dbquery("SELECT u.user_last_online,u.user_id,u.user_points,u.user_nick,p.planet_id,u.user_alliance_rank_id FROM ".$db_table['users']." AS u,".$db_table['planets']." AS p WHERE u.user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND p.planet_user_id=u.user_id AND p.planet_user_main=1 AND u.user_alliance_application='' GROUP BY u.user_id  ORDER BY u.user_points DESC, u.user_nick;");
						while ($uarr = mysql_fetch_array($ures))
						{
							echo "<tr>";
							// Nick, Planet, Punkte
							echo "<td class=\"tbldata\">".$uarr['user_nick']."</td><td class=\"tbldata\">".coords_format2($uarr['planet_id'],1)."</td><td class=\"tbldata\">".nf($uarr['user_points'])."</td>";
							// Rang
							if ($uarr['user_id']==$arr['alliance_founder_id'])
								echo "<td class=\"tbldata\">Gründer</td>";
							else
							{
								echo "<td class=\"tbldata\"><select name=\"user_alliance_rank_id[".$uarr['user_id']."]\">";
								echo "<option value=\"0\">Rang w&auml;hlen...</option>";
								foreach ($rank as $id=>$name)
								{
									echo "<option value=\"$id\"";
									if ($uarr['user_alliance_rank_id']==$id) echo " selected=\"selected\"";
									echo ">".$name."</option>";
								}
								echo "</select></td>";
							}

	            $num=check_fleet_incomming($uarr['user_id']);
	            if ($num>0)
	            {
	                echo "<td style=\"color:#f00;\" align=\"center\">".$num."</td>";
	            } else {
	                echo "<td class=\"tbldata\">-</td>";
	            }

							// Zuletzt online
							if ((time()-$conf['online_threshold']['v']*60) < $uarr['user_last_online'])
								echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
							else
								echo "<td class=\"tbldata\">".date("d.m.Y H:i",$uarr['user_last_online'])."</td>";
							// Aktionen
							echo "<td class=\"tbldata\">";
							if ($_SESSION[ROUNDID]['user']['id']!=$uarr['user_id'])
								echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uarr['user_id']."\">Nachricht</a><br/>";
							echo "<a href=\"?page=userinfo&amp;id=".$uarr['user_id']."\">Profil</a><br/>";
							if ($isFounder && $_SESSION[ROUNDID]['user']['id']!=$uarr['user_id'])
								echo "<a href=\"?page=alliance&amp;action=editmembers&amp;setfounder=".$uarr['user_id']."\" onclick=\"return confirm('Soll der Spieler \'".$uarr['user_nick']."\' wirklich zum Gründer bef&ouml;rdert werden? Dir werden dabei die Gründerrechte entzogen!');\">Gründer</a><br/>";

							if ($_SESSION[ROUNDID]['user']['id']!=$uarr['user_id'] && $uarr['user_id']!=$arr['alliance_founder_id'])
								echo "<a href=\"?page=$page&amp;action=editmembers&amp;kickuser=".$uarr['user_id'].checker_get_link_key()."\" onclick=\"return confirm('Soll ".$arr['user_nick']." wirklich aus der Allianz ausgeschlosen werden?');\">Kicken</a>";
							echo "</td></tr>";
						}
						echo "</table>";
						echo "<br/><br/><input type=\"submit\" name=\"editmemberssubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;
						<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Rundmail
				//
				elseif ($_GET['action']=="massmail")
				{
					echo "<h2>Rundmail</h2>";
					if ($isFounder || $myRight['massmail'])
					{
						// Nachricht senden
						if ($_POST['submit']!="" && checker_verify())
						{
							$ures = dbquery("SELECT user_id FROM users WHERE user_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id']." AND user_id!=".$_SESSION[ROUNDID]['user']['id']." AND user_alliance_application='';");
							if (mysql_num_rows($ures)>0)
							{
								while ($uarr=mysql_fetch_array($ures))
								{
									$subject=addslashes($_POST['message_subject'])."";
									dbquery("INSERT INTO ".$db_table['messages']." (
									message_user_from,
									message_user_to,
									message_timestamp,
									message_cat_id,
									message_subject,
									message_text,
									message_massmail
									) VALUES (
									'".$_SESSION[ROUNDID]['user']['id']."',
									'".$uarr['user_id']."',
									".time().",
									".MSG_ALLYMAIL_CAT.",
									'".$subject."',
									'".addslashes($_POST['message_text'])."',
									1
									);");
								}
								echo "Nachricht wurde gesendet!<br/><br/>";
								echo "<input type=\"button\" value=\"Neue Nachricht schreiben\" onclick=\"document.location='?page=$page&action=massmail'\" /> &nbsp; ";
								echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
							}
							else
							{
								echo "<b>Fehler:</b> Nachricht wurde nicht gesendet, keine Mitglieder vorhanden!<br/><br/>";
								echo "<input type=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=$page'\" />";
							}
						}
						else
						{
							echo "<form action=\"?page=$page&action=massmail\" method=\"POST\">";
							checker_init();
							infobox_start("Nachricht verfassen",1);
							echo "<tr><td class=\"tbltitle\" style=\"width:50px;\">Betreff:</td><td class=\"tbldata\"><input type=\"text\" name=\"message_subject\" value=\"".$_GET['message_subject']."\" size=\"30\" maxlength=\"255\"></td></tr>";
							echo "<tr><td class=\"tbltitle\">Text:</td><td class=\"tbldata\"><textarea name=\"message_text\" rows=\"5\" cols=\"50\"></textarea></td></tr>";
							infobox_end(1);
							echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" /> &nbsp;<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
							echo "</form>";
						}
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Ränge
				//
				elseif ($_GET['action']=="ranks")
				{
					echo "<h2>R&auml;nge</h2>";
					if ($isFounder || $myRight['ranks'])
					{
						// Ränge speichern
						if (count($_POST)>0 && checker_verify())
						{
							if($_POST['ranknew']!="")
								dbquery("INSERT INTO ".$db_table['alliance_ranks']." (rank_alliance_id) VALUES (".$_SESSION[ROUNDID]['user']['alliance_id'].");");
							if(($_POST['ranksubmit']!="" || $_POST['ranknew']!=""))
							{
								if (count($_POST['rank_name'])>0)
								{
									foreach ($_POST['rank_name'] as $id=>$name)
									{
										dbquery("DELETE FROM ".$db_table['alliance_rankrights']." WHERE rr_rank_id=$id;");
										if ($_POST['rank_del'][$id]==1)
										{
											dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_id=$id;");
										}
										else
										{
											dbquery("UPDATE ".$db_table['alliance_ranks']." SET rank_name='".$name."' WHERE rank_id=$id;");
											foreach ($_POST['rankright'][$id] as $rid=>$rv)
											{
												dbquery("INSERT INTO ".$db_table['alliance_rankrights']." (rr_right_id,rr_rank_id) VALUES ($rid,$id);");
											}
										}
									}
								}
							}
						}
						echo "<form action=\"?page=$page&action=ranks\" method=\"post\">";
						checker_init();

						$rankres=dbquery("SELECT rank_name,rank_id,rank_level FROM ".$db_table['alliance_ranks']." WHERE rank_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
						if (mysql_num_rows($rankres)>0)
						{
							infobox_start("Verf&uuml;gbare R&auml;nge",1);
							echo "<tr><td class=\"tbltitle\">Rangname</td><td class=\"tbltitle\">Rechte</td><td class=\"tbltitle\">L&ouml;schen</td></tr>";
							while ($arr = mysql_fetch_array($rankres))
							{
								echo "<tr><td class=\"tbldata\"><input type=\"text\" name=\"rank_name[".$arr['rank_id']."]\" value=\"".$arr['rank_name']."\" />";
								echo "<td class=\"tbldata\">";
								foreach ($rights as $k=>$v)
								{
									echo "<input type=\"checkbox\" name=\"rankright[".$arr['rank_id']."][".$k."]\" value=\"1\" ";
									$rrres=dbquery("SELECT rr_id FROM ".$db_table['alliance_rankrights']." WHERE rr_right_id=".$k." AND rr_rank_id=".$arr['rank_id'].";");
									if (mysql_num_rows($rrres)>0)
										echo " checked=\"checked\" /><span style=\"color:#0f0;\">".$v['desc']."</span><br/>";
									else
										echo" /> <span style=\"color:#f50;\">".$v['desc']."</span><br/>";
								}
								echo "</td>";

								echo "<td class=\"tbldata\"><input type=\"checkbox\" name=\"rank_del[".$arr['rank_id']."]\" value=\"1\" /></td></tr>";
							}
							infobox_end(1);
							echo "<input type=\"submit\" name=\"ranksubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
						}
						else
							echo "<i>Keine R&auml;nge vorhanden!</i><br/><br/>";
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"ranknew\" value=\"Neuer Rang\" /></form>";
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Bewerbungen
				//
				elseif ($_GET['action']=="applications")
				{
					echo "<h2>Bewerbungen</h2>";
					if ($isFounder || $myRight['applications'])
					{
						if($_POST['applicationsubmit']!="" && checker_verify())
						{
							if (count($_POST['application_answer'])>0)
							{
								foreach ($_POST['application_answer'] as $id=>$answer)
								{
									if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND user_alliance_application!='' && user_id='$id';"))>0)
									{
										$nick = get_user_nick($id);
										if ($answer==1)
										{
											$allys = get_alliance_names();
											echo "Der Spieler <b>$nick</b> wurde aufgenommen!<br/>";
											send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung angenommen","Deine Allianzbewerbung wurde angnommen!\n\n".addslashes($_POST['application_answer_text'][$id]));
											add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Die Bewerbung von [b]".$nick."[/b] wurde akzeptiert!");
											add_log(5,"Der Spieler [b]".$nick."[/b] tritt der Allianz [b][".$allys[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$allys[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] bei!",time());
											dbquery("UPDATE ".$db_table['users']." SET user_alliance_application=NULL WHERE user_id='$id';");
										}
										else
										{
											echo "Der Spieler <b>$nick</b> wurde nicht aufgenommen!<br/>";
											send_msg($id,MSG_ALLYMAIL_CAT,"Bewerbung abgelehnt","Deine Allianzbewerbung wurde abgelehnt!\n\n".addslashes($_POST['application_answer_text'][$id]));
											add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Die Bewerbung von [b]".$nick."[/b] wurde abgelehnt!");
											dbquery("UPDATE ".$db_table['users']." SET user_alliance_id=0,user_alliance_application=NULL WHERE user_id='$id';");
										}
									}
								}
								echo "<br/>";
							}
						}
						echo "<form action=\"?page=$page&action=applications\" method=\"post\">";
						checker_init();
						$res = dbquery("
						SELECT
	                        user_id,
                            user_alliance_application,
                            user_points,
                            user_registered
						FROM
							".$db_table['users']."
						WHERE
							user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."'
							AND user_alliance_application!='';");
						if (mysql_num_rows($res)>0)
						{
							infobox_start("Bewerbungen prüfen",1);
							echo "<tr><td class=\"tbltitle\">User</td><td class=\"tbltitle\">Text</td><td class=\"tbltitle\">Punkte</td><td class=\"tbltitle\">Registrierdatum</td><td class=\"tbltitle\">Aktion</td></tr>";
							while ($arr = mysql_fetch_array($res))
							{
								echo "<tr><td class=\"tbldata\">".get_user_nick($arr['user_id'])."</td>";
								echo "<td class=\"tbldata\">".text2html($arr['user_alliance_application'])."</td>";
								echo "<td class=\"tbldata\">".nf($arr['user_points'])."</td>";
								echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_registered'])."</td>";
								echo "<td class=\"tbldata\">Antwort:<br/><textarea rows=\"5\" cols=\"30\" name=\"application_answer_text[".$arr['user_id']."]\" /></textarea><br/>
								Bewerbung annehmen: <input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"1\" checked=\"checked\" /> Ja
								<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"0\" /> Nein</td></tr>";
							}
							infobox_end(1);
							echo "<input type=\"submit\" name=\"applicationsubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
						}
						else
							echo "<i>Keine Bewerbungen vorhanden!</i><br/><br/>";
						echo "<input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" /></form>";
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Allianz auflösen bestätigen
				//
				elseif ($_GET['action']=="liquidate")
				{
					echo "<h2>Allianz aufl&ouml;sen</h2>";
					if ($isFounder || $myRight['liquidate'])
					{
						if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND user_id!='".$_SESSION[ROUNDID]['user']['id']."';"))>0)
							echo "Allianz kann nicht aufgel&ouml;st werden, da sie noch Mitglieder hat. L&ouml;sche zuerst die Mitglieder!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
						else
						{
							echo "<form action=\"?page=$page\" method=\"post\">";
							echo "<input name=\"id_control\" type=\"hidden\" value=\"".$_SESSION[ROUNDID]['user']['alliance_id']."\" />";
							checker_init();
							echo "Willst du die Allianz wirklich aufl&ouml;sen?<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Nein\" />&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"liquidatesubmit\" value=\"Ja\" />";
							echo "</form>";
						}
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Allianz-News
				//
				elseif ($_GET['action']=="alliancenews")
				{
					echo "<h2>Allianznews</h2>";
					if ($isFounder || $myRight['alliancenews'])
					{
						if (($_POST['newssubmit']!="" || $_POST['newssubmitsend']!="") && checker_verify())
						{
							if (check_illegal_signs($_POST['news_title'])!="")
							{
								echo "<div style=\"color:red;\"><b>Fehler:</b> Ungültige Zeichen (".check_illegal_signs($_POST['news_title']).") im Newstitel!!</div><br/>";
								$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
								$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
								$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
							}
							elseif ($_POST['newssubmitsend']!="" && $_POST['news_text']!="" && $_POST['alliance_id']>0 && $_SESSION['alliance']['news']['preview'])
							{
								$_SESSION['alliance']['news']=Null;
								dbquery("INSERT INTO ".$db_table['alliance_news']."
								(alliance_news_alliance_id,
								alliance_news_user_id,
								alliance_news_title,
								alliance_news_text,
								alliance_news_date,
								alliance_news_alliance_to_id,
								alliance_news_public)
								VALUES
								(".$_SESSION[ROUNDID]['user']['alliance_id'].",
								".$_SESSION[ROUNDID]['user']['id'].",
								'".addslashes($_POST['news_title'])."',
								'".addslashes($_POST['news_text'])."',
								".time().",
								".$_POST['alliance_id'].",
								".$_POST['public'].")");
								echo "<div style=\"color:#0f0;\">News wurde gesendet!</div><br/>";
								$_SESSION['alliance']['news']=null;
							}
							elseif ($_POST['news_title']!="" && $_POST['news_text']!="" && $_POST['alliance_id']>0)
							{
								$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
								$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
								$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
								infobox_start("Vorschau - ".$_POST['news_title']);
								echo text2html($_POST['news_text']);
								infobox_end();
								$_SESSION['alliance']['news']['preview']=true;
							}
							else
							{
								$_SESSION['alliance']=array();
								$_SESSION['alliance']['news']=array();
								$_SESSION['alliance']['news']['news_title']=$_POST['news_title'];
								$_SESSION['alliance']['news']['news_text']=$_POST['news_text'];
								$_SESSION['alliance']['news']['alliance_id']=$_POST['alliance_id'];
								echo "<div style=\"color:red;\"><b>Fehler:</b> Nicht alle Felder ausgefüllt!</div><br/>";
							}
						}

						echo "<form action=\"?page=$page&action=".$_GET['action']."\" method=\"post\">";
						checker_init();
						if ($_GET['message_subject']!="") $_SESSION['alliance']['news']['news_title']=$_GET['message_subject'];
						infobox_start("Neue Allianzenews",1);
						$aid=$_SESSION['alliance']['news']['alliance_id'];
						if ($aid==0) $aid=$_SESSION[ROUNDID]['user']['alliance_id'];
						echo "<tr><th class=\"tbldata\" colspan=\"3\">Sende diese Nachricht nur ab, wenn du dir bezüglich der Ratshausreglen sicher bist! Eine Missachtung kann zur Sperrung des Accounts führen!</th></tr>";
						echo "<tr><td class=\"tbltitle\" width=\"170\">Betreff:</td><td class=\"tbldata\" colspan=\"2\"><input type=\"text\" name=\"news_title\" value=\"".$_SESSION['alliance']['news']['news_title']."\" size=\"62\" maxlength=\"255\"></td></tr>";
						echo "<tr><td class=\"tbltitle\" width=\"170\">Text:</td><td class=\"tbldata\" colspan=2><textarea name=\"news_text\" rows=\"18\" cols=\"60\">".$_SESSION['alliance']['news']['news_text']."</textarea></td></tr>";
						echo "<tr><td class=\"tbltitle\" width=\"170\">Adressierte Allianz:</td><td class=\"tbldata\" colspan=2><select name=\"alliance_id\">";
						$alliance=dbquery("
						SELECT
                            alliance_id,
                            alliance_tag,
                            alliance_name
						FROM
							".$db_table['alliances']."");
						while ($alliances=mysql_fetch_array($alliance))
						{
							echo "<option value=\"".$alliances['alliance_id']."\"";
							if ($alliances['alliance_id']==$aid) echo " selected=\"selected\"";
							echo ">[".$alliances['alliance_tag']."]  ".$alliances['alliance_name']."</option>";
						}
						echo "</select></td></tr>";
						echo "<tr><td class=\"tbltitle\" width=\"50\">&Ouml;ffentlich?</td>";
						echo "<td class=\"tbldata\"><input type=\"radio\" name=\"public\" value=\"1\" checked=\"checked\" />JA, für alle lesbar</td><td class=\"tbldata\"><input type=\"radio\" name=\"public\" value=\"0\" />NEIN, nur für die betreffende Allianz lesbar</td>";
						infobox_end(1);
						if ($_SESSION['alliance']['news']['preview'])
							echo "<input type=\"submit\" name=\"newssubmitsend\" value=\"Senden\"> &nbsp; ";
						echo "<input type=\"submit\" name=\"newssubmit\" value=\"Vorschau\">";
						echo " &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
						echo "</form>";
					}
					else
						echo "<b>Fehler:</b> Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Bündnissanfragen
				//
				elseif ($_GET['action']=="bnd_ask")
				{
					echo "<h2>B&uuml;ndnissanfragen</h2>";

					if ($isFounder || $myRight['relations'])
					{
						$alliances = get_alliance_names();
						if($_POST['bndsubmit']!="" && count($_POST['bnd_answer'])>0 && checker_verify())
						{
							foreach ($_POST['bnd_answer'] as $alliance_id=>$answer)
							{
								if (mysql_num_rows(dbquery("
								SELECT
								alliance_bnd_id
								FROM
								".$db_table['alliance_bnd']."
								WHERE
								((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$alliance_id.")
									OR
								(alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$alliance_id."))
								AND (alliance_bnd_level=2 OR alliance_bnd_level=3);"))==0)
								{
									$founder=mysql_fetch_array(dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id=".$alliance_id.";"));
									if ($answer==1)
									{
										send_msg($founder['alliance_founder_id'],MSG_ALLYMAIL_CAT,"B&uuml;ndnis angenommen","Das B&uuml;ndnis zwischen den Allianzen [b][".$alliances[$alliance_id]['tag']."] ".$alliances[$alliance_id]['name']."[/b] und [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] ist zustande gekommen!");
										dbquery("UPDATE ".$db_table['alliance_bnd']." SET alliance_bnd_level='2' WHERE alliance_bnd_alliance_id1=".$alliance_id." AND alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
										add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Die Allianzen [b][".$alliances[$alliance_id]['tag']."] ".$alliances[$alliance_id]['name']."[/b] und [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] schliessen ein B&uuml;ndnis!");
										add_alliance_history($alliance_id,"Die Allianzen [b][".$alliances[$alliance_id]['tag']."] ".$alliances[$alliance_id]['name']."[/b] und [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] schliessen ein B&uuml;ndnis!");
									}
									else
									{
										send_msg($founder['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Bewerbung abgelehnt","Die B&uuml;ndnisanfrage wurde von der Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] abgelehnt!");
										dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id1=".$alliance_id." AND alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
										add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Die B&uuml;ndnisanfrage der Allianz [b][".$alliances[$alliance_id]['tag']."] ".$alliances[$alliance_id]['name']."[/b] wird abgelehnt!");
										add_alliance_history($alliance_id,"Die B&uuml;ndnisanfrage wird von der Allianz Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] abgelehnt!");
									}
									echo "Anfrage der Allianz <b>[".$alliances[$alliance_id]['tag']."] ".$alliances[$alliance_id]['name']."</b> bearbeitet!<br/>";
								}
							}
							echo "<br/>&Auml;nderungen übernommen!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
						}
						else
						{
							$bres = dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND alliance_bnd_level='0';");
							if (mysql_num_rows($bres)>0)
							{
								echo "<form action=\"?page=$page&action=bnd_ask\" method=\"post\">";
								checker_init();
								infobox_start("Offene Anfragen",1);
								echo "<tr><td class=\"tbltitle\">Allianz</td><td class=\"tbltitle\">B&uuml;ndnissanfrage</td><td class=\"tbltitle\">Annehmen</td><td class=\"tbltitle\">Ablehnen</td></tr>";
								while ($barr = mysql_fetch_array($bres))
								{
									if ($barr['allianc_bnd_alliance_id1']==$_SESSION[ROUNDID]['user']['alliance_id']) $barr['allianc_bnd_alliance_id1']=$barr['allianc_bnd_alliance_id2'];
									echo "<tr><td class=\"tbldata\">".$alliances[$barr['alliance_bnd_alliance_id1']]['tag']."</td><td class=\"tbldata\">".text2html($barr['alliance_bnd_text'])."</td>";
									echo "<td class=\"tbldata\"><input type=\"radio\" name=\"bnd_answer[".$barr['alliance_bnd_alliance_id1']."]\" value=\"1\" checked=\"checked\" /></td>";
									echo "<td class=\"tbldata\"><input type=\"radio\" name=\"bnd_answer[".$barr['alliance_bnd_alliance_id1']."]\" value=\"0\" /></td></tr>";
								}
								infobox_end(1);
								echo "<input type=\"submit\" name=\"bndsubmit\" value=\"&Uuml;bernehmen\" /> <input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" /></form>";
							}
							else
								echo "Keine B&uuml;ndnisanfragen vorhanden!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
						}
					}
					else
						echo "Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
				}


				//
				// Büdniss/Kriegs- Text ansehen
				//
				elseif ($_GET['action']=="bnd_text")
				{
					if ($isFounder || $myRight['relations'])
					{
						$id=$_GET['id'];
						$res = dbquery("SELECT alliance_bnd_text FROM ".$db_table['alliance_bnd']." WHERE (alliance_bnd_alliance_id1='".$_SESSION[ROUNDID]['user']['alliance_id']."' OR alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."') AND (alliance_bnd_alliance_id1='$id' OR alliance_bnd_alliance_id2='$id');");
						$arr = mysql_fetch_array($res);

						infobox_start("Text",1);
						echo "<tr><td class=\"tbldata\">".$arr['alliance_bnd_text']."</td></tr>";
						infobox_end(1);
					}
					else
						echo "Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Büdniss Anfrage löschen (bevor bnd angenommen ist)
				//
				elseif ($_GET['action']=="bnd_cancel")
				{
					if ($isFounder || $myRight['relations'])
					{
						$alliances = get_alliance_names();
						$id=$_GET['id'];
						$res = dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE (alliance_bnd_alliance_id1='".$_SESSION[ROUNDID]['user']['alliance_id']."' OR alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."') AND (alliance_bnd_alliance_id1='$id' OR alliance_bnd_alliance_id2='$id');");
						$arr = mysql_fetch_array($res);

						if(mysql_num_rows($res)>0)
						{
							dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_id='".$arr['alliance_bnd_id']."';");
							echo "Anfrage gel&ouml;scht<br><br>";
							echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=relations'\" value=\"Zur&uuml;ck\" />";
							//Nachricht an den Leader der gegnerischen Allianz schreiben
							$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='$id'");
		          $arr=mysql_fetch_array($res);
		   				send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Anfrage zurückgenommen","Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] hat ihre Büdnisanfrage wieder zurückgezogen.");
						}
					}
					else
						echo "Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Bündniss-/Kriegspartner wählen
				//
				elseif ($_GET['action']=="relations")
				{
					echo "<h2>B&uuml;ndnisse / Kriege</h2>";

					if ($isFounder || $myRight['relations'])
					{
						$alliances = get_alliance_names();

						// Kriegserklärung schreiben
						if ($_GET['begin_war']>0)
						{
							$ares=dbquery("SELECT * FROM ".$db_table['alliances']." WHERE alliance_id='".$_GET['begin_war']."';");
							if (mysql_num_rows($ares)>0 && $_GET['begin_bnd']!=$_SESSION[ROUNDID]['user']['alliance_id'])
							{
								$aarr=mysql_fetch_array($ares);
								echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\">";
								checker_init();
								infobox_start("Kriegserkl&auml;rung an die Allianz [".$aarr['alliance_tag']."] ".$aarr['alliance_name']);
								echo "<textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea>";
								infobox_end();
								echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"".$aarr['alliance_id']."\" />";
								echo "<input type=\"submit\" name=\"sbmit_new_war\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
								echo "</form>";
							}
							else
								echo "<b>Fehler:</b> Diese Allianz existiert nicht!<br/><br/>";
						}

						// Bündnisanfrage schreiben
						elseif ($_GET['begin_bnd']>0)
						{
							$ares=dbquery("
							SELECT
								alliance_id,
								alliance_tag,
								alliance_name
							FROM
								".$db_table['alliances']."
							WHERE
								alliance_id='".$_GET['begin_bnd']."';");
							if (mysql_num_rows($ares)>0 && $_GET['begin_bnd']!=$_SESSION[ROUNDID]['user']['alliance_id'])
							{
								$aarr=mysql_fetch_array($ares);
								echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\">";
								checker_init();
								infobox_start("B&uuml;ndnisanfrage an die Allianz [".$aarr['alliance_tag']."] ".$aarr['alliance_name']);
								echo "<textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea>";
								infobox_end();
								echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"".$aarr['alliance_id']."\" />";
								echo "<input type=\"submit\" name=\"sbmit_new_bnd\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
								echo "</form>";
							}
							else
								echo "<b>Fehler:</b> Diese Allianz existiert nicht!<br/><br/>";
						}

						// Beziehungsübersicht anzeigen
						else
						{
								// Bündnis speichern
								if ($_POST['sbmit_new_bnd']!="" && checker_verify())
								{
									if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE ((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$_POST['alliance_bnd_alliance_id'].") OR (alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$_POST['alliance_bnd_alliance_id'].")) AND alliance_bnd_level>0"))>0)
										echo "Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!<br/><br/>";
									else
									{
										dbquery("INSERT INTO ".$db_table['alliance_bnd']." (alliance_bnd_alliance_id1,alliance_bnd_alliance_id2,alliance_bnd_level,alliance_bnd_text,alliance_bnd_date) VALUES (".$_SESSION[ROUNDID]['user']['alliance_id'].",".$_POST['alliance_bnd_alliance_id'].",'0','".$_POST['alliance_bnd_text']."',".time().")");
										echo "Du hast einer Allianz erfolgreich ein B&uuml;ndnis angeboten!<br/><br/>";

										//Nachricht an den Leader der gegnerischen Allianz schreiben
										$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_POST['alliance_bnd_alliance_id']."'");
										$arr=mysql_fetch_array($res);

										send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"B&uuml;ndnis anfrage","Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] fragt euch für ein B&uuml;ndnis an.\n
										Geschrieben von [b]".$_SESSION[ROUNDID]['user']['nick']."[/b].\n Geh auf die Allianzseite für mehr Details!");
									}
								}

								// Krieg speichern
								if ($_POST['sbmit_new_war']!="" && checker_verify())
								{
									$alliances = get_alliance_names();
									if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE ((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$_POST['alliance_bnd_alliance_id'].") OR (alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$_POST['alliance_bnd_alliance_id'].")) AND alliance_bnd_level>0"))>0)
										echo "Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!<br/><br/>";
									else
									{
										dbquery("INSERT INTO ".$db_table['alliance_bnd']." (alliance_bnd_alliance_id1,alliance_bnd_alliance_id2,alliance_bnd_level,alliance_bnd_text,alliance_bnd_date) VALUES (".$_SESSION[ROUNDID]['user']['alliance_id'].",".$_POST['alliance_bnd_alliance_id'].",'3','".$_POST['alliance_bnd_text']."',".time().")");
										echo "Du hast einer Allianz den Krieg erkl&auml;rt!<br/><br/>";
										add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Allianz [b][".$alliances[$_POST['alliance_bnd_alliance_id']]['tag']."] ".$alliances[$_POST['alliance_bnd_alliance_id']]['name']."[/b] wird der Krieg erkl&auml;rt!");
										add_alliance_history($_POST['alliance_bnd_alliance_id'],"Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] erkl&auml;rt den Krieg!");

										//Nachricht an den Leader der gegnerischen Allianz schreiben
										$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_POST['alliance_bnd_alliance_id']."'");
										$arr=mysql_fetch_array($res);

										send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Kriegserklärung","Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] erklärt euch den Krieg!\n
										Die Kriegserklärung wurde von [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] geschrieben.\n Geh auf die Allianzseite für mehr Details!");
									}
								}

								// Bündnis beenden
								if ($_GET['end_bnd']>0)
								{
									#$alliances = get_alliance_names();
									$res=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE ((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$_GET['end_bnd'].") OR (alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$_GET['end_bnd'].")) AND alliance_bnd_level=2");
									if (mysql_num_rows($res)==1)
									{
										dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE ((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$_GET['end_bnd'].") OR (alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$_GET['end_bnd']."));");
										echo "Das B&uuml;ndnis mit der ausgew&auml;hlten Allianz wurde aufgel&ouml;st!<br/><br/>";
										add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Das B&uuml;ndnis mit der Allianz [b][".$alliances[$_GET['end_bnd']]['tag']."] ".$alliances[$_GET['end_bnd']]['name']."[/b] wird aufgelöst!");
										add_alliance_history($_GET['end_bnd'],"Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] löst das B&uuml;ndnis auf!");

										//Nachricht an den Leader der gegnerischen Allianz schreiben
										$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_GET['end_bnd']."'");
										$arr=mysql_fetch_array($res);

										send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"B&uuml;ndnis beendet","Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] beendet ihr B&uuml;ndnis mit eurer Allianz!\n
										Ausgelöst von [b]".$_SESSION[ROUNDID]['user']['nick']."[/b].");
									}
									else
										echo "<b>Fehler:</b> B&uuml;ndnis kann nicht aufgel&ouml;st werden, es existiert gar kein B&uuml;ndnis mit dieser Allianz!<br/><br/>";
								}

								// Krieg beenden
								if ($_GET['end_war']>0)
								{
									$res=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE ((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$_GET['end_war'].") OR (alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$_GET['end_war'].")) AND alliance_bnd_level=3");
									if (mysql_num_rows($res)==1)
									{
										dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE ((alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id2=".$_GET['end_war'].") OR (alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." AND alliance_bnd_alliance_id1=".$_GET['end_war']."));");
										echo "Der Krieg mit der ausgew&auml;hlten Allianz wurde beendet!<br/><br/>";
										add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Krieg mit der Allianz [b][".$alliances[$_GET['end_war']]['tag']."] ".$alliances[$_GET['end_war']]['name']."[/b] wird beendet!");
										add_alliance_history($_GET['end_war'],"Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] beendet den Krieg!");

										//Nachricht an den Leader der gegnerischen Allianz schreiben
										$res=dbquery("SELECT alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_GET['end_war']."'");
										$arr=mysql_fetch_array($res);

										send_msg($arr['alliance_founder_id'],MSG_ALLYMAIL_CAT,"Krieg beendet","Die Allianz [b][".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] beendet den Krieg mit eurer Allianz!\n
										Ausgelöst von [b]".$_SESSION[ROUNDID]['user']['nick']."[/b].");
									}
									else
										echo "<b>Fehler:</b> Krieg kann nicht beendet werden, es wird mit dieser Allianz gar kein Krieg geführt!<br/><br/>";
								}


							// Beziehungen laden
							$bres=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id1='".$_SESSION[ROUNDID]['user']['alliance_id']."' OR alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
							$relations=array();
							if (mysql_num_rows($bres)>0)
							{
								while($barr=mysql_fetch_array($bres))
								{
									if ($barr['alliance_bnd_alliance_id1']==$_SESSION[ROUNDID]['user']['alliance_id'])
									{
										$relations[$barr['alliance_bnd_alliance_id2']]['level']=$barr['alliance_bnd_level'];
										$relations[$barr['alliance_bnd_alliance_id2']]['date']=$barr['alliance_date'];
										$relations[$barr['alliance_bnd_alliance_id2']]['text']=$barr['alliance_text'];
									}
									else
									{
										$relations[$barr['alliance_bnd_alliance_id1']]['level']=$barr['alliance_bnd_level'];
										$relations[$barr['alliance_bnd_alliance_id1']]['date']=$barr['alliance_date'];
										$relations[$barr['alliance_bnd_alliance_id1']]['text']=$barr['alliance_text'];
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
								alliance_id!='".$_SESSION[ROUNDID]['user']['alliance_id']."'
							ORDER BY
								alliance_tag,
								alliance_name;");
							if (mysql_num_rows($ares)>0)
							{
								infobox_start("&Uuml;bersicht",1);
								echo "<tr><td class=\"tbltitle\">Allianz</td><td class=\"tbltitle\">Status</td><td class=\"tbltitle\">Aktionen</td></tr>";
								while ($aarr=mysql_fetch_array($ares))
								{
									echo "<tr><td class=\"tbldata\">[".$aarr['alliance_tag']."] ".$aarr['alliance_name']."</td>";
									if ($relations[$aarr['alliance_id']]['level']==2)
									{
										echo "<td class=\"tbldata\" style=\"color:#0f0;\">B&uuml;ndnis</td>";
									}
									elseif ($relations[$aarr['alliance_id']]['level']==3)
									{
										echo "<td class=\"tbldata\" style=\"color:#f00;\">Krieg</td>";
									}
									elseif ($relations[$aarr['alliance_id']]['level']==0 && count($relations[$aarr['alliance_id']])>0)
									{
										echo "<td class=\"tbldata\" style=\"color:#ff0;\">Anfrage</td>";
									}
									else
									{
										echo "<td class=\"tbldata\">-</td>";
									}
									echo "<td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$aarr['alliance_id']."\">Info</a> ";

										if ($relations[$aarr['alliance_id']]['level']==2)
										{
											echo "<a href=\"?page=$page&action=relations&amp;end_bnd=".$aarr['alliance_id']."\">B&uuml;ndnis aufl&ouml;sen</a> ";
											echo "<a href=\"?page=$page&action=bnd_text&id=".$aarr['alliance_id']."\">B&uuml;ndnis Text</a> ";
										}
										elseif ($relations[$aarr['alliance_id']]['level']==3)
										{
											echo "<a href=\"?page=$page&action=relations&amp;end_war=".$aarr['alliance_id']."\">Krieg beenden</a> ";
											echo "<a href=\"?page=$page&action=bnd_text&id=".$aarr['alliance_id']."\">Kriegserkl&auml;rung</a> ";
										}
										elseif ($relations[$aarr['alliance_id']]['level']==0 && count($relations[$aarr['alliance_id']])>0)
										{
											echo "<a href=\"?page=$page&action=bnd_cancel&id=".$aarr['alliance_id']."\">Anfrage zurücknehmen</a> ";
										}
										else
										{
											echo "<a href=\"?page=$page&action=relations&amp;begin_bnd=".$aarr['alliance_id']."\">B&uuml;ndnis</a> ";
											echo "<a href=\"?page=$page&action=relations&amp;begin_war=".$aarr['alliance_id']."\">Krieg</a> ";
										}

									echo "</td></tr>";
								}
								infobox_end(1);
							}
							else
								echo "Es gibt noch keine Allianzen, welcher du den Krieg erkl&auml;ren kannst.<br/><br/>";
							echo "<input type=\"button\" value=\"Zur&uuml;ck zur Hauptseite\" onclick=\"document.location='?page=$page'\" />";

						}
					}
					else
						echo "Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Geschichte anzeigen
				//
				elseif ($_GET['action']=="history")
				{
					echo "<h2>Allianzgeschichte</h2>";
					if ($isFounder || $myRight['history'])
					{
						infobox_start("Geschichtsdaten",1);
						echo "<tr><th class=\"tbltitle\" style=\"width:120px;\">Datum / Zeit</th><th class=\"tbltitle\">Ereignis</th></tr>";
						$hres=dbquery("SELECT * FROM alliance_history WHERE history_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id']." ORDER BY history_timestamp DESC;");
						while ($harr=mysql_fetch_array($hres))
						{
							echo "<tr><td class=\"tbldata\">".date("d.m.Y H:i",$harr['history_timestamp'])."</td><td class=\"tbldata\">".text2html($harr['history_text'])."</td></tr>";
						}
						infobox_end(1);
						echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
					}
					else
						echo "Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Mitglieder anzeigen
				//
				elseif ($_GET['action']=="viewmembers")
				{
					echo "<h2>Allianzmitglieder</h2>";
					if ($isFounder || $myRight['viewmembers'])
					{
						$rres = dbquery("
						SELECT
                            rank_name,
                            rank_id
						FROM
							".$db_table['alliance_ranks']."
						WHERE
							rank_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
						while ($rarr=mysql_fetch_array($rres))
						{
							$rank[$rarr['rank_id']]=$rarr['rank_name'];
						}
						echo "<form action=\"?page=$page\" method=\"post\">";
						echo "<table width=\"500\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">";
						echo "<tr>
						<td class=\"tbltitle\">Nick</td>
						<td class=\"tbltitle\">Heimatplanet</td>
						<td class=\"tbltitle\">Punkte</td>
						<td class=\"tbltitle\">Rasse</td>
						<td class=\"tbltitle\">Rang</td>
						<td class=\"tbltitle\">Attack</td>
						<td class=\"tbltitle\">Online</td>
						<td class=\"tbltitle\">Aktionen</td>";
						$ures = dbquery("SELECT u.user_acttime,u.user_id,u.user_points,u.user_nick,p.planet_id,u.user_alliance_rank_id,race_name FROM ".$db_table['users']." AS u,".$db_table['planets']." AS p,".$db_table['races']." WHERE user_race_id=race_id AND u.user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND p.planet_user_id=u.user_id AND p.planet_user_main=1 AND u.user_alliance_application='' GROUP BY u.user_id ORDER BY u.user_points DESC, u.user_nick;");
						while ($uarr = mysql_fetch_array($ures))
						{
							echo "<tr";
							if (time()-ONLINE_TIME< $uarr['user_last_online'])	echo " style=\"color:#0f0;\";";
							echo ">";
							echo "<td class=\"tbldata\">".$uarr['user_nick']."</td>
							<td class=\"tbldata\">".coords_format2($uarr['planet_id'],1)."</td>
							<td class=\"tbldata\">".nf($uarr['user_points'])."</td>
							<td class=\"tbldata\">".$uarr['race_name']."</td>";
							if ($arr['alliance_founder_id']==$uarr['user_id'])
								echo "<td class=\"tbldata\">Gr&uuml;nder</td>";
							elseif ($rank[$uarr['user_alliance_rank_id']]!="")
								echo "<td class=\"tbldata\">".$rank[$uarr['user_alliance_rank_id']]."</td>";
							else
								echo "<td class=\"tbldata\">-</td>";

							$num=check_fleet_incomming($uarr['user_id']);
							if ($num>0)
								echo "<td BGCOLOR=\"#FF0000\" align=\"center\">".$num."</td>";
							else
								echo "<td class=\"tbldata\">-</td>";

							if ((time()-$conf['online_threshold']['v']*60) < $uarr['user_acttime'])
								echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
							else
								echo "<td class=\"tbldata\">".date("d.m.Y H.i",$uarr['user_acttime'])."</td>";

							if ($_SESSION[ROUNDID]['user']['id']!=$uarr['user_id'])
								echo"<td class=\"tbldata\"><a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uarr['user_id']."\">Nachricht</a></td></tr>";
							else
								echo "<td class=\"tbldata\">-</td></tr>";
						}
						echo "</table><br>";
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
					}
					else
						echo "Keine Berechtigung!<br/><br/><input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" />";
				}

				//
				// Allianz verlassen (Durchführen)
				//
				elseif ($_GET['action']=="leave" && !$isFounder)
				{
					echo "<h2>Allianz-Austritt</h2>";
					if ($_SESSION[ROUNDID]['user']['alliance_id']!=0)
					{
						echo "Du bist aus der Allianz ausgetreten!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
						$alliances = get_alliance_names();
						dbquery("UPDATE ".$db_table['users']." SET user_alliance_rank_id=0,user_alliance_id=0 WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';");
						send_msg($alliances[$_SESSION[ROUNDID]['user']['alliance_id']]['founder_id'],MSG_ALLYMAIL_CAT,"Allianzaustritt","Der Spieler ".$_SESSION[ROUNDID]['user']['nick']." trat aus der Allianz aus!");
						add_alliance_history($_SESSION[ROUNDID]['user']['alliance_id'],"Der Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] trat aus der Allianz aus!");
						$allys = get_alliance_names();
						add_log(5,"Der Spieler [b]".$_SESSION[ROUNDID]['user']['nick']."[/b] ist aus der Allianz [b][".$allys[$_SESSION[ROUNDID]['user']['alliance_id']]['tag']."] ".$allys[$_SESSION[ROUNDID]['user']['alliance_id']]['name']."[/b] ausgetreten!",time());
						$_SESSION[ROUNDID]['user']['alliance_id']=0;
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
					if ($_POST['editsubmit']!="" && checker_verify())
					{
						if ($_POST['alliance_img_dim']=="px"){
							$_POST['alliance_img_width']=min($_POST['alliance_img_width'],IMG_MAX_WIDTH);
							$_POST['alliance_img_height']=min($_POST['alliance_img_height'],IMG_MAX_HEIGHT);
						}
						if ($_POST['alliance_img_dim']=="%"){
							$_POST['alliance_img_width']=min($_POST['alliance_img_width'],100);
						 	$_POST['alliance_img_height']=min($_POST['alliance_img_height'],100);
						 }

						if($_POST['alliance_img_dim']=='%')
						{
							$img_info = getimagesize($_POST['alliance_img']); //$img_info[0] = breite, $img_info[1] = höhe
							if($img_info[0]/100*$_POST['alliance_img_width']>IMG_MAX_WIDTH)
							{
								$_POST['alliance_img_dim']="px";
								$_POST['alliance_img_width']=IMG_MAX_WIDTH;
								$message="<b>Das Bild ist zu gross mit dieser Prozentangabe!</b><br>";
							}
							if($img_info[1]/100*$_POST['alliance_img_height']>IMG_MAX_HEIGHT)
							{
								$_POST['alliance_img_dim']="px";
							  $_POST['alliance_img_height']=IMG_MAX_HEIGHT;
							  $message="<b>Das Bild ist zu gross mit dieser Prozentangabe!</b><br>";
							}
						}
						dbquery("UPDATE ".$db_table['alliances']." SET alliance_tag='".addslashes($_POST['alliance_tag'])."', alliance_name='".addslashes($_POST['alliance_name'])."', alliance_text='".addslashes($_POST['alliance_text'])."', alliance_img='".$_POST['alliance_img']."', alliance_url='".$_POST['alliance_url']."', alliance_img_width='".$_POST['alliance_img_width']."', alliance_img_height='".$_POST['alliance_img_height']."', alliance_img_dim='".$_POST['alliance_img_dim']."' WHERE alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
						$res = dbquery("SELECT * FROM ".$db_table['alliances']." WHERE alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
						$arr = mysql_fetch_array($res);
						echo "Die &Auml;nderungen wurden übernommen!<br/>$message<br/>";
					}

					// Bewerbungsvorlage speichern
					if ($_POST['applicationtemplatesubmit']!="" && checker_verify())
					{
						dbquery("UPDATE ".$db_table['alliances']." SET alliance_application_template='".addslashes($_POST['alliance_application_template'])."' WHERE alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id'].";");
						echo "Die &Auml;nderungen wurden übernommen!<br/><br/>";
					}

	        // Allianz auflösen
					if ($_POST['liquidatesubmit']!="" && $isFounder && $_SESSION[ROUNDID]['user']['alliance_id']==$_POST['id_control'] && checker_verify())
					{
						delete_alliance($arr['alliance_id'],true);
						$_SESSION[ROUNDID]['user']['alliance_id']=0;
						echo "Die Allianz wurde aufgel&ouml;st!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
					}
					// Allianzdaten anzeigen
					else
					{
						$member_count = mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND user_alliance_application='';"));
						infobox_start("[".stripslashes($arr['alliance_tag'])."] ".stripslashes($arr['alliance_name']),1);
						if ($arr['alliance_img']!="")
						{
							if($arr['alliance_img_dim']=='%')
							{
								$img_info = getimagesize($arr['alliance_img']);
								$arr['alliance_img_width']=$img_info[0]/100*$arr['alliance_img_width'];
								$arr['alliance_img_height']=$img_info[1]/100*$arr['alliance_img_height'];
								$arr['alliance_img_dim']='px';
								dbquery("UPDATE ".$db_table['alliances']." SET
									alliance_img_width=".$arr['alliance_img_width'].",
									alliance_img_height=".$arr['alliance_img_height'].",
									alliance_img_dim='".$arr['alliance_img_dim']."'
								WHERE alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
							}

							echo "<tr><td class=\"tblblack\" colspan=\"3\" align=\"center\"><img src=\"".$arr['alliance_img']."\" alt=\"Allianz-Logo\" style=\"width:".$arr['alliance_img_width']."".$arr['alliance_img_dim'].";height:".$arr['alliance_img_height']."".$arr['alliance_img_dim']."\" /></td></tr>";
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
							$bewerb_res = dbquery("SELECT user_id,user_alliance_application FROM ".$db_table['users']." WHERE user_alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
							if (mysql_num_rows($bewerb_res)>0)
							{
								$bewerbung = 0;
								while($bewerb_arr=mysql_fetch_array($bewerb_res))
								{
									if($bewerb_arr['user_alliance_application']!='')
										$bewerbung = 1;
								}
								if($bewerbung==1)
									echo "<tr><td class=\"tbltitle\" colspan=\"3\" align=\"center\"><div align=\"center\"><b><a href=\"?page=$page&action=applications\">Es sind Bewerbungen vorhanden!</a></b></div></td></tr>";
							}
						}

						// Bündnissanfragen anzeigen
						if ($isFounder || $myRight['relations'])
						{
							if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND alliance_bnd_level='0';"))>0)
								echo "<tr><td class=\"tbltitle\" colspan=\"3\" align=\"center\"><div align=\"center\"><b><a href=\"?page=$page&action=bnd_ask\">Es sind B&uuml;ndnisanfragen vorhanden!</a></b></div></td></tr>";
						}

						// Kriegserklärung anzeigen
						$time=time()-192600;
						if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."' AND alliance_bnd_level='3' AND alliance_bnd_date>'$time';"))>0)
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
						if ($isFounder || $myRight['relations']) array_push($adminBox,"<a href=\"?page=$page&action=relations\">Allianzbeziehungen (B&uuml;ndnisse / Kriege)</a>");
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
									echo "<tr>";
								echo "<td class=\"tbldata\"><b>".$ab."</b></td>\n";
								if ($bcnt%2==1)
									echo "</tr>";
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

						// Text anzeigen
						if ($arr['alliance_text']!="")
							echo "<tr><td class=\"tbldata\" colspan=\"3\" style=\"text-align:center\">".text2html($arr['alliance_text'])."</td></tr>\n";

						$alliances=array();
						// Kriege
						$wars=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE (alliance_bnd_alliance_id1='".$_SESSION[ROUNDID]['user']['alliance_id']."' OR alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."') AND alliance_bnd_level=3");
						if (mysql_num_rows($wars)>0)
						{
							$alliances=get_alliance_names();
							echo "<tr><td class=\"tbltitle\">Kriege:</td><td class=\"tbldata\"><ul>";
							while ($war=mysql_fetch_array($wars))
							{
								if ($war['alliance_bnd_alliance_id1']==$_SESSION[ROUNDID]['user']['alliance_id']) $war['alliance_bnd_alliance_id1']=$war['alliance_bnd_alliance_id2'];
								echo "<li>[".$alliances[$war['alliance_bnd_alliance_id1']]['tag']."] ".$alliances[$war['alliance_bnd_alliance_id1']]['name']."</li>";
							}
							echo "</ul></td></tr>";
						}

						// Bündnisse
						$bnds=dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE (alliance_bnd_alliance_id1='".$_SESSION[ROUNDID]['user']['alliance_id']."' OR alliance_bnd_alliance_id2='".$_SESSION[ROUNDID]['user']['alliance_id']."') AND alliance_bnd_level=2");
						if (mysql_num_rows($bnds)>0)
						{
							if (count($alliances)==0) $alliances=get_alliance_names();
							echo "<tr><td class=\"tbltitle\">B&uuml;ndnisse:</td><td class=\"tbldata\"><ul>";
							while ($bnd=mysql_fetch_array($bnds))
							{
								if ($bnd['alliance_bnd_alliance_id1']==$_SESSION[ROUNDID]['user']['alliance_id']) $bnd['alliance_bnd_alliance_id1']=$bnd['alliance_bnd_alliance_id2'];
								echo "<li>[".$alliances[$bnd['alliance_bnd_alliance_id1']]['tag']."] ".$alliances[$bnd['alliance_bnd_alliance_id1']]['name']."</li>";
							}
							echo "</ul></td></tr>";
						}

						// Besucher
						if ($arr['alliance_visits_ext']>0)
							echo "<tr><td class=\"tbltitle\" width=\"120\">Besucherz&auml;hler:</td><td class=\"tbldata\" colspan=\"2\">".nf($arr['alliance_visits_ext'])." Besucher</td></tr>\n";

						// Website
						if ($arr['alliance_url']!="")
							echo "<tr><td class=\"tbltitle\" width=\"120\">Website/Forum:</td><td class=\"tbldata\" colspan=\"2\"><b>".format_link($arr['alliance_url'])."</a></b></td></tr>\n";
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
					dbquery("UPDATE ".$db_table['users']." SET user_alliance_id=0,user_alliance_rank_id=0,user_alliance_application='' WHERE user_id=".$_SESSION[ROUNDID]['user']['id'].";");
					echo "Die fehlerhafte Verkn&uuml;pfung wurde gel&ouml;st!";
				}
				else
					echo "<form action=\"?page=$page\" method=\"post\">Diese Allianz existiert nicht!<br/><br/><input type=\"submit\" name=\"resolvefalseallyid\" value=\"Fehlerhafte Allianzverkn&uuml;pfung l&ouml;schen\" /></form>";
			}
		}
	}
?>
