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
	// 	File: userinfo.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.03.2006
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* User information
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	echo "<h1>Benutzer-Info</h1>";

	if (intval($_GET['id'])>0)
	{
		// Besuchercounter
		dbquery("UPDATE ".$db_table['users']." SET user_visits=user_visits+1 WHERE user_id='".intval($_GET['id'])."';");
		
		$res = dbquery("
		SELECT 
			user_id,
            user_visits,
            user_nick,
            user_points,
            user_profile_text,
            user_profile_img,
            user_alliance_id,
            alliance_tag,
            alliance_name,
            user_rank_highest,
            user_rank,
            user_profile_board_url,
            user_registered,
            battle_rating,
            trade_rating,
            diplomacy_rating
		FROM 
			".$db_table['users']." 
		LEFT JOIN
			user_ratings
			ON user_id=id
		LEFT JOIN
			".$db_table['alliances']." 
			ON user_alliance_id=alliance_id
		WHERE 
			user_id='".intval($_GET['id'])."';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			
 			echo '<table class="tb" style="width:640px;">';
 			echo '<tr><th colspan="2" style="text-align:center;">'.$arr['user_nick'].'</th></tr>';
			if ($arr['user_profile_img']!="")
			{
				$im = PROFILE_IMG_DIR."/".$arr['user_profile_img'];
				$ims = getimagesize($im);
				echo "<tr><td class=\"tblblack\" colspan=\"2\" style=\"text-align:center;background:#000;\">
				<img src=\"".$im."\" style=\"width:".$ims[0]."px;height:".$ims[1]."px;\" alt=\"Profil\" /></td></tr>";
			}
			if ($arr['user_profile_text']!="")
			{
				echo "<tr><td colspan=\"2\" style=\"text-align:center\">".text2html($arr['user_profile_text'])."</td></tr>";
			}
			echo "<tr><th style=\"width:120px;\">Punkte:</th><td class=\"tbldata\">".nf($arr['user_points'])."</td></tr>";
			if ($arr['user_alliance_id']>0)
			{
				echo "<tr><th style=\"width:120px;\">Allianz:</th><td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['user_alliance_id']."\">[".$arr['alliance_tag']."] ".$arr['alliance_name']."</a></td></tr>";
			}
			if ($arr['user_visits']>0)
			{
				echo "<tr><th style=\"width:120px;\">Besucherz&auml;hler:</th><td class=\"tbldata\">".nf($arr['user_visits'])." Besucher</td></tr>";
			}
			if ($arr['user_rank']>0)
			{
				echo "<tr><th style=\"width:120px;\">Aktueller Rang:</th><td class=\"tbldata\">".nf($arr['user_rank'])."</td></tr>";
			}					
			if ($arr['user_rank_highest']>0)
			{
				echo "<tr><th style=\"width:120px;\">Bester Rang:</th><td class=\"tbldata\">".nf($arr['user_rank_highest'])."</td></tr>";
			}
			if ($arr['battle_rating']>0)
			{
				echo "<tr><th style=\"width:120px;\">Kampfpunkte:</th><td class=\"tbldata\">".nf($arr['battle_rating'])."</td></tr>";
			}			
			if ($arr['trade_rating']>0)
			{
				echo "<tr><th style=\"width:120px;\">Handelspunkte:</th><td class=\"tbldata\">".nf($arr['trade_rating'])."</td></tr>";
			}			
			if ($arr['diplomacy_rating']>0)
			{
				echo "<tr><th style=\"width:120px;\">Diplomatiepunkte:</th><td class=\"tbldata\">".nf($arr['diplomacy_rating'])."</td></tr>";
			}			

			if ($arr['user_profile_board_url']!="")
			{
				echo "<tr><th style=\"width:120px;\">Foren-Profil:</th><td class=\"tbldata\"><a href=\"".$arr['user_profile_board_url']."\" onclick=\"window.open('".$arr['user_profile_board_url']."');return false;\">".$arr['user_profile_board_url']."</a></td></tr>";
			}
			if ($arr['user_registered']>0)
			{
				echo "<tr><th style=\"width:120px;\">Registriert:</th><td class=\"tbldata\">".df($arr['user_registered'])." (dabei seit ".tf(time()-$arr['user_registered']).")</td></tr>";
			}			
			echo '</table><br/>';
			echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&amp;mode=new&amp;message_user_to=".intval($_GET['id'])."'\" /> &nbsp; ";
			echo "<input type=\"button\" value=\"Punkteverlauf anzeigen\" onclick=\"document.location='?page=stats&amp;mode=user&amp;userdetail=".intval($_GET['id'])."'\" /> &nbsp; ";
		}
		else
			echo "<b>Fehler:</b> Dieser Spieler existiert nicht!<br/><br/>";
	}
	else
		echo "<b>Fehler:</b> Keine ID angegeben!";

	echo "<input type=\"button\" class=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";

	echo "<br/><br/>";
	infobox_start("&Ouml;ffentliches Benutzer-Log");
	$lres = dbquery("
	SELECT
		*
	FROM
		user_log
	WHERE
		user_id=".$arr['user_id']." 
		AND public=1
	ORDER BY timestamp DESC
	LIMIT 10;");
	if (mysql_num_rows($lres) > 0)
	{
		while ($larr = mysql_fetch_array($lres))
		{
			echo "<div style=\"border-bottom:1px solid #aaa;padding:3px 0px 5px 0px;text-align:left;\">".text2html($larr['message']);			
			echo "<span style=\"color:#ddd;font-size:7pt;padding-left:20px;\">".df($larr['timestamp'])."";
			if ($arr['user_id']==$cu->id())
			{
				echo ", ".$larr['host'];
			}
			echo "</span>
			</div>";
		}
		echo "<div style=\"font-size:7pt;padding-top:6px;\">Nur die 10 neusten Nachrichten werden angezeigt.</div>";
	}
	else
	{
		echo "Keine Nachrichten!";
	}
	infobox_end();


	if ($arr['user_id']==$cu->id())
	{
		echo "<br/>";
		infobox_start("Privates Benutzer-Log");
		$lres = dbquery("
		SELECT
			*
		FROM
			user_log
		WHERE
			user_id=".$arr['user_id']." 
			AND public=0
		ORDER BY timestamp DESC
		LIMIT 30;");
		if (mysql_num_rows($lres) > 0)
		{
			while ($larr = mysql_fetch_array($lres))
			{
				echo "<div style=\"border-bottom:1px solid #aaa;padding:3px 0px 5px 0px;text-align:left;\">".text2html($larr['message']);			
				echo "<span style=\"color:#ddd;font-size:7pt;padding-left:20px;\">".df($larr['timestamp'])."";
				echo ", ".$larr['host'];
				echo "</span>
				</div>";
			}
			echo "<div style=\"font-size:7pt;padding-top:6px;\">Nur die 30 neusten Nachrichten werden angezeigt.</div>";
		}
		else
		{
			echo "Keine Nachrichten!";
		}
		infobox_end();
	}


?>
