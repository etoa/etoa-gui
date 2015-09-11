<?PHP
	echo "<h1>Nachricht senden</h1>";
	if ($_GET['id']>0)
	{       
		$res = dbquery("
		SELECT
			user_nick
		FROM
			users
		WHERE
			user_id=".$_GET['id']."
		");
		$arr = mysql_fetch_assoc($res);
		tableStart("Nachricht an ".$arr['user_nick'],'95%');   			
     echo "<tr>
     <th>Titel:</th><td><input type=\"text\" id=\"urgendmsgsubject\" maxlength=\"200\" size=\"50\" /></td></tr>
     <tr><th>Text:</th><td><textarea id=\"urgentmsg\" cols=\"60\" rows=\"4\"></textarea></td></tr>";
    tableEnd(); 
		echo "	<input type=\"button\" onclick=\"xajax_sendUrgendMsg(".$_GET['id'].",document.getElementById('urgendmsgsubject').value,document.getElementById('urgentmsg').value)\" value=\"Senden\" /> &nbsp; 
			<input type=\"button\" onclick=\"window.close()\" value=\"Schliessen\" /><br/><br/>
		";
		
	}
	else
		error_msg("Benutzer nicht vorhanden!");	          
			          
?>