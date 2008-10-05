<?PHP

	echo "<h2>Planeten</h2>";
	Help::navi(array("Planeten","planets"));

	if (isset($_GET['order']))
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

	$res = dbquery("SELECT * FROM ".$db_table['planet_types']." ORDER BY $order $sort;");
	if (mysql_num_rows($res)>0)
	{

		infobox_start("Planetenboni",1);
		echo "<tr><td class=\"tbltitle\" colspan=\"2\"><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_metal\">".RES_METAL."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_crystal\">".RES_CRYSTAL."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_plastic\">".RES_PLASTIC."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_fuel\">".RES_FUEL."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_food\">".RES_FOOD."</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_power\">Energie</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_population\">Wachstum</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</td>";
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_buildtime\">Bauzeit</td>";
		echo "</tr>";

		while ($arr = mysql_fetch_array($res))
		{
			$x=mt_rand(1,5);
		
			echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;\">";

			$tt = new ToolTip();
			$tt->addImage(IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['type_id']."_".$x.".gif");
			echo "<img src=\"".IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['type_id']."_".$x."_small.gif\" width=\"40\" height=\"40\" alt=\"planet\" border=\"0\" / ".$tt."></td>";

			$tt = new ToolTip();
			$tt->addIcon(IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['type_id']."_".$x."_small.gif");
			$tt->addTitle($arr['type_name']);
			if ($arr['type_habitable']==1) 
				$tt->addGoodCond("Bewohnbar");
			else
				$tt->addBadCond("Unbewohnbar");
			if ($arr['type_collect_gas']==1)
				$tt->addGoodCond("Ermöglich ".RES_FUEL."abbau");
			$tt->addComment($arr['type_comment']);
			echo "<td class=\"tbltitle\" ".$tt.">".$arr['type_name']."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_metal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_crystal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_plastic'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_fuel'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_food'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_power'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_population'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_researchtime'],1,1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_buildtime'],1,1)."</td>";
			echo "</tr>";
		}
	}
	infobox_end(1);


?>
