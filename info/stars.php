<?PHP

	echo "<h2>Sterne</h2>";
	Help::navi(array("Sterne","stars"));

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
	$nr = mysql_num_rows($res);
	if ($nr>0)
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
		echo "<td class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=f_buildtime\">Bauzeit</td>";
		echo "</tr>";

		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;vertical-align:middle;\">
				<img src=\"".IMAGE_PATH."/galaxy/sol".$arr['type_id'].".gif\" width=\"40\" height=\"40\" alt=\"Stern\"/></a></td>";
			echo "<td class=\"tbltitle\" style=\"width:300px;\">
				".$arr['type_name']."<br/>
			<span style=\"font-weight:500;font-size:8pt;\">".$arr['type_comment']."</span></td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_metal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_crystal'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_plastic'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_fuel'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_food'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_power'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_population'],1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_researchtime'],1,1)."</td>";
			echo "<td class=\"tbldata\">".get_percent_string($arr['type_f_buildtime'],1,1)."</td>";
			//$rating = ($arr['type_f_metal']+$arr['type_f_crystal']+$arr['type_f_plastic']+$arr['type_f_fuel']+
			//$arr['type_f_food']+$arr['type_f_power']+$arr['type_f_population']-
			//$arr['type_f_researchtime']-$arr['type_f_buildtime']-5);
			//echo "<td class=\"tbldata\">".$rating."</td>";
			echo "</tr>";
			
			$cnt_m += $arr['type_f_metal']-1;
			$cnt_c += $arr['type_f_crystal']-1;
			$cnt_p += $arr['type_f_plastic']-1;
			$cnt_fu += $arr['type_f_fuel']-1;
			$cnt_fo += $arr['type_f_food']-1;
			$cnt_pow += $arr['type_f_power']-1;
			$cnt_pop += $arr['type_f_population']-1;
			$cnt_res += $arr['type_f_researchtime']-1;
			$cnt_bui += $arr['type_f_buildtime']-1;
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
