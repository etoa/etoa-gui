<?PHP
	/**
	* Fleet-Action: Colonialize
	*/
	
  // Planet auf Besitzer prüfen
	$ures = dbquery("
		SELECT 
			planet_user_id 
		FROM 
			planets 
		WHERE 
			planet_user_id>0 
			AND planet_id='".$arr['fleet_planet_to']."';
	");
  $uarr = mysql_fetch_row($ures);
	
	//Planet ist bereits kolonialisiert
	if ($uarr[0]>0)
	{
	  //Planet wurde bereits vom gleichen User kolonialisiert
	  if($uarr[0]==$arr['fleet_user_id'])
	  {
	    //Flotte stationieren & Waren ausladen (ohne abzug eines Kolonieschiffes)
	    $msg_ship_res=fleet_land($arr,1,1);

      // Flotte-Schiffe-Verknüpfungen löschen
      dbquery("
				DELETE FROM 
					fleet_ships 
				WHERE 
					fs_fleet_id='".$arr['fleet_id']."';
			");

      // Flotte aufheben
      dbquery("
				DELETE FROM 
					fleet 
				WHERE 
					fleet_id='".$arr['fleet_id']."';
			");

			//Nachricht senden
      $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
      $msg.= $msg_ship_res[0].$msg_ship_res[1];
      send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

      $showinfo=0;
	  }
	  
    //Planet gehört bereits an einem anderen User
    else
    {
    	//Nachricht senden
      $msg = "Die Flotte kann den Planeten nicht kolonialisieren, da er bereits von einem anderen Volk kolonialisiert wurde!\n";
      send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Landung nicht möglich",$msg);

      // Flotte zurückschicken
      fleet_return($arr,"koc");
    }
	}
   
  // Planet ist noch frei und kann kolonialisiert werden
  else
  {
    // Auf eigene Maximalanzahl prüfen
    $ures = dbquery("
			SELECT 
				COUNT(planet_user_id)
			FROM 
				planets 
			WHERE 
				planet_user_id='".$arr['fleet_user_id']."';
		");
		$uarr = mysql_fetch_row($ures);
		
		// Spieler hat bereits maximalanzahl an Planeten
    if ($uarr[0] >= $conf['user_max_planets']['v'])
    {
	  	//Nachricht senden
	    $msg = "Die Flotte kann den Planeten nicht kolonialisieren, da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist!\n";
	    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Landung nicht möglich",$msg);
	
	    // Flotte zurückschicken
	    fleet_return($arr,"koc");
    }
    
    //Kolonie erfolgreich gewonnen
    else
    {
      //Planet zurücksetzen
      reset_planet($arr['fleet_planet_to']);

      // Planet übernehmen
      dbquery("
			UPDATE
				planets
			SET
				planet_user_id='".$arr['fleet_user_id']."',
				planet_name='Unbenannt'
			WHERE
				planet_id='".$arr['fleet_planet_to']."';
				");

      //Flotte stationieren & Waren ausladen (mit abzug eines Kolonieschiffes)
      $msg_ship_res=fleet_land($arr,1);

		  // Flotte-Schiffe-Verknüpfungen löschen
		  dbquery("
				DELETE FROM 
					fleet_ships 
				WHERE 
					fs_fleet_id='".$arr['fleet_id']."';
			");

      // Flotte aufheben
      dbquery("
				DELETE FROM 
					fleet 
				WHERE 
					fleet_id='".$arr['fleet_id']."';
			");

			//Nachricht senden
      $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n";
      $msg .= "[b]Bericht:[/b] Die Flotte hat eine neue Kolonie errichtet! Dabei wurde ein Besiedlungsschiff verbraucht.\n";
      $msg.= $msg_ship_res[0].$msg_ship_res[1];
      send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Planet kolonialisiert",$msg);

      $showinfo=0;
    }
	}
?>