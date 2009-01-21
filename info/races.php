<?PHP
	echo "<h2>Rassen</h2>";
	
	//Commentet ou for the time being, because this function needs rewriting.
	//HelpUtil::breadCrumbs(array("Rassen","races"));


	//
	//Order
	//
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

	//
	//Table with a list of all races
	//
	$res = dbquery("SELECT * FROM races ORDER BY $order $sort;");
	if (mysql_num_rows($res)>0)
	{

		tableStart("Kurzinformation");
		echo "<tr>,<td class=\"tbltitle\">Logo</td>";
		echo "<td class=\"tbltitle\">Name</td>";
		echo "<td class=\"tbltitle\">Kurzbeschreibug</td></tr>";

		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr><td>
						<img src=\"".IMAGE_PATH."/races/race_".$arr['race_id']."_small.".IMAGE_EXT."\"></td>";
			echo "<td><a href=\"?page=help&site=races_detail\">".$arr['race_name']."</a></td>";
			echo "<td>".text2html($arr['race_short_comment'])."</td></tr>";

		}
		tableEnd();
	}
	
	//
	//Bonus-Malus table to compare all the races
	//
	$res = dbquery("
	SELECT 
		race_id 
	FROM 
		races 
	WHERE 
		race_active=1 
	ORDER BY 
		$order $sort;");
	if (mysql_num_rows($res)>0)
	{
		tableStart("Bonus-Malus Vergleichstabelle");
		echo "<tr><th><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_metal\">".RES_METAL."</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_crystal\">".RES_CRYSTAL."</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_plastic\">".RES_PLASTIC."</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_fuel\">".RES_FUEL."</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_food\">".RES_FOOD."</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_power\">Energie</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_population\">Wachstum</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_buildtime\">Bauzeit</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_fleettime\">Fluggeschwindigkeit</a></th></tr>";

		while ($arr = mysql_fetch_row($res))
		{
			// Make use of our new race class
			$race = new Race($arr[0]);
			
			echo "<tr><td class=\"tbltitle\">".$race."</td>";	// Using the magic __toString() method
			echo "<td>".get_percent_string($race->metal,1)."</td>";
			echo "<td>".get_percent_string($race->crystal,1)."</td>";
			echo "<td>".get_percent_string($race->plastic,1)."</td>";
			echo "<td>".get_percent_string($race->fuel,1)."</td>";
			echo "<td>".get_percent_string($race->food,1)."</td>";
			echo "<td>".get_percent_string($race->power,1)."</td>";
			echo "<td>".get_percent_string($race->population,1)."</td>";
			echo "<td>".get_percent_string($race->researchTime,1,1)."</td>";
			echo "<td>".get_percent_string($race->buildTime,1,1)."</td>";
			echo "<td>".get_percent_string($race->fleetSpeedFactor,1)."</td></tr>";
		}
		tableEnd();
	}

?>
