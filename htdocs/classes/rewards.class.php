<?php

class Rewards
{
    static function giveRewards() {

        //get all alliance bots
        $res = dbquery("
			SELECT
				user_id 
			FROM
				users
			WHERE
			    npc = 1");
        $arr = mysql_fetch_row($res);


        //check if bot has planets
        foreach ($arr as $botId) {

            $query = dbquery("
			SELECT
				sum(planet_bonus_metal) as bonus_metal,
				sum(planet_bonus_crystal) as bonus_crystal,
				sum(planet_bonus_plastic) as bonus_plastic,
				sum(planet_bonus_fuel) as bonus_fuel,
				sum(planet_bonus_food) as bonus_food
			FROM	
				planets
			WHERE
				planet_user_id=".$botId."
			AND
				planet_type_id =".CHECKPOINT_PLANET_ID);


            $bonus = mysql_fetch_row($query);

            if (array_filter($bonus))
            {
                $bot = new User($botId);
                $alliance = new Alliance($bot->allianceId());
                $cfg = Config::getInstance()->getArray();

                //get all alliance members
                foreach($alliance->members as $member) {

                    $res = dbquery("
                        SELECT
                            id,
                            planet_user_main
                        FROM	
                            planets
                        WHERE
                            planet_user_id=".$member->id."
                        ORDER BY
                            planet_user_main DESC,
                            planet_name ASC
                    ");
                    if (mysql_num_rows($res)>0)
                    {
                        $planets = array();
                        while ($arr=mysql_fetch_row($res))
                        {
                            $planets[] = $arr[0];
                        }

                        $pm = new PlanetManager($planets);

                        $prodMetal =0;
                        $prodCrystal =0;
                        $prodPlastic =0;
                        $prodFuel =0;
                        $prodFood =0;

                        //get the production
                        foreach($pm->itemObjects() as $value) {
                            $prodMetal += $value->prodMetal;
                            $prodCrystal += $value->prodCrystal;
                            $prodPlastic += $value->prodPlastic;
                            $prodFuel += $value->prodFuel;
                            $prodFood += $value->prodFood;
                        }

                        $bonusMetal = $prodMetal * ($bonus[0]/100) * $cfg['timespan']['v'];
                        $bonusCrystal = $prodCrystal * ($bonus[1]/100) * $cfg['timespan']['v'];
                        $bonusPlastic = $prodPlastic * ($bonus[2]/100) * $cfg['timespan']['v'];
                        $bonusFuel = $prodFuel * ($bonus[3]/100) * $cfg['timespan']['v'];
                        $bonusFood = $prodFood * ($bonus[4]/100) * $cfg['timespan']['v'];




                        $checkUser = dbquery("
                            SELECT
                                storage_user_id
                            FROM	
                                reward_storage
                            WHERE
                                storage_user_id=".$member->id);

                        echo $bonusMetal;
                        echo $bonusCrystal;
                        echo $bonusPlastic;
                        echo $bonusFuel;
                        echo $bonusFood;

                        if  (mysql_num_rows($checkUser)>0) {
                            dbquery("
                            UPDATE
                                reward_storage
                            SET	
                                storage_res_metal = storage_res_metal + $bonusMetal,
                                storage_res_crystal = storage_res_crystal + $bonusCrystal,
                                storage_res_plastic = storage_res_plastic + $bonusPlastic,
                                storage_res_fuel = storage_res_fuel + $bonusFuel,
                                storage_res_food = storage_res_food + $bonusFood
                            WHERE
                                storage_user_id=".$member->id);
                        }
                        else {
                            dbquery("
                                INSERT INTO
                                    reward_storage
                                VALUES	
                                    (".$member->id.",$bonusMetal,$bonusCrystal,$bonusPlastic,$bonusFuel,$bonusFood)"
                            );
                        }
                    }
                }
            }
        }
    }
}