<?PHP

	echo "<h2>Sterne</h2>";
	Help::navi(array("Sterne","stars"));

	if ($_GET['order']!="")
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

		infobox_start("Sternenboni",1);
		echo "<tr><td class=\"tbltitle\" colspan=\"2\" ><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></td>";
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
			//$star = new Star();
			
			echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;vertical-align:middle;\">
				<img src=\"".IMAGE_PATH."/stars/star".$arr['sol_type_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Stern\"/></a></td>";
				
			$tt = new ToolTip();
			$tt->addIcon(IMAGE_PATH."/stars/star".$arr['sol_type_id']."_small.".IMAGE_EXT."");
			$tt->addTitle($arr['sol_type_name']);
			$tt->addComment($arr['sol_type_comment']);
			echo "<td class=\"tbltitle\" ".$tt.">".$arr['sol_type_name']."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_metal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_crystal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_plastic'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_fuel'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_food'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_power'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_population'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_researchtime'],1,1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['sol_type_f_buildtime'],1,1)."</td>";
			//$rating = ($arr['sol_type_f_metal']+$arr['sol_type_f_crystal']+$arr['sol_type_f_plastic']+$arr['sol_type_f_fuel']+
			//$arr['sol_type_f_food']+$arr['sol_type_f_power']+$arr['sol_type_f_population']-
			//$arr['sol_type_f_researchtime']-$arr['sol_type_f_buildtime']-5);
			//echo "<td class=\"tbldata\">".$rating."</td>";
			echo "</tr>";
			
			$cnt_m += $arr['sol_type_f_metal']-1;
			$cnt_c += $arr['sol_type_f_crystal']-1;
			$cnt_p += $arr['sol_type_f_plastic']-1;
			$cnt_fu += $arr['sol_type_f_fuel']-1;
			$cnt_fo += $arr['sol_type_f_food']-1;
			$cnt_pow += $arr['sol_type_f_power']-1;
			$cnt_pop += $arr['sol_type_f_population']-1;
			$cnt_res += $arr['sol_type_f_researchtime']-1;
			$cnt_bui += $arr['sol_type_f_buildtime']-1;
			$cnt_rat += $rating;
		}
	}
	/*
	echo "<tr><th colspan=\"2\" class=\"tbltitle\">Summe</th>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_m+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_c+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_p+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_fu+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_fo+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_pow+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_pop+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_res+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string($cnt_bui+1,1)."</td>";
	echo "<td class=\"tbldata\">".$cnt_rat."</td>";
	echo "</tr>";
	echo "<tr><th colspan=\"2\" class=\"tbltitle\">Durchschnitt</th>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_m/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_c/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_p/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_fu/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_fo/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_pow/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_pop/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_res/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".get_percent_string(($cnt_bui/$nr)+1,1)."</td>";
	echo "<td class=\"tbldata\">".round($cnt_rat/$nr,2)."</td>";
	echo "</tr>"; */

	infobox_end(1);


?>
