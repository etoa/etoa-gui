 	<?PHP

	define("RANKING_SHIP_STRUCTURE",20000);
	define("RANKING_SHIP_SHIELD",25000);
	define("RANKING_SHIP_WEAPON",50000);
	define("RANKING_SHIP_SPEED",5000);
	define("RANKING_SHIP_CAPACITY",100000);
	define("RANKING_SHIP_FUEL",60);

	function rankingStars($val,$max2)
	{
		$max = $max2;
		$img = "star_r";
		
		$t = $max / 5;
		$s="";
		for ($x=0;$x<5;$x++)
		{
			if ($val==0)
				$s.= "<img src=\"images/star_g.gif\" />";
			elseif ($val > 3*$max)
				$s.= "<img src=\"images/star_y.gif\" />";
			elseif ($val > $t*$x)
				$s.= "<img src=\"images/".$img.".gif\" />";
			else
				$s.= "<img src=\"images/star_g.gif\" />";
		}
		return $s;
	}

	function shipRanking($arr)
	{
		ob_start();
		echo "<table class=\"tb\">";
		echo "<tr><th>Struktur:</th><td>".rankingStars($arr['ship_structure'],RANKING_SHIP_STRUCTURE)."</td></tr>";
		echo "<tr><th>Schilder:</th><td>".rankingStars($arr['ship_shield'],RANKING_SHIP_SHIELD)."</td></tr>";
		echo "<tr><th>Waffen:</th><td>".rankingStars($arr['ship_weapon'],RANKING_SHIP_WEAPON)."</td></tr>";
		echo "<tr><th>Speed:</th><td>".rankingStars($arr['ship_speed'],RANKING_SHIP_SPEED)."</td></tr>";
		echo "<tr><th>Kapazität:</th><td>".rankingStars($arr['ship_capacity'],RANKING_SHIP_CAPACITY)."</td></tr>";		
		echo "<tr><th>Reisekosten:</th><td>".rankingStars($arr['ship_fuel_use'],RANKING_SHIP_FUEL)."</td></tr>";		
		echo "</table>";
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}

	echo "<h2>Raumschiffe</h2>";

	$race=get_races_array();

	//
	// Details
	//
	if (isset($_GET['id']))
	{
		$sid = intval($_GET['id']);
		
		$res = dbquery("
		SELECT 
			* 
		FROM 
			ships 
		LEFT JOIN
			ship_cat
			ON ship_cat_id=cat_id
		WHERE 
			ship_id='".$sid."' 
			AND ship_show=1
		;");
		if ($arr = @mysql_fetch_array($res))
		{
			HelpUtil::breadCrumbs(array("Schiffe","shipyard"),array(text2html($arr['ship_name']),$arr['ship_id']),1);
			echo "<select onchange=\"document.location='?page=help&site=shipyard&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT 
				ship_id,
				ship_name
			FROM 
				ships
			WHERE
				ship_show=1 
				AND special_ship=0
			ORDER BY 
				ship_name;");
			while ($barr=mysql_fetch_array($bres))		
			{
				echo "<option value=\"".$barr['ship_id']."\"";
				if ($barr['ship_id']==$sid) echo " selected=\"selected\"";
				echo ">".$barr['ship_name']."</option>";
			}
			echo "</select><br/><br/>";		
			
			// Load propulsion data
			$vres=dbquery("
			Select 
				tech_id,
				tech_name,
				req_level 
			FROM 
				ship_requirements,
				technologies 
			WHERE
				obj_id=".$arr['ship_id']."
				AND tech_type_id='".TECH_SPEED_CAT."'
				AND req_tech_id=tech_id
				GROUP BY id;
			");

			tableStart($arr['ship_name']);

    	echo "<tr>
    		<td class=\"tbltitle\" style=\"width:220px;background:#000\">
    			<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" alt=\"Schiff\" />
    		</td>
    		<td class=\"tbldata\" colspan=\"3\">
    			".text2html($arr['ship_longcomment'])."
    		</td>
    	</tr>";

    	if ($arr['ship_race_id']>0)
    	{
    		echo "<tr><th class=\"tbltitle\">Rasse:</th><td colspan=\"3\" class=\"tbldata\">Dieses Schiff kann exklusiv nur durch das Volk der <b>".$race[$arr['ship_race_id']]['race_name']."</b> gebaut werden!</td></tr>";
    	}
    	
    	echo "<tr>
    		<th class=\"tbltitle\">Bewertung:
    		</th><td class=\"tbldata\" colspan=\"3\">
    			".shipRanking($arr)."
    		</td>
    	</tr>";

    	echo "<tr><th class=\"tbltitle\">Kategorie:</th><td class=\"tbldata\" colspan=\"3\">".$arr['cat_name']."</td></tr>";
    	echo "<tr><th class=\"tbltitle\">Anzahl Piloten:</th><td class=\"tbldata\" colspan=\"3\">".nf($arr['ship_pilots'])."</td></tr>";
    	
    	echo "<tr><td colspan=\"4\" style=\"height:30px;\"></td></tr>";

	    echo "<tr><th class=\"tbltitle\" colspan=\"2\" style=\"text-align:center\">Kosten</th>
	    			<th class=\"tbltitle\" colspan=\"2\" style=\"text-align:center\">Technische Daten</th></tr>";

	    echo "<tr>
	    	<td style=\"padding:0px;\">
	    	<table class=\"tb\">";
			echo "<tr>
						<td style=\"width:170px;font-weight:bold;\" class=\"resmetal\">".RES_METAL."</td>
						<td style=\"width:350px\">".nf($arr['ship_costs_metal'])." t</td>    	
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"rescrystal\">".RES_CRYSTAL."</td>
						<td style=\"width:350px\">".nf($arr['ship_costs_crystal'])." t</td>
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"resplastic\">".RES_PLASTIC."</td>
						<td style=\"width:350px\">".nf($arr['ship_costs_plastic'])." t</td>
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"resfuel\">".RES_FUEL."</td>
						<td style=\"width:350px\">".nf($arr['ship_costs_fuel'])." t</td>
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"resfood\">".RES_FOOD."</td>
						<td style=\"width:350px\">".nf($arr['ship_costs_food'])." t</td>
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"resfuel\">/100 AE</td>
						<td style=\"width:350px\">".nf($arr['ship_fuel_use'])." t</td>
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"resfuel\">Start</td>
						<td style=\"width:350px\">".nf($arr['ship_fuel_use_launch'])." t</td>
				</tr>";
			echo "<tr>
						<td style=\"font-weight:bold;\" class=\"resfuel\">Landung</td>
						<td style=\"width:350px\">".nf($arr['ship_fuel_use_landing'])." t</td>
				</tr>";
	    	
	    	echo "</table>
	    	</td>
	    	<td  colspan=\"3\" style=\"padding:0px;\">
				<table class=\"tb\" style=\"width:100%\">";
				echo "<tr>
						<td style=\"font-weight:bold;\">Struktur</td>
						<td>".nf($arr['ship_structure'])."</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Schutzschild</td>
						<td>".nf($arr['ship_shield'])."</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Waffen</td>
						<td>".nf($arr['ship_weapon'])."</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Heilung</td>
						<td>".nf($arr['ship_heal'])."</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Antriebstechnologie</td>
						<td>";
						if (mysql_num_rows($vres)>0)
						{
							while ($varr=mysql_fetch_array($vres))
							{
								echo "<a href=\"?page=help&amp;site=research&amp;id=".$varr['tech_id']."\">".$varr['tech_name']."</a> (Stufe ".$varr['req_level'].")<br/>";
							}
						}
	    			echo "</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Laderaum</td>
						<td>".nf($arr['ship_capacity'])." t</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Passagierraum</td>
						<td>".nf($arr['ship_people_capacity'])."</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Geschwindigkeit</td>
						<td>".nf($arr['ship_speed']/FLEET_FACTOR_F)." AE/h</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Startdauer</td>
						<td>".tf($arr['ship_time2start']/FLEET_FACTOR_S)."</td></tr>";
				echo "<tr>
						<td style=\"font-weight:bold;\">Landedauer</td>
						<td>".tf($arr['ship_time2land']/FLEET_FACTOR_L)."</td></tr>";
				echo "</table>	    	
	    	</td></tr>";

			

			
			echo "<tr><td colspan=\"4\" style=\"height:30px;\"></td></tr>";
			
			echo "<tr><th class=\"tbltitle\" colspan=\"4\" style=\"text-align:center\">Fähigkeiten</th></tr>";			
			
			$actions = explode(",",$arr['ship_actions']);
			$accnt=0;
			if (count($actions)>0)
			{
				echo "<tr><td colspan=\"4\" style=\"padding:0px\">
				<table class=\"tb\" style=\"width:100%\">";
				foreach ($actions as $i)
				{
					if ($ac = FleetAction::createFactory($i))
					{
						echo "<tr>
							<td class=\"tbldata\" style=\"width:150px;\">".$ac."</td>
							<td class=\"tbldata\">".$ac->desc()."</td>
							<td class=\"tbldata\" style=\"width:150px;\" ><a href=\"?page=help&site=action&action=".$i."\">Details</a></td></tr>";
							$accnt++;
					}
				}
				echo "</table>";
				echo "</td></tr>";
			}
			if ($accnt==0)
				echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center\">Keine Spezialfähigkeit vorhanden!</td></tr>";

			echo "<tr><td colspan=\"4\" style=\"height:30px;\"></td></tr>";
		
		
			echo "<tr><th class=\"tbltitle\" colspan=\"4\" style=\"text-align:center\">Voraussetzungen</th></tr>";			
			echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center\">";
			echo "<div id=\"reqInfo\" style=\"width:100%;\">
			<br/><div class=\"loadingMsg\">Bitte warten...</div>
			</div>";
			$shipCatId = $arr['ship_cat_id'];
			$shipCatIdAlliance = 6;
			$shipCatAbbr = $shipCatId == $shipCatIdAlliance ? "sa" : "s";
			echo '<script type="text/javascript">xajax_reqInfo('.$arr['ship_id'].',"'. $shipCatAbbr.'")</script>';
			echo "</td></tr>";	
		
	    tableEnd();
		}
		else
		  echo "Schiffdaten nicht gefunden!<br><br>";

		echo "<input type=\"button\" value=\"Schiff&uuml;bersicht\" onclick=\"document.location='?$link&site=$site'\" /> &nbsp; ";
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=ships'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="haven")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zum Hafen\" onclick=\"document.location='?page=haven'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="shipyard")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumschiffwerft\" onclick=\"document.location='?page=shipyard'\" /> &nbsp; ";

	}

	//
	// Übersicht
	//
	else
	{
		HelpUtil::breadCrumbs(array("Schiffe","shipyard"));
	
		if (isset($_GET['order']))
		{
			if (ctype_alpha($_GET['order']))
			{
				$order="ship_".$_GET['order'];
			}
			else if ($_GET['order']==="race_id")
			{
				$order="race_name";
			}
			else
			{
				$order="ship_order";
			}
			if ($_SESSION['help']['orderfield']==$_GET['order'])
			{
				if ($_SESSION['help']['ordersort']=="DESC")
					$sort="ASC";
				else
					$sort="DESC";
			}
			else
			{
				if (($_GET['order']==="name") || ($_GET['order']==="race_id"))
					$sort="ASC";
				else
					$sort="DESC";
			}
			$_SESSION['help']['orderfield']=$_GET['order'];
			$_SESSION['help']['ordersort']=$sort;
		}
		else
		{
			$order="ship_order";
			$sort="ASC";
		}

		$cres = dbquery("
		SELECT 
			* 
		FROM 
			ship_cat
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
					ships
				LEFT JOIN
					races
				ON
					ship_race_id=race_id
				WHERE
					ship_cat_id=".$carr['cat_id']."
					AND ship_show=1 
				ORDER BY 
					$order $sort;");
				if (mysql_num_rows($res)>0)
				{
					tableStart($carr['cat_name']);
					echo "<tr><th colspan=\"2\"><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=race_id\">Rasse</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=capacity\">Kapazität</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=speed\">Speed</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=fuel_use\">Treibstoff</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=weapon\">Waffen</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=structure\">Struktur</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=shield\">Schild</a></th>";
					echo "<th><a href=\"?$link&amp;site=$site&amp;order=pilots\">Piloten</a></th>
					<th><a href=\"?$link&amp;site=$site&amp;order=points\">Wert</a></th>
					</tr>";
					while ($arr = mysql_fetch_array($res))
					{
						$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT;
						echo "<tr><td style=\"background:#000;width:40px;\">
						<a href=\"?$link&site=$site&id=".$arr['ship_id']."\">
						<img src=\"$s_img\" alt=\"Schiffbild\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
						echo "<td ".tm($arr['ship_name'],text2html($arr['ship_shortcomment'])."<br/><br/>".shipRanking($arr)).">
							<a href=\"?$link&site=$site&id=".$arr['ship_id']."\">".$arr['ship_name']."</a>
						</td>";
						echo "<td>";
						if ($arr['ship_race_id']>0)
							echo $race[$arr['ship_race_id']]['race_name'];
						else
							echo "-";
						echo "<td>".nf($arr['ship_capacity'])."</td>";
						echo "<td>".nf($arr['ship_speed'])."</td>";
						echo "<td>".nf($arr['ship_fuel_use'])."</td>";
						echo "<td>".nf($arr['ship_weapon'])."</td>";
						echo "<td>".nf($arr['ship_structure'])."</td>";
						echo "<td>".nf($arr['ship_shield'])."</td>";
						echo "<td>".nf($arr['ship_pilots'])."</td>";
						echo "<td>".nf($arr['ship_points'])."</td>
						</tr>";
					}
					tableEnd();
				}
			}
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";
	}
?>
