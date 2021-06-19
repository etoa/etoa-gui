<?PHP

/** @var \EtoA\Race\RaceDataRepository $raceRepository */
$raceRepository = $app['etoa.race.datarepository'];
$raceNames = $raceRepository->getRaceNames();

	echo "<h2>Verteidigung</h2>";

	if (isset($_GET['id']) && intval($_GET['id']) > 0)
	{
		$did = intval($_GET['id']);

		$res = dbquery("SELECT
						`def_name`,
						`def_id`,
						`def_costs_metal`,
						`def_costs_crystal`,
						`def_costs_plastic`,
						`def_costs_fuel`,
						`def_costs_food`,
						`def_structure`,
						`def_shield`,
						`def_weapon`,
						`def_heal`,
						`def_fields`,
						`def_max_count`,
						`def_longcomment`
					FROM defense WHERE `def_id`='".$did."';");
		if ($arr = mysql_fetch_array($res))
		{
			HelpUtil::breadCrumbs(array("Verteidigung","defense"),array(text2html($arr['def_name']),$arr['def_id']),1);
			echo "<select onchange=\"document.location='?$link&amp;site=defense&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT
				def_id,
				def_name
			FROM
				defense
			WHERE
				def_show=1
			ORDER BY
				def_name;");
			while ($barr=mysql_fetch_array($bres))
			{
				echo "<option value=\"".$barr['def_id']."\"";
				if ($barr['def_id']==$did) echo " selected=\"selected\"";
				echo ">".$barr['def_name']."</option>";
			}
			echo "</select><br/><br/>";

		 	tableStart($arr['def_name']);
	   	echo "<tr><td width=\"220\" class=\"tbltitle\"><img src=\"".IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" alt=\"Verteidigung\" /></td>";
	   	echo "<td class=\"tbldata\">".text2html($arr['def_longcomment'])."</td></tr>";
			echo "<tr><td class=\"tbltitle\">Rasse</td><td class=\"tbldata\">";
				if ($arr['def_race_id']>0)
					echo $raceNames[$arr['def_race_id']]."</td></tr>";
				else
					echo "-</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</td><td class=\"tbldata\">".nf($arr['def_costs_metal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td><td class=\"tbldata\">".nf($arr['def_costs_crystal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td><td class=\"tbldata\">".nf($arr['def_costs_plastic'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td><td class=\"tbldata\">".nf($arr['def_costs_fuel'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td><td class=\"tbldata\">".nf($arr['def_costs_food'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Struktur</td><td class=\"tbldata\">".nf($arr['def_structure'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Abwehrschild</td><td class=\"tbldata\">".nf($arr['def_shield'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Schusskraft</td><td class=\"tbldata\">".nf($arr['def_weapon'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Reparatur</td><td class=\"tbldata\">".nf($arr['def_heal'])."</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Platzverbrauch</td><td class=\"tbldata\">".nf($arr['def_fields'])." Felder</td></tr>";
	    echo "<tr><td class=\"tbltitle\">Max. Anzahl</td><td class=\"tbldata\">".nf($arr['def_max_count'])."</td></tr>";
	    tableEnd();

			$otres = dbquery("
			SELECT
				d.ship_id as id,
				d.ship_name as name
			FROM
				ships d
			INNER JOIN
				obj_transforms t
				ON t.ship_id=d.ship_id
				AND t.def_id=".$arr['def_id']."
			");
			if (mysql_num_rows($otres) > 0)
			{
	    	$otarr = mysql_fetch_assoc($otres);
		    iBoxStart("Transformation");
	    	echo "Diese Verteidigungsanlage lässt sich auf ein Schiff verladen:<br/><br/>";
	    	echo "<a href=\"?$link&amp;site=shipyard&amp;id=".$otarr['id']."\">".$otarr['name']."</a>";
				iBoxEnd();

	  	}

	    iBoxStart("Technikbaum");
    	showTechTree("d",$arr['def_id']);
			iBoxEnd();

		}
		else
		  echo "Verteidigungsdaten nicht gefunden!";
		echo "<input type=\"button\" value=\"Verteidigungs&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=defense'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="defense")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Anlagen\" onclick=\"document.location='?page=defense'\" /> &nbsp; ";
	}
	else
	{
		HelpUtil::breadCrumbs(array("Verteidigung","defense"));

		if (isset($_GET['order']) && ctype_alpha($_GET['order']))
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
			$order='def_order';
			$sort="ASC";
		}

		$cres = dbquery("
		SELECT
			*
		FROM
			def_cat
		ORDER BY
			cat_order;");
		if (mysql_num_rows($cres)>0)
		{
			while ($carr = mysql_fetch_assoc($cres))
			{
				$res = dbquery("
				SELECT
					*
				FROM
					defense
				WHERE
					def_cat_id=".$carr['cat_id']."
					AND def_show=1
				ORDER BY
					$order $sort;");
				if (mysql_num_rows($res)>0)
				{
					tableStart($carr['cat_name']);

					echo "<tr>
						<th colspan=\"2\"><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=race_id\">Rasse</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=fields\">Felder</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=weapon\">Waffen</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=structure\">Struktur</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=shield\">Schild</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=heal\">Reparatur</a></th>
						<th><a href=\"?$link&amp;site=$site&amp;order=points\">Wert</a></th>
					</tr>";
					while ($arr = mysql_fetch_array($res))
					{
						$s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT;
						echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;\">
						<a href=\"?$link&site=$site&id=".$arr['def_id']."\"><img src=\"$s_img\" alt=\"Verteidigung\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
						echo "<td>
							<a href=\"?$link&site=$site&id=".$arr['def_id']."\">".$arr['def_name']."</a></td>";
						echo "<td>";
						if ($arr['def_race_id']>0)
							echo $raceNames[$arr['def_race_id']];
						else
							echo "-";
						echo "<td>".nf($arr['def_fields'])."</td>";
						echo "<td>".nf($arr['def_weapon'])."</td>";
						echo "<td>".nf($arr['def_structure'])."</td>";
						echo "<td>".nf($arr['def_shield'])."</td>";
						echo "<td>".nf($arr['def_heal'])."</td>";
						echo "<td>".nf($arr['def_points'])."</td></tr>";
					}
					tableEnd();
				}
			}
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";

}

?>
