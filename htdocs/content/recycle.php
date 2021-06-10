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
	* Recycles ships and defense
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	// DEFINITIONEN //

	define('HELP_URL_DEF',"?page=help&site=defense");
	define('HELP_URL_SHIP',"?page=help&site=shipyard");

	// Maxmimale Recyclingtech effizient
	define("RECYC_MAX_PAYBACK", $cfg->recyc_max_payback->v);

	// BEGIN SKRIPT //

	echo "<h1>Recyclingstation des Planeten ".$cp->name."</h1>";
	echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);

	//Recycling Level laden
	$rtres = dbquery("
	SELECT
		techlist_current_level
	FROM
		techlist
	WHERE
		techlist_user_id=".$cu->id."
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

		tableStart("Recycling");
 		echo "<tr><td>Deine Recyclingtechnologie ist auf Stufe ".$tech_level." entwickelt. Es werden ".$pb_percent." % der Kosten zur&uuml;ckerstattet.<br/>Der Recyclingvorgang kann nicht r&uuml;ckg&auml;ngig gemacht werden, die Objekte werden sofort verschrottet!</td></tr>";
		tableEnd();

		//Schiffe recyceln
		if (isset($_POST['submit_recycle_ships']) && $_POST['submit_recycle_ships']!="")
		{
			//Anzahl muss grösser als 0 sein
			if (count($_POST['ship_count'])>0)
			{
			    $recycled = [];
				foreach ($_POST['ship_count'] as $id=>$num)
				{
					$id = intval($id);

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
                shiplist
                INNER JOIN
                ships
            		ON
                ships.ship_id=shiplist.shiplist_ship_id
                AND shiplist.shiplist_user_id='".$cu->id."'
                AND shiplist.shiplist_entity_id='".$cp->id()."'
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
                    shiplist
                SET
                    shiplist_count=shiplist_count-".$num."
                WHERE
                    shiplist_entity_id='".$cp->id()."'
                    AND shiplist_ship_id='".$id."'
                    AND shiplist_user_id='".$cu->id."';");

                //Rohstoffe summieren
                $pb[0]+=ceil($payback*$arr['ship_costs_metal']*$num);
                $pb[1]+=ceil($payback*$arr['ship_costs_crystal']*$num);
                $pb[2]+=ceil($payback*$arr['ship_costs_plastic']*$num);
                $pb[3]+=ceil($payback*$arr['ship_costs_fuel']*$num);
                $pb[4]+=ceil($payback*$arr['ship_costs_food']*$num);
                $cnt+=$num;

                $log_ships.="[B]".$arr['ship_name'].":[/B] ".$num."\n";
                $recycled[$id] = $num;

            }
        	}
				}

				//Rohstoffe Updaten
				dbquery("
				UPDATE
					planets
				SET
          planet_res_metal=planet_res_metal+".$pb[0].",
          planet_res_crystal=planet_res_crystal+".$pb[1].",
          planet_res_plastic=planet_res_plastic+".$pb[2].",
          planet_res_fuel=planet_res_fuel+".$pb[3].",
          planet_res_food=planet_res_food+".$pb[4]."
				WHERE
					id='".$cp->id()."';");


				//Rohstoffe auf dem Planeten aktualisieren
		    $cp->resMetal+=$pb[0];
		    $cp->resCrystal+=$pb[1];
		    $cp->resPlastic+=$pb[2];
		    $cp->resFuel+=$pb[3];
		    $cp->resFood+=$pb[4];


				//Log schreiben
				$log="Der User [page user sub=edit user_id=".$cu->id."] [B]".$cu."[/B] [/page] hat auf dem Planeten [page galaxy sub=edit id=".$cp->id()."][B]".$cp->name."[/B][/page] folgende Schiffe mit dem r&uuml;ckgabewert von ".($payback*100)."% recycelt:\n\n".$log_ships."\nDies hat ihm folgende Rohstoffe gegeben:\n".RES_METAL.": ".nf($pb[0])."\n".RES_CRYSTAL.": ".nf($pb[1])."\n".RES_PLASTIC.": ".nf($pb[2])."\n".RES_FUEL.": ".nf($pb[3])."\n".RES_FOOD.": ".nf($pb[4])."\n";

				add_log(12,$log,time());

			}
			success_msg(nf($cnt)." Schiffe erfolgreich recycelt!");
            foreach ($recycled as $id => $num) {
                $app['dispatcher']->dispatch(new \EtoA\Ship\Event\ShipRecycle($id, $num), \EtoA\Ship\Event\ShipRecycle::RECYCLE_SUCCESS);
            }
		}


		//Verteidigungsanlagen recyceln
		if (isset($_POST['submit_recycle_def']) && $_POST['submit_recycle_def']!="")
		{
			//Anzahl muss grösser als 0 sein
			if (count($_POST['def_count'])>0)
			{
				$fields = 0;
                $recycled = [];
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
                deflist
                INNER JOIN
                defense
            		ON
                defense.def_id=deflist.deflist_def_id
                AND deflist.deflist_entity_id='".$cp->id()."'
                AND deflist.deflist_def_id='".$id."'
                AND deflist.deflist_user_id='".$cu->id."' ;");
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
                    deflist
                SET
                    deflist_count=deflist_count-".$num."
                WHERE
                    deflist_entity_id='".$cp->id()."'
                    AND deflist_def_id='".$id."'
                    AND deflist_user_id='".$cu->id."';");

                //Rohstoffe summieren
                $pb[0]+=ceil($payback*$arr['def_costs_metal']*$num);
                $pb[1]+=ceil($payback*$arr['def_costs_crystal']*$num);
                $pb[2]+=ceil($payback*$arr['def_costs_plastic']*$num);
                $pb[3]+=ceil($payback*$arr['def_costs_fuel']*$num);
                $pb[4]+=ceil($payback*$arr['def_costs_food']*$num);
                $fields+=$arr['def_fields']*$num;
                $cnt+=$num;

                $log_def.="[B]".$arr['def_name'].":[/B] ".$num."\n";
                $recycled[$id] = $num;
            }
        	}
				}

				//Rohstoffe und Felder updaten
				dbquery("
				UPDATE
					planets
				SET
          planet_res_metal=planet_res_metal+".$pb[0].",
          planet_res_crystal=planet_res_crystal+".$pb[1].",
          planet_res_plastic=planet_res_plastic+".$pb[2].",
          planet_res_fuel=planet_res_fuel+".$pb[3].",
          planet_res_food=planet_res_food+".$pb[4].",
          planet_fields_used=planet_fields_used-".$fields."
				WHERE
					id='".$cp->id()."';");

				//Rohstoffe auf dem Planeten aktualisieren
		    $cp->resMetal+=$pb[0];
		    $cp->resCrystal+=$pb[1];
		    $cp->resPlastic+=$pb[2];
		    $cp->resFuel+=$pb[3];
		    $cp->resFood+=$pb[4];

				//Log schreiben
				$log="Der User [page user sub=edit user_id=".$cu->id."] [B]".$cu."[/B] [/page] hat auf dem Planeten [page galaxy sub=edit id=".$cp->id()."][B]".$cp->name."[/B][/page] folgende Verteidigungsanlagen mit dem r&uuml;ckgabewert von ".($payback*100)."% recycelt:\n\n".$log_def."\nDies hat ihm folgende Rohstoffe gegeben:\n".RES_METAL.": ".nf($pb[0])."\n".RES_CRYSTAL.": ".nf($pb[1])."\n".RES_PLASTIC.": ".nf($pb[2])."\n".RES_FUEL.": ".nf($pb[3])."\n".RES_FOOD.": ".nf($pb[4])."\n";

				add_log(12,$log,time());
			}
			success_msg("".nf($cnt)." Verteidigungsanlagen erfolgreich recycelt!");
            foreach ($recycled as $id => $num) {
                $app['dispatcher']->dispatch(new \EtoA\Defense\Event\DefenseRecycle($id, $num), \EtoA\Defense\Event\DefenseRecycle::RECYCLE_SUCCESS);
            }
		}


		//
		//Schiffe
		//
		$res = dbquery("
		SELECT
			s.ship_id,
			s.ship_name,
			sl.shiplist_count
		FROM
      ships AS s
      INNER JOIN
      shiplist AS sl
      ON s.ship_id=sl.shiplist_ship_id
      AND sl.shiplist_entity_id='".$cp->id()."'
      AND s.ship_buildable='1'
      AND s.special_ship='0'
      AND sl.shiplist_count>'0'
      AND sl.shiplist_user_id='".$cu->id."'
		ORDER BY
			s.ship_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<form action=\"?page=$page\" method=\"POST\">";
			tableStart("Schiffe");
			echo "<tr>
							<th width=\"390\" colspan=\"2\" valign=\"top\">Typ</th>
							<th valign=\"top\" width=\"110\">Anzahl</th>
							<th valign=\"top\" width=\"110\">Auswahl</th>
						</tr>\n";

			$tabulator=1;
			while ($arr = mysql_fetch_array($res))
			{
				$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT;
				echo "<tr>
								<td width=\"40\">
									<a href=\"".HELP_URL_SHIP."&amp;id=".$arr['ship_id']."\"><img src=\"$s_img\" width=\"40\"  height=\"40\" border=\"0\"/></a>
								</td>";
					echo "<td width=\"66%\" valign=\"middle\">".$arr['ship_name']."</td>";
					echo "<td width=\"22%\" valign=\"middle\">".nf($arr['shiplist_count'])."</td>";
					echo "<td width=\"12%\" valign=\"middle\"><input type=\"text\" name=\"ship_count[".$arr['ship_id']."]\" size=\"8\" maxlength=\"".strlen($arr['shiplist_count'])."\" value=\"0\" title=\"Anzahl welche recyclet werden sollen\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\">
								</td>
						</tr>\n";
			}

			tableEnd();
			echo "<input type=\"submit\" class=\"button\" name=\"submit_recycle_ships\" value=\"Ausgew&auml;hlte Schiffe recyceln\"><br/></form>";
		}
		else
		{
			info_msg("Es sind keine Schiffe auf diesem Planeten vorhanden!");
		}


		//
		//Verteidigung
		//
		$res = dbquery("
		SELECT
			d.def_id,
			d.def_name,
			dl.deflist_count
		FROM
      defense AS d
      INNER JOIN
      deflist AS dl
      ON d.def_id=dl.deflist_def_id
      AND dl.deflist_entity_id='".$cp->id()."'
      AND d.def_buildable='1'
      AND dl.deflist_user_id='".$cu->id."'
      AND dl.deflist_count>0
		ORDER BY
			def_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<form action=\"?page=$page\" method=\"POST\">";
			tableStart("Verteidigungsanlagen");
			echo "<tr>
							<th colspan=\"2\">Typ</th>
							<th valign=\"top\" width=\"110\">Anzahl</th>
							<th valign=\"top\" width=\"110\">Auswahl</th>
						</tr>\n";
			$tabulator=1;
			while ($arr = mysql_fetch_array($res))
			{
				$s_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$arr['def_id']."_small.".IMAGE_EXT; //image angepasst by Lamborghini
				echo "<tr>
								<td width=\"40\">
									<a href=\"".HELP_URL_DEF."&amp;id=".$arr['def_id']."\"><img src=\"$s_img\" width=\"40\"  height=\"40\" border=\"0\"/></a>
								</td>";
					echo "<td width=\"66%\" valign=\"middle\">".$arr['def_name']."</td>";
					echo "<td width=\"22%\" valign=\"middle\">".nf($arr['deflist_count'])."</td>";
					echo "<td width=\"12%\" valign=\"middle\"><input type=\"text\" name=\"def_count[".$arr['def_id']."]\" size=\"8\" maxlength=\"".strlen($arr['deflist_count'])."\" value=\"0\" tabindex=\"".$tabulator."\" onKeyPress=\"return nurZahlen(event)\"></td>
						</tr>\n";
				$tabulator++;
			}
			tableEnd();
			echo "<input type=\"submit\" class=\"button\" name=\"submit_recycle_def\" value=\"Ausgew&auml;hlte Anlagen recyceln\"></form>";
		}
		else
			info_msg("Es sind keine Verteidigungsanlagen auf diesem Planeten vorhanden!", 1);
	}
	else
	{
		info_msg("Es können keine Schiffe oder Verteidigungsanlagen recycelt werden, da die Recyclingtechnologie noch nicht erforscht wurde!", 1);
	}



//
