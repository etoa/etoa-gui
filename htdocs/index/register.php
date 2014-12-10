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
			echo "Du kannst dich erst am ".date("d.m.Y",$cfg->p1('enable_register'))." ab ".date("H:i",$cfg->p1('enable_register'))." registrieren.<br/>
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
			$userPassword = isset($_SESSION['REGISTER']['register_user_password']) ? $_SESSION['REGISTER']['register_user_password'] : '';
			?>
			
			<p>Melde dich hier für die <?=Config::getInstance()->roundname->v?> von <?=APP_NAME?> an. Wenn du Hilfe benötigst, kannst du <a href="?index=contact">hier</a> einen Game-Admin kontaktieren.
			
			<form action="?index=register" method="post">
				<?PHP
				tableStart();
			
				echo "<tr><th style=\"width:150px;\">Vollst&auml;ndiger Name:</th>";
				echo "<td style=\"width:170px;\">
					<input type=\"text\" id=\"register_user_name\" name=\"register_user_name\" maxlength=\"".NAME_MAXLENGTH."\" size=\"".NAME_MAXLENGTH."\" value=\"".$userName."\"  autocomplete=\"off\" /></td>";
				echo "<td>Hier musst du deinen realen Namen angeben; dies dient zur Kontrolle gegen Multis. Dieser Name ist nur f&uuml;r Administratoren sichtbar!<br/><span id=\"nameStatus\"></span></td></tr>";
				
				echo "<tr><th>E-Mail:</th>";
				echo "<td>
					<input type=\"text\" id=\"register_user_email\" name=\"register_user_email\" maxlength=\"50\" size=\"30\" value=\"".$userEmail."\"  autocomplete=\"off\" /></td>";
				echo "<td>Du musst eine g&uuml;ltige E-Mail-Adresse eingeben. Falls du dein Passwort vergisst, kannst du ein neues an diese Adresse senden lassen.<br/><span id=\"emailStatus\"></span></td></tr>";

				echo "<tr><th>Benutzername:</th>";
				echo "<td>
					<input type=\"text\" id=\"register_user_nick\" name=\"register_user_nick\" maxlength=\"".NICK_MAXLENGHT."\" size=\"".NICK_MAXLENGHT."\" value=\"".$userNick."\" autocomplete=\"off\" /></td>";
				echo "<td>Mit diesem Name tritts du im Spiel als der Herrscher deines Volkes auf. <b>Der Nickname ist endgültig und kann nicht geändert werden!</b><br/><span id=\"nickStatus\"></span></td></tr>";

				echo "<tr><th>Passwort:</th>";
				echo "<td>
					<input type=\"password\" id=\"register_user_password\" name=\"register_user_password\" size=\"20\" value=\"".$userPassword."\" autocomplete=\"off\" /></td>";
				echo "<td>Wähle ein sicheres Passwort damit niemand unbefugt in deinen Account einloggen kann.</b><br/><span id=\"passwordStatus\"></span></td></tr>";
				?>
				
				<tr><td colspan="3"><br/>
				&nbsp; <input type="checkbox" name="agbread" id="agbread" value="1" />
				<label for="agbread">Ich akzeptiere die <a href="javascript:;" onclick="window.open('<?=RULES_URL?>');" >Regeln</a>
				sowie die <a href="javascript:;" onclick="window.open('<?=PRIVACY_URL?>');" >Datenschutzerklärung</a></label>
				<br/>
				<ul style="text-align:left;margin-left:30px">
					<li>Pro Person darf nur 1 Account verwendet werden. Multis werden rigoros gesperrt</a>!</li>
					<li>Nach der Registration wird ein automatisch generiertes Passwort an die angegebene E-Mail-Adresse gesendet.</li>
					<li>Der Name und die E-Mail-Adresse können nur von den Game-Administratoren eingesehen werden und werden nicht weitergegeben.</li>
				</ul>
				</td></tr>
				</table>
				<input type="submit" id="register_submit" disabled="disabled" name="register_submit" value="Anmelden!" /> &nbsp; 
				<a href="?index=login">Zurück zum Login</a>
			</form>

			<script type="text/javascript">
				$(function(){
					$('#register_user_name').focus();
					
					$('#register_user_name').keyup(function(){
						if (this.value) {
							xajax_registerCheckName(this.value);
						}
					});
					
					$('#register_user_email').keyup(function(){
						if (this.value) {
							xajax_registerCheckEmail(this.value);
						}
					});
					
					$('#register_user_nick').keyup(function(){
						if (this.value) {
							xajax_registerCheckNick(this.value);
						}
					});
					
					$('#register_user_password').keyup(function(){
						if (this.value) {
							xajax_registerCheckPassword(this.value);
						}
					});
					
					$('#agbread').click(function(){
						if (this.checked) 
							$('#register_submit').prop('disabled', false); 
						else 
							$('#register_submit').prop('disabled', true); 
					});
				});
			</script>			
			<?PHP
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
		
		try
		{
			$newUser = User::register(
				$_POST['register_user_name'],
				$_POST['register_user_email'],
				$_POST['register_user_nick'],
				$_POST['register_user_password']
			);
			add_log(3,"Der Benutzer ".$newUser->nick." (".$newUser->realName.", ".$newUser->email.") hat sich registriert!");
		
			$verificationRequired = Config::getInstance()->email_verification_required->v;
			if ($verificationRequired) {
				$newUser->setVerified(false);
				$verificationUrl = Config::getInstance()->roundurl.'/show.php?index=verifymail&key='.$newUser->verificationKey;
			} else {
				$newUser->setVerified(true);
			}
		
			$email_text = "Hallo ".$newUser->nick."\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda für die ".Config::getInstance()->roundname->v." registriert.\nHier nochmals deine Daten:\n\n";
			$email_text.= "Name: ".$newUser->realName."\n";
			$email_text.= "E-Mail: ".$newUser->email."\n";
			$email_text.= "Nick: ".$newUser->nick."\n\n";
			if ($verificationRequired) {
				$email_text.= "Klicke auf den folgenden Link um deine E-Mail Adresse zu bestätigen\n\n";
				$email_text.= $verificationUrl."\n\n";
			}
			$email_text.= "WICHTIG: Gib dein Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser unserer Loginseite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!\n";
			$email_text.= "Desweiteren solltest du dich mit den Regeln (".RULES_URL.") bekannt machen, da ein Regelverstoss eine (zeitweilige) Sperrung deines Accounts zur Folge haben kann!\n\n";
			$email_text.= "Viel Spass beim Spielen!\nDas EtoA-Team";

			$mail = new Mail("Account-Registrierung", $email_text);
			$mail->send($newUser->email);
		
			iBoxStart("Registration erfolgreich!");
			echo "Es wurde eine Bestätigungsnachricht an <b>".$_POST['register_user_email']."</b> verschickt.";
			if ($verificationRequired) {
				echo " Klicke auf den Link in der Nachricht um deinen Account zu bestätigen!";
			}
			echo "<br/><br/>Solltest du innerhalb der n&auml;chsten 5 Minuten keine E-Mail erhalten, pr&uuml;fe zun&auml;chst dein Spam-Verzeichnis.<br/><br/>
			Melde dich bei einem <a href=\"?index=contact\">Admin</a>, falls du keine E-Mail erh&auml;ltst oder andere Anmeldeprobleme auftreten.";
			iBoxEnd();
			
			echo button("Zum Login", getLoginUrl());
			echo "</div><br style=\"clear:both;\" /></div>";
			$_SESSION['REGISTER']=Null;
		}
		catch (Exception $e)
		{
			err_msg("Die Registration hat leider nicht geklappt: ".$e->getMessage());
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
