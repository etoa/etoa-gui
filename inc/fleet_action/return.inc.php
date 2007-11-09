<?PHP
	/**
	* Fleet-Action: Returned flight
	*/
            
  //Flotte stationieren und Waren ausladen
  $msg_ships_res=fleet_land($arr,1);

  // Flotte-Schiffe-Verknüpfungen löschen
  fleet_delete($arr['fleet_id']);

	$send_msg = true;

	// Für Transporte und Spionage prüfen ob Return Nachricht gewünscht ist
	if ($arr['fleet_action']=='sr' || $arr['fleet_action']=='tr')
	{
		$mres = dbquery("
		SELECT
			user_fleet_rtn_msg
		FROM
			users
		WHERE
			user_id=".$arr['fleet_user_id']."
		;");
		$marr = mysql_fetch_row($mres);
		if ($marr[0]==0)
		{
			$send_msg = false;
		}		
	}
	
	if ($send_msg)
	{
		//Nachricht senden
	  $msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n
	[b]Zielplanet:[/b] ".coords_format2($arr['fleet_planet_to'])."
	[b]Startplanet:[/b] ".coords_format2($arr['fleet_planet_from'])."
	[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."
	[b]Auftrag:[/b] ".fa($arr['fleet_action']);
	  $msg.= $msg_ships_res[0].$msg_ships_res[1];
	  send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);
	}

  $showinfo=0;
?>