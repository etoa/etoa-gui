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
	// 	Dateiname: stats.php
	// 	Topic: Statistikanzeige
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar:
	//
	// DEFINITIONEN //


	// BEGIN SKRIPT //

	echo "<h1>Statistiken</h1>";

	//
	// Details anzeigen
	//

	if ($_GET['userdetail']>0)
	{
		$res=dbquery("
		SELECT 
            user_nick,
            user_points,
            user_rank_current,
            user_id 
		FROM 
			".$db_table['users']." 
		WHERE 
			user_id='".$_GET['userdetail']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			echo "<h2>Punktedetails f&uuml;r ".text2html($arr['user_nick'])."</h2>";
			echo "<b>Punkte aktuell:</b> ".nf($arr['user_points']).", <b>Rang aktuell:</b> ".$arr['user_rank_current']."<br/><br/>";
			echo "<img src=\"inc/statsdiag.php?user=".$arr['user_id']."\" alt=\"Diagramm\" /><br/><br/>";
			$pres=dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['user_points']." 
			WHERE 
				point_user_id='".$_GET['userdetail']."' 
			ORDER BY 
				point_timestamp DESC 
			LIMIT 48; ");
			if (mysql_num_rows($pres)>0)
			{
				$points=array();
				while ($parr=mysql_fetch_array($pres))
				{
					$points[$parr['point_timestamp']]=$parr['point_points'];
					$fleet[$parr['point_timestamp']]=$parr['point_ship_points'];
					$tech[$parr['point_timestamp']]=$parr['point_tech_points'];
					$buildings[$parr['point_timestamp']]=$parr['point_building_points'];
				}
				echo "<table width=\"400\" class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\">Datum</th><th class=\"tbltitle\">Zeit</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Flotte</th><th class=\"tbltitle\">Forschung</th><th class=\"tbltitle\">Geb&auml;ude</th></tr>";
				foreach ($points as $time=>$val)
				{
					echo "<tr><td class=\"tbldata\">".date("d.m.Y",$time)."</td><td class=\"tbldata\">".date("H:i",$time)."</td>";
					echo "<td class=\"tbldata\">".nf($val)."</td><td class=\"tbldata\">".nf($fleet[$time])."</td><td class=\"tbldata\">".nf($tech[$time])."</td><td class=\"tbldata\">".nf($buildings[$time])."</td></tr>";
				}
				echo "</table><br/>";
				echo "<input type=\"button\" value=\"Userdetails anzeigen\" onclick=\"document.location='?page=userinfo&id=".$arr['user_id']."'\" /> &nbsp; ";
			}
			else
				echo "<i>Keine Punktedaten vorhanden!</i>";
		}
		else
			echo "<i>Datensatz wurde nicht gefunden!</i>";
		if ($_GET['limit']>0) $limit=$_GET['limit']; else $limit=0;
		echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&mode=$mode&limit=".$limit."'\" /> &nbsp; ";
	}

	//
	// Tabellen anzeigen
	//

	else
	{
		// Menu
		show_js_tab_menu(array("xajax_statsShowBox('user');"=>"Spieler","xajax_statsShowBox('ships')"=>"Flotten","xajax_statsShowBox('tech')"=>"Technologien","xajax_statsShowBox('buildings')"=>"Geb&auml;ude","xajax_statsShowBox('alliances')"=>"Allianzen","xajax_statsShowBox('pillory')"=>"Pranger"));
		echo "<br>";

    echo "<script type=\"text/javascript\"> xajax.loadingFunction=function(){if (document.getElementById('statsTable'))xajax.$('statsTable').innerHTML='Einen Moment bitte...';};</script>";

		echo "<div id=\"statsBox\">";
		// >> AJAX generated content inserted here
		echo "</div>";

		echo "<script type=\"text/javascript\">
		xajax_statsShowBox('user');
		</script>";


		// Legende
		echo "<p align=\"center\">Die Aktualisierung der <span ".tm("Punkteberechnung","F&uuml;r ".STATS_USER_POINTS."t verbaute Rohstoffe bekommt der Spieler 1 Punkt in der Statistik<br>F&uuml;r ".STATS_ALLIANCE_POINTS." Spielerpunkte bekommt die Allianz 1 Punkt in der Statisik")."><u>Punkte</u></span> erfolgt ";
		$h = $conf['points_update']['v']/3600;
		if ($h>1)
			echo "alle $h Stunden!<br>";
		elseif ($h==1)
			echo " jede Stunde!<br>";
		else
		{
			$m = $conf['points_update']['v']/60;
			echo "alle $m Minuten!<br>";
		}
		echo "Neu angemeldete Benutzer erscheinen erst nach der ersten Aktualisierung in der Liste.<br/>";
		echo "Letzte Aktualisierung: <b>".date("d.m.Y",$conf['statsupdate']['v'])."</b> um <b>".date("H:i",$conf['statsupdate']['v'])." Uhr</b><br/>";
		echo "<b>Legende:</b> <span style=\"color:".$conf['color_banned']['v'].";\">Gesperrt</span>, ";
		echo "<span style=\"color:".$conf['color_umod']['v'].";\">Urlaubsmodus</span>, ";
		echo "<span style=\"color:".$conf['color_inactive']['v'].";\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>, ";
		echo "<span style=\"color:".$conf['color_alliance']['v'].";\">";
		if ($mode=="alliances")
			echo "Allianz";
		else
			echo "Allianzmitglied";
		echo "</span></p>";
	}
?>
