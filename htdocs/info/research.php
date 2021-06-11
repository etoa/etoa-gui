<?PHP

	define(TECH_SPEED_CAT,1);
	echo "<h2>Technologien</h2>";

	//Detail

	if (isset($_GET['id']))
	{
		$tid = intval($_GET['id']);

		$res = dbquery("
		SELECT
			tech_id,
			tech_name,
			tech_longcomment,
			tech_type_id,
			tech_costs_metal,
			tech_costs_crystal,
			tech_costs_plastic,
			tech_costs_food,
			tech_costs_fuel,
			tech_build_costs_factor,
			tech_last_level
		FROM
			technologies
		WHERE
      tech_show=1
    AND
			tech_id='".$tid."'
			;");

		if ($arr = @mysql_fetch_array($res))
		{
			HelpUtil::breadCrumbs(array("Technologien","research"),array(text2html($arr['tech_name']),$arr['tech_id']),1);
			echo "<select onchange=\"document.location='?$link&site=research&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT
				tech_id,
				tech_name
			FROM
				technologies
			INNER JOIN
				tech_types
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
				if ($barr['tech_id']==$tid) echo " selected=\"selected\"";
				echo ">".$barr['tech_name']."</option>";
			}
			echo "</select><br/><br/>";

			tableStart($arr['tech_name']);
			echo "<tr><th class=\"tbltitle\" style=\"width:220px;\" rowspan=\"2\"><img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id'].".".IMAGE_EXT."\" style=\"width:220px;height:220px;\" alt=\"Bild ".$arr['tech_name']."\" /></td>";
			echo "<td class=\"tbldata\" colspan=\"2\"><div align=\"justify\">".text2html($arr['tech_longcomment'])."</div></td></tr>";
			echo "<tr>
				<td class=\"tbltitle\" style=\"height:20px;width:120px;\">Maximale Stufe:</td>
				<td class=\"tbldata\" style=\"height:20px;\">".$arr['tech_last_level']."</td>
			</tr>";
			tableEnd();

			if ($arr['tech_type_id']==TECH_SPEED_CAT)
			{
				$vres=dbquery("
				SELECT
					ship_name,
					ship_id,
					req_level
				FROM
					ship_requirements,
					ships
				WHERE
					req_tech_id=".$arr['tech_id']."
				AND obj_id=ship_id
				AND special_ship='0'
				GROUP BY
					ship_name,id;");

				if (mysql_num_rows($vres)>0)
				{
					tableStart("Folgende Schiffe verwenden diesen Antrieb");
					while ($varr=mysql_fetch_array($vres))
					{
						echo "<tr><td class=\"tbldata\"><a href=\"?$link&amp;site=shipyard&amp;id=".$varr['ship_id']."\">".$varr['ship_name']."</a></td><td class=\"tbldata\">ben&ouml;tigt Stufe ".$varr['req_level']."</td></tr>";
					}
					tableEnd();
				}
			}

			// Kostenentwicklung
			tableStart ("Kostenentwicklung (Faktor: ".$arr['tech_build_costs_factor'].")");
      echo "<tr><th class=\"tbltitle\" style=\"text-align:center;\">Level</th>
      			<th class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</th>
      			<th class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
      			<th class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
      			<th class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</th>
      			<th class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</th></tr>";
      for ($x=0;$x<min(30,$arr['tech_last_level']);$x++)
      {
      	$bc = calcTechCosts($arr,$x);
      	echo '<tr><td class="tbldata">'.($x+1).'</td>
      				<td class="tbldata" style="text-align:right;">'.nf($bc['metal']).'</td>
      				<td class="tbldata" style="text-align:right;">'.nf($bc['crystal']).'</td>
      				<td class="tbldata" style="text-align:right;">'.nf($bc['plastic']).'</td>
      				<td class="tbldata" style="text-align:right;">'.nf($bc['fuel']).'</td>
      				<td class="tbldata" style="text-align:right;">'.nf($bc['food']).'</td></tr>';
      }
      tableEnd();

			iBoxStart("Technikbaum");
	    showTechTree("t",$arr['tech_id']);
			iBoxEnd();

		}
		else
		  echo "Technologiedaten nicht gefunden!";
		echo "<input type=\"button\" value=\"Technologie&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
		if (!$popup)
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=tech'\" /> &nbsp; ";
	}

	//�bersicht

	else
	{
		HelpUtil::breadCrumbs(array("Technologien","research"));
		$tres=dbquery("SELECT * FROM tech_types ORDER BY type_order,type_name;");
		if (mysql_num_rows($tres)>0)
		{
			while ($tarr=mysql_fetch_array($tres))
			{
				$res=dbquery("SELECT tech_name,tech_shortcomment,tech_id FROM technologies WHERE tech_type_id=".$tarr['type_id']." AND tech_show=1 GROUP BY tech_id ORDER BY tech_order,tech_name;");
				if (mysql_num_rows($res)>0)
				{
					tableStart($tarr['type_name']);
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr>
							<td style=\"width:40px;padding:0px;background:#000\">
								<a href=\"?$link&amp;site=$site&amp;id=".$arr['tech_id']."\">
									<img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Bild ".$arr['tech_name']."\" border=\"0\"/>
								</a>
							</td>";
						echo "<td style=\"width:160px;\">
							<a href=\"?$link&amp;site=$site&amp;id=".$arr['tech_id']."\">".$arr['tech_name']."</a></td>";
						echo "<td>".$arr['tech_shortcomment']."</td></tr>";
					}
					tableEnd();
				}
			}
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";
	}

?>
