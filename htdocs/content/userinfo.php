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
	
	/**
	* User information
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	echo "<h1>Benutzerprofil</h1>";

	if (!isset($_GET['id']))
		$uid = $cu->id;
	elseif (intval($_GET['id'])>0)
		$uid = intval($_GET['id']);
	else
		$uid = 0;
	
	if ($uid > 0)
	{
		$user = new User($uid);
		
		// Besuchercounter
		if ($user->id != $cu->id)
			$user->visits++;

		if ($user->isValid)
		{			
 			tableStart("Profil von ".$user->nick);
			if ($user->profileImage != "")
			{
				$im = PROFILE_IMG_DIR."/".$user->profileImage;
				if (is_file($im))
				{
					$ims = getimagesize($im);
					echo "<tr><td class=\"tblblack\" colspan=\"2\" style=\"text-align:center;background:#000;\">
					<img src=\"".$im."\" style=\"width:".$ims[0]."px;height:".$ims[1]."px;\" alt=\"Profil\" /></td></tr>";
				}
			}
			if ($user->profileText!="")
			{
				echo "<tr><td colspan=\"2\" style=\"text-align:center\">".text2html($user->profileText)."</td></tr>";
			}
			echo "<tr><th style=\"width:120px;\">Punkte:</th><td>".nf($user->points)."</td></tr>";
      echo "<tr>
      	<th>Rasse:</th>
      	<td>".$user->race->name."</td>
      </tr>";
			if ($user->allianceId != 0)
			{
				echo "<tr><th style=\"width:120px;\">Allianz:</th><td>";
				if ($user->allianceRankName() !="")
				{
					echo $user->allianceRankName()." von ";
				}
				echo "<a href=\"?page=alliance&amp;id=".$user->allianceId."\">".$user->alliance."</a></td></tr>";
			}
			if ($user->visits > 0)
			{
				echo "<tr><th style=\"width:120px;\">Besucherz&auml;hler:</th><td>".nf($user->visits)." Besucher</td></tr>";
			}
			if ($user->rank > 0)
			{
				echo "<tr><th style=\"width:120px;\">Aktueller Rang:</th><td>".nf($user->rank)."</td></tr>";
			}					
			if ($user->rankHighest>0)
			{
				echo "<tr><th style=\"width:120px;\">Bester Rang:</th><td>".nf($user->rankHighest)."</td></tr>";
			}
			if ($user->rating->battle > 0)
			{
				echo "<tr><th style=\"width:120px;\">Kampfpunkte:</th><td>".nf($user->rating->battle)." (Gewonnen/Verloren/Total: ".nf($user->rating->battlesWon)."/".nf($user->rating->battlesLost)."/".nf($user->rating->battlesFought).")</td></tr>";
			}			
			if ($user->rating->trade > 0)
			{
				echo "<tr><th style=\"width:120px;\">Handelspunkte:</th><td>".nf($user->rating->trade)." (Einkäufe/Verkäufe: ".nf($user->rating->tradesBuy)."/".nf($user->rating->tradesSell).")</td></tr>";
			}			
			if ($user->rating->diplomacy > 0)
			{
				echo "<tr><th style=\"width:120px;\">Diplomatiepunkte:</th><td>".nf($user->rating->diplomacy)."</td></tr>";
			}
			if ($user->profileBoardUrl !="")
			{
				echo "<tr><th style=\"width:120px;\">Foren-Profil:</th><td><a href=\"".$user->profileBoardUrl."\">".$user->profileBoardUrl."</a></td></tr>";
			}
			if ($user->registered > 0)
			{
				echo "<tr><th style=\"width:120px;\">Registriert:</th><td>".df($user->registered)." (dabei seit ".tf(time()-$user->registered).")</td></tr>";
			}			
			if ($user->admin)
			{
				echo "<tr><th style=\"width:120px;\">Game-Admin:</th><td class=\"adminColor\">Dies ist ein Account eines Game-Admins. Er darf gemäss Regeln nicht angegriffen werden.</td></tr>";
			}			
			if ($user->chatadmin)
			{
				echo "<tr><th style=\"width:120px;\">Chat-Admin:</th><td>Dies ist ein Account eines Chat-Admins. Er hat das Recht, Spieler im Chat zu kicken oder zu bannen.</td></tr>";
			}			
			if ($user->ghost)
			{
				echo "<tr><th style=\"width:120px;\">Geist:</th><td>Dies ist ein Geist-Account. Er wird nicht in der Statistik angezeigt.</td></tr>";
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
				user_id=".$user->id." 
				AND public=1
			ORDER BY timestamp DESC
			LIMIT 10;");
			if (mysql_num_rows($lres) > 0)
			{
				while ($larr = mysql_fetch_array($lres))
				{
					echo "<div class=\"infoLog\">".text2html($larr['message']);			
					echo "<span>".df($larr['timestamp'],0)."";
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


			if ($user->id==$cu->id)
			{
				iBoxStart("Privates Benutzer-Log");
				$lres = dbquery("
				SELECT
					*
				FROM
					user_log
				WHERE
					user_id=".$user->id." 
					AND public=0
				ORDER BY timestamp DESC
				LIMIT 30;");
				if (mysql_num_rows($lres) > 0)
				{
					while ($larr = mysql_fetch_array($lres))
					{
						echo "<div class=\"infoLog\">".text2html($larr['message']);			
						echo "<span>".df($larr['timestamp'])."";
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
				echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&amp;mode=new&amp;message_user_to=".intval($user->id)."'\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Statistiken anzeigen\" onclick=\"document.location='?page=stats&amp;mode=user&amp;userdetail=".intval($user->id)."'\" /> &nbsp; ";
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
