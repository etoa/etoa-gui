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

if (Alliance::checkActionRights('massmail'))
{
	echo "<h2>Rundmail</h2>";
	
	// Nachricht senden
	if (isset($_POST['submit']) && checker_verify())
	{
		$ures = dbquery("SELECT 
			user_id 
		FROM 
			users 
		WHERE 
			user_alliance_id=".$arr['alliance_id']." 
			AND user_id!=".$cu->id." 
		;");
		if (mysql_num_rows($ures)>0)
		{
			while ($uarr=mysql_fetch_array($ures))
			{
				$subject=addslashes($_POST['message_subject'])."";
				
				Message::sendFromUserToUser($cu->id,$uarr['user_id'],$_POST['message_subject'],$_POST['message_text'],MSG_ALLYMAIL_CAT);
			}
			success_msg("Nachricht wurde gesendet!");
			echo "<input type=\"button\" value=\"Neue Nachricht schreiben\" onclick=\"document.location='?page=$page&action=massmail'\" /> &nbsp; ";
			echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
		}
		else
		{
			error_msg("Nachricht wurde nicht gesendet, keine Mitglieder vorhanden!");
			echo "<input type=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=$page'\" />";
		}
	}
	else
	{
		echo "<form action=\"?page=$page&action=massmail\" method=\"POST\">";
		checker_init();
		if(isset($_GET['message_subject']))
		{
			$subject = $_GET['message_subject'];
		}
		else
		{
			$subject = "";
		}
		tableStart("Nachricht verfassen");
		echo "<tr><th style=\"width:50px;\">Betreff:</th><td><input type=\"text\" name=\"message_subject\" value=\"".$subject."\" size=\"30\" maxlength=\"255\"></td></tr>";
		echo "<tr><th>Text:</th><td><textarea name=\"message_text\" rows=\"5\" cols=\"50\"></textarea><br/>".helpLink('textformat', 'Hilfe zur Formatierung')."</td></tr>";
		tableEnd();
		echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" /> &nbsp;<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
		echo "</form>";
	}
}
?>