<?php

echo'<form method="post" id="tfcalcForm">';
echo'<p>';
echo'<table>';
echo'<tr><td>Anzahl Spieler</td></tr>';
echo'<tr><td><input type="number" name="number_fields" id="number_fields" value="'.$_POST['number_fields'].'"></td></tr>';
echo'</table>';

echo'</p>';
echo'<p><input type="submit" name="create_fields" value ="Felder erzeugen"></p>';
echo'<p>';
if (isset($_POST['create_fields']))
{
    echo '<table>';
    echo '<tr><th>Spieler:</th><th>Koordinaten</th><th>Anteil in %</th></tr>';
    for ($fields=1;$fields<=$_POST['number_fields'];$fields++) {
        echo '<tr>';
        if($fields == 1)
            echo "<th>$fields (TF)</th>";
        else
            echo "<th>$fields</th>";

        echo '<td><select name="search_cell_s1'.$fields.'">';
        for ($x = 1; $x <= $conf['num_of_sectors']['p1']; $x++) {
            echo "<option value=$x>$x</option>";
        }
        echo '</select>/';

        echo '<select name="search_cell_s2'.$fields.'">';
        for ($x = 1; $x <= $conf['num_of_sectors']['p1']; $x++) {
            echo "<option value=$x>$x</option>";
        }
        echo '</select>:';

        echo '<select name="search_cell_c1'.$fields.'">';
        for ($x = 1; $x <= $conf['num_of_cells']['p1']; $x++) {
            echo "<option value=$x>$x</option>";
        }
        echo '</select>/';

        echo '<select name="search_cell_c2'.$fields.'">';
        for ($x = 1; $x <= $conf['num_of_cells']['p1']; $x++) {

                echo "<option value=$x>$x</option>";

        }

        echo '</select> : <select name="search_cell_pos'.$fields.'">';
        for ($x = 0; $x <= $conf['num_planets']['p2']; $x++)
            echo "<option value=\"$x\">$x</option>";
        echo '</select></td><td><input type="number" name="percent'.$fields.'"></td></tr>';
    }
    echo '</table>';
}
echo'<p>';
echo'Gesamtressourcen';
echo'<table>';
echo'<tr><td>Titan</td><td>Silizium</td><td>PVC</td></tr>';
echo'<tr><td><input type="number" name="total_tit" value = "'.$_POST['total_tit'].'"></td><td>
<input type="number" name="total_sili" value = "'.$_POST['total_sili'].'"></td>
<td><input type="number" name="total_pvc" value ="'.$_POST['total_pvc'].'"></td></tr>';
echo'</table>';
echo'</p>';
echo'<p><input type="button" name="calc_values" value ="Berechnen" onclick="xajax_createFields(xajax.getFormValues(\'tfcalcForm\'))"></p>';
    echo '<table id ="calc">';
    echo '</table>';
    echo'<p><input type="button" name="submit_values" value ="Aufteilen" onclick="xajax_createFields()"></p>';
echo'</form>';
