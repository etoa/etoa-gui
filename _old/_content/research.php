<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: buildings.php													//
	// Topic: Bauhof-Modul				 									//
	// Version: 0.1																	//
	// Letzte Änderung: 10.05.2006 Lamborghini			//
	//////////////////////////////////////////////////

    // DEFINITIONEN //

  define(NUM_BUILDINGS_PER_ROW,4);
  define(TECH_BUILDING_ID,8);
  define(CELL_WIDTH,175);
	define("GEN_TECH_ID",23);				// ID der Gentechnologie

	// SKRIPT //
	if ($planets->current)
	{

		echo "<h1>Forschungslabor des Planeten ".$c->name."</h1>";
		$c->resBox();

		// Forschungsliste laden
		$blres = dbquery("
		SELECT 
			* 
		FROM 
			".$db_table['techlist']." 
		WHERE 
			techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
		$builing_something=false;
		while ($blarr = mysql_fetch_array($blres))
		{
			$techlist[$blarr['techlist_tech_id']]=$blarr;
			if ($blarr['techlist_build_type']!=0) $builing_something=true;
		}

		//Gentech level laden
		$tlres = dbquery("
		SELECT
			techlist_current_level
		FROM
			".$db_table['techlist']."
		WHERE
            techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
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

		//Forschung erforschen/abbrechen
		if (($_POST['submit_info']!="" || $_POST['command_build']!="" || $_POST['command_cbuild']!="") && $_POST['id']!="" && checker_verify())
		{
			// Forschungsdaten laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['technologies']." 
			WHERE  
				tech_id='".$_POST['id']."'
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

        // Forschungslabor level laden
        $lab_res = dbquery("
        SELECT
        	buildlist_current_level
        FROM
        	".$db_table['buildlist']."
        WHERE
            buildlist_planet_id='".$c->id."'
            AND buildlist_building_id='8'
            AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
        $lab_arr = mysql_fetch_array($lab_res);
        if(mysql_num_rows($lab_res)>0)
				{
	        //level zählen welches das forschungslabor über dem angegeben level ist und faktor berechnen
	        $need_bonus_level = $lab_arr['buildlist_current_level'] - $conf['build_time_boni_forschungslabor']['p1'];
	        if($need_bonus_level<=0)
	        {
	            $time_boni_factor=1;
	        }else{
	            $time_boni_factor=1-($need_bonus_level*($conf['build_time_boni_forschungslabor']['v']/100));
	        }
				}
				else
				{
					 $time_boni_factor=1;
				}


				// Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
				$bc['metal'] = $arr['tech_costs_metal'] * pow($arr['tech_build_costs_factor'],$b_level);
				$bc['crystal'] = $arr['tech_costs_crystal'] * pow($arr['tech_build_costs_factor'],$b_level);
				$bc['plastic'] = $arr['tech_costs_plastic'] * pow($arr['tech_build_costs_factor'],$b_level);
				$bc['fuel'] = $arr['tech_costs_fuel'] * pow($arr['tech_build_costs_factor'],$b_level);
				$bc['food'] = $arr['tech_costs_food'] * pow($arr['tech_build_costs_factor'],$b_level);

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
            $btime=$btime-$farr['buildlist_people_working']*3;
            if ($btime<$btime_min) $btime=$btime_min;
        }
        $bc['food']+=$farr['buildlist_people_working']*12;

				$start_time = $techlist[$arr['tech_id']]['techlist_build_start_time'];
				$end_time = $techlist[$arr['tech_id']]['techlist_build_end_time'];

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
										AND techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
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
										'".$_SESSION[ROUNDID]['user']['id']."'
									);");

								}
								
								//Rohstoffe vom Planeten abziehen und aktualisieren
								$c->changeRes(-$bc['metal'],-$bc['crystal'],-$bc['plastic'],-$bc['fuel'],-$bc['food']);
								$b_status=1;
								
								//Log schreiben
								$log_text = "
								<b>Forschung Ausbau</b><br><br>
								<b>User:</b> [USER_ID=".$_SESSION[ROUNDID]['user']['id'].";USER_NICK=".$_SESSION[ROUNDID]['user']['nick']."]<br>
								<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
								<b>Technologie:</b> ".$arr['tech_name']."<br>
								<b>Technologie Level:</b> ".$b_level." (vor Ausbau)<br>
								<b>Erforschungsdauer:</b> ".tf($btime)."<br>
								<b>Ende:</b> ".date("Y-m-d H:i:s",$end_time)."<br>
								<b>Eingesetzte Bewohner:</b> ".nf($buildlist[BUILD_BUILDING_ID]['buildlist_people_working'])."<br>
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
								add_log_game_research($log_text,$_SESSION[ROUNDID]['user']['id'],$_SESSION[ROUNDID]['user']['alliance_id'],$c->id,$arr['tech_id'],$b_status,time());								
								
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
							AND techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");

						//Rohstoffe vom Planeten abziehen und aktualisieren
						$c->changeRes($bc['metal']*$fac,$bc['crystal']*$fac,$bc['plastic']*$fac,$bc['fuel']*$fac,$bc['food']*$fac);
						$b_status=0;
						$builing_something=false;
						
						//Log schreiben
						$log_text = "
						<b>Forschungs Abbruch</b><br><br>
						<b>User:</b> [USER_ID=".$_SESSION[ROUNDID]['user']['id'].";USER_NICK=".$_SESSION[ROUNDID]['user']['nick']."]<br>
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
						add_log_game_research($log_text,$_SESSION[ROUNDID]['user']['id'],$_SESSION[ROUNDID]['user']['alliance_id'],$c->id,$arr['tech_id'],$b_status,time());								
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
				infobox_start(text2html($arr['tech_name']),1);
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

				//
				// Baumenü
				//
				echo "<form action=\"?page=$page\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"id\" value=\"".$arr['tech_id']."\">";
                checker_init();
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
						// TODO: Colorize this..
						//echo "<td class=\"tbldata\">".nf($bc['metal'])."</td><td class=\"tbldata\">".nf($bc['crystal'])."</td><td class=\"tbldata\">".nf($bc['plastic'])."</td><td class=\"tbldata\">".nf($bc['fuel'])."</td><td class=\"tbldata\">".nf($bc['food'])."</td></tr>";
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
              echo "<tr><td class=\"tbldata\"><input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cbuild\" value=\"Abbrechen\">";
              echo "</td><td class=\"tbldata\" id=\"buildtime\">-</td><td colspan=\"5\" class=\"tbldata\">&nbsp;</td></tr>";
              if ($b_level<$arr['tech_last_level']-1)
	         		echo "<tr><td class=\"tbldata\" width=\"90\">N&auml;chste Stufe:</td><td class=\"tbldata\">".tf($btimen)."</td><td class=\"tbldata\">".nf($bcn['metal'])."</td><td class=\"tbldata\">".nf($bcn['crystal'])."</td><td class=\"tbldata\">".nf($bcn['plastic'])."</td><td class=\"tbldata\">".nf($bcn['fuel'])."</td><td class=\"tbldata\">".nf($bcn['food'])."</td></tr>";
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


				echo "<input type=\"submit\" name=\"submit_info\" value=\"Aktualisieren\" /> &nbsp; ";
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
			$tres = dbquery("SELECT * FROM ".$db_table['buildlist']." WHERE buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND buildlist_planet_id='".$c->id."';");
			while ($tarr = mysql_fetch_array($tres))
			{
				$buildlist[$tarr['buildlist_building_id']]=$tarr['buildlist_current_level'];
			}
			$rres = dbquery("SELECT * FROM ".$db_table['tech_requirements'].";");
			while ($rarr = mysql_fetch_array($rres))
			{
				if ($rarr['req_req_building_id']>0) $b_req[$rarr['req_tech_id']]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
				if ($rarr['req_req_tech_id']>0) $b_req[$rarr['req_tech_id']]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
			}

			$tres = dbquery("
			SELECT
				tt.*,
				t.tech_id,
				t.tech_name,
				t.tech_type_id,
				t.tech_costs_metal,
				t.tech_costs_crystal,
				t.tech_costs_plastic,
				t.tech_costs_fuel,
				t.tech_costs_food,
				t.tech_build_costs_factor,
				t.tech_last_level
			FROM
	      ".$db_table['tech_types']." AS tt,
	      ".$db_table['technologies']." AS t
			WHERE
        t.tech_type_id=tt.type_id
        AND t.tech_show=1
			GROUP BY
				t.tech_id
			ORDER BY
				tt.type_order ASC,
				t.tech_order ASC,
				t.tech_name ASC;");
			if (mysql_num_rows($tres)>0)
			{
				$types = array();
				$obj = array();
				while ($tarr=mysql_fetch_array($tres))
				{
					$types[$tarr['type_id']]=$tarr['type_name'];
					$obj[$tarr['type_id']][$tarr['tech_id']]=$tarr;
				}
				$cstr=checker_init();
				foreach ($types as $tid => $tname)
				{
					infobox_start($tname,1,1);

						echo "<colgroup>";
						for ($x=0;$x<NUM_BUILDINGS_PER_ROW;$x++)
							echo "<col width=\"120\">";
						echo "</colgroup>";

						$data = array();
						foreach ($obj[$tid] as $arr)
						{
							$show_this_building = 1;
							if (count($b_req[$arr['tech_id']]['t'])>0)
							{
								foreach ($b_req[$arr['tech_id']]['t'] as $b=>$l)
								{
									if ($techlist[$b]['techlist_current_level']<$l)
										$show_this_building = 0;
								}
							}
              if (count($b_req[$arr['tech_id']]['b'])>0)
              {
              	foreach ($b_req[$arr['tech_id']]['b'] as $id=>$level)
              	{
              		if ($buildlist[$id]<$level)
              		    $show_this_building = 0;
              	}
              }
             	if ($show_this_building==1)
             	    array_push($data,array("arr"=>$arr,"buildable"=>true));
							//else
             	//    array_push($data,array("arr"=>$arr,"buildable"=>false));
						}
						$cnt = 0;
						$tcnt = 1;
						$rcnt = 1;

						if (count($data)>0)
						{
							foreach ($data as $techdata)
							{
								$arr = $techdata['arr'];
								$buildable = $techdata['buildable'];

								$b_level=intval($techlist[$arr['tech_id']]['techlist_current_level']);

                // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
                $bc['metal'] = $arr['tech_costs_metal'] * pow($arr['tech_build_costs_factor'],$b_level);
                $bc['crystal'] = $arr['tech_costs_crystal'] * pow($arr['tech_build_costs_factor'],$b_level);
                $bc['plastic'] = $arr['tech_costs_plastic'] * pow($arr['tech_build_costs_factor'],$b_level);
                $bc['fuel'] = $arr['tech_costs_fuel'] * pow($arr['tech_build_costs_factor'],$b_level);
                $bc['food'] = $arr['tech_costs_food'] * pow($arr['tech_build_costs_factor'],$b_level);

								$class="tbldata";
								if (!$buildable)
								{
									if($_SESSION[ROUNDID]['user']['image_filter']==1)
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."&filter=notavailable";
									else
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."";
								}
								// Zuwenig Ressourcen markieren
								elseif($c->res->metal<$bc['metal'] || $c->res->crystal<$bc['crystal']  || $c->res->plastic<$bc['plastic']  || $c->res->fuel<$bc['fuel']  || $c->res->food<$bc['food'])
								{
									$class="tbldata2";
									if($_SESSION[ROUNDID]['user']['image_filter']==1)
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."&filter=lowres";
									else
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."";
								}
								// Standard-Ansicht
								else
								{
									$class="tbldata";
									$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."";
								}

								echo "<form action=\"?page=$page\" method=\"post\">";
								if ($cnt==0){ echo "<tr>"; }
								echo "<td class=\"".$class."\" style=\"text-align:center;width:".CELL_WIDTH."px\"><b>".text2html($arr['tech_name'])."</b><br/>";

								if ($techlist[$arr['tech_id']]['techlist_build_type']==1)
								{
									echo "<span style=\"color:#0f0;\">Stufe ".($techlist[$arr['tech_id']]['techlist_current_level']+1)." wird erforscht</span><br/>";

									//Image-Filter An/Aus
									if($_SESSION[ROUNDID]['user']['image_filter']==1)
									{
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."&filter=building";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_middle.".IMAGE_EXT."";
									}

								}
								elseif (!$buildable)
								{
									echo "Nicht erforschbar";
									if ($techlist[$arr['tech_id']]['techlist_current_level']>0)
										echo " (Stufe ".$techlist[$arr['tech_id']]['techlist_current_level'].")";
									echo "<br/>";
								}
								elseif ($techlist[$arr['tech_id']]['techlist_current_level']==0)
								{
									echo "Noch nicht erforscht<br/>";
								}
								else
								{
									echo "Stufe ".$techlist[$arr['tech_id']]['techlist_current_level']."<br/>";
								}
								echo "<input type=\"hidden\" name=\"action\" value=\"info\">";
								echo "<input type=\"hidden\" name=\"id\" value=\"".$arr['tech_id']."\">";
								echo $cstr;
						   		echo "<button name=\"submit_info\" type=\"submit\" value=\"submit_info\" title=\"Klicken um Details und Bauoptionen anzuzeigen\">";
								echo "<img src=\"".$img."\" width=\"120\" height=\"120\" alt=\"".text2html($arr['tech_name'])."\" border=\"0\">";
								echo "</button>";
								echo "</td>";
								echo "</form>";

								if (count($data)==$tcnt)
								{
									for ($x=0;$x<(NUM_BUILDINGS_PER_ROW*$rcnt)-count($data);$x++)
									{
										echo "<td class=\"tbldata\" style=\"width:".CELL_WIDTH."px\">&nbsp;</td>";
									}
								}
								if ($cnt==NUM_BUILDINGS_PER_ROW-1)
								{
									echo "</tr>";
									$cnt = -1;
									$rcnt++;
								}
								$cnt++;
								$tcnt++;
							}

						}
						else
						{
							echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center;border:0;width:100%\"><i>In dieser Kategorie kann momentan noch nichts erforscht werden!</i></td></tr>";
						}

					infobox_end(1);

				}
			}
			else
			{
				echo "<i>Es k&ouml;nnen noch keine Forschungen erforscht werden!</i>";
			}

		}

	}
	// ENDE SKRIPT //

	?>
