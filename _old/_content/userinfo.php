<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: userinfo.php													//
	// Topic: User-Info-Modul				 								//
	// Version: 0.1																	//
	// Letzte Änderung: 19.10.2004									//
	//////////////////////////////////////////////////

	// BEGIN SKRIPT //

	echo "<h1>Benutzer-Info</h1>";

	if (intval($_GET['id'])>0)
	{
		dbquery("UPDATE ".$db_table['users']." SET user_visits=user_visits+1 WHERE user_id='".intval($_GET['id'])."';");
		$res = dbquery("
		SELECT 
            user_visits,
            user_nick,
            user_points,
            user_profile_text,
            user_profile_img_url,
            user_alliance_id,
            user_highest_rank 
		FROM 
			".$db_table['users']." 
		WHERE 
			user_id='".intval($_GET['id'])."';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			$alliances = get_alliance_names();
 			infobox_start(text2html($arr['user_nick']." ".$arr['alliance_name']),1);
			if ($arr['user_profile_img_url']!="")
				echo "<tr><td class=\"tblblack\" colspan=\"2\" style=\"text-align:center\"><img src=\"".$arr['user_profile_img_url']."\" alt=\"Allianz-Logo\" /></td></tr>";
			if ($arr['user_profile_text']!="")
				echo "<tr><td class=\"tbldata\" colspan=\"2\" style=\"text-align:center\">".text2html($arr['user_profile_text'])."</td></tr>";
			echo "<tr><td class=\"tbltitle\" width=\"120\">Punkte:</td><td class=\"tbldata\">".nf($arr['user_points'])."</td></tr>";
			if ($arr['user_alliance_id']>0)
				echo "<tr><td class=\"tbltitle\" width=\"120\">Allianz:</td><td class=\"tbldata\"><a href=\"?page=alliance&info_id=".$arr['user_alliance_id']."\">".$alliances[$arr['user_alliance_id']]['tag']." ".$alliances[$arr['user_alliance_id']]['name']."</a></td></tr>";
			if ($arr['user_visits']>0)
				echo "<tr><td class=\"tbltitle\" width=\"120\">Besucherz&auml;hler:</td><td class=\"tbldata\">".nf($arr['user_visits'])." Besucher</td></tr>";
			if ($arr['user_highest_rank']>0)
				echo "<tr><td class=\"tbltitle\" width=\"120\">Bester Rang:</td><td class=\"tbldata\">".nf($arr['user_highest_rank'])."</td></tr>";
			infobox_end(1);
			echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&mode=new&message_user_to=".intval($_GET['id'])."'\" /> &nbsp; ";
			echo "<input type=\"button\" value=\"Punkteverlauf anzeigen\" onclick=\"document.location='?page=stats&mode=user&limit=&userdetail=".intval($_GET['id'])."'\" /> &nbsp; ";
		}
		else
			echo "<b>Fehler:</b>Dieser Spieler existiert nicht!";
	}
	else
		echo "<b>Fehler:</b> Keine ID angegeben!";

	echo "<input type=\"button\" class=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";


?>
