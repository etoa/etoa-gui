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
	// 	Dateiname: user.php
	// 	Topic: Benutzerverwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Fehlerhafte Logins
	//
	if ($sub=="loginfailures")
	{
		require("user/loginfailures.inc.php");
	}

	//
	// Beobachter
	//
	elseif ($sub=="observed")
	{
		require("user/observed.inc.php");		
	}

	//
	// Tickets
	//
	elseif ($sub=="tickets")
	{
		require("user/tickets.inc.php");		
	}

	
	//
	// Verwarnungen
	//
	elseif ($sub=="warnings")
	{
		require("user/warnings.inc.php");		
	}	

	//
	// Bilder prüfen
	//
	elseif ($sub=="imagecheck")
	{
		require("user/imagecheck.inc.php");		
	}
	
	//
	// Session-Log
	//
	elseif ($sub=="userlog")
	{
		require("user/userlog.inc.php");
	}

	//
	// Rassen
	//
	elseif ($sub=="race")
	{
		advanced_form("races");
	}

	//
	// History
	//
	elseif ($sub=="history")
	{
		require("user/history.inc.php");
	}

	//
	// Änderungsanträge
	//
	elseif ($sub=="requests")
	{
		require("user/requests.inc.php");
	}

	//
	// Punkteverlauf
	//
	elseif ($sub=="point")
	{
		require("user/point.inc.php");
	}

	//
	// Multisuche
	//
	elseif ($sub=="multi")
	{
		require("user/multi.inc.php");
	}

	//
	// Sittings
	//
	elseif ($sub=="sitting")
	{
		require("user/sitting.inc.php");
	}


	//
	// User-Suchergebnisse anzeigen
	//

	else
	{
		echo "<h1>Spieler</h1>";

		if ((isset($_GET['special']) || isset($_POST['user_search']) || isset($_SESSION['admin']['user_query'])) && isset($_GET['action']) && $_GET['action']=="search")
		{
			$tables = $db_table['users'];

			if (isset($_GET['special']))
			{
				switch ($_GET['special'])
				{
					case "ip":
						$sql= " user_ip='".base64_decode($_GET['val'])."'";
						break;					
					case "host":
						$sql= " user_hostname='".base64_decode($_GET['val'])."'";
						break;					
					default:
						$sql= " user_nick='%".base64_decode($_GET['val'])."%'";
				}
				$sqlstart="SELECT * FROM $tables WHERE ";
				$sqlend=" ORDER BY user_nick;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['user_query']=$sql;				
			}
			elseif ($_SESSION['admin']['user_query']=="")
			{
				$sql='';
				if ($_POST['user_id']!="")
				{
					$sql.= " AND user_id='".$_POST['user_id']."'";
				}
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}
				if ($_POST['user_name']!="")
				{
					if (stristr($_POST['qmode']['user_name'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_name ".stripslashes($_POST['qmode']['user_name']).$_POST['user_name']."$addchars'";
				}
				if ($_POST['user_email']!="")
				{
					if (stristr($_POST['qmode']['user_email'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_email ".stripslashes($_POST['qmode']['user_email']).$_POST['user_email']."$addchars'";
				}
				if ($_POST['user_email_fix']!="")
				{
					if (stristr($_POST['qmode']['user_email_fix'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_email_fix ".stripslashes($_POST['qmode']['user_email_fix']).$_POST['user_email_fix']."$addchars'";
				}
				if ($_POST['user_password']!="")
				{
					$sql.= " AND user_password LIKE '".md5($_POST['user_password'])."'";
				}
				if ($_POST['user_ip']!="")
				{
					if (stristr($_POST['qmode']['user_ip'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_ip ".stripslashes($_POST['qmode']['user_ip']).$_POST['user_ip']."$addchars'";
				}
				if ($_POST['user_alliance']!="")
				{
					if (stristr($_POST['qmode']['user_alliance'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_alliance_id=alliance_id AND alliance_name ".stripslashes($_POST['qmode']['user_alliance']).$_POST['user_alliance']."$addchars'";
					$tables.=",".$db_table['alliances'];
				}
				if ($_POST['user_race_id']!="")
				{
					$sql.= " AND user_race_id='".$_POST['user_race_id']."'";
				}
				if ($_POST['user_profile_text']!="")
				{
					if (stristr($_POST['qmode']['user_profile_text'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_profile_text ".stripslashes($_POST['qmode']['user_profile_text']).$_POST['user_profile_text']."$addchars'";
				}
				if (isset($_POST['user_hmode']) && $_POST['user_hmode']<2)
				{
					if ($_POST['user_hmode']==1)
						$sql.= " AND (user_hmode_from<".time()." AND user_hmode_to>".time().")";
					else
						$sql.= " AND (user_hmode_to<".time().")";
				}
				if (isset($_POST['user_blocked']) && $_POST['user_blocked']<2)
				{
					if ($_POST['user_blocked']==1)
						$sql.= " AND (user_blocked_from<".time()." AND user_blocked_to>".time().")";
					else
						$sql.= " AND (user_blocked_to<".time().")";
				}
				if (isset($_POST['user_admin']) && $_POST['user_admin']<2)
				{
					if ($_POST['user_admin']==1)
						$sql.= " AND user_admin=1 ";
					else
						$sql.= " AND user_admin=0 ";
				}				
				if (isset($_POST['user_comment']) && $_POST['user_comment']<2)
				{
					if ($_POST['user_comment']==1)
						$sql.= " AND user_comment!='' ";
					else
						$sql.= " AND user_comment='' ";
				}

				$sqlstart="SELECT * FROM $tables WHERE 1 ";
				$sqlend=" ORDER BY user_nick;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['user_query']=$sql;
			}
			else
  			$sql=$_SESSION['admin']['user_query'];


echo $sql;
			$res = dbquery($sql);
			$nr = mysql_num_rows($res);
			if ($nr==1)
			{
				$arr = mysql_fetch_array($res);
				echo "<script>document.location='?page=$page&sub=edit&user_id=".$arr['user_id']."';</script>
				Klicke <a href=\"?page=$page&sub=edit&user_id=".$arr['user_id']."\">hier</a> falls du nicht automatisch weitergeleitet wirst...";				
			}
			elseif ($nr>0)
			{
				echo $nr." Datens&auml;tze vorhanden<br/><br/>";
				if ($nr>20)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";

				$race = get_races_array();
				$allys=get_alliance_names();
 				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Nick</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">E-Mail</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Punkte</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Allianz</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Rasse</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Bemerkungen</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['user_blocked_from']<time() && $arr['user_blocked_to']>time())
						$uCol=USER_COLOR_BANNED;
					elseif($arr['user_hmode_from']<time() && $arr['user_hmode_to']>time())
						$uCol=USER_COLOR_HOLIDAY;
					elseif ($arr['user_deleted']!=0)
						$uCol=USER_COLOR_DELETED;
					else
						$uCol=USER_COLOR_DEFAULT;
					echo "<tr>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_name']."\">".$arr['user_id']."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_nick']."\">".$arr['user_nick']."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_name']."\">".cut_string($arr['user_name'],15)."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_email']."\">".cut_string($arr['user_email'],15)."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">".nf($arr['user_points'])."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">".$allys[$arr['user_alliance_id']]['tag']."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">".$race[$arr['user_race_id']]['race_name']."</td>";
					if ($arr['user_comment']!="")
						echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" ".tm("Interne Bemerkungen",$arr['user_comment']).">".cut_string($arr['user_comment'],11)."</td>";
					else
						echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">-</td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&amp;sub=edit&amp;&user_id=".$arr['user_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
			}
		}

		//
		// User-Daten bearbeiten
		//

		elseif ($sub=="edit")
		{
			// Geänderte Daten speichern
			if (isset($_POST['save']))
			{
				$sql = "UPDATE ".$db_table['users']." SET
				user_name='".$_POST['user_name']."',
				user_nick='".$_POST['user_nick']."',
				user_email='".$_POST['user_email']."',
				user_password_temp='".$_POST['user_password_temp']."',
				user_email_fix='".$_POST['user_email_fix']."',
				user_race_id='".$_POST['user_race_id']."',
				user_alliance_id='".$_POST['user_alliance_id']."',
				user_alliance_application='".addslashes($_POST['user_alliance_application'])."',
				user_css_style='".$_POST['user_css_style']."',
				user_image_url='".$_POST['user_image_url']."',
				user_image_ext='".$_POST['user_image_ext']."',
				user_profile_text='".addslashes($_POST['user_profile_text'])."',
				user_comment='".addslashes($_POST['user_comment'])."',
				user_signature='".addslashes($_POST['user_signature'])."',
				user_msgsignature='".addslashes($_POST['user_msgsignature'])."',
				user_multi_delets=".$_POST['user_multi_delets'].",
				user_sitting_days=".$_POST['user_sitting_days'].",
				user_admin=".$_POST['user_admin'].",
				user_show_stats=".$_POST['user_show_stats'].",
				user_alliance_rank_id=".intval($_POST['user_alliance_rank_id']).",
				user_profile_board_url='".$_POST['user_profile_board_url']."',
				user_spyship_count=".$_POST['user_spyship_count'].",
				user_spyship_id=".$_POST['user_spyship_id'].",
				user_fleet_rtn_msg=".$_POST['user_fleet_rtn_msg'].",
				user_msg_preview=".$_POST['user_msg_preview'].",
				user_msgcreation_preview=".$_POST['user_msgcreation_preview'].",
				user_msg_blink=".$_POST['user_msg_blink'].",
				user_msg_copy=".$_POST['user_msg_copy'].",
				user_game_width=".$_POST['user_game_width'].",
				user_planet_circle_width=".$_POST['user_planet_circle_width'].",
				user_item_show='".$_POST['user_item_show']."',
				user_image_filter=".$_POST['user_image_filter'].",
				user_helpbox=".$_POST['user_helpbox'].",
				user_notebox=".$_POST['user_notebox'].",
				user_havenships_buttons=".$_POST['user_havenships_buttons'].",
				user_show_adds=".$_POST['user_show_adds']."
				";				

				// Handle specialist decision
				if ($_POST['user_specialist_id']>0 && $_POST['user_specialist_time_h']>0)
				{
					$sql.=",user_specialist_time='".mktime($_POST['user_specialist_time_h'],$_POST['user_specialist_time_i'],0,$_POST['user_specialist_time_m'],$_POST['user_specialist_time_d'],$_POST['user_specialist_time_y'])."'
					,user_specialist_id=".$_POST['user_specialist_id']."	";
				}
				else
				{
					$sql.=",user_specialist_time=0
					,user_specialist_id=0	";
				}

				// Handle  image
        if ($_POST['profile_img_del']==1)
        {
					$res = dbquery("SELECT user_profile_img FROM users WHERE user_id=".$_GET['user_id'].";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
	          if (file_exists('../'.PROFILE_IMG_DIR."/".$arr['user_profile_img']))
	          {
	              unlink('../'.PROFILE_IMG_DIR."/".$arr['user_profile_img']);
	          }
	          $sql.=",user_profile_img=''";
	        }
        }
        
        // Handle avatar
        if ($_POST['avatar_img_del']==1)
        {
					$res = dbquery("SELECT user_avatar FROM users WHERE user_id=".$_GET['user_id'].";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
	          if (file_exists('../'.BOARD_AVATAR_DIR."/".$arr['user_avatar']))
	          {
	              unlink('../'.BOARD_AVATAR_DIR."/".$arr['user_avatar']);
	          }
	          $sql.=",user_avatar=''";
	        }
        }        

				// Handle password
				if ($_POST['user_password']!="")
				{
					$pres = dbquery("SELECT user_registered FROM users WHERE user_id='".$_GET['user_id']."';");
					$parr = mysql_fetch_row($pres);
					$sql.= ",user_password='".pw_salt($_POST['user_password'],$parr[0])."'";
					echo "Das Passwort wurde ge&auml;ndert!<br>";
					add_log(8,$_SESSION[SESSION_NAME]['user_nick']." ändert das Passwort von ".$_POST['user_nick']."",time());
				}				
				
				// Handle ban
				if ($_POST['ban_enable']==1)
				{
					$ban_from = mktime($_POST['user_blocked_from_h'],$_POST['user_blocked_from_i'],0,$_POST['user_blocked_from_m'],$_POST['user_blocked_from_d'],$_POST['user_blocked_from_y']);
					$ban_to = mktime($_POST['user_blocked_to_h'],$_POST['user_blocked_to_i'],0,$_POST['user_blocked_to_m'],$_POST['user_blocked_to_d'],$_POST['user_blocked_to_y']);
					$sql.= ",user_blocked_from='".$ban_from."'";
					$sql.= ",user_blocked_to='".$ban_to."'";
					$sql.= ",user_ban_admin_id='".$_POST['user_ban_admin_id']."'";
					$sql.= ",user_ban_reason='".addslashes($_POST['user_ban_reason'])."'";
					add_user_history($_GET['user_id'],"[b]Accountsperrung[/b] von [b]".date("d.m.Y H:i",$ban_from)."[/b] bis [b]".date("d.m.Y H:i",$ban_to)."[/b]\n[b]Grund:[/b] ".addslashes($_POST['user_ban_reason'])."\n[b]Verantwortlich: [/b] ".$_SESSION[SESSION_NAME]['user_nick']);
				}
				else
				{
					$sql.= ",user_blocked_from=0";
					$sql.= ",user_blocked_to=0";
					$sql.= ",user_ban_admin_id='0'";
					$sql.= ",user_ban_reason=''";
				}
				
				// Handle holiday mode
				if ($_POST['umod_enable']==1)
				{
					$sql.= ",user_hmode_from='".mktime($_POST['user_hmode_from_h'],$_POST['user_hmode_from_i'],0,$_POST['user_hmode_from_m'],$_POST['user_hmode_from_d'],$_POST['user_hmode_from_y'])."'";
					$sql.= ",user_hmode_to='".mktime($_POST['user_hmode_to_h'],$_POST['user_hmode_to_i'],0,$_POST['user_hmode_to_m'],$_POST['user_hmode_to_d'],$_POST['user_hmode_to_y'])."'";
				}
				else
				{
					$sql.= ",user_hmode_from=0";
					$sql.= ",user_hmode_to=0";
				}

				// Perform query
				$sql .= " WHERE user_id='".$_GET['user_id']."';";
				dbquery($sql);
				echo "<div style=\"color:#0f0;\">Änderungen gespeichert!</div>";

				//Aktuelles Sitten Stoppen
				if($_POST['user_sitting_active']==1)
				{
                    dbquery("
                    UPDATE
                        ".$db_table['user_sitting']."
                    SET
                        user_sitting_active='0',
                        user_sitting_sitter_user_id='0',
                        user_sitting_sitter_password='0',
                        user_sitting_date='0'
                    WHERE
                        user_sitting_user_id='".$_GET['user_id']."';");

                	//löscht alle gespeichertet Sittingdaten des users
               		dbquery("DELETE FROM ".$db_table['user_sitting_date']." WHERE user_sitting_date_user_id='".$_GET['user_id']."';");

				}
				//Sitter Passwort ändern
				if ($_POST['user_sitting_sitter_password']!="")
				{
                    dbquery("
                    UPDATE
                        ".$db_table['user_sitting']."
                    SET
                        user_sitting_sitter_password='".md5($_POST['user_sitting_sitter_password'])."'
                    WHERE
                        user_sitting_user_id='".$_GET['user_id']."';");
					echo "Das Sitter Passwort wurde ge&auml;ndert!<br>";
					add_log(8,$_SESSION[SESSION_NAME]['user_nick']." &auml;ndert das Sitterpasswort von ".$_POST['user_nick']."",time());
				}				
			}

			// User löschen
			if (isset($_POST['delete_user']))
			{
				delete_user($_GET['user_id'],false,$_SESSION[SESSION_NAME]['user_nick']);
				echo "L&ouml;schung erfolgreich!<br/><br/>";
			}
			
			// Löschantrag speichern
			if (isset($_POST['requestdelete']))
			{
				$t = time() + ($conf['user_delete_days']['v']*3600*24);
				dbquery("
				UPDATE
					users
				SET
					user_deleted=".$t."
				WHERE
					user_id=".$_GET['user_id']."
				;");			
			}
			
			// Löschantrag aufheben
			if (isset($_POST['canceldelete']))
			{
				dbquery("
				UPDATE
					users
				SET
					user_deleted=0
				WHERE
					user_id=".$_GET['user_id']."
				;");
				echo "Löschantrag aufgehoben!<br/><br/>";
			}

			// Fetch all data
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['users']." 
			WHERE 
				user_id='".$_GET['user_id']."'
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<script type=\"text/javascript\">
				function showTab(idx)
				{
					document.getElementById('tabGeneral').style.display='none';
					document.getElementById('tabData').style.display='none';
					document.getElementById('tabGame').style.display='none';
					document.getElementById('tabProfile').style.display='none';
					document.getElementById('tabAccount').style.display='none';
					document.getElementById('tabMessages').style.display='none';
					document.getElementById('tabDesign').style.display='none';
					document.getElementById('tabFailures').style.display='none';
					document.getElementById('tabPoints').style.display='none';
					//document.getElementById('tabWarnings').style.display='none';
					document.getElementById('tabTickets').style.display='none';
					
					document.getElementById(idx).style.display='';
				
				}				
				
				function loadSpecialist(st)
				{
					var elem = document.getElementById('user_specialist_id');
					xajax_showTimeBox('spt','user_specialist_time',st,elem.options[elem.selectedIndex].value);
				}

				function loadAllianceRanks(val)
				{
					var elem = document.getElementById('user_alliance_id');
					xajax_allianceRankSelector('ars','user_alliance_rank_id',val,elem.options[elem.selectedIndex].value);
				}
				
				
				
				</script>";
				
				
				$arr = mysql_fetch_array($res);
				
				echo "<h2>Details ".$arr['user_nick']."</h2>";

				echo "<form action=\"?page=$page&amp;sub=edit&amp;user_id=".$_GET['user_id']."\" method=\"post\">";
				
				echo "<div id=\"tabNav\">
					<a href=\"javascript:;\" onclick=\"showTab('tabGeneral')\">Allgemeines</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabData')\">Daten</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabAccount')\">Account</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabProfile')\">Profil</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabGame')\">Spiel</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabMessages')\">Nachrichten</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabDesign')\">Design</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabFailures')\">Loginfehler</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabPoints')\">Punkte</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabTickets')\">Tickets</a>
					
					<!--<a href=\"javascript:;\" onclick=\"showTab('tabWarnings')\">Verwarnungen</a>-->
				<br style=\"clear:both;\" />
				</div>             
				<div id=\"tabContent\">";
				
				/**
				* Allgemeines
				*/				
				echo "<table class=\"tb\" id=\"tabGeneral\">";
				echo "<tr><th style=\"width:130px;\">ID:</th><td>".$arr['user_id']."</td></tr>";
				echo "<tr><th>Registrierdatum:</th><td>".df($arr['user_registered'])."</td></tr>";
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";

				echo "<tr><th>Zulezt online:</th><td>".df($arr['user_acttime'])."</td></tr>";
				echo "<tr><th>IP:</th><td>".$arr['user_ip']."</td></tr>";
				echo "<tr><th>Host:</th><td>".$arr['user_hostname']."</td></tr>";
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";
				
				echo "<tr><th>Punkte:</th><td>".nf($arr['user_points'])." 
				[<a href=\"javascript:;\" onclick=\"toggleBox('pointGraph')\">Verlauf anzeigen</a>]
				<div id=\"pointGraph\" style=\"display:none;\"><img src=\"../misc/stats.image.php?user=".$arr['user_id']."\" alt=\"Diagramm\" /></div>
				</td></tr>";
				echo "<tr><th>Aktueller Rang:</th><td>".nf($arr['user_rank_current'])."</td></tr>";
				echo "<tr><th>Letzter Rang:</th><td>".nf($arr['user_rank_last'])."</td></tr>";
				echo "<tr><th>Höchster Rang:</th><td>".nf($arr['user_highest_rank'])."</td></tr>";
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";
				
				echo "<tr>
								<th>Rohstoffe:</th>
								<td>
								Raids: ".nf($arr['user_res_from_raid'])." t<br/> 
								Asteroiden: ".nf($arr['user_res_from_asteroid'])." t<br/>
								Nebelfelder: ".nf($arr['user_res_from_nebula'])." t
							</td>
						</tr>";						
				
				/*
				if ($arr['user_deleted']!=0)
				{
					infobox_start("Löschantrag");
					echo "<span style=\"color:".USER_COLOR_DELETED."\">Dieser Account ist zur Löschung am ".df($arr['user_deleted'])." vorgemerkt!</span>";
					infobox_end();					
				}*/
				echo "</table>";
				
				/**
				* Daten
				*/
				echo "<table class=\"tb\" id=\"tabData\" style=\"display:none;\">";
				echo "<tr>
					<th>Nick:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_nick\" value=\"".$arr['user_nick']."\" size=\"35\" maxlength=\"250\" />
						(Eine Nickänderung ist grundsätzlich nicht erlaubt)
					</td>
				</tr>";
				echo "<tr>
					<th>E-Mail:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_email\" value=\"".$arr['user_email']."\" size=\"35\" maxlength=\"250\" />
						(Rundmails gehen an diese Adresse)
					</td>
				</tr>";
				echo "<tr>
					<th>Name:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_name\" value=\"".$arr['user_name']."\" size=\"35\" maxlength=\"250\" />
						(Bei Accountübergabe anpassen)
					</td>
				</tr>";
				echo "<tr>
					<th>E-Mail fix:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_email_fix\" value=\"".$arr['user_email_fix']."\" size=\"35\" maxlength=\"250\" />
						(Bei Accountübergabe anpassen)
					</td>
				</tr>";
				echo "<tr>
					<th>Passwort:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_password\" value=\"\" size=\"35\" maxlength=\"250\" />
						(Leerlassen um altes Passwort beizubehalten)
					</td>
				</tr>";
				echo "<tr>
					<th>Temporäres Passwort:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_password_temp\" value=\"".$arr['user_password_temp']."\" size=\"30\" maxlength=\"30\" />
						(Nur dieses wird verwendet, falls ausgefüllt)
					</td>
				</tr>";
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";

				echo "<tr>
					<th>Statistikanzeige:</th>
					<td class=\"tbldata\">Ja: <input type=\"radio\" name=\"user_show_stats\" value=\"1\"";
						if ($arr['user_show_stats']==1)
						{
							echo " checked=\"checked\" ";
						}
						echo " /> Nein: <input type=\"radio\" name=\"user_show_stats\" value=\"0\" ";
						if ($arr['user_show_stats']==0)
						{
							echo " checked=\"checked\" ";
						}
						echo "/> (Legt fest ob der Spieler in der Rangliste angezeigt wird)
					</td>
				</tr>";
				echo "<tr>
					<th>Admin:</th>
					<td class=\"tbldata\">Ja: <input type=\"radio\" name=\"user_admin\" value=\"1\"";
						if ($arr['user_admin']==1)
							echo " checked=\"checked\" ";
						echo " /> Nein: <input type=\"radio\" name=\"user_admin\" value=\"0\" ";
						if ($arr['user_admin']==0)
							echo " checked=\"checked\" ";
						echo "/> (Der Spieler und seine Planeten werden als Admin markiert)
					</td>
				</tr>";
				echo "<tr>
					<th>Interne Bemerkungen:</th>
					<td class=\"tbldata\"><textarea name=\"user_comment\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_comment'])."</textarea></td>
				</tr>";
				echo "</table>";
				
				/**
				* Game-Einstellungen
				*/
				echo "<table class=\"tb\" id=\"tabGame\" style=\"display:none;\">";
				echo "<tr>
					<th style=\"width:340px;\">Rasse:</td>
					<td class=\"tbldata\"><select name=\"user_race_id\">";
					$tres = dbquery("SELECT * FROM ".$db_table['races']." ORDER BY race_name;");
					while ($tarr = mysql_fetch_array($tres))
					{
						echo "<option value=\"".$tarr['race_id']."\"";
						if ($arr['user_race_id']==$tarr['race_id']) echo " selected=\"selected\"";
						echo ">".$tarr['race_name']."</option>\n";
					}
					echo "</select>				
					</td>
				</tr>";
				
				if ($arr['user_specialist_time']>0)
					$st = $arr['user_specialist_time'];
				else
					$st = time();
				echo "<tr>
					<th class=\"tbltitle\">Spezialist:</th>
					<td class=\"tbldata\">
						<select name=\"user_specialist_id\" id=\"user_specialist_id\" onchange=\"loadSpecialist(".$st.");\">";
				echo '<option value="0">(Keiner)</option>';
				$sres = dbquery("
				SELECT
					specialist_name,
					specialist_id
				FROM
					".$db_table['specialists']."
				ORDER BY
					specialist_name				
				;");
				while ($sarr=mysql_fetch_row($sres))
				{
					echo '<option value="'.$sarr[1].'"';
					if ($arr['user_specialist_id']==$sarr[1]) 
						echo ' selected="selected"';
					echo '>'.$sarr[0].'</option>';
				}
				echo '</select> &nbsp; Arbeitsbeginn:&nbsp; <span id="spt">-</span></td></tr>';
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";
				echo "<tr>
					<th>Allianz:</td>
					<td class=\"tbldata\">
						<select id=\"user_alliance_id\" name=\"user_alliance_id\" onchange=\"loadAllianceRanks(".$arr['user_alliance_rank_id'].");\">";
						$ally_arr=get_alliance_names();
						echo "<option value=\"0\">(Keine)</option>";
						foreach ($ally_arr as $aid=>$ak)
						{
							echo "<option value=\"$aid\"";
							if ($aid==$arr['user_alliance_id']) echo " selected=\"selected\"";
							echo ">[".$ak['tag']."]  ".$ak['name']."</option>";
						}
						echo "</select> Rang: <span id=\"ars\">-</span></td>
				</tr>";
				echo "<tr>
					<th>Allianz-Bewerbung:</th>
					<td class=\"tbldata\">
						<div style=\"float:left;margin-right:5px;\">
							<textarea name=\"user_alliance_application\" cols=\"50\" rows=\"3\">".stripslashes($arr['user_alliance_application'])."</textarea>
						</div>
						(Ist dieses Feld nicht leer, ist der Spieler im Bewerbungsmodus und noch kein Mitgleid der Allianz)
						<br style=\"clear:both;\" />
					</td>
				</tr>";				
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";
        echo "<tr>
        	<th>Spionagesonden für Direktscan:</th>
          <td>
          	<input type=\"text\" name=\"user_spyship_count\" maxlength=\"5\" size=\"5\" value=\"".$arr['user_spyship_count']."\"> &nbsp; ";
	        $sres = dbquery("
	        SELECT
	        	ship_id,
	        	ship_name
	        FROM
	        	ships
	     		WHERE 
	     			ship_spy=1
	     		ORDER BY ship_name
	     		;");
	        if (mysql_num_rows($sres)>0)
	        {
	        	echo '<select name="user_spyship_id"><option value="0">(keines)</option>';
	        	while ($sarr=mysql_fetch_array($sres))
	        	{
	        		echo '<option value="'.$sarr['ship_id'].'"';
	        		if ($arr['user_spyship_id']==$sarr['ship_id'])
	        		 echo ' selected="selected"';
	        		echo '>'.$sarr['ship_name'].'</option>';
	        	}
	        }
	        else
	        {
	        	echo "Momentan steht kein Schiff zur Auswahl!";
	        }
        echo "</td></tr>";
				// Rückflug-Benachrichtingung für Flotten
        echo "<tr>
        			<th>Nachricht bei Transport-/Spionagerückkehr:</th>
        			<td>
                  <input type=\"radio\" name=\"user_fleet_rtn_msg\" value=\"1\" ";
                  if ($arr['user_fleet_rtn_msg']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert &nbsp;
              
                  <input type=\"radio\" name=\"user_fleet_rtn_msg\" value=\"0\" ";
                  if ($arr['user_fleet_rtn_msg']==0) echo " checked=\"checked\"";
        					echo "/> Deaktiviert
        		</td>
      		</tr>"; 
				
							
				echo "</table>";

				/**
				* Profil
				*/
				echo "<table class=\"tb\" id=\"tabProfile\" style=\"display:none;\">";
				echo "<tr>
					<th style=\"width:200px;\">Profil-Text:</th>
					<td class=\"tbldata\"><textarea name=\"user_profile_text\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_profile_text'])."</textarea></td></tr>";
				echo "<tr>
					<th>Profil-Bild:</th>
					<td>";
		      if ($arr['user_profile_img']!="")
		      {
		        echo '<img src="../'.PROFILE_IMG_DIR.'/'.$arr['user_profile_img'].'" alt="Profil" /><br/>';
		        echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
		      }				
				echo "</td></tr>";
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";
				echo "<tr>
					<th>Board-Signatur:</th>
					<td class=\"tbldata\"><textarea name=\"user_signature\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_signature'])."</textarea></td>
				</tr>";
				echo "<tr>
					<th>Avatarpfad:</th>
					<td class=\"tbldata\">";
		      if ($arr['user_avatar']!="")
		      {
		        echo '<img src="../'.BOARD_AVATAR_DIR.'/'.$arr['user_avatar'].'" alt="Profil" /><br/>';
		        echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_img_del\"> Bild l&ouml;schen<br/>";
		      }							
					echo "</td>
				</tr>";
	      echo "<tr>
	      	<th>Öffentliches Foren-Profil:</th>
	      	<td><input type=\"text\" name=\"user_profile_board_url\" maxlength=\"200\" size=\"50\" value=\"".$arr['user_profile_board_url']."\"></td>
	      </tr>";
				echo "</table>";
				
				/**
				* Messages
				*/				
				echo "<table class=\"tb\" id=\"tabMessages\" style=\"display:none;\">";
				echo "<tr>
						<th style=\"width:310px;\">Nachrichten-Signatur:</th>
						<td class=\"tbldata\"><textarea name=\"user_msgsignature\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_msgsignature'])."</textarea></td>
				</tr>";
        //Nachrichtenvorschau (Neue/Archiv) (An/Aus)
    		echo "<tr>
  				 		<th>Nachrichtenvorschau (Neue/Archiv):</th>
						<td>
                <input type=\"radio\" name=\"user_msg_preview\" value=\"1\" ";
                if ($arr['user_msg_preview']==1) echo " checked=\"checked\"";
                echo "/> Aktiviert
                <input type=\"radio\" name=\"user_msg_preview\" value=\"0\" ";
                if ($arr['user_msg_preview']==0) echo " checked=\"checked\"";
                echo "/> Deaktiviert
       			</td>
       	 </tr>";
       	     
          //Nachrichtenvorschau (Erstellen) (An/Aus)
          echo "<tr>
              		<th>Nachrichtenvorschau (Erstellen):</th>
          		<td>
                  <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"1\" ";
                  if ($arr['user_msgcreation_preview']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"0\" ";
                  if ($arr['user_msgcreation_preview']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              </td>
           </tr>";

          // Blinkendes Nachrichtensymbol (An/Aus)
          echo "<tr>
              		<th>Blinkendes Nachrichtensymbol:</th>
          		<td>
                  <input type=\"radio\" name=\"user_msg_blink\" value=\"1\" ";
                  if ($arr['user_msg_blink']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"user_msg_blink\" value=\"0\" ";
                  if ($arr['user_msg_blink']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              </td>
           </tr>";
           
          // Text kopieren (An/Aus)
          echo "<tr>
              		<th>Text bei Antwort/Weiterleiten kopieren:</th>
          		<td>
                  <input type=\"radio\" name=\"user_msg_copy\" value=\"1\" ";
                  if ($arr['user_msg_copy']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"user_msg_copy\" value=\"0\" ";
                  if ($arr['user_msg_copy']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              </td>
           </tr>";
				echo "<tr><td colspan=\"2\" class=\"tabSeparator\"></td></tr>";
        echo "<tr>
          		<th>Nachricht senden:</th>
      		<td>
        			Titel: <input type=\"text\" id=\"urgendmsgsubject\" maxlength=\"200\" size=\"50\" />
        			<input type=\"button\" onclick=\"xajax_sendUrgendMsg(".$arr['user_id'].",document.getElementById('urgendmsgsubject').value,document.getElementById('urgentmsg').value)\" value=\"Senden\" /><br/>
							Text: <textarea id=\"urgentmsg\" cols=\"60\" rows=\"4\"></textarea>
          </td>
       </tr>";
        echo "<tr>
            		<th>Letzte 5 Nachrichten:<br/>
            		<input type=\"button\" onclick=\"xajax_showLast5Messages(".$arr['user_id'].",'lastmsgbox');\" value=\"Neu laden\" />
            		</th>
        		<td id=\"lastmsgbox\">
        			Lade...
            </td>
         </tr>";           			
				echo "</table>";
				echo "<script type=\"text/javascript\">
					xajax_showLast5Messages(".$arr['user_id'].",'lastmsgbox');
				</script>";

				
				/**
				* Design
				*/
				$imagepacks = get_imagepacks("../");
				$designs = get_designs("../");								
				echo "<table class=\"tb\" id=\"tabDesign\" style=\"display:none;\">";
 				echo "<tr>
 					<th>Design:</th>
 					<td class=\"tbldata\">
 						<input type=\"text\" name=\"user_css_style\" id=\"user_css_style\" size=\"45\" maxlength=\"250\" value=\"".$arr['user_css_style']."\"> 
 						&nbsp; <input type=\"button\" onclick=\"document.getElementById('user_css_style').value = document.getElementById('designSelector').options[document.getElementById('designSelector').selectedIndex].value\" value=\"&lt;&lt;\" /> &nbsp; ";
		        echo "<select id=\"designSelector\">
		        <option value=\"\">(Bitte wählen)</option>";
            foreach ($designs as $k => $v)
            {
                echo "<option value=\"$k\"";
                if ($arr['user_css_style']==$k) echo " selected=\"selected\"";
                echo ">".$v['name']."</option>";
            }
            echo "</select>"; 						
 					echo "</td>
 				</tr>";
				echo "<tr>
					<th>Bildpaket / Dateiendung:</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"user_image_url\" id=\"user_image_url\" size=\"45\" maxlength=\"250\" value=\"".$arr['user_image_url']."\"> 
						<input type=\"text\" name=\"user_image_ext\" id=\"user_image_ext\" value=\"".$arr['user_image_ext']."\" size=\"3\" maxlength=\"6\" />
 						&nbsp; <input type=\"button\" onclick=\"
 						var ImageSet = document.getElementById('imageSelector').options[document.getElementById('imageSelector').selectedIndex].value.split(':');
 						document.getElementById('user_image_url').value=ImageSet[0];
 						document.getElementById('user_image_ext').value=ImageSet[1];
 						\" value=\"&lt;&lt;\" /> &nbsp; ";
		        echo "<select id=\"imageSelector\">
		        <option value=\"\">(Bitte wählen)</option>";
            foreach ($imagepacks as $k => $v)
            {
            	foreach ($v['extensions'] as $e)
            	{
                echo "<option value=\"$k:$e\"";
                if ($arr['user_image_url']==$k) echo " selected=\"selected\"";
                echo ">".$v['name']." ($e)</option>";
               }
            }
            echo "</select>"; 							
					echo "</td>
				</tr>";
        //Spielgrösse
        echo "<tr>
                <th>Spielgr&ouml;sse: (nur alte Designs)</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select name=\"user_game_width\">";
                    for ($x=70;$x<=100;$x+=10)
                    {
                        echo "<option value=\"$x\"";
                        if ($s['user']['game_width']==$x) echo " selected=\"selected\"";
                        echo ">".$x."%</option>";
                    }
                    echo "</select>
                </td>
             </tr>";

        //Planetkreisgrösse
        echo "<tr>
                <th>Planetkreisgr&ouml;sse:</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                  <select name=\"user_planet_circle_width\">";
                  for ($x=450;$x<=700;$x+=50)
                  {
                      echo "<option value=\"$x\"";
                      if ($s['user']['planet_circle_width']==$x) echo " selected=\"selected\"";
                      echo ">".$x."</option>";
                  }
                echo "</select>
                </td>
            </tr>";
	
				//Schiff/Def Ansicht (Einfach/Voll)
        echo "<tr>
            		<th>Schiff/Def Ansicht:</th>";
          echo "<td>
          				<input type=\"radio\" name=\"user_item_show\" value=\"full\"";
          				if($arr['user_item_show']=='full') echo " checked=\"checked\"";
          				echo " /> Volle Ansicht  &nbsp; 
           				<input type=\"radio\" name=\"user_item_show\" value=\"small\"";
          				if($arr['user_item_show']=='small') echo " checked=\"checked\"";
          				echo " /> Einfache Ansicht
           			</td>";
        echo "</tr>";


				//Bildfilter (An/Aus)
        echo "<tr>
            		<th>Bildfilter:</th>";
          echo "<td>
          				<input type=\"radio\" name=\"user_image_filter\" value=\"1\"";
          				if($arr['user_image_filter']==1) echo " checked=\"checked\"";
          				echo "/> An   &nbsp; 
          				<input type=\"radio\" name=\"user_image_filter\" value=\"0\"";
          				if($arr['user_image_filter']==0) echo " checked=\"checked\"";
          				echo "/> Aus
          			</td>";
       	echo "</tr>";
            	
					//Hilfefenster (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th>Separates Hilfefenster:</th>
            			<td>
                      <input type=\"radio\" name=\"user_helpbox\" value=\"1\" ";
                      if ($arr['user_helpbox']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert &nbsp; 
                      <input type=\"radio\" name=\"user_helpbox\" value=\"0\" ";
                      if ($arr['user_helpbox']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";            
            
					//Notizbox (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th>Separater Notizbox:</th>
            			<td>
                      <input type=\"radio\" name=\"user_notebox\" value=\"1\" ";
                      if ($arr['user_notebox']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert &nbsp; 
                      <input type=\"radio\" name=\"user_notebox\" value=\"0\" ";
                      if ($arr['user_notebox']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";   
          		
					// Hafen Buttons
          echo "<tr>
            			<th>Vertausche Buttons in Hafen-Schiffauswahl:</th>
            			<td>
                      <input type=\"radio\" name=\"user_havenships_buttons\" value=\"1\" ";
                      if ($arr['user_havenships_buttons']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert &nbsp; 
                      <input type=\"radio\" name=\"user_havenships_buttons\" value=\"0\" ";
                      if ($arr['user_havenships_buttons']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";     
          		
					// Werbebanner
          echo "<tr>
            			<th>Werbung anzeigen:</th>
            			<td>
                      <input type=\"radio\" name=\"user_show_adds\" value=\"1\" ";
                      if ($arr['user_show_adds']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert &nbsp; 
                      <input type=\"radio\" name=\"user_show_adds\" value=\"0\" ";
                      if ($arr['user_show_adds']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";				
				echo "</table>";
				
				/**
				* Loginfailures
				*/				
				echo "<table class=\"tb\" id=\"tabFailures\" style=\"display:none;\">";	
				$lres=dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['login_failures']." 
				WHERE
					failure_user_id=".$arr['user_id']."
				ORDER BY 
					failure_time DESC
				;");
				if (mysql_num_rows($lres)>0)
				{
					echo "<tr><th class=\"tbltitle\">Zeit</th>";
					echo "<th class=\"tbltitle\">IP-Adresse</th>
					<th class=\"tbltitle\">Hostname</th></tr>";
					while ($larr=mysql_fetch_array($lres))
					{
						echo "<tr><td class=\"tbldata\">".df($larr['failure_time'])."</td>";
						echo "<td class=\"tbldata\">".$larr['failure_ip']." 
						[<a href=\"?page=user&amp;action=search&amp;special=ip&amp;val=".base64_encode($larr['failure_ip'])."\">Suche Spieler</a>] 
						[<a href=\"?page=user\">Suche Session</a>]</td>";
						echo "<td class=\"tbldata\">".$larr['failure_host']."
						[<a href=\"?page=user&amp;action=search&amp;special=host&amp;val=".base64_encode($larr['failure_host'])."\">Suche Spieler</a>] </td></tr>";
					}
				}
				else
				{
					echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
				}
				echo "</table>";
	
				/**
				* Points
				*/				
				echo "<div class=\"tb\" id=\"tabPoints\" style=\"display:none;\">Laden...</div>";	
						echo "<script type=\"text/javascript\">
					xajax_userPointsTable(".$arr['user_id'].",'tabPoints');
				</script>";

				/**
				* Tickets
				*/				
				echo "<div class=\"tb\" id=\"tabTickets\" style=\"display:none;\">Laden...</div>";	
				echo "<script type=\"text/javascript\">
					xajax_userTickets(".$arr['user_id'].",'tabTickets');
				</script>";

				
				/**
				* Account
				*/				
				echo "<table class=\"tb\" id=\"tabAccount\" style=\"display:none;\">";
				// Sperrung
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Sperren</td><td class=\"tbldata\">Nein:<input type=\"radio\" name=\"ban_enable\" value=\"0\" onclick=\"banEnable(false);\"";
				if ($arr['user_blocked_from']==0) echo " checked=\"checked\"";
				echo " /> Ja:<input type=\"radio\" name=\"ban_enable\" value=\"1\" onclick=\"banEnable(true);\" ";
				if ($arr['user_blocked_from']>0) echo " checked=\"checked\"";
				echo " />";
				if ($arr['user_blocked_from']>0 && $arr['user_blocked_to']<time())	echo " <i><b>Diese Sperre ist abgelaufen!</b></i>";
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gesperrt von </td><td class=\"tbldata\">";
				if ($arr['user_blocked_from']==0)
					show_timebox("user_blocked_from",time());
				else
					show_timebox("user_blocked_from",$arr['user_blocked_from']);
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gesperrt bis</td><td class=\"tbldata\">";
				if ($arr['user_blocked_to']==0)
					show_timebox("user_blocked_to",time()+USER_BLOCKED_DEFAULT_TIME);
				else
					show_timebox("user_blocked_to",$arr['user_blocked_to']);
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gesperrt von</td><td class=\"tbldata\"><select name=\"user_ban_admin_id\" id=\"user_ban_admin_id\">";
				echo "<option value=\"0\">(niemand)</option>";
				$tres = dbquery("SELECT * FROM ".$db_table['admin_users']." ORDER BY user_nick;");
				while ($tarr = mysql_fetch_array($tres))
				{
					echo "<option value=\"".$tarr['user_id']."\"";
					if ($arr['user_ban_admin_id']==$tarr['user_id']) echo " selected=\"selected\"";
					echo ">".$tarr['user_nick']."</option>\n";
				}
				echo "</select></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Sperrgrund</td><td class=\"tbldata\"><textarea name=\"user_ban_reason\" id=\"user_ban_reason\" cols=\"60\" rows=\"2\">".stripslashes($arr['user_ban_reason'])."</textarea></td></tr>";
				// Urlaubsmodus
				echo "<tr><td class=\"tbltitle\" valign=\"top\">U-Mod</td><td class=\"tbldata\">Nein:<input type=\"radio\" name=\"umod_enable\" value=\"0\" onclick=\"umodEnable(false);\" checked=\"checked\" /> Ja:<input type=\"radio\" name=\"umod_enable\" value=\"1\" onclick=\"umodEnable(true);\" ";
				if ($arr['user_hmode_from']>0) echo " checked=\"checked\"";
				echo "/>";
				if ($arr['user_hmode_from']>0 && $arr['user_hmode_to']<time())	echo "<i><b>Dieser Urlaubsmodus ist abgelaufen!</b></i>";
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">U-Mod von</td><td class=\"tbldata\">";
				if ($arr['user_hmode_from']==0)
					show_timebox("user_hmode_from",time());
				else
					show_timebox("user_hmode_from",$arr['user_hmode_from']);
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">U-Mod bis</td><td class=\"tbldata\">";
				if ($arr['user_hmode_to']==0)
					show_timebox("user_hmode_to",time()+USER_HMODE_DEFAULT_TIME);
				else
					show_timebox("user_hmode_to",$arr['user_hmode_to']);
				echo "</td></tr>";



				
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gel&ouml;schte Multis</td><td class=\"tbldata\"><input type=\"text\" name=\"user_multi_delets\" value=\"".$arr['user_multi_delets']."\" size=\"3\" maxlength=\"3\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Eingetragene Multis</td><td class=\"tbldata\">";
				$multi_res = dbquery("SELECT user_multi_multi_user_id,user_multi_connection FROM ".$db_table['user_multi']." WHERE user_multi_user_id=".$arr['user_id'].";");
				while ($multi_arr = mysql_fetch_array($multi_res))
				{
					echo "<a href=\"?page=user&sub=edit&user_id=".$multi_arr['user_multi_multi_user_id']."\">".get_user_nick($multi_arr['user_multi_multi_user_id'])."</a> (".$multi_arr['user_multi_connection'].")<br>";
				}
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Sittertage</td><td class=\"tbldata\"><input type=\"text\" name=\"user_sitting_days\" value=\"".$arr['user_sitting_days']."\" size=\"3\" maxlength=\"3\" /></td></tr>";

				$sitting_res=dbquery("SELECT user_sitting_sitter_user_id,user_sitting_sitter_ip,user_sitting_date FROM ".$db_table['user_sitting']." WHERE user_sitting_user_id=".$arr['user_id']." AND user_sitting_active='1';");
				if(mysql_num_rows($sitting_res)>0)
				{
					$sitting_arr = mysql_fetch_array($sitting_res);
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Sitter Passwort</td><td class=\"tbldata\"><input type=\"text\" name=\"user_sitting_sitter_password\" value=\"\" size=\"35\" maxlength=\"250\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Sitting Infos</td><td class=\"tbldata\">Sitter: ".get_user_nick($sitting_arr['user_sitting_sitter_user_id'])."<br>Aktiviert am: ".date("d.m.Y H:i",$sitting_arr['user_sitting_date'])."<br><br>Zugriffsdaten:<br>";

                    $date_res = dbquery("
                    SELECT
                        *
                    FROM
                        ".$db_table['user_sitting_date']."
                    WHERE
                        user_sitting_date_user_id='".$arr['user_id']."'
                        AND user_sitting_date_from!=0
                        AND user_sitting_date_to!=0
                    ORDER BY
                        user_sitting_date_from;");

                    while ($date_arr=mysql_fetch_array($date_res))
                    {
                    	echo "Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."<br>";
                    }
                    echo "</td></tr>";

                    echo "<tr><td class=\"tbltitle\" valign=\"top\">Sitting Deaktivieren</td><td class=\"tbldata\">
                    Ja: <input type=\"radio\" name=\"user_sitting_active\" value=\"1\"/>
					Nein: <input type=\"radio\" name=\"user_sitting_active\" value=\"0\" checked=\"checked\"/>";
                    echo "</td></tr>";
				}
				
				
	
				
				
				
				echo "</table>";
				
			echo "</div>";
	

				echo "<script type=\"text/javascript\">
					loadSpecialist(".$st.");
					loadAllianceRanks(".$arr['user_alliance_rank_id'].");
				</script>";

	
					echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=search'\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><hr/>";
				echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=userlog&id=".$arr['user_id']."'\" value=\"Sessions\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=history&id=".$arr['user_id']."'\" value=\"History\" /> ";
				echo "<input type=\"submit\" name=\"delete_user\" value=\"User l&ouml;schen\" style=\"color:#f00\" onclick=\"return confirm('Soll dieser User entg&uuml;ltig gel&ouml;scht werden?');\"> ";
				if ($arr['user_deleted']!=0)
				{
					echo "<input type=\"submit\" name=\"canceldelete\" value=\"Löschantrag aufheben\" style=\"color:".USER_COLOR_DELETED."\" /> ";					
				}
				else
				{
					echo "<input type=\"submit\" name=\"requestdelete\" value=\"Löschantrag erteilen\" style=\"color:".USER_COLOR_DELETED."\" /> ";					
				}				
								
				echo "</form><hr/>";
				if ($arr['user_blocked_from']==0)
					echo "<script>banEnable(false);</script>";
				if ($arr['user_hmode_from']==0)
					echo "<script>umodEnable(false);</script>";
			}
			else
				echo "<i>Datensatz nicht vorhanden!</i>";
		}

		//
		// Suchmaske
		//

		else
		{
			$_SESSION['admin']['user_query']="";
			echo "Suchmaske (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt):<br/><br/>";
			echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Nickname</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";fieldqueryselbox('user_nick');echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Name</td><td class=\"tbldata\"><input type=\"text\" name=\"user_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_name');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">E-Mail</td><td class=\"tbldata\"><input type=\"text\" name=\"user_email\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_email');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Fixe E-Mail</td><td class=\"tbldata\"><input type=\"text\" name=\"user_email_fix\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_email_fix');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Passwort</td><td class=\"tbldata\"><input type=\"text\" name=\"user_password\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">IP-Adresse</td><td class=\"tbldata\"><input type=\"text\" name=\"user_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_ip');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Allianz</td><td class=\"tbldata\"><input type=\"text\" name=\"user_alliance\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'user_alliance','citybox2');\"/> ";fieldqueryselbox('user_alliance');echo "<br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
			$race = get_races_array();
			echo "<tr><td class=\"tbltitle\">Rasse</td><td class=\"tbldata\"><select name=\"user_race_id\">";
			echo "<option value=\"\">(egal)</option>";
			foreach ($race as $id=>$racedata)
				echo "<option value=\"$id\">".$racedata['race_name']."</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Profil-Text</td><td class=\"tbldata\"><input type=\"text\" name=\"user_profile_text\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_profile_text');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Urlaubsmodus</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_hmode\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_hmode\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_hmode\" value=\"1\" /> Ja</td></tr>";
			echo "<tr><td class=\"tbltitle\">Gesperrt</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_blocked\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_blocked\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_blocked\" value=\"1\"  /> Ja</td></tr>";
			echo "<tr><td class=\"tbltitle\">Admin</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_admin\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_admin\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_admin\" value=\"1\"  /> Ja</td></tr>";
			echo "<tr><td class=\"tbltitle\">Bemerkungen</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_comment\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_comment\" value=\"0\" /> Keine &nbsp; <input type=\"radio\" name=\"user_comment\" value=\"1\"  /> Vorhanden</td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"user_search\" value=\"Suche starten\" /></form>";

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['users'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";

		}
	}
?>
