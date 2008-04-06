<?PHP
		if (intval($_GET['msg_id'])>0)
		{
			$mres = dbquery("
			SELECT
					m.message_id,
          m.message_subject,
          m.message_timestamp,
          m.message_user_to,
          m.message_text,
          m.message_read
          user_nick,
          user_id
			FROM
				".$db_table['messages']." AS m
			LEFT JOIN
				".$db_table['users']."
				ON user_id=message_user_from
			WHERE
          m.message_id='".intval($_GET['msg_id'])."'
          AND m.message_user_to='".$cu->id()."'
          AND m.message_deleted=1");
			if (mysql_num_rows($mres)>0)
			{
				$marr = mysql_fetch_array($mres);
				$sender = $marr['message_user_from']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>System</i>';
				$subj = $marr['message_subject']!="" ? stripslashes($marr['message_subject']) : "<i>Kein Titel</i>";

				echo "<table class=\"tb\">";
				echo "<tr><th colspan=\"2\">".$subj."</th></tr>";
				echo "<tr><th style=\"width:100px;\">Datum:</td><td>".date("d.m.Y H:i",$marr['message_timestamp'])."</td></tr>";
				echo "<tr><th>Sender:</td><td>".$sender."</td></tr>";
				echo "<tr><th>Text:</td><td>".text2html($marr['message_text'])."</td></tr>";
				echo "</table><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=deleted'\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Wiederherstellen\" onclick=\"document.location='?page=messages&mode=deleted&restore=".$marr['message_id']."'\" />";
			}
			else
			{
				echo "Diese Nachricht existiert nicht!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=deleted'\" />";
			}
		}
		else
		{
			if (isset($_GET['restore']) && $_GET['restore']>0)
			{
				dbquery("
				UPDATE
					".$db_table['messages']."
				SET
					message_deleted=0
				WHERE
          message_id='".intval($_GET['restore'])."'
          AND message_user_to='".$cu->id()."'
          AND message_deleted=1");
      	if (mysql_affected_rows()>0)
      	{
      		echo "Nachricht wurde wiederhergestellt!<br/><br/>";
      	}
			}
			
			echo "<table class=\"tb\">";
			echo "<tr><th colspan=\"5\">Papierkorb</th></tr>";
			$mres = dbquery("
			SELECT
				cat_name,
        message_subject,
        message_id,
        message_timestamp,
        message_user_from,
        user_nick,
        user_id
			FROM
				".$db_table['messages']."
			LEFT JOIN	
				message_cat
				ON message_cat_id=cat_id
			LEFT JOIN
				".$db_table['users']."
				ON user_id=message_user_from
			WHERE
				message_user_to='".$cu->id()."'
				AND message_deleted=1
			ORDER BY
				message_timestamp DESC
			LIMIT 30;");
			if (mysql_num_rows($mres)>0)
			{
				while ($marr = mysql_fetch_array($mres))
				{
					$nick = $marr['message_user_from']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>System</i>';
					$subj = $marr['message_subject']!="" ? stripslashes($marr['message_subject']) : "<i>Kein Titel</i>";
					
					echo "<tr><td style=\"width:16px;\">
					<a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">
					<img src=\"images/pm_normal.gif\" style=\"border:none;width:16px;height:18px;\"></a></td>";
					echo "<td><a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">".$subj;
					echo "</a></td>";
					echo "<td style=\"width:120px;\">".$marr['cat_name']."</td>";
					echo "<td style=\"width:120px;\">".$nick."</td>";
					echo "<td style=\"width:120px;\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
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