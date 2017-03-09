<?php

$tpl->setView('galaxy/checkpoints');
$tpl->assign('title', "Kontrollpunkte");

if (isset($_POST['reset'])) {

                dbquery("UPDATE planets 
                         SET planet_bonus_metal = 0,
                             planet_bonus_crystal = 0,
                             planet_bonus_plastic = 0,
                             planet_bonus_fuel = 0,
                             planet_bonus_food = 0
                         WHERE planet_type_id =".CHECKPOINT_PLANET_ID);
        $tpl->assign("msg", "Boni erfolgreich zurückgesetzt!");
}

if (isset($_POST['generator'])) {
    $res = dbquery("
        SELECT
            id
        FROM	
            planets
        WHERE
            planet_type_id=".CHECKPOINT_PLANET_ID);

    if (mysql_num_rows($res)>0) {
        $cfg = Config::getInstance()->getArray();

        dbquery("UPDATE planets 
                 SET planet_bonus_metal = 0,
                     planet_bonus_crystal = 0,
                     planet_bonus_plastic = 0,
                     planet_bonus_fuel = 0,
                     planet_bonus_food = 0
                 WHERE planet_type_id =".CHECKPOINT_PLANET_ID);

        foreach(mysql_fetch_row($res) as $pid) {
            /*
             * get a random ressourcebonus for each planet
             * 0 = metal
             * 1 = crystal
             * 2 = plastic
             * 3 = fuel
             * 4 = food
            */

            switch (rand (0 , 4)) {
                case 0:
                    $field = 'planet_bonus_metal';
                    $value = rand($cfg['bonus_metal']['v'], $cfg['bonus_metal']['p1']);
                    break;
                case 1:
                    $field = 'planet_bonus_crystal';
                    $value = rand($cfg['bonus_crystal']['v'], $cfg['bonus_crystal']['p1']);
                    break;
                case 2:
                    $field = 'planet_bonus_plastic';
                    $value = rand($cfg['bonus_plastic']['v'], $cfg['bonus_plastic']['p1']);
                    break;
                case 3:
                    $field = 'planet_bonus_fuel';
                    $value = rand($cfg['bonus_fuel']['v'], $cfg['bonus_fuel']['p1']);
                    break;
                case 4:
                    $field = 'planet_bonus_food';
                    $value = rand($cfg['bonus_food']['v'], $cfg['bonus_food']['p1']);
                    break;
            }

            dbquery("UPDATE planets 
                         SET $field = $value
                         WHERE id = $pid");
        }

        $tpl->assign("msg", "Boni erfolgreich erstellt!");
    }
    else {
        $tpl->assign("infomsg", "Es konnten keine Boni erzeugt werden da keine Kontrollpunkte vorhanden sind!");
    }
}
