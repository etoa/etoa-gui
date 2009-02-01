<?php
	echo "<h2>Einstellungen</h2>";
	HelpUtil::breadCrumbs(array("Einstellungen","settings"));

	$item = array();
	$item['game_name']['p1']="Spielversion";
	$item['enable_register']['p2']="Max. Spieler";
	$item['hmode_days']['v']="Urlaubsmodus Mindestdauer";
	$item['points_update']['p1']="Einheiten/Userpunkte";
	$item['points_update']['p2']="Userpunkte/Allianzpunkte";
	$item['user_delete_days']['v']="Tage bis zur endgültigen Löschung eines Accounts";
	$item['user_inactive_days']['v']="Spieler werden inaktiv nach (in Tagen)";
	$item['user_inactive_days']['p1']="Löschung wegen Inaktivität nach (in Tagen)";
	$item['user_timeout']['v']="Timeout in Sekunden";

	$item['global_time']['v']="Globaler Bauzeitfaktor";
	$item['flight_start_time']['v']="Startzeitfaktor";
	$item['flight_land_time']['v']="Landezeitfaktor";
	$item['flight_flight_time']['v']="Flugzeitfaktor";
	$item['def_build_time']['v']="Verteidigungsbauzeitfaktor";
	$item['build_build_time']['v']="Gebäudebauzeitfaktor";
	$item['res_build_time']['v']="Forschungszeitfaktor";
	$item['ship_build_time']['v']="Schiffbauzeitfaktor";
	$item['planet_temp']['p1']="Minimale Planetentemperatur";
	$item['planet_temp']['p2']="Maximale Planetentemperatur";
	$item['planet_fields']['p1']="Minimale Feldanzahl";
	$item['planet_fields']['p2']="Maximale Feldanzahl";
	$item['num_planets']['p1']="Minimale Planetenanzahl";
	$item['num_planets']['p2']="Maximale Planetenanzahl";
	$item['user_max_planets']['v']="Maximale Planeten/User";

	$item['def_restore_percent']['v']="Verteidigungswiederherstellung";
	$item['def_wf_percent']['v']="Verteidigung ins Trümmerfeld";
	$item['ship_wf_percent']['v']="Schiffe ins Trümmerfeld";
	$item['user_attack_min_points']['v']="Noobschutz: Minimale Punkte";
	$item['user_attack_percentage']['v']="Noobschutz: Verhältnis %";
		
	$item['people_food_require']['v']="Nahrungsverbrauch pro Arbeiter";
	$item['people_multiply']['v']="Bevölkerungswachstum";

		tableStart("Grundeinstellungen");
		echo "<tr><th>Name</th>";
		echo "<th>Wert</th></tr>";
		if (UNIX)
		{
			echo "<tr><td>Revision</td>";
			echo "<td>";
			passthru("svnversion");
			echo "</td></tr>";
			
		}
		foreach ($item as $conf_name => $a)
		{
			foreach ($a as $par => $val)
			{
			echo "<tr><td>".$val."</td>";
			echo "<td>".$conf[$conf_name][$par]."</td></tr>";
			}
		}
		tableEnd();
?>