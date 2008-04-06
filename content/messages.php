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
	// 	File: messages.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Ingame-Messaging centre
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	
	
	// DEFINITIONEN //


	$msgpreview = $cu->msg_preview==1 ? true : false;
	$msgcreatpreview = $cu->msgcreation_preview==1 ? true : false;


	// BEGIN SKRIPT //
	
	// Modus setzen
	$mode = isset($_GET['mode']) && ($_GET['mode']!="") ? $_GET['mode'] : 'inbox';


	?>
	<script type="text/javascript">
	function selectNewMessages()
	{
		//max = var document.getElementById("msg_cnt").value;
		
		if (document.getElementById("select_new_messages").innerHTML=="Nur neue Nachrichten anzeigen")
		{			
			document.getElementById("select_new_messages").innerHTML="Alle Nachrichten anzeigen";
			
			// Geht jede einzelne Nachricht durch
			for (x=0;x<=document.getElementById("msg_cnt").value;x++)
			{
					document.getElementById('msg_id_'+x).style.display='none';
			}
			
		}
		else
		{
						document.getElementById("select_new_messages").innerHTML="Nur neue Nachrichten anzeigen";
						
			// Geht jede einzelne Nachricht durch
			for (x=0;x<=document.getElementById("msg_cnt").value;x++)
			{
					document.getElementById('msg_id_'+x).style.display='';
			}
		}		
	}
	
	</script>
	<?


	// Einstellungs-Schalter oben rechts
	if (isset($_GET['msgprev']))
	{
		if ($_GET['msgprev']=='on')
		{
			dbquery("UPDATE ".$db_table['users']." SET user_msg_preview=1 WHERE user_id=".$cu->id().";");
			$msgpreview=true;
			$cu->msg_preview=1;
		}
		else
		{
			dbquery("UPDATE ".$db_table['users']." SET user_msg_preview=0 WHERE user_id=".$cu->id().";");
			$msgpreview=false;
			$cu->msg_preview=0;
		}
	}
	if (isset($_GET['msgcreatprev']))
	{
		if ($_GET['msgcreatprev']=='on')
		{
			dbquery("UPDATE ".$db_table['users']." SET user_msgcreation_preview=1 WHERE user_id=".$cu->id().";");
			$msgcreatpreview=true;
			$cu->msgcreation_preview=1;
		}
		else
		{
			dbquery("UPDATE ".$db_table['users']." SET user_msgcreation_preview=0 WHERE user_id=".$cu->id().";");
			$msgcreatpreview=false;
			$cu->msgcreation_preview=0;
		}
	}	
	echo '<div style="float:right;font-size:8pt;text-align:right;">';
	if ($mode=='archiv' || $mode=='inbox')
	{
		if ($msgpreview)
		{
			echo '<a href="?page='.$page.'&amp;mode='.$mode.'&amp;msgprev=off">Textvorschau ausschalten</a>';
		}
		else
		{
			echo '<a href="?page='.$page.'&amp;mode='.$mode.'&amp;msgprev=on">Textvorschau einschalten</a>';
		}
	}
	if ($mode=='new')
	{
		if ($msgcreatpreview)
		{
			echo '<a href="?page='.$page.'&amp;mode='.$mode.'&amp;msgcreatprev=off">Vorschau ausschalten</a>';
		}
		else
		{
			echo '<a href="?page='.$page.'&amp;mode='.$mode.'&amp;msgcreatprev=on">Vorschau einschalten</a>';
		}	
		echo '<br/><a href="?page=userconfig&mode=messages">Signatur bearbeiten</a>';
	}
	echo '</div>';

	
	
	echo '<h1>Nachrichten</h1>';
	echo '<br style="clear:both;" />';

	// Menü

	show_tab_menu("mode",array(
	"inbox"=>"Posteingang",
	"new"=>"Erstellen",
	"archiv"=>"Archiv",
	"sent"=>"Gesendet",
	"deleted"=>"Papierkorb",
	"ignore"=>"Ignorierliste"
	));
	echo "<br/>";

	//
	// Neue Nachricht
	//
	if ($mode=="new")
	{
		require('content/messages/new.php');		
	}

	//
	// Ignorierliste
	//
	elseif ($mode=="ignore")
	{
		require('content/messages/ignore.php');		
	}
			
	//
	// Gelöschte Nachrichten
	//
	elseif ($mode=="deleted")
	{
		require('content/messages/deleted.php');		
	}
	
	//
	// Gesendete Nachrichten
	//
	elseif ($mode=="sent")
	{
		require('content/messages/sent.php');		
	}

/***********************
* Nachricht betrachten *
***********************/
		else
		{
			//
			// Einzelne Nachricht
			//
			if (isset($_GET['msg_id']) && intval($_GET['msg_id'])>0)
			{
				$mres = dbquery("
				SELECT
          m.message_subject,
          m.message_timestamp,
          m.message_user_from,
          m.message_text,
          m.message_read,
          c.cat_sender,
          user_nick
				FROM
          ".$db_table['messages']." AS m
        INNER JOIN
        	".$db_table['message_cat']." AS c
        	ON c.cat_id=m.message_cat_id
        LEFT JOIN
        	".$db_table['users']."
        	ON message_user_from=user_id
				WHERE                    
       		message_id='".intval($_GET['msg_id'])."'
       		AND m.message_user_to='".$cu->id()."'
       		AND m.message_deleted=0");
				if (mysql_num_rows($mres)>0)
				{
					//echo "<form action=\"?page=$page&mode=".$mode."\" method=\"post\">";
					//checker_init();
					$marr = mysql_fetch_array($mres);
					// Sender
					$sender = $marr['message_user_from']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>'.$marr['cat_sender'].'</i>';
					// Title
					$subj = $marr['message_subject']!="" ? stripslashes($marr['message_subject']) : "<i>Kein Titel</i>";
					
					echo "<table class=\"tbl\">";
					echo "<tr><td class=\"tbltitle\" colspan=\"2\">".$subj."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Datum:</td><td class=\"tbldata\" width=\"250\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Sender:</td><td class=\"tbldata\" width=\"250\">".$sender."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:<br/>";
					if (isset($_GET['src']))
					{
						echo '[<a href="?page='.$page.'&mode='.$mode.'&amp;msg_id='.$_GET['msg_id'].'">Nachricht</a>]';
					}
					else
					{
						echo '[<a href="?page='.$page.'&mode='.$mode.'&amp;msg_id='.$_GET['msg_id'].'&amp;src=1">Quelltext</a>]';
					}					
					echo "</td><td class=\"tbldata\" width=\"250\">";
					if ($marr['message_text']!="")
					{
						if (isset($_GET['src']))
						{
							echo '<textarea rows="30" cols="60" readonly="readonly">'.stripslashes($marr['message_text']).'</textarea>';
						}	
						else
						{
							echo text2html(stripslashes($marr['message_text']));
						}
					}
					else
					{
						echo "<i>Kein Text</i>";
					}
					echo "</td></tr>";
					echo "</table><br/>";
					
					if ($marr['message_read']==0)
					{
						dbquery("UPDATE ".$db_table['messages']." SET message_read=1 WHERE message_id='".intval($_GET['msg_id'])."';");
					}

					
					echo "<form action=\"?page=$page&mode=new\" method=\"post\">";
					checker_init();
					
					echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=".$mode."'\"/>&nbsp;";					
					echo "<input type=\"hidden\" name=\"message_id\" value=\"".intval($_GET['msg_id'])."\" />";
					echo "<input type=\"hidden\" name=\"message_subject\" value=\"".$marr['message_subject']."\" />";
					echo "<input type=\"hidden\" name=\"message_sender\" value=\"".$sender."\" />";
					if ($cu->msg_copy)
					{
						// Muss mit echo 'text'; erfolgen, da sonst der Text beim ersten " - Zeichen abgeschnitten wird!
						// Allerdings ist so das selbe Problem mit den ' - Zeichen!
						echo '<input type=\'hidden\' name=\'message_text\' value=\''.stripslashes($marr['message_text']).'\' />';
					}
					echo "<input type=\"submit\" value=\"Weiterleiten\" name=\"remit\" />&nbsp;";
					if ($marr['message_user_from']>0)
					{				
						echo "<input type=\"hidden\" name=\"message_user_to\" value=\"".$marr['message_user_from']."\" />";
						echo "<input type=\"submit\" value=\"Antworten\" name=\"answer\" />&nbsp;";
						echo "<input type=\"button\" value=\"Absender ignorieren\" onclick=\"document.location='?page=".$page."&amp;mode=ignore&amp;add=".$marr['message_user_from']."'\" />&nbsp;";
					}						
					echo "<input type=\"button\" value=\"L&ouml;schen\" onclick=\"document.location='?page=$page&mode=mode&del=".$_GET['msg_id']."';\" />&nbsp;";
					if ($marr['message_user_from']>0)
					{
						abuse_button("messages","Beleidigung melden",$marr['message_user_from']);
					}
					else
					{
						abuse_button("rules","Regelverstoss melden");
					}
					echo "</form>";
				}
				else
				{
					echo "<p align=\"center\" class=\"infomsg\">Diese Nachricht existiert nicht!</p>";
					echo "<p align=\"center\"><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=".$mode."'\"></p>";
				}
			}

			//
			// Übersicht
			//
			else
			{

				// Einzelne Nachricht löschen
				if (isset($_POST['submitdelete']) && checker_verify())
				{
					dbquery("
					UPDATE 
						".$db_table['messages']." 
					SET 
						message_deleted=1 
					WHERE 
						message_id='".$_POST['message_id']."' 
						AND message_user_to='".$cu->id()."';");
					success_msg("Nachricht wurde gel&ouml;scht!");
				}
				if (isset($_GET['del']) && $_GET['del']>0)
				{
					dbquery("
					UPDATE 
						".$db_table['messages']." 
					SET 
						message_deleted=1 
					WHERE 
						message_id='".$_GET['del']."' 
						AND message_user_to='".$cu->id()."';");
					if (mysql_affected_rows()>0)
					{
						success_msg("Nachricht wurde gel&ouml;scht!");
					}
					else
					{
						error_msg("Nachricht konnte nicht gelöscht werden!");
					}
				}
				
				
				// Selektiere löschen
				if (isset($_POST['submitdeleteselection'])  && checker_verify())
				{
					if($mode=="archiv")
					{
						$sqladd = " AND message_archived=1";
					}
					else
					{
						$sqladd = " AND message_archived=0";
					}
					
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
								AND message_user_to='".$cu->id()."'
								$sqladd;");
						}
						if (count($_POST['delmsg'])==1)
						{
							success_msg("Nachricht wurde gelöscht!");
						}
						else
						{
							success_msg("Nachrichten wurden gelöscht!");
						}
					}
				}
				
				// Alle Nachrichten löschen
				elseif (isset($_POST['submitdeleteall']) && checker_verify())
				{
					if($mode=="archiv")
						$sqladd = " AND message_archived=1";
					else
						$sqladd = " AND message_archived=0";
					dbquery("
					UPDATE
						".$db_table['messages']."
					SET
						message_deleted=1
					WHERE
						message_user_to='".$cu->id()."'
						$sqladd;");
					success_msg("Alle Nachrichten wurden gel&ouml;scht!");
				}
				
				// Systemnachrichten löschen
				elseif (isset($_POST['submitdeletesys']) && checker_verify())
				{
					if($mode=="archiv")
						$sqladd = " AND message_archived=1";
					else
						$sqladd = " AND message_archived=0";

					dbquery("
					UPDATE
						".$db_table['messages']."
					SET
						message_deleted=1
					WHERE
         		message_user_to='".$cu->id()."'
         		AND message_user_from=0
						$sqladd;");
					success_msg("Alle Systemnachrichten wurden gel&ouml;scht!");
				}
				elseif (isset($_POST['submitarchiving'])  && checker_verify())
				{
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
	                    AND message_user_to='".$cu->id()."'
	                    ;");
	            }
	            if (count($_POST['delmsg'])==1)
	                success_msg("Nachricht wurde archiviert!");
	            else
	                success_msg("Nachrichten wurden archiviert!");
            }
            else
            {
            	echo "Zu wenig Platz im Archiv!<br/><br/>";
            }
					}
				}


				//Zählt gelesene Nachrichten
				$cnt_res = dbquery("
				SELECT
					COUNT(message_id)
				FROM
					".$db_table['messages']."
				WHERE
          message_user_to='".$cu->id()."'
          AND message_read='1'
          AND message_deleted='0'
          AND message_archived='0'
        ;");
				$readed_msg_cnt_arr = mysql_fetch_row($cnt_res);
				$readed_msg_cnt = $readed_msg_cnt_arr[0];

				//Zählt archivierte Nachrichten
				$cnt_res = dbquery("
				SELECT
					COUNT(message_id)
				FROM
					".$db_table['messages']."
				WHERE
          message_user_to='".$cu->id()."'
          AND message_archived='1'
          AND message_deleted='0';");
				$archived_msg_cnt_arr=mysql_fetch_row($cnt_res);
				$archived_msg_cnt=$archived_msg_cnt_arr[0];


				// Rechnet %-Werte für tabelle (1/2)
				$readed_table=min(ceil($readed_msg_cnt/$conf['msg_max_store']['v']*100),100);
				$archived_table=min(ceil($archived_msg_cnt/$conf['msg_max_store']['p1']*100),100);
					
				$r_color = ($readed_table>=90) ? 'color:red;' : '';
				$a_color = ($archived_table>=90) ? 'color:red;' : '';

					
				// Archiv-Grafik
				echo "<table class=\"tbl\">";
				echo "<tr>
					<th class=\"tbltitle\" style=\"text-align:center;width:50%;".$r_color."\">
          	Gelesen: ".$readed_msg_cnt."/".$conf['msg_max_store']['v']." Nachrichten
          </th>
        	<th class=\"tbltitle\" style=\"text-align:center;width:50%;".$a_color."\">
          	Archiviert: ".$archived_msg_cnt."/".$conf['msg_max_store']['p1']." Nachrichten
        	</th>
        </tr>";
				echo '<tr>
  	     	<td class="tbldata" style="padding:0px;height:10px;"><img src="images/poll3.jpg" style="height:10px;width:'.$readed_table.'%;" alt="poll" /></td>
	        <td class="tbldata" style="padding:0px;height:10px;"><img src="images/poll2.jpg" style="height:10px;width:'.$archived_table.'%;" alt="poll" /></td>                  
        </tr>';    
        
        // Wenn es neue Nachrichten hat, Button zum Selektieren anzeigen
        if(NEW_MESSAGES>0)
        {
        	echo '<tr>
  	     					<td class="tbldata" style="text-align:center;" colspan="2">
  	     					<a href="javascript:;" onclick="selectNewMessages();" id="select_new_messages" name="select_new_messages">Nur neue Nachrichten anzeigen</a>
  	     					</td>                 
        	</tr>';
        }
                                                                       
				echo "</table><br/>";

				echo "<form action=\"?page=$page&amp;mode=".$mode."\" method=\"post\"><div>";
				$cstr = checker_init();
				echo "<input type=\"hidden\" name=\"archived_msg_cnt\" value=\"".$archived_msg_cnt."\" />";
				
				// Nachrichten
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
				$rcnt=0;
				while ($arr = mysql_fetch_array($res))
				{
					if($mode=="archiv")
					{
						$mres = dbquery("
						SELECT
							message_subject,
							message_text,
							message_id,
							message_timestamp,
							message_user_from,
							message_read,
							message_massmail,
							message_replied,
							message_forwarded,							
							user_nick							
						FROM
							".$db_table['messages']."
						LEFT JOIN
							".$db_table['users']."
							ON message_user_from=user_id									
						WHERE
							message_user_to='".$cu->id()."'
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
							message_massmail,
							message_read,
							message_replied,
							message_forwarded,
							user_nick							
						FROM
							".$db_table['messages']."
						LEFT JOIN
							".$db_table['users']."
							ON message_user_from=user_id														
						WHERE
							message_user_to='".$cu->id()."'
							AND message_cat_id='".$arr['cat_id']."'
							AND message_deleted=0
							AND message_archived=0
						ORDER BY
							message_read ASC,
							message_timestamp DESC;");
					}
					$ccnt=mysql_num_rows($mres);
					
					// Kategorie-Titel
					if ($ccnt>0)
					{
						echo "<tr>
							<td class=\"tbltitle\" colspan=\"4\">".text2html($arr['cat_name'])." (".$ccnt." Nachrichten)</td>
							<td class=\"tbltitle\" style=\"text-align:center;\"><input type=\"button\" id=\"selectBtn[".$arr['cat_id']."]\" value=\"X\" onclick=\"xajax_messagesSelectAllInCategory(".$arr['cat_id'].",".$ccnt.",this.value)\"/></td>
						</tr>";
					}
					else
					{
						echo "<tr>
							<td class=\"tbltitle\" colspan=\"5\">".text2html($arr['cat_name'])."</td>
						</tr>";
					}
					if ($ccnt>0)
					{
						$dcnt=0;
						while ($marr = mysql_fetch_array($mres))
						{							
							// Sender
							$sender = $marr['message_user_from']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>'.$arr['cat_sender'].'</i>';
							
							// Title
							$subj = $marr['message_subject']!="" ? stripslashes($marr['message_subject']) : "<i>Kein Titel</i>";
							
							// Read or not read
							if ($marr['message_read']==0)
							{
								$im_path = "images/pm_new.gif";
								$subj = '<strong>'.$subj.'</strong>';
								$sender_f = '<strong>'.$sender.'</strong>';
							}
							else
							{
								$im_path = "images/pm_normal.gif";
								$sender_f = $sender;
							}

							if ($marr['message_read']==1)
							{
	            	echo "<tr id=\"msg_id_".$rcnt."\" style=\"display:;\">";
	            	$rcnt++;
	            }
	            else
	            {
	            	echo "<tr style=\"display:;\">";
	            }
	            
	            
	            echo "				<td class=\"tbldata\" style=\"width:2%;\">
	            					<img src=\"".$im_path."\" alt=\"Mail\" id=\"msgimg".$marr['message_id']."\" />
	            				</td>
	            			<td class=\"tbldata\" style=\"width:66%;\">";
							if ($marr['message_massmail']==1)
							{
								echo "<b>[Rundmail]</b> ";
							}								
							//Wenn Speicher voll ist Nachrichten Markieren
							if($mode!="archiv" && $readed_msg_cnt>=$conf['msg_max_store']['v'])
							{
								echo "<span style=\"color:red;\" ";
                if ($msgpreview)
                {
                    echo tm($subj,text2html(substr($marr['message_text'], 0, 500)));
                }
                echo ">".$subj."</span>";
							}
							else
							{
                if ($msgpreview)
                {
									echo "<a href=\"javascript:;\" onclick=\"toggleBox('msgtext".$marr['message_id']."');xajax_messagesSetRead(".$marr['message_id'].")\" >".$subj."</a>";
                }
                else
                {
									echo "<a href=\"?page=$page&amp;msg_id=".$marr['message_id']."&amp;mode=".$mode."\">".$subj."</a>";
                }
              }
							echo "</td>";
							echo "<td class=\"tbldata\" style=\"width:15%;\">".$sender_f."</td>";
							echo "<td class=\"tbldata\" style=\"width:15%;\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
							echo "<td class=\"tbldata\" style=\"width:2%;text-align:center;padding:0px;vertical-align:middle;\">
							<input id=\"delcb_".$arr['cat_id']."_".$dcnt."\" type=\"checkbox\" name=\"delmsg[".$marr['message_id']."]\" value=\"1\" title=\"Nachricht zum L&ouml;schen markieren\" /></td>";
							echo "</tr>\n";
              if ($msgpreview)
              {
								echo "<tr style=\"display:none;\" id=\"msgtext".$marr['message_id']."\"><td colspan=\"5\" class=\"tbldata\">";
								echo text2html($marr['message_text']);
								echo "<br/><br/>";
								$msgadd = "&amp;message_text=".base64_encode($marr['message_text'])."&amp;message_sender=".base64_encode($sender);
								echo "<input type=\"button\" value=\"Weiterleiten\" onclick=\"document.location='?page=$page&mode=new&amp;message_subject=".base64_encode("Fw: ".$marr['message_subject'])."".$msgadd."'\" name=\"remit\" />&nbsp;";
								if ($marr['message_user_from']>0)
								{				
									if ($cu->msg_copy)
									{
										echo "<input type=\"button\" value=\"Antworten\" name=\"answer\" onclick=\"document.location='?page=$page&mode=new&message_user_to=".$marr['message_user_from']."&amp;message_subject=".base64_encode("Re: ".$marr['message_subject'])."".$msgadd."'\" />&nbsp;";
									}
									else
									{								
										echo "<input type=\"button\" value=\"Antworten\" name=\"answer\" onclick=\"document.location='?page=$page&mode=new&message_user_to=".$marr['message_user_from']."&amp;message_subject=".base64_encode("Re: ".$marr['message_subject'])."'\" />&nbsp;";
									}
									echo "<input type=\"button\" value=\"Absender ignorieren\" onclick=\"document.location='?page=".$page."&amp;mode=ignore&amp;add=".$marr['message_user_from']."'\" />&nbsp;";
								}						
								echo "<input type=\"button\" value=\"L&ouml;schen\" onclick=\"document.location='?page=$page&mode=mode&del=".$marr['message_id']."';\" />&nbsp;";
								if ($marr['message_user_from']>0)
								{
									abuse_button("messages","Beleidigung melden",$marr['message_user_from']);
								}
								else
								{
									abuse_button("rules","Regelverstoss melden");
								}
								echo "<br/>";
								echo "</td></tr>";
							}
							$dcnt++;
							$msgcnt++;
						}
					}
					else
					{
						echo "<tr>
							<td class=\"tbldata\" colspan=\"5\"><i>Keine Nachrichten vorhanden</i></td>
						</tr>";
					}
				}
				echo "</table><br/>";
				if ($msgcnt>0)
				{
					// Übergibt alle Nachrichten-ID's andie javascript funktion
					echo "<input type=\"hidden\" id=\"msg_cnt\" value=\"".$msgcnt."\" />";
					
					echo "<input type=\"submit\" name=\"submitdeleteselection\" value=\"Markierte l&ouml;schen\" />&nbsp;
					<input type=\"submit\" name=\"submitdeleteall\" value=\"Alle l&ouml;schen\" onclick=\"return confirm('Wirklich alle Nachrichten löschen?');\" />&nbsp;
					<input type=\"submit\" name=\"submitdeletesys\" value=\"Systemnachrichten l&ouml;schen\" />";
					if($mode!="archiv")
					{
						echo "&nbsp;<input type=\"submit\" name=\"submitarchiving\" value=\"Markierte archivieren\" />";
					}
				}
				echo "</div></form>";
			}
		}

?>

