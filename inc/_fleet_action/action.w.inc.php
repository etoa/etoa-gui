<?PHP
	/**
	* Fleet-Action: Collect wreckage/debris field
	*/ 
	
	$capa=$arr['fleet_capacity'];

	//Lädt Trümmerfeld Rohstoffe
	$rparr = mysql_fetch_array(dbquery("
		SELECT 
			planet_wf_metal,
			planet_wf_crystal,
			planet_wf_plastic 
		FROM 
			planets 
		WHERE 
			planet_id='".$arr['fleet_planet_to']."';
	"));
	$raid_r[0]=$rparr['planet_wf_metal'];
	$raid_r[1]=$rparr['planet_wf_crystal'];
	$raid_r[2]=$rparr['planet_wf_plastic'];
	$debris_sum = array_sum($raid_r);
	
	// Prüfen ob TF nicht leer
	if ($debris_sum>0)
	{
		//Rohstoffe prozentual aufteilen, wenn die Kapazität nicht für das ganze TF reicht
		for ($rcnt=0;$rcnt<3;$rcnt++)
		{
			if ($capa<=$debris_sum)
				$raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]*$capa/$debris_sum);
			else
				$raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]);
		}
	
		// Rohstoffe vom Planeten abziehen
		dbquery("
			UPDATE
				planets
			SET
				planet_wf_metal=planet_wf_metal-'".$raid_r_to_ship[0]."',
				planet_wf_crystal=planet_wf_crystal-'".$raid_r_to_ship[1]."',
				planet_wf_plastic=planet_wf_plastic-'".$raid_r_to_ship[2]."'
			WHERE
				planet_id='".$arr['fleet_planet_to']."';
		");
	
		//Summiert erhaltene Rohstoffe vom TF zu des Ladung
		$metal=$arr['fleet_res_metal']+$raid_r_to_ship[0];
		$crystal=$arr['fleet_res_crystal']+$raid_r_to_ship[1];
		$plastic=$arr['fleet_res_plastic']+$raid_r_to_ship[2];
	
		// Flotte zurückschicken mit Ress von TF und bestehenden ress
		fleet_return($arr,"wr",$metal,$crystal,$plastic);
	
		// Nachricht senden
		$msg = "[b]TR&Uuml;MMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]".coords_format2($arr['fleet_planet_to'])."[/b]\num [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b]\n erreicht und Tr&uuml;mmer gesammelt.\n";
		$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_METAL.": ".nf($raid_r_to_ship[0])."\n".RES_CRYSTAL.": ".nf($raid_r_to_ship[1])."\n".RES_PLASTIC.": ".nf($raid_r_to_ship[2])."";
		send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Tr&uuml;mmer gesammelt",$msg.$msgres);
	
	
	        //Erbeutete Rohstoffsumme speichern
	        $res_sum=array_sum($raid_r_to_ship);
	        dbquery("
			UPDATE
				users
			SET
				user_res_from_tf=user_res_from_tf+'".$res_sum."'
			WHERE
				user_id='".$arr['fleet_user_id']."';
		");  
	
		//Log schreiben
		add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat das Tr&uuml;mmerfeld bei [b]".coords_format2($arr['fleet_planet_to'])."[/b] um [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b] erreicht und Tr&uuml;mmer gesammelt.\n".$msgres,time());
	}
	// TF ist leer...
	else
	{
		// Flotte zurückschicken 
		fleet_return($arr,"wr");
	
		// Nachricht senden
		$msg = "[b]TRÜMMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]".coords_format2($arr['fleet_planet_to'])."[/b]\num [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b]\n erreicht.\n\n";
		$msgres = "Es wurden aber leider keine brauchbaren Trümmerteile mehr gefunden so dass die Flotte unverrichteter Dinge zurückkehren musste.";
		send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Tr&uuml;mmer gesammelt",$msg.$msgres);
		
	}
?>