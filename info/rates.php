<?PHP

	function clrCell($r)
	{
		$b = " style=\"color:#000;background:";
		
		$e = "\"";
		if ($r<0.5)
			return $b."#0f0".$e;		           
	  if ($r<1)
			return $b."#ff0".$e;		           
	  if ($r>5)
			return $b."#f40".$e;		           
	  if ($r>2.5)
			return $b."#f70".$e;		           
	  if ($r>1)
			return $b."#fa0".$e;		           
	}

	$m_c = round($conf['market_metal_factor']['v']/$conf['market_crystal_factor']['v'],2);
	$m_p = round($conf['market_metal_factor']['v']/$conf['market_plastic_factor']['v'],2);
	$m_fu = round($conf['market_metal_factor']['v']/$conf['market_fuel_factor']['v'],2);
	$m_fo = round($conf['market_metal_factor']['v']/$conf['market_food_factor']['v'],2);

	$c_m = round($conf['market_crystal_factor']['v']/$conf['market_metal_factor']['v'],2);
	$c_p = round($conf['market_crystal_factor']['v']/$conf['market_plastic_factor']['v'],2);
	$c_fu = round($conf['market_crystal_factor']['v']/$conf['market_fuel_factor']['v'],2);
	$c_fo = round($conf['market_crystal_factor']['v']/$conf['market_food_factor']['v'],2);

	$p_m = round($conf['market_plastic_factor']['v']/$conf['market_metal_factor']['v'],2);
	$p_c = round($conf['market_plastic_factor']['v']/$conf['market_crystal_factor']['v'],2);
	$p_fu = round($conf['market_plastic_factor']['v']/$conf['market_fuel_factor']['v'],2);
	$p_fo = round($conf['market_plastic_factor']['v']/$conf['market_food_factor']['v'],2);

	$fu_m = round($conf['market_fuel_factor']['v']/$conf['market_metal_factor']['v'],2);
	$fu_c = round($conf['market_fuel_factor']['v']/$conf['market_crystal_factor']['v'],2);
	$fu_p = round($conf['market_fuel_factor']['v']/$conf['market_plastic_factor']['v'],2);
	$fu_fo = round($conf['market_fuel_factor']['v']/$conf['market_food_factor']['v'],2);

	$fo_m = round($conf['market_food_factor']['v']/$conf['market_metal_factor']['v'],2);
	$fo_c = round($conf['market_food_factor']['v']/$conf['market_crystal_factor']['v'],2);
	$fo_p = round($conf['market_food_factor']['v']/$conf['market_plastic_factor']['v'],2);
	$fo_fu = round($conf['market_food_factor']['v']/$conf['market_fuel_factor']['v'],2);
	
	echo "<h2>Rohstoffkurse</h2>";

	helpNavi(array("Rohstoffkurse","rates"));
	
	echo "Die Rohstoffkurse sind dynamisch und ändern sich automatisch je nach dem<br/>
	 wie gross das Angebot und die Nachfrage
	nach einem Rohstoff im Markt ist.<br/><br/>";
	echo "<table class=\"tb\">";
	echo "<tr>
		<th style=\"border-left:#000 solid 1px;border-top:#000 solid 1px;background:none;width:15%\"></th>
		<th style=\"width:17%\">".RES_METAL."</th>
		<th style=\"width:17%\">".RES_CRYSTAL."</th>
		<th style=\"width:17%\">".RES_PLASTIC."</th>
		<th style=\"width:17%\">".RES_FUEL."</th>
		<th style=\"width:17%\">".RES_FOOD."</th>
	</tr>";
	echo "<tr>
		<th>".RES_METAL."</th>
		<td>-</td>
		<td".clrCell($m_c).">".$m_c."</td>
		<td".clrCell($m_p).">".$m_p."</td>
		<td".clrCell($m_fu).">".$m_fu."</td>
		<td".clrCell($m_fo).">".$m_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_CRYSTAL."</th>
		<td".clrCell($c_m).">".$c_m."</td>
		<td>-</td>
		<td".clrCell($c_p).">".$c_p."</td>
		<td".clrCell($c_fu).">".$c_fu."</td>
		<td".clrCell($c_fo).">".$c_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_PLASTIC."</th>
		<td".clrCell($p_m).">".$p_m."</td>
		<td".clrCell($p_c).">".$p_c."</td>
		<td>-</td>
		<td".clrCell($p_fu).">".$p_fu."</td>
		<td".clrCell($p_fo).">".$p_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_FUEL."</th>
		<td".clrCell($fu_m).">".$fu_m."</td>
		<td".clrCell($fu_c).">".$fu_c."</td>
		<td".clrCell($fu_p).">".$fu_p."</td>
		<td>-</td>
		<td".clrCell($fu_fo).">".$fu_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_FOOD."</th>
		<td".clrCell($fo_m).">".$fo_m."</td>
		<td".clrCell($fo_c).">".$fo_c."</td>
		<td".clrCell($fo_p).">".$fo_p."</td>
		<td".clrCell($fo_fu).">".$fo_fu."</td>
		<td>-</td>
	</tr>";
	echo "</table><br/>";
	echo "<b>Beispiel:</b> Eine Tonne ".RES_FOOD." hat den Wert von ".$fo_m." Tonnen ".RES_METAL.".<br/>
	Für eine Tonne ".RES_METAL." muss man ".$m_fo." Tonne ".RES_FOOD." aufwenden.<br/><br/>";

	echo "<table class=\"tb\">";
	echo "<tr>
		<th>Rohstoff</th>
		<th>Absoluter Kurs</th>
	</tr>";
	echo "<tr>
		<th>".RES_METAL."</th>
		<td>".$conf['market_metal_factor']['v']."</td>
	</tr>";
	echo "<tr>
		<th>".RES_CRYSTAL."</th>
		<td>".$conf['market_crystal_factor']['v']."</td>
	</tr>";
	echo "<tr>
		<th>".RES_PLASTIC."</th>
		<td>".$conf['market_plastic_factor']['v']."</td>
	</tr>";
	echo "<tr>
		<th>".RES_FUEL."</th>
		<td>".$conf['market_fuel_factor']['v']."</td>
	</tr>";
	echo "<tr>
		<th>".RES_FOOD."</th>
		<td>".$conf['market_food_factor']['v']."</td>
	</tr>";
	echo "</table><br/><br/>";



?>
