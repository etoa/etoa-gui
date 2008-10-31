<?PHP

	$t = time();	

	echo '<h1>Spezialisten</h1>';    
	$cp->resBox();
	
	//
	// Engage specialist
	//
	if (isset($_POST['submit_engage']) && isset($_POST['engage']))
	{
		echo "<br/>";
		if ($cu->specialistTime < $t)
		{
			$res = dbquery("
			SELECT
				specialist_id,
				specialist_days,
				specialist_costs_metal,
				specialist_costs_crystal,
				specialist_costs_plastic,
				specialist_costs_fuel,
				specialist_costs_food
			FROM
				specialists
			WHERE		
				specialist_id='".$_POST['engage']."'
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);

				if ($cp->resMetal >= $arr['specialist_costs_metal'] &&
				$cp->resCrystal >= $arr['specialist_costs_crystal'] &&
				$cp->resPlastic >= $arr['specialist_costs_plastic'] &&
				$cp->resFuel >= $arr['specialist_costs_fuel'] &&
				$cp->resFood >= $arr['specialist_costs_food']
				)
				{
					$st = $t + (86400 *$arr['specialist_days']);
					
					dbquery("
					UPDATE
						users
					SET
						user_specialist_id=".$arr['specialist_id'].",
						user_specialist_time=".$st."
					WHERE
						user_id=".$cu->id()."
					;");
					$cu->specialistId = $arr['specialist_id'];
					$cu->specialistTime = $st;
					
					$cp->changeRes(
					-$arr['specialist_costs_metal'],
					-$arr['specialist_costs_crystal'],
					-$arr['specialist_costs_plastic'],
					-$arr['specialist_costs_fuel'],
					-$arr['specialist_costs_food']);				
					
					ok_msg('Der gewählte Spezialist wurde eingestellt!');
				}
				else
				{
					err_msg('Zuwenig Rohstoffe vorhanden!');
				}
			}
			else
			{
				err_msg('Spezialist nicht gefunden!');
			}
		}
		else
		{
			err_msg('Es ist bereits ein Spezialist eingestellt.
			Seine Anstellung dauert noch bis '.df($cu->specialistTime).'.
			Du musst warten bis seine Anstellung beendet ist!');
		}		
	}
	
	//
	// Discharge specialist
	//
	if (isset($_POST['discharge']))
	{
		echo "<br/>";
		if ($cu->specialistId > 0 && $cu->specialistTime > $t)
		{
			dbquery("
			UPDATE
				users
			SET
				user_specialist_id=0,
				user_specialist_time=0
			WHERE
				user_id=".$cu->id()."
			;");
			$cu->specialistId = 0;
			$cu->specialistTime = 0;
			
			ok_msg('Der Spezialist wurde entlassen!');
		}
		else
		{
			err_msg('Du kannst niemanden entlassen, da kein Spezialist angestellt ist!');
		}		
	}	
	
	//
	// Show current engaged specialist
	//
	$s_active = false;
	if ($cu->specialistId > 0 && $cu->specialistTime > $t)
	{
		$s_active = true;
		
		$res = dbquery("
		SELECT
			*
		FROM
			specialists
		WHERE
			specialist_id=".$cu->specialistId."		
		");	
		$arr = mysql_fetch_assoc($res);
		echo "<form action=\"?page=".$page."\" method=\"post\">";		
		tableStart("Momentan eingestellter Spezialist");
		echo '<tr>
		<th>Funktion</th>
		<th>Angestellt bis</th>
		<th>Verbleibende Zeit</th>
		<th>Aktionen</th>
		</tr>';
		echo '<tr>
		<td>'.$arr['specialist_name'].'</td>
		<td>'.df($cu->specialistTime).'</td>
		<td id="countDownElem">'.tf($cu->specialistTime - $t).'</td>
		<td id="dischargeElem"><input type="submit" value="Entlassen" name="discharge" 
		onclick="return confirm(\'Willst du den Spezialisten wirklich entlassen? Es werden keine Ressourcen zurückerstattet, da der Spezialist diese als Abgangsentschädigung behält!\')" /></td>
		</tr>';
		tableEnd();		
		echo "</form>";
		countDown("countDownElem",$cu->specialistTime,"dischargeElem");
	}
	
	
	//
	// Show all specialists
	//
	$res = dbquery("
	SELECT
		*
	FROM
		specialists
	ORDER BY
		specialist_name
	");
	if (!$s_active)
	{
		echo "<form action=\"?page=".$page."\" method=\"post\">";
	}
	tableStart("Galaktisches Arbeitsamt ".helpLink('specialists')."",'95%');
	echo "<tr>
	<th>Name</th>
	<th>Beschreibung</th>
	<th>Effekt</th>
	<th>Kosten</th>";
	if (!$s_active)
	{		
		echo "<th>Auswahl</th>";
	}
	echo "</tr>";
	
	while ($arr=mysql_fetch_array($res))
	{
		echo '<tr>';
		echo '<th style="width:140px;">'.$arr['specialist_name'].'<br/>
		<span style="font-size:8pt;font-weight:500;">Ab '.nf($arr['specialist_points_req']).' Punkten<br/>
		Anstellbar für '.$arr['specialist_days'].' Tage</span></th>';
		echo '<td>'.$arr['specialist_desc'].'</td>';
		echo '<td style="width:220px;">';
		$bonus='';
		if ($arr['specialist_prod_metal']!=1)
			$bonus.= get_percent_string($arr['specialist_prod_metal'],1).' '.RES_METAL.'produktion<br/>';
		if ($arr['specialist_prod_crystal']!=1)
			$bonus.= get_percent_string($arr['specialist_prod_crystal'],1).' '.RES_CRYSTAL.'produktion<br/>';
		if ($arr['specialist_prod_plastic']!=1)
			$bonus.= get_percent_string($arr['specialist_prod_plastic'],1).' '.RES_PLASTIC.'produktion<br/>';
		if ($arr['specialist_prod_fuel']!=1)
			$bonus.= get_percent_string($arr['specialist_prod_fuel'],1).' '.RES_FUEL.'produktion<br/>';
		if ($arr['specialist_prod_food']!=1)
			$bonus.= get_percent_string($arr['specialist_prod_food'],1).' '.RES_FOOD.'produktion<br/>';
		if ($arr['specialist_power']!=1)
			$bonus.= get_percent_string($arr['specialist_power'],1).' Stromerzeugung<br/>';
		if ($arr['specialist_population']!=1)
			$bonus.= get_percent_string($arr['specialist_population'],1).' Bevölkerungswachstum<br/>';
		if ($arr['specialist_time_tech']!=1)
			$bonus.= get_percent_string($arr['specialist_time_tech'],1,1).' Forschungszeit<br/>';
		if ($arr['specialist_time_buildings']!=1)
			$bonus.= get_percent_string($arr['specialist_time_buildings'],1,1).' Gebäudebauzeit<br/>';
		if ($arr['specialist_time_defense']!=1)
			$bonus.= get_percent_string($arr['specialist_time_defense'],1,1).' Verteidigungsbauzeit<br/>';
		if ($arr['specialist_time_ships']!=1)
			$bonus.= get_percent_string($arr['specialist_time_ships'],1,1).' Schiffbauzeit<br/>';
		if ($arr['specialist_costs_buildings']!=1)
			$bonus.= get_percent_string($arr['specialist_costs_buildings'],1,1).' Gebäudekosten<br/>';
		if ($arr['specialist_costs_defense']!=1)
			$bonus.= get_percent_string($arr['specialist_costs_defense'],1,1).' Verteidigungskosten<br/>';
		if ($arr['specialist_costs_ships']!=1)
			$bonus.= get_percent_string($arr['specialist_costs_ships'],1,1).' Schiffbaukosten<br/>';
		if ($arr['specialist_costs_tech']!=1)
			$bonus.= get_percent_string($arr['specialist_costs_tech'],1,1).' Forschungskosten<br/>';
		if ($arr['specialist_fleet_speed']!=1)
			$bonus.= get_percent_string($arr['specialist_fleet_speed'],1).' Flottengeschwindigkeit<br/>';
		if ($arr['specialist_fleet_max']!=0)
			$bonus.= '+'.$arr['specialist_fleet_max'].' zusätzliche Flotten<br/>';
		if ($arr['specialist_def_repair']!=1)
			$bonus.= get_percent_string($arr['specialist_def_repair'],1).' Verteidigungswiederherstellung<br/>';
		if ($arr['specialist_spy_level']!=0)
			$bonus.= '+'.$arr['specialist_spy_level'].' zusätzlicher Spionagelevel<br/>';
		if ($arr['specialist_tarn_level']!=0)
			$bonus.= '+'.$arr['specialist_tarn_level'].' zusätzlicher Tarnlevel<br/>';
		if ($arr['specialist_trade_time']!=1)
			$bonus.= get_percent_string($arr['specialist_trade_time'],1,1).' Handelsgeschwindigkeit<br/>';
		if ($arr['specialist_trade_bonus']!=1)
			$bonus.= get_percent_string($arr['specialist_trade_bonus'],1).' Handelsbonus<br/>';
		
		echo $bonus;
		echo '</td>';
		echo '<td style="width:120px;">';
		echo nf($arr['specialist_costs_metal']).' '.RES_METAL.'<br/>';
		echo nf($arr['specialist_costs_crystal']).' '.RES_CRYSTAL.'<br/>';
		echo nf($arr['specialist_costs_plastic']).' '.RES_PLASTIC.'<br/>';
		echo nf($arr['specialist_costs_fuel']).' '.RES_FUEL.'<br/>';
		echo nf($arr['specialist_costs_food']).' '.RES_FOOD.'<br/>';
		echo '</td>';
		if (!$s_active)
		{
			echo '<td>';
			if ($cp->resMetal >= $arr['specialist_costs_metal'] &&
			$cp->resCrystal >= $arr['specialist_costs_crystal'] &&
			$cp->resPlastic >= $arr['specialist_costs_plastic'] &&
			$cp->resFuel >= $arr['specialist_costs_fuel'] &&
			$cp->resFood >= $arr['specialist_costs_food']
			)
			{					
				echo '<input type="radio" name="engage" value="'.$arr['specialist_id'].'" />';
			}
			else
			{
				echo 'Zuwenig Rohstoffe';
			}				
			echo '</td>';			
		}
		echo '</tr>';
	}
	tableEnd();		
	
	echo '<div><input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft des aktuellen Planeten anzeigen" /> &nbsp; ';
	
	if (!$s_active)
	{
		echo '<input type="submit" name="submit_engage" value="Gewählten Spezialisten einstellen" /></form>';
	}
	echo "</div>";
	
	
?>