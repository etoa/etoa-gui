<?PHP
        // Lädt die Bonis der Spezialschiffe und summiert sie
        $special_bonis_res = dbquery("
        SELECT
            s.special_ship_bonus_antrax,
            s.special_ship_bonus_forsteal,
            s.special_ship_bonus_build_destroy,
            s.special_ship_bonus_antrax_food,
            s.special_ship_bonus_deactivade,

            fs.fs_ship_cnt,

            sl.shiplist_special_ship_bonus_antrax,
            sl.shiplist_special_ship_bonus_forsteal,
            sl.shiplist_special_ship_bonus_build_destroy,
            sl.shiplist_special_ship_bonus_antrax_food,
            sl.shiplist_special_ship_bonus_deactivade
        FROM 
			(
				(
					".$db_table['fleet_ships']." AS fs 
				INNER JOIN 
					".$db_table['fleet']." AS f 
				ON fs.fs_fleet_id = f.fleet_id
				) 
			INNER JOIN 
				".$db_table['ships']." AS s 
			ON fs.fs_ship_id = s.ship_id
			) 
		INNER JOIN 
			".$db_table['shiplist']." AS sl 
		ON sl.shiplist_planet_id = f.fleet_planet_from 
		AND sl.shiplist_user_id = f.fleet_user_id 
		AND s.ship_id = sl.shiplist_ship_id
		AND f.fleet_id='".$arr['fleet_id']."' 
		AND s.special_ship='1';");        
        
        $special_ship_bonus_antrax = 0;
        $special_ship_bonus_forsteal = 0;
        $special_ship_bonus_build_destroy = 0;
        $special_ship_bonus_antrax_food = 0;
        $special_ship_bonus_deactivade = 0;

        if (mysql_num_rows($special_bonis_res)>0)
        {
            while ($special_bonis_arr=mysql_fetch_array($special_bonis_res))
            {
            	$special_ship_bonus_antrax+=$special_bonis_arr['special_ship_bonus_antrax'] * $special_bonis_arr['shiplist_special_ship_bonus_antrax'];
            	$special_ship_bonus_forsteal+=$special_bonis_arr['special_ship_bonus_forsteal'] * $special_bonis_arr['shiplist_special_ship_bonus_forsteal'];
            	$special_ship_bonus_build_destroy+=$special_bonis_arr['special_ship_bonus_build_destroy'] * $special_bonis_arr['shiplist_special_ship_bonus_build_destroy'];
            	$special_ship_bonus_antrax_food+=$special_bonis_arr['special_ship_bonus_antrax_food'] * $special_bonis_arr['shiplist_special_ship_bonus_antrax_food'];
            	$special_ship_bonus_deactivade+=$special_bonis_arr['special_ship_bonus_deactivade'] * $special_bonis_arr['shiplist_special_ship_bonus_deactivade'];
            }

         }
?>