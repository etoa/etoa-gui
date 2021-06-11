<?PHP
	echo "<h2>Energie</h2>";

	tableStart("Energieproduktion");
	echo "<tr><td colspan=\"6\">
	<img src=\"misc/powerproduction.image.php\" alt=\"Graph\" />
	</td></tr>";

	$res = dbquery("
	SELECT
		*
	FROM
		buildings
	WHERE
		building_type_id=".BUILDING_POWER_CAT."
	ORDER BY
		building_order
	");
	echo "<tr>
	<th>Produktionsanlage</th>
	<th>Prod Lvl 1</th>
	<th>Kostenfaktor</th>
	<th>Prodfaktor</th>
	<th>Felder/Lvl</th>
	<th>Total gebaut</th>
	</tr>";
	while ($arr = mysql_fetch_array($res))
	{
		$sres = dbquery("
		SELECT
			COUNT(buildlist_id)
		FROM
			buildlist
		WHERE
			buildlist_building_id=".$arr['building_id']."");
		$sum = mysql_result($sres,0);

		echo "<tr>
		<td>".$arr['building_name']."</td>
		<td>".$arr['building_prod_power']."</td>
		<td>".$arr['building_build_costs_factor']."</td>
		<td>".$arr['building_production_factor']."</td>
		<td>".$arr['building_fields']."</td>
		<td>".nf($sum)."</td>
		</tr>";
	}
	$res = dbquery("
	SELECT
		*
	FROM
		ships
	WHERE
		ship_prod_power>0
	ORDER BY
		ship_order
	");
	while ($arr = mysql_fetch_array($res))
	{
		$sres = dbquery("
		SELECT
			SUM(shiplist_count)
		FROM
			shiplist
		WHERE
			shiplist_ship_id=".$arr['ship_id']."");
		$sum = mysql_result($sres,0);

		$tpb1 = Planet::getSolarPowerBonus($cfg->param1('planet_temp'),$cfg->param1('planet_temp')+$cfg->value('planet_temp'));
		$tpb2 = Planet::getSolarPowerBonus($cfg->param2('planet_temp')-$cfg->value('planet_temp'),$cfg->param2('planet_temp'));

		echo "<tr>
		<td>".$arr['ship_name']."</td>
		<td>".$arr['ship_prod_power']." (".$tpb1." bis +".$tpb2.")</td>
		<td></td>
		<td></td>
		<td></td>
		<td>".nf($sum)."</td>
		</tr>";
	}
	tableEnd();


?>
