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
	* Chat message input field
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	define('RELATIVE_ROOT','../');
	include("../inc/bootstrap.inc.php");

	echo '<?xml version="1.0" encoding="UTF-8"?>';
	
	$cu = new CurrentUser($_SESSION['user_id']);
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
	</head> 		
	<body>
		<div id="chatinput">
			<form action="?" method="post" onsubmit="xajax_sendChat(xajax.getFormValues('cform'));return false;" autocomplete="off" id="cform">
			<?PHP
			$res = dbquery("
			SELECT * FROM
				chat_banns
			WHERE
				user_id=".$cu->id.";");
			if (mysql_num_rows($res)>0)
			{

			}
			else
			{
				echo 	'Text: <input type="text" id="ctext" name="ctext" value="" size="40" maxlength="255" style="color:#'.$cu->properties->chatColor.'"/>
				<br/><br/>
				<input type="hidden" id="ccolor" name="ccolor" value="#'.$cu->properties->chatColor.'">';
				?>

				<input type="button" onclick="xajax_sendChat(xajax.getFormValues('cform'));document.getElementById('ctext').focus();" value="Senden"/> &nbsp;
		<?PHP
			}
			?>

				<input type="button" onclick="xajax_logoutFromChat();parent.top.location = '..'" value="Chat schliessen"/>
			</form>
			<script type="text/javascript">
				document.forms[0].elements[0].focus()
			</script>
		</div>
	</body>
</html>