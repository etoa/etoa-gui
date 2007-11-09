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
	// 	Dateiname: allianceboard.php
	// 	Topic: Internes Allianzforum
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 29.12.2006
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 01.01.2007
	// 	Kommentar:
	//

	echo "<h1>Allianzforum</h1>";

	// Prüfen ob User in Allianz ist
	if ($_SESSION[ROUNDID]['user']['alliance_id']>0)
	{
		// Prüfen ob Allianz existiert
		$res=dbquery("SELECT alliance_id,alliance_founder_id FROM ".$db_table['alliances']." WHERE alliance_id='".$_SESSION[ROUNDID]['user']['alliance_id']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			define(BOARD_ALLIANCE_ID,$arr['alliance_id']);

			// Eigenen Rang laden
			$ures=dbquery("
			SELECT
				user_alliance_rank_id
			FROM
				".$db_table['users']."
			WHERE
				user_id=".$_SESSION[ROUNDID]['user']['id']."
				AND user_alliance_application=''
				AND user_alliance_id=".BOARD_ALLIANCE_ID.";");
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_array($ures);
  			$myRankId=$uarr['user_alliance_rank_id'];
  		}
  		else
  			$myRankId=0;
			
			// Rechte laden
			$rightres=dbquery("SELECT * FROM ".$db_table['alliance_rights']." ORDER BY right_desc;");
			$rights=array();
			if (mysql_num_rows($rightres)>0)
			{
				while ($rightarr=mysql_fetch_array($rightres))
				{
					$rights[$rightarr['right_id']]['key']=$rightarr['right_key'];
					$rights[$rightarr['right_id']]['desc']=$rightarr['right_desc'];
					if (mysql_num_rows(dbquery("SELECT rr_id FROM ".$db_table['alliance_rankrights'].",".$db_table['alliance_ranks']." WHERE rank_id=rr_rank_id AND rank_alliance_id=".BOARD_ALLIANCE_ID." AND rr_right_id=".$rightarr['right_id']." AND rr_rank_id=".$myRankId.";"))>0)								
						$myRight[$rightarr['right_key']]=true;
					else
						$myRight[$rightarr['right_key']]=false;
				}
			}	
			
			// Ränge laden
			$rres = dbquery("SELECT rank_name,rank_id FROM ".$db_table['alliance_ranks']." WHERE rank_alliance_id=".BOARD_ALLIANCE_ID.";");
			while ($rarr=mysql_fetch_array($rres))
			{
				$rank[$rarr['rank_id']]=$rarr['rank_name'];
			}					
			
			// Kategorien laden
			$catres=dbquery("SELECT cat_id FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID.";");
			if (mysql_num_rows($catres)>0)
			{
				while ($catarr=mysql_fetch_array($catres))
				{
					if (mysql_num_rows(dbquery("SELECT cr_id FROM ".$db_table['allianceboard_catranks'].",".$db_table['alliance_ranks']." WHERE rank_id=cr_rank_id AND rank_alliance_id=".BOARD_ALLIANCE_ID." AND cr_cat_id=".$catarr['cat_id']." AND cr_rank_id=".$myRankId.";"))>0)								
						$myCat[$catarr['cat_id']]=true;
					else
						$myCat[$catarr['cat_id']]=false;
				}
			}							
			
			// Gründer prüfen
			if ($arr['alliance_founder_id']==$_SESSION[ROUNDID]['user']['id'])
				$isFounder=true;
			else
				$isFounder=false;						
			
			// Allianz-User in Array laden
			$ures=dbquery("SELECT * FROM users WHERE user_alliance_id='".BOARD_ALLIANCE_ID."' ORDER BY user_nick ASC;");
			$user=array();
			if (mysql_num_rows($ures)>0)
			{
				while ($uarr=mysql_fetch_array($ures))
				{
					$user[$uarr['user_id']]['nick']=$uarr['user_nick'];
					$user[$uarr['user_id']]['rank']=$uarr['user_rank'];
					$user[$uarr['user_id']]['avatar']=$uarr['user_avatar'];
					$user[$uarr['user_id']]['signature']=$uarr['user_signature'];
				}
			}			
	
			// Change avatar function
			echo "<script type=\"text/javascript\">";
			echo "function changeAvatar(elem) { document.getElementById('avatar').src='".AVATAR_DIR."/'+elem.options[elem.selectedIndex].value;}";
			echo "function changeBullet(elem) { document.getElementById('bullet').src='".BOARD_BULLET_DIR."/'+elem.options[elem.selectedIndex].value;}";
			echo "</script>";		
		
			// User-ID zuweisen
			$s['user_id'] = $_SESSION[ROUNDID]['user']['id'];
			
			// Board-Admin prüfen
			if ($myRight['allianceboard'] || $isFounder)
				$s['admin']=true;
			else
				$s['admin']=false;
				
				
			
			//
			// Create new post in topic
			//
			if ($_GET['newpost']>0 && $s['user_id']>0)
			{
				$tres=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE.",".BOARD_CAT_TABLE." WHERE topic_id=".$_GET['newpost']." AND topic_cat_id=cat_id AND cat_alliance_id=".BOARD_ALLIANCE_ID.";");
				if (mysql_num_rows($tres)>0)
				{		
					$tarr=mysql_fetch_array($tres);
					if ($tarr['topic_closed']==0)
					{
						echo "<form action=\"?page=$page&amp;topic=".$_GET['newpost']."\" method=\"post\">";
						echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=".$tarr['cat_id']."\">".$tarr['cat_name']."</a> &gt; <a href=\"?page=$page&amp;topic=".$_GET['newpost']."\">".$tarr['topic_subject']."</a> &gt; Neuer Beitrag</h2>";
						echo "<table class=\"tb\">";
						echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"10\" cols=\"90\"></textarea></td></tr>";
						echo "</table><br/>";
						echo "<input type=\"submit\" name=\"submit\" value=\"Speichern\" /> &nbsp; ";
					}
					else
						echo "Dieses Thema ist geschlossen!<br/><br/>";
				}
				else
					echo "<b>Fehler!</b> Dieses Thema existiert nicht!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Beitrags abgebrochen werden?')) document.location='?page=$page&topic=".$tarr['topic_id']."'\" /></form>";
			}
			
			//
			// Edit Post
			//
			elseif($_GET['editpost']>0 && $s)
			{
				echo "<h2>Beitrag bearbeiten</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_GET['editpost'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($s['user_id']==$arr['post_user_id'] || $s['admin'])
					{
						echo "<form action=\"?page=$page&amp;topic=".$arr['post_topic_id']."\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"post_id\" value=\"".$arr['post_id']."\" />";
						echo "<table class=\"tb\">";
						echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"10\" cols=\"90\">".stripslashes($arr['post_text'])."</textarea></td></tr>";
						echo "</table><br/><input type=\"submit\" value=\"Speichern\" name=\"post_edit\" /> &nbsp; ";
					}
					else
						echo "<b>Fehler!</b> Keine Berechtigung!<br/><br/>";
				}
				else
					echo "<b>Fehler!</b> Datensatz nicht gefunden!<br/><br/>";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&topic=".$arr['post_topic_id']."#".$arr['post_id']."'\" /></form>";
			}
				
			//
			// Delete Post
			//
			elseif($_GET['delpost']>0 && $s)
			{
				echo "<h2>Beitrag löschen</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_GET['delpost'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($s['user_id']==$arr['post_user_id'] || $s['admin'])
					{
						echo "<form action=\"?page=$page&amp;topic=".$arr['post_topic_id']."\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"post_id\" value=\"".$arr['post_id']."\" />";
						infobox_start("Soll der folgende Beitrag wirklich gelöscht werden?");
						echo text2html($arr['post_text']);
						infobox_end();
						echo "<input type=\"submit\" value=\"L&ouml;schen\" name=\"post_delete\" onclick=\"return confirm('Wirklich löschen?');\" /> &nbsp; ";
					}
					else
						echo "<b>Fehler!</b> Keine Berechtigung!<br/><br/>";
				}
				else
					echo "<b>Fehler!</b> Datensatz nicht gefunden!<br/><br/>";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&topic=".$arr['post_topic_id']."#".$arr['post_id']."' \" /></form>";
			}		
			
			//
			// Show topic with it's posts
			//	
			elseif ($_GET['topic']>0)
			{
				$tres=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE.",".BOARD_CAT_TABLE." WHERE topic_id=".$_GET['topic']." AND topic_cat_id=cat_id;");
				if (mysql_num_rows($tres)>0)
				{
					$tarr=mysql_fetch_array($tres);
					if ($s['admin'] || $myCat[$tarr['cat_id']])
					{					
						echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=".$tarr['cat_id']."\">".$tarr['cat_name']."</a> &gt; ".$tarr['topic_subject']."</h2>";
						if ($tarr['topic_closed']==1)
						echo "<img src=\"images/closed.gif\" alt=\"closed\" style=\"width:15px;height:16px;\" /> <i>Dieses Thema ist geschlossen und es können keine weiteren Beiträge erstellt werden!</i><br/><br/>";
				
						// Save new post
						if ($_POST['submit']!="" && $_POST['post_text']!="" && $s['user_id']>0 && $tarr['topic_closed']==0)
						{
							dbquery("INSERT INTO ".BOARD_POSTS_TABLE." (post_topic_id,post_user_id,post_text,post_timestamp) VALUES (".$_GET['topic'].",".$s['user_id'].",'".addslashes($_POST['post_text'])."',".time().");");
							$mid=mysql_insert_id();
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_timestamp=".time()." WHERE topic_id=".$_GET['topic'].";");			
							echo "Beitrag gespeichert!<br/><br/>";
							echo "<script type=\"text/javascript\">document.location='?page=$page&topic=".$_GET['topic']."#".$mid."';</script>";
						}
						else
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_count=topic_count+1  WHERE topic_id=".$_GET['topic'].";");			
			
						// Edit post
						if ($_POST['post_edit']!="" && $_POST['post_text']!="" && $_POST['post_id']!="" && ($s['user_id']>0 || $s['admin']))
						{
							if ($s['admin'])
								dbquery("UPDATE ".BOARD_POSTS_TABLE." SET post_text='".addslashes($_POST['post_text'])."',post_changed=".time()." WHERE post_id=".$_POST['post_id'].";");
							else
								dbquery("UPDATE ".BOARD_POSTS_TABLE." SET post_text='".addslashes($_POST['post_text'])."',post_changed=".time()." WHERE post_id=".$_POST['post_id']." AND post_user_id=".$s['user_id'].";");
							echo "&Auml;nderungen gespeichert!<br/><br/>";
							echo "<script type=\"text/javascript\">document.location='?page=$page&topic=".$_GET['topic']."#".$_POST['post_id']."';</script>";
						}
						
						// Delete post
						if ($_POST['post_delete']!="" && $_POST['post_id']!="" && ($s['user_id']>0 || $s['admin']))
						{
							if ($s['admin'])
								dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_POST['post_id'].";");
							else
								dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_POST['post_id']." AND post_user_id=".$s['user_id'].";");
							
							echo "Beitrag gelöscht<br/><br/>";
						}			
				
						$res=dbquery("SELECT * FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$_GET['topic']." ORDER BY post_timestamp ASC;");					
						if (mysql_num_rows($res)>0)
						{			
							echo "<table class=\"tb\">";
							while ($arr=mysql_fetch_array($res))
							{
								echo "<tr><th style=\"width:150px;\"><a name=\"".$arr['post_id']."\"></a><a href=\"?page=userinfo&amp;id=".$arr['post_user_id']."\">".$user[$arr['post_user_id']]['nick']."</a><br/>";
								show_avatar($user[$arr['post_user_id']]['avatar']);
								$parr=mysql_fetch_row(dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE.",".BOARD_CAT_TABLE." WHERE post_topic_id=topic_id AND topic_cat_id=cat_id AND cat_alliance_id=".BOARD_ALLIANCE_ID." AND post_user_id=".$arr['post_user_id'].";"));
								echo "Beitr&auml;ge: ".$parr[0]."<br/><br/>".df($arr['post_timestamp'])." Uhr";
								if ($s['admin'] || $arr['post_user_id']==$s['user_id'])
									echo "<br/><a href=\"?page=$page&amp;editpost=".$arr['post_id']."\"><img src=\"images/edit.gif\" alt=\"edit\" border=\"0\" /></a> <a href=\"?page=$page&amp;delpost=".$arr['post_id']."\"><img src=\"images/delete.gif\" alt=\"del\" border=\"0\" /></a>";
								echo "</th>";
								echo "<td";
								if ($user[$arr['post_user_id']]['rank']==count($urank)-1) echo " style=\"color:".ADMIN_COLOR."\"";
								echo ">".text2html($arr['post_text']);
								if ($arr['post_changed']>0)
									echo "<br/><br/><span style=\"font-size:8pt;\">Dieser Beitrag wurde zuletzt geändert am ".date("d.m.Y",$arr['post_changed'])." um ".date("H:i",$arr['post_changed'])." Uhr.</span>";
								if ($user[$arr['post_user_id']]['signature']!="")
									echo "<hr>".text2html($user[$arr['post_user_id']]['signature']);
								echo "</td></tr>";
							}
							echo "</table><br/>";			
						}
						else
							echo "<b>Fehler!</b> Es sind keine Posts vorhanden!<br/><br/>";					
						if ($s['user_id']>0 && $tarr['topic_closed']==0)
							echo "<input type=\"button\" value=\"Neuer Beitrag\" onclick=\"document.location='?page=$page&newpost=".$_GET['topic']."'\" /> &nbsp; ";
					}
					else
						echo "<b>Fehler!</b> Kein Zugriff!<br/><br/>";
				}
				else
					echo "<b>Fehler!</b> Dieses Thema existiert nicht!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&cat=".$tarr['cat_id']."'\" />";
			}
			
			//
			// Create new topic in category
			//
			elseif ($_GET['newtopic']>0 && $s['user_id']>0)
			{
				$tres=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_id=".$_GET['newtopic'].";");
				if (mysql_num_rows($tres)>0)
				{		
					$tarr=mysql_fetch_array($tres);
					echo "<form action=\"?page=$page&amp;cat=".$_GET['newtopic']."\" method=\"post\">";
					echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=".$tarr['cat_id']."\">".$tarr['cat_name']."</a> &gt; Neues Thema</h2>";
					
					echo "<table class=\"tb\">";
					echo "<tr><th>Titel:</th><td><input name=\"topic_subject\" type=\"text\" size=\"40\" /></td></tr>";
					echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"6\" cols=\"80\"></textarea></td></tr>";
					echo "</table><br/>";
					echo "<input type=\"submit\" name=\"submit\" value=\"Speichern\" /> &nbsp; ";
				}
				else
					echo "<b>Fehler!</b> Diese Kategorie existiert nicht!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Themas abgebrochen werden?')) document.location='?page=$page&cat=".$tarr['cat_id']."'\" /></form>";
			}	
			
			//
			// Edit a topic
			//
			elseif($_GET['edittopic']>0 && $s)
			{
				echo "<h2>Thema bearbeiten</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_GET['edittopic'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($s['user_id']==$arr['topic_user_id'] || $s['admin'])
					{
						echo "<form action=\"?page=$page&amp;cat=".$arr['topic_cat_id']."\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"topic_id\" value=\"".$arr['topic_id']."\" />";
						echo "<table class=\"tb\">";
						echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"topic_subject\" size=\"40\" value=\"".$arr['topic_subject']."\" /></td></tr>";
						if ($s['admin'])
						{
							echo "<tr><th>Top-Thema:</th><td><input name=\"topic_top\" type=\"radio\" value=\"1\"";
							if ($arr['topic_top']==1) echo " checked=\"checked\"";
							echo " /> Ja <input name=\"topic_top\" type=\"radio\" value=\"0\"";
							if ($arr['topic_top']==0) echo " checked=\"checked\"";
							echo " /> Nein</td></tr>";
							echo "<tr><th>Geschlossen:</th><td><input name=\"topic_closed\" type=\"radio\" value=\"1\"";
							if ($arr['topic_closed']==1) echo " checked=\"checked\"";
							echo " /> Ja <input name=\"topic_closed\" type=\"radio\" value=\"0\"";
							if ($arr['topic_closed']==0) echo " checked=\"checked\"";
							echo " /> Nein</td></tr>";
							echo "<tr><th>Kategorie:</th><td><select name=\"topic_cat_id\">";
							$cres=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." ORDER BY cat_order,cat_name;");
							while ($carr=mysql_fetch_array($cres))
							{
								echo "<option value=\"".$carr['cat_id']."\"";
								if ($arr['topic_cat_id']==$carr['cat_id']) echo " selected=\"selected\"";
								echo ">".$carr['cat_name']."</option>";
							}
							echo "</select></td></tr>";
						}
						echo "</table><br/><input type=\"submit\" name=\"topic_edit\" value=\"Speichern\" /> ";
					}
					else
						echo "<b>Fehler!</b> Keine Berechtigung!<br/><br/>";
				}
				else
					echo "<b>Fehler!</b> Datensatz nicht gefunden!<br/><br/>";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&cat=".$arr['topic_cat_id']."'\" /></form>";
			}
			
			//
			// Delete a topic and all it's posts
			//
			elseif($_GET['deltopic']>0 && $s['admin'])
			{
				echo "<h2>Thema löschen</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_GET['deltopic'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					echo "<form action=\"?page=$page&amp;cat=".$arr['topic_cat_id']."\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"topic_id\" value=\"".$arr['topic_id']."\" />";
					echo "Soll der Beitrag <b>".$arr['topic_subject']."</b> und alle darin enthaltenen Posts gelöscht werden?";
					echo "<br/><br/><input type=\"submit\" name=\"topic_delete\" value=\"L&ouml;schen\" onclick=\"return confirm('Willst du das Thema \'".$arr['topic_subject']."\' wirklich löschen?');\" /> ";
				}
				else
					echo "<b>Fehler!</b> Datensatz nicht gefunden!<br/><br/>";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&cat=".$arr['topic_cat_id']."'\" /></form>";
			}	
			
			//
			// Show topics in category
			//
			elseif ($_GET['cat']>0 )
			{
				if ($s['admin'] || $myCat[$_GET['cat']])
				{
					$cres=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." AND cat_id=".$_GET['cat'].";");		
					if (mysql_num_rows($cres)>0)
					{
						$carr=mysql_fetch_array($cres);
						echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; ".$carr['cat_name']."</h2>";
						
						// Save new topic
						if ($_POST['submit']!="" && $_POST['topic_subject']!="" && $_POST['post_text']!="" && $s['user_id']>0)
						{
							dbquery("INSERT INTO ".BOARD_TOPIC_TABLE." (topic_subject,topic_cat_id,topic_user_id,topic_timestamp) VALUES ('".addslashes($_POST['topic_subject'])."',".$_GET['cat'].",".$s['user_id'].",".time().");");			
							$mid=mysql_insert_id();
							dbquery("INSERT INTO ".BOARD_POSTS_TABLE." (post_topic_id,post_user_id,post_text,post_timestamp) VALUES (".$mid.",".$s['user_id'].",'".addslashes($_POST['post_text'])."',".time().");");
							$pmid=mysql_insert_id();
							echo "<script type=\"text/javascript\">document.location='?page=$page&topic=".$mid."#".$pmid."';</script>";
						}			
						// Save edited topic
						elseif ($_POST['topic_edit']!="" && $_POST['topic_subject']!="" && $_POST['topic_id']>0)
						{
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_subject='".$_POST['topic_subject']."',topic_top='".$_POST['topic_top']."',topic_closed='".$_POST['topic_closed']."',topic_cat_id='".$_POST['topic_cat_id']."' WHERE topic_id=".$_POST['topic_id']."");
							echo "&Auml;nderungen gespeichert!<br/><br/>";
							if ($_POST['topic_cat_id']!=$_GET['cat'])
								echo "<script type=\"text/javascript\">document.location='?page=$page&cat=".$_POST['topic_cat_id']."';</script>";
						}
						// Delete topic
						elseif ($_POST['topic_delete']!="" && $_POST['topic_id']>0)
						{
							dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$_POST['topic_id'].";");
							dbquery("DELETE FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_POST['topic_id'].";");
							echo "Thema gelöscht!<br/><br/>";
						}
			
						
						$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_cat_id=".$_GET['cat']." ORDER BY topic_top DESC,topic_timestamp DESC, topic_subject ASC;");					
						if (mysql_num_rows($res)>0)
						{			
							echo "<table class=\"tb\">";
							echo "<tr><th colspan=\"2\">Thema</th><th>Posts</th><th>Aufrufe</th><th>Autor</th><th>Letzer Beitrag</th>";
							if ($s['admin'])
							{					
								echo "<th>Aktionen</th>";
							}
							echo "</tr>";
							while ($arr=mysql_fetch_array($res))
							{
								echo "<tr><td style=\"width:37px;\">";
								if ($arr['topic_top']==1) echo "<img src=\"images/sticky.gif\" alt=\"top\" style=\"width:22px;height:15px;\" ".tm("Wichtiges Thema","Dieses ist ein wichtiges Thema.")."/>";
								if ($arr['topic_closed']==1) echo "<img src=\"images/closed.gif\" alt=\"closed\" style=\"width:15px;height:16px;\" ".tm("Geschlossen","Es können keine weiteren Beiträge zu diesem Thema geschrieben werden.")." />";
								echo "</td>";
								echo "<td style=\"width:250px;\"><a href=\"?page=$page&amp;topic=".$arr['topic_id']."\"";
								//if ($s['last_activity']<$arr['topic_timestamp'] && $s)
								//	echo " style=\"color:#c00;\"";
								echo ">".$arr['topic_subject']."</a></td>";
								$parr=mysql_fetch_row(dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$arr['topic_id'].";"));
								echo "<td>".$parr[0]."</td>";
								echo "<td>".$arr['topic_count']."</td>";
								echo "<td>".$user[$arr['topic_user_id']]['nick']."</td>";
								$parr=mysql_fetch_array(dbquery("SELECT post_id,post_timestamp,post_user_id FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$arr['topic_id']." ORDER BY post_timestamp DESC LIMIT 1;"));
								echo "<td><a href=\"?page=$page&amp;topic=".$arr['topic_id']."#".$parr['post_id']."\">".df($parr['post_timestamp'])."</a><br/>".$user[$parr['post_user_id']]['nick']."</td>";				
								if ($s['admin'] || $s['user_id']==$arr['topic_user_id'])
								{
									echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&edittopic=".$arr['topic_id']."'\" />";
									if ($s['admin'])
										echo " <input type=\"button\" value=\"L&ouml;schen\" onclick=\"document.location='?page=$page&deltopic=".$arr['topic_id']."'\" />";
									echo "</td>";
								}
								echo "</tr>";
							}
							echo "</table><br/>";			
						}
						else
							echo "<i>Es sind noch keine Themen vorhanden</i><br/><br/>";		
						if ($s['user_id']>0)
							echo "<input type=\"button\" value=\"Neues Thema\" onclick=\"document.location='?page=$page&newtopic=".$_GET['cat']."'\" /> &nbsp; ";
					}
					else
						echo "<i><b>Fehler!</b> Kategorie existiert nicht!</i><br/><br/>";
				}
				else
					echo "Kein Zugriff!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
			}
		
			//
			// New category
			//
			elseif($_GET['action']=="newcat" && $s['admin'])
			{
				$d=opendir(BOARD_BULLET_DIR);
				$bullets=array();
				while ($f=readdir($d))
				{
					if (is_file(BOARD_BULLET_DIR."/".$f) && !is_dir(BOARD_BULLET_DIR."/".$f) && $f!=BOARD_DEFAULT_IMAGE)
					{
						array_push($bullets,$f);
					}
				}
				sort($bullets);		

				echo "<h2>Neue Kategorie</h2>";
				echo "<form action=\"?page=$page\" method=\"post\">";
				echo "<table class=\"tb\">";
				echo "<tr><th>Name:</th><td><input type=\"text\" name=\"cat_name\" size=\"40\" /></td></tr>";
				echo "<tr><th>Beschreibung:</th><td><input type=\"text\" name=\"cat_desc\" size=\"40\" value=\"\" /></td></tr>";
				echo "<tr><th>Reihenfolge/Position:</th><td><input type=\"text\" size=\"1\" maxlenght=\"2\" name=\"cat_order\" value=\"".mysql_num_rows(dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID.";"))."\" /></td></tr>";
				echo "<tr><th>Zugriff:</th><td>";
				foreach ($rank as $k=>$v)
				{
					echo "<input type=\"checkbox\" name=\"cr[".$k."]\" value=\"1\" ";
					echo" /> ".$v."</span><br/>";
				}						
				echo "</td></tr>";
				echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";	
				echo "<img src=\"".BOARD_BULLET_DIR."/".BOARD_DEFAULT_IMAGE."\" style=\"width:38px;height:35px;\" id=\"bullet\" />";
				echo "<br/>Symbol wählen: <select name=\"cat_bullet\" changeBullet=\"changeAvatar(this);\" onmousemove=\"changeBullet(this);\" onkeyup=\"changeBullet(this);\">";
				echo "<option value=\"".BOARD_DEFAULT_IMAGE."\">Standard-Symbol</option>";

				foreach ($bullets as $a)
				{
						echo "<option value=\"$a\"";
						if ($a==$arr['cat_bullet'] && $arr['cat_bullet']!="") echo " selected=\"selected\"";
						echo ">$a</option>";
				}
				echo "</select></td></tr>";

				echo "</table><br/><input type=\"submit\"name=\"cat_new\" value=\"Kategorie speichern\" /> ";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
			//
			// Edit a category
			//
			elseif($_GET['editcat']>0 && $s['admin'])
			{
				echo "<h2>Kategorie bearbeiten</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." AND cat_id=".$_GET['editcat'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					$d=opendir(BOARD_BULLET_DIR);
					$bullets=array();
					while ($f=readdir($d))
					{
						if (is_file(BOARD_BULLET_DIR."/".$f) && !is_dir(BOARD_BULLET_DIR."/".$f) && $f!=BOARD_DEFAULT_IMAGE)
						{
							array_push($bullets,$f);
						}
					}
					sort($bullets);			
					
					echo "<form action=\"?page=$page\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"cat_id\" value=\"".$arr['cat_id']."\" />";
					echo "<table class=\"tb\">";
					echo "<tr><th>Name:</th><td><input type=\"text\" name=\"cat_name\" size=\"40\" value=\"".$arr['cat_name']."\" /></td></tr>";
					echo "<tr><th>Beschreibung:</th><td><input type=\"text\" name=\"cat_desc\" size=\"40\" value=\"".$arr['cat_desc']."\" /></td></tr>";
					echo "<tr><th>Reihenfolge/Position:</th><td><input type=\"text\" size=\"1\" maxlenght=\"2\" name=\"cat_order\" value=\"".$arr['cat_order']."\" /></td></tr>";
					echo "<tr><th>Zugriff:</th><td>";
					foreach ($rank as $k=>$v)
					{
						echo "<input type=\"checkbox\" name=\"cr[".$k."]\" value=\"1\" ";
						$crres=dbquery("SELECT cr_id FROM ".$db_table['allianceboard_catranks']." WHERE cr_rank_id=".$k." AND cr_cat_id=".$arr['cat_id'].";");								
						if (mysql_num_rows($crres)>0)
							echo " checked=\"checked\" /><span style=\"color:#0f0;\">".$v."</span><br/>";
						else
							echo" /> <span style=\"color:#f50;\">".$v."</span><br/>";
					}						
					echo "</td></tr>";
					echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";	
					if ($arr['cat_bullet']=="" || !is_file(BOARD_BULLET_DIR."/".$arr['cat_bullet'])) $arr['cat_bullet']=BOARD_DEFAULT_IMAGE;
					echo "<img src=\"".BOARD_BULLET_DIR."/".$arr['cat_bullet']."\" style=\"width:38px;height:35px;\" id=\"bullet\" />";
					echo "<br/>Symbol ändern: <select name=\"cat_bullet\" changeBullet=\"changeAvatar(this);\" onmousemove=\"changeBullet(this);\" onkeyup=\"changeBullet(this);\">";
					echo "<option value=\"".BOARD_DEFAULT_IMAGE."\">Standard-Symbol</option>";
					foreach ($bullets as $a)
					{
							echo "<option value=\"$a\"";
							if ($a==$arr['cat_bullet'] && $arr['cat_bullet']!="") echo " selected=\"selected\"";
							echo ">$a</option>";
					}
					echo "</select></td></tr>";
					
					echo "</table><br/><input type=\"submit\" name=\"cat_edit\" value=\"Speichern\" /> ";
				}
				else
					echo "<b>Fehler!</b> Datensatz nicht gefunden!<br/><br/>";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
			//
			// Delete a forum category and all it's content
			//
			elseif($_GET['delcat']>0 && $s['admin'])
			{
				echo "<h2>Kategorie löschen</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." AND cat_id=".$_GET['delcat'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					echo "<form action=\"?page=$page\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"cat_id\" value=\"".$arr['cat_id']."\" />";
					echo "Soll die Kategorie <b>".$arr['cat_name']."</b> und alle darin enthaltenen Topics und Posts gelöscht werden?";
					echo "<br/><br/><input type=\"submit\" value=\"Löschen\" name=\"cat_delete\" value=\"save_edit\" onclick=\"return confirm('Willst du die Kategorie \'".$arr['cat_name']."\' wirklich löschen?');\" /> ";
				}
				else
					echo "<b>Fehler!</b> Datensatz nicht gefunden!<br/><br/>";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
			//
			// Show forum categories; this ist the default view
			//	
			else
			{
				echo "<h2>Übersicht</h2>";
				
				if ($_POST['cat_new']!="" && $_POST['cat_name']!="")
				{
					dbquery("INSERT INTO ".BOARD_CAT_TABLE." (
					cat_name,
					cat_desc,
					cat_order,
					cat_bullet,
					cat_alliance_id
					) VALUES(
					'".addslashes($_POST['cat_name'])."',
					'".addslashes($_POST['cat_desc'])."',
					'".$_POST['cat_order']."',
					'".$_POST['cat_bullet']."',
					'".BOARD_ALLIANCE_ID."');");
					$cid=mysql_insert_id();
					foreach ($_POST['cr'] as $k=>$v)
						dbquery("INSERT INTO ".$db_table['allianceboard_catranks']." (cr_cat_id,cr_rank_id) VALUES (".$cid.",$k);");
					echo "Neue Kategorie gespeichert!<br/><br/>";
				}
				elseif ($_POST['cat_edit']!="" && $_POST['cat_name']!="" && $_POST['cat_id']>0)
				{
					dbquery("UPDATE ".BOARD_CAT_TABLE." SET 
					cat_name='".addslashes($_POST['cat_name'])."',
					cat_desc='".addslashes($_POST['cat_desc'])."',
					cat_order='".$_POST['cat_order']."',
					cat_bullet='".$_POST['cat_bullet']."' 
					WHERE cat_id=".$_POST['cat_id']." AND cat_alliance_id=".BOARD_ALLIANCE_ID.";");
					dbquery("DELETE FROM ".$db_table['allianceboard_catranks']." WHERE cr_cat_id=".$_POST['cat_id'].";");
					foreach ($_POST['cr'] as $k=>$v)
						dbquery("INSERT INTO ".$db_table['allianceboard_catranks']." (cr_cat_id,cr_rank_id) VALUES (".$_POST['cat_id'].",$k);");
					
					echo "&Auml;nderungen gespeichert!<br/><br/>";
				}
				elseif ($_POST['cat_delete']!="" && $_POST['cat_id']>0)
				{
					$tres=dbquery("SELECT topic_id FROM ".BOARD_TOPIC_TABLE." WHERE topic_cat_id=".$_POST['cat_id'].";");
					if (mysql_num_rows($tres)>0)
					{
						while ($tarr=mysql_fetch_array($tres))
						{
							dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$tarr['topic_id'].";");
						}
						dbquery("DELETE FROM ".BOARD_TOPIC_TABLE." WHERE topic_cat_id=".$_POST['cat_id'].";");
					}
					dbquery("DELETE FROM ".BOARD_CAT_TABLE." WHERE cat_id=".$_POST['cat_id']." AND cat_alliance_id=".BOARD_ALLIANCE_ID.";");
					echo "Kategorie gelöscht!<br/><br/>";
				}
				
				$res=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." ORDER BY cat_order, cat_name");		
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">";
					echo "<tr><th colspan=\"2\">Kategorie</th><th>Posts</th><th>Topics</th><th>Letzer Beitrag</th>";
					if ($s['admin'])
					{					
						echo "<th>Aktionen</th>";
					}
					echo "</tr>";
					$accessCnt=0;
					while ($arr=mysql_fetch_array($res))
					{
						if ($s['admin'] || $myCat[$arr['cat_id']])
						{
							$accessCnt++;
							$pres=dbquery("SELECT topic_subject,post_id,topic_id,topic_timestamp,post_user_id FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE." WHERE post_topic_id=topic_id AND topic_cat_id=".$arr['cat_id']." ORDER BY post_timestamp DESC LIMIT 1;");
							if (mysql_num_rows($pres)>0)
							{
								$parr=mysql_fetch_row($pres);
								$ps="<a href=\"?page=$page&amp;topic=".$parr[2]."#".$parr[1]."\" ".tm($parr[0].", ".df($parr[3]),"Geschrieben von: <b>".$user[$parr[4]]['nick']."</b>").">".$parr[0]."<br/>".df($parr[3])."</a>";
							}
							else
								$ps="-";
							echo "<tr>";
							if ($arr['cat_bullet']=="" || !is_file(BOARD_BULLET_DIR."/".$arr['cat_bullet'])) $arr['cat_bullet']=BOARD_DEFAULT_IMAGE;
							echo "<td style=\"width:40px;\"><img src=\"".BOARD_BULLET_DIR."/".$arr['cat_bullet']."\" style=\"width:40px;height:40px;\" /></td>";
							echo "<td style=\"width:300px;\"";
							if ($s['admin'])
							{
								$rstr="";
								foreach ($rank as $k=>$v)
								{
									$crres=dbquery("SELECT cr_id FROM ".$db_table['allianceboard_catranks']." WHERE cr_rank_id=".$k." AND cr_cat_id=".$arr['cat_id'].";");								
									if (mysql_num_rows($crres)>0)
										$rstr.= $v.", ";
								}									
								if ($rstr!="") $rstr=substr($rstr,0,strlen($rstr)-2);
								echo " ".tm("Admin-Info: ".stripslashes($arr['cat_name']),"<b>Position:</b> ".$arr['cat_order']."<br/><b>Zugriff:</b> ".$rstr)."";
							}
							echo "><b><a href=\"?page=$page&amp;cat=".$arr['cat_id']."\"";
							//if ($s['last_activity']<$parr[3] && $s)
							//	echo " style=\"color:#c00;\"";
							echo ">".stripslashes($arr['cat_name'])."</a></b><br/>".text2html($arr['cat_desc'])."</td>";
							$fres=dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE." WHERE post_topic_id=topic_id AND topic_cat_id=".$arr['cat_id'].";");
							$farr=mysql_fetch_row($fres);
							echo "<td>".$farr[0]."</td>";
							$fres=dbquery("SELECT COUNT(*) FROM ".BOARD_TOPIC_TABLE." WHERE topic_cat_id=".$arr['cat_id'].";");
							$farr=mysql_fetch_row($fres);
							echo "<td>".$farr[0]."</td>";
							echo "<td>$ps</td>";
							if ($s['admin'])
							{
								echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&editcat=".$arr['cat_id']."'\" /><br/> 
								<input type=\"button\" value=\"L&ouml;schen\" onclick=\"document.location='?page=$page&delcat=".$arr['cat_id']."'\" /></td>";
							}
							echo "</tr>";			
						}
					}
					if ($accessCnt==0)
						echo "<tr><td class=\"tbldata\" colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
					echo "</table><br/>";
				}
				else
					echo "<i>Keine Kategorien vorhanden!</i><br/>";
				if ($s['admin'])
					echo "<br/><input type=\"button\" value=\"Neue Kategorie erstellen\" onclick=\"document.location='?page=$page&action=newcat'\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Zur Allianzseite\" onclick=\"document.location='?page=alliance'\" />";
		
				
				
			}
		}
		else
			echo "<b>Fehler!</b> Die Allianz existiert nicht!";
	}
	else
		echo "<b>Fehler!</b> Du bist in keiner Allianz und kannst darum das Allianzboard nicht nutzen!";
?>