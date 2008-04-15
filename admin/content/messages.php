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
	// 	Dateiname: messages.php
	// 	Topic: Nachrichtenverwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	define("USER_MESSAGE_CAT_ID",1);
	define("SYS_MESSAGE_CAT_ID",5);

	echo "<h1>Nachrichten</h1>";

	//
	// E-Mail-Queue
	//
	if ($sub=="queue")
	{
		echo "<h2>E-Mail-Warteschlange</h2>";
		if ($_POST['queue_release']!="")
		{
			mail_queue_send($_POST['limit']);
			echo "Warteschlange wurde abgearbeitet!<br/><br/>";
		}

		$res=dbquery("SELECT msg_id FROM mail_queue;");
		$cnt=mysql_num_rows($res);
		if ($cnt>0)
		{
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "Es sind ".$cnt." Nachrichten in der Warteschlange.<br/><br/>Manuellen Versand ausl&ouml;sen f&uuml;r <select name=\"limit\">";
			for ($x=min($cnt,10);$x<$cnt;$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "<option value=\"$cnt\">alle</option>";
			echo "</select> Nachrichten: <input type=\"submit\" name=\"queue_release\" value=\"Senden\" /></form>";
		}
		else
			echo "<i>Keine Nachrichten in der Warteschlange!</i>";
	}

	//
	// Nachrichten löschen
	//
	elseif ($sub=="delmsgs")
	{

}
	elseif ($sub=="sendmsg")
	{
		if ($_POST['submit']!="")
		{
			if ($_POST['message_subject']!="" && $_POST['message_text']!="")
			{
				//SYS_MESSAGE_CAT_ID
				Message::sendFromUserToUser(0,$_POST['message_user_to'],$_POST['message_subject'],$_POST['message_text']);
				cms_ok_msg("Nachricht wurde gesendet!");
				echo "<input type=\"button\" class=\"button\" value=\"Neue Nachricht schreiben\" onclick=\"document.location='?page=$page&sub=$sub'\">";
			}
			else
			{
				cms_err_msg("Nachricht konnte nicht gesendet werden! Text oder Titel fehlt!");
				echo "<input type=\"button\" class=\"button\" value=\"Zur&uuml;ck\" onclick=\"history.back();\">";
			}
		}
		else
		{
			echo "Nachricht an einen Spieler senden:<br/><br/>";
			echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">";
			echo "<table width=\"300\" class=\"tbl\">";
			echo "<tr><td width=\"50\" valign=\"top\">&nbsp;</td><td class=\"tbltitle\">Neue Nachricht</td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Empf&auml;nger:</td><td class=\"tbldata\" width=\"250\"><select name=\"message_user_to\">";
			$res=dbquery("SELECT user_id,user_nick FROM users ORDER BY user_nick;");
			while ($arr=mysql_fetch_array($res))
			{
				echo "<option value=\"".$arr['user_id']."\"";
				echo ">".$arr['user_nick']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Betreff:</td><td class=\"tbldata\" width=\"250\"><input type=\"text\" name=\"message_subject\" value=\"".$_GET['message_subject']."\" size=\"30\" maxlength=\"255\"></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:</td><td class=\"tbldata\" width=\"250\"><textarea name=\"message_text\" rows=\"10\" cols=\"40\"></textarea></td></tr>";
			echo "</table>";
			echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Senden\"></p>";
			echo "</form>";
		}
	}

	//
	// Ingame-Rundmail
	//
	elseif ($sub=="infomail")
	{
		if ($_POST['submit']!="")
		{
			$res = dbquery("SELECT user_id FROM users;");
			while ($arr=mysql_fetch_array($res))
			{
				dbquery("INSERT INTO messages (
				message_user_from,
				message_user_to,
				message_timestamp,
				message_cat_id,
				message_subject,
				message_text,
				message_massmail
				) VALUES (
				0,
				'".$arr['user_id']."',
				".time().",
				".SYS_MESSAGE_CAT_ID.",
				'".addslashes($_POST['message_subject'])."',
				'".addslashes($_POST['message_text'])."',
				1
				);");
			}
			cms_ok_msg("Nachricht wurde an ".mysql_num_rows($res)." Spieler gesendet!");
			echo "<p align=\"center\"><input type=\"button\" class=\"button\" value=\"Neue Nachricht schreiben\" onClick=\"document.location='?page=$page&sub=$sub'\"></p>";
		}
		else
		{
			echo "<h2>Rundmail an alle Spieler</h2>";
			echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">";
			echo "<table width=\"300\" class=\"tbl\">";
			echo "<tr><td width=\"50\" valign=\"top\">&nbsp;</td><td class=\"tbltitle\">Neue Nachricht</td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Betreff:</td><td class=\"tbldata\" width=\"250\"><input type=\"text\" name=\"message_subject\" value=\"".$_GET['message_subject']."\" size=\"30\" maxlength=\"255\"></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:</td><td class=\"tbldata\" width=\"250\"><textarea name=\"message_text\" rows=\"10\" cols=\"40\"></textarea></td></tr>";
			echo "</table>";
			echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Senden\"></p>";
			echo "</form>";
		}
	}
	elseif ($sub=="email")
	{
		$designs = get_designs("../");

		if ($_POST['submit']!="")
		{
			//Mail an alle User
			if($_POST['email_at_all']==1)
			{
                $email_adress="";
                $res = dbquery("SELECT user_email_fix,user_css_style FROM users;");
                $users=mysql_num_rows($res);
                $counter=$users;
                while ($arr=mysql_fetch_array($res))
                {

                    if($_POST['email_style']=="self")
                    {
                        $email_style=$arr['user_css_style'];
                    }
                    else
                    {
                        //formatiert string, denn sonst würde es beispielsweise '../css_style/Dark' ausgeben anstatt 'css_style/Dark'
                        $email_style=substr($_POST['email_style'],3);
                    }


                    //Sendet mail
                    send_mail(0,$arr['user_email_fix'],$_POST['email_subject'],$_POST['email_text'],$email_style,$_POST['email_align']);

                }

                add_log(8,$_SESSION[SESSION_NAME]['user_nick']." schickt eine E-mail an alle User",time());
                echo "E-mail an ".$users." User geschickt<br>";
			}
			//Mail an einen User
			else
			{
                if($_POST['email_user_to']!=0)
                {
                    $res = dbquery("SELECT user_email_fix FROM users WHERE user_id='".$_POST['email_user_to']."';");
                    $arr=mysql_fetch_array($res);
                    $email_adress=$arr['user_email_fix'];
                }
                else
                {
                    $email_adress=$_POST['email_user_to_self'];
                }

                if($_POST['email_style']=="self")
                {
                    $res = dbquery("SELECT user_css_style FROM users WHERE user_id='".$_POST['email_user_to']."';");
                    $arr=mysql_fetch_array($res);
                    $email_style=$arr['user_css_style'];
                }
                else
                {
                    //formatiert string, denn sonst würde es beispielsweise '../css_style/Dark' ausgeben anstatt 'css_style/Dark'
                    //$email_style=substr($_POST['email_style'],3);
                    $email_style=$_POST['email_style'];
                }

				send_mail(0,$email_adress,$_POST['email_subject'],$_POST['email_text'],$email_style,$_POST['email_align']);

				add_log(8,$_SESSION[SESSION_NAME]['user_nick']." schickt eine E-mail an die Adresse: ".$email_adress."",time());
				echo "E-mail erfolgreich verschickt!<br>";
			}
		}

		if ($_POST['submit_preview']!="")
		{

			if($_POST['email_user_to']!=0 || $_POST['email_user_to_self']!="" || $_POST['email_at_all']=='1')
			{
				if($_POST['email_subject']!="")
				{
					if($_POST['email_text']!="")
					{
						if($_POST['email_style']=="self" && $_POST['email_user_to']==0 && $_POST['email_at_all']!=1)
						{
							echo "Da du den Empf&auml;nger nicht aus der Liste ausgew&auml;hlt hast, musst du das Design selbst bestimmen!";
						}
						else
						{
                            if($_POST['email_user_to']!=0)
                            {
                                $res = dbquery("SELECT user_email_fix FROM users WHERE user_id='".$_POST['email_user_to']."';");
                                $arr=mysql_fetch_array($res);
                                $email_adress=$arr['user_email_fix'];
                            }
                            else
                            {
                                $email_adress=$_POST['email_user_to_self'];
                            }

                            if($_POST['email_style']=="self" && $_POST['email_user_to']!=0 && $_POST['email_at_all']!=1)
                            {
                                $res = dbquery("SELECT user_css_style FROM users WHERE user_id='".$_POST['email_user_to']."';");
                                $arr=mysql_fetch_array($res);
                                $email_style=$arr['user_css_style'];
                            }
                            elseif($_POST['email_style']=="self" && $_POST['email_at_all']==1)
                            {
                                $res = dbquery("SELECT user_css_style FROM users LIMIT 1;");
                                $arr=mysql_fetch_array($res);
                                $email_style=$arr['user_css_style'];
                            }
                            else
                            {
                                //formatiert string, denn sonst würde es beispielsweise '../css_style/Dark' ausgeben anstatt 'css_style/Dark'
                                //$email_style=substr($_POST['email_style'],3);
                                $email_style=$_POST['email_style'];
                            }


                            //Erstellt mail-vorschau
                            $email_text=nl2br(text2html($_POST['email_text']));
                            send_mail(1,$email_adress,$_POST['email_subject'],$email_text,$email_style,$_POST['email_align']);
                            echo "Bist du mit der Vorschau zufieden, klicke auf \"Senden\" und die E-mail wird versendet!<br><br>";

                            if($_POST['email_at_all']==1)
                                echo "<b>Achtung: Diese Mail wird an alle User dieser Runde geschickt!</b><br><br>";


                            //Zeigt Forumal mit den werten wieder
                            echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">";
                            echo "<table class=\"tbl\">";
                            echo "<tr><td width=\"30%\" valign=\"top\">&nbsp;</td><td class=\"tbltitle\" width=\"70%\">Neue Nachricht</td></tr>";
                            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">E-Mail Adresse:</td><td class=\"tbldata\" width=\"70%\"><input type=\"text\" name=\"email_user_to_self\" value=\"".$_POST['email_user_to_self']."\" size=\"30\" maxlength=\"255\"> <select name=\"email_user_to\">";
                            echo "<option value=\"0\" selected=\"selected\">------</option>";
                            $res=dbquery("SELECT user_id,user_nick FROM ".$db_table['users']." ORDER BY user_nick;");
                            while ($arr=mysql_fetch_array($res))
                            {
                                echo "<option value=\"".$arr['user_id']."\"";
                                if ($_POST['email_user_to']==$arr['user_id']) echo " selected=\"selected\"";
                                echo ">".$arr['user_nick']."</option>";
                            }
                            echo "</select></td></tr>";
                            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Betreff:</td><td class=\"tbldata\" width=\"70%\"><input type=\"text\" name=\"email_subject\" value=\"".$_POST['email_subject']."\" size=\"30\" maxlength=\"255\"></td></tr>";
                            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Text:</td><td class=\"tbldata\" width=\"70%\"><textarea name=\"email_text\" rows=\"10\" cols=\"40\">".$_POST['email_text']."</textarea></td></tr>";

                            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Text-Ausrichtung:</td><td class=\"tbldata\" width=\"70%\"><select name=\"email_align\">";
                            if($_POST['email_align']=="center") $center="selected=\"selected\"";
                            if($_POST['email_align']=="left") $left="selected=\"selected\"";
                            if($_POST['email_align']=="right") $right="selected=\"selected\"";
                            if($_POST['email_align']=="justify") $justify="selected=\"selected\"";
                            echo "<option value=\"center\" $center>Zentriert</option>";
                            echo "<option value=\"left\" $left>Linksb&uuml;ndig</option>";
                            echo "<option value=\"right\" $right>Rechtsb&uuml;ndig</option>";
                            echo "<option value=\"justify\" $justify>Blocksatz</option>";
                            echo "</select></td></tr>";

                            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Design w&auml;hlen:</td><td class=\"tbldata\" width=\"70%\"><select name=\"email_style\">";
                            if($_POST['email_style']=="self") $self=" selected=\"selected\"";
                            echo "<option value=\"self\" $self>Design des Benutzers</option>";
                            foreach ($designs as $k => $v)
                            {
                                echo "<option value=\"$k\"";
                                if($_POST['email_style']==$k) echo " selected=\"selected\"";
                                echo ">".$v['name']."</option>";
                            }
                            echo "</select></td></tr>";
                            if($_POST['email_at_all']==1) $all="checked=\"checked\""; else $not_at_all="checked=\"checked\"";

                            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">An alle User schicken:</td><td class=\"tbldata\" width=\"70%\"><input type=\"radio\" name=\"email_at_all\" value=\"1\" $all/> Ja  <input type=\"radio\" name=\"email_at_all\" value=\"0\" $not_at_all/> Nein</td></tr>";

                            echo "</table>";
                            echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit_preview\" value=\"Vorschau\" ></p>";
                            echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Senden\" >";
                            echo "</form>";

                    	}

                    }
                    else
                    {
                    	echo "Leere E-mails sind nicht beliebt!<br>";
                    }
                }
                else
                {
                	echo "Die E-mail muss einen Betreff haben!<br>";
                }
            }
            else
            {
            	echo "Es muss ein User, bzw. eine E-mail Adresse angegeben werden!<br>";
            }
		}
		else
		{
			echo "E-Mail versenden:<br/><br/>";
			echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td width=\"30%\" valign=\"top\">&nbsp;</td><td class=\"tbltitle\" width=\"70%\">Neue Nachricht</td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">E-Mail Adresse:</td><td class=\"tbldata\" width=\"70%\"><input type=\"text\" name=\"email_user_to_self\" value=\"\" size=\"30\" maxlength=\"255\"> <select name=\"email_user_to\">";
			echo "<option value=\"0\" selected=\"selected\">------</option>";
			$res=dbquery("SELECT user_id,user_nick FROM ".$db_table['users']." ORDER BY user_nick;");
			while ($arr=mysql_fetch_array($res))
			{
				echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Betreff:</td><td class=\"tbldata\" width=\"70%\"><input type=\"text\" name=\"email_subject\" value=\"\" size=\"30\" maxlength=\"255\"></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Text:</td><td class=\"tbldata\" width=\"70%\"><textarea name=\"email_text\" rows=\"10\" cols=\"40\"></textarea></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Text-Ausrichtung:</td><td class=\"tbldata\" width=\"70%\"><select name=\"email_align\">";
			echo "<option value=\"center\" selected=\"selected\">Zentriert</option>";
			echo "<option value=\"left\">Linksb&uuml;ndig</option>";
			echo "<option value=\"right\">Rechtsb&uuml;ndig</option>";
			echo "<option value=\"justify\">Blocksatz</option>";
			echo "</select></td></tr>";
            echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">Design w&auml;hlen:</td><td class=\"tbldata\" width=\"70%\"><select name=\"email_style\">";
            echo "<option value=\"self\" selected=\"selected\">Design des Benutzers</option>";
            foreach ($designs as $k => $v)
            {
                echo "<option value=\"$k\">".$v['name']."</option>";
            }
            echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"30%\" valign=\"top\">An alle User schicken:</td><td class=\"tbldata\" width=\"70%\"><input type=\"radio\" name=\"email_at_all\" value=\"1\" /> Ja  <input type=\"radio\" name=\"email_at_all\" value=\"0\" checked=\"checked\"/> Nein</td></tr>";

			echo "</table>";
			echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit_preview\" value=\"Vorschau\" ></p>";
			echo "</form>";
		}
	}


/************************
* Nachrichtenverwaltung *
************************/
	else
	{
		//
		// Suchresultate
		//
		if ($_POST['user_search']!="" || $_GET['action']=="searchresults")
		{
			if ($_SESSION['admin']['message_query']=="")
			{
				if ($_POST['message_user_from_id']!="")
					$sql.= " AND message_user_from=".$_POST['message_user_from_id'];
				if ($_POST['message_user_from_nick']!="")
				{
					$uid = get_user_id($_POST['message_user_from_nick']);
					if ($uid>0)
						$sql.= " AND message_user_from=$uid";
				}
				if ($_POST['message_user_to_id']!="")
					$sql.= " AND message_user_to=".$_POST['message_user_to_id'];
				if ($_POST['message_user_to_nick']!="")
				{
					$uid = get_user_id($_POST['message_user_to_nick']);
					if ($uid>0)
						$sql.= " AND message_user_to=$uid";
				}
				if ($_POST['message_subject']!="")
				{
					if (stristr($_POST['qmode']['message_subject'],"%")) $addchars = "%";else $addchars = "";
					$sql.= " AND message_subject ".stripslashes($_POST['qmode']['message_subject']).$_POST['message_subject']."$addchars'";
				}
				if ($_POST['message_text']!="")
				{
					if (stristr($_POST['qmode']['message_text'],"%")) $addchars = "%";else $addchars = "";
					$sql.= " AND message_text ".stripslashes($_POST['qmode']['message_text']).$_POST['message_text']."$addchars'";
				}
				if ($_POST['message_read']<2)
				{
					if ($_POST['message_read']==1)
						$sql.= " AND (message_read=1)";
					else
						$sql.= " AND (message_read=0)";
				}
				if ($_POST['message_massmail']<2)
				{
					if ($_POST['message_massmail']==1)
						$sql.= " AND (message_massmail=1)";
					else
						$sql.= " AND (message_massmail=0)";
				}
				if ($_POST['message_deleted']<2)
				{
					if ($_POST['message_deleted']==1)
						$sql.= " AND (message_deleted=1)";
					else
						$sql.= " AND (message_deleted=0)";
				}
				if ($_POST['message_cat_id']!="")
					$sql.= " AND message_cat_id=".$_POST['message_cat_id'];

				if ($_POST['message_limit']!="")
					$limit=" LIMIT ".$_POST['message_limit'].";";
				else
					$limit=";";

				$sqlstart = "SELECT 
					message_id,
					message_user_from,
					message_user_to,
					md.subject,
					md.text,
					message_timestamp,
					message_deleted,
					message_read,
					message_archived,
					cat_name 
					FROM 
						messages 
					INNER JOIN
						 message_data as md 
						 ON message_id=md.id
					INNER JOIN
						message_cat
						ON message_cat_id=cat_id 
					WHERE 1 ";
				$sqlend = " ORDER BY message_timestamp DESC";
				$sql = $sqlstart.$sql.$sqlend.$limit;
				$_SESSION['admin']['message_query']=$sql;
			}
			else
				$sql = $_SESSION['admin']['message_query'];

			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";

				echo "<b>Legende:</b> <span style=\"color:#0f0;\">Ungelesen</span>, <span style=\"color:#f90;\">Gel&ouml;scht</span>, <span style=\"font-style:italic;\">Archiviert</span><br/><br/>";

				echo "<table class=\"tb\">";
				echo "<tr>";
				echo "<th>Sender</th>";
				echo "<th>Empf&auml;nger</th>";
				echo "<th>Betreff</th>";
				echo "<th>Datum</th>";
				echo "<th>Kategorie</th>";
				echo "<th>Aktion</th>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['message_user_from']>0)
						$uidf = get_user_nick($arr['message_user_from']);
					else
						$uidf = "<i>System</i>";
					if ($arr['message_user_to']>0)
						$uidt = get_user_nick($arr['message_user_to']);
					else
						$uidt = "<i>System</i>";

					if ($arr['message_deleted']==1)
						$style="style=\"color:#f90\"";
					elseif ($arr['message_read']==0)
						$style="style=\"color:#0f0\"";
					elseif($arr['message_archived']==1)
						$style="style=\"font-style:italic;\"";
					else
						$style="";
					echo "<tr>";
					echo "<td $style>".cut_string($uidf,11)."</a></td>";
					echo "<td $style>".cut_string($uidt,11)."</a></td>";
					echo "<td $style ".tm($arr['subject'],text2html(substr($arr['text'], 0, 500))).">".cut_string($arr['subject'],20)."</a></td>";
					echo "<td $style>".date("Y-d-m H:i",$arr['message_timestamp'])."</a></td>";
					echo "<td $style>".$arr['cat_name']."</td>";
					echo "<td>".edit_button("?page=$page&sub=edit&message_id=".$arr['message_id'])."</td>";
					echo "</tr>";
				}
				echo "</table><br/>";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/>";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" /><br/><br/>";
			}
		}

		elseif ($_GET['sub']=="edit")
		{
			$res = dbquery("
			SELECT 
				* 
			FROM 
				messages
			INNER JOIN
				message_data as md ON md.id=message_id AND  message_id=".$_GET['message_id'].";");
			$arr = mysql_fetch_array($res);
			if ($arr['message_user_from']>0)
				$uidf = get_user_nick($arr['message_user_from']);
			else
				$uidf = "<i>System</i>";
			if ($arr['message_user_to']>0)
				$uidt = get_user_nick($arr['message_user_to']);
			else
				$uidt = "<i>System</i>";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">".$arr['message_id']."</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Sender</td><td class=\"tbldata\">$uidf</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Empf&auml;nger</td><td class=\"tbldata\">$uidt</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Datum</td><td class=\"tbldata\">".date("Y-m-d H:i:s",$arr['message_timestamp'])."</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Betreff</td><td class=\"tbldata\">".text2html($arr['subject'])."</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">".text2html($arr['text'])."</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Quelltext</td>
			<td class=\"tbldata\"><textarea rows=\"20\" cols=\"80\" readonly=\"readonly\">".stripslashes($arr['text'])."</textarea></td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Gelesen?</td><td class=\"tbldata\">";
			switch ($arr['message_read'])
			{
				case 1: echo "Ja"; break;case 0: echo "Nein";break;
			}
			echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Gel&ouml;scht?</td><td class=\"tbldata\">";
			switch ($arr['message_deleted'])
			{
				case 1: echo "Ja"; break;case 0: echo "Nein";break;
			}
			echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Rundmail?</td><td class=\"tbldata\">";
			switch ($arr['message_massmail'])
			{
				case 1: echo "Ja"; break;case 0: echo "Nein";break;
			}
			echo "</td></tr>";

			echo "</table><br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;action=searchresults'\" value=\"Zur&uuml;ck zu den Suchergebnissen\" /> &nbsp;
			<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
		}

		else
		{
			$_SESSION['admin']['message_query']=null;
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page\" method=\"post\">";
			echo "<table class=\"tb\">";
			echo "<tr><th style=\"width:130px;\">Sender-ID</th><td><input type=\"text\" name=\"message_user_from_id\" value=\"\" size=\"4\" maxlength=\"250\" /></td>";;
			echo "<tr><th>Sender-Nick</th><td><input type=\"text\" name=\"message_user_from_nick\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";;
			echo "<tr><th>Empf&auml;nger-ID</th><td><input type=\"text\" name=\"message_user_to_id\" value=\"\" size=\"4\" maxlength=\"250\" /></td>";;
			echo "<tr><th>Empf&auml;nger-Nick</th><td><input type=\"text\" name=\"message_user_to_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'message_user_to_nick','citybox1');\" /><br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td>";;
			echo "<tr><th>Betreff</th><td><input type=\"text\" name=\"message_subject\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('message_subject');echo "</td></tr>";
			echo "<tr><th>Text</th><td><input type=\"text\" name=\"message_text\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('message_text');echo "</td></tr>";
			echo "<tr><th>Gelesen</th><td><input type=\"radio\" name=\"message_read\" value=\"2\" checked=\"checked\" /> Egal
			<input type=\"radio\" name=\"message_read\" value=\"0\" /> Nein
			<input type=\"radio\" name=\"message_read\" value=\"1\" /> Ja</td></tr>";
			echo "<tr><th>Rundmail</th><td><input type=\"radio\" name=\"message_massmail\" value=\"2\" checked=\"checked\" /> Egal
			<input type=\"radio\" name=\"message_massmail\" value=\"0\" /> Nein
			<input type=\"radio\" name=\"message_massmail\" value=\"1\" /> Ja</td></tr>";
			echo "<tr><th>Gel&ouml;scht</th><td><input type=\"radio\" name=\"message_deleted\" value=\"2\" checked=\"checked\" /> Egal
			<input type=\"radio\" name=\"message_deleted\" value=\"0\" /> Nein
			<input type=\"radio\" name=\"message_deleted\" value=\"1\" /> Ja</td></tr>";
			echo "<tr><th>Kategorie</th><td><select name=\"message_cat_id\">";
			echo "<option value=\"\">(egal)</option>";
			$cres = dbquery("SELECT cat_id,cat_name FROM ".$db_table['message_cat']." ORDER BY cat_order;");
			while ($carr = mysql_fetch_array($cres))
			{
				echo "<option value=\"".$carr['cat_id']."\">".$carr['cat_name']."</option>";
			}
			echo "</select></tr>";
			echo "<tr><th>Anzahl Datens&auml;tze</th><td class=\"tbldata\"><select name=\"message_limit\">";
			for ($x=100;$x<=2000;$x+=100)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";

			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"user_search\" value=\"Suche starten\" /></form>";

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM messages;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";
		}

	}
?>

