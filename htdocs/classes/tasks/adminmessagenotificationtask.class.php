<?PHP
	/**
	* Inform admins about incomming messages
	*/
	class AdminMessageNotificationTask implements IPeriodicTask
	{
		function run()
		{
			$cnt = 0;
			$ares = dbquery("
				SELECT
					user_nick,
					user_email,
					player_id
				FROM
					admin_users
				WHERE
					player_id>0
			");
			while ($arow = mysql_fetch_row($ares))
			{
				$mres = dbquery("
					SELECT
						message_data.subject,
						message_data.text,
						users.user_nick
					FROM
						messages
					INNER JOIN
						`message_data`
					ON messages.message_id=message_data.id
					AND messages.message_user_to='".$arow[2]."'
					AND messages.message_mailed=0
					AND messages.message_read=0
					LEFT JOIN
						users
					ON messages.message_user_from=users.user_id");

				if (mysql_num_rows($mres)>0)
				{
					$count = 1;
					$email_text = "Hallo ".$arow[0].",\n\nDu hast ".mysql_num_rows($mres)." neue Nachricht(en) erhalten.\n\n";
					while ($mrow = mysql_fetch_row($mres))
					{
						if ($mrow[2]=="")
						{
							$email_text .= "#".$count." Von System mit dem Betreff '".$mrow[0]."'\n\n\n";
						}
						else
						{
							$email_text .= "#".$count." Von ".$mrow[2]." mit dem Betreff '".$mrow[0]."'\n\n".substr($mrow[1], 0, 500)."\n\n\n";
						}
						$count++;

					}
					$mail = new Mail("Neue private Nachricht in EtoA - Admin",$email_text);
					$mail->send($arow[1]);
					dbquery("UPDATE messages SET messages.message_mailed=1 WHERE messages.message_user_to='".$arow[2]."';");
					$cnt++;
				}
			}
			return "$cnt Admin-Mailbenachrichtugungen versendet";
		}

		function getDescription() {
			return "Admin-Mailbenachrichtugungen versenden";
		}
	}
?>