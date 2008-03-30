<?PHP
	/**
	* Fleet-Action: Spy
	*/
	
	echo "djghlsdkghdijfbgjdsbfbdfbbds";

	$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);

	// Lädt Spiotechlevel des Angreiffers
	$a_spy_level = 0;
	$a_tarn_level = 0;
	$spy_level_res = dbquery("
	SELECT 
		techlist_current_level,
		techlist_tech_id		
	FROM 
		".$db_table['techlist']." 
	WHERE 
		techlist_user_id='".$arr['fleet_user_id']."'
		AND (techlist_tech_id='".SPY_TECH_ID."'
		OR techlist_tech_id='".TARN_TECH_ID."');");
	if (mysql_num_rows($spy_level_res)>0)
	{
		while ($spy_level_arr=mysql_fetch_row($spy_level_res))
		{
			if ($spy_level_arr[1] == SPY_TECH_ID)
			{
				$a_spy_level = $spy_level_arr[0];
			}
			elseif($spy_level_arr[1] == TARN_TECH_ID)
			{
				$a_tarn_level = $spy_level_arr[0];
			}
		}
	}
	
	// Lädt Spiotechlevel des Verteidigers
	$d_spy_level = 0;
	$d_tarn_level = 0;
	$spy_level_res = dbquery("
	SELECT 
		techlist_current_level,
		techlist_tech_id 
	FROM 
		".$db_table['techlist']." 
	WHERE 
		techlist_user_id='".$user_to_id."'
		AND (techlist_tech_id='".SPY_TECH_ID."'
		OR techlist_tech_id='".TARN_TECH_ID."');");
	if (mysql_num_rows($spy_level_res)>0)
	{
		while ($spy_level_arr=mysql_fetch_row($spy_level_res))
		{
			if ($spy_level_arr[1] == SPY_TECH_ID)
			{
				$d_spy_level = $spy_level_arr[0];
			}
			elseif($spy_level_arr[1] == TARN_TECH_ID)
			{
				$d_tarn_level = $spy_level_arr[0];
			}
		}
	}	
	
	// Lade Spiosonden des Angreiffers
	$sres = dbquery("
	SELECT
		SUM(fs_ship_cnt)
	FROM
		fleet_ships
	INNER JOIN
		ships
		ON fs_ship_id=ship_id
		AND fs_fleet_id=".$arr['fleet_id']."
		AND ship_spy=1
	;");
	$sarr = mysql_fetch_row($sres);
	$a_spy_ships = $sarr[0];

	// Lade Spiosonden des Verteidigers
	$sres = dbquery("
	SELECT
		SUM(shiplist_count)
	FROM
		shiplist
	INNER JOIN
		ships
		ON shiplist_ship_id=ship_id
		AND shiplist_planet_id=".$arr['fleet_planet_to']."
		AND ship_spy=1
	;");
	$sarr = mysql_fetch_row($sres);
	$d_spy_ships = $sarr[0];
	

	$coords_blank = coords_format3($arr['fleet_planet_to']);
	$coords_target = coords_format2($arr['fleet_planet_to']);
	$coords_from = coords_format2($arr['fleet_planet_from']);

	if ($a_spy_ships>0)
	{
		// Calculate spy defense
		$spy_defense1 = ($d_spy_level / ($a_spy_level+$a_tarn_level) * SPY_DEFENSE_FACTOR_TECH);
		$spy_defense2 = (($d_spy_ships / $a_spy_ships)*SPY_DEFENSE_FACTOR_SHIPS);
		$spy_defense = min($spy_defense1 + $spy_defense2,SPY_DEFENSE_MAX);
		$defended = false;
		$roll = mt_rand(0,100);
		
		if ($roll <= $spy_defense)
		{
			$defended = true;
		}	
		
		if (!$defended)
		{
			// Calculate stealth bonus
			$tarn_defense = min($d_tarn_level / $a_spy_level * SPY_DEFENSE_FACTOR_TARN,SPY_DEFENSE_MAX);
					
			// Message header
			$toptext = "[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n";
			$text = '';
		
			//Gebäude anzeigen, wenn Spiotechlevel genug hoch ist
		  if ($a_spy_level >= SPY_ATTACK_SHOW_BUILDINGS && mt_rand(0,100)>$tarn_defense)
		  {
				// Lädt Gebäudedaten
				$spyres1 = dbquery("
				SELECT
			    b.building_name,
			    bl.buildlist_current_level
				FROM
			    ".$db_table['buildings']." AS b
			    INNER JOIN
			    ".$db_table['buildlist']." AS bl
			    ON bl.buildlist_building_id=b.building_id
			    AND bl.buildlist_planet_id='".$arr['fleet_planet_to']."'
			    AND bl.buildlist_user_id='".$user_to_id."'
			    AND buildlist_current_level>0
				ORDER BY
					b.building_name;");
		  	
			  $text.="\n[b]GEBÄUDE:[/b]\n";
			  if (mysql_num_rows($spyres1)>0)
			  {
			  	$text .= "[table]";
			    while ($spyarr1 = mysql_fetch_array($spyres1))
			    {
			        $text.="[tr][td]".$spyarr1['building_name']."[/td][td]".$spyarr1['buildlist_current_level']."[/td][/tr]";
			    }
			  	$text .= "[/table]";
		    }
		    else
		    {
		    	$text.="[i]Nichts vorhanden[/i]\n";
		    }
		  }
		
			// Techs anzeigen, wenn Spiotechlevel genug hoch ist
		  if ($a_spy_level >= SPY_ATTACK_SHOW_RESEARCH && mt_rand(0,100)>$tarn_defense)
		  {
				// Lädt Technologiedaten
				$spyres2 = dbquery("
				SELECT
			    t.tech_name,
			    tl.techlist_current_level
				FROM
			    ".$db_table['technologies']." AS t
			    INNER JOIN
			    ".$db_table['techlist']." AS tl
			    ON tl.techlist_tech_id=t.tech_id
			    AND tl.techlist_user_id='".$user_to_id."'
			    AND techlist_current_level>0
				ORDER BY
					t.tech_name;");	  	
					
			  $text.="\n[b]TECHNOLOGIEN[/b]:\n";
		  	if (mysql_num_rows($spyres2)>0)
		  	{
			  	$text .= "[table]";
		      while ($spyarr2 = mysql_fetch_array($spyres2))
		      {
		          $text.="[tr][td]".$spyarr2['tech_name']."[/td][td]".$spyarr2['techlist_current_level']."[/td][/tr]";
		      }
			  	$text .= "[/table]";
		    }
		    else
		    {
		    	$text.="[i]Nichts vorhanden[/i]\n";
		    }
		  }
		
			// Schiffe anzeigen, wenn Spiotechlevel genug hoch ist
		  if ($a_spy_level >= SPY_ATTACK_SHOW_SHIPS && mt_rand(0,100)>$tarn_defense)
		  {
				//Lädt Schiffsdaten
				$spyres3 = dbquery("
				SELECT
					shiplist_ship_id,
			    shiplist_count as
				FROM
			    shiplist
				WHERE
			    shiplist_planet_id='".$arr['fleet_planet_to']."'
			    AND shiplist_count>0");	  	
		  	
			  $text.="\n[b]SCHIFFE[/b]:\n";
			  if (mysql_num_rows($spyres3)>0)
			  {
			  	$text .= "[table]";
		      while ($spyarr3 = mysql_fetch_row($spyres3))
		      {
		          $text.="[tr][td]".$spyarr3[1]."[/td][td][ship ".$spyarr3[0]."][/td][/tr]";
		      }
			  	$text .= "[/table]";
		    }
		    else
		    {
		    	$text.="[i]Nichts vorhanden[/i]\n";
		    }
		  }
		
			// Verteidigung anzeigen, wenn Spiotechlevel genug hoch ist
		  if ($a_spy_level >= SPY_ATTACK_SHOW_DEFENSE && mt_rand(0,100)>$tarn_defense)
		  {
				//Lädt Verteidigungsdaten
				$spyres4 = dbquery("
				SELECT
					deflist_def_id,
			    deflist_count
				FROM
			    deflist
			   WHERE
			    deflist_planet_id='".$arr['fleet_planet_to']."'
			    AND deflist_count>0;");	
					  	
			  $text.="\n[b]VERTEIDIGUNG[/b]:\n";
		  	if (mysql_num_rows($spyres4)>0)
		  	{
			  	$text .= "[table]";
		      while ($spyarr4 = mysql_fetch_row($spyres4))
		      {
	          $text.="[tr][td]".$spyarr4[1]."[/td][td][def ".$spyarr4[0]."][/td][/tr]";
		      }
			  	$text .= "[/table]";
		    }
		    else
		    {
		    	$text.="[i]Nichts vorhanden[/i]\n";
		    }
		  }
		
			//Rohstoffe anzeigen, wenn Spiotechlevel genug hoch ist
		  if ($a_spy_level >= SPY_ATTACK_SHOW_RESSOURCEN && mt_rand(0,100)>$tarn_defense)
		  {
		    $text.="\n[b]RESSOURCEN:[/b]\n";
		  	$text .= "[table]";
		    $r = get_ress_on_planet($arr['fleet_planet_to']);
		
		    $text.= "[tr][td]".RES_METAL."[/td][td]".nf($r['metal'])."[/td][/tr]";
		    $text.= "[tr][td]".RES_CRYSTAL."[/td][td]".nf($r['crystal'])."[/td][/tr]";
		    $text.= "[tr][td]".RES_PLASTIC."[/td][td]".nf($r['plastic'])."[/td][/tr]";
		    $text.= "[tr][td]".RES_FUEL."[/td][td]".nf($r['fuel'])."[/td][/tr]";
		    $text.= "[tr][td]".RES_FOOD."[/td][td]".nf($r['food'])."[/td][/tr]";
		  	$text .= "[/table]";
		  }
		
			if ($text!='')
			{
				$toptext .= $text."\n\n[b]Spionageabwehr:[/b] ".round($spy_defense)."%\n[b]Tarnung:[/b] ".round($tarn_defense)."%";
			}
			else
			{
				$toptext .= "\nDu konntest leider nichts über den Planeten herausfinden da deine Spionagetechnologie zu wenig weit entwickelt oder der Gegner zu gut getarnt ist!\n\n[b]Spionageabwehr:[/b] ".round($spy_defense)."%\n[b]Tarnung:[/b] ".$tarn_defense."%";
			}
		
			//Spionagebericht senden
			send_msg($arr['fleet_user_id'],SHIP_SPY_MSG_CAT_ID,"Spionagebericht ".$coords_blank,$toptext);
		
			// Ausgespionierten Spieler informieren
			$text2="Eine fremde Flotte vom Planeten ".$coords_from." wurde in der Nähe deines Planeten ".$coords_target." gesichtet!\n\n[b]Spionageabwehr:[/b] ".round($spy_defense)."%";
			send_msg($user_to_id,SHIP_MONITOR_MSG_CAT_ID,"Raumüberwachung",$text2);
		}
		else
		{
			//Spionagebericht senden
			$text = "Dein Versuch, den Planeten ".$coords_target." auszuspionieren schlug fehl, da du entdeckt wurdest. Deine Sonden kehren ohne Ergebniss zurück!\n\n[b]Spionageabwehr:[/b] ".round($spy_defense)."%";
			send_msg($arr['fleet_user_id'],SHIP_SPY_MSG_CAT_ID,"Spionage fehlgeschlagen auf ".$coords_blank,$text);
		
			// Ausgespionierten Spieler informieren
			$text2="Auf deinem Planeten ".$coords_target." wurde ein Spionageversuch vom Planeten ".$coords_from." erfolgreich verhindert!\n\n[b]Spionageabwehr:[/b] ".round($spy_defense)."%";
			send_msg($user_to_id,SHIP_MONITOR_MSG_CAT_ID,"Raumüberwachung",$text2);
		}
	}
	else
	{
			//Spionagebericht senden
			$text = "Dein Versuch, den Planeten ".$coords_target." auszuspionieren schlug fehl, da du keine Spionagesonden mitgeschickt hast!";
			send_msg($arr['fleet_user_id'],SHIP_SPY_MSG_CAT_ID,"Spionage fehlgeschlagen auf ".$coords_blank,$text);
		
	}
	
	$action="sr";
	
	// Flotte zurückschicken
  fleet_return($arr,$action);

?>