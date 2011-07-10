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
	// $Author$
	// $Date$
	// $Rev$
	//
	
	/**
	* Internal messageboard for alliances
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/
					
	echo "<h1>Allianzforum</h1>";

	// Prüfen ob User in Allianz ist
	if ($cu->allianceId>0)
	{
		// Prüfen ob Allianz existiert
		$res=dbquery("SELECT alliance_id,alliance_founder_id FROM alliances WHERE alliance_id='".$cu->allianceId."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			define('BOARD_ALLIANCE_ID',$arr['alliance_id']);
			
			//Get Variablen überprüfen und IDs zuordnen
			$legal=TRUE;
			if (isset($_GET['bnd']) && $_GET['bnd']>0)
			{
				$bres=dbquery("SELECT * FROM alliance_bnd WHERE (alliance_bnd_alliance_id1=".BOARD_ALLIANCE_ID." || alliance_bnd_alliance_id2=".BOARD_ALLIANCE_ID.") AND alliance_bnd_id=".$_GET['bnd']." AND alliance_bnd_level=2;");
				if (mysql_num_rows($bres)>0)
				{		
					$barr=mysql_fetch_array($bres);
					if ($barr['alliance_bnd_alliance_id2']==BOARD_ALLIANCE_ID)
					{
						$alliance_bnd_id=$barr['alliance_bnd_alliance_id1'];
					}
					else
					{
						$alliance_bnd_id=$barr['alliance_bnd_alliance_id2'];
					}

					$alliance=get_alliance_names2($alliance_bnd_id);
					$_GET['cat']=0;
				}
				else
				{
					$legal=FALSE;
				}
			}
			else
			{
				$_GET['bnd']=0;
			}

			// Eigenen Rang laden
			$ures=dbquery("
			SELECT
				user_alliance_rank_id
			FROM
				users
			WHERE
				user_id=".$cu->id."
				AND user_alliance_id=".BOARD_ALLIANCE_ID.";");
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_array($ures);
  			$myRankId=$uarr['user_alliance_rank_id'];
  		}
  		else
  			$myRankId=0;
			
			// Rechte laden
			$rightres=dbquery("SELECT * FROM alliance_rights ORDER BY right_desc;");
			$rights=array();
			if (mysql_num_rows($rightres)>0)
			{
				while ($rightarr=mysql_fetch_array($rightres))
				{
					$rights[$rightarr['right_id']]['key']=$rightarr['right_key'];
					$rights[$rightarr['right_id']]['desc']=$rightarr['right_desc'];
					if (mysql_num_rows(dbquery("SELECT rr_id FROM alliance_rankrights,alliance_ranks WHERE rank_id=rr_rank_id AND rank_alliance_id=".BOARD_ALLIANCE_ID." AND rr_right_id=".$rightarr['right_id']." AND rr_rank_id=".$myRankId.";"))>0)								
						$myRight[$rightarr['right_key']]=true;
					else
						$myRight[$rightarr['right_key']]=false;
				}
			}	
			
			// Ränge laden
			$rres = dbquery("SELECT rank_name,rank_id FROM alliance_ranks WHERE rank_alliance_id=".BOARD_ALLIANCE_ID.";");
			$rank=array();
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
					if (mysql_num_rows(dbquery("SELECT cr_id FROM allianceboard_catranks,alliance_ranks WHERE rank_id=cr_rank_id AND rank_alliance_id=".BOARD_ALLIANCE_ID." AND cr_cat_id=".$catarr['cat_id']." AND cr_rank_id=".$myRankId.";"))>0)								
						$myCat[$catarr['cat_id']]=true;
					else
						$myCat[$catarr['cat_id']]=false;
				}
			}							
			
			// Gründer prüfen
			if ($arr['alliance_founder_id']==$cu->id)
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
			echo "function changeAvatar(elem) { document.getElementById('avatar').src='".BOARD_AVATAR_DIR."/'+elem.options[elem.selectedIndex].value;}";
			echo "function changeBullet(elem) { document.getElementById('bullet').src='".BOARD_BULLET_DIR."/'+elem.options[elem.selectedIndex].value;}";
			echo "</script>";		
		
		
			// Board-Admin prüfen
			if (Alliance::checkActionRights('allianceboard',FALSE) || $isFounder)
				$isAdmin=true;
			else
				$isAdmin=false;
				
				
			
			//
			// Create new post in topic
			//
			if (isset($_GET['newpost']) && $_GET['newpost']>0 && $cu->id>0 && legal==TRUE)
			{
				if (isset($alliance_bnd_id))
				{
					$tres=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE.",".BOARD_CAT_TABLE." WHERE topic_id=".$_GET['newpost']." AND topic_cat_id=0;");
				}
				else
				{
					$tres=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE.",".BOARD_CAT_TABLE." WHERE topic_id=".$_GET['newpost']." AND topic_cat_id=cat_id AND cat_alliance_id=".BOARD_ALLIANCE_ID.";");
				}
				if (mysql_num_rows($tres)>0)
				{		
					$tarr=mysql_fetch_array($tres);
					if ($tarr['topic_closed']==0)
					{
						echo "<form action=\"?page=$page&amp;topic=".$_GET['newpost']."&bnd=".$_GET['bnd']."\" method=\"post\">";
						if (isset($alliance_bnd_id))
						{
							echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;bnd=".$tarr['topic_bnd_id']."\">".$alliance[$alliance_bnd_id]['name']."</a> &gt; <a href=\"?page=$page&amp;topic=".$_GET['newpost']."\">".$tarr['topic_subject']."</a> &gt; Neuer Beitrag</h2>";
						}
						else
						{
							echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=".$tarr['cat_id']."\">".$tarr['cat_name']."</a> &gt; <a href=\"?page=$page&amp;topic=".$_GET['newpost']."\">".$tarr['topic_subject']."</a> &gt; Neuer Beitrag</h2>";
						}
						tablestart();
						echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"10\" cols=\"90\"></textarea></td></tr>";
						tableEnd();
						echo "<input type=\"submit\" name=\"submit\" value=\"Speichern\" /> &nbsp; ";
					}
					else
						error_msg("Dieses Thema ist geschlossen!",1);
				}
				else
					error_msg("Dieses Thema existiert nicht!");
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Beitrags abgebrochen werden?')) document.location='?page=$page&bnd=".$_GET['bnd']."&topic=".$tarr['topic_id']."'\" /></form>";
			}
			
			//
			// Edit Post
			//
			elseif(isset($_GET['editpost']) && $_GET['editpost']>0 && $s)
			{
				echo "<h2>Beitrag bearbeiten</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_GET['editpost'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($cu->id==$arr['post_user_id'] || $isAdmin)
					{
						echo "<form action=\"?page=$page&amp;bnd=".$_GET['bnd']."&topic=".$arr['post_topic_id']."\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"post_id\" value=\"".$arr['post_id']."\" />";
						tableStart();
						echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"10\" cols=\"90\">".stripslashes($arr['post_text'])."</textarea></td></tr>";
						tableEnd();
						echo "<input type=\"submit\" value=\"Speichern\" name=\"post_edit\" /> &nbsp; ";
					}
					else
						error_msg("Keine Berechtigung!");
				}
				else
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&bnd=".$_GET['bnd']."&topic=".$arr['post_topic_id']."#".$arr['post_id']."'\" /></form>";
			}
				
			//
			// Delete Post
			//
			elseif(isset($_GET['delpost']) && $_GET['delpost']>0 && $s)
			{
				echo "<h2>Beitrag löschen</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_GET['delpost'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($cu->id==$arr['post_user_id'] || $isAdmin)
					{
						echo "<form action=\"?page=$page&amp;bnd=".$_GET['bnd']."&topic=".$arr['post_topic_id']."\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"post_id\" value=\"".$arr['post_id']."\" />";
						iBoxStart("Soll der folgende Beitrag wirklich gelöscht werden?");
						echo text2html($arr['post_text']);
						iBoxEnd();
						echo "<input type=\"submit\" value=\"L&ouml;schen\" name=\"post_delete\" onclick=\"return confirm('Wirklich löschen?');\" /> &nbsp; ";
					}
					else
						error_msg("Keine Berechtigung!");
				}
				else
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&bnd=".$_GET['bnd']."&topic=".$arr['post_topic_id']."#".$arr['post_id']."' \" /></form>";
			}		
			
			//
			// Show topic with it's posts
			//	
			elseif (isset($_GET['topic']) && $_GET['topic']>0 && $legal=TRUE)
			{
				$sql = "SELECT * FROM ".BOARD_TOPIC_TABLE." LEFT JOIN ".BOARD_CAT_TABLE." ON topic_cat_id=cat_id WHERE topic_id=".$_GET['topic']." LIMIT 1";
				$tres=dbquery($sql);
				
				if (mysql_num_rows($tres)>0)
				{
					$tarr=mysql_fetch_array($tres);
					if (($alliance_bnd_id===$tarr['topic_bnd_id'] && $isAdmin) || (isset($myCat[$tarr['cat_id']]) && ($isAdmin ||$myCat[$tarr['cat_id']])))
					{				
						if ($tarr['topic_bnd_id']>0)
						{
							echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;bnd=".$tarr['topic_bnd_id']."\">".$alliance[$alliance_bnd_id]['name']."</a> &gt; ".$tarr['topic_subject']."</h2>";
						}
						else
						{	
							echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=".$tarr['cat_id']."\">".$tarr['cat_name']."</a> &gt; ".$tarr['topic_subject']."</h2>";
						}
						if ($tarr['topic_closed']==1)
						{
							echo "<img src=\"images/closed.gif\" alt=\"closed\" style=\"width:15px;height:16px;\" /> <i>Dieses Thema ist geschlossen und es können keine weiteren Beiträge erstellt werden!</i><br/><br/>";
						}
			
						// Save new post
						if (isset($_POST['submit']) && isset($_POST['post_text']) && $cu->id>0 && $tarr['topic_closed']==0)
						{
							dbquery("INSERT INTO ".BOARD_POSTS_TABLE." (post_topic_id,post_user_id,post_user_nick,post_text,post_timestamp) VALUES (".$_GET['topic'].",".$cu->id.",'".$cu->nick."','".addslashes($_POST['post_text'])."',".time().");");
							$mid=mysql_insert_id();
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_timestamp=".time()." WHERE topic_id=".$_GET['topic'].";");			
							ok_msg("Beitrag gespeichert!");
							echo "<script type=\"text/javascript\">document.location='?page=$page&bnd=".$_GET['bnd']."&topic=".$_GET['topic']."#".$mid."';</script>";
						}
						else
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_count=topic_count+1  WHERE topic_id=".$_GET['topic'].";");			
			
						// Edit post
						if (isset($_POST['post_edit']) && isset($_POST['post_text']) && isset($_POST['post_id']) && ($cu->id>0 || $isAdmin))
						{
							if ($isAdmin)
								dbquery("UPDATE ".BOARD_POSTS_TABLE." SET post_text='".addslashes($_POST['post_text'])."',post_changed=".time()." WHERE post_id=".$_POST['post_id'].";");
							else
								dbquery("UPDATE ".BOARD_POSTS_TABLE." SET post_text='".addslashes($_POST['post_text'])."',post_changed=".time()." WHERE post_id=".$_POST['post_id']." AND post_user_id=".$cu->id.";");
							ok_msg("&Auml;nderungen gespeichert!");
							echo "<script type=\"text/javascript\">document.location='?page=$page&bnd=".$_GET['bnd']."&topic=".$_GET['topic']."#".$_POST['post_id']."';</script>";
						}
						
						// Delete post
						if (isset($_POST['post_delete']) && isset($_POST['post_id']) && ($cu->id>0 || $isAdmin))
						{
							if ($isAdmin)
								dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_POST['post_id'].";");
							else
								dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_id=".$_POST['post_id']." AND post_user_id=".$cu->id.";");
							
							ok_msg("Beitrag gelöscht");
						}			
				
						$res=dbquery("SELECT * FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$_GET['topic']." ORDER BY post_timestamp ASC;");					
						if (mysql_num_rows($res)>0)
						{			
							tableStart($tarr['topic_subject']);
							while ($arr=mysql_fetch_array($res))
							{
								echo "<tr><th style=\"width:150px;\"><a name=\"".$arr['post_id']."\"></a><a href=\"?page=userinfo&amp;id=".$arr['post_user_id']."\">".$arr['post_user_nick']."</a><br/>";
								show_avatar($user[$arr['post_user_id']]['avatar']);
								$parr=mysql_fetch_row(dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE.",".BOARD_CAT_TABLE." WHERE post_topic_id=topic_id AND topic_cat_id=cat_id AND cat_alliance_id=".BOARD_ALLIANCE_ID." AND post_user_id=".$arr['post_user_id'].";"));
								$parr1=mysql_fetch_row(dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE.",alliance_bnd WHERE post_topic_id=topic_id AND topic_bnd_id=alliance_bnd_id AND (alliance_bnd_alliance_id1=".BOARD_ALLIANCE_ID." OR alliance_bnd_alliance_id2=".BOARD_ALLIANCE_ID.") AND post_user_id=".$arr['post_user_id'].";"));
								$cpost=$parr[0]+$parr1[0];
								echo "Beitr&auml;ge: ".$cpost."<br/><br/>".df($arr['post_timestamp'])." Uhr";
								if ($isAdmin || $arr['post_user_id']==$cu->id)
									echo "<br/><a href=\"?page=$page&amp;bnd=".$_GET['bnd']."&editpost=".$arr['post_id']."\"><img src=\"images/edit.gif\" alt=\"edit\" style=\"border:none\" /></a> <a href=\"?page=$page&amp;bnd=".$_GET['bnd']."&delpost=".$arr['post_id']."\"><img src=\"images/delete.gif\" alt=\"del\" style=\"border:none;\" /></a>";
								echo "</th>";
								echo "<td";
								if (isset($urank) && $user[$arr['post_user_id']]['rank']==count($urank)-1) 
									echo " style=\"color:".ADMIN_COLOR."\"";

								echo ">".text2html($arr['post_text']);
								if ($arr['post_changed']>0)
									echo "<br/><br/><span style=\"font-size:8pt;\">Dieser Beitrag wurde zuletzt geändert am ".date("d.m.Y",$arr['post_changed'])." um ".date("H:i",$arr['post_changed'])." Uhr.</span>";
								if ($user[$arr['post_user_id']]['signature']!="")
									echo "<hr>".text2html($user[$arr['post_user_id']]['signature']);
								echo "</td></tr>";
							}
							tableEnd();
						}
						else
						{
							$res = dbquery("SELECT topic_cat_id FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_GET['topic'].";");
							dbquery("DELETE FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_GET['topic'].";");
							if (mysql_num_rows($res))
							{
								$arr = mysql_fetch_assoc($res);
								echo "<script>document.location='?page=$page&cat=".$arr['topic_cat_id']."';</script>
									Klicke <a href=\"?page=$page&cat=".$arr['topic_cat_id']."\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
							}
							else
							{
								echo "<script>document.location='?page=$page';</script>
									Klicke <a href=\"?page=$page\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
							}
						}
						if ($cu->id>0 && $tarr['topic_closed']==0)
							echo "<input type=\"button\" value=\"Neuer Beitrag\" onclick=\"document.location='?page=$page&amp;bnd=".$_GET['bnd']."&newpost=".$_GET['topic']."'\" /> &nbsp; ";
					}
					else
						error_msg("Kein Zugriff!");
				}
				else
					error_msg("Dieses Thema existiert nicht!");
				if ($tarr['topic_bnd_id']>0)
				{
					echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;bnd=".$tarr['topic_bnd_id']."'\" />";
				}
				else
				{
					echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;cat=".$tarr['cat_id']."'\" />";
				}
			}
			
			//
			// Create new topic in category
			//
			elseif (isset($_GET['newtopic']) && $_GET['newtopic']>0 && $cu->id>0 && $legal=TRUE)
			{
				if (isset($_GET['bnd']) && $_GET['bnd']>0)
				{
					echo "<form action=\"?page=$page&amp;bnd=".$_GET['bnd']."\" method=\"post\">";
					echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;bnd=".$_GET['bnd']."\">".$alliance[$alliance_bnd_id]['name']."</a> &gt; Neues Thema</h2>";
				}
				else
				{
					$tres=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_id=".$_GET['newtopic'].";");
					if (mysql_num_rows($tres)>0)
					{		
						$tarr=mysql_fetch_array($tres);
						echo "<form action=\"?page=$page&amp;cat=".$_GET['newtopic']."\" method=\"post\">";
						echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=".$tarr['cat_id']."\">".$tarr['cat_name']."</a> &gt; Neues Thema</h2>";

					}
					else
						$legal=FALSE;
				}
				if ($legal==TRUE)
				{					
					tableStart();
					echo "<tr><th>Titel:</th><td><input name=\"topic_subject\" type=\"text\" size=\"40\" /></td></tr>";
					echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"6\" cols=\"80\"></textarea></td></tr>";
					tableEnd();
					echo "<input type=\"submit\" name=\"submit\" value=\"Speichern\" /> &nbsp; ";
				}
				else
					error_msg("Diese Kategorie existiert nicht!");
				if (!isset($_GET['bnd']))
				{				
					echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Themas abgebrochen werden?')) document.location='?page=$page&amp;cat=".$tarr['cat_id']."'\" /></form>";
				}
				else
				{
					echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Themas abgebrochen werden?')) document.location='?page=$page&amp;bnd=".$_GET['bnd']."'\" /></form>";
				}
			}
			
	
			//
			// Edit a topic
			//
			elseif(isset($_GET['edittopic']) && $_GET['edittopic']>0 && $s  && $legal==TRUE)
			{
				echo "<h2>Thema bearbeiten</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_GET['edittopic']." AND topic_bnd_id=".$_GET['bnd'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($cu->id==$arr['topic_user_id'] || $isAdmin)
					{
						echo "<form action=\"?page=$page&amp;bnd=".$_GET['bnd']."&cat=".$arr['topic_cat_id']."\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"topic_id\" value=\"".$arr['topic_id']."\" />";
						echo "<input type=\"hidden\" name=\"topic_bnd_id\" value=\"".$arr['topic_bnd_id']."\" />";
						tableStart();
						echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"topic_subject\" size=\"40\" value=\"".$arr['topic_subject']."\" /></td></tr>";
						if ($isAdmin)
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
							if ($_GET['bnd']!=0)
							{
								echo "<tr><th>Kategorie:</th><td>".$alliance[$alliance_bnd_id]['name']."</td></tr>";
							}
							else
							{
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
						
						}
						tableEnd();
						echo "<input type=\"submit\" name=\"topic_edit\" value=\"Speichern\" /> ";
					}
					else
						error_msg("Keine Berechtigung!");
				}
				else
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&amp;bnd=".$arr['topic_bnd_id']."'\" /></form>";
			}
			
			//
			// Delete a topic and all it's posts
			//
			elseif(isset($_GET['deltopic']) && $_GET['deltopic']>0 && $isAdmin)
			{
				echo "<h2>Thema löschen</h2>";
				$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_GET['deltopic'].";");		
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					echo "<form action=\"?page=$page&amp;bnd=".$arr['topic_bnd_id']."&amp;cat=".$arr['topic_cat_id']."\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"topic_id\" value=\"".$arr['topic_id']."\" />";
					echo "Soll der Beitrag <b>".$arr['topic_subject']."</b> und alle darin enthaltenen Posts gelöscht werden?";
					echo "<br/><br/><input type=\"submit\" name=\"topic_delete\" value=\"L&ouml;schen\" onclick=\"return confirm('Willst du das Thema \'".$arr['topic_subject']."\' wirklich löschen?');\" /> ";
				}
				else
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&amp;bnd=".$arr['topic_bnd_id']."'\" /></form>";
			}	
			
			//
			// Show topics in category
			//
			elseif (isset($_GET['cat']) && $_GET['cat']>0)
			{
				if ($isAdmin || isset($myCat[$_GET['cat']]))
				{
					$cres=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." AND cat_id=".$_GET['cat'].";");		
					if (mysql_num_rows($cres)>0)
					{
						$carr=mysql_fetch_array($cres);
						echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; ".($carr['cat_name']!="" ? stripslashes($carr['cat_name']) : "Unbenannt")."</h2>";
						
						// Save new topic
						if (isset($_POST['submit']) && isset($_POST['topic_subject']) && isset($_POST['post_text']) && $cu->id>0)
						{
							dbquery("INSERT INTO ".BOARD_TOPIC_TABLE." (topic_subject,topic_cat_id,topic_user_id,topic_user_nick,topic_timestamp) VALUES ('".addslashes($_POST['topic_subject'])."',".$_GET['cat'].",".$cu->id.",'".$cu->nick."',".time().");");
							$mid=mysql_insert_id();
							dbquery("INSERT INTO ".BOARD_POSTS_TABLE." (post_topic_id,post_user_id,post_user_nick,post_text,post_timestamp) VALUES (".$mid.",".$cu->id.",'".$cu->nick."','".addslashes($_POST['post_text'])."',".time().");");
							$pmid=mysql_insert_id();
							echo "<script type=\"text/javascript\">document.location='?page=$page&topic=".$mid."#".$pmid."';</script>";
						}			
						// Save edited topic
						elseif (isset($_POST['topic_edit']) && isset($_POST['topic_subject']) && isset($_POST['topic_id']) && $_POST['topic_id']>0)
						{
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_subject='".$_POST['topic_subject']."',topic_top='".$_POST['topic_top']."',topic_closed='".$_POST['topic_closed']."',topic_cat_id='".$_POST['topic_cat_id']."',topic_bnd_id='".$_POST['topic_bnd_id']."' WHERE topic_id=".$_POST['topic_id']."");
							echo "&Auml;nderungen gespeichert!<br/><br/>";
							if ($_POST['topic_cat_id']!=$_GET['cat'])
								echo "<script type=\"text/javascript\">document.location='?page=$page&amp;cat=".$_POST['topic_cat_id']."';</script>";
						}
						// Delete topic
						elseif (isset($_POST['topic_delete']) && isset($_POST['topic_id']) && $_POST['topic_id']>0)
						{
							dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$_POST['topic_id'].";");
							dbquery("DELETE FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_POST['topic_id'].";");
							ok_msg("Thema gelöscht!");
						}			
						
						$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_cat_id=".$_GET['cat']." ORDER BY topic_top DESC,topic_timestamp DESC, topic_subject ASC;");					
						if (mysql_num_rows($res)>0)
						{			
							tableStart();
							echo "<tr><th colspan=\"2\">Thema</th><th>Posts</th><th>Aufrufe</th><th>Autor</th><th>Letzer Beitrag</th>";
							if ($isAdmin)
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

								echo ">".$arr['topic_subject']."</a></td>";
								$parr=mysql_fetch_row(dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$arr['topic_id'].";"));
								echo "<td>".$parr[0]."</td>";
								echo "<td>".$arr['topic_count']."</td>";
								echo "<td>".$user[$arr['topic_user_id']]['nick']."</td>";
								$parr=mysql_fetch_array(dbquery("SELECT post_id,post_timestamp,post_user_id,post_user_nick FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$arr['topic_id']." ORDER BY post_timestamp DESC LIMIT 1;"));
								echo "<td><a href=\"?page=$page&amp;topic=".$arr['topic_id']."#".$parr['post_id']."\">".df($parr['post_timestamp'])."</a><br/>".$parr['post_user_nick']."</td>";				
								if ($isAdmin || $cu->id==$arr['topic_user_id'])
								{
									echo "<td style=\"vertical-align:middle;text-align:center;\">
									<a href=\"?page=$page&edittopic=".$arr['topic_id']."\" title=\"Thema bearbeiten\">".icon('edit')."</a>";
									if ($isAdmin)
										echo " <a href=\"?page=$page&deltopic=".$arr['topic_id']."\" title=\"Thema löschen \">".icon('delete')."</a>";
									echo "</td>";
								}
								echo "</tr>";
							}
							tableEnd();
						}
						else
							error_msg("Es sind noch keine Themen vorhanden!");
						if ($cu->id>0)
							echo "<input type=\"button\" value=\"Neues Thema\" onclick=\"document.location='?page=$page&newtopic=".$_GET['cat']."'\" /> &nbsp; ";
					}
					else
						error_msg("Kategorie existiert nicht!");
						
					
				}
				else
					error_msg("Kein Zugriff!");
				echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
			}
		
			//
			// Show bnd topics in category
			//
			elseif (isset($_GET['bnd']) && $_GET['bnd']>0 )
			{
				if ($isAdmin || isset($myCat[$_GET['cat']]))
				{
					if ($legal=TRUE)
					{								
						echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; ".$alliance[$alliance_bnd_id]['name']."</h2>";
						
						// Save new topic
						if (isset($_POST['submit']) && isset($_POST['topic_subject']) && isset($_POST['post_text']) && $cu->id>0)
						{
							dbquery("INSERT INTO ".BOARD_TOPIC_TABLE." (topic_subject,topic_bnd_id,topic_user_id,topic_user_nick,topic_timestamp) VALUES ('".addslashes($_POST['topic_subject'])."',".$_GET['bnd'].",".$cu->id.",'".$cu->nick."',".time().");");
							$mid=mysql_insert_id();
							dbquery("INSERT INTO ".BOARD_POSTS_TABLE." (post_topic_id,post_user_id,post_user_nick,post_text,post_timestamp) VALUES (".$mid.",".$cu->id.",'".$cu->nick."','".addslashes($_POST['post_text'])."',".time().");");
							$pmid=mysql_insert_id();
							echo "<script type=\"text/javascript\">document.location='?page=$page&bnd=".$_GET['bnd']."&topic=".$mid."#".$pmid."';</script>";
						}			
						// Save edited topic
						elseif (isset($_POST['topic_edit']) && isset($_POST['topic_subject']) && isset($_POST['topic_id']) && $_POST['topic_id']>0)
						{
							dbquery("UPDATE ".BOARD_TOPIC_TABLE." SET topic_subject='".$_POST['topic_subject']."',topic_top='".$_POST['topic_top']."',topic_closed='".$_POST['topic_closed']."',topic_bnd_id='".$_POST['topic_bnd_id']."' WHERE topic_id=".$_POST['topic_id']."");
							ok_msg("&Auml;nderungen gespeichert!");
							if ($_POST['topic_bnd_id']!=$_GET['bnd'])
								echo "<script type=\"text/javascript\">document.location='?page=$page&amp;bnd=".$_POST['topic_bnd_id']."';</script>";
						}
						// Delete topic
						elseif (isset($_POST['topic_delete']) && isset($_POST['topic_id']) && $_POST['topic_id']>0)
						{
							dbquery("DELETE FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$_POST['topic_id'].";");
							dbquery("DELETE FROM ".BOARD_TOPIC_TABLE." WHERE topic_id=".$_POST['topic_id'].";");
							ok_msg("Thema gelöscht!");
						}
			
						
						$res=dbquery("SELECT * FROM ".BOARD_TOPIC_TABLE." WHERE topic_bnd_id=".$_GET['bnd']." ORDER BY topic_top DESC,topic_timestamp DESC, topic_subject ASC;");					
						if (mysql_num_rows($res)>0)
						{			
							tableStart();
							echo "<tr><th colspan=\"2\">Thema</th><th>Posts</th><th>Aufrufe</th><th>Autor</th><th>Letzer Beitrag</th>";
							if ($isAdmin)
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
								echo "<td style=\"width:250px;\"><a href=\"?page=$page&amp;bnd=".$_GET['bnd']."&topic=".$arr['topic_id']."\"";
								echo ">".$arr['topic_subject']."</a></td>";
								$parr=mysql_fetch_row(dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$arr['topic_id'].";"));
								echo "<td>".$parr[0]."</td>";
								echo "<td>".$arr['topic_count']."</td>";
								echo "<td>".$user[$arr['topic_user_id']]['nick']."</td>";
								$parr=mysql_fetch_array(dbquery("SELECT post_id,post_timestamp,post_user_id,post_user_nick FROM ".BOARD_POSTS_TABLE." WHERE post_topic_id=".$arr['topic_id']." ORDER BY post_timestamp DESC LIMIT 1;"));
								echo "<td><a href=\"?page=$page&amp;topic=".$arr['topic_id']."#".$parr['post_id']."\">".df($parr['post_timestamp'])."</a><br/>".$parr['post_user_nick']."</td>";				
								if ($isAdmin || $cu->id==$arr['topic_user_id'])
								{
									echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&bnd=".$_GET['bnd']."&edittopic=".$arr['topic_id']."'\" />";
									if ($isAdmin)
										echo " <input type=\"button\" value=\"L&ouml;schen\" onclick=\"document.location='?page=$page&deltopic=".$arr['topic_id']."'\" />";
									echo "</td>";
								}
								echo "</tr>";
							}
							tableEnd();
						}
						else
							error_msg("Es sind noch keine Themen vorhanden!");
						if ($cu->id>0)
							echo "<input type=\"button\" value=\"Neues Thema\" onclick=\"document.location='?page=$page&newtopic=".$_GET['bnd']."&bnd=".$_GET['bnd']."'\" /> &nbsp; ";
					}
					else
						error_msg("Kategorie existiert nicht!");
						
					
				}
				else
					error_msg("Kein Zugriff!");
				echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
			}
		
		
			//
			// New category
			//
			elseif(isset($_GET['action']) && $_GET['action']=="newcat" && $isAdmin)
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
				tableStart();
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
						echo ">$a</option>";
				}
				echo "</select></td></tr>";
				tableEnd();
				echo "<input type=\"submit\"name=\"cat_new\" value=\"Kategorie speichern\" /> ";
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
			//
			// Edit a category
			//
			elseif(isset($_GET['editcat']) && $_GET['editcat']>0 && $isAdmin)
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
					tableStart();
					echo "<tr><th>Name:</th><td><input type=\"text\" name=\"cat_name\" size=\"40\" value=\"".$arr['cat_name']."\" /></td></tr>";
					echo "<tr><th>Beschreibung:</th><td><input type=\"text\" name=\"cat_desc\" size=\"40\" value=\"".$arr['cat_desc']."\" /></td></tr>";
					echo "<tr><th>Reihenfolge/Position:</th><td><input type=\"text\" size=\"1\" maxlenght=\"2\" name=\"cat_order\" value=\"".$arr['cat_order']."\" /></td></tr>";
					echo "<tr><th>Zugriff:</th><td>";
					foreach ($rank as $k=>$v)
					{
						echo "<input type=\"checkbox\" name=\"cr[".$k."]\" value=\"1\" ";
						$crres=dbquery("SELECT cr_id FROM allianceboard_catranks WHERE cr_rank_id=".$k." AND cr_cat_id=".$arr['cat_id'].";");								
						if (mysql_num_rows($crres)>0)
							echo " checked=\"checked\" /><span style=\"color:#0f0;\">".$v."</span><br/>";
						else
							echo" /> <span style=\"color:#f50;\">".$v."</span><br/>";
					}						
					echo "</td></tr>";
					echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";	
					if ($arr['cat_bullet']=="" || !is_file(BOARD_BULLET_DIR."/".$arr['cat_bullet'])) $arr['cat_bullet']=BOARD_DEFAULT_IMAGE;
					echo "<img src=\"".BOARD_BULLET_DIR."/".$arr['cat_bullet']."\" style=\"width:38px;height:35px;\" id=\"bullet\" />";
					echo "<br/>Symbol ändern: <select name=\"cat_bullet\" onmousemove=\"changeBullet(this);\" onkeyup=\"changeBullet(this);\">";
					echo "<option value=\"".BOARD_DEFAULT_IMAGE."\">Standard-Symbol</option>";
					foreach ($bullets as $a)
					{
							echo "<option value=\"$a\"";
							if ($a==$arr['cat_bullet'] && $arr['cat_bullet']!="") echo " selected=\"selected\"";
							echo ">$a</option>";
					}
					echo "</select></td></tr>";
					
					tableEnd();
					echo "<input type=\"submit\" name=\"cat_edit\" value=\"Speichern\" /> ";
				}
				else
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
		//
		//edit a bnd category
		elseif(isset($_GET['editbnd']) && $_GET['editbnd']>0 && $isAdmin)
			{
				echo "<h2>Kategorie bearbeiten</h2>";
				$res=dbquery("SELECT * FROM alliance_bnd WHERE (alliance_bnd_alliance_id1=".BOARD_ALLIANCE_ID." || alliance_bnd_alliance_id2=".BOARD_ALLIANCE_ID.") AND alliance_bnd_id=".$_GET['editbnd'].";");		
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
					$alliance_bnd_id=0;
					if ($arr['alliance_bnd_alliance_id2']==BOARD_ALLIANCE_ID)
					{
						$alliance_bnd_id=$arr['alliance_bnd_alliance_id1'];
					}
					else
					{
						$alliance_bnd_id=$arr['alliance_bnd_alliance_id2'];
					}
					$alliance=get_alliance_names($alliance_bnd_id);
					echo "<form action=\"?page=$page\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"bnd_id\" value=\"".$arr['alliance_bnd_id']."\" />";
					tableStart();
					echo "<tr><th>Name:</th><td>".$alliance[$alliance_bnd_id]['name']."</td></tr>";
					echo "<tr><th>Beschreibung:</th><td>".$arr['alliance_bnd_text']."</td></tr>";
					//echo "<tr><th>Reihenfolge/Position:</th><td><input type=\"text\" size=\"1\" maxlenght=\"2\" name=\"cat_order\" value=\"".$arr['cat_order']."\" /></td></tr>";
					echo "<tr><th>Zugriff:</th><td>";
					foreach ($rank as $k=>$v)
					{
						echo "<input type=\"checkbox\" name=\"cr[".$k."]\" value=\"1\" ";
						$crres=dbquery("SELECT cr_id FROM allianceboard_catranks WHERE cr_rank_id=".$k." AND cr_bnd_id=".$arr['alliance_bnd_id'].";");								
						if (mysql_num_rows($crres)>0)
							echo " checked=\"checked\" /><span style=\"color:#0f0;\">".$v."</span><br/>";
						else
							echo" /> <span style=\"color:#f50;\">".$v."</span><br/>";
					}						
					echo "</td></tr>";
					/*echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";	
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
					echo "</select></td></tr>";*/
					
					tableEnd();
					echo "<input type=\"submit\" name=\"cat_edit\" value=\"Speichern\" /> ";
				}
				else
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
						
			//
			// Delete a forum category and all it's content
			//
			elseif(isset($_GET['delcat']) && $_GET['delcat']>0 && $isAdmin)
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
					error_msg("Datensatz nicht gefunden!");
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
			}
			
			//
			// Show forum categories; this ist the default view
			//	
			else
			{
				echo "<h2>Übersicht</h2>";
				
				if (isset($rank) && count($rank)>0)
				{
				
					if (isset($_POST['cat_new']) && isset($_POST['cat_name']))
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
						if (isset($_POST['cr']))
						{
							foreach ($_POST['cr'] as $k=>$v)
							{
								dbquery("INSERT INTO allianceboard_catranks (cr_cat_id,cr_rank_id) VALUES (".$cid.",$k);");
							}
						}
						ok_msg("Neue Kategorie gespeichert!");
					}
					elseif (isset($_POST['cat_edit']) && isset($_POST['cat_name']) && isset($_POST['cat_id']) && $_POST['cat_id']>0)
					{
						dbquery("UPDATE ".BOARD_CAT_TABLE." SET 
						cat_name='".addslashes($_POST['cat_name'])."',
						cat_desc='".addslashes($_POST['cat_desc'])."',
						cat_order='".$_POST['cat_order']."',
						cat_bullet='".$_POST['cat_bullet']."' 
						WHERE cat_id=".$_POST['cat_id']." AND cat_alliance_id=".BOARD_ALLIANCE_ID.";");
						dbquery("DELETE FROM allianceboard_catranks WHERE cr_cat_id=".$_POST['cat_id'].";");
						if (isset($_POST['cr']))
						{
							foreach ($_POST['cr'] as $k=>$v)
							{
								dbquery("INSERT INTO allianceboard_catranks (cr_cat_id,cr_rank_id) VALUES (".$_POST['cat_id'].",$k);");
							}
						}
						ok_msg("&Auml;nderungen gespeichert!");
					}
					elseif (isset($_POST['cat_edit']) && isset($_POST['bnd_id']) && $_POST['bnd_id']>0)
					{
						dbquery("DELETE FROM allianceboard_catranks WHERE cr_bnd_id=".$_POST['bnd_id'].";");
						if (isset($_POST['cr']))
						{
							foreach ($_POST['cr'] as $k=>$v)
							{
								dbquery("INSERT INTO allianceboard_catranks (cr_bnd_id,cr_rank_id) VALUES (".$_POST['bnd_id'].",$k);");
							}
						}
						ok_msg("&Auml;nderungen gespeichert!");
					}
					elseif (isset($_POST['cat_delete']) && isset($_POST['cat_id']) && $_POST['cat_id']>0)
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
						ok_msg("Kategorie gelöscht!");
					}
					
					$res=dbquery("SELECT * FROM ".BOARD_CAT_TABLE." WHERE cat_alliance_id=".BOARD_ALLIANCE_ID." ORDER BY cat_order, cat_name");		
					if (mysql_num_rows($res)>0)
					{
						tableStart();
						echo "<tr><th colspan=\"2\">Kategorie</th><th>Posts</th><th>Topics</th><th>Letzer Beitrag</th>";
						if ($isAdmin)
						{					
							echo "<th style=\"width:50px;\">Aktionen</th>";
						}
						echo "</tr>";
						$accessCnt=0;
						while ($arr=mysql_fetch_array($res))
						{
							if ($isAdmin || isset($myCat[$arr['cat_id']]))
							{
								$accessCnt++;
								$pres=dbquery("SELECT topic_subject,post_id,topic_id,topic_timestamp,post_user_id,post_user_nick FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE." WHERE post_topic_id=topic_id AND topic_cat_id=".$arr['cat_id']." ORDER BY post_timestamp DESC LIMIT 1;");
								if (mysql_num_rows($pres)>0)
								{
									$parr=mysql_fetch_row($pres);
									$ps="<a href=\"?page=$page&amp;topic=".$parr[2]."#".$parr[1]."\" ".tm($parr[0].", ".df($parr[3]),"Geschrieben von: <b>".$parr[5]."</b>").">".$parr[0]."<br/>".df($parr[3])."</a>";
								}
								else
									$ps="-";
								echo "<tr>";
								if ($arr['cat_bullet']=="" || !is_file(BOARD_BULLET_DIR."/".$arr['cat_bullet'])) $arr['cat_bullet']=BOARD_DEFAULT_IMAGE;
								echo "<td style=\"width:40px;vertical-align:middle;\">
									<a href=\"?page=$page&amp;bnd=0&cat=".$arr['cat_id']."\">
										<img src=\"".BOARD_BULLET_DIR."/".$arr['cat_bullet']."\" style=\"width:40px;height:40px;\" />
									</a>
								</td>";
								echo "<td style=\"width:300px;\"";
								if ($isAdmin)
								{
									$rstr="";
									foreach ($rank as $k=>$v)
									{
										$crres=dbquery("SELECT cr_id FROM allianceboard_catranks WHERE cr_rank_id=".$k." AND cr_cat_id=".$arr['cat_id'].";");								
										if (mysql_num_rows($crres)>0)
											$rstr.= $v.", ";
									}									
									if ($rstr!="") $rstr=substr($rstr,0,strlen($rstr)-2);
									echo " ".tm("Admin-Info: ".stripslashes($arr['cat_name']),"<b>Position:</b> ".$arr['cat_order']."<br/><b>Zugriff:</b> ".$rstr)."";
								}
								echo ">
								<b><a href=\"?page=$page&amp;bnd=0&cat=".$arr['cat_id']."\">".($arr['cat_name']!="" ? stripslashes($arr['cat_name']) : "Unbenannt")."</a></b>
								<br/>".text2html($arr['cat_desc'])."</td>";
								$fres=dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE." WHERE post_topic_id=topic_id AND topic_cat_id=".$arr['cat_id'].";");
								$farr=mysql_fetch_row($fres);
								echo "<td>".$farr[0]."</td>";
								$fres=dbquery("SELECT COUNT(*) FROM ".BOARD_TOPIC_TABLE." WHERE topic_cat_id=".$arr['cat_id'].";");
								$farr=mysql_fetch_row($fres);
								echo "<td>".$farr[0]."</td>";
								echo "<td>$ps</td>";
								if ($isAdmin)
								{
									echo "<td style=\"vertical-align:middle;text-align:center;\">
										<a href=\"?page=$page&editcat=".$arr['cat_id']."\">".icon('edit')."</a> 
										<a href=\"?page=$page&delcat=".$arr['cat_id']."\">".icon('delete')."</a>
									</td>";
								}
								echo "</tr>";			
							}
						}
						if ($accessCnt==0)
							echo "<tr><td colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
						tableEnd();
						
						
					}
					else
						error_msg("Keine Kategorien vorhanden!");
					if ($isAdmin)
						echo "<br/><input type=\"button\" value=\"Neue Kategorie erstellen\" onclick=\"document.location='?page=$page&action=newcat'\" /> &nbsp; ";
					echo "<input type=\"button\" value=\"Zur Allianzseite\" onclick=\"document.location='?page=alliance'\" /><br/><br/>";
					
					
					//shows Bnd forums
					$res=dbquery("SELECT * FROM alliance_bnd WHERE (alliance_bnd_alliance_id1=".BOARD_ALLIANCE_ID." || alliance_bnd_alliance_id2=".BOARD_ALLIANCE_ID.") AND alliance_bnd_level=2 ORDER BY alliance_bnd_id");		
					if (mysql_num_rows($res)>0)
					{
						tableStart();
						echo "<tr><th colspan=\"2\">Bündnisforen</th><th>Posts</th><th>Topics</th><th>Letzer Beitrag</th>";
						if ($isAdmin)
						{					
							echo "<th>Aktionen</th>";
						}
						echo "</tr>";
						$accessCnt=0;
						$alliance_bnd_id=0;
						while ($arr=mysql_fetch_array($res))
						{
							if ($arr['alliance_bnd_alliance_id2']==BOARD_ALLIANCE_ID)
							{
								$alliance_bnd_id=$arr['alliance_bnd_alliance_id1'];
							}
							else
							{
								$alliance_bnd_id=$arr['alliance_bnd_alliance_id2'];
							}
							$alliance=get_alliance_names();
							
							if ($isAdmin || isset($myCat[$arr['alliance_bnd_id']]))
							{
								$accessCnt++;
								$pres=dbquery("SELECT topic_subject,post_id,topic_id,topic_timestamp,post_user_id,post_user_nick FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE." WHERE post_topic_id=topic_id AND topic_bnd_id=".$arr['alliance_bnd_id']." ORDER BY post_timestamp DESC LIMIT 1;");
								if (mysql_num_rows($pres)>0)
								{
									$parr=mysql_fetch_row($pres);
									$ps="<a href=\"?page=$page&amp;topic=".$parr[2]."#".$parr[1]."\" ".tm($parr[0].", ".df($parr[3]),"Geschrieben von: <b>".$parr[5]."</b>").">".$parr[0]."<br/>".df($parr[3])."</a>";//ToDo User auch von anderen Allianzen
								}
								else
									$ps="-";
								echo "<tr>";
								if ($arr['cat_bullet']=="" || !is_file(BOARD_BULLET_DIR."/".$arr['cat_bullet'])) $arr['cat_bullet']=BOARD_DEFAULT_IMAGE;
								echo "<td style=\"width:40px;\"><img src=\"".BOARD_BULLET_DIR."/".$arr['cat_bullet']."\" style=\"width:40px;height:40px;\" /></td>";
								echo "<td style=\"width:300px;\"";
								if ($isAdmin)
								{
									$rstr="";
									foreach ($rank as $k=>$v)
									{
										$crres=dbquery("SELECT cr_id FROM allianceboard_catranks WHERE cr_rank_id=".$k." AND cr_bnd_id=".$arr['alliance_bnd_id'].";");								
										if (mysql_num_rows($crres)>0)
											$rstr.= $v.", ";
									}									
									if ($rstr!="") $rstr=substr($rstr,0,strlen($rstr)-2);
									echo " ".tm("Admin-Info: ".stripslashes($alliance[$alliance_bnd_id]['name']),/*"<b>Position:</b> ".$arr['cat_order']."<br/>*/"<b>Zugriff:</b> ".$rstr)."";
								}
								echo "><b><a href=\"?page=$page&amp;cat=0&bnd=".$arr['alliance_bnd_id']."\"";
								echo ">".stripslashes($alliance[$alliance_bnd_id]['name'])."</a></b><br/>".text2html($arr['alliance_bnd_text'])."</td>";
								$fres=dbquery("SELECT COUNT(*) FROM ".BOARD_POSTS_TABLE.",".BOARD_TOPIC_TABLE." WHERE post_topic_id=topic_id AND topic_bnd_id=".$arr['alliance_bnd_id'].";");
								$farr=mysql_fetch_row($fres);
								echo "<td>".$farr[0]."</td>";
								$fres=dbquery("SELECT COUNT(*) FROM ".BOARD_TOPIC_TABLE." WHERE topic_bnd_id=".$arr['alliance_bnd_id'].";");
								$farr=mysql_fetch_row($fres);
								echo "<td>".$farr[0]."</td>";
								echo "<td>$ps</td>";
								if ($isAdmin)
								{
									echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&editbnd=".$arr['alliance_bnd_id']."'\" /><br/> 
									</td>";
								}
								echo "</tr>";			
							}
						}
						if ($accessCnt==0)
							echo "<tr><td colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
						tableEnd();
					}

		
				}
				else
				{
					error_msg("Bevor das Forum benutzt werden kann müssen [url=?page=alliance&amp;action=ranks]Ränge[/url] erstellt werden!");
				}				
			}
		}
		else
			error_msg("Die Allianz existiert nicht!");
	}
	else
		error_msg("Du bist in keiner Allianz und kannst darum das Allianzboard nicht nutzen!");
?>