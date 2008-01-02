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
	// 	Dateiname: haven_choose_action.php
	// 	Topic: Raumschiffhafen - Aktionsauswahl
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 09.06.2006
	// 	Kommentar:
	//

	define("INACTIVE_TIME",time()-(24*3600*$conf['user_inactive_days']['v']));

	$_SESSION['haven']['status']="chooseAction";
	if ($_SESSION['haven']['wormhole']>0 && count($_SESSION['haven']['target'])>0)
	{
		$wormhole=true;
		$tvar="target2";
		$fvar="fleet2";
	}
	else
	{
		$wormhole=false;
		$tvar="target";
		$fvar="fleet";
	}


	echo "<h2>Aktion und Start</h2>";

	if (count($_SESSION['haven']['ships'])>0)
	{
		// Zielkoordinaten speichern
		if ($_POST['submit_planetselection']!="")
		{
			$_SESSION['haven'][$tvar]['sx']=$_POST['fleet_sx'];
			$_SESSION['haven'][$tvar]['sy']=$_POST['fleet_sy'];
			$_SESSION['haven'][$tvar]['cx']=$_POST['fleet_cx'];
			$_SESSION['haven'][$tvar]['cy']=$_POST['fleet_cy'];
			$_SESSION['haven'][$tvar]['p']=$_POST['fleet_p'];
		}

		// Queries
		if ($_SESSION['haven'][$tvar]['p']==0)
		{
			$pres=dbquery("
			SELECT
			*
			FROM
			".$db_table['space_cells']."
			WHERE
			cell_sx='".$_SESSION['haven'][$tvar]['sx']."'
			AND cell_sy='".$_SESSION['haven'][$tvar]['sy']."'
			AND cell_cx='".$_SESSION['haven'][$tvar]['cx']."'
			AND cell_cy='".$_SESSION['haven'][$tvar]['cy']."'
			AND (cell_asteroid=1 OR cell_nebula=1 OR cell_wormhole_id>0)");
			//$acc="asteroid";
		}
		else
		{
			$pres = dbquery("
			SELECT
				*
			FROM
        ".$db_table['planets']." AS p,
        ".$db_table['space_cells']." AS c,
        ".$db_table['planet_types']." AS t
			WHERE
        t.type_id=p.planet_type_id
        AND p.planet_solsys_id=c.cell_id
        AND c.cell_sx='".$_SESSION['haven'][$tvar]['sx']."'
        AND c.cell_sy='".$_SESSION['haven'][$tvar]['sy']."'
        AND c.cell_cx='".$_SESSION['haven'][$tvar]['cx']."'
        AND c.cell_cy='".$_SESSION['haven'][$tvar]['cy']."'
        AND p.planet_solsys_pos='".$_SESSION['haven'][$tvar]['p']."';");
		}

		// Check if planet exists
		if (mysql_num_rows($pres)>0)
		{
			$parr = mysql_fetch_array($pres);

			echo "<form action=\"?page=$page\" method=\"post\">";

			// Geschwindigkeitsfaktor speichern
			if ($_POST['speed_percent']>=0.1)
				$_SESSION['haven'][$fvar]['speed_percent']=$_POST['speed_percent'];
			else
				$_SESSION['haven'][$fvar]['speed_percent']=1;

			// Kosten und Flugdauer berechnen

			if ($wormhole)
			{
				$warr = mysql_fetch_array(dbquery("SELECT cell_sx,cell_sy,cell_cx,cell_cy FROM ".$db_table['space_cells']." WHERE cell_id=".$_SESSION['haven']['wormhole'].";"));
				$sx1=$warr['cell_sx'];
				$sy1=$warr['cell_sy'];
				$cx1=$warr['cell_cx'];
				$cy1=$warr['cell_cy'];
      	$p1=0;
			}
			else
			{
				$sx1=$c->sx;
				$sy1=$c->sy;
				$cx1=$c->cx;
				$cy1=$c->cy;
				$p1=$c->solsys_pos;		// Position des aktuellen Planeten im Sonnensystem
			}
			$nx=$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
			$ny=$conf['num_of_cells']['p2'];		// Anzahl Zellen X
			$ae=$conf['cell_length']['v'];			// Länge vom Solsys in AE
			$np=$conf['num_planets']['p2'];			// Max. Planeten im Solsys
			$cae=$_SESSION['haven']['fleet']['costs_per_ae'];// Totale Kosten pro 100 AE

			$speed = $_SESSION['haven']['fleet']['min_speed'];		// Geschwindigkeit der Flotte
			$speed = ceil($speed * $_SESSION['haven'][$fvar]['speed_percent']);
			$cae = ceil($cae * $_SESSION['haven'][$fvar]['speed_percent']);

			$dx = abs(((($_SESSION['haven'][$tvar]['sx']-1) * $nx) + $_SESSION['haven'][$tvar]['cx']) - ((($sx1-1) * $nx) + $cx1));
			$dy = abs(((($_SESSION['haven'][$tvar]['sy']-1) * $nx) + $_SESSION['haven'][$tvar]['cy']) - ((($sy1-1) * $nx) + $cy1));
			$sd = sqrt(pow($dx,2)+pow($dy,2));			// Distanze zwischen den beiden Zellen
			$sae = $sd * $ae;											// Distance in AE units
			if ($sx1==$_SESSION['haven'][$tvar]['sx'] && $sy1==$_SESSION['haven'][$tvar]['sy'] && $cx1==$_SESSION['haven'][$tvar]['cx'] && $cy1==$_SESSION['haven'][$tvar]['cy'])
				$ps = abs($_SESSION['haven'][$tvar]['p']-$p1)*$ae/4/$np;				// Planetendistanz wenn sie im selben Solsys sind
			else
				$ps = ($ae/2) - (($_SESSION['haven'][$tvar]['p'])*$ae/4/$np);	// Planetendistanz wenn sie nicht im selben Solsys sind
			$ssae = $sae + $ps;
			if ($wormhole)
				$costs = ceil(($ssae * $cae / 100));
			else
				$costs = ceil(($ssae * $cae / 100) + $_SESSION['haven']['fleet']['costs_launch_land']);
			$timeforflight = $ssae / $speed;
			if ($wormhole)
				$timetotal =  ($timeforflight*3600);
			else
				$timetotal =  ($timeforflight*3600) + $_SESSION['haven']['fleet']['time_to_start'] + $_SESSION['haven']['fleet']['time_to_land'];

			$_SESSION['haven'][$fvar]['flight_costs'] = $costs;
			$_SESSION['haven'][$fvar]['flight_duration'] = $timetotal;
			$_SESSION['haven'][$fvar]['flight_distance'] = $ssae;
			$_SESSION['haven'][$fvar]['flight_food'] = ceil($_SESSION['haven']['fleet']['total_pilots'] * $conf['people_food_require']['v']/3600 * $_SESSION['haven'][$fvar]['flight_duration']);

			//Totale Flugkosten
			$_SESSION['haven']['fleettotal']['flight_costs'] = $_SESSION['haven']['fleet']['flight_costs']+$_SESSION['haven']['fleet2']['flight_costs'];
			$_SESSION['haven']['fleettotal']['flight_duration'] = $_SESSION['haven']['fleet']['flight_duration']+$_SESSION['haven']['fleet2']['flight_duration'];
			$_SESSION['haven']['fleettotal']['flight_distance'] = $_SESSION['haven']['fleet']['flight_distance']+$_SESSION['haven']['fleet2']['flight_distance'];
			$_SESSION['haven']['fleettotal']['flight_food'] = $_SESSION['haven']['fleet']['flight_food']+$_SESSION['haven']['fleet2']['flight_food'];



			// Es hat zuwenig Treibstoff für diese lange Strecke
			if ($_SESSION['haven']['fleet']['flight_costs']>$c->res->fuel)
			{
				$_SESSION['haven']['status']="choosePlanet";
				echo "Du hast zuwenig Treibstoff (".nf($c->res->fuel)."/".nf($_SESSION['haven']['fleet']['flight_costs']).") f&uuml;r diesen Flug!<br/><br/>";
				echo "<input type=\"button\" name=\"backtochosetarget\" onClick=\"document.location='?page=$page'\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\" />";
			}
			// Es hat zuwenig Nahrung für diese lange Strecke
			elseif ($_SESSION['haven']['fleet']['flight_food']>$c->res->food)
			{
				$_SESSION['haven']['status']="choosePlanet";
				echo "Du hast zuwenig Nahrung (".nf($c->res->food)."/".nf($_SESSION['haven']['fleet']['flight_food']).") f&uuml;r diesen Flug!<br/><br/>";
				echo "<input type=\"button\" name=\"backtochosetarget\" onClick=\"document.location='?page=$page'\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\" />";
			}
			// Die Flugkosten überschreiten die Kapazität
			elseif (($_SESSION['haven']['fleet']['flight_costs']+$_SESSION['haven']['fleettotal']['flight_food'])>$_SESSION['haven']['fleet']['total_capacity'])
			{
				$_SESSION['haven']['status']="choosePlanet";
				echo "Du hast zuwenig Kapazit&auml;t (".$_SESSION['haven']['fleet']['total_capacity'].") um gen&uuml;gend Treibstoff (".$_SESSION['haven']['fleet']['flight_costs'].") und Nahrung (".$_SESSION['haven']['fleettotal']['flight_food'].") f&uuml;r diesen Flug mitzunehmen!<br/><br/>";
				echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page'\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\">";
			}
			else
			{
				if ($parr['cell_wormhole_id']>0 && !$wormhole)
				{
					$whres = dbquery("SELECT techlist_current_level FROM ".$db_table['techlist']." WHERE techlist_tech_id=".TECH_WORMHOLE." AND techlist_user_id=".$s['user']['id'].";");
					$wharr = mysql_fetch_row($whres);
					if ($wharr[0]>0)
					{
						$_SESSION['haven']['wormhole']=$parr['cell_wormhole_id'];
						$_SESSION['haven']['status']="choosePlanet";
						echo "Deine Schiffe werden mit der gew&auml;hlten Geschwindigkeit zum Wurmloch fliegen. Beim Flug durch das Wurmloch vergeht keine Zeit und es wird kein Treibstoff ben&ouml;tigt. Jetzt muss noch das Ziel f&uuml;r den Flug nach dem Wurmloch gew&auml;hlt werden!<br/><br/>";
						echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page'\" value=\"Zur Zielauswahl &gt;&gt;&gt; \" title=\"Zur&uuml;ck zur Zielauswahl\">";
					}
					else
					{
						$_SESSION['haven']['status']="choosePlanet";
						echo "Du verf&uuml;gst noch nicht &uuml;ber die Technologie um Wurml&ouml;cher anzufliegen!<br/><br/>";
						echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page'\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\">";
					}
				}
				elseif ($parr['cell_wormhole_id']>0 && $wormhole)
				{
					$_SESSION['haven']['status']="choosePlanet";
					echo "Es k&ouml;nnen nicht zwei Wurml&ouml;cher hintereinander angeflogen werden!<br/><br/>";
					echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page'\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\">";
				}
				else
				{
          if ($_SESSION['haven']['fleet']['can_recycle'] && ($parr['planet_wf_metal']>0 || $parr['planet_wf_crystal']>0 || $parr['planet_wf_plastic']>0))
              $addrow=1;
          elseif ($_SESSION['haven']['fleet']['can_collect_gas']==1 && $parr['type_collect_gas']==1)
              $addrow=1;
          else
              $addrow=0;

          infobox_start("Aktion und Waren w&auml;hlen",1);

          echo "<tr><td class=\"tbltitle\" width=\"25%\">Startplanet:</td><td class=\"tbldata\" width=\"75%\">".$c->getString()."</td></tr>";
          if ($wormhole)
          {
              echo "<tr><td class=\"tbltitle\" width=\"25%\">Wurmloch-Eintrittspunkt:</td><td class=\"tbldata\" width=\"75%\">".$_SESSION['haven']['target']['sx']."/".$_SESSION['haven']['target']['sy']." : ".$_SESSION['haven']['target']['cx']."/".$_SESSION['haven']['target']['cy']."</td></tr>";
              echo "<tr><td class=\"tbltitle\" width=\"25%\">Wurmloch-Austrittspunkt:</td><td class=\"tbldata\" width=\"75%\">$sx1/$sy1 : $cx1/$cy1</td></tr>";
          }

          echo "<tr><td class=\"tbltitle\" width=\"40%\">Ziel:</td>";
          if ($parr['planet_name']!="")
              echo "<td class=\"tbldata\" width=\"60%\">".$parr['planet_name']." (".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos'].")</td></tr>";
          elseif ($parr['cell_nebula']==1)
              echo "<td class=\"tbldata\" width=\"60%\">Intergalaktischer Nebel (".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy'].")</td></tr>";
          elseif ($parr['cell_asteroid']==1)
              echo "<td class=\"tbldata\" width=\"60%\">Asteroidenfeld (".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy'].")</td></tr>";
          else
              echo "<td class=\"tbldata\" width=\"60%\">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']."</td></tr>";
          if ($parr['planet_id']>0)
          {
              echo "<tr><td class=\"tbltitle\" width=\"40%\">Besitzer:</td>";
              if ($parr['planet_user_id']!=0)
              {
                  if ($parr['planet_user_id']==$s['user']['id'])
                      echo "<td class=\"tbldata\" width=\"60%\"><i>Dieser Planet geh&ouml;rt dir</i></td></tr>";
                  else
                      echo "<td class=\"tbldata\" width=\"60%\">".get_user_nick($parr['planet_user_id'])."</td></tr>";
              }
              else
                  echo "<td class=\"tbldata\" width=\"60%\"><i>Unbewohnter Planet</i></td></tr>";
          }
          echo "<tr><td class=\"tbltitle\" width=\"40%\">Geschwindigkeit:</td><td class=\"tbldata\" width=\"60%\">".nf($speed)." AE/h</td></tr>";
          echo "<tr><td class=\"tbltitle\" width=\"40%\">Entfernung:</td><td class=\"tbldata\" width=\"60%\">".nf($_SESSION['haven']['fleettotal']['flight_distance'])." AE</td></tr>";
          echo "<tr><td class=\"tbltitle\" width=\"40%\">Flugdauer:</td><td class=\"tbldata\" width=\"60%\">".tf($_SESSION['haven']['fleettotal']['flight_duration'])."</td></tr>";
          echo "<tr><td class=\"tbltitle\" width=\"40%\">Treibstoff:</td><td class=\"tbldata\" width=\"60%\">".nf($_SESSION['haven']['fleettotal']['flight_costs'])." ".$rsc['fuel']."</td></tr>";
          echo "<tr><td class=\"tbltitle\" width=\"40%\">Nahrung:</td><td class=\"tbldata\" width=\"60%\">".nf($_SESSION['haven']['fleettotal']['flight_food'])." ".$rsc['food']."</td></tr>";

          //
          // Aktionen
          //
          $launchable=true;
          if ($_SESSION['haven']['fleet']['can_recycle']==1  && ($parr['planet_wf_metal']>0 || $parr['planet_wf_crystal']>0 || $parr['planet_wf_plastic']>0))
          	$wreckage=true;
          else
          	$wreckage=false;

          echo "<tr><td class=\"tbltitle\" width=\"40%\" valign=\"top\">Aktion:</td>";
          echo "<td class=\"tbldata\">";
          
          // Start & Ziel sind identisch
          if ($c->id==$parr['planet_id'])
          {
              if ($wreckage)
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"wo\" checked=\"checked\"> Tr&uuml;mmer einsammeln<br/>";
              elseif ($_SESSION['haven']['fleet']['can_recycle']==1  && ($parr['planet_wf_metal']==0 && $parr['planet_wf_crystal']==0 && $parr['planet_wf_plastic']==0))
              {
                  echo "<i>Keine Aktion m&ouml;glich! Es existiert kein Tr&uuml;mmerfeld!</i><br/>";
                  $launchable=false;
              }
              else
              {
                  echo  "<i>Keine Aktion m&ouml;glich! Dies ist der selbe Planet wie der Startplanet und deine Flotte kann auch nicht ein Tr&uuml;mmerfeld abbauen</i><br/>";
                  $launchable=false;
              }
          }
          // Eigener Planet
          elseif ($parr['planet_user_id']==$s['user']['id'])
          {
             echo "<input type=\"radio\" name=\"fleet_action\" value=\"to\" checked=\"checked\"> Waren transportieren<br/>";
             echo "<input type=\"radio\" name=\"fleet_action\" value=\"fo\"> Waren abholen<br/>";
             echo "<input type=\"radio\" name=\"fleet_action\" value=\"po\"> Flotte stationieren<br/>";
             if ($wreckage)
             {
               echo "<input type=\"radio\" name=\"fleet_action\" value=\"wo\" checked=\"checked\"> Tr&uuml;mmer einsammeln<br/>";
             }
          }
          // Planet eines anderen Spielers
          elseif ($parr['planet_user_id']>0)
          {
              $ures = dbquery("
              SELECT
                  u.user_nick,
                  u.user_hmode_from,
                  u.user_hmode_to,
                  u.user_points,
                  u.user_alliance_id,
                  u.user_blocked_from,
                  u.user_blocked_to,
                  u.user_last_online
              FROM
              	".$db_table['users']." AS u
              WHERE
              	u.user_id='".$parr['planet_user_id']."';");
              $uarr = mysql_fetch_array($ures);

              //Fragt ob Krieg zwischen den Allianzen herrscht
              $war=dbquery("
              SELECT
              	*
              FROM
              	".$db_table['alliance_bnd']."
              WHERE
                  ((alliance_bnd_alliance_id1='".$s['user']['alliance_id']."'
                      AND alliance_bnd_alliance_id2='".$uarr['user_alliance_id']."')
                  OR (alliance_bnd_alliance_id2='".$s['user']['alliance_id']."'
                      AND alliance_bnd_alliance_id1='".$uarr['user_alliance_id']."'))
                  AND alliance_bnd_level='3';");

              // Urlaub prüfen
              if ($uarr['user_hmode_from']!=0 && $uarr['user_hmode_to']!=0)
              {
              	if (!$wreckage)
              	{
                  echo "<i>Der Spieler <b>".$uarr['user_nick']."</b> ist im Urlaub und darum k&ouml;nnnen ihm keine Flotten geschickt werden!</i>";
                  $launchable=false;
              	}
              }
              // Anfängerschutz überprüfen (ausgeschlossen sind inaktive,gesperrte, oder kriegsgegner)
              elseif ( ($s['user']['points']*USER_ATTACK_PERCENTAGE<=$uarr['user_points'] && $s['user']['points']/USER_ATTACK_PERCENTAGE>=$uarr['user_points']) || $uarr['user_last_online']<INACTIVE_TIME || ($uarr['user_blocked_from']>0 && $uarr['user_blocked_from']<time() && $uarr['user_blocked_to']>time()) || mysql_num_rows($war)>0)
              {
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"so\" checked=\"checked\"> Ausspionieren<br/>";

                  if ($conf['battleban']['v']!=0 && $conf['battleban_time']['p1']<=time() && $conf['battleban_time']['p2']>time())
                  {
                  	echo "<div style=\"color:red;\" ".tm("Kampfsperre","<b>Von:</b> ".date("d.m.Y H:i",$conf['battleban_time']['p1'])."<br><b>Bis:</b> ".date("d.m.Y H:i",$conf['battleban_time']['p2'])."<br><b>Grund:</b> ".text2html($conf['battleban']['p1'])."").">Angriffssperre aktiv!</div>";
                  }
                  else
                  {
                      echo "<input type=\"radio\" name=\"fleet_action\" value=\"ao\"> Angreifen<br/>";
                      if ($_SESSION['haven']['fleet']['can_invade']==1 && $parr['planet_user_main']==0)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"io\"> &Uuml;bernehmen<br/>";
                      if ($_SESSION['haven']['fleet']['can_bomb']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"bo\"> Bombardieren<br/>";
                      if ($_SESSION['haven']['fleet']['can_antrax']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"xo\"> Giftgas<br/>";
                      if ($_SESSION['haven']['fleet']['can_tarn']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"vo\"> Tarnangriff<br/>";
                      if ($_SESSION['haven']['fleet']['can_fake']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"eo\"> Fakeangriff<br/>";
                      if ($_SESSION['haven']['fleet']['can_steal']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"lo\"> Spionageangriff<br/>";
                      if ($_SESSION['haven']['fleet']['can_tf']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"zo\"> Tr&uuml;mmerfeld erstellen<br/>";
                      if ($_SESSION['haven']['fleet']['can_deactivade']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"do\"> Deaktivierungsbombe<br/>";
                      if ($_SESSION['haven']['fleet']['can_antrax_food']==1)
                          echo "<input type=\"radio\" name=\"fleet_action\" value=\"ho\"> Antrax<br/>";
                  }
              }
              else
              {
              	echo "Anf&auml;ngerschutz aktiv! Die Punkte des Users m&uuml;ssen zwischen ".(USER_ATTACK_PERCENTAGE*100)."% und ".(100/USER_ATTACK_PERCENTAGE)."% von deinen Punkten liegen!<br/>Es k&ouml;nnen keine Flotten zu diesem Spieler gesendet werden";
              
              }
              
              if ($wreckage)
              {
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"wo\"> Tr&uuml;mmer einsammeln<br/>";
              }
          }
          // Unbewohnter Planet
          elseif ($parr['planet_user_id']==0)
          {
              // Asteroidenfeld
              if ($_SESSION['haven']['fleet']['can_asteroid']==1 && mysql_num_rows(dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_id=".$parr['cell_id']." AND cell_asteroid=1 AND cell_solsys_num_planets=0 AND  cell_nebula=0"))>0)
              {
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"yo\" checked=\"checked\"> Asteroiden sammeln<br/>";
              }
              elseif ($_SESSION['haven']['fleet']['can_collect_gas']==1 && mysql_num_rows(dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_id=".$parr['cell_id']." AND cell_nebula=1 AND cell_solsys_num_planets=0 AND cell_asteroid=0"))>0)
              {
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"no\" checked=\"checked\"> Nebel erkunden<br/>";
              }
              elseif ($_SESSION['haven']['fleet']['can_collect_gas']==1 && $parr['type_collect_gas']==1)
              {
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"go\" checked=\"checked\"> Gas saugen<br/>";
              }
              elseif ($wreckage)
              {
                  echo "<input type=\"radio\" name=\"fleet_action\" value=\"wo\" checked=\"checked\"> Tr&uuml;mmer einsammeln<br/>";
              }
              elseif ($parr['type_habitable']==0)
              {
                  echo "<i>Kann keine Kolonie errichten, da der Zielplanet unbewohnbar ist!</i><br/>";
                  $launchable=false;
              }
              elseif ($_SESSION['haven']['fleet']['can_colonialize']==1)
              {
                  if (mysql_num_rows(dbquery("SELECT planet_id FROM ".$db_table['planets']." WHERE planet_user_id='".$s['user']['id']."';"))<=USER_MAX_PLANETS)
                      echo "<input type=\"radio\" name=\"fleet_action\" value=\"ko\" checked=\"checked\"> Kolonie errichten<br/>";
                  else
                  {
                      echo "<i>Kann keine weitere Kolonie errichten, du hast bereits die Maximalanzahl von ".USER_MAX_PLANETS." Planeten!</i><br/>";
                      $launchable=false;
                  }
              }
              else
              {
                  echo "<i>Keine Aktion m&ouml;glich!</i><br/>";
                  $launchable=false;
              }
          }
          echo "</td></tr>";

          // Waren einladen
          if ($launchable)
          {

          	$_SESSION['haven']['fleet']['res_capacity'] = $_SESSION['haven']['fleet']['total_capacity'] - $_SESSION['haven']['fleettotal']['flight_costs'] - $_SESSION['haven']['fleettotal']['flight_food'];
            echo "<tr>
            				<td class=\"tbltitle\" width=\"40%\" rowspan=\"5\" valign=\"top\">
            					Waren mitnehmen:<br> (max. ".nf($_SESSION['haven']['fleet']['res_capacity'])." t)<br/><a href=\"javascript:;\" onclick=\"fulload(".strlen($_SESSION['haven']['fleet']['res_capacity']).");\">Alles einladen</a>
            				</td>
            				<td class=\"tbldata\" width=\"60%\">
            					<input type=\"text\" name=\"fleet_res_metal\" id=\"fleet_res_metal\" value=\"0\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value, ".$_SESSION['haven']['fleet']['res_capacity'].", '', '');\">&nbsp;t ".$rsc['metal']."
            				</td>
            			</tr>
            				<td class=\"tbldata\" width=\"60%\">
            					<input type=\"text\" name=\"fleet_res_crystal\" id=\"fleet_res_crystal\" value=\"0\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value, ".$_SESSION['haven']['fleet']['res_capacity'].", '', '');\">&nbsp;t ".$rsc['crystal']."
            				</td>
            			</tr>
            				<td class=\"tbldata\" width=\"60%\">
            					<input type=\"text\" name=\"fleet_res_plastic\" id=\"fleet_res_plastic\" value=\"0\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value, ".$_SESSION['haven']['fleet']['res_capacity'].", '', '');\">&nbsp;t ".$rsc['plastic']."
            				</td>
            			</tr>
            				<td class=\"tbldata\" width=\"60%\">
            					<input type=\"text\" name=\"fleet_res_fuel\" id=\"fleet_res_fuel\" value=\"0\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value, ".$_SESSION['haven']['fleet']['res_capacity'].", '', '');\">&nbsp;t ".$rsc['fuel']."
            				</td>
            			</tr>
            				<td class=\"tbldata\" width=\"60%\">
            					<input type=\"text\" name=\"fleet_res_food\" id=\"fleet_res_food\" value=\"0\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value, ".$_SESSION['haven']['fleet']['res_capacity'].", '', '');\">&nbsp;t ".$rsc['food']."
            				</td>
            			</tr>";
            if ($_SESSION['haven']['fleet']['people_capacity']>0)
            {
              echo "<tr>
                			<td class=\"tbltitle\" width=\"40%\" rowspan=\"1\" valign=\"top\">
                				Bewohner mitnehmen: (max. ".nf($_SESSION['haven']['fleet']['people_capacity']).")<br/>
                			</td>
                			<td class=\"tbldata\" width=\"60%\">
                				<input type=\"text\" name=\"fleet_res_people\" id=\"fleet_res_people\" value=\"0\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value, ".$_SESSION['haven']['fleet']['people_capacity'].", '', '');\">&nbsp; Bewohner
                			</td>
                		</tr>";
            }
          }
          infobox_end(1);
          echo "<input type=\"hidden\" name=\"flight_cell_to\" value=\"".$parr['cell_id']."\">";
          echo "<input type=\"submit\" name=\"reset\" value=\"Vorgang abbrechen\" title=\"Vorgang abbrechen\"/> &nbsp; ";
          echo "<input type=\"submit\" name=\"back\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\" /> &nbsp; ";
          if ($launchable)
              echo "<input type=\"submit\" name=\"submit_actionselection\" value=\"Start &gt;&gt;&gt;\" title=\"Klicke hier um zu starten\">&nbsp;";
          echo "</form><br/><br/>";

          infobox_start("Ausgew&auml;hlte Schiffe",1);
          foreach ($_SESSION['haven']['ship_names'] as $id=> $name)
          {
              echo "<tr><td class=\"tbldata\">$name</td><td class=\"tbldata\">".$_SESSION['haven']['ships'][$id]."</td></tr>";
          }
          infobox_end(1);


				}
			}
		}
		else
		{
			$_SESSION['haven']['status']="choosePlanet";
			echo "<b>Fehler:</b> Das Ziel ".$_POST['fleet_sx']."/".$_POST['fleet_sy']." : ".$_POST['fleet_cx']."/".$_POST['fleet_cy']." : ".$_POST['fleet_p']." existiert nicht!<br/><br/>";
			echo "<input type=\"button\" name=\"back\" onclick=\"document.location='?page=$page';\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Zielauswahl\" title=\"Zur&uuml;ck zur Zielauswahl\">";
		}
	}
	else
	{
		$_SESSION['haven']=Null;
		echo "<b>Fehler:</b> Es sind keine Schiffe ausgew&auml;hlt!<br/><br/>";
		echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck zur Flottenauswahl\" title=\"\">";

	}


?>
