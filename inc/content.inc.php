<?PHP	
	
	$time = time();

	// Go to user setup page if user wasn't set up correctly
	if (!$cu->isSetup() && $page!="help" && $page!="contact")
	{
		require("inc/usersetup.inc.php");		
	}
	else
	{
		// Show tipps
		if (ENABLE_TIPS==1)
		{
			if (!isset($s['tipp_shown']))
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
				$s['tipp_shown'] = true;
			}
		}
		
		// SYSTEMNACHRICHT //
		if ($cfg->value('system_message')!="")
		{
			echo "<br/>";
			iBoxStart("<span style=\"color:red;\">WICHTIGE SYSTEMNACHRICHT</span>");
			echo text2html($cfg->value('system_message'));
			iBoxEnd();
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
		$page != 'contact' &&
		$page != 'help')
		{
			echo '<h1>Du befindest dich im Urlaubsmodus</h1>
			Der Urlaubsmous dauert bis mindestens: <b>'.date("d.m.Y, H:i",$cu->hmode_to).'</b><br/>';
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
						if ($k=="login_pw")
							$post.="[b]".$k.":[/b] ******* (hidden by system)\n";
						else
							$post.="[b]".$k.":[/b] ".$v."\n";
					}
				}
				
				dbquery("INSERT INTO
					user_surveillance
				(
					timestamp,
					user_id,
					page,
					request,
					post,
					session
				)
				VALUES
				(
					".time().",
					".$cu->id.",
					'".$page."',
					'".$req."',
					'".$post."',
					'".$s['key']."'
				)");
			}

			// Cheating-Schutz für externe Formulare
			// Todo: I this works properly, ban user immediately
			/*if ($page!=DEFAULT_PAGE && !isset($_SERVER["HTTP_REFERER"]) && count($_POST)>0)
			{
				echo "<h1>Cheat-Schutz</h1> Du hast anscheinend versucht, über ein externes Formular eine Aktion durchzuführen. 
				Dies ist nicht erlaubt!<br/><br/>";
				
				$str = "Der Spieler ".$cu->nick." (".$s['user_id'].") hat versucht, über ein externes Formular auf das Spiel zuzugreifen!\n";
				$str.= "IP-Adresse: ".$_SERVER['REMOTE_ADDR']."\n";
				$str.= "Host: ".resolveIp($_SERVER['REMOTE_ADDR'])."";
				$str.="\nSeite: $page\n";
				$str.="Inhalt des POST-Arrays:\n";
				foreach ($_POST as $k => $v)
				{
					$str.="$k: $v\n";
				}
				$str.="\nInhalt des GET-Arrays:\n";
				foreach ($_GET as $k => $v)
				{
					$str.="$k: $v\n";
				}		
				$str.="\nBrowser: ".$_SERVER["HTTP_USER_AGENT"];	
				add_log(3,$str);
				$s=Null;
			}*/
			if (true)
			{
				if (eregi('^[a-z\_]+$',$page)  && strlen($page)<=50)
				{
					// DEBUG
					$query_counter=0; 
					$queries=array();
					
					// Content includen
					if (!include("content/".$page.".php"))
					{
						echo '<h1>Fehler</h1>
						Die Seite <b>'.$page.'</b> existiert nicht!<br/><br/>
						<input type="button" onclick="history.back();" value="Zurück" />';
					}
				}
				else
				{
					echo '<h1>Fehler</h1>
					Der Seitenname <b>'.$page.'</b> enth&auml;lt unerlaubte Zeichen!<br/><br/>
					<input type="button" onclick="history.back();" value="Zurück" />';
				}
			}
		}
	}	
	
?>