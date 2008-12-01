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
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

 
	// BEGIN SKRIPT //
	echo "<h1>Allianz</h1>";
	echo "<div id=\"allianceinfo\"></div>"; //nur zu entwicklungszwecken!


/**************************************************/
/* Allianzinformationen                           */
/**************************************************/	
	if ((isset($_GET['info_id']) && intval($_GET['info_id'])>0) || (isset($_GET['id']) && intval($_GET['id'])>0))
	{
		require("alliance/info.inc.php");
	}

/**************************************************/
/* User ist NICHT in einer Allianz                */
/**************************************************/
	elseif ($cu->allianceId == 0)
	{
		require("alliance/foreign.inc.php");
	}
	else
	{

/**************************************************/
/* User ist in der Allianz                        */
/**************************************************/

			$myRankId = $cu->allianceRankId;			
			
			// Allianzdaten laden
			$res = dbquery("
			SELECT
			    *
			FROM
				alliances
			WHERE
				alliance_id='".$cu->allianceId."'
			LIMIT 1;");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);

				$ally = new Alliance($cu->allianceId);


				// Rechte laden
				$rightres=dbquery("SELECT * FROM alliance_rights ORDER BY right_desc;");
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
               	alliance_rankrights,
                alliance_ranks
            WHERE
                alliance_ranks.rank_id=alliance_rankrights.rr_rank_id
                AND alliance_ranks.rank_alliance_id=".$cu->allianceId."
                AND alliance_rankrights.rr_right_id=".$rightarr['right_id']."
                AND alliance_rankrights.rr_rank_id=".$myRankId.";");
						
						if (mysql_num_rows($check_res)>0)
							$myRight[$rightarr['right_key']]=true;
						else
							$myRight[$rightarr['right_key']]=false;
					}
				}

				// Gründer prüfen
				if ($arr['alliance_founder_id']==$cu->id)
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
				// Wings verwalten
				//
				elseif (isset($_GET['action']) && $_GET['action']=="wings")
				{
					if (Alliance::checkActionRights('wings'))
					{
						require("alliance/wings.inc.php");
					}
				}

				//
				// Allianz verlassen (Durchführen)
				//
				elseif (isset($_GET['action']) && $_GET['action']=="leave" && !$isFounder)
				{
					echo "<h2>Allianz-Austritt</h2>";
					if ($cu->allianceId!=0)
					{
						$ally->kickMember($cu->id,0);

						
						ok_msg("Du bist aus der Allianz ausgetreten!");
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
						

						//add_log(5,"Der Spieler [b]".$cu->nick."[/b] ist aus der Allianz [b][".$allys[$cu->allianceId]['tag']."] ".$allys[$cu->allianceId]['name']."[/b] ausgetreten!",time());

					}
					else
						echo "Du bist in keiner Allianz!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
				}

				//
				// Allianzbasis
				//
				elseif (isset($_GET['action']) && $_GET['action']=="base")
				{
					require("alliance/base.inc.php");
				}

				//
				// Allianz-Hauptseite anzeigen
				//
				else
				{
					// Änderungen übernehmen
					if (isset($_POST['editsubmit']) && checker_verify())
					{
						// Prüft Korrektheit des Allianztags und Namen, wenn diese geändert haben
						$check=false;
						if($_POST['alliance_tag']!=$arr['alliance_tag'] || $_POST['alliance_name']!=$arr['alliance_name'])
						{
							// Prüfen, ob der Allianzname bzw. Tag nicht nur aus Leerschlägen besteht
							$check_tag = str_replace(' ','',$_POST['alliance_tag']);
							$check_name = str_replace(' ','',$_POST['alliance_name']);
							
							if($check_name!='' && $check_tag!='')
							{
								$check_tag = check_illegal_signs($_POST['alliance_tag']);
								$check_name = check_illegal_signs($_POST['alliance_name']);
								$signs = check_illegal_signs("gibt eine liste von unerlaubten zeichen aus! ; < > & etc.");
								if($check_tag=="" && $check_name=="")
								{
									// Prüft, ob dieser Tag oder Name bereits vorhanden ist
									$check_res = dbquery("
									SELECT 
										COUNT(*)
									FROM 
										alliances
									WHERE 
										alliance_tag='".$_POST['alliance_tag']."'
										OR alliance_name='".$_POST['alliance_name']."'
									;");
									// Name / Tag sind bereits vergeben
									if(mysql_result($check_res,0)>0)
									{										
										echo "Fehler! Der gewünschte Tag oder Name ist bereits vergeben!<br>";
									}
									// Name / Tag sind noch nicht vergeben
									else
									{
										$check = true;
									}
								}
								else
								{
									echo "Fehler! Unerlaubte Zeichen (".$signs.") im Allianztag oder im Allianznamen!<br>";
								}
							}
							else
							{
								echo "Fehler! Der Allianzname und Allianztag dürfen nicht nur aus Leerzeichen besttehen!<br>";
							}
						}
						
						// Name und/oder Tag wird übernommen
						if($check)
						{
							$alliance_tag = $_POST['alliance_tag'];
							$alliance_name = $_POST['alliance_name'];
							
							add_alliance_history($cu->allianceId,"[b]".$cu->nick."[/b] ändert den Allianzname und/oder Tag von [b]".$arr['alliance_name']." (".$arr['alliance_tag'].")[/b] in [b]".$_POST['alliance_name']." (".$_POST['alliance_tag'].")[/b]!");
						}
						// Name und/oder Tag sind fehlerhaft
						else
						{
							$alliance_tag = $arr['alliance_tag'];
							$alliance_name = $arr['alliance_name'];
						}
						
						
						// Prüft Korrektheit des Allianzbildes
            $alliance_img_string="";
            if (isset($_POST['alliance_img_del']) && $_POST['alliance_img_del']==1)
            {
              if (file_exists(ALLIANCE_IMG_DIR."/".$arr['alliance_img']))
              {
                  @unlink(ALLIANCE_IMG_DIR."/".$arr['alliance_img']);
              }
              $alliance_img_string="alliance_img='',
              lliance_img_check=0,";
            }
            elseif (isset($_FILES['alliance_img_file']['tmp_name']) && $_FILES['alliance_img_file']['tmp_name']!="")
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
                      $fname = "alliance_".$cu->allianceId."_".time().".".$ext;
                      if (file_exists(ALLIANCE_IMG_DIR."/".$arr['user_avatar']))
                          @unlink(ALLIANCE_IMG_DIR."/".$arr['user_avatar']);
                      move_uploaded_file($source,ALLIANCE_IMG_DIR."/".$fname);
	                    if ($ims[0]>ALLIANCE_IMG_WIDTH || $ims[1]>ALLIANCE_IMG_HEIGHT)
											{
												if (resizeImage(ALLIANCE_IMG_DIR."/".$fname,ALLIANCE_IMG_DIR."/".$fname,ALLIANCE_IMG_WIDTH,ALLIANCE_IMG_HEIGHT,$ext))
												{
													echo "Bildgrösse wurde angepasst! ";
                        	echo "Allianzbild gespeichert!<br/>";
                        	$alliance_img_string="alliance_img='".$fname."',
                        	alliance_img_check=1,";
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
                      	$alliance_img_string="alliance_img='".$fname."',
                      	alliance_img_check=1,";
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

						if(!isset($message))
						{
							$message = "";	
						}
						
						dbquery("
						UPDATE 
							alliances 
						SET 
							alliance_tag='".addslashes($alliance_tag)."', 
							alliance_name='".addslashes($alliance_name)."', 
							alliance_text='".addslashes($_POST['alliance_text'])."',
						 	".$alliance_img_string."
							alliance_url='".$_POST['alliance_url']."',
							alliance_accept_applications='".$_POST['alliance_accept_applications']."',
							alliance_accept_bnd='".$_POST['alliance_accept_bnd']."',
							alliance_public_memberlist='".$_POST['alliance_public_memberlist']."'
						WHERE 
							alliance_id=".$cu->allianceId.";");
						$res = dbquery("SELECT * FROM alliances WHERE alliance_id='".$cu->allianceId."';");
						$arr = mysql_fetch_array($res);
						echo "Die &Auml;nderungen wurden übernommen!<br/>".$message."<br/>";
						
						// Hack
						$ally = new Alliance($cu->allianceId);
					}

					// Bewerbungsvorlage speichern
					if (isset($_POST['applicationtemplatesubmit']) && $_POST['applicationtemplatesubmit']!="" && checker_verify())
					{
						dbquery("UPDATE alliances SET alliance_application_template='".addslashes($_POST['alliance_application_template'])."' WHERE alliance_id=".$cu->allianceId.";");
						echo "Die &Auml;nderungen wurden übernommen!<br/><br/>";
					}

	        // Allianz auflösen
					if (isset($_POST['liquidatesubmit']) && $_POST['liquidatesubmit']!="" && $isFounder && $cu->allianceId==$_POST['id_control'] && checker_verify())
					{
						delete_alliance($arr['alliance_id'],true);
						$cu->setAllianceId(0);
						echo "Die Allianz wurde aufgel&ouml;st!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
					}
					// Allianzdaten anzeigen
					else
					{
						
						tableStart("[".stripslashes($arr['alliance_tag'])."] ".stripslashes($arr['alliance_name']));
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
								allianceboard_catranks
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
						echo "<tr><th>Internes Forum</th><td class=\"tbldata\" colspan=\"2\"><b><a href=\"?page=allianceboard\">Forum&uuml;bersicht</a></b> &nbsp; $ps</td></tr>";

						// Umfrage verlinken
						$pres=dbquery("SELECT poll_title,poll_question,poll_id FROM alliance_polls WHERE poll_alliance_id=".$arr['alliance_id']." ORDER BY poll_timestamp DESC LIMIT 2;");
						$pcnt=mysql_num_rows($pres);
						if ($pcnt>0)
						{
							$parr=mysql_fetch_array($pres);
							echo "<tr><th>Umfrage:</th>
							<td class=\"tbldata\" colspan=\"2\"><a href=\"?page=$page&amp;action=viewpoll\"><b>".stripslashes($parr['poll_title']).":</b> ".stripslashes($parr['poll_question'])."</a>";
							if ($pcnt>1)
								echo " &nbsp; (<a href=\"?page=$page&amp;action=viewpoll\">mehr Umfragen</a>)";
							echo "</td></tr>";
						}

						// Bewerbungen anzeigen
						if ($isFounder || $myRight['applications'])
						{
							$ares = dbquery("
							SELECT
								COUNT(user_id)
							FROM
								alliance_applications
							WHERE
								alliance_id=".$cu->allianceId."	
							;");							
							$aarr= mysql_fetch_row($ares);
							if ($aarr[0]>0)
							{
								echo "<tr><th colspan=\"3\" align=\"center\">
								<div align=\"center\"><b><a href=\"?page=$page&action=applications\">Es sind Bewerbungen vorhanden!</a></b></div>
								</th></tr>";
							}
						}

						// Wing-Anfrage
						if (($isFounder || $myRight['wings']) && $ally->motherRequestId > 0)
						{
							echo "<tr><th colspan=\"3\" align=\"center\">
							<div align=\"center\"><b><a href=\"?page=$page&action=wings\">Es ist eine Wing-Anfrage vorhanden!</a></b></div>
							</th></tr>";
						}

						if ($ally->motherId != 0)
						{
							echo "<tr>
											<th colspan=\"3\" style=\"text-align:center;\">
												Diese Allianz ist ein Wing von <b><a href=\"?page=$page&amp;action=info&amp;id=".$ally->motherId."\">".$ally->mother."</a></b>
											</th>
										</tr>";				
						}


						// Bündnissanfragen anzeigen
						if ($isFounder || $myRight['relations'])
						{
							$bres = dbquery("
							SELECT 
								alliance_bnd_id 
							FROM 
								alliance_bnd 
							WHERE 
								alliance_bnd_alliance_id2='".$cu->allianceId."' 
								AND alliance_bnd_level='0';");
							if (mysql_num_rows($bres)>0)
								echo "<tr>
									<th colspan=\"3\" style=\"text-align:center;color:#0f0\">
										<a  style=\"color:#0f0\" href=\"?page=$page&action=relations\">Es sind B&uuml;ndnisanfragen vorhanden!</a>
								</th></tr>";
						}

						// Kriegserklärung anzeigen
						$time=time()-192600;
						if (mysql_num_rows(dbquery("SELECT alliance_bnd_id FROM alliance_bnd WHERE alliance_bnd_alliance_id2='".$cu->allianceId."' AND alliance_bnd_level='3' AND alliance_bnd_date>'$time';"))>0)
						if ($isFounder || $myRight['relations'])
							echo "<tr>
								<th colspan=\"3\" align=\"center\"><b>
									<div align=\"center\"><a href=\"?page=$page&action=relations\">Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</a></div></b></th></tr>";
						else
							echo "<tr><th colspan=\"3\" align=\"center\"><div align=\"center\"><b>Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</b></div></th></tr>";

						// Verwaltung
						$adminBox=array();


						if ($isFounder || $myRight['viewmembers']) array_push($adminBox,"<a href=\"?page=$page&amp;action=viewmembers\">Mitglieder anzeigen</a>");
						if ($isFounder || $myRight['editmembers']) array_push($adminBox,"<a href=\"?page=$page&action=editmembers\">Mitglieder verwalten</a>");
						if ($isFounder || $myRight['editdata']) array_push($adminBox,"<a href=\"?page=$page&amp;action=editdata\">Allianz-Daten</a>");
						if ($isFounder || $myRight['wings']) array_push($adminBox,"<a href=\"?page=$page&action=wings\">Wings verwalten</a>");
						if ($isFounder || $myRight['applicationtemplate']) array_push($adminBox,"<a href=\"?page=$page&action=applicationtemplate\">Bewerbungsvorlage</a>");
						if ($isFounder || $myRight['history']) array_push($adminBox,"<a href=\"?page=$page&action=history\">Geschichte</a>");
						if ($isFounder || $myRight['massmail']) array_push($adminBox,"<a href=\"?page=$page&action=massmail\">Rundmail</a>");
						if ($isFounder || $myRight['ranks']) array_push($adminBox,"<a href=\"?page=$page&action=ranks\">R&auml;nge</a>");
						if ($isFounder || $myRight['alliancenews']) array_push($adminBox,"<a href=\"?page=$page&action=alliancenews\">Allianznews (Rathaus)</a>");
						if ($isFounder || $myRight['relations']) array_push($adminBox,"<a href=\"?page=$page&action=relations\">Diplomatie</a>");
						if ($isFounder || $myRight['polls']) array_push($adminBox,"<a href=\"?page=$page&action=polls\">Umfragen verwalten</a>");
						if ($isFounder || $myRight['liquidate']) array_push($adminBox,"<a href=\"?page=$page&action=liquidate\">Allianz aufl&ouml;sen</a>");
						
						array_push($adminBox,"<a href=\"?page=$page&action=base\">Allianzbasis</a>");

						if (!$isFounder) array_push($adminBox,"<a href=\"?page=$page&action=leave\" onclick=\"return confirm('Allianz wirklich verlassen?');\">Allianz verlassen</a>");

						$cnt=count($adminBox);
						if ($cnt>0)
						{
							echo"<tr><th width=\"120\" rowspan=\"".(ceil($cnt/2)+1)."\">Verwaltung:</th>";
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


						// Letzte Ereignisse anzeigen
						if ($isFounder || $myRight['history'])
						{
							echo "<tr>
								<th width=\"120\">Letzte Ereignisse:</th>
								<td class=\"tbldata\" colspan=\"2\">";
							$hres=dbquery("
							SELECT 
								* 
							FROM 
								alliance_history 
							WHERE 
								history_alliance_id=".$cu->allianceId." 
							ORDER BY 
								history_timestamp DESC
							LIMIT 5;");
							if(mysql_num_rows($hres)>0)
							{
								while ($harr=mysql_fetch_array($hres))
								{
									echo "<div class=\"infoLog\">".text2html($harr['history_text'])." <span>".df($harr['history_timestamp'])."</span></div>";
								}
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
					 		(alliance_bnd_alliance_id1='".$cu->allianceId."' 
					 		OR alliance_bnd_alliance_id2='".$cu->allianceId."') 
					 		AND alliance_bnd_level=3
					 	;");
						if (mysql_num_rows($wars)>0)
						{
							
							echo "<tr>
											<th>Kriege:</th>
											<td class=\"tbldata\" colspan=\"2\">
												<table class=\"tbl\">
													<tr>
														<th style=\"width:50%;\">Allianz</th>
														<th style=\"width:25%;\">Von</th>
														<th style=\"width:25%;\">Bis</th>
													</tr>";
									while ($war=mysql_fetch_array($wars))
									{
										if (isset($war['a1id']) && $war['a1id']==$cu->allianceId) 
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
					 		(alliance_bnd_alliance_id1='".$cu->allianceId."' 
					 		OR alliance_bnd_alliance_id2='".$cu->allianceId."') 
					 		AND alliance_bnd_level=4
					 	;");
						if (mysql_num_rows($wars)>0)
						{			
							echo "<tr>
											<th>Friedensabkommen:</th>
											<td class=\"tbldata\" colspan=\"2\">
												<table class=\"tbl\">
													<tr>
														<th style=\"width:50%;\">Allianz</th>
														<th style=\"width:25%;\">Von</th>
														<th style=\"width:25%;\">Bis</th>
													</tr>";					
									while ($war=mysql_fetch_array($wars))
									{
										if (isset($war['a1id']) && $war['a1id']==$cu->allianceId) 
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
					 		(alliance_bnd_alliance_id1='".$cu->allianceId."' 
					 		OR alliance_bnd_alliance_id2='".$cu->allianceId."') 
					 		AND alliance_bnd_level=2
					 	;");
						if (mysql_num_rows($wars)>0)
						{				
							echo "<tr>
											<th>Bündnisse:</th>
											<td class=\"tbldata\" colspan=\"2\">
												<table class=\"tbl\">
													<tr>
														<th style=\"width:50%;\">Allianz</th>
														<th style=\"width:25%;\">Von</th>
														<th style=\"width:25%;\">Bündnisname</th>
													</tr>";		
			
									while ($war=mysql_fetch_array($wars))
									{
										if (isset($war['a1id']) && $war['a1id']==$cu->allianceId) 
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
							echo "<tr><th width=\"120\">Besucherzähler:</th><td class=\"tbldata\" colspan=\"2\">".nf($arr['alliance_visits_ext'])." Besucher</td></tr>\n";
						}
						
						// Wings
						$wings = $ally->getWings();
						if (count($wings) > 0)
						{
							echo "<tr><th width=\"120\">Wings:</th><td class=\"tbldata\" colspan=\"2\">";
							echo "<table class=\"tb\">";
							echo "<tr>
								<th>Name</th>
								<th>Punkte</th>
								<th>Mitglieder</th>
								<th>Punkteschnitt</th>
							</tr>";
							foreach ($wings as $wid => $wdata)
							{
								echo "<tr>
								<td><a href=\"?page=alliance&amp;id=".$wid."\">".$wdata."</a></td>
								<td>".nf($wdata->points)."</td>
								<td>".$wdata->memberCount."</td>
								<td>".nf($wdata->avgPoints)."</td>
								</tr>";
							}
							echo "</td></tr>";
							tableEnd();
							echo "</td></tr>";
						}						
						

						// Website
						if ($arr['alliance_url']!="")
						{
							echo "<tr><th width=\"120\">Website/Forum:</th><td class=\"tbldata\" colspan=\"2\"><b>".
							format_link($arr['alliance_url'])."</a></b></td></tr>\n";
						}
						
						// Diverses
						echo "<tr><th width=\"120\">Mitglieder:</th>
						<td class=\"tbldata\" colspan=\"2\">".$ally->memberCount."</td></tr>\n";
						// Punkte
						echo "<tr>
										<th>Punkte / Schnitt:</th>
										<td colspan=\"2\">";
										echo nf($ally->points)." / ".nf($ally->avgPoints)."";
										echo "</td>
									</tr>";
						echo "<tr><th width=\"120\">Gr&uuml;nder:</th>
						<td class=\"tbldata\" colspan=\"2\">
							<a href=\"?page=userinfo&amp;id=".$ally->founderId."\">".$ally->founder."</a></td></tr>";
						// Gründung
						echo "<tr>
										<th>Gründungsdatum:</th>
										<td class=\"tbldata\" colspan=\"2\">
											".df($ally->foundationDate)." (vor ".tf(time() - $ally->foundationDate).")
										</td>
									</tr>";								
						echo "\n</table><br/>";
					}
				}
			}
			else
			{
				if ($_POST['resolvefalseallyid']!="")
				{
					dbquery("
					UPDATE 
						users 
					SET 
						user_alliance_id=0,
						user_alliance_rank_id=0 
					WHERE 
						user_id=".$cu->id.";");
					echo "Die fehlerhafte Verkn&uuml;pfung wurde gel&ouml;st!";
				}
				else
					echo "<form action=\"?page=$page\" method=\"post\">Diese Allianz existiert nicht!<br/><br/>
					<input type=\"submit\" name=\"resolvefalseallyid\" value=\"Fehlerhafte Allianzverkn&uuml;pfung l&ouml;schen\" /></form>";
			}
		
	}
?>
