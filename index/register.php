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

	function drawRegForm()
	{
		$cfg = Config::getInstance();
		
		// Load user count
		$ucnt=mysql_fetch_row(dbquery("SELECT COUNT(user_id) FROM users;"));
		
		// Check if registration is switched off
		if ($cfg->get('enable_register')==0)
		{
			echo "Die Registration ist momentan ausgeschaltet! Schau bitte ein anderes Mal vorbei!<br/><br/>";
		}
		// Check if current time is lower tjhan registration opening time
		elseif ($cfg->get('enable_register')==1 && $cfg->p1('enable_register')!="" && $cfg->p1('enable_register')>time())
		{
			echo "Du kannst duch erst am ".date("d.m.Y",$cfg->p1('enable_register'))." ab ".date("H:i",$$cfg->p1('enable_register'))." registrieren.<br/>
			 Schau doch dann nochmal vorbei oder registriere dich in einer anderen Runde!<br/><br/>";
		}
		// Check if there are too much users
		elseif ($cfg->p2('enable_register')<= $ucnt[0])
		{
			echo "Das Spiel ist mit ".$ucnt[0]." registrierten Mitspielern momentan ausgelastet.<br/> 
			Schau bitte ein anderes Mal vorbei oder registriere dich in einer anderen Runde!<br/><br/>";
		}
		else
		{
			
			$userName = isset($_SESSION['REGISTER']['register_user_name']) ? $_SESSION['REGISTER']['register_user_name'] : '';
			$userNick = isset($_SESSION['REGISTER']['register_user_nick']) ? $_SESSION['REGISTER']['register_user_nick'] : '';
			$userEmail = isset($_SESSION['REGISTER']['register_user_email']) ? $_SESSION['REGISTER']['register_user_email'] : '';
			
			
			echo 'Melde dich hier für die '.Config::getInstance()->roundname->v.' von '.$cfg->get('game_name').' an. Es sind noch <b>'.max($cfg->p2('enable_register')-$ucnt[0],0).'</b> von <b>'.$cfg->p2('enable_register').'</b> Plätzen frei!<br/><br/>';
			echo "<form action=\"?index=register\" method=\"post\">
			<div style=\"width:700px;margin:5px auto;\">";

			tableStart("Anmeldeformular");
		
			echo "<tr><th class=\"tbltitle\" style=\"width:150px;\">Vollst&auml;ndiger Name:</th>";
			echo "<td class=\"tbldata\" style=\"width:170px;\">
				<input type=\"text\" id=\"register_user_name\" name=\"register_user_name\" maxlength=\"".NAME_MAXLENGTH."\" size=\"".NAME_MAXLENGTH."\" value=\"".$userName."\" onkeyup=\"xajax_registerCheckName(this.value)\" onblur=\"xajax_registerCheckName(this.value)\" /></td>";
			echo "<td class=\"tbldata\" id=\"nameStatus\">Hier musst du deinen realen Namen angeben; dies dient zur Kontrolle gegen Multis. Dieser Name ist nur f&uuml;r Administratoren sichtbar!</td></tr>";
			
			echo "<tr><th class=\"tbltitle\">Benutzername:</th>";
			echo "<td class=\"tbldata\">
				<input type=\"text\" name=\"register_user_nick\" maxlength=\"".NICK_MAXLENGHT."\" size=\"".NICK_MAXLENGHT."\" value=\"".$userNick."\" onkeyup=\"xajax_registerCheckNick(this.value)\" onblur=\"xajax_registerCheckNick(this.value)\" /></td>";
			echo "<td class=\"tbldata\" id=\"nickStatus\">Mit diesem Name tritts du im Spiel als der Herrscher deines Volkes auf. <b>Der Nickname ist endgültig und kann nicht geändert werden!</b></td></tr>";
			
			echo "<tr><th class=\"tbltitle\">E-Mail:</th>";
			echo "<td class=\"tbldata\">
				<input type=\"text\" name=\"register_user_email\" maxlength=\"50\" size=\"30\" value=\"".$userEmail."\" onkeyup=\"xajax_registerCheckEmail(this.value)\" onblur=\"xajax_registerCheckEmail(this.value)\" /></td>";
			echo "<td class=\"tbldata\" id=\"emailStatus\">Du musst eine g&uuml;ltige E-Mail-Adresse eingeben. Auf diese wird dir ein Passwort zugeschickt mit dem du dich einloggen kannst.</td></tr>";
			echo "<tr><td colspan=\"3\">
			<input type=\"checkbox\" name=\"agbread\" value=\"1\" onclick=\"if (this.checked) document.getElementById('register_submit').disabled=false; else document.getElementById('register_submit').disabled='disabled';\" />
			Ich akzeptiere die <a href=\"javascript:;\" onclick=\"window.open('".Config::getInstance()->loginurl->v."/regeln');\" >Regeln</a>
			sowie die <a href=\"javascript:;\" onclick=\"window.open('".Config::getInstance()->loginurl->v."/privacy');\" >Datenschutzerklärung</a>

			<br/><ul style=\"text-align:left;margin-left:30px\">
			<li>Pro Person darf nur 1 Account verwendet werden. Multis werden rigoros <a href=\"javascript:;\" onclick=\"window.open('?index=pillory');\">gesperrt</a>!</li>
			<li>Nach der Registration wird ein automatisch generiertes Passwort an die angegebene E-Mail-Adresse gesendet.</li>
			<li>Der Name und die E-Mail-Adresse können nur von den Game-Administratoren eingesehen werden und werden nicht weitergegeben.</li>
			</ul>

			</td></tr>";
			echo "</table><br/>";
			

			echo "<input type=\"submit\" id=\"register_submit\" disabled=\"disabled\" name=\"register_submit\" value=\"Anmelden!\" /></div></form><br/>
			<script type=\"text/javascript\">document.getElementById('register_user_name').focus()</script>";
		}		
		
	}

	// BEGIN SCRIPT //
	
	echo '<h1>Account registrieren</h1>';
	
	//
	// Handle registration submit
	//
	if (isset($_POST['register_submit']) && $_POST['register_submit']!="")
	{
		$_SESSION['REGISTER']=$_POST;
		
		$errorCode="";
		if (User::register(array(
			"name" => $_POST['register_user_name'],
			"nick" => $_POST['register_user_nick'],
			"email" => $_POST['register_user_email']),$errorCode))
    {
				

        iBoxStart("Registration erfolgreich!");
        echo "Es wurde eine E-Mail an <b>".$_POST['register_user_email']."</b> verschickt, in der ein automatisch generiertes Passwort f&uuml;r deine Erstanmeldung steht.
        <b>Bitte &auml;ndere dieses Passwort sobald als m&ouml;glich in den Einstellungen.</b><br/><br/>
        Solltest du innerhalb der n&auml;chsten 5 Minuten keine E-Mail erhalten, pr&uuml;fe zun&auml;chst dein Spam-Verzeichnis.<br/><br/>
        Melde dich bei einem <a href=\"?index=contact\">Admin</a>, falls du keine E-Mail erh&auml;ltst oder andere Anmeldeprobleme auftreten.";
        iBoxEnd();

				echo button("Zum Login",LOGINSERVER_URL);

        echo "</div><br style=\"clear:both;\" /></div>";
    		$_SESSION['REGISTER']=Null;
    }
    else
    {
			if ($errorCode!="")
	  	{
	      err_msg($errorCode);
	      drawRegForm();
	  	}      	      	
    	else
    	{
        echo "<h2>Fehler</h2>";
        echo "Beim Speichern der Daten trat ein Fehler auf! Bitte informiere den Entwickler:<br/><br/><a href=\"mailto:mail@etoa.ch\">E-Mail senden</a></p>";
    	}
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
