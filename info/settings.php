<?php
	echo "<h2>Einstellungen</h2>";
	helpNavi(array("Einstellungen","settings"));

	$item['game_name']['p1']="Spielversion";
	$item['enable_register']['p2']="Max. Spieler";
	$item['hmode_days']['v']="Urlaubsmodus Mindestdauer";
	$item['points_update']['p1']="Einheiten/Userpunkt";
	$item['points_update']['p2']="Userpunkte/Allypunk";
	$item['user_delete_days']['v']="Tage bis zur endgültigen Löschung eines Accounts";
	$item['user_inactive_days']['v']="Spieler werde inaktiv nach (in Tagen)";
	$item['user_inactive_days']['p1']="Löschung wegen Inaktivität nach (in Tagen)";
	$item['user_timeout']['v']="Timeout in Sekunden";


//	$item['enable_register']['p2']="Spieler";


		infobox_start("Grundeinstellungen",1);
		echo "<tr><td class=\"tbltitle\">Name</td>";
		echo "<td class=\"tbltitle\">Wert</td></tr>";
		if (file_exists("VERSION"))
		{
			echo "<tr><td class=\"tbldata\">Revision</td>";
			echo "<td class=\"tbldata\">";
			readfile("VERSION");
			echo "</td></tr>";
			
		}
		foreach ($item as $conf_name => $a)
		{
			foreach ($a as $par => $val)
			{
			echo "<tr><td class=\"tbldata\">".$val."</td>";
			echo "<td class=\"tbldata\">".$conf[$conf_name][$par]."</td></tr>";
			}
		}
		infobox_end(1);
?>