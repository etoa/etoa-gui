<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: ressources.php												//
	// Topic: Rohstoffproduktionsübersicht					//
	// Version: 0.1																	//
	// Letzte Änderung: 10.05.2006 Lamborghini								//
	//////////////////////////////////////////////////

	define(RES_BUILDING_CAT,2);

	if ($planets->current)
	{
		$c = $planets->getCurrentData();

		//
		// Wirtschaft neu berechnen ("Zauberknopf")
		//
		if ($_GET['action']=="update")
		{
			$c->updateEconomy();
		}

		//
		// Poduktionsrate umstellen
		//
		if ($_POST['submitpercent']!="")
		{
			if (count($_POST['buildlist_prod_percent'])>0)
			{
				foreach ($_POST['buildlist_prod_percent'] as $id=>$val)
				{
					if ($val>1) $val=1; if ($val<0) $val=0;
					dbquery("UPDATE ".$db_table['buildlist']." SET buildlist_prod_percent=$val WHERE buildlist_user_id=".$_SESSION[ROUNDID]['user']['id']." AND buildlist_planet_id=".$c->id." AND buildlist_building_id='$id';");
				}
				$c->updateEconomy();
			}
		}

		echo "<h1>Wirtschaft des Planeten ".$c->name."</h1>";
		$c->resBox();

		echo "<input type=\"button\" onclick=\"document.location='?page=planetstats'\" value=\"Ressourcen aller Planeten anzeigen\" />
		<input type=\"button\" onclick=\"document.location='?page=$page&action=update'\" value=\"Neu berechnen\" /><br/><br/>";


		//
		// Produktion pro Stunde und Energieverbrauch
		//

		echo "<form action=\"?page=$page\" method=\"post\">";
		$bres = dbquery("
		SELECT 
			b.building_id,
			b.building_name,
			b.building_type_id,
			b.building_prod_metal,
			b.building_prod_crystal,
			b.building_prod_plastic,
			b.building_prod_fuel,
			b.building_prod_food,
			b.building_power_use,
			b.building_production_factor,
			l.buildlist_current_level,
			l.buildlist_prod_percent
		FROM 
            ".$db_table['buildings']." AS b,
            ".$db_table['buildlist']." AS l
		WHERE 
            b.building_id=l.buildlist_building_id
            AND l.buildlist_user_id=".$_SESSION[ROUNDID]['user']['id']."
            AND l.buildlist_planet_id=".$c->id."
            AND l.buildlist_current_level>0
            AND (b.building_prod_metal>0
                OR b.building_prod_crystal>0
                OR b.building_prod_plastic>0
                OR b.building_prod_fuel>0
                OR b.building_prod_food>0
                OR b.building_power_use>0)
		ORDER BY 
			b.building_type_id,
			b.building_order;");
		if (mysql_num_rows($bres)>0)
		{
			infobox_start("Produktion pro Stunde und Energieverbrauch",1);
			echo "<tr><td class=\"tbltitle\">Geb&auml;ude</td>";
			echo "<td class=\"tbltitle\">".RES_METAL."</td>";
			echo "<td class=\"tbltitle\">".RES_CRYSTAL."</td>";
			echo "<td class=\"tbltitle\">".RES_PLASTIC."</td>";
			echo "<td class=\"tbltitle\">".RES_FUEL."</td>";
			echo "<td class=\"tbltitle\">".RES_FOOD."</td>";
			echo "<td class=\"tbltitle\" colspan=\"2\">Energieverbrauch</td>";
			echo "</tr>";

			$cnt = array();
			$pwrcnt = 0;
			while ($barr = mysql_fetch_array($bres))
			{
				// Ist das gebäudelevel > 0
				if($barr['buildlist_current_level']>0)
				{
					// Errechnen der Produktion pro Gebäude
                    echo "<tr><td class=\"tbltitle\" width=\"170\">".$barr['building_name']." (".$barr['buildlist_current_level'].")</td>";
                    $bp['metal']    = ceil($barr['building_prod_metal'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
                    $bp['crystal']= ceil($barr['building_prod_crystal'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
                    $bp['plastic']= ceil($barr['building_prod_plastic'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
                    $bp['fuel']     = ceil($barr['building_prod_fuel'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
                    $bp['food']     = ceil($barr['building_prod_food'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));


                    // Addieren der Planeten- und Rassenboni
                    if ($bp['metal']!="") $bp['metal']  = $bp['metal'] + ($bp['metal'] * ($c->type->metal-1)) + ($bp['metal'] * ($user['race']['metal']-1) + ($bp['metal'] * ($c->sol->type->metal-1)));
                    if ($bp['crystal']!="") $bp['crystal']= $bp['crystal'] + ($bp['crystal'] * ($c->type->crystal-1)) +  ($bp['crystal'] * ($user['race']['crystal']-1) + ($bp['crystal'] * ($c->sol->type->crystal-1)));
                    if ($bp['plastic']!="") $bp['plastic']= $bp['plastic'] + ($bp['plastic'] * ($c->type->plastic-1)) + ($bp['plastic'] * ($user['race']['plastic']-1) + ($bp['plastic'] * ($c->sol->type->plastic-1)));
                    if ($bp['fuel']!="") $bp['fuel'] = $bp['fuel'] + ($bp['fuel'] * ($c->type->fuel-1)) + ($bp['fuel'] * ($user['race']['fuel']-1) + ($bp['fuel'] * ($c->sol->type->fuel-1)));
                    if ($bp['food']!="") $bp['food'] = $bp['food'] + ($bp['food'] * ($c->type->food-1)) + ($bp['food'] * ($user['race']['food']-1) + ($bp['food'] * ($c->sol->type->food-1)));

                    // Zum Total hinzufügen
                    $cnt['metal'] += $bp['metal']*$barr['buildlist_prod_percent'];
                    $cnt['crystal'] += $bp['crystal']*$barr['buildlist_prod_percent'];
                    $cnt['plastic'] += $bp['plastic']*$barr['buildlist_prod_percent'];
                    $cnt['fuel'] += $bp['fuel']*$barr['buildlist_prod_percent'];
                    $cnt['food'] += $bp['food']*$barr['buildlist_prod_percent'];

                    $building_power_use = $barr['building_power_use'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);

                    // Werte anzeigen
                    echo "<td width=\"70\" class=\"tbldata\">".nf($bp['metal']*$barr['buildlist_prod_percent'],1)."</td>";
                    echo "<td width=\"70\" class=\"tbldata\">".nf($bp['crystal']*$barr['buildlist_prod_percent'],1)."</td>";
                    echo "<td width=\"70\" class=\"tbldata\">".nf($bp['plastic']*$barr['buildlist_prod_percent'],1)."</td>";
                    echo "<td width=\"70\" class=\"tbldata\">".nf($bp['fuel']*$barr['buildlist_prod_percent'],1)."</td>";
                    echo "<td width=\"70\" class=\"tbldata\">".nf($bp['food']*$barr['buildlist_prod_percent'],1)."</td>";
                    echo "<td width=\"70\" class=\"tbldata\"";
                    if ($building_power_use>0) echo " style=\"color:#f00\"";
                    echo ">".nf($building_power_use*$barr['buildlist_prod_percent'])."</td>";
                    echo "<td width=\"50\" class=\"tbldata\">";
                    if ($barr['building_type_id']==RES_BUILDING_CAT)
                    {
                        echo "<select name=\"buildlist_prod_percent[".$barr['building_id']."]\">\n";
                        $prod_percent = $barr['buildlist_prod_percent'];
                        for ($x=0;$x<1;$x+=0.1)
                        {
                            if ($x>0.9) $vx=0;
                            else $vx = 1-$x;
                            $perc = $vx*100;
                            echo "<option value=\"$vx\"";
                            if (doubleval($vx)>=doubleval($prod_percent)) echo " selected=\"selected\"";
                            echo ">$perc %</option>\n";
                        }
                        echo "</select>";
                    }
                    else
                        echo "&nbsp;";
                    echo "</td>";
                    echo "</tr>";
                    $pwrcnt+=$building_power_use*$barr['buildlist_prod_percent'];
                }
			}
			$pwrcnt=floor($pwrcnt);
			//echo "<tr><td class=\"tbltitle\" colspan=\"8\" height=\"4\"></td></tr>";

			// Anzeigen der Gesamtproduktion
			echo "<tr><td class=\"tbltitle\">TOTAL</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['metal'])."</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['crystal'])."</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['plastic'])."</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['fuel'])."</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['food'])."</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#f00\">".nf($pwrcnt)."</td>";
			echo "<td width=\"70\" class=\"tbldata\" style=\"color:#f00\"><input type=\"submit\" name=\"submitpercent\" class=\"button\" style=\"font-size:8pt;\" value=\"Speichern\" /></td>";
			echo "</tr>";

			// Bei zuwenig Strom Warnmessage
			if ($pwrcnt > $c->prod->power)
			{
				echo "<tr><td class=\"tbldata\" colspan=\"8\" style=\"color:#f00; text-align:center;\">Zuwenig Energie! ".nf(floor($pwrcnt))." ben&ouml;tigt, ".nf(floor($c->prod->power))." verf&uuml;gbar. Gesamtproduktion wird auf ".(round($c->prod->power / $pwrcnt,3)*100)."% gesenkt!</td>";

				$cnt['metal'] = floor($cnt['metal'] * $c->prod->power / $pwrcnt);
				$cnt['crystal'] = floor($cnt['crystal'] * $c->prod->power / $pwrcnt);
				$cnt['fuel'] = floor($cnt['fuel'] * $c->prod->power / $pwrcnt);
				$cnt['plastic'] = floor($cnt['plastic'] * $c->prod->power / $pwrcnt);
				$cnt['food'] = floor($cnt['food'] * $c->prod->power / $pwrcnt);

				echo "<tr><td class=\"tbltitle\">TOTAL</td>";
				echo "<td class=\"tbldata\">".nf($cnt['metal'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['crystal'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['plastic'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['fuel'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['food'])."</td>";
				echo "<td class=\"tbldata\" colspan=\"2\">".nf(floor($c->prod->power))."</td>";
				echo "</tr>";
			}
			infobox_end(1);
		}
		else
		{
			echo "Es wurden noch keine Produktionsgeb&auml;ude gebaut!<br/><br/>";
		}
		echo "</form>";




		//
		// Energie
		//
		$pres = dbquery("
		SELECT 
			b.building_id,
			b.building_name,
			b.building_type_id,
			b.building_prod_power,
			b.building_power_use,
			b.building_production_factor,
			l.buildlist_current_level,
			l.buildlist_prod_percent
		FROM 
            ".$db_table['buildings']." AS b,
            ".$db_table['buildlist']." AS l
		WHERE 
            b.building_id=l.buildlist_building_id
            AND l.buildlist_user_id=".$_SESSION[ROUNDID]['user']['id']."
            AND l.buildlist_planet_id=".$c->id."
            AND l.buildlist_current_level>0
            AND b.building_prod_power>0
		ORDER BY 
			b.building_order,
			b.building_name;");
		if (mysql_num_rows($pres)>0)
		{
			echo "<table class=\"tblc\">";
			echo "<tr><td class=\"tbltitle\" colspan=\"2\">Energie</td></tr>";
			while ($parr = mysql_fetch_array($pres))
			{
				$bp['power'] 	= round($parr['building_prod_power'] * pow($parr['building_production_factor'],$parr['buildlist_current_level']-1));
				// Addieren der Planeten- und Rassenboni
				if ($bp['power']!="") $bp['power'] = $bp['power'] + ($bp['power'] * ($c->type->power-1)) + ($bp['power'] * ($user['race']['power']-1) + ($bp['power'] * ($c->sol->type->power-1)));

				echo "<tr><td class=\"tbltitle\">".$parr['building_name']." (".$parr['buildlist_current_level'].")</td>";
				echo "<td class=\"tbldata\">".nf(floor($bp['power']))."</td></tr>";

				// Zum Total hinzufügen
				$cnt['power'] += $bp['power'];
			}
			//$pwrfree = floor($cnt['power']) - floor($pwrcnt);
			$power_rest = ceil($c->prod->power)-floor($c->use->power);
			//echo "<tr><td class=\"tbltitle\" colspan=\"2\" height=\"1\"></td></tr>";
			echo "<tr><td class=\"tbltitle\">TOTAL</td><td class=\"tbldata\">".nf(floor($cnt['power']))."</td></tr>";
			echo "<tr><td class=\"tbltitle\">Benutzt / Verf&uuml;gbar</td><td class=\"tbldata\"";
			if (floor($power_rest)<0)
				echo " style=\"color:#f00\"";
			else
				echo " style=\"color:#0f0\"";
			echo ">".nf(floor($c->use->power))." / ".nf(floor($power_rest))."</td></tr>";
			echo "</table><br/><br/>";
		}

		//
		// Lager
		//

		$bres = dbquery("
		SELECT
            b.building_name,
            b.building_store_metal,
            b.building_store_crystal,
            b.building_store_plastic,
            b.building_store_fuel,
            b.building_store_food,
            b.building_store_factor,
            l.buildlist_current_level
		FROM
            ".$db_table['buildings']." AS b,
            ".$db_table['buildlist']." AS l
		WHERE
            b.building_id = l.buildlist_building_id
            AND l.buildlist_planet_id=".$c->id."
            AND l.buildlist_current_level>0
            AND 
                (b.building_store_metal>0 
                OR b.building_store_crystal>0 
                OR b.building_store_plastic>0 
                OR b.building_store_fuel>0 
                OR b.building_store_food>0);");
		if (mysql_num_rows($bres)>0)
		{
			infobox_start("Lagerkapazit&auml;t",1);
			echo "<tr><td class=\"tbltitle\">Geb&auml;ude</td>";
			echo "<td class=\"tbltitle\">".RES_METAL."</td>";
			echo "<td class=\"tbltitle\">".RES_CRYSTAL."</td>";
			echo "<td class=\"tbltitle\">".RES_PLASTIC."</td>";
			echo "<td class=\"tbltitle\">".RES_FUEL."</td>";
			echo "<td class=\"tbltitle\">".RES_FOOD."</td>";
			echo "</tr>";

			echo "<tr><td class=\"tbltitle\">Grundkapazit&auml;t</td>";
			for ($x=0;$x<5;$x++)
			{
  			echo "<td class=\"tbldata\">".nf($conf['def_store_capacity']['v'])."</td>";
  			$storetotal[$x]=$conf['def_store_capacity']['v'];
  		}
  		echo "</tr>";
			while ($barr=mysql_fetch_array($bres))
			{
					echo "<tr><td class=\"tbltitle\" width=\"150\">".$barr['building_name']." (".$barr['buildlist_current_level'].")</td>";
					$level = $barr['buildlist_current_level']-1;
					$store[0]=round($barr['building_store_metal'] * pow($barr['building_store_factor'],$level));
					$store[1]=round($barr['building_store_crystal'] * pow($barr['building_store_factor'],$level));
					$store[2]=round($barr['building_store_plastic'] * pow($barr['building_store_factor'],$level));
					$store[3]=round($barr['building_store_fuel'] * pow($barr['building_store_factor'],$level));
					$store[4]=round($barr['building_store_food'] * pow($barr['building_store_factor'],$level));
					foreach ($store as $id=>$s)
					{
						$storetotal[$id]+=$s;
						echo "<td class=\"tbldata\">".nf($s)."</td>";
					}
					echo "</tr>";
			}
			//echo "<tr><td class=\"tbltitle\" colspan=\"8\" height=\"1\"></td></tr>";
			echo "<tr><td class=\"tbltitle\">TOTAL</td>";
			foreach ($storetotal as $id=>$s)
			{
				echo "<td class=\"tbldata\">".nf($s,1)."</td>";
			}
			echo "</tr>";
			infobox_end(1);
		}


		//
		// Boni
		//

		infobox_start("Boni",1);


		echo "<tr><td class=\"tbltitle\">&nbsp;</td><td class=\"tbltitle\">".$c->type->name."</td>";
		echo "<td class=\"tbltitle\">".$user['race']['name']."</td>";
		echo "<td class=\"tbltitle\">".$c->sol_type_name."</td>";
		//echo "<td class=\"tbltitle\" rowspan=\"11\" width=\"1\"></td>";
		echo "<td class=\"tbltitle\">TOTAL</td></tr>";

		echo "<tr><td class=\"tbldata\">Produktion ".RES_METAL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->metal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['metal'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->metal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->metal,$user['race']['metal'],$c->sol->type->metal),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Produktion ".RES_CRYSTAL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->crystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['crystal'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->crystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->crystal,$user['race']['crystal'],$c->sol->type->crystal),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Produktion ".RES_PLASTIC."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->plastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['plastic'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->plastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->plastic,$user['race']['plastic'],$c->sol->type->plastic),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Produktion ".RES_FUEL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->fuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['fuel'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->fuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->fuel,$user['race']['fuel'],$c->sol->type->fuel),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Produktion ".RES_FOOD."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->food,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['food'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->food,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->food,$user['race']['food'],$c->sol->type->food),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Produktion Energie</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->power,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['power'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->power,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->power,$user['race']['power'],$c->sol->type->power),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Bev&ouml;lkerungswachstum</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->population,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['population'],1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->population,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->population,$user['race']['population'],$c->sol->type->population),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Forschungszeit</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->researchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['researchtime'],1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->researchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->researchtime,$user['race']['researchtime'],$c->sol->type->researchtime),1,1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Bauzeit (Geb&auml;ude)</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->type->buildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['buildtime'],1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($c->sol->type->buildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($c->type->buildtime,$user['race']['buildtime'],$c->sol->type->buildtime),1,1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">Fluggeschwindigkeit</td>";
		echo "<td class=\"tbldata\">-</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['fleettime'],1,1)."</td>";
		echo "<td class=\"tbldata\">-</td>";
		echo "<td class=\"tbldata\">".get_percent_string($user['race']['fleettime'],1,1)."</td></tr>";

		infobox_end(1);

	}
	else
		echo "<h2>Fehler</h2> Dieser Planet existiert nicht oder er geh&ouml;rt nicht dir!";

?>

