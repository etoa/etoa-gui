<?PHP

	//
	// Updates
	//
	if($sub=='log')
	{
		echo "<h1>InGame-Chat Log</h1>";
		echo "<table class=\"tb\">
		<colgroup>
		<col style=\"width:80px;\" />
		<col style=\"width:80px;\" />
		<col />
		<col />
		</colgroup>";
		echo "<tr>";
		echo "<tr>";
		echo "<th colspan=\"2\">";
		if (isset($_GET['order_field']) && $_GET['order_field']=="timestamp")
		{
			echo "<i>Datum</i> ";
		}
		else
		{
			echo "Datum ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=timestamp&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=timestamp&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "</th>";

		if (isset($_GET['order_field']) && $_GET['order_field']=="nick")
		{
			echo "<th><i>User</i> ";
		}
		else
		{
			echo "<th>User ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=nick&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=nick&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "<th>Nachricht</th>";
		echo "</tr>";

		if (isset($_GET['order_field']) && $_GET['order_field']=="nick")
		{
			$order="nick,timestamp";
		}
		else
		{
			$order="timestamp";
		}

		if (isset($_GET['order']) && $_GET['order']=="ASC")
		{
			$sort="ASC";
		}
		else
		{
			$sort="DESC";
		}

		$res=dbquery("
		SELECT
				*
		FROM
			chat_log
		ORDER BY
			$order $sort
		LIMIT 10000;");
		if (mysql_num_rows($res)>0)
		{
			$cnt = 1;
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>";
				echo "<td>".date("d.m.Y",$arr['timestamp'])."</td>";
				echo "<td>".date("H:i:s",$arr['timestamp'])."</td>";
				echo "<td><a href=\"?page=user&sub=edit&id=".$arr['user_id']."\">".$arr['nick']."</a></td>";
				echo "<td>".$arr['text']."</td>";
				echo "</tr>";
				$cnt++;
			}
		}
		else
		{
			echo "<tr><td colspan=\"5\" align=\"center\"><i>Keine Nachrichten vorhanden!</i></tr>";
		}
		echo "</table>";
	}

	//
	// Live Chat
	//
	else
	{
		if (isset($_GET['ban']) && $_GET['ban'] > 0)
		{
			$uid = $_GET['ban'];
			dbquery("INSERT INTO
				chat_banns
			(user_id,reason,timestamp)
			VALUES (".$uid.",'Banned by Admin',".time().")
			ON DUPLICATE KEY UPDATE timestamp=".time()."");

			dbquery("
			UPDATE
				chat_users
			SET
				kick='Bannend by Admin'
			WHERE
				user_id='".$uid."'");
			ChatManager::sendSystemMessage(get_user_nick($uid)." wurde gebannt!");
		}
		elseif(isset($_GET['unban']) && $_GET['unban'] > 0)
		{
			$uid = $_GET['unban'];
			dbquery("DELETE FROM
				chat_banns
			WHERE
				user_id=".$uid.";");
		}
		elseif(isset($_GET['kick']) && $_GET['kick'] > 0)
		{
			$uid = $_GET['kick'];
			dbquery("
			UPDATE
				chat_users
			SET
				kick='Kicked by Admin'
			WHERE
				user_id='".$uid."'");
			ChatManager::sendSystemMessage(get_user_nick($uid)." wurde gekickt!");
		}
		elseif(isset($_GET['del']) && $_GET['del'] > 0)
		{
			$uid = $_GET['del'];
			dbquery("
			DELETE FROM
				chat_users
			WHERE
				user_id='".$uid."'");
		}
	?>

	<h1>InGame-Chat</h1>
	<fieldset style="width:70%;float:left;height:500px;">
		<legend>Live-Chat</legend>
			<div id="chatitems" style="height:100%;overflow:auto;background:#222;padding:3px">

			</div>
			<div id="lastid" style="display:none;visibility:hidden">
				<?PHP echo $lastid;?>
			</div>
			<script type="text/javascript">
				xajax_loadChat(0);
			</script>
	</fieldset>
	<fieldset style="width:25%;float:right;height:300px;">
		<legend>Users online</legend>
		<div id="chatuserlist" style="display:block;">
			Lade...
		</div>
			<script type="text/javascript">
				xajax_showChatUsers();

			</script>
	</fieldset>

	<fieldset style="width:25%;float:right;height:163px;margin-top:20px">
		<legend>Gebannte User</legend>
		<div id="bannedchatuserlist" style="display:block;">
			Lade...
		</div>
			<script type="text/javascript">
				xajax_showBannedChatUsers();

			</script>
	</fieldset>
		<?PHP
	}
?>
