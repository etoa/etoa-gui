<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
	
	/**
	* Shows information about the planetar population
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2009 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	if ($cp)
	{

		echo "<h1>Bunker des Planeten ".$cp->name."</h1>";
		echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);
		
		// Navigation
		$tabitems = array(
			"res"=>"Rohstoffbunker",
	 		"bunker"=>"Flottenbunker",
	 		"fleet"=>"Schiffe einbunkern",
		);
	 	show_tab_menu("mode",$tabitems);
		
		$mode = (isset($_GET['mode']) && ctype_alsc($_GET['mode'])) ? $_GET['mode'] : "res";
		
		$bl = new BuildList($cp->id,$cp->id);
		$sl = new ShipList($cp->id,$cu->id);
		
		if ($mode=="fleet" || $mode=="bunker")
		{
			$res = dbquery("
			SELECT
				ship_id,
				ship_name,
				ship_shortcomment,
				ship_structure,
				special_ship
			FROM
					ships");
			while ($arr = mysql_fetch_assoc($res))
			{
				$ships[$arr['ship_id']] = $arr;
			}
		}
		
		
		if ($mode=="bunker")
		{
			if ($bl->getLevel(FLEET_BUNKER_ID)>0)
			{
				if (isset($_POST['submit_bunker_fleet']) && checker_verify())
				{
					$count = 0;
					foreach($_POST['ship_bunker_count'] as $shipId=>$cnt)
					{
						$cnt = nf_back($cnt);
						if ($cnt>0)
						{
							$count += $cnt;
							$sl->leaveShelter($shipId,$cnt);
						}
					}
					if ($count>0)
					{
						echo "<br />";
						success_msg("Schiffe wurden ausgebunkert!");
					}
				}
					
				echo "<form action=\"?page=$page&amp;mode=bunker\" method=\"post\">";
				checker_init();
				tableStart("Flottenbunker");
				echo "<tr>
						<th colspan=\"5\">Schiffe wählen:</th>
					</tr>
					<tr>
						<th colspan=\"2\">Typ</th>
						<th width=\"150\">Struktur</th>
						<th width=\"110\">Eingebunkert</th>
						<th width=\"110\">Ausbunkern</th>
					</tr>";
				
				$res = dbquery("
					SELECT
						shiplist_ship_id,
						shiplist_bunkered
					FROM
						shiplist
					WHERE
						shiplist_user_id=".$cu->id."
						AND shiplist_entity_id=".$cp->id."
						AND shiplist_bunkered>0
					;");
					$val = 0;
					$structure = 0;
					$count = 0;
					$jsAllShips = array();	// Array for selectable ships
					while ($arr = mysql_fetch_assoc($res))
					{
						if($ships[$arr['shiplist_ship_id']]['special_ship']==1)
						{
						echo "<tr>
							<td style=\"width:40px;background:#000;\">
									<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['shiplist_ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
							</td>";
						}
						else
						{
						echo "<tr>
							<td style=\"width:40px;background:#000;\">
								<a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['shiplist_ship_id']."\">
									<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['shiplist_ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
								</a>
							</td>";
						}
						echo "<td ".tm($ships[$arr['shiplist_ship_id']]['ship_name'],"<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['shiplist_ship_id']."_middle.".IMAGE_EXT."\" style=\"float:left;margin-right:5px;\">".text2html($ships[$arr['shiplist_ship_id']]['ship_shortcomment']."<br/><br style=\"clear:both;\"/>")).">".$ships[$arr['shiplist_ship_id']]['ship_name']."</td>";
						echo "<td width=\"150\">".nf($ships[$arr['shiplist_ship_id']]['ship_structure'])."</td>";
						echo "<td width=\"110\">".nf($arr['shiplist_bunkered'])."<br/>";
				  
				  echo "</td>";
				  echo "<td width=\"110\"><input type=\"text\" 
						id=\"ship_bunker_count_".$arr['shiplist_ship_id']."\" 
						name=\"ship_bunker_count[".$arr['shiplist_ship_id']."]\" 
						size=\"10\" value=\"$val\"  
						title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\" 
						onclick=\"this.select();\"
						onkeyup=\"FormatNumber(this.id,this.value,".$arr['shiplist_bunkered'].",'','');\"/>
					<br/>
					<a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').value=".$arr['shiplist_bunkered'].";document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').select()\">Alle</a> &nbsp; 
					<a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').value=0;document.getElementById('ship_count_".$arr['shiplist_ship_id']."').select()\">Keine</a></td></tr>";
					$structure += $arr['shiplist_bunkered']*$ships[$arr['shiplist_ship_id']]['ship_structure'];
					$count += $arr['shiplist_bunkered'];
					$jsAllShips["ship_bunker_count_".$arr['shiplist_ship_id']]=$arr['shiplist_bunkered'];
					}
				echo "<tr><th colspan=\"2\">Benutzt:</th><td>".nf($structure)."/".nf($bl->getBunkerFleetSpace())."</td><td>".nf($count)."/".nf($bl->getBunkerFleetCount())."</td><td >";
			
			// Select all ships button			
			echo "<a href=\"javascript:;\" onclick=\"";
			foreach ($jsAllShips as $k => $v)
			{
				echo "document.getElementById('".$k."').value=".$v.";";
			}
			echo "\">Alle wählen</a>";			
			echo "</td></tr>
				<tr><th colspan=\"2\">Verfügbar:</th><td><img src=\"misc/progress.image.php?r=1&w=100&p=".round($structure/$bl->getBunkerFleetSpace()*100)."\" alt=\"progress\" /></td>
				<td><img src=\"misc/progress.image.php?r=1&w=100&p=".round($count/$bl->getBunkerFleetCount()*100)."\" alt=\"progress\" /></td><td></td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"submit_bunker_fleet\" value=\"Ausbunkern\" />";
				echo "</form>";
			}
			else
			{
				echo "<br />";
				info_msg("Der Flottenbunker wurde noch nicht gebaut!");
			}
		}
		elseif ($mode=="fleet")
		{
			if ($bl->getLevel(FLEET_BUNKER_ID)>0)
			{
				if (isset($_POST['submit_bunker_fleet']) && checker_verify())
				{
					
					$count = $bl->getBunkerFleetCount();
					$structure = $bl->getBunkerFleetSpace();
					$countBunker = 0;
					$spaceBunker = 0;
					$counter = 0;
					$res = dbquery("
						SELECT
							shiplist_ship_id,
							shiplist_bunkered
						FROM
							shiplist
						WHERE
							shiplist_user_id=".$cu->id."
							AND shiplist_entity_id=".$cp->id."
							AND shiplist_bunkered>0
						;");
					while ($arr = mysql_fetch_assoc($res))
					{
						$count -= $arr['shiplist_bunkered'];
						$structure -= $arr['shiplist_bunkered']*$ships[$arr['shiplist_ship_id']]['ship_structure'];
					}
					
					foreach($_POST['ship_bunker_count'] as $shipId=>$cnt)
					{
						$cnt = nf_back($cnt);
						if ($cnt>0)
						{
							$countBunker = min($count,$cnt);
							$spaceBunker = $ships[$shipId]['ship_structure']>0 ? min($cnt,$structure/$ships[$shipId]['ship_structure']) : $cnt;
							$cnt = floor(min($countBunker,$spaceBunker));
							$cnt = $sl->bunker($shipId,$cnt);
							$count -= $cnt;
							$structure -= $cnt*$ships[$shipId]['ship_structure'];
							$counter += $cnt;
						}
					}
					if ($counter>0)
					{
						echo "<br />";
						success_msg("Schiffe wurden eingebunkert!");
					}
					else
					{
						echo "<br />";
						error_msg("Schiffe konnten nicht eingebunkert werden, da kein Platz mehr vorhanden war!");
					}
						
				}
				
				echo "<form action=\"?page=$page&amp;mode=fleet\" method=\"post\">";
				checker_init();
				tableStart("Vorhandene Raumschiffe");
				echo "<tr>
						<th colspan=\"5\">Schiffe wählen:</th>
					</tr>
					<tr>
						<th colspan=\"2\">Typ</th>
						<th width=\"150\">Struktur</th>
						<th width=\"110\">Anzahl</th>
						<th width=\"110\">Einbunkern</th>
					</tr>";
				
				$res = dbquery("
					SELECT
						shiplist_ship_id,
						shiplist_count
					FROM
						shiplist
					WHERE
						shiplist_user_id=".$cu->id."
						AND shiplist_entity_id=".$cp->id."
						AND shiplist_count>0
					;");
					$val = 0;
					$jsAllShips = array();	// Array for selectable ships
					while ($arr = mysql_fetch_assoc($res))
					{
						if($ships[$arr['shiplist_ship_id']]['special_ship']==1)
						{
						echo "<tr>
							<td style=\"width:40px;background:#000;\">
								<a href=\"?page=ship_upgrade&amp;id=".$arr['shiplist_ship_id']."\">
									<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['shiplist_ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
								</a>
							</td>";
						}
						else
						{
						echo "<tr>
							<td style=\"width:40px;background:#000;\">
								<a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['shiplist_ship_id']."\">
									<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['shiplist_ship_id']."_small.".IMAGE_EXT."\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
								</a>
							</td>";
						}
						echo "<td ".tm($ships[$arr['shiplist_ship_id']]['ship_name'],"<img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['shiplist_ship_id']."_middle.".IMAGE_EXT."\" style=\"float:left;margin-right:5px;\">".text2html($ships[$arr['shiplist_ship_id']]['ship_shortcomment']."<br/><br style=\"clear:both;\"/>")).">".$ships[$arr['shiplist_ship_id']]['ship_name']."</td>";
						echo "<td width=\"150\">".nf($ships[$arr['shiplist_ship_id']]['ship_structure'])."</td>";
						echo "<td width=\"110\">".nf($arr['shiplist_count'])."<br/>";
				  
				  echo "</td>";
				  echo "<td width=\"110\"><input type=\"text\" 
						id=\"ship_bunker_count_".$arr['shiplist_ship_id']."\" 
						name=\"ship_bunker_count[".$arr['shiplist_ship_id']."]\" 
						size=\"10\" value=\"$val\"  
						title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\" 
						onclick=\"this.select();\" 
						onkeyup=\"FormatNumber(this.id,this.value,".$arr['shiplist_count'].",'','');\"/>
					<br/>
					<a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').value=".$arr['shiplist_count'].";document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').select()\">Alle</a> &nbsp; 
					<a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').value=0;document.getElementById('ship_bunker_count_".$arr['shiplist_ship_id']."').select()\">Keine</a></td></tr>";
					$jsAllShips["ship_bunker_count_".$arr['shiplist_ship_id']]=$arr['shiplist_count'];
				}
				echo "<tr><td colspan=\"3\"><td><td >";
			
			// Select all ships button			
			echo "<a href=\"javascript:;\" onclick=\"";
			foreach ($jsAllShips as $k => $v)
			{
				echo "document.getElementById('".$k."').value=".$v.";";
			}
			echo "\">Alle wählen</a>";			
			echo "</td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"submit_bunker_fleet\" value=\"Einbunkern\" />";
				echo "</form>";
			}
			else
			{
				echo "<br />";
				info_msg("Der Flottenbunker wurde noch nicht gebaut!");
			}
		}
		else
		{
			if ($bl->getLevel(RES_BUNKER_ID)>0)
			{
				if (isset($_POST['submit_bunker_res']) && checker_verify())
				{
					$sum = nf_back($_POST['bunker_metal']) + nf_back($_POST['bunker_crystal']) + nf_back($_POST['bunker_plastic']) + nf_back($_POST['bunker_fuel']) + nf_back($_POST['bunker_food']);
					$percent = $sum/$bl->getBunkerRes();
					if ($percent<1) $percent =1;
					$cp->chgBunker(1,nf_back($_POST['bunker_metal'])/$percent);
					$cp->chgBunker(2,nf_back($_POST['bunker_crystal'])/$percent);
					$cp->chgBunker(3,nf_back($_POST['bunker_plastic'])/$percent);
					$cp->chgBunker(4,nf_back($_POST['bunker_fuel'])/$percent);
					$cp->chgBunker(5,nf_back($_POST['bunker_food'])/$percent);
					
					echo "<br />";
					success_msg("Änderungen wurden übernommen!");
				}
		
				//
				// Rohstoffbunker
				//
				$bunkered = $cp->bunkerMetal + $cp->bunkerCrystal + $cp->bunkerPlastic + $cp->bunkerFuel + $cp->bunkerFood;
				echo "<form action=\"?page=$page\" method=\"post\">";
				checker_init();
				tableStart("Rohstoffbunker",400);
				echo "
				<tr><th style=\"width:150px\">".RES_ICON_METAL."".RES_METAL."</th>
				<td><input type=\"text\" id=\"bunker_metal\" name=\"bunker_metal\" value=\"".nf($cp->bunkerMetal)."\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th style=\"width:150px\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
					<td><input type=\"text\" id=\"bunker_crysttal\" name=\"bunker_crystal\" value=\"".nf($cp->bunkerCrystal)."\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th style=\"width:150px\">".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
					<td><input type=\"text\" id=\"bunker_plastic\" name=\"bunker_plastic\" value=\"".nf($cp->bunkerPlastic)."\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th style=\"width:150px\">".RES_ICON_FUEL."".RES_FUEL."</th>
					<td><input type=\"text\" id=\"bunker_fuel\" name=\"bunker_fuel\" value=\"".nf($cp->bunkerFuel)."\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th style=\"width:150px\">".RES_ICON_FOOD."".RES_FOOD."</th>
					<td><input type=\"text\" id=\"bunker_food\" name=\"bunker_food\" value=\"".nf($cp->bunkerFood)."\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th style=\"width:150px\">Benutzt:</th>
				<td>".nf($bunkered)."/".nf($bl->getBunkerRes())."</td></tr>
				<tr><th>Verfügbar:</th><td><img src=\"misc/progress.image.php?r=1&w=100&p=".round($bunkered/$bl->getBunkerRes()*100)."\" alt=\"progress\" /></td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"submit_bunker_res\" value=\"Speichern\" />";
				echo "</form>";
			}
			else
			{
				echo "<br />";
				info_msg("Der Rohstoffbunker wurde noch nicht gebaut!");
			}
		}
	}
?>