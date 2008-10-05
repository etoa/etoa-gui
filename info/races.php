<?PHP
	echo "<h2>Rassen</h2>";
	Help::navi(array("Rassen","races"));

	if (isset($_GET['order']))
	{
		$order="race_".$_GET['order'];
		if ($_SESSION['help']['orderfield']==$_GET['order'])
		{
			if ($_SESSION['help']['ordersort']=="DESC")
				$sort="ASC";
			else
				$sort="DESC";
		}
		else
		{
			if ($_GET['order']=="name")
				$sort="ASC";
			else
				$sort="DESC";
		}
		$_SESSION['help']['orderfield']=$_GET['order'];
		$_SESSION['help']['ordersort']=$sort;
	}
	else
	{
		$order="race_name";
		$sort="ASC";
	}

	$res = dbquery("SELECT * FROM races WHERE race_active=1 ORDER BY $order $sort;");
	if (mysql_num_rows($res)>0)
	{

		infobox_start("Rassenboni",1);
		echo "<tr><td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_metal\">".RES_METAL."</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_crystal\">".RES_CRYSTAL."</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_plastic\">".RES_PLASTIC."</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_fuel\">".RES_FUEL."</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_food\">".RES_FOOD."</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_power\">Energie</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_population\">Wachstum</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_buildtime\">Bauzeit</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_fleettime\">Fluggeschwindigkeit</a></td></tr>";

		while ($arr = mysql_fetch_array($res))
		{
			$x=mt_rand(1,5);
			echo "<tr><td class=\"tbltitle\">".$arr['race_name']."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_metal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_crystal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_plastic'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_fuel'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_food'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_power'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_population'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_researchtime'],1,1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_buildtime'],1,1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['race_f_fleettime'],1,1)."</td></tr>";

		}
		infobox_end(1);
	}

	$res = dbquery("SELECT * FROM ".$db_table['races']." ORDER BY $order $sort;");
	if (mysql_num_rows($res)>0)
	{

		infobox_start("Rasseninfos",1);
		echo "<tr><td class=\"tbltitle\">Name</td>";
		echo "<td class=\"tbltitle\">Beschreibung</td></tr>";

		while ($arr = mysql_fetch_array($res))
		{
			$x=mt_rand(1,5);
			echo "<tr><td class=\"tbltitle\">".$arr['race_name']."</td>";
			echo "<td class=\"tbldata\">".text2html($arr['race_comment'])."</td></tr>";

		}
		infobox_end(1);
	}


?>
