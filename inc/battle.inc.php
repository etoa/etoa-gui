<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: battle.php													//
	// Topic: Kampfscript			 									//
	// Version: 0.1																	//
	// Letzte Änderung: 21.05.2005									//
	//////////////////////////////////////////////////


function battle($fleet_id,$planet_id)
{
		global $db_table;

    // BEGIN SKRIPT //

    $ships_a = array();
    $special_ships_a = array();
    $ships_d = array();
    $special_ships_d = array();
    $defs = array();

    $structure_a=0;
    $shield_a=0;
    $weapon_a=0;
    $count_a=0;
    $count_heal_a=0;
    
    $alliances_have_war = 0;

		//Lädt Flottendaten
    $fleetarr = mysql_fetch_array(dbquery("
		SELECT
			fleet_user_id,
			fleet_landtime,
			fleet_launchtime,
			fleet_cell_from,
			fleet_cell_to,
			fleet_planet_from,
			fleet_planet_to,
			fleet_capacity,
			fleet_action
		FROM
			".$db_table['fleet']."
		WHERE
			fleet_id='".$fleet_id."';
		"));

		//Speichert ID von beiden Kontrahenten
    $user_a_id = $fleetarr['fleet_user_id'];
    $user_d_id = get_user_id_by_planet($planet_id);
    
   	// Kampf abbrechen falls User gleich
    if ($user_a_id==$user_d_id)
    {
	    $msg = "[b]KAMPFBERICHT[/b]\nvom Planeten ".coords_format2($planet_id)."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$fleetarr['fleet_landtime'])."\n\n";
	    $msg.= "[b]Angreifer:[/b] ".get_user_nick($user_a_id)."\n";
	    $msg.= "[b]Verteidiger:[/b] ".get_user_nick($user_d_id)."\n\n";
    	$msg.= "Der Kampf wurde abgebrochen da Angreifer und Verteidiger demselben Imperium angehören!";
    	
			$bstat = "Unentschieden";
			$bstat2 = "Unentschieden";
			$return_fleet=true;
    	return array($return_v,$msg,$bstat,$bstat2,$return_fleet);
  	}
/*
		//Lädt Allianz ID von beiden Kontrahenten
		$alliance_res = mysql_query("
		SELECT
			a.user_alliance_id AS alliance_id_a,
			d.user_alliance_id AS alliance_id_d
		FROM
			".$db_table['users']." AS a,
			".$db_table['users']." AS d
		WHERE
			a.user_id='".$user_a_id."'
			AND d.user_id='".$user_d_id."';");
		$alliance_arr = mysql_fetch_array($alliance_res);
*/
		$alliance_info_a = get_user_alliance($user_a_id);
		$alliance_info_d = get_user_alliance($user_d_id);

		// Prüft, ob Krieg herrscht
		if($alliance_info_a['alliance_id']!="" && $alliance_info_d['alliance_id']!="")
		{
			$war_check_res = mysql_query("
			SELECT
				alliance_bnd_id
			FROM
				".$db_table['alliance_bnd']."
			WHERE
					(alliance_bnd_alliance_id1='".$alliance_info_a['alliance_id']."'
					AND alliance_bnd_alliance_id2='".$alliance_info_d['alliance_id']."')
				OR
					(alliance_bnd_alliance_id1='".$alliance_info_d['alliance_id']."'
					AND alliance_bnd_alliance_id2='".$alliance_info_a['alliance_id']."')
				AND alliance_bnd_level='3';");
			if (mysql_num_rows($war_check_res)>0)
			{
				$alliances_have_war = 1;
			}
		}
/*
		// Lädt Allianz Name + Kürzel
		if($alliance_arr['alliance_id_a']>0)
		{
			$alliance_info_a = get_user_alliance($user_a_id);
		}
		if($alliance_arr['alliance_id_d']>0)
		{
			$alliance_info_d = get_user_alliance($user_d_id);
		}
*/
    $msg = "[b]KAMPFBERICHT[/b]\nvom Planeten ".coords_format2($planet_id)."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$fleetarr['fleet_landtime'])."\n\n";
    $msg.= "[b]Angreifer:[/b] ".get_user_nick($user_a_id)."\n";
    $msg.= "[b]Verteidiger:[/b] ".get_user_nick($user_d_id)."\n\n";
    $msg.= "[b]ANGREIFENDE FLOTTE:[/b]\n";


	//
	// Flotten Daten (att)
	//

        // Daten der angreifenden Flotte laden (spezial)
        $sfres = dbquery("
			SELECT
				s.ship_id,
				s.ship_name,
				s.ship_structure,
				s.ship_shield,
				s.ship_weapon,
				s.ship_costs_metal,
				s.ship_costs_crystal,
				s.ship_costs_plastic,
				s.ship_costs_fuel,
				s.ship_costs_food,
				s.ship_steal,
				s.special_ship,
				s.special_ship_need_exp,
				s.special_ship_exp_factor,
				s.special_ship_bonus_weapon,
				s.special_ship_bonus_structure,
				s.special_ship_bonus_shield,
				s.special_ship_bonus_heal,
				s.special_ship_bonus_capacity,

				fs.fs_ship_cnt,
				fs.fs_ship_id,
				fs.fs_special_ship,
				fs.fs_special_ship_level,
				fs.fs_special_ship_exp,
				fs.fs_special_ship_bonus_weapon,
				fs.fs_special_ship_bonus_structure,
				fs.fs_special_ship_bonus_shield,
				fs.fs_special_ship_bonus_heal,
				fs.fs_special_ship_bonus_capacity,
				fs.fs_special_ship_bonus_speed,
				fs.fs_special_ship_bonus_pilots,
				fs.fs_special_ship_bonus_tarn,
				fs.fs_special_ship_bonus_antrax,
				fs.fs_special_ship_bonus_forsteal,
				fs.fs_special_ship_bonus_build_destroy,
				fs.fs_special_ship_bonus_antrax_food,
				fs.fs_special_ship_bonus_deactivade
			FROM
				".$db_table['shiplist']." AS sl 
				INNER JOIN
				(
					(
						".$db_table['fleet_ships']." AS fs
						INNER JOIN 
						".$db_table['fleet']." AS f 
						ON fs.fs_fleet_id = f.fleet_id
						AND f.fleet_id='".$fleet_id."'
					)
					INNER JOIN 
					".$db_table['ships']." AS s 
					ON fs.fs_ship_id = s.ship_id
					AND s.special_ship='1'
				)
				ON sl.shiplist_ship_id = s.ship_id
				AND sl.shiplist_planet_id = f.fleet_planet_from
				AND sl.shiplist_user_id = f.fleet_user_id
			ORDER BY
				s.special_ship DESC,
				s.ship_name;
		");

        if (mysql_num_rows($sfres)>0)
        {
            while ($sfarr=mysql_fetch_array($sfres))
            {
                if ($sfarr['ship_steal']!='50')
                {
                    $ship_steal=$sfarr['ship_steal'];
                }
                else
                {
                    $dont_steal=1;
                }

                $msg.= "[B]".$sfarr['ship_name']."[/B] ".nf($sfarr['fs_ship_cnt'])."\n";

                array_push(
                $special_ships_a,
                array("id"=>$sfarr['ship_id'],
                "cnt"=>$sfarr['fs_ship_cnt'],
                "new_cnt"=>0,
                "name"=>$sfarr['ship_name'],
                "structure"=>$sfarr['ship_structure'],
                "shield"=>$sfarr['ship_shield'],
                "weapon"=>$sfarr['ship_weapon'],
                "costs_metal"=>$sfarr['ship_costs_metal'],
                "costs_crystal"=>$sfarr['ship_costs_crystal'],
                "costs_plastic"=>$sfarr['ship_costs_plastic'],
                "costs_fuel"=>$sfarr['ship_costs_fuel'],
                "costs_food"=>$sfarr['ship_costs_food'],

                "need_exp"=>$sfarr['special_ship_need_exp'],
                "exp_factor"=>$sfarr['special_ship_exp_factor'],
                "bonus_weapon"=>$sfarr['special_ship_bonus_weapon'],
                "bonus_structure"=>$sfarr['special_ship_bonus_structure'],
                "bonus_shield"=>$sfarr['special_ship_bonus_shield'],
                "bonus_heal"=>$sfarr['special_ship_bonus_heal'],
                "bonus_capacity"=>$sfarr['special_ship_bonus_capacity'],


                "ship_exp"=>$sfarr['fs_special_ship_exp'],
                "ship_level"=>$sfarr['fs_special_ship_level'],
                "ship_bonus_weapon"=>$sfarr['fs_special_ship_bonus_weapon'],
                "ship_bonus_structure"=>$sfarr['fs_special_ship_bonus_structure'],
                "ship_bonus_shield"=>$sfarr['fs_special_ship_bonus_shield'],
                "ship_bonus_heal"=>$sfarr['fs_special_ship_bonus_heal'],
                "ship_bonus_capacity"=>$sfarr['fs_special_ship_bonus_capacity'],
                "ship_bonus_speed"=>$sfarr['fs_special_ship_bonus_speed'],
                "ship_bonus_pilots"=>$sfarr['fs_special_ship_bonus_pilots'],
                "ship_bonus_tarn"=>$sfarr['fs_special_ship_bonus_tarn'],
                "ship_bonus_antrax"=>$sfarr['fs_special_ship_bonus_antrax'],
                "ship_bonus_forsteal"=>$sfarr['fs_special_ship_bonus_forsteal'],
                "ship_bonus_destroy"=>$sfarr['fs_special_ship_bonus_build_destroy'],
                "ship_bonus_antrax_food"=>$sfarr['fs_special_ship_bonus_antrax_food'],
                "ship_bonus_deactivade"=>$sfarr['fs_special_ship_bonus_deactivade'])
                );

                $structure_a+=$sfarr['ship_structure']*$sfarr['fs_ship_cnt'];
                $shield_a+=$sfarr['ship_shield']*$sfarr['fs_ship_cnt'];
                $weapon_a+=$sfarr['ship_weapon']*$sfarr['fs_ship_cnt'];
                $count_a+=$sfarr['fs_ship_cnt'];
            }
        }

        // Daten der angreifenden Flotte laden (normal)
        $fres = dbquery("
			SELECT
				s.ship_id,
				s.ship_name,
				s.ship_structure,
				s.ship_shield,
				s.ship_weapon,
				s.ship_costs_metal,
				s.ship_costs_crystal,
				s.ship_costs_plastic,
				s.ship_costs_fuel,
				s.ship_costs_food,
				s.ship_steal,
				fs.fs_ship_cnt
			FROM
				(
					".$db_table['fleet_ships']." AS fs 
					INNER JOIN 
					".$db_table['fleet']." AS f 
					ON fs.fs_fleet_id = f.fleet_id
					AND f.fleet_id='".$fleet_id."'
				)
				INNER JOIN 
				".$db_table['ships']." AS s ON 
				fs.fs_ship_id = s.ship_id
				AND s.special_ship='0'
			ORDER BY
				s.ship_name;
		");
        if (mysql_num_rows($fres)>0)
        {
            while ($farr=mysql_fetch_array($fres))
            {
                if ($farr['ship_steal']!='50')
                {
                    $ship_steal=$farr['ship_steal'];
                }
                else
                {
                    $dont_steal=1;
                }

                $msg.= "".$farr['ship_name']." ".nf($farr['fs_ship_cnt'])."\n";

                array_push(
                $ships_a,
                array("id"=>$farr['ship_id'],
                "cnt"=>$farr['fs_ship_cnt'],
                "new_cnt"=>0,
                "name"=>$farr['ship_name'],
                "structure"=>$farr['ship_structure'],
                "shield"=>$farr['ship_shield'],
                "weapon"=>$farr['ship_weapon'],
                "costs_metal"=>$farr['ship_costs_metal'],
                "costs_crystal"=>$farr['ship_costs_crystal'],
                "costs_plastic"=>$farr['ship_costs_plastic'],
                "costs_fuel"=>$farr['ship_costs_fuel'],
                "costs_food"=>$farr['ship_costs_food'])
                );

                $structure_a+=$farr['ship_structure']*$farr['fs_ship_cnt'];
                $shield_a+=$farr['ship_shield']*$farr['fs_ship_cnt'];
                $weapon_a+=$farr['ship_weapon']*$farr['fs_ship_cnt'];
                $count_a+=$farr['fs_ship_cnt'];
            }
        }

        //Daten der Heilenden Flotte laden (att)
        $fhres=dbquery("
			SELECT
				fs.fs_ship_cnt,
				s.ship_heal
			FROM
				(
					".$db_table['fleet_ships']." AS fs 
					INNER JOIN 
					".$db_table['fleet']." AS f 
					ON fs.fs_fleet_id = f.fleet_id
					AND f.fleet_id='".$fleet_id."'
				)
				INNER JOIN 
				".$db_table['ships']." AS s 
				ON fs.fs_ship_id = s.ship_id
				AND s.ship_heal>'0';
		");
        if (mysql_num_rows($fhres)>0)
        {
            while ($fharr=mysql_fetch_array($fhres))
            {
                $count_heal_a+=$fharr['fs_ship_cnt'];
                $heal_a+=$fharr['ship_heal']*$fharr['fs_ship_cnt'];
            }
        }




	//
	// Flotten & Def Daten (def)
	//

        $msg.= "\n[b]VERTEIDIGENDE FLOTTE:[/b]\n";

        // Daten der Verteidigung und der Flotte auf dem Planeten laden
        $psres = dbquery("
			SELECT
				s.ship_id,
				s.ship_name,
				s.ship_structure,
				s.ship_shield,
				s.ship_weapon,
				s.ship_costs_metal,
				s.ship_costs_crystal,
				s.ship_costs_plastic,
				s.ship_costs_fuel,
				s.ship_costs_food,
				s.ship_steal,
				s.special_ship,
				s.special_ship_need_exp,
				s.special_ship_exp_factor,
				s.special_ship_bonus_weapon,
				s.special_ship_bonus_structure,
				s.special_ship_bonus_shield,
				s.special_ship_bonus_heal,
				s.special_ship_bonus_capacity,

				sl.shiplist_count,
				sl.shiplist_special_ship,
				sl.shiplist_special_ship_level,
				sl.shiplist_special_ship_exp,
				sl.shiplist_special_ship_bonus_weapon,
				sl.shiplist_special_ship_bonus_structure,
				sl.shiplist_special_ship_bonus_shield,
				sl.shiplist_special_ship_bonus_heal,
				sl.shiplist_special_ship_bonus_capacity,
				sl.shiplist_special_ship_bonus_speed,
				sl.shiplist_special_ship_bonus_pilots,
				sl.shiplist_special_ship_bonus_tarn,
				sl.shiplist_special_ship_bonus_antrax,
				sl.shiplist_special_ship_bonus_forsteal,
				sl.shiplist_special_ship_bonus_build_destroy,
				sl.shiplist_special_ship_bonus_antrax_food,
				sl.shiplist_special_ship_bonus_deactivade
			FROM
				".$db_table['shiplist']." AS sl
				INNER JOIN 
				".$db_table['ships']." AS s 
				ON sl.shiplist_ship_id = s.ship_id
				AND sl.shiplist_planet_id='".$planet_id."'
				AND sl.shiplist_user_id='".$user_d_id."'
				AND sl.shiplist_count>'0'
			ORDER BY
				s.special_ship DESC,
				s.ship_name;
		");

        if (mysql_num_rows($psres)>0)
        {
            while ($psarr=mysql_fetch_array($psres))
            {
            	//Spezialschiffe (def)
            	if($psarr['special_ship']==1)
            	{
                    $msg.= "[B]".$psarr['ship_name']."[/B] ".nf($psarr['shiplist_count'])."\n";

                    array_push(
                    $special_ships_d,
                    array("id"=>$psarr['ship_id'],
                    "cnt"=>$psarr['shiplist_count'],
                    "new_cnt"=>0,
                    "name"=>$psarr['ship_name'],
                    "structure"=>$psarr['ship_structure'],
                    "shield"=>$psarr['ship_shield'],
                    "weapon"=>$psarr['ship_weapon'],
                    "costs_metal"=>$psarr['ship_costs_metal'],
                    "costs_crystal"=>$psarr['ship_costs_crystal'],
                    "costs_plastic"=>$psarr['ship_costs_plastic'],
                    "costs_fuel"=>$psarr['ship_costs_fuel'],
                    "costs_food"=>$psarr['ship_costs_food'],

                    "need_exp"=>$psarr['special_ship_need_exp'],
                    "exp_factor"=>$psarr['special_ship_exp_factor'],
                    "bonus_weapon"=>$psarr['special_ship_bonus_weapon'],
                    "bonus_structure"=>$psarr['special_ship_bonus_structure'],
                    "bonus_shield"=>$psarr['special_ship_bonus_shield'],
                    "bonus_heal"=>$psarr['special_ship_bonus_heal'],

                    "ship_exp"=>$psarr['shiplist_special_ship_exp'],
                    "ship_level"=>$psarr['shiplist_special_ship_level'],
                    "ship_bonus_weapon"=>$psarr['shiplist_special_ship_bonus_weapon'],
                    "ship_bonus_structure"=>$psarr['shiplist_special_ship_bonus_structure'],
                    "ship_bonus_shield"=>$psarr['shiplist_special_ship_bonus_shield'],
                    "ship_bonus_heal"=>$psarr['shiplist_special_ship_bonus_heal'],
                    "ship_bonus_capacity"=>$psarr['shiplist_special_ship_bonus_capacity'],
                    "ship_bonus_speed"=>$psarr['shiplist_special_ship_bonus_speed'],
                    "ship_bonus_pilots"=>$psarr['shiplist_special_ship_bonus_pilots'],
                    "ship_bonus_tarn"=>$psarr['shiplist_special_ship_bonus_tarn'],
                    "ship_bonus_antrax"=>$psarr['shiplist_special_ship_bonus_antrax'],
                    "ship_bonus_forsteal"=>$psarr['shiplist_special_ship_bonus_forsteal'],
                    "ship_bonus_destroy"=>$psarr['shiplist_special_ship_bonus_build_destroy'],
                    "ship_bonus_antrax_food"=>$psarr['shiplist_special_ship_bonus_antrax_food'],
                    "ship_bonus_deactivade"=>$psarr['shiplist_special_ship_bonus_deactivade'])
                    );

                    $structure_d+=$psarr['ship_structure']*$psarr['shiplist_count'];
                    $shield_d+=$psarr['ship_shield']*$psarr['shiplist_count'];
                    $weapon_d+=$psarr['ship_weapon']*$psarr['shiplist_count'];
                    $count_d+=$psarr['shiplist_count'];
                    $count_ds+=$psarr['shiplist_count'];
            	}
            	// normale schiffe (def)
            	else
            	{
                    $msg.= "".$psarr['ship_name']." ".nf($psarr['shiplist_count'])."\n";

                    array_push(
                    $ships_d,
                    array("id"=>$psarr['ship_id'],
                    "cnt"=>$psarr['shiplist_count'],
                    "new_cnt"=>0,
                    "name"=>$psarr['ship_name'],
                    "structure"=>$psarr['ship_structure'],
                    "shield"=>$psarr['ship_shield'],
                    "weapon"=>$psarr['ship_weapon'],
                    "costs_metal"=>$psarr['ship_costs_metal'],
                    "costs_crystal"=>$psarr['ship_costs_crystal'],
                    "costs_plastic"=>$psarr['ship_costs_plastic'],
                    "costs_fuel"=>$psarr['ship_costs_fuel'],
                    "costs_food"=>$psarr['ship_costs_food'])
                    );

                    $structure_d+=$psarr['ship_structure']*$psarr['shiplist_count'];
                    $shield_d+=$psarr['ship_shield']*$psarr['shiplist_count'];
                    $weapon_d+=$psarr['ship_weapon']*$psarr['shiplist_count'];
                    $count_d+=$psarr['shiplist_count'];
                    $count_ds+=$psarr['shiplist_count'];
				}
            }

            //Daten der Heilenden Flotte laden (def)
            $phres=dbquery("
				SELECT
					sl.shiplist_count,
					s.ship_heal
				FROM
					".$db_table['shiplist']." AS sl
					INNER JOIN 
					".$db_table['ships']." AS s 
					ON sl.shiplist_ship_id = s.ship_id
					AND sl.shiplist_planet_id='".$planet_id."'
					AND sl.shiplist_user_id='".$user_d_id."'
					AND sl.shiplist_count>'0'
					AND s.ship_heal>'0';
			");

            if (mysql_num_rows($phres)>0)
            {
                while ($pharr=mysql_fetch_array($phres))
                {
                    $count_heal_d+=$pharr['shiplist_count'];
                    $heal_d+=$pharr['ship_heal']*$pharr['shiplist_count'];
                }
            }
        }
        else
        {
            $msg.= "[i]Nichts vorhanden![/i]\n";
        }

        $msg.= "\n[b]PLANETARE VERTEIDIGUNG:[/b]\n";
        $pdres = dbquery("
			SELECT
				d.def_id,
				d.def_name,
				dl.deflist_count,
				d.def_structure,
				d.def_shield,
				d.def_weapon,
				d.def_costs_metal,
				d.def_costs_crystal,
				d.def_costs_plastic,
				d.def_costs_fuel,
				d.def_costs_food
			FROM
				".$db_table['deflist']." AS dl
				INNER JOIN 
				".$db_table['defense']." AS d ON 
				dl.deflist_def_id = d.def_id
				AND dl.deflist_planet_id='".$planet_id."'
				AND dl.deflist_user_id='".$user_d_id."'
				AND dl.deflist_count>'0';
		");

        if (mysql_num_rows($pdres))
        {
            while ($pdarr=mysql_fetch_array($pdres))
            {
                $msg.= "".$pdarr['def_name']." ".nf($pdarr['deflist_count'])."\n";

                array_push(
                $defs,
                array("id"=>$pdarr['def_id'],
                "cnt"=>$pdarr['deflist_count'],
                "new_cnt"=>0,
                "name"=>$pdarr['def_name'],
                "structure"=>$pdarr['def_structure'],
                "shield"=>$pdarr['def_shield'],
                "weapon_s"=>$pdarr['def_weapon'],
                "costs_metal"=>$pdarr['def_costs_metal'],
                "costs_crystal"=>$pdarr['def_costs_crystal'],
                "costs_plastic"=>$pdarr['def_costs_plastic'],
                "costs_fuel"=>$pdarr['def_costs_fuel'],
                "costs_food"=>$pdarr['def_costs_food'])
                );

                $structure_d+=$pdarr['def_structure']*$pdarr['deflist_count'];
                $shield_d+=$pdarr['def_shield']*$pdarr['deflist_count'];
                $weapon_d+=$pdarr['def_weapon']*$pdarr['deflist_count'];
                $count_d+=$pdarr['deflist_count'];
                $count_dd+=$pdarr['deflist_count'];
            }
        }
        else
        {
            $msg.= "[i]Nichts vorhanden![/i]\n";
        }

        $msg.= "\n";


	//
	//Technologie Daten laden (att)
	//
		$weapon_tech_a=1;
		$structure_tech_a=1;
        $shield_tech_a=1;
        $heal_tech_a=1;

        //Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (att)
        $techres_a = dbquery("
			SELECT
				techlist_tech_id,
				techlist_current_level
			FROM
				".$db_table['techlist']."
			WHERE
				techlist_user_id='".$user_a_id."'
				AND
				(
					techlist_tech_id='".STRUCTURE_TECH_ID."'
					OR techlist_tech_id='".SHIELD_TECH_ID."'
					OR techlist_tech_id='".WEAPON_TECH_ID."'
					OR techlist_tech_id='".REGENA_TECH_ID."'
				)
   		;");

		//forschung laden und bonus dazu rechnen (att)
        while ($techarr_a = mysql_fetch_array($techres_a))
        {
            if ($techarr_a['techlist_tech_id']==SHIELD_TECH_ID)
                $shield_tech_a+=($techarr_a['techlist_current_level']/10);

            if ($techarr_a['techlist_tech_id']==STRUCTURE_TECH_ID)
                $structure_tech_a+=($techarr_a['techlist_current_level']/10);

            if ($techarr_a['techlist_tech_id']==WEAPON_TECH_ID)
                $weapon_tech_a+=($techarr_a['techlist_current_level']/10);

            if ($techarr_a['techlist_tech_id']==REGENA_TECH_ID)
                $heal_tech_a+=($techarr_a['techlist_current_level']/10);
        }
        //bonus von spezialschiffe laden und dazu rechnen (att)
        foreach ($special_ships_a as $id=>$data)
        {
			$weapon_tech_a+=$data['bonus_weapon']*$data['ship_bonus_weapon'];
			$structure_tech_a+=$data['bonus_structure']*$data['ship_bonus_structure'];
			$shield_tech_a+=$data['bonus_shield']*$data['ship_bonus_shield'];
			$heal_tech_a+=$data['bonus_heal']*$data['ship_bonus_heal'];
        }



	//
	//Technologie Daten laden (def)
	//

		$weapon_tech_d=1;
		$structure_tech_d=1;
        $shield_tech_d=1;
        $heal_tech_d=1;

        //Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (def)
        $techres_d = dbquery("
			SELECT
				techlist_tech_id,
				techlist_current_level
			FROM
				".$db_table['techlist']."
			WHERE
				techlist_user_id='".$user_d_id."'
				AND
				(
					techlist_tech_id='".STRUCTURE_TECH_ID."'
					OR techlist_tech_id='".SHIELD_TECH_ID."'
					OR techlist_tech_id='".WEAPON_TECH_ID."'
					OR techlist_tech_id='".REGENA_TECH_ID."'
				)
        ;");

		//forschung laden und bonus dazu rechnen (def)
        while ($techarr_d = mysql_fetch_array($techres_d))
        {
            if ($techarr_d['techlist_tech_id']==SHIELD_TECH_ID)
                $shield_tech_d+=($techarr_d['techlist_current_level']/10);

            if ($techarr_d['techlist_tech_id']==STRUCTURE_TECH_ID)
                $structure_tech_d+=($techarr_d['techlist_current_level']/10);

            if ($techarr_d['techlist_tech_id']==WEAPON_TECH_ID)
                $weapon_tech_d+=($techarr_d['techlist_current_level']/10);

            if ($techarr_d['techlist_tech_id']==REGENA_TECH_ID)
                $heal_tech_d+=($techarr_d['techlist_current_level']/10);
        }
        //bonus von spezialschiffe laden und dazu rechnen (def)
        foreach ($special_ships_d as $id=>$data)
        {
			$weapon_tech_d+=$data['bonus_weapon']*$data['ship_bonus_weapon'];
			$structure_tech_d+=$data['bonus_structure']*$data['ship_bonus_structure'];
			$shield_tech_d+=$data['bonus_shield']*$data['ship_bonus_shield'];
			$heal_tech_d+=$data['bonus_heal']*$data['ship_bonus_heal'];
        }



	//
	//Kampf Daten errechnen
	//
		//$init_... = wert vor dem kampf (wird nicht verändert)

		//Anzahl Schiffe
        $init_count_a = $count_a;
        $init_count_d = $count_d;

        //Anzahl der heilenden Schiffe
        $init_count_heal_a=$count_heal_a;
        $init_count_heal_d=$count_heal_d;

		//Heilpunkte
		$heal_points_a=$heal_a*$heal_tech_a;
		$heal_points_d=$heal_d*$heal_tech_d;

        //Schidfstärke
        $shield_a*=$shield_tech_a;
        $shield_d*=$shield_tech_d;

		//Strukturstärke
        $structure_a*=$structure_tech_a;
        $structure_d*=$structure_tech_d;

		//Waffenstärke
        $init_weapon_a_b=$weapon_a*$weapon_tech_a;
        $init_weapon_d_b=$weapon_d*$weapon_tech_d;

		//Schild + Strukturstärke
        $init_strushield_a=$shield_a+$structure_a;
        $init_strushield_d=$shield_d+$structure_d;

		//Schild + Strukturstärke
        $strushield_a=$init_strushield_a;
        $strushield_d=$init_strushield_d;


        $msg.="[b]DATEN DES ANGREIFERS[/b]\n[b]Schild (".nf($shield_tech_a*100)."%):[/b] ".nf($shield_a)."\n[b]Struktur (".nf($structure_tech_a*100)."%):[/b] ".nf($structure_a)."\n[b]Waffen (".nf($weapon_tech_a*100)."%):[/b] ".nf($init_weapon_a_b)."\n[b]Einheiten:[/b] ".nf($count_a);
        $msg.="\n\n[b]DATEN DES VERTEIDIGERS[/b]\n[b]Schild (".nf($shield_tech_d*100)."%):[/b] ".nf($shield_d)."\n[b]Struktur (".nf($structure_tech_d*100)."%):[/b] ".nf($structure_d)."\n[b]Waffen (".nf($weapon_tech_d*100)."%):[/b] ".nf($init_weapon_d_b)."\n[b]Einheiten:[/b] ".nf($count_d)."\n\n";


	//
	//Der Kampf!
	//

        for ($bx=0;$bx<BATTLE_ROUNDS;$bx++)
        {
            $weapon_a_b = @round($init_weapon_a_b * $count_a / $init_count_a);
            $weapon_d_b = @round($init_weapon_d_b * $count_d / $init_count_d);

            $strushield_d_b=$strushield_d-$weapon_a_b;
            $strushield_a_b=$strushield_a-$weapon_d_b;

            $runde = $bx+1;

            if ($strushield_a_b<=0)
                $strushield_a_b=0;
            if ($strushield_d_b<=0)
                $strushield_d_b=0;

            $msg.="\n".$runde.": ".$count_a." Einheiten des Angreifes schiessen mit einer St&auml;rke von ".nf($weapon_a_b)." auf den Verteidiger. Der Verteidiger hat danach noch ".nf($strushield_d_b)." Struktur- und Schildpunkte\n" ;
            $msg.="\n".$runde.": ".$count_d." Einheiten des Verteidigers schiessen mit einer St&auml;rke von ".nf($weapon_d_b)." auf den Angreifer. Der Angreifer hat danach noch ".nf($strushield_a_b)." Struktur- und Schildpunkte\n" ;

            $count_a=@ceil($init_count_a*$strushield_a_b/$init_strushield_a);
            $count_d=@ceil($init_count_d*$strushield_d_b/$init_strushield_d);

            if ($count_a<=0) $count_a=0;
            if ($count_d<=0) $count_d=0;

            $strushield_a=$strushield_a_b;
            $strushield_d=$strushield_d_b;

            if ($count_heal_a>0 && $count_a>0)
            {
                $count_heal_a=@ceil($init_count_heal_a*$strushield_a_b/$init_strushield_a);
                $heal_point_a=$count_heal_a*$heal_points_a/$init_count_heal_a;
                $strushield_a+=$heal_point_a;
                if ($strushield_a>$init_strushield_a)
                    $strushield_a=$init_strushield_a;

                $msg.="\n".$runde.": ".$count_heal_a." Einheiten des Angreifes heilen ".nf($heal_point_a)." Struktur- und Schildpunkte. Der Angreifer hat danach wieder ".nf($strushield_a)." Struktur- und Schildpunkte\n" ;
            }

            if ($count_heal_d>0 && $count_d>0)
            {
                $count_heal_d=@ceil($init_count_heal_d*$strushield_d_b/$init_strushield_d);
                $heal_point_d=$count_heal_d*$heal_points_d/$init_count_heal_d;
                $strushield_d+=$heal_point_d;
                if ($strushield_d>$init_strushield_d) $strushield_d=$init_strushield_d;
                $msg.="\n".$runde.": ".$count_heal_d." Einheiten des Verteidigers heilen ".nf($heal_point_d)." Struktur- und Schildpunkte. Der Verteidiger hat danach wieder ".nf($strushield_d)." Struktur- und Schildpunkte\n" ;
            }
            $msg.="\n";
            if ($strushield_a_b<=0 || $strushield_d_b<=0)
                break;
        }

        $msg.= "Der Kampf dauerte $runde Runden!\n\n";



	//
	//Daten nach dem Kampf
	//

        $ships_a_db = array();
        $special_ships_a_db = array();
        $ships_d_db = array();
        $special_ships_d_db = array();
        $defs_db = array();

		//Trümmerfeld
        $wf[0]=0; 	//Titan
        $wf[1]=0;	//Silizium
        $wf[2]=0;	//PVC

		//Gesamtkosten der verlorenen Schiffe (att)
		$lose_fleet_a[0]=0;		//Titan
		$lose_fleet_a[1]=0;		//Silizium
		$lose_fleet_a[2]=0;		//PVC
		$lose_fleet_a[3]=0;		//Tritium
		$lose_fleet_a[4]=0;		//Nahrung

		//Gesamtkosten der verlorenen Schiffe (def)
		$lose_fleet_d[0]=0;		//Titan
		$lose_fleet_d[1]=0;		//Silizium
		$lose_fleet_d[2]=0;		//PVC
		$lose_fleet_d[3]=0;		//Tritium
		$lose_fleet_d[4]=0;		//Nahrung


		//
		//überlebende Schiffe errechnen
		//


		//Schiffe (att)
        foreach ($ships_a as $id=>$data)
        {
            //wenn die def des schiffes <= 0 ist, ist das schiff zerstört (z.b. onefight kampfdrohne)
            if(($data['shield']+$data['structure'])<=0)
            {
                $num = 0;
            }
            else
            {
                $num = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));
            }
            //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
            if($data['cnt']<$num)
            	$num=$data['cnt'];

			$ships_a[$id]['new_cnt']=$num;

            //$ships_a_db[$data['id']]=$num; test

            $rest_ships_a+=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_a[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_a[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_a[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_a[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_a[4]+=round(($data['cnt']-$num)*$data['costs_food']);

        }
        //Spezialschiffe (att)
        foreach ($special_ships_a as $id=>$data)
        {
            //wenn die def des schiffes <= 0 ist, ist das schiff zerstört (z.b. onefight kampfdrohne)
            if(($data['shield']+$data['structure'])<=0)
            {
                $num = 0;
            }
            else
            {
                $num = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));
            }
            //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
            if($data['cnt']<$num)
            	$num=$data['cnt'];

			$special_ships_a[$id]['new_cnt']=$num;

            $special_ships_a_db[$data['id']]=$num;

            $rest_special_ships_a+=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_a[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_a[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_a[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_a[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_a[4]+=round(($data['cnt']-$num)*$data['costs_food']);

        }


        //Schiffe (def)
        foreach ($ships_d as $id=>$data)
        {
        	// Prevent div 0 errors
        	if ($shield_d+$structure_d!=0)
            $num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));
          else
          	$num = 0;

            //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
            if($data['cnt']<$num)
            	$num=$data['cnt'];

			$ships_d[$id]['new_cnt']=$num;

            $ships_d_db[$data['id']]=$num;
            $rest_ships_d+=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_d[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_d[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_d[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_d[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_d[4]+=round(($data['cnt']-$num)*$data['costs_food']);

        }
        //spezialschiffe (def)
        foreach ($special_ships_d as $id=>$data)
        {
        	// Prevent div 0 errors
        	if ($shield_d+$structure_d!=0)        	
            $num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));
					else
						$num = 0;

            //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
            if($data['cnt']<$num)
            	$num=$data['cnt'];

			$special_ships_d[$id]['new_cnt']=$num;

            $special_ships_d_db[$data['id']]=$num;
            $rest_special_ships_d+=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_d[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_d[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_d[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_d[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_d[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }
        foreach ($defs as $id=>$data)
        {
        	// Prevent div 0 errors
        	if ($shield_d+$structure_d!=0)
            $num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));
         	else
         		$num = 0;
         		
            $num = $num + round(($data['cnt']-$num) * DEF_RESTORE_PERCENT);

            //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
            if($data['cnt']<$num)
            	$num=$data['cnt'];

			$defs[$id]['new_cnt']=$num;

            $defs_db[$data['id']]=$num;
            $rest_def_d+=$num;
            $wf[0]+=round(($data['cnt']-$num)*DEF_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*DEF_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*DEF_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_d[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_d[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_d[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_d[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_d[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }

		//Erfahrung für die Spezialschiffe errechnen
        $new_exp_d_init = round((($lose_fleet_a[0] + $lose_fleet_a[1] + $lose_fleet_a[2] + $lose_fleet_a[3] + $lose_fleet_a[4]) / 100000));
		$new_exp_a_init = round((($lose_fleet_d[0] + $lose_fleet_d[1] + $lose_fleet_d[2] + $lose_fleet_d[3] + $lose_fleet_d[4]) / 100000));


		//Das entstandene Trümmerfeld erstellen/hochladen
        dbquery("
			UPDATE
				".$db_table['planets']."
			SET
				planet_wf_metal=planet_wf_metal+'".abs($wf[0])."',
				planet_wf_crystal=planet_wf_crystal+'".abs($wf[1])."',
				planet_wf_plastic=planet_wf_plastic+'".abs($wf[2])."'
			WHERE
				planet_id='".$planet_id."';
		");

        //Löscht die flotte und setzt alle schiffe & def zurück (es wird wieder eingetragen!)
        dbquery("
			UPDATE
				".$db_table['deflist']."
			SET
				deflist_count='0'
			WHERE
				deflist_planet_id='".$planet_id."';
		");
        dbquery("
			UPDATE
				".$db_table['shiplist']."
			SET
				shiplist_count='0'
			WHERE
				shiplist_planet_id='".$planet_id."';
		");
        dbquery("
			DELETE FROM
				".$db_table['fleet_ships']."
			WHERE
				fs_fleet_id='".$fleet_id."';
		");


	//
	//Auswertung
	//

		//
		//Der Angreifer hat gewonnen!
		//

    if ($count_d==0 && $count_a>0)
    {
      $return_v = 1;

      $msg.= "Der Angreifer hat den Kampf gewonnen!\n\n";

        //Stellt die Schiffe wieder her (att)
			foreach ($ships_a as $id=>$data)
			{
				if($data['new_cnt']>0)
				{
                    dbquery("
						INSERT INTO
							".$db_table['fleet_ships']."
							(
								fs_fleet_id,
								fs_ship_id,
								fs_ship_cnt
							)
						VALUES
							(
								'".$fleet_id."',
								'".$data['id']."',
								'".$data['new_cnt']."'
							);
					");
				}
			}

			//Stellt die Spezialschiffe wieder her (att)
      foreach ($special_ships_a as $id=>$data)
      {
				if($data['new_cnt']>0)
				{
					$exp=$data['ship_exp']+$new_exp_a_init;
			
			              dbquery("
			              	INSERT INTO
			              		".$db_table['fleet_ships']."
			                  	(
								fs_fleet_id,
								fs_ship_id,
								fs_ship_cnt,
								fs_special_ship,
								fs_special_ship_level,
								fs_special_ship_exp,
								fs_special_ship_bonus_weapon,
								fs_special_ship_bonus_structure,
								fs_special_ship_bonus_shield,
								fs_special_ship_bonus_heal,
								fs_special_ship_bonus_capacity,
								fs_special_ship_bonus_speed,
								fs_special_ship_bonus_pilots,
								fs_special_ship_bonus_tarn,
								fs_special_ship_bonus_antrax,
								fs_special_ship_bonus_forsteal,
								fs_special_ship_bonus_build_destroy,
								fs_special_ship_bonus_antrax_food,
								fs_special_ship_bonus_deactivade
							)
			              	VALUES
							(
								'".$fleet_id."',
								'".$data['id']."',
								'".$data['new_cnt']."',
								'1',
								'".$data['ship_level']."',
								'".$exp."',
								'".$data['ship_bonus_weapon']."',
								'".$data['ship_bonus_structure']."',
								'".$data['ship_bonus_shield']."',
								'".$data['ship_bonus_heal']."',
								'".$data['ship_bonus_capacity']."',
								'".$data['ship_bonus_speed']."',
								'".$data['ship_bonus_pilots']."',
								'".$data['ship_bonus_tarn']."',
								'".$data['ship_bonus_antrax']."',
								'".$data['ship_bonus_forsteal']."',
								'".$data['ship_bonus_destroy']."',
								'".$data['ship_bonus_antrax_food']."',
								'".$data['ship_bonus_deactivade']."'
							);
					");
				}
      }

      //Stellt die Verteidigung wieder her (def)
      foreach ($defs as $id=>$data)
      {
      	if($data['new_cnt']>0)
      	{
        	if (mysql_num_rows(dbquery("
						SELECT
							deflist_def_id
						FROM
							".$db_table['deflist']."
						WHERE
							deflist_planet_id='".$planet_id."'
							AND deflist_def_id='".$data['id']."';
					"))>0)
              {
                dbquery("
								UPDATE
									".$db_table['deflist']."
								SET
									deflist_count='".$data['new_cnt']."'
								WHERE
									deflist_planet_id='".$planet_id."'
									AND deflist_def_id='".$data['id']."';");
              }
              else
              {
                  dbquery("
				INSERT INTO
					".$db_table['deflist']."
					(
						deflist_user_id,
						deflist_planet_id,
						deflist_def_id,
						deflist_count
					)
				VALUES
					(
						'".$user_d_id."',
						'".$planet_id."',
						'".$data['id']."',
						'".$data['new_cnt']."'
					);
			");
              }
        }
      }

      //setzt die werte der spezialschiffe zurück (def)
      foreach ($special_ships_d as $id=>$data)
      {
         dbquery("
				UPDATE
					".$db_table['shiplist']."
				SET
					shiplist_special_ship_level='0',
					shiplist_special_ship_exp='0',
					shiplist_special_ship_bonus_weapon='0',
					shiplist_special_ship_bonus_structure='0',
					shiplist_special_ship_bonus_shield='0',
					shiplist_special_ship_bonus_heal='0',
					shiplist_special_ship_bonus_capacity='0',
					shiplist_special_ship_bonus_speed='0',
					shiplist_special_ship_bonus_pilots='0',
					shiplist_special_ship_bonus_tarn='0',
					shiplist_special_ship_bonus_antrax='0',
					shiplist_special_ship_bonus_forsteal='0',
					shiplist_special_ship_bonus_build_destroy='0',
					shiplist_special_ship_bonus_antrax_food='0',
					shiplist_special_ship_bonus_deactivade='0'
				WHERE
					shiplist_user_id='".$user_d_id."'
					AND shiplist_ship_id='".$data['id']."';");
      }


			//Kapazität rechnen
            $capa=0;
            $special_ship_bonus_capacity=1;

            //Kapazitätsbonus von den Spezialschiffen rechnen
            foreach ($special_ships_a as $id=>$data)
            {
							$special_ship_bonus_capacity+=$data['bonus_capacity']*$data['ship_bonus_capacity'];
            }

            //Kapazität der überlebenden Schiffe rechnen
            $rfrar=mysql_fetch_row(dbquery("
				SELECT
					SUM(s.ship_capacity*fs.fs_ship_cnt) AS capa
				FROM
					(
						".$db_table['fleet_ships']." AS fs 
						INNER JOIN 
						".$db_table['ships']." AS s 
						ON fs.fs_ship_id = s.ship_id
					)
					INNER JOIN 
					".$db_table['fleet']." AS f 
					ON fs.fs_fleet_id = f.fleet_id
					AND f.fleet_id='".$fleet_id."'
				GROUP BY
					f.fleet_id;
			"));
            $capa=$rfrar[0];
            $capa=$capa*$special_ship_bonus_capacity;
            $capa=$fleetarr['fleet_capacity'];

            $res_raid_factor=0.5;
            if ($dont_steal!=1 && $ship_steal!="")
                $res_raid_factor=0.75;

			//Rohstoffe vom gegnerischen Planeten abfragen
            $rparr = mysql_fetch_array(dbquery("
				SELECT
					planet_res_metal,
					planet_res_crystal,
					planet_res_plastic,
					planet_res_fuel,
					planet_res_food
				FROM
					".$db_table['planets']."
				WHERE
					planet_id='".$planet_id."';
			"));

            $raid_r[0]=$rparr['planet_res_metal']*$res_raid_factor;
            $raid_r[1]=$rparr['planet_res_crystal']*$res_raid_factor;
            $raid_r[2]=$rparr['planet_res_plastic']*$res_raid_factor;
            $raid_r[3]=$rparr['planet_res_fuel']*$res_raid_factor;
            $raid_r[4]=$rparr['planet_res_food']*$res_raid_factor;

            for ($rcnt=0;$rcnt<5;$rcnt++)
            {
                if ($capa<=array_sum($raid_r))
                    $raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]*$capa/array_sum($raid_r));
                else
                    $raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]);
            }

            $sql = "
				UPDATE
					".$db_table['fleet']."
				SET
					fleet_res_metal=fleet_res_metal+'".$raid_r_to_ship[0]."',
					fleet_res_crystal=fleet_res_crystal+'".$raid_r_to_ship[1]."',
					fleet_res_plastic=fleet_res_plastic+'".$raid_r_to_ship[2]."',
					fleet_res_fuel=fleet_res_fuel+'".$raid_r_to_ship[3]."',
					fleet_res_food=fleet_res_food+'".$raid_r_to_ship[4]."'
				WHERE
					fleet_id='".$fleet_id."';";
			
			dbquery($sql);

            dbquery("
				UPDATE
					".$db_table['planets']."
				SET
					planet_res_metal=planet_res_metal-'".$raid_r_to_ship[0]."',
					planet_res_crystal=planet_res_crystal-'".$raid_r_to_ship[1]."',
					planet_res_plastic=planet_res_plastic-'".$raid_r_to_ship[2]."',
					planet_res_fuel=planet_res_fuel-'".$raid_r_to_ship[3]."',
					planet_res_food=planet_res_food-'".$raid_r_to_ship[4]."'
				WHERE
					planet_id='".$planet_id."';
			");

            //Erbeutete Rohstoffsumme speichern
            $res_sum=array_sum($raid_r_to_ship);
            dbquery("
				UPDATE
					".$db_table['users']."
				SET
					user_res_from_raid=user_res_from_raid+'".$res_sum."'
				WHERE
					user_id='".$user_a_id."';
			");

            $msg.= "[b]BEUTE:[/b]\n";
            $msg.= "".RES_METAL.": ".nf($raid_r_to_ship[0])."\n";
            $msg.= "".RES_CRYSTAL.": ".nf($raid_r_to_ship[1])."\n";
            $msg.= "".RES_PLASTIC.": ".nf($raid_r_to_ship[2])."\n";
            $msg.= "".RES_FUEL.": ".nf($raid_r_to_ship[3])."\n";
            $msg.= "".RES_FOOD.": ".nf($raid_r_to_ship[4])."\n";
            $msg.= "\n\n";
    }

    //
    //Der Verteidiger hat gewonnen
    //
    elseif ($count_a==0 && $count_d>0)
    {
        $return_v = 2;
        $msg.= "Der Verteidiger hat den Kampf gewonnen!\n\n";

				//löscht die angreiffende flotte
        dbquery("
				DELETE FROM
					".$db_table['fleet']."
				WHERE
					fleet_id='".$fleet_id."';");

        //setzt die werte der spezialschiffe zurück (att)
        foreach ($special_ships_a as $id=>$data)
        {
            dbquery("
			UPDATE
				".$db_table['shiplist']."
			SET
				shiplist_special_ship_level='0',
				shiplist_special_ship_exp='0',
				shiplist_special_ship_bonus_weapon='0',
				shiplist_special_ship_bonus_structure='0',
				shiplist_special_ship_bonus_shield='0',
				shiplist_special_ship_bonus_heal='0',
				shiplist_special_ship_bonus_capacity='0',
				shiplist_special_ship_bonus_speed='0',
				shiplist_special_ship_bonus_pilots='0',
				shiplist_special_ship_bonus_tarn='0',
				shiplist_special_ship_bonus_antrax='0',
				shiplist_special_ship_bonus_forsteal='0',
				shiplist_special_ship_bonus_build_destroy='0',
				shiplist_special_ship_bonus_antrax_food='0',
				shiplist_special_ship_bonus_deactivade='0'
			WHERE
				shiplist_user_id='".$user_a_id."'
				AND shiplist_ship_id='".$data['id']."';
		");
        }


        //Stellt die Schiffe wieder her (def)
				foreach ($ships_d as $id=>$data)
				{
					if($data['new_cnt']>0)
					{
			                if (mysql_num_rows(dbquery("
							SELECT
								shiplist_ship_id
							FROM
								".$db_table['shiplist']."
							WHERE
								shiplist_planet_id='".$planet_id."'
								AND shiplist_ship_id='".$data['id']."';
						"))>0)
			                {
			                    dbquery("
								UPDATE
									".$db_table['shiplist']."
								SET
									shiplist_count='".$data['new_cnt']."'
								WHERE
									shiplist_planet_id='".$planet_id."'
									AND shiplist_ship_id='".$data['id']."'
							;");
			                }
			                else
			                {
			                    dbquery("
								INSERT INTO
									".$db_table['shiplist']."
									(
										shiplist_user_id,
										shiplist_planet_id,
										shiplist_ship_id,
										shiplist_count
									)
								VALUES
									(
										'".$user_d_id."',
										'".$planet_id."',
										'".$data['id']."',
										'".$data['new_cnt']."'
									);
							");
			                }
					}
				}

				//Stellt die Spezialschiffe wieder her (def)
        foreach ($special_ships_d as $id=>$data)
        {
		if($data['new_cnt']>0)
		{
			$exp=$data['ship_exp']+$new_exp_d_init;

                if (mysql_num_rows(dbquery("
				SELECT
					shiplist_ship_id
				FROM
					".$db_table['shiplist']."
				WHERE
					shiplist_planet_id='".$planet_id."'
					AND shiplist_ship_id='".$data['id']."';
			"))>0)
                {
                    dbquery("
					UPDATE
						".$db_table['shiplist']."
					SET
						shiplist_count='".$data['new_cnt']."',
						shiplist_special_ship='1',
						shiplist_special_ship_level='".$data['ship_level']."',
						shiplist_special_ship_exp='".$exp."',
						shiplist_special_ship_bonus_weapon='".$data['ship_bonus_weapon']."',
						shiplist_special_ship_bonus_structure='".$data['ship_bonus_structure']."',
						shiplist_special_ship_bonus_shield='".$data['ship_bonus_shield']."',
						shiplist_special_ship_bonus_heal='".$data['ship_bonus_heal']."',
						shiplist_special_ship_bonus_capacity='".$data['ship_bonus_capacity']."',
						shiplist_special_ship_bonus_speed='".$data['ship_bonus_speed']."',
						shiplist_special_ship_bonus_pilots='".$data['ship_bonus_pilots']."',
						shiplist_special_ship_bonus_tarn='".$data['ship_bonus_tarn']."',
						shiplist_special_ship_bonus_antrax='".$data['ship_bonus_antrax']."',
						shiplist_special_ship_bonus_forsteal='".$data['ship_bonus_forsteal']."',
						shiplist_special_ship_bonus_build_destroy='".$data['ship_bonus_destroy']."',
						shiplist_special_ship_bonus_antrax_food='".$data['ship_bonus_antrax_food']."',
						shiplist_special_ship_bonus_deactivade='".$data['ship_bonus_deactivade']."'
					WHERE
						shiplist_planet_id='".$planet_id."'
						AND shiplist_ship_id='".$data['id']."';
				");
                }
                else
                {
                    dbquery("
					INSERT INTO
						".$db_table['shiplist']."
						(
							shiplist_user_id,
							shiplist_planet_id,
							shiplist_ship_id,
							shiplist_count,
							shiplist_special_ship,
							shiplist_special_ship_level,
							shiplist_special_ship_exp,
							shiplist_special_ship_bonus_weapon,
							shiplist_special_ship_bonus_structure,
							shiplist_special_ship_bonus_shield,
							shiplist_special_ship_bonus_heal,
							shiplist_special_ship_bonus_capacity,
							shiplist_special_ship_bonus_speed,
							shiplist_special_ship_bonus_pilots,
							shiplist_special_ship_bonus_tarn,
							shiplist_special_ship_bonus_antrax,
							shiplist_special_ship_bonus_forsteal,
							shiplist_special_ship_bonus_build_destroy,
							shiplist_special_ship_bonus_antrax_food,
							shiplist_special_ship_bonus_deactivade
						)
					VALUES
						(
							'".$user_d_id."',
							'".$planet_id."',
							'".$data['id']."',
							'".$data['new_cnt']."',
							'1',
							'".$data['ship_level']."',
							'".$exp."',
							'".$data['ship_bonus_weapon']."',
							'".$data['ship_bonus_structure']."',
							'".$data['ship_bonus_shield']."',
							'".$data['ship_bonus_heal']."',
							'".$data['ship_bonus_capacity']."',
							'".$data['ship_bonus_speed']."',
							'".$data['ship_bonus_pilots']."',
							'".$data['ship_bonus_tarn']."',
							'".$data['ship_bonus_antrax']."',
							'".$data['ship_bonus_forsteal']."',
							'".$data['ship_bonus_destroy']."',
							'".$data['ship_bonus_antrax_food']."',
							'".$data['ship_bonus_deactivade']."'
						);
				");
                }
		}
        }

        //Stellt die Verteidigung wieder her (def)
        foreach ($defs as $id=>$data)
        {
        	if($data['new_cnt']>0)
        	{
                if (mysql_num_rows(dbquery("
				SELECT
					deflist_def_id
				FROM
					".$db_table['deflist']."
				WHERE
					deflist_planet_id='".$planet_id."'
					AND deflist_def_id='".$data['id']."';
			"))>0)
                {
                    dbquery("
					UPDATE
						".$db_table['deflist']."
					SET
						deflist_count='".$data['new_cnt']."'
					WHERE
						deflist_planet_id='".$planet_id."'
						AND deflist_def_id='".$data['id']."';
				");
                }
                else
                {
                    dbquery("
					INSERT INTO
						".$db_table['deflist']."
						(
							deflist_user_id,
							deflist_planet_id,
							deflist_def_id,
							deflist_count
						)
					VALUES
						(
							'".$user_d_id."',
							'".$planet_id."',
							'".$data['id']."',
							'".$data['new_cnt']."'
						);
				");
                }
            }
        }

    }

    //
    //Der Kampf endete unentschieden
    //
    else
    {

        //
        //	Unentschieden, beide Flotten wurden zerstört
        //
        if ($count_a==0 && $count_d==0)
        {
        		$return_v = 3;
            $msg.= "Der Kampf endete unentschieden, da sowohl die Einheiten des Angreifes als auch die Einheiten des Verteidigers alle zerstört wurden!\n\n";
            
            //löscht die angreiffende flotte
            dbquery("
						DELETE FROM
							".$db_table['fleet']."
						WHERE
							fleet_id='$fleet_id';");

            //setzt die werte der spezialschiffe zurück (att)
            foreach ($special_ships_a as $id=>$data)
            {
                dbquery("
								UPDATE
									".$db_table['shiplist']."
								SET
									shiplist_special_ship_level='0',
									shiplist_special_ship_exp='0',
									shiplist_special_ship_bonus_weapon='0',
									shiplist_special_ship_bonus_structure='0',
									shiplist_special_ship_bonus_shield='0',
									shiplist_special_ship_bonus_heal='0',
									shiplist_special_ship_bonus_capacity='0',
									shiplist_special_ship_bonus_speed='0',
									shiplist_special_ship_bonus_pilots='0',
									shiplist_special_ship_bonus_tarn='0',
									shiplist_special_ship_bonus_antrax='0',
									shiplist_special_ship_bonus_forsteal='0',
									shiplist_special_ship_bonus_build_destroy='0',
									shiplist_special_ship_bonus_antrax_food='0',
									shiplist_special_ship_bonus_deactivade='0'
								WHERE
									shiplist_user_id='".$user_a_id."'
									AND shiplist_ship_id='".$data['id']."';");
            }

            //setzt die werte der spezialschiffe zurück (def)
            foreach ($special_ships_d as $id=>$data)
            {
                dbquery("
								UPDATE
									".$db_table['shiplist']."
								SET
									shiplist_special_ship_level='0',
									shiplist_special_ship_exp='0',
									shiplist_special_ship_bonus_weapon='0',
									shiplist_special_ship_bonus_structure='0',
									shiplist_special_ship_bonus_shield='0',
									shiplist_special_ship_bonus_heal='0',
									shiplist_special_ship_bonus_capacity='0',
									shiplist_special_ship_bonus_speed='0',
									shiplist_special_ship_bonus_pilots='0',
									shiplist_special_ship_bonus_tarn='0',
									shiplist_special_ship_bonus_antrax='0',
									shiplist_special_ship_bonus_forsteal='0',
									shiplist_special_ship_bonus_build_destroy='0',
									shiplist_special_ship_bonus_antrax_food='0',
									shiplist_special_ship_bonus_deactivade='0'
								WHERE
									shiplist_user_id='".$user_d_id."'
									AND shiplist_ship_id='".$data['id']."';");
            }

            //Stellt die Verteidigung wieder her (def)
            foreach ($defs as $id=>$data)
            {
                if($data['new_cnt']>0)
                {
                    if (mysql_num_rows(dbquery("
					SELECT
						deflist_def_id
					FROM
						".$db_table['deflist']."
					WHERE
						deflist_planet_id='".$planet_id."'
						AND deflist_def_id='".$data['id']."';
				"))>0)
                    {
                        dbquery("
						UPDATE
							".$db_table['deflist']."
						SET
							deflist_count='".$data['new_cnt']."'
						WHERE
							deflist_planet_id='".$planet_id."'
							AND deflist_def_id='".$data['id']."';
					");
                    }
                    else
                    {
                        dbquery("
						INSERT INTO
							".$db_table['deflist']."
							(
								deflist_user_id,
								deflist_planet_id,
								deflist_def_id,
								deflist_count
							)
						VALUES
							(
								'".$user_d_id."',
								'".$planet_id."',
								'".$data['id']."',
								'".$data['new_cnt']."'
							);
					");
                    }
                }
            }
        }

        //
        //	Unentschieden, beide Flotten haben überlebt
        //
        else
        {
      		$return_v = 4;

          $msg.= "Der Kampf endete unentschieden und die Flotten zogen sich zurück!\n\n";

          //Stellt die Schiffe wieder her (att)
          foreach ($ships_a as $id=>$data)
          {
              if($data['new_cnt']>0)
              {
                  dbquery("
									INSERT INTO
										".$db_table['fleet_ships']."
										(
											fs_fleet_id,
											fs_ship_id,
											fs_ship_cnt
										)
									VALUES
										(
											'".$fleet_id."',
											'".$data['id']."',
											'".$data['new_cnt']."'
										);");
              }
          }

          //Stellt die Spezialschiffe wieder her (att)
          foreach ($special_ships_a as $id=>$data)
          {
            if($data['new_cnt']>0)
            {
                $exp=$data['ship_exp']+$new_exp_a_init;

                dbquery("
                INSERT INTO
                ".$db_table['fleet_ships']."
                (
									fs_fleet_id,
									fs_ship_id,
									fs_ship_cnt,
									fs_special_ship,
									fs_special_ship_level,
									fs_special_ship_exp,
									fs_special_ship_bonus_weapon,
									fs_special_ship_bonus_structure,
									fs_special_ship_bonus_shield,
									fs_special_ship_bonus_heal,
									fs_special_ship_bonus_capacity,
									fs_special_ship_bonus_speed,
									fs_special_ship_bonus_pilots,
									fs_special_ship_bonus_tarn,
									fs_special_ship_bonus_antrax,
									fs_special_ship_bonus_forsteal,
									fs_special_ship_bonus_build_destroy,
									fs_special_ship_bonus_antrax_food,
									fs_special_ship_bonus_deactivade
								)
					      VALUES
					      (
									'".$fleet_id."',
									'".$data['id']."',
									'".$data['new_cnt']."',
									'1',
									'".$data['ship_level']."',
									'".$exp."',
									'".$data['ship_bonus_weapon']."',
									'".$data['ship_bonus_structure']."',
									'".$data['ship_bonus_shield']."',
									'".$data['ship_bonus_heal']."',
									'".$data['ship_bonus_capacity']."',
									'".$data['ship_bonus_speed']."',
									'".$data['ship_bonus_pilots']."',
									'".$data['ship_bonus_tarn']."',
									'".$data['ship_bonus_antrax']."',
									'".$data['ship_bonus_forsteal']."',
									'".$data['ship_bonus_destroy']."',
									'".$data['ship_bonus_antrax_food']."',
									'".$data['ship_bonus_deactivade']."'
								);");
            }
          }

          //Stellt die Schiffe wieder her (def)
          foreach ($ships_d as $id=>$data)
          {
            if($data['new_cnt']>0)
            {
                if (
                  mysql_num_rows(dbquery("
									SELECT
										shiplist_ship_id
									FROM
										".$db_table['shiplist']."
									WHERE
										shiplist_planet_id='".$planet_id."'
										AND shiplist_ship_id='".$data['id']."';")
									)>0)
                {
                    dbquery("
										UPDATE
											".$db_table['shiplist']."
										SET
											shiplist_count='".$data['new_cnt']."'
										WHERE
											shiplist_planet_id='".$planet_id."'
											AND shiplist_ship_id='".$data['id']."';");
                }
                else
                {
                    dbquery("
										INSERT INTO
										".$db_table['shiplist']."
										(
											shiplist_user_id,
											shiplist_planet_id,
											shiplist_ship_id,
											shiplist_count
										)
										VALUES
										(
											'".$user_d_id."',
											'".$planet_id."',
											'".$data['id']."',
											'".$data['new_cnt']."'
										);");
                }
            }
          }

          //Stellt die Spezialschiffe wieder her (def)
          foreach ($special_ships_d as $id=>$data)
          {
              if($data['new_cnt']>0)
              {
                  $exp=$data['ship_exp']+$new_exp_d_init;

                  if (
                  		mysql_num_rows(dbquery("
											SELECT
												shiplist_ship_id
											FROM
												".$db_table['shiplist']."
											WHERE
												shiplist_planet_id='".$planet_id."'
												AND shiplist_ship_id='".$data['id']."'
												AND shiplist_special_ship=1;")
											)>0)
                  {
                    dbquery("
										UPDATE
											".$db_table['shiplist']."
										SET
											shiplist_count='".$data['new_cnt']."',
											shiplist_special_ship='1',
											shiplist_special_ship_level='".$data['ship_level']."',
											shiplist_special_ship_exp='".$exp."',
											shiplist_special_ship_bonus_weapon='".$data['ship_bonus_weapon']."',
											shiplist_special_ship_bonus_structure='".$data['ship_bonus_structure']."',
											shiplist_special_ship_bonus_shield='".$data['ship_bonus_shield']."',
											shiplist_special_ship_bonus_heal='".$data['ship_bonus_heal']."',
											shiplist_special_ship_bonus_capacity='".$data['ship_bonus_capacity']."',
											shiplist_special_ship_bonus_speed='".$data['ship_bonus_speed']."',
											shiplist_special_ship_bonus_pilots='".$data['ship_bonus_pilots']."',
											shiplist_special_ship_bonus_tarn='".$data['ship_bonus_tarn']."',
											shiplist_special_ship_bonus_antrax='".$data['ship_bonus_antrax']."',
											shiplist_special_ship_bonus_forsteal='".$data['ship_bonus_forsteal']."',
											shiplist_special_ship_bonus_build_destroy='".$data['ship_bonus_destroy']."',
											shiplist_special_ship_bonus_antrax_food='".$data['ship_bonus_antrax_food']."',
											shiplist_special_ship_bonus_deactivade='".$data['ship_bonus_deactivade']."'
										WHERE
											shiplist_planet_id='".$planet_id."'
											AND shiplist_ship_id='".$data['id']."';");
                  }
                  else
                  {
                    dbquery("
										INSERT INTO
										".$db_table['shiplist']."
										(
											shiplist_user_id,
											shiplist_planet_id,
											shiplist_ship_id,
											shiplist_count,
											shiplist_special_ship,
											shiplist_special_ship_level,
											shiplist_special_ship_exp,
											shiplist_special_ship_bonus_weapon,
											shiplist_special_ship_bonus_structure,
											shiplist_special_ship_bonus_shield,
											shiplist_special_ship_bonus_heal,
											shiplist_special_ship_bonus_capacity,
											shiplist_special_ship_bonus_speed,
											shiplist_special_ship_bonus_pilots,
											shiplist_special_ship_bonus_tarn,
											shiplist_special_ship_bonus_antrax,
											shiplist_special_ship_bonus_forsteal,
											shiplist_special_ship_bonus_build_destroy,
											shiplist_special_ship_bonus_antrax_food,
											shiplist_special_ship_bonus_deactivade
										)
										VALUES
										(
											'".$user_d_id."',
											'".$planet_id."',
											'".$data['id']."',
											'".$data['new_cnt']."',
											'1',
											'".$data['ship_level']."',
											'".$exp."',
											'".$data['ship_bonus_weapon']."',
											'".$data['ship_bonus_structure']."',
											'".$data['ship_bonus_shield']."',
											'".$data['ship_bonus_heal']."',
											'".$data['ship_bonus_capacity']."',
											'".$data['ship_bonus_speed']."',
											'".$data['ship_bonus_pilots']."',
											'".$data['ship_bonus_tarn']."',
											'".$data['ship_bonus_antrax']."',
											'".$data['ship_bonus_forsteal']."',
											'".$data['ship_bonus_destroy']."',
											'".$data['ship_bonus_antrax_food']."',
											'".$data['ship_bonus_deactivade']."'
										);");
                  }
              }
          }

          //Stellt die Verteidigung wieder her (def)
          foreach ($defs as $id=>$data)
          {
              if($data['new_cnt']>0)
              {
                  if (
                  		mysql_num_rows(dbquery("
											SELECT
												deflist_def_id
											FROM
												".$db_table['deflist']."
											WHERE
												deflist_planet_id='".$planet_id."'
												AND deflist_def_id='".$data['id']."';")
										)>0)
						        {
							        dbquery("
											UPDATE
												".$db_table['deflist']."
											SET
												deflist_count='".$data['new_cnt']."'
											WHERE
												deflist_planet_id='".$planet_id."'
												AND deflist_def_id='".$data['id']."';");
                    }
                    else
                    {
                      dbquery("
											INSERT INTO
											".$db_table['deflist']."
											(
												deflist_user_id,
												deflist_planet_id,
												deflist_def_id,
												deflist_count
											)
											VALUES
											(
												'".$user_d_id."',
												'".$planet_id."',
												'".$data['id']."',
												'".$data['new_cnt']."'
											);");
                  }
              }
          }
        }
    }

    $msg.= "[b]TR&Uuml;MMERFELD:[/b]\n";
    $msg.= "".RES_METAL.": ".nf(abs($wf[0]))."\n";
    $msg.= "".RES_CRYSTAL.": ".nf(abs($wf[1]))."\n";
    $msg.= "".RES_PLASTIC.": ".nf(abs($wf[2]))."\n";
    $msg.= "\n\n";

    $msg.= "[b]Zustand nach dem Kampf:[/b]\n\n";
    $msg.= "[b]ANGREIFENDE FLOTTE:[/b]\n";


	//
	//Auflistung der Schiffe nach dem Kampf
	//
		$fleet_rest_a=0;
		//Spezialschiffe (att)
        if (count($special_ships_a)>0)
        {
        	$fleet_rest_a=1;
            foreach ($special_ships_a as $id=>$data)
            {
                $msg.= "[b]".$data['name']."[/b] ".$data['new_cnt']."\n";
            }
        }
		//Schiffe (att)
        if (count($ships_a)>0)
        {
        	$fleet_rest_a=1;

            foreach ($ships_a as $id=>$data)
            {
                $msg.= "".$data['name']." ".$data['new_cnt']."\n";
            }
        }
        //Zeigt die Gewonnenen EXP der Spezialschiffe (att)
        if (count($special_ships_a_db)>0 && $new_exp_a_init>0)
        {
				//$msg.= "\nDie Spezialschiffe von [B]".get_user_nick($user_a_id)."[/B] erhalten ".nf($new_exp_a_init)." EXP!\n\n";
				$msg.= "\nGewonnene EXP: ".nf($new_exp_a_init)."\n\n";
		}



        $msg.= "\n[b]VERTEIDIGENDE FLOTTE:[/b]\n";

        //Spezialschiffe (def)
		$fleet_rest_d=0;
        if (($special_ships_d)>0)
        {
        	$fleet_rest_d=1;
            foreach ($special_ships_d as $id=>$data)
            {
                $msg.= "[b]".$data['name']."[/b] ".$data['new_cnt']."\n";
            }
        }
        //Schiffe (def)
        if (($ships_d)>0)
        {
        	$fleet_rest_d=1;
            foreach ($ships_d as $id=>$data)
            {
                $msg.= "".$data['name']." ".$data['new_cnt']."\n";
            }
        }
        //Zeigt die Gewonnenen EXP der Spezialschiffe (def)
        if (count($special_ships_d_db)>0 && $new_exp_d_init>0)
        {
			$msg.= "\nGewonnene EXP: ".nf($new_exp_d_init)."\n\n";
		}

        $msg.= "\n[b]VERTEIDIGUNG:[/b]\n";
        //Verteidigung (def)
        if (count($defs)>0)
        {
            foreach ($defs as $id=>$data)
            {
            	$num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));
                $numr = round(($data['cnt']-$num) * DEF_RESTORE_PERCENT);
                $msg.= "".$data['name']." ".$num." (+".$numr.")\n";
            }
        }
        else
        {
            $msg.= "[i]Nichts vorhanden![/i]\n";
        }
        $perc = DEF_RESTORE_PERCENT*100;
        $msg.="\n$perc% der Verteidigungsanlagen werden repariert!";

        //Log schreiben
        dbquery("
			INSERT INTO
				".$db_table['logs_battle']."
				(
					logs_battle_user1_id,
					logs_battle_user2_id,
					logs_battle_user1_alliance_id,
					logs_battle_user1_alliance_tag,
					logs_battle_user1_alliance_name,
					logs_battle_user2_alliance_id,
					logs_battle_user2_alliance_tag,
					logs_battle_user2_alliance_name,
					logs_battle_alliances_have_war,
					logs_battle_planet_id,
					logs_battle_fleet_action,
					logs_battle_result,
					logs_battle_user1_ships_cnt,
					logs_battle_user2_ships_cnt,
					logs_battle_user2_defs_cnt,
					logs_battle_user1_weapon,
					logs_battle_user1_shield,
					logs_battle_user1_structure,
					logs_battle_user1_weapon_bonus,
					logs_battle_user1_shield_bonus,
					logs_battle_user1_structure_bonus,
					logs_battle_user2_weapon,
					logs_battle_user2_shield,
					logs_battle_user2_structure,
					logs_battle_user2_weapon_bonus,
					logs_battle_user2_shield_bonus,
					logs_battle_user2_structure_bonus,
					logs_battle_user1_win_exp,
					logs_battle_user2_win_exp,
					logs_battle_user1_win_metal,
					logs_battle_user1_win_crystal,
					logs_battle_user1_win_pvc,
					logs_battle_user1_win_tritium,
					logs_battle_user1_win_food,
					logs_battle_tf_metal,
					logs_battle_tf_crystal,
					logs_battle_tf_pvc,
					logs_battle_fight,
					logs_battle_time,
					logs_battle_fleet_landtime
				)
			VALUES
				(
					'".$user_a_id."',
					'".$user_d_id."',
					'".$alliance_info_a['alliance_id']."',
					'".$alliance_info_a['alliance_tag']."',
					'".$alliance_info_a['alliance_name']."',
					'".$alliance_info_d['alliance_id']."',
					'".$alliance_info_d['alliance_tag']."',
					'".$alliance_info_d['alliance_name']."',
					'".$alliances_have_war."',
					'".$planet_id."',
					'".$fleetarr['fleet_action']."',
					'".$return_v."',
					'".$init_count_a."',
					'".$init_count_d."',
					'".$count_dd."',
					'".$init_weapon_a_b."',
					'".$shield_a."',
					'".$structure_a."',
					'".($weapon_tech_a*100)."',
					'".($shield_tech_a*100)."',
					'".($structure_tech_a*100)."',
					'".$init_weapon_d_b."',
					'".$shield_d."',
					'".$structure_d."',
					'".($weapon_tech_d*100)."',
					'".($shield_tech_d*100)."',
					'".($structure_tech_d*100)."',
					'".$new_exp_a_init."',
					'".$new_exp_d_init."',
					'".$raid_r_to_ship[0]."',
					'".$raid_r_to_ship[1]."',
					'".$raid_r_to_ship[2]."',
					'".$raid_r_to_ship[3]."',
					'".$raid_r_to_ship[4]."',
					'".$wf[0]."',
					'".$wf[1]."',
					'".$wf[2]."',
					'".$msg."',
					'".time()."',
					'".$fleetarr['fleet_landtime']."');"
				);


				switch ($return_v)
				{
					case 1:	//angreifer hat gewonnen
						$bstat = "Gewonnen";
						$bstat2 = "Verloren";
						$return_fleet=true;
						break;
					case 2:	//agreifer hat verloren
						$bstat = "Verloren";
						$bstat2 = "Gewonnen";
						$return_fleet=false;
						break;
					case 3:	//beide flotten sind kaputt
						$bstat = "Unentschieden";
						$bstat2 = "Unentschieden";
						$return_fleet=false;
						break;
					case 4: //beide flotten haben überlebt
						$bstat = "Unentschieden";
						$bstat2 = "Unentschieden";
						$return_fleet=true;
						break;
				}			


        return array($return_v,$msg,$bstat,$bstat2,$return_fleet);

}


















//
// Kampfsimulator
//


function battle_simulation($ships_a,$special_ships_a,$ships_d,$special_ships_d,$tech_a,$tech_d,$def_d)
{
		global $db_table,$conf;

        // BEGIN SKRIPT //


        $structure_a=0;
        $shield_a=0;
        $weapon_a=0;
        $count_a=0;
        $count_heal_a=0;


        $msg = "KAMPFBERICHT<br><br>";
        $msg.= "<b>Angreifer:</b> der imperator<br>";
        $msg.= "<b>Verteidiger:</b> das arme arschloch<br><br>";
        $msg.= "<b>ANGREIFENDE FLOTTE:</b><br>";



        //
        //Angreiffende Flotte Spezial Schiffe
        //
        for ($j=0; $special_ships_a[$j]!=""; $j++)
        {
        	$msg.= "<b>".$special_ships_a[$j]['name']." (".$special_ships_a[$j]['level'].")</b> ".nf($special_ships_a[$j]['cnt'])."<br>";

            $structure_a+=$special_ships_a[$j]['structure']*$special_ships_a[$j]['cnt'];
            $shield_a+=$special_ships_a[$j]['shield']*$special_ships_a[$j]['cnt'];
            $weapon_a+=$special_ships_a[$j]['weapon']*$special_ships_a[$j]['cnt'];
        	$count_a+=$special_ships_a[$j]['cnt'];

			if($special_ships_a[$j]['bonus_weapon']>0)
				$bonus_weapon_a=0;
			else
				$bonus_weapon_a=0;

			if($special_ships_a[$j]['bonus_structure']>0)
				$bonus_structure_a=0;
			else
				$bonus_structure_a=0;

			if($special_ships_a[$j]['bonus_shield']>0)
				$bonus_shield_a=0;
			else
				$bonus_shield_a=0;

			if($special_ships_a[$j]['bonus_heal']>0)
				$bonus_heal_a=0;
			else
				$bonus_heal_a=0;
        }


        //
        //Angreiffende Flotte normal
        //
        for ($i=0; $ships_a[$i]!=""; $i++)
        {
        	$msg.= "".$ships_a[$i]['name']." ".nf($ships_a[$i]['cnt'])."<br>";

            $structure_a+=$ships_a[$i]['structure']*$ships_a[$i]['cnt'];
            $shield_a+=$ships_a[$i]['shield']*$ships_a[$i]['cnt'];
            $weapon_a+=$ships_a[$i]['weapon']*$ships_a[$i]['cnt'];
        	$count_a+=$ships_a[$i]['cnt'];

        	//Heilende Schiffe
        	if($ships_a[$i]['heal']>0)
        	{
                $count_heal_a+=$ships_a[$i]['cnt'];
                $heal_a+=$ships_a[$i]['heal']*$ships_a[$i]['cnt'];
        	}
        }
        if($i==0 && $j==0)
        {
        	$msg.= "<i>Nichts vorhanden!</i><br>";
        }
        $dont_steal=1;




		$msg.= "<br><br><b>VERTEIDIGENDE FLOTTE:</b><br>";

        //
        //Verteidigende Flotte Spezial Schiffe
        //
        for ($j=0; $special_ships_d[$j]!=""; $j++)
        {
        	$msg.= "<b>".$special_ships_d[$j]['name']." (".$special_ships_d[$j]['level'].")</b> ".nf($special_ships_d[$j]['cnt'])."<br>";

            $structure_d+=$special_ships_d[$j]['structure']*$special_ships_d[$j]['cnt'];
            $shield_d+=$special_ships_d[$j]['shield']*$special_ships_d[$j]['cnt'];
            $weapon_d+=$special_ships_d[$j]['weapon']*$special_ships_d[$j]['cnt'];
        	$count_d+=$special_ships_d[$j]['cnt'];
        	$count_ds+=$ships_d[$i]['cnt'];

			if($special_ships_d[$j]['bonus_weapon']>0)
				$bonus_weapon_d=0;
			else
				$bonus_weapon_d=0;

			if($special_ships_d[$j]['bonus_structure']>0)
				$bonus_structure_d=0;
			else
				$bonus_structure_d=0;

			if($special_ships_d[$j]['bonus_shield']>0)
				$bonus_shield_d=0;
			else
				$bonus_shield_d=0;

			if($special_ships_d[$j]['bonus_heal']>0)
				$bonus_heal_d=0;
			else
				$bonus_heal_d=0;
        }

		//
        //Verteidigende Flotte normal
        //
        for ($i=0; $ships_d[$i]!=""; $i++)
        {
        	$msg.= "".$ships_d[$i]['name']." ".nf($ships_d[$i]['cnt'])."<br>";

            $structure_d+=$ships_d[$i]['structure']*$ships_d[$i]['cnt'];
            $shield_d+=$ships_d[$i]['shield']*$ships_d[$i]['cnt'];
            $weapon_d+=$ships_d[$i]['weapon']*$ships_d[$i]['cnt'];
        	$count_d+=$ships_d[$i]['cnt'];
        	$count_ds+=$ships_d[$i]['cnt'];

        	//Heilende Schiffe
        	if($ships_d[$i]['heal']>0)
        	{
                $count_heal_d+=$ships_d[$i]['cnt'];
                $heal_d+=$ships_d[$i]['heal']*$ships_d[$i]['cnt'];
        	}
        }
        if($i==0 && $j==0)
        {
        	$msg.= "<i>Nichts vorhanden!</i><br>";
        }



		$msg.= "<br><br><b>PLANETARE VERTEIDIGUNG:</b><br>";
		//
        //Verteidigung
        //
        for ($i=0; $def_d[$i]!=""; $i++)
        {
        	$msg.= "".$def_d[$i]['name']." ".nf($def_d[$i]['cnt'])."<br>";

            $structure_d+=$def_d[$i]['structure']*$def_d[$i]['cnt'];
            $shield_d+=$def_d[$i]['shield']*$def_d[$i]['cnt'];
            $weapon_d+=$def_d[$i]['weapon']*$def_d[$i]['cnt'];
        	$count_d+=$def_d[$i]['cnt'];
        	$count_dd+=$def_d[$i]['cnt'];

        }
        if($i==0)
        {
        	$msg.= "<i>Nichts vorhanden!</i><br>";
        }



        $shield_tech_a=1+$bonus_shield_a;
        $structure_tech_a=1+$bonus_structure_a;
        $weapon_tech_a=1+$bonus_weapon_a;
        $regena_tech_a=1+$bonus_heal_a;

        $shield_tech_d=1+$bonus_shield_d;
        $structure_tech_d=1+$bonus_structure_d;
        $weapon_tech_d=1+$bonus_weapon_d;
        $regena_tech_d=1+$bonus_heal_d;

		//
		//Technologie vom Angreifer laden
		//
        for ($i=0; $tech_a[$i]!=""; $i++)
        {
            if ($tech_a[$i]['id']==SHIELD_TECH_ID)
                $shield_tech_a+=($tech_a[$i]['level']/10);

            if ($tech_a[$i]['id']==STRUCTURE_TECH_ID)
                $structure_tech_a+=($tech_a[$i]['level']/10);

            if ($tech_a[$i]['id']==WEAPON_TECH_ID)
                $weapon_tech_a+=($tech_a[$i]['level']/10);

            if ($tech_a[$i]['id']==REGENA_TECH_ID)
            {
                $heal_points_a=$heal_a*($regena_tech_a+$tech_a[$i]['level']/10);
            }
            else
            {
                $heal_points_a=$heal_a;
            }
        }

		//
		//Technologie vom Verteidiger laden
		//
        for ($i=0; $tech_d[$i]!=""; $i++)
        {
            if ($tech_d[$i]['id']==SHIELD_TECH_ID)
                $shield_tech_d+=($tech_d[$i]['level']/10);

            if ($tech_d[$i]['id']==STRUCTURE_TECH_ID)
                $structure_tech_d+=($tech_d[$i]['level']/10);

            if ($tech_d[$i]['id']==WEAPON_TECH_ID)
                $weapon_tech_d+=($tech_d[$i]['level']/10);

            if ($tech_d[$i]['id']==REGENA_TECH_ID)
            {
                $heal_points_d=$heal_a*($regena_tech_d+$tech_d[$i]['level']/10);
            }
            else
            {
                $heal_points_d=$heal_a;
            }
        }

        $init_weapon_a_b=$weapon_a*($weapon_tech_a+$bonus_weapon_a);
        $init_weapon_d_b=$weapon_d*($weapon_tech_d+$bonus_weapon_d);

        $shield_a*=$shield_tech_a+$bonus_shield_a;
        $shield_d*=$shield_tech_d+$bonus_shield_d;

        $structure_a*=$structure_tech_a+$bonus_structure_a;
        $structure_d*=$structure_tech_d+$bonus_structure_d;

        $init_strushield_a=$shield_a+$structure_a;
        $init_strushield_d=$shield_d+$structure_d;

        $msg.="<br><br><b>DATEN DES ANGREIFERS</b><br>";
        $msg.="<b>Schild (".nf($shield_tech_a*100)."%):</b> ".nf($shield_a)."<br>";
        $msg.="<b>Struktur (".nf($structure_tech_a*100)."%):</b> ".nf($structure_a)."<br>";
        $msg.="<b>Waffen (".nf($weapon_tech_a*100)."%):</b> ".nf($init_weapon_a_b)."<br>";
        $msg.="<b>Einheiten:</b> ".nf($count_a)."<br><br>";
        $msg.="<b>DATEN DES VERTEIDIGERS</b><br>";
        $msg.="<b>Schild (".nf($shield_tech_d*100)."%):</b> ".nf($shield_d)."<br>";
        $msg.="<b>Struktur (".nf($structure_tech_d*100)."%):</b> ".nf($structure_d)."<br>";
        $msg.="<b>Waffen (".nf($weapon_tech_d*100)."%):</b> ".nf($init_weapon_d_b)."<br>";
        $msg.="<b>Einheiten:</b> ".nf($count_d)."<br><br>";

        $init_count_a = $count_a;
        $init_count_d = $count_d;

        $init_count_heal_a=$count_heal_a;
        $init_count_heal_d=$count_heal_d;

        $strushield_a=$init_strushield_a;
        $strushield_d=$init_strushield_d;


        for ($bx=0;$bx<BATTLE_ROUNDS;$bx++)
        {
            $weapon_a_b = @round($init_weapon_a_b * $count_a / $init_count_a);
            $weapon_d_b = @round($init_weapon_d_b * $count_d / $init_count_d);

            $strushield_d_b=$strushield_d-$weapon_a_b;
            $strushield_a_b=$strushield_a-$weapon_d_b;

            $runde = $bx+1;

            if ($strushield_a_b<=0)
                $strushield_a_b=0;
            if ($strushield_d_b<=0)
                $strushield_d_b=0;

            $msg.="<br>$runde: $count_a Einheiten des Angreifes schiessen mit einer Stärke von ".nf($weapon_a_b)." auf den Verteidiger. Der Verteidiger hat danach noch ".nf($strushield_d_b)." Struktur- und Schildpunkte<br>";
            $msg.="<br>$runde: $count_d Einheiten des Verteidigers schiessen mit einer Stärke von ".nf($weapon_d_b)." auf den Angreifer. Der Angreifer hat danach noch ".nf($strushield_a_b)." Struktur- und Schildpunkte<br>";

            $count_a=@ceil($init_count_a*$strushield_a_b/$init_strushield_a);
            $count_d=@ceil($init_count_d*$strushield_d_b/$init_strushield_d);

            if ($count_a<=0) $count_a=0;
            if ($count_d<=0) $count_d=0;

            $strushield_a=$strushield_a_b;
            $strushield_d=$strushield_d_b;

            if ($count_heal_a>0 && $count_a>0)
            {
                $count_heal_a=@ceil($init_count_heal_a*$strushield_a_b/$init_strushield_a);
                $heal_point_a=$count_heal_a*$heal_points_a/$init_count_heal_a;
                $strushield_a+=$heal_point_a;
                if ($strushield_a>$init_strushield_a)
                    $strushield_a=$init_strushield_a;

                $msg.="<br>$runde: $count_heal_a Einheiten des Angreifes heilen ".nf($heal_point_a)." Struktur- und Schildpunkte. Der Angreifer hat danach wieder ".nf($strushield_a)." Struktur- und Schildpunkte<br>";
            }

            if ($count_heal_d>0 && $count_d>0)
            {
                $count_heal_d=@ceil($init_count_heal_d*$strushield_d_b/$init_strushield_d);
                $heal_point_d=$count_heal_d*$heal_points_d/$init_count_heal_d;
                $strushield_d+=$heal_point_d;
                if ($strushield_d>$init_strushield_d) $strushield_d=$init_strushield_d;
                $msg.="<br>$runde: $count_heal_d Einheiten des Verteidigers heilen ".nf($heal_point_d)." Struktur- und Schildpunkte. Der Verteidiger hat danach wieder ".nf($strushield_d)." Struktur- und Schildpunkte<br>";
            }
            if ($strushield_a_b<=0 || $strushield_d_b<=0)
                break;
        }

        $msg.= "<br>Der Kampf dauerte $runde Runden!<br><br>";

        $ships_a_db = array();
        $ships_d_db = array();
        $def_d_db = array();

        $wf[0]=0;
        $wf[1]=0;
        $wf[2]=0;

		$lose_fleet_a[0]=0;
		$lose_fleet_a[1]=0;
		$lose_fleet_a[2]=0;
		$lose_fleet_a[3]=0;
		$lose_fleet_a[4]=0;

		$lose_fleet_d[0]=0;
		$lose_fleet_d[1]=0;
		$lose_fleet_d[2]=0;
		$lose_fleet_d[3]=0;
		$lose_fleet_d[4]=0;


        foreach ($ships_a as $id=>$data)
        {
            //überlebende schiffe errechnen (wenn die def des schiffes <= 0 ist, ist das schiff zerstört (onefight)
            if(($data['shield']+$data['structure'])<=0)
            {
                $num = 0;
            }
            else
            {
                $num = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));
            }
            $ships_a_db[$data['id']]=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_a[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_a[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_a[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_a[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_a[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }
        //spezialschiffe att
        foreach ($special_ships_a as $id=>$data)
        {
            //überlebende schiffe errechnen (wenn die def des schiffes <= 0 ist, ist das schiff zerstört (onefight)
            if(($data['shield']+$data['structure'])<=0)
            {
                $num = 0;
            }
            else
            {
                $num = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));
            }
            $special_ships_a_db[$data['id']]=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_a[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_a[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_a[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_a[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_a[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }
        foreach ($ships_d as $id=>$data)
        {
            $num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));
            $ships_d_db[$data['id']]=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_d[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_d[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_d[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_d[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_d[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }
        //spezialschiffe def
        foreach ($special_ships_d as $id=>$data)
        {
            //überlebende schiffe errechnen (wenn die def des schiffes <= 0 ist, ist das schiff zerstört (onefight)

            $num = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));

            $special_ships_d_db[$data['id']]=$num;
            $wf[0]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*SHIP_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_d[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_d[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_d[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_d[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_d[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }
        foreach ($def_d as $id=>$data)
        {
            $num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));
            $num = $num + round(($data['cnt']-$num) * $conf['def_restore_percent']['v']);
            $def_d_db[$data['id']]=$num;
            $wf[0]+=round(($data['cnt']-$num)*DEF_WF_PERCENT*$data['costs_metal']);
            $wf[1]+=round(($data['cnt']-$num)*DEF_WF_PERCENT*$data['costs_crystal']);
            $wf[2]+=round(($data['cnt']-$num)*DEF_WF_PERCENT*$data['costs_plastic']);

			$lose_fleet_d[0]+=round(($data['cnt']-$num)*$data['costs_metal']);
            $lose_fleet_d[1]+=round(($data['cnt']-$num)*$data['costs_crystal']);
            $lose_fleet_d[2]+=round(($data['cnt']-$num)*$data['costs_plastic']);
            $lose_fleet_d[3]+=round(($data['cnt']-$num)*$data['costs_fuel']);
            $lose_fleet_d[4]+=round(($data['cnt']-$num)*$data['costs_food']);
        }


        $new_exp_d_init = ($lose_fleet_a[0] + $lose_fleet_a[1] + $lose_fleet_a[2] + $lose_fleet_a[3] + $lose_fleet_a[4]) / 100000;
		$new_exp_a_init = ($lose_fleet_d[0] + $lose_fleet_d[1] + $lose_fleet_d[2] + $lose_fleet_d[3] + $lose_fleet_d[4]) / 100000;

		$new_exp_d = $new_exp_d_init;
		$new_exp_a = $new_exp_a_init;

        if ($count_d==0 && $count_a>0)
        {
            $return_v = 2;
            $msg.= "<br>Der Angreifer hat den Kampf gewonnen!<br><br>";

			$msg.= "<b>Die Spezialschiffe von der imperator erhalten ".nf($new_exp_a_init)." EXP!</b><br>";

			if($new_exp_a>0)
			{

                foreach ($special_ships_a as $id=>$data)
                {

                    //Errechnet das Level welches das Spezialschiff erreichen wird (att)
                    for ($level=$data['level'];$new_exp_a>($data['need_exp']*pow($data['exp_factor'],$level));$level++)
                    {

                    }

					if($data['level']<$level)
					{
                    	$msg.= "".$data['name']." steigt von Level ".$data['level']." auf Level $level!<br>";
                    }

                }
			}

        }
        elseif ($count_a==0 && $count_d>0)
        {
            $return_v = 0;
            $msg.= "Der Verteidiger hat den Kampf gewonnen!<br><br>";

			$msg.= "<br><b>Die Spezialschiffe von das arme arschloch erhalten ".nf($new_exp_d_init)." EXP!</b><br>";

			if($new_exp_d>0)
			{
                foreach ($special_ships_d as $id=>$data)
                {


                    //Errechnet das Level welches das Spezialschiff erreichen wird (def)
                    for ($level=0;$new_exp_d>($data['need_exp']*pow($data['exp_factor'],$level));$level++)
                    {

                    }

					if($data['level']<$level)
					{
                    	$msg.= "".$data['name']." steigt so von Level ".$data['level']." auf Level $level!<br>";
                    }

                }
			}

        }
        else
        {
            $return_v = 1;
            if ($count_a==0 && $count_d==0)
            {
                $msg.= "Der Kampf endete unentschieden, da sowohl die Einheiten des Angreifes als auch die Einheiten des Verteidigers alle zerstört wurden!<br><br>";
            }
            else
            {
 				$msg.= "Der Kampf endete unentschieden und die Flotten zogen sich zurück!<br><br>";

            	$msg.= "<br><b>Die Spezialschiffe von der imperator erhalten ".nf($new_exp_a_init)." EXP!</b><br>";
                foreach ($special_ships_a as $id=>$data)
                {
                    //Errechnet das Level welches das Spezialschiff erreichen wird (att)
                    for ($level=0;$new_exp_a>($data['need_exp']*pow($data['exp_factor'],$level));$level++)
                    {

                    }

                    $msg.= "".$data['name']." erreicht so Level $level!<br><br>";
                }

				$msg.= "<br><b>Die Spezialschiffe von das arme arschloch erhalten ".nf($new_exp_d_init)." EXP!</b><br>";
                foreach ($special_ships_d as $id=>$data)
                {
                    //Errechnet das Level welches das Spezialschiff erreichen wird (def)
                    for ($level=0;$new_exp_d>($data['need_exp']*pow($data['exp_factor'],$level));$level++)
                    {

                    }

                    $msg.= "".$data['name']." erreicht so Level $level!<br><br>";
                }
			}
		}

        $msg.= "<br><b>TR&Uuml;MMERFELD:</b><br>";
        $msg.= "".RES_METAL.": ".nf(abs($wf[0]))."<br>";
        $msg.= "".RES_CRYSTAL.": ".nf(abs($wf[1]))."<br>";
        $msg.= "".RES_PLASTIC.": ".nf(abs($wf[2]))."<br>";
        $msg.= "<br><br>";

        $msg.= "<b>Zustand nach dem Kampf:</b><br><br>";
        $msg.= "<b>ANGREIFENDE FLOTTE:</b><br>";


		$fleet_rest_a=0;
        if (count($special_ships_a)>0)
        {
        	$fleet_rest_a=1;
            foreach ($special_ships_a as $id=>$data)
            {

                if(($data['shield']+$data['structure'])<=0)
                {
                    $count = 0;
                }
                else
                {
                    $count = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));
                }

                //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
                if($data['cnt']<$count)
                	$count=$data['cnt'];

                $loose=$data['cnt']-$count;
                $msg.= "<b>".$data['name']."</b> ".$count." (-$loose)<br>";
            }
        }

        if (count($ships_a)>0)
        {
        	$fleet_rest_a=1;

            foreach ($ships_a as $id=>$data)
            {

                if(($data['shield']+$data['structure'])<=0)
                {
                    $count = 0;
                }
                else
                {
                    $count = ceil($data['cnt']*$strushield_a/($shield_a+$structure_a));
                }

                //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
                if($data['cnt']<$count)
                	$count=$data['cnt'];

                $loose=$data['cnt']-$count;
                $msg.= "".$data['name']." ".$count." (-$loose)<br>";
            }
        }
        elseif($fleet_rest_a==0)
        {
            $msg.= "<i>Nichts vorhanden!</i><br>";
        }



        $msg.= "<br><b>VERTEIDIGENDE FLOTTE:</b><br>";
		$fleet_rest_d=0;
        if (($special_ships_d)>0)
        {
        	$fleet_rest_d=1;
            foreach ($special_ships_d as $id=>$data)
            {
                $count=ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));

                //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
                if($data['cnt']<$count)
                	$count=$data['cnt'];

                $loose=$data['cnt']-$count;
                $msg.= "<b>".$data['name']."</b> ".$count." (-$loose)<br>";
            }
        }
        if (($ships_d)>0)
        {
        	$fleet_rest_d=1;
            foreach ($ships_d as $id=>$data)
            {
                $count=ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));

                //stellt sicher, dass nicht mehr schiffe nach dem kampf sind als vor dem kampf
                if($data['cnt']<$count)
                	$count=$data['cnt'];

                $loose=$data['cnt']-$count;
                $msg.= "".$data['name']." ".$count." (-$loose) -> data_cnt=".$data['cnt'].", strushield_d=".$strushield_d.", shield_d+structure_d=".($shield_d+$structure_d)."<br>";
            }
        }
        elseif($fleet_rest_d==0)
        {
            $msg.= "<i>Nichts vorhanden!</i><br>";
        }

        $msg.= "<br><b>VERTEIDIGUNG:</b><br>";
        if (count($def_d)>0)
        {
            foreach ($def_d as $id=>$data)
            {
                $num = ceil($data['cnt']*$strushield_d/($shield_d+$structure_d));

                //stellt sicher, dass nicht mehr anlagen nach dem kampf sind als vor dem kampf
                if($data['cnt']<$num)
                	$num=$data['cnt'];

                $numr = round(($data['cnt']-$num) * $conf['def_restore_percent']['v']);
                $msg.= "".$data['name']." ".$num." (+$numr)<br>";
            }
        }
        else
        {
            $msg.= "<i>Nichts vorhanden!</i>";
        }
        $perc = $conf['def_restore_percent']['v']*100;
        $msg.="<br>$perc% der Verteidigungsanlagen werden repariert!<br><br>";





    $msg.="<br><br><a href=\"?page=battle_simulation\">zurück</a>";
    //send_msg($_SESSION[ROUNDID]['user']['id'],SHIP_WAR_MSG_CAT_ID,"Kampfbericht (Simulation)",$msg);
    infobox_start("Kampfbericht (Simulation)");
    echo $msg;
    infobox_end();

}
?>