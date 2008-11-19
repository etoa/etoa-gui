<?PHP
		$fields = array();
		$fields['user_name']="Vollst&auml;ndiger Name";
		$fields['user_nick']="Benutzername";
		$fields['user_email_fix']="Fixe E-Mail";

		if (isset($_GET['answer_request']) && $_GET['answer_request']>0)
		{
			echo "<h1>&Auml;nderungsantrag bearbeiten</h1>";
			$res=dbquery("
			SELECT
      	request_id,
      	request_user_id,
      	request_timestamp,
      	request_field,
      	request_value,
      	request_comment,
      	request_handled,
      	request_allowed,
      	request_answer,
      	user_nick as admin
			FROM
      	user_requests
      LEFT JOIN
      	admin_users
      	ON request_admin_id=user_id
			WHERE
				request_id=".$_GET['answer_request'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);

       	$res2=dbquery("
       	SELECT
       		".$arr['request_field']." AS field,
       		user_nick
       	FROM
       	    users
       	WHERE
       	    user_id=".$arr['request_user_id'].";");
				$arr2=mysql_fetch_array($res2);

				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "<input type=\"hidden\" value=\"".$_GET['answer_request']."\" name=\"request_id\" />";
				echo "<table class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\" style=\"width:200px;\">Spieler:</th><td class=\"tbldata\">".$arr2['user_nick']."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Feld:</th><td class=\"tbldata\">".$fields[$arr['request_field']]."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Alter Wert:</th><td class=\"tbldata\">".$arr2['field']."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Neuer Wert:</th><td class=\"tbldata\">".stripslashes($arr['request_value'])."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Zeit:</th><td class=\"tbldata\">".date("d.m.Y H:i",$arr['request_timestamp'])."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Kommentar:</th><td class=\"tbldata\">".text2html($arr['request_comment'])."</td></tr>";
				if ($arr['request_handled']==0)
				{
					echo "<th class=\"tbltitle\">Antwort:</th><td class=\"tbldata\"><input type=\"radio\" name=\"answer\" value=\"allow\" checked=\"checked\" /> Erlauben<br/>";
					echo "<input type=\"radio\" name=\"answer\" value=\"deny\" /> Verbieten<br/>";
					echo "<textarea name=\"answer_text\" rows=\"5\" cols=\"50\"></textarea></td></tr>";
				}
				else
				{
				echo "<tr><th class=\"tbltitle\">Antwort:</th><td class=\"tbldata\">".text2html($arr['request_answer'])."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Antrag angenommen?:</th><td class=\"tbldata\">".($arr['request_allowed']==1 ? "Ja" : "Nein")."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Admin:</th><td class=\"tbldata\">".$arr['admin']."</td></tr>";
					
				}
				echo "</table><br/><br/>";
				
				if ($arr['request_handled']==0)
				{
					echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" /> &nbsp; <input type=\"submit\" name=\"cancel\" value=\"Abbrechen\" /></form>";
				}
				else
				{
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Zurück\" />";
				}
			}
			else
				cms_err_msg("Datensatz nicht vorhanden!");
		}
		else
		{
			echo "<h1>&Auml;nderungsantr&auml;ge</h1>";

			if (isset($_POST['submit']))
			{
				$res=dbquery("SELECT request_user_id,request_field,request_value FROM user_requests WHERE request_id=".$_POST['request_id'].";");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					// User-Nick und E-Mail laden
					$ures=dbquery("SELECT user_nick,user_email FROM users WHERE user_id=".$arr['request_user_id'].";");
					$uarr=mysql_fetch_array($ures);
					if ($_POST['answer']=="allow")
					{
						dbquery("UPDATE users SET ".$arr['request_field']."='".addslashes($arr['request_value'])."' WHERE user_id=".$arr['request_user_id'].";");
						send_msg($arr['request_user_id'],5,"Änderungsanfrage bearbeitet","Hier die Antwort auf deine Änderungsanfrage:\n\n".addslashes($_POST['answer_text'])."");
						$mail_subject="Antrag angenommen";
						$mail_text="Hallo ".$uarr['user_nick']."\n\nDeine Anfrage wurde angenommen. Hier unsere Antwort:\n\n".addslashes($_POST['answer_text']);
					}
					else
					{
						send_msg($arr['request_user_id'],5,"Änderungsanfrage zur&uuml;ckgewiesen","Hier die Antwort auf deine Änderungsanfrage:\n\n".addslashes($_POST['answer_text'])."");
						$mail_subject="Antrag abgelehnt";
						$mail_text="Hallo ".$uarr['user_nick']."\n\nDeine Anfrage wurde nicht angenommen. Hier unsere Antwort:\n\n".addslashes($_POST['answer_text']);
					}
					/****** ToDo: DB-Content *****/
	        $email_header = "From: Escape to Andromeda<mail@etoa.net>\n";
	        $email_header .= "Reply-To: mail@etoa.net\n";
	        $email_header .= "X-Mailer: PHP/" . phpversion(). "\n";
	        $email_header .= "X-Sender-IP: ".$REMOTE_ADDR."\n";
	        $email_header .= "Content-type: text/html\n";
	        $email_header .= "Content-Style-Type: text/css\n";					
					mail_queue($uarr['user_email'],$mail_subject,$mail_text,"");
					cms_ok_msg("Antwort gesendet!");
					
					dbquery("
					UPDATE 
						user_requests
					SET 
						request_handled=1,
						request_allowed=".($_POST['answer']=="allow" ? 1 : 0).",
						request_admin_id=".$_SESSION[SESSION_NAME]['user_id'].",
						request_answer='".addslashes($_POST['answer_text'])."'
					WHERE 
						request_id=".$_POST['request_id']."
					;");					
				}
				else
					cms_err_msg("Der Datensatz ist nicht vorhanden!");
			}

			echo "<h2>Offene Anfragen</h2>";
			$res=dbquery("SELECT request_id,user_nick,user_email_fix,user_name,request_timestamp,
				request_field,
				request_value 
			FROM 
				user_requests
			LEFT JOIN
				users 
			ON
				user_id=request_user_id
			WHERE
				request_handled=0
			ORDER BY 
				request_timestamp ASC
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\">Spieler</th>
				<th class=\"tbltitle\">Feld</th>
				<th class=\"tbltitle\">Alter Wert</th>
				<th class=\"tbltitle\">Neuer Wert</th>
				<th class=\"tbltitle\">Zeit</th>
				<th class=\"tbltitle\">Aktionen</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".$arr['user_nick']."</td>";
					echo "<td class=\"tbldata\">".$fields[$arr['request_field']]."</td>";
					echo "<td class=\"tbldata\">".$arr[$arr['request_field']]."</td>";
					echo "<td class=\"tbldata\">".stripslashes($arr['request_value'])."</td>";
					echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['request_timestamp'])."</td>";
					echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;answer_request=".$arr['request_id']."\">Beantworten</a></td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine Anfragen vorhanden</i>";
			}
			
			echo "<h2>Bearbeitete Anfragen</h2>";
			$res=dbquery("
			SELECT 
				request_id,
				user_nick,
				user_email_fix,
				user_name,
				request_timestamp,
				request_field,
				request_value,
				request_allowed
			FROM 
				user_requests
			LEFT JOIN
				users 
			ON
				user_id=request_user_id 
			WHERE
				request_handled=1
			ORDER BY 
				request_timestamp ASC;
			");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\">Spieler</th><th class=\"tbltitle\">Feld</th>
				<th class=\"tbltitle\">Alter Wert</th>
				<th class=\"tbltitle\">Neuer Wert</th>
				<th class=\"tbltitle\">Angenommen?</th>
				<th class=\"tbltitle\">Zeit</th>
				<th class=\"tbltitle\">Aktionen</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".$arr['user_nick']."</td>";
					echo "<td class=\"tbldata\">".$fields[$arr['request_field']]."</td>";
					echo "<td class=\"tbldata\">".$arr[$arr['request_field']]."</td>";
					echo "<td class=\"tbldata\">".stripslashes($arr['request_value'])."</td>";
					echo "<td class=\"tbldata\">".($arr['request_allowed']==1 ? "Ja" : "Nein")."</td>";
					echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['request_timestamp'])."</td>";
					echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;answer_request=".$arr['request_id']."\">Anzeigen</a></td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine Anfragen vorhanden</i>";
			}			
		}
?>