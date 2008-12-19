<?PHP

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

	HelpUtil::breadCrumbs(array("Rohstoffkurse","rates"));
	
	echo "Die Rohstoffkurse sind dynamisch und ändern sich automatisch je nach dem<br/>
	 wie gross das Angebot und die Nachfrage
	nach einem Rohstoff im Markt ist.<br/><br/>";
	echo "<table class=\"tb\">";
	echo "<tr>
		<th style=\"width:15%\"></th>
		<th style=\"width:17%\">".RES_METAL."</th>
		<th style=\"width:17%\">".RES_CRYSTAL."</th>
		<th style=\"width:17%\">".RES_PLASTIC."</th>
		<th style=\"width:17%\">".RES_FUEL."</th>
		<th style=\"width:17%\">".RES_FOOD."</th>
	</tr>";
	echo "<tr>
		<th>".RES_METAL."</th>
		<td>-</td>
		<td".HelpUtil::colorizeMarketRate($m_c).">".$m_c."</td>
		<td".HelpUtil::colorizeMarketRate($m_p).">".$m_p."</td>
		<td".HelpUtil::colorizeMarketRate($m_fu).">".$m_fu."</td>
		<td".HelpUtil::colorizeMarketRate($m_fo).">".$m_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_CRYSTAL."</th>
		<td".HelpUtil::colorizeMarketRate($c_m).">".$c_m."</td>
		<td>-</td>
		<td".HelpUtil::colorizeMarketRate($c_p).">".$c_p."</td>
		<td".HelpUtil::colorizeMarketRate($c_fu).">".$c_fu."</td>
		<td".HelpUtil::colorizeMarketRate($c_fo).">".$c_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_PLASTIC."</th>
		<td".HelpUtil::colorizeMarketRate($p_m).">".$p_m."</td>
		<td".HelpUtil::colorizeMarketRate($p_c).">".$p_c."</td>
		<td>-</td>
		<td".HelpUtil::colorizeMarketRate($p_fu).">".$p_fu."</td>
		<td".HelpUtil::colorizeMarketRate($p_fo).">".$p_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_FUEL."</th>
		<td".HelpUtil::colorizeMarketRate($fu_m).">".$fu_m."</td>
		<td".HelpUtil::colorizeMarketRate($fu_c).">".$fu_c."</td>
		<td".HelpUtil::colorizeMarketRate($fu_p).">".$fu_p."</td>
		<td>-</td>
		<td".HelpUtil::colorizeMarketRate($fu_fo).">".$fu_fo."</td>
	</tr>";
	echo "<tr>
		<th>".RES_FOOD."</th>
		<td".HelpUtil::colorizeMarketRate($fo_m).">".$fo_m."</td>
		<td".HelpUtil::colorizeMarketRate($fo_c).">".$fo_c."</td>
		<td".HelpUtil::colorizeMarketRate($fo_p).">".$fo_p."</td>
		<td".HelpUtil::colorizeMarketRate($fo_fu).">".$fo_fu."</td>
		<td>-</td>
	</tr>";
	echo "</table><br/>";
	echo "<b>Beispiel:</b> Eine Tonne ".RES_FOOD." hat den Wert von ".$fo_m." Tonnen ".RES_METAL.".<br/>
	Für eine Tonne ".RES_METAL." muss man ".$m_fo." Tonnen ".RES_FOOD." aufwenden.<br/><br/>";
	echo "<b>Legende:</b><br/><br/> kleiner Bedarf/grosses Angebot
		<span style=\"background:#0f0;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<span style=\"background:#ff0;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<span style=\"background:#fa0;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<span style=\"background:#f70;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<span style=\"background:#f40;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		grosser Bedarf/kleines Angebot
		<br/><br/>";
	
	
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
