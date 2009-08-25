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
	// $Author$
	// $Date$
	// $Rev$
	//
	
	$t = time();	

	$uCnt = User::count();
	$totAvail = ceil($uCnt*SPECIALIST_AVAILABILITY_FACTOR);

	echo '<h1>Spezialisten</h1>';    
	$cp->resBox($cu->properties->smallResBox);
	
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
				specialist_costs_food,
				specialist_points_req
			FROM
				specialists
			WHERE		
				specialist_id='".$_POST['engage']."'
				AND specialist_enabled = 1
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);

				$tres = dbquery("
				SELECT 
					COUNT(user_id) 
				FROM 
					users 
				WHERE 
					user_specialist_time>".time()." 
					AND user_specialist_id=".$arr['specialist_id'].";");
				$tarr = mysql_fetch_row($tres);
				$used = min($tarr[0],$totAvail);
				$factor = 1 + (SPECIALIST_MAX_COSTS_FACTOR / $totAvail * $used);



				if ($cu->points >= $arr['specialist_points_req'])
				{
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
							user_id=".$cu->id."
						;");
						$cu->specialistId = $arr['specialist_id'];
						$cu->specialistTime = $st;
						
						$cp->changeRes(
						-$arr['specialist_costs_metal'],
						-$arr['specialist_costs_crystal'],
						-$arr['specialist_costs_plastic'],
						-$arr['specialist_costs_fuel'],
						-$arr['specialist_costs_food']);
						
						//Update every planet
						foreach ($planets as $pid)
							sendBackendMessage("planetupdate:".$pid);
						
						ok_msg('Der gewählte Spezialist wurde eingestellt!');
					}
					else
					{
						err_msg('Zuwenig Rohstoffe vorhanden!');
					}
				}
				else
				{
					err_msg('Zuwenig Punkte!');
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
		echo '<br/>';
		if ($cu->specialistId > 0 && $cu->specialistTime > $t)
		{
			dbquery("
			UPDATE
				users
			SET
				user_specialist_id=0,
				user_specialist_time=0
			WHERE
				user_id=".$cu->id."
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
			AND specialist_enabled = 1
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
		<td id="countDownElem">';
		if ($cu->specialistTime - $t > 0)
			echo tf($cu->specialistTime - $t);
		else
			echo 'Anstellung abgelaufen!';
		echo '</td>
		<td id="dischargeElem">';
		if ($cu->specialistTime - $t > 0)
			echo '<input type="submit" value="Entlassen" name="discharge" 
		onclick="return confirm(\'Willst du den Spezialisten wirklich entlassen? Es werden keine Ressourcen zurückerstattet, da der Spezialist diese als Abgangsentschädigung behält!\')" />';
		echo '</td>
		</tr>';
		tableEnd();		
		echo "</form>";
		if ($cu->specialistTime - $t > 0)
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
	WHERE
		specialist_enabled = 1		
	ORDER BY
		specialist_name
	");
	if (!$s_active)
	{
		echo "<form action=\"?page=".$page."\" method=\"post\">";
	}
	tableStart("Galaktisches Arbeitsamt ".helpLink('specialists')."");
	echo "<tr>
	<th>Name</th>
	<th>Benötigte Punkte</th>
	<th>Anstellbar für</th>
	<th>Verfügbar</th>
	<th>Kosten</th>";
	if (!$s_active)
	{		
		echo "<th>Auswahl</th>";
	}
	echo "</tr>";
	
	
	while ($arr=mysql_fetch_array($res))
	{
		$tres = dbquery("
		SELECT 
			COUNT(user_id) 
		FROM 
			users 
		WHERE 
			user_specialist_time>".time()." 
			AND user_specialist_id=".$arr['specialist_id'].";");
		$tarr = mysql_fetch_row($tres);
		$used = min($tarr[0],$totAvail);
		$avail = $totAvail - $used;
		if ($totAvail!=0)
			$factor = 1 + (SPECIALIST_MAX_COSTS_FACTOR / $totAvail * $used);
		else
			$factor = 1;

		echo '<tr>';
		echo '<th style="width:140px;">'.$arr['specialist_name'].'</th>';
		echo '<td>';
		echo nf($arr['specialist_points_req']);
		echo '</td>';
		echo '<td>';
		echo $arr['specialist_days'].' Tage';
		echo '</td>';
		echo '<td style="color:'.($avail>0?'#0f0':'#f90').'">';
		echo $avail." / ".$totAvail;
		echo '</td>';
		echo '<td style="width:150px;">';
		echo RES_ICON_METAL.nf($arr['specialist_costs_metal']*$factor).' '.RES_METAL.'<br style="clear:both;"/>';
		echo RES_ICON_CRYSTAL.nf($arr['specialist_costs_crystal']*$factor).' '.RES_CRYSTAL.'<br style="clear:both;"/>';
		echo RES_ICON_PLASTIC.nf($arr['specialist_costs_plastic']*$factor).' '.RES_PLASTIC.'<br style="clear:both;"/>';
		echo RES_ICON_FUEL.nf($arr['specialist_costs_fuel']*$factor).' '.RES_FUEL.'<br style="clear:both;"/>';
		echo RES_ICON_FOOD.nf($arr['specialist_costs_food']*$factor).' '.RES_FOOD.'<br style="clear:both;"/>';
		echo '</td>';
		if (!$s_active)
		{
			echo '<td>';
			if ($avail > 0)
			{
				if ($cp->resMetal >= $arr['specialist_costs_metal']*$factor &&
				$cp->resCrystal >= $arr['specialist_costs_crystal']*$factor &&
				$cp->resPlastic >= $arr['specialist_costs_plastic']*$factor &&
				$cp->resFuel >= $arr['specialist_costs_fuel']*$factor &&
				$cp->resFood >= $arr['specialist_costs_food']*$factor &&
				$cu->points >= $arr['specialist_points_req']
				)
				{					
					echo '<input type="radio" name="engage" value="'.$arr['specialist_id'].'" />';
				}
				else
				{
					echo 'Zuwenig Rohstoffe/Punkte';
				}				
			}
			else
			{
				echo "Zurzeit nicht verfügbar!";
			}			
			echo '</td>';			
		}
		echo '</tr>';
	}
	tableEnd();		
	
	
	if (!$s_active)
	{
		echo '<input type="submit" name="submit_engage" value="Gewählten Spezialisten einstellen" /></form>';
	}

	echo '<div><br/><input type="button" onclick="document.location=\'?page=economy\'" value="Wirtschaft des aktuellen Planeten anzeigen" /></div>';
	
	
?>