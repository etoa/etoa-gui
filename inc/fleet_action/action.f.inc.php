<?PHP
	/**
	* Fleet-Action: Fetch
	*/
	
	 $pres = dbquery("
			SELECT
				planet_res_metal,
				planet_res_crystal,
				planet_res_plastic,
				planet_res_fuel,
				planet_res_food,
				planet_people,
				planet_user_id
			FROM
				planets
			WHERE
				planet_id='".$arr['fleet_planet_to']."';
		");
	$parr = mysql_fetch_array($pres);

	if ($arr['fleet_user_id']==$parr['planet_user_id'])
	{
		$capa = $arr['fleet_res_metal']+$arr['fleet_res_crystal']+$arr['fleet_res_plastic']+$arr['fleet_res_fuel']+$arr['fleet_res_food']+$arr['fleet_capacity'];
		$capa_cnt = 0;
		
		$load[0]=0;
		$load[1]=0;
		$load[2]=0;
		$load[3]=0;
		$load[4]=0;
		
		
		$load[0] = floor(min($arr['fleet_res_metal'],$parr['planet_res_metal'],$capa));
		$capa_cnt += $load[0];
		if ($capa_cnt < $capa)
		{
			$load[1] = floor(min($arr['fleet_res_crystal'],$parr['planet_res_crystal'],$capa-$capa_cnt));
			$capa_cnt += $load[1];
			if ($capa_cnt < $capa)
			{
				$load[2] = floor(min($arr['fleet_res_plastic'],$parr['planet_res_plastic'],$capa-$capa_cnt));
				$capa_cnt += $load[2];
				if ($capa_cnt < $capa)
				{
					$load[3] = floor(min($arr['fleet_res_fuel'],$parr['planet_res_fuel'],$capa-$capa_cnt));
					$capa_cnt += $load[3];
					if ($capa_cnt < $capa)
					{
						$load[4] = floor(min($arr['fleet_res_food'],$parr['planet_res_food'],$capa-$capa_cnt));
						$capa_cnt += $load[4];
					}			
				}			
			}				
		}		
		
		$load_people = min($arr['fleet_res_people'],$arr['fleet_capacity_people'],$parr['planet_people']);
		
		$msg = "[B]WAREN ABGEHOLT[/B]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n";
		$msg.= "\nFolgende Waren wurden abgeholt: \n\n[table]";
		$msg.= "[tr][th]".RES_METAL."[/th][td]".nf($load[0])."[/td][/tr]";
		$msg.= "[tr][th]".RES_CRYSTAL."[/th][td]".nf($load[1])."[/td][/tr]";
		$msg.= "[tr][th]".RES_PLASTIC."[/th][td]".nf($load[2])."[/td][/tr]";
		$msg.= "[tr][th]".RES_FUEL."[/th][td]".nf($load[3])."[/td][/tr]";
		$msg.= "[tr][th]".RES_FOOD."[/th][td]".nf($load[4])."[/td][/tr]";
		if ($load_people>0)
		{
			$msg.= "[tr][th]Bewohner[/th][td]".nf($load_people)."[/td][/tr]";
		}
		$msg.= "[/table]";		
		
		
     dbquery("
			UPDATE
				".$db_table['planets']."
			SET
				planet_res_metal=planet_res_metal-'".$load[0]."',
				planet_res_crystal=planet_res_crystal-'".$load[1]."',
				planet_res_plastic=planet_res_plastic-'".$load[2]."',
				planet_res_fuel=planet_res_fuel-'".$load[3]."',
				planet_res_food=planet_res_food-'".$load[4]."',
				planet_people=planet_people-'".$load_people."',
			WHERE
				planet_id='".$arr['fleet_planet_to']."';
		");		
		
		// Nachrichten senden
		send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Warenabholung",$msg);
  	fleet_return($arr,"fr",$load[0],$load[1],$load[2],$load[3],$load[4],$load_people);
	}
	else
	{
  	fleet_return($arr,"fr","0","0","0","0","0","0");
  }

?>