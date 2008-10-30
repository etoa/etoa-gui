<?PHP
	/**
	* Fleet-Action: Create debris field
	*
	* Whole fleet will be destroyed!
	*/ 

			//Verwandelt die ganze Flotte in ein TF (Grösse = 40% der Baukosten)
			$rres=dbquery("
				SELECT
					s.ship_id,
					s.ship_costs_metal,
					s.ship_costs_crystal,
					s.ship_costs_plastic,
					fs.fs_ship_cnt
				FROM
					fleet_ships AS fs 
					INNER JOIN ships AS s ON fs.fs_ship_id = s.ship_id
					AND fs.fs_fleet_id='".$arr['fleet_id']."';
			");
			while ($rarr=mysql_fetch_array($rres))
			{
				$cnt=ceil($rarr['fs_ship_cnt']*0.4);
				$tf_metal+=$cnt*$rarr['ship_costs_metal'];
				$tf_crystal+=$cnt*$rarr['ship_costs_crystal'];
				$tf_plastic+=$cnt*$rarr['ship_costs_plastic'];

				dbquery("
					DELETE FROM 
						fleet_ships 
					WHERE 
						fs_fleet_id='".$arr['fleet_id']."' 
						AND fs_ship_id='".$rarr['ship_id']."';
				");
			}

			//Speichert enstandenes TF (Rohstoffe werden zum bestehenden TF summiert)
			dbquery("
				UPDATE
					planets
				SET
					planet_wf_metal=planet_wf_metal+'".$tf_metal."',
					planet_wf_crystal=planet_wf_crystal+'".$tf_crystal."',
					planet_wf_plastic=planet_wf_plastic+'".$tf_plastic."'
				WHERE
					planet_id='".$arr['fleet_planet_to']."';
			");

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
			$coords_target = coords_format2($arr['fleet_planet_to']);
			$coords_from = coords_format2($arr['fleet_planet_from']);
			$msg="Eine Flotte vom Planeten ".$coords_from." hat auf dem Planeten ".$coords_target." ein Trümmerfeld erstellt.";
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Trümmerfeld erstellt",$msg);

			//Log schreiben
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten ".$coords_from." hat auf dem Planeten ".$coords_target." ein Trümmerfeld erstellt.",time());
	
?>