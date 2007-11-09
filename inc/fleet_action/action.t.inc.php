<?PHP
	/**
	* Fleet-Action: Transport
	*/
	
	//Waren ausladen
  $msgres=fleet_land($arr,2);

	//Sucht User-ID
	$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);

	$msg = "[B]TRANSPORT GELANDET[/B]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n";
	$msg.= $msgres;
	
	// Nachrichten senden
	send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Transport angekommen",$msg);
	
	//Nachricht an Empfänger senden, falls Empfänger != Sender
	if ($arr['fleet_user_id']!=$user_to_id)
	{
		send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Transport angekommen",$msg);
	}

  // Flotte zurückschicken & Waren aus dem Frachtraum löschen
  fleet_return($arr,"tr","0","0","0","0","0","0");

	// Handel loggen falls der transport an einen anderen user ging
	if($arr['fleet_user_id']!=$user_to_id)
	{
		add_log("11","Der Spieler [URL=?page=user&sub=edit&user_id=".$arr['fleet_user_id']."][B]".get_user_nick($arr['fleet_user_id'])."[/B][/URL] sendet dem Spieler [URL=?page=user&sub=edit&user_id=".$user_to_id."][B]".get_user_nick($user_to_id)."[/B][/URL] folgende Rohstoffe\n\n".$msgres."",time());
	}

?>