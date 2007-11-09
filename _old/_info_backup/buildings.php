<?PHP

if ($site!=Null)
	define("SHOWLEVELS",30);
else
	define("SHOWLEVELS",5);


define(BUILDING_GENERAL_CAT,1);
define(BUILDING_RES_CAT,2);
define(BUILDING_POWER_CAT,3);
define(BUILDING_STORE_CAT,4);

echo "<h2>Geb&auml;ude</h2>";

if ($_GET['id']!="")
{
	if ($b_level==0) $b_level=1;
	$res = dbquery("SELECT * FROM ".$db_table['buildings']." WHERE building_id='".$_GET['id']."';");
	if ($arr = @mysql_fetch_array($res))
	{
		helpNavi(array("Geb&auml;de","buildings"),array(text2html($arr['building_name']),$arr['building_id']),1);
		echo "<select onchange=\"document.location='?page=help&site=buildings&id='+this.options[this.selectedIndex].value\">";
		$bres=dbquery("SELECT 
			building_id,
			building_name 
		FROM 
			".$db_table['buildings']." 
		INNER JOIN
			".$db_table['building_types']." 
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
			if ($barr['building_id']==$_GET['id']) echo " selected=\"selected\"";
			echo ">".$barr['building_name']."</option>";
		}
		echo "</select><br/><br/>";		

		$res_level = dbquery("SELECT buildlist_current_level FROM ".$db_table['buildlist']." WHERE buildlist_building_id ='".$_GET['id']."' AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND buildlist_planet_id='".$c->id."';");
		if(mysql_num_rows($res_level)>0)
		{
			$arr_level = mysql_fetch_array($res_level);
		}

		infobox_start(text2html($arr['building_name']),1);
		echo "<tr><td class=\"tbltitle\" width=\"220\"><img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id'].".".IMAGE_EXT."\" style=\"width:220px;height:220px;\" alt=\"Bild ".$arr['building_name']."\" /></td>";
		echo "<td class=\"tbldata\"><div align=\"justify\">".text2html($arr['building_longcomment'])."</div></td></tr>";
		infobox_end(1);


		// Metallmine
        if ($arr['building_id']==1)
        {
        infobox_start("Produktion von ".$rsc['metal']." (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
            $prod_item = round($arr['building_prod_metal'] * pow($arr['building_production_factor'],$level-1));
            $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
            if($level==$arr_level['buildlist_current_level'])
                echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
            else
                echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";

        }
            infobox_end(1);
        }

        // Siliziummine
        if ($arr['building_id']==2)
        {
            infobox_start("Produktion von ".$rsc['crystal']." (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_crystal'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
            if($level==$arr_level['buildlist_current_level'])
                echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
            else
                echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
        }
            infobox_end(1);
        }
        // Chemiefabrik
        if ($arr['building_id']==3)
        {
        infobox_start("Produktion von ".$rsc['plastic']." (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_plastic'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
            if($level==$arr_level['buildlist_current_level'])
                echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
            else
                echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
        }
        infobox_end(1);
        }
        // Tritiumsynthetizer
        if ($arr['building_id']==4)
        {
        infobox_start("Produktion von ".$rsc['fuel']." (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_fuel'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
            if($level==$arr_level['buildlist_current_level'])
                echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
            else
                echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td></tr>";
        }
        infobox_end(1);
        }
        // Gew&auml&auml;chshaus
        if ($arr['building_id']==5)
        {
            infobox_start("Produktion von ".$rsc['food']." (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td><td class=\"tbltitle\">Energie</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_food'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
            if($level==$arr_level['buildlist_current_level'])
                echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td></tr>";
            else
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

        // Wohnmodul
        if ($arr['building_id']==7)
        {
        infobox_start("Platz f&uuml;r Bewohner",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Wohnplatz</td></tr>";

        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_people_place'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
        infobox_end(1);
        }

        // Windkraftwerk
        if ($arr['building_id']==12)
        {
            infobox_start("Energieproduktion (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
        infobox_end(1);
        }

        // Solarkaftwerk
        if ($arr['building_id']==13)
        {
            infobox_start("Energieproduktion (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
        infobox_end(1);
        }

        // Fusionskraftwerk
        if ($arr['building_id']==14)
        {
          infobox_start("Energieproduktion (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
            infobox_end(1);
        }

        // Gezeitenkraftwerk
        if ($arr['building_id']==15)
        {
            infobox_start("Energieproduktion (ohne Boni)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Produktion</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_prod_power'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
        infobox_end(1);
        }
        // Titanspeicher
        if ($arr['building_id']==16)
        {
        $pbarr = mysql_fetch_row(dbquery("SELECT building_store_metal FROM ".$db_table['buildings']." WHERE building_id=6;"));
            infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_metal'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
        infobox_end(1);
        }
        // Siliziumspeicher
        if ($arr['building_id']==17)
        {
        $pbarr = mysql_fetch_row(dbquery("SELECT building_store_crystal FROM ".$db_table['buildings']." WHERE building_id=6;"));
            infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_crystal'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
            infobox_end(1);
        }
        // Lagerhalle
        if ($arr['building_id']==18)
        {
        $pbarr = mysql_fetch_row(dbquery("SELECT building_store_plastic FROM ".$db_table['buildings']." WHERE building_id=6;"));
            infobox_start("Kapazit&auml;t inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).")",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_plastic'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
          infobox_end(1);
        }
        // Nahrungssilos
        if ($arr['building_id']==19)
        {
        $pbarr = mysql_fetch_row(dbquery("SELECT building_store_food FROM ".$db_table['buildings']." WHERE building_id=6;"));
            infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_food'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
            infobox_end(1);
        }
        // Tritiumsilo
        if ($arr['building_id']==20)
        {
        $pbarr = mysql_fetch_row(dbquery("SELECT building_store_fuel FROM ".$db_table['buildings']." WHERE building_id=6;"));
        infobox_start("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (".nf($pbarr[0]).") und Standardkapazit&auml;t (".nf(STD_FIELDS).") des Planeten)",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Kapazit&auml;t</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = STD_FIELDS + $pbarr[0] + round($arr['building_store_fuel'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td></tr>";
          else
                 echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td></tr>";
        }
        infobox_end(1);
        }

        // Orbitalplatform
        if ($arr['building_id']==22)
        {
            infobox_start("Zus&auml;tzliche Felder",1);
        echo "<tr><td class=\"tbltitle\">Stufe</td><td class=\"tbltitle\">Felder</td><td class=\"tbltitle\">Energieverbrauch</td><td class=\"tbltitle\">Speicher ".$rsc['metal']."</td><td class=\"tbltitle\">Speicher ".$rsc['crystal']."</td><td class=\"tbltitle\">Speicher ".$rsc['plastic']."</td></tr>";
        for ($level=$b_level;$level<SHOWLEVELS+$b_level;$level++)
        {
          $prod_item = round($arr['building_fieldsprovide'] * pow($arr['building_production_factor'],$level-1));
          $power_use = round($arr['building_power_use'] * pow($arr['building_production_factor'],$level-1));

          if($level==$arr_level['buildlist_current_level'])
             echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">".nf($prod_item)."</td><td class=\"tbldata2\">".nf($power_use)."</td>";
          else
            echo "<tr><td class=\"tbldata\">$level</td><td class=\"tbldata\">".nf($prod_item)."</td><td class=\"tbldata\">".nf($power_use)."</td>";

          $prod_item = round($arr['building_store_metal'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
            echo "<td class=\"tbldata2\">".nf($prod_item)."</td>";
          else
            echo "<td class=\"tbldata\">".nf($prod_item)."</td>";

          $prod_item = round($arr['building_store_crystal'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
            echo "<td class=\"tbldata2\">".nf($prod_item)."</td>";
          else
            echo "<td class=\"tbldata\">".nf($prod_item)."</td>";

          $prod_item = round($arr['building_store_plastic'] * pow($arr['building_store_factor'],$level-1));
          if($level==$arr_level['buildlist_current_level'])
            echo "<td class=\"tbldata2\">".nf($prod_item)."</td>";
          else
            echo "<td class=\"tbldata\">".nf($prod_item)."</td>";
        }
        infobox_end(1);
        }
        }
        else
        echo "Geb&auml;udeinfodaten nicht gefunden!<br/><br/>";

	echo "<input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
	echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";

	if ($_SESSION['lastpage']=="buildings")
	   echo "<input type=\"button\" value=\"Zur&uuml;ck zum Bauhof\" onclick=\"document.location='?page=buildings'\" /> &nbsp; ";

}

//
// Kategorieinfos
//
elseif($_GET['type_id']>0)
{
	if ($_GET['type_id']==BUILDING_STORE_CAT)
	{
		helpNavi(array("Geb&auml;de","buildings"),array("Kategorie: Speicher",$_GET['type_id']));
		infobox_start("Lagerkapazit&auml;t");
		echo "<div align=\"justify\">";
		echo "Du kannst auf einem Planeten nicht unentlich viele Rohstoffe lagern. Jeder Planet hat eine Lagerkapazit&auml;t von ".intval($conf['def_store_capacity']['v']).". Um die Lagerkapazit&auml;t zu erh&ouml;hen, kannst du eine Planetenbasis und danach verschiedene Speicher, Lagerhallen und Silos bauen, welche die Kapazit&auml;t erh&ouml;hen. Wenn eine Zahl in der Rohstoffanzeige rot gef&auml;rbt ist, bedeutet das, dass dieser Rohstoff die Lagerkapazit&auml;t &uuml;berschreitet. Baue in diesem Fall den Speicher aus. Eine &uuml;berschrittene Lagerkapazit&auml;t bedeutet, dass nichts mehr produziert wird, jedoch werden Rohstoffe, die z.B. mit einer Flotte ankommen, trotzdem auf dem Planeten gespeichert.<br>";
		echo "</div>";
		infobox_end();
		echo "Klicke <a href=\"?page=ressources\">hier</a> um zu der Speicher&uuml;bersicht des aktuellen Planeten zu gelangen.";
	}
	elseif($_GET['type_id']==BUILDING_POWER_CAT)
	{
		helpNavi(array("Geb&auml;de","buildings"),array("Kategorie: Kraftwerke",$_GET['type_id']));
		infobox_start("Energie");
		echo "<div align=\"justify\">";
		echo "Wo es eine Produkion hat, braucht es auch Energie. Diese Energie, welche von verschiedenen Anlagen gebraucht wird, spenden uns verschiedene Kraftwerkstypen. Je h&ouml;her diese Ausgebaut sind, desto mehr Leistung erbringen sie und versorgen so die wachsende Wirtschaft.<br>
		Hat es zu wenig Energie, wird die Produktion prozentual gedrosselt, was verheerende Auswirkungen haben kann!";
		echo "</div>";
		infobox_end();
		echo "Klicke <a href=\"?page=ressources\">hier</a> um zu der Energie&uuml;bersicht des aktuellen Planeten zu gelangen.";
	}
	elseif($_GET['type_id']==BUILDING_GENERAL_CAT)
	{
		helpNavi(array("Geb&auml;de","buildings"),array("Kategorie: Allgemeine Geb&auml;de",$_GET['type_id']));
		infobox_start("Allgemeine Geb&auml;de");
		echo "<div align=\"justify\">";
		echo "Diese Geb&auml;de werden ben&ouml;tigt um deinen Planeten auszubauen und die Produktion und Forschung zu erm&ouml;glichen.";
		echo "</div>";
		infobox_end();
	}
	elseif($_GET['type_id']==BUILDING_RES_CAT)
	{
		helpNavi(array("Geb&auml;de","buildings"),array("Kategorie: Rohstoffgeb&auml;ude",$_GET['type_id']));
		infobox_start("Rohstoffgeb&auml;ude");
		echo "<div align=\"justify\">";
		echo "Diese Geb&auml;de liefern Rohstoffe, welche du f&uuml;r den Aufbau deiner Zivilisation brauchst.";
		echo "</div>";
		infobox_end();
		echo "Klicke <a href=\"?page=ressources\">hier</a> um zu der Produktions&uuml;bersicht des aktuellen Planeten zu gelangen.";
	}
	else
	{
		echo "<i>Zu dieser Kategorie sind keine Informationen vorhanden!</i>";
	}

	echo "<br/><br/><input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
	echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";
}

//
// übersicht
//
else
{
	helpNavi(array("Geb&auml;de","buildings"));
	
	$tres=dbquery("
	SELECT 
        type_id,
        type_name 
	FROM 
		".$db_table['building_types']." 
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
				".$db_table['buildings'].",
				".$db_table['building_types']." 
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
				infobox_start(text2html($tarr['type_name'])." [<a href=\"?page=$page&amp;site=$site&amp;type_id=".$tarr['type_id']."\">info</a>]",1);
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\" style=\"width:40px\" ".tm("Info","Klicke auf das Bild f&uuml;r Details")."><a href=\"?page=$page&site=$site&id=".$arr['building_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_small.".IMAGE_EXT."\" style=\"width:40px;height:40px;\" alt=\"Bild ".text2html($arr['building_name'])."\" border=\"0\"/></a></td>";
					echo "<th class=\"tbltitle\" style=\"width:160px\" ".tm("Info","Klicke auf das Bild f&uuml;r Details").">".text2html($arr['building_name'])."</th>";
					echo "<td class=\"tbldata\" ".tm(text2html($arr['building_name']),text2html($arr['building_longcomment'])).">".text2html($arr['building_shortcomment'])."</td>";
					echo "<td class=\"tbldata\" style=\"width:90px\" ".tm("Info","Ben&ouml;tigte Felder pro Stufe").">";
					if($arr['building_fields']=='0')
						echo "<b>Keine Felder</b></td>";
					elseif($arr['building_fields']=='1')
						echo "<b>".$arr['building_fields']." Feld</b></td>";
					else
						echo "<b>".$arr['building_fields']." Felder</b></td>";
					echo "</tr>";
				}
				infobox_end(1);
			}
		}
	}
}


?>
