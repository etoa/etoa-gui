<h1>InGame-Chat</h1>
<fieldset style="width:70%;float:left;height:500px;">
	<legend>Live-Chat</legend>
		<div id="chatitems" style="height:100%;overflow:auto">

		</div>
		<div id="lastid" style="display:none;visibility:hidden"><?PHP echo $lastid;?></div>
		<script type="text/javascript">
			xajax_loadChat(0);
		</script>
</fieldset>
<fieldset style="width:25%;float:right;height:500px;">
	<legend>Users online</legend>
	<div id="userlist" style="display:none;">

	</div>
		<script type="text/javascript">
			xajax_showChatUsers();
			xajax_setChatUserOnline(1);
		</script>
</fieldset>
