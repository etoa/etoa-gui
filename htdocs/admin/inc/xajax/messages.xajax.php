<?PHP

$xajax->register(XAJAX_FUNCTION,"showDetail");
$xajax->register(XAJAX_FUNCTION,"restoreReport");

function showDetail($type)
{
	$or = new xajaxResponse();
	ob_start();
	switch ($type)
	{
		case 'market':
			tableStart("Detailsuche",'auto');
			echo "<input type=\"hidden\" name=\"table\" id=\"table\" value=\"1\" />
				<tr>
					<th>Unterkategorie</th>
					<td>
						<select id=\"subtype\" name=\"subtype\">
							<option value=\"\">(egal)</option>";
					foreach (MarketReport::$subTypes as $k=>$v)
			echo "			<option value=\"".$k."\">".$v."</option>";

			echo "		</select>
					</td>
				</tr>
				<tr>
				<th>Schiff</th>
				<td>
					<select id=\"ship_id\" name=\"ship_id\">
						<option value=\"\">(egal)</option>";
			// Schiffe laden
			$res = dbquery("
							SELECT
								ship_id,
								ship_name
							FROM
								ships
							ORDER BY
								ship_name;");
			while ($arr=mysql_fetch_row($res))
		echo "			<option value=\"".$arr[0]."\">".$arr[1]."</option>";

		echo "		</select>
				</td>
			</tr>
			<tr>
				<th>Anzahl</th>
				<td>
					<input type=\"text\" name=\"ship_count\" value=\"\" size=\"6\" maxlength=\"250\" />
				</td>
			</tr>
			<tr>
				<th>Angebot</th>
				<td>
					<input id=\"sell_0\" type=\"checkbox\" name=\"sell_0\" value=\"1\" title=\"Titan im Angebot\" />&nbsp;Titan
					<input id=\"sell_1\" type=\"checkbox\" name=\"sell_1\" value=\"1\" title=\"Silizium im Angebot\" />&nbsp;Silizium
					<input id=\"sell_2\" type=\"checkbox\" name=\"sell_2\" value=\"1\" title=\"PVC im Angebot\" />&nbsp;PVC
					<input id=\"sell_3\" type=\"checkbox\" name=\"sell_3\" value=\"1\" title=\"Tritium im Angebot\" />&nbsp;Tritium
					<input id=\"sell_4\" type=\"checkbox\" name=\"sell_4\" value=\"1\" title=\"Nahrung im Angebot\" />&nbsp;Nahrung
				</td>
			</tr>
			<tr>
				<th>Preis</th>
				<td>
					<input id=\"buy_0\" type=\"checkbox\" name=\"buy_0\" value=\"1\" title=\"Titan als Preis\" />&nbsp;Titan
					<input id=\"buy_1\" type=\"checkbox\" name=\"buy_1\" value=\"1\" title=\"Silizium als Preis\" />&nbsp;Silizium
					<input id=\"buy_2\" type=\"checkbox\" name=\"buy_2\" value=\"1\" title=\"PVC als Preis\" />&nbsp;PVC
					<input id=\"buy_3\" type=\"checkbox\" name=\"buy_3\" value=\"1\" title=\"Tritium als Preis\" />&nbsp;Tritium
					<input id=\"buy_4\" type=\"checkbox\" name=\"buy_4\" value=\"1\" title=\"Nahrung als Preis\" />&nbsp;Nahrung
				</td>
			</tr>
			<tr>
				<th>Flotten-ID's</th>
				<td>
					<input type=\"text\" name=\"fleet1_id\" value=\"\" size=\"4\" maxlength=\"250\" />
					<input type=\"text\" name=\"fleet2_id\" value=\"\" size=\"4\" maxlength=\"250\" />
				</td>
			</tr>";
		tableEnd();
		break;
		case 'other':
			tableStart("Detailsuche",'auto');
			echo "<input type=\"hidden\" name=\"table\" id=\"table\" value=\"1\" />
				<tr>
					<th>Unterkategorie</th>
					<td>
						<select id=\"subtype\" name=\"subtype\">
							<option value=\"\">(egal)</option>";
						foreach (OtherReport::$subTypes as $k=>$v)
							echo "			<option value=\"".$k."\">".$v."</option>";

						echo "		</select>
					</td>
				</tr>
				<tr>
					<th>Aktion:</th>
					<td>
						<select name=\"action\">
							<option value=\"\">(egal)</option>";
				$fas = FleetAction::getAll();
				foreach ($fas as $fa)
					echo "<option value=\"".$fa->code()."\" style=\"color:".FleetAction::$attitudeColor[$fa->attitude()]."\">".$fa->name()."</option>";

				echo "</select> &nbsp; <select name=\"status\">";
				echo "<option value=\"\">(egal)</option>";
				foreach (FleetAction::$statusCode as $k => $v)
				{
					echo "<option value=\"".$k."\">".$v."</option>";
				}
				echo "</select>
					</td>
				</tr>
				<tr>
					<th>Schiff</th>
					<td>
						<select id=\"ship_id\" name=\"ship_id\">
							<option value=\"\">(egal)</option>";
					// Schiffe laden
					$res = dbquery("
								SELECT
									ship_id,
									ship_name
								FROM
									ships
								ORDER BY
									ship_name;");
					while ($arr=mysql_fetch_row($res))
			echo "			<option value=\"".$arr[0]."\">".$arr[1]."</option>";

			echo "		</select>
					</td>
				</tr>
				<tr>
					<th>Anzahl</th>
					<td>
						<input type=\"text\" name=\"ship_count\" value=\"\" size=\"6\" maxlength=\"250\" />
					</td>
				</tr>
				<tr>
					<th>Rohstoffe</th>
					<td>
						<input id=\"res_0\" type=\"checkbox\" name=\"res_0\" value=\"1\" title=\"Titan\" />&nbsp;Titan
						<input id=\"res_1\" type=\"checkbox\" name=\"res_1\" value=\"1\" title=\"Silizium\" />&nbsp;Silizium
						<input id=\"res_2\" type=\"checkbox\" name=\"res_2\" value=\"1\" title=\"PVC\" />&nbsp;PVC
						<input id=\"res_3\" type=\"checkbox\" name=\"res_3\" value=\"1\" title=\"Tritium\" />&nbsp;Tritium
						<input id=\"res_4\" type=\"checkbox\" name=\"res_4\" value=\"1\" title=\"Nahrung\" />&nbsp;Nahrung
					</td>
				</tr>
				<tr>
					<th>Flotten-ID</th>
					<td>
						<input type=\"text\" name=\"fleet1_id\" value=\"\" size=\"4\" maxlength=\"250\" />
					</td>
				</tr>";
			tableEnd();
		break;

		default:
	}

	$or->assign('detail','innerHTML',ob_get_contents());
	$or->assign('detail',"style.display",'');
	ob_end_clean();
	return $or;
}

function restoreReport($id)
{
	$or = new xajaxResponse();
	$r = Report::createFactory($id);
	$r->deleted = false;
	$or->assign('deleted','innerHTML','Nein');
	return $or;
}



?>
