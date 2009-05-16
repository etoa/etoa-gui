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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: def.inc.php
	// 	Created: 07.5.2007
	// 	Last edited: 06.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Main chat screen
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	define('RELATIVE_ROOT','../');
	include("../inc/bootstrap.inc.php");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	
	<head>
		<title>EtoA Chat</title>
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
	 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-language" content="de" />		
		<link rel="stylesheet" type="text/css" href="../css/chat.css" />
		<?PHP
			echo $xajax->printJavascript(XAJAX_DIR);
		?>
		<script type="text/javascript">
			function showUserList()
			{
				if (document.getElementById('userlist').style.display=='')
				{
					hideUserList();
				}
				else
				{
					document.getElementById('userlist').style.bottom='0px';
					document.getElementById('userlist').style.display='';
					window.scrollBy(0,100000);
					document.getElementById('userListButton').value='User verbergen';
					xajax_showChatUsers();
				}
			}
			function hideUserList()
			{
				document.getElementById('userlist').style.display='none';
				window.scrollBy(0,100000);
				document.getElementById('userListButton').value='User anzeigen';
				xajax_showChatUsers();
			}
		</script>			
	</head> 		
	<body>
		<?PHP
			$res = dbquery("
			SELECT * FROM
				chat_banns
			WHERE
				user_id=".$_SESSION['user_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				echo "<p>Du wurdest vom Chat gebannt!<br/><br/>
				<b>Grund:</b> ".$arr['reason']."<br/>
				<b>Zeit:</b> ".df($arr['timestamp'])."</p>";
			}
			else
			{
		?>
		<div id="chatitems">

		</div>
		

		<div id="userlist" style="display:none;">

		</div>
		
		<div id="lastid" style="display:none;visibility:hidden"><?PHP echo isset($lastid) ? $lastid : 0;?></div>
		
			<div id="chatchannelcontrols">
				<input type="button" id="userListButton" onclick="showUserList()" value="User anzeigen"/>
			</div>		
		
		<script type="text/javascript">
			xajax_loadChat(0);
			xajax_showChatUsers();
			xajax_setChatUserOnline(1);

		</script>
		<?PHP
			}
			?>

	</body>
</html>
<?PHP      
	dbclose();
?>