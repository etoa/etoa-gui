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
	// 	Dateiname: pillory.php	
	// 	Topic: Pranger
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 27.04.2007
	// 	Kommentar: 	
	//

	echo '<h1>Pranger</h1>';
	echo "Hier ist eine Liste aller gesperrten Spieler:<br/><br/>";

	$res = dbquery("SELECT 
		u.user_nick,
		u.user_blocked_from,
		u.user_blocked_to,
		u.user_ban_reason, 
		a.user_nick AS admin_nick,
		a.user_email AS admin_email
	FROM 
		users AS u
	LEFT JOIN
		admin_users AS a
	ON
		u.user_ban_admin_id = a.user_id
	WHERE 
		u.user_blocked_from<".time()." 
		AND u.user_blocked_to>".time()." 
	ORDER BY 
		u.user_blocked_from DESC;");
	if (mysql_num_rows($res)>0)
	{
		echo "<table class=\"tbl\" style=\"margin:5px auto;\"><tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Von:</td><td class=\"tbltitle\">Bis:</td><td class=\"tbltitle\">Admin</td><td class=\"tbltitle\">Grund</td></tr>";
		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr>
				<td class=\"tbldata\" valign=\"top\" width=\"90\">".$arr['user_nick']."</td>
				<td class=\"tbldata\" valign=\"top\">".date("d.m.Y H:i",$arr['user_blocked_from'])."</td>
				<td class=\"tbldata\" valign=\"top\">".date("d.m.Y H:i",$arr['user_blocked_to'])."</td>
				<td class=\"tbldata\"><a href=\"mailto:".$arr['admin_email']."\">".$arr['admin_nick']."</a></td>
				<td class=\"tbldata\">".text2html($arr['user_ban_reason'])."</td>
			</tr>";
		}
		echo "</table><br/>";
	}
	else
		echo "<i>Keine Eintr&auml;ge vorhanden!</i><br/><br/>";
?>