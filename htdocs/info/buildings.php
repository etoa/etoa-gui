<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

if ($site!=Null)
	define("SHOWLEVELS",30);
else
	define("SHOWLEVELS",5);


echo "<h2>Geb&auml;ude</h2>";

if (isset($_GET['id']) && intval($_GET['id']) > 0)
{
	$bid = intval($_GET['id']);

	$b_level = 1;

	$res = dbquery("SELECT * FROM buildings WHERE building_show=1 AND building_id='".$bid."';");
	if ($arr = @mysql_fetch_array($res))
	{
		HelpUtil::breadCrumbs(array("Geb&auml;ude","buildings"),array(text2html($arr['building_name']),$arr['building_id']),1);
		echo "<select onchange=\"document.location='?$link&amp;site=buildings&id='+this.options[this.selectedIndex].value\">";
		$bres=dbquery("SELECT
			building_id,
			building_name
		FROM
			buildings
		INNER JOIN
			building_types
		ON
			building_type_id=type_id
		WHERE
			building_show=1
		ORDER BY
			type_order,
			building_order,
			building_name;");
		while ($barr=mysql_fetch_array($bres))
		{
			echo "<option value=\"".$barr['building_id']."\"";
			if ($barr['building_id']==$bid)
				echo " selected=\"selected\"";
			echo ">".$barr['building_name']."</option>";
		}
		echo "</select><br/><br/>";

		$currentLevel = 0;
		if (isset($cu) && isset($cp))
		{
			$res_level = dbquery("
			SELECT
				buildlist_current_level
			FROM
				buildlist
			WHERE
				buildlist_building_id ='".$bid."'
				AND buildlist_user_id='".$cu->id."'
				AND buildlist_entity_id='".$cp->id."';");
			if(mysql_num_rows($res_level)>0)
			{
				$arr_level = mysql_fetch_row($res_level);
				$currentLevel = $arr_level[0];
			}
		}

		tableStart(text2html($arr['building_name']));
		echo "<tr>
			<th style=\"width:220px;background:#000;padding:0px;\" rowspan=\"2\">
				<img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id'].".".IMAGE_EXT."\" style=\"width:220px;height:220px;background:#000;margin:0px;\" align=\"top\" alt=\"Bild ".$arr['building_name']."\" />
			</th>
			<td colspan=\"2\">
				<div align=\"justify\">".text2html($arr['building_longcomment'])."</div>
			</td>
		</tr>
		<tr>
			<th style=\"height:20px;width:120px;\">Maximale Stufe:</th>
			<td style=\"height:20px;\">".$arr['building_last_level']."</td>
		</tr>";
		tableEnd();

		// Metallmine
    if ($arr['building_id']==1)
    {
	    tableStart("Produktion von ".RES_METAL." (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	        $prod_item = round($arr['building_prod_metal'] * pow($arr['building_production_factor'],$level-1));
	        $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
	        else
	            echo "<tr><td>$level</td><td>".nf($prod_item)."</td><td>".nf($power_use)."</td></tr>";
	    }
			tableEnd();
    }

    // Siliziummine
    elseif ($arr['building_id']==2)
    {
       tableStart("Produktion von ".RES_CRYSTAL." (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_crystal'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
	        else
	            echo "<tr><td>$level</td><td>".nf($prod_item)."</td><td>".nf($power_use)."</td></tr>";
	    }
	    tableEnd();
    }

    // Chemiefabrik
    elseif ($arr['building_id']==3)
    {
    	tableStart("Produktion von ".RES_PLASTIC." (ohne Boni)");
    	echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
    	for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
    	{
      	$prod_item = round($arr['building_prod_plastic'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
        if($level==$currentLevel)
            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
        else
            echo "<tr><td>$level</td><td>".nf($prod_item)."</td><td>".nf($power_use)."</td></tr>";
  	  }
    	tableEnd();
    }

  	// Tritiumsynthetizer
 		elseif ($arr['building_id']==4)
    {
	    tableStart("Produktion von ".RES_FUEL." (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_fuel'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
	        else
	            echo "<tr><td>$level</td><td>".nf($prod_item)."</td><td>".nf($power_use)."</td></tr>";
	    }
	    tableEnd();
   	}

    // Gew&auml&auml;chshaus
    elseif ($arr['building_id']==5)
    {
      tableStart("Produktion von ".RES_FOOD." (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_food'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
	        else
	            echo "<tr><td>$level</td><td>".nf($prod_item)."</td><td>".nf($power_use)."</td></tr>";
	    }
	    tableEnd();
    }

    // Planetenbasis
    elseif ($arr['building_id']==6)
    {
	    tableStart("Produktion (ohne Boni)");
	    echo "<tr><th>Rohstoff</th><th>Prod.</th><th>Lager</th></tr>";
	    echo "<tr><td>".RES_METAL."</td><td>".nf($arr['building_prod_metal'])."</td><td>".nf($arr['building_store_metal'])."</td></tr>";
	    echo "<tr><td>".RES_CRYSTAL."</td><td>".nf($arr['building_prod_crystal'])."</td><td>".nf($arr['building_store_crystal'])."</td></tr>";
	    echo "<tr><td>".RES_PLASTIC."</td><td>".nf($arr['building_prod_plastic'])."</td><td>".nf($arr['building_store_plastic'])."</td></tr>";
	    echo "<tr><td>".RES_FUEL."</td><td>".nf($arr['building_prod_fuel'])."</td><td>".nf($arr['building_store_fuel'])."</td></tr>";
	    echo "<tr><td>".RES_FOOD."</td><td>".nf($arr['building_prod_food'])."</td><td>".nf($arr['building_store_food'])."</td></tr>";
	    echo "<tr><td>Bewohner</td><td>-</td><td> ".nf($arr['building_people_place'])." Plätze</td></tr>";
	    echo "<tr><td>Energie</td><td>".nf($arr['building_prod_metal'])."</td><td>-</td></tr>";
	    tableEnd();

    }

    // Wohnmodul
    elseif ($arr['building_id']==7)
    {
			$pbarr = mysql_fetch_row(dbquery("SELECT building_people_place FROM buildings WHERE building_id=6;"));
			echo "Beachte das es einen Grundwohnraum für <b>".nf($config->param1Int('user_start_people'))."</b> Menschen pro Planet gibt. Ebenfalls bietet die
			<a href=\"?$link&amp;site=buildings&amp;id=6\">Planetenbasis</a> Platz für <b>".$pbarr[0]."</b> Menschen.<br/>";

	    tableStart("Platz f&uuml;r Bewohner");
	    echo "<tr>
	    	<th>Stufe</th>
	    	<th>Wohnplatz</th>
	    	<th>Wohnplatz mit Grundbonus und Planetenbasis</th>
	    	</tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_people_place'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td>
	         <td class=\"tbldata2\">".nf($prod_item)."</td>
	         <td class=\"tbldata2\">".nf($prod_item+$pbarr[0]+$config->param1Int('user_start_people'))."</td>
	         </tr>";
	      else
	      	echo "<tr><td>$level</td><td>".nf($prod_item)."</td>
	      	<td>".nf($prod_item+$pbarr[0]+$config->param1Int('user_start_people'))."</td></tr>";
	    }
	    tableEnd();
    }

    // Windkraftwerk
    elseif ($arr['building_id']==12)
    {
     	tableStart("Energieproduktion (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Produktion</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    // Solarkaftwerk
    elseif ($arr['building_id']==13)
    {
     	tableStart("Energieproduktion (ohne Boni)");
	    echo "<tr><th>Stufe</t><th>Produktion</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    // Fusionskraftwerk
    elseif ($arr['building_id']==14)
    {
      tableStart("Energieproduktion (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Produktion</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    // Gezeitenkraftwerk
    elseif ($arr['building_id']==15)
    {
      tableStart("Energieproduktion (ohne Boni)");
	    echo "<tr><th>Stufe</t><th>Produktion</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    // Titanspeicher
    elseif ($arr['building_id']==16)
    {
    	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_metal FROM buildings WHERE building_id=6;"));
      tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf($config->getInt("def_store_capacity")).") des Planeten)");
	    echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = $config->getInt("def_store_capacity") + $pbarr[0] + round($arr['building_store_metal'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    // Siliziumspeicher
    elseif ($arr['building_id']==17)
    {
    	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_crystal FROM buildings WHERE building_id=6;"));
      tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf($config->getInt("def_store_capacity")).") des Planeten)");
	    echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = $config->getInt("def_store_capacity") + $pbarr[0] + round($arr['building_store_crystal'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
      tableEnd();
    }

    // Lagerhalle
    elseif ($arr['building_id']==18)
    {
	    $pbarr = mysql_fetch_row(dbquery("SELECT building_store_plastic FROM buildings WHERE building_id=6;"));
	    tableStart("Kapazit&auml;t inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf($config->getInt("def_store_capacity")).")");
	    echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = $config->getInt("def_store_capacity") + $pbarr[0] + round($arr['building_store_plastic'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
      tableEnd();
    }

    // Nahrungssilos
    elseif ($arr['building_id']==19)
    {
	    $pbarr = mysql_fetch_row(dbquery("SELECT building_store_food FROM buildings WHERE building_id=6;"));
      tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf($config->getInt("def_store_capacity")).") des Planeten)");
	    echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = $config->getInt("def_store_capacity") + $pbarr[0] + round($arr['building_store_food'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
    	tableEnd();
    }

    // Tritiumsilo
    elseif ($arr['building_id']==20)
    {
	    $pbarr = mysql_fetch_row(dbquery("SELECT building_store_fuel FROM buildings WHERE building_id=6;"));
	    tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf($config->getInt("def_store_capacity")).") des Planeten)");
	    echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = $config->getInt("def_store_capacity") + $pbarr[0] + round($arr['building_store_fuel'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	      else
	             echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    // Orbitalplatform
    elseif ($arr['building_id']==22)
    {
      tableStart("Zus&auml;tzliche Felder");
	    echo "<tr><th>Stufe</th><th>Felder</th><th>Energieverbrauch</th><th>Speicher ".RES_METAL."</th><th>Speicher ".RES_CRYSTAL."</th><th>Speicher ".RES_PLASTIC."</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_fieldsprovide'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));

	      if($level==$currentLevel)
	         echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td>";
	      else
	        echo "<tr><td>$level</td><td>".nf($prod_item)."</td><td>".nf($power_use)."</td>";

	      $prod_item = round($arr['building_store_metal'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	        echo "<td class=\"tbldata2\">".nf($prod_item)."</td>";
	      else
	        echo "<td>".nf($prod_item)."</td>";

	      $prod_item = round($arr['building_store_crystal'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	        echo "<td class=\"tbldata2\">".nf($prod_item)."</td>";
	      else
	        echo "<td>".nf($prod_item)."</td>";

	      $prod_item = round($arr['building_store_plastic'] * pow($arr['building_store_factor'],$level-1));
	      if($level==$currentLevel)
	        echo "<td class=\"tbldata2\">".nf($prod_item)."</td>";
	      else
	        echo "<td>".nf($prod_item)."</td>";
	    }
	    tableEnd();
    }
	//Raketensilo
	elseif ($arr['building_id']==25)
	{
	    tableStart("Energieverbrauch (ohne Boni)");
	    echo "<tr><th>Stufe</th><th>Energie</th></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	        $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
	        else
	            echo "<tr><td>$level</td><td>".nf($power_use)."</td></tr>";
	    }
			tableEnd();
    }
    // Rohstoffbunker
    elseif ($arr['building_id']==26)
    {
      tableStart("Bunkern von Rohstoffen");
	    echo "<tr><th>Stufe</th><th>Kapazität</th></tr>";
	    for ($level=$b_level;$level<$arr['building_last_level']+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_bunker_res'] * pow($arr['building_store_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	        else
	            echo "<tr><td>$level</td><td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }
    // Flottenbunker
    elseif ($arr['building_id']==27)
    {
      tableStart("Bunkern von Schiffen");
	    echo "<tr><th>Stufe</th><th>Kapazität Stuktur</th><th>Kapazität Anzahl</th></tr>";
	    for ($level=$b_level;$level<$arr['building_last_level']+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_bunker_fleet_space'] * pow($arr['building_store_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td>";
	        else
	            echo "<tr><td>$level</td><td>".nf($prod_item)."</td>";
	      $prod_item = round($arr['building_bunker_fleet_count'] * pow($arr['building_store_factor'],$level-1));
	        if($level==$currentLevel)
	            echo "<td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
	        else
	            echo "<td>".nf($prod_item)."</td></tr>";
	    }
	    tableEnd();
    }

    tableStart ("Kostenentwicklung (Faktor: ".$arr['building_build_costs_factor'].")");
    echo "<tr><th style=\"text-align:center;\">Level</th>
    			<th>".RES_ICON_METAL."".RES_METAL."</th>
    			<th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
    			<th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
    			<th>".RES_ICON_FUEL."".RES_FUEL."</th>
        	<th>".RES_ICON_FOOD."".RES_FOOD."</th>
   <!-- 	<th>".RES_ICON_POWER."Energie</th>     -->
    			<th>Felder</th></tr>";
    for ($x=0;$x<min(30,$arr['building_last_level']);$x++)
    {
    	$bc = calcBuildingCosts($arr,$x);
    	echo '<tr><td>'.($x+1).'</td>
    				<td style="text-align:right;">'.nf($bc['metal']).'</td>
    				<td style="text-align:right;">'.nf($bc['crystal']).'</td>
    				<td style="text-align:right;">'.nf($bc['plastic']).'</td>
    				<td style="text-align:right;">'.nf($bc['fuel']).'</td>
    				<td style="text-align:right;">'.nf($bc['food']).'</td>
   <!-- 	  <td style="text-align:right;">'.nf($bc['power']).'</td>      -->
    				<td style="text-align:right;">'.nf($arr['building_fields']*($x+1)).'</td></tr>';
    }
    tableEnd();

		iBoxStart("Technikbaum");
    showTechTree("b",$arr['building_id']);
		iBoxEnd();

	}
  else
  {
  	error_msg("Geb&auml;udeinfodaten nicht gefunden!");
  }

	echo "<input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
	if (!$popup)
	{
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";
	}
	if (isset($_SESSION['lastpage']) && $_SESSION['lastpage']=="buildings" && !$popup)
	{
	   echo "<input type=\"button\" value=\"Zur&uuml;ck zum Bauhof\" onclick=\"document.location='?page=buildings'\" /> &nbsp; ";
	}

}
/*
//
// Allianzgebäude
//
elseif (isset($_GET['aid']))
{
	$b_level = 1;

	$res = dbquery("SELECT * FROM alliance_buildings WHERE alliance_building_id='".$_GET['aid']."' LIMIT 1;");
	if ($arr = @mysql_fetch_array($res))
	{
		HelpUtil::breadCrumbs(array("Allianzgeb&auml;ude","buildings"),array(text2html($arr['alliance_building_name']),$arr['alliance_building_id']),1);
		echo "<select onchange=\"document.location='?page=help&site=buildings&aid='+this.options[this.selectedIndex].value\">";
		$bres=dbquery("SELECT
			alliance_building_id,
			alliance_building_name
		FROM
			alliance_buildings
		WHERE
			alliance_building_show=1
		ORDER BY
			alliance_building_name;");
		while ($barr=mysql_fetch_array($bres))
		{
			echo "<option value=\"".$barr['alliance_building_id']."\"";
			if ($barr['alliance_building_id']==$_GET['aid'])
				echo " selected=\"selected\"";
			echo ">".$barr['alliance_building_name']."</option>";
		}
		echo "</select><br/><br/>";

		$currentLevel = 0;
		if (isset($cu) && $cu->allianceId()>0)
		{
			$res_level = dbquery("
			SELECT
				alliance_buildlist_current_level
			FROM
				alliance_buildlist
			WHERE
				alliance_buildlist_building_id ='".$_GET['aid']."'
				AND alliance_buildlist_alliance_id='".$cu->allianceId()."'
			LIMIT 1;");
			if(mysql_num_rows($res_level)>0)
			{
				$arr_level = mysql_fetch_row($res_level);
				$currentLevel = $arr_level[0];
			}
		}

		tableStart(text2html($arr['alliance_building_name']));
		echo "<tr>
			<th style=\"width:220px;background:#000;padding:0px;\" rowspan=\"2\">
				<img src=\"".IMAGE_PATH."/".IMAGE_ALLIANCE_BUILDING_DIR."/building".$arr['alliance_building_id'].".".IMAGE_EXT."\" style=\"width:220px;height:220px;background:#000;margin:0px;\" align=\"top\" alt=\"Bild ".$arr['alliance_building_name']."\" />
			</th>
			<td colspan=\"2\">
				<div align=\"justify\">".text2html($arr['alliance_building_longcomment'])."</div>
			</td>
		</tr>
		<tr>
			<th style=\"height:20px;width:120px;\">Maximale Stufe:</th>
			<td style=\"height:20px;\">".$arr['alliance_building_last_level']."</td>
		</tr>";
		tableEnd();

		$useTabs = false;
		if ($useTabs)
		{
			$tc = new TabControl("help",array("Spezielles","Kosten","Technikbaum"));
			$tc->open();
		}

	if ($useTabs)
		{
    	$tc->close();
    	$tc->open();
		}
    tableStart ("Kostenentwicklung (Faktor: ".$arr['alliance_building_costs_factor'].")");
    echo "<tr><th style=\"text-align:center;\">Level</th>
    			<th>".RES_ICON_METAL."".RES_METAL."</th>
    			<th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
    			<th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
    			<th>".RES_ICON_FUEL."".RES_FUEL."</th>
    			<th>".RES_ICON_FOOD."".RES_FOOD."</th></tr>";
    for ($x=0;$x<min(30,$arr['alliance_building_last_level']);$x++)
    {
    	$bc = calcAllianceBuildingCosts($arr,$x);
    	echo '<tr><td>'.($x+1).'</td>
    				<td style="text-align:right;">'.nf($bc['metal']).'</td>
    				<td style="text-align:right;">'.nf($bc['crystal']).'</td>
    				<td style="text-align:right;">'.nf($bc['plastic']).'</td>
    				<td style="text-align:right;">'.nf($bc['fuel']).'</td>
    				<td style="text-align:right;">'.nf($bc['food']).'</td></tr>';
    }
    tableEnd();

		if ($useTabs)
		{
	    $tc->close();
  	  $tc->open();
    }

		/*iBoxStart("Technikbaum");
			showTechTree("b",$arr['building_id']);
		iBoxEnd();

		if ($useTabs)
		{
			$tc->close();
	    $tc->end();
	  }
	}
  else
  {
  	error_msg("Geb&auml;udeinfodaten nicht gefunden!");
  }

	echo "<input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
	if (!$popup)
	{
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";
	}
	if (isset($_SESSION['lastpage']) && $_SESSION['lastpage']=="buildings" && !$popup)
	{
	   echo "<input type=\"button\" value=\"Zur&uuml;ck zum Bauhof\" onclick=\"document.location='?page=buildings'\" /> &nbsp; ";
	}

}*/

//
// Kategorieinfos
//
elseif(isset($_GET['type_id']) && intval($_GET['type_id'])>0)
{
	$btid = intval($_GET['type_id']);

	if ($btid==BUILDING_STORE_CAT)
	{
		echo "<b>Lagerkapazit&auml;t</b><br>";
		echo "Du kannst auf einem Planeten nicht unendlich viele Rohstoffe lagern. Jeder Planet hat eine Lagerkapazit&auml;t von ".$config->getInt("def_store_capacity").". Um die Lagerkapazit&auml;t zu erh&ouml;hen, kannst du eine Planetenbasis und danach verschiedene Speicher, Lagerhallen und Silos bauen, welche die Kapazit&auml;t erh&ouml;hen. Wenn eine Zahl in der Rohstoffanzeige rot gef&auml;rbt ist, bedeutet das, dass dieser Rohstoff die Lagerkapazit&auml;t &uuml;berschreitet. Baue in diesem Fall den Speicher aus. Eine &uuml;berschrittene Lagerkapazit&auml;t bedeutet, dass nichts mehr produziert wird, jedoch werden Rohstoffe, die z.B. mit einer Flotte ankommen, trotzdem auf dem Planeten gespeichert.<br>";
	}
	elseif($btid==BUILDING_POWER_CAT)
	{
		echo "<b>Energie</b><br>";
		echo "Wo es eine Produkion hat, braucht es auch Energie. Diese Energie, welche von verschiedenen Anlagen gebraucht wird, spenden uns verschiedene Kraftwerkstypen. Je h&ouml;her diese Ausgebaut sind, desto mehr Leistung erbringen sie und versorgen so die wachsende Wirtschaft.<br>
		Hat es zu wenig Energie, wird die Produktion prozentual gedrosselt, was verheerende Auswirkungen haben kann!";
	}
	elseif($btid==BUILDING_GENERAL_CAT)
	{
		echo "<b>Allgemeine Geb&auml;ude</b><br/>";
		echo "Diese Geb&auml;ude werden ben&ouml;tigt um deinen Planeten auszubauen und die Produktion und Forschung zu erm&ouml;glichen.";
	}
	elseif($btid==BUILDING_RES_CAT)
	{
		echo "<b>Rohstoffgeb&auml;ude</b><br/>";
		echo "Diese Geb&auml;ude liefern Rohstoffe, welche du f&uuml;r den Aufbau deiner Zivilisation brauchst.";
	}
	else
	{
		echo "<i>Zu dieser Kategorie sind keine Informationen vorhanden!</i>";
	}

	echo "<br/><br/><input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
	echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";
}

//
// übersicht
//
else
{
	HelpUtil::breadCrumbs(array("Geb&auml;ude","buildings"));

	$tres=dbquery("
	SELECT
        type_id,
        type_name
	FROM
		building_types
	ORDER BY
		type_order,
		type_name;");
	if (mysql_num_rows($tres)>0)
	{
		while ($tarr=mysql_fetch_array($tres))
		{
			$res = dbquery("
			SELECT
                building_name,
                building_shortcomment,
                building_longcomment,
                building_id,
                type_name,
                building_fields
			FROM
				buildings,
				building_types
			WHERE
				building_type_id=".$tarr['type_id']."
				AND building_show=1
			GROUP BY
				building_id
			ORDER BY
                building_order,
                building_name;");
			if (mysql_num_rows($res)>0)
			{
				tableStart("<span>".text2html($tarr['type_name'])."</span>");
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>
						<td style=\"width:40px;padding:0px;background:#000;vertical-align:middle;\">
							<a href=\"?$link&amp;site=$site&id=".$arr['building_id']."\">
								<img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_small.".IMAGE_EXT."\" align=\"top\" style=\"width:40px;height:40px;background:#000;margin:0px;\" alt=\"Bild ".text2html($arr['building_name'])."\" border=\"0\"/></a></td>";
					echo "<td style=\"width:130px;\">
						<a href=\"?$link&amp;site=$site&amp;id=".$arr['building_id']."\"><b>".text2html($arr['building_name'])."</a></a>
					</td>";
					echo "<td>".text2html($arr['building_shortcomment'])."</td>";
					echo "<td style=\"width:90px\">";
					if($arr['building_fields']=='0')
						echo "<b>Keine Felder</b></td>";
					elseif($arr['building_fields']=='1')
						echo "<b>".$arr['building_fields']." Feld</b></td>";
					else
						echo "<b>".$arr['building_fields']." Felder</b></td>";
					echo "</tr>";
				}
				tableEnd();
			}
		}
	}
	/*$res = dbquery("
		SELECT
			alliance_building_name,
			alliance_building_shortcomment,
			alliance_building_longcomment,
			alliance_building_id
		FROM
			alliance_buildings
		WHERE
			alliance_building_show=1
		ORDER BY
			alliance_building_name;");
	if (mysql_num_rows($res)>0)
	{
		tableStart("<span>Allianzgebäude</span>");
		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr>
				<td style=\"width:40px;padding:0px;background:#000;vertical-align:middle;\">
					<a href=\"?page=$page&site=$site&aid=".$arr['alliance_building_id']."\">
						<img src=\"".IMAGE_PATH."/".IMAGE_ALLIANCE_BUILDING_DIR."/building".$arr['alliance_building_id']."_small.".IMAGE_EXT."\" align=\"top\" style=\"width:40px;height:40px;background:#000;margin:0px;\" alt=\"Bild ".text2html($arr['alliance_building_name'])."\" border=\"0\"/></a></td>";
			echo "<td style=\"width:130px;\">
				<a href=\"?page=$page&site=$site&aid=".$arr['alliance_building_id']."\"><b>".text2html($arr['alliance_building_name'])."</a></a>
			</td>";
			echo "<td>".text2html($arr['alliance_building_shortcomment'])."</td>";
			echo "</tr>";
		}
		tableEnd();
	}*/
}


?>
