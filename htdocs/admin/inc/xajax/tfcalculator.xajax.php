<?PHP

$xajax->register(XAJAX_FUNCTION,"createFields");

function createFields($form)
{
    $objResponse = new xajaxResponse();
    
    $out = '<table><tr><th>Spieler</th><th>Koordinaten</th><th>Anteil Titan</th><th>Anteil Sili</th><th>Anteil PVC</th></tr>';
    for ($fields=1;$fields<=$form['number_fields'];$fields++) {
        $entity = Entity::createFactoryByCoords($form['search_cell_s1'.$fields],$form['search_cell_s2'.$fields],$form['search_cell_c1'.$fields],$form['search_cell_c2'.$fields],$form['search_cell_pos'.$fields]);
        $out .= '<tr>';
        $out .= '<td>'.$entity->owner()->nick.'</td>';
        $out .= '<td>'.$entity.'</td>';
        $out .= '<td><input type = "number" readonly value ="'.(round($form['total_tit']*($form['percent'.$fields]/100))).'" name="tit'.$fields.'"/></td>';
        $out .= '<td><input type = "number" readonly value ="'.(round($form['total_sili']*($form['percent'.$fields]/100))).'" name ="sili'.$fields.'"/></td>';
        $out .= '<td><input type = "number" readonly value ="'.(round($form['total_pvc']*($form['percent'.$fields]/100))).'" name ="pvc'.$fields.'"/></td>';
        $out .= '</tr>';
    }
    $out .= '</table><p><input type="submit" name="submit_values" value ="Aufteilen"></p>';
    
    $objResponse->script("document.getElementById('calc').innerHTML = ''");
    $objResponse->append("calc","innerHTML", $out);
    return $objResponse;
};