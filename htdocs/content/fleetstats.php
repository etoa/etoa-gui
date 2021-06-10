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
	* Shows information about all ships
	*
	* @author Lamborghini <lamborghini@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/


	echo '<h1>Schiffsübersicht</h1>';

	define('HELP_URL',"?page=help&site=shipyard");

	//Button "Zurück zum Raumschiffshafen"
	echo '<input type="button" onclick="document.location=\'?page=fleets\'" value="Flotten anzeigen" /> &nbsp; ';
	echo '<input type="button" onclick="document.location=\'?page=haven\'" value="Raumschiffshafen des aktuellen Planeten anzeigen" /><br/><br/>';

	//Prüft ob Schiffe vorhanden sind
  $res = dbquery("
  SELECT
  	COUNT(*)
  FROM
  	shiplist
  WHERE
  	shiplist_user_id='".$cu->id."';");
  if(mysql_result($res,0)>0)
  {
  	//
  	// Läd alle benötigten Daten
  	//

  	//Speichert Planetnamen in ein Array
  	$planet_data = array();
		foreach ($pm->itemObjects() as $p)
		{
			$planet_data[$p->id()]=$p->name();
		}


  	// Speichert alle Schiffe des Users, welche auf den Planeten stationiert sind
  	$shiplist_data = array();
    $shiplist_bunkered = [];
  	$res = dbquery("
		SELECT
			shiplist_ship_id,
			shiplist_count,
			shiplist_bunkered,
			shiplist_entity_id
		FROM
			shiplist
			INNER JOIN
			planets
			ON shiplist_entity_id=id
		WHERE
			 shiplist_user_id='".$cu->id."'
		ORDER BY
			planet_user_main DESC,
			planet_name ASC;");
  	while ($arr=mysql_fetch_array($res))
  	{
  		$shiplist_data[$arr['shiplist_ship_id']][$arr['shiplist_entity_id']] = $arr['shiplist_count'];
		$shiplist_bunkered[$arr['shiplist_ship_id']][$arr['shiplist_entity_id']] = $arr['shiplist_bunkered'];
  	}


  	// Speichert alle Schiffe des Users, die sich im Bau befinden
  	$queue_data = array();
  	$res = dbquery("
	  SELECT
	  	SUM(queue_cnt) AS cnt,
	  	queue_ship_id,
	  	queue_entity_id
	  FROM
	  	ship_queue
	  	INNER JOIN
			planets
			ON queue_entity_id=id
	  WHERE
	  	queue_user_id='".$cu->id."'
	  GROUP BY
	  	queue_entity_id,
	  	queue_ship_id
	  ORDER BY
			planet_user_main DESC,
			planet_name ASC;");
		if(mysql_num_rows($res)>0)
		{
		 	while ($arr=mysql_fetch_array($res))
		 	{
  			$queue_data[$arr['queue_ship_id']][$arr['queue_entity_id']] = $arr['cnt'];
		 	}
  	}


  	// Speichert alle Schiffe des Users, die sich im All befinden
  	$fleet_data = array();
  	$res = dbquery("
		SELECT
		  SUM(fs.fs_ship_cnt) AS cnt,
		  fs.fs_ship_id
		FROM
      fleet_ships AS fs
      INNER JOIN
      fleet AS f
      ON fs.fs_fleet_id=f.id
      AND f.user_id='".$cu->id."'
		GROUP BY
			fs.fs_ship_id;");
		if(mysql_num_rows($res)>0)
		{
		 	while ($arr=mysql_fetch_array($res))
		 	{
  			$fleet_data[$arr['fs_ship_id']] = $arr['cnt'];
		 	}
  	}


		tableStart("Schiffe");
		echo '<tr>
						<th colspan=\'2\'>Schiff</th>
						<th width=\'100\'>Im Orbit</th>
						<th width=\'100\'>Eingebunkert</th>
						<th width=\'100\'>Im Bau</th>
						<th width=\'100\'>Im All</th>
					</tr>';

		//Listet alle Schiffe auf, die allgemein gebaut werden können (auch die, die der User nach dem Technikbaum noch nicht bauen könnte oder nicht seiner Rasse entsprechen)
	  $sres = dbquery("
	  SELECT
	  	ship_id,
	    ship_name,
	    special_ship
	  FROM
	  	ships
	  ORDER BY
	  	special_ship DESC,
	  	ship_name;");
	  if(mysql_num_rows($sres)>0)
	  {
	  	while ($sarr=mysql_fetch_array($sres))
	  	{
			 	//Zeigt Informationen (Zeile) an wenn Schiffe vorhanden sind
			  if(
				(isset($shiplist_data[$sarr['ship_id']]) && array_sum($shiplist_data[$sarr['ship_id']])>0)
				|| (isset($queue_data[$sarr['ship_id']]) && array_sum($queue_data[$sarr['ship_id']])>0)
				|| (isset($fleet_data[$sarr['ship_id']]) && $fleet_data[$sarr['ship_id']]>0)
				|| (isset($shiplist_bunkered[$sarr['ship_id']]) && array_sum($shiplist_bunkered[$sarr['ship_id']])>0))
			  {
			  	$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_small.".IMAGE_EXT;
			  	echo '<tr>
			  					<td style="background:#000" style="width:40px;height:40px;">';

			  					if($sarr['special_ship']==1)
			  					{
			  						echo '<a href="?page=ship_upgrade&amp;id='.$sarr['ship_id'].'" title="Zum Upgrademenu"><img src="'.$s_img.'" style="width:40px;height:40px;"/></a>';
			  					}
			  					else
			  					{
			  						echo '<a href="'.HELP_URL.'" title="Info zu diesem Schiff anzeigen"><img src="'.$s_img.'" style="width:40px;height:40px;"/></a>';
			  					}
			  		echo '</td>
			  					<td>
			  						'.$sarr['ship_name'].'
			  					</td>';
			  				//Spalte gebauter Schiffe
		  					if(isset($shiplist_data[$sarr['ship_id']]))
		  					{
		  						// Summiert die Anzahl Schiffe von allen Planeten
		  						$total = array_sum($shiplist_data[$sarr['ship_id']]);

		  						// Listet die Anzahl Schiffe von jedem einzelen Planeten auf
		  						$tm = "";
		  						foreach ($shiplist_data[$sarr['ship_id']] as $planet_id => $count)
							  	{
							  		$tm .= "<b>".$planet_data[$planet_id]."</b>: ".nf($count)."<br>";
							  	}

		  						echo '
			  					<td '.tm("Anzahl",$tm).'>
			  						'.nf($total).'
			  					</td>';
			  				}
			  				else
			  				{
			  					echo '
			  					<td>
			  						&nbsp;
			  					</td>';
			  				}

			  				//Spalte eingebunkerter Schiffe
		  					if(isset($shiplist_bunkered[$sarr['ship_id']]))
		  					{
		  						// Summiert die Anzahl Schiffe von allen Planeten
		  						$total = array_sum($shiplist_bunkered[$sarr['ship_id']]);

		  						// Listet die Anzahl Schiffe von jedem einzelen Planeten auf
		  						$tm = "";
		  						foreach ($shiplist_bunkered[$sarr['ship_id']] as $planet_id => $count)
							  	{
									if ($count)
							  			$tm .= "<b>".$planet_data[$planet_id]."</b>: ".nf($count)."<br>";
							  	}
		  						if ($tm!="")
								{
									echo '
				  					<td '.tm("Anzahl",$tm).'>
				  						'.nf($total).'
				  					</td>';
								}
								else
									echo '
									<td>
										&nbsp;
									</td>';
			  				}
			  				else
			  				{
			  					echo '
			  					<td>
			  						&nbsp;
			  					</td>';
			  				}

			  				//Spalte bauender Schiffe
		  					if(isset($queue_data[$sarr['ship_id']]))
		  					{
		  						// Summiert die Anzahl Schiffe von allen Planeten
		  						$total = array_sum($queue_data[$sarr['ship_id']]);

		  						// Listet die Anzahl Schiffe von jedem einzelen Planeten auf
		  						$tm = "";
		  						foreach ($queue_data[$sarr['ship_id']] as $planet_id => $count)
							  	{
							  		$tm .= "<b>".$planet_data[$planet_id]."</b>: ".nf($count)."<br>";
							  	}

		  						echo '
			  					<td '.tm("Anzahl",$tm).'>
			  						'.nf($total).'
			  					</td>';
			  				}
			  				else
			  				{
			  					echo '
			  					<td>
			  						&nbsp;
			  					</td>';
			  				}


			  				//Spalte fliegender Schiffe
		  					if(isset($fleet_data[$sarr['ship_id']]))
		  					{
		  						// Summiert die Anzahl Schiffe von allen Planeten
		  						$total = $fleet_data[$sarr['ship_id']];
		  						echo '
			  					<td>
			  						'.nf($total).'
			  					</td>';
			  				}
			  				else
			  				{
			  					echo '
			  					<td>
			  						&nbsp;
			  					</td>';
			  				}
			  	echo '</tr>';
			  }
	  	}
	  }

		tableEnd();

		//Arrays löschen (Speicher freigeben)
		mysql_free_result($res);
		mysql_free_result($sres);
    unset($arr);
    unset($sarr);
    unset($planet_data);
    unset($shiplist_data);
    unset($queue_data);
    unset($fleet_data);
	}
	else
	{
		error_msg("Es sind noch keine Schiffe vorhanden!");
	}
?>
