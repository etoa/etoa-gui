<?PHP

$url = "?$link&amp;site=$site";

if (isset($_GET['detail'])) {

	$raceId = $_GET['detail'];

	$res = dbQuerySave("
	SELECT
		*
	FROM
		races
	WHERE
		race_id=?
	;", array($raceId));

	$arr=mysql_fetch_array($res);

	echo "<h2>Rassen: ".$arr['race_name']."</h2>";

	echo text2html($arr['race_comment'])."<br/><br/>";
	tableStart('',300);
	echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
	if ($arr['race_f_metal']!=1)
	{
		echo "<tr><th>".RES_ICON_METAL."Produktion von ".RES_METAL.":</td><td>".get_percent_string($arr['race_f_metal'],1)."</td></tr>";
	}
	if ($arr['race_f_crystal']!=1)
	{
		echo "<tr><th>".RES_ICON_CRYSTAL."Produktion von ".RES_CRYSTAL.":</td><td>".get_percent_string($arr['race_f_crystal'],1)."</td></tr>";
	}
	if ($arr['race_f_plastic']!=1)
	{
		echo "<tr><th>".RES_ICON_PLASTIC."Produktion von ".RES_PLASTIC.":</td><td>".get_percent_string($arr['race_f_plastic'],1)."</td></tr>";
	}
	if ($arr['race_f_fuel']!=1)
	{
		echo "<tr><th>".RES_ICON_FUEL."Produktion von ".RES_FUEL.":</td><td>".get_percent_string($arr['race_f_fuel'],1)."</td></tr>";
	}
	if ($arr['race_f_food']!=1)
	{
		echo "<tr><th>".RES_ICON_FOOD."Produktion von ".RES_FOOD.":</td><td>".get_percent_string($arr['race_f_food'],1)."</td></tr>";
	}
	if ($arr['race_f_power']!=1)
	{
		echo "<tr><th>".RES_ICON_POWER."Produktion von Energie:</td><td>".get_percent_string($arr['race_f_power'],1)."</td></tr>";
	}
	if ($arr['race_f_population']!=1)
	{
		echo "<tr><th>".RES_ICON_PEOPLE."Bevölkerungswachstum:</td><td>".get_percent_string($arr['race_f_population'],1)."</td></tr>";
	}
	if ($arr['race_f_researchtime']!=1)
	{
		echo "<tr><th>".RES_ICON_TIME."Forschungszeit:</td><td>".get_percent_string($arr['race_f_researchtime'],1,1)."</td></tr>";
	}
	if ($arr['race_f_buildtime']!=1)
	{
		echo "<tr><th>".RES_ICON_TIME."Bauzeit:</td><td>".get_percent_string($arr['race_f_buildtime'],1,1)."</td></tr>";
	}
	if ($arr['race_f_fleettime']!=1)
	{
		echo "<tr><th>".RES_ICON_TIME."Fluggeschwindigkeit:</td><td>".get_percent_string($arr['race_f_fleettime'],1)."</td></tr>";
	}
	tableEnd();
	tableStart('',500);

	echo  "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
	$res=dbQuerySave("
	SELECT
		ship_id
	FROM
		ships
	WHERE
	ship_race_id=?
	AND ship_buildable=1
	AND special_ship=0;", array($raceId));
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_array($res))
		{
			$ship = new Ship($arr['ship_id']);
			echo "<tr><td style=\"background:black;\"><img src=\"".$ship->imgPath()."\" style=\"width:40px;height:40px;border:none;\" alt=\"ship".$ship->id."\" /></td>
			<th style=\"width:180px;\">".text2html($ship->name)."</th>
			<td>".text2html($ship->shortComment)."</td></tr>";
		}
	}
	else {
		echo "<tr><td colspan=\"3\">Keine Rassenschiffe vorhanden</td></tr>";
	}

	tableEnd();
	tableStart('',500);
	echo  "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
	$res=dbQuerySave("
	SELECT
		def_id,
		def_name,
		def_shortcomment
	FROM
		defense
	WHERE
	def_race_id=?
	AND def_buildable=1;", array($raceId));
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_array($res))
		{
			$s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT;
			echo "<tr><td style=\"background:black;\"><img src=\"".$s_img."\" style=\"width:40px;height:40px;border:none;\" alt=\"def".$arr['def_id']."\" /></td>
			<th style=\"width:180px;\">".text2html($arr['def_name'])."</th>
			<td>".text2html($arr['def_shortcomment'])."</td></tr>";
		}
	}
	else {
		echo "<tr><td colspan=\"3\">Keine Rassenverteidigung vorhanden</td></tr>";
	}

	tableEnd();
	echo "<br/>";
	echo button("Rassenübersicht",$url)."&nbsp;&nbsp; ";
	
} else {

	echo "<h2>Rassen</h2>";

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
		echo "<tr>";
		//"<td class=\"tbltitle\">Logo</td>";
		echo "<td class=\"tbltitle\">Name</td>";
		echo "<td class=\"tbltitle\">Kurzbeschreibug</td></tr>";

		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr>";
			echo "<td><a href=\"?$link&amp;site=races&amp;detail=".$arr['race_id']."\">".$arr['race_name']."</a></td>";
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
		echo "<tr><th><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_metal\">".RES_METAL."</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">".RES_CRYSTAL."</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">".RES_PLASTIC."</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">".RES_FUEL."</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_food\">".RES_FOOD."</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_power\">Energie</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_population\">Wachstum</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_buildtime\">Bauzeit</a></th>";
		echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fleettime\">Fluggeschwindigkeit</a></th></tr>";

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
}
?>
