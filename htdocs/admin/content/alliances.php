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
	// 	Dateiname: alliances.php
	// 	Topic: Allianz-Verwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Bilder prüfen test by lambo test by nicu
	//

use Pimple\Container;

	if ($sub=="imagecheck")
	{
		$dir = ALLIANCE_IMG_DIR."/";
		echo "<h1>Allianz-Bilder prüfen</h1>";

		//
		// Check submit
		//
		if (isset($_POST['validate_submit']))
		{
			foreach ($_POST['validate'] as $id=>$v) {
				if ($v == 0) {
					if (removeAlliancePicture($app, $id)) {
						echo "Bild entfernt!<br/><br/>";
					}
				} else {
					markAlliancePictureChecked($app, $id);
				}
			}
		}

		//
		// Check new images
		//
		echo "<h2>Noch nicht verifizierte Bilder</h2>";
		echo "Diese Bilder gehören zu aktiven Allianzen. Bitte prüfe regelmässig, ob sie nicht gegen unsere Regeln verstossen!<br/>";
		$data = fetchAlliancesWithUncheckedPictures($app);
		if (count($data) > 0)
		{
			echo "Es sind ".count($data)." Bilder gespeichert!<br/><br/>";
			echo "<form action=\"\" method=\"post\">
			<table class=\"tb\"><tr><th>User</th><th>Fehler</th><th>Aktionen</th></tr>";
			foreach ($data as $arr)
			{
				echo "<tr><td>[".$arr['alliance_tag']."] ".$arr['alliance_name']."</td><td>";
				if (file_exists($dir.$arr['alliance_img']))
				{
					echo '<img src="'.$dir.$arr['alliance_img'].'" alt="Profil" />';
				}
				else
				{
					echo '<span style=\"color:red\">Bild existiert nicht!</span>';
				}
				echo "</td><td>
				<input type=\"radio\" name=\"validate[".$arr['alliance_id']."]\" value=\"1\" checked=\"checked\"> Bild ist in Ordnung<br/>
				<input type=\"radio\" name=\"validate[".$arr['alliance_id']."]\" value=\"0\" > Bild verstösst gegen die Regeln. Lösche es!<br/>
				</td></tr>";
			}
			echo "</table><br/>
			<input type=\"submit\" name=\"validate_submit\" value=\"Speichern\" /></form>";
		}
		else
		{
			echo "<br/><i>Keine Bilder vorhanden!</i>";
		}

		//
		// Orphans
		//
		$data = fetchAlliancesWithPictures($app);
		$nr = count($data);
		$paths = array();
		$nicks = array();
		if ($nr > 0)
		{
			foreach ($data as $arr)
			{
				$paths[$arr['alliance_id']] = $arr['alliance_img'];
				$nicks[$arr['alliance_id']] = $arr['alliance_name'];
			}
		}
		$files = array();
		if (is_dir($dir)) {
			$d = opendir($dir);
			while ($f = readdir($d))
			{
				if (is_file($dir.$f))
				{
					array_push($files,$f);
				}
			}
			closedir($d);
		}

		$overhead = array();
		while(count($files)>0)
		{
			$k = array_pop($files);
			if (!in_array($k,$paths))
				array_push($overhead,$k);
		}

		if (isset($_GET['action']) && $_GET['action']=="clearoverhead")
		{
			while(count($overhead)>0)
			{
				unlink($dir.array_pop($overhead));
			}
			echo "Verwaiste Bilder gelöscht!<br/><bt/>";
		}
		$co = count($overhead);

		echo "<h2>Verwaiste Bilder</h2>";
		if ($co>0)
		{
				echo "Diese Bilder gehören zu Allianzen, die nicht mehr in unserer Datenbank vorhanden sind.<br/>
				Es sind $co Bilder vorhanden. <a href=\"?page=$page&amp;sub=$sub&amp;action=clearoverhead\">Lösche alle verwaisten Bilder</a><br/><br/>";
				echo "<table class=\"tb\">
				<tr><th>Datei</th><th>Bild</th></tr>";
				foreach($overhead as $v)
				{
					echo "<tr><td>".$v."</td>";
					echo '<td><img src="'.$dir.$v.'" alt="Profil" /></td></tr>';
				}
				echo "</table><br/>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}
	}


	//
	// Gebäude bearbeiten
	//
	elseif ($sub=="buildingsdata")
	{
		advanced_form("alliancebuildings", $twig);
	}

	//
	// Tech bearbeiten
	//
	elseif ($sub=="techdata")
	{
		advanced_form("alliancetechnologies", $twig);
	}

	//
	// Erstellen
	//
	elseif ($sub=="create")
	{
		echo "<h1>Allianz erstellen</h1>";

		if (isset($_POST['create']))
		{
			$errorCode = null;
			if (Alliance::create(array(
				"name" => $_POST['alliance_name'],
				"tag" => $_POST['alliance_tag'],
				"founder" => new User($_POST['alliance_founder_id'])
				),$errorCode))
			{
				success_msg("Allianz wurde erstellt! [[page alliances sub=edit id=".$errorCode->id."]Details[/page]]");
			}
			else
			{
				error_msg("Allianz konnte nicht erstellt werden!\n\n".$errorCode."");
			}
		}

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo '<table class="tbl">';
		echo "<tr><th>Tag:</th><td>
		<input type=\"text\" name=\"alliance_tag\" value=\"\" required />
		</td></td>";
		echo "<tr><th>Name:</th><td>
		<input type=\"text\" name=\"alliance_name\" value=\"\" required />
		</td></td>";
		echo "<tr><th>Gründer:</th><td>
		<select name=\"alliance_founder_id\" />";
		foreach (usersWithoutAllianceList($app) as $key => $value)
		{
			echo "<option value=\"".$key."\">".$value."</option>";
		}
		echo "</select>
		</td></td>";
		tableEnd();
		echo "<p><input type=\"submit\" name=\"create\" value=\"Erstellen\" /></p>
		</form>";
	}

	//
	// Allianznews (Rathaus)
	//
	elseif ($sub=="news")
	{
		echo '<h1>Allianz-News</h1>';

		echo 'News entfernen die älter als <select id="timespan">
		<option value="604800">1 Woche</option>
		<option value="1209600">2 Wochen</option>
		<option value="2592000" selected="selected">1 Monat</option>
		<option value="5184000">2 Monate</option>
		<option value="7776000">3 Monate</option>
		<option value="15552000">6 Monate</option>
		</select> sind:
		<input type="button" onclick="xajax_allianceNewsRemoveOld(document.getElementById(\'timespan\').options[document.getElementById(\'timespan\').selectedIndex].value)" value="Ausführen" /><br/><br/>';

		$ban_timespan = array(
		21600=>'6 Stunden',
		43200=>'12 Stunden',
		64800=>'18 Stunden',
		86400=>'1 Tag',
		172800=>'2 Tage',
		259200=>'3 Tage',
		432000=>'5 Tage',
		604800=>'1 Woche'
		);
		$ban_text = $conf['townhall_ban']['p1']!='' ? stripslashes($conf['townhall_ban']['p1']) : 'Rathaus-Missbrauch';

		echo 'Standardeinstellung für Sperre: <select id="ban_timespan">';
		foreach ($ban_timespan as $k => $v)
		{
			echo '<option value="'.$k.'"';
			echo  $conf['townhall_ban']['v']==$k ? ' selected="selected"' : '';
			echo '>'.$v.'</option>';
		}
		echo '</select> mit folgendem Text: <input type="text" id="ban_text" value="'.$ban_text.'" size="35" /> ';
		echo '<input type="button" onclick="xajax_allianceNewsSetBanTime(document.getElementById(\'ban_timespan\').options[document.getElementById(\'ban_timespan\').selectedIndex].value,document.getElementById(\'ban_text\').value)" value="Speichern" /><br/><br/>';

		echo '<form id="newsForm" action="?page='.$page.'&amp;sub='.$sub.'" method="post">';
		echo '<div id="newsBox">Lade...</div></form>';
		echo '<script type="text/javascript">xajax_allianceNewsLoad()</script>';
	}

	elseif ($sub=="crap")
	{
		echo "<h1>Überflüssige Daten</h1>";

		if (isset($_GET['action']) && $_GET['action']=="cleanupRanks")
		{
			if (deleteRanksWithoutAlliance($app) > 0) {
				echo "Fehlerhafte Daten gelöscht.";
			}
		}
		elseif (isset($_GET['action']) && $_GET['action']=="cleanupDiplomacy")
		{
			if (deleteDiplomacyWithoutAlliance($app) > 0) {
				echo "Fehlerhafte Daten gelöscht.";
			}
		}
		elseif (isset($_GET['action']) && $_GET['action']=="cleanupEmptyAlliances")
		{
			$drop = $app['db']
				->executeQuery("SELECT * FROM alliances ORDER BY alliance_tag;")
				->fetchAllAssociative();
			if (count($data) > 0)
			{
				$cnt=0;
				foreach ($data as $arr)
				{
					if (numberOfUsersInAlliance($app, $arr['alliance_id']) == 0)
					{
						$app['db']
							->executeStatement("DELETE FROM alliances
								WHERE alliance_id = ?;",
							[$arr['alliance_id']]);

						$app['db']
							->executeStatement("DELETE FROM alliance_ranks
								WHERE rank_alliance_id = ?;",
							[$arr['alliance_id']]);

						$app['db']
							->executeStatement("DELETE FROM alliance_bnd
								WHERE alliance_bnd_alliance_id1 = ?
									OR alliance_bnd_alliance_id2 = ;",
							[$arr['alliance_id'], $arr['alliance_id']]);

						$cnt++;
					}
				}
			}
			echo "$cnt leere Allianzen wurden gelöscht.<br/>";
		}

		// Ränge ohne Allianz
		echo "<h2>Ränge ohne Allianz</h2>";
		$ranksWithoutAlliance = numberOfRanksWithoutAlliance($app);
		if ($ranksWithoutAlliance > 0) {
			echo "$ranksWithoutAlliance Ränge ohne Allianz.
			<a href=\"?page=$page&amp;sub=$sub&amp;action=cleanupRanks\">Löschen?</a>";
		} else {
			echo "Keine fehlerhaften Daten gefunden.";
		}

		// Bündnisse/Kriege ohne Allianz
		echo "<h2>Bündnisse/Kriege ohne Allianz</h2>";
		$bndWithoutAlliance = numberOfDiplomaciesWithoutAlliance($app);
		if ($bndWithoutAlliance > 0) {
			echo "$bndWithoutAlliance Bündnisse/Kriege ohne Allianz.
			<a href=\"?page=$page&amp;sub=$sub&amp;action=cleanupDiplomacy\">Löschen?</a>";
		} else {
			echo "Keine fehlerhaften Daten gefunden.";
		}

		// Allianzen ohne Gründer
		echo "<h2>Allianzen ohne Gründer</h2>";
		$alliancesWithoutFounder = $app['db']
			->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_tag
				FROM alliances a
				WHERE NOT EXISTS (
					SELECT 1
					FROM users u
					WHERE a.alliance_founder_id = u.user_id
				);")
			->fetchAllAssociative();
		if (count($alliancesWithoutFounder) > 0) {
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Tag</th>
			<th class=\"tbltitle\">Name</th>
			<th>&nbsp;</th></tr>";
			foreach($alliancesWithoutFounder as $arr)
			{
				echo "<tr><td class=\"tbldata\">".$arr['alliance_name']."</td>
				<td class=\"tbldata\">".$arr['alliance_tag']."</td>
				<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."\">detail</a></td></tr>";
			}
			echo "</table><br/>";
			echo count($alliancesWithoutFounder)." Allianzen ohne Gründer.";
		} else {
			echo "Keine fehlerhaften Daten gefunden.";
		}

		// User mit fehlerhafter Allianz-Verknüpfung
		echo "<h2>User mit fehlerhafter Allianz-Verknüpfung</h2>";
		$usersWithInvalidAlliances = $app['db']
			->executeQuery("SELECT
					user_id,
					user_nick,
					user_email
				FROM users u
				WHERE
					user_alliance_id != 0
					AND NOT EXISTS (
						SELECT 1
						FROM alliances a
						WHERE a.alliance_id = u.user_alliance_id
					);")
			->fetchAllAssociative();
		if (count($usersWithInvalidAlliances) > 0) {
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Nick</th>
			<th class=\"tbltitle\">E-Mail</th>
			<th>&nbsp;</th></tr>";
			foreach($usersWithInvalidAlliances as $arr)
			{
				echo "<tr><td class=\"tbldata\">".$arr['user_nick']."</td>
				<td class=\"tbldata\">".$arr['user_email']."</td>
				<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">detail</a></td></tr>";
			}
			echo "</table><br/>";
			echo count($usersWithInvalidAlliances) ." User mit fehlerhafter Verknüpfung.";
		} else {
			echo "Keine fehlerhaften Daten gefunden.";
		}

		// Leere Allianzen
		echo "<h2>Leere Allianzen (Allianzen ohne User)</h2>";
		$alliancesWithoutUsers = $app['db']
			->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_tag
				FROM alliances a
				WHERE NOT EXISTS (
					SELECT 1
					FROM users u
					WHERE a.alliance_id = u.user_alliance_id
				);")
			->fetchAllAssociative();
		if (count($alliancesWithoutUsers) > 0) {
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Name</th>
			<th class=\"tbltitle\">Tag</th><th>&nbsp;</th>
			<th>&nbsp;</th></tr>";
			foreach($alliancesWithoutUsers as $arr)
			{
				echo "<tr><td class=\"tbldata\">".$arr['alliance_name']."</td>
				<td class=\"tbldata\">".$arr['alliance_tag']."</td>
				<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."\">detail</a></td>
				<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=drop&amp;alliance_id=".$arr['alliance_id']."\">löschen</a></td></tr>";
			}
			echo "</table><br/>";
			echo count($alliancesWithoutUsers)." Allianzen sind leer.
			<a href=\"?page=$page&amp;sub=$sub&amp;action=cleanupEmptyAlliances\">Löschen?</a>";
		} else {
			echo "Keine fehlerhaften Daten gefunden.";
		}
	}
	else
	{
		$twig->addGlobal('title', 'Allianzen');

		//
		// Suchergebnisse
		//

		if ((isset($_POST['alliance_search']) && $_POST['alliance_search']!="" || $_SESSION['admin']['queries']['alliances']!="") && isset($_GET['action']) && $_GET['action']=="search")
		{
			$twig->addGlobal('subtitle', 'Suchergebnisse');

  			if ($_SESSION['admin']['queries']['alliances']=="")
  			{
				$sql = '';
				if ($_POST['alliance_id']!="")
				{
					$sql.= " AND alliance_id ".stripslashes($_POST['qmode']['alliance_id']).$_POST['alliance_id']."$addchars'";
				}
				if ($_POST['alliance_tag']!="")
				{
					if (stristr($_POST['qmode']['alliance_tag'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND alliance_tag ".stripslashes($_POST['qmode']['alliance_tag']).$_POST['alliance_tag']."$addchars'";
				}
				if ($_POST['alliance_name']!="")
				{
					if (stristr($_POST['qmode']['alliance_name'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND alliance_name ".stripslashes($_POST['qmode']['alliance_name']).$_POST['alliance_name']."$addchars'";
				}
				if ($_POST['alliance_text']!="")
				{
					if (stristr($_POST['qmode']['alliance_text'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND alliance_text ".stripslashes($_POST['qmode']['alliance_text']).$_POST['alliance_text']."$addchars'";
				}

				$sqlstart =	"SELECT
					alliance_id,
					alliance_name,
					alliance_tag,
					alliance_foundation_date,
					alliance_founder_id,
					COUNT(user_id) AS cnt
				FROM
					alliances
				LEFT JOIN
					users ON user_alliance_id=alliance_id
				WHERE 1 ";
				$sqlend = "
				GROUP BY alliance_id
				ORDER BY alliance_tag;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['queries']['alliances'] = $sql;
			}
			else {
				$sql = $_SESSION['admin']['queries']['alliances'];
			}

			$data = $app['db']
				->executeQuery($sql)
				->fetchAllAssociative();
			$nr = count($data);
			if ($nr==1)
			{
				$arr = $data[0];
				echo "<script>document.location='?page=$page&sub=edit&id=".$arr['alliance_id']."';</script>
				Klicke <a href=\"?page=$page&sub=edit&id=".$arr['alliance_id']."\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
			}
			elseif ($nr > 0)
			{
				echo $nr." Datens&auml;tze vorhanden<br/><br/>";
				if ($nr > 20)
				{
					echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
				}

 				$users = get_user_names();
				echo "<table class=\"tb\">";
				echo "<tr>";
				echo "<th>ID</th>";
				echo "<th>Name</th>";
				echo "<th>Gründer</th>";
				echo "<th>Gründung</th>";
				echo "<th>User</th>";
				echo "<th>&nbsp;</th>";
				echo "</tr>";
				foreach ($data as $arr)
				{
					echo "<tr>";
					echo "<td>".$arr['alliance_id']."</td>";
					echo "<td>[".$arr['alliance_tag']."] <a href=\"?page=$page&sub=edit&alliance_id=".$arr['alliance_id']."\">".$arr['alliance_name']."</a></td>";
					echo "<td>".$users[$arr['alliance_founder_id']]['nick']."</td>";
					echo "<td>".df($arr['alliance_foundation_date'])."</td>";
					echo "<td>".$arr['cnt']."</td>";
					echo "<td style=\"width:50px;\">";
					echo del_button("?page=$page&sub=drop&alliance_id=".$arr['alliance_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=dropinactive'\" value=\"Leere Allianzen löschen\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zurück\" />";
			}
		}

		//
		// Leere Allianzen löschen
		//

		elseif (isset($_GET['sub']) && $_GET['sub']=="dropinactive")
		{
			echo "Sollen folgende leeren Allianzen gelöscht werden?<br/><br/>";
			$data = $app['db']
				->executeQuery("SELECT *
					FROM alliances
					ORDER BY alliance_tag;", [])
				->fetchAllAssociative();

			if (count($data) > 0)
			{
 				$users = get_user_names();
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Tag</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Gründer</td>";
				echo "<th>Gründung</th>";
				echo "<td valign=\"top\">&nbsp;</td>";
				echo "</tr>";
				$cnt = 0;
				foreach ($data as $arr)
				{
					if (numberOfUsersInAlliance($app, $arr['alliance_id']) == 0)
					{
						echo "<tr>";
						echo "<td class=\"tbldata\">".$arr['alliance_id']."</td>";
						echo "<td class=\"tbldata\">".$arr['alliance_name']."</td>";
						echo "<td class=\"tbldata\">".$arr['alliance_tag']."</td>";
						echo "<td class=\"tbldata\">".$users[$arr['alliance_founder_id']]['nick']."</td>";
						echo "<td class=\"tbldata\">".date("Y-m-d",$arr['alliance_foundation_date'])."</td>";
						echo "<td class=\"tbldata\"><a href=\"?page=$page&sub=edit&alliance_id=".$arr['alliance_id']."\">details</a></td>";
						echo "</tr>";
						$cnt++;
					}
				}
				echo "</table>";
				echo "<br/>$cnt von ".mysql_num_rows($res)." Allianzen sind zur Löschung vorgesehen!<br/>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Nein, zurück zur Übersicht\" /> ";
				if ($cnt > 0) {
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=dropinactive'\" value=\"Ja, löschen!\" />";
				}
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zurück\" />";
			}
		}

		//
		// Daten bearbeiten
		//

		elseif (isset($_GET['sub']) && $_GET['sub']=="edit")
		{
			include("alliance/edit.inc.php");
		}

		//
		// Daten löschen
		//

		elseif (isset($_GET['sub']) && $_GET['sub']=="drop")
		{
			$arr = fetchAlliance($app, $_GET['alliance_id']);
			if ($arr != null)
			{
				echo "Soll folgende Allianz gelöscht werden?<br/><br/>";
				echo "<form action=\"?page=$page\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">".$arr['alliance_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td><td class=\"tbldata\">".$arr['alliance_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Tag</td><td class=\"tbldata\">".$arr['alliance_tag']."</td></tr>";
				$users = get_user_names();
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gründer</td><td class=\"tbldata\">".$users[$arr['alliance_founder_id']]['nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">".text2html($arr['alliance_text'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gründung</td><td class=\"tbldata\">".date("Y-m-d H:i:s",$arr['alliance_foundation_date'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Website</td><td class=\"tbldata\">".$arr['alliance_url']."</td></tr>";
				if (isset($arr['alliance_img'])) {
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td><td class=\"tbldata\"><img src=\"".ALLIANCE_IMG_DIR.'/'.$arr['alliance_img']."\" width=\"100%\" alt=\"".$arr['alliance_img']."\" /></td></tr>";
				}
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Mitglieder</td><td class=\"tbldata\">";
				$usersInAlliance = fetchUsersInAlliance($app, $arr['alliance_id']);
				if (count($usersInAlliance) > 0)
				{
					echo "<table style=\"width:100%\">";
					foreach ($usersInAlliance as $uarr)
						echo "<tr><td>".$uarr['user_nick']."</td>
						<td>".$uarr['user_points']." Punkte</td>
						<td>[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$uarr['user_id']."\">details</a>] [<a href=\"?page=messages&amp;sub=sendmsg&amp;user_id=".$uarr['user_id']."\">msg</a>]</td></tr>";
					echo "</table>";
				}
				else {
					echo "<b>KEINE MITGLIEDER!</b>";
				}
				echo "</td></tr>";
				echo "</table>";
				echo "<input type=\"hidden\" name=\"alliance_id\" value=\"".$arr['alliance_id']."\" />";
				echo "<br/><input type=\"submit\" name=\"drop\" value=\"Löschen\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zurück\" onclick=\"history.back();\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
				echo "</form>";
			}
			else {
				echo "<b>Fehler:</b> Datensatz nicht gefunden!<br/><br/><a href=\"javascript:history.back();\">Zurück</a>";
			}
		}

		//
		// Suchmaske
		//

		else
		{
			$_SESSION['admin']['queries']['alliances']="";

			// Allianz löschen
			if (isset($_POST['drop']))
			{
				$ally = new Alliance($_POST['alliance_id']);
				if ($ally->delete()) {
					echo "Die Allianz wurde gelöscht!<br/><br/>";
				} else {
					echo MessageBox::error("", "Allianz konnte nicht gelöscht werden (ist sie in einem aktiven Krieg?)");
				}
			}

			// Leere Allianzen löschen
			if (isset($_GET['action']) && $_GET['action']=="dropinactive")
			{
				$data = $app['db']
					->executeQuery("SELECT *
						FROM alliances
						ORDER BY alliance_tag;")
					->fetchAllAssociative();
				if (count($data) > 0)
				{
					$cnt=0;
					foreach ($data as $arr)
					{
						if (numberOfUsersInAlliance($app, $arr['alliance_id']) == 0)
						{
							$app['db']
								->executeStatement("DELETE FROM alliances
									WHERE alliance_id = ?;",
									[$arr['alliance_id']]);
							$app['db']
								->executeStatement("DELETE FROM alliance_ranks
									WHERE rank_alliance_id = ?;",
									[$arr['alliance_id']]);
							$app['db']
								->executeStatement("DELETE FROM alliance_bnd
									WHERE alliance_bnd_alliance_id1 = ?
										OR alliance_bnd_alliance_id2 = ?;",
									[$arr['alliance_id'], $arr['alliance_id']]);
							$cnt++;
						}
					}
				}
				echo "$cnt leere Allianzen wurden gelöscht!<br/><br/>";
			}

			// Suchmaske
			$twig->addGlobal("subtitle", 'Suchmaske');

			echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_id\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_id');echo"</td></tr>";
			echo "<tr><td class=\"tbltitle\">Tag</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_tag');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Name</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'alliance_name','citybox2');\"/> ";fieldqueryselbox('alliance_name');echo "<br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Text</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_text\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_text');echo "</td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"alliance_search\" value=\"Suche starten\" /> (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt)</form>";
			echo "<br/>Es sind ".nf(numberOfAlliances($app))." Einträge in der Datenbank vorhanden.";
		}
	}

	function numberOfAlliances(Container $app): int
	{
		return $app['db']
			->executeQuery("SELECT count(*)
				FROM alliances;")
			->fetchOne();
	}

	function numberOfUsersInAlliance(Container $app, int $allianceId): int
	{
		return $app['db']
			->executeQuery("SELECT COUNT(*)
				FROM users
				WHERE user_alliance_id = ?;",
				[$allianceId])
			->fetchOne();
	}

	function fetchUsersInAlliance(Container $app, int $allianceId): array
	{
		return $app['db']
			->executeQuery("SELECT
					user_id,
					user_nick,
					user_points
				FROM users
				WHERE user_alliance_id = ?
				ORDER BY user_nick;",
				[$allianceId])
			->fetchAllAssociative();
	}

	function fetchAlliance(Container $app, ?int $id)
	{
		return $app['db']
			->executeQuery("SELECT *
				FROM alliances
				WHERE alliance_id = ?;",
				[$id])
			->fetchAssociative();
	}

	function usersWithoutAllianceList(Container $app): array
	{
		$res = $app['db']
			->executeQuery("SELECT user_id, user_nick
				FROM users
				WHERE user_alliance_id = 0
				ORDER BY user_nick;");
		$data = [];
		while ($arr = $res->fetchAssociative())
		{
			$data[$arr['user_id']] = $arr['user_nick'];
		}
		return $data;
	}

	function removeAlliancePicture(Container $app, int $allianceId): bool
	{
		$arr = $app['db']
			->executeQuery("SELECT alliance_img
				FROM alliances
				WHERE alliance_id = ?;",
				[$allianceId])
			->fetchAssociative();
		if ($arr != null) {
			if (file_exists(ALLIANCE_IMG_DIR."/".$arr['alliance_img'])) {
				unlink(ALLIANCE_IMG_DIR."/".$arr['alliance_img']);
			}
			$affected = $app['db']
				->executeStatement("UPDATE alliances
					SET alliance_img = '',
						alliance_img_check = 0
					WHERE alliance_id = ?;",
					[$allianceId]);
			return $affected > 0;
		}
		return false;
	}

	function markAlliancePictureChecked(Container $app, int $allianceId): void
	{
		$app['db']
			->executeStatement("UPDATE alliances
				SET alliance_img_check = 0
				WHERE alliance_id = ?;",
				[$allianceId]);
	}

	function fetchAlliancesWithUncheckedPictures(Container $app): array
	{
		return $app['db']
			->executeQuery("SELECT
					alliance_id,
					alliance_tag,
					alliance_name,
					alliance_img
				FROM
					alliances
				WHERE
					alliance_img_check = 1
					AND alliance_img != '';")
			->fetchAllAssociative();
	}

	function fetchAlliancesWithPictures(Container $app): array
	{
		return $app['db']
			->executeQuery("SELECT
					alliance_id,
					alliance_name,
					alliance_img
				FROM
					alliances
				WHERE
					alliance_img!=''")
			->fetchAllAssociative();
	}

	function numberOfRanksWithoutAlliance(Container $app): int
	{
		return $app['db']
			->executeQuery("SELECT
					COUNT(r.rank_id)
				FROM alliance_ranks r
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE r.rank_alliance_id = a.alliance_id
				);")
			->fetchOne();
	}

	function deleteRanksWithoutAlliance(Container $app): int
	{
		return $app['db']
			->executeStatement("DELETE FROM alliance_ranks
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE rank_alliance_id = a.alliance_id
				);");
	}

	function numberOfDiplomaciesWithoutAlliance(Container $app): int
	{
		return $app['db']
			->executeQuery("SELECT
					COUNT(b.alliance_bnd_id)
				FROM alliance_bnd b
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE b.alliance_bnd_alliance_id1 = a.alliance_id
				)
				OR NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE b.alliance_bnd_alliance_id2 = a.alliance_id
				);")
			->fetchOne();
	}

	function deleteDiplomacyWithoutAlliance(Container $app): int
	{
		return $app['db']
			->executeStatement("DELETE FROM alliance_bnd
				WHERE NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE alliance_bnd_alliance_id1 = a.alliance_id
				)
				OR NOT EXISTS (
					SELECT 1
					FROM alliances a
					WHERE alliance_bnd_alliance_id2 = a.alliance_id
				)");
	}