<?PHP	
	// Start PHP output stack
	ob_start();											
	$time = time();

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
				infobox_start("<span style=\"color:#0f0;\">TIPP</span>");
				echo text2html($arr[0]);
				infobox_end();			
			}
			$s['tipp_shown'] = true;
		}
	}
	
	// SYSTEMNACHRICHT //
	if ($conf['system_message']['v']!="")
	{
		echo "<br/>";
		infobox_start("<span style=\"color:red;\">WICHTIGE SYSTEMNACHRICHT</span>");
		echo text2html($conf['system_message']['v']);
		infobox_end();
	}
	
	// Auf Löschung prüfen
	if ($s['user']['deleted'] > 0 &&
	$page != 'contact' &&
	$page != 'userconfig')
	{
		echo '<h1>Dein Account ist zut Löschung vorgeschlagen!</h1>';		
		echo 'Die Löschung erfolgt frühestens um <b>'.df($s['user']['deleted']).'</b>!<br/><br/>
		<input type="button" onclick="document.location=\'?page=userconfig&mode=misc\'" value="Löschung aufheben" /> 
		<input type="button" onclick="document.location=\'?page=contact\'" value="Admin kontaktieren" /> ';
	}
	
	// Auf Sperrung prüfen
	elseif ($uarr['user_blocked_from'] > 0 && 
	$uarr['user_blocked_from'] < $time && 
	$uarr['user_blocked_to'] > $time &&
	$page != 'contact' &&
	$page != 'help')
	{
		echo '<h1>Dein Account ist gesperrt!</h1>
		<b>Grund:</b> '.$uarr["user_ban_reason"].'.<br/>
		<b>Zeitraum:</b> <span style="color:#f90">'.date("d.m.Y, H:i",$uarr["user_blocked_from"]).'</span> 
		bis <span style="color:#0f0">'.date("d.m.Y, H:i",$uarr["user_blocked_to"]).'</span><br/>
		<b>Gesamtdauer der Sperre:</b> '.tf($uarr["user_blocked_to"]-$uarr["user_blocked_from"]).'<br/>
		<b>Dauer:</b> '.tf($uarr["user_blocked_to"]-max(time(),$uarr["user_blocked_from"])).'<br/>';
		$ares = dbquery("
		SELECT
			user_nick,
			user_email
		FROM
			".$db_table['admin_users']."
		WHERE
			user_id='".$uarr['user_ban_admin_id']."'
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
	elseif ($uarr["user_hmode_from"]>0 && 
	$page != 'userconfig' &&
	$page != 'contact' &&
	$page != 'help')
	{
		echo '<h1>Du befindest dich im Urlaubsmodus</h1>
		Der Urlaubsmous dauert bis mindestens: <b>'.date("d.m.Y, H:i",$uarr["user_hmode_to"]).'</b><br/>';
		if($uarr["user_hmode_to"]<time())
		{
		 	echo '<br/><span style="color:#0f0">Die Minimaldauer ist abgelaufen!</span><br/><br/>
		 	<input type="button" onclick="document.location=\'?page=userconfig&mode=misc\'" value="Einstellungen" /><br/>';
		}
		else
		{
			echo 'Zeit bis Deaktivierung möglich ist: <b>'.tf($uarr["user_hmode_to"]-max(time(),$uarr["user_hmode_from"])).'</b><br/>';
		}
		echo '<br/>Solltest du Fragen oder Probleme mit dem Urlaubsmodus haben,<br/>
		dann <a href="?page=contact">melde</a> dich bei einem Game-Administrator.';
	}

	// Seite anzeigen
	else
	{
		
		// Apply choosen itemset
		if (isset($s['itemset_key']) && isset($_POST[md5($s['itemset_key'])]) && isset($_POST['itemset_id']))
		{
			addItemSetListToPlanet($c->id,$s['user']['id'],$_POST['itemset_id']);
			$s['itemset_key']=null;
			$c->update(1);
		}
		
		// Display first time message
		if ($planets->first_time)
		{
			
			$res = dbquery("
			SELECT
				set_id,
				set_name
			FROM
				default_item_sets
			WHERE
				set_active=1
			");
			if (mysql_num_rows($res)>1)
			{
				$k = mt_rand(10000,99999);
				$s['itemset_key']=$k;
				infobox_start("Start-Objekte");
				echo "<form action=\"?\" method=\"post\">";
				checker_init();
				echo "Euch stehen mehrere Vorlagen von Start-Objekte zur Auswahl. Bitte wählt eine Vorlage aus, die darin definierten Objekte
				werden dann eurem Hauptplanet hinzugefügt: <br/><br/><select name=\"itemset_id\">";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<option value=\"".$arr['set_id']."\">".$arr['set_name']."</option>";
				}
				echo "</select> <input type=\"submit\" value=\"Weiter\" name=\"".md5($k)."\" /></form>";
				infobox_end();
			}
			elseif(mysql_num_rows($res)==1)
			{
				$arr = mysql_fetch_array($res);
				addItemSetListToPlanet($c->id,$s['user']['id'],$arr['set_id']);							
			}
			
			$c->update(1);
			
			echo '<br/>';
			infobox_start("Willkommen");
			echo text2html($conf['welcome_message']['v']);
			infobox_end();
			echo '<input type="button" value="Zum Heimatplaneten" onclick="document.location=\'?page=planetoverview\'" />';
			if (!isset($s['allow_planet_change_counter']) || $s['allow_planet_change_counter']==0)
			{
				send_msg($s['user']['id'],USER_MSG_CAT_ID,'Willkommen',$conf['welcome_message']['v']);
			}
			
			// Set marker so that a planet change is allowed
			$s['allow_planet_change']=true;
			
			
			
			
		}
		else
		{
			if (isset($s['allow_planet_change']) && $s['allow_planet_change'])
			{
				infobox_start("Planetenwechsel");
				echo "<div style=\"color:yellow;\">Während deinem ersten Login ist es möglich, deinen 
				Hauptplanet zu wechseln. <b>Beachte dass dafür noch KEINE Gebäude auf dem Planeten gebaut sein dürfen!</b><br/>
				Klicke <a href=\"?page=userconfig&amp;mode=misc\">hier</a> um dies zu tun!</div>";
				infobox_end();
			}
		
			if (eregi('^[a-z\_]+$',$page)  && strlen($page)<=50)
			{
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
	
	// End PHP output stack and send content to browser
	ob_end_flush();												
?>