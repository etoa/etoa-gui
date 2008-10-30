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


	echo "<h1>Benutzerprofil</h1>";

	if (!isset($_GET['id']))
		$uid = $cu->id();

	if (isset($_GET['id']) && intval($_GET['id'])>0)
		$uid = $_GET['id'];
	
	if (isset($uid))
	{
		$user = new User($uid);
		
		// Besuchercounter
		$user->increaseVisitorCounter();

		if ($user->isValid())
		{			
 			tableStart("Profil von ".$user->nick());
			if ($user->profileImage() != "")
			{
				$im = PROFILE_IMG_DIR."/".$user->profileImage();
				$ims = getimagesize($im);
				echo "<tr><td class=\"tblblack\" colspan=\"2\" style=\"text-align:center;background:#000;\">
				<img src=\"".$im."\" style=\"width:".$ims[0]."px;height:".$ims[1]."px;\" alt=\"Profil\" /></td></tr>";
			}
			if ($user->profileText()!="")
			{
				echo "<tr><td colspan=\"2\" style=\"text-align:center\">".text2html($user->profileText())."</td></tr>";
			}
			echo "<tr><th style=\"width:120px;\">Punkte:</th><td>".nf($user->points())."</td></tr>";
      echo "<tr>
      	<th>Rasse:</th>
      	<td>".$user->raceName()."</td>
      </tr>";
			if ($user->allianceName() != "")
			{
				echo "<tr><th style=\"width:120px;\">Allianz:</th><td>";
				if ($user->allianceRankName() !="")
				{
					echo $user->allianceRankName()." von ";
				}
				echo "<a href=\"?page=alliance&amp;info_id=".$user->allianceId."\">".$user->allianceName()."</a></td></tr>";
			}
			if ($user->visits()>0)
			{
				echo "<tr><th style=\"width:120px;\">Besucherz&auml;hler:</th><td>".nf($user->visits())." Besucher</td></tr>";
			}
			if ($user->rank()>0)
			{
				echo "<tr><th style=\"width:120px;\">Aktueller Rang:</th><td>".nf($user->rank())."</td></tr>";
			}					
			if ($user->rankHighest()>0)
			{
				echo "<tr><th style=\"width:120px;\">Bester Rang:</th><td>".nf($user->rankHighest())."</td></tr>";
			}
			if ($user->rating('battle_rating') > 0)
			{
				echo "<tr><th style=\"width:120px;\">Kampfpunkte:</th><td>".nf($user->rating('battle_rating'))."</td></tr>";
			}			
			if ($user->rating('trade_rating') > 0)
			{
				echo "<tr><th style=\"width:120px;\">Handelspunkte:</th><td>".nf($user->rating('trade_rating'))."</td></tr>";
			}			
			if ($user->rating('diplomacy_rating') >0)
			{
				echo "<tr><th style=\"width:120px;\">Diplomatiepunkte:</th><td>".nf($user->rating('diplomacy_rating'))."</td></tr>";
			}
			if ($user->profileBoardUrl() !="")
			{
				echo "<tr><th style=\"width:120px;\">Foren-Profil:</th><td><a href=\"".$user->profileBoardUrl()."\">".$user->profileBoardUrl()."</a></td></tr>";
			}
			if ($user->registered()>0)
			{
				echo "<tr><th style=\"width:120px;\">Registriert:</th><td>".df($user->registered())." (dabei seit ".tf(time()-$user->registered()).")</td></tr>";
			}			
			tableEnd();

			//
			// User-Log
			//
			
			iBoxStart("&Ouml;ffentliches Benutzer-Log");
			$lres = dbquery("
			SELECT
				*
			FROM
				user_log
			WHERE
				user_id=".$user->id()." 
				AND public=1
			ORDER BY timestamp DESC
			LIMIT 10;");
			if (mysql_num_rows($lres) > 0)
			{
				while ($larr = mysql_fetch_array($lres))
				{
					echo "<div style=\"border-bottom:1px solid #aaa;padding:3px 0px 5px 0px;text-align:left;\">".text2html($larr['message']);			
					echo "<span style=\"color:#ddd;font-size:7pt;padding-left:20px;\">".df($larr['timestamp'])."";
					if ($user->id()==$cu->id())
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
			iBoxEnd();


			if ($user->id()==$cu->id())
			{
				iBoxStart("Privates Benutzer-Log");
				$lres = dbquery("
				SELECT
					*
				FROM
					user_log
				WHERE
					user_id=".$user->id()." 
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
				iBoxEnd();
			}
			
			if (!$popup)
			{
				echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&amp;mode=new&amp;message_user_to=".intval($user->id())."'\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Statistiken anzeigen\" onclick=\"document.location='?page=stats&amp;mode=user&amp;userdetail=".intval($user->id())."'\" /> &nbsp; ";
			}

		}
		else
			echo "<b>Fehler:</b> Dieser Spieler existiert nicht!<br/><br/>";
	}
	else
	{
		echo "<b>Fehler:</b> Keine ID angegeben!<br/><br/>";
	}

	if (isset($_GET['id']) && !$popup)
		echo "<input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";



?>
