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
	// 	Dateiname: home.php
	// 	Topic: Willkommensseite der Administration
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Offline
	//
	if ($sub=="offline")
	{
		echo "<h1>Spiel offline nehmen</h1>";

		if (isset($_GET['off']) && $_GET['off']==1)
		{
			$cfg->set('offline',1);
		}
		if (isset($_GET['on']) && $_GET['on']==1)
		{
			$cfg->set('offline',0);
		}

		if (isset($_POST['save']))
		{
			$cfg->set('offline_ips_allow', $_POST['offline_ips_allow']);
			$cfg->set('offline_message', $_POST['offline_message']);
		}

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		if ($cfg->get('offline')==1)
		{
			echo "<span style=\"color:#f90;\">Das Spiel ist offline!</span><br/><br/>
			Erlaubte IP's (deine ist ".$_SERVER['REMOTE_ADDR']."):<br/> <textarea name=\"offline_ips_allow\" rows=\"6\" cols=\"60\">".$cfg->offline_ips_allow->v."</textarea><br/>
			Nachricht: <br/><textarea name=\"offline_message\" rows=\"6\" cols=\"60\">".$cfg->offline_message->v."</textarea><br/><br/>
			<input type=\"submit\" value=\"&Auml;nderungen speichern\" name=\"save\" /> &nbsp; 
			<input type=\"button\" value=\"Spiel online stellen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;on=1'\" />";

		}
		else
		{
			echo "<span style=\"color:#0f0;\">Das Spiel ist online!</span><br/><br/>
			<input type=\"button\" value=\"Spiel offline nehmen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;off=1'\" />";
		}
		echo "</form>";
	}


	//
	// Rangliste
	//
	elseif ($sub=="stats")
	{
		require("home/stats.inc.php");
	}

	//
	// Statistiken
	//
	elseif ($sub === "gamestats") {
        echo $twig->render('admin/overview/gamestats.html.twig', [
            'userStats' => file_exists(USERSTATS_OUTFILE) ? USERSTATS_OUTFILE : null,
            'xmlInfo' => file_exists(XML_INFO_FILE) ? XML_INFO_FILE : null,
            'gameStats' => is_file(GAMESTATS_FILE) ? file_get_contents(GAMESTATS_FILE) : null,
        ]);
        exit();
	}

	//
	// Changelog
	//
	elseif ($sub === "changelog") {
		$Parsedown = new Parsedown();
		$changelogFile = "../../Changelog.md";
		$changelogPublicFile = "../../Changelog_public.md";
        echo $twig->render('admin/overview/changelog.html.twig', [
            'changelog' => is_file($changelogFile) ? $Parsedown->text(file_get_contents($changelogFile)) : null,
            'changelogPublic' => is_file($changelogPublicFile) ? $Parsedown->text(file_get_contents($changelogPublicFile)) : null,
        ]);
        exit();
	}

	//
	// Admin Session-Log
	//
	elseif ($sub=="adminlog")
	{
		echo "<h1>Admin-Log</h1>";

		if (isset($_POST['logshow']) && $_POST['logshow']!="")
		{
			$ures=dbquery("SELECT
				user_nick
				FROM admin_users
				WHERE user_id=".$_POST['user_id'].";");
			if (mysql_num_rows($ures)>0)
			{
				$uarr=mysql_fetch_array($ures);
				echo "<h2>Session-Log f&uuml;r ".$uarr['user_nick']."</h2>";

				$sql = "SELECT
					l.*,
					u.user_nick
				FROM
					admin_user_sessionlog l
				INNER JOIN
					admin_users u
					ON l.user_id=u.user_id
					AND l.user_id=".$_POST['user_id']."
				ORDER BY
					time_action DESC;";
				$res=dbquery($sql);
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">
					<tr>
						<th>Login</th>
						<th>Aktivität</th>
						<th>Logout</th>
						<th>Dauer</th>
						<th>IP</th>
						<th>Browser</th>
						<th>OS</th>
					</tr>";
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr>
							<td>".date("d.m.Y, H:i",$arr['time_login'])."</td>";
						echo "<td>";
						if ($arr['time_action']>0)
							echo date("d.m.Y H:i",$arr['time_action']);
						else
							echo "-";
						echo "</td>";
						echo "<td>";
						if ($arr['time_logout']>0)
							echo date("d.m.Y, H:i",$arr['time_logout']);
						else
							echo "-";
						echo "</td>";
						echo "<td>";
						if (max($arr['time_logout'],$arr['time_action'])-$arr['time_login']>0)
							echo tf(max($arr['time_logout'],$arr['time_action'])-$arr['time_login']);
						else
							echo "-";
						if ($arr['log_session_key']==$s->id)
							echo " <span style=\"color:#0f0\">aktiv</span>";
						echo "</td>";
						echo "<td title=\"".Net::getHost($arr['ip_addr'])."\">".$arr['ip_addr']."</td>";
						$browser = get_browser($arr['user_agent'], true);
						echo "<td title=\"".$arr['user_agent']."\">".$browser['parent']."</td>";
						echo "<td title=\"".$arr['user_agent']."\">".$browser['platform']."</td>";
						echo "</tr>";
					}
					echo "</table>";
				}
				else
					echo "<i>Keine Eintr&auml;ge vorhanden</i>";

			}
			else
			{
				echo "<h2>Fehler</h2><i>User nicht vorhanden</i>";
			}
			echo "<br/><br/><input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
		}
		else
		{
			$logDelTimespan = array(
				array(1296000,"15 Tage"),
				array(2592000,"30 Tage"),
				array(3888000,"45 Tage"),
				array(5184000,"60 Tage"),
			);

			if (isset($_GET['kick']) && $_GET['kick']>0)
			{
				if ($_GET['kick']!=$cu->id)
				{
					AdminSession::kick($_GET['kick']);
					add_log(8,$cu->nick." l&ouml;scht die Session des Administrators mit der ID ".$_GET['kick'],time());
				}
				else
					echo error_msg("Du kannst nicht dich selbst kicken!");
			}

			if (isset($_POST['delentrys']) && $_POST['delentrys']!="")
			{
				if (isset($logDelTimespan[$_POST['log_timestamp']]))
				{
					$td = $logDelTimespan[$_POST['log_timestamp']][0];
					$nr = AdminSession::cleanupLogs($td);
					echo "<p>".$nr." Eintr&auml;ge wurden gel&ouml;scht!</p>";
				}
			}

			echo "<h2>Aktive Sessions</h2>";
			echo "Das Timeout betr&auml;gt ".tf($cfg->admin_timeout->v)."<br/><br/>";

			$res=dbquery("
				SELECT 
					s.user_id,
					s.ip_addr,
					s.user_agent,
					s.time_login,
					s.time_action,
					u.user_nick
				FROM
					admin_user_sessions s
				INNER JOIN 
					admin_users u
					ON s.user_id=u.user_id
				ORDER BY
					time_action DESC;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">
				<tr>
					<th>Status</th>
					<th>Nick</th>
					<th>Login</th>
					<th>Aktivität</th>
					<th>Dauer</th>
					<th>IP</th>
					<th>User Agent</th>
					<th>Kicken</th>
				</tr>";
				$t = time();
				while ($arr=mysql_fetch_array($res))
				{
					if (ini_get("browscap") != null)
					{
						$bc = get_browser($arr['user_agent'], true);
						$browser = $bc['parent'].' ['.$bc['platform'].']';
					} else {
						$browser = $arr['user_agent'];
					}
					echo "<tr>
						<td ".($t - $cfg->admin_timeout->v < $arr['time_action'] ? 'style="color:#0f0;">Online': 'style="color:red;">Timeout')."</td>
						<td>".$arr['user_nick']."</td>
						<td>".date("d.m.Y H:i",$arr['time_login'])."</td>
						<td>".date("d.m.Y H:i",$arr['time_action'])."</td>
						<td>".tf($arr['time_action']-$arr['time_login'])."</td>
						<td title=\"".Net::getHost($arr['ip_addr'])."\">".$arr['ip_addr']."</td>
						<td title=\"".$arr['user_agent']."\">".$browser."</td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['user_id']."\">Kick</a></td>
					</tr>";
				}
				echo "</table>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden!</i>";

			echo "<h2>Session-Log</h2>";
			$res=dbquery("SELECT 
				user_nick,
				u.user_id,
				COUNT(*) as cnt
			FROM admin_users u
			INNER JOIN
				admin_user_sessionlog l
				ON l.user_id=u.user_id
			GROUP BY u.user_id ORDER BY u.user_nick;");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "Benutzer w&auml;hlen: <select name=\"user_id\">";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']." (".$arr['cnt']." Sessions)</option>";
				}
				echo "</select> &nbsp; <input type=\"submit\" name=\"logshow\" value=\"Anzeigen\" /></form>";

				$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM admin_user_sessionlog;"));

				echo "<h2>Logs löschen</h2>";
				echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
				echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>
					Eintr&auml;ge l&ouml;schen die &auml;lter als <select name=\"log_timestamp\">";
				foreach ($logDelTimespan as $k => $lts)
				{
					echo "<option value=\"".$k."\">".$lts[1]."</option>";
				}
				echo "</select> sind: <input type=\"submit\" name=\"delentrys\" value=\"Ausf&uuml;hren\" /></form>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden</i>";
		}
	}

	//
	// User bearbeiten
	//
	elseif ($sub=="adminusers")
	{
		require("home/adminusers.inc.php");
	}

	//
	// User beobachten
	//
	elseif ($sub=="observed")
	{
		require("home/observed.inc.php");
	}

	elseif ($sub === "sysinfo") {
		$unix = UNIX ? posix_uname() : null;
		echo $twig->render('admin/overview/sysinfo.html.twig', [
			'phpVersion' => phpversion(),
			'dbVersion' => mysql_get_client_info(),
			'webserverVersion' => $_SERVER['SERVER_SOFTWARE'],
			'unixName' => UNIX ? $unix['sysname'] . ' ' . $unix['release'] . ' ' . $unix['version'] : null,
		]);
		exit();
	}

	//
	// Übersicht
	//
	else {
		//
		// Universum generieren
		//
		$res = dbquery("SELECT COUNT(id) FROM cells;");
		$arr = mysql_fetch_row($res);

		// Flottensperre aktiv
		$fleetBanTitle = null;
		$fleetBanText = null;
		if ($conf['flightban']['v']==1) {
			// Prüft, ob die Sperre schon abgelaufen ist
			if($conf['flightban_time']['p1'] <= time() && $conf['flightban_time']['p2'] >= time()) {
				$flightban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Flüge gestartet werden!";
			} elseif($conf['flightban_time']['p1'] > time() && $conf['flightban_time']['p2'] > time()) {
				$flightban_time_status = "Ausstehend";
			} else {
				$flightban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
			}

			$fleetBanTitle = "Flottensperre aktiviert";
			$fleetBanText = "Die Flottensperre wurde aktiviert.<br><br><b>Status:</b> ".$flightban_time_status."<br><b>Zeit:</b> ".date("d.m.Y H:i",$conf['flightban_time']['p1'])." - ".date("d.m.Y H:i",$conf['flightban_time']['p2'])."<br><b>Grund:</b> ".$conf['flightban']['p1']."<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
		}

		// Kampfsperre aktiv
		if ($conf['battleban']['v']==1) {
			// Prüft, ob die Sperre schon abgelaufen ist
			if ($conf['battleban_time']['p1'] <= time() && $conf['battleban_time']['p2'] >= time()) {
				$battleban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Angriffe geflogen werden!";
			} elseif ($conf['battleban_time']['p1'] > time() && $conf['battleban_time']['p2'] > time()) {
				$battleban_time_status = "Ausstehend";
			} else {
				$battleban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
			}

			$fleetBanTitle = "Kampfsperre aktiviert";
			$fleetBanText = "Die Kampfsperre wurde aktiviert.<br><br><b>Status:</b> " . $battleban_time_status . "<br><b>Zeit:</b> " . date("d.m.Y H:i", $conf['battleban_time']['p1']) . " - " . date("d.m.Y H:i", $conf['battleban_time']['p2']) . "<br><b>Grund:</b> " . $conf['battleban']['p1'] . "<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
		}

		//
		// Schnellsuche
		//
		$_SESSION['planets']['query']=Null;
		$_SESSION['admin']['user_query']="";
		$_SESSION['admin']['queries']['alliances']="";

		echo $twig->render('admin/overview/overview.html.twig', [
			'welcomeMessage' => 'Hallo <b>' .$cu->nick. '</b>, willkommen im Administrationsmodus! Deine Rolle(n): <b>' . $cu->getRolesStr() . '.</b>',
			'hasTfa' => !empty($cu->tfaSecret),
			'didBigBangHappen' => $arr[0]!=0,
			'forcePasswordChange' => $cu->forcePasswordChange,
			'numNewTickets' => Ticket::countNew(),
			'numOpenTickets' => Ticket::countAssigned($cu->id),
			'fleetBanText' => $fleetBanText,
			'fleetBanTitle' => $fleetBanTitle,
		]);
		exit();
	}
