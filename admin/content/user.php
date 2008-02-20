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
	if ($sub=="xml")
	{
		require("user/xml.inc.php");
	}

	//
	// Statistics-Graph
	//
	elseif ($sub=="userstats")
	{
		echo "<h1>Userstatistiken</h1>";
		echo "<h2>Online / Registrierte User</h2>";
		if (file_exists(CACHE_ROOT."/out/userstats.png"))
		{
			echo "<img src=\"../cache/out/userstats.png\" alt=\"Userstats\" />";
		}
		else
		{
			error_msg("Run scripts/userstats.php periodically to update the image!",1);			
		}	
	}
	
	//
	// Fehlerhafte Logins
	//
	elseif ($sub=="loginfailures")
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
				if ($_POST['user_nick_search']!="")
				{
					if (stristr($_POST['qmode']['user_nick_search'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick_search']).$_POST['user_nick_search']."$addchars'";
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
				success_msg("Löschantrag gespeichert!");		
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
				success_msg("Löschantrag aufgehoben!");
			}

			// Fetch all data
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['users']." 
			INNER JOIN
        ".$db_table['races']."
        ON user_race_id=race_id
			WHERE 
				user_id='".$_GET['user_id']."';");
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
					document.getElementById('tabComments').style.display='none';
					document.getElementById('tabEconomy').style.display='none';
					
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
				
				
				function toggleText(elemId,switchId)
				{
					if (document.getElementById(switchId).innerHTML=='Anzeigen')
					{
						document.getElementById(elemId).style.display='';	
						document.getElementById(switchId).innerHTML='Verbergen';
					}
					else
					{
						document.getElementById(elemId).style.display='none';					
						document.getElementById(switchId).innerHTML='Anzeigen';
					}		
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
					<a href=\"javascript:;\" onclick=\"showTab('tabComments')\">Kommentare</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabEconomy')\">Wirtschaft</a>
					
					<!--<a href=\"javascript:;\" onclick=\"showTab('tabWarnings')\">Verwarnungen</a>-->
				<br style=\"clear:both;\" />
				</div><br><br>";
				//echo "<div id=\"tabContent\">";
				
				
				
				
				/**
				* Allgemeines
				*/				
				echo "<div id=\"tabGeneral\">";
				
				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\">ID:</td>
								<td class=\"tbldata\">".$arr['user_id']."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Registrierdatum:</td>
								<td class=\"tbldata\">".df($arr['user_registered'])."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Zulezt online:</td>
								<td class=\"tbldata\">".df($arr['user_acttime'])."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">IP:</td>
								<td class=\"tbldata\">".$arr['user_ip']."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Host:</td>
								<td class=\"tbldata\">".$arr['user_hostname']."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Punkte:</td>
								<td class=\"tbldata\">
									".nf($arr['user_points'])." 
									[<a href=\"javascript:;\" onclick=\"toggleBox('pointGraph')\">Verlauf anzeigen</a>]
									<div id=\"pointGraph\" style=\"display:none;\"><img src=\"../misc/stats.image.php?user=".$arr['user_id']."\" alt=\"Diagramm\" /></div>
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Aktueller Rang:</td>
								<td class=\"tbldata\">".nf($arr['user_rank_current'])."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Letzter Rang:</td>
								<td class=\"tbldata\">".nf($arr['user_rank_last'])."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Höchster Rang:</td>
								<td class=\"tbldata\">".nf($arr['user_highest_rank'])."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Rohstoffe von...</td>
								<td class=\"tbldata\">
									Raids: ".nf($arr['user_res_from_raid'])." t<br/> 
									Asteroiden: ".nf($arr['user_res_from_asteroid'])." t<br/>
									Nebelfelder: ".nf($arr['user_res_from_nebula'])." t
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Infos:</td>
								<td class=\"tbldata\">";
									if ($arr['user_deleted']!=0)
									{
										echo "<div style=\"color:".USER_COLOR_DELETED."\">Dieser Account ist zur Löschung am ".df($arr['user_deleted'])." vorgemerkt</div>";
									}						
									if ($arr['user_hmode_from']>0)
									{
										echo "<div style=\"color:".COLOR_UMOD."\">Dieser Account ist im Urlaubsmodus seit ".df($arr['user_hmode_from'])." bis mindestens ".df($arr['user_hmode_to'])."</div>";
									}						
									if ($arr['user_blocked_from']>0 && $arr['user_blocked_to']>time())
									{
										echo "<div style=\"color:".COLOR_BANNED."\">Dieser Account ist im gesperrt von ".df($arr['user_blocked_from'])." bis ".df($arr['user_blocked_to']);
										if ($arr['user_ban_reason']!="")
										{
											echo ". Grund: ".stripslashes($arr['user_ban_reason']);
										}
										echo "</div>";
									}
									$cres=dbquery("
									SELECT 
										COUNT(comment_id),
										MAX(comment_timestamp) 
									FROM 
										user_comments
									WHERE
										comment_user_id=".$arr['user_id']."
									;");	
									$carr = mysql_fetch_row($cres);
									if ($carr[0] > 0)
									{
										echo "<div>Kommentare: ".$carr[0]." vorhanden, neuster Kommentar von ".df($carr[1])."</div>";
									}	
									if ($arr['user_comment']!="")
									{
										echo "<div>Bemerkungen: ".$arr['user_comment']."</div>";
									}							
					echo "</td>
							</tr>";					
				
				echo "</table>";
				echo "</div>";
				
				
				
				/**
				* Daten
				*/
				echo "<div id=\"tabData\" style=\"display:none;\">";
				
				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\">Nick:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_nick\" value=\"".$arr['user_nick']."\" size=\"35\" maxlength=\"250\" /> (Eine Nickänderung ist grundsätzlich nicht erlaubt)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">E-Mail:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_email\" value=\"".$arr['user_email']."\" size=\"35\" maxlength=\"250\" /> (Rundmails gehen an diese Adresse)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Name:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_name\" value=\"".$arr['user_name']."\" size=\"35\" maxlength=\"250\" /> (Bei Accountübergabe anpassen)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">E-Mail fix:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_email_fix\" value=\"".$arr['user_email_fix']."\" size=\"35\" maxlength=\"250\" /> (Bei Accountübergabe anpassen)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Passwort:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_password\" value=\"\" size=\"35\" maxlength=\"250\" /> (Leerlassen um altes Passwort beizubehalten)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Temporäres Passwort:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_password_temp\" value=\"".$arr['user_password_temp']."\" size=\"30\" maxlength=\"30\" /> (Nur dieses wird verwendet, falls ausgefüllt)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Statistikanzeige:</td>
								<td class=\"tbldata\">
									Ja: <input type=\"radio\" name=\"user_show_stats\" value=\"1\"";
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
							</tr>
							<tr>
								<td class=\"tbltitle\">Admin:</td>
								<td class=\"tbldata\">Ja: <input type=\"radio\" name=\"user_admin\" value=\"1\"";
									if ($arr['user_admin']==1)
										echo " checked=\"checked\" ";
									echo " /> Nein: <input type=\"radio\" name=\"user_admin\" value=\"0\" ";
									if ($arr['user_admin']==0)
										echo " checked=\"checked\" ";
									echo "/> (Der Spieler und seine Planeten werden als Admin markiert)
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Interne Bemerkungen:</td>
								<td class=\"tbldata\"><textarea name=\"user_comment\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_comment'])."</textarea></td>
							</tr>";
				echo "</table>";
				
				echo "</div>";
				
				
				
				/**
				* Game-Einstellungen
				*/
				echo "<div id=\"tabGame\" style=\"display:none;\">";
				
				if ($arr['user_specialist_time']>0)
				{
					$st = $arr['user_specialist_time'];
				}
				else
				{
					$st = time();
				}
				
				echo "<script type=\"text/javascript\">
								loadSpecialist(".$st.");
								loadAllianceRanks(".$arr['user_alliance_rank_id'].");
							</script>";
				
				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\">Rasse:</td>
								<td class=\"tbldata\">
									<select name=\"user_race_id\">";
									$tres = dbquery("SELECT * FROM ".$db_table['races']." ORDER BY race_name;");
									while ($tarr = mysql_fetch_array($tres))
									{
										echo "<option value=\"".$tarr['race_id']."\"";
										if ($arr['user_race_id']==$tarr['race_id']) echo " selected=\"selected\"";
										echo ">".$tarr['race_name']."</option>\n";
									}
									echo "</select>				
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" class=\"tbltitle\">Spezialist:</td>
								<td class=\"tbldata\">
									<select name=\"user_specialist_id\" id=\"user_specialist_id\" onchange=\"loadSpecialist(".$st.");\">
									<option value=\"0\">(Keiner)</option>";
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
										{
											echo ' selected="selected"';
										}
										echo '>'.$sarr[0].'</option>';
									}
									echo "</select> &nbsp; Arbeitsbeginn:&nbsp; <span id=\"spt\">-</span>
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Allianz:</td>
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
							</tr>
							<tr>
								<td class=\"tbltitle\">Allianz-Bewerbung:</td>
								<td class=\"tbldata\">
									<div style=\"float:left;margin-right:5px;\">
										<textarea name=\"user_alliance_application\" cols=\"50\" rows=\"3\">".stripslashes($arr['user_alliance_application'])."</textarea>
									</div>
									(Ist dieses Feld nicht leer, ist der Spieler im Bewerbungsmodus und noch kein Mitgleid der Allianz)
									<br style=\"clear:both;\" />
								</td>
							</tr>
							<tr>
			        	<td class=\"tbltitle\">Spionagesonden für Direktscan:</td>
			          <td class=\"tbldata\">
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
					echo "</td>
							</tr>
							<tr>
	        			<td class=\"tbltitle\">Nachricht bei Transport-/Spionagerückkehr:</td>
	        			<td class=\"tbldata\">
	                  <input type=\"radio\" name=\"user_fleet_rtn_msg\" value=\"1\" ";
	                  if ($arr['user_fleet_rtn_msg']==1)
	                  {
	                  	echo " checked=\"checked\"";
	                  }
	                  echo "/> Aktiviert &nbsp;
	              
	                  <input type=\"radio\" name=\"user_fleet_rtn_msg\" value=\"0\" ";
	                  if ($arr['user_fleet_rtn_msg']==0)
	                  {
	                  	echo " checked=\"checked\"";
	                  }
	        					echo "/> Deaktiviert
	        			</td>
      				</tr>"; 
				echo "</table>";
				
				echo "</div>";



				/**
				* Profil
				*/
				echo "<div id=\"tabProfile\" style=\"display:none;\">";
				
				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\">Profil-Text:</td>
								<td class=\"tbldata\">
									<textarea name=\"user_profile_text\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_profile_text'])."</textarea>
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Profil-Bild:</td>
								<td class=\"tbldata\">";
					      if ($arr['user_profile_img']!="")
					      {
					        echo '<img src="../'.PROFILE_IMG_DIR.'/'.$arr['user_profile_img'].'" alt="Profil" /><br/>';
					        echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
					      }				
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Board-Signatur:</td>
								<td class=\"tbldata\">
									<textarea name=\"user_signature\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_signature'])."</textarea>
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Avatarpfad:</td>
								<td class=\"tbldata\">";
						      if ($arr['user_avatar']!="")
						      {
						        echo '<img src="../'.BOARD_AVATAR_DIR.'/'.$arr['user_avatar'].'" alt="Profil" /><br/>';
						        echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_img_del\"> Bild l&ouml;schen<br/>";
						      }							
					echo "</td>
							</tr>
							<tr>
				      	<td class=\"tbltitle\">Öffentliches Foren-Profil:</td>
				      	<td class=\"tbldata\">
				      		<input type=\"text\" name=\"user_profile_board_url\" maxlength=\"200\" size=\"50\" value=\"".$arr['user_profile_board_url']."\">
				      	</td>
				      </tr>";
				echo "</table>";
				
				echo "</div>";
				
				
				
				/**
				* Messages
				*/		
				echo "<div id=\"tabMessages\" style=\"display:none;\">";
				
				echo "<script type=\"text/javascript\">
								xajax_showLast5Messages(".$arr['user_id'].",'lastmsgbox');
							</script>";
				
				echo "<table class=\"tbl\">";		
				echo "<tr>
								<td class=\"tbltitle\">Nachrichten-Signatur:</td>
								<td class=\"tbldata\">
									<textarea name=\"user_msgsignature\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_msgsignature'])."</textarea>
								</td>
							</tr>
							<tr>
	  				 		<td class=\"tbltitle\">Nachrichtenvorschau (Neue/Archiv):</td>
								<td class=\"tbldata\">
		                <input type=\"radio\" name=\"user_msg_preview\" value=\"1\" ";
		                if ($arr['user_msg_preview']==1) echo " checked=\"checked\"";
		                echo "/> Aktiviert
		                <input type=\"radio\" name=\"user_msg_preview\" value=\"0\" ";
		                if ($arr['user_msg_preview']==0) echo " checked=\"checked\"";
		                echo "/> Deaktiviert
		       			</td>
		       	 </tr>
		       	 <tr>
             		<td class=\"tbltitle\">Nachrichtenvorschau (Erstellen):</td>
          			<td class=\"tbldata\">
                  <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"1\" ";
                  if ($arr['user_msgcreation_preview']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"0\" ";
                  if ($arr['user_msgcreation_preview']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              	</td>
           		</tr>
           		<tr>
              	<td class=\"tbltitle\">Blinkendes Nachrichtensymbol:</td>
          			<td class=\"tbldata\">
                  <input type=\"radio\" name=\"user_msg_blink\" value=\"1\" ";
                  if ($arr['user_msg_blink']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"user_msg_blink\" value=\"0\" ";
                  if ($arr['user_msg_blink']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              	</td>
           		</tr>
           		<tr>
              	<td class=\"tbltitle\">Text bei Antwort/Weiterleiten kopieren:</td>
	          		<td class=\"tbldata\">
	                <input type=\"radio\" name=\"user_msg_copy\" value=\"1\" ";
	                if ($arr['user_msg_copy']==1) echo " checked=\"checked\"";
	                echo "/> Aktiviert
	                <input type=\"radio\" name=\"user_msg_copy\" value=\"0\" ";
	                if ($arr['user_msg_copy']==0) echo " checked=\"checked\"";
	                echo "/> Deaktiviert
	              </td>
           		</tr>
           		<tr>
           			<td colspan=\"2\" class=\"tabSeparator\"></td>
           		</tr>
           		<tr>
          			<td class=\"tbltitle\">Nachricht senden:</td>
			      		<td class=\"tbldata\">
		        			Titel: <input type=\"text\" id=\"urgendmsgsubject\" maxlength=\"200\" size=\"50\" />
		        			<input type=\"button\" onclick=\"xajax_sendUrgendMsg(".$arr['user_id'].",document.getElementById('urgendmsgsubject').value,document.getElementById('urgentmsg').value)\" value=\"Senden\" /><br/>
									Text: <textarea id=\"urgentmsg\" cols=\"60\" rows=\"4\"></textarea>
			          </td>
       				</tr>";
       	echo "</table><br><br>";
       	
       	echo "<h2>Letzte 5 Nachrichten</h2>";
       	echo "<input type=\"button\" onclick=\"xajax_showLast5Messages(".$arr['user_id'].",'lastmsgbox');\" value=\"Neu laden\" /><br><br>";
       	echo "<div id=\"lastmsgbox\">Lade...</div>";
				
				echo "</div>";

				
				
				/**
				* Design
				*/								
				echo "<div id=\"tabDesign\" style=\"display:none;\">";
				
				$imagepacks = get_imagepacks("../");
				$designs = get_designs("../");
				
				echo "<table class=\"tbl\">";
 				echo "<tr>
			 					<td class=\"tbltitle\">Design:</td>
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
			            echo "</select>
			          </td>
 							</tr>
 							<tr>
								<td class=\"tbltitle\">Bildpaket / Dateiendung:</td>
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
			            echo "</select>
			          </td>
							</tr>
							<tr>
                <td class=\"tbltitle\">Spielgrösse: (nur alte Designs)</td>
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
             </tr>
             <tr>
                <td class=\"tbltitle\">Planetkreisgr&ouml;sse:</td>
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
            	</tr>
            	<tr>
            		<td class=\"tbltitle\">Schiff/Def Ansicht:</td>
            		<td class=\"tbldata\">
          				<input type=\"radio\" name=\"user_item_show\" value=\"full\"";
          				if($arr['user_item_show']=='full') echo " checked=\"checked\"";
          				echo " /> Volle Ansicht  &nbsp; 
           				<input type=\"radio\" name=\"user_item_show\" value=\"small\"";
          				if($arr['user_item_show']=='small') echo " checked=\"checked\"";
          				echo " /> Einfache Ansicht
           			</td>
           		</tr>
           		<tr>
            		<td class=\"tbltitle\">Bildfilter:</td>
            		<td class=\"tbldata\">
          				<input type=\"radio\" name=\"user_image_filter\" value=\"1\"";
          				if($arr['user_image_filter']==1) echo " checked=\"checked\"";
          				echo "/> An   &nbsp; 
          				<input type=\"radio\" name=\"user_image_filter\" value=\"0\"";
          				if($arr['user_image_filter']==0) echo " checked=\"checked\"";
          				echo "/> Aus
          			</td>
          		</tr>
       				<tr>
          			<td class=\"tbltitle\">Separates Hilfefenster:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"user_helpbox\" value=\"1\" ";
                    if ($arr['user_helpbox']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"user_helpbox\" value=\"0\" ";
                    if ($arr['user_helpbox']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>
          		<tr>
          			<td class=\"tbltitle\">Separater Notizbox:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"user_notebox\" value=\"1\" ";
                    if ($arr['user_notebox']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"user_notebox\" value=\"0\" ";
                    if ($arr['user_notebox']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>
          		<tr>
          			<td class=\"tbltitle\">Vertausche Buttons in Hafen-Schiffauswahl:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"user_havenships_buttons\" value=\"1\" ";
                    if ($arr['user_havenships_buttons']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"user_havenships_buttons\" value=\"0\" ";
                    if ($arr['user_havenships_buttons']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>
          		<tr>
          			<td class=\"tbltitle\">Werbung anzeigen:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"user_show_adds\" value=\"1\" ";
                    if ($arr['user_show_adds']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"user_show_adds\" value=\"0\" ";
                    if ($arr['user_show_adds']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>";				
				echo "</table>";
				
				echo "</div>";
				
				
				
				/**
				* Loginfailures
				*/		
				echo "<div id=\"tabFailures\" style=\"display:none;\">";
				
				echo "<table class=\"tbl\">";			
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
					echo "<tr>
									<th class=\"tbltitle\">Zeit</th>
									<th class=\"tbltitle\">IP-Adresse</th>
									<th class=\"tbltitle\">Hostname</th>
								</tr>";
									while ($larr=mysql_fetch_array($lres))
									{
										echo "<tr>
														<td class=\"tbldata\">".df($larr['failure_time'])."</td>
														<td class=\"tbldata\">".$larr['failure_ip']." 
															[<a href=\"?page=user&amp;action=search&amp;special=ip&amp;val=".base64_encode($larr['failure_ip'])."\">Suche Spieler</a>] 
															[<a href=\"?page=user\">Suche Session</a>]
														</td>
														<td class=\"tbldata\">".$larr['failure_host']."
															[<a href=\"?page=user&amp;action=search&amp;special=host&amp;val=".base64_encode($larr['failure_host'])."\">Suche Spieler</a>]
														</td>
													</tr>";
									}
				}
				else
				{
					echo "<tr>
									<td class=\"tbldata\">Keine fehlgeschlagenen Logins</td>
								</tr>";
				}
				echo "</table>";
				
				echo "</div>";
				
				
	
				/**
				* Points
				*/
				echo "<div id=\"tabPoints\" style=\"display:none;\">Laden...</div>";	
				
				echo "<script type=\"text/javascript\">
								xajax_userPointsTable(".$arr['user_id'].",'tabPoints');
							</script>";
							
							

				/**
				* Tickets
				*/				
				echo "<div id=\"tabTickets\" style=\"display:none;\">Laden...</div>";	
				
				echo "<script type=\"text/javascript\">
								xajax_userTickets(".$arr['user_id'].",'tabTickets');
							</script>";



				/**
				* Kommentare
				*/				
				echo "<div id=\"tabComments\" style=\"display:none;\">Laden...</div>";	
				
				echo "<script type=\"text/javascript\">
								xajax_userComments(".$arr['user_id'].",'tabComments');
							</script>";
				
				
				
				/**
				* Account
				*/				
				echo "<div id=\"tabAccount\" style=\"display:none;\">";
				
				echo "<table class=\"tbl\">";
				// Sperrung
				echo "<tr>
								<td class=\"tbltitle\" valign=\"top\">Sperren</td>
								<td class=\"tbldata\">
									Nein:<input type=\"radio\" name=\"ban_enable\" value=\"0\" onclick=\"banEnable(false);\"";
									if ($arr['user_blocked_from']==0)
									{
										echo " checked=\"checked\"";
									}
									echo " /> Ja:<input type=\"radio\" name=\"ban_enable\" value=\"1\" onclick=\"banEnable(true);\" ";
									if ($arr['user_blocked_from']>0)
									{
										echo " checked=\"checked\"";
									}
									echo " />";
									if ($arr['user_blocked_from']>0 && $arr['user_blocked_to']<time())
									{
										echo " <i><b>Diese Sperre ist abgelaufen!</b></i>";
									}	
									
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Gesperrt von </td>
								<td class=\"tbldata\">";
									if ($arr['user_blocked_from']==0)
									{
										show_timebox("user_blocked_from",time());
									}
									else
									{
										show_timebox("user_blocked_from",$arr['user_blocked_from']);
									}
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Gesperrt bis</td>
								<td class=\"tbldata\">";
									if ($arr['user_blocked_to']==0)
									{
										show_timebox("user_blocked_to",time()+USER_BLOCKED_DEFAULT_TIME);
									}
									else
									{
										show_timebox("user_blocked_to",$arr['user_blocked_to']);
									}
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Gesperrt von</td>
								<td class=\"tbldata\">
									<select name=\"user_ban_admin_id\" id=\"user_ban_admin_id\">
									<option value=\"0\">(niemand)</option>";
									$tres = dbquery("SELECT * FROM ".$db_table['admin_users']." ORDER BY user_nick;");
									while ($tarr = mysql_fetch_array($tres))
									{
										echo "<option value=\"".$tarr['user_id']."\"";
										if ($arr['user_ban_admin_id']==$tarr['user_id']) echo " selected=\"selected\"";
										echo ">".$tarr['user_nick']."</option>\n";
									}
									echo "</select>
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Sperrgrund</td>
								<td class=\"tbldata\">
									<textarea name=\"user_ban_reason\" id=\"user_ban_reason\" cols=\"60\" rows=\"2\">".stripslashes($arr['user_ban_reason'])."</textarea>
								</td>
							</tr>";
				// Urlaubsmodus
				echo "<tr>
								<td class=\"tbltitle\" valign=\"top\">U-Mod</td>
								<td class=\"tbldata\">
									Nein:<input type=\"radio\" name=\"umod_enable\" value=\"0\" onclick=\"umodEnable(false);\" checked=\"checked\" /> Ja:<input type=\"radio\" name=\"umod_enable\" value=\"1\" onclick=\"umodEnable(true);\" ";
									if ($arr['user_hmode_from']>0)
									{
										echo " checked=\"checked\"";
									}
									echo "/>";
									if ($arr['user_hmode_from']>0 && $arr['user_hmode_to']<time())
									{
										echo "<i><b>Dieser Urlaubsmodus ist abgelaufen!</b></i>";
									}
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">U-Mod von</td>
								<td class=\"tbldata\">";
									if ($arr['user_hmode_from']==0)
									{
										show_timebox("user_hmode_from",time());
									}
									else
									{
										show_timebox("user_hmode_from",$arr['user_hmode_from']);
									}
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">U-Mod bis</td>
								<td class=\"tbldata\">";
									if ($arr['user_hmode_to']==0)
									{
										show_timebox("user_hmode_to",time()+USER_HMODE_DEFAULT_TIME);
									}
									else
									{
										show_timebox("user_hmode_to",$arr['user_hmode_to']);
									}
					echo "</td>
							</tr>";
				// Multis & Sitting
				echo "<tr>
								<td class=\"tbltitle\" valign=\"top\">Gel&ouml;schte Multis</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_multi_delets\" value=\"".$arr['user_multi_delets']."\" size=\"3\" maxlength=\"3\" />
								</td>
								</tr>
								<tr>
									<td class=\"tbltitle\" valign=\"top\">Eingetragene Multis</td>
									<td class=\"tbldata\">";
									$multi_res = dbquery("SELECT user_multi_multi_user_id,user_multi_connection FROM ".$db_table['user_multi']." WHERE user_multi_user_id=".$arr['user_id'].";");
									while ($multi_arr = mysql_fetch_array($multi_res))
									{
										echo "<a href=\"?page=user&sub=edit&user_id=".$multi_arr['user_multi_multi_user_id']."\">".get_user_nick($multi_arr['user_multi_multi_user_id'])."</a> (".$multi_arr['user_multi_connection'].")<br>";
									}
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Sittertage</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_sitting_days\" value=\"".$arr['user_sitting_days']."\" size=\"3\" maxlength=\"3\" />
								</td>
							</tr>";

							$sitting_res=dbquery("
							SELECT 
								user_sitting_sitter_user_id,
								user_sitting_sitter_ip,
								user_sitting_date 
							FROM 
								".$db_table['user_sitting']." 
							WHERE 
								user_sitting_user_id='".$arr['user_id']."' 
								AND user_sitting_active='1';");
							if(mysql_num_rows($sitting_res)>0)
							{
								$sitting_arr = mysql_fetch_array($sitting_res);
								
								echo "<tr>
												<td class=\"tbltitle\" valign=\"top\">Sitter Passwort</td>
												<td class=\"tbldata\">
													<input type=\"text\" name=\"user_sitting_sitter_password\" value=\"\" size=\"35\" maxlength=\"250\" />
												</td>
											</tr>
											<tr>
												<td class=\"tbltitle\" valign=\"top\">Sitting Infos</td>
												<td class=\"tbldata\">
													Sitter: ".get_user_nick($sitting_arr['user_sitting_sitter_user_id'])."<br>Aktiviert am: ".date("d.m.Y H:i",$sitting_arr['user_sitting_date'])."<br><br>Zugriffsdaten:<br>";
			
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
			            echo "</td>
			            		</tr>
			            		<tr>
			            			<td class=\"tbltitle\" valign=\"top\">Sitting Deaktivieren</td>
			            			<td class=\"tbldata\">
			                    Ja: <input type=\"radio\" name=\"user_sitting_active\" value=\"1\"/>
													Nein: <input type=\"radio\" name=\"user_sitting_active\" value=\"0\" checked=\"checked\"/>
												</td></tr>";
							}
				echo "</table>";
				
				echo "</div>";
				
				
				
				/**
				* Wirtschaft
				*/
				echo "<div id=\"tabEconomy\" style=\"display:none;\">";
				
				// Stopt Ladedauer
				$tmr = timerStart();
				
				//
				// Rohstoff- und Produktionsübersicht
				//
								
				// Sucht alle Planet IDs des Users
				$pres = dbquery("
					SELECT 
						planet_id
					FROM 
						planets
					WHERE
						planet_user_id='".$arr['user_id']."'");
				if(mysql_num_rows($pres)>0)
				{ 
					infobox_start("Rohstoff- und Produktionsübersicht",0);
					echo "<div align=\"center\">";
					echo "<table class=\"tbc\">";
					echo "<tr>
									<td class=\"tbldata2\">Minimum</td>
									<td class=\"tbldata3\">Maximum</td>
									<td class=\"tbldata\" style=\"font-style:italic\">Speicher bald voll</td>
									<td class=\"tbldata\" style=\"font-weight:bold\">Speicher voll</td>
								</tr>";
					echo "</table>";
					echo "</div><br><br>";
					
					
					// Läd alle "Planetclass" Daten in ein Array
					$planets = array();
					while($parr=mysql_fetch_array($pres))
					{
						$planets[] = new Planet($parr['planet_id']);
					}
			
					$cnt_res=0;
					$max_res=array(0,0,0,0,0,0);
					$min_res=array(9999999999,9999999999,9999999999,9999999999,9999999999,9999999999);
					$tot_res=array(0,0,0,0,0,0);
				
					$cnt_prod=0;
					$max_prod=array(0,0,0,0,0,0);
					$min_prod=array(9999999999,9999999999,9999999999,9999999999,9999999999,9999999999);
					$tot_prod=array(0,0,0,0,0,0);
					foreach ($planets as $p)
					{
						//Speichert die aktuellen Rohstoffe in ein Array
						$val_res[$p->id][0]=floor($p->res->metal);
						$val_res[$p->id][1]=floor($p->res->crystal);
						$val_res[$p->id][2]=floor($p->res->plastic);
						$val_res[$p->id][3]=floor($p->res->fuel);
						$val_res[$p->id][4]=floor($p->res->food);
						$val_res[$p->id][5]=floor($p->people);
				
						for ($x=0;$x<6;$x++)
						{
							$max_res[$x]=max($max_res[$x],$val_res[$p->id][$x]);
							$min_res[$x]=min($min_res[$x],$val_res[$p->id][$x]);
							$tot_res[$x]+=$val_res[$p->id][$x];
						}
				
						//Speichert die aktuellen Rohstoffproduktionen in ein Array
						$val_prod[$p->id][0]=floor($p->prod->metal);
						$val_prod[$p->id][1]=floor($p->prod->crystal);
						$val_prod[$p->id][2]=floor($p->prod->plastic);
						$val_prod[$p->id][3]=floor($p->prod->fuel);
						$val_prod[$p->id][4]=floor($p->prod->food);
						$val_prod[$p->id][5]=floor($p->prod->people);
				
						for ($x=0;$x<6;$x++)
						{
							$max_prod[$x]=max($max_prod[$x],$val_prod[$p->id][$x]);
							$min_prod[$x]=min($min_prod[$x],$val_prod[$p->id][$x]);
							$tot_prod[$x]+=$val_prod[$p->id][$x];
						}
				
						//Speichert die aktuellen Speicher in ein Array
						$val_store[$p->id][0]=floor($p->store->metal);
						$val_store[$p->id][1]=floor($p->store->crystal);
						$val_store[$p->id][2]=floor($p->store->plastic);
						$val_store[$p->id][3]=floor($p->store->fuel);
						$val_store[$p->id][4]=floor($p->store->food);
						$val_store[$p->id][5]=floor($p->people_place);
				
						//Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)
				
						//Titan
						if($p->prod->metal>0)
						{
				      if ($p->store->metal - $p->res->metal > 0)
				      {
				      	$val_time[$p->id][0]=ceil(($p->store->metal-$p->res->metal)/$p->prod->metal*3600);
				      }
				      else
				      {
				        $val_time[$p->id][0]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][0]=0;
				    }
				    
						//Silizium
						if($p->prod->crystal>0)
						{
				      if ($p->store->crystal - $p->res->crystal > 0)
				      {
				      	$val_time[$p->id][1]=ceil(($p->store->crystal-$p->res->crystal)/$p->prod->crystal*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][1]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][1]=0;
				    }
				    
						//PVC
						if($p->prod->plastic>0)
						{
				      if ($p->store->plastic - $p->res->plastic > 0)
				      {
				        $val_time[$p->id][2]=ceil(($p->store->plastic-$p->res->plastic)/$p->prod->plastic*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][2]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][2]=0;
				    }
				    
						//Tritium
						if($p->prod->fuel>0)
						{
				      if ($p->store->fuel - $p->res->fuel > 0)
				      {
				       	$val_time[$p->id][3]=ceil(($p->store->fuel-$p->res->fuel)/$p->prod->fuel*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][3]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][3]=0;
				    }
				    
						//Nahrung
						if($p->prod->food>0)
						{
					    if ($p->store->food - $p->res->food > 0)
					    {
					      $val_time[$p->id][4]=ceil(($p->store->food-$p->res->food)/$p->prod->food*3600);
					    }
					    else
					   	{
					    	$val_time[$p->id][4]=0;
					    }
				    }
				    else
				    {
				    	$val_time[$p->id][4]=0;
				    }
				
						//Bewohner
						if($p->prod->people>0)
						{
				      if ($p->people_place - $p->people > 0)
				      {
				        $val_time[$p->id][5]=ceil(($p->people_place-$p->people)/$p->prod->people*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][5]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][5]=0;
				    }
					}
				
				
					//
					// Rohstoffe/Bewohner und Speicher
					//
				
					echo "<h2>Rohstoffe und Bewohner</h2>";
					echo "<table class=\"tbl\">";
					echo "<tr>
									<td class=\"tbltitle\">Name:</td>
									<td class=\"tbltitle\">".RES_METAL."</td>
									<td class=\"tbltitle\">".RES_CRYSTAL."</td>
									<td class=\"tbltitle\">".RES_PLASTIC."</td>
									<td class=\"tbltitle\">".RES_FUEL."</td>
									<td class=\"tbltitle\">".RES_FOOD."</td>
									<td class=\"tbltitle\">Bewohner</td>
								</tr>";
					foreach ($planets as $p)
					{
						echo "<tr>
										<td class=\"tbldata\">
											<a href=\"?page=galaxy&sub=edit&planet_id=".$p->id."\">".$p->name."</a>
										</td>";
						for ($x=0;$x<6;$x++)
						{
							echo "<td";
							if ($max_res[$x]==$val_res[$p->id][$x])
							{
								echo " class=\"tbldata3\"";
							}
							elseif ($min_res[$x]==$val_res[$p->id][$x])
							{
								 echo " class=\"tbldata2\"";
							}
							else
							{
								 echo " class=\"tbldata\"";
							}
				
				
							//Der Speicher ist noch nicht gefüllt
							if($val_res[$p->id][$x]<$val_store[$p->id][$x] && $val_time[$p->id][$x]!=0)
							{
								echo " ".tm("Speicher","Speicher voll in ".tf($val_time[$p->id][$x])."")." ";
								if ($val_time[$p->id][$x]<43200)
								{
									echo " style=\"font-style:italic;\" ";
								}
								echo ">".nf($val_res[$p->id][$x])."</td>";
							}
							//Speicher Gefüllt
							else
							{
								echo " ".tm("Speicher","Speicher voll!")."";
								echo " style=\"\" ";
								echo "><b>".nf($val_res[$p->id][$x])."</b></td>";
							}
				
						}
						echo "</tr>";
						$cnt_res++;
					}
					echo "<tr>
									<td colspan=\"6\"></td>
								</tr>
								<tr>
									<td class=\"tbltitle\">Total</td>";
					for ($x=0;$x<6;$x++)
					{
						echo "<td class=\"tbltitle\">".nf($tot_res[$x])."</td>";
					}
					echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
					for ($x=0;$x<6;$x++)
					{
						echo "<td class=\"tbltitle\">".nf($tot_res[$x]/$cnt_res)."</td>";
					}
					echo "</tr>";
					echo "</table>";
				
				
				
					//
					// Rohstoffproduktion inkl. Energie
					//
				
					// Ersetzt Bewohnerwerte durch Energiewerte
					$max_prod[5] = 0;
					$min_prod[5] = 9999999999;
					$tot_prod[5] = 0;
					foreach ($planets as $p)
					{
						//Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
						$val_prod[$p->id][5]=floor($p->prod->power);
						
						// Gibt Min. / Max. aus
						$max_prod[5]=max($max_prod[5],$val_prod[$p->id][5]);
						$min_prod[5]=min($min_prod[5],$val_prod[$p->id][5]);
						$tot_prod[5]+=$val_prod[$p->id][5];	
					}
				
				
				
				
					echo "<h2>Produktion</h2>";
					echo "<table class=\"tbl\">";
					echo "<tr><th class=\"tbltitle\">Name:</th>
					<th class=\"tbltitle\">".RES_METAL."</th>
					<th class=\"tbltitle\">".RES_CRYSTAL."</th>
					<th class=\"tbltitle\">".RES_PLASTIC."</th>
					<th class=\"tbltitle\">".RES_FUEL."</th>
					<th class=\"tbltitle\">".RES_FOOD."</th>
					<th class=\"tbltitle\">Energie</th></tr>";
					foreach ($planets as $p)
					{
						echo "<tr><td class=\"tbldata\"><a href=\"?page=economy&amp;planet_id=".$p->id."\">".$p->name."</a></td>";
						for ($x=0;$x<6;$x++)
						{
							// Erstellt TM-Box für jeden Rohstoff
							// Titan
							if($x == 0)
							{
								$tm_header = "Titan-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_metal']."<br\>".$p->type->name.": ".$p->type->metal."<br\>".$p->sol_type_name.": ".$p->sol->type->metal."";
							}
							elseif($x == 1)
							{
								$tm_header = "Silizium-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_crystal']."<br\>".$p->type->name.": ".$p->type->crystal."<br\>".$p->sol_type_name.": ".$p->sol->type->crystal."";
							}
							elseif($x == 2)
							{
								$tm_header = "PVC-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_plastic']."<br\>".$p->type->name.": ".$p->type->plastic."<br\>".$p->sol_type_name.": ".$p->sol->type->plastic."";
							}
							elseif($x == 3)
							{
								$tm_header = "Tritium-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_fuel']."<br\>".$p->type->name.": ".$p->type->fuel."<br\>".$p->sol_type_name.": ".$p->sol->type->fuel."";
							}
							elseif($x == 4)
							{
								$tm_header = "Nahrungs-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_food']."<br\>".$p->type->name.": ".$p->type->food."<br\>".$p->sol_type_name.": ".$p->sol->type->food."";
							}
							elseif($x == 5)
							{
								$tm_header = "Energie-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_power']."<br\>".$p->type->name.": ".$p->type->power."<br\>".$p->sol_type_name.": ".$p->sol->type->power."";
							}
							else
							{
								$tm_header = "";
								$tm = "";
							}
							
							
							echo "<td";
							if ($max_prod[$x]==$val_prod[$p->id][$x])
							{
								echo " class=\"tbldata3\"";
							}
							elseif ($min_prod[$x]==$val_prod[$p->id][$x])
							{
								 echo " class=\"tbldata2\"";
							}
							else
							{
								 echo " class=\"tbldata\"";
							}
							echo " ".tm($tm_header,$tm).">".nf($val_prod[$p->id][$x])."</td>";
						}
						echo "</tr>";
						$cnt_prod++;
					}
					echo "<tr><td colspan=\"6\"></td></tr>";
					echo "<tr><th class=\"tbltitle\">Total</th>";
					for ($x=0;$x<6;$x++)
						echo "<td class=\"tbltitle\">".nf($tot_prod[$x])."</td>";
					echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
					for ($x=0;$x<6;$x++)
						echo "<td class=\"tbltitle\">".nf($tot_prod[$x]/$cnt_prod)."</td>";
					echo "</tr>";
					echo "</table><br><br>";
					
					infobox_end(0);
				}
				else
				{
					infobox_start("Rohstoff- und Produktionsübersicht");
					echo "Der User hat noch keinen Planeten!";
					infobox_end();
				}

				//
				// 5 letzte Bauaufträge
				//
				
				$lbres = dbquery("
				SELECT 
					b.building_name,
					log.logs_game_id,
					log.logs_game_building_id,
					log.logs_game_text,
					log.logs_game_build_type,
					log.logs_game_timestamp
				FROM 
						(
							logs_game AS log
						INNER JOIN
							buildings AS b
						ON
							log.logs_game_building_id=b.building_id
						)
					INNER JOIN
						logs_game_cat AS cat
					ON
						log.logs_game_cat=cat.logs_game_cat_id
						AND cat.logs_game_cat_id='1'
						AND log.logs_game_user_id='".$arr['user_id']."'
				ORDER BY 
					log.logs_game_timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lbres)>0)
				{ 
					infobox_start("5 letzte Bauaufträge",1);
					echo "<tr>
									<td class=\"tbltitle\" style=\"width:25%\">Zeit</td>
									<td class=\"tbltitle\" style=\"width:30%\">Gebäude</td>
									<td class=\"tbltitle\" style=\"width:30%\">Aktion</td>
									<td class=\"tbltitle\" style=\"width:15%\">Anzeigen</td>
								</tr>";
								
								
					while ($lbarr = mysql_fetch_array($lbres))
					{
						$text = encode_logtext($lbarr['logs_game_text']);
						echo "<tr>
										<td class=\"tbldata\">".date("Y-m-d H:i:s",$lbarr['logs_game_timestamp'])."</td>
										<td class=\"tbldata\">".$lbarr['building_name']."</td>
										<td class=\"tbldata\">";
											if($lbarr['logs_game_build_type']==1)
											{
												echo "Ausbau";
											}
											elseif($lbarr['logs_game_build_type']==2)
											{
												echo "Abriss";
											}
											else
											{
												echo "Abbruch";
											}		
							echo "</td>
										<td class=\"tbldata\">
											<a href=\"javascript:;\" id=\"buildings_".$lbarr['logs_game_id']."\" onclick=\"toggleText('".$lbarr['logs_game_id']."','buildings_".$lbarr['logs_game_id']."')\">Anzeigen</a>
										</td>
									</tr>
									</tr>
										<td class=\"tbldata\" id=\"".$lbarr['logs_game_id']."\" style=\"display:none;\" colspan=\"4\">".$text."</td>
									</tr>"; 
					}
					
					infobox_end(1);
				}
				else
				{
					infobox_start("5 letzte Bauaufträge");
					echo "Es sind keine Logs vorhanden!";
					infobox_end();
				}
				
				
				//
				// 5 letzte Forschungsaufträge
				//
				
				$lres = dbquery("
				SELECT 
					t.tech_name,
					log.logs_game_id,
					log.logs_game_tech_id,
					log.logs_game_text,
					log.logs_game_build_type,
					log.logs_game_timestamp
				FROM 
						(
							logs_game AS log
						INNER JOIN
							technologies AS t
						ON
							log.logs_game_tech_id=t.tech_id
						)
					INNER JOIN
						logs_game_cat AS cat
					ON
						log.logs_game_cat=cat.logs_game_cat_id
						AND cat.logs_game_cat_id='2'
						AND log.logs_game_user_id='".$arr['user_id']."'
				ORDER BY 
					log.logs_game_timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lres)>0)
				{ 
					infobox_start("5 letzte Forschungsaufträge",1);
					echo "<tr>
									<td class=\"tbltitle\" style=\"width:25%\">Zeit</td>
									<td class=\"tbltitle\" style=\"width:30%\">Forschung</td>
									<td class=\"tbltitle\" style=\"width:30%\">Aktion</td>
									<td class=\"tbltitle\" style=\"width:15%\">Anzeigen</td>
								</tr>";
								
								
					while ($larr = mysql_fetch_array($lres))
					{
						$text = encode_logtext($larr['logs_game_text']);
						
						echo "<tr>
										<td class=\"tbldata\">".date("Y-m-d H:i:s",$larr['logs_game_timestamp'])."</td>
										<td class=\"tbldata\">".$larr['tech_name']."</td>
										<td class=\"tbldata\">";
											if($larr['logs_game_build_type']==1)
											{
												echo "Ausbau";
											}
											else
											{
												echo "Abbruch";
											}
							echo "</td>
										<td class=\"tbldata\">
											<a href=\"javascript:;\" id=\"tech_".$larr['logs_game_id']."\" onclick=\"toggleText('".$larr['logs_game_id']."','tech_".$larr['logs_game_id']."')\">Anzeigen</a>
										</td>
									</tr>
									</tr>
										<td class=\"tbldata\" id=\"".$larr['logs_game_id']."\" style=\"display:none;\" colspan=\"4\">".$text."</td>
									</tr>"; 
					}
					
					infobox_end(1);
				}
				else
				{
					infobox_start("5 letzte Forschungsaufträge");
					echo "Es sind keine Logs vorhanden!";
					infobox_end();
				}
				
				
				//
				// 5 letzte Schiffsaufträge
				//
				
				$lres = dbquery("
				SELECT 
					log.logs_game_id,
					log.logs_game_text,
					log.logs_game_build_type,
					log.logs_game_timestamp
				FROM 
						logs_game AS log
					INNER JOIN
						logs_game_cat AS cat
					ON
						log.logs_game_cat=cat.logs_game_cat_id
						AND cat.logs_game_cat_id='3'
						AND log.logs_game_user_id='".$arr['user_id']."'
				ORDER BY 
					log.logs_game_timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lres)>0)
				{ 
					infobox_start("5 letzte Schiffsaufträge",1);
					echo "<tr>
									<td class=\"tbltitle\" style=\"width:45%\">Zeit</td>
									<td class=\"tbltitle\" style=\"width:40%\">Aktion</td>
									<td class=\"tbltitle\" style=\"width:15%\">Anzeigen</td>
								</tr>";
								
								
					while ($larr = mysql_fetch_array($lres))
					{
						$text = encode_logtext($larr['logs_game_text']);
						
						echo "<tr>
										<td class=\"tbldata\">".date("Y-m-d H:i:s",$larr['logs_game_timestamp'])."</td>
										<td class=\"tbldata\">";
										if($larr['logs_game_build_type']==1)
											{
												echo "Neuer Auftrag";
											}
											else
											{
												echo "Abbruch";
											}
							echo "</td>
										<td class=\"tbldata\">
											<a href=\"javascript:;\" id=\"ship_".$larr['logs_game_id']."\" onclick=\"toggleText('".$larr['logs_game_id']."','ship_".$larr['logs_game_id']."')\">Anzeigen</a>
										</td>
									</tr>
									</tr>
										<td class=\"tbldata\" id=\"".$larr['logs_game_id']."\" style=\"display:none;\" colspan=\"3\">".$text."</td>
									</tr>"; 
					}
					
					infobox_end(1);
				}
				else
				{
					infobox_start("5 letzte Schiffsaufträge");
					echo "Es sind keine Logs vorhanden!";
					infobox_end();
				}
				
				
				
				//
				// 5 letzte Verteidigungsaufträge
				//
				
				$lres = dbquery("
				SELECT 
					log.logs_game_id,
					log.logs_game_text,
					log.logs_game_build_type,
					log.logs_game_timestamp
				FROM 
						logs_game AS log
					INNER JOIN
						logs_game_cat AS cat
					ON
						log.logs_game_cat=cat.logs_game_cat_id
						AND cat.logs_game_cat_id='4'
						AND log.logs_game_user_id='".$arr['user_id']."'
				ORDER BY 
					log.logs_game_timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lres)>0)
				{ 
					infobox_start("5 letzte Verteidigungsaufträge",1);
					echo "<tr>
									<td class=\"tbltitle\" style=\"width:45%\">Zeit</td>
									<td class=\"tbltitle\" style=\"width:40%\">Aktion</td>
									<td class=\"tbltitle\" style=\"width:15%\">Anzeigen</td>
								</tr>";
								
								
					while ($larr = mysql_fetch_array($lres))
					{
						$text = encode_logtext($larr['logs_game_text']);
						
						echo "<tr>
										<td class=\"tbldata\">".date("Y-m-d H:i:s",$larr['logs_game_timestamp'])."</td>
										<td class=\"tbldata\">";
										if($larr['logs_game_build_type']==1)
											{
												echo "Neuer Auftrag";
											}
											else
											{
												echo "Abbruch";
											}
							echo "</td>
										<td class=\"tbldata\">
											<a href=\"javascript:;\" id=\"def_".$larr['logs_game_id']."\" onclick=\"toggleText('".$larr['logs_game_id']."','def_".$larr['logs_game_id']."')\">Anzeigen</a>
										</td>
									</tr>
									</tr>
										<td class=\"tbldata\" id=\"".$larr['logs_game_id']."\" style=\"display:none;\" colspan=\"3\">".$text."</td>
									</tr>"; 
					}
					
					infobox_end(1);
				}
				else
				{
					infobox_start("5 letzte Schiffsaufträge");
					echo "Es sind keine Logs vorhanden!";
					infobox_end();
				}
				
				echo "Wirtschaftsseite geladen in ".timerStop($tmr)." sec<br/>";
				
				echo "</div>";
				
				
				// Buttons
				echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=search'\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><hr/>";
				echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=userlog&id=".$arr['user_id']."'\" value=\"Sessions\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=history&id=".$arr['user_id']."'\" value=\"History\" /> ";
				//echo "<input type=\"submit\" name=\"delete_user\" value=\"User l&ouml;schen\" style=\"color:#f00\" onclick=\"return confirm('Soll dieser User entg&uuml;ltig gel&ouml;scht werden?');\"> ";
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
			{
				echo "<i>Datensatz nicht vorhanden!</i>";
			}
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
