<?PHP
	echo "<h2>Spezialisten</h2>";
	Help::navi(array("Spezialisten","specialists"));
	
	iBoxStart("Info");
	echo "Spezialisten können für eine fixe Zeitdauer angestellt werden und verstärken während dieser Zeit
	dein Imperium in eine bestimmte Richtung. Sie können erst ab einer bestimmten Punktzahl eingestellt werden. Von jedem Typ
	ist nur eine gewisse Anzahl verfügbar. Ebenfalls steigt der Preis mit steigender Nachfrage, nach einem bestimmten Spezialisten. Die
	Einstellung geschieht per sofort, und nach Ablauf der Anstellung verlässt der Spezialist dein Imperium wieder. 
	Es kann immer nur ein Spezialist gleichzeitig angestellt werden. Man kann aber einen Spezialisten vorzeitig entlassen,
	um Platz für einen neuen zu schaffen; man erhält in diesem Fall aber keine Ressourcen zurück.";	
	iBoxEnd();
	
	
		$res = dbquery("
	SELECT
		*
	FROM
		specialists
	WHERE
		specialist_enabled = 1
	ORDER BY
		specialist_name
	");
	tableStart("Verfügbare Spezialisten",'95%');
	echo "<tr>
	<th>Name</th>
	<th>Beschreibung</th>
	<th>Effekt</th>
	<th>Grundpreis</th>";
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
			$bonus.= get_percent_string($arr['specialist_prod_food'],1).' '.RES_FOOD.'sproduktion<br/>';
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
			$bonus.= '<span style="color:#0f0;">+'.$arr['specialist_fleet_max'].'</span> zusätzliche Flotten<br/>';
		if ($arr['specialist_def_repair']!=1)
			$bonus.= get_percent_string($arr['specialist_def_repair'],1).' Verteidigungswiederherstellung<br/>';
		if ($arr['specialist_spy_level']!=0)
			$bonus.= '<span style="color:#0f0;">+'.$arr['specialist_spy_level'].'</span> zusätzliche Spionagelevel<br/>';
		if ($arr['specialist_tarn_level']!=0)
			$bonus.= '<span style="color:#0f0;">+'.$arr['specialist_tarn_level'].'</span> zusätzliche Tarnlevel<br/>';
		if ($arr['specialist_trade_time']!=1)
			$bonus.= get_percent_string($arr['specialist_trade_time'],1).' Handelsflottengeschwindigkeit<br/>';
		if ($arr['specialist_trade_bonus']!=1)
			$bonus.= get_percent_string($arr['specialist_trade_bonus'],1,1).' Handelskosten<br/>';
		
		echo $bonus;
		echo '</td>';
		echo '<td style="width:120px;">';
		echo nf($arr['specialist_costs_metal']).' '.RES_METAL.'<br/>';
		echo nf($arr['specialist_costs_crystal']).' '.RES_CRYSTAL.'<br/>';
		echo nf($arr['specialist_costs_plastic']).' '.RES_PLASTIC.'<br/>';
		echo nf($arr['specialist_costs_fuel']).' '.RES_FUEL.'<br/>';
		echo nf($arr['specialist_costs_food']).' '.RES_FOOD.'<br/>';
		echo '</td>';
		echo '</tr>';
	}
	tableEnd();		
?>