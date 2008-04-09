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
            user_visits,
            user_nick,
            user_points,
            user_profile_text,
            user_profile_img,
            user_alliance_id,
            user_alliance_application,
            alliance_tag,
            alliance_name,
            user_highest_rank,
            user_rank_current,
            user_profile_board_url,
            user_registered,
            user_points_battle,
            user_points_trade,
            user_points_diplomacy
		FROM 
			".$db_table['users']." 
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
			if ($arr['user_alliance_id']>0 && $arr['user_alliance_application']=='')
			{
				echo "<tr><th style=\"width:120px;\">Allianz:</th><td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['user_alliance_id']."\">[".$arr['alliance_tag']."] ".$arr['alliance_name']."</a></td></tr>";
			}
			if ($arr['user_visits']>0)
			{
				echo "<tr><th style=\"width:120px;\">Besucherz&auml;hler:</th><td class=\"tbldata\">".nf($arr['user_visits'])." Besucher</td></tr>";
			}
			if ($arr['user_rank_current']>0)
			{
				echo "<tr><th style=\"width:120px;\">Aktueller Rang:</th><td class=\"tbldata\">".nf($arr['user_rank_current'])."</td></tr>";
			}					
			if ($arr['user_highest_rank']>0)
			{
				echo "<tr><th style=\"width:120px;\">Bester Rang:</th><td class=\"tbldata\">".nf($arr['user_highest_rank'])."</td></tr>";
			}
			if ($arr['user_points_battle']>0)
			{
				echo "<tr><th style=\"width:120px;\">Kampfpunkte:</th><td class=\"tbldata\">".nf($arr['user_points_battle'])."</td></tr>";
			}			
			if ($arr['user_points_trade']>0)
			{
				echo "<tr><th style=\"width:120px;\">Handelspunkte:</th><td class=\"tbldata\">".nf($arr['user_points_trade'])."</td></tr>";
			}			
			if ($arr['user_points_diplomacy']>0)
			{
				echo "<tr><th style=\"width:120px;\">Diplomatiepunkte:</th><td class=\"tbldata\">".nf($arr['user_points_diplomacy'])."</td></tr>";
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
			echo "<b>Fehler:</b>Dieser Spieler existiert nicht!";
	}
	else
		echo "<b>Fehler:</b> Keine ID angegeben!";

	echo "<input type=\"button\" class=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";


?>
