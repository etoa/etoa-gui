<?php
	$url = "?$link&amp;site=$site";
	$id = isset($_GET['id']) ? $_GET['id'] : 0;

	echo "<h1>Schiffe</h1>";

	if ($id>0)
	{
		$ship = new ShipNG($id);
		if ($ship->valid)
		{
			tableStart($ship->name,600);
			echo "<tr><td colspan=\"4\">
			".$ship->img("b","left")."
			<b>".$ship->typeName."</b><br/><br/>

			".$ship->longDesc."
			</td>";
			echo "<tr>";

			echo "<td style=\"width:50%;padding:0px;\">";

			// Data
			echo "<table class=\"tb\" style=\"margin:0px;\">";
			echo "<tr><th colspan=\"3\">Daten</th></tr>";
			echo "<tr><td>Hülle</td><td>".$ship->hull."</td></tr>";
			echo "<tr><td>Regeneration</td><td>".$ship->regeneration."</td></tr>";
			echo "<tr><td>Beweglichkeit</td><td>".$ship->agility."</td></tr>";
			echo "<tr><td>Geschwindigkeit</td><td>".nf($ship->speed)." AE/h</td></tr>";
			echo "<tr><td>Startzeit</td><td>".tf($ship->delay)."</td></tr>";
			echo "<tr><td>Piloten</td><td>".nf($ship->pilots)."</td></tr>";
			echo "<tr><td>Treibstoff</td><td>".nf($ship->fuel)." t/100AE + ".$ship->baseFuel." t</td></tr>";
			echo "<tr><td>Treibstofflager</td><td>".nf($ship->capacityFuel)."</td></tr>";
			echo "<tr><td>Frachtraum</td><td>".nf($ship->capacity)."</td></tr>";
			echo "<tr><td>Passagierraum</td><td>".nf($ship->capacityPeople)."</td></tr>";
			echo "</table>";

			echo "<table class=\"tb\" style=\"margin:0px;\">";
			echo "<tr><th colspan=\"3\">Kosten</th></tr>";
			foreach ($resNames as $rk=>$rn)
			{
				echo "<tr><td class=\"rescolor".$rk."\">".$resIcons[$rk]."$rn</td><td class=\"rescolor".$rk."\">".$ship->{"costs".$rk}."</td></tr>";
			}
			echo "</table>";

			echo "</td><td style=\"padding:0px;\">";

			// Weapons
			echo "<table class=\"tb\" style=\"margin:0px;\">";
			echo "<tr><th colspan=\"3\">Waffen</th></tr>";
			foreach ($weaponNames as $wk=>$wn)
			{
				echo "<tr>
				<td>$wn</td>
				<td>".$ship->{"damage".$wk}."</td>
				<td>".$ship->{"multifire".$wk}."x</td>
				</tr>";
			}
			echo "</table>";

			// Defenses
			echo "<table class=\"tb\" style=\"margin:0px;\">";
			echo "<tr><th colspan=\"2\">Verteidigung</th></tr>";
			foreach ($defenseNames as $wk=>$wn)
			{
				echo "<tr>
				<td>$wn</td>
				<td>".$ship->{"shield".$wk}."</td>
				</tr>";
			}
			echo "</table>";

			echo "<table class=\"tb\" style=\"margin:0px;\">";
			echo "<tr><th colspan=\"2\">Fähigkeiten</th></tr>";
			foreach ($ship->getActions() as $ac)
			{
				echo "<tr>
				<td>$ac</td>
				</tr>";
			}
			echo "</table>";

			echo "</td>";
			echo "</tr>";

			tableEnd();
			echo button("Zur Übersicht",$url)."&nbsp;&nbsp; ";
		}
		else
		{
			err_msg("Schiff nicht vorhanden!");
		}
	}
	else
	{

		tableStart("Übersicht");
		foreach(ShipNG::find(null,"name") as $sid => $ship)
		{
			echo "<tr>
			<td style=\"width:40px;background:#000\">".$ship->img("s")."</td>
			<td style=\"width:150px\"><a href=\"$url&amp;id=".$sid."\">".$ship->name."</a></td>
			<td>".$ship->typeName."</td>
			<td>".$ship->shortDesc."</td>
			</tr>";
		}
		tableEnd();
	}
?>
