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
	session_start();
	include("../bootstrap.inc.php");
	include("../conf.inc.php");
	include("../functions.php");
	define('XAJAX_DIR',"../libs/xajax");
	require_once(XAJAX_DIR."/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	require_once("../inc/xajax/chat.xajax.php");
	$xajax->setFlag('debug',false);
	$xajax->processRequest();
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
	</head> 		
	<body>
		<div id="chatinput">
			<form action="?" method="post" onsubmit="xajax_sendChat(xajax.getFormValues('cform'));return false;" autocomplete="off" id="cform">
				Text: <input type="text" id="ctext" name="ctext" value="" size="40" maxlength="255" /> <br/><br/>
				<select name="ccolor" onchange="document.getElementById('ctext').focus();document.getElementById('ctext').style.color=this.value;this.style.color=this.value">
					<option value="" style="background:#000;color:#fff">Standard</option>
					<option value="#f00" style="background:#000;color:#f00">Rot</option>
					<option value="#f90" style="background:#000;color:#f90">Orange</option>
					<option value="#ff0" style="background:#000;color:#ff0">Gelb</option>
					<option value="#0f0" style="background:#000;color:#0f0">Grün</option>
					<option value="#0ff" style="background:#000;color:#0ff">Cyan</option>
					<option value="#00f" style="background:#000;color:#00f">Blau</option>
					<option value="#FF00E5" style="background:#000;color:#FF00E5">Pink</option>
				</select>
				<input type="button" onclick="xajax_sendChat(xajax.getFormValues('cform'));document.getElementById('ctext').focus();" value="Senden"/> &nbsp;
				<input type="button" onclick="xajax_logoutFromChat();parent.top.location = '..'" value="Chat schliessen"/>
			</form>
			<script type="text/javascript">
				document.forms[0].elements[0].focus()
			</script>
		</div>
	</body>
</html>