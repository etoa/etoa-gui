<?PHP

	echo "<h2>Sterne</h2>";
	helpNavi(array("Sterne","stars"));

	if ($_GET['order']!="")
	{
		$order="type_".$_GET['order'];
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
		$order="type_name";
		$sort="ASC";
	}

	$res = dbquery("SELECT * FROM ".$db_table['sol_types']." ORDER BY $order $sort;");
	if (mysql_num_rows($res)>0)
	{

		infobox_start("Sternenboni",1);
		echo "<tr><td class=\"tbltitle\" colspan=\"2\"><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_metal\">".$rsc['metal']."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_crystal\">".$rsc['crystal']."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_plastic\">".$rsc['plastic']."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_fuel\">".$rsc['fuel']."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_food\">".$rsc['food']."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_power\">Energie</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_population\">Wachstum</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_buildtime\">Bauzeit</td></tr>";

		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\" style=\"width:40px;\"><img src=\"".IMAGE_PATH."/galaxy/sol".$arr['type_id'].".gif\" width=\"40\" height=\"40\" alt=\"Stern\"/></a></td>";
			echo "<td class=\"tbltitle\" ".tm("Info",text2html($arr['type_comment'])).">".$arr['type_name']."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_metal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_crystal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_plastic'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_fuel'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_food'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_power'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_population'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_researchtime'],1,1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_buildtime'],1,1)."</td></tr>";
		}
	}
	infobox_end(1);


?>
