<?PHP

	$t = time();	

	echo '<h1>Spezialisten</h1>';    
	$c->resBox();
	
	if (isset($_POST['submit_engage']) && isset($_POST['engage']))
	{
		if ($s['user']['specialist_time'] < $t)
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
				".$db_table['specialists']."
			WHERE		
				specialist_id='".$_POST['engage']."'
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);

				if ($c->res->metal >= $arr['specialist_costs_metal'] &&
				$c->res->crystal >= $arr['specialist_costs_crystal'] &&
				$c->res->plastic >= $arr['specialist_costs_plastic'] &&
				$c->res->fuel >= $arr['specialist_costs_fuel'] &&
				$c->res->food >= $arr['specialist_costs_food']
				)
				{		

					$st = $t + (86400 *$arr['specialist_days']);
					
					dbquery("
					UPDATE
						".$db_table['users']."
					SET
						user_specialist_id=".$arr['specialist_id'].",
						user_specialist_time=".$st."
					WHERE
						user_id=".$s['user']['id']."
					;");
					$s['user']['specialist_time'] = $st;
					$s['user']['specialist_id'] = $arr['specialist_id'];
					
					$c->changeRes(
					-$arr['specialist_costs_metal'],
					-$arr['specialist_costs_crystal'],
					-$arr['specialist_costs_plastic'],
					-$arr['specialist_costs_fuel'],
					-$arr['specialist_costs_food']);				
					
					echo 'Der gewählte Spezialist wurde eingestellt!<br/><br/>';
				}
				else
				{
					echo '<b>Fehler!</b> Zuwenig Rohstoffe vorhanden!<br/><br/>';
				}
			}
			else
			{
				echo '<b>Fehler!</b> Spezialist nicht gefunden!<br/><br/>';
			}
		}
		else
		{
			echo '<b>Fehler!</b> Es ist bereits ein Spezialist eingestellt.<br/> 
			Seine Anstellung dauert noch bis '.df($s['user']['specialist_time']).'.<br/> 
			Du musst warten bis seine Anstellung beendet ist!<br/><br/>';
		}		
	}
	
	
	$s_active = false;
	if ($s['user']['specialist_id']>0 && $s['user']['specialist_time'] > $t)
	{
		$s_active = true;
	}
	
	$res = dbquery("
	SELECT
		* 
	FROM
		".$db_table['specialists']."
	WHERE
		specialist_points_req<=".intval($s['user']['points'])."
	ORDER BY
		specialist_name
	;");
	if (mysql_num_rows($res)>0)
	{
		echo '<form action="?page='.$page.'" method="post"><table class="tb" style="">';
		if ($s_active)
		{
			echo '<tr><th colspan="4" style="text-align:center;">Übersicht</th></tr>';
		}
		else
		{
			echo '<tr><th colspan="5" style="text-align:center;">Übersicht</th></tr>';
		}
		while ($arr=mysql_fetch_array($res))
		{
			echo '<tr>';
			if ($s_active && $s['user']['specialist_id']==$arr['specialist_id'])
			{
				echo '<th style="width:140px;color:#0f0">'.$arr['specialist_name'].'<br/>
				<span style="font-size:8pt;font-weight:500;">Angestellt bis:<br/>
				'.df($s['user']['specialist_time']).'</span></th>';
				echo '<td style="color:#0f0;">'.$arr['specialist_desc'].'</td>';
			}
			else
			{
				echo '<th style="width:140px;">'.$arr['specialist_name'].'<br/>
				<span style="font-size:8pt;font-weight:500;">Ab '.nf($arr['specialist_points_req']).' Punkten<br/>
				Anstellbar für '.$arr['specialist_days'].' Tage</span></th>';
				echo '<td>'.$arr['specialist_desc'].'</td>';
			}
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
				if ($c->res->metal >= $arr['specialist_costs_metal'] &&
				$c->res->crystal >= $arr['specialist_costs_crystal'] &&
				$c->res->plastic >= $arr['specialist_costs_plastic'] &&
				$c->res->fuel >= $arr['specialist_costs_fuel'] &&
				$c->res->food >= $arr['specialist_costs_food']
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
		echo '</table><br/>';
		echo '<input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft" /> ';
		if (!$s_active)
		{
			echo '<input type="submit" name="submit_engage" value="Gewählten Spezialisten einstellen" /></form>';
		}
	}
	else
	{
		echo 'Du hast zuwenig Punkte ('.nf($s['user']['points']).' um einen Spezialisten anzustellen!<br/><br/>';
		echo '<input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft" /> ';
	}

?>