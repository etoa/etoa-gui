<?PHP

	define(TECH_SPEED_CAT,1);
	echo "<h2>Technologien</h2>";

	if ($_GET['id']!="")
	{
		if ($b_level==0) $b_level==1;
		$res = dbquery("SELECT tech_id,tech_name,tech_longcomment,tech_type_id FROM ".$db_table['technologies']." WHERE tech_id='".$_GET['id']."';");
		if ($arr = @mysql_fetch_array($res))
		{
			helpNavi(array("Technologien","research"),array(text2html($arr['tech_name']),$arr['tech_id']),1);
			echo "<select onchange=\"document.location='?page=help&site=research&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT 
				tech_id,
				tech_name 
			FROM 
				".$db_table['technologies']." 
			INNER JOIN
				".$db_table['tech_types']." 
			ON
				tech_type_id=type_id		
			WHERE 
				tech_show=1
			ORDER BY 
				type_order,
				tech_order,
				tech_name;");
			while ($barr=mysql_fetch_array($bres))		
			{
				echo "<option value=\"".$barr['tech_id']."\"";
				if ($barr['tech_id']==$_GET['id']) echo " selected=\"selected\"";
				echo ">".$barr['tech_name']."</option>";
			}
			echo "</select><br/><br/>";		

			infobox_start($arr['tech_name'],1);
			echo "<tr><th class=\"tbltitle\" style=\"width:220px;\"><img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id'].".".IMAGE_EXT."\" style=\"width:220px;height:220px;\" alt=\"Bild ".$arr['tech_name']."\" /></td>";
			echo "<td class=\"tbldata\"><div align=\"justify\">".text2html($arr['tech_longcomment'])."</div></td></tr>";
			infobox_end(1);
			if ($arr['tech_type_id']==TECH_SPEED_CAT)
			{
				$vres=dbquery("Select ship_name,ship_id,req_req_tech_level FROM ".$db_table['ship_requirements'].",".$db_table['ships']." WHERE
				req_req_tech_id=".$arr['tech_id']."
				AND req_ship_id=ship_id
				AND special_ship='0'
				GROUP BY ship_name,req_id;");
				if (mysql_num_rows($vres)>0)
				{
					infobox_start("Folgende Schiffe verwenden diesen Antrieb",1);
					while ($varr=mysql_fetch_array($vres))
					{
						echo "<tr><td class=\"tbldata\"><a href=\"?page=help&amp;site=shipyard&amp;id=".$varr['ship_id']."\">".$varr['ship_name']."</a></td><td class=\"tbldata\">ben&ouml;tigt Stufe ".$varr['req_req_tech_level']."</td></tr>";
					}
					infobox_end(1);
				}
			}
		}
		else
		  echo "Technologiedaten nicht gefunden!";
		echo "<input type=\"button\" value=\"Technologie&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=tech'\" /> &nbsp; ";
	}
	else
	{
		helpNavi(array("Technologien","research"));
		$tres=dbquery("SELECT * FROM ".$db_table['tech_types']." ORDER BY type_order,type_name;");
		if (mysql_num_rows($tres)>0)
		{
			while ($tarr=mysql_fetch_array($tres))
			{
				$res=dbquery("SELECT tech_name,tech_shortcomment,tech_id FROM ".$db_table['technologies']." WHERE tech_type_id=".$tarr['type_id']." AND tech_show=1 GROUP BY tech_id ORDER BY tech_order,tech_name;");
				if (mysql_num_rows($res)>0)
				{
					infobox_start($tarr['type_name'],1);
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr><td class=\"tbldata\" style=\"width:40px;\"><a href=\"?page=$page&site=$site&id=".$arr['tech_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Bild ".$arr['tech_name']."\" border=\"0\"/></a></td>";
						echo "<td class=\"tbltitle\" style=\"width:160px;\">".$arr['tech_name']."</td>";
						echo "<td class=\"tbldata\">".$arr['tech_shortcomment']."</td></tr>";
					}
					infobox_end(1);
				}
			}
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";
	}

?>
