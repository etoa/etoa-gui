<?PHP
		if (intval($_GET['msg_id'])>0)
		{
			$mres = dbquery("
			SELECT
        md.subject,
        md.text,
        m.message_timestamp,
        m.message_user_to,
        m.message_read
			FROM
				messages AS m
			INNER JOIN
				message_data as md
				ON md.id=message_id				
        AND m.message_id='".intval($_GET['msg_id'])."'
        AND m.message_user_from='".$cu->id."'
        ");
			if (mysql_num_rows($mres)>0)
			{
				$marr = mysql_fetch_array($mres);
				$sender = $marr['message_user_from']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>System</i>';
				$subj = $marr['subject']!="" ? stripslashes($marr['subject']) : "<i>Kein Titel</i>";
				
				tableStart();
				echo "<tr><th colspan=\"2\">".$subj."</th></tr>";
				echo "<tr><th style=\"width:100px;\">Datum:</td><td>".date("d.m.Y H:i",$marr['message_timestamp'])."</td></tr>";
				echo "<tr><th>Sender:</td><td>".$sender."</td></tr>";
				echo "<tr><th>Text:</td><td>".text2html($marr['text'])."</td></tr>";
				tableEnd();
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=sent'\" /> &nbsp; ";
				}
				else
				{
					error_msg("Diese Nachricht existiert nicht!");
					echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=sent'\" />";
				}
		}
		else
		{
			echo "<table class=\"tb\">";
			echo "<tr><th colspan=\"5\">Gesendete Nachrichten</th></tr>";

			$mres = dbquery("
			SELECT
        md.subject,
        message_id,
        message_timestamp,
        message_user_to,
        message_read,
        user_nick
			FROM
				messages
			INNER JOIN
				message_data as md
				ON md.id=message_id							
			LEFT JOIN
				users
				ON message_user_to=user_id				
			WHERE
        message_user_from='".$cu->id."'
			ORDER BY
				message_timestamp DESC
			LIMIT 30;");
			if (mysql_num_rows($mres)>0)
			{
				while ($marr = mysql_fetch_array($mres))
				{
					$sender = $marr['message_user_to']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>System</i>';
					if ($marr['message_read']==0)
					{
						$im_path = "images/pm_new.gif";
					}
					else
					{
						$im_path = "images/pm_normal.gif";
					}
					echo "<tr><td class=\"tbldata\" style=\"width:16px;\">
					<a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">
					<img src=\"".$im_path."\" style=\"border:none;width:16px;height:18px;\"></a></td>";
					echo "<td class=\"tbldata\"><a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">";
					if ($marr['subject']!="")
					{
						echo stripslashes($marr['subject']);
					}
					else
					{
						echo "<i>Kein Titel</i>";
					}
					echo "</a></td><td class=\"tbldata\" style=\"width:120px;\">".$sender."</td>";
					echo "<td class=\"tbldata\" style=\"width:120px;\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
				}
			}
			else
			{
				echo "<tr><td class=\"tbldata\" width=\"400\" colspan=\"4\"><i>Keine Nachrichten vorhanden</i></td>";
			}
			echo "</table>";
			echo "<br/>Es werden nur die 30 neusten Nachrichten angezeigt.";
		}
?>