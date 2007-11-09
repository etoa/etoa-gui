<?PHP
	/**
	* Fleet-Action: Position
	*/
            
  //Flotte stationieren und Waren ausladen
  $msg_ships_res=fleet_land($arr,1);

  // Flotte-Schiffe-Verknüpfungen löschen
 	fleet_delete($arr['fleet_id']);

	//Nachricht senden
  $msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Startplanet:[/b] ".coords_format2($arr['fleet_planet_from'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n[b]Auftrag:[/b] ".fa($arr['fleet_action']);
  $msg.= $msg_ships_res[0].$msg_ships_res[1];
  send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

  $showinfo=0;
?>