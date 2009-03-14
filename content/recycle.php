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
  $cp->resBox($cu->properties->smallResBox);
	
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


		echo "<h2>Mobile Anlagen</h2>";		
		
		if (isset($_POST['dtransform_submit']))
		{
			$sl = new ShipList($cp->id,$cu->id);
			$dl = new DefList($cp->id,$cu->id);
			
			$cnt=0;
			if (isset($_POST['dtransform']) && count($_POST['dtransform']) >0)
			{
				foreach ($_POST['dtransform'] as $k => $v)
				{
					$res = dbquery("
					SELECT
						l.deflist_count as cnt,			
						t.ship_id as id,
						t.num_def
					FROM
						deflist l
					INNER JOIN
						obj_transforms t
						ON t.def_id=l.deflist_def_id
						AND l.deflist_user_id=".$cu->id."
						AND l.deflist_entity_id=".$cp->id."
						AND l.deflist_count > 0
						AND l.deflist_def_id=".$k."
					");					
					if (mysql_num_rows($res))
					{
						$arr = mysql_fetch_assoc($res);
						$packcount = min(max(0,$v),$arr['cnt']);
						
						if ($packcount>0)
						{
							$sl->add($arr['id'],$dl->remove($k,$packcount));
							$cnt += $packcount;
						}
					}
				}
			}
			if ($cnt>0)
			{
				ok_msg("$packcount Verteidigungsanlagen wurden verladen!");
			}			
		}

		if (isset($_POST['stransform_submit']))
		{
			$sl = new ShipList($cp->id,$cu->id);
			$dl = new DefList($cp->id,$cu->id);
			
			$cnt=0;
			if (isset($_POST['stransform']) && count($_POST['stransform']) >0)
			{
				foreach ($_POST['stransform'] as $k => $v)
				{
					$res = dbquery("
					SELECT
						l.shiplist_count as cnt,			
						t.def_id as id,
						t.num_def
					FROM
						shiplist l
					INNER JOIN
						obj_transforms t
						ON t.ship_id=l.shiplist_ship_id
						AND l.shiplist_user_id=".$cu->id."
						AND l.shiplist_entity_id=".$cp->id."
						AND l.shiplist_count > 0
						AND l.shiplist_ship_id=".$k."
					");					
					if (mysql_num_rows($res))
					{
						$arr = mysql_fetch_assoc($res);
						$packcount = min(max(0,$v),$arr['cnt']);
						if ($packcount>0)
						{
							$dl->add($arr['id'],$sl->remove($k,$packcount));
							$cnt += $packcount;
						}
					}
				}
			}
			if ($cnt>0)
			{
				ok_msg("$packcount Verteidigungsanlagen wurden installiert!");
			}			
		}


		$mob = false;
		$otres = dbquery("
		SELECT
			d.def_id as id,
			d.def_name as name,
			l.deflist_count as cnt			
		FROM
			defense d
		INNER JOIN
			obj_transforms t
			ON t.def_id=d.def_id
		INNER JOIN
			deflist l
			ON l.deflist_def_id=d.def_id
			AND l.deflist_user_id=".$cu->id."
			AND l.deflist_entity_id=".$cp->id."
			AND l.deflist_count > 0
		");
		if (mysql_num_rows($otres) > 0)
		{
			$mob = true;
			echo "<form action=\"?page=$page\" method=\"post\">";
			tableStart("Verteidigungsanlagen auf Träger verladen");
			echo "<tr><th>Typ</th><th>Anzahl</th></tr>";
			while ($otarr = mysql_fetch_assoc($otres))
			{
				echo "<tr><td>".$otarr['name']."</td>
				<td><input type=\"text\" name=\"dtransform[".$otarr['id']."]\" value=\"".$otarr['cnt']."\" size=\"7\" /></td></tr>";
			}
			tableEnd();
			echo "<input type=\"submit\" name=\"dtransform_submit\" value=\"Verladen\" /></form><br/>";
		}

		$otres = dbquery("
		SELECT
			d.ship_id as id,
			d.ship_name as name,
			l.shiplist_count as cnt			
		FROM
			ships d
		INNER JOIN
			obj_transforms t
			ON t.ship_id=d.ship_id
		INNER JOIN
			shiplist l
			ON l.shiplist_ship_id=d.ship_id
			AND l.shiplist_user_id=".$cu->id."
			AND l.shiplist_entity_id=".$cp->id."
			AND l.shiplist_count > 0
		");
		if (mysql_num_rows($otres) > 0)
		{
			$mob = true;
			echo "<form action=\"?page=$page\" method=\"post\">";
			tableStart("Mobile Verteidigung installieren");
			echo "<tr><th>Typ</th><th>Anzahl</th></tr>";
			while ($otarr = mysql_fetch_assoc($otres))
			{
				echo "<tr><td>".$otarr['name']."</td>
				<td><input type=\"text\" name=\"stransform[".$otarr['id']."]\" value=\"".$otarr['cnt']."\" size=\"7\" /></td></tr>";
			}
			tableEnd();
			echo "<input type=\"submit\" name=\"stransform_submit\" value=\"Ausladen und installieren\" /></form><br/>";
		}

		if (!$mob)
			echo "Keine mobilen Anlagen vorhanden!<br/>";
		
		
		tableStart("Recycling");
 		echo "<tr><td>Deine Recyclingtechnologie ist auf Stufe ".$tech_level." entwickelt. Es werden ".$pb_percent." % der Kosten zur&uuml;ckerstattet.<br/>Der Recyclingvorgang kann nicht r&uuml;ckgangig gemacht werden, die Objekte werden sofort verschrottet!</td></tr>";
		tableEnd();

		//Schiffe recyceln
		if (isset($_POST['submit_recycle_ships']) && $_POST['submit_recycle_ships']!="")
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
				$log="Der User [URL=?page=user&sub=edit&user_id=".$cu->id."] [B]".$cu."[/B] [/URL] hat auf dem Planeten [URL=?page=galaxy&sub=edit&planet_id=".$cp->id()."][B]".$cp->name."[/B][/URL] folgende Schiffe mit dem r&uuml;ckgabewert von ".($payback*100)."% recycelt:\n\n".$log_ships."\nDies hat ihm folgende Rohstoffe gegeben:\n".RES_METAL.": ".nf($pb[0])."\n".RES_CRYSTAL.": ".nf($pb[1])."\n".RES_PLASTIC.": ".nf($pb[2])."\n".RES_FUEL.": ".nf($pb[3])."\n".RES_FOOD.": ".nf($pb[4])."\n";

				add_log(12,$log,time());

			}
			ok_msg(nf($cnt)." Schiffe erfolgreich recycelt!");
		}


		//Verteidigungsanlagen recyceln
		if (isset($_POST['submit_recycle_def']) && $_POST['submit_recycle_def']!="")
		{
			//Anzahl muss grösser als 0 sein
			if (count($_POST['def_count'])>0)
			{
				$fields = 0;
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
				$log="Der User [URL=?page=user&sub=edit&user_id=".$cu->id."] [B]".$cu."[/B] [/URL] hat auf dem Planeten [URL=?page=galaxy&sub=edit&planet_id=".$cp->id()."][B]".$cp->name."[/B][/URL] folgende Verteidigungsanlagen mit dem r&uuml;ckgabewert von ".($payback*100)."% recycelt:\n\n".$log_def."\nDies hat ihm folgende Rohstoffe gegeben:\n".RES_METAL.": ".nf($pb[0])."\n".RES_CRYSTAL.": ".nf($pb[1])."\n".RES_PLASTIC.": ".nf($pb[2])."\n".RES_FUEL.": ".nf($pb[3])."\n".RES_FOOD.": ".nf($pb[4])."\n";

				add_log(12,$log,time());
			}
			ok_msg("".nf($cnt)." Verteidigungsanlagen erfolgreich recycelt!");
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
			error_msg("Es sind keine Schiffe auf diesem Planeten vorhanden!");
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
			error_msg("Es sind keine Verteidigungsanlagen auf diesem Planeten vorhanden!");
	}
	else
	{
		error_msg("Die Recyclingtechnologie wurde noch nicht entwickelt!");
	}



//
