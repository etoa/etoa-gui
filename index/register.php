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
	// 	Dateiname: register.php
	// 	Topic: Anmeldung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar:
	//

	function register_error($typ)
	{
		switch ($typ)
		{
			case "register_false_char":
				$str = "Du hast ein unerlaubtes Zeichen im Benutzernamen oder im vollst&auml;ndigen Namen!";
			break;
			case "register_not_filled_out":
				$str = "Nicht alle Felder sind ausgef&uuml;llt!";
			break;
			case "user_exists":
				$str = "Der Benutzer mit diesem Nicknamen oder dieser E-Mail-Adresse existiert bereits!";
			break;
			case "invalid_email":
				$str = "Diese E-Mail-Adresse scheint ung&uuml;ltig zu sein. Pr&uuml;fe nach, ob dein E-Mail-Server online ist und die Adresse im korrekten Format vorliegt!";
			break;
			case "nick_length":
				$str = "Dein Nickname muss mindestens ".NICK_MINLENGHT." Zeichen und maximum ".NICK_MAXLENGHT." Zeichen haben!";
			break;
			case "nick_space":
				$str = "Dein Nickname darf nicht nur aus Leerzeichen bestehen!";
			break;
			default:
				$str = "Ein unbekannter Fehler trat auf!";
			break;
		}
		echo "<div style=\"color:#f50;font-weight:bold;\">Fehler: $str</div><br/>";
	}

	function drawRegForm()
	{
		global $page,$db_table,$conf,$db;
		$rsc = get_races_array();
		
		// Load user count
		$ucnt=mysql_fetch_row($db->query("SELECT COUNT(user_id) FROM ".$db_table['users'].";"));
		
		// Check if registration is switched off
		if ($conf['enable_register']['v']==0)
		{
			echo "Die Registration ist momentan ausgeschaltet! Schau bitte ein anderes Mal vorbei!<br/><br/>";
		}
		// Check if current time is lower tjhan registration opening time
		elseif ($conf['enable_register']['v']==1 && $conf['enable_register']['p1']!="" && $conf['enable_register']['p1']>time())
		{
			echo "Du kannst duch erst am ".date("d.m.Y",$conf['enable_register']['p1'])." ab ".date("H:i",$conf['enable_register']['p1'])." registrieren.<br/>
			 Schau doch dann nochmal vorbei oder registriere dich in einer anderen Runde!<br/><br/>";
		}
		// Check if there are too much users
		elseif ($conf['enable_register']['p2']<= $ucnt[0])
		{
			echo "Das Spiel ist mit ".$ucnt[0]." registrierten Mitspielern momentan ausgelastet.<br/> 
			Schau bitte ein anderes Mal vorbei oder registriere dich in einer anderen Runde!<br/><br/>";
		}
		else
		{
			
			$userName = isset($_SESSION['REGISTER']['register_user_name']) ? $_SESSION['REGISTER']['register_user_name'] : '';
			$userNick = isset($_SESSION['REGISTER']['register_user_nick']) ? $_SESSION['REGISTER']['register_user_nick'] : '';
			$userEmail = isset($_SESSION['REGISTER']['register_user_email']) ? $_SESSION['REGISTER']['register_user_email'] : '';
			$raceId = isset($_SESSION['REGISTER']['register_user_race_id']) ? $_SESSION['REGISTER']['register_user_race_id'] : 0;
			echo 'Melde dich hier für die '.GAMEROUND_NAME.' von '.$conf['game_name']['v'].' an. Es sind noch <b>'.max($conf['enable_register']['p2']-$ucnt[0],0).'</b> von <b>'.$conf['enable_register']['p2'].'</b> Plätzen frei!<br/><br/>';
			echo "<form action=\"?index=register\" method=\"post\"><div>";
			echo "<table style=\"margin:5px auto;width:700px;\">";
			
			echo "<tr><th class=\"tbltitle\" style=\"width:150px;\">Vollst&auml;ndiger Name:</th>";
			echo "<td class=\"tbldata\" style=\"width:170px;\">
				<input type=\"text\" name=\"register_user_name\" maxlength=\"".NAME_MAXLENGTH."\" size=\"".NAME_MAXLENGTH."\" value=\"".$userName."\" onkeyup=\"xajax_registerCheckName(this.value)\" onblur=\"xajax_registerCheckName(this.value)\" /></td>";
			echo "<td class=\"tbldata\" id=\"nameStatus\">Hier musst du deinen realen Namen angeben; dies dient zur Kontrolle gegen Multis. Dieser Name ist nur f&uuml;r Administratoren sichtbar!</td></tr>";
			
			echo "<tr><th class=\"tbltitle\">Benutzername:</th>";
			echo "<td class=\"tbldata\">
				<input type=\"text\" name=\"register_user_nick\" maxlength=\"".NICK_MAXLENGHT."\" size=\"".NICK_MAXLENGHT."\" value=\"".$userNick."\" onkeyup=\"xajax_registerCheckNick(this.value)\" onblur=\"xajax_registerCheckNick(this.value)\" /></td>";
			echo "<td class=\"tbldata\" id=\"nickStatus\">Mit diesem Name tritts du im Spiel als der Herrscher deines Volkes auf. <b>Der Nickname ist endgültig und kann nicht geändert werden!</b></td></tr>";
			
			echo "<tr><th class=\"tbltitle\">E-Mail:</th>";
			echo "<td class=\"tbldata\">
				<input type=\"text\" name=\"register_user_email\" maxlength=\"50\" size=\"30\" value=\"".$userEmail."\" onkeyup=\"xajax_registerCheckEmail(this.value)\" onblur=\"xajax_registerCheckEmail(this.value)\" /></td>";
			echo "<td class=\"tbldata\" id=\"emailStatus\">Du musst eine g&uuml;ltige E-Mail-Adresse eingeben. Auf diese wird dir ein Passwort zugeschickt mit dem du dich einloggen kannst.</td></tr>";
			
			echo "<tr><th class=\"tbltitle\">Rasse:</th>";
			echo "<td class=\"tbldata\"><select name=\"register_user_race_id\" onchange=\"xajax_registerShowRace(this.options[this.selectedIndex].value)\" onkeyup=\"xajax_registerShowRace(this.options[this.selectedIndex].value)\" onclick=\"xajax_registerShowRace(this.options[this.selectedIndex].value)\">";
			foreach ($rsc as $race)
			{
				echo "<option value=\"".$race['race_id']."\"";
				if ($race['race_id']==$raceId) 
				{
					echo " selected=\"selected\"";
				}
				echo ">".$race['race_name']."</option>";
			}
			echo "</select></td>";
			// xajax content will be placed in the following cell
			echo "<td class=\"tbldata\" id=\"raceInfo\">
				W&auml;hle eine Rasse f&uuml;r dein Volk. Jede Rasse hat St&auml;rken und Schw&auml;chen, sowie rassenspezifische Raumschiffe
			</td></tr>";
			echo "<tr><td colspan=\"3\">&nbsp;</td></tr>";
			echo "<tr><td colspan=\"3\" class=\"tbldata\"><ul>
			<li>Mit der Anmeldung akzeptierst du unsere <a href=\"javascript:;\" onclick=\"window.open('".LOGINSERVER_URL."?page=regeln');\" >Regeln</a>!</li>
			<li>Pro Person darf nur 1 Account verwendet werden. Multis werden rigoros <a href=\"javascript:;\" onclick=\"window.open('?index=pillory');\">gesperrt</a>!</li>
			<li>Nach der Registration wird ein automatisch generiertes Passwort an die angegebene E-Mail-Adresse gesendet.</li>
			<li>Der Name und die E-Mail-Adresse können nur von den Game-Administratoren eingesehen werden und werden nicht weitergegeben.</li>
			</ul></td></tr>";
			echo "</table><br/><input type=\"submit\" name=\"register_submit\" value=\"Anmelden!\" /></div></form><br/>";
		}		
		
	}

	// BEGIN SCRIPT //
	
	showTitle('Registrieren');
	
	//
	// Handle registration submit
	//
	if (isset($_POST['register_submit']) && $_POST['register_submit']!="")
	{
		$_SESSION['REGISTER']=$_POST;
		if ($_POST['register_user_name']!="" && $_POST['register_user_nick']!="" && $_POST['register_user_email']!="")
		{
			if (checkValidNick($_POST['register_user_nick']) && checkValidName($_POST['register_user_name']))
			{
				$nick=str_replace(' ','',$_POST['register_user_nick']);
				$nick_length=strlen(utf8_decode($_POST['register_user_nick']));
				if($nick!='')
				{
					if($nick_length>=NICK_MINLENGHT && $nick_length<=NICK_MAXLENGHT)
					{
          	if (checkEmail($_POST['register_user_email'])==TRUE)
          	{
          	  $res = mysql_query("SELECT user_id FROM ".$db_table['users']." WHERE user_nick='".$_POST['register_user_nick']."' OR user_email_fix='".$_POST['register_user_email']."';");
          	  if (mysql_num_rows($res)==0)
          	  {
          	      $pw = mt_rand(100000000,9999999999);
          				$time = time();
          	
          	      if ($db->query("
          	      INSERT INTO
          	      ".$db_table['users']." (
          	          user_name,
          	          user_nick,
          	          user_password,
          	          user_email,
          	          user_email_fix,
          	          user_race_id,
          	          user_registered)
          	      VALUES
          	          ('".$_POST['register_user_name']."',
          	          '".$_POST['register_user_nick']."',
          	          '".pw_salt($pw,$time)."',
          	          '".$_POST['register_user_email']."',
          	          '".$_POST['register_user_email']."',
          	          '".$_POST['register_user_race_id']."',
          	          '".$time."');"))
          	      {
											$rsc = get_races_array();         
											/* 	      	
          	          $email_text = "Hallo ".$_POST['register_user_nick']."<br><br/>Du hast dich erfolgreich beim Sci-Fi Browsergame <a href=\"http://www.etoa.ch\">Escape to Andromeda</a> registriert.<br>Hier nochmals deine Daten:<br><br>";
          	          $email_text.= "<b>Universum:</b> ".GAMEROUND_NAME."<br>";
          	          $email_text.= "<b>Name:</b> ".$_POST['register_user_name']."<br>";
          	          $email_text.= "<b>E-Mail:</b> ".$_POST['register_user_email']."<br>";
          	          $email_text.= "<b>*Nick:</b> ".$_POST['register_user_nick']."<br>";
          	          $email_text.= "<b>*Passwort:</b> ".$pw."<br>";
          	          $email_text.= "<b>Rasse:</b> ".$rsc[$_POST['register_user_race_id']]['race_name']."<br><br>";
          	          $email_text.= "* Benötigst du f&uuml;r das Login!<br><br>";
          	          $email_text.= "WICHTIG: Gib das Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser der Login- und der Einstellungs-Seite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!<br>Desweiteren solltest du dich mit den <a href=\"".LOGINSERVER_URL."?page=regeln\">Regeln</a> bekannt machen, da ein Regelverstoss eine (temporäre) Sperrung deines Accounts zur Folge haben könnte!<br><br>";
          	          $email_text.= "Viel Spass beim Spielen wünscht...<br>Das EtoA-Team";
          	          */
          	          $email_text = "Hallo ".$_POST['register_user_nick']."\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda registriert.\nHier nochmals deine Daten:\n\n";
          	          $email_text.= "Universum: ".GAMEROUND_NAME."\n";
          	          $email_text.= "Name: ".$_POST['register_user_name']."\n";
          	          $email_text.= "E-Mail: ".$_POST['register_user_email']."\n";
          	          $email_text.= "Nick: ".$_POST['register_user_nick']."\n";
          	          $email_text.= "Passwort: ".$pw."\n";
          	          $email_text.= "Rasse: ".$rsc[$_POST['register_user_race_id']]['race_name']."\n\n";
          	          $email_text.= "WICHTIG: Gib das Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser der Login- und der Einstellungs-Seite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!\n";
          	          $email_text.= "Desweiteren solltest du dich mit den Regeln (".LOGINSERVER_URL."?page=regeln) bekannt machen, da ein Regelverstoss eine (zeitweilige) Sperrung deines Accounts zur Folge haben kann!\n\n";
          	          $email_text.= "Viel Spass beim Spielen!\nDas EtoA-Team";

          	
          	          send_mail(0,$_POST['register_user_email'],"EtoA Registrierung",$email_text,"","left",1);
          	
          	          add_log(3,"Der Benutzer ".$_POST['register_user_nick']." (".$_POST['register_user_name'].", ".$_POST['register_user_email'].") hat sich registriert!",time());
          	          infobox_start("Registration erfolgreich!");
          	          echo "Es wurde eine E-Mail an <b>".$_POST['register_user_email']."</b> verschickt, in der ein automatisch generiertes Passwort f&uuml;r deine Erstanmeldung steht.<br/>
          	          Bitte &auml;ndere dieses Passwort sobald als m&ouml;glich in den Einstellungen.<br/><br/>
          	          Solltest du innerhalb der n&auml;chsten 5 Minuten keine E-Mail erhalten, pr&uuml;fe zun&auml;chst dein Spam-Verzeichnis.<br/><br/>
          	          Melde dich bei einem <a href=\"?index=contact\">Admin</a>, falls du keine E-Mail erh&auml;ltst oder andere Anmeldeprobleme auftreten.";
          	          infobox_end();
          	      }
          	      else
          	      {
          	          echo "<h2>Fehler</h2";
          	          echo "Beim Speichern der Daten trat ein Fehler auf! Bitte informiere den Entwickler:<br/><br/><a href=\"mailto:mail@etoa.ch\">E-Mail senden</a></p>";
          	      }
          	      echo "</div><br style=\"clear:both;\" /></div>";
          	      $_SESSION['REGISTER']=Null;
          	  }
          	  else
          	  {
          	      register_error("user_exists");
          	      drawRegForm();
          	  }
          	}
          	else
          	{
          	    register_error("invalid_email");
          	    drawRegForm();
          	}
        	}
        	else
        	{
        		register_error("nick_length");
        		drawRegForm();
        	}
        }
        else
        {
        	register_error("nick_space");
					drawRegForm();  
        }
			}
			else
			{
				register_error("register_false_char");
				drawRegForm();
			}
		}
		else
		{
			register_error("register_not_filled_out");
			drawRegForm();
		}
	}
	
	//
	// Registration form
	//
	else
	{
		drawRegForm();
	}
	
?>
