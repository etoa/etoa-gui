<?PHP

	echo "<h2>Verteidigung</h2>";
	$race=get_races_array();

	if ($_GET['id']!="")
	{
		if ($_GET['level']==0) $_GET['level']=1;
		$res = dbquery("SELECT * FROM ".$db_table['defense']." WHERE def_id='".$_GET['id']."';");
		if ($arr = @mysql_fetch_array($res))
		{
			Help::navi(array("Verteidigung","defense"),array(text2html($arr['def_name']),$arr['def_id']),1);
			echo "<select onchange=\"document.location='?page=help&site=defense&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT 
				def_id,
				def_name 
			FROM 
				".$db_table['defense']." 
			WHERE 
				def_show=1
			ORDER BY 
				def_name;");
			while ($barr=mysql_fetch_array($bres))		
			{
				echo "<option value=\"".$barr['def_id']."\"";
				if ($barr['def_id']==$_GET['id']) echo " selected=\"selected\"";
				echo ">".$barr['def_name']."</option>";
			}
			echo "</select><br/><br/>";		
			
		 	infobox_start($arr['def_name'],1);
	   	echo "<tr><td width=\"220\" class=\"tbltitle\"><img src=\"".IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" alt=\"Verteidigung\" /></td>";
	   	echo "<td class=\"tbldata\">".text2html($arr['def_longcomment'])."</td></tr>";
			echo "<tr><td class=\"tbltitle\">Rasse</td><td class=\"tbldata\">";
				if ($arr['def_race_id']>0)
					echo $race[$arr['def_race_id']]['race_name']."</td></tr>";
				else
					echo "-</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['metal']."</td><td class=\"tbldata\">".nf($arr['def_costs_metal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['crystal']."</td><td class=\"tbldata\">".nf($arr['def_costs_crystal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['plastic']."</td><td class=\"tbldata\">".nf($arr['def_costs_plastic'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['fuel']."</td><td class=\"tbldata\">".nf($arr['def_costs_fuel'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['food']."</td><td class=\"tbldata\">".nf($arr['def_costs_food'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Struktur</td><td class=\"tbldata\">".nf($arr['def_structure'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Abwehrschild</td><td class=\"tbldata\">".nf($arr['def_shield'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Schusskraft</td><td class=\"tbldata\">".nf($arr['def_weapon'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Platzverbrauch</td><td class=\"tbldata\">".nf($arr['def_fields'])." Felder</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Max. Anzahl</td><td class=\"tbldata\">".nf($arr['def_max_count'])."</td></tr>";
	    infobox_end(1);
		}
		else
		  echo "Verteidigungsdaten nicht gefunden!";
		echo "<input type=\"button\" value=\"Verteidigungs&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=defense'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="defense")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Anlagen\" onclick=\"document.location='?page=defense'\" /> &nbsp; ";
	}
	else
	{
		Help::navi(array("Verteidigung","defense"));		
		
		if ($_GET['order']!="")
		{
			$order="def_".$_GET['order'];
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
			$order="def_name";
			$sort="ASC";
		}

		$res = dbquery("SELECT * FROM ".$db_table['defense']." WHERE def_buildable=1 ORDER BY $order $sort;");
		if (mysql_num_rows($res)>0)
		{
			infobox_start("&Uuml;bersicht",1);

			echo "<tr></th><th class=\"tbltitle\" colspan=\"2\"><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=race_id\">Rasse</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=fields\">Felder</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=weapon\">Waffen</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=structure\">Struktur</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=shield\">Schild</a></th>";
			//echo "<th class=\"tbltitle\">&nbsp;</th></tr>";
			while ($arr = mysql_fetch_array($res))
			{
				$s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT;
				echo "<tr><td class=\"tbldata\" style=\"width:40px;\"><a href=\"?page=$page&site=$site&id=".$arr['def_id']."\"><img src=\"$s_img\" alt=\"Verteidigung\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
				echo "<td class=\"tbltitle\">".$arr['def_name']."</td>";
				echo "<td class=\"tbldata\">";
				if ($arr['def_race_id']>0)
					echo $race[$arr['def_race_id']]['race_name'];
				else
					echo "-";
				echo "<td class=\"tbldata\">".nf($arr['def_fields'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['def_weapon'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['def_structure'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['def_shield'])."</td></tr>";
				//echo "<td class=\"tbldata\"><a href=\"?page=$page&site=$site&id=".$arr['def_id']."\">Details</a></td></tr>";
			}
			infobox_end(1);
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";
			
}

?>