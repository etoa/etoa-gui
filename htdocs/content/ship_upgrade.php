<?PHP
//
// Upgrade menu eines spezial Schiffes
//
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    echo "<h1>Schiffsupgrade-Menu</h1>";

    //
    // Upgrade speichern
    //
    if (
        isset($_POST['submit_upgrade']) && $_POST['submit_upgrade'] != ""
        && intval($_POST['id']) > 0 && $_POST['upgrade'] != ""
        && ctype_alpha($_POST['upgrade']) && checker_verify()
    ) {
        dbquery("
        UPDATE
            shiplist
        SET
            shiplist_special_ship_level=shiplist_special_ship_level+1,
            shiplist_special_ship_bonus_" . $_POST['upgrade'] . "=shiplist_special_ship_bonus_" . $_POST['upgrade'] . "+1
        WHERE
            shiplist_ship_id='" . intval($_POST['id']) . "'
            AND shiplist_user_id='" . $cu->id . "';");

        success_msg("Upgrade erfolgreich duchgeführt!");

        $app['dispatcher']->dispatch(new \EtoA\Ship\Event\ShipUpgrade(), \EtoA\Ship\Event\ShipUpgrade::UPGRADE_SUCCESS);
    }


    //Liest alle notwendigen Daten für das Upgradende Schiff aus der DB heraus
    $res = dbquery("
    SELECT
        ships.ship_name,
        ships.special_ship_max_level,
        ships.special_ship_need_exp,
        ships.special_ship_exp_factor,
        ships.special_ship_bonus_weapon,
        ships.special_ship_bonus_structure,
        ships.special_ship_bonus_shield,
        ships.special_ship_bonus_heal,
        ships.special_ship_bonus_capacity,
        ships.special_ship_bonus_speed,
        ships.special_ship_bonus_pilots,
        ships.special_ship_bonus_tarn,
        ships.special_ship_bonus_antrax,
        ships.special_ship_bonus_forsteal,
        ships.special_ship_bonus_build_destroy,
        ships.special_ship_bonus_antrax_food,
        ships.special_ship_bonus_deactivade,
        ships.special_ship_bonus_readiness,

        shiplist.shiplist_special_ship_level,
        shiplist.shiplist_special_ship_exp,
        shiplist.shiplist_special_ship_bonus_structure,
        shiplist.shiplist_special_ship_bonus_shield,
        shiplist.shiplist_special_ship_bonus_weapon,
        shiplist.shiplist_special_ship_bonus_heal,
        shiplist.shiplist_special_ship_bonus_capacity,
        shiplist.shiplist_special_ship_bonus_speed,
        shiplist.shiplist_special_ship_bonus_pilots,
        shiplist.shiplist_special_ship_bonus_tarn,
        shiplist.shiplist_special_ship_bonus_antrax,
        shiplist.shiplist_special_ship_bonus_forsteal,
        shiplist.shiplist_special_ship_bonus_build_destroy,
        shiplist.shiplist_special_ship_bonus_antrax_food,
        shiplist.shiplist_special_ship_bonus_deactivade,
        shiplist.shiplist_special_ship_bonus_readiness
    FROM
            ships AS ships
        INNER JOIN
            shiplist AS shiplist
        ON ships.ship_id=shiplist.shiplist_ship_id
        AND ships.special_ship='1'
        AND shiplist.shiplist_user_id='" . $cu->id . "'
        AND shiplist.shiplist_ship_id='" . intval($_GET['id']) . "'
        AND shiplist.shiplist_count>'0'
    ;");

    if (mysql_num_rows($res) > 0) {
        $arr = mysql_fetch_array($res);

        $init_level = $arr['shiplist_special_ship_level'];
        $init_exp = $arr['shiplist_special_ship_exp'];
        $exp = $init_exp;

        $rest_exp = $exp;


        //Errechnet das Level aus den momentanen erfahrungen (exp)
        //Diese Schleife nicht löschen, die hat schon ihren Sinn, auch wenn nichts in der Klammer ist :P
        $level = 0;
        while ($exp >= ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'], $level))) {
            $level++;
        }

        //Errechnet die benötigten EXP für das nächste Level
        $exp_for_next_level = ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'], $level));


        echo "<form action=\"?page=$page&amp;id=" . intval($_GET['id']) . "\" method=\"post\">";
        checker_init();

        tableStart($arr['ship_name']);
        echo "
                 <tr>
                     <th width=\"25%\">Level</th>";

        if ($arr['special_ship_max_level'] <= $init_level && $arr['special_ship_max_level'] != 0) {
            echo "<td width=\"10%\">" . $init_level . " (max.)</td>";
        } else {
            if ($level - $init_level <= 0) {
                echo "<td width=\"10%\">$init_level (+" . ($level - $init_level) . ")</td>";
            } else {
                echo "<td style=\"color:green;\" width=\"10%\">$init_level (+" . ($level - $init_level) . ")</td>";
            }
        }
        echo "
                     <td  width=\"65%\">Level des Schiffes</td>
                 </tr>
                 <tr>
                     <th width=\"25%\">Erfahrung</th>
                     <td width=\"10%\">" . nf($arr['shiplist_special_ship_exp']) . "</td>
                     <td width=\"65%\">Erfahrung des Schiffes</td>
                 </tr>
                 <tr>
                     <th width=\"25%\">Ben. Erfahrung</th>";

        if ($arr['special_ship_max_level'] <= $init_level && $arr['special_ship_max_level'] != 0) {
            echo "<td width=\"10%\"> - </td>";
        } else {
            echo "<td width=\"10%\">" . nf($exp_for_next_level) . "</td>";
        }

        echo "<td width=\"65%\">Benötigte Erfahrung bis zum nächsten LevelUp</td>
                 </tr>

                 ";
        tableEnd();

        //Zeigt alle Bonis die das Schiff upgraden kann
        tableStart("Bonis");
        echo "
                 <tr>
                     <th width=\"25%\">Skill</th>
                     <th width=\"10%\">Bonus</th>
                     <th width=\"63%\">Info</th>
                     <th width=\"2%\">LvL</th>
                 </tr>
                 ";


        // Waffentechnik Bonus
        if ($arr['special_ship_bonus_weapon'] > 0) {
            echo "<tr>
                         <th>Waffen (" . $arr['shiplist_special_ship_bonus_weapon'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_weapon'] * $arr['special_ship_bonus_weapon'] * 100, 1)) . "%</td>
                         <td>Waffenbonus im Kampf (" . ($arr['special_ship_bonus_weapon'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"weapon\" border=\"0\"></td>
                     </tr>";
        }
        // Struktur Bonus
        if ($arr['special_ship_bonus_structure'] > 0) {
            echo "<tr>
                         <th>Panzerung (" . $arr['shiplist_special_ship_bonus_structure'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_structure'] * $arr['special_ship_bonus_structure'] * 100, 1)) . "%</td>
                         <td>Struktur im Kampf (" . ($arr['special_ship_bonus_structure'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"structure\" border=\"0\"></td>
                     </tr>";
        }
        // Schild Bonus
        if ($arr['special_ship_bonus_shield'] > 0) {
            echo "<tr>
                         <th>Schild (" . $arr['shiplist_special_ship_bonus_shield'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_shield'] * $arr['special_ship_bonus_shield'] * 100, 1)) . "%</td>
                         <td>Schildbonus im Kampf (" . ($arr['special_ship_bonus_shield'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"shield\" border=\"0\"></td>
                     </tr>";
        }
        // kapazitäts Bonus
        if ($arr['special_ship_bonus_capacity'] > 0) {
            echo "<tr>
                         <th>Kapazität (" . $arr['shiplist_special_ship_bonus_capacity'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_capacity'] * $arr['special_ship_bonus_capacity'] * 100, 1)) . "%</td>
                         <td>Erhöht die Kapazität der ganzen Flotte (" . ($arr['special_ship_bonus_capacity'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"capacity\" border=\"0\"></td>
                     </tr>";
        }
        // Speed Bonus
        if ($arr['special_ship_bonus_speed'] > 0) {
            echo "<tr>
                         <th>Speed (" . $arr['shiplist_special_ship_bonus_speed'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_speed'] * $arr['special_ship_bonus_speed'] * 100, 1)) . "%</td>
                         <td>Erhöht den Speed der ganzen Flotte (" . ($arr['special_ship_bonus_speed'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"speed\" border=\"0\"></td>
                     </tr>";
        }
        // Tarn Bonus
        if ($arr['special_ship_bonus_tarn'] > 0) {
            echo "<tr>
                         <th>Tarnung (" . $arr['shiplist_special_ship_bonus_tarn'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_tarn'] * $arr['special_ship_bonus_tarn'] * 100, 1)) . "%</td>
                         <td>Ermöglicht eine absolute Tarnung der Flotte (" . ($arr['special_ship_bonus_tarn'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"tarn\" border=\"0\"></td>
                     </tr>";
        }
        // Piloten Bonus
        if ($arr['special_ship_bonus_pilots'] > 0) {
            echo "<tr>
                         <th>Besatzung (" . $arr['shiplist_special_ship_bonus_pilots'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_pilots'] * $arr['special_ship_bonus_pilots'] * 100, 1)) . "%</td>
                         <td>Verringert die benötigten Piloten der Flotte (" . ($arr['special_ship_bonus_pilots'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"pilots\" border=\"0\"></td>
                     </tr>";
        }
        // Heal Bonus
        if ($arr['special_ship_bonus_heal'] > 0) {
            echo "<tr>
                         <th>Heilung (" . $arr['shiplist_special_ship_bonus_heal'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_heal'] * $arr['special_ship_bonus_heal'] * 100, 1)) . "%</td>
                         <td>Heilbonus im Kampf (" . ($arr['special_ship_bonus_heal'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
                     </tr>";
        }
        // Giftgas Bonus
        if ($arr['special_ship_bonus_antrax'] > 0) {
            echo "<tr>
                         <th>Giftgas (" . $arr['shiplist_special_ship_bonus_antrax'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_antrax'] * $arr['special_ship_bonus_antrax'] * 100, 1)) . "%</td>
                         <td>Erhöht Giftgaseffekt (" . ($arr['special_ship_bonus_antrax'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
                     </tr>";
        }
        // Techklau Bonus
        if ($arr['special_ship_bonus_forsteal'] > 0) {
            echo "<tr>
                         <th>Spionageangriff (" . $arr['shiplist_special_ship_bonus_forsteal'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_forsteal'] * $arr['special_ship_bonus_forsteal'] * 100, 1)) . "%</td>
                         <td>Erhöht die Erfolgschancen beim Spionageangriff (" . ($arr['special_ship_bonus_forsteal'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"forsteal\" border=\"0\"></td>
                     </tr>";
        }
        // Bombardieren Bonus
        if ($arr['special_ship_bonus_build_destroy'] > 0) {
            echo "<tr>
                         <th>Bombardieren (" . $arr['shiplist_special_ship_bonus_build_destroy'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_build_destroy'] * $arr['special_ship_bonus_build_destroy'] * 100, 1)) . "%</td>
                         <td>Erhöht Bombardierungschancen (" . ($arr['special_ship_bonus_build_destroy'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"build_destroy\" border=\"0\"></td>
                     </tr>";
        }
        // Antrax Bonus
        if ($arr['special_ship_bonus_antrax_food'] > 0) {
            echo "<tr>
                         <th>Antrax (" . $arr['shiplist_special_ship_bonus_antrax_food'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_antrax_food'] * $arr['special_ship_bonus_antrax_food'] * 100, 1)) . "%</td>
                         <td>Erhöht Antraxeffekt (" . ($arr['special_ship_bonus_antrax_food'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"antrax_food\" border=\"0\"></td>
                     </tr>";
        }
        // Deaktivieren Bonus
        if ($arr['special_ship_bonus_deactivade'] > 0) {
            echo "<tr>
                         <th>Deaktivieren (" . $arr['shiplist_special_ship_bonus_deactivade'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_deactivade'] * $arr['special_ship_bonus_deactivade'] * 100, 1)) . "%</td>
                         <td>Erhöht Deaktivierungschancen (" . ($arr['special_ship_bonus_deactivade'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"deactivade\" border=\"0\"></td>
                     </tr>";
        }
        // Readyness Bonus
        if ($arr['special_ship_bonus_readiness'] > 0) {
            echo "<tr>
                         <th>Bereitschaft (" . $arr['shiplist_special_ship_bonus_readiness'] . ")</th>
                         <td>" . (round($arr['shiplist_special_ship_bonus_readiness'] * $arr['special_ship_bonus_readiness'] * 100, 1)) . "%</td>
                         <td>Verringert die Start- und Landezeit der ganzen Flotte (" . ($arr['special_ship_bonus_readiness'] * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"readiness\" border=\"0\"></td>
                     </tr>";
        }



        tableEnd();

        //Level Button anzeigen, wenn genügend EXP vorhaden
        if ($level - $init_level > 0 && ($arr['special_ship_max_level'] > $init_level || $arr['special_ship_max_level'] == 0)) {
            echo "<input type=\"hidden\" name=\"id\" value=\"" . intval($_GET['id']) . "\">";
            echo "<input type=\"submit\" class=\"button\" name=\"submit_upgrade\" value=\"Gewähltes Upgrade duchführen\" /><br><br>";
        }
        echo "</form>";


        echo "<input type=\"button\" value=\"Zurück zur Übersicht\" onclick=\"document.location='?page=ship_upgrade'\" />";
    } else {
        error_msg("Du musst dieses Schiff zuerst bauen, oder auf den Planeten wechseln, auf dem sich das Schiff befindet!", 1);
    }
}







//
// Spezial Schiffe Auflisten
//
else {
    echo "<h1>Spezialschiffe</h1>";

    //Listet alle spezial Schiffe auf die der user besitzt
    $res = dbquery("
      SELECT
        ships.ship_id,
        ships.ship_name,
        ships.ship_longcomment,
        ships.ship_race_id,
        ships.special_ship_max_level,
        ships.special_ship_need_exp,
        ships.special_ship_exp_factor,

        shiplist.shiplist_special_ship_level,
        shiplist.shiplist_special_ship_exp
      FROM
        ships AS ships
        INNER JOIN
        shiplist AS shiplist
        ON ships.ship_id=shiplist.shiplist_ship_id
        AND shiplist.shiplist_user_id='" . $cu->id . "'
        AND ships.special_ship='1'
        AND shiplist.shiplist_count>'0'
      ORDER BY
          ships.ship_name;");

    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_array($res)) {
            $init_level = $arr['shiplist_special_ship_level'];
            $init_exp = $arr['shiplist_special_ship_exp'];
            $exp = $init_exp;
            $rest_exp = $exp;

            //Errechnet das Level aus den momentanen erfahrungen (exp)
            //Diese Schleife nicht löschen, die hat schon ihren Sinn, auch wenn nichts in der Klammer ist :P
            $level = 0;
            while ($exp >= ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'], $level))) {
                $level++;
            }

            //Errechnet die benötigten EXP
            $exp_for_next_level = ceil($arr['special_ship_need_exp'] * pow($arr['special_ship_exp_factor'], $level));


            tableStart($arr['ship_name']);

            echo "
                        <tr>
                            <th style=\"width:220px;\">
                                <a href=\"?page=ship_upgrade&amp;id=" . $arr['ship_id'] . "\"><img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['ship_id'] . "." . IMAGE_EXT . "\" width=\"220\" height=\"220\" alt=\"Klicke hier um ins Upgrade Menu zu gelangen\" title=\"Klicke hier um ins Upgrade Menu zu gelangen\" border=\"0\"/></a></th>
                            <td colspan=\"3\">" . text2html($arr['ship_longcomment']) . "</td>
                        </tr>";
            echo "
                         <tr>
                            <th class=\"tbltitle\">Level</th>";

            if ($arr['special_ship_max_level'] <= $init_level && $arr['special_ship_max_level'] != 0) {
                echo "<td>$init_level (max.)</td>";
            } else {
                if ($level - $init_level <= 0) {
                    echo "<td>$init_level (+" . ($level - $init_level) . ")</td>";
                } else {
                    echo "<td style=\"color:green;\">$init_level (+" . ($level - $init_level) . ")</td>";
                }
            }
            echo "
                            <td>Level des Schiffes</td>
                         </tr>
                         <tr>
                            <th class=\"tbltitle\">Erfahrung</th>
                            <td>" . nf($arr['shiplist_special_ship_exp']) . "</td>
                            <td>Erfahrung des Schiffes</td>
                         </tr>
                         <tr>
                            <th class=\"tbltitle\">Ben. Erfahrung</th>";

            if ($arr['special_ship_max_level'] <= $init_level && $arr['special_ship_max_level'] != 0)
                echo "<td> - </td>";
            else
                echo "<td>" . nf($exp_for_next_level) . "</td>";

            echo "<td>Benötigte Erfahrung bis zum nächsten LevelUp</td>
                         </tr>

                         ";
            tableEnd();
        }
    } else {
        echo "Du bist noch nicht im Besitz eines Spezialschiffes!<br>";
    }
}
