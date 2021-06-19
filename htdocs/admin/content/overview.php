<?PHP

use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Support\DatabaseManagerRepository;
use EtoA\Universe\CellRepository;
use League\CommonMark\CommonMarkConverter;

if ($sub == "offline") {
	takeOffline();
} elseif ($sub == "stats") {
	require("home/stats.inc.php");
} elseif ($sub === "gamestats") {
	gameStatsView();
} elseif ($sub === "changelog") {
	$markdown = $app['etoa.util.markdown'];
	changelogView($markdown);
} elseif ($sub == "adminlog") {
	$sessionRepository = $app['etoa.admin.session.repository'];
	$adminUserRepo = $app['etoa.admin.user.repository'];
	$sessionManager = $app['etoa.admin.session.manager'];
	if (isset($_POST['logshow']) && $_POST['logshow'] != "") {
		adminSessionLogForUserView($s, $sessionRepository, $adminUserRepo);
	} else {
		adminSessionLogView($cu, $sessionRepository, $sessionManager);
	}
} elseif ($sub == "adminusers") {
	require("home/adminusers.inc.php");
} elseif ($sub == "observed") {
	require("home/observed.inc.php");
} elseif ($sub === "sysinfo") {
	$databaseManager = $app['etoa.db.manager.repository'];
	systemInfoView($databaseManager);
} else {
	$universeCellRepo = $app['etoa.universe.cell.repository'];
	$ticketRepo = $app['etoa.help.ticket.repository'];
	indexView($cu, $universeCellRepo, $ticketRepo);
}

function takeOffline()
{
	global $cfg;
	global $sub;
	global $page;

	echo "<h1>Spiel offline nehmen</h1>";

	if (isset($_GET['off']) && $_GET['off'] == 1) {
		$cfg->set('offline', 1);
	}
	if (isset($_GET['on']) && $_GET['on'] == 1) {
		$cfg->set('offline', 0);
	}

	if (isset($_POST['save'])) {
		$cfg->set('offline_ips_allow', $_POST['offline_ips_allow']);
		$cfg->set('offline_message', $_POST['offline_message']);
	}

	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	if ($cfg->get('offline') == 1) {
		echo "<span style=\"color:#f90;\">Das Spiel ist offline!</span><br/><br/>
		Erlaubte IP's (deine ist " . $_SERVER['REMOTE_ADDR'] . "):<br/> <textarea name=\"offline_ips_allow\" rows=\"6\" cols=\"60\">" . $cfg->offline_ips_allow->v . "</textarea><br/>
		Nachricht: <br/><textarea name=\"offline_message\" rows=\"6\" cols=\"60\">" . $cfg->offline_message->v . "</textarea><br/><br/>
		<input type=\"submit\" value=\"Änderungen speichern\" name=\"save\" /> &nbsp;
		<input type=\"button\" value=\"Spiel online stellen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;on=1'\" />";
	} else {
		echo "<span style=\"color:#0f0;\">Das Spiel ist online!</span><br/><br/>
		<input type=\"button\" value=\"Spiel offline nehmen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;off=1'\" />";
	}
	echo "</form>";
}

function gameStatsView()
{
	global $twig;

	echo $twig->render('admin/overview/gamestats.html.twig', [
		'userStats' => file_exists(USERSTATS_OUTFILE) ? USERSTATS_OUTFILE : null,
		'xmlInfo' => file_exists(XML_INFO_FILE) ? XML_INFO_FILE : null,
		'gameStats' => is_file(GAMESTATS_FILE) ? file_get_contents(GAMESTATS_FILE) : null,
	]);
	exit();
}

function changelogView(CommonMarkConverter $markdown)
{
	global $twig;

	$changelogFile = "../../Changelog.md";
	$changelogPublicFile = "../../Changelog_public.md";
	echo $twig->render('admin/overview/changelog.html.twig', [
		'changelog' => is_file($changelogFile) ? $markdown->convertToHtml(file_get_contents($changelogFile)) : null,
		'changelogPublic' => is_file($changelogPublicFile) ? $markdown->convertToHtml(file_get_contents($changelogPublicFile)) : null,
	]);
	exit();
}

function adminSessionLogForUserView(
	AdminSession $s,
	AdminSessionRepository $sessionRepository,
	AdminUserRepository $adminUserRepo
) {
	global $page;
	global $sub;

	$adminUser = $adminUserRepo->find($_POST['user_id']);
	if ($adminUser != null) {
		echo "<h2>Session-Log für " . $adminUser->nick . "</h2>";

		$sessions = $sessionRepository->findSessionLogsByUser($_POST['user_id']);
		if (count($sessions) > 0) {
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
			foreach ($sessions as $arr) {
				echo "<tr>
						<td>" . date("d.m.Y, H:i", $arr['time_login']) . "</td>";
				echo "<td>";
				if ($arr['time_action'] > 0)
					echo date("d.m.Y H:i", $arr['time_action']);
				else
					echo "-";
				echo "</td>";
				echo "<td>";
				if ($arr['time_logout'] > 0)
					echo date("d.m.Y, H:i", $arr['time_logout']);
				else
					echo "-";
				echo "</td>";
				echo "<td>";
				if (max($arr['time_logout'], $arr['time_action']) - $arr['time_login'] > 0) {
					echo tf(max($arr['time_logout'], $arr['time_action']) - $arr['time_login']);
				} else {
					echo "-";
				}
				if ($arr['session_id'] == $s->id) {
					echo " <span style=\"color:#0f0\">aktiv</span>";
				}
				echo "</td>";
				echo "<td title=\"" . Net::getHost($arr['ip_addr']) . "\">" . $arr['ip_addr'] . "</td>";
				$browser = get_browser($arr['user_agent'], true);
				echo "<td title=\"" . $arr['user_agent'] . "\">" . $browser['parent'] . "</td>";
				echo "<td title=\"" . $arr['user_agent'] . "\">" . $browser['platform'] . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		} else
			echo "<i>Keine Einträge vorhanden</i>";
	} else {
		echo "<h2>Fehler</h2><i>User nicht vorhanden</i>";
	}
	echo "<br/><br/><input type=\"button\" value=\"Zur Übersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
}

function adminSessionLogView(
	AdminUser $cu,
	AdminSessionRepository $sessionRepository,
	AdminSessionManager $sessionManager
) {
	global $cfg;
	global $page;
	global $sub;

	echo "<h1>Admin-Log</h1>";

	$logDelTimespan = [
		[1296000, "15 Tage"],
		[2592000, "30 Tage"],
		[3888000, "45 Tage"],
		[5184000, "60 Tage"],
	];

	if (isset($_GET['kick']) && $_GET['kick'] > 0) {
		if ($_GET['kick'] != $cu->id) {
			$sessionManager->kick($_GET['kick']);
			add_log(8, $cu->nick . " löscht die Session des Administrators mit der ID " . $_GET['kick']);
		} else
			echo error_msg("Du kannst nicht dich selbst kicken!");
	}

	if (isset($_POST['delentrys']) && $_POST['delentrys'] != "") {
		if (isset($logDelTimespan[$_POST['log_timestamp']])) {
			$td = $logDelTimespan[$_POST['log_timestamp']][0];
			$nr = $sessionManager->cleanupLogs($td);
			echo "<p>" . $nr . " Einträge wurden gelöscht!</p>";
		}
	}

	echo "<h2>Aktive Sessions</h2>";
	echo "Das Timeout beträgt " . tf($cfg->admin_timeout->v) . "<br/><br/>";

	$sessions = $sessionRepository->findAll();

	if (count($sessions) > 0) {
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
		foreach ($sessions as $arr) {
			$browser = get_browser($arr['user_agent'], true);
			echo "<tr>
					<td " . ($t - $cfg->admin_timeout->v < $arr['time_action'] ? 'style="color:#0f0;">Online' : 'style="color:red;">Timeout') . "</td>
					<td>" . $arr['user_nick'] . "</td>
					<td>" . date("d.m.Y H:i", $arr['time_login']) . "</td>
					<td>" . date("d.m.Y H:i", $arr['time_action']) . "</td>
					<td>" . tf($arr['time_action'] - $arr['time_login']) . "</td>
					<td title=\"" . Net::getHost($arr['ip_addr']) . "\">" . $arr['ip_addr'] . "</td>
					<td title=\"" . $arr['user_agent'] . "\">" . $browser['parent'] . ' on ' . $browser['platform'] . "</td>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;kick=" . $arr['user_id'] . "\">Kick</a></td>
				</tr>";
		}
		echo "</table>";
	} else
		echo "<i>Keine Einträge vorhanden!</i>";

	echo "<h2>Session-Log</h2>";
	$usersWithSessionLogs = $sessionRepository->findUsersWithSessionLogs();
	if (count($usersWithSessionLogs) > 0) {
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "Benutzer wählen: <select name=\"user_id\">";
		foreach ($usersWithSessionLogs as $arr) {
			echo "<option value=\"" . $arr['user_id'] . "\">" . $arr['user_nick'] . " (" . $arr['cnt'] . " Sessions)</option>";
		}
		echo "</select> &nbsp; <input type=\"submit\" name=\"logshow\" value=\"Anzeigen\" /></form>";

		echo "<h2>Logs löschen</h2>";
		echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
		echo "Es sind " . nf($sessionRepository->countSessionLog()) . " Einträge in der Datenbank vorhanden.<br/><br/>
				Einträge löschen die älter als <select name=\"log_timestamp\">";
		foreach ($logDelTimespan as $k => $lts) {
			echo "<option value=\"" . $k . "\">" . $lts[1] . "</option>";
		}
		echo "</select> sind: <input type=\"submit\" name=\"delentrys\" value=\"Ausführen\" /></form>";
	} else {
		echo "<i>Keine Einträge vorhanden</i>";
	}
}

function systemInfoView(DatabaseManagerRepository $databaseManager)
{
	global $twig;

	$unix = UNIX ? posix_uname() : null;
	echo $twig->render('admin/overview/sysinfo.html.twig', [
		'phpVersion' => phpversion(),
		'dbVersion' => $databaseManager->getDatabasePlatform(),
		'webserverVersion' => $_SERVER['SERVER_SOFTWARE'],
		'unixName' => UNIX ? $unix['sysname'] . ' ' . $unix['release'] . ' ' . $unix['version'] : null,
	]);
	exit();
}

function indexView(
	AdminUser $cu,
	CellRepository $universeCellRepo,
	TicketRepository $ticketRepo
) {
	global $conf;
	global $twig;

	// Flottensperre aktiv
	$fleetBanTitle = null;
	$fleetBanText = null;
	if ($conf['flightban']['v'] == 1) {
		// Prüft, ob die Sperre schon abgelaufen ist
		if ($conf['flightban_time']['p1'] <= time() && $conf['flightban_time']['p2'] >= time()) {
			$flightban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Flüge gestartet werden!";
		} elseif ($conf['flightban_time']['p1'] > time() && $conf['flightban_time']['p2'] > time()) {
			$flightban_time_status = "Ausstehend";
		} else {
			$flightban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
		}

		$fleetBanTitle = "Flottensperre aktiviert";
		$fleetBanText = "Die Flottensperre wurde aktiviert.<br><br><b>Status:</b> " . $flightban_time_status . "<br><b>Zeit:</b> " . date("d.m.Y H:i", $conf['flightban_time']['p1']) . " - " . date("d.m.Y H:i", $conf['flightban_time']['p2']) . "<br><b>Grund:</b> " . $conf['flightban']['p1'] . "<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
	}

	// Kampfsperre aktiv
	if ($conf['battleban']['v'] == 1) {
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
	$_SESSION['planets']['query'] = Null;
	$_SESSION['admin']['user_query'] = "";
	$_SESSION['admin']['queries']['alliances'] = "";

	echo $twig->render('admin/overview/overview.html.twig', [
		'welcomeMessage' => 'Hallo <b>' . $cu->nick . '</b>, willkommen im Administrationsmodus! Deine Rolle(n): <b>' . $cu->getRolesStr() . '.</b>',
		'hasTfa' => !empty($cu->tfaSecret),
		'didBigBangHappen' => $universeCellRepo->count() != 0,
		'forcePasswordChange' => $cu->forcePasswordChange,
		'numNewTickets' => $ticketRepo->countNew(),
		'numOpenTickets' => $ticketRepo->countAssigned($cu->id),
		'fleetBanText' => $fleetBanText,
		'fleetBanTitle' => $fleetBanTitle,
		'adminInfo' => getAdminText('admininfo'),
		'systemMessage' => getAdminText('system_message'),
	]);
	exit();
}
