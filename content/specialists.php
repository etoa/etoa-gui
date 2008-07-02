<?PHP

	$t = time();	

	echo '<h1>Spezialisten</h1>';    
	$cp->resBox();
	
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
						".$db_table['users']."
					SET
						user_specialist_id=".$arr['specialist_id'].",
						user_specialist_time=".$st."
					WHERE
						user_id=".$cu->id()."
					;");
					$s['user']['specialist_time'] = $st;
					$s['user']['specialist_id'] = $arr['specialist_id'];
					
					$cp->changeRes(
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
	
	infobox_start("Spezialisten",1);
	show_tab_menu("category",array("admiral"=>"Admiral",
											"engineer"=>"Ingenieur",
 											"geology"=>"Geologe",
 											"technocracy"=>"Technokrat",
 											"biologist"=>"Biologe",
 											"spy"=>"Spion",
 											"merchant"=>"Meisterhändler"));

	echo "<tr>
			<td class=\"tbldata\">
				<div id=\"specialisten\"  style=\"max-height:500px; overflow:auto;\">
					<table>"; 
					?>
                    
					<tr>
						<td class="tbldata"><a href="?page=help&site=shipyard&amp;id=2"><img src="images/imagepacks/Discovery/ships/ship2_small.gif" width="40" height="40" border="0" /></a></td><td class="tbltitle" width="30%">
	  			      	<span style="font-weight:500">ANTARES Jäger<br/> Gebaut:</span> 0
	  			      	</td>
	  			    	<td class="tbldata" width="13%">0h 0m 20s</td>
	  			    	<td class="tbldata" width="10%" >750</td>
	  			    	<td class="tbldata" width="10%" >575</td>
   						<td class="tbldata" width="10%" >420</td>
						<td class="tbldata" width="10%" >50</td>
						<td class="tbldata" width="10%" >0</td>
                        <td class="tbldata"><input type="text" value="0" id="build_count_2" name="build_count[2]" size="5" maxlength="9" onmouseover="stm(['','Es k&ouml;nnen maximal 910`456 Schiffe gebaut werden.'],stl)" onmouseout="htm()" tabindex="3" onkeyup="FormatNumber(this.id,this.value, 910456, '', '');"/><br><a href="javascript:;" onclick="document.getElementById('build_count_2').value=910456;">max</a></td>
                        </tr>
                                            
                   <?PHP 
	echo "			</table>
				</div>      							
			</td>
		</tr>";
								
	infobox_end(1);
	
/*	while ($arr=mysql_fetch_array($res))
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
		echo '</table><br/>';
		echo '<input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft" /> ';
		
		
		if (!$s_active)
		{
			echo '<input type="submit" name="submit_engage" value="Gewählten Spezialisten einstellen" /></form>';
		}*/
?>