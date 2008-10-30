<?PHP

	echo "<h2>Sterne</h2>";
	Help::navi(array("Sterne","stars"));

	if (isset($_GET['order']))
	{
		$order="sol_type_".$_GET['order'];
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
		$order="sol_type_name";
		$sort="ASC";
	}

	$res = dbquery("SELECT * FROM ".$db_table['sol_types']." ORDER BY $order $sort;");
	$nr = mysql_num_rows($res);
	if ($nr>0)
	{

		tableStart("Sternenboni");
		echo "<tr><th colspan=\"2\" ><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_metal\">".RES_METAL."</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_crystal\">".RES_CRYSTAL."</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_plastic\">".RES_PLASTIC."</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_fuel\">".RES_FUEL."</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_food\">".RES_FOOD."</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_power\">Energie</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_population\">Wachstum</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</th>";
		echo "<th><a href=\"?page=$page&amp;site=$site&amp;order=f_buildtime\">Bauzeit</th>";
		echo "</tr>";

		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr><td style=\"width:40px;background:#000;vertical-align:middle;\">
				<img src=\"".IMAGE_PATH."/stars/star".$arr['sol_type_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Stern\"/></a></td>";
				
			$tt = new ToolTip();
			$tt->addIcon(IMAGE_PATH."/stars/star".$arr['sol_type_id']."_small.".IMAGE_EXT."");
			$tt->addTitle($arr['sol_type_name']);
			$tt->addComment($arr['sol_type_comment']);
			echo "<td ".$tt."><b>".$arr['sol_type_name']."</b><br/>".$arr['sol_type_comment']."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_metal'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_crystal'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_plastic'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_fuel'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_food'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_power'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_population'],1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_researchtime'],1,1)."</td>";
			echo "<td>".get_percent_string($arr['sol_type_f_buildtime'],1,1)."</td>";
			echo "</tr>";
		}
	}
	tableEnd();


?>
