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
	// 	File: research.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Manages technology research
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

   // DEFINITIONEN //

  define('NUM_BUILDINGS_PER_ROW',4);
  define('TECH_BUILDING_ID',8);
  define('CELL_WIDTH',175);


	// SKRIPT //
	if ($planets->current)
	{
		$res = dbquery("
        SELECT 
        	buildlist_people_working,
        	buildlist_current_level
        FROM 
          ".$db_table['buildlist']."
     		WHERE
        	buildlist_planet_id='".$c->id."' 
      		AND buildlist_building_id='".TECH_BUILDING_ID."'
	  	;");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_row($res);
			define('CURRENT_LAB_LEVEL',$arr[1]);
			define('PEOPLE_WORKING',$arr[0]);
			
			// Überschrift
			echo "<h1>Forschungslabor (Stufe ".CURRENT_LAB_LEVEL.") des Planeten ".$c->name."</h1>";
			$c->resBox();

      //level zählen welches das forschungslabor über dem angegeben level ist und faktor berechnen
      $need_bonus_level = CURRENT_LAB_LEVEL - $conf['build_time_boni_forschungslabor']['p1'];
      if($need_bonus_level<=0)
      {
          $time_boni_factor=1;
      }
      else
      {
          $time_boni_factor=max($conf['build_time_boni_forschungslabor']['p2'] , 1-($need_bonus_level*($conf['build_time_boni_forschungslabor']['v']/100)));
      }


		// Forschungsliste laden
		$blres = dbquery("
		SELECT 
			* 
		FROM 
			".$db_table['techlist']." 
		WHERE 
			techlist_user_id='".$s['user']['id']."';");
		$builing_something=false;
		while ($blarr = mysql_fetch_array($blres))
		{
			$techlist[$blarr['techlist_tech_id']]=$blarr;
			if ($blarr['techlist_build_type']!=0) 
			{
				$builing_something=true;
			}
		}

		//Gentech level laden
		$tlres = dbquery("
		SELECT
			techlist_current_level
		FROM
			".$db_table['techlist']."
		WHERE
            techlist_user_id='".$s['user']['id']."'
            AND techlist_tech_id='".GEN_TECH_ID."';");
		if(mysql_num_rows($tlres)>0)
		{
			$tlarr = mysql_fetch_array($tlres);
			define("GEN_TECH_LEVEL",$tlarr['techlist_current_level']);
    }
    else
    {
    	define("GEN_TECH_LEVEL",0);
    }


		// Load built buildings
		$tres = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['buildlist']." 
			WHERE 
				buildlist_user_id='".$s['user']['id']."' 
				AND buildlist_planet_id='".$c->id."'
			;");
		while ($tarr = mysql_fetch_array($tres))
		{
			$buildlist[$tarr['buildlist_building_id']]=$tarr['buildlist_current_level'];
		}
		
		// Load requirements
		$rres = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['tech_requirements'].";");
		while ($rarr = mysql_fetch_array($rres))
		{
			if ($rarr['req_req_building_id']>0) 
				$b_req[$rarr['req_tech_id']]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
			if ($rarr['req_req_tech_id']>0) 
				$b_req[$rarr['req_tech_id']]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
		}


		//
		//Forschung erforschen/abbrechen
		//
		if (count($_POST)>0	&& checker_verify())
		{
			$bid = 0;
			foreach ($_POST as $k => $v)
			{
				if(stristr($k,'_x'))
				{
					$bid = eregi_replace('show_([0-9]+)_x', '\1', $k);
					break;
				}
			}
			if ($bid==0 && isset($_POST['show']))
			{
				$bid = $_POST['show'];
			}
			if ($bid==0 && isset($_POST['id']))
			{
				$bid = $_POST['id'];
			}			
			
			// Forschungsdaten laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['technologies']." 
			WHERE  
				tech_id='".$bid."'
				AND tech_show='1';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				if ($techlist[$arr['tech_id']]['techlist_current_level']!=null)
				{
					$b_level=$techlist[$arr['tech_id']]['techlist_current_level'];
				}
				else
				{
					$b_level=0;
				}

				if ($techlist[$arr['tech_id']]['techlist_build_type']!="")
				{
					$b_status=$techlist[$arr['tech_id']]['techlist_build_type'];
				}
				else
				{
					$b_status=0;
				}

				$bc = calcTechCosts($arr,$b_level);

				$bcn['metal'] = $arr['tech_costs_metal'] * pow($arr['tech_build_costs_factor'],$b_level+1);
				$bcn['crystal'] = $arr['tech_costs_crystal'] * pow($arr['tech_build_costs_factor'],$b_level+1);
				$bcn['plastic'] = $arr['tech_costs_plastic'] * pow($arr['tech_build_costs_factor'],$b_level+1);
				$bcn['fuel'] = $arr['tech_costs_fuel'] * pow($arr['tech_build_costs_factor'],$b_level+1);
				$bcn['food'] = $arr['tech_costs_food'] * pow($arr['tech_build_costs_factor'],$b_level+1);


				// Bauzeit
				$btime_global_factor = $conf['global_time']['v'];
				$btime_build_factor = $conf['res_build_time']['v'];
				$bonus = $c->race->researchtime + $c->type->researchtime + $c->sol->type->researchtime-2;

				$btime = ($bc['metal']+$bc['crystal']+$bc['plastic']+$bc['fuel']+$bc['food']) / 12 * $btime_global_factor * $btime_build_factor * $time_boni_factor;
				$btime *= $bonus;

				$btimen = ($bcn['metal']+$bcn['crystal']+$bcn['plastic']+$bcn['fuel']+$bcn['food']) / 12 * $btime_global_factor * $btime_build_factor * $time_boni_factor;
				$btimen  *= $bonus;

				$dtime = ($dc['metal']+$dc['crystal']+$dc['plastic']+$dc['fuel']+$dc['food']) / 12 * $btime_global_factor * $btime_build_factor * $time_boni_factor;
				$dtime  *= $bonus;

        $fres = dbquery("
        SELECT 
        	buildlist.buildlist_people_working 
        FROM 
          ".$db_table['buildlist']."
          INNER JOIN
          ".$db_table['buildings']." 
          ON
          buildlist.buildlist_building_id=buildings.building_id 
          AND buildlist.buildlist_planet_id='".$c->id."' 
          AND buildings.building_id='".TECH_BUILDING_ID."';");
    		if (mysql_num_rows($fres)>0)
        {
            $farr=mysql_fetch_array($fres);
            $btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
            $btime=$btime-PEOPLE_WORKING*3;
            if ($btime<$btime_min) $btime=$btime_min;
        }
        $bc['food']+=PEOPLE_WORKING*12;

				$start_time = $techlist[$arr['tech_id']]['techlist_build_start_time'];
				$end_time = $techlist[$arr['tech_id']]['techlist_build_end_time'];
				$planet_id = $techlist[$arr['tech_id']]['techlist_planet_id'];
				

				//
				// Befehle ausführen
				//

				if ($_POST['command_build']!="" && $b_status==0)
				{
					if (!$builing_something)
					{

							if ($c->res->metal >= $bc['metal'] && $c->res->crystal >= $bc['crystal'] && $c->res->plastic >= $bc['plastic']  && $c->res->fuel >= $bc['fuel']  && $c->res->food >= $bc['food'])
							{
								$end_time = time()+$btime;
								if (sizeof($techlist[$arr['tech_id']])>0)
								{
									dbquery("
									UPDATE 
										".$db_table['techlist']." 
									SET
	                  techlist_build_type='1',
	                  techlist_build_start_time='".time()."',
	                  techlist_build_end_time='".$end_time."',
	                  techlist_planet_id='".$c->id."'
									WHERE
										techlist_tech_id='".$arr['tech_id']."'
										AND techlist_user_id='".$s['user']['id']."';");
								}
								else
								{
									dbquery("
									INSERT INTO 
									".$db_table['techlist']." 
									(
										techlist_planet_id,
										techlist_build_type,
										techlist_build_start_time,
										techlist_build_end_time,
										techlist_tech_id,
										techlist_user_id
									)
									VALUES
									(
										'".$c->id."',
										'1',
										'".time()."',
										'".$end_time."',
										'".$arr['tech_id']."',
										'".$s['user']['id']."'
									);");

								}
								$planet_id=$c->id;
								
								//Rohstoffe vom Planeten abziehen und aktualisieren
								$c->changeRes(-$bc['metal'],-$bc['crystal'],-$bc['plastic'],-$bc['fuel'],-$bc['food']);
								$b_status=1;
								
								//Log schreiben
								$log_text = "
								<b>Forschung Ausbau</b><br><br>
								<b>User:</b> [USER_ID=".$s['user']['id'].";USER_NICK=".$s['user']['nick']."]<br>
								<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
								<b>Technologie:</b> ".$arr['tech_name']."<br>
								<b>Technologie Level:</b> ".$b_level." (vor Ausbau)<br>
								<b>Erforschungsdauer:</b> ".tf($btime)."<br>
								<b>Ende:</b> ".date("Y-m-d H:i:s",$end_time)."<br>
								<b>Forschungslabor Level:</b> ".CURRENT_LAB_LEVEL."<br>
								<b>Eingesetzte Bewohner:</b> ".nf(PEOPLE_WORKING)."<br>
								<b>Gen-Tech Level:</b> ".GEN_TECH_LEVEL."<br><br>
								<b>Kosten</b><br>
								<b>".RES_METAL.":</b> ".nf($bc['metal'])."<br>
								<b>".RES_CRYSTAL.":</b> ".nf($bc['crystal'])."<br>
								<b>".RES_PLASTIC.":</b> ".nf($bc['plastic'])."<br>
								<b>".RES_FUEL.":</b> ".nf($bc['fuel'])."<br>
								<b>".RES_FOOD.":</b> ".nf($bc['food'])."<br><br>
								<b>Restliche Rohstoffe auf dem Planeten</b><br><br>
								<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
								<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
								<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
								<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
								<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
								";
								
								//Log Speichern
								add_log_game_research($log_text,$s['user']['id'],$s['user']['alliance_id'],$c->id,$arr['tech_id'],$b_status,time());								
								
							}
							else
							{
								echo "<i>Bauauftrag kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!</i><br/><br/>";
							}
					}
					else
					{
						echo "<i>Bauauftrag kann nicht gestartet werden, es wird bereits an einem Geb&auml;ude gearbeitet!</i><br/><br/>";
					}
				}


				if ($_POST['command_cbuild']!="" && $b_status==1)
				{
					if ($techlist[$arr['tech_id']]['techlist_build_end_time'] > time())
					{
						$fac = ($end_time-time())/($end_time-$start_time);
						dbquery("
						UPDATE 
							".$db_table['techlist']." 
						SET
							techlist_build_type='0',
							techlist_build_start_time='0',
							techlist_build_end_time='0'
						WHERE 
							techlist_tech_id='".$arr['tech_id']."'
							AND techlist_user_id='".$s['user']['id']."';");

						//Rohstoffe vom Planeten abziehen und aktualisieren
						$c->changeRes($bc['metal']*$fac,$bc['crystal']*$fac,$bc['plastic']*$fac,$bc['fuel']*$fac,$bc['food']*$fac);
						$b_status=0;
						$builing_something=false;
						
						//Log schreiben
						$log_text = "
						<b>Forschungs Abbruch</b><br><br>
						<b>User:</b> [USER_ID=".$s['user']['id'].";USER_NICK=".$s['user']['nick']."]<br>
						<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
						<b>Forschung:</b> ".$arr['tech_name']."<br>
						<b>Forschungs Level:</b> ".$b_level." (nach Abbruch)<br>
						<b>Start der Forschung:</b> ".date("Y-m-d H:i:s",$start_time)."<br>
						<b>Ende der Forschung:</b> ".date("Y-m-d H:i:s",$end_time)."<br><br>
						<b>Erhaltene Rohstoffe</b><br>
						<b>Faktor:</b> ".$fac."<br>
						<b>".RES_METAL.":</b> ".nf($bc['metal']*$fac)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($bc['crystal']*$fac)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($bc['plastic']*$fac)."<br>
						<b>".RES_FUEL.":</b> ".nf($bc['fuel']*$fac)."<br>
						<b>".RES_FOOD.":</b> ".nf($bc['food']*$fac)."<br><br>
						<b>Rohstoffe auf dem Planeten</b><br><br>
						<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
						<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
						<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
						";
						
						//Log Speichern
						add_log_game_research($log_text,$s['user']['id'],$s['user']['alliance_id'],$c->id,$arr['tech_id'],$b_status,time());								
					}
					else
					{
						echo "<i>Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!</i><br/><br/>";
					}
				}

				if ($b_status==1)
				{
					$color="color:#0f0;";
					$status_text="Wird erforscht";
				}
				else
				{
					$color="";
					$status_text="Unt&auml;tig";
				}

				//
				// Forschungsdaten anzeigen
				//
				infobox_start(text2html($arr['tech_name']." ".$b_level),1);
				echo "<tr><td width=\"220\" rowspan=\"3\" class=\"tbldata\"><a href=\"?page=help&amp;site=research&amp;id=".$arr['tech_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" border=\"0\" /></a></td>";
				echo "<td valign=\"top\" class=\"tbldata\" colspan=\"2\">".text2html($arr['tech_shortcomment'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" height=\"20\" width=\"50%\">Status:</td>";
				echo "<td id=\"buildstatus\" class=\"tbldata\" width=\"50%\" style=\"".$color."\">$status_text</td></tr>";
				echo "<tr><td class=\"tbltitle\" height=\"20\" width=\"50%\">Stufe:</td>";

				if ($b_level>0)
				{
					echo "<td id=\"buildlevel\" class=\"tbldata\" width=\"50%\">$b_level</td></tr>";
				}
				else
				{
					echo "<td id=\"buildlevel\" class=\"tbldata\" width=\"50%\">Noch nicht erforscht</td></tr>";
				}
				infobox_end(1);


				// Check requirements for this building
				$requirements_passed = true;
				$bid = $arr['tech_id'];
				if (count($b_req[$bid]['b'])>0)
				{
					foreach ($b_req[$bid]['b'] as $b => $l)
					{
						if ($buildlist[$b]<$l)
						{
							$requirements_passed = false;
						}
					}
				}								
				if (count($b_req[$bid]['t'])>0)
				{
					foreach ($b_req[$bid]['t'] as $id => $level)
					{
						if ($techlist[$id]['techlist_current_level']<$level)
						{
							$requirements_passed = false;
						}
					}
				}

				//
				// Baumenü
				//
				echo "<form action=\"?page=$page\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"id\" value=\"".$arr['tech_id']."\">";
                checker_init();
                
                
				if ($requirements_passed)
				{
				infobox_start("Forschoptionen",1);
				echo "<tr>
				<td class=\"tbltitle\" width=\"16%\">Aktion</td>
				<td class=\"tbltitle\" width=\"14%\">Zeit</th>
				<td class=\"tbltitle\" width=\"14%\">".RES_METAL."</td>
				<td class=\"tbltitle\" width=\"14%\">".RES_CRYSTAL."</td>
				<td class=\"tbltitle\" width=\"14%\">".RES_PLASTIC."</td>
				<td class=\"tbltitle\" width=\"14%\">".RES_FUEL."</td>
				<td class=\"tbltitle\" width=\"14%\">".RES_FOOD."</td></tr>";

				$notAvStyle=" style=\"color:red;\"";

				// Bauen
				if ($b_status==0)
				{
					// Wartezeiten auf Ressourcen berechnen
					if ($c->prod->metal>0) $bwait['metal']=ceil(($bc['metal']-$c->res->metal)/$c->prod->metal*3600);else $bwait['metal']=0;
					if ($c->prod->crystal>0) $bwait['crystal']=ceil(($bc['crystal']-$c->res->crystal)/$c->prod->crystal*3600);else $bwait['crystal']=0;
					if ($c->prod->plastic>0) $bwait['plastic']=ceil(($bc['plastic']-$c->res->plastic)/$c->prod->plastic*3600);else $bwait['plastic']=0;
					if ($c->prod->fuel>0) $bwait['fuel']=ceil(($bc['fuel']-$c->res->fuel)/$c->prod->fuel*3600);else $bwait['fuel']=0;
					if ($c->prod->food>0) $bwait['food']=ceil(($bc['food']-$c->res->food)/$c->prod->food*3600);else $bwait['food']=0;
					$bwmax=max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);

					// Baukosten-String
					$bcstring.="<td class=\"tbldata\"";
					if ($bc['metal']>$c->res->metal)
						$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff","<b>".nf($bc['metal']-$c->res->metal)."</b> ".RES_METAL."<br/>Bereit in <b>".tf($bwait['metal'])."</b>");
					$bcstring.= ">".nf($bc['metal'])."</td><td class=\"tbldata\"";
					if ($bc['crystal']>$c->res->crystal)
						$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['crystal']-$c->res->crystal)." ".RES_CRYSTAL."<br/>Bereit in <b>".tf($bwait['crystal'])."</b>");
					$bcstring.= ">".nf($bc['crystal'])."</td><td class=\"tbldata\"";
					if ($bc['plastic']>$c->res->plastic)
						$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['plastic']-$c->res->plastic)." ".RES_PLASTIC."<br/>Bereit in <b>".tf($bwait['plastic'])."</b>");
					$bcstring.= ">".nf($bc['plastic'])."</td><td class=\"tbldata\"";
					if ($bc['fuel']>$c->res->fuel)
						$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['fuel']-$c->res->fuel)." ".RES_FUEL."<br/>Bereit in <b>".tf($bwait['fuel'])."</b>");
					$bcstring.= ">".nf($bc['fuel'])."</td><td class=\"tbldata\"";
					if ($bc['food']>$c->res->food)
						$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['food']-$c->res->food)." ".RES_FOOD."<br/>Bereit in <b>".tf($bwait['food'])."</b>");
					$bcstring.= ">".nf($bc['food'])."</td></tr>";

					// Maximale Stufe erreicht
					if ($b_level>=$arr['tech_last_level'])
					{
						echo "<tr><td colspan=\"7\" class=\"tbldata\"><i>Keine Weiterentwicklung m&ouml;glich.</i></td></tr>";
					}
					// Es wird bereits geforscht
					elseif ($builing_something)
					{
						echo "<tr><td class=\"tbldata\" style=\"color:red;\">Erforschen</td><td class=\"tbldata\">".tf($btime)."</td>";
						echo $bcstring;
						//echo "<td class=\"tbldata\">".nf($bc['metal'])."</td><td class=\"tbldata\">".nf($bc['crystal'])."</td><td class=\"tbldata\">".nf($bc['plastic'])."</td><td class=\"tbldata\">".nf($bc['fuel'])."</td><td class=\"tbldata\">".nf($bc['food'])."</td></tr>";
						echo "<tr><td class=\"tbldata\" colspan=\"7\"><i>Es kann nichts erforscht werden da gerade an einer anderen Technik geforscht wird!</i></td></tr>";
					}
					// Zuwenig Rohstoffe vorhanden
					elseif ($c->res->metal<$bc['metal'] || $c->res->crystal<$bc['crystal']  || $c->res->plastic<$bc['plastic']  || $c->res->fuel<$bc['fuel']  || $c->res->food<$bc['food'])
					{
						echo "<tr><td class=\"tbldata\" style=\"color:red;\">Erforschen</td><td class=\"tbldata\">".tf($btime)."</td>";
						echo $bcstring;
						echo "<tr><td class=\"tbldata\" colspan=\"7\"><i>Keine Weiterentwicklung m&ouml;glich, zuwenig Rohstoffe!</i></td></tr>";
					}
					// Forschen
					elseif ($b_level==0)
					{
						echo "<tr><td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td class=\"tbldata\">".tf($btime)."</td>";
						echo "<td class=\"tbldata\">".nf($bc['metal'])."</td><td class=\"tbldata\">".nf($bc['crystal'])."</td><td class=\"tbldata\">".nf($bc['plastic'])."</td><td class=\"tbldata\">".nf($bc['fuel'])."</td><td class=\"tbldata\">".nf($bc['food'])."</td></tr>";
					}
					// Ausbauen
					else
					{
						echo "<tr><td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td class=\"tbldata\">".tf($btime)."</td>";
						echo "<td class=\"tbldata\">".nf($bc['metal'])."</td><td class=\"tbldata\">".nf($bc['crystal'])."</td><td class=\"tbldata\">".nf($bc['plastic'])."</td><td class=\"tbldata\">".nf($bc['fuel'])."</td><td class=\"tbldata\">".nf($bc['food'])."</td></tr>";
					}
				}


				// Bau abbrechen
				if ($b_status==1)
				{
					if ($planet_id==$c->id)
					{
              echo "<tr><td class=\"tbldata\"><input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cbuild\" value=\"Abbrechen\"  onclick=\"if (this.value=='Abbrechen'){return confirm('Wirklich abbrechen?');}\" />";
              echo "</td><td class=\"tbldata\" id=\"buildtime\">-</td><td colspan=\"5\" class=\"tbldata\">&nbsp;</td></tr>";
              if ($b_level<$arr['tech_last_level']-1)
	         		echo "<tr><td class=\"tbldata\" width=\"90\">N&auml;chste Stufe:</td><td class=\"tbldata\">".tf($btimen)."</td><td class=\"tbldata\">".nf($bcn['metal'])."</td><td class=\"tbldata\">".nf($bcn['crystal'])."</td><td class=\"tbldata\">".nf($bcn['plastic'])."</td><td class=\"tbldata\">".nf($bcn['fuel'])."</td><td class=\"tbldata\">".nf($bcn['food'])."</td></tr>";
	         }
					else
					{
						echo "<tr><td class=\"tbldata\" colspan=\"7\">Technologie wird auf einem anderen Planeten bereits erforscht!</td></tr>";					
					}
				}


				infobox_end(1);

				if ($bwmax>0)
					echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Forschen vorhanden sind: <b>".tf($bwmax)."</b><br/><br/>";



					if ($b_status==1 || $b_status==2)
					{
						?>
							<script type="text/javascript">
								function setCountdown()
								{
									var ts;
									cTime = <?PHP echo time();?>;
									b_level = <?PHP echo $b_level;?>;
									te = <?PHP if($end_time) echo $end_time; else echo 0;?>;
									tc = cTime + cnt;
									window.status = tc;
									ts = te - tc;

									if(b_level>0)
									{
										b_level=b_level+1;
									}
									else
									{
										b_level=1;
									}

									if (ts>=0)
									{
										t = Math.floor(ts / 3600 / 24);
										h = Math.floor(ts / 3600);
										m = Math.floor((ts-(h*3600))/60);
										s = Math.floor((ts-(h*3600)-(m*60)));
										nv = h+"h "+m+"m "+s+"s";
									}
									else
									{
										nv = "-";
										document.getElementById('buildstatus').firstChild.nodeValue="Fertig";
										document.getElementById('buildlevel').firstChild.nodeValue=b_level;
										document.getElementById("buildcancel").name = "submit_info";
							  			document.getElementById("buildcancel").value = "Aktualisieren";
									}
									document.getElementById('buildtime').firstChild.nodeValue=nv;
									cnt = cnt + 1;
									setTimeout("setCountdown()",1000);
								}
								if (document.getElementById('buildtime')!=null)
								{
									cnt = 0;
									setCountdown();
								}
							</script>
						<?PHP
					}
				}
				else
				{
					echo "Voraussetzungen noch nicht erfüllt!<br/><br/>";
				}

				echo "<input type=\"submit\" name=\"command_show\" value=\"Aktualisieren\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
				echo "</form>";
			}
			else
			{
				echo "<b>Fehler:</b> Technik nich vorhanden!<br/><br/><a href=\"?page=$page\">&Uuml;bersicht</a>";
			}
		}

		//
		// Übersicht anziegen
		//
		else
		{
			
    	infobox_start("Labor-Infos");
    	echo "<b>Eingestellte Arbeiter:</b> ".nf(PEOPLE_WORKING)."<br/>
    	<b>Bauzeitverringerung:</b> ";
    	if ($need_bonus_level>=0)
    	{
    		echo get_percent_string($time_boni_factor)." durch Stufe ".CURRENT_LAB_LEVEL." (-".((1-$conf['build_time_boni_forschungslabor']['p2'])*100)."% maximum)<br/>";
    	}
    	else
    	{
    		echo "Stufe ".$conf['build_time_boni_forschungslabor']['p1']." erforderlich!<br/>";
    	}
    	infobox_end();			
			
			
			// Load categories
			$tres = dbquery("
			SELECT
				*
			FROM
        ".$db_table['tech_types']."
			ORDER BY
				type_order ASC
			;");				
			if (mysql_num_rows($tres)>0)
			{			

				// Load technologies
				$bres = dbquery("
				SELECT
					tech_type_id,
					tech_id,
					tech_name,
					tech_last_level,
					tech_shortcomment,
					tech_costs_metal,
					tech_costs_crystal,
					tech_costs_plastic,
					tech_costs_fuel,
					tech_costs_food,
					tech_build_costs_factor,
					tech_show
				FROM
					".$db_table['technologies']."
				ORDER BY
					tech_order,
					tech_name
				;");	
				$tech = array();
				if (mysql_num_rows($bres)>0)			
				{
					while ($barr = mysql_fetch_Array($bres))
					{
						$tid = $barr['tech_type_id'];
						$bid = $barr['tech_id'];
						$tech[$tid][$bid]['name'] = $barr['tech_name'];
						$tech[$tid][$bid]['last_level'] = $barr['tech_last_level'];
						$tech[$tid][$bid]['shortcomment'] = $barr['tech_shortcomment'];
						$tech[$tid][$bid]['tech_costs_metal'] = $barr['tech_costs_metal'];
						$tech[$tid][$bid]['tech_costs_crystal'] = $barr['tech_costs_crystal'];
						$tech[$tid][$bid]['tech_costs_plastic'] = $barr['tech_costs_plastic'];
						$tech[$tid][$bid]['tech_costs_fuel'] = $barr['tech_costs_fuel'];
						$tech[$tid][$bid]['tech_costs_food'] = $barr['tech_costs_food'];
						$tech[$tid][$bid]['tech_build_costs_factor'] = $barr['tech_build_costs_factor'];
						$tech[$tid][$bid]['show'] = $barr['tech_show'];
					}
				}
			
				$cstr=checker_init();
				echo "<form action=\"?page=$page\" method=\"post\"><div>";
				echo $cstr;
				while ($tarr = mysql_fetch_array($tres))
				{
					infobox_start($tarr['type_name'],1,1);

					$cnt = 0; // Counter for current row
					$scnt = 0; // Counter for shown techs

					// Check if techs are avalaiable in this category
					$bdata = $tech[$tarr['type_id']];
					if (count($bdata)>0)
					{
						// Run through all techs in this cat
						foreach ($bdata as $bid => $bv)
						{						
							
							// Aktuellen Level feststellen
							$b_level=intval($techlist[$bid]['techlist_current_level']);
							$end_time=intval($techlist[$bid]['techlist_build_end_time']);
							
							// Check requirements for this tech
							$requirements_passed = true;
							if (count($b_req[$bid]['t'])>0)
							{
								foreach ($b_req[$bid]['t'] as $b=>$l)
								{
									if ($techlist[$b]['techlist_current_level']<$l)
										$requirements_passed = false;
								}
							}
              if (count($b_req[$bid]['b'])>0)
              {
              	foreach ($b_req[$bid]['b'] as $id=>$level)
              	{
              		if ($buildlist[$id]<$level)
              		    $requirements_passed = false;
              	}
              }

							if ($bv['show']==0 && $b_level>0)
							{
								$subtitle =  'Kann nicht erforscht werden';
								$tmtext = '<span style="color:#999">Es ist nicht vorgesehen dass diese Technologie erforscht werden kann!</span><br/>';
								$color = '#999';
								if($use_img_filter)
								{
									$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."&filter=na";
								}
								else
								{
									$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."";
								}		
							}
							elseif ($bv['show']==1)
							{
								// Voraussetzungen nicht erfüllt
								if (!$requirements_passed)
								{
									$subtitle =  'Voraussetzungen nicht erfüllt';
									$tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Technologie zu erforschen!</span><br/>';
									$color = '#999';
									if($use_img_filter)
									{
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."&filter=na";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."";
									}							
								}
								// Ist im Bau
								elseif ($techlist[$bid]['techlist_build_type']==1)
								{
									$subtitle =  "Forschung auf Stufe ".($b_level+1);
									$tmtext = "<span style=\"color:#0f0\">Wird ausgebaut!<br/>Dauer: ".tf($end_time-time())."</span><br/>";
									$color = '#0f0';
									if($use_img_filter)
									{
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."&filter=building";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."";
									}
								}
								// Untätig
								else
								{
              	  // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
              	  $bc['metal'] = $bv['tech_costs_metal'] * pow($bv['tech_build_costs_factor'],$b_level);
              	  $bc['crystal'] = $bv['tech_costs_crystal'] * pow($bv['tech_build_costs_factor'],$b_level);
              	  $bc['plastic'] = $bv['tech_costs_plastic'] * pow($bv['tech_build_costs_factor'],$b_level);
              	  $bc['fuel'] = $bv['tech_costs_fuel'] * pow($bv['tech_build_costs_factor'],$b_level);
              	  $bc['food'] = $bv['tech_costs_food'] * pow($bv['tech_build_costs_factor'],$b_level);
									
									// Zuwenig Ressourcen
									if($b_level<$bv['last_level'] && ($c->res->metal < $bc['metal'] || $c->res->crystal < $bc['crystal']  || $c->res->plastic < $bc['plastic']  || $c->res->fuel < $bc['fuel']  || $c->res->food < $bc['food']))
									{
										$tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r<br/>weitere Forschungen!</span><br/>";
										$color = '#f00';
										if($use_img_filter)
										{
											$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."&filter=lowres";
										}
										else
										{
											$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."";
										}
									}
									else
									{
										$tmtext = "";
										$color = '#fff';
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."";
									}
									
									if ($b_level==0)
									{
										$subtitle = "Noch nicht erforscht";
									}
									elseif ($b_level>=$bv['last_level'])
									{
										$subtitle = 'Vollständig erforscht';
									}
									else
									{
										$subtitle = 'Erforscht';
									}
								}
              }
              
							// Display all buildings that are buildable or are already built
							if (($requirements_passed && $bv['show']==1) || $b_level>0)
							{			
								// Display row starter if needed				
								if ($cnt==0) 
								{
									echo "<tr>";
								}

								echo "<td class=\"tbldata\" style=\"color:".$color.";text-align:center;width:".CELL_WIDTH."px\">
												<b>".$bv['name']."";
												if ($b_level>0) echo ' '.$b_level;
												echo "</b><br/>".$subtitle."<br/>
												<input name=\"show_".$bid."\" type=\"image\" value=\"".$bid."\" src=\"".$img."\" ".tm($bv['name'],$tmtext.$bv['shortcomment'])." style=\"width:120px;height:120px;\" />
								</td>\n";

								$cnt++;
								$scnt++;
							}
								
							// Display row finisher if needed			
							if ($cnt==NUM_BUILDINGS_PER_ROW)
							{
								echo "</tr>";
								$cnt = 0;
							}	
						}

						// Fill up missing cols and end row
						if ($cnt<NUM_BUILDINGS_PER_ROW && $cnt>0)
						{
							for ($x=0;$x < NUM_BUILDINGS_PER_ROW-$cnt;$x++)
							{
								echo "<td class=\"tbldata\" style=\"width:".CELL_WIDTH."px;\">&nbsp;</td>";
							}
							echo '</tr>';
						}							
						
						// Display message if no tech can be researched
						if ($scnt==0)
						{								
							echo "<tr>
											<td class=\"tbldata\" colspan=\"".NUM_BUILDINGS_PER_ROW."\" style=\"text-align:center;border:0;width:100%\">
												<i>In dieser Kategorie kann momentan noch nichts gebaut werden!</i>
											</td>
										</tr>";								
						}	

					}
					else
					{
						echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center;border:0;width:100%\"><i>In dieser Kategorie kann momentan noch nichts erforscht werden!</i></td></tr>";
					}
					infobox_end(1);
				}
				echo '</div></form>';				
			}
			else
			{
				echo "<i>Es k&ouml;nnen noch keine Forschungen erforscht werden!</i>";
			}
		}
		
		}
		else
		{
			echo "<h1>Forschungslabor des Planeten ".$c->name."</h1>";
			$c->resBox();
			echo "Das Forschungslabor wurde noch nicht gebaut!";
		}
	}
	// ENDE SKRIPT //

	?>
