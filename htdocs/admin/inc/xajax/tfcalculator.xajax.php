<?PHP

$xajax->register(XAJAX_FUNCTION,"showNumberField");
$xajax->register(XAJAX_FUNCTION,"createPlayers");
$xajax->register(XAJAX_FUNCTION,"createValues");
$xajax->register(XAJAX_FUNCTION,"splitDebris");




function showNumberField() {
    $response = new xajaxResponse();
    ob_start();
    echo '<form id="createForm">
                <p>
                    <table>
                        <tr>
                            <td>Anzahl Spieler</td>
                        </tr>
                        <tr>
                            <td>
                                <input 
                                    type="text" 
                                    name="number_fields" 
                                    id="number_fields"
                                    oninput="showElement(\'createPlayers\',this.value>1)">
                            </td>
                        </tr>
                    </table>
                </p>
                <input type="button" id ="createPlayers" value="Felder erzeugen" style="display: none" onclick="xajax_createPlayers(document.getElementById(\'number_fields\').value)"/>
          <form>';

    $response->assign("numbers","innerHTML",ob_get_contents());
    ob_end_clean();

    return $response;
}

function createPlayers($nr) {
    if($nr>1) {

        $cfg = Config::getInstance();
        $conf = $cfg->getArray();
        $response = new xajaxResponse();

        ob_start();

        echo '<form id="valuesForm" onsubmit="xajax_createValues(xajax.getFormValues(\'valuesForm\'),document.getElementById(\'number_fields\').value); return false;">
        <table>
            <tr>
            <th>Spieler:</th>
            <th>Koordinaten</th>
            <th>Anteil in %</th></tr>';
        for ($fields = 1; $fields <= $nr; $fields++) {
            echo '<tr>';
            if ($fields == 1)
                echo "<th>$fields (TF)</th>";
            else
                echo "<th>$fields</th>";

            echo '<td><select name="search_cell_s1' . $fields . '">';
            for ($x = 1; $x <= $conf['num_of_sectors']['p1']; $x++) {
                echo "<option value=$x>$x</option>";
            }
            echo '</select>/';

            echo '<select name="search_cell_s2' . $fields . '">';
            for ($x = 1; $x <= $conf['num_of_sectors']['p1']; $x++) {
                echo "<option value=$x>$x</option>";
            }
            echo '</select>:';

            echo '<select name="search_cell_c1' . $fields . '">';
            for ($x = 1; $x <= $conf['num_of_cells']['p1']; $x++) {
                echo "<option value=$x>$x</option>";
            }
            echo '</select>/';

            echo '<select name="search_cell_c2' . $fields . '">';
            for ($x = 1; $x <= $conf['num_of_cells']['p1']; $x++) {

                echo "<option value=$x>$x</option>";

            }

            echo '</select> : <select name="search_cell_pos' . $fields . '">';
            for ($x = 0; $x <= $conf['num_planets']['p2']; $x++)
                echo "<option value=\"$x\">$x</option>";
            echo '</select></td><td><input type="number" name="percent' . $fields . '"></td></tr>';
        }
        echo '</table>';
        echo '<p>';
        echo 'Gesamtressourcen';
        echo '<table>';
        echo '<tr><td>Titan</td><td>Silizium</td><td>PVC</td></tr>';
        echo '<tr><td><input type="number" name="total_tit"></td><td>
        <input type="number" name="total_sili""></td>
        <td><input type="number" name="total_pvc""></td></tr>';
        echo '</table>';
        echo '</p>';
        echo '<p><input type="submit" name="calc_values" value ="Berechnen"></p>';
        echo '</form>';

        $response->assign("players","innerHTML",ob_get_contents());
        ob_end_clean();
        return $response;



    }
}

function createValues($form, $nr)
{
    $response = new xajaxResponse();
    $error = false;

    ob_start();
    echo '
    <form id="debrisForm" onsubmit="xajax_splitDebris(xajax.getFormValues(\'debrisForm\'),xajax.getFormValues(\'valuesForm\')); return false;">
        <table>
            <tr>
                <th>Spieler</th>
                <th>Koordinaten</th>
                <th>Anteil Titan</th>
                <th>Anteil Sili</th>
                <th>Anteil PVC</th>
            </tr>';

    for ($fields=1; $fields <= $nr; $fields++) {
        $entity = Entity::createFactoryByCoords($form['search_cell_s1'.$fields],$form['search_cell_s2'.$fields],$form['search_cell_c1'.$fields],$form['search_cell_c2'.$fields],$form['search_cell_pos'.$fields]);
        if(!$entity) {
            $error = true;
        }
        else {
            echo '
            <tr>
                <td>'.$entity->owner()->nick.'</td>
                <td>'.$entity.'</td>
                <td><input type = "number" value ="'.(round($form['total_tit']*($form['percent'.$fields]/100))).'" name="tit'.$fields.'"/></td>
                <td><input type = "number" value ="'.(round($form['total_sili']*($form['percent'.$fields]/100))).'" name ="sili'.$fields.'"/></td>
                <td><input type = "number" value ="'.(round($form['total_pvc']*($form['percent'.$fields]/100))).'" name ="pvc'.$fields.'"/></td>
            </tr>';
        }
    }
    echo '</table><p><input type="submit" name="submit_values" value ="Aufteilen"></p></form>';

    if($error) {
        $out = 'Ungültige Koordinaten!';
    }
    else {
        $out = ob_get_contents();
    }


    $response->assign("calc","innerHTML", $out);
    ob_end_clean();
    return $response;
};

function splitDebris($formValues,$formPlayers) {

    $players = (sizeof($formValues)-1)/3;
    $response = new xajaxResponse();

    for ($fields = 1; $fields <= $players; $fields++) {
        $entity = Entity::createFactoryByCoords($formPlayers['search_cell_s1' . $fields], $formPlayers['search_cell_s2' . $fields], $formPlayers['search_cell_c1' . $fields], $formPlayers['search_cell_c2' . $fields], $formPlayers['search_cell_pos' . $fields]);
        if ($entity && $entity->userId) {
            if ($fields == 1) {
                $sql = "
                    INSERT INTO
                    market_ressource
                            (
                            entity_id,
                            buy_0,
                            buy_1,
                            buy_2,
                            for_user,
                            `text`,
                            datum)
                    VALUES (
                        299,
                        " . (intval($formPlayers['total_tit']) - intval($formValues['tit' . $fields])) . ",
                        " . (intval($formPlayers['total_sili']) - intval($formValues['sili' . $fields])) . ",
                        " . (intval($formPlayers['total_pvc']) - intval($formValues['pvc' . $fields])) . ",
                        " . $entity->userId . ",
                        'Trümmerfeld',
                        " . time() . "
                    );";
                $logs = "
                    INSERT INTO
                    logs_debris
                            (
                            time,
                            admin_id,
                            user_id,
                            metal,
                            crystal,
                            plastic)
                    VALUES (
                        " . time() . ",
                        " . $_SESSION['user_id'] . ",
                        " . $entity->userId . ",
                        " . (-1*(intval($formPlayers['total_tit']) - intval($formValues['tit' . $fields]))) . ",
                        " . (-1*(intval($formPlayers['total_sili']) - intval($formValues['sili' . $fields]))) . ",
                        " . (-1*(intval($formPlayers['total_pvc']) - intval($formValues['pvc' . $fields]))) . "
                    )";
            } else {
                $sql = "
                    INSERT INTO
                    market_ressource
                            (
                            entity_id,
                            sell_0,
                            sell_1,
                            sell_2,
                            for_user,
                            `text`,
                            datum)
                    VALUES (
                        299,
                        " . $formValues['tit' . $fields] . ",
                        " . $formValues['sili' . $fields] . ",
                        " . $formValues['pvc' . $fields] . ",
                        " . $entity->userId . ",
                        'Trümmerfeld',
                        " . time() . "
                    );";
                $logs = "    
                    INSERT INTO
                    logs_debris
                            (
                            time,
                            admin_id,
                            user_id,
                            metal,
                            crystal,
                            plastic)
                    VALUES (
                        " . time() . ",
                        " . $_SESSION['user_id'] . ",
                        " . $entity->userId . ",
                        " . $formValues['tit' . $fields] . ",
                        " . $formValues['sili' . $fields] . ",
                         " . $formValues['pvc' . $fields] . "
                    )";
            }
            dbquery($sql);
            dbquery($logs);

            $response->assign("tfContent","innerHTML", "Trümmerfeld aufgeteilt!");
        }
        else {
            $response->alert("Fehler, das Trümmerfeld konnte nicht aufgeteilt werden!");
            break;
        }
    }
    return $response;
}
