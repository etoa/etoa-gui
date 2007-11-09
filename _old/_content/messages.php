<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: messages.php													//
	// Topic: Nachrichten-Modul		 									//
	// Version: 0.1																	//
	// Letzte Änderung: 01.10.2004									//
	//////////////////////////////////////////////////

	// DATEN LADEN


	// DEFINITIONEN //

	define(USER_MSG_CAT_ID,1);
	define(BAN_HOURS,$conf['msg_ban_hours']['v']);
	define(FLOOD_CONTROL,$conf['msg_flood_control']['v']);

	$wordbanlist=explode(";",$conf['wordbanlist']['v']);
	$wordwhitelist=explode(";",$conf['wordbanlist']['p1']);

	// BEGIN SKRIPT //

	if ($_GET['mode']!="") $mode=$_GET['mode']; else $mode="messages";

	echo "<h1>Nachrichten</h1>";

	// Menü

	show_tab_menu("mode",array("messages"=>"Neue","old"=>"Gelesene","archiv"=>"Archiv","new"=>"Erstellen","sent"=>"Gesendet"));
	//echo "<br>"; this <br> sucks here ;), this menu is thought as a tab menu like in windows dialog fields
	echo "<br>"; //The "split" sucks -.-. IE dosnt show it right with the borders and the space is in the whole game :P and in the whole game are spaces -> buildings, research, shipyard, defense etc.  *g*

	if ($mode=="new")
	{
		if ($_POST['submit']!="" && checker_verify())
		{
			$uid = get_user_id(rawurldecode($_POST['message_user_to']));
			if ($uid>0)
			{
				$flood_interval = time()-FLOOD_CONTROL;
				if (mysql_num_rows(dbquery("SELECT message_id FROM ".$db_table['messages']." WHERE message_user_from='".$_SESSION[ROUNDID]['user']['id']."' AND message_user_to=$uid AND message_timestamp>$flood_interval;"))>0)
				{
					infobox_start("Nachrichtenversand",1);
					echo "<tr><td class=\"tbldata\"><b>Flood-Kontrolle!</b> Du kannst erst nach ".FLOOD_CONTROL." Sekunden eine neue Nachricht an ".$_POST['message_user_to']." schreiben!</td></tr>";
					infobox_end(1);
					echo "<input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onClick=\"history.back();\">";
				}
				else
				{
					$check_subject=check_illegal_signs($_POST['message_subject']);

					if($check_subject=="")
					{
         	    dbquery("INSERT INTO ".$db_table['messages']." (message_user_from,message_user_to,message_timestamp,message_cat_id,message_subject,message_text) VALUES ('".$_SESSION[ROUNDID]['user']['id']."','$uid',".time().",".USER_MSG_CAT_ID.",'".addslashes($_POST['message_subject'])."','".addslashes($_POST['message_text'])."');");
         	    infobox_start("Nachrichtenversand",1);
         	    echo "<tr><td class=\"tbldata\">Nachricht wurde gesendet!</td></tr>";
         	    infobox_end(1);
         	    echo "<input type=\"button\" class=\"button\" value=\"Neue Nachricht schreiben\" onClick=\"document.location='?page=$page&mode=new'\">";
         	}
         	else
         	{
         		echo "Du hast ein unerlaubtes Zeichen ( ".$check_subject." ) im Betreff!";
         	}
				}
			}
			else
			{
				infobox_start("Nachrichtenversand",1);
				echo "<tr><td class=\"tbldata\"><b>Fehler:</b>: Dieser Benutzer existiert nicht!</td></tr>";
				infobox_end(1);
				echo "<input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onClick=\"history.back();\">";
			}
		}
		else
		{
			$res = dbquery("SELECT user_msgcreation_preview FROM ".$db_table['users']." WHERE user_id=".$_SESSION[ROUNDID]['user']['id'].";");
			$arr = mysql_fetch_row($res);
			$msgpreview = $arr[0];

			echo "<form action=\"?page=$page&mode=".$mode."\" method=\"POST\" name=\"msgform\">";
			checker_init();
			echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
			echo "<tr>
			     	<td class=\"tbltitle\" colspan=\"3\">Neue Nachricht</td>
				</tr>";
			echo "<tr>
					<td class=\"tbltitle\" width=\"50\" valign=\"top\">Empf&auml;nger:</td>
					<td class=\"tbldata\" width=\"250\"  colspan=\"2\">
						<input type=\"text\" name=\"message_user_to\" id=\"user_nick\" autocomplete=\"off\" value=\"";
						if (intval($_GET['message_user_to'])!="") echo get_user_nick(intval($_GET['message_user_to']));
					echo "\" size=\"30\" maxlength=\"255\" onkeyup=\"xajax_searchUser(this.value);\"><br/>
					<div id=\"citybox\">&nbsp;</div>

					</td>
			     </tr>";
			echo "<tr>
					<td class=\"tbltitle\" width=\"50\" valign=\"top\">Betreff:</td>
					<td class=\"tbldata\" width=\"250\" colspan=\"2\">
						<input type=\"text\" name=\"message_subject\" value=\"".($_GET['message_subject'])."\" size=\"30\" maxlength=\"255\">
					</td>
			     </tr>";
				echo "<tr>
					<td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:</td>
					<td class=\"tbldata\" width=\"250\"><textarea name=\"message_text\" id=\"message\" rows=\"10\" cols=\"60\" ";
					if ($msgpreview==1)
						echo "onkeyup=\"xajax_messagesNewMessagePreview(this.value)\"";
					echo ">";

	        //Fügt Signatur ein wenn vorhanden
	        $res=dbquery("SELECT user_msgsignature FROM users WHERE user_id=".$_SESSION[ROUNDID]['user']['id'].";");
	        $arr=mysql_fetch_array($res);
	        if ($arr['user_msgsignature']!="") echo $arr['user_msgsignature'];

					if ($msgpreview==1)
						$prevstr="xajax_messagesNewMessagePreview(document.getElementById('message').value)";
					else
						$prevstr="";

					echo "</textarea></td><td class=\"tbldata\">
					<input type=\"button\" onclick=\"bbcode(this.form,'b','');$prevstr\" value=\"B\" style=\"font-weight:bold;\">
					<input type=\"button\" onclick=\"bbcode(this.form,'i','');$prevstr\" value=\"I\" style=\"font-style:italic;\">
					<input type=\"button\" onclick=\"bbcode(this.form,'u','');$prevstr\" value=\"U\" style=\"text-decoration:underline\">
					<input type=\"button\" onclick=\"bbcode(this.form,'c','');$prevstr\" value=\"Center\" style=\"text-align:center\"> <br/><br/>
					<input type=\"button\" onclick=\"namedlink(this.form,'url');$prevstr\" value=\"Link\">
					<input type=\"button\" onclick=\"namedlink(this.form,'email');$prevstr\" value=\"E-Mail\">
					<input type=\"button\" onclick=\"bbcode(this.form,'img','http://');$prevstr\" value=\"Bild\"> <br/><br/>";
					?>
					<select id="sizeselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'size');<?PHP echo $prevstr;?>">
					  <option value="0">Gr&ouml;sse</option>
					  <option value="7">winzig</option>
						<option value="10">klein</option>
						<option value="12">mittel</option>
						<option value="16">groß</option>
						<option value="20">riesig</option>
					</select>
					<select id="colorselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'color');<?PHP echo $prevstr;?>">
					  <option value="0">Farbe</option>
					  <option value="skyblue" style="color: skyblue;">sky blue</option>
						<option value="royalblue" style="color: royalblue;">royal blue</option>
						<option value="blue" style="color: blue;">blue</option>
						<option value="darkblue" style="color: darkblue;">dark-blue</option>
						<option value="orange" style="color: orange;">orange</option>
						<option value="orangered" style="color: orangered;">orange-red</option>

						<option value="crimson" style="color: crimson;">crimson</option>
						<option value="red" style="color: red;">red</option>
						<option value="firebrick" style="color: firebrick;">firebrick</option>
						<option value="darkred" style="color: darkred;">dark red</option>
						<option value="green" style="color: green;">green</option>
						<option value="limegreen" style="color: limegreen;">limegreen</option>
						<option value="seagreen" style="color: seagreen;">sea-green</option>
						<option value="deeppink" style="color: deeppink;">deeppink</option>
						<option value="tomato" style="color: tomato;">tomato</option>

						<option value="coral" style="color: coral;">coral</option>
						<option value="purple" style="color: purple;">purple</option>
						<option value="indigo" style="color: indigo;">indigo</option>
						<option value="burlywood" style="color: burlywood;">burlywood</option>
						<option value="sandybrown" style="color: sandybrown;">sandy brown</option>
						<option value="sienna" style="color: sienna;">sienna</option>
						<option value="chocolate" style="color: chocolate;">chocolate</option>
						<option value="teal" style="color: teal;">teal</option>
						<option value="silver" style="color: silver;">silver</option>
					</select>
				<?php
					echo "</td>";
			echo "</tr>";
			if ($msgpreview==1)
			echo "<tr>
					<td class=\"tbltitle\">Vorschau:</td>
					<td class=\"tbldata\" colspan=\"2\" id=\"msgPreview\">Vorschau wird geladen...
				</td></tr>";
			echo "</table>";
			if ($msgpreview==1)
				echo "<script type=\"text/javascript\">xajax_messagesNewMessagePreview(document.getElementById('message').value)</script>";
			echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Senden\" onclick=\"if (document.getElementById('message_user_to').value=='') {window.alert('Empf&auml;nger fehlt!');document.getElementById('message_user_to').focus();return false;}\"></p>";
			echo "</form>";
		}
	}
	elseif ($mode=="sent")
	{
		if (intval($_GET['msg_id'])>0)
		{
			$mres = dbquery("
			SELECT
                m.message_subject,
                m.message_timestamp,
                m.message_user_to,
                m.message_text,
                m.message_read
			FROM
				".$db_table['messages']." AS m
			WHERE
                m.message_id='".intval($_GET['msg_id'])."'
                AND m.message_user_from='".$_SESSION[ROUNDID]['user']['id']."'
                AND m.message_deleted=0");
			if (mysql_num_rows($mres)>0)
			{
				$marr = mysql_fetch_array($mres);
				$sender = get_user_nick($marr['message_user_to']);
				echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
				echo "<tr><td width=\"50\" valign=\"top\">&nbsp;</td><td class=\"tbltitle\">";
				if ($marr['message_subject']!="")
					echo stripslashes($marr['message_subject']);
				else
					echo "<i>Kein Titel</i>";
				echo "</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Datum:</td><td class=\"tbldata\" width=\"250\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Empf&auml;nger:</td><td class=\"tbldata\" width=\"250\">".$sender."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:</td><td class=\"tbldata\" width=\"250\">".text2html($marr['message_text'])."</td></tr>";
					echo "</table>";
					echo "<p align=\"center\">";
					echo "<input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=messages&mode=sent'\"></p>";
				}
				else
				{
					echo "<p align=\"center\" class=\"infomsg\">Diese Nachricht existiert nicht!</p>";
					echo "<p align=\"center\"><input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=messages&mode=sent'\"></p>";
				}
			}
			else
			{
				echo "<table width=\"400\" align=\"center\" class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\" colspan=\"4\">Gesendete Nachrichten</td></tr>";
				$mres = dbquery("
				SELECT
                    message_subject,
                    message_id,
                    message_timestamp,
                    message_user_to,
                    message_read
				FROM
					".$db_table['messages']."
				WHERE
                    message_user_from='".$_SESSION[ROUNDID]['user']['id']."'
                    AND message_cat_id='".USER_MSG_CAT_ID."'
                    AND message_deleted=0
				ORDER BY
					message_timestamp DESC
				LIMIT 30;");
				if (mysql_num_rows($mres)>0)
				{
					while ($marr = mysql_fetch_array($mres))
					{
						$sender = get_user_nick($marr['message_user_to']);
						if ($marr['message_read']==0)
							$im_path = "images/pm_new.gif";
						else
							$im_path = "images/pm_normal.gif";
						echo "<tr><td class=\"tbldata\" style=\"width:16px;text-align:center;\"><a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\"><img src=\"$im_path\" style=\"border:none;width:16px;height:18px;\"></a></td>";
						echo "<td class=\"tbldata\"><a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">";
						if ($marr['message_subject']!="")
							echo stripslashes($marr['message_subject']);
						else
							echo "<i>Kein Titel</i>";
						echo "</a></td><td class=\"tbldata\" width=\"100\">".$sender."</td>";
						echo "<td class=\"tbldata\" width=\"100\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
					}
				}
				else
				{
					echo "<tr><td class=\"tbldata\" width=\"400\" colspan=\"4\"><i>Keine Nachrichten vorhanden</i></td>";
				}
				echo "</table>";
				echo "<p align=\"center\">Es werden nur die 30 neusten Nachrichten angezeigt.</p>";
			}
		}

/***********************
* Nachricht betrachten *
***********************/
		else
		{
			//
			// Einzelne Nachricht
			//
			if (intval($_GET['msg_id'])>0)
			{
				$mres = dbquery("
				SELECT
                    m.message_subject,
                    m.message_timestamp,
                    m.message_user_from,
                    m.message_text,
                    m.message_read,
                    c.cat_sender
				FROM
                    ".$db_table['messages']." AS m,
                    ".$db_table['message_cat']." AS c
				WHERE
                    c.cat_id=m.message_cat_id
                    AND m.message_id='".intval($_GET['msg_id'])."'
                    AND m.message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
                    AND m.message_deleted=0");
				if (mysql_num_rows($mres)>0)
				{
					echo "<form action=\"?page=$page&mode=".$mode."\" method=\"POST\">";
					checker_init();
					$marr = mysql_fetch_array($mres);
					if ($marr['message_user_from']>0)
						$sender = get_user_nick($marr['message_user_from']);
					else
						$sender = $marr['cat_sender'];
					echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
					echo "<tr><td class=\"tbltitle\" colspan=\"2\">";
					if ($marr['message_subject']!="")
						echo stripslashes($marr['message_subject']);
					else
						echo "<i>Kein Titel</i>";
					echo "</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Datum:</td><td class=\"tbldata\" width=\"250\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Sender:</td><td class=\"tbldata\" width=\"250\">$sender</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:</td><td class=\"tbldata\" width=\"250\">";
					if ($marr['message_text']!="")
						echo text2html($marr['message_text']);
					else
						echo "<i>Kein Text</i>";
					echo "</td></tr>";
					echo "</table>";
					if ($marr['message_read']==0)
						dbquery("UPDATE ".$db_table['messages']." SET message_read=1 WHERE message_id='".intval($_GET['msg_id'])."';");
					echo "<p align=\"center\">";
					if ($marr['message_user_from']>0)
					{
						echo "<input type=\"button\" class=\"button\" value=\"Antworten\" onClick=\"document.location='?page=$page&mode=new&message_user_to=".rawurlencode($marr['message_user_from'])."&message_subject=".rawurlencode("Re: ".$marr['message_subject'])."'\">&nbsp;";
					}
					echo "<input type=\"hidden\" name=\"message_id\" value=\"".intval($_GET['msg_id'])."\">";
					echo "<input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=messages&mode=".$mode."'\">&nbsp;";
					echo "<input type=\"submit\" class=\"button\" value=\"L&ouml;schen\" name=\"submitdelete\"></p>";

					if ($marr['message_read']==0)
					{
						$wordban=0;
						foreach ($wordbanlist as $banword)
						{
							if (@stristr($marr['message_text'],$banword))
							{
								$wordban++;
							}
						}
						foreach ($wordwhitelist as $unbanword)
						{
							if (@stristr($marr['message_text'],$unbanword))
							{
								$wordban--;
							}
						}

						if ($wordban>0)
						{
							add_log(2,"[b]M&ouml;gliche Beleidugung in Nachricht[/b]\n\n[b]Sender:[/b] $sender\n[b]Empfänger:[/b] ".$_SESSION[ROUNDID]['user']['nick']."\n[b]Betreff:[/b] ".$marr['message_subject']."\n[b]Sendezeit:[/b] ".date("d.m.Y H:i",$marr['message_timestamp'])."\n\n".$marr['message_text'],time());
							echo "<input type=\"hidden\" name=\"user_ban_id\" value=\"".$marr['message_user_from']."\">";
							echo "<p align=\"center\" style=\"color:#f00;\"><b>Diese Nachricht enth&auml;lt vermutlich Beleidigungen.<br/> Soll der Sender f&uuml;r ".BAN_HOURS." Stunden gesperrt werden? </b><br/><br/><input type=\"submit\" class=\"button\" name=\"submit_ban\" value=\"Ja, Sender sperren!\"></p>";
						}
					}
					echo "</form>";
				}
				else
				{
					echo "<p align=\"center\" class=\"infomsg\">Diese Nachricht existiert nicht!</p>";
					echo "<p align=\"center\"><input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=messages&mode=".$mode."'\"></p>";
				}
			}

			//
			// Übersicht
			//
			else
			{
				if ($_POST['submit_ban']!="" && checker_verify())
				{
					$user_blocked_from=time();
					$user_blocked_to=time()+(3600*BAN_HOURS);
					dbquery("UPDATE ".$db_table['users']." SET user_blocked_from='$user_blocked_from',user_blocked_to='$user_blocked_to',user_ban_reason='Beleidigung in Ingame-Nachricht' WHERE user_id='".$_POST['user_ban_id']."';");
					add_log(3,"[b]Sperrung[/b]\n\n[b]Spieler:[/b] ".get_user_nick($_POST['user_ban_id'])."\n[b]Von:[/b] ".date("d.m.Y H:i",$user_blocked_from)."\n[b]Bis:[/b] ".date("d.m.Y H:i",$user_blocked_to)."\n[b]Grund:[/b] Beleidigung in Ingame-Nachricht gegen den Spieler ".$_SESSION[ROUNDID]['user']['nick'].".",time());

					echo "<p align=\"center\">Der Spieler wurde gesperrt!</p>";
				}
				if ($_POST['submitdelete']!=""  && checker_verify())
				{
					if (dbquery("UPDATE ".$db_table['messages']." SET message_deleted=1 WHERE message_id='".$_POST['message_id']."' AND message_user_to='".$_SESSION[ROUNDID]['user']['id']."';"))
						echo "<p align=\"center\">Nachricht wurde gel&ouml;scht!</p>";
				}
				if ($_POST['submitdeleteselection']!=""  && checker_verify())
				{
					if ($mode=="old")
						$sqladd = " AND message_read=1";
					elseif($mode=="archiv")
						$sqladd = " AND message_archived=1";
					else
						$sqladd = " AND message_read=0";

					if (count($_POST['delmsg'])>0)
					{
						foreach ($_POST['delmsg'] as $id=>$val)
						{
							dbquery("
							UPDATE
								".$db_table['messages']."
							SET
								message_deleted=1
							WHERE
								message_id='$id'
								AND message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
								$sqladd;");
						}
						if (count($_POST['delmsg'])==1)
							echo "<p align=\"center\">Nachricht wurde gel&ouml;scht!</p>";
						else
							echo "<p align=\"center\">Nachrichten wurden gel&ouml;scht!</p>";
					}
				}
				elseif ($_POST['submitdeleteall']!="" && checker_verify())
				{
					if ($mode=="old")
						$sqladd = " AND message_read=1";
					elseif($mode=="archiv")
						$sqladd = " AND message_archived=1";
					else
						$sqladd = " AND message_read=0";

					dbquery("
					UPDATE
						".$db_table['messages']."
					SET
						message_deleted=1
					WHERE
						message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
						$sqladd;");
					echo "<p align=\"center\">Alle Nachrichten wurden gel&ouml;scht!</p>";
				}
				elseif ($_POST['submitdeletesys']!="" && checker_verify())
				{
					if ($mode=="old")
						$sqladd = " AND message_read=1";
					elseif($mode=="archiv")
						$sqladd = " AND message_archived=1";
					else
						$sqladd = " AND message_read=0";

					dbquery("
					UPDATE
						".$db_table['messages']."
					SET
						message_deleted=1
					WHERE
                        message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
                        AND message_user_from=0
						$sqladd;");
					echo "<p align=\"center\">Alle Systemnachrichten wurden gel&ouml;scht!</p>";
				}
				elseif ($_POST['submitarchiving']!=""  && checker_verify())
				{
					if ($mode=="old")
						$sqladd = " AND message_read=1";
					else
						$sqladd = " AND message_read=0";

					if (count($_POST['delmsg'])>0)
					{
						if(count($_POST['delmsg'])<=($conf['msg_max_store']['p1']-$_POST['archived_msg_cnt']))
						{
                            foreach ($_POST['delmsg'] as $id=>$val)
                            {
                                dbquery("
                                UPDATE
                                    ".$db_table['messages']."
                                SET
                                    message_archived=1
                                WHERE
                                    message_id='".$id."'
                                    AND message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
                                    ".$sqladd.";");
                            }
                            if (count($_POST['delmsg'])==1)
                                echo "<p align=\"center\">Nachricht wurde archiviert!</p>";
                            else
                                echo "<p align=\"center\">Nachrichten wurden archiviert!</p>";
                        }
                        else
                        {
                        	echo "<p align=\"center\">Zu wenig Platz im Archiv!</p>";
                        }
					}
				}

				// Nachrichten anzeigen

				$res = dbquery("SELECT user_msg_preview FROM ".$db_table['users']." WHERE user_id=".$_SESSION[ROUNDID]['user']['id'].";");
				$arr = mysql_fetch_row($res);
				$msgpreview = $arr[0];

				//Zählt gelesene Nachrichten
				$cnt_res = dbquery("
				SELECT
					message_id
				FROM
					".$db_table['messages']."
				WHERE
                    message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
                    AND message_read='1'
                    AND message_deleted='0'
                    AND message_archived='0';");
				$readed_msg_cnt=mysql_num_rows($cnt_res);

				//Zählt archivierte Nachrichten
				$cnt_res = dbquery("
				SELECT
					message_id
				FROM
					".$db_table['messages']."
				WHERE
                    message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
                    AND message_archived='1'
                    AND message_deleted='0';");
				$archived_msg_cnt=mysql_num_rows($cnt_res);

				//Errechnet wie viele Prozent gewüllt sind
				$readed_store=ceil($readed_msg_cnt/$conf['msg_max_store']['v']*100);
				$archived_store=ceil($archived_msg_cnt/$conf['msg_max_store']['p1']*100);

				//Rchnet %-Werte für tabelle (1/2)
				$readed_table=ceil($readed_msg_cnt/$conf['msg_max_store']['v']*50);
				$archived_table=ceil($archived_msg_cnt/$conf['msg_max_store']['p1']*50);

				if($readed_store>=90)
					$class_readed="tbldata2";
				else
					$class_readed="tbldata";

				if($archived_store>=90)
					$class_archived="tbldata2";
				else
					$class_archived="tbldata";

				echo "<table class=\"tbl\">";
				echo "<tr>
                        <td class=\"$class_readed\" width=\"50%\" style=\"text-align:center;\" colspan=\"2\">
                            Gelesene: ".$readed_msg_cnt."/".$conf['msg_max_store']['v']." Nachrichten
                        </td>
                        <td class=\"$class_archived\" width=\"50%\" style=\"text-align:center;\" colspan=\"2\">
                            Archiv: ".$archived_msg_cnt."/".$conf['msg_max_store']['p1']." Nachrichten
                        </td>
                     </tr>";
				echo "<tr>
                        <td class=\"tbltitle\" width=\"".$readed_table."%\">&nbsp;</td>
                        <td class=\"tbldata\" width=\"".(50-$readed_table)."%\">&nbsp;</td>
                        <td class=\"tbltitle\" width=\"".$archived_table."%\">&nbsp;</td>
                        <td class=\"tbldata\" width=\"".(50-$archived_table)."%\">&nbsp;</td>
                     </tr>";
				echo "</table>";

				echo "<form action=\"?page=$page&mode=".$mode."\" method=\"post\">";
				checker_init();
				echo "<input type=\"hidden\" name=\"archived_msg_cnt\" value=\"".$archived_msg_cnt."\">";
				echo "<table class=\"tbl\">";
				$res = dbquery("
				SELECT
	                cat_id,
                    cat_name,
                    cat_desc,
                    cat_sender
				FROM
					".$db_table['message_cat']."
				ORDER BY
					cat_order;");
				$msgcnt=0;
				while ($arr = mysql_fetch_array($res))
				{
					if ($mode=="old")
					{
						$mres = dbquery("
						SELECT
							message_subject,
							message_text,
							message_id,
							message_timestamp,
							message_user_from,
							message_read,
							message_massmail
						FROM
							".$db_table['messages']."
						WHERE
							message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
							AND message_cat_id='".$arr['cat_id']."'
							AND message_read=1
							AND message_deleted=0
							AND message_archived=0
						ORDER BY
							message_timestamp DESC;");
					}
					elseif($mode=="archiv")
					{
						$mres = dbquery("
						SELECT
							message_subject,
							message_text,
							message_id,
							message_timestamp,
							message_user_from,
							message_read,
							message_massmail
						FROM
							".$db_table['messages']."
						WHERE
							message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
							AND message_cat_id='".$arr['cat_id']."'
							AND message_deleted=0
							AND message_archived=1
						ORDER BY
							message_timestamp DESC;");
					}
					else
					{
						$mres = dbquery("
						SELECT
							message_subject,
							message_text,
							message_id,
							message_timestamp,
							message_user_from,
							message_read,
							message_massmail
						FROM
							".$db_table['messages']."
						WHERE
							message_user_to='".$_SESSION[ROUNDID]['user']['id']."'
							AND message_cat_id='".$arr['cat_id']."'
							AND message_read=0
							AND message_deleted=0
							AND message_archived=0
						ORDER BY
							message_timestamp DESC;");
					}

					$ccnt=mysql_num_rows($mres);
					if ($ccnt>0)
					{
						echo "<tr><td class=\"tbltitle\" colspan=\"4\">".text2html($arr['cat_name'])." (".mysql_num_rows($mres)." Nachrichten)</td>
						<td class=\"tbltitle\" style=\"text-align:center;\"><input type=\"button\" id=\"selectBtn[".$arr['cat_id']."]\" value=\"X\" onclick=\"xajax_messagesSelectAllInCategory(".$arr['cat_id'].",$ccnt,this.value)\"/></td></tr>";
					}
					else
					{
						echo "<tr><td class=\"tbltitle\" colspan=\"5\">".text2html($arr['cat_name'])."</td></tr>";
					}
					if ($ccnt>0)
					{
						$ccnt=0;
						while ($marr = mysql_fetch_array($mres))
						{
							if ($marr['message_user_from']>0)
								$sender = get_user_nick($marr['message_user_from']);
							else
								$sender = $arr['cat_sender'];
							if ($marr['message_read']==0)
								$im_path = "images/pm_new.gif";
							else
								$im_path = "images/pm_normal.gif";
							if ($marr['message_subject']!="")
								$subj = stripslashes($marr['message_subject']);
							else
								$subj =  "<i>Kein Titel</i>";

							//Wenn Speicher voll ist Nachrichten Markieren
							if($mode=="messages" && $readed_msg_cnt>=$conf['msg_max_store']['v'])
							{
                echo "<tr><td class=\"tbldata\" style=\"width:16px;text-align:center;\"><img src=\"$im_path\" style=\"border:none;width:16px;height:18px;\"></td>";
								echo "<td class=\"tbldata2\">";
								if ($marr['message_massmail']==1)
								{
									echo "<b>[Rundmail]</b> ";
								}
								
								echo "<span ";
                if ($msgpreview==1)
                {
                    echo tm($subj,text2html(substr($marr['message_text'], 0, 500)));
                }
                echo ">".$subj."</span>";
							}
							else
							{
                echo "<tr><td class=\"tbldata\" style=\"width:16px;text-align:center;\"><a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\"><img src=\"".$im_path."\" style=\"border:none;width:16px;height:18px;\"></a></td>";
								echo "<td class=\"tbldata\">";
								if ($marr['message_massmail']==1)
								{
									echo "<b>[Rundmail]</b> ";
								}
								
								echo "<a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\" ";
                if ($msgpreview==1)
                {
                    echo tm($subj,text2html(substr($marr['message_text'], 0, 500)));
                }
                echo ">".$subj."</a>";
                            }
							echo "</td>";
							echo "<td class=\"tbldata\" width=\"100\">".$sender."</td>";
							echo "<td class=\"tbldata\" width=\"100\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
							echo "<td class=\"tbldata\" width=\"20\" style=\"text-align:center;\"><input id=\"delcb[".$arr['cat_id']."][$ccnt]\" type=\"checkbox\" name=\"delmsg[".$marr['message_id']."]\" value=\"1\" title=\"Nachricht zum L&ouml;schen markieren\"></td>";
							echo "</tr>\n";
							$ccnt++;
							$msgcnt++;
						}
					}
					else
					{
						echo "<tr><td class=\"tbldata\" width=\"400\" colspan=\"5\"><i>Keine Nachrichten vorhanden</i></td>";
					}
				}
				echo "</table>";
				if ($msgcnt>0)
				{
					echo "<p align=\"center\"><input type=\"submit\" name=\"submitdeleteselection\" value=\"Markierte l&ouml;schen\" class=\"button\" />&nbsp;<input type=\"submit\" name=\"submitdeleteall\" value=\"Alle l&ouml;schen\" class=\"button\" />&nbsp;<input type=\"submit\" name=\"submitdeletesys\" value=\"Systemnachrichten l&ouml;schen\" class=\"button\" />";
					if($mode=="old")
						echo "&nbsp;<input type=\"submit\" name=\"submitarchiving\" value=\"Markierte archivieren\" class=\"button\" />";
					echo "</p>";
				}
				echo "</form>";
			}
		}

?>

