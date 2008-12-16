<?PHP
	session_start();
	include("../conf.inc.php");
	include("../functions.php");
	dbconnect();

	define('XAJAX_DIR',"../libs/xajax");
	require_once(XAJAX_DIR."/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	require_once("../inc/xajax/chat.xajax.php");
	$xajax->setFlag('debug',false);
	$xajax->processRequest();
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/chat.css" />
		<?PHP
			echo $xajax->printJavascript(XAJAX_DIR);
		?>
		<script type="text/javascript">
			function showChannel(chanid)
			{
				document.getElementById('chatitems').style.display='';
				document.getElementById('userlist').style.display='none';
			}			
			function showUserList()
			{
				document.getElementById('chatitems').style.display='none';
				document.getElementById('userlist').style.display='';
				xajax_showChatUsers();
			}
		</script>
	</head>
	<body id="chattext" >
		<div id="chatitems">

		</div>
		<div id="lastid" style="display:none;visibility:hidden"><?PHP echo $lastid;?></div>
		<script type="text/javascript">xajax_loadChat(0);xajax_setChatUserOnline();</script>
		
		<div id="userlist" style="display:none;">

		</div>
	
		<div id="channelbox">
		[<a href="javascript:;" onclick="showChannel(0)">Allgemein</a>]
		[<a href="javascript:;" onclick="showUserList()">Userliste</a>]
		</div>		
	</body>	
</html>
<?PHP
	dbclose();
?>
