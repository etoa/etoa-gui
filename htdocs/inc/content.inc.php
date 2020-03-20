<?PHP

	$time = time();

	// Get tutorial
	$ttm = new TutorialManager();
	if (!$ttm->hasReadTutorial($cu->id, 1)) {
		$twig->addGlobal('tutorial_id', 1);
	}
	else if ($cu->isSetup() && !$ttm->hasReadTutorial($cu->id, 2)) {
        $twig->addGlobal('tutorial_id', 2);
	} elseif ($cu->isSetup() && $ttm->hasReadTutorial($cu->id, 2) && $app['etoa.quests.enabled']) {
        $app['cubicle.quests.initializer']->initialize($cu->id);
	}

	// Go to user setup page if user wasn't set up correctly
	if (!$cu->isSetup() && $page!="help" && $page!="contact")
	{
		require("inc/usersetup.inc.php");
	}
	else
	{
		// Show tipps
		if (ENABLE_TIPS==1 && $s->firstView)
		{
			$res = dbquery("
			SELECT
				tip_text
			FROM
				tips
			WHERE
				tip_active=1
			ORDER BY
				RAND()
			LIMIT 1;
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<br/>";
				iBoxStart("<span style=\"color:#0f0;\">TIPP</span>");
				echo text2html($arr[0]);
				iBoxEnd();
			}
		}

		$tm = new TextManager();

		// SYSTEMNACHRICHT //
		$systemMessage = $tm->getText('system_message');
		if ($systemMessage->enabled && !empty($systemMessage->content))
		{
			echo "<br />";
			iBoxStart("<span style=\"color:red;\">WICHTIGE SYSTEMNACHRICHT</span>");
			echo text2html($systemMessage->content);
			iBoxEnd();
		}

		//Eventhandler //
		$backendStatus = RuntimeDataStore::get('backend_status');
		if ($backendStatus != null && $backendStatus == 0)
		{
			$infoText = $tm->getText('backend_offline_message');
			if ($infoText->enabled && !empty($infoText->content))
			{
				echo "<br />";
				iBoxStart("<span style=\"color:red;\">UPDATEDIENST</span>");
				echo text2html($infoText->content);
				iBoxEnd();
			}
		}

		// E-Mail verification
		if (!$cu->isVerified)
		{
			if (isset($_GET['resendverificationmail'])) {
				$verificationUrl = Config::getInstance()->roundurl.'/show.php?index=verifymail&key='.$cu->verificationKey;
				$email_text = "Hallo ".$cu->nick."\n\n";
				$email_text.= "Damit du alle Funktionen von Escape to Andromeda benutzen kannst muss deine E-Mail Adresse verifiziert werden. Bitte klicke auf den folgenden Link, um die Verifikation für die ".Config::getInstance()->roundname->v." abzuschliessen:\n\n";
				$email_text.= $verificationUrl."\n\n";
				$email_text.= "Viel Spass beim Spielen!\nDas EtoA-Team";
				$mail = new Mail("Account-Bestätigung", $email_text);
				$mail->send($cu->email);
				success_msg("Bestätigungsmail wurde gesendet!");
			}
			else
			{
				iBoxStart("Verifikation erforderlich");
				echo "Deine E-Mailadresse <b>".$cu->email."</b> muss bestätigt werden, damit du alle Funktionen benutzen kannst!<br/><br/>";
				echo '<a href="?page='.$page.'&resendverificationmail">Bestätigungsmail nochmals versenden</a>';
				iBoxEnd();
			}
		}

		// Auf Löschung prüfen
		if ($cu->deleted > 0 &&
		$page != 'contact' &&
		$page != 'userconfig')
		{
			echo '<h1>Dein Account ist zut Löschung vorgeschlagen!</h1>';
			echo 'Die Löschung erfolgt frühestens um <b>'.df($cu->deleted).'</b>!<br/><br/>
			<input type="button" onclick="document.location=\'?page=userconfig&mode=misc\'" value="Löschung aufheben" /> 
			<input type="button" onclick="document.location=\'?page=contact\'" value="Admin kontaktieren" /> ';
		}

		// Auf Sperrung prüfen
		elseif ($cu->blocked_from > 0 &&
		$cu->blocked_from < $time &&
		$cu->blocked_to > $time &&
		$page != 'contact' &&
		$page != 'help')
		{
			echo '<h1>Dein Account ist gesperrt!</h1>
			<b>Grund:</b> '.$cu->ban_reason.'.<br/>
			<b>Zeitraum:</b> <span style="color:#f90">'.date("d.m.Y, H:i",$cu->blocked_from).'</span> 
			bis <span style="color:#0f0">'.date("d.m.Y, H:i",$cu->blocked_to).'</span><br/>
			<b>Gesamtdauer der Sperre:</b> '.tf($cu->blocked_to-$cu->blocked_from).'<br/>
			<b>Dauer:</b> '.tf($cu->blocked_to-max(time(),$cu->blocked_from)).'<br/>';
			$ares = dbquery("
			SELECT
				user_nick,
				user_email
			FROM
				admin_users
			WHERE
				user_id='".$cu->ban_admin_id."'
			;");
			if (mysql_num_rows($ares)>0)
			{
				$aarr=mysql_fetch_row($ares);
				echo '<b>Gesperrt von:</b> '.$aarr[0].', <a href="mailto:'.$aarr[1].'">'.$aarr[1].'</a><br/>';
			}
			echo '<br/>Solltest du Fragen zu dieser Sperrung haben oder dich ungerecht behandelt fühlen,<br/>
			dann <a href="?page=contact">melde</a> dich bei einem Game-Administrator.';
		}

		// Aus Urlaub prüfen
		elseif ($cu->hmode_from>0 &&
		$page != 'userconfig' &&
		$page != 'messages' &&
		$page != 'stats' &&
		$page != 'townhall' &&
		$page != 'buddylist' &&
		$page != 'userinfo' &&
		$page != 'contact' &&
		$page != 'help')
		{
			echo '<h1>Du befindest dich im Urlaubsmodus</h1>
			Der Urlaubsmodus dauert bis mindestens: <b>'.date("d.m.Y, H:i",$cu->hmode_to).'</b><br/>';
			if($cu->hmode_to < time())
			{
			 	echo '<br/><span style="color:#0f0">Die Minimaldauer ist abgelaufen!</span><br/><br/>
			 	<input type="button" onclick="document.location=\'?page=userconfig&mode=misc\'" value="Einstellungen" /><br/>';
			}
			else
			{
				echo 'Zeit bis Deaktivierung möglich ist: <b>'.tf($cu->hmode_to-max(time(),$cu->hmode_from)).'</b><br/>';
			}
			echo '<br/>Solltest du Fragen oder Probleme mit dem Urlaubsmodus haben,<br/>
			dann <a href="?page=contact">melde</a> dich bei einem Game-Administrator.';
		}


		elseif ($s->sittingActive && $s->falseSitter && $page!="userconfig")
		{
			echo '<h1>Sitting ist aktiv</h1>
			Dein Account wird gesitted bis <b>'.df($s->sittingUntil).'</b><br/><br/>';
			echo button("Einstellungen","?page=userconfig&mode=sitting");

		}

		// Seite anzeigen
		else
		{
			// 1984
			if ($cu->monitored)
			{
				$req = "";
				foreach ($_GET as $k=>$v)
				{
					if ($k!="page")
					{
						$req.="[b]".$k.":[/b] ".$v."\n";
					}
				}
				$post = "";
				foreach ($_POST as $k=>$v)
				{
					if (is_array($v))
					{
						$post.="[b]".$k.":[/b] ".dump($v,1);
					}
					else
					{
						if ($k==$s->passwordField)
							$post.="[b]".$k.":[/b] *******\n";
						else
							$post.="[b]".$k.":[/b] ".$v."\n";
					}
				}

				dbQuerySave("INSERT DELAYED INTO
					user_surveillance
				(
					timestamp,
					user_id,
					page,
					request,
					request_raw,
					post,
					session
				)
				VALUES
				(
					UNIX_TIMESTAMP(),
					?, ?, ?, ?, ?, ?
				)", array($cu->id, $page, $req, $_SERVER['QUERY_STRING'], $post, $s->id));
			}

			// Change display mode (full/small) if requested
			if (isset($_GET['change_display_mode'])) {
				if ($_GET['change_display_mode'] == 'small') {
					$cu->properties->itemShow = 'small';
				}
				elseif ($_GET['change_display_mode'] == 'full') {
					$cu->properties->itemShow = 'full';
				}
				forward("?page=$page");
			}

			if (true)
			{
				if (preg_match('/^[a-z\_]+$/',$page)  && strlen($page)<=50)
				{
					// DEBUG
					$query_counter=0;
					$queries=array();

					// Content includen
					$contentFile = "content/".$page.".php";
					if (!file_exists($contentFile) || !include($contentFile))
					{
						echo '<h1>Fehler</h1>
						Die Seite <b>'.$page.'</b> existiert nicht!<br/><br/>
						<input type="button" onclick="history.back();" value="Zurück" />';
					}

					if (isset($_GET['sub']))
						$lasub = $_GET['sub'];
                                        elseif (isset($_GET['action']))
	                                        $lasub = $_GET['action'];
                                        elseif (isset($_GET['site']))
	                                        $lasub = $_GET['site'];
					else
						$lasub="";

					logAccess($page,"ingame",$lasub);
				}
				else
				{
					echo '<h1>Fehler</h1>
					Der Seitenname enth&auml;lt unerlaubte Zeichen!<br/><br/>
					<input type="button" onclick="history.back();" value="Zurück" />';
				}
			}
		}
	}

    if ($app['etoa.quests.enabled']) {
        $twig->addGlobal('quests', array_values($app['etoa.quest.responselistener']->getQuests()));
    }
