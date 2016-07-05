<?PHP

$xajax->register(XAJAX_FUNCTION,"createFields");

function createFields($form)
{
    $objResponse = new xajaxResponse();

    $out = '<tr><th>Spieler</th><th>Koordinaten</th><th>Anteil Titan</th><th>Anteil Sili</th><th>Anteil PVC</th></tr>';
    for ($fields=1;$fields<=$form['number_fields'];$fields++) {
        $entity = Entity::createFactoryByCoords($form['search_cell_s1'.$fields],$form['search_cell_s2'.$fields],$form['search_cell_c1'.$fields],$form['search_cell_c2'.$fields],$form['search_cell_pos'.$fields]);
        $out .= '<tr>';
        $out .= '<td>'.$entity->owner()->nick.'</td>';
        $out .= '<td>'.$entity.'</td>';
        $out .= '<td>'.(round($form['total_tit']*($form['percent'.$fields]/100))).'</td>';
        $out .= '<td>'.(round($form['total_sili']*($form['percent'.$fields]/100))).'</td>';
        $out .= '<td>'.(round($form['total_pvc']*($form['percent'.$fields]/100))).'</td>';
        $out .= '</tr>';
    }

    $objResponse->script("document.getElementById('calc').innerHTML = ''");
    $objResponse->append("calc","innerHTML", $out);
    return $objResponse;
};