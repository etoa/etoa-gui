<?PHP

			if (isset($_GET['id']))
				$id = $_GET['id'];
			elseif(isset($_GET['user_id']))
				$id = $_GET['user_id'];
			else
				$id = 0;

            $nick = mysql_fetch_row(dbquery("SELECT user_nick FROM users WHERE user_id=".$id.";"))[0];

			// Geänderte Daten speichern
			if (isset($_POST['save']))
			{
                if ($nick !== $_POST['user_nick']) {

                    $user = new User($id);
                    $user->addToUserLog("settings","{nick} hat seinen Namen zu ".$_POST['user_nick']." geändert.",1);
                }

                // Speichert Usertdaten in der Tabelle "users"
				$sql = "UPDATE users SET
				user_name='".$_POST['user_name']."',
				npc='".$_POST['npc']."',
				user_nick='".$_POST['user_nick']."',
				user_email='".$_POST['user_email']."',
				user_password_temp='".$_POST['user_password_temp']."',
				user_email_fix='".$_POST['user_email_fix']."',
        		dual_name ='".$_POST['dual_name']."',
        		dual_email ='".$_POST['dual_email']."',
				user_race_id='".$_POST['user_race_id']."',
				user_alliance_id='".$_POST['user_alliance_id']."',
				user_profile_text='".addslashes($_POST['user_profile_text'])."',
				user_signature='".addslashes($_POST['user_signature'])."',
				user_multi_delets=".$_POST['user_multi_delets'].",
				user_sitting_days=".$_POST['user_sitting_days'].",
				user_chatadmin=".$_POST['user_chatadmin'].",
				admin=".$_POST['admin'].",
				user_ghost=".$_POST['user_ghost'].",
				user_changed_main_planet=".$_POST['user_changed_main_planet'].",
				user_profile_board_url='".$_POST['user_profile_board_url']."',
				user_alliace_shippoints='".$_POST['user_alliace_shippoints']."',
				user_alliace_shippoints_used='".$_POST['user_alliace_shippoints_used']."'";

				if (isset($_POST['user_alliance_rank_id']))
				{
					$sql.= ",user_alliance_rank_id=".intval($_POST['user_alliance_rank_id'])."";
				}
				if (isset($_POST['user_profile_img_check']))
				{
					$sql.= ",user_profile_img_check=0";
				}

				//new Multi
				if (($_POST['new_multi']!="")&&(($_POST['multi_reason']!="")))
            	{
	                if (get_user_id($_POST['new_multi'])==0)
                    {
                        error_msg("Dieser User exisitert nicht!");
                    }
                    //ist der eigene nick eingetragen
                    elseif (get_user_id($_POST['new_multi'])==$_GET['id'])
                    {
                        error_msg("Man kann nicht den selben Nick im Sitting eintragen!");
                    }
                    else
                    {
						$res = dbquery("SELECT * FROM user_multi WHERE user_id=".$_GET['id']."
										AND multi_id =".get_user_id($_POST['new_multi']));
						if (mysql_num_rows($res)==0) {
							dbquery("
							INSERT INTO
								user_multi
							(user_id,multi_id,connection,timestamp)
							VALUES
							(".$_GET['id'].",".get_user_id($_POST['new_multi']).",'".mysql_real_escape_string($_POST['multi_reason'])."',UNIX_TIMESTAMP())");
     					}
						else
						{
							dbquery("
							UPDATE
								user_multi
							SET
								activ=1,
							connection='".mysql_real_escape_string($_POST['multi_reason'])."',
							timestamp=UNIX_TIMESTAMP()
							WHERE
								user_id=".$_GET['id']."
							AND
								multi_id = ".get_user_id($_POST['new_multi']));
						}
						success_msg("Neuer User angelegt!");
	                }
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
					$res = dbquery("SELECT user_profile_img FROM users WHERE user_id=".$id.";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
	          			if (file_exists(PROFILE_IMG_DIR."/".$arr['user_profile_img']))
	          			{
	              			unlink(PROFILE_IMG_DIR."/".$arr['user_profile_img']);
	          			}
	          			$sql.=",user_profile_img=''";
	        		}
        		}

        		// Handle avatar
        		if (isset($_POST['avatar_img_del']) && $_POST['avatar_img_del']==1)
        		{
					$res = dbquery("SELECT user_avatar FROM users WHERE user_id=".$id.";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
	          			if (file_exists(BOARD_AVATAR_DIR."/".$arr['user_avatar']))
	          			{
	              			unlink(BOARD_AVATAR_DIR."/".$arr['user_avatar']);
	          			}
	          			$sql.=",user_avatar=''";
	        		}
        		}

				// Handle password
				if (isset($_POST['user_password']) && $_POST['user_password']!="")
				{
					$pres = dbquery("SELECT user_registered FROM users WHERE user_id='".$id."';");
					$parr = mysql_fetch_row($pres);
					$sql.= ",user_password='".saltPasswort($_POST['user_password'])."'";
					echo "Das Passwort wurde ge&auml;ndert!<br>";
					add_log(8,$cu->nick." ändert das Passwort von ".$_POST['user_nick']."",time());
				}

				// Handle ban
				if ($_POST['ban_enable']==1)
				{
					$ban_from = parseDatePicker('user_blocked_from', $_POST);
					$ban_to = parseDatePicker('user_blocked_to', $_POST);
					$sql.= ",user_blocked_from='".$ban_from."'";
					$sql.= ",user_blocked_to='".$ban_to."'";
					$sql.= ",user_ban_admin_id='".$_POST['user_ban_admin_id']."'";
					$sql.= ",user_ban_reason='".addslashes($_POST['user_ban_reason'])."'";

					$usr = new User($id);

					$usr->addToUserLog("account","{nick} wird von [b]".date("d.m.Y H:i",$ban_from)."[/b] bis [b]".date("d.m.Y H:i",$ban_to)."[/b] gesperrt.\n[b]Grund:[/b] ".addslashes($_POST['user_ban_reason'])."\n[b]Verantwortlich: [/b] ".mysql_fetch_array(dbquery("SELECT user_nick FROM admin_users WHERE user_id = ".$_POST['user_ban_admin_id'] ))['user_nick'],1);
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
                    $usr = new User($id);
                    $usr->activateUmode(true);
					$sql.= ",user_hmode_from='".parseDatePicker('user_hmode_from', $_POST)."'";
					$sql.= ",user_hmode_to='".parseDatePicker('user_hmode_to', $_POST)."'";
				}
				else
				{
                    $usr = new User($id);
                    $usr->removeUmode(true);
				}

				// Perform query
				$sql .= " WHERE user_id='".$id."';";
				dbquery($sql);



				//
				// Speichert Usereinstellungen in der Tabelle "user_properties"
				//

				$sql = "UPDATE user_properties SET
				image_url='".$_POST['image_url']."',
				image_ext='".$_POST['image_ext']."',
				css_style='".$_POST['css_style']."',
				planet_circle_width=".$_POST['planet_circle_width'].",
				item_show='".$_POST['item_show']."',
				image_filter=".$_POST['image_filter'].",
				msgsignature='".addslashes($_POST['msgsignature'])."',
				msgcreation_preview=".$_POST['msgcreation_preview'].",
				msg_preview=".$_POST['msg_preview'].",
				helpbox=".$_POST['helpbox'].",
				notebox=".$_POST['notebox'].",
				msg_copy=".$_POST['msg_copy'].",
				msg_blink=".$_POST['msg_blink'].",
				spyship_id=".$_POST['spyship_id'].",
				spyship_count='".$_POST['spyship_count']."',
				analyzeship_id=".$_POST['analyzeship_id'].",
				analyzeship_count='".$_POST['analyzeship_count']."',
				havenships_buttons=".$_POST['havenships_buttons'].",
				show_adds=".$_POST['show_adds'].",
				fleet_rtn_msg=".$_POST['fleet_rtn_msg']."";

				// Perform query
				$sql .= " WHERE id='".$id."';";
				dbquery($sql);


				if($_POST['del_multi'])
				{
					//Multi löschen
    				foreach ($_POST['del_multi'] as $m_id=>$data)
					{
						$m_id = intval($m_id);

						if ($_POST['del_multi'][$m_id]==1)
						{
							dbquery("UPDATE
								user_multi
							SET
								activ='0',
								timestamp=UNIX_TIMESTAMP()
							WHERE
								user_id=".$_GET['id']."
							AND multi_id=".$_POST['multi_nick'][$m_id]);

							dbquery("UPDATE
								users
							SET
								user_multi_delets=user_multi_delets+1
							WHERE
								user_id=".$_GET['id']);

							success_msg("Eintrag gelöscht!");
						}
					}
				}

				//Sitting löschen
				if($_POST['del_sitting'])
				{
					foreach ($_POST['del_sitting'] as $s_id=>$data)
					{
						$s_id = intval($s_id);

						if ($_POST['del_sitting'][$s_id]==1)
						{
							dbquery("UPDATE
								user_sitting
							SET
								date_to =UNIX_TIMESTAMP()
							WHERE
								id =$s_id");

							success_msg("Eintrag gelöscht!");
						}
					}
				}

				//new sitting
				if ($_POST['sitter_nick']!="")
				{
					if ($_POST['sitter_password1']==$_POST['sitter_password2'] && $_POST['sitter_password1']!="")
					{
						$sitting_from = parseDatePicker('sitting_time_from', $_POST);
						$sitting_to = parseDatePicker('sitting_time_to', $_POST);
						$diff = ceil(($sitting_to - $sitting_from)/86400);
						$pw = saltPasswort($_POST['sitter_password1']);
						$sitterId = get_user_id($_POST['sitter_nick']);

						if($diff>0) {
								if($sitterId<>0) {
								if($diff<=$_POST['user_sitting_days']) {
									dbquery("INSERT INTO user_sitting (
												user_id,sitter_id,password,date_from,date_to
												) VALUES (".
										$_GET['id'].",$sitterId,'$pw',$sitting_from,$sitting_to);");
								}
								else {
									error_msg("So viele Tage sind nicht mehr vorhanden!!");
								}
							}
							else {
								error_msg("Dieser Sitternick exisitert nicht!");
							}
						}
						else {
							error_msg("Enddatum muss größer als Startdatum sein!");
						}
					}
				}
				echo MessageBox::ok("", "&Auml;nderungen wurden &uuml;bernommen!","submitresult");
			}

			// User löschen
			if (isset($_POST['delete_user']))
			{
				$user = new User($id);
				if ($user->delete(false,$cu->nick))
					success_msg("L&ouml;schung erfolgreich!");
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
					user_id=".$id."
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
					user_id=".$id."
				;");
				success_msg("Löschantrag aufgehoben!");
			}

			if (isset($_GET['setverified']))
			{
				dbquery("
				UPDATE
					users
				SET
					verification_key=''
				WHERE
					user_id=".$id."
				;");
				success_msg("Account freigeschaltet!");
			}

			// Fetch all data
			$res = dbquery("
				SELECT
					users.*,
					races.*,
					user_properties.*,
					user_sessionlog.time_action AS time_log,
					user_sessionlog.ip_addr AS ip_log,
					user_sessionlog.user_agent AS agent_log,
					user_sessions.time_action,
					user_sessions.user_agent,
					user_sessions.ip_addr
				FROM
					users
				INNER JOIN
					user_properties
				ON 
					user_id = id
				LEFT JOIN
					races
				ON
					user_race_id = race_id
				LEFT JOIN
					user_sessionlog
				ON
					users.user_id = user_sessionlog.user_id
				LEFT JOIN
					user_sessions
				ON
					users.user_id = user_sessions.user_id
				WHERE
					users.user_id = '".$id."'
				ORDER BY
					user_sessionlog.time_action DESC
				LIMIT 1
				;");
			if (mysql_num_rows($res)>0)
			{
				// Load data
				$arr = mysql_fetch_array($res);

				// Some preparations
				$st = $arr['user_specialist_time']>0 ? $arr['user_specialist_time'] : time();

				$ip = $arr['ip_addr']!=null ? $arr['ip_addr'] : $arr['ip_log'];
				$agent = $arr['user_agent']!=null ? $arr['user_agent'] : $arr['agent_log'];

				// Javascript
				echo "<script type=\"text/javascript\">

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

                $twig->addGlobal('subtitle', "User bearbeiten: ".$arr['user_nick']);

				echo "<form action=\"?page=$page&amp;sub=edit&amp;id=".$id."\" method=\"post\">
				<input type=\"hidden\" id=\"tabactive\" name=\"tabactive\" value=\"\" />";

		echo '<div class="tabs" id="user_edit_tabs">
		<ul>
			<li><a href="#tabs-1">Info</a></li>
			<li><a href="#tabs-2">Account</a></li>
			<li><a href="#tabs-3">Daten</a></li>
			<li><a href="#tabs-4">Sitting</a></li>
			<li><a href="#tabs-5">Profil</a></li>
			<li><a href="#tabs-6">Design</a></li>
			<li><a href="#tabs-7">Nachrichten</a></li>
			<li><a href="#tabs-8">Loginfehler</a></li>
			<li><a href="#tabs-9">Punkte</a></li>
			<li><a href="#tabs-10">Tickets</a></li>
			<li><a href="#tabs-11">Kommentare</a></li>
			<li><a href="#tabs-12">Log</a></li>
			<li><a href="#tabs-13">Wirtschaft</a></li>
		</ul>
		<div id="tabs-1">';


				/**
				* Allgemeines
				*/

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
								<td class=\"tbltitle\">Zulezt online:</td>";
				if ($arr['time_action'])
					echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
				elseif ($arr['time_log'])
					echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['time_log'])."</td>";
				else
					echo "<td class=\"tbldata\">Noch nicht eingeloggt!</td>";
				echo		"</tr>
							<tr>
								<td class=\"tbltitle\">IP/Host:</td>
								<td class=\"tbldata\"><a href=\"?page=user&amp;sub=ipsearch&amp;ip=".$ip."\">".$ip."</a>,
								 <a href=\"?page=user&amp;sub=ipsearch&amp;host=".Net::getHost($ip)."\">".Net::getHost($ip)."</a></td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Agent:</td>
								<td class=\"tbldata\">".$agent."</td>
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
								<td class=\"tbltitle\">Rang:</td>
								<td class=\"tbldata\">".nf($arr['user_rank'])." (aktuell), ".nf($arr['user_rank_highest'])." (max)</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Rohstoffe von...</td>
								<td class=\"tbldata\">
									Raids: ".nf($arr['user_res_from_raid'])." t<br/> 
									Asteroiden: ".nf($arr['user_res_from_asteroid'])." t<br/>
									Nebelfelder: ".nf($arr['user_res_from_nebula'])." t<br/>
                  Trümmerfelder: ".nf($arr['user_res_from_tf'])." t
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Infos:</td>
								<td class=\"tbldata\">";


									if ($arr['user_observe']!="")
									{
										echo "<div>Benutzer steht unter <b>Beobachtung</b>: ".$arr['user_observe']." &nbsp; [<a href=\"?page=user&sub=observed&text=".$id."\">Ändern</a>]</div>";
									}
									if ($arr['user_deleted']!=0)
									{
										echo "<div class=\"userDeletedColor\">Dieser Account ist zur Löschung am ".df($arr['user_deleted'])." vorgemerkt</div>";
									}
									if ($arr['user_hmode_from']>0)
									{
										echo "<div class=\"userHolidayColor\">Dieser Account ist im Urlaubsmodus seit ".df($arr['user_hmode_from'])." bis mindestens ".df($arr['user_hmode_to'])."</div>";
									}
									if ($arr['user_blocked_from']>0 && $arr['user_blocked_to']>time())
									{
										echo "<div class=\"userLockedColor\">Dieser Account ist im gesperrt von ".df($arr['user_blocked_from'])." bis ".df($arr['user_blocked_to']);
										if ($arr['user_ban_reason']!="")
										{
											echo ". Grund: ".stripslashes($arr['user_ban_reason']);
										}
										echo "</div>";
									}
									if ($arr['admin']!=0)
									{
										echo "<div class=\"adminColor\">Dies ist ein Admin-Account!</div>";
									}
									if ($arr['user_ghost']!=0)
									{
										echo "<div class=\"userGhostColor\">Dies ist ein Geist-Account. Er wird nicht in der Statistik angezeigt!</div>";
									}
									if ($arr['user_chatadmin']!=0)
									{
										echo "<div>Dieser User ist ein Chat-Admin.</div>";
									}
									if ($arr['verification_key']!='')
									{
										echo "<div>Die E-Mail Adresse ist nocht nicht bestätigt [<a href=\"?page=$page&sub=$sub&id=$id&setverified\">Freischalten</a>].</div>";
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
										[<a href=\"javascript:;\" onclick=\"$('.tabs').tabs('select', 10);\">Zeigen</a>]
										</div>";
									}

									// Tickets
									$nTickets = Ticket::find(array("user_id"=>$arr['user_id'],"status"=>"new"));
									$nt = count($nTickets);
									$aTickets = Ticket::find(array("user_id"=>$arr['user_id'],"status"=>"assigned"));
									$at = count($aTickets);

									if ($nt+$at > 0)
									{
										echo "<div><b>".$nt." neue Tickets</b> und <b>".$at." zugewiesene Tickets</b> vorhanden
										[<a href=\"javascript:;\" onclick=\"$('.tabs').tabs('select', 9);\">Zeigen</a>]
										</div>";
									}

									// Verwarnungen
									$cres=dbquery("
									SELECT 
										COUNT(warning_id),
										MAX(warning_date)
									FROM 
										user_warnings
									WHERE
										warning_user_id=".$arr['user_id']."
									;");
									$carr = mysql_fetch_row($cres);
									if ($carr[0] > 0)
									{
										echo "<div><b>".$carr[0]." Verwarnungen</b> vorhanden, neuste  von ".df($carr[1])."
										[<a href=\"?page=user&amp;sub=warnings&amp;user=".$id."\">Zeigen</a>]
										</div>";
									}


					echo "</td>
							</tr>";

				echo "</table>";

				echo '</div><div id="tabs-2">';

				/**
				* Account
				*/

				echo "<table class=\"tbl\">";
				echo "<tr>
							<td class=\"tbltitle\">Nick:</td>
							<td class=\"tbldata\">
								<input type=\"text\" name=\"user_nick\" value=\"".$arr['user_nick']."\" size=\"35\" maxlength=\"250\" />
							</td>
							<td>Eine Nickänderung ist grundsätzlich nicht erlaubt</td>
						</tr>
						<tr>
							<td class=\"tbltitle\">E-Mail:</td>
							<td class=\"tbldata\">
								<input type=\"text\" name=\"user_email\" value=\"".$arr['user_email']."\" size=\"35\" maxlength=\"250\" />
							</td>
							<td>Rundmails gehen an diese Adresse</td>
						</tr>
						<tr>
							<td class=\"tbltitle\">Name:</td>
							<td class=\"tbldata\">
								<input type=\"text\" name=\"user_name\" value=\"".$arr['user_name']."\" size=\"35\" maxlength=\"250\" />
							</td>
							<td>Bei Accountübergabe anpassen</td>
						</tr>
						<tr>
							<td class=\"tbltitle\">E-Mail fix:</td>
							<td class=\"tbldata\">
								<input type=\"text\" name=\"user_email_fix\" value=\"".$arr['user_email_fix']."\" size=\"35\" maxlength=\"250\" />
							</td>
							<td>Bei Accountübergabe anpassen</td>
						</tr>
              		</tr>
								<td class=\"tbltitle\">Name Dual:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"dual_name\" value=\"".$arr['dual_name']."\" size=\"35\" maxlength=\"250\" />
								</td>
								<td>Bei Dualänderung anpassen</td>
							</tr>
              		<tr>
								<td class=\"tbltitle\">E-Mail Dual:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"dual_email\" value=\"".$arr['dual_email']."\" size=\"35\" maxlength=\"250\" />
								</td>
								<td>Bei Dualänderung anpassen</td>
							</tr>
             		<tr>
								<td class=\"tbltitle\">Passwort:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_password\" value=\"\" size=\"35\" maxlength=\"250\" />
								</td>
								<td>Leerlassen um altes Passwort beizubehalten</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Temporäres Passwort:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_password_temp\" value=\"".$arr['user_password_temp']."\" size=\"30\" maxlength=\"30\" /> 
								</td>
								<td>Nur dieses wird verwendet, falls ausgefüllt</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Geist:</td>
								<td class=\"tbldata\">
									<input type=\"radio\" name=\"user_ghost\" id=\"user_ghost1\" value=\"1\"";
									if ($arr['user_ghost']==1)
									{
										echo " checked=\"checked\" ";
									}
									echo " /><label for=\"user_ghost1\">Ja</label> 
									<input type=\"radio\" name=\"user_ghost\" id=\"user_ghost0\" value=\"0\" ";
									if ($arr['user_ghost']==0)
									{
										echo " checked=\"checked\" ";
									}
									echo "/><label for=\"user_ghost0\">Nein</label>
								</td>
								<td>Legt fest ob der Spieler in der Rangliste ausgeblendet wird</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Chat-Admin:</td>
								<td class=\"tbldata\">
									<input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin1\" value=\"1\"";
									if ($arr['user_chatadmin']==1)
										echo " checked=\"checked\" ";
									echo " /><label for=\"user_chatadmin1\">Ja</label> 
									<input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin0\"value=\"0\" ";
									if ($arr['user_chatadmin']==0)
										echo " checked=\"checked\" ";
									echo "/><label for=\"user_chatadmin0\">Nein</label><br />
									<input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin2\"value=\"2\" ";
									if ($arr['user_chatadmin']==2)
										echo " checked=\"checked\" ";
									echo "/><label for=\"user_chatadmin2\">Leiter Team Community</label><br />
									<input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin3\"value=\"3\" ";
									if ($arr['user_chatadmin']==3)
										echo " checked=\"checked\" ";
									echo "/><label for=\"user_chatadmin3\">Entwickler mit Adminrechten</label>
									</td>
								<td>Der Spieler hat Adminrechte im Chat und einen silbernen Stern für Chatadmin,
									einen grünen Stern für Leiter Team Community bzw. einen cyanfarbenen Stern für
									Entwickler mit Adminrechten (Entwickler mit Adminrechten funktioniert nur, wenn unten 'Admin' auf 'Ja' gestellt wird).</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">Admin:</td>
								<td class=\"tbldata\">
									<input type=\"radio\" name=\"admin\" id=\"admin1\" value=\"1\"";
									if ($arr['admin']==1)
										echo " checked=\"checked\" ";
									echo " /><label for=\"admin1\">Ja</label> 
									<input type=\"radio\" name=\"admin\" id=\"admin0\" value=\"0\" ";
									if ($arr['admin']==0)
										echo " checked=\"checked\" ";
									echo "/><label for=\"admin0\">Nein</label> 
									<input type=\"radio\" name=\"admin\" id=\"admin2\" value=\"2\" ";
									if ($arr['admin']==2)
										echo " checked=\"checked\" ";
									echo "/><label for=\"admin2\">Entwickler ohne Adminrechte</label>
								</td>
								<td>Admin: Der Spieler wird in der Raumkarte als Game-Admin markiert.<br/>Entwickler: Der Spieler bekommt einen nutzlosen roten Stern im Chat, keine Markierung</td>
							</tr>
							<tr>
								<td class=\"tbltitle\">NPC:</td>
								<td class=\"tbldata\">
									<input type=\"radio\" name=\"npc\" id=\"npc1\" value=\"1\"";
									if ($arr['npc']==1)
										echo " checked=\"checked\" ";
									echo " /><label for=\"npc1\">Ja</label> 
									<input type=\"radio\" name=\"npc\" id=\"npc0\" value=\"0\" ";
									if ($arr['npc']==0)
										echo " checked=\"checked\" ";
									echo "/><label for=\"npc0\">Nein</label> 
								</td>
								<td>Spieler wird als NPC markiert</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Sperren</td>
								<td class=\"tbldata\">
									<input type=\"radio\" name=\"ban_enable\" id=\"ban_enable0\" value=\"0\" onclick=\"$('#ban_options').hide();\"";
									if ($arr['user_blocked_from']==0)
									{
										echo " checked=\"checked\"";
									}
									echo " /><label for=\"ban_enable0\">Nein</label> 
									<input type=\"radio\" name=\"ban_enable\" id=\"ban_enable1\" value=\"1\" onclick=\"$('#ban_options').show();\" ";
									if ($arr['user_blocked_from']>0)
									{
										echo " checked=\"checked\"";
									}
									echo " /><label for=\"ban_enable1\">Ja</label>";
									if ($arr['user_blocked_from']>0 && $arr['user_blocked_to']<time())
									{
										echo " <i><b>Diese Sperre ist abgelaufen!</b></i>";
									}
									echo "<table id=\"ban_options\">
										<tr>
								<td class=\"tbltitle\" valign=\"top\">Von </td>
								<td class=\"tbldata\">";
									showDatepicker("user_blocked_from", $arr['user_blocked_from']>0 ? $arr['user_blocked_from'] : time(), true);
								echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Bis</td>
								<td class=\"tbldata\">";
									showDatepicker("user_blocked_to", $arr['user_blocked_from']>0 ? $arr['user_blocked_to'] : time()+USER_BLOCKED_DEFAULT_TIME, true);
							echo "</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Admin</td>
								<td class=\"tbldata\">
									<select name=\"user_ban_admin_id\" id=\"user_ban_admin_id\">
									<option value=\"0\">(niemand)</option>";
									$tres = dbquery("SELECT * FROM admin_users ORDER BY user_nick;");
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
								<td class=\"tbltitle\" valign=\"top\">Grund</td>
								<td class=\"tbldata\">
									<textarea name=\"user_ban_reason\" id=\"user_ban_reason\" cols=\"45\" rows=\"2\">".stripslashes($arr['user_ban_reason'])."</textarea>
								</td>
							</tr>
							</table>";

							echo "</td>
								<td>Der Benutzer kann sich nicht einloggen und erscheint auf dem Pranger</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">U-Mod</td>
								<td class=\"tbldata\">
									<input type=\"radio\" name=\"umod_enable\" id=\"umod_enable0\" value=\"0\" onclick=\"$('#umod_options').hide();\" checked=\"checked\" /><label for=\"umod_enable0\">Nein</label> 
									<input type=\"radio\" name=\"umod_enable\" id=\"umod_enable1\" value=\"1\" onclick=\"$('#umod_options').show();\" ";
									if ($arr['user_hmode_from']>0)
									{
										echo " checked=\"checked\"";
									}
									echo "/><label for=\"umod_enable1\">Ja</label> ";
									if ($arr['user_hmode_from']>0 && $arr['user_hmode_to']<time())
									{
										echo "<i><b>Dieser Urlaubsmodus ist abgelaufen!</b></i>";
									}
							echo "<table id=\"umod_options\">
									<tr>
										<td class=\"tbltitle\" valign=\"top\">Von</td>
										<td class=\"tbldata\">";
											showDatepicker("user_hmode_from", $arr['user_hmode_from']>0 ? $arr['user_hmode_from'] : time(), true);
							echo "</td>
									</tr>
									<tr>
										<td class=\"tbltitle\" valign=\"top\">Bis</td>
										<td class=\"tbldata\">";
											showDatepicker("user_hmode_to", $arr['user_hmode_to']>0 ? $arr['user_hmode_to'] : time()+USER_HMODE_DEFAULT_TIME, true);
							echo "</td>
									</tr>
									</table>
								</td>
								<td>Der Benutzer kann nichts mehr bauen, wird aber auch nicht angegriffen</td>
							</tr>";


				echo "</table>";

				echo '</div><div id="tabs-3">';


				/**
				* Game-Daten
				*/

				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\">Rasse:</td>
								<td class=\"tbldata\">
									<select name=\"user_race_id\">
									<option value=\"0\">(Keine)</option>";
									$tres = dbquery("SELECT * FROM races ORDER BY race_name;");
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
										specialists
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
									echo "</select> &nbsp; Bis:&nbsp; <span id=\"spt\">-</span>
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
			          	<input type=\"text\" name=\"spyship_count\" maxlength=\"5\" size=\"5\" value=\"".$arr['spyship_count']."\"> &nbsp; ";
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
					        	echo '<select name="spyship_id"><option value="0">(keines)</option>';
					        	while ($sarr=mysql_fetch_array($sres))
					        	{
					        		echo '<option value="'.$sarr['ship_id'].'"';
					        		if ($arr['spyship_id']==$sarr['ship_id'])
					        		 echo ' selected="selected"';
					        		echo '>'.$sarr['ship_name'].'</option>';
					        	}
					        }
					        else
					        {
					        	echo "Momentan steht kein Schiff zur Auswahl!";
					        }
					echo "</td>
							</tr>";
							echo "<tr>
			        	<td class=\"tbltitle\">Analysatoren für Quickanalyse:</td>
			          <td class=\"tbldata\">
			          	<input type=\"text\" name=\"analyzeship_count\" maxlength=\"5\" size=\"5\" value=\"".$arr['analyzeship_count']."\"> &nbsp; ";
						$sres = dbquery("
						SELECT 
			        ship_id, 
			        ship_name
						FROM 
							ships 
						WHERE 
							ship_buildable='1'
							AND (
							ship_actions LIKE '%,analyze'
							OR ship_actions LIKE 'analyze,%'
							OR ship_actions LIKE '%,analyze,%'
							OR ship_actions LIKE 'analyze'
							)
						ORDER BY 
							ship_name ASC");
					        if (mysql_num_rows($sres)>0)
					        {
					        	echo '<select name="analyzeship_id"><option value="0">(keines)</option>';
					        	while ($sarr=mysql_fetch_array($sres))
					        	{
					        		echo '<option value="'.$sarr['ship_id'].'"';
					        		if ($arr['analyzeship_id']==$sarr['ship_id'])
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
								<td class=\"tbltitle\" valign=\"top\">Verfügbare Allianzschiffteile</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_alliace_shippoints\" value=\"".$arr['user_alliace_shippoints']."\" size=\"10\" maxlength=\"10\" />
								</td>
							</tr>
							<tr>
								<td class=\"tbltitle\" valign=\"top\">Verbaute Allianzschiffteile</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"user_alliace_shippoints_used\" value=\"".$arr['user_alliace_shippoints_used']."\" size=\"10\" maxlength=\"10\" />
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
						<td class=\"tbltitle\" valign=\"top\">Sittertage</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"user_sitting_days\" value=\"".$arr['user_sitting_days']."\" size=\"3\" maxlength=\"3\" />
						</td>
					  </tr>";

                // Hauptplanet geändert
                echo "</td>
                        </tr>
                        <tr>
                            <td class=\"tbltitle\" valign=\"top\">Hauptplanet geändert</td>
                            <td class=\"tbldata\">
                                <label><input type=\"radio\" name=\"user_changed_main_planet\" value=\"1\" ".($arr['user_changed_main_planet'] ? "checked" : "")." />&nbsp;Ja</label>&nbsp;
                                <label><input type=\"radio\" name=\"user_changed_main_planet\" value=\"0\" ".(!$arr['user_changed_main_planet'] ? "checked" : "")." />&nbsp;Nein</label>
                            </td>
                        </tr>";

				// Tabelle Ende

					echo "</table>";
					echo "
					<script type=\"text/javascript\">
					loadSpecialist(".$st.");loadAllianceRanks(".$arr['user_alliance_rank_id'].");
					</script>";

				echo '</div><div id="tabs-4">';

				/**
				* Sitting & Multi
				*/

				$multi_res = dbquery("SELECT * FROM user_multi WHERE user_id=".$arr['user_id']." AND activ=1;");
				$del_multi_res = dbquery("SELECT * FROM user_multi WHERE user_id=".$arr['user_id']." AND activ=0;");
				echo '<table class="tb">
						<tr>
							<th rowspan="'.(mysql_num_rows($multi_res)+1).'" valign="top">Eingetragene Multis</th>
							<th>Name</th>
							<th>Begründung</th>
							<th>Eingetragen</th>
							<th>Löschen</th>
						</tr>';
				while ($multi_arr = mysql_fetch_array($multi_res))
				{
					echo '<tr>
							<td>
								<a href="?page=user&sub=edit&user_id='.$multi_arr['multi_id'].'" name="multi_nick"".">'.get_user_nick($multi_arr['multi_id']).'</a>
								<input type="hidden" name="multi_nick['.$multi_arr['multi_id'].']" value="'.$multi_arr['multi_id'].'" readonly="readonly">
							</td>
							<td>
								'.$multi_arr['connection'].'
							</td>
							<td>
								'.($multi_arr['timestamp'] > 0 ? df($multi_arr['timestamp']) : '-').'
							</td>
							<td>
								<input type="checkbox" name="del_multi['.$multi_arr["multi_id"].']" value="1">
							</td>							
						</tr>';
				}
				echo '<tr>
							<th rowspan="'.(mysql_num_rows($del_multi_res)+1).'" valign="top">Gelöschte Multis</th>
							<th>Name</th>
							<th>Begründung</th>
							<th>Gelöscht</th>
							<th></th>
						</tr>';
				while ($del_multi_arr = mysql_fetch_array($del_multi_res))
				{
					echo '<tr>
							<td>
								<a href="?page=user&sub=edit&user_id='.$del_multi_arr['multi_id'].'">'.get_user_nick($del_multi_arr['multi_id']).'</a>
							</td>
							<td>
								'.$del_multi_arr['connection'].'
							</td>
							<td>
								'.($del_multi_arr['timestamp'] > 0 ? df($del_multi_arr['timestamp']) : '-').'
							</td>								
						</tr>';
				}
				echo '</table>';

				$sitting_res = dbquery("SELECT * FROM user_sitting WHERE user_id='".$arr['user_id']."' ORDER BY id DESC;");
				$sitted_res = dbquery("SELECT * FROM user_sitting WHERE sitter_id='".$arr['user_id']."' ORDER BY id DESC;");
				echo '<table class="tb">
						<tr>
							<th rowspan="'.(mysql_num_rows($sitting_res)+1).'" valign="top">Wurde gesittet</th>
							<th>Sitter</th>
							<th>Start</th>
							<th>Ende</th>
							<th>Abbrechen</th>
						</tr>';
				while ($sitting_arr = mysql_fetch_array($sitting_res))
				{
					$used_days += (($sitting_arr['date_to']-$sitting_arr['date_from'])/86400);

					$time = time();
					echo '<tr>
							<td>
								<a href="?page=user&sub=edit&user_id='.$sitting_arr['sitter_id'].'">'.get_user_nick($sitting_arr['sitter_id']).'</a>
							</td>
							<td>
								'.df($sitting_arr['date_from']).'
							</td>
							<td>
								'.df($sitting_arr['date_to']).'
							</td>';
							if ($sitting_arr['date_to']>$time) {
								echo'<td>
										<input type="checkbox" name="del_sitting['.$sitting_arr["id"].']" value="1">
									</td>';
							}
							else {
								echo'<td/>';
							}
						echo'</tr>';
				}

				echo '<tr>
						<th rowspan="'.(mysql_num_rows($sitted_res)+1).'" valign="top">Hat gesittet</th>
						<th>Gesitteter User</th>
						<th>Start</th><br>
						<th>Ende</th>
					</tr>';
				while ($sitted_arr = mysql_fetch_array($sitted_res))
				{
					echo '<tr>
							<td>
								<a href="?page=user&sub=edit&user_id='.$sitted_arr['user_id'].'">'.get_user_nick($sitted_arr['user_id']).'</a>
							</td>
							<td>
								'.df($sitted_arr['date_from']).'
							</td>
							<td>
								'.df($sitted_arr['date_to']).'
							</td>
						</tr>';
				}
				echo '</table>

				<h2>Multi einrichten</h2>

				<table class="tb">
					<tr>
						<td>User</td>
						<td>
							<input type="text" name="new_multi" maxlength="20" size="20"  autocomplete="off" placeholder="Usernick"">
						</td>
					</tr>
					<tr>
						<td>Beziehung</td>
						<td>
							<input type="text" name="multi_reason" maxlength="20" size="20"">
						</td>
					</tr>
				</table>	

				<h2>Sitting einrichten</h2>

				<table class="tb">
					<tr>
						<td>Übrige Tage</td>
						<td>
							'.floor($arr['user_sitting_days']-$used_days).'
						</td>
					</tr>
					<tr>
						<td>Sitter</td>
						<td>
							<input type="text" maxlength="20" size="20" name="sitter_nick">
						</td>
					</tr>
					<tr>
						<td>Passwort</td>
						<td>
							<input type="text" maxlength="20" size="20" name="sitter_password1">
						</td>
					</tr>
					<tr>
						<td>Passwort wiederholen</td>
						<td>
							<input type="text" maxlength="20" size="20" name="sitter_password2">
						</td>
					</tr>
				    <tr>
						<td>Von</td>
						<td>';
							showDatepicker("sitting_time_from", time(), true);
				   echo'</td>
					</tr>
					<tr>
						<td>Bis</td>
					 	<td>';
						    showDatepicker("sitting_time_to", time(), true);
				   echo'</td>
						</tr>
				</table>
				
				</div><div id="tabs-5">';

				/**
				* Profil
				*/

				echo "<table class=\"tb\">";
				echo "<tr>
								<th>Profil-Text:</th>
								<td class=\"tbldata\">
									<textarea name=\"user_profile_text\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_profile_text'])."</textarea>
								</td>
							</tr>
							<tr>
								<th>Profil-Bild:</th>
								<td class=\"tbldata\">";
					      if ($arr['user_profile_img']!="")
					      {
					        if ($arr['user_profile_img_check']==1)
					       	 	echo "<input type=\"checkbox\" value=\"0\" name=\"user_profile_img_check\"> Bild-Verifikation bestätigen<br/>";
					        echo '<img src="'.PROFILE_IMG_DIR.'/'.$arr['user_profile_img'].'" alt="Profil" /><br/>';
					        echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
					      }
					      else
					      {
					      	echo "<i>Keines</i>";
					      }
					echo "</td>
							</tr>
							<tr>
								<th>Board-Signatur:</th>
								<td class=\"tbldata\">
									<textarea name=\"user_signature\" cols=\"60\" rows=\"8\">".stripslashes($arr['user_signature'])."</textarea>
								</td>
							</tr>
							<tr>
								<th>Avatarpfad:</th>
								<td class=\"tbldata\">";
						      if ($arr['user_avatar']!="")
						      {
						        echo '<img src="'.BOARD_AVATAR_DIR.'/'.$arr['user_avatar'].'" alt="Profil" /><br/>';
						        echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_img_del\"> Bild l&ouml;schen<br/>";
						      }
					      else
					      {
					      	echo "<i>Keines</i>";
					      }
					echo "</td>
							</tr>
							<tr>
				      	<th>Öffentliches Foren-Profil:</th>
				      	<td class=\"tbldata\">
				      		<input type=\"text\" name=\"user_profile_board_url\" maxlength=\"200\" size=\"50\" value=\"".$arr['user_profile_board_url']."\">
				      	</td>
				      </tr>";

						echo '<tr><th>Banner:</th><td>';
						$name = Ranking::getUserBannerPath($id);
						if (file_exists($name))
						{
							echo '
							<img src="'.$name.'" alt="Banner"><br>
							Generiert: '.df(filemtime($name)).'<br/>
							<textarea readonly="readonly" rows="2" cols="65">&lt;a href="'.USERBANNER_LINK_URL.'"&gt;&lt;img src="'.$cfg->roundurl.'/'.$name.'" width="468" height="60" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea>
							<textarea readonly="readonly" rows="2" cols="65">[url='.USERBANNER_LINK_URL.'][img]'.$cfg->roundurl.'/'.$name.'[/img][/url]</textarea>';
						}
						echo '</td></tr>';
				echo "</table>";

				echo '</div><div id="tabs-6">';

				/**
				* Design
				*/

				$imagepacks = get_imagepacks();
				$designs = get_designs();

				echo "<table class=\"tbl\" style=\"width:1000px\">";
 				echo "<tr>
			 					<td class=\"tbltitle\">Design:</td>
			 					<td class=\"tbldata\">
			 						<input type=\"text\" name=\"css_style\" id=\"css_style\" size=\"45\" maxlength=\"250\" value=\"".$arr['css_style']."\"> 
			 						&nbsp; <input type=\"button\" onclick=\"document.getElementById('css_style').value = document.getElementById('designSelector').options[document.getElementById('designSelector').selectedIndex].value\" value=\"&lt;&lt;\" /> &nbsp; ";
					        echo "<select id=\"designSelector\">
					        <option value=\"\">(Bitte wählen)</option>";
			            foreach ($designs as $k => $v)
			            {
			                echo "<option value=\"$k\"";
			                if ($arr['css_style']==$k) echo " selected=\"selected\"";
			                echo ">".$v['name']."</option>";
			            }
			            echo "</select>
			          </td>
 							</tr>
 							<tr>
								<td class=\"tbltitle\">Bildpaket / Dateiendung:</td>
								<td class=\"tbldata\">
									<input type=\"text\" name=\"image_url\" id=\"image_url\" size=\"45\" maxlength=\"250\" value=\"".$arr['image_url']."\"> 
									<input type=\"text\" name=\"image_ext\" id=\"image_ext\" value=\"".$arr['image_ext']."\" size=\"3\" maxlength=\"6\" />
			 						&nbsp; <input type=\"button\" onclick=\"
									var imageSetVal = document.getElementById('imageSelector').options[document.getElementById('imageSelector').selectedIndex].value;
									if (imageSetVal!='') {
			 						var ImageSet = imageSetVal.split(':');
			 						document.getElementById('image_url').value=ImageSet[0];
			 						document.getElementById('image_ext').value=ImageSet[1];
									} else {
									document.getElementById('image_url').value='';
			 						document.getElementById('image_ext').value='';
									}
			 						\" value=\"&lt;&lt;\" /> &nbsp; ";
					        echo "<select id=\"imageSelector\">
					        <option value=\"\">(Bitte wählen)</option>";
			            foreach ($imagepacks as $v)
			            {
			            	foreach ($v['extensions'] as $e)
			            	{
			                echo "<option value=\"".$v['path'].":".$e."\"";
			                if ($arr['image_url']==$v['path']) echo " selected=\"selected\"";
			                echo ">".$v['name']." ($e)</option>";
			               }
			            }
			            echo "</select>
			          </td>
			</tr>
             <tr>
                <td class=\"tbltitle\">Planetkreisgr&ouml;sse:</td>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                  <select name=\"planet_circle_width\">";
                  for ($x=450;$x<=700;$x+=50)
                  {
                      echo "<option value=\"$x\"";
                      if ($arr['planet_circle_width']==$x) echo " selected=\"selected\"";
                      echo ">".$x."</option>";
                  }
                echo "</select>
                </td>
            	</tr>
            	<tr>
            		<td class=\"tbltitle\">Schiff/Def Ansicht:</td>
            		<td class=\"tbldata\">
          				<input type=\"radio\" name=\"item_show\" value=\"full\"";
          				if($arr['item_show']=='full' || $arr['item_show']=='') echo " checked=\"checked\"";
          				echo " /> Volle Ansicht  &nbsp; 
           				<input type=\"radio\" name=\"item_show\" value=\"small\"";
          				if($arr['item_show']=='small') echo " checked=\"checked\"";
          				echo " /> Einfache Ansicht 
           			</td>
           		</tr>
           		<tr>
            		<td class=\"tbltitle\">Bildfilter:</td>
            		<td class=\"tbldata\">
          				<input type=\"radio\" name=\"image_filter\" value=\"1\"";
          				if($arr['image_filter']==1) echo " checked=\"checked\"";
          				echo "/> An   &nbsp; 
          				<input type=\"radio\" name=\"image_filter\" value=\"0\"";
          				if($arr['image_filter']==0) echo " checked=\"checked\"";
          				echo "/> Aus
          			</td>
          		</tr>
       				<tr>
          			<td class=\"tbltitle\">Separates Hilfefenster:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"helpbox\" value=\"1\" ";
                    if ($arr['helpbox']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"helpbox\" value=\"0\" ";
                    if ($arr['helpbox']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>
          		<tr>
          			<td class=\"tbltitle\">Separater Notizbox:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"notebox\" value=\"1\" ";
                    if ($arr['notebox']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"notebox\" value=\"0\" ";
                    if ($arr['notebox']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>
          		<tr>
          			<td class=\"tbltitle\">Vertausche Buttons in Hafen-Schiffauswahl:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"havenships_buttons\" value=\"1\" ";
                    if ($arr['havenships_buttons']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"havenships_buttons\" value=\"0\" ";
                    if ($arr['havenships_buttons']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>
          		<tr>
          			<td class=\"tbltitle\">Werbung anzeigen:</td>
          			<td class=\"tbldata\">
                    <input type=\"radio\" name=\"show_adds\" value=\"1\" ";
                    if ($arr['show_adds']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert &nbsp; 
                    <input type=\"radio\" name=\"show_adds\" value=\"0\" ";
                    if ($arr['show_adds']==0) echo " checked=\"checked\"";
          					echo "/> Deaktiviert
            		</td>
          		</tr>";
				echo "</table>";

				echo '</div><div id="tabs-7">';

				/**
				* Messages
				*/

				echo "<table class=\"tbl\">";
				echo "<tr>
								<td class=\"tbltitle\">Nachrichten-Signatur:</td>
								<td class=\"tbldata\">
									<textarea name=\"msgsignature\" cols=\"60\" rows=\"8\">".stripslashes($arr['msgsignature'])."</textarea>
								</td>
							</tr>
							<tr>
	  				 		<td class=\"tbltitle\">Nachrichtenvorschau (Neue/Archiv):</td>
								<td class=\"tbldata\">
		                <input type=\"radio\" name=\"msg_preview\" value=\"1\" ";
		                if ($arr['msg_preview']==1) echo " checked=\"checked\"";
		                echo "/> Aktiviert
		                <input type=\"radio\" name=\"msg_preview\" value=\"0\" ";
		                if ($arr['msg_preview']==0) echo " checked=\"checked\"";
		                echo "/> Deaktiviert
		       			</td>
		       	 </tr>
		       	 <tr>
             		<td class=\"tbltitle\">Nachrichtenvorschau (Erstellen):</td>
          			<td class=\"tbldata\">
                  <input type=\"radio\" name=\"msgcreation_preview\" value=\"1\" ";
                  if ($arr['msgcreation_preview']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"msgcreation_preview\" value=\"0\" ";
                  if ($arr['msgcreation_preview']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              	</td>
           		</tr>
           		<tr>
              	<td class=\"tbltitle\">Blinkendes Nachrichtensymbol:</td>
          			<td class=\"tbldata\">
                  <input type=\"radio\" name=\"msg_blink\" value=\"1\" ";
                  if ($arr['msg_blink']==1) echo " checked=\"checked\"";
                  echo "/> Aktiviert
                  <input type=\"radio\" name=\"msg_blink\" value=\"0\" ";
                  if ($arr['msg_blink']==0) echo " checked=\"checked\"";
                  echo "/> Deaktiviert
              	</td>
           		</tr>
           		<tr>
              	<td class=\"tbltitle\">Text bei Antwort/Weiterleiten kopieren:</td>
	          		<td class=\"tbldata\">
	                <input type=\"radio\" name=\"msg_copy\" value=\"1\" ";
	                if ($arr['msg_copy']==1) echo " checked=\"checked\"";
	                echo "/> Aktiviert
	                <input type=\"radio\" name=\"msg_copy\" value=\"0\" ";
	                if ($arr['msg_copy']==0) echo " checked=\"checked\"";
	                echo "/> Deaktiviert
	              </td>
           		</tr>

							<tr>
	        			<td class=\"tbltitle\">Nachricht bei Transport-/Spionagerückkehr:</td>
	        			<td class=\"tbldata\">
	                  <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"1\" ";
	                  if ($arr['fleet_rtn_msg']==1)
	                  {
	                  	echo " checked=\"checked\"";
	                  }
	                  echo "/> Aktiviert &nbsp;
	              
	                  <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"0\" ";
	                  if ($arr['fleet_rtn_msg']==0)
	                  {
	                  	echo " checked=\"checked\"";
	                  }
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

		echo '</div><div id="tabs-8">';

				/**
				* Loginfailures
				*/

				echo "<table class=\"tbl\">";
				$lres=dbquery("
				SELECT 
					* 
				FROM 
					login_failures 
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
									<th class=\"tbltitle\">Client</th>
								</tr>";
									while ($larr=mysql_fetch_array($lres))
									{
										echo "<tr>
														<td class=\"tbldata\">".df($larr['failure_time'])."</td>
														<td class=\"tbldata\">
															<a href=\"?page=$page&amp;sub=ipsearch&amp;ip=".$larr['failure_ip']."\">".$larr['failure_ip']."</a>
														</td>
														<td class=\"tbldata\">
															<a href=\"?page=$page&amp;sub=ipsearch&amp;host=".Net::getHost($larr['failure_ip'])."\">".Net::getHost($larr['failure_ip'])."</a>
														</td>
														<td class=\"tbldata\">".$larr['failure_client']."</td>
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

				echo '</div><div id="tabs-9">';

				/**
				* Points
				*/

				$cUser = new User($id);

				tableStart("Bewertung");
				echo "<tr>
								<td>Kampfpunkte</td>
								<td>".$cUser->rating->battle."</td>
							</tr>";
				echo "<tr>
								<td>Kämpfe gewonnen/verloren/total</td>
								<td>".$cUser->rating->battlesWon."/".$cUser->rating->battlesLost."/".$cUser->rating->battlesFought."</td>
							</tr>";
				echo "<tr>
								<td>Handelspunkte</td>
								<td>".$cUser->rating->trade."</td>
							</tr>";
				echo "<tr>
								<td>Handel Einkauf/Verkauf</td>
								<td>".$cUser->rating->tradesBuy."/".$cUser->rating->tradesSell."</td>
							</tr>";
				echo "<tr>
								<td>Diplomatiepunkte</td>
								<td>".$cUser->rating->diplomacy."</td>
							</tr>";
				tableEnd();

				// DON'T BUILD IN A FEATURE THAT'S NOT YET AVILABLE
				/*
				echo "<div id=\"pointsBox\">
					<div style=\"text-align:center;\"><img src=\"web/images/ajax-loader-circle.gif\" /><br/>Wird geladen...</div>
				</div>
				";	*/

				echo '</div><div id="tabs-10">';

				/**
				* Tickets
				*/

				echo "<div id=\"ticketsBox\">";

				$tlist = Ticket::find(array('user_id'=> $id));
				if (count($tlist)>0)
				{
					tableStart('Tickets','100%');
					echo "<tr><th>ID</th><th>Status</th><th>Kategorie</th><th>User</th><th>Letzte Änderung</th></tr>";
					foreach ($tlist as $tid => &$ti)
					{
						echo "
						<tr>
							<td>".popupLink('tickets',$ti->idString,'','id='.$tid)."</td>
							<td>".$ti->statusName."</td>
							<td>".$ti->catName."</td>
							<td><a href=\"javascript:;\" ".cTT($ti->userNick,"tt".$ti->id).">".$ti->userNick."</a></td>
							<td>".df($ti->time)."</td>
						</tr>";
					}
					tableEnd();
				}
				else
				{
					echo '<p>Dieser User hat keine Tickets</p>';
				}

					//<div style=\"text-align:center;\"><img src=\"web/images/ajax-loader-circle.gif\" /><br/>Wird geladen...</div>
				echo "</div>";

				echo '</div><div id="tabs-11">';

				/**
				* Kommentare
				*/

				echo "<div id=\"commentsBox\">
					<div style=\"text-align:center;\"><img src=\"web/images/ajax-loader-circle.gif\" /><br/>Wird geladen...</div>
				</div>";

				echo '</div><div id="tabs-12">';

				/**
				* Log
				*/
				echo "<div id=\"logsBox\">
					<div style=\"text-align:center;\"></div>
				</div>";



				echo '</div><div id="tabs-13">';

				/**
				* Wirtschaft
				*/

				echo "
				<div id=\"tabEconomy\">
				Das Laden aller Wirtschaftsdaten kann einige Sekunden dauern!<br/><br/>
				<input type=\"button\" value=\"Wirtschaftsdaten laden\" onclick=\"showLoader('tabEconomy');xajax_loadEconomy(".$arr['user_id'].",'tabEconomy');\" /> 
				</div>";

			echo '
				</div>
			</div>';


				// Buttons
				echo "<p>";
				echo "<input type=\"submit\" name=\"save\" value=\"&Auml;nderungen &uuml;bernehmen\" class=\"positive\" /> &nbsp;";
				if ($arr['user_deleted']!=0)
				{
					echo "<input type=\"submit\" name=\"canceldelete\" value=\"Löschantrag aufheben\" class=\"userDeletedColor\" /> &nbsp;";
				}
				else
				{
					echo "<input type=\"submit\" name=\"requestdelete\" value=\"Löschantrag erteilen\" class=\"userDeletedColor\" /> &nbsp;";
				}
				echo "<input type=\"submit\" name=\"delete_user\" value=\"User l&ouml;schen\" class=\"remove\" onclick=\"return confirm('Soll dieser User entg&uuml;ltig gel&ouml;scht werden?');\"></p>";

				echo "<hr/><p>";
				echo button("Planeten","?page=galaxy&sq=".searchQueryUrl("user_id:=:".$arr['user_id']))." &nbsp;";
				echo button("Gebäude","?page=buildings&sq=".searchQueryUrl("user_nick:=:".$arr['user_nick']))." &nbsp;";
				echo "<input type=\"button\" value=\"Forschungen\" onclick=\"document.location='?page=techs&action=search&query=".searchQuery(array("user_id"=>$arr['user_id']))."'\" /> &nbsp;";
				echo button("Schiffe","?page=ships&sq=".searchQueryUrl("user_nick:=:".$arr['user_nick']))." &nbsp;";
				echo button("Verteidigung","?page=def&sq=".searchQueryUrl("user_nick:=:".$arr['user_nick']))." &nbsp;";
				echo button("Raketen","?page=missiles&sq=".searchQueryUrl("user_nick:=:".$arr['user_nick']))." &nbsp;";
				echo "<input type=\"button\" value=\"IP-Adressen &amp; Hosts\" onclick=\"document.location='?page=user&amp;sub=ipsearch&amp;user=".$arr['user_id']."'\" /></p>";



				echo "<hr/>";
				echo "<p><input type=\"button\" value=\"Spielerdaten neu laden\" onclick=\"document.location='?page=$page&sub=edit&amp;user_id=".$arr['user_id']."'\" /> &nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=search'\" /> &nbsp;";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /></p>";


				echo "</form>";

				if ($arr['user_blocked_from']==0)
					echo "<script>$(function() { $('#ban_options').hide(); });</script>";
				if ($arr['user_hmode_from']==0)
					echo "<script>$(function() { $('#umod_options').hide(); });</script>";

				echo '<script>
					$(function() {
						$( "#user_edit_tabs" ).bind( "tabsshow", function(event, ui) {
							if (ui.index == 6) {
								xajax_showLast5Messages('.$id.',"lastmsgbox");
							} else if (ui.index == 8) {
								xajax_userPointsTable('.$id.',"pointsBox");
							} else if (ui.index == 9) {
								xajax_userTickets('.$id.',"ticketsBox");
							} else if (ui.index == 10) {
								xajax_userComments('.$id.',"commentsBox");
							} else if (ui.index == 11) {
								xajax_userLogs('.$id.',"logsBox");
							}
						});						
					});
				</script>';

			}
			else
			{
				echo "<i>Datensatz nicht vorhanden!</i>";
			}


?>
