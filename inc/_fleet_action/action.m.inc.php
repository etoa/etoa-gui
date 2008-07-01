<?PHP

	$land_action = 1;
	$sres = dbquery("
		SELECT 
			fs_ship_id 
		FROM
			fleet_ships
		WHERE 
			fs_fleet_id=".$arr['fleet_id']."
	;");
	$sn = mysql_num_rows($sres);
	while ($sarr=mysql_fetch_row($sres))
	{
		if ($sarr[0]==MARKET_SHIP_ID && $sn==1)
		{
			$land_action = 2;
			break;
		}
	}

	// Sucht User-ID
	$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);

	// Resources and ships
	if ($land_action==1)
	{
      //Flotte stationieren und Waren ausladen
      $msg_ships_res=fleet_land($arr,1);

			//Nachricht senden
			$msg.="Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die gekauften Schiffe sind gelandet.\n";
			$msg.=$msg_ships_res[0];
			//Wenn das schiff auch Rohstoffe mitgebracht hat
			if($arr['fleet_res_metal']!='0' || $arr['fleet_res_crystal']!='0' || $arr['fleet_res_plastic']!='0' || $arr['fleet_res_fuel']!='0' || $arr['fleet_res_food']!='0')
			{
				//Nachricht, wie viele Rohstoffe abgeladen wurden
				$msg.="Es wurden zudem folgende Rohstoffe abgeladen:\n";
				$msg.=$msg_ships_res[1];
			}

			$msg.="\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";

			send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Flotte vom Handelsministerium",$msg);
	}
	
	// Only resources
	else
	{
      //Waren ausladen
      $msgres=fleet_land($arr,2);

			//Nachricht senden
			$msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Folgende Waren wurden ausgeladen:\n";
			$msg.= $msgres;
			$msg.="\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";

			send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Transport vom Handelsministerium",$msg);
	}

	// Flotte-Schiffe-Verknüpfungen löschen
	fleet_delete($arr['fleet_id']);
?>