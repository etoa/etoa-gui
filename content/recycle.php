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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: recycle.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Recycles ships and defense
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// DEFINITIONEN //

	define('HELP_URL_DEF',"?page=help&site=defense");
	define('HELP_URL_SHIP',"?page=help&site=shipyard");

	// BEGIN SKRIPT //

	echo "<h1>Recyclingstation des Planeten ".$cp->name."</h1>";
  $cp->resBox();
	
	//Recycling Level laden
	$rtres = dbquery("
	SELECT
		techlist_current_level
	FROM
		".$db_table['techlist']."
	WHERE
		techlist_user_id=".$cu->id()."
        AND techlist_tech_id=".RECYC_TECH_ID."
        AND techlist_current_level>0;");

	if (mysql_num_rows($rtres)>0)
	{
		$rtarr = mysql_fetch_row($rtres);
		$tech_level = $rtarr[0];
		$payback_max = RECYC_MAX_PAYBACK;
		$payback = ($payback_max)-($payback_max/$tech_level);
		$pb_percent = round($payback*100,2);
    $pb[0]=0;
    $pb[1]=0;
    $pb[2]=0;
    $pb[3]=0;
    $pb[4]=0;
    $cnt=0;
    $log_ships="";
    $log_def="";

 		echo "Deine Recyclingtechnologie ist auf Stufe ".$tech_level." entwickelt. Es werden ".$pb_percent." % der Kosten zur&uuml;ckerstattet.<br/>Der Recyclingvorgang kann nicht r&uuml;ckgangig gemacht werden, die Objekte werden sofort verschrottet!<br>";	

		//Schiffe recyceln
		if ($_POST['submit_recycle_ships']!="")
		{
			//Anzahl muss grösser als 0 sein
			if (count($_POST['ship_count'])>0)
			{
				foreach ($_POST['ship_count'] as $id=>$num)
				{
					$num=abs($num);
					if($num>0)
					{
            $res = dbquery("
            SELECT
                ships.ship_name,
                ships.ship_costs_metal,
                ships.ship_costs_crystal,
                ships.ship_costs_plastic,
                ships.ship_costs_fuel,
                ships.ship_costs_food,
                shiplist.shiplist_count
            FROM
                ".$db_table['shiplist']."
                INNER JOIN
                ".$db_table['ships']."
            		ON
                ships.ship_id=shiplist.shiplist_ship_id
                AND shiplist.shiplist_user_id='".$cu->id()."'
                AND shiplist.shiplist_planet_id='".$cp->id()."'
                AND shiplist.shiplist_ship_id='".$id."';");
            if (mysql_num_rows($res)>0)
            {
                $arr = mysql_fetch_array($res);
                
                //Anzahl anpassen, wenn angegebene Anzahl grösser ist, als die effektive Anzahl auf dem Planeten
                if($num > $arr['shiplist_count'])
                {
                	$num=$arr['shiplist_count'];
              	}
              	
                //Schiffe vom Planeten abziehen
                dbquery("
                UPDATE
                    ".$db_table['shiplist']."
                SET
                    shiplist_count=shiplist_count-".$num."
                WHERE
                    shiplist_planet_id='".$cp->id()."'
                    AND shiplist_ship_id='".$id."'
                    AND shiplist_user_id='".$cu->id()."';");

                //Rohstoffe summieren
                $pb[0]+=ceil($payback*$arr['ship_costs_metal']*$num);
                $pb[1]+=ceil($payback*$arr['ship_costs_crystal']*$num);
                $pb[2]+=ceil($payback*$arr['ship_costs_plastic']*$num);
                $pb[3]+=ceil($payback*$arr['ship_costs_fuel']*$num);
                $pb[4]+=ceil($payback*$arr['ship_costs_food']*$num);
                $cnt+=$num;

                $log_ships.="[B]".$arr['ship_name'].":[/B] ".$num."\n";
              	
            }
        	}
				}
				
				//Rohstoffe Updaten
				dbquery("
				UPDATE
					".$db_table['planets']."
				SET
          planet_res_metal=planet_res_metal+".$pb[0].",
          planet_res_crystal=planet_res_crystal+".$pb[1].",
          planet_res_plastic=planet_res_plastic+".$pb[2].",
          planet_res_fuel=planet_res_fuel+".$pb[3].",
          planet_res_food=planet_res_food+".$pb[4]."
				WHERE
					planet_id='".$cp->id()."';");
					
					
				//Rohstoffe auf dem Planeten aktualisieren
		    $cp->resMetal+=$pb[0];
		    $cp->resCrystal+=$pb[1];
		    $cp->resPlastic+=$pb[2];
		    $cp->resFuel+=$pb[3];
		    $cp->resFood+=$pb[4];				


				//Log schreiben
				$log="Der User [URL=?page=user&sub=edit&user_id=".$cu->id()."] [B]".$_SESSION[ROUNDID]['user']['nick']."[/B] [/URL] hat auf dem Planeten [URL=?page=galaxy&sub=edit&planet_id=".$cp->id()."][B]".$cp->name."[/B][/URL] folgende Schiffe mit dem r&uuml;ckgabewert von ".($payback*100)."% recycelt:\n\n".$log_ships."\nDies hat ihm folgende Rohstoffe gegeben:\n".RES_METAL.": ".nf($pb[0])."\n".RES_CRYSTAL.": ".nf($pb[1])."\n".RES_PLASTIC.": ".nf($pb[2])."\n".RES_FUEL.": ".nf($pb[3])."\n".RES_FOOD.": ".nf($pb[4])."\n";

				add_log(12,$log,time());

			}
			echo "<br>".nf($cnt)." Schiffe erfolgreich recycelt!<br>";
		}


		//Verteidigungsanlagen recyceln
		if ($_POST['submit_recycle_def']!="")
		{
			//Anzahl muss grösser als 0 sein
			if (count($_POST['def_count'])>0)
			{
				foreach ($_POST['def_count'] as $id=>$num)
				{
					$num=abs($num);
					if($num>0)
					{
            $res = dbquery("
            SELECT
                defense.def_name,
                defense.def_costs_metal,
                defense.def_costs_crystal,
                defense.def_costs_plastic,
                defense.def_costs_fuel,
                defense.def_costs_food,
                defense.def_fields,
                deflist.deflist_count
            FROM
                ".$db_table['deflist']."
                INNER JOIN
                ".$db_table['defense']."
            		ON
                defense.def_id=deflist.deflist_def_id
                AND deflist.deflist_planet_id='".$cp->id()."'
                AND deflist.deflist_def_id='".$id."'
                AND deflist.deflist_user_id='".$cu->id()."' ;");
            if (mysql_num_rows($res)>0)
            {
                $arr = mysql_fetch_array($res);
                
                 //Anzahl anpassen, wenn angegebene Anzahl grösser ist, als die effektive Anzahl auf dem Planeten
                if($num > $arr['deflist_count'])
                {
                	$num=$arr['deflist_count'];
              	}
              	               
                //Defese vom Planeten Abziehen
                dbquery("
                UPDATE
                    ".$db_table['deflist']."
                SET
                    deflist_count=deflist_count-".$num."
                WHERE
                    deflist_planet_id='".$cp->id()."'
                    AND deflist_def_id='".$id."'
                    AND deflist_user_id='".$cu->id()."';");

                //Rohstoffe summieren
                $pb[0]+=ceil($payback*$arr['def_costs_metal']*$num);
                $pb[1]+=ceil($payback*$arr['def_costs_crystal']*$num);
                $pb[2]+=ceil($payback*$arr['def_costs_plastic']*$num);
                $pb[3]+=ceil($payback*$arr['def_costs_fuel']*$num);
                $pb[4]+=ceil($payback*$arr['def_costs_food']*$num);
                $fields+=$arr['def_fields']*$num;
                $cnt+=$num;

                $log_def.="[B]".$arr['def_name'].":[/B] ".$num."\n";
            }
        	}
				}
				
				//Rohstoffe und Felder updaten
				dbquery("
				UPDATE
					".$db_table['planets']."
				SET
          planet_res_metal=planet_res_metal+".$pb[0].",
          planet_res_crystal=planet_res_crystal+".$pb[1].",
          planet_res_plastic=planet_res_plastic+".$pb[2].",
          planet_res_fuel=planet_res_fuel+".$pb[3].",
          planet_res_food=planet_res_food+".$pb[4].",
          planet_fields_used=planet_fields_used-".$fields."
				WHERE
					planet_id='".$cp->id()."';");

				//Rohstoffe auf dem Planeten aktualisieren
		    $cp->resMetal+=$pb[0];
		    $cp->resCrystal+=$pb[1];
		    $cp->resPlastic+=$pb[2];
		    $cp->resFuel+=$pb[3];
		    $cp->resFood+=$pb[4];

				//Log schreiben
				$log="Der User [URL=?page=user&sub=edit&user_id=".$cu->id()."] [B]".$_SESSION[ROUNDID]['user']['nick']."[/B] [/URL] hat auf dem Planeten [URL=?page=galaxy&sub=edit&planet_id=".$cp->id()."][B]".$cp->name."[/B][/URL] folgende Verteidigungsanlagen mit dem r&uuml;ckgabewert von ".($payback*100)."% recycelt:\n\n".$log_def."\nDies hat ihm folgende Rohstoffe gegeben:\n".RES_METAL.": ".nf($pb[0])."\n".RES_CRYSTAL.": ".nf($pb[1])."\n".RES_PLASTIC.": ".nf($pb[2])."\n".RES_FUEL.": ".nf($pb[3])."\n".RES_FOOD.": ".nf($pb[4])."\n";

				add_log(12,$log,time());
			}
			echo "<br>".nf($cnt)." Verteidigungsanlagen erfolgreich recycelt!<br>";
		}


		//
		//Schiffe
		//
		echo "<h2>Schiffe</h2>";

		$res = dbquery("
		SELECT
			s.ship_id,
			s.ship_name,
			sl.shiplist_count
		FROM
      ".$db_table['ships']." AS s
      INNER JOIN
      ".$db_table['shiplist']." AS sl
      ON s.ship_id=sl.shiplist_ship_id
      AND sl.shiplist_planet_id='".$cp->id()."'
      AND s.ship_buildable='1'
      AND s.special_ship='0'
      AND sl.shiplist_count>'0'
      AND sl.shiplist_user_id='".$cu->id()."'
		ORDER BY
			s.ship_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<form action=\"?page=$page\" method=\"POST\">";
			echo "<table width=\"500\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">\n";
			echo "<tr>
							<td class=\"tbltitle\" width=\"390\" colspan=\"2\" valign=\"top\">Typ</td>
							<td class=\"tbltitle\" valign=\"top\" width=\"110\">Anzahl</td>
							<td class=\"tbltitle\" valign=\"top\" width=\"110\">Auswahl</td>
						</tr>\n";

			$tabulator=1;
			while ($arr = mysql_fetch_array($res))
			{
				$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT;
				echo "<tr>
								<td class=\"tbldata\" width=\"40\">
									<a href=\"".HELP_URL_SHIP."&amp;id=".$arr['ship_id']."\"><img src=\"$s_img\" width=\"40\"  height=\"40\" border=\"0\"/></a>
								</td>";
					echo "<td class=\"tbldata\" width=\"66%\" valign=\"middle\">".$arr['ship_name']."</td>";
					echo "<td class=\"tbldata\" width=\"22%\" valign=\"middle\">".nf($arr['shiplist_count'])."</td>";
					echo "<td class=\"tbldata\" width=\"12%\" valign=\"middle\"><input type=\"text\" name=\"ship_count[".$arr['ship_id']."]\" size=\"8\" maxlength=\"".strlen($arr['shiplist_count'])."\" value=\"0\" title=\"Anzahl welche recyclet werden sollen\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\">
								</td>
						</tr>\n";
			}
			
			echo "</table><br/>\n";
			echo "<input type=\"submit\" class=\"button\" name=\"submit_recycle_ships\" value=\"Ausgew&auml;hlte Schiffe recyceln\"><br/></form>";
		}
		else
		{
			echo "Es sind keine Schiffe auf diesem Planeten vorhanden!";
		}


		//
		//Verteidigung
		//
		echo "<h2>Verteidigungsanlagen</h2>";
		$res = dbquery("
		SELECT
			d.def_id,
			d.def_name,
			dl.deflist_count
		FROM
      ".$db_table['defense']." AS d
      INNER JOIN
      ".$db_table['deflist']." AS dl
      ON d.def_id=dl.deflist_def_id
      AND dl.deflist_planet_id='".$cp->id()."'
      AND d.def_buildable='1'
      AND dl.deflist_user_id='".$cu->id()."'
      AND dl.deflist_count>0
		ORDER BY
			def_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<form action=\"?page=$page\" method=\"POST\">";
			echo "<table width=\"500\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">\n";
			echo "<tr>
							<td class=\"tbltitle\"  colspan=\"2\">Typ</td>
							<td class=\"tbltitle\" valign=\"top\" width=\"110\">Anzahl</td>
							<td class=\"tbltitle\" valign=\"top\" width=\"110\">Auswahl</td>
						</tr>\n";
			$tabulator=1;
			while ($arr = mysql_fetch_array($res))
			{
				$s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT; //image angepasst by Lamborghini
				echo "<tr>
								<td class=\"tbldata\" width=\"40\">
									<a href=\"".HELP_URL_DEF."&amp;id=".$arr['def_id']."\"><img src=\"$s_img\" width=\"40\"  height=\"40\" border=\"0\"/></a>
								</td>";
					echo "<td class=\"tbldata\" width=\"66%\" valign=\"middle\">".$arr['def_name']."</td>";
					echo "<td class=\"tbldata\" width=\"22%\" valign=\"middle\">".nf($arr['deflist_count'])."</td>";
					echo "<td class=\"tbldata\" width=\"12%\" valign=\"middle\"><input type=\"text\" name=\"def_count[".$arr['def_id']."]\" size=\"8\" maxlength=\"".strlen($arr['deflist_count'])."\" value=\"0\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\"></td>
						</tr>\n";
				$tabulator++;
			}
			echo "</table><br/>\n";
			echo "<input type=\"submit\" class=\"button\" name=\"submit_recycle_def\" value=\"Ausgew&auml;hlte Anlagen recyceln\"></form>";
		}
		else
			echo "Es sind keine Verteidigungsanlagen auf diesem Planeten vorhanden!";
	}
	else
	{
		echo "Die Recyclingtechnologie wurde noch nicht entwickelt!";
	}



//
