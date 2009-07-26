<?PHP
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
		chatSystemMessage(get_user_nick($uid)." wurde gebannt!");
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
		chatSystemMessage(get_user_nick($uid)." wurde gekickt!");
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
		<div id="chatitems" style="height:100%;overflow:auto">

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

