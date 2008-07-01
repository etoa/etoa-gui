<?PHP

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
				if (isset($_POST['user_alliance_rank_id']))
				{
					$sql.= ",user_alliance_rank_id=".intval($_POST['user_alliance_rank_id'])."";
				}
				if (isset($_POST['user_profile_img_check']))
				{
					$sql.= ",user_profile_img_check=0";
				}
				
				

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
        if (isset($_POST['profile_img_del']) && $_POST['profile_img_del']==1)
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
        if (isset($_POST['avatar_img_del']) && $_POST['avatar_img_del']==1)
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
				if (isset($_POST['user_password']) && $_POST['user_password']!="")
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
				cms_ok_msg("&Auml;nderungen wurden &uuml;bernommen!","submitresult");

				//Aktuelles Sitten Stoppen
				if(isset($_POST['user_sitting_active']) && $_POST['user_sitting_active']==1)
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
				if (isset($_POST['user_sitting_sitter_password']) && $_POST['user_sitting_sitter_password']!="")
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
				users
			LEFT JOIN
        races
        ON user_race_id=race_id
			WHERE 
				user_id='".$_GET['user_id']."';");
			if (mysql_num_rows($res)>0)
			{
				// Load data				
				$arr = mysql_fetch_array($res);

				// Some preparations
				$st = $arr['user_specialist_time']>0 ? $arr['user_specialist_time'] : time();
				
				// Javascript				
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
					document.getElementById('tabactive').value=idx;
					if (document.getElementById('submitresult'))
					{
						document.getElementById('submitresult').style.display='none';
					}
					
					if (idx=='tabGame')
					{
						loadSpecialist(".$st.");loadAllianceRanks(".$arr['user_alliance_rank_id'].");
					}
					else if (idx=='tabMessages')
					{
						xajax_showLast5Messages(".$arr['user_id'].",'lastmsgbox');
					}
					else if (idx=='tabPoints')
					{
						xajax_userPointsTable(".$arr['user_id'].",'tabPoints');
					}
					else if (idx=='tabTickets')
					{
						xajax_userTickets(".$arr['user_id'].",'tabTickets');
					}
					else if (idx=='tabComments')
					{
						xajax_userComments(".$arr['user_id'].",'tabComments');
					}
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
			
				echo "<h2>Details <span style=\"color:#0f0;\">".$arr['user_nick']."</span> ".cb_button("add_user=".$arr['user_id']."")."</h2>";

				echo "<form action=\"?page=$page&amp;sub=edit&amp;user_id=".$_GET['user_id']."\" method=\"post\">
				<input type=\"hidden\" id=\"tabactive\" name=\"tabactive\" value=\"\" />";

				
				// Show the navigation
				echo "<div id=\"tabNav\">
					<a href=\"javascript:;\" onclick=\"showTab('tabGeneral')\">Allgemeines</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabData');\">Daten</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabAccount')\">Account</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabProfile')\">Profil</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabGame');\">Spiel</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabMessages');\">Nachrichten</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabDesign')\">Design</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabFailures')\">Loginfehler</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabPoints');\">Punkte</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabTickets');\">Tickets</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabComments');\">Kommentare</a>
					<a href=\"javascript:;\" onclick=\"showTab('tabEconomy')\">Wirtschaft</a>
					
					<!--<a href=\"javascript:;\" onclick=\"showTab('tabWarnings')\">Verwarnungen</a>-->
				<br style=\"clear:both;\" />
				</div><br>";
				//echo "<div id=\"tabContent\">";
				
				
				
				
				/**
				* Allgemeines
				*/				
				echo "<div id=\"tabGeneral\">";
				
				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\" style=\"width:180px;\">ID:</td>
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
								<td class=\"tbldata\">".nf($arr['user_rank'])."</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Höchster Rang:</td>
								<td class=\"tbldata\">".nf($arr['user_rank_highest'])."</td>
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
									
									// Kommentare
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
										echo "<div><b>".$carr[0]." Kommentare</b> vorhanden, neuster Kommentar von ".df($carr[1])."
										[<a href=\"javascript:;\" onclick=\"showTab('tabComments');\">Zeigen</a>]
										</div>";
									}	
									
									// Bemerkung
									if ($arr['user_comment']!="")
									{
										echo "<div><b>Bemerkungen:</b> ".$arr['user_comment']." [<a href=\"javascript:;\" onclick=\"showTab('tabData');\">Ändern</a>]</div>";
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
							</tr>";
							echo "<tr>
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
							ship_buildable='1'
							AND (
							ship_actions LIKE '%,spy'
							OR ship_actions LIKE 'spy,%'
							OR ship_actions LIKE '%,spy,%'
							OR ship_actions LIKE 'spy'
							)
						ORDER BY 
							ship_name ASC");
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
					        if ($arr['user_profile_img_check']==1)
					       	 	echo "<input type=\"checkbox\" value=\"0\" name=\"user_profile_img_check\"> Bild-Verifikation bestätigen<br/>";
					        echo '<img src="../'.PROFILE_IMG_DIR.'/'.$arr['user_profile_img'].'" alt="Profil" /><br/>';
					        echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
					      }				
					      else
					      {
					      	echo "<i>Keines</i>";
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
					      else
					      {
					      	echo "<i>Keines</i>";
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
       	echo "</table><br/>";
       	
       	echo "<h2>Letzte 5 Nachrichten</h2>";
       	echo "<input type=\"button\" onclick=\"showLoader('lastmsgbox');xajax_showLast5Messages(".$arr['user_id'].",'lastmsgbox');\" value=\"Neu laden\" /><br><br>";
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
				echo "<div id=\"tabPoints\" style=\"display:none;\">
					<div style=\"text-align:center;\"><img src=\"../images/loadingmiddle.gif\" /><br/>Wird geladen...</div>
				</div>";	
				
				/**
				* Tickets
				*/				
				echo "<div id=\"tabTickets\" style=\"display:none;\">
					<div style=\"text-align:center;\"><img src=\"../images/loadingmiddle.gif\" /><br/>Wird geladen...</div>
				</div>";	
				

				/**
				* Kommentare
				*/				
				echo "<div id=\"tabComments\" style=\"display:none;\">
					<div style=\"text-align:center;\"><img src=\"../images/loadingmiddle.gif\" /><br/>Wird geladen...</div>
				</div>";	
				
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

					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Kampfpunkte</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_points_battle\" value=\"".$arr['user_points_battle']."\" size=\"3\" maxlength=\"5\" />
								</td>
							</tr>";
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Handelspunkte</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_points_trade\" value=\"".$arr['user_points_trade']."\" size=\"3\" maxlength=\"5\" />
								</td>
							</tr>";
					echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Diplomatiepunkte</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_points_diplomacy\" value=\"".$arr['user_points_diplomacy']."\" size=\"3\" maxlength=\"5\" />
								</td>
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
				
				echo "Das Laden aller Wirtschaftsdaten kann einige Sekunden dauern!<br/><br/>
				<input type=\"button\" value=\"Wirtschaftsdaten laden\" onclick=\"showLoader('tabEconomy');xajax_loadEconomy(".$arr['user_id'].",'tabEconomy');\" /> ";
				
				echo "</div>";
				
				
				// Buttons
				echo "<br/>";
	
				echo "<input type=\"submit\" name=\"save\" value=\"&Auml;nderungen &uuml;bernehmen\" style=\"color:#0f0\" /> &nbsp;";
				if ($arr['user_deleted']!=0)
				{
					echo "<input type=\"submit\" name=\"canceldelete\" value=\"Löschantrag aufheben\" style=\"color:".USER_COLOR_DELETED."\" /> &nbsp;";					
				}
				else
				{
					echo "<input type=\"submit\" name=\"requestdelete\" value=\"Löschantrag erteilen\" style=\"color:".USER_COLOR_DELETED."\" /> &nbsp;";					
				}				
				echo "<input type=\"submit\" name=\"delete_user\" value=\"User l&ouml;schen\" style=\"color:#f00\" onclick=\"return confirm('Soll dieser User entg&uuml;ltig gel&ouml;scht werden?');\"> ";
				
				echo "<hr/>";
				echo "<input type=\"button\" value=\"Planeten\" onclick=\"document.location='?page=galaxy&action=search&query=".searchQuery(array("planet_user_id"=>$arr['user_id']))."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Gebäude\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Forschungen\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Schiffe\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Verteidigung\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Raketen\" onclick=\"document.location='?page=messages&sub=sendmsg&user_id=".$arr['user_id']."'\" /> ";
				
				echo "<hr/>";
				echo "<input type=\"button\" value=\"Spielerdaten neu laden\" onclick=\"document.location='?page=$page&sub=edit&amp;user_id=".$arr['user_id']."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=search'\" /> &nbsp;";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";

								
				echo "</form>";
				
				if ($arr['user_blocked_from']==0)
					echo "<script>banEnable(false);</script>";
				if ($arr['user_hmode_from']==0)
					echo "<script>umodEnable(false);</script>";
				
				if(isset($_POST['tabactive']) && $_POST['tabactive']!="")
				{
					echo "<script>showTab('".$_POST['tabactive']."');</script>";
					
				}
					
			}
			else
			{
				echo "<i>Datensatz nicht vorhanden!</i>";
			}


?>