<?PHP

echo "<h4>: Geb&auml;ude :</h4>";

if ($site!=Null)
	define("SHOWLEVELS",35);
else
	define("SHOWLEVELS",5);


if ($_GET['id']!="")
{
	if ($b_level==0) $b_level=1;
	$res = dbquery("SELECT * FROM ".$db_table['buildings']." WHERE building_id='".$_GET['id']."';");
	if ($arr = @mysql_fetch_array($res))
	{
		infobox_start($arr['building_name'],1);
		echo "<tr><td class=\"tbltitle\" width=\"150\"><img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id'].".".IMAGE_EXT."\" style=\"width:150px;height:150px;\" alt=\"Bild ".$arr['building_name']."\" /></td>";
		echo "<td class=\"tbldata\">".$arr['building_longcomment']."</td></tr>";
		infobox_end(1);


		// Metallmine
	  if ($arr['building_id']==1)
	  {
	    infobox_start("Produktion von ".$rsc['metal']." (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_metal'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
	    }
			infobox_end(1);
	  }
		// Siliziummine
	  if ($arr['building_id']==2)
	  {
			infobox_start("Produktion von ".$rsc['crystal']." (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_crystal'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
	    }
			infobox_end(1);
	  }
		// Chemiefabrik
	  if ($arr['building_id']==3)
	  {
	  	infobox_start("Produktion von ".$rsc['plastic']." (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_plastic'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
	    }
	    echo "</table>";
	    echo "<p>Werte sind OHNE spezielle Boni berechnet!</p>";
	  }
		// Tritiumsynthetizer
	  if ($arr['building_id']==4)
	  {
	    infobox_start("Produktion von ".$rsc['fuel']." (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_fuel'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
	    }
	    infobox_end(1);
	  }
		// Gew&auml;chshaus
	  if ($arr['building_id']==5)
	  {
			infobox_start("Produktion von ".$rsc['food']." (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_food'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
	    }
	    infobox_end(1);
	  }
		// Planetenbasis
	  if ($arr['building_id']==6)
	  {
	    infobox_start("Produktion (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">Rohstoff</td><td class=\"tbltitle\">Prod.</td><td class=\"tbltitle\">Lager</td></tr>";
      echo "<tr><td class=\"tbldata\">".$rsc['metal']."</td><td class=\"tbldata\">".nf($arr['building_prod_metal'])."</td><td class=\"tbldata\">".nf($arr['building_store_metal'])."</td></tr>";
      echo "<tr><td class=\"tbldata\">".$rsc['crystal']."</td><td class=\"tbldata\">".nf($arr['building_prod_crystal'])."</td><td class=\"tbldata\">".nf($arr['building_store_crystal'])."</td></tr>";
      echo "<tr><td class=\"tbldata\">".$rsc['plastic']."</td><td class=\"tbldata\">".nf($arr['building_prod_plastic'])."</td><td class=\"tbldata\">".nf($arr['building_store_plastic'])."</td></tr>";
      echo "<tr><td class=\"tbldata\">".$rsc['fuel']."</td><td class=\"tbldata\">".nf($arr['building_prod_fuel'])."</td><td class=\"tbldata\">".nf($arr['building_store_fuel'])."</td></tr>";
      echo "<tr><td class=\"tbldata\">".$rsc['food']."</td><td class=\"tbldata\">".nf($arr['building_prod_food'])."</td><td class=\"tbldata\">".nf($arr['building_store_food'])."</td></tr>";
      echo "<tr><td class=\"tbldata\">Energie</td><td class=\"tbldata\">".nf($arr['building_prod_metal'])."</td><td class=\"tbldata\">-</td></tr>";
	    infobox_end(1);
	  }

		// Windkraftwerk
	  if ($arr['building_id']==12)
	  {
			infobox_start("Energieproduktion (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
	    infobox_end(1);
	  }

		// Solarkaftwerk
	  if ($arr['building_id']==13)
	  {
			infobox_start("Energieproduktion (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
	    infobox_end(1);
	  }

		// Fusionskraftwerk
	  if ($arr['building_id']==14)
	  {
		  infobox_start("Energieproduktion (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
			infobox_end(1);
	  }

		// Gezeitenkraftwerk
	  if ($arr['building_id']==15)
	  {
			infobox_start("Energieproduktion (ohne Boni)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Produktion</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
	    infobox_end(1);
	  }
		// Titanspeicher
	  if ($arr['building_id']==16)
	  {
	  	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_metal FROM ".$db_table['buildings']." WHERE building_id=6;"));
			infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_metal'] * pow($arr['building_store_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
	    infobox_end(1);
	  }
		// Siliziumspeicher
	  if ($arr['building_id']==17)
	  {
	  	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_crystal FROM ".$db_table['buildings']." WHERE building_id=6;"));
			infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_crystal'] * pow($arr['building_store_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
			infobox_end(1);
	  }
		// Lagerhalle
	  if ($arr['building_id']==18)
	  {
	  	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_plastic FROM ".$db_table['buildings']." WHERE building_id=6;"));
			infobox_start("Kapazit&auml;t inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).")",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_plastic'] * pow($arr['building_store_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
		  infobox_end(1);
	  }
		// Nahrungssilos
	  if ($arr['building_id']==19)
	  {
	  	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_food FROM ".$db_table['buildings']." WHERE building_id=6;"));
			infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_food'] * pow($arr['building_store_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
			infobox_end(1);
	  }
		// Tritiumsilo
	  if ($arr['building_id']==20)
	  {
	  	$pbarr = mysql_fetch_row(dbquery("SELECT building_store_fuel FROM ".$db_table['buildings']." WHERE building_id=6;"));
	    infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_fuel'] * pow($arr['building_store_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	    }
	    infobox_end(1);
	  }

		// Orbitalplatform
	  if ($arr['building_id']==22)
	  {
			infobox_start("Zus&auml;tzliche Felder",1);
	    echo "<tr><td class=\"tbltitle\">#</td><td class=\"tbltitle\">Felder</td><td class=\"tbltitle\">Energieverbrauch</td><td class=\"tbltitle\">Speicher ".$rsc['metal']."</td><td class=\"tbltitle\">Speicher ".$rsc['crystal']."</td><td class=\"tbltitle\">Speicher ".$rsc['plastic']."</td></tr>";
	    for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
	    {
	      $prod_item = round($arr['building_fieldsprovide'] * pow($arr['building_production_factor'],$level-1));
	      $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
	      echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td>";

	      $prod_item = round($arr['building_store_metal'] * pow($arr['building_store_factor'],$level-1));
	      echo "<td class=\"tbldata\">".nf($prod_item)."</td>";

	      $prod_item = round($arr['building_store_crystal'] * pow($arr['building_store_factor'],$level-1));
	      echo "<td class=\"tbldata\">".nf($prod_item)."</td>";

	      $prod_item = round($arr['building_store_plastic'] * pow($arr['building_store_factor'],$level-1));
	      echo "<td class=\"tbldata\">".nf($prod_item)."</td></tr>";
	  	}
	  	infobox_end(1);
	  }
	}
	else
	  echo "Geb&auml;udeinfodaten nicht gefunden!";
	echo "<input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
	echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";

}
else
{
	$res = dbquery("SELECT building_name,building_shortcomment,building_id,type_name FROM ".$db_table['buildings'].",".$db_table['building_types']." WHERE building_type_id=type_id AND building_show=1 GROUP BY building_id ORDER BY type_name,building_name;");
	if (mysql_num_rows($res)>0)
	{
		infobox_start("&Uuml;bersicht",1);
		
		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\" width=\"50\"><img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_small.".IMAGE_EXT."\" style=\"width:50px;height:50px;\" alt=\"Bild ".$arr['building_name']."\" /></td>";
			echo "<th class=\"tbltitle\">".$arr['building_name']."</th>";
			echo "<th class=\"tbldata\">".$arr['type_name']."</th>";
			echo "<td class=\"tbldata\">".$arr['building_shortcomment']."</td>";
			echo "<td class=\"tbldata\" width=\"50\"><a href=\"?page=$page&site=$site&id=".$arr['building_id']."\">Details</a></td></tr>";
		}
		infobox_end(1);
	}
	else
		echo "<i>Keine Daten vorhanden!</i>";
	echo "</div>";

}

?>
