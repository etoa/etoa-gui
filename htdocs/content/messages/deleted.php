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
	//

		if (isset($_GET['msg_id']))
		{
			if (intval($_GET['msg_id'])>0)
			{
				$mres = dbquery("
				SELECT
					m.message_id,
          			md.subject,
          			m.message_timestamp,
          			m.message_user_to,
          			md.text,
          			m.message_read
          			user_nick,
          			user_id
				FROM
					messages AS m
				INNER JOIN
					message_data as md
					ON md.id=message_id						
				LEFT JOIN
					users
					ON user_id=message_user_from
				WHERE
          			m.message_id='".intval($_GET['msg_id'])."'
          			AND m.message_user_to='".$cu->id."'
          			AND m.message_deleted=1");
				if (mysql_num_rows($mres)>0)
				{
					$marr = mysql_fetch_array($mres);
					$subj = $marr['subject']!="" ? htmlentities($marr['subject'],ENT_QUOTES,'UTF-8') : "<i>Kein Titel</i>";

					tableStart();
					echo "<tr><th colspan=\"2\">".$subj."</th></tr>";
					echo "<tr><th style=\"width:100px;\">Datum:</td><td>".date("d.m.Y H:i",$marr['message_timestamp'])."</td></tr>";
					echo "<tr><th>Sender:</td><td>".userPopUp($marr['message_user_from'],$marr['user_nick'],0)."</td></tr>";
					echo "<tr><th>Text:</td><td>".text2html(addslashes($marr['text']))."</td></tr>";
					tableEnd();
					echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=deleted'\" /> &nbsp; ";
					echo "<input type=\"button\" value=\"Wiederherstellen\" onclick=\"document.location='?page=messages&mode=deleted&restore=".$marr['message_id']."'\" />";
				}
				else
				{
					error_msg("Diese Nachricht existiert nicht!");
					echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=messages&mode=deleted'\" />";
				}
			}
		}
		else
		{
			if (isset($_GET['restore']) && $_GET['restore']>0)
			{
				dbquery("
				UPDATE
					messages
				SET
					message_deleted=0
				WHERE
          			message_id='".intval($_GET['restore'])."'
          			AND message_user_to='".$cu->id."'
          			AND message_deleted=1");
      			if (mysql_affected_rows()>0)
      			{
      				echo "Nachricht wurde wiederhergestellt!<br/><br/>";
      			}
			}
			
			tableStart();
			echo "<tr><th colspan=\"5\">Papierkorb</th></tr>";
			$mres = dbquery("
			SELECT
				cat_name,
        		md.subject,
        		message_id,
        		message_timestamp,
        		message_user_from,
        		user_nick,
        		user_id
			FROM
				messages
			INNER JOIN
				message_data as md
				ON md.id=message_id						
			LEFT JOIN	
				message_cat
				ON message_cat_id=cat_id
			LEFT JOIN
				users
				ON user_id=message_user_from
			WHERE
				message_user_to='".$cu->id."'
				AND message_deleted=1
			ORDER BY
				message_timestamp DESC
			LIMIT 30;");
			if (mysql_num_rows($mres)>0)
			{
				while ($marr = mysql_fetch_array($mres))
				{
					$subj = $marr['subject']!="" ? htmlentities($marr['subject'],ENT_QUOTES,'UTF-8') : "<i>Kein Titel</i>";
					
					echo "<tr><td style=\"width:16px;\">
					<a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">
					<img src=\"images/pm_normal.gif\" style=\"border:none;width:16px;height:18px;\"></a></td>";
					echo "<td><a href=\"?page=$page&msg_id=".$marr['message_id']."&mode=".$mode."\">".$subj;
					echo "</a></td>";
					echo "<td style=\"width:120px;\">".$marr['cat_name']."</td>";
					echo "<td style=\"width:120px;\">".userPopUP($marr['message_user_from'],$marr['user_nick'],0)."</td>";
					echo "<td style=\"width:120px;\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
				}
			}
			else
			{
				echo "<tr><td width=\"400\" colspan=\"4\"><i>Keine Nachrichten vorhanden</i></td>";
			}
			tableEnd();
			echo "<br/>Es werden nur die 30 neusten Nachrichten angezeigt.";
		}
?>