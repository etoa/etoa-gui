<?PHP
	session_start();
	include("../conf.inc.php");
	include("../functions.php");
	dbconnect();

	define(XAJAX_DIR,"../libs/xajax");
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
	</head>
	<body id="chattext" >
		<div id="chatitems">

		</div>
		<div id="lastid" style="display:none;visibility:hidden"><?PHP echo $lastid;?></div>
		<script type="text/javascript">xajax_loadChat(0)</script>
		
<a name="bancor"></a>
<script language="JavaScript">

    if(window.XMLHttpRequest)
    {
      if(window.location.hash != "#bancor")
        window.location.hash = "#bancor";
    }
    else
    {
        window.location.href = "#bancor";
    }
</script>		
		
		
	</body>	
</html>
<?PHP
	dbclose();
?>
