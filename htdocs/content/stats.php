<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\RuntimeDataStore;

/** @var RuntimeDataStore */
$runtimeDataStore = $app['etoa.runtime.datastore'];

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

	// BEGIN SKRIPT //

	echo "<h1>Statistiken</h1>";

	//
	// Details anzeigen
	//

	if (isset($_GET['userdetail']) && intval($_GET['userdetail'])>0)
	{
		$udid = intval($_GET['userdetail']);
		$res=dbquery("
		SELECT
            user_nick,
            user_points,
            user_rank,
            user_id
		FROM
			users
		WHERE
			user_id='".$udid."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			tableStart("Statistiken f&uuml;r ".text2html($arr['user_nick'])."");

			echo "<tr><td colspan=\"6\" style=\"text-align:center;\">
				<b>Punkte aktuell:</b> ".nf($arr['user_points']).", <b>Rang aktuell:</b> ".$arr['user_rank']."
			</td></tr>";
			echo "<tr><td colspan=\"6\" style=\"text-align:center;\">
				<img src=\"misc/stats.image.php?user=".$arr['user_id']."\" alt=\"Diagramm\" />
			</td></tr>";
			$pres=dbquery("
			SELECT
				*
			FROM
				user_points
			WHERE
				point_user_id='".$udid."'
			ORDER BY
				point_timestamp DESC
			LIMIT 48; ");
			if (mysql_num_rows($pres)>0)
			{
				$points = [];
				$fleet = [];
				$tech = [];
				$buildings = [];
				while ($parr=mysql_fetch_array($pres))
				{
					$points[$parr['point_timestamp']]=$parr['point_points'];
					$fleet[$parr['point_timestamp']]=$parr['point_ship_points'];
					$tech[$parr['point_timestamp']]=$parr['point_tech_points'];
					$buildings[$parr['point_timestamp']]=$parr['point_building_points'];
				}
				echo "<tr><th>Datum</th><th>Zeit</th><th>Punkte</th><th>Flotte</th><th>Forschung</th><th>Geb&auml;ude</th></tr>";
				foreach ($points as $time=>$val)
				{
					echo "<tr><td>".date("d.m.Y",$time)."</td><td>".date("H:i",$time)."</td>";
					echo "<td>".nf($val)."</td><td>".nf($fleet[$time])."</td><td>".nf($tech[$time])."</td><td>".nf($buildings[$time])."</td></tr>";
				}
			}
			else
			{
				echo "<tr><td colspan=\"6\"><i>Keine Punktedaten vorhanden!</td></tr>";
			}

			tableEnd();

			if (!$popup)
				echo "<input type=\"button\" value=\"Profil anzeigen\" onclick=\"document.location='?page=userinfo&id=".$arr['user_id']."'\" /> &nbsp; ";

		}
		else
			error_msg("Datensatz wurde nicht gefunden!");
	}

	elseif (isset($_GET['alliancedetail']) && intval($_GET['alliancedetail'])>0)
	{
		$adid = intval($_GET['alliancedetail']);

		$res=dbquery("
		SELECT
            alliance_tag,
			alliance_name,
            alliance_points,
            alliance_rank_current,
            alliance_id
		FROM
			alliances
		WHERE
			alliance_id='".$adid."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			echo "<h2>Punktedetails f&uuml;r [".text2html($arr['alliance_tag'])."] ".text2html($arr['alliance_name'])."</h2>";
			echo "<b>Punkte aktuell:</b> ".nf($arr['alliance_points']).", <b>Rang aktuell:</b> ".$arr['alliance_rank_current']."<br/><br/>";
			echo "<img src=\"misc/alliance_stats.image.php?alliance=".$arr['alliance_id']."\" alt=\"Diagramm\" /><br/><br/>";
			$pres=dbquery("
			SELECT
				*
			FROM
				alliance_points
			WHERE
				point_alliance_id='".$adid."'
			ORDER BY
				point_timestamp DESC
			LIMIT 48; ");
			if (mysql_num_rows($pres)>0)
			{
				$points = [];
				$avg = [];
				$user = [];
				while ($parr=mysql_fetch_array($pres))
				{
					$points[$parr['point_timestamp']]=$parr['point_points'];
					$avg[$parr['point_timestamp']]=$parr['point_avg'];
					$user[$parr['point_timestamp']]=$parr['point_cnt'];
				}
				tableStart('','400');
				echo "<tr><th>Datum</th><th>Zeit</th><th>Punkte</th><th>User-Schnitt</th><th>User</th></tr>";
				foreach ($points as $time=>$val)
				{
					echo "<tr><td>".date("d.m.Y",$time)."</td><td>".date("H:i",$time)."</td>";
					echo "<td>".nf($points[$time])."</td><td>".nf($avg[$time])."</td><td>".nf($user[$time])."</td></tr>";
				}
				tableEnd();
				echo "<input type=\"button\" value=\"Allianzdetails anzeigen\" onclick=\"document.location='?page=alliance&info_id=".$arr['alliance_id']."'\" /> &nbsp; ";
			}
			else
				error_msg("Keine Punktedaten vorhanden!");
		}
		else
			error_msg("Datensatz wurde nicht gefunden!");

		$limit = 0;
		if (isset($_GET['limit']))
		{
			$limit=intval($_GET['limit']);
		}
		echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&mode=$mode&limit=".$limit."'\" /> &nbsp; ";
	}

	//
	// Tabellen anzeigen
	//

	else
	{
		$_SESSION['alliance_tag'] = $cu->allianceTag();

		$ddm = new DropdownMenu(1);
		$ddm->add('total','Gesamtstatistik','xajax_statsShowBox(\'user\');');

		$ddm->add('buildings','Gebäude','xajax_statsShowBox(\'buildings\');');
		$ddm->add('tech','Forschung','xajax_statsShowBox(\'tech\');');
		$ddm->add('ships','Schiffe','xajax_statsShowBox(\'ships\');');
		$ddm->add('exp','Erfahrung','xajax_statsShowBox(\'exp\');');

		$ddm->add('battle','Kampf','xajax_statsShowBox(\'battle\');');
		$ddm->add('trade','Handel','xajax_statsShowBox(\'trade\');');
		$ddm->add('diplomacy','Diplomatie','xajax_statsShowBox(\'diplomacy\');');
		echo $ddm;

		$ddm = new DropdownMenu(1);
		$ddm->add('alliances','Allianzen','xajax_statsShowBox(\'alliances\');');
		$ddm->add('base','Allianzbasis','xajax_statsShowBox(\'base\');');
		$ddm->add('titles','Titel','xajax_statsShowBox(\'titles\');');

		$ddm->add('pillory','Pranger','xajax_statsShowBox(\'pillory\');');
		$ddm->add('gamestats','Spielstatistik','xajax_statsShowBox(\'gamestats\');');

		echo $ddm;



		echo "<br/>";

    echo "<div id=\"statsBox\">
    <div class=\"loadingMsg\">Lade Daten... <br/>(JavaScript muss aktiviert sein!)</div>";
		// >> AJAX generated content inserted here
		echo "</div>";

		if (isset($_GET['mode']) && ctype_alpha($_GET['mode']))
		{
			$mode = $_GET['mode'];
		}
		elseif(isset($_SESSION['statsmode']))
		{
			$mode=$_SESSION['statsmode'];
		}
		else
		{
			$mode="user";
		}

		echo "<script type=\"text/javascript\">
		xajax_statsShowBox('".$mode."');
		</script><br/>";


		// Legende
		iBoxStart("Legende zur Statistik");
		echo "<b>Farben:</b>
		<span class=\"userSelfColor\">Eigener Account</span>,
		<span class=\"userLockedColor\">Gesperrt</span>,
		<span class=\"userHolidayColor\">Urlaubsmodus</span>,
		<span class=\"userInactiveColor\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>,
		<span class=\"userAllianceMemberColor\">Allianz(-mitglied)</span>
		<br/>";
		$statsUpdate = $runtimeDataStore->get('statsupdate');
		if ($statsUpdate != null)
		{
			echo "Letzte Aktualisierung: <b>".df($statsUpdate)." Uhr</b><br/>";
		}
		echo "Die Aktualisierung der Punkte erfolgt ";
		$h = $config->getInt('points_update')/3600;
		if ($h>1)
			echo "alle $h Stunden!<br>";
		elseif ($h==1)
			echo " jede Stunde!<br>";
		else
		{
			$m = $config->getInt('points_update')/60;
			echo "alle $m Minuten!<br/>";
		}
		echo "Neu angemeldete Benutzer erscheinen erst nach der ersten Aktualisierung in der Liste.<br/>";
		echo "F&uuml;r ".$config->param1Int('points_update')." verbaute Rohstoffe bekommt der Spieler 1 Punkt in der Statistik<br/>
		F&uuml;r ".$config->param2Int('points_update')." Spielerpunkte bekommt die Allianz 1 Punkt in der Statistik";
		iBoxEnd();
	}
?>
